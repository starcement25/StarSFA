package com.forcepower.acedns.backgroundTask;

import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.doAmount;
import static com.forcepower.acedns.constants.Constants.doBargainNo;
import static com.forcepower.acedns.constants.Constants.doNo;

public class TRANS_GetDoNoFRomServerAfterSubmitDOTask extends AsyncTask<String, Void, String> {

    public String st_getStatus = "";
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_GetDoNoFRomServerAfterSubmitDOTask(Context context, boolean finish, String status) {
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
    protected String doInBackground(String... params)
    {
        String POST_result = "";
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("DO_no", doNo);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_GetDoNoFRomServerAfterSubmitDOTask: " +BaseUrl.baseUrl + AceDnsWebServiceURL.GETDoNOURL);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_GetDoNoFRomServerAfterSubmitDOTask value: " +values);
            POST_result = HttpCalling.httpGetCallWithXmlResponseAndTimeOutParam(BaseUrl.baseUrl + AceDnsWebServiceURL.GETDoNOURL, values,10,0,30);

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_GetDoNoFRomServerAfterSubmitDOTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        doNo=result;
        Utils.cancelProgressDialog();
        Intent intent = new Intent(mContext, MenuActivity.class);
        String doSuccessMessage=" Thanks for your DO against Bargain No. <font color='#D7B56D'>"+doBargainNo+"</font><br>Your DO Number is <font color='#D7B56D'>"+doNo+"</font> of<br><font color='#D7B56D'>"+doAmount+"</font>";
        if(doNo.equalsIgnoreCase("Network Failure"))
        {
            doSuccessMessage=" Bargain Failed... Please check your internet connection and try again.";
        }
        intent.putExtra("doSuccessMessage",doSuccessMessage);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        mContext.startActivity(intent);

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
