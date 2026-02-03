package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.StockAuditDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitStockAuditTask extends AsyncTask<String, Void, String> {

    public String st_getStatus = "";
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format


    public TRANS_SubmitStockAuditTask(Context context, boolean finish, String status) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
        this.st_getStatus = status;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (st_getStatus.equals("SUBMIT")) {
            Utils.showProgressDialog(mContext, "Uploading data. Please wait..");
        }

    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                prepareXMLData();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitStockAudittURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitStockAuditTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitStockAuditTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                Log.d("excp", ""+e.toString());
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitStockAuditTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (st_getStatus.equals("SUBMIT")) {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                Utils.cancelProgressDialog();
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
                }
                mAceDnsTransactionDatabase.updateUnUploadedHintRemarks("S");
                mAceDnsTransactionDatabase.updateUnuploadedStockAuditDetails();
                mAceDnsTransactionDatabase.closeDatabase();
                if (finish) {
                    if (result.equalsIgnoreCase("2")) {
                        Constants.dataResfresh = true;
                    }
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            } else {
                Utils.cancelProgressDialog();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                mAceDnsTransactionDatabase.closeDatabase();
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }

            }
        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
                }
                mAceDnsTransactionDatabase.updateUnUploadedHintRemarks("S");
                mAceDnsTransactionDatabase.updateUnuploadedStockAuditDetails();
            }
            mAceDnsTransactionDatabase.closeDatabase();
        }

    }


    public void prepareXMLData() {
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("STOCK", "");
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String tada = "";
            if(Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC")){
                tada = currentLocation.getTA_DA_mode();
            }else{
                tada = "";
            }
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "<TA_DA_MODE><![CDATA[" + tada + "]]></TA_DA_MODE>" +
                    "</location>";
            if (currentLocation.getTransId().substring(0, 1).equalsIgnoreCase("S") || currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("NS")) {
                xmlData += "<stock_audit>";
                xmlData += location;
                ArrayList<StockAuditDetails> unUploadedStockAudit = mAceDnsTransactionDatabase.getUnuploadedStockAuditDetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedStockAudit.size(); jj++) {
                    StockAuditDetails currentHeader = unUploadedStockAudit.get(jj);
                    String hintRemarks = mAceDnsTransactionDatabase.getUnuploadedHintRemarks(currentLocation.getTransId());
                    if (currentLocation.getTransId().substring(0, 1).equalsIgnoreCase("S")) {

                        if(Constants.weightage.toUpperCase().matches("YES")){
                            xmlData += "<stock_audit_details>"
                                    + "<transaction_id><![CDATA[" + currentHeader.getTransactionId() + "]]></transaction_id>"
                                    + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                    + "<product_code><![CDATA[" + currentHeader.getProductCode() + "]]></product_code>"
                                    + "<prod_mrp><![CDATA[" + currentHeader.getProductMrp() + "]]></prod_mrp>"
                                    + "<prod_details><![CDATA[" + currentHeader.getProductDetails() + "]]></prod_details>"
                                    + "<remarks><![CDATA[" + currentHeader.getRemarks() + "]]></remarks>"
                                    + "<HINT_REMARKS><![CDATA[" + hintRemarks + "]]></HINT_REMARKS>"
                                    + "<mfd_date><![CDATA[" + currentHeader.getmfgDate() + "]]></mfd_date>"
                                    + "<UOM><![CDATA[" + currentHeader.getuom() + "]]></UOM>"
                                    + "<quantity><![CDATA[" + currentHeader.getQuantity() + "]]></quantity>"
                                    + "<weightage><![CDATA["+ currentHeader.getWeightage() +"]]></weightage>"
                                    +
                                    "</stock_audit_details>";
                        }else {
                            xmlData += "<stock_audit_details>"
                                    + "<transaction_id><![CDATA[" + currentHeader.getTransactionId() + "]]></transaction_id>"
                                    + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                    + "<product_code><![CDATA[" + currentHeader.getProductCode() + "]]></product_code>"
                                    + "<prod_mrp><![CDATA[" + currentHeader.getProductMrp() + "]]></prod_mrp>"
                                    + "<prod_details><![CDATA[" + currentHeader.getProductDetails() + "]]></prod_details>"
                                    + "<remarks><![CDATA[" + currentHeader.getRemarks() + "]]></remarks>"
                                    + "<HINT_REMARKS><![CDATA[" + hintRemarks + "]]></HINT_REMARKS>"
                                    + "<mfd_date><![CDATA[" + currentHeader.getmfgDate() + "]]></mfd_date>"
                                    + "<UOM><![CDATA[" + currentHeader.getuom() + "]]></UOM>"
                                    + "<quantity><![CDATA[" + currentHeader.getQuantity() + "]]></quantity>"
                                    + "<weightage><![CDATA["+""+"]]></weightage>" +
                                    "</stock_audit_details>";
                        }
                    } else {
                        if(Constants.weightage.toUpperCase().matches("YES")) {
                            xmlData += "<stock_audit_details>"
                                    + "<transaction_id><![CDATA[" + currentHeader.getTransactionId() + "]]></transaction_id>"
                                    + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                    + "<remarks><![CDATA[" + currentHeader.getRemarks() + "]]></remarks>"
                                    + "<HINT_REMARKS><![CDATA[" + hintRemarks + "]]></HINT_REMARKS>"
                                    + "<mfd_date><![CDATA[" + currentHeader.getmfgDate() + "]]></mfd_date>"
                                    + "<quantity><![CDATA[" + "" + "]]></quantity>"
                                    + "<weightage><![CDATA[" + currentHeader.getWeightage() + "]]></weightage>"
                                    + "</stock_audit_details>";
                        }else{
                            xmlData += "<stock_audit_details>"
                                    + "<transaction_id><![CDATA[" + currentHeader.getTransactionId() + "]]></transaction_id>"
                                    + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                    + "<remarks><![CDATA[" + currentHeader.getRemarks() + "]]></remarks>"
                                    + "<HINT_REMARKS><![CDATA[" + hintRemarks + "]]></HINT_REMARKS>"
                                    + "<mfd_date><![CDATA[" + currentHeader.getmfgDate() + "]]></mfd_date>"
                                    + "<quantity><![CDATA[" + "" + "]]></quantity>"
                                    + "<weightage><![CDATA["+""+"]]></weightage>"
                                    + "</stock_audit_details>";
                        }
                    }
                }
                xmlData += "</stock_audit>";
            }
        }
        xmlData += "</root>";
    }
}
