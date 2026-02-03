import 'dart:convert';
import 'dart:developer';

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

  @override
  void initState() {
    super.initState();
    fetchDeclarationData();
  }

  void fetchDeclarationData() async {
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      log("the id is ${userDetails!.message} ${userDetails!.empCode} ${userDetails!.empName} ${userDetails!.saleAccess} ${userDetails!.newPassword} ${userDetails!.deviceid}");
      declarationData = await DeclarationRequestDataList.getCustomerById(
          userDetails!.empCode ?? '');
    } catch (e) {
      print("Error fetching data: $e");
    } finally {
      setState(() {
        isLoading = false;
      });
    }
  }

  void asmApproveButtonClick(DeclarationRequestDataList declaration) async {
    log('ASM approve button click');
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      final headers = {
        'Content-Type': 'application/json',
      };
      final Map<String, String> data = {
        'table_id': declaration!.requestId ?? '',
        'dealer_id': declaration!.customerId ?? '',
        'emp_code': userDetails!.empCode ?? '',
        'status': 'Approve',
      };
      log("Request Data: ${jsonEncode(data)}");
      final response = await http.post(
        Uri.parse(
            'https://sfa.starcement.co.in/api_approve_reject_dealer_exclusive.php'),
        headers: headers,
        body: jsonEncode(data),
      );
      log("Status Code: ${response.statusCode}");
      log("Response Body: ${response.body}");
      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['process_status'] == "Yes") {
          log('✅ Approval successful');
          fetchDeclarationData();
        } else {
          log('❌ Approval failed: ${responseData['error']}');
          showDialog(
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
      } else {
        log("HTTP Error: ${response.statusCode}");
      }
    } catch (e) {
      log("ASM approval failed: $e");
    }
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
    log("Reject reason: $reason");
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      final headers = {
        'Content-Type': 'application/json',
      };
      final Map<String, String> data = {
        'table_id': declaration!.requestId ?? '',
        'dealer_id': declaration!.customerId ?? '',
        'emp_code': userDetails!.empCode ?? '',
        'status': 'Reject',
        'reason': reason,
      };
      log("Reject Request Data: ${jsonEncode(data)}");
      final response = await http.post(
        Uri.parse(
            'https://sfa.starcement.co.in/api_approve_reject_dealer_exclusive.php'),
        headers: headers,
        body: jsonEncode(data),
      );
      log("Reject Status Code: ${response.statusCode}");
      log("Reject Response Body: ${response.body}");
      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['process_status'] == "Yes") {
          log('✅ Rejection successful');
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text("Request rejected successfully.")),
          );
          fetchDeclarationData();
        } else {
          showDialog(
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
      } else {
        log("❌ HTTP Error: ${response.statusCode}");
      }
    } catch (e) {
      log("❌ ASM rejection failed: $e");
    }
  }

  void rsmApproveButtonClick(DeclarationRequestDataList declaration) async {
    log('RSM approve button click');
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      final headers = {
        'Content-Type': 'application/json',
      };
      final Map<String, String> data = {
        'table_id': declaration!.requestId ?? '',
        'dealer_id': declaration!.customerId ?? '',
        'emp_code': userDetails!.empCode ?? '',
        'status': 'Approve',
      };
      log("Request Data: ${jsonEncode(data)}");
      final response = await http.post(
        Uri.parse(
            'https://sfa.starcement.co.in/api_approve_reject_dealer_exclusive.php'),
        headers: headers,
        body: jsonEncode(data),
      );
      log("Status Code: ${response.statusCode}");
      log("Response Body: ${response.body}");
      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['process_status'] == "Yes") {
          log('✅ Approval successful');
          fetchDeclarationData();
        } else {
          log('❌ Approval failed: ${responseData['error']}');
          showDialog(
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
      } else {
        log("HTTP Error: ${response.statusCode}");
      }
    } catch (e) {
      log("RSM approval failed: $e");
    }
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
    log("Reject reason: $reason");
    try {
      final userDetails = await UserLoginClass.getLocalUser();
      final headers = {
        'Content-Type': 'application/json',
      };
      final Map<String, String> data = {
        'table_id': declaration!.requestId ?? '',
        'dealer_id': declaration!.customerId ?? '',
        'emp_code': userDetails!.empCode ?? '',
        'status': 'Reject',
        'reason': reason,
      };
      log("Reject Request Data: ${jsonEncode(data)}");
      final response = await http.post(
        Uri.parse(
            'https://sfa.starcement.co.in/api_approve_reject_dealer_exclusive.php'),
        headers: headers,
        body: jsonEncode(data),
      );
      log("Reject Status Code: ${response.statusCode}");
      log("Reject Response Body: ${response.body}");
      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        if (responseData['process_status'] == "Yes") {
          log('✅ Rejection successful');
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text("Request rejected successfully.")),
          );
          fetchDeclarationData();
        } else {
          showDialog(
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
      } else {
        log("❌ HTTP Error: ${response.statusCode}");
      }
    } catch (e) {
      log("❌ ASM rejection failed: $e");
    }
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
        body: Container(
            color: const Color.fromARGB(255, 255, 255, 255),
            padding: const EdgeInsets.all(16),
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : declarationData == null
                    ? const Center(child: Text('No data found'))
                    : declarationData![0].myLvl == 'L3'
                        ? SingleChildScrollView(
                            child: Column(
                            children: declarationData!.map((declaration) {
                              if (declaration!.asmApproveStatus == 'Pending') {
                                return Card(
                                  elevation: 6,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  color:
                                      const Color.fromARGB(255, 255, 255, 255),
                                  child: Padding(
                                    padding: const EdgeInsets.all(16),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        buildDataRow("Dealer Name",
                                            declaration!.dealerName),
                                        buildDataRow(
                                            "Branch Name", declaration!.branch),
                                        buildDataRow(
                                            "Declaration Month",
                                            declaration!.month! +
                                                "/" +
                                                declaration!.year!),
                                        buildDataRow("Lifting Qty",
                                            declaration!.liftingQty),
                                        buildDataRow("ASM Approve Status",
                                            declaration!.asmApproveStatus),
                                        buildDataRow("Declaration Status",
                                            declaration!.status),
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
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  color:
                                      const Color.fromARGB(255, 255, 255, 255),
                                  child: Padding(
                                    padding: const EdgeInsets.all(16),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        buildDataRow("Dealer Name",
                                            declaration!.dealerName),
                                        buildDataRow(
                                            "Branch Name", declaration!.branch),
                                        buildDataRow(
                                            "Declaration Month",
                                            declaration!.month! +
                                                "/" +
                                                declaration!.year!),
                                        buildDataRow("Lifting Qty",
                                            declaration!.liftingQty),
                                        buildDataRow("ASM Approve Status",
                                            declaration!.asmApproveStatus),
                                        buildDataRow("Declaration Status",
                                            declaration!.status),
                                      ],
                                    ),
                                  ),
                                );
                              }
                            }).toList(),
                          ))
                        : SingleChildScrollView(
                            child: Column(
                            children: declarationData!.map((declaration) {
                              if (declaration!.rsmApproveStatus == 'Pending') {
                                return Card(
                                  elevation: 6,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  color:
                                      const Color.fromARGB(255, 255, 255, 255),
                                  child: Padding(
                                    padding: const EdgeInsets.all(16),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        buildDataRow("Dealer Name",
                                            declaration!.dealerName),
                                        buildDataRow(
                                            "Branch Name", declaration!.branch),
                                        buildDataRow(
                                            "Declaration Month",
                                            declaration!.month! +
                                                "/" +
                                                declaration!.year!),
                                        buildDataRow("Lifting Qty",
                                            declaration!.liftingQty),
                                        buildDataRow("ASM Approve Status",
                                            declaration!.asmApproveStatus),
                                        buildDataRow("RSM Approve Status",
                                            declaration!.rsmApproveStatus),
                                        buildDataRow(
                                            "Status", declaration!.status),
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
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  color:
                                      const Color.fromARGB(255, 255, 255, 255),
                                  child: Padding(
                                    padding: const EdgeInsets.all(16),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        buildDataRow("Dealer Name",
                                            declaration!.dealerName),
                                        buildDataRow(
                                            "Branch Name", declaration!.branch),
                                        buildDataRow(
                                            "Declaration Month",
                                            declaration!.month! +
                                                "/" +
                                                declaration!.year!),
                                        buildDataRow("Lifting Qty",
                                            declaration!.liftingQty),
                                        buildDataRow("ASM Approve Status",
                                            declaration!.asmApproveStatus),
                                        buildDataRow("RSM Approve Status",
                                            declaration!.rsmApproveStatus),
                                        buildDataRow(
                                            "Status", declaration!.status),
                                      ],
                                    ),
                                  ),
                                );
                              }
                            }).toList(),
                          ))),
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
          'https://sfa.starcement.co.in/api_get_dealer_req_list.php?emp_code=$dNScustomerCode'),
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
        final ledgerBalanceData = jsonResponse['result'][0];
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
