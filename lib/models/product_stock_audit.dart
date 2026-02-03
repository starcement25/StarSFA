import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'dart:developer';
import 'dart:core';
import 'package:intl/intl.dart';
import 'package:starsfa/models/location_class.dart';

class StockProductDataClass {
  String? prodCode;
  String? productGroupCode;
  String? productGroupName;
  String? productSubGroupCod;
  String? productSubGroupName;
  String? productBrandCode;
  String? productBrandName;
  String? prodDesc;
  String? blackList;
  String? acedns;
  String? clStk;
  String? uom1;
  String? uom2;
  String? conversionFactor;
  String? packSize;
  String? uom3;
  String? conversionFactorTwo;
  String? td;
  String? branchCode;
  String? verticalValue;
  String? secondaryUnit;
  String? dnsProdCode;
  String? focus;
  String? weightage;
  String? vat;
  String? addlVat;
  String? freightCost;
  String? packunit;
  String? prodSize;
  String? qty;
  String? uom4;
  String? uom5;
  String? remarks;
  String? transId;
  String? customerCode;

  StockProductDataClass(
      {this.prodCode,
      this.productGroupCode,
      this.productGroupName,
      this.productSubGroupCod,
      this.productSubGroupName,
      this.productBrandCode,
      this.productBrandName,
      this.prodDesc,
      this.blackList,
      this.acedns,
      this.clStk,
      this.uom1,
      this.uom2,
      this.conversionFactor,
      this.packSize,
      this.uom3,
      this.conversionFactorTwo,
      this.td,
      this.branchCode,
      this.verticalValue,
      this.secondaryUnit,
      this.dnsProdCode,
      this.focus,
      this.weightage,
      this.vat,
      this.addlVat,
      this.freightCost,
      this.packunit,
      this.prodSize,
      this.qty,
      this.uom4,
      this.uom5,
      this.remarks,
      this.transId,
      this.customerCode});

  Map<String, dynamic> toJson() {
    return {
      'prod_code': prodCode,
      'product_group_code': productGroupCode,
      'product_group_name': productGroupName,
      'product_sub_group_cod': productSubGroupCod,
      'product_sub_group_name': productSubGroupName,
      'product_brand_code': productBrandCode,
      'product_brand_name': productBrandName,
      'prod_desc': prodDesc,
      'black_list': blackList,
      'acedns': acedns,
      'cl_stk': clStk,
      'uom1': uom1,
      'uom2': uom2,
      'conversion_factor': conversionFactor,
      'pack_size': packSize,
      'uom3': uom3,
      'conversion_factor_two': conversionFactorTwo,
      'TD': td,
      'branch_code': branchCode,
      'vertical_value': verticalValue,
      'secondary_unit': secondaryUnit,
      'dns_prod_code': dnsProdCode,
      'focus': focus,
      'weightage': weightage,
      'vat': vat,
      'addl_vat': addlVat,
      'freight_cost': freightCost,
      'pack_unit': packunit,
      'prod_size': prodSize,
      'UOM4': uom4,
      'UOM5': uom5,
    };
  }

  factory StockProductDataClass.fromJson(Map<String, dynamic> json) {
    return StockProductDataClass(
      prodCode: json['prod_code'],
      productGroupCode: json['product_group_code'],
      productGroupName: json['product_group_name'],
      productSubGroupCod: json['product_sub_group_cod'],
      productSubGroupName: json['product_sub_group_name'],
      productBrandCode: json['product_brand_code'],
      productBrandName: json['product_brand_name'],
      prodDesc: json['prod_desc'],
      blackList: json['black_list'],
      acedns: json['acedns'],
      clStk: json['cl_stk'],
      uom1: json['uom1'],
      uom2: json['uom2'],
      conversionFactor: json['conversion_factor'],
      packSize: json['pack_size'],
      uom3: json['uom3'],
      conversionFactorTwo: json['conversion_factor_two'],
      td: json['TD'],
      branchCode: json['branch_code'],
      verticalValue: json['vertical_value'],
      secondaryUnit: json['secondary_unit'],
      dnsProdCode: json['dns_prod_code'],
      focus: json['focus'],
      weightage: json['weightage'],
      vat: json['vat'],
      addlVat: json['addl_vat'],
      freightCost: json['freight_cost'],
      packunit: json['pack_unit'],
      prodSize: json['prod_size'],
      uom4: json['UOM4'],
      uom5: json['UOM5'],
    );
  }

  // get all the records from the table
  static Future<List<StockProductDataClass>> getAllRecords(
      String customerCode, String dateVal) async {
    final localDB = await LocalDB.openMyDatabase();
    String sVal1 = customerCode;
    String branch = '';

    List<Map<String, dynamic>> result = await localDB.query(
      'customer_master',
      where: 'customer_code = ?',
      whereArgs: [sVal1],
    );
    if (result.isNotEmpty) {
      branch = result.first['branch_code'] as String;
    }

    String sql =
        "SELECT  DISTINCT PM.*,0 FROM product_master PM WHERE PM.acedns = 'Y' AND PM.black_list = 'N'  AND PM.branch_code='$branch'";

    //log("sql- $sql");
    final List<Map<String, dynamic>> records1 = await localDB.rawQuery(sql);

    //print(records1);
    return records1
        .map((json) => StockProductDataClass.fromJson(json))
        .toList();
  }

  Future<List<StockProductDataClass>> getStockAuditProductList() async {
    final localDB = await LocalDB.openMyDatabase();

    final List<Map<String, dynamic>> records1 = await localDB
        .rawQuery('select emp_code,emp_name from  mis_details_emp_datewise');
    //print(records1);
    return records1
        .map((json) => StockProductDataClass.fromJson(json))
        .toList();
  }

  Future<String> getCustomerBranchByCode(String code) async {
    final localDB = await LocalDB.openMyDatabase();
    List<Map<String, dynamic>> result = await localDB.query(
      'customer_master',
      where: 'customer_code = ?',
      whereArgs: [code],
    );
    if (result.isNotEmpty) {
      return result.first['branch_code'] as String;
    }
    return '';
  }

  // upload attendance to server
  static Future<bool> updateStockAuditServer(String id) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final localDB = await LocalDB.openMyDatabase();
    final user = await UserLoginClass.getLocalUser();
    // get location by id
    final LocationClass? location = await LocationClass.getLocationById(id);
    if (location == null) {
      return false;
    }
    // get attendance by id
    final StockProductDataClass stockClass = await getDataransId(id);

    String xmlData = '''
      <?xml version="1.0" encoding="UTF-8"?>
      <root>
        <stock_audit>
          <location>
            <emp_code><![CDATA[${location.empCode}]]></emp_code>
            <trans_id><![CDATA[${location.transId}]]></trans_id>
            <latt><![CDATA[${location.latt}]]></latt>
            <longi><![CDATA[${location.longi}]]></longi>
            <date><![CDATA[${location.date}]]></date>
            <TA_DA_MODE><![CDATA[]]></TA_DA_MODE>
          </location>
          <stock_audit_details>
          <transaction_id><![CDATA[${stockClass.transId}]]></transaction_id>
          <customer_code><![CDATA[${stockClass.customerCode}]]></customer_code>
          <product_code><![CDATA[${stockClass.prodCode}]]></product_code>
          <prod_mrp><![CDATA[]]></prod_mrp>
          <prod_details><![CDATA[]]></prod_details>
          <remarks><![CDATA[${stockClass.remarks}]]></remarks>
          <HINT_REMARKS><![CDATA[Others]]></HINT_REMARKS>
          <mfd_date><![CDATA[]]></mfd_date>
          <UOM><![CDATA[${stockClass.uom1}]]></UOM>
          <quantity><![CDATA[${stockClass.qty}]]></quantity><
          weightage><![CDATA[]]></weightage>
          </stock_audit_details>
        </stock_audit>
      </root>
    ''';
    // remove all new lines
    xmlData = xmlData.replaceAll('\n', '');
    // remove all tabs
    xmlData = xmlData.replaceAll('\t', '');
    // remove all big spaces
    xmlData = xmlData.replaceAll('  ', '');
    // http://sfa.starcement.co.in/operationdb-stock-audit-6.0.5.php?nick_name=STAR&emp_code=E0555&last_update_time=2024-08-22€03:24:07
    final String lastUpdateTime =
        DateFormat('yyyy-MM-dd€HH:mm:ss').format(DateTime.now());
    final Uri url = Uri.parse(
        '${AppWebService.stockAuditExportURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=$lastUpdateTime');
    final response = await http.post(url, body: xmlData);
    log(' Response: ${response.body}');
    log(' Status Code: ${response.statusCode}');
    log(' URL: $url');
    log(' XML Data: $xmlData');
    if (response.statusCode == 200 && response.body == '1') {
      // update flag
      await localDB.update('location', {'flag': '1'},
          where: 'trans_id = ?', whereArgs: [id]);
      return true;
    } else {
      return false;
    }
  }

  static Future<StockProductDataClass> getDataransId(String transId) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> sd = await localDB.query(
      'stock_audit',
      where: 'transaction_id like ?',
      whereArgs: ['%$transId%'],
    );
    return StockProductDataClass(
      customerCode: sd[0]['customer_code'].toString(),
      transId: sd[0]['transaction_id'].toString(),
      qty: sd[0]['quantity'].toString(),
      prodCode: sd[0]['product_code'].toString(),
      uom1: sd[0]['UOM'].toString(),
      remarks: sd[0]['remarks'].toString(),
    );
  }
}
