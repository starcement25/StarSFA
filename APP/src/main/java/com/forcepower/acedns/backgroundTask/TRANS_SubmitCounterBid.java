package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.PlantProductWiseRARate;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.MCrypt;
import org.json.JSONObject;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.AceDnsWebServiceURL.parentURLLaravel;

public class TRANS_SubmitCounterBid extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String message = "";
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog mProgressDialog;

    public TRANS_SubmitCounterBid(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        message = "Uploading data.Please wait.";
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        xmlData = prepareXMLData();
        if (finish) {
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
            String url = parentURLLaravel + AceDnsWebServiceURL.submitCounterBidURL;
            try {
                MCrypt mcrypt = new MCrypt();
                JSONObject jo = new JSONObject();
                jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
                jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
                //			jo.put("verificationcode",MCrypt.bytesToHex( mcrypt.encrypt(Constants.employeeDetailObject.getverificationtoken()) ));
                jo.put("xmldata", MCrypt.bytesToHex(mcrypt.encrypt(xmlData)));
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCounterBid: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCounterBid value: " +jo);
                POST_result = new HttpCalling().httpPostCallWithXmlResponseDecrypted(url, jo.toString()).trim();
            } catch (Exception e) {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCounterBid result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (finish) {
            mProgressDialog.cancel();
        }
        if (result.equalsIgnoreCase("success")) {
            dataHelperObj.UpadateCounterBidLocation();
            dataHelperObj.closeDatabase();
            if (finish) {
                Toast.makeText(mContext, "Bid submitted successfully", Toast.LENGTH_LONG).show();
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                        | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        } else {
            dataHelperObj.closeDatabase();
            if (finish) {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                        | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
    }

    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";

        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetUnSyncedCounterBidLocation();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" + "<emp_code><![CDATA["
                    + currentLocation.getEmpCode() + "]]></emp_code>"
                    + "<trans_id><![CDATA[" + currentLocation.getTransId()
                    + "]]></trans_id>" + "<latt><![CDATA["
                    + currentLocation.getLatitude() + "]]></latt>"
                    + "<longi><![CDATA[" + currentLocation.getLongitude()
                    + "]]></longi>" + "<date><![CDATA["
                    + currentLocation.getDate() + "]]></date>" + "</location>";

            xmlData += "<counter_bid_rate>";
            xmlData += location;
            xmlData += "<counter_bid_rate_data>";

            ArrayList<PlantProductWiseRARate> unUploadedSaudaDetails = dataHelperObj.GetCounterBidDetails(currentLocation.getTransId());
            for (int jj = 0; jj < unUploadedSaudaDetails.size(); jj++) {
                PlantProductWiseRARate saudadetails = unUploadedSaudaDetails.get(jj);
                xmlData += "<counter_bid_rate_details>"
                        + "<counter_bid_id><![CDATA[" + saudadetails.getCounterBidId() + "]]></counter_bid_id>"
                        + "<bid_id><![CDATA[" + saudadetails.getBidId() + "]]></bid_id>"
                        + "<prod_code><![CDATA[" + saudadetails.getProdCode() + "]]></prod_code>"
                        + "<bid_status><![CDATA[" + saudadetails.getCounterBidStatus() + "]]></bid_status>"
                        + "</counter_bid_rate_details>";
            }
            xmlData += "</counter_bid_rate_data></counter_bid_rate>";
        }
        xmlData += "</root>";
        return xmlData;
    }
}
