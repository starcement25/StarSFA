import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class DeclarationRequest extends StatefulWidget {
  final bool isOptionSelected;
  const DeclarationRequest({super.key, this.isOptionSelected = false});

  @override
  State<DeclarationRequest> createState() => _DeclarationRequestState();
}

class _DeclarationRequestState extends State<DeclarationRequest> {
  List<DeclarationRequestDataList>? declarationData;
  String myLvl = "";
  bool isLoading = true;
  String statusType = 'pending';
  String selectedMonth = "All";
  List<String> allMonth = [];
  TextEditingController searchController = TextEditingController();
  String? searchText = '';

  @override
  void initState() {
    super.initState();
    searchController.addListener(() => setState(() {}));
    _populateMonths();
    fetchDeclarationData();
  }

  void _populateMonths() {
    final now = DateTime.now();

    // Determine current FY start year
    // FY runs April to March, so if current month < April, FY started last year
    final currentFYStartYear = now.month >= 4 ? now.year : now.year - 1;
    final previousFYStartYear = currentFYStartYear - 1;

    const monthNames = [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December'
    ];

    // FY months order: April(4) to March(3)
    final fyMonthOrder = [4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3];

    List<String> months = ['All'];

    for (int fyStartYear in [previousFYStartYear, currentFYStartYear]) {
      for (int monthNum in fyMonthOrder) {
        // April–December belong to fyStartYear, January–March to fyStartYear+1
        final year = monthNum >= 4 ? fyStartYear : fyStartYear + 1;
        months.add('${monthNames[monthNum - 1]}, $year');
      }
    }

    setState(() {
      allMonth = months;
    });
  }

  void fetchDeclarationData() async {
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      declarationData = await DeclarationRequestDataList.getCustomerById(
          userDetails!.empCode ?? '');
    } catch (e) {
    } finally {
      setState(() {
        isLoading = false;
      });
    }
  }

  void asmApproveButtonClick(DeclarationRequestDataList declaration) async {
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      final headers = {
        'Content-Type': 'application/json',
      };
      final Map<String, String> data = {
        'table_id': declaration.requestId ?? '',
        'dealer_id': declaration.customerId ?? '',
        'emp_code': userDetails!.empCode ?? '',
        'status': 'Approve',
      };
      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}api_approve_reject_dealer_exclusive.php'),
        headers: headers,
        body: jsonEncode(data),
      );
      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['process_status'] == "Yes") {
          fetchDeclarationData();
        } else {
          showDialog(
            // ignore: use_build_context_synchronously
            context: context,
            builder: (_) => AlertDialog(
              title: const Text("Error"),
              content: Text(responseData['error'] ?? "Unknown error"),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text("OK"),
                )
              ],
            ),
          );
        }
      }
    } catch (e) {}
  }

  void asmRejectButtonClick(DeclarationRequestDataList declaration) {
    final TextEditingController reasonController = TextEditingController();
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text("Reject Reason"),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text("Please enter the reason for rejection:"),
              const SizedBox(height: 10),
              TextField(
                controller: reasonController,
                maxLines: 3,
                decoration: InputDecoration(
                  hintText: "Enter reason",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              child: const Text("Cancel"),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
            ElevatedButton(
              child: const Text("Submit"),
              onPressed: () async {
                Navigator.of(context).pop();
                String reason = reasonController.text.trim();
                if (reason.isEmpty) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text("Reason is required")),
                  );
                  return;
                }
                await handleAsmReject(reason, declaration);
              },
            ),
          ],
        );
      },
    );
  }

  Future<void> handleAsmReject(
      String reason, DeclarationRequestDataList declaration) async {
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      final headers = {
        'Content-Type': 'application/json',
      };
      final Map<String, String> data = {
        'table_id': declaration.requestId ?? '',
        'dealer_id': declaration.customerId ?? '',
        'emp_code': userDetails!.empCode ?? '',
        'status': 'Reject',
        'reason': reason,
      };
      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}api_approve_reject_dealer_exclusive.php'),
        headers: headers,
        body: jsonEncode(data),
      );
      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['process_status'] == "Yes") {
          // ignore: use_build_context_synchronously
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text("Request rejected successfully.")),
          );
          fetchDeclarationData();
        } else {
          showDialog(
            // ignore: use_build_context_synchronously
            context: context,
            builder: (_) => AlertDialog(
              title: const Text("Error"),
              content: Text(responseData['error'] ?? "Unknown error"),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text("OK"),
                )
              ],
            ),
          );
        }
      }
    } catch (e) {}
  }

  void rsmApproveButtonClick(DeclarationRequestDataList declaration) async {
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      final headers = {
        'Content-Type': 'application/json',
      };
      final Map<String, String> data = {
        'table_id': declaration.requestId ?? '',
        'dealer_id': declaration.customerId ?? '',
        'emp_code': userDetails!.empCode ?? '',
        'status': 'Approve',
      };
      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}api_approve_reject_dealer_exclusive.php'),
        headers: headers,
        body: jsonEncode(data),
      );
      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['process_status'] == "Yes") {
          fetchDeclarationData();
        } else {
          showDialog(
            // ignore: use_build_context_synchronously
            context: context,
            builder: (_) => AlertDialog(
              title: const Text("Error"),
              content: Text(responseData['error'] ?? "Unknown error"),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text("OK"),
                )
              ],
            ),
          );
        }
      }
    } catch (e) {}
  }

  void rsmRejectButtonClick(DeclarationRequestDataList declaration) {
    final TextEditingController reasonController = TextEditingController();
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text("Reject Reason"),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text("Please enter the reason for rejection:"),
              const SizedBox(height: 10),
              TextField(
                controller: reasonController,
                maxLines: 3,
                decoration: InputDecoration(
                  hintText: "Enter reason",
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              child: const Text("Cancel"),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
            ElevatedButton(
              child: const Text("Submit"),
              onPressed: () async {
                Navigator.of(context).pop();
                String reason = reasonController.text.trim();
                if (reason.isEmpty) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text("Reason is required")),
                  );
                  return;
                }
                await handleRsmReject(reason, declaration);
              },
            ),
          ],
        );
      },
    );
  }

  Future<void> handleRsmReject(
      String reason, DeclarationRequestDataList declaration) async {
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      final headers = {
        'Content-Type': 'application/json',
      };
      final Map<String, String> data = {
        'table_id': declaration.requestId ?? '',
        'dealer_id': declaration.customerId ?? '',
        'emp_code': userDetails!.empCode ?? '',
        'status': 'Reject',
        'reason': reason,
      };
      final response = await http.post(
        Uri.parse(
            '${AppWebService.baseURL}api_approve_reject_dealer_exclusive.php'),
        headers: headers,
        body: jsonEncode(data),
      );
      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['process_status'] == "Yes") {
          // ignore: use_build_context_synchronously
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text("Request rejected successfully.")),
          );
          fetchDeclarationData();
        } else {
          showDialog(
            // ignore: use_build_context_synchronously
            context: context,
            builder: (_) => AlertDialog(
              title: const Text("Error"),
              content: Text(responseData['error'] ?? "Unknown error"),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text("OK"),
                )
              ],
            ),
          );
        }
      }
    } catch (e) {}
  }

  String formatMonthYear(String? month, String? year) {
  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];
  if (month == null || year == null) return 'N/A';
  final monthIndex = int.tryParse(month);
  if (monthIndex == null || monthIndex < 1 || monthIndex > 12) {
    return '$month/$year';
  }
  return '${monthNames[monthIndex - 1]}, $year';
}

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      child: Scaffold(
        appBar: AppBar(
          leading: IconButton(
            icon: const Icon(
              Icons.arrow_back,
              color: Colors.white,
            ),
            onPressed: () {
              Navigator.of(context).pop();
            },
          ),
          backgroundColor: Colors.red,
          title: const Text(
            'Declaration Request',
            style: TextStyle(color: Colors.white),
          ),
        ),
        body: SafeArea(
          child: GestureDetector(
              behavior: HitTestBehavior.opaque,
              onTap: () {
                FocusScope.of(context).unfocus();
              },
              child: Column(children: [
                Padding(
                  padding:
                      const EdgeInsets.only(left: 10.0, right: 10.0, top: 10.0),
                  child: _buildDropdown(
                    label: 'Month',
                    selectedValue: selectedMonth,
                    items: allMonth,
                    onChanged: (val) {
                      if (val == null) return;
                      setState(() => selectedMonth = val);
                    },
                  ),
                ),
                Padding(
                  padding:
                      const EdgeInsets.only(left: 10.0, right: 10.0, top: 10.0),
                  child: LabeledTextField(
                    controller: searchController,
                    hintText: 'Search...',
                    initialValue: searchText,
                  ),
                ),
                Padding(
                  padding:
                      const EdgeInsets.only(left: 10.0, right: 10.0, top: 10.0),
                  child: Row(children: [
                    Expanded(
                      flex: 1,
                      child: SizedBox(
                        width: double.infinity,
                        child: OutlinedButton(
                          onPressed: () {
                            setState(() {
                              statusType = "Pending";
                            });
                          },
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            side: const BorderSide(color: Colors.grey),
                            backgroundColor: statusType.toLowerCase() ==
                                    'Pending'.toLowerCase()
                                ? Colors.red
                                : Colors.grey,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(4),
                            ),
                          ),
                          child: Text(
                            "Pending",
                            style: const TextStyle(
                              fontSize: 16,
                              color: Color.fromARGB(255, 255, 255, 255),
                            ),
                          ),
                        ),
                      ),
                    ),
                    SizedBox(width: 5),
                    Expanded(
                      flex: 1,
                      child: SizedBox(
                        width: double.infinity,
                        child: OutlinedButton(
                          onPressed: () {
                            setState(() {
                              statusType = "Approved";
                            });
                          },
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            side: const BorderSide(color: Colors.grey),
                            backgroundColor: statusType.toLowerCase() ==
                                    'Approved'.toLowerCase()
                                ? Colors.red
                                : Colors.grey,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(4),
                            ),
                          ),
                          child: Text(
                            "Approved",
                            style: const TextStyle(
                              fontSize: 16,
                              color: Color.fromARGB(255, 255, 255, 255),
                            ),
                          ),
                        ),
                      ),
                    ),
                    SizedBox(width: 5),
                    Expanded(
                      flex: 1,
                      child: SizedBox(
                        width: double.infinity,
                        child: OutlinedButton(
                          onPressed: () {
                            setState(() {
                              statusType = "Rejected";
                            });
                          },
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            side: const BorderSide(color: Colors.grey),
                            backgroundColor: statusType.toLowerCase() ==
                                    'Rejected'.toLowerCase()
                                ? Colors.red
                                : Colors.grey,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(4),
                            ),
                          ),
                          child: Text(
                            "Rejected",
                            style: const TextStyle(
                              fontSize: 16,
                              color: Color.fromARGB(255, 255, 255, 255),
                            ),
                          ),
                        ),
                      ),
                    ),
                  ]),
                ),
                Expanded(
                    child: SingleChildScrollView(
                        padding: const EdgeInsets.only(
                            left: 10.0, right: 10.0, top: 10),
                        child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Container(
                                padding:
                                    const EdgeInsets.only(left: 0, right: 0),
                                child: isLoading
                                    ? const Center(
                                        child: CircularProgressIndicator())
                                    : declarationData == null
                                        ? const Center(
                                            child: Text('No data found'))
                                        : declarationData![0].myLvl == 'L3'
                                            ? SingleChildScrollView(
                                                child: Column(
                                                children: declarationData!
                                                    .where((declaration) {
                                                      final query =
                                                          searchController.text
                                                              .trim()
                                                              .toLowerCase();
                                                      bool matchesStatus;
                                                      if (statusType
                                                              .toLowerCase() ==
                                                          'pending') {
                                                        matchesStatus = declaration
                                                                .asmApproveStatus!
                                                                .toLowerCase() ==
                                                            'pending';
                                                      } else if (statusType
                                                              .toLowerCase() ==
                                                          'approved') {
                                                        matchesStatus = declaration
                                                                .asmApproveStatus!
                                                                .toLowerCase() ==
                                                            'approved';
                                                      } else {
                                                        matchesStatus = declaration
                                                                .asmApproveStatus!
                                                                .toLowerCase() ==
                                                            'rejected';
                                                      }

                                                      bool matchesMonth;
                                                      if (selectedMonth
                                                              .toLowerCase() ==
                                                          'all') {
                                                        matchesMonth = true;
                                                      } else {
                                                        String month = switch (
                                                            selectedMonth
                                                                .split(',')[0]
                                                                .trim()
                                                                .toLowerCase()) {
                                                          'january' => '01',
                                                          'february' => '02',
                                                          'march' => '03',
                                                          'april' => '04',
                                                          'may' => '05',
                                                          'june' => '06',
                                                          'july' => '07',
                                                          'august' => '08',
                                                          'september' => '09',
                                                          'october' => '10',
                                                          'november' => '11',
                                                          'december' => '12',
                                                          _ => '',
                                                        };
                                                        matchesMonth = month ==
                                                                declaration
                                                                    .month &&
                                                            selectedMonth
                                                                    .split(
                                                                        ',')[1]
                                                                    .trim() ==
                                                                declaration
                                                                    .year;
                                                      }

                                                      final matchesSearch = query
                                                              .isEmpty ||
                                                          (declaration
                                                                  .dealerName
                                                                  ?.toLowerCase()
                                                                  .contains(
                                                                      query) ??
                                                              false) ||
                                                          (declaration.branch
                                                                  ?.toLowerCase()
                                                                  .contains(
                                                                      query) ??
                                                              false);

                                                      return matchesStatus &&
                                                          matchesMonth &&
                                                          matchesSearch;
                                                    })
                                                    .toList()
                                                    .map((declaration) {
                                                      if (declaration
                                                              .asmApproveStatus ==
                                                          'Pending') {
                                                        return Card(
                                                          elevation: 6,
                                                          shape:
                                                              RoundedRectangleBorder(
                                                            borderRadius:
                                                                BorderRadius
                                                                    .circular(
                                                                        16),
                                                          ),
                                                          color: const Color
                                                              .fromARGB(255,
                                                              255, 255, 255),
                                                          child: Padding(
                                                            padding:
                                                                const EdgeInsets
                                                                    .all(16),
                                                            child: Column(
                                                              crossAxisAlignment:
                                                                  CrossAxisAlignment
                                                                      .start,
                                                              children: [
                                                                buildDataRow(
                                                                    "Dealer Name",
                                                                    declaration
                                                                        .dealerName),
                                                                buildDataRow(
                                                                    "Branch Name",
                                                                    declaration
                                                                        .branch),
                                                                buildDataRow(
                                                                    "Declaration Month",
                                                                    formatMonthYear(declaration.month, declaration.year)),
                                                                buildDataRow(
                                                                    "Lifting Qty",
                                                                    declaration
                                                                        .liftingQty),
                                                                buildDataRow(
                                                                    "ASM Approve Status",
                                                                    declaration
                                                                        .asmApproveStatus),
                                                                buildDataRow(
                                                                    "Declaration Status",
                                                                    declaration
                                                                        .status),
                                                                buildCustomButton(
                                                                    "Approve",
                                                                    () => asmApproveButtonClick(
                                                                        declaration),
                                                                    "Reject",
                                                                    () => asmRejectButtonClick(
                                                                        declaration)),
                                                              ],
                                                            ),
                                                          ),
                                                        );
                                                      } else {
                                                        return Card(
                                                          elevation: 6,
                                                          shape:
                                                              RoundedRectangleBorder(
                                                            borderRadius:
                                                                BorderRadius
                                                                    .circular(
                                                                        16),
                                                          ),
                                                          color: const Color
                                                              .fromARGB(255,
                                                              255, 255, 255),
                                                          child: Padding(
                                                            padding:
                                                                const EdgeInsets
                                                                    .all(16),
                                                            child: Column(
                                                              crossAxisAlignment:
                                                                  CrossAxisAlignment
                                                                      .start,
                                                              children: [
                                                                buildDataRow(
                                                                    "Dealer Name",
                                                                    declaration
                                                                        .dealerName),
                                                                buildDataRow(
                                                                    "Branch Name",
                                                                    declaration
                                                                        .branch),
                                                                buildDataRow(
                                                                    "Declaration Month",
                                                                    formatMonthYear(declaration.month, declaration.year)),
                                                                buildDataRow(
                                                                    "Lifting Qty",
                                                                    declaration
                                                                        .liftingQty),
                                                                buildDataRow(
                                                                    "ASM Approve Status",
                                                                    declaration
                                                                        .asmApproveStatus),
                                                                buildDataRow(
                                                                    "Declaration Status",
                                                                    declaration
                                                                        .status),
                                                              ],
                                                            ),
                                                          ),
                                                        );
                                                      }
                                                    })
                                                    .toList(),
                                              ))
                                            : SingleChildScrollView(
                                                child: Column(
                                                children: declarationData!
                                                    .where((declaration) {
                                                      final query =
                                                          searchController.text
                                                              .trim()
                                                              .toLowerCase();
                                                      bool matchesStatus;
                                                      if (statusType
                                                              .toLowerCase() ==
                                                          'pending') {
                                                        matchesStatus = declaration
                                                                .rsmApproveStatus!
                                                                .toLowerCase() ==
                                                            'pending';
                                                      } else if (statusType
                                                              .toLowerCase() ==
                                                          'approved') {
                                                        matchesStatus = declaration
                                                                .rsmApproveStatus!
                                                                .toLowerCase() ==
                                                            'approved';
                                                      } else {
                                                        matchesStatus = declaration
                                                                .rsmApproveStatus!
                                                                .toLowerCase() ==
                                                            'rejected';
                                                      }

                                                      bool matchesMonth;
                                                      if (selectedMonth
                                                              .toLowerCase() ==
                                                          'all') {
                                                        matchesMonth = true;
                                                      } else {
                                                        String month = switch (
                                                            selectedMonth
                                                                .split(',')[0]
                                                                .trim()
                                                                .toLowerCase()) {
                                                          'january' => '01',
                                                          'february' => '02',
                                                          'march' => '03',
                                                          'april' => '04',
                                                          'may' => '05',
                                                          'june' => '06',
                                                          'july' => '07',
                                                          'august' => '08',
                                                          'september' => '09',
                                                          'october' => '10',
                                                          'november' => '11',
                                                          'december' => '12',
                                                          _ => '',
                                                        };
                                                        matchesMonth = month ==
                                                                declaration
                                                                    .month &&
                                                            selectedMonth
                                                                    .split(
                                                                        ',')[1]
                                                                    .trim() ==
                                                                declaration
                                                                    .year;
                                                      }

                                                      final matchesSearch = query
                                                              .isEmpty ||
                                                          (declaration
                                                                  .dealerName
                                                                  ?.toLowerCase()
                                                                  .contains(
                                                                      query) ??
                                                              false) ||
                                                          (declaration.branch
                                                                  ?.toLowerCase()
                                                                  .contains(
                                                                      query) ??
                                                              false);

                                                      return matchesStatus &&
                                                          matchesMonth &&
                                                          matchesSearch;
                                                    })
                                                    .toList()
                                                    .map((declaration) {
                                                      if (declaration
                                                              .rsmApproveStatus ==
                                                          'Pending') {
                                                        return Card(
                                                          elevation: 6,
                                                          shape:
                                                              RoundedRectangleBorder(
                                                            borderRadius:
                                                                BorderRadius
                                                                    .circular(
                                                                        16),
                                                          ),
                                                          color: const Color
                                                              .fromARGB(255,
                                                              255, 255, 255),
                                                          child: Padding(
                                                            padding:
                                                                const EdgeInsets
                                                                    .all(16),
                                                            child: Column(
                                                              crossAxisAlignment:
                                                                  CrossAxisAlignment
                                                                      .start,
                                                              children: [
                                                                buildDataRow(
                                                                    "Dealer Name",
                                                                    declaration
                                                                        .dealerName),
                                                                buildDataRow(
                                                                    "Branch Name",
                                                                    declaration
                                                                        .branch),
                                                                buildDataRow(
                                                                  "Declaration Month",
                                                                  formatMonthYear(declaration.month, declaration.year)),
                                                                buildDataRow(
                                                                    "Lifting Qty",
                                                                    declaration
                                                                        .liftingQty),
                                                                buildDataRow(
                                                                    "ASM Approve Status",
                                                                    declaration
                                                                        .asmApproveStatus),
                                                                buildDataRow(
                                                                    "RSM Approve Status",
                                                                    declaration
                                                                        .rsmApproveStatus),
                                                                buildDataRow(
                                                                    "Status",
                                                                    declaration
                                                                        .status),
                                                                buildCustomButton(
                                                                    "Approve",
                                                                    () => rsmApproveButtonClick(
                                                                        declaration),
                                                                    "Reject",
                                                                    () => rsmRejectButtonClick(
                                                                        declaration))
                                                              ],
                                                            ),
                                                          ),
                                                        );
                                                      } else {
                                                        return Card(
                                                          elevation: 6,
                                                          shape:
                                                              RoundedRectangleBorder(
                                                            borderRadius:
                                                                BorderRadius
                                                                    .circular(
                                                                        16),
                                                          ),
                                                          color: const Color
                                                              .fromARGB(255,
                                                              255, 255, 255),
                                                          child: Padding(
                                                            padding:
                                                                const EdgeInsets
                                                                    .all(16),
                                                            child: Column(
                                                              crossAxisAlignment:
                                                                  CrossAxisAlignment
                                                                      .start,
                                                              children: [
                                                                buildDataRow(
                                                                    "Dealer Name",
                                                                    declaration
                                                                        .dealerName),
                                                                buildDataRow(
                                                                    "Branch Name",
                                                                    declaration
                                                                        .branch),
                                                                buildDataRow(
                                                                    "Declaration Month",
                                                                    formatMonthYear(declaration.month, declaration.year)),
                                                                buildDataRow(
                                                                    "Lifting Qty",
                                                                    declaration
                                                                        .liftingQty),
                                                                buildDataRow(
                                                                    "ASM Approve Status",
                                                                    declaration
                                                                        .asmApproveStatus),
                                                                buildDataRow(
                                                                    "RSM Approve Status",
                                                                    declaration
                                                                        .rsmApproveStatus),
                                                                buildDataRow(
                                                                    "Status",
                                                                    declaration
                                                                        .status),
                                                              ],
                                                            ),
                                                          ),
                                                        );
                                                      }
                                                    })
                                                    .toList(),
                                              )),
                              )
                            ])))
              ])),
        ),
      ),
    );
  }
}

Widget buildDataRow(String title, String? value) {
  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 5.0),
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          flex: 3,
          child: Text(
            "$title:",
            style: const TextStyle(fontSize: 12),
          ),
        ),
        Expanded(
          flex: 5,
          child: Text(
            value ?? "N/A",
            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
          ),
        ),
      ],
    ),
  );
}

Widget buildCustomButton(
  String? label1,
  VoidCallback? onPressed1,
  String? label2,
  VoidCallback? onPressed2,
) {
  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 5.0),
    child: Row(
      children: [
        // First button
        Expanded(
          child: ElevatedButton(
            onPressed: onPressed2,
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 12),
              backgroundColor: Colors.deepOrange,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              textStyle:
                  const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
            ),
            child: Text(label2!),
          ),
        ),
        const SizedBox(width: 10), // space between buttons
        // Second button
        Expanded(
          child: ElevatedButton(
            onPressed: onPressed1,
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 12),
              backgroundColor: Colors.teal,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              textStyle:
                  const TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
            ),
            child: Text(label1!),
          ),
        ),
      ],
    ),
  );
}

class DeclarationRequestDataList {
  String? myLvl;
  String? requestId;
  String? customerCode;
  String? customerId;
  String? month;
  String? year;
  String? dealerName;
  String? branch;
  String? liftingQty;
  String? asmApproveStatus;
  String? rsmApproveStatus;
  String? status;

  DeclarationRequestDataList({
    this.requestId,
    this.customerCode,
    this.customerId,
    this.month,
    this.year,
    this.dealerName,
    this.branch,
    this.liftingQty,
    this.asmApproveStatus,
    this.rsmApproveStatus,
    this.status,
    this.myLvl,
  });

  factory DeclarationRequestDataList.fromJson(Map<String, dynamic> json) {
    return DeclarationRequestDataList(
        requestId: json['id'],
        customerCode: json['customer_code'],
        customerId: json['customer_id'],
        month: json['month'],
        year: json['current_year'],
        dealerName: json['dealer_name'],
        branch: json['branch'],
        liftingQty: json['lifting_qty'],
        asmApproveStatus: json['asm_approve_status'],
        rsmApproveStatus: json['rsm_approve_status'],
        status: json['status'],
        myLvl: json['myLvl']);
  }

  Map<String, dynamic> toJson() {
    return {
      'id': requestId,
      'customer_code': customerCode,
      'customer_id': customerId,
      'month': month,
      'current_year': year,
      'dealer_name': dealerName,
      'branch': branch,
      'lifting_qty': liftingQty,
      'asm_approve_status': asmApproveStatus,
      'rsm_approve_status': rsmApproveStatus,
      'status': status,
      'myLvl': myLvl,
    };
  }

  static Future<List<DeclarationRequestDataList>> getCustomerById(
      String dNScustomerCode) async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }
    final response = await http.get(
      Uri.parse(
          '${AppWebService.baseURL}api_get_dealer_req_list.php?emp_code=$dNScustomerCode'),
    );
    if (response.statusCode == 200) {
      final jsonResponse = json.decode(response.body);
      String lvl = "";

      if (jsonResponse['level'] != null && jsonResponse['level'].isNotEmpty) {
        lvl = jsonResponse['level'];
      }

      if (jsonResponse['result'] != null &&
          jsonResponse['result'] is List &&
          jsonResponse['result'].isNotEmpty) {
        List<DeclarationRequestDataList> dataList =
            (jsonResponse['result'] as List).map((item) {
          final data = DeclarationRequestDataList.fromJson(item);
          data.myLvl = lvl;
          return data;
        }).toList();

        // DeclarationRequestDataList declarationObj =
        //     DeclarationRequestDataList();
        // declarationObj.requestId = ledgerBalanceData['id'];
        // declarationObj.customerCode = ledgerBalanceData['customer_code'];
        // declarationObj.customerId = ledgerBalanceData['customer_id'];
        // declarationObj.month = ledgerBalanceData['month'];
        // declarationObj.year = ledgerBalanceData['current_year'];
        // declarationObj.dealerName = ledgerBalanceData['dealer_name'];
        // declarationObj.branch = ledgerBalanceData['branch'];
        // declarationObj.liftingQty = ledgerBalanceData['lifting_qty'];
        // declarationObj.asmApproveStatus =
        //     ledgerBalanceData['asm_approve_status'];
        // declarationObj.rsmApproveStatus =
        //     ledgerBalanceData['rsm_approve_status'];
        // declarationObj.status = ledgerBalanceData['status'];
        // declarationObj.myLvl = lvl;

        return dataList;
      } else {
        throw Exception('No data found in result');
      }
    } else {
      throw Exception('Failed to load customer list');
    }
  }
}

Widget _buildDropdown({
  required String label,
  required String selectedValue,
  required List<String> items,
  required ValueChanged<String?> onChanged,
  bool enabled = true,
}) {
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
    // disable dropdown if no items loaded yet
    items: !enabled || items.isEmpty
        ? null
        : items.map((String value) {
            return DropdownMenuItem<String>(
              value: value,
              child: Text(
                value,
                style: const TextStyle(fontSize: 13),
                overflow: TextOverflow.ellipsis,
              ),
            );
          }).toList(),
    onChanged: enabled && items.isNotEmpty ? onChanged : null,
    hint: Text(
      items.isEmpty ? 'No data available' : 'Select $label',
      style: const TextStyle(fontSize: 13),
    ),
  );
}

class LabeledTextField extends StatelessWidget {
  final String hintText;
  final TextEditingController controller;
  final String? initialValue;

  const LabeledTextField({
    super.key,
    required this.hintText,
    required this.controller,
    this.initialValue,
  });

  @override
  Widget build(BuildContext context) {
    // Set initial value only if provided and controller is empty
    if (initialValue != null && controller.text.isEmpty) {
      controller.text = initialValue!;
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        TextField(
          controller: controller,
          decoration: InputDecoration(
            hintText: hintText,
            border: const OutlineInputBorder(),
          ),
        ),
      ],
    );
  }
}
