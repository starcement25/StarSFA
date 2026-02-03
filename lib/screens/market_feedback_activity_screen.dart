import 'package:flutter/material.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/mf_stk_audit_details.dart';
import 'package:starsfa/models/mf_stk_audit_header.dart';

class MarketFeedbackActivityScreen extends StatefulWidget {
  const MarketFeedbackActivityScreen({super.key});

  @override
  State<MarketFeedbackActivityScreen> createState() =>
      _MarketFeedbackActivityScreenState();
}

class _MarketFeedbackActivityScreenState
    extends State<MarketFeedbackActivityScreen> {
  Future<List<Map<String, dynamic>>> getData() async {
    final data = await LocalDB.rawQuery('''
      SELECT 
          mf.*, mfd.*, cm.customer_name
      FROM 
          mf_stk_audit_header AS mf
      LEFT JOIN 
          mf_stk_audit_details AS mfd 
      ON 
          mf.mf_stk_audit_id = mfd.mf_stk_audit_id
      LEFT JOIN
          customer_master AS cm
      ON
          mf.customer_code = cm.customer_code
''');
    return data;
  }

  void _popUpDetails(BuildContext context, List<MfStkAuditDetails> details) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return SimpleDialog(
          clipBehavior: Clip.antiAlias,
          // border radius
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(10),
          ),
          contentPadding: const EdgeInsets.all(0),
          children: [
            // title
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(5),
              decoration: const BoxDecoration(
                color: Colors.red,
              ),
              child: Center(
                child: Row(
                  children: [
                    // back button
                    IconButton(
                      icon: const Icon(Icons.arrow_back, color: Colors.white),
                      onPressed: () {
                        Navigator.pop(context);
                      },
                    ),
                    const Text('Feedback Details',
                        style: TextStyle(
                          color: Colors.white,
                          fontStyle: FontStyle.italic,
                          fontSize: 20,
                        )),
                  ],
                ),
              ),
            ),
            // header
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(5),
              decoration: BoxDecoration(
                color: Colors.grey[300],
              ),
              child: const Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Flexible(
                      flex: 7,
                      child: Text('Product Details',
                          style: TextStyle(
                            fontStyle: FontStyle.italic,
                            fontSize: 20,
                          ))),
                  Flexible(
                      flex: 3,
                      child: Text('Quantity',
                          style: TextStyle(
                            fontStyle: FontStyle.italic,
                            fontSize: 20,
                          ))),
                ],
              ),
            ),
            // body
            for (var item in details)
              Container(
                width: double.infinity,
                decoration: const BoxDecoration(
                  color: Colors.white,
                  // border at the bottom of the container
                  border: Border(
                    bottom: BorderSide(width: 1.0, color: Colors.black),
                  ),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Flexible(
                        flex: 7,
                        child: Text(
                          item.competitorName,
                          style: const TextStyle(fontSize: 18),
                        ),
                      ),
                      Flexible(
                        flex: 3,
                        child: Text(
                          item.qtyMt.toString(),
                          style: const TextStyle(fontSize: 18),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            // footer with total quantity
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(5),
              decoration: BoxDecoration(
                color: Colors.grey[300],
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text('Total Quantity',
                      style: TextStyle(
                        fontStyle: FontStyle.italic,
                        fontSize: 20,
                      )),
                  Text(
                      details
                          .map((e) => e.qtyMt)
                          .reduce((value, element) =>
                              (double.parse(value) + double.parse(element))
                                  .toString())
                          .toString(),
                      style: const TextStyle(
                        fontStyle: FontStyle.italic,
                        fontSize: 20,
                      )),
                ],
              ),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
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
      body: FutureBuilder<List<Map<String, dynamic>>>(
        future: getData(),
        builder: (BuildContext context, AsyncSnapshot snapshot) {
          if (snapshot.connectionState == ConnectionState.done) {
            if (snapshot.hasError) {
              return Center(
                child: Text('Error: ${snapshot.error}'),
              );
            } else {
              final List<MfStkAuditHeader> mfStkAuditHeader = [];
              for (int i = 0; i < snapshot.data.length; i++) {
                // check for unique mf_stk_audit_id
                if (mfStkAuditHeader.indexWhere((element) =>
                        element.mfStkAuditId ==
                        snapshot.data[i]['mf_stk_audit_id']) ==
                    -1) {
                  mfStkAuditHeader
                      .add(MfStkAuditHeader.fromJson(snapshot.data[i]));
                }
              }
              final List<MfStkAuditDetails> mfStkAuditDetails = [];
              for (int i = 0; i < snapshot.data.length; i++) {
                mfStkAuditDetails
                    .add(MfStkAuditDetails.fromJson(snapshot.data[i]));
              }
              return Column(
                children: [
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(5),
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                    ),
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Flexible(
                            flex: 7,
                            child: Text('Customer Name',
                                style: TextStyle(
                                  fontStyle: FontStyle.italic,
                                  fontSize: 20,
                                ))),
                        Flexible(
                            flex: 3,
                            child: Text('Quantity',
                                style: TextStyle(
                                  fontStyle: FontStyle.italic,
                                  fontSize: 20,
                                ))),
                      ],
                    ),
                  ),
                  Expanded(
                    child: ListView.builder(
                        itemCount: mfStkAuditHeader.length,
                        itemBuilder: (BuildContext context, int index) {
                          final totalQty = mfStkAuditDetails
                              .where((element) =>
                                  element.mfStkAuditId ==
                                  mfStkAuditHeader[index].mfStkAuditId)
                              .map((e) => e.qtyMt)
                              .reduce((value, element) =>
                                  (double.parse(value) + double.parse(element))
                                      .toString());
                          return InkWell(
                            onTap: () => _popUpDetails(
                                context,
                                mfStkAuditDetails
                                    .where((element) =>
                                        element.mfStkAuditId ==
                                        mfStkAuditHeader[index].mfStkAuditId)
                                    .toList()),
                            child: Container(
                              width: double.infinity,
                              decoration: const BoxDecoration(
                                color: Colors.white,
                                // border at the bottom of the container
                                border: Border(
                                  bottom: BorderSide(
                                      width: 1.0, color: Colors.black),
                                ),
                              ),
                              child: Padding(
                                padding: const EdgeInsets.all(8.0),
                                child: Row(
                                  mainAxisAlignment:
                                      MainAxisAlignment.spaceBetween,
                                  children: [
                                    Flexible(
                                      flex: 7,
                                      child: Text(
                                        mfStkAuditHeader[index].customerName ??
                                            '',
                                        style: const TextStyle(fontSize: 18),
                                      ),
                                    ),
                                    Flexible(
                                      flex: 3,
                                      child: Text(
                                        totalQty.toString(),
                                        style: const TextStyle(fontSize: 18),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          );
                        }),
                  ),
                ],
              );
            }
          } else {
            return const Center(
              child: CircularProgressIndicator(),
            );
          }
        },
      ),
    );
  }
}

class MarketFeedbackActivity {
  final String? customerName;
  final String? customerCode;
  final String? productDetails;
  final String? qty;

  MarketFeedbackActivity(
      {required this.customerName,
      required this.customerCode,
      required this.productDetails,
      required this.qty});

  factory MarketFeedbackActivity.fromJson(Map<String, dynamic> json) {
    return MarketFeedbackActivity(
      customerName: json['customer_name'],
      customerCode: json['customer_code'],
      productDetails: json['competitor_name'],
      qty: json['PTC'],
    );
  }
}
