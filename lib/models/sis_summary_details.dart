import 'dart:convert';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class SisSummaryDetails {
  String? processStatus;
  String? processMessage;
  String? countRows;
  String? countColumns;
  String? dateTime;
  List<DataValue>? dataValue;

  SisSummaryDetails({
    this.processStatus,
    this.processMessage,
    this.countRows,
    this.countColumns,
    this.dateTime,
    this.dataValue,
  });

  factory SisSummaryDetails.fromJson(Map<String, dynamic> json) {
    return SisSummaryDetails(
      processStatus: json['process_status'],
      processMessage: json['process_message'],
      countRows: json['count_rows'],
      countColumns: json['count_columns'],
      dateTime: json['date_time'],
      dataValue: (json['datavalue'] as List)
          .map((e) => DataValue.fromJson(e))
          .toList(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'process_status': processStatus,
      'process_message': processMessage,
      'count_rows': countRows,
      'count_columns': countColumns,
      'date_time': dateTime,
      'datavalue': dataValue?.map((e) => e.toJson()).toList(),
    };
  }

  // get all SIS summary details
  static Future<SisSummaryDetails> getSisSummaryDetails() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return SisSummaryDetails(
        processStatus: 'Failed',
        processMessage: 'No Internet Connection',
        countRows: '0',
        countColumns: '0',
        dateTime: '',
        dataValue: [],
      );
    }
    final user = await UserLoginClass.getLocalUser();
    Uri url = Uri.parse(
        '${AppWebService.sisSummaryDetailsURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    print(
        '${AppWebService.sisSummaryDetailsURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=&incremental_download=no&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    http.Response response = await http.get(url);
    print('SIS Summary Details URL: $url');
    print('SIS Summary Details Status Code: ${response.statusCode}');
    print('SIS Summary Details Response: ${response.body}');
    // check if the response is successful
    if (response.statusCode == 200) {
      // return the response body
      return SisSummaryDetails.fromJson(jsonDecode(response.body));
    } else {
      return SisSummaryDetails(
        processStatus: 'Failed',
        processMessage: 'Failed to get SIS Summary Details',
        countRows: '0',
        countColumns: '0',
        dateTime: '',
        dataValue: [],
      );
    }
  }
}

class DataValue {
  String? empCode;
  String? name;
  String? monthYear;

  String? salesVolumeMT;
  String? salesVolumeTGT;
  String? salesVolumeACH;
  String? salesVolumeACHPercent;
  String? salesVolumeWGTPercent;
  String? salesVolumeScorePercent;

  String? monthlyUniqueVisit;
  String? monthlyUniqueVisitTGT;
  String? monthlyUniqueVisitACH;
  String? monthlyUniqueVisitACHPercent;
  String? monthlyUniqueVisitWGTPercent;
  String? monthlyUniqueVisitScorePercent;

  String? dealerAppointment;
  String? dealerAppointmentTGT;
  String? dealerAppointmentACH;
  String? dealerAppointmentACHPercent;
  String? dealerAppointmenWGTPercent;
  String? dealerAppointmentScorePercent;

  String? activeDealerCount;
  String? activeDealerCountTGT;
  String? activeDealerCountACH;
  String? activeDealerCountACHPercent;
  String? activeDealerCountWGTPercent;
  String? activeDealerCountScorePercent;

  String? paramiterFive;
  String? fiveTGT;
  String? fiveACH;
  String? fiveACHPercent;
  String? fiveWGTPercent;
  String? fiveScorePercent;

  String? paramiterSix;
  String? sixTGT;
  String? sixACH;
  String? sixACHPercent;
  String? sixWGTPercent;
  String? sixScorePercent;

  String? paramiterSeven;
  String? sevenTGT;
  String? sevenACH;
  String? sevenACHPercent;
  String? sevenWGTPercent;
  String? sevenSCOREPercent;

  String? earningScorePercent;
  String? penaltyPercent;
  String? finalScorePercent;
  String? otsi;
  String? sisEarningMonth;
  String? remarks;

  DataValue({
    this.empCode,
    this.name,
    this.monthYear,
    this.salesVolumeMT,
    this.salesVolumeTGT,
    this.salesVolumeACH,
    this.salesVolumeACHPercent,
    this.salesVolumeWGTPercent,
    this.salesVolumeScorePercent,
    this.monthlyUniqueVisit,
    this.monthlyUniqueVisitTGT,
    this.monthlyUniqueVisitACH,
    this.monthlyUniqueVisitACHPercent,
    this.monthlyUniqueVisitWGTPercent,
    this.monthlyUniqueVisitScorePercent,
    this.dealerAppointment,
    this.dealerAppointmentTGT,
    this.dealerAppointmentACH,
    this.dealerAppointmentACHPercent,
    this.dealerAppointmenWGTPercent,
    this.dealerAppointmentScorePercent,
    this.activeDealerCount,
    this.activeDealerCountTGT,
    this.activeDealerCountACH,
    this.activeDealerCountACHPercent,
    this.activeDealerCountWGTPercent,
    this.activeDealerCountScorePercent,
    this.paramiterFive,
    this.fiveTGT,
    this.fiveACH,
    this.fiveACHPercent,
    this.fiveWGTPercent,
    this.fiveScorePercent,
    this.paramiterSix,
    this.sixTGT,
    this.sixACH,
    this.sixACHPercent,
    this.sixWGTPercent,
    this.sixScorePercent,
    this.paramiterSeven,
    this.sevenTGT,
    this.sevenACH,
    this.sevenACHPercent,
    this.sevenWGTPercent,
    this.sevenSCOREPercent,
    this.earningScorePercent,
    this.penaltyPercent,
    this.finalScorePercent,
    this.otsi,
    this.sisEarningMonth,
    this.remarks,
  });

  factory DataValue.fromJson(Map<String, dynamic> json) {
    return DataValue(
      empCode: json['emp_code'],
      name: json['name'],
      monthYear: json['month_year'],
      salesVolumeMT: json['sales_volume_MT'],
      salesVolumeTGT: json['sales_volume_TGT'],
      salesVolumeACH: json['sales_volume_ACH'],
      salesVolumeACHPercent: json['sales_volume_ACH_percent'],
      salesVolumeWGTPercent: json['sales_volume_WGT_percent'],
      salesVolumeScorePercent: json['sales_volume_SCORE_percent'],
      monthlyUniqueVisit: json['monthly_unique_visit'],
      monthlyUniqueVisitTGT: json['monthly_unique_visit_TGT'],
      monthlyUniqueVisitACH: json['monthly_unique_visit_ACH'],
      monthlyUniqueVisitACHPercent: json['monthly_unique_visit_ACH_percent'],
      monthlyUniqueVisitWGTPercent: json['monthly_unique_visit_WGT_percent'],
      monthlyUniqueVisitScorePercent:
          json['monthly_unique_visit_SCORE_percent'],
      dealerAppointment: json['dealer_appointment'],
      dealerAppointmentTGT: json['dealer_appointment_TGT'],
      dealerAppointmentACH: json['dealer_appointment_ACH'],
      dealerAppointmentACHPercent: json['dealer_appointment_ACH_percent'],
      dealerAppointmenWGTPercent: json['dealer_appointment_WGT_percent'],
      dealerAppointmentScorePercent: json['dealer_appointment_SCORE_percent'],
      activeDealerCount: json['active_dealer_count'],
      activeDealerCountTGT: json['active_dealer_count_TGT'],
      activeDealerCountACH: json['active_dealer_count_ACH'],
      activeDealerCountACHPercent: json['active_dealer_count_ACH_percent'],
      activeDealerCountWGTPercent: json['active_dealer_count_WGT_percent'],
      activeDealerCountScorePercent: json['active_dealer_count_SCORE_percent'],
      paramiterFive: json['paramiter_five'],
      fiveTGT: json['five_TGT'],
      fiveACH: json['five_ACH'],
      fiveACHPercent: json['five_ACH_percent'],
      fiveWGTPercent: json['five_WGT_percent'],
      fiveScorePercent: json['five_SCORE_percent'],
      paramiterSix: json['paramiter_six'],
      sixTGT: json['six_TGT'],
      sixACH: json['six_ACH'],
      sixACHPercent: json['six_ACH_percent'],
      sixWGTPercent: json['six_WGT_percent'],
      sixScorePercent: json['six_SCORE_percent'],
      paramiterSeven: json['paramiter_seven'],
      sevenTGT: json['seven_TGT'],
      sevenACH: json['seven_ACH'],
      sevenACHPercent: json['seven_ACH_percent'],
      sevenWGTPercent: json['seven_WGT_percent'],
      sevenSCOREPercent: json['seven_SCORE_percent'],
      earningScorePercent: json['earning_score_percent'],
      penaltyPercent: json['penalty_percent'],
      finalScorePercent: json['final_score_percent'],
      otsi: json['OTSI'],
      sisEarningMonth: json['SIS_earning_month'] + '',
      remarks: json['remarks'],
    );
  }

  Map<String, String> toJson() {
    return {
      'emp_code': empCode.toString(),
      'name': name.toString(),
      'month_year': monthYear.toString(),
      'sales_volume_MT': salesVolumeMT.toString(),
      'sales_volume_TGT': salesVolumeTGT.toString(),
      'sales_volume_ACH': salesVolumeACH.toString(),
      'sales_volume_ACH_percent': salesVolumeACHPercent.toString(),
      'sales_volume_WGT_percent': salesVolumeWGTPercent.toString(),
      'sales_volume_SCORE_percent': salesVolumeScorePercent.toString(),
      'monthly_unique_visit': monthlyUniqueVisit.toString(),
      'monthly_unique_visit_TGT': monthlyUniqueVisitTGT.toString(),
      'monthly_unique_visit_ACH': monthlyUniqueVisitACH.toString(),
      'monthly_unique_visit_ACH_percent':
          monthlyUniqueVisitACHPercent.toString(),
      'monthly_unique_visit_WGT_percent':
          monthlyUniqueVisitWGTPercent.toString(),
      'monthly_unique_visit_SCORE_percent':
          monthlyUniqueVisitScorePercent.toString(),
      'dealer_appointment': dealerAppointment.toString(),
      'dealer_appointment_TGT': dealerAppointmentTGT.toString(),
      'dealer_appointment_ACH': dealerAppointmentACH.toString(),
      'dealer_appointment_ACH_percent': dealerAppointmentACHPercent.toString(),
      'dealer_appointment_WGT_percent': dealerAppointmenWGTPercent.toString(),
      'dealer_appointment_SCORE_percent':
          dealerAppointmentScorePercent.toString(),
      'active_dealer_count': activeDealerCount.toString(),
      'active_dealer_count_TGT': activeDealerCountTGT.toString(),
      'active_dealer_count_ACH': activeDealerCountACH.toString(),
      'active_dealer_count_ACH_percent': activeDealerCountACHPercent.toString(),
      'active_dealer_count_WGT_percent': activeDealerCountWGTPercent.toString(),
      'active_dealer_count_SCORE_percent':
          activeDealerCountScorePercent.toString(),
      'paramiter_five': paramiterFive.toString(),
      'five_TGT': fiveTGT.toString(),
      'five_ACH': fiveACH.toString(),
      'five_ACH_percent': fiveACHPercent.toString(),
      'five_WGT_percent': fiveWGTPercent.toString(),
      'five_SCORE_percent': fiveScorePercent.toString(),
      'paramiter_six': paramiterSix.toString(),
      'six_TGT': sixTGT.toString(),
      'six_ACH': sixACH.toString(),
      'six_ACH_percent': sixACHPercent.toString(),
      'six_WGT_percent': sixWGTPercent.toString(),
      'six_SCORE_percent': sixScorePercent.toString(),
      'paramiter_seven': paramiterSeven.toString(),
      'seven_TGT': sevenTGT.toString(),
      'seven_ACH': sevenACH.toString(),
      'seven_ACH_percent': sevenACHPercent.toString(),
      'seven_WGT_percent': sevenWGTPercent.toString(),
      'seven_SCORE_percent': sevenSCOREPercent.toString(),
      'earning_score_percent': earningScorePercent.toString(),
      'penalty_percent': penaltyPercent.toString(),
      'final_score_percent': finalScorePercent.toString(),
      'OTSI': otsi.toString(),
      'SIS_earning_month': sisEarningMonth.toString(),
      'remarks': remarks.toString(),
    };
  }
}
