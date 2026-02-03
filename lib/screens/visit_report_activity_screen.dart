import 'package:flutter/material.dart';
import 'package:starsfa/models/local_db.dart';

class VisitReportActivityScreen extends StatefulWidget {
  const VisitReportActivityScreen({super.key});

  @override
  State<VisitReportActivityScreen> createState() =>
      _VisitReportActivityScreenState();
}

class _VisitReportActivityScreenState extends State<VisitReportActivityScreen> {
  Future<List<VisitReportActivity>> getData() async {
    final data = await LocalDB.rawQuery('''
      SELECT 
          cm.customer_name, 
          cm.customer_code, 
          ci.check_in_time, 
          ci.check_out_time, 
          ci.remarks
      FROM 
          check_in_out_details AS ci
      LEFT JOIN 
          customer_master AS cm 
      ON 
          ci.customer_code = cm.customer_code
''');
    final List<VisitReportActivity> visitReportActivity = [];
    for (int i = 0; i < data.length; i++) {
      visitReportActivity.add(VisitReportActivity.fromJson(data[i]));
    }
    return visitReportActivity;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title:
            const Text('Visit Report', style: TextStyle(color: Colors.white)),
        backgroundColor: Colors.red,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
      ),
      body: FutureBuilder<List<VisitReportActivity>>(
        future: getData(),
        builder: (BuildContext context, AsyncSnapshot snapshot) {
          if (snapshot.connectionState == ConnectionState.done) {
            if (snapshot.hasError) {
              return Center(
                child: Text('Error: ${snapshot.error}'),
              );
            } else {
              return DataTable(
                  decoration: const BoxDecoration(
                    border: Border(
                      top: BorderSide(width: 1.0, color: Colors.black),
                      bottom: BorderSide(width: 1.0, color: Colors.black),
                    ),
                  ),
                  headingRowColor: WidgetStateProperty.resolveWith<Color>(
                      (Set<WidgetState> states) {
                    if (states.contains(WidgetState.hovered)) {
                      return Colors.yellow[800]!;
                    }
                    return Colors.yellow[800]!;
                  }),
                  columns: const <DataColumn>[
                    DataColumn(
                      label: Text('Customer Name',
                          style: TextStyle(fontStyle: FontStyle.italic)),
                    ),
                    DataColumn(
                      label: Text('Check In',
                          style: TextStyle(fontStyle: FontStyle.italic)),
                    ),
                    DataColumn(
                      label: Text('Check Out',
                          style: TextStyle(fontStyle: FontStyle.italic)),
                    ),
                  ],
                  rows: List<DataRow>.generate(snapshot.data.length, (index) {
                    return DataRow(
                        onLongPress: () {
                          showDialog(
                            context: context,
                            builder: (BuildContext context) {
                              return AlertDialog(
                                title: const Text('Remarks'),
                                content: Text(snapshot.data[index].remarks),
                                actions: <Widget>[
                                  TextButton(
                                    onPressed: () {
                                      Navigator.of(context).pop();
                                    },
                                    child: const Text('Close'),
                                  ),
                                ],
                              );
                            },
                          );
                        },
                        cells: <DataCell>[
                          DataCell(Text(snapshot.data[index].customerName)),
                          DataCell(Text(snapshot.data[index].checkIn)),
                          DataCell(Text(snapshot.data[index].checkOut)),
                        ]);
                  }));
            }
          } else {
            return const Center(
              child: CircularProgressIndicator(),
            );
          }
        },
      ),
    );
  }
}

class VisitReportActivity {
  final String customerName;
  final String customerCode;
  final String checkIn;
  final String checkOut;
  final String remarks;

  VisitReportActivity({
    required this.customerName,
    required this.customerCode,
    required this.checkIn,
    required this.checkOut,
    required this.remarks,
  });

  // from json
  factory VisitReportActivity.fromJson(Map<String, dynamic> json) {
    return VisitReportActivity(
      customerName: json['customer_name'],
      customerCode: json['customer_code'],
      checkIn: json['check_in_time'],
      checkOut: json['check_out_time'],
      remarks: json['remarks'],
    );
  }
}
