import 'dart:convert';
import 'package:http/http.dart' as http;

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class ProductMasterInputClass {
  List<List<String>>? productInputData;

  ProductMasterInputClass({this.productInputData});

  factory ProductMasterInputClass.fromTXT(String txt) {
    txt = utf8.decode(txt.runes.toList());

    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> productInput = [];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      productInput.add(temp);
    }
    return ProductMasterInputClass(productInputData: productInput);
  }

  toJson() {
    return {'productInput': productInputData!.map((e) => e).toList()};
  }

  static Future<bool> getProductMasterInput() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.productMasterDetailsURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    http.Response response = await http.get(url);
    // print('Survey Input URL: $url');
    // print('Survey Input Status Code: ${response.statusCode}');
    //print('Survey Input Response: ${response.body}');
    if (response.statusCode == 200) {
      if (response.body.contains('¥')) {
        final ProductMasterInputClass product =
            ProductMasterInputClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<String> columnNames = await localDB
            .rawQuery('PRAGMA table_info(product_master)')
            .then((value) {
          final List<String> temp = [];
          for (var element in value) {
            if(element['name'].toString()!='cl_stk'){
              temp.add(element['name'].toString());
            }
            
          }
          return temp;
        });
        final batch = localDB.batch();
        for (int j = 0; j < product.productInputData!.length; j++) {
          final Map<String, dynamic> productInputMap = {};
          for (int i = 0; i < columnNames.length; i++) {
            productInputMap[columnNames[i]] = product.productInputData![j][i];
          }

          batch.insert('product_master', productInputMap);
        }
        await batch.commit(noResult: true);
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  static Future<List<Map<String, dynamic>>> getProductFromLocalDB(
      String query) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> productData = await localDB.rawQuery(
        "$query ${query.contains('WHERE') ? 'AND' : 'WHERE'} acedns = 'Y'");
    return productData;
  }
}
