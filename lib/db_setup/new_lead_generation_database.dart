import 'package:path/path.dart';
import 'package:sqflite/sqflite.dart';

class LeadListMasterTableDataSet {
  String? leadGenerationId;
  String? empCode;
  String? latitude;
  String? longitude;
  String? leadType;
  String? partyName;
  String? branch;
  String? district;
  String? state;
  String? qtyReq;
  String? productPackaging;
  String? expRatePerBag;
  String? contactPersonName;
  String? designation;
  String? contactNumber;
  String? mailId;
  String? mode;
  String? quotation;
  String? po;
  String? status;
  String? remarks;
  String? assignedTo;
  String? selfOther;
  String? accBlockIsRequired;
  String? categoryTypeConstruction;
  String? nextVisitDate;
  String? leadStatus;
  String? currentBrandUsed;
  String? currentPrice;
  String? currentPriceCompetitor;
  String? rTiming;
  String? actionOnLead;
  String? approvedPrice;
  String? salesOrg;
  String? division;
  String? distributionChannel;
  String? documentType;
  String? customerReferenceNo;
  String? customerReferenceDate;
  String? validToDate;
  String? materialNumber;
  String? soldToParty;
  String? shipToParty;
  String? poMethod;
  String? shareLeadSiteDetailsPic;
  String? typeLead;
  String? leadRemarks;
  String? leadAction;
  String? creditTerms;
  String? monthQty;
  String? quotationProvided;
  String? quotationProvidedDate;
  String? misSubmissionDate;
  String? hosSubmissionDate;
  String? downloadTime;
  String? destination;
  String? companyConstraint;
  String? reason;
  String? nov;
  String? incoterms;
  String? servingLocation;
  String? quotedPrice;
  String? tpc;
  String? payment;
  String? lastPrice;
  String? prevLastPrice;
  String? leadQuotationStatus;
  String? lostOrderReason;
  String? quotationNumber;
  String? quotationPdf;
  String? poNumber;
  String? poDate;
  String? poImage;
  String? poRevertNote;
  String? poRevertLevel;
  String? poFowardNote;
  String? contractNumber;
  String? salesOrderNumber;

  LeadListMasterTableDataSet({
    this.leadGenerationId,
    this.empCode,
    this.latitude,
    this.longitude,
    this.leadType,
    this.partyName,
    this.branch,
    this.district,
    this.state,
    this.qtyReq,
    this.productPackaging,
    this.expRatePerBag,
    this.contactPersonName,
    this.designation,
    this.contactNumber,
    this.mailId,
    this.mode,
    this.quotation,
    this.po,
    this.status,
    this.remarks,
    this.assignedTo,
    this.selfOther,
    this.accBlockIsRequired,
    this.categoryTypeConstruction,
    this.nextVisitDate,
    this.leadStatus,
    this.currentBrandUsed,
    this.currentPrice,
    this.currentPriceCompetitor,
    this.rTiming,
    this.actionOnLead,
    this.approvedPrice,
    this.salesOrg,
    this.division,
    this.distributionChannel,
    this.documentType,
    this.customerReferenceNo,
    this.customerReferenceDate,
    this.validToDate,
    this.materialNumber,
    this.soldToParty,
    this.shipToParty,
    this.poMethod,
    this.shareLeadSiteDetailsPic,
    this.typeLead,
    this.leadRemarks,
    this.leadAction,
    this.creditTerms,
    this.monthQty,
    this.quotationProvided,
    this.quotationProvidedDate,
    this.misSubmissionDate,
    this.hosSubmissionDate,
    this.downloadTime,
    this.destination,
    this.companyConstraint,
    this.reason,
    this.nov,
    this.incoterms,
    this.servingLocation,
    this.quotedPrice,
    this.tpc,
    this.payment,
    this.lastPrice,
    this.prevLastPrice,
    this.leadQuotationStatus,
    this.lostOrderReason,
    this.quotationNumber,
    this.quotationPdf,
    this.poNumber,
    this.poDate,
    this.poImage,
    this.poRevertNote,
    this.poRevertLevel,
    this.poFowardNote,
    this.contractNumber,
    this.salesOrderNumber,
  });

  Map<String, dynamic> toMap() {
    return {
      'lead_generation_id': leadGenerationId,
      'emp_code': empCode,
      'latitude': latitude,
      'longitude': longitude,
      'lead_type': leadType,
      'party_name': partyName,
      'branch': branch,
      'district': district,
      'state': state,
      'qty_req': qtyReq,
      'product_packaging': productPackaging,
      'exp_rate_per_bag': expRatePerBag,
      'contact_person_name': contactPersonName,
      'designation': designation,
      'contact_number': contactNumber,
      'mail_id': mailId,
      'mode': mode,
      'quotation': quotation,
      'PO': po,
      'status': status,
      'remarks': remarks,
      'assigned_to': assignedTo,
      'self_other': selfOther,
      'acc_block_is_required': accBlockIsRequired,
      'category_type_construction': categoryTypeConstruction,
      'next_visit_date': nextVisitDate,
      'lead_status': leadStatus,
      'current_brand_used': currentBrandUsed,
      'current_price': currentPrice,
      'current_price_competitor': currentPriceCompetitor,
      'r_timing': rTiming,
      'action_on_lead': actionOnLead,
      'approved_price': approvedPrice,
      'sales_org': salesOrg,
      'division': division,
      'distribution_channel': distributionChannel,
      'document_type': documentType,
      'customer_reference_no': customerReferenceNo,
      'customer_reference_date': customerReferenceDate,
      'valid_to_date': validToDate,
      'material_number': materialNumber,
      'sold_to_party': soldToParty,
      'ship_to_party': shipToParty,
      'PO_method': poMethod,
      'share_lead_site_details_pic': shareLeadSiteDetailsPic,
      'type_lead': typeLead,
      'lead_remarks': leadRemarks,
      'lead_action': leadAction,
      'credit_terms': creditTerms,
      'month_qty': monthQty,
      'quotation_provided': quotationProvided,
      'quotation_provided_date': quotationProvidedDate,
      'mis_submission_date': misSubmissionDate,
      'hos_submission_date': hosSubmissionDate,
      'download_time': downloadTime,
      'destination': destination,
      'company_constraint': companyConstraint,
      'reason': reason,
      'nov': nov,
      'incoterms': incoterms,
      'serving_location': servingLocation,
      'quoted_price': quotedPrice,
      'tpc': tpc,
      'payment': payment,
      'last_price': lastPrice,
      'prev_last_price': prevLastPrice,
      'lead_quotation_status': leadQuotationStatus,
      'lost_order_reason': lostOrderReason,
      'quotation_number': quotationNumber,
      'quotation_pdf': quotationPdf,
      'po_number': poNumber,
      'po_date': poDate,
      'po_image': poImage,
      'po_revert_note': poRevertNote,
      'po_revert_level': poRevertLevel,
      'po_foward_note': poFowardNote,
      'contract_number': contractNumber,
      'sales_order_number': salesOrderNumber,
    };
  }

  factory LeadListMasterTableDataSet.fromMap(Map<String, dynamic> map) {
    return LeadListMasterTableDataSet(
      leadGenerationId: map['lead_generation_id'],
      empCode: map['emp_code'],
      latitude: map['latitude'],
      longitude: map['longitude'],
      leadType: map['lead_type'],
      partyName: map['party_name'],
      branch: map['branch'],
      district: map['district'],
      state: map['state'],
      qtyReq: map['qty_req'],
      productPackaging: map['product_packaging'],
      expRatePerBag: map['exp_rate_per_bag'],
      contactPersonName: map['contact_person_name'],
      designation: map['designation'],
      contactNumber: map['contact_number'],
      mailId: map['mail_id'],
      mode: map['mode'],
      quotation: map['quotation'],
      po: map['PO'],
      status: map['status'],
      remarks: map['remarks'],
      assignedTo: map['assigned_to'],
      selfOther: map['self_other'],
      accBlockIsRequired: map['acc_block_is_required'],
      categoryTypeConstruction: map['category_type_construction'],
      nextVisitDate: map['next_visit_date'],
      leadStatus: map['lead_status'],
      currentBrandUsed: map['current_brand_used'],
      currentPrice: map['current_price'],
      currentPriceCompetitor: map['current_price_competitor'],
      rTiming: map['r_timing'],
      actionOnLead: map['action_on_lead'],
      approvedPrice: map['approved_price'],
      salesOrg: map['sales_org'],
      division: map['division'],
      distributionChannel: map['distribution_channel'],
      documentType: map['document_type'],
      customerReferenceNo: map['customer_reference_no'],
      customerReferenceDate: map['customer_reference_date'],
      validToDate: map['valid_to_date'],
      materialNumber: map['material_number'],
      soldToParty: map['sold_to_party'],
      shipToParty: map['ship_to_party'],
      poMethod: map['PO_method'],
      shareLeadSiteDetailsPic: map['share_lead_site_details_pic'],
      typeLead: map['type_lead'],
      leadRemarks: map['lead_remarks'],
      leadAction: map['lead_action'],
      creditTerms: map['credit_terms'],
      monthQty: map['month_qty'],
      quotationProvided: map['quotation_provided'],
      quotationProvidedDate: map['quotation_provided_date'],
      misSubmissionDate: map['mis_submission_date'],
      hosSubmissionDate: map['hos_submission_date'],
      downloadTime: map['download_time'],
      destination: map['destination'],
      companyConstraint: map['company_constraint'],
      reason: map['reason'],
      nov: map['nov'],
      incoterms: map['incoterms'],
      servingLocation: map['serving_location'],
      quotedPrice: map['quoted_price'],
      tpc: map['tpc'],
      payment: map['payment'],
      lastPrice: map['last_price'],
      prevLastPrice: map['prev_last_price'],
      leadQuotationStatus: map['lead_quotation_status'],
      lostOrderReason: map['lost_order_reason'],
      quotationNumber: map['quotation_number'],
      quotationPdf: map['quotation_pdf'],
      poNumber: map['po_number'],
      poDate: map['po_date'],
      poImage: map['po_image'],
      poRevertNote: map['po_revert_note'],
      poRevertLevel: map['po_revert_level'],
      poFowardNote: map['po_foward_note'],
      contractNumber: map['contract_number'],
      salesOrderNumber: map['sales_order_number'],
    );
  }
}

class CustomerMasterTableDataSet {
  String? custCode;
  String? custName;
  String? phoneNo;
  String? district;
  String? state;
  String? address;
  String? custType;

  CustomerMasterTableDataSet({
    this.custCode,
    this.custName,
    this.phoneNo,
    this.district,
    this.state,
    this.address,
    this.custType,
  });

  Map<String, dynamic> toMap() {
    return {
      'cust_code': custCode,
      'cust_name': custName,
      'phone_no': phoneNo,
      'district': district,
      'state': state,
      'address': address,
      'cust_type': custType,
    };
  }

  factory CustomerMasterTableDataSet.fromMap(Map<String, dynamic> map) {
    return CustomerMasterTableDataSet(
      custCode: map['cust_code'],
      custName: map['cust_name'],
      phoneNo: map['phone_no'],
      district: map['district'],
      state: map['state'],
      address: map['address'],
      custType: map['cust_type'],
    );
  }
}

class NewLeadGenerationDatabase {
  static final NewLeadGenerationDatabase _instance =
      NewLeadGenerationDatabase._internal();
  static Database? _database;

  static const String _dbName = 'NewDatabaseStoreForLead.db';
  static const int _dbVersion = 1;

  factory NewLeadGenerationDatabase() => _instance;
  NewLeadGenerationDatabase._internal();

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDatabase();
    return _database!;
  }

  Future<Database> _initDatabase() async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, _dbName);
    return await openDatabase(
      path,
      version: _dbVersion,
      onCreate: (db, version) async {
        await _createTables(db);
      },
    );
  }

  Future<void> _createTables(Database db) async {
    await db.execute('''
      CREATE TABLE IF NOT EXISTS lead_list_master_table (
        lead_generation_id TEXT PRIMARY KEY,
        emp_code TEXT, latitude TEXT, longitude TEXT, lead_type TEXT,
        party_name TEXT, branch TEXT, district TEXT, state TEXT, qty_req TEXT,
        product_packaging TEXT, exp_rate_per_bag TEXT, contact_person_name TEXT,
        designation TEXT, contact_number TEXT, mail_id TEXT, mode TEXT,
        quotation TEXT, PO TEXT, status TEXT, remarks TEXT, assigned_to TEXT,
        self_other TEXT, acc_block_is_required TEXT, category_type_construction TEXT,
        next_visit_date TEXT, lead_status TEXT, current_brand_used TEXT,
        current_price TEXT, current_price_competitor TEXT, r_timing TEXT,
        action_on_lead TEXT, approved_price TEXT, sales_org TEXT, division TEXT,
        distribution_channel TEXT, document_type TEXT, customer_reference_no TEXT,
        customer_reference_date TEXT, valid_to_date TEXT, material_number TEXT,
        sold_to_party TEXT, ship_to_party TEXT, PO_method TEXT,
        share_lead_site_details_pic TEXT, type_lead TEXT, lead_remarks TEXT,
        lead_action TEXT, credit_terms TEXT, month_qty TEXT,
        quotation_provided TEXT, quotation_provided_date TEXT,
        mis_submission_date TEXT, hos_submission_date TEXT, download_time TEXT,
        destination TEXT, company_constraint TEXT, reason TEXT, nov TEXT,
        incoterms TEXT, serving_location TEXT, quoted_price TEXT, tpc TEXT,
        payment TEXT, last_price TEXT, prev_last_price TEXT,
        lead_quotation_status TEXT,
        lost_order_reason TEXT 
      )
    ''');

    await db.execute('''
      CREATE TABLE IF NOT EXISTS customer_master_table (
        cust_code TEXT, cust_name TEXT, phone_no TEXT,
        district TEXT, state TEXT, address TEXT, cust_type TEXT
      )
    ''');
  }

  // ── Lead List Master Table ──────────────────────────────────────────────────
  Future<void> _ensureNewColumn(Database db) async {
    final columns =
        await db.rawQuery('PRAGMA table_info(lead_list_master_table)');
    final exists = columns.any((col) => col['name'] == 'lost_order_reason');
    if (!exists) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN lost_order_reason TEXT NULL',
      );
    }
    final existsQuotationNumber =
        columns.any((col) => col['name'] == 'quotation_number');
    if (!existsQuotationNumber) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN quotation_number TEXT NULL',
      );
    }
    final existsQuotationPdf =
        columns.any((col) => col['name'] == 'quotation_pdf');
    if (!existsQuotationPdf) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN quotation_pdf TEXT NULL',
      );
    }
    final existsPoNumber = columns.any((col) => col['name'] == 'po_number');
    if (!existsPoNumber) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN po_number TEXT NULL',
      );
    }
    final existsPoDate = columns.any((col) => col['name'] == 'po_date');
    if (!existsPoDate) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN po_date TEXT NULL',
      );
    }
    final existsPoImage = columns.any((col) => col['name'] == 'po_image');
    if (!existsPoImage) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN po_image TEXT NULL',
      );
    }
    final existsPoRevertNote =
        columns.any((col) => col['name'] == 'po_revert_note');
    if (!existsPoRevertNote) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN po_revert_note TEXT NULL',
      );
    }
    final existsPoRevertLevel =
        columns.any((col) => col['name'] == 'po_revert_level');
    if (!existsPoRevertLevel) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN po_revert_level TEXT NULL',
      );
    }
    final existsPoFowardNote =
        columns.any((col) => col['name'] == 'po_foward_note');
    if (!existsPoFowardNote) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN po_foward_note TEXT NULL',
      );
    }
    final existsContractNumber =
        columns.any((col) => col['name'] == 'contract_number');
    if (!existsContractNumber) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN contract_number TEXT NULL',
      );
    }
    final existsSalesOrderNumber =
        columns.any((col) => col['name'] == 'sales_order_number');
    if (!existsSalesOrderNumber) {
      await db.execute(
        'ALTER TABLE lead_list_master_table ADD COLUMN sales_order_number TEXT NULL',
      );
    }
  }

  Future<void> insertLeadListMasterTableData(
      LeadListMasterTableDataSet data) async {
    final db = await database;
    try {
      await _ensureNewColumn(db);
      await db.delete('lead_list_master_table',
          where: 'lead_generation_id = ?', whereArgs: [data.leadGenerationId]);
      await db.insert(
        'lead_list_master_table',
        data.toMap(),
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
    } catch (e) {
      print('insertLeadListMasterTableData error: $e');
    }
  }

  Future<void> updateLeadListMasterTableData(
      LeadListMasterTableDataSet data) async {
    final db = await database;
    final map = data.toMap()..remove('lead_generation_id');
    try {
      await db.update(
        'lead_list_master_table',
        map,
        where: 'lead_generation_id = ?',
        whereArgs: [data.leadGenerationId],
      );
    } catch (e) {
      print('updateLeadListMasterTableData error: $e');
    }
  }

  Future<List<LeadListMasterTableDataSet>> getAllLeadListMasterTableData(
      int value) async {
    final db = await database;
    try {
      final result = await db.rawQuery(
        'SELECT * FROM lead_list_master_table WHERE CAST(lead_quotation_status AS INTEGER) >= ?',
        [value],
      );
      return result.map((e) => LeadListMasterTableDataSet.fromMap(e)).toList();
    } catch (e) {
      print('getAllLeadListMasterTableData error: $e');
      return [];
    }
  }

  Future<LeadListMasterTableDataSet> getLeadGenerationDetails(
      String value) async {
    final db = await database;
    try {
      final result = await db.query(
        'lead_list_master_table',
        where: 'lead_generation_id = ?',
        whereArgs: [value],
        limit: 1,
      );
      if (result.isNotEmpty)
        return LeadListMasterTableDataSet.fromMap(result.first);
    } catch (e) {
      print('getLeadGenerationDetails error: $e');
    }
    return LeadListMasterTableDataSet();
  }

  Future<int> getCountLeadListMasterTableData(int value, bool checker) async {
    final db = await database;
    try {
      final operator = checker ? '>=' : '=';
      final result = await db.rawQuery(
        'SELECT COUNT(*) FROM lead_list_master_table WHERE CAST(lead_quotation_status AS INTEGER) $operator ?',
        [value],
      );
      return Sqflite.firstIntValue(result) ?? 0;
    } catch (e) {
      print('getCountLeadListMasterTableData error: $e');
      return 0;
    }
  }

  Future<String> getTotalQtyListMasterTableData(int value, bool checker) async {
    final db = await database;
    try {
      final operator = checker ? '>=' : '=';
      final result = await db.rawQuery(
        'SELECT SUM(CAST(qty_req AS REAL)) FROM lead_list_master_table WHERE CAST(lead_quotation_status AS INTEGER) $operator ?',
        [value],
      );
      final total = result.first.values.first;
      final qty = total != null ? (total as num).toDouble() : 0.0;
      return formatToKLCrRounded(qty);
    } catch (e) {
      print('getTotalQtyListMasterTableData error: $e');
      return '0';
    }
  }

  String formatToKLCrRounded(double value) {
    if (value >= 10000000) return '~${(value / 10000000).toStringAsFixed(2)}Cr';
    if (value >= 100000) return '~${(value / 100000).toStringAsFixed(2)}L';
    if (value >= 1000) return '~${(value / 1000).toStringAsFixed(2)}K';
    return value.toStringAsFixed(2);
  }

  // ── Customer Master Table ───────────────────────────────────────────────────

  Future<void> insertCustomerMasterTable(
      CustomerMasterTableDataSet data) async {
    final db = await database;
    final existing = await db.query(
      'customer_master_table',
      where: 'cust_code = ? AND cust_type = ?',
      whereArgs: [data.custCode, data.custType],
      limit: 1,
    );
    if (existing.isNotEmpty) return;

    try {
      await db.insert(
        'customer_master_table',
        data.toMap(),
        conflictAlgorithm: ConflictAlgorithm.ignore,
      );
    } catch (e) {
      print('insertCustomerMasterTable error: $e');
    }
  }

  Future<List<CustomerMasterTableDataSet>> getAllCustomerMasterTable(
      String value) async {
    final db = await database;
    try {
      final result = await db.query(
        'customer_master_table',
        where: 'cust_type = ?',
        whereArgs: [value],
      );
      return result.map((e) => CustomerMasterTableDataSet.fromMap(e)).toList();
    } catch (e) {
      print('getAllCustomerMasterTable error: $e');
      return [];
    }
  }

  Future<CustomerMasterTableDataSet> getCustomerDetails(String value) async {
    final db = await database;
    try {
      final result = await db.query(
        'customer_master_table',
        where: 'cust_code = ?',
        whereArgs: [value],
        limit: 1,
      );
      if (result.isNotEmpty)
        return CustomerMasterTableDataSet.fromMap(result.first);
    } catch (e) {
      print('getCustomerDetails error: $e');
    }
    return CustomerMasterTableDataSet();
  }

  Future<void> closeDatabase() async {
    final db = _database;
    if (db != null && db.isOpen) {
      await db.close();
      _database = null;
    }
  }
}
