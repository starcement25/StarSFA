import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:sqflite/sqflite.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class CompetitorGroupMasterClass {
  List<List<String>>? competitorGroupMasterData;

  CompetitorGroupMasterClass({this.competitorGroupMasterData});

  factory CompetitorGroupMasterClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> destinationMasterData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      destinationMasterData.add(temp);
    }
    return CompetitorGroupMasterClass(
        competitorGroupMasterData: destinationMasterData);
  }

  Map<String, dynamic> toJson() {
    return {
      'destinationMasterData': competitorGroupMasterData?.map((e) => e).toList()
    };
  }

  static Future<bool> getCompetitorGroupMaster() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.competitorGroupURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    http.Response response = await http.get(url);
    // print('Destination Master URL: $url');
    // print('Destination Master Status Code: ${response.statusCode}');
    // print('Destination Master Response: ${response.body}');
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final CompetitorGroupMasterClass destination =
            CompetitorGroupMasterClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<CompetitorGroupMasterDB> destinationMasterDB = [];
        for (int i = 0;
            i < destination.competitorGroupMasterData!.length;
            i++) {
          final CompetitorGroupMasterDB destinationMaster =
              CompetitorGroupMasterDB(
            groupName: destination.competitorGroupMasterData![i][0],
            competitorName: destination.competitorGroupMasterData![i][1],
            uom: destination.competitorGroupMasterData![i][2],
            displayName: destination.competitorGroupMasterData![i][3],
            branchCode: destination.competitorGroupMasterData![i][4],
            productType: destination.competitorGroupMasterData![i][5],
          );
          destinationMasterDB.add(destinationMaster);
        }
        final batch = localDB.batch();
        for (int i = 0; i < destinationMasterDB.length; i++) {
          batch.insert(
              'competitor_group_master', destinationMasterDB[i].toJson(),
              conflictAlgorithm: ConflictAlgorithm.replace);
        }
        // clear the table
        await localDB.delete('competitor_group_master');
        await batch.commit(noResult: true);
        return true;
      }
      return false;
    }
    return false;
  }
}

class CompetitorGroupMasterDB {
  String? groupName;
  String? competitorName;
  String? uom;
  String? displayName;
  String? branchCode;
  String? productType;

  CompetitorGroupMasterDB({
    this.groupName,
    this.competitorName,
    this.uom,
    this.displayName,
    this.branchCode,
    this.productType,
  });

  factory CompetitorGroupMasterDB.fromJson(Map<String, dynamic> json) {
    return CompetitorGroupMasterDB(
      groupName: json['group_name'],
      competitorName: json['competitor_name'],
      uom: json['uom'],
      displayName: json['display_name'],
      branchCode: json['branch_code'],
      productType: json['product_type'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'group_name': groupName,
      'competitor_name': competitorName,
      'uom': uom,
      'display_name': displayName,
      'branch_code': branchCode,
      'product_type': productType,
    };
  }

  // get destination master from the local database
  static Future<List<CompetitorGroupMasterDB>> getCompetitorbyBranch(String branchCode) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> destinationMasterDB =
        await localDB.query('competitor_group_master',
            where: 'branch_code = ?', whereArgs: [branchCode]);
    return destinationMasterDB
        .map((e) => CompetitorGroupMasterDB.fromJson(e))
        .toList();
  }
}
