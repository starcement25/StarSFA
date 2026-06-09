import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:http/io_client.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/determine_position.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class SiteLeadConversionTrackingScreen extends StatefulWidget {
  final bool isOptionSelected;
  const SiteLeadConversionTrackingScreen(
      {super.key, this.isOptionSelected = false});

  @override
  State<SiteLeadConversionTrackingScreen> createState() =>
      _SiteLeadConversionTrackingScreen();
}

class _SiteLeadConversionTrackingScreen
    extends State<SiteLeadConversionTrackingScreen> {
  StateInfo? stateInfo;
  DistrictInfo? districtInfo;
  ProjectSegmentInfo? projectSegmentInfo;
  TypeOfConstructionInfo? typeOfConstructionInfo;
  CurrentStageOfConstructionInfo? currentStageOfConstructionInfo;
  CementBrandInfo? cementBrandInfo;
  DecisionMakerInfo? decisionMakerInfo;
  ProductDemoInfo? productDemoInfo;
  VisitTypeInfo? visitTypeInfo;
  VisitTypeNonStarInfo? visitTypeNonStarInfo;
  VisitTypeStarInfo? visitTypeStarInfo;
  MeetUpPersonInfo? meetUpPersonInfo;
  BranchInfo? branchInfo;
  ApprovedByInfo? approvedByInfo;
  RssdDealerInfo? rssdDealerInfo;
  SiteSegmentInfo? siteSegmentInfo;
  RouteNameEmployeeInfo? routeNameEmployeeInfo;
  RouteNameCustomerInfo? routeNameCustomerInfo;

  TextEditingController contactController = TextEditingController();

  bool isLoading = true;
  bool isMainLoading = false;
  bool isNewAdd = true;
  bool isOpenCementBrandEditText = false;

  String? empCode, lat, lng;

  String? customerName,
      meetingPersonPhoneNumber,
      siteName,
      customerContactNumber,
      fullAddress,
      contactorName,
      contactorPhoneNumber,
      engineerName,
      engineerPhoneNumber,
      sitePotential,
      otherCementBrandName,
      pricePerBag,
      consumedTillDate,
      estimatedRequirement,
      buildUpArea,
      remarks,
      noOdBagOrder;

  String? selectedRouteCode,
      selectedRouteName,
      selectedUpdateRouteCode,
      selectedUpdateRouteName,
      selectMeetingPerson,
      selectMeetingPersonCode,
      selectBranch,
      selectBranchCode,
      selectState,
      selectStateCode,
      selectDistrict,
      selectDistrictCode,
      selectRegInStellar,
      selectRegInStellarCode,
      selectSiteSegment,
      selectSiteSegmentCode,
      selectProjectSegment,
      selectProjectSegmentCode,
      selectTypeOfConstruction,
      selectTypeOfConstructionCode,
      selectConstructionStatus,
      selectConstructionStatusCode,
      selectCementBrand,
      selectCementBrandCode,
      selectDecisionMaker,
      selectDecisionMakerCode,
      selectProductDemo,
      selectProductDemoCode,
      selectVisitType,
      selectVisitTypeCode,
      selectVisitSubType,
      selectVisitSubTypeCode,
      selectDeliveryDate,
      selectDeliveryDateCode,
      selectDealer,
      selectDealerCode,
      selectApprovedBy,
      selectApprovedByCode,
      selectSiteName,
      selectSiteCode;

  String? showVisitType;

  bool? isAlreadyRegisterInOtherSite,
      isNewCustomer,
      isAlreadyRegisterInSameSite;

  TextEditingController customerNameController = TextEditingController();
  TextEditingController meetingPersonPhoneNumberController =
      TextEditingController();
  TextEditingController siteNameController = TextEditingController();
  TextEditingController customerContactNumberController =
      TextEditingController();
  TextEditingController fullAddressController = TextEditingController();
  TextEditingController contactorNameController = TextEditingController();
  TextEditingController contactorPhoneNoController = TextEditingController();
  TextEditingController engineerNameController = TextEditingController();
  TextEditingController engineerPhoneNoController = TextEditingController();
  TextEditingController sitePotentialController = TextEditingController();
  TextEditingController otherCementBrandNameController =
      TextEditingController();
  TextEditingController pricePerBagController = TextEditingController();
  TextEditingController consumedTillDateController = TextEditingController();
  TextEditingController estimatedRequirementController =
      TextEditingController();
  TextEditingController buildUpAreaController = TextEditingController();
  TextEditingController remarksController = TextEditingController();
  TextEditingController noOdBagOrderController = TextEditingController();

  @override
  void initState() {
    super.initState();
    locationGet();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _showFirstDialog();
    });
  }

  void locationGet() async {
    final user = await UserLoginClass.getLocalUser() ?? UserLoginClass();
    final location = await DeterminePosition.getPosition(null, null, null);

    empCode = user.empCode;
    lat = location.latitude.toString();
    lng = location.longitude.toString();
  }

  // void _showFirstDialog() {
  //   bool isSubmitting = false;
  //   showDialog(
  //     context: context,
  //     barrierDismissible: false,
  //     builder: (BuildContext context) {
  //       return StatefulBuilder(
  //         builder: (context, setState) {
  //           return AlertDialog(
  //             title: Text('Enter Initial Details'),
  //             content: SingleChildScrollView(
  //               child: Column(
  //                 mainAxisSize: MainAxisSize.min,
  //                 crossAxisAlignment: CrossAxisAlignment.start,
  //                 children: [
  //                   const Text(
  //                     'Route Name *',
  //                     style: TextStyle(fontWeight: FontWeight.bold),
  //                   ),
  //                   const SizedBox(height: 8),
  //                   SizedBox(
  //                     width: double.infinity,
  //                     child: OutlinedButton(
  //                       onPressed: () {
  //                         showSelectorDialog<RouteNameEmployeeInfo>(
  //                           fetchData: () =>
  //                               RouteNameEmployeeInfo.fetchDataFromApi(),
  //                           dialogTitle: 'Select Employee Route',
  //                           getDisplayText: (item) => item.routeName ?? '',
  //                           onSelected: (item) {
  //                             setState(() {
  //                               selectedRouteCode = item.routeCode!;
  //                               selectedRouteName = item.routeName!;
  //                             });
  //                           },
  //                         );
  //                       },
  //                       style: OutlinedButton.styleFrom(
  //                         padding: const EdgeInsets.symmetric(vertical: 16),
  //                         side: const BorderSide(color: Colors.grey),
  //                         backgroundColor: Colors.white,
  //                         shape: RoundedRectangleBorder(
  //                           borderRadius: BorderRadius.circular(4),
  //                         ),
  //                       ),
  //                       child: const Text(
  //                         'Select Route',
  //                         style: TextStyle(fontSize: 16, color: Colors.black),
  //                       ),
  //                     ),
  //                   ),
  //                   const SizedBox(height: 8),
  //                   if ((selectedRouteName ?? '').trim().isNotEmpty)
  //                     Text(
  //                       selectedRouteName!,
  //                       style: const TextStyle(
  //                         fontWeight: FontWeight.bold,
  //                         color: Color.fromARGB(255, 255, 166, 0),
  //                       ),
  //                     ),
  //                   const SizedBox(height: 16),
  //                   const Text(
  //                     'Customer Contact No. *',
  //                     style: TextStyle(fontWeight: FontWeight.bold),
  //                   ),
  //                   const SizedBox(height: 8),
  //                   TextField(
  //                     controller: contactController,
  //                     keyboardType: TextInputType.phone,
  //                     decoration: const InputDecoration(
  //                       hintText: 'Enter phone number',
  //                       border: OutlineInputBorder(),
  //                     ),
  //                   ),
  //                 ],
  //               ),
  //             ),
  //             actions: [
  //               TextButton(
  //                 onPressed: () {
  //                   Navigator.of(context).pop(); // Close dialog
  //                   Navigator.of(context)
  //                       .maybePop(); // Go back to previous page
  //                 },
  //                 child: const Text('Back'),
  //               ),
  //               isSubmitting
  //                   ? const Padding(
  //                       padding: EdgeInsets.all(8.0),
  //                       child: SizedBox(
  //                         width: 24,
  //                         height: 24,
  //                         child: CircularProgressIndicator(strokeWidth: 2),
  //                       ),
  //                     )
  //                   : TextButton(
  //                       onPressed: () async {
  //                         if (contactController.text.trim().isEmpty) {
  //                           ScaffoldMessenger.of(context).showSnackBar(
  //                             const SnackBar(
  //                                 content: Text(
  //                                     "Please enter customer contact number")),
  //                           );
  //                           return;
  //                         } else if (!RegExp(r'^\d{10}$')
  //                             .hasMatch(contactController.text.trim())) {
  //                           ScaffoldMessenger.of(context).showSnackBar(
  //                             const SnackBar(
  //                                 content: Text(
  //                                     "Please enter valid 10 digit customer contact number")),
  //                           );
  //                           return;
  //                         } else if (selectedRouteCode == null ||
  //                             selectedRouteCode!.isEmpty) {
  //                           ScaffoldMessenger.of(context).showSnackBar(
  //                             const SnackBar(
  //                                 content: Text("Please select route")),
  //                           );
  //                           return;
  //                         } else {
  //                           customerContactNumber =
  //                               contactController.text.trim();
  //                           setState(() {
  //                             isSubmitting = true;
  //                           });

  //                           try {
  //                             final status =
  //                                 await CustomerStatusInfo.fetchCustomerStatus(
  //                               contactController.text.trim(),
  //                               selectedRouteCode!,
  //                             );

  //                             setState(() {
  //                               isAlreadyRegisterInOtherSite =
  //                                   status.isAlreadyRegisteredInOtherRoute;
  //                               isAlreadyRegisterInSameSite =
  //                                   status.isAlreadyRegisteredInSameRoute;
  //                               isNewCustomer = status.isNewCustomer;
  //                             });

  //                             // ignore: avoid_print
//print('Already Registered: $isAlreadyRegisterInOtherSite');
  //                             // ignore: avoid_print
//print('Is New Customer: $isNewCustomer');

  //                             if (isAlreadyRegisterInSameSite == true) {
  //                               Navigator.of(context).pop();
  //                               WidgetsBinding.instance
  //                                   .addPostFrameCallback((_) {
  //                                 _showSecondDialog();
  //                               });
  //                             } else if (isAlreadyRegisterInOtherSite == true) {
  //                               Navigator.of(context).pop();
  //                               WidgetsBinding.instance
  //                                   .addPostFrameCallback((_) {
  //                                 _showThirdDialog();
  //                               });
  //                             } else {
  //                               Navigator.of(context).pop();
  //                             }
  //                           } catch (e) {
  //                             setState(() {
  //                               isSubmitting = false;
  //                             });
  //                             ScaffoldMessenger.of(context).showSnackBar(
  //                               SnackBar(
  //                                   content: Text("Error: ${e.toString()}")),
  //                             );
  //                           }
  //                         }
  //                       },
  //                       child: const Text('Submit'),
  //                     ),
  //             ],
  //           );
  //         },
  //       );
  //     },
  //   );
  // }

  Future<void> _showFirstDialog() async {
    RouteNameEmployeeInfo? selectedItem;

    bool isSubmitting = false;

    await showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setState) {
            return AlertDialog(
              title: Text('Enter Initial Details'),
              content: SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Route Name *',
                        style: TextStyle(fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton(
                        onPressed: () {
                          showSelectorDialog<RouteNameEmployeeInfo>(
                            fetchData: () =>
                                RouteNameEmployeeInfo.fetchDataFromApi(),
                            dialogTitle: 'Select Employee Route',
                            getDisplayText: (item) => item.routeName ?? '',
                            onSelected: (item) {
                              setState(() {
                                selectedItem = item;
                              });
                            },
                          );
                        },
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          side: const BorderSide(color: Colors.grey),
                          backgroundColor: Colors.white,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(4),
                          ),
                        ),
                        child: const Text('Select Route'),
                      ),
                    ),
                    if (selectedItem?.routeName?.isNotEmpty == true)
                      Text(
                        selectedItem!.routeName!,
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Color.fromARGB(255, 255, 166, 0),
                        ),
                      ),
                    const SizedBox(height: 16),
                    const Text('Customer Contact No. *',
                        style: TextStyle(fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    TextField(
                      controller: contactController,
                      keyboardType: TextInputType.phone,
                      maxLength: 10,
                      decoration: const InputDecoration(
                        hintText: 'Enter phone number',
                        border: OutlineInputBorder(),
                      ),
                    ),
                  ],
                ),
              ),
              actions: [
                TextButton(
                  onPressed: () {
                    Navigator.of(context).pop(); // Back
                  },
                  child: const Text('Back'),
                ),
                isSubmitting
                    ? const SizedBox(
                        width: 24,
                        height: 24,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    : TextButton(
                        onPressed: () async {
                          final phone = contactController.text.trim();
                          if (phone.isEmpty ||
                              !RegExp(r'^\d{10}$').hasMatch(phone)) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text(
                                    "Please enter valid 10 digit customer contact number"),
                              ),
                            );
                            return;
                          }
                          if (selectedItem == null) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                content: Text("Please select route"),
                              ),
                            );
                            return;
                          }

                          setState(() {
                            isSubmitting = true;
                          });

                          try {
                            final status =
                                await CustomerStatusInfo.fetchCustomerStatus(
                              phone,
                              selectedItem!.routeCode!,
                            );

                            setState(() {
                              isAlreadyRegisterInOtherSite =
                                  status.isAlreadyRegisteredInOtherRoute;
                              isAlreadyRegisterInSameSite =
                                  status.isAlreadyRegisteredInSameRoute;
                              isNewCustomer = status.isNewCustomer;
                              customerContactNumber =
                                  contactController.text.trim();
                            });

                            // ignore: avoid_print
                            print(
                                'Already Registered: $isAlreadyRegisterInOtherSite');
                            // ignore: avoid_print
                            print('Is New Customer: $isNewCustomer');

                            if (isAlreadyRegisterInSameSite == true) {
                              // Navigator.of(context).pop();
                              WidgetsBinding.instance.addPostFrameCallback((_) {
                                _showSecondDialog();
                              });
                            } else if (isAlreadyRegisterInOtherSite == true) {
                              // Navigator.of(context).pop();
                              WidgetsBinding.instance.addPostFrameCallback((_) {
                                _showThirdDialog();
                              });
                            } else {
                              WidgetsBinding.instance.addPostFrameCallback((_) {
                                _showFifthDialog();
                              });
                              // Navigator.of(context).pop();
                            }

                            // ignore: use_build_context_synchronously
                            Navigator.of(context).pop(selectedItem);
                          } catch (e) {
                            setState(() {
                              isSubmitting = false;
                            });
                            // ignore: use_build_context_synchronously
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(content: Text("Error: $e")),
                            );
                          }
                        },
                        child: const Text('Submit'),
                      ),
              ],
            );
          },
        );
      },
    ).then((selected) {
      if (selected != null) {
        setState(() {
          selectedRouteName = selected.routeName;
          selectedRouteCode = selected.routeCode;
          customerContactNumber = contactController.text.trim();
        });
      }
    });
  }

  void _showSecondDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('+91 ${customerContactNumber ?? ''}'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 16),
                Text(
                  'This phone no has been registered with us with ${selectedRouteName ?? ''} site',
                  style: TextStyle(fontWeight: FontWeight.normal),
                ),
                const SizedBox(height: 22),
                const Text(
                  'Do you want to create a new site using this no.',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 16),
              ],
            ),
          ),
          actions: [
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () {
                      setState(() {
                        isNewAdd = false;
                      });
                      Navigator.of(context).pop();
                    },
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(color: Colors.red),
                    ),
                    child: const Text(
                      'Visit Site',
                      style: TextStyle(color: Colors.red),
                    ),
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: ElevatedButton(
                    onPressed: () {
                      setState(() {
                        isNewAdd = true;
                      });
                      Navigator.of(context).pop();
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.red,
                    ),
                    child: const Text('Create New'),
                  ),
                ),
              ],
            ),
          ],
        );
      },
    );
  }

  void _showThirdDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('+91 ${customerContactNumber ?? ''}'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 16),
                Text(
                  'This phone no has been registered with us with another site',
                  style: TextStyle(fontWeight: FontWeight.normal),
                ),
                const SizedBox(height: 22),
                const Text(
                  'Do you want to create a new site using this no.',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 16),
              ],
            ),
          ),
          actions: [
            Expanded(
              child: OutlinedButton(
                onPressed: () {
                  Navigator.of(context).pop();
                  WidgetsBinding.instance.addPostFrameCallback((_) {
                    _showFourthDialog();
                  });
                },
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: Colors.red),
                ),
                child: const Text(
                  'Visit Other Site',
                  style: TextStyle(color: Colors.red),
                ),
              ),
            ),
            const SizedBox(height: 10),
            Expanded(
              child: ElevatedButton(
                onPressed: () {
                  Navigator.of(context).pop();
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.red,
                ),
                child: const Text('Create New on This Site'),
              ),
            ),
          ],
        );
      },
    );
  }

  void _showFourthDialog() {
    bool isSubmitting = false;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setState) {
            return AlertDialog(
              title: Text('+91 ${customerContactNumber ?? ''}'),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 16),
                    const Text(
                      'Select other register route for update Site Information',
                      style: TextStyle(fontWeight: FontWeight.normal),
                    ),
                    const SizedBox(height: 16),
                    SizedBox(
                      width: double.infinity,
                      child: OutlinedButton(
                        onPressed: () {
                          showSelectorDialog<RouteNameCustomerInfo>(
                            fetchData: () =>
                                RouteNameCustomerInfo.fetchDataFromApi(
                                    customerContactNumber!),
                            dialogTitle: 'Select Customer Route',
                            getDisplayText: (item) => item.routeName ?? '',
                            onSelected: (item) {
                              setState(() {
                                selectedUpdateRouteCode = item.routeCode!;
                                selectedUpdateRouteName = item.routeName!;
                              });
                            },
                          );
                        },
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          side: const BorderSide(color: Colors.grey),
                          backgroundColor: Colors.white,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(4),
                          ),
                        ),
                        child: const Text(
                          'Select Route',
                          style: TextStyle(fontSize: 16, color: Colors.black),
                        ),
                      ),
                    ),
                    const SizedBox(height: 8),
                    if ((selectedUpdateRouteName ?? '').trim().isNotEmpty)
                      Text(
                        selectedUpdateRouteName!,
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Color.fromARGB(255, 255, 166, 0),
                        ),
                      ),
                  ],
                ),
              ),
              actions: [
                isSubmitting
                    ? const Padding(
                        padding: EdgeInsets.all(8.0),
                        child: SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        ),
                      )
                    : TextButton(
                        onPressed: () async {
                          if (selectedUpdateRouteCode == null ||
                              selectedUpdateRouteCode!.isEmpty) {
                            ScaffoldMessenger.of(context).showSnackBar(
                              const SnackBar(
                                  content: Text("Please select Route Name")),
                            );
                            return;
                          } else {
                            setState(() {
                              isSubmitting = true;
                              selectedRouteCode = selectedUpdateRouteCode;
                              selectedRouteName = selectedUpdateRouteName;
                            });

                            try {
                              final status =
                                  await CustomerStatusInfo.fetchCustomerStatus(
                                customerContactNumber!,
                                selectedUpdateRouteCode!,
                              );

                              setState(() {
                                isAlreadyRegisterInOtherSite =
                                    status.isAlreadyRegisteredInOtherRoute;
                                isAlreadyRegisterInSameSite =
                                    status.isAlreadyRegisteredInSameRoute;
                                isNewCustomer = status.isNewCustomer;
                              });

                              // ignore: avoid_print
                              print(
                                  'Already Registered: $isAlreadyRegisterInOtherSite');
                              // ignore: avoid_print
                              print('Is New Customer: $isNewCustomer');

                              if (isAlreadyRegisterInSameSite == true) {
                                // ignore: use_build_context_synchronously
                                Navigator.of(context).pop();
                                WidgetsBinding.instance
                                    .addPostFrameCallback((_) {
                                  _showSecondDialog();
                                });
                              } else if (isAlreadyRegisterInOtherSite == true) {
                                // ignore: use_build_context_synchronously
                                Navigator.of(context).pop();
                                WidgetsBinding.instance
                                    .addPostFrameCallback((_) {
                                  _showThirdDialog();
                                });
                              } else {
                                // ignore: use_build_context_synchronously
                                Navigator.of(context).pop();
                              }
                            } catch (e) {
                              setState(() {
                                isSubmitting = false;
                              });
                              // ignore: use_build_context_synchronously
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                    content: Text("Error: ${e.toString()}")),
                              );
                            }
                          }
                        },
                        child: const Text('Update Existing Site'),
                      ),
              ],
            );
          },
        );
      },
    );
  }

  void _showFifthDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setState) {
            return AlertDialog(
              title: Text('+91 ${customerContactNumber ?? ''}'),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 16),
                    const Text(
                      'Do you want to create a new site using this no.',
                      style: TextStyle(fontWeight: FontWeight.normal),
                    ),
                    const SizedBox(height: 16),
                  ],
                ),
              ),
              actions: [
                TextButton(
                  onPressed: () {
                    Navigator.of(context).pop();
                  },
                  child: const Text('Create New Site'),
                ),
              ],
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

  void _checkDataAndRequestForUploadNewSiteDetails() async {
    if (customerNameController.text.trim().isEmpty) {
      _showSnack("Please enter Customer Name");
      return;
    } else if ((selectMeetingPersonCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Meeting Person");
      return;
    } else if (meetingPersonPhoneNumberController.text.trim().isEmpty) {
      _showSnack("Please enter Meeting Person Contact Number");
      return;
    } else if (meetingPersonPhoneNumberController.text.trim().isNotEmpty &&
        !RegExp(r'^\d{10}$')
            .hasMatch(meetingPersonPhoneNumberController.text.trim())) {
      _showSnack("Please enter valid Meeting Person Contact Number");
      return;
    } else if (siteNameController.text.trim().isEmpty) {
      _showSnack("Please enter Site Name");
      return;
    } else if ((selectBranchCode?.trim() ?? '').isEmpty) {
      _showSnack("Please enter Branch");
      return;
    } else if ((selectStateCode?.trim() ?? '').isEmpty) {
      _showSnack("Please enter State");
      return;
    } else if ((selectDistrict?.trim() ?? '').isEmpty) {
      _showSnack("Please enter District");
      return;
    } else if (fullAddressController.text.trim().isEmpty) {
      _showSnack("Please enter Full Address");
      return;
    } else if ((selectSiteSegmentCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Site Segment");
      return;
    } else if ((selectProjectSegmentCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Project Segment");
      return;
    } else if ((selectTypeOfConstructionCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Type of Construction");
      return;
    } else if ((sitePotentialController.text.trim()).isEmpty) {
      _showSnack("Please enter Site Potential");
      return;
    } else if ((selectConstructionStatusCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Current Stage of Construction");
      return;
    } else if ((selectCementBrandCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Cement Brand");
      return;
    } else if ((estimatedRequirementController.text.trim()).isEmpty) {
      _showSnack("Please enter Estimated Requirement.");
      return;
    } else if ((buildUpAreaController.text.trim()).isEmpty) {
      _showSnack("Please enter Build up Area");
      return;
    } else if ((showVisitType?.trim() ?? '').isEmpty) {
      _showSnack("Please select Visit Types");
      return;
    } else if ((noOdBagOrderController.text.trim()).isEmpty) {
      _showSnack("Please enter No of Bags Order");
      return;
    } else if (int.parse(estimatedRequirementController.text.trim()) >
        int.parse(sitePotentialController.text.trim())) {
      _showSnack("Estimated requirement cannot be more than site potential.");
      return;
    } else if (engineerPhoneNoController.text.trim().isNotEmpty &&
        !RegExp(r'^\d{10}$').hasMatch(engineerPhoneNoController.text.trim())) {
      _showSnack("Please enter valid Engineer Contact Number");
      return;
    } else if (contactorPhoneNoController.text.trim().isNotEmpty &&
        !RegExp(r'^\d{10}$').hasMatch(contactorPhoneNoController.text.trim())) {
      _showSnack("Please enter valid Petty Contractor Number");
      return;
    }

    if (otherCementBrandNameController.text.trim().isNotEmpty) {
      otherCementBrandName = otherCementBrandNameController.text.trim();
    } else {
      otherCementBrandName = selectCementBrandCode!.trim();
    }

    // If all validations pass, build dataset
    Map<String, dynamic> dataSet = {
      "route_code": selectedRouteCode ?? '',
      "cust_name": customerNameController.text.trim(),
      "meeting_person_type": selectMeetingPersonCode?.trim() ?? '',
      "meeting_person_phone": meetingPersonPhoneNumberController.text.trim(),
      "site_name": siteNameController.text.trim(),
      "branch_code": selectBranchCode?.trim() ?? '',
      "state": selectStateCode?.trim() ?? '',
      "district": selectDistrictCode?.trim() ?? '',
      "cust_phone": customerContactNumber?.trim() ?? '',
      "address": fullAddressController.text.trim(),
      "contractor_name": contactorNameController.text.trim(),
      "contractor_phone": contactorPhoneNoController.text.trim(),
      "engineer_name": engineerNameController.text.trim(),
      "engineer_phone": engineerPhoneNoController.text.trim(),
      "engg_reg_star_stellar": selectRegInStellarCode?.trim() ?? '',
      "site_segment": selectSiteSegmentCode?.trim() ?? '',
      "project_segment": selectProjectSegmentCode?.trim() ?? '',
      "type_of_construction": selectTypeOfConstructionCode?.trim() ?? '',
      "site_potential": sitePotentialController.text.trim(),
      "construction_stage": selectConstructionStatusCode?.trim() ?? '',
      "cement_brand": otherCementBrandName?.trim() ?? '',
      "price_per_bag": pricePerBagController.text.trim(),
      "consumed_till_date": consumedTillDateController.text.trim(),
      "estimated_req": estimatedRequirementController.text.trim(),
      "built_up_area": buildUpAreaController.text.trim(),
      "decision_maker": selectDecisionMakerCode?.trim() ?? '',
      "product_demo": selectProductDemoCode?.trim() ?? '',
      "remarks": remarksController.text.trim(),
      "visit_type": selectVisitTypeCode?.trim() ?? '',
      "visit_sub_type": selectVisitSubTypeCode?.trim() ?? '',
      "date_of_delivery": selectDeliveryDateCode?.trim() ?? '',
      "bags_ordered": noOdBagOrderController.text.trim(),
      "rssd": selectDealerCode?.trim() ?? '',
      "approved_by": selectApprovedByCode?.trim() ?? '',
      "latitude": lat?.trim() ?? '',
      "longitude": lng?.trim() ?? '',
      "visited_by": empCode?.trim() ?? '',
    };

    // ignore: avoid_print
    print(jsonEncode(dataSet));

    setState(() {
      isMainLoading = true;
    });
    try {
      final status = await NewSiteUploadInfo.uploadData(dataSet, 'new');
      _showSnack(status.message);

      if (status.process_status == 'Yes') {
        Future.delayed(const Duration(seconds: 1), () {
          // ignore: use_build_context_synchronously
          Navigator.of(context).pop();
        });
      }
    } catch (e) {
      _showSnack("Something went wrong: $e");
    } finally {
      setState(() {
        isMainLoading = false;
      });
    }
  }

  void _checkDataAndRequestForUpdateExistingSiteDetails() async {
    if ((selectMeetingPersonCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Meeting Person");
      return;
    } else if (meetingPersonPhoneNumberController.text.trim().isEmpty) {
      _showSnack("Please enter Meeting Person Contact Number");
      return;
    } else if (meetingPersonPhoneNumberController.text.trim().isNotEmpty &&
        !RegExp(r'^\d{10}$')
            .hasMatch(meetingPersonPhoneNumberController.text.trim())) {
      _showSnack("Please enter valid Meeting Person Contractor Number");
      return;
    } else if (fullAddressController.text.trim().isEmpty) {
      _showSnack("Please enter Customer Full Address");
      return;
    } else if ((selectSiteSegmentCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Site Segment");
      return;
    } else if ((selectProjectSegmentCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Project Segment");
      return;
    } else if ((selectTypeOfConstructionCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Type of Construction");
      return;
    } else if ((sitePotentialController.text.trim()).isEmpty) {
      _showSnack("Please enter Site Potential");
      return;
    } else if ((selectConstructionStatusCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Current Stage of Construction");
      return;
    } else if ((selectCementBrandCode?.trim() ?? '').isEmpty) {
      _showSnack("Please select Current Cement Brand");
      return;
    } else if ((estimatedRequirementController.text.trim()).isEmpty) {
      _showSnack("Please enter Estimated Requirement.");
      return;
    } else if ((buildUpAreaController.text.trim()).isEmpty) {
      _showSnack("Please enter Build up Areas");
      return;
    } else if ((showVisitType?.trim() ?? '').isEmpty) {
      _showSnack("Please select Visit Types");
      return;
    } else if ((noOdBagOrderController.text.trim()).isEmpty) {
      _showSnack("Please enter No of Bags Order");
      return;
    } else if (int.parse(estimatedRequirementController.text.trim()) >
        int.parse(sitePotentialController.text.trim())) {
      _showSnack("Estimated requirement cannot be more than site potential.");
      return;
    } else if (engineerPhoneNoController.text.trim().isNotEmpty &&
        !RegExp(r'^\d{10}$').hasMatch(engineerPhoneNoController.text.trim())) {
      _showSnack("Please enter valid Engineer Contact Number");
      return;
    } else if (contactorPhoneNoController.text.trim().isNotEmpty &&
        !RegExp(r'^\d{10}$').hasMatch(contactorPhoneNoController.text.trim())) {
      _showSnack("Please enter valid Petty Contractor Number");
      return;
    }

    if (otherCementBrandNameController.text.trim().isNotEmpty) {
      otherCementBrandName = otherCementBrandNameController.text.trim();
    } else {
      otherCementBrandName = selectCementBrandCode!.trim();
    }

    // If all validations pass, build dataset
    Map<String, dynamic> dataSet = {
      "route_code": selectedRouteCode ?? '',
      "cust_name": customerNameController.text.trim(),
      "meeting_person_type": selectMeetingPersonCode?.trim() ?? '',
      "meeting_person_phone": meetingPersonPhoneNumberController.text.trim(),
      "site_name": selectSiteName,
      "branch_code": selectBranchCode?.trim() ?? '',
      "state": selectStateCode?.trim() ?? '',
      "district": selectDistrictCode?.trim() ?? '',
      "cust_phone": customerContactNumber?.trim() ?? '',
      "address": fullAddressController.text.trim(),
      "contractor_name": contactorNameController.text.trim(),
      "contractor_phone": contactorPhoneNoController.text.trim(),
      "engineer_name": engineerNameController.text.trim(),
      "engineer_phone": engineerPhoneNoController.text.trim(),
      "engg_reg_star_stellar": selectRegInStellarCode?.trim() ?? '',
      "site_segment": selectSiteSegmentCode?.trim() ?? '',
      "project_segment": selectProjectSegmentCode?.trim() ?? '',
      "type_of_construction": selectTypeOfConstructionCode?.trim() ?? '',
      "site_potential": sitePotentialController.text.trim(),
      "construction_stage": selectConstructionStatusCode?.trim() ?? '',
      "cement_brand": otherCementBrandName?.trim() ?? '',
      "price_per_bag": pricePerBagController.text.trim(),
      "consumed_till_date": consumedTillDateController.text.trim(),
      "estimated_req": estimatedRequirementController.text.trim(),
      "built_up_area": buildUpAreaController.text.trim(),
      "decision_maker": selectDecisionMakerCode?.trim() ?? '',
      "product_demo": selectProductDemoCode?.trim() ?? '',
      "remarks": remarksController.text.trim(),
      "visit_type": selectVisitTypeCode?.trim() ?? '',
      "visit_sub_type": selectVisitSubTypeCode?.trim() ?? '',
      "date_of_delivery": selectDeliveryDateCode?.trim() ?? '',
      "bags_ordered": noOdBagOrderController.text.trim(),
      "rssd": selectDealerCode?.trim() ?? '',
      "approved_by": selectApprovedByCode?.trim() ?? '',
      "latitude": lat?.trim() ?? '',
      "longitude": lng?.trim() ?? '',
      "visited_by": empCode?.trim() ?? '',
    };

    // ignore: avoid_print
    print(jsonEncode(dataSet));

    setState(() {
      isMainLoading = true;
    });
    try {
      final status = await NewSiteUploadInfo.uploadData(dataSet, 'update');
      _showSnack(status.message);

      if (status.process_status == 'Yes') {
        Future.delayed(const Duration(seconds: 1), () {
          // ignore: use_build_context_synchronously
          Navigator.of(context).pop();
        });
      }
    } catch (e) {
      _showSnack("Something went wrong: $e");
    } finally {
      setState(() {
        isMainLoading = false;
      });
    }
  }

  void _showSnack(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
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
                  'Project KHOJ',
                  style: TextStyle(color: Colors.white),
                ),
              ),
              body: SafeArea(
                  child: GestureDetector(
                behavior: HitTestBehavior.opaque,
                onTap: () {
                  FocusScope.of(context).unfocus();
                },
                child: isNewAdd
                    ? Stack(
                        children: [
                          SingleChildScrollView(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                SelectButtonWithLabel(
                                  buttonLabel: 'Route Name *',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Route Name")),
                                    );
                                  },
                                  value: selectedRouteName,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: customerNameController,
                                  hintText: 'Customer Name (Mandatory)',
                                  label: 'Customer Name *',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : customerName,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Meeting Person *',
                                  onPressed: () {
                                    showSelectorDialog<MeetUpPersonInfo>(
                                      fetchData: () =>
                                          MeetUpPersonInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Meet up Person',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectMeetingPerson = item.name!;
                                          selectMeetingPersonCode = item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectMeetingPerson,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller:
                                      meetingPersonPhoneNumberController,
                                  hintText:
                                      'Meeting Person Phone Number (Mandatory)',
                                  label: 'Meeting Person Phone Number *',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue:
                                      isNewAdd ? '' : meetingPersonPhoneNumber,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: siteNameController,
                                  hintText: 'Site Name (Mandatory)',
                                  label: 'Site Name *',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : siteName,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Branch *',
                                  onPressed: () {
                                    showSelectorDialog<BranchInfo>(
                                      fetchData: () =>
                                          BranchInfo.fetchDataFromApi(
                                              selectedRouteCode!),
                                      dialogTitle: 'Select Branch',
                                      getDisplayText: (item) =>
                                          item.branchName ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectBranch = item.branchName!;
                                          selectBranchCode = item.branchCode!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectBranch,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'State *',
                                  onPressed: () {
                                    showSelectorDialog<StateInfo>(
                                      fetchData: () =>
                                          StateInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select State',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectState = item.name!;
                                          selectStateCode = item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectState,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'District *',
                                  onPressed: () {
                                    if (selectState!.isEmpty == true) {
                                      ScaffoldMessenger.of(context)
                                          .showSnackBar(
                                        const SnackBar(
                                            content: Text(
                                                "First Select the state name.")),
                                      );
                                    } else {
                                      showSelectorDialog<DistrictInfo>(
                                        fetchData: () =>
                                            DistrictInfo.fetchDataFromApi(
                                                selectStateCode),
                                        dialogTitle: 'Select District',
                                        getDisplayText: (item) =>
                                            item.districtName ?? '',
                                        onSelected: (item) {
                                          setState(() {
                                            selectDistrict = item.districtName!;
                                            selectDistrictCode =
                                                item.districtName!;
                                          });
                                        },
                                      );
                                    }
                                  },
                                  value: selectDistrict,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: customerContactNumberController,
                                  hintText:
                                      'Customer Contact Number (Mandatory)',
                                  label: 'Customer Contact No. *',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue: customerContactNumber,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: fullAddressController,
                                  hintText: 'Full Address (Mandatory)',
                                  label: 'Full Address *',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : fullAddress,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: contactorNameController,
                                  hintText:
                                      'Petty Contractor - Head Mason Name',
                                  label: 'Petty Contractor - Head Mason Name',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : contactorName,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: contactorPhoneNoController,
                                  hintText:
                                      'Petty Contractor - Head Mason Contact No.',
                                  label:
                                      'Petty Contractor - Head Mason Contact No.',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue:
                                      isNewAdd ? '' : contactorPhoneNumber,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: engineerNameController,
                                  hintText: 'Engineer Name',
                                  label: 'Engineer Name',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : engineerName,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: engineerPhoneNoController,
                                  hintText: 'Engineer Contact No.',
                                  label: 'Engineer Contact No.',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue:
                                      isNewAdd ? '' : engineerPhoneNumber,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Engineer Regd. In Star Stellar',
                                  onPressed: () {
                                    showSelectorDialog<YesNoOption>(
                                      fetchData: () =>
                                          YesNoOption.fetchDataFromStatic(),
                                      dialogTitle:
                                          'Select Engineer Regd. In Star Stellar',
                                      getDisplayText: (item) => item.label,
                                      onSelected: (item) {
                                        setState(() {
                                          selectRegInStellar = item.label;
                                          selectRegInStellarCode = item.label;
                                        });
                                      },
                                    );
                                  },
                                  value: selectRegInStellar,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Site Segment *',
                                  onPressed: () {
                                    showSelectorDialog<SiteSegmentInfo>(
                                      fetchData: () =>
                                          SiteSegmentInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Site Segment',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectSiteSegment = item.name!;
                                          selectSiteSegmentCode = item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectSiteSegment,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Project Segment *',
                                  onPressed: () {
                                    showSelectorDialog<ProjectSegmentInfo>(
                                      fetchData: () =>
                                          ProjectSegmentInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Project Segment',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectProjectSegment = item.name!;
                                          selectProjectSegmentCode = item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectProjectSegment,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Type Of Construction *',
                                  onPressed: () {
                                    showSelectorDialog<TypeOfConstructionInfo>(
                                      fetchData: () => TypeOfConstructionInfo
                                          .fetchDataFromApi(),
                                      dialogTitle:
                                          'Select Type Of Construction',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectTypeOfConstruction = item.name!;
                                          selectTypeOfConstructionCode =
                                              item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectTypeOfConstruction,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: sitePotentialController,
                                  hintText: 'Site Potential (Mandatory)',
                                  label: 'Site Potential (No. of Bags) *',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : sitePotential,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel:
                                      'Current Stage of Construction *',
                                  onPressed: () {
                                    showSelectorDialog<
                                        CurrentStageOfConstructionInfo>(
                                      fetchData: () =>
                                          CurrentStageOfConstructionInfo
                                              .fetchDataFromApi(),
                                      dialogTitle:
                                          'Select Current Stage of Construction',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectConstructionStatus = item.name!;
                                          selectConstructionStatusCode =
                                              item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectConstructionStatus,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Cement Brand Used *',
                                  onPressed: () {
                                    showSelectorDialog<CementBrandInfo>(
                                      fetchData: () =>
                                          CementBrandInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Cement Brand Used',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectCementBrand = item.name!;
                                          selectCementBrandCode = item.name!;
                                        });

                                        if (item.name!
                                                .toString()
                                                .toLowerCase() ==
                                            'other') {
                                          isOpenCementBrandEditText = true;
                                        } else {
                                          isOpenCementBrandEditText = false;
                                        }
                                      },
                                    );
                                  },
                                  value: selectCementBrand,
                                ),
                                isOpenCementBrandEditText
                                    ? SizedBox(height: 15)
                                    : SizedBox(height: 0),
                                isOpenCementBrandEditText
                                    ? LabeledTextField(
                                        controller:
                                            otherCementBrandNameController,
                                        hintText: 'Name of the OTHER Brand',
                                        label: 'Name of the OTHER Brand',
                                        maxLength: 255,
                                        keyboardType: TextInputType.name,
                                        isEditable: isNewAdd ? true : false,
                                        initialValue: isNewAdd
                                            ? ''
                                            : otherCementBrandName,
                                      )
                                    : SizedBox(height: 0),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: pricePerBagController,
                                  hintText: 'Price Per Bag (Rs.)',
                                  label: 'Price Per Bag (Rs.)',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : pricePerBag,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: consumedTillDateController,
                                  hintText: 'Consumed Till Date (No. of Bags)',
                                  label: 'Consumed Till Date (No. of Bags)',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue:
                                      isNewAdd ? '' : consumedTillDate,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: estimatedRequirementController,
                                  hintText: 'Estimated Requirement (Mandatory)',
                                  label:
                                      'Estimated Requirement (No. of Bags) *',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue:
                                      isNewAdd ? '' : estimatedRequirement,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: buildUpAreaController,
                                  hintText: 'Built up Area (Mandatory)',
                                  label: 'Built up Area (Sq. ft) *',
                                  maxLength: 20,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : buildUpArea,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Decision Maker',
                                  onPressed: () {
                                    showSelectorDialog<DecisionMakerInfo>(
                                      fetchData: () =>
                                          DecisionMakerInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Decision Maker',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectDecisionMaker = item.name!;
                                          selectDecisionMakerCode = item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectDecisionMaker,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Product Demo',
                                  onPressed: () {
                                    showSelectorDialog<ProductDemoInfo>(
                                      fetchData: () =>
                                          ProductDemoInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Product Demo',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectProductDemo = item.name!;
                                          selectProductDemoCode = item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectProductDemo,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: remarksController,
                                  hintText: 'Overall Remarks',
                                  label: 'Overall Remarks',
                                  maxLength: 512,
                                  keyboardType: TextInputType.name,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : remarks,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Visit Type *',
                                  onPressed: () {
                                    showSelectorDialog<VisitTypeInfo>(
                                        fetchData: () =>
                                            VisitTypeInfo.fetchDataFromApi(),
                                        dialogTitle: 'Select Visit Type',
                                        getDisplayText: (item) =>
                                            item.name ?? '',
                                        onSelected: (item) {
                                          final visitName =
                                              item.name?.trim() ?? '';
                                          if (visitName.isEmpty) return;

                                          setState(() {
                                            selectVisitType = visitName;
                                            selectVisitTypeCode = visitName;
                                            showVisitType = visitName;
                                          });

                                          // Defer the second dialog using Future.delayed
                                          Future.delayed(Duration.zero, () {
                                            if (visitName.toLowerCase() ==
                                                'star site') {
                                              showSelectorDialog<
                                                  VisitTypeStarInfo>(
                                                fetchData: () =>
                                                    VisitTypeStarInfo
                                                        .fetchDataFromApi(),
                                                dialogTitle:
                                                    'Select Visit Type',
                                                getDisplayText: (item) =>
                                                    item.name ?? '',
                                                onSelected: (item) {
                                                  final subTypeName =
                                                      item.name?.trim() ?? '';
                                                  if (subTypeName.isEmpty) {
                                                    return;
                                                  }

                                                  setState(() {
                                                    selectVisitSubType =
                                                        subTypeName;
                                                    selectVisitSubTypeCode =
                                                        subTypeName;
                                                    showVisitType =
                                                        '$visitName, $subTypeName';
                                                  });
                                                },
                                              );
                                            } else {
                                              showSelectorDialog<
                                                  VisitTypeNonStarInfo>(
                                                fetchData: () =>
                                                    VisitTypeNonStarInfo
                                                        .fetchDataFromApi(),
                                                dialogTitle:
                                                    'Select Visit Type',
                                                getDisplayText: (item) =>
                                                    item.name ?? '',
                                                onSelected: (item) {
                                                  final subTypeName =
                                                      item.name?.trim() ?? '';
                                                  if (subTypeName.isEmpty) {
                                                    return;
                                                  }

                                                  setState(() {
                                                    selectVisitSubType =
                                                        subTypeName;
                                                    selectVisitSubTypeCode =
                                                        subTypeName;
                                                    showVisitType =
                                                        '$visitName, $subTypeName';
                                                  });
                                                },
                                              );
                                            }
                                          });
                                        });
                                  },
                                  value: showVisitType,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Requested Date of Delivery',
                                  onPressed: () async {
                                    DateTime? pickedDate = await showDatePicker(
                                      context: context,
                                      initialDate: DateTime.now(),
                                      firstDate: DateTime(2000),
                                      lastDate: DateTime(2100),
                                    );

                                    if (pickedDate != null) {
                                      setState(() {
                                        // Format for display: DD/MM/YYYY
                                        selectDeliveryDate =
                                            "${pickedDate.day.toString().padLeft(2, '0')}/"
                                            "${pickedDate.month.toString().padLeft(2, '0')}/"
                                            "${pickedDate.year}";

                                        // Format for backend: YYYY-MM-DD
                                        selectDeliveryDateCode =
                                            "${pickedDate.year}-"
                                            "${pickedDate.month.toString().padLeft(2, '0')}-"
                                            "${pickedDate.day.toString().padLeft(2, '0')}";
                                      });
                                    }
                                  },
                                  value: selectDeliveryDate,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: noOdBagOrderController,
                                  hintText: 'No. of Bags Ordered',
                                  label: 'No. of Bags Ordered',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: isNewAdd ? true : false,
                                  initialValue: isNewAdd ? '' : noOdBagOrder,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel:
                                      'Source of Purchase Dealer/RSSD (Name)',
                                  onPressed: () {
                                    showSelectorDialog<RssdDealerInfo>(
                                      fetchData: () =>
                                          RssdDealerInfo.fetchDataFromApi(),
                                      dialogTitle:
                                          'Select Purchase Dealer/RSSD',
                                      getDisplayText: (item) =>
                                          item.customerName ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectDealer = item.customerName!;
                                          selectDealerCode = item.customerCode!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectDealer,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'To Be Approved By',
                                  onPressed: () {
                                    showSelectorDialog<ApprovedByInfo>(
                                      fetchData: () =>
                                          ApprovedByInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Approved By',
                                      getDisplayText: (item) =>
                                          item.empName ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectApprovedBy = item.empName!;
                                          selectApprovedByCode = item.empCode!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectApprovedBy,
                                ),
                                SizedBox(height: 65),
                              ],
                            ),
                          ),
                          Positioned(
                            left: 0,
                            right: 0,
                            bottom: 0,
                            child: Container(
                              padding: const EdgeInsets.all(16),
                              color: Colors.white,
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
                                    _checkDataAndRequestForUploadNewSiteDetails();
                                    // ScaffoldMessenger.of(context).showSnackBar(
                                    //   const SnackBar(content: Text("Submitted")),
                                    // );
                                  },
                                  child: const Text(
                                    'Submit',
                                    style: TextStyle(
                                        fontSize: 16, color: Colors.white),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      )
                    : Stack(
                        children: [
                          SingleChildScrollView(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  '+91 ${customerContactNumber ?? ''}',
                                  style: const TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                    color: Color.fromRGBO(55, 0, 255, 1),
                                  ),
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Route Name *',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Route Name")),
                                    );
                                  },
                                  value: selectedRouteName,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Site Name *',
                                  onPressed: () {
                                    showSelectorDialog<CustomerSiteListInfo>(
                                      fetchData: () =>
                                          CustomerSiteListInfo.fetchDataFromApi(
                                              customerContactNumber!,
                                              selectedRouteCode!),
                                      dialogTitle: 'Select Site Name',
                                      getDisplayText: (item) =>
                                          item.siteName ?? '',
                                      onSelected: (item) {
                                        isOpenCementBrandEditText = false;
                                        setState(() {
                                          siteName = item.siteName ?? '';
                                          selectSiteName = item.siteName ?? '';
                                          customerName =
                                              item.siteDetails?['cust_name'] ??
                                                  '';
                                          selectMeetingPerson =
                                              item.siteDetails?[
                                                      'meeting_person_type'] ??
                                                  '';
                                          selectMeetingPersonCode =
                                              item.siteDetails?[
                                                      'meeting_person_type'] ??
                                                  '';
                                          meetingPersonPhoneNumber =
                                              item.siteDetails?[
                                                      'meeting_person_phone'] ??
                                                  '';
                                          selectBranch = item.siteDetails?[
                                                  'branch_name'] ??
                                              '';
                                          selectBranchCode = item.siteDetails?[
                                                  'branch_code'] ??
                                              '';
                                          selectState =
                                              item.siteDetails?['state'] ?? '';
                                          selectStateCode =
                                              item.siteDetails?['state'] ?? '';
                                          selectDistrict =
                                              item.siteDetails?['district'] ??
                                                  '';
                                          selectDistrictCode =
                                              item.siteDetails?['district'] ??
                                                  '';
                                          fullAddress =
                                              item.siteDetails?['address'] ??
                                                  '';
                                          contactorName = item.siteDetails?[
                                                  'contractor_name'] ??
                                              '';
                                          contactorPhoneNumber =
                                              item.siteDetails?[
                                                      'contractor_phone'] ??
                                                  '';
                                          engineerName = item.siteDetails?[
                                                  'engineer_name'] ??
                                              '';
                                          engineerPhoneNumber =
                                              item.siteDetails?[
                                                      'engineer_phone'] ??
                                                  '';
                                          selectRegInStellar = item
                                                      .siteDetails?[
                                                  'engg_reg_star_stellar'] ??
                                              '';
                                          selectRegInStellarCode = item
                                                      .siteDetails?[
                                                  'engg_reg_star_stellar'] ??
                                              '';
                                          selectSiteSegment = item.siteDetails?[
                                                  'site_segment'] ??
                                              '';
                                          selectSiteSegmentCode =
                                              item.siteDetails?[
                                                      'site_segment'] ??
                                                  '';
                                          selectProjectSegment =
                                              item.siteDetails?[
                                                      'project_segment'] ??
                                                  '';
                                          selectProjectSegmentCode =
                                              item.siteDetails?[
                                                      'project_segment'] ??
                                                  '';
                                          selectTypeOfConstruction =
                                              item.siteDetails?[
                                                      'type_of_construction'] ??
                                                  '';
                                          selectTypeOfConstructionCode =
                                              item.siteDetails?[
                                                      'type_of_construction'] ??
                                                  '';
                                          sitePotential = item.siteDetails?[
                                                  'site_potential'] ??
                                              '';
                                          selectConstructionStatus =
                                              item.siteDetails?[
                                                      'construction_stage'] ??
                                                  '';
                                          selectConstructionStatusCode =
                                              item.siteDetails?[
                                                      'construction_stage'] ??
                                                  '';
                                          selectCementBrand = item.siteDetails?[
                                                  'cement_brand'] ??
                                              '';
                                          selectCementBrandCode =
                                              item.siteDetails?[
                                                      'cement_brand'] ??
                                                  '';
                                          otherCementBrandName =
                                              item.siteDetails?[
                                                      'cement_brand'] ??
                                                  '';
                                          pricePerBag = item.siteDetails?[
                                                  'price_per_bag'] ??
                                              '';
                                          consumedTillDate = item.siteDetails?[
                                                  'consumed_till_date'] ??
                                              '';
                                          estimatedRequirement =
                                              item.siteDetails?[
                                                      'estimated_req'] ??
                                                  '';
                                          buildUpArea = item.siteDetails?[
                                                  'built_up_area'] ??
                                              '';
                                          selectDecisionMaker =
                                              item.siteDetails?[
                                                      'decision_maker'] ??
                                                  '';
                                          selectDecisionMakerCode =
                                              item.siteDetails?[
                                                      'decision_maker'] ??
                                                  '';
                                          selectProductDemo = item.siteDetails?[
                                                  'product_demo'] ??
                                              '';
                                          selectProductDemoCode =
                                              item.siteDetails?[
                                                      'product_demo'] ??
                                                  '';
                                          remarks =
                                              item.siteDetails?['remarks'] ??
                                                  '';
                                          selectVisitType =
                                              item.siteDetails?['visit_type'] ??
                                                  '';
                                          selectVisitTypeCode =
                                              item.siteDetails?['visit_type'] ??
                                                  '';
                                          showVisitType = (item.siteDetails?[
                                                      'visit_type'] ??
                                                  '') +
                                              // ignore: prefer_interpolation_to_compose_strings
                                              ', ' +
                                              (item.siteDetails?[
                                                      'visit_sub_type'] ??
                                                  '');
                                          selectVisitSubType =
                                              item.siteDetails?[
                                                      'visit_sub_type'] ??
                                                  '';
                                          selectVisitSubTypeCode =
                                              item.siteDetails?[
                                                      'visit_sub_type'] ??
                                                  '';
                                          selectDeliveryDate =
                                              item.siteDetails?[
                                                      'date_of_delivery'] ??
                                                  '';
                                          selectDeliveryDateCode =
                                              item.siteDetails?[
                                                      'date_of_delivery'] ??
                                                  '';
                                          noOdBagOrder = item.siteDetails?[
                                                  'bags_ordered'] ??
                                              '';
                                          selectDealer =
                                              item.siteDetails?['rssd_name'] ??
                                                  '';
                                          selectDealerCode =
                                              item.siteDetails?['rssd'] ?? '';
                                          selectApprovedBy = item.siteDetails?[
                                                  'approved_by_name'] ??
                                              '';
                                          selectApprovedByCode =
                                              item.siteDetails?[
                                                      'approved_by'] ??
                                                  '';
                                        });
                                      },
                                    );
                                  },
                                  value: selectSiteName,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: customerNameController,
                                  hintText: 'Customer Name (Mandatory)',
                                  label: 'Customer Name *',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: false,
                                  initialValue: isNewAdd ? '' : customerName,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Meeting Person *',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Meeting Person")),
                                    );
                                  },
                                  value: selectMeetingPerson,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller:
                                      meetingPersonPhoneNumberController,
                                  hintText:
                                      'Meeting Person Phone Number (Mandatory)',
                                  label: 'Meeting Person Phone Number *',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue:
                                      isNewAdd ? '' : meetingPersonPhoneNumber,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Branch *',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Branch Name")),
                                    );
                                  },
                                  value: selectBranch,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'State *',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the State")),
                                    );
                                  },
                                  value: selectState,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'District *',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the District")),
                                    );
                                  },
                                  value: selectDistrict,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: fullAddressController,
                                  hintText: 'Full Address (Mandatory)',
                                  label: 'Full Address *',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: false,
                                  initialValue: isNewAdd ? '' : fullAddress,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: contactorNameController,
                                  hintText:
                                      'Petty Contractor - Head Mason Name',
                                  label: 'Petty Contractor - Head Mason Name',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: false,
                                  initialValue: isNewAdd ? '' : contactorName,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: contactorPhoneNoController,
                                  hintText:
                                      'Petty Contractor - Head Mason Contact No.',
                                  label:
                                      'Petty Contractor - Head Mason Contact No.',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue:
                                      isNewAdd ? '' : contactorPhoneNumber,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: engineerNameController,
                                  hintText: 'Engineer Name',
                                  label: 'Engineer Name',
                                  maxLength: 255,
                                  keyboardType: TextInputType.name,
                                  isEditable: false,
                                  initialValue: isNewAdd ? '' : engineerName,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: engineerPhoneNoController,
                                  hintText: 'Engineer Contact No.',
                                  label: 'Engineer Contact No.',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue:
                                      isNewAdd ? '' : engineerPhoneNumber,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Engineer Regd. In Star Stellar',
                                  onPressed: () {
                                    showSelectorDialog<YesNoOption>(
                                      fetchData: () =>
                                          YesNoOption.fetchDataFromStatic(),
                                      dialogTitle:
                                          'Select Engineer Regd. In Star Stellar',
                                      getDisplayText: (item) => item.label,
                                      onSelected: (item) {
                                        setState(() {
                                          selectRegInStellar = item.label;
                                          selectRegInStellarCode = item.label;
                                        });
                                      },
                                    );
                                  },
                                  value: selectRegInStellar,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Site Segment *',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Site Segment")),
                                    );
                                  },
                                  value: selectSiteSegment,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Project Segment *',
                                  onPressed: () {
                                    showSelectorDialog<ProjectSegmentInfo>(
                                      fetchData: () =>
                                          ProjectSegmentInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Project Segment',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectProjectSegment = item.name!;
                                          selectProjectSegmentCode = item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectProjectSegment,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Type Of Construction *',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Type of Construction")),
                                    );
                                  },
                                  value: selectTypeOfConstruction,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: sitePotentialController,
                                  hintText: 'Site Potential (Mandatory)',
                                  label: 'Site Potential (No. of Bags) *',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: true,
                                  initialValue: isNewAdd ? '' : sitePotential,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel:
                                      'Current Stage of Construction *',
                                  onPressed: () {
                                    showSelectorDialog<
                                        CurrentStageOfConstructionInfo>(
                                      fetchData: () =>
                                          CurrentStageOfConstructionInfo
                                              .fetchDataFromApi(),
                                      dialogTitle:
                                          'Select Current Stage of Construction',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectConstructionStatus = item.name!;
                                          selectConstructionStatusCode =
                                              item.name!;
                                        });
                                      },
                                    );
                                  },
                                  value: selectConstructionStatus,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Cement Brand Used *',
                                  onPressed: () {
                                    showSelectorDialog<CementBrandInfo>(
                                      fetchData: () =>
                                          CementBrandInfo.fetchDataFromApi(),
                                      dialogTitle: 'Select Cement Brand Used',
                                      getDisplayText: (item) => item.name ?? '',
                                      onSelected: (item) {
                                        setState(() {
                                          selectCementBrand = item.name!;
                                          selectCementBrandCode = item.name!;
                                        });

                                        if (item.name!
                                                .toString()
                                                .toLowerCase() ==
                                            'other') {
                                          isOpenCementBrandEditText = true;
                                        } else {
                                          isOpenCementBrandEditText = false;
                                        }
                                      },
                                    );
                                  },
                                  value: selectCementBrand,
                                ),
                                isOpenCementBrandEditText
                                    ? SizedBox(height: 15)
                                    : SizedBox(height: 0),
                                isOpenCementBrandEditText
                                    ? LabeledTextField(
                                        controller:
                                            otherCementBrandNameController,
                                        hintText: 'Name of the OTHER Brand',
                                        label: 'Name of the OTHER Brand',
                                        maxLength: 255,
                                        keyboardType: TextInputType.name,
                                        isEditable: true,
                                        initialValue: isNewAdd
                                            ? ''
                                            : otherCementBrandName,
                                      )
                                    : SizedBox(height: 0),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: pricePerBagController,
                                  hintText: 'Price Per Bag (Rs.)',
                                  label: 'Price Per Bag (Rs.)',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue: isNewAdd ? '' : pricePerBag,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: consumedTillDateController,
                                  hintText: 'Consumed Till Date (No. of Bags)',
                                  label: 'Consumed Till Date (No. of Bags)',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue:
                                      isNewAdd ? '' : consumedTillDate,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: estimatedRequirementController,
                                  hintText: 'Estimated Requirement (Mandatory)',
                                  label:
                                      'Estimated Requirement (No. of Bags) *',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: true,
                                  initialValue:
                                      isNewAdd ? '' : estimatedRequirement,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: buildUpAreaController,
                                  hintText: 'Built up Area (Mandatory)',
                                  label: 'Built up Area (Sq. ft) *',
                                  maxLength: 20,
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue: isNewAdd ? '' : buildUpArea,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Decision Maker',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Decision Maker")),
                                    );
                                  },
                                  value: selectDecisionMaker,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Product Demo',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Product Demo")),
                                    );
                                  },
                                  value: selectProductDemo,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: remarksController,
                                  hintText: 'Overall Remarks',
                                  label: 'Overall Remarks',
                                  maxLength: 512,
                                  keyboardType: TextInputType.name,
                                  isEditable: true,
                                  initialValue: isNewAdd ? '' : remarks,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Visit Type *',
                                  onPressed: () {
                                    showSelectorDialog<VisitTypeInfo>(
                                        fetchData: () =>
                                            VisitTypeInfo.fetchDataFromApi(),
                                        dialogTitle: 'Select Visit Type',
                                        getDisplayText: (item) =>
                                            item.name ?? '',
                                        onSelected: (item) {
                                          final visitName =
                                              item.name?.trim() ?? '';
                                          if (visitName.isEmpty) return;

                                          setState(() {
                                            selectVisitType = visitName;
                                            selectVisitTypeCode = visitName;
                                            showVisitType = visitName;
                                          });

                                          // Defer the second dialog using Future.delayed
                                          Future.delayed(Duration.zero, () {
                                            if (visitName.toLowerCase() ==
                                                'star site') {
                                              showSelectorDialog<
                                                  VisitTypeStarInfo>(
                                                fetchData: () =>
                                                    VisitTypeStarInfo
                                                        .fetchDataFromApi(),
                                                dialogTitle:
                                                    'Select Visit Type',
                                                getDisplayText: (item) =>
                                                    item.name ?? '',
                                                onSelected: (item) {
                                                  final subTypeName =
                                                      item.name?.trim() ?? '';
                                                  if (subTypeName.isEmpty) {
                                                    return;
                                                  }

                                                  setState(() {
                                                    selectVisitSubType =
                                                        subTypeName;
                                                    selectVisitSubTypeCode =
                                                        subTypeName;
                                                    showVisitType =
                                                        '$visitName, $subTypeName';
                                                  });
                                                },
                                              );
                                            } else {
                                              showSelectorDialog<
                                                  VisitTypeNonStarInfo>(
                                                fetchData: () =>
                                                    VisitTypeNonStarInfo
                                                        .fetchDataFromApi(),
                                                dialogTitle:
                                                    'Select Visit Type',
                                                getDisplayText: (item) =>
                                                    item.name ?? '',
                                                onSelected: (item) {
                                                  final subTypeName =
                                                      item.name?.trim() ?? '';
                                                  if (subTypeName.isEmpty) {
                                                    return;
                                                  }

                                                  setState(() {
                                                    selectVisitSubType =
                                                        subTypeName;
                                                    selectVisitSubTypeCode =
                                                        subTypeName;
                                                    showVisitType =
                                                        '$visitName, $subTypeName';
                                                  });
                                                },
                                              );
                                            }
                                          });
                                        });
                                  },
                                  value: showVisitType,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'Requested Date of Delivery',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Date of Delivery")),
                                    );
                                  },
                                  value: selectDeliveryDate,
                                ),
                                SizedBox(height: 15),
                                LabeledTextField(
                                  controller: noOdBagOrderController,
                                  hintText: 'No. of Bags Ordered',
                                  label: 'No. of Bags Ordered',
                                  maxLength: 10,
                                  keyboardType: TextInputType.number,
                                  isEditable: false,
                                  initialValue: isNewAdd ? '' : noOdBagOrder,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel:
                                      'Source of Purchase Dealer/RSSD (Name)',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Purchase Dealer or RSSD name")),
                                    );
                                  },
                                  value: selectDealer,
                                ),
                                SizedBox(height: 15),
                                SelectButtonWithLabel(
                                  buttonLabel: 'To Be Approved By',
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                          content: Text(
                                              "Can not able to change the Approved By name")),
                                    );
                                  },
                                  value: selectApprovedBy,
                                ),
                                SizedBox(height: 65),
                              ],
                            ),
                          ),
                          Positioned(
                            left: 0,
                            right: 0,
                            bottom: 0,
                            child: Container(
                              padding: const EdgeInsets.all(16),
                              color: Colors.white,
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
                                    _checkDataAndRequestForUpdateExistingSiteDetails();
                                    // ScaffoldMessenger.of(context).showSnackBar(
                                    //   const SnackBar(content: Text("Submitted")),
                                    // );
                                  },
                                  child: const Text(
                                    'Submit',
                                    style: TextStyle(
                                        fontSize: 16, color: Colors.white),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
              )),
            ),
            if (isMainLoading)
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
  final VoidCallback onPressed;
  final String buttonLabel;
  final String? value;

  const SelectButtonWithLabel({
    super.key,
    required this.onPressed,
    required this.buttonLabel,
    this.value,
  });

  @override
  Widget build(BuildContext context) {
    final hasValue = (value ?? '').trim().isNotEmpty;
    // ignore: unnecessary_null_comparison
    final isEnabled = onPressed != null;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: double.infinity,
          child: OutlinedButton(
            onPressed: onPressed,
            style: OutlinedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 16),
              side: const BorderSide(color: Colors.grey),
              backgroundColor: isEnabled ? Colors.white : Colors.grey.shade100,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(4),
              ),
            ),
            child: Text(
              buttonLabel,
              style: const TextStyle(fontSize: 16, color: Colors.black),
            ),
          ),
        ),
        const SizedBox(height: 8),
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
        Text(
          label,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 5),
        TextField(
          controller: controller,
          keyboardType: keyboardType,
          enabled: isEditable,
          maxLength: maxLength,
          decoration: InputDecoration(
            hintText: hintText,
            border: const OutlineInputBorder(),
            filled: !isEditable,
            fillColor: !isEditable ? Colors.grey.shade200 : null,
          ),
        ),
      ],
    );
  }
}

class StateInfo {
  String? name;

  StateInfo({this.name});

  factory StateInfo.fromJson(Map<String, dynamic> json) {
    return StateInfo(
      name: json['state_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'state_name': name,
    };
  }

  static Future<List<StateInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse('${AppWebService.baseURL}misreport/api_get_state_details.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => StateInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class DistrictInfo {
  String? state;
  String? districtName;

  DistrictInfo({this.state, this.districtName});

  factory DistrictInfo.fromJson(Map<String, dynamic> json) {
    return DistrictInfo(
      state: json['state'],
      districtName: json['district_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'state': state,
      'district_name': districtName,
    };
  }

  static Future<List<DistrictInfo>> fetchDataFromApi(selectedStateCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);
    final response = await ioClient.get(
      Uri.parse('${AppWebService.baseURL}misreport/api_get_district_name.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => DistrictInfo.fromJson(item))
            .where((district) => district.state == selectedStateCode)
            .toList();
      } else {
        throw Exception('Invalid or empty district response');
      }
    } else {
      throw Exception('Failed to fetch district list');
    }
  }
}

class ProjectSegmentInfo {
  String? name;

  ProjectSegmentInfo({this.name});

  factory ProjectSegmentInfo.fromJson(Map<String, dynamic> json) {
    return ProjectSegmentInfo(
      name: json['project_segment'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'project_segment': name,
    };
  }

  static Future<List<ProjectSegmentInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);
    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_project_segment.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => ProjectSegmentInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class TypeOfConstructionInfo {
  String? name;

  TypeOfConstructionInfo({this.name});

  factory TypeOfConstructionInfo.fromJson(Map<String, dynamic> json) {
    return TypeOfConstructionInfo(
      name: json['type_of_construction'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'type_of_construction': name,
    };
  }

  static Future<List<TypeOfConstructionInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_type_of_construction.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => TypeOfConstructionInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class CurrentStageOfConstructionInfo {
  String? name;

  CurrentStageOfConstructionInfo({this.name});

  factory CurrentStageOfConstructionInfo.fromJson(Map<String, dynamic> json) {
    return CurrentStageOfConstructionInfo(
      name: json['current_stage_of_construction'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'current_stage_of_construction': name,
    };
  }

  static Future<List<CurrentStageOfConstructionInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_current_stage_of_construction.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => CurrentStageOfConstructionInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class CementBrandInfo {
  String? name;

  CementBrandInfo({this.name});

  factory CementBrandInfo.fromJson(Map<String, dynamic> json) {
    return CementBrandInfo(
      name: json['cement_brand'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'cement_brand': name,
    };
  }

  static Future<List<CementBrandInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_cement_brand_used.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => CementBrandInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class DecisionMakerInfo {
  String? name;

  DecisionMakerInfo({this.name});

  factory DecisionMakerInfo.fromJson(Map<String, dynamic> json) {
    return DecisionMakerInfo(
      name: json['decision_maker'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'decision_maker': name,
    };
  }

  static Future<List<DecisionMakerInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse('${AppWebService.baseURL}misreport/api_get_decision_maker.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => DecisionMakerInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class ProductDemoInfo {
  String? name;

  ProductDemoInfo({this.name});

  factory ProductDemoInfo.fromJson(Map<String, dynamic> json) {
    return ProductDemoInfo(
      name: json['product_demo'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'product_demo': name,
    };
  }

  static Future<List<ProductDemoInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse('${AppWebService.baseURL}misreport/api_get_product_demo.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => ProductDemoInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class VisitTypeInfo {
  String? name;

  VisitTypeInfo({this.name});

  factory VisitTypeInfo.fromJson(Map<String, dynamic> json) {
    return VisitTypeInfo(
      name: json['visit_type'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'visit_type': name,
    };
  }

  static Future<List<VisitTypeInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse('${AppWebService.baseURL}misreport/api_get_visit_type.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => VisitTypeInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class VisitTypeNonStarInfo {
  String? name;

  VisitTypeNonStarInfo({this.name});

  factory VisitTypeNonStarInfo.fromJson(Map<String, dynamic> json) {
    return VisitTypeNonStarInfo(
      name: json['visit_type_non_star'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'visit_type_non_star': name,
    };
  }

  static Future<List<VisitTypeNonStarInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_visit_type_nonstar.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => VisitTypeNonStarInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class VisitTypeStarInfo {
  String? name;

  VisitTypeStarInfo({this.name});

  factory VisitTypeStarInfo.fromJson(Map<String, dynamic> json) {
    return VisitTypeStarInfo(
      name: json['visit_type_star'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'visit_type_star': name,
    };
  }

  static Future<List<VisitTypeStarInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_visit_type_star.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => VisitTypeStarInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class MeetUpPersonInfo {
  String? name;

  MeetUpPersonInfo({this.name});

  factory MeetUpPersonInfo.fromJson(Map<String, dynamic> json) {
    return MeetUpPersonInfo(
      name: json['meet_up_person'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'meet_up_person': name,
    };
  }

  static Future<List<MeetUpPersonInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse('${AppWebService.baseURL}misreport/api_get_meet_up_person.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => MeetUpPersonInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class BranchInfo {
  String? branchName;
  String? branchCode;

  BranchInfo({this.branchName, this.branchCode});

  factory BranchInfo.fromJson(Map<String, dynamic> json) {
    return BranchInfo(
      branchName: json['branch_name'],
      branchCode: json['branch_code'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'branch_name': branchName,
      'branch_code': branchCode,
    };
  }

  static Future<List<BranchInfo>> fetchDataFromApi(String routeCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_branch.php?route_code=$routeCode'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => BranchInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty district response');
      }
    } else {
      throw Exception('Failed to fetch district list');
    }
  }
}

class ApprovedByInfo {
  String? empCode;
  String? empName;

  ApprovedByInfo({this.empCode, this.empName});

  factory ApprovedByInfo.fromJson(Map<String, dynamic> json) {
    return ApprovedByInfo(
      empCode: json['emp_code'],
      empName: json['emp_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'emp_code': empCode,
      'emp_name': empName,
    };
  }

  static Future<List<ApprovedByInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();
    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport//api_get_approved_by.php?emp_code=${user?.empCode}'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['employees'] != null &&
          jsonResponse['employees'] is List) {
        return (jsonResponse['employees'] as List)
            .map((item) => ApprovedByInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty district response');
      }
    } else {
      throw Exception('Failed to fetch district list');
    }
  }
}

class RssdDealerInfo {
  String? customerCode;
  String? customerName;

  RssdDealerInfo({this.customerCode, this.customerName});

  factory RssdDealerInfo.fromJson(Map<String, dynamic> json) {
    return RssdDealerInfo(
      customerCode: json['customer_code'],
      customerName: json['customer_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'customer_code': customerCode,
      'customer_name': customerName,
    };
  }

  static Future<List<RssdDealerInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();
    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_list_of_rssd.php?emp_code=${user?.empCode}'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['dealers'] != null &&
          jsonResponse['dealers'] is List) {
        return (jsonResponse['dealers'] as List)
            .map((item) => RssdDealerInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty district response');
      }
    } else {
      throw Exception('Failed to fetch district list');
    }
  }
}

class SiteSegmentInfo {
  String? name;

  SiteSegmentInfo({this.name});

  factory SiteSegmentInfo.fromJson(Map<String, dynamic> json) {
    return SiteSegmentInfo(
      name: json['state_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'state_name': name,
    };
  }

  static Future<List<SiteSegmentInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse('${AppWebService.baseURL}misreport/api_site_segment.php'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['value'] != null &&
          jsonResponse['value'] is List) {
        return (jsonResponse['value'] as List)
            .map((item) => SiteSegmentInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty response');
      }
    } else {
      throw Exception('Failed to fetch state list');
    }
  }
}

class RouteNameEmployeeInfo {
  String? routeCode;
  String? routeName;

  RouteNameEmployeeInfo({this.routeCode, this.routeName});

  factory RouteNameEmployeeInfo.fromJson(Map<String, dynamic> json) {
    return RouteNameEmployeeInfo(
      routeCode: json['route_code'],
      routeName: json['route_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'route_code': routeCode,
      'route_name': routeName,
    };
  }

  static Future<List<RouteNameEmployeeInfo>> fetchDataFromApi() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);
    final user = await UserLoginClass.getLocalUser();
    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_route_name_employee.php?emp_code=${user?.empCode}'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['data'] != null &&
          jsonResponse['data'] is List) {
        return (jsonResponse['data'] as List)
            .map((item) => RouteNameEmployeeInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty district response');
      }
    } else {
      throw Exception('Failed to fetch district list');
    }
  }
}

class RouteNameCustomerInfo {
  String? routeCode;
  String? routeName;

  RouteNameCustomerInfo({this.routeCode, this.routeName});

  factory RouteNameCustomerInfo.fromJson(Map<String, dynamic> json) {
    return RouteNameCustomerInfo(
      routeCode: json['route_code'],
      routeName: json['route'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'route_code': routeCode,
      'route': routeName,
    };
  }

  static Future<List<RouteNameCustomerInfo>> fetchDataFromApi(
      String customerNumber) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final response = await ioClient.get(
      Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_route_using_phone.php?cust_phone=$customerNumber'),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes" &&
          jsonResponse['data'] != null &&
          jsonResponse['data'] is List) {
        return (jsonResponse['data'] as List)
            .map((item) => RouteNameCustomerInfo.fromJson(item))
            .toList();
      } else {
        throw Exception('Invalid or empty district response');
      }
    } else {
      throw Exception('Failed to fetch district list');
    }
  }
}

class YesNoOption {
  final String label;
  final bool value;

  YesNoOption({required this.label, required this.value});

  factory YesNoOption.fromJson(Map<String, dynamic> json) {
    return YesNoOption(
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
  static Future<List<YesNoOption>> fetchDataFromStatic() async {
    return [
      YesNoOption(label: 'Yes', value: true),
      YesNoOption(label: 'No', value: false),
    ];
  }
}

class CustomerStatusInfo {
  final bool isAlreadyRegisteredInOtherRoute;
  final bool isNewCustomer;
  final bool isAlreadyRegisteredInSameRoute;

  CustomerStatusInfo({
    required this.isAlreadyRegisteredInOtherRoute,
    required this.isNewCustomer,
    required this.isAlreadyRegisteredInSameRoute,
  });

  factory CustomerStatusInfo.fromJson(Map<String, dynamic> json) {
    return CustomerStatusInfo(
        isAlreadyRegisteredInOtherRoute:
            json['is_already_register_in_other_route'] == 1,
        isNewCustomer: json['is_register_in_this_route'] == 0 &&
            json['is_already_register_in_other_route'] == 0,
        isAlreadyRegisteredInSameRoute: json['is_register_in_this_route'] == 1);
  }

  static Future<CustomerStatusInfo> fetchCustomerStatus(
      String phone, String routeCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final uri = Uri.parse(
        '${AppWebService.baseURL}misreport/api_get_fetch_site_data.php');

    final response = await ioClient.post(
      uri,
      headers: {
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'route_code': routeCode,
        'cust_phone': phone,
      }),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      // ignore: avoid_print
      print(jsonResponse.toString());
      if (jsonResponse['process_status'] == "Yes") {
        return CustomerStatusInfo.fromJson(jsonResponse);
      } else if (jsonResponse['process_status'] == "No") {
        return CustomerStatusInfo.fromJson(jsonResponse);
      } else {
        throw Exception('Customer not found or invalid response');
      }
    } else {
      throw Exception('Failed to fetch customer status');
    }
  }
}

class CustomerSiteListInfo {
  String? siteName;
  Map<String, dynamic>? siteDetails;

  CustomerSiteListInfo({
    this.siteName,
    this.siteDetails,
  });

  factory CustomerSiteListInfo.fromJson(Map<String, dynamic> json) {
    final latestVisit = json['latest_visit'];

    if (latestVisit is Map<String, dynamic>) {
      return CustomerSiteListInfo(
        siteName: latestVisit['site_name'],
        siteDetails: latestVisit,
      );
    } else {
      debugPrint(
          "Skipping item due to missing or invalid 'latest_visit': ${jsonEncode(json)}");
      return CustomerSiteListInfo(siteName: null, siteDetails: null);
    }
  }

  Map<String, dynamic> toJson() {
    return {
      'site_name': siteName,
      'latest_visit': siteDetails,
    };
  }

  static Future<List<CustomerSiteListInfo>> fetchDataFromApi(
      String phone, String routeCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) throw Exception('No internet connection');

    HttpClient httpClient = HttpClient()
      ..badCertificateCallback =
          (X509Certificate cert, String host, int port) =>
              host == "salesmpower.acedns.in";

    IOClient ioClient = IOClient(httpClient);

    final uri =
        Uri.parse('${AppWebService.baseURL}misreport/api_get_site_details.php');

    // ignore: avoid_print
    print('${AppWebService.baseURL}misreport/api_get_site_details.php');
    // ignore: avoid_print
    print(jsonEncode({
      'route_code': routeCode,
      'cust_phone': phone,
    }));

    final response = await ioClient.post(
      uri,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'route_code': routeCode,
        'cust_phone': phone,
      }),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      // ignore: avoid_print
      print(jsonEncode(jsonResponse));

      if (jsonResponse['process_status'] == "Yes") {
        final List<dynamic> siteList = jsonResponse['sites'] ?? [];

        return siteList
            .whereType<Map<String, dynamic>>()
            .map((item) => CustomerSiteListInfo.fromJson(item))
            .where((e) => e.siteDetails != null) // only keep valid items
            .toList();
      } else {
        throw Exception(
            'No site data found or process_status is No. routeCode=$routeCode, phone=$phone');
      }
    } else {
      throw Exception('Failed to fetch site list. HTTP ${response.statusCode}');
    }
  }
}

class NewSiteUploadInfo {
  final String message;
  // ignore: non_constant_identifier_names
  final String process_status;

  NewSiteUploadInfo({
    required this.message,
    // ignore: non_constant_identifier_names
    required this.process_status,
  });

  factory NewSiteUploadInfo.fromJson(Map<String, dynamic> json, String change) {
    return NewSiteUploadInfo(
        message: json['process_status'] == 'Yes'
            ? change == 'new'
                ? 'Successfully added new site information...'
                : 'Successfully update site information...'
            : json['error'],
        process_status: json['process_status']);
  }

  static Future<NewSiteUploadInfo> uploadData(
      Map<String, dynamic> dataSet, String change) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }

    // ignore: avoid_print
    print(jsonEncode(dataSet));

    HttpClient httpClient = HttpClient()
      ..badCertificateCallback = (X509Certificate cert, String host, int port) {
        return host == "salesmpower.acedns.in"; // Only ignore for this host
      };

    IOClient ioClient = IOClient(httpClient);

    final uri =
        Uri.parse('${AppWebService.baseURL}misreport/api_submit_site_form.php');

    final response = await ioClient.post(
      uri,
      headers: {
        'Content-Type': 'application/json',
      },
      body: jsonEncode(dataSet),
    );

    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      if (jsonResponse['process_status'] == "Yes") {
        return NewSiteUploadInfo.fromJson(jsonResponse, change);
      } else if (jsonResponse['process_status'] == "No") {
        return NewSiteUploadInfo.fromJson(jsonResponse, change);
      } else {
        throw Exception('Customer not found or invalid response');
      }
    } else {
      throw Exception('Failed to fetch customer status');
    }
  }
}
