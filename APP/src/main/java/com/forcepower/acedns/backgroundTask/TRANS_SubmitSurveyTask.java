package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.bean.SurveyDetails;

import java.util.ArrayList;

public class TRANS_SubmitSurveyTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog mProgressDialog;
    String str_getStatus = "";

    public TRANS_SubmitSurveyTask(Context context, boolean finish, String get_status) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.finish = finish;
        this.str_getStatus = get_status;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        if (str_getStatus.equals("SUBMIT")) {
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Uploading data.\nPlease wait..");
            mProgressDialog.setCancelable(false);
            mProgressDialog.show();
        }

    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
//                prepareXMLData1();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitSurveyURL + "?nick_name="
                        + Constants.nickName + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyTask: " +xmlData);
                prepareXMLData1();
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
//        mProgressDialog.cancel();
        if (str_getStatus.equals("SUBMIT")) {
            mProgressDialog.cancel();
            Constants.isDCAtoDCE = false;
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.UpadateSurveyLocation();
                dataHelperObj.UPDATESurveyOutPUT();
                dataHelperObj.closeDatabase();
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
                new TRANS_SurveyImageTask(mContext, "SURVEY", result, finish).execute();

                if(Constants.nickName.toUpperCase().matches("NIMBUS")){
                    Utils.showColorToast(mContext,"Submitted successfully");
                }
            } else {
                dataHelperObj.closeDatabase();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                            | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
            Constants.LIPLMOBILENO = "";
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.UpadateSurveyLocation();
                dataHelperObj.UPDATESurveyOutPUT();
                dataHelperObj.closeDatabase();
            }
        }

    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetSurveyLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);

//            Log.d("TAG", "_DOWNLOAD_ prepareXMLData: "+ currentLocation.getLatitude());
//            Log.d("TAG", "_DOWNLOAD_ prepareXMLData: "+ currentLocation.getLongitude());

            String location = "<location>"
                    + "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>"
                    + "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>"
                    + "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>"
                    + "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>"
                    + "<date><![CDATA[" + currentLocation.getDate() + "]]></date>"
                    + "</location>";

            if (currentLocation.getTransId().startsWith("SU") || currentLocation.getTransId().startsWith("NSU")) {

                xmlData += "<survey>";
                xmlData += location;
                xmlData += "<surveydata>";
                ///
                SurveyDetails obj = dataHelperObj.GetSurveyHeader(currentLocation.getTransId());
                String surveyheader = "";
                surveyheader = "<survey_header>"
                        + "<survey_type><![CDATA[" + obj.getType() + "]]></survey_type>"
                        + "<menu_name><![CDATA[" + obj.getMenuName() + "]]></menu_name>"
                        + "<mall_id><![CDATA[" + obj.getMallID() + "]]></mall_id>"
                        + "<mall_name><![CDATA[" + obj.getMallName() + "]]></mall_name>"
                        + "<business_name><![CDATA[" + obj.getBusinessName() + "]]></business_name>"
                        + "<contact_name><![CDATA[" + obj.getContactName() + "]]></contact_name>"
                        + "<phone_no><![CDATA[" + obj.getPhoneNo() + "]]></phone_no>"
                        + "<questions_answered><![CDATA[" + obj.getQuestion() + "]]></questions_answered>"
                        + "<route_code><![CDATA[" + obj.getRouteCode() + "]]></route_code>"
                        + "<check_in_time><![CDATA[" + obj.getCheck_in_time() + "]]></check_in_time>"
                        + "<survey_id><![CDATA[" + obj.getSurveyID() + "]]></survey_id>"
                        + "</survey_header>";
                xmlData += surveyheader;
                ///
                ArrayList<SurveyDetails> unUploadedSurveyDetails = dataHelperObj.GETSurveyDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedSurveyDetails.size(); jj++) {
                    SurveyDetails mSaudaDetails = unUploadedSurveyDetails.get(jj);
                    xmlData += "<survey_details>"
                            + "<survey_id><![CDATA[" + mSaudaDetails.getSurveyID() + "]]></survey_id>"
                            + "<action_id><![CDATA[" + mSaudaDetails.getActionId() + "]]></action_id>"
                            + "<value><![CDATA[" + mSaudaDetails.getValue().trim() + "]]></value>"
                            + "<type><![CDATA[" + mSaudaDetails.getType() + "]]></type>"
                            + "<row_id><![CDATA[" + mSaudaDetails.getRowId() + "]]></row_id>"
                            + "</survey_details>";
                }
                xmlData += "</surveydata></survey>";
            } else if (currentLocation.getTransId().trim().startsWith("A")) {
                xmlData += "<attendance>";
                xmlData += location;
                ArrayList<Attendance> unUploadedAttendance = dataHelperObj.getUnuploadedAttendance(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedAttendance.size(); jj++) {
                    Attendance detailsObj = unUploadedAttendance.get(jj);
                    xmlData += "<attendancedata>"
                            + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                            + "<date><![CDATA[" + detailsObj.getDate() + "]]></date>" +
                            "</attendancedata>";
                }
                xmlData += "</attendance>";
            }
        }
        xmlData += "</root>";
        return xmlData;
    }

    public void prepareXMLData1() {
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetSurveyLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);

            if (currentLocation.getTransId().startsWith("SU") || currentLocation.getTransId().startsWith("NSU")) {
                ArrayList<SurveyDetails> unUploadedSurveyDetails = dataHelperObj.GETSurveyDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedSurveyDetails.size(); jj++) {
                    SurveyDetails mSaudaDetails = unUploadedSurveyDetails.get(jj);
                    String a= "\n<survey_details>"
                            + "<survey_id><![CDATA[" + mSaudaDetails.getSurveyID() + "]]></survey_id>"
                            + "<action_id><![CDATA[" + mSaudaDetails.getActionId() + "]]></action_id>"
                            + "<value><![CDATA[" + mSaudaDetails.getValue().trim() + "]]></value>"
                            + "<type><![CDATA[" + mSaudaDetails.getType() + "]]></type>"
                            + "<row_id><![CDATA[" + mSaudaDetails.getRowId() + "]]></row_id>"
                            + "</survey_details>\n";
                    Log.d("TAG", "TRANS_SubmitSurveyTask: "+a);
                }
            }
        }
    }
}
