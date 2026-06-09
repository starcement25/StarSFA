import 'package:http/http.dart' as http;
import 'package:starsfa/log/log_service.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/location_class.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class RouteCheckinCheckout {
  static String _cleanXml(String xml) {
    return xml.replaceAll('\n', '').replaceAll('\t', '').replaceAll('  ', '');
  }

  static Future<bool> updateRouteCheckinCheckout() async {
    try {
      if (!await NetworkService.checkConnectionAll()) {
        print("TTT : NetworkService.checkConnectionAll ");
        return false;
      }
      final user = await UserLoginClass.getLocalUser();
      print("TTT : ${user?.empCode} ");

      final db = await LocalDB.openMyDatabase();
      final listData = await db.rawQuery(
          "SELECT * FROM location JOIN check_in_out_details ON location.trans_id=check_in_out_details.trans_id WHERE location.flag='0'");

      print("TTT : ${listData.length} ");

      for (var item in listData) {
        var id = item['trans_id'].toString();
        print("TTT : ${item['trans_id'].toString()} ");
        final attendance = await db.query(
          'check_in_out_details',
          where: 'trans_id = ?',
          whereArgs: [id],
        );
        if (attendance.isEmpty) {
          print("TTT : check_in_out_details ");
          return false;
        }

        Map<String, dynamic> json = attendance.first;

        final location = await LocationClass.getLocationById(id);
        if (location == null) {
          print("TTT : LocationClass.getLocationById ");
          return false;
        }

        String xmlData = _cleanXml('''
        <?xml version="1.0" encoding="UTF-8"?>
        <root>
          <check_in_out>
           <location>
            <emp_code><![CDATA[${location.empCode}]]></emp_code>
            <trans_id><![CDATA[$id]]></trans_id>
            <latt><![CDATA[${location.latt}]]></latt>
            <longi><![CDATA[${location.longi}]]></longi>
            <date><![CDATA[${location.date}]]></date>
          </location>
            <checkinoutdata>
              <trans_id><![CDATA[$id]]></trans_id>
              <check_in_time><![CDATA[${json['check_in_time']?.toString()}]]></check_in_time>
              <customer_code><![CDATA[${json['customer_code']?.toString()}]]></customer_code>
              <check_out_time><![CDATA[${json['check_out_time']?.toString()}]]></check_out_time>
              <remarks><![CDATA[${json['remarks']?.toString()}]]></remarks>
              <hint_remarks><![CDATA[${json['remarks']?.toString()}]]></hint_remarks>
              <date><![CDATA[${json['check_in_time']?.toString()}]]></date>
              <product_tagging><![CDATA[]]></product_tagging>
              <uploaded_photo><![CDATA[]]></uploaded_photo>
              <base_latt><![CDATA[${location.latt}]]></base_latt>
              <base_longi><![CDATA[${location.longi}]]></base_longi>
            </checkinoutdata>
          </check_in_out>
        </root>
        ''');

        final url = Uri.parse(
            '${AppWebService.operationdbCheckinCheckout}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}');

        final response = await http.post(url, body: xmlData);

        print(
            "TTT : ${AppWebService.operationdbCheckinCheckout}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode} ");

        await LogService.logSetup(
            '${AppWebService.operationdbCheckinCheckout}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}');
        await LogService.logSetup(
            "${response.body.trim()}  :  ${response.statusCode}  :  $xmlData");
      }

      return true;
    } catch (e) {
      print("updateAttendanceServer error : $e");
      return false;
    }
  }
}
