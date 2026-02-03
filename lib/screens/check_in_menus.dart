// ignore_for_file: use_build_context_synchronously

import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/determine_position.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/route_master_class.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/check_out.dart';
import 'package:starsfa/screens/collection_screen.dart';
import 'package:starsfa/screens/home_screen.dart';
import 'package:starsfa/screens/market_feedback_screen.dart';
import 'package:starsfa/screens/order_screen.dart';
import 'package:starsfa/screens/route_plan_screen.dart';
import 'package:starsfa/screens/stock_audit_screen.dart';

class CheckInMenus extends StatefulWidget {
  final bool isOptionSelected;
  const CheckInMenus({super.key, this.isOptionSelected = false});

  @override
  State<CheckInMenus> createState() => _CheckInMenusState();
}

class _CheckInMenusState extends State<CheckInMenus> {
  final String sessionDateTimeFormat = 'yyyyMMddhhmmss';
  String sessionID = DateFormat('yyyyMMddhhmmss').format(DateTime.now());
  bool showMenus = false;
  final localDB = LocalDB;
  String customer_type="";
  final List<String> checkInMenuItems = [
    'Check Out',
    'Stock Audit',
    'Order',
    'Market Feedback',
    'Collection',
  ];
  final Map<String, String> checkInMenuItemsIcons = {
    'Check Out': 'assets/check_in_icons/Check Out.svg',
    'Stock Audit': 'assets/check_in_icons/Stock Audit.svg',
    'Order': 'assets/check_in_icons/Order.svg',
    'Market Feedback': 'assets/check_in_icons/Market Feedback.svg',
    'Collection': 'assets/check_in_icons/Collection.svg',
  };
  Map<String, Widget?> checkInMenuItemsRoutes = {
    'Check Out': null,
    'Stock Audit': null,
    'Order': null,
    'Market Feedback': null,
    'Collection': null,
  };
  Map<String, bool> checkInMenuItemsEnabled = {
    'Check Out': true,
    'Stock Audit': true,
    'Order': true,
    'Market Feedback': true,
    'Collection': true,
  };


  Future<bool> checkIfRoutePlanExists() async {
    final localDB = await LocalDB.openMyDatabase();
    final String selectedDateString =
        DateFormat(RoutePlanCalendar.dateFormat).format(DateTime.now());
    final List<Map<String, dynamic>> routePlanData = await localDB.rawQuery(
        'SELECT * FROM route_plan_transaction WHERE visit_date = ?',
        [selectedDateString]);
    return routePlanData.isNotEmpty;
  }

  Future<String?> selectTransactionType() {
    // pop up dialog box to select transaction type
    // options: Primary, Secondary
    return showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) {
          return PopScope(
            canPop: false,
            child: SimpleDialog(
              titlePadding: const EdgeInsets.all(0),
              // border radius
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
              title: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // back button
                  IconButton(
                    icon: const Icon(
                      Icons.arrow_back,
                      color: Colors.red,
                    ),
                    onPressed: () {
                      Navigator.of(context).pop();
                    },
                  ),
                  const Expanded(
                    child: Text(
                      'Transaction Type',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                          // color: Colors.red,
                          ),
                    ),
                  ),
                ],
              ),
              backgroundColor: Colors.blue[100],
              children: [
                InkWell(
                  onTap: () {
                    Navigator.of(context).pop('Primary');
                  },
                  child: Padding(
                    padding: const EdgeInsets.all(10.0),
                    child: Material(
                      elevation: 2,
                      borderRadius: BorderRadius.circular(10),
                      clipBehavior: Clip.hardEdge,
                      child: Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: Colors.grey[300],
                            // implement elevation
                          ),
                          alignment: Alignment.center,
                          child: const Text('Primary')),
                    ),
                  ),
                ),
                InkWell(
                  onTap: () {
                    Navigator.of(context).pop('Secondary');
                  },
                  child: Padding(
                    padding: const EdgeInsets.all(10.0),
                    child: Material(
                      elevation: 2,
                      borderRadius: BorderRadius.circular(10),
                      clipBehavior: Clip.hardEdge,
                      child: Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: Colors.grey[300],
                            // implement elevation
                          ),
                          alignment: Alignment.center,
                          child: const Text('Secondary')),
                    ),
                  ),
                ),
              ],
            ),
          );
        });
  }

  Future<String?> selectRoute() {
    return showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) {
          final selectedDate = DateTime.now();
          Future<List<RouteMasterDB>> getRouteMaster() async {
            final localDB = await LocalDB.openMyDatabase();
            final List<Map<String, dynamic>> routeMasterData = await localDB
                .rawQuery('SELECT * FROM route_master ORDER BY route_name ASC');
            List<RouteMasterDB> routeMasterList = [];
            for (int i = 0; i < routeMasterData.length; i++) {
              routeMasterList.add(RouteMasterDB.fromMap(routeMasterData[i]));
            }
            // get existing route plan for the selected date
            final String selectedDateString =
                DateFormat(RoutePlanCalendar.dateFormat).format(selectedDate);
            final List<Map<String, dynamic>> routePlanData = await localDB
                .rawQuery(
                    'SELECT * FROM route_plan_transaction WHERE visit_date = ?',
                    [selectedDateString]);
            final List<String> selectedRouteCodes = [];
            for (int i = 0; i < routePlanData.length; i++) {
              selectedRouteCodes.add(routePlanData[i]['route_code'].toString());
            }
            // filter the selected route codes
            routeMasterList = routeMasterList
                .where(
                    (element) => selectedRouteCodes.contains(element.routeCode))
                .toList();
            return routeMasterList;
          }

          final Future<List<RouteMasterDB>> routeMasterList = getRouteMaster();

          return PopScope(
            canPop: false,
            child: StatefulBuilder(builder: (context, setState) {
              return Dialog(
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10)),
                // opaqueness of the dialog
                backgroundColor: Colors.white,
                clipBehavior: Clip.hardEdge,
                child: Container(
                  padding: const EdgeInsets.all(20),
                  decoration: const BoxDecoration(
                    // image: DecorationImage(
                    //   image: AssetImage('assets/background.jpg'),
                    //   fit: BoxFit.cover,
                    // ),
                    color: Color.fromARGB(255, 236, 229, 221),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Row(
                        children: [
                          // back button
                          IconButton(
                            style: ButtonStyle(
                              iconColor: WidgetStateProperty.all(Colors.red),
                              // backgroundColor:
                              //     MaterialStateProperty.all(Colors.red),
                            ),
                            icon: const Icon(
                              Icons.arrow_back,
                              color: Colors.red,
                            ),
                            onPressed: () {
                              Navigator.of(context).pop();
                            },
                          ),
                          Expanded(
                            child: Container(
                              width: double.infinity,
                              margin:
                                  const EdgeInsets.symmetric(horizontal: 40),
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 10),
                              decoration: BoxDecoration(
                                // color: Colors.red[600],
                                borderRadius: BorderRadius.circular(10),
                              ),
                              alignment: Alignment.center,
                              child: const Text(
                                'Select a Route',
                                style: TextStyle(
                                  color: Colors.red,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 10),
                      FutureBuilder<List<RouteMasterDB>>(
                          future: routeMasterList,
                          builder: (context, snapshot) {
                            if (snapshot.connectionState ==
                                ConnectionState.waiting) {
                              return const Center(
                                  child: CircularProgressIndicator());
                            }
                            if (snapshot.hasError) {
                              return Text('Error: ${snapshot.error}');
                            } else {
                              final List<RouteMasterDB> routeMasterList =
                                  snapshot.data ?? [];
                              return ListView.separated(
                                separatorBuilder: (context, index) =>
                                    const Divider(
                                  color: Colors.grey,
                                ),
                                shrinkWrap: true,
                                itemCount: routeMasterList.length,
                                itemBuilder: (context, index) {
                                  return ListTile(
                                    contentPadding: const EdgeInsets.all(0),
                                    onTap: () {
                                      // close the dialog box
                                      Navigator.of(context).pop(
                                          routeMasterList[index].routeCode);
                                    },

                                    title: Text(
                                        routeMasterList[index].routeName ?? ''),
                                    // subtitle: Text(routeMasterList[index].routeCode ?? ''),
                                  );
                                },
                              );
                            }
                          }),
                    ],
                  ),
                ),
              );
            }),
          );
        });
  }

  Future<String?> selectCustomer(String routeCode, String custType) {
    return showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) {
          final Future<List<CustomerMasterDB>> getCustomerMaster =CustomerMasterDB.getCustomerMasterDB(routeCode, custType);
          List<CustomerMasterDB>? customerMasterList;
          String searchValue = '';
          return PopScope(
            canPop: false,
            child: StatefulBuilder(builder: (context, setState) {
              return Dialog(
                shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10)),
                backgroundColor: Colors.white,
                insetPadding: const EdgeInsets.all(20),
                clipBehavior: Clip.hardEdge,
                child: Container(
                  padding: const EdgeInsets.all(8.0),
                  decoration: const BoxDecoration(
                    color: Color.fromARGB(255, 236, 229, 221),
                  ),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          // back button
                          IconButton(
                            icon: const Icon(
                              Icons.arrow_back,
                              color: Colors.red,
                            ),
                            onPressed: () {
                              gotoHome();
                            },
                          ),
                          Expanded(
                            child: Container(
                              width: double.infinity,
                              margin: const EdgeInsets.symmetric(horizontal: 40),
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 10,
                              ),
                              decoration: BoxDecoration(
                                // color: Colors.red[600],
                                borderRadius: BorderRadius.circular(10),
                              ),
                              alignment: Alignment.center,
                              child: const Text(
                                'Select a Customer',
                                style: TextStyle(
                                    color: Colors.red,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 16),
                                textAlign: TextAlign.center,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 10),
                      Expanded(
                        child: FutureBuilder<List<CustomerMasterDB>>(
                            future: getCustomerMaster,
                            builder: (context, snapshot) {
                              if (snapshot.connectionState ==
                                  ConnectionState.waiting) {
                                return const Center(
                                    child: CircularProgressIndicator());
                              }
                              if (snapshot.hasError) {
                                return Text('Error: ${snapshot.error}');
                              } else {
                                if (snapshot.data?.isEmpty ?? true) {
                                  return Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      const Text(
                                          'No customers found for the selected route'),
                                      const SizedBox(height: 10),
                                      ElevatedButton(
                                        style: ElevatedButton.styleFrom(
                                          foregroundColor: Colors.white,
                                          backgroundColor: Colors.black,
                                        ),
                                        onPressed: () {
                                          Navigator.of(context).pop();
                                        },
                                        child: const Text('OK'),
                                      ),
                                    ],
                                  );
                                } else {
                                  customerMasterList = snapshot.data;
                                  // sort the list
                                  customerMasterList?.sort((a, b) =>
                                      (a.customerName ?? '')
                                          .compareTo((b.customerName ?? '')));
                                  customerMasterList = customerMasterList
                                      ?.where((element) => (element.customerName
                                                  ?.toLowerCase() ??
                                              '')
                                          .contains(searchValue.toLowerCase()))
                                      .toList();
                                  return Column(
                                    children: [
                                      // Search Bar
                                      Padding(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 8.0),
                                        child: TextField(
                                          onChanged: (value) {
                                            // filter the list
                                            setState(() {
                                              searchValue = value;
                                              // customerMasterList = customerMasterList
                                              //     ?.where((element) => (element
                                              //                 .customerName
                                              //                 ?.toLowerCase() ??
                                              //             '')
                                              //         .contains(searchValue
                                              //             .toLowerCase()))
                                              //     .toList();
                                            });
                                          },
                                          decoration: const InputDecoration(
                                            labelText: 'Search Customer',
                                            prefixIcon: Icon(Icons.search),
                                          ),
                                        ),
                                      ),
                                      const SizedBox(height: 10),
                                      customerMasterList?.isEmpty ?? true
                                          ? const Text(
                                              'No customers found',
                                              style: TextStyle(
                                                color: Colors.red,
                                                fontWeight: FontWeight.bold,
                                              ),
                                            )
                                          : Expanded(
                                              child: ListView.separated(
                                                separatorBuilder:
                                                    (context, index) =>
                                                        const Divider(
                                                  color: Colors.grey,
                                                ),
                                                shrinkWrap: true,
                                                itemCount: customerMasterList
                                                        ?.length ??
                                                    0,
                                                itemBuilder: (context, index) {
                                                  return ListTile(
                                                    contentPadding:
                                                        const EdgeInsets.all(0),
                                                    onTap: () async {
                                                       final selectedCustomer = customerMasterList?[index];
                                                        final prefs = await SharedPreferences.getInstance();
if (selectedCustomer?.customerType == 'Non Star') {
    log('stock audit false');
    checkInMenuItemsEnabled['Stock Audit'] = false;
    await prefs.setBool('stock_audit_enabled', false);
  } else {
    log('stock audit true');
    checkInMenuItemsEnabled['Stock Audit'] = true;
    await prefs.setBool('stock_audit_enabled', true);
  }
                                                      Navigator.of(context).pop(customerMasterList?[index].customerCode);
                                                    },
                                                    title: Text(
                                                        customerMasterList?[
                                                                    index]
                                                                .customerName ??
                                                            ''),
                                                    subtitle: Text(
                                                        customerMasterList?[
                                                                    index]
                                                                .customerType ??
                                                            ''),
                                                  );
                                                },
                                              ),
                                            ),
                                    ],
                                  );
                                }
                              }
                            }),
                      ),
                    ],
                  ),
                ),
              );
            }),
          );
        });
  }

  Future<List<String>?> selectPurposeofVisit() {
    final List<String> purposeOfVisit = [
      'Competitor Stock Audit',
      'Occasional/Festival Gifts Distribution',
      'Routine Visit',
      'Competitor Schemes Update',
      'Competitor Price Update',
      'Convert to Star Dealer',
      'Convert to Star Sub Dealer',
      'Convert to RSSD',
    ];
    List<int> selectedPurposeOfVisitIndex = [];
    final TextEditingController othersController = TextEditingController();
    // Single/Multiple Selection
    return showDialog(
        context: context,
        barrierDismissible: false,
        builder: (BuildContext contextPurpose) {
          return StatefulBuilder(builder: (contextPurpose, setState) {
            return PopScope(
              canPop: false,
              child: Dialog(
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10)),
                backgroundColor: Colors.white,
                insetPadding: const EdgeInsets.all(20),
                child: Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.symmetric(
                            vertical: 8, horizontal: 10),
                        decoration: BoxDecoration(
                          // color: Colors.red[600],
                          borderRadius: BorderRadius.circular(10),
                        ),
                        alignment: Alignment.center,
                        child: Row(
                          children: [
                            IconButton(
                              icon: const Icon(
                                Icons.arrow_back,
                                color: Colors.red,
                              ),
                              onPressed: () {
                                gotoHome();
                              },
                            ),
                            const Expanded(
                              child: Text(
                                'Select Purpose of Visit',
                                style: TextStyle(
                                  color: Colors.red,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 18,
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 10),
                      Expanded(
                        child: ListView.separated(
                          separatorBuilder: (context, index) => const Divider(
                            color: Colors.grey,
                          ),
                          shrinkWrap: true,
                          itemCount: purposeOfVisit.length,
                          itemBuilder: (context, index) {
                            return ListTile(
                              contentPadding: const EdgeInsets.all(0),
                              onTap: () {
                                setState(() {
                                  if (selectedPurposeOfVisitIndex
                                      .contains(index)) {
                                    selectedPurposeOfVisitIndex.remove(index);
                                  } else {
                                    selectedPurposeOfVisitIndex.add(index);
                                  }
                                });
                              },
                              leading: Checkbox(
                                activeColor: Colors.red,
                                value:
                                    selectedPurposeOfVisitIndex.contains(index),
                                onChanged: (value) {
                                  setState(() {
                                    if (value == true) {
                                      selectedPurposeOfVisitIndex.add(index);
                                    } else {
                                      selectedPurposeOfVisitIndex.remove(index);
                                    }
                                  });
                                },
                              ),
                              title: Text(purposeOfVisit[index]),
                            );
                          },
                        ),
                      ),
                      // Others Text Field
                      Container(
                        padding: const EdgeInsets.all(10),
                        child: TextField(
                          controller: othersController,
                          onChanged: (value) {
                            setState(() {});
                          },
                          decoration: const InputDecoration(
                            labelText: 'Others',
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.all(
                                Radius.circular(10),
                              ),
                            ),
                          ),
                        ),
                      ),
                      (selectedPurposeOfVisitIndex.isEmpty &&
                              othersController.text.trim().isEmpty)
                          ? const SizedBox()
                          :
                          // Submit Button
                          ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                foregroundColor: Colors.white,
                                backgroundColor: Colors.red,
                              ),
                              onPressed: () {
                                final String others =
                                    othersController.text.trim();
                                final List<String> selectedPurposeOfVisit = [];
                                for (int i = 0;
                                    i < selectedPurposeOfVisitIndex.length;
                                    i++) {
                                  selectedPurposeOfVisit.add(purposeOfVisit[
                                      selectedPurposeOfVisitIndex[i]]);
                                }
                                if (others.isNotEmpty) {
                                  selectedPurposeOfVisit.add(others);
                                }

                                Navigator.of(context)
                                    .pop(selectedPurposeOfVisit);
                              },
                              child: const Text('Submit'),
                            ),
                    ],
                  ),
                ),
              ),
            );
          });
        });
  }

  Future<bool?> checkWithinDistance(
      String? baseLattStr, String? baseLongiStr) async {
    final double baseLatt = double.tryParse(baseLattStr ?? '') ?? 0.0;
    final double baseLongi = double.tryParse(baseLongiStr ?? '') ?? 0.0;
    if (baseLatt == 0.0 || baseLongi == 0.0) {
      return true;
    }
    return showDialog(
        context: context,
        builder: (context) {
          final Future<DeterminePosition> locationData =
              DeterminePosition.getPosition(baseLatt, baseLongi, 300.0);
          return PopScope(
            canPop: false,
            child: Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
              backgroundColor: Colors.white,
              insetPadding: const EdgeInsets.all(20),
              clipBehavior: Clip.hardEdge,
              child: Container(
                decoration: const BoxDecoration(
                  // image: DecorationImage(
                  //   image: AssetImage('assets/background.jpg'),
                  //   fit: BoxFit.cover,
                  // ),
                  color: Color.fromARGB(255, 236, 229, 221),
                ),
                padding: const EdgeInsets.all(8.0),
                child: FutureBuilder<DeterminePosition>(
                    future: locationData,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(
                              width: double.infinity,
                              padding: const EdgeInsets.symmetric(
                                  vertical: 8, horizontal: 10),
                              decoration: BoxDecoration(
                                color: Colors.red[600],
                                borderRadius: BorderRadius.circular(10),
                              ),
                              alignment: Alignment.center,
                              child: const Text(
                                'Coverage Area Checking',
                                style: TextStyle(
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold),
                              ),
                            ),
                            const SizedBox(height: 10),
                            const SizedBox(
                              height: 50,
                              width: 50,
                              child: CircularProgressIndicator(
                                color: Colors.black,
                              ),
                            ),
                            const SizedBox(height: 10),
                            const Text('Checking Your Coverage Area...'),
                          ],
                        );
                      }
                      if (snapshot.hasError) {
                        return Text('Error: ${snapshot.error}');
                      } else {
                        final DeterminePosition locationData = snapshot.data ??
                            DeterminePosition('snapshot.error');
                        return Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const SizedBox(height: 10),
                            locationData.isWithinDistance ?? false
                                ? const Text('You are under Coverage Area',
                                    style: TextStyle(fontSize: 16),
                                    textAlign: TextAlign.center)
                                : const Text('You are not under Coverage Area',
                                    style: TextStyle(fontSize: 16),
                                    textAlign: TextAlign.center),
                            const SizedBox(height: 10),
                            ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                foregroundColor: Colors.white,
                                backgroundColor: Colors.red[600],
                              ),
                              onPressed: () {
                                Navigator.of(context)
                                    .pop(locationData.isWithinDistance);
                              },
                              child: const Text('OK'),
                            ),
                          ],
                        );
                      }
                    }),
              ),
            ),
          );
        });
  }

  void showCheckInMenus() async {
    setState(() {
      showMenus = true;
    });
  }

  void setCheckIn(
      String customerCode, String customerName, String customerType) async {
    final Box box = await Hive.openBox('checkIn');
    await box.put('customerCode', customerCode);
    await box.put('checkIn', true);
    // update localDB
    await LocalDB.rawQuery(
      "INSERT INTO app_variables VALUES ('${AppWebService.nickname}', 'customer_code', '$customerCode', 'check_in')",
    );
    await LocalDB.rawQuery(
      "INSERT INTO app_variables VALUES ('${AppWebService.nickname}', 'customer_name', '$customerName', 'check_in')",
    );
    await LocalDB.rawQuery(
      "INSERT INTO app_variables VALUES ('${AppWebService.nickname}', 'customer_type', '$customerType', 'check_in')",
    );
  }

  void setPurposeOfVisit(List<String> purposeOfVisit) async {
    final Box box = await Hive.openBox('checkIn');
    await box.put('purposeOfVisit', purposeOfVisit);
    await LocalDB.rawQuery(
      "INSERT INTO app_variables VALUES ('${AppWebService.nickname}', 'purpose_of_visit', '$purposeOfVisit', 'check_in')",
    );
  }

  void setRouteCode(String routeCode) async {
    final Box box = await Hive.openBox('checkIn');
    await box.put('routeCode', routeCode);
    await LocalDB.rawQuery(
      "INSERT INTO app_variables VALUES ('${AppWebService.nickname}', 'route_code', '$routeCode', 'check_in')",
    );
  }

  void gotoHome() {
    Navigator.of(context).pop();
    // goto home screen
    Navigator.of(context).pushReplacement(
      MaterialPageRoute(
        builder: (context) => const HomeScreen(
          showPopup: false,
        ),
      ),
    );
  }

  void optionsSelectorHandler() async {
    String transactionType = '';
    String routeCode = '';
    String custType = '';
    String customerCode = '';
    CustomerMasterDB customerMasterDB = CustomerMasterDB();
    bool? isWithinDistance;

    final bool isRoutePlanExists = await checkIfRoutePlanExists();
    if (!isRoutePlanExists) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Route Plan not available for today'),
        ),
      );
      // Goto Home Screen
      Navigator.of(context).pop();
      return;
    }

    // while (transactionType.isEmpty ||
    //     routeCode.isEmpty ||
    //     customerCode.isEmpty ||
    //     !(isWithinDistance ?? false)) {
    transactionType = await selectTransactionType() ?? '';
    if (transactionType.isEmpty) {
      gotoHome();
      return;
    }

    routeCode = await selectRoute() ?? '';
    if (routeCode.isEmpty) {
      gotoHome();
      return;
    }

    custType = transactionType == 'Primary'
        ? "'Dealer','SHIP TO PARTY','Exclusive Dealer'"
        : transactionType == 'Secondary'
            ? "'Sub Dealer','Non Star'"
            : 'NA';

    customerCode = await selectCustomer(routeCode, custType) ?? '';
    if (customerCode.isEmpty) {
      Navigator.of(context).pop();
      return;
    }

    customerMasterDB =
        await CustomerMasterDB.getCustomerMasterDBByCustomerCode(customerCode);
    log("Lattitude: ${customerMasterDB.baseLatt} Longitude: ${customerMasterDB.baseLongi}");
    isWithinDistance = await checkWithinDistance(
        customerMasterDB.baseLatt, customerMasterDB.baseLongi);
    // isWithinDistance = true;
    log("isWithinDistance: $isWithinDistance");
    if (!(isWithinDistance ?? true)) {
      gotoHome();
      return;
    }

    List<String> purposeOfVisit = [];
    purposeOfVisit = await selectPurposeofVisit() ?? [];
    if (purposeOfVisit.isEmpty) {
      gotoHome();
      return;
    }
    setCheckIn(customerCode, customerMasterDB.customerName ?? '',
        customerMasterDB.customerType ?? '');
    setPurposeOfVisit(purposeOfVisit);
    setRouteCode(routeCode);
    await LocalDB.rawQuery(
      "INSERT INTO app_variables VALUES ('${AppWebService.nickname}', 'checkintime', '${DateFormat('yyyy-MM-dd hh:mm:ss').format(DateTime.now())}', 'check_in')",
    );
    showCheckInMenus();
  }

  // update sessionID every 5 seconds
  void updateSessionID() {
    setState(() {
      sessionID = DateFormat(sessionDateTimeFormat).format(DateTime.now());
    });
  }

  void checkInMenusInitiator() async {
    // get local user data from localDB
    final user = await UserLoginClass.getLocalUser();
    final String userID = user?.empCode ?? '';
    // final String currentDateString = DateFormat(sessionDateTimeFormat).format(DateTime.now());
    if (widget.isOptionSelected) {
      showCheckInMenus();
    } else {
      optionsSelectorHandler();
    }
    // update sessionID
    updateSessionID();
    final String marketFeedbackSessionID = 'MF$userID$sessionID';
    final String orderSessionID = 'O$userID$sessionID';
    final String collectionSessionID = 'P$userID$sessionID';
    final String stockSessionID = 'NS$userID$sessionID';
    checkInMenuItemsRoutes['Market Feedback'] =
        MarketFeedbackScreen(sessionID: marketFeedbackSessionID);
    Hive.openBox(marketFeedbackSessionID);
    checkInMenuItemsRoutes['Order'] = OrderScreen(sessionID: orderSessionID);
    Hive.openBox(orderSessionID);
    checkInMenuItemsRoutes['Collection'] =
        CollectionScreen(sessionID: collectionSessionID);
    Hive.openBox(collectionSessionID);
    checkInMenuItemsRoutes['Check Out'] = const CheckOut();
    Hive.openBox(stockSessionID);
    checkInMenuItemsRoutes['Stock Audit'] =
        StockAuditScreen(sessionID: orderSessionID);
  }

  @override
  void initState() {
    super.initState();
    Hive.openBox('checkIn');
    WidgetsBinding.instance.addPostFrameCallback((_) {
      checkInMenusInitiator();
    });
    loadMenuState();
  }

  Future<void> loadMenuState() async {
  final prefs = await SharedPreferences.getInstance();
  final stockAuditEnabled = prefs.getBool('stock_audit_enabled') ?? false;

  setState(() {
    checkInMenuItemsEnabled = {
      'Check Out': true,
      'Stock Audit': stockAuditEnabled,
      'Order': true,
      'Market Feedback': true,
      'Collection': true,
    };
  });
}

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      child: Scaffold(
        appBar: const PreferredSize(
          preferredSize: Size.fromHeight(kToolbarHeight),
          child: AppBarWidget(),
        ),
        drawer: DrawerWidget(
          parentContext: context,
        ),
        body: Container(
          width: double.infinity,
          height: double.infinity,
          decoration: const BoxDecoration(
            // image: DecorationImage(
            //   image: AssetImage('assets/background.jpg'),
            //   fit: BoxFit.cover,
            // ),
            color: Color.fromARGB(255, 236, 229, 221),
          ),
          // 3 * 3 Grid View
          child: showMenus
              ? GridView.count(
                  crossAxisCount: 3,
                  children: [
                    for (var item in checkInMenuItems)
                      MenuButtonWidget1(
                        label: item,
                        icon: checkInMenuItemsIcons[item] ?? '',
                        route: checkInMenuItemsRoutes[item],
                        isEnabled: checkInMenuItemsEnabled[item]??true  ,
                      ),
                  ],
                )
              : const SizedBox(),
        ),
      ),
    );
  }

  
}
