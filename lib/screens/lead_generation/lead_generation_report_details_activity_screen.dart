import 'package:flutter/material.dart';
import 'package:starsfa/db_setup/new_lead_generation_database.dart';

class LeadGenerationReportDetailsActivityScreen extends StatefulWidget {
  final LeadListMasterTableDataSet siteLeadData;

  const LeadGenerationReportDetailsActivityScreen({
    super.key,
    required this.siteLeadData,
  });

  @override
  State<LeadGenerationReportDetailsActivityScreen> createState() =>
      _LeadGenerationReportDetailsActivityScreenState();
}

class _LeadGenerationReportDetailsActivityScreenState
    extends State<LeadGenerationReportDetailsActivityScreen> {
  bool _isLoading = false;

  // Resolved values
  String _soldToPartyName = '';
  String _soldToPartyAddress = '';
  String _soldToPartyState = '';
  String _soldToPartyDistrict = '';
  String _shipToPartyName = '';
  String _shipToPartyAddress = '';
  String _shipToPartyState = '';
  String _shipToPartyDistrict = '';
  String _dateStamp = '';
  String _timeStamp = '';

  @override
  void initState() {
    super.initState();
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
        _soldToPartyAddress = sold.address ?? '';
        _soldToPartyState = sold.state ?? '';
        _soldToPartyDistrict = sold.district ?? '';
      }

      // Resolve ship-to party
      if (data.shipToParty != null && data.shipToParty!.isNotEmpty) {
        final ship = await db.getCustomerDetails(data.shipToParty!);
        _shipToPartyName = ship.custName ?? '';
        _shipToPartyAddress = ship.address ?? '';
        _shipToPartyState = ship.state ?? '';
        _shipToPartyDistrict = ship.district ?? '';
      }

      // Split download_time into date and time
      if (data.downloadTime != null && data.downloadTime!.isNotEmpty) {
        final parts = data.downloadTime!.split(' ');
        _dateStamp = parts.isNotEmpty ? parts[0] : '';
        _timeStamp = parts.length > 1 ? parts[1] : '';
      }
    } catch (e) {
      print('_loadResolvedData error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  String _resolveStatusLabel(String? statusCode) {
    switch (statusCode) {
      case '1':
        return 'SO Lead Create';
      case '2':
        return 'HOS Hold';
      case '3':
        return 'HOS Revision';
      case '4':
        return 'HOS Reject';
      case '5':
        return 'HOS Approved';
      case '6':
        return 'MIS Quotation Create';
      case '7':
        return 'MIS Send To COO';
      case '8':
        return 'COO Lead Revision';
      case '9':
        return 'COO Lead Reject';
      case '10':
        return 'COO Lead Approved';
      case '11':
        return 'MIS Sent To SAP';
      case '12':
        return 'Lost Order';
      case '13':
        return 'SAP Quotation Create';
      case '14':
        return 'SAP PO Received';
      case '15':
        return 'SAP Contract Create';
      case '16':
        return 'SAP SO Create';
      default:
        return statusCode ?? 'N/A';
    }
  }

  @override
  Widget build(BuildContext context) {
    final data = widget.siteLeadData;

    final List<Map<String, String>> fields = [
      {"label": "Lead Id", "value": data.leadGenerationId ?? ""},
      {"label": "Employee Code", "value": data.empCode ?? ""},
      {"label": "Date Stamp", "value": _dateStamp},
      {"label": "Time Stamp", "value": _timeStamp},
      {"label": "Latitude", "value": data.latitude ?? ""},
      {"label": "Longitude", "value": data.longitude ?? ""},
      {"label": "Sold to Party Name", "value": _soldToPartyName},
      {"label": "Sold to Party Code", "value": data.soldToParty ?? ""},
      {"label": "Sold to Party Address", "value": _soldToPartyAddress},
      {"label": "Sold to Party State", "value": _soldToPartyState},
      {"label": "Sold to Party District", "value": _soldToPartyDistrict},
      {"label": "Ship to Party Name", "value": _shipToPartyName},
      {"label": "Ship to Party Code", "value": data.shipToParty ?? ""},
      {"label": "Ship to Party Address", "value": _shipToPartyAddress},
      {"label": "Ship to Party State", "value": _shipToPartyState},
      {"label": "Ship to Party District", "value": _shipToPartyDistrict},
      {"label": "Segment", "value": data.typeLead ?? ""},
      {"label": "Lead Source", "value": data.leadType ?? ""},
      {"label": "Product + Packaging", "value": data.productPackaging ?? ""},
      {"label": "Total Potential (MT)", "value": data.qtyReq ?? ""},
      {"label": "Quotation Quantity (MT)", "value": data.monthQty ?? ""},
      {"label": "Current Brand Used", "value": data.currentBrandUsed ?? ""},
      {"label": "Expected Rate", "value": data.expRatePerBag ?? ""},
      {"label": "Current Price Star", "value": data.currentPrice ?? ""},
      {
        "label": "Current Price Competitor",
        "value": data.currentPriceCompetitor ?? ""
      },
      {"label": "Contact Person", "value": data.contactPersonName ?? ""},
      {"label": "Designation", "value": data.designation ?? ""},
      {"label": "Contact Number", "value": data.contactNumber ?? ""},
      {"label": "Mail Id", "value": data.mailId ?? ""},
      {"label": "Mode of Payment", "value": data.mode ?? ""},
      {"label": "Credit Terms", "value": data.creditTerms ?? ""},
      {"label": "AAC Block Required", "value": data.accBlockIsRequired ?? ""},
      {
        "label": "Construction Type",
        "value": data.categoryTypeConstruction ?? ""
      },
      {"label": "Next Visit Date", "value": data.nextVisitDate ?? ""},
      {"label": "Requirement Type", "value": data.incoterms ?? ""},
      {"label": "Sales Officer Remarks", "value": data.leadRemarks ?? ""},
      {"label": "Assigned To", "value": data.assignedTo ?? ""},
      {"label": "Requirement Timing", "value": data.rTiming ?? ""},
      {"label": "Destination", "value": data.destination ?? ""},
      {"label": "Company Constraint", "value": data.companyConstraint ?? ""},
      {"label": "Reason", "value": data.reason ?? ""},
      {"label": "NOV", "value": data.nov ?? ""},
      {"label": "Quoted Price", "value": data.quotedPrice ?? ""},
      {"label": "Approved Price", "value": data.approvedPrice ?? ""},
      {"label": "Last Price", "value": data.lastPrice ?? ""},
      {"label": "Previous Last Price", "value": data.prevLastPrice ?? ""},
      {"label": "TPC", "value": data.tpc ?? ""},
      {"label": "Payment", "value": data.payment ?? ""},
      {"label": "Quotation Provided", "value": data.quotationProvided ?? ""},
      {
        "label": "Quotation Provided Date",
        "value": data.quotationProvidedDate ?? ""
      },
      {"label": "MIS Submission Date", "value": data.misSubmissionDate ?? ""},
      {"label": "HOS Submission Date", "value": data.hosSubmissionDate ?? ""},
      {"label": "Branch", "value": data.branch ?? ""},
      {"label": "District", "value": data.district ?? ""},
      {"label": "State", "value": data.state ?? ""},
      {
        "label": "Lead Status",
        "value": _resolveStatusLabel(data.leadQuotationStatus)
      },
    ];

    if (data.leadQuotationStatus == '12') {
      fields.add(
          {"label": "Reason of Lost", "value": data.lostOrderReason ?? ""});
    }

    // Conditional fields
    final incoterms = (data.incoterms ?? '').toLowerCase();
    if (incoterms == 'exw') {
      fields.add({"label": "Ex. Works", "value": data.servingLocation ?? ""});
    }
    if (incoterms == 'fos') {
      fields.add({"label": "FOS Siding", "value": data.servingLocation ?? ""});
    }

    return PopScope(
      canPop: false,
      onPopInvoked: (didPop) {
        if (!didPop) Navigator.pop(context, true);
      },
      child: Stack(
        children: [
          Scaffold(
            appBar: AppBar(
              backgroundColor: Colors.red,
              title: const Text(
                'Lead Details',
                style: TextStyle(color: Colors.white),
              ),
              leading: IconButton(
                icon: const Icon(Icons.arrow_back, color: Colors.white),
                onPressed: () => Navigator.pop(context, true),
              ),
            ),
            body: ListView.builder(
              padding: const EdgeInsets.all(10),
              itemCount: fields.length,
              itemBuilder: (context, index) {
                final item = fields[index];
                return Column(
                  children: [
                    const Divider(height: 1, color: Colors.black12),
                    LabelValueText(
                      label: item["label"]!,
                      value: item["value"]!,
                    ),
                  ],
                );
              },
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

// ── Label / Value row ──────────────────────────────────────────────────────────

class LabelValueText extends StatelessWidget {
  final String label;
  final String value;

  const LabelValueText({
    super.key,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 1,
            child: Text(
              label,
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
              value.isEmpty ? '-' : value,
              style: const TextStyle(
                fontSize: 13,
                color: Colors.black,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
