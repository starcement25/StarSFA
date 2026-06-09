import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class ManagerActivityClass {
  String? empCode;
  String? empName;
  String? inTime;
  String? counterVisit;

  ManagerActivityClass({
    this.empCode,
    this.empName,
    this.inTime,
    this.counterVisit,
  });

  ManagerActivityClass.fromMap(Map<String, dynamic> map) {
    empCode = map['emp_code'];
    empName = map['emp_name'];
    inTime = map['in_time'];
    counterVisit = map['counter_visit'];
  }

  Map<String, dynamic> toMap() {
    return {
      'emp_code': empCode,
      'emp_name': empName,
      'in_time': inTime,
      'counter_visit': counterVisit,
    };
  }

  // from TXT
  List<ManagerActivityClass> getListFromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<ManagerActivityClass> managerActivityList = [];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      managerActivityList.add(ManagerActivityClass(
        empCode: temp[0],
        empName: temp[1],
        inTime: temp[2],
        counterVisit: temp[3],
      ));
    }
    return managerActivityList;
  }

  static Future<List<ManagerActivityClass>> getManagerActivity() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }
    final user = await UserLoginClass.getLocalUser();
    // 2024-05-28
    final String dateval = DateFormat('yyyy-MM-dd').format(DateTime.now());
    final String timeval = DateFormat('HH:mm:ss').format(DateTime.now());
    final url = Uri.parse(
        '${AppWebService.managerActivityURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&dateval=$dateval&timeval=$timeval');
    print(url.toString());
    final response = await http.get(url);
    if (response.statusCode == 200) {
      if (response.body.contains('¥')) {
        final List<ManagerActivityClass> managerActivityList =
            ManagerActivityClass().getListFromTXT(response.body);
        return managerActivityList;
      } else {
        return [];
      }
    } else {
      return [];
    }
  }
}
