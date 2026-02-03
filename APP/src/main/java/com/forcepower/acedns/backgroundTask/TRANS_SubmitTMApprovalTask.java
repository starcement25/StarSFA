package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitTMApprovalTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    ProgressDialog pd;
    Boolean shouldFinish = false;
    ArrayList<commonDatabaseHelper> unUploadedData;
    public TRANS_SubmitTMApprovalTask(Context context, Boolean shouldFinish) {
        this.mContext = context;
        this.shouldFinish = shouldFinish;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        unUploadedData=new ArrayList<>();
        unUploadedData=dataHelperObj.getUnuploadedTMApproval();
    }
    public String prepareXMLDataTMApproval(Context mContext) {
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        for (int jj = 0; jj < unUploadedData.size(); jj++)
        {
            commonDatabaseHelper currentHeader = unUploadedData.get(jj);
            String approvalStatus = currentHeader.getItem5().toLowerCase();
            if(approvalStatus.equalsIgnoreCase("approved"))
            {
                approvalStatus="yes";
            }
            else if(approvalStatus.equalsIgnoreCase("rejected"))
            {
                approvalStatus="reject";
            }
            xmlData += "<TECH_MEET_APPROVAL>"
                    + "<meet_id><![CDATA[" + currentHeader.getItem0() + "]]></meet_id>"
                    + "<meet_date><![CDATA[" + currentHeader.getItem1() + "]]></meet_date>"
                    + "<no_of_mason><![CDATA[" + currentHeader.getItem2() + "]]></no_of_mason>"
                    + "<dealer_code><![CDATA[" + currentHeader.getItem3() + "]]></dealer_code>"
                    + "<dealer_name><![CDATA[" + currentHeader.getItem4() + "]]></dealer_name>"
                    + "<is_approved><![CDATA[" + approvalStatus + "]]></is_approved>"
                    + "<approved_date_time><![CDATA[" + currentHeader.getItem6() + "]]></approved_date_time>"
                    + "<approved_by><![CDATA[" + currentHeader.getItem7() + "]]></approved_by>"
                    +"</TECH_MEET_APPROVAL>";
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
                                AceDnsWebServiceURL.submitTMApproval
                                + "?nick_name=" + Constants.nickName
                                + "&emp_code="
                                + Constants.employeeDetailObject.getEmpCode();
                        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTMApprovalTask: " +uri);
                        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTMApprovalTask value: " +xmlData);
                        POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
                    } catch (Exception e) {
                        POST_result = "Network Failure";
                    } finally {
                    }
                }

        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTMApprovalTask result: " +POST_result);

        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (unUploadedData.size()>0) {
            if (result.length() == 1)
            {
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                    dataHelperObj.updateUnUpdatedTMApprovalData();
                }
            }
        }

        if (shouldFinish) {
            pd.cancel();
            Activity activity = (Activity) mContext;
            activity.finish();
        }

    }
}
