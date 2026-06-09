import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:starsfa/models/self_appraisal_customer_wise_class.dart';
import 'package:starsfa/screens/target_achieved/target_achievement_details_screen.dart';

class TargetAchievementScreen extends StatefulWidget {
  const TargetAchievementScreen({super.key});

  @override
  State<TargetAchievementScreen> createState() =>
      _TargetAchievementScreenState();
}

class _TargetAchievementScreenState extends State<TargetAchievementScreen> {
  Future<List<Map<String, dynamic>>> futureCust =
      SelfAppraisalCustomerWiseDB.getAllRecords1();
  String? selectedICode = 'All';
  int _selectedOption = 1;
  double _hightestValue = 100;
  String? selectedCustomerName = 'All';

  static List<Map<String, dynamic>> monthData = [];
  static List<Map<String, dynamic>> showMonthDataList = [];
  static List<Map<String, dynamic>> customerData = [];

  @override
  void initState() {
    super.initState();
    futureCust.then((data) {
      final seen = <String>{};
      final List<Map<String, dynamic>> tempList = [
        {'customer_code': 'All', 'customer_name': 'All Customers'},
      ];

      for (var row in data) {
        final code = (row['customer_code'] ?? '').toString().trim();
        final name = (row['customer_name'] ?? '').toString().trim();
        if (code.isNotEmpty && !seen.contains(code)) {
          seen.add(code);
          tempList.add({'customer_code': code, 'customer_name': name});
        }
      }

      setState(() {
        customerData = tempList;
      });
      _buildCurrentYearMonthData(data);
    });
  }

  @override
  void dispose() {
    super.dispose();
  }

  void _buildCurrentYearMonthData(List<Map<String, dynamic>> data) {
    double value = 0.0;

    const monthOrder = [
      'Apr',
      'May',
      'Jun',
      'Jul',
      'Aug',
      'Sep',
      'Oct',
      'Nov',
      'Dec',
      'Jan',
      'Feb',
      'Mar'
    ];

    const monthMap = {
      '1': 'Jan',
      '2': 'Feb',
      '3': 'Mar',
      '4': 'Apr',
      '5': 'May',
      '6': 'Jun',
      '7': 'Jul',
      '8': 'Aug',
      '9': 'Sep',
      '10': 'Oct',
      '11': 'Nov',
      '12': 'Dec',
    };

    final Map<String, Map<String, double>> accumulator = {
      for (var m in monthOrder) m: {'target': 0.0, 'achievement': 0.0}
    };

    final bool isAll = selectedICode?.toLowerCase() == 'all';

    for (var row in data) {
      if (!isAll) {
        final String rowCode = (row['customer_code'] ?? '').toString().trim();
        if (rowCode != selectedICode) continue;
      }

      String rawMonth = (row['month'] ?? '').toString().trim();
      String? shortMonth = monthMap[rawMonth] ??
          monthMap[rawMonth.replaceFirst(RegExp(r'^0'), '')];
      shortMonth ??= monthOrder.contains(rawMonth) ? rawMonth : null;

      if (shortMonth == null || !accumulator.containsKey(shortMonth)) continue;

      final double target =
          double.tryParse((row['target'] ?? '0').toString()) ?? 0.0;
      final double achievement =
          double.tryParse((row['achievement'] ?? '0').toString()) ?? 0.0;

      accumulator[shortMonth]!['target'] =
          accumulator[shortMonth]!['target']! + target;
      accumulator[shortMonth]!['achievement'] =
          accumulator[shortMonth]!['achievement']! + achievement;

      if (value < accumulator[shortMonth]!['target']!) {
        value = accumulator[shortMonth]!['target']!;
      }
      if (value < accumulator[shortMonth]!['achievement']!) {
        value = accumulator[shortMonth]!['achievement']!;
      }
    }

    setState(() {
      _hightestValue = value;
      monthData = monthOrder
          .map((m) => {
                'month': m,
                'target': accumulator[m]!['target']!,
                'achievement': accumulator[m]!['achievement']!,
              })
          .toList();
      showMonthDataList = monthOrder
          .map((m) => {
                'month': m,
                'target': accumulator[m]!['target']!,
                'achievement': accumulator[m]!['achievement']!,
              })
          .where((item) => item['target'] != 0 || item['achievement'] != 0)
          .toList();
    });
  }

  void _buildPreviousYearMonthData(List<Map<String, dynamic>> data) {
    double value = 0.0;

    const monthOrder = [
      'Apr',
      'May',
      'Jun',
      'Jul',
      'Aug',
      'Sep',
      'Oct',
      'Nov',
      'Dec',
      'Jan',
      'Feb',
      'Mar'
    ];

    const monthMap = {
      '1': 'Jan',
      '2': 'Feb',
      '3': 'Mar',
      '4': 'Apr',
      '5': 'May',
      '6': 'Jun',
      '7': 'Jul',
      '8': 'Aug',
      '9': 'Sep',
      '10': 'Oct',
      '11': 'Nov',
      '12': 'Dec',
    };

    final Map<String, Map<String, double>> accumulator = {
      for (var m in monthOrder) m: {'target': 0.0, 'achievement': 0.0}
    };

    final bool isAll = selectedICode?.toLowerCase() == 'all';

    for (var row in data) {
      if (!isAll) {
        final String rowCode = (row['customer_code'] ?? '').toString().trim();
        if (rowCode != selectedICode) continue;
      }

      String rawMonth = (row['month'] ?? '').toString().trim();
      String? shortMonth = monthMap[rawMonth] ??
          monthMap[rawMonth.replaceFirst(RegExp(r'^0'), '')];
      shortMonth ??= monthOrder.contains(rawMonth) ? rawMonth : null;

      if (shortMonth == null || !accumulator.containsKey(shortMonth)) continue;

      final double target =
          double.tryParse((row['previous_target'] ?? '0').toString()) ?? 0.0;
      final double achievement =
          double.tryParse((row['previous_achievement'] ?? '0').toString()) ??
              0.0;

      accumulator[shortMonth]!['target'] =
          accumulator[shortMonth]!['target']! + target;
      accumulator[shortMonth]!['achievement'] =
          accumulator[shortMonth]!['achievement']! + achievement;

      if (value < accumulator[shortMonth]!['target']!) {
        value = accumulator[shortMonth]!['target']!;
      }
      if (value < accumulator[shortMonth]!['achievement']!) {
        value = accumulator[shortMonth]!['achievement']!;
      }
    }

    setState(() {
      _hightestValue = value;
      monthData = monthOrder
          .map((m) => {
                'month': m,
                'target': accumulator[m]!['target']!,
                'achievement': accumulator[m]!['achievement']!,
              })
          .toList();
      showMonthDataList = monthOrder
          .map((m) => {
                'month': m,
                'target': accumulator[m]!['target']!,
                'achievement': accumulator[m]!['achievement']!,
              })
          .where((item) => item['target'] != 0 || item['achievement'] != 0)
          .toList();
    });
  }

  void _buildComparativeYearMonthData(List<Map<String, dynamic>> data) {
    double value = 0.0;

    const monthOrder = [
      'Apr',
      'May',
      'Jun',
      'Jul',
      'Aug',
      'Sep',
      'Oct',
      'Nov',
      'Dec',
      'Jan',
      'Feb',
      'Mar'
    ];

    const monthMap = {
      '1': 'Jan',
      '2': 'Feb',
      '3': 'Mar',
      '4': 'Apr',
      '5': 'May',
      '6': 'Jun',
      '7': 'Jul',
      '8': 'Aug',
      '9': 'Sep',
      '10': 'Oct',
      '11': 'Nov',
      '12': 'Dec',
    };

    final Map<String, Map<String, double>> accumulator = {
      for (var m in monthOrder) m: {'target': 0.0, 'achievement': 0.0}
    };

    final bool isAll = selectedICode?.toLowerCase() == 'all';

    for (var row in data) {
      if (!isAll) {
        final String rowCode = (row['customer_code'] ?? '').toString().trim();
        if (rowCode != selectedICode) continue;
      }

      String rawMonth = (row['month'] ?? '').toString().trim();
      String? shortMonth = monthMap[rawMonth] ??
          monthMap[rawMonth.replaceFirst(RegExp(r'^0'), '')];
      shortMonth ??= monthOrder.contains(rawMonth) ? rawMonth : null;

      if (shortMonth == null || !accumulator.containsKey(shortMonth)) continue;

      final double target =
          double.tryParse((row['previous_achievement'] ?? '0').toString()) ??
              0.0;
      final double achievement =
          double.tryParse((row['achievement'] ?? '0').toString()) ?? 0.0;

      accumulator[shortMonth]!['target'] =
          accumulator[shortMonth]!['target']! + target;
      accumulator[shortMonth]!['achievement'] =
          accumulator[shortMonth]!['achievement']! + achievement;

      if (value < accumulator[shortMonth]!['target']!) {
        value = accumulator[shortMonth]!['target']!;
      }
      if (value < accumulator[shortMonth]!['achievement']!) {
        value = accumulator[shortMonth]!['achievement']!;
      }
    }

    setState(() {
      _hightestValue = value;
      monthData = monthOrder
          .map((m) => {
                'month': m,
                'target': accumulator[m]!['target']!,
                'achievement': accumulator[m]!['achievement']!,
              })
          .toList();
      showMonthDataList = monthOrder
          .map((m) => {
                'month': m,
                'target': accumulator[m]!['target']!,
                'achievement': accumulator[m]!['achievement']!,
              })
          .where((item) => item['target'] != 0 || item['achievement'] != 0)
          .toList();
    });
  }

  void _showPopup() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (BuildContext context) {
        return DraggableScrollableSheet(
          builder: (context, scrollController) {
            int tempSelected = _selectedOption;
            return StatefulBuilder(
              builder: (context, setModalState) {
                return Container(
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    borderRadius:
                        BorderRadius.vertical(top: Radius.circular(20)),
                  ),
                  padding: const EdgeInsets.only(
                      left: 20, right: 20, top: 12, bottom: 12),
                  child: SingleChildScrollView(
                    controller: scrollController,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Center(
                          child: Container(
                            width: 40,
                            height: 4,
                            margin: const EdgeInsets.only(bottom: 16),
                            decoration: BoxDecoration(
                              color: Colors.grey.shade300,
                              borderRadius: BorderRadius.circular(2),
                            ),
                          ),
                        ),
                        const Text(
                          "Select Your Option",
                          style: TextStyle(
                              fontSize: 16, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 8),
                        RadioListTile<int>(
                          contentPadding: EdgeInsets.zero,
                          title: const Text("Current FY"),
                          value: 1,
                          groupValue: tempSelected,
                          activeColor: Colors.red,
                          onChanged: (value) {
                            setModalState(() => tempSelected = value!);
                            setState(() => _selectedOption = value!);
                            futureCust.then(
                                (data) => _buildCurrentYearMonthData(data));
                            Navigator.of(context).pop();
                          },
                        ),
                        RadioListTile<int>(
                          contentPadding: EdgeInsets.zero,
                          title: const Text("Previous FY"),
                          value: 2,
                          groupValue: tempSelected,
                          activeColor: Colors.red,
                          onChanged: (value) {
                            setModalState(() => tempSelected = value!);
                            setState(() => _selectedOption = value!);
                            futureCust.then(
                                (data) => _buildPreviousYearMonthData(data));
                            Navigator.of(context).pop();
                          },
                        ),
                        RadioListTile<int>(
                          contentPadding: EdgeInsets.zero,
                          title: const Text("Current FY vs Previous FY"),
                          value: 3,
                          groupValue: tempSelected,
                          activeColor: Colors.red,
                          onChanged: (value) {
                            setModalState(() => tempSelected = value!);
                            setState(() => _selectedOption = value!);
                            futureCust.then(
                                (data) => _buildComparativeYearMonthData(data));
                            Navigator.of(context).pop();
                          },
                        ),
                        const SizedBox(height: 20),
                        Row(
                          children: [
                            Expanded(
                              child: ElevatedButton(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.red,
                                  foregroundColor: Colors.white,
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 14),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                ),
                                onPressed: () {
                                  setState(
                                      () => _selectedOption = tempSelected);
                                  Navigator.of(context).pop();
                                },
                                child: const Text("Submit"),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                      ],
                    ),
                  ),
                );
              },
            );
          },
        );
      },
    );
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

  Widget _legendDot(Color color) {
    return Container(
      width: 12,
      height: 12,
      decoration: BoxDecoration(color: color, shape: BoxShape.circle),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(110),
        child: AppBar(
          backgroundColor: Colors.red,
          leading: IconButton(
            icon: Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: 1.5),
              ),
              child:
                  const Icon(Icons.arrow_back, color: Colors.white, size: 18),
            ),
            onPressed: () => Navigator.pop(context),
          ),
          title: const Text(
            'Month-wise Performance',
            style: TextStyle(
                color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18),
          ),
          bottom: PreferredSize(
            preferredSize: const Size.fromHeight(52),
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
              child: GestureDetector(
                onTap: _showPopup,
                child: Container(
                  width: double.infinity,
                  padding:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  decoration: BoxDecoration(
                    color: Colors.red.shade400,
                    borderRadius: BorderRadius.circular(30),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        _selectedOption == 1
                            ? 'Current FY'
                            : _selectedOption == 2
                                ? 'Previous FY'
                                : 'Current FY vs Previous FY',
                        style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 16),
                      ),
                      const Icon(Icons.keyboard_arrow_down,
                          color: Colors.white, size: 24),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
      body: SingleChildScrollView(
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
                      offset: Offset(0, 2)),
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
                      setState(() {
                        selectedICode = item['customer_code'] ?? 'All';
                        selectedCustomerName =
                            item['customer_name'] ?? 'All Customers';
                      });
                      futureCust.then((data) {
                        if (_selectedOption == 1) {
                          _buildCurrentYearMonthData(data);
                        } else if (_selectedOption == 2) {
                          _buildPreviousYearMonthData(data);
                        } else {
                          _buildComparativeYearMonthData(data);
                        }
                      });
                    },
                  );
                },
                child: Container(
                  width: double.infinity,
                  padding:
                      const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(30),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        selectedICode == 'All'
                            ? 'All'
                            : selectedCustomerName ?? "",
                        style:
                            const TextStyle(color: Colors.black, fontSize: 16),
                      ),
                      const Icon(Icons.keyboard_arrow_down,
                          color: Colors.black, size: 24),
                    ],
                  ),
                ),
              ),
            ),
            const SizedBox(height: 10),

            // ── Performance Graph card
            Container(
              width: double.infinity,
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
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Padding(
                    padding: EdgeInsets.fromLTRB(20, 20, 20, 0),
                    child: Text(
                      'Performance Graph',
                      style:
                          TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.fromLTRB(20, 10, 20, 0),
                    child: Row(
                      children: [
                        _legendDot(_selectedOption == 1
                            ? const Color(0xFF4A90E2)
                            : _selectedOption == 2
                                ? const Color(0xFF0076fe)
                                : const Color(0xFFFF7700)),
                        const SizedBox(width: 6),
                        Text(
                          _selectedOption == 1
                              ? "Targets (MT)"
                              : _selectedOption == 2
                                  ? "Targets (MT)"
                                  : "Last Year\nAchievements (MT)",
                          style: const TextStyle(fontSize: 13),
                        ),
                        const SizedBox(width: 20),
                        _legendDot(_selectedOption == 1
                            ? const Color(0xFF50E3C2)
                            : _selectedOption == 2
                                ? const Color(0xFF00fa85)
                                : const Color(0xFF50E3C2)),
                        const SizedBox(width: 6),
                        Text(
                          _selectedOption == 1
                              ? "Achievements (MT)"
                              : _selectedOption == 2
                                  ? "Achievements (MT)"
                                  : "Current Year\nAchievements (MT)",
                          style: const TextStyle(fontSize: 13),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 20),

                  // ── Scrollable bar chart
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 10),
                    child: SizedBox(
                      height: 320,
                      width: MediaQuery.of(context).size.width,
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // ── Fixed Y-axis labels (no touch, no barGroups)
                          SizedBox(
                            width: 45,
                            child: Padding(
                              padding: const EdgeInsets.only(bottom: 30),
                              child: BarChart(
                                BarChartData(
                                  maxY: _hightestValue > 0
                                      ? (_hightestValue.toDouble() * 4 / 3)
                                      : 100,
                                  minY: 0,
                                  // ✅ NO barTouchData here — this chart has no bars
                                  gridData: const FlGridData(show: false),
                                  borderData: FlBorderData(show: false),
                                  barGroups: const [],
                                  titlesData: FlTitlesData(
                                    leftTitles: AxisTitles(
                                      sideTitles: SideTitles(
                                        showTitles: true,
                                        reservedSize: 45,
                                        interval: _hightestValue > 0
                                            ? (_hightestValue.toDouble() *
                                                    4 /
                                                    3) /
                                                5
                                            : 20,
                                        getTitlesWidget: (value, meta) => Text(
                                          value.toInt().toString(),
                                          style: const TextStyle(
                                              color: Colors.grey, fontSize: 10),
                                        ),
                                      ),
                                    ),
                                    rightTitles: const AxisTitles(
                                        sideTitles:
                                            SideTitles(showTitles: false)),
                                    topTitles: const AxisTitles(
                                        sideTitles:
                                            SideTitles(showTitles: false)),
                                    bottomTitles: const AxisTitles(
                                        sideTitles:
                                            SideTitles(showTitles: false)),
                                  ),
                                ),
                              ),
                            ),
                          ),

                          // ── Scrollable bars ✅ touchCallback is HERE
                          Expanded(
                            child: SingleChildScrollView(
                              scrollDirection: Axis.horizontal,
                              child: SizedBox(
                                width: monthData.length * 70.0,
                                height: 320,
                                child: BarChart(
                                  BarChartData(
                                    maxY: _hightestValue > 0
                                        ? (_hightestValue.toDouble() * 4 / 3)
                                        : 100,
                                    minY: 0,
                                    gridData: FlGridData(
                                      show: true,
                                      drawVerticalLine: false,
                                      horizontalInterval: _hightestValue > 0
                                          ? (_hightestValue.toDouble() *
                                                  4 /
                                                  3) /
                                              5
                                          : 20,
                                      getDrawingHorizontalLine: (value) =>
                                          FlLine(
                                        color: Colors.grey.shade300,
                                        strokeWidth: 1,
                                        dashArray: [5, 5],
                                      ),
                                    ),
                                    borderData: FlBorderData(show: false),
                                    titlesData: FlTitlesData(
                                      leftTitles: const AxisTitles(
                                          sideTitles:
                                              SideTitles(showTitles: false)),
                                      rightTitles: const AxisTitles(
                                          sideTitles:
                                              SideTitles(showTitles: false)),
                                      topTitles: const AxisTitles(
                                          sideTitles:
                                              SideTitles(showTitles: false)),
                                      bottomTitles: AxisTitles(
                                        sideTitles: SideTitles(
                                          showTitles: true,
                                          reservedSize: 30,
                                          getTitlesWidget: (value, meta) {
                                            final index = value ~/ 2;
                                            if (index >= monthData.length)
                                              return const SizedBox();
                                            return Padding(
                                              padding:
                                                  const EdgeInsets.only(top: 8),
                                              child: Text(
                                                monthData[index]['month'],
                                                style: const TextStyle(
                                                    color: Colors.grey,
                                                    fontSize: 11),
                                              ),
                                            );
                                          },
                                        ),
                                      ),
                                    ),
                                    barGroups: List.generate(
                                      monthData.length,
                                      (index) {
                                        final data = monthData[index];
                                        return BarChartGroupData(
                                          x: index * 2,
                                          groupVertically: false,
                                          barRods: [
                                            BarChartRodData(
                                              toY: data['target'],
                                              color: _selectedOption == 1
                                                  ? const Color(0xFF4A90E2)
                                                  : _selectedOption == 2
                                                      ? const Color(0xFF0076fe)
                                                      : const Color(0xFFFF7700),
                                              width: 18,
                                              borderRadius:
                                                  const BorderRadius.only(
                                                topLeft: Radius.circular(4),
                                                topRight: Radius.circular(4),
                                              ),
                                            ),
                                            BarChartRodData(
                                              toY: data['achievement'],
                                              color: _selectedOption == 1
                                                  ? const Color(0xFF50E3C2)
                                                  : _selectedOption == 2
                                                      ? const Color(0xFF00fa85)
                                                      : const Color(0xFF50E3C2),
                                              width: 18,
                                              borderRadius:
                                                  const BorderRadius.only(
                                                topLeft: Radius.circular(4),
                                                topRight: Radius.circular(4),
                                              ),
                                            ),
                                          ],
                                          barsSpace: 4,
                                          showingTooltipIndicators:
                                              data['target'] > 0 ||
                                                      data['achievement'] > 0
                                                  ? [0, 1]
                                                  : [],
                                        );
                                      },
                                    ),
                                    // ✅ touchCallback on the CORRECT chart
                                    barTouchData: BarTouchData(
                                      enabled: true,
                                      touchCallback: (FlTouchEvent event,
                                          BarTouchResponse? response) {
                                        if (event is FlTapUpEvent &&
                                            response != null &&
                                            response.spot != null) {
                                          final groupIndex = response
                                              .spot!.touchedBarGroupIndex;
                                          final data = monthData[groupIndex];
                                          print(data);
                                          if (selectedICode
                                                  ?.toLowerCase()
                                                  .trim() ==
                                              'all') {
                                            Navigator.push(
                                              context,
                                              MaterialPageRoute(
                                                builder: (_) =>
                                                    TargetAchievementDetailsScreen(
                                                        month: data['month'],
                                                        target: data['target'],
                                                        achievement:
                                                            data['achievement'],
                                                        selectedOption:
                                                            _selectedOption),
                                              ),
                                            );
                                          }
                                        }
                                      },
                                      touchTooltipData: BarTouchTooltipData(
                                        getTooltipColor: (_) =>
                                            Colors.transparent,
                                        tooltipPadding: EdgeInsets.zero,
                                        tooltipMargin: 6,
                                        getTooltipItem:
                                            (group, groupIndex, rod, rodIndex) {
                                          if (rod.toY == 0) return null;
                                          return BarTooltipItem(
                                            rod.toY.toInt().toString(),
                                            const TextStyle(
                                              color: Colors.black,
                                              fontSize: 10,
                                              fontWeight: FontWeight.bold,
                                            ),
                                          );
                                        },
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),
                ],
              ),
            ),
            const SizedBox(height: 10),

            // ── Monthly Breakup header
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    "Monthly Breakup",
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                  ),
                  const Text("Detailed Performance Analysis",
                      style: TextStyle(fontSize: 13)),
                  const Divider(),
                ],
              ),
            ),
            const SizedBox(height: 10),

            // ── Monthly list
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              padding: EdgeInsets.zero,
              itemCount: showMonthDataList.length,
              itemBuilder: (context, index) {
                final item = showMonthDataList[index];
                final double target = item['target'];
                final double achievement = item['achievement'];
                final double difference = achievement - target;
                final double percentage =
                    target > 0 ? (achievement / target) * 100 : 0;
                final bool isSurplus = difference >= 0;
                final String percentageText =
                    target > 0 ? '${percentage.toStringAsFixed(2)} %' : '--- %';
                final String differenceText = isSurplus
                    ? '+${difference.toStringAsFixed(0)} MT'
                    : '${difference.toStringAsFixed(0)} MT';

                const monthNames = {
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
                final String fullMonth =
                    monthNames[item['month']] ?? item['month'];
                final double progressRatio =
                    target > 0 ? (achievement / target).clamp(0.0, 1.0) : 1.0;

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
                              offset: Offset(0, 2)),
                        ],
                      ),
                      child: Column(
                        children: [
                          Padding(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 14),
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
                                        color: percentage >= 100
                                            ? Colors.green.shade50
                                            : percentage >= 50
                                                ? Colors.orange.shade50
                                                : Colors.red.shade50,
                                        borderRadius: BorderRadius.circular(10),
                                      ),
                                      child: Icon(
                                        Icons.bar_chart,
                                        color: percentage >= 100
                                            ? Colors.green
                                            : percentage >= 50
                                                ? Colors.orange
                                                : Colors.red,
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
                                            fullMonth,
                                            style: const TextStyle(
                                              fontSize: 18,
                                              fontWeight: FontWeight.bold,
                                              color: Colors.black,
                                            ),
                                          ),
                                          const SizedBox(height: 2),
                                          Text(
                                            _selectedOption == 3
                                                ? 'Previous FY Ach - ${target.toStringAsFixed(2)} MT'
                                                : 'Target - ${target.toStringAsFixed(2)} MT',
                                            style: const TextStyle(
                                                fontSize: 13,
                                                color: Colors.black87),
                                          ),
                                          Text(
                                            _selectedOption == 3
                                                ? 'Current FY Ach - ${achievement.toStringAsFixed(2)} MT'
                                                : 'Ach - ${achievement.toStringAsFixed(2)} MT',
                                            style: const TextStyle(
                                                fontSize: 13,
                                                color: Colors.black87),
                                          ),
                                        ],
                                      ),
                                    ),
                                    if (_selectedOption != 3 ||
                                        achievement != 0) ...[
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 10, vertical: 6),
                                        decoration: BoxDecoration(
                                          color: percentage >= 100
                                              ? Colors.green.shade50
                                              : percentage >= 50
                                                  ? Colors.orange.shade50
                                                  : Colors.red.shade50,
                                          borderRadius:
                                              BorderRadius.circular(8),
                                          border: Border.all(
                                            color: percentage >= 100
                                                ? Colors.green.shade100
                                                : percentage >= 50
                                                    ? Colors.orange.shade100
                                                    : Colors.red.shade100,
                                          ),
                                        ),
                                        child: Row(
                                          mainAxisSize: MainAxisSize.min,
                                          children: [
                                            Icon(
                                              percentage >= 100
                                                  ? Icons.trending_up
                                                  : Icons.trending_down,
                                              color: percentage >= 100
                                                  ? Colors.green
                                                  : percentage >= 50
                                                      ? Colors.orange
                                                      : Colors.red,
                                              size: 16,
                                            ),
                                            const SizedBox(width: 4),
                                            Text(
                                              percentageText,
                                              style: TextStyle(
                                                color: percentage >= 100
                                                    ? Colors.green
                                                    : percentage >= 50
                                                        ? Colors.orange
                                                        : Colors.red,
                                                fontWeight: FontWeight.bold,
                                                fontSize: 14,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ],
                                ),
                                const SizedBox(height: 12),
                                if (_selectedOption != 3 ||
                                    achievement != 0) ...[
                                  ClipRRect(
                                    borderRadius: BorderRadius.circular(6),
                                    child: Stack(
                                      children: [
                                        Container(
                                          height: 8,
                                          width: double.infinity,
                                          color: Colors.grey.shade200,
                                        ),
                                        FractionallySizedBox(
                                          widthFactor: progressRatio == 0
                                              ? 1.0
                                              : progressRatio,
                                          child: Container(
                                            height: 8,
                                            decoration: BoxDecoration(
                                              gradient: LinearGradient(
                                                colors: [
                                                  percentage >= 100
                                                      ? Colors.green.shade300
                                                      : percentage >= 50
                                                          ? Colors
                                                              .orange.shade300
                                                          : Colors.red.shade300,
                                                  percentage >= 100
                                                      ? Colors.green.shade600
                                                      : percentage >= 50
                                                          ? Colors
                                                              .orange.shade600
                                                          : Colors.red.shade600,
                                                ],
                                              ),
                                            ),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  const SizedBox(height: 10),
                                  Row(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      const Text(
                                        'Difference',
                                        style: TextStyle(
                                            color: Colors.grey, fontSize: 13),
                                      ),
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 10, vertical: 5),
                                        decoration: BoxDecoration(
                                          color: percentage >= 100
                                              ? Colors.green.shade50
                                              : percentage >= 50
                                                  ? Colors.orange.shade50
                                                  : Colors.red.shade50,
                                          borderRadius:
                                              BorderRadius.circular(8),
                                          border: Border.all(
                                            color: percentage >= 100
                                                ? Colors.green.shade100
                                                : percentage >= 50
                                                    ? Colors.orange.shade100
                                                    : Colors.red.shade100,
                                          ),
                                        ),
                                        child: Row(
                                          mainAxisSize: MainAxisSize.min,
                                          children: [
                                            Text(
                                              differenceText,
                                              style: TextStyle(
                                                color: percentage >= 100
                                                    ? Colors.green
                                                    : percentage >= 50
                                                        ? Colors.orange
                                                        : Colors.red,
                                                fontWeight: FontWeight.bold,
                                                fontSize: 14,
                                              ),
                                            ),
                                            const SizedBox(width: 6),
                                            Text(
                                              isSurplus
                                                  ? 'Surplus'
                                                  : 'Shortfall',
                                              style: TextStyle(
                                                color: percentage >= 100
                                                    ? Colors.green
                                                    : percentage >= 50
                                                        ? Colors.orange
                                                        : Colors.red,
                                                fontSize: 13,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ],
                            ),
                          ),
                          const Divider(
                              height: 1,
                              color: Color(0xFFFEF2F2),
                              thickness: 1),
                        ],
                      ),
                    ),
                    const SizedBox(height: 10),
                  ],
                );
              },
            ),
          ],
        ),
      ),
    );
  }
}
