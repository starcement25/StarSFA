import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:path_provider/path_provider.dart';
import 'package:starsfa/db_setup/sbg_database.dart' show SBGDatabase;
import 'package:starsfa/models/app_web_service.dart';

import '../db_setup/dataset/CustomerCompetitorQuantityModel.dart';

class DataForDownloading {
  final String baseUrl = AppWebService.baseURL; // replace with BaseUrl.baseUrl

  // ─────────────────────────────────────────────
  // Download txt file and save locally
  // ─────────────────────────────────────────────
  Future<File?> _downloadTxt(String url, String fileName) async {
    try {
      final dir = await getApplicationDocumentsDirectory();
      final file = File('${dir.path}/$fileName.txt');

      if (await file.exists()) await file.delete();

      final response = await http.get(Uri.parse(url));
      if (response.statusCode == 200) {
        await file.writeAsBytes(response.bodyBytes);
        return file;
      }
    } catch (e) {
      print('_DOWNLOAD_ downloadTxt error: $e');
    }
    return null;
  }

  // ─────────────────────────────────────────────
  // Download CompetitorQuantity → customer_competitor_quantity table
  // ─────────────────────────────────────────────
  Future<bool> downloadCompetitorQuantity(String empCode) async {
    final url = '${baseUrl}misreport/get_competitor_qty.php?emp_code=$empCode';
    print('_DOWNLOAD_ CompetitorQuantity: $url');

    try {
      final file = await _downloadTxt(url, 'CompetitorQuantity');
      if (file == null) return false;

      final lines = await file.readAsLines();
      int noColumn = -1;
      final List<CustomerCompetitorQuantityModel> rowList = [];

      for (final line in lines) {
        if (line.contains('¥')) {
          final parts = line.split('¥');
          noColumn = int.tryParse(parts[1].trim()) ?? -1;
        } else {
          final rowData = line.split('^');
          if (noColumn != -1 && rowData.length == noColumn) {
            rowList.add(CustomerCompetitorQuantityModel(
              competitorQuantityId: rowData[0],
              customerCode: rowData[1],
              customerName: rowData[2],
              mandatory: rowData[3],
              competitorName: rowData[4],
              type: rowData[5],
              quantity: rowData[6],
              customerDNS: rowData[7],
              flag: 0,
            ));
          }
        }
      }

      print('_DOWNLOAD_ CompetitorQuantity total rows: ${rowList.length}');

      // Use compute() to move chunked insert off the main isolate
      await SBGDatabase.instance.insertCustomerCompetitorQuantityBatch(rowList);

      print('_DOWNLOAD_ CompetitorQuantity batch insert done');
      return true;
    } catch (e) {
      print('_DOWNLOAD_ CompetitorQuantity error: $e');
      return false;
    }
  }

  // ─────────────────────────────────────────────
  // Run both downloads together
  // ─────────────────────────────────────────────
  Future<bool> downloadAllSBGData(String empCode) async {
    final competitorResult = await downloadCompetitorQuantity(empCode);
    if (!competitorResult) {
      print('_DOWNLOAD_ CompetitorQuantity failed');
      return false;
    }

    // Only download sbg_feedback from server if your API supports it
    // final feedbackResult = await downloadSbgFeedback(empCode);
    // if (!feedbackResult) return false;

    return true;
  }
}
