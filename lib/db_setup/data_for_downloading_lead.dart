import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:path_provider/path_provider.dart';
import 'new_lead_generation_database.dart';

class DataForDownloadingLead {
  final NewLeadGenerationDatabase _db = NewLeadGenerationDatabase();
  String _typeOfUser = '';

  // ── Public entry point ─────────────────────────────────────────────────────

  Future<bool> addAllFormDataForLead({
    required String empCode,
    required String baseUrl,
  }) async {
    await _fetchEmployeeType(empCode: empCode, baseUrl: baseUrl);

    final soldOk =
        await _downloadSoldToPartyList(empCode: empCode, baseUrl: baseUrl);
    if (!soldOk) return false;

    final shipOk =
        await _downloadShipToPartyList(empCode: empCode, baseUrl: baseUrl);
    if (!shipOk) return false;

    return await _downloadLeadList(empCode: empCode, baseUrl: baseUrl);
  }

  // ── Employee type fetch ────────────────────────────────────────────────────

  Future<void> _fetchEmployeeType({
    required String empCode,
    required String baseUrl,
  }) async {
    try {
      final uri = Uri.parse('${baseUrl}api/employee/?emp_code=$empCode');
      final response = await http.get(uri).timeout(const Duration(seconds: 30));

      if (response.statusCode == 200) {
        final List<dynamic> arr = jsonDecode(response.body);
        if (arr.isNotEmpty) {
          final level = (arr[0]['level'] ?? '').toString().toLowerCase();
          if (level == 'nt_to') {
            _typeOfUser = 'HOS';
          } else if (level == 'nt') {
            _typeOfUser = 'SO';
          }
        }
      }
    } catch (e) {
      print('_fetchEmployeeType error: $e');
    }
  }

  // ── Download helper ────────────────────────────────────────────────────────

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
      print('_downloadTxt error: $e');
    }
    return null;
  }

  // ── Sold-to party ──────────────────────────────────────────────────────────

  Future<bool> _downloadSoldToPartyList({
    required String empCode,
    required String baseUrl,
  }) async {
    final url =
        '${baseUrl}api/ptblcustomermasterlist/?customer_type=sold&emp_code=$empCode';

    try {
      final file = await _downloadTxt(url, 'sold_to_party');
      if (file == null) return false;

      return await _parseAndInsertCustomerFile(file, 'sold_to_party');
    } catch (e) {
      print('_downloadSoldToPartyList error: $e');
      return false;
    }
  }

  // ── Ship-to party ──────────────────────────────────────────────────────────

  Future<bool> _downloadShipToPartyList({
    required String empCode,
    required String baseUrl,
  }) async {
    final url =
        '${baseUrl}api/ptblcustomermasterlist/?customer_type=ship&emp_code=$empCode';

    try {
      final file = await _downloadTxt(url, 'ship_to_party');
      if (file == null) return false;

      return await _parseAndInsertCustomerFile(file, 'ship_to_party');
    } catch (e) {
      print('_downloadShipToPartyList error: $e');
      return false;
    }
  }

  // ── Shared customer file parser ────────────────────────────────────────────

  Future<bool> _parseAndInsertCustomerFile(File file, String custType) async {
    int noColumn = -1;

    try {
      final lines = await file.readAsLines();

      for (final line in lines) {
        if (line.contains('¥')) {
          // Header line — extract expected column count
          final parts = line.split('¥');
          noColumn = int.tryParse(parts[1]) ?? -1;
        } else {
          final rowData = line.split('^');
          if (rowData.length == noColumn) {
            final temp = CustomerMasterTableDataSet(
              custCode: rowData[1],
              custName: rowData[4],
              phoneNo: rowData[20],
              district: rowData[14],
              state: rowData[11],
              address: rowData[9],
              custType: custType,
            );
            await _db.insertCustomerMasterTable(temp);
          }
        }
      }
      return true;
    } catch (e) {
      print('_parseAndInsertCustomerFile error: $e');
      return false;
    }
  }

  // ── Lead list ──────────────────────────────────────────────────────────────

  Future<bool> _downloadLeadList({
    required String empCode,
    required String baseUrl,
  }) async {
    final url =
        '${baseUrl}api/leadmaster/txt/?user_type=$_typeOfUser&login_username=$empCode';
    print('_downloadLeadList URL: $url');

    try {
      final file = await _downloadTxt(url, 'leadDetails');
      if (file == null) return false;

      int noColumn = -1;
      final lines = await file.readAsLines();

      for (final line in lines) {
        if (line.contains('¥')) {
          final parts = line.split('¥');
          noColumn = int.tryParse(parts[1]) ?? -1;
        } else {
          final rowData = line.split('^');
          if (rowData.length == noColumn) {
            final temp = LeadListMasterTableDataSet(
              leadGenerationId: rowData[0],
              empCode: rowData[1],
              latitude: rowData[2],
              longitude: rowData[3],
              leadType: rowData[4],
              partyName: rowData[5],
              branch: rowData[6],
              district: rowData[7],
              state: rowData[8],
              qtyReq: rowData[9],
              productPackaging: rowData[10],
              expRatePerBag: rowData[11],
              contactPersonName: rowData[12],
              designation: rowData[13],
              contactNumber: rowData[14],
              mailId: rowData[15],
              mode: rowData[16],
              quotation: rowData[17],
              po: rowData[18],
              status: rowData[19],
              remarks: rowData[20],
              assignedTo: rowData[21],
              selfOther: rowData[22],
              accBlockIsRequired: rowData[23],
              categoryTypeConstruction: rowData[24],
              nextVisitDate: rowData[25],
              leadStatus: rowData[26],
              currentBrandUsed: rowData[27],
              currentPrice: rowData[28],
              currentPriceCompetitor: rowData[29],
              rTiming: rowData[30],
              actionOnLead: rowData[31],
              approvedPrice: rowData[32],
              salesOrg: rowData[33],
              division: rowData[34],
              distributionChannel: rowData[35],
              documentType: rowData[36],
              customerReferenceNo: rowData[37],
              customerReferenceDate: rowData[38],
              validToDate: rowData[39],
              materialNumber: rowData[40],
              soldToParty: rowData[41],
              shipToParty: rowData[42],
              poMethod: rowData[43],
              shareLeadSiteDetailsPic: rowData[44],
              typeLead: rowData[45],
              leadRemarks: rowData[46],
              leadAction: rowData[47],
              creditTerms: rowData[48],
              monthQty: rowData[49],
              quotationProvided: rowData[50],
              quotationProvidedDate: rowData[51],
              misSubmissionDate: rowData[52],
              hosSubmissionDate: rowData[53],
              downloadTime: rowData[54],
              destination: rowData[55],
              companyConstraint: rowData[56],
              reason: rowData[57],
              nov: rowData[58],
              incoterms: rowData[59],
              servingLocation: rowData[60],
              quotedPrice: rowData[61],
              tpc: rowData[62],
              payment: rowData[63],
              lastPrice: rowData[64],
              prevLastPrice: rowData[65],
              leadQuotationStatus: rowData[66],
              lostOrderReason: rowData[67],
              quotationNumber: rowData[68],
              quotationPdf: rowData[69],
              poNumber: rowData[70],
              poDate: rowData[71],
              poImage: rowData[72],
              poRevertNote: rowData[73],
              poRevertLevel: rowData[74],
              poFowardNote: rowData[75],
              contractNumber: rowData[76],
              salesOrderNumber: rowData[77],
            );
            await _db.insertLeadListMasterTableData(temp);
          } else {
            print(
                'SKIPPED LINE — expected $noColumn got ${rowData.length}: $line');
          }
        }
      }
      return true;
    } catch (e) {
      print('_downloadLeadList error: $e');
      return false;
    }
  }
}
