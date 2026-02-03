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
import com.forcepower.acedns.util.UploadUnuploadedData;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.AceDnsWebServiceURL.submitTransactionURL;
import static com.forcepower.acedns.constants.AceDnsWebServiceURL.submitTransactionURLForScheme;

public class TRANS_LoginTimePendingOrder extends
        AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    String gitMasterUpdateTime = "2014-06-09 18:19:20";
    String loyaltyPurchaseUpdateTime = "2014-06-09 18:19:20";
    Boolean isUnSyncedNewCustomerPresent = false, IsUnuploadedRoutePlanExist = false;

    public TRANS_LoginTimePendingOrder(Context context, boolean finish) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
        gitMasterUpdateTime = mAceDnsTransactionDatabase.getlastDownloadTime("git_master");
        loyaltyPurchaseUpdateTime = mAceDnsTransactionDatabase.getlastDownloadTime("loyalty_purchase_details");
    }


    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        IsUnuploadedRoutePlanExist = mAceDnsTransactionDatabase.IsUnuploadedRoutePlanExist();
        int recordcount = mAceDnsTransactionDatabase.GetUnuploadedCustomerCount();
        if (recordcount > 0) {
            isUnSyncedNewCustomerPresent = true;
        }
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (isUnSyncedNewCustomerPresent) {

            String POST_result_customer = "";
            POST_result_customer = makeCustomerDataSendProcess();
            if (POST_result_customer.equalsIgnoreCase("1") || POST_result_customer.equalsIgnoreCase("2")) {

                mAceDnsTransactionDatabase.UpdateCustomerLocationData();
                mAceDnsTransactionDatabase.updateUnuploadedCustomer();
                POST_result = makeRouteplanAndOrderDataSubmitProcess(POST_result);

            }
        } else {
            POST_result = makeRouteplanAndOrderDataSubmitProcess(POST_result);
        }

        return POST_result;
    }

    private String makeRouteplanAndOrderDataSubmitProcess(String POST_result) {
        if (IsUnuploadedRoutePlanExist) {
            String POST_result_route_plan = "";
            POST_result_route_plan = makeRoutePlanDataSendingProcess();
            if (POST_result_route_plan.equalsIgnoreCase("1") || POST_result_route_plan.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.updateUnuploadedRoute();
                POST_result = orderSubmitTask(POST_result);
            }
        } else {
            POST_result = orderSubmitTask(POST_result);
        }
        return POST_result;
    }

    private String orderSubmitTask(String POST_result) {
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {

                String orderurl = submitTransactionURL;
                if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes")) {
                    orderurl = submitTransactionURLForScheme;
                }
                String uri = BaseUrl.baseUrl +
                        orderurl
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate
                        + "&last_git_master_update_time="
                        + gitMasterUpdateTime
                        + "&last_loyalty_purchase_update_time="
                        + loyaltyPurchaseUpdateTime;
                xmlData = UploadUnuploadedData.prepareXMLData(mContext);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);

            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder result: " +POST_result);
        return POST_result;
    }

    private String makeCustomerDataSendProcess() {
        String POST_result_customer = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {

                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNewCustomerURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                String xmlDataForNewCustomer = UploadUnuploadedData.PrepareXMLForNewCustomer(mContext);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder value: " +xmlDataForNewCustomer);
                POST_result_customer = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlDataForNewCustomer);
            } catch (Exception e) {
                POST_result_customer = "";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder result: " +POST_result_customer);
        return POST_result_customer;
    }

    private String makeRoutePlanDataSendingProcess() {
        String POST_result_route_plan = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                String xmlDataForNewRoute = prepareXMLDataRoute();
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitRouteURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder value: " +xmlDataForNewRoute);
                POST_result_route_plan = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlDataForNewRoute);
            } catch (Exception e) {
                POST_result_route_plan = "";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_LoginTimePendingOrder result: " +POST_result_route_plan);
        return POST_result_route_plan;
    }
    public String prepareXMLDataRoute() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        xmlData += "<route_plan>";
        ArrayList<RoutePlanMasterDetails> unUploadedRoute = mAceDnsTransactionDatabase.getUnuploadedRoute("");
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

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.length() == 1) {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")
                    || result.equalsIgnoreCase("4")
                    || result.equalsIgnoreCase("5")
                    || result.equalsIgnoreCase("7"))
            {
//                for(int i=0;i<unUploadedTransactionGlobal.size();i++)
//                {
//                    Location currentLocation = unUploadedTransactionGlobal.get(i);
//                    mAceDnsTransactionDatabase.UpdateLocationDataByTransId(currentLocation.getTransId());
//                }
                mAceDnsTransactionDatabase.UpdateLocationData();
                mAceDnsTransactionDatabase.updateUnUploadedHintRemarks("O");
                mAceDnsTransactionDatabase.updateUnuploadedOrderHeader();
                mAceDnsTransactionDatabase.updateUnuploadedOrderDetails();
                mAceDnsTransactionDatabase.updateUnuploadedPaymentHeader();
                mAceDnsTransactionDatabase.updateUnuploadedPaymentDetails();
                mAceDnsTransactionDatabase.updateUnuploadedAttendance();
                mAceDnsTransactionDatabase.closeDatabase();
            } else {
                mAceDnsTransactionDatabase.closeDatabase();

            }
        }
        /* ==================================================== */

        else if (result.length() > 1) {
            String[] resultArray = result.split(",");// For STOCK IN DataRefresh
            if (resultArray.length > 1) {
                if (resultArray[0].equalsIgnoreCase("3")) {
                    mAceDnsTransactionDatabase.UpdateLocationData();
                    mAceDnsTransactionDatabase.updateUnUploadedHintRemarks("O");
                    mAceDnsTransactionDatabase.updateUnuploadedOrderHeader();
                    mAceDnsTransactionDatabase.updateUnuploadedOrderDetails();
                    mAceDnsTransactionDatabase.updateUnuploadedPaymentHeader();
                    mAceDnsTransactionDatabase.updateUnuploadedPaymentDetails();
                    mAceDnsTransactionDatabase.updateUnuploadedAttendance();
                    mAceDnsTransactionDatabase.closeDatabase();

					/*Constants.stockDataRefresh = true;
					Constants.stockSender = result;*/
                }
            }
        }
    }
}
