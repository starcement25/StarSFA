import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/destination_master.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/route_master_class.dart';

class OrderScreen extends StatefulWidget {
  final String sessionID;
  const OrderScreen({super.key, required this.sessionID});

  @override
  State<OrderScreen> createState() => _OrderScreenState();
}

class _OrderScreenState extends State<OrderScreen> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController orderTypeController = TextEditingController();
  final String orderTypeKey = 'orderType';
  final String freightKey = 'freight';
  final TextEditingController freightController = TextEditingController();
  final String destinationKey = 'destination';
  final TextEditingController destinationController = TextEditingController();

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

  Widget getOrderType() {
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
          getLabel('Order Type'),
          ValueListenableBuilder<Box>(
              valueListenable: Hive.box(widget.sessionID).listenable(),
              builder: (context, box, child) {
                final String value = box.get(orderTypeKey, defaultValue: '');
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  orderTypeController.text = value;
                });
                return InkWell(
                  onTap: () async {
                    const orderTypeSql =
                        "SELECT order_type FROM order_form_details LIMIT 1";
                    final orderTypeResult =
                        await LocalDB.rawQuery(orderTypeSql);
                    // print(tableViewResult);
                    final List<String> menuValues =
                        orderTypeResult.first['order_type'].split(',');
                    // print(mwnuValues);
                    getOrderTypeSelectionDialog(menuValues);
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
                      controller: orderTypeController,
                      validator: (value) {
                        if (value!.isEmpty) {
                          return 'Please select Order Type';
                        }
                        return null;
                      },
                      enabled: false,
                      // align text to center
                      textAlign: TextAlign.center,
                      decoration: const InputDecoration(
                        hintText: 'Select Order Type',
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
                );
              }),
        ],
      ),
    );
  }

  getOrderTypeSelectionDialog(List<String> values) {
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
                    child: const Text('Select Order Type',
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
                            .where((element) => element
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
                            values[index],
                          ),
                          onTap: () {
                            // get hive box with session token
                            final Box<dynamic> box = Hive.box(widget.sessionID);
                            box.put(orderTypeKey, values[index]);
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

  Widget getFreight() {
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
          getLabel('Freight'),
          ValueListenableBuilder<Box>(
              valueListenable: Hive.box(widget.sessionID).listenable(),
              builder: (context, box, child) {
                final String value = box.get(freightKey, defaultValue: '');
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  freightController.text = value;
                });
                return InkWell(
                  onTap: () async {
                    const freightSql =
                        "SELECT freight_component FROM order_form_details LIMIT 1";
                    final freightResult = await LocalDB.rawQuery(freightSql);
                    final List<String> menuValues =
                        freightResult.first['freight_component'].split(',');
                    getFreightSelectionDialog(menuValues);
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
                      controller: freightController,
                      validator: (value) {
                        if (value!.isEmpty) {
                          return 'Please select Freight';
                        }
                        return null;
                      },
                      enabled: false,
                      // align text to center
                      textAlign: TextAlign.center,
                      decoration: const InputDecoration(
                        hintText: 'Select Freight',
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
                );
              }),
        ],
      ),
    );
  }

  getFreightSelectionDialog(List<String> values) {
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
                    child: const Text('Select Freight',
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
                            .where((element) => element
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
                            values[index],
                          ),
                          onTap: () {
                            final TextEditingController
                                freightAmountController =
                                TextEditingController();
                            // check if value is 'FOR'
                            if (values[index] == 'FOR') {
                              // pop dialog
                              Navigator.of(context).pop();
                              // show dialog
                              showDialog(
                                context: context,
                                builder: (context) {
                                  return Dialog(
                                    child: Container(
                                      padding: const EdgeInsets.all(8.0),
                                      decoration: BoxDecoration(
                                        borderRadius: BorderRadius.circular(5),
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
                                              borderRadius:
                                                  BorderRadius.circular(5),
                                            ),
                                            child: const Text(
                                                'Enter Freight Amount',
                                                style: TextStyle(
                                                  fontSize: 18,
                                                  color: Colors.black,
                                                  fontWeight: FontWeight.bold,
                                                )),
                                          ),
                                          const SizedBox(height: 10),
                                          TextField(
                                            controller: freightAmountController,
                                            decoration: InputDecoration(
                                              hintText: 'Enter Freight Amount',
                                              prefixIcon:
                                                  const Icon(Icons.money),
                                              suffix: IconButton(
                                                  onPressed: () {
                                                    // check if value is empty
                                                    if (freightAmountController
                                                        .text.isEmpty) {
                                                      ScaffoldMessenger.of(
                                                              context)
                                                          .showSnackBar(
                                                        const SnackBar(
                                                          content: Text(
                                                              'Please enter Freight Amount'),
                                                        ),
                                                      );
                                                      return;
                                                    }
                                                    // get hive box with session token
                                                    final Box<dynamic> box =
                                                        Hive.box(
                                                            widget.sessionID);
                                                    box.put(freightKey,
                                                        '${values[index]},${freightAmountController.text}');
                                                    Navigator.of(context).pop();
                                                  },
                                                  icon: const Icon(
                                                    Icons.arrow_forward,
                                                    color: Colors.black,
                                                  )),
                                            ),
                                            keyboardType: TextInputType.number,
                                          ),
                                        ],
                                      ),
                                    ),
                                  );
                                },
                              );
                            } else {
                              // get hive box with session token
                              final Box<dynamic> box =
                                  Hive.box(widget.sessionID);
                              box.put(freightKey, values[index]);
                              Navigator.of(context).pop();
                            }
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

  Widget getDestination() {
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
          getLabel('Destination'),
          ValueListenableBuilder<Box>(
              valueListenable: Hive.box(widget.sessionID).listenable(),
              builder: (context, box, child) {
                final String value =
                    box.get(destinationKey, defaultValue: ',').split(',')[1];
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  destinationController.text = value;
                });
                return InkWell(
                  onTap: () async {
                    final destinationResult =
                        await DestinationMasterDB.getDestinationMasterDB();
                    getDestinationSelectionDialog(destinationResult);
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
                      controller: destinationController,
                      validator: (value) {
                        if (value!.isEmpty) {
                          return 'Please select Destination';
                        }
                        return null;
                      },
                      enabled: false,
                      // align text to center
                      textAlign: TextAlign.center,
                      decoration: const InputDecoration(
                        hintText: 'Select Destination',
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
                );
              }),
        ],
      ),
    );
  }

  getDestinationSelectionDialog(List<DestinationMasterDB> values) {
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
                    child: const Text('Select Destination',
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
                            .where((element) => element.destinationName
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
                            values[index].destinationName ?? '',
                          ),
                          onTap: () {
                            // get hive box with session token
                            final Box<dynamic> box = Hive.box(widget.sessionID);
                            box.put(destinationKey,
                                '${values[index].destinationCode},${values[index].destinationName}');
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
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Order Screen'),
      ),
      body: Column(
        children: <Widget>[
          Expanded(
            child: SingleChildScrollView(
              child: Form(
                key: _formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // Customer Details
                    const CustomerDetailsOrder(),
                    // Order Type
                    getOrderType(),
                    // Freight
                    getFreight(),
                    // Destination
                    getDestination(),
                  ],
                ),
              ),
            ),
          ),
          InkWell(
            onTap: () {
              // show order added
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Order Added'),
                ),
              );
              // clear box
              Hive.box(widget.sessionID).clear();
              // pop
              Navigator.of(context).pop();
            },
            child: Container(
              margin: const EdgeInsets.all(10),
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.black,
                borderRadius: BorderRadius.circular(5),
              ),
              child: const Center(
                child: Text(
                  'Add Order',
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
          }),
    ]);
  }
}
