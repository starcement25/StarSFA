package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.DemoForm;
import com.forcepower.acedns.bean.Location;
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

public class TRANS_Demo_Form_TransactionTask extends AsyncTask<String, Void, String>{

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";

    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_Demo_Form_TransactionTask(Context context, ArrayList<Location> unUploadedTransaction)
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
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitDemoFormURL + "?nick_name=" + Constants.nickName;// + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Demo_Form_TransactionTask: " +uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Demo_Form_TransactionTask value: " +xmlData);
            POST_result=HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(uri,xmlData);
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Demo_Form_TransactionTask result: " +POST_result);
        return POST_result;

    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        String process= "";
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
            Utils.showToast(mContext,"Data Saved");
        }else{
            //Utils.showToast(mContext,"Server error");
        }
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

            ArrayList<DemoForm> unUploadedVisit = mAceDnsTransactionDatabase.getUnuploadedDemoForm(currentLocation.getTransId());
            for(int jj=0;jj<unUploadedVisit.size();jj++)
            {
                DemoForm detailsObj = unUploadedVisit.get(jj);



                    json.put("demo_form_id", detailsObj.getDemo_form_id());
                    json.put("demo_latt", detailsObj.getDemo_latt());
                    json.put("demo_longi", detailsObj.getDemo_longi());
                    json.put("customer_name", detailsObj.getCustomer_name());
                    json.put("prod_interested", detailsObj.getProd_interested());
                    json.put("demo_achieved", detailsObj.getDemo_achieved());
                    json.put("future_appointment_date", detailsObj.getFuture_appointment_date());
                    json.put("demo_given_by", detailsObj.getDemo_given_by());
                    json.put("sales_achieved", detailsObj.getSales_achieved());
                    json.put("ask_details", detailsObj.getAsk_details());
                    json.put("full_payment", detailsObj.getFull_payment());
                    json.put("sale_model", detailsObj.getSale_model());
                    json.put("sale_price", detailsObj.getSale_price());
                    json.put("sale_exchange", detailsObj.getSale_exchange());
                    json.put("sale_payment_details", detailsObj.getSale_payment_details());
                json.put("booking_done", detailsObj.getBooking_done());
                json.put("booking_model", detailsObj.getBooking_model());
                json.put("booking_sale_price", detailsObj.getBooking_sale_price());
                json.put("booking_adv_amount", detailsObj.getBooking_adv_amount());

                json.put("booking_full_payment", detailsObj.getBooking_full_payment());
                json.put("balance_due", detailsObj.getBalance_due());

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
