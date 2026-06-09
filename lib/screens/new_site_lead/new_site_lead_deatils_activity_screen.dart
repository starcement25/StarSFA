import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:http/io_client.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class NewSiteLeadDeatilsActivityScreen extends StatefulWidget {
  final bool isOptionSelected;
  const NewSiteLeadDeatilsActivityScreen(
      {super.key, this.isOptionSelected = false});

  @override
  State<NewSiteLeadDeatilsActivityScreen> createState() =>
      _NewSiteLeadDeatilsActivityScreen();
}

class _NewSiteLeadDeatilsActivityScreen
    extends State<NewSiteLeadDeatilsActivityScreen> {
  String? id;
  // ignore: non_constant_identifier_names
  String? transaction_id;
  // ignore: non_constant_identifier_names
  String? unique_id;
  // ignore: non_constant_identifier_names
  String? visit_date;
  // ignore: non_constant_identifier_names
  String? emp_code;
  // ignore: non_constant_identifier_names
  String? emp_name;
  String? zone;
  String? branch;
  String? district;
  String? state;
  String? longitude;
  String? latitude;
  // ignore: non_constant_identifier_names
  String? cust_name;
  // ignore: non_constant_identifier_names
  String? cust_phn_no;
  String? address;
  // ignore: non_constant_identifier_names
  String? site_segment;
  // ignore: non_constant_identifier_names
  String? visit_type;
  // ignore: non_constant_identifier_names
  String? project_segment;
  // ignore: non_constant_identifier_names
  String? type_of_const;
  // ignore: non_constant_identifier_names
  String? built_up_area;
  // ignore: non_constant_identifier_names
  String? no_of_bag;
  String? conversion;
  // ignore: non_constant_identifier_names
  String? site_priority;
  // ignore: non_constant_identifier_names
  String? counter_code;
  // ignore: non_constant_identifier_names
  String? created_at;
  // ignore: non_constant_identifier_names
  String? updated_at;
  // ignore: non_constant_identifier_names
  String? new_site_lead_id;
  // ignore: non_constant_identifier_names
  String? new_site_lead_unique_id;
  // ignore: non_constant_identifier_names
  String? petty_contractor_registered;
  // ignore: non_constant_identifier_names
  String? head_mason_name;
  // ignore: non_constant_identifier_names
  String? contractor_id;
  // ignore: non_constant_identifier_names
  String? head_mason_contact;
  // ignore: non_constant_identifier_names
  String? engg_registered;
  // ignore: non_constant_identifier_names
  String? engg_name;
  // ignore: non_constant_identifier_names
  String? engg_id;
  // ignore: non_constant_identifier_names
  String? engg_contact;
  // ignore: non_constant_identifier_names
  String? meeting_person;
  // ignore: non_constant_identifier_names
  String? decision_maker;
  // ignore: non_constant_identifier_names
  String? current_stage_of_construction;
  // ignore: non_constant_identifier_names
  String? site_potential;
  // ignore: non_constant_identifier_names
  String? consumed_till_date;
  // ignore: non_constant_identifier_names
  String? balance_potential;
  // ignore: non_constant_identifier_names
  String? site_category;
  // ignore: non_constant_identifier_names
  String? brand_used;
  // ignore: non_constant_identifier_names
  String? price_per_bag;
  // ignore: non_constant_identifier_names
  String? select_product;
  // ignore: non_constant_identifier_names
  String? no_of_bags_ordered;
  // ignore: non_constant_identifier_names
  String? requested_date;
  // ignore: non_constant_identifier_names
  String? counter_type;
  // ignore: non_constant_identifier_names
  String? counter_name;
  // ignore: non_constant_identifier_names
  String? reason_for_non_conversion;
  // ignore: non_constant_identifier_names
  String? weather_shield_demo;
  // ignore: non_constant_identifier_names
  String? approval_status;
  // ignore: non_constant_identifier_names
  String? approval_date_time;
  // ignore: non_constant_identifier_names
  String? asm_name;
  // ignore: non_constant_identifier_names
  String? asm_id;
  // ignore: non_constant_identifier_names
  String? actual_date_of_delivery;
  // ignore: non_constant_identifier_names
  String? delivery_remarks;
  // ignore: non_constant_identifier_names
  String? reason_for_not_delivery;
  // ignore: non_constant_identifier_names
  String? site_status;
  // ignore: non_constant_identifier_names
  String? floor_count;
  // ignore: non_constant_identifier_names
  String? balance_potential_manual;
  // ignore: non_constant_identifier_names
  String? site_remarks;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _showFirstDialog();
    });
  }

  void _showFirstDialog() {
    showSelectorDialog<SiteLeadDataList>(
      fetchData: () => SiteLeadDataList.fetchDataFromApi(),
      dialogTitle: 'Select Site Lead',
      getDisplayText: (item) => item,
      enableSearch: true,
      onSelected: (item) {
        setState(() {
          id = item.id;
          transaction_id = item.transaction_id;
          unique_id = item.unique_id;
          visit_date = item.visit_date;
          emp_code = item.emp_code;
          emp_name = item.emp_name;
          zone = item.zone;
          branch = item.branch;
          district = item.district;
          state = item.state;
          longitude = item.longitude;
          latitude = item.latitude;
          cust_name = item.cust_name;
          cust_phn_no = item.cust_phn_no;
          address = item.address;
          site_segment = item.site_segment;
          visit_type = item.visit_type;
          project_segment = item.project_segment;
          type_of_const = item.type_of_const;
          built_up_area = item.built_up_area;
          no_of_bag = item.no_of_bag;
          conversion = item.conversion;
          site_priority = item.site_priority;
          counter_code = item.counter_code;
          created_at = item.created_at;
          updated_at = item.updated_at;
          new_site_lead_id = item.new_site_lead_id;
          new_site_lead_unique_id = item.new_site_lead_unique_id;
          petty_contractor_registered = item.petty_contractor_registered;
          head_mason_name = item.head_mason_name;
          contractor_id = item.contractor_id;
          head_mason_contact = item.head_mason_contact;
          engg_registered = item.engg_registered;
          engg_name = item.engg_name;
          engg_id = item.engg_id;
          engg_contact = item.engg_contact;
          meeting_person = item.meeting_person;
          decision_maker = item.decision_maker;
          current_stage_of_construction = item.current_stage_of_construction;
          site_potential = item.site_potential;
          consumed_till_date = item.consumed_till_date;
          balance_potential = item.balance_potential;
          site_category = item.site_category;
          brand_used = item.brand_used;
          price_per_bag = item.price_per_bag;
          select_product = item.select_product;
          no_of_bags_ordered = item.no_of_bags_ordered;
          requested_date = item.requested_date;
          counter_type = item.counter_type;
          counter_name = item.counter_name;
          reason_for_non_conversion = item.reason_for_non_conversion;
          weather_shield_demo = item.weather_shield_demo;
          approval_status = item.approval_status;
          approval_date_time = item.approval_date_time;
          asm_name = item.asm_name;
          asm_id = item.asm_id;
          actual_date_of_delivery = item.actual_date_of_delivery;
          delivery_remarks = item.delivery_remarks;
          reason_for_not_delivery = item.reason_for_not_delivery;
          site_status = item.site_status;
          floor_count = item.floor_count;
          balance_potential_manual = item.balance_potential_manual;
          site_remarks = item.site_remarks;
        });
      },
    );
  }

  void showSelectorDialog<T>({
    required Future<List<T>> Function() fetchData,
    required String dialogTitle,
    required SiteLeadDataList Function(T) getDisplayText,
    required void Function(T selectedItem) onSelected,
    bool enableSearch = true,
  }) {
    TextEditingController searchController = TextEditingController();
    List<T> allItems = []; // use provided list
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
                                filteredItems = allItems.where((item) {
                                  final searchText = value.toLowerCase();

                                  final leadId = getDisplayText(item)
                                          .unique_id
                                          ?.toLowerCase() ??
                                      '';
                                  final soldTo = getDisplayText(item)
                                          .cust_name
                                          ?.toLowerCase() ??
                                      '';
                                  final shipTo = getDisplayText(item)
                                          .cust_phn_no
                                          ?.toLowerCase() ??
                                      '';
                                  return leadId.contains(searchText) ||
                                      soldTo.contains(searchText) ||
                                      shipTo.contains(searchText);
                                }).toList();
                              });
                            },
                          ),
                        ),
                      Expanded(
                          child: filteredItems.isEmpty
                              ? const Center(child: Text('No data found'))
                              : ListView.builder(
                                  itemCount: filteredItems.length,
                                  itemBuilder: (context, index) {
                                    final item = filteredItems[index];
                                    String showData = '';
                                    Color textColor =
                                        Color.fromARGB(255, 0, 0, 0);
                                    showData = 'Pending';
                                    textColor = Color.fromARGB(255, 0, 0, 0);
                                    return InkWell(
                                      onTap: () {
                                        onSelected(item);
                                        Navigator.pop(context);
                                      },
                                      child: Container(
                                        margin: const EdgeInsets.symmetric(
                                            horizontal: 8, vertical: 4),
                                        padding: const EdgeInsets.all(12),
                                        decoration: BoxDecoration(
                                          color: Colors.white,
                                          borderRadius:
                                              BorderRadius.circular(8),
                                          boxShadow: [
                                            BoxShadow(
                                              color: Colors.black12,
                                              blurRadius: 4,
                                              offset: Offset(0, 2),
                                            )
                                          ],
                                        ),
                                        child: Column(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.start,
                                          children: [
                                            Row(
                                              mainAxisAlignment:
                                                  MainAxisAlignment
                                                      .spaceBetween,
                                              children: [
                                                Text(
                                                  "I'd : ${getDisplayText(item).unique_id}",
                                                  style: TextStyle(
                                                    fontSize: 14,
                                                    fontWeight: FontWeight.bold,
                                                    color: Color.fromARGB(
                                                        255, 0, 0, 0),
                                                  ),
                                                ),
                                                Text(
                                                  showData,
                                                  style: TextStyle(
                                                    fontSize: 14,
                                                    color: textColor,
                                                  ),
                                                ),
                                              ],
                                            ),
                                            const SizedBox(height: 6),
                                            Text(
                                              "Customer Name : ${getDisplayText(item).cust_name}",
                                              style: TextStyle(
                                                fontSize: 12,
                                                color: Color.fromARGB(
                                                    255, 0, 0, 0),
                                              ),
                                            ),
                                            const SizedBox(height: 6),
                                            Text(
                                              "Contact Number : ${getDisplayText(item).cust_phn_no}",
                                              style: TextStyle(
                                                fontSize: 12,
                                                color: Color.fromARGB(
                                                    255, 0, 0, 0),
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    );
                                  },
                                )),
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
                  'Activity Report of Site Visit',
                  style: TextStyle(color: Colors.white),
                ),
                actions: [
                  IconButton(
                    icon: const Icon(
                      Icons.filter_list, // ← filter icon
                      color: Colors.white,
                    ),
                    onPressed: () {
                      _showFirstDialog();
                    },
                  ),
                ],
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
                          // Transaction I'd
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Transaction I\'d',
                            value: transaction_id ?? '',
                          ),
                          // Unique Site I'd
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Unique Site I\'d',
                            value: unique_id ?? '',
                          ),
                          // Site Creation Date
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Creation Date',
                            value: created_at ?? '',
                          ),
                          // Visit Date
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Visit Date',
                            value: visit_date ?? '',
                          ),
                          // Employee Code
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Employee Code',
                            value: emp_code ?? '',
                          ),
                          // Employee Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Employee Name',
                            value: emp_name ?? '',
                          ),
                          // Zone
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Zone',
                            value: zone ?? '',
                          ),
                          // State
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'State',
                            value: state ?? '',
                          ),
                          // Branch
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Branch',
                            value: branch ?? '',
                          ),
                          // District
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'District',
                            value: district ?? '',
                          ),
                          // Latitude
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Latitude',
                            value: latitude ?? '',
                          ),
                          // Longitude
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Longitude',
                            value: longitude ?? '',
                          ),
                          // Customer Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Customer Name',
                            value: cust_name ?? '',
                          ),
                          // Customer Contact No.
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Customer Contact No.',
                            value: cust_phn_no ?? '',
                          ),
                          // Customer Address
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Customer Address',
                            value: address ?? '',
                          ),
                          // Petty Contractor Regd. In Star Link
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Petty Contractor Regd. In Star Link',
                            value: petty_contractor_registered ?? '',
                          ),
                          // Petty Contractor - Head Mason Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Head Mason Name',
                            value: head_mason_name ?? '',
                          ),
                          // Petty Contractor- Head Mason Contact No.
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Head Mason Contact No.',
                            value: head_mason_contact ?? '',
                          ),
                          // Engineer Regd. In Star Stellar
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Engineer Regd. In Star Stellar',
                            value: engg_registered ?? '',
                          ),
                          // Engineer Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Engineer Name',
                            value: engg_name ?? '',
                          ),
                          // Engineer Contact No.
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Engineer Contact No.',
                            value: engg_contact ?? '',
                          ),
                          // Meeting Person
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Meeting Person',
                            value: meeting_person ?? '',
                          ),
                          // Decision Maker
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Decision Maker',
                            value: decision_maker ?? '',
                          ),
                          // Site Segment
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Segment',
                            value: site_segment ?? '',
                          ),
                          // Visit Type
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Visit Type',
                            value: visit_type ?? '',
                          ),
                          // Project Segment
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Project Segment',
                            value: project_segment ?? '',
                          ),
                          // Type of Construction
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Type of Construction',
                            value: type_of_const ?? '',
                          ),
                          // Floor Count
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Floor Count',
                            value: floor_count ?? '',
                          ),
                          // Current Stage of Construction
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Current Stage of Construction',
                            value: current_stage_of_construction ?? '',
                          ),
                          // Built-up Area (Sq. Ft.)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Built-up Area',
                            value: built_up_area ?? '',
                          ),
                          // Site Potential (No. of Bags)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Potential',
                            value: site_potential ?? '',
                          ),
                          // Consumed Till Date (No. of Bags)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Consumed Till Date',
                            value: consumed_till_date ?? '',
                          ),
                          // Balance Potential (No. of Bags)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Balance Potential',
                            value: balance_potential ?? '',
                          ),
                          // Balance Potential (No. of Bags)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Balance Potential (Manual)',
                            value: balance_potential_manual ?? '',
                          ),
                          // Site Category (Potential based)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Category',
                            value: site_category ?? '',
                          ),
                          // Brand Used
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Brand Used',
                            value: brand_used ?? '',
                          ),
                          // Price Per Bag (RSP)
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Price Per Bag',
                            value: price_per_bag ?? '',
                          ),
                          // Conversion
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Conversion',
                            value: conversion ?? '',
                          ),
                          // Product
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Product',
                            value: select_product ?? '',
                          ),
                          // No. of Bags Ordered
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'No. of Bags Ordered',
                            value: no_of_bags_ordered ?? '',
                          ),
                          // Requested Date of Delivery
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Requested Date of Delivery',
                            value: requested_date ?? '',
                          ),
                          // Counter Type
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Counter Type',
                            value: counter_type ?? '',
                          ),
                          // Counter Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Counter Name',
                            value: counter_name ?? '',
                          ),
                          // Counter Code
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Counter Code',
                            value: counter_code ?? '',
                          ),
                          // Reasons for non-conversion
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Reasons for non-conversion',
                            value: reason_for_non_conversion ?? '',
                          ),
                          // Site Priority
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Priority',
                            value: site_priority ?? '',
                          ),
                          // Weather Shield Demo
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Weather Shield Demo',
                            value: weather_shield_demo ?? '',
                          ),
                          // Approval Status
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Approval Status',
                            value: approval_status ?? '',
                          ),
                          // Date and time
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Date and time',
                            value: approval_date_time ?? '',
                          ),
                          // ASM Name
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'ASM Name',
                            value: asm_name ?? '',
                          ),
                          // ASM Employee ID
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'ASM Employee I\'d',
                            value: asm_id ?? '',
                          ),
                          // Actual Date of Delivery
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Actual Date of Delivery',
                            value: actual_date_of_delivery ?? '',
                          ),
                          // Delivery Remarks
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Delivery Remarks',
                            value: delivery_remarks ?? '',
                          ),
                          // Reason For Not Delivery
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Reason For Not Delivery',
                            value: reason_for_not_delivery ?? '',
                          ),
                          // Site Remarks
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Remarks',
                            value: site_remarks ?? '',
                          ),
                          // Site Status
                          Container(
                            height: 1,
                            width: double.infinity,
                            color: Colors.black,
                          ),
                          LabelValueText(
                            label: 'Site Status',
                            value: site_status ?? '',
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

class LabelText extends StatelessWidget {
  final String label;

  const LabelText({
    super.key,
    required this.label,
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

class SiteLeadDataList {
  String? id;
  // ignore: non_constant_identifier_names
  String? transaction_id;
  // ignore: non_constant_identifier_names
  String? unique_id;
  // ignore: non_constant_identifier_names
  String? visit_date;
  // ignore: non_constant_identifier_names
  String? emp_code;
  // ignore: non_constant_identifier_names
  String? emp_name;
  String? zone;
  String? branch;
  String? district;
  String? state;
  String? longitude;
  String? latitude;
  // ignore: non_constant_identifier_names
  String? cust_name;
  // ignore: non_constant_identifier_names
  String? cust_phn_no;
  String? address;
  // ignore: non_constant_identifier_names
  String? site_segment;
  // ignore: non_constant_identifier_names
  String? visit_type;
  // ignore: non_constant_identifier_names
  String? project_segment;
  // ignore: non_constant_identifier_names
  String? type_of_const;
  // ignore: non_constant_identifier_names
  String? built_up_area;
  // ignore: non_constant_identifier_names
  String? no_of_bag;
  String? conversion;
  // ignore: non_constant_identifier_names
  String? site_priority;
  // ignore: non_constant_identifier_names
  String? counter_code;
  // ignore: non_constant_identifier_names
  String? created_at;
  // ignore: non_constant_identifier_names
  String? updated_at;
  // ignore: non_constant_identifier_names
  String? new_site_lead_id;
  // ignore: non_constant_identifier_names
  String? new_site_lead_unique_id;
  // ignore: non_constant_identifier_names
  String? petty_contractor_registered;
  // ignore: non_constant_identifier_names
  String? head_mason_name;
  // ignore: non_constant_identifier_names
  String? contractor_id;
  // ignore: non_constant_identifier_names
  String? head_mason_contact;
  // ignore: non_constant_identifier_names
  String? engg_registered;
  // ignore: non_constant_identifier_names
  String? engg_name;
  // ignore: non_constant_identifier_names
  String? engg_id;
  // ignore: non_constant_identifier_names
  String? engg_contact;
  // ignore: non_constant_identifier_names
  String? meeting_person;
  // ignore: non_constant_identifier_names
  String? decision_maker;
  // ignore: non_constant_identifier_names
  String? current_stage_of_construction;
  // ignore: non_constant_identifier_names
  String? site_potential;
  // ignore: non_constant_identifier_names
  String? consumed_till_date;
  // ignore: non_constant_identifier_names
  String? balance_potential;
  // ignore: non_constant_identifier_names
  String? site_category;
  // ignore: non_constant_identifier_names
  String? brand_used;
  // ignore: non_constant_identifier_names
  String? price_per_bag;
  // ignore: non_constant_identifier_names
  String? select_product;
  // ignore: non_constant_identifier_names
  String? no_of_bags_ordered;
  // ignore: non_constant_identifier_names
  String? requested_date;
  // ignore: non_constant_identifier_names
  String? counter_type;
  // ignore: non_constant_identifier_names
  String? counter_name;
  // ignore: non_constant_identifier_names
  String? reason_for_non_conversion;
  // ignore: non_constant_identifier_names
  String? weather_shield_demo;
  // ignore: non_constant_identifier_names
  String? approval_status;
  // ignore: non_constant_identifier_names
  String? approval_date_time;
  // ignore: non_constant_identifier_names
  String? asm_name;
  // ignore: non_constant_identifier_names
  String? asm_id;
  // ignore: non_constant_identifier_names
  String? actual_date_of_delivery;
  // ignore: non_constant_identifier_names
  String? delivery_remarks;
  // ignore: non_constant_identifier_names
  String? reason_for_not_delivery;
  // ignore: non_constant_identifier_names
  String? site_status;
  // ignore: non_constant_identifier_names
  String? floor_count;
  // ignore: non_constant_identifier_names
  String? balance_potential_manual;
  // ignore: non_constant_identifier_names
  String? site_remarks;

  SiteLeadDataList({
    this.id,
    // ignore: non_constant_identifier_names
    this.transaction_id,
    // ignore: non_constant_identifier_names
    this.unique_id,
    // ignore: non_constant_identifier_names
    this.visit_date,
    // ignore: non_constant_identifier_names
    this.emp_code,
    // ignore: non_constant_identifier_names
    this.emp_name,
    this.zone,
    this.branch,
    this.district,
    this.state,
    this.longitude,
    this.latitude,
    // ignore: non_constant_identifier_names
    this.cust_name,
    // ignore: non_constant_identifier_names
    this.cust_phn_no,
    this.address,
    // ignore: non_constant_identifier_names
    this.site_segment,
    // ignore: non_constant_identifier_names
    this.visit_type,
    // ignore: non_constant_identifier_names
    this.project_segment,
    // ignore: non_constant_identifier_names
    this.type_of_const,
    // ignore: non_constant_identifier_names
    this.built_up_area,
    // ignore: non_constant_identifier_names
    this.no_of_bag,
    this.conversion,
    // ignore: non_constant_identifier_names
    this.site_priority,
    // ignore: non_constant_identifier_names
    this.counter_code,
    // ignore: non_constant_identifier_names
    this.created_at,
    // ignore: non_constant_identifier_names
    this.updated_at,
    // ignore: non_constant_identifier_names
    this.new_site_lead_id,
    // ignore: non_constant_identifier_names
    this.new_site_lead_unique_id,
    // ignore: non_constant_identifier_names
    this.petty_contractor_registered,
    // ignore: non_constant_identifier_names
    this.head_mason_name,
    // ignore: non_constant_identifier_names
    this.contractor_id,
    // ignore: non_constant_identifier_names
    this.head_mason_contact,
    // ignore: non_constant_identifier_names
    this.engg_registered,
    // ignore: non_constant_identifier_names
    this.engg_name,
    // ignore: non_constant_identifier_names
    this.engg_id,
    // ignore: non_constant_identifier_names
    this.engg_contact,
    // ignore: non_constant_identifier_names
    this.meeting_person,
    // ignore: non_constant_identifier_names
    this.decision_maker,
    // ignore: non_constant_identifier_names
    this.current_stage_of_construction,
    // ignore: non_constant_identifier_names
    this.site_potential,
    // ignore: non_constant_identifier_names
    this.consumed_till_date,
    // ignore: non_constant_identifier_names
    this.balance_potential,
    // ignore: non_constant_identifier_names
    this.site_category,
    // ignore: non_constant_identifier_names
    this.brand_used,
    // ignore: non_constant_identifier_names
    this.price_per_bag,
    // ignore: non_constant_identifier_names
    this.select_product,
    // ignore: non_constant_identifier_names
    this.no_of_bags_ordered,
    // ignore: non_constant_identifier_names
    this.requested_date,
    // ignore: non_constant_identifier_names
    this.counter_type,
    // ignore: non_constant_identifier_names
    this.counter_name,
    // ignore: non_constant_identifier_names
    this.reason_for_non_conversion,
    // ignore: non_constant_identifier_names
    this.weather_shield_demo,
    // ignore: non_constant_identifier_names
    this.approval_status,
    // ignore: non_constant_identifier_names
    this.approval_date_time,
    // ignore: non_constant_identifier_names
    this.asm_name,
    // ignore: non_constant_identifier_names
    this.asm_id,
    // ignore: non_constant_identifier_names
    this.actual_date_of_delivery,
    // ignore: non_constant_identifier_names
    this.delivery_remarks,
    // ignore: non_constant_identifier_names
    this.reason_for_not_delivery,
    // ignore: non_constant_identifier_names
    this.site_status,
    // ignore: non_constant_identifier_names
    this.floor_count,
    // ignore: non_constant_identifier_names
    this.balance_potential_manual,
    // ignore: non_constant_identifier_names
    this.site_remarks,
  });

  factory SiteLeadDataList.fromLine(String line) {
    return SiteLeadDataList(
      id: line.split('^')[1],
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
      site_remarks: line.split('^')[63],
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
      'site_remarks': site_remarks,
    };
  }

  static Future<List<SiteLeadDataList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };
    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();
    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_get_site_list_site_lead_today.php?emp_code=${user?.empCode}"),
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
      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}
