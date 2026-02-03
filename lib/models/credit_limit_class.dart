import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class CreditLimitClass {
  List<CreditLimitData>? creditLimitData;

  CreditLimitClass({this.creditLimitData});

  CreditLimitClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    creditLimitData = <CreditLimitData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      creditLimitData?.add(CreditLimitData.fromXML(element));
    }
  }

  toJson() {
    return {
      'creditLimitData':
          creditLimitData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getCreditLimit() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(AppWebService.creditLimitURL);
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
        final CreditLimitClass creditLimit =
            CreditLimitClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> creditLimitMap = {};
        creditLimit.creditLimitData?.forEach((element) {
          if (element.columnName != null) {
            creditLimitMap[element.columnName.toString()] =
                element.columnData;
          }
        });
        creditLimitMap
            .removeWhere((key, value) => key == 'last_update_time');
        await localDB.insert('credit_limit', creditLimitMap);
        // print('Credit Limit Details: $creditLimitMap');
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class CreditLimitData {
  String? columnName;
  String? columnData;

  CreditLimitData({this.columnName, this.columnData});

  CreditLimitData.fromXML(XmlElement element) {
    columnName = element.name.local;
    columnData = element.innerText;
  }

  toJson() {
    return {'columnName': columnName, 'columnData': columnData};
  }
}
