import 'package:flutter/material.dart';
import 'package:starsfa/models/sis_summary_details.dart';
import 'package:starsfa/models/user_login_class.dart';

class SisSummaryScreen extends StatefulWidget {
  const SisSummaryScreen({super.key});

  @override
  State<SisSummaryScreen> createState() => _SisSummaryScreenState();
}

class _SisSummaryScreenState extends State<SisSummaryScreen> {
  String? selectedMonth;
  String? empName;
  Future<SisSummaryDetails> sisSummaryDetails() async {
    final localUser = await UserLoginClass.getLocalUser();
    setState(() {
      empName = localUser?.empName;
    });
    final sisSummary = await SisSummaryDetails.getSisSummaryDetails();
    return sisSummary;
  }

  Future<SisSummaryDetails>? sisSummaryDetailsData;

  @override
  void initState() {
    super.initState();
    sisSummaryDetailsData = sisSummaryDetails();
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
          'SIS Summary',
          style: TextStyle(color: Colors.white),
        ),
      ),
      body: Container(
        decoration: const BoxDecoration(
          color: Color.fromARGB(255, 236, 229, 221),
        ),
        child: FutureBuilder<SisSummaryDetails>(
          future: sisSummaryDetailsData,
          builder: (context, snapshot) {
            if (snapshot.hasData) {
              final SisSummaryDetails? sisSummary = snapshot.data;
              final List<DataValue>? dataValue = sisSummary?.dataValue;
              List<String> months =
                  dataValue?.map((e) => e.monthYear ?? '').toList() ?? [];

              final Map<String, int> monthMap = {
                'Jan': 1,
                'Feb': 2,
                'Mar': 3,
                'Apr': 4,
                'May': 5,
                'Jun': 6,
                'Jul': 7,
                'Aug': 8,
                'Sep': 9,
                'Oct': 10,
                'Nov': 11,
                'Dec': 12,
              };

              months.removeWhere((m) => m.isEmpty || !m.contains('-'));

              months.sort((a, b) {
                try {
                  final aSplit = a.split('-');
                  final bSplit = b.split('-');

                  if (aSplit.length != 2 || bSplit.length != 2) return 0;

                  final aMonth = aSplit[0];
                  final bMonth = bSplit[0];

                  final aYear = 2000 + int.parse(aSplit[1]);
                  final bYear = 2000 + int.parse(bSplit[1]);

                  final aMonthNum = monthMap[aMonth] ?? 0;
                  final bMonthNum = monthMap[bMonth] ?? 0;

                  if (aYear == bYear) {
                    return aMonthNum.compareTo(bMonthNum);
                  }
                  return aYear.compareTo(bYear);
                } catch (e) {
                  print('error happen $e');
                  return 0;
                }
              });
              return Column(children: <Widget>[
                // Select Month
                Container(
                  padding: const EdgeInsets.all(8.0),
                  margin: const EdgeInsets.all(8.0),
                  width: double.infinity,
                  alignment: Alignment.center,
                  decoration: BoxDecoration(
                    color: Colors.grey[200]?.withOpacity(0.5),
                    borderRadius: BorderRadius.circular(5),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text('Selected Month: ',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          )),
                      const SizedBox(width: 10),
                      DropdownButton<String>(
                        value: selectedMonth ?? months.last,
                        items: months.map((String value) {
                          return DropdownMenuItem<String>(
                            value: value,
                            child: Text(
                              value,
                              style: const TextStyle(fontSize: 16),
                            ),
                          );
                        }).toList(),
                        onChanged: (String? value) {
                          setState(() {
                            selectedMonth = value ?? '';
                          });
                        },
                      ),
                    ],
                  ),
                ),
                // Heading - Username, Value
                Container(
                  padding: const EdgeInsets.all(8.0),
                  margin: const EdgeInsets.all(8.0),
                  width: double.infinity,
                  alignment: Alignment.center,
                  decoration: BoxDecoration(
                    color: Colors.grey[200]?.withOpacity(0.5),
                    borderRadius: BorderRadius.circular(5),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Flexible(
                        flex: 8,
                        child: Text(
                          empName ?? '',
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                          textAlign: TextAlign.center,
                        ),
                      ),
                      const Flexible(
                        flex: 2,
                        child: Text(
                          'Value',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                // SIS Summary Details
                Expanded(
                  child: SingleChildScrollView(
                    scrollDirection: Axis.vertical,
                    child: Column(
                      children: [
                        // sales_volume
                        SisSummaryDataValue(
                          dataValues: dataValue ?? [],
                          selectedMonth: selectedMonth ?? months.last,
                          labelDetail: 'sales_volume_MT',
                          detail: 'sales_volume',
                        ),
                        // monthly_unique_visit
                        SisSummaryDataValue(
                          dataValues: dataValue ?? [],
                          selectedMonth: selectedMonth ?? months.last,
                          labelDetail: 'monthly_unique_visit',
                          detail: 'monthly_unique_visit',
                        ),
                        // dealer_appointment
                        SisSummaryDataValue(
                          dataValues: dataValue ?? [],
                          selectedMonth: selectedMonth ?? months.last,
                          labelDetail: 'dealer_appointment',
                          detail: 'dealer_appointment',
                        ),
                        // active_dealer_count
                        SisSummaryDataValue(
                          dataValues: dataValue ?? [],
                          selectedMonth: selectedMonth ?? months.last,
                          labelDetail: 'active_dealer_count',
                          detail: 'active_dealer_count',
                        ),
                        // paramiter_five
                        SisSummaryDataValue(
                          dataValues: dataValue ?? [],
                          selectedMonth: selectedMonth ?? months.last,
                          labelDetail: 'paramiter_five',
                          detail: 'five',
                        ),
                        // paramiter_six
                        SisSummaryDataValue(
                          dataValues: dataValue ?? [],
                          selectedMonth: selectedMonth ?? months.last,
                          labelDetail: 'paramiter_six',
                          detail: 'six',
                        ),
                        // paramiter_six
                        SisSummaryDataValue(
                          dataValues: dataValue ?? [],
                          selectedMonth: selectedMonth ?? months.last,
                          labelDetail: 'paramiter_seven',
                          detail: 'seven',
                        ),
                        // extra details
                        SisSummaryDataValueExtra(
                          dataValues: dataValue ?? [],
                          selectedMonth: selectedMonth ?? months.last,
                        ),
                      ],
                    ),
                  ),
                ),
              ]);
            } else if (snapshot.hasError) {
              return Text('${snapshot.error} Hello world');
            }
            return const Center(
                child: CircularProgressIndicator(
              valueColor: AlwaysStoppedAnimation<Color>(Colors.black),
            ));
          },
        ),
      ),
    );
  }
}

class SisSummaryDataValue extends StatelessWidget {
  const SisSummaryDataValue({
    Key? key,
    required this.dataValues,
    required this.selectedMonth,
    required this.detail,
    required this.labelDetail,
  }) : super(key: key);

  final List<DataValue> dataValues;
  final String? selectedMonth;
  final String labelDetail;
  final String detail;

  @override
  Widget build(BuildContext context) {
    final int index =
        dataValues.indexWhere((element) => element.monthYear == selectedMonth);
    final DataValue dataValue = dataValues[index];
    final Map<String, String> values = dataValue.toJson();
    final String label = values[labelDetail] ?? '';
    final String tgt = values['${detail}_TGT'] ?? '';
    final String ach = values['${detail}_ACH'] ?? '';
    final String achPercent = values['${detail}_ACH_percent'] ?? '';
    final String wgtPercent = values['${detail}_WGT_percent'] ?? '';
    final String scorePercent = values['${detail}_SCORE_percent'] ?? '';
    final List<String> details = [
      'TGT',
      'ACH',
      'SIS SLAB (%)',
      'WGT (%)',
      'SCORE (%)',
    ];
    final List<String> sixDetails = [
      'Active Dealer% Last Month',
      'Active Dealer% Current Month',
      'Increase (%)',
      'WGT (%)',
      'SCORE (%)',
    ];
    return (label.isEmpty || label == 'null')
        ? Container()
        : Container(
            padding: const EdgeInsets.all(8.0),
            margin: const EdgeInsets.all(8.0),
            width: double.infinity,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              color: Colors.grey[200]?.withOpacity(0.5),
              borderRadius: BorderRadius.circular(5),
            ),
            child: Column(
              children: [
                Text(
                  label,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const Divider(),
                const SizedBox(height: 10),
                ...details
                    .map((e) => Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              (detail == 'six')
                                  ? sixDetails[details.indexOf(e)]
                                  : e,
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              e == 'TGT'
                                  ? tgt
                                  : e == 'ACH'
                                      ? ach
                                      : e == 'SIS SLAB (%)'
                                          ? achPercent
                                          : e == 'WGT (%)'
                                              ? wgtPercent
                                              : e == 'SCORE (%)'
                                                  ? scorePercent
                                                  : '',
                              style: const TextStyle(
                                fontSize: 16,
                              ),
                            ),
                          ],
                        ))
                    .toList(),
              ],
            ),
          );
  }
}

class SisSummaryDataValueExtra extends StatelessWidget {
  const SisSummaryDataValueExtra(
      {Key? key, required this.dataValues, required this.selectedMonth})
      : super(key: key);

  final List<DataValue> dataValues;
  final String? selectedMonth;

  @override
  Widget build(BuildContext context) {
    final int index =
        dataValues.indexWhere((element) => element.monthYear == selectedMonth);
    final DataValue dataValue = dataValues[index];
    final Map<String, String> values = dataValue.toJson();
    // earning_score_percent
    final String earningScorePercent = values['earning_score_percent'] ?? '';
    // penalty_percent
    final String penaltyPercent = values['penalty_percent'] ?? '';
    // final_score_percent
    final String finalScorePercent = values['final_score_percent'] ?? '';
    // OTSI
    final String otsi = values['OTSI'] ?? '';
    // SIS_earning_month
    final String sisEarningMonth = values['SIS_earning_month'] ?? '';
    // remarks
    final String remarks = values['remarks'] ?? '';
    final List<String> details = [
      'Earning Score (%)',
      'Penalty (%)',
      'Final Score (%)',
      'OTSI (Rs.)',
      'SIS Earning for the Month',
      'Remarks',
    ];
    return Container(
      padding: const EdgeInsets.all(8.0),
      margin: const EdgeInsets.all(8.0),
      width: double.infinity,
      alignment: Alignment.center,
      decoration: BoxDecoration(
        color: Colors.grey[200]?.withOpacity(0.5),
        borderRadius: BorderRadius.circular(5),
      ),
      child: Column(
        children: [
          // const Text(
          //   'Extra Details',
          //   style: TextStyle(
          //     fontSize: 16,
          //     fontWeight: FontWeight.bold,
          //   ),
          // ),
          // const Divider(),
          // const SizedBox(height: 10),
          ...details
              .map((e) => Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        e,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Text(
                        e == 'Earning Score (%)'
                            ? earningScorePercent
                            : e == 'Penalty (%)'
                                ? penaltyPercent
                                : e == 'Final Score (%)'
                                    ? finalScorePercent
                                    : e == 'OTSI (Rs.)'
                                        ? otsi
                                        : e == 'SIS Earning for the Month'
                                            ? sisEarningMonth
                                            : e == 'Remarks'
                                                ? remarks
                                                : '',
                        style: const TextStyle(
                          fontSize: 16,
                        ),
                      ),
                    ],
                  ))
              .toList(),
        ],
      ),
    );
  }
}
