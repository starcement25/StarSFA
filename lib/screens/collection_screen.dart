import 'package:flutter/material.dart';
import 'package:starsfa/models/bank_master_class.dart';

class CollectionScreen extends StatefulWidget {
  final String sessionID;
  const CollectionScreen({super.key, required this.sessionID});

  @override
  State<CollectionScreen> createState() => _CollectionScreenState();
}

class _CollectionScreenState extends State<CollectionScreen> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _totalAmountReceivedController =
      TextEditingController();
  final TextEditingController _transactionNumberController =
      TextEditingController();
  final TextEditingController _transactionDateController =
      TextEditingController();
  final TextEditingController _remarksController = TextEditingController();
  final TextEditingController _bankNameController = TextEditingController();
  final List<String> paymentModes = ['Cash', 'Cheque', 'Card', 'NEFT', 'RTGS'];
  final List<IconData> paymentIcons = [
    Icons.payments_rounded,
    Icons.request_quote,
    Icons.credit_card,
    Icons.assured_workload,
    Icons.assured_workload,
  ];
  String paymentMode = 'Cash';

  void getPaymentMode() {
    showDialog(
        context: context,
        builder: (context) {
          return PopScope(
            canPop: false,
            child: AlertDialog(
              title: const Text('Select Payment Mode'),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  for (int i = 0; i < paymentModes.length; i++)
                    ListTile(
                      leading: Icon(paymentIcons[i]),
                      title: Text(paymentModes[i]),
                      onTap: () {
                        setState(() {
                          paymentMode = paymentModes[i];
                        });
                        Navigator.pop(context);
                      },
                    ),
                ],
              ),
            ),
          );
        });
  }

  getLabel(String? label) {
    if (label != null) {
      return Container(
        padding: const EdgeInsets.all(8.0),
        width: double.infinity,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          color: Colors.grey[200],
          borderRadius: BorderRadius.circular(5),
        ),
        child: Text(label,
            style: const TextStyle(
              fontSize: 16,
              color: Colors.black,
              fontWeight: FontWeight.bold,
            )),
      );
    } else {
      return const Text('No display name found');
    }
  }

  Widget getDataInputField(
      {required TextEditingController controller,
      required String labelText,
      required String hintText,
      required TextInputType keyboardType}) {
    return Container(
      margin: const EdgeInsets.all(10),
      padding: const EdgeInsets.all(8.0),
      decoration: BoxDecoration(
        // border: Border.all(color: Colors.grey),
        borderRadius: BorderRadius.circular(5),
        boxShadow: const [
          BoxShadow(
            color: Colors.black26,
            blurRadius: 5,
            spreadRadius: 2.5,
          ),
        ],
        color: Colors.white,
      ),
      child: Column(
        children: [
          getLabel(labelText),
          Container(
            margin: const EdgeInsets.symmetric(vertical: 10),
            // padding: const EdgeInsets.all(8.0),
            width: double.infinity,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              // border: Border.all(color: Colors.black),
              borderRadius: BorderRadius.circular(5),
            ),
            child: TextFormField(
              controller: controller,
              validator: (value) {
                if (value!.isEmpty) {
                  return 'Please select Destination';
                }
                return null;
              },
              keyboardType: keyboardType,
              // enabled: false,
              // align text to center
              textAlign: TextAlign.center,
              decoration: InputDecoration(
                hintText: hintText,
                focusColor: Colors.black,
                // Show border
                border: const OutlineInputBorder(
                  borderSide: BorderSide(color: Colors.black),
                ),
                errorBorder: const OutlineInputBorder(
                  borderSide: BorderSide(color: Colors.red),
                ),
                disabledBorder: const OutlineInputBorder(
                  borderSide: BorderSide(color: Colors.black),
                ),
              ),
              style: const TextStyle(
                fontSize: 16,
                color: Colors.black,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget getDataInputFieldBankName(
      {required TextEditingController controller,
      required String labelText,
      required String hintText}) {
    return Container(
      margin: const EdgeInsets.all(10),
      padding: const EdgeInsets.all(8.0),
      decoration: BoxDecoration(
        // border: Border.all(color: Colors.grey),
        borderRadius: BorderRadius.circular(5),
        boxShadow: const [
          BoxShadow(
            color: Colors.black26,
            blurRadius: 5,
            spreadRadius: 2.5,
          ),
        ],
        color: Colors.white,
      ),
      child: Column(
        children: [
          getLabel(labelText),
          InkWell(
            onTap: () async {
              final values = await BankMasterDB.getAllBankMaster();
              getBankNameSekectionDialog(values);
            },
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10),
              // padding: const EdgeInsets.all(8.0),
              width: double.infinity,
              alignment: Alignment.center,
              decoration: BoxDecoration(
                // border: Border.all(color: Colors.black),
                borderRadius: BorderRadius.circular(5),
              ),
              child: TextFormField(
                controller: controller,
                validator: (value) {
                  if (value!.isEmpty) {
                    return 'Please select Destination';
                  }
                  return null;
                },
                keyboardType: TextInputType.text,
                enabled: false,
                // align text to center
                textAlign: TextAlign.center,
                decoration: InputDecoration(
                  hintText: hintText,
                  focusColor: Colors.black,
                  // Show border
                  border: const OutlineInputBorder(
                    borderSide: BorderSide(color: Colors.black),
                  ),
                  errorBorder: const OutlineInputBorder(
                    borderSide: BorderSide(color: Colors.red),
                  ),
                  disabledBorder: const OutlineInputBorder(
                    borderSide: BorderSide(color: Colors.black),
                  ),
                ),
                style: const TextStyle(
                  fontSize: 16,
                  color: Colors.black,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  getBankNameSekectionDialog(List<BankMasterDB> values) {
    return showDialog(
      context: context,
      builder: (context) {
        return StatefulBuilder(
            builder: (BuildContext context, StateSetter setState) {
          return Dialog(
            child: Container(
              // margin: const EdgeInsets.all(10),
              padding: const EdgeInsets.all(8.0),
              decoration: BoxDecoration(
                // border: Border.all(color: Colors.grey),
                borderRadius: BorderRadius.circular(5),
                boxShadow: const [
                  BoxShadow(
                    color: Colors.black26,
                    blurRadius: 5,
                    spreadRadius: 2.5,
                  ),
                ],
                color: Colors.white,
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    padding: const EdgeInsets.all(8.0),
                    width: double.infinity,
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      borderRadius: BorderRadius.circular(5),
                    ),
                    child: const Text('Select Bank Name',
                        style: TextStyle(
                          fontSize: 18,
                          color: Colors.black,
                          fontWeight: FontWeight.bold,
                        )),
                  ),

                  // Search bar
                  TextField(
                    decoration: const InputDecoration(
                      hintText: 'Search',
                      prefixIcon: Icon(Icons.search),
                    ),
                    onChanged: (value) {
                      setState(() {
                        values = values
                            .where((element) => element.bankName
                                .toString()
                                .toLowerCase()
                                .contains(value.toLowerCase()))
                            .toList();
                      });
                    },
                  ),
                  const SizedBox(height: 10),
                  Flexible(
                    child: ListView.separated(
                      itemCount: values.length,
                      separatorBuilder: (context, index) => const Divider(),
                      itemBuilder: (context, index) {
                        return ListTile(
                          title: Text(
                            values[index].bankName ?? '',
                          ),
                          onTap: () {
                            _bankNameController.text =
                                values[index].bankName ?? '';
                            Navigator.of(context).pop();
                          },
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          );
        });
      },
    );
  }

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      getPaymentMode();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        appBar: AppBar(
          leading: IconButton(
            icon: const Icon(Icons.arrow_back, color: Colors.white),
            onPressed: () {
              Navigator.pop(context);
            },
          ),
          backgroundColor: Colors.red,
          title: Text(
            'Collection ($paymentMode)',
            style: const TextStyle(
              color: Colors.white,
            ),
          ),
        ),
        body: Column(
          children: [
            Expanded(
              child: SingleChildScrollView(
                child: Form(
                  key: _formKey,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Total Amount Received
                      getDataInputField(
                          controller: _totalAmountReceivedController,
                          labelText: 'Total Amount Received',
                          hintText: 'Enter Total Amount Received',
                          keyboardType: TextInputType.number),
                      // Transaction Number
                      if (paymentMode == 'Cheque')
                        getDataInputField(
                            controller: _transactionNumberController,
                            labelText: 'Cheque Number',
                            hintText: 'Enter Cheque Number',
                            keyboardType: TextInputType.number),
                      if (paymentMode == 'NEFT' || paymentMode == 'RTGS')
                        getDataInputField(
                            controller: _transactionNumberController,
                            labelText: 'UTR Number',
                            hintText: 'Enter UTR Number',
                            keyboardType: TextInputType.number),
                      // Bank Name
                      if (paymentMode == 'NEFT' || paymentMode == 'RTGS')
                        getDataInputFieldBankName(
                          controller: _bankNameController,
                          labelText: 'Bank Name',
                          hintText: 'Enter Bank Name',
                        ),
                      // Transaction Date
                      if (paymentMode != 'Cash' && paymentMode != 'Card')
                        getDataInputField(
                            controller: _transactionDateController,
                            labelText: 'Transaction Date',
                            hintText: 'Enter Date (DD/MM/YYYY)',
                            keyboardType: TextInputType.datetime),
                      // Remarks
                      getDataInputField(
                          controller: _remarksController,
                          labelText: 'Remarks',
                          hintText: 'Enter Remarks',
                          keyboardType: TextInputType.text),
                    ],
                  ),
                ),
              ),
            ),
            InkWell(
              onTap: () => submit(context),
              child: Container(
                margin: const EdgeInsets.all(10),
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: Colors.red,
                  borderRadius: BorderRadius.circular(5),
                ),
                child: const Center(
                  child: Text(
                    'Submit Collection',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                    ),
                  ),
                ),
              ),
            ),
          ],
        ));
  }

  void submit(BuildContext context) {
    if (_formKey.currentState!.validate()) {
      // Submit the form
      // Call the API
      // Show the response
      // Clear the form
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Collection Submitted Successfully'),
        ),
      );
      _totalAmountReceivedController.clear();
      _transactionNumberController.clear();
      _transactionDateController.clear();
      _remarksController.clear();
      _bankNameController.clear();
    }
  }
}
