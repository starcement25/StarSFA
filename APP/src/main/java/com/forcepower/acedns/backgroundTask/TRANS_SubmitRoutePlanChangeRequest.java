package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitRoutePlanChangeRequest extends AsyncTask<String, Void, String> {

    public String str_status = "";
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    ArrayList<RoutePlanMasterDetails> selectedRouteList;

    public TRANS_SubmitRoutePlanChangeRequest(Context context, boolean finish, String status,ArrayList<RoutePlanMasterDetails> selectedRouteList) {
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
                xmlData = prepareXMLData();
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitRoutePlanChangeRequestURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRoutePlanChangeRequest: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRoutePlanChangeRequest value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRoutePlanChangeRequest result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {


            } else {

            }

            Utils.cancelProgressDialog();


    }


    public String prepareXMLData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        xmlData += "<route_plan_change>";
        ArrayList<RoutePlanMasterDetails> unUploadedRoute = selectedRouteList;//dataHelperObj.getUnuploadedRoute("");
        String wrkwth = "";
        for (int jj = 0; jj < unUploadedRoute.size(); jj++) {
            RoutePlanMasterDetails detailsObj = unUploadedRoute.get(jj);

            xmlData += "<route_plan_details>"
                    + "<route_plan_trans_id><![CDATA[" + detailsObj.getTranId() + "]]></route_plan_trans_id>"
                    + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                    + "<route_code><![CDATA[" + detailsObj.getRoutecode() + "]]></route_code>"
                    + "<route_name><![CDATA[" + detailsObj.getRouteName() + "]]></route_name>"
                    + "<visit_date><![CDATA[" + detailsObj.getVisitDate() + "]]></visit_date>"
                    + "<remarks><![CDATA[" + detailsObj.getRemarks() + "]]></remarks>"
                    + "<create_date><![CDATA[" + detailsObj.getCreateDate() + "]]></create_date>"
                    + "</route_plan_details>";
        }
        xmlData += "</route_plan_change>";
        xmlData += "</root>";
        return xmlData;
    }
}
