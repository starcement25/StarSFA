package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.TelecallerList;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

public class TRANS_Telecaller_Form_TransactionTask extends AsyncTask<String, Void, String>{

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";

    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_Telecaller_Form_TransactionTask(Context context, ArrayList<Location> unUploadedTransaction)
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
        Utils.showProgressDialog(mContext, "Uploading Data.Please wait.");
        //xmlData = prepareXMLData();
    }

    @Override
    protected String doInBackground(String... params)
    {
        //prepareXMLData();
        prepareXMLData();
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext))
        {
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitTelecallerFormURL + "?nick_name=" + Constants.nickName;// + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();

            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Telecaller_Form_TransactionTask: " +uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Telecaller_Form_TransactionTask value: " +xmlData);

            POST_result=HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(uri,xmlData);
        }

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Telecaller_Form_TransactionTask result: " +POST_result);
        return POST_result;

    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        String process = "";
        try {

            JSONObject jsonObject = new JSONObject(result);
            process = jsonObject.getString("process_status");
        } catch (JSONException e) {
            e.printStackTrace();
        }
        if (process.equalsIgnoreCase("YES") || result.equalsIgnoreCase("1"))
        {
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
                {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(),"trans_id","location");
                    //mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(),"trans_id","doctor_visit_details");
                }
            //Utils.cancelProgressDialog();
            Utils.showToast(mContext,"Data Seved");
        }else{
            //Utils.showToast(mContext,"Server error");
        }
        Utils.cancelProgressDialog();
        mAceDnsTransactionDatabase.closeDatabase();
    }

    public void prepareXMLData()
    {

        JSONObject json = new JSONObject();
        JSONObject productdata = new JSONObject();
        JSONObject customerdata = new JSONObject();
        JSONArray arrayProduct = new JSONArray();
        JSONArray arrayCust = new JSONArray();
        try
        {

        xmlData = "";
        for(int ii=0;ii<unUploadedTransaction.size();ii++)
        {
            Location currentLocation = unUploadedTransaction.get(ii);

            ArrayList<TelecallerList> unUploadedVisit = mAceDnsTransactionDatabase.getUnuploadedTelecallerForm(currentLocation.getTransId());
            for(int jj=0;jj<unUploadedVisit.size();jj++)
            {
                TelecallerList detailsObj = unUploadedVisit.get(jj);

                json.put("telecaller_form_id", detailsObj.getTelecaller_form_id());
                json.put("customer_name", detailsObj.getCustomer_name());
                json.put("mobile", detailsObj.getMobile());
                json.put("is_connected", detailsObj.getIs_connected());
                json.put("not_connected_reason", detailsObj.getNot_connected_reason());
                json.put("demo_existing_product", detailsObj.getDemo_existing_product());
                json.put("demo_achieved", detailsObj.getDemo_achieved());
                json.put("demo_appointment_datetime", detailsObj.getDemo_appointment_datetime());
                json.put("allocated_user", detailsObj.getAllocated_user());
                json.put("next_appointment_date_time", detailsObj.getNext_appointment_date_time());
                json.put("service_interest", detailsObj.getService_interest());
                json.put("service_type", detailsObj.getService_type());
                json.put("collected_amount", detailsObj.getCollected_amount());
                json.put("service_date", detailsObj.getService_date());
                json.put("remarks", detailsObj.getRemarks());
                json.put("update_by", detailsObj.getUpdate_by());
                json.put("demo_interested_product", detailsObj.getDemo_interested_product());


            }


        }
        }
        catch (Exception e)
        {
            e.printStackTrace();
            System.out.println("JSON could not be made");
        }
        xmlData = json.toString();
        Log.d("jsonTent", ""+json.toString());
    }
}
