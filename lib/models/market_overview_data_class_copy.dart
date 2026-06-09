import 'dart:convert';

import 'package:http/http.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/location_class.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/survey_header_class.dart';
import 'package:starsfa/models/survey_output_class.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class MarketOverviewDataClass {
  final LocationClass location;
  final SurveyHeaderClass surveyHeader;
  final List<SurveyOutputClass> surveyDetails;

  MarketOverviewDataClass({
    required this.location,
    required this.surveyHeader,
    required this.surveyDetails,
  });

  // Convert the data to XML format
  XmlElement toXml() {
    return XmlElement(
      XmlName('root'),
      [],
      [
        XmlElement(
          XmlName('survey'),
          [],
          [
            location.toXml(),
            XmlElement(
              XmlName('surveydata'),
              [],
              [
                surveyHeader.toXML(),
                ...surveyDetails.map((surveyDetail) => surveyDetail.toXml()),
              ],
            ),
          ],
        ),
      ],
    );
  }

  // get market overview data by id
  static Future<MarketOverviewDataClass?> getMarketOverviewDataById(
      String id) async {
    // get location data
    final location = await LocationClass.getLocationById(id);
    if (location == null) {
      return null;
    }

    // get survey header data
    final surveyHeader = await SurveyHeaderClass.getSurveyHeaderById(id);
    if (surveyHeader == null) {
      return null;
    }

    // get survey details data
    final surveyDetails = await SurveyOutputClass.getSurveyDetailsById(id);
    if (surveyDetails == null) {
      return null;
    }

    return MarketOverviewDataClass(
      location: location,
      surveyHeader: surveyHeader,
      surveyDetails: surveyDetails,
    );
  }

  // set flag to 1 by survey id in survey_header, survey_output and location
  static Future<bool> setFlagTo1(String surveyId) async {
    final localDB = await LocalDB.openMyDatabase();
    // set flag to 1 in survey_header
    await localDB.update(
      'survey_header',
      {'flag': 1},
      where: 'survey_id = ?',
      whereArgs: [surveyId],
    );
    // set flag to 1 in survey_output
    await localDB.update(
      'survey_output',
      {'flag': 1},
      where: 'survey_id = ?',
      whereArgs: [surveyId],
    );
    // set flag to 1 in location
    await localDB.update(
      'location',
      {'flag': 1},
      where: 'trans_id = ?',
      whereArgs: [surveyId],
    );
    return true;
  }

  // upload market overview data
  static Future<bool> uploadMarketOverviewData() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    // get all distinct survey ids from survey_header
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, Object?>> surveyIds = await localDB.query(
      'survey_header',
      columns: ['survey_id'],
      distinct: true,
      where: 'flag = ?',
      whereArgs: ['0'],
    );
    // check if survey ids is empty
    if (surveyIds.isEmpty) {
      return true;
    }
    // get all market overview data by survey id
    List<MarketOverviewDataClass> marketOverviewData = [];

    for (final surveyId in surveyIds) {
      final marketOverviewDataById =
          await MarketOverviewDataClass.getMarketOverviewDataById(
        surveyId['survey_id'].toString(),
      );
      if (marketOverviewDataById != null) {
        marketOverviewData.add(marketOverviewDataById);
      }
    }

    // upload all market overview data
    for (final marketOverviewData in marketOverviewData) {
      // upload the data
      final XmlElement xml = marketOverviewData.toXml();
      final UserLoginClass? user = await UserLoginClass.getLocalUser();
      // upload the data to the server
      final String url =
          '${AppWebService.surveyUploadURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=1971-01-01 10:10:10';
      String bodyData =
          '<?xml version="1.0" encoding="UTF-8"?>${xml.toXmlString(newLine: '')}';
      //  remove the new line character
      bodyData = bodyData.replaceAll('&lt;', '<');
      bodyData = bodyData.replaceAll('&gt;', '>');
      print("URL: $url");
      print("Body: $bodyData");
      // if upload fails return false
      final Response response = await http.post(
        Uri.parse(url),
        body: bodyData,
        encoding: Encoding.getByName('utf-8'),
        headers: {
          'Content-Type': 'application/xml',
        },
      );
      print("Response: ${response.statusCode}");
      print("Response: ${response.body}");
      if (response.statusCode == 200) {
        if (response.body == '2') {
          // set flag to 1 in survey_header, survey_output and location
          await MarketOverviewDataClass.setFlagTo1(
            marketOverviewData.surveyHeader.surveyId,
          );
        } else {
          return false;
        }
      } else {
        // show error message
        return false;
      }
    }
    // await AppFilesUpload.uploadImageZip();
    return true;
  }
}
