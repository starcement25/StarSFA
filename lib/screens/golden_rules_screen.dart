import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_pdfview/flutter_pdfview.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';

class GoldenRulesScreen extends StatefulWidget {
  const GoldenRulesScreen({super.key});

  @override
  State<GoldenRulesScreen> createState() => _GoldenRulesScreenState();
}

class _GoldenRulesScreenState extends State<GoldenRulesScreen> {
  Future<Uint8List> getPDFDocument() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return Future.error('No internet connection');
    }
        final localDB = await LocalDB.openMyDatabase();
        final List<Map<String, dynamic>> goldenRulesData = await localDB.rawQuery('SELECT * FROM branchwise_goldenrule');
        final String grFileName = goldenRulesData[0]['gr_file_name'];
    // get the pdf data from the server
    final Uri url = Uri.parse(
        '${AppWebService.baseURL}/golden_rules/$grFileName');
    final http.Response response = await http.get(url);
    return response.bodyBytes;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Golden Rules'),
      ),
      // show a pdf file from - http://sfa.starcement.co.in//golden_rules/Golden_Rules_Card_WB.PDF
      body: FutureBuilder<Uint8List>(
          future: getPDFDocument(),
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(
                child: CircularProgressIndicator(),
              );
            }
            return PDFView(
              pdfData: snapshot.data,
            );
          }),
    );
  }
}
