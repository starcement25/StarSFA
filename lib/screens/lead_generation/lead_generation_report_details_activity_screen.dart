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
import 'package:starsfa/screens/lead_generation/lead_generation_report_activity_screen.dart';
import 'package:starsfa/screens/lead_generation/lead_generation_activity_screen.dart';

class LeadGenerationReportDetailsActivityScreen extends StatefulWidget {
  final RequestLeadListData siteLeadData;

  const LeadGenerationReportDetailsActivityScreen({
    super.key,
    required this.siteLeadData,
  });

  @override
  State<LeadGenerationReportDetailsActivityScreen> createState() =>
      _LeadGenerationReportDetailsActivityScreen();
}

class _LeadGenerationReportDetailsActivityScreen
    extends State<LeadGenerationReportDetailsActivityScreen> {
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
                  'Site Lead Details',
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
                          // Lead Id
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Lead I\'d',
                            value: widget.siteLeadData.lead_generation_id ?? '',
                          ),
                          // Sales Officer Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Sales Officer Name',
                            value:
                                widget.siteLeadData.emp_details_emp_name ?? '',
                          ),
                          // Date Stamp
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Date Stamp',
                            value:
                                widget.siteLeadData.download_time_date_stamp ??
                                    '',
                          ),
                          // Time Stamp
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Time Stamp',
                            value:
                                widget.siteLeadData.download_time_time_stamp ??
                                    '',
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
                          // Sold to Party Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Sold to Party Name',
                            value: widget
                                    .siteLeadData.sold_to_party_details_name ??
                                '',
                          ),
                          // Sold to Party Code
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Sold to Party Code',
                            value: widget.siteLeadData.sold_to_party_code ?? '',
                          ),
                          // Sold to Party Address
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Sold to Party Address',
                            value: widget.siteLeadData
                                    .sold_to_party_details_address ??
                                '',
                          ),
                          // Sold to Party State
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Sold to Party State',
                            value: widget
                                    .siteLeadData.sold_to_party_details_state ??
                                '',
                          ),
                          // Sold to Party Districts
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Sold to Party Districts',
                            value: widget.siteLeadData
                                    .sold_to_party_details_districts ??
                                '',
                          ),
                          // Ship to Party Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Ship to Party Name',
                            value: widget
                                    .siteLeadData.ship_to_party_details_name ??
                                '',
                          ),
                          // Ship to Party Code
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: "Ship to Party Code",
                            value: widget.siteLeadData.ship_to_party ?? '',
                          ),
                          // Ship to Party Address
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Ship to Party Address',
                            value: widget.siteLeadData
                                    .ship_to_party_details_address ??
                                '',
                          ),
                          // Ship to Party State
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Ship to Party State',
                            value: widget
                                    .siteLeadData.ship_to_party_details_state ??
                                '',
                          ),
                          // Ship to Party Districts
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Ship to Party Districts',
                            value: widget.siteLeadData
                                    .ship_to_party_details_districts ??
                                '',
                          ),
                          // Segment
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Segment',
                            value: widget.siteLeadData.type_lead ?? '',
                          ),
                          // Lead Source
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Lead Source',
                            value: widget.siteLeadData.lead_type ?? '',
                          ),
                          // Product + Packaging
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Product + Packaging',
                            value: widget.siteLeadData.product_packaging ?? '',
                          ),
                          // Total Potential of Site (MT)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Total Potential of Site (MT)',
                            value: widget.siteLeadData.qty_req ?? '',
                          ),
                          // Quotation Quantity (MT)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Quotation Quantity (MT)',
                            value: widget.siteLeadData.month_qty ?? '',
                          ),
                          // Current Brand Used
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Current Brand Used',
                            value: widget.siteLeadData.current_brand_used ?? '',
                          ),
                          // Expected Rate per Bag
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Expected Rate per Bag',
                            value: widget.siteLeadData.exp_rate_per_bag ?? '',
                          ),
                          // Current Price Star Rs. per Bag
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Current Price Star Rs. per Bag',
                            value: widget.siteLeadData.current_price ?? '',
                          ),
                          // Current Price Competitor Rs. per Bag
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Current Price Competitor Rs. per Bag',
                            value:
                                widget.siteLeadData.current_price_competitor ??
                                    '',
                          ),
                          // Contact Person Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Contact Person Name',
                            value:
                                widget.siteLeadData.contact_person_name ?? '',
                          ),
                          // Designation
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Designation',
                            value: widget.siteLeadData.designation ?? '',
                          ),
                          // Contact Number
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Contact Number',
                            value: widget.siteLeadData.contact_number ?? '',
                          ),
                          // Mail Id
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Mail Id',
                            value: widget.siteLeadData.mail_id ?? '',
                          ),
                          // Mode of Payment
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Mode of Payment',
                            value: widget.siteLeadData.mode ?? '',
                          ),
                          // Credit Terms
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Credit Terms',
                            value: widget.siteLeadData.credit_terms ?? '',
                          ),
                          // AAC Block is Required or Not
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'AAC Block is Required or Not',
                            value:
                                widget.siteLeadData.acc_block_is_required ?? '',
                          ),
                          // Category Type of Construction
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Category Type of Construction',
                            value: widget
                                    .siteLeadData.category_type_construction ??
                                '',
                          ),
                          // Lead Status
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Lead Status',
                            value: widget.siteLeadData.lead_status ?? '',
                          ),
                          // Next Visit Date
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Next Visit Date',
                            value: widget.siteLeadData.next_visit_date ?? '',
                          ),
                          // Requirement Type
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Requirement Type',
                            value: widget.siteLeadData.incoterms ?? '',
                          ),
                          // Ex. Works
                          if (widget.siteLeadData.incoterms!.toLowerCase() ==
                              'exw') ...[
                            Container(
                              height: 1,
                              width: double.infinity,
                              color: Colors.black,
                            ),
                            LabelValueText(
                              label: 'Ex. Works',
                              value: widget.siteLeadData.serving_location ?? '',
                            ),
                          ],
                          // FOS Siding
                          if (widget.siteLeadData.incoterms!.toLowerCase() ==
                              'fos') ...[
                            Container(
                              height: 1,
                              width: double.infinity,
                              color: Colors.black,
                            ),
                            LabelValueText(
                              label: 'FOS Siding',
                              value: widget.siteLeadData.serving_location ?? '',
                            ),
                          ],
                          // Sales Officer Remarks
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Sales Officer Remarks',
                            value: widget.siteLeadData.lead_remarks ?? '',
                          ),
                          // Assigned To
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Assigned To',
                            value: widget.siteLeadData.assigned_to ?? '',
                          ),
                          // Requirement Timing
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Requirement Timing',
                            value: widget.siteLeadData.r_timing ?? '',
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
              bottomNavigationBar: widget.siteLeadData.lead_action
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
                          onPressed: () async {
                            final result = await Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => LeadGenerationActivityScreen(),
                              ),
                            );
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
    Key? key,
    required this.label,
    required this.hintText,
    required this.controller,
    this.maxLength,
    this.keyboardType = TextInputType.text,
    this.isEditable = true,
    this.initialValue,
    this.isMandatory = false,
  }) : super(key: key);

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
