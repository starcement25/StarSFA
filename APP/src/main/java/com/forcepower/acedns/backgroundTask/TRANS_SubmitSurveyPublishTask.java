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
import com.forcepower.acedns.bean.SurveyPublish;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitSurveyPublishTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog mProgressDialog;
    String str_status = "";

    public TRANS_SubmitSurveyPublishTask(Context context, boolean finish, String getStatusOBJ) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.str_status = getStatusOBJ;
        this.finish = finish;
    }

    public TRANS_SubmitSurveyPublishTask(Context context, String getStatusOBJ) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.str_status = getStatusOBJ;

    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        if (str_status.equals("DCA")) {
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Uploading data.Please wait..");
            mProgressDialog.setCancelable(false);
            mProgressDialog.show();
        }

    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitSurveyPublishURL + "?nick_name="
                        + Constants.nickName + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyPublishTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyPublishTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyPublishTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (str_status.equals("DCA")) {
            mProgressDialog.cancel();
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.UpadateSurveyPublishLocation();
                dataHelperObj.UPDATEDCATransaction();
                dataHelperObj.closeDatabase();
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
                if (Constants.isSurveyImageTake) {
                    new TRANS_UploadImages(mContext, "SURVEY_DCA", result, finish).execute();
                } else {
                    Toast.makeText(mContext, "Data submitted successfully", Toast.LENGTH_SHORT).show();
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
                Constants.isSurveyImageTake = false;
            } else {
                dataHelperObj.closeDatabase();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
            Constants.LIPLMOBILENO = "";
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.UpadateSurveyPublishLocation();
                dataHelperObj.UPDATEDCATransaction();
                dataHelperObj.closeDatabase();
            }
        }


    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetSurveyPublishLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>"
                    + "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>"
                    + "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>"
                    + "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>"
                    + "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>"
                    + "<date><![CDATA[" + currentLocation.getDate() + "]]></date>"
                    + "</location>";

            if (currentLocation.getTransId().startsWith("SUA")) {

                xmlData += "<surveyaudit>";
                xmlData += location;
                xmlData += "<surveyauditdata>";

                ArrayList<SurveyPublish> unUploadedSurveyDetails = dataHelperObj.GETSurveyPublishDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedSurveyDetails.size(); jj++) {
                    SurveyPublish mSaudaDetails = unUploadedSurveyDetails.get(jj);
                    xmlData += "<survey_audit_details>"
                            + "<survey_audit_id><![CDATA[" + mSaudaDetails.getDcaTransId() + "]]></survey_audit_id>"
                            + "<survey_id><![CDATA[" + mSaudaDetails.getSurveyId() + "]]></survey_id>"
                            + "<action_id><![CDATA[" + mSaudaDetails.getActionId() + "]]></action_id>"
                            + "<value><![CDATA[" + mSaudaDetails.getValue() + "]]></value>"
                            + "<status><![CDATA[" + mSaudaDetails.getStatus() + "]]></status>"
                            + "<type><![CDATA[" + mSaudaDetails.getType() + "]]></type>"
                            + "<row_id><![CDATA[" + mSaudaDetails.getRowId() + "]]></row_id>"
                            + "</survey_audit_details>";
                }
                xmlData += "</surveyauditdata></surveyaudit>";
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
}
