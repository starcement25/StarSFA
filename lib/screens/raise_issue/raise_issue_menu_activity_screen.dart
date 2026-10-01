import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:starsfa/screens/raise_issue/raise_issue_activity_screen.dart';
import 'package:starsfa/screens/raise_issue/raise_issue_history_activity_screen.dart';
import 'package:starsfa/themes/sfa_theme.dart';

class RaiseIssueMenuActivityScreen extends StatefulWidget {
  const RaiseIssueMenuActivityScreen({super.key});

  @override
  State<RaiseIssueMenuActivityScreen> createState() =>
      _RaiseIssueMenuActivityScreenState();
}

class _RaiseIssueMenuActivityScreenState
    extends State<RaiseIssueMenuActivityScreen> {
  final List<Map<String, String>> menuItems = [
    {
      'featureName': 'new',
      'label': 'New Issue',
      'iconPath': 'assets/market_overview_icons/new_issue.png',
    },
    {
      'featureName': 'history',
      'label': 'Issue History',
      'iconPath': 'assets/market_overview_icons/issue_history.png',
    },
  ];

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
          'Raise Issue',
          style: TextStyle(color: Colors.white),
        ),
      ),
      backgroundColor: Colors.white,
      body: Container(
        decoration: const BoxDecoration(
          color: Color.fromARGB(255, 236, 229, 221),
        ),
        child: GridView.builder(
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 3,
          ),
          itemCount: menuItems.length,
          itemBuilder: (context, index) {
            final item = menuItems[index];
            return InkWell(
              onTap: () {
                if (item['featureName'] == 'new') {
                  Navigator.of(context).push(
                    MaterialPageRoute(
                      builder: (context) => const RaiseIssueActivityScreen(),
                    ),
                  );
                } else {
                  Navigator.of(context).push(
                    MaterialPageRoute(
                      builder: (context) =>
                          const RaiseIssueHistoryActivityScreen(),
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
                              final path = item['iconPath']!;
                              if (path.isEmpty) {
                                return const SizedBox();
                              }
                              if (path.endsWith('.svg')) {
                                return SvgPicture.asset(path);
                              } else {
                                return Image.asset(path);
                              }
                            },
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
