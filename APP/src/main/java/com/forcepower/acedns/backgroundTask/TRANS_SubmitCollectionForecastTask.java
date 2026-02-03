package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.MenuOutstandingParent;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitCollectionForecastTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsDatabase dataHelperObj;
    String xmlData = "";
    boolean firstTimeTransaction = false;
    ProgressDialog mProgressDialog;
    ArrayList<String> DistinctTDVerificationId;

    public TRANS_SubmitCollectionForecastTask(Context context, boolean firstTimeTransaction) {
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
            dataHelperObj.UpdateLocationDataForCollectionForecast();
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
				Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
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
            if (currentLocation.getTransId().startsWith("CF")) {
                xmlData += "<COLLECTION_FORECAST>";
                xmlData += location;
                ArrayList<MenuOutstandingParent> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedCollectionForecastDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {
                    MenuOutstandingParent currentHeader = unUploadedOrdrHeadr.get(jj);

                    xmlData += "<FORECAST_DETAILS>"
                            + "<FORECAST_ID><![CDATA[" + currentHeader.getForecastId() + "]]></FORECAST_ID>"
                            + "<CUSTOMER_CODE><![CDATA[" + currentHeader.getCustomerCode() + "]]></CUSTOMER_CODE>"
                            + "<FORECAST_DATE><![CDATA[" + currentHeader.getForecastDate() + "]]></FORECAST_DATE>"
                            + "<INVOICE_AMOUNT><![CDATA[" + currentHeader.getTotalInvoice() + "]]></INVOICE_AMOUNT>"
                            + "<AMOUNT_RECEIVED><![CDATA[" + currentHeader.getForecastAmount() + "]]></AMOUNT_RECEIVED>"
                            + "</FORECAST_DETAILS>";
                }
                xmlData += "</COLLECTION_FORECAST>";
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
                        AceDnsWebServiceURL.submitCollectionForecastTransactionURL
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCollectionForecastTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCollectionForecastTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);


            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCollectionForecastTask result: " +POST_result);
        return POST_result;
    }

}
