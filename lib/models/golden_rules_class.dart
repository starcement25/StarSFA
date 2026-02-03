import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class GoldenRulesClass {
  List<List<String>>? goldenRulesData;

  GoldenRulesClass({this.goldenRulesData});

  GoldenRulesClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    goldenRulesData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      goldenRulesData?.add(temp);
    }
  }

  static Future<bool> getGoldenRules() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.branchWiseGoldenRuleURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no');
    // get response from the server
    http.Response response = await http.get(url);
    // print('Destination Master URL: $url');
    // print('Destination Master Status Code: ${response.statusCode}');
    // print('Destination Master Response: ${response.body}');
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        final GoldenRulesClass goldenRules =
            GoldenRulesClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final batch = localDB.batch();
        for (int i = 0; i < goldenRules.goldenRulesData!.length; i++) {
          final List<String> goldenRule = goldenRules.goldenRulesData![i];
          final GoldenRulesDB goldenRulesDBItem = GoldenRulesDB(
            state: goldenRule[0],
            grFileName: goldenRule[1],
            acedns: goldenRule[2],
            startDate: goldenRule[3],
            endDate: goldenRule[4],
          );
          batch.insert('branchwise_goldenrule', goldenRulesDBItem.toMap());
        }
        await batch.commit();
        return true;
      } else {
        throw Exception('Failed to load Golden Rules');
      }
    } else {
      throw Exception('Failed to load Golden Rules');
    }
  }
}

class GoldenRulesDB {
  final String? state;
  final String? grFileName;
  final String? acedns;
  final String? startDate;
  final String? endDate;

  GoldenRulesDB({
    this.state,
    this.grFileName,
    this.acedns,
    this.startDate,
    this.endDate,
  });

  Map<String, dynamic> toMap() {
    return {
      'state': state,
      'gr_file_name': grFileName,
      'acedns': acedns,
      'start_date': startDate,
      'end_date': endDate,
    };
  }
}
