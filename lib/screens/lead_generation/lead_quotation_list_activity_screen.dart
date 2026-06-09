import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:starsfa/db_setup/data_for_downloading_lead.dart';
import 'package:starsfa/db_setup/new_lead_generation_database.dart';
import 'package:starsfa/log/lead_status_and_color_code.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class LeadQuotationListActivityScreen extends StatefulWidget {
  const LeadQuotationListActivityScreen({
    super.key,
  });

  @override
  State<LeadQuotationListActivityScreen> createState() =>
      _LeadQuotationListActivityScreenState();
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

class _LeadQuotationListActivityScreenState
    extends State<LeadQuotationListActivityScreen> {
  bool _isLoadingForRefresh = false;
  bool _isLoading = false;
  int _typeSelected = 1;
  List<_EnrichedLead> _enrichedList = [];
  final List<_EnrichedLead> _showEnrichedList = [];
  int _totalLeadCount = 0;
  int _poPendingCount = 0;
  int _poRevisionCount = 0;
  int _lostLeadCount = 0;
  int _poReceivedCount = 0;
  final ImagePicker picker = ImagePicker();
  final GlobalKey<ScaffoldMessengerState> _messengerKey =
      GlobalKey<ScaffoldMessengerState>();

  TextEditingController lostLeadReasonController = TextEditingController();
  String? leadId = '';
  String? leadQuotationId = '';

  int _empCategory = 0;
  List<XFile> images = [];
  DateTime? poDate;
  String? existingPoImageUrl; // ← NEW: tracks already-saved server image
  final TextEditingController poNumberController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _fetchEmployeeCategory();
  }

  Future<void> _fetchEmployeeCategory() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final userDetails = await UserLoginClass.getLocalUser();
      String emp_code = userDetails!.empCode ?? '';
      final headers = {
        'Content-Type': 'application/json',
      };
      final response = await http.get(
        Uri.parse('${AppWebService.sbDevUrl}api/employee/?emp_code=$emp_code'),
        headers: headers,
      );
      if (response.statusCode == 200) {
        int empType = 0;
        final responseData = jsonDecode(response.body);
        print(responseData);
        if (responseData[0]['level'].toString().toLowerCase() == 'nt_to') {
          empType = 2;
          setState(() {
            _empCategory = empType;
            _isLoading = false;
          });
        } else if (responseData[0]['level'].toString().toLowerCase() == 'nt') {
          empType = 1;
          setState(() {
            _empCategory = empType;
            _isLoading = false;
          });
        } else {
          setState(() {
            _isLoading = false;
          });
          // ignore: use_build_context_synchronously
          Navigator.of(context).pop();
        }
        _fetchLeadData();
      }
      // ignore: empty_catches
    } catch (e) {}
  }

  void refreshLeadList() async {
    setState(() {
      _isLoadingForRefresh = true;
    });

    final downloader = DataForDownloadingLead();

    final success = await downloader.addAllFormDataForLead(
      empCode: 'E0555',
      baseUrl: '${AppWebService.sbDevUrl}',
    );

    if (success) {
      // ignore: avoid_print
      print('All data downloaded successfully');
      _fetchLeadData();
      setState(() {
        _isLoadingForRefresh = false;
      });
    } else {
      // ignore: avoid_print
      print('Download failed');
      setState(() {
        _isLoadingForRefresh = false;
      });
      // ignore: use_build_context_synchronously
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Download failed'),
        ),
      );
    }
  }

  Future<void> _fetchLeadData() async {
    setState(() => _isLoading = true);
    try {
      final db = NewLeadGenerationDatabase();
      final leads = await db.getAllLeadListMasterTableData(12);
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
      });
      if (_empCategory == 1) _filterAllLeadListAndShowForSO();
      if (_empCategory == 2) _filterAllLeadListAndShowForHOS();
    } catch (e) {
      print('_fetchLeadData error: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  // ── Fetch & enrich ─────────────────────────────────────────────────────────
  void _filterAllLeadListAndShowForSO() {
    int totalCount = 0;
    int poPendingCount = 0;
    int poRevisionCount = 0;
    int lostLeadCount = 0;

    _showEnrichedList.clear();

    for (final lead in _enrichedList) {
      if (lead.raw.leadQuotationStatus == '14') {
        totalCount++;
        poPendingCount++;
        if (_typeSelected == 1) {
          _showEnrichedList.add(lead);
        }
      } else if (lead.raw.leadQuotationStatus == '17') {
        totalCount++;
        poRevisionCount++;
        if (_typeSelected == 2) {
          _showEnrichedList.add(lead);
        }
      } else if (lead.raw.leadQuotationStatus == '12') {
        totalCount++;
        lostLeadCount++;
        if (_typeSelected == 3) {
          _showEnrichedList.add(lead);
        }
      }
    }

    setState(() {
      _totalLeadCount = totalCount;
      _poPendingCount = poPendingCount;
      _poRevisionCount = poRevisionCount;
      _lostLeadCount = lostLeadCount;
    });
  }

  void _filterAllLeadListAndShowForHOS() {
    int totalCount = 0;
    int poPendingCount = 0;
    int poReceivedCount = 0;
    int lostLeadCount = 0;
    int poRevisionCount = 0;

    _showEnrichedList.clear();

    for (final lead in _enrichedList) {
      if (lead.raw.leadQuotationStatus == '14' ||
          lead.raw.leadQuotationStatus == '17') {
        totalCount++;
        poPendingCount++;
        if (_typeSelected == 1) {
          _showEnrichedList.add(lead);
        }
      } else if (lead.raw.leadQuotationStatus == '15') {
        totalCount++;
        poReceivedCount++;
        if (_typeSelected == 2) {
          _showEnrichedList.add(lead);
        }
      } else if (lead.raw.leadQuotationStatus == '19') {
        totalCount++;
        poRevisionCount++;
        if (_typeSelected == 3) {
          _showEnrichedList.add(lead);
        }
      } else if (lead.raw.leadQuotationStatus == '12') {
        totalCount++;
        lostLeadCount++;
        if (_typeSelected == 4) {
          _showEnrichedList.add(lead);
        }
      }
    }

    setState(() {
      _totalLeadCount = totalCount;
      _poPendingCount = poPendingCount;
      _poReceivedCount = poReceivedCount;
      _lostLeadCount = lostLeadCount;
      _poRevisionCount = poRevisionCount;
    });
  }

  void checkForLostReason() {
    print(leadQuotationId);
    if (lostLeadReasonController.text.isNotEmpty) {
      _sendLostReasonToServer();
    } else {
      _messengerKey.currentState?.showSnackBar(
        const SnackBar(
          content: Text('Please enter lost reason'),
          backgroundColor: Colors.red,
        ),
      );
      Future.delayed(const Duration(seconds: 2), () {
        _showPopupForLost();
      });
    }
  }

  // ── FIXED: checkForPoDetails now accounts for existingPoImageUrl ───────────
  void checkForPoDetails() {
    print(poDate.toString());
    if (poNumberController.text.isEmpty) {
      _messengerKey.currentState?.showSnackBar(
        const SnackBar(
          content: Text('Please enter PO Number'),
          backgroundColor: Colors.red,
        ),
      );
      Future.delayed(const Duration(seconds: 2), () {
        _showPopupForPO();
      });
    } else if (poDate == null) {
      _messengerKey.currentState?.showSnackBar(
        const SnackBar(
          content: Text('Please select PO Date'),
          backgroundColor: Colors.red,
        ),
      );
      Future.delayed(const Duration(seconds: 2), () {
        _showPopupForPO();
      });
    } else if (images.isEmpty && existingPoImageUrl == null) {
      // ← FIXED: pass if existing server image is present
      _messengerKey.currentState?.showSnackBar(
        const SnackBar(
          content: Text('Please attach image'),
          backgroundColor: Colors.red,
        ),
      );
      Future.delayed(const Duration(seconds: 2), () {
        _showPopupForPO();
      });
    } else if (images.isNotEmpty) {
      print('images is not empty');
      // New image selected → upload it first
      _sendImageToServer('lead', 'po_submit');
    } else {
      print('images is not empty22222222');
      // No new image but existing URL present → reuse it
      _sendPoDetailsToServer(existingPoImageUrl!);
    }
  }

  Future<void> _sendLostReasonToServer() async {
    try {
      setState(() {
        _isLoading = true;
      });
      final Map<String, String> mainObject = {
        'lost_order_reason': lostLeadReasonController.text,
        'lead_quotation_status': '12',
      };
      print('${AppWebService.sbDevUrl}api/leadmaster/${leadQuotationId!}');
      final response = await http.put(
        Uri.parse(
            '${AppWebService.sbDevUrl}api/leadmaster/${leadQuotationId!}/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      print("Status Code: ${response.statusCode}");
      print("Response Body: ${response.body}");
      if (response.statusCode == 200) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _messengerKey.currentState?.showSnackBar(
          const SnackBar(
            content: Text('Lost reason add successfully'),
            backgroundColor: Colors.green,
          ),
        );
        lostLeadReasonController.clear();
        refreshLeadList();
      } else {
        setState(() {
          _isLoading = false;
        });
        final responseData = jsonDecode(response.body);
        _messengerKey.currentState?.showSnackBar(
          SnackBar(
            content: Text(responseData.error),
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {}
  }

  Future<void> _sendImageToServer(String category, String subCategory) async {
    try {
      setState(() {
        _isLoading = true;
      });

      var request = http.MultipartRequest(
          'POST', Uri.parse('${AppWebService.sbDevUrl}api/media/'));
      request.fields.addAll({
        'category': category,
        'sub_category': subCategory,
        'query_id': leadQuotationId ?? ''
      });
      request.files
          .add(await http.MultipartFile.fromPath('file', images[0].path));
      print(images[0].path);
      http.StreamedResponse response = await request.send();

      if (response.statusCode == 201) {
        final responseBody = await response.stream.bytesToString();
        final responseData = jsonDecode(responseBody);
        print('Status: ${response.statusCode}');
        print('Response Data: ${responseData['url']}');
        _sendPoDetailsToServer(responseData['url']);
      } else {
        print(response.reasonPhrase);
      }
    } catch (e) {
      print(e.toString());
    }
  }

  Future<void> _sendPoDetailsToServer(String imageUrl) async {
    try {
      setState(() {
        _isLoading = true;
      });
      final Map<String, String> mainObject = {
        'lead_quotation_status': '15',
        'po_number': poNumberController.text,
        'po_date': poDate.toString(),
        'po_image': imageUrl,
      };
      print('${AppWebService.sbDevUrl}api/leadmaster/${leadQuotationId!}');
      final response = await http.put(
        Uri.parse(
            '${AppWebService.sbDevUrl}api/leadmaster/${leadQuotationId!}/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      print("Status Code: ${response.statusCode}");
      print("Response Body: ${response.body}");
      if (response.statusCode == 200) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _messengerKey.currentState?.showSnackBar(
          const SnackBar(
            content: Text('PO Details send to HOS successfully'),
            backgroundColor: Colors.green,
          ),
        );
        poNumberController.clear();
        poDate = null;
        images.clear();
        existingPoImageUrl = null; // ← clear on success
        refreshLeadList();
      } else {
        setState(() {
          _isLoading = false;
        });
        final responseData = jsonDecode(response.body);
        _messengerKey.currentState?.showSnackBar(
          SnackBar(
            content: Text(responseData.error),
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {}
  }

  Future<void> _sendForwardToMISbyHOS() async {
    try {
      setState(() {
        _isLoading = true;
      });
      final Map<String, String> mainObject = {
        'lead_quotation_status': '16',
      };
      print('${AppWebService.sbDevUrl}api/leadmaster/${leadQuotationId!}');
      final response = await http.put(
        Uri.parse(
            '${AppWebService.sbDevUrl}api/leadmaster/${leadQuotationId!}/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      print("Status Code: ${response.statusCode}");
      print("Response Body: ${response.body}");
      if (response.statusCode == 200) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _messengerKey.currentState?.showSnackBar(
          const SnackBar(
            content: Text('PO Details send to MIS successfully'),
            backgroundColor: Colors.green,
          ),
        );
        lostLeadReasonController.clear();
        refreshLeadList();
      } else {
        setState(() {
          _isLoading = false;
        });
        final responseData = jsonDecode(response.body);
        _messengerKey.currentState?.showSnackBar(
          SnackBar(
            content: Text(responseData.error),
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {}
  }

  Future<void> _sendSendBackToSObyHOS() async {
    try {
      setState(() {
        _isLoading = true;
      });
      final Map<String, String> mainObject = {
        'lead_quotation_status': '17',
      };
      print('${AppWebService.sbDevUrl}api/leadmaster/${leadQuotationId!}');
      final response = await http.put(
        Uri.parse(
            '${AppWebService.sbDevUrl}api/leadmaster/${leadQuotationId!}/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      print("Status Code: ${response.statusCode}");
      print("Response Body: ${response.body}");
      if (response.statusCode == 200) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _messengerKey.currentState?.showSnackBar(
          const SnackBar(
            content:
                Text('PO Details send back to SO for revision successfully'),
            backgroundColor: Colors.green,
          ),
        );
        lostLeadReasonController.clear();
        refreshLeadList();
      } else {
        setState(() {
          _isLoading = false;
        });
        final responseData = jsonDecode(response.body);
        _messengerKey.currentState?.showSnackBar(
          SnackBar(
            content: Text(responseData.error),
            backgroundColor: Colors.red,
          ),
        );
      }
    } catch (e) {}
  }

  // ── Build ──────────────────────────────────────────────────────────────────
  Widget _buildStatBoxForSO(String label, int count) {
    return Expanded(
      child: GestureDetector(
        onTap: () {
          if (label == 'PO-Pending') {
            _typeSelected = 1;
            _filterAllLeadListAndShowForSO();
          } else if (label == 'PO-Revision') {
            _typeSelected = 2;
            _filterAllLeadListAndShowForSO();
          } else if (label == 'Lead Lost') {
            _typeSelected = 3;
            _filterAllLeadListAndShowForSO();
          }
        },
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 10),
          decoration: BoxDecoration(
            border: Border.all(
              color: Colors.grey.shade200,
              width: 1,
            ),
            borderRadius: BorderRadius.circular(10),
            color: Colors.white,
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
      ),
    );
  }

  Widget _buildStatBoxForHOS(String label, int count) {
    return Expanded(
      child: GestureDetector(
        onTap: () {
          if (label == 'PO-Pending') {
            _typeSelected = 1;
            _filterAllLeadListAndShowForHOS();
          } else if (label == 'PO-Received') {
            _typeSelected = 2;
            _filterAllLeadListAndShowForHOS();
          } else if (label == 'PO-Revision') {
            _typeSelected = 3;
            _filterAllLeadListAndShowForHOS();
          } else if (label == 'Lead\nLost') {
            _typeSelected = 4;
            _filterAllLeadListAndShowForHOS();
          }
        },
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 10),
          decoration: BoxDecoration(
            border: Border.all(
              color: Colors.grey.shade200,
              width: 1,
            ),
            borderRadius: BorderRadius.circular(10),
            color: Colors.white,
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
      ),
    );
  }

  void _showPopupForLost() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (BuildContext context) {
        return AnimatedPadding(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(context).viewInsets.bottom,
          ),
          duration: const Duration(milliseconds: 150),
          curve: Curves.easeOut,
          child: DraggableScrollableSheet(
            initialChildSize: .5,
            minChildSize: 0.5,
            maxChildSize: 1,
            expand: false,
            builder: (context, scrollController) {
              return StatefulBuilder(
                builder: (context, setState) {
                  return Container(
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.vertical(
                        top: Radius.circular(20),
                      ),
                    ),
                    padding: const EdgeInsets.only(
                      left: 20,
                      right: 20,
                      top: 12,
                      bottom: 20,
                    ),
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
                            "Why the lead lost?",
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 20),
                          TextField(
                            controller: lostLeadReasonController,
                            maxLines: 6,
                            minLines: 6,
                            textAlignVertical: TextAlignVertical.top,
                            decoration: const InputDecoration(
                              labelText: "Reason of lost lead",
                              alignLabelWithHint: true,
                              border: OutlineInputBorder(),
                            ),
                          ),
                          const SizedBox(height: 20),
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.red,
                                foregroundColor: Colors.white,
                                padding:
                                    const EdgeInsets.symmetric(vertical: 14),
                              ),
                              onPressed: () {
                                Navigator.of(context).pop();
                                checkForLostReason();
                              },
                              child: const Text("Submit"),
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              );
            },
          ),
        );
      },
    );
  }

  // ── FIXED: _showPopupForPO now shows pre-filled poDate, poNumber, and image ─
  void _showPopupForPO() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (BuildContext context) {
        return DraggableScrollableSheet(
          initialChildSize: 0.6,
          minChildSize: 0.4,
          maxChildSize: 0.92,
          builder: (context, scrollController) {
            return StatefulBuilder(
              builder: (context, setSheetState) {
                return Container(
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    borderRadius:
                        BorderRadius.vertical(top: Radius.circular(20)),
                  ),
                  padding: EdgeInsets.only(
                    left: 20,
                    right: 20,
                    top: 12,
                    bottom: MediaQuery.of(context).viewInsets.bottom + 20,
                  ),
                  child: ListView(
                    controller: scrollController,
                    children: [
                      // Drag handle
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
                        "PO Details",
                        style: TextStyle(
                            fontSize: 16, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 20),

                      // PO Number — pre-filled if available
                      TextField(
                        controller: poNumberController,
                        decoration: const InputDecoration(
                          labelText: "PO Number",
                          border: OutlineInputBorder(),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // PO Date — pre-filled if available
                      GestureDetector(
                        onTap: () async {
                          final picked = await showDatePicker(
                            context: context,
                            initialDate: poDate ?? DateTime.now(),
                            firstDate: DateTime(2020),
                            lastDate: DateTime(2100),
                          );
                          if (picked != null) {
                            setSheetState(() => poDate = picked);
                          }
                        },
                        child: Container(
                          height: 46,
                          padding: const EdgeInsets.symmetric(horizontal: 10),
                          decoration: BoxDecoration(
                            border: Border.all(color: Colors.grey),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  poDate != null
                                      ? '${poDate!.day}/${poDate!.month}/${poDate!.year}'
                                      : 'PO Date',
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: poDate != null
                                        ? Colors.black
                                        : Colors.grey.shade500,
                                  ),
                                ),
                              ),
                              const Icon(Icons.calendar_month_outlined,
                                  color: Colors.red, size: 18),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // ── FIXED: PO Image picker — shows status based on existing/new image ──
                      GestureDetector(
                        onTap: () async {
                          if (images.length == 1) {
                            _messengerKey.currentState?.showSnackBar(
                              const SnackBar(
                                content: Text('Maximum 1 image is allowed'),
                                backgroundColor: Colors.red,
                              ),
                            );
                            return;
                          }
                          final XFile? image = await picker.pickImage(
                              source: ImageSource.camera);
                          if (image != null) {
                            setSheetState(() {
                              existingPoImageUrl =
                                  null; // new image replaces existing
                              images.add(image);
                            });
                          }
                        },
                        child: Container(
                          height: 46,
                          padding: const EdgeInsets.symmetric(horizontal: 10),
                          decoration: BoxDecoration(
                            border: Border.all(
                              color: (images.isNotEmpty ||
                                      existingPoImageUrl != null)
                                  ? Colors.green
                                  : Colors.grey,
                            ),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  (images.isNotEmpty ||
                                          existingPoImageUrl != null)
                                      ? 'Image attached ✓'
                                      : 'PO Image',
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: (images.isNotEmpty ||
                                            existingPoImageUrl != null)
                                        ? Colors.green
                                        : Colors.black,
                                  ),
                                ),
                              ),
                              Icon(
                                Icons.image,
                                color: (images.isNotEmpty ||
                                        existingPoImageUrl != null)
                                    ? Colors.green
                                    : Colors.red,
                                size: 18,
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // ── Show existing server image (network) ──────────────────
                      if (existingPoImageUrl != null)
                        Padding(
                          padding: const EdgeInsets.only(bottom: 12),
                          child: Stack(
                            children: [
                              ClipRRect(
                                borderRadius: BorderRadius.circular(8),
                                child: Image.network(
                                  existingPoImageUrl!,
                                  height: 100,
                                  width: 100,
                                  fit: BoxFit.cover,
                                  errorBuilder: (_, __, ___) => Container(
                                    height: 100,
                                    width: 100,
                                    color: Colors.grey.shade200,
                                    child: const Icon(Icons.broken_image,
                                        size: 40, color: Colors.grey),
                                  ),
                                ),
                              ),
                              Positioned(
                                right: 4,
                                top: 4,
                                child: GestureDetector(
                                  onTap: () => setSheetState(
                                      () => existingPoImageUrl = null),
                                  child: Container(
                                    padding: const EdgeInsets.all(4),
                                    decoration: const BoxDecoration(
                                      color: Colors.white,
                                      shape: BoxShape.circle,
                                    ),
                                    child: const Icon(Icons.close,
                                        size: 16, color: Colors.black),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),

                      // ── Show newly picked local images ────────────────────────
                      if (images.isNotEmpty)
                        GridView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          gridDelegate:
                              const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 3,
                            mainAxisSpacing: 8,
                            crossAxisSpacing: 8,
                          ),
                          itemCount: images.length,
                          itemBuilder: (context, index) {
                            return Stack(
                              children: [
                                Positioned.fill(
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(8),
                                    child: Image.file(
                                      File(images[index].path),
                                      fit: BoxFit.cover,
                                    ),
                                  ),
                                ),
                                Positioned(
                                  right: 4,
                                  top: 4,
                                  child: GestureDetector(
                                    onTap: () => setSheetState(
                                        () => images.removeAt(index)),
                                    child: Container(
                                      padding: const EdgeInsets.all(4),
                                      decoration: const BoxDecoration(
                                        color: Colors.white,
                                        shape: BoxShape.circle,
                                      ),
                                      child: const Icon(Icons.close,
                                          size: 16, color: Colors.black),
                                    ),
                                  ),
                                ),
                              ],
                            );
                          },
                        ),

                      const SizedBox(height: 20),

                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.red,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 14),
                          ),
                          onPressed: () {
                            Navigator.of(context).pop();
                            checkForPoDetails();
                          },
                          child: const Text("Submit"),
                        ),
                      ),
                    ],
                  ),
                );
              },
            );
          },
        );
      },
    );
  }

  // ── FIXED: _showPopupForPO now shows pre-filled poDate, poNumber, and image ─
  void _showDetailsPopupForPO() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (BuildContext context) {
        return DraggableScrollableSheet(
          initialChildSize: 0.6,
          minChildSize: 0.4,
          maxChildSize: 0.92,
          builder: (context, scrollController) {
            return StatefulBuilder(
              builder: (context, setSheetState) {
                return Container(
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    borderRadius:
                        BorderRadius.vertical(top: Radius.circular(20)),
                  ),
                  padding: EdgeInsets.only(
                    left: 20,
                    right: 20,
                    top: 12,
                    bottom: MediaQuery.of(context).viewInsets.bottom + 20,
                  ),
                  child: ListView(
                    controller: scrollController,
                    children: [
                      // Drag handle
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
                        "PO Details",
                        style: TextStyle(
                            fontSize: 16, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 20),

                      // PO Number — pre-filled if available
                      TextField(
                        controller: poNumberController,
                        decoration: const InputDecoration(
                          labelText: "PO Number",
                          border: OutlineInputBorder(),
                        ),
                        enabled: false,
                      ),
                      const SizedBox(height: 16),

                      // PO Date — pre-filled if available
                      GestureDetector(
                        onTap: () {},
                        child: Container(
                          height: 46,
                          padding: const EdgeInsets.symmetric(horizontal: 10),
                          decoration: BoxDecoration(
                            border: Border.all(color: Colors.grey),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  poDate != null
                                      ? '${poDate!.day}/${poDate!.month}/${poDate!.year}'
                                      : 'PO Date',
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: poDate != null
                                        ? Colors.black
                                        : Colors.grey.shade500,
                                  ),
                                ),
                              ),
                              const Icon(Icons.calendar_month_outlined,
                                  color: Colors.red, size: 18),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // ── FIXED: PO Image picker — shows status based on existing/new image ──
                      GestureDetector(
                        onTap: () {},
                        child: Container(
                          height: 46,
                          padding: const EdgeInsets.symmetric(horizontal: 10),
                          decoration: BoxDecoration(
                            border: Border.all(
                              color: (images.isNotEmpty ||
                                      existingPoImageUrl != null)
                                  ? Colors.green
                                  : Colors.grey,
                            ),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: Row(
                            children: [
                              Expanded(
                                child: Text(
                                  (images.isNotEmpty ||
                                          existingPoImageUrl != null)
                                      ? 'Image attached ✓'
                                      : 'PO Image',
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: (images.isNotEmpty ||
                                            existingPoImageUrl != null)
                                        ? Colors.green
                                        : Colors.black,
                                  ),
                                ),
                              ),
                              Icon(
                                Icons.image,
                                color: (images.isNotEmpty ||
                                        existingPoImageUrl != null)
                                    ? Colors.green
                                    : Colors.red,
                                size: 18,
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // ── Show existing server image (network) ──────────────────
                      if (existingPoImageUrl != null)
                        Padding(
                          padding: const EdgeInsets.only(bottom: 12),
                          child: Stack(
                            children: [
                              ClipRRect(
                                borderRadius: BorderRadius.circular(8),
                                child: Image.network(
                                  existingPoImageUrl!,
                                  height: 100,
                                  width: 100,
                                  fit: BoxFit.cover,
                                  errorBuilder: (_, __, ___) => Container(
                                    height: 100,
                                    width: 100,
                                    color: Colors.grey.shade200,
                                    child: const Icon(Icons.broken_image,
                                        size: 40, color: Colors.grey),
                                  ),
                                ),
                              ),
                              Positioned(
                                right: 4,
                                top: 4,
                                child: GestureDetector(
                                  onTap: () => setSheetState(
                                      () => existingPoImageUrl = null),
                                  child: Container(
                                    padding: const EdgeInsets.all(4),
                                    decoration: const BoxDecoration(
                                      color: Colors.white,
                                      shape: BoxShape.circle,
                                    ),
                                    child: const Icon(Icons.close,
                                        size: 16, color: Colors.black),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),

                      // ── Show newly picked local images ────────────────────────
                      if (images.isNotEmpty)
                        GridView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          gridDelegate:
                              const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 3,
                            mainAxisSpacing: 8,
                            crossAxisSpacing: 8,
                          ),
                          itemCount: images.length,
                          itemBuilder: (context, index) {
                            return Stack(
                              children: [
                                Positioned.fill(
                                  child: ClipRRect(
                                    borderRadius: BorderRadius.circular(8),
                                    child: Image.file(
                                      File(images[index].path),
                                      fit: BoxFit.cover,
                                    ),
                                  ),
                                ),
                                Positioned(
                                  right: 4,
                                  top: 4,
                                  child: GestureDetector(
                                    onTap: () => setSheetState(
                                        () => images.removeAt(index)),
                                    child: Container(
                                      padding: const EdgeInsets.all(4),
                                      decoration: const BoxDecoration(
                                        color: Colors.white,
                                        shape: BoxShape.circle,
                                      ),
                                      child: const Icon(Icons.close,
                                          size: 16, color: Colors.black),
                                    ),
                                  ),
                                ),
                              ],
                            );
                          },
                        ),
                    ],
                  ),
                );
              },
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      child: ScaffoldMessenger(
        key: _messengerKey,
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
                  'Quotation List',
                  style: TextStyle(color: Colors.white),
                ),
                iconTheme: const IconThemeData(color: Colors.white),
                actions: [
                  IconButton(
                    onPressed: () {
                      refreshLeadList();
                    },
                    icon: const Icon(Icons.refresh, color: Colors.white),
                  ),
                ],
              ),
              body: SafeArea(
                child: Column(
                  children: [
                    if (_empCategory == 1) ...[
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
                              Text(
                                'Total Lead: $_totalLeadCount',
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.black,
                                ),
                              ),
                              Row(
                                children: [
                                  _buildStatBoxForSO(
                                      'PO-Pending', _poPendingCount),
                                  const SizedBox(width: 8),
                                  _buildStatBoxForSO(
                                      'PO-Revision', _poRevisionCount),
                                  const SizedBox(width: 8),
                                  _buildStatBoxForSO(
                                      'Lead Lost', _lostLeadCount),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ),
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
                                                buildDataRow(
                                                    "Exp. Rate per Bag",
                                                    item.expRatePerBag),
                                                buildDataRow(
                                                    "No. of Bags", item.qtyReq),
                                                buildDataRow(
                                                  "Lead Status",
                                                  enriched.leadStatusLabel,
                                                  valueColor:
                                                      enriched.leadStatusColor,
                                                ),
                                                if (item.leadQuotationStatus ==
                                                        '12' &&
                                                    (item.lostOrderReason !=
                                                            null &&
                                                        item.lostOrderReason!
                                                            .isNotEmpty)) ...[
                                                  buildDataRow("Lost Reason",
                                                      item.lostOrderReason),
                                                ],
                                                const SizedBox(height: 10),
                                                Row(
                                                  children: [
                                                    if (item.leadQuotationStatus !=
                                                        '12') ...[
                                                      Expanded(
                                                        child:
                                                            buildCustomButton(
                                                          "Reason of Lost",
                                                          () {
                                                            leadQuotationId = item
                                                                .leadGenerationId;
                                                            _showPopupForLost();
                                                          },
                                                        ),
                                                      ),
                                                      const SizedBox(width: 10),
                                                      Expanded(
                                                        child:
                                                            buildCustomButton(
                                                          "PO Fill-up",
                                                          () {
                                                            leadQuotationId = item
                                                                .leadGenerationId;
                                                            // ── FIXED: safe date parse ──
                                                            poDate = (item.poDate !=
                                                                        null &&
                                                                    item.poDate!
                                                                        .isNotEmpty)
                                                                ? DateTime
                                                                    .tryParse(item
                                                                        .poDate!)
                                                                : null;
                                                            poNumberController
                                                                    .text =
                                                                item.poNumber ??
                                                                    '';
                                                            // ── FIXED: load existing image URL ──
                                                            existingPoImageUrl =
                                                                (item.poImage !=
                                                                            null &&
                                                                        item.poImage!
                                                                            .isNotEmpty)
                                                                    ? item
                                                                        .poImage
                                                                    : null;
                                                            images.clear();
                                                            _showPopupForPO();
                                                          },
                                                        ),
                                                      ),
                                                    ],
                                                    if (item.leadQuotationStatus ==
                                                            '12' &&
                                                        (item.lostOrderReason ==
                                                                null ||
                                                            item.lostOrderReason!
                                                                .isEmpty)) ...[
                                                      Expanded(
                                                        child:
                                                            buildCustomButton(
                                                          "Reason of Lost",
                                                          () {
                                                            leadQuotationId = item
                                                                .leadGenerationId;
                                                            _showPopupForLost();
                                                          },
                                                        ),
                                                      ),
                                                    ],
                                                  ],
                                                ),
                                              ],
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
                    if (_empCategory == 2) ...[
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
                              Text(
                                'Total Lead: $_totalLeadCount',
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.black,
                                ),
                              ),
                              Row(
                                children: [
                                  _buildStatBoxForHOS(
                                      'PO-Pending', _poPendingCount),
                                  const SizedBox(width: 8),
                                  _buildStatBoxForHOS(
                                      'PO-Received', _poReceivedCount),
                                  const SizedBox(width: 8),
                                  _buildStatBoxForHOS(
                                      'PO-Revision', _poRevisionCount),
                                  const SizedBox(width: 8),
                                  _buildStatBoxForHOS(
                                      'Lead\nLost', _lostLeadCount),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ),
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
                                                buildDataRow(
                                                    "Exp. Rate per Bag",
                                                    item.expRatePerBag),
                                                buildDataRow(
                                                    "No. of Bags", item.qtyReq),
                                                buildDataRow(
                                                  "Lead Status",
                                                  enriched.leadStatusLabel,
                                                  valueColor:
                                                      enriched.leadStatusColor,
                                                ),
                                                if (item.leadQuotationStatus ==
                                                        '12' &&
                                                    (item.lostOrderReason !=
                                                            null &&
                                                        item.lostOrderReason!
                                                            .isNotEmpty)) ...[
                                                  buildDataRow("Lost Reason",
                                                      item.lostOrderReason),
                                                ],
                                                const SizedBox(height: 10),
                                                Row(
                                                  children: [
                                                    if (_typeSelected == 2) ...[
                                                      Expanded(
                                                        child:
                                                            buildCustomButton(
                                                          "Check PO Details",
                                                          () {
                                                            leadQuotationId = item
                                                                .leadGenerationId;
                                                            // ── FIXED: safe date parse ──
                                                            poDate = (item.poDate !=
                                                                        null &&
                                                                    item.poDate!
                                                                        .isNotEmpty)
                                                                ? DateTime
                                                                    .tryParse(item
                                                                        .poDate!)
                                                                : null;
                                                            poNumberController
                                                                    .text =
                                                                item.poNumber ??
                                                                    '';
                                                            // ── FIXED: load existing image URL ──
                                                            existingPoImageUrl =
                                                                (item.poImage !=
                                                                            null &&
                                                                        item.poImage!
                                                                            .isNotEmpty)
                                                                    ? item
                                                                        .poImage
                                                                    : null;
                                                            images.clear();
                                                            _showDetailsPopupForPO();
                                                          },
                                                        ),
                                                      ),
                                                      const SizedBox(width: 10),
                                                      Expanded(
                                                        child:
                                                            buildCustomButton(
                                                          "Forward to MIS",
                                                          () {
                                                            leadQuotationId = item
                                                                .leadGenerationId;
                                                            _sendForwardToMISbyHOS();
                                                          },
                                                        ),
                                                      ),
                                                    ],
                                                    if (_typeSelected == 3) ...[
                                                      Expanded(
                                                        child:
                                                            buildCustomButton(
                                                          "Send Back to SO",
                                                          () {
                                                            leadQuotationId = item
                                                                .leadGenerationId;
                                                            _sendSendBackToSObyHOS();
                                                          },
                                                        ),
                                                      ),
                                                    ]
                                                  ],
                                                ),
                                              ],
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
                  ],
                ),
              ),
            ),
            if (_isLoading) ...[
              Container(
                color: Colors.black.withOpacity(0.3),
                child: const Center(child: CircularProgressIndicator()),
              )
            ],
            if (_isLoadingForRefresh) ...[
              Container(
                color: Colors.black.withOpacity(0.3),
                child: Center(
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                        vertical: 20, horizontal: 28),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: const Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        CircularProgressIndicator(color: Colors.red),
                        SizedBox(height: 16),
                        Text(
                          'Refreshing data...',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w500,
                            color: Colors.black87,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              )
            ],
          ],
        ),
      ),
    );
  }
}

// ── Helpers ────────────────────────────────────────────────────────────────────

Widget buildDataRow(String title, String? value, {Color? valueColor}) {
  String capitalize(String v) =>
      v.isEmpty ? v : v[0].toUpperCase() + v.substring(1).toLowerCase();
  String display = '';
  if (title == 'Lead Status') {
    display = (value == null || value.isEmpty) ? 'N/A' : value;
  } else {
    display = (value == null || value.isEmpty) ? 'N/A' : capitalize(value);
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
