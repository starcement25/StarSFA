import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:starsfa/models/app_web_service.dart';
import 'package:starsfa/models/local_db.dart';
import 'package:starsfa/models/network_service.dart';
import 'package:starsfa/models/user_login_class.dart';

class ComplaintReport {
  List<List<String>>? complaintDataClassData;

  ComplaintReport({this.complaintDataClassData});

  factory ComplaintReport.fromTXT(String txt) {
    // encode txt to utf8
    txt = utf8.decode(txt.runes.toList());
    print(txt);
    final List<String> lines = txt.split('\n');
    final int totalRecords = int.parse(lines[0].split('¥')[0]);
    // final int totalColumns = int.parse(lines[0].split('¥')[1]);
    final List<String> data = [];
    for (int i = 2; i < totalRecords + 2; i++) {
      data.add(lines[i]);
    }
    final List<List<String>> complaintDataClassData = <List<String>>[];
    for (int i = 0; i < data.length; i++) {
      final List<String> temp = data[i].split('^');
      complaintDataClassData.add(temp);
    }
    return ComplaintReport(complaintDataClassData: complaintDataClassData);
  }

  static Future<bool> getComplaintReportData() async {
    bool isConnected = await NetworkService.checkConnectionAll();
    if (!isConnected) {
      return false;
    }
    final user = await UserLoginClass.getLocalUser();
    String incrementalDownload = await UserLoginClass.getincrementalDownload();
    incrementalDownload = 'no';
    String lastUpdateTime = await UserLoginClass.lastUpdateTime();
    lastUpdateTime = lastUpdateTime.replaceAll(' ', '?');
    Uri url = Uri.parse(
        '${AppWebService.complaintReportURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}&last_update_time=$lastUpdateTime&incremental_download=$incrementalDownload&data_download_time=1971-01-01?10:10:10');
    // get response from the server
    Uri.parse(
        '${AppWebService.complaintReportURL}?nick_name=${AppWebService.nickname}&emp_code=${user?.empCode}');
    // get response from the server
    http.Response response = await http.get(url);

    // check if the response is successful
    if (response.statusCode == 200) {
      // check if the response is formatted correctly
      if (response.body.contains('¥')) {
        // return the response body
        final ComplaintReport mComplaintDataClass =
            ComplaintReport.fromTXT(response.body);
        final localDB = await LocalDB.openMyDatabase();
        final List<Complaint> mComplaintDataDBDB = [];
        for (int i = 0;
            i < mComplaintDataClass.complaintDataClassData!.length;
            i++) {
          final Complaint mDashboardDataClassLocal = Complaint(
            complaintId: mComplaintDataClass.complaintDataClassData?[i][0],
            empCode: mComplaintDataClass.complaintDataClassData?[i][1],
            complaintSegment: mComplaintDataClass.complaintDataClassData?[i][2],
            complaintCategory: mComplaintDataClass.complaintDataClassData?[i]
                [3],
            complaintReceiver: mComplaintDataClass.complaintDataClassData?[i]
                [4],
            dateOfFirstVisitToCustomer:
                mComplaintDataClass.complaintDataClassData?[i][5],
            firstVisitMadeSalesTeamName:
                mComplaintDataClass.complaintDataClassData?[i][6],
            firSubmitted: mComplaintDataClass.complaintDataClassData?[i][7],
            firstVisitMadeByTeTm: mComplaintDataClass.complaintDataClassData?[i]
                [8],
            customerName: mComplaintDataClass.complaintDataClassData?[i][9],
            customerContactNo: mComplaintDataClass.complaintDataClassData?[i]
                [10],
            customerAddressPin: mComplaintDataClass.complaintDataClassData?[i]
                [11],
            typeOfComplaint: mComplaintDataClass.complaintDataClassData?[i][12],
            remarksOthers: mComplaintDataClass.complaintDataClassData?[i][13],
            natureOfComplaint: mComplaintDataClass.complaintDataClassData?[i]
                [14],
            complaintEffortsDetails:
                mComplaintDataClass.complaintDataClassData?[i][15],
            typeOfCement: mComplaintDataClass.complaintDataClassData?[i][16],
            nameOfThePlant: mComplaintDataClass.complaintDataClassData?[i][17],
            batchNo: mComplaintDataClass.complaintDataClassData?[i][18],
            dateOfSupply: mComplaintDataClass.complaintDataClassData?[i][19],
            noOfBagsPurchased: mComplaintDataClass.complaintDataClassData?[i]
                [20],
            dateOfUsage: mComplaintDataClass.complaintDataClassData?[i][21],
            suppliedBy: mComplaintDataClass.complaintDataClassData?[i][22],
            currentStatusOfSite: mComplaintDataClass.complaintDataClassData?[i]
                [23],
            storageConditionOfCement:
                mComplaintDataClass.complaintDataClassData?[i][24],
            weightOfCementBags: mComplaintDataClass.complaintDataClassData?[i]
                [25],
            qualityCoarseAggregates:
                mComplaintDataClass.complaintDataClassData?[i][26],
            qualityFineAggregates:
                mComplaintDataClass.complaintDataClassData?[i][27],
            qualityOfWater: mComplaintDataClass.complaintDataClassData?[i][28],
            qualityOfAdmixture: mComplaintDataClass.complaintDataClassData?[i]
                [29],
            degreeQualityControl: mComplaintDataClass.complaintDataClassData?[i]
                [30],
            investigationObservations:
                mComplaintDataClass.complaintDataClassData?[i][31],
            rootCauseAnalysis: mComplaintDataClass.complaintDataClassData?[i]
                [32],
            correctionsSuggestedTechnicalTeam:
                mComplaintDataClass.complaintDataClassData?[i][33],
            customerIsConvinced: mComplaintDataClass.complaintDataClassData?[i]
                [34],
            starCementReused: mComplaintDataClass.complaintDataClassData?[i]
                [35],
            actionPlanNotConvinced:
                mComplaintDataClass.complaintDataClassData?[i][36],
            followUpPlan: mComplaintDataClass.complaintDataClassData?[i][37],
            managersRecommendation:
                mComplaintDataClass.complaintDataClassData?[i][38],
            complaintStatus: mComplaintDataClass.complaintDataClassData?[i][39],
            expectedDateOfClosing:
                mComplaintDataClass.complaintDataClassData?[i][40],
            closedDate: mComplaintDataClass.complaintDataClassData?[i][41],
            remarks: mComplaintDataClass.complaintDataClassData?[i][42],
            uploadedImage: mComplaintDataClass.complaintDataClassData?[i][43],
            firImage: mComplaintDataClass.complaintDataClassData?[i][43],
            ccrImage: mComplaintDataClass.complaintDataClassData?[i][44],
            complaintImage: mComplaintDataClass.complaintDataClassData?[i][45],
            bill2Image: mComplaintDataClass.complaintDataClassData?[i][46],
            branch: mComplaintDataClass.complaintDataClassData?[i][47],
            district: mComplaintDataClass.complaintDataClassData?[i][48],
          );
          mComplaintDataDBDB.add(mDashboardDataClassLocal);
        }
        final batch = localDB.batch();
        await localDB.delete('complaint_master');
        for (int i = 0; i < mComplaintDataDBDB.length; i++) {
          batch.insert('complaint_master', mComplaintDataDBDB[i].toJson());
          //print("mis_details_emp_json- $mDashboardDataDBDB[i].toJson()");
        }
        await batch.commit(noResult: true);
        //return true;
        final List<Map<String, dynamic>> records1 =
            await localDB.rawQuery('select emp_code from  complaint_master');
        print("$records1");

        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
}

class Complaint {
  String? complaintId;
  String? empCode;
  String? complaintSegment;
  String? complaintCategory;
  String? complaintReceiver;
  String? dateOfFirstVisitToCustomer;
  String? firstVisitMadeSalesTeamName;
  String? firSubmitted;
  String? firstVisitMadeByTeTm;
  String? customerName;
  String? customerContactNo;
  String? customerAddressPin;
  String? typeOfComplaint;
  String? remarksOthers;
  String? natureOfComplaint;
  String? complaintEffortsDetails;
  String? typeOfCement;
  String? nameOfThePlant;
  String? batchNo;
  String? dateOfSupply;
  String? noOfBagsPurchased;
  String? dateOfUsage;
  String? suppliedBy;
  String? currentStatusOfSite;
  String? storageConditionOfCement;
  String? weightOfCementBags;
  String? qualityCoarseAggregates;
  String? qualityFineAggregates;
  String? qualityOfWater;
  String? qualityOfAdmixture;
  String? degreeQualityControl;
  String? investigationObservations;
  String? rootCauseAnalysis;
  String? correctionsSuggestedTechnicalTeam;
  String? customerIsConvinced;
  String? starCementReused;
  String? actionPlanNotConvinced;
  String? followUpPlan;
  String? managersRecommendation;
  String? complaintStatus;
  String? expectedDateOfClosing;
  String? closedDate;
  String? remarks;
  String? uploadedImage;
  String? firImage;
  String? ccrImage;
  String? complaintImage;
  String? bill2Image;
  String? branch;
  String? district;

  Complaint({
    required this.complaintId,
    required this.empCode,
    required this.complaintSegment,
    required this.complaintCategory,
    required this.complaintReceiver,
    required this.dateOfFirstVisitToCustomer,
    required this.firstVisitMadeSalesTeamName,
    required this.firSubmitted,
    required this.firstVisitMadeByTeTm,
    required this.customerName,
    required this.customerContactNo,
    required this.customerAddressPin,
    required this.typeOfComplaint,
    required this.remarksOthers,
    required this.natureOfComplaint,
    required this.complaintEffortsDetails,
    required this.typeOfCement,
    required this.nameOfThePlant,
    required this.batchNo,
    required this.dateOfSupply,
    required this.noOfBagsPurchased,
    required this.dateOfUsage,
    required this.suppliedBy,
    required this.currentStatusOfSite,
    required this.storageConditionOfCement,
    required this.weightOfCementBags,
    required this.qualityCoarseAggregates,
    required this.qualityFineAggregates,
    required this.qualityOfWater,
    required this.qualityOfAdmixture,
    required this.degreeQualityControl,
    required this.investigationObservations,
    required this.rootCauseAnalysis,
    required this.correctionsSuggestedTechnicalTeam,
    required this.customerIsConvinced,
    required this.starCementReused,
    required this.actionPlanNotConvinced,
    required this.followUpPlan,
    required this.managersRecommendation,
    required this.complaintStatus,
    required this.expectedDateOfClosing,
    required this.closedDate,
    required this.remarks,
    required this.uploadedImage,
    required this.firImage,
    required this.ccrImage,
    required this.complaintImage,
    required this.bill2Image,
    required this.branch,
    required this.district,
  });

  Map<String, dynamic> toJson() {
    return {
      'complaint_id': complaintId,
      'emp_code': empCode,
      'complaint_segment': complaintSegment,
      'complaint_category': complaintCategory,
      'complaint_receiver': complaintReceiver,
      'date_of_first_visit_to_customer': dateOfFirstVisitToCustomer,
      'first_visit_made_sales_team_name': firstVisitMadeSalesTeamName,
      'FIR_submitted': firSubmitted,
      'first_visit_made_by_TE_TM': firstVisitMadeByTeTm,
      'customer_name': customerName,
      'customer_contact_no': customerContactNo,
      'customer_address_pin': customerAddressPin,
      'type_of_complaint': typeOfComplaint,
      'remarks_others': remarksOthers,
      'nature_of_complaint': natureOfComplaint,
      'complaint_efforts_details': complaintEffortsDetails,
      'type_of_cement': typeOfCement,
      'name_of_the_plant': nameOfThePlant,
      'batch_no': batchNo,
      'date_of_supply': dateOfSupply,
      'no_of_bags_purchased': noOfBagsPurchased,
      'date_of_usage': dateOfUsage,
      'supplied_by': suppliedBy,
      'current_status_of_site': currentStatusOfSite,
      'storage_condition_of_cement': storageConditionOfCement,
      'weight_of_cement_bags': weightOfCementBags,
      'quality_coarse_aggregates': qualityCoarseAggregates,
      'quality_fine_aggregates': qualityFineAggregates,
      'quality_of_water': qualityOfWater,
      'quality_of_admixture': qualityOfAdmixture,
      'degree_quality_control': degreeQualityControl,
      'investigation_observations': investigationObservations,
      'root_cause_analysis': rootCauseAnalysis,
      'corrections_suggested_technical_team': correctionsSuggestedTechnicalTeam,
      'customer_is_convinced': customerIsConvinced,
      'star_cement_reused': starCementReused,
      'action_plan_not_convinced': actionPlanNotConvinced,
      'follow_up_plan': followUpPlan,
      'managers_recommendation': managersRecommendation,
      'complaint_status': complaintStatus,
      'expected_date_of_closing': expectedDateOfClosing,
      'closed_date': closedDate,
      'remarks': remarks,
      'FIR_image': firImage,
      'CCR_image': ccrImage,
      'complaint_image': complaintImage,
      'Bill2_image': bill2Image,
      'branch': branch,
      'district': district,
    };
  }

  // Convert a Complaint into a Map. The keys must correspond to the names of the columns in the database.
  Map<String, dynamic> toMap() {
    return {
      'complaintId': complaintId,
      'empCode': empCode,
      'complaintSegment': complaintSegment,
      'complaintCategory': complaintCategory,
      'complaintReceiver': complaintReceiver,
      'dateOfFirstVisitToCustomer': dateOfFirstVisitToCustomer,
      'firstVisitMadeSalesTeamName': firstVisitMadeSalesTeamName,
      'firSubmitted': firSubmitted,
      'firstVisitMadeByTeTm': firstVisitMadeByTeTm,
      'customerName': customerName,
      'customerContactNo': customerContactNo,
      'customerAddressPin': customerAddressPin,
      'typeOfComplaint': typeOfComplaint,
      'remarksOthers': remarksOthers,
      'natureOfComplaint': natureOfComplaint,
      'complaintEffortsDetails': complaintEffortsDetails,
      'typeOfCement': typeOfCement,
      'nameOfThePlant': nameOfThePlant,
      'batchNo': batchNo,
      'dateOfSupply': dateOfSupply,
      'noOfBagsPurchased': noOfBagsPurchased,
      'dateOfUsage': dateOfUsage,
      'suppliedBy': suppliedBy,
      'currentStatusOfSite': currentStatusOfSite,
      'storageConditionOfCement': storageConditionOfCement,
      'weightOfCementBags': weightOfCementBags,
      'qualityCoarseAggregates': qualityCoarseAggregates,
      'qualityFineAggregates': qualityFineAggregates,
      'qualityOfWater': qualityOfWater,
      'qualityOfAdmixture': qualityOfAdmixture,
      'degreeQualityControl': degreeQualityControl,
      'investigationObservations': investigationObservations,
      'rootCauseAnalysis': rootCauseAnalysis,
      'correctionsSuggestedTechnicalTeam': correctionsSuggestedTechnicalTeam,
      'customerIsConvinced': customerIsConvinced,
      'starCementReused': starCementReused,
      'actionPlanNotConvinced': actionPlanNotConvinced,
      'followUpPlan': followUpPlan,
      'managersRecommendation': managersRecommendation,
      'complaintStatus': complaintStatus,
      'expectedDateOfClosing': expectedDateOfClosing,
      'closedDate': closedDate,
      'remarks': remarks,
      'uploadedImage': uploadedImage,
      'ccrImage': ccrImage,
      'complaintImage': complaintImage,
      'bill2Image': bill2Image,
      'branch': branch,
      'district': district,
    };
  }
}
