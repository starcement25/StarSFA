import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/screens/emp_login_page.dart';
import 'package:http/http.dart' as http;
import 'package:url_launcher/url_launcher.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  bool showPopup = false;
  bool showButtonPopup = false;
  String? popupMessage = "";
  String? bodyLink;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      checkAppStatus();
    });
  }

  Future<void> checkAppStatus() async {
    try {
      final response = await http.get(
        Uri.parse('${AppWebService.baseURL}sfa_downtime_api.php'),
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);

        if (data["status"] == "success") {
          String appStatus = data["app_status"];
          popupMessage = data["body_message"];
          String isLinkAvailable = data["is_link_available"];
          bodyLink = data["body_link"];

          if (appStatus == "start") {
            gotoNextPage();
          } else if (appStatus == "stop") {
            showButtonPopup = isLinkAvailable == "Y";
            _showCustomPopup();
          }
        } else {
          gotoNextPage(); // fallback if status is not success
        }
      } else {
        gotoNextPage(); // fallback on failed response
      }
    } catch (e) {
      gotoNextPage(); // fallback on exception
    }
  }

  void _showCustomPopup() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text('Maintenance'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 16),
                Text(
                  popupMessage ?? '',
                  style: TextStyle(fontWeight: FontWeight.normal),
                ),
                const SizedBox(height: 16),
              ],
            ),
          ),
          actions: [
            Row(
              children: [
                showButtonPopup
                    ? Expanded(
                        child: OutlinedButton(
                          onPressed: () async {
                            if (bodyLink != null &&
                                await canLaunchUrl(Uri.parse(bodyLink!))) {
                              await launchUrl(
                                Uri.parse(bodyLink!),
                                mode: LaunchMode.externalApplication,
                              );
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                    content: Text("Could not open the link")),
                              );
                            }
                          },
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(color: Colors.red),
                          ),
                          child: const Text(
                            'Open Website',
                            style: TextStyle(color: Colors.red),
                          ),
                        ),
                      )
                    : const SizedBox(height: 1),
              ],
            ),
          ],
        );
      },
    );
  }

  void gotoNextPage() {
    Future.delayed(const Duration(seconds: 3), () {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(
          builder: (context) => const EmpLoginPage(),
        ),
      );
    });
  }

  @override
  Widget build(BuildContext context) {
    // NetworkService().initialize(context);
    return Scaffold(
      body: Image.asset(
        'assets/Splash Screen.png',
        fit: BoxFit.cover,
        width: double.infinity,
        height: double.infinity,
      ),
    );
  }
}
