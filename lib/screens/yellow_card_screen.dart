import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/route_master_class.dart';
import 'package:starsfa/models/yellow_card_date_validation_customerwise_class.dart';
import 'package:starsfa/screens/home_screen.dart';
import 'package:starsfa/screens/route_plan_screen.dart';

class YellowCardScreen extends StatefulWidget {
  const YellowCardScreen({super.key});

  @override
  State<YellowCardScreen> createState() => _YellowCardScreenState();
}

class _YellowCardScreenState extends State<YellowCardScreen> {
  String routeCode = '';
  String customerCode = '';
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _dateController = TextEditingController();
  final TextEditingController _challanNumberController =
      TextEditingController();
  final TextEditingController _quantityController = TextEditingController();
  final TextEditingController _productsController = TextEditingController();

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

  Widget getDate() {
    final getItem = YellowCardDateValidationCustomerWiseDB
        .getYellowCardDateValidationCustomerWiseDB(customerCode);
    return FutureBuilder<YellowCardDateValidationCustomerWiseDB?>(
        future: getItem,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }
          final YellowCardDateValidationCustomerWiseDB? item = snapshot.data;
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
                getLabel('Date'),
                InkWell(
                  onTap: () async {
                    final DateTime? picked = await showDatePicker(
                      context: context,
                      initialDate: DateFormat('yyyy-mm-dd')
                          .parse(item?.validationFrom ?? '2001-01-01'),
                      firstDate: DateFormat('yyyy-mm-dd')
                          .parse(item?.validationFrom ?? '2001-01-01'),
                      lastDate: DateFormat('yyyy-mm-dd')
                          .parse(item?.validationTo ?? '2001-01-01'),
                    );
                    if (picked != null) {
                      // 26th September 2021
                      const String dateFormat = 'dd MMMM yyyy';
                      final String formattedDate =
                          DateFormat(dateFormat).format(picked);
                      _dateController.text = formattedDate;
                    }
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
                      controller: _dateController,
                      enabled: false,
                      // align text to center
                      textAlign: TextAlign.center,
                      decoration: const InputDecoration(
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
                  ),
                ),
              ],
            ),
          );
        });
  }

  Widget getProducts() {
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
          getLabel('Products'),
          Container(
            margin: const EdgeInsets.symmetric(vertical: 10),
            // padding: const EdgeInsets.all(8.0),
            width: double.infinity,
            alignment: Alignment.center,
            decoration: BoxDecoration(
              // border: Border.all(color: Colors.black),
              borderRadius: BorderRadius.circular(5),
            ),
            child: InkWell(
              onTap: () async {
                List<String> products = [
                  'OPC',
                  'PPC',
                  'ARC',
                ];
                final String? selectedProduct = await showDialog(
                    context: context,
                    builder: (context) {
                      return Dialog(
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(10)),
                        backgroundColor: Colors.white,
                        insetPadding: const EdgeInsets.all(20),
                        child: Padding(
                          padding: const EdgeInsets.all(8.0),
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Container(
                                width: double.infinity,
                                padding: const EdgeInsets.symmetric(
                                    vertical: 8, horizontal: 10),
                                decoration: BoxDecoration(
                                  color: Colors.black,
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                alignment: Alignment.center,
                                child: const Text(
                                  'Select a Product',
                                  style: TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.bold),
                                ),
                              ),
                              const SizedBox(height: 10),
                              ListView.separated(
                                separatorBuilder: (context, index) =>
                                    const Divider(
                                  color: Colors.grey,
                                ),
                                shrinkWrap: true,
                                itemCount: products.length,
                                itemBuilder: (context, index) {
                                  return ListTile(
                                    onTap: () {
                                      // close the dialog box
                                      Navigator.of(context)
                                          .pop(products[index]);
                                    },
                                    title: Text(products[index]),
                                  );
                                },
                              ),
                            ],
                          ),
                        ),
                      );
                    });
                if (selectedProduct != null) {
                  _productsController.text = selectedProduct;
                }
              },
              child: TextFormField(
                controller: _productsController,
                validator: (value) {
                  if (value!.isEmpty) {
                    return 'Please select a product';
                  }
                  return null;
                },
                keyboardType: TextInputType.text,
                enabled: false,
                // align text to center
                textAlign: TextAlign.center,
                decoration: const InputDecoration(
                  hintText: 'Enter Products',
                  focusColor: Colors.black,
                  // Show border
                  border: OutlineInputBorder(
                    borderSide: BorderSide(color: Colors.black),
                  ),
                  errorBorder: OutlineInputBorder(
                    borderSide: BorderSide(color: Colors.red),
                  ),
                  disabledBorder: OutlineInputBorder(
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

  Future<bool> checkIfRoutePlanExists() async {
    final localDB = await LocalDB.openMyDatabase();
    final String selectedDateString =
        DateFormat(RoutePlanCalendar.dateFormat).format(DateTime.now());
    final List<Map<String, dynamic>> routePlanData = await localDB.rawQuery(
        'SELECT * FROM route_plan_transaction WHERE visit_date = ?',
        [selectedDateString]);
    return routePlanData.isNotEmpty;
  }

  Future<String?> selectRoute() {
    return showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) {
          final selectedDate = DateTime.now();
          Future<List<RouteMasterDB>> getRouteMaster() async {
            final localDB = await LocalDB.openMyDatabase();
            final List<Map<String, dynamic>> routeMasterData = await localDB
                .rawQuery('SELECT * FROM route_master ORDER BY route_name ASC');
            List<RouteMasterDB> routeMasterList = [];
            for (int i = 0; i < routeMasterData.length; i++) {
              routeMasterList.add(RouteMasterDB.fromMap(routeMasterData[i]));
            }
            // get existing route plan for the selected date
            final String selectedDateString =
                DateFormat(RoutePlanCalendar.dateFormat).format(selectedDate);
            final List<Map<String, dynamic>> routePlanData = await localDB
                .rawQuery(
                    'SELECT * FROM route_plan_transaction WHERE visit_date = ?',
                    [selectedDateString]);
            final List<String> selectedRouteCodes = [];
            for (int i = 0; i < routePlanData.length; i++) {
              selectedRouteCodes.add(routePlanData[i]['route_code'].toString());
            }
            // filter the selected route codes
            routeMasterList = routeMasterList
                .where(
                    (element) => selectedRouteCodes.contains(element.routeCode))
                .toList();
            return routeMasterList;
          }

          final Future<List<RouteMasterDB>> routeMasterList = getRouteMaster();

          return PopScope(
            canPop: false,
            child: StatefulBuilder(builder: (context, setState) {
              return Dialog(
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10)),
                // opaqueness of the dialog
                backgroundColor: Colors.white,
                insetPadding: const EdgeInsets.all(20),
                child: Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.symmetric(
                            vertical: 8, horizontal: 10),
                        decoration: BoxDecoration(
                          color: Colors.black,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        alignment: Alignment.center,
                        child: const Text(
                          'Select a Route',
                          style: TextStyle(
                              color: Colors.white, fontWeight: FontWeight.bold),
                        ),
                      ),
                      const SizedBox(height: 10),
                      Expanded(
                        child: FutureBuilder<List<RouteMasterDB>>(
                            future: routeMasterList,
                            builder: (context, snapshot) {
                              if (snapshot.connectionState ==
                                  ConnectionState.waiting) {
                                return const Center(
                                    child: CircularProgressIndicator());
                              }
                              if (snapshot.hasError) {
                                return Text('Error: ${snapshot.error}');
                              } else {
                                final List<RouteMasterDB> routeMasterList =
                                    snapshot.data ?? [];
                                return Expanded(
                                  child: ListView.separated(
                                    separatorBuilder: (context, index) =>
                                        const Divider(
                                      color: Colors.grey,
                                    ),
                                    shrinkWrap: true,
                                    itemCount: routeMasterList.length,
                                    itemBuilder: (context, index) {
                                      return ListTile(
                                        onTap: () {
                                          // close the dialog box
                                          Navigator.of(context).pop(
                                              routeMasterList[index].routeCode);
                                        },

                                        title: Text(
                                            routeMasterList[index].routeName ??
                                                ''),
                                        // subtitle: Text(routeMasterList[index].routeCode ?? ''),
                                      );
                                    },
                                  ),
                                );
                              }
                            }),
                      ),
                    ],
                  ),
                ),
              );
            }),
          );
        });
  }

  Future<String?> selectCustomer(String routeCode) {
    return showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) {
          final Future<List<CustomerMasterDB>> getCustomerMaster =
              CustomerMasterDB.getCustomerMasterDB(routeCode, '');
          return PopScope(
            canPop: false,
            child: Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
              backgroundColor: Colors.white,
              insetPadding: const EdgeInsets.all(20),
              child: Padding(
                padding: const EdgeInsets.all(8.0),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.symmetric(
                          vertical: 8, horizontal: 10),
                      decoration: BoxDecoration(
                        color: Colors.black,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      alignment: Alignment.center,
                      child: const Text(
                        'Select a Customer',
                        style: TextStyle(
                            color: Colors.white, fontWeight: FontWeight.bold),
                      ),
                    ),
                    const SizedBox(height: 10),
                    Flexible(
                      child: FutureBuilder<List<CustomerMasterDB>>(
                          future: getCustomerMaster,
                          builder: (context, snapshot) {
                            if (snapshot.connectionState ==
                                ConnectionState.waiting) {
                              return const Center(
                                  child: CircularProgressIndicator());
                            }
                            if (snapshot.hasError) {
                              return Text('Error: ${snapshot.error}');
                            } else {
                              List<CustomerMasterDB> customerMasterList =
                                  snapshot.data ?? [];
                              // filter out custType = 'Non Star'
                              customerMasterList = customerMasterList
                                  .where((element) =>
                                      element.customerType != 'Non Star')
                                  .toList();
                              if (customerMasterList.isEmpty) {
                                return Column(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    const Text(
                                        'No customers found for the selected route'),
                                    const SizedBox(height: 10),
                                    ElevatedButton(
                                      style: ElevatedButton.styleFrom(
                                        foregroundColor: Colors.white,
                                        backgroundColor: Colors.black,
                                      ),
                                      onPressed: () {
                                        Navigator.of(context).pop();
                                      },
                                      child: const Text('OK'),
                                    ),
                                  ],
                                );
                              } else {
                                return Flexible(
                                  child: ListView.separated(
                                    separatorBuilder: (context, index) =>
                                        const Divider(
                                      color: Colors.grey,
                                    ),
                                    shrinkWrap: true,
                                    itemCount: customerMasterList.length,
                                    itemBuilder: (context, index) {
                                      return ListTile(
                                        onTap: () {
                                          // close the dialog box
                                          Navigator.of(context).pop(
                                              customerMasterList[index]
                                                  .customerCode);
                                        },
                                        title: Text(customerMasterList[index]
                                                .customerName ??
                                            ''),
                                        subtitle: Text(customerMasterList[index]
                                                .customerType ??
                                            ''),
                                      );
                                    },
                                  ),
                                );
                              }
                            }
                          }),
                    ),
                  ],
                ),
              ),
            ),
          );
        });
  }

  void optionSelector() async {
    final bool isRoutePlanExists = await checkIfRoutePlanExists();
    if (!isRoutePlanExists) {
      // ignore: use_build_context_synchronously
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Route Plan not available for today'),
        ),
      );
      // Goto Home Screen
      // ignore: use_build_context_synchronously
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(
          builder: (context) => const HomeScreen(
            showPopup: false,
          ),
        ),
      );
      return;
    }
    routeCode = await selectRoute() ?? '';
    if (routeCode.isNotEmpty) {
      customerCode = await selectCustomer(routeCode) ?? '';
      if (customerCode.isNotEmpty) {
      } else {
        // Goto Home Screen
        // ignore: use_build_context_synchronously
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(
            builder: (context) => const HomeScreen(
              showPopup: false,
            ),
          ),
        );
      }
    }
    setState(() {});
  }

  @override
  void initState() {
    super.initState();
    optionSelector();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Yellow Card'),
      ),
      body: Column(
        children: [
          CustomerDetailsYellowCard(
            routeCode: routeCode,
            customerCode: customerCode,
          ),
          // Yellow Card Details
          Expanded(
            child: SingleChildScrollView(
              child: Form(
                key: _formKey,
                child: Column(
                  children: [
                    // Date
                    getDate(),
                    // Challan Number
                    getDataInputField(
                        controller: _challanNumberController,
                        labelText: 'Challan Number',
                        hintText: 'Enter Challan Number',
                        keyboardType: TextInputType.text),
                    // Quantity
                    getDataInputField(
                        controller: _quantityController,
                        labelText: 'Quantity',
                        hintText: 'Enter Quantity',
                        keyboardType: TextInputType.number),
                    // Products
                    getProducts(),
                  ],
                ),
              ),
            ),
          ),
          // Submit Button
          InkWell(
            // onTap: () => submit(context),
            child: Container(
              margin: const EdgeInsets.all(10),
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.black,
                borderRadius: BorderRadius.circular(5),
              ),
              child: const Center(
                child: Text(
                  'Submit',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class CustomerDetailsYellowCard extends StatefulWidget {
  final String routeCode;
  final String customerCode;
  const CustomerDetailsYellowCard(
      {super.key, required this.routeCode, required this.customerCode});

  @override
  State<CustomerDetailsYellowCard> createState() =>
      _CustomerDetailsYellowCardState();
}

class _CustomerDetailsYellowCardState extends State<CustomerDetailsYellowCard> {
  String routeName = '';

  void getRouteName() async {
    final String routeCode = widget.routeCode;
    if (routeCode.isEmpty) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('No Route found for this Customer'),
        ),
      );
      return;
    }
    final RouteMasterDB routeMasterDB =
        await RouteMasterDB.getRouteMasterDBByRouteCode(routeCode);
    setState(() {
      routeName = routeMasterDB.routeName ?? '';
    });
  }

  @override
  void initState() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      getRouteName();
    });
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    final Future<CustomerMasterDB> getCustomerMasterDB =
        CustomerMasterDB.getCustomerMasterDBByCustomerCode(widget.customerCode);
    return FutureBuilder(
      future: getCustomerMasterDB,
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
                  // Customer Details
                  const Center(
                    child: Text(
                      'Customer Details',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  const Divider(),
                  Text('Customer Name: ${customer.customerName}'),
                  Text('Customer Code: ${customer.customerCode}'),
                  Text('Customer Route: $routeName'),
                  Text('Credit Limit: ${customer.creditLimit}'),
                ],
              ),
            );
          } else {
            return const Text('No Customer Details');
          }
        } else {
          return const Center(
            child: CircularProgressIndicator(
              color: Colors.black,
            ),
          );
        }
      },
    );
  }
}
