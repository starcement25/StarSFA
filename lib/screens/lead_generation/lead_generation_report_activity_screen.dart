import 'package:flutter/material.dart';
import 'package:starsfa/db_setup/new_lead_generation_database.dart';
import 'package:starsfa/log/lead_status_and_color_code.dart';
import 'package:starsfa/screens/lead_generation/lead_generation_log_activity_screen.dart';
import 'package:starsfa/screens/lead_generation/lead_generation_report_details_activity_screen.dart';

class LeadGenerationReportActivityScreen extends StatefulWidget {
  final String title;
  final int statusCode;
  final bool isShowLeadStatus;
  final bool isShowDateFilter;
  final bool isShowPOandLost;

  const LeadGenerationReportActivityScreen({
    super.key,
    required this.title,
    required this.statusCode,
    required this.isShowLeadStatus,
    required this.isShowDateFilter,
    required this.isShowPOandLost,
  });

  @override
  State<LeadGenerationReportActivityScreen> createState() =>
      _LeadGenerationReportActivityScreenState();
}

// ── Enriched model ─────────────────────────────────────────────────────────────
class _EnrichedLead {
  final LeadListMasterTableDataSet raw;
  final String soldToPartyName;
  final String shipToPartyName;
  final String leadStatusLabel;
  final Color leadStatusColor;

  _EnrichedLead({
    required this.raw,
    required this.soldToPartyName,
    required this.shipToPartyName,
    required this.leadStatusLabel,
    required this.leadStatusColor,
  });
}

class _LeadGenerationReportActivityScreenState
    extends State<LeadGenerationReportActivityScreen> {
  bool _isLoading = false;
  List<_EnrichedLead> _enrichedList = [];
  List<_EnrichedLead> _showEnrichedList = [];
  int _selectedTab = 0;
  int _hotLeadCount = 0;
  int _warmLeadCount = 0;
  int _coldLeadCount = 0;
  int _poReceivedLeadCount = 0;
  int _lostOrderLeadCount = 0;
  int _totalLeadCount = 0;
  int _last30DaysCount = 0;
  int _last3MonthsCount = 0;
  int _ytdCount = 0;
  DateTime? _startDate;
  DateTime? _endDate;

  TextEditingController lostLeadReasonController = TextEditingController();
  String? leadId = '';

  @override
  void initState() {
    super.initState();
    if (!widget.isShowLeadStatus &&
        !widget.isShowDateFilter &&
        !widget.isShowPOandLost) {
      _fetchLeadData();
    }
    if (widget.isShowLeadStatus) {
      _fetchLeadDataLeadStatus();
    }
    if (widget.isShowPOandLost) {
      _fetchLeadDataCustomerResponse();
    }
    if (widget.isShowDateFilter) {
      _fetchLeadDataDateFilter();
    }
  }

  // ── Fetch & enrich ─────────────────────────────────────────────────────────
  Future<void> _fetchLeadData() async {
    setState(() => _isLoading = true);
    try {
      final db = NewLeadGenerationDatabase();
      final leads = await db.getAllLeadListMasterTableData(widget.statusCode);
      final List<_EnrichedLead> enriched = [];

      for (final lead in leads) {
        final soldCustomer =
            lead.soldToParty != null && lead.soldToParty!.isNotEmpty
                ? await db.getCustomerDetails(lead.soldToParty!)
                : null;

        final shipCustomer =
            lead.shipToParty != null && lead.shipToParty!.isNotEmpty
                ? await db.getCustomerDetails(lead.shipToParty!)
                : null;

        final status =
            LeadStatusAndColorCode.resolveStatus(lead.leadQuotationStatus);

        enriched.add(_EnrichedLead(
          raw: lead,
          soldToPartyName: soldCustomer?.custName ?? lead.soldToParty ?? 'N/A',
          shipToPartyName: shipCustomer?.custName ?? lead.shipToParty ?? 'N/A',
          leadStatusLabel: status.label,
          leadStatusColor: status.color,
        ));
      }

      setState(() {
        _enrichedList = enriched;
        _showEnrichedList = enriched;
      });
    } catch (e) {
      print('_fetchLeadData error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _fetchLeadDataLeadStatus() async {
    setState(() => _isLoading = true);
    try {
      final db = NewLeadGenerationDatabase();
      final leads = await db.getAllLeadListMasterTableData(widget.statusCode);
      final List<_EnrichedLead> enriched = [];
      final List<_EnrichedLead> filterEnriched = [];

      for (final lead in leads) {
        final soldCustomer =
            lead.soldToParty != null && lead.soldToParty!.isNotEmpty
                ? await db.getCustomerDetails(lead.soldToParty!)
                : null;

        final shipCustomer =
            lead.shipToParty != null && lead.shipToParty!.isNotEmpty
                ? await db.getCustomerDetails(lead.shipToParty!)
                : null;

        final status =
            LeadStatusAndColorCode.resolveStatus(lead.leadQuotationStatus);

        enriched.add(_EnrichedLead(
          raw: lead,
          soldToPartyName: soldCustomer?.custName ?? lead.soldToParty ?? 'N/A',
          shipToPartyName: shipCustomer?.custName ?? lead.shipToParty ?? 'N/A',
          leadStatusLabel: status.label,
          leadStatusColor: status.color,
        ));
        if (lead.leadStatus?.toLowerCase() == 'hot') {
          _hotLeadCount++;
          filterEnriched.add(_EnrichedLead(
            raw: lead,
            soldToPartyName:
                soldCustomer?.custName ?? lead.soldToParty ?? 'N/A',
            shipToPartyName:
                shipCustomer?.custName ?? lead.shipToParty ?? 'N/A',
            leadStatusLabel: status.label,
            leadStatusColor: status.color,
          ));
        }
        if (lead.leadStatus?.toLowerCase() == 'warm') {
          _warmLeadCount++;
        }
        if (lead.leadStatus?.toLowerCase() == 'cold') {
          _coldLeadCount++;
        }
      }

      setState(() {
        _enrichedList = enriched;
        _showEnrichedList = filterEnriched;
      });
    } catch (e) {
      print('_fetchLeadData error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _fetchLeadDataCustomerResponse() async {
    setState(() => _isLoading = true);
    try {
      final db = NewLeadGenerationDatabase();
      final leads = await db.getAllLeadListMasterTableData(widget.statusCode);
      final List<_EnrichedLead> enriched = [];
      final List<_EnrichedLead> filterEnriched = [];

      for (final lead in leads) {
        final soldCustomer =
            lead.soldToParty != null && lead.soldToParty!.isNotEmpty
                ? await db.getCustomerDetails(lead.soldToParty!)
                : null;

        final shipCustomer =
            lead.shipToParty != null && lead.shipToParty!.isNotEmpty
                ? await db.getCustomerDetails(lead.shipToParty!)
                : null;

        final status =
            LeadStatusAndColorCode.resolveStatus(lead.leadQuotationStatus);

        enriched.add(_EnrichedLead(
          raw: lead,
          soldToPartyName: soldCustomer?.custName ?? lead.soldToParty ?? 'N/A',
          shipToPartyName: shipCustomer?.custName ?? lead.shipToParty ?? 'N/A',
          leadStatusLabel: status.label,
          leadStatusColor: status.color,
        ));
        if (int.parse(lead.leadQuotationStatus ?? '0') >= 15) {
          _poReceivedLeadCount++;
          filterEnriched.add(_EnrichedLead(
            raw: lead,
            soldToPartyName:
                soldCustomer?.custName ?? lead.soldToParty ?? 'N/A',
            shipToPartyName:
                shipCustomer?.custName ?? lead.shipToParty ?? 'N/A',
            leadStatusLabel: status.label,
            leadStatusColor: status.color,
          ));
        }
        if (lead.leadQuotationStatus?.toLowerCase() == '12') {
          _lostOrderLeadCount++;
        }
      }

      setState(() {
        _enrichedList = enriched;
        _showEnrichedList = filterEnriched;
      });
    } catch (e) {
      print('_fetchLeadData error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _fetchLeadDataDateFilter() async {
    setState(() => _isLoading = true);
    try {
      final db = NewLeadGenerationDatabase();
      final leads = await db.getAllLeadListMasterTableData(widget.statusCode);
      final List<_EnrichedLead> enriched = [];

      final now = DateTime.now();
      final last30 = now.subtract(const Duration(days: 30));
      final last3Months = DateTime(now.year, now.month - 3, now.day);
      final ytdStart = DateTime(now.year - 1, now.month, now.day);

      int total = 0, last30Count = 0, last3Count = 0, ytd = 0;

      for (final lead in leads) {
        final soldCustomer =
            lead.soldToParty != null && lead.soldToParty!.isNotEmpty
                ? await db.getCustomerDetails(lead.soldToParty!)
                : null;
        final shipCustomer =
            lead.shipToParty != null && lead.shipToParty!.isNotEmpty
                ? await db.getCustomerDetails(lead.shipToParty!)
                : null;
        final status =
            LeadStatusAndColorCode.resolveStatus(lead.leadQuotationStatus);

        enriched.add(_EnrichedLead(
          raw: lead,
          soldToPartyName: soldCustomer?.custName ?? lead.soldToParty ?? 'N/A',
          shipToPartyName: shipCustomer?.custName ?? lead.shipToParty ?? 'N/A',
          leadStatusLabel: status.label,
          leadStatusColor: status.color,
        ));

        total++;

        // Parse your lead date field — adjust field name as needed
        final leadDate = DateTime.tryParse(lead.downloadTime ?? '');
        if (leadDate != null) {
          if (leadDate.isAfter(last30)) last30Count++;
          if (leadDate.isAfter(last3Months)) last3Count++;
          if (leadDate.isAfter(ytdStart)) ytd++;
        }
      }

      setState(() {
        _enrichedList = enriched;
        _showEnrichedList = enriched;
        _totalLeadCount = total;
        _last30DaysCount = last30Count;
        _last3MonthsCount = last3Count;
        _ytdCount = ytd;
      });
    } catch (e) {
      print('_fetchLeadDataDateFilter error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  // ── Filter against Lead Status ─────────────────────────────────────────────
  Future<void> _filterLeadDataAgainstLeadStatus() async {
    final List<_EnrichedLead> filterEnriched = [];
    String value = 'hot';
    switch (_selectedTab) {
      case 0:
        value = 'hot';
        break;
      case 1:
        value = 'warm';
        break;
      case 2:
        value = 'cold';
        break;
    }
    for (final lead in _enrichedList) {
      if (lead.raw.leadStatus?.toLowerCase() == value) {
        filterEnriched.add(lead);
      }
    }
    setState(() {
      _showEnrichedList = filterEnriched;
    });
  }

  // ── Filter against Lead Status ─────────────────────────────────────────────
  Future<void> _filterLeadDataAgainstCustomerResponse() async {
    final List<_EnrichedLead> filterEnriched = [];
    for (final lead in _enrichedList) {
      if (_selectedTab == 0) {
        if (lead.raw.leadQuotationStatus?.toLowerCase() != '12') {
          filterEnriched.add(lead);
        }
      } else {
        if (lead.raw.leadQuotationStatus?.toLowerCase() == '12') {
          filterEnriched.add(lead);
        }
      }
    }
    setState(() {
      _showEnrichedList = filterEnriched;
    });
  }

  // ── Filter against Lead Status ─────────────────────────────────────────────
  void _filterByDateRange() {
    if (_startDate == null && _endDate == null) return;
    final filtered = _enrichedList.where((lead) {
      final leadDate = DateTime.tryParse(lead.raw.downloadTime ?? '');
      if (leadDate == null) return false;
      if (_startDate != null && leadDate.isBefore(_startDate!)) return false;
      if (_endDate != null && leadDate.isAfter(_endDate!)) return false;
      return true;
    }).toList();
    setState(() => _showEnrichedList = filtered);
  }

  // ── Build ──────────────────────────────────────────────────────────────────
  Widget _buildStatBox(String label, int count) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 10),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey.shade200),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              label,
              style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
            ),
            const SizedBox(height: 4),
            Text(
              '$count',
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: Colors.red,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTabButton(String label, int index, int count) {
    // ✅ no need to pass _selectedTab
    final isSelected = _selectedTab == index;
    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() => _selectedTab = index);
          if (widget.isShowLeadStatus) {
            _filterLeadDataAgainstLeadStatus();
          }
          if (widget.isShowPOandLost) {
            _filterLeadDataAgainstCustomerResponse();
          }
        },
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          padding: const EdgeInsets.symmetric(vertical: 11),
          decoration: BoxDecoration(
            color: isSelected ? Colors.red : Colors.transparent,
            borderRadius: BorderRadius.circular(999),
          ),
          alignment: Alignment.center,
          child: Text(
            label + count.toString(),
            style: TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: isSelected ? Colors.white : Colors.grey.shade500,
              letterSpacing: 0.3,
            ),
          ),
        ),
      ),
    );
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
              title: Text(
                widget.title,
                style: const TextStyle(color: Colors.white),
              ),
            ),
            body: SafeArea(
              child: Column(
                children: [
                  if (widget.isShowLeadStatus) ...[
                    Padding(
                      padding: const EdgeInsets.fromLTRB(12, 12, 12, 4),
                      child: Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(999),
                          border: Border.all(color: Colors.grey.shade200),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.06),
                              blurRadius: 4,
                              offset: const Offset(0, 1),
                            ),
                          ],
                        ),
                        child: Row(
                          // ✅ Remove MainAxisSize.min — let Row fill full width
                          children: [
                            _buildTabButton('HOT-', 0, _hotLeadCount),
                            _buildTabButton('WARM-', 1, _warmLeadCount),
                            _buildTabButton('COLD-', 2, _coldLeadCount),
                          ],
                        ),
                      ),
                    ),
                  ],
                  if (widget.isShowPOandLost) ...[
                    Padding(
                      padding: const EdgeInsets.fromLTRB(12, 12, 12, 4),
                      child: Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(999),
                          border: Border.all(color: Colors.grey.shade200),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.06),
                              blurRadius: 4,
                              offset: const Offset(0, 1),
                            ),
                          ],
                        ),
                        child: Row(
                          // ✅ Remove MainAxisSize.min — let Row fill full width
                          children: [
                            _buildTabButton(
                                'PO Received-', 0, _poReceivedLeadCount),
                            _buildTabButton(
                                'Lost Order-', 1, _lostOrderLeadCount),
                          ],
                        ),
                      ),
                    ),
                  ],
                  if (widget.isShowDateFilter) ...[
                    Padding(
                      padding: const EdgeInsets.fromLTRB(12, 12, 12, 4),
                      child: Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: Colors.grey.shade200),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.06),
                              blurRadius: 4,
                              offset: const Offset(0, 1),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // ── Total Lead ──────────────────────────────────────────
                            Text(
                              'Total Lead: $_totalLeadCount',
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: Colors.black,
                              ),
                            ),
                            const SizedBox(height: 12),

                            // ── Stats row ───────────────────────────────────────────
                            Row(
                              children: [
                                _buildStatBox('Last 30 Days', _last30DaysCount),
                                const SizedBox(width: 8),
                                _buildStatBox(
                                    'Last 3 Months', _last3MonthsCount),
                                const SizedBox(width: 8),
                                _buildStatBox('YTD', _ytdCount),
                              ],
                            ),
                            const SizedBox(height: 12),

                            // ── Date filter row ─────────────────────────────────────
                            Row(
                              children: [
                                // Filter icon button
                                Container(
                                  width: 46,
                                  height: 46,
                                  decoration: BoxDecoration(
                                    color: Colors.red,
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  child: IconButton(
                                    icon: const Icon(Icons.filter_alt_outlined,
                                        color: Colors.white, size: 22),
                                    onPressed: _filterByDateRange,
                                  ),
                                ),
                                const SizedBox(width: 8),

                                // Start date
                                Expanded(
                                  child: GestureDetector(
                                    onTap: () async {
                                      final picked = await showDatePicker(
                                        context: context,
                                        initialDate:
                                            _startDate ?? DateTime.now(),
                                        firstDate: DateTime(2020),
                                        lastDate: DateTime(2100),
                                      );
                                      if (picked != null) {
                                        setState(() => _startDate = picked);
                                      }
                                    },
                                    child: Container(
                                      height: 46,
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 10),
                                      decoration: BoxDecoration(
                                        border: Border.all(
                                            color: Colors.grey.shade300),
                                        borderRadius: BorderRadius.circular(10),
                                      ),
                                      child: Row(
                                        children: [
                                          Icon(Icons.calendar_month_outlined,
                                              color: Colors.red, size: 18),
                                          const SizedBox(width: 6),
                                          Text(
                                            _startDate != null
                                                ? '${_startDate!.day}/${_startDate!.month}/${_startDate!.year}'
                                                : 'Start date',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: _startDate != null
                                                  ? Colors.black
                                                  : Colors.grey.shade500,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 8),

                                // End date
                                Expanded(
                                  child: GestureDetector(
                                    onTap: () async {
                                      final picked = await showDatePicker(
                                        context: context,
                                        initialDate: _endDate ?? DateTime.now(),
                                        firstDate: DateTime(2020),
                                        lastDate: DateTime(2100),
                                      );
                                      if (picked != null) {
                                        setState(() => _endDate = picked);
                                      }
                                    },
                                    child: Container(
                                      height: 46,
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 10),
                                      decoration: BoxDecoration(
                                        border: Border.all(
                                            color: Colors.grey.shade300),
                                        borderRadius: BorderRadius.circular(10),
                                      ),
                                      child: Row(
                                        children: [
                                          Icon(Icons.calendar_month_outlined,
                                              color: Colors.red, size: 18),
                                          const SizedBox(width: 6),
                                          Text(
                                            _endDate != null
                                                ? '${_endDate!.day}/${_endDate!.month}/${_endDate!.year}'
                                                : 'End date',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: _endDate != null
                                                  ? Colors.black
                                                  : Colors.grey.shade500,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                  Expanded(
                    child: GestureDetector(
                      behavior: HitTestBehavior.opaque,
                      onTap: () => FocusScope.of(context).unfocus(),
                      child: _showEnrichedList.isEmpty && !_isLoading
                          ? const Center(child: Text('No data found'))
                          : ListView.builder(
                              padding: const EdgeInsets.all(16),
                              itemCount: _showEnrichedList.length,
                              itemBuilder: (context, index) {
                                final enriched = _showEnrichedList[index];
                                final item = enriched.raw;
                                return Card(
                                  elevation: 6,
                                  margin: const EdgeInsets.only(bottom: 12),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  color: Colors.white,
                                  child: Stack(
                                    children: [
                                      Padding(
                                        padding: const EdgeInsets.all(16),
                                        child: Column(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.start,
                                          children: [
                                            buildDataRow("Lead Id",
                                                item.leadGenerationId),
                                            buildDataRow("Contact Person",
                                                item.contactPersonName),
                                            buildDataRow("Contact Number",
                                                item.contactNumber),
                                            buildDataRow("Sold to Party",
                                                enriched.soldToPartyName),
                                            buildDataRow("Ship to Party",
                                                enriched.shipToPartyName),
                                            buildDataRow("Next Visit Date",
                                                item.nextVisitDate),
                                            buildDataRow("Exp. Rate per Bag",
                                                item.expRatePerBag),
                                            buildDataRow(
                                                "No. of Bags", item.qtyReq),
                                            buildDataRow(
                                              "Lead Status",
                                              enriched.leadStatusLabel,
                                              valueColor:
                                                  enriched.leadStatusColor,
                                            ),
                                            const SizedBox(height: 10),
                                            Row(
                                              children: [
                                                Expanded(
                                                  child: buildCustomButton(
                                                    "View Details",
                                                    () async {
                                                      final result =
                                                          await Navigator.push(
                                                        context,
                                                        MaterialPageRoute(
                                                          builder: (_) =>
                                                              LeadGenerationReportDetailsActivityScreen(
                                                            siteLeadData: item,
                                                          ),
                                                        ),
                                                      );
                                                      if (result == true) {
                                                        _fetchLeadData();
                                                      }
                                                    },
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ],
                                        ),
                                      ),

                                      // ── History icon ─────────────────────────────────────────────
                                      Positioned(
                                        top: 8,
                                        right: 8,
                                        child: IconButton(
                                          icon: const Icon(
                                            Icons.history,
                                            color: Colors.red,
                                            size: 22,
                                          ),
                                          onPressed: () {
                                            Navigator.push(
                                              context,
                                              MaterialPageRoute(
                                                builder: (_) =>
                                                    LeadGenerationLogActivityScreen(
                                                  leadId:
                                                      item.leadGenerationId ??
                                                          '',
                                                  siteLeadData: item,
                                                ),
                                              ),
                                            );
                                          },
                                        ),
                                      ),
                                    ],
                                  ),
                                );
                              },
                            ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          if (_isLoading)
            Container(
              color: Colors.black.withOpacity(0.3),
              child: const Center(child: CircularProgressIndicator()),
            ),
        ],
      ),
    );
  }
}

// ── Helpers ────────────────────────────────────────────────────────────────────

Widget buildDataRow(String title, String? value, {Color? valueColor}) {
  String _capitalize(String v) =>
      v.isEmpty ? v : v[0].toUpperCase() + v.substring(1).toLowerCase();
  String display = '';
  if (title == 'Lead Status') {
    display = (value == null || value.isEmpty) ? 'N/A' : value;
  } else {
    display = (value == null || value.isEmpty) ? 'N/A' : _capitalize(value);
  }

  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 5.0),
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          flex: 3,
          child: Text(
            "$title:",
            style: const TextStyle(fontSize: 12),
          ),
        ),
        Expanded(
          flex: 5,
          child: Text(
            display,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: valueColor ?? Colors.black,
            ),
          ),
        ),
      ],
    ),
  );
}

Widget buildCustomButton(String label, VoidCallback? onPressed) {
  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 5.0),
    child: SizedBox(
      width: double.infinity,
      child: ElevatedButton(
        onPressed: onPressed,
        style: ElevatedButton.styleFrom(
          padding: const EdgeInsets.symmetric(vertical: 12),
          backgroundColor: Colors.red,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
          textStyle: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
        ),
        child: Text(label),
      ),
    ),
  );
}
