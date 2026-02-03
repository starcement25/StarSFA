import 'dart:convert';
import 'dart:developer';
import 'dart:io';

import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/lead_generation/lead_generation_report_details_activity_screen.dart';

class LeadGenerationReportActivityScreen extends StatefulWidget {
  final bool isOptionSelected;
  const LeadGenerationReportActivityScreen(
      {super.key, this.isOptionSelected = false});

  @override
  State<LeadGenerationReportActivityScreen> createState() =>
      _LeadGenerationReportActivityScreenState();
}

class _LeadGenerationReportActivityScreenState
    extends State<LeadGenerationReportActivityScreen> {
  bool _isLoading = true;
  String startDate = '';
  String endDate = '';
  String statusType = 'Pending';
  List<RequestLeadListData>? allLeadListData;
  List<RequestLeadListData>? filterLeadListData;
  List<RequestLeadListData>? showLeadListData;
  DateTime normalize(DateTime d) => DateTime(d.year, d.month, d.day);

  @override
  void initState() {
    super.initState();
    fetchDeclarationData();
  }

  void fetchDeclarationData() async {
    try {
      List<RequestLeadListData> dataSet =
          await RequestLeadListData.fetchDataFromApi('E0555', 'hos');
      setState(() {
        allLeadListData = dataSet;
        filterLeadListData = List.from(dataSet);
        checkAgainstStatus();
      });
    } catch (e) {
      print("Error fetching data: $e");
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  DateTime? parseApiDate(String? date) {
    if (date == null || date.isEmpty) return null;
    try {
      return DateFormat('yyyy-MM-dd').parse(date);
    } catch (_) {
      return null;
    }
  }

  DateTime? parseUiDate(String date) {
    if (date.isEmpty) return null;
    return DateFormat('dd-MM-yyyy').parse(date);
  }

  void checkAgainstStatus() {
    if (allLeadListData == null) return;

    final DateTime? start =
        startDate.isEmpty ? null : normalize(parseUiDate(startDate)!);

    final DateTime? end =
        endDate.isEmpty ? null : normalize(parseUiDate(endDate)!);

    setState(() {
      showLeadListData = allLeadListData!.where((item) {
        // -------- STATUS FILTER --------
        if (statusType.isNotEmpty) {
          if (item.lead_action == null ||
              item.lead_action!.toLowerCase() != statusType.toLowerCase()) {
            return false;
          }
        }

        // -------- DATE FILTER --------
        final apiDate = parseApiDate(item.download_time_date_stamp);
        if (apiDate == null) return false;

        final created = normalize(apiDate);

        // Only start date
        if (start != null && end == null) {
          return !created.isBefore(start);
        }

        // Only end date
        if (start == null && end != null) {
          return !created.isAfter(end);
        }

        // Both dates
        if (start != null && end != null) {
          return !created.isBefore(start) && !created.isAfter(end);
        }

        return true;
      }).toList();
    });
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
                  icon: const Icon(
                    Icons.arrow_back,
                    color: Colors.white,
                  ),
                  onPressed: () {
                    Navigator.of(context).pop();
                  },
                ),
                backgroundColor: Colors.red,
                title: const Text(
                  'Lead Generation',
                  style: TextStyle(color: Colors.white),
                ),
              ),
              body: SafeArea(
                  child: GestureDetector(
                behavior: HitTestBehavior.opaque,
                onTap: () {
                  FocusScope.of(context).unfocus();
                },
                child: Stack(
                  children: [
                    SingleChildScrollView(
                      padding: const EdgeInsets.all(16.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(children: [
                            Expanded(
                              flex: 4,
                              child: Text(
                                "Start Date",
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                            Expanded(
                              flex: 6,
                              child: SizedBox(
                                width: double.infinity,
                                child: OutlinedButton(
                                  onPressed: () async {
                                    DateTime? pickedDate = await showDatePicker(
                                      context: context,
                                      initialDate: DateTime.now(),
                                      firstDate: DateTime(2000),
                                      lastDate: DateTime.now(),
                                    );

                                    if (pickedDate != null) {
                                      String date = "";
                                      if (pickedDate.day < 10) {
                                        date = "0${pickedDate.day}";
                                      } else {
                                        date = "${pickedDate.day}";
                                      }
                                      if (pickedDate.month < 10) {
                                        date = "${date}-0${pickedDate.month}";
                                      } else {
                                        date = "${date}-${pickedDate.month}";
                                      }
                                      date = "${date}-${pickedDate.year}";
                                      setState(() {
                                        startDate = date;
                                      });
                                    }
                                  },
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                        vertical: 10),
                                    side: const BorderSide(color: Colors.grey),
                                    backgroundColor: Colors.white,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  child: Text(
                                    startDate,
                                    style: const TextStyle(
                                      fontSize: 16,
                                      color: Colors.black,
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ]),
                          SizedBox(height: 5),
                          Row(
                            children: [
                              Expanded(
                                flex: 4,
                                child: Text(
                                  "End Date",
                                  style: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                              Expanded(
                                flex: 6,
                                child: SizedBox(
                                  width: double.infinity,
                                  child: OutlinedButton(
                                    onPressed: () async {
                                      DateTime? pickedDate =
                                          await showDatePicker(
                                        context: context,
                                        initialDate: DateTime.now(),
                                        firstDate: DateTime(2000),
                                        lastDate: DateTime.now(),
                                      );

                                      if (pickedDate != null) {
                                        String date = "";
                                        if (pickedDate.day < 10) {
                                          date = "0${pickedDate.day}";
                                        } else {
                                          date = "${pickedDate.day}";
                                        }
                                        if (pickedDate.month < 10) {
                                          date = "${date}-0${pickedDate.month}";
                                        } else {
                                          date = "${date}-${pickedDate.month}";
                                        }
                                        date = "${date}-${pickedDate.year}";
                                        setState(() {
                                          endDate = date;
                                        });
                                      }
                                    },
                                    style: OutlinedButton.styleFrom(
                                      padding: const EdgeInsets.symmetric(
                                          vertical: 10),
                                      side:
                                          const BorderSide(color: Colors.grey),
                                      backgroundColor: Colors.white,
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(4),
                                      ),
                                    ),
                                    child: Text(
                                      endDate,
                                      style: const TextStyle(
                                        fontSize: 16,
                                        color: Colors.black,
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          SizedBox(height: 5),
                          Row(
                            children: [
                              Expanded(
                                flex: 1,
                                child: SizedBox(height: 10),
                              ),
                              Expanded(
                                flex: 6,
                                child: SizedBox(
                                  width: double.infinity,
                                  child: OutlinedButton(
                                    onPressed: () {
                                      checkAgainstStatus();
                                    },
                                    style: OutlinedButton.styleFrom(
                                      padding: const EdgeInsets.symmetric(
                                          vertical: 10),
                                      side:
                                          const BorderSide(color: Colors.grey),
                                      backgroundColor: Colors.red,
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(4),
                                      ),
                                    ),
                                    child: Text(
                                      "Search",
                                      style: const TextStyle(
                                        fontSize: 16,
                                        color:
                                            Color.fromARGB(255, 255, 255, 255),
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                              Expanded(
                                flex: 1,
                                child: SizedBox(height: 10),
                              ),
                            ],
                          ),
                          SizedBox(height: 8),
                          Row(children: [
                            Expanded(
                              flex: 1,
                              child: SizedBox(
                                width: double.infinity,
                                child: OutlinedButton(
                                  onPressed: () {
                                    setState(() {
                                      statusType = "pending";
                                    });
                                    checkAgainstStatus();
                                  },
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                        vertical: 10),
                                    side: const BorderSide(color: Colors.grey),
                                    backgroundColor: statusType.toLowerCase() ==
                                            'Pending'.toLowerCase()
                                        ? Colors.red
                                        : Colors.grey,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  child: Text(
                                    "Pending",
                                    style: const TextStyle(
                                      fontSize: 16,
                                      color: Color.fromARGB(255, 255, 255, 255),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            SizedBox(width: 5),
                            Expanded(
                              flex: 1,
                              child: SizedBox(
                                width: double.infinity,
                                child: OutlinedButton(
                                  onPressed: () {
                                    setState(() {
                                      statusType = "hold";
                                    });
                                    checkAgainstStatus();
                                  },
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                        vertical: 10),
                                    side: const BorderSide(color: Colors.grey),
                                    backgroundColor: statusType.toLowerCase() ==
                                            'Hold'.toLowerCase()
                                        ? Colors.red
                                        : Colors.grey,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  child: Text(
                                    "Hold",
                                    style: const TextStyle(
                                      fontSize: 16,
                                      color: Color.fromARGB(255, 255, 255, 255),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            SizedBox(width: 5),
                            Expanded(
                              flex: 1,
                              child: SizedBox(
                                width: double.infinity,
                                child: OutlinedButton(
                                  onPressed: () {
                                    setState(() {
                                      statusType = "yes";
                                    });
                                    checkAgainstStatus();
                                  },
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                        vertical: 10),
                                    side: const BorderSide(color: Colors.grey),
                                    backgroundColor: statusType.toLowerCase() ==
                                            'yes'.toLowerCase()
                                        ? Colors.red
                                        : Colors.grey,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  child: Text(
                                    "Accept",
                                    style: const TextStyle(
                                      fontSize: 16,
                                      color: Color.fromARGB(255, 255, 255, 255),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            SizedBox(width: 5),
                            Expanded(
                              flex: 1,
                              child: SizedBox(
                                width: double.infinity,
                                child: OutlinedButton(
                                  onPressed: () {
                                    setState(() {
                                      statusType = "no";
                                    });
                                    checkAgainstStatus();
                                  },
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                        vertical: 10),
                                    side: const BorderSide(color: Colors.grey),
                                    backgroundColor: statusType.toLowerCase() ==
                                            'no'.toLowerCase()
                                        ? Colors.red
                                        : Colors.grey,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  child: Text(
                                    "Reject",
                                    style: const TextStyle(
                                      fontSize: 16,
                                      color: Color.fromARGB(255, 255, 255, 255),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ]),
                          Container(
                            padding: const EdgeInsets.all(10),
                            child: (showLeadListData == null ||
                                    showLeadListData!.isEmpty)
                                ? const Center(child: Text('No data found'))
                                : ListView.builder(
                                    shrinkWrap: true, // IMPORTANT
                                    physics:
                                        const NeverScrollableScrollPhysics(), // IMPORTANT
                                    itemCount: showLeadListData!.length,
                                    itemBuilder: (context, index) {
                                      final siteLeadData =
                                          showLeadListData![index];
                                      return Card(
                                        elevation: 6,
                                        margin:
                                            const EdgeInsets.only(bottom: 12),
                                        shape: RoundedRectangleBorder(
                                          borderRadius:
                                              BorderRadius.circular(16),
                                        ),
                                        color: Colors.white,
                                        child: Padding(
                                          padding: const EdgeInsets.all(16),
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              buildDataRow(
                                                  "Lead Id",
                                                  siteLeadData
                                                      .lead_generation_id),
                                              buildDataRow(
                                                  "Contact Persion Name",
                                                  siteLeadData
                                                      .contact_person_name),
                                              buildDataRow("Contact Number",
                                                  siteLeadData.contact_number),
                                              buildDataRow(
                                                  "Sold to Party",
                                                  siteLeadData
                                                      .sold_to_party_details_name),
                                              buildDataRow(
                                                  "Ship to Party",
                                                  siteLeadData
                                                      .ship_to_party_details_name),
                                              buildDataRow("Next Visit Date",
                                                  siteLeadData.next_visit_date),
                                              buildDataRow(
                                                  "Exp. Rate per Bag",
                                                  siteLeadData
                                                      .exp_rate_per_bag),
                                              buildDataRow("No of Bag Order",
                                                  siteLeadData.qty_req),
                                              buildDataRow("Lead Status",
                                                  siteLeadData.lead_status),
                                              const SizedBox(height: 10),
                                              buildCustomButton(
                                                "View Details",
                                                () async {
                                                  final result =
                                                      await Navigator.push(
                                                    context,
                                                    MaterialPageRoute(
                                                      builder: (_) =>
                                                          LeadGenerationReportDetailsActivityScreen(
                                                        siteLeadData:
                                                            siteLeadData!,
                                                      ),
                                                    ),
                                                  );
                                                  if (result == true) {
                                                    fetchDeclarationData();
                                                  }
                                                },
                                              ),
                                            ],
                                          ),
                                        ),
                                      );
                                    },
                                  ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              )),
            ),
            if (_isLoading)
              Container(
                color: Colors.black.withOpacity(0.3),
                child: const Center(
                  child: CircularProgressIndicator(),
                ),
              ),
          ],
        ));
  }
}

Widget buildDataRow(String title, String? value) {
  String capitalizeFirst(String value) {
    if (value.isEmpty) return value;
    return value[0].toUpperCase() + value.substring(1).toLowerCase();
  }

  final displayValue =
      (value == null || value.isEmpty) ? "N/A" : capitalizeFirst(value);
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
            displayValue ?? "N/A",
            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
          ),
        ),
      ],
    ),
  );
}

Widget buildCustomButton(String? label1, VoidCallback? onPressed1) {
  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 5.0),
    child: Row(
      children: [
        Expanded(
          child: ElevatedButton(
            onPressed: onPressed1,
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 12),
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              textStyle:
                  const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
            ),
            child: Text(label1!),
          ),
        ),
      ],
    ),
  );
}

class RequestLeadListData {
  String? lead_generation_id;
  String? emp_details_emp_name;
  String? download_time_date_stamp;
  String? download_time_time_stamp;
  String? latitude;
  String? longitude;

  String? sold_to_party_details_name;
  String? sold_to_party_code;
  String? sold_to_party_details_address;
  String? sold_to_party_details_state;
  String? sold_to_party_details_districts;

  String? ship_to_party_details_name;
  String? ship_to_party;
  String? ship_to_party_details_address;
  String? ship_to_party_details_state;
  String? ship_to_party_details_districts;

  String? type_lead; //Segment
  String? lead_type; //Lead Source
  String? product_packaging; //Product & Packaging

  String? qty_req;
  String? month_qty;
  String? current_brand_used;
  String? exp_rate_per_bag;
  String? current_price;
  String? current_price_competitor;

  String? contact_person_name;
  String? designation;
  String? contact_number;
  String? mail_id;

  String? mode; //Mode of Payment
  String? credit_terms; //Credit Terms
  String? acc_block_is_required; //AAC Block is Required or Not
  String? category_type_construction; //Category type of Construction
  String? lead_status; //Lead Status
  String? next_visit_date; //Next Visit Date
  String? incoterms; //Requirement Type
  String? serving_location;

  String? lead_remarks; // Remarks

  String? assigned_to;
  String? assigned_to_details_emp_name; //Assigned To Name
  String? r_timing; //Requirement Timing
  String? lead_action; //lead action

  RequestLeadListData({
    this.lead_generation_id,
    this.emp_details_emp_name,
    this.download_time_date_stamp,
    this.download_time_time_stamp,
    this.latitude,
    this.longitude,
    this.sold_to_party_details_name,
    this.sold_to_party_code,
    this.sold_to_party_details_address,
    this.sold_to_party_details_state,
    this.sold_to_party_details_districts,
    this.ship_to_party_details_name,
    this.ship_to_party,
    this.ship_to_party_details_address,
    this.ship_to_party_details_state,
    this.ship_to_party_details_districts,
    this.type_lead,
    this.lead_type,
    this.product_packaging,
    this.qty_req,
    this.month_qty,
    this.current_brand_used,
    this.exp_rate_per_bag,
    this.current_price,
    this.current_price_competitor,
    this.contact_person_name,
    this.designation,
    this.contact_number,
    this.mail_id,
    this.mode,
    this.credit_terms,
    this.acc_block_is_required,
    this.category_type_construction,
    this.lead_status,
    this.next_visit_date,
    this.incoterms,
    this.serving_location,
    this.lead_remarks,
    this.assigned_to,
    this.assigned_to_details_emp_name,
    this.r_timing,
    this.lead_action,
  });

  factory RequestLeadListData.fromJson(Map<String, dynamic> json) {
    final downloadTime = json['download_time']?.toString();
    String? datePart;
    String? timePart;
    if (downloadTime != null && downloadTime.contains('T')) {
      final parts = downloadTime.split('T');
      datePart = parts.isNotEmpty ? parts[0] : null;
      timePart = parts.length > 1 ? parts[1] : null;
    }
    return RequestLeadListData(
      lead_generation_id: json['lead_generation_id'] ?? '',
      emp_details_emp_name: json['emp_details']?['emp_name'] ?? '',
      download_time_date_stamp: datePart ?? '',
      download_time_time_stamp: timePart ?? '',
      latitude: json['latitude']?.toString() ?? '',
      longitude: json['longitude']?.toString() ?? '',
      sold_to_party_details_name: json['sold_to_party_details']?['name'] ?? '',
      sold_to_party_code: json['sold_to_party'] ?? '',
      sold_to_party_details_address:
          json['sold_to_party_details']?['address'] ?? '',
      sold_to_party_details_state:
          json['sold_to_party_details']?['state'] ?? '',
      sold_to_party_details_districts:
          json['sold_to_party_details']?['districts'] ?? '',
      ship_to_party_details_name: json['ship_to_party_details']?['name'] ?? '',
      ship_to_party: json['ship_to_party'] ?? '',
      ship_to_party_details_address:
          json['ship_to_party_details']?['address'] ?? '',
      ship_to_party_details_state:
          json['ship_to_party_details']?['state'] ?? '',
      ship_to_party_details_districts:
          json['ship_to_party_details']?['districts'] ?? '',
      type_lead: json['type_lead'] ?? '',
      lead_type: json['lead_type'] ?? '',
      product_packaging: json['product_packaging'] ?? '',
      qty_req: json['qty_req'] ?? '',
      month_qty: json['month_qty'] ?? '',
      current_brand_used: json['current_brand_used'] ?? '',
      exp_rate_per_bag: json['exp_rate_per_bag'] ?? '',
      current_price: json['current_price'] ?? '',
      current_price_competitor: json['current_price_competitor'] ?? '',
      contact_person_name: json['contact_person_name'] ?? '',
      designation: json['designation'] ?? '',
      contact_number: json['contact_number'] ?? '',
      mail_id: json['mail_id'] ?? '',
      mode: json['mode'] ?? '',
      credit_terms: json['credit_terms'] ?? '',
      acc_block_is_required: json['acc_block_is_required'] ?? '',
      category_type_construction: json['category_type_construction'] ?? '',
      lead_status: json['lead_status'] ?? '',
      next_visit_date: json['next_visit_date'] ?? '',
      incoterms: json['incoterms'] ?? '',
      serving_location: json['serving_location'] ?? '',
      lead_remarks: json['lead_remarks'] ?? '',
      assigned_to: json['assigned_to'] ?? '',
      assigned_to_details_emp_name:
          json['assigned_to_details']?['emp_name'] ?? '',
      r_timing: json['r_timing'] ?? '',
      lead_action: json['lead_action'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'lead_generation_id': lead_generation_id,
      'emp_details_emp_name': emp_details_emp_name,
      'download_time_date_stamp': download_time_date_stamp,
      'download_time_time_stamp': download_time_time_stamp,
      'latitude': latitude,
      'longitude': longitude,
      'sold_to_party_details_name': sold_to_party_details_name,
      'sold_to_party_code': sold_to_party_code,
      'sold_to_party_details_address': sold_to_party_details_address,
      'sold_to_party_details_state': sold_to_party_details_state,
      'sold_to_party_details_districts': sold_to_party_details_districts,
      'ship_to_party_details_name': ship_to_party_details_name,
      'ship_to_party': ship_to_party,
      'ship_to_party_details_address': ship_to_party_details_address,
      'ship_to_party_details_state': ship_to_party_details_state,
      'ship_to_party_details_districts': ship_to_party_details_districts,
      'type_lead': type_lead,
      'lead_type': lead_type,
      'product_packaging': product_packaging,
      'qty_req': qty_req,
      'month_qty': month_qty,
      'current_brand_used': current_brand_used,
      'exp_rate_per_bag': exp_rate_per_bag,
      'current_price': current_price,
      'current_price_competitor': current_price_competitor,
      'contact_person_name': contact_person_name,
      'designation': designation,
      'contact_number': contact_number,
      'mail_id': mail_id,
      'mode': mode,
      'credit_terms': credit_terms,
      'acc_block_is_required': acc_block_is_required,
      'category_type_construction': category_type_construction,
      'lead_status': lead_status,
      'next_visit_date': next_visit_date,
      'incoterms': incoterms,
      'lead_remarks': lead_remarks,
      'assigned_to': assigned_to,
      'assigned_to_details_emp_name': assigned_to_details_emp_name,
      'r_timing': r_timing,
      'lead_action': lead_action
    };
  }

  static Future<List<RequestLeadListData>> fetchDataFromApi(
      String empCode, String typeOfUser) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }

    String url = 'https://ntquotation.myvtd.site/api/leadmaster/';
    if (typeOfUser == 'hos') {
      url = url + '?assigned_to=' + empCode;
    } else {
      url = url + '?emp_code=' + empCode;
    }
    log(url);
    final response = await http.get(Uri.parse(url));
    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse is List) {
        return jsonResponse
            .map((item) => RequestLeadListData.fromJson(item))
            .toList();
      } else if (jsonResponse is Map<String, dynamic>) {
        // API sometimes returns a single object
        return [RequestLeadListData.fromJson(jsonResponse)];
      } else {
        return [];
      }
    } else {
      throw Exception('Failed to load customer list');
    }
  }
}
