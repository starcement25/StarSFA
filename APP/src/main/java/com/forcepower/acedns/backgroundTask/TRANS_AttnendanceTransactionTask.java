package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class TRANS_AttnendanceTransactionTask extends AsyncTask<String, Void, String>{

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";

    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_AttnendanceTransactionTask(Context context,ArrayList<Location> unUploadedTransaction)
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
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C"))
        {
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.attendanceTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_AttnendanceTransactionTask: " +uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_AttnendanceTransactionTask value: " +xmlData);
            POST_result= HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri,xmlData);
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_AttnendanceTransactionTask result: " +POST_result);

        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2"))
        {
            if((!Constants.menuDetailsObj.getattendance_journey_info().equalsIgnoreCase(null) || !Constants.menuDetailsObj.getattendance_journey_info().equalsIgnoreCase("null") || !Constants.menuDetailsObj.getattendance_journey_info().trim().equalsIgnoreCase("")) )
            {
                new TRANS_AttnendanceJourneyTransactionTask(mContext, unUploadedTransaction,"attendence").execute();
            }
            else{
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
                {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(),"trans_id","location");
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(),"trans_id","attendence");
                }
            }

            if(Constants.nickName.toUpperCase().matches("NIMBUS") || result.equalsIgnoreCase("1") ){
                Utils.showColorToast(mContext,"Submitted successfully");
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
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>"+
                    "<TA_DA_MODE><![CDATA[" + tada + "]]></TA_DA_MODE>" +
                    "</location>";

            xmlData += "<attendance>";
            xmlData += location;
            ArrayList<Attendance> unUploadedAttendance = mAceDnsTransactionDatabase.getUnuploadedAttendance(currentLocation.getTransId());
            for(int jj=0;jj<unUploadedAttendance.size();jj++)
            {
                Attendance detailsObj = unUploadedAttendance.get(jj);
                xmlData += "<attendancedata>"
                        + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                        + "<date><![CDATA["+ detailsObj.getDate() + "]]></date>" +
                        "</attendancedata>";
            }
            xmlData += "</attendance>";

        }
        xmlData += "</root>";
    }
}
