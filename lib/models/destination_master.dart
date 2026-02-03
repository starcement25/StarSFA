import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class DestinationMasterClass {
  List<List<String>>? destinationMasterData;

  DestinationMasterClass({this.destinationMasterData});

  factory DestinationMasterClass.fromTXT(String txt) {
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
    return DestinationMasterClass(destinationMasterData: destinationMasterData);
  }

  Map<String, dynamic> toJson() {
    return {
      'destinationMasterData': destinationMasterData?.map((e) => e).toList()
    };
  }

  static Future<bool> getDestinationMaster() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.destinationMasterURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
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
        final DestinationMasterClass destination =
            DestinationMasterClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<DestinationMasterDB> destinationMasterDB = [];
        for (int i = 0; i < destination.destinationMasterData!.length; i++) {
          final DestinationMasterDB destinationMaster = DestinationMasterDB(
            destinationCode: destination.destinationMasterData?[i][0],
            destinationName: destination.destinationMasterData?[i][1],
          );
          destinationMasterDB.add(destinationMaster);
        }
        final batch = localDB.batch();
        for (int i = 0; i < destinationMasterDB.length; i++) {
          batch.insert('destination_master', destinationMasterDB[i].toJson());
        }
        await batch.commit(noResult: true);
        return true;
      }
      return false;
    }
    return false;
  }
}

class DestinationMasterDB {
  String? destinationCode;
  String? destinationName;

  DestinationMasterDB({this.destinationCode, this.destinationName});

  factory DestinationMasterDB.fromJson(Map<String, dynamic> json) {
    return DestinationMasterDB(
      destinationCode: json['destination_code'],
      destinationName: json['destination_name'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'destination_code': destinationCode,
      'destination_name': destinationName,
    };
  }

  // get destination master from the local database
  static Future<List<DestinationMasterDB>> getDestinationMasterDB() async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> destinationMasterDB =
        await localDB.query('destination_master');
    return destinationMasterDB
        .map((e) => DestinationMasterDB.fromJson(e))
        .toList();
  }
}
