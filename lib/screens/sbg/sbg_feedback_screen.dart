// ignore_for_file: use_build_context_synchronously

import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/db_setup/dataset/SBGFeedbackModel.dart';
import 'package:starsfa/db_setup/sbg_database.dart' show SBGDatabase;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/route_master_class.dart';

import '../../db_setup/dataset/CustomerCompetitorQuantityModel.dart';

class SBGFeedbackScreen extends StatefulWidget {
  final String sessionID;
  const SBGFeedbackScreen({super.key, required this.sessionID});

  @override
  State<SBGFeedbackScreen> createState() => _SBGFeedbackScreenState();
}

class _SBGFeedbackScreenState extends State<SBGFeedbackScreen> {
  bool isLoading = false;
  List<CustomerCompetitorQuantityModel> dataList = [];
  int totalCount = 0;
  String dns = '';
  String userType = '';
  String selectedUniverseType = '';
  String selectedState = '';
  String selectedDistrict = '';
  String selectedBlock = '';
  String selectedGramPanchayat = '';
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
      final Box box = Hive.box('checkIn');
      box.put('sbgMenuOpened', true);
    });
  }

  Future<void> _loadData() async {
    final Box box = Hive.box('checkIn');
    final String customerCode = box.get('customerCode', defaultValue: '');
    if (customerCode.isEmpty) return;

    final list = await SBGDatabase.instance
        .getAllCustomerCompetitorQuantitySorted(customerCode);
    String dnsCode = list[0].customerDNS ?? '';
    print("dnsCode: ${list[0]}");
    setState(() {
      dataList = list;
      dns = dnsCode;
      _recalculateTotal();
    });
  }

  void _recalculateTotal() {
    int total = 0;
    for (final item in dataList) {
      total += int.tryParse(item.quantity ?? '0') ?? 0;
    }
    totalCount = total;
  }

  void showToast(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message)),
    );
  }

  Future<void> submit(BuildContext context) async {
    try {
      print("userType $userType");
      if (selectedUniverseType == '') {
        showToast("Please select universe type");
        return;
      }
      if (userType.toLowerCase() == 'ne') {
        if (selectedState == '') {
          showToast("Please select state");
          return;
        } else if (selectedDistrict == '') {
          showToast("Please select district");
          return;
        } else if (selectedBlock == '') {
          showToast("Please select block");
          return;
        } else if (selectedGramPanchayat == '') {
          showToast("Please select gram panchayat");
          return;
        }
      }
      for (final item in dataList) {
        await SBGDatabase.instance.insertOrUpdateSbgFeedback(item);
      }
      await SBGDatabase.instance.getAllSbgFeedback();
      await callSubmitApi();
      final Box box = Hive.box('checkIn');
      await box.put('sbgSubmitted', true);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Submitted successfully')),
      );
      Navigator.of(context).pop();
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Failed: ${e.toString()}')),
      );
    }
  }

  Future<void> callSubmitApi() async {
    try {
      print('callSubmitApi');
      final Box box = Hive.box('checkIn');
      final String empCode = box.get('empCode', defaultValue: '');

      final List<SBGFeedbackModel> feedbackList =
          await SBGDatabase.instance.getAllSbgFeedbackWhereFlag1();
      print('feedbackList: ${feedbackList.length}');
      print('feedbackList: ${selectedGramPanchayat}');
      if (feedbackList.isEmpty) return;

      // Build XML
      final StringBuffer xmlBuffer = StringBuffer();
      xmlBuffer.write("<?xml version='1.0' encoding='UTF-8'?><root>");
      xmlBuffer.write("<marketfeedback>");
      xmlBuffer.write(
          '<universetype><![CDATA[$selectedUniverseType]]></universetype>');
      xmlBuffer.write('<state><![CDATA[$selectedState]]></state>');
      xmlBuffer.write('<district><![CDATA[$selectedDistrict]]></district>');
      xmlBuffer.write('<block><![CDATA[$selectedBlock]]></block>');
      xmlBuffer.write(
          '<grampanchayat><![CDATA[$selectedGramPanchayat]]></grampanchayat>');
      xmlBuffer.write("<details>");
      String marketFeedbackId = '';
      if (feedbackList.isNotEmpty) {
        final String dateTime = feedbackList[0].dateTime ?? '';
        final String cleanDateTime = dateTime
            .replaceAll('-', '')
            .replaceAll(' ', '')
            .replaceAll(':', '');
        marketFeedbackId = 'SBG$empCode$cleanDateTime';
      } else {
        final DateTime now = DateTime.now();
        final String cleanDateTime = '${now.year}'
            '${now.month.toString().padLeft(2, '0')}'
            '${now.day.toString().padLeft(2, '0')}'
            '${now.hour.toString().padLeft(2, '0')}'
            '${now.minute.toString().padLeft(2, '0')}'
            '${now.second.toString().padLeft(2, '0')}';
        marketFeedbackId = 'SBG$empCode$cleanDateTime';
      }

      for (final item in feedbackList) {
        final String dateTime = feedbackList[0].dateTime ?? '';
        xmlBuffer.write('<item>');
        xmlBuffer.write(
            '<market_feedback_id><![CDATA[$marketFeedbackId]]></market_feedback_id>');
        xmlBuffer.write(
            '<customer_code><![CDATA[${item.customerCode ?? ''}]]></customer_code>');
        xmlBuffer.write('<emp_code><![CDATA[$empCode]]></emp_code>');
        xmlBuffer.write(
            '<competitor_quantity_id><![CDATA[${item.competitorCode ?? ''}]]></competitor_quantity_id>');
        xmlBuffer.write('<qty><![CDATA[${item.quantity ?? '0'}]]></qty>');
        xmlBuffer.write('<date_time><![CDATA[$dateTime]]></date_time>');
        xmlBuffer.write('</item>');
      }

      xmlBuffer.write('</details></marketfeedback></root>');

      final String xmlData = xmlBuffer.toString();
      print('SBG XML: $xmlData');

      // POST request
      final Uri uri = Uri.parse(
          '${AppWebService.baseURL}misreport/save_competitor_qty_v1.php');

      final http.Response response = await http.post(
        uri,
        headers: {'Content-Type': 'application/xml'},
        body: xmlData,
      );

      print('SBG Response: ${response.body}');

      final Map<String, dynamic> jsonResponse = jsonDecode(response.body);
      print(jsonResponse);
      if (jsonResponse['status'] == true) {
        // Update local DB quantities & clear feedback table
        for (final item in feedbackList) {
          await SBGDatabase.instance.updateCustomerCompetitorQuantity(
            item.customerCode ?? '',
            item.competitorCode ?? '',
            item.quantity ?? '0',
          );
        }
        await SBGDatabase.instance.deleteAllSbgFeedback();
      } else {
        throw Exception('Server returned status false');
      }
    } catch (e) {
      rethrow;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () => Navigator.of(context).pop(),
        ),
        backgroundColor: Colors.red,
        title: const Text(
          'SBG Feedback',
          style: TextStyle(color: Colors.white),
        ),
      ),
      body: Column(
        children: <Widget>[
          // ── SCROLLABLE AREA (header + list) ───────────
          Expanded(
            child: SingleChildScrollView(
              child: Column(
                children: [
                  // Customer details header
                  CustomerDetailsMarketFeedback(
                    dns: dns,
                    onUniverseTypeChanged: (val) =>
                        setState(() => selectedUniverseType = val),
                    onStateChanged: (val) =>
                        setState(() => selectedState = val),
                    onDistrictChanged: (val) =>
                        setState(() => selectedDistrict = val),
                    onBlockChanged: (val) =>
                        setState(() => selectedBlock = val),
                    onGramPanchayatChanged: (val) =>
                        setState(() => selectedGramPanchayat = val),
                    onUserTypeChanged: (val) => setState(() => userType = val),
                  ),

                  // Column label row
                  Padding(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    child: Row(
                      children: const [
                        Expanded(
                          child: Text(
                            'Competitor Name',
                            style: TextStyle(color: Colors.black, fontSize: 16),
                          ),
                        ),
                        Text(
                          'Potential (MT)',
                          style: TextStyle(color: Colors.black, fontSize: 16),
                        ),
                      ],
                    ),
                  ),

                  // List — shrinkWrap so it sizes to content inside scroll view
                  dataList.isEmpty
                      ? const Center(
                          child: CircularProgressIndicator(color: Colors.red))
                      : ListView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: dataList.length,
                          itemBuilder: (context, index) {
                            final item = dataList[index];
                            return Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 12, vertical: 8),
                              decoration: BoxDecoration(
                                border: Border(
                                  bottom: BorderSide(color: Colors.grey[300]!),
                                ),
                              ),
                              child: Row(
                                children: [
                                  Expanded(
                                    child: Text(
                                      item.competitorName ?? '',
                                      style: TextStyle(
                                        color: item.mandatory == 'Y'
                                            ? Colors.red
                                            : Colors.black,
                                        fontSize: 14,
                                      ),
                                    ),
                                  ),
                                  SizedBox(
                                    width: 100,
                                    child: TextFormField(
                                      key: ValueKey(
                                          '${item.competitorQuantityId}_$index'),
                                      initialValue: item.quantity ?? '0',
                                      keyboardType: TextInputType.number,
                                      textAlign: TextAlign.right,
                                      decoration: const InputDecoration(
                                        border: OutlineInputBorder(),
                                        contentPadding: EdgeInsets.symmetric(
                                            horizontal: 8, vertical: 4),
                                      ),
                                      onChanged: (value) {
                                        item.quantity =
                                            value.isEmpty ? '0' : value;
                                        setState(() {
                                          _recalculateTotal();
                                        });
                                      },
                                    ),
                                  ),
                                  Text(
                                    '  MT',
                                    style: TextStyle(
                                      color: item.mandatory == 'Y'
                                          ? Colors.red
                                          : Colors.black,
                                      fontSize: 14,
                                    ),
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                ],
              ),
            ),
          ),

          // ── PINNED FOOTER ──────────────────────────────
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            color: Colors.grey[200],
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Total Counter Potential',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                ),
                Text(
                  '$totalCount MT',
                  style: const TextStyle(
                      fontWeight: FontWeight.bold, fontSize: 16),
                ),
              ],
            ),
          ),

          // ── PINNED SUBMIT BUTTON ───────────────────────
          InkWell(
            onTap: isLoading
                ? null
                : () async {
                    setState(() => isLoading = true);
                    await submit(context);
                    setState(() => isLoading = false);
                  },
            child: Container(
              width: double.infinity,
              margin: const EdgeInsets.all(10),
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.red,
                borderRadius: BorderRadius.circular(5),
              ),
              child: Center(
                child: isLoading
                    ? const CircularProgressIndicator(color: Colors.white)
                    : const Text(
                        'Submit Feedback',
                        style: TextStyle(color: Colors.white, fontSize: 16),
                      ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class CustomerDetailsMarketFeedback extends StatefulWidget {
  final String dns;
  final Function(String)? onUniverseTypeChanged;
  final Function(String)? onStateChanged;
  final Function(String)? onDistrictChanged;
  final Function(String)? onBlockChanged;
  final Function(String)? onGramPanchayatChanged;
  final Function(String)? onUserTypeChanged;

  const CustomerDetailsMarketFeedback({
    super.key,
    required this.dns,
    this.onUniverseTypeChanged,
    this.onStateChanged,
    this.onDistrictChanged,
    this.onBlockChanged,
    this.onGramPanchayatChanged,
    this.onUserTypeChanged,
  });

  @override
  State<CustomerDetailsMarketFeedback> createState() =>
      _CustomerDetailsMarketFeedbackState();
}

class _CustomerDetailsMarketFeedbackState
    extends State<CustomerDetailsMarketFeedback> {
  String routeName = '';
  bool isShow = false;

  String selectedUniverseType = "";
  List<String> allUniverseTypes = [
    "Star - Dealer",
    "Non Star - Dealer",
    "Star -Sub Dealer",
    "Star - RSAR",
    "Non Star - Non link Sub Dealer",
    "Non Star - Star Link Sub Dealer"
  ];

  String selectedState = "";
  List<String> allState = [];

  String selectedDistrict = "";
  List<String> allDistrict = [];

  String selectedBlock = "";
  List<String> allBlock = [];

  String selectedGramPanchayat = "";
  List<String> allGramPanchayat = [];

  // ── NEW: lock flags — true once a value already exists for that field ──
  bool isStateLocked = false;
  bool isDistrictLocked = false;
  bool isBlockLocked = false;
  bool isGramPanchayatLocked = false;

  static const String kOtherOption = 'Other';

  @override
  void initState() {
    super.initState();
    Hive.openBox('cart');
    WidgetsBinding.instance.addPostFrameCallback((_) {
      getRouteName();
      if (widget.dns.isNotEmpty) {
        _loadDefaultData();
      }
    });
  }

  @override
  void didUpdateWidget(CustomerDetailsMarketFeedback oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.dns.isEmpty && widget.dns.isNotEmpty) {
      _loadDefaultData();
    }
  }

  void getRouteName() async {
    final Box box = Hive.box('checkIn');
    final String routeCode = box.get('routeCode', defaultValue: '');
    if (routeCode.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('No Route found for this Customer')),
      );
      return;
    }
    final RouteMasterDB routeMasterDB =
        await RouteMasterDB.getRouteMasterDBByRouteCode(routeCode);
    setState(() {
      routeName = routeMasterDB.routeName ?? '';
    });
  }

  Future<void> _loadStates() async {
    final states = await SBGDatabase.instance.getAllStateNameList();
    setState(() {
      allState = states;
      selectedState = "";
      selectedDistrict = "";
      allDistrict = [];
      selectedBlock = "";
      allBlock = [];
      selectedGramPanchayat = "";
      allGramPanchayat = [];
      isStateLocked = false;
      isDistrictLocked = false;
      isBlockLocked = false;
      isGramPanchayatLocked = false;
    });
  }

  Future<void> _loadDefaultData() async {
    if (widget.dns.isEmpty) {
      await _loadStates();
      return;
    }

    final customer =
        await SBGDatabase.instance.getCustomerMarketFeedbackDetails(widget.dns);

    if (customer == null) {
      await _loadStates();
      return;
    }

    final String universeType = customer.customerUniverseType ?? '';
    final String state = customer.customerState ?? '';
    final String district = customer.customerDistrict ?? '';
    final String block = customer.customerBlockName ?? '';
    final String gramPanchayat = customer.customerGramPanchayat ?? '';
    final String type = customer.customerType ?? '';

    final results = await Future.wait([
      SBGDatabase.instance.getAllStateNameList(),
      state.isNotEmpty
          ? SBGDatabase.instance.getAllDistrictNameList(state)
          : Future.value(<String>[]),
      state.isNotEmpty && district.isNotEmpty
          ? SBGDatabase.instance.getAllBlockNameList(state, district)
          : Future.value(<String>[]),
      state.isNotEmpty && district.isNotEmpty && block.isNotEmpty
          ? SBGDatabase.instance
              .getAllGramPanchayatNameList(state, district, block)
          : Future.value(<String>[]),
    ]);

    setState(() {
      allState = results[0];
      allDistrict = results[1];
      allBlock = results[2];
      allGramPanchayat = results[3];

      selectedUniverseType = universeType;
      selectedState = state;
      selectedDistrict = district;
      selectedBlock = block;
      selectedGramPanchayat = gramPanchayat;
      isShow = type.toLowerCase() == 'ne';

      // Lock each field only if it already came with a value
      isStateLocked = state.isNotEmpty;
      isDistrictLocked = district.isNotEmpty;
      isBlockLocked = block.isNotEmpty;
      isGramPanchayatLocked = gramPanchayat.isNotEmpty;
    });

    widget.onUniverseTypeChanged?.call(universeType);
    widget.onStateChanged?.call(state);
    widget.onDistrictChanged?.call(district);
    widget.onBlockChanged?.call(block);
    widget.onGramPanchayatChanged?.call(gramPanchayat);
    widget.onUserTypeChanged?.call(type);
  }

  Future<void> _onStateChanged(String stateName) async {
    setState(() {
      selectedState = stateName;
      selectedDistrict = "";
      allDistrict = [];
      selectedBlock = "";
      allBlock = [];
      selectedGramPanchayat = "";
      allGramPanchayat = [];
    });
    final districts =
        await SBGDatabase.instance.getAllDistrictNameList(stateName);
    setState(() {
      allDistrict = districts;
    });
  }

  Future<void> _onDistrictChanged(String districtName) async {
    setState(() {
      selectedDistrict = districtName;
      selectedBlock = "";
      allBlock = [];
      selectedGramPanchayat = "";
      allGramPanchayat = [];
    });
    final blocks = await SBGDatabase.instance
        .getAllBlockNameList(selectedState, districtName);
    setState(() {
      allBlock = blocks;
    });
  }

  Future<void> _onBlockChanged(String blockName) async {
    setState(() {
      selectedBlock = blockName;
      selectedGramPanchayat = "";
      allGramPanchayat = [];
    });
    final grams = await SBGDatabase.instance.getAllGramPanchayatNameList(
        selectedState, selectedDistrict, blockName);
    setState(() {
      allGramPanchayat = grams;
    });
  }

  // ── NEW: popup for custom Gram Panchayat entry ──
  Future<String?> _showGramPanchayatOtherDialog() {
    final TextEditingController controller = TextEditingController();
    final GlobalKey<FormState> formKey = GlobalKey<FormState>();
    return showDialog<String>(
      context: context,
      barrierDismissible: false,
      builder: (dialogContext) {
        return AlertDialog(
          title: const Text('Enter Gram Panchayat'),
          content: Form(
            key: formKey,
            child: TextFormField(
              controller: controller,
              autofocus: true,
              textCapitalization: TextCapitalization.words,
              decoration: const InputDecoration(
                labelText: 'Gram Panchayat Name',
                border: OutlineInputBorder(),
              ),
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'Gram Panchayat is required';
                }
                return null;
              },
            ),
          ),
          actions: [
            ElevatedButton(
              onPressed: () {
                if (formKey.currentState!.validate()) {
                  Navigator.of(dialogContext)
                      .pop(controller.text.trim() + " (O)");
                }
              },
              child: const Text('Submit'),
            ),
          ],
        );
      },
    );
  }

  // ── Editable dropdown (used for Universe Type, and unlocked location fields) ──
  Widget _buildDropdown({
    required String label,
    required String selectedValue,
    required List<String> items,
    required ValueChanged<String?> onChanged,
    bool enabled = true,
  }) {
    // Guard: if selectedValue isn't in items, inject it so Dropdown doesn't crash.
    final List<String> safeItems = List<String>.from(items);
    if (selectedValue.isNotEmpty && !safeItems.contains(selectedValue)) {
      safeItems.insert(0, selectedValue);
    }

    return DropdownButtonFormField<String>(
      value: selectedValue.isEmpty ? null : selectedValue,
      isExpanded: true,
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(fontSize: 13),
        contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(6)),
        filled: true,
        fillColor: enabled ? Colors.white : Colors.grey[200],
      ),
      items: !enabled || safeItems.isEmpty
          ? null
          : safeItems.map((String value) {
              return DropdownMenuItem<String>(
                value: value,
                child: Text(
                  value,
                  style: const TextStyle(fontSize: 13),
                  overflow: TextOverflow.ellipsis,
                ),
              );
            }).toList(),
      onChanged: enabled && safeItems.isNotEmpty ? onChanged : null,
      hint: Text(
        safeItems.isEmpty ? 'No data available' : 'Select $label',
        style: const TextStyle(fontSize: 13),
      ),
    );
  }

  // ── NEW: read-only display for locked fields (safe for custom "Other" text too) ──
  Widget _buildLockedField({
    required String label,
    required String value,
  }) {
    return TextFormField(
      initialValue: value,
      readOnly: true,
      enabled: false,
      style: const TextStyle(fontSize: 13, color: Colors.black87),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(fontSize: 13),
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(6)),
        filled: true,
        fillColor: Colors.grey[200],
        suffixIcon:
            const Icon(Icons.lock_outline, size: 16, color: Colors.grey),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return ValueListenableBuilder<Box>(
      valueListenable: Hive.box('checkIn').listenable(),
      builder: (BuildContext context, Box box, Widget? child) {
        final String customerCode = box.get('customerCode', defaultValue: '');
        return FutureBuilder(
          future:
              CustomerMasterDB.getCustomerMasterDBByCustomerCode(customerCode),
          builder:
              (BuildContext context, AsyncSnapshot<CustomerMasterDB> snapshot) {
            if (snapshot.connectionState == ConnectionState.done) {
              if (snapshot.hasData) {
                final CustomerMasterDB customer =
                    snapshot.data ?? CustomerMasterDB();
                return Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(10),
                  margin: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: Colors.grey[300],
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: <Widget>[
                      Text('Customer Code: ${widget.dns}',
                          style: const TextStyle(
                              fontSize: 14, color: Colors.black)),
                      Text('Customer Name: ${customer.customerName}',
                          style: const TextStyle(
                              fontSize: 14, color: Colors.black)),
                      Text('Customer Type: ${customer.customerType}',
                          style: const TextStyle(
                              fontSize: 14, color: Colors.black)),
                      Text('Route Name: $routeName',
                          style: const TextStyle(
                              fontSize: 14, color: Colors.black)),
                      const SizedBox(height: 10),

                      // Universe Type — ALWAYS editable
                      _buildDropdown(
                        label: 'Universe Type',
                        selectedValue: selectedUniverseType,
                        items: allUniverseTypes,
                        onChanged: (val) {
                          if (val == null) return;
                          setState(() => selectedUniverseType = val);
                          widget.onUniverseTypeChanged?.call(val);
                        },
                      ),
                      if (isShow) ...[
                        const SizedBox(height: 10),

                        // State
                        isStateLocked
                            ? _buildLockedField(
                                label: 'State', value: selectedState)
                            : _buildDropdown(
                                label: 'State',
                                selectedValue: selectedState,
                                items: allState,
                                onChanged: (val) {
                                  if (val == null) return;
                                  _onStateChanged(val);
                                  widget.onStateChanged?.call(val);
                                },
                              ),
                        const SizedBox(height: 10),

                        // District
                        isDistrictLocked
                            ? _buildLockedField(
                                label: 'District', value: selectedDistrict)
                            : _buildDropdown(
                                label: 'District',
                                selectedValue: selectedDistrict,
                                items: allDistrict,
                                enabled: selectedState.isNotEmpty,
                                onChanged: (val) {
                                  if (val == null) return;
                                  _onDistrictChanged(val);
                                  widget.onDistrictChanged?.call(val);
                                },
                              ),
                        const SizedBox(height: 10),

                        // Block
                        isBlockLocked
                            ? _buildLockedField(
                                label: 'Block', value: selectedBlock)
                            : _buildDropdown(
                                label: 'Block',
                                selectedValue: selectedBlock,
                                items: allBlock,
                                enabled: selectedDistrict.isNotEmpty,
                                onChanged: (val) {
                                  if (val == null) return;
                                  _onBlockChanged(val);
                                  widget.onBlockChanged?.call(val);
                                },
                              ),
                        const SizedBox(height: 10),

                        // Gram Panchayat (with "Other" popup)
                        // Gram Panchayat
                        isGramPanchayatLocked ||
                                (selectedGramPanchayat.isNotEmpty &&
                                    !allGramPanchayat
                                        .contains(selectedGramPanchayat))
                            ? _buildLockedField(
                                label: 'Gram Panchayat',
                                value: selectedGramPanchayat)
                            : _buildDropdown(
                                label: 'Gram Panchayat',
                                selectedValue: selectedGramPanchayat,
                                items: [...allGramPanchayat, kOtherOption],
                                enabled: selectedBlock.isNotEmpty,
                                onChanged: (val) async {
                                  if (val == null) return;
                                  if (val == kOtherOption) {
                                    final String? customValue =
                                        await _showGramPanchayatOtherDialog();
                                    if (customValue != null &&
                                        customValue.isNotEmpty) {
                                      setState(() {
                                        selectedGramPanchayat = customValue;
                                        isGramPanchayatLocked =
                                            true; // lock after custom entry
                                      });
                                      widget.onGramPanchayatChanged
                                          ?.call(customValue);
                                    }
                                  } else {
                                    setState(() => selectedGramPanchayat = val);
                                    widget.onGramPanchayatChanged?.call(val);
                                  }
                                },
                              ),
                      ],
                    ],
                  ),
                );
              } else {
                return const Text('No Customer Details');
              }
            } else {
              return const Center(
                child: CircularProgressIndicator(color: Colors.black),
              );
            }
          },
        );
      },
    );
  }
}
