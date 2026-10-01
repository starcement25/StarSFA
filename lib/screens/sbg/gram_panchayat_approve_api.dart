import 'dart:convert';

import 'package:http/http.dart' as http;

class GramPanchayatApproveApi {
  static Future<String> updateStatus({
    required String empCode,
    required String customerCode,
    required String status,
  }) async {
    final uri = Uri.https(
      'sfa.starcement.co.in',
      '/misreport/approve_gram_panchayat.php',
      {
        'customer_code': customerCode,
        'status': status,
        'emp_code': empCode,
      },
    );
    final response = await http.get(uri).timeout(const Duration(seconds: 30));
    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception('HTTP ${response.statusCode}');
    }

    final decoded = jsonDecode(response.body);
    if (decoded is! Map<String, dynamic>) {
      throw const FormatException('Invalid response from server');
    }
    final responseStatus = decoded['status']?.toString() ?? '';
    final message = decoded['message']?.toString() ?? '';
    if (responseStatus.toLowerCase() != 'success') {
      throw Exception(message.isEmpty ? 'Approval failed' : message);
    }
    return message.isEmpty ? 'Status updated successfully' : message;
  }
}
