package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.StockAuditDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitStockReturnTask extends AsyncTask<String, Void, String> {

    public String st_getStatus = "";
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitStockReturnTask(Context context, boolean finish, String status) {
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
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                prepareXMLData();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitStockReturnURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitStockReturnTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitStockReturnTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitStockReturnTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (st_getStatus.equals("SUBMIT"))
        {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                Utils.cancelProgressDialog();
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
                }
                mAceDnsTransactionDatabase.updateUnUploadedHintRemarks("S");
                mAceDnsTransactionDatabase.updateUnuploadedStockAuditDetails();
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
                Utils.cancelProgressDialog();
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
        unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("STOCK_RETURN", "");
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
                xmlData += "<van_stock_return>";
                xmlData += location;
                ArrayList<StockAuditDetails> unUploadedStockAudit = mAceDnsTransactionDatabase.getUnuploadedStockReturnDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedStockAudit.size(); jj++)
                {
                    StockAuditDetails currentHeader = unUploadedStockAudit.get(jj);
                    String hintRemarks = mAceDnsTransactionDatabase.getUnuploadedHintRemarks(currentLocation.getTransId());
                        xmlData += "<van_stock_return_details>"
                                + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                + "<prod_code><![CDATA[" + currentHeader.getProductCode() + "]]></prod_code>"
                                + "<return_trans_id><![CDATA[" + currentHeader.getTransactionId() + "]]></return_trans_id>"
                                + "<return_qty><![CDATA[" + currentHeader.getQuantity() + "]]></return_qty>"
                                + "<order_no><![CDATA[" + currentHeader.getReturnOrderNumber() + "]]></order_no>"
                                + "<return_reason><![CDATA[" + currentHeader.getReturnReason() + "]]></return_reason>"
                                + "</van_stock_return_details>";

                }
                xmlData += "</van_stock_return>";

        }
        xmlData += "</root>";
    }
}
