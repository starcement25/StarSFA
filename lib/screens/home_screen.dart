// ignore_for_file: public_member_api_docs, sort_constructors_first, use_build_context_synchronously
import 'dart:convert';
import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:starsfa/main.dart';
import 'package:starsfa/models/app_files_upload.dart';

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
import 'package:starsfa/screens/data_download_dictionary_sreen.dart';
import 'package:starsfa/screens/dealer_declaration/declaration_request.dart';
import 'package:starsfa/screens/emp_login_page.dart';
import 'package:starsfa/screens/golden_rules_screen.dart';
import 'package:starsfa/screens/help_screen.dart';
import 'package:starsfa/screens/leaderboard_screen.dart';
import 'package:starsfa/screens/manager_activity_screen.dart';
import 'package:starsfa/screens/market_overview_menu_screen.dart';
import 'package:starsfa/screens/outstanding_screen.dart';
import 'package:starsfa/screens/route_plan_screen.dart';
import 'package:starsfa/screens/sis_summary_bd_screen.dart';
import 'package:starsfa/screens/sis_summary_screen.dart';
import 'package:starsfa/screens/site_lead_approval_screen.dart';
import 'package:starsfa/screens/site_lead_conversion_tracking/site_lead_conversion_tracking_screen.dart';
import 'package:starsfa/screens/lead_generation/lead_generation_report_activity_screen.dart';
import 'package:starsfa/screens/new_site_lead/new_site_lead_list_activity_screen.dart';
import 'package:starsfa/screens/target_screen.dart';
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
  final List<String> landingMenuItems = [
    'Route Plan',
    'Check In',
    'SIS Report',
    'BD SIS Report',
    'Leaderboard',
    'Site Lead Approval',
    'Track Order',
    'Market Overview',
    'Target',
    'Activity',
    'Dashboard',
    'New Site Lead Approval',
    'Lead Generation Report'
  ];

  final Map<String, String> landingMenuItemsIcons = {
    'Route Plan': 'assets/home_icons/Route Plan.svg',
    'Check In': 'assets/home_icons/Check In.svg',
    'SIS Report': 'assets/home_icons/SIS Report.svg',
    'BD SIS Report': 'assets/home_icons/BD SIS.svg',
    'Leaderboard': 'assets/home_icons/Leaderboard.svg',
    'Site Lead Approval': 'assets/home_icons/Site Lead Approval.svg',
    'Track Order': 'assets/home_icons/Track Order.svg',
    'Market Overview': 'assets/home_icons/Market Overview.svg',
    'Target': 'assets/home_icons/Target Achieved.svg',
    // 'Yellow Card': Icons.warning,
    'Activity': 'assets/home_icons/Activity.svg',
    'Dashboard': 'assets/home_icons/Dashboard.svg',
    'New Site Lead Approval':'assets/home_icons/New Site Lead Approval.svg',
    'Lead Generation Report':'assets/home_icons/New Site Lead Approval.svg'
  };

  final Map<String, Widget?> landingMenuItemsRoutes = {
    'Route Plan': const RoutePlanScreen(),
    'Check In': const CheckInMenus(),
    'SIS Report': const SisSummaryScreen(),
    'BD SIS Report': const SisSummaryBDScreen(),
    'Leaderboard': const LeaderboardScreen(),
    'Site Lead Approval': const SiteLeadApprovalScreen(),
    'Track Order': const TrackOrderScreen(),
    'Market Overview': const MarketOverviewMenuScreen(),
    'Target': const TargetScreen(),
    // 'Yellow Card': const YellowCardScreen(),
    'Activity': const ActivityScreen(),
    'Dashboard': const DashboardScreen1(),
    'New Site Lead Approval': const NewSiteLeadListActivityScreen(),
    'Lead Generation Report': const LeadGenerationReportActivityScreen(),
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

  Future<void> _loadUserType() async {
     final user = await UserLoginClass.getLocalUser();
    final response = await http.get(Uri.parse(
      'https://sfa.starcement.co.in/misreport/api_get_employee_detail_site_lead.php?emp_code=${user?.empCode}',
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
        decoration: const BoxDecoration(
            color: Color.fromARGB(255, 236, 229, 221)),
        child: GridView.count(
          crossAxisCount: 3,
          children: [
            for (var item in filteredMenuItems)
              MenuButtonWidget(
                label: item,
                icon: landingMenuItemsIcons[item] ?? '',
                route: landingMenuItemsRoutes[item],
              ),
          ],
        ),
      ),
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
        log("click $label and $isEnabled");
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
}else{
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
    // Upload Pending Data
    // Route Plan
    bool upload =
        await RoutePlanTransactionClass.saveRoutePlanTransactionServer();
    if (!upload) {
      Navigator.pop(context);
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Route Plan data.'),
        ),
      );
      // return;
    }
    // Attendance
    upload = await AttendanceClass.saveAttendanceServer();
    if (!upload) {
      Navigator.pop(context);
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Attendance data.'),
        ),
      );
      // return;
    }
    // Market Overview
    upload = await MarketOverviewDataClass.uploadMarketOverviewData();
    if (!upload) {
      Navigator.pop(context);
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Market Overview data.'),
        ),
      );
      // return;
    }
    // Image Upload
    upload = await AppFilesUpload.uploadImageZip1();
    if (!upload) {
      Navigator.pop(context);
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Image data. Or no Image for upload.'),
        ),
      );
      // return;
    }
    
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
                )
          ),
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
    // upload pending data
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
    // Upload Pending Data
    // Route Plan
    bool upload =
        await RoutePlanTransactionClass.saveRoutePlanTransactionServer();
    if (!upload) {
      Navigator.pop(context);
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Route Plan data.'),
        ),
      );
      // return;
    }
    // Attendance
    upload = await AttendanceClass.saveAttendanceServer();
    if (!upload) {
      Navigator.pop(context);
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Attendance data.'),
        ),
      );
      // return;
    }
    // Market Overview
    upload = await MarketOverviewDataClass.uploadMarketOverviewData();
    if (!upload) {
      Navigator.pop(context);
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload Market Overview data.'),
        ),
      );
      // return;
    }

    Navigator.pop(context);
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
      // return;
    }
    await LocationClass.saveLocation(LocationClass(
        empCode: user?.empCode.toString() ?? '',
        transId: transId,
        latt: location.latitude.toString(),
        longi: location.longitude.toString(),
        date: date));
    await AttendanceClass(
      empCode: user?.empCode.toString() ?? '',
      transId: transId,
      date: date,
      flag: '0',
    ).saveAttendance();
    final bool isUploaded = await AttendanceClass.updateCheckoutServer(transId);
    if (!isUploaded) {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Unable to upload to server.'),
        ),
      );
      return;
    } else {
      ScaffoldMessenger.of(widget.parentContext).showSnackBar(
        const SnackBar(
          content: Text('Report submitted successfully.'),
        ),
      );
    }
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
                Text('Exclusive dealer declaration request', style: TextStyle(fontSize: 20)),
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
    log('Location: ${location.latitude}, ${location.longitude}');
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
