package com.forcepower.acedns.util;

import android.content.Context;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.api.clients.RestClient;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.TentFormDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class JsonsReceiver {

    public static void getStateWiseWeitage(Context mContext) {
        Utils.showProgressDialog(mContext, "Downloading Weightage data..");
        //Toast.makeText(mContext,"json",Toast.LENGTH_SHORT).show();
        Call<String> call = RestClient.getRestServiceString(mContext).weightageCalc(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                //Log.d("Response Cat :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {
                        //mAceDnsDatabase.setWeightageValue(jsonResult);
                        //insert(jsonResult,mContext);
                        AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(mContext);
                        boolean flg = aceDnsDatabase.setWeightageValue(jsonResult);
                        if(flg){
                            //Toast.makeText(mContext,"Weightage 1",Toast.LENGTH_SHORT).show();
                        }else{
                            Toast.makeText(mContext,"Weightage downloading error",Toast.LENGTH_SHORT).show();
                        }

                    }catch (Exception e){

                    }

                }
                Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                Utils.cancelProgressDialog();
            }
        });

    }


    public static void getSisEmpData(Context mContext) {
        Utils.showProgressDialog(mContext, "Downloding Sis Emp data..");
        //Toast.makeText(mContext,"json",Toast.LENGTH_SHORT).show();
        Call<String> call = RestClient.getRestServiceString(mContext).sis_region_level_wise_emp_data(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("Response Cat :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {
                        //mAceDnsDatabase.setWeightageValue(jsonResult);
                        //insert(jsonResult,mContext);
                        AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(mContext);
                        boolean flg = aceDnsDatabase.setSisEmpValue(jsonResult);
                       // Log.d("sis_val >", jsonResult + "");
                        if(flg){
                            //Toast.makeText(mContext,"Weightage 1",Toast.LENGTH_SHORT).show();
                        }else{
                            //Toast.makeText(mContext,"Sis Emp downloading error-",Toast.LENGTH_SHORT).show();
                        }

                    }catch (Exception e){

                    }

                }
                Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                Utils.cancelProgressDialog();
            }
        });

    }


    private static void insert(String js,Context mContext) {
        try {
            JSONObject obj = new JSONObject(js);
            obj.getString("process_status");

            if(obj.getString("process_status").equals("YES")){
               //String datavalue = obj.getString("datavalue");
                String row_count = obj.getString("countrows");
                JSONArray dataArray  = obj.getJSONArray("datavalue");
                if(dataArray.length() == Integer.parseInt(row_count)){
                    for (int i = 0; i < dataArray.length(); i++) {
                        JSONObject dataobj = dataArray.getJSONObject(i);

                        //Toast.makeText(mContext,"oum1 " +dataobj.getString("weightage_conversio1") ,Toast.LENGTH_SHORT).show();
                    }
                }else{
                    Toast.makeText(mContext,"Weightage downloading error",Toast.LENGTH_SHORT).show();
                }

            }

        }catch (JSONException e){

        }
    }


    public static void getSisAppVisibility(Context mContext) {
        Utils.showProgressDialog(mContext, "Downloding Sis App data..");
        //Toast.makeText(mContext,"json",Toast.LENGTH_SHORT).show();
        Call<String> call = RestClient.getRestServiceString(mContext).sis_app_visibility(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("Response :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {
                        //mAceDnsDatabase.setWeightageValue(jsonResult);
                        //insert(jsonResult,mContext);
                        AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(mContext);
                        boolean flg = aceDnsDatabase.setSisAppVisibilityValue(jsonResult);
                        if(flg){
                            //Toast.makeText(mContext,"Weightage 1",Toast.LENGTH_SHORT).show();
                        }else{
                            //Toast.makeText(mContext,"Sis Emp downloading error-",Toast.LENGTH_SHORT).show();
                        }

                    }catch (Exception e){

                    }

                }
                Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                Utils.cancelProgressDialog();
            }
        });

    }

    static boolean rflg = false;
    public static boolean getCustomerProductStock(Context mContext) {
        //Utils.showProgressDialog(mContext, "Downloding Customer Product Stock data..");
        Call<String> call = RestClient.getRestServiceString(mContext).customer_product_stock(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("Response :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {

                        boolean flg =  isDoneInsert(mContext,jsonResult);
                        try {
                            Thread.sleep(1000);
                        } catch (InterruptedException e) {
                            // TODO Auto-generated catch block
                            e.printStackTrace();
                        }
                        rflg = false;
                        if(flg){
                            rflg =true;
                            //Toast.makeText(mContext,""+jsonResult,Toast.LENGTH_SHORT).show();
                        }else{
                            Toast.makeText(mContext,"Customer product stock downloading error-",Toast.LENGTH_SHORT).show();
                        }

                    }catch (Exception e){

                    }

                }
                //Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                //Utils.cancelProgressDialog();
            }
        });
        return rflg;
    }

    public static boolean isDoneInsert(Context context,String jsonResult){
        AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(context);
        if(aceDnsDatabase.setCustomer_product_stockValue(jsonResult)){
            return true;
        }else {
            return false;
        }
    }

    public static boolean setProp_form_accessibility(Context mContext) {
        //Utils.showProgressDialog(mContext, "Downloding Customer Product Stock data..");
        Call<String> call = RestClient.getRestServiceString(mContext).prop_form_accessibility(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("Response :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {

                        boolean flg =  isDoneInsertProp(mContext,jsonResult);
                        Log.d("Propello :=>", jsonResult + "");
                        try {
                            Thread.sleep(1000);
                        } catch (InterruptedException e) {
                            // TODO Auto-generated catch block
                            e.printStackTrace();
                        }
                        rflg = false;
                        if(flg){
                            rflg =true;
                            //Toast.makeText(mContext,""+jsonResult,Toast.LENGTH_SHORT).show();
                        }else{
                            Toast.makeText(mContext,"Propello Accesibility downloading error-",Toast.LENGTH_SHORT).show();
                        }

                    }catch (Exception e){

                    }

                }
                //Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                //Utils.cancelProgressDialog();
            }
        });
        return rflg;
    }

    public static boolean isDoneInsertProp(Context context,String jsonResult){
        AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(context);
        if(aceDnsDatabase.setProp_form_accessibilityValue(jsonResult)){
            return true;
        }else {
            return false;
        }
    }

    public static boolean prop_demo_form_data(Context mContext) {
        //Utils.showProgressDialog(mContext, "Downloding Customer Product Stock data..");
        Call<String> call = RestClient.getRestServiceString(mContext).prop_demo_form_data(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("Response :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {

                        boolean flg = true;// isDoneInsertProp(mContext,jsonResult);
                        Log.d("PropelloDemoForm :=>", jsonResult + "");

                        if(!Constants.demoFormCust.isEmpty()){
                            Constants.demoFormCust.clear();
                        }

                        try {
                            JSONObject obj = new JSONObject(jsonResult);
                            obj.getString("process_status");

                            if(obj.getString("process_status").equals("YES")){
                                //String datavalue = obj.getString("datavalue");
                                String row_count = obj.getString("countrows");
                                JSONArray dataArray  = obj.getJSONArray("datavalue");
                                if(dataArray.length() == Integer.parseInt(row_count)){
                                    for (int i = 0; i < dataArray.length(); i++) {
                                        JSONObject dataobj = dataArray.getJSONObject(i);
                                        TentFormDetails td=new TentFormDetails();
                                        String pdata="";
                                        td.setCustomer_name(dataobj.getString("customer_name"));
                                        td.setMobile_no(dataobj.getString("mobile_no"));
                                        td.setDemo_tentative_date_time(dataobj.getString("demo_tentative_date_time"));
                                        td.setSource_type(dataobj.getString("source_type"));
                                        td.setEmp_name(dataobj.getString("emp_name"));
                                        if(!dataobj.getString("prod_data").toUpperCase().matches("NULL")) {
                                        JSONArray productArray  = dataobj.getJSONArray("prod_data");

                                        for (int ii = 0; ii < productArray.length(); ii++) {

                                                JSONObject pobj = productArray.getJSONObject(ii);
                                                if (ii == 0) {
                                                    pdata = "" + pdata + "" + pobj.getString("product");
                                                } else {
                                                    pdata = "" + pdata + "," + pobj.getString("product");
                                                }
                                            }
                                        }
                                        td.setProductInterested(pdata);
                                        Constants.demoFormCust.add(td);
                                        //Toast.makeText(mContext,"1 " +dataobj.getString("1") ,Toast.LENGTH_SHORT).show();
                                    }
                                }else{
                                    Toast.makeText(mContext,"customer_product_stock downloading error",Toast.LENGTH_SHORT).show();
                                }

                            }

                        }catch (JSONException e){
                            Log.d("PropelloDemoForm ex", e.toString() + "");
                        } finally {

                        }

                        try {
                            Thread.sleep(100);
                        } catch (InterruptedException e) {
                            // TODO Auto-generated catch block
                            e.printStackTrace();
                        }
                        rflg = false;
                        if(flg){
                            rflg =true;
                            //Toast.makeText(mContext,""+jsonResult,Toast.LENGTH_SHORT).show();
                        }else{
                            Toast.makeText(mContext,"Propello Accesibility downloading error-",Toast.LENGTH_SHORT).show();
                        }

                    }catch (Exception e){

                    }

                }
                //Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                //Utils.cancelProgressDialog();
            }
        });
        return rflg;
    }

    public static boolean prop_telecaller_form_data(Context mContext) {
        //Utils.showProgressDialog(mContext, "Downloding Customer Product Stock data..");
        Call<String> call = RestClient.getRestServiceString(mContext).prop_tele_form_data(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("PropelloTelecalerRes", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {

                        boolean flg = true;// isDoneInsertProp(mContext,jsonResult);
                        Log.d("PropelloTelecaler", jsonResult + "");

                        try {
                            JSONObject obj = new JSONObject(jsonResult);
                            obj.getString("process_status");

                            if(obj.getString("process_status").equals("YES")){
                                //String datavalue = obj.getString("datavalue");
                                String row_count = obj.getString("countrows");
                                JSONArray dataArray  = obj.getJSONArray("datavalue");
                                if(dataArray.length() == Integer.parseInt(row_count)){
                                    for (int i = 0; i < dataArray.length(); i++) {
                                        JSONObject dataobj = dataArray.getJSONObject(i);
                                        TentFormDetails td=new TentFormDetails();
                                        String pdata="";
                                        td.setCustomer_name(dataobj.getString("customer_name"));
                                        td.setMobile_no(dataobj.getString("mobile_no"));
                                        //td.setDemo_tentative_date_time(dataobj.getString("demo_tentative_date_time"));
                                        td.setProductInterested(dataobj.getString("prod_data"));
                                        td.setEmp_name(dataobj.getString("emp_name"));
                                        td.setEnd_date_time(dataobj.getString("date_time"));
                                        td.setSource_type(dataobj.getString("source_type"));


                                        Constants.telecallerFormCust.add(td);
                                        /*JSONArray productArray  = dataobj.getJSONArray("prod_data");
                                        for (int ii = 0; ii < productArray.length(); ii++) {
                                            JSONObject pobj = productArray.getJSONObject(i);
                                            if(ii==0){
                                                pdata=""+ pdata +"" + pobj.getString("product");
                                            }else {
                                                pdata = "" + pdata + "," + pobj.getString("product");
                                            }
                                        }*/

                                        //Toast.makeText(mContext,"1 " +dataobj.getString("1") ,Toast.LENGTH_SHORT).show();
                                    }
                                }else{
                                    Toast.makeText(mContext,"Telecaller data downloading error",Toast.LENGTH_SHORT).show();
                                }

                            }

                        }catch (JSONException e){
                            Log.d("PropelloDemoForm ex", e.toString() + "");
                        } finally {

                        }


                        try {
                            Thread.sleep(1000);
                        } catch (InterruptedException e) {
                            // TODO Auto-generated catch block
                            e.printStackTrace();
                        }
                        rflg = false;
                        if(flg){
                            rflg =true;
                            //Toast.makeText(mContext,""+jsonResult,Toast.LENGTH_SHORT).show();
                        }else{
                            Toast.makeText(mContext,"Propello Accesibility downloading error-",Toast.LENGTH_SHORT).show();
                        }

                    }catch (Exception e){

                    }

                }
                //Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                //Utils.cancelProgressDialog();
            }
        });
        return rflg;
    }

    public static boolean setSisSummary(Context mContext) {
        //Utils.showProgressDialog(mContext, "Downloding Customer Product Stock data..");
        Call<String> call = RestClient.getRestServiceString(mContext).sis_summary_data(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                //Log.d("Response :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {

                        boolean flg =  isDoneInsertSisSummary(mContext,jsonResult);
                        Log.d("sisSummary", jsonResult + "");

                        rflg = false;
                        if(flg){
                            rflg =true;
                            //Toast.makeText(mContext,""+jsonResult,Toast.LENGTH_SHORT).show();
                        }else{
                            //Toast.makeText(mContext,"Sis Summary downloading error-",Toast.LENGTH_SHORT).show();
                        }

                    }catch (Exception e){

                    }

                }
                //Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                //Utils.cancelProgressDialog();
            }
        });
        return rflg;
    }

    public static boolean isDoneInsertSisSummary(Context context,String jsonResult){
        AceDnsDatabase aceDnsDatabase = new AceDnsDatabase(context);
        if(aceDnsDatabase.setSisSummaryValue(jsonResult)){
            return true;
        }else {
            return false;
        }
    }



    public static ArrayList<CustomerDetails> starsaathi_ledger_customer_list(Context mContext) {
        ArrayList<CustomerDetails> detailList = new ArrayList<CustomerDetails>();
        //Utils.showProgressDialog(mContext, "Downloding Customer data..");
        Call<String> call = RestClient.getRestServiceString(mContext).starsaathi_cust_name(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("Response :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {
                        JSONObject obj = new JSONObject(jsonResult);
                        //obj.getString("process_status");
                        if(obj.getString("process_status").equals("YES")){
                            //String datavalue = obj.getString("datavalue");

                            JSONArray dataArray  = obj.getJSONArray("customer_data");

                                for (int i = 0; i < dataArray.length(); i++) {
                                    JSONObject dataobj = dataArray.getJSONObject(i);

                                    CustomerDetails detailsObj = new CustomerDetails();
                                    detailsObj.setCustomerCode(dataobj.getString("customer_code"));
                                    detailsObj.setCustomerName(dataobj.getString("customer_name"));
                                    detailsObj.setDnsCustCode(dataobj.getString("dns_customer_code"));
                                    detailList.add(detailsObj);
                                    //Toast.makeText(mContext,"1 " +dataobj.getString("1") ,Toast.LENGTH_SHORT).show();
                                }


                        }

                    }catch (JSONException e){
                        Log.d("sis_summary_error", e.toString() + "");
                    } finally {

                    }

                }
                //Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                //Utils.cancelProgressDialog();
            }
        });
        return detailList;
    }


    public static void getStarSaathiLedgerCust(Context mContext) {
        Utils.showProgressDialog(mContext, "Downloding data..");
        //Toast.makeText(mContext,"json",Toast.LENGTH_SHORT).show();
        Call<String> call = RestClient.getRestServiceString(mContext).starsaathi_cust_name(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                // Log.d("LD :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {

                        //setValue(jsonResult);

                    }catch (Exception e){

                    }

                }
                Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                Utils.cancelProgressDialog();
            }
        });

    }


}
