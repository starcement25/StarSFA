import 'package:flutter/material.dart';
import 'package:starsfa/models/self_appraisal_customer_wise_class.dart';

class TargetAchievementDetailsScreen extends StatefulWidget {
  final String month;
  final double target;
  final double achievement;
  final int selectedOption;

  const TargetAchievementDetailsScreen({
    super.key,
    required this.month,
    required this.target,
    required this.achievement,
    required this.selectedOption,
  });

  @override
  State<TargetAchievementDetailsScreen> createState() =>
      _TargetAchievementDetailsScreenState();
}

class _TargetAchievementDetailsScreenState
    extends State<TargetAchievementDetailsScreen> {
  List<Map<String, dynamic>> customerWiseList = [];
  bool isLoading = true;

  // Full month name map
  static const monthNames = {
    'Apr': 'April',
    'May': 'May',
    'Jun': 'June',
    'Jul': 'July',
    'Aug': 'August',
    'Sep': 'September',
    'Oct': 'October',
    'Nov': 'November',
    'Dec': 'December',
    'Jan': 'January',
    'Feb': 'February',
    'Mar': 'March',
  };

  // Short month → numeric map for DB matching
  static const monthToNum = {
    'Jan': '1',
    'Feb': '2',
    'Mar': '3',
    'Apr': '4',
    'May': '5',
    'Jun': '6',
    'Jul': '7',
    'Aug': '8',
    'Sep': '9',
    'Oct': '10',
    'Nov': '11',
    'Dec': '12',
  };

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final allData = await SelfAppraisalCustomerWiseDB.getAllRecords1();

    final String? targetMonthNum = monthToNum[widget.month];

    // Filter rows matching this month
    final filtered = allData.where((row) {
      String rawMonth = (row['month'] ?? '').toString().trim();
      // Normalize: remove leading zero
      final normalized = rawMonth.replaceFirst(RegExp(r'^0'), '');
      return normalized == targetMonthNum || rawMonth == widget.month;
    }).toList();

    // Build per-customer summary
    final Map<String, Map<String, double>> accumulator = {};

    for (var row in filtered) {
      final String customerName =
          (row['customer_name'] ?? 'Unknown').toString().trim();

      if (!accumulator.containsKey(customerName)) {
        accumulator[customerName] = {'target': 0.0, 'achievement': 0.0};
      }

      double target = 0.0;
      double achievement = 0.0;

      if (widget.selectedOption == 1) {
        target = double.tryParse((row['target'] ?? '0').toString()) ?? 0.0;
        achievement =
            double.tryParse((row['achievement'] ?? '0').toString()) ?? 0.0;
      } else if (widget.selectedOption == 2) {
        target =
            double.tryParse((row['previous_target'] ?? '0').toString()) ?? 0.0;
        achievement =
            double.tryParse((row['previous_achievement'] ?? '0').toString()) ??
                0.0;
      } else {
        // Comparative: previous achievement vs current achievement
        target =
            double.tryParse((row['previous_achievement'] ?? '0').toString()) ??
                0.0;
        achievement =
            double.tryParse((row['achievement'] ?? '0').toString()) ?? 0.0;
      }

      accumulator[customerName]!['target'] =
          accumulator[customerName]!['target']! + target;
      accumulator[customerName]!['achievement'] =
          accumulator[customerName]!['achievement']! + achievement;
    }

    setState(() {
      customerWiseList = accumulator.entries
          .map((e) => {
                'customer_name': e.key,
                'target': e.value['target']!,
                'achievement': e.value['achievement']!,
              })
          .toList();
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    final String fullMonth = monthNames[widget.month] ?? widget.month;

    // Column headers based on selectedOption
    final String col1Label =
        widget.selectedOption == 3 ? 'Prev Ach (MT)' : 'Target (MT)';
    final String col2Label =
        widget.selectedOption == 3 ? 'Curr Ach (MT)' : 'Ach (MT)';

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
            child: const Icon(Icons.arrow_back, color: Colors.white, size: 18),
          ),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          '$fullMonth - Performance Details',
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            fontSize: 18,
          ),
        ),
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 10),
              child: Column(
                children: [
                  const SizedBox(height: 10),

                  // ── Summary card
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      boxShadow: const [
                        BoxShadow(
                            color: Colors.black12,
                            blurRadius: 4,
                            offset: Offset(0, 2)),
                      ],
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _summaryTile(
                          label: col1Label.replaceAll(' (MT)', ''),
                          value: '${widget.target.toStringAsFixed(2)} MT',
                          color: Colors.blue,
                        ),
                        Container(
                            width: 1, height: 40, color: Colors.grey.shade300),
                        _summaryTile(
                          label: col2Label.replaceAll(' (MT)', ''),
                          value: '${widget.achievement.toStringAsFixed(2)} MT',
                          color: Colors.green,
                        ),
                        Container(
                            width: 1, height: 40, color: Colors.grey.shade300),
                        _summaryTile(
                          label: '%',
                          value: widget.target > 0
                              ? '${(widget.achievement / widget.target * 100).toStringAsFixed(1)}%'
                              : '---',
                          color: Colors.orange,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 10),

                  // ── Table
                  customerWiseList.isEmpty
                      ? const Padding(
                          padding: EdgeInsets.all(20),
                          child: Text('No data found for this month.',
                              style: TextStyle(color: Colors.grey)),
                        )
                      : Container(
                          decoration: BoxDecoration(
                            border: Border.all(color: Colors.black, width: 1),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Column(
                            children: [
                              // Header row
                              Row(
                                children: [
                                  _headerCell('Customer Name', flex: 2),
                                  _dividerV(),
                                  _headerCell(col1Label),
                                  _dividerV(),
                                  _headerCell(col2Label),
                                ],
                              ),
                              Container(height: 1, color: Colors.black),

                              // Data rows
                              ListView.separated(
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                padding: EdgeInsets.zero,
                                itemCount: customerWiseList.length,
                                separatorBuilder: (_, __) =>
                                    Container(height: 1, color: Colors.black26),
                                itemBuilder: (context, index) {
                                  final item = customerWiseList[index];
                                  final double t = item['target'];
                                  final double a = item['achievement'];
                                  final bool isEven = index % 2 == 0;

                                  return Row(
                                    children: [
                                      _dataCell(
                                        item['customer_name'],
                                        flex: 2,
                                        bgColor: isEven
                                            ? Colors.white
                                            : Colors.grey.shade50,
                                        align: TextAlign.left,
                                      ),
                                      _dividerV(),
                                      _dataCell(
                                        '${t.toStringAsFixed(2)} MT',
                                        bgColor: isEven
                                            ? Colors.white
                                            : Colors.grey.shade50,
                                        align: TextAlign.right,
                                      ),
                                      _dividerV(),
                                      _dataCell(
                                        '${a.toStringAsFixed(2)} MT',
                                        bgColor: isEven
                                            ? Colors.white
                                            : Colors.grey.shade50,
                                        textColor: a >= t
                                            ? Colors.green.shade700
                                            : Colors.red.shade700,
                                        align: TextAlign.right,
                                      ),
                                    ],
                                  );
                                },
                              ),
                            ],
                          ),
                        ),
                  const SizedBox(height: 20),
                ],
              ),
            ),
    );
  }

  Widget _summaryTile(
      {required String label, required String value, required Color color}) {
    return Column(
      children: [
        Text(label,
            style: TextStyle(
                fontSize: 12,
                color: Colors.grey.shade600,
                fontWeight: FontWeight.w500)),
        const SizedBox(height: 4),
        Text(value,
            style: TextStyle(
                fontSize: 15, color: color, fontWeight: FontWeight.bold)),
      ],
    );
  }

  Widget _headerCell(String text, {int flex = 1}) {
    return Expanded(
      flex: flex,
      child: Container(
        height: 38,
        color: Colors.red,
        padding: const EdgeInsets.symmetric(horizontal: 6),
        child: Center(
          child: Text(
            text,
            textAlign: TextAlign.center,
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.bold,
              fontSize: 12,
            ),
          ),
        ),
      ),
    );
  }

  Widget _dataCell(String text,
      {int flex = 1,
      Color? bgColor,
      Color? textColor,
      TextAlign align = TextAlign.center}) {
    return Expanded(
      flex: flex,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 6),
        color: bgColor ?? Colors.white,
        child: Text(
          text,
          textAlign: align,
          style: TextStyle(
            color: textColor ?? Colors.black87,
            fontSize: 13,
            fontWeight: FontWeight.w500,
          ),
        ),
      ),
    );
  }

  Widget _dividerV() => Container(width: 1, color: Colors.black26);
}
