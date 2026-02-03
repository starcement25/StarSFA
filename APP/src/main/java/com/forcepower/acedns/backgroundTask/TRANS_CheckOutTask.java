package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.YellowCard;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.RegisterActivities;

import java.util.ArrayList;

public class TRANS_CheckOutTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    String gitMasterUpdateTime = "2014-06-09 18:19:20";
    String loyaltyPurchaseUpdateTime = "2014-06-09 18:19:20";
    ProgressDialog pd;
    Boolean showProgressDialog;

    public TRANS_CheckOutTask(Context context, Boolean showProgressDialog) {
        this.mContext = context;
        this.showProgressDialog = showProgressDialog;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);

        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        gitMasterUpdateTime = dataHelperObj.getlastDownloadTime("git_master");
        loyaltyPurchaseUpdateTime = dataHelperObj.getlastDownloadTime("loyalty_purchase_details");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (showProgressDialog) {
            pd = new ProgressDialog(mContext);
            pd.setMessage("Check Out in process..Please wait");
            pd.setCancelable(false);
            pd.show();
        }

        xmlData = prepareXMLDataForCheckOut();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            ;
            try {

                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitCheckOutURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&last_git_master_update_time=" + gitMasterUpdateTime + "&last_loyalty_purchase_update_time=" + loyaltyPurchaseUpdateTime;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CheckOutTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CheckOutTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CheckOutTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (result.equalsIgnoreCase("1")) {
            if(!Constants.menuDetailsObj.getcheckout_journey_info().equalsIgnoreCase(null) && !Constants.menuDetailsObj.getcheckout_journey_info().equalsIgnoreCase("null") && !Constants.menuDetailsObj.getcheckout_journey_info().trim().equalsIgnoreCase(""))
            {
                ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("CHECKOUT", "");
                new TRANS_AttnendanceJourneyTransactionTask(mContext, unUploadedTransaction,"checkout").execute();
            }
            else{
                dataHelperObj.UpdateLocationDataForCheckOut();
                dataHelperObj.updateUnuploadedAttendance();
            }

        }
        dataHelperObj.closeDatabase();
        if (showProgressDialog) {
            pd.cancel();
            Intent startMain = new Intent(Intent.ACTION_MAIN);
            startMain.addCategory(Intent.CATEGORY_HOME);
            startMain.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            RegisterActivities.removeAllActivities();
        }

    }

    public String prepareXMLDataForCheckOut() {
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
        int numberOfUnUplodedData = unUploadedTransaction.size();
        if (numberOfUnUplodedData > 0) {
            for (int ii = 0; ii < numberOfUnUplodedData; ii++) {
                Location currentLocation = unUploadedTransaction.get(ii);
                String tada = "";
                if(Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC")){
                    tada = currentLocation.getTA_DA_mode();
                }else{
                    tada = "";
                }
                String location = "<location>" +
                        "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                        "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                        "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                        "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                        "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                        "<TA_DA_MODE><![CDATA[" + tada + "]]></TA_DA_MODE>" +
                        "</location>";
                if (currentLocation.getTransId().startsWith("CH")) {
                    xmlData += "<checkout>";
                    xmlData += location;
                    ArrayList<YellowCard> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedYellowCard(currentLocation.getTransId());
                    for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {

                        ArrayList<Attendance> unUploadedAttendance = dataHelperObj.getUnuploadedAttendance(currentLocation.getTransId());
                        Attendance detailsObj = unUploadedAttendance.get(jj);
                        xmlData += "<checkout_data>"
                                + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                                + "<date><![CDATA[" + detailsObj.getDate() + "]]></date>" +
                                "</checkout_data>";
                    }
                    xmlData += "</checkout>";
                } else {
                    dataHelperObj.closeDatabase();
                }
            }
            xmlData += "</root>";
        }

        dataHelperObj.closeDatabase();
        return xmlData;
    }
}
