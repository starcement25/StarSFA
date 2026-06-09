import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/screens/new_site_lead/new_site_lead_list_activity_screen.dart';

class NewSiteLeadListDetailsActivityScreen extends StatefulWidget {
  final SiteLeadDataList siteLeadData;

  const NewSiteLeadListDetailsActivityScreen({
    super.key,
    required this.siteLeadData,
  });

  @override
  State<NewSiteLeadListDetailsActivityScreen> createState() =>
      _NewSiteLeadListDetailsActivityScreen();
}

class _NewSiteLeadListDetailsActivityScreen
    extends State<NewSiteLeadListDetailsActivityScreen> {
  bool _isLoading = false;
  String? statusUpdatePopupStatus = '';
  String? status = '';
  String? statusUpdatePopupActualDateOfDelivery = '';
  TextEditingController statusUpdatePopupDeliveryRemarksController =
      TextEditingController();
  TextEditingController statusUpdatePopupReasonForNotDeliveryController =
      TextEditingController();
  // TextEdit String
  String? statusUpdatePopupDeliveryRemarks = '';
  String? statusUpdatePopupReasonForNotDelivery = '';

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  void showSelectorDialog<T>({
    required Future<List<T>> Function() fetchData,
    required String dialogTitle,
    required String Function(T) getDisplayText,
    required void Function(T selectedItem) onSelected,
    required bool enableSearch,
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
                // ignore: use_build_context_synchronously
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
                    title: Text(
                      dialogTitle,
                      style: TextStyle(color: Colors.white),
                    ),
                    leading: IconButton(
                      icon: const Icon(
                        Icons.close,
                        color: Colors.white,
                      ),
                      onPressed: () => Navigator.pop(context, true),
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
                                          Navigator.pop(context, true);
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

  Future<void> _requestForUpdateStatusOfSiteLead() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final Map<String, String> object = {
        "site_id": widget.siteLeadData.unique_id!.trim(),
        "approval_status": statusUpdatePopupStatus!.trim(),
        "actual_date_of_delivery":
            statusUpdatePopupActualDateOfDelivery!.trim(),
        "delivery_remarks":
            statusUpdatePopupDeliveryRemarksController.text.trim(),
        "reason_for_not_delivery":
            statusUpdatePopupReasonForNotDeliveryController.text.trim()
      };

      print("Site Lead Send Data : ${jsonEncode(object)}");

      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}misreport/api_asm_approve_site_lead.php'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(object),
      );
      print("Status Code: ${response.statusCode}");
      print("Response Body: ${response.body}");

      if (response.statusCode == 200) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _showSnackBar('Successfully Updated Status Site Lead.');
        // ignore: use_build_context_synchronously
        Navigator.pop(context, true);
      } else {
        setState(() {
          _isLoading = false;
        });
        final responseData = jsonDecode(response.body);
        _showSnackBar(responseData.error);
      }
      // ignore: empty_catches
    } catch (e) {}
  }

  void _showSnackBar(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: Colors.red,
      ),
    );
  }

  void _showApprovalDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setStateDialog) {
            return AlertDialog(
              title: Text('Update Site Lead Status'),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Site Status
                    SelectButtonWithLabel(
                      buttonLabel: 'Status',
                      onPressed: () {
                        showSelectorDialog<ApprovalStatusList>(
                          fetchData: () =>
                              ApprovalStatusList.fetchDataFromApi(),
                          dialogTitle: 'Select Approval Status',
                          getDisplayText: (item) => item.title ?? '',
                          enableSearch: true,
                          onSelected: (item) {
                            setStateDialog(() {
                              statusUpdatePopupStatus = item.title;
                              status = item.title;
                            });
                          },
                        );
                      },
                      value: statusUpdatePopupStatus,
                      isMandatory: true,
                    ),
                    SizedBox(height: 7),
                    if ((status ?? '').toLowerCase() == 'approved') ...[
                      // Actual Date of Delivery
                      SelectButtonWithLabel(
                        buttonLabel: 'Actual Date of Delivery',
                        onPressed: () async {
                          DateTime today = DateTime.now();
                          DateTime? pickedDate = await showDatePicker(
                            context: context,
                            initialDate: DateTime.now(),
                            firstDate:
                                DateTime(today.year, today.month, today.day),
                            lastDate: DateTime(2100),
                          );
                          if (pickedDate != null) {
                            setStateDialog(() {
                              statusUpdatePopupActualDateOfDelivery =
                                  "${pickedDate.year}-${pickedDate.month}-${pickedDate.day}";
                            });
                          }
                        },
                        value: statusUpdatePopupActualDateOfDelivery,
                        isMandatory: true,
                      ),
                      SizedBox(height: 7),
                      // Delivery Remarks
                      LabeledTextField(
                        controller: statusUpdatePopupDeliveryRemarksController,
                        hintText: 'Delivery Remarks',
                        label: 'Delivery Remarks',
                        keyboardType: TextInputType.name,
                        isEditable: true,
                        initialValue: statusUpdatePopupDeliveryRemarks,
                        isMandatory: false,
                      ),
                      SizedBox(height: 7),
                    ],
                    if ((status ?? '').toLowerCase() == 'rejected') ...[
                      // Reason for Not Delivery
                      LabeledTextField(
                        controller:
                            statusUpdatePopupReasonForNotDeliveryController,
                        hintText: 'Reason for Not Delivery',
                        label: 'Reason for Not Delivery',
                        keyboardType: TextInputType.name,
                        isEditable: true,
                        initialValue: statusUpdatePopupReasonForNotDelivery,
                        isMandatory: false,
                      ),
                      SizedBox(height: 7),
                    ],
                  ],
                ),
              ),
              actions: [
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () {
                          Navigator.of(context).pop();
                        },
                        style: OutlinedButton.styleFrom(
                          side: const BorderSide(color: Colors.red),
                        ),
                        child: const Text(
                          'Close',
                          style: TextStyle(color: Colors.red),
                        ),
                      ),
                    ),
                    SizedBox(width: 10),
                    Expanded(
                      child: ElevatedButton(
                        onPressed: () {
                          if (status!.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content:
                                    Text("Please select a Site Lead Status"),
                                backgroundColor: Colors.red,
                              ),
                            );
                          } else {
                            Navigator.of(context).pop();
                            _requestForUpdateStatusOfSiteLead();
                          }
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.red,
                        ),
                        child: const Text('Update'),
                      ),
                    ),
                  ],
                ),
              ],
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
                  'New Site Lead Details',
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
                          // Transaction I'd
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Transaction I\'d',
                            value: widget.siteLeadData.transaction_id ?? '',
                          ),
                          // Unique Site I'd
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Unique Site I\'d',
                            value: widget.siteLeadData.unique_id ?? '',
                          ),
                          // Site Creation Date
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Creation Date',
                            value: widget.siteLeadData.created_at ?? '',
                          ),
                          // Visit Date
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Visit Date',
                            value: widget.siteLeadData.visit_date ?? '',
                          ),
                          // Employee Code
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Employee Code',
                            value: widget.siteLeadData.emp_code ?? '',
                          ),
                          // Employee Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Employee Name',
                            value: widget.siteLeadData.emp_name ?? '',
                          ),
                          // Zone
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Zone',
                            value: widget.siteLeadData.zone ?? '',
                          ),
                          // State
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'State',
                            value: widget.siteLeadData.state ?? '',
                          ),
                          // Branch
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Branch',
                            value: widget.siteLeadData.branch ?? '',
                          ),
                          // District
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'District',
                            value: widget.siteLeadData.district ?? '',
                          ),
                          // Latitude
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Latitude',
                            value: widget.siteLeadData.latitude ?? '',
                          ),
                          // Longitude
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Longitude',
                            value: widget.siteLeadData.longitude ?? '',
                          ),
                          // Customer Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: "Customer Name",
                            value: widget.siteLeadData.cust_name ?? '',
                          ),
                          // Customer Contact No.
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Customer Contact No.',
                            value: widget.siteLeadData.cust_phn_no ?? '',
                          ),
                          // Customer Address
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Customer Address',
                            value: widget.siteLeadData.address ?? '',
                          ),
                          // Petty Contractor Regd. In Star Link
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Petty Contractor Regd. In Star Link',
                            value: widget
                                    .siteLeadData.petty_contractor_registered ??
                                '',
                          ),
                          // Petty Contractor - Head Mason Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Head Mason Name',
                            value: widget.siteLeadData.head_mason_name ?? '',
                          ),
                          // Petty Contractor- Head Mason Contact No.
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Head Mason Contact No.',
                            value: widget.siteLeadData.head_mason_contact ?? '',
                          ),
                          // Engineer Regd. In Star Stellar
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Engineer Regd. In Star Stellar',
                            value: widget.siteLeadData.engg_registered ?? '',
                          ),
                          // Engineer Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Engineer Name',
                            value: widget.siteLeadData.engg_name ?? '',
                          ),
                          // Engineer Contact No.
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Engineer Contact No.',
                            value: widget.siteLeadData.engg_contact ?? '',
                          ),
                          // Meeting Person
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Meeting Person',
                            value: widget.siteLeadData.meeting_person ?? '',
                          ),
                          // Decision Maker
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Decision Maker',
                            value: widget.siteLeadData.decision_maker ?? '',
                          ),
                          // Site Segment
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Segment',
                            value: widget.siteLeadData.site_segment ?? '',
                          ),
                          // Visit Type
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Visit Type',
                            value: widget.siteLeadData.visit_type ?? '',
                          ),
                          // Project Segment
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Project Segment',
                            value: widget.siteLeadData.project_segment ?? '',
                          ),
                          // Type of Construction
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Type of Construction',
                            value: widget.siteLeadData.type_of_const ?? '',
                          ),
                          // Floor Count
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Floor Count',
                            value: widget.siteLeadData.floor_count ?? '',
                          ),
                          // Current Stage of Construction
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Current Stage of Construction',
                            value: widget.siteLeadData
                                    .current_stage_of_construction ??
                                '',
                          ),
                          // Built-up Area (Sq. Ft.)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Built-up Area',
                            value: widget.siteLeadData.built_up_area ?? '',
                          ),
                          // Site Potential (No. of Bags)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Potential',
                            value: widget.siteLeadData.site_potential ?? '',
                          ),
                          // Consumed Till Date (No. of Bags)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Consumed Till Date',
                            value: widget.siteLeadData.consumed_till_date ?? '',
                          ),
                          // Balance Potential (No. of Bags)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Balance Potential',
                            value: widget.siteLeadData.balance_potential ?? '',
                          ),
                          // Balance Potential (No. of Bags)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Balance Potential (Manual)',
                            value:
                                widget.siteLeadData.balance_potential_manual ??
                                    '',
                          ),
                          // Site Category (Potential based)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Category',
                            value: widget.siteLeadData.site_category ?? '',
                          ),

                          // Brand Used
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Brand Used',
                            value: widget.siteLeadData.brand_used ?? '',
                          ),
                          // Price Per Bag (RSP)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Price Per Bag',
                            value: widget.siteLeadData.price_per_bag ?? '',
                          ),
                          // Conversion
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Conversion',
                            value: widget.siteLeadData.conversion ?? '',
                          ),
                          // Product
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Product',
                            value: widget.siteLeadData.select_product ?? '',
                          ),
                          // No. of Bags Ordered
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'No. of Bags Ordered',
                            value: widget.siteLeadData.no_of_bags_ordered ?? '',
                          ),
                          // Requested Date of Delivery
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Requested Date of Delivery',
                            value: widget.siteLeadData.requested_date ?? '',
                          ),
                          // Counter Type
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Counter Type',
                            value: widget.siteLeadData.counter_type ?? '',
                          ),
                          // Counter Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Counter Name',
                            value: widget.siteLeadData.counter_name ?? '',
                          ),
                          // Counter Code
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Counter Code',
                            value: widget.siteLeadData.counter_code ?? '',
                          ),
                          // Reasons for non-conversion
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Reasons for non-conversion',
                            value:
                                widget.siteLeadData.reason_for_non_conversion ??
                                    '',
                          ),
                          // Site Priority
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Priority',
                            value: widget.siteLeadData.site_priority ?? '',
                          ),
                          // Weather Shield Demo
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Weather Shield Demo',
                            value:
                                widget.siteLeadData.weather_shield_demo ?? '',
                          ),
                          // Approval Status
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Approval Status',
                            value: widget.siteLeadData.approval_status ?? '',
                          ),
                          // Date and time
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Date and time',
                            value: widget.siteLeadData.approval_date_time ?? '',
                          ),
                          // ASM Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'ASM Name',
                            value: widget.siteLeadData.asm_name ?? '',
                          ),
                          // ASM Employee ID
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'ASM Employee I\'d',
                            value: widget.siteLeadData.asm_id ?? '',
                          ),
                          // Actual Date of Delivery
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Actual Date of Delivery',
                            value:
                                widget.siteLeadData.actual_date_of_delivery ??
                                    '',
                          ),
                          // Delivery Remarks
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Delivery Remarks',
                            value: widget.siteLeadData.delivery_remarks ?? '',
                          ),
                          // Reason For Not Delivery
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Reason For Not Delivery',
                            value:
                                widget.siteLeadData.reason_for_not_delivery ??
                                    '',
                          ),
                          // Site Remarks
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Remarks',
                            value: widget.siteLeadData.remarks ?? '',
                          ),
                          // Site Status
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Status',
                            value: widget.siteLeadData.site_status ?? '',
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
              bottomNavigationBar: widget.siteLeadData.approval_status
                          ?.toLowerCase() ==
                      'pending'.toLowerCase()
                  ? Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: SizedBox(
                        width: double.infinity,
                        height: 50,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.red,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(8),
                            ),
                          ),
                          onPressed: () {
                            _showApprovalDialog();
                          },
                          child: const Text(
                            'Submit',
                            style: TextStyle(fontSize: 16, color: Colors.white),
                          ),
                        ),
                      ),
                    )
                  : null,
            ),
            if (_isLoading)
              Container(
                // ignore: deprecated_member_use
                color: Colors.black.withOpacity(0.3),
                child: const Center(
                  child: CircularProgressIndicator(),
                ),
              ),
          ],
        ));
  }
}

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

class SelectButtonWithLabel extends StatelessWidget {
  final VoidCallback? onPressed;
  final String buttonLabel;
  final String? value;
  final bool isMandatory;
  final bool isEnabled;
  final String? errorMessage;

  const SelectButtonWithLabel({
    super.key,
    required this.buttonLabel,
    this.onPressed,
    this.value,
    this.isMandatory = false,
    this.isEnabled = true,
    this.errorMessage = '',
  });

  @override
  Widget build(BuildContext context) {
    final hasValue = (value ?? '').trim().isNotEmpty;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: double.infinity,
          child: OutlinedButton(
            onPressed: () {
              FocusScope.of(context).unfocus();
              if (isEnabled) {
                if (onPressed != null) onPressed!();
              } else {
                if ((errorMessage ?? '').isNotEmpty) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(errorMessage!),
                      backgroundColor: Colors.red,
                    ),
                  );
                }
              }
            },
            style: OutlinedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 16),
              side: const BorderSide(color: Colors.grey),
              backgroundColor: isEnabled ? Colors.white : Colors.grey.shade100,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(4),
              ),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Text(
                  buttonLabel,
                  style: TextStyle(
                    fontSize: 16,
                    color: isEnabled ? Colors.black : Colors.grey,
                  ),
                ),
                if (isMandatory) ...[
                  const SizedBox(width: 4),
                  const Text(
                    "*",
                    style: TextStyle(
                      color: Colors.red,
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ],
            ),
          ),
        ),
        const SizedBox(height: 4),
        if (hasValue)
          Text(
            value!,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              color: Color.fromARGB(255, 255, 166, 0),
            ),
          ),
      ],
    );
  }
}

class LabeledTextField extends StatelessWidget {
  final String label;
  final String hintText;
  final TextEditingController controller;
  final TextInputType keyboardType;
  final bool isEditable;
  final bool isMandatory;
  final String? initialValue;
  final int? maxLength;

  const LabeledTextField({
    super.key,
    required this.label,
    required this.hintText,
    required this.controller,
    this.maxLength,
    this.keyboardType = TextInputType.text,
    this.isEditable = true,
    this.initialValue,
    this.isMandatory = false,
  });

  @override
  Widget build(BuildContext context) {
    // Set initial value only if provided and controller is empty
    if (initialValue != null && controller.text.isEmpty) {
      controller.text = initialValue!;
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              label,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
              ),
            ),
            if (isMandatory) ...[
              const SizedBox(width: 4),
              const Text(
                "*",
                style: TextStyle(
                  color: Colors.red,
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ],
        ),
        const SizedBox(height: 5),
        TextField(
          controller: controller,
          keyboardType: keyboardType,
          enabled: isEditable,
          maxLength: maxLength,
          decoration: InputDecoration(
            hintText: isMandatory ? "$hintText (Mandatory)" : hintText,
            border: const OutlineInputBorder(),
            filled: !isEditable,
            fillColor: !isEditable ? Colors.grey.shade200 : null,
          ),
        ),
      ],
    );
  }
}

//Approval Status
class ApprovalStatusList {
  String? title;
  String? value;

  ApprovalStatusList({
    this.title,
    this.value,
  });

  factory ApprovalStatusList.fromLine(String line) {
    return ApprovalStatusList(
      title: line.split('^')[0],
      value: line.split('^')[0],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'value': value,
      'title': title,
    };
  }

  static Future<List<ApprovalStatusList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };
    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(Uri.parse(
        "${AppWebService.baseURL}misreport/api_approval_status_site_lead.php"));

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);
      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => ApprovalStatusList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}
