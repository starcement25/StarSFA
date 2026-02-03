// market_feedback

import 'package:sqflite/sqflite.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:xml/xml.dart';

class MarketFeedback {
  final String? marketFeedbackId;
  final String? competitorName;
  String? ptd;
  String? ptr;
  final String? ptc;
  final String? customerCode;
  String? billingExFor;
  String? wspExFor;
  final String? rspExFor;
  final String? nodExFor;
  final String? routeCode;
  final String? productGroup;
  final String? pv;

  MarketFeedback({
    required this.marketFeedbackId,
    required this.competitorName,
    required this.ptd,
    required this.ptr,
    required this.ptc,
    required this.customerCode,
    required this.billingExFor,
    required this.wspExFor,
    required this.routeCode,
    required this.productGroup,
    required this.pv,
    required this.rspExFor,
    required this.nodExFor,
  });

  factory MarketFeedback.fromJson(Map<String, dynamic> json) {
    return MarketFeedback(
      marketFeedbackId: json['market_feedback_id'],
      competitorName: json['competitor_name'],
      ptd: json['PTD'],
      ptr: json['PTR'],
      ptc: json['PTC'],
      customerCode: json['customer_code'],
      billingExFor: json['billing_ex_for'],
      wspExFor: json['wsp_ex_for'],
      routeCode: json['route_code'],
      productGroup: json['product_group'],
      pv: json['PV'],
      rspExFor: json['rsp_ex_for'],
      nodExFor: json['nod_ex_for'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'market_feedback_id': marketFeedbackId,
      'competitor_name': competitorName,
      'ptd': ptd,
      'ptr': ptr,
      'ptc': ptc,
      'customer_code': customerCode,
      'billing_ex_for': billingExFor,
      'wsp_ex_for': wspExFor,
      'route_code': routeCode,
      'product_group': productGroup,
      'pv': pv,
    };
  }

  // Convert to XML format
  XmlElement toXml() {
    if (billingExFor == 'FOR') {
      billingExFor = ptd;
      ptd = '0';
    } else {
      billingExFor = '0';
    }
    if (wspExFor == 'FOR') {
      wspExFor = ptr;
      ptr = '0';
    } else {
      wspExFor = '0';
    }
    return XmlElement(
      XmlName('MARKET_FEEDBACK_DATA'),
      [],
      [
        XmlElement(XmlName('MARKET_FEEDBACK_ID'), [],
            [XmlText('<![CDATA[$marketFeedbackId]]>')]),
        XmlElement(
            XmlName('route_code'), [], [XmlText('<![CDATA[$routeCode]]>')]),
        XmlElement(XmlName('customer_code'), [],
            [XmlText('<![CDATA[$customerCode]]>')]),
        XmlElement(XmlName('product_group'), [], [
          XmlText('<![CDATA[${productGroup == 'null' ? '' : productGroup}]]>')
        ]),
        XmlElement(XmlName('competitor_name'), [],
            [XmlText('<![CDATA[$competitorName]]>')]),
        XmlElement(XmlName('PTD'), [], [XmlText('<![CDATA[$ptd]]>')]),
        XmlElement(XmlName('PTR'), [], [XmlText('<![CDATA[$ptr]]>')]),
        XmlElement(XmlName('PTC'), [], [XmlText('<![CDATA[$ptc]]>')]),
        XmlElement(XmlName('PV'), [],
            [XmlText('<![CDATA[${pv == 'null' ? '' : pv}]]>')]),
        XmlElement(XmlName('BILLING_EX_FOR'), [],
            [XmlText('<![CDATA[$billingExFor]]>')]),
        XmlElement(
            XmlName('WSP_EX_FOR'), [], [XmlText('<![CDATA[$wspExFor]]>')]),
        XmlElement(XmlName('RSP_EX_FOR'), [],
            [XmlText('<![CDATA[${rspExFor == 'null' ? '' : rspExFor}]]>')]),
        XmlElement(XmlName('NOD_EX_FOR'), [],
            [XmlText('<![CDATA[${nodExFor == 'null' ? '' : nodExFor}]]>')]),
      ],
    );
  }

  // get market_feedback by id
  static Future<List<MarketFeedback>?> getMarketFeedbackById(String id) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> marketFeedback = await localDB.query(
        'market_feedback',
        where: 'market_feedback_id = ?',
        whereArgs: [id]);
    if (marketFeedback.isEmpty) {
      return null;
    } else {
      return marketFeedback.map((e) => MarketFeedback.fromJson(e)).toList();
    }
  }

  // save market_feedback to local database
  Future<bool> saveMarketFeedback() async {
    final localDB = await LocalDB.openMyDatabase();
    final batch = localDB.batch();
    batch.insert('market_feedback', toJson(),
        conflictAlgorithm: ConflictAlgorithm.replace);
    await batch.commit(noResult: true);
    return true;
  }
}
