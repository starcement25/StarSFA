import 'dart:convert';

import 'package:http/http.dart';
import 'package:starsfa/log/log_service.dart';
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

  /// Convert object to XML
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
                ...surveyDetails.map((e) => e.toXml()).toList(),
              ],
            ),
          ],
        ),
      ],
    );
  }

  /// Fetch full survey data by ID
  static Future<MarketOverviewDataClass?> getMarketOverviewDataById(
      String id) async {
    try {
      final location = await LocationClass.getLocationById(id);
      if (location == null) return null;

      final surveyHeader = await SurveyHeaderClass.getSurveyHeaderById(id);
      if (surveyHeader == null) return null;

      final surveyDetails = await SurveyOutputClass.getSurveyDetailsById(id);
      if (surveyDetails == null) return null;

      return MarketOverviewDataClass(
        location: location,
        surveyHeader: surveyHeader,
        surveyDetails: surveyDetails,
      );
    } catch (e) {
      print("Error getMarketOverviewDataById : $e");
      return null;
    }
  }

  /// Set uploaded flag = 1
  static Future<bool> setFlagTo1(String surveyId) async {
    try {
      final db = await LocalDB.openMyDatabase();

      await db.update(
        'survey_header',
        {'flag': 1},
        where: 'survey_id = ?',
        whereArgs: [surveyId],
      );

      await db.update(
        'survey_output',
        {'flag': 1},
        where: 'survey_id = ?',
        whereArgs: [surveyId],
      );

      await db.update(
        'location',
        {'flag': 1},
        where: 'trans_id = ?',
        whereArgs: [surveyId],
      );

      return true;
    } catch (e) {
      print("Error setFlagTo1 : $e");
      return false;
    }
  }

  static void printLong(String text) {
    const chunkSize = 800;
    for (int i = 0; i < text.length; i += chunkSize) {
      print(text.substring(i, (i + chunkSize).clamp(0, text.length)));
    }
  }

  /// Upload Market Overview Data
  static Future<bool> uploadMarketOverviewData() async {
    try {
      /// Check internet
      bool isConnected = await NetworkService.checkConnectionAll();
      if (!isConnected) {
        await LogService.logSetup('uploadMarketOverviewData Network Error');
        print("No internet connection");
        return false;
      }

      final db = await LocalDB.openMyDatabase();

      /// Fetch pending survey ids
      final List<Map<String, Object?>> surveyIds = await db.query(
        'survey_header',
        columns: ['survey_id'],
        distinct: true,
        where: 'flag = ?',
        whereArgs: [0],
      );

      if (surveyIds.isEmpty) {
        print("No pending survey found");
        return true;
      }

      /// Build full survey objects
      List<MarketOverviewDataClass> surveyDataList = [];

      for (final item in surveyIds) {
        final data = await getMarketOverviewDataById(
          item['survey_id'].toString(),
        );

        if (data != null) {
          surveyDataList.add(data);
        }
      }

      /// Get logged user
      final user = await UserLoginClass.getLocalUser();
      if (user == null) {
        await LogService.logSetup(
            'uploadMarketOverviewData User not found locally');
        print("User not found locally");
        return false;
      }

      /// Upload each survey
      for (final surveyData in surveyDataList) {
        final xmlElement = surveyData.toXml();

        final url =
            '${AppWebService.surveyUploadURL}?nick_name=${AppWebService.nickname}'
            '&emp_code=${user.empCode}'
            '&last_update_time=1971-01-01 10:10:10';

        String bodyData =
            '<?xml version="1.0" encoding="UTF-8"?>${xmlElement.toXmlString(newLine: '')}'
                .replaceAll("&lt;", '<')
                .replaceAll("&gt;", '>');

        print("Upload URL : $url");
        printLong("Upload Body : $bodyData");
        await LogService.logSetup('uploadMarketOverviewData : $url');
        await LogService.logSetup('uploadMarketOverviewData : $bodyData');

        final Response response = await http.post(
          Uri.parse(url),
          body: bodyData,
          encoding: Encoding.getByName('utf-8'),
          headers: {
            'Content-Type': 'application/xml',
          },
        );

        print("Response Code : ${response.statusCode}");
        print("Response Body : ${response.body}");

        if (response.statusCode == 200) {
          if (response.body.trim() == '2') {
            await setFlagTo1(surveyData.surveyHeader.surveyId);
          } else {
            await LogService.logSetup(
                'uploadMarketOverviewData API returned failure response ${response.body.trim()}');
            print("API returned failure response");
            return false;
          }
        } else {
          await LogService.logSetup(
              'uploadMarketOverviewData API status failure');
          print("API status failure");
          return false;
        }
      }

      print("All survey uploaded successfully");
      return true;
    } catch (e) {
      await LogService.logSetup('uploadMarketOverviewData error : $e');
      print("uploadMarketOverviewData error : $e");
      return false;
    }
  }
}
