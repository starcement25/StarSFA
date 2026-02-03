package com.forcepower.acedns.api.clients;


import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.Field;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Query;


public interface RestService {

    @GET("state-sku-wise-weightage-json.php")
    Call<String> weightageCalc(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("sis-region-level-wise-emp-data-json.php")
    Call<String> sis_region_level_wise_emp_data(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("sis-app-visibility-json.php")
    Call<String> sis_app_visibility(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("starsaathi_ledger_by_id.php")
    Call<String> starsaathi_ledger_by_id(@Query("the_id") String the_id);

    @GET("starsaathi_ledger_customer_list.php")
    Call<String> starsaathi_cust_name(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("customer-product-wise-stock-json.php")
    Call<String> customer_product_stock(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("prop_form_accessibility.php")
    Call<String> prop_form_accessibility(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("demo-require-customer-list.php")
    Call<String> prop_demo_form_data(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("telecaller-require-customer-list.php")
    Call<String> prop_tele_form_data(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("sis-summary-details-v1.php")
    Call<String> sis_summary_data(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

    @GET("customer_orientation_upload.php")
    Call<String> customer_orientation_upload(@Query("nick_name") String nick_name, @Query("emp_code") String emp_code);

}
