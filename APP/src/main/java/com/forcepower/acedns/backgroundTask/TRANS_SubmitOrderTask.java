package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.UploadUnuploadedData;

public class TRANS_SubmitOrderTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    String xmlDataInvoiceInformation = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    String gitMasterUpdateTime = "2014-06-09 18:19:20";
    String loyaltyPurchaseUpdateTime = "2014-06-09 18:19:20";
    ProgressDialog pd;
    boolean UploadInvoiceInformation = false;
    String POST_result_for_invoice_info = "0";
    Boolean isUnSyncedNewCustomerPresent = false;

    public TRANS_SubmitOrderTask(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        gitMasterUpdateTime = dataHelperObj.getlastDownloadTime("git_master");
        loyaltyPurchaseUpdateTime = dataHelperObj
                .getlastDownloadTime("loyalty_purchase_details");
    }

    public TRANS_SubmitOrderTask(Context context, boolean finish, boolean UploadInvoiceInformation) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        gitMasterUpdateTime = dataHelperObj.getlastDownloadTime("git_master");
        loyaltyPurchaseUpdateTime = dataHelperObj
                .getlastDownloadTime("loyalty_purchase_details");
        this.UploadInvoiceInformation = UploadInvoiceInformation;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        pd = new ProgressDialog(mContext);
        pd.setMessage("Submitting transaction.Please wait");
        pd.setCancelable(false);
        pd.show();
        int recordcount = dataHelperObj.GetUnuploadedCustomerCount();
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
                dataHelperObj.UpdateCustomerLocationData();
                dataHelperObj.updateUnuploadedCustomer();
                POST_result = orderSubmitTask(POST_result);
            }
        } else {
            POST_result = orderSubmitTask(POST_result);
        }

        return POST_result;
    }

    private String makeCustomerDataSendProcess() {
        String POST_result_customer = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {

            try {
                String xmlDataForNewCustomer = UploadUnuploadedData.PrepareXMLForNewCustomer(mContext);
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNewCustomerURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask value: " +xmlData);
                POST_result_customer = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlDataForNewCustomer);
            } catch (Exception e) {
                POST_result_customer = "";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask result: " +POST_result_customer);
        return POST_result_customer;
    }

    private String orderSubmitTask(String POST_result) {
        if (HTTPUtils.isConnectionPossible(mContext) ) {
            try {
                xmlData = UploadUnuploadedData.prepareXMLData(mContext);
                String submitTransactionURLApi = AceDnsWebServiceURL.submitTransactionURL;
                if (Constants.orderFormDetailsObj.getInputScreenPriceValidation().equalsIgnoreCase("yes")) {
                    submitTransactionURLApi = AceDnsWebServiceURL.submitTransactionSpecialURL;
                }
                if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes")) {
                    submitTransactionURLApi = AceDnsWebServiceURL.submitTransactionURLForScheme;
                }

                String uri = BaseUrl.baseUrl + submitTransactionURLApi + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&last_git_master_update_time=" + gitMasterUpdateTime + "&last_loyalty_purchase_update_time=" + loyaltyPurchaseUpdateTime;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
                if (UploadInvoiceInformation) {
                    xmlDataInvoiceInformation = UploadUnuploadedData.prepareXMLDataForInvoiceInformation(mContext);
                    if (xmlDataInvoiceInformation != null) {
                        String uriInvoiceInfo = BaseUrl.baseUrl +
                                AceDnsWebServiceURL.submitInvoiceInformationURL
                                + "?nick_name=" + Constants.nickName
                                + "&emp_code="
                                + Constants.employeeDetailObject.getEmpCode();

                        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask: " +uri);
                        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask value: " +xmlData);
                        POST_result_for_invoice_info = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uriInvoiceInfo, xmlDataInvoiceInformation);
                    }


                }

            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask result: " +POST_result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitOrderTask result: " +POST_result_for_invoice_info);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        pd.cancel();
        if (UploadInvoiceInformation) {
            if (POST_result_for_invoice_info.matches("1")) {
                dataHelperObj.UpdateInVoiceInformationFlagTo1();
            }

        }
        int recordcount = dataHelperObj.GetEditedCustomerCount();
        if (result.length() == 1) {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")
                    || result.equalsIgnoreCase("4")
                    || result.equalsIgnoreCase("5")
                    || result.equalsIgnoreCase("7")) {
                // dataHelperObj.updateUnuploadedLocationData();
                dataHelperObj.UpdateLocationData();
                dataHelperObj.updateUnUploadedHintRemarks("O");
                dataHelperObj.updateUnuploadedOrderHeader();
                dataHelperObj.updateUnuploadedOrderDetails();
                dataHelperObj.updateUnuploadedPaymentHeader();
                dataHelperObj.updateUnuploadedPaymentDetails();
                dataHelperObj.updateUnuploadedAttendance();
                // dataHelperObj.updateCustomerFlag();
                dataHelperObj.closeDatabase();
                if (finish) {
                    if (result.equalsIgnoreCase("2")) {
                        Constants.dataResfresh = true;
                    } else if (result.equalsIgnoreCase("4")) {
                        Constants.loyaltyDataRefresh = true;
                    } else if (result.equalsIgnoreCase("5")) {
                        Constants.transactionDelete = true;
                    } else if (result.equalsIgnoreCase("7")) {
                        Constants.transDeleteAndRefresh = true;
                    }
                }

            } else {
                dataHelperObj.closeDatabase();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG)
                        .show();
            }
        }
        /* ==================================================== */

        else if (result.length() > 1) {
            String[] resultArray = result.split(",");// For STOCK IN DataRefresh
            if (resultArray.length > 1) {
                if (resultArray[0].equalsIgnoreCase("3")) {
                    dataHelperObj.UpdateLocationData();
                    dataHelperObj.updateUnUploadedHintRemarks("O");
                    dataHelperObj.updateUnuploadedOrderHeader();
                    dataHelperObj.updateUnuploadedOrderDetails();
                    dataHelperObj.updateUnuploadedPaymentHeader();
                    dataHelperObj.updateUnuploadedPaymentDetails();
                    dataHelperObj.updateUnuploadedAttendance();
                    // dataHelperObj.updateCustomerFlag();
                    dataHelperObj.closeDatabase();

                    Constants.stockDataRefresh = true;
                    Constants.stockSender = result;
                }
            } else {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG)
                        .show();
            }
        }
        /* ==================================================== */

        else {
            dataHelperObj.closeDatabase();
            if(!Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes"))
            {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
            }

        }
        /* ==================================================== */
        if (Constants.orderFormDetailsObj.getCustomerInfoCheck().equalsIgnoreCase("yes")
                && recordcount > 0) {
            new TRANS_SubmitEditedCustomer(mContext, true).execute();

        } else {
            if (finish) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                if (Constants.OrderTransactionTaskCalledFrom.equalsIgnoreCase("order"))
                    intent.putExtra("transactionType", "order");
                Constants.OrderTransactionTaskCalledFrom = "";
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }


    }

}
