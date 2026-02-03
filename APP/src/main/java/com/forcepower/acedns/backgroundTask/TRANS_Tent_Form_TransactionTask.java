package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.TentCust;
import com.forcepower.acedns.bean.TentFormDetails;
import com.forcepower.acedns.bean.TentProduct;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

public class TRANS_Tent_Form_TransactionTask extends AsyncTask<String, Void, String>{

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";

    ArrayList<Location> unUploadedTransaction;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public TRANS_Tent_Form_TransactionTask(Context context, ArrayList<Location> unUploadedTransaction)
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
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitTentFormURL + "?nick_name=" + Constants.nickName;// + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Tent_Form_TransactionTask: " +uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Tent_Form_TransactionTask value: " +xmlData);
            POST_result=HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(uri,xmlData);
        }

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_Tent_Form_TransactionTask result: " +POST_result);
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
        if (process.equalsIgnoreCase("YES") || result.equalsIgnoreCase("2"))
        {
                for (int ii = 0; ii < unUploadedTransaction.size(); ii++)
                {
                    mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(),"trans_id","location");
                    //mAceDnsTransactionDatabase.updateUnuploadedLocation(unUploadedTransaction.get(ii).getTransId(),"trans_id","doctor_visit_details");
                }

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
        String mobile="";
        for(int ii=0;ii<unUploadedTransaction.size();ii++)
        {
            Location currentLocation = unUploadedTransaction.get(ii);

            ArrayList<TentFormDetails> unUploadedVisit = mAceDnsTransactionDatabase.getUnuploadedTentForm(currentLocation.getTransId());
            for(int jj=0;jj<unUploadedVisit.size();jj++)
            {
                TentFormDetails detailsObj = unUploadedVisit.get(jj);



                    json.put("tent_form_id", detailsObj.getTent_form_id());
                    json.put("starting_date_time", detailsObj.getStarting_date_time());
                    json.put("end_date_time", detailsObj.getEnd_date_time());
                    json.put("starting_latt", detailsObj.getStarting_latt());
                    json.put("starting_longi", detailsObj.getStarting_longi());
                    json.put("end_latt", detailsObj.getEnd_latt());
                    json.put("end_longi", detailsObj.getEnd_logi());
                    json.put("starting_image", detailsObj.getStarting_image());
                    json.put("end_image", detailsObj.getEnd_image());
                    json.put("customer_name", detailsObj.getCustomer_name());
                    json.put("mobile_no", detailsObj.getMobile_no());
                    json.put("interested_for_demo", detailsObj.getInterested_for_demo());
                    json.put("demo_tentative_date_time", detailsObj.getDemo_tentative_date_time());
                    json.put("remarks", detailsObj.getRemarks());
                    //json.put("interested_for_demo", detailsObj.getInterested_for_demo());
                mobile = detailsObj.getMobile_no();

            }

            ArrayList<TentProduct> unUploadedPro = mAceDnsTransactionDatabase.getUnuploadedTentFormProduct(currentLocation.getTransId(),mobile);
            for(int iii=0;iii<unUploadedPro.size();iii++)
            {
                TentProduct detailsObjP = unUploadedPro.get(iii);
                productdata = new JSONObject();
                productdata.put("tent_form_id", ""+detailsObjP.getTent_form_id());
                productdata.put("product", ""+detailsObjP.getProduct());
                productdata.put("brand", ""+detailsObjP.getBrand());
                productdata.put("life_of_product", ""+detailsObjP.getLife_of_product());
                productdata.put("mobile_no", ""+detailsObjP.getMobile_no());

                arrayProduct.put(productdata);
            }

            String p ="";
            ArrayList<TentCust> unUploadedCus = mAceDnsTransactionDatabase.getUnuploadedTentFormCust(currentLocation.getTransId(),mobile);
            for(int iii=0;iii<unUploadedCus.size();iii++)
            {
                TentCust detailsObjP = unUploadedCus.get(iii);
                customerdata = new JSONObject();
                p = p + detailsObjP.getProduct_type() + ",";
                if((iii+1) == unUploadedCus.size()) {
                    customerdata.put("tent_form_id", "" + detailsObjP.getTent_form_id());
                    customerdata.put("customer_alternate_phone_no", "" + detailsObjP.getCustomer_alternate_phone_no());
                    customerdata.put("customer_address", "" + detailsObjP.getCustomer_address());
                    customerdata.put("customer_other_details", "" + detailsObjP.getCustomer_other_details());
                    customerdata.put("product_type", "" + p);
                    customerdata.put("mobile_no", "" + detailsObjP.getMobile_no());
                    arrayCust.put(customerdata);
                }

            }


            json.put("customerdata", arrayCust);
            json.put("productdata", arrayProduct);



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
