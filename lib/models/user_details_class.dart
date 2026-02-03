import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class UserDetailsClass {
  List<UserDetailsData>? userDetailsData;

  UserDetailsClass({this.userDetailsData});

  UserDetailsClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    userDetailsData ??= <UserDetailsData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      userDetailsData?.add(UserDetailsData.fromXML(element));
    }
  }

  toJson() {
    return {
      'userDetailsData': userDetailsData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getUserDetails() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(AppWebService.userDetailsURL);
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
        final UserDetailsClass userDetails =
            UserDetailsClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> userDetailsMap = {};
        userDetails.userDetailsData?.forEach((element) {
          if (element.columnName != null) {
            if (element.columnName == 'business_prospect_phone_not_mandatory') {
              element.columnName = 'business_prospect_phone_mandatory';
            }
            userDetailsMap[element.columnName.toString()] = element.columnData;
          }
        });
        userDetailsMap.removeWhere((key, value) => key == 'last_update_time');
        await localDB.insert('user_details', userDetailsMap);
        // print('User Details: $userDetailsMap');
        return true;
      } else {
        return false;
      }
    } else {
      throw Exception('Failed to load UserDetails');
    }
  }
}

class UserDetailsData {
  String? columnName;
  String? columnData;

  UserDetailsData({this.columnName, this.columnData});

  UserDetailsData.fromXML(XmlElement element) {
    columnName = element.name.local;
    columnData = element.innerText;
  }

  toJson() {
    return {'columnName': columnName, 'columnData': columnData};
  }
}
