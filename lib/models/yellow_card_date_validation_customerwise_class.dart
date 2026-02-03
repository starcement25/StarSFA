import 'dart:convert';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class YellowCardDateValidationCustomerWiseClass {
  List<List<String>> yellowCardDateValidationCustomerwiseData = [];

  YellowCardDateValidationCustomerWiseClass(
      {required this.yellowCardDateValidationCustomerwiseData});

  factory YellowCardDateValidationCustomerWiseClass.fromTXT(String txt) {
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> yellowCardDateValidation = [];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      yellowCardDateValidation.add(temp);
    }
    return YellowCardDateValidationCustomerWiseClass(
        yellowCardDateValidationCustomerwiseData: yellowCardDateValidation);
  }

  toJson() {
    return {
      'yellowCardDateValidation':
          yellowCardDateValidationCustomerwiseData.map((e) => e).toList()
    };
  }

  static Future<bool> getYellowCardDateValidation() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.yellowCardValidationDateCustomerWiseURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    http.Response response = await http.get(url);
    // print('Yellow Card Date Validation URL: $url');
    // print('Yellow Card Date Validation Status Code: ${response.statusCode}');
    // print('Yellow Card Date Validation Response: ${response.body}');
    if (response.statusCode == 200) {
      if (response.body.contains('¥')) {
        final YellowCardDateValidationCustomerWiseClass
            yellowCardDateValidationCustomerwise =
            YellowCardDateValidationCustomerWiseClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<String> columnNames = await localDB
            .rawQuery(
                'PRAGMA table_info(yellow_card_date_validation_customerwise)')
            .then((value) {
          final List<String> temp = [];
          for (var element in value) {
            temp.add(element['name'].toString());
          }
          return temp;
        });
        final batch = localDB.batch();
        for (int j = 0;
            j <
                yellowCardDateValidationCustomerwise
                    .yellowCardDateValidationCustomerwiseData.length;
            j++) {
          final Map<String, dynamic> yellowCardDateValidationMap = {};
          for (int i = 0; i < columnNames.length; i++) {
            yellowCardDateValidationMap[columnNames[i]] =
                yellowCardDateValidationCustomerwise
                    .yellowCardDateValidationCustomerwiseData[j][i];
          }
          // print('yellowCardDateValidationMap: $yellowCardDateValidationMap');
          // await localDB.insert('yellow_card_date_validation_customerwise',
              // yellowCardDateValidationMap);
          batch.insert('yellow_card_date_validation_customerwise',
              yellowCardDateValidationMap);
        }
        // clear the table
        await localDB.delete('yellow_card_date_validation_customerwise');
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

class YellowCardDateValidationCustomerWiseDB{
  String? customerCode;
  String? validationFrom;
  String? validationTo;
  String? validationLastDate;

  YellowCardDateValidationCustomerWiseDB({this.customerCode, this.validationFrom, this.validationTo, this.validationLastDate});

  factory YellowCardDateValidationCustomerWiseDB.fromMap(Map<String, dynamic> map){
    return YellowCardDateValidationCustomerWiseDB(
      customerCode: map['customer_code'],
      validationFrom: map['validation_from'],
      validationTo: map['validation_to'],
      validationLastDate: map['validation_last_date']
    );
  }

  Map<String, dynamic> toMap(){
    return {
      'customer_code': customerCode,
      'validation_from': validationFrom,
      'validation_to': validationTo,
      'validation_last_date': validationLastDate
    };
  }

  static Future<YellowCardDateValidationCustomerWiseDB?> getYellowCardDateValidationCustomerWiseDB(String customerCode) async{
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> yellowCardDateValidationCustomerWiseDB = await localDB.rawQuery('SELECT * FROM yellow_card_date_validation_customerwise WHERE customer_code = ?', [customerCode]);
    if(yellowCardDateValidationCustomerWiseDB.isNotEmpty){
      return YellowCardDateValidationCustomerWiseDB.fromMap(yellowCardDateValidationCustomerWiseDB[0]);
    }else{
      return null;
    }
  }
}
