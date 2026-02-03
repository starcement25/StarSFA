// Employee Login Page
// Company Logo
// Fields: Employee ID, Password
// Buttons: Login

// ignore_for_file: use_build_context_synchronously

import 'package:flutter/material.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/data_download_dictionary_sreen.dart';
import 'package:starsfa/screens/home_screen.dart';
import 'package:starsfa/themes/sfa_theme.dart';

class EmpLoginPage extends StatefulWidget {
  const EmpLoginPage({super.key});

  @override
  State<EmpLoginPage> createState() => _EmpLoginPageState();
}

class _EmpLoginPageState extends State<EmpLoginPage> {
  String _empName = '';
  final TextEditingController _empIdController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  bool _isLoading = false;
  bool _isFirstLogin = false;
  UserLoginClass loggedInUser = UserLoginClass();

  void getLocalUser() async {
    loggedInUser = await UserLoginClass.getLocalUser() ?? UserLoginClass();
    if (loggedInUser.empCode != null) {
      setState(() {
        _empIdController.text = loggedInUser.empCode ?? '';
        _empName = loggedInUser.empName ?? '';
      });
    }
  }

  void getLocalUserFirst() async {
    bool isFirstLogin1 = await UserLoginClass.getLocalUserFirstLogin();
    if (loggedInUser.empCode != null) {
      setState(() {
        _isFirstLogin = isFirstLogin1;
      });
    }
  }

  @override
  void initState() {
    super.initState();
    // call after build method is called
    WidgetsBinding.instance.addPostFrameCallback((_) {
      getLocalUser();
      getLocalUserFirst();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.red,
        title: const Text(
          'LOGIN',
          style: TextStyle(color: Colors.white, fontSize: 16.0),
        ),
        centerTitle: true,
        actions: [
          InkWell(
            //  Data Backup
            child: const Row(
              children: [
                Icon(
                  Icons.backup,
                  color: Colors.white,
                ),
                SizedBox(width: 5),
                Text('Data Backup',
                    style: TextStyle(fontSize: 10, color: Colors.white)),
                SizedBox(width: 5),
              ],
            ),
            onTap: () async {
              // show loading indicator popup - Sending Backup
              showDialog(
                  context: context,
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
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Backup submitted successfully.'),
                  ),
                );
              } else {
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Failed to send backup.'),
                  ),
                );
              }
            },
          ),
        ],
      ),
      body: Container(
        // Background Image
        decoration: const BoxDecoration(
          // image: DecorationImage(
          //   image: AssetImage('assets/background.jpg'),
          //   fit: BoxFit.cover,
          // ),
          color: Color.fromARGB(255, 236, 229, 221),
        ),
        child: Center(
          child: Container(
            margin: SfaTheme.padding
                .add(const EdgeInsets.symmetric(horizontal: 20)),
            padding: SfaTheme.padding,
            decoration: const BoxDecoration(
              color: Colors.transparent,
              borderRadius: SfaTheme.borderRadius,
              // iOS Style Backdrop
              // boxShadow: [
              //   BoxShadow(
              //     color: Colors.black.withOpacity(0.2),
              //     blurRadius: 10,
              //     spreadRadius: 5,
              //   ),
              // ],
            ),
            child: Column(
              // mainAxisAlignment: MainAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: <Widget>[
                const SizedBox(
                  height: 50,
                ),
                // Company Logo
                Column(
                  children: [
                    Container(
                      height: 100,
                      width: 100,
                      margin: SfaTheme.padding,
                      // padding: SfaTheme.padding,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.all(Radius.circular(10)),
                        // boxShadow: [SfaTheme.boxShadow],
                      ),
                      clipBehavior: Clip.hardEdge,
                      child: const Image(
                        image: NetworkImage(
                            "${AppWebService.downloadLogoURL}?nick_name=${AppWebService.nickname}"),
                        fit: BoxFit.contain,
                      ),
                      // child:
                      //     SvgPicture.asset('assets/StarSFA_Company Logo.svg'),
                    ),
                    const SizedBox(
                      height: 100,
                    ),
                  ],
                ),
                Expanded(
                  child: SingleChildScrollView(
                    child: Column(children: [
                      // Employee Name
                      if (_empName.isNotEmpty)
                        Padding(
                          padding: const EdgeInsets.all(8.0),
                          child: Text(
                            'Employee Name: $_empName',
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                            ),
                          ),
                        ),
                      // Login Form
                      Form(
                        child: Column(
                          children: <Widget>[
                            // Employee ID
                            Padding(
                              padding: const EdgeInsets.all(8.0),
                              child: TextFormField(
                                controller: _empIdController,
                                decoration: const InputDecoration(
                                  labelText: 'Employee ID',
                                  border: OutlineInputBorder(),
                                ),
                                enabled: _empIdController.text.isEmpty,
                              ),
                            ),
                            // Password
                            Padding(
                              padding: const EdgeInsets.all(8.0),
                              child: TextFormField(
                                controller: _passwordController,
                                decoration: const InputDecoration(
                                  labelText: 'Password',
                                  border: OutlineInputBorder(),
                                ),
                                obscureText:
                                    true, // This line hides the password
                              ),
                            ),
                            // Login Button
                            Padding(
                              padding: const EdgeInsets.all(8.0),
                              child: _isLoading
                                  ? const CircularProgressIndicator()
                                  : ElevatedButton(
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: Colors.red,
                                      ),
                                      onPressed: () async {
                                        // close keyboard
                                        FocusScope.of(context).unfocus();

                                        // Check if the fields are empty
                                        if (_empIdController.text.isEmpty ||
                                            _passwordController.text.isEmpty) {
                                          ScaffoldMessenger.of(context)
                                              .showSnackBar(const SnackBar(
                                                  content: Text(
                                                      'Please fill all the fields')));
                                          return;
                                        }

                                        // check if the user is already logged in
                                        if (loggedInUser.empCode != null) {
                                          // check for the password
                                          if (_passwordController.text.trim() !=
                                              loggedInUser.newPassword
                                                  ?.trim()) {
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              const SnackBar(
                                                content: Text(
                                                    'Password does not match. Please enter the correct password'),
                                              ),
                                            );
                                            return;
                                          }
                                          // Check if the Employee ID and Password are correct
                                          setState(() {
                                            _isLoading = true;
                                          });
                                          final UserLoginClass resp =
                                              await UserLoginClass
                                                  .empLoginCheck(
                                                      _empIdController.text
                                                          .trim(),
                                                      _passwordController.text
                                                          .trim(),
                                                      false);
                                          setState(() {
                                            _isLoading = false;
                                          });
                                          if (resp.message != 'Success') {
                                            // Show Error Message
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content: Text(
                                                    'Login Failed: ${resp.message}'),
                                              ),
                                            );
                                            return;
                                          }
                                          // save user to local db
                                          await UserLoginClass.saveLocalUser(
                                              resp);
                                          // Show success message
                                          ScaffoldMessenger.of(context)
                                              .showSnackBar(
                                            SnackBar(
                                              content: Text(
                                                  'Logged in as ${loggedInUser.empName}'),
                                            ),
                                          );
                                          if (!_isFirstLogin) {
                                            Navigator.pushReplacement(
                                                context,
                                                MaterialPageRoute(
                                                    builder: (context) =>
                                                        const DataDownloadDictionaryScreen(
                                                          incrementalDownload:
                                                              true,
                                                        )));
                                            return;
                                          }
                                          // Navigate to Data Download
                                          // Navigator.pushReplacement(
                                          //     context,
                                          //     MaterialPageRoute(
                                          //         builder: (context) =>
                                          //             const DataDownloadDictionaryScreen(
                                          //               incrementalDownload: true,
                                          //             )));
                                          Navigator.pushReplacement(
                                              context,
                                              MaterialPageRoute(
                                                  builder: (context) =>
                                                      const HomeScreen(
                                                        showPopup: true,
                                                      )));
                                        } else {
                                          // Check if the Employee ID and Password are correct
                                          setState(() {
                                            _isLoading = true;
                                          });
                                          final UserLoginClass resp =
                                              await UserLoginClass
                                                  .empLoginCheck(
                                                      _empIdController.text
                                                          .trim(),
                                                      _passwordController.text
                                                          .trim(),
                                                      true);
                                          setState(() {
                                            _isLoading = false;
                                          });
                                          if (resp.message == 'Success') {
                                            // Show success message
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content: Text(
                                                    'Login Success as ${resp.empName}'),
                                              ),
                                            );
                                            // save user to local db
                                            await UserLoginClass.saveLocalUser(
                                                resp);
                                          } else {
                                            // Show Error Message
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content: Text(
                                                    'Login Failed: ${resp.message}'),
                                              ),
                                            );
                                            return;
                                          }
                                          // Navigate to Data Download
                                          Navigator.pushReplacement(
                                              context,
                                              MaterialPageRoute(
                                                  builder: (context) =>
                                                      const DataDownloadDictionaryScreen(
                                                        incrementalDownload:
                                                            false,
                                                      )));
                                        }
                                      },
                                      child: const Text(
                                        'Login',
                                        style: TextStyle(
                                          color: Colors.white,
                                        ),
                                      ),
                                    ),
                            ),
                          ],
                        ),
                      ),
                    ]),
                  ),
                )
              ],
            ),
          ),
        ),
      ),
    );
  }
}
