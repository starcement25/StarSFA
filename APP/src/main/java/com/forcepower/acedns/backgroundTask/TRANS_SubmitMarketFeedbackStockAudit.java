package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.MarketFeedbackStockAudit;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.SaudaHeader;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class TRANS_SubmitMarketFeedbackStockAudit extends AsyncTask<String, Void, String> {

    public String str_getStatus = "";
    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String message = "";
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog mProgressDialog;

    public TRANS_SubmitMarketFeedbackStockAudit(Context context, boolean finish, String getStatus) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        message = "Uploading data.Please wait.";
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.str_getStatus = getStatus;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (str_getStatus.equals("SUBMIT")) {
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Uploading data.Please wait..");
            mProgressDialog.setCancelable(false);
            mProgressDialog.show();
        }

    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {

            try {
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitMarketFeedbackStock + "?nick_name="
                        + Constants.nickName + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitMarketFeedbackStockAudit: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitMarketFeedbackStockAudit value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitMarketFeedbackStockAudit result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (str_getStatus.equals("SUBMIT")) {
            mProgressDialog.cancel();
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.UpadateStockAuditLocation();
                dataHelperObj.closeDatabase();
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
                //Toast.makeText(mContext, "Submitted successfully", 15000).show();
                if (finish) {
                    new TRANS_SubmitFeedBack(mContext, true, "SUBMIT").execute();
                }
            } else {
                dataHelperObj.closeDatabase();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                            | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.UpadateStockAuditLocation();
                dataHelperObj.closeDatabase();
            }
        }

    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetStockAuditLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" + "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>"
                    + "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>"
                    + "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>"
                    + "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>"
                    + "<date><![CDATA[" + currentLocation.getDate() + "]]></date>"
                    + "<purpose_of_visit><![CDATA["+ currentLocation.getPurpose_of_visit() +"]]></purpose_of_visit>"
                    + "</location>";

            if (currentLocation.getTransId().startsWith("MS") || currentLocation.getTransId().startsWith("NMS")) {

                xmlData += "<MARKET_FEEDBACK_STKAUDIT>";
                xmlData += location;
                xmlData += "<MF_STKAUDIT_DATA>";
                SaudaHeader mSaudaHeader = dataHelperObj.GETMarketFeedbackHeader(currentLocation.getTransId());

                xmlData += "<MF_STKAUDIT_HEADER>"
                        + "<MF_STKAUDIT_ID><![CDATA[" + mSaudaHeader.getSaudaNo() + "]]></MF_STKAUDIT_ID>"
                        + "<customer_code><![CDATA[" + mSaudaHeader.getCustomerCode() + "]]></customer_code>"
                        + "<image_name><![CDATA[" + mSaudaHeader.getImageName() + "]]></image_name>"
                        + "<remarks><![CDATA[" + mSaudaHeader.getRemarks() + "]]></remarks>"
                        + "</MF_STKAUDIT_HEADER>";

                ArrayList<MarketFeedbackStockAudit> unUploadedMarketFeedbackStockAudit = dataHelperObj.GETMarketFeedbackStockAuditDetails(mSaudaHeader.getSaudaNo());
                for (int jj = 0; jj < unUploadedMarketFeedbackStockAudit.size(); jj++) {
                    MarketFeedbackStockAudit marketFeedbackStockAudit = unUploadedMarketFeedbackStockAudit.get(jj);
                    xmlData += "<MF_STKAUDIT_DETAILS>"

                            + "<MF_STKAUDIT_ID><![CDATA[" + marketFeedbackStockAudit.getStockAuditId() + "]]></MF_STKAUDIT_ID>"
                            + "<COMPETITOR_NAME><![CDATA[" + marketFeedbackStockAudit.getCompetitorName().toUpperCase() + "]]></COMPETITOR_NAME>"
                            + "<QTY_MT><![CDATA[" + marketFeedbackStockAudit.getQuantity() + "]]></QTY_MT>"
                            + "<SCHEME_DISCOUNT><![CDATA[" + marketFeedbackStockAudit.getDiscount() + "]]></SCHEME_DISCOUNT>"

                            + "</MF_STKAUDIT_DETAILS>";
                }

                xmlData += "</MF_STKAUDIT_DATA></MARKET_FEEDBACK_STKAUDIT>";
            } else if (currentLocation.getTransId().trim().startsWith("A")) {
                xmlData += "<attendance>";
                xmlData += location;
                ArrayList<Attendance> unUploadedAttendance = dataHelperObj.getUnuploadedAttendance(currentLocation.getTransId());
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
