import 'dart:convert';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class BranchMasterClass {
  List<List<String>>? branchMasterData;

  BranchMasterClass({this.branchMasterData});

  BranchMasterClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    branchMasterData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      branchMasterData?.add(temp);
    }
  }

  toJson() {
    return {'branchMasterData': branchMasterData?.map((e) => e).toList()};
  }

  static Future<bool> getBranchMaster() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.branchMasterURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    http.Response response = await http.get(url);
    // print('Branch Master URL: $url');
    // print('Branch Master Status Code: ${response.statusCode}');
    // print('Branch Master Response: ${response.body}');
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final BranchMasterClass branchMaster =
            BranchMasterClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        // get column names
        final List<String> columnNames = await localDB
            .rawQuery('PRAGMA table_info(branch_master)')
            .then((value) {
          final List<String> temp = [];
          for (var element in value) {
            temp.add(element['name'].toString());
          }
          return temp;
        });
                final batch = localDB.batch();
        branchMaster.branchMasterData?.forEach((element) async {
          Map<String, dynamic> branchMasterMap = {};
          for (int i = 0; i < columnNames.length; i++) {
            // check if the column exists in indexedColumns
            branchMasterMap[columnNames[i]] =
                element[columnNames.indexOf(columnNames[i])];
          }
           batch.insert('branch_master', branchMasterMap);
        });
        // clear the table
        await localDB.delete('branch_master');
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
