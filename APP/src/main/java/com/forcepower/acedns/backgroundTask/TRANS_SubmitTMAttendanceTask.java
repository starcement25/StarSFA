package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitTMAttendanceTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    ProgressDialog pd;
    Boolean shouldFinish = false;
    ArrayList<commonDatabaseHelper> unUploadedData;
    public TRANS_SubmitTMAttendanceTask(Context context, Boolean shouldFinish) {
        this.mContext = context;
        this.shouldFinish = shouldFinish;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        unUploadedData=new ArrayList<>();
        unUploadedData=dataHelperObj.getUnuploadedTMStatus();
    }
    public String prepareXMLDataTMApproval(Context mContext) {
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        for (int jj = 0; jj < unUploadedData.size(); jj++)
        {
            commonDatabaseHelper currentHeader = unUploadedData.get(jj);
            xmlData += "<TECH_MEET_STATUS>"
                    + "<meet_id><![CDATA[" + currentHeader.getItem0() + "]]></meet_id>"
                    + "<meet_date><![CDATA[" + currentHeader.getItem1() + "]]></meet_date>"
                    + "<no_of_mason><![CDATA[" + currentHeader.getItem2() + "]]></no_of_mason>"
                    + "<dealer_code><![CDATA[" + currentHeader.getItem3() + "]]></dealer_code>"
                    + "<dealer_name><![CDATA[" + currentHeader.getItem4() + "]]></dealer_name>"
                    + "<mason_details><![CDATA[" + currentHeader.getItem5() + "]]></mason_details>"
                    + "<meet_status><![CDATA[" + currentHeader.getItem6() + "]]></meet_status>"
                    + "<status_update_date_time><![CDATA[" + currentHeader.getItem7() + "]]></status_update_date_time>"
                    + "<status_update_by><![CDATA[" + currentHeader.getItem8() + "]]></status_update_by>"
                    + "<image><![CDATA[" + currentHeader.getItem9() + "]]></image>"
                    + "<remarks><![CDATA[" + currentHeader.getItem10() + "]]></remarks>"
                    +"</TECH_MEET_STATUS>";
        }

            xmlData += "</root>";

        return xmlData;
    }
    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (shouldFinish) {
            pd = new ProgressDialog(mContext);
            pd.setMessage("Submitting transaction.Please wait");
            pd.setCancelable(false);
            pd.show();
        }
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if(unUploadedData.size()>0)
        {
            xmlData = prepareXMLDataTMApproval(mContext);
                if (HTTPUtils.isConnectionPossible(mContext) ) {
                    try {
                        String uri = BaseUrl.baseUrl +
                                AceDnsWebServiceURL.submitTMStatusUpdate
                                + "?nick_name=" + Constants.nickName
                                + "&emp_code="
                                + Constants.employeeDetailObject.getEmpCode();
                        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTMAttendanceTask: " +uri);
                        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTMAttendanceTask value: " +xmlData);
                        POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
                    } catch (Exception e) {
                        POST_result = "Network Failure";
                    } finally {
                    }
                }

        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTMAttendanceTask result: " +POST_result);

        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (unUploadedData.size()>0) {
            if (result.length() == 1)
            {
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                    dataHelperObj.updateUnUpdatedTMApprovalStatusData();
                }
            }
        }

        if (shouldFinish) {
            pd.cancel();
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            mContext.startActivity(intent);
        }

    }
}
