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

public class TRANS_SubmitTravelExpenseTask extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    ArrayList<String> tourTransIdList;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitTravelExpenseTask(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        xmlData = prepareXMLData();
        Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {

                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitTourExpURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTravelExpenseTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTravelExpenseTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitTravelExpenseTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            Utils.cancelProgressDialog();
            for (int ii = 0; ii < tourTransIdList.size(); ii++) {
                dataHelperObj.updateUnuploadedTourExp(tourTransIdList.get(ii));
                dataHelperObj.updateUnuploadedLocation(tourTransIdList.get(ii));
            }
            new TRANS_TourAttachmentExportTask(mContext, "TRAVEL", result, finish).execute();
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
        tourTransIdList = new ArrayList<String>();
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
            if (currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("TT")) {
                xmlData += "<tour_expense>";
                xmlData += location;
                tourTransIdList.add(currentLocation.getTransId());
                String[] unUploadedTourExp = dataHelperObj.getUnuploadedTourExp(currentLocation.getTransId());
                xmlData += "<tour_expense_details>"
                        + "<tour_exp_trans_id><![CDATA[" + unUploadedTourExp[0] + "]]></tour_exp_trans_id>"
                        + "<emp_code><![CDATA[" + unUploadedTourExp[1] + "]]></emp_code>"
                        + "<start_destination><![CDATA[" + unUploadedTourExp[2] + "]]></start_destination>"
                        + "<end_destination><![CDATA[" + unUploadedTourExp[3] + "]]></end_destination>"
                        + "<fare><![CDATA[" + unUploadedTourExp[4] + "]]></fare>"
                        + "<tour_date><![CDATA[" + unUploadedTourExp[5] + "]]></tour_date>"
                        + "<transport_mode_cat_id><![CDATA[" + unUploadedTourExp[6] + "]]></transport_mode_cat_id>"
                        + "<transport_mode_sub_cat_id><![CDATA[" + unUploadedTourExp[7] + "]]></transport_mode_sub_cat_id>"
                        + "<distance><![CDATA[" + unUploadedTourExp[8] + "]]></distance>"
                        + "<attachment_id><![CDATA[" + unUploadedTourExp[10] + "]]></attachment_id>"
                        + "<supporting_attached><![CDATA[" + unUploadedTourExp[9] + "]]></supporting_attached>" +
                        "</tour_expense_details>";
                xmlData += "</tour_expense>";
            }
        }
        xmlData += "</root>";
        return xmlData;

    }
}
