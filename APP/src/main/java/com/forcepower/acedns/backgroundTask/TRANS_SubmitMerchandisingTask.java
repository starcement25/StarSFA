package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitMerchandisingTask extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false, isAttached = false;
    ArrayList<String> merchandisingList;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format


    public TRANS_SubmitMerchandisingTask(Context context, boolean finish, boolean isAttached) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.isAttached = isAttached;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                xmlData = prepareXMLData();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitMerchandisingTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitMerchandisingTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitMerchandisingTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitMerchandisingTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            Utils.cancelProgressDialog();
            for (int ii = 0; ii < merchandisingList.size(); ii++) {
                dataHelperObj.updateUnuploadedMerchandisingDetails(merchandisingList.get(ii));
                dataHelperObj.updateUnuploadedLocation(merchandisingList.get(ii));
            }
            if (isAttached) {
                new TRANS_TravelFoodingLodgingAttachmentExportTask(mContext, "MERCHANDISING", result, finish).execute();
            } else {
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        } else {
            Utils.cancelProgressDialog();
            Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
            if (finish) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
        dataHelperObj.closeDatabase();
    }


    public String prepareXMLData() {
        String xmlData = "";
        merchandisingList = new ArrayList<String>();
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedMerchandisingTransaction();
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);

            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";
            if (currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("MC")) {
                xmlData += "<merchandising_data>";
                xmlData += location;
                merchandisingList.add(currentLocation.getTransId());
                String[] unUploadedTourExp = dataHelperObj.getUnuploadedMerchandisingDetails(currentLocation.getTransId());
                xmlData += "<merchandising_details>"
                        + "<merchandising_id><![CDATA[" + unUploadedTourExp[0] + "]]></merchandising_id>"
                        + "<emp_code><![CDATA[" + unUploadedTourExp[1] + "]]></emp_code>"
                        + "<prod_code><![CDATA[" + unUploadedTourExp[2] + "]]></prod_code>"
                        + "<remarks><![CDATA[" + unUploadedTourExp[5] + "]]></remarks>"
                        + "<attachment_id><![CDATA[" + unUploadedTourExp[6] + "]]></attachment_id>"
                        + "<client_id><![CDATA[" + unUploadedTourExp[3] + "]]></client_id>"
                        + "<issue_status><![CDATA[" + unUploadedTourExp[7] + "]]></issue_status>"
                        + "<rectifying_issue_id><![CDATA[" + unUploadedTourExp[8] + "]]></rectifying_issue_id>"
                        + "<trans_type><![CDATA[" + unUploadedTourExp[4] + "]]></trans_type>" +
                        "</merchandising_details>";
                xmlData += "</merchandising_data>";
            }
        }
        xmlData += "</root>";
        return xmlData;

    }
}
