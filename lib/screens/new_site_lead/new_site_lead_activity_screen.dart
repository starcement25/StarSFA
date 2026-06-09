import 'dart:convert';
import 'dart:io';

import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class NewSiteLeadActivityScreen extends StatefulWidget {
  final bool isOptionSelected;
  const NewSiteLeadActivityScreen({super.key, this.isOptionSelected = false});

  @override
  State<NewSiteLeadActivityScreen> createState() =>
      _NewSiteLeadActivityScreenState();
}

class _NewSiteLeadActivityScreenState extends State<NewSiteLeadActivityScreen> {
  int _leadCategory = 0;
  bool _isNonEditable = false;
  bool _isLoading = false;
  bool _isSubmitButtonShow = false;
  String? _userType = '';
  List<SiteLeadDataList>? leadListData = [];

  // ***New Site Lead & Conversion Tracking***
  int checker = 0;
  String? date = '';
  // TextEdit Controller
  TextEditingController newSiteLeadTransactionIdController =
      TextEditingController();
  TextEditingController newSiteLeadUniqueSiteIdController =
      TextEditingController();
  TextEditingController newSiteLeadSiteCreationDateController =
      TextEditingController();
  TextEditingController newSiteLeadVisitDateController =
      TextEditingController();
  TextEditingController newSiteLeadEmployeeCodeController =
      TextEditingController();
  TextEditingController newSiteLeadEmployeeNameController =
      TextEditingController();
  TextEditingController newSiteLeadZoneController = TextEditingController();
  TextEditingController newSiteLeadLatitudeController = TextEditingController();
  TextEditingController newSiteLeadLongitudeController =
      TextEditingController();
  TextEditingController newSiteLeadCustomerNameController =
      TextEditingController();
  TextEditingController newSiteLeadCustomerContactNoController =
      TextEditingController();
  TextEditingController newSiteLeadFullAddressController =
      TextEditingController();
  TextEditingController newSiteLeadMasonNameController =
      TextEditingController();
  TextEditingController newSiteLeadMasonContactNoController =
      TextEditingController();
  TextEditingController newSiteLeadEngineerNameController =
      TextEditingController();
  TextEditingController newSiteLeadEngineerContactNoController =
      TextEditingController();
  TextEditingController newSiteLeadBuiltUpAreaController =
      TextEditingController();
  TextEditingController newSiteLeadSitePotentialController =
      TextEditingController();
  TextEditingController newSiteLeadConsumedTillDateController =
      TextEditingController();
  TextEditingController newSiteLeadBalancePotentialController =
      TextEditingController();
  TextEditingController newSiteLeadBalancePotentialControllerManual =
      TextEditingController();
  TextEditingController newSiteLeadSiteCategoryController =
      TextEditingController();
  TextEditingController newSiteLeadPricePerBagsController =
      TextEditingController();
  TextEditingController newSiteLeadNoOfBagOrderedController =
      TextEditingController();
  TextEditingController newSiteLeadCounterCodeController =
      TextEditingController();
  TextEditingController newSiteLeadASMEmpCodeController =
      TextEditingController();
  TextEditingController newSiteLeadRemarksController = TextEditingController();
  TextEditingController newSiteLeadDeliveryRemarksController =
      TextEditingController();
  TextEditingController newSiteLeadReasonForNotDeliveryController =
      TextEditingController();
  // TextEdit String
  String? newSiteLeadTransactionId = '';
  String? newSiteLeadUniqueSiteId = '';
  String? newSiteLeadSiteCreationDate = '';
  String? newSiteLeadVisitDate = '';
  String? newSiteLeadEmployeeCode = '';
  String? newSiteLeadEmployeeName = '';
  String? newSiteLeadZone = '';
  String? newSiteLeadLatitude = '';
  String? newSiteLeadLongitude = '';
  String? newSiteLeadCustomerName = '';
  String? newSiteLeadCustomerContactNo = '';
  String? newSiteLeadFullAddress = '';
  String? newSiteLeadMasonName = '';
  String? newSiteLeadMasonContactNo = '';
  String? newSiteLeadEngineerName = '';
  String? newSiteLeadEngineerContactNo = '';
  String? newSiteLeadBuiltUpArea = '';
  String? newSiteLeadSitePotential = '';
  String? newSiteLeadConsumedTillDate = '';
  String? newSiteLeadBalancePotential = '';
  String? newSiteLeadBalancePotentialManual = '';
  String? newSiteLeadSiteCategory = '';
  String? newSiteLeadPricePerBags = '';
  String? newSiteLeadNoOfBagOrdered = '';
  String? newSiteLeadCounterCode = '';
  String? newSiteLeadASMEmpCode = '';
  String? newSiteLeadRemarks = '';
  String? newSiteLeadDeliveryRemarks = '';
  String? newSiteLeadReasonForNotDelivery = '';
  // Button String
  String? newSiteLeadBranchName = '';
  String? newSiteLeadBranchCode = '';
  String? newSiteLeadStateName = '';
  String? newSiteLeadDistrictName = '';
  String? newSiteLeadPettyContractorIsRegdInStarLink = '';
  String? newSiteLeadEngineerIsRegdInStarStellar = '';
  String? newSiteLeadMeetingPerson = '';
  String? newSiteLeadDecisionMaker = '';
  String? newSiteLeadSiteSegment = '';
  String? newSiteLeadVisitType = '';
  String? newSiteLeadProjectSegment = '';
  String? newSiteLeadTypeOfConstruction = '';
  String? newSiteLeadNumberOfFloor = '';
  String? newSiteLeadCurrentStageOfConstruction = '';
  String? newSiteLeadBrandUsed = '';
  String? newSiteLeadConversion = '';
  String? newSiteLeadProduct = '';
  String? newSiteLeadRequestedDateOfDelivery = '';
  String? newSiteLeadCounterType = '';
  String? newSiteLeadCounterName = '';
  String? newSiteLeadNonConvertingReason = '';
  String? newSiteLeadSitePriority = '';
  String? newSiteLeadWeatherShieldDemo = '';
  String? newSiteLeadAsmName = '';
  String? newSiteLeadSiteStatus = '';
  // ***New Site Lead & Conversion Tracking***

  // ***Existing Site Lead & Conversion Tracking***
  int isShowMason = 0;
  int isShowEngineer = 0;
  int isShowProduct = 0;
  int isApproved = 0;
  int isRejected = 0;
  int isShowASM = 0;
  String? isShowFloorCount = '';
  String? visitTypeValue = '';
  int existingChecker = 0;
  bool isNonEditable = true;
  // TextEdit Controller
  TextEditingController existingSiteLeadTransactionIdController =
      TextEditingController();
  TextEditingController existingSiteLeadSiteCreationDateController =
      TextEditingController();
  TextEditingController existingSiteLeadVisitDateController =
      TextEditingController();
  TextEditingController existingSiteLeadEmployeeCodeController =
      TextEditingController();
  TextEditingController existingSiteLeadEmployeeNameController =
      TextEditingController();
  TextEditingController existingSiteLeadZoneController =
      TextEditingController();
  TextEditingController existingSiteLeadLatitudeController =
      TextEditingController();
  TextEditingController existingSiteLeadLongitudeController =
      TextEditingController();
  TextEditingController existingSiteLeadCustomerNameController =
      TextEditingController();
  TextEditingController existingSiteLeadCustomerContactNoController =
      TextEditingController();
  TextEditingController existingSiteLeadFullAddressController =
      TextEditingController();
  TextEditingController existingSiteLeadMasonNameController =
      TextEditingController();
  TextEditingController existingSiteLeadMasonContactNoController =
      TextEditingController();
  TextEditingController existingSiteLeadEngineerNameController =
      TextEditingController();
  TextEditingController existingSiteLeadEngineerContactNoController =
      TextEditingController();
  TextEditingController existingSiteLeadBuiltUpAreaController =
      TextEditingController();
  TextEditingController existingSiteLeadSitePotentialController =
      TextEditingController();
  TextEditingController existingSiteLeadConsumedTillDateController =
      TextEditingController();
  TextEditingController existingSiteLeadBalancePotentialController =
      TextEditingController();
  TextEditingController existingSiteLeadBalancePotentialControllerManual =
      TextEditingController();
  TextEditingController existingSiteLeadSiteCategoryController =
      TextEditingController();
  TextEditingController existingSiteLeadPricePerBagsController =
      TextEditingController();
  TextEditingController existingSiteLeadNoOfBagOrderedController =
      TextEditingController();
  TextEditingController existingSiteLeadCounterCodeController =
      TextEditingController();
  TextEditingController existingSiteLeadStatusDateTimeController =
      TextEditingController();
  TextEditingController existingSiteLeadASMEmpCodeController =
      TextEditingController();
  TextEditingController existingSiteLeadRemarksController =
      TextEditingController();
  TextEditingController existingSiteLeadDeliveryRemarksController =
      TextEditingController();
  TextEditingController existingSiteLeadReasonForNotDeliveryController =
      TextEditingController();
  // TextEdit String
  String? existingSiteLeadTransactionId = '';
  String? existingSiteLeadSiteCreationDate = '';
  String? existingSiteLeadVisitDate = '';
  String? existingSiteLeadEmployeeCode = '';
  String? existingSiteLeadEmployeeName = '';
  String? existingSiteLeadZone = '';
  String? existingSiteLeadLatitude = '';
  String? existingSiteLeadLongitude = '';
  String? existingSiteLeadCustomerName = '';
  String? existingSiteLeadCustomerContactNo = '';
  String? existingSiteLeadFullAddress = '';
  String? existingSiteLeadMasonName = '';
  String? existingSiteLeadMasonContactNo = '';
  String? existingSiteLeadEngineerName = '';
  String? existingSiteLeadEngineerContactNo = '';
  String? existingSiteLeadBuiltUpArea = '';
  String? existingSiteLeadSitePotential = '';
  String? existingSiteLeadConsumedTillDate = '';
  String? existingSiteLeadBalancePotential = '';
  String? existingSiteLeadBalancePotentialManual = '';
  String? existingSiteLeadSiteCategory = '';
  String? existingSiteLeadPricePerBags = '';
  String? existingSiteLeadNoOfBagOrdered = '';
  String? existingSiteLeadCounterCode = '';
  String? existingSiteLeadStatusDateTime = '';
  String? existingSiteLeadASMEmpCode = '';
  String? existingSiteLeadRemarks = '';
  String? existingSiteLeadDeliveryRemarks = '';
  String? existingSiteLeadReasonForNotDelivery = '';
  // Button String
  String? existingSiteLeadUniqueSiteId = '';
  String? existingSiteLeadState = '';
  String? existingSiteLeadBranch = '';
  String? existingSiteLeadBranchCode = '';
  String? existingSiteLeadDistrict = '';
  String? existingSiteLeadPettyContractorIsRegdInStarLink = '';
  String? existingSiteLeadEngineerIsRegdInStarStellar = '';
  String? existingSiteLeadMeetingPerson = '';
  String? existingSiteLeadDecisionMaker = '';
  String? existingSiteLeadSiteSegment = '';
  String? existingSiteLeadVisitType = '';
  String? existingSiteLeadProjectSegment = '';
  String? existingSiteLeadTypeOfConstruction = '';
  String? existingSiteLeadNumberOfFloor = '';
  String? existingSiteLeadCurrentStageOfConstruction = '';
  String? existingSiteLeadBrandUsed = '';
  String? existingSiteLeadConversion = '';
  String? existingSiteLeadProduct = '';
  String? existingSiteLeadRequestedDateOfDelivery = '';
  String? existingSiteLeadCounterType = '';
  String? existingSiteLeadCounterName = '';
  String? existingSiteLeadNonConvertingReason = '';
  String? existingSiteLeadSitePriority = '';
  String? existingSiteLeadWeatherShieldDemo = '';
  String? existingSiteLeadApprovalStatus = '';
  String? existingSiteLeadAsmName = '';
  String? existingSiteLeadDateOfDelivery = '';
  String? existingSiteLeadSiteStatus = '';
  // ***Existing Site Lead & Conversion Tracking***

  // ***Existing Site Lead & Conversion Tracking For ASM***
  String? transactionId;
  String? uniqueSiteId;
  String? creationDate;
  String? visitDate;
  String? employeeCode;
  String? employeeName;
  String? zone;
  String? state;
  String? branch;
  String? district;
  String? latitude;
  String? longitude;
  String? customerName;
  String? customerContactNumber;
  String? fullAddress;
  String? pettyContractorRegdInStarLink;
  String? pettyContractorHeadMasonName;
  String? pettyContractorHeadMasonContactNumber;
  String? engineerRegdInStarStellar;
  String? engineerName;
  String? engineerContactNumber;
  String? meetingPerson;
  String? decisionMaker;
  String? siteSegment;
  String? visitType;
  String? projectSegment;
  String? typeofConstruction;
  String? numberOfFloor;
  String? currentStageOfConstruction;
  String? builtUpArea;
  String? sitePotential;
  String? consumedTillDate;
  String? balancePotential;
  String? balancePotentialManual;
  String? siteCategory;
  String? brandUsed;
  String? pricePerBags;
  String? conversion;
  String? selectProduct;
  String? noOfBagOrdered;
  String? requestedDateOfDelivery;
  String? counterType;
  String? counterName;
  String? counterCode;
  String? reasonForNonConversion;
  String? sitePriority;
  String? weatherShieldDemo;
  String? asmName;
  String? asmEmployeeCode;
  String? remarks;
  String? siteStatus;
  // ***Existing Site Lead & Conversion Tracking For ASM***

  // ***Existing Site Lead & Conversion Tracking For ASM Status Update Popup***
  String? status = '';
  // TextEdit Controller
  TextEditingController statusUpdatePopupDeliveryRemarksController =
      TextEditingController();
  TextEditingController statusUpdatePopupReasonForNotDeliveryController =
      TextEditingController();
  // TextEdit String
  String? statusUpdatePopupDeliveryRemarks = '';
  String? statusUpdatePopupReasonForNotDelivery = '';
  // Button String
  String? statusUpdatePopupStatus = '';
  String? statusUpdatePopupActualDateOfDelivery = '';
  // ***Existing Site Lead & Conversion Tracking For ASM Status Update Popup***

  @override
  void initState() {
    super.initState();
    _newLeadInformation();
    _fetchEmployeeDetails();

    newSiteLeadSitePotentialController.addListener(_calculateBalance);
    newSiteLeadConsumedTillDateController.addListener(_calculateBalance);
    newSiteLeadCustomerContactNoController.addListener(_checkData);
    existingSiteLeadSitePotentialController
        .addListener(_calculateBalanceExisting);
    existingSiteLeadConsumedTillDateController
        .addListener(_calculateBalanceExisting);
  }

  @override
  void dispose() {
    newSiteLeadSitePotentialController.removeListener(_calculateBalance);
    newSiteLeadConsumedTillDateController.removeListener(_calculateBalance);
    newSiteLeadCustomerContactNoController.removeListener(_checkData);
    super.dispose();
  }

  void _calculateBalance() {
    final sitePotential =
        int.tryParse(newSiteLeadSitePotentialController.text) ?? 0;
    final consumedTillDate =
        int.tryParse(newSiteLeadConsumedTillDateController.text) ?? 0;

    final balance = sitePotential - consumedTillDate;

    String category = "";
    if (balance >= 1000) {
      category = "High";
    } else if (balance >= 200) {
      category = "Medium";
    } else {
      category = "Low";
    }

    setState(() {
      newSiteLeadBalancePotential = balance.toString();
      newSiteLeadBalancePotentialController.text = balance.toString();
      newSiteLeadSiteCategory = category;
      newSiteLeadSiteCategoryController.text = category;
    });

    // ignore: avoid_print
    print("hit");
  }

  void _calculateBalanceExisting() {
    final sitePotential =
        int.tryParse(existingSiteLeadSitePotentialController.text) ?? 0;
    final consumedTillDate =
        int.tryParse(existingSiteLeadConsumedTillDateController.text) ?? 0;

    final balance = sitePotential - consumedTillDate;

    String category = "";
    if (balance >= 1000) {
      category = "High";
    } else if (balance >= 200) {
      category = "Medium";
    } else {
      category = "Low";
    }

    setState(() {
      existingSiteLeadBalancePotential = balance.toString();
      existingSiteLeadBalancePotentialController.text = balance.toString();
      existingSiteLeadSiteCategory = category;
      existingSiteLeadSiteCategoryController.text = category;
    });

    // ignore: avoid_print
    print("hit");
  }

  void _checkData() {
    String a = '';
    if (newSiteLeadCustomerContactNoController.text.length == 7) {
      a = '${date!}${newSiteLeadCustomerContactNoController.text[6]}***';
    }
    if (newSiteLeadCustomerContactNoController.text.length == 8) {
      a = '${date!}${newSiteLeadCustomerContactNoController.text[6]}${newSiteLeadCustomerContactNoController.text[7]}**';
    }
    if (newSiteLeadCustomerContactNoController.text.length == 9) {
      a = '${date!}${newSiteLeadCustomerContactNoController.text[6]}${newSiteLeadCustomerContactNoController.text[7]}${newSiteLeadCustomerContactNoController.text[8]}*';
    }
    if (newSiteLeadCustomerContactNoController.text.length == 10) {
      a = date! +
          newSiteLeadCustomerContactNoController.text[6] +
          newSiteLeadCustomerContactNoController.text[7] +
          newSiteLeadCustomerContactNoController.text[8] +
          newSiteLeadCustomerContactNoController.text[9];
    }

    setState(() {
      newSiteLeadUniqueSiteIdController.text = a;
      newSiteLeadUniqueSiteId = a;
    });
  }

  void _newLeadInformation() async {
    try {
      Position position = await _getCurrentLocation();
      DateTime now = DateTime.now();

      final userDetails = await UserLoginClass.getLocalUser();
      String? empCode = userDetails!.empCode ?? '';
      String? empName = userDetails.empName ?? '';

      String formattedDate = DateFormat('yyyy-MM-dd').format(now);
      String idCode = DateFormat('yyyyMMddHHmmss').format(now);

      String latitudeLocation = position.latitude.toString();
      String longitudeLocation = position.longitude.toString();

      date = DateFormat('yyMMdd').format(now);

      setState(() {
        newSiteLeadTransactionId = 'SU$empCode$idCode';
        newSiteLeadTransactionIdController.text = 'SU$empCode$idCode';

        newSiteLeadUniqueSiteId = "${DateFormat('yyMMdd').format(now)}****";
        newSiteLeadUniqueSiteIdController.text =
            "${DateFormat('yyMMdd').format(now)}****";

        newSiteLeadSiteCreationDate = formattedDate;
        newSiteLeadSiteCreationDateController.text = formattedDate;

        newSiteLeadVisitDate = formattedDate;
        newSiteLeadVisitDateController.text = formattedDate;

        newSiteLeadEmployeeCode = empCode.toString();
        newSiteLeadEmployeeCodeController.text = empCode.toString();

        newSiteLeadEmployeeName = empName.toString();
        newSiteLeadEmployeeNameController.text = empName.toString();

        newSiteLeadLatitude = latitudeLocation;
        newSiteLeadLatitudeController.text = latitudeLocation;

        newSiteLeadLongitude = longitudeLocation;
        newSiteLeadLongitudeController.text = longitudeLocation;

        existingSiteLeadVisitDateController.text = formattedDate;
        existingSiteLeadVisitDate = formattedDate;

        existingSiteLeadEmployeeCode = empCode.toString();
        existingSiteLeadEmployeeCodeController.text = empCode.toString();

        existingSiteLeadEmployeeName = empName.toString();
        existingSiteLeadEmployeeNameController.text = empName.toString();

        existingSiteLeadLatitude = latitudeLocation;
        existingSiteLeadLatitudeController.text = latitudeLocation;

        existingSiteLeadLongitude = longitudeLocation;
        existingSiteLeadLongitudeController.text = longitudeLocation;
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

  Future<void> _fetchEmployeeDetails() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final userDetails = await UserLoginClass.getLocalUser();
      String? empCode = userDetails!.empCode ?? '';
      final headers = {
        'Content-Type': 'application/json',
      };
      final response = await http.get(
        Uri.parse(
            '${AppWebService.baseURL}misreport/api_get_employee_detail_site_lead.php?emp_code=$empCode'),
        headers: headers,
      );

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['designation']!.toString().toLowerCase() == 'asm') {
          setState(() {
            _userType = 'asm';
          });
        } else {
          setState(() {
            _userType = 'so';
          });
        }
        setState(() {
          newSiteLeadZone = responseData['zone'].toString();
          _isLoading = false;
        });
      }
    } catch (e) {
      // ignore: avoid_print
      print("❌ Api Calling Error: $e");
    }
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
                                    Color textColor =
                                        Color.fromARGB(255, 0, 0, 0);
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
                                                  getDisplayText(item)
                                                      .approval_status
                                                      .toString()
                                                      .toUpperCase(),
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

  void showSelectorASMLeadDialog<T>({
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

  void showSelectorExistingASMLeadDialog<T>({
    required Future<List<T>> Function() fetchData,
    required String dialogTitle,
    required ExistingSiteLeadDataList Function(T) getDisplayText,
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

  void _showSnackBar(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: Colors.red,
      ),
    );
  }

  void _showYesNoDialog(
      BuildContext context, String title, String msg, int type) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(title),
          content: Text(msg),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop(false);
              },
              child: Text("No"),
            ),
            TextButton(
              onPressed: () {
                if (type == 1) {
                  checker = 1;
                  Navigator.of(context).pop(true);
                  _checkNewLeadInformation();
                } else {
                  existingChecker = 1;
                  Navigator.of(context).pop(true);
                  _checkExistingLeadInformation();
                }
              },
              child: Text("Yes"),
            ),
          ],
        );
      },
    );
  }

  //New Site Lead related function
  void _checkNewLeadInformation() {
    if (newSiteLeadBranchName == '') {
      _showSnackBar("Please select Branch.");
      return;
    }
    if (newSiteLeadStateName == '') {
      _showSnackBar("Please select State.");
      return;
    }
    if (newSiteLeadDistrictName == '') {
      _showSnackBar("Please select District.");
      return;
    }
    if (newSiteLeadCustomerNameController.text.trim().isEmpty) {
      _showSnackBar("Please enter Customer Name.");
      return;
    }
    if (newSiteLeadCustomerContactNoController.text.trim().isEmpty) {
      _showSnackBar("Please enter Customer Contact Number.");
      return;
    }
    if (newSiteLeadCustomerContactNoController.text.trim().length != 10) {
      _showSnackBar("Please enter Correct Customer Contact Number.");
      return;
    }
    if (newSiteLeadFullAddressController.text.trim().isEmpty) {
      _showSnackBar("Please enter Customer Full Address.");
      return;
    }

    if (newSiteLeadPettyContractorIsRegdInStarLink == '') {
      _showSnackBar("Please select is register contractor in StarLink.");
      return;
    }
    if (newSiteLeadMasonNameController.text.trim().isEmpty &&
        (newSiteLeadPettyContractorIsRegdInStarLink ?? '').toLowerCase() ==
            'yes') {
      _showSnackBar("Please enter Contractor Name.");
      return;
    }
    if (newSiteLeadMasonContactNoController.text.trim().isEmpty &&
        (newSiteLeadPettyContractorIsRegdInStarLink ?? '').toLowerCase() ==
            'yes') {
      _showSnackBar("Please enter Contractor Contact Number.");
      return;
    }
    if (newSiteLeadMasonContactNoController.text.trim().length != 10 &&
        (newSiteLeadPettyContractorIsRegdInStarLink ?? '').toLowerCase() ==
            'yes') {
      _showSnackBar("Please enter Correct Contractor Contact Number.");
      return;
    }
    if ((newSiteLeadPettyContractorIsRegdInStarLink ?? '').toLowerCase() ==
            'no' &&
        (newSiteLeadMasonNameController.text.trim() != '' ||
            newSiteLeadMasonContactNoController.text.trim() != '')) {
      if (newSiteLeadMasonNameController.text.trim().isEmpty) {
        _showSnackBar("Please enter Contractor Name.");
        return;
      }
      if (newSiteLeadMasonContactNoController.text.trim().isEmpty) {
        _showSnackBar("Please enter Contractor Contact Number.");
        return;
      }
      if (newSiteLeadMasonContactNoController.text.trim().length != 10) {
        _showSnackBar("Please enter Correct Contractor Contact Number.");
        return;
      }
    }

    if (newSiteLeadEngineerIsRegdInStarStellar == '') {
      _showSnackBar("Please select is register engineer in StarStellar.");
      return;
    }
    if (newSiteLeadEngineerNameController.text.trim().isEmpty &&
        (newSiteLeadEngineerIsRegdInStarStellar ?? '').toLowerCase() == 'yes') {
      _showSnackBar("Please enter Engineer Name.");
      return;
    }
    if (newSiteLeadEngineerContactNoController.text.trim().isEmpty &&
        (newSiteLeadEngineerIsRegdInStarStellar ?? '').toLowerCase() == 'yes') {
      _showSnackBar("Please enter Engineer Contact Number.");
      return;
    }
    if (newSiteLeadEngineerContactNoController.text.length != 10 &&
        (newSiteLeadEngineerIsRegdInStarStellar ?? '').toLowerCase() == 'yes') {
      _showSnackBar("Please enter Correct Engineer Contact Number.");
      return;
    }
    if ((newSiteLeadEngineerIsRegdInStarStellar ?? '').toLowerCase() == 'no' &&
        (newSiteLeadEngineerNameController.text.trim() != '' ||
            newSiteLeadEngineerContactNoController.text.trim() != '')) {
      if (newSiteLeadEngineerNameController.text.trim().isEmpty) {
        _showSnackBar("Please enter Engineer Name.");
        return;
      }
      if (newSiteLeadEngineerContactNoController.text.trim().isEmpty) {
        _showSnackBar("Please enter Engineer Contact Number.");
        return;
      }
      if (newSiteLeadEngineerContactNoController.text.trim().length != 10) {
        _showSnackBar("Please enter Correct Engineer Contact Number.");
        return;
      }
    }

    if (newSiteLeadMeetingPerson == '') {
      _showSnackBar("Please select Meeting Person.");
      return;
    }
    if (newSiteLeadDecisionMaker == '') {
      _showSnackBar("Please select Decision Maker.");
      return;
    }
    if (newSiteLeadSiteSegment == '') {
      _showSnackBar("Please select Site Segment.");
      return;
    }
    if (newSiteLeadVisitType == '') {
      _showSnackBar("Please select Visit Type.");
      return;
    }
    if (newSiteLeadProjectSegment == '') {
      _showSnackBar("Please select Project Segment.");
      return;
    }
    if (newSiteLeadTypeOfConstruction == '') {
      _showSnackBar("Please select Type of Construction.");
      return;
    }
    if (isShowFloorCount == '1' && newSiteLeadNumberOfFloor == '') {
      _showSnackBar("Please select Number of Floor.");
      return;
    }
    if (newSiteLeadCurrentStageOfConstruction == '') {
      _showSnackBar("Please select Current Stage of Construction.");
      return;
    }

    final builtUpArea = newSiteLeadBuiltUpAreaController.text.trim();
    if (builtUpArea.isEmpty) {
      _showSnackBar("Please enter Built-up Area.");
      return;
    }
    final intBuiltUpArea = int.tryParse(builtUpArea);
    if (intBuiltUpArea == null) {
      _showSnackBar("Please enter current Built-up Area.");
      return;
    }
    if (intBuiltUpArea < 1) {
      _showSnackBar("Please enter current Built-up Area.");
      return;
    }

    final sitePotential = newSiteLeadSitePotentialController.text.trim();
    if (sitePotential.isEmpty) {
      _showSnackBar("Please enter Site Potential.");
      return;
    }
    final intSitePotential = int.tryParse(sitePotential);
    if (intSitePotential == null) {
      _showSnackBar("Please enter current Site Potential.");
      return;
    }
    if (intSitePotential < 1) {
      _showSnackBar("Please enter current Site Potential.");
      return;
    }

    final consumedTillDate = newSiteLeadConsumedTillDateController.text.trim();
    if (consumedTillDate.isEmpty) {
      _showSnackBar("Please enter Consumed Till Date.");
      return;
    }
    final intConsumedTillDate = int.tryParse(consumedTillDate);
    if (intConsumedTillDate == null) {
      _showSnackBar("Please enter current Consumed Till Date.");
      return;
    }
    if (intConsumedTillDate < 1) {
      _showSnackBar("Please enter current Consumed Till Date.");
      return;
    }

    final balancePotential = newSiteLeadBalancePotentialController.text.trim();
    final intBalancePotential = int.tryParse(balancePotential);
    if (intBalancePotential == null) {
      _showSnackBar("Site Potential can't lower then Consumed Till Date qty.");
      return;
    }
    if (intBalancePotential < 0) {
      _showSnackBar("Site Potential can't lower then Consumed Till Date qty.");
      return;
    }

    final balancePotentialManual =
        newSiteLeadBalancePotentialControllerManual.text.trim();
    final intBalancePotentialManual = int.tryParse(balancePotentialManual);
    if (intBalancePotentialManual == null) {
      _showSnackBar("Site Potential can't lower then Consumed Till Date qty.");
      return;
    }
    if (intBalancePotentialManual < 0) {
      _showSnackBar("Site Potential can't lower then Consumed Till Date qty.");
      return;
    }
    if (intBalancePotential != intBalancePotentialManual && checker == 0) {
      _showYesNoDialog(context, 'Sure...',
          'Are you sure balance potential you enter that\'s correct?', 1);
      return;
    }

    if (newSiteLeadSiteCategoryController.text.trim().isEmpty) {
      _showSnackBar("Please enter Site Category.");
      return;
    }
    if (newSiteLeadBrandUsed!.trim().isEmpty) {
      _showSnackBar("Please select Brand Used.");
      return;
    }
    if (newSiteLeadPricePerBagsController.text.trim().isEmpty) {
      _showSnackBar("Please enter Price per Bag.");
      return;
    }

    final pricePerBags = newSiteLeadPricePerBagsController.text.trim();
    final intPricePerBags = int.tryParse(pricePerBags);
    if (intPricePerBags == null) {
      _showSnackBar("Please enter Price per Bag.");
      return;
    }
    if (intPricePerBags < 200 || intPricePerBags > 999) {
      _showSnackBar("Please enter Price per Bag between 200 to 999.");
      return;
    }

    if (newSiteLeadConversion!.trim().isEmpty) {
      _showSnackBar("Please select Business Generation.");
      return;
    }
    if (newSiteLeadConversion!.trim().toLowerCase() != "non converted") {
      if (newSiteLeadProduct!.trim().isEmpty) {
        _showSnackBar("Please select Product Name.");
        return;
      }
      if (newSiteLeadNoOfBagOrderedController.text.trim().isEmpty) {
        _showSnackBar("Please enter Order Quantity.");
        return;
      }
      final orderQuantity = newSiteLeadNoOfBagOrderedController.text.trim();
      final intOrderQuantity = int.tryParse(orderQuantity);
      if (intOrderQuantity == null) {
        _showSnackBar("Please enter Order Quantity.");
        return;
      }
      if (intOrderQuantity > intBalancePotential) {
        _showSnackBar("Order qty. can't more that Balance Potential.");
        return;
      }
      if (intOrderQuantity > 0 &&
          newSiteLeadRequestedDateOfDelivery!.trim().isEmpty) {
        _showSnackBar("Please select Requested Date of Delivery.");
        return;
      }
      if (newSiteLeadCounterType!.trim().isEmpty) {
        _showSnackBar("Please select Counter Type.");
        return;
      }
      if (newSiteLeadCounterName!.trim().isEmpty) {
        _showSnackBar("Please select Counter Name.");
        return;
      }
    }
    if (newSiteLeadConversion!.trim().toLowerCase() == "non converted" &&
        newSiteLeadNonConvertingReason!.trim().isEmpty) {
      _showSnackBar("Please select Reason for Non-Business Generation.");
      return;
    }
    if (newSiteLeadSitePriority!.trim().isEmpty) {
      _showSnackBar("Please select Site Priority.");
      return;
    }
    if (newSiteLeadWeatherShieldDemo!.trim().isEmpty) {
      _showSnackBar("Please select Weather Shield Demo.");
      return;
    }
    if (newSiteLeadSiteStatus!.trim().isEmpty) {
      _showSnackBar("Please select Site Status.");
      return;
    }

    _requestForNewSiteLead();
  }

  Future<void> _requestForNewSiteLead() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final Map<String, String> object = {
        "site_transaction_id": newSiteLeadTransactionIdController.text.trim(),
        "site_unique_id": newSiteLeadUniqueSiteIdController.text.trim(),
        "site_creation_date": newSiteLeadSiteCreationDateController.text.trim(),
        "site_visit_date": newSiteLeadVisitDateController.text.trim(),
        "employee_code": newSiteLeadEmployeeCodeController.text.trim(),
        "employee_name": newSiteLeadEmployeeNameController.text.trim(),
        "zone": newSiteLeadZoneController.text.trim(),
        "branch": newSiteLeadBranchCode!.trim(),
        "state": newSiteLeadStateName!.trim(),
        "district": newSiteLeadDistrictName!.trim(),
        "latitude": newSiteLeadLatitudeController.text.trim(),
        "longitude": newSiteLeadLongitudeController.text.trim(),
        "customer_name": newSiteLeadCustomerNameController.text.trim(),
        "customer_contact_number":
            newSiteLeadCustomerContactNoController.text.trim(),
        "customer_full_address": newSiteLeadFullAddressController.text.trim(),
        "is_register_contractor":
            newSiteLeadPettyContractorIsRegdInStarLink!.trim(),
        "contractor_name": newSiteLeadMasonNameController.text.trim(),
        "contractor_contact_number":
            newSiteLeadMasonContactNoController.text.trim(),
        "is_register_engineer": newSiteLeadEngineerIsRegdInStarStellar!.trim(),
        "engineer_name": newSiteLeadEngineerNameController.text.trim(),
        "engineer_contact_number":
            newSiteLeadEngineerContactNoController.text.trim(),
        "meeting_person": newSiteLeadMeetingPerson!.trim(),
        "decision_maker": newSiteLeadDecisionMaker!.trim(),
        "site_segment": newSiteLeadSiteSegment!.trim(),
        "visit_type": newSiteLeadVisitType!.trim(),
        "project_segment": newSiteLeadProjectSegment!.trim(),
        "type_of_construction": newSiteLeadTypeOfConstruction!.trim(),
        "floor_count": newSiteLeadNumberOfFloor!.trim(),
        "current_stage_of_construction":
            newSiteLeadCurrentStageOfConstruction!.trim(),
        "built_up_area": newSiteLeadBuiltUpAreaController.text.trim(),
        "site_potential": newSiteLeadSitePotentialController.text.trim(),
        "consumed_till_date": newSiteLeadConsumedTillDateController.text.trim(),
        "balance_potential": newSiteLeadBalancePotentialController.text.trim(),
        "balance_potential_manual":
            newSiteLeadBalancePotentialControllerManual.text.trim(),
        "site_category": newSiteLeadSiteCategoryController.text.trim(),
        "brand_used": newSiteLeadBrandUsed!.trim(),
        "price_per_bag": newSiteLeadPricePerBagsController.text.trim(),
        "conversion": newSiteLeadConversion!.trim(),
        "product_name": newSiteLeadProduct!.trim(),
        "order_quantity": newSiteLeadNoOfBagOrderedController.text.trim(),
        "requested_date_of_delivery":
            newSiteLeadRequestedDateOfDelivery!.trim(),
        "counter_type": newSiteLeadCounterType!.trim(),
        "counter_name": newSiteLeadCounterName!.trim(),
        "counter_code": newSiteLeadCounterCodeController.text.trim(),
        "reason_for_non_conversion": newSiteLeadNonConvertingReason!.trim(),
        "site_priority": newSiteLeadSitePriority!.trim(),
        "weather_shield_demo": newSiteLeadWeatherShieldDemo!.trim(),
        "approval_status": "Pending",
        "date_time": "",
        "asm_name": newSiteLeadAsmName!.trim(),
        "asm_employee_id": newSiteLeadASMEmpCodeController.text.trim(),
        "actual_date_of_delivery": "",
        "delivery_remarks": newSiteLeadDeliveryRemarksController.text.trim(),
        "reason_for_not_delivery":
            newSiteLeadReasonForNotDeliveryController.text.trim(),
        "site_status": newSiteLeadSiteStatus!.trim(),
        "remarks": newSiteLeadRemarksController.text.trim(),
      };

      // ignore: avoid_print
      print("Site Lead Send Data : ${jsonEncode(object)}");

      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}misreport/api_site_lead_form_submit.php'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(object),
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

  //Existing Site Lead related function
  void _showExistingSiteLead(SiteLeadDataList dataSet) {
    setState(() {
      existingSiteLeadUniqueSiteId = dataSet.unique_id;

      existingSiteLeadTransactionIdController.text = dataSet.transaction_id!;
      existingSiteLeadTransactionId = dataSet.transaction_id;

      existingSiteLeadSiteCreationDateController.text = dataSet.created_at!;
      existingSiteLeadSiteCreationDate = dataSet.created_at;

      existingSiteLeadZoneController.text = dataSet.zone!;
      existingSiteLeadZone = dataSet.zone;

      existingSiteLeadState = dataSet.state;
      existingSiteLeadBranch = dataSet.branch;
      existingSiteLeadBranchCode = dataSet.branch;
      existingSiteLeadDistrict = dataSet.district;

      existingSiteLeadLatitudeController.text = dataSet.latitude!;
      existingSiteLeadLatitude = dataSet.latitude;
      existingSiteLeadLongitudeController.text = dataSet.longitude!;
      existingSiteLeadLongitude = dataSet.longitude;

      existingSiteLeadCustomerNameController.text = dataSet.cust_name!;
      existingSiteLeadCustomerName = dataSet.cust_name;

      existingSiteLeadCustomerContactNoController.text = dataSet.cust_phn_no!;
      existingSiteLeadCustomerContactNo = dataSet.cust_phn_no;

      existingSiteLeadFullAddressController.text = dataSet.address!;
      existingSiteLeadFullAddress = dataSet.address;

      existingSiteLeadPettyContractorIsRegdInStarLink =
          dataSet.petty_contractor_registered;
      existingSiteLeadMasonNameController.text = dataSet.head_mason_name!;
      existingSiteLeadMasonName = dataSet.head_mason_name;
      existingSiteLeadMasonContactNoController.text =
          dataSet.head_mason_contact!;
      existingSiteLeadMasonContactNo = dataSet.head_mason_contact;

      existingSiteLeadEngineerIsRegdInStarStellar = dataSet.engg_registered;
      existingSiteLeadEngineerNameController.text = dataSet.engg_name!;
      existingSiteLeadEngineerName = dataSet.engg_name;
      existingSiteLeadEngineerContactNoController.text = dataSet.engg_contact!;
      existingSiteLeadEngineerContactNo = dataSet.engg_contact;

      existingSiteLeadMeetingPerson = dataSet.meeting_person;
      existingSiteLeadDecisionMaker = dataSet.decision_maker;
      existingSiteLeadSiteSegment = dataSet.site_segment;
      existingSiteLeadVisitType = dataSet.visit_type;
      existingSiteLeadProjectSegment = dataSet.project_segment;
      existingSiteLeadTypeOfConstruction = dataSet.type_of_const;
      existingSiteLeadNumberOfFloor = dataSet.floor_count;
      existingSiteLeadCurrentStageOfConstruction =
          dataSet.current_stage_of_construction;

      existingSiteLeadBuiltUpAreaController.text = dataSet.built_up_area!;
      existingSiteLeadBuiltUpArea = dataSet.built_up_area;
      existingSiteLeadSitePotentialController.text = dataSet.site_potential!;
      existingSiteLeadSitePotential = dataSet.site_potential;
      existingSiteLeadConsumedTillDateController.text =
          dataSet.consumed_till_date!;
      existingSiteLeadConsumedTillDate = dataSet.consumed_till_date;
      existingSiteLeadBalancePotentialController.text =
          dataSet.balance_potential!;
      existingSiteLeadBalancePotential = dataSet.balance_potential;
      existingSiteLeadBalancePotentialControllerManual.text =
          dataSet.balance_potential_manual!;
      existingSiteLeadBalancePotentialManual = dataSet.balance_potential_manual;
      existingSiteLeadSiteCategoryController.text = dataSet.site_category!;
      existingSiteLeadSiteCategory = dataSet.site_category;

      existingSiteLeadBrandUsed = dataSet.brand_used;

      existingSiteLeadPricePerBagsController.text = dataSet.price_per_bag!;
      existingSiteLeadPricePerBags = dataSet.price_per_bag;

      existingSiteLeadConversion = dataSet.conversion;
      existingSiteLeadProduct = dataSet.select_product;

      existingSiteLeadNoOfBagOrderedController.text =
          dataSet.no_of_bags_ordered!;
      existingSiteLeadNoOfBagOrdered = dataSet.no_of_bags_ordered;

      existingSiteLeadRequestedDateOfDelivery = dataSet.requested_date;
      existingSiteLeadCounterType = dataSet.counter_type;
      existingSiteLeadCounterName = dataSet.counter_name;
      existingSiteLeadCounterCodeController.text = dataSet.counter_code!;
      existingSiteLeadCounterCode = dataSet.counter_code;

      existingSiteLeadNonConvertingReason = dataSet.reason_for_non_conversion;
      existingSiteLeadSitePriority = dataSet.site_priority;
      existingSiteLeadWeatherShieldDemo = dataSet.weather_shield_demo;

      existingSiteLeadApprovalStatus = dataSet.approval_status;
      existingSiteLeadStatusDateTimeController.text =
          dataSet.approval_date_time!;
      existingSiteLeadStatusDateTime = dataSet.approval_date_time;

      existingSiteLeadAsmName = dataSet.asm_name;
      existingSiteLeadASMEmpCodeController.text = dataSet.asm_id!;
      existingSiteLeadASMEmpCode = dataSet.asm_id;

      existingSiteLeadDateOfDelivery = dataSet.actual_date_of_delivery;
      existingSiteLeadDeliveryRemarksController.text =
          dataSet.delivery_remarks!;
      existingSiteLeadDeliveryRemarks = dataSet.delivery_remarks;
      existingSiteLeadReasonForNotDeliveryController.text =
          dataSet.reason_for_not_delivery!;
      existingSiteLeadReasonForNotDelivery = dataSet.reason_for_not_delivery;
      existingSiteLeadSiteStatus = dataSet.site_status;

      visitTypeValue = dataSet.visit_type;

      existingSiteLeadRemarks = dataSet.remarks;
      existingSiteLeadRemarksController.text = dataSet.remarks!;
    });

    isShowMason = 0;
    isShowEngineer = 0;
    isShowProduct = 0;
    isApproved = 0;
    isRejected = 0;
    isShowASM = 0;
    isShowFloorCount = '0';

    if (dataSet.type_of_const!.toLowerCase() ==
            'Commercial OR Business Center'.toLowerCase() ||
        dataSet.type_of_const!.toLowerCase() ==
            'Individual House(IHB)'.toLowerCase() ||
        dataSet.type_of_const!.toLowerCase() ==
            'Residential Complex'.toLowerCase()) {
      isShowFloorCount = '1';
    } else {
      isShowFloorCount = '0';
    }

    if (dataSet.petty_contractor_registered!.toLowerCase() == 'yes' ||
        dataSet.petty_contractor_registered!.toLowerCase() == 'no') {
      isShowMason = 1;
    }
    if (dataSet.engg_registered!.toLowerCase() == 'yes' ||
        dataSet.engg_registered!.toLowerCase() == 'no') {
      isShowEngineer = 1;
    }
    if (dataSet.visit_type!.toLowerCase() == 'non star site' &&
        (dataSet.conversion!.toLowerCase() == 'non converted' ||
            dataSet.conversion!.toLowerCase() ==
                'converted to non star site')) {
      isShowProduct = 0;
    } else {
      isShowProduct = 1;
    }

    if (dataSet.visit_type!.toLowerCase() == 'non star site' &&
        dataSet.conversion!.toLowerCase() == 'converted to non star site') {
      setState(() {
        _isNonEditable = true;
      });
    } else {
      setState(() {
        _isNonEditable = false;
      });
    }

    if (dataSet.approval_status!.toLowerCase() == 'approved') {
      isApproved = 1;
      isNonEditable = false;
      setState(() {
        _isSubmitButtonShow = true;
      });
    } else {
      isNonEditable = true;
    }
    if (dataSet.approval_status!.toLowerCase() == 'rejected') {
      isRejected = 1;
    }
    if ((dataSet.no_of_bags_ordered ?? '').trim().isNotEmpty) {
      isShowASM = 1;
    }
    // ignore: avoid_print
    print("dataSet.no_of_bags_ordered : $isShowASM");
  }

  void _checkExistingLeadInformation() {
    if (existingSiteLeadPettyContractorIsRegdInStarLink!.trim().isEmpty) {
      _showSnackBar("Please select is register contractor in StarLink.");
      return;
    }
    if (existingSiteLeadPettyContractorIsRegdInStarLink!.trim().toLowerCase() ==
            'yes' &&
        existingSiteLeadMasonNameController.text.isEmpty) {
      _showSnackBar("Please enter Contractor Name.");
      return;
    }
    if (existingSiteLeadPettyContractorIsRegdInStarLink!.trim().toLowerCase() ==
            'yes' &&
        existingSiteLeadMasonContactNoController.text.isEmpty) {
      _showSnackBar("Please enter Contractor Contact Number.");
      return;
    }
    if (existingSiteLeadPettyContractorIsRegdInStarLink!.trim().toLowerCase() ==
            'yes' &&
        existingSiteLeadMasonContactNoController.text.length != 10) {
      _showSnackBar("Please enter Correct Contractor Contact Number.");
      return;
    }
    if (existingSiteLeadPettyContractorIsRegdInStarLink!.trim().toLowerCase() ==
            'no' &&
        (existingSiteLeadMasonNameController.text.isNotEmpty ||
            existingSiteLeadMasonContactNoController.text.isNotEmpty)) {
      if (existingSiteLeadMasonNameController.text.isEmpty) {
        _showSnackBar("Please enter Contractor Name.");
        return;
      }
      if (existingSiteLeadMasonContactNoController.text.isEmpty) {
        _showSnackBar("Please enter Contractor Contact Number.");
        return;
      }
      if (existingSiteLeadMasonContactNoController.text.length != 10) {
        _showSnackBar("Please enter Correct Contractor Contact Number.");
        return;
      }
    }

    if (existingSiteLeadEngineerIsRegdInStarStellar!.trim().isEmpty) {
      _showSnackBar("Please select is register engineer in StarStellar.");
      return;
    }
    if (existingSiteLeadEngineerIsRegdInStarStellar!.trim().toLowerCase() ==
            'yes' &&
        existingSiteLeadEngineerNameController.text.isEmpty) {
      _showSnackBar("Please enter Engineer Name.");
      return;
    }
    if (existingSiteLeadEngineerIsRegdInStarStellar!.trim().toLowerCase() ==
            'yes' &&
        existingSiteLeadEngineerContactNoController.text.isEmpty) {
      _showSnackBar("Please enter Engineer Contact Number.");
      return;
    }
    if (existingSiteLeadEngineerIsRegdInStarStellar!.trim().toLowerCase() ==
            'yes' &&
        existingSiteLeadEngineerContactNoController.text.length != 10) {
      _showSnackBar("Please enter Correct Engineer Contact Number.");
      return;
    }
    if (existingSiteLeadEngineerIsRegdInStarStellar!.trim().toLowerCase() ==
            'no' &&
        (existingSiteLeadEngineerNameController.text.isNotEmpty ||
            existingSiteLeadEngineerContactNoController.text.isNotEmpty)) {
      if (existingSiteLeadEngineerNameController.text.isEmpty) {
        _showSnackBar("Please enter Engineer Name.");
        return;
      }
      if (existingSiteLeadEngineerContactNoController.text.isEmpty) {
        _showSnackBar("Please enter Engineer Contact Number.");
        return;
      }
      if (existingSiteLeadEngineerContactNoController.text.length != 10) {
        _showSnackBar("Please enter Correct Engineer Contact Number.");
        return;
      }
    }

    if (existingSiteLeadMeetingPerson!.trim().isEmpty) {
      _showSnackBar("Please select Meeting Person.");
      return;
    }
    if (existingSiteLeadDecisionMaker!.trim().isEmpty) {
      _showSnackBar("Please select Decision Maker.");
      return;
    }
    if (existingSiteLeadCurrentStageOfConstruction!.trim().isEmpty) {
      _showSnackBar("Please select Current Stage of Construction.");
      return;
    }

    final consumedTillDate =
        existingSiteLeadConsumedTillDateController.text.trim();
    if (consumedTillDate.isEmpty) {
      _showSnackBar("Please enter Consumed Till Date.");
      return;
    }
    final intConsumedTillDate = int.tryParse(consumedTillDate);
    if (intConsumedTillDate == null) {
      _showSnackBar("Please enter current Consumed Till Date.");
      return;
    }
    if (intConsumedTillDate < 1) {
      _showSnackBar("Please enter current Consumed Till Date.");
      return;
    }

    final balancePotential =
        existingSiteLeadBalancePotentialController.text.trim();
    final intBalancePotential = int.tryParse(balancePotential);
    if (intBalancePotential == null) {
      _showSnackBar("Site Potential can't lower then Consumed Till Date qty.");
      return;
    }
    if (intBalancePotential < 0) {
      _showSnackBar("Site Potential can't lower then Consumed Till Date qty.");
      return;
    }

    final balancePotentialManual =
        existingSiteLeadBalancePotentialControllerManual.text.trim();
    final intBalancePotentialManual = int.tryParse(balancePotentialManual);
    if (intBalancePotentialManual == null) {
      _showSnackBar("Site Potential can't lower then Consumed Till Date qty.");
      return;
    }
    if (intBalancePotentialManual < 0) {
      _showSnackBar("Site Potential can't lower then Consumed Till Date qty.");
      return;
    }
    if (intBalancePotential != intBalancePotentialManual &&
        existingChecker == 0) {
      _showYesNoDialog(context, 'Sure...',
          'Are you sure balance potential you enter that\'s correct?', 2);
      return;
    }

    if (existingSiteLeadBrandUsed!.trim().isEmpty) {
      _showSnackBar("Please select Brand Used.");
      return;
    }

    if (existingSiteLeadPricePerBagsController.text.trim().isEmpty) {
      _showSnackBar("Please enter Price per Bag.");
      return;
    }
    final pricePerBags = existingSiteLeadPricePerBagsController.text.trim();
    final intPricePerBags = int.tryParse(pricePerBags);
    if (intPricePerBags == null) {
      _showSnackBar("Please enter Price per Bag.");
      return;
    }
    if (intPricePerBags < 200 || intPricePerBags > 999) {
      _showSnackBar("Please enter Price per Bag between 200 to 999.");
      return;
    }

    if (existingSiteLeadConversion!.trim().isEmpty) {
      _showSnackBar("Please select Business Generation.");
      return;
    }
    if (existingSiteLeadConversion!.trim().toLowerCase() != 'non converted') {
      if (existingSiteLeadProduct!.trim().isEmpty) {
        _showSnackBar("Please select Product Name.");
        return;
      }
      if (existingSiteLeadNoOfBagOrderedController.text.trim().isEmpty) {
        _showSnackBar("Please enter Order Quantity.");
        return;
      }
      final orderQty = existingSiteLeadNoOfBagOrderedController.text.trim();
      final intOrderQty = int.tryParse(orderQty);
      if (intOrderQty == null) {
        _showSnackBar("Please enter Order Quantity.");
        return;
      }
      if (intOrderQty > intBalancePotential) {
        _showSnackBar("Order qty. can't more that Balance Potential.");
        return;
      }
      if (intOrderQty > 0 &&
          existingSiteLeadRequestedDateOfDelivery!.trim().isEmpty) {
        _showSnackBar("Please select Requested Date of Delivery.");
        return;
      }
      if (existingSiteLeadCounterType!.trim().isEmpty) {
        _showSnackBar("Please select Counter Type.");
        return;
      }
      if (existingSiteLeadCounterName!.trim().isEmpty) {
        _showSnackBar("Please select Counter Name.");
        return;
      }
    }
    if (existingSiteLeadNonConvertingReason!.trim().isEmpty &&
        existingSiteLeadConversion!.trim().toLowerCase() == 'non converted') {
      _showSnackBar("Please select Reason for Non-Business Generation.");
      return;
    }

    if (existingSiteLeadWeatherShieldDemo!.trim().isEmpty) {
      _showSnackBar("Please select Weather Shield Demo.");
      return;
    }
    if (existingSiteLeadSiteStatus!.trim().isEmpty) {
      _showSnackBar("Please select Site Status.");
      return;
    }

    _requestForExistingSiteLead();
  }

  Future<void> _requestForExistingSiteLead() async {
    try {
      print("existingSiteLeadBranchCode : $existingSiteLeadBranchCode");
      setState(() {
        _isLoading = true;
      });

      final Map<String, String> object = {
        "site_transaction_id":
            existingSiteLeadTransactionIdController.text.trim(),
        "site_unique_id": existingSiteLeadUniqueSiteId!.trim(),
        "site_creation_date":
            existingSiteLeadSiteCreationDateController.text.trim(),
        "site_visit_date": existingSiteLeadVisitDateController.text.trim(),
        "employee_code": existingSiteLeadEmployeeCodeController.text.trim(),
        "employee_name": existingSiteLeadEmployeeNameController.text.trim(),
        "zone": existingSiteLeadZoneController.text.trim(),
        "branch": existingSiteLeadBranchCode!.trim(),
        "state": existingSiteLeadState!.trim(),
        "district": existingSiteLeadDistrict!.trim(),
        "latitude": existingSiteLeadLatitudeController.text.trim(),
        "longitude": existingSiteLeadLongitudeController.text.trim(),
        "customer_name": existingSiteLeadCustomerNameController.text.trim(),
        "customer_contact_number":
            existingSiteLeadCustomerContactNoController.text.trim(),
        "customer_full_address":
            existingSiteLeadFullAddressController.text.trim(),
        "is_register_contractor":
            existingSiteLeadPettyContractorIsRegdInStarLink!.trim(),
        "contractor_name": existingSiteLeadMasonNameController.text.trim(),
        "contractor_contact_number":
            existingSiteLeadMasonContactNoController.text.trim(),
        "is_register_engineer":
            existingSiteLeadEngineerIsRegdInStarStellar!.trim(),
        "engineer_name": existingSiteLeadEngineerNameController.text.trim(),
        "engineer_contact_number":
            existingSiteLeadEngineerContactNoController.text.trim(),
        "meeting_person": existingSiteLeadMeetingPerson!.trim(),
        "decision_maker": existingSiteLeadDecisionMaker!.trim(),
        "site_segment": existingSiteLeadSiteSegment!.trim(),
        "visit_type": existingSiteLeadVisitType!.trim(),
        "project_segment": existingSiteLeadProjectSegment!.trim(),
        "type_of_construction": existingSiteLeadTypeOfConstruction!.trim(),
        "floor_count": existingSiteLeadNumberOfFloor!.trim(),
        "current_stage_of_construction":
            existingSiteLeadCurrentStageOfConstruction!.trim(),
        "built_up_area": existingSiteLeadBuiltUpAreaController.text.trim(),
        "site_potential": existingSiteLeadSitePotentialController.text.trim(),
        "consumed_till_date":
            existingSiteLeadConsumedTillDateController.text.trim(),
        "balance_potential":
            existingSiteLeadBalancePotentialController.text.trim(),
        "balance_potential_manual":
            existingSiteLeadBalancePotentialControllerManual.text.trim(),
        "site_category": existingSiteLeadSiteCategoryController.text.trim(),
        "brand_used": existingSiteLeadBrandUsed!.trim(),
        "price_per_bag": existingSiteLeadPricePerBagsController.text.trim(),
        "conversion": existingSiteLeadConversion!.trim(),
        "product_name": existingSiteLeadProduct!.trim(),
        "order_quantity": existingSiteLeadNoOfBagOrderedController.text.trim(),
        "requested_date_of_delivery":
            existingSiteLeadRequestedDateOfDelivery!.trim(),
        "counter_type": existingSiteLeadCounterType!.trim(),
        "counter_name": existingSiteLeadCounterName!.trim(),
        "counter_code": existingSiteLeadCounterCodeController.text.trim(),
        "reason_for_non_conversion":
            existingSiteLeadNonConvertingReason!.trim(),
        "site_priority": existingSiteLeadSitePriority!.trim(),
        "weather_shield_demo": existingSiteLeadWeatherShieldDemo!.trim(),
        "approval_status": _leadCategory == 3 ? "Approved" : "Pending",
        "date_time": "",
        "asm_name": existingSiteLeadAsmName!.trim(),
        "asm_employee_id": existingSiteLeadASMEmpCodeController.text.trim(),
        "actual_date_of_delivery": "",
        "delivery_remarks":
            existingSiteLeadDeliveryRemarksController.text.trim(),
        "reason_for_not_delivery":
            existingSiteLeadReasonForNotDeliveryController.text.trim(),
        "site_status": existingSiteLeadSiteStatus!.trim(),
        "remarks": existingSiteLeadRemarksController.text.trim()
      };

      // ignore: avoid_print
      print("Site Lead Send Data : ${jsonEncode(object)}");

      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}misreport/api_site_lead_form_submit.php'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(object),
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
        _showSnackBar('Successfully Updated Site Lead.');
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

  //Existing Site Lead related function for ASM
  void _showExistingSiteLeadForASM(ExistingSiteLeadDataList dataSet) {
    setState(() {
      transactionId = dataSet.transaction_id;
      uniqueSiteId = dataSet.unique_id;
      creationDate = dataSet.created_at;
      visitDate = dataSet.visit_date;
      employeeCode = dataSet.emp_code;
      employeeName = dataSet.emp_name;
      zone = dataSet.zone;
      state = dataSet.state;
      branch = dataSet.branch;
      district = dataSet.district;
      latitude = dataSet.latitude;
      longitude = dataSet.longitude;
      customerName = dataSet.cust_name;
      customerContactNumber = dataSet.cust_phn_no;
      fullAddress = dataSet.address;
      pettyContractorRegdInStarLink = dataSet.petty_contractor_registered;
      pettyContractorHeadMasonName = dataSet.head_mason_name;
      pettyContractorHeadMasonContactNumber = dataSet.head_mason_contact;
      engineerRegdInStarStellar = dataSet.engg_registered;
      engineerName = dataSet.engg_name;
      engineerContactNumber = dataSet.engg_contact;
      meetingPerson = dataSet.meeting_person;
      decisionMaker = dataSet.decision_maker;
      siteSegment = dataSet.site_segment;
      visitType = dataSet.visit_type;
      projectSegment = dataSet.project_segment;
      typeofConstruction = dataSet.type_of_const;
      numberOfFloor = dataSet.floor_count;
      currentStageOfConstruction = dataSet.current_stage_of_construction;
      builtUpArea = dataSet.built_up_area;
      sitePotential = dataSet.site_potential;
      consumedTillDate = dataSet.consumed_till_date;
      balancePotential = dataSet.balance_potential;
      balancePotentialManual = dataSet.balance_potential_manual;
      siteCategory = dataSet.site_category;
      brandUsed = dataSet.brand_used;
      pricePerBags = dataSet.price_per_bag;
      conversion = dataSet.conversion;
      selectProduct = dataSet.select_product;
      noOfBagOrdered = dataSet.no_of_bags_ordered;
      requestedDateOfDelivery = dataSet.requested_date;
      counterType = dataSet.counter_type;
      counterName = dataSet.counter_name;
      counterCode = dataSet.counter_code;
      reasonForNonConversion = dataSet.reason_for_non_conversion;
      sitePriority = dataSet.site_priority;
      weatherShieldDemo = dataSet.weather_shield_demo;
      asmName = dataSet.asm_name;
      asmEmployeeCode = dataSet.asm_id;
      siteStatus = dataSet.site_status;
      remarks = dataSet.remarks;
      _isSubmitButtonShow = true;
    });
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
                            if (status!.trim().toLowerCase() == 'approved') {
                              if (statusUpdatePopupActualDateOfDelivery!
                                  .trim()
                                  .isEmpty) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text(
                                        "Please select a Actual Date of Delivery"),
                                    backgroundColor: Colors.red,
                                  ),
                                );
                              } else {
                                Navigator.of(context).pop();
                                _requestForUpdateStatusOfSiteLead();
                              }
                            } else {
                              Navigator.of(context).pop();
                              _requestForUpdateStatusOfSiteLead();
                            }
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

  Future<void> _requestForUpdateStatusOfSiteLead() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final Map<String, String> object = {
        "site_id": uniqueSiteId!.trim(),
        "approval_status": statusUpdatePopupStatus!.trim(),
        "actual_date_of_delivery":
            statusUpdatePopupActualDateOfDelivery!.trim(),
        "delivery_remarks":
            statusUpdatePopupDeliveryRemarksController.text.trim(),
        "reason_for_not_delivery":
            statusUpdatePopupReasonForNotDeliveryController.text.trim()
      };

      // ignore: avoid_print
      print("Site Lead Send Data : ${jsonEncode(object)}");

      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}misreport/api_asm_approve_site_lead.php'),
        headers: {
          'Content-Type': 'application/json',
        },
        body: jsonEncode(object),
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
        _showSnackBar('Successfully Updated Status Site Lead.');
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
                  'New Site Lead',
                  style: TextStyle(color: Colors.white),
                ),
                actions: [
                  if (_userType!.toLowerCase() == 'asm') ...[
                    IconButton(
                      icon: const Icon(
                        Icons.filter_list, // ← filter icon
                        color: Colors.white,
                      ),
                      onPressed: () {
                        showSelectorExistingASMLeadDialog<
                            ExistingSiteLeadDataList>(
                          fetchData: () =>
                              ExistingSiteLeadDataList.fetchDataFromApi(),
                          dialogTitle: 'Select Lead',
                          getDisplayText: (item) => item,
                          enableSearch: true,
                          onSelected: (item) {
                            _showExistingSiteLeadForASM(item);
                          },
                        );
                      },
                    ),
                  ],
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
                      padding: const EdgeInsets.all(10.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          if (_userType!.toLowerCase() == 'so') ...[
                            Row(
                              children: [
                                Expanded(
                                  child: GestureDetector(
                                    onTap: () {
                                      setState(() {
                                        _leadCategory = 1;
                                        _isSubmitButtonShow = true;
                                      });
                                    },
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        Radio<int>(
                                          materialTapTargetSize:
                                              MaterialTapTargetSize.shrinkWrap,
                                          visualDensity: VisualDensity.compact,
                                          value: 1,
                                          groupValue: _leadCategory,
                                          onChanged: (value) {
                                            setState(() {
                                              _leadCategory = value!;
                                              _isSubmitButtonShow = true;
                                            });
                                          },
                                        ),
                                        const Flexible(
                                          child: Text("New Site",
                                              style: TextStyle(fontSize: 12)),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                                Expanded(
                                  child: GestureDetector(
                                    onTap: () {
                                      setState(() {
                                        _leadCategory = 2;
                                        _isSubmitButtonShow = false;
                                        _isNonEditable = false;
                                      });
                                    },
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        Radio<int>(
                                          materialTapTargetSize:
                                              MaterialTapTargetSize.shrinkWrap,
                                          visualDensity: VisualDensity.compact,
                                          value: 2,
                                          groupValue: _leadCategory,
                                          onChanged: (value) {
                                            setState(() {
                                              _leadCategory = value!;
                                              _isSubmitButtonShow = false;
                                            });
                                          },
                                        ),
                                        const Flexible(
                                          child: Text("Existing Site",
                                              style: TextStyle(fontSize: 12)),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                                Expanded(
                                  child: GestureDetector(
                                    onTap: () {
                                      setState(() {
                                        _leadCategory = 3;
                                        _isSubmitButtonShow = false;
                                        _isNonEditable = true;
                                      });
                                    },
                                    child: Row(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        Radio<int>(
                                          materialTapTargetSize:
                                              MaterialTapTargetSize.shrinkWrap,
                                          visualDensity: VisualDensity.compact,
                                          value: 3,
                                          groupValue: _leadCategory,
                                          onChanged: (value) {
                                            setState(() {
                                              _leadCategory = value!;
                                              _isSubmitButtonShow = false;
                                            });
                                          },
                                        ),
                                        const Flexible(
                                          child: Text("Switch Site",
                                              style: TextStyle(fontSize: 12)),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            SizedBox(height: 7),
                            if (_leadCategory == 1) ...[
                              // Transaction Id
                              LabeledTextField(
                                controller: newSiteLeadTransactionIdController,
                                hintText: 'Transaction I\'d',
                                label: 'Transaction I\'d',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadTransactionId,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Unique Site Id
                              LabeledTextField(
                                controller: newSiteLeadUniqueSiteIdController,
                                hintText: 'Unique Site I\'d',
                                label: 'Unique Site I\'d',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadUniqueSiteId,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Creation Date
                              LabeledTextField(
                                controller:
                                    newSiteLeadSiteCreationDateController,
                                hintText: 'Site Creation Date',
                                label: 'Site Creation Date',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadSiteCreationDate,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Visit Date
                              LabeledTextField(
                                controller: newSiteLeadVisitDateController,
                                hintText: 'Visit Date',
                                label: 'Visit Date',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadVisitDate,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Employee Code
                              LabeledTextField(
                                controller: newSiteLeadEmployeeCodeController,
                                hintText: 'Employee Code',
                                label: 'Employee Code',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadEmployeeCode,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Employee Name
                              LabeledTextField(
                                controller: newSiteLeadEmployeeNameController,
                                hintText: 'Employee Name',
                                label: 'Employee Name',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadEmployeeName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Zone
                              LabeledTextField(
                                controller: newSiteLeadZoneController,
                                hintText: 'Zone',
                                label: 'Zone',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadZone,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // State
                              SelectButtonWithLabel(
                                buttonLabel: 'State',
                                onPressed: () {
                                  showSelectorDialog<StateNameList>(
                                    fetchData: () =>
                                        StateNameList.fetchDataFromApi(),
                                    dialogTitle: 'Select State Name',
                                    getDisplayText: (item) =>
                                        item.stateName ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadStateName = item.stateName;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadStateName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Branch
                              SelectButtonWithLabel(
                                buttonLabel: 'Branch',
                                onPressed: () {
                                  showSelectorDialog<BranchNameList>(
                                    fetchData: () =>
                                        BranchNameList.fetchDataFromApi(),
                                    dialogTitle: 'Select Branch Name',
                                    getDisplayText: (item) =>
                                        item.branchName ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadBranchCode = item.branchCode;
                                        newSiteLeadBranchName = item.branchName;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadBranchName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // District
                              SelectButtonWithLabel(
                                buttonLabel: 'District',
                                onPressed: () {
                                  if (newSiteLeadStateName == '') {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                        content:
                                            Text('Please select State first.'),
                                        backgroundColor: Colors.red,
                                      ),
                                    );
                                  } else {
                                    showSelectorDialog<DistrictsNameList>(
                                      fetchData: () =>
                                          DistrictsNameList.fetchDataFromApi(
                                              newSiteLeadStateName!),
                                      dialogTitle: 'Select Districts Name',
                                      getDisplayText: (item) =>
                                          item.districtsName ?? '',
                                      enableSearch: true,
                                      onSelected: (item) {
                                        setState(() {
                                          newSiteLeadDistrictName =
                                              item.districtsName;
                                        });
                                      },
                                    );
                                  }
                                },
                                value: newSiteLeadDistrictName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Latitude
                              LabeledTextField(
                                controller: newSiteLeadLatitudeController,
                                hintText: 'Latitude',
                                label: 'Latitude',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadLatitude,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Longitude
                              LabeledTextField(
                                controller: newSiteLeadLongitudeController,
                                hintText: 'Longitude',
                                label: 'Longitude',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: newSiteLeadLongitude,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Customer Name
                              LabeledTextField(
                                controller: newSiteLeadCustomerNameController,
                                hintText: 'Customer Name',
                                label: 'Customer Name',
                                keyboardType: TextInputType.name,
                                isEditable: true,
                                initialValue: newSiteLeadCustomerName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Customer Contact Number
                              LabeledTextField(
                                  controller:
                                      newSiteLeadCustomerContactNoController,
                                  hintText: 'Customer Contact Number',
                                  label: 'Customer Contact Number',
                                  keyboardType: TextInputType.phone,
                                  isEditable: true,
                                  initialValue: newSiteLeadCustomerContactNo,
                                  isMandatory: true,
                                  maxLength: 10),
                              SizedBox(height: 7),
                              // Full Address
                              LabeledTextField(
                                controller: newSiteLeadFullAddressController,
                                hintText: 'Full Address',
                                label: 'Full Address',
                                keyboardType: TextInputType.name,
                                isEditable: true,
                                initialValue: newSiteLeadFullAddress,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Petty Contractor Regd. In Star Link
                              SelectButtonWithLabel(
                                buttonLabel:
                                    'Petty Contractor Regd. In Star Link',
                                onPressed: () {
                                  showSelectorDialog<
                                      PettyContractorRegdInStarLinkList>(
                                    fetchData: () =>
                                        PettyContractorRegdInStarLinkList
                                            .fetchDataFromApi(),
                                    dialogTitle:
                                        'Is Petty Contractor Regd. in Star Link ?',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadPettyContractorIsRegdInStarLink =
                                            item.title;
                                      });
                                      if (item.title == "Yes") {
                                        Future.delayed(
                                            Duration(milliseconds: 150), () {
                                          showSelectorDialog<
                                              PettyContractorList>(
                                            fetchData: () => PettyContractorList
                                                .fetchDataFromApi(),
                                            dialogTitle:
                                                'Select Petty Contractor',
                                            getDisplayText: (item) =>
                                                '${item.name} (+91-${item.contactNumber})',
                                            enableSearch: true,
                                            onSelected: (item1) {
                                              setState(() {
                                                newSiteLeadMasonName =
                                                    item1.name;
                                                newSiteLeadMasonNameController
                                                    .text = item1.name!;

                                                newSiteLeadMasonContactNo =
                                                    item1.contactNumber;
                                                newSiteLeadMasonContactNoController
                                                        .text =
                                                    item1.contactNumber!;
                                              });
                                            },
                                          );
                                        });
                                      } else {
                                        newSiteLeadMasonNameController.clear();
                                        newSiteLeadMasonContactNoController
                                            .clear();
                                        setState(() {
                                          newSiteLeadMasonName = "";
                                          newSiteLeadMasonContactNo = "";
                                        });
                                      }
                                    },
                                  );
                                },
                                value:
                                    newSiteLeadPettyContractorIsRegdInStarLink,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              if (newSiteLeadPettyContractorIsRegdInStarLink !=
                                      "Not Required" &&
                                  newSiteLeadPettyContractorIsRegdInStarLink !=
                                      "") ...[
                                // Petty Contractor - Head Mason Name
                                LabeledTextField(
                                  controller: newSiteLeadMasonNameController,
                                  hintText:
                                      'Petty Contractor - Head Mason Name',
                                  label: 'Petty Contractor - Head Mason Name',
                                  keyboardType: TextInputType.name,
                                  isEditable:
                                      newSiteLeadPettyContractorIsRegdInStarLink ==
                                          "No",
                                  initialValue: newSiteLeadMasonName,
                                  isMandatory:
                                      newSiteLeadPettyContractorIsRegdInStarLink ==
                                          "Yes",
                                ),
                                SizedBox(height: 7),
                                // Petty Contractor - Head Mason Contact Number
                                LabeledTextField(
                                    controller:
                                        newSiteLeadMasonContactNoController,
                                    hintText:
                                        'Petty Contractor - Head Mason Contact Number',
                                    label:
                                        'Petty Contractor - Head Mason Contact Number',
                                    keyboardType: TextInputType.phone,
                                    isEditable:
                                        newSiteLeadPettyContractorIsRegdInStarLink ==
                                            "No",
                                    initialValue: newSiteLeadMasonContactNo,
                                    isMandatory:
                                        newSiteLeadPettyContractorIsRegdInStarLink ==
                                            "Yes",
                                    maxLength: 10),
                                SizedBox(height: 7),
                              ],
                              // Engineer Regd. In Star Stellar
                              SelectButtonWithLabel(
                                buttonLabel: 'Engineer Regd. In Star Stellar',
                                onPressed: () {
                                  showSelectorDialog<
                                      EngineerRegdInStarStellarList>(
                                    fetchData: () =>
                                        EngineerRegdInStarStellarList
                                            .fetchDataFromApi(),
                                    dialogTitle:
                                        'Is Engineer Regd. in Star Stellar ?',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadEngineerIsRegdInStarStellar =
                                            item.title;
                                      });
                                      if (item.title == "Yes") {
                                        Future.delayed(
                                            Duration(milliseconds: 150), () {
                                          showSelectorDialog<EngineerList>(
                                            fetchData: () =>
                                                EngineerList.fetchDataFromApi(),
                                            dialogTitle: 'Select Engineer',
                                            getDisplayText: (item) =>
                                                '${item.name} (+91-${item.contactNumber})',
                                            enableSearch: true,
                                            onSelected: (item1) {
                                              setState(() {
                                                newSiteLeadEngineerName =
                                                    item1.name;
                                                newSiteLeadEngineerNameController
                                                    .text = item1.name!;
                                                newSiteLeadEngineerContactNo =
                                                    item1.contactNumber;
                                                newSiteLeadEngineerContactNoController
                                                        .text =
                                                    item1.contactNumber!;
                                              });
                                            },
                                          );
                                        });
                                      } else {
                                        newSiteLeadEngineerNameController
                                            .clear();
                                        newSiteLeadEngineerContactNoController
                                            .clear();
                                        setState(() {
                                          newSiteLeadEngineerName = "";
                                          newSiteLeadEngineerContactNo = "";
                                        });
                                      }
                                    },
                                  );
                                },
                                value: newSiteLeadEngineerIsRegdInStarStellar,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              if (newSiteLeadEngineerIsRegdInStarStellar !=
                                      "" &&
                                  newSiteLeadEngineerIsRegdInStarStellar !=
                                      "Not Available") ...[
                                // Engineer Name
                                LabeledTextField(
                                  controller: newSiteLeadEngineerNameController,
                                  hintText: 'Engineer Name',
                                  label: 'Engineer Name',
                                  keyboardType: TextInputType.name,
                                  isEditable:
                                      newSiteLeadEngineerIsRegdInStarStellar ==
                                          "No",
                                  initialValue: newSiteLeadEngineerName,
                                  isMandatory:
                                      newSiteLeadEngineerIsRegdInStarStellar ==
                                          "Yes",
                                ),
                                SizedBox(height: 7),
                                // Engineer Contact Number
                                LabeledTextField(
                                    controller:
                                        newSiteLeadEngineerContactNoController,
                                    hintText: 'Engineer Contact Number',
                                    label: 'Engineer Contact Number',
                                    keyboardType: TextInputType.name,
                                    isEditable:
                                        newSiteLeadEngineerIsRegdInStarStellar ==
                                            "No",
                                    initialValue: newSiteLeadEngineerContactNo,
                                    isMandatory:
                                        newSiteLeadEngineerIsRegdInStarStellar ==
                                            "Yes",
                                    maxLength: 10),
                                SizedBox(height: 7),
                              ],
                              // Meeting Person
                              SelectButtonWithLabel(
                                buttonLabel: 'Meeting Person',
                                onPressed: () {
                                  showSelectorDialog<MeetingPersonList>(
                                    fetchData: () =>
                                        MeetingPersonList.fetchDataFromApi(),
                                    dialogTitle: 'Select Meeting Person',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadMeetingPerson = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadMeetingPerson,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Decision Maker
                              SelectButtonWithLabel(
                                buttonLabel: 'Decision Maker',
                                onPressed: () {
                                  showSelectorDialog<DecisionMakerList>(
                                    fetchData: () =>
                                        DecisionMakerList.fetchDataFromApi(),
                                    dialogTitle: 'Select Decision Maker',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadDecisionMaker = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadDecisionMaker,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Site Segment
                              SelectButtonWithLabel(
                                buttonLabel: 'Site Segment',
                                onPressed: () {
                                  showSelectorDialog<SiteSegmentList>(
                                    fetchData: () =>
                                        SiteSegmentList.fetchDataFromApi(),
                                    dialogTitle: 'Select Site Segment',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadSiteSegment = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadSiteSegment,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Visit Type
                              SelectButtonWithLabel(
                                buttonLabel: 'Visit Type',
                                onPressed: () {
                                  showSelectorDialog<VisitTypeList>(
                                    fetchData: () =>
                                        VisitTypeList.fetchDataFromApi(),
                                    dialogTitle: 'Select Visit Type',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadVisitType = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadVisitType,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Project Segment
                              SelectButtonWithLabel(
                                buttonLabel: 'Project Segment',
                                onPressed: () {
                                  showSelectorDialog<ProjectSegmentList>(
                                    fetchData: () =>
                                        ProjectSegmentList.fetchDataFromApi(),
                                    dialogTitle: 'Select Project Segment',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadProjectSegment = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadProjectSegment,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Type of Construction
                              SelectButtonWithLabel(
                                buttonLabel: 'Type of Construction',
                                onPressed: () {
                                  showSelectorDialog<TypeOfConstructionList>(
                                    fetchData: () => TypeOfConstructionList
                                        .fetchDataFromApi(),
                                    dialogTitle: 'Select Type of Construction',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadTypeOfConstruction =
                                            item.title;
                                        isShowFloorCount = item.value;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadTypeOfConstruction,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              if (isShowFloorCount == '1') ...[
                                //Floor Count
                                SelectButtonWithLabel(
                                  buttonLabel: 'Number of Floor',
                                  onPressed: () {
                                    showSelectorDialog<FloorCountList>(
                                      fetchData: () =>
                                          FloorCountList.fetchDataFromApi(),
                                      dialogTitle: 'Select Number of Floor',
                                      getDisplayText: (item) =>
                                          item.title ?? '',
                                      enableSearch: true,
                                      onSelected: (item) {
                                        setState(() {
                                          newSiteLeadNumberOfFloor = item.title;
                                        });
                                      },
                                    );
                                  },
                                  value: newSiteLeadNumberOfFloor,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                              ],
                              // Current Stage of Construction
                              SelectButtonWithLabel(
                                buttonLabel: 'Current Stage of Construction',
                                onPressed: () {
                                  showSelectorDialog<
                                      CurrentStageOfConstructionList>(
                                    fetchData: () =>
                                        CurrentStageOfConstructionList
                                            .fetchDataFromApi(),
                                    dialogTitle:
                                        'Select Current Stage of Construction',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadCurrentStageOfConstruction =
                                            item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadCurrentStageOfConstruction,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Built-Up Area
                              LabeledTextField(
                                controller: newSiteLeadBuiltUpAreaController,
                                hintText: 'Built-Up Area',
                                label: 'Built-Up Area',
                                keyboardType: TextInputType.number,
                                isEditable: true,
                                initialValue: newSiteLeadBuiltUpArea,
                                isMandatory: true,
                                maxLength: 5,
                              ),
                              SizedBox(height: 7),
                              // Site Potential
                              LabeledTextField(
                                controller: newSiteLeadSitePotentialController,
                                hintText: 'Site Potential',
                                label: 'Site Potential',
                                keyboardType: TextInputType.number,
                                isEditable: true,
                                initialValue: newSiteLeadSitePotential,
                                isMandatory: true,
                                maxLength: 5,
                              ),
                              SizedBox(height: 7),
                              // Consumed Till Date
                              LabeledTextField(
                                controller:
                                    newSiteLeadConsumedTillDateController,
                                hintText: 'Consumed Till Date',
                                label: 'Consumed Till Date',
                                keyboardType: TextInputType.number,
                                isEditable: true,
                                initialValue: newSiteLeadConsumedTillDate,
                                isMandatory: true,
                                maxLength: 5,
                              ),
                              SizedBox(height: 7),
                              // Balance Potential
                              LabeledTextField(
                                controller:
                                    newSiteLeadBalancePotentialController,
                                hintText: 'Balance Potential',
                                label: 'Balance Potential',
                                keyboardType: TextInputType.number,
                                isEditable: false,
                                initialValue: newSiteLeadBalancePotential,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Balance Potential manual
                              LabeledTextField(
                                controller:
                                    newSiteLeadBalancePotentialControllerManual,
                                hintText: 'Balance Potential',
                                label: 'Balance Potential',
                                keyboardType: TextInputType.number,
                                isEditable: true,
                                initialValue: newSiteLeadBalancePotentialManual,
                                isMandatory: true,
                                maxLength: 5,
                              ),
                              SizedBox(height: 7),
                              // Site Category
                              LabeledTextField(
                                controller: newSiteLeadSiteCategoryController,
                                hintText: 'Site Category',
                                label: 'Site Category',
                                keyboardType: TextInputType.number,
                                isEditable: false,
                                initialValue: newSiteLeadSiteCategory,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Brand Used
                              SelectButtonWithLabel(
                                buttonLabel: 'Brand Used',
                                onPressed: () {
                                  if (newSiteLeadVisitType == null ||
                                      newSiteLeadVisitType!.isEmpty) {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(
                                              "Please select Visit Type first")),
                                    );
                                    return; // stop execution
                                  }
                                  showSelectorDialog<BrandUsedList>(
                                    fetchData: () =>
                                        BrandUsedList.fetchDataFromApi(
                                            newSiteLeadVisitType ?? ""),
                                    dialogTitle: 'Select Brand Used',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadBrandUsed = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadBrandUsed,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Price Per Bags
                              LabeledTextField(
                                controller: newSiteLeadPricePerBagsController,
                                hintText: 'Price Per Bags',
                                label: 'Price Per Bags',
                                keyboardType: TextInputType.number,
                                isEditable: true,
                                initialValue: newSiteLeadPricePerBags,
                                isMandatory: true,
                                maxLength: 3,
                              ),
                              SizedBox(height: 7),
                              // Conversion
                              SelectButtonWithLabel(
                                buttonLabel: 'Business Generation',
                                onPressed: () {
                                  if (newSiteLeadVisitType == null ||
                                      newSiteLeadVisitType!.isEmpty) {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(
                                              "Please select Visit Type first")),
                                    );
                                    return; // stop execution
                                  }
                                  showSelectorDialog<ConversionList>(
                                    fetchData: () =>
                                        ConversionList.fetchDataFromApi(
                                            newSiteLeadVisitType ?? ""),
                                    dialogTitle: 'Select Business Generation',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadConversion = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadConversion,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              if (newSiteLeadConversion != "Non Converted" &&
                                  newSiteLeadConversion != "") ...[
                                // Select Product
                                SelectButtonWithLabel(
                                  buttonLabel: 'Select Product',
                                  onPressed: () {
                                    if (newSiteLeadVisitType == null ||
                                        newSiteLeadVisitType!.isEmpty) {
                                      ScaffoldMessenger.of(context)
                                          .showSnackBar(
                                        SnackBar(
                                            content: Text(
                                                "Please select Visit Type first")),
                                      );
                                      return; // stop execution
                                    }
                                    if (newSiteLeadConversion == null ||
                                        newSiteLeadConversion!.isEmpty) {
                                      ScaffoldMessenger.of(context)
                                          .showSnackBar(
                                        SnackBar(
                                            content: Text(
                                                "Please select Conversion first")),
                                      );
                                      return; // stop execution
                                    }
                                    showSelectorDialog<ProductList>(
                                      fetchData: () =>
                                          ProductList.fetchDataFromApi(
                                              newSiteLeadVisitType ?? "",
                                              newSiteLeadConversion ?? ''),
                                      dialogTitle: 'Select Product',
                                      getDisplayText: (item) =>
                                          item.title ?? '',
                                      enableSearch: true,
                                      onSelected: (item) {
                                        setState(() {
                                          newSiteLeadProduct = item.title;
                                        });
                                      },
                                    );
                                  },
                                  value: newSiteLeadProduct,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                                // No. of Bag Ordered
                                LabeledTextField(
                                  controller:
                                      newSiteLeadNoOfBagOrderedController,
                                  hintText: 'No. of Bag Ordered',
                                  label: 'No. of Bag Ordered',
                                  keyboardType: TextInputType.number,
                                  isEditable: true,
                                  initialValue: newSiteLeadNoOfBagOrdered,
                                  isMandatory: true,
                                  maxLength: 5,
                                ),
                                SizedBox(height: 7),
                                // Requested Date of Delivery
                                SelectButtonWithLabel(
                                  buttonLabel: 'Requested Date of Delivery',
                                  onPressed: () async {
                                    DateTime today = DateTime.now();

                                    DateTime? pickedDate = await showDatePicker(
                                      context: context,
                                      initialDate: DateTime.now(),
                                      firstDate: DateTime(
                                          today.year, today.month, today.day),
                                      lastDate: DateTime(2100),
                                    );

                                    if (pickedDate != null) {
                                      setState(() {
                                        newSiteLeadRequestedDateOfDelivery =
                                            "${pickedDate.year}-${pickedDate.month}-${pickedDate.day}";
                                      });
                                    }
                                  },
                                  value: newSiteLeadRequestedDateOfDelivery,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                              ],
                              // Counter Type
                              SelectButtonWithLabel(
                                buttonLabel: 'Counter Type',
                                onPressed: () {
                                  showSelectorDialog<CounterTypeList>(
                                    fetchData: () =>
                                        CounterTypeList.fetchDataFromApi(),
                                    dialogTitle: 'Select Counter Type',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadCounterType = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadCounterType,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Counter Name
                              SelectButtonWithLabel(
                                buttonLabel: 'Counter Name',
                                onPressed: () {
                                  if (newSiteLeadCounterType == null ||
                                      newSiteLeadCounterType!.isEmpty) {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(
                                              "Please select Counter Type first")),
                                    );
                                    return; // stop execution
                                  }
                                  showSelectorDialog<CounterNameList>(
                                    fetchData: () =>
                                        CounterNameList.fetchDataFromApi(
                                            newSiteLeadCounterType ?? ""),
                                    dialogTitle: 'Select Counter Name',
                                    getDisplayText: (item) => item.name ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadCounterName = item.name;
                                        newSiteLeadCounterCode = item.code;
                                        newSiteLeadCounterCodeController.text =
                                            item.code.toString();
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadCounterName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Counter Code
                              LabeledTextField(
                                controller: newSiteLeadCounterCodeController,
                                hintText: 'Counter Code',
                                label: 'Counter Code',
                                keyboardType: TextInputType.number,
                                isEditable: false,
                                initialValue: newSiteLeadCounterCode,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              if (newSiteLeadConversion == 'Non Converted') ...[
                                // Reason for Non-Conversion
                                SelectButtonWithLabel(
                                  buttonLabel: 'Reason for Non-Conversion',
                                  onPressed: () {
                                    showSelectorDialog<NonConvertedReasonList>(
                                      fetchData: () => NonConvertedReasonList
                                          .fetchDataFromApi(),
                                      dialogTitle:
                                          'Select Reason for Non-Conversion',
                                      getDisplayText: (item) =>
                                          item.title ?? '',
                                      enableSearch: true,
                                      onSelected: (item) {
                                        setState(() {
                                          newSiteLeadNonConvertingReason =
                                              item.title;
                                        });
                                      },
                                    );
                                  },
                                  value: newSiteLeadNonConvertingReason,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                              ],
                              // Site Priority
                              SelectButtonWithLabel(
                                buttonLabel: 'Site Priority',
                                onPressed: () {
                                  showSelectorDialog<SitePriorityList>(
                                    fetchData: () =>
                                        SitePriorityList.fetchDataFromApi(),
                                    dialogTitle: 'Select Site Priority',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadSitePriority = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadSitePriority,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Weather Shield Demo
                              SelectButtonWithLabel(
                                buttonLabel: 'Weather Shield Demo',
                                onPressed: () {
                                  showSelectorDialog<WeatherShieldDemoList>(
                                    fetchData: () => WeatherShieldDemoList
                                        .fetchDataFromApi(),
                                    dialogTitle: 'Select Weather Shield Demo',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadWeatherShieldDemo =
                                            item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadWeatherShieldDemo,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // ASM Name
                              SelectButtonWithLabel(
                                buttonLabel: 'ASM Name',
                                onPressed: () {
                                  showSelectorDialog<AsmNameList>(
                                    fetchData: () =>
                                        AsmNameList.fetchDataFromApi(),
                                    dialogTitle: 'Select ASM Name',
                                    getDisplayText: (item) => item.name ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadAsmName = item.name;
                                        newSiteLeadASMEmpCode = item.code;
                                        newSiteLeadASMEmpCodeController.text =
                                            item.code ?? "";
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadAsmName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // ASM Employee Code
                              LabeledTextField(
                                controller: newSiteLeadASMEmpCodeController,
                                hintText: 'ASM Employee Code',
                                label: 'ASM Employee Code',
                                keyboardType: TextInputType.number,
                                isEditable: false,
                                initialValue: newSiteLeadASMEmpCode,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              // Remarks
                              LabeledTextField(
                                controller: newSiteLeadRemarksController,
                                hintText: 'Remarks',
                                label: 'Remarks',
                                keyboardType: TextInputType.text,
                                isEditable: false,
                                initialValue: newSiteLeadRemarks,
                                isMandatory: false,
                              ),
                              SizedBox(height: 7),
                              // Site Status
                              SelectButtonWithLabel(
                                buttonLabel: 'Site Status',
                                onPressed: () {
                                  showSelectorDialog<SiteStatusList>(
                                    fetchData: () =>
                                        SiteStatusList.fetchDataFromApi(),
                                    dialogTitle: 'Select Site Status',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        newSiteLeadSiteStatus = item.title;
                                      });
                                    },
                                  );
                                },
                                value: newSiteLeadSiteStatus,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                            ],
                            if (_leadCategory == 2 || _leadCategory == 3) ...[
                              //Unique Site I'd
                              SelectButtonWithLabel(
                                buttonLabel: 'Unique Site I\'d',
                                onPressed: () {
                                  showSelectorLeadDialog<SiteLeadDataList>(
                                    fetchData: () =>
                                        SiteLeadDataList.fetchDataFromApi(
                                            _leadCategory),
                                    dialogTitle: 'Select Lead',
                                    getDisplayText: (item) => item,
                                    enableSearch: true,
                                    onSelected: (item) {
                                      _showExistingSiteLead(item);
                                    },
                                  );
                                },
                                value: existingSiteLeadUniqueSiteId,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Transaction I'd
                              LabeledTextField(
                                controller:
                                    existingSiteLeadTransactionIdController,
                                hintText: 'Transaction I\'d',
                                label: 'Transaction I\'d',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadTransactionId,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Site Creation Date
                              LabeledTextField(
                                controller:
                                    existingSiteLeadSiteCreationDateController,
                                hintText: 'Site Creation Date',
                                label: 'Site Creation Date',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadSiteCreationDate,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Visit Date
                              LabeledTextField(
                                controller: existingSiteLeadVisitDateController,
                                hintText: 'Visit Date',
                                label: 'Visit Date',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadVisitDate,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Employee Code
                              LabeledTextField(
                                controller:
                                    existingSiteLeadEmployeeCodeController,
                                hintText: 'Employee Code',
                                label: 'Employee Code',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadEmployeeCode,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Employee Name
                              LabeledTextField(
                                controller:
                                    existingSiteLeadEmployeeNameController,
                                hintText: 'Employee Name',
                                label: 'Employee Name',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadEmployeeName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Zone
                              LabeledTextField(
                                controller: existingSiteLeadZoneController,
                                hintText: 'Zone',
                                label: 'Zone',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadZone,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //State
                              SelectButtonWithLabel(
                                  buttonLabel: 'State',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content:
                                              Text("State can't be editable")),
                                    );
                                    return;
                                  },
                                  value: existingSiteLeadState,
                                  isMandatory: true,
                                  isEnabled: false),
                              SizedBox(height: 7),
                              //Branch
                              SelectButtonWithLabel(
                                  buttonLabel: 'Branch',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content:
                                              Text("Branch can't be editable")),
                                    );
                                    return;
                                  },
                                  value: existingSiteLeadBranch,
                                  isMandatory: true,
                                  isEnabled: false),
                              SizedBox(height: 7),
                              //District
                              SelectButtonWithLabel(
                                  buttonLabel: 'District',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(
                                              "District can't be editable")),
                                    );
                                    return;
                                  },
                                  value: existingSiteLeadDistrict,
                                  isMandatory: true,
                                  isEnabled: false),
                              SizedBox(height: 7),
                              //Latitude
                              LabeledTextField(
                                controller: existingSiteLeadLatitudeController,
                                hintText: 'Latitude',
                                label: 'Latitude',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadLatitude,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Longitude
                              LabeledTextField(
                                controller: existingSiteLeadLongitudeController,
                                hintText: 'Longitude',
                                label: 'Longitude',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadLongitude,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Customer Name
                              LabeledTextField(
                                controller:
                                    existingSiteLeadCustomerNameController,
                                hintText: 'Customer Name',
                                label: 'Customer Name',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadCustomerName,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Customer Contact Number
                              LabeledTextField(
                                controller:
                                    existingSiteLeadCustomerContactNoController,
                                hintText: 'Customer Contact Number',
                                label: 'Customer Contact Number',
                                keyboardType: TextInputType.phone,
                                isEditable: false,
                                initialValue: existingSiteLeadCustomerContactNo,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Full Address
                              LabeledTextField(
                                controller:
                                    existingSiteLeadFullAddressController,
                                hintText: 'Full Address',
                                label: 'Full Address',
                                keyboardType: TextInputType.name,
                                isEditable: false,
                                initialValue: existingSiteLeadFullAddress,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Petty Contractor Regd. In Star Link
                              SelectButtonWithLabel(
                                buttonLabel:
                                    'Petty Contractor Regd. In Star Link',
                                onPressed: () {
                                  showSelectorDialog<
                                      PettyContractorRegdInStarLinkList>(
                                    fetchData: () =>
                                        PettyContractorRegdInStarLinkList
                                            .fetchDataFromApi(),
                                    dialogTitle:
                                        'Is Petty Contractor Regd. in Star Link ?',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        existingSiteLeadPettyContractorIsRegdInStarLink =
                                            item.title;
                                      });
                                      if (item.title == "Yes") {
                                        setState(() {
                                          isShowMason = 1;
                                        });
                                        Future.delayed(
                                            Duration(milliseconds: 150), () {
                                          showSelectorDialog<
                                              PettyContractorList>(
                                            fetchData: () => PettyContractorList
                                                .fetchDataFromApi(),
                                            dialogTitle:
                                                'Select Petty Contractor',
                                            getDisplayText: (item) =>
                                                '${item.name} (+91-${item.contactNumber})',
                                            enableSearch: true,
                                            onSelected: (item1) {
                                              setState(() {
                                                existingSiteLeadMasonName =
                                                    item1.name;
                                                existingSiteLeadMasonNameController
                                                    .text = item1.name!;

                                                existingSiteLeadMasonContactNo =
                                                    item1.contactNumber;
                                                existingSiteLeadMasonContactNoController
                                                        .text =
                                                    item1.contactNumber!;
                                              });
                                            },
                                          );
                                        });
                                      } else {
                                        if (item.title == "No") {
                                          setState(() {
                                            isShowMason = 1;
                                          });
                                        } else {
                                          setState(() {
                                            isShowMason = 0;
                                          });
                                        }
                                        existingSiteLeadMasonNameController
                                            .clear();
                                        existingSiteLeadMasonContactNoController
                                            .clear();
                                        setState(() {
                                          existingSiteLeadMasonName = "";
                                          existingSiteLeadMasonContactNo = "";
                                        });
                                      }
                                    },
                                  );
                                },
                                value:
                                    existingSiteLeadPettyContractorIsRegdInStarLink,
                                isMandatory: true,
                                isEnabled:
                                    _leadCategory == 3 ? false : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                              if (isShowMason == 1) ...[
                                //Head Mason Name
                                LabeledTextField(
                                  controller:
                                      existingSiteLeadMasonNameController,
                                  hintText:
                                      'Petty Contractor - Head Mason Name',
                                  label: 'Petty Contractor - Head Mason Name',
                                  keyboardType: TextInputType.name,
                                  isEditable: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                  initialValue: existingSiteLeadMasonName,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                                //Head Mason Contact Number
                                LabeledTextField(
                                  controller:
                                      existingSiteLeadMasonContactNoController,
                                  hintText:
                                      'Petty Contractor - Head Mason Contact Number',
                                  label:
                                      'Petty Contractor - Head Mason Contact Number',
                                  keyboardType: TextInputType.phone,
                                  isEditable: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                  initialValue: existingSiteLeadMasonContactNo,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                              ],
                              //Engineer Regd. In Star Stellar
                              SelectButtonWithLabel(
                                buttonLabel: 'Engineer Regd. In Star Stellar',
                                onPressed: () {
                                  showSelectorDialog<
                                      EngineerRegdInStarStellarList>(
                                    fetchData: () =>
                                        EngineerRegdInStarStellarList
                                            .fetchDataFromApi(),
                                    dialogTitle:
                                        'Is Engineer Regd. in Star Stellar ?',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        existingSiteLeadEngineerIsRegdInStarStellar =
                                            item.title;
                                      });
                                      if (item.title == "Yes") {
                                        setState(() {
                                          isShowEngineer = 1;
                                        });
                                        Future.delayed(
                                            Duration(milliseconds: 150), () {
                                          showSelectorDialog<EngineerList>(
                                            fetchData: () =>
                                                EngineerList.fetchDataFromApi(),
                                            dialogTitle: 'Select Engineer',
                                            getDisplayText: (item) =>
                                                '${item.name} (+91-${item.contactNumber})',
                                            enableSearch: true,
                                            onSelected: (item1) {
                                              setState(() {
                                                existingSiteLeadEngineerName =
                                                    item1.name;
                                                existingSiteLeadEngineerNameController
                                                    .text = item1.name!;
                                                existingSiteLeadEngineerContactNo =
                                                    item1.contactNumber;
                                                existingSiteLeadEngineerContactNoController
                                                        .text =
                                                    item1.contactNumber!;
                                              });
                                            },
                                          );
                                        });
                                      } else {
                                        if (item.title == 'No') {
                                          setState(() {
                                            isShowEngineer = 1;
                                          });
                                        } else {
                                          setState(() {
                                            isShowEngineer = 0;
                                          });
                                        }
                                        existingSiteLeadEngineerNameController
                                            .clear();
                                        existingSiteLeadEngineerContactNoController
                                            .clear();
                                        setState(() {
                                          existingSiteLeadEngineerName = "";
                                          existingSiteLeadEngineerContactNo =
                                              "";
                                        });
                                      }
                                    },
                                  );
                                },
                                value:
                                    existingSiteLeadEngineerIsRegdInStarStellar,
                                isMandatory: true,
                                isEnabled:
                                    _leadCategory == 3 ? false : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                              if (isShowEngineer == 1) ...[
                                //Engineer Name
                                LabeledTextField(
                                  controller:
                                      existingSiteLeadEngineerNameController,
                                  hintText: 'Engineer Name',
                                  label: 'Engineer Name',
                                  keyboardType: TextInputType.name,
                                  isEditable: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                  initialValue: existingSiteLeadEngineerName,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                                //Engineer Contact Number
                                LabeledTextField(
                                  controller:
                                      existingSiteLeadEngineerContactNoController,
                                  hintText: 'Engineer Contact Number',
                                  label: 'Engineer Contact Number',
                                  keyboardType: TextInputType.name,
                                  isEditable: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                  initialValue:
                                      existingSiteLeadEngineerContactNo,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                              ],
                              //Meeting Person
                              SelectButtonWithLabel(
                                buttonLabel: 'Meeting Person',
                                onPressed: () {
                                  showSelectorDialog<MeetingPersonList>(
                                    fetchData: () =>
                                        MeetingPersonList.fetchDataFromApi(),
                                    dialogTitle: 'Select Meeting Person',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        existingSiteLeadMeetingPerson =
                                            item.title;
                                      });
                                    },
                                  );
                                },
                                value: existingSiteLeadMeetingPerson,
                                isMandatory: true,
                                isEnabled:
                                    _leadCategory == 3 ? false : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                              //Decision Maker
                              SelectButtonWithLabel(
                                buttonLabel: 'Decision Maker',
                                onPressed: () {
                                  showSelectorDialog<DecisionMakerList>(
                                    fetchData: () =>
                                        DecisionMakerList.fetchDataFromApi(),
                                    dialogTitle: 'Select Decision Maker',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        existingSiteLeadDecisionMaker =
                                            item.title;
                                      });
                                    },
                                  );
                                },
                                value: existingSiteLeadDecisionMaker,
                                isMandatory: true,
                                isEnabled:
                                    _leadCategory == 3 ? false : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                              //Site Segment
                              SelectButtonWithLabel(
                                  buttonLabel: 'Site Segment',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(
                                              "Site Segment can't be editable")),
                                    );
                                    return;
                                  },
                                  value: existingSiteLeadSiteSegment,
                                  isMandatory: true,
                                  isEnabled: false),
                              SizedBox(height: 7),
                              //Visit Type
                              SelectButtonWithLabel(
                                  buttonLabel: 'Visit Type',
                                  onPressed: () {
                                    if (_leadCategory == 3) {
                                      setState(() {
                                        existingSiteLeadVisitType =
                                            'Non Star Site';
                                        existingSiteLeadConversion =
                                            'Converted to Non Star Site';
                                        _isNonEditable = true;
                                        existingSiteLeadPricePerBags = "0";
                                        isShowProduct = 0;
                                      });
                                    } else {
                                      ScaffoldMessenger.of(context)
                                          .showSnackBar(
                                        SnackBar(
                                            content: Text(
                                                "Visit Type can't be editable")),
                                      );
                                      return;
                                    }
                                  },
                                  value: existingSiteLeadVisitType,
                                  isMandatory: true,
                                  isEnabled: _leadCategory == 3),
                              SizedBox(height: 7),
                              //Project Segment
                              SelectButtonWithLabel(
                                  buttonLabel: 'Project Segment',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(
                                              "Project Segment can't be editable")),
                                    );
                                    return;
                                  },
                                  value: existingSiteLeadProjectSegment,
                                  isMandatory: true,
                                  isEnabled: false),
                              SizedBox(height: 7),
                              //Type of Construction
                              SelectButtonWithLabel(
                                  buttonLabel: 'Type of Construction',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(
                                              "Type of Construction can't be editable")),
                                    );
                                    return;
                                  },
                                  value: existingSiteLeadTypeOfConstruction,
                                  isMandatory: true,
                                  isEnabled: false),
                              SizedBox(height: 7),
                              if (isShowFloorCount == '1') ...[
                                //Number of Floor
                                SelectButtonWithLabel(
                                    buttonLabel: 'Number of Floor',
                                    onPressed: () {
                                      ScaffoldMessenger.of(context)
                                          .showSnackBar(
                                        SnackBar(
                                            content: Text(
                                                "Number of Floor can't be editable")),
                                      );
                                      return;
                                    },
                                    value: existingSiteLeadNumberOfFloor,
                                    isMandatory: true,
                                    isEnabled: false),
                                SizedBox(height: 7),
                              ],
                              //Current Stage of Construction
                              SelectButtonWithLabel(
                                buttonLabel: 'Current Stage of Construction',
                                onPressed: () {
                                  showSelectorDialog<
                                      CurrentStageOfConstructionList>(
                                    fetchData: () =>
                                        CurrentStageOfConstructionList
                                            .fetchDataFromApi(),
                                    dialogTitle:
                                        'Select Current Stage of Construction',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        existingSiteLeadCurrentStageOfConstruction =
                                            item.title;
                                      });
                                    },
                                  );
                                },
                                value:
                                    existingSiteLeadCurrentStageOfConstruction,
                                isMandatory: true,
                                isEnabled:
                                    _leadCategory == 3 ? false : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                              //Built-Up Area
                              LabeledTextField(
                                controller:
                                    existingSiteLeadBuiltUpAreaController,
                                hintText: 'Built-Up Area',
                                label: 'Built-Up Area',
                                keyboardType: TextInputType.number,
                                isEditable: false,
                                initialValue: existingSiteLeadBuiltUpArea,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Site Potential
                              LabeledTextField(
                                controller:
                                    existingSiteLeadSitePotentialController,
                                hintText: 'Site Potential',
                                label: 'Site Potential',
                                keyboardType: TextInputType.number,
                                isEditable: false,
                                initialValue: existingSiteLeadSitePotential,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Consumed Till Date
                              LabeledTextField(
                                controller:
                                    existingSiteLeadConsumedTillDateController,
                                hintText: 'Consumed Till Date',
                                label: 'Consumed Till Date',
                                keyboardType: TextInputType.number,
                                isEditable: _leadCategory == 3
                                    ? _isNonEditable
                                    : !isNonEditable,
                                initialValue: existingSiteLeadConsumedTillDate,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Balance Potential
                              LabeledTextField(
                                controller:
                                    existingSiteLeadBalancePotentialController,
                                hintText: 'Balance Potential',
                                label: 'Balance Potential',
                                keyboardType: TextInputType.number,
                                isEditable: false,
                                initialValue: existingSiteLeadBalancePotential,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Balance Potential Manual
                              LabeledTextField(
                                controller:
                                    existingSiteLeadBalancePotentialControllerManual,
                                hintText: 'Balance Potential',
                                label: 'Balance Potential',
                                keyboardType: TextInputType.number,
                                isEditable: _leadCategory == 3
                                    ? _isNonEditable
                                    : !isNonEditable,
                                initialValue:
                                    existingSiteLeadBalancePotentialManual,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Site Category
                              LabeledTextField(
                                controller:
                                    existingSiteLeadSiteCategoryController,
                                hintText: 'Site Category',
                                label: 'Site Category',
                                keyboardType: TextInputType.number,
                                isEditable: false,
                                initialValue: existingSiteLeadSiteCategory,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Brand Used
                              SelectButtonWithLabel(
                                buttonLabel: 'Brand Used',
                                onPressed: () {
                                  showSelectorDialog<ExistingBrandUsedList>(
                                    fetchData: () =>
                                        ExistingBrandUsedList.fetchDataFromApi(
                                            _leadCategory),
                                    dialogTitle: 'Select Brand Used',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        existingSiteLeadBrandUsed = item.title;
                                        visitTypeValue = item.value;
                                      });
                                    },
                                  );
                                },
                                value: existingSiteLeadBrandUsed,
                                isMandatory: true,
                                isEnabled: _leadCategory == 3
                                    ? _isNonEditable
                                    : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                              //Price Per Bags
                              LabeledTextField(
                                controller:
                                    existingSiteLeadPricePerBagsController,
                                hintText: 'Price Per Bags',
                                label: 'Price Per Bags',
                                keyboardType: TextInputType.number,
                                isEditable: _leadCategory == 3
                                    ? _isNonEditable
                                    : !isNonEditable,
                                initialValue: existingSiteLeadPricePerBags,
                                isMandatory: true,
                              ),
                              SizedBox(height: 7),
                              //Conversion
                              SelectButtonWithLabel(
                                buttonLabel: 'Business Generation',
                                onPressed: () {
                                  showSelectorDialog<ConversionList>(
                                    fetchData: () =>
                                        ConversionList.fetchDataFromApi(
                                            visitTypeValue ?? ""),
                                    dialogTitle: 'Select Business Generation',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      if (item.title != "Non Converted" &&
                                          item.title != "") {
                                        setState(() {
                                          isShowProduct = 1;
                                        });
                                      } else {
                                        setState(() {
                                          isShowProduct = 0;
                                        });
                                      }
                                      setState(() {
                                        existingSiteLeadConversion = item.title;
                                      });
                                    },
                                  );
                                },
                                value: existingSiteLeadConversion,
                                isMandatory: true,
                                isEnabled:
                                    _leadCategory == 3 ? false : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                              if (isShowProduct == 1) ...[
                                //Select Product
                                SelectButtonWithLabel(
                                  buttonLabel: 'Select Product',
                                  onPressed: () {
                                    showSelectorDialog<ProductList>(
                                      fetchData: () =>
                                          ProductList.fetchDataFromApi(
                                              visitTypeValue ?? "",
                                              existingSiteLeadConversion ?? ''),
                                      dialogTitle: 'Select Product',
                                      getDisplayText: (item) =>
                                          item.title ?? '',
                                      enableSearch: true,
                                      onSelected: (item) {
                                        setState(() {
                                          existingSiteLeadProduct = item.title;
                                        });
                                      },
                                    );
                                  },
                                  value: existingSiteLeadProduct,
                                  isMandatory: true,
                                  isEnabled: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                ),
                                SizedBox(height: 7),
                                //No. of Bag Ordered
                                LabeledTextField(
                                  controller:
                                      existingSiteLeadNoOfBagOrderedController,
                                  hintText: 'No. of Bag Ordered',
                                  label: 'No. of Bag Ordered',
                                  keyboardType: TextInputType.number,
                                  isEditable: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                  initialValue: existingSiteLeadNoOfBagOrdered,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                                //Requested Date of Delivery
                                SelectButtonWithLabel(
                                  buttonLabel: 'Requested Date of Delivery',
                                  onPressed: () async {
                                    DateTime today = DateTime.now();

                                    DateTime? pickedDate = await showDatePicker(
                                      context: context,
                                      initialDate: DateTime.now(),
                                      firstDate: DateTime(
                                          today.year, today.month, today.day),
                                      lastDate: DateTime(2100),
                                    );

                                    if (pickedDate != null) {
                                      setState(() {
                                        existingSiteLeadRequestedDateOfDelivery =
                                            "${pickedDate.year}-${pickedDate.month}-${pickedDate.day}";
                                      });
                                    }
                                  },
                                  value:
                                      existingSiteLeadRequestedDateOfDelivery,
                                  isMandatory: true,
                                  isEnabled: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                ),
                                SizedBox(height: 7),
                              ],
                              if (!_isNonEditable) ...[
                                //Counter Type
                                SelectButtonWithLabel(
                                  buttonLabel: 'Counter Type',
                                  onPressed: () {
                                    showSelectorDialog<CounterTypeList>(
                                      fetchData: () =>
                                          CounterTypeList.fetchDataFromApi(),
                                      dialogTitle: 'Select Counter Type',
                                      getDisplayText: (item) =>
                                          item.title ?? '',
                                      enableSearch: true,
                                      onSelected: (item) {
                                        setState(() {
                                          existingSiteLeadCounterType =
                                              item.title;
                                        });
                                      },
                                    );
                                  },
                                  value: existingSiteLeadCounterType,
                                  isMandatory: true,
                                  isEnabled: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                ),
                                SizedBox(height: 7),
                                //Counter Name
                                SelectButtonWithLabel(
                                  buttonLabel: 'Counter Name',
                                  onPressed: () {
                                    if (existingSiteLeadCounterType == null ||
                                        existingSiteLeadCounterType!.isEmpty) {
                                      ScaffoldMessenger.of(context)
                                          .showSnackBar(
                                        SnackBar(
                                            content: Text(
                                                "Please select Counter Type first")),
                                      );
                                      return; // stop execution
                                    }
                                    showSelectorDialog<CounterNameList>(
                                      fetchData: () =>
                                          CounterNameList.fetchDataFromApi(
                                              existingSiteLeadCounterType ??
                                                  ""),
                                      dialogTitle: 'Select Counter Name',
                                      getDisplayText: (item) => item.name ?? '',
                                      enableSearch: true,
                                      onSelected: (item) {
                                        setState(() {
                                          existingSiteLeadCounterName =
                                              item.name;
                                          existingSiteLeadCounterCode =
                                              item.code;
                                          existingSiteLeadCounterCodeController
                                              .text = item.code.toString();
                                        });
                                      },
                                    );
                                  },
                                  value: existingSiteLeadCounterName,
                                  isMandatory: true,
                                  isEnabled: _leadCategory == 3
                                      ? false
                                      : !isNonEditable,
                                ),
                                SizedBox(height: 7),
                                //Counter Code
                                LabeledTextField(
                                  controller:
                                      existingSiteLeadCounterCodeController,
                                  hintText: 'Counter Code',
                                  label: 'Counter Code',
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue: existingSiteLeadCounterCode,
                                  isMandatory: true,
                                ),
                                SizedBox(height: 7),
                              ],

                              if (isShowProduct == 0) ...[
                                //Reason for Non-Conversion
                                SelectButtonWithLabel(
                                  buttonLabel: 'Reason for Non-Conversion',
                                  onPressed: () {
                                    showSelectorDialog<NonConvertedReasonList>(
                                      fetchData: () => NonConvertedReasonList
                                          .fetchDataFromApi(),
                                      dialogTitle:
                                          'Select Reason for Non-Conversion',
                                      getDisplayText: (item) =>
                                          item.title ?? '',
                                      enableSearch: true,
                                      onSelected: (item) {
                                        setState(() {
                                          existingSiteLeadNonConvertingReason =
                                              item.title;
                                        });
                                      },
                                    );
                                  },
                                  value: existingSiteLeadNonConvertingReason,
                                  isMandatory: true,
                                  isEnabled: !isNonEditable,
                                ),
                                SizedBox(height: 7),
                              ],
                              //Site Priority
                              SelectButtonWithLabel(
                                  buttonLabel: 'Site Priority',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                          content: Text(
                                              "Site Priority can't be editable")),
                                    );
                                    return;
                                  },
                                  value: existingSiteLeadSitePriority,
                                  isMandatory: true,
                                  isEnabled: false),
                              SizedBox(height: 7),
                              //Weather Shield Demo
                              SelectButtonWithLabel(
                                buttonLabel: 'Weather Shield Demo',
                                onPressed: () {
                                  showSelectorDialog<WeatherShieldDemoList>(
                                    fetchData: () => WeatherShieldDemoList
                                        .fetchDataFromApi(),
                                    dialogTitle: 'Select Weather Shield Demo',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        existingSiteLeadWeatherShieldDemo =
                                            item.title;
                                      });
                                    },
                                  );
                                },
                                value: existingSiteLeadWeatherShieldDemo,
                                isMandatory: true,
                                isEnabled:
                                    _leadCategory == 3 ? false : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                              if (!_isNonEditable) ...[
                                //Approval Status
                                SelectButtonWithLabel(
                                    buttonLabel: 'Approval Status',
                                    onPressed: () {
                                      ScaffoldMessenger.of(context)
                                          .showSnackBar(
                                        SnackBar(
                                            content: Text(
                                                "Approval Status can't be editable")),
                                      );
                                      return;
                                    },
                                    value: existingSiteLeadApprovalStatus,
                                    isMandatory: true,
                                    isEnabled: false),
                                SizedBox(height: 7),
                                if (isApproved == 1) ...[
                                  //Date and Time
                                  LabeledTextField(
                                    controller:
                                        existingSiteLeadStatusDateTimeController,
                                    hintText: 'Date and Time',
                                    label: 'Date and Time',
                                    keyboardType: TextInputType.number,
                                    isEditable: false,
                                    initialValue:
                                        existingSiteLeadStatusDateTime,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                                if (isShowASM == 1) ...[
                                  //ASM Name
                                  SelectButtonWithLabel(
                                    buttonLabel: 'ASM Name',
                                    onPressed: () {
                                      showSelectorDialog<AsmNameList>(
                                        fetchData: () =>
                                            AsmNameList.fetchDataFromApi(),
                                        dialogTitle: 'Select ASM Name',
                                        getDisplayText: (item) =>
                                            item.name ?? '',
                                        enableSearch: true,
                                        onSelected: (item) {
                                          setState(() {
                                            existingSiteLeadAsmName = item.name;
                                            existingSiteLeadASMEmpCode =
                                                item.code;
                                            existingSiteLeadASMEmpCodeController
                                                .text = item.code ?? "";
                                          });
                                        },
                                      );
                                    },
                                    value: existingSiteLeadAsmName,
                                    isMandatory: true,
                                    isEnabled: !isNonEditable,
                                  ),
                                  SizedBox(height: 7),
                                  //ASM Employee Code
                                  LabeledTextField(
                                    controller:
                                        existingSiteLeadASMEmpCodeController,
                                    hintText: 'ASM Employee Code',
                                    label: 'ASM Employee Code',
                                    keyboardType: TextInputType.number,
                                    isEditable: false,
                                    initialValue: existingSiteLeadASMEmpCode,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                                if (isApproved == 1) ...[
                                  //Date of Delivery
                                  SelectButtonWithLabel(
                                      buttonLabel: 'Date of Delivery',
                                      onPressed: () {
                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(
                                          SnackBar(
                                              content: Text(
                                                  "Date of Delivery can't be editable")),
                                        );
                                        return;
                                      },
                                      value: existingSiteLeadDateOfDelivery,
                                      isMandatory: true,
                                      isEnabled: false),
                                  SizedBox(height: 7),
                                  //Delivery Remarks
                                  LabeledTextField(
                                    controller:
                                        existingSiteLeadDeliveryRemarksController,
                                    hintText: 'Delivery Remarks',
                                    label: 'Delivery Remarks',
                                    keyboardType: TextInputType.number,
                                    isEditable: false,
                                    initialValue:
                                        existingSiteLeadDeliveryRemarks,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                                if (isRejected == 1) ...[
                                  //Reason For Not Delivery
                                  LabeledTextField(
                                    controller:
                                        existingSiteLeadReasonForNotDeliveryController,
                                    hintText: 'Reason For Not Delivery',
                                    label: 'Reason For Not Delivery',
                                    keyboardType: TextInputType.number,
                                    isEditable: false,
                                    initialValue:
                                        existingSiteLeadReasonForNotDelivery,
                                    isMandatory: true,
                                  ),
                                  SizedBox(height: 7),
                                ],
                              ],
                              // Remarks
                              LabeledTextField(
                                controller: existingSiteLeadRemarksController,
                                hintText: 'Remarks',
                                label: 'Remarks',
                                keyboardType: TextInputType.text,
                                isEditable: false,
                                initialValue: existingSiteLeadRemarks,
                                isMandatory: false,
                              ),
                              SizedBox(height: 7),
                              //Site Status
                              SelectButtonWithLabel(
                                buttonLabel: 'Site Status',
                                onPressed: () {
                                  showSelectorDialog<SiteStatusList>(
                                    fetchData: () =>
                                        SiteStatusList.fetchDataFromApi(),
                                    dialogTitle: 'Select Site Status',
                                    getDisplayText: (item) => item.title ?? '',
                                    enableSearch: true,
                                    onSelected: (item) {
                                      setState(() {
                                        existingSiteLeadSiteStatus = item.title;
                                      });
                                    },
                                  );
                                },
                                value: existingSiteLeadSiteStatus,
                                isMandatory: true,
                                isEnabled: _leadCategory == 3
                                    ? !_isNonEditable
                                    : !isNonEditable,
                              ),
                              SizedBox(height: 7),
                            ],
                          ],
                          if (_userType!.toLowerCase() == 'asm') ...[
                            // Transaction Id
                            AsmTextField(
                                title: "Transaction I'd",
                                value: transactionId ?? "",
                                isShow: true),
                            // Unique Site Id
                            AsmTextField(
                                title: "Unique Site I'd",
                                value: uniqueSiteId ?? "",
                                isShow: true),
                            // Creation Date
                            AsmTextField(
                                title: "Creation Date",
                                value: creationDate ?? "",
                                isShow: true),
                            // Visit Date
                            AsmTextField(
                                title: "Visit Date",
                                value: visitDate ?? "",
                                isShow: true),
                            // Employee Code
                            AsmTextField(
                                title: "Employee Code",
                                value: employeeCode ?? "",
                                isShow: true),
                            // Employee Name
                            AsmTextField(
                                title: "Employee Name",
                                value: employeeName ?? "",
                                isShow: true),
                            // Zone
                            AsmTextField(
                                title: "Zone", value: zone ?? "", isShow: true),
                            // State
                            AsmTextField(
                                title: "State",
                                value: state ?? "",
                                isShow: true),
                            // Branch
                            AsmTextField(
                                title: "Branch",
                                value: branch ?? "",
                                isShow: true),
                            // District
                            AsmTextField(
                                title: "District",
                                value: district ?? "",
                                isShow: true),
                            // Latitude
                            AsmTextField(
                                title: "Latitude",
                                value: latitude ?? "",
                                isShow: true),
                            // Longitude
                            AsmTextField(
                                title: "Longitude",
                                value: longitude ?? "",
                                isShow: true),
                            // Customer Name
                            AsmTextField(
                                title: "Customer Name",
                                value: customerName ?? "",
                                isShow: true),
                            // Customer Contact Number
                            AsmTextField(
                                title: "Customer Contact Number",
                                value: customerContactNumber ?? "",
                                isShow: true),
                            // Full Address
                            AsmTextField(
                                title: "Full Address",
                                value: fullAddress ?? "",
                                isShow: true),
                            // Petty Contractor Regd. In Star Link
                            AsmTextField(
                                title: "Petty Contractor Regd. In Star Link",
                                value: pettyContractorRegdInStarLink ?? "",
                                isShow: true),
                            // Petty Contractor - Head Mason Name
                            AsmTextField(
                                title: "Petty Contractor - Head Mason Name",
                                value: pettyContractorHeadMasonName ?? "",
                                isShow: true),
                            // Petty Contractor - Head Mason Contact Number
                            AsmTextField(
                                title:
                                    "Petty Contractor - Head Mason Contact Number",
                                value:
                                    pettyContractorHeadMasonContactNumber ?? "",
                                isShow: true),
                            // Engineer Regd. In Star Stellar
                            AsmTextField(
                                title: "Engineer Regd. In Star Stellar",
                                value: engineerRegdInStarStellar ?? "",
                                isShow: true),
                            // Engineer Name
                            AsmTextField(
                                title: "Engineer Name",
                                value: engineerName ?? "",
                                isShow: true),
                            // Engineer Contact Number
                            AsmTextField(
                                title: "Engineer Contact Number",
                                value: engineerContactNumber ?? "",
                                isShow: true),
                            // Meeting Person
                            AsmTextField(
                                title: "Meeting Person",
                                value: meetingPerson ?? "",
                                isShow: true),
                            // Decision Maker
                            AsmTextField(
                                title: "Decision Maker",
                                value: decisionMaker ?? "",
                                isShow: true),
                            // Site Segment
                            AsmTextField(
                                title: "Site Segment",
                                value: siteSegment ?? "",
                                isShow: true),
                            // Visit Type
                            AsmTextField(
                                title: "Visit Type",
                                value: visitType ?? "",
                                isShow: true),
                            // Project Segment
                            AsmTextField(
                                title: "Project Segment",
                                value: projectSegment ?? "",
                                isShow: true),
                            // Type of Construction
                            AsmTextField(
                                title: "Type of Construction",
                                value: typeofConstruction ?? "",
                                isShow: true),
                            // Floor Count
                            AsmTextField(
                                title: "Number of Floor",
                                value: numberOfFloor ?? "",
                                isShow: true),
                            // Current Stage of Construction
                            AsmTextField(
                                title: "Current Stage of Construction",
                                value: currentStageOfConstruction ?? "",
                                isShow: true),
                            // Built-Up Area
                            AsmTextField(
                                title: "Built-Up Area",
                                value: builtUpArea ?? "",
                                isShow: true),
                            // Site Potential
                            AsmTextField(
                                title: "Site Potential",
                                value: sitePotential ?? "",
                                isShow: true),
                            // Consumed Till Date
                            AsmTextField(
                                title: "Consumed Till Date",
                                value: consumedTillDate ?? "",
                                isShow: true),
                            // Balance Potential
                            AsmTextField(
                                title: "Balance Potential",
                                value: balancePotential ?? "",
                                isShow: true),
                            // Balance Potential manual
                            AsmTextField(
                                title: "Balance Potential manual",
                                value: balancePotentialManual ?? "",
                                isShow: true),
                            // Site Category
                            AsmTextField(
                                title: "Site Category",
                                value: siteCategory ?? "",
                                isShow: true),
                            // Brand Used
                            AsmTextField(
                                title: "Brand Used",
                                value: brandUsed ?? "",
                                isShow: true),
                            // Price Per Bags
                            AsmTextField(
                                title: "Price Per Bags",
                                value: pricePerBags ?? "",
                                isShow: true),
                            // Conversion
                            AsmTextField(
                                title: "Business Generation",
                                value: conversion ?? "",
                                isShow: true),
                            // Select Product
                            AsmTextField(
                                title: "Select Product",
                                value: selectProduct ?? "",
                                isShow: true),
                            // No. of Bag Ordered
                            AsmTextField(
                                title: "No. of Bag Ordered",
                                value: noOfBagOrdered ?? "",
                                isShow: true),
                            // Requested Date of Delivery
                            AsmTextField(
                                title: "Requested Date of Delivery",
                                value: requestedDateOfDelivery ?? "",
                                isShow: true),
                            // Counter Type
                            AsmTextField(
                                title: "Counter Type",
                                value: counterType ?? "",
                                isShow: true),
                            // Counter Name
                            AsmTextField(
                                title: "Counter Name",
                                value: counterName ?? "",
                                isShow: true),
                            // Counter Code
                            AsmTextField(
                                title: "Counter Code",
                                value: counterCode ?? "",
                                isShow: true),
                            // Reason for Non-Conversion
                            AsmTextField(
                                title: "Reason for Non-Conversion",
                                value: reasonForNonConversion ?? "",
                                isShow: true),
                            // Site Priority
                            AsmTextField(
                                title: "Site Priority",
                                value: sitePriority ?? "",
                                isShow: true),
                            // Weather Shield Demo
                            AsmTextField(
                                title: "Weather Shield Demo",
                                value: weatherShieldDemo ?? "",
                                isShow: true),
                            // ASM Name
                            AsmTextField(
                                title: "ASM Name",
                                value: asmName ?? "",
                                isShow: true),
                            // ASM Employee Code
                            AsmTextField(
                                title: "ASM Employee Code",
                                value: asmEmployeeCode ?? "",
                                isShow: true),
                            // Remarks
                            AsmTextField(
                                title: "Remarks",
                                value: remarks ?? "",
                                isShow: true),
                            // Site Status
                            AsmTextField(
                                title: "Site Status",
                                value: siteStatus ?? "",
                                isShow: true),
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
                            if (_userType!.toLowerCase() == 'asm') {
                              _showApprovalDialog();
                            }
                            if (_userType!.toLowerCase() == 'so') {
                              if (_leadCategory == 1) {
                                _checkNewLeadInformation();
                              } else {
                                _checkExistingLeadInformation();
                              }
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

class AsmTextField extends StatelessWidget {
  final String title;
  final String value;
  final bool isShow;

  const AsmTextField({
    super.key,
    required this.title,
    required this.value,
    this.isShow = true,
  });

  @override
  Widget build(BuildContext context) {
    if (!isShow) return const SizedBox();
    return Container(
      decoration: BoxDecoration(
          border: Border.all(color: const Color.fromARGB(255, 0, 0, 0))),
      child: IntrinsicHeight(
        child: Row(
          children: [
            Expanded(
              flex: 1,
              child: Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                child: Text(
                  title,
                  style: const TextStyle(fontSize: 14),
                ),
              ),
            ),
            Container(
              width: 1,
              color: Colors.black,
            ),
            Expanded(
              flex: 1,
              child: Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                child: Text(
                  value,
                  style: const TextStyle(fontSize: 14),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ***Pre-define API***
//State
class StateNameList {
  String? stateName;
  String? stateCode;

  StateNameList({
    this.stateName,
    this.stateCode,
  });

  factory StateNameList.fromLine(String line) {
    return StateNameList(
      stateName: line.split('^')[0],
      stateCode: line.split('^')[0],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'branch_code': stateCode,
      'branch_name': stateName,
    };
  }

  static Future<List<StateNameList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_state_list_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => StateNameList.fromLine(line))
          .toList()
        ..sort((a, b) => (a.stateName ?? '').compareTo(b.stateName ?? ''));

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Branch
class BranchNameList {
  String? branchCode;
  String? branchName;

  BranchNameList({
    this.branchCode,
    this.branchName,
  });

  factory BranchNameList.fromLine(String line) {
    return BranchNameList(
      branchCode: line.split('^')[0],
      branchName: line.split('^')[1],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'branch_code': branchCode,
      'branch_name': branchName,
    };
  }

  static Future<List<BranchNameList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse("${AppWebService.baseURL}misreport/api_branch_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => BranchNameList.fromLine(line))
          .toList()
        ..sort((a, b) => (a.branchName ?? '').compareTo(b.branchName ?? ''));

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Districts
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
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse("${AppWebService.baseURL}misreport/api_district_site_lead.php"),
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
          .toList()
        ..sort(
            (a, b) => (a.districtsName ?? '').compareTo(b.districtsName ?? ''));

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Petty Contractor Regd In Star Link
class PettyContractorRegdInStarLinkList {
  String? title;
  String? value;

  PettyContractorRegdInStarLinkList({
    this.title,
    this.value,
  });

  factory PettyContractorRegdInStarLinkList.fromLine(String line) {
    return PettyContractorRegdInStarLinkList(
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

  static Future<List<PettyContractorRegdInStarLinkList>>
      fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_petty_contractor_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => PettyContractorRegdInStarLinkList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Petty Contractor
class PettyContractorList {
  String? code;
  String? name;
  String? contactNumber;

  PettyContractorList({
    this.code,
    this.name,
    this.contactNumber,
  });

  factory PettyContractorList.fromLine(String line) {
    return PettyContractorList(
      code: line.split('^')[0],
      name: line.split('^')[1],
      contactNumber: line.split('^')[2],
    );
  }

  Map<String, dynamic> toJson() {
    return {'name': name, 'code': code, 'contactNumber': contactNumber};
  }

  static Future<List<PettyContractorList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_star_link_contractor_site_lead.php?emp_code=${user?.empCode}"),
    );
    // final response = await ioClient.get(
    //   Uri.parse(
    //       "${AppWebService.baseURL}misreport/api_star_link_contractor_site_lead.php?emp_code=E1993"),
    // );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => PettyContractorList.fromLine(line))
          .toList()
        ..sort((a, b) => (a.name ?? '').compareTo(b.name ?? ''));

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Engineer Regd In Star Stellar
class EngineerRegdInStarStellarList {
  String? title;
  String? value;

  EngineerRegdInStarStellarList({
    this.title,
    this.value,
  });

  factory EngineerRegdInStarStellarList.fromLine(String line) {
    return EngineerRegdInStarStellarList(
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

  static Future<List<EngineerRegdInStarStellarList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_engg_registered_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => EngineerRegdInStarStellarList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Engineer
class EngineerList {
  String? code;
  String? name;
  String? contactNumber;

  EngineerList({
    this.code,
    this.name,
    this.contactNumber,
  });

  factory EngineerList.fromLine(String line) {
    return EngineerList(
      code: line.split('^')[0],
      name: line.split('^')[1],
      contactNumber: line.split('^')[2],
    );
  }

  Map<String, dynamic> toJson() {
    return {'name': name, 'code': code, 'contactNumber': contactNumber};
  }

  static Future<List<EngineerList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_star_stellar_engg_site_lead.php?emp_code=${user?.empCode}"),
    );
    // final response = await ioClient.get(
    //   Uri.parse(
    //       "${AppWebService.baseURL}misreport/api_star_stellar_engg_site_lead.php?emp_code=E1993"),
    // );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => EngineerList.fromLine(line))
          .toList()
        ..sort((a, b) => (a.name ?? '').compareTo(b.name ?? ''));

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Meeting Person
class MeetingPersonList {
  String? title;
  String? value;

  MeetingPersonList({
    this.title,
    this.value,
  });

  factory MeetingPersonList.fromLine(String line) {
    return MeetingPersonList(
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

  static Future<List<MeetingPersonList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_meeting_person_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => MeetingPersonList.fromLine(line))
          .toList()
        ..sort((a, b) {
          if (a.title == "Other") return 1;
          if (b.title == "Other") return -1;
          return (a.title ?? '').compareTo(b.title ?? '');
        });

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Decision Maker
class DecisionMakerList {
  String? title;
  String? value;

  DecisionMakerList({
    this.title,
    this.value,
  });

  factory DecisionMakerList.fromLine(String line) {
    return DecisionMakerList(
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

  static Future<List<DecisionMakerList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_decision_maker_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => DecisionMakerList.fromLine(line))
          .toList()
        ..sort((a, b) {
          if (a.title == "Other") return 1;
          if (b.title == "Other") return -1;
          return (a.title ?? '').compareTo(b.title ?? '');
        });

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Site Segment
class SiteSegmentList {
  String? title;
  String? value;

  SiteSegmentList({
    this.title,
    this.value,
  });

  factory SiteSegmentList.fromLine(String line) {
    return SiteSegmentList(
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

  static Future<List<SiteSegmentList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_site_segment_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => SiteSegmentList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Visit Type
class VisitTypeList {
  String? title;
  String? value;

  VisitTypeList({
    this.title,
    this.value,
  });

  factory VisitTypeList.fromLine(String line) {
    return VisitTypeList(
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

  static Future<List<VisitTypeList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_visit_type_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => VisitTypeList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Project Segment
class ProjectSegmentList {
  String? title;
  String? value;

  ProjectSegmentList({
    this.title,
    this.value,
  });

  factory ProjectSegmentList.fromLine(String line) {
    return ProjectSegmentList(
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

  static Future<List<ProjectSegmentList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_project_segment_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => ProjectSegmentList.fromLine(line))
          .toList()
        ..sort((a, b) {
          if (a.title == "Other") return 1;
          if (b.title == "Other") return -1;
          return (a.title ?? '').compareTo(b.title ?? '');
        });

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Type of Construction
class TypeOfConstructionList {
  String? title;
  String? value;

  TypeOfConstructionList({
    this.title,
    this.value,
  });

  factory TypeOfConstructionList.fromLine(String line) {
    return TypeOfConstructionList(
      title: line.split('^')[0],
      value: line.split('^')[1],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'value': value,
      'title': title,
    };
  }

  static Future<List<TypeOfConstructionList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_construction_category_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => TypeOfConstructionList.fromLine(line))
          .toList()
        ..sort((a, b) {
          if (a.title == "Other") return 1;
          if (b.title == "Other") return -1;
          return (a.title ?? '').compareTo(b.title ?? '');
        });

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Floor Count
class FloorCountList {
  String? title;
  String? value;

  FloorCountList({
    this.title,
    this.value,
  });

  factory FloorCountList.fromLine(String line) {
    return FloorCountList(
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

  static Future<List<FloorCountList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse("${AppWebService.baseURL}misreport/api_floor_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => FloorCountList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Current Stage of Construction
class CurrentStageOfConstructionList {
  String? title;
  String? value;

  CurrentStageOfConstructionList({
    this.title,
    this.value,
  });

  factory CurrentStageOfConstructionList.fromLine(String line) {
    return CurrentStageOfConstructionList(
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

  static Future<List<CurrentStageOfConstructionList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_current_stage_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => CurrentStageOfConstructionList.fromLine(line))
          .toList()
        ..sort((a, b) {
          if (a.title == "Other") return 1;
          if (b.title == "Other") return -1;
          return (a.title ?? '').compareTo(b.title ?? '');
        });

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Brand Used
class BrandUsedList {
  String? title;
  String? value;

  BrandUsedList({
    this.title,
    this.value,
  });

  factory BrandUsedList.fromLine(String line) {
    return BrandUsedList(
      title: line.split('^')[0],
      value: line.split('^')[1],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'value': value,
      'title': title,
    };
  }

  static Future<List<BrandUsedList>> fetchDataFromApi(String type) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_brand_used_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => BrandUsedList.fromLine(line))
          .where((item) => item.value?.trim() == type.trim())
          .toList()
        ..sort((a, b) {
          if (a.title == "Other") return 1;
          if (b.title == "Other") return -1;
          return (a.title ?? '').compareTo(b.title ?? '');
        });

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

class ExistingBrandUsedList {
  String? title;
  String? value;

  ExistingBrandUsedList({
    this.title,
    this.value,
  });

  factory ExistingBrandUsedList.fromLine(String line) {
    print(line);
    return ExistingBrandUsedList(
      title: line.split('^')[0],
      value: line.split('^')[1],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'value': value,
      'title': title,
    };
  }

  static Future<List<ExistingBrandUsedList>> fetchDataFromApi(
      int category) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_brand_used_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) =>
              line.trim().isNotEmpty &&
              !line.contains("¥") &&
              (category == 3
                  ? line.split('^')[1].toLowerCase().contains("non star site")
                  : true))
          .map((line) => ExistingBrandUsedList.fromLine(line))
          .toList();

      final starSiteList = <ExistingBrandUsedList>[];
      final otherList = <ExistingBrandUsedList>[];
      for (var item in employees) {
        if ((item.value ?? "").toUpperCase() == "STAR SITE") {
          starSiteList.add(item);
        } else {
          otherList.add(item);
        }
      }
      otherList.sort((a, b) {
        if (a.title == "Other") return 1;
        if (b.title == "Other") return -1;
        return (a.title ?? '').compareTo(b.title ?? '');
      });
      final finalList = [...starSiteList, ...otherList];
      return finalList;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Conversion
class ConversionList {
  String? title;
  String? value;

  ConversionList({
    this.title,
    this.value,
  });

  factory ConversionList.fromLine(String line) {
    return ConversionList(
      title: line.split('^')[0],
      value: line.split('^')[1],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'value': value,
      'title': title,
    };
  }

  static Future<List<ConversionList>> fetchDataFromApi(String type) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_conversion_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => ConversionList.fromLine(line))
          .where((item) => item.value?.trim() == type.trim())
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Product
class ProductList {
  String? title;
  String? value;
  String? value1;

  ProductList({
    this.title,
    this.value,
    this.value1,
  });

  factory ProductList.fromLine(String line) {
    return ProductList(
      title: line.split('^')[0],
      value: line.split('^')[2],
      value1: line.split('^')[1],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'value': value,
      'value1': value1,
      'title': title,
    };
  }

  static Future<List<ProductList>> fetchDataFromApi(
      String visitTypeValue, String conversion) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_select_product_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => ProductList.fromLine(line))
          .where((item) =>
              item.value?.trim() == visitTypeValue.trim() &&
              item.value1?.trim() == conversion.trim())
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Counter Type
class CounterTypeList {
  String? title;
  String? value;

  CounterTypeList({
    this.title,
    this.value,
  });

  factory CounterTypeList.fromLine(String line) {
    return CounterTypeList(
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

  static Future<List<CounterTypeList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_counter_type_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => CounterTypeList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Counter Name
class CounterNameList {
  String? type;
  String? name;
  String? code;

  CounterNameList({
    this.type,
    this.name,
    this.code,
  });

  factory CounterNameList.fromLine(String line) {
    return CounterNameList(
      type: line.split('^')[2],
      name: line.split('^')[1],
      code: line.split('^')[0],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'type': type,
      'name': name,
      'code': code,
    };
  }

  static Future<List<CounterNameList>> fetchDataFromApi(String type) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();
    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_counter_name_site_lead.php?emp_code=${user?.empCode}"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => CounterNameList.fromLine(line))
          .where((item) => item.type?.trim() == type.trim())
          .toList()
        ..sort((a, b) {
          if (a.name == "Other") return 1;
          if (b.name == "Other") return -1;
          return (a.name ?? '').compareTo(b.name ?? '');
        });

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Non Converted Reason
class NonConvertedReasonList {
  String? title;
  String? value;

  NonConvertedReasonList({
    this.title,
    this.value,
  });

  factory NonConvertedReasonList.fromLine(String line) {
    return NonConvertedReasonList(
      title: line.split('^')[0],
      value: line.split('^')[0],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'title': title,
      'value': value,
    };
  }

  static Future<List<NonConvertedReasonList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_non_conversion_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => NonConvertedReasonList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Site Priority
class SitePriorityList {
  String? title;
  String? value;

  SitePriorityList({
    this.title,
    this.value,
  });

  factory SitePriorityList.fromLine(String line) {
    return SitePriorityList(
      title: line.split('^')[0],
      value: line.split('^')[0],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'title': title,
      'value': value,
    };
  }

  static Future<List<SitePriorityList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_site_priority_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => SitePriorityList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Weather Shield Demo
class WeatherShieldDemoList {
  String? title;
  String? value;

  WeatherShieldDemoList({
    this.title,
    this.value,
  });

  factory WeatherShieldDemoList.fromLine(String line) {
    return WeatherShieldDemoList(
      title: line.split('^')[0],
      value: line.split('^')[0],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'title': title,
      'value': value,
    };
  }

  static Future<List<WeatherShieldDemoList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_weather_shield_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => WeatherShieldDemoList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//ASM Name
class AsmNameList {
  String? name;
  String? code;

  AsmNameList({
    this.name,
    this.code,
  });

  factory AsmNameList.fromLine(String line) {
    return AsmNameList(
      name: line.split('^')[1],
      code: line.split('^')[0],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'name': name,
      'code': code,
    };
  }

  static Future<List<AsmNameList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_asm_site_lead.php?emp_code=${user?.empCode}"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => AsmNameList.fromLine(line))
          .toList()
        ..sort((a, b) {
          if (a.name == "Other") return 1;
          if (b.name == "Other") return -1;
          return (a.name ?? '').compareTo(b.name ?? '');
        });

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Site Status
class SiteStatusList {
  String? title;
  String? value;

  SiteStatusList({
    this.title,
    this.value,
  });

  factory SiteStatusList.fromLine(String line) {
    return SiteStatusList(
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

  static Future<List<SiteStatusList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_site_status_site_lead.php"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) => line.trim().isNotEmpty && !line.contains("¥"))
          .map((line) => SiteStatusList.fromLine(line))
          .toList();

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Existing Site Lead
class SiteLeadDataList {
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
  String? remarks;

  SiteLeadDataList({
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

  static Future<List<SiteLeadDataList>> fetchDataFromApi(
      int leadCategory) async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_get_site_list_site_lead.php?emp_code=${user?.empCode}"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');
      print('Length : ${lines.length}');
      final employees = lines
          .where((line) =>
              line.trim().isNotEmpty &&
              !line.contains("¥") &&
              !line.contains("#") &&
              (leadCategory == 3
                  ? line
                          .split('^')[52]
                          .toString()
                          .toLowerCase()
                          .contains("approved") &&
                      line.split('^')[16].toString().toLowerCase() ==
                          "star site"
                  : true))
          .map((line) => SiteLeadDataList.fromLine(line))
          .toList();

      // ignore: avoid_print
      print('Length : ${lines.length}');

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
  }
}

//Existing Site Lead
class ExistingSiteLeadDataList {
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
  String? remarks;

  ExistingSiteLeadDataList({
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
    this.remarks,
  });

  factory ExistingSiteLeadDataList.fromLine(String line) {
    return ExistingSiteLeadDataList(
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

  static Future<List<ExistingSiteLeadDataList>> fetchDataFromApi() async {
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "sfa.starcement.co.in"; // allow this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();

    final response = await ioClient.get(
      Uri.parse(
          "${AppWebService.baseURL}misreport/api_get_asm_reqst_site_lead.php?asm_id=${user?.empCode}"),
    );

    if (response.statusCode == 200) {
      final body = utf8.decode(response.bodyBytes);

      final lines = body.split('\n');

      final employees = lines
          .where((line) =>
              line.trim().isNotEmpty &&
              !line.contains("¥") &&
              !line.contains("#"))
          .map((line) => ExistingSiteLeadDataList.fromLine(line))
          .toList();

      // ignore: avoid_print
      print('Length : ${employees.length}');

      return employees;
    } else {
      throw Exception('Failed to fetch employee list');
    }
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
