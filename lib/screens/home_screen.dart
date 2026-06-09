// ignore_for_file: public_member_api_docs, sort_constructors_first, use_build_context_synchronously
import 'dart:convert';
import 'dart:math';

import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:starsfa/db_setup/DataForDownloading.dart';
import 'package:starsfa/log/log_service.dart';
import 'package:starsfa/log/route_checkin_checkout.dart';
import 'package:starsfa/main.dart';
import 'package:starsfa/models/app_files_upload.dart';
import 'package:flutter/cupertino.dart';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/attendance_class.dart';
import 'package:starsfa/models/determine_position.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/location_class.dart';
import 'package:starsfa/models/market_overview_data_class.dart';
import 'package:starsfa/models/route_plan_transaction_class.dart';
import 'package:starsfa/models/complaint_report.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/activity_screen.dart';
import 'package:starsfa/screens/check_in_menus.dart';
import 'package:starsfa/screens/customer_outstanding/customer_wise_outstanding_screen.dart';
import 'package:starsfa/screens/data_download_dictionary_sreen.dart';
import 'package:starsfa/screens/dealer_declaration/declaration_request.dart';
import 'package:starsfa/screens/emp_login_page.dart';
import 'package:starsfa/screens/golden_rules_screen.dart';
import 'package:starsfa/screens/help_screen.dart';
import 'package:starsfa/screens/manager_activity_screen.dart';
import 'package:starsfa/screens/market_overview_menu_screen.dart';
import 'package:starsfa/screens/outstanding_screen.dart';
import 'package:starsfa/screens/route_plan_screen.dart';
import 'package:starsfa/screens/sis_summary_screen.dart';
import 'package:starsfa/screens/new_site_lead/new_site_lead_list_activity_screen.dart';
import 'package:starsfa/screens/target_achieved/target_achievement_screen.dart';
import 'package:starsfa/screens/track_order_screen.dart';
import 'package:starsfa/screens/dashboard1.dart';
import 'package:starsfa/themes/sfa_theme.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:flutter_svg/flutter_svg.dart';

class HomeScreen extends StatefulWidget {
  final bool showPopup;
  const HomeScreen({super.key, required this.showPopup});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  String userName = 'N/A';
  String userType = 'ASM';
  int _lostOrderCount = 0;
  final List<String> landingMenuItems = [
    'Route Plan',
    'Check In',
    'SIS Report',
    'Site Lead Approval',
    'Track Order',
    'Market Overview',
    'Target',
    'Activity',
    'Dashboard',
    // 'New Site Lead Approval',
    // 'Lead Funnel Management',
    'Outstanding Report',
    // 'Lead Quotation List',
  ];

  final Map<String, String> landingMenuItemsIcons = {
    'Route Plan': 'assets/home_icons/Route Plan.svg',
    'Check In': 'assets/home_icons/Check In.svg',
    'SIS Report': 'assets/home_icons/SIS Report.svg',
    'Site Lead Approval': 'assets/home_icons/Site Lead Approval.svg',
    'Track Order': 'assets/home_icons/Track Order.svg',
    'Market Overview': 'assets/home_icons/Market Overview.svg',
    'Target': 'assets/home_icons/Target Achieved.svg',
    'Activity': 'assets/home_icons/Activity.svg',
    'Dashboard': 'assets/home_icons/Dashboard.svg',
    // 'New Site Lead Approval': 'assets/home_icons/New Site Lead Approval.svg',
    // 'Lead Funnel Management': 'assets/home_icons/Lead Funnel.svg',
    'Outstanding Report': 'assets/home_icons/outstanding.svg',
    // 'Lead Quotation List': 'assets/home_icons/Lead Funnel.svg',
  };

  final Map<String, Widget?> landingMenuItemsRoutes = {
    'Route Plan': const RoutePlanScreen(),
    'Check In': const CheckInMenus(),
    'SIS Report': const SisSummaryScreen(),
    'Site Lead Approval': const NewSiteLeadListActivityScreen(),
    'Track Order': const TrackOrderScreen(),
    'Market Overview': const MarketOverviewMenuScreen(),
    'Target': const TargetAchievementScreen(),
    'Activity': const ActivityScreen(),
    'Dashboard': const DashboardScreen1(),
    // 'New Site Lead Approval': const SiteLeadApprovalScreen(),
    // 'New Site Lead Approval': const NewSiteLeadListActivityScreen(),
    // 'Lead Funnel Management': const LeadGenerationGraphActivityScreen(),
    'Outstanding Report': const CustomerWiseOutstandingScreen(),
    // 'Lead Quotation List': const LeadQuotationListActivityScreen(),
  };

  void getLocalUser() async {
    final user = await UserLoginClass.getLocalUser() ?? UserLoginClass();
    userName = user.empName ?? 'N/A';
    await ComplaintReport.getComplaintReportData();
    setState(() {});
  }

  // go check in
  void goCheckIn() async {
    await Future.delayed(const Duration(seconds: 3));
    final resp = await LocalDB.rawQuery(
        "SELECT * FROM app_variables WHERE operation_type = 'check_in'");
    if (resp.isNotEmpty) {
      await Hive.openBox('checkIn');
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => const CheckInMenus(
            isOptionSelected: true,
          ),
        ),
      );
    }
  }

  @override
  void initState() {
    super.initState();
    checkEmployeeCategory();
    callDOBchecker();
    checkVersion();
    createNewTableAndAddAllDataForCustomerAgeing();
    createNewTableAndAddAllDataForCustomerQuantity();
  }

  void checkEmployeeCategory() async {
    final localDB = await LocalDB.openMyDatabase();
    final user = await UserLoginClass.getLocalUser();
    final List<Map<String, dynamic>> employeeData = await localDB.rawQuery(
        "SELECT * FROM emp_master WHERE emp_code = '${user?.empCode}'");
    print("SELECT * FROM emp_master WHERE emp_code = '${user?.empCode}'");
    if (employeeData.isNotEmpty) {
      final employeeCategory = employeeData[0]['sale_access'];
      print("======================");
      print(employeeCategory);
      print("======================");
      if (employeeCategory == 'BD') {
        setState(() {
          landingMenuItems.remove('Route Plan');
          landingMenuItems.remove('Check In');
          // landingMenuItems.remove('SIS Report');
          landingMenuItems.remove('Site Lead Approval');
          landingMenuItems.remove('Track Order');
          landingMenuItems.remove('Target');
          landingMenuItems.remove('Dashboard');
          landingMenuItems.remove('Outstanding Report');
        });
      }
    }
  }

  // add new data for market feedback
  void createNewTableAndAddAllDataForCustomerQuantity() async {
    await LocalDB.createCustomerQuantityTable();
    await _fetchAndInsertCustomerQuantity();
  }

  void createNewTableAndAddAllDataForCustomerAgeing() async {
    await LocalDB.createCustomerAgeingTable();
    await LocalDB.createCustomerAgeingInvoiceNoTable();
    print("AGEING DATABASE CREATED");
  }

  Future<void> _fetchAndInsertCustomerQuantity() async {
    try {
      final user = await UserLoginClass.getLocalUser();
      final String url =
          '${AppWebService.baseURL}misreport/get_competitor_qty.php?emp_code=${user?.empCode}';
      print(url);
      final response = await http.get(Uri.parse(url));

      if (response.statusCode == 200) {
        int noColumn = -1;
        final lines = response.body.split('\n');

        for (String line in lines) {
          if (line.trim().isEmpty) continue;

          if (line.contains('¥')) {
            // header line — extract column count
            final parts = line.split('¥');
            noColumn = int.tryParse(parts[1].trim()) ?? -1;
            print('Column count: $noColumn');
          } else if (line.contains('#')) {
            // skip this line
            continue;
          } else {
            // data line
            final rowData = ('$line ').split('^');

            if (rowData.length == noColumn) {
              await LocalDB.insertCustomerQuantity({
                'customer_code': rowData[1].trim(),
                'customer_name': rowData[2].trim(),
                'competitor_code': rowData[3].trim(),
                'competitor_name': rowData[3].trim(),
                'quantity': rowData[4].trim(),
                'flag': 0,
              });
            }
          }
        }

        print('Customer Quantity data inserted successfully');
      } else {
        print('API Error: ${response.statusCode}');
      }
    } catch (e) {
      print('Failed to fetch customer quantity: $e');
    }
  }

  void checkVersion() async {
    final user = await UserLoginClass.getLocalUser();
    await LogService.logSetup('New Version Use : ${user?.empCode} [1.0.9]');
  }

  void primaryFunction() {
    getLocalUser();
    Hive.openBox(RoutePlanCalendar.routePlanBoxKey);
    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      if (widget.showPopup) {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => const GoldenRulesScreen(),
          ),
        );
      }
    });
    goCheckIn();
    _loadUserType();
  }

  void callDOBchecker() {
    Future.delayed(Duration.zero, () {
      checkDOB(context);
    });
  }

  Future checkDOB(BuildContext context) async {
    final user = await UserLoginClass.getLocalUser();
    final response = await http.get(Uri.parse(
        "${AppWebService.baseURL}misreport/get-employee-dob.php?emp_code=${user?.empCode}"));

    final data = jsonDecode(response.body);

    if (data["status"] == true) {
      BirthdayPopup.show(
          context: context,
          name: data["emp_name"],
          title: data["title"],
          message: data["message"],
          imageUrl: data["img"],
          emp_code: user?.empCode ?? '',
          onClose: primaryFunction);
    } else {
      if (data["message"] == "Birthday Not Found") {
        DOBPopup.show(
            context: context,
            emp_code: user?.empCode ?? '',
            recall: callDOBchecker);
      } else {
        primaryFunction();
      }
    }
  }

  Future<void> _loadUserType() async {
    final user = await UserLoginClass.getLocalUser();
    final response = await http.get(Uri.parse(
      '${AppWebService.baseURL}misreport/api_get_employee_detail_site_lead.php?emp_code=${user?.empCode}',
    ));

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      setState(() {
        userType = data['designation']?.toString() ?? '';
      });
    }
  }

  List<String> getFilteredLandingMenuItems() {
    return landingMenuItems.where((item) {
      if (item == 'New Site Lead Approval') {
        // return userType.contains('ASM'); // ✅ show only for ASM
        return false;
      }
      return true; // show all others
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    final filteredMenuItems = getFilteredLandingMenuItems();
    return Scaffold(
      appBar: const PreferredSize(
        preferredSize: Size.fromHeight(kToolbarHeight),
        child: AppBarWidget(),
      ),
      // Drawer
      drawer: DrawerWidget(
        parentContext: context,
      ),
      body: Container(
        // Background Image
        decoration:
            const BoxDecoration(color: Color.fromARGB(255, 236, 229, 221)),
        child: GridView.count(
          crossAxisCount: 3,
          children: [
            for (var item in filteredMenuItems) ...[
              if (item == 'Lead Funnel Management' ||
                  item == 'Lead Quotation List') ...[
                MOSimpleButton(
                    label: item,
                    icon: landingMenuItemsIcons[item] ?? '',
                    route: landingMenuItemsRoutes[item],
                    lostOrderCount: _lostOrderCount,
                    onReturn: () {}),
              ] else ...[
                MenuButtonWidget(
                  label: item,
                  icon: landingMenuItemsIcons[item] ?? '',
                  route: landingMenuItemsRoutes[item],
                ),
              ],
            ]
          ],
        ),
      ),
    );
  }
}

class BirthdayPopup {
  static void show({
    required BuildContext context,
    required String name,
    required String title,
    required String message,
    required String imageUrl,
    required String emp_code,
    required VoidCallback onClose,
  }) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) {
        return Dialog(
          backgroundColor: Colors.transparent,
          child: Stack(
            children: [
              Container(
                height: 350,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(15),
                  image: DecorationImage(
                    image: NetworkImage(imageUrl),
                    fit: BoxFit.cover,
                  ),
                ),
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  alignment: Alignment.center,
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        title,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 10),
                      Text(
                        name,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 10),
                      Text(
                        message,
                        style: const TextStyle(color: Colors.white),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 10),
                      const Text(
                        "~ Star Cement Family",
                        style: TextStyle(color: Colors.white),
                      ),
                    ],
                  ),
                ),
              ),

              /// Close Button
              Positioned(
                right: 5,
                top: 5,
                child: IconButton(
                  icon: const Icon(Icons.close, color: Colors.white),
                  onPressed: () async {
                    Navigator.pop(context);
                    await callSeenAPI(emp_code);
                    onClose();
                  },
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  static Future callSeenAPI(String emp_code) async {
    await http.post(
      Uri.parse("${AppWebService.baseURL}misreport/birthday_wish_seen.php"),
      body: {
        "emp_code": emp_code,
      },
    );
  }
}

class DOBPopup {
  static void show({
    required BuildContext context,
    required String emp_code,
    required VoidCallback recall,
  }) {
    DateTime? selectedDate;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) {
        return StatefulBuilder(builder: (context, setState) {
          Future<void> pickDate() async {
            final DateTime today = DateTime(
              DateTime.now().year,
              DateTime.now().month,
              DateTime.now().day,
            );

            final DateTime? picked = await showCupertinoModalPopup(
              context: context,
              builder: (_) {
                DateTime tempDate = selectedDate ?? today;

                return Container(
                  height: 250,
                  color: Colors.white,
                  child: Column(
                    children: [
                      /// Done Button
                      Align(
                        alignment: Alignment.centerRight,
                        child: TextButton(
                          onPressed: () {
                            Navigator.pop(context, tempDate);
                          },
                          child: const Text("Done"),
                        ),
                      ),

                      Expanded(
                        child: CupertinoDatePicker(
                          mode: CupertinoDatePickerMode.date,
                          maximumDate: today,
                          initialDateTime: tempDate,
                          onDateTimeChanged: (date) {
                            tempDate = date;
                          },
                        ),
                      ),
                    ],
                  ),
                );
              },
            );

            if (picked != null) {
              setState(() {
                selectedDate = picked;
              });
            }
          }

          return Dialog(
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
            child: Container(
              height: 220,
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      "Your Date of Birth not updated.",
                      style: TextStyle(fontSize: 14),
                    ),
                  ),
                  Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                      "Please update your Date of Birth...",
                      style: TextStyle(fontSize: 14),
                    ),
                  ),
                  const SizedBox(height: 25),

                  /// DOB BUTTON
                  ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.grey.shade200,
                      foregroundColor: Colors.black,
                      minimumSize: const Size(double.infinity, 45),
                    ),
                    onPressed: pickDate,
                    child: Text(
                      selectedDate == null
                          ? "Select DOB"
                          : "${selectedDate!.day.toString().padLeft(2, '0')}-${selectedDate!.month.toString().padLeft(2, '0')}-${selectedDate!.year}",
                    ),
                  ),

                  const SizedBox(height: 25),

                  /// SUBMIT BUTTON
                  ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.red,
                      minimumSize: const Size(double.infinity, 40),
                    ),
                    onPressed: () async {
                      if (selectedDate == null) return;

                      String dob =
                          "${selectedDate!.day.toString().padLeft(2, '0')}-${selectedDate!.month.toString().padLeft(2, '0')}-${selectedDate!.year}";

                      Navigator.pop(context);
                      await updateDOBAPI(emp_code, dob);
                      recall();
                    },
                    child: const Text("Submit",
                        style: TextStyle(color: Colors.white)),
                  ),
                ],
              ),
            ),
          );
        });
      },
    );
  }

  static Future updateDOBAPI(String emp_code, String dob) async {
    await http.post(
      Uri.parse("${AppWebService.baseURL}misreport/update_employee_dob.php"),
      body: {
        "emp_code": emp_code,
        "dob": dob,
      },
    );
  }
}

class MenuButtonWidget extends StatelessWidget {
  final String label;
  final String icon;
  final Widget? route;
  const MenuButtonWidget({
    required this.label,
    required this.icon,
    this.route,
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () async {
        final isAttendance = await AttendanceClass.getAttendanceByDate(
            DateFormat('yyyy-MM-dd').format(DateTime.now()));
        if (label == 'Market Overview') {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (context) => route ?? Container()),
          );
        } else if (!isAttendance && label != 'Route Plan') {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Please mark your attendance first.'),
            ),
          );
          return;
        } else {
          if (route != null) {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => route ?? Container()),
            );
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text('$label is not available for the Customer.'),
              ),
            );
          }
        }
      },
      child: Container(
        margin: SfaTheme.padding,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.7),
          borderRadius: SfaTheme.borderRadius,
          border: Border.all(
            color: Colors.red,
            width: 2,
          ),
          // iOS Style Backdrop
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.2),
              blurRadius: 5,
              spreadRadius: 2,
            ),
          ],
        ),
        child: Container(
          height: 60,
          width: 60,
          // margin: SfaTheme.padding,
          // padding: SfaTheme.padding,
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: SfaTheme.borderRadius,
            boxShadow: [SfaTheme.boxShadow],
          ),
          child: Padding(
            padding: const EdgeInsets.all(5),
            child: SvgPicture.asset(
              icon,
            ),
          ),
        ),
      ),
    );
  }
}

class MenuButtonWidget1 extends StatelessWidget {
  final String label;
  final String icon;
  final Widget? route;
  final bool isEnabled;
  const MenuButtonWidget1({
    required this.label,
    required this.icon,
    required this.isEnabled,
    this.route,
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () async {
        print("click $label and $isEnabled");
        if (isEnabled) {
          final isAttendance = await AttendanceClass.getAttendanceByDate(
              DateFormat('yyyy-MM-dd').format(DateTime.now()));
          if (!isAttendance && label != 'Route Plan') {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text('Please mark your attendance first.'),
              ),
            );
            return;
          }
          if (route != null) {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => route ?? Container()),
            );
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text('$label is not available for the Customer.'),
              ),
            );
          }
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('$label is not available for the Customer.'),
            ),
          );
        }
      },
      child: Container(
        margin: SfaTheme.padding,
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(0.7),
          borderRadius: SfaTheme.borderRadius,
          border: Border.all(
            color: Colors.red,
            width: 2,
          ),
          // iOS Style Backdrop
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.2),
              blurRadius: 5,
              spreadRadius: 2,
            ),
          ],
        ),
        child: Container(
          height: 60,
          width: 60,
          // margin: SfaTheme.padding,
          // padding: SfaTheme.padding,
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: SfaTheme.borderRadius,
            boxShadow: [SfaTheme.boxShadow],
          ),
          child: Padding(
            padding: const EdgeInsets.all(5),
            child: SvgPicture.asset(
              icon,
            ),
          ),
        ),
      ),
    );
  }
}

class MOSimpleButton extends StatelessWidget {
  final String label;
  final String icon;
  final Widget? route;
  final int lostOrderCount;
  final VoidCallback? onReturn;

  const MOSimpleButton({
    required this.label,
    required this.icon,
    required this.lostOrderCount,
    this.route,
    this.onReturn,
    super.key,
  });

  String? get _badgeLabel {
    if (lostOrderCount <= 0) return null;
    return lostOrderCount > 9 ? '9+' : '$lostOrderCount';
  }

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () async {
        if (route != null) {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (context) => route ?? Container()),
          ).then((_) {
            onReturn?.call();
          });
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('$label is not available for the Customer.'),
            ),
          );
        }
      },
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          AspectRatio(
            aspectRatio: 1.0,
            child: Container(
              margin: SfaTheme.padding,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: SfaTheme.borderRadius,
                border: Border.all(color: Colors.red, width: 2),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.2),
                    blurRadius: 10,
                    spreadRadius: 3,
                  ),
                ],
              ),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Flexible(
                    flex: 3,
                    child: Padding(
                      padding: const EdgeInsets.all(8),
                      child: SvgPicture.asset(
                        icon,
                        fit: BoxFit.contain,
                      ),
                    ),
                  ),
                  Flexible(
                    flex: 2,
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 4),
                      child: Text(
                        label,
                        textAlign: TextAlign.center,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          if (_badgeLabel != null)
            Positioned(
              top: 0,
              right: 0,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 2),
                decoration: BoxDecoration(
                  color: Colors.red,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: Colors.white, width: 1.5),
                ),
                constraints: const BoxConstraints(minWidth: 18, minHeight: 18),
                child: Text(
                  _badgeLabel!,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                  ),
                  textAlign: TextAlign.center,
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class DrawerWidget extends StatefulWidget {
  final BuildContext parentContext;
  const DrawerWidget({
    Key? key,
    required this.parentContext,
  }) : super(key: key);

  @override
  State<DrawerWidget> createState() => _DrawerWidgetState();
}

class _DrawerWidgetState extends State<DrawerWidget> {
  String userName = 'N/A';
  PackageInfo? packageInfo;

  void _synchronizeData() async {
    Navigator.pop(context);
    // Show popup - Uploading Pending Data
    showDialog(
      context: widget.parentContext,
      builder: (context) {
        return const AlertDialog(
          title: Text('Uploading Pending Data'),
          content: Text('Please wait while we upload the pending data.'),
          actions: [
            // loading indicator
            Center(child: CircularProgressIndicator()),
          ],
        );
      },
    );

    final user = await UserLoginClass.getLocalUser();
    final downloader = DataForDownloading();
    await downloader.downloadAllSBGData(user?.empCode ?? '');

    // Upload Pending Data

    // Route Plan
    await LogService.logSetup('_synchronizeData RoutePlanTransaction Start');
    bool upload =
        await RoutePlanTransactionClass.saveRoutePlanTransactionServer();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Route Plan data.'),
        ),
      );
      await LogService.logSetup(
          '_synchronizeData RoutePlanTransaction Upload failed');
    } else {
      await LogService.logSetup(
          '_synchronizeData RoutePlanTransaction Upload success');
    }
    await LogService.logSetup('_synchronizeData RoutePlanTransaction end');

    // Attendance
    await LogService.logSetup('_synchronizeData Attendance Start');
    upload = await AttendanceClass.saveAttendanceServer();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Attendance data.'),
        ),
      );
      await LogService.logSetup('_synchronizeData Attendance Upload failed');
    } else {
      await LogService.logSetup('_synchronizeData Attendance Upload success');
    }
    await LogService.logSetup('_synchronizeData Attendance end');

    // Market Overview
    await LogService.logSetup('_synchronizeData MarketOverview Start');
    upload = await MarketOverviewDataClass.uploadMarketOverviewData();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Market Overview data.'),
        ),
      );
      await LogService.logSetup(
          '_synchronizeData MarketOverview Upload failed');
    } else {
      await LogService.logSetup(
          '_synchronizeData MarketOverview Upload success');
    }
    await LogService.logSetup('_synchronizeData MarketOverview end');

    await LogService.logSetup('_synchronizeData RouteCheckinCheckout Start');
    upload = await RouteCheckinCheckout.updateRouteCheckinCheckout();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Market Feedback data.'),
        ),
      );
      await LogService.logSetup(
          '_synchronizeData RouteCheckinCheckout Upload failed');
    } else {
      await LogService.logSetup(
          '_synchronizeData RouteCheckinCheckout Upload success');
    }
    await LogService.logSetup('_synchronizeData RouteCheckinCheckout end');

    // Image Upload
    await LogService.logSetup('AppFilesUpload Start');
    upload = await AppFilesUpload.uploadImageZip1();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Image data. Or no Image for upload.'),
        ),
      );
      await LogService.logSetup('AppFilesUpload Upload failed');
    } else {
      await LogService.logSetup('AppFilesUpload Upload success');
    }
    await LogService.logSetup('AppFilesUpload end');

    // get current context
    final curentContext = StarSFA.getCurrentContext();
    Navigator.pop(curentContext ?? widget.parentContext);

    // Navigate to Data Download
    Navigator.pushAndRemoveUntil(
      widget.parentContext,
      MaterialPageRoute(
          builder: (context) => const DataDownloadDictionaryScreen(
                incrementalDownload: true,
                isSync: true,
              )),
      (route) => false,
    );
  }

  void _submitDaysReport() async {
    Navigator.pop(context);
    // check if the attendance is marked
    final isAttendance = await AttendanceClass.getAttendanceByDate(
        DateFormat('yyyy-MM-dd').format(DateTime.now()));
    if (!isAttendance) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Please mark your attendance first.'),
        ),
      );
      return;
    }
    // check if the time is before 6 PM
    if (DateTime.now().hour < 18) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Please submit after 18:00 Hrs.'),
        ),
      );
      return;
    }

    // Show popup - Uploading Pending Data
    showDialog(
      context: widget.parentContext,
      builder: (_) {
        return const AlertDialog(
          title: Text('Uploading Pending Data'),
          content: Text('Please wait while we upload the pending data.'),
          actions: [
            Center(child: CircularProgressIndicator()),
          ],
        );
      },
    );

    // Navigator.pop(context);

    // <<<<<<<<<<<<<<<<<<<<<---Route Plan--->>>>>>>>>>>>>>>>>>>>>
    await LogService.logSetup('_submitDaysReport RoutePlanTransaction Start');
    bool upload =
        await RoutePlanTransactionClass.saveRoutePlanTransactionServer();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Route Plan data.'),
        ),
      );
      Navigator.of(widget.parentContext, rootNavigator: true).pop();
      // await LogService.logSetup('_submitDaysReport RoutePlanTransaction Upload failed');
    } else {
      await LogService.logSetup(
          '_submitDaysReport RoutePlanTransaction Upload success');
    }
    await LogService.logSetup('_submitDaysReport RoutePlanTransaction End');
    // <<<<<<<<<<<<<<<<<<<<<---Route Plan--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Market Overview--->>>>>>>>>>>>>>>>>>>>>
    await LogService.logSetup(
        '_submitDaysReport MarketOverviewDataClass Start');
    upload = await MarketOverviewDataClass.uploadMarketOverviewData();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Market Overview data.'),
        ),
      );
      // Navigator.of(widget.parentContext, rootNavigator: true).pop();
      await LogService.logSetup(
          '_submitDaysReport MarketOverviewDataClass Upload failed');
    } else {
      await LogService.logSetup(
          '_submitDaysReport MarketOverviewDataClass Upload success');
    }
    await LogService.logSetup('_submitDaysReport MarketOverviewDataClass End');
    // <<<<<<<<<<<<<<<<<<<<<---Market Overview--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Attendance--->>>>>>>>>>>>>>>>>>>>>
    await LogService.logSetup('_submitDaysReport Attendance Start');
    upload = await AttendanceClass.saveAttendanceServer();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Attendance data.'),
        ),
      );
      // Navigator.of(widget.parentContext, rootNavigator: true).pop();
      await LogService.logSetup('_submitDaysReport Attendance Upload failed');
    } else {
      await LogService.logSetup('_submitDaysReport Attendance Upload success');
    }
    await LogService.logSetup('_submitDaysReport Attendance End');
    // <<<<<<<<<<<<<<<<<<<<<---Attendance--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Route Checkin Checkout--->>>>>>>>>>>>>>>>>>>>>
    await LogService.logSetup('_submitDaysReport RouteCheckinCheckout Start');
    upload = await RouteCheckinCheckout.updateRouteCheckinCheckout();
    if (!upload) {
      Navigator.pop(context);
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Market Feedback data.'),
        ),
      );
      await LogService.logSetup(
          '_synchronizeData RouteCheckinCheckout Upload failed');
    } else {
      await LogService.logSetup(
          '_synchronizeData RouteCheckinCheckout Upload success');
    }
    await LogService.logSetup('_synchronizeData RouteCheckinCheckout end');
    // <<<<<<<<<<<<<<<<<<<<<---Route Checkin Checkout--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Image upload--->>>>>>>>>>>>>>>>>>>>>
    await LogService.logSetup('_submitDaysReport AppFilesUpload Start');
    upload = await AppFilesUpload.uploadImageZip1();
    if (!upload) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Image data. Or no Image for upload.'),
        ),
      );
      // Navigator.of(widget.parentContext, rootNavigator: true).pop();
      await LogService.logSetup(
          '_submitDaysReport AppFilesUpload Upload failed');
    } else {
      await LogService.logSetup(
          '_submitDaysReport AppFilesUpload Upload success');
    }
    await LogService.logSetup('_submitDaysReport AppFilesUpload End');
    // <<<<<<<<<<<<<<<<<<<<<---Image upload--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Close popup--->>>>>>>>>>>>>>>>>>>>>
    Navigator.of(widget.parentContext, rootNavigator: true).pop();
    await LogService.logSetup('_submitDaysReport Uploading popup close');
    // <<<<<<<<<<<<<<<<<<<<<---Close popup--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Data collece and location pick--->>>>>>>>>>>>>>>>>>>>>
    final user = await UserLoginClass.getLocalUser();
    final String transId =
        "CH${user?.empCode}${DateFormat('yyyyMMddhhmmss').format(DateTime.now())}";
    final String date =
        DateFormat('yyyy-MM-dd HH:mm:ss').format(DateTime.now());
    final location = await DeterminePosition.getPosition(null, null, null);
    if (location.latitude == null || location.longitude == null) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to get the location, Please try again.'),
        ),
      );
      await LogService.logSetup('_submitDaysReport Location not pick');
    } else {
      await LogService.logSetup('_submitDaysReport Location picked');
    }
    // <<<<<<<<<<<<<<<<<<<<<---Data collece and location pick--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Save data in local store--->>>>>>>>>>>>>>>>>>>>>
    await LocationClass.saveLocation(LocationClass(
        empCode: user?.empCode.toString() ?? '',
        transId: transId,
        latt: location.latitude.toString(),
        longi: location.longitude.toString(),
        date: date));
    await LogService.logSetup('_submitDaysReport Location saved in local db');
    // <<<<<<<<<<<<<<<<<<<<<---Save data in local store--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Updated attendance into surver--->>>>>>>>>>>>>>>>>>>>>
    await AttendanceClass(
      empCode: user?.empCode.toString() ?? '',
      transId: transId,
      date: date,
      flag: '0',
    ).saveAttendance();
    await LogService.logSetup('_submitDaysReport Attendance saved in surver');
    // <<<<<<<<<<<<<<<<<<<<<---Updated attendance into surver--->>>>>>>>>>>>>>>>>>>>>

    // <<<<<<<<<<<<<<<<<<<<<---Checkout API Call--->>>>>>>>>>>>>>>>>>>>>
    final bool isUploaded = await AttendanceClass.updateCheckoutServer(transId);
    await LogService.logSetup('_submitDaysReport Checkout upload $isUploaded');
    if (!isUploaded) {
      // ScaffoldMessenger.of(context).showSnackBar(
      //   const SnackBar(
      //     content: Text('Unable to upload to server.'),
      //   ),
      // );
      await LogService.logSetup('_submitDaysReport Checkout upload failed');
    } else {
      // ScaffoldMessenger.of(context).showSnackBar(
      //   const SnackBar(
      //     content: Text('Report submitted successfully.'),
      //   ),
      // );
      await LogService.logSetup('_submitDaysReport Checkout upload success');
    }
    // <<<<<<<<<<<<<<<<<<<<<---Checkout API Call--->>>>>>>>>>>>>>>>>>>>>
  }

  void getLocalUser() async {
    final user = await UserLoginClass.getLocalUser() ?? UserLoginClass();
    userName = user.empName ?? 'N/A';
    setState(() {});
  }

  void getAppVersion() async {
    PackageInfo packageInfoLocal = await PackageInfo.fromPlatform();
    setState(() {
      packageInfo = packageInfoLocal;
    });
  }

  @override
  void initState() {
    super.initState();
    getLocalUser();
    getAppVersion();
  }

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: [
          // Drawer Header
          DrawerHeader(
              decoration: const BoxDecoration(
                color: Colors.red,
              ),
              // Logged in User Details
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const CircleAvatar(
                      radius: 40,
                      backgroundColor: Colors.white,
                      child: Icon(
                        Icons.person,
                        size: 50,
                        color: Colors.red,
                      )),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Text(
                      userName,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              )),

          // Drawer Menu Items
          //  Help
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.help),
                SizedBox(width: 10),
                Text('Help', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const HelpScreen(),
                ),
              );
            },
          ),
          //  Manager Activity
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.person),
                SizedBox(width: 10),
                Text('Manager Activity', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const ManagerActivityScreen(),
                ),
              );
            },
          ),
          //  Schemes
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.card_giftcard),
                SizedBox(width: 10),
                Text('Schemes', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () {
              // close the drawer
              Navigator.pop(context);
              // show popup
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text(
                      'No Scheme found in your database, Please contact Admin.'),
                ),
              );
            },
          ),
          // Outstanding
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.money),
                SizedBox(width: 10),
                Text('Outstanding', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const OutstandingScreen(),
                ),
              );
            },
          ),
          //  Data Backup
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.backup),
                SizedBox(width: 10),
                Text('Data Backup', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () async {
              Navigator.pop(context);
              // show loading indicator popup - Sending Backup
              showDialog(
                  context: widget.parentContext,
                  builder: (context) {
                    return const AlertDialog(
                      title: Text('Sending Backup'),
                      content:
                          Text('SFA Data Backup is in progress. Please wait.'),
                      actions: [
                        // loading indicator
                        Center(
                            child: CircularProgressIndicator(
                          color: Colors.red,
                        )),
                      ],
                    );
                  });
              final resp = await LocalDB.backupDB();
              if (resp) {
                Navigator.pop(widget.parentContext);
                ScaffoldMessenger.of(widget.parentContext).showSnackBar(
                  const SnackBar(
                    content: Text('Backup submitted successfully.'),
                  ),
                );
              } else {
                Navigator.pop(widget.parentContext);
                ScaffoldMessenger.of(widget.parentContext).showSnackBar(
                  const SnackBar(
                    content: Text('Failed to send backup.'),
                  ),
                );
              }
            },
          ),
          //  Logout
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.logout),
                SizedBox(width: 10),
                Text('Logout', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () {
              // Move to the login screen
              Navigator.pushReplacement(
                  context,
                  MaterialPageRoute(
                      builder: (context) => const EmpLoginPage()));
            },
          ),
          // Synchronize Data
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.sync),
                SizedBox(width: 10),
                Text('Synchronize Data', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () {
              _synchronizeData();
            },
          ),
          // Declaration Request
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.mark_email_unread),
                SizedBox(width: 10),
                Text('Exclusive dealer declaration request',
                    style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const DeclarationRequest(),
                ),
              );
            },
          ),
          // Submit Day's Report
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.send),
                SizedBox(width: 10),
                Text('Submit Day\'s Report', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () async {
              _submitDaysReport();
            },
          ),
          //  Notification Hub
          ListTile(
            title: const Row(
              children: [
                Icon(Icons.notifications_active),
                SizedBox(width: 10),
                Text('Notification Hub', style: TextStyle(fontSize: 20)),
              ],
            ),
            onTap: () {},
          ),

          if (packageInfo != null)
            Container(
              width: double.infinity,
              margin: const EdgeInsets.all(10),
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(10),
                color: Colors.red,
              ),
              alignment: Alignment.center,
              child: Text(
                'App Version: ${packageInfo?.version ?? 'N/A'}',
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class AppBarWidget extends StatefulWidget {
  const AppBarWidget({super.key});

  @override
  State<AppBarWidget> createState() => _AppBarWidgetState();
}

class _AppBarWidgetState extends State<AppBarWidget> {
  void loadingAttendancePopup() {
    showDialog(
      context: context,
      builder: (context) {
        return const PopScope(
          canPop: false,
          child: AlertDialog(
            title: Text('Attendance'),
            content: Text('Please wait while we mark your attendance.'),
            actions: [
              // loading indicator
              Center(child: CircularProgressIndicator()),
            ],
          ),
        );
      },
    );
  }

  void attendance() async {
    // get local user
    final user = await UserLoginClass.getLocalUser() ?? UserLoginClass();
    // get the user id
    final empCode = user.empCode;
    // get the current date
    final date = DateFormat('yyyy-MM-dd hh:mm:ss').format(DateTime.now());
    final dateTime = DateFormat('yyyyMMddhhmmss').format(DateTime.now());
    final transId = 'A$empCode$dateTime';
    // check if attendance is already marked
    final isAttendance =
        await AttendanceClass.getAttendanceByDate(date.split(' ')[0]);
    if (isAttendance) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Attendance already marked for today.'),
        ),
      );
      return;
    }
    // get the current location
    final location = await DeterminePosition.getPosition(null, null, null);
    print('Location: ${location.latitude}, ${location.longitude}');
    // check if location is null
    if (location.latitude == null || location.longitude == null) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Unable to get the location, Please try again.'),
        ),
      );
      return;
    }
    await LocationClass.saveLocation(LocationClass(
        empCode: empCode.toString(),
        transId: transId,
        latt: location.latitude.toString(),
        longi: location.longitude.toString(),
        date: date));
    final AttendanceClass attendance = AttendanceClass(
        empCode: empCode.toString(), transId: transId, date: date, flag: '0');
    final bool isUploaded = await attendance.saveAttendance();
    if (!isUploaded) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Attendance marked, but unable to upload to server.'),
        ),
      );
      return;
    }
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Attendance marked successfully.'),
      ),
    );
    Navigator.pop(context);
  }

  @override
  Widget build(BuildContext context) {
    return AppBar(
      toolbarHeight: 70,
      backgroundColor: Colors.red,
      centerTitle: true,
      title: Container(
        height: 60,
        width: 60,
        margin: const EdgeInsets.all(10),
        padding: const EdgeInsets.all(3),
        decoration: BoxDecoration(
          // color: Colors.white.withOpacity(0.8),
          borderRadius: BorderRadius.circular(10),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.2),
              blurRadius: 10,
              spreadRadius: 5,
            ),
          ],
        ),
        clipBehavior: Clip.hardEdge,
        child: Image.network(
          "${AppWebService.downloadLogoURL}?nick_name=${AppWebService.nickname}",
          // height: 60,
        ),
      ),
      // backgroundColor: Colors.white.withOpacity(0.5),
      actions: [
        // Notification Icon
        IconButton(
          onPressed: () {},
          icon: const Icon(
            Icons.notifications,
            color: Colors.white,
          ),
        ),
        // Attendance Icon
        InkWell(
          onTap: () {
            loadingAttendancePopup();
            attendance();
          },
          child: SvgPicture.asset(
            // Attendance Icon
            'assets/home_icons/Attendance Icon.svg',
            // color white
            height: 30,
            width: 30,
          ),
        ),
        const SizedBox(width: 10),
      ],
    );
  }
}
