package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_SubmitDOTask extends AsyncTask<String, Void, String> {

    public String st_getStatus = "";
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_SubmitDOTask(Context context, boolean finish, String status) {
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
        if (HTTPUtils.isConnectionPossible(mContext) ) {
            try {
                prepareXMLData();
                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitDoURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitDOTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitDOTask value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitDOTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (st_getStatus.equals("SUBMIT"))
        {
            Utils.cancelProgressDialog();
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
//                Utils.cancelProgressDialog();
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
                }
                mAceDnsTransactionDatabase.closeDatabase();
                if (finish) {
                    if (result.equalsIgnoreCase("2")) {
                        Constants.dataResfresh = true;
                    }
                    new TRANS_GetDoNoFRomServerAfterSubmitDOTask(mContext, true, "SUBMIT").execute();
//                    Intent intent = new Intent(mContext, MenuActivity.class);
//                    String doSuccessMessage=" Thanks for your DO against Bargain No. <font color='#D7B56D'>"+doBargainNo+"</font><br>Your DO Number is <font color='#D7B56D'>"+doNo+"</font> of<br><font color='#D7B56D'>"+doAmount+"</font>";
//                    intent.putExtra("doSuccessMessage",doSuccessMessage);
//                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
//                    mContext.startActivity(intent);
                }
            } else {
//                Utils.cancelProgressDialog();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                mAceDnsTransactionDatabase.closeDatabase();
                if (finish) {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        }
        else
        {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2"))
            {
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
                {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId());
                }
            }
            mAceDnsTransactionDatabase.closeDatabase();
        }

    }


    public void prepareXMLData() {
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("DO_transaction", "");
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
        {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";
                xmlData += "<DO_TRANSACTION>";
                xmlData += location;
                ArrayList<SaudaDetails> unUploadedStockAudit = mAceDnsTransactionDatabase.getUnuploadedDODetails(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedStockAudit.size(); jj++)
                {
                    SaudaDetails currentHeader = unUploadedStockAudit.get(jj);
                        xmlData += "<DO_DETAILS>"
                                + "<SAUDA_NO><![CDATA[" + currentHeader.getSaudaNo() + "]]></SAUDA_NO>"
                                + "<customer_code><![CDATA[" + currentHeader.getCustomerCode() + "]]></customer_code>"
                                + "<DESTINATION><![CDATA[" + currentHeader.getdestinationCode() + "]]></DESTINATION>"
                                + "<DO_NO><![CDATA[" + currentHeader.getdono() + "]]></DO_NO>"
                                + "<SKU_CODE><![CDATA[" + currentHeader.getSkuCode() + "]]></SKU_CODE>"
                                + "<DO_QTY><![CDATA[" + currentHeader.getQuantity() + "]]></DO_QTY>"
                                + "<DO_RATE><![CDATA[" + currentHeader.getSaleRate() + "]]></DO_RATE>"
                                + "<DO_AMOUNT><![CDATA[" + currentHeader.getAmount() + "]]></DO_AMOUNT>"
                                + "<DO_DATE><![CDATA[" + currentHeader.getdate() + "]]></DO_DATE>"
                                + "<PO_NO><![CDATA[" + currentHeader.getpono() + "]]></PO_NO>"
                                + "<delivery_date ><![CDATA[" + currentHeader.getdate() + "]]></delivery_date >"
                                + "</DO_DETAILS>";
                }
                xmlData += "</DO_TRANSACTION>";

        }
        xmlData += "</root>";
    }
}
