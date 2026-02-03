// mf_stk_audit_header

import 'package:starsfa/models/local_db.dart';
import 'package:xml/xml.dart';

class MfStkAuditHeader {
  final String mfStkAuditId;
  final String customerCode;
  final String? customerName;
  final String remarks;
  final String image;

  MfStkAuditHeader({
    required this.mfStkAuditId,
    required this.customerCode,
    this.customerName,
    required this.remarks,
    required this.image,
  });

  factory MfStkAuditHeader.fromJson(Map<String, dynamic> json) {
    return MfStkAuditHeader(
      mfStkAuditId: json['mf_stk_audit_id'],
      customerCode: json['customer_code'],
      customerName: json['customer_name'],
      remarks: json['remarks'],
      image: json['image'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'mf_stk_audit_id': mfStkAuditId,
      'customer_code': customerCode,
      'remarks': remarks,
      'image': image,
    };
  }

  // Convert to XML format
  XmlElement toXml() {
    return XmlElement(
      XmlName('MF_STKAUDIT_HEADER'),
      [],
      [
        XmlElement(XmlName('MF_STKAUDIT_ID'), [],
            [XmlText('<![CDATA[$mfStkAuditId]]>')]),
        XmlElement(XmlName('customer_code'), [],
            [XmlText('<![CDATA[$customerCode]]>')]),
        XmlElement(XmlName('image_name'), [], [XmlText('<![CDATA[$image]]>')]),
        XmlElement(XmlName('remarks'), [], [XmlText('<![CDATA[$remarks]]>')]),
      ],
    );
  }

  // get mf_stk_audit_header by id
  static Future<MfStkAuditHeader?> getMfStkAuditHeaderById(String id) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> mfStkAuditHeader = await localDB.query(
        'mf_stk_audit_header',
        where: 'mf_stk_audit_id = ?',
        whereArgs: [id]);
    if (mfStkAuditHeader.isEmpty) {
      return null;
    } else {
      return MfStkAuditHeader(
        mfStkAuditId: mfStkAuditHeader[0]['mf_stk_audit_id'].toString(),
        customerCode: mfStkAuditHeader[0]['customer_code'].toString(),
        remarks: mfStkAuditHeader[0]['remarks'].toString(),
        image: mfStkAuditHeader[0]['image'].toString(),
      );
    }
  }

  // save mf_stk_audit_header to local database
  static Future<bool> saveMfStkAuditHeader(
      MfStkAuditHeader mfStkAuditHeader) async {
    final localDB = await LocalDB.openMyDatabase();
    final batch = localDB.batch();
    batch.insert('mf_stk_audit_header', mfStkAuditHeader.toJson());
    await batch.commit(noResult: true);
    return true;
  }
}
