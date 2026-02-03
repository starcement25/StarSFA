package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitOldCustomer extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitOldCustomer(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Uploading Customer Data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                xmlData = prepareXMLData();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitOldCustomerURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOldCustomer: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOldCustomer value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOldCustomer result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        Utils.cancelProgressDialog();
        if (Constants.dataResfresh == true) {
            Constants.dataResfresh = false;
            new AUTH_UpdateCheck(mContext, true).execute(Constants.employeeDetailObject.getEmpCode());
        } else {
            new DATA_LoadDatabaseDetails(mContext).execute(Constants.employeeDetailObject.getEmpCode());
        }
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        xmlData += "<new_customer>";
        ArrayList<CustomerDetails> unUploadedCust = mAceDnsTransactionDatabase.getUnuploadedCustomerList();
        for (int jj = 0; jj < unUploadedCust.size(); jj++) {
            CustomerDetails detailsObj = unUploadedCust.get(jj);
            xmlData += "<new_customer_details>"
                    + "<customer_code><![CDATA[" + detailsObj.getCustomerCode() + "]]></customer_code>"
                    + "<customer_name><![CDATA[" + detailsObj.getCustomerName() + "]]></customer_name>"
                    + "<Phone_no><![CDATA[" + detailsObj.getNumber() + "]]></Phone_no>"
                    + "<pin_code><![CDATA[" + detailsObj.getPin() + "]]></pin_code>"
                    + "<area><![CDATA[" + detailsObj.getNewRouteCode() + "]]></area>"
                    + "<area_name><![CDATA[" + detailsObj.getNewRouteName() + "]]></area_name>"
                    + "<rds_tag><![CDATA[" + detailsObj.getRdsTag() + "]]></rds_tag>"
                    + "</new_customer_details>";
        }
        xmlData += "</new_customer>";
        xmlData += "</root>";
        return xmlData;
    }
}

