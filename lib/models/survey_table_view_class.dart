import 'dart:convert';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class SurveyTableViewClass {
  List<List<String>>? surveyTableViewData;

  SurveyTableViewClass({this.surveyTableViewData});

  SurveyTableViewClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    surveyTableViewData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      surveyTableViewData?.add(temp);
    }
  }

  static Future<bool> getSurveyTableView() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.surveyTableViewURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    http.Response response = await http.get(url);
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final SurveyTableViewClass surveyTableView =
            SurveyTableViewClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        // get column names
        final List<String> columnNames = await localDB
            .rawQuery('PRAGMA table_info(table_view)')
            .then((value) {
          final List<String> temp = [];
          for (var element in value) {
            temp.add(element['name'].toString());
          }
          return temp;
        });
        surveyTableView.surveyTableViewData?.forEach((element) async {
          final Map<String, dynamic> surveyTableViewMap = {};
          for (int i = 0; i < columnNames.length; i++) {
            surveyTableViewMap[columnNames[i]] = element[i];
          }
          await localDB.insert('table_view', surveyTableViewMap);
        });
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}
