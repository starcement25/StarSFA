import 'dart:convert';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class RouteMasterClass {
  List<List<String>>? routeMasterData;

  RouteMasterClass({this.routeMasterData});

  RouteMasterClass.fromTXT(String txt) {
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    routeMasterData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^')..insert(3, '');
      routeMasterData?.add(temp);
    }
  }

  toJson() {
    return {'routeMasterData': routeMasterData?.map((e) => e).toList()};
  }

  static Future<bool> getRouteMaster() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    String incrementalDownload = await UserLoginClass.getincrementalDownload();
    incrementalDownload = 'no';
    String lastUpdateTime = await UserLoginClass.lastUpdateTime();
    lastUpdateTime = lastUpdateTime.replaceAll(' ', '?');
    Uri url = Uri.parse(
        '${AppWebService.routeDetailsURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=$lastUpdateTime&incremental_download=$incrementalDownload&data_download_time=1971-01-01?10:10:10');
    http.Response response = await http.get(url);
    print("Route Master URL: $url");
    print("Route Master Response: ${response.body}");
    if (response.statusCode == 200) {
      if (response.body.contains('¥')) {
        final RouteMasterClass routeMaster =
            RouteMasterClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<String> columnNames = await localDB
            .rawQuery('PRAGMA table_info(route_master)')
            .then((value) {
          final List<String> temp = [];
          for (var element in value) {
            temp.add(element['name'].toString());
          }
          return temp;
        });
        final batch = localDB.batch();
        for (int i = 0; i < (routeMaster.routeMasterData?.length ?? 0); i++) {
          final Map<String, dynamic> routeMasterMap = {};
          for (int j = 0; j < columnNames.length; j++) {
            routeMasterMap[columnNames[j]] = routeMaster.routeMasterData?[i][j];
          }
          batch.insert('route_master', routeMasterMap);
          // print('Route Master Map: $routeMasterMap');
        }
        // clear the table
        await localDB.delete('route_master');
        await batch.commit();
        return true;
      }

      return false;
    }
    return false;
  }
}

class RouteMasterDB {
  String? routeCode;
  String? routeName;
  String? branchCode;
  String? createDate;

  RouteMasterDB(
      {this.routeCode, this.routeName, this.branchCode, this.createDate});

  RouteMasterDB.fromMap(Map<String, dynamic> map) {
    routeCode = map['route_code'];
    routeName = map['route_name'];
    branchCode = map['branch_code'];
    createDate = map['create_date'];
  }

  Map<String, dynamic> toMap() {
    return {
      'route_code': routeCode,
      'route_name': routeName,
      'branch_code': branchCode,
      'create_date': createDate,
    };
  }

  static Future<RouteMasterDB> getRouteMasterDBByRouteCode(
      String routeCode) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> maps = await localDB
        .query('route_master', where: 'route_code = ?', whereArgs: [routeCode]);
    if (maps.isNotEmpty) {
      return RouteMasterDB.fromMap(maps.first);
    }
    return RouteMasterDB();
  }
}
