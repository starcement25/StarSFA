import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class SurveyFormDetailsClass {
  List<SurveyFormDetailsData>? surveyFormDetailsData;

  SurveyFormDetailsClass({this.surveyFormDetailsData});

  SurveyFormDetailsClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    surveyFormDetailsData = <SurveyFormDetailsData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      surveyFormDetailsData?.add(SurveyFormDetailsData.fromXML(element));
    }
  }

  toJson() {
    return {
      'surveyFormDetailsData':
          surveyFormDetailsData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getSurveyFormDetails() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(AppWebService.surveyFormDetailsURL);
    // get response from the server
    http.Response response = await http.post(url, body: {
      'emp_code': user?.empCode.toString(),
      'nick_name': AppWebService.nickname,
      'mode': 'SETUP',
      'deviceid': user?.deviceid.toString()
    });
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is xml
      if (response.body.startsWith('<?xml')) {
        // return the response body
        final SurveyFormDetailsClass surveyFormDetails =
            SurveyFormDetailsClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> surveyFormDetailsMap = {};
        surveyFormDetails.surveyFormDetailsData?.forEach((element) {
          if (element.columnName != null) {
            surveyFormDetailsMap[element.columnName.toString()] =
                element.columnData;
          }
        });
        surveyFormDetailsMap
            .removeWhere((key, value) => key == 'last_update_time');
        await localDB.insert('survey_form_details', surveyFormDetailsMap);
        // print('Survey Form Details: $surveyFormDetailsMap');
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class SurveyFormDetailsData {
  String? columnName;
  String? columnData;

  SurveyFormDetailsData({this.columnName, this.columnData});

  SurveyFormDetailsData.fromXML(XmlElement element) {
    columnName = element.name.local;
    columnData = element.innerText;
  }

  toJson() {
    return {'columnName': columnName, 'columnData': columnData};
  }
}
