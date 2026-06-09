// ignore_for_file: use_build_context_synchronously

import 'dart:convert';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:http/http.dart' as http;

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/determine_position.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/location_class.dart';
import 'package:starsfa/models/market_feedback.dart';
import 'package:starsfa/models/mf_stk_audit_details.dart';
import 'package:starsfa/models/mf_stk_audit_header.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/check_in_menus.dart';

class MarketFeedbackCartScreen extends StatefulWidget {
  final List<MarketFeedback> marketFeedbackList;
  final List<MfStkAuditDetails> mfStkAuditDetailsList;
  final List<String> competitorTypeList;
  final List<MfStkAuditHeader> mfStkAuditHeaderList;

  const MarketFeedbackCartScreen(
      {super.key,
      required this.marketFeedbackList,
      required this.mfStkAuditDetailsList,
      required this.competitorTypeList,
      required this.mfStkAuditHeaderList});

  @override
  State<MarketFeedbackCartScreen> createState() =>
      _MarketFeedbackCartScreenState();
}

class _MarketFeedbackCartScreenState extends State<MarketFeedbackCartScreen> {
  final ImagePicker picker = ImagePicker();
  String remarks = '';
  bool isLoading = false;

  Future<String> getRemarks(BuildContext context) async {
    // show a dialog to get remarks
    String remarks = '';
    await showDialog(
      context: context,
      builder: (context) {
        return PopScope(
          canPop: false,
          child: AlertDialog(
            title: const Text('Remarks'),
            content: TextField(
              onChanged: (value) {
                remarks = value;
              },
            ),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.of(context).pop();
                },
                child: const Text('Ok'),
              ),
            ],
          ),
        );
      },
    );
    return remarks;
  }

  void submit() async {
    if (widget.marketFeedbackList.isEmpty) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('No data to save'),
        ),
      );
      return;
    }
    setState(() {
      isLoading = true;
    });
    final String customerCode = await LocalDB.rawQuery(
      "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'customer_code'",
    ).then((value) => value[0]['variable_value']);
    final String routeCode = await LocalDB.rawQuery(
      "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'route_code'",
    ).then((value) => value[0]['variable_value']);
    // get picture
    final bool isTakePicture = await takePicture();
    String imagePath = '';
    if (isTakePicture) {
      final XFile? image = await picker.pickImage(source: ImageSource.camera);
      imagePath = image?.path ?? '';
    }
    // get location details
    final position = await DeterminePosition.getPosition(null, null, null);
    if (position.latitude == null || position.longitude == null) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Location not found'),
        ),
      );
      return;
    }
    final user = await UserLoginClass.getLocalUser();
    String purposeOfVisit = await LocalDB.rawQuery(
      "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'purpose_of_visit'",
    ).then((value) => value[0]['variable_value']);
    purposeOfVisit = purposeOfVisit.replaceAll('[', '');
    purposeOfVisit = purposeOfVisit.replaceAll(']', '');
    String transId =
        'MF${user?.empCode}${DateFormat('yyyyMMddhhmmss').format(DateTime.now())}';
    // MS - Market Feedback
    final LocationClass locationMS = LocationClass(
      empCode: user?.empCode ?? '',
      latt: position.latitude.toString(),
      longi: position.longitude.toString(),
      date: DateFormat('yyyy-MM-dd hh:mm:ss').format(DateTime.now()),
      transId: transId.replaceFirst('MF', 'MS'),
      purposeOfVisit: purposeOfVisit,
    );
    final bool isSavedLocation = await LocationClass.saveLocation(locationMS);
    if (!isSavedLocation) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Error: Location not saved'),
        ),
      );
      return;
    }

    final MfStkAuditHeader mfStkAuditHeader = MfStkAuditHeader(
      mfStkAuditId: transId.replaceFirst('MF', 'MS'),
      customerCode: customerCode,
      remarks: remarks,
      image: imagePath,
    );
    final bool isSavedMfStkAuditHeader =
        await MfStkAuditHeader.saveMfStkAuditHeader(mfStkAuditHeader);
    if (!isSavedMfStkAuditHeader) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Error: MfStkAuditHeader not saved'),
        ),
      );
      return;
    }
    widget.mfStkAuditDetailsList.forEach((e) async {
      final MfStkAuditDetails mfStkAuditDetails = MfStkAuditDetails(
        mfStkAuditId: transId.replaceFirst('MF', 'MS'),
        competitorName: e.competitorName,
        qtyMt: e.qtyMt,
        schemeDiscount: e.schemeDiscount,
      );
      final bool isSavedMfStkAuditDetails =
          await MfStkAuditDetails.saveMfStkAuditDetails(mfStkAuditDetails);
      if (!isSavedMfStkAuditDetails) {
        // show snackbar
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Error: MfStkAuditDetails not saved'),
          ),
        );
        return;
      }
    });

    final LocationClass locationMF = LocationClass(
      empCode: user?.empCode ?? '',
      latt: position.latitude.toString(),
      longi: position.longitude.toString(),
      date: DateFormat('yyyy-MM-dd hh:mm:ss').format(DateTime.now()),
      transId: transId,
      purposeOfVisit: purposeOfVisit,
    );
    final bool isSavedLocationMF = await LocationClass.saveLocation(locationMF);
    if (!isSavedLocationMF) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Error: Location not saved'),
        ),
      );
      return;
    }

    // Market Feedback
    widget.marketFeedbackList.forEach((e) async {
      final MarketFeedback marketFeedback = MarketFeedback(
        marketFeedbackId: transId,
        competitorName: e.competitorName,
        ptd: e.ptd,
        ptr: e.ptr,
        ptc: e.ptc,
        customerCode: customerCode,
        billingExFor: e.billingExFor,
        wspExFor: e.wspExFor,
        routeCode: routeCode,
        productGroup: e.productGroup,
        pv: e.pv,
        rspExFor: e.rspExFor,
        nodExFor: e.nodExFor,
      );
      final bool isSavedMarketFeedback =
          await marketFeedback.saveMarketFeedback();
      if (!isSavedMarketFeedback) {
        // show snackbar
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Error: MarketFeedback not saved'),
          ),
        );
        return;
      }
    });

    // show snackbar
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Market Feedback saved successfully'),
      ),
    );

    await Hive.openBox('cart');
    final cart = await Hive.openBox('cart');
    await cart.put('flag', false);

    await uploadData();

    setState(() {
      isLoading = false;
    });

    Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) {
      return const CheckInMenus(isOptionSelected: true);
    }));
  }

  Future<int> uploadData() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return 0;
    }
    // upload data to server
    final user = await UserLoginClass.getLocalUser();

    // get mf transaction id from location
    List<String> mfStkAuditIds = [];
    await LocalDB.rawQuery(
            "SELECT * FROM location WHERE trans_id LIKE '%MF%' AND flag = '0'")
        .then((value) {
      for (int i = 0; i < value.length; i++) {
        mfStkAuditIds.add(value[i]['trans_id']);
      }
    });
    print('MF STK Audit Ids: $mfStkAuditIds');
    // get ms transaction id from location
    List<String> msStkAuditIds = [];
    await LocalDB.rawQuery(
            "SELECT * FROM location WHERE trans_id LIKE '%MS%' AND flag = '0'")
        .then((value) {
      for (int i = 0; i < value.length; i++) {
        msStkAuditIds.add(value[i]['trans_id']);
      }
    });
    print('MS STK Audit Ids: $msStkAuditIds');
    // XML for MS
    for (int i = 0; i < msStkAuditIds.length; i++) {
      final msStkAuditId = msStkAuditIds[i];
      final mfStkAuditHeader = await MfStkAuditHeader.getMfStkAuditHeaderById(
        msStkAuditId,
      );
      final List<MfStkAuditDetails> mfStkAuditDetails =
          await MfStkAuditDetails.getMarketFeedbackDetailsById(
                msStkAuditId,
              ) ??
              [];
      final location = await LocationClass.getLocationById(msStkAuditId);
      String xml =
          '<?xml version="1.0" encoding="UTF-8"?><root><MARKET_FEEDBACK_STKAUDIT>${location?.toXml()}<MF_STKAUDIT_DATA>${mfStkAuditHeader?.toXml().toXmlString()}';
      for (int j = 0; j < mfStkAuditDetails.length; j++) {
        xml += mfStkAuditDetails[j].toXml().toXmlString();
      }
      xml += "</MF_STKAUDIT_DATA></MARKET_FEEDBACK_STKAUDIT></root>";
      xml = xml.replaceAll('&lt;', '<');
      xml = xml.replaceAll('&gt;', '>');
      print('XML: $xml');
      String url =
          '${AppWebService.marketFeedbackStockAuditSA}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=${DateFormat('yyyy-MM-dd€HH:mm:ss').format(DateTime.now())}';
      print('URL: $url');
      final response = await http.post(
        Uri.parse(url),
        body: xml,
        encoding: Encoding.getByName('utf-8'),
        headers: {
          'Content-Type': 'application/xml',
        },
      );
      print("Response: ${response.statusCode}");
      print("Response: ${response.body}");
      if (response.statusCode == 200) {
        if (response.body == '2' || response.body == '1') {
          // set flag to 1 in location
          await LocationClass.setLocationFlag(msStkAuditId);
        } else {
          // show error message
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Error in uploading MS data'),
            ),
          );
        }
      }
    }
    // XML for MF
    for (int i = 0; i < mfStkAuditIds.length; i++) {
      final mfStkAuditId = mfStkAuditIds[i];
      final List<MarketFeedback> marketFeedback =
          await MarketFeedback.getMarketFeedbackById(
                mfStkAuditId,
              ) ??
              [];
      final location = await LocationClass.getLocationById(mfStkAuditId);
      String xml =
          '<?xml version="1.0" encoding="UTF-8"?><root><MARKET_FEEDBACK>${location?.toXml()}';
      for (int j = 0; j < marketFeedback.length; j++) {
        xml += marketFeedback[j].toXml().toXmlString();
      }
      xml += '</MARKET_FEEDBACK></root>';
      xml = xml.replaceAll('&lt;', '<');
      xml = xml.replaceAll('&gt;', '>');
      print('XML: $xml');
      String url =
          '${AppWebService.marketFeedbackURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=${DateFormat('yyyy-MM-dd€HH:mm:ss').format(DateTime.now())}';
      print('URL: $url');
      final response = await http.post(
        Uri.parse(url),
        body: xml,
        encoding: Encoding.getByName('utf-8'),
        headers: {
          'Content-Type': 'application/xml',
        },
      );
      print("Response: ${response.statusCode}");
      print("Response: ${response.body}");
      if (response.statusCode == 200) {
        if (response.body == '2' || response.body == '1') {
          // set flag to 1 in location
          await LocationClass.setLocationFlag(mfStkAuditId);
        } else {
          // show error message
          // ScaffoldMessenger.of(context).showSnackBar(
          //   const SnackBar(
          //     content: Text('Error in uploading MF data'),
          //   ),
          // );
        }
      }
    }
    return 1;
  }

  void _syncCart() {
    final Box<dynamic> box = Hive.box('cart');

    box.put('competitorTypeList', widget.competitorTypeList);
    box.put('marketFeedbackList', widget.marketFeedbackList);
    box.put('mfStkAuditHeaderList', widget.mfStkAuditHeaderList);
    box.put('mfStkAuditDetailsList', widget.mfStkAuditDetailsList);
  }

  Future<bool> takePicture() async {
    final resp = await showDialog(
        context: context,
        builder: (context) {
          return AlertDialog(
            title: const Text('Take Picture'),
            content: const Text('Do you want to take a picture?'),
            actions: <Widget>[
              TextButton(
                onPressed: () {
                  Navigator.pop(context, true);
                },
                child: const Text('Yes'),
              ),
              TextButton(
                onPressed: () {
                  Navigator.pop(context, false);
                },
                child: const Text('No'),
              ),
            ],
          );
        });
    return resp ?? false;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color.fromARGB(255, 236, 229, 225),
      appBar: AppBar(
        title: const Text('Market Feedback',
            style: TextStyle(color: Colors.white)),
        backgroundColor: Colors.red,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
      ),
      body: Column(
        children: <Widget>[
          Expanded(
            child: ListView.builder(
              itemCount: widget.marketFeedbackList.length,
              itemBuilder: (BuildContext context, int index) {
                // index. marketFeedbackList[index].competitorName
                // Quantity
                // Discount Scheme
                return InkWell(
                  onLongPress: () {
                    // show confirmation dialog
                    showDialog(
                      context: context,
                      builder: (context) {
                        return AlertDialog(
                          title: const Text('Delete'),
                          content: const Text(
                              'Are you sure you want to delete this item?'),
                          actions: <Widget>[
                            TextButton(
                              onPressed: () {
                                Navigator.pop(context);
                              },
                              child: const Text('No'),
                            ),
                            TextButton(
                              onPressed: () {
                                // Delete
                                setState(() {
                                  widget.marketFeedbackList.removeAt(index);
                                  widget.mfStkAuditDetailsList.removeAt(index);
                                  widget.mfStkAuditHeaderList.removeAt(index);
                                  widget.competitorTypeList.removeAt(index);
                                });
                                _syncCart();
                                Navigator.pop(context);
                              },
                              child: const Text('Yes'),
                            ),
                          ],
                        );
                      },
                    );
                  },
                  child: Container(
                    margin: const EdgeInsets.all(10),
                    padding: const EdgeInsets.all(10),
                    decoration: const BoxDecoration(
                      borderRadius: BorderRadius.all(Radius.circular(10)),
                      color: Colors.white,
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: <Widget>[
                        // Competitor Name
                        Text(
                          '${index + 1}. ${widget.marketFeedbackList[index].competitorName}',
                          style: const TextStyle(
                            color: Colors.black,
                            fontSize: 20,
                          ),
                        ),
                        // Vertical Divider
                        const VerticalDivider(
                          color: Colors.black,
                          thickness: 2,
                        ),
                        // Quantity
                        Text(
                          'Quantity: ${widget.mfStkAuditDetailsList[index].qtyMt}',
                          style: const TextStyle(
                            color: Colors.black,
                            fontSize: 20,
                          ),
                        ),

                        // Discount Scheme
                        Text(
                          "Discount Scheme: ${widget.mfStkAuditDetailsList[index].schemeDiscount == '' ? 'N/A' : widget.mfStkAuditDetailsList[index].schemeDiscount}",
                          style: const TextStyle(
                            color: Colors.black,
                            fontSize: 20,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
          // Row Icon Buttons - Remarks, Save, Add More
          isLoading
              ? const Padding(
                  padding: EdgeInsets.all(8.0),
                  child: CircularProgressIndicator(
                    color: Colors.red,
                  ),
                )
              : Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: <Widget>[
                    Container(
                      width: 100,
                      margin: const EdgeInsets.all(10),
                      padding: const EdgeInsets.all(10),
                      decoration: const BoxDecoration(
                        borderRadius: BorderRadius.all(Radius.circular(10)),
                        color: Colors.red,
                      ),
                      child: IconButton(
                        icon: const Icon(
                          Icons.add_comment,
                          color: Colors.white,
                          size: 30,
                        ),
                        onPressed: () {
                          // Add Remarks
                          getRemarks(context).then((value) {
                            setState(() {
                              remarks = value;
                            });
                          });
                        },
                      ),
                    ),
                    Container(
                      width: 100,
                      margin: const EdgeInsets.all(10),
                      padding: const EdgeInsets.all(10),
                      decoration: const BoxDecoration(
                        borderRadius: BorderRadius.all(Radius.circular(10)),
                        color: Colors.red,
                      ),
                      child: IconButton(
                        icon: const Icon(
                          Icons.check,
                          color: Colors.white,
                          size: 30,
                        ),
                        onPressed: () {
                          // Save
                          submit();
                        },
                      ),
                    ),
                    Container(
                      width: 100,
                      margin: const EdgeInsets.all(10),
                      padding: const EdgeInsets.all(10),
                      decoration: const BoxDecoration(
                        borderRadius: BorderRadius.all(Radius.circular(10)),
                        color: Colors.red,
                      ),
                      child: IconButton(
                        icon: const Icon(
                          Icons.add,
                          color: Colors.white,
                          size: 30,
                        ),
                        onPressed: () {
                          // Add More
                          Navigator.pop(context);
                        },
                      ),
                    ),
                  ],
                ),
        ],
      ),
    );
  }
}
