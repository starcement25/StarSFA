import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'dart:developer';
import 'package:starsfa/models/dash_emp_name.dart';

class DashboardDataClass {
  List<List<String>>? dashboardDataClassData;

  DashboardDataClass({this.dashboardDataClassData});

  factory DashboardDataClass.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    log(txt);
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> dashboardDataClassData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      dashboardDataClassData.add(temp);
    }
    return DashboardDataClass(dashboardDataClassData: dashboardDataClassData);
  }

  static Future<List<DashEmpName>> getSelfAppraisalCustomerWise() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return [];
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.dashboardDataUrl}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}');
    // get response from the server
    http.Response response = await http.get(url);

    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final DashboardDataClass mDashboardDataClass =
            DashboardDataClass.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<DashboardDataDB> mDashboardDataDBDB = [];
        for (int i = 0;
            i < mDashboardDataClass.dashboardDataClassData!.length;
            i++) {
          final DashboardDataDB mDashboardDataClassLocal = DashboardDataDB(
            empCode: mDashboardDataClass.dashboardDataClassData?[i][0],
            empName: mDashboardDataClass.dashboardDataClassData?[i][1],
            dataDate: mDashboardDataClass.dashboardDataClassData?[i][2],
            attTime: mDashboardDataClass.dashboardDataClassData?[i][3],
            chkOutTime: mDashboardDataClass.dashboardDataClassData?[i][4],
            counterMeet: mDashboardDataClass.dashboardDataClassData?[i][5],
            megaMasonMeet: mDashboardDataClass.dashboardDataClassData?[i][6],
            engineersMeet: mDashboardDataClass.dashboardDataClassData?[i][7],
            professionalMeet: mDashboardDataClass.dashboardDataClassData?[i][8],
            contractorMeet: mDashboardDataClass.dashboardDataClassData?[i][9],
            dealerSubdealerMeet: mDashboardDataClass.dashboardDataClassData?[i]
                [10],
            complaint: mDashboardDataClass.dashboardDataClassData?[i][11],
            masonMeet: mDashboardDataClass.dashboardDataClassData?[i][12],
            ihbMeet: mDashboardDataClass.dashboardDataClassData?[i][13],
            smallEngineersMeet: mDashboardDataClass.dashboardDataClassData?[i]
                [14],
            bigContractorMeet: mDashboardDataClass.dashboardDataClassData?[i]
                [15],
            catchThemYoung: mDashboardDataClass.dashboardDataClassData?[i][16],
            pcTraningProgramme: mDashboardDataClass.dashboardDataClassData?[i]
                [17],
            customerGuidanceCamp: mDashboardDataClass.dashboardDataClassData?[i]
                [18],
            siteVisit: mDashboardDataClass.dashboardDataClassData?[i][19],
            complaintReport: mDashboardDataClass.dashboardDataClassData?[i][20],
            dhalaiService: mDashboardDataClass.dashboardDataClassData?[i][21],
          );
          mDashboardDataDBDB.add(mDashboardDataClassLocal);
        }
        final batch = localDB.batch();
        for (int i = 0; i < mDashboardDataDBDB.length; i++) {
          batch.insert(
              'mis_details_emp_datewise', mDashboardDataDBDB[i].toJson());
          //log("mis_details_emp_json- $mDashboardDataDBDB[i].toJson()");
        }
        await batch.commit(noResult: true);
        //return true;
        final List<Map<String, dynamic>> records1 = await localDB.rawQuery(
            'select distinct emp_code,emp_name from  mis_details_emp_datewise');
        log("$records1");

        return records1.map((json) => DashEmpName.fromJson(json)).toList();
      } else {
        //return false;
        final localDB = await LocalDB.openMyDatabase();
        final List<Map<String, dynamic>> records1 = await localDB.rawQuery(
            'select distinct emp_code,emp_name from  mis_details_emp_datewise');
        log("$records1");

        return records1.map((json) => DashEmpName.fromJson(json)).toList();
      }
    } else {
      //return false;
      final localDB = await LocalDB.openMyDatabase();
      final List<Map<String, dynamic>> records1 = await localDB.rawQuery(
          'select distinct emp_code,emp_name from  mis_details_emp_datewise');
      log("$records1");

      return records1.map((json) => DashEmpName.fromJson(json)).toList();
    }
  }
}

class DashboardDataDB {
  String? empCode;
  String? empName;
  String? dataDate;
  String? attTime;
  String? chkOutTime;
  String? counterMeet;
  String? megaMasonMeet;
  String? engineersMeet;
  String? professionalMeet;
  String? contractorMeet;
  String? dealerSubdealerMeet;
  String? complaint;
  String? masonMeet;
  String? ihbMeet;
  String? smallEngineersMeet;
  String? bigContractorMeet;
  String? catchThemYoung;
  String? pcTraningProgramme;
  String? customerGuidanceCamp;
  String? siteVisit;
  String? complaintReport;
  String? dhalaiService;

  DashboardDataDB(
      {this.empCode,
      this.empName,
      this.dataDate,
      this.attTime,
      this.chkOutTime,
      this.counterMeet,
      this.megaMasonMeet,
      this.engineersMeet,
      this.professionalMeet,
      this.contractorMeet,
      this.dealerSubdealerMeet,
      this.complaint,
      this.masonMeet,
      this.ihbMeet,
      this.smallEngineersMeet,
      this.bigContractorMeet,
      this.catchThemYoung,
      this.pcTraningProgramme,
      this.customerGuidanceCamp,
      this.siteVisit,
      this.complaintReport,
      this.dhalaiService});

  Map<String, dynamic> toJson() {
    return {
      'emp_code': empCode,
      'emp_name': empName,
      'data_date': dataDate,
      'att_time': attTime,
      'chk_out_time': chkOutTime,
      'counter_meet': counterMeet,
      'mega_mason_meet': megaMasonMeet,
      'engineers_meet': engineersMeet,
      'professional_meet': professionalMeet,
      'contractor_meet': contractorMeet,
      'dealer_subdealer_meet': dealerSubdealerMeet,
      'complaint': complaint,
      'mason_meet': masonMeet,
      'IHB_meet': ihbMeet,
      'small_engineers_meet': smallEngineersMeet,
      'big_contractor_meet': bigContractorMeet,
      'catch_them_young': catchThemYoung,
      'pc_traning_programme': pcTraningProgramme,
      'customer_guidance_camp': customerGuidanceCamp,
      'site_visit': siteVisit,
      'complaint_report': complaintReport,
      'dhalai_service': dhalaiService,
    };
  }

  factory DashboardDataDB.fromJson(Map<String, dynamic> json) {
    return DashboardDataDB(
      empCode: json['emp_code'],
      empName: json['emp_name'],
      dataDate: json['data_date'],
      attTime: json['att_time'],
      chkOutTime: json['chk_out_time'],
      counterMeet: json['counter_meet'],
      megaMasonMeet: json['mega_mason_meet'],
      engineersMeet: json['engineers_meet'],
      professionalMeet: json['professional_meet'],
      contractorMeet: json['contractor_meet'],
      dealerSubdealerMeet: json['dealer_subdealer_meet'],
      complaint: json['complaint'],
      masonMeet: json['mason_meet'],
      ihbMeet: json['IHB_meet'],
      smallEngineersMeet: json['small_engineers_meet'],
      bigContractorMeet: json['big_contractor_meet'],
      catchThemYoung: json['catch_them_young'],
      pcTraningProgramme: json['pc_traning_programme'],
      customerGuidanceCamp: json['customer_guidance_camp'],
      siteVisit: json['site_visit'],
      complaintReport: json['complaint_report'],
      dhalaiService: json['dhalai_service'],
    );
  }

  // get all the records from the table
  static Future<List<DashboardDataDB>> getAllRecords(
      String sVal, String dateVal) async {
    final localDB = await LocalDB.openMyDatabase();
    String sVal1 = sVal;
    String dateVal1 = dateVal;
    //date_val1 = "2024-07-23";
    // final List<Map<String, dynamic>> records =
    //   await localDB.query('mis_details_emp_datewise');
    String sql =
        "select emp_code,emp_name,data_date,att_time,chk_out_time,counter_meet,mega_mason_meet,engineers_meet,professional_meet,contractor_meet,dealer_subdealer_meet,complaint,mason_meet,IHB_meet,small_engineers_meet,big_contractor_meet,catch_them_young,pc_traning_programme,customer_guidance_camp,site_visit,complaint_report,dhalai_service from  mis_details_emp_datewise where emp_name='$sVal1' and data_date='$dateVal1'";
    log("sql- $sql");
    final List<Map<String, dynamic>> records1 = await localDB.rawQuery(sql);

    log("$records1");
    return records1
        .map((dashboardDataDB) => DashboardDataDB.fromJson(dashboardDataDB))
        .toList();
  }

  static Future<List<DashboardDataDB>> getAllEmpNameRecords() async {
    final localDB = await LocalDB.openMyDatabase();

    final List<Map<String, dynamic>> records1 = await localDB
        .rawQuery('select emp_code,emp_name from  mis_details_emp_datewise');
    log("$records1");
    return records1
        .map((dashboardDataDB) => DashboardDataDB.fromJson(dashboardDataDB))
        .toList();
  }

  Future<List<DashEmpName>> getDashEmpList() async {
    final localDB = await LocalDB.openMyDatabase();

    final List<Map<String, dynamic>> records1 = await localDB
        .rawQuery('select emp_code,emp_name from  mis_details_emp_datewise');
    log("$records1");
    return records1.map((json) => DashEmpName.fromJson(json)).toList();
  }

  Future<List<DashEmpName>> getdata() async {
    return DashboardDataClass.getSelfAppraisalCustomerWise();
  }
}
