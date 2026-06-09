import 'package:flutter_svg/flutter_svg.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/db_setup/data_for_downloading_lead.dart';
import 'package:starsfa/db_setup/new_lead_generation_database.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/screens/lead_generation/lead_generation_report_activity_screen.dart';

class LeadGenerationGraphActivityScreen extends StatefulWidget {
  const LeadGenerationGraphActivityScreen({super.key});

  @override
  State<LeadGenerationGraphActivityScreen> createState() =>
      _LeadGenerationGraphActivityScreen();
}

class _LeadGenerationGraphActivityScreen
    extends State<LeadGenerationGraphActivityScreen> {
  bool _isLoading = false;
  String _leadCategory = "";

  int countLeadCreate = 0;
  String qtyLeadCreate = '0';

  int countLeadQualified = 0;
  String qtyLeadQualified = '0';

  int countQuotationCreation = 0;
  String qtyQuotationCreation = '0';

  int countQuotationApproved = 0;
  String qtyQuotationApproved = '0';

  int countQuotationSent = 0;
  String qtyQuotationSent = '0';

  int countPOReceived = 0;
  String qtyPOReceived = '0';

  int countLostOrder = 0;
  String qtyLostOrder = '0';

  int countContractCreation = 0;
  String qtyContractCreation = '0';

  int countSOCreated = 0;
  String qtySOCreated = '0';

  int countFulfilment = 0;
  String qtyFulfilment = '0';

  int countClosed = 0;
  String qtyClosed = '0';

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    final db = NewLeadGenerationDatabase();

    final dbCountleadcreate = await db.getCountLeadListMasterTableData(1, true);
    final dbQtyleadcreate = await db.getTotalQtyListMasterTableData(1, true);

    final dbCountleadqualified =
        await db.getCountLeadListMasterTableData(5, true);
    final dbQtyleadqualified = await db.getTotalQtyListMasterTableData(5, true);

    final dbCountquotationcreation =
        await db.getCountLeadListMasterTableData(6, true);
    final dbQtyquotationcreation =
        await db.getTotalQtyListMasterTableData(6, true);

    final dbCountquotationapproved =
        await db.getCountLeadListMasterTableData(10, true);
    final dbQtyquotationapproved =
        await db.getTotalQtyListMasterTableData(10, true);

    final dbCountquotationsent =
        await db.getCountLeadListMasterTableData(14, true);
    final dbQtyquotationsent =
        await db.getTotalQtyListMasterTableData(14, true);

    final dbCountporeceived =
        await db.getCountLeadListMasterTableData(15, true);
    final dbQtyporeceived = await db.getTotalQtyListMasterTableData(15, true);

    final dbCountlostorder =
        await db.getCountLeadListMasterTableData(12, false);
    final dbQtylostorder = await db.getTotalQtyListMasterTableData(12, false);

    final dbCountcontractcreation =
        await db.getCountLeadListMasterTableData(20, true);
    final dbQtycontractcreation =
        await db.getTotalQtyListMasterTableData(20, true);

    final dbCountsocreated =
        await db.getCountLeadListMasterTableData(21, false);
    final dbQtysocreated = await db.getTotalQtyListMasterTableData(21, false);

    final dbCountfulfilment =
        await db.getCountLeadListMasterTableData(21, false);
    final dbQtyfulfilment = await db.getTotalQtyListMasterTableData(21, false);

    final dbCountclosed = await db.getCountLeadListMasterTableData(21, false);
    final dbQtyclosed = await db.getTotalQtyListMasterTableData(21, false);

    setState(() {
      countLeadCreate = dbCountleadcreate;
      qtyLeadCreate = dbQtyleadcreate;

      countLeadQualified = dbCountleadqualified;
      qtyLeadQualified = dbQtyleadqualified;

      countQuotationCreation = dbCountquotationcreation;
      qtyQuotationCreation = dbQtyquotationcreation;

      countQuotationApproved = dbCountquotationapproved;
      qtyQuotationApproved = dbQtyquotationapproved;

      countQuotationSent = dbCountquotationsent;
      qtyQuotationSent = dbQtyquotationsent;

      countPOReceived = dbCountporeceived;
      qtyPOReceived = dbQtyporeceived;

      countLostOrder = dbCountlostorder;
      qtyLostOrder = dbQtylostorder;

      countContractCreation = dbCountcontractcreation;
      qtyContractCreation = dbQtycontractcreation;

      countSOCreated = dbCountsocreated;
      qtySOCreated = dbQtysocreated;

      countFulfilment = dbCountfulfilment;
      qtyFulfilment = dbQtyfulfilment;

      countClosed = dbCountclosed;
      qtyClosed = dbQtyclosed;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xfff4f6f9),
      appBar: AppBar(
        backgroundColor: Colors.red,
        title: const Text(
          "Lead Generation",
          style: TextStyle(color: Colors.white),
        ),
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            onPressed: () async {
              setState(() {
                _isLoading = true;
              });

              final downloader = DataForDownloadingLead();

              final success = await downloader.addAllFormDataForLead(
                empCode: 'E0555',
                baseUrl: '${AppWebService.sbDevUrl}',
              );

              if (success) {
                // ignore: avoid_print
                print('All data downloaded successfully');
                _loadData();
                setState(() {
                  _isLoading = false;
                });
              } else {
                // ignore: avoid_print
                print('Download failed');
                setState(() {
                  _isLoading = false;
                });
                // ignore: use_build_context_synchronously
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Download failed'),
                  ),
                );
              }
            },
            icon: const Icon(Icons.refresh, color: Colors.white),
          ),
        ],
      ),
      body: SafeArea(
          child: _isLoading
              ? const Center(child: CircularProgressIndicator())
              : Column(
                  children: [
                    const SizedBox(height: 15),
                    Expanded(
                      child: SingleChildScrollView(
                        child: Column(
                          children: [
                            Padding(
                              padding:
                                  const EdgeInsets.symmetric(horizontal: 10),
                              child: Column(
                                children: [
                                  Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 16),
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius: BorderRadius.circular(12),
                                        border: Border.all(
                                          color: const Color(0xFFFFBABE),
                                          width: 1.2,
                                        ),
                                      ),
                                      child: Column(
                                        children: [
                                          InkWell(
                                            onTap: () {
                                              setState(() {
                                                _leadCategory =
                                                    _leadCategory == 'hos'
                                                        ? ''
                                                        : 'hos';
                                              });
                                            },
                                            borderRadius:
                                                BorderRadius.circular(12),
                                            child: Container(
                                              height: 55,
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                      horizontal: 5),
                                              child: Row(
                                                mainAxisAlignment:
                                                    MainAxisAlignment
                                                        .spaceBetween,
                                                children: [
                                                  Column(
                                                    mainAxisAlignment:
                                                        MainAxisAlignment
                                                            .center,
                                                    crossAxisAlignment:
                                                        CrossAxisAlignment
                                                            .start,
                                                    children: [
                                                      Text(
                                                        'Lead Funnel Representation',
                                                        style: const TextStyle(
                                                          color:
                                                              Color(0xFFF45058),
                                                          fontSize: 16,
                                                          fontWeight:
                                                              FontWeight.w700,
                                                        ),
                                                      ),
                                                      Text(
                                                        '(Unique Lead Count)',
                                                        style: const TextStyle(
                                                          color:
                                                              Color(0xFF181A1E),
                                                          fontSize: 14,
                                                          fontWeight:
                                                              FontWeight.w400,
                                                        ),
                                                      ),
                                                    ],
                                                  ),
                                                  Icon(
                                                    Icons.keyboard_arrow_down,
                                                    color:
                                                        const Color(0xFFF45058),
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ),
                                          if (_leadCategory == 'hos') ...[
                                            SizedBox(height: 5),
                                            Column(
                                              mainAxisAlignment:
                                                  MainAxisAlignment.center,
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.center,
                                              children: [
                                                const SizedBox(height: 15),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_1.svg',
                                                  title: 'LEAD CREATE',
                                                  subtitle: 'By Sales Team',
                                                  title1:
                                                      '$countLeadCreate Leads',
                                                  value1: '$qtyLeadCreate',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 200,
                                                  height: 70,
                                                  count: 0,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title: 'LEAD CREATE',
                                                          statusCode: 1,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_2.svg',
                                                  title: 'LEAD QUALIFIED',
                                                  subtitle: 'By HOS',
                                                  title1:
                                                      '$countLeadQualified Leads',
                                                  value1: '$qtyLeadQualified',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 180,
                                                  height: 70,
                                                  count: 1,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title:
                                                              'LEAD QUALIFIED',
                                                          statusCode: 5,
                                                          isShowLeadStatus:
                                                              true,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_3.svg',
                                                  title: 'QUOTATION CREATION',
                                                  subtitle: 'By MIS',
                                                  title1:
                                                      '$countQuotationCreation Leads',
                                                  value1:
                                                      '$qtyQuotationCreation',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 160,
                                                  height: 70,
                                                  count: 2,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title:
                                                              'QUOTATION CREATION',
                                                          statusCode: 6,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_4.svg',
                                                  title: 'QUOTATION APPROVED',
                                                  subtitle: '',
                                                  title1:
                                                      '$countQuotationApproved Leads',
                                                  value1:
                                                      '$qtyQuotationApproved',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 140,
                                                  height: 70,
                                                  count: 3,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title:
                                                              'QUOTATION APPROVED',
                                                          statusCode: 10,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_5.svg',
                                                  title: 'QUOTATION SENT',
                                                  subtitle: '',
                                                  title1:
                                                      '$countQuotationSent Leads',
                                                  value1: '$qtyQuotationSent',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 120,
                                                  height: 70,
                                                  count: 4,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title:
                                                              'QUOTATION SENT',
                                                          statusCode: 14,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_6.svg',
                                                  title: 'CUSTOMER RESPONSE',
                                                  subtitle: 'Follow-Up By SO',
                                                  title1: 'PO $countPOReceived',
                                                  value1: '$qtyPOReceived',
                                                  title2:
                                                      'Lost $countLostOrder',
                                                  value2: '$qtyLostOrder',
                                                  isShow2ndLayout: true,
                                                  metricColor: Colors.green,
                                                  metricColor2: Colors.red,
                                                  width: 100,
                                                  height: 90,
                                                  count: 5,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title:
                                                              'CUSTOMER RESPONSE',
                                                          statusCode: 12,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost: true,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                              ],
                                            ),
                                            SizedBox(height: 10),
                                          ]
                                        ],
                                      )),
                                  SizedBox(height: 16),
                                  Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 16),
                                      decoration: BoxDecoration(
                                        color: Colors.white,
                                        borderRadius: BorderRadius.circular(12),
                                        border: Border.all(
                                          color: const Color(0xFFFFBABE),
                                          width: 1.2,
                                        ),
                                      ),
                                      child: Column(
                                        children: [
                                          InkWell(
                                            onTap: () {
                                              setState(() {
                                                _leadCategory =
                                                    _leadCategory == 'mis'
                                                        ? ''
                                                        : 'mis';
                                              });
                                            },
                                            borderRadius:
                                                BorderRadius.circular(12),
                                            child: Container(
                                              height: 55,
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                      horizontal: 5),
                                              child: Row(
                                                mainAxisAlignment:
                                                    MainAxisAlignment
                                                        .spaceBetween,
                                                children: [
                                                  Text(
                                                    'Contract Creation',
                                                    style: TextStyle(
                                                      color: const Color(
                                                          0xFFF45058),
                                                      fontSize: 16,
                                                      fontWeight:
                                                          FontWeight.w700,
                                                    ),
                                                  ),
                                                  Icon(
                                                    Icons.keyboard_arrow_down,
                                                    color:
                                                        const Color(0xFFF45058),
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ),
                                          if (_leadCategory == 'mis') ...[
                                            SizedBox(height: 5),
                                            Column(
                                              mainAxisAlignment:
                                                  MainAxisAlignment.center,
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.center,
                                              children: [
                                                const SizedBox(height: 15),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_7.svg',
                                                  title: 'CONTRACT CREATION',
                                                  subtitle: 'In SAP',
                                                  title1:
                                                      '$countContractCreation Leads',
                                                  value1:
                                                      '$qtyContractCreation',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 200,
                                                  height: 70,
                                                  count: 0,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title:
                                                              'CONTRACT CREATION',
                                                          statusCode: 20,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_8.svg',
                                                  title: 'SO CREATED',
                                                  subtitle: 'In SAP',
                                                  title1:
                                                      '$countSOCreated Leads',
                                                  value1: '$qtySOCreated',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 180,
                                                  height: 70,
                                                  count: 1,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title: 'SO CREATED',
                                                          statusCode: 21,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_9.svg',
                                                  title: 'FULFILMENT',
                                                  subtitle: 'By Logistic',
                                                  title1:
                                                      '$countFulfilment Leads',
                                                  value1: '$qtyFulfilment',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 160,
                                                  height: 70,
                                                  count: 2,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title: 'FULFILMENT',
                                                          statusCode: 21,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              false,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_10.svg',
                                                  title: 'CLOSED',
                                                  subtitle: '',
                                                  title1: '$countClosed Leads',
                                                  value1: '$qtyClosed',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 140,
                                                  height: 70,
                                                  count: 3,
                                                  onTap: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            const LeadGenerationReportActivityScreen(
                                                          title: 'CLOSED',
                                                          statusCode: 21,
                                                          isShowLeadStatus:
                                                              false,
                                                          isShowDateFilter:
                                                              true,
                                                          isShowPOandLost:
                                                              false,
                                                        ),
                                                      ),
                                                    );
                                                  },
                                                ),
                                                FunnelImageItem(
                                                  imagePath:
                                                      'assets/funnel/design_11.svg',
                                                  title: '',
                                                  subtitle: '',
                                                  title1: '',
                                                  value1: '',
                                                  title2: '',
                                                  value2: '',
                                                  isShow2ndLayout: false,
                                                  metricColor:
                                                      Colors.blueGrey.shade400,
                                                  width: 140,
                                                  height: 30,
                                                  count: 3,
                                                  onTap: () {},
                                                ),
                                              ],
                                            ),
                                            SizedBox(height: 10),
                                          ]
                                        ],
                                      )),
                                ],
                              ),
                            )
                          ],
                        ),
                      ),
                    ),
                  ],
                )),
    );
  }
}

class ReportButton extends StatelessWidget {
  final String title;
  final String count;
  final Color color;
  final Color bgColor;
  final VoidCallback onTap;

  const ReportButton({
    super.key,
    required this.title,
    required this.count,
    required this.color,
    required this.bgColor,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(10),
      onTap: onTap,
      child: Container(
        height: 48,
        padding: const EdgeInsets.symmetric(horizontal: 14),
        decoration: BoxDecoration(
          color: color,
          borderRadius: BorderRadius.circular(10),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              title,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(width: 10),

            /// Count Capsule
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: bgColor,
                border: Border.all(color: Colors.white),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                count,
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class FunnelImageItem extends StatelessWidget {
  final String imagePath;
  final String title1;
  final String title2;
  final String title;
  final String subtitle;
  final String value1;
  final String value2;
  final bool isShow2ndLayout;
  final Color metricColor;
  final Color metricColor2;
  final double width;
  final double height;
  final VoidCallback? onTap;
  final int count;

  const FunnelImageItem({
    super.key,
    required this.imagePath,
    required this.title1,
    required this.title,
    required this.subtitle,
    required this.value1,
    required this.metricColor,
    this.metricColor2 = Colors.red,
    required this.width,
    required this.height,
    required this.title2,
    required this.value2,
    required this.isShow2ndLayout,
    required this.count,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final double badgeWidth = 330 - width - 20 * count;

    Widget badge({
      required String label,
      required String value,
      required Color color,
    }) {
      return Container(
        width: badgeWidth,
        padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 5),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              // ignore: deprecated_member_use
              color: Colors.black.withOpacity(0.1),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Expanded(
              child: Text(
                label,
                style: const TextStyle(
                  color: Color(0xFF2C3E50),
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                ),
                overflow: TextOverflow.ellipsis,
              ),
            ),
            const SizedBox(width: 4),
            Text(
              value,
              style: TextStyle(
                color: color,
                fontSize: 11,
                fontWeight: FontWeight.w900,
              ),
            ),
          ],
        ),
      );
    }

    return GestureDetector(
      onTap: onTap,
      child: SizedBox(
        width: width + badgeWidth,
        height: height,
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // ─── LEFT: Funnel Slice ───────────────────────────────────
            SizedBox(
              width: width,
              height: height,
              child: Stack(
                alignment: Alignment.center,
                children: [
                  SvgPicture.asset(
                    imagePath,
                    width: width,
                    height: height,
                    fit: BoxFit.fill,
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          title,
                          textAlign: TextAlign.center,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 0.5,
                          ),
                        ),
                        if (subtitle.isNotEmpty)
                          Text(
                            subtitle,
                            textAlign: TextAlign.center,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              // ignore: deprecated_member_use
                              color: Colors.white.withOpacity(0.85),
                              fontSize: 10,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            // ─── RIGHT: Data Panel ────────────────────────────────────
            SizedBox(
              width: badgeWidth,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (title1 != '') ...{
                    badge(
                      label: title1,
                      value: value1,
                      color: metricColor,
                    )
                  },
                  if (isShow2ndLayout) ...[
                    const SizedBox(height: 6),
                    badge(
                      label: title2,
                      value: value2,
                      color: metricColor2,
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class EmployeeStatusCountListData {
  String? title;
  String? count;

  EmployeeStatusCountListData({this.title, this.count});

  factory EmployeeStatusCountListData.fromJson(Map<String, dynamic> json) {
    return EmployeeStatusCountListData(
        title: json['title']?.toString() ?? '',
        count: json['count']?.toString() ?? '');
  }

  Map<String, dynamic> toJson() {
    return {'title': title, 'count': count};
  }

  static Future<List<EmployeeStatusCountListData>> fetchDataFromApi(
      String empCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }

    String url = '${AppWebService.sbDevUrl}api/leadmaster/?emp_code=$empCode';

    // ignore: avoid_print
    print(url);
    final response = await http.get(Uri.parse(url));
    if (response.statusCode == 200) {
      // final jsonResponse = json.decode(response.body);
      // if (jsonResponse is List) {
      //   return jsonResponse
      //       .map((item) => EmployeeStatusCountListData.fromJson(item))
      //       .toList();
      // } else if (jsonResponse is Map<String, dynamic>) {
      //   return [EmployeeStatusCountListData.fromJson(jsonResponse)];
      // } else {
      //   return [];
      // }
      final List<Map<String, dynamic>> staticJson = [
        {"title": "Lead Created", "count": 100},
        {"title": "Quotation Requested", "count": 45},
        {"title": "Quotation Generated", "count": 15},
        {"title": "Approval Pending", "count": 20},
        {"title": "Approved", "count": 15},
        {"title": "Rejected", "count": 5}
      ];

      return staticJson
          .map((item) => EmployeeStatusCountListData.fromJson(item))
          .toList();
    } else {
      throw Exception('Failed to load item');
    }
  }
}
