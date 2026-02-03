package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.BillingInformationStockSummaryData;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.masterApiCallingFlag;
import static com.forcepower.acedns.constants.Constants.retailerappTransactionReason;

public class TRANS_SubmitRetailerStockOutTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsDatabase dataHelperObj;
    String xmlData = "";
    boolean firstTimeTransaction = false;
    ProgressDialog mProgressDialog;
    ArrayList<String> DistinctTDVerificationId;
    ArrayList<BillingInformationStockSummaryData> unUploadedOrdrHeadr;
    public TRANS_SubmitRetailerStockOutTask(Context context, boolean firstTimeTransaction) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsDatabase(mContext);
        this.firstTimeTransaction = firstTimeTransaction;
        DistinctTDVerificationId = new ArrayList<>();
        unUploadedOrdrHeadr=new ArrayList<>();
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        if (firstTimeTransaction) {
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Uploading data.Please wait..");
            mProgressDialog.setCancelable(false);
            mProgressDialog.show();
        }
    }

    @Override
    protected String doInBackground(String... params) {
        return StockInSubmitTask();
    }

    @Override
    protected void onPostExecute(String result)
    {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1")|| unUploadedOrdrHeadr.size()==0)
        {
            if (result.equalsIgnoreCase("1"))
            {
                dataHelperObj.UpdateLocationDataForStockOut();
            }

            if (firstTimeTransaction )
            {
                mProgressDialog.dismiss();
                if (result.equalsIgnoreCase("1"))
                {
                    Utils.showToast(mContext, "Transaction submitted successfully");
                }

                if(retailerappTransactionReason.equalsIgnoreCase("sendDataBeforeStockOut"))
                {

                    Constants.masterApiCallingFlagFromReport=false;
                    downloadStockOutRelatedData();
                }
                else if(retailerappTransactionReason.equalsIgnoreCase("sendDataBeforeReport"))
                {

                    Constants.masterApiCallingFlagFromReport=true;
                    downloadStockOutRelatedData();

                }

                else if(retailerappTransactionReason.equalsIgnoreCase("sendDataAfterTransaction"))
                {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                            | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        }
        else
        {
            if (firstTimeTransaction)
            {
                mProgressDialog.dismiss();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                        | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
    }

    private void downloadStockOutRelatedData() {
        masterApiCallingFlag = false;
        Utils.showProgressDialog(mContext, "Updating stock data..");
        new Thread() {
            public void run()
            {
                new commonAsyncTaskMaster(mContext, "billing_information");
            }
        }.start();
    }

    public String prepareXMLData(Context mContext) {
        String xmlData = "";
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
            if (currentLocation.getTransId().startsWith("SO")) {
                xmlData += "<STOCK_OUT_DETAILS>";
                xmlData += location;

                unUploadedOrdrHeadr = dataHelperObj.getUnuploadedStockOutDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {
                    BillingInformationStockSummaryData currentHeader = unUploadedOrdrHeadr.get(jj);

//					xmlData += "<REQUISITION_DATA>";
                    xmlData += "<STOCK_OUT_DATA>"
                            + "<STOCK_OUT_ID><![CDATA[" + currentHeader.getsold_out_id() + "]]></STOCK_OUT_ID>"
                            + "<PROD_CODE><![CDATA[" + currentHeader.getProductCode() + "]]></PROD_CODE>"
                            + "<IMEI><![CDATA[" + currentHeader.getimei() + "]]></IMEI>"
                            + "<STOCK_OUT_DATE><![CDATA[" + currentHeader.getsold_out_date() + "]]></STOCK_OUT_DATE>"
                            + "<STOCK_OUT_QTY><![CDATA[" + currentHeader.getqty() + "]]></STOCK_OUT_QTY>"
                            + "<STOCK_OUT_CUSTOMER_CODE><![CDATA[" + currentHeader.getcustomerCode() + "]]></STOCK_OUT_CUSTOMER_CODE>"
                            + "</STOCK_OUT_DATA>";
                }
                xmlData += "</STOCK_OUT_DETAILS>";
            }


        }

        xmlData += "</root>";
        dataHelperObj.closeDatabase();
        return xmlData;
    }

    private String StockInSubmitTask() {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                xmlData = prepareXMLData(mContext);
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitStockOutDetailsTransactionURL
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRetailerStockOutTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRetailerStockOutTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);


            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRetailerStockOutTask result: " +POST_result);
        return POST_result;
    }

}
