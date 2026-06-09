import 'package:path/path.dart';
import 'package:sqflite/sqflite.dart';
import 'package:starsfa/db_setup/dataset/CustomerCompetitorQuantityModel.dart';
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
    return await openDatabase(path, version: 1, onCreate: _createDB);
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
}
