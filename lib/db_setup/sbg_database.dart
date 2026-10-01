import 'package:path/path.dart';
import 'package:sqflite/sqflite.dart';
import 'package:starsfa/db_setup/dataset/CustomerCompetitorQuantityModel.dart';
import 'package:starsfa/db_setup/dataset/CustomerMarketFeedbackDetailsDataSet.dart';
import 'package:starsfa/db_setup/dataset/MarketFeedbackAddressDataSet.dart';
import 'package:starsfa/db_setup/dataset/SBGFeedbackModel.dart';

class SBGDatabase {
  static final SBGDatabase instance = SBGDatabase._init();
  static Database? _database;

  SBGDatabase._init();

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDB('sbg_store.db');
    return _database!;
  }

  Future<Database> _initDB(String filePath) async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, filePath);
    return await openDatabase(
      path,
      version: 1,
      onCreate: _createDB,
      onOpen: _ensureCustomerMarketFeedbackDetailsColumns,
    );
  }

  Future<void> _ensureCustomerMarketFeedbackDetailsColumns(Database db) async {
    const requiredColumns = <String, String>{
      'customer_code': 'TEXT',
      'customer_type': 'TEXT',
      'customer_universe_type': 'TEXT',
      'customer_state': 'TEXT',
      'customer_district': 'TEXT',
      'customer_block_name': 'TEXT',
      'customer_gram_panchayat': 'TEXT',
      'approve_status': 'TEXT',
      'approve_by': 'TEXT',
    };

    final tableInfo = await db.rawQuery(
      'PRAGMA table_info(customer_market_feedback_details)',
    );
    if (tableInfo.isEmpty) {
      await db.execute('''
        CREATE TABLE customer_market_feedback_details (
          customer_code TEXT,
          customer_type TEXT,
          customer_universe_type TEXT,
          customer_state TEXT,
          customer_district TEXT,
          customer_block_name TEXT,
          customer_gram_panchayat TEXT,
          approve_status TEXT,
          approve_by TEXT
        )
      ''');
      return;
    }

    final existingColumns = tableInfo
        .map((column) => column['name'].toString().toLowerCase())
        .toSet();

    await db.transaction((txn) async {
      for (final column in requiredColumns.entries) {
        if (!existingColumns.contains(column.key.toLowerCase())) {
          await txn.execute(
            'ALTER TABLE customer_market_feedback_details '
            'ADD COLUMN ${column.key} ${column.value}',
          );
        }
      }
    });
  }

  Future _createDB(Database db, int version) async {
    // customer_competitor_quantity table
    await db.execute('''
      CREATE TABLE customer_competitor_quantity (
        competitor_quantity_id TEXT,
        customer_code TEXT,
        customer_name TEXT,
        mandatory TEXT,
        competitor_name TEXT,
        type TEXT,
        quantity TEXT,
        customer_dns TEXT,
        flag INTEGER DEFAULT 0
      )
    ''');

    // sbg_feedback table
    await db.execute('''
      CREATE TABLE sbg_feedback (
        customer_code TEXT,
        competitor_code TEXT,
        quantity TEXT,
        date_time TEXT,
        flag INTEGER DEFAULT 1
      )
    ''');

    // customer_market_feedback_details table
    await db.execute('''
      CREATE TABLE customer_market_feedback_details (
        customer_code TEXT,
        customer_type TEXT,
        customer_universe_type TEXT,
        customer_state TEXT,
        customer_district TEXT,
        customer_block_name TEXT,
        customer_gram_panchayat TEXT,
        approve_status TEXT,
        approve_by TEXT
      )
    ''');

    // market_feedback_address table
    await db.execute('''
      CREATE TABLE market_feedback_address (
        state TEXT,
        district TEXT,
        block_name TEXT,
        gram_panchayat TEXT
      )
    ''');
  }

  // ─────────────────────────────────────────────
  // customer_competitor_quantity — FUNCTIONS
  // ─────────────────────────────────────────────
  // Batch insert — delete all then insert fresh (fast for 87k+ rows)
  Future<void> insertCustomerCompetitorQuantityBatch(
      List<CustomerCompetitorQuantityModel> list) async {
    final db = await database;

    // Clear existing data first in one shot
    await db.delete('customer_competitor_quantity');

    const int chunkSize = 500;
    for (int i = 0; i < list.length; i += chunkSize) {
      final chunk = list.sublist(
          i, i + chunkSize > list.length ? list.length : i + chunkSize);
      final batch = db.batch();
      for (final item in chunk) {
        batch.insert(
          'customer_competitor_quantity',
          item.toMap(),
          conflictAlgorithm: ConflictAlgorithm.ignore,
        );
      }
      await batch.commit(noResult: true);
      print(
          '_INSERT_ chunk ${i ~/ chunkSize + 1} done — rows ${i + chunk.length}/${list.length}');
    }
  }

  // Get all by customer code
  Future<List<CustomerCompetitorQuantityModel>>
      getAllCustomerCompetitorQuantity(String customerCode) async {
    final db = await database;
    final result = await db.query(
      'customer_competitor_quantity',
      where: 'customer_code = ?',
      whereArgs: [customerCode],
    );
    return result
        .map((e) => CustomerCompetitorQuantityModel.fromMap(e))
        .toList();
  }

  // Get all sorted: Y non-Others → N non-Others → Others last, each alpha
  Future<List<CustomerCompetitorQuantityModel>>
      getAllCustomerCompetitorQuantitySorted(String customerCode) async {
    final list = await getAllCustomerCompetitorQuantity(customerCode);
    list.sort((a, b) {
      int priorityA = _getSortPriority(a);
      int priorityB = _getSortPriority(b);
      if (priorityA != priorityB) return priorityA.compareTo(priorityB);
      return (a.competitorName ?? '')
          .toLowerCase()
          .compareTo((b.competitorName ?? '').toLowerCase());
    });
    return list;
  }

  int _getSortPriority(CustomerCompetitorQuantityModel item) {
    if ((item.competitorName ?? '').trim().toLowerCase() == 'others') return 3;
    if ((item.mandatory ?? '').toUpperCase() == 'Y') return 1;
    return 2;
  }

  // Update quantity and set flag = 1
  Future<int> updateCustomerCompetitorQuantity(
      String customerCode, String competitorQuantityId, String quantity) async {
    final db = await database;
    return await db.update(
      'customer_competitor_quantity',
      {'quantity': quantity, 'flag': 1},
      where: 'customer_code = ? AND competitor_quantity_id = ?',
      whereArgs: [customerCode, competitorQuantityId],
    );
  }

  // Get quantity for a specific customer + competitor
  Future<String> getQuantityAgainstCustomerAndCompetitor(
      String customerCode, String competitorQuantityId) async {
    final db = await database;
    final result = await db.query(
      'customer_competitor_quantity',
      where: 'customer_code = ? AND competitor_quantity_id = ?',
      whereArgs: [customerCode, competitorQuantityId],
    );
    if (result.isNotEmpty) {
      return result.first['quantity'] as String? ?? '0';
    }
    return '0';
  }

  // Check if any records exist for a customer
  Future<bool> checkAvailable(String customerCode) async {
    final db = await database;
    final result = await db.query(
      'customer_competitor_quantity',
      where: 'customer_code = ?',
      whereArgs: [customerCode],
    );
    return result.isNotEmpty;
  }

  // ─────────────────────────────────────────────
  // sbg_feedback — FUNCTIONS
  // ─────────────────────────────────────────────

  // Insert or update sbg_feedback
  Future<void> insertOrUpdateSbgFeedback(
      CustomerCompetitorQuantityModel data) async {
    final db = await database;
    final existing = await db.query(
      'sbg_feedback',
      where: 'customer_code = ? AND competitor_code = ?',
      whereArgs: [data.customerCode, data.competitorQuantityId],
    );

    final now = DateTime.now();
    final dateTime =
        '${now.year}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')} '
        '${now.hour.toString().padLeft(2, '0')}:${now.minute.toString().padLeft(2, '0')}:${now.second.toString().padLeft(2, '0')}';

    if (existing.isNotEmpty) {
      await db.update(
        'sbg_feedback',
        {
          'quantity': data.quantity,
          'date_time': dateTime,
          'flag': 1
        }, // ✅ was 0
        where: 'customer_code = ? AND competitor_code = ?',
        whereArgs: [data.customerCode, data.competitorQuantityId],
      );
    } else {
      await db.insert('sbg_feedback', {
        'customer_code': data.customerCode,
        'competitor_code': data.competitorQuantityId,
        'quantity': data.quantity,
        'date_time': dateTime,
        'flag': 1, // ✅ was 0
      });
    }
  }

  // Get all sbg_feedback records
  Future<List<SBGFeedbackModel>> getAllSbgFeedback() async {
    final db = await database;
    final result = await db.query('sbg_feedback');
    return result.map((e) => SBGFeedbackModel.fromMap(e)).toList();
  }

  // Get sbg_feedback where flag = 1
  Future<List<SBGFeedbackModel>> getAllSbgFeedbackWhereFlag1() async {
    final db = await database;
    final result = await db.query(
      'sbg_feedback',
      where: 'flag = ?',
      whereArgs: [1],
    );
    return result.map((e) => SBGFeedbackModel.fromMap(e)).toList();
  }

  // Delete all sbg_feedback
  Future<void> deleteAllSbgFeedback() async {
    final db = await database;
    await db.delete('sbg_feedback');
  }

  Future<void> close() async {
    final db = await database;
    db.close();
  }

  // ─────────────────────────────────────────────
  // customer_market_feedback_details — FUNCTIONS
  // ─────────────────────────────────────────────

  // Insert or update customer_market_feedback_details
  Future<void> insertCustomerMarketFeedbackDetails(
      CustomerMarketFeedbackDetailsDataSet data) async {
    final db = await database;
    final existing = await db.query(
      'customer_market_feedback_details',
      columns: ['customer_code'],
      where: 'customer_code = ?',
      whereArgs: [data.customerCode],
      limit: 1,
    );

    if (existing.isNotEmpty) {
      await updateDataFromCustomerMarketFeedbackDetails(data);
      return;
    }

    await db.transaction((txn) async {
      await txn.insert('customer_market_feedback_details', {
        'customer_code': data.customerCode,
        'customer_type': data.customerType,
        'customer_universe_type': data.customerUniverseType,
        'customer_state': data.customerState,
        'customer_district': data.customerDistrict,
        'customer_block_name': data.customerBlockName,
        'customer_gram_panchayat': data.customerGramPanchayat,
        'approve_status': data.approveStatus,
        'approve_by': data.approveBy,
      });
    });
  }

  Future<void> updateDataFromCustomerMarketFeedbackDetails(
      CustomerMarketFeedbackDetailsDataSet data) async {
    final db = await database;
    await db.transaction((txn) async {
      await txn.update(
        'customer_market_feedback_details',
        {
          'customer_type': data.customerType,
          'customer_universe_type': data.customerUniverseType,
          'customer_state': data.customerState,
          'customer_district': data.customerDistrict,
          'customer_block_name': data.customerBlockName,
          'customer_gram_panchayat': data.customerGramPanchayat,
          'approve_status': data.approveStatus,
          'approve_by': data.approveBy,
        },
        where: 'customer_code = ?',
        whereArgs: [data.customerCode],
      );
    });
  }

  // Get customer_market_feedback_details records
  Future<CustomerMarketFeedbackDetailsDataSet?>
      getCustomerMarketFeedbackDetails(String customerCode) async {
    final db = await database;
    final result = await db.query(
      'customer_market_feedback_details',
      where: 'customer_code = ?',
      whereArgs: [customerCode],
    );
    if (result.isEmpty) return null;
    return CustomerMarketFeedbackDetailsDataSet.fromMap(result.first);
  }

  Future<List<Map<String, dynamic>>> getCustomerGPDetails({
    required int offset,
    required int limit,
    required int filterType,
  }) async {
    final db = await database;
    String where;
    switch (filterType) {
      case 2:
        where = "COALESCE(approve_status, '') = '1'";
        break;
      case 3:
        where = "TRIM(COALESCE(customer_gram_panchayat, '')) = ''";
        break;
      default:
        where = "TRIM(COALESCE(customer_gram_panchayat, '')) != '' "
            "AND COALESCE(approve_status, '') = '0'";
    }

    return db.query(
      'customer_market_feedback_details',
      where: where,
      orderBy: 'customer_code',
      limit: limit,
      offset: offset,
    );
  }

  Future<CustomerGPDetailsCounts> getCustomerGPDetailsCounts() async {
    final db = await database;
    final rows = await db.rawQuery('''
      SELECT
        COUNT(*) AS total_count,
        SUM(CASE
          WHEN TRIM(COALESCE(customer_gram_panchayat, '')) != '' THEN 1
          ELSE 0
        END) AS updated_count,
        SUM(CASE
          WHEN TRIM(COALESCE(customer_gram_panchayat, '')) = '' THEN 1
          ELSE 0
        END) AS pending_count
      FROM customer_market_feedback_details
    ''');
    final row = rows.first;
    return CustomerGPDetailsCounts(
      total: (row['total_count'] as num?)?.toInt() ?? 0,
      updated: (row['updated_count'] as num?)?.toInt() ?? 0,
      pending: (row['pending_count'] as num?)?.toInt() ?? 0,
    );
  }

  Future<void> updateCustomerGPApprovalStatus({
    required String customerCode,
    required String approvedBy,
  }) async {
    final db = await database;
    await db.update(
      'customer_market_feedback_details',
      {'approve_status': '1', 'approve_by': approvedBy},
      where: 'customer_code = ?',
      whereArgs: [customerCode],
    );
  }

  Future<void> rejectCustomerGPDetails({
    required String customerCode,
    required String rejectedBy,
  }) async {
    final db = await database;
    await db.update(
      'customer_market_feedback_details',
      {
        'customer_state': null,
        'customer_district': null,
        'customer_block_name': null,
        'customer_gram_panchayat': null,
        'approve_status': '0',
        'approve_by': rejectedBy,
      },
      where: 'customer_code = ?',
      whereArgs: [customerCode],
    );
  }

  // ─────────────────────────────────────────────
  // market_feedback_address — FUNCTIONS
  // ─────────────────────────────────────────────

  // Insert or update market_feedback_address
  Future<void> insertMarketFeedbackAddress(
      MarketFeedbackAddressDataSet data) async {
    final db = await database;
    final existing = await db.query(
      'market_feedback_address',
      where: 'state = ? AND district=? AND block_name=? AND gram_panchayat=?',
      whereArgs: [
        data.state,
        data.district,
        data.blockName,
        data.gramPanchayat
      ],
    );
    if (existing.isEmpty) {
      await db.insert('market_feedback_address', {
        'state': data.state,
        'district': data.district,
        'block_name': data.blockName,
        'gram_panchayat': data.gramPanchayat,
      });
    }
  }

  // get state name list market_feedback_address
  Future<List<String>> getAllStateNameList() async {
    final db = await database;
    final result = await db.rawQuery(
      'SELECT DISTINCT state FROM market_feedback_address ORDER BY state',
    );
    return result
        .map((e) => e['state'] as String)
        .where((state) => state.isNotEmpty)
        .toList();
  }

  // get district name list against state market_feedback_address
  Future<List<String>> getAllDistrictNameList(String stateName) async {
    final db = await database;
    final result = await db.rawQuery(
      'SELECT DISTINCT district FROM market_feedback_address WHERE state = ? ORDER BY district',
      [stateName],
    );
    return result
        .map((e) => e['district'] as String)
        .where((district) => district.isNotEmpty)
        .toList();
  }

  // get block name list against state market_feedback_address
  Future<List<String>> getAllBlockNameList(
      String stateName, String districtName) async {
    final db = await database;
    final result = await db.rawQuery(
      'SELECT DISTINCT block_name FROM market_feedback_address WHERE state = ? AND district = ? ORDER BY block_name',
      [stateName, districtName],
    );
    return result
        .map((e) => e['block_name'] as String)
        .where((district) => district.isNotEmpty)
        .toList();
  }

  // get Gram Panchayat name list against state market_feedback_address
  Future<List<String>> getAllGramPanchayatNameList(
      String stateName, String districtName, String blockName) async {
    final db = await database;
    final result = await db.rawQuery(
      "SELECT DISTINCT gram_panchayat FROM market_feedback_address WHERE state = ? AND district = ? AND block_name = ? ORDER BY CASE WHEN gram_panchayat = 'Municipal corporation' THEN 1 ELSE 0 END, gram_panchayat;",
      [stateName, districtName, blockName],
    );
    return result
        .map((e) => e['gram_panchayat'] as String)
        .where((district) => district.isNotEmpty)
        .toList();
  }
}

class CustomerGPDetailsCounts {
  final int total;
  final int updated;
  final int pending;

  const CustomerGPDetailsCounts({
    required this.total,
    required this.updated,
    required this.pending,
  });
}
