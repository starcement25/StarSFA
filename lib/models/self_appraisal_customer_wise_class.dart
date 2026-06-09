import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class SelfAppraisalCustomerWiseClass {
  List<List<String>>? selfAppraisalCustomerWiseData;

  SelfAppraisalCustomerWiseClass({this.selfAppraisalCustomerWiseData});

  factory SelfAppraisalCustomerWiseClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> selfAppraisalCustomerWiseData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      selfAppraisalCustomerWiseData.add(temp);
    }
    return SelfAppraisalCustomerWiseClass(
        selfAppraisalCustomerWiseData: selfAppraisalCustomerWiseData);
  }

  static Future<bool> getSelfAppraisalCustomerWise() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.selfAppraisalCustomerWise}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    http.Response response = await http.get(url);
    // print('Self Appraisal Customer Wise URL: $url');
    // print('Self Appraisal Customer Wise Status Code: ${response.statusCode}');
    // print('Self Appraisal Customer Wise Response: ${response.body}');
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final SelfAppraisalCustomerWiseClass selfAppraisalCustomerWise =
            SelfAppraisalCustomerWiseClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<SelfAppraisalCustomerWiseDB> selfAppraisalCustomerWiseDB =
            [];
        for (int i = 0;
            i < selfAppraisalCustomerWise.selfAppraisalCustomerWiseData!.length;
            i++) {
          final SelfAppraisalCustomerWiseDB selfAppraisalCustomerWiseLocal =
              SelfAppraisalCustomerWiseDB(
            customerCode:
                selfAppraisalCustomerWise.selfAppraisalCustomerWiseData?[i][0],
            customerName:
                selfAppraisalCustomerWise.selfAppraisalCustomerWiseData?[i][1],
            target: selfAppraisalCustomerWise.selfAppraisalCustomerWiseData?[i]
                [6],
            achievement:
                selfAppraisalCustomerWise.selfAppraisalCustomerWiseData?[i][7],
            month: selfAppraisalCustomerWise.selfAppraisalCustomerWiseData?[i]
                [5],
            previousAchievement:
                selfAppraisalCustomerWise.selfAppraisalCustomerWiseData?[i][8],
            previousTarget:
                selfAppraisalCustomerWise.selfAppraisalCustomerWiseData?[i][9],
          );
          selfAppraisalCustomerWiseDB.add(selfAppraisalCustomerWiseLocal);
        }
        final batch = localDB.batch();
        for (int i = 0; i < selfAppraisalCustomerWiseDB.length; i++) {
          batch.insert('self_appraisal_summary',
              selfAppraisalCustomerWiseDB[i].toJson());
        }
        // clear the table
        await localDB.delete('self_appraisal_summary');
        await batch.commit(noResult: true);
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class SelfAppraisalCustomerWiseDB {
  String? customerCode;
  String? customerName;
  String? target;
  String? month;
  String? achievement;
  String? previousTarget;
  String? previousAchievement;

  SelfAppraisalCustomerWiseDB({
    this.customerCode,
    this.customerName,
    this.target,
    this.achievement,
    this.month,
    this.previousTarget,
    this.previousAchievement,
  });

  Map<String, dynamic> toJson() {
    return {
      'customer_code': customerCode,
      'customer_name': customerName,
      'target': target,
      'achievement': achievement,
      'month': month,
      'previous_target': previousTarget,
      'previous_achievement': previousAchievement,
    };
  }

  factory SelfAppraisalCustomerWiseDB.fromJson(Map<String, dynamic> json) {
    return SelfAppraisalCustomerWiseDB(
      customerCode: json['customer_code'],
      customerName: json['customer_name'],
      target: json['target'],
      achievement: json['achievement'].toString(),
      month: json['month'].toString(),
      previousTarget: json['previous_target'].toString(),
      previousAchievement: json['previous_achievement'].toString(),
    );
  }

  // get all the records from the table
  static Future<List<SelfAppraisalCustomerWiseDB>> getAllRecords() async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> records =
        await localDB.query('self_appraisal_summary', orderBy: 'customer_name');
    return List.generate(records.length, (index) {
      return SelfAppraisalCustomerWiseDB.fromJson(records[index]);
    });
  }

  // get all the records from the table
  static Future<List<Map<String, dynamic>>> getAllRecords1() async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> records =
        await localDB.query('self_appraisal_summary', orderBy: 'customer_name');
    return records;
  }
}
