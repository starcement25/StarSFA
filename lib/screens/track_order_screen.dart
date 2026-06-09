import 'package:flutter/material.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/track_order_class.dart';
// import 'package:url_launcher/url_launcher.dart';

class TrackOrderScreen extends StatefulWidget {
  const TrackOrderScreen({super.key});

  @override
  State<TrackOrderScreen> createState() => _TrackOrderScreenState();
}

class _TrackOrderScreenState extends State<TrackOrderScreen> {
  CustomerMasterDB? selectedCustomer;
  final Future<List<CustomerMasterDB>> getCustomerMaster =
      CustomerMasterDB.getCustomerMasterDB('', '');
  late Future<List<TrackOrderClass>> future;
  String searchText = '';

  void selectCustomer() async {
    final selectedLocal = await showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) {
          List<CustomerMasterDB> customerMasterList = [];
          return PopScope(
            canPop: false,
            child: Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
              backgroundColor: Colors.white,
              insetPadding: const EdgeInsets.all(20),
              clipBehavior: Clip.hardEdge,
              child: Container(
                // Background Image
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
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.symmetric(
                          vertical: 8, horizontal: 10),
                      decoration: BoxDecoration(
                        // color: Colors.black,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      alignment: Alignment.center,
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          // back button
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
                          const Expanded(
                            child: Text(
                              'Select a Customer',
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
                    FutureBuilder<List<CustomerMasterDB>>(
                        future: getCustomerMaster,
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
                                                contentPadding:
                                                    const EdgeInsets.all(0),
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
                                                            .customerType ??
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
        future = getTrackOrderData();
      });
    }
  }

  Future<List<TrackOrderClass>> getTrackOrderData() async {
    final trackOrderResponse =
        await TrackOrderResponse.getTrackOrderDataByDnsCustomerCode(
            selectedCustomer?.dnsCustCode ?? '');
    return trackOrderResponse.orderData ?? [];
  }

  @override
  void initState() {
    super.initState();
    // call after the build method is called
    WidgetsBinding.instance.addPostFrameCallback((_) {
      selectCustomer();
    });
    future = getTrackOrderData();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            Navigator.of(context).pop();
          },
        ),
        backgroundColor: Colors.red,
        title: const Text(
          'Track Order',
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
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Customer details
            InkWell(
              onTap: selectCustomer,
              child: Container(
                width: double.infinity,
                margin: const EdgeInsets.all(10),
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: Colors.grey[200]?.withOpacity(0.5),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Customer Name: ${selectedCustomer?.customerName ?? ''}',
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    Text(
                        'Customer Type: ${selectedCustomer?.customerType ?? ''}'),
                  ],
                ),
              ),
            ),
            // Two Tabs - Online and Offline
            Flexible(
              child: DefaultTabController(
                length: 2,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const TabBar(
                      indicatorColor: Colors.black,
                      labelColor: Colors.black,
                      tabs: [
                        Tab(
                          text: 'APP Order',
                        ),
                        Tab(
                          text: 'Offline Order',
                        ),
                      ],
                    ),
                    const SizedBox(
                      height: 10,
                    ),
                    // TabBarView
                    Flexible(
                      child: TabBarView(
                        children: [
                          // Online
                          Container(
                            padding: const EdgeInsets.all(10),
                            child: FutureBuilder<List<TrackOrderClass>>(
                                future: future,
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
                                    final trackOrderList = snapshot.data ?? [];
                                    return trackOrderList.isEmpty
                                        ? const Center(
                                            child: Text(
                                              'No order data found.',
                                              style: TextStyle(
                                                  color: Colors.black,
                                                  fontSize: 20,
                                                  fontWeight: FontWeight.bold),
                                            ),
                                          )
                                        : ListView.builder(
                                            itemCount: trackOrderList.length,
                                            itemBuilder: (context, index) {
                                              return TrackOrderListTile(
                                                  trackOrder:
                                                      trackOrderList[index]);
                                            },
                                          );
                                  }
                                }),
                          ),
                          // Offline
                          Container(
                            padding: const EdgeInsets.all(10),
                            child: const Column(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text('Offline Orders'),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class TrackOrderListTile extends StatefulWidget {
  const TrackOrderListTile({
    super.key,
    required this.trackOrder,
  });

  final TrackOrderClass trackOrder;

  @override
  State<TrackOrderListTile> createState() => _TrackOrderListTileState();
}

class _TrackOrderListTileState extends State<TrackOrderListTile> {
  bool isExpanded = false;
  final Map<String, Color> statusColor = {
    'order canceled': Colors.red,
    'dispatched': Colors.green,
    'do approved': Colors.orange,
    'cancelled': Colors.grey,
  };

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        print(
            "Tapped: ${widget.trackOrder.status} → Challan Data: ${widget.trackOrder.orderChallanData}");
        if (widget.trackOrder.orderChallanData?.isNotEmpty ?? false) {
          setState(() {
            isExpanded = !isExpanded;
          });
        } else {
          ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
            content: Text('No challan data available'),
          ));
        }
      },
      child: Container(
        width: double.infinity,
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: Colors.grey[200]?.withOpacity(0.5),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                // appOrderNo
                Text(
                  widget.trackOrder.apporderno ?? 'NA',
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
                // Quantity
                Text(
                  'X${widget.trackOrder.qty ?? 'NA'}',
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
                // status
                Text(
                  (widget.trackOrder.status ?? 'NA').toUpperCase(),
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                    color:
                        statusColor[widget.trackOrder.status?.toLowerCase()] ??
                            Colors.black,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),
            // erporderno
            Text('Sales Order No: ${widget.trackOrder.erporderno ?? 'NA'}'),
            const SizedBox(height: 7),
            // prod_display_name
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Product: '),
                Flexible(
                  child: Text(widget.trackOrder.prodDisplayName ?? 'NA'),
                ),
              ],
            ),
            const SizedBox(height: 7),
            // order_full_date_time
            Text(widget.trackOrder.orderFullDateTime ?? 'NA'),
            const SizedBox(height: 7),

            if (widget.trackOrder.destination != '') ...[
              // Destination
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Destination: ',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Colors.black,
                    ),
                  ),
                  Flexible(
                    child: Text(widget.trackOrder.destination ?? 'NA'),
                  ),
                ],
              ),
              const SizedBox(height: 7),
            ],
            if (widget.trackOrder.freight != '') ...[
              // Freight
              RichText(
                text: TextSpan(
                  style: DefaultTextStyle.of(context).style,
                  children: [
                    const TextSpan(
                      text: 'Freight: ',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                        color: Colors.black,
                      ),
                    ),
                    TextSpan(
                      text: widget.trackOrder.freight ?? 'NA',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.black,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 7),
            ],
            if (widget.trackOrder.plantName != '') ...[
              // plantName
              if (widget.trackOrder.freight == 'EXW') ...[
                RichText(
                  text: TextSpan(
                    style: DefaultTextStyle.of(context).style,
                    children: [
                      const TextSpan(
                        text: 'Dump Name: ',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 12,
                          color: Colors.black,
                        ),
                      ),
                      TextSpan(
                        text: widget.trackOrder.plantName ?? 'NA',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.black,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
              if (widget.trackOrder.freight == 'FOR') ...[
                RichText(
                  text: TextSpan(
                    style: DefaultTextStyle.of(context).style,
                    children: [
                      const TextSpan(
                        text: 'Plant Name: ',
                        style: TextStyle(fontWeight: FontWeight.bold),
                      ),
                      TextSpan(
                        text: widget.trackOrder.plantName ?? 'NA',
                      ),
                    ],
                  ),
                ),
              ],
              const SizedBox(height: 7),
            ],

            // Expandable
            if (isExpanded)
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Divider(
                    color: Colors.black,
                  ),
                  const SizedBox(height: 7),
                  // orderChallanData
                  if (widget.trackOrder.orderChallanData != null)
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        // const Text('Challan Data',style: TextStyle(fontWeight: FontWeight.bold)),
                        // const SizedBox(height: 7),
                        for (var challanData
                            in widget.trackOrder.orderChallanData ?? [])
                          Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: Colors.grey[300],
                              borderRadius: BorderRadius.circular(10),
                            ),
                            margin: const EdgeInsets.only(bottom: 10),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                // Labels
                                const Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Text('Invoice No'),
                                    Text('Invoice Date'),
                                    Text('Invoice Qty(MT)'),
                                    Text('Truck No'),
                                    // Text('Driver Contact'),
                                  ],
                                ),
                                // Values
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Text(challanData.challanno ?? 'NA'),
                                    Text(challanData.challandt ?? 'NA'),
                                    Text(challanData.challanqty ?? 'NA'),
                                    Text(challanData.truckno ?? 'NA')
                                    // InkWell(
                                    //   onTap: () async {
                                    //     String phoneNumber =
                                    //         challanData.driverno ?? '';
                                    //     if (phoneNumber.isNotEmpty) {
                                    //       // Uri url = Uri(
                                    //       //     scheme: 'tel', path: phoneNumber);
                                    //       // if (await canLaunchUrl(url)) {
                                    //       //   await launchUrl(url);
                                    //       // } else {
                                    //       //   throw 'Could not launch $url';
                                    //       // }
                                    //     }
                                    //   },
                                    //   child: Text(
                                    //     challanData.driverno ?? 'NA',
                                    //     style: const TextStyle(
                                    //         color: Colors.blue,
                                    //         decoration:
                                    //             TextDecoration.underline),
                                    //   ),
                                    // ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                      ],
                    ),
                ],
              ),
            // Arrow
            if (widget.trackOrder.orderChallanData?.isNotEmpty ?? false)
              const SizedBox(height: 10),
            if (widget.trackOrder.orderChallanData?.isNotEmpty ?? false)
              Align(
                alignment: Alignment.center,
                child: Icon(
                  isExpanded
                      ? Icons.keyboard_arrow_up
                      : Icons.keyboard_arrow_down,
                  color: Colors.black,
                ),
              ),
          ],
        ),
      ),
    );
  }
}
