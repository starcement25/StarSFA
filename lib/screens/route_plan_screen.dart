// ignore_for_file: use_build_context_synchronously

import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'package:intl/intl.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/route_plan_transaction_class.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:table_calendar/table_calendar.dart';

class RoutePlanScreen extends StatefulWidget {
  const RoutePlanScreen({super.key});

  @override
  State<RoutePlanScreen> createState() => _RoutePlanScreenState();
}

class _RoutePlanScreenState extends State<RoutePlanScreen> {
  void showRoutes(DateTime selectedDate) {
    // check if the date is in the past
    if (selectedDate.isBefore(DateTime(
        DateTime.now().year, DateTime.now().month, DateTime.now().day))) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
        content: Text('Cannot create Route Plan for past dates'),
        duration: Duration(seconds: 2),
      ));
      return;
    }
    showDialog(
        context: context,
        builder: (context) {
          List<String> selectedRoutes = [];
          bool isLoading = false;
          String search = '';
          List<RoutePlanTransactionDB> filteredRoutes = [];
          Future<List<RoutePlanTransactionDB>> getRouteMaster() async {
            final localDB = await LocalDB.openMyDatabase();
            final List<Map<String, dynamic>> routeMasterData = await localDB
                .rawQuery('SELECT * FROM route_master ORDER BY route_name ASC');
            final List<RoutePlanTransactionDB> routeMasterList = [];
            for (int i = 0; i < routeMasterData.length; i++) {
              routeMasterList
                  .add(RoutePlanTransactionDB.fromMap(routeMasterData[i]));
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
            setState(() {
              selectedRoutes = selectedRouteCodes;
            });

            return routeMasterList;
          }

          Future<void> saveRoutePlan(DateTime selectedDate,
              List<RoutePlanTransactionDB> selectedRoutes) async {
            final user = await UserLoginClass.getLocalUser();
            final localDB = await LocalDB.openMyDatabase();
            final batch = localDB.batch();
            // delete existing route plan for the selected date
            batch.delete('route_plan_transaction',
                where: 'visit_date = ?',
                whereArgs: [
                  DateFormat(RoutePlanCalendar.dateFormat).format(selectedDate)
                ]);
            // insert new route plan
            for (int i = 0; i < selectedRoutes.length; i++) {
              final Map<String, String> routePlanMap = {
                'route_plan_trans_id':
                    'RP${user?.empCode}${DateFormat('yyyyMMddHHmmss').format(DateTime.now())}',
                'emp_code': user?.empCode ?? '',
                'route_code': selectedRoutes[i].routeCode ?? '',
                'visit_date': DateFormat(RoutePlanCalendar.dateFormat)
                    .format(selectedDate),
                'create_date':
                    DateFormat('yyyy-MM-dd HH:mm:ss').format(DateTime.now()),
                'route_name': selectedRoutes[i].routeName ?? '',
                'flag': '0',
                'status': 'active',
                'working_with': '',
              };
              batch.insert('route_plan_transaction', routePlanMap);
            }
            // commit the batch
            await batch.commit();
            // change the selected date to trigger the ValueListenableBuilder
            // await Hive.box(RoutePlanCalendar.routePlanBoxKey)
            //     .put(RoutePlanCalendar.selectedDateKey, '');
            await Hive.box(RoutePlanCalendar.routePlanBoxKey).put(
                RoutePlanCalendar.selectedDateKey,
                DateFormat(RoutePlanCalendar.dateFormat).format(selectedDate));
            await RoutePlanTransactionClass.saveRoutePlanTransactionServer();
            // Pop Context
            Navigator.of(context).pop();
          }

          final Future<List<RoutePlanTransactionDB>> routeMasterList =
              getRouteMaster();

          return StatefulBuilder(builder: (context, setState) {
            return Dialog(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
              // opaqueness of the dialog
              backgroundColor: Colors.white,
              insetPadding: const EdgeInsets.all(20),
              clipBehavior: Clip.hardEdge,
              child: Container(
                padding: const EdgeInsets.all(8.0),
                decoration: const BoxDecoration(
                  // image: DecorationImage(
                  //   image: AssetImage('assets/background.jpg'),
                  //   fit: BoxFit.cover,
                  // ),
                  color: Color.fromARGB(255, 236, 229, 221),
                ),
                child: Column(
                  children: [
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.symmetric(
                          vertical: 8, horizontal: 10),
                      decoration: BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      alignment: Alignment.center,
                      child: Row(
                        children: [
                          IconButton(
                            icon: const Icon(
                              Icons.arrow_back,
                              color: Colors.white,
                            ),
                            onPressed: () {
                              Navigator.of(context).pop();
                            },
                          ),
                          Expanded(
                            child: Text(
                              'Route Plan for ${DateFormat('d MMM yyyy').format(selectedDate)}',
                              style: const TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.bold),
                              textAlign: TextAlign.center,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 10),
                    Expanded(
                      child: FutureBuilder<List<RoutePlanTransactionDB>>(
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
                              final List<RoutePlanTransactionDB>
                                  routeMasterList = snapshot.data ?? [];
                              if (search.isEmpty) {
                                filteredRoutes = routeMasterList;
                              }
                              return Column(
                                children: [
                                  // Search bar
                                  Padding(
                                    padding: const EdgeInsets.symmetric(
                                        horizontal: 8.0),
                                    child: TextField(
                                      onChanged: (value) {
                                        setState(() {
                                          search = value;
                                          filteredRoutes = routeMasterList
                                              .where((route) =>
                                                  route.routeName
                                                      ?.toLowerCase()
                                                      .contains(search
                                                          .toLowerCase()) ??
                                                  false)
                                              .toList();
                                        });
                                      },
                                      decoration: const InputDecoration(
                                        labelText: 'Search',
                                        prefixIcon: Icon(Icons.search),
                                      ),
                                    ),
                                  ),
                                  Expanded(
                                    child: ListView.separated(
                                      separatorBuilder: (context, index) =>
                                          const Divider(
                                        color: Colors.grey,
                                      ),
                                      shrinkWrap: true,
                                      itemCount: filteredRoutes.length,
                                      itemBuilder: (context, index) {
                                        return ListTile(
                                          onTap: () {
                                            setState(() {
                                              if (selectedRoutes.contains(
                                                  filteredRoutes[index]
                                                      .routeCode)) {
                                                if (selectedDate ==
                                                    DateTime(
                                                      DateTime.now().year,
                                                      DateTime.now().month,
                                                      DateTime.now().day,
                                                    )) {
                                                  return;
                                                }
                                                selectedRoutes.remove(
                                                    filteredRoutes[index]
                                                        .routeCode);
                                              } else {
                                                selectedRoutes.add(
                                                    filteredRoutes[index]
                                                            .routeCode ??
                                                        '');
                                              }
                                            });
                                          },
                                          // checkboxes
                                          leading: Checkbox(
                                              value: selectedRoutes.contains(
                                                  filteredRoutes[index]
                                                      .routeCode),
                                              checkColor: Colors.white,
                                              activeColor: Colors.green,
                                              onChanged: (value) {
                                                setState(() {
                                                  if (selectedRoutes.contains(
                                                      filteredRoutes[index]
                                                          .routeCode)) {
                                                    if (selectedDate ==
                                                        DateTime(
                                                          DateTime.now().year,
                                                          DateTime.now().month,
                                                          DateTime.now().day,
                                                        )) {
                                                      return;
                                                    }
                                                    selectedRoutes.remove(
                                                        filteredRoutes[index]
                                                            .routeCode);
                                                  } else {
                                                    selectedRoutes.add(
                                                        filteredRoutes[index]
                                                                .routeCode ??
                                                            '');
                                                  }
                                                });
                                              }),
                                          title: Text(
                                              filteredRoutes[index].routeName ??
                                                  ''),
                                          // subtitle: Text(routeMasterList[index].routeCode ?? ''),
                                        );
                                      },
                                    ),
                                  ),
                                  const SizedBox(height: 10),
                                  // save button
                                  InkWell(
                                    onTap: () async {
                                      setState(() {
                                        isLoading = true;
                                      });
                                      saveRoutePlan(
                                              selectedDate,
                                              routeMasterList
                                                  .where((element) =>
                                                      selectedRoutes.contains(
                                                          element.routeCode))
                                                  .toList())
                                          .then((value) {
                                        setState(() {
                                          isLoading = false;
                                        });
                                        // pop up message
                                        ScaffoldMessenger.of(context)
                                            .showSnackBar(SnackBar(
                                          content: Text(
                                              'Route Plan saved for ${DateFormat('d MMM yyyy').format(selectedDate)}'),
                                          duration: const Duration(seconds: 2),
                                        ));
                                        Navigator.of(context).pop();
                                      });
                                    },
                                    child: Container(
                                      width: double.infinity,
                                      padding: const EdgeInsets.symmetric(
                                          vertical: 8),
                                      decoration: BoxDecoration(
                                        color: Colors.red,
                                        borderRadius: BorderRadius.circular(10),
                                      ),
                                      alignment: Alignment.center,
                                      child: isLoading
                                          ? const CircularProgressIndicator(
                                              valueColor:
                                                  AlwaysStoppedAnimation<Color>(
                                                      Colors.white),
                                            )
                                          : const Text(
                                              'Submit',
                                              style: TextStyle(
                                                  color: Colors.white,
                                                  fontWeight: FontWeight.bold),
                                            ),
                                    ),
                                  ),
                                ],
                              );
                            }
                          }),
                    ),
                  ],
                ),
              ),
            );
          });
        });
  }

  Future<List<RoutePlanTransactionDB>> getExistingRoutePlan(
      DateTime selectedDate) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> routePlanData = await localDB.rawQuery(
        // 'SELECT * FROM route_plan_transaction WHERE visit_date = ?',
        'SELECT * FROM route_plan_transaction, route_master WHERE route_plan_transaction.route_code = route_master.route_code AND visit_date = ?',
        [DateFormat(RoutePlanCalendar.dateFormat).format(selectedDate)]);
    final List<RoutePlanTransactionDB> routeMasterList = [];
    for (int i = 0; i < routePlanData.length; i++) {
      routeMasterList.add(RoutePlanTransactionDB.fromMap(routePlanData[i]));
    }
    return routeMasterList;
  }

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          color: Colors.white,
          onPressed: () {
            Navigator.of(context).pop();
          },
        ),
        backgroundColor: Colors.red,
        title: const Text(
          'Route Plan',
          style: TextStyle(
            color: Colors.white,
          ),
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
          children: <Widget>[
            // A Calendar widget with onTap, onLongPress, onDoubleTap and onSwipe callbacks
            RoutePlanCalendar(onLongPress: showRoutes),
            Expanded(
              child: ValueListenableBuilder<Box>(
                valueListenable:
                    Hive.box(RoutePlanCalendar.routePlanBoxKey).listenable(),
                builder: (context, box, child) {
                  final String selectedDateString =
                      box.get(RoutePlanCalendar.selectedDateKey) as String;
                  if (selectedDateString.isEmpty) {
                    return const Center(
                      child: Text(
                        'Select a date to view the Route Plan',
                        style: TextStyle(color: Colors.black, fontSize: 16),
                      ),
                    );
                  }
                  final DateTime selectedDate =
                      DateFormat(RoutePlanCalendar.dateFormat)
                          .parse(selectedDateString);
                  final Future<List<RoutePlanTransactionDB>> routeMasterList =
                      getExistingRoutePlan(selectedDate);
                  return Column(
                    children: [
                      Container(
                        width: double.infinity,
                        margin: const EdgeInsets.all(10),
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          // color: Colors.grey[200],
                          color: Colors.red,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Column(
                          children: [
                            Text(
                              'Route Plan for ${DateFormat('d MMM yyyy').format(selectedDate)}',
                              style: const TextStyle(
                                  color: Colors.white, fontSize: 16),
                            ),
                          ],
                        ),
                      ),
                      Expanded(
                        child: Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(10),
                          margin: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            // color: Colors.grey[200],
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: FutureBuilder<List<RoutePlanTransactionDB>>(
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
                                  final List<RoutePlanTransactionDB>
                                      routeMasterList = snapshot.data ?? [];
                                  return routeMasterList.isEmpty
                                      ? Center(
                                          child: Column(
                                            mainAxisAlignment:
                                                MainAxisAlignment.center,
                                            children: [
                                              const Text(
                                                'No Route Plan found',
                                                style: TextStyle(
                                                    color: Colors.black,
                                                    fontSize: 16),
                                              ), // create a new route plan button
                                              InkWell(
                                                onTap: () {
                                                  showRoutes(selectedDate);
                                                },
                                                child: Container(
                                                  // width: double.infinity,
                                                  margin: const EdgeInsets.only(
                                                      top: 10),
                                                  padding: const EdgeInsets
                                                      .symmetric(vertical: 8),
                                                  decoration: BoxDecoration(
                                                    color: Colors.red,
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                            10),
                                                  ),
                                                  alignment: Alignment.center,
                                                  child: const Text(
                                                    'Create New Route Plan',
                                                    style: TextStyle(
                                                        color: Colors.white,
                                                        fontWeight:
                                                            FontWeight.bold),
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                        )
                                      : Column(
                                          children: [
                                            Expanded(
                                              child: ListView.separated(
                                                separatorBuilder:
                                                    (context, index) =>
                                                        const Divider(
                                                  color: Colors.grey,
                                                ),
                                                shrinkWrap: true,
                                                itemCount:
                                                    routeMasterList.length,
                                                itemBuilder: (context, index) {
                                                  return ListTile(
                                                      tileColor: Colors.white,
                                                      title: Text(
                                                          routeMasterList[index]
                                                                  .routeName ??
                                                              ''),
                                                      subtitle: Text(
                                                          routeMasterList[index]
                                                                  .routeCode ??
                                                              ''),
                                                      trailing: Column(
                                                        children: [
                                                          const Text(
                                                            'Created on',
                                                            style: TextStyle(
                                                              color: Colors
                                                                  .black54,
                                                              fontSize: 12,
                                                            ),
                                                          ),
                                                          Text(
                                                            routeMasterList[
                                                                        index]
                                                                    .createDate ??
                                                                '',
                                                            style:
                                                                const TextStyle(
                                                              color: Colors
                                                                  .black54,
                                                              fontSize: 12,
                                                            ),
                                                          ),
                                                        ],
                                                      ));
                                                },
                                              ),
                                            ),
                                            InkWell(
                                              onTap: () {
                                                showRoutes(selectedDate);
                                              },
                                              child: Container(
                                                width: double.infinity,
                                                margin: const EdgeInsets.only(
                                                    top: 10),
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                        vertical: 8),
                                                decoration: BoxDecoration(
                                                  color: Colors.red,
                                                  borderRadius:
                                                      BorderRadius.circular(10),
                                                ),
                                                alignment: Alignment.center,
                                                child: const Text(
                                                  'Add new Route Plan',
                                                  style: TextStyle(
                                                    color: Colors.white,
                                                    fontWeight: FontWeight.bold,
                                                  ),
                                                ),
                                              ),
                                            ),
                                          ],
                                        );
                                }
                              }),
                        ),
                      ),
                    ],
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class RoutePlanCalendar extends StatefulWidget {
  final void Function(DateTime selectedDate) onLongPress;
  const RoutePlanCalendar({super.key, required this.onLongPress});
  static const String routePlanBoxKey = 'routePlanBox';
  static const String selectedDateKey = 'selectedDate';
  static const String dateFormat = 'dd-MM-yyyy';

  @override
  State<RoutePlanCalendar> createState() => _RoutePlanCalendarState();
}

class _RoutePlanCalendarState extends State<RoutePlanCalendar> {
  CalendarFormat _calendarFormat = CalendarFormat.month;
  DateTime _focusedDay = DateTime.now();
  DateTime _selectedDay = DateTime.now();

  Future<bool> isRoutePlanAvailable(DateTime selectedDay) async {
    final localDB = await LocalDB.openMyDatabase();
    final List<Map<String, dynamic>> routePlanData = await localDB.rawQuery(
        'SELECT * FROM route_plan_transaction WHERE visit_date = ?',
        [DateFormat(RoutePlanCalendar.dateFormat).format(selectedDay)]);
    return routePlanData.isNotEmpty;
  }

  void storeSelectedDate(DateTime selectedDay) async {
    final Box routePlanBox = Hive.box(RoutePlanCalendar.routePlanBoxKey);
    await routePlanBox.put(RoutePlanCalendar.selectedDateKey,
        DateFormat(RoutePlanCalendar.dateFormat).format(selectedDay));
  }

  @override
  void initState() {
    super.initState();
    // Open the route plan box
    Hive.openBox(RoutePlanCalendar.routePlanBoxKey);
    storeSelectedDate(_selectedDay);
  }

  @override
  Widget build(BuildContext context) {
    return TableCalendar(
      firstDay: DateTime.now().add(const Duration(days: -365)),
      lastDay: DateTime.now().add(const Duration(days: 365)),
      focusedDay: _focusedDay,
      calendarFormat: _calendarFormat,
      availableCalendarFormats: const {
        CalendarFormat.month: 'Month',
      },
      // change style of the date cell according to the date
      calendarBuilders: CalendarBuilders(
        defaultBuilder: (context, date, _) {
          final Future<bool> isRoutePlanAvailableFuture =
              isRoutePlanAvailable(date);
          return FutureBuilder<bool>(
              future: isRoutePlanAvailableFuture,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.done) {
                  final bool isRoutePlanAvailable = snapshot.data ?? false;
                  if (isRoutePlanAvailable) {
                    // print('Date: $date ${isSameDay(date, DateTime.now())}');
                    // if day is today
                    if (isSameDay(
                        date,
                        DateTime(DateTime.now().year, DateTime.now().month,
                            DateTime.now().day))) {
                      return AnimatedContainer(
                        duration: const Duration(milliseconds: 100),
                        padding: const EdgeInsets.all(0),
                        margin: const EdgeInsets.all(6.0),
                        decoration: BoxDecoration(
                          color: Colors.green[400],
                          shape: BoxShape.circle,
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          date.day.toString(),
                          style: const TextStyle(
                              color: Colors
                                  .white), // Style for dates with route plan
                        ),
                      );
                    }
                    // if previous day
                    else if (date.isBefore(DateTime.now())) {
                      return AnimatedContainer(
                        duration: const Duration(milliseconds: 100),
                        padding: const EdgeInsets.all(0),
                        margin: const EdgeInsets.all(6.0),
                        decoration: BoxDecoration(
                          color: Colors.red[400],
                          shape: BoxShape.circle,
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          date.day.toString(),
                          style: const TextStyle(
                              color: Colors
                                  .white), // Style for dates with route plan
                        ),
                      );
                    } else {
                      return AnimatedContainer(
                        duration: const Duration(milliseconds: 100),
                        padding: const EdgeInsets.all(0),
                        margin: const EdgeInsets.all(6.0),
                        decoration: BoxDecoration(
                          color: Colors.blue[400],
                          shape: BoxShape.circle,
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          date.day.toString(),
                          style: const TextStyle(
                              color: Colors
                                  .white), // Style for dates with route plan
                        ),
                      );
                    }
                  } else {
                    if (date.weekday == DateTime.sunday) {
                      return Center(
                        child: Text(
                          date.day.toString(),
                          style: const TextStyle(
                              color: Colors.red), // Style for weekend dates
                        ),
                      );
                    } else {
                      return Center(
                        child: Text(
                          date.day.toString(),
                          style: const TextStyle(
                              color: Colors.black), // Style for weekday dates
                        ),
                      );
                    }
                  }
                } else {
                  return Center(
                    child: Text(
                      date.day.toString(),
                      style: const TextStyle(
                          color: Colors.black), // Style for weekday dates
                    ),
                  );
                }
              });
        },
      ),
      availableGestures: AvailableGestures.horizontalSwipe,
      selectedDayPredicate: (day) {
        return isSameDay(_selectedDay, day);
      },
      onDaySelected: (selectedDay, focusedDay) async {
        // print('Selected: $selectedDay');
        if (!isSameDay(_selectedDay, selectedDay)) {
          setState(() {
            _selectedDay = selectedDay;
            _focusedDay = focusedDay;
          });
          storeSelectedDate(selectedDay);
        }
      },
      onDayLongPressed: (selectedDay, focusedDay) {
        // print('Long pressed: $selectedDay');
        // shift focus to the selected day
        setState(() {
          _focusedDay = focusedDay;
          _selectedDay = selectedDay;
        });
        storeSelectedDate(selectedDay);
        final DateTime selectedDate = DateFormat(RoutePlanCalendar.dateFormat)
            .parse(
                DateFormat(RoutePlanCalendar.dateFormat).format(selectedDay));
        widget.onLongPress(selectedDate);
      },
      onPageChanged: (focusedDay) {
        _focusedDay = focusedDay;
      },
      onFormatChanged: (format) {
        if (_calendarFormat != format) {
          setState(() {
            _calendarFormat = format;
          });
        }
      },
      // availableGestures: AvailableGestures.all,
    );
  }
}
