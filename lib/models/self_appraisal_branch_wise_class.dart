import 'dart:convert';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class SelfAppraisalBranchWiseClass {
  List<List<String>>? selfAppraisalBranchWiseData;

  SelfAppraisalBranchWiseClass({this.selfAppraisalBranchWiseData});

  factory SelfAppraisalBranchWiseClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> selfAppraisalBranchWiseData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      selfAppraisalBranchWiseData.add(temp);
    }
    return SelfAppraisalBranchWiseClass(
        selfAppraisalBranchWiseData: selfAppraisalBranchWiseData);
  }

  static Future<bool> getSelfAppraisalBranchWise() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.selfAppraisalBranchWise}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    http.Response response = await http.get(url);
    log('Self Appraisal Branch Wise URL: $url');
    log('Self Appraisal Branch Wise Status Code: ${response.statusCode}');
    log('Self Appraisal Branch Wise Response: ${response.body}');
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final SelfAppraisalBranchWiseClass selfAppraisalBranchWise =
            SelfAppraisalBranchWiseClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<SelfAppraisalBranchWiseDB> selfAppraisalBranchWiseDB = [];
        for (int i = 0;
            i < selfAppraisalBranchWise.selfAppraisalBranchWiseData!.length;
            i++) {
          final SelfAppraisalBranchWiseDB selfAppraisalBranchWiseLocal =
              SelfAppraisalBranchWiseDB(
            branchCode: selfAppraisalBranchWise.selfAppraisalBranchWiseData?[i]
                [0],
            branchName: selfAppraisalBranchWise.selfAppraisalBranchWiseData?[i]
                [1],
            target: selfAppraisalBranchWise.selfAppraisalBranchWiseData?[i][4],
            achievement: selfAppraisalBranchWise.selfAppraisalBranchWiseData?[i]
                [5],
            month: selfAppraisalBranchWise.selfAppraisalBranchWiseData?[i][3],
          );
          selfAppraisalBranchWiseDB.add(selfAppraisalBranchWiseLocal);
        }
        final batch = localDB.batch();
        for (int i = 0; i < selfAppraisalBranchWiseDB.length; i++) {
          batch.insert('self_appraisal_branch_wise',
              selfAppraisalBranchWiseDB[i].toJson());
        }
        // clear the table
        await localDB.delete('self_appraisal_branch_wise');
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

class SelfAppraisalBranchWiseDB {
  String? branchCode;
  String? branchName;
  String? target;
  String? month;
  String? achievement;

  SelfAppraisalBranchWiseDB({
    this.branchCode,
    this.branchName,
    this.target,
    this.achievement,
    this.month,
  });

  Map<String, dynamic> toJson() {
    return {
      'branch_code': branchCode,
      'branch_name': branchName,
      'target': target,
      'achievement': achievement,
      'month': month,
    };
  }

  factory SelfAppraisalBranchWiseDB.fromJson(Map<String, dynamic> json) {
    return SelfAppraisalBranchWiseDB(
      branchCode: json['branch_code'],
      branchName: json['branch_name'],
      target: json['target'],
      achievement: json['achievement'],
      month: json['month'],
    );
  }

  // get all the records from the table
  static Future<List<SelfAppraisalBranchWiseDB>> getAllRecords() async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> records =
        await localDB.query('self_appraisal_branch_wise');
    return List.generate(records.length, (index) {
      return SelfAppraisalBranchWiseDB.fromJson(records[index]);
    });
  }
}
