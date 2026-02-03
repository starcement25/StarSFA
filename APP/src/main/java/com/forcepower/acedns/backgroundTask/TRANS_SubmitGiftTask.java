package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitGiftTask extends AsyncTask<String, Void, String> {


    public String st_getStatus = "";
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitGiftTask(Context context, boolean finish, String status) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
        this.st_getStatus = status;
    }
    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (st_getStatus.equals("SUBMIT")) {
            Utils.showProgressDialog(mContext, "Uploading data. Please wait..");
        }
    }
    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) ) {
            try {
                prepareXMLData();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.giftDeliveryTransaction + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitGiftTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitGiftTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitGiftTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (st_getStatus.equals("SUBMIT"))
        {
            Utils.cancelProgressDialog();
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
//                Utils.cancelProgressDialog();
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
                }
                mAceDnsTransactionDatabase.closeDatabase();
                if (finish) {
                    if (result.equalsIgnoreCase("2")) {
                        Constants.dataResfresh = true;
                    }

                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            } else {
//                Utils.cancelProgressDialog();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                mAceDnsTransactionDatabase.closeDatabase();
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        }
        else
        {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2"))
            {
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
                {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
                }
            }
            mAceDnsTransactionDatabase.closeDatabase();
        }

    }


    public void prepareXMLData() {
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("gift_transaction", "");
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
        {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";
                xmlData += "<gift_delivery>";
                xmlData += location;
                ArrayList<commonDatabaseHelper> unUploadedStockAudit = mAceDnsTransactionDatabase.getUnuploadedGiftDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedStockAudit.size(); jj++)
                {
                    commonDatabaseHelper currentHeader = unUploadedStockAudit.get(jj);
                        xmlData += "<gift_delivery_details>"
                                + "<delivery_id><![CDATA[" + currentHeader.getItem0() + "]]></delivery_id>"
                                + "<delivery_date><![CDATA[" + currentHeader.getItem1() + "]]></delivery_date>"
                                + "<gift_id><![CDATA[" + currentHeader.getItem2() + "]]></gift_id>"
                                + "<gift_name><![CDATA[" + currentHeader.getItem3() + "]]></gift_name>"
                                + "<customer_broad_option><![CDATA[" + currentHeader.getItem4() + "]]></customer_broad_option>"
                                + "<customer_code><![CDATA[" + currentHeader.getItem5() + "]]></customer_code>"
                                + "<gift_delivery_option><![CDATA[" + currentHeader.getItem6() + "]]></gift_delivery_option>"
                                + "<owner_name><![CDATA[" + currentHeader.getItem7() + "]]></owner_name>"
                                + "<employee_name><![CDATA[" + currentHeader.getItem8() + "]]></employee_name>"
                                + "<employee_mobile><![CDATA[" + currentHeader.getItem9() + "]]></employee_mobile>"
                                + "<employee_relation_owner><![CDATA[" + currentHeader.getItem10() + "]]></employee_relation_owner>"
                                + "<address><![CDATA[" + currentHeader.getItem11() + "]]></address>"
                                + "<route_code><![CDATA[" + currentHeader.getItem12() + "]]></route_code>"
                                + "<image_1><![CDATA[" + currentHeader.getItem13() + "]]></image_1>"
                                + "<image_2><![CDATA[" + currentHeader.getItem14() + "]]></image_2>"
                                + "</gift_delivery_details>";
                }
                xmlData += "</gift_delivery>";

        }
        xmlData += "</root>";
    }
}
