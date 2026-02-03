package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_CashDepositeReceiveTask extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    String xmlData = "";
    boolean finish = false;
    String xmlDataInvoiceInformation = "";
    String POST_result_for_invoice_info = "0";
    ArrayList<String> tourTransIdList;

    public TRANS_CashDepositeReceiveTask(Context context, boolean finish) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        if (finish)
            Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                xmlData = prepareXMLData();
                String url = BaseUrl.baseUrl + AceDnsWebServiceURL.submitCashReceiveDepositURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CashDepositeReceiveTask: " +url);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CashDepositeReceiveTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(url, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_CashDepositeReceiveTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {

            for (int ii = 0; ii < tourTransIdList.size(); ii++) {
                dataHelperObj.updateUnuploadedLocation(tourTransIdList.get(ii));
            }

            if (finish) {
                Utils.cancelProgressDialog();
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

            if (finish) {
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
        dataHelperObj.closeDatabase();
    }


    public String prepareXMLData() {
        String xmlData = "";
        tourTransIdList = new ArrayList<>();
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
                    "<purpose_of_visit><![CDATA[" + currentLocation.getDate() + "]]></purpose_of_visit>" +
                    "</location>";
            if (currentLocation.getTransId().substring(0, 3).equalsIgnoreCase("EXE") || currentLocation.getTransId().substring(0, 2).equalsIgnoreCase("CC") || currentLocation.getTransId().substring(0, 3).equalsIgnoreCase("CRE") || currentLocation.getTransId().substring(0, 3).equalsIgnoreCase("CPE")) {
                xmlData += "<CASH_DEPOSITRECEIVE>";
                xmlData += location;
                tourTransIdList.add(currentLocation.getTransId());
                String[] unUploadedTourExp = dataHelperObj.getUnuploadedCashReceivedDeposited(currentLocation.getTransId());
//				 xmlData += "<freight_expense_details>"
//				 xmlData += "<freight_expense_details>"
                xmlData += "<cash_deposit_recv_id><![CDATA[" + unUploadedTourExp[0] + "]]></cash_deposit_recv_id>"
                        + "<emp_code><![CDATA[" + unUploadedTourExp[1] + "]]></emp_code>"
                        + "<trans_type><![CDATA[" + unUploadedTourExp[2] + "]]></trans_type>"
                        + "<date><![CDATA[" + unUploadedTourExp[3] + "]]></date>"
                        + "<amount><![CDATA[" + unUploadedTourExp[4] + "]]></amount>"
                        + "<remarks><![CDATA[" + unUploadedTourExp[5] + "]]></remarks>";

//								 +"</freight_expense_details>";

                xmlData += "</CASH_DEPOSITRECEIVE>";
            }
        }
        xmlData += "</root>";
        return xmlData;

    }
}
