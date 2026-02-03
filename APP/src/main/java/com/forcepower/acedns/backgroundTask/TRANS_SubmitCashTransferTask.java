package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.TourExpenseLandingActivitySpecial;
import com.forcepower.acedns.bean.CashTransferReceive;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitCashTransferTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<Location> unUploadedTransactionAll;
    ArrayList<Location> unUploadedTransactionCTorCR;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitCashTransferTask(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
        unUploadedTransactionCTorCR = new ArrayList<>();
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
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.cashTransferTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCashTransferTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCashTransferTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCashTransferTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            for (int ii = 0; ii < unUploadedTransactionCTorCR.size(); ii++) {
                mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransactionCTorCR.get(ii).getTransId());
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
        unUploadedTransactionAll = dataHelperObj.getUnuploadedTransaction("", "");
        int numberOfUnUplodedData = unUploadedTransactionAll.size();
        if (numberOfUnUplodedData > 0) {
            for (int ii = 0; ii < numberOfUnUplodedData; ii++) {
                Location currentLocation = unUploadedTransactionAll.get(ii);
                String location = "<location>" +
                        "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                        "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                        "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                        "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                        "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                        "</location>";
                if (currentLocation.getTransId().startsWith("CT") || currentLocation.getTransId().startsWith("CR")) {
                    unUploadedTransactionCTorCR.add(currentLocation);
                    isUnsyncedDataPresent = true;
                    xmlData += "<cash_transfer>";
                    xmlData += location;
                    ArrayList<CashTransferReceive> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedCashTransfer(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {

                        CashTransferReceive currentItem = unUploadedOrdrHeadr.get(jj);
                        xmlData += "<cashtransferdata>"
                                + "<cash_transaction_id><![CDATA[" + currentItem.getcash_trans_rcv_trans_id() + "]]></cash_transaction_id>"
                                + "<despatcher_code><![CDATA[" + currentItem.getdespatcher_code() + "]]></despatcher_code>"
                                + "<receiver_code><![CDATA[" + currentItem.getreceiver_code() + "]]></receiver_code>"
                                + "<despatch_value><![CDATA[" + currentItem.getdespatch_value() + "]]></despatch_value>"
                                + "<rec_value><![CDATA[" + currentItem.getrec_value() + "]]></rec_value>"
                                + "<transaction_type><![CDATA[" + currentItem.gettransaction_type() + "]]></transaction_type>"
                                + "<cash_transfer_id><![CDATA[" + currentItem.getcash_transfer_id() + "]]></cash_transfer_id>"
                                + "</cashtransferdata>";
                    }
                    xmlData += "</cash_transfer>";
                } else {
                    dataHelperObj.closeDatabase();
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
