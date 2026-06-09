package com.forcepower.acedns.new_activity.market_feedback.api_connect;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerCompetitorQuantityDataSet;
import com.forcepower.acedns.newDataBase.data_set.SBGFeedbackDataSet;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import org.json.JSONException;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

public class TRANS_CompetitorPotentialFeedbackTask extends AsyncTask<String, Void, String> {
    Context mContext;
    String xmlData = "";
    String universeType="";
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    private OnTaskCompleteListener listener;
    public interface OnTaskCompleteListener {
        void onSuccess();
        void onFailure(String error);
    }
    public TRANS_CompetitorPotentialFeedbackTask(Context context,String universeType, OnTaskCompleteListener listener) {
        this.mContext = context;
        this.listener = listener;
        this.universeType=universeType;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(context);
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected String doInBackground(String... params) {
        prepareXMLData();
        String POST_result = "";
        Log.d("TAG", "_DOWNLOAD_ doInBackground: "+xmlData);
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            String uri = "https://sfa.starcement.co.in/misreport/save_competitor_qty.php";
            POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
        }
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        Log.d("TAG", "_DOWNLOAD_ onPostExecute: "+result);
        try {
            JSONObject obj=new JSONObject(result);
            if(obj.getBoolean("status")){
                mNewDatabaseForSiteLead.deleteAllDataFromSbgFeedback();
                if (listener != null) listener.onSuccess();
            }else{
                if (listener != null) listener.onFailure("Status false");
            }
        } catch (JSONException e) {
            if (listener != null) listener.onFailure(e.getMessage());
        }
    }

    public void prepareXMLData() {
        ArrayList<SBGFeedbackDataSet> arr1 = mNewDatabaseForSiteLead.getAllSbgFeedbackWhereFlag1();

        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        xmlData += "<marketfeedback>";
        xmlData += "<universetype>" +
                "<![CDATA[" +universeType + "]]>" +
                "</universetype>";
        xmlData += "<details>";
        String value="SBG"+Constants.employeeDetailObject.getEmpCode();
        if(!arr1.isEmpty())
            value=value+arr1.get(0).getDateTime().replaceAll("-","").replaceAll(" ","").replaceAll(":","");
        else{
            String timestamp = new SimpleDateFormat("yyyyMMddHHmmss", Locale.getDefault())
                    .format(Calendar.getInstance().getTime());
            value=value+timestamp;
        }

        for (int ii = 0; ii < arr1.size(); ii++) {
            mNewDatabaseForSiteLead.updateCustomerCompetitorQuantity(arr1.get(ii).getCustomerCode(),arr1.get(ii).getCompetitorQuantityId(),arr1.get(ii).getQuantity());
            String a= "<item>"
                    + "<market_feedback_id><![CDATA[" +value + "]]></market_feedback_id>"
                    + "<customer_code><![CDATA[" + arr1.get(ii).getCustomerCode() + "]]></customer_code>"
                    + "<emp_code><![CDATA[" + Constants.employeeDetailObject.getEmpCode() + "]]></emp_code>"
                    + "<competitor_quantity_id><![CDATA[" + arr1.get(ii).getCompetitorQuantityId() + "]]></competitor_quantity_id>"
                    + "<qty><![CDATA[" + arr1.get(ii).getQuantity() + "]]></qty>"
                    + "<date_time><![CDATA[" + arr1.get(ii).getDateTime() + "]]></date_time>"
                    + "</item>";
            Log.d("TAG", "_DOWNLOAD_ : "+a);
            xmlData +=a;
        }
        xmlData += "</details>" + "</marketfeedback>";
        xmlData += "</root>";
    }
}
