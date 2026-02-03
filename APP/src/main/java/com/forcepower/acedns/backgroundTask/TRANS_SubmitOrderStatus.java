package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.OrderStatus;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitOrderStatus extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitOrderStatus(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                xmlData = PrepareXML();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitOrderStatusURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderStatus: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderStatus value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderStatus result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            Utils.cancelProgressDialog();
            dataHelperObj.UpdateOrderStatus();
            dataHelperObj.closeDatabase();
            if (finish) {
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                            | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                } else {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                            | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }

        } else {
            Utils.cancelProgressDialog();
            Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000).show();
            dataHelperObj.closeDatabase();
            if (finish) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                        | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
    }

    public String PrepareXML() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";

        ArrayList<OrderStatus> orderStatusList = dataHelperObj.GetUnuploadedOrderStatus();
        for (int count = 0; count < orderStatusList.size(); count++) {
            OrderStatus orderStatus = orderStatusList.get(count);
            xmlData += "<order_status>"
                    + "<order_no><![CDATA[" + orderStatus.getOrderNo() + "]]></order_no>"
                    + "<product_code><![CDATA[" + orderStatus.getProductCode() + "]]></product_code>"
                    + "<delivery_qty><![CDATA[" + orderStatus.getAlreadyDeliveredQuantity() + "]]></delivery_qty>"
                    + "<remarks><![CDATA[" + orderStatus.getRemarks() + "]]></remarks>"
                    + "<status><![CDATA[" + orderStatus.getStatus() + "]]></status>"
                    + "</order_status>";
        }
        xmlData += "</root>";
        return xmlData;
    }
}
