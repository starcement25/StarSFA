import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class MarketFeedbackDetailsClass {
  List<MarketFeedbackDetailsData>? marketFeedbackDetailsData;

  MarketFeedbackDetailsClass({this.marketFeedbackDetailsData});

  MarketFeedbackDetailsClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    marketFeedbackDetailsData = <MarketFeedbackDetailsData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      marketFeedbackDetailsData
          ?.add(MarketFeedbackDetailsData.fromXML(element));
    }
  }

  toJson() {
    return {
      'marketFeedbackDetailsData':
          marketFeedbackDetailsData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getMarketFeedbackDetails() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(AppWebService.marketFeedbackDetailsURL);
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
        final MarketFeedbackDetailsClass marketFeedbackDetails =
            MarketFeedbackDetailsClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> marketFeedbackDetailsMap = {};
        marketFeedbackDetails.marketFeedbackDetailsData?.forEach((element) {
          if (element.columnName != null) {
            marketFeedbackDetailsMap[element.columnName.toString()] =
                element.columnData;
          }
        });
        marketFeedbackDetailsMap
            .removeWhere((key, value) => key == 'mf_secondary_sales_option');
        await localDB.insert(
            'market_feedback_details', marketFeedbackDetailsMap);
        // print('Market Feedback Details: $marketFeedbackDetailsMap');
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class MarketFeedbackDetailsData {
  String? columnName;
  String? columnData;

  MarketFeedbackDetailsData({this.columnName, this.columnData});

  MarketFeedbackDetailsData.fromXML(XmlElement element) {
    columnName = element.name.local;
    columnData = element.innerText;
  }

  toJson() {
    return {'columnName': columnName, 'columnData': columnData};
  }
}
