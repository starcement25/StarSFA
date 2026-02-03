import 'dart:convert';
import 'dart:developer';

import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';
import 'package:http/http.dart' as http;

class SisSummaryDetailsBD {
  String? processStatus;
  String? processMessage;
  int? countrows;
  String? countcolumns;
  String? datetime;
  List<DatavalueBD>? datavalue;

  SisSummaryDetailsBD(
      {this.processStatus,
      this.processMessage,
      this.countrows,
      this.countcolumns,
      this.datetime,
      this.datavalue});

  SisSummaryDetailsBD.fromJson(Map<String, dynamic> json) {
    processStatus = json['process_status'];
    processMessage = json['process_message'];
    countrows = json['countrows'];
    countcolumns = json['countcolumns'];
    datetime = json['datetime'];
    if (json['datavalue'] != null) {
      datavalue = <DatavalueBD>[];
      json['datavalue'].forEach((v) {
        datavalue!.add(DatavalueBD.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data['process_status'] = processStatus;
    data['process_message'] = processMessage;
    data['countrows'] = countrows;
    data['countcolumns'] = countcolumns;
    data['datetime'] = datetime;
    if (datavalue != null) {
      data['datavalue'] = datavalue!.map((v) => v.toJson()).toList();
    }
    return data;
  }

  static Future<SisSummaryDetailsBD> getSisSummaryDetailsBD() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    final localUser = await UserLoginClass.getLocalUser();
    final url = Uri.parse(
        "${AppWebService.sisSummaryDetailsBDURL}?nick_name=${AppWebService.nickname}&emp_code=${localUser?.empCode}");
    log('SisSummaryDetailsBD: $url');
    final response = await http.get(url);
    if (response.statusCode == 200) {
      log('SisSummaryDetailsBD: ${response.body}');
      return SisSummaryDetailsBD.fromJson(jsonDecode(response.body));
    } else {
      throw Exception(
          'Failed to load SisSummaryDetailsBD ${response.statusCode}');
    }
  }

  static Future<SisSummaryDetailsBD> getSisSummaryHeadersBD() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      throw Exception('No internet connection');
    }
    final localUser = await UserLoginClass.getLocalUser();
    final url = Uri.parse(
        "${AppWebService.sisSummaryHeaderBDURL}?nick_name=${AppWebService.nickname}&emp_code=${localUser?.empCode}");
    log('SisSummaryDetailsBD: $url');
    final response = await http.get(url);
    if (response.statusCode == 200) {
      log('SisSummaryDetailsBD: ${response.body}');
      return SisSummaryDetailsBD.fromJson(jsonDecode(response.body));
    } else {
      throw Exception(
          'Failed to load SisSummaryDetailsBD ${response.statusCode}');
    }
  }
}

class DatavalueBD {
  String? empCode;
  String? name;
  String? monthYear;
  String? parameter1;
  String? salesVolumeTGT;
  String? salesVolumeACTUAL;
  String? sisSlabPercent;
  String? premiumSalesConversionTarget;
  String? salesVolumeSCOREPercent;
  String? parameter2;
  String? monthlyUniqueVisitTGT;
  String? monthlyUniqueVisitACH;
  String? activitiesSisSlabPercent;
  String? activities;
  String? monthlyUniqueVisitSCOREPercent;
  String? parameter3;
  String? dealerAppointmentACTUAL;
  String? dealerAppointmentSisSlabPercent;
  String? influencerRegistration;
  String? dealerAppointmentSCOREPercent;
  String? parameter4;
  String? activeDealerCountTGT;
  String? activeDealerCountACH;
  String? activeDealerCountSisSlabPercent;
  String? activeDealerGrowth;
  String? activeDealerCountSCOREPercent;
  String? paramiterFive;
  String? fiveTGT;
  String? fiveACH;
  String? fiveSisSlabPercent;
  String? activeInfluencerGrowth;
  String? fiveSCOREPercent;
  String? paramiterSix;
  String? sixACTUAL;
  String? sixSlabPercent;
  String? sixSCOREPercent;
  String? earningScorePercent;
  String? penaltyPercent;
  String? finalScorePercent;
  String? remarks;
  String? headerId;

  DatavalueBD(
      {this.empCode,
      this.name,
      this.monthYear,
      this.parameter1,
      this.salesVolumeTGT,
      this.salesVolumeACTUAL,
      this.sisSlabPercent,
      this.premiumSalesConversionTarget,
      this.salesVolumeSCOREPercent,
      this.parameter2,
      this.monthlyUniqueVisitTGT,
      this.monthlyUniqueVisitACH,
      this.activitiesSisSlabPercent,
      this.activities,
      this.monthlyUniqueVisitSCOREPercent,
      this.parameter3,
      this.dealerAppointmentACTUAL,
      this.dealerAppointmentSisSlabPercent,
      this.influencerRegistration,
      this.dealerAppointmentSCOREPercent,
      this.parameter4,
      this.activeDealerCountTGT,
      this.activeDealerCountACH,
      this.activeDealerCountSisSlabPercent,
      this.activeDealerGrowth,
      this.activeDealerCountSCOREPercent,
      this.paramiterFive,
      this.fiveTGT,
      this.fiveACH,
      this.fiveSisSlabPercent,
      this.activeInfluencerGrowth,
      this.fiveSCOREPercent,
      this.paramiterSix,
      this.sixACTUAL,
      this.sixSlabPercent,
      this.sixSCOREPercent,
      this.earningScorePercent,
      this.penaltyPercent,
      this.finalScorePercent,
      this.remarks,
      this.headerId});

  DatavalueBD.fromJson(Map<String, dynamic> json) {
    empCode = json['emp_code'];
    name = json['name'];
    monthYear = json['month_year'];
    parameter1 = json['parameter_1'];
    salesVolumeTGT = json['sales_volume_TGT'];
    salesVolumeACTUAL = json['sales_volume_ACTUAL'];
    sisSlabPercent = json['sis_slab_percent'];
    premiumSalesConversionTarget = json['premium_sales_conversion_target'];
    salesVolumeSCOREPercent = json['sales_volume_SCORE_percent'];
    parameter2 = json['parameter_2'];
    monthlyUniqueVisitTGT = json['monthly_unique_visit_TGT'];
    monthlyUniqueVisitACH = json['monthly_unique_visit_ACH'];
    activitiesSisSlabPercent = json['activities_sis_slab_percent'];
    activities = json['activities'];
    monthlyUniqueVisitSCOREPercent = json['monthly_unique_visit_SCORE_percent'];
    parameter3 = json['parameter_3'];
    dealerAppointmentACTUAL = json['dealer_appointment_ACTUAL'];
    dealerAppointmentSisSlabPercent =
        json['dealer_appointment_sis_slab_percent'];
    influencerRegistration = json['influencer_registration'];
    dealerAppointmentSCOREPercent = json['dealer_appointment_SCORE_percent'];
    parameter4 = json['parameter_4'];
    activeDealerCountTGT = json['active_dealer_count_TGT'];
    activeDealerCountACH = json['active_dealer_count_ACH'];
    activeDealerCountSisSlabPercent =
        json['active_dealer_count_sis_slab_percent'];
    activeDealerGrowth = json['active_dealer_growth'];
    activeDealerCountSCOREPercent = json['active_dealer_count_SCORE_percent'];
    paramiterFive = json['paramiter_five'];
    fiveTGT = json['five_TGT'];
    fiveACH = json['five_ACH'];
    fiveSisSlabPercent = json['five_sis_slab_percent'];
    activeInfluencerGrowth = json['active_influencer_growth'];
    fiveSCOREPercent = json['five_SCORE_percent'];
    paramiterSix = json['paramiter_six'];
    sixACTUAL = json['six_ACTUAL'];
    sixSlabPercent = json['six_slab_percent'];
    sixSCOREPercent = json['six_SCORE_percent'];
    earningScorePercent = json['earning_score_percent'];
    penaltyPercent = json['penalty_percent'];
    finalScorePercent = json['final_score_percent'];
    remarks = json['remarks'];
    headerId = json['header_id'];
  }

  Map<String, String> toJson() {
    final Map<String, String> data = <String, String>{};
    data['emp_code'] = empCode.toString();
    data['name'] = name.toString();
    data['month_year'] = monthYear.toString();
    data['parameter_1'] = parameter1.toString();
    data['sales_volume_TGT'] = salesVolumeTGT.toString();
    data['sales_volume_ACTUAL'] = salesVolumeACTUAL.toString();
    data['sis_slab_percent'] = sisSlabPercent.toString();
    data['premium_sales_conversion_target'] =
        premiumSalesConversionTarget.toString();
    data['sales_volume_SCORE_percent'] = salesVolumeSCOREPercent.toString();
    data['parameter_2'] = parameter2.toString();
    data['monthly_unique_visit_TGT'] = monthlyUniqueVisitTGT.toString();
    data['monthly_unique_visit_ACH'] = monthlyUniqueVisitACH.toString();
    data['activities_sis_slab_percent'] = activitiesSisSlabPercent.toString();
    data['activities'] = activities.toString();
    data['monthly_unique_visit_SCORE_percent'] =
        monthlyUniqueVisitSCOREPercent.toString();
    data['parameter_3'] = parameter3.toString();
    data['dealer_appointment_ACTUAL'] = dealerAppointmentACTUAL.toString();
    data['dealer_appointment_sis_slab_percent'] =
        dealerAppointmentSisSlabPercent.toString();
    data['influencer_registration'] = influencerRegistration.toString();
    data['dealer_appointment_SCORE_percent'] =
        dealerAppointmentSCOREPercent.toString();
    data['parameter_4'] = parameter4.toString();
    data['active_dealer_count_TGT'] = activeDealerCountTGT.toString();
    data['active_dealer_count_ACH'] = activeDealerCountACH.toString();
    data['active_dealer_count_sis_slab_percent'] =
        activeDealerCountSisSlabPercent.toString();
    data['active_dealer_growth'] = activeDealerGrowth.toString();
    data['active_dealer_count_SCORE_percent'] =
        activeDealerCountSCOREPercent.toString();
    data['paramiter_five'] = paramiterFive.toString();
    data['five_TGT'] = fiveTGT.toString();
    data['five_ACH'] = fiveACH.toString();
    data['five_sis_slab_percent'] = fiveSisSlabPercent.toString();
    data['active_influencer_growth'] = activeInfluencerGrowth.toString();
    data['five_SCORE_percent'] = fiveSCOREPercent.toString();
    data['paramiter_six'] = paramiterSix.toString();
    data['six_ACTUAL'] = sixACTUAL.toString();
    data['six_slab_percent'] = sixSlabPercent.toString();
    data['six_SCORE_percent'] = sixSCOREPercent.toString();
    data['earning_score_percent'] = earningScorePercent.toString();
    data['penalty_percent'] = penaltyPercent.toString();
    data['final_score_percent'] = finalScorePercent.toString();
    data['remarks'] = remarks.toString();
    data['header_id'] = headerId.toString();
    return data;
  }
}
