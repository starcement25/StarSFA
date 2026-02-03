import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class MenuDetailsClass {
  List<MenuDetailsData>? menuDetailsData;

  MenuDetailsClass({this.menuDetailsData});

  MenuDetailsClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    menuDetailsData ??= <MenuDetailsData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      menuDetailsData?.add(MenuDetailsData.fromXML(element));
    }
  }

  toJson() {
    return {
      'menuDetailsData': menuDetailsData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getMenuDetails() async {
    final bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        "${AppWebService.menuDetailsURL}?emp_code=${user?.empCode}&nick_name=${AppWebService.nickname}&mode=SETUP&deviceid=${user?.deviceid}");
    // get response from the server
    http.Response response = await http.get(url);
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is xml
      if (response.body.startsWith('<?xml')) {
        // return the response body
        final MenuDetailsClass menuDetails =
            MenuDetailsClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> menuDetailsMap = {};
        menuDetails.menuDetailsData?.forEach((element) {
          if (element.columnName != null) {
            if (element.columnName == 'order') {
              element.columnName = 'take_order';
            }
            if (element.columnName == 'notes_and_info') {
              element.columnName = 'notes';
            }
            if (element.columnName == 'tour_exp_fuel_bill') {
              element.columnName = 'tour_exp';
            }
            menuDetailsMap[element.columnName.toString()] = element.columnData;
          }
        });
        menuDetailsMap.removeWhere((key, value) => key == 'last_update_time');
        await localDB.insert('menu_details', menuDetailsMap);
        return true;
      } else {
        return false;
      }
    } else {
      throw Exception(
          'Failed to load data: ${response.statusCode} - ${response.body}');
    }
  }
}

class MenuDetailsData {
  String? columnName;
  String? columnData;

  MenuDetailsData({this.columnName, this.columnData});

  MenuDetailsData.fromXML(XmlElement element) {
    // print('Element: ${element.toXmlString()}');
    columnName = element.name.local;
    columnData = element.innerText;
    // print('Column Name: $columnName');
    // print('Column Data: $columnData');
  }

  toJson() {
    return {
      'columnName': '$columnName',
      'columnData': '$columnData',
    };
  }
}
