import 'dart:math';

import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/user_login_class.dart';
// import 'package:starsfa/models/self_appraisal_customer_wise_class.dart';
import 'package:starsfa/screens/customer_outstanding/customer_wise_outstanding_report_details_screen.dart';

class CustomerWiseOutstandingScreen extends StatefulWidget {
  const CustomerWiseOutstandingScreen({super.key});

  @override
  State<CustomerWiseOutstandingScreen> createState() =>
      _CustomerWiseOutstandingScreenState();
}

class _CustomerWiseOutstandingScreenState
    extends State<CustomerWiseOutstandingScreen> {
  late Future<List<Map<String, dynamic>>> futureCust;
  static List<Map<String, dynamic>> customerData = [];
  static List<Map<String, dynamic>> itemDataSet = [];
  List<Map<String, dynamic>> valueInfo = [];
  List<Map<String, dynamic>> labelInfo = [];
  Map<String, dynamic> customerInfo = {};
  bool _isLoading = false;

  String? selectedICode = '';
  String? selectedCustomerName = 'Select Customer';

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    super.dispose();
  }

  Future<void> _loadData() async {
    futureCust = LocalDB.getCustomerAgeingList();
    futureCust.then((data) {
      final seen = <String>{};
      final List<Map<String, dynamic>> tempList = [];

      for (var row in data) {
        final code = (row['customer_code'] ?? '').toString().trim();
        final name = (row['customer_name'] ?? '').toString().trim();
        if (code.isNotEmpty && !seen.contains(code)) {
          seen.add(code);
          tempList.add({'customer_code': code, 'customer_name': name});
        }
      }
      if (!mounted) return;
      setState(() {
        customerData = tempList;
      });
    });
  }

  Future<bool> _fetchAndInsertCustomerAgeing() async {
    try {
      final user = await UserLoginClass.getLocalUser();
      final String url =
          '${AppWebService.baseURL}misreport/sfa_customer_ageing_api.php?emp_code=${user?.empCode}';
      print(url);
      final response = await http.get(Uri.parse(url));

      if (response.statusCode == 200) {
        int noColumn = -1;
        final lines = response.body.split('\n');

        for (String line in lines) {
          if (line.trim().isEmpty) continue;

          if (line.contains('¥')) {
            // header line — extract column count
            final parts = line.split('¥');
            noColumn = int.tryParse(parts[1].trim()) ?? -1;
            print('Column count: $noColumn');
          } else if (line.contains('#')) {
            // skip this line
            continue;
          } else {
            // data line
            final rowData = ('$line ').split('^');

            await LocalDB.insertCustomerAgeing({
              'customer_name': rowData[1].trim(),
              'customer_code': rowData[0].trim(),
              'title_1': rowData[4].trim(),
              'value_1': rowData[5].trim(),
              'invoice_count_1': '',
              'title_2': rowData[6].trim(),
              'value_2': rowData[7].trim(),
              'invoice_count_2': '',
              'title_3': rowData[8].trim(),
              'value_3': rowData[9].trim(),
              'invoice_count_3': '',
              'title_4': rowData[10].trim(),
              'value_4': rowData[11].trim(),
              'invoice_count_4': '',
              'title_5': rowData[12].trim(),
              'value_5': rowData[13].trim(),
              'invoice_count_5': '',
              'title_6': rowData[14].trim(),
              'value_6': rowData[15].trim(),
              'invoice_count_6': '',
              'title_7': rowData[16].trim(),
              'value_7': rowData[17].trim(),
              'invoice_count_7': '',
              'title_8': rowData[18].trim(),
              'value_8': rowData[19].trim(),
              'invoice_count_8': '',
              'title_9': rowData[20].trim(),
              'value_9': rowData[21].trim(),
              'invoice_count_9': '',
              'total_amount': '1800200',
              'total_invoice_count': '55',
            });
          }
        }

        print('Customer Quantity data inserted successfully');
        return true;
      } else {
        print('API Error: ${response.statusCode}');
        return false;
      }
    } catch (e) {
      print('Failed to fetch customer quantity: $e');
      return false;
    }
  }

  void showSelectorDialog({
    required Future<List<Map<String, dynamic>>> Function() fetchData,
    required String dialogTitle,
    required String Function(Map<String, dynamic>) getDisplayText,
    required void Function(Map<String, dynamic> selectedItem) onSelected,
    required bool enableSearch,
  }) {
    TextEditingController searchController = TextEditingController();
    List<Map<String, dynamic>> allItems = [];
    List<Map<String, dynamic>> filteredItems = [];
    bool isLoading = true;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setDialogState) {
            if (isLoading) {
              fetchData().then((items) {
                setDialogState(() {
                  allItems = items;
                  filteredItems = items;
                  isLoading = false;
                });
              }).catchError((error) {
                setDialogState(() => isLoading = false);
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text('Failed to load data: $error')),
                );
              });
            }

            return Dialog(
              insetPadding: EdgeInsets.zero,
              backgroundColor: Colors.white,
              child: SafeArea(
                child: Scaffold(
                  appBar: AppBar(
                    backgroundColor: Colors.red,
                    title: Text(dialogTitle,
                        style: const TextStyle(color: Colors.white)),
                    leading: IconButton(
                      icon: const Icon(Icons.close, color: Colors.white),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ),
                  body: Column(
                    children: [
                      if (enableSearch)
                        Padding(
                          padding: const EdgeInsets.all(16),
                          child: TextField(
                            controller: searchController,
                            decoration: const InputDecoration(
                              hintText: 'Search...',
                              prefixIcon: Icon(Icons.search),
                              border: OutlineInputBorder(),
                            ),
                            onChanged: (value) {
                              setDialogState(() {
                                filteredItems = allItems
                                    .where((item) => getDisplayText(item)
                                        .toLowerCase()
                                        .contains(value.toLowerCase()))
                                    .toList();
                              });
                            },
                          ),
                        ),
                      Expanded(
                        child: isLoading
                            ? const Center(child: CircularProgressIndicator())
                            : filteredItems.isEmpty
                                ? const Center(child: Text('No data found'))
                                : ListView.builder(
                                    itemCount: filteredItems.length,
                                    itemBuilder: (context, index) {
                                      final item = filteredItems[index];
                                      return ListTile(
                                        title: Text(getDisplayText(item)),
                                        onTap: () {
                                          onSelected(item);
                                          Navigator.pop(context);
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
      },
    );
  }

  void getCustomerInfo(String customerCode) async {
    customerInfo = await LocalDB.getCustomerAgeing(customerCode);
    List<Map<String, dynamic>> a = [
      {
        'id': customerInfo['title_1'],
        'amount': double.parse(customerInfo['value_1'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_1'] ?? '') ?? 0,
        'title': '0 - ${customerInfo['title_1'].replaceAll('Day', '')} Days'
      },
      {
        'id': customerInfo['title_2'],
        'amount': double.parse(customerInfo['value_2'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_2'] ?? '') ?? 0,
        'title':
            '${int.parse(customerInfo['title_1'].replaceAll('Day', '')) + 1} - ${customerInfo['title_2'].replaceAll('Day', '')} Days'
      },
      {
        'id': customerInfo['title_3'],
        'amount': double.parse(customerInfo['value_3'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_3'] ?? '') ?? 0,
        'title':
            '${int.parse(customerInfo['title_2'].replaceAll('Day', '')) + 1} - ${customerInfo['title_3'].replaceAll('Day', '')} Days'
      },
      {
        'id': customerInfo['title_4'],
        'amount': double.parse(customerInfo['value_4'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_4'] ?? '') ?? 0,
        'title':
            '${int.parse(customerInfo['title_3'].replaceAll('Day', '')) + 1} - ${customerInfo['title_4'].replaceAll('Day', '')} Days'
      },
      {
        'id': customerInfo['title_5'],
        'amount': double.parse(customerInfo['value_5'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_5'] ?? '') ?? 0,
        'title':
            '${int.parse(customerInfo['title_4'].replaceAll('Day', '')) + 1} - ${customerInfo['title_5'].replaceAll('Day', '')} Days'
      },
      {
        'id': customerInfo['title_6'],
        'amount': double.parse(customerInfo['value_6'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_6'] ?? '') ?? 0,
        'title':
            '${int.parse(customerInfo['title_5'].replaceAll('Day', '')) + 1} - ${customerInfo['title_6'].replaceAll('Day', '')} Days'
      },
      {
        'id': customerInfo['title_7'],
        'amount': double.parse(customerInfo['value_7'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_7'] ?? '') ?? 0,
        'title':
            '${int.parse(customerInfo['title_6'].replaceAll('Day', '')) + 1} - ${customerInfo['title_7'].replaceAll('Day', '')} Days'
      },
      {
        'id': customerInfo['title_8'],
        'amount': double.parse(customerInfo['value_8'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_8'] ?? '') ?? 0,
        'title':
            '${int.parse(customerInfo['title_7'].replaceAll('Day', '')) + 1} - ${customerInfo['title_8'].replaceAll('Day', '')} Days'
      },
      {
        'id': customerInfo['title_9'],
        'amount': double.parse(customerInfo['value_9'] ?? '0'),
        'invoiceCount':
            int.tryParse(customerInfo['invoice_count_9'] ?? '') ?? 0,
        'title': '+${customerInfo['title_9'].replaceAll('Abv', '')} Days'
      },
    ];
    List<Map<String, dynamic>> b = [
      {
        'title_1': double.parse(customerInfo['value_1'] ?? '0'),
        'title_2': double.parse(customerInfo['value_2'] ?? '0'),
        'title_3': double.parse(customerInfo['value_3'] ?? '0'),
        'title_4': double.parse(customerInfo['value_4'] ?? '0'),
        'title_5': double.parse(customerInfo['value_5'] ?? '0'),
        'title_6': double.parse(customerInfo['value_6'] ?? '0'),
        'title_7': double.parse(customerInfo['value_7'] ?? '0'),
        'title_8': double.parse(customerInfo['value_8'] ?? '0'),
        'title_9': double.parse(customerInfo['value_9'] ?? '0'),
      }
    ];
    List<Map<String, dynamic>> c = [
      {
        'label': '0 - ${customerInfo['title_1'].replaceAll('Day', '')} Days',
        'color': Color(0xFF2ECC71)
      },
      {
        'label':
            '${int.parse(customerInfo['title_1'].replaceAll('Day', '')) + 1} - ${customerInfo['title_2'].replaceAll('Day', '')} Days',
        'color': Color(0xFF73D467)
      },
      {
        'label':
            '${int.parse(customerInfo['title_2'].replaceAll('Day', '')) + 1} - ${customerInfo['title_3'].replaceAll('Day', '')} Days',
        'color': Color(0xFFB8DF46)
      },
      {
        'label':
            '${int.parse(customerInfo['title_3'].replaceAll('Day', '')) + 1} - ${customerInfo['title_4'].replaceAll('Day', '')} Days',
        'color': Color(0xFFF4C430)
      },
      {
        'label':
            '${int.parse(customerInfo['title_4'].replaceAll('Day', '')) + 1} - ${customerInfo['title_5'].replaceAll('Day', '')} Days',
        'color': Color(0xFFF4A020)
      },
      {
        'label':
            '${int.parse(customerInfo['title_5'].replaceAll('Day', '')) + 1} - ${customerInfo['title_6'].replaceAll('Day', '')} Days',
        'color': Color(0xFFF47C20)
      },
      {
        'label':
            '${int.parse(customerInfo['title_6'].replaceAll('Day', '')) + 1} - ${customerInfo['title_7'].replaceAll('Day', '')} Days',
        'color': Color(0xFFF05050)
      },
      {
        'label':
            '${int.parse(customerInfo['title_7'].replaceAll('Day', '')) + 1} - ${customerInfo['title_8'].replaceAll('Day', '')} Days',
        'color': Color(0xFFD63030)
      },
      {
        'label': '+${customerInfo['title_9'].replaceAll('Abv', '')} Days',
        'color': Color(0xFF8B0000)
      },
    ];
    setState(() {
      itemDataSet = a;
      valueInfo = b;
      labelInfo = c;
    });
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
              border: Border.all(
                color: Colors.white,
                width: 1.5,
              ),
            ),
            child: const Icon(
              Icons.arrow_back,
              color: Colors.white,
              size: 18,
            ),
          ),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Customer Wise Outstanding',
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            fontSize: 18,
          ),
        ),
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            onPressed: () async {
              if (!mounted) return;
              setState(() {
                _isLoading = true;
              });
              final success = await _fetchAndInsertCustomerAgeing();
              if (!mounted) return;
              if (success) {
                await _loadData(); // ✅ await here
                if (!mounted) return;
                setState(() {
                  _isLoading = false; // ✅ now runs AFTER data is loaded
                });
              } else {
                setState(() {
                  _isLoading = false;
                });
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text('Download failed')),
                );
              }
            },
            icon: const Icon(Icons.refresh, color: Colors.white),
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 10),
              child: Column(
                children: [
                  const SizedBox(height: 10),
                  // ── Customer selector
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
                    child: GestureDetector(
                      onTap: () {
                        showSelectorDialog(
                          dialogTitle: 'Select Customer',
                          enableSearch: true,
                          fetchData: () async => customerData,
                          getDisplayText: (item) => item['customer_name'] ?? '',
                          onSelected: (item) {
                            getCustomerInfo(item['customer_code']);
                            setState(() {
                              selectedICode = item['customer_code'] ?? '';
                              selectedCustomerName =
                                  item['customer_name'] ?? 'Select Customer';
                            });
                          },
                        );
                      },
                      child: Container(
                        width: double.infinity,
                        padding: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 14,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(30),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              selectedICode == ''
                                  ? 'Select Customer'
                                  : selectedCustomerName ?? "",
                              style: const TextStyle(
                                color: Colors.black,
                                fontSize: 16,
                              ),
                            ),
                            const Icon(
                              Icons.keyboard_arrow_down,
                              color: Colors.black,
                              size: 24,
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 10),
                  if (selectedICode != '') ...[
                    // ── Donut chart
                    _AgeWiseDonutChart(data: valueInfo, buckets: labelInfo),
                    // const SizedBox(height: 10),
                    // GridView.count(
                    //   crossAxisCount: 3,
                    //   shrinkWrap: true,
                    //   physics: const NeverScrollableScrollPhysics(),
                    //   crossAxisSpacing: 8,
                    //   mainAxisSpacing: 8,
                    //   childAspectRatio: 1.1,
                    //   children: itemDataSet.map((item) {
                    //     final int index = itemDataSet.indexOf(item);
                    //     final List<Color> cardColors = [
                    //       const Color(0xFF2ECC71),
                    //       const Color(0xFF73D467),
                    //       const Color(0xFFB8DF46),
                    //       const Color(0xFFF4C430),
                    //       const Color(0xFFF4A020),
                    //       const Color(0xFFF47C20),
                    //       const Color(0xFFF05050),
                    //       const Color(0xFFD63030),
                    //       const Color(0xFF8B0000),
                    //     ];
                    //     return _SummaryCard(
                    //       label: item['title'],
                    //       amount:
                    //           '₹${(item['amount'] as num).toStringAsFixed(2)}',
                    //       invoiceCount: '${item['invoiceCount']} Invoices',
                    //       color: cardColors[index],
                    //       onPressed: () {
                    //         Navigator.of(context).push(
                    //           MaterialPageRoute(
                    //             builder: (context) =>
                    //                 CustomerWiseOutstandingReportDetailsScreen(
                    //               title: item['id'],
                    //               selectedICode: selectedICode ?? '',
                    //               selectedCustomerName:
                    //                   selectedCustomerName ?? '',
                    //               colorCode: cardColors[index],
                    //             ),
                    //           ),
                    //         );
                    //       },
                    //     );
                    //   }).toList(),
                    // ),
                  ],
                ],
              ),
            ),
    );
  }
}

// ── Paste this widget class anywhere in your file ──

class _AgeWiseDonutChart extends StatefulWidget {
  final List<Map<String, dynamic>> data;
  final List<Map<String, dynamic>> buckets;
  const _AgeWiseDonutChart({required this.data, required this.buckets});

  @override
  State<_AgeWiseDonutChart> createState() => _AgeWiseDonutChartState();
}

class _AgeWiseDonutChartState extends State<_AgeWiseDonutChart> {
  int touchedIndex = -1;

  // Map your DB columns to bucket labels here
  static const List<String> dbKeys = [
    'title_1',
    'title_2',
    'title_3',
    'title_4',
    'title_5',
    'title_6',
    'title_7',
    'title_8',
    'title_9',
  ];

  double _getValue(int index) {
    if (widget.data.isEmpty) return (9 - index) * 10000; // placeholder
    final key = dbKeys[index];
    num val = 0;
    for (final row in widget.data) {
      val += (row[key] ?? 0) as num;
    }
    return val.toDouble();
  }

  @override
  Widget build(BuildContext context) {
    final List<double> values = List.generate(9, _getValue);
    double total = values.fold(0, (a, b) => a + b);
    final double total1 = values.fold(0, (a, b) => a + b);
    if (total == 0) total = 1;

    return Container(
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
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Age-wise Outstanding',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.bold,
              color: Colors.black87,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              // ── Legend ──
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    ...List.generate(9, (i) {
                      final value = values[i];
                      final pct = total > 0 ? (value / total * 100) : 0.0;
                      return Padding(
                        padding: const EdgeInsets.symmetric(vertical: 6),
                        child: Row(
                          children: [
                            SizedBox(
                              width: 90,
                              child: Row(
                                children: [
                                  Container(
                                    width: 8,
                                    height: 8,
                                    decoration: BoxDecoration(
                                      color:
                                          widget.buckets[i]['color'] as Color,
                                      shape: BoxShape.circle,
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Text(
                                      widget.buckets[i]['label'] as String,
                                      style: const TextStyle(
                                        fontSize: 12,
                                        color: Colors.black87,
                                        fontWeight: FontWeight.w500,
                                      ),
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(4),
                                child: Stack(
                                  children: [
                                    Container(
                                      height: 4,
                                      width: double.infinity,
                                      color: Colors.grey.shade100,
                                    ),
                                    FractionallySizedBox(
                                      alignment: Alignment.centerLeft,
                                      widthFactor: pct / 100,
                                      child: Container(
                                        height: 4,
                                        decoration: BoxDecoration(
                                          color: widget.buckets[i]['color']
                                              as Color,
                                          borderRadius:
                                              BorderRadius.circular(4),
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            SizedBox(
                              width: 35,
                              child: Text(
                                '${pct.toStringAsFixed(0)}%',
                                style: TextStyle(
                                  fontSize: 11,
                                  color: Colors.grey.shade500,
                                ),
                                textAlign: TextAlign.right,
                              ),
                            ),
                            const SizedBox(width: 12),
                            SizedBox(
                              width: 60,
                              child: Text(
                                '₹${_formatAmount(value)}',
                                style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: Colors.black87,
                                ),
                                textAlign: TextAlign.right,
                              ),
                            ),
                          ],
                        ),
                      );
                    }),
                    const Padding(
                      padding: EdgeInsets.symmetric(vertical: 8),
                      child: Divider(color: Colors.black12, height: 1),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 0),
                      child: Row(
                        children: [
                          const SizedBox(
                            width: 90,
                            child: Padding(
                              padding: EdgeInsets.only(left: 16),
                              child: Text(
                                'Total',
                                style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.black54,
                                ),
                              ),
                            ),
                          ),
                          const Spacer(),
                          const SizedBox(width: 20),
                          SizedBox(
                            width: 35,
                            child: Text(
                              '100%',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                color: Colors.grey.shade600,
                              ),
                              textAlign: TextAlign.right,
                            ),
                          ),
                          const SizedBox(width: 12),
                          SizedBox(
                            width: 60,
                            child: Text(
                              '₹${_formatAmount(total1)}',
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Colors.black87,
                              ),
                              textAlign: TextAlign.right,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  String _formatAmount(double val) {
    if (val == 0) return '0';
    String str = val.toStringAsFixed(0);
    if (str.length <= 3) return str;
    String lastThree = str.substring(str.length - 3);
    String otherNumbers = str.substring(0, str.length - 3);
    if (otherNumbers.isNotEmpty) {
      final RegExp reg = RegExp(r'(\d)(?=(\d{2})+(?!\d))');
      otherNumbers =
          otherNumbers.replaceAllMapped(reg, (Match m) => '${m[1]},');
      return '$otherNumbers,$lastThree';
    }
    return str;
  }
}

class _SummaryCard extends StatelessWidget {
  final String label;
  final String amount;
  final String invoiceCount;
  final Color color;
  final VoidCallback? onPressed;

  const _SummaryCard({
    required this.label,
    required this.amount,
    required this.invoiceCount,
    required this.color,
    required this.onPressed,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onPressed,
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(10),
          boxShadow: const [
            BoxShadow(
              color: Colors.black12,
              blurRadius: 4,
              offset: Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // ── Colored header
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 8),
              decoration: BoxDecoration(
                color: color,
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(10),
                  topRight: Radius.circular(10),
                ),
              ),
              child: Text(
                label,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 11,
                ),
              ),
            ),
            // ── Amount
            Expanded(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    amount,
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: Colors.black87,
                    ),
                  ),
                  // const Padding(
                  //   padding: EdgeInsets.symmetric(
                  //     horizontal: 8,
                  //     vertical: 4,
                  //   ),
                  //   child: Divider(
                  //     height: 1,
                  //     thickness: 0.5,
                  //     color: Colors.black12,
                  //   ),
                  // ),
                  // // ── Invoice count
                  // Text(
                  //   invoiceCount,
                  //   textAlign: TextAlign.center,
                  //   style: const TextStyle(
                  //     fontSize: 11,
                  //     color: Colors.black54,
                  //   ),
                  // ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
