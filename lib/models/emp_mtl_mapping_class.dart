import 'dart:convert';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class EmpMTLMappingClass {
  List<List<String>>? branchMasterData;

  EmpMTLMappingClass({this.branchMasterData});

  EmpMTLMappingClass.fromTXT(String txt) {
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    final List<String> data = [];
    for (int i = 1; i < totalRecords + 1; i++) {
      if (i < lines.length && lines[i].trim().isNotEmpty) {
        data.add(lines[i]);
      }
    }
    branchMasterData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      branchMasterData?.add(data[i].split('^'));
    }
  }

  toJson() {
    return {'empMTLMappingData': branchMasterData?.map((e) => e).toList()};
  }

  static Future<bool> getBranchMaster() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.empMtlMappingURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    http.Response response = await http.get(url);
    if (response.statusCode == 200) {
      if (response.body.contains('¥')) {
        final EmpMTLMappingClass branchMaster =
            EmpMTLMappingClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<String> columnNames = await localDB
            .rawQuery('PRAGMA table_info(emp_mtl_mapping)')
            .then((value) {
          final List<String> temp = [];
          for (var element in value) {
            temp.add(element['name'].toString());
          }
          return temp;
        });
        final batch = localDB.batch();
        branchMaster.branchMasterData?.forEach((element) {
          if (element.length < columnNames.length)
            return; // malformed row, skip
          final Map<String, dynamic> branchMasterMap = {};
          for (int i = 0; i < columnNames.length; i++) {
            branchMasterMap[columnNames[i]] = element[i];
          }
          batch.insert('emp_mtl_mapping', branchMasterMap);
        });
        await localDB.delete('emp_mtl_mapping');
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
