package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_AttnendanceJourneyTransactionTask extends AsyncTask<String, Void, String>{
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";

    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    String attendenceOrCheckOut;
    public TRANS_AttnendanceJourneyTransactionTask(Context context, ArrayList<Location> unUploadedTransaction,String attendenceOrCheckOut)
    {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new  AceDnsTransactionDatabase(mContext);
        this.unUploadedTransaction=unUploadedTransaction;
        this.attendenceOrCheckOut=attendenceOrCheckOut;
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
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C"))
        {
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.attendanceCheckoutJourneyInfoTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();

            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_AttnendanceJourneyTransactionTask: " +uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_AttnendanceJourneyTransactionTask value: " +xmlData);

            POST_result=HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri,xmlData);
        }

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_AttnendanceJourneyTransactionTask result: " +POST_result);
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
            String columnName="attendance_id";
            if(attendenceOrCheckOut.matches("checkout"))
            {
                columnName="checkout_id";
            }
            ArrayList<commonDatabaseHelper> unUploadedAttendance = mAceDnsTransactionDatabase.getUnuploadedAttendanceJourney(currentLocation.getTransId(),columnName);
            for(int jj=0;jj<unUploadedAttendance.size();jj++)
            {
                commonDatabaseHelper detailsObj = unUploadedAttendance.get(jj);
                xmlData += "<att_checkout_info>"
                        + "<attendance_id><![CDATA[" + detailsObj.getItem0() + "]]></attendance_id>"
                        + "<checkout_id><![CDATA["+ detailsObj.getItem1() + "]]></checkout_id>"
                        + "<emp_code><![CDATA["+ detailsObj.getItem2() + "]]></emp_code>"
                        + "<create_date><![CDATA["+ detailsObj.getItem3() + "]]></create_date>"
                        + "<att_starting_km><![CDATA["+ detailsObj.getItem4() + "]]></att_starting_km>"
                        + "<att_odometer><![CDATA["+ detailsObj.getItem5() + "]]></att_odometer>"
                        + "<att_necessary_items><![CDATA["+ detailsObj.getItem6() + "]]></att_necessary_items>"
                        + "<att_activities><![CDATA["+ detailsObj.getItem7() + "]]></att_activities>"
                        + "<checkout_ending_km><![CDATA["+ detailsObj.getItem8() + "]]></checkout_ending_km>"
                        + "<checkout_odometer><![CDATA["+ detailsObj.getItem9() + "]]></checkout_odometer>"
                        + "<vehicle_type><![CDATA["+ detailsObj.getItem10() + "]]></vehicle_type>"
                        +"</att_checkout_info>";

            }

        }
        xmlData += "</root>";

    }
}
