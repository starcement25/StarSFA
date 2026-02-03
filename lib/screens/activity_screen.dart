import 'dart:convert';
import 'dart:developer';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import 'package:flutter_svg/svg.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/screens/market_feedback_activity_screen.dart';
import 'package:starsfa/screens/market_overview_menu_screen.dart';
import 'package:starsfa/screens/visit_report_activity_screen.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/themes/sfa_theme.dart';
import 'package:geolocator/geolocator.dart';
import 'package:intl/intl.dart';

class ActivityScreen extends StatefulWidget {
  const ActivityScreen({super.key});

  @override
  State<ActivityScreen> createState() => _ActivityScreenState();
}

class _ActivityScreenState extends State<ActivityScreen> {
  bool _showButtonSection = false;
  final List<String> landingMenuItems = [
    'Order',
    'Stock Audit',
    'Collection',
    'Market Overview',
    'Market Feedback',
    // 'Yellow Card',
    'Visit Report',
    'No Activity',
    // 'Khoj',
  ];

  final Map<String, String> landingMenuItemsIcons = {
    'Order': 'assets/check_in_icons/Order.svg',
    'Stock Audit': 'assets/check_in_icons/Stock Audit.svg',
    'Collection': 'assets/check_in_icons/Collection.svg',
    'Market Overview': 'assets/check_in_icons/Market Overview.svg',
    'Market Feedback': 'assets/check_in_icons/Market Feedback.svg',
    // 'Yellow Card': 'assets/home_icons/Yellow Card.svg',
    'Visit Report': 'assets/check_in_icons/Visit Report.svg',
    'No Activity': 'assets/check_in_icons/No Activity.svg',
    // 'Khoj': 'assets/khoj.jpg',
  };

  final Map<String, Widget?> landingMenuItemsWidgets = {
    // 'Order': const GoldenRulesScreen(),
    // 'Stock Audit': const GoldenRulesScreen(),
    // 'Collection': const GoldenRulesScreen(),
    'Market Overview': const MarketOverviewMenuScreen(isFromActivity: true),
    'Market Feedback': const MarketFeedbackActivityScreen(),
    // 'Yellow Card': const GoldenRulesScreen(),
    'Visit Report': const VisitReportActivityScreen(),
    // 'No Activity': const GoldenRulesScreen(),
    // 'Khoj': const VisitReportActivityScreen(),
  };
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        backgroundColor: const Color.fromARGB(255, 236, 229, 221),
        appBar: AppBar(
          title: const Text('Activity', style: TextStyle(color: Colors.white)),
          backgroundColor: Colors.red,
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () {
              Navigator.pop(context);
            },
          ),
        ),
        body: Column(children: [
          Expanded(
            child: GridView.count(
              crossAxisCount: 3,
              children: [
                for (var item in landingMenuItems)
                  ActivityButtonWidget(
                      label: item,
                      icon: landingMenuItemsIcons[item] ?? '',
                      route: landingMenuItemsWidgets[item]),
              ],
            ),
          ),
          // if (_showButtonSection) ButtonSection(),
          IconButton(
            color: Colors.red,
            onPressed: () {
              log('click the arrow button');
              setState(() {
                _showButtonSection = !_showButtonSection;
              });
              log('value of _showButtonSection $_showButtonSection');
            },
            icon: const Icon(
              Icons.arrow_drop_up,
              size: 36.0,
            ),
          )
        ]));
  }
}

class ButtonSection extends StatelessWidget {
  const ButtonSection({super.key});

  @override
  Widget build(BuildContext context) {
    final Color color = Theme.of(context).primaryColor;
    return SizedBox(
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: [
          ButtonWithText(
            color: color,
            icon: Icons.call,
            label: 'Today',
          ),
          ButtonWithText(
            color: color,
            icon: Icons.near_me,
            label: 'MTD',
          ),
          ButtonWithText(
            color: color,
            icon: Icons.share,
            label: 'Date',
          ),
        ],
      ),
    );
  }
}

class ButtonWithText extends StatelessWidget {
  const ButtonWithText({
    super.key,
    required this.color,
    required this.icon,
    required this.label,
  });

  final Color color;
  final IconData icon;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, color: color, size: 28),
        const SizedBox(height: 4),
        Text(label, style: TextStyle(color: color, fontSize: 12)),
      ],
    );
  }
}

class ActivityButtonWidget extends StatelessWidget {
  final String label;
  final String icon;
  final Widget? route;
  const ActivityButtonWidget({
    required this.label,
    required this.icon,
    this.route,
    super.key,
  });

  Future<int> counterMap(String activity) async {
    final Map<String, int> counter = {
      'Order': 0,
      'Stock Audit': 0,
      'Collection': 0,
      'Market Overview': 0,
      'Market Feedback': 0,
      'Yellow Card': 0,
      'Visit Report': 0,
      'No Activity': 0,
    };
    // market overview
    if (activity == 'Market Overview') {
      final data = await LocalDB.rawQuery(
          "SELECT COUNT(*) FROM location WHERE trans_id LIKE 'SU%'");
      log('Market Overview quary: ${data[0]['COUNT(*)']}');

      final khojCount = await _KhojCountInfo();
      log('Market Overview khoj: $khojCount');

      final siteCount = await _SiteCountInfo();
      log('Market Overview site count: $siteCount');

      final count =
          data[0]['COUNT(*)'] + int.parse(khojCount) + int.parse(siteCount);
      log('Market Overview Total: $count');

      return count;
    }
    // market feedback
    if (activity == 'Market Feedback') {
      final data = await LocalDB.rawQuery(
          "SELECT COUNT(*) FROM location WHERE trans_id LIKE 'MF%'");
      log('Market Feedback: $data');
      return data[0]['COUNT(*)'];
    }
    // visit report
    if (activity == 'Visit Report') {
      final data = await LocalDB.rawQuery(
          "SELECT COUNT(*) FROM location WHERE trans_id LIKE 'CI%'");
      log('Visit Report: $data');
      return data[0]['COUNT(*)'];
    }
    return counter[activity] ?? 0;
  }

  Future<String> _KhojCountInfo() async {
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      String emp_code = userDetails!.empCode ?? '';
      final response = await http.get(Uri.parse(
          'https://sfa.starcement.co.in/misreport/api_get_count_site_visit_employee.php?emp_code=' +
              emp_code));
      log("Status Code: ${response.statusCode}");
      log("Response Body: ${response.body}");
      final responseData = jsonDecode(response.body);
      if (responseData['process_status'].toString().toLowerCase() == 'no') {
        return "0";
      } else {
        return responseData['count_visit'].toString();
      }
    } catch (e) {
      return ''; // return something in error case
    }
  }

  Future<String> _SiteCountInfo() async {
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      String emp_code = userDetails!.empCode ?? '';
      final response = await http.get(Uri.parse(
          'https://sfa.starcement.co.in/misreport/api_get_count_new_site_lead.php?emp_code=' +
              emp_code));
      log("Status Code: ${response.statusCode}");
      log("Response Body: ${response.body}");
      final responseData = jsonDecode(response.body);
      if (responseData['process_status'].toString().toLowerCase() == 'no') {
        return "0";
      } else {
        // return responseData['count_visit'].toString();
        return "0";
      }
    } catch (e) {
      return ''; // return something in error case
    }
  }

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () async {
        if (route != null) {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (context) => route ?? Container()),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text('$label is not available'),
            ),
          );
        }
      },
      child: Stack(
        children: [
          Positioned(
            left: 0,
            top: 0,
            right: 0,
            bottom: 0,
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
                  child: icon?.endsWith('.svg') == true
                      ? SvgPicture.asset(
                          icon,
                        )
                      : Image.asset(icon ?? ''),
                ),
              ),
            ),
          ),
          // Counter Badge
          Positioned(
            right: 0,
            top: 0,
            child: FutureBuilder<int>(
                future: counterMap(label),
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return const SizedBox();
                  } else if (snapshot.hasError) {
                    return const SizedBox();
                  } else if (snapshot.hasData) {
                    final data = snapshot.data ?? [];
                    return Container(
                      width: 40,
                      padding: const EdgeInsets.all(5),
                      decoration: const BoxDecoration(
                        color: Colors.red,
                        shape: BoxShape.circle,
                      ),
                      alignment: Alignment.center,
                      child: Text(
                        data.toString(),
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                        ),
                      ),
                    );
                  } else {
                    return const SizedBox();
                  }
                }),
          ),
        ],
      ),
    );
  }
}
