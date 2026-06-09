// route_plan_transaction
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
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      // don't add if the line is empty
      if (lines[i].isEmpty) {
        continue;
      } else {
        data.add(lines[i]);
      }
    }
    final List<List<String>> routePlanTransactionData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      routePlanTransactionData.add(temp);
    }
    return RoutePlanTransactionClass(
        routePlanTransactionData: routePlanTransactionData);
  }

  toJson() {
    return {
      'routePlanTransactionData': routePlanTransactionData,
    };
  }

  static Future<bool> getRoutePlanTransaction() async {
    final bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    String incrementalDownload = await UserLoginClass.getincrementalDownload();
    incrementalDownload = 'no';
    String lastUpdateTime = await UserLoginClass.lastUpdateTime();
    lastUpdateTime = lastUpdateTime.replaceAll(' ', '?');
    Uri url = Uri.parse(
        '${AppWebService.routePlanURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=$lastUpdateTime&incremental_download=$incrementalDownload&data_download_time=1971-01-01?10:10:10');
    print("Route Plan URL: $url");
    http.Response response = await http.get(url);
    if (response.statusCode == 200) {
      if (response.body.contains('¥')) {
        final RoutePlanTransactionClass routePlanTransaction =
            RoutePlanTransactionClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<RoutePlanTransactionDB> routePlanTransactionDB = [];
        for (int i = 0;
            i < routePlanTransaction.routePlanTransactionData!.length;
            i++) {
          final RoutePlanTransactionDB temp = RoutePlanTransactionDB(
              routePlanTransId:
                  routePlanTransaction.routePlanTransactionData![i][0],
              empCode: routePlanTransaction.routePlanTransactionData![i][1],
              routeCode: routePlanTransaction.routePlanTransactionData![i][2],
              visitDate: routePlanTransaction.routePlanTransactionData![i][3],
              createDate: routePlanTransaction.routePlanTransactionData![i][4],
              status: routePlanTransaction.routePlanTransactionData![i][5],
              routeName: routePlanTransaction.routePlanTransactionData![i][6],
              flag: '1');
          routePlanTransactionDB.add(temp);
        }
        // clear the table
        await localDB.delete('route_plan_transaction');
        final batch = localDB.batch();
        for (int i = 0; i < routePlanTransactionDB.length; i++) {
          batch.insert(
              'route_plan_transaction', routePlanTransactionDB[i].toMap());
        }
        await batch.commit();
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  static Future<List<RoutePlanTransactionDB>>
      getRoutePlanTransactionDB() async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> routePlanTransactionDB =
        await localDB.query('route_plan_transaction');
    final List<RoutePlanTransactionDB> routePlanTransaction = [];
    for (int i = 0; i < routePlanTransactionDB.length; i++) {
      final RoutePlanTransactionDB temp =
          RoutePlanTransactionDB.fromMap(routePlanTransactionDB[i]);
      routePlanTransaction.add(temp);
    }
    return routePlanTransaction;
  }

  static Future<bool> saveRoutePlanTransactionServer() async {
    final bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    // get user
    final user = await UserLoginClass.getLocalUser();
    // 2024-05-09€14:49:28
    final String currentDateTime =
        DateFormat('yyyy-MM-dd€HH:mm:ss').format(DateTime.now());
    final Uri url = Uri.parse(
        '${AppWebService.submitRoutePlanURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=$currentDateTime');
    // get route plan transaction from local db
    List<RoutePlanTransactionDB> routePlanTransactionDB =
        await getRoutePlanTransactionDB();
    // filter for flag = 0
    routePlanTransactionDB =
        routePlanTransactionDB.where((element) => element.flag == '0').toList();
    // get unique route plan transaction id
    final List<String> routePlanTransId =
        routePlanTransactionDB.map((e) => e.routePlanTransId!).toList();
    for (int i = 0; i < routePlanTransId.length; i++) {
      bool isUploaded = false;
      final List<RoutePlanTransactionDB> routePlanTransaction =
          routePlanTransactionDB
              .where(
                  (element) => element.routePlanTransId == routePlanTransId[i])
              .toList();
      final XmlElement xml =
          RoutePlanTransactionDB.listToXML(routePlanTransaction);
      String xmlString = xml.toXmlString(
        newLine: '',
      );
      xmlString = '<?xml version="1.0" encoding="UTF-8"?>$xmlString';
      xmlString = xmlString.replaceAll('&lt;', '<');
      xmlString = xmlString.replaceAll('&gt;', '>');
      print(xmlString);
      final http.Response response = await http.post(
        url,
        body: xmlString,
        headers: {
          'Content-Type': 'application/xml',
        },
      );
      print("Response: ${response.body}, Status Code: ${response.statusCode}");
      if (response.statusCode == 200 && response.body == '1') {
        isUploaded = true;
      } else {
        isUploaded = false;
      }
      if (isUploaded) {
        final localDB = await LocalDB.openMyDatabase();
        await localDB.update(
          'route_plan_transaction',
          {'flag': '1'},
          where: 'route_plan_trans_id = ?',
          whereArgs: [routePlanTransId[i]],
        );
      }
    }
    return true;
  }
}

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

  Map<String, dynamic> toMap() {
    return {
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
  }

  // to XML
  XmlElement toXML() {
    final builder = XmlBuilder();
    builder.processing('xml', 'version="1.0" encoding="UTF-8"');
    builder.element('root', nest: () {
      builder.element('route_plan', nest: () {
        builder.element('route_plan_details', nest: () {
          builder.element('route_plan_trans_id',
              nest: '<![CDATA[$routePlanTransId]]>');
          builder.element('emp_code', nest: '<![CDATA[$empCode]]>');
          builder.element('route_code', nest: '<![CDATA[$routeCode]]>');
          builder.element('visit_date', nest: '<![CDATA[$visitDate]]>');
          builder.element('create_date', nest: '<![CDATA[$createDate]]>');
          builder.element('route_name', nest: '<![CDATA[$routeName]]>');
          builder.element('previous_route_code',
              nest: '<![CDATA[$previousRouteCode]]>');
          builder.element('previous_route_name',
              nest: '<![CDATA[$previousRouteName]]>');
          builder.element('remarks', nest: '<![CDATA[$remarks]]>');
          builder.element('distributor_code',
              nest: '<![CDATA[$distributorCode]]>');
          builder.element('status', nest: '<![CDATA[$status]]>');
          builder.element('working_with', nest: '<![CDATA[$workingWith]]>');
        });
      });
    });
    return builder.buildDocument().rootElement;
  }

  // list to XML
  static XmlElement listToXML(
      List<RoutePlanTransactionDB> routePlanTransaction) {
    final builder = XmlBuilder();
    builder.processing('xml', 'version="1.0" encoding="UTF-8"');
    builder.element('root', nest: () {
      builder.element('route_plan', nest: () {
        for (int i = 0; i < routePlanTransaction.length; i++) {
          if (routePlanTransaction[i].flag != 1) {
            builder.element('route_plan_details', nest: () {
              builder.element('route_plan_trans_id',
                  nest:
                      '<![CDATA[${routePlanTransaction[i].routePlanTransId}]]>');
              builder.element('emp_code',
                  nest: '<![CDATA[${routePlanTransaction[i].empCode}]]>');
              builder.element('route_code',
                  nest: '<![CDATA[${routePlanTransaction[i].routeCode}]]>');
              builder.element('visit_date',
                  nest: '<![CDATA[${routePlanTransaction[i].visitDate}]]>');
              builder.element('create_date',
                  nest: '<![CDATA[${routePlanTransaction[i].createDate}]]>');
              builder.element('route_name',
                  nest: '<![CDATA[${routePlanTransaction[i].routeName}]]>');
              builder.element('previous_route_code',
                  nest:
                      '<![CDATA[${routePlanTransaction[i].previousRouteCode}]]>');
              builder.element('previous_route_name',
                  nest:
                      '<![CDATA[${routePlanTransaction[i].previousRouteName}]]>');
              builder.element('remarks',
                  nest: '<![CDATA[${routePlanTransaction[i].remarks}]]>');
              builder.element('distributor_code',
                  nest:
                      '<![CDATA[${routePlanTransaction[i].distributorCode}]]>');
              builder.element('status',
                  nest: '<![CDATA[${routePlanTransaction[i].status}]]>');
              builder.element('working_with',
                  nest: '<![CDATA[${routePlanTransaction[i].workingWith}]]>');
            });
          }
        }
      });
    });
    return builder.buildDocument().rootElement;
  }
}
