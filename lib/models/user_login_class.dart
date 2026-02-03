import 'dart:developer';
import 'dart:io';

import 'package:hive_flutter/hive_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:sqflite/sqflite.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:xml/xml.dart';
import 'package:intl/intl.dart';

const String nickName = AppWebService.nickname;

class UserLoginClass {
  String? message;
  String? empCode;
  String? empName;
  String? saleAccess;
  String? newPassword;
  String? deviceid;
  String? acedns;
  static const String boxName = 'userBox';
  static const String keyName = 'userProfile';

  UserLoginClass({
    this.message,
    this.empCode,
    this.empName,
    this.saleAccess,
    this.newPassword,
    this.deviceid,
    this.acedns,
  });

  UserLoginClass.fromJson(Map<dynamic, dynamic> json) {
    empCode = json['emp_code'];
    empName = json['emp_name'];
    saleAccess = json['sale_access'];
    newPassword = json['newpassword'];
    deviceid = json['deviceid'];
    acedns = json['acedns'];
  }

  UserLoginClass.fromXml(String xmlString) {
    final XmlDocument doc = XmlDocument.parse(xmlString);
    final XmlElement recordset = doc.rootElement;
    final XmlElement data = recordset.getElement('data') as XmlElement;
    empCode = data.getElement('emp_code')?.innerText;
    empName = data.getElement('emp_name')?.innerText;
    saleAccess = data.getElement('sale_access')?.innerText;
    newPassword = data.getElement('newpassword')?.innerText;
    deviceid = data.getElement('deviceid')?.innerText;
    acedns = data.getElement('acedns')?.innerText;
  }

  Map<String, dynamic> toJson() {
    return {
      'emp_code': empCode,
      'emp_name': empName,
      'sale_access': saleAccess,
      'newpassword': newPassword,
      'deviceid': deviceid,
      'acedns': acedns,
    };
  }

  static Future<UserLoginClass> empLoginCheck(
      String empId, String password, bool isCreateDB) async {
    String url = AppWebService.empLogin;
    String deviceId = '';

    Map<String, String> responseMessages = {
      "NOT LICENSED USER": "Unlicensed User. \n YOU ARE NOT A LICENSED USER",
      "NOT VALID USER":
          "Invalid User. \n Please provide valid UserId and Password",
      "6": "Device initialization Error.\nPlease ReLogin after few mins.",
      "5": "Device is having compatibility ERROR.\nPlease contact your ADMIN",
      "0":
          "Your ACEdns LOGIN credentials has been registered to different DEVICE.\nPlease contact your ADMIN."
    };
    // add parameters to the URL
    url +=
        '?emp_code=$empId&newpassword=$password&nick_name=$nickName&deviceid=';
    // check if the device is android or ios
    UserLoginClass loggedInUser =
        await UserLoginClass.getLocalUser() ?? UserLoginClass();
    //if(loggedInUser.deviceid!=null){
    if (loggedInUser.deviceid == null || loggedInUser.deviceid == '') {
      if (Platform.isAndroid) {
        deviceId =
            "$nickName$empId-Android${DateFormat('yyyyMMddHHmmss').format(DateTime.now())}";
        url += deviceId;
      } else if (Platform.isIOS) {
        // IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
        deviceId =
            "$nickName$empId-IOS${DateFormat('yyyyMMddHHmmss').format(DateTime.now())}";
        url += deviceId;
      } else {
        deviceId =
            "$nickName$empId-Unknown${DateFormat('yyyyMMddHHmmss').format(DateTime.now())}";
        url += deviceId;
      }
    } else {
      loggedInUser = await UserLoginClass.getLocalUser() ?? UserLoginClass();
      deviceId = loggedInUser.deviceid ?? '';
      url += deviceId;
    }
    // }

    log(url);
    // Await the http get response, then decode the json-formatted response.
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return UserLoginClass(message: 'No internet connection');
    }
    final response = await http.post(Uri.parse(url));
    if (response.statusCode == 200) {
      log('Login Response: ${response.body}');
      // check if the response is XML
      if (response.body.startsWith('<?xml')) {
        // parse the xml response
        UserLoginClass user = UserLoginClass.fromXml(response.body);
        user.message = 'Success';
        if (loggedInUser.deviceid == null || loggedInUser.deviceid == '') {
          user.deviceid = deviceId;
        }
        log(user.toJson().toString());
        if (isCreateDB) {
          log("hello world create DB");
          await LocalDB.createMyDatabase(user);
        } else {
          log("hello world already DB");
        }
        return user;
      } else {
        UserLoginClass user =
            UserLoginClass(message: responseMessages[response.body]);
        if (user.deviceid == '') {
          user.deviceid = deviceId;
        }
        return user;
      }
    } else {
      return UserLoginClass(
          message: 'Failed to load user, status code: ${response.statusCode}');
    }
  }

  // from DB
  factory UserLoginClass.fromDB(Map<String, dynamic> userMap) {
    return UserLoginClass(
        empCode: userMap['emp_code'],
        empName: userMap['emp_name'],
        saleAccess: userMap['sale_access'],
        newPassword: userMap['password'],
        deviceid: userMap['device_id']);
  }

  // to DB
  Map<String, dynamic> toDB() {
    return {
      'emp_code': empCode,
      'emp_name': empName,
      'sale_access': saleAccess,
      'password': newPassword,
      'device_id': deviceid
    };
  }

  // save user to local storage hive
  static Future<void> saveLocalUser(UserLoginClass user) async {
    final Database localDb = await LocalDB.openMyDatabase();
    final Map<String, dynamic> userMap = {
      'emp_code': user.empCode,
      'date': DateTime.now().toString(),
      'emp_name': user.empName,
      'device_id': user.deviceid,
      'password': user.newPassword,
      'sale_access': user.saleAccess,
    };
    try {
      await localDb.delete('employee_master_login');
    } catch (e) {
      log(e.toString());
    }
    await localDb.insert('employee_master_login', userMap);
  }

  // get user from local database
  static Future<UserLoginClass?> getLocalUser() async {
    // get local db
    Database localDb = await LocalDB.openMyDatabase();
    // get user_details table
    List<Map<String, dynamic>> userMapList = [];
    try {
      userMapList = await localDb.query('employee_master_login');
    } on Exception {
      return null;
    }
    // print(userMapList);
    // check if the user is not null
    if (userMapList.isNotEmpty) {
      return UserLoginClass.fromDB(userMapList[0]);
    }
    return null;
  }

  static void setincrementalDownload(bool value) async {
    final box = await Hive.openBox('incrementalDownload');
    box.put('incrementalDownload', value);
  }

  static Future<String> getincrementalDownload() async {
    final box = await Hive.openBox('incrementalDownload');
    final value = box.get('incrementalDownload') ?? false;
    return value ? 'yes' : 'no';
  }

  static Future<String> lastUpdateTime() async {
    var value = await LocalDB.rawQuery(
        "SELECT last_download_time FROM data_download_log ORDER BY last_download_time DESC");
    return value[0]['last_download_time'] ?? '';
  }

  static Future<bool> getLocalUserFirstLogin() async {
    // get local db
    Database localDb = await LocalDB.openMyDatabase();
    // get user_details table
    List<Map<String, dynamic>> userMapList = [];
    String date = DateFormat('yyyy-MM-dd').format(DateTime.now()).toString();
    try {
      userMapList = await localDb.rawQuery(
          "SELECT *FROM employee_master_login WHERE date like '$date%'");
    } on Exception {
      return false;
    }
    // print(userMapList);
    // check if the user is not null
    if (userMapList.isNotEmpty) {
      return true;
    }
    return false;
  }

  // remove user from local storage hive
  // static void removeLocalUser(BuildContext context) async {
  //   final localDB = await LocalDB.openMyDatabase();
  // await LocalDB.deleteMyDatabase();
  // Move to the login screen
  //   Navigator.pushReplacement(
  //       context, MaterialPageRoute(builder: (context) => const EmpLoginPage()));
  // }
}
