package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_DeleteTransactionTask extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    String message = "";
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    String newCustomerCode = "", newCustomerName = "", newCustomerNumber = "";
    ArrayList<String> loyaltyTransIdList;
    String loyaltyPurchaseUpdateTime = "2014-06-09 18:19:20";

    String MODE;
    String startDate, endDate, rdsCode, branchCode, transactionID;

    public TRANS_DeleteTransactionTask(Context context, String MODE, String transactionID, String startDate, String endDate, String rdsCode, String branchCode) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        message = "Deleting transaction.Please wait.";
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        loyaltyPurchaseUpdateTime = dataHelperObj.getlastDownloadTime("loyalty_purchase_details");

        this.startDate = startDate;
        this.endDate = endDate;
        this.rdsCode = rdsCode;
        this.branchCode = branchCode;
        this.transactionID = transactionID;
        this.MODE = MODE;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, message);
        xmlData = prepareXMLData();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitTransactionDeletionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&last_loyalty_purchase_update_time=" + loyaltyPurchaseUpdateTime;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_DeleteTransactionTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_DeleteTransactionTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_DeleteTransactionTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1")) {
            Utils.showToast(mContext, "Transaction has been deleted successfully.");
        } else if (result.equalsIgnoreCase("2")) {
            Utils.showToast(mContext, "Transaction ID not found.\nPlease contact ACEdns Admin.");
        } else if (result.equalsIgnoreCase("0")) {
            Utils.showToast(mContext, "Transaction could not be Deleted.\nPlease contact ACEdns Admin.");
        } else {
            Utils.showToast(mContext, "");
        }
        dataHelperObj.closeDatabase();
        Intent intent = new Intent(mContext, MenuActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        mContext.startActivity(intent);
    }

    public String prepareXMLData() {
        String xmlData = "";
        loyaltyTransIdList = new ArrayList<String>();
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        xmlData = xmlData + "<deletion_data>" +
                "<order_no><![CDATA[" + transactionID + "]]></order_no>" +
                "<branch_code><![CDATA[" + branchCode + "]]></branch_code>" +
                "<rds_code><![CDATA[" + rdsCode + "]]></rds_code>" +
                "<start_date><![CDATA[" + startDate + "]]></start_date>" +
                "<end_date><![CDATA[" + endDate + "]]></end_date>" +
                "<deletion_mode><![CDATA[" + MODE + "]]></deletion_mode>" +
                "</deletion_data>";
        xmlData += "</root>";
        dataHelperObj.closeDatabase();
        return xmlData;
    }
}
