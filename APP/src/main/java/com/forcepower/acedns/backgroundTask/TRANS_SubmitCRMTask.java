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

public class TRANS_SubmitCRMTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    ArrayList<String> TransIdList;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format


    public TRANS_SubmitCRMTask(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        xmlData = prepareXMLData();
        if (finish)
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitCRMURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCRMTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCRMTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCRMTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {

        super.onPostExecute(result);

        if (finish)
            Utils.cancelProgressDialog();

        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            for (int ii = 0; ii < TransIdList.size(); ii++) {
                dataHelperObj.updateUnuploadedLocation(TransIdList.get(ii));
            }
            new TRANS_TravelFoodingLodgingAttachmentExportTask(mContext, "CALL_RECORD", result, finish).execute();
        } else {


            if (finish) {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000).show();
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
        dataHelperObj.closeDatabase();
    }


    public String prepareXMLData() {
        String xmlData = "";
        TransIdList = new ArrayList<>();
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransaction("", "");
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);

            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";
            if (currentLocation.getTransId().substring(0, 3).equalsIgnoreCase("CRM")) {
                xmlData += "<CRM_info>";
                xmlData += location;
                TransIdList.add(currentLocation.getTransId());
                String[] unUploadedTourExp = dataHelperObj.getUnuploadedCrmDetail(currentLocation.getTransId());

                xmlData += "<CRM_details>"
                        + "<call_id><![CDATA[" + unUploadedTourExp[0] + "]]></call_id>"
                        + "<customer_code><![CDATA[" + unUploadedTourExp[1] + "]]></customer_code>"
                        + "<call_duration><![CDATA[" + unUploadedTourExp[2] + "]]></call_duration>"
                        + "<recorded_file><![CDATA[" + unUploadedTourExp[3] + "]]></recorded_file>"
                        + "</CRM_details>";
                xmlData += "</CRM_info>";
            }
        }
        xmlData += "</root>";
        return xmlData;

    }
}
