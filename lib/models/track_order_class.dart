import 'dart:convert';

import 'package:starsfa/models/app_web_service.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/network_service.dart';

class TrackOrderClass {
  String? apporderno;
  String? erporderno;
  String? erporderdt;
  String? isdoimp;
  String? orderFor;
  String? customerCode;
  String? dnsCustomerCode;
  String? status;
  String? prodCode;
  String? dnsProdCode;
  String? prodDisplayName;
  String? qty;
  String? orderFullDateTime;
  String? destination;
  String? freight;
  String? plantName;
  List<OrderChallanData>? orderChallanData;

  TrackOrderClass(
      {this.apporderno,
      this.erporderno,
      this.erporderdt,
      this.isdoimp,
      this.orderFor,
      this.customerCode,
      this.dnsCustomerCode,
      this.status,
      this.prodCode,
      this.dnsProdCode,
      this.prodDisplayName,
      this.qty,
      this.orderFullDateTime,
      this.destination,
      this.freight,
      this.plantName,
      this.orderChallanData});

  TrackOrderClass.fromJson(Map<String?, dynamic> json) {
    apporderno = json['apporderno'];
    erporderno = json['erporderno'];
    erporderdt = json['erporderdt'];
    isdoimp = json['isdoimp'];
    orderFor = json['order_for'];
    customerCode = json['customer_code'];
    dnsCustomerCode = json['dns_customer_code'];
    status = json['status'];
    prodCode = json['prod_code'];
    dnsProdCode = json['dns_prod_code'];
    prodDisplayName = json['prod_display_name'];
    qty = json['qty'];
    destination = json['destination_address'] ?? '';
    freight = json['freight'] ?? '';
    plantName = json['plant_name'] ?? '';
    orderFullDateTime = json['order_full_date_time'];
    if (json['order_challan_data'] != null) {
      orderChallanData = <OrderChallanData>[];
      json['order_challan_data'].forEach((v) {
        orderChallanData!.add(OrderChallanData.fromJson(v));
      });
    }
  }

  Map<String?, dynamic> toJson() {
    final Map<String?, dynamic> data = <String?, dynamic>{};
    data['apporderno'] = apporderno;
    data['erporderno'] = erporderno;
    data['erporderdt'] = erporderdt;
    data['isdoimp'] = isdoimp;
    data['order_for'] = orderFor;
    data['customer_code'] = customerCode;
    data['dns_customer_code'] = dnsCustomerCode;
    data['status'] = status;
    data['prod_code'] = prodCode;
    data['dns_prod_code'] = dnsProdCode;
    data['prod_display_name'] = prodDisplayName;
    data['qty'] = qty;
    data['destination'] = destination;
    data['freight'] = freight;
    data['plantName'] = plantName;
    data['order_full_date_time'] = orderFullDateTime;
    if (orderChallanData != null) {
      data['order_challan_data'] =
          orderChallanData!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class OrderChallanData {
  String? apporderno;
  String? erporderno;
  String? erporderdt;
  String? challanno;
  String? challandt;
  String? prodCode;
  String? dnsProdCode;
  String? prodDisplayName;
  String? qty;
  String? challanqty;
  String? customerCode;
  String? dnsCustomerCode;
  String? truckno;
  String? driverno;
  String? ischlnimp;

  OrderChallanData(
      {this.apporderno,
      this.erporderno,
      this.erporderdt,
      this.challanno,
      this.challandt,
      this.prodCode,
      this.dnsProdCode,
      this.prodDisplayName,
      this.qty,
      this.challanqty,
      this.customerCode,
      this.dnsCustomerCode,
      this.truckno,
      this.driverno,
      this.ischlnimp});

  OrderChallanData.fromJson(Map<String?, dynamic> json) {
    apporderno = json['apporderno'];
    erporderno = json['erporderno'];
    erporderdt = json['erporderdt'];
    challanno = json['invno'];
    challandt = json['invdt'];
    prodCode = json['prod_code'];
    dnsProdCode = json['dns_prod_code'];
    prodDisplayName = json['prod_display_name'];
    qty = json['qty'];
    challanqty = json['invqty'];
    customerCode = json['customer_code'];
    dnsCustomerCode = json['dns_customer_code'];
    truckno = json['truckno'];
    driverno = json['driverno'];
    ischlnimp = json['ischlnimp'];
  }

  Map<String?, dynamic> toJson() {
    final Map<String?, dynamic> data = <String?, dynamic>{};
    data['apporderno'] = apporderno;
    data['erporderno'] = erporderno;
    data['erporderdt'] = erporderdt;
    data['challanno'] = challanno;
    data['challandt'] = challandt;
    data['prod_code'] = prodCode;
    data['dns_prod_code'] = dnsProdCode;
    data['prod_display_name'] = prodDisplayName;
    data['qty'] = qty;
    data['challanqty'] = challanqty;
    data['customer_code'] = customerCode;
    data['dns_customer_code'] = dnsCustomerCode;
    data['truckno'] = truckno;
    data['driverno'] = driverno;
    data['ischlnimp'] = ischlnimp;
    return data;
  }
}

class TrackOrderResponse {
  String? status;
  String? message;
  List<TrackOrderClass>? orderData;

  TrackOrderResponse({this.status, this.message, this.orderData});

  TrackOrderResponse.fromJson(Map<String?, dynamic> json) {
    status = json['process_status'];
    message = json['process_message'];
    if (json['order_data'] != null) {
      orderData = <TrackOrderClass>[];
      json['order_data'].forEach((v) {
        orderData!.add(TrackOrderClass.fromJson(v));
      });
    }
  }

  Map<String?, dynamic> toJson() {
    final Map<String?, dynamic> data = <String?, dynamic>{};
    data['process_status'] = status;
    data['process_message'] = message;
    if (orderData != null) {
      data['order_data'] = orderData!.map((v) => v.toJson()).toList();
    }
    return data;
  }

  // get data by dnsCustomerCode
  static Future<TrackOrderResponse> getTrackOrderDataByDnsCustomerCode(
      String dnsCustomerCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return TrackOrderResponse();
    }
    final url =
        Uri.parse('${AppWebService.trackOrderURL}?the_id=$dnsCustomerCode');
    final response = await http.get(url);
    // ignore: avoid_print
    print('URL: $url');
    // ignore: avoid_print
    print('Response: ${response.body}');
    if (response.statusCode == 200) {
      return TrackOrderResponse.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Failed to load data');
    }
  }
}
