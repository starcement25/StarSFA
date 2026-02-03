import 'package:flutter/material.dart';
import 'package:starsfa/models/starsaathi_ledger_customer_list_model.dart';

class OutstandingScreen extends StatefulWidget {
  const OutstandingScreen({super.key});

  @override
  State<OutstandingScreen> createState() => _OutstandingScreenState();
}

class _OutstandingScreenState extends State<OutstandingScreen> {
  StarsaathiLedgerCustomerList? selectedCustomer;
  Future<StarsaathiLedgerbyId?> getCustomerLedger = Future.value();
  void selectCustomer() async {
    String searchText = '';
    final selectedLocal = await showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) {
          List<StarsaathiLedgerCustomerList> customerMasterList = [];
          return PopScope(
            canPop: false,
            child: Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
              backgroundColor: Colors.white,
              insetPadding: const EdgeInsets.all(20),
              child: Container(
                decoration: const BoxDecoration(
                  // image: DecorationImage(
                  //   image: AssetImage('assets/background.jpg'),
                  //   fit: BoxFit.cover,
                  // ),
                  color: Color.fromARGB(255, 236, 229, 221),
                ),
                padding: const EdgeInsets.all(8.0),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Row(
                      children: [
                        // Close button
                        IconButton(
                          icon: const Icon(
                            Icons.arrow_back,
                            color: Colors.red,
                          ),
                          onPressed: () {
                            Navigator.of(context).pop();
                            Navigator.of(context).pop();
                          },
                        ),
                        Expanded(
                          child: Container(
                            width: double.infinity,
                            padding: const EdgeInsets.symmetric(
                                vertical: 8, horizontal: 10),
                            decoration: BoxDecoration(
                              // color: Colors.black,
                              borderRadius: BorderRadius.circular(10),
                            ),
                            alignment: Alignment.center,
                            child: const Text(
                              'Please select party',
                              style: TextStyle(
                                  color: Colors.red,
                                  fontWeight: FontWeight.bold),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    FutureBuilder<List<StarsaathiLedgerCustomerList>>(
                        future: StarsaathiLedgerCustomerList.getCustomerList(),
                        builder: (context, snapshot) {
                          if (snapshot.connectionState ==
                              ConnectionState.waiting) {
                            return const Center(
                                child: CircularProgressIndicator(
                              color: Colors.black,
                            ));
                          }
                          if (snapshot.hasError) {
                            return Text('Error: ${snapshot.error}');
                          } else {
                            customerMasterList = snapshot.data ?? [];
                            return StatefulBuilder(
                              builder: (BuildContext context, setState) {
                                customerMasterList = customerMasterList
                                    .where((element) => element.customerName
                                        .toString()
                                        .toLowerCase()
                                        .contains(searchText.toLowerCase()))
                                    .toList();
                                if (customerMasterList.isEmpty) {
                                  return Column(
                                    children: [
                                      const Text('No customers found'),
                                      const SizedBox(height: 10),
                                      ElevatedButton(
                                        style: ElevatedButton.styleFrom(
                                          foregroundColor: Colors.white,
                                          backgroundColor: Colors.black,
                                        ),
                                        onPressed: () {
                                          Navigator.of(context).pop();
                                          Navigator.of(context).pop();
                                        },
                                        child: const Text('OK'),
                                      ),
                                    ],
                                  );
                                } else {
                                  return Flexible(
                                    child: Column(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        // Search bar
                                        TextField(
                                          decoration: const InputDecoration(
                                            hintText: 'Search Customer',
                                            prefixIcon: Icon(Icons.search),
                                          ),
                                          onChanged: (value) {
                                            setState(() {
                                              searchText = value;
                                            });
                                          },
                                        ),
                                        const SizedBox(height: 10),
                                        Flexible(
                                          child: ListView.separated(
                                            separatorBuilder:
                                                (context, index) =>
                                                    const Divider(
                                              color: Colors.grey,
                                            ),
                                            shrinkWrap: true,
                                            itemCount:
                                                customerMasterList.length,
                                            itemBuilder: (context, index) {
                                              return ListTile(
                                                onTap: () {
                                                  // close the dialog box
                                                  Navigator.of(context).pop(
                                                      customerMasterList[
                                                          index]);
                                                },
                                                title: Text(
                                                    customerMasterList[index]
                                                            .customerName ??
                                                        ''),
                                                subtitle: Text(
                                                    customerMasterList[index]
                                                            .dnsCustomerCode ??
                                                        ''),
                                              );
                                            },
                                          ),
                                        ),
                                      ],
                                    ),
                                  );
                                }
                              },
                            );
                          }
                        }),
                  ],
                ),
              ),
            ),
          );
        });
    if (selectedLocal != null) {
      setState(() {
        selectedCustomer = selectedLocal;
        getCustomerLedger = StarsaathiLedgerbyId.getCustomerById(
            selectedCustomer?.dnsCustomerCode ?? '');
      });
    }
  }

  @override
  void initState() {
    super.initState();
    // call after build method
    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      selectCustomer();
    });
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
          'Outstanding Ledger',
          style: TextStyle(color: Colors.white),
        ),
      ),
      body: Container(
        decoration: const BoxDecoration(
          // image: DecorationImage(
          //   image: AssetImage('assets/background.jpg'),
          //   fit: BoxFit.cover,
          // ),
          color: Color.fromARGB(255, 236, 229, 221),
        ),
        child: FutureBuilder<StarsaathiLedgerbyId?>(
            future: getCustomerLedger,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(
                  child: CircularProgressIndicator(
                    color: Colors.black,
                  ),
                );
              }
              if (snapshot.hasError) {
                return Text('Error: ${snapshot.error}');
              }
              if (snapshot.data == null) {
                return const Center(child: Text('No data found'));
              } else {
                final customer = snapshot.data as StarsaathiLedgerbyId;
                return Column(
                  children: [
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(10),
                      margin: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        border: Border.all(color: Colors.black),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      alignment: Alignment.center,
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            'Customer Name: ${customer.name1}',
                            style: const TextStyle(fontSize: 16),
                            textAlign: TextAlign.center,
                          ),
                          // Text('Customer Code: ${customer.customerCode}'),
                          Text('₹ ${customer.balance}',
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 26,
                              )),
                          const Text('Outstanding Balance',
                              style: TextStyle(
                                fontWeight: FontWeight.w600,
                                fontSize: 18,
                              )),
                          // Text('Credit Limit: ${customer.creditLimit}'),
                          // Text('Credit Expose: ${customer.creditExpose}'),
                        ],
                      ),
                    ),
                    Expanded(
                      child: customer.ledgerData == null ||
                              customer.ledgerData!.isEmpty
                          ? const Center(child: Text('No Ledger Data Found'))
                          : Container(
                              padding: const EdgeInsets.all(10.0),
                              decoration: BoxDecoration(
                                border: Border.all(color: Colors.black),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              margin: const EdgeInsets.all(10),
                              child: ListView.separated(
                                separatorBuilder: (context, index) =>
                                    const Divider(
                                  color: Colors.grey,
                                ),
                                itemCount: customer.ledgerData?.length ?? 0,
                                itemBuilder: (context, index) {
                                  final ledger = customer.ledgerData?[index];
                                  return Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text('Voucher No: ${ledger?.voucherNo}'),
                                      Text('Amount CR: ${ledger?.amountCr}'),
                                      Text('Amount DR: ${ledger?.amountDr}'),
                                      Text('Narration: ${ledger?.narration}'),
                                      Text(
                                          'Voucher Date: ${ledger?.voucherDate}'),
                                    ],
                                  );
                                },
                              ),
                            ),
                    ),
                  ],
                );
              }
            }),
      ),
    );
  }
}
