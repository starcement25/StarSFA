import 'package:starsfa/models/local_db.dart';
import 'package:xml/xml.dart';

class LocationClass {
  final String empCode;
  final String transId;
  final String latt;
  final String longi;
  final String date;
  final String? purposeOfVisit;
  String flag;

  LocationClass({
    required this.empCode,
    required this.transId,
    required this.latt,
    required this.longi,
    required this.date,
    this.purposeOfVisit,
    this.flag = '0',
  });

  toJson() {
    return {
      'emp_code': empCode,
      'trans_id': transId,
      'latt': latt,
      'longi': longi,
      'date': date,
      'purpose_of_visit': purposeOfVisit,
      'flag': flag,
    };
  }

  // Convert to XML format
  XmlElement toXml() {
    return XmlElement(
      XmlName('location'),
      [],
      [
        XmlElement(XmlName('emp_code'), [], [XmlText('<![CDATA[$empCode]]>')]),
        XmlElement(XmlName('trans_id'), [], [XmlText('<![CDATA[$transId]]>')]),
        XmlElement(XmlName('latt'), [], [XmlText('<![CDATA[$latt]]>')]),
        XmlElement(XmlName('longi'), [], [XmlText('<![CDATA[$longi]]>')]),
        XmlElement(XmlName('date'), [], [XmlText('<![CDATA[$date]]>')]),
        XmlElement(XmlName('purpose_of_visit'), [],
            [XmlText('<![CDATA[$purposeOfVisit]]>')]),
      ],
    );
  }

  // get location by id
  static Future<LocationClass?> getLocationById(String id) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> location =
        await localDB.query('location', where: 'trans_id = ?', whereArgs: [id]);
    if (location.isEmpty) {
      return null;
    } else {
      return LocationClass(
        empCode: location[0]['emp_code'].toString(),
        transId: location[0]['trans_id'].toString(),
        latt: location[0]['latt'].toString(),
        longi: location[0]['longi'].toString(),
        date: location[0]['date'].toString(),
        flag: location[0]['flag'].toString(),
        purposeOfVisit: location[0]['purpose_of_visit'].toString(),
      );
    }
  }

  static Future<bool> saveLocation(LocationClass location) async {
    final localDB = await LocalDB.openMyDatabase();
    try {
      await localDB.insert('location', location.toJson());
      return true;
    } on Exception {
      // print('Error: $e');
      return false;
    }
  }

  // set location flag
  static Future<bool> setLocationFlag(String id) async {
    final localDB = await LocalDB.openMyDatabase();
    try {
      await localDB.update('location', {'flag': '1'},
          where: 'trans_id = ?', whereArgs: [id]);
      return true;
    } on Exception {
      // print('Error: $e');
      return false;
    }
  }
}
