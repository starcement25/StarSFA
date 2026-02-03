package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.RoutePlanCustomer;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitRouteCustomerPlan extends AsyncTask<String, Void, String> {

    public String str_getStatus;
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitRouteCustomerPlan(Context context, boolean finish, String status) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.str_getStatus = status;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        if (str_getStatus.equals("SUBMIT")) {
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
        }

    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNewRouteCustURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                xmlData = prepareXMLData();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRouteCustomerPlan: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRouteCustomerPlan value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRouteCustomerPlan result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (str_getStatus.equals("SUBMIT")) {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                Utils.cancelProgressDialog();
                dataHelperObj.updateUnuploadedRoute();
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
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                dataHelperObj.closeDatabase();
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
            dataHelperObj.closeDatabase();
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                dataHelperObj.updateUnuploadedRoute();
                dataHelperObj.closeDatabase();
            }
        }

    }


    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<RoutePlanMasterDetails> unUploadedDistinctRoute = dataHelperObj.getUnuploadedRoute();

        for (int countm = 0; countm < unUploadedDistinctRoute.size(); countm++) {
            String transid = unUploadedDistinctRoute.get(countm).getTranId();
            xmlData += "<route_plan>";
            ArrayList<RoutePlanMasterDetails> unUploadedRoute = dataHelperObj.getUnuploadedRoute(transid);
            for (int jj = 0; jj < unUploadedRoute.size(); jj++) {
                RoutePlanMasterDetails detailsObj = unUploadedRoute.get(jj);
                xmlData += "<routedata>";
                xmlData += "<route_plan_header>"
                        + "<route_plan_trans_id><![CDATA[" + detailsObj.getTranId() + "]]></route_plan_trans_id>"
                        + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                        + "<route_code><![CDATA[" + detailsObj.getRoutecode() + "]]></route_code>"
                        + "<route_name><![CDATA[" + detailsObj.getRouteName() + "]]></route_name>"
                        + "<visit_date><![CDATA[" + detailsObj.getVisitDate() + "]]></visit_date>"
                        + "<remarks><![CDATA[" + detailsObj.getRemarks() + "]]></remarks>"
                        + "<create_date><![CDATA[" + detailsObj.getCreateDate() + "]]></create_date>"
                        + "<status><![CDATA[" + detailsObj.getStatus() + "]]></status>"
                        + "</route_plan_header>";

                ArrayList<RoutePlanCustomer> unUploadedCustomer = dataHelperObj.getUnuploadedRouteCustomerPlan(detailsObj.getTranId(), detailsObj.getRoutecode());
                for (int count = 0; count < unUploadedCustomer.size(); count++) {
                    RoutePlanCustomer obj = unUploadedCustomer.get(count);
                    xmlData += "<route_plan_details>"
                            + "<route_plan_trans_id><![CDATA[" + obj.getTranSactionId() + "]]></route_plan_trans_id>"
                            + "<route_code><![CDATA[" + obj.getRouteCode() + "]]></route_code>"
                            + "<visit_date><![CDATA[" + obj.getVisitDate() + "]]></visit_date>"
                            + "<customer_code><![CDATA[" + obj.getCustomerCode() + "]]></customer_code>"
                            + "<status><![CDATA[" + obj.getStatus() + "]]></status>"
                            + "</route_plan_details>";
                }
                xmlData += "</routedata>";
            }
            xmlData += "</route_plan>";
        }
        xmlData += "</root>";
        return xmlData;
    }
}
