package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.StokistDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_Stokist_Visit_TransactionTask extends AsyncTask<String, Void, String>{

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";

    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_Stokist_Visit_TransactionTask(Context context, ArrayList<Location> unUploadedTransaction)
    {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new  AceDnsTransactionDatabase(mContext);
        this.unUploadedTransaction=unUploadedTransaction;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute()
    {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params)
    {
        prepareXMLData();
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext))
        {
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.stockistVisitTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Stokist_Visit_TransactionTask: " +uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Stokist_Visit_TransactionTask value: " +xmlData);
            POST_result=HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri,xmlData);
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Stokist_Visit_TransactionTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2"))
        {
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
                {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(),"trans_id","location");
                }

        }
        mAceDnsTransactionDatabase.closeDatabase();
    }


    public void prepareXMLData()
    {

        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        for(int ii=0;ii<unUploadedTransaction.size();ii++)
        {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>"+
                    "</location>";

            xmlData += "<stockist_visit>";
            xmlData += location;
            ArrayList<StokistDetails> unUploadedVisit = mAceDnsTransactionDatabase.getUnuploadedStokistVisit(currentLocation.getTransId());
            for(int jj=0;jj<unUploadedVisit.size();jj++)
            {
                StokistDetails detailsObj = unUploadedVisit.get(jj);
                xmlData += "<stockist_visit_details>"
                        + "<VISIT_TRANS_ID><![CDATA[" + detailsObj.getTransactionId() + "]]></VISIT_TRANS_ID>"
                        + "<stockist_code><![CDATA["+ detailsObj.getStokistCode() + "]]></stockist_code>"
                        + "<customer_code><![CDATA["+ detailsObj.getCustomerCode() + "]]></customer_code>"
                        + "<sale><![CDATA["+ detailsObj.getSale() + "]]></sale>"
                        + "<folder><![CDATA["+ detailsObj.getFolder() + "]]></folder>"
                        + "<prod_code><![CDATA["+ detailsObj.getProd_code() + "]]></prod_code>"+
                        "</stockist_visit_details>";
            }
            xmlData += "</stockist_visit>";

        }
        xmlData += "</root>";

    }
}
