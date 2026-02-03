import 'dart:convert';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class BankMasterClass {
  List<List<String>>? bankMasterData;

  BankMasterClass({this.bankMasterData});

  factory BankMasterClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> bankMasterData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      bankMasterData.add(temp);
    }
    return BankMasterClass(bankMasterData: bankMasterData);
  }

  static Future<bool> getBankMaster() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.bankMasterURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    http.Response response = await http.get(url);
    // print('Bank Master URL: $url');
    // print('Bank Master Status Code: ${response.statusCode}');
    // print('Bank Master Response: ${response.body}');
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final BankMasterClass bank = BankMasterClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<BankMasterDB> bankMasterDB = [];
        for (int i = 0; i < bank.bankMasterData!.length; i++) {
          final BankMasterDB bankMaster = BankMasterDB(
            bankId: bank.bankMasterData?[i][0],
            bankName: bank.bankMasterData?[i][1],
          );
          bankMasterDB.add(bankMaster);
        }
        final batch = localDB.batch();
        for (int i = 0; i < bankMasterDB.length; i++) {
          batch.insert('bank_master', bankMasterDB[i].toJson());
        }
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

class BankMasterDB {
  String? bankId;
  String? bankName;

  BankMasterDB({this.bankId, this.bankName});

  // from JSON
  BankMasterDB.fromJson(Map<String, dynamic> json) {
    bankId = json['bank_id'];
    bankName = json['bank_name'];
  }

  // to JSON
  Map<String, dynamic> toJson() {
    return {'bank_id': bankId, 'bank_name': bankName};
  }

  // get all bank master data from the local database
  static Future<List<BankMasterDB>> getAllBankMaster() async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> bankMasterData =
        await localDB.query('bank_master');
    return bankMasterData.map((e) => BankMasterDB.fromJson(e)).toList();
  }
}
