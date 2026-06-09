// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/product_stock_audit.dart';
import 'package:starsfa/models/route_master_class.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/models/location_class.dart';
import 'package:starsfa/models/determine_position.dart';

class StockAuditScreen extends StatefulWidget {
  final String sessionID;
  const StockAuditScreen({super.key, required this.sessionID});

  @override
  State<StockAuditScreen> createState() => _StockAuditScreen();
}

class _StockAuditScreen extends State<StockAuditScreen> {
  bool isLoading = false;
  List<StockProductDataClass> allData = [];
  String branchCode = '';
  List<StockProductDataClass> product = [];
  TextEditingController controllerRemarks = TextEditingController();
  final List<TextEditingController> _controllers = [];
  String customerCode = '';
  String sessionToken = '';
  final GlobalKey<FormState> formKey = GlobalKey<FormState>();

  Future<void> generateSessionToken() async {
    final user = await UserLoginClass.getLocalUser();
    final String sessionTokenGen =
        'S${user?.empCode}${DateFormat('yyyyMMddHHmmss').format(DateTime.now())}';
    // open hive box with session token
    await Hive.openBox(sessionTokenGen);
    setState(() {
      sessionToken = sessionTokenGen;
    });
    print(sessionToken);
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

  @override
  void initState() {
    super.initState();
    _fetchProductata();
    //_fetchOrders();
    //loginUser(Constants.userId);
  }

  void _fetchProductata() async {
    try {
      final Box box = Hive.box('checkIn');
      customerCode = box.get('customerCode', defaultValue: '');
      //String branch? = await stockProductDataClass.getCustomerBranchByCode(customerCode);
      final fetchedData =
          await StockProductDataClass.getAllRecords(customerCode, 'from');
      setState(() {
        allData = fetchedData;
        //print(allData);
      });
    } catch (e) {
      print('Error fetching dash_data: $e');
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
          'Stock Audit',
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            fontSize: 18,
          ),
        ),
      ),
      body: Column(
        children: <Widget>[
          const CustomerDetailsOrder(),
          Row(
            children: [
              Expanded(
                flex: 4, // 40% of the width
                child: Container(
                  color: Colors.red,
                  child: const Center(
                    child: Text(
                      'Product Name',
                      style: TextStyle(color: Colors.white),
                    ),
                  ),
                ),
              ),
              Container(
                width: 1, // Divider width
                color: Colors.black, // Divider color
              ),
              Expanded(
                flex: 2, // 40% of the width
                child: Container(
                  color: Colors.red,
                  child: const Center(
                    child: Text(
                      'Qty',
                      style: TextStyle(color: Colors.white),
                    ),
                  ),
                ),
              ),
              Container(
                width: 1, // Divider width
                color: Colors.black, // Divider color
              ),
              Expanded(
                flex: 2, // 20% of the width
                child: Container(
                  color: Colors.red,
                  child: const Center(
                    child: Text(
                      'UOM',
                      style: TextStyle(color: Colors.white),
                    ),
                  ),
                ),
              ),
              Container(
                width: 2, // Divider width
                color: Colors.black, // Divider color
              ),
              Expanded(
                flex: 2, // 20% of the width
                child: Container(
                  color: Colors.green,
                  child: const Center(
                    child: Text(
                      'Add',
                      style: TextStyle(color: Colors.white),
                    ),
                  ),
                ),
              ),
            ],
          ),
          Expanded(
              child: ListView.builder(
            itemCount: allData.length,
            itemBuilder: (context, index) {
              final order = allData[index];
              _controllers.add(TextEditingController());
              return ListTile(
                  title: Row(children: [
                Expanded(
                  flex: 2,
                  child: Text(
                    order.prodDesc.toString(),
                    style: const TextStyle(
                      fontSize: 12,
                      color: Colors.black,
                    ),
                  ),
                ),
                Expanded(
                  flex: 1,
                  child: TextFormField(
                    controller: _controllers[index],
                    keyboardType: TextInputType.number,
                    textAlign: TextAlign.center,
                    decoration: const InputDecoration(
                      hintText: '',
                      isDense: true,
                      contentPadding: EdgeInsets.fromLTRB(10, 10, 10, 0),
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
                      fontSize: 14,
                      color: Colors.black,
                    ),
                  ),
                ),
                Expanded(
                  flex: 1,
                  child: Text(
                    order.uom1.toString(),
                    textAlign: TextAlign.center,
                  ),
                ),
                Expanded(
                  flex: 1,
                  child: IconButton.outlined(
                    icon: const Icon(Icons.add),
                    color: Colors.blue,
                    splashColor: Colors.white,
                    iconSize: 24.0,
                    padding: const EdgeInsets.all(0.0),
                    onPressed: () {
                      setState(() {
                        if (_controllers[index].text.toString().isEmpty) {
                          showMsg('Add Qty');
                        } else {
                          StockProductDataClass d = StockProductDataClass();
                          d.prodCode = allData[index].prodCode.toString();
                          d.prodDesc = allData[index].prodDesc.toString();
                          d.uom1 = allData[index].uom1.toString();
                          d.qty = _controllers[index].text.toString();
                          d.customerCode = customerCode;
                          product.add(d);
                          _controllers[index].text = '';
                          allData.removeAt(index);
                        }
                      });
                    },
                  ),
                )
              ]));
            },
          )),
          InkWell(
            onTap: () async {
              product.isEmpty ? showMsg('Add atleast one product') : checkOut();
            },
            child: Container(
              margin: const EdgeInsets.all(10),
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.red,
                borderRadius: BorderRadius.circular(5),
              ),
              child: Center(
                child: isLoading
                    ? const CircularProgressIndicator(
                        color: Colors.white,
                      )
                    : const Text(
                        'Check Out',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 16,
                        ),
                      ),
              ),
            ),
          ),
          const SizedBox(
            height: 10,
          ),
        ],
      ),
    );
  }

  Future<List<StockProductDataClass>?> checkOut() {
    // Single/Multiple Selection
    return showDialog(
        context: context,
        barrierDismissible: false,
        builder: (BuildContext contextPurpose) {
          return StatefulBuilder(builder: (contextPurpose, setState) {
            return PopScope(
              canPop: false,
              child: Dialog(
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10)),
                backgroundColor: Colors.white,
                insetPadding: const EdgeInsets.all(2),
                child: Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Stack(
                    children: [
                      Positioned(
                        right: 0,
                        top: 0,
                        left: 0,
                        bottom: 0,
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Container(
                              width: double.infinity,
                              padding: const EdgeInsets.symmetric(
                                  vertical: 8, horizontal: 10),
                              decoration: BoxDecoration(
                                // color: Colors.red[600],
                                borderRadius: BorderRadius.circular(10),
                              ),
                              alignment: Alignment.centerLeft,
                              child: Row(
                                children: [
                                  IconButton(
                                    icon: const Icon(
                                      Icons.add,
                                      color: Colors.red,
                                    ),
                                    onPressed: () {
                                      Navigator.of(context).pop(product);
                                    },
                                  ),
                                  const Expanded(
                                    child: Text(
                                      '',
                                      style: TextStyle(
                                        color: Colors.red,
                                        fontWeight: FontWeight.bold,
                                        fontSize: 18,
                                      ),
                                      textAlign: TextAlign.center,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(height: 10),
                            Expanded(
                              child: ListView.separated(
                                separatorBuilder: (context, index) =>
                                    const Divider(
                                  color: Colors.grey,
                                ),
                                shrinkWrap: true,
                                itemCount: product.length,
                                itemBuilder: (context, index) {
                                  return ListTile(
                                      contentPadding: const EdgeInsets.all(0),
                                      onTap: () {
                                        setState(() {});
                                      },
                                      title: Column(
                                        children: [
                                          Text(
                                            '${index + 1}: ${product[index].prodDesc}',
                                            textAlign: TextAlign.left,
                                          ),
                                          Text(
                                            'QTY. ${product[index].qty} - ${product[index].uom1}',
                                            textAlign: TextAlign.left,
                                          ),
                                        ],
                                      ));
                                },
                              ),
                            ),

                            Row(
                              children: [
                                Expanded(
                                  // flex: 1,
                                  child: ElevatedButton(
                                    style: ElevatedButton.styleFrom(
                                      foregroundColor: Colors.white,
                                      backgroundColor: Colors.red,
                                    ),
                                    onPressed: () {
                                      showRemarks('');
                                    },
                                    child: const Text('Remarks'),
                                  ),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  // flex: 1,
                                  child: ElevatedButton(
                                    style: ElevatedButton.styleFrom(
                                      foregroundColor: Colors.white,
                                      backgroundColor: Colors.red,
                                    ),
                                    onPressed: () {
                                      setState(() {
                                        isLoading = true;
                                      });
                                      saveStock(product);
                                      setState(() {
                                        isLoading = false;
                                      });
                                      Navigator.of(context).pop(product);
                                    },
                                    child: const Text('Submit'),
                                  ),
                                ),
                              ],
                            )

                            // Submit Button
                          ],
                        ),
                      ),
                      if (isLoading)
                        Positioned(
                            child: Container(
                          height: 100,
                          width: 100,
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(10),
                          ),
                          color: Colors.black.withOpacity(0.5),
                          child: const Center(
                            child: CircularProgressIndicator(
                              color: Colors.red,
                            ),
                          ),
                        ))
                    ],
                  ),
                ),
              ),
            );
          });
        });
  }

  showMsg(String msg) async {
    return showDialog(
        context: context,
        builder: (context) {
          return PopScope(
            canPop: false,
            child: Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
              backgroundColor: Colors.white,
              insetPadding: const EdgeInsets.all(20),
              clipBehavior: Clip.hardEdge,
              child: Container(
                  decoration: const BoxDecoration(
                    color: Color.fromARGB(255, 236, 229, 221),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const SizedBox(height: 10),
                      Text(msg,
                          style: const TextStyle(fontSize: 16),
                          textAlign: TextAlign.center),
                      const SizedBox(height: 10),
                      ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          foregroundColor: Colors.white,
                          backgroundColor: Colors.red[600],
                        ),
                        onPressed: () {
                          Navigator.of(context).pop(true);
                        },
                        child: const Text('OK'),
                      ),
                    ],
                  )),
            ),
          );
        });
  }

  showRemarks(String msg) async {
    return showDialog(
        context: context,
        builder: (context) {
          return PopScope(
            canPop: false,
            child: Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
              backgroundColor: Colors.white,
              insetPadding: const EdgeInsets.all(20),
              clipBehavior: Clip.hardEdge,
              child: Container(
                  decoration: const BoxDecoration(
                    color: Color.fromARGB(255, 236, 229, 221),
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const SizedBox(height: 50),
                      Container(
                        padding: const EdgeInsets.all(10),
                        child: TextField(
                          controller: controllerRemarks,
                          onChanged: (value) {
                            setState(() {});
                          },
                          decoration: const InputDecoration(
                            labelText: 'Remarks',
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.all(
                                Radius.circular(10),
                              ),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 10),
                      ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          foregroundColor: Colors.white,
                          backgroundColor: Colors.red[600],
                        ),
                        onPressed: () {
                          for (var i = 0; i <= product.length; i++) {
                            // _product[i].remarks=_controllerRemarks.text.toString();
                          }

                          Navigator.of(context).pop(true);
                        },
                        child: const Text('Remarks Submit'),
                      ),
                    ],
                  )),
            ),
          );
        });
  }

  Future<void> saveStock(List<StockProductDataClass> selectedProduct) async {
    final user = await UserLoginClass.getLocalUser();
    final localDB = await LocalDB.openMyDatabase();
    final batch = localDB.batch();
    String trns =
        'S${user?.empCode}${DateFormat('yyyyMMddHHmmss').format(DateTime.now())}';

    final position = await DeterminePosition.getPosition(null, null, null);
    if (position.latitude == null || position.longitude == null) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Location not found'),
        ),
      );
      return;
    }
    String purposeOfVisit = await LocalDB.rawQuery(
      "SELECT * FROM app_variables WHERE operation_type = 'check_in' AND variable_name = 'purpose_of_visit'",
    ).then((value) => value[0]['variable_value']);
    purposeOfVisit = purposeOfVisit.replaceAll('[', '');
    purposeOfVisit = purposeOfVisit.replaceAll(']', '');

    final LocationClass locationMS = LocationClass(
      empCode: user?.empCode ?? '',
      latt: position.latitude.toString(),
      longi: position.longitude.toString(),
      date: DateFormat('yyyy-MM-dd hh:mm:ss').format(DateTime.now()),
      transId: trns.replaceFirst('S', 'S'),
      purposeOfVisit: purposeOfVisit,
    );

    final bool isSavedLocation = await LocationClass.saveLocation(locationMS);
    if (!isSavedLocation) {
      // show snackbar
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Error: Location not saved'),
        ),
      );
      return;
    }

    // insert new
    for (int i = 0; i < selectedProduct.length; i++) {
      final Map<String, String> stockMap = {
        'transaction_id': trns,
        'customer_code': selectedProduct[i].customerCode ?? '',
        'product_code': selectedProduct[i].prodCode ?? '',
        'quantity': selectedProduct[i].qty ?? '',
        'remarks': controllerRemarks.text.toString(),
        'UOM': selectedProduct[i].uom1 ?? '',
        'flag': '0',
        'product_mrp': '',
        'product_details': '',
        'mfd_date': '',
        'weightage': '',
      };

      batch.insert('stock_audit', stockMap);
    }
    // commit the batch
    await batch.commit();

    await StockProductDataClass.updateStockAuditServer(trns);
    // Pop Context
    Navigator.of(context).pop();
  }
}

class CustomerDetailsOrder extends StatefulWidget {
  const CustomerDetailsOrder({super.key});

  @override
  State<CustomerDetailsOrder> createState() => _CustomerDetailsOrderState();
}

class _CustomerDetailsOrderState extends State<CustomerDetailsOrder> {
  String routeName = '';

  void getRouteName() async {
    final Box box = Hive.box('checkIn');
    final String routeCode = box.get('routeCode', defaultValue: '');
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
    return Column(children: <Widget>[
      ValueListenableBuilder<Box>(
          valueListenable: Hive.box('checkIn').listenable(),
          builder: (BuildContext context, Box box, Widget? child) {
            final String customerCode =
                box.get('customerCode', defaultValue: '');
            Future<CustomerMasterDB> getCustomerMasterDB =
                CustomerMasterDB.getCustomerMasterDBByCustomerCode(
                    customerCode);
            return FutureBuilder(
              future: getCustomerMasterDB,
              builder: (BuildContext context,
                  AsyncSnapshot<CustomerMasterDB> snapshot) {
                if (snapshot.connectionState == ConnectionState.done) {
                  if (snapshot.hasData) {
                    final CustomerMasterDB customer =
                        snapshot.data ?? CustomerMasterDB();
                    //branch_code = ${customer.customerName}
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
          }),
    ]);
  }
}
