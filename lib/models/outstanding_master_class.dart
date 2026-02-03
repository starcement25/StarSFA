import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class OutstandingMasterClass {
  List<OutstandingMasterData>? outstandingMasterData;

  OutstandingMasterClass({this.outstandingMasterData});

  OutstandingMasterClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    outstandingMasterData = <OutstandingMasterData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      outstandingMasterData?.add(OutstandingMasterData.fromXML(element));
    }
  }

  toJson() {
    return {
      'outstandingMasterData':
          outstandingMasterData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getOutstandingMaster() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(AppWebService.outstandingMasterURL);
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
        final OutstandingMasterClass outstandingMaster =
            OutstandingMasterClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> outstandingMasterMap = {};
        outstandingMaster.outstandingMasterData?.forEach((element) {
          if (element.columnName != null) {
            outstandingMasterMap[element.columnName.toString()] =
                element.columnData;
          }
        });
        outstandingMasterMap
            .removeWhere((key, value) => key == 'last_update_time');
        await localDB.insert('outstanding_master', outstandingMasterMap);
        // print('Outstanding Master Details: $outstandingMasterMap');
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class OutstandingMasterData {
  String? columnName;
  String? columnData;

  OutstandingMasterData({this.columnName, this.columnData});

  OutstandingMasterData.fromXML(XmlElement element) {
    columnName = element.name.local;
    columnData = element.innerText;
  }

  toJson() {
    return {'columnName': columnName, 'columnData': columnData};
  }
}
