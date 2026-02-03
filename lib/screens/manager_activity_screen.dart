import 'package:flutter/material.dart';
import 'package:starsfa/models/manager_activity_class.dart';

class ManagerActivityScreen extends StatefulWidget {
  const ManagerActivityScreen({super.key});

  @override
  State<ManagerActivityScreen> createState() => _ManagerActivityScreenState();
}

class _ManagerActivityScreenState extends State<ManagerActivityScreen> {
  final Future<List<ManagerActivityClass>>? getManagerActivity =
      ManagerActivityClass.getManagerActivity();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Employee Activities'),
      ),
      body: FutureBuilder<List<ManagerActivityClass>>(
        future: getManagerActivity,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(
              child: CircularProgressIndicator(),
            );
          } else {
            return Column(
              children: [
                Table(
                  border: TableBorder.all(),
                  children: [
                    TableRow(
                      children: [
                        Container(
                          height: 60,
                          padding: const EdgeInsets.all(8.0),
                          decoration: const BoxDecoration(
                              // color: Colors.black,
                              ),
                          alignment: Alignment.center,
                          child: const Text(
                            'Emp Name',
                            style: TextStyle(
                                color: Colors.red,
                                fontSize: 18,
                                fontWeight: FontWeight.bold),
                          ),
                        ),
                        // Container(
                        //   height: 60,
                        //   padding: const EdgeInsets.all(8.0),
                        //   decoration: const BoxDecoration(
                        //     color: Colors.black,
                        //   ),
                        //   alignment: Alignment.center,
                        //   child: const Text(
                        //     'Emp Code',
                        //     style: TextStyle(color: Colors.white),
                        //   ),
                        // ),
                        Container(
                          height: 60,
                          padding: const EdgeInsets.all(8.0),
                          decoration: const BoxDecoration(
                              // color: Colors.black,
                              ),
                          alignment: Alignment.center,
                          child: const Text(
                            'In Time',
                            style: TextStyle(
                                color: Colors.red,
                                fontSize: 18,
                                fontWeight: FontWeight.bold),
                          ),
                        ),
                        Container(
                          height: 60,
                          padding: const EdgeInsets.all(8.0),
                          decoration: const BoxDecoration(
                              // color: Colors.black,
                              ),
                          alignment: Alignment.center,
                          child: const Text(
                            'Counter Visit',
                            style: TextStyle(
                                color: Colors.red,
                                fontSize: 16,
                                fontWeight: FontWeight.bold),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                Expanded(
                  child: SingleChildScrollView(
                    child: IntrinsicHeight(
                      child: Table(
                        border: const TableBorder.symmetric(
                          outside: BorderSide(color: Colors.grey),
                        ),
                        children: [
                          for (var managerActivity in snapshot.data ?? [])
                            TableRow(children: [
                              Container(
                                height: 60,
                                padding: const EdgeInsets.all(8.0),
                                decoration: const BoxDecoration(
                                  border: Border(
                                    bottom: BorderSide(color: Colors.grey),
                                  ),
                                ),
                                alignment: Alignment.centerLeft,
                                child: Text(managerActivity.empName ?? ''),
                              ),
                              // Container(
                              //   height: 60,
                              //   padding: const EdgeInsets.all(8.0),
                              //   decoration: const BoxDecoration(
                              //     border: Border(
                              //       bottom: BorderSide(color: Colors.grey),
                              //     ),
                              //   ),
                              //   alignment: Alignment.center,
                              //   child: Text(managerActivity.empCode ?? ''),
                              // ),
                              Container(
                                height: 60,
                                padding: const EdgeInsets.all(8.0),
                                decoration: const BoxDecoration(
                                  border: Border(
                                    bottom: BorderSide(color: Colors.grey),
                                  ),
                                ),
                                alignment: Alignment.center,
                                child: Text(managerActivity.inTime ?? ''),
                              ),
                              Container(
                                height: 60,
                                padding: const EdgeInsets.all(8.0),
                                decoration: const BoxDecoration(
                                  border: Border(
                                    bottom: BorderSide(color: Colors.grey),
                                  ),
                                ),
                                alignment: Alignment.center,
                                child: Text(managerActivity.counterVisit ?? ''),
                              ),
                            ]),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            );
          }
        },
      ),
    );
  }
}
