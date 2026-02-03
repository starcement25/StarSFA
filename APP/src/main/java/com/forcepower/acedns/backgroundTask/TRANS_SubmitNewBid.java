package com.forcepower.acedns.backgroundTask;

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

public class TRANS_SubmitNewBid extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String message = "";
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
//	ProgressDialog mProgressDialog;

    public TRANS_SubmitNewBid(Context context, boolean finish) {
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
//		if(finish)
//		{
//			mProgressDialog = new ProgressDialog(mContext);
//			mProgressDialog.setMessage("Uploading data.Please wait..");
//			mProgressDialog.setCancelable(false);
//			mProgressDialog.show();
//		}
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            String url = parentURLLaravel + AceDnsWebServiceURL.submitNewBidURL;
            try {
                MCrypt mcrypt = new MCrypt();
                JSONObject jo = new JSONObject();
                jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
                jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
                //			jo.put("verificationcode",MCrypt.bytesToHex( mcrypt.encrypt(Constants.employeeDetailObject.getverificationtoken()) ));
                jo.put("xmldata", MCrypt.bytesToHex(mcrypt.encrypt(xmlData)));
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewBid: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewBid value: " +jo);
                POST_result = new HttpCalling().httpPostCallWithXmlResponseDecrypted(url, jo.toString()).trim();
            } catch (Exception e) {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewBid result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
//		if(finish)
//		{
//			mProgressDialog.cancel();
//		}
        if (result.equalsIgnoreCase("success")) {
            dataHelperObj.UpadateNewBidLocation();
            dataHelperObj.closeDatabase();
            if (finish) {
                Toast.makeText(mContext, "Bid submitted successfully", 15000).show();
            }
        } else {
            dataHelperObj.closeDatabase();
            if (finish) {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000).show();
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
        ArrayList<Location> unUploadedTransaction = dataHelperObj.GetUnSyncedNewBidLocation();
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

            xmlData += "<RA_bid_rate>";
            xmlData += location;
            xmlData += "<RA_bid_rate_data>";

            ArrayList<PlantProductWiseRARate> unUploadedSaudaDetails = dataHelperObj.GetNewBidDetails(currentLocation.getTransId());
            for (int jj = 0; jj < unUploadedSaudaDetails.size(); jj++) {
                PlantProductWiseRARate saudadetails = unUploadedSaudaDetails.get(jj);
                xmlData += "<RA_bid_rate_details>"
                        + "<bid_id><![CDATA[" + saudadetails.getBidId() + "]]></bid_id>"
                        + "<plant_name><![CDATA[" + saudadetails.getPlantName() + "]]></plant_name>"
                        + "<prod_code><![CDATA[" + saudadetails.getProdCode() + "]]></prod_code>"
                        + "<released_rate><![CDATA[" + saudadetails.getReleaseRate() + "]]></released_rate>"
                        + "<base_rate><![CDATA[" + saudadetails.getBaseRate() + "]]></base_rate>"
                        + "<server_indicative_rate><![CDATA[" + saudadetails.getIndicativeRateServer() + "]]></server_indicative_rate>"
                        + "<app_indicative_rate><![CDATA[" + saudadetails.getIndicativeRateApp() + "]]></app_indicative_rate>"
                        + "<customer_code ><![CDATA[" + saudadetails.getCustomerCode() + "]]></customer_code>"
                        + "<qty><![CDATA[" + saudadetails.getQty() + "]]></qty>"
                        + "<bid_rate><![CDATA[" + saudadetails.getbidPrice() + "]]></bid_rate>"
                        + "<primary_freight><![CDATA[" + saudadetails.getPrimaryFreight() + "]]></primary_freight>"
                        + "<secondary_freight><![CDATA[" + saudadetails.getSecondaryFreight() + "]]></secondary_freight>"
                        + "<depot_cost><![CDATA[" + saudadetails.getDepotCost() + "]]></depot_cost>"
                        + "<GST_percent><![CDATA[" + saudadetails.getgstPercent() + "]]></GST_percent>"
                        + "<GST_value><![CDATA[" + saudadetails.getgstValue() + "]]></GST_value>"
                        + "<branch_code><![CDATA[" + saudadetails.getbranchCode() + "]]></branch_code>"
                        + "<incoterms><![CDATA[" + saudadetails.getuserChosenIncoterms() + "]]></incoterms>"
                        + "<vertical_value><![CDATA[" + saudadetails.getverticalOfEmployee() + "]]></vertical_value>"
                        + "<margin_cost><![CDATA[" + saudadetails.getmarginCost() + "]]></margin_cost>"
                        + "<honeycomb_cost><![CDATA[" + saudadetails.gethoneyCombCost() + "]]></honeycomb_cost>"
                        + "<detention_cost><![CDATA[" + saudadetails.getdetentionCost() + "]]></detention_cost>"
                        + "</RA_bid_rate_details>";
            }
            xmlData += "</RA_bid_rate_data></RA_bid_rate>";
        }
        xmlData += "</root>";
        return xmlData;
    }
}
