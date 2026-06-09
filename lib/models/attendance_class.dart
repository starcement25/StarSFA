import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:starsfa/log/log_service.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/location_class.dart';
import 'package:starsfa/models/network_service.dart';

class AttendanceClass {
  String? empCode;
  String? transId;
  String? date;
  String? flag;

  AttendanceClass({
    this.empCode,
    this.transId,
    this.date,
    this.flag,
  });

  Map<String, dynamic> toJson() {
    return {
      'emp_code': empCode,
      'trans_id': transId,
      'date': date,
      'flag': flag ?? '0',
    };
  }

  factory AttendanceClass.fromJson(Map<String, dynamic> json) {
    return AttendanceClass(
      empCode: json['emp_code']?.toString(),
      transId: json['trans_id']?.toString(),
      date: json['date']?.toString(),
      flag: json['flag']?.toString() ?? '0',
    );
  }

  // ================= GET ATTENDANCE =================

  static Future<AttendanceClass?> getAttendanceByTransId(String transId) async {
    try {
      final db = await LocalDB.openMyDatabase();

      final attendance = await db.query(
        'attendence',
        where: 'trans_id = ?',
        whereArgs: [transId],
      );

      if (attendance.isEmpty) return null;

      return AttendanceClass.fromJson(attendance.first);
    } catch (e) {
      print("getAttendanceByTransId error : $e");
      return null;
    }
  }

  // ================= SAVE ATTENDANCE =================

  Future<bool> saveAttendance() async {
    try {
      final db = await LocalDB.openMyDatabase();
      await db.insert('attendence', toJson());

      return await updateAttendanceServer(transId ?? '');
    } catch (e) {
      print("saveAttendance error : $e");
      return false;
    }
  }

  // ================= BULK UPLOAD =================

  static Future<bool> saveAttendanceServer() async {
    try {
      final db = await LocalDB.openMyDatabase();

      final attendance = await db.query(
        'attendence',
        where: 'flag = ?',
        whereArgs: ['0'],
      );

      if (attendance.isEmpty) {
        print("No pending attendance");
        return true;
      }

      List<AttendanceClass> attendanceList =
          attendance.map((e) => AttendanceClass.fromJson(e)).toList();

      for (final item in attendanceList) {
        bool uploaded = (item.transId?.contains('C') ?? false)
            ? await updateCheckoutServer(item.transId ?? '')
            : await updateAttendanceServer(item.transId ?? '');

        if (!uploaded) return false;
      }

      print("Attendance upload completed");
      return true;
    } catch (e) {
      print("saveAttendanceServer error : $e");
      return false;
    }
  }

  // ================= XML CLEANER =================

  static String _cleanXml(String xml) {
    return xml.replaceAll('\n', '').replaceAll('\t', '').replaceAll('  ', '');
  }

  // ================= UPLOAD ATTENDANCE =================

  static Future<bool> updateAttendanceServer(String id) async {
    try {
      if (!await NetworkService.checkConnectionAll()) {
        print("updateAttendanceServer no internet");
        return false;
      }

      final db = await LocalDB.openMyDatabase();

      final location = await LocationClass.getLocationById(id);
      if (location == null) return false;

      final attendance = await getAttendanceByTransId(id);
      if (attendance == null) return false;

      String xmlData = _cleanXml('''
      <?xml version="1.0" encoding="UTF-8"?>
      <root>
        <attendance>
          <location>
            <emp_code><![CDATA[${location.empCode}]]></emp_code>
            <trans_id><![CDATA[${location.transId}]]></trans_id>
            <latt><![CDATA[${location.latt}]]></latt>
            <longi><![CDATA[${location.longi}]]></longi>
            <date><![CDATA[${location.date}]]></date>
            <TA_DA_MODE><![CDATA[]]></TA_DA_MODE>
          </location>
          <attendancedata>
            <emp_code><![CDATA[${attendance.empCode}]]></emp_code>
            <date><![CDATA[${attendance.date?.split(' ')[0]}]]></date>
          </attendancedata>
        </attendance>
      </root>
      ''');

      final lastUpdateTime =
          DateFormat('yyyy-MM-dd HH:mm:ss').format(DateTime.now());

      final url = Uri.parse(
          '${AppWebService.operationdbAttendance}?nick_name=${AppWebService.nickname}&emp_code=${attendance.empCode}&last_update_time=$lastUpdateTime');

      final response = await http.post(url, body: xmlData);

      await LogService.logSetup(
          '${AppWebService.operationdbAttendance}?nick_name=${AppWebService.nickname}&emp_code=${attendance.empCode}&last_update_time=$lastUpdateTime');
      await LogService.logSetup(xmlData);
      await LogService.logSetup(response.statusCode.toString());

      if (response.statusCode == 200 && response.body.trim() == '1') {
        await db.update(
          'attendence',
          {'flag': '1'},
          where: 'trans_id = ?',
          whereArgs: [id],
        );

        print("Attendance uploaded");
        return true;
      }

      return false;
    } catch (e) {
      print("updateAttendanceServer error : $e");
      return false;
    }
  }

  // ================= UPLOAD CHECKOUT =================

  static Future<bool> updateCheckoutServer(String id) async {
    try {
      if (!await NetworkService.checkConnectionAll()) return false;

      final db = await LocalDB.openMyDatabase();

      final location = await LocationClass.getLocationById(id);
      if (location == null) return false;

      final attendance = await getAttendanceByTransId(id);
      if (attendance == null) return false;

      String xmlData = _cleanXml('''
      <?xml version="1.0" encoding="UTF-8"?>
      <root>
        <checkout>
          <location>
            <emp_code><![CDATA[${location.empCode}]]></emp_code>
            <trans_id><![CDATA[${location.transId}]]></trans_id>
            <latt><![CDATA[${location.latt}]]></latt>
            <longi><![CDATA[${location.longi}]]></longi>
            <date><![CDATA[${location.date}]]></date>
            <TA_DA_MODE><![CDATA[]]></TA_DA_MODE>
          </location>
        </checkout>
      </root>
      ''');

      final lastUpdateTime =
          DateFormat('yyyy-MM-dd HH:mm:ss').format(DateTime.now());

      final url = Uri.parse(
          '${AppWebService.operationdbCheckout}?nick_name=${AppWebService.nickname}&emp_code=${attendance.empCode}&last_update_time=$lastUpdateTime');

      final response = await http.post(url, body: xmlData);

      await LogService.logSetup(
          '${AppWebService.operationdbCheckout}?nick_name=${AppWebService.nickname}&emp_code=${attendance.empCode}&last_update_time=$lastUpdateTime');
      await LogService.logSetup(xmlData);
      await LogService.logSetup(response.statusCode.toString());
      await LogService.logSetup(response.body.trim());

      if (response.statusCode == 200 && response.body.trim() == '1') {
        await db.update(
          'attendence',
          {'flag': '1'},
          where: 'trans_id = ?',
          whereArgs: [id],
        );
        await LogService.logSetup('update attendence local db');
        print("Checkout uploaded");
        return true;
      } else {
        await LogService.logSetup('update attendence local db error');
      }

      return false;
    } catch (e) {
      await LogService.logSetup('update updateCheckoutServer error : $e');
      print("updateCheckoutServer error : $e");
      return false;
    }
  }

  // ================= CHECK ATTENDANCE BY DATE =================

  static Future<bool> getAttendanceByDate(String date) async {
    try {
      final db = await LocalDB.openMyDatabase();

      final attendance = await db.query(
        'attendence',
        where: 'date LIKE ?',
        whereArgs: ['%$date%'],
      );

      return attendance.isNotEmpty;
    } catch (e) {
      print("getAttendanceByDate error : $e");
      return false;
    }
  }
}
