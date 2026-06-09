// ignore_for_file: use_build_context_synchronously

import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/db_setup/dataset/SBGFeedbackModel.dart';
import 'package:starsfa/db_setup/sbg_database.dart' show SBGDatabase;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/route_master_class.dart';

import '../../db_setup/dataset/CustomerCompetitorQuantityModel.dart';

class SBGFeedbackScreen extends StatefulWidget {
  final String sessionID;
  const SBGFeedbackScreen({super.key, required this.sessionID});

  @override
  State<SBGFeedbackScreen> createState() => _SBGFeedbackScreenState();
}

class _SBGFeedbackScreenState extends State<SBGFeedbackScreen> {
  bool isLoading = false;
  List<CustomerCompetitorQuantityModel> dataList = [];
  int totalCount = 0;
  String dns = '';
  String selectedUniverseType = '';
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
      final Box box = Hive.box('checkIn');
      box.put('sbgMenuOpened', true);
    });
  }

  Future<void> _loadData() async {
    final Box box = Hive.box('checkIn');
    final String customerCode = box.get('customerCode', defaultValue: '');
    if (customerCode.isEmpty) return;

    final list = await SBGDatabase.instance
        .getAllCustomerCompetitorQuantitySorted(customerCode);
    String dnsCode = list[0].customerDNS ?? '';
    print("dnsCode: ${list[0]}");
    setState(() {
      dataList = list;
      dns = dnsCode;
      _recalculateTotal();
    });
  }

  void _recalculateTotal() {
    int total = 0;
    for (final item in dataList) {
      total += int.tryParse(item.quantity ?? '0') ?? 0;
    }
    totalCount = total;
  }

  Future<void> submit(BuildContext context) async {
    try {
      // Save all edited quantities to sbg_feedback table
      for (final item in dataList) {
        await SBGDatabase.instance.insertOrUpdateSbgFeedback(item);
      }

      // Fetch all saved feedback for API
      await SBGDatabase.instance.getAllSbgFeedback();

      await callSubmitApi();
      final Box box = Hive.box('checkIn');
      await box.put('sbgSubmitted', true);
      // final result = await YourApiService.submitSbgFeedback(feedbackList);

      // On success
      // await SBGDatabase.instance.deleteAllSbgFeedback();

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Submitted successfully')),
      );
      Navigator.of(context).pop();
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed: ${e.toString()}')),
      );
    }
  }

  Future<void> callSubmitApi() async {
    try {
      print('callSubmitApi');
      final Box box = Hive.box('checkIn');
      final String empCode = box.get('empCode', defaultValue: '');

      final List<SBGFeedbackModel> feedbackList =
          await SBGDatabase.instance.getAllSbgFeedbackWhereFlag1();
      print('feedbackList: ${feedbackList.length}');
      if (feedbackList.isEmpty) return;

      // Build XML
      final StringBuffer xmlBuffer = StringBuffer();
      xmlBuffer.write("<?xml version='1.0' encoding='UTF-8'?><root>");
      xmlBuffer.write("<marketfeedback>");
      xmlBuffer.write(
          '<universetype><![CDATA[$selectedUniverseType]]></universetype>');
      xmlBuffer.write("<details>");
      String marketFeedbackId = '';
      if (feedbackList.isNotEmpty) {
        final String dateTime = feedbackList[0].dateTime ?? '';
        final String cleanDateTime = dateTime
            .replaceAll('-', '')
            .replaceAll(' ', '')
            .replaceAll(':', '');
        marketFeedbackId = 'SBG$empCode$cleanDateTime';
      } else {
        final DateTime now = DateTime.now();
        final String cleanDateTime = '${now.year}'
            '${now.month.toString().padLeft(2, '0')}'
            '${now.day.toString().padLeft(2, '0')}'
            '${now.hour.toString().padLeft(2, '0')}'
            '${now.minute.toString().padLeft(2, '0')}'
            '${now.second.toString().padLeft(2, '0')}';
        marketFeedbackId = 'SBG$empCode$cleanDateTime';
      }

      for (final item in feedbackList) {
        final String dateTime = feedbackList[0].dateTime ?? '';
        xmlBuffer.write('<item>');
        xmlBuffer.write(
            '<market_feedback_id><![CDATA[$marketFeedbackId]]></market_feedback_id>');
        xmlBuffer.write(
            '<customer_code><![CDATA[${item.customerCode ?? ''}]]></customer_code>');
        xmlBuffer.write('<emp_code><![CDATA[$empCode]]></emp_code>');
        xmlBuffer.write(
            '<competitor_quantity_id><![CDATA[${item.competitorCode ?? ''}]]></competitor_quantity_id>');
        xmlBuffer.write('<qty><![CDATA[${item.quantity ?? '0'}]]></qty>');
        xmlBuffer.write('<date_time><![CDATA[$dateTime]]></date_time>');
        xmlBuffer.write('</item>');
      }

      xmlBuffer.write('</details></marketfeedback></root>');

      final String xmlData = xmlBuffer.toString();
      print('SBG XML: $xmlData');

      // POST request
      final Uri uri = Uri.parse(
          '${AppWebService.baseURL}misreport/save_competitor_qty.php');

      final http.Response response = await http.post(
        uri,
        headers: {'Content-Type': 'application/xml'},
        body: xmlData,
      );

      print('SBG Response: ${response.body}');

      final Map<String, dynamic> jsonResponse = jsonDecode(response.body);
      print(jsonResponse);
      if (jsonResponse['status'] == true) {
        // Update local DB quantities & clear feedback table
        for (final item in feedbackList) {
          await SBGDatabase.instance.updateCustomerCompetitorQuantity(
            item.customerCode ?? '',
            item.competitorCode ?? '',
            item.quantity ?? '0',
          );
        }
        await SBGDatabase.instance.deleteAllSbgFeedback();
      } else {
        throw Exception('Server returned status false');
      }
    } catch (e) {
      rethrow;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.of(context).pop(),
        ),
        backgroundColor: Colors.red,
        title: const Text(
          'SBG Feedback',
          style: TextStyle(color: Colors.white),
        ),
      ),
      body: Column(
        children: <Widget>[
          // Customer details at top
          CustomerDetailsMarketFeedback(
            dns: dns,
            onRouteChanged: (universeType) {
              setState(() {
                selectedUniverseType = universeType;
              });
            },
          ),

          // List in the middle
          Expanded(
              child: dataList.isEmpty
                  ? const Center(
                      child: CircularProgressIndicator(color: Colors.red))
                  : Column(
                      mainAxisAlignment: MainAxisAlignment.start,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                          Row(
                            children: [
                              Expanded(
                                child: Text(
                                  'Competitor Name',
                                  style: TextStyle(
                                    color: Colors.black,
                                    fontSize: 16,
                                  ),
                                ),
                              ),
                              Text(
                                'Potential (MT)',
                                style: TextStyle(
                                  color: Colors.black,
                                  fontSize: 16,
                                ),
                              ),
                            ],
                          ),
                          Expanded(
                              child: ListView.builder(
                            itemCount: dataList.length,
                            itemBuilder: (context, index) {
                              final item = dataList[index];
                              return Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12, vertical: 8),
                                decoration: BoxDecoration(
                                  border: Border(
                                    bottom:
                                        BorderSide(color: Colors.grey[300]!),
                                  ),
                                ),
                                child: Row(
                                  children: [
                                    Expanded(
                                      child: Text(
                                        item.competitorName ?? '',
                                        style: TextStyle(
                                          color: item.mandatory == 'Y'
                                              ? Colors.red
                                              : Colors.black,
                                          fontSize: 14,
                                        ),
                                      ),
                                    ),
                                    SizedBox(
                                      width: 100,
                                      child: TextFormField(
                                        key: ValueKey(
                                            '${item.competitorQuantityId}_$index'),
                                        initialValue: item.quantity ?? '0',
                                        keyboardType: TextInputType.number,
                                        textAlign: TextAlign.right,
                                        decoration: const InputDecoration(
                                          border: OutlineInputBorder(),
                                          contentPadding: EdgeInsets.symmetric(
                                              horizontal: 8, vertical: 4),
                                        ),
                                        onChanged: (value) {
                                          item.quantity =
                                              value.isEmpty ? '0' : value;
                                          setState(() {
                                            _recalculateTotal();
                                          });
                                        },
                                      ),
                                    ),
                                    Text(
                                      '  MT',
                                      style: TextStyle(
                                        color: item.mandatory == 'Y'
                                            ? Colors.red
                                            : Colors.black,
                                        fontSize: 14,
                                      ),
                                    ),
                                  ],
                                ),
                              );
                            },
                          )),
                        ])),

          // Total count row
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            color: Colors.grey[200],
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Total Counter Potential',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                ),
                Text(
                  '$totalCount MT',
                  style: const TextStyle(
                      fontWeight: FontWeight.bold, fontSize: 16),
                ),
              ],
            ),
          ),

          // Submit button always at bottom
          InkWell(
            onTap: isLoading
                ? null
                : () async {
                    setState(() => isLoading = true);
                    await submit(context);
                    setState(() => isLoading = false);
                  },
            child: Container(
              width: double.infinity,
              margin: const EdgeInsets.all(10),
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.red,
                borderRadius: BorderRadius.circular(5),
              ),
              child: Center(
                child: isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text(
                        'Submit Feedback',
                        style: TextStyle(color: Colors.white, fontSize: 16),
                      ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class CustomerDetailsMarketFeedback extends StatefulWidget {
  final String dns;
  final Function(String routeCode)? onRouteChanged; // callback to parent

  const CustomerDetailsMarketFeedback({
    super.key,
    required this.dns,
    this.onRouteChanged,
  });

  @override
  State<CustomerDetailsMarketFeedback> createState() =>
      _CustomerDetailsMarketFeedbackState();
}

class _CustomerDetailsMarketFeedbackState
    extends State<CustomerDetailsMarketFeedback> {
  String routeName = '';
  String selectedUniverseType = "";
  List<String> allUniverseTypes = [
    "Star - Dealer",
    "Non Star - Dealer",
    "Star -Sub Dealer",
    "Star - RSAR",
    "Non Star - Non link Sub Dealer",
    "Non Star - Star Link Sub Dealer"
  ];
  void getRouteName() async {
    final Box box = Hive.box('checkIn');
    final String routeCode = box.get('routeCode', defaultValue: '');
    if (routeCode.isEmpty) {
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
          builder:
              (BuildContext context, AsyncSnapshot<CustomerMasterDB> snapshot) {
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
                      Text(
                        'Customer Code: ${widget.dns}',
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.black,
                        ),
                      ),
                      Text(
                        'Customer Name: ${customer.customerName}',
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.black,
                        ),
                      ),
                      Text(
                        'Customer Type: ${customer.customerType}',
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.black,
                        ),
                      ),
                      Text(
                        'Route Name: $routeName',
                        style: TextStyle(
                          fontSize: 14,
                          color: Colors.black,
                        ),
                      ),
                      const SizedBox(height: 10),
                      DropdownButtonFormField<String>(
                        value: selectedUniverseType.isEmpty
                            ? null
                            : selectedUniverseType,
                        isExpanded: true,
                        decoration: InputDecoration(
                          labelText: 'Universe Type',
                          labelStyle: const TextStyle(fontSize: 13),
                          contentPadding: const EdgeInsets.symmetric(
                              horizontal: 10, vertical: 6),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(6),
                          ),
                          filled: true,
                          fillColor: Colors.white,
                        ),
                        items: allUniverseTypes.map((String universeType) {
                          return DropdownMenuItem<String>(
                            value: universeType,
                            child: Text(
                              universeType,
                              style: const TextStyle(fontSize: 13),
                              overflow: TextOverflow.ellipsis,
                            ),
                          );
                        }).toList(),
                        onChanged: (String? newRouteCode) {
                          if (newRouteCode == null) return;
                          setState(() {
                            selectedUniverseType = newRouteCode;
                          });
                          // Also notify parent if needed
                          widget.onRouteChanged?.call(newRouteCode);
                        },
                      ),
                    ],
                  ),
                );
              } else {
                return const Text('No Customer Details');
              }
            } else {
              return const Center(
                child: CircularProgressIndicator(color: Colors.black),
              );
            }
          },
        );
      },
    );
  }
}
