import 'dart:convert';
import 'dart:developer';

import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/survey_input_class.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/lead_generation/lead_generation_activity_screen.dart';
import 'package:starsfa/screens/market_overview_dynamic_form.dart';
import 'package:starsfa/screens/market_overview_dynamic_form_data.dart';
import 'package:starsfa/screens/market_overview_sub_menu_screen.dart';
import 'package:starsfa/screens/new_site_lead/new_site_lead_activity_screen.dart';
import 'package:starsfa/screens/new_site_lead/new_site_lead_deatils_activity_screen.dart';
import 'package:starsfa/screens/site_lead_conversion_tracking/site_lead_conversion_tracking_screen.dart';
import 'package:starsfa/screens/khoj/khoj_activity_screen.dart';
import 'package:starsfa/screens/rsar_meeting/create_rsar_meeting_record_activity_screen.dart';
import 'package:starsfa/themes/sfa_theme.dart';

class MarketOverviewMenuScreen extends StatefulWidget {
  final bool isFromActivity;
  const MarketOverviewMenuScreen({super.key, this.isFromActivity = false});

  @override
  State<MarketOverviewMenuScreen> createState() =>
      _MarketOverviewMenuScreenState();
}

class _MarketOverviewMenuScreenState extends State<MarketOverviewMenuScreen> {
  static String getMenuItemQuery =
      "SELECT DISTINCT survey_sub_menu FROM survey_input";

  late final Future<List<Map<String, dynamic>>> getMenuItemFuture;

  String customMenuNames(String name) {
    switch (name) {
      case 'Branding Verification':
        return 'OOH & WALL WRAP';
      case 'Counter Branding':
        return 'Retail Branding';
      case 'Khoj':
        return 'Khoj';
      case 'New Site Lead and Conversion Tracking':
        return 'New Site Lead and Conversion Tracking';
      default:
        return name;
    }
  }
  String userType = 'ASM';

  @override
  void initState() {
    super.initState();
    getMenuItemFuture =
        SurveyInputClass.getSurveyInputFromLocalDB(getMenuItemQuery);

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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
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
          'Market Overview',
          style: TextStyle(color: Colors.white),
        ),
      ),
      backgroundColor: Colors.white,
      body: Container(
        decoration: const BoxDecoration(
          color: Color.fromARGB(255, 236, 229, 221),
        ),
        child: FutureBuilder<List<Map<String, dynamic>>>(
            future: getMenuItemFuture,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(
                  child: CircularProgressIndicator(),
                );
              } else if (snapshot.hasError) {
                return Center(
                  child: Text('Error: ${snapshot.error}'),
                );
              } else if (snapshot.hasData) {
                if (snapshot.data?.isNotEmpty ?? false) {
                  final List<Map<String, dynamic>> data = (snapshot.data ?? []).where((item) {
                    log(userType);
                    log(item['survey_sub_menu']);
                    if (userType.contains('ASM') &&
                        item['survey_sub_menu'].toString().toLowerCase() == 'New Site Lead and Conversion Tracking'.toLowerCase()) {
                      return false; // hide for ASM
                    }
                    if(item['survey_sub_menu'].toString().toLowerCase() == 'New Site Lead and Conversion Tracking'.toLowerCase()){
                      return false;
                    }
                    return true;
                    
                  }).toList();
                  log('dataset' + snapshot.data.toString());
                  return GridView.builder(
                    gridDelegate:
                        const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 3,
                    ),
                    itemCount: data.length,
                    itemBuilder: (context, index) {
                      return SubMenuItems(
                        isFromActivity: widget.isFromActivity,
                        showName: customMenuNames(
                            data[index]['survey_sub_menu'].toString()),
                        menuName: data[index]['survey_sub_menu'].toString(),
                        mainMenuName: data[index]['survey_sub_menu'].toString(),
                      );
                    },
                  );
                } else {
                  return const Center(
                    child: Text('No Data'),
                  );
                }
              } else {
                return const Center(
                  child: Text('No Data'),
                );
              }
            }),
      ),
    );
  }
}

class SubMenuItems extends StatefulWidget {
  final bool isFromActivity;
  final String menuName;
  final String mainMenuName;
  final String showName;
  const SubMenuItems(
      {super.key,
      required this.menuName,
      required this.showName,
      required this.mainMenuName,
      this.isFromActivity = false});

  @override
  State<SubMenuItems> createState() => _SubMenuItemsState();
}

class _SubMenuItemsState extends State<SubMenuItems> {
  late Future<List<Map<String, String>>> getMenuItemFuture;

  Future<List<Map<String, String>>> getMenuItem() async {
    final String query =
        "SELECT display_name,row_id FROM survey_input WHERE survey_sub_menu = '${widget.menuName}' AND type = 'menu'";
    final List<Map<String, dynamic>> displayNamesResult =
        await SurveyInputClass.getSurveyInputFromLocalDB(query);
    final List<Map<String, String>> displayNames = displayNamesResult
        .map((e) => {
              'display_name': e['display_name'].toString(),
              'row_id': e['row_id'].toString()
            })
        .toList();
    return displayNames;
  }

  Map<String, String> menuIcons = {
    'KYC': 'assets/market_overview_icons/KYC.svg',
    'Site Visit': 'assets/market_overview_icons/Site Visit.svg',
    'Technical Meets': 'assets/market_overview_icons/Technical Meet.svg',
    'Branding': 'assets/market_overview_icons/Branding.svg',
    'Branding Verification': 'assets/market_overview_icons/OOH & Wall Wrap.svg',
    'Dhalai Services': 'assets/market_overview_icons/Dhalai Services.svg',
    'Site Lead and Conversion Tracking':
        'assets/market_overview_icons/Site Lead & Conversion Tracking.svg',
    'Complaint Report': 'assets/market_overview_icons/Complaint Report.svg',
    'Counter Branding': 'assets/market_overview_icons/Retail Branding.svg',
    'Corporate Branding': 'assets/market_overview_icons/Corporate Branding.svg',
    'Lead Generation': 'assets/market_overview_icons/Lead Generation.svg',
    'Khoj': 'assets/Khoj.svg',
    'New Site Lead and Conversion Tracking': 'assets/siteleadsvg.svg',
  };

  Future<List<Map<String, dynamic>>> getMenuDataCount(String menuName) async {
    final String query =
        "SELECT DISTINCT survey_id FROM survey_header WHERE survey_type = '$menuName'";
    final List<Map<String, dynamic>> displayNamesResult =
        await LocalDB.rawQuery(query);
    return displayNamesResult;
  }

  @override
  void initState() {
    super.initState();
    getMenuItemFuture = getMenuItem();
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Map<String, String>>>(
        future: getMenuItemFuture,
        builder: (context, snapshot) {
          final List<Map<String, String>> data = snapshot.data ?? [];
          log('Data D: $data');
          return InkWell(
            onTap: () async {
              if (data.isNotEmpty) {
                log('Hello World ${widget.menuName}');
                final List<Map<String, String>> displayNames = data;
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (context) => MarketOverviewSubMenuScreen(
                      menuName: widget.menuName,
                      menuItems: displayNames,
                      isFromActivity: widget.isFromActivity,
                      mainMenuName: widget.menuName,
                    ),
                  ),
                );
              } else {
                log('Hello World1 ${widget.menuName}');

                // if (widget.menuName.trim().toLowerCase() == 'lead generation') {
                //   log('Hello World it working fine 222');
                //   Navigator.of(context).push(
                //     MaterialPageRoute(
                //         builder: (context) =>
                //             const CreateRsarMeetingRecordActivityScreen()),
                //   );
                // } else 
                if (widget.menuName.trim().toLowerCase() == 'khoj') {
                  log('Hello World it working fine 111');
                  Navigator.of(context).push(
                    MaterialPageRoute(
                      builder: (context) => widget.isFromActivity
                          ? const KhojActivityScreen()
                          : const SiteLeadConversionTrackingScreen(),
                    ),
                  );
                } else if (widget.menuName.trim().toLowerCase() ==
                    'new site lead and conversion tracking') {
                  log('Hello World it working fine 222');
                  Navigator.of(context).push(
                    MaterialPageRoute(
                      builder: (context) => widget.isFromActivity
                          ? const NewSiteLeadDeatilsActivityScreen()
                          : const NewSiteLeadActivityScreen(),
                    ),
                  );
                } else {
                  Navigator.of(context).push(
                    MaterialPageRoute(
                      builder: (context) => widget.isFromActivity
                          ? MarketOverviewDynamicFormData(
                              menuName: widget.menuName,
                              showName: widget.showName,
                              itemRowId: '',
                            )
                          : MarketOverviewDynamicForm(
                              menuName: widget.menuName,
                              showName: widget.showName,
                              menuId: '',
                              isMenu: true,
                              mainMenuName: widget.menuName,
                            ),
                    ),
                  );
                }
              }
            },
            child: Stack(
              children: [
                Positioned(
                  top: 0,
                  left: 0,
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
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.2),
                          blurRadius: 10,
                          spreadRadius: 5,
                        ),
                      ],
                    ),
                    child: Container(
                      height: 60,
                      width: 60,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        borderRadius: SfaTheme.borderRadius,
                        boxShadow: [SfaTheme.boxShadow],
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(5),
                        child: Builder(
                          builder: (_) {
                            final path = menuIcons[widget.menuName];

                            if (path == null || path.isEmpty) {
                              return SvgPicture.asset('assets/siteleadsvg.svg');
                            }

                            if (path.endsWith('.svg')) {
                              return SvgPicture.asset(path);
                            } else {
                              return Image.asset(path);
                            }
                          },
                        ),
                        // menuIcons[widget.menuName]?.endsWith('.svg') == true
                        //     ? SvgPicture.asset(
                        //         menuIcons[widget.menuName] ?? '',
                        //       )
                        //     : Image.asset(menuIcons[widget.menuName] ?? ''),
                      ),
                    ),
                  ),
                ),
                if (widget.isFromActivity)
                  Positioned(
                    right: 0,
                    top: 0,
                    child: FutureBuilder<List<Map<String, dynamic>>>(
                        future: getMenuDataCount(widget.menuName),
                        builder: (context, snapshot) {
                          if (snapshot.connectionState ==
                              ConnectionState.waiting) {
                            return const SizedBox();
                          } else if (snapshot.hasError) {
                            return const SizedBox();
                          } else if (snapshot.hasData) {
                            final List<Map<String, dynamic>> data =
                                snapshot.data ?? [];
                            return data.isEmpty
                                ? const SizedBox()
                                : Container(
                                    width: 40,
                                    padding: const EdgeInsets.all(5),
                                    decoration: const BoxDecoration(
                                      color: Colors.red,
                                      shape: BoxShape.circle,
                                    ),
                                    alignment: Alignment.center,
                                    child: Text(
                                      data.length.toString(),
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
        });
  }
}
