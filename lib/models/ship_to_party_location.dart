import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:sqflite/sqflite.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/user_login_class.dart';

class ShipToPartyOption {
  final String id;
  final String value;

  const ShipToPartyOption({required this.id, required this.value});
}

class ShipToPartyLocationRepository {
  static const _table = 'ship_to_party_location';

  static Future<Database> _database() async {
    final database = await LocalDB.openMyDatabase();
    await database.execute('''
      CREATE TABLE IF NOT EXISTS $_table (
        ship_to_party_code TEXT PRIMARY KEY,
        ship_to_party_name TEXT,
        customer_type TEXT,
        dealer_code TEXT,
        dealer_name TEXT,
        branch_code TEXT,
        branch_name TEXT,
        latitude TEXT,
        longitude TEXT,
        destination_name TEXT,
        flag INTEGER DEFAULT 0
      )
    ''');
    return database;
  }

  static Future<int> count({String type = 'total'}) async {
    final database = await _database();
    var where = '';
    if (type == 'pending') {
      where = '''WHERE latitude IS NULL OR longitude IS NULL
          OR TRIM(latitude) = '' OR TRIM(longitude) = ''
          OR CAST(latitude AS REAL) = 0 OR CAST(longitude AS REAL) = 0''';
    } else if (type != 'total') {
      where = '''WHERE latitude IS NOT NULL AND longitude IS NOT NULL
          AND TRIM(latitude) != '' AND TRIM(longitude) != ''
          AND CAST(latitude AS REAL) != 0 AND CAST(longitude AS REAL) != 0''';
    }
    final result = await database.rawQuery(
      'SELECT COUNT(*) AS item_count FROM $_table $where',
    );
    return Sqflite.firstIntValue(result) ?? 0;
  }

  static Future<List<ShipToPartyOption>> branches() async {
    final database = await _database();
    final rows = await database.rawQuery('''
      SELECT DISTINCT branch_code, branch_name FROM $_table
      WHERE branch_name IS NOT NULL AND TRIM(branch_name) != ''
      ORDER BY branch_name ASC
    ''');
    return rows
        .map((row) => ShipToPartyOption(
              id: row['branch_code']?.toString() ?? '',
              value: row['branch_name']?.toString() ?? '',
            ))
        .toList();
  }

  static Future<List<ShipToPartyOption>> dealers(
    String branchCode,
    String customerType,
  ) async {
    final database = await _database();
    final rows = await database.rawQuery('''
      SELECT DISTINCT dealer_code, dealer_name FROM $_table
      WHERE branch_code = ? AND customer_type = ? AND dealer_name IS NOT NULL
        AND TRIM(dealer_name) != ''
      ORDER BY dealer_name ASC
    ''', [branchCode, customerType]);
    return rows
        .map((row) => ShipToPartyOption(
              id: row['dealer_code']?.toString() ?? '',
              value: row['dealer_name']?.toString() ?? '',
            ))
        .toList();
  }

  static Future<List<ShipToPartyOption>> shipToParties(
      String branchCode, String dealerCode) async {
    final database = await _database();
    final rows = await database.rawQuery('''
      SELECT DISTINCT ship_to_party_code, ship_to_party_name, destination_name
      FROM $_table
      WHERE branch_code = ? AND dealer_code = ?
        AND ship_to_party_name IS NOT NULL
        AND (latitude IS NULL OR longitude IS NULL
          OR TRIM(latitude) = '' OR TRIM(longitude) = ''
          OR CAST(latitude AS REAL) = 0 OR CAST(longitude AS REAL) = 0)
      ORDER BY ship_to_party_name ASC, destination_name ASC
    ''', [branchCode, dealerCode]);
    return rows.map((row) {
      final code = row['ship_to_party_code']?.toString() ?? '';
      final name = row['ship_to_party_name']?.toString() ?? '';
      final destination = row['destination_name']?.toString() ?? '';
      return ShipToPartyOption(
        id: code,
        value: '$name ($code${destination.isEmpty ? '' : ' - $destination'})',
      );
    }).toList();
  }

  static Future<bool> submitLocation({
    required String branchCode,
    required String dealerCode,
    required String shipToPartyCode,
    required double latitude,
    required double longitude,
  }) async {
    final user = await UserLoginClass.getLocalUser();
    final payload = <String, dynamic>{
      'emp_code': user?.empCode ?? '',
      'branch_code': branchCode,
      'dns_ship_to_party_code': shipToPartyCode,
      'dealer_code': dealerCode,
      'latitude': latitude.toString(),
      'longitude': longitude.toString(),
    };

    var uploaded = false;
    try {
      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}misreport/api_update_ship_to_party_location.php'),
        headers: const {'Content-Type': 'application/json'},
        body: jsonEncode(payload),
      );
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        uploaded = decoded is Map &&
            decoded['process_status']?.toString().toLowerCase() != 'no';
      }
    } catch (_) {
      uploaded = false;
    }

    final database = await _database();
    await database.update(
      _table,
      {
        'latitude': latitude.toString(),
        'longitude': longitude.toString(),
        'flag': uploaded ? 2 : 1,
      },
      where: 'ship_to_party_code = ?',
      whereArgs: [shipToPartyCode],
    );
    return uploaded;
  }

  static Future<bool> uploadPending() async {
    final database = await _database();
    final rows =
        await database.query(_table, where: 'flag = ?', whereArgs: [1]);
    var allUploaded = true;
    for (final row in rows) {
      final uploaded = await submitLocation(
        branchCode: row['branch_code']?.toString() ?? '',
        dealerCode: row['dealer_code']?.toString() ?? '',
        shipToPartyCode: row['ship_to_party_code']?.toString() ?? '',
        latitude: double.tryParse(row['latitude']?.toString() ?? '') ?? 0,
        longitude: double.tryParse(row['longitude']?.toString() ?? '') ?? 0,
      );
      if (!uploaded) allUploaded = false;
    }
    return allUploaded;
  }

  static Future<bool> downloadAll() async {
    final user = await UserLoginClass.getLocalUser();
    final empCode = user?.empCode?.trim() ?? '';
    if (empCode.isEmpty) return false;

    try {
      final response = await http.get(Uri.parse(
          '${AppWebService.baseURL}misreport/api_get_ship_to_party_list.php?emp_code=$empCode'));
      if (response.statusCode != 200) return false;

      final downloaded = <Map<String, Object?>>[];
      var columnCount = -1;
      for (final line in const LineSplitter().convert(response.body)) {
        if (line.contains('¥')) {
          final parts = line.split('¥');
          if (parts.length > 1) {
            columnCount = int.tryParse(parts[1].trim()) ?? -1;
          }
          continue;
        }
        if (line.contains('#')) continue;
        final values = line.split('^');
        if (columnCount < 10 || values.length != columnCount) continue;
        final latitude = double.tryParse(values[7].trim()) ?? 0;
        final longitude = double.tryParse(values[8].trim()) ?? 0;
        downloaded.add({
          'ship_to_party_code': values[0],
          'ship_to_party_name': values[1],
          'customer_type': values[2],
          'branch_code': values[3],
          'branch_name': values[4],
          'dealer_code': values[5],
          'dealer_name': values[6],
          'latitude': values[7],
          'longitude': values[8],
          'destination_name': values[9],
          'flag': latitude == 0 || longitude == 0 ? 0 : 2,
        });
      }

      final database = await _database();
      await database.transaction((transaction) async {
        await transaction.delete(_table);
        final batch = transaction.batch();
        for (final row in downloaded) {
          batch.insert(_table, row,
              conflictAlgorithm: ConflictAlgorithm.replace);
        }
        await batch.commit(noResult: true);
      });
      return true;
    } catch (error) {
      return false;
    }
  }

  static Future<bool> synchronize() async {
    final uploaded = await uploadPending();
    if (!uploaded) return false;
    return downloadAll();
  }
}
