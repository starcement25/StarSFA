import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:xml/xml.dart';
import 'package:sqflite/sqflite.dart';

class TableStructureDetails {
  List<Data>? data;

  TableStructureDetails({this.data});

  TableStructureDetails.fromJson(Map<String, dynamic> json) {
    if (json['data'] != null) {
      data = <Data>[];
      json['data'].forEach((v) {
        data?.add(Data.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if (this.data != null) {
      data['data'] = this.data?.map((v) => v.toJson()).toList();
    }
    return data;
  }

  TableStructureDetails.fromXml(String xml) {
    final XmlDocument doc = XmlDocument.parse(xml);
    final XmlElement recordsetXml = doc.rootElement;
    for (XmlElement element in recordsetXml.childElements) {
      data ??= <Data>[];
      data?.add(Data.fromXml(element));
    }
  }

  static Future<TableStructureDetails> getTableStructureDetails({
    String? mode = "INSTALL",
    UserLoginClass? user,
  }) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return TableStructureDetails();
    }
    // get local user
    // user ??= await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        "${AppWebService.databaseDetailsURL}?emp_code=${user?.empCode}&nick_name=${AppWebService.nickname}&mode=$mode&deviceid=${user?.deviceid}");
    print('Query: $url');
    // get response from the server
    http.Response response = await http.get(url);
    // check if the response is successful
    if (response.statusCode == 200) {
      print('Query: ${response.body}');
      // check if the response is xml
      if (response.body.startsWith('<?xml')) {
        // return the response body
        return TableStructureDetails.fromXml(response.body);
      } else {
        // return an empty object
        return TableStructureDetails();
      }
    } else {
      // return an empty object
      return TableStructureDetails();
    }
  }

  // create and populate the table structure in the local database
  static Future<void> createTableStructure(
      UserLoginClass user, Database db) async {
    // get the table structure details
    TableStructureDetails table = await getTableStructureDetails(user: user);
    // check if the table structure is not empty
    if (table.data?.isNotEmpty ?? false) {
      // open the local database
      Database dbLocal = db;
      // local database batch
      Batch batch = dbLocal.batch();
      // loop through the table structure
      for (Data data in table.data ?? []) {
        // check if the table name is not empty
        if (data.tableName?.isNotEmpty ?? false) {
          // print('Creating Table: ${data.tableName}');
          // print('Table Structure: ${data.tableStructure}');
          // table structure query seperate by line
          List<String> queries = data.tableStructure?.split(';') ?? [];
          // clean the queries
          queries = queries.map((String query) {
            return query.trim();
          }).toList();
          // create the table
          for (String query in queries) {
            if (query.isNotEmpty) {
              // print('Query: $query');
              batch.execute(query);
            }
          }
        }
      }
      // commit the batch
      await batch.commit();
    }
  }
}

class Data {
  String? tableName;
  String? tableStructure;
  String? transaction;
  String? master;
  String? dbVersion;
  String? baseUrlChanged;
  String? currentBaseurlApp;

  Data(
      {this.tableName,
      this.tableStructure,
      this.transaction,
      this.master,
      this.dbVersion,
      this.baseUrlChanged,
      this.currentBaseurlApp});

  Data.fromJson(Map<String, dynamic> json) {
    tableName = json['table_name'];
    tableStructure = json['table_structure'];
    transaction = json['transaction'];
    master = json['master'];
    dbVersion = json['db_version'];
    baseUrlChanged = json['base_url_changed'];
    currentBaseurlApp = json['current_baseurl_app'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['table_name'] = tableName;
    data['table_structure'] = tableStructure;
    data['transaction'] = transaction;
    data['master'] = master;
    data['db_version'] = dbVersion;
    data['base_url_changed'] = baseUrlChanged;
    data['current_baseurl_app'] = currentBaseurlApp;
    return data;
  }

  Data.fromXml(XmlElement data) {
    tableName = data.getElement('table_name')?.innerText;
    tableStructure = data.getElement('table_structure')?.innerText;
    transaction = data.getElement('transaction')?.innerText;
    master = data.getElement('master')?.innerText;
    dbVersion = data.getElement('db_version')?.innerText;
    baseUrlChanged = data.getElement('base_url_changed')?.innerText;
    currentBaseurlApp = data.getElement('current_baseurl_app')?.innerText;
  }
}
