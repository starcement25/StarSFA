package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitRetailerStockReallocationTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsDatabase dataHelperObj;
    String xmlData = "";
    boolean firstTimeTransaction = false;
    ProgressDialog mProgressDialog;
    ArrayList<String> DistinctTDVerificationId;

    public TRANS_SubmitRetailerStockReallocationTask(Context context, boolean firstTimeTransaction) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsDatabase(mContext);
        this.firstTimeTransaction = firstTimeTransaction;
        DistinctTDVerificationId = new ArrayList<>();
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
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1")) {
            dataHelperObj.UpdateLocationDataForStockReallocation();
            if (firstTimeTransaction) {
                mProgressDialog.dismiss();
                Utils.showToast(mContext, "Transaction sent to server successfully");

                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                        | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);

            }
        } else {
            if (firstTimeTransaction) {
                mProgressDialog.dismiss();
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK
                        | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
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
            if (currentLocation.getTransId().startsWith("RA")) {
                xmlData += "<STOCK_REALLOCATION>";
                xmlData += location;
                ArrayList<ProductMasterDetails> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedStockReallocationDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {
                    ProductMasterDetails currentHeader = unUploadedOrdrHeadr.get(jj);

                    xmlData += "<STOCK_REALLOCATION_DATA>"
                            + "<ALLOCATION_ID><![CDATA[" + currentHeader.getallocation_id() + "]]></ALLOCATION_ID>"
                            + "<CUSTOMER_CODE><![CDATA[" + currentHeader.getcustomerCode() + "]]></CUSTOMER_CODE>"
                            + "<PROD_CODE><![CDATA[" + currentHeader.getProdCode() + "]]></PROD_CODE>"
                            + "<QTY><![CDATA[" + currentHeader.getQty() + "]]></QTY>"
                            + "</STOCK_REALLOCATION_DATA>";
                }
                xmlData += "</STOCK_REALLOCATION>";
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
                        AceDnsWebServiceURL.submitStockReallocationTransactionURL
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRetailerStockReallocationTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRetailerStockReallocationTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);

            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitRetailerStockReallocationTask result: " +POST_result);
        return POST_result;
    }

}
