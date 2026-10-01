import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';

class IssueHistoryData {
  String? id;
  String? empCode;
  String? empName;
  String? branchCode;
  String? branchName;
  String? customerCode;
  String? customerName;
  String? category;
  String? state;
  String? district;
  String? escalatedDepartment; // maps to esc_dept
  String? issue;
  String? remarks;
  String? status;
  String? statusChangeDate; // maps to status_cnange_date
  String? createdAt;

  IssueHistoryData({
    this.id,
    this.empCode,
    this.empName,
    this.branchCode,
    this.branchName,
    this.customerCode,
    this.customerName,
    this.category,
    this.state,
    this.district,
    this.escalatedDepartment,
    this.issue,
    this.remarks,
    this.status,
    this.statusChangeDate,
    this.createdAt,
  });

  factory IssueHistoryData.fromJson(Map<String, dynamic> json) {
    return IssueHistoryData(
      id: json['id']?.toString(),
      empCode: json['emp_code']?.toString(),
      empName: json['emp_name']?.toString(),
      branchCode: json['branch_code']?.toString(),
      branchName: json['branch_name']?.toString(),
      customerCode: json['customer_code']?.toString(),
      customerName: json['customer_name']?.toString(),
      category: json['category']?.toString(),
      state: json['state']?.toString(),
      district: json['district']?.toString(),
      escalatedDepartment: json['esc_dept']?.toString(),
      issue: json['issue']?.toString(),
      remarks: json['remarks']?.toString(),
      status: json['status']?.toString(),
      statusChangeDate: json['status_cnange_date']?.toString(),
      createdAt: json['created_at']?.toString(),
    );
  }

  static Future<List<IssueHistoryData>> getIssueHistoryList(
      String empCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }

    final String url =
        "${AppWebService.baseURL}misreport/api_raise_issue_list.php?emp_code=$empCode";
    final response = await http.get(Uri.parse(url));

    if (response.statusCode == 200) {
      final decoded = json.decode(response.body);
      if (decoded['process_status']?.toString().toLowerCase() == 'yes') {
        final List<dynamic> data = decoded['data'] ?? [];
        return data.map((json) => IssueHistoryData.fromJson(json)).toList();
      } else {
        return [];
      }
    } else {
      throw Exception('Failed to load issue history');
    }
  }

  static Future<String> updateIssueStatus({
    required String issueId,
    required String status,
  }) async {
    final isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) throw Exception('Network Failure');

    final response = await http.post(
      Uri.parse(
        '${AppWebService.baseURL}misreport/api_raise_issue_status_change.php',
      ),
      body: {'id': issueId, 'status': status},
    );
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception('HTTP ${response.statusCode}');
    }

    final decoded = jsonDecode(response.body);
    if (decoded is! Map<String, dynamic>) {
      throw const FormatException('Invalid response from server');
    }
    final message = decoded['process_message']?.toString() ?? '';
    if (decoded['process_status']?.toString().toLowerCase() != 'yes') {
      throw Exception(message.isEmpty ? 'Failed to update status.' : message);
    }
    return message.isEmpty ? 'Status updated successfully.' : message;
  }
}
