package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.TourExpenseLandingActivitySpecial;
import com.forcepower.acedns.bean.CashDeposit;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitCashDepositTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitCashDepositTask(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");

    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (finish) {
            Utils.showProgressDialog(mContext, "Uploading data. Please wait..");
        }

    }

    @Override
    protected String doInBackground(String... params) {
        prepareXMLData();
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !xmlData.matches("")) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.cashDepositTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCashDepositTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCashDepositTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCashDepositTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
            }
            if (finish) {
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
            }
        } else {
            if (finish) {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
            }
        }
        mAceDnsTransactionDatabase.closeDatabase();
        if (finish) {
            Utils.cancelProgressDialog();
            Intent intent = new Intent(mContext, TourExpenseLandingActivitySpecial.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            mContext.startActivity(intent);
        }

    }


    public void prepareXMLData() {
        Boolean isUnsyncedDataPresent = false;
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
        int numberOfUnUplodedData = unUploadedTransaction.size();
        if (numberOfUnUplodedData > 0) {
            for (int ii = 0; ii < numberOfUnUplodedData; ii++) {
                Location currentLocation = unUploadedTransaction.get(ii);
                String location = "<location>" +
                        "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                        "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                        "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                        "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                        "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                        "</location>";
                if (currentLocation.getTransId().startsWith("CD")) {
                    isUnsyncedDataPresent = true;
                    xmlData += "<cash_deposit_details>";
                    xmlData += location;
                    ArrayList<CashDeposit> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedCashDeposit(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {

                        CashDeposit currentItem = unUploadedOrdrHeadr.get(jj);
                        xmlData += "<cashdepositdata>"
                                + "<cash_deposit_trans_id><![CDATA[" + currentItem.getcash_deposit_trans_id() + "]]></cash_deposit_trans_id>"
                                + "<bank_name><![CDATA[" + currentItem.getbank_name() + "]]></bank_name>"
                                + "<deposit_value><![CDATA[" + currentItem.getdeposit_value() + "]]></deposit_value>"
                                + "</cashdepositdata>";

                    }
                    xmlData += "</cash_deposit_details>";
                } else {
                    dataHelperObj.closeDatabase();
//					return "No data present";
                }
            }
            xmlData += "</root>";
        }

        if (!isUnsyncedDataPresent) {
            xmlData = "";
        }
        dataHelperObj.closeDatabase();
    }
}
