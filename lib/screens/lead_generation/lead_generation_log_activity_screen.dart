import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:starsfa/db_setup/new_lead_generation_database.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';

class LeadGenerationLogActivityScreen extends StatefulWidget {
  final String leadId;
  final LeadListMasterTableDataSet siteLeadData;

  const LeadGenerationLogActivityScreen(
      {super.key, required this.leadId, required this.siteLeadData});

  @override
  State<LeadGenerationLogActivityScreen> createState() =>
      _LeadGenerationLogActivityScreenState();
}

class _LeadGenerationLogActivityScreenState
    extends State<LeadGenerationLogActivityScreen> {
  bool _isLoading = false;
  List<LogReportList> dataSet = [];

  String _soldToPartyName = '';
  String _shipToPartyName = '';
  String _closedDate = '---';

  @override
  void initState() {
    super.initState();
    functionForDataCollect();
    _loadResolvedData();
  }

  Future<void> _loadResolvedData() async {
    setState(() => _isLoading = true);
    try {
      final db = NewLeadGenerationDatabase();
      final data = widget.siteLeadData;
      // Resolve sold-to party
      if (data.soldToParty != null && data.soldToParty!.isNotEmpty) {
        final sold = await db.getCustomerDetails(data.soldToParty!);
        _soldToPartyName = sold.custName ?? '';
      }
      // Resolve ship-to party
      if (data.shipToParty != null && data.shipToParty!.isNotEmpty) {
        final ship = await db.getCustomerDetails(data.shipToParty!);
        _shipToPartyName = ship.custName ?? '';
      }
    } catch (e) {
      print('_loadResolvedData error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> functionForDataCollect() async {
    setState(() => _isLoading = true);
    final result = await LogReportList.fetchLogDataAgainstLeadId(widget.leadId);
    // final result =
    //     await LogReportList.fetchLogDataAgainstLeadId('L05552604180108');

    for (int i = 0; i < result.length; i++) {
      if (result[i].statusCode == 4 ||
          result[i].statusCode == 9 ||
          result[i].statusCode == 12 ||
          result[i].statusCode == 16) {
        setState(() {
          _closedDate = formatDate(result[i].dateTime ?? '');
        });
      }
    }

    setState(() {
      dataSet = result;
      _isLoading = false;
    });
  }

  String formatDate(String utcDate) {
    DateTime utcTime = DateTime.parse(utcDate); // parsed as UTC
    DateTime localTime = utcTime.toLocal(); // convert to local

    return DateFormat('dd-MM-yyyy hh:mm a').format(localTime);
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      child: Stack(
        children: [
          Scaffold(
            appBar: AppBar(
              leading: IconButton(
                icon: const Icon(Icons.arrow_back, color: Colors.white),
                onPressed: () => Navigator.of(context).pop(),
              ),
              backgroundColor: Colors.red,
              title: const Text(
                'Lead Activity Report',
                style: TextStyle(color: Colors.white),
              ),
            ),
            body: SafeArea(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Container(
                    child: SingleChildScrollView(
                  child: Column(
                    mainAxisSize: MainAxisSize.max,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // ── Red header ───────────────────────────────────────
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 14),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              // ignore: deprecated_member_use
                              color: Colors.black.withOpacity(0.08),
                              blurRadius: 8,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  flex: 1,
                                  child: Text(
                                    'Lead Id',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 2,
                                  child: Text(
                                    widget.leadId,
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 5),
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  flex: 1,
                                  child: Text(
                                    'Sold to Party',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 2,
                                  child: Text(
                                    _soldToPartyName,
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 5),
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  flex: 1,
                                  child: Text(
                                    'Ship to Party',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 2,
                                  child: Text(
                                    _shipToPartyName,
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 5),
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  flex: 1,
                                  child: Text(
                                    'Contact Person',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 2,
                                  child: Text(
                                    widget.siteLeadData.contactPersonName ?? '',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 5),
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  flex: 1,
                                  child: Text(
                                    'Contact No',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 2,
                                  child: Text(
                                    widget.siteLeadData.contactNumber ?? '',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 5),
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  flex: 1,
                                  child: Text(
                                    'Lead Qty',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 2,
                                  child: Text(
                                    widget.siteLeadData.qtyReq != null &&
                                            widget
                                                .siteLeadData.qtyReq!.isNotEmpty
                                        ? "${widget.siteLeadData.qtyReq} MT"
                                        : "",
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 5),
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  flex: 1,
                                  child: Text(
                                    'Create Date',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 2,
                                  child: Text(
                                    formatDate(
                                        widget.siteLeadData.downloadTime ?? ''),
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 5),
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  flex: 1,
                                  child: Text(
                                    'Closed Date',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.grey,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ),
                                Expanded(
                                  flex: 2,
                                  child: Text(
                                    _closedDate,
                                    style: const TextStyle(
                                      fontSize: 13,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 8),
                      // ── Rows ─────────────────────────────────────────────

                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 14),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              // ignore: deprecated_member_use
                              color: Colors.black.withOpacity(0.08),
                              blurRadius: 8,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: ListView.separated(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: dataSet.length,
                          separatorBuilder: (_, __) => Divider(
                            height: 1,
                            color: Colors.grey.shade200,
                            indent: 16,
                            endIndent: 16,
                          ),
                          itemBuilder: (context, index) {
                            final item = dataSet[index];
                            return Padding(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 16, vertical: 12),
                              child: Row(
                                children: [
                                  // Activity label
                                  Expanded(
                                    flex: 4,
                                    child: Text(
                                      item.label ?? '',
                                      style: const TextStyle(
                                        fontSize: 13,
                                        color: Colors.black87,
                                      ),
                                    ),
                                  ),
                                  // Date Time
                                  Text(
                                    formatDate(item.dateTime ?? ''),
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: Colors.grey.shade700,
                                    ),
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                      ),
                    ],
                  ),
                )),
              ),
            ),
          ),
          if (_isLoading)
            Container(
              // ignore: deprecated_member_use
              color: Colors.black.withOpacity(0.3),
              child: const Center(child: CircularProgressIndicator()),
            ),
        ],
      ),
    );
  }
}

class LogReportList {
  final String? label;
  final String? dateTime;
  final int? statusCode;

  LogReportList({this.label, this.dateTime, this.statusCode});

  factory LogReportList.fromJson(Map<String, dynamic> json) {
    switch (json['lead_quotation_status']) {
      case 1:
        if (json['changes']['lead_generation_id'] == null) {
          return LogReportList(
              label: 'SO Lead Update',
              dateTime: json['created_at'],
              statusCode: 1);
        } else {
          return LogReportList(
              label: 'Lead Create',
              dateTime: json['created_at'],
              statusCode: 1);
        }
      case 2:
        return LogReportList(
            label: 'HOS Hold the Lead',
            dateTime: json['created_at'],
            statusCode: 2);
      case 3:
        return LogReportList(
            label: 'HOS send for Revision',
            dateTime: json['created_at'],
            statusCode: 3);
      case 4:
        return LogReportList(
            label: 'HOS Reject the Lead',
            dateTime: json['created_at'],
            statusCode: 4);
      case 5:
        return LogReportList(
            label: 'HOS Approved', dateTime: json['created_at'], statusCode: 5);
      case 6:
        return LogReportList(
            label: 'MIS Create Price Approval',
            dateTime: json['created_at'],
            statusCode: 6);
      case 7:
        return LogReportList(
            label: 'MIS Send to COO',
            dateTime: json['created_at'],
            statusCode: 7);
      case 8:
        return LogReportList(
            label: 'COO send for Revision',
            dateTime: json['created_at'],
            statusCode: 8);
      case 9:
        return LogReportList(
            label: 'COO Reject the Lead',
            dateTime: json['created_at'],
            statusCode: 9);
      case 10:
        return LogReportList(
            label: 'COO Approved',
            dateTime: json['created_at'],
            statusCode: 10);
      case 11:
        return LogReportList(
            label: 'MIS Send the Lead to SAP',
            dateTime: json['created_at'],
            statusCode: 11);
      case 12:
        return LogReportList(
            label: 'Lost Order', dateTime: json['created_at'], statusCode: 12);
      case 13:
        return LogReportList(
            label: 'SAP create Quotation',
            dateTime: json['created_at'],
            statusCode: 13);
      case 14:
        return LogReportList(
            label: 'SAP Received PO from Customer',
            dateTime: json['created_at'],
            statusCode: 14);
      case 15:
        return LogReportList(
            label: 'SAP Create Contract',
            dateTime: json['created_at'],
            statusCode: 15);
      case 16:
        return LogReportList(
            label: 'SAP Create SO',
            dateTime: json['created_at'],
            statusCode: 16);
    }
    return LogReportList(label: '', dateTime: '', statusCode: 0);
  }

  Map<String, dynamic> toJson() {
    return {'label': label, 'dateTime': dateTime, 'statusCode': statusCode};
  }

  static Future<List<LogReportList>> fetchLogDataAgainstLeadId(
      String leadId) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }

    String url =
        '${AppWebService.sbDevUrl}api/leadmaster_log/?lead_generation_id=$leadId&ordering=created_at';
    final response = await http.get(Uri.parse(url));
    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse is List) {
        return jsonResponse
            .map((item) => LogReportList.fromJson(item))
            .toList();
      } else if (jsonResponse is Map<String, dynamic>) {
        return [LogReportList.fromJson(jsonResponse)];
      } else {
        return [];
      }
    } else {
      throw Exception('Failed to load log list');
    }
  }
}
