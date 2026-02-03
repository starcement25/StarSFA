import 'dart:core';
import 'dart:developer';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
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

  toJson() {
    return {
      'emp_code': empCode,
      'trans_id': transId,
      'date': date,
      'flag': flag,
    };
  }

  // from json
  factory AttendanceClass.fromJson(Map<String, dynamic> json) {
    return AttendanceClass(
      empCode: json['emp_code'],
      transId: json['trans_id'],
      date: json['date'],
      flag: json['flag'].toString(),
    );
  }

  // get attendance by transId
  static Future<AttendanceClass> getAttendanceByTransId(String transId) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> attendance = await localDB.query(
      'attendence',
      where: 'trans_id like ?',
      whereArgs: ['%$transId%'],
    );
    return AttendanceClass(
      empCode: attendance[0]['emp_code'].toString(),
      transId: attendance[0]['trans_id'].toString(),
      date: attendance[0]['date'].toString(),
      flag: attendance[0]['flag'].toString(),
    );
  }

  // save attendance
  Future<bool> saveAttendance() async {
    final localDB = await LocalDB.openMyDatabase();
    await localDB.insert('attendence', toJson());
    final isUploaded = await updateAttendanceServer(transId ?? '');
    return isUploaded;
  }

  // save attendance checkout to server
  static Future<bool> saveAttendanceServer() async {
    // get attendance and checkout data from local db
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> attendance = await localDB.query(
      'attendence',
      where: 'flag = ?',
      whereArgs: ['0'],
    );
    if (attendance.isEmpty) {
      return true;
    }
    List<AttendanceClass> attendanceList = [];
    for (int i = 0; i < attendance.length; i++) {
      attendanceList.add(AttendanceClass.fromJson(attendance[i]));
    }
    for (int i = 0; i < attendanceList.length; i++) {
      final isUploaded = (attendanceList[i].transId?.contains('C') ?? false)
          ? await updateCheckoutServer(attendanceList[i].transId ?? '')
          : await updateAttendanceServer(attendanceList[i].transId ?? '');
      if (!isUploaded) {
        return false;
      }
    }
    return true;
  }

  // upload attendance to server
  static Future<bool> updateAttendanceServer(String id) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final localDB = await LocalDB.openMyDatabase();
    // get location by id
    final LocationClass? location = await LocationClass.getLocationById(id);
    if (location == null) {
      return false;
    }
    // get attendance by id
    final AttendanceClass attendanceClass = await getAttendanceByTransId(id);

    String xmlData = '''
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
            <emp_code><![CDATA[${attendanceClass.empCode}]]></emp_code>
            <date><![CDATA[${attendanceClass.date?.split(' ')[0]}]]></date>
          </attendancedata>
        </attendance>
      </root>
    ''';
    // remove all new lines
    xmlData = xmlData.replaceAll('\n', '');
    // remove all tabs
    xmlData = xmlData.replaceAll('\t', '');
    // remove all big spaces
    xmlData = xmlData.replaceAll('  ', '');
    final String lastUpdateTime =
        DateFormat('yyyy-MM-dd€HH:mm:ss').format(DateTime.now());
    final Uri url = Uri.parse(
        '${AppWebService.operationdbAttendance}?nick_name=${AppWebService.nickname}&emp_code=${attendanceClass.empCode}&last_update_time=$lastUpdateTime');
    final response = await http.post(url, body: xmlData);
    if (response.statusCode == 200 && response.body == '1') {
      // update flag
      await localDB.update('attendence', {'flag': '1'},
          where: 'trans_id = ?', whereArgs: [id]);
      return true;
    } else {
      return false;
    }
  }

  // upload checkout to server
  static Future<bool> updateCheckoutServer(String id) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final localDB = await LocalDB.openMyDatabase();
    // get location by id
    final LocationClass? location = await LocationClass.getLocationById(id);
    if (location == null) {
      return false;
    }
    // get attendance by id
    final AttendanceClass attendanceClass = await getAttendanceByTransId(id);

    // prepare data
    String xmlData = '''
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
    ''';
    // remove all new lines
    xmlData = xmlData.replaceAll('\n', '');
    // remove all tabs
    xmlData = xmlData.replaceAll('\t', '');
    // remove all big spaces
    xmlData = xmlData.replaceAll('  ', '');
    final String lastUpdateTime =
        DateFormat('yyyy-MM-dd€HH:mm:ss').format(DateTime.now());
    final Uri url = Uri.parse(
        '${AppWebService.operationdbCheckout}?nick_name=${AppWebService.nickname}&emp_code=${attendanceClass.empCode}&last_update_time=$lastUpdateTime');
    final response = await http.post(url, body: xmlData);
    if (response.statusCode == 200 && response.body == '1') {
      // update flag
      await localDB.update('attendence', {'flag': '1'},
          where: 'trans_id = ?', whereArgs: [id]);
      return true;
    } else {
      return false;
    }
  }

  static Future<bool> getAttendanceByDate(String date) async {
    final localDB = await LocalDB.openMyDatabase();
    // where date like %date%
    final List<Map<String, Object?>> attendance = await localDB
        .query('attendence', where: 'date like ?', whereArgs: ['%$date%']);
    if (attendance.isEmpty) {
      return false;
    } else {
      return true;
    }
  }
}
