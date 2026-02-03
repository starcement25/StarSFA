import 'package:starsfa/models/local_db.dart';
import 'package:xml/xml.dart';

class SurveyHeaderClass {
  final String surveyType;
  final String menuName;
  final String mallId;
  final String mallName;
  final String businessName;
  final String contactName;
  final String phoneNo;
  final String questionsAnswered;
  final String routeCode;
  final String checkInTime;
  final String surveyId;

  SurveyHeaderClass({
    required this.surveyType,
    required this.menuName,
    required this.mallId,
    required this.mallName,
    required this.businessName,
    required this.contactName,
    required this.phoneNo,
    required this.questionsAnswered,
    required this.routeCode,
    required this.checkInTime,
    required this.surveyId,
  });

  toJson() {
    return {
      'survey_type': surveyType,
      'menu_name': menuName,
      'mall_id': mallId,
      'mall_name': mallName,
      'business_name': businessName,
      'contact_name': contactName,
      'phone_no': phoneNo,
      'questions_answered': questionsAnswered,
      'route_code': routeCode,
      'check_in_time': checkInTime,
      'survey_id': surveyId,
    };
  }

  XmlElement toXML() {
    return XmlElement(
      XmlName('survey_header'),
      [],
      [
        XmlElement(XmlName('survey_type'), [], [XmlText('<![CDATA[$surveyType]]>')]),
        XmlElement(XmlName('menu_name'), [], [XmlText('<![CDATA[$menuName]]>')]),
        XmlElement(XmlName('mall_id'), [], [XmlText('<![CDATA[$mallId]]>')]),
        XmlElement(XmlName('mall_name'), [], [XmlText('<![CDATA[$mallName]]>')]),
        XmlElement(XmlName('business_name'), [], [XmlText('<![CDATA[$businessName]]>')]),
        XmlElement(XmlName('contact_name'), [], [XmlText('<![CDATA[$contactName]]>')]),
        XmlElement(XmlName('phone_no'), [], [XmlText('<![CDATA[$phoneNo]]>')]),
        XmlElement(
            XmlName('questions_answered'), [], [XmlText('<![CDATA[$questionsAnswered]]>')]),
        XmlElement(XmlName('route_code'), [], [XmlText('<![CDATA[$routeCode]]>')]),
        XmlElement(XmlName('check_in_time'), [], [XmlText('<![CDATA[$checkInTime]]>')]),
        XmlElement(XmlName('survey_id'), [], [XmlText('<![CDATA[$surveyId]]>')]),
      ],
    );
  }

  // get survey header by id
  static Future<SurveyHeaderClass?> getSurveyHeaderById(String id) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> surveyHeader =
        await localDB.query('survey_header', where: 'survey_id = ?', whereArgs: [id]);
    if (surveyHeader.isEmpty) {
      return null;
    } else {
      return SurveyHeaderClass(
        surveyType: surveyHeader[0]['survey_type'].toString(),
        menuName: surveyHeader[0]['menu_name'].toString(),
        mallId: surveyHeader[0]['mall_id'].toString(),
        mallName: surveyHeader[0]['mall_name'].toString(),
        businessName: surveyHeader[0]['business_name'].toString(),
        contactName: surveyHeader[0]['contact_name'].toString(),
        phoneNo: surveyHeader[0]['phone_no'].toString(),
        questionsAnswered: surveyHeader[0]['questions_answered'].toString(),
        routeCode: surveyHeader[0]['route_code'].toString(),
        checkInTime: surveyHeader[0]['check_in_time'].toString(),
        surveyId: surveyHeader[0]['survey_id'].toString(),
      );
    }
  }

  static Future<bool> saveSurveyHeader(SurveyHeaderClass surveyHeader) async {
    final localDB = await LocalDB.openMyDatabase();
    try {
      await localDB.insert('survey_header', surveyHeader.toJson());
      return true;
    } on Exception {
      // print('Error: $e');
      return false;
    }
  }
}
