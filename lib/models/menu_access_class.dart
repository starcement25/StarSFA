import 'dart:convert';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class MenuAccessClass {
  List<String> menuAccessData = [];

  MenuAccessClass({required this.menuAccessData});

  factory MenuAccessClass.fromTXT(String txt) {
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<String> menuAccess = [];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      menuAccess.add(temp[0]);
    }
    return MenuAccessClass(menuAccessData: menuAccess);
  }

  toJson() {
    return {'menuAccess': menuAccessData.map((e) => e).toList()};
  }

  static Future<bool> getMenuAccess() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.menuAccessURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    http.Response response = await http.get(url);
    if (response.statusCode == 200) {
      if (response.body.contains('¥')) {
        final MenuAccessClass menuAccess =
            MenuAccessClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<String> columnNames = await localDB
            .rawQuery('PRAGMA table_info(menu_access)')
            .then((value) {
          final List<String> temp = [];
          for (var element in value) {
            temp.add(element['name'].toString());
          }
          return temp;
        });
        final batch = localDB.batch();
        for (int i = 0; i < menuAccess.menuAccessData.length; i++) {
          final Map<String, dynamic> menuAccessMap = {};
          for (int j = 0; j < columnNames.length; j++) {
            menuAccessMap[columnNames[j]] = menuAccess.menuAccessData[i];

            // print('menuAccessMap: $menuAccessMap');
            batch.insert('menu_access', menuAccessMap);
          }
        }
        // clear the table
        await localDB.delete('menu_access');
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
