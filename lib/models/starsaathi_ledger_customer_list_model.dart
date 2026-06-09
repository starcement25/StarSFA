import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class StarsaathiLedgerCustomerList {
  String? customerName;
  String? customerCode;
  String? dnsCustomerCode;

  StarsaathiLedgerCustomerList({
    this.customerName,
    this.customerCode,
    this.dnsCustomerCode,
  });

  factory StarsaathiLedgerCustomerList.fromJson(Map<String, dynamic> json) {
    return StarsaathiLedgerCustomerList(
      customerName: json['customer_name'],
      customerCode: json['customer_code'],
      dnsCustomerCode: json['dns_customer_code'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'customer_name': customerName,
      'customer_code': customerCode,
      'dns_customer_code': dnsCustomerCode,
    };
  }

  static Future<List<StarsaathiLedgerCustomerList>> getCustomerList() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }
    // get the user details
    final userDetails = await UserLoginClass.getLocalUser();
    final response = await http.get(
      Uri.parse(
          '${AppWebService.starsaathiLedgerCustomerList}?emp_code=${userDetails?.empCode}&nick_name=${AppWebService.nickname}'),
    );
    if (response.statusCode == 200) {
      print(
          '${AppWebService.starsaathiLedgerCustomerList}?emp_code=${userDetails?.empCode}&nick_name=${AppWebService.nickname}');
      print('${response.statusCode} and the value is ${response.body}');
      final List<dynamic> customerList =
          json.decode(response.body)['customer_data'];
      return customerList
          .map((json) => StarsaathiLedgerCustomerList.fromJson(json))
          .toList();
    } else {
      throw Exception('Failed to load customer list');
    }
  }
}

class StarsaathiLedgerbyId {
  String? customerCode;
  String? dnsCustomerCode;
  String? theCustomerId;
  String? balance;
  String? date;
  String? link;
  String? creditLimit;
  String? creditExpose;
  String? name1;
  List<LedgerData>? ledgerData;

  StarsaathiLedgerbyId({
    this.customerCode,
    this.dnsCustomerCode,
    this.theCustomerId,
    this.balance,
    this.date,
    this.link,
    this.creditLimit,
    this.creditExpose,
    this.name1,
    this.ledgerData,
  });

  factory StarsaathiLedgerbyId.fromJson(Map<String, dynamic> json) {
    return StarsaathiLedgerbyId(
      customerCode: json['customer_code'],
      dnsCustomerCode: json['dns_customer_code'],
      theCustomerId: json['the_customer_id'],
      balance: json['balance'],
      date: json['date'],
      link: json['link'],
      creditLimit: json['credit_limit'],
      creditExpose: json['credit_expose'],
      name1: json['name1'],
      ledgerData: json['ledger_data']
          .map<LedgerData>((json) => LedgerData.fromJson(json))
          .toList(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'customer_code': customerCode,
      'dns_customer_code': dnsCustomerCode,
      'the_customer_id': theCustomerId,
      'balance': balance,
      'date': date,
      'link': link,
      'credit_limit': creditLimit,
      'credit_expose': creditExpose,
      'name1': name1,
      'ledger_data': ledgerData?.map((e) => e.toJson()).toList(),
    };
  }

  static Future<StarsaathiLedgerbyId> getCustomerById(
      String dNScustomerCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return StarsaathiLedgerbyId();
    }
    final response = await http.get(
      Uri.parse(
          '${AppWebService.starsaathiLedgerbyId}?the_id=$dNScustomerCode'),
    );
    if (response.statusCode == 200) {
      StarsaathiLedgerbyId ledgerbyId = StarsaathiLedgerbyId();
      final Map<String, dynamic> ledgerBalanceData =
          json.decode(response.body)['ledger_balance_data'];
      final List<dynamic>? ledgerData =
          json.decode(response.body)['ledger_data'];
      ledgerbyId.customerCode = ledgerBalanceData['customer_code'];
      ledgerbyId.dnsCustomerCode = ledgerBalanceData['dns_customer_code'];
      ledgerbyId.theCustomerId = ledgerBalanceData['the_customer_id'];
      ledgerbyId.balance = ledgerBalanceData['balance'];
      ledgerbyId.date = ledgerBalanceData['date'];
      ledgerbyId.link = ledgerBalanceData['link'];
      ledgerbyId.creditLimit = ledgerBalanceData['credit_limit'];
      ledgerbyId.creditExpose = ledgerBalanceData['credit_expose'];
      ledgerbyId.name1 = ledgerBalanceData['name1'];
      ledgerbyId.ledgerData = ledgerData
          ?.map<LedgerData>((json) => LedgerData.fromJson(json))
          .toList();
      return ledgerbyId;
    } else {
      throw Exception('Failed to load customer list');
    }
  }
}

class LedgerData {
  String? customerCode;
  String? dnsCustomerCode;
  String? voucherDate;
  String? voucherNo;
  String? quantity;
  String? amountDr;
  String? amountCr;
  String? balance;
  String? narration;
  String? entryDate;
  String? convertedVoucherDate;
  String? companyCode;

  LedgerData({
    this.customerCode,
    this.dnsCustomerCode,
    this.voucherDate,
    this.voucherNo,
    this.quantity,
    this.amountDr,
    this.amountCr,
    this.balance,
    this.narration,
    this.entryDate,
    this.convertedVoucherDate,
    this.companyCode,
  });

  factory LedgerData.fromJson(Map<String, dynamic> json) {
    return LedgerData(
      customerCode: json['customer_code'],
      dnsCustomerCode: json['dns_customer_code'],
      voucherDate: json['voucher_date'],
      voucherNo: json['voucher_no'],
      quantity: json['quantity'],
      amountDr: json['amount_dr'],
      amountCr: json['amount_cr'],
      balance: json['balance'],
      narration: json['narration'],
      entryDate: json['entry_date'],
      convertedVoucherDate: json['converted_voucher_date'],
      companyCode: json['company_code'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'customer_code': customerCode,
      'dns_customer_code': dnsCustomerCode,
      'voucher_date': voucherDate,
      'voucher_no': voucherNo,
      'quantity': quantity,
      'amount_dr': amountDr,
      'amount_cr': amountCr,
      'balance': balance,
      'narration': narration,
      'entry_date': entryDate,
      'converted_voucher_date': convertedVoucherDate,
      'company_code': companyCode,
    };
  }
}
