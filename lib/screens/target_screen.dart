import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:starsfa/models/self_appraisal_branch_wise_class.dart';
import 'package:starsfa/models/self_appraisal_customer_wise_class.dart';
import 'dart:math' as math;

import 'package:starsfa/screens/target_screen_table.dart';

class TargetScreen extends StatefulWidget {
  const TargetScreen({super.key});

  @override
  State<TargetScreen> createState() => _TargetScreenState();
}

class _TargetScreenState extends State<TargetScreen> {
  Future<List<SelfAppraisalCustomerWiseDB>> futureCust =
      SelfAppraisalCustomerWiseDB.getAllRecords();
  Future<List<SelfAppraisalBranchWiseDB>> futureBranch =
      SelfAppraisalBranchWiseDB.getAllRecords();
  String? selectedICode = 'All';
  String? selectedBranchCode;
  bool isPresentCustomerWise = true;
  String monthNumberToName(String monthNumber) {
    int monthInt = int.parse(monthNumber);
    switch (monthInt) {
      case 1:
        return 'Jan';
      case 2:
        return 'Feb';
      case 3:
        return 'Mar';
      case 4:
        return 'Apr';
      case 5:
        return 'May';
      case 6:
        return 'Jun';
      case 7:
        return 'Jul';
      case 8:
        return 'Aug';
      case 9:
        return 'Sep';
      case 10:
        return 'Oct';
      case 11:
        return 'Nov';
      case 12:
        return 'Dec';
      default:
        return '';
    }
  }

  // change the orientation of the screen
  void changeOrientation() {
    // wait for _TargetScreenState.initState() to complete
    WidgetsBinding.instance.addPostFrameCallback((_) {
      // check if the orientation is portrait
      if (MediaQuery.of(context).orientation == Orientation.portrait) {
        // change the orientation to landscape
        SystemChrome.setPreferredOrientations([
          DeviceOrientation.landscapeLeft,
          DeviceOrientation.landscapeRight
        ]);
      }
    });
  }

  // Pop Up selection dialog - Branchwise and Customerwise
  void _showDialogSelection() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Please Select Financial Year'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: <Widget>[
              ListTile(
                title: const Text('FY 2024-25', style: TextStyle(fontSize: 20)),
                onTap: () {
                  setState(() {
                    isPresentCustomerWise = false;
                  });
                  Navigator.pop(context);
                },
              ),
              ListTile(
                title: const Text('FY 2025-26', style: TextStyle(fontSize: 20)),
                onTap: () {
                  setState(() {
                    isPresentCustomerWise = true;
                  });
                  Navigator.pop(context);
                },
              ),
            ],
          ),
        );
      },
    );
  }

  @override
  void initState() {
    changeOrientation();
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      _showDialogSelection();
    });
  }

  @override
  void dispose() {
    // reset the orientation to portrait
    SystemChrome.setPreferredOrientations(
        [DeviceOrientation.portraitUp, DeviceOrientation.portraitDown]);
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
        backgroundColor: Colors.red,
        title: const Text(
          'Customer wise Target-vs-Achv.',
          style: TextStyle(color: Colors.white),
        ),
      ),
      body: Container(
        decoration: const BoxDecoration(
          // image: DecorationImage(
          //   image: AssetImage('assets/background.jpg'),
          //   fit: BoxFit.cover,
          // ),
          color: Color.fromARGB(255, 236, 229, 221),
        ),
        child: FutureBuilder<dynamic>(
            future: futureCust,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.done) {
                if (snapshot.hasData) {
                  List<TargetClass> data = [];
                  final dataCust = snapshot.data ?? [];
                  if (isPresentCustomerWise) {
                    data =
                        List<TargetClass>.from(dataCust.map((e) => TargetClass(
                              iCode: e.customerCode ?? '',
                              iName: e.customerName ?? '',
                              month: e.month ?? '',
                              target: e.target ?? '',
                              achievement: e.achievement ?? '',
                            )));
                  } else {
                    // final dataBranch = snapshot.data ?? [];
                    data =
                        List<TargetClass>.from(dataCust.map((e) => TargetClass(
                              iCode: e.customerCode ?? '',
                              iName: e.customerName ?? '',
                              month: e.month ?? '',
                              target: e.previousTarget,
                              achievement: e.previousAchievement ?? '',
                            )));
                  }
                  // get unique months
                  final List<String> months =
                      data.map((e) => e.month).toSet().toList();
                  List<TargetClass> allData = [];
                  //  for each month get the all data
                  for (var month in months) {
                    final List<TargetClass> monthData = data
                        .where((element) => element.month == month)
                        .toList();
                    allData.add(TargetClass(
                      iCode: 'All',
                      iName: 'All',
                      target: monthData.fold(0.0, (previousValue, element) {
                        final sum =
                            previousValue + double.parse(element.target);
                        return sum;
                      }).toString(),
                      achievement: monthData
                          .fold(
                              0.0,
                              (previousValue, element) =>
                                  previousValue +
                                  double.parse(element.achievement))
                          .toString(),
                      month: month,
                    ));
                  }
                  // sort data by name
                  // data.sort((a, b) => a.iName.compareTo(b.iName));
                  data = allData + data;
                  WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
                    setState(() {
                      data = allData + data;
                    });
                  });

                  // get unique customer codes
                  final List<String> customerCodes =
                      data.map((e) => e.iCode).toSet().toList();
                  final List<TargetClass> selectedData = data
                      .where((element) => element.iCode == selectedICode)
                      .toList();
                  return Column(
                    children: [
                      Container(
                        // height: 50,
                        padding: const EdgeInsets.all(8.0),
                        width: double.infinity,
                        alignment: Alignment.center,
                        decoration: BoxDecoration(
                          color: Colors.grey[200],
                          borderRadius: BorderRadius.circular(5),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            const Text(
                              // isPresentCustomerWise
                              'Selected Customer:',
                              // : 'Selected Branch:',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(width: 10),
                            DropdownButton<String>(
                              value: selectedICode ?? 'All',
                              onChanged: (String? newValue) {
                                setState(() {
                                  selectedICode = newValue;
                                });
                              },
                              items: customerCodes
                                  .map<DropdownMenuItem<String>>(
                                      (String value) {
                                return DropdownMenuItem<String>(
                                  value: value,
                                  child: Text(data
                                      .where(
                                          (element) => element.iCode == value)
                                      .first
                                      .iName),
                                );
                              }).toList(),
                            ),
                          ],
                        ),
                      ),
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsets.only(
                            top: 8.0,
                            left: 50.0,
                            right: 8.0,
                            bottom: 8.0,
                          ),
                          child: AspectRatio(
                            aspectRatio: 5,
                            child: BarChart(
                              BarChartData(
                                barTouchData: BarTouchData(
                                  allowTouchBarBackDraw: false,
                                  touchCallback:
                                      (flTouchEvent, barTouchResponse) {
                                    print('Touch Callback');
                                    print(flTouchEvent.runtimeType.toString());
                                    if (flTouchEvent.runtimeType.toString() ==
                                        'FlTapUpEvent') {
                                      final int index = barTouchResponse
                                              ?.spot?.touchedBarGroupIndex ??
                                          selectedData.length - 1;
                                      final String month = data[index].month;
                                      final String label =
                                          '${isPresentCustomerWise ? 'Customer' : 'Branch'} wise Tar-vs-Achv for ${monthNumberToName(month)},2024';
                                      // go to target screen table
                                      Navigator.push(
                                        context,
                                        MaterialPageRoute(
                                          builder: (context) =>
                                              TargetScreenTable(
                                            title: label,
                                            data: selectedData[index].iCode ==
                                                    'All'
                                                ? data
                                                    .where((element) => (element
                                                                .month ==
                                                            month &&
                                                        element.iCode != 'All'))
                                                    .toList()
                                                : selectedData
                                                    .where((element) =>
                                                        element.month == month)
                                                    .toList(),
                                          ),
                                        ),
                                      );
                                    }
                                  },
                                  touchTooltipData: BarTouchTooltipData(
                                    getTooltipItem:
                                        (group, groupIndex, rod, rodIndex) {
                                      final String message = rodIndex == 0
                                          ? 'Target for ${monthNumberToName((groupIndex + 1).toString())}: ${rod.toY.toString()}'
                                          : 'Achievement for ${monthNumberToName((groupIndex + 1).toString())}: ${rod.toY.toString()}';
                                      return BarTooltipItem(
                                        // rod.toY.toString(),
                                        message,
                                        const TextStyle(
                                          color: Colors.white,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      );
                                    },
                                  ),
                                ),
                                titlesData: FlTitlesData(
                                  show: true,
                                  leftTitles: AxisTitles(
                                    axisNameSize: 0,
                                    // drawBelowEverything: true,
                                    sideTitles: SideTitles(
                                      showTitles: true,
                                      reservedSize: selectedData
                                              .fold(
                                                  '0.0',
                                                  (previousValue, element) => math
                                                      .max(
                                                          double.parse(element
                                                              .achievement),
                                                          double.parse(
                                                              element.target))
                                                      .toString())
                                              .length *
                                          20.0,
                                      getTitlesWidget: (value, meta) {
                                        return Text(
                                          '${value.toInt()} ',
                                          textAlign: TextAlign.left,
                                        );
                                      },
                                    ),
                                  ),
                                  bottomTitles: AxisTitles(
                                    sideTitles: SideTitles(
                                      showTitles: true,
                                      reservedSize: 36,
                                      getTitlesWidget: (value, meta) {
                                        final index = value.toInt();
                                        return SideTitleWidget(
                                          axisSide: meta.axisSide,
                                          child: Text(
                                            monthNumberToName(
                                              data[index].month,
                                            ),
                                            style: const TextStyle(
                                              fontSize: 12,
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                        );
                                      },
                                    ),
                                  ),
                                  rightTitles: const AxisTitles(),
                                  topTitles: const AxisTitles(),
                                ),
                                alignment: BarChartAlignment.spaceBetween,
                                borderData: FlBorderData(
                                  show: true,
                                  border: const Border.symmetric(
                                    horizontal: BorderSide(
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                                barGroups: selectedData
                                    .asMap()
                                    .entries
                                    .map(
                                      (e) => BarChartGroupData(
                                        x: e.key,
                                        barRods: [
                                          BarChartRodData(
                                            borderRadius:
                                                const BorderRadius.only(
                                              topLeft: Radius.circular(5),
                                              topRight: Radius.circular(5),
                                            ),
                                            width: 16,
                                            toY: double.parse(e.value.target),
                                            color: Colors.blue,
                                          ),
                                          BarChartRodData(
                                            borderRadius:
                                                const BorderRadius.only(
                                              topLeft: Radius.circular(5),
                                              topRight: Radius.circular(5),
                                            ),
                                            width: 16,
                                            toY: double.parse(
                                                e.value.achievement),
                                            color: Colors.green,
                                          ),
                                        ],
                                      ),
                                    )
                                    .toList(),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  );
                } else {
                  return const Center(
                    child: Text('No data found'),
                  );
                }
              } else {
                return const Center(
                  child: CircularProgressIndicator(),
                );
              }
            }),
      ),
    );
  }
}

class TargetClass {
  final String iCode;
  final String iName;
  final String month;
  final String target;
  final String achievement;

  TargetClass({
    required this.iCode,
    required this.iName,
    required this.month,
    required this.target,
    required this.achievement,
  });
}
