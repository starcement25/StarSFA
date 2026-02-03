import 'dart:developer';

import 'package:starsfa/models/local_db.dart';
import 'package:xml/xml.dart';

class SurveyOutputClass {
  final String surveyId;
  final String actionId;
  final String value;
  final String type;
  final String rowId;

  SurveyOutputClass({
    required this.surveyId,
    required this.actionId,
    required this.value,
    required this.type,
    required this.rowId,
  });

  toJson() {
    return {
      'survey_id': surveyId,
      'action_id': actionId,
      'value': value,
      'type': type,
      'row_id': rowId,
    };
  }

  // Convert to XML format
  XmlElement toXml() {
    return XmlElement(
      XmlName('survey_details'),
      [],
      [
        XmlElement(
            XmlName('survey_id'), [], [XmlText('<![CDATA[$surveyId]]>')]),
        XmlElement(
            XmlName('action_id'), [], [XmlText('<![CDATA[$actionId]]>')]),
        XmlElement(XmlName('value'), [], [XmlText('<![CDATA[$value]]>')]),
        XmlElement(XmlName('type'), [], [XmlText('<![CDATA[$type]]>')]),
        XmlElement(XmlName('row_id'), [], [XmlText('<![CDATA[$rowId]]>')]),
      ],
    );
  }

  // get survey output by id
  static Future<List<SurveyOutputClass>?> getSurveyDetailsById(
      String id) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> surveyOutput = await localDB
        .query('survey_output', where: 'survey_id = ?', whereArgs: [id]);
    if (surveyOutput.isEmpty) {
      return null;
    } else {
      return surveyOutput.map((surveyOutput) {
        return SurveyOutputClass(
          surveyId: surveyOutput['survey_id'].toString(),
          actionId: surveyOutput['action_id'].toString(),
          value: surveyOutput['value'].toString(),
          type: surveyOutput['type'].toString(),
          rowId: surveyOutput['row_id'].toString(),
        );
      }).toList();
    }
  }

  static Future<bool> saveSurveyOutput(SurveyOutputClass surveyOutput) async {
    final localDB = await LocalDB.openMyDatabase();
    try {
      await localDB.insert('survey_output', surveyOutput.toJson());
      return true;
    } on Exception {
      // print('Error: $e');
      return false;
    }
  }

  static Future<bool> saveComplaintReport(Map<String, String> value) async {
    log(value.toString());
    final localDB = await LocalDB.openMyDatabase();
    try {
      int reps = await localDB.insert('complaint_master', value);
      log("$reps");
      return true;
    } on Exception {
      return false;
    }
  }
}
