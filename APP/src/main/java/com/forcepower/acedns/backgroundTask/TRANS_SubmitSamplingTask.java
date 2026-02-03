package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.ProductPromotionDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitSamplingTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog mProgressDialog;

    public TRANS_SubmitSamplingTask(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Uploading data.\nPlease wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {

                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitSamplingURL + "?nick_name="
                        + Constants.nickName + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSamplingTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSamplingTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSamplingTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        mProgressDialog.cancel();
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            mAceDnsTransactionDatabase.UpdateSamplingLocation();
            mAceDnsTransactionDatabase.closeDatabase();
            if (result.equalsIgnoreCase("2")) {
                Constants.dataResfresh = true;
            }
            new TRANS_SubmitSMS(mContext, true).execute();
        } else {
            mAceDnsTransactionDatabase.closeDatabase();
            Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000).show();
            new TRANS_SubmitSMS(mContext, true).execute();
        }
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.GetSamplingLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";

            if (currentLocation.getTransId().startsWith("PP")) {
                xmlData += "<product_promotion>";
                xmlData += location;
                xmlData += "<product_promotion_details>";
                ProductPromotionDetails mProductPromotionDetails = mAceDnsTransactionDatabase.GETSampling(currentLocation.getTransId());

                xmlData +=
                        "<PROSPECT_CODE><![CDATA[" + mProductPromotionDetails.getProspectCode() + "]]></PROSPECT_CODE>"
                                + "<PROSPECT_NAME><![CDATA[" + mProductPromotionDetails.getProspectName() + "]]></PROSPECT_NAME>"
                                + "<PIN><![CDATA[" + mProductPromotionDetails.getPinCode() + "]]></PIN>"
                                + "<STREET_NAME><![CDATA[" + mProductPromotionDetails.getStreetName() + "]]></STREET_NAME>"
                                + "<STREET_NO><![CDATA[" + mProductPromotionDetails.getStreetNo() + "]]></STREET_NO>"
                                + "<BUILDING_NO><![CDATA[" + mProductPromotionDetails.getBuildingNo() + "]]></BUILDING_NO>"
                                + "<APARTMENT_NO><![CDATA[" + mProductPromotionDetails.getApartmentNo() + "]]></APARTMENT_NO>"
                                + "<PHONE_NO><![CDATA[" + mProductPromotionDetails.getPhoneNo() + "]]></PHONE_NO>"
                                + "<EMAIL><![CDATA[" + mProductPromotionDetails.getEmailID() + "]]></EMAIL>"
                                + "<COMPETITOR_NAME><![CDATA[" + mProductPromotionDetails.getCompetitorName().toUpperCase() + "]]></COMPETITOR_NAME>"
                                + "<OIL_USED><![CDATA[" + mProductPromotionDetails.getOilUsed() + "]]></OIL_USED>";


                xmlData += "</product_promotion_details></product_promotion>";
            } else if (currentLocation.getTransId().startsWith("A")) {
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