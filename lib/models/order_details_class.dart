import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class OrderFormDetailsClass {
  List<OrderFormDetailsData>? orderFormDetailsData;

  OrderFormDetailsClass({this.orderFormDetailsData});

  OrderFormDetailsClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    orderFormDetailsData ??= <OrderFormDetailsData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      orderFormDetailsData?.add(OrderFormDetailsData.fromXML(element));
    }
  }

  toJson() {
    return {
      'orderDetailsData': orderFormDetailsData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getOrderDetails() async {
    final bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(AppWebService.orderFormDetailsURL);
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
        final OrderFormDetailsClass orderDetails =
            OrderFormDetailsClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> orderDetailsMap = {};
        orderDetails.orderFormDetailsData?.forEach((element) {
          if (element.columnName != null) {
            // if (element.columnName == 'business_prospect_phone_not_mandatory') {
            //   element.columnName = 'business_prospect_phone_mandatory';
            // }
            orderDetailsMap[element.columnName.toString()] = element.columnData;
          }
        });
        orderDetailsMap.removeWhere((key, value) => key == 'last_update_time');
        await localDB.insert('order_form_details', orderDetailsMap);
        // print('Order Details: $orderDetailsMap');
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class OrderFormDetailsData {
  String? columnName;
  String? columnData;

  OrderFormDetailsData({this.columnName, this.columnData});

  OrderFormDetailsData.fromXML(XmlElement element) {
    columnName = element.name.local;
    columnData = element.innerText;
  }

  toJson() {
    return {'columnName': columnName, 'columnData': columnData};
  }
}
