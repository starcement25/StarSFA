import 'dart:developer';
import 'dart:io';

import 'package:archive/archive_io.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/table_structure_details.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:device_info_plus/device_info_plus.dart';

class LocalDB {
  static Future<Database> createMyDatabase(UserLoginClass user) async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, 'acedns_${AppWebService.nickname}_sfa.db');
    log('Database Path: $path');
    final database = await openDatabase(
      path,
      version: 1,
      onCreate: (db, version) async {
        log("onCreate");
        // set android locale
        // await db.androidSetLocale('en-US');
      },
      onOpen: (db) async {
        log("onOpen");
        // set android locale
        // await db.androidSetLocale('en-US');
      },
    );
    // create the tables
    await TableStructureDetails.createTableStructure(user, database);
log("Hey");
    return database;
  }

  static Future<Database> openMyDatabase() async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, 'acedns_${AppWebService.nickname}_sfa.db');
    // print('Database Path: $path');
    final database = await openDatabase(
      path,
      version: 1,
      onCreate: (db, version) async {
        // set android locale
        // await db.androidSetLocale('en-US');
        await db.execute('''
          CREATE TABLE IF NOT EXISTS employee_master_login (
            emp_code TEXT PRIMARY KEY,
            date TEXT,
            emp_name TEXT,
            device_id TEXT,
            password TEXT,
            sale_access TEXT
          )
        ''');
      },
      onOpen: (db) async {},
    );
    return database;
  }

  // raw query
  static Future<List<Map<String, dynamic>>> rawQuery(String query) async {
    final Database database = await openMyDatabase();
    log('Query: $query');
    final List<Map<String, dynamic>> result = await database.rawQuery(query);
    log('Result: $result');
    return result;
  }

  // save db inside a zip file
  static Future<bool> backupDB() async {
    final user = await UserLoginClass.getLocalUser();
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, 'acedns_${AppWebService.nickname}_sfa.db');
    final backupPath =
        join(dbPath, '${AppWebService.nickname}_${user?.empCode}.zip');
    // Read the database file
    final bytes = File(path).readAsBytesSync();

    // Create a zip file and add the database file to it
    final archive = Archive();
    archive.addFile(ArchiveFile(
        'acedns_${AppWebService.nickname}_${user?.empCode}_sfa.db',
        bytes.length,
        bytes));

    // Encode the zip file
    final zipData = ZipEncoder().encode(archive);

    // Save the zip file
    File(backupPath)
      ..createSync(recursive: true)
      ..writeAsBytesSync(zipData ?? []);

    log('Database backed up to: $backupPath');

    // send the backup file via email
    final resp = await sendEmail(backupPath);
    return resp;
  }

  // send email
  static Future<bool> sendEmail(String backupPath) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    UserLoginClass? user = await UserLoginClass.getLocalUser();
    String email = "centralcell@starcement.co.in";
    // 26-07-2024 @ 09:51:52 hrs.
    String dateTime =
        DateFormat('dd-MM-yyyy @ HH:mm:ss').format(DateTime.now());
    String subject =
        'STAR DB Backup of ${user?.empName}(${user?.empCode}) as on $dateTime';
    // Device type - Android, iOS, etc.
    final DeviceInfoPlugin deviceInfo = DeviceInfoPlugin();
    //AndroidDeviceInfo androidInfo = await deviceInfo.androidInfo;
    //IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
    String deviceType = 'Unknown';
    if (Platform.isAndroid) {
      deviceType = 'Android';
    } else if (Platform.isIOS) {
      deviceType = 'iOS';
    }
    // Device Manufacturer :
    String manufacturer = 'Unknown';
    if (Platform.isAndroid) {
      final AndroidDeviceInfo androidInfo = await deviceInfo.androidInfo;
      manufacturer = androidInfo.manufacturer;
    } else if (Platform.isIOS) {
      final IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
      manufacturer = iosInfo.model;
    }
    // Device Model :
    String model = 'Unknown';
    if (Platform.isAndroid) {
      final AndroidDeviceInfo androidInfo = await deviceInfo.androidInfo;
      model = androidInfo.model;
    } else if (Platform.isIOS) {
      final IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
      model = iosInfo.model;
    }
    // Device OS Version :
    String osVersion = 'Unknown';
    if (Platform.isAndroid) {
      final AndroidDeviceInfo androidInfo = await deviceInfo.androidInfo;
      osVersion = androidInfo.version.release;
    } else if (Platform.isIOS) {
      final IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
      osVersion = iosInfo.systemVersion;
    }
    // Device OS Type :
    String osType = 'Unknown';
    if (Platform.isAndroid) {
      osType = 'Android';
    } else if (Platform.isIOS) {
      osType = 'iOS';
    }
    // Device ID :
    String deviceId = 'Unknown';
    if (Platform.isAndroid) {
      final AndroidDeviceInfo androidInfo = await deviceInfo.androidInfo;
      deviceId = androidInfo.id;
    } else if (Platform.isIOS) {
      final IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
      deviceId = iosInfo.name;
    }
    final body = '''
    <h1>STAR DB Backup</h1>
    <p>Backup of ${user?.empName}(${user?.empCode}) as on $dateTime</p>
    <p>Device Type: $deviceType</p>
    <p>Manufacturer: $manufacturer</p>
    <p>Model: $model</p>
    <p>OS Version: $osVersion</p>
    <p>OS Type: $osType</p>
    <p>Device ID: $deviceId</p>
    ''';

    var request = http.MultipartRequest(
        'POST',
        Uri.parse(
            'https://sfa.starcement.co.in/star_sfa_db_backup_email.php'));

    try {
      request.fields['toMail'] = email;
      request.fields['subject'] = subject;
      request.fields['body'] = body;
      request.files
          .add(await http.MultipartFile.fromPath('attachment', backupPath));
      var response = await request.send();
      if (response.statusCode == 200) {
        // log the response
        String respBody = await response.stream.bytesToString();
        log('Email sent successfully: $respBody');
        return true;
      } else {
        log('Failed to send email: ${response.statusCode}');
        return false;
      }
    } catch (error) {
      log('Failed to send email: $error');
      return false;
    }
  }
}
