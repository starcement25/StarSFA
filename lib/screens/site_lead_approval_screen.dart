// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:starsfa/models/site_lead_download_approval_class.dart';
import 'package:starsfa/models/user_login_class.dart';

class SiteLeadApprovalScreen extends StatefulWidget {
  const SiteLeadApprovalScreen({super.key});

  @override
  State<SiteLeadApprovalScreen> createState() => _SiteLeadApprovalScreenState();
}

class _SiteLeadApprovalScreenState extends State<SiteLeadApprovalScreen> {
  late Future<List<SiteLeadDownloadApprovalClass>> future;
  String from = '';
  String to = '';
  int selectedTabIndex = 0;
  List<String> status = ['Pending', 'Approved', 'Rejected'];
  TextEditingController actualDeliveryDateController = TextEditingController();
  TextEditingController remarksController = TextEditingController();
  TextEditingController reasonForRejectionController = TextEditingController();

  initialize() async {
    final user = await UserLoginClass.getLocalUser();
    setState(() {
      future = SiteLeadDownloadApprovalClass.getSiteLeadDownloadApproval(
          user?.empCode ?? '', from, to);
    });
  }

  Future<String> datePicker() async {
    // Implement date picker
    final DateTime? picked = await showDatePicker(
        context: context,
        initialDate: DateTime.now(),
        firstDate: DateTime.now().subtract(const Duration(days: 1 * 365)),
        lastDate: DateTime.now());
    if (picked != null) {
      const String dateFormat = 'yyyy-MM-dd';
      final String formattedDate = DateFormat(dateFormat).format(picked);
      return formattedDate;
    } else {
      return '';
    }
  }

  Future<bool> updateStatus(String surveyId, String status) async {
    // Implement update status
    // if Status is Approved, show Actual Delivery Date and Remarks
    // if Status is Rejected, show Remarks and Reason for Rejection

    // Show pop up dialog
    showDialog(
      context: context,
      builder: (context) {
        return SimpleDialog(
          contentPadding: const EdgeInsets.all(10),
          children: [
            const SizedBox(
              height: 10,
            ),
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.red,
                borderRadius: BorderRadius.circular(5),
              ),
              alignment: Alignment.center,
              child: Text(
                'Input Details for ${status == 'Approved' ? 'Approval' : 'Rejection'}',
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ),
            (status == 'Approved')
                ? InkWell(
                    onTap: () async {
                      final String date = await datePicker();
                      print('Date: $date');
                      actualDeliveryDateController.text = date;
                    },
                    child: Container(
                      width: double.infinity,
                      margin: const EdgeInsets.only(top: 10),
                      padding: const EdgeInsets.all(5),
                      decoration: BoxDecoration(
                        color: Colors.grey[200],
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Column(
                        children: [
                          const Text(
                            'Actual Delivery Date',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(
                            height: 10,
                          ),
                          TextField(
                            controller: actualDeliveryDateController,
                            textAlign: TextAlign.center,
                            enabled: false,
                            decoration: const InputDecoration(
                              contentPadding: EdgeInsets.all(5.0),

                              hintText: 'Select Date',
                              focusColor: Colors.black,
                              // Show border
                              border: OutlineInputBorder(
                                borderSide: BorderSide(color: Colors.black),
                              ),
                              errorBorder: OutlineInputBorder(
                                borderSide: BorderSide(color: Colors.red),
                              ),
                            ),
                            style: const TextStyle(
                              fontSize: 16,
                              color: Colors.black,
                            ),
                          ),
                        ],
                      ),
                    ),
                  )
                : const SizedBox(),
            // Remarks
            Container(
              width: double.infinity,
              margin: const EdgeInsets.only(top: 10),
              padding: const EdgeInsets.all(5),
              decoration: BoxDecoration(
                color: Colors.grey[200],
                borderRadius: BorderRadius.circular(5),
              ),
              child: Column(
                children: [
                  const Text(
                    'Remarks',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(
                    height: 10,
                  ),
                  TextField(
                    controller: remarksController,
                    textAlign: TextAlign.center,
                    decoration: const InputDecoration(
                      contentPadding: EdgeInsets.all(5.0),
                      hintText: 'Enter Remarks',
                      focusColor: Colors.black,
                      // Show border
                      border: OutlineInputBorder(
                        borderSide: BorderSide(color: Colors.black),
                      ),
                      errorBorder: OutlineInputBorder(
                        borderSide: BorderSide(color: Colors.red),
                      ),
                    ),
                    style: const TextStyle(
                      fontSize: 16,
                      color: Colors.black,
                    ),
                  ),
                ],
              ),
            ),
            (status == 'Rejected')
                ? Container(
                    width: double.infinity,
                    margin: const EdgeInsets.only(top: 10),
                    padding: const EdgeInsets.all(5),
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      borderRadius: BorderRadius.circular(5),
                    ),
                    child: Column(
                      children: [
                        const Text(
                          'Reason for Rejection',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(
                          height: 10,
                        ),
                        TextField(
                          controller: reasonForRejectionController,
                          textAlign: TextAlign.center,
                          decoration: const InputDecoration(
                            contentPadding: EdgeInsets.all(5.0),
                            hintText: 'Enter Reason for Rejection',
                            focusColor: Colors.black,
                            // Show border
                            border: OutlineInputBorder(
                              borderSide: BorderSide(color: Colors.black),
                            ),
                            errorBorder: OutlineInputBorder(
                              borderSide: BorderSide(color: Colors.red),
                            ),
                          ),
                          style: const TextStyle(
                            fontSize: 16,
                            color: Colors.black,
                          ),
                        ),
                      ],
                    ),
                  )
                : const SizedBox(),
            const SizedBox(
              height: 10,
            ),

            Container(
              margin: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: status == 'Approved' ? Colors.green : Colors.red,
                borderRadius: BorderRadius.circular(10),
              ),
              child: TextButton(
                onPressed: () async {
                  // check if all fields are filled
                  if (status == 'Approved') {
                    if (actualDeliveryDateController.text.isEmpty ||
                        remarksController.text.isEmpty) {
                      // Show error message - Please fill all fields
                      _showErrorMessage('Please fill all fields');
                      return;
                    }
                  } else {
                    if (remarksController.text.isEmpty ||
                        reasonForRejectionController.text.isEmpty) {
                      // Show error message - Please fill all fields
                      _showErrorMessage('Please fill all fields');
                      return;
                    }
                  }
                  // Implement update status
                  final response = await SiteLeadDownloadApprovalClass
                      .updateSiteLeadApproval(
                          surveyId,
                          status,
                          actualDeliveryDateController.text,
                          remarksController.text,
                          reasonForRejectionController.text);
                  if (response == '1') {
                    // Show success message
                    _showSuccessMessage('Status updated successfully');
                    // Refresh data
                    initialize();
                  } else {
                    // Show error message
                    _showErrorMessage('Failed to update status');
                  }
                  // Close dialog
                  Navigator.of(context).pop();
                },
                child: Text(
                  status == 'Approved' ? 'Approve' : 'Reject',
                  style: const TextStyle(
                    color: Colors.white,
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
    return true;
  }

  void _showSuccessMessage(String message) {
    SnackBar snackBar = SnackBar(
      content: Text(message,
          style: const TextStyle(
            fontSize: 16,
          )),
      backgroundColor: Colors.green,
    );

    ScaffoldMessenger.of(context).showSnackBar(snackBar);
  }

  void _showErrorMessage(String message) {
    AlertDialog snackBar = AlertDialog(
      title: const Text(
        'Error',
        style: TextStyle(color: Colors.red),
      ),
      content: Text(message,
          style: const TextStyle(
            fontSize: 16,
          )),
      actions: [
        TextButton(
          onPressed: () {
            Navigator.of(context).pop();
          },
          child: const Text('OK'),
        ),
      ],
    );

    showDialog(
      context: context,
      builder: (context) {
        return snackBar;
      },
    );
  }

  @override
  void initState() {
    super.initState();
    future =
        SiteLeadDownloadApprovalClass.getSiteLeadDownloadApproval('', from, to);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
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
          'Site Lead Approval',
          style: TextStyle(color: Colors.white),
        ),
      ),
      backgroundColor: const Color(0xF1F5F7FF),
      body: Column(
        children: [
          // From date
          Container(
            margin: const EdgeInsets.all(6),
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Row(
              children: [
                const Expanded(
                  child: Text(
                    'From Date',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                Expanded(
                  child: InkWell(
                    onTap: () async {
                      final String date = await datePicker();
                      setState(() {
                        from = date;
                      });
                    },
                    child: Container(
                      margin: const EdgeInsets.only(left: 10),
                      padding: const EdgeInsets.all(5),
                      decoration: BoxDecoration(
                        color: Colors.grey[200],
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Text(
                        from == '' ? 'Select Date' : from,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
          // To date
          Container(
            margin: const EdgeInsets.all(6),
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Row(
              children: [
                const Expanded(
                  child: Text(
                    'To Date',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                Expanded(
                  child: InkWell(
                    onTap: () async {
                      final String date = await datePicker();
                      setState(() {
                        to = date;
                      });
                    },
                    child: Container(
                      margin: const EdgeInsets.only(left: 10),
                      padding: const EdgeInsets.all(5),
                      decoration: BoxDecoration(
                        color: Colors.grey[200],
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: Text(
                        to == '' ? 'Select Date' : to,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
          // Submit button
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: InkWell(
              onTap: () {
                setState(() {
                  initialize();
                });
              },
              child: Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: Colors.red,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  alignment: Alignment.center,
                  child: const Text('Submit',
                      style: TextStyle(color: Colors.white))),
            ),
          ),
          Expanded(
            child: FutureBuilder<List<SiteLeadDownloadApprovalClass>>(
                future: future,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return const Center(child: CircularProgressIndicator());
                  }
                  if (snapshot.hasError) {
                    return Center(child: Text('Error: ${snapshot.error}'));
                  }
                  List<SiteLeadDownloadApprovalClass> data =
                      snapshot.data ?? [];
                  data = data
                      .where((element) =>
                          element.status?.toLowerCase() ==
                          status[selectedTabIndex].toLowerCase())
                      .toList();
                  return Column(
                    children: [
                      // 3 Tabs - Pending, Approved, Rejected
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          Flexible(
                            flex: 1,
                            child: InkWell(
                              onTap: () {
                                setState(() {
                                  selectedTabIndex = 0;
                                });
                              },
                              child: Container(
                                margin: const EdgeInsets.only(
                                  right: 10,
                                  left: 10,
                                ),
                                padding: const EdgeInsets.all(10),
                                decoration: BoxDecoration(
                                  color: selectedTabIndex == 0
                                      ? Colors.red
                                      : Colors.grey[400],
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                alignment: Alignment.center,
                                child: Text('Pending',
                                    style: TextStyle(
                                        color: selectedTabIndex == 0
                                            ? Colors.white
                                            : Colors.black)),
                              ),
                            ),
                          ),
                          Flexible(
                            flex: 1,
                            child: InkWell(
                              onTap: () {
                                setState(() {
                                  selectedTabIndex = 1;
                                });
                              },
                              child: Container(
                                margin: const EdgeInsets.only(right: 10),
                                padding: const EdgeInsets.all(10),
                                decoration: BoxDecoration(
                                  color: selectedTabIndex == 1
                                      ? Colors.red
                                      : Colors.grey[400],
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                alignment: Alignment.center,
                                child: Text('Approved',
                                    style: TextStyle(
                                        color: selectedTabIndex == 1
                                            ? Colors.white
                                            : Colors.black)),
                              ),
                            ),
                          ),
                          Flexible(
                            flex: 1,
                            child: InkWell(
                              onTap: () {
                                setState(() {
                                  selectedTabIndex = 2;
                                });
                              },
                              child: Container(
                                margin: const EdgeInsets.only(right: 10),
                                padding: const EdgeInsets.all(10),
                                decoration: BoxDecoration(
                                  color: selectedTabIndex == 2
                                      ? Colors.red
                                      : Colors.grey[400],
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                alignment: Alignment.center,
                                child: Text('Rejected',
                                    style: TextStyle(
                                        color: selectedTabIndex == 2
                                            ? Colors.white
                                            : Colors.black)),
                              ),
                            ),
                          ),
                        ],
                      ),
                      Expanded(
                        child: data.isEmpty
                            ? const Center(
                                child: Text(
                                'No data found',
                                style: TextStyle(fontSize: 20),
                              ))
                            : ListView.builder(
                                itemCount: data.length,
                                itemBuilder: (context, index) {
                                  final item = data[index];
                                  Map<String, String> dataItem = {
                                    'Customer Name': item.customerName ?? '',
                                    'Contact No': item.contactNo ?? '',
                                    'Address': item.address ?? '',
                                    'Dealer Name': item.dealerName ?? '',
                                    'Dealer Code': item.dealerCode ?? '',
                                    'Visit Type': item.visitType ?? '',
                                    'Product': item.product ?? '',
                                    'No of Bag': item.noOfBag ?? '',
                                    'Request Date': item.requestDate ?? '',
                                    'Status': item.status ?? '',
                                  };
                                  if (selectedTabIndex == 1) {
                                    dataItem.addAll({
                                      'Actual Delivery Date':
                                          item.actualDeliveryDate ?? '',
                                      'Remarks': item.remarks ?? '',
                                    });
                                  } else if (selectedTabIndex == 2) {
                                    dataItem.addAll({
                                      'Remarks': item.remarks ?? '',
                                      'Reason for Rejection':
                                          item.reasonForRejection ?? '',
                                    });
                                  }
                                  return Container(
                                    margin: const EdgeInsets.all(10),
                                    decoration: BoxDecoration(
                                      color: Colors.white60,
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: Column(
                                      children: [
                                        ...dataItem.entries.map((e) {
                                          return SiteLeadDataRow(
                                            label: e.key,
                                            value: e.value,
                                          );
                                        }).toList(),
                                        const SizedBox(
                                          height: 10,
                                        ),
                                        // Approve and Reject button
                                        if (selectedTabIndex == 0)
                                          Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.spaceEvenly,
                                            children: [
                                              Flexible(
                                                flex: 1,
                                                child: InkWell(
                                                  onTap: () {
                                                    // Implement approve
                                                    print('Approve');
                                                    updateStatus(
                                                        item.surveyId ?? '',
                                                        'Approved');
                                                  },
                                                  child: Container(
                                                    width: double.infinity,
                                                    margin:
                                                        const EdgeInsets.only(
                                                      left: 10,
                                                      right: 10,
                                                    ),
                                                    padding:
                                                        const EdgeInsets.all(
                                                            10),
                                                    decoration: BoxDecoration(
                                                      color: Colors.green,
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                              10),
                                                    ),
                                                    alignment: Alignment.center,
                                                    child: const Text('Approve',
                                                        style: TextStyle(
                                                            color:
                                                                Colors.white)),
                                                  ),
                                                ),
                                              ),
                                              Flexible(
                                                flex: 1,
                                                child: InkWell(
                                                  onTap: () {
                                                    // Implement reject
                                                    print('Reject');
                                                    updateStatus(
                                                        item.surveyId ?? '',
                                                        'Rejected');
                                                  },
                                                  child: Container(
                                                    width: double.infinity,
                                                    margin:
                                                        const EdgeInsets.only(
                                                      left: 10,
                                                      right: 10,
                                                    ),
                                                    padding:
                                                        const EdgeInsets.all(
                                                            10),
                                                    decoration: BoxDecoration(
                                                      color: Colors.red,
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                              10),
                                                    ),
                                                    alignment: Alignment.center,
                                                    child: const Text('Reject',
                                                        style: TextStyle(
                                                            color:
                                                                Colors.white)),
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                        const SizedBox(
                                          height: 10,
                                        ),
                                      ],
                                    ),
                                  );
                                },
                              ),
                      ),
                    ],
                  );
                }),
          ),
        ],
      ),
    );
  }
}

class SiteLeadDataRow extends StatelessWidget {
  final String label;
  final String value;
  const SiteLeadDataRow({
    super.key,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(8.0).copyWith(bottom: 0),
      child: Row(
        children: [
          Expanded(
            flex: 2,
            child: Text(
              label,
              style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
          Expanded(
            flex: 3,
            child: Container(
              margin: const EdgeInsets.only(left: 10),
              padding: const EdgeInsets.all(5),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(5),
              ),
              child: Text(
                value,
                style: const TextStyle(
                  fontSize: 14,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
