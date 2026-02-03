import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class RoutePlanDetailsClass {
  List<RoutePlanDetailsData>? routePlanDetailsData;

  RoutePlanDetailsClass({this.routePlanDetailsData});

  RoutePlanDetailsClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    routePlanDetailsData ??= <RoutePlanDetailsData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      routePlanDetailsData?.add(RoutePlanDetailsData.fromXML(element));
    }
  }

  toJson() {
    return {
      'routePlanDetailsData':
          routePlanDetailsData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getRoutePlanDetails() async {
    final bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(AppWebService.routePlanDetailsURL);
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
        final RoutePlanDetailsClass routePlanDetails =
            RoutePlanDetailsClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> routePlanDetailsMap = {};
        routePlanDetails.routePlanDetailsData?.forEach((element) {
          if (element.columnName != null) {
            // if (element.columnName == 'business_prospect_phone_not_mandatory') {
            //   element.columnName = 'business_prospect_phone_mandatory';
            // }
            routePlanDetailsMap[element.columnName.toString()] =
                element.columnData;
          }
        });
        routePlanDetailsMap
            .removeWhere((key, value) => key == 'last_update_time');
        await localDB.insert('route_plan_details', routePlanDetailsMap);
        // print('Route Plan Details: $routePlanDetailsMap');
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class RoutePlanDetailsData {
  String? columnName;
  String? columnData;

  RoutePlanDetailsData({this.columnName, this.columnData});

  RoutePlanDetailsData.fromXML(XmlElement element) {
    columnName = element.name.local;
    columnData = element.innerText;
  }

  toJson() {
    return {'columnName': columnName, 'columnData': columnData};
  }
}
