package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.content.ContentValues;
import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.Gravity;
import android.view.Window;
import android.view.WindowManager.LayoutParams;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.backgroundTask.AUTH_LoadEmployeeMasterData;
import com.forcepower.acedns.backgroundTask.DATA_ConfirmDownloadTask;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.parser.SurveyFormDetailsXMLParser;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;
import com.forcepower.acedns.util.commonAsyncTaskSETUP;

import java.io.File;
import java.util.ArrayList;
import java.util.concurrent.atomic.AtomicBoolean;

import static com.forcepower.acedns.activity.auth.LoginActivity.mEmployeeIdOrPhopneNumber;
import static com.forcepower.acedns.activity.auth.LoginActivity.mPassword;

import androidx.annotation.NonNull;

public class ActivityDownloadStatus extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    private static TextView mTextViewDownloadText = null;
    AceDnsDatabase dbHelper;
    Context mContext;
    String httpResponse = "";
    private Handler mPrepareSurveyHandler;
    private int mCount = 0;
    private String mDownLoadMenuname = "";
    private LinearLayout parent = null;
    private ArrayList<ImageView> mImageViewList = null;

    @SuppressLint("HandlerLeak")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_download_status);
        RegisterActivities.registerActivity(this);

        updateMenuList();

        parent = findViewById(R.id.linearLayoutParent);
        mTextViewDownloadText = findViewById(R.id.download);

        mContext = ActivityDownloadStatus.this;
        dbHelper = new AceDnsDatabase(mContext);
        mCount = 0;

        AceDnsDatabase setupDataHelperObj = new AceDnsDatabase(mContext);
        setupDataHelperObj.deleteNonIncrementalData();
        setupDataHelperObj.closeDatabase();
        Constants.isDownLoadComplete = true;
        deleteExistingFiles();
        DrawLayout();
        if (dbHelper.isEmpMasterLoginEmpty()) {
            Constants.downloadTableList.add("employee_master_login");
        }

        mPrepareSurveyHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                final int listcount = threadmsg.getData().getInt("JOBALLOCATE");
                ActivityDownloadStatus.this.runOnUiThread(() -> {
                    if (listcount == Constants.downloadTableList.size() - 1) {
                        if (Constants.isDownLoadComplete) {
                            SetSuccessImage(mDownLoadMenuname);
                        } else {
                            if (Constants.isCommpleteDownLoadComplete) {
                                Constants.isCommpleteDownLoadComplete = false;
                            }
                            Constants.isDownLoadComplete = true;
                            SetErrorImage(mDownLoadMenuname);
                            Constants.DownloadErrorMsg += "\n" + mDownLoadMenuname;
                        }
                        dbHelper = new AceDnsDatabase(mContext);
                        dbHelper.updateDictTimeInLogTable();
                        dbHelper.closeDatabase();
                        mCount = 0;
                        new DATA_ConfirmDownloadTask(mContext).execute();
                    } else {
                        if (Constants.isDownLoadComplete) {
                            SetSuccessImage(mDownLoadMenuname);
                            mCount += 1;
                            mDownLoadMenuname = Constants.downloadTableList.get(mCount);
                            Log.d("TAG", "run11111: sync time");
                            DownloadData(mCount, mDownLoadMenuname);
                        } else {
                            if (Constants.isCommpleteDownLoadComplete) {
                                Constants.isCommpleteDownLoadComplete = false;
                            }
                            Constants.isDownLoadComplete = true;
                            SetErrorImage(mDownLoadMenuname);
                            Constants.DownloadErrorMsg += "\n" + mDownLoadMenuname;
                            mCount += 1;
                            mDownLoadMenuname = Constants.downloadTableList.get(mCount);
                            Log.d("TAG", "run11111: sync time2");
                            DownloadData(mCount, mDownLoadMenuname);
                        }
                    }
                });
            }
        };
        Log.d("TAG", "run11111: sync time3");
        mDownLoadMenuname = Constants.downloadTableList.get(mCount);
        DownloadData(mCount, mDownLoadMenuname);
    }

    private void updateMenuList() {
        new Thread() {
            public void run() {
                _DOWNLOAD_survey_form_details();
            }
        }.start();
    }

    public void _DOWNLOAD_survey_form_details() {
        Log.d("TAG", "_DOWNLOAD_survey_form_details: Calling ");
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
        values.put("mode", "SETUP");
        values.put("incremental_download", "no");
        values.put("last_update_time", "1971-01-01?10:10:10");

        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.surveyFormDetailsURL, values);
        Log.d("TAG", "_DOWNLOAD_survey_form_details: " + BaseUrl.baseUrl + AceDnsWebServiceURL.surveyFormDetailsURL);
        Log.d("TAG", "_DOWNLOAD_survey_form_details: " + values);
        Log.d("TAG", "_DOWNLOAD_survey_form_details: " + httpResponse);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            SurveyFormDetailsXMLParser parser = new SurveyFormDetailsXMLParser(httpResponse);
            Constants.surveyFormDetailsObj = parser.getParsedData();
            Log.d("TAG", "_DOWNLOAD_survey_form_details: " + Constants.surveyFormDetailsObj);
            if (Constants.surveyFormDetailsObj != null) {
                try (AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(mContext)) {
                    mAceDnsDatabase.InsertToSurveyFormDetails(Constants.surveyFormDetailsObj);
                }
            }
        }
    }

    private void DrawLayout() {
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        params.gravity = Gravity.CENTER_VERTICAL;
        mImageViewList = new ArrayList<>();
        for (int count = 0; count < Constants.downloadTableList.size(); count++) {
            LinearLayout childlayout = new LinearLayout(this);
            childlayout.setLayoutParams(params);
            childlayout.setPadding(1, 1, 1, 3);
            childlayout.setOrientation(LinearLayout.HORIZONTAL);

            TextView mtext = new TextView(mContext);
            String text = SeTLoaderText(Constants.downloadTableList.get(count), 0);
            mtext.setText(text);
            mtext.setTextColor(Color.BLACK);
            mtext.setTypeface(null, Typeface.BOLD);

            ImageView mimageview = new ImageView(this);
            mimageview.setImageResource(R.drawable.red_ball);
            mimageview.setTag(Constants.downloadTableList.get(count));
            mImageViewList.add(mimageview);

            childlayout.addView(mimageview);
            childlayout.addView(mtext);
            parent.addView(childlayout);
        }
    }

    public void DownloadData(final int task, final String params) {
        SeTLoaderText(params, 1);
        new Thread() {
            public void run() {
                new commonAsyncTaskMaster(mContext, "stock_reallocation");
                if (params.equalsIgnoreCase("menu_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                }

                if (params.equalsIgnoreCase("employee_master_login")) {
                    new AUTH_LoadEmployeeMasterData(mContext, true).execute(mEmployeeIdOrPhopneNumber, mPassword);
                } else if (params.equalsIgnoreCase("user_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                } else if (params.equalsIgnoreCase("order_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                } else if (params.equalsIgnoreCase("product_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                } else if (params.equalsIgnoreCase("route_plan_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                } else if (params.equalsIgnoreCase("sauda_form_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                } else if (params.equalsIgnoreCase("self_appraisal_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                } else if (params.equalsIgnoreCase("survey_form_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                } else if (params.equalsIgnoreCase("market_feedback_details")) {
                    new commonAsyncTaskSETUP(mContext, params);
                } else if (params.equalsIgnoreCase("broker_master")) {
                    new commonAsyncTaskMaster(mContext, "broker_master");
                } else if (params.equalsIgnoreCase("branch_route_freight")) {
                    new commonAsyncTaskMaster(mContext, "branch_route_freight");
                } else if (params.equalsIgnoreCase("conversion_data")) {
                    new commonAsyncTaskMaster(mContext, "conversion_data");
                } else if (params.equalsIgnoreCase("customer_product_relation")) {
                    new commonAsyncTaskMaster(mContext, "customer_product_relation");
                } else if (params.equalsIgnoreCase("customer_product_info")) {
                    new commonAsyncTaskMaster(mContext, "customer_product_info");
                } else if (params.equalsIgnoreCase("bargain_transaction")) {
                    new commonAsyncTaskMaster(mContext, "bargain_transaction");
                } else if (params.equalsIgnoreCase("depot_cost")) {
                    new commonAsyncTaskMaster(mContext, "depot_cost");
                } else if (params.equalsIgnoreCase("primary_freight")) {
                    new commonAsyncTaskMaster(mContext, "primary_freight");
                } else if (params.equalsIgnoreCase("RA_route_freight")) {
                    new commonAsyncTaskMaster(mContext, "RA_route_freight");
                } else if (params.equalsIgnoreCase("load_distribution")) {
                    new commonAsyncTaskMaster(mContext, "load_distribution");
                } else if (params.equalsIgnoreCase("route_master")) {
                    new commonAsyncTaskMaster(mContext, "route_master");
                } else if (params.equalsIgnoreCase("route_master_crm")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("BOQ_master")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("BOQ_master")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("customer_proposed_product")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("gift_master")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("farmer_master")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("retailer-wise-target-ach")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("beatwise_TA_DA")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("bank_master")) {
                    new commonAsyncTaskMaster(mContext, "bank_master");
                } else if (params.equalsIgnoreCase("attendance_checkout_details")) {
                    Constants.masterApiCallingFlag = true;
                    new commonAsyncTaskMaster(mContext, "attendance_checkout_details");
                } else if (params.equalsIgnoreCase("customer_master")) {
                    new commonAsyncTaskMaster(mContext, "customer_master");
                } else if (params.equalsIgnoreCase("customer_master_crm")) {
                    new commonAsyncTaskMaster(mContext, "customer_master_crm");
                } else if (params.equalsIgnoreCase("customer_product_wise_msl")) {
                    new commonAsyncTaskMaster(mContext, "customer_product_wise_msl");
                } else if (params.equalsIgnoreCase("credit_limit")) {
                    new commonAsyncTaskMaster(mContext, "credit_limit");
                } else if (params.equalsIgnoreCase("outstanding_master")) {
                    new commonAsyncTaskMaster(mContext, "outstanding_master");
                } else if (params.equalsIgnoreCase("product_group_master")) {
                    new commonAsyncTaskMaster(mContext, "product_group_master");
                } else if (params.equalsIgnoreCase("product_sub_group_master")) {
                    new commonAsyncTaskMaster(mContext, "product_sub_group_master");
                } else if (params.equalsIgnoreCase("product_brand_master")) {
                    new commonAsyncTaskMaster(mContext, "product_brand_master");
                } else if (params.equalsIgnoreCase("product_master")) {
                    new commonAsyncTaskMaster(mContext, "product_master");
                } else if (params.equalsIgnoreCase("cat_subcat_brand_mapping")) {
                    new commonAsyncTaskMaster(mContext, "cat_subcat_brand_mapping");
                } else if (params.equalsIgnoreCase("cat_subcat_prod_mapping")) {
                    new commonAsyncTaskMaster(mContext, "cat_subcat_prod_mapping");
                } else if (params.equalsIgnoreCase("closing_stock")) {
                    new commonAsyncTaskMaster(mContext, "closing_stock");
                } else if (params.equalsIgnoreCase("mrp_master")) {
                    new commonAsyncTaskMaster(mContext, "mrp_master");
                } else if (params.equalsIgnoreCase("prev_stock_counting_master")) {
                    new commonAsyncTaskMaster(mContext, "prev_stock_counting_master");
                } else if (params.equalsIgnoreCase("prodqty_custclass_wise_TD")) {
                    new commonAsyncTaskMaster(mContext, "prodqty_custclass_wise_TD");
                } else if (params.equalsIgnoreCase("route_plan")) {
                    new commonAsyncTaskMaster(mContext, "route_plan");
                } else if (params.equalsIgnoreCase("travel_category")) {
                    new commonAsyncTaskMaster(mContext, "travel_category");
                } else if (params.equalsIgnoreCase("travel_sub_category")) {
                    new commonAsyncTaskMaster(mContext, "travel_sub_category");
                } else if (params.equalsIgnoreCase("state_district_town")) {
                    new commonAsyncTaskMaster(mContext, "state_district_town");
                } else if (params.equalsIgnoreCase("loyalty_customer")) {
                    new commonAsyncTaskMaster(mContext, "loyalty_customer");
                } else if (params.equalsIgnoreCase("scheme_details")) {
                    new commonAsyncTaskMaster(mContext, "scheme_details");
                } else if (params.equalsIgnoreCase("loyalty_purchase_details")) {
                    new commonAsyncTaskMaster(mContext, "loyalty_purchase_details");
                } else if (params.equalsIgnoreCase("redeeme_details")) {
                    new commonAsyncTaskMaster(mContext, "redeeme_details");
                } else if (params.equalsIgnoreCase("rds_master")) {
                    new commonAsyncTaskMaster(mContext, "rds_master");
                } else if (params.equalsIgnoreCase("emp_master")) {
                    new commonAsyncTaskMaster(mContext, "emp_master");
                } else if (params.equalsIgnoreCase("branch_master")) {
                    new commonAsyncTaskMaster(mContext, "branch_master");
                } else if (params.equalsIgnoreCase("honeycomb_cost")) {
                    new commonAsyncTaskMaster(mContext, "honeycomb_cost");
                } else if (params.equalsIgnoreCase("margin_cost")) {
                    new commonAsyncTaskMaster(mContext, "margin_cost");
                } else if (params.equalsIgnoreCase("vendor_master")) {
                    new commonAsyncTaskMaster(mContext, "vendor_master");
                } else if (params.equalsIgnoreCase("git_master")) {
                    new commonAsyncTaskMaster(mContext, "git_master");
                } else if (params.equalsIgnoreCase("mis_transaction_log")) {
                    new commonAsyncTaskMaster(mContext, "mis_transaction_log");
                } else if (params.equalsIgnoreCase("user_access")) {
                    new commonAsyncTaskMaster(mContext, "user_access");
                } else if (params.equalsIgnoreCase("sauda_allocation")) {
                    new commonAsyncTaskMaster(mContext, "sauda_allocation");
                } else if (params.equalsIgnoreCase("TD_allocation")) {
                    new commonAsyncTaskMaster(mContext, "TD_allocation");
                } else if (params.equalsIgnoreCase("customer_branch_relation")) {
                    new commonAsyncTaskMaster(mContext, "customer_branch_relation");
                } else if (params.equalsIgnoreCase("sample_master")) {
                    new commonAsyncTaskMaster(mContext, "sample_master");
                } else if (params.equalsIgnoreCase("survey_category_master")) {
                    new commonAsyncTaskMaster(mContext, "survey_category_master");
                } else if (params.equalsIgnoreCase("survey_input_details")) {
                    new commonAsyncTaskMaster(mContext, "survey_input_details");
                } else if (params.equalsIgnoreCase("generic_oil_master")) {
                    new commonAsyncTaskMaster(mContext, "generic_oil_master");
                } else if (params.equalsIgnoreCase("self_appraisal_emp_week_wise")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("van_stock_allocation")) {
                    new commonAsyncTaskMaster(mContext, "van_stock_allocation");
                } else if (params.equalsIgnoreCase("prospective_customer_master")) {
                    new commonAsyncTaskMaster(mContext, "prospective_customer_master");
                } else if (params.equalsIgnoreCase("menu_access")) {
                    new commonAsyncTaskMaster(mContext, "menu_access");
                } else if (params.equalsIgnoreCase("sauda_allocation_access")) {
                    new commonAsyncTaskMaster(mContext, "sauda_allocation_access");
                } else if (params.equalsIgnoreCase("TD_allocation_access")) {
                    new commonAsyncTaskMaster(mContext, "TD_allocation_access");
                } else if (params.equalsIgnoreCase("sauda_allocation_log")) {
                    new commonAsyncTaskMaster(mContext, "sauda_allocation_log");
                } else if (params.equalsIgnoreCase("street_master")) {
                    new commonAsyncTaskMaster(mContext, "street_master");
                } else if (params.equalsIgnoreCase("pending_contract")) {
                    new commonAsyncTaskMaster(mContext, "pending_contract");
                } else if (params.equalsIgnoreCase("mall_master")) {
                    new commonAsyncTaskMaster(mContext, "mall_master");
                } else if (params.equalsIgnoreCase("mall_survey_relation")) {
                    new commonAsyncTaskMaster(mContext, "mall_survey_relation");
                } else if (params.equalsIgnoreCase("sauda_transaction_log")) {
                    new commonAsyncTaskMaster(mContext, "sauda_transaction_log", true, "");
                } else if (params.equalsIgnoreCase("prev_order_counting_master")) {
                    new commonAsyncTaskMaster(mContext, "prev_order_counting_master");
                } else if (params.equalsIgnoreCase("order_status")) {
                    new commonAsyncTaskMaster(mContext, "order_status");
                } else if (params.equalsIgnoreCase("outstanding_ageing")) {
                    new commonAsyncTaskMaster(mContext, "outstanding_ageing");
                } else if (params.equalsIgnoreCase("branch_geo_fencing")) {
                    new commonAsyncTaskMaster(mContext, "branch_geo_fencing");
                } else if (params.equalsIgnoreCase("bargain_mrp")) {
                    new commonAsyncTaskMaster(mContext, "bargain_mrp");
                } else if (params.equalsIgnoreCase("sale_performance")) {
                    new commonAsyncTaskMaster(mContext, "sale_performance");
                } else if (params.equalsIgnoreCase("competitor_group_master")) {
                    new commonAsyncTaskMaster(mContext, "competitor_group_master");
                } else if (params.equalsIgnoreCase("destination_master")) {
                    new commonAsyncTaskMaster(mContext, "destination_master");
                } else if (params.equalsIgnoreCase("branchwise_scheme_PDF")) {
                    Constants.masterApiCallingFlag = true;
                    new commonAsyncTaskMaster(mContext, "branchwise_scheme_PDF");
                } else if (params.equalsIgnoreCase("golden_rules")) {
                    Constants.masterApiCallingFlag = true;
                    new commonAsyncTaskMaster(mContext, "golden_rules");
                } else if (params.equalsIgnoreCase("route_customer_plan")) {
                    new commonAsyncTaskMaster(mContext, "route_customer_plan");
                } else if (params.equalsIgnoreCase("survey_table_view")) {
                    new commonAsyncTaskMaster(mContext, "survey_table_view");
                } else if (params.equalsIgnoreCase("emp_menu_access")) {
                    new commonAsyncTaskMaster(mContext, "emp_menu_access");
                } else if (params.equalsIgnoreCase("survey_publish")) {
                    new commonAsyncTaskMaster(mContext, "survey_publish");
                } else if (params.equalsIgnoreCase("offer_publish")) {
                    new commonAsyncTaskMaster(mContext, "offer_publish");
                } else if (params.equalsIgnoreCase("mcx_rate")) {
                    new commonAsyncTaskMaster(mContext, "mcx_rate");
                } else if (params.equalsIgnoreCase("order_approval")) {
                    new commonAsyncTaskMaster(mContext, "order_approval");
                } else if (params.equalsIgnoreCase("branch_destination")) {
                    new commonAsyncTaskMaster(mContext, "branch_destination");
                } else if (params.equalsIgnoreCase("branch_dump")) {
                    new commonAsyncTaskMaster(mContext, "branch_dump");
                } else if (params.equalsIgnoreCase("customer_broker_relation")) {
                    new commonAsyncTaskMaster(mContext, "customer_broker_relation");
                } else if (params.equalsIgnoreCase("brokerage_cost")) {
                    new commonAsyncTaskMaster(mContext, "brokerage_cost");
                } else if (params.equalsIgnoreCase("fs_survey_publish")) {
                    new commonAsyncTaskMaster(mContext, "fs_survey_publish");
                } else if (params.equalsIgnoreCase("target_achievement")) {
                    new commonAsyncTaskMaster(mContext, "target_achievement");
                } else if (params.equalsIgnoreCase("self_appraisal_customer_wise")) {
                    new commonAsyncTaskMaster(mContext, "self_appraisal_customer_wise");
                } else if (params.equalsIgnoreCase("self_appraisal_emp_wise")) {
                    new commonAsyncTaskMaster(mContext, "self_appraisal_emp_wise");
                } else if (params.equalsIgnoreCase("TA_DA_limit")) {
                    new commonAsyncTaskMaster(mContext, "TA_DA_limit");
                } else if (params.equalsIgnoreCase("self_appraisal_branch_wise")) {
                    new commonAsyncTaskMaster(mContext, "self_appraisal_branch_wise");
                } else if (params.equalsIgnoreCase("self_appraisal_productgroup_wise")) {
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("state_master")) {
                    new commonAsyncTaskMaster(mContext, "state_master");
                } else if (params.equalsIgnoreCase("catalogue_info")) {
                    new commonAsyncTaskMaster(mContext, "catalogue_info");
                } else if (params.equalsIgnoreCase("facilitator_master")) {
                    new commonAsyncTaskMaster(mContext, "facilitator_master");
                } else if (params.equalsIgnoreCase("site_master")) {
                    new commonAsyncTaskMaster(mContext, "site_master");
                } else if (params.equalsIgnoreCase("customer_product_wise_orderplan")) {
                    new commonAsyncTaskMaster(mContext, "customer_product_wise_orderplan");
                } else if (params.equalsIgnoreCase("non_trade_customer_master")) {
                    new commonAsyncTaskMaster(mContext, "non_trade_customer_master");
                } else if (params.equalsIgnoreCase("distributor_route_relation")) {
                    new commonAsyncTaskMaster(mContext, "distributor_route_relation");
                } else if (params.equalsIgnoreCase("yellow-card-date-validation")) {
                    new commonAsyncTaskMaster(mContext, "yellow-card-date-validation");
                } else if (params.equalsIgnoreCase("yellow-card-date-validation_customerwise")) {
                    new commonAsyncTaskMaster(mContext, "yellow_card_date_validation_customerwise");
                } else if (params.equalsIgnoreCase("market_feedback_tagging")) {
                    new commonAsyncTaskMaster(mContext, "market_feedback_tagging");
                } else if (params.equalsIgnoreCase("scheme_master")) {
                    new commonAsyncTaskMaster(mContext, "scheme_master");
                } else if (params.equalsIgnoreCase("freebies_master")) {
                    new commonAsyncTaskMaster(mContext, "freebies_master");
                } else if (params.equalsIgnoreCase("billing_information")) {
                    Constants.masterApiCallingFlag = true;
                    new commonAsyncTaskMaster(mContext, "billing_information");
                } else if (params.equalsIgnoreCase("dealer_transaction")) {
                    new commonAsyncTaskMaster(mContext, "dealer_transaction");
                } else if (params.equalsIgnoreCase("stock_allocation")) {
                    Constants.masterApiCallingFlag = true;
                    new commonAsyncTaskMaster(mContext, "stock_allocation");
                } else if (params.equalsIgnoreCase("stock_balance_details")) {
                    Constants.masterApiCallingFlag = true;
                    new commonAsyncTaskMaster(mContext, params);
                } else if (params.equalsIgnoreCase("order_summary")) {
                    new commonAsyncTaskMaster(mContext, "order_summary");
                } else if (params.equalsIgnoreCase("emp_mtl_mapping")) {
                    new commonAsyncTaskMaster(mContext, "emp_mtl_mapping");
                }
                Message msg = mPrepareSurveyHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyHandler.sendMessage(msg);
            }
        }.start();
    }

    private void SetSuccessImage(String params) {
        for (int count = 0; count < mImageViewList.size(); count++) {
            String tag = (String) mImageViewList.get(count).getTag();
            if (tag.equalsIgnoreCase(params)) {
                mImageViewList.get(count).setImageResource(R.drawable.green_ball);
                mImageViewList.get(count).clearAnimation();
                mImageViewList.get(count).setFocusable(true);
            }
        }
    }

    private void SetErrorImage(String params) {
        for (int count = 0; count < mImageViewList.size(); count++) {
            String tag = (String) mImageViewList.get(count).getTag();
            if (tag.equalsIgnoreCase(params)) {
                mImageViewList.get(count).setImageResource(R.drawable.cross_ball);
                mImageViewList.get(count).clearAnimation();
                mImageViewList.get(count).setFocusable(true);
            }
        }
    }

    public void deleteExistingFiles() {
        File textFile;
        textFile = new File(Utils.getAppStoragePath(mContext) + "bank_master.txt");
        if (textFile.exists()) {
            new AtomicBoolean(textFile.delete());
        }
        File closingStkTxt = new File(Utils.getAppStoragePath(mContext) + "closing_stock.txt");
        if (closingStkTxt.exists()) {
            new AtomicBoolean(closingStkTxt.delete());
        }
        File customerTxt = new File(Utils.getAppStoragePath(mContext) + "customer_master.txt");
        if (customerTxt.exists()) {
            new AtomicBoolean(customerTxt.delete());
        }
        File customerCrmTxt = new File(Utils.getAppStoragePath(mContext) + "customer_master_crm.txt");
        if (customerCrmTxt.exists()) {
            new AtomicBoolean(customerCrmTxt.delete());
        }
        File crdtLimitTxt = new File(Utils.getAppStoragePath(mContext) + "credit_limit.txt");
        if (crdtLimitTxt.exists()) {
            new AtomicBoolean(crdtLimitTxt.delete());
        }
        File mrpTxt = new File(Utils.getAppStoragePath(mContext) + "mrp_master.txt");
        if (mrpTxt.exists()) {
            new AtomicBoolean(mrpTxt.delete());
        }
        File stockTxt = new File(Utils.getAppStoragePath(mContext) + "prev_stock_counting_master.txt");
        if (stockTxt.exists()) {
            new AtomicBoolean(stockTxt.delete());
        }
        File outstandingTxt = new File(Utils.getAppStoragePath(mContext) + "outstanding_master.txt");
        if (outstandingTxt.exists()) {
            new AtomicBoolean(outstandingTxt.delete());
        }
        File groupTxt = new File(Utils.getAppStoragePath(mContext) + "product_group_master.txt");
        if (groupTxt.exists()) {
            new AtomicBoolean(groupTxt.delete());
        }
        File subGrpTxt = new File(Utils.getAppStoragePath(mContext) + "product_sub_grp_master.txt");
        if (subGrpTxt.exists()) {
            new AtomicBoolean(subGrpTxt.delete());
        }
        File brandTxt = new File(Utils.getAppStoragePath(mContext) + "product_brand_master.txt");
        if (brandTxt.exists()) {
            new AtomicBoolean(brandTxt.delete());
        }
        File cat_subcat_brand_mapping = new File(Utils.getAppStoragePath(mContext) + "cat_subcat_brand_mapping.txt");
        if (cat_subcat_brand_mapping.exists()) {
            new AtomicBoolean(cat_subcat_brand_mapping.delete());
        }
        File cat_subcat_prod_mapping = new File(Utils.getAppStoragePath(mContext) + "cat_subcat_prod_mapping.txt");
        if (cat_subcat_prod_mapping.exists()) {
            new AtomicBoolean(cat_subcat_prod_mapping.delete());
        }
        File productTxt = new File(Utils.getAppStoragePath(mContext) + "product_master.txt");
        if (productTxt.exists()) {
            new AtomicBoolean(productTxt.delete());
        }
        File routeTxt = new File(Utils.getAppStoragePath(mContext) + "route_master.txt");
        if (routeTxt.exists()) {
            new AtomicBoolean(routeTxt.delete());
        }
        File routeTxtCrm = new File(Utils.getAppStoragePath(mContext) + "route_master_crm.txt");
        if (routeTxtCrm.exists()) {
            new AtomicBoolean(routeTxtCrm.delete());
        }
        File routePlanTxt = new File(Utils.getAppStoragePath(mContext) + "route_plan_master.txt");
        if (routePlanTxt.exists()) {
            new AtomicBoolean(routePlanTxt.delete());
        }
        File tourCatTxt = new File(Utils.getAppStoragePath(mContext) + "tour_category.txt");
        if (tourCatTxt.exists()) {
            new AtomicBoolean(tourCatTxt.delete());
        }
        File tourSubCatTxt = new File(Utils.getAppStoragePath(mContext) + "tour_sub_category.txt");
        if (tourSubCatTxt.exists()) {
            new AtomicBoolean(tourSubCatTxt.delete());
        }
        File tourOutletTxt = new File(Utils.getAppStoragePath(mContext) + "outlet_master.txt");
        if (tourOutletTxt.exists()) {
            new AtomicBoolean(tourOutletTxt.delete());
        }
        File loyaltyCustTxt = new File(Utils.getAppStoragePath(mContext) + "loyalty_customer.txt");
        if (loyaltyCustTxt.exists()) {
            new AtomicBoolean(loyaltyCustTxt.delete());
        }
        File rdsTxt = new File(Utils.getAppStoragePath(mContext) + "rds_master.txt");
        if (rdsTxt.exists()) {
            new AtomicBoolean(rdsTxt.delete());
        }
        File branchTxt = new File(Utils.getAppStoragePath(mContext) + "branch_master.txt");
        if (branchTxt.exists()) {
            new AtomicBoolean(branchTxt.delete());
        }
        File empTxt = new File(Utils.getAppStoragePath(mContext) + "emp_master.txt");
        if (empTxt.exists()) {
            new AtomicBoolean(empTxt.delete());
        }
        File vendorTxt = new File(Utils.getAppStoragePath(mContext) + "vendor_master.txt");
        if (vendorTxt.exists()) {
            new AtomicBoolean(vendorTxt.delete());
        }
        File gitTxt = new File(Utils.getAppStoragePath(mContext) + "git_master.txt");
        if (gitTxt.exists()) {
            new AtomicBoolean(gitTxt.delete());
        }
        File misTxt = new File(Utils.getAppStoragePath(mContext) + "mis_transaction_log.txt");
        if (misTxt.exists()) {
            new AtomicBoolean(misTxt.delete());
        }
        File schemeTxt = new File(Utils.getAppStoragePath(mContext) + "scheme_details.txt");
        if (schemeTxt.exists()) {
            new AtomicBoolean(schemeTxt.delete());
        }
        File loyaltyPurchaseTxt = new File(Utils.getAppStoragePath(mContext) + "loyalty_purchase_details.txt");
        if (loyaltyPurchaseTxt.exists()) {
            new AtomicBoolean(loyaltyPurchaseTxt.delete());
        }
        File redeemAwardTxt = new File(Utils.getAppStoragePath(mContext) + "redeem_details.txt.txt");
        if (redeemAwardTxt.exists()) {
            new AtomicBoolean(redeemAwardTxt.delete());
        }
        File accessTxt = new File(Utils.getAppStoragePath(mContext) + "user_access.txt");
        if (accessTxt.exists()) {
            new AtomicBoolean(accessTxt.delete());
        }

        File saudaTxt = new File(Utils.getAppStoragePath(mContext) + "sauda_allocation.txt");
        if (saudaTxt.exists()) {
            new AtomicBoolean(saudaTxt.delete());
        }
        File TDAllocTxt = new File(Utils.getAppStoragePath(mContext) + "TD_allocation.txt");
        if (TDAllocTxt.exists()) {
            new AtomicBoolean(TDAllocTxt.delete());
        }
        File custBranchTxt = new File(Utils.getAppStoragePath(mContext) + "cust_branch_relation.txt");
        if (custBranchTxt.exists()) {
            new AtomicBoolean(custBranchTxt.delete());
        }

        File SurveyCategoryText = new File(Utils.getAppStoragePath(mContext) + "survey_category_master.txt");
        if (SurveyCategoryText.exists()) {
            new AtomicBoolean(SurveyCategoryText.delete());
        }
        File SurveyInputText = new File(Utils.getAppStoragePath(mContext) + "survey_input_details.txt");
        if (SurveyInputText.exists()) {
            new AtomicBoolean(SurveyInputText.delete());
        }

        File GenericOil = new File(Utils.getAppStoragePath(mContext) + "generic_oil_master.txt");
        if (GenericOil.exists()) {
            new AtomicBoolean(GenericOil.delete());
        }

        File SaudaAllocationAccess = new File(Utils.getAppStoragePath(mContext) + "sauda_allocation_access.txt");
        if (SaudaAllocationAccess.exists()) {
            new AtomicBoolean(SaudaAllocationAccess.delete());
        }

        File TDAllocationAccess = new File(Utils.getAppStoragePath(mContext) + "TD_allocation_access.txt");
        if (TDAllocationAccess.exists()) {
            new AtomicBoolean(TDAllocationAccess.delete());
        }

        File MenuAccess = new File(Utils.getAppStoragePath(mContext) + "menu_access.txt");
        if (MenuAccess.exists()) {
            new AtomicBoolean(MenuAccess.delete());
        }

        File SaudaAllocationLog = new File(Utils.getAppStoragePath(mContext) + "sauda_allocation_log.txt");
        if (SaudaAllocationLog.exists()) {
            new AtomicBoolean(SaudaAllocationLog.delete());
        }

        File StreetMaster = new File(Utils.getAppStoragePath(mContext) + "street_master.txt");
        if (StreetMaster.exists()) {
            new AtomicBoolean(StreetMaster.delete());
        }

        File pendingContractFile = new File(Utils.getAppStoragePath(mContext) + "pending_contract.txt");
        if (pendingContractFile.exists()) {
            new AtomicBoolean(pendingContractFile.delete());
        }

        File MallMaster = new File(Utils.getAppStoragePath(mContext) + "mall_master.txt");
        if (MallMaster.exists()) {
            new AtomicBoolean(MallMaster.delete());
        }

        File MallSurveyRelation = new File(Utils.getAppStoragePath(mContext) + "mall_survey_relation.txt");
        if (MallSurveyRelation.exists()) {
            new AtomicBoolean(MallSurveyRelation.delete());
        }

        File Saudatralog = new File(Utils.getAppStoragePath(mContext) + "sauda_transaction_log.txt");
        if (Saudatralog.exists()) {
            new AtomicBoolean(Saudatralog.delete());
        }

        File PreviousOrderCounting = new File(Utils.getAppStoragePath(mContext) + "prev_order_counting_master.txt");
        if (PreviousOrderCounting.exists()) {
            new AtomicBoolean(PreviousOrderCounting.delete());
        }

        File OrderStatus = new File(Utils.getAppStoragePath(mContext) + "order_status.txt");
        if (OrderStatus.exists()) {
            new AtomicBoolean(OrderStatus.delete());
        }

        File OutstandingAgein = new File(Utils.getAppStoragePath(mContext) + "outstanding_ageing.txt");
        if (OutstandingAgein.exists()) {
            new AtomicBoolean(OutstandingAgein.delete());
        }

        File SaudaMRP = new File(Utils.getAppStoragePath(mContext) + "sauda_mrp.txt");
        if (SaudaMRP.exists()) {
            new AtomicBoolean(SaudaMRP.delete());
        }

        File SalesPerformance = new File(Utils.getAppStoragePath(mContext) + "sale_performance.txt");
        if (SalesPerformance.exists()) {
            new AtomicBoolean(SalesPerformance.delete());
        }

        File DestinationMaster = new File(Utils.getAppStoragePath(mContext) + "destination_master.txt");
        if (DestinationMaster.exists()) {
            new AtomicBoolean(DestinationMaster.delete());
        }
    }

    @SuppressLint("SetTextI18n")
    public String SeTLoaderText(String params, int show) {
        StringBuilder text = new StringBuilder();
        if (params.contains("_")) {
            String[] splittedParam = params.split("_");
            for (String s : splittedParam) {
                if (text.toString().matches("")) {
                    text = new StringBuilder(s);
                } else {
                    text.append(" ").append(s);
                }
            }
            text = new StringBuilder(Utils.capitalize(text.toString()));
        } else if (params.contains("-")) {
            String[] splittedParam = params.split("-");
            for (String s : splittedParam) {
                if (text.toString().matches("")) {
                    text = new StringBuilder(s);
                } else {
                    text.append(" ").append(s);
                }
            }
            text = new StringBuilder(Utils.capitalize(text.toString()));
        } else {
            text = new StringBuilder(params);
        }

        if (show == 1) {
            mTextViewDownloadText.setText("Downloading " + text + "..");
        }
        return text.toString();
    }

    @Override
    public void onPause() {
        super.onPause();
        if (Utils.loaderDialog != null && Utils.loaderDialog.isShowing()) {
            Utils.cancelProgressDialog();
        }
    }
}