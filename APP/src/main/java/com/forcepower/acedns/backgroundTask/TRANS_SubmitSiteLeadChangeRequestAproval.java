package com.forcepower.acedns.backgroundTask;

import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.SiteLeadApproval;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitSiteLeadChangeRequestAproval extends AsyncTask<String, Void, String> {

    public String str_status = "";
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    ArrayList<SiteLeadApproval> selectedRouteList;

    public TRANS_SubmitSiteLeadChangeRequestAproval(Context context, boolean finish, String status, ArrayList<SiteLeadApproval> selectedRouteList) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.str_status = status;
        this.selectedRouteList = selectedRouteList;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (str_status.equals("SUBMIT")) {
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
        }

    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {

                ContentValues values = new ContentValues();
                values.put("nick_name", Constants.nickName);
                values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
                values.put("status", selectedRouteList.get(0).getStatus());
                values.put("survey_id", selectedRouteList.get(0).getTran_id());
                values.put("actual_date_delivery", selectedRouteList.get(0).getActual_delivery_date());
                values.put("delivery_remarks", selectedRouteList.get(0).getRemarks());
                values.put("reason_not_delivery", selectedRouteList.get(0).getReson());


                xmlData ="";// prepareXMLData();
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.operationdb_site_lead_change_approval + "?nick_name=" + Constants.nickName
                        + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                        + "&status=" + selectedRouteList.get(0).getStatus()
                        + "&survey_id=" + selectedRouteList.get(0).getTran_id();
                //POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlData);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSiteLeadChangeRequestAproval: " +BaseUrl.baseUrl + AceDnsWebServiceURL.operationdb_site_lead_change_approval);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSiteLeadChangeRequestAproval value: " +values);
                POST_result = HttpCalling.httpPostCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.operationdb_site_lead_change_approval, values);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSiteLeadChangeRequestAproval result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                mContext.startActivity(intent);

            } else {

            }

            Utils.cancelProgressDialog();


    }

}
