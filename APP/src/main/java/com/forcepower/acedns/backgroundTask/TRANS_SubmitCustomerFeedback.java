package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.ProspectCustDetails;
import com.forcepower.acedns.bean.ProspectCustHeader;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitCustomerFeedback extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xml = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitCustomerFeedback(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        xml = prepareXMLData();
        Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNewBusinessProspectURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCustomerFeedback: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCustomerFeedback value: " +xml);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xml);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCustomerFeedback result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            Utils.cancelProgressDialog();
            dataHelperObj.updateUnuploadedProspectCustDetails();
            dataHelperObj.updateUnuploadedProspectCustHeader();
            dataHelperObj.closeDatabase();
            if (finish) {
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                } else {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        } else {
            Utils.cancelProgressDialog();
            dataHelperObj.closeDatabase();
            Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000).show();
            if (finish) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
    }


    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
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
            if (currentLocation.getTransId().startsWith("DC")) {
                ArrayList<ProspectCustHeader> unUploadedProspectCustHeader = dataHelperObj.getUnuploadedProspectCustHeadr("", currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedProspectCustHeader.size(); jj++) {
                    ProspectCustHeader currentHeader = unUploadedProspectCustHeader.get(jj);
                    xmlData += "<prospective_customer>";
                    xmlData += location;
                    xmlData += "<prospective_customer_header>"
                            + "<trans_id><![CDATA[" + currentHeader.getTransId() + "]]></trans_id>"
                            + "<address><![CDATA[" + currentHeader.getAddress() + "]]></address>"
                            + "<pin><![CDATA[" + currentHeader.getPin() + "]]></pin>"
                            + "<area><![CDATA[" + currentHeader.getRouteCode() + "]]></area>"
                            + "<area_name><![CDATA[" + currentHeader.getRouteName() + "]]></area_name>"
                            + "<phone_no><![CDATA[" + currentHeader.getPhone() + "]]></phone_no>"
                            + "<remarks><![CDATA[" + currentHeader.getRemarks() + "]]></remarks>"
                            + "<tagged_customer_code><![CDATA[" + currentHeader.getTagCust() + "]]></tagged_customer_code>"
                            + "<cust_type><![CDATA[" + currentHeader.getCustType() + "]]></cust_type>"
                            + "<customer_name><![CDATA[" + currentHeader.getName() + "]]></customer_name>" +
                            "</prospective_customer_header>";
                    ArrayList<ProspectCustDetails> unUploadedProspctCustDetails = dataHelperObj.getUnuploadedProspectCustDetails(currentHeader.getTransId());
                    for (int kk = 0; kk < unUploadedProspctCustDetails.size(); kk++) {
                        ProspectCustDetails currentDetails = unUploadedProspctCustDetails.get(kk);
                        xmlData += "<prospective_customer_details>"
                                + "<trans_id><![CDATA[" + currentDetails.getTransId() + "]]></trans_id>"
                                + "<product_code><![CDATA[" + currentDetails.getProdCode() + "]]></product_code>" +
                                "</prospective_customer_details>";
                    }
                    xmlData += "</prospective_customer>";
                }
            }
        }
        xmlData += "</root>";
        return xmlData;
    }
}
