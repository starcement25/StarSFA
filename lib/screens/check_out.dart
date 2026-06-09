// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:intl/intl.dart';
import 'package:starsfa/log/route_checkin_checkout.dart';
import 'package:starsfa/models/determine_position.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/location_class.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/screens/home_screen.dart';

class CheckOut extends StatefulWidget {
  const CheckOut({super.key});

  @override
  State<CheckOut> createState() => _CheckOutState();
}

class _CheckOutState extends State<CheckOut> {
  String checkOutMessage = '';

  Future<bool> ischeckOut1(BuildContext context) async {
    final String customerCode = await LocalDB.rawQuery(
      "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'customer_code'",
    ).then((value) => value[0]['variable_value']);

    final user = await UserLoginClass.getLocalUser();
    final mfStkAuditIdLike =
        '${user?.empCode}${DateFormat('yyyyMMdd').format(DateTime.now())}';
    final List<Map<String, Object?>> mfStkAuditHeaderValue =
        await LocalDB.rawQuery(
      "SELECT * FROM `mf_stk_audit_header` WHERE `mf_stk_audit_id` LIKE '%$mfStkAuditIdLike%' AND customer_code = '$customerCode'",
    );

    await Hive.openBox('cart');
    final cart = await Hive.openBox('cart');
    final bool flag = cart.get('flag', defaultValue: true);
    print('Flag: $flag');

    // ─── Check Market Feedback ───
    if (mfStkAuditHeaderValue.isEmpty || flag) {
      setState(() {
        checkOutMessage = 'At least one Market Feedback is required';
      });
      return false;
    }

    // ─── Check SBG only if SBG is visible AND was opened ───
    final Box checkInBox = Hive.box('checkIn');
    final bool isShowSBG = checkInBox.get('isShowSBG', defaultValue: false);
    final bool sbgMenuOpened =
        checkInBox.get('sbgMenuOpened', defaultValue: false);

    print('isShowSBG: $isShowSBG | sbgMenuOpened: $sbgMenuOpened');

    if (isShowSBG && sbgMenuOpened) {
      final bool sbgSubmitted =
          checkInBox.get('sbgSubmitted', defaultValue: false);
      print('sbgSubmitted: $sbgSubmitted');
      if (!sbgSubmitted) {
        setState(() {
          checkOutMessage = 'Please submit SBG Feedback before checking out';
        });
        return false;
      }
    }

    cart.put('flag', true);
    return true;
  }

  Future<bool> ischeckOut(BuildContext context) async {
    final String customerCode = await LocalDB.rawQuery(
      "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'customer_code'",
    ).then((value) => value[0]['variable_value']);

    final user = await UserLoginClass.getLocalUser();
    final mfStkAuditIdLike =
        '${user?.empCode}${DateFormat('yyyyMMdd').format(DateTime.now())}';
    final List<Map<String, Object?>> mfStkAuditHeaderValue =
        await LocalDB.rawQuery(
      "SELECT * FROM `mf_stk_audit_header` WHERE `mf_stk_audit_id` LIKE '%$mfStkAuditIdLike%' AND customer_code = '$customerCode'",
    );

    await Hive.openBox('cart');
    final cart = Hive.box('cart');
    final bool flag = cart.get('flag', defaultValue: true);
    print('Flag: $flag');

    // ─── Check Market Feedback ───
    if (mfStkAuditHeaderValue.isEmpty || flag) {
      setState(() {
        checkOutMessage = 'At least one Market Feedback is required';
      });
      return false;
    }

    // ─── Check SBG ───
    final Box checkInBox = await Hive.openBox('checkIn'); // ✅ ensure open
    final bool isShowSBG = checkInBox.get('isShowSBG', defaultValue: false);
    final bool sbgMenuOpened =
        checkInBox.get('sbgMenuOpened', defaultValue: false);

    print('isShowSBG: $isShowSBG | sbgMenuOpened: $sbgMenuOpened');

    if (isShowSBG) {
      // ✅ Block checkout if SBG menu was never opened
      if (!sbgMenuOpened) {
        setState(() {
          checkOutMessage =
              'Please open and submit SBG Feedback before checking out';
        });
        return false;
      }

      // ✅ Block checkout if SBG was opened but not submitted
      final bool sbgSubmitted =
          checkInBox.get('sbgSubmitted', defaultValue: false);
      print('sbgSubmitted: $sbgSubmitted');
      if (!sbgSubmitted) {
        setState(() {
          checkOutMessage = 'Please submit SBG Feedback before checking out';
        });
        return false;
      }
    }

    cart.put('flag', true);
    return true;
  }

  void checkOut() async {
    final bool isCheckOut = await ischeckOut(context);
    if (isCheckOut) {
      // Upload data to server
      final String checkInTime = await LocalDB.rawQuery(
        "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'checkintime'",
      ).then((value) => value[0]['variable_value']);
      final String checkOutTime =
          DateFormat('yyyy-MM-dd HH:mm:ss').format(DateTime.now());
      final String customerCode = await LocalDB.rawQuery(
        "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'customer_code'",
      ).then((value) => value[0]['variable_value']);
      final String remarks = await getRemarks(context);

      print("TTT : $remarks");

      final user = await UserLoginClass.getLocalUser();
      final String transId =
          'CI${user?.empCode}${DateFormat('yyyyMMddhhmmss').format(DateTime.now())}';
      // insert into check_in_out_details
      await LocalDB.rawQuery(
          "INSERT INTO check_in_out_details ('trans_id', 'check_in_time', 'check_out_time', 'customer_code', 'remarks') VALUES ('$transId', '$checkInTime', '$checkOutTime', '$customerCode', '$remarks')");

      print("TTT : Local Database updated");

      // get location
      final currentLocation =
          await DeterminePosition.getPosition(null, null, null);
      // insert into location
      final LocationClass location = LocationClass(
        empCode: user?.empCode ?? '',
        transId: transId,
        latt: currentLocation.latitude.toString(),
        longi: currentLocation.longitude.toString(),
        date: DateFormat('yyyy-MM-dd hh:mm:ss').format(DateTime.now()),
        flag: '0',
      );
      await LocalDB.rawQuery(
          "INSERT INTO location ('emp_code', 'trans_id', 'latt', 'longi', 'date', 'flag') VALUES ('${location.empCode}', '${location.transId}', '${location.latt}', '${location.longi}', '${location.date}', '${location.flag}')");

      // await uploadData(context);
      // clear app_variables
      await LocalDB.rawQuery(
        "DELETE FROM app_variables WHERE operation_type = 'check_in'",
      );

      print("TTT : Local Database updated111");

      // New API Call for CheckIN CheckOUT
      await RouteCheckinCheckout.updateRouteCheckinCheckout();

      // navigate to the home screen
      Navigator.of(context).push(MaterialPageRoute(
          builder: (context) => const HomeScreen(
                showPopup: false,
              )));
    } else {
      // Show message atleast one Market Feedback is required
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(checkOutMessage),
        ),
      );
      // pop
      Navigator.of(context).pop();
    }
  }

  Future<String> getRemarks(BuildContext context) async {
    // show a dialog to get remarks
    String remarks = '';
    await showDialog(
      context: context,
      builder: (context) {
        return PopScope(
          canPop: false,
          child: SimpleDialog(
            contentPadding: const EdgeInsets.all(0),
            // border radius
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(10),
            ),
            backgroundColor: const Color.fromARGB(255, 236, 229, 221),
            clipBehavior: Clip.antiAlias,
            children: [
              // title
              Container(
                color: Colors.red,
                padding: const EdgeInsets.all(10),
                child: const Center(
                  child: Text(
                    'Remarks',
                    style: TextStyle(color: Colors.white),
                  ),
                ),
              ),
              // text field
              Padding(
                padding: const EdgeInsets.all(10),
                child: TextField(
                  decoration: const InputDecoration(
                    hintText: 'Enter Remarks',
                  ),
                  onChanged: (value) {
                    remarks = value;
                  },
                ),
              ),
              // button - submit
              Padding(
                padding: const EdgeInsets.all(10),
                child: InkWell(
                  onTap: () {
                    Navigator.of(context).pop();
                  },
                  child: Container(
                    color: Colors.red,
                    padding: const EdgeInsets.all(10),
                    child: const Center(
                      child: Text(
                        'Submit',
                        style: TextStyle(color: Colors.white),
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
    return remarks;
  }

  @override
  void initState() {
    super.initState();
    checkOut();
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            CircularProgressIndicator(
              color: Colors.red,
            ),
            SizedBox(width: 20),
            Text('Checking Out', style: TextStyle(color: Colors.red)),
          ],
        ),
      ),
    );
  }
}
