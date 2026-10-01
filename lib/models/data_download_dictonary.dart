import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class DataDownloadDictionary {
  String? updateDateTime;
  List<String>? tableNames;

  DataDownloadDictionary({this.tableNames, this.updateDateTime});

  static Future<DataDownloadDictionary> getTableNames(
      bool incrementalDownload) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    UserLoginClass? user = await UserLoginClass.getLocalUser();
    String lastUpdateTime = '';
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> dataDownloadLog = await localDB.query(
        'data_download_log',
        columns: ['last_download_time'],
        orderBy: 'last_download_time DESC',
        limit: 1);
    if (dataDownloadLog.isNotEmpty) {
      lastUpdateTime = dataDownloadLog[0]['last_download_time'];
    }

    String deviceId = user?.deviceid ?? '';
    if (deviceId.isEmpty) {
      deviceId = user?.empCode ?? 'ABCD';
    }

    final String url =
        "${AppWebService.dataDownloadDictonaryURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&incremental_download=${incrementalDownload ? 'yes' : 'no'}&last_update_time=$lastUpdateTime&device_id=${deviceId}";
    print('Tables URL: $url');
    http.Response response =
        await http.get(Uri.parse(url)).timeout(const Duration(seconds: 60));
    if (response.statusCode == 200) {
      List<String> tableNames = response.body.split('\n');
      if (!tableNames.contains('customer_master')) {
        tableNames.add('customer_master');
      }
      final String updateDateTime = tableNames.removeAt(0).replaceAll('€', ' ');
      tableNames.removeWhere(
        (e) => (e == ''),
      );
      print('Tables: $tableNames');
      return DataDownloadDictionary(
          tableNames: tableNames, updateDateTime: updateDateTime);
    } else {
      throw Exception(
          'Failed to load data: ${response.statusCode} - ${response.body}');
    }
  }
}
