import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';

class RoutePlanTransactionClass {
  List<List<String>>? routePlanTransactionData;

  RoutePlanTransactionClass({this.routePlanTransactionData});

  factory RoutePlanTransactionClass.fromTXT(String txt) {
    txt = utf8.decode(txt.trim().runes.toList());

    final lines = txt.split('\n');
    final totalRecords = int.parse(lines[0].split('¥')[0]);

    final List<List<String>> data = [];

    for (int i = 2; i < totalRecords + 2; i++) {
      if (lines[i].isEmpty) continue;
      data.add(lines[i].split('^'));
    }

    return RoutePlanTransactionClass(routePlanTransactionData: data);
  }

  /* ================= DOWNLOAD ================= */

  static Future<bool> getRoutePlanTransaction() async {
    try {
      final isConnected = await NetworkService.checkConnectionAll();
      if (!isConnected) return false;

      final user = await UserLoginClass.getLocalUser();

      String lastUpdateTime = await UserLoginClass.lastUpdateTime();
      lastUpdateTime = lastUpdateTime.replaceAll(' ', '?');

      final url = Uri.parse(AppWebService.routePlanURL).replace(
        queryParameters: {
          'nick_name': AppWebService.nickname,
          'emp_code': user?.empCode ?? '',
          'last_update_time': lastUpdateTime,
          'incremental_download': 'no',
          'data_download_time': '1971-01-01?10:10:10'
        },
      );

      print("Route Plan URL: $url");

      final response = await http.get(url);

      if (response.statusCode != 200 || !response.body.contains('¥'))
        return false;

      final parsed = RoutePlanTransactionClass.fromTXT(response.body);

      final localDB = await LocalDB.openMyDatabase();

      await localDB.delete('route_plan_transaction');

      final batch = localDB.batch();

      for (final row in parsed.routePlanTransactionData!) {
        batch.insert(
          'route_plan_transaction',
          RoutePlanTransactionDB(
            routePlanTransId: row[0],
            empCode: row[1],
            routeCode: row[2],
            visitDate: row[3],
            createDate: row[4],
            status: row[5],
            routeName: row[6],
            flag: '1',
          ).toMap(),
        );
      }

      await batch.commit();
      return true;
    } catch (e) {
      print("getRoutePlanTransaction error $e");
      return false;
    }
  }

  /* ================= LOCAL FETCH ================= */

  static Future<List<RoutePlanTransactionDB>>
      getRoutePlanTransactionDB() async {
    final localDB = await LocalDB.openMyDatabase();

    final result = await localDB.query('route_plan_transaction');

    return result.map((e) => RoutePlanTransactionDB.fromMap(e)).toList();
  }

  /* ================= UPLOAD ================= */

  static Future<bool> saveRoutePlanTransactionServer() async {
    try {
      final isConnected = await NetworkService.checkConnectionAll();
      if (!isConnected) return false;

      final user = await UserLoginClass.getLocalUser();

      final currentDateTime =
          DateFormat('yyyy-MM-dd€HH:mm:ss').format(DateTime.now());

      final url = Uri.parse(AppWebService.submitRoutePlanURL).replace(
        queryParameters: {
          'nick_name': AppWebService.nickname,
          'emp_code': user?.empCode ?? '',
          'last_update_time': currentDateTime,
        },
      );

      List<RoutePlanTransactionDB> data = await getRoutePlanTransactionDB();

      data = data.where((e) => e.flag == '0').toList();

      final ids = data.map((e) => e.routePlanTransId!).toSet().toList();

      final localDB = await LocalDB.openMyDatabase();

      bool allUploaded = true;

      for (final id in ids) {
        try {
          final records = data.where((e) => e.routePlanTransId == id).toList();

          final xml = RoutePlanTransactionDB.listToXML(records);

          print("data : ${xml.toString()}");

          final response = await http.post(
            url,
            body: xml.toXmlString(newLine: ''),
            headers: {'Content-Type': 'application/xml'},
          );

          if (response.statusCode == 200 && response.body == '1') {
            await localDB.update(
              'route_plan_transaction',
              {'flag': '1'},
              where: 'route_plan_trans_id = ?',
              whereArgs: [id],
            );
          } else {
            allUploaded = false;
          }
        } catch (e) {
          print("Upload error $e");
          allUploaded = false;
        }
      }

      return allUploaded;
    } catch (e) {
      print("saveRoutePlanTransactionServer error $e");
      return false;
    }
  }
}

/* ===================================================== */

class RoutePlanTransactionDB {
  String? routePlanTransId;
  String? empCode;
  String? routeCode;
  String? visitDate;
  String? createDate;
  String? routeName;
  String? flag;
  String? previousRouteCode;
  String? previousRouteName;
  String? remarks;
  String? distributorCode;
  String? status;
  String? workingWith;

  RoutePlanTransactionDB({
    this.routePlanTransId,
    this.empCode,
    this.routeCode,
    this.visitDate,
    this.createDate,
    this.routeName,
    this.flag,
    this.previousRouteCode,
    this.previousRouteName,
    this.remarks,
    this.distributorCode,
    this.status,
    this.workingWith,
  });

  RoutePlanTransactionDB.fromMap(Map<String, dynamic> map) {
    routePlanTransId = map['route_plan_trans_id'];
    empCode = map['emp_code'];
    routeCode = map['route_code'];
    visitDate = map['visit_date'];
    createDate = map['create_date'];
    routeName = map['route_name'];
    flag = map['flag'];
    previousRouteCode = map['previous_route_code'];
    previousRouteName = map['previous_route_name'];
    remarks = map['remarks'];
    distributorCode = map['distributor_code'];
    status = map['status'];
    workingWith = map['working_with'];
  }

  Map<String, dynamic> toMap() => {
        'route_plan_trans_id': routePlanTransId,
        'emp_code': empCode,
        'route_code': routeCode,
        'visit_date': visitDate,
        'create_date': createDate,
        'route_name': routeName,
        'flag': flag,
        'previous_route_code': previousRouteCode,
        'previous_route_name': previousRouteName,
        'remarks': remarks,
        'distributor_code': distributorCode,
        'status': status,
        'working_with': workingWith,
      };

  /* ================= XML ================= */

  static XmlElement listToXML(List<RoutePlanTransactionDB> list) {
    final builder = XmlBuilder();

    builder.element('root', nest: () {
      builder.element('route_plan', nest: () {
        for (final item in list) {
          if (item.flag == '1') continue;

          builder.element('route_plan_details', nest: () {
            void add(String tag, String? value) {
              builder.element(tag, nest: () {
                builder.cdata(value ?? '');
              });
            }

            add('route_plan_trans_id', item.routePlanTransId);
            add('emp_code', item.empCode);
            add('route_code', item.routeCode);
            add('visit_date', item.visitDate);
            add('create_date', item.createDate);
            add('route_name', item.routeName);
            add('previous_route_code', item.previousRouteCode);
            add('previous_route_name', item.previousRouteName);
            add('remarks', item.remarks);
            add('distributor_code', item.distributorCode);
            add('status', item.status);
            add('working_with', item.workingWith);
          });
        }
      });
    });

    return builder.buildDocument().rootElement;
  }
}
