import 'package:flutter/material.dart';
import 'package:starsfa/models/local_db.dart';

class CustomerWiseOutstandingReportDetailsScreen extends StatefulWidget {
  final String title;
  final String selectedICode;
  final String selectedCustomerName;
  final Color colorCode;
  const CustomerWiseOutstandingReportDetailsScreen(
      {super.key,
      required this.title,
      required this.selectedICode,
      required this.selectedCustomerName,
      required this.colorCode});

  @override
  State<CustomerWiseOutstandingReportDetailsScreen> createState() =>
      _CustomerWiseOutstandingReportDetailsScreenState();
}

class _CustomerWiseOutstandingReportDetailsScreenState
    extends State<CustomerWiseOutstandingReportDetailsScreen> {
  List<Map<String, dynamic>> itemDataSet = [];
  List<Map<String, dynamic>> filteredItemDataSet = [];
  List<Map<String, dynamic>> filterDataSet = [];
  Map<String, dynamic> customerInfo = {};
  Map<String, dynamic> customerInvoiceInfo = {};

  @override
  void initState() {
    super.initState();
    _showCustomerWiseOustanding();
  }

  void _showCustomerWiseOustanding() async {
    customerInfo = await LocalDB.getCustomerAgeing(widget.selectedICode);
    itemDataSet.clear();
    List<Map<String, dynamic>> dataSet =
        await LocalDB.getCustomerAgeingInvoiceNo(widget.selectedICode);
    List<Map<String, dynamic>> a = [
      {
        'id': 1,
        'start_date': 0,
        'end_date': 999,
        'title': 'All',
        'is_select': false
      },
      {
        'id': 2,
        'start_date': 0,
        'end_date': int.parse(customerInfo['title_1'].replaceAll('Day', '')),
        'title':
            '0 To ${int.parse(customerInfo['title_1'].replaceAll('Day', ''))} Days',
        'is_select': widget.title == customerInfo['title_1']
      },
      {
        'id': 3,
        'start_date':
            int.parse(customerInfo['title_1'].replaceAll('Day', '')) + 1,
        'end_date': int.parse(customerInfo['title_2'].replaceAll('Day', '')),
        'title':
            '${int.parse(customerInfo['title_1'].replaceAll('Day', '')) + 1} To ${int.parse(customerInfo['title_2'].replaceAll('Day', ''))} Days',
        'is_select': widget.title == customerInfo['title_2']
      },
      {
        'id': 4,
        'start_date':
            int.parse(customerInfo['title_2'].replaceAll('Day', '')) + 1,
        'end_date': int.parse(customerInfo['title_3'].replaceAll('Day', '')),
        'title':
            '${int.parse(customerInfo['title_2'].replaceAll('Day', '')) + 1} To ${int.parse(customerInfo['title_3'].replaceAll('Day', ''))} Days',
        'is_select': widget.title == customerInfo['title_3']
      },
      {
        'id': 5,
        'start_date':
            int.parse(customerInfo['title_3'].replaceAll('Day', '')) + 1,
        'end_date': int.parse(customerInfo['title_4'].replaceAll('Day', '')),
        'title':
            '${int.parse(customerInfo['title_3'].replaceAll('Day', '')) + 1} To ${int.parse(customerInfo['title_4'].replaceAll('Day', ''))} Days',
        'is_select': widget.title == customerInfo['title_4']
      },
      {
        'id': 6,
        'start_date':
            int.parse(customerInfo['title_4'].replaceAll('Day', '')) + 1,
        'end_date': int.parse(customerInfo['title_5'].replaceAll('Day', '')),
        'title':
            '${int.parse(customerInfo['title_4'].replaceAll('Day', '')) + 1} To ${int.parse(customerInfo['title_5'].replaceAll('Day', ''))} Days',
        'is_select': widget.title == customerInfo['title_5']
      },
      {
        'id': 7,
        'start_date':
            int.parse(customerInfo['title_5'].replaceAll('Day', '')) + 1,
        'end_date': int.parse(customerInfo['title_6'].replaceAll('Day', '')),
        'title':
            '${int.parse(customerInfo['title_5'].replaceAll('Day', '')) + 1} To ${int.parse(customerInfo['title_6'].replaceAll('Day', ''))} Days',
        'is_select': widget.title == customerInfo['title_6']
      },
      {
        'id': 8,
        'start_date':
            int.parse(customerInfo['title_6'].replaceAll('Day', '')) + 1,
        'end_date': int.parse(customerInfo['title_7'].replaceAll('Day', '')),
        'title':
            '${int.parse(customerInfo['title_6'].replaceAll('Day', '')) + 1} To ${int.parse(customerInfo['title_7'].replaceAll('Day', ''))} Days',
        'is_select': widget.title == customerInfo['title_7']
      },
      {
        'id': 9,
        'start_date':
            int.parse(customerInfo['title_7'].replaceAll('Day', '')) + 1,
        'end_date': int.parse(customerInfo['title_8'].replaceAll('Day', '')),
        'title':
            '${int.parse(customerInfo['title_7'].replaceAll('Day', '')) + 1} To ${int.parse(customerInfo['title_8'].replaceAll('Day', ''))} Days',
        'is_select': widget.title == customerInfo['title_8']
      },
      {
        'id': 10,
        'start_date':
            int.parse(customerInfo['title_8'].replaceAll('Day', '')) + 1,
        'end_date': 999,
        'title':
            '${int.parse(customerInfo['title_8'].replaceAll('Day', '')) + 1}+ Days',
        'is_select': widget.title == customerInfo['title_8']
      },
    ];

    for (int i = 0; i < dataSet.length; i++) {
      itemDataSet.add({
        'id': dataSet[i]['invoice_no'],
        'invoice_no': dataSet[i]['invoice_no'],
        'invoice_dt': dataSet[i]['invoice_date'],
        'due_days': dataSet[i]['invoice_age'],
        'invoice_amount': dataSet[i]['invoice_value'],
        'inv_bal_amount': dataSet[i]['invoice_value'],
        'aging': dataSet[i]['invoice_age'],
      });
    }

    setState(() {
      filterDataSet = a;
      filteredItemDataSet
        ..clear()
        ..addAll(itemDataSet.where((item) {
          final aging = int.tryParse(item['aging'].toString()) ?? 0;

          for (int i = 0; i < filterDataSet.length; i++) {
            if (filterDataSet[i]['is_select']) {
              return aging >= filterDataSet[i]['start_date'] &&
                  aging <= filterDataSet[i]['end_date'];
            }
          }
          return false;
        }));
    });
  }

  Color _getAgingColor(int aging) {
    if (aging <= 3) return const Color(0xFF2ECC71); // green       → 0 days
    if (aging <= 7) return const Color(0xFF73D467); // light green → 1–3 days
    if (aging <= 17) return const Color(0xFFB8DF46); // yellow-green→ 4–7 days
    if (aging <= 25) return const Color(0xFFF4C430); // yellow      → 8–17 days
    if (aging <= 30) return const Color(0xFFF4A020); // orange      → 18–25 days
    if (aging <= 45) return const Color(0xFFF47C20); // dark orange → 26–30 days
    if (aging <= 60) return const Color(0xFFF05050); // light red   → 31–45 days
    if (aging <= 90) return const Color(0xFFD63030); // red         → 46–60 days
    return const Color(0xFF8B0000); // dark red    → 61+ days
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.red,
        leading: IconButton(
          icon: Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: Colors.white, width: 1.5),
            ),
            child: const Icon(
              Icons.arrow_back,
              color: Colors.white,
              size: 18,
            ),
          ),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          'Customer Wise Outstanding',
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            fontSize: 18,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(10, 10, 10, 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // ── Customer info card
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: List.generate(filterDataSet.length, (index) {
                  final item = filterDataSet[index];
                  final bool isSelected = item['is_select'] as bool;
                  return GestureDetector(
                    onTap: () {
                      setState(() {
                        for (var f in filterDataSet) {
                          f['is_select'] = false;
                        }
                        filterDataSet[index]['is_select'] = true;

                        final selected = filterDataSet[index];
                        if (selected['id'] == 1) {
                          // "All" selected
                          filteredItemDataSet
                            ..clear()
                            ..addAll(itemDataSet);
                        } else {
                          filteredItemDataSet
                            ..clear()
                            ..addAll(itemDataSet.where((item) {
                              final aging =
                                  int.tryParse(item['aging'].toString()) ?? 0;
                              return aging >= selected['start_date'] &&
                                  aging <= selected['end_date'];
                            }));
                        }
                      });
                    },
                    child: Container(
                      margin: const EdgeInsets.only(right: 8),
                      padding: const EdgeInsets.symmetric(
                        horizontal: 14,
                        vertical: 8,
                      ),
                      decoration: BoxDecoration(
                        color: isSelected ? Colors.red : Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(
                          color: isSelected ? Colors.red : Colors.grey.shade300,
                          width: 1.5,
                        ),
                        boxShadow: isSelected
                            ? [
                                BoxShadow(
                                  color: Colors.red.withOpacity(0.3),
                                  blurRadius: 6,
                                  offset: const Offset(0, 2),
                                )
                              ]
                            : [],
                      ),
                      child: Text(
                        item['title'],
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.bold,
                          color: isSelected ? Colors.white : Colors.black54,
                        ),
                      ),
                    ),
                  );
                }),
              ),
            ),
            const SizedBox(height: 10),

            // ── Invoice cards
            ...List.generate(filteredItemDataSet.length, (index) {
              final item = filteredItemDataSet[index];
              final int aging = int.tryParse(item['aging'].toString()) ?? 0;
              final colorCode = _getAgingColor(aging);
              return Column(
                children: [
                  Container(
                    width: double.infinity,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      boxShadow: const [
                        BoxShadow(
                          color: Colors.black12,
                          blurRadius: 4,
                          offset: Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        Padding(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 16,
                            vertical: 14,
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Container(
                                    width: 44,
                                    height: 44,
                                    decoration: BoxDecoration(
                                      color: colorCode.withOpacity(0.1),
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: Icon(
                                      Icons.document_scanner_outlined,
                                      color: colorCode,
                                      size: 24,
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          item['invoice_no'],
                                          style: const TextStyle(
                                            fontSize: 18,
                                            fontWeight: FontWeight.bold,
                                            color: Colors.black,
                                          ),
                                        ),
                                        const SizedBox(height: 2),
                                        Text(
                                          item['invoice_dt'],
                                          style: const TextStyle(
                                            fontSize: 13,
                                            color: Colors.black87,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 10,
                                      vertical: 6,
                                    ),
                                    decoration: BoxDecoration(
                                      color: colorCode.withOpacity(0.1),
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: Text(
                                      "Age : ${item['aging']} Days",
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: colorCode,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 10),
                              Container(
                                width: double.infinity,
                                height: 1,
                                color: Colors.grey.shade100,
                              ),
                              const SizedBox(height: 10),
                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    "Total Invoice Amount ",
                                    style: const TextStyle(
                                      fontSize: 14,
                                      color: Colors.black87,
                                    ),
                                  ),
                                  Text(
                                    "₹ ${item['invoice_amount']}",
                                    style: const TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.black,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        const Divider(
                          height: 1,
                          color: Color(0xFFFEF2F2),
                          thickness: 1,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 10),
                ],
              );
            }),
          ],
        ),
      ),
    );
  }
}
