import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/market_overview_dynamic_form_class.dart';

class MarketOverviewDynamicFormData extends StatefulWidget {
  final String menuName;
  final String showName;
  final String itemRowId;
  const MarketOverviewDynamicFormData(
      {super.key,
      required this.menuName,
      required this.showName,
      required this.itemRowId});

  @override
  State<MarketOverviewDynamicFormData> createState() =>
      _MarketOverviewDynamicFormDataState();
}

class _MarketOverviewDynamicFormDataState
    extends State<MarketOverviewDynamicFormData> {
  late Future<List<MarketOverviewDynamicFormClass>> getFormItems;
  late Future<List<Map<String, String>>> availableSurveysFuture;
  String selectedSurveyId = '';

  Future<List<MarketOverviewDynamicFormClass>>
      getFormItemsFromLocalDBbySurveyId(String surveyId) async {
    final localDB = await LocalDB.openMyDatabase();
    String query = '''
  SELECT survey_output.*, survey_input.display_name
  FROM survey_output
  JOIN survey_input ON survey_output.row_id = survey_input.row_id
  WHERE survey_output.survey_id = '$surveyId'
  ORDER BY survey_input.display_order ASC
  ''';
    final List<Map<String, dynamic>> formItemsResult =
        await localDB.rawQuery(query);

    final List<MarketOverviewDynamicFormClass> formItems = await Future.wait(
      formItemsResult.map((e) async {
        final mutableMap = Map<String, dynamic>.from(e);
        final List<Map<String, dynamic>> branchResult = await localDB.rawQuery(
            "SELECT branch_name FROM branch_master WHERE branch_code = '${mutableMap['value']}' LIMIT 1");
        print(
            "SELECT branch_name FROM branch_master WHERE branch_code = '${mutableMap['value']}' LIMIT 1");
        if (branchResult.isNotEmpty) {
          mutableMap['value'] = branchResult[0]['branch_name'].toString();
        } else {
          final List<
              Map<String,
                  dynamic>> customerResult = await localDB.rawQuery(
              "SELECT customer_name FROM customer_master WHERE customer_code = '${mutableMap['value']}' LIMIT 1");
          print(
              "SELECT customer_name FROM customer_master WHERE customer_code = '${mutableMap['value']}' LIMIT 1");
          if (customerResult.isNotEmpty) {
            mutableMap['value'] = customerResult[0]['customer_name'].toString();
          } else {
            print("No data found");
          }
        }
        return MarketOverviewDynamicFormClass.fromJson(mutableMap);
      }).toList(),
    );

    return formItems;
  }

  Future<List<Map<String, String>>> availableSurveys() async {
    String query =
        "SELECT DISTINCT survey_id FROM survey_header WHERE survey_type LIKE '%${widget.menuName}%' AND menu_name LIKE '%${widget.itemRowId}%' ORDER BY survey_id DESC";
    final List<Map<String, dynamic>> surveyIdsResult =
        await LocalDB.rawQuery(query);
    final List<String> surveyIds =
        surveyIdsResult.map((e) => e['survey_id'].toString()).toList();
    final List<Map<String, String>> surveyNames = [];
    for (int i = 0; i < surveyIds.length; i++) {
      query =
          "SELECT value FROM survey_output WHERE survey_id = '${surveyIds[i]}' LIMIT 1";
      final List<Map<String, dynamic>> surveyNamesResult =
          await LocalDB.rawQuery(query);
      String branchCode = surveyNamesResult[0]['value'].toString();

      // Fetch branch_name from branch_master using branch_code
      final List<Map<String, dynamic>> branchResult = await LocalDB.rawQuery(
          "SELECT branch_name FROM branch_master WHERE branch_code = '$branchCode' LIMIT 1");
      String surveyName = branchResult.isNotEmpty
          ? branchResult[0]['branch_name'].toString()
          : branchCode;

      // get date time from SUE055520240718113633 - (7-21) - YYYYMMDDHHMMSS
      String dateTime = surveyIds[i].substring(7, 21);
      // Insert separators to match the ISO 8601 format (YYYY-MM-DDTHH:MM:SS)
      String formattedDateTime =
          "${dateTime.substring(0, 4)}-${dateTime.substring(4, 6)}-${dateTime.substring(6, 8)}T${dateTime.substring(8, 10)}:${dateTime.substring(10, 12)}:${dateTime.substring(12, 14)}";

      // Parse the formatted datetime string
      DateTime parsedDateTime = DateTime.parse(formattedDateTime);

      surveyName =
          '$surveyName - ${DateFormat('dd MMM yyyy HH:mm:ss').format(parsedDateTime)}';
      surveyNames.add({'survey_id': surveyIds[i], 'survey_name': surveyName});
    }
    return surveyNames;
  }

  @override
  void initState() {
    super.initState();
    availableSurveysFuture = availableSurveys();
    getFormItems = getFormItemsFromLocalDBbySurveyId(selectedSurveyId);
    Future.delayed(const Duration(seconds: 1), () {
      setState(() {
        getFormItems = getFormItemsFromLocalDBbySurveyId(selectedSurveyId);
      });
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
          widget.showName,
          style: const TextStyle(color: Colors.white),
        ),
      ),
      body: Column(
        children: [
          // List of available surveys
          FutureBuilder<List<Map<String, String>>>(
              future: availableSurveysFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(
                    child: CircularProgressIndicator(),
                  );
                }
                if (snapshot.hasError) {
                  print('Error: ${snapshot.error}');
                  return const Center(
                    child: Text('Error loading data'),
                  );
                }
                final List<Map<String, String>> surveyNames =
                    snapshot.data ?? [];
                if (surveyNames.isEmpty) {
                  return const Center(
                    child: Text('No surveys available',
                        style: TextStyle(
                          color: Colors.red,
                          fontSize: 16,
                        )),
                  );
                }
                selectedSurveyId = surveyNames[0]['survey_id'].toString();
                return Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(10),
                  margin: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: Colors.grey[300],
                    borderRadius: BorderRadius.circular(10),
                  ),
                  clipBehavior: Clip.antiAlias,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Select Survey',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      DropdownButton<String>(
                        isExpanded: true,
                        value: selectedSurveyId,
                        items: surveyNames.map<DropdownMenuItem<String>>(
                            (Map<String, String> survey) {
                          return DropdownMenuItem<String>(
                            value: survey['survey_id'],
                            child: Text(
                              survey['survey_name'].toString(),
                            ),
                          );
                        }).toList(),
                        onChanged: (String? surveyId) {
                          setState(() {
                            selectedSurveyId = surveyId!;
                            getFormItems =
                                getFormItemsFromLocalDBbySurveyId(surveyId);
                          });
                        },
                      ),
                    ],
                  ),
                );
              }),
          // List of form items
          Expanded(
            child: FutureBuilder<List<MarketOverviewDynamicFormClass>>(
                future: getFormItems,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return const Center(
                      child: CircularProgressIndicator(),
                    );
                  }
                  if (snapshot.hasError) {
                    print('Error: ${snapshot.error}');
                    return const Center(
                      child: Text('Error loading data'),
                    );
                  }
                  final List<MarketOverviewDynamicFormClass> formItems =
                      snapshot.data ?? [];
                  if (formItems.isEmpty) {
                    return const Center(
                      child: Text('No form items available',
                          style: TextStyle(
                            color: Colors.red,
                            fontSize: 16,
                          )),
                    );
                  }
                  // show form items in a table
                  return Container(
                    margin: const EdgeInsets.all(10),
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: SingleChildScrollView(
                        scrollDirection: Axis.vertical,
                        child: Column(
                          children: [
                            ...formItems.map((formItem) {
                              return Container(
                                margin: const EdgeInsets.only(bottom: 10),
                                child: Row(
                                  children: [
                                    Expanded(
                                      flex: 2,
                                      child: Text(
                                        formItem.displayName ?? '',
                                        style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ),
                                    Expanded(
                                      flex: 3,
                                      child: Container(
                                        margin: const EdgeInsets.only(left: 10),
                                        padding: const EdgeInsets.all(5),
                                        decoration: BoxDecoration(
                                          color: Colors.white,
                                          borderRadius:
                                              BorderRadius.circular(5),
                                        ),
                                        child: Text(
                                          formItem.value ?? '',
                                          style: const TextStyle(
                                            fontSize: 14,
                                          ),
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              );
                            }).toList(),
                          ],
                        )),
                  );
                }),
          ),
        ],
      ),
    );
  }
}
