package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.QuotationDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

;

public class TRANS_SubmitQuotationDetailsTask extends AsyncTask<String, Void, String> {
    Context mContext;
    AceDnsDatabase dataHelperObj;
    String xmlData = "";
    boolean firstTimeTransaction = false;
    ProgressDialog mProgressDialog;
    ArrayList<String> DistinctTDVerificationId;

    public TRANS_SubmitQuotationDetailsTask(Context context, boolean firstTimeTransaction) {
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
        return quotationSubmitTask();
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1")) {
            dataHelperObj.UpdateLocationDataForQuotation();
            if (firstTimeTransaction) {
                mProgressDialog.dismiss();
                Utils.showToast(mContext, "Quotation submitted successfully");

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
            if (currentLocation.getTransId().startsWith("Q")) {
                xmlData += "<QUOTATION>";
                xmlData += location;
                ArrayList<QuotationDetails> unUploadedOrdrHeadr = dataHelperObj.getUnuploadedQuotationHeadr(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedOrdrHeadr.size(); jj++) {
                    QuotationDetails currentHeader = unUploadedOrdrHeadr.get(jj);

                    xmlData += "<QUOTATIONDATA>";
                    xmlData += "<QUOTATION_HEADER>"
                            + "<QUOTATION_NO><![CDATA[" + currentHeader.getQuotationNo() + "]]></QUOTATION_NO>"
                            + "<DATE><![CDATA[" + currentHeader.getQuotationDate() + "]]></DATE>"
                            + "<CUSTOMER_NAME><![CDATA[" + currentHeader.getQuotationForName() + "]]></CUSTOMER_NAME>"
                            + "<ADDRESS><![CDATA[" + currentHeader.getQuotationForAddress() + "]]></ADDRESS>"
                            + "<EMAIL><![CDATA[" + currentHeader.getQuotationForEmail() + "]]></EMAIL>"
                            + "<PHONE_NO><![CDATA[" + currentHeader.getQuotationForPhone() + "]]></PHONE_NO>"
                            + "<CONTACT_NAME><![CDATA[" + currentHeader.getQuotationContactPersonName() + "]]></CONTACT_NAME>"
                            + "<CONTACT_PHONE><![CDATA[" + currentHeader.getQuotationContactPersonPhone() + "]]></CONTACT_PHONE>"
                            + "<CONTACT_EMAIL><![CDATA[" + currentHeader.getQuotationContactPersonEmail() + "]]></CONTACT_EMAIL>"
                            + "<VALIDITY><![CDATA[" + currentHeader.getQuotationValidity() + "]]></VALIDITY>"
                            + "<TOTAL_AMOUNT><![CDATA[" + currentHeader.getQuotationProductAmount() + "]]></TOTAL_AMOUNT>"
                            + "</QUOTATION_HEADER>";

                    ArrayList<QuotationDetails> unUploadedOrdrDetails = dataHelperObj.getUnuploadedQuotationDetails(currentHeader.getQuotationNo());
                    for (int kk = 0; kk < unUploadedOrdrDetails.size(); kk++) {
                        QuotationDetails currentDetails = unUploadedOrdrDetails.get(kk);
                        xmlData += "<QUOTATION_DETAILS>"
                                + "<QUOTATION_NO><![CDATA[" + currentDetails.getQuotationNo() + "]]></QUOTATION_NO>"
                                + "<QTY><![CDATA[" + currentDetails.getQuotationQuantity() + "]]></QTY>"
                                + "<PRODUCT_ID><![CDATA[" + currentDetails.getQuotationProductCode() + "]]></PRODUCT_ID>"
                                + "<PRODUCT_DESC><![CDATA[" + currentDetails.getQuotationProductDesc() + "]]></PRODUCT_DESC>"
                                + "<UNIT_PRICE><![CDATA[" + currentDetails.getQuotationProductRate() + "]]></UNIT_PRICE>"
                                + "<TAXES><![CDATA[" + currentDetails.getQuotationProductTax() + "]]></TAXES>"
                                + "<AMOUNT><![CDATA[" + currentDetails.getQuotationProductAmount() + "]]></AMOUNT>"
                                + "</QUOTATION_DETAILS>";
                    }
                    xmlData += "</QUOTATIONDATA>";
                }
                xmlData += "</QUOTATION>";

            }
        }
        xmlData += "</root>";
        dataHelperObj.closeDatabase();
        return xmlData;
    }

    private String quotationSubmitTask() {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {
                xmlData = prepareXMLData(mContext);
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitQuotationTransactionURL
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitQuotationDetailsTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitQuotationDetailsTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);


            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitQuotationDetailsTask result: " +POST_result);
        return POST_result;
    }

}
