import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class LeadGenerationActivityScreen extends StatefulWidget {
  final bool isOptionSelected;
  const LeadGenerationActivityScreen(
      {super.key, this.isOptionSelected = false});

  @override
  State<LeadGenerationActivityScreen> createState() =>
      _LeadGenerationActivityScreenState();
}

class _LeadGenerationActivityScreenState
    extends State<LeadGenerationActivityScreen> {
  // ignore: non_constant_identifier_names
  String emp_code = '';
  int hotLeadCount = 0;
  int warmLeadCount = 0;
  int coldLeadCount = 0;
  bool _isLoading = true;
  bool _isSubmitButtonShow = false;
  int _selectedLeadStatusRadio = 0;

  List<LeadListData>? leadListData = [];

  int _empCategory = 0;

  String? showVisitType;
  TextEditingController uniqueLeadIdController = TextEditingController();

  // Existing Lead Details
  bool isDataEditable = false;
  String? existingLeadId = '';
  String? existingSalesOfficerName = '';
  String? existingReferredBy = '';
  String? existingDateStamp = '';
  String? existingTimeStamp = '';
  String? existingLatitude = '';
  String? existingLongitude = '';
  String? existingSoldToPartyName = '';
  String? existingSoldToPartyCode = '';
  String? existingSoldToPartyAddress = '';
  String? existingSoldToPartyState = '';
  String? existingSoldToPartyDistricts = '';
  String? existingShipToPartyName = '';
  String? existingShipToPartyCode = '';
  String? existingShipToPartyAddress = '';
  String? existingShipToPartyState = '';
  String? existingShipToPartyDistricts = '';
  String? existingSegment = '';
  String? existingLeadSource = '';
  String? existingProductPackaging = '';
  String? existingTotalPotentialOfSite = '';
  String? existingQuotationQuantity = '';
  String? existingCurrentBrandUsed = '';
  String? existingExpectedRatePerBag = '';
  String? existingCurrentPriceStarPerBag = '';
  String? existingCurrentPriceCompetitorPerBag = '';
  String? existingContactPersonName = '';
  String? existingDesignation = '';
  String? existingContactNumber = '';
  String? existingMailId = '';
  String? existingModeOfPayment = '';
  String? existingCreditTerms = '';
  String? existingAacBlockRequired = '';
  String? existingCategoryTypeOfConstruction = '';
  String? existingLeadStatus = '';
  String? existingNextVisitDate = '';
  String? existingRequirementType = '';
  String? existingExWorks = '';
  String? existingFosSiding = '';
  String? existingSalesOfficerRemarks = '';
  String? existingAssignedTo = '';
  String? existingAssignedToCode = '';
  String? existingRequirementTiming = '';

  TextEditingController existingSalesOfficerNameController =
      TextEditingController();
  TextEditingController existingReferredByController = TextEditingController();
  TextEditingController existingDateStampController = TextEditingController();
  TextEditingController existingTimeStampController = TextEditingController();
  TextEditingController existingLatitudeController = TextEditingController();
  TextEditingController existingLongitudeController = TextEditingController();
  TextEditingController existingSoldToPartyNameController =
      TextEditingController();
  TextEditingController existingSoldToPartyCodeController =
      TextEditingController();
  TextEditingController existingSoldToPartyAddressController =
      TextEditingController();
  TextEditingController existingSoldToPartyStateController =
      TextEditingController();
  TextEditingController existingSoldToPartyDistrictsController =
      TextEditingController();
  TextEditingController existingShipToPartyNameController =
      TextEditingController();
  TextEditingController existingShipToPartyCodeController =
      TextEditingController();
  TextEditingController existingShipToPartyAddressController =
      TextEditingController();
  TextEditingController existingShipToPartyStateController =
      TextEditingController();
  TextEditingController existingShipToPartyDistrictsController =
      TextEditingController();
  TextEditingController existingTotalPotentialOfSiteController =
      TextEditingController();
  TextEditingController existingQuotationQuantityController =
      TextEditingController();
  TextEditingController existingCurrentBrandUsedController =
      TextEditingController();
  TextEditingController existingExpectedRatePerBagController =
      TextEditingController();
  TextEditingController existingCurrentPriceStarPerBagController =
      TextEditingController();
  TextEditingController existingCurrentPriceCompetitorPerBagController =
      TextEditingController();
  TextEditingController existingContactPersonNameController =
      TextEditingController();
  TextEditingController existingDesignationController = TextEditingController();
  TextEditingController existingContactNumberController =
      TextEditingController();
  TextEditingController existingMailIdController = TextEditingController();
  TextEditingController existingFosSidingController = TextEditingController();
  TextEditingController existingSalesOfficerRemarksController =
      TextEditingController();
  // Existing Lead Details

  // HOS Lead Details
  String? leadId = '';
  String? leadSalesOfficerName = '';
  String? leadDateStamp = '';
  String? leadTimeStamp = '';
  String? leadLatitude = '';
  String? leadLongitude = '';
  String? leadSoldToPartyName = '';
  String? leadSoldToPartyCode = '';
  String? leadSoldToPartyAddress = '';
  String? leadSoldToPartyState = '';
  String? leadSoldToPartyDistricts = '';
  String? leadShipToPartyName = '';
  String? leadShipToPartyCode = '';
  String? leadShipToPartyAddress = '';
  String? leadShipToPartyState = '';
  String? leadShipToPartyDistricts = '';
  String? leadSegment = '';
  String? leadLeadSource = '';
  String? leadProductPackaging = '';
  String? leadTotalQtyRequired = '';
  String? leadQuotationQuantity = '';
  String? leadCurrentBrandUser = '';
  String? leadExpectedRatePerBag = '';
  String? leadCurrentPriceStarPerBag = '';
  String? leadCurrentPriceCompetitorPerBag = '';
  String? leadContactPersonName = '';
  String? leadDesignation = '';
  String? leadContactNumber = '';
  String? leadMailId = '';
  String? leadModeOfPayment = '';
  String? leadCreditTerms = '';
  String? leadAacBlock = '';
  String? leadCategoryTypeOfConstruction = '';
  String? leadLeadStatus = '';
  String? leadNextVisitDate = '';
  String? leadRequirementType = '';
  String? leadExWorks = '';
  String? leadFosSiding = '';
  String? leadSaleOfficerRemarks = '';
  String? leadAssignedTo = '';
  String? leadRequirementTiming = '';
  String? leadAction = '';

  TextEditingController leadSalesOfficerNameController =
      TextEditingController();
  TextEditingController leadDateStampController = TextEditingController();
  TextEditingController leadTimeStampController = TextEditingController();
  TextEditingController leadLatitudeController = TextEditingController();
  TextEditingController leadLongitudeController = TextEditingController();
  TextEditingController leadSoldToPartyNameController = TextEditingController();
  TextEditingController leadSoldToPartyCodeController = TextEditingController();
  TextEditingController leadSoldToPartyAddressController =
      TextEditingController();
  TextEditingController leadSoldToPartyStateController =
      TextEditingController();
  TextEditingController leadSoldToPartyDistrictsController =
      TextEditingController();
  TextEditingController leadShipToPartyNameController = TextEditingController();
  TextEditingController leadShipToPartyCodeController = TextEditingController();
  TextEditingController leadShipToPartyAddressController =
      TextEditingController();
  TextEditingController leadShipToPartyStateController =
      TextEditingController();
  TextEditingController leadShipToPartyDistrictsController =
      TextEditingController();
  TextEditingController leadTotalQtyRequiredController =
      TextEditingController();
  TextEditingController leadQuotationQuantityController =
      TextEditingController();
  TextEditingController leadCurrentBrandUserController =
      TextEditingController();
  TextEditingController leadExpectedRatePerBagController =
      TextEditingController();
  TextEditingController leadCurrentPriceStarPerBagController =
      TextEditingController();
  TextEditingController leadCurrentPriceCompetitorPerBagController =
      TextEditingController();
  TextEditingController leadContactPersonNameController =
      TextEditingController();
  TextEditingController leadDesignationController = TextEditingController();
  TextEditingController leadContactNumberController = TextEditingController();
  TextEditingController leadMailIdController = TextEditingController();
  TextEditingController leadFosSidingController = TextEditingController();
  TextEditingController leadSaleOfficerRemarksController =
      TextEditingController();
  // HOS Lead Details

  // HOS Lead Action Popup
  int _selectedOption = 0;
  String? sendingQuotation;
  String? hosRemarks;

  TextEditingController sendingQuotationController = TextEditingController();
  TextEditingController hosRemarksController = TextEditingController();
  // HOS Lead Action Popup

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
      emp_code = userDetails!.empCode ?? '';
      final headers = {
        'Content-Type': 'application/json',
      };
      final response = await http.get(
        Uri.parse('${AppWebService.sbDevUrl}api/employee/?emp_code=$emp_code'),
        headers: headers,
      );
      // ignore: avoid_print
      print(
          "✅ Api Calling : ${AppWebService.sbDevUrl}api/employee/?emp_code=$emp_code");
      // ignore: avoid_print
      print("API Status Code: ${response.statusCode}");
      // ignore: avoid_print
      print("API Response Body: ${response.body}");

      if (response.statusCode == 200) {
        int empType = 0;
        final responseData = jsonDecode(response.body);
        if (responseData[0]['level'].toString().toLowerCase() == 'nt_to') {
          empType = 2;
          await _fetchDeclarationData('hos');

          setState(() {
            _empCategory = empType;
          });
        } else if (responseData[0]['level'].toString().toLowerCase() == 'nt') {
          empType = 1;
          await _fetchDeclarationData('so');

          setState(() {
            _empCategory = empType;
          });
        } else {
          setState(() {
            _isLoading = false;
          });
        }
      }
    } catch (e) {
      // ignore: avoid_print
      print("❌ Api Calling Error: $e");
    }
  }

  Future<void> _fetchDeclarationData(String type) async {
    try {
      final result = await LeadListData.getCustomerById(emp_code, type);
      // ignore: avoid_print
      print('Lead List count: ${result.length}');

      int hotLead = 0;
      int warmLead = 0;
      int coldLead = 0;

      for (var item in result) {
        if (item.lead_status.toString().toUpperCase() == 'HOT') {
          hotLead++;
        } else if (item.lead_status.toString().toUpperCase() == 'WARM') {
          warmLead++;
        } else if (item.lead_status.toString().toUpperCase() == 'COLD') {
          coldLead++;
        }
      }

      setState(() {
        leadListData = result; // <- updates UI after fetching
        _isLoading = false;
        hotLeadCount = hotLead;
        warmLeadCount = warmLead;
        coldLeadCount = coldLead;
      });
    } catch (e) {
      // ignore: avoid_print
      print("Error fetching data: $e");
    }
  }

  Future<void> _requestForUpdateLeadGeneration() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final Map<String, String> mainObject = {
        'type_lead': existingSegment ?? '',
        'lead_type': existingLeadSource ?? '',
        'product_packaging': (existingProductPackaging ?? '').toUpperCase(),
        'qty_req': existingTotalPotentialOfSiteController.text,
        'month_qty': existingQuotationQuantityController.text,
        'current_brand_used': existingCurrentBrandUsedController.text,
        'exp_rate_per_bag': existingExpectedRatePerBagController.text,
        'current_price': existingCurrentPriceStarPerBagController.text,
        'current_price_competitor':
            existingCurrentPriceCompetitorPerBagController.text,
        'contact_person_name': existingContactPersonNameController.text,
        'designation': existingDesignationController.text,
        'contact_number': existingContactNumberController.text,
        'mail_id': existingMailIdController.text,
        'mode': existingModeOfPayment ?? '',
        'credit_terms': existingCreditTerms ?? '',
        'acc_block_is_required': existingAacBlockRequired ?? '',
        'category_type_construction': existingCategoryTypeOfConstruction ?? '',
        'lead_status': existingLeadStatus ?? '',
        'next_visit_date': existingNextVisitDate ?? '',
        'incoterms': existingRequirementType ?? '',
        'lead_remarks': existingSalesOfficerRemarksController.text,
        'assigned_to': existingAssignedToCode ?? '',
        'r_timing': existingRequirementTiming ?? '',
        'lead_action': 'PENDING',
      };
      if ((existingRequirementType ?? '').toLowerCase() == 'fos') {
        mainObject['serving_location'] = existingFosSidingController.text;
      } else if ((existingRequirementType ?? '').toLowerCase() == 'exw') {
        mainObject['serving_location'] = existingExWorks ?? '';
      } else {
        mainObject['serving_location'] = '';
      }

      // ignore: avoid_print
      print("Lead Generation Send Data : ${jsonEncode(mainObject)}");
      // ignore: avoid_print
      print('${AppWebService.sbDevUrl}api/leadmaster/${existingLeadId!}');

      final response = await http.put(
        Uri.parse(
            '${AppWebService.sbDevUrl}api/leadmaster/${existingLeadId!}/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      // ignore: avoid_print
      print("Status Code: ${response.statusCode}");
      // ignore: avoid_print
      print("Response Body: ${response.body}");
      if (response.statusCode == 200) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _showSnackBar('Successfully Lead Updated.');
        // ignore: use_build_context_synchronously
        Navigator.pop(context);
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

  Future<void> _requestForHosUpdateLeadGeneration() async {
    try {
      setState(() {
        _isLoading = true;
      });
      final Map<String, String> mainObject = {
        'remarks': hosRemarksController.text,
      };

      switch (_selectedOption) {
        case 1:
          mainObject['lead_action'] = 'YES';
          mainObject['approved_price'] = sendingQuotationController.text;
          break;
        case 2:
          mainObject['lead_action'] = 'NO';
          break;
        case 3:
          mainObject['lead_action'] = 'HOLD';
          break;
        case 4:
          mainObject['lead_action'] = 'REVISION';
          break;
      }

      // ignore: avoid_print
      print("Lead Generation Send Data : ${jsonEncode(mainObject)}");
      // ignore: avoid_print
      print('${AppWebService.sbDevUrl}api/leadmaster/${leadId!}');

      final response = await http.put(
        Uri.parse('${AppWebService.sbDevUrl}api/leadmaster/${leadId!}/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      // ignore: avoid_print
      print("Status Code: ${response.statusCode}");
      // ignore: avoid_print
      print("Response Body: ${response.body}");
      if (response.statusCode == 200) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _showSnackBar('Successfully Lead Updated.');
        // ignore: use_build_context_synchronously
        Navigator.pop(context);
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

  void _existingLeadDataShowForSO(LeadListData dataSet) {
    bool value = false;

    if (dataSet.lead_action?.toLowerCase() == 'pending' ||
        dataSet.lead_action?.toLowerCase() == 'revision') {
      value = true;
    }

    setState(() {
      _isSubmitButtonShow = value;
      isDataEditable = value;
      existingLeadId = dataSet.lead_generation_id;

      existingSalesOfficerName = dataSet.emp_details_emp_name;
      existingDateStamp = dataSet.download_time_date_stamp;
      existingTimeStamp = dataSet.download_time_time_stamp;
      existingLatitude = dataSet.latitude;
      existingLongitude = dataSet.longitude;

      existingSoldToPartyName = dataSet.sold_to_party_details_name;
      existingSoldToPartyCode = dataSet.sold_to_party_code;
      existingSoldToPartyAddress = dataSet.sold_to_party_details_address;
      existingSoldToPartyState = dataSet.sold_to_party_details_state;
      existingSoldToPartyDistricts = dataSet.sold_to_party_details_districts;

      existingShipToPartyName = dataSet.ship_to_party_details_name;
      existingShipToPartyCode = dataSet.ship_to_party;
      existingShipToPartyAddress = dataSet.ship_to_party_details_address;
      existingShipToPartyState = dataSet.ship_to_party_details_state;
      existingShipToPartyDistricts = dataSet.ship_to_party_details_districts;

      existingSegment = dataSet.type_lead;
      existingLeadSource = dataSet.lead_type;
      existingProductPackaging = dataSet.product_packaging;

      existingTotalPotentialOfSite = dataSet.qty_req;
      existingQuotationQuantity = dataSet.month_qty;
      existingCurrentBrandUsed = dataSet.current_brand_used;
      existingExpectedRatePerBag = dataSet.exp_rate_per_bag;
      existingCurrentPriceStarPerBag = dataSet.current_price;
      existingCurrentPriceCompetitorPerBag = dataSet.current_price_competitor;

      existingContactPersonName = dataSet.contact_person_name;
      existingDesignation = dataSet.designation;
      existingContactNumber = dataSet.contact_number;
      existingMailId = dataSet.mail_id;

      existingModeOfPayment = dataSet.mode;
      existingCreditTerms = dataSet.credit_terms;
      existingAacBlockRequired = dataSet.acc_block_is_required;
      existingCategoryTypeOfConstruction = dataSet.category_type_construction;
      existingLeadStatus = dataSet.lead_status;
      existingNextVisitDate = dataSet.next_visit_date;
      existingRequirementType = dataSet.incoterms;
      existingExWorks = dataSet.serving_location;
      existingFosSiding = dataSet.serving_location;

      existingSalesOfficerRemarks = dataSet.lead_remarks;

      existingAssignedTo = dataSet.assigned_to_details_emp_name;
      existingAssignedToCode = dataSet.assigned_to;
      existingRequirementTiming = dataSet.r_timing;
    });

    existingSalesOfficerNameController.text =
        dataSet.emp_details_emp_name ?? '';
    existingDateStampController.text = dataSet.download_time_date_stamp ?? '';
    existingTimeStampController.text = dataSet.download_time_time_stamp ?? '';
    existingLatitudeController.text = dataSet.latitude ?? '';
    existingLongitudeController.text = dataSet.longitude ?? '';
    existingSoldToPartyNameController.text =
        dataSet.sold_to_party_details_name ?? '';
    existingSoldToPartyCodeController.text = dataSet.sold_to_party_code ?? '';
    existingSoldToPartyAddressController.text =
        dataSet.sold_to_party_details_address ?? '';
    existingSoldToPartyStateController.text =
        dataSet.sold_to_party_details_state ?? '';
    existingSoldToPartyDistrictsController.text =
        dataSet.sold_to_party_details_districts ?? '';
    existingShipToPartyNameController.text =
        dataSet.ship_to_party_details_name ?? '';
    existingShipToPartyCodeController.text = dataSet.ship_to_party ?? '';
    existingShipToPartyAddressController.text =
        dataSet.ship_to_party_details_address ?? '';
    existingShipToPartyStateController.text =
        dataSet.ship_to_party_details_state ?? '';
    existingShipToPartyDistrictsController.text =
        dataSet.ship_to_party_details_districts ?? '';
    existingTotalPotentialOfSiteController.text = dataSet.qty_req ?? '';
    existingQuotationQuantityController.text = dataSet.month_qty ?? '';
    existingCurrentBrandUsedController.text = dataSet.current_brand_used ?? '';
    existingExpectedRatePerBagController.text = dataSet.exp_rate_per_bag ?? '';
    existingCurrentPriceStarPerBagController.text = dataSet.current_price ?? '';
    existingCurrentPriceCompetitorPerBagController.text =
        dataSet.current_price_competitor ?? '';
    existingContactPersonNameController.text =
        dataSet.contact_person_name ?? '';
    existingDesignationController.text = dataSet.designation ?? '';
    existingContactNumberController.text = dataSet.contact_number ?? '';
    existingMailIdController.text = dataSet.mail_id ?? '';
    existingFosSidingController.text = dataSet.serving_location ?? '';
    existingSalesOfficerRemarksController.text = dataSet.lead_remarks ?? '';
  }

  void _existingLeadDataShowForHOS(LeadListData dataSet) {
    setState(() {
      leadId = dataSet.lead_generation_id;

      leadSalesOfficerName = dataSet.emp_details_emp_name;
      leadDateStamp = dataSet.download_time_date_stamp;
      leadTimeStamp = dataSet.download_time_time_stamp;
      leadLatitude = dataSet.latitude;
      leadLongitude = dataSet.longitude;

      leadSoldToPartyName = dataSet.sold_to_party_details_name;
      leadSoldToPartyCode = dataSet.sold_to_party_code;
      leadSoldToPartyAddress = dataSet.sold_to_party_details_address;
      leadSoldToPartyState = dataSet.sold_to_party_details_state;
      leadSoldToPartyDistricts = dataSet.sold_to_party_details_districts;

      leadShipToPartyName = dataSet.ship_to_party_details_name;
      leadShipToPartyCode = dataSet.ship_to_party;
      leadShipToPartyAddress = dataSet.ship_to_party_details_address;
      leadShipToPartyState = dataSet.ship_to_party_details_state;
      leadShipToPartyDistricts = dataSet.ship_to_party_details_districts;

      leadSegment = dataSet.type_lead;
      leadLeadSource = dataSet.lead_type;
      leadProductPackaging = dataSet.product_packaging;

      leadTotalQtyRequired = dataSet.qty_req;
      leadQuotationQuantity = dataSet.month_qty;
      leadCurrentBrandUser = dataSet.current_brand_used;
      leadExpectedRatePerBag = dataSet.exp_rate_per_bag;
      leadCurrentPriceStarPerBag = dataSet.current_price;
      leadCurrentPriceCompetitorPerBag = dataSet.current_price_competitor;

      leadContactPersonName = dataSet.contact_person_name;
      leadDesignation = dataSet.designation;
      leadContactNumber = dataSet.contact_number;
      leadMailId = dataSet.mail_id;

      leadModeOfPayment = dataSet.mode;
      leadCreditTerms = dataSet.credit_terms;
      leadAacBlock = dataSet.acc_block_is_required;
      leadCategoryTypeOfConstruction = dataSet.category_type_construction;
      leadLeadStatus = dataSet.lead_status;
      leadNextVisitDate = dataSet.next_visit_date;
      leadRequirementType = dataSet.incoterms;
      leadExWorks = dataSet.serving_location;
      leadFosSiding = dataSet.serving_location;

      leadSaleOfficerRemarks = dataSet.lead_remarks;

      leadAssignedTo = dataSet.assigned_to_details_emp_name;
      leadRequirementTiming = dataSet.r_timing;
      leadAction = dataSet.lead_action;
    });
  }

  void _checkUpdateLeadInformation() {
    if (existingSegment == '') {
      _showSnackBar("Please select segment.");
    } else if (existingLeadSource == '') {
      _showSnackBar("Please select lead source.");
    } else if (existingCurrentBrandUsedController.text == '') {
      _showSnackBar("Please enter current brand used.");
    } else if (existingExpectedRatePerBagController.text == '') {
      _showSnackBar("Please enter expected rate per bag.");
    } else if (existingCurrentPriceStarPerBagController.text == '') {
      _showSnackBar("Please enter current price star rs per bag.");
    } else if (existingCurrentPriceCompetitorPerBagController.text == '') {
      _showSnackBar("Please enter current price competitor rs per bag.");
    } else if (existingContactPersonNameController.text == '') {
      _showSnackBar("Please enter contact person name.");
    } else if (existingDesignationController.text == '') {
      _showSnackBar("Please enter contact person designation.");
    } else if (existingContactNumberController.text == '') {
      _showSnackBar("Please enter contact person phone number.");
    } else if (existingContactNumberController.text.length != 10) {
      _showSnackBar("Please enter contact person correct phone number.");
    } else if (existingMailIdController.text == '') {
      _showSnackBar("Please enter contact person mail id.");
    } else if (existingCategoryTypeOfConstruction == '') {
      _showSnackBar("Please select construction type.");
    } else if (existingLeadStatus == '') {
      _showSnackBar("Please select lead status.");
    } else if (existingNextVisitDate == '') {
      _showSnackBar("Please select next visit date.");
    } else if (existingRequirementType == '') {
      _showSnackBar("Please select requirement type.");
    } else if (existingAssignedTo == '') {
      _showSnackBar("Please select assigned to.");
    } else if (existingRequirementTiming == '') {
      _showSnackBar("Please select requirements timing.");
    } else {
      _requestForUpdateLeadGeneration();
    }
  }

  void _checkUpdateHosLeadInformation() {
    if (_selectedOption == 0) {
      _showSnackBar("Please select lead status you want to update.");
    } else if (sendingQuotationController.text == '' && _selectedOption == 1) {
      _showSnackBar('Please enter Quotation Amount.');
    } else if (hosRemarksController.text == '') {
      _showSnackBar('Please enter Remarks.');
    } else {
      _requestForHosUpdateLeadGeneration();
    }
  }

  void _showSnackBar(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: Colors.red,
      ),
    );
  }

  void showSelectorLeadDialog<T>({
    required String dialogTitle,
    required List<T> items, // <--- add this
    required LeadListData Function(T) getDisplayText,
    required void Function(T selectedItem) onSelected,
    bool enableSearch = true,
  }) {
    TextEditingController searchController = TextEditingController();
    List<T> allItems = List.from(items); // use provided list
    List<T> filteredItems = allItems.where((item) {
      final leadStatus = getDisplayText(item).lead_status?.toLowerCase() ?? '';
      // ignore: avoid_print
      print(leadStatus);
      switch (_selectedLeadStatusRadio) {
        case 1:
          if (leadStatus.toLowerCase() != 'hot') {
            return false;
          }
          break;
        case 2:
          if (leadStatus.toLowerCase() != 'warm') {
            return false;
          }
          break;
        case 3:
          if (leadStatus.toLowerCase() != 'cold') {
            return false;
          }
          break;
      }
      return true;
    }).toList();

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) {
        return StatefulBuilder(
          builder: (context, setState) {
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
                                          .lead_generation_id
                                          ?.toLowerCase() ??
                                      '';
                                  final soldTo = getDisplayText(item)
                                          .sold_to_party_details_name
                                          ?.toLowerCase() ??
                                      '';
                                  final shipTo = getDisplayText(item)
                                          .ship_to_party_details_name
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
                                        Color.fromARGB(255, 45, 45, 45);

                                    if ((getDisplayText(item).lead_action ?? '')
                                            .toLowerCase() ==
                                        'pending') {
                                      showData = 'Pending';
                                      textColor =
                                          Color.fromARGB(255, 181, 147, 0);
                                    } else if ((getDisplayText(item)
                                                    .lead_action ??
                                                '')
                                            .toLowerCase() ==
                                        'hold') {
                                      showData = 'Hold';
                                      textColor =
                                          Color.fromARGB(255, 0, 121, 181);
                                    } else if ((getDisplayText(item)
                                                    .lead_action ??
                                                '')
                                            .toLowerCase() ==
                                        'revision') {
                                      showData = 'Revision';
                                      textColor =
                                          Color.fromARGB(255, 181, 181, 0);
                                    } else if ((getDisplayText(item)
                                                    .lead_action ??
                                                '')
                                            .toLowerCase() ==
                                        'no') {
                                      showData = 'Cancel';
                                      textColor =
                                          Color.fromARGB(255, 181, 0, 0);
                                    } else if ((getDisplayText(item)
                                                    .lead_action ??
                                                '')
                                            .toLowerCase() ==
                                        'yes') {
                                      showData = 'Approved';
                                      textColor =
                                          Color.fromARGB(255, 0, 181, 0);
                                    }

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
                                                  "I'd : ${getDisplayText(item).lead_generation_id}",
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
                                              "Sold to Party : ${getDisplayText(item).sold_to_party_details_name}",
                                              style: TextStyle(
                                                fontSize: 12,
                                                color: Color.fromARGB(
                                                    255, 45, 45, 45),
                                              ),
                                            ),
                                            const SizedBox(height: 6),
                                            Text(
                                              "Ship to Party : ${getDisplayText(item).ship_to_party_details_name}",
                                              style: TextStyle(
                                                fontSize: 12,
                                                color: Color.fromARGB(
                                                    255, 45, 45, 45),
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

  void _showPopup() {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (BuildContext context) {
        return DraggableScrollableSheet(
          initialChildSize: 0.8,
          minChildSize: 0.6,
          maxChildSize: 0.92,
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
                  padding: EdgeInsets.only(
                    left: 20,
                    right: 20,
                    top: 12,
                    bottom: MediaQuery.of(context).viewInsets.bottom + 20,
                  ),
                  child: SingleChildScrollView(
                    controller: scrollController,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
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
                          "Please select an option",
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 8),

                        // Radio options
                        RadioListTile<int>(
                          contentPadding: EdgeInsets.zero,
                          title: const Text("Yes, send quotation."),
                          value: 1,
                          groupValue: _selectedOption,
                          onChanged: (value) =>
                              setState(() => _selectedOption = value!),
                        ),
                        RadioListTile<int>(
                          contentPadding: EdgeInsets.zero,
                          title: const Text("No, not required quotation."),
                          value: 2,
                          groupValue: _selectedOption,
                          onChanged: (value) =>
                              setState(() => _selectedOption = value!),
                        ),
                        RadioListTile<int>(
                          contentPadding: EdgeInsets.zero,
                          title: const Text("Hold."),
                          value: 3,
                          groupValue: _selectedOption,
                          onChanged: (value) =>
                              setState(() => _selectedOption = value!),
                        ),
                        RadioListTile<int>(
                          contentPadding: EdgeInsets.zero,
                          title: const Text("Send back for revision."),
                          value: 4,
                          groupValue: _selectedOption,
                          onChanged: (value) =>
                              setState(() => _selectedOption = value!),
                        ),

                        const SizedBox(height: 10),

                        // Quotation amount — only for option 1
                        if (_selectedOption == 1) ...[
                          TextField(
                            controller: sendingQuotationController,
                            keyboardType: TextInputType.number,
                            decoration: const InputDecoration(
                              labelText: "Enter Quotation Amount",
                              border: OutlineInputBorder(),
                            ),
                          ),
                          const SizedBox(height: 14),
                        ],

                        // Tall remarks field
                        TextField(
                          controller: hosRemarksController,
                          maxLines: 6,
                          minLines: 6,
                          textAlignVertical: TextAlignVertical.top,
                          decoration: const InputDecoration(
                            labelText: "Enter Your Remarks",
                            alignLabelWithHint: true,
                            border: OutlineInputBorder(),
                          ),
                        ),

                        const SizedBox(height: 20),

                        // Action buttons
                        Row(
                          children: [
                            Expanded(
                              child: OutlinedButton(
                                onPressed: () => Navigator.of(context).pop(),
                                style: OutlinedButton.styleFrom(
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 14),
                                  side: const BorderSide(color: Colors.black26),
                                ),
                                child: const Text(
                                  "Cancel",
                                  style: TextStyle(color: Colors.black),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: ElevatedButton(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.red,
                                  foregroundColor: Colors.white,
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 14),
                                ),
                                onPressed: () {
                                  Navigator.of(context).pop();
                                  _checkUpdateHosLeadInformation();
                                },
                                child: const Text("Submit"),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                );
              },
            );
          },
        );
      },
    );
  }

  // ignore: non_constant_identifier_names
  void clearAllDataSO_Level() {
    setState(() {
      existingLeadId = '';
      existingReferredBy = '';
      existingSalesOfficerName = '';
      existingDateStamp = '';
      existingTimeStamp = '';
      existingLatitude = '';
      existingLongitude = '';
      existingSoldToPartyName = '';
      existingSoldToPartyCode = '';
      existingSoldToPartyAddress = '';
      existingSoldToPartyState = '';
      existingSoldToPartyDistricts = '';
      existingShipToPartyName = '';
      existingShipToPartyCode = '';
      existingShipToPartyAddress = '';
      existingShipToPartyState = '';
      existingShipToPartyDistricts = '';
      existingSegment = '';
      existingLeadSource = '';
      existingProductPackaging = '';
      existingTotalPotentialOfSite = '';
      existingQuotationQuantity = '';
      existingCurrentBrandUsed = '';
      existingExpectedRatePerBag = '';
      existingCurrentPriceStarPerBag = '';
      existingCurrentPriceCompetitorPerBag = '';
      existingContactPersonName = '';
      existingDesignation = '';
      existingContactNumber = '';
      existingMailId = '';
      existingModeOfPayment = '';
      existingCreditTerms = '';
      existingAacBlockRequired = '';
      existingCategoryTypeOfConstruction = '';
      existingLeadStatus = '';
      existingNextVisitDate = '';
      existingRequirementType = '';
      existingExWorks = '';
      existingFosSiding = '';
      existingSalesOfficerRemarks = '';
      existingAssignedTo = '';
      existingAssignedToCode = '';
      existingRequirementTiming = '';
    });
    existingReferredByController.text = '';
    existingSalesOfficerNameController.text = '';
    existingDateStampController.text = '';
    existingTimeStampController.text = '';
    existingLatitudeController.text = '';
    existingLongitudeController.text = '';
    existingSoldToPartyNameController.text = '';
    existingSoldToPartyCodeController.text = '';
    existingSoldToPartyAddressController.text = '';
    existingSoldToPartyStateController.text = '';
    existingSoldToPartyDistrictsController.text = '';
    existingShipToPartyNameController.text = '';
    existingShipToPartyCodeController.text = '';
    existingShipToPartyAddressController.text = '';
    existingShipToPartyStateController.text = '';
    existingShipToPartyDistrictsController.text = '';
    existingTotalPotentialOfSiteController.text = '';
    existingQuotationQuantityController.text = '';
    existingCurrentBrandUsedController.text = '';
    existingExpectedRatePerBagController.text = '';
    existingCurrentPriceStarPerBagController.text = '';
    existingCurrentPriceCompetitorPerBagController.text = '';
    existingContactPersonNameController.text = '';
    existingDesignationController.text = '';
    existingContactNumberController.text = '';
    existingMailIdController.text = '';
    existingFosSidingController.text = '';
    existingSalesOfficerRemarksController.text = '';
  }

  // ignore: non_constant_identifier_names
  void clearAllDataHOS_Level() {
    setState(() {
      leadId = '';
      existingReferredBy = '';
      leadSalesOfficerName = '';
      leadDateStamp = '';
      leadTimeStamp = '';
      leadLatitude = '';
      leadLongitude = '';
      leadSoldToPartyName = '';
      leadSoldToPartyCode = '';
      leadSoldToPartyAddress = '';
      leadSoldToPartyState = '';
      leadSoldToPartyDistricts = '';
      leadShipToPartyName = '';
      leadShipToPartyCode = '';
      leadShipToPartyAddress = '';
      leadShipToPartyState = '';
      leadShipToPartyDistricts = '';
      leadSegment = '';
      leadLeadSource = '';
      leadProductPackaging = '';
      leadTotalQtyRequired = '';
      leadQuotationQuantity = '';
      leadCurrentBrandUser = '';
      leadExpectedRatePerBag = '';
      leadCurrentPriceStarPerBag = '';
      leadCurrentPriceCompetitorPerBag = '';
      leadContactPersonName = '';
      leadDesignation = '';
      leadContactNumber = '';
      leadMailId = '';
      leadModeOfPayment = '';
      leadCreditTerms = '';
      leadAacBlock = '';
      leadCategoryTypeOfConstruction = '';
      leadLeadStatus = '';
      leadNextVisitDate = '';
      leadRequirementType = '';
      leadExWorks = '';
      leadFosSiding = '';
      leadSaleOfficerRemarks = '';
      leadAssignedTo = '';
      leadRequirementTiming = '';
    });
    existingReferredByController.text = '';
    leadSalesOfficerNameController.text = '';
    leadDateStampController.text = '';
    leadTimeStampController.text = '';
    leadLatitudeController.text = '';
    leadLongitudeController.text = '';
    leadSoldToPartyNameController.text = '';
    leadSoldToPartyCodeController.text = '';
    leadSoldToPartyAddressController.text = '';
    leadSoldToPartyStateController.text = '';
    leadSoldToPartyDistrictsController.text = '';
    leadShipToPartyNameController.text = '';
    leadShipToPartyCodeController.text = '';
    leadShipToPartyAddressController.text = '';
    leadShipToPartyStateController.text = '';
    leadShipToPartyDistrictsController.text = '';
    leadTotalQtyRequiredController.text = '';
    leadQuotationQuantityController.text = '';
    leadCurrentBrandUserController.text = '';
    leadExpectedRatePerBagController.text = '';
    leadCurrentPriceStarPerBagController.text = '';
    leadCurrentPriceCompetitorPerBagController.text = '';
    leadContactPersonNameController.text = '';
    leadDesignationController.text = '';
    leadContactNumberController.text = '';
    leadMailIdController.text = '';
    leadFosSidingController.text = '';
    leadSaleOfficerRemarksController.text = '';
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
                          if (_empCategory == 1) ...[
                            // Filter against lead status
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Expanded(
                                      child: RadioListTile<int>(
                                        contentPadding: EdgeInsets.zero,
                                        dense: true,
                                        visualDensity: VisualDensity(
                                            horizontal: -4, vertical: -4),
                                        title: Text("Hot ($hotLeadCount)"),
                                        value: 1,
                                        // ignore: deprecated_member_use
                                        groupValue: _selectedLeadStatusRadio,
                                        // ignore: deprecated_member_use
                                        onChanged: (value) {
                                          clearAllDataSO_Level();
                                          setState(() {
                                            _selectedLeadStatusRadio = value!;
                                          });
                                        },
                                      ),
                                    ),
                                    Expanded(
                                      child: RadioListTile<int>(
                                        contentPadding: EdgeInsets.zero,
                                        dense: true,
                                        visualDensity: VisualDensity(
                                            horizontal: -4, vertical: -4),
                                        title: Text("Warm ($warmLeadCount)"),
                                        value: 2,
                                        // ignore: deprecated_member_use
                                        groupValue: _selectedLeadStatusRadio,
                                        // ignore: deprecated_member_use
                                        onChanged: (value) {
                                          clearAllDataSO_Level();
                                          setState(() {
                                            _selectedLeadStatusRadio = value!;
                                          });
                                        },
                                      ),
                                    ),
                                    Expanded(
                                      child: RadioListTile<int>(
                                        contentPadding: EdgeInsets.zero,
                                        dense: true,
                                        visualDensity: VisualDensity(
                                            horizontal: -4, vertical: -4),
                                        title: Text("Cold ($coldLeadCount)"),
                                        value: 3,
                                        // ignore: deprecated_member_use
                                        groupValue: _selectedLeadStatusRadio,
                                        // ignore: deprecated_member_use
                                        onChanged: (value) {
                                          clearAllDataSO_Level();
                                          setState(() {
                                            _selectedLeadStatusRadio = value!;
                                          });
                                        },
                                      ),
                                    ),
                                  ],
                                ),
                                SizedBox(height: 7),
                              ],
                            ),
                            if (_selectedLeadStatusRadio != 0) ...[
                              // Basic information
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Search Lead Id',
                                      onPressed: () {
                                        showSelectorLeadDialog<LeadListData>(
                                          dialogTitle: 'Select Lead',
                                          items: leadListData!,
                                          getDisplayText: (item) => item,
                                          onSelected: (item) {
                                            _existingLeadDataShowForSO(item);
                                          },
                                        );
                                      },
                                      value: existingLeadId,
                                      errorMessage: '',
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller: existingReferredByController,
                                    hintText: 'Referred By',
                                    label: 'Referred By',
                                    keyboardType: TextInputType.name,
                                    isEditable: false,
                                    initialValue: existingReferredBy,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ),
                              // Sales Officer Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          existingSalesOfficerNameController,
                                      hintText: 'Sales Officer Name',
                                      label: 'Sales Officer Name',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingSalesOfficerName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: existingDateStampController,
                                      hintText: 'Date Stamp',
                                      label: 'Date Stamp',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingDateStamp,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: existingTimeStampController,
                                      hintText: 'Time Stamp',
                                      label: 'Time Stamp',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingTimeStamp,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: existingLatitudeController,
                                      hintText: 'Latitude',
                                      label: 'Latitude',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingLatitude,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: existingLongitudeController,
                                      hintText: 'Longitude',
                                      label: 'Longitude',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingLongitude,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Sold to Party Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          existingSoldToPartyNameController,
                                      hintText: 'Sold to Party Name',
                                      label: 'Sold to Party Name',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingSoldToPartyName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingSoldToPartyCodeController,
                                      hintText: 'Sold to Party Code',
                                      label: 'Sold to Party Code',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingSoldToPartyCode,
                                      isMandatory: false,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingSoldToPartyAddressController,
                                      hintText: 'Sold to Party Address',
                                      label: 'Sold to Party Address',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingSoldToPartyAddress,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingSoldToPartyStateController,
                                      hintText: 'Sold to Party State',
                                      label: 'Sold to Party State',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingSoldToPartyState,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingSoldToPartyDistrictsController,
                                      hintText: 'Sold to Party Districts',
                                      label: 'Sold to Party Districts',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue:
                                          existingSoldToPartyDistricts,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Ship to Party Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          existingShipToPartyNameController,
                                      hintText: 'Ship to Party Name',
                                      label: 'Ship to Party Name',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingShipToPartyName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingShipToPartyCodeController,
                                      hintText: 'Ship to Party Code',
                                      label: 'Ship to Party Code',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingShipToPartyCode,
                                      isMandatory: false,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingShipToPartyAddressController,
                                      hintText: 'Ship to Party Address',
                                      label: 'Ship to Party Address',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingShipToPartyAddress,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingShipToPartyStateController,
                                      hintText: 'Ship to Party State',
                                      label: 'Ship to Party State',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: existingShipToPartyState,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingShipToPartyDistrictsController,
                                      hintText: 'Ship to Party Districts',
                                      label: 'Ship to Party Districts',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue:
                                          existingShipToPartyDistricts,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Source, Segment, Packaging
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Segment',
                                        onPressed: () {
                                          showSelectorDialog<SegmentList>(
                                            fetchData: () => SegmentList
                                                .fetchDataFromStatic(),
                                            dialogTitle: 'Select Segment',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingSegment = item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: existingSegment,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Segment.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Lead Source',
                                        onPressed: () {
                                          showSelectorDialog<LeadSourceList>(
                                            fetchData: () => LeadSourceList
                                                .fetchDataFromStatic(),
                                            dialogTitle: 'Select Lead Source',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingLeadSource = item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: existingLeadSource,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Lead Source.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Product + Packaging',
                                        onPressed: () {
                                          showSelectorDialog<ProductNameList>(
                                            fetchData: () => ProductNameList
                                                .fetchDataFromApi(),
                                            dialogTitle:
                                                'Select Product & Packaging',
                                            getDisplayText: (item) =>
                                                item.productName ?? '',
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingProductPackaging =
                                                    item.productName;
                                              });
                                            },
                                          );
                                        },
                                        value: existingProductPackaging,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Product & Packaging.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                  ]),
                              // Quantity Required and Price
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          existingTotalPotentialOfSiteController,
                                      hintText: 'Total Qty Required (MT)',
                                      label: 'Total Potential of Site',
                                      maxLength: 5,
                                      keyboardType: TextInputType.number,
                                      isEditable: isDataEditable,
                                      initialValue:
                                          existingTotalPotentialOfSite,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingQuotationQuantityController,
                                      hintText: 'Monthly Qty Required (MT)',
                                      label: 'Quotation Quantity',
                                      maxLength: 5,
                                      keyboardType: TextInputType.number,
                                      isEditable: isDataEditable,
                                      initialValue: existingQuotationQuantity,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingCurrentBrandUsedController,
                                      hintText: 'Current Brand Used',
                                      label: 'Current Brand Used',
                                      maxLength: 25,
                                      keyboardType: TextInputType.name,
                                      isEditable: isDataEditable,
                                      initialValue: existingCurrentBrandUsed,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingExpectedRatePerBagController,
                                      hintText: 'Expected Rate Per Bag',
                                      label: 'Expected Rate Per Bag',
                                      maxLength: 4,
                                      keyboardType: TextInputType.number,
                                      isEditable: isDataEditable,
                                      initialValue: existingExpectedRatePerBag,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingCurrentPriceStarPerBagController,
                                      hintText: 'Current Price Star',
                                      label: 'Current Price Star Rs. Per Bag',
                                      maxLength: 4,
                                      keyboardType: TextInputType.number,
                                      isEditable: isDataEditable,
                                      initialValue:
                                          existingCurrentPriceStarPerBag,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingCurrentPriceCompetitorPerBagController,
                                      hintText: 'Current Price Competitor',
                                      label:
                                          'Current Price Competitor Rs. Per Bag',
                                      maxLength: 4,
                                      keyboardType: TextInputType.number,
                                      isEditable: isDataEditable,
                                      initialValue:
                                          existingCurrentPriceCompetitorPerBag,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Contact Person Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          existingContactPersonNameController,
                                      hintText: 'Contact Person Name',
                                      label: 'Contact Person Name',
                                      maxLength: 25,
                                      keyboardType: TextInputType.name,
                                      isEditable: isDataEditable,
                                      initialValue: existingContactPersonName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: existingDesignationController,
                                      hintText: 'Designation',
                                      label: 'Designation',
                                      maxLength: 25,
                                      keyboardType: TextInputType.name,
                                      isEditable: isDataEditable,
                                      initialValue: existingDesignation,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          existingContactNumberController,
                                      hintText: 'Contact Number',
                                      label: 'Contact Number',
                                      maxLength: 10,
                                      keyboardType: TextInputType.number,
                                      isEditable: isDataEditable,
                                      initialValue: existingContactNumber,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: existingMailIdController,
                                      hintText: 'Mail Id',
                                      label: 'Mail Id',
                                      maxLength: 50,
                                      keyboardType: TextInputType.emailAddress,
                                      isEditable: isDataEditable,
                                      initialValue: existingMailId,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Payment Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Mode of Payment',
                                        onPressed: () {
                                          showSelectorDialog<ModeOfPaymentList>(
                                            fetchData: () => ModeOfPaymentList
                                                .fetchDataFromStatic(),
                                            dialogTitle:
                                                'Select Mode of Payment',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingModeOfPayment =
                                                    item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: existingModeOfPayment,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Mode of Payment.",
                                        isMandatory: false),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Credit Terms',
                                        onPressed: () {
                                          showSelectorDialog<CreditTermsList>(
                                            fetchData: () => CreditTermsList
                                                .fetchDataFromStatic(),
                                            dialogTitle: 'Select Credit Terms',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingCreditTerms =
                                                    item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: existingCreditTerms,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Credit Terms.",
                                        isMandatory: false),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel:
                                            'AAC Block is Required or Not',
                                        onPressed: () {
                                          showSelectorDialog<
                                              AacBlockRequiredCheckList>(
                                            fetchData: () =>
                                                AacBlockRequiredCheckList
                                                    .fetchDataFromStatic(),
                                            dialogTitle:
                                                'Select AAC Block is Required or Not',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingAacBlockRequired =
                                                    item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: existingAacBlockRequired,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update AAC Block Required Status.",
                                        isMandatory: false),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel:
                                            'Category Type of Construction',
                                        onPressed: () {
                                          showSelectorDialog<
                                              ConstructionTypeList>(
                                            fetchData: () =>
                                                ConstructionTypeList
                                                    .fetchDataFromStatic(),
                                            dialogTitle:
                                                'Select Category Type of Construction',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingCategoryTypeOfConstruction =
                                                    item.label;
                                              });
                                            },
                                          );
                                        },
                                        value:
                                            existingCategoryTypeOfConstruction,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Category of Construction.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Lead Status',
                                        onPressed: () {
                                          showSelectorDialog<LeadStatusList>(
                                            fetchData: () => LeadStatusList
                                                .fetchDataFromStatic(),
                                            dialogTitle: 'Select Lead Status',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingLeadStatus = item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: existingLeadStatus,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Lead Status.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Next Visit Date',
                                        onPressed: () async {
                                          DateTime? pickedDate =
                                              await showDatePicker(
                                            context: context,
                                            initialDate: DateTime.now().add(
                                                const Duration(
                                                    days:
                                                        1)), // tomorrow as default
                                            firstDate: DateTime.now().add(
                                                const Duration(
                                                    days:
                                                        1)), // only future dates
                                            lastDate: DateTime(2100),
                                          );

                                          if (pickedDate != null) {
                                            setState(() {
                                              existingNextVisitDate =
                                                  "${pickedDate.day}-${pickedDate.month}-${pickedDate.year}";
                                            });
                                          }
                                        },
                                        value: existingNextVisitDate,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Next Visit Date.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Requirement Type',
                                        onPressed: () {
                                          showSelectorDialog<
                                              RequirementTypeList>(
                                            fetchData: () => RequirementTypeList
                                                .fetchDataFromStatic(),
                                            dialogTitle:
                                                'Select Requirement Type',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                existingRequirementType =
                                                    item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: existingRequirementType,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Requirement Type.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    if (existingRequirementType!
                                            .toLowerCase() ==
                                        'exw') ...[
                                      SelectButtonWithLabel(
                                          buttonLabel: 'Ex. Works',
                                          onPressed: () {
                                            showSelectorDialog<ExWorkList>(
                                              fetchData: () => ExWorkList
                                                  .fetchDataFromStatic(),
                                              dialogTitle: 'Select Ex. Works',
                                              getDisplayText: (item) =>
                                                  item.label,
                                              enableSearch: false,
                                              onSelected: (item) {
                                                setState(() {
                                                  existingExWorks = item.label;
                                                });
                                              },
                                            );
                                          },
                                          value: existingExWorks,
                                          isMandatory: true),
                                      SizedBox(height: 7),
                                    ],
                                    if (existingRequirementType!
                                            .toLowerCase() ==
                                        'fos') ...[
                                      LabeledTextField(
                                        controller: existingFosSidingController,
                                        hintText: 'FOS Siding',
                                        label: 'FOS Siding',
                                        maxLength: 100,
                                        keyboardType: TextInputType.name,
                                        isEditable: true,
                                        initialValue: existingFosSiding,
                                      ),
                                      SizedBox(height: 7),
                                    ],
                                  ]),
                              // Remarks & Assigned Person Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          existingSalesOfficerRemarksController,
                                      hintText: 'Sales Officer Remarks',
                                      label: 'Sales Officer Remarks',
                                      maxLength: 255,
                                      keyboardType: TextInputType.name,
                                      isEditable: isDataEditable,
                                      initialValue: existingSalesOfficerRemarks,
                                      isMandatory: false,
                                    ),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Assigned To',
                                        onPressed: () {
                                          showSelectorDialog<EmployeeNameList>(
                                            fetchData: () => EmployeeNameList
                                                .fetchDataFromApi('hos'),
                                            dialogTitle: 'Select Assigned To',
                                            getDisplayText: (item) =>
                                                item.empName ?? '',
                                            enableSearch: true,
                                            onSelected: (item) {
                                              setState(() {
                                                existingAssignedTo =
                                                    item.empName;
                                                existingAssignedToCode =
                                                    item.empCode;
                                              });
                                            },
                                          );
                                        },
                                        value: existingAssignedTo,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Assigned To.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Requirement Timing',
                                        onPressed: () {
                                          showSelectorDialog<
                                              RequirementTimingList>(
                                            fetchData: () =>
                                                RequirementTimingList
                                                    .fetchDataFromStatic(),
                                            dialogTitle:
                                                'Select Requirement Timing',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: true,
                                            onSelected: (item) {
                                              setState(() {
                                                existingRequirementTiming =
                                                    item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: existingRequirementTiming,
                                        isEnabled: isDataEditable,
                                        errorMessage:
                                            "You can't able to update Requirement Timing.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                  ])
                            ]
                          ],
                          if (_empCategory == 2) ...[
                            // Filter against lead status
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Expanded(
                                      child: RadioListTile<int>(
                                        contentPadding: EdgeInsets.zero,
                                        dense: true,
                                        visualDensity: VisualDensity(
                                            horizontal: -4, vertical: -4),
                                        title: Text("Hot ($hotLeadCount)"),
                                        value: 1,
                                        // ignore: deprecated_member_use
                                        groupValue: _selectedLeadStatusRadio,
                                        // ignore: deprecated_member_use
                                        onChanged: (value) {
                                          clearAllDataHOS_Level();
                                          setState(() {
                                            _selectedLeadStatusRadio = value!;
                                          });
                                        },
                                      ),
                                    ),
                                    Expanded(
                                      child: RadioListTile<int>(
                                        contentPadding: EdgeInsets.zero,
                                        dense: true,
                                        visualDensity: VisualDensity(
                                            horizontal: -4, vertical: -4),
                                        title: Text("Warm ($warmLeadCount)"),
                                        value: 2,
                                        // ignore: deprecated_member_use
                                        groupValue: _selectedLeadStatusRadio,
                                        // ignore: deprecated_member_use
                                        onChanged: (value) {
                                          clearAllDataHOS_Level();
                                          setState(() {
                                            _selectedLeadStatusRadio = value!;
                                          });
                                        },
                                      ),
                                    ),
                                    Expanded(
                                      child: RadioListTile<int>(
                                        contentPadding: EdgeInsets.zero,
                                        dense: true,
                                        visualDensity: VisualDensity(
                                            horizontal: -4, vertical: -4),
                                        title: Text("Cold ($coldLeadCount)"),
                                        value: 3,
                                        // ignore: deprecated_member_use
                                        groupValue: _selectedLeadStatusRadio,
                                        // ignore: deprecated_member_use
                                        onChanged: (value) {
                                          clearAllDataHOS_Level();
                                          setState(() {
                                            _selectedLeadStatusRadio = value!;
                                          });
                                        },
                                      ),
                                    ),
                                  ],
                                ),
                                SizedBox(height: 7),
                              ],
                            ),
                            if (_selectedLeadStatusRadio != 0) ...[
                              // Basic information
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Search Lead Id',
                                      onPressed: () {
                                        showSelectorLeadDialog<LeadListData>(
                                          dialogTitle: 'Select Lead',
                                          items: leadListData!,
                                          getDisplayText: (item) => item,
                                          onSelected: (item) {
                                            _existingLeadDataShowForHOS(item);
                                          },
                                        );
                                      },
                                      value: leadId,
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller: existingReferredByController,
                                    hintText: 'Referred By',
                                    label: 'Referred By',
                                    keyboardType: TextInputType.name,
                                    isEditable: false,
                                    initialValue: existingReferredBy,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ),
                              // Sales Officer Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          leadSalesOfficerNameController,
                                      hintText: 'Sales Officer Name',
                                      label: 'Sales Officer Name',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadSalesOfficerName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadDateStampController,
                                      hintText: 'Date Stamp',
                                      label: 'Date Stamp',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadDateStamp,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadTimeStampController,
                                      hintText: 'Time Stamp',
                                      label: 'Time Stamp',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadTimeStamp,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadLatitudeController,
                                      hintText: 'Latitude',
                                      label: 'Latitude',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadLatitude,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadLongitudeController,
                                      hintText: 'Longitude',
                                      label: 'Longitude',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadLongitude,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Sold to Party Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller: leadSoldToPartyNameController,
                                      hintText: 'Sold to Party Name',
                                      label: 'Sold to Party Name',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadSoldToPartyName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadSoldToPartyCodeController,
                                      hintText: 'Sold to Party Code',
                                      label: 'Sold to Party Code',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadSoldToPartyCode,
                                      isMandatory: false,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadSoldToPartyAddressController,
                                      hintText: 'Sold to Party Address',
                                      label: 'Sold to Party Address',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadSoldToPartyAddress,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadSoldToPartyStateController,
                                      hintText: 'Sold to Party State',
                                      label: 'Sold to Party State',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadSoldToPartyState,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadSoldToPartyDistrictsController,
                                      hintText: 'Sold to Party Districts',
                                      label: 'Sold to Party Districts',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadSoldToPartyDistricts,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Ship to Party Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller: leadShipToPartyNameController,
                                      hintText: 'Ship to Party Name',
                                      label: 'Ship to Party Name',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadShipToPartyName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadShipToPartyCodeController,
                                      hintText: 'Ship to Party Code',
                                      label: 'Ship to Party Code',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadShipToPartyCode,
                                      isMandatory: false,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadShipToPartyAddressController,
                                      hintText: 'Ship to Party Address',
                                      label: 'Ship to Party Address',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadShipToPartyAddress,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadShipToPartyStateController,
                                      hintText: 'Ship to Party State',
                                      label: 'Ship to Party State',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadShipToPartyState,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadShipToPartyDistrictsController,
                                      hintText: 'Ship to Party Districts',
                                      label: 'Ship to Party Districts',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadShipToPartyDistricts,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Source, Segment, Packaging
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Segment',
                                        onPressed: () {},
                                        value: leadSegment,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Lead Source',
                                        onPressed: () {},
                                        value: leadLeadSource,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Product + Packaging',
                                        onPressed: () {},
                                        value: leadProductPackaging,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: false),
                                    SizedBox(height: 7),
                                  ]),
                              // Quantity Required and Price
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          leadTotalQtyRequiredController,
                                      hintText: 'Total Potential of Site (MT)',
                                      label: 'Total Qty Required (MT)',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadTotalQtyRequired,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadQuotationQuantityController,
                                      hintText: 'Quotation Quantity (MT)',
                                      label: 'Monthly Qty Required (MT)',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadQuotationQuantity,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadCurrentBrandUserController,
                                      hintText: 'Current Brand Used',
                                      label: 'Current Brand Used',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadCurrentBrandUser,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadExpectedRatePerBagController,
                                      hintText: 'Expected Rate per Bag',
                                      label: 'Expected Rate per Bag',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadExpectedRatePerBag,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadCurrentPriceStarPerBagController,
                                      hintText:
                                          'Current Price Star Rs. per Bag',
                                      label: 'Current Price Star',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadCurrentPriceStarPerBag,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          leadCurrentPriceCompetitorPerBagController,
                                      hintText:
                                          'Current Price Competitor Rs. per Bag',
                                      label: 'Current Price Competitor',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue:
                                          leadCurrentPriceCompetitorPerBag,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Contact Person Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          leadContactPersonNameController,
                                      hintText: 'Contact Person Name',
                                      label: 'Contact Person Name',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadContactPersonName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadDesignationController,
                                      hintText: 'Designation',
                                      label: 'Designation',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadDesignation,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadContactNumberController,
                                      hintText: 'Contact Number',
                                      label: 'Contact Number',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadContactNumber,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: leadMailIdController,
                                      hintText: 'Mail Id',
                                      label: 'Mail Id',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadMailId,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                  ]),
                              // Payment Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Mode of Payment',
                                        onPressed: () {},
                                        value: leadModeOfPayment,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: false),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Credit Terms',
                                        onPressed: () {},
                                        value: leadCreditTerms,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: false),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel:
                                            'AAC Block is Required or Not',
                                        onPressed: () {},
                                        value: leadAacBlock,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: false),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel:
                                            'Category Type of Construction',
                                        onPressed: () {},
                                        value: leadCategoryTypeOfConstruction,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Lead Status',
                                        onPressed: () {},
                                        value: leadLeadStatus,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Next Visit Date',
                                        onPressed: () {},
                                        value: leadNextVisitDate,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Requirement Type',
                                        onPressed: () {},
                                        value: leadRequirementType,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    if (existingRequirementType!
                                            .toLowerCase() ==
                                        'exw') ...[
                                      SelectButtonWithLabel(
                                          buttonLabel: 'Ex. Works',
                                          onPressed: () {},
                                          value: leadExWorks,
                                          isEnabled: false,
                                          errorMessage:
                                              "You can't able to update.",
                                          isMandatory: true),
                                      SizedBox(height: 7),
                                    ],
                                    if (existingRequirementType!
                                            .toLowerCase() ==
                                        'fos') ...[
                                      LabeledTextField(
                                        controller: leadFosSidingController,
                                        hintText: 'FOS Siding',
                                        label: 'FOS Siding',
                                        maxLength: 100,
                                        keyboardType: TextInputType.name,
                                        isEditable: false,
                                        initialValue: leadFosSiding,
                                      ),
                                      SizedBox(height: 7),
                                    ],
                                  ]),
                              // Remarks & Assigned Person Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    LabeledTextField(
                                      controller:
                                          leadSaleOfficerRemarksController,
                                      hintText: 'Sales Officer Remarks',
                                      label: 'Sales Officer Remarks',
                                      keyboardType: TextInputType.name,
                                      isEditable: false,
                                      initialValue: leadSaleOfficerRemarks,
                                      isMandatory: false,
                                    ),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Assigned To',
                                        onPressed: () {},
                                        value: leadAssignedTo,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Requirement Timing',
                                        onPressed: () {},
                                        value: leadRequirementTiming,
                                        isEnabled: false,
                                        errorMessage:
                                            "You can't able to update.",
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    if (_selectedLeadStatusRadio != 3 &&
                                        (leadAction?.toUpperCase() == 'PENDING' ||
                                            leadAction?.toUpperCase() ==
                                                'HOLD' ||
                                            leadAction?.toUpperCase() ==
                                                'REVISION')) ...[
                                      SelectButtonWithLabel(
                                          buttonLabel: 'Lead Action',
                                          onPressed: () {
                                            setState(() {
                                              _selectedOption = 0;
                                            });
                                            _showPopup();
                                          },
                                          value: showVisitType,
                                          isMandatory: true),
                                      SizedBox(height: 7),
                                    ]
                                  ])
                            ]
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
              )),
              bottomNavigationBar: _isSubmitButtonShow
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
                            if (_empCategory == 1) {
                              _checkUpdateLeadInformation();
                            } else if (_empCategory == 2) {
                              _checkUpdateHosLeadInformation();
                            }
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

class LeadListData {
  // ignore: non_constant_identifier_names
  String? lead_generation_id;
  // ignore: non_constant_identifier_names
  String? emp_details_emp_name;
  // ignore: non_constant_identifier_names
  String? download_time_date_stamp;
  // ignore: non_constant_identifier_names
  String? download_time_time_stamp;
  String? latitude;
  String? longitude;

  // ignore: non_constant_identifier_names
  String? sold_to_party_details_name;
  // ignore: non_constant_identifier_names
  String? sold_to_party_code;
  // ignore: non_constant_identifier_names
  String? sold_to_party_details_address;
  // ignore: non_constant_identifier_names
  String? sold_to_party_details_state;
  // ignore: non_constant_identifier_names
  String? sold_to_party_details_districts;

  // ignore: non_constant_identifier_names
  String? ship_to_party_details_name;
  // ignore: non_constant_identifier_names
  String? ship_to_party;
  // ignore: non_constant_identifier_names
  String? ship_to_party_details_address;
  // ignore: non_constant_identifier_names
  String? ship_to_party_details_state;
  // ignore: non_constant_identifier_names
  String? ship_to_party_details_districts;

  // ignore: non_constant_identifier_names
  String? type_lead; //Segment
  // ignore: non_constant_identifier_names
  String? lead_type; //Lead Source
  // ignore: non_constant_identifier_names
  String? product_packaging; //Product & Packaging

  // ignore: non_constant_identifier_names
  String? qty_req;
  // ignore: non_constant_identifier_names
  String? month_qty;
  // ignore: non_constant_identifier_names
  String? current_brand_used;
  // ignore: non_constant_identifier_names
  String? exp_rate_per_bag;
  // ignore: non_constant_identifier_names
  String? current_price;
  // ignore: non_constant_identifier_names
  String? current_price_competitor;

  // ignore: non_constant_identifier_names
  String? contact_person_name;
  String? designation;
  // ignore: non_constant_identifier_names
  String? contact_number;
  // ignore: non_constant_identifier_names
  String? mail_id;

  String? mode; //Mode of Payment
  // ignore: non_constant_identifier_names
  String? credit_terms; //Credit Terms
  // ignore: non_constant_identifier_names
  String? acc_block_is_required; //AAC Block is Required or Not
  // ignore: non_constant_identifier_names
  String? category_type_construction; //Category type of Construction
  // ignore: non_constant_identifier_names
  String? lead_status; //Lead Status
  // ignore: non_constant_identifier_names
  String? next_visit_date; //Next Visit Date
  String? incoterms; //Requirement Type
  // ignore: non_constant_identifier_names
  String? serving_location;

  // ignore: non_constant_identifier_names
  String? lead_remarks; // Remarks

  // ignore: non_constant_identifier_names
  String? assigned_to;
  // ignore: non_constant_identifier_names
  String? assigned_to_details_emp_name; //Assigned To Name
  // ignore: non_constant_identifier_names
  String? r_timing; //Requirement Timing
  // ignore: non_constant_identifier_names
  String? lead_action; //lead action

  LeadListData({
    // ignore: non_constant_identifier_names
    this.lead_generation_id,
    // ignore: non_constant_identifier_names
    this.emp_details_emp_name,
    // ignore: non_constant_identifier_names
    this.download_time_date_stamp,
    // ignore: non_constant_identifier_names
    this.download_time_time_stamp,
    this.latitude,
    this.longitude,
    // ignore: non_constant_identifier_names
    this.sold_to_party_details_name,
    // ignore: non_constant_identifier_names
    this.sold_to_party_code,
    // ignore: non_constant_identifier_names
    this.sold_to_party_details_address,
    // ignore: non_constant_identifier_names
    this.sold_to_party_details_state,
    // ignore: non_constant_identifier_names
    this.sold_to_party_details_districts,
    // ignore: non_constant_identifier_names
    this.ship_to_party_details_name,
    // ignore: non_constant_identifier_names
    this.ship_to_party,
    // ignore: non_constant_identifier_names
    this.ship_to_party_details_address,
    // ignore: non_constant_identifier_names
    this.ship_to_party_details_state,
    // ignore: non_constant_identifier_names
    this.ship_to_party_details_districts,
    // ignore: non_constant_identifier_names
    this.type_lead,
    // ignore: non_constant_identifier_names
    this.lead_type,
    // ignore: non_constant_identifier_names
    this.product_packaging,
    // ignore: non_constant_identifier_names
    this.qty_req,
    // ignore: non_constant_identifier_names
    this.month_qty,
    // ignore: non_constant_identifier_names
    this.current_brand_used,
    // ignore: non_constant_identifier_names
    this.exp_rate_per_bag,
    // ignore: non_constant_identifier_names
    this.current_price,
    // ignore: non_constant_identifier_names
    this.current_price_competitor,
    // ignore: non_constant_identifier_names
    this.contact_person_name,
    this.designation,
    // ignore: non_constant_identifier_names
    this.contact_number,
    // ignore: non_constant_identifier_names
    this.mail_id,
    this.mode,
    // ignore: non_constant_identifier_names
    this.credit_terms,
    // ignore: non_constant_identifier_names
    this.acc_block_is_required,
    // ignore: non_constant_identifier_names
    this.category_type_construction,
    // ignore: non_constant_identifier_names
    this.lead_status,
    // ignore: non_constant_identifier_names
    this.next_visit_date,
    this.incoterms,
    // ignore: non_constant_identifier_names
    this.serving_location,
    // ignore: non_constant_identifier_names
    this.lead_remarks,
    // ignore: non_constant_identifier_names
    this.assigned_to,
    // ignore: non_constant_identifier_names
    this.assigned_to_details_emp_name,
    // ignore: non_constant_identifier_names
    this.r_timing,
    // ignore: non_constant_identifier_names
    this.lead_action,
  });

  factory LeadListData.fromJson(Map<String, dynamic> json) {
    final downloadTime = json['download_time']?.toString();
    String? datePart;
    String? timePart;
    if (downloadTime != null && downloadTime.contains('T')) {
      final parts = downloadTime.split('T');
      datePart = parts.isNotEmpty ? parts[0] : null;
      timePart = parts.length > 1 ? parts[1] : null;
    }
    return LeadListData(
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

  static Future<List<LeadListData>> getCustomerById(
      String empCode, String typeOfUser) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }

    String url = '${AppWebService.sbDevUrl}api/leadmaster/';
    if (typeOfUser == 'hos') {
      url = '$url?assigned_to=$empCode';
    } else {
      url = '$url?emp_code=$empCode';
    }
    final response = await http.get(Uri.parse(url));
    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse is List) {
        return jsonResponse.map((item) => LeadListData.fromJson(item)).toList();
      } else if (jsonResponse is Map<String, dynamic>) {
        // API sometimes returns a single object
        return [LeadListData.fromJson(jsonResponse)];
      } else {
        return [];
      }
    } else {
      throw Exception('Failed to load customer list');
    }
  }
}

class EmployeeNameList {
  String? empCode;
  String? empId;
  String? empName;
  String? email;
  String? phone;
  String? location;

  EmployeeNameList({
    this.empCode,
    this.empId,
    this.empName,
    this.email,
    this.phone,
    this.location,
  });

  factory EmployeeNameList.fromLine(String line) {
    final parts = line.split('^');
    return EmployeeNameList(
      empCode: parts.isNotEmpty ? parts[0] : null,
      empId: parts.length > 1 ? parts[1] : null,
      empName: parts.length > 2 ? parts[2] : null,
      email: parts.length > 10 ? parts[10] : null,
      phone: parts.length > 11 ? parts[11] : null,
      location: parts.length > 12 ? parts[12] : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'emp_code': empCode,
      'emp_id': empId,
      'emp_name': empName,
      'email': email,
      'phone': phone,
      'location': location,
    };
  }

  static Future<List<EmployeeNameList>> fetchDataFromApi(String empLvl) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "ntquotation.myvtd.site"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.sbDevUrl}api/employee_list/?level_type=$empLvl"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && line.contains("^"))
          .map((line) => EmployeeNameList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

class SoldToPartyNameList {
  String? customerCode;
  String? customerName;
  String? customerContactNumber;
  String? customerDistrict;
  String? customerState;
  String? customerAddress;

  SoldToPartyNameList({
    this.customerCode,
    this.customerName,
    this.customerContactNumber,
    this.customerDistrict,
    this.customerState,
    this.customerAddress,
  });

  factory SoldToPartyNameList.fromLine(String line) {
    final parts = line.split('^');
    return SoldToPartyNameList(
      customerCode: parts.length > 1 ? parts[1] : null,
      customerName: parts.length > 4 ? parts[4] : null,
      customerContactNumber: parts.length > 20 ? parts[20] : null,
      customerDistrict: parts.length > 14 ? parts[14] : null,
      customerState: parts.length > 11 ? parts[11] : null,
      customerAddress: parts.length > 9 ? parts[9] : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'customer_code': customerCode,
      'customer_name': customerName,
      'customer_contact_number': customerContactNumber,
      'customer_district': customerDistrict,
      'customer_state': customerState,
      'customer_address': customerAddress,
    };
  }

  static Future<List<SoldToPartyNameList>> fetchDataFromApi(
      String newLeadOtherSalesOfficerCode) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "ntquotation.myvtd.site"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.sbDevUrl}api/ptblcustomermasterlist/?emp_code=$newLeadOtherSalesOfficerCode&customer_type=sold"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && line.contains("^"))
          .map((line) => SoldToPartyNameList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

class ShipToPartyNameList {
  String? customerCode;
  String? customerName;
  String? customerContactNumber;
  String? customerDistrict;
  String? customerState;
  String? customerAddress;

  ShipToPartyNameList({
    this.customerCode,
    this.customerName,
    this.customerContactNumber,
    this.customerDistrict,
    this.customerState,
    this.customerAddress,
  });

  factory ShipToPartyNameList.fromLine(String line) {
    final parts = line.split('^');
    return ShipToPartyNameList(
      customerCode: parts.length > 1 ? parts[1] : null,
      customerName: parts.length > 4 ? parts[4] : null,
      customerContactNumber: parts.length > 20 ? parts[20] : null,
      customerDistrict: parts.length > 14 ? parts[14] : null,
      customerState: parts.length > 11 ? parts[11] : null,
      customerAddress: parts.length > 9 ? parts[9] : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'customer_code': customerCode,
      'customer_name': customerName,
      'customer_contact_number': customerContactNumber,
      'customer_district': customerDistrict,
      'customer_state': customerState,
      'customer_address': customerAddress,
    };
  }

  static Future<List<ShipToPartyNameList>> fetchDataFromApi(
      String newLeadOtherSalesOfficerCode) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "ntquotation.myvtd.site"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.sbDevUrl}api/ptblcustomermasterlist/?emp_code=$newLeadOtherSalesOfficerCode&customer_type=ship"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && line.contains("^"))
          .map((line) => ShipToPartyNameList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

class StateNameList {
  String? stateCode;
  String? stateName;

  StateNameList({
    this.stateCode,
    this.stateName,
  });

  factory StateNameList.fromLine(String line) {
    return StateNameList(
      stateCode: line,
      stateName: line,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'state_code': stateCode,
      'state_name': stateName,
    };
  }

  static Future<List<StateNameList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "ntquotation.myvtd.site"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse("${AppWebService.sbDevUrl}api/lead_state_list/"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => StateNameList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

class DistrictsNameList {
  String? districtsCode;
  String? districtsName;

  DistrictsNameList({
    this.districtsCode,
    this.districtsName,
  });

  factory DistrictsNameList.fromLine(String line) {
    final parts = line.split('^');
    return DistrictsNameList(
      districtsCode: parts.isNotEmpty ? parts[0] : null,
      districtsName: parts.isNotEmpty ? parts[0] : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'districts_code': districtsCode,
      'districts_name': districtsName,
    };
  }

  static Future<List<DistrictsNameList>> fetchDataFromApi(
      String stateName) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "ntquotation.myvtd.site"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse("${AppWebService.sbDevUrl}api/lead_district_list/"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) =>
              line.trim().isNotEmpty &&
              line.contains("^") &&
              line.contains(stateName))
          .map((line) => DistrictsNameList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

class SegmentList {
  final String label;
  final String value;

  SegmentList({required this.label, required this.value});

  factory SegmentList.fromJson(Map<String, dynamic> json) {
    return SegmentList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<SegmentList>> fetchDataFromStatic() async {
    return [
      SegmentList(label: 'KEY', value: 'Key'),
      SegmentList(label: 'NON-KEY', value: 'Non-Key'),
      SegmentList(label: 'TENDER', value: 'Tender'),
      SegmentList(label: 'ROE', value: 'ROE'),
    ];
  }
}

class LeadSourceList {
  final String label;
  final String value;

  LeadSourceList({required this.label, required this.value});

  factory LeadSourceList.fromJson(Map<String, dynamic> json) {
    return LeadSourceList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<LeadSourceList>> fetchDataFromStatic() async {
    return [
      LeadSourceList(
          label: 'ONLINE (WHATSAPP, E-MAIL, CALL)',
          value: 'ONLINE (WHATSAPP, E-MAIL, CALL)'),
      LeadSourceList(
          label: 'OFFLINE (VISIT TO CLIENT SITE/OFFICE)',
          value: 'OFFLINE (VISIT TO CLIENT SITE/OFFICE)'),
      LeadSourceList(
          label: "OFFLINE (CLIENT'S VISIT TO STAR OFFICE)",
          value: "OFFLINE (CLIENT'S VISIT TO STAR OFFICE)"),
      LeadSourceList(
          label: 'ONLINE (TENDERS ETC.)', value: 'ONLINE (TENDERS ETC.)'),
      LeadSourceList(
          label: 'PROJECT INFORMATION FROM OTHER SOURCES',
          value: 'PROJECT INFORMATION FROM OTHER SOURCES'),
    ];
  }
}

class ProductNameList {
  String? productName;
  String? productCode;

  ProductNameList({
    this.productName,
    this.productCode,
  });

  factory ProductNameList.fromLine(String line) {
    return ProductNameList(
      productName: line,
      productCode: line,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'product_name': productName,
      'product_code': productCode,
    };
  }

  static Future<List<ProductNameList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "ntquotation.myvtd.site"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse("${AppWebService.sbDevUrl}api/lead_product_list/"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => ProductNameList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

class ModeOfPaymentList {
  final String label;
  final String value;

  ModeOfPaymentList({required this.label, required this.value});

  factory ModeOfPaymentList.fromJson(Map<String, dynamic> json) {
    return ModeOfPaymentList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<ModeOfPaymentList>> fetchDataFromStatic() async {
    return [
      ModeOfPaymentList(label: 'ADVANCE', value: 'ADVANCE'),
      ModeOfPaymentList(label: 'BANK GUARANTEE', value: 'BANK GUARANTEE'),
      ModeOfPaymentList(label: 'CLEAN CREDIT', value: 'CLEAN CREDIT'),
      ModeOfPaymentList(label: 'SECURITY CHEQUE', value: 'SECURITY CHEQUE'),
      ModeOfPaymentList(label: 'POST DATED CHEQUE', value: 'POST DATED CHEQUE'),
      ModeOfPaymentList(label: 'OTHERS', value: 'OTHERS'),
    ];
  }
}

class CreditTermsList {
  final String label;
  final String value;

  CreditTermsList({required this.label, required this.value});

  factory CreditTermsList.fromJson(Map<String, dynamic> json) {
    return CreditTermsList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<CreditTermsList>> fetchDataFromStatic() async {
    return [
      CreditTermsList(label: '7 DAYS', value: '7 DAYS'),
      CreditTermsList(label: '15 DAYS', value: '15 DAYS'),
      CreditTermsList(label: '30 DAYS', value: '30 DAYS'),
      CreditTermsList(label: '45 DAYS', value: '45 DAYS'),
      CreditTermsList(label: '60 DAYS', value: '60 DAYS'),
      CreditTermsList(label: '75 DAYS', value: '75 DAYS'),
      CreditTermsList(label: '90 DAYS', value: '90 DAYS'),
    ];
  }
}

class AacBlockRequiredCheckList {
  final String label;
  final String value;

  AacBlockRequiredCheckList({required this.label, required this.value});

  factory AacBlockRequiredCheckList.fromJson(Map<String, dynamic> json) {
    return AacBlockRequiredCheckList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<AacBlockRequiredCheckList>> fetchDataFromStatic() async {
    return [
      AacBlockRequiredCheckList(label: 'YES', value: 'YES'),
      AacBlockRequiredCheckList(label: 'NO', value: 'NO'),
    ];
  }
}

class ConstructionTypeList {
  final String label;
  final String value;

  ConstructionTypeList({required this.label, required this.value});

  factory ConstructionTypeList.fromJson(Map<String, dynamic> json) {
    return ConstructionTypeList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<ConstructionTypeList>> fetchDataFromStatic() async {
    return [
      ConstructionTypeList(
          label: 'BUILDING PROJECTS - PUBLIC OR PRIVATE',
          value: 'BUILDING PROJECTS - PUBLIC OR PRIVATE'),
      ConstructionTypeList(
          label: 'INDUSTRIAL - MANUFACTURING',
          value: 'INDUSTRIAL - MANUFACTURING'),
      ConstructionTypeList(
          label: 'INDUSTRIAL - WAREHOUSING', value: 'INDUSTRIAL - WAREHOUSING'),
      ConstructionTypeList(
          label: 'ROADS, BRIDGES AND HIGHWAYS',
          value: 'ROADS, BRIDGES AND HIGHWAYS'),
      ConstructionTypeList(
          label: 'WATER SUPPLY & DISTRIBUTION',
          value: 'WATER SUPPLY & DISTRIBUTION'),
      ConstructionTypeList(label: 'POWER PROJECTS', value: 'POWER PROJECTS'),
      ConstructionTypeList(
          label: 'RAILWAY PROJECT OR SLEEPER MANUFACTURING',
          value: 'RAILWAY PROJECT OR SLEEPER MANUFACTURING'),
      ConstructionTypeList(
          label: 'PRE-CAST INDUSTRIES (PIPE AND POLES)',
          value: 'PRE-CAST INDUSTRIES (PIPE AND POLES)'),
      ConstructionTypeList(label: 'OTHER', value: 'OTHER'),
    ];
  }
}

class LeadStatusList {
  final String label;
  final String value;

  LeadStatusList({required this.label, required this.value});

  factory LeadStatusList.fromJson(Map<String, dynamic> json) {
    return LeadStatusList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<LeadStatusList>> fetchDataFromStatic() async {
    return [
      LeadStatusList(label: 'HOT', value: 'HOT'),
      LeadStatusList(label: 'WARM', value: 'WARM'),
      LeadStatusList(label: 'COLD', value: 'COLD'),
    ];
  }
}

class RequirementTypeList {
  final String label;
  final String value;

  RequirementTypeList({required this.label, required this.value});

  factory RequirementTypeList.fromJson(Map<String, dynamic> json) {
    return RequirementTypeList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<RequirementTypeList>> fetchDataFromStatic() async {
    return [
      RequirementTypeList(label: 'FOR', value: 'Free on Road (FOR)'),
      RequirementTypeList(label: 'EXW', value: 'Ex. Works (ExW)'),
      RequirementTypeList(label: 'FOS', value: 'Free on Siding (FOS)'),
    ];
  }
}

class ExWorkList {
  final String label;
  final String value;

  ExWorkList({required this.label, required this.value});

  factory ExWorkList.fromJson(Map<String, dynamic> json) {
    return ExWorkList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<ExWorkList>> fetchDataFromStatic() async {
    return [
      ExWorkList(label: "EX. LUMS PLANT", value: "EX. LUMS PLANT"),
      ExWorkList(label: "EX. GGU LINE-1/SCNEL", value: "EX. GGU LINE-1/SCNEL"),
      ExWorkList(label: "EX. SGU PLANT", value: "EX. SGU PLANT"),
      ExWorkList(label: "EX. BYRNIHAT DUMP", value: "EX. BYRNIHAT DUMP"),
      ExWorkList(label: "EX. JORABAT DUMP", value: "EX. JORABAT DUMP"),
      ExWorkList(label: "EX. FULERTAL DUMP", value: "EX. FULERTAL DUMP"),
      ExWorkList(label: "EX. SILIGURI-2 DUMP", value: "EX. SILIGURI-2 DUMP"),
      ExWorkList(label: "EX. VAIRENGTE DUMP", value: "EX. VAIRENGTE DUMP"),
      ExWorkList(label: "EX. AIZWAL DUMP", value: "EX. AIZWAL DUMP"),
      ExWorkList(label: "EX. SILCHAR DUMP", value: "EX. SILCHAR DUMP"),
    ];
  }
}

class RequirementTimingList {
  final String label;
  final String value;

  RequirementTimingList({required this.label, required this.value});

  factory RequirementTimingList.fromJson(Map<String, dynamic> json) {
    return RequirementTimingList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<RequirementTimingList>> fetchDataFromStatic() async {
    return [
      RequirementTimingList(label: '7 DAYS', value: '7 DAYS'),
      RequirementTimingList(label: '15 DAYS', value: '15 DAYS'),
      RequirementTimingList(label: '30 DAYS', value: '30 DAYS'),
      RequirementTimingList(label: '45 DAYS', value: '45 DAYS'),
      RequirementTimingList(label: '60 DAYS', value: '60 DAYS'),
      RequirementTimingList(label: '75 DAYS', value: '75 DAYS'),
      RequirementTimingList(label: '90 DAYS', value: '90 DAYS'),
    ];
  }
}

class LeadActionList {
  final String label;
  final String value;

  LeadActionList({required this.label, required this.value});

  factory LeadActionList.fromJson(Map<String, dynamic> json) {
    return LeadActionList(
      label: json['label'],
      value: json['value'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'label': label,
      'value': value,
    };
  }

  // Static list instead of API call
  static Future<List<LeadActionList>> fetchDataFromStatic() async {
    return [
      LeadActionList(label: 'YES', value: 'Yes, send quotation'),
      LeadActionList(label: 'NO', value: 'No, not required quotation'),
      LeadActionList(label: 'HOLD', value: 'Hold'),
      LeadActionList(label: 'REVISION', value: 'Send back for revision'),
    ];
  }
}
