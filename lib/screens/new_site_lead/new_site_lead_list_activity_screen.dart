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
import 'package:starsfa/screens/new_site_lead/new_site_lead_list_details_activity_screen.dart';

class NewSiteLeadListActivityScreen extends StatefulWidget {
  final bool isOptionSelected;
  const NewSiteLeadListActivityScreen(
      {super.key, this.isOptionSelected = false});

  @override
  State<NewSiteLeadListActivityScreen> createState() =>
      _NewSiteLeadListActivityScreen();
}

class _NewSiteLeadListActivityScreen
    extends State<NewSiteLeadListActivityScreen> {
  bool _isLoading = false;
  String startDate = '';
  String endDate = '';
  String statusType = 'Pending';
  List<SiteLeadDataList>? allSiteLeadData;
  List<SiteLeadDataList>? filterSiteLeadData;
  List<SiteLeadDataList>? showSiteLeadData;

  @override
  void initState() {
    super.initState();
    fetchDeclarationData();
  }

  @override
  void dispose() {
    super.dispose();
  }

  void fetchDeclarationData() async {
    try {
      List<SiteLeadDataList> dataSet =
          await SiteLeadDataList.fetchDataFromApi();
      setState(() {
        allSiteLeadData = dataSet;
        filterSiteLeadData = List.from(dataSet);
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
      return DateFormat('yyyy-MM-dd HH:mm:ss').parse(date);
    } catch (_) {
      return null;
    }
  }

  DateTime? parseUiDate(String date) {
    if (date.isEmpty) return null;
    return DateFormat('dd-MM-yyyy').parse(date);
  }

  DateTime normalize(DateTime d) => DateTime(d.year, d.month, d.day);
  void checkAgainstStatus() {
    if (allSiteLeadData == null) return;

    final DateTime? start =
        startDate.isEmpty ? null : normalize(parseUiDate(startDate)!);

    final DateTime? end =
        endDate.isEmpty ? null : normalize(parseUiDate(endDate)!);

    setState(() {
      showSiteLeadData = allSiteLeadData!.where((item) {
        // -------- STATUS FILTER --------
        if (statusType.isNotEmpty) {
          if (item.approval_status == null ||
              item.approval_status!.toLowerCase() != statusType.toLowerCase()) {
            return false;
          }
        }

        // -------- DATE FILTER --------
        final apiDate = parseApiDate(item.created_at);
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
                  'New Site Lead List',
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
                      padding: const EdgeInsets.all(10.0),
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
                                      statusType = "Pending";
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
                                      statusType = "Approved";
                                    });
                                    checkAgainstStatus();
                                  },
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                        vertical: 10),
                                    side: const BorderSide(color: Colors.grey),
                                    backgroundColor: statusType.toLowerCase() ==
                                            'Approved'.toLowerCase()
                                        ? Colors.red
                                        : Colors.grey,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  child: Text(
                                    "Approved",
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
                                      statusType = "Rejected";
                                    });
                                    checkAgainstStatus();
                                  },
                                  style: OutlinedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                        vertical: 10),
                                    side: const BorderSide(color: Colors.grey),
                                    backgroundColor: statusType.toLowerCase() ==
                                            'Rejected'.toLowerCase()
                                        ? Colors.red
                                        : Colors.grey,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  child: Text(
                                    "Rejected",
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
                            child: (showSiteLeadData == null ||
                                    showSiteLeadData!.isEmpty)
                                ? const Center(child: Text('No data found'))
                                : ListView.builder(
                                    shrinkWrap: true, // IMPORTANT
                                    physics:
                                        const NeverScrollableScrollPhysics(), // IMPORTANT
                                    itemCount: showSiteLeadData!.length,
                                    itemBuilder: (context, index) {
                                      final siteLeadData =
                                          showSiteLeadData![index];
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
                                              buildDataRow("Customer Name",
                                                  siteLeadData.cust_name),
                                              buildDataRow("Contact No",
                                                  siteLeadData.cust_phn_no),
                                              buildDataRow("Address",
                                                  siteLeadData.address),
                                              buildDataRow("Dealer Name",
                                                  siteLeadData.counter_name),
                                              buildDataRow("Dealer Code",
                                                  siteLeadData.counter_code),
                                              buildDataRow("Visit type",
                                                  siteLeadData.visit_type),
                                              buildDataRow("Product",
                                                  siteLeadData.select_product),
                                              buildDataRow(
                                                  "No of Bag",
                                                  siteLeadData
                                                      .no_of_bags_ordered),
                                              buildDataRow("Request Date",
                                                  siteLeadData.requested_date),
                                              buildDataRow("Status",
                                                  siteLeadData.approval_status),
                                              const SizedBox(height: 10),
                                              buildCustomButton(
                                                "View Details",
                                                () async {
                                                  final result = await Navigator.push(
                                                    context,
                                                    MaterialPageRoute(
                                                      builder: (_) => NewSiteLeadListDetailsActivityScreen(
                                                        siteLeadData: siteLeadData!,
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

class SiteLeadDataList {
  String? transaction_id;
  String? unique_id;
  String? visit_date;
  String? emp_code;
  String? emp_name;
  String? zone;
  String? branch;
  String? district;
  String? state;
  String? longitude;
  String? latitude;
  String? cust_name;
  String? cust_phn_no;
  String? address;
  String? site_segment;
  String? visit_type;
  String? project_segment;
  String? type_of_const;
  String? built_up_area;
  String? no_of_bag;
  String? conversion;
  String? site_priority;
  String? counter_code;
  String? created_at;
  String? updated_at;
  String? new_site_lead_id;
  String? new_site_lead_unique_id;
  String? petty_contractor_registered;
  String? head_mason_name;
  String? contractor_id;
  String? head_mason_contact;
  String? engg_registered;
  String? engg_name;
  String? engg_id;
  String? engg_contact;
  String? meeting_person;
  String? decision_maker;
  String? current_stage_of_construction;
  String? site_potential;
  String? consumed_till_date;
  String? balance_potential;
  String? site_category;
  String? brand_used;
  String? price_per_bag;
  String? select_product;
  String? no_of_bags_ordered;
  String? requested_date;
  String? counter_type;
  String? counter_name;
  String? reason_for_non_conversion;
  String? weather_shield_demo;
  String? approval_status;
  String? approval_date_time;
  String? asm_name;
  String? asm_id;
  String? actual_date_of_delivery;
  String? delivery_remarks;
  String? reason_for_not_delivery;
  String? site_status;
  String? floor_count;
  String? balance_potential_manual;
  String? remarks;

  SiteLeadDataList({
    this.transaction_id,
    this.unique_id,
    this.visit_date,
    this.emp_code,
    this.emp_name,
    this.zone,
    this.branch,
    this.district,
    this.state,
    this.longitude,
    this.latitude,
    this.cust_name,
    this.cust_phn_no,
    this.address,
    this.site_segment,
    this.visit_type,
    this.project_segment,
    this.type_of_const,
    this.built_up_area,
    this.no_of_bag,
    this.conversion,
    this.site_priority,
    this.counter_code,
    this.created_at,
    this.updated_at,
    this.new_site_lead_id,
    this.new_site_lead_unique_id,
    this.petty_contractor_registered,
    this.head_mason_name,
    this.contractor_id,
    this.head_mason_contact,
    this.engg_registered,
    this.engg_name,
    this.engg_id,
    this.engg_contact,
    this.meeting_person,
    this.decision_maker,
    this.current_stage_of_construction,
    this.site_potential,
    this.consumed_till_date,
    this.balance_potential,
    this.site_category,
    this.brand_used,
    this.price_per_bag,
    this.select_product,
    this.no_of_bags_ordered,
    this.requested_date,
    this.counter_type,
    this.counter_name,
    this.reason_for_non_conversion,
    this.weather_shield_demo,
    this.approval_status,
    this.approval_date_time,
    this.asm_name,
    this.asm_id,
    this.actual_date_of_delivery,
    this.delivery_remarks,
    this.reason_for_not_delivery,
    this.site_status,
    this.floor_count,
    this.balance_potential_manual,
    this.remarks,
  });

  factory SiteLeadDataList.fromLine(String line) {
    return SiteLeadDataList(
      transaction_id: line.split('^')[1],
      unique_id: line.split('^')[2],
      visit_date: line.split('^')[3],
      emp_code: line.split('^')[4],
      emp_name: line.split('^')[5],
      zone: line.split('^')[6],
      branch: line.split('^')[7],
      district: line.split('^')[8],
      state: line.split('^')[9],
      longitude: line.split('^')[10],
      latitude: line.split('^')[11],
      cust_name: line.split('^')[12],
      cust_phn_no: line.split('^')[13],
      address: line.split('^')[14],
      site_segment: line.split('^')[15],
      visit_type: line.split('^')[16],
      project_segment: line.split('^')[17],
      type_of_const: line.split('^')[18],
      built_up_area: line.split('^')[19],
      no_of_bag: line.split('^')[20],
      conversion: line.split('^')[21],
      site_priority: line.split('^')[22],
      counter_code: line.split('^')[23],
      created_at: line.split('^')[24],
      updated_at: line.split('^')[25],
      new_site_lead_id: line.split('^')[26],
      new_site_lead_unique_id: line.split('^')[27],
      petty_contractor_registered: line.split('^')[28],
      head_mason_name: line.split('^')[29],
      contractor_id: line.split('^')[30],
      head_mason_contact: line.split('^')[31],
      engg_registered: line.split('^')[32],
      engg_name: line.split('^')[33],
      engg_id: line.split('^')[34],
      engg_contact: line.split('^')[35],
      meeting_person: line.split('^')[36],
      decision_maker: line.split('^')[37],
      current_stage_of_construction: line.split('^')[38],
      site_potential: line.split('^')[39],
      consumed_till_date: line.split('^')[40],
      balance_potential: line.split('^')[41],
      site_category: line.split('^')[42],
      brand_used: line.split('^')[43],
      price_per_bag: line.split('^')[44],
      select_product: line.split('^')[45],
      no_of_bags_ordered: line.split('^')[46],
      requested_date: line.split('^')[47],
      counter_type: line.split('^')[48],
      counter_name: line.split('^')[49],
      reason_for_non_conversion: line.split('^')[50],
      weather_shield_demo: line.split('^')[51],
      approval_status: line.split('^')[52],
      approval_date_time: line.split('^')[53],
      asm_name: line.split('^')[54],
      asm_id: line.split('^')[55],
      actual_date_of_delivery: line.split('^')[57],
      delivery_remarks: line.split('^')[58],
      reason_for_not_delivery: line.split('^')[59],
      site_status: line.split('^')[60],
      floor_count: line.split('^')[61],
      balance_potential_manual: line.split('^')[62],
      remarks: line.split('^')[63],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'transaction_id': transaction_id,
      'unique_id': unique_id,
      'visit_date': visit_date,
      'emp_code': emp_code,
      'emp_name': emp_name,
      'zone': zone,
      'branch': branch,
      'district': district,
      'state': state,
      'longitude': longitude,
      'latitude': latitude,
      'cust_name': cust_name,
      'cust_phn_no': cust_phn_no,
      'address': address,
      'site_segment': site_segment,
      'visit_type': visit_type,
      'project_segment': project_segment,
      'type_of_const': type_of_const,
      'built_up_area': built_up_area,
      'no_of_bag': no_of_bag,
      'conversion': conversion,
      'site_priority': site_priority,
      'counter_code': counter_code,
      'created_at': created_at,
      'updated_at': updated_at,
      'new_site_lead_id': new_site_lead_id,
      'new_site_lead_unique_id': new_site_lead_unique_id,
      'petty_contractor_registered': petty_contractor_registered,
      'head_mason_name': head_mason_name,
      'contractor_id': contractor_id,
      'head_mason_contact': head_mason_contact,
      'engg_registered': engg_registered,
      'engg_name': engg_name,
      'engg_id': engg_id,
      'engg_contact': engg_contact,
      'meeting_person': meeting_person,
      'decision_maker': decision_maker,
      'current_stage_of_construction': current_stage_of_construction,
      'site_potential': site_potential,
      'consumed_till_date': consumed_till_date,
      'balance_potential': balance_potential,
      'site_category': site_category,
      'brand_used': brand_used,
      'price_per_bag': price_per_bag,
      'select_product': select_product,
      'no_of_bags_ordered': no_of_bags_ordered,
      'requested_date': requested_date,
      'counter_type': counter_type,
      'counter_name': counter_name,
      'reason_for_non_conversion': reason_for_non_conversion,
      'weather_shield_demo': weather_shield_demo,
      'approval_status': approval_status,
      'approval_date_time': approval_date_time,
      'asm_name': asm_name,
      'asm_id': asm_id,
      'actual_date_of_delivery': actual_date_of_delivery,
      'delivery_remarks': delivery_remarks,
      'reason_for_not_delivery': reason_for_not_delivery,
      'site_status': site_status,
      'floor_count': floor_count,
      'balance_potential_manual': balance_potential_manual,
      'remarks': remarks,
    };
  }

  static Future<List<SiteLeadDataList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "devsfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();

    final response = await ioClient.get(
      Uri.parse(
          "https://sfa.starcement.co.in/misreport/api_get_asm_reqst_site_lead.php?asm_id=${user?.empCode}"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) =>
              line.trim().isNotEmpty &&
              !line.contains("¥") &&
              !line.contains("#"))
          .map((line) => SiteLeadDataList.fromLine(line))
          .toList();

      log('Length : ' + employees.length.toString());

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}
