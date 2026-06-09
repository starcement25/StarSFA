import 'dart:convert';
import 'dart:io';

import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class QueryGenerationActivityScreen extends StatefulWidget {
  final bool isOptionSelected;
  const QueryGenerationActivityScreen(
      {super.key, this.isOptionSelected = false});

  @override
  State<QueryGenerationActivityScreen> createState() =>
      _QueryGenerationActivityScreenState();
}

class _QueryGenerationActivityScreenState
    extends State<QueryGenerationActivityScreen> {
  // ignore: non_constant_identifier_names
  String emp_code = '';
  String _status = '';
  // ignore: non_constant_identifier_names
  String _lead_status = '';
  bool _isLoading = true;
  int _leadCategory = 0;
  int _leadStatus = 0;

  List<LeadListData>? leadListData = [];
  LeadListData selectedData = LeadListData();

  // New Lead Generation Details
  int _isSelectSoldToParty = 0;
  int _isSelectShipToParty = 0;
  int _forWhom = 0;

  String? newLeadUniqueId = '';
  String? newLeadReferredBy = '';
  String? newLeadOtherSalesOfficerCode = '';
  String? newLeadSalesOfficerName = '';
  String? newLeadDateStamp = '';
  String? newLeadTimeStamp = '';
  String? newLeadLatitude = '';
  String? newLeadLongitude = '';
  String? newLeadSoldToPartyName = '';
  String? newLeadSoldToPartyCode = '';
  String? newLeadSoldToPartyAddress = '';
  String? newLeadSoldToPartyState = '';
  String? newLeadSoldToPartyDistricts = '';
  String? newLeadShipToPartyName = '';
  String? newLeadShipToPartyCode = '';
  String? newLeadShipToPartyAddress = '';
  String? newLeadShipToPartyState = '';
  String? newLeadShipToPartyDistricts = '';
  String? newLeadSegment = '';
  String? newLeadLeadSource = '';
  String? newLeadProductPackaging = '';
  String? newLeadTotalPotentialOfSite = '';
  String? newLeadQuotationQuantity = '';
  String? newLeadCurrentBrandUsed = '';
  String? newLeadExpectedRatePerBag = '';
  String? newLeadCurrentPriceStarPerBag = '';
  String? newLeadCurrentPriceCompetitorPerBag = '';
  String? newLeadContactPersonName = '';
  String newLeadDesignation = '';
  String? newLeadContactNumber = '';
  String? newLeadMailId = '';
  String? newLeadModeOfPayment = '';
  String? newLeadCreditTerms = '';
  String? newLeadAacBlockRequiredOrNot = '';
  String? newLeadCategoryTypeOfConstruction = '';
  String? newLeadLeadStatus = '';
  String? newLeadNextVisitDate = '';
  String? newLeadRequirementType = '';
  String? newLeadExWorks = '';
  String? newLeadFosSiding = '';
  String? newLeadSalesOfficerRemarks = '';
  String? newLeadAssignedTo = '';
  String? newLeadAssignedToCode = '';
  String? newLeadRequirementTiming = '';

  TextEditingController newLeadUniqueIdController = TextEditingController();
  TextEditingController newLeadReferredByController = TextEditingController();
  TextEditingController newLeadSalesOfficerNameController =
      TextEditingController();
  TextEditingController newLeadDateStampController = TextEditingController();
  TextEditingController newLeadTimeStampController = TextEditingController();
  TextEditingController newLeadLatitudeController = TextEditingController();
  TextEditingController newLeadLongitudeController = TextEditingController();
  TextEditingController newLeadSoldToPartyNameController =
      TextEditingController();
  TextEditingController newLeadSoldToPartyCodeController =
      TextEditingController();
  TextEditingController newLeadSoldToPartyAddressController =
      TextEditingController();
  TextEditingController newLeadShipToPartyNameController =
      TextEditingController();
  TextEditingController newLeadShipToPartyCodeController =
      TextEditingController();
  TextEditingController newLeadShipToPartyAddressController =
      TextEditingController();
  TextEditingController newLeadTotalPotentialOfSiteController =
      TextEditingController();
  TextEditingController newLeadQuotationQuantityController =
      TextEditingController();
  TextEditingController newLeadCurrentBrandUsedController =
      TextEditingController();
  TextEditingController newLeadExpectedRatePerBagController =
      TextEditingController();
  TextEditingController newLeadCurrentPriceStarPerBagController =
      TextEditingController();
  TextEditingController newLeadCurrentPriceCompetitorPerBagController =
      TextEditingController();
  TextEditingController newLeadContactPersonNameController =
      TextEditingController();
  TextEditingController newLeadDesignationController = TextEditingController();
  TextEditingController newLeadContactNumberController =
      TextEditingController();
  TextEditingController newLeadMailIdController = TextEditingController();
  TextEditingController newLeadFosSidingController = TextEditingController();
  TextEditingController newLeadSalesOfficerRemarksController =
      TextEditingController();
  // New Lead Generation Details

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

  TextEditingController existingReferredByController = TextEditingController();
  TextEditingController existingSalesOfficerNameController =
      TextEditingController();
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

  @override
  void initState() {
    super.initState();
    _fetchDeclarationData();
    _basicInformation();
  }

  Future<void> _fetchDeclarationData() async {
    try {
      setState(() {
        _isLoading = true;
      });
      final userDetails = await UserLoginClass.getLocalUser();
      emp_code = userDetails!.empCode ?? '';
      final result = await LeadListData.getCustomerById(emp_code);
      // ignore: avoid_print
      print('Lead List count: ${result.length}');
      setState(() {
        leadListData = result; // <- updates UI after fetching
        _isLoading = false;
      });
    } catch (e) {
      // ignore: avoid_print
      print("Error fetching data: $e");
    }
  }

  void _basicInformation() async {
    try {
      Position position = await _getCurrentLocation();
      DateTime now = DateTime.now();

      final userDetails = await UserLoginClass.getLocalUser();
      String? empCode = userDetails!.empCode ?? '';
      empCode = empCode.toString().replaceAll(RegExp(r'^[A-Z]'), '');

      String formattedDate = DateFormat('yyyy-MM-dd').format(now);
      String formattedTime = DateFormat('HH:mm:ss').format(now);
      String idCode = DateFormat('yyMMddHHmm').format(now);

      String latitudeLocation = position.latitude.toString();
      String longitudeLocation = position.longitude.toString();

      setState(() {
        newLeadDateStamp = formattedDate;
        newLeadTimeStamp = formattedTime;
        newLeadLatitude = latitudeLocation;
        newLeadLongitude = longitudeLocation;
        newLeadUniqueId = 'L$empCode$idCode';
      });
    } catch (e) {
      // ignore: avoid_print
      print('Error: $e');
    }
  }

  Future<Position> _getCurrentLocation() async {
    bool serviceEnabled;
    LocationPermission permission;

    // Check if location services are enabled
    serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      throw Exception('Location services are disabled.');
    }

    // Check permission
    permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        throw Exception('Location permissions are denied');
      }
    }

    if (permission == LocationPermission.deniedForever) {
      throw Exception(
        'Location permissions are permanently denied, cannot request.',
      );
    }

    // Get current position
    return await Geolocator.getCurrentPosition(
      // ignore: deprecated_member_use
      desiredAccuracy: LocationAccuracy.high,
    );
  }

  Future<void> _requestForNewLeadGeneration() async {
    try {
      setState(() {
        _isLoading = true;
      });

      _requestForNewQueryGeneration('YES');

      String dateTime = newLeadDateStamp ?? '';
      dateTime = '$dateTime ${newLeadTimeStamp ?? ''}';
      String selfOther = _forWhom == 1 ? 'SELF' : 'OTHER';

      final Map<String, String> surveyLocation = {
        'emp_code': newLeadOtherSalesOfficerCode ?? '',
        'trans_id': newLeadUniqueId ?? '',
        'latt': newLeadLatitude ?? '',
        'longi': newLeadLongitude ?? '',
        'date': dateTime,
      };

      final Map<String, String> surveyHeader = {
        'survey_type': 'Lead Generation',
        'menu_name': 'RA514',
        'mall_id': '',
        'mall_hs_name': '',
        'business_name': '',
        'contact_name': '',
        'phone_no': '',
        'questions_answered': '',
        'route_code': '',
        'check_in_time': '',
        'survey_id': newLeadUniqueId ?? '',
      };

      final Map<String, String> surveyOutput = {
        'lead_generation_id': newLeadUniqueId ?? '',
        'self_other': selfOther,
        'emp_code': newLeadOtherSalesOfficerCode ?? '',
        'date': newLeadDateStamp ?? '',
        'time': newLeadTimeStamp ?? '',
        'latitude': newLeadLatitude ?? '',
        'longitude': newLeadLongitude ?? '',
        'sold_to_party': newLeadSoldToPartyCode ?? '',
        'ship_to_party': newLeadShipToPartyCode ?? '',
        'type_lead': newLeadSegment ?? '',
        'lead_type': newLeadLeadSource ?? '',
        'product_packaging': (newLeadProductPackaging ?? '').toUpperCase(),
        'qty_req': newLeadTotalPotentialOfSiteController.text,
        'month_qty': newLeadQuotationQuantityController.text,
        'current_brand_used': newLeadCurrentBrandUsedController.text,
        'exp_rate_per_bag': newLeadExpectedRatePerBagController.text,
        'current_price': newLeadCurrentPriceStarPerBagController.text,
        'current_price_competitor':
            newLeadCurrentPriceCompetitorPerBagController.text,
        'contact_person_name': newLeadContactPersonNameController.text,
        'designation': newLeadDesignationController.text,
        'contact_number': newLeadContactNumberController.text,
        'mail_id': newLeadMailIdController.text,
        'mode': newLeadModeOfPayment ?? '',
        'credit_terms': newLeadCreditTerms ?? '',
        'acc_block_is_required': newLeadAacBlockRequiredOrNot ?? '',
        'category_type_construction': newLeadCategoryTypeOfConstruction ?? '',
        'lead_status': newLeadLeadStatus ?? '',
        'next_visit_date': newLeadNextVisitDate ?? '',
        'incoterms': newLeadRequirementType ?? '',
        'lead_remarks': newLeadSalesOfficerRemarksController.text,
        'assigned_to': newLeadAssignedToCode ?? '',
        'r_timing': newLeadRequirementTiming ?? '',
        'payment': newLeadModeOfPayment ?? '',
        'party_name': newLeadSoldToPartyNameController.text,
        'branch': newLeadSoldToPartyAddressController.text,
        'district': (newLeadSoldToPartyDistricts ?? '').toUpperCase(),
        'state': (newLeadSoldToPartyState ?? '').toUpperCase(),
      };
      if ((newLeadRequirementType ?? '').toLowerCase() == 'fos') {
        surveyOutput['serving_location'] = newLeadFosSidingController.text;
      } else if ((newLeadRequirementType ?? '').toLowerCase() == 'exw') {
        surveyOutput['serving_location'] = newLeadExWorks ?? '';
      } else {
        surveyOutput['serving_location'] = '';
      }

      final Map<String, String> soldToParty = {
        'sold_to_party': newLeadSoldToPartyCode ?? '',
        'sold_to_party_name': newLeadSoldToPartyNameController.text,
        'sold_to_party_address': newLeadSoldToPartyAddressController.text,
        'sold_to_party_state': newLeadSoldToPartyState ?? '',
        'sold_to_party_districts': newLeadSoldToPartyDistricts ?? '',
      };

      final Map<String, String> shipToParty = {
        'ship_to_party': newLeadShipToPartyCode ?? '',
        'ship_to_party_name': newLeadShipToPartyNameController.text,
        'ship_to_party_address': newLeadShipToPartyAddressController.text,
        'ship_to_party_state': newLeadShipToPartyState ?? '',
        'ship_to_party_districts': newLeadShipToPartyDistricts ?? '',
      };

      final Map<String, String> quantityData = {
        'qty_req': newLeadTotalPotentialOfSiteController.text,
        'month_qty': newLeadQuotationQuantityController.text,
        'current_brand_used': newLeadCurrentBrandUsedController.text,
        'exp_rate_per_bag': newLeadExpectedRatePerBagController.text,
        'current_price': newLeadCurrentPriceStarPerBagController.text,
        'current_price_competitor':
            newLeadCurrentPriceCompetitorPerBagController.text,
      };

      final Map<String, String> contactPerson = {
        'contact_person_name': newLeadContactPersonNameController.text,
        'designation': newLeadDesignationController.text,
        'contact_number': newLeadContactNumberController.text,
        'mail_id': newLeadMailIdController.text,
      };

      final Map<String, String> otherInfo = {
        'mode': newLeadModeOfPayment ?? '',
        'credit_terms': newLeadCreditTerms ?? '',
        'acc_block_is_required': newLeadAacBlockRequiredOrNot ?? '',
        'category_type_construction': newLeadCategoryTypeOfConstruction ?? '',
        'lead_status': newLeadLeadStatus ?? '',
        'next_visit_date': newLeadNextVisitDate ?? '',
        'incoterms': newLeadRequirementType ?? '',
        'lead_remarks': newLeadSalesOfficerRemarksController.text,
        'assigned_to': newLeadAssignedToCode ?? '',
        'r_timing': newLeadRequirementTiming ?? '',
      };
      if ((newLeadRequirementType ?? '').toLowerCase() == 'fos') {
        otherInfo['serving_location'] = newLeadFosSidingController.text;
      } else if ((newLeadRequirementType ?? '').toLowerCase() == 'exw') {
        otherInfo['serving_location'] = newLeadExWorks ?? '';
      } else {
        otherInfo['serving_location'] = '';
      }

      final Map<String, dynamic> mainObject = {
        'survey_location': surveyLocation,
        'survey_header': surveyHeader,
        'survey_output': surveyOutput,
        'survey_sold_to_party': soldToParty,
        'survey_ship_to_party': shipToParty,
        'survey_quantity_data': quantityData,
        'survey_contact_person': contactPerson,
        'survey_other_info': otherInfo,
      };

      // ignore: avoid_print
      print("Lead Generation Send Data : ${jsonEncode(mainObject)}");

      final response = await http.post(
        Uri.parse('${AppWebService.sbDevUrl}api/leadmaster/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      // ignore: avoid_print
      print("Status Code: ${response.statusCode}");
      // ignore: avoid_print
      print("Response Body: ${response.body}");
      if (response.statusCode == 201) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _showSnackBar('Successfully New Lead Added.');
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

  Future<void> _requestForNewQueryGeneration(String leadAction) async {
    try {
      if (leadAction != 'YES') {
        setState(() {
          _isLoading = true;
        });
      }

      String dateTime = newLeadDateStamp ?? '';
      dateTime = '$dateTime ${newLeadTimeStamp ?? ''}';
      String selfOther = _forWhom == 1 ? 'SELF' : 'OTHER';

      final Map<String, String> surveyLocation = {
        'emp_code': newLeadOtherSalesOfficerCode ?? '',
        'trans_id': newLeadUniqueId ?? '',
        'latt': newLeadLatitude ?? '',
        'longi': newLeadLongitude ?? '',
        'date': dateTime,
      };

      final Map<String, String> surveyHeader = {
        'survey_type': 'Lead Generation',
        'menu_name': 'RA514',
        'mall_id': '',
        'mall_hs_name': '',
        'business_name': '',
        'contact_name': '',
        'phone_no': '',
        'questions_answered': '',
        'route_code': '',
        'check_in_time': '',
        'survey_id': newLeadUniqueId ?? '',
      };

      final Map<String, String> surveyOutput = {
        'lead_generation_id': newLeadUniqueId ?? '',
        'self_other': selfOther,
        'emp_code': newLeadOtherSalesOfficerCode ?? '',
        'date': newLeadDateStamp ?? '',
        'time': newLeadTimeStamp ?? '',
        'latitude': newLeadLatitude ?? '',
        'longitude': newLeadLongitude ?? '',
        'sold_to_party': newLeadSoldToPartyCode ?? '',
        'ship_to_party': newLeadShipToPartyCode ?? '',
        'lead_action': leadAction,
        'type_lead': newLeadSegment ?? '',
        'lead_type': newLeadLeadSource ?? '',
        'product_packaging': (newLeadProductPackaging ?? '').toUpperCase(),
        'qty_req': newLeadTotalPotentialOfSiteController.text,
        'month_qty': newLeadQuotationQuantityController.text,
        'current_brand_used': newLeadCurrentBrandUsedController.text,
        'exp_rate_per_bag': newLeadExpectedRatePerBagController.text,
        'current_price': newLeadCurrentPriceStarPerBagController.text,
        'current_price_competitor':
            newLeadCurrentPriceCompetitorPerBagController.text,
        'contact_person_name': newLeadContactPersonNameController.text,
        'designation': newLeadDesignationController.text,
        'contact_number': newLeadContactNumberController.text,
        'mail_id': newLeadMailIdController.text,
        'mode': newLeadModeOfPayment ?? '',
        'credit_terms': newLeadCreditTerms ?? '',
        'acc_block_is_required': newLeadAacBlockRequiredOrNot ?? '',
        'category_type_construction': newLeadCategoryTypeOfConstruction ?? '',
        'lead_status': newLeadLeadStatus?.split(" ")[0].toUpperCase() ?? '',
        'next_visit_date': newLeadNextVisitDate ?? '',
        'incoterms': newLeadRequirementType ?? '',
        'lead_remarks': newLeadSalesOfficerRemarksController.text,
        'assigned_to': newLeadAssignedToCode ?? '',
        'r_timing': newLeadRequirementTiming ?? '',
        'payment': newLeadModeOfPayment ?? '',
        'party_name': newLeadSoldToPartyNameController.text,
        'branch': newLeadSoldToPartyAddressController.text,
        'district': (newLeadSoldToPartyDistricts ?? '').toUpperCase(),
        'state': (newLeadSoldToPartyState ?? '').toUpperCase(),
        'referred_by': newLeadReferredByController.text,
      };
      if ((newLeadRequirementType ?? '').toLowerCase() == 'fos') {
        surveyOutput['serving_location'] = newLeadFosSidingController.text;
      } else if ((newLeadRequirementType ?? '').toLowerCase() == 'exw') {
        surveyOutput['serving_location'] = newLeadExWorks ?? '';
      } else {
        surveyOutput['serving_location'] = '';
      }

      final Map<String, String> soldToParty = {
        'sold_to_party': newLeadSoldToPartyCode ?? '',
        'sold_to_party_name': newLeadSoldToPartyNameController.text,
        'sold_to_party_address': newLeadSoldToPartyAddressController.text,
        'sold_to_party_state': newLeadSoldToPartyState ?? '',
        'sold_to_party_districts': newLeadSoldToPartyDistricts ?? '',
      };

      final Map<String, String> shipToParty = {
        'ship_to_party': newLeadShipToPartyCode ?? '',
        'ship_to_party_name': newLeadShipToPartyNameController.text,
        'ship_to_party_address': newLeadShipToPartyAddressController.text,
        'ship_to_party_state': newLeadShipToPartyState ?? '',
        'ship_to_party_districts': newLeadShipToPartyDistricts ?? '',
      };

      final Map<String, String> quantityData = {
        'qty_req': newLeadTotalPotentialOfSiteController.text,
        'month_qty': newLeadQuotationQuantityController.text,
        'current_brand_used': newLeadCurrentBrandUsedController.text,
        'exp_rate_per_bag': newLeadExpectedRatePerBagController.text,
        'current_price': newLeadCurrentPriceStarPerBagController.text,
        'current_price_competitor':
            newLeadCurrentPriceCompetitorPerBagController.text,
      };

      final Map<String, String> contactPerson = {
        'contact_person_name': newLeadContactPersonNameController.text,
        'designation': newLeadDesignationController.text,
        'contact_number': newLeadContactNumberController.text,
        'mail_id': newLeadMailIdController.text,
      };

      final Map<String, String> otherInfo = {
        'mode': newLeadModeOfPayment ?? '',
        'credit_terms': newLeadCreditTerms ?? '',
        'acc_block_is_required': newLeadAacBlockRequiredOrNot ?? '',
        'category_type_construction': newLeadCategoryTypeOfConstruction ?? '',
        'lead_status': newLeadLeadStatus ?? '',
        'next_visit_date': newLeadNextVisitDate ?? '',
        'incoterms': newLeadRequirementType ?? '',
        'lead_remarks': newLeadSalesOfficerRemarksController.text,
        'assigned_to': newLeadAssignedToCode ?? '',
        'r_timing': newLeadRequirementTiming ?? '',
      };
      if ((newLeadRequirementType ?? '').toLowerCase() == 'fos') {
        otherInfo['serving_location'] = newLeadFosSidingController.text;
      } else if ((newLeadRequirementType ?? '').toLowerCase() == 'exw') {
        otherInfo['serving_location'] = newLeadExWorks ?? '';
      } else {
        otherInfo['serving_location'] = '';
      }

      final Map<String, dynamic> mainObject = {
        'survey_location': surveyLocation,
        'survey_header': surveyHeader,
        'survey_output': surveyOutput,
        'survey_sold_to_party': soldToParty,
        'survey_ship_to_party': shipToParty,
        'survey_quantity_data': quantityData,
        'survey_contact_person': contactPerson,
        'survey_other_info': otherInfo,
      };

      // ignore: avoid_print
      print("Lead Generation Send Data : ${jsonEncode(mainObject)}");

      final response = await http.post(
        Uri.parse('${AppWebService.sbDevUrl}api/leadquerymaster/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      // ignore: avoid_print
      print("Status Code: ${response.statusCode}");
      // ignore: avoid_print
      print("Response Body: ${response.body}");
      if (response.statusCode == 201) {
        if (leadAction != 'YES') {
          setState(() {
            _isLoading = false;
          });
          jsonDecode(response.body);
          _showSnackBar('Successfully New Enquiry Added.');
          // ignore: use_build_context_synchronously
          Navigator.pop(context);
        }
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

  Future<void> _requestForUpdateQueryGeneration() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final Map<String, String> surveyOutput = {
        'lead_generation_id': existingLeadId ?? '',
        'sold_to_party': existingSoldToPartyCode ?? '',
        'ship_to_party': existingShipToPartyCode ?? '',
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
        'lead_status': existingLeadStatus?.split(" ")[0].toUpperCase() ?? '',
        'next_visit_date': existingNextVisitDate ?? '',
        'incoterms': existingRequirementType ?? '',
        'lead_remarks': existingSalesOfficerRemarksController.text,
        'assigned_to': existingAssignedToCode ?? '',
        'r_timing': existingRequirementTiming ?? '',
        'payment': existingModeOfPayment ?? ''
      };
      if ((existingRequirementType ?? '').toLowerCase() == 'fos') {
        surveyOutput['serving_location'] = existingFosSidingController.text;
      } else if ((existingRequirementType ?? '').toLowerCase() == 'exw') {
        surveyOutput['serving_location'] = existingExWorks ?? '';
      } else {
        surveyOutput['serving_location'] = '';
      }

      // ignore: avoid_print
      print("Lead Generation Send Data : ${jsonEncode(surveyOutput)}");

      final response = await http.post(
        Uri.parse(
            '${AppWebService.sbDevUrl}api/leadquerymaster/${existingLeadId!}/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(surveyOutput),
      );
      // ignore: avoid_print
      print("Status Code: ${response.statusCode}");
      // ignore: avoid_print
      print("Response Body: ${response.body}");
      if (response.statusCode == 201) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _showSnackBar('Successfully New Lead Added.');
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

  Future<void> _requestForUpdateLeadGeneration() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final Map<String, String> surveyOutput1 = {
        'lead_action': 'YES',
      };
      await http.post(
        Uri.parse(
            '${AppWebService.sbDevUrl}api/leadquerymaster/${existingLeadId!}/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(surveyOutput1),
      );

      final userDetails = await UserLoginClass.getLocalUser();
      String empCode = userDetails!.empCode ?? '';
      String selfOther = empCode == selectedData.emp_code ? 'SELF' : 'OTHER';

      String dateTime = existingDateStampController.text;
      dateTime = '$dateTime ${existingTimeStampController.text}';

      final Map<String, String> surveyLocation = {
        'emp_code': selectedData.emp_code ?? '',
        'trans_id': existingLeadId ?? '',
        'latt': existingLatitudeController.text,
        'longi': existingLongitudeController.text,
        'date': dateTime,
      };

      final Map<String, String> surveyHeader = {
        'survey_type': 'Lead Generation',
        'menu_name': 'RA514',
        'mall_id': '',
        'mall_hs_name': '',
        'business_name': '',
        'contact_name': '',
        'phone_no': '',
        'questions_answered': '',
        'route_code': '',
        'check_in_time': '',
        'survey_id': existingLeadId ?? '',
      };

      final Map<String, String> surveyOutput = {
        'lead_generation_id': existingLeadId ?? '',
        'self_other': selfOther,
        'emp_code': selectedData.emp_code ?? '',
        'date': existingDateStampController.text,
        'time': existingTimeStampController.text,
        'latitude': existingLatitudeController.text,
        'longitude': existingLongitudeController.text,
        'sold_to_party': selectedData.sold_to_party_code ?? '',
        'ship_to_party': selectedData.ship_to_party ?? '',
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
        'lead_status': existingLeadStatus?.split(" ")[0].toUpperCase() ?? '',
        'next_visit_date': existingNextVisitDate ?? '',
        'incoterms': existingRequirementType ?? '',
        'lead_remarks': existingSalesOfficerRemarksController.text,
        'assigned_to': existingAssignedToCode ?? '',
        'r_timing': existingRequirementTiming ?? '',
        'payment': existingModeOfPayment ?? '',
        'party_name': existingSoldToPartyNameController.text,
        'branch': existingSoldToPartyAddressController.text,
        'district': (existingSoldToPartyDistricts ?? '').toUpperCase(),
        'state': (existingSoldToPartyState ?? '').toUpperCase(),
        'referred_by': existingReferredByController.text,
      };
      if ((existingRequirementType ?? '').toLowerCase() == 'fos') {
        surveyOutput['serving_location'] = existingFosSidingController.text;
      } else if ((existingRequirementType ?? '').toLowerCase() == 'exw') {
        surveyOutput['serving_location'] = existingExWorks ?? '';
      } else {
        surveyOutput['serving_location'] = '';
      }

      final Map<String, String> soldToParty = {
        'sold_to_party': selectedData.sold_to_party_code ?? '',
        'sold_to_party_name': existingSoldToPartyNameController.text,
        'sold_to_party_address': existingSoldToPartyAddressController.text,
        'sold_to_party_state': existingSoldToPartyState ?? '',
        'sold_to_party_districts': existingSoldToPartyDistricts ?? '',
      };

      final Map<String, String> shipToParty = {
        'ship_to_party': selectedData.ship_to_party ?? '',
        'ship_to_party_name': existingShipToPartyNameController.text,
        'ship_to_party_address': existingShipToPartyAddressController.text,
        'ship_to_party_state': existingShipToPartyState ?? '',
        'ship_to_party_districts': existingShipToPartyDistricts ?? '',
      };

      final Map<String, String> quantityData = {
        'qty_req': existingTotalPotentialOfSiteController.text,
        'month_qty': existingQuotationQuantityController.text,
        'current_brand_used': existingCurrentBrandUsedController.text,
        'exp_rate_per_bag': existingExpectedRatePerBagController.text,
        'current_price': existingCurrentPriceStarPerBagController.text,
        'current_price_competitor':
            existingCurrentPriceCompetitorPerBagController.text,
      };

      final Map<String, String> contactPerson = {
        'contact_person_name': existingContactPersonNameController.text,
        'designation': existingDesignationController.text,
        'contact_number': existingContactNumberController.text,
        'mail_id': existingMailIdController.text,
      };

      final Map<String, String> otherInfo = {
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
      };
      if ((existingRequirementType ?? '').toLowerCase() == 'fos') {
        otherInfo['serving_location'] = existingFosSidingController.text;
      } else if ((existingRequirementType ?? '').toLowerCase() == 'exw') {
        otherInfo['serving_location'] = existingExWorks ?? '';
      } else {
        otherInfo['serving_location'] = '';
      }

      final Map<String, dynamic> mainObject = {
        'survey_location': surveyLocation,
        'survey_header': surveyHeader,
        'survey_output': surveyOutput,
        'survey_sold_to_party': soldToParty,
        'survey_ship_to_party': shipToParty,
        'survey_quantity_data': quantityData,
        'survey_contact_person': contactPerson,
        'survey_other_info': otherInfo,
      };

      // ignore: avoid_print
      print("Lead Generation Send Data : ${jsonEncode(mainObject)}");

      final response = await http.post(
        Uri.parse('${AppWebService.sbDevUrl}api/leadmaster/'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(mainObject),
      );
      // ignore: avoid_print
      print("Status Code: ${response.statusCode}");
      // ignore: avoid_print
      print("Response Body: ${response.body}");
      if (response.statusCode == 201) {
        setState(() {
          _isLoading = false;
        });
        jsonDecode(response.body);
        _showSnackBar('Successfully New Lead Added.');
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
    bool value = true;

    setState(() {
      selectedData = dataSet;
      isDataEditable = value;
      existingLeadId = dataSet.lead_generation_id;
      existingReferredBy = dataSet.customer_reference_no;

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
    existingReferredByController.text = dataSet.customer_reference_no ?? '';
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

  void _repeatLeadDataShowForSO(LeadListData dataSet) {
    bool value = true;

    setState(() {
      selectedData = dataSet;
      isDataEditable = value;
      existingLeadId = newLeadUniqueId;
      existingReferredBy = dataSet.customer_reference_no;

      existingSalesOfficerName = dataSet.emp_details_emp_name;
      existingDateStamp = newLeadDateStamp;
      existingTimeStamp = newLeadTimeStamp;
      existingLatitude = newLeadLatitude;
      existingLongitude = newLeadLongitude;

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
    existingReferredByController.text = dataSet.customer_reference_no ?? '';
    existingDateStampController.text = newLeadDateStamp ?? '';
    existingTimeStampController.text = newLeadTimeStamp ?? '';
    existingLatitudeController.text = newLeadLatitude ?? '';
    existingLongitudeController.text = newLeadLongitude ?? '';
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

  void _clearAllExistingData() {
    setState(() {
      isDataEditable = false;
      existingLeadId = '';

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

  void _selectSelfRadioButton(int value) async {
    final userDetails = await UserLoginClass.getLocalUser();
    setState(() {
      _forWhom = value;
      newLeadSalesOfficerName = userDetails!.empName ?? '';
      newLeadOtherSalesOfficerCode = emp_code;
    });
    newLeadSalesOfficerNameController.text = userDetails!.empName ?? '';
  }

  void _checkNewLeadInformation() {
    if (_forWhom == 0) {
      _showSnackBar('Please select sales officer.');
    } else if (newLeadOtherSalesOfficerCode == '') {
      _showSnackBar("Please select sales officer.");
    } else if (newLeadSoldToPartyNameController.text == '') {
      _showSnackBar("Please enter sold to party name.");
    } else if (newLeadSoldToPartyAddressController.text == '') {
      _showSnackBar("Please enter sold to party address.");
    } else if (newLeadSoldToPartyState == '') {
      _showSnackBar("Please enter sold to party state.");
    } else if (newLeadSoldToPartyDistricts == '') {
      _showSnackBar("Please enter sold to party districts.");
    } else if (newLeadSegment == '') {
      _showSnackBar("Please select segment.");
    } else if (newLeadLeadSource == '') {
      _showSnackBar("Please select lead source.");
    } else if (newLeadCurrentBrandUsedController.text == '') {
      _showSnackBar("Please enter current brand used.");
    } else if (newLeadExpectedRatePerBagController.text == '') {
      _showSnackBar("Please enter expected rate per bag.");
    } else if (newLeadCurrentPriceStarPerBagController.text == '') {
      _showSnackBar("Please enter current price star rs per bag.");
    } else if (newLeadCurrentPriceCompetitorPerBagController.text == '') {
      _showSnackBar("Please enter current price competitor rs per bag.");
    } else if (newLeadContactPersonNameController.text == '') {
      _showSnackBar("Please enter contact person name.");
    } else if (newLeadDesignationController.text == '') {
      _showSnackBar("Please enter contact person designation.");
    } else if (newLeadContactNumberController.text == '') {
      _showSnackBar("Please enter contact person phone number.");
    } else if (newLeadContactNumberController.text.length != 10) {
      _showSnackBar("Please enter contact person correct phone number.");
    } else if (newLeadMailIdController.text == '') {
      _showSnackBar("Please enter contact person mail id.");
    } else if (newLeadCategoryTypeOfConstruction == '') {
      _showSnackBar("Please select construction type.");
    } else if (newLeadLeadStatus == '') {
      _showSnackBar("Please select lead status.");
    } else if (newLeadNextVisitDate == '') {
      _showSnackBar("Please select next visit date.");
    } else if (newLeadRequirementType == '') {
      _showSnackBar("Please select requirement type.");
    } else if (newLeadAssignedTo == '') {
      _showSnackBar("Please select assigned to.");
    } else if (newLeadRequirementTiming == '') {
      _showSnackBar("Please select requirements timing.");
    } else {
      _requestForNewLeadGeneration();
    }
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

  void _showSnackBar(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: Colors.red,
      ),
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
      final leadAction = getDisplayText(item).lead_action?.toLowerCase() ?? '';
      final leadStatus = getDisplayText(item).lead_status?.toLowerCase() ?? '';

      // ignore: avoid_print
      print(leadAction);
      // ignore: avoid_print
      print(leadStatus);
      // ignore: avoid_print
      print(_lead_status);

      if (_status.toLowerCase() == 'existing') {
        if (leadAction != 'pending' ||
            _lead_status != leadStatus.toLowerCase()) {
          return false;
        }
      }
      if (_status.toLowerCase() == 'repeat' ||
          _lead_status != leadStatus.toLowerCase()) {
        if (leadAction != 'yes') {
          return false;
        }
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
                                  final leadStatus = getDisplayText(item)
                                          .lead_status
                                          ?.toLowerCase() ??
                                      '';

                                  if (_status.toLowerCase() == 'existing') {
                                    if (leadStatus != 'pending') {
                                      return false;
                                    }
                                  }
                                  if (_status.toLowerCase() == 'repeat') {
                                    if (leadStatus != 'yes') {
                                      return false;
                                    }
                                  }

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
                  'Enquiry Creation',
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
                          Row(
                            children: [
                              Expanded(
                                child: RadioListTile<int>(
                                  contentPadding: EdgeInsets.zero,
                                  title: const Text("New"),
                                  value: 1,
                                  // ignore: deprecated_member_use
                                  groupValue: _leadCategory,
                                  // ignore: deprecated_member_use
                                  onChanged: (value) {
                                    _clearAllExistingData();
                                    setState(() {
                                      _lead_status = '';
                                      _leadStatus = 0;
                                      _leadCategory = value!;
                                    });
                                  },
                                ),
                              ),
                              Expanded(
                                child: RadioListTile<int>(
                                  contentPadding: EdgeInsets.zero,
                                  title: const Text("Existing"),
                                  value: 2,
                                  // ignore: deprecated_member_use
                                  groupValue: _leadCategory,
                                  // ignore: deprecated_member_use
                                  onChanged: (value) {
                                    _clearAllExistingData();
                                    setState(() {
                                      _lead_status = '';
                                      _status = 'Existing';
                                      _leadStatus = 0;
                                      _leadCategory = value!;
                                      newLeadSalesOfficerName = '';
                                    });
                                    newLeadSalesOfficerNameController.text = '';
                                  },
                                ),
                              ),
                              Expanded(
                                child: RadioListTile<int>(
                                  contentPadding: EdgeInsets.zero,
                                  title: const Text("Repeat"),
                                  value: 3,
                                  // ignore: deprecated_member_use
                                  groupValue: _leadCategory,
                                  // ignore: deprecated_member_use
                                  onChanged: (value) {
                                    _clearAllExistingData();
                                    setState(() {
                                      _lead_status = '';
                                      _leadStatus = 0;
                                      _status = 'Repeat';
                                      _leadCategory = value!;
                                      newLeadSalesOfficerName = '';
                                    });
                                    newLeadSalesOfficerNameController.text = '';
                                  },
                                ),
                              ),
                            ],
                          ),
                          if (_leadCategory == 1) ...[
                            // Basic information
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                LabeledTextField(
                                  controller: newLeadUniqueIdController,
                                  hintText: 'Unique Enquiry Id',
                                  label: 'Unique Enquiry Id',
                                  keyboardType: TextInputType.name,
                                  initialValue: newLeadUniqueId,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                                LabeledTextField(
                                  controller: newLeadReferredByController,
                                  hintText: 'Referred By',
                                  label: 'Referred By',
                                  keyboardType: TextInputType.name,
                                  initialValue: newLeadReferredBy,
                                  isMandatory: false,
                                ),
                                SizedBox(height: 7),
                              ],
                            ),
                            // For Whom
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Self / Other',
                                  style: TextStyle(
                                    fontSize: 15,
                                    color: const Color.fromARGB(255, 0, 0, 0),
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                SizedBox(height: 1),
                                Row(
                                  children: [
                                    Expanded(
                                      child: RadioListTile<int>(
                                        contentPadding: EdgeInsets.zero,
                                        title: const Text("Self"),
                                        value: 1,
                                        // ignore: deprecated_member_use
                                        groupValue: _forWhom,
                                        // ignore: deprecated_member_use
                                        onChanged: (value) {
                                          _selectSelfRadioButton(value!);
                                        },
                                      ),
                                    ),
                                    Expanded(
                                      child: RadioListTile<int>(
                                        contentPadding: EdgeInsets.zero,
                                        title: const Text("Other"),
                                        value: 2,
                                        // ignore: deprecated_member_use
                                        groupValue: _forWhom,
                                        // ignore: deprecated_member_use
                                        onChanged: (value) {
                                          newLeadSalesOfficerNameController
                                              .clear();
                                          setState(() {
                                            _forWhom = value!;
                                          });
                                        },
                                      ),
                                    ),
                                  ],
                                ),
                                SizedBox(height: 7),
                                if (_forWhom == 2) ...[
                                  SelectButtonWithLabel(
                                    buttonLabel: 'Sales Officer Name',
                                    onPressed: () {
                                      showSelectorDialog<EmployeeNameList>(
                                        fetchData: () =>
                                            EmployeeNameList.fetchDataFromApi(
                                                'so'),
                                        dialogTitle:
                                            'Select Sales Officer Name',
                                        getDisplayText: (item) =>
                                            item.empName ?? '',
                                        enableSearch: true,
                                        onSelected: (item) {
                                          setState(() {
                                            newLeadSalesOfficerName =
                                                item.empName;
                                            newLeadOtherSalesOfficerCode =
                                                item.empCode;
                                          });
                                          newLeadSalesOfficerNameController
                                              .text = item.empName ?? '';
                                        },
                                      );
                                    },
                                    value: '',
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ],
                            ),
                            if (_forWhom != 0) ...[
                              LabeledTextField(
                                controller: newLeadSalesOfficerNameController,
                                hintText: 'Sales Officer Name',
                                label: 'Sales Officer Name',
                                isEditable: false,
                                keyboardType: TextInputType.name,
                                initialValue: newLeadSalesOfficerName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              LabeledTextField(
                                controller: newLeadDateStampController,
                                hintText: 'Date Stamp',
                                label: 'Date Stamp',
                                isEditable: false,
                                keyboardType: TextInputType.name,
                                initialValue: newLeadDateStamp,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              LabeledTextField(
                                controller: newLeadTimeStampController,
                                hintText: 'Time Stamp',
                                label: 'Time Stamp',
                                isEditable: false,
                                keyboardType: TextInputType.name,
                                initialValue: newLeadTimeStamp,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              LabeledTextField(
                                controller: newLeadLatitudeController,
                                hintText: 'Latitude',
                                label: 'Latitude',
                                isEditable: false,
                                keyboardType: TextInputType.name,
                                initialValue: newLeadLatitude,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              LabeledTextField(
                                controller: newLeadLongitudeController,
                                hintText: 'Longitude',
                                label: 'Longitude',
                                isEditable: false,
                                keyboardType: TextInputType.name,
                                initialValue: newLeadLongitude,
                                isMandatory: true,
                              ),
                            ],
                            if (newLeadSalesOfficerName != '') ...[
                              // Sold to Party Information
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                    buttonLabel: 'Sold to Party Name',
                                    onPressed: () {
                                      if (newLeadOtherSalesOfficerCode == '') {
                                        _showSnackBar(
                                            'Please select sales officer name first.');
                                      } else {
                                        showSelectorDialog<SoldToPartyNameList>(
                                          fetchData: () => SoldToPartyNameList
                                              .fetchDataFromApi(
                                                  newLeadOtherSalesOfficerCode!),
                                          dialogTitle:
                                              'Select Sold to Party Name',
                                          getDisplayText: (item) =>
                                              item.customerName ?? '',
                                          enableSearch: true,
                                          onSelected: (item) {
                                            setState(() {
                                              _isSelectSoldToParty = 1;
                                              newLeadSoldToPartyName =
                                                  item.customerName;
                                              newLeadSoldToPartyCode =
                                                  item.customerCode;
                                              newLeadSoldToPartyAddress =
                                                  item.customerAddress;
                                              newLeadSoldToPartyState =
                                                  item.customerState;
                                              newLeadSoldToPartyDistricts =
                                                  item.customerDistrict;
                                            });
                                            newLeadSoldToPartyNameController
                                                .text = item.customerName ?? '';
                                            newLeadSoldToPartyCodeController
                                                .text = item.customerCode ?? '';
                                            newLeadSoldToPartyAddressController
                                                    .text =
                                                item.customerAddress ?? '';
                                          },
                                        );
                                      }
                                    },
                                    value: '',
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        newLeadSoldToPartyNameController,
                                    hintText: 'Sold to Party Name',
                                    label: 'Sold to Party Name',
                                    maxLength: 50,
                                    keyboardType: TextInputType.name,
                                    initialValue: newLeadSoldToPartyName,
                                    isEditable: _isSelectSoldToParty == 0,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        newLeadSoldToPartyCodeController,
                                    hintText: 'Sold to Party Code',
                                    label: 'Sold to Party Code',
                                    keyboardType: TextInputType.name,
                                    isEditable: false,
                                    initialValue: newLeadSoldToPartyCode,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        newLeadSoldToPartyAddressController,
                                    hintText: 'Sold to Party Address',
                                    label: 'Sold to Party Address',
                                    maxLength: 100,
                                    keyboardType: TextInputType.name,
                                    isEditable: _isSelectSoldToParty == 0,
                                    initialValue: newLeadSoldToPartyAddress,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Sold to Party State',
                                      onPressed: () {
                                        if (_isSelectSoldToParty == 0) {
                                          showSelectorDialog<StateNameList>(
                                            fetchData: () => StateNameList
                                                .fetchDataFromApi(),
                                            dialogTitle:
                                                'Select Sold to Party State',
                                            getDisplayText: (item) =>
                                                item.stateName ?? '',
                                            enableSearch: true,
                                            onSelected: (item) {
                                              setState(() {
                                                newLeadSoldToPartyState =
                                                    item.stateCode;
                                              });
                                            },
                                          );
                                        } else {
                                          _showSnackBar(
                                              'Can not change sold to party state.');
                                        }
                                      },
                                      isEnabled: true,
                                      value: newLeadSoldToPartyState,
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Sold to Party Districts',
                                      onPressed: () {
                                        if (_isSelectSoldToParty == 0) {
                                          if (newLeadSoldToPartyState == '') {
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content: Text(
                                                    'Please select Sold to Party State first.'),
                                                backgroundColor: Colors.red,
                                              ),
                                            );
                                          } else {
                                            showSelectorDialog<
                                                DistrictsNameList>(
                                              fetchData: () => DistrictsNameList
                                                  .fetchDataFromApi(
                                                      newLeadSoldToPartyState!),
                                              dialogTitle:
                                                  'Select Sold to Party Districts',
                                              getDisplayText: (item) =>
                                                  item.districtsName ?? '',
                                              enableSearch: true,
                                              onSelected: (item) {
                                                setState(() {
                                                  newLeadSoldToPartyDistricts =
                                                      item.districtsName;
                                                });
                                              },
                                            );
                                          }
                                        } else {
                                          _showSnackBar(
                                              'Can not change sold to party districts.');
                                        }
                                      },
                                      isEnabled: true,
                                      value: newLeadSoldToPartyDistricts,
                                      isMandatory: true),
                                ],
                              ),
                              // Ship to Party Information
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                    buttonLabel: 'Ship to Party Name',
                                    onPressed: () {
                                      if (newLeadOtherSalesOfficerCode == '') {
                                        _showSnackBar(
                                            'Please select sales officer name first.');
                                      } else {
                                        showSelectorDialog<ShipToPartyNameList>(
                                          fetchData: () => ShipToPartyNameList
                                              .fetchDataFromApi(
                                                  newLeadOtherSalesOfficerCode!),
                                          dialogTitle:
                                              'Select Ship to Party Name',
                                          getDisplayText: (item) =>
                                              item.customerName ?? '',
                                          enableSearch: true,
                                          onSelected: (item) {
                                            setState(() {
                                              _isSelectShipToParty = 1;
                                              newLeadShipToPartyName =
                                                  item.customerName;
                                              newLeadShipToPartyCode =
                                                  item.customerCode;
                                              newLeadShipToPartyAddress =
                                                  item.customerAddress;
                                              newLeadShipToPartyState =
                                                  item.customerState;
                                              newLeadShipToPartyDistricts =
                                                  item.customerDistrict;
                                            });
                                            newLeadShipToPartyNameController
                                                .text = item.customerName ?? '';
                                            newLeadShipToPartyCodeController
                                                .text = item.customerCode ?? '';
                                            newLeadShipToPartyAddressController
                                                    .text =
                                                item.customerAddress ?? '';
                                          },
                                        );
                                      }
                                    },
                                    value: '',
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        newLeadShipToPartyNameController,
                                    hintText: 'Ship to Party Name',
                                    label: 'Ship to Party Name',
                                    maxLength: 50,
                                    isEditable: _isSelectShipToParty == 0,
                                    keyboardType: TextInputType.name,
                                    initialValue: newLeadShipToPartyName,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        newLeadShipToPartyCodeController,
                                    hintText: 'Ship to Party Code',
                                    label: 'Ship to Party Code',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: newLeadShipToPartyCode,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        newLeadShipToPartyAddressController,
                                    hintText: 'Ship to Party Address',
                                    label: 'Ship to Party Address',
                                    maxLength: 100,
                                    isEditable: _isSelectShipToParty == 0,
                                    keyboardType: TextInputType.name,
                                    initialValue: newLeadShipToPartyAddress,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Ship to Party State',
                                      onPressed: () {
                                        if (_isSelectShipToParty == 0) {
                                          showSelectorDialog<StateNameList>(
                                            fetchData: () => StateNameList
                                                .fetchDataFromApi(),
                                            dialogTitle:
                                                'Select Ship to Party State',
                                            getDisplayText: (item) =>
                                                item.stateName ?? '',
                                            enableSearch: true,
                                            onSelected: (item) {
                                              setState(() {
                                                newLeadShipToPartyState =
                                                    item.stateCode;
                                              });
                                            },
                                          );
                                        } else {
                                          _showSnackBar(
                                              'Can not change ship to party state.');
                                        }
                                      },
                                      value: newLeadShipToPartyState,
                                      isEnabled: true,
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Ship to Party Districts',
                                      onPressed: () {
                                        if (_isSelectShipToParty == 0) {
                                          if (newLeadShipToPartyState == '') {
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content: Text(
                                                    'Please select Ship to Party State first.'),
                                                backgroundColor: Colors.red,
                                              ),
                                            );
                                          } else {
                                            showSelectorDialog<
                                                DistrictsNameList>(
                                              fetchData: () => DistrictsNameList
                                                  .fetchDataFromApi(
                                                      newLeadShipToPartyState!),
                                              dialogTitle:
                                                  'Select Ship to Party Districts',
                                              getDisplayText: (item) =>
                                                  item.districtsName ?? '',
                                              enableSearch: true,
                                              onSelected: (item) {
                                                setState(() {
                                                  newLeadShipToPartyDistricts =
                                                      item.districtsName;
                                                });
                                              },
                                            );
                                          }
                                        } else {
                                          _showSnackBar(
                                              'Can not change ship to party districts.');
                                        }
                                      },
                                      isEnabled: true,
                                      value: newLeadShipToPartyDistricts,
                                      isMandatory: true),
                                ],
                              ),
                              // Source, Segment, Packaging
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    SizedBox(height: 7),
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
                                                newLeadSegment = item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: newLeadSegment,
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Enquiry Source',
                                        onPressed: () {
                                          showSelectorDialog<LeadSourceList>(
                                            fetchData: () => LeadSourceList
                                                .fetchDataFromStatic(),
                                            dialogTitle:
                                                'Select Enquiry Source',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                newLeadLeadSource = item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: newLeadLeadSource,
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
                                              newLeadProductPackaging =
                                                  item.productName;
                                            });
                                          },
                                        );
                                      },
                                      value: newLeadProductPackaging,
                                    ),
                                  ]),
                              // Quantity Required and Price
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          newLeadTotalPotentialOfSiteController,
                                      hintText: 'Total Qty Required (MT)',
                                      label: 'Total Potential of Site (MT)',
                                      maxLength: 5,
                                      keyboardType: TextInputType.number,
                                      initialValue: newLeadTotalPotentialOfSite,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          newLeadQuotationQuantityController,
                                      hintText: 'Monthly Qty Required (MT)',
                                      label: 'Quotation Quantity (MT)',
                                      maxLength: 5,
                                      keyboardType: TextInputType.number,
                                      initialValue: newLeadQuotationQuantity,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          newLeadCurrentBrandUsedController,
                                      hintText: 'Current Brand Used',
                                      label: 'Current Brand Used',
                                      maxLength: 25,
                                      keyboardType: TextInputType.name,
                                      initialValue: newLeadCurrentBrandUsed,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          newLeadExpectedRatePerBagController,
                                      hintText: 'Expected Rate Per Bag',
                                      label: 'Expected Rate Per Bag',
                                      maxLength: 4,
                                      keyboardType: TextInputType.number,
                                      initialValue: newLeadExpectedRatePerBag,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          newLeadCurrentPriceStarPerBagController,
                                      hintText: 'Current Price Star',
                                      label: 'Current Price Star Rs. Per Bag',
                                      maxLength: 4,
                                      keyboardType: TextInputType.number,
                                      initialValue:
                                          newLeadCurrentPriceStarPerBag,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          newLeadCurrentPriceCompetitorPerBagController,
                                      hintText: 'Current Price Competitor',
                                      label:
                                          'Current Price Competitor Rs. Per Bag',
                                      maxLength: 4,
                                      keyboardType: TextInputType.number,
                                      initialValue:
                                          newLeadCurrentPriceCompetitorPerBag,
                                      isMandatory: true,
                                    ),
                                  ]),
                              // Contact Person Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          newLeadContactPersonNameController,
                                      hintText: 'Contact Person Name',
                                      label: 'Contact Person Name',
                                      maxLength: 25,
                                      keyboardType: TextInputType.name,
                                      initialValue: newLeadContactPersonName,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: newLeadDesignationController,
                                      hintText: 'Designation',
                                      label: 'Designation',
                                      maxLength: 25,
                                      keyboardType: TextInputType.name,
                                      initialValue: newLeadDesignation,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller:
                                          newLeadContactNumberController,
                                      hintText: 'Contact Number',
                                      label: 'Contact Number',
                                      maxLength: 10,
                                      keyboardType: TextInputType.number,
                                      initialValue: newLeadContactNumber,
                                      isMandatory: true,
                                    ),
                                    SizedBox(height: 7),
                                    LabeledTextField(
                                      controller: newLeadMailIdController,
                                      hintText: 'Mail Id',
                                      label: 'Mail Id',
                                      maxLength: 50,
                                      keyboardType: TextInputType.emailAddress,
                                      initialValue: newLeadMailId,
                                      isMandatory: true,
                                    ),
                                  ]),
                              // Payment Information
                              Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                      buttonLabel: 'Mode of Payment',
                                      onPressed: () {
                                        showSelectorDialog<ModeOfPaymentList>(
                                          fetchData: () => ModeOfPaymentList
                                              .fetchDataFromStatic(),
                                          dialogTitle: 'Select Mode of Payment',
                                          getDisplayText: (item) => item.label,
                                          enableSearch: false,
                                          onSelected: (item) {
                                            setState(() {
                                              newLeadModeOfPayment = item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: newLeadModeOfPayment,
                                    ),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                      buttonLabel: 'Credit Terms',
                                      onPressed: () {
                                        showSelectorDialog<CreditTermsList>(
                                          fetchData: () => CreditTermsList
                                              .fetchDataFromStatic(),
                                          dialogTitle: 'Select Credit Terms',
                                          getDisplayText: (item) => item.label,
                                          enableSearch: false,
                                          onSelected: (item) {
                                            setState(() {
                                              newLeadCreditTerms = item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: newLeadCreditTerms,
                                    ),
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
                                          getDisplayText: (item) => item.label,
                                          enableSearch: false,
                                          onSelected: (item) {
                                            setState(() {
                                              newLeadAacBlockRequiredOrNot =
                                                  item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: newLeadAacBlockRequiredOrNot,
                                    ),
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
                                                newLeadCategoryTypeOfConstruction =
                                                    item.label;
                                              });
                                            },
                                          );
                                        },
                                        value:
                                            newLeadCategoryTypeOfConstruction,
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    SelectButtonWithLabel(
                                        buttonLabel: 'Enquiry Status',
                                        onPressed: () {
                                          showSelectorDialog<LeadStatusList>(
                                            fetchData: () => LeadStatusList
                                                .fetchDataFromStatic(),
                                            dialogTitle:
                                                'Select Enquiry Status',
                                            getDisplayText: (item) =>
                                                item.label,
                                            enableSearch: false,
                                            onSelected: (item) {
                                              setState(() {
                                                newLeadLeadStatus = item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: newLeadLeadStatus,
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
                                              newLeadNextVisitDate =
                                                  "${pickedDate.day}-${pickedDate.month}-${pickedDate.year}";
                                            });
                                          }
                                        },
                                        value: newLeadNextVisitDate,
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
                                                newLeadRequirementType =
                                                    item.label;
                                              });
                                            },
                                          );
                                        },
                                        value: newLeadRequirementType,
                                        isMandatory: true),
                                    SizedBox(height: 7),
                                    if (newLeadRequirementType!.toLowerCase() ==
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
                                                  newLeadExWorks = item.label;
                                                });
                                              },
                                            );
                                          },
                                          value: newLeadExWorks,
                                          isMandatory: true),
                                      SizedBox(height: 7),
                                    ],
                                    if (newLeadRequirementType!.toLowerCase() ==
                                        'fos') ...[
                                      LabeledTextField(
                                        controller: newLeadFosSidingController,
                                        hintText: 'FOS Siding',
                                        label: 'FOS Siding',
                                        maxLength: 100,
                                        keyboardType: TextInputType.name,
                                        initialValue: newLeadFosSiding,
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
                                        newLeadSalesOfficerRemarksController,
                                    hintText: 'Sales Officer Remarks',
                                    label: 'Sales Officer Remarks',
                                    maxLength: 255,
                                    keyboardType: TextInputType.name,
                                    initialValue: newLeadSalesOfficerRemarks,
                                  ),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Assigned To',
                                      onPressed: () {
                                        showSelectorDialog<EmployeeNameList>(
                                          fetchData: () =>
                                              EmployeeNameList.fetchDataFromApi(
                                                  'hos'),
                                          dialogTitle: 'Select Assigned To',
                                          getDisplayText: (item) =>
                                              item.empName ?? '',
                                          enableSearch: true,
                                          onSelected: (item) {
                                            setState(() {
                                              newLeadAssignedTo = item.empName;
                                              newLeadAssignedToCode =
                                                  item.empCode;
                                            });
                                          },
                                        );
                                      },
                                      value: newLeadAssignedTo,
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Requirement Timing',
                                      onPressed: () {
                                        showSelectorDialog<
                                            RequirementTimingList>(
                                          fetchData: () => RequirementTimingList
                                              .fetchDataFromStatic(),
                                          dialogTitle:
                                              'Select Requirement Timing',
                                          getDisplayText: (item) => item.label,
                                          enableSearch: true,
                                          onSelected: (item) {
                                            setState(() {
                                              newLeadRequirementTiming =
                                                  item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: newLeadRequirementTiming,
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                ],
                              ),
                            ],
                          ],
                          if (_leadCategory == 2 || _leadCategory == 3) ...[
                            Row(
                              children: [
                                Expanded(
                                  child: RadioListTile<int>(
                                    contentPadding: EdgeInsets.zero,
                                    title: const Text("Hot"),
                                    value: 1,
                                    // ignore: deprecated_member_use
                                    groupValue: _leadStatus,
                                    // ignore: deprecated_member_use
                                    onChanged: (value) {
                                      _clearAllExistingData();
                                      setState(() {
                                        _leadStatus = 1;
                                        _lead_status = 'hot';
                                      });
                                    },
                                  ),
                                ),
                                Expanded(
                                  child: RadioListTile<int>(
                                    contentPadding: EdgeInsets.zero,
                                    title: const Text("Warm"),
                                    value: 2,
                                    // ignore: deprecated_member_use
                                    groupValue: _leadStatus,
                                    // ignore: deprecated_member_use
                                    onChanged: (value) {
                                      _clearAllExistingData();
                                      setState(() {
                                        _leadStatus = 2;
                                        _lead_status = 'warm';
                                      });
                                    },
                                  ),
                                ),
                                Expanded(
                                  child: RadioListTile<int>(
                                    contentPadding: EdgeInsets.zero,
                                    title: const Text("Cold"),
                                    value: 3,
                                    // ignore: deprecated_member_use
                                    groupValue: _leadStatus,
                                    // ignore: deprecated_member_use
                                    onChanged: (value) {
                                      _clearAllExistingData();
                                      setState(() {
                                        _leadStatus = 3;
                                        _lead_status = 'cold';
                                      });
                                    },
                                  ),
                                ),
                              ],
                            ),
                            if (_lead_status.isNotEmpty) ...[
                              // Enquiry Id
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Search Enquiry Id',
                                      onPressed: () {
                                        showSelectorLeadDialog<LeadListData>(
                                          dialogTitle: 'Select Enquiry',
                                          items: leadListData!,
                                          getDisplayText: (item) => item,
                                          onSelected: (item) {
                                            if (_leadCategory == 2) {
                                              _existingLeadDataShowForSO(item);
                                            } else {
                                              _repeatLeadDataShowForSO(item);
                                            }
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
                                    initialValue: existingReferredBy,
                                    isMandatory: false,
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
                                    initialValue: existingSalesOfficerName,
                                    isEditable: false,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller: existingDateStampController,
                                    hintText: 'Date Stamp',
                                    label: 'Date Stamp',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingDateStamp,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller: existingTimeStampController,
                                    hintText: 'Time Stamp',
                                    label: 'Time Stamp',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingTimeStamp,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller: existingLatitudeController,
                                    hintText: 'Latitude',
                                    label: 'Latitude',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingLatitude,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller: existingLongitudeController,
                                    hintText: 'Longitude',
                                    label: 'Longitude',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingLongitude,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ),
                              // Sold to Party Information
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  LabeledTextField(
                                    controller:
                                        existingSoldToPartyNameController,
                                    hintText: 'Sold to Party Name',
                                    label: 'Sold to Party Name',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingSoldToPartyName,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        existingSoldToPartyCodeController,
                                    hintText: 'Sold to Party Code',
                                    label: 'Sold to Party Code',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingSoldToPartyCode,
                                    isMandatory: false,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        existingSoldToPartyAddressController,
                                    hintText: 'Sold to Party Address',
                                    label: 'Sold to Party Address',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingSoldToPartyAddress,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        existingSoldToPartyStateController,
                                    hintText: 'Sold to Party State',
                                    label: 'Sold to Party State',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingSoldToPartyState,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        existingSoldToPartyDistrictsController,
                                    hintText: 'Sold to Party Districts',
                                    label: 'Sold to Party Districts',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingSoldToPartyDistricts,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ),
                              // Ship to Party Information
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  LabeledTextField(
                                    controller:
                                        existingShipToPartyNameController,
                                    hintText: 'Ship to Party Name',
                                    label: 'Ship to Party Name',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingShipToPartyName,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        existingShipToPartyCodeController,
                                    hintText: 'Ship to Party Code',
                                    label: 'Ship to Party Code',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingShipToPartyCode,
                                    isMandatory: false,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        existingShipToPartyAddressController,
                                    hintText: 'Ship to Party Address',
                                    label: 'Ship to Party Address',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingShipToPartyAddress,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        existingShipToPartyStateController,
                                    hintText: 'Ship to Party State',
                                    label: 'Ship to Party State',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingShipToPartyState,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller:
                                        existingShipToPartyDistrictsController,
                                    hintText: 'Ship to Party Districts',
                                    label: 'Ship to Party Districts',
                                    isEditable: false,
                                    keyboardType: TextInputType.name,
                                    initialValue: existingShipToPartyDistricts,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ),
                              // Source, Segment, Packaging
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Segment',
                                      onPressed: () {
                                        showSelectorDialog<SegmentList>(
                                          fetchData: () =>
                                              SegmentList.fetchDataFromStatic(),
                                          dialogTitle: 'Select Segment',
                                          getDisplayText: (item) => item.label,
                                          enableSearch: false,
                                          onSelected: (item) {
                                            setState(() {
                                              existingSegment = item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: existingSegment,
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Segment.",
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Enquiry Source',
                                      onPressed: () {
                                        showSelectorDialog<LeadSourceList>(
                                          fetchData: () => LeadSourceList
                                              .fetchDataFromStatic(),
                                          dialogTitle: 'Select Enquiry Source',
                                          getDisplayText: (item) => item.label,
                                          enableSearch: false,
                                          onSelected: (item) {
                                            setState(() {
                                              existingLeadSource = item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: existingLeadSource,
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Enquiry Source.",
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
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Product & Packaging.",
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                ],
                              ),
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
                                    initialValue: existingTotalPotentialOfSite,
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
                                    initialValue:
                                        existingCurrentPriceCompetitorPerBag,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ),
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
                                    initialValue: existingDesignation,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                  LabeledTextField(
                                    controller: existingContactNumberController,
                                    hintText: 'Contact Number',
                                    label: 'Contact Number',
                                    maxLength: 10,
                                    keyboardType: TextInputType.number,
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
                                    initialValue: existingMailId,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ),
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
                                          dialogTitle: 'Select Mode of Payment',
                                          getDisplayText: (item) => item.label,
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
                                      isEnabled: true,
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
                                          getDisplayText: (item) => item.label,
                                          enableSearch: false,
                                          onSelected: (item) {
                                            setState(() {
                                              existingCreditTerms = item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: existingCreditTerms,
                                      isEnabled: true,
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
                                          getDisplayText: (item) => item.label,
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
                                      isEnabled: true,
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
                                          fetchData: () => ConstructionTypeList
                                              .fetchDataFromStatic(),
                                          dialogTitle:
                                              'Select Category Type of Construction',
                                          getDisplayText: (item) => item.label,
                                          enableSearch: false,
                                          onSelected: (item) {
                                            setState(() {
                                              existingCategoryTypeOfConstruction =
                                                  item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: existingCategoryTypeOfConstruction,
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Category of Construction.",
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Enquiry Status',
                                      onPressed: () {
                                        showSelectorDialog<LeadStatusList>(
                                          fetchData: () => LeadStatusList
                                              .fetchDataFromStatic(),
                                          dialogTitle: 'Select Enquiry Status',
                                          getDisplayText: (item) => item.label,
                                          enableSearch: false,
                                          onSelected: (item) {
                                            setState(() {
                                              existingLeadStatus = item.label;
                                            });
                                          },
                                        );
                                      },
                                      value: existingLeadStatus,
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Enquiry Status.",
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
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Next Visit Date.",
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Requirement Type',
                                      onPressed: () {
                                        showSelectorDialog<RequirementTypeList>(
                                          fetchData: () => RequirementTypeList
                                              .fetchDataFromStatic(),
                                          dialogTitle:
                                              'Select Requirement Type',
                                          getDisplayText: (item) => item.label,
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
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Requirement Type.",
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  if (existingRequirementType!.toLowerCase() ==
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
                                  if (existingRequirementType!.toLowerCase() ==
                                      'fos') ...[
                                    LabeledTextField(
                                      controller: existingFosSidingController,
                                      hintText: 'FOS Siding',
                                      label: 'FOS Siding',
                                      maxLength: 100,
                                      keyboardType: TextInputType.name,
                                      initialValue: existingFosSiding,
                                    ),
                                    SizedBox(height: 7),
                                  ],
                                ],
                              ),
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
                                    initialValue: existingSalesOfficerRemarks,
                                    isMandatory: false,
                                  ),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Assigned To',
                                      onPressed: () {
                                        showSelectorDialog<EmployeeNameList>(
                                          fetchData: () =>
                                              EmployeeNameList.fetchDataFromApi(
                                                  'hos'),
                                          dialogTitle: 'Select Assigned To',
                                          getDisplayText: (item) =>
                                              item.empName ?? '',
                                          enableSearch: true,
                                          onSelected: (item) {
                                            setState(() {
                                              existingAssignedTo = item.empName;
                                              existingAssignedToCode =
                                                  item.empCode;
                                            });
                                          },
                                        );
                                      },
                                      value: existingAssignedTo,
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Assigned To.",
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Requirement Timing',
                                      onPressed: () {
                                        showSelectorDialog<
                                            RequirementTimingList>(
                                          fetchData: () => RequirementTimingList
                                              .fetchDataFromStatic(),
                                          dialogTitle:
                                              'Select Requirement Timing',
                                          getDisplayText: (item) => item.label,
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
                                      isEnabled: true,
                                      errorMessage:
                                          "You can't able to update Requirement Timing.",
                                      isMandatory: true),
                                  SizedBox(height: 7),
                                ],
                              ),
                            ]
                          ]
                        ],
                      ),
                    ),
                  ],
                ),
              )),
              bottomNavigationBar: Row(children: [
                Expanded(
                  child: Padding(
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
                          if (_leadCategory == 1) {
                            _requestForNewQueryGeneration('PENDING');
                          } else if (_leadCategory == 2) {
                            _requestForUpdateQueryGeneration();
                          }
                        },
                        child: const Text(
                          'Save as Draft',
                          style: TextStyle(fontSize: 16, color: Colors.white),
                        ),
                      ),
                    ),
                  ),
                ),
                Expanded(
                  child: Padding(
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
                          if (_leadCategory == 1) {
                            _checkNewLeadInformation();
                          } else {
                            _checkUpdateLeadInformation();
                          }
                        },
                        child: const Text(
                          'Move to Lead',
                          style: TextStyle(fontSize: 16, color: Colors.white),
                        ),
                      ),
                    ),
                  ),
                ),
              ]),
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
  String? lead_type; //Enquiry Source
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
  // ignore: non_constant_identifier_names
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
  String? lead_status; //Enquiry Status
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
  // ignore: non_constant_identifier_names
  String? emp_code; //lead action
  // ignore: non_constant_identifier_names
  String? customer_reference_no;

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
    // ignore: non_constant_identifier_names
    this.emp_code,
    // ignore: non_constant_identifier_names
    this.customer_reference_no,
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
      emp_code: json['emp_code'] ?? '',
      customer_reference_no: json['customer_reference_no'] ?? '',
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
      'lead_action': lead_action,
      'customer_reference_no': customer_reference_no
    };
  }

  static Future<List<LeadListData>> getCustomerById(String empCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }

    String url =
        '${AppWebService.sbDevUrl}api/leadquerymaster/?emp_code=$empCode';
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
          label: 'TENDER / GOVERNMENT PROJECT INFO',
          value: 'Tender / Government Project Info'),
      LeadSourceList(label: 'AGENCIES', value: 'Agencies'),
      LeadSourceList(label: "SITE VISIT", value: "Site Visit"),
      LeadSourceList(label: 'COMPANY SELF', value: 'Company Self'),
      LeadSourceList(label: 'PHONE CALL', value: 'Phone Call'),
      LeadSourceList(
          label: 'SALES TEAM GENERATED', value: 'Sales Team Generated'),
      LeadSourceList(label: 'SOCIAL MEDIA', value: 'Social Media'),
      LeadSourceList(label: 'ONLINE ADS', value: 'Online Ads'),
      LeadSourceList(label: 'EMAIL CAMPAIGNS', value: 'Email Campaigns'),
      LeadSourceList(
          label: 'WALK-IN AT STAR OFFICE', value: 'Walk-in at Star Office'),
      LeadSourceList(label: 'MEETING', value: 'Meeting'),
      LeadSourceList(
          label: 'REFERRAL FROM EXISTING CUSTOMER',
          value: 'Referral from Existing Customer'),
      LeadSourceList(
          label: 'DEALER / RSAR REFERENCE', value: 'Dealer / RSAR Reference'),
      LeadSourceList(
          label: 'EXHIBITIONS / TRADE FAIRS',
          value: 'Exhibitions / Trade Fairs'),
      LeadSourceList(
          label: 'HOARDINGS / BANNERS', value: 'Hoardings / Banners'),
      LeadSourceList(label: 'NEWSPAPER ADS', value: 'Newspaper Ads'),
      LeadSourceList(
          label: 'CAMPAIGNS / PROMOTIONAL ACTIVITIES',
          value: 'Campaigns / Promotional Activities'),
      LeadSourceList(label: 'OLD LEADS', value: 'Old Leads'),
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
      LeadStatusList(
          label: 'Hot ( Immediate Requirement - within 7 days )', value: 'HOT'),
      LeadStatusList(
          label: 'Warm ( Planned Required - within 8 to 14 days )',
          value: 'WARM'),
      LeadStatusList(
          label: 'Cold ( Future requirement - after 15 or more days )',
          value: 'COLD'),
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
