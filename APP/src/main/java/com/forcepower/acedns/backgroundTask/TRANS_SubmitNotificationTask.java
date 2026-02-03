package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitNotificationTask extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    String gitMasterUpdateTime = "2014-06-09 18:19:20";
    String loyaltyPurchaseUpdateTime = "2014-06-09 18:19:20";
    //ProgressDialog pd;
    AceDnsDatabase dbObj;
    GPSTracker gpstracker;
    ArrayList<Location> unUploadedTransaction;

    public TRANS_SubmitNotificationTask(Context context) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.dbObj = new AceDnsDatabase(mContext);
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        gitMasterUpdateTime = dataHelperObj.getlastDownloadTime("git_master");
        loyaltyPurchaseUpdateTime = dataHelperObj.getlastDownloadTime("loyalty_purchase_details");

        gpstracker = new GPSTracker(mContext);

    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.pushAckURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&last_git_master_update_time=" + gitMasterUpdateTime + "&last_loyalty_purchase_update_time=" + loyaltyPurchaseUpdateTime;
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNotificationTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNotificationTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNotificationTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1")) {
            for (int j = 0; j < unUploadedTransaction.size(); j++) {
                dataHelperObj.updateUnuploadedLocation(unUploadedTransaction.get(j).getTransId(), "trans_id", "location");
            }
        }
        dataHelperObj.closeDatabase();
    }


    public String prepareXMLData() {
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        unUploadedTransaction = dataHelperObj.getUnPublishNOTIFICATION();
        if (unUploadedTransaction.size() > 0) {
            for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                Location currentLocation = unUploadedTransaction.get(ii);
                String notId = dataHelperObj.getNotfId(currentLocation.getTransId());
                xmlData += "<notification>";
                xmlData += "<location>" +
                        "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                        "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                        "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                        "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                        "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                        "<notification_id><![CDATA[" + notId + "]]></notification_id>" +
                        "</location>";
                xmlData += "</notification>";

            }
        }

        xmlData += "</root>";
        return xmlData;

    }
}
