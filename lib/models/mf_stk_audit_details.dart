// mf_stk_audit_details

import 'package:starsfa/models/local_db.dart';
import 'package:xml/xml.dart';

class MfStkAuditDetails {
  final String mfStkAuditId;
  final String competitorName;
  final String qtyMt;
  final String schemeDiscount;

  MfStkAuditDetails({
    required this.mfStkAuditId,
    required this.competitorName,
    required this.qtyMt,
    required this.schemeDiscount,
  });

  factory MfStkAuditDetails.fromJson(Map<String, dynamic> json) {
    return MfStkAuditDetails(
      mfStkAuditId: json['mf_stk_audit_id'],
      competitorName: json['competitor_name'],
      qtyMt: json['qty_mt'],
      schemeDiscount: json['scheme_discount'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'mf_stk_audit_id': mfStkAuditId,
      'competitor_name': competitorName,
      'qty_mt': qtyMt,
      'scheme_discount': schemeDiscount,
    };
  }

  // Convert to XML format
  XmlElement toXml() {
    return XmlElement(
      XmlName('MF_STKAUDIT_DETAILS'),
      [],
      [
        XmlElement(XmlName('MF_STKAUDIT_ID'), [],
            [XmlText('<![CDATA[$mfStkAuditId]]>')]),
        XmlElement(XmlName('COMPETITOR_NAME'), [],
            [XmlText('<![CDATA[$competitorName]]>')]),
        XmlElement(XmlName('QTY_MT'), [], [XmlText('<![CDATA[$qtyMt]]>')]),
        XmlElement(XmlName('SCHEME_DISCOUNT'), [],
            [XmlText('<![CDATA[$schemeDiscount]]>')]),
      ],
    );
  }

  // get market_feedback_details by id
  static Future<List<MfStkAuditDetails>?> getMarketFeedbackDetailsById(
      String id) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> marketFeedbackDetails = await localDB
        .query('mf_stk_audit_details',
            where: 'mf_stk_audit_id = ?', whereArgs: [id]);
    if (marketFeedbackDetails.isEmpty) {
      return null;
    } else {
      return List.generate(marketFeedbackDetails.length, (index) {
        return MfStkAuditDetails.fromJson(marketFeedbackDetails[index]);
      });
    }
  }

  // save market_feedback_details to local database
  static Future<bool> saveMfStkAuditDetails(
      MfStkAuditDetails mfStkAuditDetails) async {
    final localDB = await LocalDB.openMyDatabase();
    await localDB.insert('mf_stk_audit_details', mfStkAuditDetails.toJson());
    return true;
  }
}
