package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_OdometerJourneyTransactionTask extends AsyncTask<String, Void, String>{

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";


    //ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    //String attendenceOrCheckOut;
    public TRANS_OdometerJourneyTransactionTask(Context context)
    {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new  AceDnsTransactionDatabase(mContext);
        //this.unUploadedTransaction=unUploadedTransaction;
        //this.attendenceOrCheckOut=attendenceOrCheckOut;
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
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.attendanceCheckoutJourneyInfoTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_OdometerJourneyTransactionTask: " +uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_OdometerJourneyTransactionTask value: " +xmlData);
            POST_result=HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri,xmlData);
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_OdometerJourneyTransactionTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2"))
        {

        }
        mAceDnsTransactionDatabase.closeDatabase();
        Utils.showToast(mContext,"Submited");
    }


    public void prepareXMLData()
    {
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";

            String columnName="attendance_id";

            ArrayList<commonDatabaseHelper> unUploadedAttendance = mAceDnsTransactionDatabase.getOdometerJourney();
            for(int jj=0;jj<unUploadedAttendance.size();jj++)
            {
                commonDatabaseHelper detailsObj = unUploadedAttendance.get(jj);
                xmlData += "<att_checkout_info>"
                        + "<attendance_id><![CDATA[" + "" + "]]></attendance_id>"
                        + "<checkout_id><![CDATA["+ "" + "]]></checkout_id>"
                        + "<emp_code><![CDATA["+ Constants.employeeDetailObject.getEmpCode() + "]]></emp_code>"
                        + "<create_date><![CDATA["+ detailsObj.getItem3() + "]]></create_date>"
                        + "<att_starting_km><![CDATA["+ detailsObj.getItem4() + "]]></att_starting_km>"
                        + "<att_odometer><![CDATA["+ detailsObj.getItem5() + "]]></att_odometer>"
                        + "<att_necessary_items><![CDATA["+ "" + "]]></att_necessary_items>"
                        + "<att_activities><![CDATA["+ "" + "]]></att_activities>"
                        + "<checkout_ending_km><![CDATA["+ "" + "]]></checkout_ending_km>"
                        + "<checkout_odometer><![CDATA["+ "" + "]]></checkout_odometer>"
                        + "<vehicle_type><![CDATA["+ "" + "]]></vehicle_type>"
                        +"</att_checkout_info>";

            }


        xmlData += "</root>";

    }
}
