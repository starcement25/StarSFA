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
    print('Database Path: $path');
    final database = await openDatabase(
      path,
      version: 1,
      onCreate: (db, version) async {
        print("onCreate");
        // set android locale
        // await db.androidSetLocale('en-US');
      },
      onOpen: (db) async {
        print("onOpen");
        // set android locale
        // await db.androidSetLocale('en-US');
      },
    );
    // create the tables
    await TableStructureDetails.createTableStructure(user, database);

    print("Hey");
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
    // print('Query: $query');
    final List<Map<String, dynamic>> result = await database.rawQuery(query);
    // print('Result: $result');
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
      ..writeAsBytesSync(zipData);

    print('Database backed up to: $backupPath');

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

    var request = http.MultipartRequest('POST',
        Uri.parse('${AppWebService.baseURL}star_sfa_db_backup_email.php'));

    try {
      request.fields['toMail'] = email;
      request.fields['subject'] = subject;
      request.fields['body'] = body;
      request.files
          .add(await http.MultipartFile.fromPath('attachment', backupPath));
      var response = await request.send();
      if (response.statusCode == 200) {
        // print the response
        String respBody = await response.stream.bytesToString();
        print('Email sent successfully: $respBody');
        return true;
      } else {
        print('Failed to send email: ${response.statusCode}');
        return false;
      }
    } catch (error) {
      print('Failed to send email: $error');
      return false;
    }
  }

  // New Table
  static Future<void> createCustomerQuantityTable() async {
    final Database database = await openMyDatabase();
    await database.execute('''
    CREATE TABLE IF NOT EXISTS customer_quantity (
      customer_name TEXT,
      customer_code TEXT,
      competitor_name TEXT,
      competitor_code TEXT,
      quantity TEXT,
      flag INTEGER DEFAULT 0
    )
  ''');
  }

  static Future<void> createCustomerAgeingTable() async {
    final Database database = await openMyDatabase();
    // await database.execute('DROP TABLE IF EXISTS customer_ageing');
    await database.execute('''
      CREATE TABLE IF NOT EXISTS customer_ageing (
        customer_name TEXT,
        customer_code TEXT,
        title_1 TEXT, value_1 TEXT, invoice_count_1 TEXT,
        title_2 TEXT, value_2 TEXT, invoice_count_2 TEXT,
        title_3 TEXT, value_3 TEXT, invoice_count_3 TEXT,
        title_4 TEXT, value_4 TEXT, invoice_count_4 TEXT,
        title_5 TEXT, value_5 TEXT, invoice_count_5 TEXT,
        title_6 TEXT, value_6 TEXT, invoice_count_6 TEXT,
        title_7 TEXT, value_7 TEXT, invoice_count_7 TEXT,
        title_8 TEXT, value_8 TEXT, invoice_count_8 TEXT,
        title_9 TEXT, value_9 TEXT, invoice_count_9 TEXT,
        total_amount TEXT,
        total_invoice_count TEXT
      )
    ''');
  }

  static Future<void> createCustomerAgeingInvoiceNoTable() async {
    final Database database = await openMyDatabase();
    // await database.execute('DROP TABLE IF EXISTS customer_ageing_invoice_no');
    await database.execute('''
    CREATE TABLE IF NOT EXISTS customer_ageing_invoice_no (
      customer_name TEXT,
      customer_code TEXT,
      invoice_no TEXT,
      invoice_date TEXT,
      invoice_value TEXT,
      invoice_age TEXT
    )
  ''');
  }

  static Future<int> insertCustomerQuantity(Map<String, dynamic> data) async {
    final Database database = await openMyDatabase();
    final existing = await database.query(
      'customer_quantity',
      where: 'customer_code = ? AND competitor_code = ?',
      whereArgs: [data['customer_code'], data['competitor_code']],
    );

    if (existing.isNotEmpty) {
      return -1;
    }

    return await database.insert(
      'customer_quantity',
      data,
      conflictAlgorithm: ConflictAlgorithm.ignore,
    );
  }

  static Future<int> updateCustomerQuantity(
      String customerCode, String competitorCode, String quantity) async {
    final Database database = await openMyDatabase();

    final result = await database.update(
      'customer_quantity',
      {
        'quantity': quantity,
        'flag': 1,
      },
      where: 'customer_code = ? AND competitor_code = ?',
      whereArgs: [customerCode, competitorCode],
    );

    return result;
  }

  static Future<String> getQuantity(
      String customerCode, String competitorCode) async {
    final Database database = await openMyDatabase();

    final result = await database.query(
      'customer_quantity',
      columns: ['quantity'],
      where: 'customer_code = ? AND competitor_code = ?',
      whereArgs: [customerCode, competitorCode],
    );
    print(customerCode);
    print(competitorCode);
    if (result.isNotEmpty) {
      return result.first['quantity'] as String? ?? '0';
    }

    return '0';
  }

  static Future<int> insertCustomerAgeing(Map<String, dynamic> data) async {
    final Database database = await openMyDatabase();
    final existing = await database.query(
      'customer_ageing',
      where: 'customer_code = ?',
      whereArgs: [data['customer_code']],
    );

    if (existing.isNotEmpty) {
      await database.delete(
        'customer_ageing',
        where: 'customer_code = ?',
        whereArgs: [data['customer_code']],
      );
    }

    return await database.insert(
      'customer_ageing',
      data,
      conflictAlgorithm: ConflictAlgorithm.ignore,
    );
  }

  static Future<Map<String, dynamic>> getCustomerAgeing(
      String customerCode) async {
    final Database database = await openMyDatabase();

    final result = await database.query(
      'customer_ageing',
      where: 'customer_code = ?',
      whereArgs: [customerCode],
    );
    if (result.isNotEmpty) {
      return Map<String, dynamic>.from(result.first);
    }
    return {};
  }

  static Future<List<Map<String, dynamic>>> getCustomerAgeingList() async {
    final Database database = await openMyDatabase();
    final List<Map<String, Object?>> records =
        await database.query('customer_ageing', orderBy: 'customer_name');
    return records.map((e) => Map<String, dynamic>.from(e)).toList();
  }

  static Future<int> insertCustomerAgeingInvoiceNo(
      Map<String, dynamic> data) async {
    final Database database = await openMyDatabase();
    final existing = await database.query(
      'customer_ageing_invoice_no',
      where: 'invoice_no = ?',
      whereArgs: [data['invoice_no']],
    );

    if (existing.isNotEmpty) {
      await database.delete(
        'customer_ageing_invoice_no',
        where: 'invoice_no = ?',
        whereArgs: [data['invoice_no']],
      );
    }

    return await database.insert(
      'customer_ageing_invoice_no',
      data,
      conflictAlgorithm: ConflictAlgorithm.ignore,
    );
  }

  static Future<List<Map<String, dynamic>>> getCustomerAgeingInvoiceNo(
      String customerCode) async {
    final Database database = await openMyDatabase();
    final List<Map<String, Object?>> records = await database.rawQuery(
      'SELECT * FROM customer_ageing_invoice_no WHERE customer_code = ? ORDER BY CAST(invoice_age AS INTEGER) ASC',
      [customerCode],
    );
    return records.map((e) => Map<String, dynamic>.from(e)).toList();
  }
}
