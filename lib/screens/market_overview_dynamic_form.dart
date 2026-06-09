import 'dart:io';

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:path_provider/path_provider.dart';
import 'package:starsfa/models/determine_position.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/location_class.dart';
import 'package:starsfa/models/market_overview_data_class.dart';
import 'package:starsfa/models/market_overview_dynamic_form_class.dart';
import 'package:starsfa/models/survey_header_class.dart';
import 'package:starsfa/models/survey_input_class.dart';
import 'package:image_picker/image_picker.dart';
import 'package:starsfa/models/survey_output_class.dart';
import 'package:starsfa/models/user_login_class.dart';

class MarketOverviewDynamicForm extends StatefulWidget {
  final String menuName;
  final String menuId;
  final bool isMenu;
  final String showName;
  final String mainMenuName;
  const MarketOverviewDynamicForm({
    super.key,
    required this.menuName,
    required this.menuId,
    this.isMenu = false,
    required this.showName,
    required this.mainMenuName,
  });

  @override
  State<MarketOverviewDynamicForm> createState() =>
      _MarketOverviewDynamicFormState();
}

class _MarketOverviewDynamicFormState extends State<MarketOverviewDynamicForm> {
  late final Future<List<MarketOverviewDynamicFormClass>> getFormItems;
  String sessionToken = '';
  final GlobalKey<FormState> formKey = GlobalKey<FormState>();
  bool isLoading = false;

  Future<void> generateSessionToken() async {
    final user = await UserLoginClass.getLocalUser();
    final String sessionTokenGen =
        'SU${user?.empCode}${DateFormat('yyyyMMddHHmmss').format(DateTime.now())}';
    await Hive.openBox(sessionTokenGen);
    setState(() {
      sessionToken = sessionTokenGen;
    });
    print(sessionToken);
  }

  Future<List<MarketOverviewDynamicFormClass>> getFormItemsFromLocalDB() async {
    final String getFormItemsQuery = widget.isMenu
        ? "SELECT * FROM survey_input WHERE survey_sub_menu = '${widget.menuName}'"
        : "SELECT * FROM survey_input WHERE menu_id = '${widget.menuId}' AND type <> 'menu'";
    final List<Map<String, dynamic>> formItemsData =
        await SurveyInputClass.getSurveyInputFromLocalDB(getFormItemsQuery);
    await Hive.openBox(sessionToken);
    return formItemsData
        .map((item) => MarketOverviewDynamicFormClass.fromJson(item))
        .toList();
  }

  void saveFormData(List<MarketOverviewDynamicFormClass> formItems) async {
    for (MarketOverviewDynamicFormClass item in formItems) {
      final Box<dynamic> box = Hive.box(sessionToken);
      final String? value = (box.get(item.rowId) ?? '').trim();
      if (item.mandatory == 'Y' && (value?.isEmpty ?? true)) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Please enter ${item.displayName}'),
            backgroundColor: Colors.red,
          ),
        );
        return;
      }
      if (item.type == 'double' && (value != '') && item.validation != '') {
        double? length = double.tryParse(item.validation ?? '');
        if (length != value?.length && length != null) {
          print(item.validation ?? '');
          print(value!.length.toString());
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('Please enter a valid ${item.displayName}'),
              backgroundColor: Colors.red,
            ),
          );
          return;
        }
      }
    }
    if (!(formKey.currentState?.validate() ?? false)) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please fill all mandatory fields'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    } else {
      setState(() {
        isLoading = true;
      });
    }

    final UserLoginClass user =
        await UserLoginClass.getLocalUser() ?? UserLoginClass();

    Map<String, bool> dataSaved = {
      'header': false,
      'output': false,
      'location': false,
    };
    Map<String, String> complaintReportData = {};
    for (var item in formItems) {
      final Box<dynamic> box = Hive.box(sessionToken);
      String? value = (box.get(item.rowId) ?? '').trim();
      if (item.type == 'radio') {
        if (value == '') {
          continue;
        } else {
          List<String> values = value?.split(',') ?? [];
          final directory = await getTemporaryDirectory();
          for (String value in values) {
            final File file = File(value);
            String fileExtension = file.path.split('.').last;
            String fileName =
                "${user.empCode}${DateFormat('yyyyMMddHHmmss').format(DateTime.now())}.$fileExtension";
            final String newPath = '${directory.path}/Images/';
            await Directory(newPath).create(recursive: true);
            print("$newPath$fileName");
            file.rename('$newPath$fileName');
            values[values.indexOf(value)] = fileName;
            await Future.delayed(const Duration(seconds: 1));
          }
          value = values.join(',');
        }
        if (value.contains(',')) {
          value.split(',').map((e) async {
            final SurveyOutputClass surveyOutput = SurveyOutputClass(
              surveyId: sessionToken,
              rowId: item.rowId ?? '',
              value: e,
              type: widget.menuName,
              actionId: item.actionId ?? '',
            );
            dataSaved['output'] =
                await SurveyOutputClass.saveSurveyOutput(surveyOutput);
          });
        } else {
          final SurveyOutputClass surveyOutput = SurveyOutputClass(
            surveyId: sessionToken,
            rowId: item.rowId ?? '',
            value: value,
            type: widget.menuName,
            actionId: item.actionId ?? '',
          );
          dataSaved['output'] =
              await SurveyOutputClass.saveSurveyOutput(surveyOutput);
        }
      } else {
        var type = widget.menuName;
        String value1 = value ?? '';
        if (item.rowId == 'RA209') {
          String sessionToken1 = sessionToken.replaceAll('SU', 'C');
          value1 = '$value1;$sessionToken1;${user.empCode}';
          type = 'Complaint Report';
        }
        final SurveyOutputClass surveyOutput = SurveyOutputClass(
          surveyId: sessionToken,
          rowId: item.rowId ?? '',
          value: value1,
          type: type,
          actionId: item.actionId ?? '',
        );
        dataSaved['output'] =
            await SurveyOutputClass.saveSurveyOutput(surveyOutput);
      }
      if (item.insertTableDetail?.contains('#') ?? false) {
        String action = item.insertTableDetail?.split('#')[0] ?? '';
        List<String> columns =
            item.insertTableDetail?.split('#')[2].split(';') ?? [];
        if (action == 'update') {
          for (String column in columns) {
            complaintReportData.addAll({column: value ?? ''});
          }
        } else if (action == 'insert') {
          for (String column in columns) {
            complaintReportData.addAll({column: value ?? ''});
          }
        } else {}
      }
    }
    if (widget.menuId == 'RA280') {
      SurveyOutputClass.saveComplaintReport(complaintReportData);
    }
    final SurveyHeaderClass surveyHeader = SurveyHeaderClass(
      surveyId: sessionToken,
      surveyType: widget.menuName,
      menuName: widget.menuId,
      mallId: '',
      mallName: '',
      businessName: '',
      contactName: '',
      phoneNo: '',
      questionsAnswered: '',
      routeCode: '',
      checkInTime: '',
    );
    dataSaved['header'] =
        await SurveyHeaderClass.saveSurveyHeader(surveyHeader);
    final DeterminePosition determinePosition =
        await DeterminePosition.getPosition(null, null, null);
    final LocationClass location = LocationClass(
      empCode: user.empCode ?? '',
      transId: sessionToken,
      latt: determinePosition.latitude.toString(),
      longi: determinePosition.longitude.toString(),
      date: DateFormat('yyyy-MM-dd HH:mm:ss').format(DateTime.now()),
    );
    dataSaved['location'] = await LocationClass.saveLocation(location);
    if (dataSaved.containsValue(false)) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
              'Error saving data for ${dataSaved.keys.firstWhere((key) => dataSaved[key] == false)}'),
          backgroundColor: Colors.red,
        ),
      );
    } else {
      generateSessionToken().then((value) => Hive.box(sessionToken).clear());
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Data saved successfully'),
          backgroundColor: Colors.green,
        ),
      );
    }
    setState(() {
      isLoading = false;
    });

    Navigator.of(context).pop();

    MarketOverviewDataClass.uploadMarketOverviewData();
  }

  @override
  void initState() {
    super.initState();
    generateSessionToken();
    getFormItems = getFormItemsFromLocalDB();
  }

  @override
  void dispose() {
    super.dispose();
    Future.delayed(const Duration(seconds: 5), () {
      Hive.box(sessionToken).deleteFromDisk();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(
            Icons.arrow_back,
            color: Colors.white,
          ),
          onPressed: () {
            Navigator.of(context).pop();
          },
        ),
        backgroundColor: Colors.red,
        title: Text(
          widget.menuName,
          style: const TextStyle(color: Colors.white),
        ),
      ),
      body: FutureBuilder<List<MarketOverviewDynamicFormClass>>(
          future: getFormItems,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(
                child: CircularProgressIndicator(),
              );
            } else if (snapshot.hasError) {
              return Center(
                child: Text('Error: ${snapshot.error}'),
              );
            } else if (snapshot.hasData) {
              final List<MarketOverviewDynamicFormClass> formItems =
                  snapshot.data ?? [];
              return Column(
                children: [
                  Expanded(
                    child: Form(
                      key: formKey,
                      child: ListView.builder(
                        itemCount: formItems.length,
                        itemBuilder: (context, index) {
                          print("display name");
                          print(formItems[index].displayName);
                          print("row id");
                          print(formItems[index].rowId);
                          print("row id");
                          print(formItems[index].rowId);
                          return ValueListenableBuilder<Box>(
                            valueListenable:
                                Hive.box(sessionToken).listenable(),
                            builder: (context, box, child) {
                              return MarketOverviewDynamicFormItems(
                                item: formItems[index],
                                sessionToken: sessionToken,
                              );
                            },
                          );
                        },
                      ),
                    ),
                  ),
                  isLoading
                      ? const Center(
                          child: Padding(
                            padding: EdgeInsets.all(10.0),
                            child: CircularProgressIndicator(
                              valueColor:
                                  AlwaysStoppedAnimation<Color>(Colors.black),
                            ),
                          ),
                        )
                      : InkWell(
                          onTap: () => saveFormData(formItems),
                          child: Container(
                            margin: const EdgeInsets.all(10),
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: Colors.red,
                              borderRadius: BorderRadius.circular(5),
                            ),
                            child: const Center(
                              child: Text(
                                'Submit',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 16,
                                ),
                              ),
                            ),
                          ),
                        ),
                ],
              );
            } else {
              return const Center(
                child: Text('No Data'),
              );
            }
          }),
    );
  }
}

class MarketOverviewDynamicFormItems extends StatefulWidget {
  final MarketOverviewDynamicFormClass item;
  final String sessionToken;
  const MarketOverviewDynamicFormItems(
      {super.key, required this.item, required this.sessionToken});

  @override
  State<MarketOverviewDynamicFormItems> createState() =>
      _MarketOverviewDynamicFormItemsState();
}

class _MarketOverviewDynamicFormItemsState
    extends State<MarketOverviewDynamicFormItems> {
  final TextEditingController textEditingController = TextEditingController();
  List<XFile?> clickedImages = <XFile>[];
  final ImagePicker picker = ImagePicker();
  bool isEnable = true;
  bool isEdit = true;

  getLabel(String? label) {
    if (label != null) {
      return Container(
        padding: const EdgeInsets.all(8.0),
        width: double.infinity,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          color: Colors.grey[200],
          borderRadius: BorderRadius.circular(5),
        ),
        child: Text(label,
            style: const TextStyle(
              fontSize: 16,
              color: Colors.black,
              fontWeight: FontWeight.bold,
            )),
      );
    } else {
      return const Text('No display name found');
    }
  }

  getTextField(MarketOverviewDynamicFormClass item) {
    return ValueListenableBuilder<Box>(
        valueListenable: Hive.box(widget.sessionToken).listenable(),
        builder: (context, box, child) {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            textEditingController.text = box.get(item.rowId) ?? '';
          });
          return Padding(
            padding: const EdgeInsets.symmetric(vertical: 10.0),
            child: TextFormField(
              enabled: isEdit,
              controller: textEditingController,
              validator: (value) {
                if (item.mandatory == 'Y' && value!.isEmpty) {
                  return 'Please enter ${item.displayName}';
                }
                return null;
              },
              textAlign: TextAlign.center,
              keyboardType: item.type == 'double'
                  ? TextInputType.number
                  : TextInputType.text,
              decoration: InputDecoration(
                contentPadding: const EdgeInsets.all(5.0),
                hintText:
                    'Enter ${item.displayName} ${item.mandatory == 'Y' ? "(Mandatory)" : ''}',
                focusColor: Colors.black,
                border: const OutlineInputBorder(
                  borderSide: BorderSide(color: Colors.black),
                ),
                errorBorder: const OutlineInputBorder(
                  borderSide: BorderSide(color: Colors.red),
                ),
              ),
              onChanged: (value) {
                final Box<dynamic> box = Hive.box(widget.sessionToken);
                box.put(item.rowId, value);
              },
              onEditingComplete: () {
                final Box<dynamic> box = Hive.box(widget.sessionToken);
                box.put(item.rowId, textEditingController.text.trim());
              },
            ),
          );
        });
  }

  getDropDown(MarketOverviewDynamicFormClass item) {
    final List<String> dropdownItems = item.type?.split(':') ?? [];
    return ValueListenableBuilder<Box>(
        valueListenable: Hive.box(widget.sessionToken).listenable(),
        builder: (context, box, child) {
          return DropdownButtonFormField<String>(
            value: box.get(item.rowId),
            validator: (value) {
              if (item.mandatory == 'Y' && value == null) {
                return 'Please select ${item.displayName}';
              }
              return null;
            },
            items: dropdownItems.map((e) {
              return DropdownMenuItem<String>(
                value: e,
                child: Text(e),
              );
            }).toList(),
            onChanged: (value) {
              if (!isEdit) {
                return;
              }
              final Box<dynamic> box = Hive.box(widget.sessionToken);
              box.put(item.rowId, value);
            },
            decoration: InputDecoration(
              contentPadding: const EdgeInsets.all(5.0),
              helperText:
                  'Select ${item.displayName} ${item.mandatory == 'Y' ? '(Mandatory)' : ''}',
              focusColor: Colors.black,
            ),
          );
        });
  }

  Widget getMasterView(MarketOverviewDynamicFormClass item) {
    return ValueListenableBuilder<Box>(
        valueListenable: Hive.box(widget.sessionToken).listenable(),
        builder: (context, box, child) {
          final String? value = box.get('${item.rowId}_name');
          WidgetsBinding.instance.addPostFrameCallback((_) {
            textEditingController.text = value ?? '';
          });
          return InkWell(
            onTap: !isEdit
                ? null
                : () async {
                    print("${item.displayTableName}");
                    final List<String> masterViewDataItems =
                        item.displayTableName?.split('##') ?? [];
                    final List<String> masterViewQueryDependentRow =
                        (masterViewDataItems.last.isEmpty ||
                                masterViewDataItems.length == 1)
                            ? []
                            : masterViewDataItems.last.split('&');
                    print(
                        'masterViewQueryDependentRow: $masterViewQueryDependentRow');
                    final String masterViewQueryItems =
                        masterViewDataItems.first;
                    final List<String> masterViewItems =
                        masterViewQueryItems.split('#');
                    final String masterViewTableName = masterViewItems.first;
                    final List<String> masterViewColumns =
                        masterViewItems[1].split('%');
                    String masterViewQuery =
                        'SELECT DISTINCT ${masterViewColumns.join(',')} FROM $masterViewTableName';
                    if (masterViewQueryDependentRow != []) {
                      final Box<dynamic> box = Hive.box(widget.sessionToken);
                      for (var masterViewQueryDependentRowItem
                          in masterViewQueryDependentRow) {
                        final List<String> masterViewQueryDependentRowItems =
                            masterViewQueryDependentRowItem.split(';');
                        final String masterViewQueryDependentRowName =
                            masterViewQueryDependentRowItems.first;
                        final String masterViewQueryDependentRowValue =
                            (masterViewQueryDependentRowItems.last
                                    .contains('-'))
                                ? box.get(masterViewQueryDependentRowItems.last
                                        .split('-')
                                        .last) ??
                                    ''
                                : masterViewQueryDependentRowItems.last;
                        if (masterViewQueryDependentRowValue.isEmpty) {
                          final String dependentRowDispNameQuery =
                              "SELECT display_name FROM survey_input WHERE row_id = '${masterViewQueryDependentRowItems.last.split('-').last}'";
                          final List<Map<String, dynamic>>
                              dependentRowDispNameResult =
                              await LocalDB.rawQuery(dependentRowDispNameQuery);
                          final String dependentRowDispName =
                              dependentRowDispNameResult.isEmpty
                                  ? ''
                                  : dependentRowDispNameResult
                                      .first['display_name']
                                      .toString();
                          textEditingController.text =
                              'Please select $dependentRowDispName first';
                          Future.delayed(const Duration(seconds: 2), () {
                            textEditingController.text = '';
                          });
                          return;
                        } else {
                          if (masterViewQuery.contains('WHERE')) {
                            masterViewQuery +=
                                " AND $masterViewQueryDependentRowName = '$masterViewQueryDependentRowValue'";
                          } else {
                            masterViewQuery +=
                                " WHERE $masterViewQueryDependentRowName = '$masterViewQueryDependentRowValue'";
                          }
                        }
                      }
                    }
                    final List<Map<String, dynamic>> masterViewResult =
                        await LocalDB.rawQuery(masterViewQuery);
                    return getMasterViewSelectionDialog(
                        masterViewResult, masterViewColumns);
                  },
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10),
              width: double.infinity,
              alignment: Alignment.center,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(5),
              ),
              child: TextFormField(
                  controller: textEditingController,
                  validator: (value) {
                    if (item.mandatory == 'Y' && value!.isEmpty) {
                      return 'Please enter ${item.displayName}';
                    }
                    return null;
                  },
                  enabled: false,
                  textAlign: TextAlign.center,
                  decoration: InputDecoration(
                    contentPadding: const EdgeInsets.all(5.0),
                    hintText:
                        '${(item.displayName?.contains('Select') ?? false) ? '' : 'Select'} ${item.displayName}',
                    focusColor: Colors.black,
                    border: const OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.black),
                    ),
                    errorBorder: const OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.red),
                    ),
                    disabledBorder: const OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.black),
                    ),
                  ),
                  style: const TextStyle(
                    fontSize: 16,
                    color: Colors.black,
                  )),
            ),
          );
        });
  }

  getMasterViewSelectionDialog(List<Map<String, dynamic>> masterViewData,
      List<String> masterViewColumns) {
    return showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
            builder: (BuildContext context, StateSetter setState) {
          return Dialog(
            child: Container(
              padding: const EdgeInsets.all(8.0),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(5),
                boxShadow: const [
                  BoxShadow(
                    color: Colors.black26,
                    blurRadius: 5,
                    spreadRadius: 2.5,
                  ),
                ],
                color: Colors.white,
              ),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8.0),
                    width: double.infinity,
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      borderRadius: BorderRadius.circular(5),
                    ),
                    child: Text(
                        '${(widget.item.displayName?.contains('Select') ?? false) ? '' : 'Select'} ${widget.item.displayName}',
                        style: const TextStyle(
                          fontSize: 18,
                          color: Colors.black,
                          fontWeight: FontWeight.bold,
                        )),
                  ),
                  TextField(
                    decoration: const InputDecoration(
                      contentPadding: EdgeInsets.all(5.0),
                      hintText: 'Search',
                      prefixIcon: Icon(Icons.search),
                    ),
                    onChanged: (value) {
                      setState(() {
                        masterViewData = masterViewData
                            .where((element) => element[masterViewColumns.last]
                                .toString()
                                .toLowerCase()
                                .contains(value.toLowerCase()))
                            .toList();
                      });
                    },
                  ),
                  const SizedBox(height: 10),
                  Expanded(
                    child: ListView.separated(
                      itemCount: masterViewData.length,
                      separatorBuilder: (context, index) => const Divider(),
                      itemBuilder: (context, index) {
                        return ListTile(
                          title: Text(
                            masterViewData[index][masterViewColumns.last],
                          ),
                          onTap: () {
                            final Box<dynamic> box =
                                Hive.box(widget.sessionToken);
                            box.put(widget.item.rowId,
                                masterViewData[index][masterViewColumns.first]);
                            box.put('${widget.item.rowId}_name',
                                masterViewData[index][masterViewColumns.last]);
                            Navigator.of(context).pop();
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          );
        });
      },
    );
  }

  Widget getTableView(MarketOverviewDynamicFormClass item) {
    return ValueListenableBuilder<Box>(
        valueListenable: Hive.box(widget.sessionToken).listenable(),
        builder: (context, box, child) {
          print('TYPE: ${item.type}');
          print('ROW ID: ${item.rowId}');
          print('DISPLAY NAME: ${item.displayName}');
          final String? value = box.get('${item.rowId}');
          WidgetsBinding.instance.addPostFrameCallback((_) {
            textEditingController.text = value ?? '';
          });
          bool isDependent = item.mandatory == 'DEPENDENT';
          if (isDependent) {
            String dependentRowId = item.validation?.split('#').first ?? '';
            String? dependentRowValue = box.get(dependentRowId);
            if (dependentRowValue != null) {
              List<String> validationRowValues =
                  item.validation?.split('#').last.split(';') ?? [];
              isEnable = false;
              for (String validationRowValue in validationRowValues) {
                if (dependentRowValue.contains(validationRowValue)) {
                  isEnable = true;
                  break;
                }
              }
              WidgetsBinding.instance.addPostFrameCallback((_) {
                setState(() {
                  isEnable = isEnable;
                });
              });
            }
          }

          if (isEnable) {
            return InkWell(
              onTap: !isEdit
                  ? null
                  : () async {
                      if (isDependent) {
                        String dependentRowId =
                            item.validation?.split('#').first ?? '';
                        if (box.get(dependentRowId) == null) {
                          final String dependentRowDispNameQuery =
                              "SELECT display_name FROM survey_input WHERE row_id = '$dependentRowId'";
                          final List<Map<String, dynamic>>
                              dependentRowDispNameResult =
                              await LocalDB.rawQuery(dependentRowDispNameQuery);
                          final String dependentRowDispName =
                              dependentRowDispNameResult.isEmpty
                                  ? ''
                                  : dependentRowDispNameResult
                                      .first['display_name']
                                      .toString();
                          textEditingController.text =
                              'Please select $dependentRowDispName first';
                          Future.delayed(const Duration(seconds: 2), () {
                            textEditingController.text = '';
                          });
                          return;
                        }
                      }
                      final String tableViewQuery =
                          "SELECT * FROM table_view WHERE row_id = '${item.rowId}'";
                      print(tableViewQuery);
                      List<Map<String, dynamic>> tableViewResult =
                          await LocalDB.rawQuery(tableViewQuery);
                      print(tableViewResult.first['value']);
                      final List<String> mwnuValues =
                          tableViewResult.first['value'].split('/') ?? [];
                      await getTableViewSelectionDialog(mwnuValues);
                      if ((item.action?.isNotEmpty ?? false) &&
                          box.get(item.rowId) != null) {
                        List<String> actionItems =
                            item.action?.split("\$") ?? [];
                        for (String actionItem in actionItems) {
                          String actionValidationOption =
                              actionItem.split(':').first;
                          if (actionValidationOption == box.get(item.rowId)) {
                            print(actionItem);
                            List<String> actionOptions =
                                actionItem.split('#')[1].split(':');
                            String heading =
                                actionItem.split('#').first.split(':').last;
                            getSubTableViewSelectionDialog(
                                actionOptions, heading);
                          }
                        }
                      }
                    },
              child: Container(
                margin: const EdgeInsets.symmetric(vertical: 10),
                width: double.infinity,
                alignment: Alignment.center,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(5),
                ),
                child: TextFormField(
                  controller: textEditingController,
                  validator: (value) {
                    if (item.mandatory == 'Y' && value!.isEmpty) {
                      return 'Please enter ${item.displayName}';
                    }
                    if (isDependent && value!.isEmpty) {
                      return 'Please enter ${item.displayName}';
                    }
                    return null;
                  },
                  enabled: false,
                  textAlign: TextAlign.center,
                  decoration: InputDecoration(
                    contentPadding: const EdgeInsets.all(5.0),
                    hintText:
                        '${(item.displayName?.contains('Select') ?? false) ? '' : 'Select'} ${item.displayName}',
                    focusColor: Colors.black,
                    border: const OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.black),
                    ),
                    errorBorder: const OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.red),
                    ),
                    disabledBorder: const OutlineInputBorder(
                      borderSide: BorderSide(color: Colors.black),
                    ),
                  ),
                  style: const TextStyle(
                    fontSize: 16,
                    color: Colors.black,
                  ),
                ),
              ),
            );
          } else {
            return const SizedBox();
          }
        });
  }

  getTableViewSelectionDialog(List<String> values) {
    return showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
            builder: (BuildContext context, StateSetter setState) {
          return Dialog(
            child: Container(
              padding: const EdgeInsets.all(8.0),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(5),
                boxShadow: const [
                  BoxShadow(
                    color: Colors.black26,
                    blurRadius: 5,
                    spreadRadius: 2.5,
                  ),
                ],
                color: Colors.white,
              ),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8.0),
                    width: double.infinity,
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      borderRadius: BorderRadius.circular(5),
                    ),
                    child: Text(
                        '${(widget.item.displayName?.contains('Select') ?? false) ? '' : 'Select'} ${widget.item.displayName}',
                        style: const TextStyle(
                          fontSize: 18,
                          color: Colors.black,
                          fontWeight: FontWeight.bold,
                        )),
                  ),
                  TextField(
                    decoration: const InputDecoration(
                      contentPadding: EdgeInsets.all(5.0),
                      hintText: 'Search',
                      prefixIcon: Icon(Icons.search),
                    ),
                    onChanged: (value) {
                      setState(() {
                        values = values
                            .where((element) => element
                                .toString()
                                .toLowerCase()
                                .contains(value.toLowerCase()))
                            .toList();
                      });
                    },
                  ),
                  const SizedBox(height: 10),
                  Expanded(
                    child: ListView.separated(
                      itemCount: values.length,
                      separatorBuilder: (context, index) => const Divider(),
                      itemBuilder: (context, index) {
                        return ListTile(
                          title: Text(
                            values[index],
                          ),
                          onTap: () {
                            final Box<dynamic> box =
                                Hive.box(widget.sessionToken);
                            box.put(widget.item.rowId, values[index]);
                            Navigator.of(context).pop();
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          );
        });
      },
    );
  }

  getSubTableViewSelectionDialog(List<String> values, String heading) {
    return showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
            builder: (BuildContext context, StateSetter setState) {
          return Dialog(
            child: Container(
              padding: const EdgeInsets.all(8.0),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(5),
                boxShadow: const [
                  BoxShadow(
                    color: Colors.black26,
                    blurRadius: 5,
                    spreadRadius: 2.5,
                  ),
                ],
                color: Colors.white,
              ),
              child: Column(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8.0),
                    width: double.infinity,
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      borderRadius: BorderRadius.circular(5),
                    ),
                    child: Text(
                        '${(heading.contains('Select')) ? '' : 'Select'} $heading',
                        style: const TextStyle(
                          fontSize: 18,
                          color: Colors.black,
                          fontWeight: FontWeight.bold,
                        )),
                  ),
                  TextField(
                    decoration: const InputDecoration(
                      contentPadding: EdgeInsets.all(5.0),
                      hintText: 'Search',
                      prefixIcon: Icon(Icons.search),
                    ),
                    onChanged: (value) {
                      setState(() {
                        values = values
                            .where((element) => element
                                .toString()
                                .toLowerCase()
                                .contains(value.toLowerCase()))
                            .toList();
                      });
                    },
                  ),
                  const SizedBox(height: 10),
                  Expanded(
                    child: ListView.separated(
                      itemCount: values.length,
                      separatorBuilder: (context, index) => const Divider(),
                      itemBuilder: (context, index) {
                        return ListTile(
                          title: Text(
                            values[index],
                          ),
                          onTap: () {
                            final Box<dynamic> box =
                                Hive.box(widget.sessionToken);
                            box.put(widget.item.rowId,
                                "${box.get(widget.item.rowId)}, ${values[index]}");
                            Navigator.of(context).pop();
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          );
        });
      },
    );
  }

  getDate(MarketOverviewDynamicFormClass item) {
    return ValueListenableBuilder(
        valueListenable: Hive.box(widget.sessionToken).listenable(),
        builder: (context, box, child) {
          final String? value = box.get('${item.rowId}');
          WidgetsBinding.instance.addPostFrameCallback((_) {
            textEditingController.text = value ?? '';
          });
          return InkWell(
            onTap: !isEdit
                ? null
                : () async {
                    final DateTime? picked = await showDatePicker(
                      context: context,
                      initialDate: DateTime.now(),
                      firstDate: DateTime.now()
                          .subtract(const Duration(days: 5 * 365)),
                      lastDate: (item.validation == '<=current_date')
                          ? DateTime.now()
                          : DateTime.now().add(const Duration(days: 5 * 365)),
                    );
                    if (picked != null) {
                      final String dateFormat = item.action ?? 'dd/MM/yyyy';
                      final String formattedDate =
                          DateFormat(dateFormat).format(picked);
                      final Box<dynamic> box = Hive.box(widget.sessionToken);
                      box.put(item.rowId, formattedDate);
                      textEditingController.text = formattedDate;
                    }
                  },
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10),
              width: double.infinity,
              alignment: Alignment.center,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(5),
              ),
              child: TextFormField(
                controller: textEditingController,
                validator: (value) {
                  if (item.mandatory == 'Y' && value!.isEmpty) {
                    return 'Please enter ${item.displayName}';
                  }
                  return null;
                },
                enabled: false,
                textAlign: TextAlign.center,
                decoration: InputDecoration(
                  contentPadding: const EdgeInsets.all(5.0),
                  hintText:
                      '${(item.displayName?.contains('Select') ?? false) ? '' : 'Select'} ${item.displayName}',
                  focusColor: Colors.black,
                  border: const OutlineInputBorder(
                    borderSide: BorderSide(color: Colors.black),
                  ),
                  errorBorder: const OutlineInputBorder(
                    borderSide: BorderSide(color: Colors.red),
                  ),
                ),
                style: const TextStyle(
                  fontSize: 16,
                  color: Colors.black,
                ),
              ),
            ),
          );
        });
  }

  getPhoto(MarketOverviewDynamicFormClass item) {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      textEditingController.text =
          'Click Photo ${item.mandatory == 'Y' ? '(Mandatory)' : ''}';
    });
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 10),
      width: double.infinity,
      alignment: Alignment.center,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(5),
      ),
      child: ValueListenableBuilder<Box>(
          valueListenable: Hive.box(widget.sessionToken).listenable(),
          builder: (context, box, child) {
            final List<String?>? imagesPath =
                box.get(item.rowId)?.split(',') ?? [];
            clickedImages =
                imagesPath?.map((e) => XFile(e ?? '')).toList() ?? [];
            return Column(
              children: [
                Padding(
                  padding: const EdgeInsets.all(10.0),
                  child: GridView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: imagesPath?.length ?? 0,
                    gridDelegate:
                        const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 3,
                      crossAxisSpacing: 5.0,
                      mainAxisSpacing: 5.0,
                    ),
                    itemBuilder: (context, index) {
                      return Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(10),
                        ),
                        clipBehavior: Clip.hardEdge,
                        child: InkWell(
                          onTap: () => imageOnTap(imagesPath?[index]),
                          child: Image.file(
                            File(imagesPath?[index] ?? ''),
                            fit: BoxFit.cover,
                          ),
                        ),
                      );
                    },
                  ),
                ),
                InkWell(
                  onTap: () async {
                    final int maxImages = int.parse(item.validation ?? '0');
                    if (clickedImages.length >= maxImages && maxImages != 0) {
                      textEditingController.text =
                          'Max $maxImages images allowed';
                      Future.delayed(const Duration(seconds: 2), () {
                        textEditingController.text =
                            "Click Photo ${item.mandatory == 'Y' ? '(Mandatory)' : ''}";
                      });
                      return;
                    }
                    final XFile? image =
                        await picker.pickImage(source: ImageSource.camera);
                    if (image != null) {
                      clickedImages.add(image);
                      final Box<dynamic> box = Hive.box(widget.sessionToken);
                      box.put(item.rowId,
                          clickedImages.map((e) => e?.path).toList().join(','));
                    }
                  },
                  child: Container(
                    padding: const EdgeInsets.all(8.0),
                    width: double.infinity,
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: TextFormField(
                      controller: textEditingController,
                      validator: (value) {
                        if (item.mandatory == 'Y' && clickedImages.isEmpty) {
                          return 'Please click photo for ${item.displayName}';
                        }
                        return null;
                      },
                      enabled: false,
                      textAlign: TextAlign.center,
                      decoration: InputDecoration(
                        contentPadding: const EdgeInsets.all(5.0),
                        hintText:
                            'Click Photo ${item.mandatory == 'Y' ? '(Mandatory)' : ''}',
                        focusColor: Colors.black,
                        border: const OutlineInputBorder(
                          borderSide: BorderSide(color: Colors.black),
                        ),
                        errorBorder: const OutlineInputBorder(
                          borderSide: BorderSide(color: Colors.red),
                        ),
                        disabledBorder: OutlineInputBorder(
                          borderSide: const BorderSide(color: Colors.black),
                          borderRadius: BorderRadius.circular(20),
                        ),
                      ),
                      style: const TextStyle(
                        fontSize: 16,
                        color: Colors.black,
                      ),
                    ),
                  ),
                ),
              ],
            );
          }),
    );
  }

  imageOnTap(String? filePath) {
    Image image = Image.file(File(filePath ?? ''));
    return showDialog(
      context: context,
      builder: (BuildContext context) {
        return Scaffold(
          appBar: null,
          backgroundColor: Colors.transparent,
          body: Container(
            margin: const EdgeInsets.all(10),
            width: double.infinity,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Container(
                  alignment: Alignment.topRight,
                  child: ElevatedButton(
                    style: ButtonStyle(
                      backgroundColor:
                          WidgetStateProperty.all<Color>(Colors.black),
                      shape: WidgetStateProperty.all<OutlinedBorder>(
                        RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(90),
                        ),
                      ),
                    ),
                    onPressed: () {
                      Navigator.pop(context, false);
                    },
                    child: const Icon(
                      Icons.close,
                      color: Colors.white,
                    ),
                  ),
                ),
                Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(5),
                  ),
                  clipBehavior: Clip.antiAlias,
                  child: image,
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget getWidget(MarketOverviewDynamicFormClass item) {
    switch (item.type) {
      case 'double':
        return getTextField(item);
      case ' ':
        return getTextField(item);
      case null:
        return getTextField(item);
      case 'date':
        return getDate(item);
      case 'radio':
        return getPhoto(item);
      case 'masterview':
        return getMasterView(item);
      case 'tableview':
        return getTableView(item);
      case 'dependenttableview':
        return getTableView(item);
      case 'multileveltableview':
        return getTableView(item);
      default:
        if (item.type?.contains(':') ?? false) {
          return getDropDown(item);
        } else {
          return const Text('Type Not Found');
        }
    }
  }

  dependentDataAutofill(MarketOverviewDynamicFormClass item) {
    final Box<dynamic> box = Hive.box(widget.sessionToken);
    String value = box.get(item.rowId) ?? '';
    if (value != '') {
      if (item.insertTableDetail?.contains('#') ?? false) {
        String edit = item.insertTableDetail?.split('#').first ?? '';
        if (edit == 'masterviewdisplay') {
          setState(() {
            isEdit = false;
          });
          print('isEdit: $isEdit');
        }
      }
      return;
    }
    if ((item.insertTableDetail?.contains('#') ?? false) &&
        (!(item.insertTableDetail?.contains('insert') ?? false)) &&
        (!(item.insertTableDetail?.contains('update') ?? false))) {
      String edit = item.insertTableDetail?.split('#').first ?? '';
      String tableName = item.insertTableDetail?.split('#')[1] ?? '';
      String column = item.insertTableDetail?.split('#')[2] ?? '';
      String queryValue =
          item.insertTableDetail?.split('#')[3].split(';').first ?? '';
      String dependentRowId =
          item.insertTableDetail?.split('#')[3].split(';').last ?? '';
      String dependentRowValue = box.get(dependentRowId) ?? '';
      if (dependentRowValue.isNotEmpty) {
        String query =
            "SELECT $column FROM $tableName WHERE $queryValue = '$dependentRowValue'";
        LocalDB.rawQuery(query).then((value) {
          if (value.isNotEmpty && value.first[column] != '') {
            box.put(item.rowId, value.first[column]);
            if (item.type == 'masterview') {
              String masterTable =
                  item.displayTableName?.split('#').first ?? '';
              List<String> masterColumns =
                  item.displayTableName?.split('#')[1].split('%') ?? [];
              String masterQuery =
                  "SELECT ${masterColumns.last} FROM $masterTable WHERE ${masterColumns.first} = '${value.first[column]}'";
              LocalDB.rawQuery(masterQuery).then((value) {
                if (value.isNotEmpty && value.first[masterColumns.last] != '') {
                  box.put(
                      '${item.rowId}_name', value.first[masterColumns.last]);
                }
              });
            }
            if (edit == 'masterviewdisplay') {
              setState(() {
                isEdit = false;
              });
              print('isEdit: $isEdit');
            }
          }
        });
      }
    }
  }

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    dependentDataAutofill(widget.item);
    return Container(
      height: isEnable ? null : 0,
      margin: isEnable ? const EdgeInsets.all(10) : null,
      padding: const EdgeInsets.all(8.0),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(5),
        boxShadow: isEnable
            ? const [
                BoxShadow(
                  color: Colors.black26,
                  blurRadius: 5,
                  spreadRadius: 2.5,
                ),
              ]
            : [],
        color: Colors.white,
      ),
      child: Column(
        children: [
          getLabel(widget.item.displayName),
          getWidget(widget.item),
        ],
      ),
    );
  }
}
