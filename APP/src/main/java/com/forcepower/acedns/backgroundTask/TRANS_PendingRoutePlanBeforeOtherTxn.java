package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

/**
 * Created by Admin on 25-02-2017.
 */

public class TRANS_PendingRoutePlanBeforeOtherTxn extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    String type = "";
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_PendingRoutePlanBeforeOtherTxn(Context context, String type) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.type = type;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                xmlData = prepareXMLData();
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitRouteURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_PendingRoutePlanBeforeOtherTxn: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_PendingRoutePlanBeforeOtherTxn value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_PendingRoutePlanBeforeOtherTxn result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        Utils.cancelProgressDialog();
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
            dataHelperObj.updateUnuploadedRoute();
            dataHelperObj.closeDatabase();
            if (result.equalsIgnoreCase("2")) {
                Constants.dataResfresh = true;
            }
            if (type.equalsIgnoreCase("ORDER") || type.equalsIgnoreCase("COLLECTION")) {
                new TRANS_SubmitOrderTask(mContext, true).execute();
            } else if (type.equalsIgnoreCase("STOCK")) {
                new TRANS_SubmitStockAuditTask(mContext, true, "SUBMIT").execute();
            } else if (type.equalsIgnoreCase("YELLOW CARD")) {
                new TRANS_SubmitYellowCardTask(mContext, true).execute();
            }
        } else {
            Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
            dataHelperObj.closeDatabase();
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            mContext.startActivity(intent);
        }
    }


    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        xmlData += "<route_plan>";
        ArrayList<RoutePlanMasterDetails> unUploadedRoute = dataHelperObj.getUnuploadedRoute("");
        String wrkwth = "";
        for (int jj = 0; jj < unUploadedRoute.size(); jj++) {
            RoutePlanMasterDetails detailsObj = unUploadedRoute.get(jj);
            wrkwth = "";
            if(detailsObj.getWorkingWIth().toString().equals(" ")){
                wrkwth = "";
            }else {
                wrkwth = detailsObj.getWorkingWIth();
            }
            xmlData += "<route_plan_details>"
                    + "<route_plan_trans_id><![CDATA[" + detailsObj.getTranId() + "]]></route_plan_trans_id>"
                    + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                    + "<route_code><![CDATA[" + detailsObj.getRoutecode() + "]]></route_code>"
                    + "<route_name><![CDATA[" + detailsObj.getRouteName() + "]]></route_name>"
                    + "<visit_date><![CDATA[" + detailsObj.getVisitDate() + "]]></visit_date>"
                    + "<remarks><![CDATA[" + detailsObj.getRemarks() + "]]></remarks>"
                    + "<create_date><![CDATA[" + detailsObj.getCreateDate() + "]]></create_date>"
                    + "<status><![CDATA[" + detailsObj.getStatus() + "]]></status>"
                    + "<distributor_code><![CDATA[" + detailsObj.getDistributorCode() + "]]></distributor_code>"
                    + "<WORKING_WITH><![CDATA[" + wrkwth + "]]></WORKING_WITH>"
                    + "</route_plan_details>";
        }
        xmlData += "</route_plan>";
        xmlData += "</root>";
        return xmlData;
    }
}
