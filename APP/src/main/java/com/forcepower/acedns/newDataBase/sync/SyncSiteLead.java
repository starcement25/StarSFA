package com.forcepower.acedns.newDataBase.sync;

import android.content.Context;
import android.util.Log;

import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;

import org.json.JSONObject;

import java.util.ArrayList;

import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class SyncSiteLead {
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    public SyncSiteLead(Context context){
        mNewDatabaseForSiteLead=new NewDatabaseForSiteLead(context);
    }
    public void uploadAllPendingSiteLead(){
        if(mNewDatabaseForSiteLead.isTableExists("new_site_lead_and_conversion_tracking")){
            ArrayList<DataForUpload> totalList=mNewDatabaseForSiteLead.getAllUnUploadedSiteLead();
            if(totalList.isEmpty()){
                Log.d("TAG", "uploadAllPendingSiteLead 11 : No data for upload");
            }else{
                for(int i=0;i<totalList.size();i++){
                    uploadData(totalList.get(i));
                }
            }
        }else{
            Log.d("TAG", "uploadAllPendingSiteLead 11 : No table found");
        }
    }
    private void uploadData(DataForUpload dataSet){
        new Thread(() -> {
            try {
                JSONObject obj = new JSONObject();
                obj.put("site_transaction_id", dataSet.getSite_transaction_id());
                obj.put("site_unique_id", dataSet.getSite_unique_id());
                obj.put("site_creation_date",dataSet.getSite_creation_date());
                obj.put("site_visit_date", dataSet.getSite_visit_date());
                obj.put("employee_code", dataSet.getEmployee_code());
                obj.put("employee_name", dataSet.getEmployee_name());
                obj.put("zone", dataSet.getZone());
                obj.put("branch", dataSet.getBranch());
                obj.put("state", dataSet.getState());
                obj.put("district",  dataSet.getDistrict());
                obj.put("latitude",  dataSet.getLatitude());
                obj.put("longitude", dataSet.getLongitude());
                obj.put("customer_name",  dataSet.getCustomer_name());
                obj.put("customer_contact_number", dataSet.getCustomer_contact_number());
                obj.put("customer_full_address", dataSet.getCustomer_full_address());
                obj.put("is_register_contractor", dataSet.getIs_register_contractor());
                obj.put("contractor_name", dataSet.getContractor_name());
                obj.put("contractor_contact_number", dataSet.getContractor_contact_number());
                obj.put("is_register_engineer", dataSet.getIs_register_engineer());
                obj.put("engineer_name", dataSet.getEngineer_name());
                obj.put("engineer_contact_number", dataSet.getEngineer_contact_number());
                obj.put("meeting_person",  dataSet.getMeeting_person());
                obj.put("decision_maker",  dataSet.getDecision_maker());
                obj.put("site_segment",  dataSet.getSite_segment());
                obj.put("visit_type",  dataSet.getVisit_type());
                obj.put("project_segment",  dataSet.getProject_segment());
                obj.put("type_of_construction",  dataSet.getType_of_construction());
                obj.put("floor_count",  dataSet.getFloor_count());
                obj.put("current_stage_of_construction",  dataSet.getCurrent_stage_of_construction());
                obj.put("built_up_area",  dataSet.getBuilt_up_area());
                obj.put("site_potential",  dataSet.getSite_potential());
                obj.put("consumed_till_date",  dataSet.getConsumed_till_date());
                obj.put("balance_potential",  dataSet.getBalance_potential());
                obj.put("balance_potential_manual",  dataSet.getBalance_potential_manual());
                obj.put("site_category",  dataSet.getSite_category());
                obj.put("brand_used",  dataSet.getBrand_used());
                obj.put("price_per_bag",  dataSet.getPrice_per_bag());
                obj.put("conversion",  dataSet.getConversion());
                obj.put("product_name",  dataSet.getProduct_name());
                obj.put("order_quantity",  dataSet.getOrder_quantity());
                obj.put("requested_date_of_delivery",  dataSet.getRequested_date_of_delivery());
                obj.put("counter_type",  dataSet.getCounter_type());
                obj.put("counter_name", dataSet.getCounter_name());
                obj.put("counter_code",  dataSet.getCounter_code());
                obj.put("reason_for_non_conversion", dataSet.getReason_for_non_conversion());
                obj.put("site_priority", dataSet.getSite_priority());
                obj.put("weather_shield_demo",  dataSet.getWeather_shield_demo());
                obj.put("approval_status",  dataSet.getApproval_status());
                obj.put("date_time",  dataSet.getDate_time());
                obj.put("asm_name",  dataSet.getAsm_name());
                obj.put("asm_employee_id",  dataSet.getAsm_employee_id());
                obj.put("actual_date_of_delivery",  dataSet.getActual_date_of_delivery());
                obj.put("delivery_remarks",  dataSet.getDelivery_remarks());
                obj.put("reason_for_not_delivery",  dataSet.getReason_for_not_delivery());
                obj.put("site_status",  dataSet.getSite_status());
                obj.put("remarks",  dataSet.getRemarks());

                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, obj.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/api_site_lead_form_submit.php")
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();

                if (response.body() != null) {
                    String responseString = response.body().string();
                    try {
                        Log.d("TAG", "uploadAllPendingSiteLead 0 : "+responseString);
                        JSONObject jsonObject = new JSONObject(responseString);
                        if (jsonObject.optString("process_status").equalsIgnoreCase("yes")) {
                            Log.d("TAG", "uploadAllPendingSiteLead 100: working fine");
                            int result=mNewDatabaseForSiteLead.updateSiteLead(dataSet.getSite_transaction_id(),dataSet.getSite_unique_id(),dataSet.getCustomer_contact_number());
                            if (result > 0) {
                                Log.d("DB", "uploadAllPendingSiteLead Updated successfully");
                            } else {
                                Log.d("DB", "uploadAllPendingSiteLead No matching record found");
                            }
                        }
                    } catch (Exception e) {
                        Log.d("TAG", "uploadAllPendingSiteLead 1 : "+e.getMessage());
                    }
                }
            } catch (Exception e) {
                Log.d("TAG", "uploadAllPendingSiteLead 2 : "+e.getMessage());
            }
        }).start();
    }
}
