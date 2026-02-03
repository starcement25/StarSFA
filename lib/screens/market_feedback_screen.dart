// ignore_for_file: use_build_context_synchronously

import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:starsfa/models/competitor_group_master_class.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/market_feedback.dart';
import 'package:starsfa/models/mf_stk_audit_details.dart';
import 'package:starsfa/models/mf_stk_audit_header.dart';
import 'package:starsfa/models/route_master_class.dart';
import 'package:starsfa/screens/market_feedback_cart_screen.dart';  

class MarketFeedbackScreen extends StatefulWidget {
  final String sessionID;
  const MarketFeedbackScreen({super.key, required this.sessionID});

  @override
  State<MarketFeedbackScreen> createState() => _MarketFeedbackScreenState();
}

class _MarketFeedbackScreenState extends State<MarketFeedbackScreen> {
  final TextEditingController competitorNameController =
      TextEditingController();
  final TextEditingController quantityController = TextEditingController();
  final TextEditingController schemeController = TextEditingController();
  final TextEditingController billingController = TextEditingController();
  final TextEditingController wspController = TextEditingController();
  final TextEditingController rspController = TextEditingController();
  List<String> competitorTypeList = [];
  List<MarketFeedback> marketFeedbackList = [];
  List<MfStkAuditHeader> mfStkAuditHeaderList = [];
  List<MfStkAuditDetails> mfStkAuditDetailsList = [];
  final String competitorNameKey = 'competitorName';
  final String competitorTypeKey = 'competitorType';
  final String quantityKey = 'quantity';
  final String schemeKey = 'scheme';
  final String billingTypeKey = 'billingType';
  final String billingPriceKey = 'billingPrice';
  final String wspTypeKey = 'wspType';
  final String wspPriceKey = 'wspPrice';
  final String rspPriceKey = 'rspPrice';
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();
  bool isLoading = false;

  getLabel(String? label, {Color? color}) {
    if (label != null) {
      return Container(
        padding: const EdgeInsets.all(8.0),
        width: double.infinity,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          color: color ?? Colors.grey[200],
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

  Widget getCompetitorName() {
    return Container(
      margin: const EdgeInsets.all(10),
      padding: const EdgeInsets.all(8.0),
      decoration: BoxDecoration(
        // border: Border.all(color: Colors.grey),
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
          getLabel('Competitor Name'),
          ValueListenableBuilder<Box>(
              valueListenable: Hive.box(widget.sessionID).listenable(),
              builder: (context, box, child) {
                final String value =
                    box.get(competitorNameKey, defaultValue: '');
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  competitorNameController.text = value;
                });
                return InkWell(
                  onTap: () async {
                    final Box<dynamic> checkInBox = Hive.box('checkIn');
                    final String customerCode =
                        checkInBox.get('customerCode', defaultValue: '');
                    CustomerMasterDB getCustomerMasterDB =
                        await CustomerMasterDB
                            .getCustomerMasterDBByCustomerCode(customerCode);
                    final List<CompetitorGroupMasterDB> competitorMasterDBList =
                        await CompetitorGroupMasterDB.getCompetitorbyBranch(
                            getCustomerMasterDB.branchCode ?? '');
                    // print(tableViewResult);
                    // print(mwnuValues);
                    getCompetitorNameSelectionDialog(competitorMasterDBList);
                  },
                  child: Container(
                    margin: const EdgeInsets.symmetric(vertical: 10),
                    // padding: const EdgeInsets.all(8.0),
                    width: double.infinity,
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      // border: Border.all(color: Colors.black),
                      borderRadius: BorderRadius.circular(5),
                    ),
                    child: TextFormField(
                      controller: competitorNameController,
                      validator: (value) {
                        if (value!.isEmpty) {
                          return 'Please enter Competitor Name';
                        }
                        return null;
                      },
                      enabled: false,
                      // align text to center
                      textAlign: TextAlign.center,
                      decoration: const InputDecoration(
                        hintText: 'Select Competitor Name',
                        focusColor: Colors.black,
                        // Show border
                        border: OutlineInputBorder(
                          borderSide: BorderSide(color: Colors.black),
                        ),
                        errorBorder: OutlineInputBorder(
                          borderSide: BorderSide(color: Colors.red),
                        ),
                        disabledBorder: OutlineInputBorder(
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
              }),
        ],
      ),
    );
  }

  getCompetitorNameSelectionDialog(List<CompetitorGroupMasterDB> values) {
    String searchValue = '';
    return showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
            builder: (BuildContext context, StateSetter setState) {
          List<CompetitorGroupMasterDB> filteredValues = values
              .where((element) => (element.competitorName ?? '')
                  .toLowerCase()
                  .contains(searchValue.toLowerCase()))
              .toList();
          // sort by MANDATORY STAR, BENCHMARK COMPETITOR, OTHER COMPETITOR
          filteredValues.sort((b, a) {
            final Map<String, int> order = {
              'MANDATORY STAR': 2,
              'BENCHMARK COMPETITOR': 1,
            };
            return order[a.productType]?.compareTo(order[b.productType] ?? 0) ??
                0;
          });
          return Dialog(
            child: Container(
              // margin: const EdgeInsets.all(10),
              padding: const EdgeInsets.all(8.0),
              decoration: BoxDecoration(
                // border: Border.all(color: Colors.grey),
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
                    child: const Text('Select Competitor Name',
                        style: TextStyle(
                          fontSize: 18,
                          color: Colors.black,
                          fontWeight: FontWeight.bold,
                        )),
                  ),

                  // Search bar
                  TextField(
                    decoration: const InputDecoration(
                      hintText: 'Search',
                      prefixIcon: Icon(Icons.search),
                    ),
                    onChanged: (value) {
                      setState(() {
                        searchValue = value;
                      });
                    },
                  ),
                  const SizedBox(height: 10),
                  Expanded(
                    child: ListView.separated(
                      itemCount: filteredValues.length,
                      separatorBuilder: (context, index) => const Divider(),
                      itemBuilder: (context, index) {
                        Color? labelColor(String v) {
                          final Map<String, Color> colorMap = {
                            'BENCHMARK COMPETITOR':
                                Colors.blue[900] ?? Colors.black,
                            'MANDATORY STAR': Colors.red,
                            'OPTIONAL STAR': Colors.black,
                          };
                          return colorMap[v];
                        }

                        return ListTile(
                          title: Text(
                            filteredValues[index].competitorName ?? '',
                            style: TextStyle(
                              fontSize: 16,
                              color: labelColor(
                                  filteredValues[index].productType ?? ''),
                            ),
                          ),
                          onTap: () {
                            // get hive box with session token
                            final Box<dynamic> box = Hive.box(widget.sessionID);
                            box.put(competitorNameKey,
                                filteredValues[index].competitorName ?? '');
                            box.put(competitorTypeKey,
                                filteredValues[index].productType ?? '');
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

  getQuantity() {
    return Container(
      margin: const EdgeInsets.all(10),
      padding: const EdgeInsets.all(8.0),
      decoration: BoxDecoration(
        // border: Border.all(color: Colors.grey),
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
          getLabel('Quantity(MT)'),
          ValueListenableBuilder<Box>(
              valueListenable: Hive.box(widget.sessionID).listenable(),
              builder: (context, box, child) {
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  quantityController.text = box.get(quantityKey) ?? '';
                });
                return Padding(
                  padding: const EdgeInsets.symmetric(vertical: 10.0),
                  child: TextFormField(
                    controller: quantityController,
                    // validator: (value) {
                    //   if (value!.isEmpty) {
                    //     return 'Please enter Quantity';
                    //   }
                    //   return null;
                    // },
                    textAlign: TextAlign.center,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(
                      hintText: 'Enter Quantity',
                      focusColor: Colors.black,
                      // Show border
                      border: OutlineInputBorder(
                        borderSide: BorderSide(color: Colors.black),
                      ),
                      errorBorder: OutlineInputBorder(
                        borderSide: BorderSide(color: Colors.red),
                      ),
                    ),
                    onChanged: (value) {
                      // get hive box with session token
                      final Box<dynamic> box = Hive.box(widget.sessionID);
                      box.put(quantityKey, value);
                    },
                  ),
                );
              }),
        ],
      ),
    );
  }

  getScheme() {
    return Container(
      margin: const EdgeInsets.all(10),
      padding: const EdgeInsets.all(8.0),
      decoration: BoxDecoration(
        // border: Border.all(color: Colors.grey),
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
          getLabel('Scheme'),
          ValueListenableBuilder<Box>(
              valueListenable: Hive.box(widget.sessionID).listenable(),
              builder: (context, box, child) {
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  schemeController.text = box.get(schemeKey) ?? '';
                });
                return Padding(
                  padding: const EdgeInsets.symmetric(vertical: 10.0),
                  child: TextFormField(
                    controller: schemeController,
                    // validator: (value) {
                    //   if (value!.isEmpty) {
                    //     return 'Please enter Quantity';
                    //   }
                    //   return null;
                    // },
                    textAlign: TextAlign.center,
                    keyboardType: TextInputType.text,
                    decoration: const InputDecoration(
                      hintText: 'Enter Scheme (if any)',
                      focusColor: Colors.black,
                      // Show border
                      border: OutlineInputBorder(
                        borderSide: BorderSide(color: Colors.black),
                      ),
                      errorBorder: OutlineInputBorder(
                        borderSide: BorderSide(color: Colors.red),
                      ),
                    ),
                    onChanged: (value) {
                      // get hive box with session token
                      final Box<dynamic> box = Hive.box(widget.sessionID);
                      box.put(schemeKey, value);
                    },
                  ),
                );
              }),
        ],
      ),
    );
  }

  getPriceDetails() {
    return Container(
      margin: const EdgeInsets.all(10),
      padding: const EdgeInsets.all(8.0),
      decoration: BoxDecoration(
        // border: Border.all(color: Colors.grey),
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
          getLabel('Price Details'),
          ValueListenableBuilder<Box>(
              valueListenable: Hive.box(widget.sessionID).listenable(),
              builder: (context, box, child) {
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  billingController.text = box.get(billingPriceKey) ?? '';
                  wspController.text = box.get(wspPriceKey) ?? '';
                  rspController.text = box.get(rspPriceKey) ?? '';
                });
                final String billingType = box.get(billingTypeKey) ?? '';
                final String wspType = box.get(wspTypeKey) ?? '';
                return Row(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Column(
                          children: [
                            getLabel(
                                'Billing ${billingType == '' ? '' : '($billingType)'}',
                                color: Colors.yellow[200]),
                            const SizedBox(height: 10),
                            TextFormField(
                              onTap: () {
                                getPriceType(billingTypeKey);
                              },
                              controller: billingController,
                              validator: (value) {
                                if (value!.isEmpty) {
                                  return 'Please enter Billing';
                                }else if(int.parse(value) < 300||int.parse(value) > 700){
                                  return 'Billing/Bag value must be with in 300 to 700';
                                  }
                                return null;
                              },
                              textAlign: TextAlign.center,
                              keyboardType: TextInputType.number,
                              decoration: const InputDecoration(
                                hintText: 'Billing',
                                focusColor: Colors.black,
                                // Show border
                                border: OutlineInputBorder(
                                  borderSide: BorderSide(color: Colors.black),
                                ),
                                errorBorder: OutlineInputBorder(
                                  borderSide: BorderSide(color: Colors.red),
                                ),
                              ),
                              onChanged: (value) {
                                // get hive box with session token
                                final Box<dynamic> box =
                                    Hive.box(widget.sessionID);
                                box.put(billingPriceKey, value);
                              },
                            ),
                          ],
                        ),
                      ),
                    ),
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Column(
                          children: [
                            getLabel('WSP ${wspType == '' ? '' : '($wspType)'}',
                                color: Colors.pink[200]),
                            const SizedBox(height: 10),
                            TextFormField(
                              onTap: () {
                                getPriceType(wspTypeKey);
                              },
                              controller: wspController,
                              validator: (value) {
                                if (value!.isEmpty) {
                                  return 'Please enter WSP';
                                }else if(int.parse(value) < 300||int.parse(value) > 700){
                                  return 'WSP/Bag value must be with in 300 to 700';
                                  }
                                return null;
                              },
                              textAlign: TextAlign.center,
                              keyboardType: TextInputType.number,
                              decoration: const InputDecoration(
                                hintText: 'WSP',
                                focusColor: Colors.black,
                                // Show border
                                border: OutlineInputBorder(
                                  borderSide: BorderSide(color: Colors.black),
                                ),
                                errorBorder: OutlineInputBorder(
                                  borderSide: BorderSide(color: Colors.red),
                                ),
                              ),
                              onChanged: (value) {
                                // get hive box with session token
                                final Box<dynamic> box =
                                    Hive.box(widget.sessionID);
                                box.put(wspPriceKey, value);
                              },
                            ),
                          ],
                        ),
                      ),
                    ),
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Column(
                          children: [
                            getLabel('RSP', color: Colors.red[200]),
                            const SizedBox(height: 10),
                            TextFormField(
                              controller: rspController,
                              validator: (value) {
                                if (value!.isEmpty) {
                                  return 'Please enter RSP';
                                }else if(int.parse(value) < 300||int.parse(value) > 700){
                                  return 'RSP/Bag value must be with in 300 to 700';
                                  }
                                return null;
                              },
                              textAlign: TextAlign.center,
                              keyboardType: TextInputType.number,
                              decoration: const InputDecoration(
                                hintText: 'RSP',
                                focusColor: Colors.black,
                                // Show border
                                border: OutlineInputBorder(
                                  borderSide: BorderSide(color: Colors.black),
                                ),
                                errorBorder: OutlineInputBorder(
                                  borderSide: BorderSide(color: Colors.red),
                                ),
                              ),
                              onChanged: (value) {
                                // get hive box with session token
                                final Box<dynamic> box =
                                    Hive.box(widget.sessionID);
                                box.put(rspPriceKey, value);
                              },
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                );
              }),
        ],
      ),
    );
  }

  getPriceType(String key) {
    final List<String> values = ['EX', 'FOR'];
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return PopScope(
          canPop: false,
          child: Dialog(
            child: Container(
              height: values.length * 100.0,
              padding: const EdgeInsets.all(8.0),
              decoration: BoxDecoration(
                // border: Border.all(color: Colors.grey),
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
                  getLabel('Select Price Type'),
                  const SizedBox(height: 10),
                  Flexible(
                    child: ListView.separated(
                      itemCount: values.length,
                      separatorBuilder: (context, index) => const Divider(),
                      itemBuilder: (context, index) {
                        return ListTile(
                          title: Text(
                            values[index],
                          ),
                          onTap: () {
                            // get hive box with session token
                            final Box<dynamic> box = Hive.box(widget.sessionID);
                            box.put(key, values[index]);
                            Navigator.of(context).pop();
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  void _syncCart({bool reverse = false}) {
    final Box<dynamic> box = Hive.box('cart');
    if (!reverse) {
      box.put('competitorTypeList', competitorTypeList);
      box.put('marketFeedbackList', marketFeedbackList);
      box.put('mfStkAuditHeaderList', mfStkAuditHeaderList);
      box.put('mfStkAuditDetailsList', mfStkAuditDetailsList);
    } else {
      competitorTypeList = box.get('competitorTypeList', defaultValue: []);
      marketFeedbackList = box.get('marketFeedbackList', defaultValue: []);
      mfStkAuditHeaderList = box.get('mfStkAuditHeaderList', defaultValue: []);
      mfStkAuditDetailsList =
          box.get('mfStkAuditDetailsList', defaultValue: []);
    }
  }

  @override
  void initState() {
    super.initState();
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
            // pop the screen
            Navigator.of(context).pop();
          },
        ),
        backgroundColor: Colors.red,
        title: const Text(
          'Market Feedback',
          style: TextStyle(
            color: Colors.white,
          ),
        ),
      ),
      body: Column(
        children: <Widget>[
          // Customer Details
          const CustomerDetailsMarketFeedback(),
          Expanded(
            child: SingleChildScrollView(
              child: Form(
                key: _formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // Competitor Name
                    getCompetitorName(),
                    // Quantity
                    getQuantity(),
                    // Scheme
                    getScheme(),
                    // Price Details
                    getPriceDetails(),
                    // Submit Button
                    Row(
                      children: [
                        Flexible(
                          flex: 1,
                          child: InkWell(
                            onTap: () async {
                              setState(() {
                                isLoading = true;
                              });
                              await submit(context);
                              setState(() {
                                isLoading = false;
                              });
                            },
                            child: Container(
                              margin: const EdgeInsets.all(10),
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(
                                color: Colors.red,
                                borderRadius: BorderRadius.circular(5),
                              ),
                              child: Center(
                                child: isLoading
                                    ? const CircularProgressIndicator(
                                        color: Colors.white,
                                      )
                                    : const Text(
                                        'Add to Cart',
                                        style: TextStyle(
                                          color: Colors.white,
                                          fontSize: 16,
                                        ),
                                      ),
                              ),
                            ),
                          ),
                        ),
                        Flexible(
                          flex: 1,
                          child: InkWell(
                            onTap: () async {
                              checkOut();
                            },
                            child: Container(
                              margin: const EdgeInsets.all(10),
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(
                                color: Colors.red,
                                borderRadius: BorderRadius.circular(5),
                              ),
                              child: Center(
                                child: isLoading
                                    ? const CircularProgressIndicator(
                                        color: Colors.white,
                                      )
                                    : const Text(
                                        'Check Out',
                                        style: TextStyle(
                                          color: Colors.white,
                                          fontSize: 16,
                                        ),
                                      ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Future<void> submit(BuildContext context) async {
    if (_formKey.currentState!.validate()) {
      // get hive box with session token
      final Box<dynamic> box = Hive.box(widget.sessionID);
      // get values from the box
      // "INSERT INTO app_variables VALUES ('${AppWebService.nickname}', 'customer_code', '$customerCode', 'check_in')",
      final String customerCode = await LocalDB.rawQuery(
        "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'customer_code'",
      ).then((value) => value[0]['variable_value']);
      final String routeCode = await LocalDB.rawQuery(
        "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'route_code'",
      ).then((value) => value[0]['variable_value']);
      final String competitorName =
          box.get(competitorNameKey, defaultValue: '');
      final String quantity = box.get(quantityKey, defaultValue: '');
      final String scheme = box.get(schemeKey, defaultValue: '');
      final String billingType = box.get(billingTypeKey, defaultValue: '');
      final String billingPrice = box.get(billingPriceKey, defaultValue: '');
      final String wspType = box.get(wspTypeKey, defaultValue: '');
      final String wspPrice = box.get(wspPriceKey, defaultValue: '');
      final String rspPrice = box.get(rspPriceKey, defaultValue: '');
      competitorTypeList.add(box.get(competitorTypeKey, defaultValue: ''));
      final MfStkAuditHeader mfStkAuditHeader = MfStkAuditHeader(
        // mfStkAuditId: transId.replaceFirst('MF', 'MS'),
        mfStkAuditId: '',
        customerCode: customerCode,
        remarks: '',
        image: '',
      );
      mfStkAuditHeaderList.add(mfStkAuditHeader);
      // final bool isSavedMfStkAuditHeader =
      //     await MfStkAuditHeader.saveMfStkAuditHeader(mfStkAuditHeader);
      // if (!isSavedMfStkAuditHeader) {
      //   // show snackbar
      //   // ignore: use_build_context_synchronously
      //   ScaffoldMessenger.of(context).showSnackBar(
      //     const SnackBar(
      //       content: Text('Error: Market Feedback not saved'),
      //     ),
      //   );
      //   return;
      // }
      final MfStkAuditDetails mfStkAuditDetails = MfStkAuditDetails(
        // mfStkAuditId: transId.replaceFirst('MF', 'MS'),
        mfStkAuditId: '',
        competitorName: competitorName,
        qtyMt: quantity,
        schemeDiscount: scheme,
      );
      mfStkAuditDetailsList.add(mfStkAuditDetails);
      // final bool isSavedMfStkAuditDetails =
      //     await MfStkAuditDetails.saveMarketFeedbackDetails(mfStkAuditDetails);
      // if (!isSavedMfStkAuditDetails) {
      //   // show snackbar
      //   // ignore: use_build_context_synchronously
      //   ScaffoldMessenger.of(context).showSnackBar(
      //     const SnackBar(
      //       content: Text('Error: Market Feedback Details not saved'),
      //     ),
      //   );
      //   return;
      // }
      // MF - Market Feedback
      // final LocationClass locationMF = LocationClass(
      //   empCode: user?.empCode ?? '',
      //   latt: position.latitude.toString(),
      //   longi: position.longitude.toString(),
      //   date: DateFormat('yyyy-MM-dd hh:mm:ss').format(DateTime.now()),
      //   transId: transId,
      // );
      // await LocationClass.saveLocation(locationMF);
      // if (!isSavedLocation) {
      //   // show snackbar
      //   // ignore: use_build_context_synchronously
      //   ScaffoldMessenger.of(context).showSnackBar(
      //     const SnackBar(
      //       content: Text('Error: Location not saved'),
      //     ),
      //   );
      //   return;
      // }
      bool isMatchFound = marketFeedbackList.any((item) => item.competitorName == competitorName);
      if(isMatchFound){
ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          // ignore: prefer_interpolation_to_compose_strings
          content: Text('You already added '+competitorName+" ."),
        ),
      );
      }else{
        final MarketFeedback marketFeedback = MarketFeedback(
        // marketFeedbackId: transId,
        marketFeedbackId: '',
        customerCode: customerCode,
        routeCode: routeCode,
        competitorName: competitorName,
        billingExFor: billingType,
        ptd: billingPrice,
        wspExFor: wspType,
        ptr: wspPrice,
        ptc: rspPrice,
        productGroup: '',
        pv: '',
        rspExFor: '',
        nodExFor: '',
      );
      marketFeedbackList.add(marketFeedback);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Market Feedback added successfully'),
        ),
      );
      box.clear();
      _syncCart();
      }
      
    }
  }

  void checkOut() async {
    setState(() {
      isLoading = true;
    });
    String message = '';
    bool isValid = false;
    String customerType = await LocalDB.rawQuery(
      "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'customer_type'",
    ).then((value) => value[0]['variable_value']);
    log('Competitor Type List: $competitorTypeList');
    log('Customer Type: $customerType');
    // check if any competitor type is added
    if (competitorTypeList.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please add to cart'),
        ),
      );
      setState(() {
        isLoading = false;
      });
      return;
    }

    log(customerType.toUpperCase());

    switch (customerType.toUpperCase()) {
      case "EXCLUSIVE DEALER":
        message = "Add minimum One MANDATORY STAR";
        // check if mandatory star is added in productTypeList
        if (competitorTypeList.contains("MANDATORY STAR")) {
          isValid = true;
        } else {
          isValid = false;
        }
        break;
      case "NON STAR":
        message = "Add minimum One BENCHMARK COMPETITOR/OTHER COMPETITOR";
        // check if benchmark competitor or other competitor is added in productTypeList
        if (competitorTypeList.contains("BENCHMARK COMPETITOR") ||
            competitorTypeList.contains("OTHER COMPETITOR")) {
          isValid = true;
        } else {
          isValid = false;
        }
        break;
      case "SUB DEALER":
        message =
            "Add minimum MANDATORY STAR & BENCHMARK COMPETITOR/OTHER COMPETITOR";
        // check if mandatory star and benchmark competitor or other competitor is added in productTypeList
        if (competitorTypeList.contains("MANDATORY STAR") &&
            (competitorTypeList.contains("BENCHMARK COMPETITOR") ||
                competitorTypeList.contains("OTHER COMPETITOR"))) {
          isValid = true;
        } else {
          isValid = false;
        }
        break;
      case "DEALER":
        message =
            "Add minimum One MANDATORY STAR & BENCHMARK COMPETITOR/OTHER COMPETITOR";
        // check if mandatory star and benchmark competitor or other competitor is added in productTypeList
        if (competitorTypeList.contains("MANDATORY STAR") &&
            (competitorTypeList.contains("BENCHMARK COMPETITOR") ||
                competitorTypeList.contains("OTHER COMPETITOR"))) {
          isValid = true;
        } else {
          isValid = false;
        }
        break;
      case "SHIP TO PARTY":
        message =
            "Add minimum One";
        // check if mandatory star and benchmark competitor or other competitor is added in productTypeList
        if (competitorTypeList.contains("MANDATORY STAR")) {
          isValid = true;
        } else {
          isValid = false;
        }
        break;
      default:
        message = "Customer type is invalid";
        isValid =
            false; // Assuming validation passes if none of the above conditions are met
    }
    setState(() {
      isLoading = false;
    });
    if (isValid) {
      // Goto Check Out
      Navigator.of(context)
          .push(
            MaterialPageRoute(
              builder: (context) => MarketFeedbackCartScreen(
                marketFeedbackList: marketFeedbackList,
                mfStkAuditDetailsList: mfStkAuditDetailsList,
                mfStkAuditHeaderList: mfStkAuditHeaderList,
                competitorTypeList: competitorTypeList,
              ),
            ),
          )
          .whenComplete(() => _syncCart(reverse: true));
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(message),
        ),
      );
      return;
    }
  }
}

class CustomerDetailsMarketFeedback extends StatefulWidget {
  const CustomerDetailsMarketFeedback({super.key});

  @override
  State<CustomerDetailsMarketFeedback> createState() =>
      _CustomerDetailsMarketFeedbackState();
}

class _CustomerDetailsMarketFeedbackState
    extends State<CustomerDetailsMarketFeedback> {
  String routeName = '';

  void getRouteName() async {
    final Box box = Hive.box('checkIn');
    final String routeCode = box.get('routeCode', defaultValue: '');
    if (routeCode.isEmpty) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('No Route found for this Customer'),
        ),
      );
      return;
    }
    final RouteMasterDB routeMasterDB =
        await RouteMasterDB.getRouteMasterDBByRouteCode(routeCode);
    setState(() {
      routeName = routeMasterDB.routeName ?? '';
    });
  }

  @override
  void initState() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      getRouteName();
    });
    super.initState();
    Hive.openBox('cart');
  }

  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder<Box>(
        valueListenable: Hive.box('checkIn').listenable(),
        builder: (BuildContext context, Box box, Widget? child) {
          final String customerCode = box.get('customerCode', defaultValue: '');
          Future<CustomerMasterDB> getCustomerMasterDB =
              CustomerMasterDB.getCustomerMasterDBByCustomerCode(customerCode);
          return FutureBuilder(
            future: getCustomerMasterDB,
            builder: (BuildContext context,
                AsyncSnapshot<CustomerMasterDB> snapshot) {
              if (snapshot.connectionState == ConnectionState.done) {
                if (snapshot.hasData) {
                  final CustomerMasterDB customer =
                      snapshot.data ?? CustomerMasterDB();
                  return Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(10),
                    margin: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        // Customer Details
                        const Text(
                          'Customer Details',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        Text('Customer: ${customer.customerName}'),
                        // Text('Customer Code: ${customer.customerCode}'),
                        Text('Route: $routeName'),
                      ],
                    ),
                  );
                } else {
                  return const Text('No Customer Details');
                }
              } else {
                return const Center(
                  child: CircularProgressIndicator(
                    color: Colors.black,
                  ),
                );
              }
            },
          );
        });
  }
}
