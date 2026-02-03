import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:http/http.dart' as http;

class ProductDetailsClass{
  List<ProductDetailsData>? productDetailsData;

  ProductDetailsClass({this.productDetailsData});

  ProductDetailsClass.fromXML(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    productDetailsData ??= <ProductDetailsData>[];
    for (XmlElement element in recordsetXml.childElements.first.childElements) {
      productDetailsData?.add(ProductDetailsData.fromXML(element));
    }
  }

  toJson() {
    return {
      'productDetailsData': productDetailsData?.map((e) => e.columnData).toList()
    };
  }

  static Future<bool> getProductDetails() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(AppWebService.productDetailsURL);
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
        final ProductDetailsClass productDetails =
            ProductDetailsClass.fromXML(response.body);
        final localDB = await LocalDB.openMyDatabase();
        Map<String, dynamic> productDetailsMap = {};
        productDetails.productDetailsData?.forEach((element) {
          if (element.columnName != null) {
            // if (element.columnName == 'business_prospect_phone_not_mandatory') {
            //   element.columnName = 'business_prospect_phone_mandatory';
            // }
            productDetailsMap[element.columnName.toString()] = element.columnData;
          }
        });
        productDetailsMap.removeWhere((key, value) => key == 'last_update_time');
        await localDB.insert('product_details', productDetailsMap);
        // print('Product Details: $productDetailsMap');
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class ProductDetailsData {
  String? columnName;
  String? columnData;

  ProductDetailsData({this.columnName, this.columnData});

  ProductDetailsData.fromXML(XmlElement element) {
    columnName = element.name.local;
    columnData = element.innerText;
  }

  toJson() {
    return {
      'columnName': columnName,
      'columnData': columnData
    };
  }
}