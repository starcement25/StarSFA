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
import com.forcepower.acedns.bean.SurveyDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitSurveyHeader extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog mProgressDialog;

    public TRANS_SubmitSurveyHeader(Context context) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Uploading data.\nPlease wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                xmlData = prepareXMLData();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitSurveyHeaderURL
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyHeader: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyHeader value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSurveyHeader result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        mProgressDialog.cancel();
        Constants.isDCAtoDCE = false;
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            mAceDnsTransactionDatabase.UpadateSurveyHeaderLocation();
            if (result.equalsIgnoreCase("2")) {
                Constants.dataResfresh = true;
            }
        } else {
            Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
        }
        mAceDnsTransactionDatabase.closeDatabase();
        Intent intent = new Intent(mContext, MenuActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        mContext.startActivity(intent);
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.GetSurveyHeaderLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>"
                    + "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>"
                    + "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>"
                    + "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>"
                    + "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>"
                    + "<date><![CDATA[" + currentLocation.getDate() + "]]></date>"
                    + "</location>";
            if (currentLocation.getTransId().startsWith("NSU")) {
                xmlData += "<nosurvey>";
                xmlData += location;
                xmlData += "<nosurveydata>";
                ArrayList<SurveyDetails> unUploadedSurveyDetails = mAceDnsTransactionDatabase.GETSurveyHeader(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedSurveyDetails.size(); jj++) {
                    SurveyDetails mSaudaDetails = unUploadedSurveyDetails.get(jj);
                    xmlData += "<survey_id><![CDATA[" + mSaudaDetails.getSurveyID() + "]]></survey_id>"
                            + "<survey_type><![CDATA[" + mSaudaDetails.getType() + "]]></survey_type>"
                            + "<menu_name><![CDATA[" + mSaudaDetails.getMenuName() + "]]></menu_name>"
                            + "<mall_id><![CDATA[" + mSaudaDetails.getMallID() + "]]></mall_id>"
                            + "<mall_name><![CDATA[" + mSaudaDetails.getMallName() + "]]></mall_name>"
                            + "<business_name><![CDATA[" + mSaudaDetails.getBusinessName() + "]]></business_name>";
                }
                xmlData += "</nosurveydata></nosurvey>";
            } else if (currentLocation.getTransId().trim().startsWith("A")) {
                xmlData += "<attendance>";
                xmlData += location;
                ArrayList<Attendance> unUploadedAttendance = mAceDnsTransactionDatabase.getUnuploadedAttendance(currentLocation.getTransId());
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
