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
import com.forcepower.acedns.util.Utils;


import static com.forcepower.acedns.activity.OrderEditActivity.grnMasterSkuListItemGlobal;

public class TRANS_SubmitOrderEditTransactionTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "",customerCode,orderNo;
    boolean firstTimeTransaction ;
    ProgressDialog mProgressDialog;

    public TRANS_SubmitOrderEditTransactionTask(Context context, boolean firstTimeTransaction,String customerCode,String orderNo,  AceDnsTransactionDatabase dataHelperObj  ) {
        this.mContext = context;
        this.dataHelperObj = dataHelperObj;
        this.firstTimeTransaction = firstTimeTransaction;
        this.customerCode = customerCode;
        this.orderNo = orderNo;
    }
    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        if (firstTimeTransaction) {
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Uploading data.Please wait..");
            mProgressDialog.setCancelable(false);
            mProgressDialog.show();
        }
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                xmlData = prepareXMLData();
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitOrderEditTransactionURL
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderEditTransactionTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderEditTransactionTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);

            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderEditTransactionTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
            if (firstTimeTransaction) {
                mProgressDialog.dismiss();
                Utils.showToast(mContext, "Transaction saved successfully");

                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                        | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
                dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
            }
            else{
                dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                Utils.showToast(mContext, "Something went Wrong while storing data. Transaction failed. Please contact admin!");
            }

    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";

        for (int i = 0; i < grnMasterSkuListItemGlobal.size(); i++) {
            commonDatabaseHelper commonDatabaseHelper = grnMasterSkuListItemGlobal.get(i);
            String oldQty = commonDatabaseHelper.getItem2();
            String newQty = commonDatabaseHelper.getItem7();
            String sRate = commonDatabaseHelper.getItem3();
            String skuCode = commonDatabaseHelper.getItem0();
            if(Utils.isNumeric(newQty) && Double.parseDouble(newQty)>0) {
                Double currentAmount = Double.parseDouble(sRate) * Double.parseDouble(newQty);
                xmlData += "<order_edit>"
                        + "<order_no><![CDATA[" + orderNo + "]]></order_no>"
                        + "<customer_code><![CDATA[" + customerCode + "]]></customer_code>"
                        + "<product_code><![CDATA[" + skuCode + "]]></product_code>"
                        + "<edit_qty><![CDATA[" + newQty + "]]></edit_qty>"
                        + "<edit_amount><![CDATA[" + Constants.defaultFormat.format(currentAmount) + "]]></edit_amount>"
                        + "</order_edit>";
            }
                }
        xmlData += "</root>";
        return xmlData;
    }

}
