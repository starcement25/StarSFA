package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitNewRoute extends AsyncTask<String, Void, String> {

    public String str_Status = "";
    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitNewRoute(Context context, String status) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.str_Status = status;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (str_Status.equals("SUBMIT")) {
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
        }

    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNewRouteURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewRoute: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewRoute value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewRoute result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (str_Status.equals("SUBMIT")) {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                Utils.cancelProgressDialog();
                dataHelperObj.updateUnuploadedRouteMaster();
                dataHelperObj.closeDatabase();
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);

            } else {
                Utils.cancelProgressDialog();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                dataHelperObj.closeDatabase();
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);

            }
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.updateUnuploadedRouteMaster();
                dataHelperObj.closeDatabase();
            }
        }


    }


    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<RouteDetails> routeDetailsList = dataHelperObj.getUnuploadedRouteList();
        for (int jj = 0; jj < routeDetailsList.size(); jj++) {
            RouteDetails detailsObj = routeDetailsList.get(jj);
            xmlData += "<new_route>"
                    + "<route_code><![CDATA[" + detailsObj.getRouteCode() + "]]></route_code>"
                    + "<route_name><![CDATA[" + detailsObj.getRouteName() + "]]></route_name>"
                    + "<distributor_code><![CDATA[" + detailsObj.getDistributorCode() + "]]></distributor_code>"
                    + "</new_route>";
        }
        xmlData += "</root>";
        return xmlData;
    }
}
