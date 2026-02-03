package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.WholeSaleInfo;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitWholeSaleTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog mProgressDialog;
    String str_status;

    public TRANS_SubmitWholeSaleTask(Context context, String status) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
        this.str_status = status;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        if (str_status.equals("SUBMIT")) {
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Uploading data.\nPlease wait..");
            mProgressDialog.setCancelable(false);
            mProgressDialog.show();
        }

    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                xmlData = prepareXMLData();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitWholeSaleURL + "?nick_name="
                        + Constants.nickName + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;

                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitWholeSaleTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitWholeSaleTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitWholeSaleTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (str_status.equals("SUBMIT")) {
            mProgressDialog.cancel();
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.UpadateWholeSaleLocation();
                mAceDnsTransactionDatabase.UpdateWholeSalerDetails();
                mAceDnsTransactionDatabase.closeDatabase();
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
            } else {
                mAceDnsTransactionDatabase.closeDatabase();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
            }
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            mContext.startActivity(intent);
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.UpadateWholeSaleLocation();
                mAceDnsTransactionDatabase.UpdateWholeSalerDetails();
            }

            mAceDnsTransactionDatabase.closeDatabase();
        }

    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.GetWholeSaleLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>"
                    + "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>"
                    + "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>"
                    + "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>"
                    + "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>"
                    + "<date><![CDATA[" + currentLocation.getDate() + "]]></date>"
                    + "</location>";

            if (currentLocation.getTransId().startsWith("W")) {
                xmlData += "<wholesaler_info>";
                xmlData += location;
                WholeSaleInfo obj = mAceDnsTransactionDatabase.GETWholeSaleInfoDetails(currentLocation.getTransId());
                xmlData += "<wholesaler_details>"
                        + "<wholesale_trans_id><![CDATA[" + obj.getWholesaleID() + "]]></wholesale_trans_id>"
                        + "<customer_code><![CDATA[" + obj.getCustomerCode() + "]]></customer_code>"
                        + "<debit_not_collected><![CDATA[" + obj.getDebitnotCollected() + "]]></debit_not_collected>"
                        + "<last_debit_note_received><![CDATA[" + obj.getLastDebitNoteReceived() + "]]></last_debit_note_received>"
                        + "<closing_stock_value><![CDATA[" + obj.getClosingStockValue() + "]]></closing_stock_value>"
                        + "<log_book><![CDATA[" + obj.getLogBook() + "]]></log_book>"
                        + "</wholesaler_details>";

                xmlData += "</wholesaler_info>";
            } else if (currentLocation.getTransId().trim().startsWith("A")) {
                xmlData += "<attendance>";
                xmlData += location;
                ArrayList<Attendance> unUploadedAttendance = mAceDnsTransactionDatabase.getUnuploadedAttendance(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedAttendance.size(); jj++) {
                    Attendance detailsObj = unUploadedAttendance.get(jj);
                    xmlData += "<attendancedata>"
                            + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                            + "<date><![CDATA[" + detailsObj.getDate() + "]]></date>" +
                            "</attendancedata>";
                }
                xmlData += "</attendance>";
            }
        }
        xmlData += "</root>";
        return xmlData;
    }
}
