package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.CallDurationDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_CallDurationTransactionTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<CallDurationDetails> unUploadedTransaction;
    Boolean isUnuploadedDataPresent = false;

    public TRANS_CallDurationTransactionTask(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (finish) {
            Utils.showProgressDialog(mContext, "Uploading call duration. Please wait..");
        }
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (isUnuploadedDataPresent) {
            if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
                try {
                    String url = BaseUrl.baseUrl + AceDnsWebServiceURL.callDurationTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    prepareXMLData();
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CallDurationTransactionTask: " +url);
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CallDurationTransactionTask value: " +xmlData);
                    POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlData);
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CallDurationTransactionTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {

            for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(), "transaction_id", "call_duration");
            }

        }
        if (finish) {
            Utils.cancelProgressDialog();
        }
        mAceDnsTransactionDatabase.closeDatabase();
    }


    public void prepareXMLData() {
        unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedCallDurationList();
        if (unUploadedTransaction.size() > 0) {
            isUnuploadedDataPresent = true;
            xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
            for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                xmlData += "<call_duration>";
                xmlData += "<call_duration_details>"
                        + "<transaction_id><![CDATA[" + unUploadedTransaction.get(ii).getTransId() + "]]></transaction_id>"
                        + "<customer_code><![CDATA[" + unUploadedTransaction.get(ii).getCustomerCode() + "]]></customer_code>"
                        + "<callduration><![CDATA[" + unUploadedTransaction.get(ii).getCallDuration() + "]]></callduration>"
                        + "</call_duration_details>";
                xmlData += "</call_duration>";
            }
            xmlData += "</root>";
        }
    }
}
