import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'package:mime/mime.dart';
import 'package:path_provider/path_provider.dart';
import 'package:share_plus/share_plus.dart';
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/user_login_class.dart';

/// ---------------- MODEL ----------------
class OcrItem {
  String slNo;
  String name;
  String address;
  String phoneNo;
  String ilpRegistered; // normalized to "Yes" / "No" / ""

  OcrItem({
    this.slNo = '',
    this.name = '',
    this.address = '',
    this.phoneNo = '',
    this.ilpRegistered = '',
  });
}

/// Normalizes OCR / free-text ILP values ("YES", " yes ", "No ", "n", "y", etc.)
/// into the canonical "Yes" / "No" / "" used by the radio group.
String normalizeIlpValue(String raw) {
  final v = raw.trim().toLowerCase();
  if (v == 'yes' || v == 'y') return 'Yes';
  if (v == 'no' || v == 'n') return 'No';
  return '';
}

/// ---------------- SCREEN ----------------
class ShowOcrDataAndSubmitScreen extends StatefulWidget {
  final String dealerSubDealerName;
  final String address;
  final String areaLocation;
  final String techMeetType;
  final String date;
  final String branchCode;
  final String customerCode;

  /// Image files to run OCR on, in order.
  final List<File> imageFiles;

  const ShowOcrDataAndSubmitScreen(
      {super.key,
      required this.dealerSubDealerName,
      required this.address,
      required this.areaLocation,
      required this.techMeetType,
      required this.date,
      required this.imageFiles,
      required this.branchCode,
      required this.customerCode});

  @override
  State<ShowOcrDataAndSubmitScreen> createState() =>
      _ShowOcrDataAndSubmitScreenState();
}

class _ShowOcrDataAndSubmitScreenState
    extends State<ShowOcrDataAndSubmitScreen> {
  static const String ocrUrl =
      'https://starocr.myvtd.site/extract-structured/?engine=aws';

  final List<OcrItem> list = [];
  final List<TextEditingController> nameControllers = [];
  final List<TextEditingController> addressControllers = [];
  final List<TextEditingController> phoneControllers = [];

  // Needed to anchor the iOS/iPadOS share sheet popover to the download button.
  final GlobalKey _downloadButtonKey = GlobalKey();

  bool isProcessing = false;
  String progressText = '';
  int runningSlNo = 1;

  @override
  void initState() {
    super.initState();
    _runOcrPipeline();
  }

  @override
  void dispose() {
    for (final c in nameControllers) {
      c.dispose();
    }
    for (final c in addressControllers) {
      c.dispose();
    }
    for (final c in phoneControllers) {
      c.dispose();
    }
    super.dispose();
  }

  // ---------------- OCR PIPELINE ----------------

  Future<void> _runOcrPipeline() async {
    if (widget.imageFiles.isEmpty) {
      _toast('No image found to process');
      return;
    }

    setState(() {
      isProcessing = true;
      list.clear();
      runningSlNo = 1;
    });

    for (int i = 0; i < widget.imageFiles.length; i++) {
      setState(() {
        progressText =
            'Please wait... OCR is processing image ${i + 1} of ${widget.imageFiles.length}...';
      });

      try {
        final data = await _uploadAndExtract(widget.imageFiles[i]);
        _appendOcrResponse(data);
      } catch (e) {
        _toast('OCR failed on image ${i + 1}: $e');
      }
    }

    setState(() {
      isProcessing = false;
      progressText = '';
    });
  }

  Future<Map<String, dynamic>> _uploadAndExtract(File file) async {
    final mimeType = lookupMimeType(file.path) ?? 'image/jpeg';
    final parts = mimeType.split('/');

    final request = http.MultipartRequest('POST', Uri.parse(ocrUrl))
      ..headers['accept'] = 'application/json'
      ..files.add(
        await http.MultipartFile.fromPath(
          'file',
          file.path,
          contentType:
              MediaType(parts[0], parts.length > 1 ? parts[1] : 'jpeg'),
        ),
      );

    final streamedResponse =
        await request.send().timeout(const Duration(seconds: 120));
    final response = await http.Response.fromStream(streamedResponse);

    if (response.statusCode < 200 || response.statusCode >= 300) {
      throw Exception(
          'Server error: ${response.statusCode} - ${response.body}');
    }

    try {
      return jsonDecode(response.body) as Map<String, dynamic>;
    } catch (_) {
      throw Exception('Invalid JSON response');
    }
  }

  void _appendOcrResponse(Map<String, dynamic> data) {
    final participants = data['participants'];
    if (participants is! List) return;

    setState(() {
      for (final raw in participants) {
        if (raw is! Map) continue;
        final name = (raw['name'] ?? '').toString();
        final contactNo = (raw['contact_no'] ?? '').toString();
        final address = (raw['address'] ?? '').toString();
        // OCR can return "YES", " No ", "y", mixed case, stray whitespace — normalize it
        // so it actually matches a radio option instead of showing neither selected.
        final ilp = normalizeIlpValue((raw['ilpregister'] ?? '').toString());

        if (name.isNotEmpty ||
            contactNo.isNotEmpty ||
            address.isNotEmpty ||
            ilp.isNotEmpty) {
          list.add(OcrItem(
            slNo: '$runningSlNo',
            name: name,
            phoneNo: contactNo,
            address: address,
            ilpRegistered: ilp,
          ));
          nameControllers.add(TextEditingController(text: name));
          addressControllers.add(TextEditingController(text: address));
          phoneControllers.add(TextEditingController(text: contactNo));
          runningSlNo++;
        }
      }
    });
  }

  // ---------------- LIST OPS ----------------

  void _addNewItem() {
    setState(() {
      list.add(OcrItem());
      nameControllers.add(TextEditingController());
      addressControllers.add(TextEditingController());
      phoneControllers.add(TextEditingController());
      _renumberList();
    });
  }

  void _deleteItem(int index) {
    setState(() {
      list.removeAt(index);
      nameControllers.removeAt(index).dispose();
      addressControllers.removeAt(index).dispose();
      phoneControllers.removeAt(index).dispose();
      _renumberList();
    });
  }

  void _renumberList() {
    for (int i = 0; i < list.length; i++) {
      list[i].slNo = '${i + 1}';
    }
  }

  // ---------------- SUBMIT ----------------

  Future<void> _submitOcrData() async {
    final validItems = <OcrItem>[];
    for (final item in list) {
      if (item.name.trim().isEmpty || item.phoneNo.trim().isEmpty) {
        _toast("Sl No ${item.slNo}: name/phone can't be empty");
        return;
      }
      validItems.add(item);
    }
    final user = await UserLoginClass.getLocalUser();
    final payload = {
      'participants': validItems
          .map((item) => {
                'date': widget.date,
                'participant_name': item.name.trim(),
                'contact_no': item.phoneNo.trim(),
                'address': item.address.trim(),
                'ilp_registered': item.ilpRegistered.trim(),
              })
          .toList(),
      'date': widget.date,
      'tech_meet_type': widget.techMeetType,
      'address': widget.address,
      'branch_code': widget.branchCode,
      'dealer_code': widget.customerCode,
      'employee_code': user?.empCode,
    };

    // TODO: send `payload` to your submit API
    print(jsonEncode(payload));

    final response = await http.post(
      Uri.parse('${AppWebService.baseURL}misreport/api_store_ocr_data.php'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode(payload),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      _toast('Successfully update in server.');
      final navigator = Navigator.of(context);
      navigator.pop();
      navigator.pop();
    } else {
      _toast('Failed to submit data');
    }
  }

  // ---------------- CSV EXPORT ----------------

  Future<void> _exportListToCsv() async {
    if (list.isEmpty) {
      _toast('No data to export');
      return;
    }

    final csv = StringBuffer();
    csv.write('List of Participants\n\n');
    csv.write(
        'Name of Dealer / Sub Dealer,${_csvEscape(widget.dealerSubDealerName)}\n');
    csv.write('Address,${_csvEscape(widget.address)}\n');
    csv.write('Area / Location,${_csvEscape(widget.areaLocation)}\n');
    csv.write('Tech Meet type,${_csvEscape(widget.techMeetType)}\n');
    csv.write('Date,${_csvEscape(widget.date)}\n\n');

    csv.write(
        'Sl. No.,Name of Participants,Address,Contact No,ILP registered (Yes/No),Signature\n');
    for (final item in list) {
      csv.write('${_csvEscape(item.slNo)},');
      csv.write('${_csvEscape(item.name)},');
      csv.write('${_csvEscape(item.address)},');
      csv.write('${_csvEscape(item.phoneNo)},');
      csv.write('${_csvEscape(item.ilpRegistered)},');
      csv.write('\n'); // Signature left blank
    }

    final fileName =
        'Participants_${DateTime.now().toIso8601String().replaceAll(RegExp(r'[:.]'), '')}.csv';

    try {
      final dir = await getApplicationDocumentsDirectory();
      final file = File('${dir.path}/$fileName');
      await file.writeAsString(csv.toString());

      // iOS/iPadOS presents the share sheet as a popover and requires an
      // anchor rect (sharePositionOrigin) — without it you get the
      // "sharePositionOrigin: argument must be set" PlatformException.
      Rect? sharePositionOrigin;
      final box =
          _downloadButtonKey.currentContext?.findRenderObject() as RenderBox?;
      if (box != null && box.hasSize) {
        sharePositionOrigin = box.localToGlobal(Offset.zero) & box.size;
      }

      await Share.shareXFiles(
        [XFile(file.path)],
        text: 'Participants CSV',
        sharePositionOrigin: sharePositionOrigin,
      );
    } catch (e) {
      print(e.toString());
      _toast('Failed to save CSV: $e');
    }
  }

  String _csvEscape(String? value) {
    if (value == null) return '';
    if (value.contains(',') || value.contains('"') || value.contains('\n')) {
      return '"${value.replaceAll('"', '""')}"';
    }
    return value;
  }

  void _toast(String message) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), duration: const Duration(seconds: 2)),
    );
  }

  // ---------------- UI ----------------

  Widget _infoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 2),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 1,
            child: Text(label,
                style: const TextStyle(fontSize: 11, color: Color(0xFF666666))),
          ),
          const Text(': ', style: TextStyle(fontSize: 11, color: Colors.black)),
          Expanded(
            flex: 1,
            child: Text(value,
                style: const TextStyle(fontSize: 11, color: Color(0xFF333333))),
          ),
        ],
      ),
    );
  }

  Widget _headerInfoBlock() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'List of Participants',
          textAlign: TextAlign.center,
          style: TextStyle(
              fontSize: 16, fontWeight: FontWeight.w700, color: Colors.black),
        ),
        const SizedBox(height: 10),
        _infoRow('Name of Dealer / Sub Dealer', widget.dealerSubDealerName),
        _infoRow('Address', widget.address),
        _infoRow('Area / Location', widget.areaLocation),
        _infoRow('Tech Meet Type', widget.techMeetType),
        _infoRow('Date', widget.date),
        const SizedBox(height: 10),
      ],
    );
  }

  Widget _headerRow() {
    return Column(
      children: [
        Container(height: 1, color: Colors.black),
        SizedBox(
          height: 40,
          child: Row(
            children: [
              Container(width: 1, color: Colors.black),
              const _HeaderCell(text: 'Sl. No.', width: 50),
              Container(width: 1, color: Colors.black),
              const _HeaderCell(text: 'Name of Participants', width: 150),
              Container(width: 1, color: Colors.black),
              const _HeaderCell(text: 'Address', width: 175),
              Container(width: 1, color: Colors.black),
              const _HeaderCell(text: 'Contact No.', width: 150),
              Container(width: 1, color: Colors.black),
              const _HeaderCell(text: 'ILP registered (Yes/No)', width: 120),
              Container(width: 1, color: Colors.black),
              const _HeaderCell(text: '', width: 50),
              Container(width: 1, color: Colors.black),
            ],
          ),
        ),
        Container(height: 1, color: Colors.black),
      ],
    );
  }

  /// Compact Yes/No selector — plain Radio + tappable label, no
  /// RadioListTile padding, so it doesn't blow out the column width.
  Widget _ilpSelector(OcrItem item) {
    Widget option(String value) {
      final selected = item.ilpRegistered == value;
      return Expanded(
        child: InkWell(
          onTap: () => setState(() => item.ilpRegistered = value),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            mainAxisSize: MainAxisSize.min,
            children: [
              SizedBox(
                width: 20,
                height: 20,
                child: Radio<String>(
                  value: value,
                  groupValue:
                      item.ilpRegistered.isEmpty ? null : item.ilpRegistered,
                  onChanged: (val) =>
                      setState(() => item.ilpRegistered = val ?? ''),
                  materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  visualDensity: VisualDensity.compact,
                ),
              ),
              const SizedBox(width: 2),
              Text(value,
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: selected ? FontWeight.w600 : FontWeight.normal,
                  )),
            ],
          ),
        ),
      );
    }

    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [option('Yes'), option('No')],
    );
  }

  Widget _dataRow(int index) {
    final item = list[index];
    return Column(
      children: [
        SizedBox(
          height: 44,
          child: Row(
            children: [
              Container(width: 1, color: const Color(0xFF999999)),
              SizedBox(
                width: 50,
                child: Center(
                  child: Text(item.slNo,
                      style: const TextStyle(
                          fontSize: 12, color: Color(0xFF333333))),
                ),
              ),
              Container(width: 1, color: const Color(0xFF999999)),
              SizedBox(
                width: 150,
                child: TextFormField(
                  controller: nameControllers[index],
                  style:
                      const TextStyle(fontSize: 12, color: Color(0xFF333333)),
                  decoration: const InputDecoration(
                    border: InputBorder.none,
                    isDense: true,
                    hintText: 'Participant Name',
                    contentPadding:
                        EdgeInsets.symmetric(horizontal: 4, vertical: 8),
                  ),
                  onChanged: (val) => item.name = val,
                ),
              ),
              Container(width: 1, color: const Color(0xFF999999)),
              SizedBox(
                width: 175,
                child: TextFormField(
                  controller: addressControllers[index],
                  style:
                      const TextStyle(fontSize: 12, color: Color(0xFF333333)),
                  decoration: const InputDecoration(
                    border: InputBorder.none,
                    isDense: true,
                    hintText: 'Address',
                    contentPadding:
                        EdgeInsets.symmetric(horizontal: 4, vertical: 8),
                  ),
                  onChanged: (val) => item.address = val,
                ),
              ),
              Container(width: 1, color: const Color(0xFF999999)),
              SizedBox(
                width: 150,
                child: TextFormField(
                  controller: phoneControllers[index],
                  keyboardType: TextInputType.number,
                  maxLength: 10,
                  inputFormatters: [FilteringTextInputFormatter.digitsOnly],
                  style:
                      const TextStyle(fontSize: 12, color: Color(0xFF333333)),
                  decoration: const InputDecoration(
                    border: InputBorder.none,
                    isDense: true,
                    counterText: '',
                    hintText: 'Contact No',
                    contentPadding:
                        EdgeInsets.symmetric(horizontal: 4, vertical: 8),
                  ),
                  onChanged: (val) => item.phoneNo = val,
                ),
              ),
              Container(width: 1, color: const Color(0xFF999999)),
              SizedBox(
                width: 120,
                child: _ilpSelector(item),
              ),
              Container(width: 1, color: const Color(0xFF999999)),
              SizedBox(
                width: 50,
                child: IconButton(
                  icon: const Icon(Icons.delete_outline,
                      size: 20, color: Colors.red),
                  onPressed: () => _deleteItem(index),
                ),
              ),
              Container(width: 1, color: const Color(0xFF999999)),
            ],
          ),
        ),
        Container(height: 1, color: const Color(0xFF999999)),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      child: Scaffold(
        appBar: AppBar(
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () => Navigator.of(context).pop(),
          ),
          backgroundColor: Colors.red,
          title: const Text('OCR Data Check',
              style: TextStyle(color: Colors.white)),
          actions: [
            IconButton(
              key: _downloadButtonKey,
              icon: const Icon(Icons.download, color: Colors.white),
              onPressed: _exportListToCsv,
            ),
            IconButton(
              icon: const Icon(Icons.add_circle_outline, color: Colors.white),
              onPressed: _addNewItem,
            ),
          ],
        ),
        body: SafeArea(
          child: Stack(
            children: [
              GestureDetector(
                behavior: HitTestBehavior.opaque,
                onTap: () => FocusScope.of(context).unfocus(),
                child: Padding(
                  padding: const EdgeInsets.all(10),
                  child: Column(
                    children: [
                      _headerInfoBlock(),
                      Expanded(
                        child: SingleChildScrollView(
                          scrollDirection: Axis.horizontal,
                          child: SizedBox(
                            width: 50 +
                                150 +
                                175 +
                                150 +
                                120 +
                                50 +
                                7, // + border widths
                            child: Column(
                              children: [
                                _headerRow(),
                                Expanded(
                                  child: ListView.builder(
                                    itemCount: list.length,
                                    itemBuilder: (context, index) =>
                                        _dataRow(index),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              if (isProcessing)
                Container(
                  color: Colors.black45,
                  child: Center(
                    child: Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const CircularProgressIndicator(color: Colors.red),
                          const SizedBox(height: 12),
                          Text(progressText, textAlign: TextAlign.center),
                        ],
                      ),
                    ),
                  ),
                ),
            ],
          ),
        ),
        bottomNavigationBar: Padding(
          padding: const EdgeInsets.all(16.0),
          child: SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red,
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8)),
              ),
              onPressed: isProcessing ? null : _submitOcrData,
              child: const Text('Save',
                  style: TextStyle(fontSize: 16, color: Colors.white)),
            ),
          ),
        ),
      ),
    );
  }
}

class _HeaderCell extends StatelessWidget {
  final String text;
  final double width;

  const _HeaderCell({required this.text, required this.width});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: width,
      child: Center(
        child: Text(
          text,
          textAlign: TextAlign.center,
          style: const TextStyle(
              fontSize: 12, color: Colors.black, fontWeight: FontWeight.w500),
        ),
      ),
    );
  }
}
