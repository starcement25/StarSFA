import 'dart:convert';
import 'dart:developer';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class KhojActivityScreen extends StatefulWidget {
  final bool isOptionSelected;
  const KhojActivityScreen({super.key, this.isOptionSelected = false});

  @override
  State<KhojActivityScreen> createState() => _KhojActivityScreenState();
}

class _KhojActivityScreenState extends State<KhojActivityScreen> {
  String? showInList;
  String? customerName;
  String? cnPhoneNumber;
  String? meetPerson;
  String? mpPhoneNumber;
  String? siteName;
  String? routeName;
  String? branchName;
  String? stateName;
  String? districtName;
  String? fullAddress;
  String? contractorName;
  String? conPhoneNumber;
  String? engineerName;
  String? engPhoneNumber;
  String? isRegister;
  String? siteSegment;
  String? projectSegment;
  String? constructionType;
  String? currentStage;
  String? cementBrand;
  String? sitePotential;
  String? pricePerBag;
  String? consumedTillDate;
  String? requirement;
  String? buildUpArea;
  String? decisionMaker;
  String? productDemo;
  String? visitType;
  String? deliveryDate;
  String? orderQty;
  String? remarks;
  String? dealerName;
  String? approvedBy;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _showFirstDialog();
    });
  }

  void _showFirstDialog() {
    showSelectorDialog<KhojListInfo>(
      fetchData: () => KhojListInfo.fetchDataFromApi(),
      dialogTitle: 'Select Khoj',
      getDisplayText: (item) => item.showInList ?? '',
      onSelected: (item) {
        setState(() {
          showInList = item.showInList;
          customerName = item.customerName;
          cnPhoneNumber = item.cnPhoneNumber;
          meetPerson = item.meetPerson;
          mpPhoneNumber = item.mpPhoneNumber;
          siteName = item.siteName;
          routeName = item.routeName;
          branchName = item.branchName;
          stateName = item.stateName;
          districtName = item.districtName;
          fullAddress = item.fullAddress;
          contractorName = item.contractorName;
          conPhoneNumber = item.conPhoneNumber;
          engineerName = item.engineerName;
          engPhoneNumber = item.engPhoneNumber;
          isRegister = item.isRegister;
          siteSegment = item.siteSegment;
          projectSegment = item.projectSegment;
          constructionType = item.constructionType;
          currentStage = item.currentStage;
          cementBrand = item.cementBrand;
          sitePotential = item.sitePotential;
          pricePerBag = item.pricePerBag;
          consumedTillDate = item.consumedTillDate;
          requirement = item.requirement;
          buildUpArea = item.buildUpArea;
          decisionMaker = item.decisionMaker;
          productDemo = item.productDemo;
          visitType = item.visitType;
          deliveryDate = item.deliveryDate;
          orderQty = item.orderQty;
          remarks = item.remarks;
          dealerName = item.dealerName;
          approvedBy = item.approvedBy;
        });
      },
    );
  }

  void showSelectorDialog<T>({
    required Future<List<T>> Function() fetchData,
    required String dialogTitle,
    required String Function(T) getDisplayText,
    required void Function(T selectedItem) onSelected,
    bool enableSearch = true,
  }) {
    TextEditingController searchController = TextEditingController();
    List<T> allItems = [];
    List<T> filteredItems = [];
    bool isLoading = true;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
            if (isLoading) {
              fetchData().then((items) {
                setState(() {
                  allItems = items;
                  filteredItems = items;
                  isLoading = false;
                });
              }).catchError((error) {
                setState(() {
                  isLoading = false;
                });
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(content: Text('Failed to load data: $error')),
                );
              });
            }

            return Dialog(
              insetPadding: EdgeInsets.zero,
              backgroundColor: Colors.white,
              child: SafeArea(
                child: Scaffold(
                  appBar: AppBar(
                    backgroundColor: Colors.red,
                    title: Text(dialogTitle),
                    leading: IconButton(
                      icon: const Icon(Icons.close),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ),
                  body: Column(
                    children: [
                      if (enableSearch)
                        Padding(
                          padding: const EdgeInsets.all(16),
                          child: TextField(
                            controller: searchController,
                            decoration: const InputDecoration(
                              hintText: 'Search...',
                              prefixIcon: Icon(Icons.search),
                              border: OutlineInputBorder(),
                            ),
                            onChanged: (value) {
                              setState(() {
                                filteredItems = allItems
                                    .where((item) => getDisplayText(item)
                                        .toLowerCase()
                                        .contains(value.toLowerCase()))
                                    .toList();
                              });
                            },
                          ),
                        ),
                      Expanded(
                        child: isLoading
                            ? const Center(child: CircularProgressIndicator())
                            : filteredItems.isEmpty
                                ? const Center(child: Text('No data found'))
                                : ListView.builder(
                                    itemCount: filteredItems.length,
                                    itemBuilder: (context, index) {
                                      final item = filteredItems[index];
                                      return ListTile(
                                        title: Text(getDisplayText(item)),
                                        onTap: () {
                                          onSelected(item);
                                          Navigator.pop(context);
                                        },
                                      );
                                    },
                                  ),
                      ),
                    ],
                  ),
                ),
              ),
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
                  'Activity Report of project Khoj',
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
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelText(
                            label: 'Customer Information',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Cutsomer Name',
                            value: customerName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Phone Number',
                            value: cnPhoneNumber ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelText(
                            label: 'Meetup Person Information',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Meet Person',
                            value: meetPerson ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Phone Number',
                            value: mpPhoneNumber ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelText(
                            label: 'Site Information',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Name',
                            value: siteName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Route',
                            value: routeName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Branch',
                            value: branchName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'State',
                            value: stateName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'District',
                            value: districtName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Address',
                            value: fullAddress ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelText(
                            label: 'Contractor & Engineer Information',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Contractor Name',
                            value: contractorName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Phone Number',
                            value: conPhoneNumber ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Engineer Name',
                            value: engineerName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Phone Number',
                            value: engPhoneNumber ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Is Regd. in Stellar',
                            value: isRegister ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelText(
                            label: 'Segment & Construction Information',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Segment',
                            value: siteSegment ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Project Segment',
                            value: projectSegment ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Construction Type',
                            value: constructionType ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Current Stage',
                            value: currentStage ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Cement Brand',
                            value: cementBrand ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelText(
                            label: 'Site Potential Information',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Potential',
                            value: sitePotential ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Price Per Bag',
                            value: pricePerBag ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Consumed Till Now',
                            value: consumedTillDate ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Requirement',
                            value: requirement ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Built up Area',
                            value: buildUpArea ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Delivery Date',
                            value: deliveryDate ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Order Qty.',
                            value: orderQty ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelText(
                            label: 'Other Information',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Decision Maker',
                            value: decisionMaker ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Product Demo',
                            value: productDemo ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Visit Type',
                            value: visitType ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Remarks',
                            value: remarks ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Dealer/RSSD name',
                            value: dealerName ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Approved By',
                            value: approvedBy ?? '',
                          ),
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              )),
            ),
          ],
        ));
  }
}

class LabelValueText extends StatelessWidget {
  final String label;
  final String value;

  const LabelValueText({
    Key? key,
    required this.label,
    required this.value,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        // Left border line
        Container(
          width: 1,
          height: 40, // Optional: Set height if needed
          color: Colors.black,
        ),

        // First text (30%)
        Expanded(
          flex: 1, // 30%
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8.0),
            child: Text(
              label,
              style: TextStyle(
                  fontSize: 13, color: const Color.fromARGB(255, 57, 57, 57)),
            ),
          ),
        ),

        // Middle border line
        Container(
          width: 1,
          height: 40,
          color: Colors.black,
        ),

        // Second text (70%)
        Expanded(
          flex: 2, // 70%
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8.0),
            child: Text(
              value,
              style: TextStyle(fontSize: 13, color: Colors.black),
            ),
          ),
        ),

        // Last border line
        Container(
          width: 1,
          height: 40,
          color: Colors.black,
        ),
      ],
    );
  }
}

class LabelText extends StatelessWidget {
  final String label;

  const LabelText({
    Key? key,
    required this.label,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        // Left border line
        Container(
          width: 1,
          height: 40, // Optional: Set height if needed
          color: Colors.black,
        ),

        // First text (30%)
        Expanded(
          flex: 1, // 30%
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8.0),
            child: Text(
              label,
              style: TextStyle(
                fontSize: 15,
                color: const Color.fromARGB(255, 0, 0, 0),
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ),

        // Last border line
        Container(
          width: 1,
          height: 40,
          color: Colors.black,
        ),
      ],
    );
  }
}

class KhojListInfo {
  String? showInList;
  String? customerName;
  String? cnPhoneNumber;
  String? meetPerson;
  String? mpPhoneNumber;
  String? siteName;
  String? routeName;
  String? branchName;
  String? stateName;
  String? districtName;
  String? fullAddress;
  String? contractorName;
  String? conPhoneNumber;
  String? engineerName;
  String? engPhoneNumber;
  String? isRegister;
  String? siteSegment;
  String? projectSegment;
  String? constructionType;
  String? currentStage;
  String? cementBrand;
  String? sitePotential;
  String? pricePerBag;
  String? consumedTillDate;
  String? requirement;
  String? buildUpArea;
  String? decisionMaker;
  String? productDemo;
  String? visitType;
  String? deliveryDate;
  String? orderQty;
  String? remarks;
  String? dealerName;
  String? approvedBy;

  KhojListInfo(
      {this.showInList,
      this.customerName,
      this.cnPhoneNumber,
      this.meetPerson,
      this.mpPhoneNumber,
      this.siteName,
      this.routeName,
      this.branchName,
      this.stateName,
      this.districtName,
      this.fullAddress,
      this.contractorName,
      this.conPhoneNumber,
      this.engineerName,
      this.engPhoneNumber,
      this.isRegister,
      this.siteSegment,
      this.projectSegment,
      this.constructionType,
      this.currentStage,
      this.cementBrand,
      this.sitePotential,
      this.pricePerBag,
      this.consumedTillDate,
      this.requirement,
      this.buildUpArea,
      this.decisionMaker,
      this.productDemo,
      this.visitType,
      this.deliveryDate,
      this.orderQty,
      this.remarks,
      this.dealerName,
      this.approvedBy});

  factory KhojListInfo.fromJson(Map<String, dynamic> json) {
    return KhojListInfo(
      showInList: json['visit_list']['district']+' '+json['visit_list']['updated_at'],
      customerName: json['visit_list']['cust_name'],
      cnPhoneNumber: '+91 '+json['visit_list']['site_code'].split('-').first,
      meetPerson: json['visit_list']['meeting_person_type'],
      mpPhoneNumber: '+91 '+json['visit_list']['meeting_person_phone'],
      siteName: json['visit_list']['site_name'],
      routeName: json['visit_list']['route_name'],
      branchName: json['visit_list']['branch_name'],
      stateName: json['visit_list']['state'],
      districtName: json['visit_list']['district'],
      fullAddress: json['visit_list']['address'],
      contractorName: json['visit_list']['contractor_name'],
      conPhoneNumber: '+91 '+json['visit_list']['contractor_phone'],
      engineerName: json['visit_list']['engineer_name'],
      engPhoneNumber: '+91 '+json['visit_list']['engineer_phone'],
      isRegister: json['visit_list']['engg_reg_star_stellar'],
      siteSegment: json['visit_list']['site_segment'],
      projectSegment: json['visit_list']['project_segment'],
      constructionType: json['visit_list']['type_of_construction'],
      currentStage: json['visit_list']['construction_stage'],
      cementBrand: json['visit_list']['cement_brand'],
      sitePotential: json['visit_list']['site_potential']+' Bags',
      pricePerBag: json['visit_list']['price_per_bag']+'/-',
      consumedTillDate: json['visit_list']['consumed_till_date'],
      requirement: json['visit_list']['estimated_req']+' Bags',
      buildUpArea: json['visit_list']['built_up_area']+' Sq. ft',
      decisionMaker: json['visit_list']['decision_maker'],
      productDemo: json['visit_list']['product_demo'],
      visitType: json['visit_list']['visit_type'] +
          ', ' +
          json['visit_list']['visit_sub_type'],
      deliveryDate: json['visit_list']['date_of_delivery'],
      orderQty: json['visit_list']['bags_ordered']+' Bags',
      remarks: json['visit_list']['remarks'],
      dealerName: json['visit_list']['rssd_name'],
      approvedBy: json['visit_list']['approved_by_name'],
    );
  }

  static Future<List<KhojListInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    final user = await UserLoginClass.getLocalUser();
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    log('https://sfa.starcement.co.in/misreport/api_get_list_site_visit.php?emp_code=${user?.empCode}');

    final response = await ioClient.get(
      Uri.parse(
          'https://sfa.starcement.co.in/misreport/api_get_list_site_visit.php?emp_code=${user?.empCode}'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['sites'] != null &&
          jsonResponse['sites'] is List) {
        return (jsonResponse['sites'] as List)
            .map((item) => KhojListInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty Khoj response');
      }
    } else {
      throw Exception('Failed to fetch Khoj list');
    }
  }
}
