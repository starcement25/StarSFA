import 'dart:convert';

import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

// temp.setCustomerCode(RowData[0]);
//                         temp.setCustomerName(RowData[1]);
//                         temp.setRouteCode(RowData[2]);
//                         temp.setEmpCode(RowData[3]);
//                         temp.setCurrentBalance(RowData[4]);
//                         temp.setCreditLimit(RowData[5]);
//                         temp.setIsACEDNS(RowData[6]);
//                         temp.setIsBlackList(RowData[7]);
//                         temp.setTradeDiscount(RowData[8]);
//                         temp.setCustomerType(RowData[9]);
//                         temp.setRdsTag(RowData[10]);
//                         temp.setSaudaValidityPeriod(RowData[11]);
//                         temp.setAddress(RowData[12]);
//                         temp.setPin(RowData[13]);
//                         temp.setNumber(RowData[14]);
//                         temp.setDnsCustCode(RowData[15]);
//                         temp.setLandlineNo(RowData[16]);
//                         temp.setOwnerName(RowData[17]);
//                         temp.setOwnerPhone(RowData[18]);
//                         temp.setCustClass(RowData[19]);
//                         temp.setWeeklyClosingDay(RowData[20]);
//                         temp.setCoverageType(RowData[21]);
//                         temp.setTIN(RowData[22]);
//                         temp.setPAN(RowData[23]);
//                         temp.setMinimumStock(RowData[24]);
//                         temp.setBranchCode(RowData[25]);
//                         temp.setVisitDay(RowData[26]);
//                         temp.setEmail(RowData[27]);
//                         temp.setSaudaLimit(RowData[28]);
//                         temp.setPendingQty(RowData[29]);
//                         temp.setIncoTerms(RowData[30]);
//                         temp.setLoadabilityTon(RowData[31]);
//                         temp.setTransportMode(RowData[32]);
//                         temp.setstate(RowData[33]);
//                         temp.setSaudaType(RowData[34]);
//                         temp.setzone(RowData[35]);
//                         temp.setVisitSequence(RowData[36]);
//                         temp.setactivated(RowData[37]);
//                         temp.setactivated_customer_code(RowData[38]);
//                         temp.setretailer_app(RowData[39]);
//                         temp.setbase_latt(RowData[40]);
//                         temp.setbase_longi(RowData[41]);
//                         temp.setneed_location_update(RowData[42]);
//                         temp.setciLogic(RowData[44]);
//                         temp.setcategoryOfStore(RowData[45]);
//                         temp.setinStoreActivityPossible(RowData[46]);
//                         temp.setIsNewCustomer(RowData[47]);
//                         temp.setownerImage(RowData[48]);
//                         temp.setfirmName(RowData[49]);
//                         temp.setoutletImage(RowData[50]);
//                         temp.setgstImage(RowData[51]);
//                         temp.setadharNo(RowData[52]);
//                         temp.setadharImage(RowData[53]);
//                         temp.setWhatsappNumber(RowData[54]);
//                         temp.setDateOfBirth(RowData[55]);
//                         temp.setDateOfAnniversary(RowData[56]);
//                         temp.setSpouseDateOfBirth(RowData[57]);
//                         temp.setFlag("1");
class CustomerMasterClass {
  List<List<String>>? customerMasterData;

  CustomerMasterClass({this.customerMasterData});

  CustomerMasterClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    customerMasterData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      customerMasterData?.add(temp);
    }
  }

  static Future<bool> getCustomerMaster() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final List<String> indexedColumns = [
      'customer_code',
      'customer_name',
      'route_code',
      'emp_code',
      'current_balance',
      'credit_limit',
      'acedns',
      'black_list',
      'TD',
      'cust_type',
      'rds_tag',
      'sauda_validity_period',
      'address',
      'pin',
      'phone_no',
      'dns_customer_code',
      'landline_no',
      'owner_name',
      'owner_phone',
      'cust_class',
      'weekly_closing_day',
      'coverage_type',
      'tin',
      'pan',
      'minimum_stock',
      'branch_code',
      'visit_day',
      'email',
      'sauda_limit',
      'pending_qty',
      'incoterms',
      'loadability_ton',
      'transport_mode',
      'state_code',
      'sauda_type',
      'zone',
      'visit_sequence',
      'activated',
      'activated_customer_code',
      'retailer_app',
      'base_latt',
      'base_longi',
      'need_location_update',
      'CI_logic',
      'category_of_store',
      'instore_activity',
      'is_new_customer',
      'owner_image',
      'firm_name',
      'firm_image',
      'gst_image',
      'aadhar',
      'aadhar_image',
      'whatsapp_no',
      'date_of_birth',
      'date_of_anniversary',
      'spouse_birth_date',
    ];
    final user = await UserLoginClass.getLocalUser();
    String incrementalDownload = await UserLoginClass.getincrementalDownload();
    incrementalDownload = 'no';
    String lastUpdateTime = await UserLoginClass.lastUpdateTime();
    lastUpdateTime = lastUpdateTime.replaceAll(' ', '?');
    Uri url = Uri.parse(
        '${AppWebService.customerMasterURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=$lastUpdateTime&incremental_download=$incrementalDownload&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    http.Response response = await http.get(url);
    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final CustomerMasterClass customerMaster =
            CustomerMasterClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        // get column names
        // final List<String> columnNames = await localDB
        //     .rawQuery('PRAGMA table_info(customer_master)')
        //     .then((value) {
        //   final List<String> temp = [];
        //   for (var element in value) {
        //     temp.add(element['name'].toString());
        //   }
        //   return temp;
        // });
        final batch = localDB.batch();
        customerMaster.customerMasterData?.forEach((element) async {
          final Map<String, dynamic> customerMasterMap = {};
          for (int i = 0; i < indexedColumns.length; i++) {
            // check if the column exists in indexedColumns
            customerMasterMap[indexedColumns[i]] =
                element[indexedColumns.indexOf(indexedColumns[i])];
          }
          // print('customerMasterMap: $customerMasterMap');
          batch.insert('customer_master', customerMasterMap);
        });
        // clear the table
        await localDB.delete('customer_master');
        await batch.commit(noResult: true);
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class CustomerMasterDB {
  String? customerCode;
  String? customerName;
  String? routeCode;
  String? empCode;
  String? currentBalance;
  String? creditLimit;
  String? isACEDNS;
  String? isBlackList;
  String? tradeDiscount;
  String? customerType;
  String? rdsTag;
  String? saudaValidityPeriod;
  String? address;
  String? pin;
  String? number;
  String? dnsCustCode;
  String? landlineNo;
  String? ownerName;
  String? ownerPhone;
  String? custClass;
  String? weeklyClosingDay;
  String? coverageType;
  String? tin;
  String? pan;
  String? minimumStock;
  String? branchCode;
  String? visitDay;
  String? email;
  String? saudaLimit;
  String? pendingQty;
  String? incoTerms;
  String? loadabilityTon;
  String? transportMode;
  String? state;
  String? saudaType;
  String? zone;
  String? visitSequence;
  String? activated;
  String? activatedCustomerCode;
  String? retailerApp;
  String? baseLatt;
  String? baseLongi;
  String? needLocationUpdate;

  CustomerMasterDB({
    this.customerCode,
    this.customerName,
    this.routeCode,
    this.empCode,
    this.currentBalance,
    this.creditLimit,
    this.isACEDNS,
    this.isBlackList,
    this.tradeDiscount,
    this.customerType,
    this.rdsTag,
    this.saudaValidityPeriod,
    this.address,
    this.pin,
    this.number,
    this.dnsCustCode,
    this.landlineNo,
    this.ownerName,
    this.ownerPhone,
    this.custClass,
    this.weeklyClosingDay,
    this.coverageType,
    this.tin,
    this.pan,
    this.minimumStock,
    this.branchCode,
    this.visitDay,
    this.email,
    this.saudaLimit,
    this.pendingQty,
    this.incoTerms,
    this.loadabilityTon,
    this.transportMode,
    this.state,
    this.saudaType,
    this.zone,
    this.visitSequence,
    this.activated,
    this.activatedCustomerCode,
    this.retailerApp,
    this.baseLatt,
    this.baseLongi,
    this.needLocationUpdate,
  });

  CustomerMasterDB.fromMap(Map<String, dynamic> map) {
    customerCode = map['customer_code'];
    customerName = map['customer_name'];
    routeCode = map['route_code'];
    empCode = map['emp_code'];
    currentBalance = map['current_balance'];
    creditLimit = map['credit_limit'];
    isACEDNS = map['acedns'];
    isBlackList = map['black_list'];
    tradeDiscount = map['TD'];
    customerType = map['cust_type'];
    rdsTag = map['rds_tag'];
    saudaValidityPeriod = map['sauda_validity_period'];
    address = map['address'];
    pin = map['pin'];
    number = map['phone_no'];
    dnsCustCode = map['dns_customer_code'];
    landlineNo = map['landline_no'];
    ownerName = map['owner_name'];
    ownerPhone = map['owner_phone'];
    custClass = map['cust_class'];
    weeklyClosingDay = map['weekly_closing_day'];
    coverageType = map['coverage_type'];
    tin = map['tin'];
    pan = map['pan'];
    minimumStock = map['minimum_stock'];
    branchCode = map['branch_code'];
    visitDay = map['visit_day'];
    email = map['email'];
    saudaLimit = map['sauda_limit'];
    pendingQty = map['pending_qty'];
    incoTerms = map['incoterms'];
    loadabilityTon = map['loadability_ton'];
    transportMode = map['transport_mode'];
    state = map['state_code'];
    saudaType = map['sauda_type'];
    zone = map['zone'];
    visitSequence = map['visit_sequence'];
    activated = map['activated'];
    activatedCustomerCode = map['activated_customer_code'];
    retailerApp = map['retailer_app'];
    baseLatt = map['base_latt'];
    baseLongi = map['base_longi'];
    needLocationUpdate = map['need_location_update'];
  }

  Map<String, dynamic> toMap() {
    return {
      'customer_code': customerCode,
      'customer_name': customerName,
      'route_code': routeCode,
      'emp_code': empCode,
      'current_balance': currentBalance,
      'credit_limit': creditLimit,
      'acedns': isACEDNS,
      'black_list': isBlackList,
      'TD': tradeDiscount,
      'cust_type': customerType,
      'rds_tag': rdsTag,
      'sauda_validity_period': saudaValidityPeriod,
      'address': address,
      'pin': pin,
      'phone_no': number,
      'dns_customer_code': dnsCustCode,
      'landline_no': landlineNo,
      'owner_name': ownerName,
      'owner_phone': ownerPhone,
      'cust_class': custClass,
      'branch_code': branchCode
    };
  }

  static Future<List<CustomerMasterDB>> getCustomerMasterDB(
      String routeCode, String custType) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> customerMasterList = (routeCode == '' &&
            custType == '')
        ? await localDB.rawQuery(
            // "SELECT * FROM customer_master WHERE cust_type LIKE 'Dealer' ORDER BY customer_name ASC LIMIT 100")
            "SELECT * FROM customer_master WHERE cust_type LIKE 'Dealer' ORDER BY customer_name ASC")
        : (custType == '')
            ? await localDB.query('customer_master',
                where: 'route_code = ?', whereArgs: [routeCode])
            : await localDB.rawQuery("SELECT * FROM customer_master WHERE route_code='$routeCode' AND cust_type IN ($custType) ORDER BY customer_name ASC");
    final List<CustomerMasterDB> customerMasterDB = [];
    for (int i = 0; i < customerMasterList.length; i++) {
      customerMasterDB.add(CustomerMasterDB.fromMap(customerMasterList[i]));
    }
    return customerMasterDB;
  }

  static Future<CustomerMasterDB> getCustomerMasterDBByCustomerCode(
      String customerCode) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> customerMasterList = await localDB.query(
        'customer_master',
        where: 'customer_code = ?',
        whereArgs: [customerCode]);
    final List<CustomerMasterDB> customerMasterDB = [];
    for (int i = 0; i < customerMasterList.length; i++) {
      customerMasterDB.add(CustomerMasterDB.fromMap(customerMasterList[i]));
    }
    return customerMasterDB.first;
  }
}
