import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:starsfa/models/sis_summary_details_bd.dart';

class SisSummaryBDScreen extends StatefulWidget {
  const SisSummaryBDScreen({super.key});

  @override
  State<SisSummaryBDScreen> createState() => _SisSummaryBDScreenState();
}

class _SisSummaryBDScreenState extends State<SisSummaryBDScreen> {
  final Future<SisSummaryDetailsBD> bdSisSummaryDetailsBD =
      SisSummaryDetailsBD.getSisSummaryDetailsBD();
  SisSummaryDetailsBD? bdSisSummaryHeaderBD;
  String? selectedMonth;

  Future<SisSummaryDetailsBD> getSisSummaryDetailsBD() async {
    bdSisSummaryHeaderBD = await SisSummaryDetailsBD.getSisSummaryHeadersBD();
    log('bdSisSummaryHeaderBD: ${bdSisSummaryHeaderBD?.toJson()}');
    return SisSummaryDetailsBD.getSisSummaryDetailsBD();
  }

  List<String> parameters = [
    'parameter_1',
    'parameter_2',
    'parameter_3',
    'parameter_4',
    'paramiter_five',
    'paramiter_six',
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('BD SIS Summary'),
      ),
      body: FutureBuilder<SisSummaryDetailsBD>(
        future: getSisSummaryDetailsBD(),
        builder: (context, snapshot) {
          if (snapshot.hasData) {
            final SisSummaryDetailsBD? sisSummary = snapshot.data;
            final List<DatavalueBD> dataValue = sisSummary?.datavalue ?? [];
            final List<String> months =
                dataValue.map((e) => e.monthYear ?? '').toList();
            if (dataValue.isEmpty) {
              return const Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text('No Data Found', style: TextStyle(fontSize: 20)),
                  ],
                ),
              );
            } else {
              return Column(children: <Widget>[
                // Select Month
                Container(
                  padding: const EdgeInsets.all(8.0),
                  margin: const EdgeInsets.all(8.0),
                  width: double.infinity,
                  alignment: Alignment.center,
                  decoration: BoxDecoration(
                    color: Colors.grey[200],
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
                        value: selectedMonth ?? (months.last),
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
                // SIS Summary Details
                Expanded(
                  child: SingleChildScrollView(
                    scrollDirection: Axis.vertical,
                    child: Column(
                      children: [
                        ...parameters.map((e) => SisSummaryDataValue(
                              dataValues: dataValue,
                              selectedMonth: selectedMonth ?? months.last,
                              labelDetail: e,
                              detaiLsLabel:
                                  bdSisSummaryHeaderBD ?? SisSummaryDetailsBD(),
                            )),
                        // // parameter_1
                        // SisSummaryDataValue(
                        //   dataValues: dataValue,
                        //   selectedMonth: selectedMonth ?? months.last,
                        //   labelDetail: 'parameter_1',

                        // ),
                        // // parameter_2
                        // SisSummaryDataValue(
                        //   dataValues: dataValue,
                        //   selectedMonth: selectedMonth ?? months.last,
                        //   labelDetail: 'parameter_2',
                        // ),
                        // // parameter_3
                        // SisSummaryDataValue(
                        //   dataValues: dataValue,
                        //   selectedMonth: selectedMonth ?? months.last,
                        //   labelDetail: 'parameter_3',
                        // ),
                        // // parameter_4
                        // SisSummaryDataValue(
                        //   dataValues: dataValue,
                        //   selectedMonth: selectedMonth ?? months.last,
                        //   labelDetail: 'parameter_4',
                        // ),
                        // // paramiter_five
                        // SisSummaryDataValue(
                        //   dataValues: dataValue,
                        //   selectedMonth: selectedMonth ?? months.last,
                        //   labelDetail: 'paramiter_five',
                        // ),
                        // // paramiter_six
                        // SisSummaryDataValue(
                        //   dataValues: dataValue,
                        //   selectedMonth: selectedMonth ?? months.last,
                        //   labelDetail: 'paramiter_six',
                        // ),
                        // extra details
                        SisSummaryDataValueExtra(
                          dataValues: dataValue,
                          selectedMonth: selectedMonth ?? months.last,
                        ),
                      ],
                    ),
                  ),
                ),
              ]);
            }
          } else if (snapshot.hasError) {
            return Text('${snapshot.error}');
          }
          return const Center(
              child: CircularProgressIndicator(
            valueColor: AlwaysStoppedAnimation<Color>(Colors.black),
          ));
        },
      ),
    );
  }
}

class SisSummaryDataValue extends StatelessWidget {
  const SisSummaryDataValue({
    Key? key,
    required this.dataValues,
    required this.selectedMonth,
    required this.labelDetail,
    required this.detaiLsLabel,
  }) : super(key: key);

  final List<DatavalueBD> dataValues;
  final String? selectedMonth;
  final String labelDetail;
  final SisSummaryDetailsBD detaiLsLabel;

  @override
  Widget build(BuildContext context) {
    final int index =
        dataValues.indexWhere((element) => element.monthYear == selectedMonth);
    final DatavalueBD dataValue = dataValues[index];
    final DatavalueBD headers = detaiLsLabel.datavalue
            ?.where((e) => e.headerId == dataValue.headerId)
            .first ??
        DatavalueBD();
    final Map<String, String> values = dataValue.toJson();
    final String label = values[labelDetail] ?? '';
    final Map<String, List<String>> details = {
      'parameter_1': [
        "sales_volume_TGT",
        "sales_volume_ACTUAL",
        "sis_slab_percent",
        "premium_sales_conversion_target",
        "sales_volume_SCORE_percent",
      ],
      'parameter_2': [
        "monthly_unique_visit_TGT",
        "monthly_unique_visit_ACH",
        "activities_sis_slab_percent",
        "activities",
        "monthly_unique_visit_SCORE_percent",
      ],
      'parameter_3': [
        "dealer_appointment_ACTUAL",
        "dealer_appointment_sis_slab_percent",
        "influencer_registration",
        "dealer_appointment_SCORE_percent",
      ],
      'parameter_4': [
        "active_dealer_count_TGT",
        "active_dealer_count_ACH",
        "active_dealer_count_sis_slab_percent",
        "active_dealer_growth",
        "active_dealer_count_SCORE_percent",
      ],
      'paramiter_five': [
        "five_TGT",
        "five_ACH",
        "five_sis_slab_percent",
        "active_influencer_growth",
        "five_SCORE_percent",
      ],
      'paramiter_six': [
        "six_ACTUAL",
        "six_slab_percent",
        "six_SCORE_percent",
      ],
    };
    // final Map<String, List<String>> detailsLabel = {
    //   'parameter_1': [
    //     "Target",
    //     "Actual",
    //     "SIS Slab (%)",
    //     "Premium Sales Conversion Target",
    //     "Score (%)",
    //   ],
    //   'parameter_2': [
    //     "Target",
    //     "Actual",
    //     "SIS Slab (%)",
    //     "Activities",
    //     "Score (%)",
    //   ],
    //   'parameter_3': [
    //     "Actual",
    //     "SIS Slab (%)",
    //     "Influencer Registration",
    //     "Score (%)",
    //   ],
    //   'parameter_4': [
    //     "Active Dealer Count Target",
    //     "Active Dealer Count Actual",
    //     "SIS Slab (%)",
    //     "Active Dealer Growth",
    //     "Score (%)",
    //   ],
    //   'paramiter_five': [
    //     "Target",
    //     "Actual",
    //     "SIS Slab (%)",
    //     "Active Influencer Growth",
    //     "Score (%)",
    //   ],
    //   'paramiter_six': [
    //     "Actual",
    //     "SIS Slab (%)",
    //     "Score (%)",
    //   ],
    // };
    return (label.isEmpty)
        ? Container()
        : Container(
            padding: const EdgeInsets.all(8.0),
            margin: const EdgeInsets.all(8.0),
            width: double.infinity,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              color: Colors.grey[200],
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
                ...details[labelDetail]!
                    .map((e) => Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              headers.toJson()[e] ?? '',
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            Text(
                              values[e] ?? '',
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

  final List<DatavalueBD> dataValues;
  final String? selectedMonth;

  @override
  Widget build(BuildContext context) {
    final int index =
        dataValues.indexWhere((element) => element.monthYear == selectedMonth);
    final DatavalueBD dataValue = dataValues[index];
    final Map<String, String> values = dataValue.toJson();
    // earning_score_percent
    final String earningScorePercent = values['earning_score_percent'] ?? '';
    // penalty_percent
    final String penaltyPercent = values['penalty_percent'] ?? '';
    // final_score_percent
    final String finalScorePercent = values['final_score_percent'] ?? '';
    // remarks
    final String remarks = values['remarks'] ?? '';
    final List<String> details = [
      'Earning Score (%)',
      'Penalty (%)',
      'Final Score (%)',
      'Remarks',
    ];
    return Container(
      padding: const EdgeInsets.all(8.0),
      margin: const EdgeInsets.all(8.0),
      width: double.infinity,
      alignment: Alignment.center,
      decoration: BoxDecoration(
        color: Colors.grey[200],
        borderRadius: BorderRadius.circular(5),
      ),
      child: Column(
        children: [
          const Text(
            'Extra Details',
            style: TextStyle(
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
