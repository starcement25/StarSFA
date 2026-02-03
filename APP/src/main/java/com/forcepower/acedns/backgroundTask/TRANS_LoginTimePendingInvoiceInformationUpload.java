package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.RoutePlanCustomer;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.UploadUnuploadedData;

import java.util.ArrayList;

public class TRANS_LoginTimePendingInvoiceInformationUpload extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_LoginTimePendingInvoiceInformationUpload(Context context) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";

        if (xmlData != null) {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    xmlData = UploadUnuploadedData.prepareXMLDataForInvoiceInformation(mContext);
                    String uri = BaseUrl.baseUrl +
                            AceDnsWebServiceURL.submitInvoiceInformationURL
                            + "?nick_name=" + Constants.nickName
                            + "&emp_code="
                            + Constants.employeeDetailObject.getEmpCode();
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingInvoiceInformationUpload: " +uri);
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingInvoiceInformationUpload value: " +xmlData);
                    POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
                } catch (Exception e) {
                    POST_result = "Network Failure";
                } finally {
                }
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingInvoiceInformationUpload result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1")) {
            dataHelperObj.UpdateInVoiceInformationFlagTo1();
            dataHelperObj.closeDatabase();
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
