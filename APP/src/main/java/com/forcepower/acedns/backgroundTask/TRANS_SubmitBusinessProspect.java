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
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitBusinessProspect extends AsyncTask<String, Void, String> {

    public String str_getStatus = "", prefix = "";
    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xml = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitBusinessProspect(Context context, boolean finish, String status, String prefix) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.str_getStatus = status;
        this.prefix = prefix;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (prefix.matches("DC")) {
            xml = prepareXMLDataForNew();
        } else//if(prefix.matches("DE"))
        {
            xml = prepareXMLDataForExisting();
        }
        if (str_getStatus.equals("SUBMIT")) {
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
        }

    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String currentApi = "";
                if (prefix.matches("DC")) {
                    currentApi = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNewBusinessProspectURL;
                } else//if(prefix.matches("DE"))
                {
                    currentApi = BaseUrl.baseUrl + AceDnsWebServiceURL.submitExistingBusinessProspectURL;
                }

                String uri = currentApi + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitBusinessProspect: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitBusinessProspect value: " +xml);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xml);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitBusinessProspect result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (str_getStatus.equals("SUBMIT")) {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                Utils.cancelProgressDialog();
                dataHelperObj.UpdateLocationDataForBusinessProspect(prefix);
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
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.UpdateLocationDataForBusinessProspect(prefix);
                dataHelperObj.closeDatabase();
            }
        }


    }


    public String prepareXMLDataForNew() {
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
            if (currentLocation.getTransId().startsWith(prefix)) {
                ArrayList<ProspectCustHeader> unUploadedProspectCustHeader = dataHelperObj.getUnuploadedProspectCustHeadr(prefix, currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedProspectCustHeader.size(); jj++) {
                    ProspectCustHeader currentHeader = unUploadedProspectCustHeader.get(jj);
                    xmlData += "<prospective_customer>";
                    xmlData += location;
                    xmlData += "<prospective_customer_header>"
                            + "<trans_id><![CDATA[" + currentHeader.getTransId() + "]]></trans_id>"
                            + "<customer_code><![CDATA[" + currentHeader.getCode() + "]]></customer_code>"
                            + "<address><![CDATA[" + currentHeader.getAddress() + "]]></address>"
                            + "<pin><![CDATA[" + currentHeader.getPin() + "]]></pin>"
                            + "<area><![CDATA[" + currentHeader.getRouteCode() + "]]></area>"
                            + "<phone_no><![CDATA[" + currentHeader.getPhone() + "]]></phone_no>"
                            + "<remarks><![CDATA[" + currentHeader.getRemarks() + "]]></remarks>"
                            + "<tagged_customer_code><![CDATA[" + currentHeader.getTagCust() + "]]></tagged_customer_code>"
                            + "<cust_type><![CDATA[" + "R" + "]]></cust_type>"
                            + "<category_of_store><![CDATA[" + currentHeader.getCategory_of_store() + "]]></category_of_store>"
                            + "<check_in_time><![CDATA[" + currentHeader.getCheck_in_time() + "]]></check_in_time>"
                            + "<customer_name><![CDATA[" + currentHeader.getName() + "]]></customer_name>"
                            + "</prospective_customer_header>";
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


    public String prepareXMLDataForExisting() {
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
            if (currentLocation.getTransId().startsWith(prefix)) {
                ArrayList<ProspectCustHeader> unUploadedProspectCustHeader = dataHelperObj.getUnuploadedProspectCustHeadr(prefix, currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedProspectCustHeader.size(); jj++) {
                    ProspectCustHeader currentHeader = unUploadedProspectCustHeader.get(jj);
                    xmlData += "<prospective_customer>";
                    xmlData += location;
                    xmlData += "<prospective_customer_header>"
                            + "<trans_id><![CDATA[" + currentHeader.getTransId() + "]]></trans_id>"
                            + "<customer_code><![CDATA[" + currentHeader.getCode() + "]]></customer_code>"
                            + "<customer_name><![CDATA[" + currentHeader.getName() + "]]></customer_name>"
                            + "<check_in_time><![CDATA[" + currentHeader.getCheck_in_time() + "]]></check_in_time>"
                            + "<remarks><![CDATA[" + currentHeader.getRemarks() + "]]></remarks>"
                            + "</prospective_customer_header>";
                    xmlData += "</prospective_customer>";
                }
            }
        }
        xmlData += "</root>";
        return xmlData;
    }
}
