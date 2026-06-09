import 'dart:convert';

import 'package:flutter_svg/flutter_svg.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/lead_generation/query_generation_activity_screen.dart';
import 'package:starsfa/screens/lead_generation/lead_generation_activity_screen.dart';
import 'package:starsfa/themes/sfa_theme.dart';

class LeadQueryActivityScreen extends StatefulWidget {
  const LeadQueryActivityScreen({super.key});

  @override
  State<LeadQueryActivityScreen> createState() =>
      _LeadQueryActivityScreenState();
}

class _LeadQueryActivityScreenState extends State<LeadQueryActivityScreen> {
  String emp_code = '';
  bool _isLoading = true;
  int _empCategory = 0;
  @override
  void initState() {
    super.initState();
    _fetchEmployeeCategory();
  }

  Future<void> _fetchEmployeeCategory() async {
    try {
      setState(() {
        _isLoading = true;
      });

      final userDetails = await UserLoginClass.getLocalUser();
      emp_code = userDetails!.empCode ?? '';
      final headers = {
        'Content-Type': 'application/json',
      };
      final response = await http.get(
        Uri.parse('${AppWebService.sbDevUrl}api/employee/?emp_code=$emp_code'),
        headers: headers,
      );
      if (response.statusCode == 200) {
        int empType = 0;
        final responseData = jsonDecode(response.body);
        print(responseData);
        if (responseData[0]['level'].toString().toLowerCase() == 'nt_to') {
          empType = 2;
          setState(() {
            _empCategory = empType;
            _isLoading = false;
          });
        } else if (responseData[0]['level'].toString().toLowerCase() == 'nt') {
          empType = 1;
          setState(() {
            _empCategory = empType;
            _isLoading = false;
          });
        } else {
          setState(() {
            _isLoading = false;
          });
          // ignore: use_build_context_synchronously
          Navigator.of(context).pop();
        }
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
              title: Text(
                "Lead Management",
                style: const TextStyle(color: Colors.white),
              ),
            ),
            body: Container(
              color: const Color.fromARGB(255, 236, 229, 221),
              child: GridView.count(
                crossAxisCount: 3,
                children: [
                  if (_empCategory == 1) ...{
                    MOSimpleButton(
                      title: "Enquiry",
                      icon: 'assets/home_icons/Enquiry.svg',
                      onTap: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (context) =>
                                QueryGenerationActivityScreen(),
                          ),
                        );
                      },
                    ),
                    MOSimpleButton(
                      title: "Lead",
                      icon: 'assets/home_icons/Lead.svg',
                      onTap: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (context) =>
                                LeadGenerationActivityScreen(),
                          ),
                        );
                      },
                    ),
                  },
                  if (_empCategory == 2) ...{
                    MOSimpleButton(
                      title: "Lead",
                      icon: 'assets/home_icons/Lead.svg',
                      onTap: () {
                        Navigator.of(context).push(
                          MaterialPageRoute(
                            builder: (context) =>
                                LeadGenerationActivityScreen(),
                          ),
                        );
                      },
                    ),
                  },
                ],
              ),
            ),
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
      ),
    );
  }
}

class MOSimpleButton extends StatelessWidget {
  final String title;
  final String icon;
  final VoidCallback onTap;

  const MOSimpleButton({
    super.key,
    required this.title,
    required this.icon,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      child: Container(
        margin: SfaTheme.padding,
        decoration: BoxDecoration(
          // ignore: deprecated_member_use
          color: Colors.white,
          borderRadius: SfaTheme.borderRadius,
          border: Border.all(color: Colors.red, width: 2),
          boxShadow: [
            BoxShadow(
              // ignore: deprecated_member_use
              color: Colors.black.withOpacity(0.2),
              blurRadius: 10,
              spreadRadius: 3,
            ),
          ],
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              height: 70,
              width: 70,
              child: Padding(
                padding: const EdgeInsets.all(8),
                child: SvgPicture.asset(icon),
              ),
            ),
            const SizedBox(height: 5),
            Text(
              title,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
