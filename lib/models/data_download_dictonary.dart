import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class DataDownloadDictionary {
  String? updateDateTime;
  List<String>? tableNames;

  DataDownloadDictionary({this.tableNames, this.updateDateTime});

  // get the table names
  static Future<DataDownloadDictionary> getTableNames(
      bool incrementalDownload) async {
    // check if the device is connected to the internet
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    // get local user from hive
    UserLoginClass? user = await UserLoginClass.getLocalUser();
    // get last update time
    String lastUpdateTime = '';
    // get latest from data_download_log sorting by last_download_time\
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
    // String deviceId = '';
    if (deviceId.isEmpty) {
      deviceId = user?.empCode ?? 'ABCD';
    }

    final String url =
        "${AppWebService.dataDownloadDictonaryURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&incremental_download=${incrementalDownload ? 'yes' : 'no'}&last_update_time=$lastUpdateTime&device_id=${deviceId}";
    // final String url = "${AppWebService.dataDownloadDictonaryURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&incremental_download=${incrementalDownload ? 'yes' : 'no'}&last_update_time=$lastUpdateTime&device_id=";
    print('Tables URL: $url');
    // get response from the server
    http.Response response =
        await http.get(Uri.parse(url)).timeout(const Duration(seconds: 60));
//     const responseBody = '''
// menu_details
// user_details
// order_details
// product_details
// route_plan_details
// survey_form_details
// survey_table_view
// market_feedback_details
// route_master
// bank_master
// customer_master
// credit_limit
// outstanding_master
// product_group_master
// product_master
// route_plan
// emp_master
// branch_master
// survey_category_master
// survey_input_details
// generic_oil_master
// competitor_group_master
// menu_access
// destination_master
// self_appraisal_details
// self_appraisal_customer_wise
// self_appraisal_branch_wise
// branchwise_scheme_PDF
// golden_rules
// yellow-card-date-validation
// yellow-card-date-validation_customerwise
// attendance_checkout_details
// branch_geo_fencing
// ''';
    // final response = http.Response(responseBody, 200);
    // check if the response is successful
    if (response.statusCode == 200) {
      // split the response body
      List<String> tableNames = response.body.split('\n');
      if (!tableNames.contains('customer_master')) {
        tableNames.add('customer_master');
      }
      // remove the first element and store
      final String updateDateTime = tableNames.removeAt(0).replaceAll('€', ' ');
      // const String updateDateTime = '2021-10-01 00:00:00';
      // remove any blank table names
      tableNames.removeWhere(
        (e) => (e == ''),
      );
      print('Tables: $tableNames');

      // String url1 = AppWebService.deviceIdUpdateURL;
      // url1 +=
      // '?emp_code=${user?.empCode}&nick_name=${AppWebService.nickname}&deviceId=${user?.deviceid}';

      // final response1 = await http.post(Uri.parse(url1));

      // return the response body
      return DataDownloadDictionary(
          tableNames: tableNames, updateDateTime: updateDateTime);
    } else {
      // error exception
      throw Exception(
          'Failed to load data: ${response.statusCode} - ${response.body}');
    }
  }
}
