import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:sqflite/sqflite.dart';
import 'package:starsfa/models/bank_master_class.dart';
import 'package:starsfa/models/branch_master_class.dart';
import 'package:starsfa/models/competitor_group_master_class.dart';
import 'package:starsfa/models/complaint_report.dart';
import 'package:starsfa/models/customer_master_class.dart';
import 'package:starsfa/models/data_download_dictonary.dart';
import 'package:starsfa/models/destination_master.dart';
import 'package:starsfa/models/employee_master_class.dart';
import 'package:starsfa/models/golden_rules_class.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/market_feedback_details_class.dart';
import 'package:starsfa/models/menu_access_class.dart';
import 'package:starsfa/models/menu_details_class.dart';
import 'package:starsfa/models/order_details_class.dart';
import 'package:starsfa/models/product_details_class.dart';
import 'package:starsfa/models/route_master_class.dart';
import 'package:starsfa/models/route_plan_details_class.dart';
import 'package:starsfa/models/route_plan_transaction_class.dart';
import 'package:starsfa/models/self_appraisal_branch_wise_class.dart';
import 'package:starsfa/models/self_appraisal_customer_wise_class.dart';
import 'package:starsfa/models/survey_input_class.dart';
import 'package:starsfa/models/survey_form_details_class.dart';
import 'package:starsfa/models/survey_table_view_class.dart';
import 'package:starsfa/models/user_details_class.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:starsfa/models/product_master_download.dart';
import 'package:starsfa/models/yellow_card_date_validation_customerwise_class.dart';
import 'package:starsfa/screens/home_screen.dart';

class DataDownloadDictionaryScreen extends StatefulWidget {
  final bool incrementalDownload;
  final bool isSync;
  const DataDownloadDictionaryScreen(
      {super.key, required this.incrementalDownload, this.isSync = false});

  @override
  State<DataDownloadDictionaryScreen> createState() =>
      _DataDownloadDictionaryScreenState();
}

class _DataDownloadDictionaryScreenState
    extends State<DataDownloadDictionaryScreen> {
  late Future<DataDownloadDictionary> future;
  Map<String, dynamic> menuDetailsMap = {
    'menu_details': {
      'screen_name': 'Menu Details',
      'get_data': MenuDetailsClass.getMenuDetails,
      'status': null,
      'isCalled': false
    },
    'user_details': {
      'screen_name': 'User Details',
      'get_data': UserDetailsClass.getUserDetails,
      'status': null,
      'isCalled': false,
    },
    'order_details': {
      'screen_name': 'Order Details',
      'get_data': OrderFormDetailsClass.getOrderDetails,
      'status': null,
      'isCalled': false,
    },
    'product_details': {
      'screen_name': 'Product Details',
      'get_data': ProductDetailsClass.getProductDetails,
      'status': null,
      'isCalled': false
    },
    'route_plan_details': {
      'screen_name': 'Route Plan Details',
      'get_data': RoutePlanDetailsClass.getRoutePlanDetails,
      'status': null,
      'isCalled': false
    },
    'survey_form_details': {
      'screen_name': 'Survey Form Details',
      'get_data': SurveyFormDetailsClass.getSurveyFormDetails,
      'status': null,
      'isCalled': false
    },
    'survey_table_view': {
      'screen_name': 'Survey Table View',
      'get_data': SurveyTableViewClass.getSurveyTableView,
      'status': null,
      'isCalled': false
    },
    'market_feedback_details': {
      'screen_name': 'Market Feedback Details',
      'get_data': MarketFeedbackDetailsClass.getMarketFeedbackDetails,
      'status': null,
      'isCalled': false
    },
    'route_master': {
      'screen_name': 'Route Master',
      'get_data': RouteMasterClass.getRouteMaster,
      'status': null,
      'isCalled': false
    },
    'bank_master': {
      'screen_name': 'Bank Master',
      'get_data': BankMasterClass.getBankMaster,
      'status': null,
      'isCalled': false
    },
    'customer_master': {
      'screen_name': 'Customer Master',
      'get_data': CustomerMasterClass.getCustomerMaster,
      'status': null,
      'isCalled': false
    },
    'credit_limit': {
      'screen_name': 'Credit Limit',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'outstanding_master': {
      'screen_name': 'Outstanding Master',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'product_group_master': {
      'screen_name': 'Product Group Master',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'product_master': {
      'screen_name': 'Product Master',
      'get_data': ProductMasterInputClass.getProductMasterInput,
      'status': null,
      'isCalled': false
    },
    'route_plan': {
      'screen_name': 'Route Plan',
      'get_data': RoutePlanTransactionClass.getRoutePlanTransaction,
      'status': null,
      'isCalled': false
    },
    'emp_master': {
      'screen_name': 'Employee Master',
      'get_data': EmployeeMasterClass.getEmployeeMaster,
      'status': null,
      'isCalled': false
    },
    'branch_master': {
      'screen_name': 'Branch Master',
      'get_data': BranchMasterClass.getBranchMaster,
      'status': null,
      'isCalled': false
    },
    'survey_category_master': {
      'screen_name': 'Survey Category Master',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'survey_input_details': {
      'screen_name': 'Survey Input Details',
      'get_data': SurveyInputClass.getSurveyInput,
      'status': null,
      'isCalled': false
    },
    'generic_oil_master': {
      'screen_name': 'Generic Oil Master',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'competitor_group_master': {
      'screen_name': 'Competitor Group Master',
      'get_data': CompetitorGroupMasterClass.getCompetitorGroupMaster,
      'status': null,
      'isCalled': false
    },
    'menu_access': {
      'screen_name': 'Menu Access',
      'get_data': MenuAccessClass.getMenuAccess,
      'status': null,
      'isCalled': false
    },
    'destination_master': {
      'screen_name': 'Destination Master',
      'get_data': DestinationMasterClass.getDestinationMaster,
      'status': null,
      'isCalled': false
    },
    'self_appraisal_details': {
      'screen_name': 'Self Appraisal Details',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'self_appraisal_customer_wise': {
      'screen_name': 'Self Appraisal Customer Wise',
      'get_data': SelfAppraisalCustomerWiseClass.getSelfAppraisalCustomerWise,
      'status': null,
      'isCalled': false
    },
    'self_appraisal_branch_wise': {
      'screen_name': 'Self Appraisal Branch Wise',
      'get_data': SelfAppraisalBranchWiseClass.getSelfAppraisalBranchWise,
      'status': null,
      'isCalled': false
    },
    'branchwise_scheme_PDF': {
      'screen_name': 'Branchwise Scheme PDF',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'golden_rules': {
      'screen_name': 'Golden Rules',
      'get_data': GoldenRulesClass.getGoldenRules,
      'status': null,
      'isCalled': false
    },
    'yellow-card-date-validation': {
      'screen_name': 'Yellow Card Date Validation',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'yellow-card-date-validation_customerwise': {
      'screen_name': 'Yellow Card Date Validation Customer Wise',
      'get_data':
          YellowCardDateValidationCustomerWiseClass.getYellowCardDateValidation,
      'status': null,
      'isCalled': false
    },
    'attendance_checkout_details': {
      'screen_name': 'Attendance Checkout Details',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
    'branch_geo_fencing': {
      'screen_name': 'Branch Geo Fencing',
      'get_data': null,
      'status': null,
      'isCalled': false
    },
     'complaint_master': {
      'screen_name': 'Complaint Report',
      'get_data': ComplaintReport.getComplaintReportData,
      'status': null,
      'isCalled': false
    }
  };

  Future<void> gotoHomeScreen() async {
    while (menuDetailsMap.values
        .where((element) => element['status'] == null)
        .isNotEmpty) {
      // sort the menuDetailsMap by status
      // menuDetailsMap.entries.toList().sort((a, b) {
      //   if (a.value['status'] == null) {
      //     return 1;
      //   } else if (b.value['status'] == null) {
      //     return -1;
      //   } else {
      //     return 0;
      //   }
      // });
      await Future.delayed(const Duration(seconds: 3));
    }
    // ignore: use_build_context_synchronously
    Navigator.of(context).pushAndRemoveUntil(
        MaterialPageRoute(
            builder: (context) => HomeScreen(
                  showPopup: widget.isSync ? false : true,
                )),
        (route) => false);
  }

  @override
  void initState() {
    super.initState();
    future = DataDownloadDictionary.getTableNames(widget.incrementalDownload);
    UserLoginClass.setincrementalDownload(widget.incrementalDownload);
    gotoHomeScreen();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.red,
        title: const Text(
          'Data Download Dictionary',
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
        child: FutureBuilder<DataDownloadDictionary>(
          future: future,
          builder: (BuildContext context, AsyncSnapshot snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(
                child: CircularProgressIndicator(),
              );
            } else if (snapshot.hasError) {
              return Center(
                child: Text('Error: ${snapshot.error}'),
              );
            } else {
              final DataDownloadDictionary data = snapshot.data;
              final List<String> tableNames = data.tableNames ?? [];
              for (var e in tableNames) {
                if (menuDetailsMap.containsKey(e)) {
                  if (menuDetailsMap[e]['isCalled'] == false) {
                    menuDetailsMap[e]['isCalled'] = true;
                    getData(e);
                  }
                }
              }
              // remove all tables from menuDetailsMap if that doesn't exists in tableNames
              menuDetailsMap
                  .removeWhere((key, value) => !tableNames.contains(key));
              // remove all tables from tableNames where getData is null
              tableNames.removeWhere((element) {
                return menuDetailsMap[element]['get_data'] == null;
              });

              return Column(
                children: [
                  // ProgressBar with the number of tables downloaded and total tables inside the progress bar
                  _progressIndicator(),
                  Expanded(
                    child: ListView.builder(
                      itemCount: tableNames.length,
                      itemBuilder: (BuildContext context, int index) {
                        return Padding(
                          padding: const EdgeInsets.all(10),
                          child: ListTile(
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                              title: Text(
                                menuDetailsMap[tableNames[index]]
                                    ['screen_name'],
                                style: const TextStyle(
                                    color:
                                        // (menuDetailsMap[tableNames[index]]
                                        //             ['status'] !=
                                        //         null)
                                        //     ? Colors.green
                                        //     :
                                        Colors.black),
                              ),
                              // tileColor: Colors.red,
                              // (menuDetailsMap[tableNames[index]]
                              //             ['status'] ==
                              //         null)
                              //     ?
                              // Colors.white60,
                              // : (menuDetailsMap[tableNames[index]]
                              //             ['status'] ==
                              //         true)
                              //     ? Colors.green.shade600
                              //     : Colors.red.shade600,
                              leading: (menuDetailsMap[tableNames[index]]
                                          ['status'] ==
                                      null)
                                  ? const CircularProgressIndicator(
                                      color: Colors.black,
                                    )
                                  : Icon(
                                      (menuDetailsMap[tableNames[index]]
                                                  ['status'] ==
                                              true)
                                          ? Icons.download_done
                                          : (menuDetailsMap[tableNames[index]]
                                                      ['get_data'] ==
                                                  null)
                                              ? Icons.error_outline_outlined
                                              : Icons.file_download_off,
                                      color: Colors.black,
                                    )),
                        );
                      },
                    ),
                  ),
                ],
              );
            }
          },
        ),
      ),
    );
  }

  Container _progressIndicator() {
    return Container(
      padding: const EdgeInsets.all(10),
      margin: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(10),
        color: Colors.white,
        boxShadow: const [
          BoxShadow(
            color: Colors.grey,
            blurRadius: 5,
            offset: Offset(0, 3),
          )
        ],
      ),
      child: Column(
        children: [
          Text(
            '${menuDetailsMap.values.where((element) => element['status'] == true).length} / ${menuDetailsMap.length} Downloaded (Success: ${menuDetailsMap.values.where((element) => element['status'] == true).length}, Failed: ${menuDetailsMap.values.where((element) => element['status'] == false).length}, Pending: ${menuDetailsMap.values.where((element) => element['status'] == null).length})',
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(
            height: 10,
          ),
          LinearProgressIndicator(
            borderRadius: BorderRadius.circular(10),
            value: menuDetailsMap.values
                    .where((element) => element['status'] != null)
                    .length /
                menuDetailsMap.length,
            valueColor: const AlwaysStoppedAnimation<Color>(Colors.green),
            backgroundColor: Colors.grey,
            minHeight: 10,
          ),
        ],
      ),
    );
  }

  void getData(String tableName) async {
    if (menuDetailsMap[tableName]['status'] == null &&
        menuDetailsMap[tableName]['get_data'] != null) {
      final bool status = await menuDetailsMap[tableName]['get_data']();
      setState(() {
        menuDetailsMap[tableName]['status'] = status;
      });
      if (status) {
        // insert the data in table data_download_log
        final localDB = await LocalDB.openMyDatabase();
        // insert and if already exists then replace
        await localDB.insert(
          'data_download_log',
          {
            'table_name': tableName,
            // 2024-05-14?06:50:36
            'last_download_time': DateFormat('yyyy-MM-dd HH:mm:ss')
                .format(DateTime.now())
                .toString(),
            'is_download': 'yes',
            'is_refresh': 'no',
          },
          conflictAlgorithm: ConflictAlgorithm.replace,
        );
      }
    } else {
      if (menuDetailsMap[tableName]['get_data'] == null) {
        menuDetailsMap[tableName]['status'] = false;
      }
    }
  }
}
