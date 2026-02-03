// ignore_for_file: must_be_immutable

import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/screens/market_overview_dynamic_form.dart';
import 'package:starsfa/screens/market_overview_dynamic_form_data.dart';
import 'package:starsfa/themes/sfa_theme.dart';

class MarketOverviewSubMenuScreen extends StatefulWidget {
  final bool isFromActivity;
  final String menuName;
  final String mainMenuName;
  final List<Map<String, String>> menuItems;
  const MarketOverviewSubMenuScreen(
      {super.key,
      required this.menuItems,
      required this.menuName,
      this.isFromActivity = false,
      required this.mainMenuName});

  @override
  State<MarketOverviewSubMenuScreen> createState() =>
      _MarketOverviewSubMenuScreenState();
}

class _MarketOverviewSubMenuScreenState
    extends State<MarketOverviewSubMenuScreen> {
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
        title: Text(
          widget.menuName,
          style: const TextStyle(color: Colors.white),
        ),
      ),
      body: Container(
        decoration: const BoxDecoration(
          // image: DecorationImage(
          //   image: AssetImage('assets/background.jpg'),
          //   fit: BoxFit.cover,
          // ),
          color: Color.fromARGB(255, 236, 229, 221),
        ),
        child: GridView.builder(
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 3,
          ),
          itemCount: widget.menuItems.length,
          itemBuilder: (context, index) {
            return MOSubMenuItems(
              isFromActivity: widget.isFromActivity,
              displayName:
                  widget.menuItems[index]['display_name'] ?? 'Not Found',
              rowId: widget.menuItems[index]['row_id'] ?? '',
            );
          },
        ),
      ),
    );
  }
}

class MOSubMenuItems extends StatelessWidget {
  final bool isFromActivity;
  final String rowId;
  final String displayName;
  MOSubMenuItems(
      {super.key,
      required this.displayName,
      required this.rowId,
      this.isFromActivity = false});
  String customMenuNames(String name) {
    switch (name) {
      case 'Branding Verification':
        return 'OOH & WALL WRAP';
      case 'Counter Branding':
        return 'Retail Branding';
      case 'RA280':
        return 'Complaint Report';
      case 'RA281':
        return 'Complaint Report';
      default:
        return name;
    }
  }

  Map<String, String> menuIcons = {
    'Counter Meet': 'assets/technical_meet_icons/Counter Meet.svg',
    'Big Counter Meet': 'assets/technical_meet_icons/Big Counter Meet.svg',
    'Mega Mason Meet': 'assets/technical_meet_icons/Mega Mason Meet.svg',
    'Dhalai Meet': 'assets/technical_meet_icons/Dhalai Meet.svg',
    'Engineers Meet': 'assets/technical_meet_icons/Engineers Meet.svg',
    'Professional Visit': 'assets/technical_meet_icons/Professional Visit.svg',
    'Startech': 'assets/technical_meet_icons/Startech.svg',
    'Contractor Meet': 'assets/technical_meet_icons/Contractor Meet.svg',
    'Plant Visit': 'assets/technical_meet_icons/Plant visit.svg',
    'Dealer/Subdealer Visit':
        'assets/technical_meet_icons/Dealer_subdealer visit.svg',
    'Complain': 'assets/technical_meet_icons/Complain.svg',
    'Mason Meet': 'assets/technical_meet_icons/Mason Meet.svg',
    'IHB Meet': 'assets/technical_meet_icons/IHB Meet.svg',
    'Small Engineers Meet':
        'assets/technical_meet_icons/Small Engineers Meet.svg',
    'Big Contractor Meet':
        'assets/technical_meet_icons/Big Contractor Meet.svg',
    'Catch Them Young': 'assets/technical_meet_icons/Catch Them Young.svg',
    'PC One Day Training Programme':
        'assets/technical_meet_icons/PC1day Programme.svg',
    'Customer Guidance Camp':
        'assets/technical_meet_icons/Customer Guidance Camp.svg',
    'New': 'assets/complaint_report_icons/New Icon.svg',
    'Existing': 'assets/complaint_report_icons/Existing Icon.svg',
  };

  Future<List<Map<String, dynamic>>> getMenuDataCount(String menuName) async {
    final String query =
        "SELECT DISTINCT survey_id FROM survey_header WHERE survey_type = '$menuName'";
    final List<Map<String, dynamic>> displayNamesResult =
        await LocalDB.rawQuery(query);
    return displayNamesResult;
  }

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () async {
        if (rowId.isNotEmpty) {
          Navigator.of(context).push(
            MaterialPageRoute(
              builder: (context) => isFromActivity
                  ? MarketOverviewDynamicFormData(
                      menuName: rowId == 'RA280'
                          ? customMenuNames(rowId)
                          : rowId == 'RA281'
                              ? customMenuNames(rowId)
                              : displayName,
                      showName: customMenuNames(displayName),
                      itemRowId: rowId,
                    )
                  : MarketOverviewDynamicForm(
                      menuName: rowId == 'RA280'
                          ? customMenuNames(rowId)
                          : rowId == 'RA281'
                              ? customMenuNames(rowId)
                              : displayName,
                      showName: customMenuNames(displayName),
                      menuId: rowId,
                      mainMenuName: customMenuNames(rowId),
                    ),
            ),
          );
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
                // iOS Style Backdrop
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
                    menuIcons[displayName] ?? '',
                  ),
                ),
              ),
            ),
          ),
          if (isFromActivity)
            // Counter Badge
            Positioned(
              right: 0,
              top: 0,
              child: FutureBuilder<List<Map<String, dynamic>>>(
                  future: getMenuDataCount(displayName),
                  builder: (context, snapshot) {
                    if (snapshot.connectionState == ConnectionState.waiting) {
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
  }
}
