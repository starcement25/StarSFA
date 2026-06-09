import 'dart:convert';
import 'package:http/http.dart' as http;

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class SiteLeadDownloadApprovalClass {
  String? surveyId;
  String? customerName;
  String? contactNo;
  String? address;
  String? dealerName;
  String? dealerCode;
  String? visitType;
  String? product;
  String? noOfBag;
  String? requestDate;
  String? status;
  String? actualDeliveryDate;
  String? remarks;
  String? reasonForRejection;

  SiteLeadDownloadApprovalClass({
    this.surveyId,
    this.customerName,
    this.contactNo,
    this.address,
    this.dealerName,
    this.dealerCode,
    this.visitType,
    this.product,
    this.noOfBag,
    this.requestDate,
    this.status,
    this.actualDeliveryDate,
    this.remarks,
    this.reasonForRejection,
  });

  SiteLeadDownloadApprovalClass.fromJson(Map<String, dynamic> json) {
    surveyId = json['survey_id'];
    customerName = json['customer_name'];
    contactNo = json['contact_no'];
    address = json['address'];
    dealerName = json['dealer_name'];
    dealerCode = json['dealer_code'];
    visitType = json['visit_type'];
    product = json['product'];
    noOfBag = json['no_of_bag'];
    requestDate = json['request_date'];
    status = json['status'];
    actualDeliveryDate = json['actual_delivery_date'];
    remarks = json['remarks'];
    reasonForRejection = json['reason_for_rejection'];
  }

  // from txt file
  static List<SiteLeadDownloadApprovalClass> fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> masterData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      masterData.add(temp);
    }
    final List<SiteLeadDownloadApprovalClass> list =
        <SiteLeadDownloadApprovalClass>[];
    for (int i = 0; i < masterData.length; i++) {
      final SiteLeadDownloadApprovalClass siteLeadDownloadApprovalClass =
          SiteLeadDownloadApprovalClass(
        surveyId: masterData[i][0],
        customerName: masterData[i][1],
        contactNo: masterData[i][2],
        address: masterData[i][3],
        dealerName: masterData[i][4],
        dealerCode: masterData[i][5],
        product: masterData[i][6],
        noOfBag: masterData[i][7],
        requestDate: masterData[i][8],
        visitType: masterData[i][9],
        status: masterData[i][10],
        actualDeliveryDate: masterData[i][11],
        remarks: masterData[i][12],
        reasonForRejection: masterData[i][13],
      );
      list.add(siteLeadDownloadApprovalClass);
    }
    return list;
  }

  static Future<List<SiteLeadDownloadApprovalClass>>
      getSiteLeadDownloadApproval(
          String empCode, String from, String to) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return <SiteLeadDownloadApprovalClass>[];
    }
    String url =
        "${AppWebService.siteLeadDownloadApproval}?emp_code=$empCode&nick_name=${AppWebService.nickname}&from=$from&to=$to";
    print('URL: $url');
    final response = await http.get(Uri.parse(url));
    print('Response: ${response.body}');
    if (response.statusCode == 200) {
      final data = (response.body);
      return SiteLeadDownloadApprovalClass.fromTXT(data);
    } else {
      throw Exception('Failed to load data');
    }
  }

  static Future<String> updateSiteLeadApproval(
      String surveyId,
      String status,
      String actualDeliveryDate,
      String remarks,
      String reasonForRejection) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return 'No Internet Connection';
    }
    final user = await UserLoginClass.getLocalUser();
    final String empCode = user?.empCode ?? '';
    String url =
        "${AppWebService.siteLeadApprovalUpdate}?emp_code=$empCode&nick_name=${AppWebService.nickname}&survey_id=$surveyId&status=$status";
    print('URL: $url');
    final response = await http.post(Uri.parse(url), body: {
      'nick_name': AppWebService.nickname,
      'status': status,
      'survey_id': surveyId,
      'actual_date_delivery': actualDeliveryDate,
      'delivery_remarks': remarks,
      'reason_not_delivery': reasonForRejection,
    });
    print('Response: ${response.body}');
    if (response.statusCode == 200) {
      return response.body;
    } else {
      throw Exception('Failed to update data');
    }
  }
}
