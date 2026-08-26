package com.forcepower.acedns.util;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import android.os.SystemClock;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.ActivityActivationReport;
import com.forcepower.acedns.activity.ActivityAttendanceReport;
import com.forcepower.acedns.activity.ActivityReportLanding;
import com.forcepower.acedns.activity.ActivityRetailerAppSplashReport;
import com.forcepower.acedns.activity.ActivityStockBalanceReport;
import com.forcepower.acedns.activity.ActivityStockReport;
import com.forcepower.acedns.activity.CashTransferOrReceiveActivity;
import com.forcepower.acedns.activity.CrmActivity;
import com.forcepower.acedns.activity.RetailerStockInActivity;
import com.forcepower.acedns.activity.RetailerStockOutActivity;
import com.forcepower.acedns.activity.RetailerStockOutActivitySpecial;
import com.forcepower.acedns.activity.ReverseAuctionActivity;
import com.forcepower.acedns.adapter.BaseOilRateGeneratedPriceInputAdapter;
import com.forcepower.acedns.adapter.BaseOilRatePriceGenerationInputAdapter;
import com.forcepower.acedns.adapter.ExpandableListAdapterPricePublish;
import com.forcepower.acedns.adapter.ManagerActivityReportAdapter;
import com.forcepower.acedns.adapter.McxBaseOilRateGeneratedPriceInputAdapter;
import com.forcepower.acedns.adapter.McxRatePriceGenerationInputAdapter;
import com.forcepower.acedns.adapter.ZeroOutletCustomerAdapter;
import com.forcepower.acedns.bean.*;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.StringReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Locale;

import static com.forcepower.acedns.constants.AceDnsWebServiceURL.parentURLLaravel;
import static com.forcepower.acedns.constants.Constants.CounterBidListListForRAOnToday;
import static com.forcepower.acedns.constants.Constants.REVERSE_AUCTION_FLAG;
import static com.forcepower.acedns.constants.Constants.UnVerifiedCashReceiveList;
import static com.forcepower.acedns.constants.Constants.allocatedRouteCodeTodayCrm;
import static com.forcepower.acedns.constants.Constants.customerDetailsListReverseAuction;
import static com.forcepower.acedns.constants.Constants.dateString;
import static com.forcepower.acedns.constants.Constants.expListView;
import static com.forcepower.acedns.constants.Constants.listChildDataGlobal;
import static com.forcepower.acedns.constants.Constants.listDataHeaderGlobal;
import static com.forcepower.acedns.constants.Constants.mOrderReportDetailsListGlobal;
import static com.forcepower.acedns.constants.Constants.masterApiCallingFlag;
import static com.forcepower.acedns.constants.Constants.masterApiCallingFlagFromReport;
import static com.forcepower.acedns.constants.Constants.priceListOilGrpFinalList;
import static com.forcepower.acedns.constants.Constants.publishRateList;
import static com.forcepower.acedns.constants.Constants.splashScreenDetailsList;
import static com.forcepower.acedns.constants.Constants.submitEnabledFlag;
import static com.forcepower.acedns.constants.Constants.timeVal;
import static com.forcepower.acedns.constants.Constants.zeroOutletMasterList;
import static com.forcepower.acedns.util.Utils.convertCommaSeparatedListToProperFormat;

public class commonAsyncTaskMasterNew {
    Context mContext;
    Activity activity;
    AceDnsDatabase dbHelper;
    int noRows = -1, noColumn = -1;
    String timeStamp = "";
    String lastUpdate, status = "";
    String dwnldDictTime;
    String mIsInCremental = "";
    String currentDate = "";
    boolean isIndependantDownload = false;
    boolean mIsnavigationon = false;

    String accessStartDate = "", accessEndDate = "", period = "";

    @SuppressLint("SimpleDateFormat")
    public commonAsyncTaskMasterNew(Context context, String statusOBJ) {
        this.mContext = context;
        this.activity = (Activity) context;
        this.status = statusOBJ;
        dbHelper = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        lastUpdate = dbHelper.getlastDownloadTime(status);
        currentDate = new SimpleDateFormat("yyyy-MM-dd").format(Calendar.getInstance().getTime());
        dwnldDictTime = dbHelper.getlastDownloadTime("download_dictionary");
        if (status.equalsIgnoreCase("product_master")) {
            _DOWNLOAD_product_master();
        }
        if (status.equalsIgnoreCase("stock_reallocation")) {
        }
        if (status.equalsIgnoreCase("DOWNLOAD_imei_status")) {
            DOWNLOAD_imei_status();
        }
        if (status.equalsIgnoreCase("conversion_data")) {
            DOWNLOAD_iprod_unit_conv_matrix();
        } else if (status.equalsIgnoreCase("mall_master")) {
            _DOWNLOAD_mall_master();
        } else if (status.equalsIgnoreCase("route_master")) {
            _DOWNLOAD_route_master();
        } else if (status.equalsIgnoreCase("route_master_crm")) {
            DOWNLOAD_route_master_crm();
        } else if (status.equalsIgnoreCase("product_group_master")) {
            _DOWNLOAD_product_group_master();
        } else if (status.equalsIgnoreCase("load_distribution")) {
            _DOWNLOAD_load_distribution();
        } else if (status.equalsIgnoreCase("branch_route_freight")) {
            _DOWNLOAD_branch_route_freight();
        } else if (status.equalsIgnoreCase("RA_route_freight")) {
            DOWNLOAD_RA_route_freight();
        } else if (status.equalsIgnoreCase("bank_master")) {
            _DOWNLOAD_bank_master();
        } else if (status.equalsIgnoreCase("attendance_checkout_details")) {
            _DOWNLOAD_attendance_report();
        } else if (status.equalsIgnoreCase("broker_master")) {
            _DOWNLOAD_broker_master();
        } else if (status.equalsIgnoreCase("credit_limit")) {
            _DOWNLOAD_credit_limit();
        } else if (status.equalsIgnoreCase("outstanding_master")) {
            _DOWNLOAD_outstanding_master();
        } else if (status.equalsIgnoreCase("travel_category")) {
            _DOWNLOAD_travel_category();
        } else if (status.equalsIgnoreCase("product_sub_group_master")) {
            _DOWNLOAD_product_sub_group_master();
        } else if (status.equalsIgnoreCase("product_brand_master")) {
            _DOWNLOAD_product_brand_master();
        } else if (status.equalsIgnoreCase("closing_stock")) {
            _DOWNLOAD_closing_stock();
        } else if (status.equalsIgnoreCase("mrp_master")) {
            _DOWNLOAD_mrp_master();
        } else if (status.equalsIgnoreCase("prev_stock_counting_master")) {
            _DOWNLOAD_prev_stock_counting_master();
        } else if (status.equalsIgnoreCase("prodqty_custclass_wise_TD")) {
            _DOWNLOAD_prodqty_custclass_wise_TD();
        } else if (status.equalsIgnoreCase("route_plan")) {
            _DOWNLOAD_route_plan();
        } else if (status.equalsIgnoreCase("route_plan_mf")) {
            _DOWNLOAD_route_master_mf();
        } else if (status.equalsIgnoreCase("travel_sub_category")) {
            _DOWNLOAD_travel_sub_category();
        } else if (status.equalsIgnoreCase("loyalty_customer")) {
            _DOWNLOAD_loyalty_customer();
        } else if (status.equalsIgnoreCase("scheme_details")) {
            _DOWNLOAD_scheme_details();
        } else if (status.equalsIgnoreCase("loyalty_purchase_details")) {
            _DOWNLOAD_loyalty_purchase_details();
        } else if (status.equalsIgnoreCase("redeeme_details")) {
            _DOWNLOAD_redeeme_details();
        } else if (status.equalsIgnoreCase("rds_master")) {
            _DOWNLOAD_rds_master();
        } else if (status.equalsIgnoreCase("emp_master")) {
            _DOWNLOAD_emp_master();
        } else if (status.equalsIgnoreCase("branch_master")) {
            _DOWNLOAD_branch_master();
        } else if (status.equalsIgnoreCase("honeycomb_cost")) {
            DOWNLOAD_honey_comb_cost();
        } else if (status.equalsIgnoreCase("margin_cost")) {
            DOWNLOAD_margin_cost();
        } else if (status.equalsIgnoreCase("vendor_master")) {
            _DOWNLOAD_vendor_master();
        } else if (status.equalsIgnoreCase("mis_transaction_log")) {
            _DOWNLOAD_mis_transaction_log();
        } else if (status.equalsIgnoreCase("user_access")) {
            _DOWNLOAD_user_access();
        } else if (status.equalsIgnoreCase("customer_branch_relation")) {
            _DOWNLOAD_customer_branch_relation();
        } else if (status.equalsIgnoreCase("sample_master")) {
            DOWNLOAD_sample();
        } else if (status.equalsIgnoreCase("survey_category_master")) {
            _DOWNLOAD_survey_category_master();
        } else if (status.equalsIgnoreCase("survey_input_details")) {
            _DOWNLOAD_survey_input_details();
        } else if (status.equalsIgnoreCase("BOQ_master")) {
            DOWNLOAD_boq_master();
        } else if (status.equalsIgnoreCase("customer_proposed_product")) {
            DOWNLOAD_customer_proposed_product();
        } else if (status.equalsIgnoreCase("gift_master")) {
            DOWNLOAD_gift_master();
        } else if (status.equalsIgnoreCase("additional_material")) {
            DOWNLOAD_additional_material_master();
        } else if (status.equalsIgnoreCase("generic_oil_master")) {
            _DOWNLOAD_generic_oil_master();
        } else if (status.equalsIgnoreCase("van_stock_allocation")) {
            DOWNLOAD_van_sales_stock_master();
        } else if (status.equalsIgnoreCase("self_appraisal_emp_week_wise")) {
            DOWNLOAD_self_appraisal_emp_week_wise();
        } else if (status.equalsIgnoreCase("prospective_customer_master")) {
            DOWNLOAD_prospective_customer_master();
        } else if (status.equalsIgnoreCase("menu_access")) {
            _DOWNLOAD_menu_access();
        } else if (status.equalsIgnoreCase("sauda_allocation_access")) {
            _DOWNLOAD_sauda_allocation_access();
        } else if (status.equalsIgnoreCase("TD_allocation_access")) {
            _DOWNLOAD_TD_allocation_access();
        } else if (status.equalsIgnoreCase("sauda_allocation_log")) {
            _DOWNLOAD_sauda_allocation_log();
        } else if (status.equalsIgnoreCase("street_master")) {
            _DOWNLOAD_street_master();
        } else if (status.equalsIgnoreCase("pending_contract")) {
            _DOWNLOAD_pending_contract();
        } else if (status.equalsIgnoreCase("mall_survey_relation")) {
            _DOWNLOAD_mall_survey_relation();
        } else if (status.equalsIgnoreCase("prev_order_counting_master")) {
            _DOWNLOAD_prev_order_counting_master();
        } else if (status.equalsIgnoreCase("farmer_master")) {
            DOWNLOAD_farmer_master();
        } else if (status.equalsIgnoreCase("order_status")) {
            _DOWNLOAD_order_status();
        } else if (status.equalsIgnoreCase("outstanding_ageing")) {
            _DOWNLOAD_outstanding_ageing();
        } else if (status.equalsIgnoreCase("sauda_mrp")) {
            downloadSaudaMrp();
        } else if (status.equalsIgnoreCase("bargain_mrp")) {
            downloadBargainMrp();
        } else if (status.equalsIgnoreCase("grn")) {
            downloadGrnDo();
        } else if (status.equalsIgnoreCase("customer_product_relation")) {
            customerProductRelation();
        } else if (status.equalsIgnoreCase("customer_product_info")) {
            customer_product_info();
        } else if (status.equalsIgnoreCase("mcx_rate")) {
            mcxRate();
        } else if (status.equalsIgnoreCase("order_approval")) {
            orderApproval();
        } else if (status.equalsIgnoreCase("technical_meet_approval")) {
            technicalMeetApproval();
        } else if (status.equalsIgnoreCase("technical_meet_approval_status")) {
            technicalMeetApprovalStatus();
        } else if (status.equalsIgnoreCase("branch_destination")) {
            branchDestination();
        } else if (status.equalsIgnoreCase("branch_dump")) {
            branchDump();
        } else if (status.equalsIgnoreCase("customer_broker_relation")) {
            customer_broker_relation();
        } else if (status.equalsIgnoreCase("brokerage_cost")) {
            brokerage_cost();
        } else if (status.equalsIgnoreCase("depot_cost")) {
            depotCostDownload();
        } else if (status.equalsIgnoreCase("bargain_transaction")) {
            bargainTransactionDownload();
        } else if (status.equalsIgnoreCase("bargainReportDownload")) {
            bargainReportDownload();
        } else if (status.equalsIgnoreCase("bargainReportDownloadLevel2")) {
            bargainReportDownloadLevel2();
        } else if (status.equalsIgnoreCase("doReportDownload")) {
            doReportDownload();
        } else if (status.equalsIgnoreCase("doReportDownloadLevel2")) {
            doReportDownloadLevel2();
        } else if (status.equalsIgnoreCase("primary_freight")) {
            freightCostDownload();
        } else if (status.equalsIgnoreCase("sale_performance")) {
            _DOWNLOAD_sale_performance();
        } else if (status.equalsIgnoreCase("emp_menu_access")) {
            _DOWNLOAD_emp_menu_access();
        } else if (status.equalsIgnoreCase("customer_product_wise_orderplan")) {
            _DOWNLOAD_customer_product_wise_orderplan();
        } else if (status.equalsIgnoreCase("survey_publish")) {
            _DOWNLOAD_survey_publish();
        } else if (status.equalsIgnoreCase("offer_publish")) {
            _DOWNLOAD_offer_publish();
        } else if (status.equalsIgnoreCase("non_trade_customer_master")) {
            _DOWNLOAD_non_trade_customer_master();
        } else if (status.equalsIgnoreCase("distributor_route_relation")) {
            _DOWNLOAD_distributor_route_relation();
        } else if (status.equalsIgnoreCase("customerToDelete")) {
            customerToDelete();
        } else if (status.equalsIgnoreCase("catalogue_info")) {
            _DOWNLOAD_catalogue_info();
        } else if (status.equalsIgnoreCase("facilitator_master")) {
            _DOWNLOAD_facilitator_master();
        } else if (status.equalsIgnoreCase("complaint_master")) {
            _DOWNLOAD_complaint_master();
        } else if (status.equalsIgnoreCase("lead_generation_master")) {
            _DOWNLOAD_lead_generation_master();
        } else if (status.equalsIgnoreCase("quality_complaint_master")) {
            _DOWNLOAD_quality_complaint_master();
        } else if (status.equalsIgnoreCase("mtl_testing_master")) {
            _DOWNLOAD_MtlTestingFormat_master();
        } else if (status.equalsIgnoreCase("site_lead_conversion_master")) {
            _DOWNLOAD_site_lead_conversion_masterURL();
        } else if (status.equalsIgnoreCase("site_master")) {
            _DOWNLOAD_site_master();
        } else if (status.equalsIgnoreCase("self_appraisal_productgroup_wise")) {
            _DOWNLOAD_self_appraisal_productgroup_wise();
        } else if (status.equalsIgnoreCase("state_master")) {
            DOWNLOAD_state_master();
        } else if (status.equalsIgnoreCase("yellow-card-date-validation")) {
            DOWNLOAD_yellow_card_date_validation();
        } else if (status.equalsIgnoreCase("yellow_card_date_validation_customerwise")) {
            _DOWNLOAD_yellow_card_date_validation_customerwise();
        } else if (status.equalsIgnoreCase("market_feedback_tagging")) {
            _DOWNLOAD_market_feedback_tagging_master();
        } else if (status.equalsIgnoreCase("vendor-details-download")) {
            _DOWNLOAD_VendorDetailsDownload();
        } else if (status.equalsIgnoreCase("scheme_master")) {
            DOWNLOAD_scheme_master();
        } else if (status.equalsIgnoreCase("freebies_master")) {
            DOWNLOAD_freebies_master();
        } else if (status.equalsIgnoreCase("ra_sauda")) {
            DOWNLOAD_RA_margin_rate();
        } else if (status.equalsIgnoreCase("ra_sauda_counter")) {
            DOWNLOAD_plant_product_wise_RA_rate_rejected();
        } else if (status.equalsIgnoreCase("ra_window_timing")) {
            DOWNLOAD_plant_product_wise_RA_rate_accepted();
        } else if (status.equalsIgnoreCase("billing_information")) {
            DOWNLOAD_billing_info();
        } else if (status.equalsIgnoreCase("dealer_transaction")) {
            DOWNLOAD_dealer_transaction();
        } else if (status.equalsIgnoreCase("stock_allocation")) {
            stock_allocation_master();
        } else if (status.equalsIgnoreCase("stock_balance_details")) {
            stock_balance_details_master();
        } else if (status.equalsIgnoreCase("zero_outlet")) {
            zero_outlet_details();
        } else if (status.equalsIgnoreCase("splash_screen_details")) {
            splashScreenDetailsApiCaling();
        } else if (status.equalsIgnoreCase("manager_activity")) {
            managerActivityApiCaling();
        } else if (status.equalsIgnoreCase("manager_activity_details")) {
            managerActivityDetailsApiCaling();
        } else if (status.equalsIgnoreCase("mcx_price_generation")) {
            McxpriceGenerationApiCaling();
        } else if (status.equalsIgnoreCase("price_generation")) {
            priceGenerationApiCaling();
        } else if (status.equalsIgnoreCase("release_pricing")) {
            priceReleaseApiCalling();
        } else if (status.equalsIgnoreCase("order_summary")) {
            DOWNLOAD_order_summary();
        } else if (status.equalsIgnoreCase("state_district_town")) {
            DOWNLOAD_state_district_town();
        } else if (status.equalsIgnoreCase("self_appraisal_branch_wise")) {
            _DOWNLOAD_self_appraisal_branch_wise();
        } else if (status.equalsIgnoreCase("self_appraisal_customer_wise")) {
            DownloadSelfAppraisalSummary();
        } else if (status.equalsIgnoreCase("retailer-wise-target-ach")) {
            DOWNLOAD_tar_ach_retailer_wise();
        } else if (status.equalsIgnoreCase("beatwise_TA_DA")) {
            DOWNLOAD_beat_wise_TA_DA();
        } else if (status.equalsIgnoreCase("self_appraisal_emp_wise")) {
            DownloadSelfAppraisalEmpWise();
        } else if (status.equalsIgnoreCase("TA_DA_limit")) {
            DownloaddesingnationWiseTATDLimit();
        } else if (status.equalsIgnoreCase("target_achievement_route_categorywise")) {
            DOWNLOAD_target_achievement_route_categorywise();
        } else if (status.equalsIgnoreCase("kyc_master")) {
            _DOWNLOAD_KYC_MASTER();
        } else if (status.equalsIgnoreCase("site_lead_approval")) {
            _DOWNLOAD_SITE_LEAD_APPROVAL_MASTER();
        } else if (status.equalsIgnoreCase("dashboard_data_download")) {
            _DOWNLOAD_MIS_DASHBOARD();
        } else if (status.equalsIgnoreCase("target_achievement")) {
            _DOWNLOAD_target_achievement();
        } else if (status.equalsIgnoreCase("emp_date_wise_route_allocation")) {
            DOWNLOAD_emp_date_wise_route_allocation();
        } else if (status.equalsIgnoreCase("fs_survey_publish")) {
            _DOWNLOAD_fs_survey_publish();
        } else if (status.equalsIgnoreCase("survey_table_view")) {
            _DOWNLOAD_survey_table_view();
        } else if (status.equalsIgnoreCase("route_customer_plan")) {
            _DOWNLOAD_route_customer_plan();
        } else if (status.equalsIgnoreCase("competitor_group_master")) {
            _DOWNLOAD_competitor_group_master();
        } else if (status.equalsIgnoreCase("branch_geo_fencing")) {
            _DOWNLOAD_branchwise_geofencing_master();
        } else if (status.equalsIgnoreCase("sauda_allocation")) {
            _DOWNLOAD_sauda_allocation();
        } else if (status.equalsIgnoreCase("TD_allocation")) {
            _DOWNLOAD_TD_allocation();
        } else if (status.equalsIgnoreCase("customer_master")) {
            _DOWNLOAD_customer_master();
        } else if (status.equalsIgnoreCase("customer_master_mf")) {
            _DOWNLOAD_customer_master_mf_taging();
        } else if (status.equalsIgnoreCase("customer_master_crm")) {
            DOWNLOAD_customer_master_crm();
        } else if (status.equalsIgnoreCase("customer_product_wise_msl")) {
            DOWNLOAD_customer_product_wise_msl();
        } else if (status.equalsIgnoreCase("destination_master")) {
            _DOWNLOAD_destination_master();
        } else if (status.equalsIgnoreCase("branchwise_scheme_PDF")) {
            DOWNLOAD_scheme_pdf_master();
        } else if (status.equalsIgnoreCase("scheme_pdf")) {
            DOWNLOAD_scheme_pdf_master_without_branch();
        } else if (status.equalsIgnoreCase("golden_rules")) {
            DOWNLOAD_branchwise_golden_rule();
        } else if (status.equalsIgnoreCase("git_master")) {
            _DOWNLOAD_git_master();
        } else if (status.equalsIgnoreCase("cash_transfer_master")) {
            DOWNLOAD_cash_transfer_master();
        } else if (status.equalsIgnoreCase("customer_orientation_data")) {
            DOWNLOAD_customer_orientation_data();
        }
        if (status.equalsIgnoreCase("cat_subcat_brand_mapping")) {
            DOWNLOAD_cat_subcat_brand_mapping();
        }
        if (status.equalsIgnoreCase("cat_subcat_prod_mapping")) {
            DOWNLOAD_cat_subcat_prod_mapping();
        }
        if (status.equalsIgnoreCase("customer_product_info")) {
        }
        if (status.equalsIgnoreCase("emp_mtl_mapping")) {
            _DOWNLOAD_emp_mtl_mapping();
        }
    }

    @SuppressLint("SimpleDateFormat")
    public commonAsyncTaskMasterNew(Context context, String statusOBJ, boolean isIndependantDownload) {
        this.mContext = context;
        this.status = statusOBJ;
        dbHelper = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        lastUpdate = dbHelper.getlastDownloadTime(status);
        currentDate = new SimpleDateFormat("yyyy-MM-dd").format(Calendar.getInstance().getTime());
        dwnldDictTime = dbHelper.getlastDownloadTime("download_dictionary");
        this.isIndependantDownload = isIndependantDownload;
        if (status.equalsIgnoreCase("loyalty_purchase_details")) {
            _DOWNLOAD_loyalty_purchase_details();
        }
    }

    @SuppressLint("SimpleDateFormat")
    public commonAsyncTaskMasterNew(Context context, String statusOBJ, boolean isnavigationon, String empty) {
        this.mContext = context;
        this.status = statusOBJ;
        dbHelper = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        lastUpdate = dbHelper.getlastDownloadTime(status);
        currentDate = new SimpleDateFormat("yyyy-MM-dd").format(Calendar.getInstance().getTime());
        dwnldDictTime = dbHelper.getlastDownloadTime("download_dictionary");
        this.mIsnavigationon = isnavigationon;
        _DOWNLOAD_sauda_transaction_log();
    }

    public void _DOWNLOAD_product_master() {
        Increment_product_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.productMasterDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<ProductMasterDetails> prodList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ProductMasterDetails temp = new ProductMasterDetails();
                        temp.setProdCode(RowData[0]);
                        temp.setGrpCode(RowData[1]);
                        temp.setGrpName(RowData[2]);
                        temp.setSubGrpCode(RowData[3]);
                        temp.setSubGrpName(RowData[4]);
                        temp.setBrndCode(RowData[5]);
                        temp.setBrndName(RowData[6]);
                        temp.setDesc(RowData[7]);
                        temp.setIsBlkLst(RowData[8]);
                        temp.setIsAcedns(RowData[9]);
                        temp.setUom1(RowData[10]);
                        temp.setUom2(RowData[11]);
                        temp.setConversionFactor(RowData[12]);
                        temp.setPackSize(RowData[13]);
                        temp.setUOM3(RowData[14]);
                        temp.setConversionFactorTwo(RowData[15]);
                        temp.setTD(RowData[16]);
                        temp.setBranchCode(RowData[17]);
                        temp.setVerticalValue(RowData[18]);
                        temp.setSecondaryUnit(RowData[19]);
                        temp.setDnsProdCode(RowData[20]);
                        temp.setFocus(RowData[21]);
                        temp.setWeightage(RowData[22]);
                        temp.setVatRate(RowData[23]);
                        temp.setAdditionalVatRate(RowData[24]);
                        temp.setFreightCost(RowData[25]);
                        temp.setpackUnit(RowData[26]);
                        temp.setsize(RowData[27]);
                        temp.setuom4(RowData[28]);
                        temp.setuom5(RowData[29]);
                        temp.setClosingStk("");
                        prodList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToProductMaster(prodList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isProductTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isProductTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_imei_status() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.imeiStatusURL + "?nick_name=" + Constants.nickName + "&IMEI=" + Constants.imei;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        final ArrayList<ImeiDetails> prodList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ImeiDetails temp = new ImeiDetails();
                        temp.setimei(RowData[0]);
                        temp.setbillDate(RowData[1]);
                        temp.setbilledTo(RowData[2]);
                        temp.setstockOutDate(RowData[3]);
                        temp.setstockOutTime(RowData[4]);
                        temp.setstockOutDoneBy(RowData[5]);
                        temp.setactivationDate(RowData[6]);
                        temp.setactivationTime(RowData[7]);
                        prodList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        final Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            TextView imeitv = activity.findViewById(R.id.imeitv);
            TextView billdatetv = activity.findViewById(R.id.billdatetv);
            TextView billedtotv = activity.findViewById(R.id.billedtotv);
            TextView stockoutdatetv = activity.findViewById(R.id.stockoutdatetv);
            TextView stockouttimetv = activity.findViewById(R.id.stockouttimetv);
            TextView stockoutdonebytv = activity.findViewById(R.id.stockoutdonebytv);
            TextView activationdatetv = activity.findViewById(R.id.activationdatetv);
            TextView activationtimetv = activity.findViewById(R.id.activationtimetv);
            imeitv.setText("-");
            billdatetv.setText("-");
            billedtotv.setText("-");
            stockoutdatetv.setText("-");
            stockouttimetv.setText("-");
            stockoutdonebytv.setText("-");
            activationdatetv.setText("-");
            activationtimetv.setText("-");
            Utils.cancelProgressDialog();
            if (!prodList.isEmpty()) {
                final ImeiDetails imeiDetails = prodList.get(0);
                imeitv.setText(imeiDetails.getimei());
                billdatetv.setText(imeiDetails.getbillDate());
                billedtotv.setText(imeiDetails.getbilledTo());
                stockoutdatetv.setText(imeiDetails.getstockOutDate());
                stockouttimetv.setText(imeiDetails.getstockOutTime());
                stockoutdonebytv.setText(imeiDetails.getstockOutDoneBy());
                activationdatetv.setText(imeiDetails.getactivationDate());
                activationtimetv.setText(imeiDetails.getactivationTime());
            } else {
                Utils.showToast(mContext, "No relevant data found.");
            }
        });
    }

    public void DOWNLOAD_iprod_unit_conv_matrix() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.conversionDataDownloadURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        final ArrayList<ConversionDataDetails> dataList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ConversionDataDetails temp = new ConversionDataDetails();
                        temp.setprod_code(RowData[0]);
                        temp.setmapped_prod_code(RowData[1]);
                        temp.setis_flash(RowData[2]);
                        temp.setflash_name(RowData[3]);
                        dataList.add(temp);
                    }
                }
            }
            buffer.close();
            long insertStatus = dbHelper.InsertToConversionMatrix(dataList);
            if (insertStatus == noRows && noRows > 0) {
            } else if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskMaster(mContext, status);
            } else if (noRows == 0 && noColumn != 0) {
            } else {
                if (noRows != 0) {
                    Constants.isDownLoadComplete = false;
                }
            }
        } catch (IOException ignored) {
        }
    }

    public void DOWNLOAD_cat_subcat_brand_mapping() {
        Increment_cat_subcat_brand_prod_mapping();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.catSubcatBrandMappingURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CatSubCatBrandProdDetails> prodList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CatSubCatBrandProdDetails temp = new CatSubCatBrandProdDetails();
                        temp.setid_row(RowData[0]);
                        temp.setcat_id(RowData[1]);
                        temp.setsub_cat_id(RowData[2]);
                        temp.setcat_name(RowData[3]);
                        temp.setsub_cat_name(RowData[4]);
                        temp.setbrand_prod_id(RowData[5]);
                        temp.setbrand_prod_name(RowData[6]);
                        temp.setstatus(RowData[7]);
                        prodList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToPCatSubcatBrandMapping(prodList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isProductTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isProductTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_emp_mtl_mapping() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.mtl_no_download_detailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<MallMaster> mMallMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MallMaster temp = new MallMaster();
                        temp.setMallId(RowData[0]);
                        temp.setMallName(RowData[1]);
                        mMallMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToMTLMaster(mMallMasterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_cat_subcat_prod_mapping() {
        Increment_cat_subcat_brand_prod_mapping();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.catSubcatProdMappingURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CatSubCatBrandProdDetails> prodList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CatSubCatBrandProdDetails temp = new CatSubCatBrandProdDetails();
                        temp.setid_row(RowData[0]);
                        temp.setcat_id(RowData[1]);
                        temp.setsub_cat_id(RowData[2]);
                        temp.setcat_name(RowData[3]);
                        temp.setsub_cat_name(RowData[4]);
                        temp.setbrand_prod_id(RowData[5]);
                        temp.setbrand_prod_name(RowData[6]);
                        temp.setstatus(RowData[7]);
                        prodList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToPCatSubcatProdMapping(prodList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isProductTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isProductTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_mall_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.mallMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<MallMaster> mMallMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MallMaster temp = new MallMaster();
                        temp.setMallId(RowData[0]);
                        temp.setMallName(RowData[1]);
                        temp.setAddress(RowData[2]);
                        temp.setLandmark(RowData[3]);
                        temp.setArea(RowData[4]);
                        temp.setCity(RowData[5]);
                        temp.setPincode(RowData[6]);
                        temp.setState(RowData[7]);
                        temp.setCountry(RowData[8]);
                        temp.setClosedOn(RowData[9]);
                        temp.setSTDCode(RowData[10]);
                        temp.setUpcomingEvents(RowData[11]);
                        temp.setType(RowData[12]);
                        temp.setGeneralFacility(RowData[13]);
                        temp.setStreetNumber(RowData[14]);
                        temp.setMarket(RowData[15]);
                        temp.setOpeningTime(RowData[16]);
                        temp.setClosingTime(RowData[17]);
                        temp.setRating(RowData[18]);
                        temp.setPhoneNo(RowData[19]);
                        temp.setFloor(RowData[20]);
                        mMallMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToMallMaster(mMallMasterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DownloadSelfAppraisalSummary() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.selfAppraisalCustomerWise + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SelfAppraisalDetailsCustomerWise> mMallMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SelfAppraisalDetailsCustomerWise temp = new SelfAppraisalDetailsCustomerWise();
                        temp.setcutomerCode(RowData[0]);
                        temp.setcustomerName(RowData[1]);
                        temp.setvertical(RowData[2]);
                        temp.setproduct_group_code(RowData[3]);
                        temp.setprod_code(RowData[4]);
                        temp.setmonth(RowData[5]);
                        temp.settarget(RowData[6]);
                        temp.setachievement(RowData[7]);
                        temp.setPrivousTarget(RowData[8]);
                        temp.setPrevousAchievement(RowData[9]);
                        mMallMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToCustomerSelfAppraisalSummary(mMallMasterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DownloadSelfAppraisalEmpWise() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.selfAppraisalEmpWise + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SelfAppraisalDetailsCustomerWise> mMallMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SelfAppraisalDetailsCustomerWise temp = new SelfAppraisalDetailsCustomerWise();
                        temp.setempCode(RowData[0]);
                        temp.setempName(RowData[1]);
                        temp.setvertical(RowData[2]);
                        temp.setproduct_group_code(RowData[3]);
                        temp.setprod_code(RowData[4]);
                        temp.setmonth(RowData[5]);
                        temp.settarget(RowData[6]);
                        temp.setachievement(RowData[7]);
                        mMallMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToEmpSelfAppraisalSummary(mMallMasterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DownloaddesingnationWiseTATDLimit() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.desingnationWiseTATDLimitUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<DesignationWiseTADALimit> mMallMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        DesignationWiseTADALimit temp = new DesignationWiseTADALimit();
                        temp.setfuel_allowance(RowData[0]);
                        temp.setair_facility(RowData[1]);
                        temp.setrail_facility(RowData[2]);
                        temp.setfooding_in_station(RowData[3]);
                        temp.setfooding_night_stay(RowData[4]);
                        temp.setlodging_per_day(RowData[5]);
                        temp.setown_arrangement_per_day(RowData[6]);
                        mMallMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToDesignationwiseTADA(mMallMasterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_route_master() {
        Increment_route_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.routeDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RouteDetails> routeList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RouteDetails temp = new RouteDetails();
                        temp.setRouteCode(RowData[0]);
                        temp.setRouteName(RowData[1]);
                        temp.setReplacingRouteCode(RowData[2]);
                        temp.setbranch_code(RowData[3]);
                        routeList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToRouteMaster(routeList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isRouteTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn == 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0) {
            Constants.isRouteTableUpdated = false;
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void DOWNLOAD_route_master_crm() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.routeDetailsAlternateURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RouteDetails> routeList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RouteDetails temp = new RouteDetails();
                        temp.setRouteCode(RowData[0]);
                        temp.setRouteName(RowData[1]);
                        temp.setReplacingRouteCode(RowData[2]);
                        routeList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToRouteMasterCrm(routeList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isRouteTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn == 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0) {
            Constants.isRouteTableUpdated = false;
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_product_group_master() {
        Increment_product_group_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.productGroupDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<ProductGroupDetails> grpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ProductGroupDetails temp = new ProductGroupDetails();
                        temp.setGroupCode(RowData[0]);
                        temp.setGroupName(RowData[1]);
                        temp.setVerticalValue(RowData[2]);
                        temp.setStockOutType(RowData[3]);
                        grpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToProductGroupMaster(grpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isProductGroupTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "product_group_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isProductGroupTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_load_distribution() {
        Increment_load_distribution();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.loadDistributionDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<LoadDistribution> LoadDistributionList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        LoadDistribution temp = new LoadDistribution();
                        temp.setprod_code(RowData[0]);
                        temp.setqty_truck_load(RowData[1]);
                        temp.setdownload_time(RowData[2]);
                        temp.settransport_mode(RowData[3]);
                        temp.settruck_load(RowData[4]);
                        LoadDistributionList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = 0;
        if (noRows > 0 && noColumn > 0) {
            insertStatus = dbHelper.InsertToLoadDistribution(LoadDistributionList);
        }
        if (insertStatus == noRows && noRows > 0) {
            Constants.isLoadDistributionTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isLoadDistributionTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_branch_route_freight() {
        Increment_branch_route_freight();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.branchRouteFreightDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<BranchRouteFreight> branchRouteFreightList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BranchRouteFreight temp = new BranchRouteFreight();
                        temp.setbranch_code(RowData[0]);
                        temp.setroute_code(RowData[1]);
                        temp.setfreight(RowData[2]);
                        temp.setacedns(RowData[3]);
                        temp.setdate(RowData[4]);
                        temp.setcapacity(RowData[5]);
                        temp.setTransportMode(RowData[6]);
                        temp.setVertical(RowData[7]);
                        temp.settransit_time(RowData[8]);
                        branchRouteFreightList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = 0;
        if (noRows > 0 && noColumn > 0) {
            insertStatus = dbHelper.InsertToBranchRouteFreight(branchRouteFreightList);
        }
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBranchRouteFreightTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBranchRouteFreightTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_bank_master() {
        Increment_bank_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.bankURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<BankDetails> bnkList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BankDetails temp = new BankDetails();
                        temp.setBankId(RowData[0]);
                        temp.setBankName(RowData[1]);
                        bnkList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToBankMaster(bnkList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBankTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "bank_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBankTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_attendance_report() {
        Increment_attendance_report();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.attendanceReportURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<AttendanceReportDetails> bnkList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        AttendanceReportDetails temp = new AttendanceReportDetails();
                        temp.setemp_code(RowData[0]);
                        temp.settrans_id(RowData[1]);
                        temp.setdate(RowData[2]);
                        bnkList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToAttendanceReportMaster(bnkList);
        if (!bnkList.isEmpty()) {
            add_last_update_time(status);
        }
        if (insertStatus == noRows && noRows > 0) {
            Constants.isAttendanceReportTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isAttendanceReportTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isAttendanceReportTableUpdated = false;
            }
        }
        if (!masterApiCallingFlag) {
            Activity activity = (Activity) mContext;
            activity.runOnUiThread(() -> {
                Utils.cancelProgressDialog();
                Intent intent = new Intent(mContext, ActivityAttendanceReport.class);
                intent.putExtra("SELECTION", ActivityReportLanding.SELECTION);
                intent.putExtra("STARTDATE", ActivityReportLanding.mStartDate);
                intent.putExtra("ENDDATE", ActivityReportLanding.mEndDate);
                mContext.startActivity(intent);
            });
        }
    }

    public void _DOWNLOAD_broker_master() {
        Increment_broker_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.brokerDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<BrokerMaster> mBrokerMastersList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BrokerMaster temp = new BrokerMaster();
                        temp.setBrokerId(RowData[0]);
                        temp.setBrokerName(RowData[1]);
                        temp.setAcedns(RowData[2]);
                        temp.setBrokerageCost(RowData[3]);
                        mBrokerMastersList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBrokerMaster(mBrokerMastersList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBrokerUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBrokerUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_credit_limit() {
        Increment_credit_limit();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.creditLimitURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CustomerDetails> customerList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerDetails temp = new CustomerDetails();
                        temp.setCustomerCode(RowData[0]);
                        temp.setCreditLimit(RowData[1]);
                        customerList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.updateCreditLimit(customerList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "credit_limit");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_outstanding_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.outstandingDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes") + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<OutstandingDetails> outList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        OutstandingDetails temp = new OutstandingDetails();
                        temp.setCustomerCode(RowData[0]);
                        temp.setRecId(RowData[1]);
                        temp.setInvoice_id(RowData[2]);
                        temp.setCustomerName(RowData[3]);
                        temp.setDate(RowData[4]);
                        temp.setInvoice_amount(RowData[5]);
                        temp.setDue_amount(RowData[6]);
                        outList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToOutstandingMaster(outList);
        if (insertStatus == noRows || noRows == 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "outstanding_master");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_travel_category() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.travelExpCategoryURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes") + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<TravelExpCategory> catList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        TravelExpCategory temp = new TravelExpCategory();
                        temp.setCategoryId(RowData[0]);
                        temp.setCategoryName(RowData[1]);
                        catList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToTravelExpCatMaster(catList);
        if (insertStatus == noRows || noRows == 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "travel_category");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_product_sub_group_master() {
        Increment_product_sub_group_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.productSubGroupDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<ProductSubGrpDetails> subGrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ProductSubGrpDetails temp = new ProductSubGrpDetails();
                        temp.setSubGrpCode(RowData[0]);
                        temp.setGrpCode(RowData[1]);
                        temp.setSubGrpName(RowData[2]);
                        subGrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToProductSubGroupMaster(subGrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isProductSubGroupTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "product_sub_group_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isProductSubGroupTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_product_brand_master() {
        Increment_product_brand_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.productBrandDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<ProductBrandDetails> brandList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ProductBrandDetails temp = new ProductBrandDetails();
                        temp.setBrandCode(RowData[0]);
                        temp.setSubGrpCode(RowData[1]);
                        temp.setBrandName(RowData[2]);
                        brandList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToProductBrandMaster(brandList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isProductBrandTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "product_brand_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isProductBrandTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_closing_stock() {
        Increment_closing_stock();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.closingStockURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<ClosingStock> clStkList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ClosingStock temp = new ClosingStock();
                        temp.setProductCode(RowData[0]);
                        temp.setClosingStk(RowData[1]);
                        clStkList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (Exception ignored) {
        }
        long insertStatus = dbHelper.insertToClosingStock(clStkList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isClosingStockUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "closing_stock");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isClosingStockUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_mrp_master() {
        Increment_mrp_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.mrpURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<MRPDetails> mrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MRPDetails temp = new MRPDetails();
                        temp.setProdCode(RowData[0]);
                        temp.setMrpCode(RowData[1]);
                        temp.setMrpValue(RowData[2]);
                        temp.setSaleRate(RowData[3]);
                        temp.setUom(RowData[4]);
                        temp.setBranchCode(RowData[5]);
                        temp.setDestinationCode(RowData[6]);
                        temp.setOrderType(RowData[7]);
                        temp.setAcedns(RowData[8]);
                        temp.setWSRate(RowData[9]);
                        temp.setDistributorRate(RowData[10]);
                        temp.setSSRate(RowData[11]);
                        temp.setDepotRate(RowData[12]);
                        mrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToMRPMaster(mrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isMrpTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "mrp_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isMrpTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_prev_stock_counting_master() {
        Increment_prev_stock_counting_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.prevStockCountingURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<PrevStockCountingDetails> stockList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        PrevStockCountingDetails temp = new PrevStockCountingDetails();
                        temp.setCustomerCode(RowData[0]);
                        temp.setProductCode(RowData[1]);
                        temp.setVisitDetails(RowData[2]);
                        stockList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToPrevStockCountingMaster(stockList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isPrevStockCountingUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "prev_stock_counting_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isPrevStockCountingUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_prodqty_custclass_wise_TD() {
        Increment_prodqty_custclass_wise_TD();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.prodQtyCustClassWiseTD + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<ProdQtyCustClassWiseTDDetails> dataList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ProdQtyCustClassWiseTDDetails temp = new ProdQtyCustClassWiseTDDetails();
                        temp.setBranchCode(RowData[0]);
                        temp.setProductCode(RowData[1]);
                        temp.setQtySlab(RowData[2]);
                        temp.setTDPercent(RowData[3]);
                        temp.setCustClass(RowData[4]);
                        temp.setAcedns(RowData[5]);
                        dataList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToProductQtyCustClassWiseTDMaster(dataList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isPrevStockCountingUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "prodqty_custclass_wise_TD");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isPrevStockCountingUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_route_plan() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.routePlanURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes") + "&last_update_time=" + lastUpdate + "&current_date=" + currentDate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RoutePlanMasterDetails> routePlanList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else if (line.indexOf("µ") > 0) {
                    String[] dataArray = line.split("µ");
                    accessStartDate = dataArray[0];
                    accessEndDate = dataArray[1];
                    period = dataArray[2];
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RoutePlanMasterDetails temp = new RoutePlanMasterDetails();
                        temp.setTranId(RowData[0]);
                        temp.setEmpCode(RowData[1]);
                        temp.setRoutecode(RowData[2]);
                        temp.setVisitDate(RowData[3]);
                        temp.setCreateDate(RowData[4]);
                        temp.setStatus(RowData[5]);
                        temp.setDistributorCode(RowData[6]);
                        temp.setRouteName(RowData[7]);
                        temp.setWorkingWIth(RowData[8]);
                        routePlanList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = -1;
        if (!routePlanList.isEmpty()) {
            insertStatus = dbHelper.InsertToRoutePlanMaster(routePlanList);
        }
        if (!accessStartDate.isEmpty() && !accessEndDate.isEmpty() && !period.isEmpty()) {
            dbHelper.InsertToRoutePlanAccessTable(accessStartDate, accessEndDate, period);
        }
        if (insertStatus == noRows || noRows == 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "route_plan");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_travel_sub_category() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.travelExpSubCategoryURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes") + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<TravelExpSubCategory> subList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        TravelExpSubCategory temp = new TravelExpSubCategory();
                        temp.setSubCatId(RowData[0]);
                        temp.setSubCatName(RowData[1]);
                        temp.setCategoryId(RowData[2]);
                        subList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToTravelExpSubCatMaster(subList);
        if (insertStatus == noRows || noRows == 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "travel_sub_category");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_loyalty_customer() {
        Increment_loyalty_customer();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.loyaltyCustomerURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<LoyaltyCustomerDetails> loyalCustList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        LoyaltyCustomerDetails temp = new LoyaltyCustomerDetails();
                        temp.setCardHolderCode(RowData[0]);
                        temp.setCardHolderName(RowData[1]);
                        temp.setCardNumber(RowData[2]);
                        temp.setCardType(RowData[3]);
                        temp.setPurchaseValue(RowData[4]);
                        temp.setRewardPoint(RowData[5]);
                        temp.setLastUpdate(RowData[6]);
                        temp.setRedeemed(RowData[7]);
                        temp.setPhone(RowData[8]);
                        temp.setAddress(RowData[9]);
                        temp.setVehicleNo(RowData[10]);
                        temp.setCardExpDate(RowData[11]);
                        loyalCustList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToCardHolderMaster(loyalCustList);
        if (insertStatus == noRows || noRows == 0) {
            dbHelper.insertToLogTable(timeStamp, "loyalty_customer");
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "loyalty_customer");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_scheme_details() {
        Increment_scheme_details();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.capMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SchemeFreebiesDetails> schemeList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SchemeFreebiesDetails temp = new SchemeFreebiesDetails();
                        temp.setVerticalName(RowData[0]);
                        temp.setSchemeValue(RowData[1]);
                        schemeList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToSchemeDetails(schemeList);
        if (insertStatus == noRows || noRows == 0) {
            Constants.isLoyaltyTableUpdated = false;
            dbHelper.insertToLogTable(timeStamp, "scheme_details");
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "scheme_details");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_loyalty_purchase_details() {
        Increment_loyalty_purchase_details();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.loyaltyPurchaseURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<LoyaltyPurchaseDetails> purchaseList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        LoyaltyPurchaseDetails temp = new LoyaltyPurchaseDetails();
                        temp.setLoyaltyCardNo(RowData[0]);
                        temp.setVerticalName(RowData[1]);
                        temp.setPurchaseValue(RowData[2]);
                        temp.setRwrdPoint(RowData[3]);
                        temp.setRdmdPoint(RowData[4]);
                        purchaseList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToLoyaltyPurchaseDetails(purchaseList);
        if (insertStatus == noRows || noRows == 0) {
            Constants.isLoyaltyPurchaseUpdated = false;
            dbHelper.insertToLogTable(timeStamp, "loyalty_purchase_details");
            if (Constants.downloadTableList != null
                    && Constants.downloadTableList.contains("card_transaction")) {
                if (isIndependantDownload) {
                    new commonAsyncTaskMaster(mContext, "loyalty_purchase_details", true);
                } else {
                    new commonAsyncTaskMaster(mContext, "loyalty_purchase_details", false);
                }
            }
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "loyalty_purchase_details");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_redeeme_details() {
        Increment_redeeme_details();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.redeemPointURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<RedeemeDetails> schemeList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RedeemeDetails temp = new RedeemeDetails();
                        temp.setExpDate(RowData[0]);
                        if (!RowData[1].isEmpty()) {
                            temp.setPoints(Integer.parseInt(RowData[1]));
                        } else {
                            temp.setPoints(0);
                        }
                        temp.setAward(RowData[2]);
                        schemeList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToRedeemeDetails(schemeList);
        if (insertStatus == noRows || noRows == 0) {
            dbHelper.insertToLogTable(timeStamp, "redeeme_details");
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "redeeme_details");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_rds_master() {
        Increment_rds_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.rdsMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<RDSDetails> rdsList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == 3) {
                        noRows = Integer.parseInt(RowData[0]);
                        noColumn = Integer.parseInt(RowData[2]);
                    } else {
                        if (RowData.length == noColumn) {
                            RDSDetails temp = new RDSDetails();
                            temp.setRdsCode(RowData[0]);
                            temp.setRdsName(RowData[1]);
                            temp.setemp_code(RowData[2]);
                            temp.setRdsType(RowData[3]);
                            temp.setrds_nick_name(RowData[4]);
                            temp.setrds_address(RowData[5]);
                            temp.setrds_pin_code(RowData[6]);
                            temp.setrds_tin_no(RowData[7]);
                            temp.setrds_cst_no(RowData[8]);
                            rdsList.add(temp);
                        }
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToRDSMaster(rdsList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isRDSTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "rds_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isRDSTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_emp_master() {
        Increment_emp_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.otherEmployeeURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<EmployeeMasterDetails> empList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        EmployeeMasterDetails temp = new EmployeeMasterDetails();
                        temp.setEmpCode(RowData[0]);
                        temp.setEmpName(RowData[1]);
                        temp.setSaleAccess(RowData[2]);
                        temp.setReportingTo(RowData[3]);
                        temp.setLevel(RowData[4]);
                        temp.setDesignation(RowData[5]);
                        temp.setVerticalValue(RowData[6]);
                        temp.setBranchCode(RowData[7]);
                        temp.setState(RowData[8]);
                        temp.setZone(RowData[9]);
                        temp.setAcedns(RowData[10]);
                        temp.setLowerLeaves(RowData[11]);
                        temp.setEmail(RowData[12]);
                        temp.setLoginType(RowData[13]);
                        temp.setRegion(RowData[14]);
                        empList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToEmpMaster(empList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isEmployeeUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "emp_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isEmployeeUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_branch_master() {
        Increment_branch_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.branchURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<BranchMasterDetails> mBranchMasterDetailsList = new ArrayList<BranchMasterDetails>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BranchMasterDetails temp = new BranchMasterDetails();
                        temp.setCompanyCode(RowData[0]);
                        temp.setBranchCode(RowData[1]);
                        temp.setBranchName(RowData[2]);
                        temp.setHq(RowData[3]);
                        temp.setPlantName(RowData[4]);
                        temp.setisPlant(RowData[5]);
                        temp.setbranch_state(RowData[6]);
                        mBranchMasterDetailsList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBranchMaster(mBranchMasterDetailsList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBranchUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "branch_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBranchUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_honey_comb_cost() {
        Increment_honey_comb_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.honeyCombURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<HoneyCombandMarginCostDetails> mBranchMasterDetailsList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        HoneyCombandMarginCostDetails temp = new HoneyCombandMarginCostDetails();
                        temp.setprodCode(RowData[0]);
                        temp.settransportMode(RowData[1]);
                        temp.sethoneycombCost(RowData[2]);
                        temp.setplantname(RowData[3]);
                        temp.setstate(RowData[4]);
                        mBranchMasterDetailsList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = 0;
        if (noRows > 0 && noColumn > 0) {
            insertStatus = dbHelper.InsertToHoneyCombMaster(mBranchMasterDetailsList);
        }
        if (insertStatus == noRows && noRows > 0) {
            Constants.isHoneyCombUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isHoneyCombUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_margin_cost() {
        Increment_margin_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.marginCostURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<HoneyCombandMarginCostDetails> MasterDataDetailsList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        HoneyCombandMarginCostDetails temp = new HoneyCombandMarginCostDetails();
                        temp.setprodCode(RowData[0]);
                        temp.setstate(RowData[1]);
                        temp.setMarginCost(RowData[2]);
                        MasterDataDetailsList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = 0;
        if (noRows > 0 && noColumn > 0) {
            insertStatus = dbHelper.InsertToMarginMaster(MasterDataDetailsList);
        }
        if (insertStatus == noRows && noRows > 0) {
            Constants.isMarginCostUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isMarginCostUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_vendor_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.vendorURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes") + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<VendorDetails> vendorList = new ArrayList<VendorDetails>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        VendorDetails temp = new VendorDetails();
                        temp.setVendorCode(RowData[0]);
                        temp.setVendorName(RowData[1]);
                        vendorList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToVendorMaster(vendorList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "vendor_master");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_mis_transaction_log() {
        InCrement_mis_transaction_log();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.misTransactionLogURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<MIS_TransctionDetails> misList = new ArrayList<MIS_TransctionDetails>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MIS_TransctionDetails temp = new MIS_TransctionDetails();
                        temp.setBranchCode(RowData[0]);
                        temp.setRdsCode(RowData[1]);
                        temp.setEmpCode(RowData[2]);
                        temp.setTransDate(RowData[3]);
                        temp.setTransId(RowData[4]);
                        temp.setCustomerCode(RowData[5]);
                        temp.setCustomerName(RowData[6]);
                        temp.setSkuCode(RowData[7]);
                        temp.setQty(RowData[8]);
                        temp.setSaleRate(RowData[9]);
                        temp.setAmount(RowData[10]);
                        temp.setVAT(RowData[11]);
                        temp.setTD(RowData[12]);
                        temp.setTransType(RowData[13]);
                        temp.setdInstruction(RowData[14]);
                        temp.setGroupCode(RowData[15]);
                        misList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToMISTransaction(misList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isMISTransactionTableUpdated = false;
            dbHelper.insertToLogTable(timeStamp, "mis_transaction_log");
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "mis_transaction_log");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isMISTransactionTableUpdated = false;
            dbHelper.insertToLogTable(timeStamp, "mis_transaction_log");
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_user_access() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.userAccessURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes") + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<UserAccessDetails> accessList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        UserAccessDetails temp = new UserAccessDetails();
                        temp.setEmpCode(RowData[0]);
                        temp.setAccessibilityMenu(RowData[1]);
                        accessList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToUserAccessTable(accessList);
        if (insertStatus == noRows || noRows == 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "user_access");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_customer_branch_relation() {
        Increment_customer_branch_relation();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.custBranchURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CustBranchRelationalDetails> custRDSListList = new ArrayList<CustBranchRelationalDetails>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustBranchRelationalDetails temp = new CustBranchRelationalDetails();
                        temp.setCustCode(RowData[0]);
                        temp.setBranchCode(RowData[1]);
                        temp.setAcedns(RowData[2]);
                        custRDSListList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToCustBranchMaster(custRDSListList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustBranchUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "customer_branch_relation");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustBranchUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_sample() {
        Increment_sample();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.sampleDownloadURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SampleDetails> custRDSListList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SampleDetails temp = new SampleDetails();
                        temp.setReferenceNo(RowData[0]);
                        String samplePhoto = RowData[1];
                        temp.setSamplePhoto(samplePhoto);
                        temp.setAcedns(RowData[2]);
                        custRDSListList.add(temp);
                        InputStream inputStream = new HttpCalling().httpGetCallWithInputStreamResponse(BaseUrl.baseUrl + samplePhoto);
                        String[] fileNameAray = samplePhoto.split("/");
                        Download_txt_laravel(inputStream, fileNameAray[fileNameAray.length - 1], mContext);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToSampleMaster(custRDSListList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSampleMasterTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSampleMasterTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_survey_category_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.surveyCategoryDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes") + "&data_download_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SurveyCategoryMaster> mSurveyCategoryMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SurveyCategoryMaster temp = new SurveyCategoryMaster();
                        temp.setRowId(RowData[0]);
                        temp.setCategoryId(RowData[1]);
                        temp.setSubCategoryId(RowData[2]);
                        temp.setCategoryName(RowData[3]);
                        temp.setSubCategoryName(RowData[4]);
                        mSurveyCategoryMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSurveyCategoryMaster(mSurveyCategoryMasterList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "survey_category_master");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else if (noRows == 0) {
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_survey_input_details() {
        Increment_survey_input_details();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.surveyInputDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SurveyInput> mSurveyInputList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SurveyInput temp = new SurveyInput();
                        temp.setSurveyRowId(RowData[0]);
                        temp.setSurveyActionId(RowData[1]);
                        temp.setSurveyMenuId(RowData[2]);
                        temp.setSurveyLayoutName(RowData[3]);
                        temp.setSurveyDisplayName(RowData[4]);
                        temp.setSurveyType(RowData[5]);
                        temp.setSurveyTableName(RowData[6]);
                        temp.setSurveyMadatory(RowData[7]);
                        temp.setSurveyAction(RowData[8]);
                        temp.setSurveyValidation(RowData[9]);
                        temp.setSurveyDisplayOrder(RowData[10]);
                        temp.setSurveySurveyType(RowData[11]);
                        temp.setSurveySubMenu(RowData[12]);
                        temp.setAceDns(RowData[13]);
                        temp.setinsert_table_detail(RowData[14]);
                        mSurveyInputList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSurveyInput(mSurveyInputList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSurveyTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "survey_input_details");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSurveyTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_boq_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.BoqMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<commonDatabaseHelper> mGenericOilMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        temp.setItem7(RowData[7]);
                        temp.setItem8(RowData[8]);
                        temp.setItem9(RowData[9]);
                        temp.setItem10(RowData[10]);
                        mGenericOilMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBoqMaster(mGenericOilMasterList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "BOQ_master");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_customer_proposed_product() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerProposedProductURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<commonDatabaseHelper> mGenericOilMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            int i = 0;
            while ((line = buffer.readLine()) != null) {
                if (i > 2) {
                    String[] RowData = line.split("\\^");
                    commonDatabaseHelper temp = new commonDatabaseHelper();
                    temp.setItem0(RowData[0]);
                    temp.setItem1(RowData[1]);
                    temp.setItem2(RowData[2]);
                    mGenericOilMasterList.add(temp);
                }
                i++;
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        decideNavigation();
    }

    public void DOWNLOAD_gift_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.GiftMasterDownloadURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<commonDatabaseHelper> mGenericOilMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            int i = 0;
            while ((line = buffer.readLine()) != null) {
                if (i > 2) {
                    String[] RowData = line.split("\\^");
                    commonDatabaseHelper temp = new commonDatabaseHelper();
                    temp.setItem0(RowData[0]);
                    temp.setItem1(RowData[1]);
                    temp.setItem2(RowData[2]);
                    temp.setItem3(RowData[3]);
                    temp.setItem4(RowData[4]);
                    temp.setItem5(RowData[5]);
                    mGenericOilMasterList.add(temp);
                }
                i++;
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        decideNavigation();
    }

    public void DOWNLOAD_additional_material_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.AdditionalMaterialURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<commonDatabaseHelper> MasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        MasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToAdditionalMaterialMaster(MasterList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "additional_material");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_generic_oil_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.genericOilDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<GenericOilMaster> mGenericOilMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        GenericOilMaster temp = new GenericOilMaster();
                        temp.setOilName(RowData[0]);
                        temp.setCompetitorName(RowData[1]);
                        mGenericOilMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToGenericOilMaster(mGenericOilMasterList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "generic_oil_master");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_self_appraisal_emp_week_wise() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.weekwiseTargetAchievementDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<EmpTargetAchievementWeekWise> dataList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        EmpTargetAchievementWeekWise temp = new EmpTargetAchievementWeekWise();
                        temp.setemp_code(RowData[0]);
                        temp.setmonth(RowData[1]);
                        temp.setyear(RowData[2]);
                        temp.setweek1_target(RowData[3]);
                        temp.setweek1_ach(RowData[4]);
                        temp.setweek2_target(RowData[5]);
                        temp.setweek2_ach(RowData[6]);
                        temp.setweek3_target(RowData[7]);
                        temp.setweek3_ach(RowData[8]);
                        temp.setweek4_target(RowData[9]);
                        temp.setweek4_ach(RowData[10]);
                        temp.setmonth_target(RowData[11]);
                        temp.setmonth_ach(RowData[12]);
                        dataList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSelfAppraisalEmpWeekWise(dataList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "generic_oil_master");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_van_sales_stock_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.vanSalesStockAllocationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<VanSalesStockAllocationMaster> dataList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        VanSalesStockAllocationMaster temp = new VanSalesStockAllocationMaster();
                        temp.setemp_code(RowData[0]);
                        temp.setprod_code(RowData[1]);
                        temp.setallocated_qty(RowData[2]);
                        temp.setbalance_qty(RowData[3]);
                        temp.setacedns(RowData[4]);
                        dataList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToVanSalesStockAllocation(dataList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_prospective_customer_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.prospectiveCustomerDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<CustomerDetails> prospectiveCustomerMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        try {
                            CustomerDetails temp = new CustomerDetails();
                            temp.setEmpCode(RowData[0]);
                            temp.setCustomerCode(RowData[1]);
                            temp.setCustomerName(RowData[2]);
                            temp.setAddress(RowData[3]);
                            temp.setPin(RowData[4]);
                            temp.setRouteCode(RowData[5]);
                            temp.setNumber(RowData[6]);
                            temp.setCustomerType(RowData[7]);
                            temp.setTaggedCustomerCode(RowData[8]);
                            temp.setDrCategory(RowData[9]);
                            prospectiveCustomerMasterList.add(temp);
                        } catch (Exception ignored) {
                        }
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToProspectiveCustomerMaster(prospectiveCustomerMasterList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_menu_access() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.menuAccessURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<MenuAccess> mMenuAccessList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MenuAccess temp = new MenuAccess();
                        temp.setNotAccessibilityMenu(RowData[0]);
                        mMenuAccessList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus;
        insertStatus = dbHelper.InserttoMenuAccessTable(mMenuAccessList);
        if (insertStatus == noRows || noRows == 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "menu_access");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_sauda_allocation_access() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.saudaAllocationAccessURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaOrTDAllocationAccess> mSaudaAllocationAccessList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    if (noRows != 0 && noColumn != 0) {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            SaudaOrTDAllocationAccess temp = new SaudaOrTDAllocationAccess();
                            temp.setEmployeeCode(RowData[0]);
                            temp.setDesignation(RowData[1]);
                            temp.setDoAllocation(RowData[2]);
                            temp.setGetAllocation(RowData[3]);
                            mSaudaAllocationAccessList.add(temp);
                        }
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = 0;
        if (noRows != 0 && noColumn != 0) {
            insertStatus = dbHelper.InsertToSaudaOrTDAllocationAccess(mSaudaAllocationAccessList, "sauda_allocation_access");
        }
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else if (noRows == 0) {
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_TD_allocation_access() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.TDAllocationAccessURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaOrTDAllocationAccess> mSaudaAllocationAccessList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    if (noRows != 0 && noColumn != 0) {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            SaudaOrTDAllocationAccess temp = new SaudaOrTDAllocationAccess();
                            temp.setEmployeeCode(RowData[0]);
                            temp.setDesignation(RowData[1]);
                            temp.setDoAllocation(RowData[2]);
                            temp.setGetAllocation(RowData[3]);
                            mSaudaAllocationAccessList.add(temp);
                        }
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = 0;
        if (noRows != 0 && noColumn != 0) {
            insertStatus = dbHelper.InsertToSaudaOrTDAllocationAccess(mSaudaAllocationAccessList, "TD_allocation_access");
        }
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else if (noRows == 0) {
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_sauda_allocation_log() {
        Increment_sauda_allocation_log();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.saudaAllocationLogURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SaudaAllocationLog> mSaudaAllocationLogList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SaudaAllocationLog obj = new SaudaAllocationLog();
                        obj.setAllocationId(RowData[0]);
                        obj.setDate(RowData[1]);
                        obj.setEmployeCode(RowData[2]);
                        obj.setProductFilterCode(RowData[3]);
                        obj.setQuantityinLtr(RowData[4]);
                        obj.setQuantityinTon(RowData[5]);
                        mSaudaAllocationLogList.add(obj);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSaudaAllocationLog(mSaudaAllocationLogList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSaudaAllocationUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "sauda_allocation_log");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSaudaAllocationUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_street_master() {
        Increment_street_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.masterStreetLogURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<StreetName> mStreetNameList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        StreetName obj = new StreetName();
                        obj.setStreetName(RowData[0]);
                        obj.setPinCode(RowData[1]);
                        mStreetNameList.add(obj);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToStreetName(mStreetNameList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isStreetUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "street_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isStreetUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_pending_contract() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.pendingContractURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<PendingContract> pendingContractList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        PendingContract temp = new PendingContract();
                        temp.setBranchCode(RowData[0]);
                        temp.setProductGroupCode(RowData[1]);
                        temp.setProductCode(RowData[2]);
                        temp.setCustomerCode(RowData[3]);
                        temp.setBrokerID(RowData[4]);
                        temp.setQuantity0to15(RowData[5]);
                        temp.setQuantity16to30(RowData[6]);
                        temp.setQuantity31to45(RowData[7]);
                        temp.setQuantity46to60(RowData[8]);
                        temp.setQuantityGreater60(RowData[9]);
                        temp.setGreater60Days(RowData[10]);
                        temp.setContractQuantity(RowData[11]);
                        temp.setDespatchQuantity(RowData[12]);
                        temp.setPendingQuantity(RowData[13]);
                        pendingContractList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToPendingContract(pendingContractList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "pending_contract");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_mall_survey_relation() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.mallSurveyRelationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<MallSurveyRelation> mMallSurveyRelationList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MallSurveyRelation temp = new MallSurveyRelation();
                        temp.setMenuId(RowData[0]);
                        temp.setRowId(RowData[1]);
                        temp.setMallInfo(RowData[2]);
                        temp.setType(RowData[3]);
                        mMallSurveyRelationList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToMallSurveyRelationMaster(mMallSurveyRelationList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "mall_survey_relation");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_prev_order_counting_master() {
        Increment_prev_order_counting_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.previousOrderCountingURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<PreviousOrderCounting> mPreviousOrderCountingList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        PreviousOrderCounting temp = new PreviousOrderCounting();
                        temp.setCustomerCode(RowData[0]);
                        temp.setProductCode(RowData[1]);
                        temp.setVisitDetails(RowData[2]);
                        mPreviousOrderCountingList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToPreviousOrderCountingMaster(mPreviousOrderCountingList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isPreviousOrderCounting = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "prev_order_counting_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isPreviousOrderCounting = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_farmer_master() {
        Increment_farmer_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.farmerMasterDownloadURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<FarmerMasterDetails> mPreviousOrderCountingList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        FarmerMasterDetails temp = new FarmerMasterDetails();
                        temp.setfarmerId(RowData[0]);
                        temp.setempCode(RowData[1]);
                        temp.setfarmerName(RowData[2]);
                        temp.setaddress(RowData[3]);
                        temp.setdistrict(RowData[4]);
                        temp.settaluka(RowData[5]);
                        temp.setlocality(RowData[6]);
                        temp.setpin(RowData[7]);
                        temp.setfarming_area(RowData[8]);
                        temp.settype_of_corps(RowData[9]);
                        temp.setmi_applied_for(RowData[10]);
                        temp.setspacing(RowData[11]);
                        temp.setchitta_andangal(RowData[12]);
                        temp.setland_suervey_map(RowData[13]);
                        temp.setsmall_farmer_certificate(RowData[14]);
                        temp.setphoto(RowData[15]);
                        temp.setaadhar(RowData[16]);
                        temp.setBOQ_generated(RowData[17]);
                        temp.setquotation_generated(RowData[18]);
                        temp.setdocument_uploaded_to_TANHODA(RowData[19]);
                        temp.settotal_area(RowData[20]);
                        temp.setmi_area(RowData[21]);
                        temp.setlinked_dealer(RowData[22]);
                        temp.setmi_id(RowData[23]);
                        temp.setfarmer_type(RowData[24]);
                        temp.setmi_reference_no(RowData[25]);
                        temp.setfittings_accessories(RowData[26]);
                        temp.setadditional_material(RowData[27]);
                        mPreviousOrderCountingList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToFarmerMaster(mPreviousOrderCountingList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isFarmerMaster = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "farmer_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isFarmerMaster = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_order_status() {
        Increment_order_status();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.orderStatusURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<OrderStatus> mOrderStatusList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        OrderStatus temp = new OrderStatus();
                        temp.setOrderNo(RowData[0]);
                        temp.setCustomerCode(RowData[1]);
                        temp.setProductCode(RowData[2]);
                        temp.setOrderQuantity(RowData[3]);
                        temp.setAlreadyDeliveredQuantity(RowData[4]);
                        temp.setStatus(RowData[5]);
                        temp.setRemarks(RowData[6]);
                        temp.setFlag(RowData[7]);
                        temp.setrate(RowData[8]);
                        temp.setamount(RowData[9]);
                        temp.setweightage(RowData[10]);
                        mOrderStatusList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToOrderStatusMaster(mOrderStatusList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isOrderStatus = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "order_status");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isOrderStatus = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_outstanding_ageing() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.outstandingAgeingURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<OutstandingAgeing> mOutstandingAgeingList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        OutstandingAgeing temp = new OutstandingAgeing();
                        temp.setCustomerCode(RowData[0]);
                        temp.setCustomerName(RowData[1]);
                        temp.setOutstandingAmount(RowData[2]);
                        temp.setOutstanding0to15(RowData[3]);
                        temp.setOutstanding16to30(RowData[4]);
                        temp.setOutstanding31to45(RowData[5]);
                        temp.setOutstanding46to90(RowData[6]);
                        temp.setOutstandingGreater90(RowData[7]);
                        mOutstandingAgeingList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToOutStandingAgeingMaster(mOutstandingAgeingList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "outstanding_ageing");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void downloadSaudaMrp() {
        Increment_sauda_mrp();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.saudaMrpURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaMrp> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SaudaMrp temp = new SaudaMrp();
                        temp.setSKUCode(RowData[0]);
                        temp.setMrpCode(RowData[1]);
                        temp.setMrpValue(RowData[2]);
                        temp.setSaleRate(RowData[3]);
                        temp.setUOM(RowData[4]);
                        temp.setBranchCode(RowData[5]);
                        temp.setbasic_rate(RowData[6]);
                        temp.setprimary_freight(RowData[7]);
                        temp.setdepot_cost(RowData[8]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSaudaMRPMaster(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSaudaMrpTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "sauda_mrp");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSaudaMrpTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void downloadBargainMrp() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.bargainMrpURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaMrp> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SaudaMrp temp = new SaudaMrp();
                        temp.setSKUCode(RowData[0]);
                        temp.setMrpCode(RowData[1]);
                        temp.setSaleRate(RowData[2]);
                        temp.setBranchCode(RowData[3]);
                        temp.setbasic_rate(RowData[4]);
                        temp.setprimary_freight(RowData[5]);
                        temp.setdepot_cost(RowData[6]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBargainMRPMaster(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSaudaMrpTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "sauda_mrp");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSaudaMrpTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void downloadGrnDo() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.grnDoDownloadURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> dataList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        dataList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
    }

    public void customerProductRelation() {
        Increment_customer_product_relation();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerProductRelationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CustomerProductRelationDetails> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerProductRelationDetails temp = new CustomerProductRelationDetails();
                        temp.setcustomerCode(RowData[0]);
                        temp.setProductCode(RowData[1]);
                        temp.setacedns(RowData[2]);
                        temp.setmcxRateParam(RowData[3]);
                        temp.setpremium(RowData[4]);
                        temp.setTD(RowData[5]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToCustomerProductRelation(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustomerProductTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "sauda_mrp");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustomerProductTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void customer_product_info() {
        Increment_customer_product_info();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerProductInfoURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        temp.setItem7(RowData[7]);
                        temp.setItem8(RowData[8]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToCustomerProductInfo(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustomerProductInfoTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "customer_product_info");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustomerProductInfoTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void mcxRate() {
        Increment_mcx_rate();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.mcxRateIncrementalURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        temp.setItem7(RowData[7]);
                        temp.setItem8(RowData[8]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToMxcRate(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isMcxRateTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "mcx_rate");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isMcxRateTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void orderApproval() {
        Increment_order_approval();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.OrderApprovalIncrementalURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> masterList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        temp.setItem7(RowData[7]);
                        temp.setItem8(RowData[8]);
                        temp.setItem9(RowData[9]);
                        temp.setItem10(RowData[10]);
                        temp.setItem11(RowData[11]);
                        temp.setItem12(RowData[12]);
                        temp.setItem13(RowData[13]);
                        temp.setItem14(RowData[14]);
                        temp.setItem15(RowData[15]);
                        temp.setItem16(RowData[16]);
                        temp.setItem17(RowData[17]);
                        temp.setItem18(RowData[18]);
                        temp.setItem19(RowData[19]);
                        temp.setItem20(RowData[20]);
                        temp.setItem21(RowData[21]);
                        temp.setItem22(RowData[22]);
                        temp.setItem23(RowData[23]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToOrderApproval(masterList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isOrderApprovalTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "order_approval");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isOrderApprovalTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void technicalMeetApproval() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.TechnicalMeetApprovalURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> masterList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        temp.setItem7(RowData[7]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToTechnicalMeetApproval(masterList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "technical_meet_approval");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void technicalMeetApprovalStatus() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.TechnicalMeetApprovalDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> masterList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToTechnicalMeetApprovalStatus(masterList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "technical_meet_approval_status");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void branchDestination() {
        Increment_branch_destination();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.BranchDestinationIncrementalURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> masterList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBranchDestination(masterList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBranchDestinationTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "branch_destination");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBranchDestinationTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void branchDump() {
        Increment_branch_dump();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.BranchDumpIncrementalURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> masterList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBranchDump(masterList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBranchDumpTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "branch_dump");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBranchDumpTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void customer_broker_relation() {
        Increment_customer_broker_relation();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerBrokerRelationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToCustomerBrokerRelation(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustomerBrokerRelationTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "customer_broker_relation");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustomerBrokerRelationTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void brokerage_cost() {
        Increment_brokerage_cost();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.BrokerageCostURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<commonDatabaseHelper> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBrokerageCost(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBrokarageCostTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "brokerage_cost");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBrokarageCostTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void bargainTransactionDownload() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.bargainTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaDetails> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SaudaDetails temp = new SaudaDetails();
                        temp.setSaudaNo(RowData[0]);
                        temp.setCustomerCode(RowData[1]);
                        temp.setbranchCode(RowData[2]);
                        temp.setSkuCode(RowData[3]);
                        temp.setQuantity(RowData[4]);
                        temp.setSaleRate(RowData[5]);
                        temp.setAmount(RowData[6]);
                        temp.setStatus(RowData[7]);
                        temp.setIncoterms(RowData[8]);
                        temp.setmapped_prod_code(RowData[9]);
                        temp.setdns_sauda_no(RowData[10]);
                        temp.setFreightCharge(RowData[11]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBargainTransaction(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "bargain_transaction");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void bargainReportDownload() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.bargainReportDataDownloadModeWiseURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&mode=" + Constants.mode + "&from_date=" + Constants.from_date + "&to_date=" + Constants.to_date;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        OrderReportDetails temp = new OrderReportDetails();
                        temp.setcustomerName(RowData[1]);
                        temp.setcustomerCode(RowData[0]);
                        temp.setTransactionId(RowData[2]);
                        temp.setQuantity(RowData[3]);
                        temp.setAmount(Utils.addAllItemsOfAnArray(RowData[4]));
                        mOrderReportDetailsListGlobal.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
    }

    public void doReportDownload() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.doReportDataDownloadModeWiseURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&mode=" + Constants.mode + "&from_date=" + Constants.from_date + "&to_date=" + Constants.to_date;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        OrderReportDetails temp = new OrderReportDetails();
                        temp.setcustomerName(RowData[1]);
                        temp.setcustomerCode(RowData[0]);
                        temp.setTransactionId(RowData[2]);
                        temp.setQuantity(RowData[3]);
                        temp.setAmount(Utils.addAllItemsOfAnArray(RowData[4]));
                        mOrderReportDetailsListGlobal.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
    }

    public void bargainReportDownloadLevel2() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.bargainReportDataDownloadModeWiseSaudaListURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&mode=" + Constants.mode + "&from_date=" + Constants.from_date + "&to_date=" + Constants.to_date + "&customer_code=" + Constants.customerCode;
        Download_txt(URL);
        int saudaCount = 1;
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    if (line.contains("FT")) {
                        saudaCount = bargainReportLevel3(saudaCount, line);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
    }

    public void doReportDownloadLevel2() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.doReportDataDownloadModeWiseSaudaListURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&mode=" + Constants.mode + "&from_date=" + Constants.from_date + "&to_date=" + Constants.to_date + "&customer_code=" + Constants.customerCode;
        Download_txt(URL);
        int doCount = 1;
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    if (line.contains("DO"))
                        doCount = doReportLevel3(doCount, line);
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
    }

    public int bargainReportLevel3(int saudaCount, String line) {
        mOrderReportDetailsListGlobal = new ArrayList<>();
        String URL;
        String saudaNo = line;
        String substringTrsnId = saudaNo.substring(7);
        String dateInString = Utils.changeDateFormat("yyyyMMddhhmmss", "dd/MM/yyyy hh:mm:ss", substringTrsnId);
        String ListItemHeader = "Bargain " + saudaCount + "- " + dateInString;
        listDataHeaderGlobal.add(ListItemHeader);
        URL = BaseUrl.baseUrl + AceDnsWebServiceURL.bargainReportDataDownloadModeWiseSaudaListWithDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&mode=" + Constants.mode + "&from_date=" + Constants.from_date + "&to_date=" + Constants.to_date + "&sauda_no=" + saudaNo;
        Download_txt_with_custom_name(URL, status + "2");
        File csvFile2 = new File(Utils.getAppStoragePath(mContext) + status + "2.txt");
        FileReader file2 = null;
        try {
            file2 = new FileReader(csvFile2);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer2 = new BufferedReader(file2);
        try {
            String line2 ;
            while ((line2 = buffer2.readLine()) != null) {
                if (line2.indexOf("¥") > 0) {
                    String[] dataArray = line2.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line2.indexOf("€") > 0) {
                    timeStamp = line2;
                } else {
                    String[] RowData = line2.split("\\^");
                    if (RowData.length == noColumn) {
                        OrderReportDetails temp = new OrderReportDetails();
                        temp.setcustomerName(RowData[1]);
                        temp.setcustomerCode(RowData[0]);
                        temp.setTransactionId(RowData[2]);
                        temp.setQuantity(RowData[5]);
                        temp.setAmount(RowData[6]);
                        temp.setdesc(RowData[4]);
                        temp.setisApproved(Boolean.parseBoolean(RowData[7]));
                        mOrderReportDetailsListGlobal.add(temp);
                    }
                }
            }
            listChildDataGlobal.put(ListItemHeader, mOrderReportDetailsListGlobal);
            buffer2.close();
        } catch (IOException ignored) {
        }
        saudaCount++;
        return saudaCount;
    }

    public int doReportLevel3(int doCount, String line) {
        mOrderReportDetailsListGlobal = new ArrayList<>();
        String URL;
        String doNo = line;
        String substringTrsnId = doNo.substring(7);
        String dateInString = Utils.changeDateFormat("yyyyMMddhhmmss", "dd/MM/yyyy hh:mm:ss", substringTrsnId);
        String ListItemHeader = "DO " + doCount + "- " + dateInString;
        listDataHeaderGlobal.add(ListItemHeader);
        URL = BaseUrl.baseUrl + AceDnsWebServiceURL.doReportDataDownloadModeWiseSaudaListWithDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&mode=" + Constants.mode + "&from_date=" + Constants.from_date + "&to_date=" + Constants.to_date + "&DO_no=" + doNo;
        Download_txt_with_custom_name(URL, status + "2");
        File csvFile2 = new File(Utils.getAppStoragePath(mContext) + status + "2.txt");
        FileReader file2 = null;
        try {
            file2 = new FileReader(csvFile2);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer2 = new BufferedReader(file2);
        try {
            String line2 ;
            while ((line2 = buffer2.readLine()) != null) {
                if (line2.indexOf("¥") > 0) {
                    String[] dataArray = line2.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line2.indexOf("€") > 0) {
                    timeStamp = line2;
                } else {
                    String[] RowData = line2.split("\\^");
                    if (RowData.length == noColumn) {
                        OrderReportDetails temp = new OrderReportDetails();
                        temp.setcustomerName(RowData[1]);
                        temp.setcustomerCode(RowData[0]);
                        temp.setTransactionId(RowData[2]);
                        temp.setQuantity(RowData[5]);
                        temp.setAmount(RowData[6]);
                        temp.setdesc(RowData[4]);
                        temp.setisApproved(false);
                        mOrderReportDetailsListGlobal.add(temp);
                    }
                }
            }
            listChildDataGlobal.put(ListItemHeader, mOrderReportDetailsListGlobal);
            buffer2.close();
        } catch (IOException ignored) {
        }
        doCount++;
        return doCount;
    }

    public void depotCostDownload() {
        Increment_depot_cost();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.depotCostURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaDetails> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SaudaDetails temp = new SaudaDetails();
                        temp.setSkuCode(RowData[0]);
                        temp.setbranchCode(RowData[1]);
                        temp.setDepotCost(RowData[2]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToDepotCost(saudaMrpList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBargainTransactionTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "depot_cost");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBargainTransactionTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void freightCostDownload() {
        Increment_freight_cost();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.primaryFreightURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaDetails> saudaMrpList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SaudaDetails temp = new SaudaDetails();
                        temp.setSkuCode(RowData[0]);
                        temp.setbranchCode(RowData[1]);
                        temp.setPrimaryFreight(RowData[2]);
                        temp.settransportMode(RowData[3]);
                        temp.settrackLoad(RowData[4]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToPrimaryFreightCost(saudaMrpList);

        if (insertStatus == noRows && noRows > 0) {
            Constants.isFreightCostTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "sauda_mrp");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isFreightCostTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_sale_performance() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.salesPerformanceURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SalesPerformance> pendingContractList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SalesPerformance temp = new SalesPerformance();
                        temp.setCustomerCode(RowData[0]);
                        temp.setCustomerName(RowData[1]);
                        temp.setEmployeeCode(RowData[2]);
                        temp.setEmployeeName(RowData[3]);
                        temp.setProductGroupCode(RowData[4]);
                        temp.setProductCode(RowData[5]);
                        temp.setProductDescription(RowData[6]);
                        temp.setYTDSale(RowData[7]);
                        temp.setMTDSale(RowData[8]);
                        pendingContractList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSalesPerformance(pendingContractList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "sale_performance");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_emp_menu_access() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.employeemenuAccessURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<EmployeeMenuAccess> mEmployeeMenuAccessList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        EmployeeMenuAccess temp = new EmployeeMenuAccess();
                        temp.setMenu(RowData[0]);
                        mEmployeeMenuAccessList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus;
        insertStatus = dbHelper.InserttoEmployeeMenuAccessTable(mEmployeeMenuAccessList);
        if (insertStatus == noRows || noRows == 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "emp_menu_access");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_customer_product_wise_orderplan() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.CustomerProductWiseOrderPlan + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<CustomerProductWiseOrderPlanDetails> mCustomerProductWiseOrderPlanDetailsList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerProductWiseOrderPlanDetails temp = new CustomerProductWiseOrderPlanDetails();
                        temp.setcutomerCode(RowData[0]);
                        temp.setProdCode(RowData[1]);
                        temp.setMonth(RowData[2]);
                        temp.setPurchase(RowData[3]);
                        temp.setPlan(RowData[4]);
                        mCustomerProductWiseOrderPlanDetailsList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToCustomerProductWiseOrderPlan(mCustomerProductWiseOrderPlanDetailsList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "customer_product_wise_orderplan");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_survey_publish() {
        Increment_survey_publish();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.surveyPublishURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SurveyPublish> surveyPublishList = new ArrayList<SurveyPublish>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SurveyPublish temp = new SurveyPublish();
                        temp.setSurveyId(RowData[0]);
                        temp.setMallId(RowData[1]);
                        temp.setRowId(RowData[2]);
                        temp.setActionId(RowData[3]);
                        temp.setValue(RowData[4]);
                        temp.setStatus(RowData[5]);
                        surveyPublishList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSurveyPublish(surveyPublishList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSurveyPublishTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "survey_publish");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSurveyPublishTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_offer_publish() {
        Increment_offer_publish();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.offerPublishURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SurveyPublish> surveyPublishList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SurveyPublish temp = new SurveyPublish();
                        temp.setSurveyId(RowData[0]);
                        temp.setMallId(RowData[1]);
                        temp.setRowId(RowData[2]);
                        temp.setActionId(RowData[3]);
                        temp.setValue(RowData[4]);
                        temp.setStatus(RowData[5]);
                        surveyPublishList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToOfferPublish(surveyPublishList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isOfferPublishTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "offer_publish");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isOfferPublishTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_non_trade_customer_master() {
        Increment_non_trade_customer_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.nonTradeCustomerURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CustomerDetails> customerList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerDetails temp = new CustomerDetails();
                        temp.setCustomerCode(RowData[0]);
                        temp.setCustomerName(RowData[1]);
                        temp.setAddress(RowData[2]);
                        temp.setNumber(RowData[3]);
                        temp.setRouteCode(RowData[4]);
                        temp.setEmpCode(RowData[5]);
                        temp.setRdsTag(RowData[6]);
                        customerList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToNonTradeCustomerMaster(customerList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isNonTradeCustomer = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "non_trade_customer_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isNonTradeCustomer = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_distributor_route_relation() {
        Increment_distributor_route_relation();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.distributorRouteDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RouteDetails> mRouteDetailsList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RouteDetails temp = new RouteDetails();
                        temp.setDistributorCode(RowData[0]);
                        temp.setRouteCode(RowData[1]);
                        temp.setEmployeeCode(RowData[2]);
                        temp.setacedns(RowData[3]);
                        temp.setvisitDay(RowData[4]);
                        mRouteDetailsList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToDistributorRouteMaster(mRouteDetailsList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isDistributorRouteTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn == 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "distributor_route_relation");
        } else if (noRows == 0) {
            Constants.isDistributorRouteTableUpdated = false;
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void customerToDelete() {
        Increment_distributor_route_relation();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerToDeleteURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<String> customerToDeleteList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        customerToDeleteList.add(RowData[0]);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        if (!customerToDeleteList.isEmpty()) {
            String commaseparatedCustomerList = android.text.TextUtils.join(", ", customerToDeleteList);
            commaseparatedCustomerList = convertCommaSeparatedListToProperFormat(commaseparatedCustomerList);
            boolean isDeletionSuccess = dbHelper.deleteCustomerAndRelatedData(commaseparatedCustomerList);
            if (isDeletionSuccess) {
                customerDeleteConfirmationApi(commaseparatedCustomerList);
            }
        }
    }

    private void customerDeleteConfirmationApi(String commaseparatedCustomerList) {
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
        values.put("customer_code", commaseparatedCustomerList);
        HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.customerDeleteConfirmationURL, values);
    }

    public void _DOWNLOAD_catalogue_info() {
        Increment_catalogue_info();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.catalogueDownloaderUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CatalogueInfoDetails> catalogueList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CatalogueInfoDetails temp = new CatalogueInfoDetails();
                        temp.setVertical(RowData[0]);
                        temp.setFileName(RowData[1]);
                        temp.setFileVersion(RowData[2]);
                        catalogueList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToCatalogueInfo(catalogueList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "catalogue_info");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        }
    }

    public void _DOWNLOAD_facilitator_master() {
        Increment_facilitator_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.facilitatorMasterDownloaderUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<FacilitatorMasterDetails> catalogueList = new ArrayList<>();
        ArrayList<Integer> a = new ArrayList<>();
        ArrayList<Integer> b = new ArrayList<>();
        ArrayList<String> c = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            int ai = 0;
            while ((line = buffer.readLine()) != null) {
                ai = ai + 1;
                if (line.indexOf("¥") > 0) {
                    a.add(1);
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    b.add(1);
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        c.add(RowData[0]);
                        FacilitatorMasterDetails temp = new FacilitatorMasterDetails();
                        temp.setf_code(RowData[0]);
                        temp.setemp_code(RowData[1]);
                        temp.setfaclitator_name(RowData[2]);
                        temp.setf_type(RowData[3]);
                        temp.setfirm_name(RowData[4]);
                        temp.setf_address(RowData[5]);
                        temp.setf_pin(RowData[6]);
                        temp.setf_area(RowData[7]);
                        temp.setf_sub_area(RowData[8]);
                        temp.setmobile_no(RowData[9]);
                        temp.setemail_id(RowData[10]);
                        temp.setdob(RowData[11]);
                        temp.setannniversary(RowData[12]);
                        temp.setacedns(RowData[13]);
                        temp.setbranch_code(RowData[14]);
                        temp.setcheck_in_date(RowData[15]);
                        temp.setf_category(RowData[16]);
                        temp.setf_city(RowData[17]);
                        temp.setf_state(RowData[18]);
                        temp.setdesignation(RowData[19]);
                        temp.setnature_of_work(RowData[20]);
                        temp.setVisit_count(RowData[21]);
                        temp.setF_status(RowData[22]);
                        temp.setArchitect_name(RowData[23]);
                        temp.setType_of_work(RowData[24]);
                        temp.setRunning_sites(RowData[25]);
                        temp.setProduct_required(RowData[26]);
                        temp.setSample_required(RowData[27]);
                        temp.setSample_status(RowData[28]);
                        temp.setF_conversion(RowData[29]);
                        temp.setF_product(RowData[30]);
                        temp.setF_product_qty(RowData[31]);
                        temp.setF_product_value(RowData[32]);
                        temp.setF_product_thickness(RowData[33]);
                        temp.setF_history(RowData[34]);
                        temp.setNext_follow_up_date(RowData[35]);
                        catalogueList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToFacilitatorMaster(catalogueList);
        if (insertStatus == c.size() && noRows > 0) {
            Constants.isFacilitatorTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isFacilitatorTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_site_master() {
        Increment_site_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.siteMasterDownloaderUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SiteMasterDetails> siteMasterList = new ArrayList<>();
        FileReader file = null;
        int ai = 0;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ai = ai + 1;
                        SiteMasterDetails temp = new SiteMasterDetails();
                        temp.setsite_id(RowData[0]);
                        temp.setemp_code(RowData[1]);
                        temp.setsite_name(RowData[2]);
                        temp.setaddress(RowData[3]);
                        temp.setcity(RowData[4]);
                        temp.setlocation(RowData[5]);
                        temp.setsub_area(RowData[6]);
                        temp.setstate(RowData[7]);
                        temp.setpin(RowData[8]);
                        temp.setcontact_person(RowData[9]);
                        temp.setphone_no(RowData[10]);
                        temp.setsite_reffered_by(RowData[11]);
                        temp.setsq_feet_area(RowData[12]);
                        temp.setfacilitator_mapped(RowData[13]);
                        temp.setproject_type(RowData[14]);
                        temp.setcurrent_status(RowData[15]);
                        temp.setacedns(RowData[16]);
                        temp.setproduct_info(RowData[17]);
                        temp.setproduct_category(RowData[18]);
                        temp.setcheck_in_date(RowData[19]);
                        temp.setdealer_involved(RowData[20]);
                        temp.setsub_dealer_involved(RowData[21]);
                        temp.setstage_of_construction(RowData[22]);
                        temp.setmaterial_sold_value(RowData[23]);
                        temp.setdesc_thickness_qty(RowData[24]);
                        temp.setveneer_species(RowData[25]);
                        temp.setveneer_qty(RowData[26]);
                        temp.setremarks(RowData[27]);
                        temp.setscope_of_teak(RowData[28]);
                        temp.setscope_of_NTD(RowData[29]);
                        temp.setfollow_up_date(RowData[30]);
                        temp.setexpected_month_maturity(RowData[31]);
                        temp.setescalation_clause(RowData[32]);
                        temp.setauth_retailer_involved(RowData[33]);
                        temp.setarchitect_involved(RowData[34]);
                        temp.setcontractor_involved(RowData[35]);
                        temp.setarea(RowData[36]);
                        temp.setdesignation(RowData[37]);
                        temp.setemail(RowData[38]);
                        temp.setcontact_person_type(RowData[39]);
                        temp.setSite_owner_name(RowData[40]);
                        temp.setSite_owner_contact(RowData[41]);
                        temp.setVisit_count(RowData[42]);
                        temp.setArchitect_name(RowData[43]);
                        temp.setRunning_sites(RowData[44]);
                        temp.setProduct_required(RowData[45]);
                        temp.setSample_status(RowData[46]);
                        temp.setSample_require(RowData[47]);
                        temp.setAssociation(RowData[48]);
                        temp.setLast_meeting_remakrs(RowData[49]);
                        siteMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToSiteMaster(siteMasterList);
        if (insertStatus == ai && noRows > 0) {
            Constants.isSiteTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSiteTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_self_appraisal_productgroup_wise() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.productGroupWiseTargetAchievement + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SelfAppraisalDetailsProductGroupWise> mSelfAppraisalProductGroupWiseList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SelfAppraisalDetailsProductGroupWise temp = new SelfAppraisalDetailsProductGroupWise();
                        temp.setproductGroupCode(RowData[0]);
                        temp.setproductGroupName(RowData[1]);
                        temp.setempCode(RowData[2]);
                        temp.setmonth(RowData[3]);
                        temp.settarget(RowData[4]);
                        temp.setachievement(RowData[5]);
                        mSelfAppraisalProductGroupWiseList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToProductGroupWiseTargetAchievement(mSelfAppraisalProductGroupWiseList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "self_appraisal_productgroup_wise");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_state_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.stateMasterUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SelfAppraisalDetailsProductGroupWise> mSelfAppraisalProductGroupWiseList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SelfAppraisalDetailsProductGroupWise temp = new SelfAppraisalDetailsProductGroupWise();
                        temp.setproductGroupCode(RowData[0]);
                        temp.setproductGroupName(RowData[1]);
                        mSelfAppraisalProductGroupWiseList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToStateMaster(mSelfAppraisalProductGroupWiseList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_yellow_card_date_validation() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.yellowCardValidationDate + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<YellowCardDateValidation> mSelfAppraisalProductGroupWiseList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        YellowCardDateValidation temp = new YellowCardDateValidation();
                        temp.setvalidationMonth(RowData[0]);
                        temp.setvalidationDate(RowData[1]);
                        mSelfAppraisalProductGroupWiseList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToYellowCardValidationMonth(mSelfAppraisalProductGroupWiseList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_scheme_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.schemeMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SchemeFreebiesDetails> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SchemeFreebiesDetails temp = new SchemeFreebiesDetails();
                        temp.setschemeId(RowData[0]);
                        temp.setstartDate(RowData[1]);
                        temp.setendDate(RowData[2]);
                        temp.setprodCode(RowData[3]);
                        temp.setqty(RowData[4]);
                        temp.setamount(RowData[5]);
                        temp.setscheme_type(RowData[6]);
                        temp.setscheme_filter(RowData[7]);
                        temp.setProductUom(RowData[8]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSchemeMaster(masterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_freebies_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.freebieMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SchemeFreebiesDetails> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SchemeFreebiesDetails temp = new SchemeFreebiesDetails();
                        temp.setschemeId(RowData[0]);
                        temp.setfreebiesProdCode(RowData[1]);
                        temp.setfreebiesProdDesc(RowData[2]);
                        temp.setqty(RowData[3]);
                        temp.setvaluePercent(RowData[4]);
                        temp.setvalueAmount(RowData[5]);
                        temp.setFreebieUom(RowData[6]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToFreebiesMaster(masterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_plant_product_wise_RA_rate() {
        try {
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RASaudaRateDownload, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<PlantProductWiseRARate> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        PlantProductWiseRARate temp = new PlantProductWiseRARate();
                        temp.setPlantName(RowData[0]);
                        temp.setProdCode(RowData[1]);
                        temp.setReleaseRate(RowData[2]);
                        temp.setAcedns(RowData[3]);
                        temp.setBaseRate(RowData[4]);
                        temp.setIndicativeRateServer(RowData[5]);
                        temp.setgstPercent(RowData[6]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (Exception ignored) {
        }
        dbHelper.InsertToPlantProductWiseRARate(masterList);
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            dbHelper.GetReverseAuctionCustomers();
            if (!customerDetailsListReverseAuction.isEmpty()) {
                Intent intent = new Intent(mContext, ReverseAuctionActivity.class);
                mContext.startActivity(intent);
            } else {
                customerDetailsListReverseAuction.size();
                Utils.ShowAlertDialogCommon(mContext, "Please Note!", "No customer found for auction! Please Synchronize Data.", "OK");
            }
        });
    }

    public void DOWNLOAD_RA_route_freight() {
        try {
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RARouteFreight, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RARouteFreight> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RARouteFreight temp = new RARouteFreight();
                        temp.setPlantName(RowData[0]);
                        temp.setroute_code(RowData[1]);
                        temp.setzone(RowData[2]);
                        temp.setTransportMode(RowData[3]);
                        temp.setcapacity(RowData[4]);
                        temp.setfreight(RowData[5]);
                        temp.setVertical(RowData[6]);
                        temp.setbranch_code(RowData[7]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (Exception ignored) {
        }
        dbHelper.InsertToRARouteFreight(masterList);
    }

    public void DOWNLOAD_plant_product_wise_RA_rate_accepted() {
        String status = "bid_rate_accepted";
        try {
            lastUpdate = dbHelper.getlastDownloadTime("RA_bid_rate_details_accepted");
            if (lastUpdate.matches("")) {
                lastUpdate = "1971-01-01€10:10:10";
            }
            lastUpdate = lastUpdate.replace("€", " ");
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            jo.put("last_update_time", MCrypt.bytesToHex(mcrypt.encrypt(lastUpdate)));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RASaudaAcceptedBidRateDownload, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<PlantProductWiseRARate> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        PlantProductWiseRARate temp = new PlantProductWiseRARate();
                        temp.setBidId(RowData[0]);
                        temp.setPlantName(RowData[1]);
                        temp.setProdCode(RowData[2]);
                        temp.setReleaseRate(RowData[3]);
                        temp.setBaseRate(RowData[4]);
                        temp.setIndicativeRateServer(RowData[5]);
                        temp.setIndicativeRateApp(RowData[6]);
                        temp.setCustomerCode(RowData[7]);
                        temp.setQty(RowData[8]);
                        temp.setbidPrice(RowData[9]);
                        temp.setCounterBid(RowData[10]);
                        temp.setCounterBidRate(RowData[11]);
                        temp.setCounterBidStatus(RowData[12]);
                        temp.setPrimaryFreight(RowData[13]);
                        temp.setSecondaryFreight(RowData[14]);
                        temp.setDepotCost(RowData[15]);
                        temp.setgstPercent(RowData[16]);
                        temp.setgstValue(RowData[17]);
                        temp.setbranchCode(RowData[18]);
                        temp.setuserChosenIncoterms(RowData[19]);
                        temp.setverticalOfEmployee(RowData[20]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
            if (!masterList.isEmpty()) {
                dbHelper.InsertToRABidRateDetails(masterList);
                add_last_update_time("RA_bid_rate_details_accepted");
            }
        } catch (Exception ignored) {
        }
        DOWNLOAD_plant_product_wise_RA_rate_counter_bid_accepted();
    }

    public void DOWNLOAD_plant_product_wise_RA_rate_counter_bid_accepted() {
        String status = "counter_bid_rate_accepted";
        try {
            lastUpdate = dbHelper.getlastDownloadTime(status);
            if (lastUpdate.matches("")) {
                lastUpdate = "1971-01-01€10:10:10";
            }
            lastUpdate = lastUpdate.replace("€", " ");
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            jo.put("last_update_time", MCrypt.bytesToHex(mcrypt.encrypt(lastUpdate)));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RASaudaCounterBidAcceptedBidRateDownload, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<PlantProductWiseRARate> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        PlantProductWiseRARate temp = new PlantProductWiseRARate();
                        temp.setBidId(RowData[0]);
                        temp.setPlantName(RowData[1]);
                        temp.setProdCode(RowData[2]);
                        temp.setReleaseRate(RowData[3]);
                        temp.setBaseRate(RowData[4]);
                        temp.setIndicativeRateServer(RowData[5]);
                        temp.setIndicativeRateApp(RowData[6]);
                        temp.setCustomerCode(RowData[7]);
                        temp.setQty(RowData[8]);
                        temp.setbidPrice(RowData[9]);
                        temp.setCounterBid(RowData[10]);
                        temp.setCounterBidRate(RowData[11]);
                        temp.setCounterBidStatus(RowData[12]);
                        temp.setPrimaryFreight(RowData[13]);
                        temp.setSecondaryFreight(RowData[14]);
                        temp.setDepotCost(RowData[15]);
                        temp.setgstPercent(RowData[16]);
                        temp.setgstValue(RowData[17]);
                        temp.setbranchCode(RowData[18]);
                        temp.setuserChosenIncoterms(RowData[19]);
                        temp.setverticalOfEmployee(RowData[20]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
            if (!masterList.isEmpty()) {
                dbHelper.InsertToRABidRateDetails(masterList);
                add_last_update_time(status);
            }
        } catch (Exception ignored) {
        }
        DOWNLOAD_RA_Window_Time_Download();
    }

    public void DOWNLOAD_RA_margin_rate() {
        String status = "RA_margin_rate";
        try {
            lastUpdate = dbHelper.getlastDownloadTime(status);
            if (lastUpdate.matches("")) {
                lastUpdate = "1971-01-01€10:10:10";
            }
            lastUpdate = lastUpdate.replace("€", " ");
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            jo.put("last_update_time", MCrypt.bytesToHex(mcrypt.encrypt(lastUpdate)));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RAMarginDownload, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RAMarginCostDetails> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RAMarginCostDetails temp = new RAMarginCostDetails();
                        temp.setprodCode(RowData[0]);
                        temp.setstate(RowData[1]);
                        temp.setMarginCost(RowData[2]);
                        temp.setZone(RowData[3]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
            if (!masterList.isEmpty()) {
                dbHelper.InsertToRAMarginCost(masterList);
                add_last_update_time(status);
            }
        } catch (Exception ignored) {
        }
        DOWNLOAD_RA_detention_rate();
    }

    public void DOWNLOAD_RA_detention_rate() {
        String status = "RA_detention_rate";
        try {
            lastUpdate = dbHelper.getlastDownloadTime(status);
            if (lastUpdate.matches("")) {
                lastUpdate = "1971-01-01€10:10:10";
            }
            lastUpdate = lastUpdate.replace("€", " ");
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            jo.put("last_update_time", MCrypt.bytesToHex(mcrypt.encrypt(lastUpdate)));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RADetentionCostDownload, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RADetentionCostDetails> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RADetentionCostDetails temp = new RADetentionCostDetails();
                        temp.setbranchCode(RowData[0]);
                        temp.setprodCode(RowData[1]);
                        temp.setDetentionCost(RowData[2]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
            if (!masterList.isEmpty()) {
                dbHelper.InsertToRADetentionCost(masterList);
                add_last_update_time(status);
            }
        } catch (Exception ignored) {
        }
        DOWNLOAD_plant_product_wise_RA_rate();
    }

    public void DOWNLOAD_plant_product_wise_RA_rate_rejected() {
        String status = "bid_rate_rejected";
        try {
            lastUpdate = dbHelper.getlastDownloadTime("RA_bid_rate_details_rejected");
            if (lastUpdate.matches("")) {
                lastUpdate = "1971-01-01€10:10:10";
            }
            lastUpdate = lastUpdate.replace("€", " ");
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            jo.put("last_update_time", MCrypt.bytesToHex(mcrypt.encrypt(lastUpdate)));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RASaudaRejectedBidRateDownload, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<PlantProductWiseRARate> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        PlantProductWiseRARate temp = new PlantProductWiseRARate();
                        temp.setBidId(RowData[0]);
                        temp.setPlantName(RowData[1]);
                        temp.setProdCode(RowData[2]);
                        temp.setReleaseRate(RowData[3]);
                        temp.setBaseRate(RowData[4]);
                        temp.setIndicativeRateServer(RowData[5]);
                        temp.setIndicativeRateApp(RowData[6]);
                        temp.setCustomerCode(RowData[7]);
                        temp.setQty(RowData[8]);
                        temp.setbidPrice(RowData[9]);
                        temp.setCounterBid(RowData[10]);
                        temp.setCounterBidRate(RowData[11]);
                        temp.setCounterBidStatus(RowData[12]);
                        temp.setPrimaryFreight(RowData[13]);
                        temp.setSecondaryFreight(RowData[14]);
                        temp.setDepotCost(RowData[15]);
                        temp.setgstPercent(RowData[16]);
                        temp.setgstValue(RowData[17]);
                        temp.setbranchCode(RowData[18]);
                        temp.setuserChosenIncoterms(RowData[19]);
                        temp.setverticalOfEmployee(RowData[20]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
            if (!masterList.isEmpty()) {
                dbHelper.InsertToRABidRateDetails(masterList);
                add_last_update_time("RA_bid_rate_details_rejected");
            }
        } catch (Exception ignored) {
        }
        DOWNLOAD_plant_product_wise_RA_rate_counter();
    }

    public void DOWNLOAD_plant_product_wise_RA_rate_counter() {
        try {
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RASaudaCounterBidRateDownload, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<PlantProductWiseRARate> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        PlantProductWiseRARate temp = new PlantProductWiseRARate();
                        temp.setBidId(RowData[0]);
                        temp.setPlantName(RowData[1]);
                        temp.setProdCode(RowData[2]);
                        temp.setReleaseRate(RowData[3]);
                        temp.setBaseRate(RowData[4]);
                        temp.setIndicativeRateServer(RowData[5]);
                        temp.setIndicativeRateApp(RowData[6]);
                        temp.setCustomerCode(RowData[7]);
                        temp.setQty(RowData[8]);
                        temp.setbidPrice(RowData[9]);
                        temp.setCounterBid(RowData[10]);
                        temp.setCounterBidRate(RowData[11]);
                        temp.setCounterBidStatus(RowData[12]);
                        temp.setPrimaryFreight(RowData[13]);
                        temp.setSecondaryFreight(RowData[14]);
                        temp.setDepotCost(RowData[15]);
                        temp.setgstPercent(RowData[16]);
                        temp.setgstValue(RowData[17]);
                        temp.setbranchCode(RowData[18]);
                        temp.setuserChosenIncoterms(RowData[19]);
                        temp.setverticalOfEmployee(RowData[20]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (Exception ignored) {
        }
        dbHelper.InsertToRABidRateDetails(masterList);
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            dbHelper.getCounterBidListForRAOnToday();
            if (!CounterBidListListForRAOnToday.isEmpty()) {
                Intent intent = new Intent(mContext, ReverseAuctionActivity.class);
                mContext.startActivity(intent);
            } else {
                Utils.showToast(mContext, "No auction data found! Please Synchronize Data.");
                REVERSE_AUCTION_FLAG = 0;
                Intent intent = new Intent(mContext, ReverseAuctionActivity.class);
                mContext.startActivity(intent);
            }
        });
    }

    public void DOWNLOAD_RA_Window_Time_Download() {
        try {
            MCrypt mcrypt = new MCrypt();
            JSONObject jo = new JSONObject();
            jo.put("nickname", MCrypt.bytesToHex(mcrypt.encrypt(Constants.nickName)));
            jo.put("emp_code", MCrypt.bytesToHex(mcrypt.encrypt(Constants.employeeDetailObject.getEmpCode())));
            Utils.downloadDecryptTextFile(parentURLLaravel + AceDnsWebServiceURL.RAWindowTimeDownload, jo, Utils.getAppStoragePath(mContext) + status + ".txt");
        } catch (Exception ignored) {
        }
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RADateWiseWindowTime> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("##") > 0) {
                    String[] dataArray = line.split("##");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RADateWiseWindowTime temp = new RADateWiseWindowTime();
                        temp.setTimeFrom(RowData[0]);
                        temp.setTimeTo(RowData[1]);
                        temp.setlast_window_time(RowData[2]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (Exception ignored) {
        }
        dbHelper.InsertToRAWindowTime(masterList);
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            ArrayList<RADateWiseWindowTime> WindowOpenCloseTimingForToday = dbHelper.getWindowOpenCloseTimingForToday();
            if (!WindowOpenCloseTimingForToday.isEmpty()) {
                boolean isCurrentTimeInWindowOpenTime = false;
                for (int i = 0; i < WindowOpenCloseTimingForToday.size(); i++) {
                    isCurrentTimeInWindowOpenTime = Utils.checkCurrentTimeIsWithinGivenRangeOrNot(dateString + "-" + WindowOpenCloseTimingForToday.get(i).getTimeFrom(), dateString + "-" + WindowOpenCloseTimingForToday.get(i).getTimeTo(), "yyyyMMdd-HH:mm:ss");
                    if (isCurrentTimeInWindowOpenTime)
                        break;
                }
                if (isCurrentTimeInWindowOpenTime) {
                    REVERSE_AUCTION_FLAG = 1;
                    Utils.showProgressDialog(mContext, "Updating auction related data..");
                    new Thread() {
                        public void run() {
                            new commonAsyncTaskMaster(mContext, "ra_sauda");
                        }
                    }.start();
                } else {
                    REVERSE_AUCTION_FLAG = 2;
                    Utils.showProgressDialog(mContext, "Updating auction related data..");
                    new Thread() {
                        public void run() {
                            new commonAsyncTaskMaster(mContext, "ra_sauda_counter");
                        }
                    }.start();
                }
            } else {
                REVERSE_AUCTION_FLAG = 0;
                Utils.showToast(mContext, "No window timing information found! Please Synchronize Data.");
                Intent intent = new Intent(mContext, ReverseAuctionActivity.class);
                mContext.startActivity(intent);
            }
        });
    }

    public void stock_allocation_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.stockAllocationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<CustomerProductAllocation> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerProductAllocation temp = new CustomerProductAllocation();
                        temp.setAllocationId(RowData[0]);
                        temp.setCustomerCode(RowData[1]);
                        temp.setprodCode(RowData[2]);
                        temp.setQty(RowData[3]);
                        temp.setFromDate(RowData[4]);
                        temp.setToDate(RowData[5]);
                        temp.setAcedns(RowData[6]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        if (!masterList.isEmpty()) {
            long insertStatus = dbHelper.InsertToStockInAllocation(masterList);
            if (insertStatus == noRows && noRows > 0) {
            } else if (noRows == 0 && noColumn != 0) {
            } else {
                if (noRows != 0) {
                    Constants.isDownLoadComplete = false;
                }
            }
        }
        if (!masterApiCallingFlag) {
            Activity activity = (Activity) mContext;
            activity.runOnUiThread(() -> {
                Utils.cancelProgressDialog();
                AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(mContext);
                ArrayList<ProductMasterDetails> productMasterList = mAceDnsDatabase.getProductMasterListRetailserStockin("", Integer.parseInt(Constants.productDetailsObj.getNoFilter()));
                if (!productMasterList.isEmpty()) {
                    Intent intent = new Intent(mContext, RetailerStockInActivity.class);
                    mContext.startActivity(intent);
                } else {
                    Utils.showToast(mContext, "No stock left.");
                }
            });
        }
    }

    public void stock_balance_details_master() {
        Increment_stock_balance_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.stockBalanceURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<ProductMasterDetails> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ProductMasterDetails temp = new ProductMasterDetails();
                        temp.setslNo(RowData[0]);
                        temp.setcustomerCode(RowData[1]);
                        temp.setProdCode(RowData[2]);
                        temp.setallocation_id(RowData[3]);
                        temp.setallocationDate(RowData[4]);
                        temp.setallocatedQty(RowData[5]);
                        temp.setrequisition_id(RowData[6]);
                        temp.setRequisitionDate(RowData[7]);
                        temp.setQty(RowData[8]);
                        temp.setbilledQty(RowData[9]);
                        temp.setstockOutQty(RowData[10]);
                        temp.setactiveFlag(RowData[11]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        if (!masterList.isEmpty()) {
            dbHelper.insertToStockBalanceDetails(masterList);
            add_last_update_time(status);
        }
        if (!masterApiCallingFlag) {
            Activity activity = (Activity) mContext;
            activity.runOnUiThread(() -> {
                Utils.cancelProgressDialog();
                Intent intent = new Intent(mContext, ActivityStockBalanceReport.class);
                intent.putExtra("SELECTION", ActivityReportLanding.SELECTION);
                intent.putExtra("STARTDATE", ActivityReportLanding.mStartDate);
                intent.putExtra("ENDDATE", ActivityReportLanding.mEndDate);
                mContext.startActivity(intent);
            });
        }
    }

    public void zero_outlet_details() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.zeroOutletDetailsUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        zeroOutletMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        ProductMasterDetails temp = new ProductMasterDetails();
                        temp.setcustomerCode(RowData[0]);
                        temp.setCustomerName(RowData[1]);
                        temp.setProdCode(RowData[2]);
                        temp.setcustomerPhone(RowData[3]);
                        temp.setDesc(RowData[4]);
                        zeroOutletMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            ShoweroOutletDetailsDialog();
        });

    }
    
    @SuppressLint("SetTextI18n")
    public void ShoweroOutletDetailsDialog() {
        final Dialog mDetailsDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar_Fullscreen);
        mDetailsDialog.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        mDetailsDialog.setContentView(R.layout.dialog_stock_report_retailer_imei);
        mDetailsDialog.setCancelable(true);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.textviewTitleName);
        TextView imeiLabel =  mDetailsDialog.findViewById(R.id.imeiLabel);
        TextView imeiLabel2 =  mDetailsDialog.findViewById(R.id.imeiLabe2);
        FrameLayout timeOfAttendance = mDetailsDialog.findViewById(R.id.timeOfAttendance);
        timeOfAttendance.setVisibility(View.VISIBLE);
        textViewTitleName.setText("Customers with Zero outlet");
        imeiLabel.setText("Customer");
        imeiLabel2.setText("Phone");
        ListView dialogList =  mDetailsDialog.findViewById(R.id.listdata);
        ArrayList<ProductMasterDetails> zeroOutletMasterListlocal = new ArrayList<>();
        for (int i = 0; i < zeroOutletMasterList.size(); i++) {
            ProductMasterDetails currentItem = zeroOutletMasterList.get(i);
            boolean isItemPresent = false;
            for (int i2 = 0; i2 < zeroOutletMasterListlocal.size(); i2++) {
                if (zeroOutletMasterListlocal.get(i2).getcustomerCode().equalsIgnoreCase(currentItem.getcustomerCode())) {
                    isItemPresent = true;
                    break;
                }
            }
            if (!isItemPresent) {
                zeroOutletMasterListlocal.add(currentItem);
            }
        }
        ZeroOutletCustomerAdapter orderReportAdapterLevel2 = new ZeroOutletCustomerAdapter(mContext, R.layout.retailerapp_attendance_report_child, zeroOutletMasterListlocal, false);
        dialogList.setAdapter(orderReportAdapterLevel2);
        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            String custCodeOfClickedItem = ((TextView) arg1.findViewById(R.id.invisibleProdCode)).getText().toString();
            String custNameOfClickedItem = ((TextView) arg1.findViewById(R.id.textViewSku)).getText().toString();
            ArrayList<ProductMasterDetails> zeroOutletMasterListlocal1 = new ArrayList<>(zeroOutletMasterList);
            for (Iterator<ProductMasterDetails> i = zeroOutletMasterListlocal1.iterator(); i.hasNext(); ) {
                ProductMasterDetails x = i.next();
                if (!x.getcustomerCode().matches(custCodeOfClickedItem)) {
                    i.remove();
                }
            }
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Collections.sort(zeroOutletMasterListlocal1, Comparator.comparing(ProductMasterDetails::getDesc));
            }
            ShowEmpAttendanceDetailsDialogProduct(zeroOutletMasterListlocal1, custNameOfClickedItem);
        });
        mDetailsDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowEmpAttendanceDetailsDialogProduct(ArrayList<ProductMasterDetails> zeroOutletMasterListlocal, String custNameOfClickedItem) {
        final Dialog mDetailsDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar_Fullscreen);
        mDetailsDialog.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        mDetailsDialog.setContentView(R.layout.dialog_stock_report_retailer_imei);
        mDetailsDialog.setCancelable(true);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.textviewTitleName);
        TextView imeiLabel =  mDetailsDialog.findViewById(R.id.imeiLabel);
        textViewTitleName.setText(custNameOfClickedItem);
        imeiLabel.setText("Products");
        ListView dialogList =  mDetailsDialog.findViewById(R.id.listdata);
        ZeroOutletCustomerAdapter orderReportAdapterLevel2 = new ZeroOutletCustomerAdapter(mContext, R.layout.retailerapp_attendance_report_child, zeroOutletMasterListlocal, true);
        dialogList.setAdapter(orderReportAdapterLevel2);
        mDetailsDialog.show();
    }

    public void splashScreenDetailsApiCaling() {
        String URLSplashScrenDetails = BaseUrl.baseUrl + AceDnsWebServiceURL.splashScreenDetailsUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URLSplashScrenDetails);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        splashScreenDetailsList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SplashScreenDetails temp = new SplashScreenDetails();
                        temp.setattendanceToday(RowData[0]);
                        temp.settotalSaleMtd(RowData[1]);
                        temp.setclosingStock(RowData[2]);
                        temp.setzeroOutlet(RowData[3]);
                        temp.settotalSaleFtd(RowData[4]);
                        splashScreenDetailsList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            mContext.startActivity(new Intent(mContext, ActivityRetailerAppSplashReport.class));
        });
    }

    public void managerActivityApiCaling() {
        String URLSplashScrenDetails = BaseUrl.baseUrl + AceDnsWebServiceURL.managerActivityUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&dateval=" + Utils.getCurrentDateTimeInGivenFormat("yyyy-MM-dd") + "&timeval=" + timeVal;
        Download_txt(URLSplashScrenDetails);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        final ArrayList<EmployeeMasterDetails> empActivityList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        EmployeeMasterDetails temp = new EmployeeMasterDetails();
                        temp.setEmpCode(RowData[0]);
                        temp.setEmpName(RowData[1]);
                        temp.setattendanceTime(RowData[2]);
                        temp.settotalVisit(RowData[3]);
                        if (Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("customize")) {
                            temp.setTotal_survey(RowData[4]);
                        } else {
                            temp.setTotal_survey("");
                        }
                        empActivityList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            if (!empActivityList.isEmpty()) {
                ShowProductDetailsDialog(empActivityList);
            }
        });
    }

    public void managerActivityDetailsApiCaling() {
        String date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
        String URLSplashScrenDetails = BaseUrl.baseUrl + AceDnsWebServiceURL.managerActivityOnClickUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.cust_emp_selected + "&dateval=" + date;
        Download_txt(URLSplashScrenDetails);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        final ArrayList<EmployeeMasterDetails> empActivityList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == 4) {
                        EmployeeMasterDetails temp = new EmployeeMasterDetails();
                        temp.setEmpCode(RowData[0]);
                        temp.setEmpName(RowData[1]);
                        temp.setattendanceTime(RowData[2]);
                        temp.settotalVisit(RowData[3]);
                        if (Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("customize")) {
                            temp.setTotal_survey(RowData[4]);
                        } else {
                            temp.setTotal_survey("");
                        }
                        empActivityList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            if (!empActivityList.isEmpty()) {
                ShowProductDetailsOnclickDialog(empActivityList);
            } else {
                Utils.showToast(mContext, "No Data Found");
            }
        });
    }

    public void priceGenerationApiCaling() {
        String URLSplashScrenDetails = BaseUrl.baseUrl + AceDnsWebServiceURL.baseOilRateApi + "?nick_name=" + Constants.nickName;
        Download_txt(URLSplashScrenDetails);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        final ArrayList<commonDatabaseHelper> empActivityList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1].toLowerCase());
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        empActivityList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            if (!empActivityList.isEmpty()) {
                ShowBaseOilRatePriceGenerationDialog(empActivityList);
            }
        });
    }

    public void McxpriceGenerationApiCaling() {
        String URLSplashScrenDetails = BaseUrl.baseUrl + AceDnsWebServiceURL.mcxBaseOilRateApi + "?nick_name=" + Constants.nickName;
        Download_txt(URLSplashScrenDetails);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        final ArrayList<commonDatabaseHelper> itemList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        itemList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            if (!itemList.isEmpty()) {
                ShowMcxRatePriceGenerationDialog(itemList);
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public void ShowProductDetailsDialog(ArrayList<EmployeeMasterDetails> empActivityList) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_manager_activity);
        mDetailsDialog.setCancelable(true);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> {
            mDetailsDialog.cancel();
            if (Constants.isOrederToNewCustomer) {
                ((Activity) mContext).finish();
            }
        });
        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText("Employee Activities");
        ListView dialogList =  mDetailsDialog.findViewById(R.id.listdata);
        ManagerActivityReportAdapter ReportAdapterObject = new ManagerActivityReportAdapter(mContext, R.layout.manager_activity_report_child, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);
        FrameLayout fl_ts =  mDetailsDialog.findViewById(R.id.fl_ts);
        if (Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("customize")) {
            fl_ts.setVisibility(View.VISIBLE);
        }
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                Constants.cust_emp_selected = empActivityList.get(position).getEmpCode();
                if (Constants.nickName.equalsIgnoreCase("SHAKTI") || Constants.nickName.equalsIgnoreCase("CORAL")) {
                    if (HTTPUtils.isConnectionPossible(mContext)) {
                        Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
                        new Thread() {
                            public void run() {
                                Constants.isOrederToNewCustomer = false;
                                timeVal = Utils.getCurrentDateTimeInGivenFormat("HH:mm:ss");
                                new commonAsyncTaskMaster(mContext, "manager_activity_details");
                            }
                        }.start();
                    } else {
                        Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                    }
                }
            }
        });
        mDetailsDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowProductDetailsOnclickDialog(ArrayList<EmployeeMasterDetails> empActivityList) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_manager_activity_details);
        mDetailsDialog.setCancelable(true);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> {
            mDetailsDialog.cancel();
            if (Constants.isOrederToNewCustomer) {
                ((Activity) mContext).finish();
            }
        });
        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText("Employee Activities Details");
        ListView dialogList =  mDetailsDialog.findViewById(R.id.listdata);
        ManagerActivityReportAdapter ReportAdapterObject = new ManagerActivityReportAdapter(mContext, R.layout.manager_activity_report_child, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);
        FrameLayout fl_ts =  mDetailsDialog.findViewById(R.id.fl_ts);
        if (Constants.menuDetailsObj.getmanager_activity().equalsIgnoreCase("customize")) {
            fl_ts.setVisibility(View.GONE);
        }
        mDetailsDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowMcxRatePriceGenerationDialog(ArrayList<commonDatabaseHelper> empActivityList) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.price_generation_dialog_list_material);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->mDetailsDialog.dismiss());
        mDetailsDialog.setCancelable(true);
        TextView text1 = mDetailsDialog.findViewById(R.id.text1);
        text1.setText("Type");
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setText("Generate MCX Rate");
        btn_generate.setOnClickListener(view -> {
            priceListOilGrpFinalList = new ArrayList<>();
            for (int i = 0; i < McxRatePriceGenerationInputAdapter.priceListOilGrp.size(); i++) {
                commonDatabaseHelper productMasterDetails = McxRatePriceGenerationInputAdapter.priceListOilGrp.get(i);
                String input = productMasterDetails.getItem1();
                if (Utils.isNumeric(input) && Double.parseDouble(input) > 0) {
                    priceListOilGrpFinalList.add(productMasterDetails);
                }
            }
            if (!priceListOilGrpFinalList.isEmpty()) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
                    new Thread() {
                        public void run() {
                            mDetailsDialog.dismiss();
                            McxPriceGenerationFinalPArtApiCalling();
                        }
                    }.start();
                } else {
                    Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                }
            }
        });
        textViewTitleName.setText("Mcx Price Generation");
        ListView dialogList = mDetailsDialog.findViewById(R.id.list);
        McxRatePriceGenerationInputAdapter ReportAdapterObject = new McxRatePriceGenerationInputAdapter(mContext, R.layout.list_item__base_oil_price_input_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);
        mDetailsDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowBaseOilRatePriceGenerationDialog(ArrayList<commonDatabaseHelper> empActivityList) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.price_generation_dialog_list_material);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->mDetailsDialog.dismiss());
        mDetailsDialog.setCancelable(true);
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setOnClickListener(view -> {
            priceListOilGrpFinalList = new ArrayList<>();
            for (int i = 0; i < BaseOilRatePriceGenerationInputAdapter.priceListOilGrp.size(); i++) {
                commonDatabaseHelper productMasterDetails = BaseOilRatePriceGenerationInputAdapter.priceListOilGrp.get(i);
                String prod = productMasterDetails.getItem0();
                String mandatory = productMasterDetails.getItem1();
                String lower = productMasterDetails.getItem2();
                String upper = productMasterDetails.getItem3();
                String input = productMasterDetails.getItem4();
                if (Utils.isNumeric(input) && Double.parseDouble(input) > 0) {
                    if (Double.parseDouble(input) >= Double.parseDouble(lower) && Double.parseDouble(input) <= Double.parseDouble(upper)) {
                        priceListOilGrpFinalList.add(productMasterDetails);
                    } else {
                        if (Double.parseDouble(input) < Double.parseDouble(lower)) {
                            Utils.showToast(mContext, "Input for " + prod + " must be grater than or equal to " + lower);
                        } else {
                            Utils.showToast(mContext, "Input for " + prod + " must be lesser than or equal to " + upper);
                        }
                        return;
                    }
                } else {
                    if (mandatory.equalsIgnoreCase("y")) {
                        Utils.showToast(mContext, "Input is mandatory for " + prod);
                        return;
                    }
                }
            }
            if (!priceListOilGrpFinalList.isEmpty()) {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
                    new Thread() {
                        public void run() {
                            mDetailsDialog.dismiss();
                            priceGenerationFinalPArtApiCalling();
                        }
                    }.start();
                } else {
                    Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                }
            }
        });
        textViewTitleName.setText("Price Generation");
        ListView dialogList =  mDetailsDialog.findViewById(R.id.list);
        BaseOilRatePriceGenerationInputAdapter ReportAdapterObject = new BaseOilRatePriceGenerationInputAdapter(mContext, R.layout.list_item__base_oil_price_input_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);
        mDetailsDialog.show();
    }

    public void priceGenerationFinalPArtApiCalling() {
        StringBuilder loose_rate = new StringBuilder();
        StringBuilder oilsVal = new StringBuilder();
        for (int i = 0; i < priceListOilGrpFinalList.size(); i++) {
            commonDatabaseHelper productMasterDetails = priceListOilGrpFinalList.get(i);
            String prod = productMasterDetails.getItem0();
            String input = productMasterDetails.getItem4();
            if (loose_rate.toString().matches("")) {
                loose_rate = new StringBuilder(prod);
            } else {
                loose_rate.append(",").append(prod);
            }
            if (oilsVal.toString().matches("")) {
                oilsVal = new StringBuilder(input);
            } else {
                oilsVal.append(",").append(input);
            }
        }
        ContentValues values = new ContentValues();
        values.put("loose_rate", oilsVal.toString());
        values.put("oils_val", loose_rate.toString());
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.RateGenerationApi + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        String response = HttpCalling.httpPostCallWithXmlResponse(url, values);
        ArrayList<commonDatabaseHelper> generatedPriceList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(new StringReader(response));
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        generatedPriceList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            if (!generatedPriceList.isEmpty()) {
                RateGenerationDownloadDialog(generatedPriceList);
            }
        });
    }

    public void McxPriceGenerationFinalPArtApiCalling() {
        StringBuilder loose_rate = new StringBuilder();
        StringBuilder oilsVal = new StringBuilder();
        for (int i = 0; i < priceListOilGrpFinalList.size(); i++) {
            commonDatabaseHelper productMasterDetails = priceListOilGrpFinalList.get(i);
            String prod = productMasterDetails.getItem0();
            String input = productMasterDetails.getItem1();
            if (loose_rate.toString().matches("")) {
                loose_rate = new StringBuilder(prod);
            } else {
                loose_rate.append(",").append(prod);
            }
            if (oilsVal.toString().matches("")) {
                oilsVal = new StringBuilder(input);
            } else {
                oilsVal.append(",").append(input);
            }
        }
        ContentValues values = new ContentValues();
        values.put("loose_rate", oilsVal.toString());
        values.put("oils_val", loose_rate.toString());
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.McxRateGenerationApi + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        String response = HttpCalling.httpPostCallWithXmlResponse(url, values);
        ArrayList<commonDatabaseHelper> generatedMcxPriceList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(new StringReader(response));
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        temp.setItem7(RowData[7]);
                        temp.setItem8(RowData[8]);
                        generatedMcxPriceList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            if (!generatedMcxPriceList.isEmpty()) {
                McxRateGenerationDownloadDialog(generatedMcxPriceList);
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public void RateGenerationDownloadDialog(ArrayList<commonDatabaseHelper> generatedPriceList) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.price_generated_dialog_list_material);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->mDetailsDialog.dismiss());
        mDetailsDialog.setCancelable(true);
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setOnClickListener(view -> {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
                new Thread() {
                    public void run() {
                        mDetailsDialog.dismiss();
                        priceReleaseApiCalling();
                    }
                }.start();
            } else {
                Utils.showToast(mContext, "You need an active internet connection to use this feature.");
            }
        });
        textViewTitleName.setText("Base Product Sale Rate");
        ListView dialogList = mDetailsDialog.findViewById(R.id.list);
        BaseOilRateGeneratedPriceInputAdapter ReportAdapterObject = new BaseOilRateGeneratedPriceInputAdapter(mContext, R.layout.list_item_price_generated_material, generatedPriceList);
        dialogList.setAdapter(ReportAdapterObject);
        mDetailsDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void McxRateGenerationDownloadDialog(ArrayList<commonDatabaseHelper> generatedPriceList) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.price_generated_dialog_list_material);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->  mDetailsDialog.dismiss());
        mDetailsDialog.setCancelable(true);
        TextView headerTV1 = mDetailsDialog.findViewById(R.id.headerTV1);
        TextView headerTV2 = mDetailsDialog.findViewById(R.id.headerTV2);
        TextView headerTV3 = mDetailsDialog.findViewById(R.id.headerTV3);
        TextView headerTV4 = mDetailsDialog.findViewById(R.id.headerTV4);
        TextView headerTV5 = mDetailsDialog.findViewById(R.id.headerTV5);

        headerTV1.setText("Loose Rate\nOpen");
        headerTV2.setText("Loose Rate\nClose");
        headerTV3.setText("Process\nCost");
        headerTV4.setText("Packing\nRealization");
        headerTV5.setText("Margin\nCost");

        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setText("OK");
        btn_generate.setOnClickListener(view -> {
            mDetailsDialog.dismiss();
            Utils.showToast(mContext, "MCX rate generated successfully...");
        });
        textViewTitleName.setText("Base Product Mcx Rate");

        ListView dialogList = mDetailsDialog.findViewById(R.id.list);
        McxBaseOilRateGeneratedPriceInputAdapter ReportAdapterObject = new McxBaseOilRateGeneratedPriceInputAdapter(mContext, R.layout.list_item_price_generated_material, generatedPriceList);
        dialogList.setAdapter(ReportAdapterObject);
        mDetailsDialog.show();
    }

    public void priceReleaseApiCalling() {
        String releasedRateDownload = BaseUrl.baseUrl + AceDnsWebServiceURL.ReleasedRateDownoadApi;
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
        String response = HttpCalling.httpGetCallWithXmlResponse(releasedRateDownload, values);
        ArrayList<commonDatabaseHelper> generatedPriceListDownloaded = new ArrayList<>();
        BufferedReader buffer2 = new BufferedReader(new StringReader(response));
        try {
            String line;
            while ((line = buffer2.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        generatedPriceListDownloaded.add(temp);
                    }
                }
            }
            buffer2.close();
            if (!generatedPriceListDownloaded.isEmpty()) {
                dbHelper.insertToReleaseRateDetails(generatedPriceListDownloaded);
            }
        } catch (IOException ignored) {
        }
        String URLSplashScrenDetails = BaseUrl.baseUrl + AceDnsWebServiceURL.RatePublishApi + "?nick_name=" + Constants.nickName;
        Download_txt(URLSplashScrenDetails);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        publishRateList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6("");
                        temp.setItem7("");
                        publishRateList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            if (!publishRateList.isEmpty()) {
                ShowPricePublishDialog();
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public void ShowPricePublishDialog() {
        submitEnabledFlag = false;
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.price_publish_dialog_list_material);
        mDetailsDialog.setCancelable(true);
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.title);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        Button btn_generate = mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setOnClickListener(view -> {
            if (submitEnabledFlag) {
                if (!publishRateList.isEmpty()) {
                    if (HTTPUtils.isConnectionPossible(mContext)) {
                        Utils.showProgressDialog(mContext, "Uploading Data, Please Wait..");
                        new Thread() {
                            public void run() {
                                StringBuilder xmlData = new StringBuilder("<?xml version='1.0' encoding='UTF-8'?><root>");
                                for (int x = 0; x < publishRateList.size(); x++) {
                                    commonDatabaseHelper currentItem = publishRateList.get(x);
                                    String date = currentItem.getItem7();
                                    String prevOrCurrent = currentItem.getItem6();
                                    if (!prevOrCurrent.equalsIgnoreCase("previous")) {
                                        date = "";
                                    }
                                    xmlData.append("<publish_rate_info>" + "<product_sub_group_code><![CDATA[]]></product_sub_group_code>" + "<product_sub_group_name><![CDATA[").append(currentItem.getItem2()).append("]]></product_sub_group_name>").append("<prod_code><![CDATA[").append(currentItem.getItem0()).append("]]></prod_code>").append("<publish_rate_type><![CDATA[").append(prevOrCurrent).append("]]></publish_rate_type>").append("<publish_rate_date><![CDATA[").append(date).append("]]></publish_rate_date>").append("</publish_rate_info>");
                                }
                                xmlData.append("</root>");
                                String POST_result;
                                try {
                                    String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.RatePublishTransApi + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                                    POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData.toString());
                                } catch (Exception e) {
                                    POST_result = "Network Failure";
                                }
                                Activity activity = (Activity) mContext;
                                String finalPOST_result = POST_result;
                                activity.runOnUiThread(() -> {
                                    Utils.cancelProgressDialog();
                                    if (finalPOST_result.equalsIgnoreCase("1")) {
                                        Toast.makeText(mContext, Constants.deleverySuccessMsg, Toast.LENGTH_SHORT).show();
                                        mDetailsDialog.dismiss();
                                    } else {
                                        Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                                    }
                                });
                            }
                        }.start();
                    } else {
                        Utils.showToast(mContext, "You need an active internet connection to use this feature.");
                    }
                }
            }
        });
        textViewTitleName.setText("Release Rate");
        List<String> listDataHeader = new ArrayList<>();
        HashMap<String, List<commonDatabaseHelper>> listChildData = new HashMap<>();
        for (int x = 0; x < publishRateList.size(); x++) {
            commonDatabaseHelper item = publishRateList.get(x);
            String prodGrp = item.getItem2();
            if (!listDataHeader.contains(prodGrp)) {
                listDataHeader.add(prodGrp);
                List<commonDatabaseHelper> prodWithCurrentProdGrp = new ArrayList<>();
                for (int i = 0; i < publishRateList.size(); i++) {
                    if (prodGrp.equalsIgnoreCase(publishRateList.get(i).getItem2())) {
                        prodWithCurrentProdGrp.add(publishRateList.get(i));
                    }
                }
                listChildData.put(prodGrp, prodWithCurrentProdGrp);
            }
        }
        expListView = mDetailsDialog.findViewById(R.id.list);
        expListView.setOnGroupClickListener((parent, v, groupPosition, id) -> parent.isGroupExpanded(groupPosition));
        try {
            ExpandableListAdapterPricePublish ReportAdapterObject = new ExpandableListAdapterPricePublish(mContext, listDataHeader, listChildData);
            expListView.setAdapter(ReportAdapterObject);
            for (int i = 0; i < ReportAdapterObject.getGroupCount(); i++)
                expListView.expandGroup(i);
        } catch (Exception ignored) {
        }
        mDetailsDialog.show();
    }

    public void DOWNLOAD_billing_info() {
        Increment_customer_product_billing();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.billingInformationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<BillingInformationStockSummaryData> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BillingInformationStockSummaryData temp = new BillingInformationStockSummaryData();
                        temp.setcustomerCode(RowData[0]);
                        temp.setProductCode(RowData[1]);
                        temp.setimei(RowData[2]);
                        temp.setsold_out_date(RowData[3]);
                        temp.setinvoiceDate(RowData[4]);
                        temp.setstkOutCustomerCode(RowData[5]);
                        temp.setactivationDate(RowData[6]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToCustomerProductBilling(masterList);
        if (!masterList.isEmpty()) {
            add_last_update_time(status);
        }
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustomerProductBillingUpdated = false;
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustomerProductBillingUpdated = false;
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
        if (!masterApiCallingFlag) {
            Activity activity = (Activity) mContext;
            activity.runOnUiThread(() -> {
                Utils.cancelProgressDialog();
                if (masterApiCallingFlagFromReport) {
                    Intent intent = new Intent(mContext, ActivityStockReport.class);
                    if (Constants.retailerappReportType.equalsIgnoreCase("activationreport")) {
                        intent = new Intent(mContext, ActivityActivationReport.class);
                    }
                    intent.putExtra("SELECTION", ActivityReportLanding.SELECTION);
                    intent.putExtra("STARTDATE", ActivityReportLanding.mStartDate);
                    intent.putExtra("ENDDATE", ActivityReportLanding.mEndDate);
                    mContext.startActivity(intent);
                } else {
                    if (Constants.stockOutTypeRetailerApp.equalsIgnoreCase("special")) {
                        AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(mContext);
                        mAceDnsDatabase.getRetailerStockOutProductList();
                        if (!Constants.retailerStockOutProductList.isEmpty()) {
                            Intent intent = new Intent(mContext, RetailerStockOutActivitySpecial.class);
                            mContext.startActivity(intent);
                        } else {
                            Utils.showToast(mContext, "No product in stock.");
                        }
                    } else {
                        Intent intent = new Intent(mContext, RetailerStockOutActivity.class);
                        mContext.startActivity(intent);
                    }
                }
            });
        }
    }

    public void DOWNLOAD_dealer_transaction() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.dealerTransactionURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<DealerTransaction> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        DealerTransaction temp = new DealerTransaction();
                        temp.setCustomerCode(RowData[0]);
                        temp.setCustomerName(RowData[1]);
                        temp.setEmpCode(RowData[2]);
                        temp.setcontact_person_name(RowData[3]);
                        temp.setcontact_person_phone(RowData[4]);
                        temp.setYTD_sales(RowData[5]);
                        temp.setdues(RowData[6]);
                        temp.setlegends_earned_points(RowData[7]);
                        temp.setlegends_tier(RowData[8]);
                        temp.setlegends_total_points(RowData[9]);
                        temp.setcheck_in_date(RowData[10]);
                        temp.setcomments(RowData[11]);
                        temp.setactivity(RowData[12]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
    }

    public void DOWNLOAD_order_summary() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.orderSummaryURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<OrderDetails> masterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        OrderDetails temp = new OrderDetails();
                        temp.setOrderNo(RowData[0]);
                        temp.setCustomerCode(RowData[1]);
                        temp.setSkuCode(RowData[2]);
                        temp.setVisitQty(RowData[3]);
                        temp.setVisitDate(RowData[4]);
                        temp.setSaleRate(RowData[5]);
                        temp.setAmount(RowData[6]);
                        masterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToOrderSummary(masterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_state_district_town() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.stateDistrictTownTableUrl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<StateDistrictTown> ListOfItems = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        StateDistrictTown temp = new StateDistrictTown();
                        temp.setstate(RowData[0]);
                        temp.setdistrict(RowData[1]);
                        temp.settown(RowData[2]);
                        ListOfItems.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToStateDistrictTown(ListOfItems);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_self_appraisal_branch_wise() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.branchWiseTargetAchievement + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SelfAppraisalDetailsBranchWise> mSelfAppraisalBranchWiseList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SelfAppraisalDetailsBranchWise temp = new SelfAppraisalDetailsBranchWise();
                        temp.setbranchCode(RowData[0]);
                        temp.setbranchName(RowData[1]);
                        temp.setempCode(RowData[2]);
                        temp.setmonth(RowData[3]);
                        temp.settarget(RowData[4]);
                        temp.setachievement(RowData[5]);
                        mSelfAppraisalBranchWiseList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBranchWiseTargetAchievement(mSelfAppraisalBranchWiseList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "self_appraisal_branch_wise");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_beat_wise_TA_DA() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.beatWiseTADADownload + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<commonDatabaseHelper> mSelfAppraisalCustomerWiseList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        temp.setItem7(RowData[7]);
                        temp.setItem8(RowData[8]);
                        temp.setItem9(RowData[9]);
                        temp.setItem10(RowData[10]);
                        mSelfAppraisalCustomerWiseList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBeatWiseTADA(mSelfAppraisalCustomerWiseList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_tar_ach_retailer_wise() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.retailerWiseTargetAchievement + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<commonDatabaseHelper> mSelfAppraisalCustomerWiseList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        commonDatabaseHelper temp = new commonDatabaseHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);
                        temp.setItem2(RowData[2]);
                        temp.setItem3(RowData[3]);
                        temp.setItem4(RowData[4]);
                        temp.setItem5(RowData[5]);
                        temp.setItem6(RowData[6]);
                        temp.setItem7(RowData[7]);
                        temp.setItem8(RowData[8]);
                        temp.setItem9(RowData[9]);
                        temp.setItem10(RowData[10]);
                        temp.setItem11(RowData[11]);
                        temp.setItem12(RowData[12]);
                        temp.setItem13(RowData[13]);
                        temp.setItem14(RowData[14]);
                        mSelfAppraisalCustomerWiseList.add(temp);

                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToRetailerWiseTargetAchievement(mSelfAppraisalCustomerWiseList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "self_appraisal_customer_wise");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_target_achievement() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.empTargetAcheivementURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<EmployeeTargetAcheivement> mEmployeeTargetAcheivementList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        EmployeeTargetAcheivement temp = new EmployeeTargetAcheivement();
                        temp.setEmpCode(RowData[0]);
                        temp.setEmpName(RowData[1]);
                        temp.setMonth(RowData[2]);
                        temp.setDistrict(RowData[3]);
                        temp.setCustomerCode(RowData[4]);
                        temp.setCustomerName(RowData[5]);
                        temp.setVolumeTarget(RowData[6]);
                        temp.setVolumeAcheivement(RowData[7]);
                        temp.setCollectionTarget(RowData[8]);
                        temp.setCollectionAcheivement(RowData[9]);
                        temp.setcust_type(RowData[10]);
                        mEmployeeTargetAcheivementList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToEmployeeTargetAcheivement(mEmployeeTargetAcheivementList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "target_achievement");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_emp_date_wise_route_allocation() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.empDateWiseRouteAllocationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<EmpDateWiseRouteAlloc> mEmployeeTargetAcheivementList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        EmpDateWiseRouteAlloc temp = new EmpDateWiseRouteAlloc();
                        temp.setEmpCode(RowData[0]);
                        temp.setRouteCode(RowData[1]);
                        temp.setDate(RowData[2]);
                        mEmployeeTargetAcheivementList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        dbHelper.InsertToEmpDateWiseRouteAlocation(mEmployeeTargetAcheivementList);
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            dbHelper.getRouteListForEmployeeTodayCRM();
            if (!allocatedRouteCodeTodayCrm.isEmpty()) {
                Intent intent = new Intent(mContext, CrmActivity.class);
                mContext.startActivity(intent);
            } else {
                Utils.showToast(mContext, "No route allocated to you today! Please Synchronize Data.");
            }
        });
    }

    public void _DOWNLOAD_fs_survey_publish() {
        Increment_fs_survey_publish();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.fssurveyPublishURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<FsSurveyPublish> fsSurveyPublishList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        FsSurveyPublish temp = new FsSurveyPublish();
                        temp.setFsSurveyId(RowData[0]);
                        temp.setMallId(RowData[1]);
                        temp.setMallName(RowData[2]);
                        temp.setPincode(RowData[3]);
                        temp.setBusinessName(RowData[4]);
                        temp.setType(RowData[5]);
                        temp.setDceStatus(RowData[6]);
                        fsSurveyPublishList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToFsSurveyPublish(fsSurveyPublishList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isFsSurveyPublishTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "fs_survey_publish");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isFsSurveyPublishTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_survey_table_view() {
        Increment_survey_table_view();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.surveyTableViewURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SurveyTableView> mSurveyTableViewList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SurveyTableView temp = new SurveyTableView();
                        temp.setRowId(RowData[0]);
                        temp.setType(RowData[1]);
                        temp.setValue(RowData[2]);
                        temp.setDependentOn(RowData[3]);
                        temp.setDependentValue(RowData[4]);
                        temp.setAction(RowData[5]);
                        mSurveyTableViewList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToTableViewMaster(mSurveyTableViewList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isTableView = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "survey_table_view");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isTableView = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_route_customer_plan() {
        Increment_route_customer_plan();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.routeCustomerPlanDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RoutePlanCustomer> routePlanCustomerList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RoutePlanCustomer temp = new RoutePlanCustomer();
                        temp.setTranSactionId(RowData[0]);
                        temp.setRouteCode(RowData[1]);
                        temp.setVisitDate(RowData[2]);
                        temp.setCustomerCode(RowData[3]);
                        temp.setStatus(RowData[4]);
                        routePlanCustomerList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToRoutePlanCustomerMaster(routePlanCustomerList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isRouteCustomer = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "route_customer_plan");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isRouteCustomer = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_competitor_group_master() {
        String branch;
        try {
            branch = dbHelper.getCustAllBranch();
        } catch (Exception e) {
            branch = "";
        }
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.competitorGroupURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&branch=" + branch;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<CompetitorGroupMaster> mCompetitorGroupMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CompetitorGroupMaster temp = new CompetitorGroupMaster();
                        temp.setGroupName(RowData[0]);
                        temp.setCompetitorName(RowData[1]);
                        temp.setUom(RowData[2]);
                        temp.setDisplay_name(RowData[3]);
                        temp.setBranch_code(RowData[4]);
                        temp.setProduct_type(RowData[5]);
                        mCompetitorGroupMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToCompetitorGroupMaster(mCompetitorGroupMasterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "competitor_group_master");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_branchwise_geofencing_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.branchWiseGeoFencingURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<BranchMasterDetails> mCompetitorGroupMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BranchMasterDetails temp = new BranchMasterDetails();
                        temp.setBranchCode(RowData[0]);
                        temp.setgeo_fencing(RowData[1]);
                        mCompetitorGroupMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBranchWiseGeoFencing(mCompetitorGroupMasterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "competitor_group_master");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_sauda_allocation() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.saudaURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaAllocationDetails> saudaList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    if (noRows != 0 && noColumn != 0) {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            SaudaAllocationDetails temp = new SaudaAllocationDetails();
                            temp.setEmpCode(RowData[0]);
                            temp.setProdFilterCode(RowData[1]);
                            temp.setQty(RowData[2]);
                            temp.setBal(RowData[2]);
                            temp.setAllotedQty(RowData[3]);
                            saudaList.add(temp);
                        }
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = 0;
        if (noRows != 0 && noColumn != 0) {
            insertStatus = dbHelper.insertToSaudaAllocation(saudaList);
        }
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "sauda_allocation");
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else if (noRows == 0) {
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_TD_allocation() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.TDAllocationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SaudaAllocationDetails> saudaList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    if (noRows != 0 && noColumn != 0) {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            SaudaAllocationDetails temp = new SaudaAllocationDetails();
                            temp.setEmpCode(RowData[0]);
                            temp.setProdFilterCode(RowData[1]);
                            temp.setAllotedTD(RowData[2]);
                            saudaList.add(temp);
                        }
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = 0;
        if (noRows != 0 && noColumn != 0) {
            insertStatus = dbHelper.insertToTDAllocation(saudaList);
        }
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, status);
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else if (noRows == 0) {
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_customer_master() {
        Increment_customer_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CustomerDetails> customerList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerDetails temp = new CustomerDetails();
                        temp.setCustomerCode(RowData[0]);
                        temp.setCustomerName(RowData[1]);
                        temp.setRouteCode(RowData[2]);
                        temp.setEmpCode(RowData[3]);
                        temp.setCurrentBalance(RowData[4]);
                        temp.setCreditLimit(RowData[5]);
                        temp.setIsACEDNS(RowData[6]);
                        temp.setIsBlackList(RowData[7]);
                        temp.setTradeDiscount(RowData[8]);
                        temp.setCustomerType(RowData[9]);
                        temp.setRdsTag(RowData[10]);
                        temp.setSaudaValidityPeriod(RowData[11]);
                        temp.setAddress(RowData[12]);
                        temp.setPin(RowData[13]);
                        temp.setNumber(RowData[14]);
                        temp.setDnsCustCode(RowData[15]);
                        temp.setLandlineNo(RowData[16]);
                        temp.setOwnerName(RowData[17]);
                        temp.setOwnerPhone(RowData[18]);
                        temp.setCustClass(RowData[19]);
                        temp.setWeeklyClosingDay(RowData[20]);
                        temp.setCoverageType(RowData[21]);
                        temp.setTIN(RowData[22]);
                        temp.setPAN(RowData[23]);
                        temp.setMinimumStock(RowData[24]);
                        temp.setBranchCode(RowData[25]);
                        temp.setVisitDay(RowData[26]);
                        temp.setEmail(RowData[27]);
                        temp.setSaudaLimit(RowData[28]);
                        temp.setPendingQty(RowData[29]);
                        temp.setIncoTerms(RowData[30]);
                        temp.setLoadabilityTon(RowData[31]);
                        temp.setTransportMode(RowData[32]);
                        temp.setstate(RowData[33]);
                        temp.setSaudaType(RowData[34]);
                        temp.setzone(RowData[35]);
                        temp.setVisitSequence(RowData[36]);
                        temp.setactivated(RowData[37]);
                        temp.setactivated_customer_code(RowData[38]);
                        temp.setretailer_app(RowData[39]);
                        temp.setbase_latt(RowData[40]);
                        temp.setbase_longi(RowData[41]);
                        temp.setneed_location_update(RowData[42]);
                        temp.setciLogic(RowData[44]);
                        temp.setcategoryOfStore(RowData[45]);
                        temp.setinStoreActivityPossible(RowData[46]);
                        temp.setIsNewCustomer(RowData[47]);
                        temp.setownerImage(RowData[48]);
                        temp.setfirmName(RowData[49]);
                        temp.setoutletImage(RowData[50]);
                        temp.setgstImage(RowData[51]);
                        temp.setadharNo(RowData[52]);
                        temp.setadharImage(RowData[53]);
                        temp.setWhatsappNumber(RowData[54]);
                        temp.setDateOfBirth(RowData[55]);
                        temp.setDateOfAnniversary(RowData[56]);
                        temp.setSpouseDateOfBirth(RowData[57]);
                        temp.setFlag("1");
                        customerList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        dbHelper.getMenuDetailsObj();
        long insertStatus = dbHelper.insertToCustomerMaster(customerList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustomerTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "customer_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustomerTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_customer_master_crm() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerAlternateUrlForCrm + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CustomerDetails> customerList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerDetails temp = new CustomerDetails();
                        temp.setCustomerCode(RowData[0]);
                        temp.setCustomerName(RowData[1]);
                        temp.setRouteCode(RowData[2]);
                        temp.setEmpCode(RowData[3]);
                        temp.setCurrentBalance(RowData[4]);
                        temp.setCreditLimit(RowData[5]);
                        temp.setIsACEDNS(RowData[6]);
                        temp.setIsBlackList(RowData[7]);
                        temp.setTradeDiscount(RowData[8]);
                        temp.setCustomerType(RowData[9]);
                        temp.setRdsTag(RowData[10]);
                        temp.setSaudaValidityPeriod(RowData[11]);
                        temp.setAddress(RowData[12]);
                        temp.setPin(RowData[13]);
                        temp.setNumber(RowData[14]);
                        temp.setDnsCustCode(RowData[15]);
                        temp.setLandlineNo(RowData[16]);
                        temp.setOwnerName(RowData[17]);
                        temp.setOwnerPhone(RowData[18]);
                        temp.setCustClass(RowData[19]);
                        temp.setWeeklyClosingDay(RowData[20]);
                        temp.setCoverageType(RowData[21]);
                        temp.setTIN(RowData[22]);
                        temp.setPAN(RowData[23]);
                        temp.setMinimumStock(RowData[24]);
                        temp.setBranchCode(RowData[25]);
                        temp.setVisitDay(RowData[26]);
                        temp.setEmail(RowData[27]);
                        temp.setFlag("1");
                        customerList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToCustomerMasterForAllocation(customerList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustomerTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "customer_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustomerTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_customer_product_wise_msl() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerProductMsl + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CustomerProductWiseMsl> customerList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerProductWiseMsl temp = new CustomerProductWiseMsl();
                        temp.setcustomerCode(RowData[0]);
                        temp.setprodCode(RowData[1]);
                        temp.setmsl(RowData[2]);
                        temp.setacedns(RowData[3]);
                        customerList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToCustomerProductMsl(customerList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustomerTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "customer_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustomerTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_destination_master() {
        Increment_destination_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.destinationMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<DestinationMaster> destinationList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        DestinationMaster temp = new DestinationMaster();
                        temp.setDestinationCode(RowData[0]);
                        temp.setDestinationName(RowData[1]);
                        destinationList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToDestinationMaster(destinationList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isDestinationUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "destination_master");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isDestinationUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
        if (!masterApiCallingFlag) {
            Utils.makePdfViewingProcess(mContext);
        }
    }

    public void DOWNLOAD_scheme_pdf_master() {
        Increment_scheme_pdf_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.branchWiseSchemePdfDownloadMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<BranchWisePdfMaster> destinationList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BranchWisePdfMaster temp = new BranchWisePdfMaster();
                        temp.setbranch_code(RowData[0]);
                        String currentPdfFileName = RowData[1];
                        temp.setPDF_file_name(currentPdfFileName);
                        temp.setAcedns(RowData[2]);
                        temp.setStartDate(RowData[3]);
                        temp.setEndDate(RowData[4]);
                        destinationList.add(temp);
                        String urlstr = BaseUrl.baseUrl + "schemes/" + currentPdfFileName;
                        InputStream inputStream = new HttpCalling().httpGetCallWithInputStreamResponse(urlstr);
                        Download_txt_laravel(inputStream, currentPdfFileName, mContext);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBranchWiseSchemePdfMaster(destinationList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSchemePdfMasterUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSchemePdfMasterUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
        if (!masterApiCallingFlag) {
            Utils.makePdfViewingProcess(mContext);
        }
    }

    public void DOWNLOAD_scheme_pdf_master_without_branch() {
        Increment_scheme_pdf_master_without_branch();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.SchemePdfDownloadMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<BranchWisePdfMaster> destinationList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BranchWisePdfMaster temp = new BranchWisePdfMaster();
                        String currentPdfFileName = RowData[0];
                        temp.setPDF_file_name(currentPdfFileName);
                        temp.setAcedns(RowData[1]);
                        temp.setStartDate(RowData[2]);
                        temp.setEndDate(RowData[3]);
                        temp.setSchemeName(RowData[4]);
                        destinationList.add(temp);
                        String urlstr = BaseUrl.baseUrl + "schemes/" + currentPdfFileName;
                        InputStream inputStream = new HttpCalling().httpGetCallWithInputStreamResponse(urlstr);
                        Download_txt_laravel(inputStream, currentPdfFileName, mContext);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBranchWiseSchemePdfMasterWithoutBranch(destinationList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSchemePdfMasterWithoutBranchUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSchemePdfMasterWithoutBranchUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
        if (!masterApiCallingFlag) {
            Utils.makePdfViewingProcessWithoutBranch(mContext);
        }
    }

    public void DOWNLOAD_branchwise_golden_rule() {
        Increment_branch_wise_golden_rule();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.branchWiseGoldenRuleURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<BranchWisePdfMaster> destinationList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BranchWisePdfMaster temp = new BranchWisePdfMaster();
                        temp.setbranch_code(RowData[0]);
                        temp.setgr_file_name(RowData[1]);
                        temp.setAcedns(RowData[2]);
                        temp.setStartDate(RowData[3]);
                        temp.setEndDate(RowData[4]);
                        destinationList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToBranchWiseGoldenRules(destinationList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isBranchWiseGoldenRuleMasterUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isBranchWiseGoldenRuleMasterUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
        if (!masterApiCallingFlag) {
            Utils.makePdfViewingProcessGoldenRule(mContext);
        }
    }

    public void _DOWNLOAD_git_master() {
        SystemClock.sleep(7000);
        Increment_git_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.gitURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<GITDetails> gitList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        GITDetails temp = new GITDetails();
                        temp.setGrnNo(RowData[0]);
                        temp.setDespatcherCode(RowData[1]);
                        temp.setProdCode(RowData[2]);
                        temp.setDespatchQty(RowData[3]);
                        temp.setBalancedRecceivedQty(RowData[4]);
                        temp.setStatus(RowData[5]);
                        temp.setOrderNumber(RowData[6]);
                        temp.setTransType(RowData[7]);
                        temp.setSaleRate(RowData[8]);
                        gitList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToGITMaster(gitList);
        if (insertStatus == noRows || noRows == 0) {
            Constants.isGITTableUpdated = false;
            if (isIndependantDownload) {
                Utils.showToast(mContext, "Data downloaded successfully");
                dbHelper.insertToLogTable(timeStamp, "git_master");
            } else {
                decideNavigation();
            }
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "git_master");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void DOWNLOAD_cash_transfer_master() {
        Increment_cash_transfer_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.cashTransferMasterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<CashTransferReceive> dataList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CashTransferReceive temp = new CashTransferReceive();
                        temp.setcash_trans_rcv_trans_id(RowData[0]);
                        temp.setdespatcher_code(RowData[1]);
                        temp.setreceiver_code(RowData[2]);
                        temp.setdespatch_value(RowData[3]);
                        temp.setrec_value(RowData[4]);
                        temp.setstatus(RowData[5]);
                        temp.settransaction_type(RowData[6]);
                        temp.setcash_transfer_id(RowData[7]);
                        dataList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToCashTransferMaster(dataList);
        if (insertStatus == noRows && noRows > 0) {
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
        Activity activity = (Activity) mContext;
        activity.runOnUiThread(() -> {
            Utils.cancelProgressDialog();
            dbHelper.getUVerifiedCashReceive();
            if (!UnVerifiedCashReceiveList.isEmpty()) {
                Intent intent = new Intent(mContext, CashTransferOrReceiveActivity.class);
                mContext.startActivity(intent);
            } else {
                Utils.showToast(mContext, "No pending data found!");
            }
        });
    }

    public void DOWNLOAD_customer_orientation_data() {
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("deviceId", Constants.deviceId);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
        HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.updateCheckURL, values);
    }

    public void _DOWNLOAD_sauda_transaction_log() {
        Increment_sauda_transaction_log();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.saudaTransactionLogURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate + "&incremental_download=" + mIsInCremental + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<SauadaTransactionLog> mSauadaTransactionLogList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SauadaTransactionLog obj = new SauadaTransactionLog();
                        obj.setBranchCode(RowData[0]);
                        obj.setBrokerId(RowData[1]);
                        obj.setEmployeeCode(RowData[2]);
                        obj.setSaudaDate(RowData[3]);
                        obj.setSaudaNo(RowData[4]);
                        obj.setCustomerCode(RowData[5]);
                        obj.setProductCode(RowData[6]);
                        obj.setQuantity(RowData[7]);
                        obj.setConvertQtyOne(RowData[8]);
                        obj.setConvertQtyTwo(RowData[9]);
                        obj.setSaleRate(RowData[10]);
                        obj.setTD(RowData[11]);
                        obj.setPremium(RowData[12]);
                        obj.setFreightCharge(RowData[13]);
                        obj.setAmount(RowData[14]);
                        obj.setPlant(RowData[15]);
                        obj.setState(RowData[16]);
                        obj.setZone(RowData[17]);
                        mSauadaTransactionLogList.add(obj);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToSaudaTransactionLog(mSauadaTransactionLogList);
        Constants.isSaudaTransactionUpdated = false;
        if (insertStatus == noRows && noRows > 0) {
            if (mIsnavigationon) {
                decideNavigation();
            }
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "sauda_transaction_log", true, "");
        } else if (noRows == 0 && noColumn != 0) {
            if (mIsnavigationon) {
                decideNavigation();
            }
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void Increment_sauda_transaction_log() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSaudaTransactionUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_git_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isGITTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_destination_master() {
        if (Constants.isFirstLoginOfApp || Constants.isDestinationUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_scheme_pdf_master() {
        if (Constants.isFirstLoginOfApp || Constants.isSchemePdfMasterUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_scheme_pdf_master_without_branch() {
        if (Constants.isFirstLoginOfApp || Constants.isSchemePdfMasterWithoutBranchUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_branch_wise_golden_rule() {
        if (Constants.isFirstLoginOfApp || Constants.isBranchWiseGoldenRuleMasterUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_customer_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isCustomerTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_route_customer_plan() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isRouteCustomer)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_survey_table_view() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isTableView)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_fs_survey_publish() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isFsSurveyPublishTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_catalogue_info() {
        if ((Constants.isFirstLoginOfApp)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_facilitator_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isFacilitatorTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_site_master() {
        if ((Constants.isFirstLoginOfApp || (Constants.isSiteTableUpdated))) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_distributor_route_relation() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isDistributorRouteTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_non_trade_customer_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isNonTradeCustomer)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_offer_publish() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isOfferPublishTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_product_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isProductTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_stock_balance_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isStockBalanceReportTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_customer_product_billing() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isCustomerProductBillingUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_cat_subcat_brand_prod_mapping() {
        if ((Constants.isFirstLoginOfApp)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_product_group_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isProductGroupTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_load_distribution() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isLoadDistributionTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_branch_route_freight() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isBranchRouteFreightTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_bank_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isBankTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_attendance_report() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isAttendanceReportTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_broker_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isBrokerUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_credit_limit() {
        if (Constants.isFirstLoginOfApp) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_product_sub_group_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isProductSubGroupTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_closing_stock() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isClosingStockUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_mrp_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isMrpTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_prev_stock_counting_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isPrevStockCountingUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_prodqty_custclass_wise_TD() {
        if ((Constants.isFirstLoginOfApp)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_loyalty_customer() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isLoyaltyTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_scheme_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isLoyaltyTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_loyalty_purchase_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isLoyaltyPurchaseUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_redeeme_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isLoyaltyTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_rds_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isRDSTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_emp_master() {
        if (Constants.isFirstLoginOfApp || Constants.isEmployeeUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_cash_transfer_master() {
        if (Constants.isFirstLoginOfApp || Constants.isEmployeeUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_branch_master() {
        if (Constants.isFirstLoginOfApp || Constants.isBranchUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_honey_comb_master() {
        if (Constants.isFirstLoginOfApp || Constants.isHoneyCombUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_margin_master() {
        if (Constants.isFirstLoginOfApp || Constants.isMarginCostUpdated) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_customer_branch_relation() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isCustBranchUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_sample() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSampleMasterTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrement_mis_transaction_log() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isMISTransactionTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_survey_input_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSurveyTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_sauda_allocation_log() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSaudaAllocationUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_street_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isStreetUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_prev_order_counting_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isPreviousOrderCounting)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_farmer_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isFarmerMaster)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_order_status() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isOrderStatus)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_sauda_mrp() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSaudaMrpTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_customer_product_relation() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isCustomerProductTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_customer_product_info() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isCustomerProductInfoTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_mcx_rate() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isMcxRateTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_order_approval() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isOrderApprovalTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_branch_destination() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isBranchDestinationTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_branch_dump() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isBranchDumpTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_customer_broker_relation() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isCustomerBrokerRelationTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_brokerage_cost() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isBrokarageCostTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_depot_cost() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isDepotCostTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_freight_cost() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isFreightCostTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    private void Download_txt(String URL) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile ;
        InputStream is = null;
        URL url ;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
            if (outputFile.exists())
                outputFile.delete();
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.setConnectTimeout(0);
            c.connect();
            is = c.getInputStream();
            byte[] buffer = new byte[1024];
            int len1;
            while ((len1 = is.read(buffer)) != -1) {
                fbo.write(buffer, 0, len1);
            }
            fbo.flush();
        } catch (Exception ignored) {
        } finally {
            if (c != null)
                c.disconnect();
            if (fbo != null)
                try {
                    fbo.close();
                } catch (IOException ignored) {
                }
            if (is != null)
                try {
                    is.close();
                } catch (IOException ignored) {
                }
        }
    }

    private void Download_txt_with_custom_name(String URL, String name) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile;
        InputStream is = null;
        URL url;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + name + ".txt");
            if (outputFile.exists())
                outputFile.delete();
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.setConnectTimeout(0);
            c.connect();
            is = c.getInputStream();
            byte[] buffer = new byte[1024];
            int len1;
            while ((len1 = is.read(buffer)) != -1) {
                fbo.write(buffer, 0, len1);
            }
            fbo.flush();
        } catch (Exception ignored) {
        } finally {
            if (c != null)
                c.disconnect();
            if (fbo != null)
                try {
                    fbo.close();
                } catch (IOException ignored) {
                }
            if (is != null)
                try {
                    is.close();
                } catch (IOException ignored) {
                }
        }
    }

    public static void Download_txt_laravel(InputStream inputStrm, String fileName, Context mContext) {
        FileOutputStream fileOutputStrm = null;
        File outputFile;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + fileName);
            if (outputFile.exists()) {
                outputFile.delete();
            }
            fileOutputStrm = new FileOutputStream(outputFile, false);
            byte[] buffer = new byte[1024];
            int len1;
            while ((len1 = inputStrm.read(buffer)) != -1) {
                fileOutputStrm.write(buffer, 0, len1);
            }
            fileOutputStrm.flush();
        } catch (Exception ignored) {
        } finally {
            if (fileOutputStrm != null)
                try {
                    fileOutputStrm.close();
                } catch (IOException ignored) {
                }
            if (inputStrm != null)
                try {
                    inputStrm.close();
                } catch (IOException ignored) {
                }
        }
    }

    public void decideNavigation() {
        if (!(noRows == 0 && noColumn != 0)) {
            dbHelper.insertToLogTable(timeStamp, status);
        }
    }

    public void Increment_route_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isRouteTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_product_brand_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isProductBrandTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_survey_publish() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSurveyPublishTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void add_last_update_time(String tableName) {
        dbHelper.insertToLogTable(timeStamp, tableName);
    }

    public void _DOWNLOAD_yellow_card_date_validation_customerwise() {
        Increment_yellow_card_date_validation_customerwise();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.yellowCardValidationDateCustomerWise + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<YellowCardDateValidation> mSelfAppraisalProductGroupWiseList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        YellowCardDateValidation temp = new YellowCardDateValidation();
                        temp.setCustomer_code(RowData[0]);
                        temp.setValidation_from(RowData[1]);
                        temp.setValidation_to(RowData[2]);
                        temp.setValidation_last_date(RowData[3]);
                        mSelfAppraisalProductGroupWiseList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToYellowCardValidationCustomerwise(mSelfAppraisalProductGroupWiseList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSiteTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSiteTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void Increment_yellow_card_date_validation_customerwise() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSurveyPublishTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void _DOWNLOAD_market_feedback_tagging_master() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.marketFeedbackTaggingDetails + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<MarketFeedbackTagging> mCompetitorGroupMasterList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MarketFeedbackTagging temp = new MarketFeedbackTagging();
                        temp.setCustomer_code(RowData[0]);
                        temp.setIs_active(RowData[1]);
                        mCompetitorGroupMasterList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToMarketFeedBackTaggingMaster(mCompetitorGroupMasterList);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "market_feedback_tagging");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_VendorDetailsDownload() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.vendorDetailsDownload + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<BrandingVendorDetails> mBrandingVendorDetails = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        BrandingVendorDetails temp = new BrandingVendorDetails();
                        temp.setState(RowData[0]);
                        temp.setArea(RowData[1]);
                        temp.setCity(RowData[2]);
                        temp.setLocation(RowData[3]);
                        temp.setFacing(RowData[4]);
                        temp.setMedia(RowData[5]);
                        temp.setType(RowData[6]);
                        temp.setL_R(RowData[7]);
                        temp.setT_B(RowData[8]);
                        temp.setQty(RowData[9]);
                        temp.setFascia(RowData[10]);
                        temp.setSq_ft(RowData[11]);
                        temp.setVendor(RowData[12]);
                        temp.setVendor_code(RowData[13]);
                        temp.setLocation_facing(RowData[14]);
                        mBrandingVendorDetails.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.InsertToVendorDetailsDownloadMaster(mBrandingVendorDetails);
        if (insertStatus == noRows && noRows > 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "vendor-details-download");
        } else if (noRows == 0 && noColumn != 0) {
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void DOWNLOAD_target_achievement_route_categorywise() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.targetAchievementDetailsRouteWise + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<TargetAchievementRouteCategorywise> customerList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        TargetAchievementRouteCategorywise temp = new TargetAchievementRouteCategorywise();
                        temp.setEmp_code(RowData[0]);
                        temp.setEmp_name(RowData[1]);
                        temp.setRoute_code(RowData[2]);
                        temp.setRoute_name(RowData[3]);
                        temp.setRoute_no(RowData[4]);
                        temp.setArea(RowData[5]);
                        temp.setDTS_target(RowData[6]);
                        temp.setDTS_achievement(RowData[7]);
                        temp.setDW_achievement(RowData[8]);
                        temp.setDW_target(RowData[9]);
                        temp.setOthers_target(RowData[10]);
                        temp.setOthers_achievement(RowData[11]);
                        temp.setMonth((RowData[12]));
                        customerList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToSelf_appraisal_route_product_group_wise(customerList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isTargetTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "target_achievement_route_categorywise");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isTargetTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_complaint_master() {
        Increment_complaint_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.complaint_master_masterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<ComplaintMaster> catalogueList = new ArrayList<>();
        ArrayList<Integer> a = new ArrayList<>();
        ArrayList<Integer> b = new ArrayList<>();
        ArrayList<String> c = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    a.add(1);
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    b.add(1);
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        c.add(RowData[0]);
                        ComplaintMaster temp = new ComplaintMaster();
                        temp.setComplaint_id(RowData[0]);
                        temp.setEmp_code(RowData[1]);
                        temp.setComplaint_segment(RowData[2]);
                        temp.setComplaint_category(RowData[3]);
                        temp.setComplaint_receiver(RowData[4]);
                        temp.setDate_of_first_visit_to_customer(RowData[5]);
                        temp.setFirst_visit_made_sales_team_name(RowData[6]);
                        temp.setFIR_submitted(RowData[7]);
                        temp.setFirst_visit_made_by_TE_TM(RowData[8]);
                        temp.setCustomer_name(RowData[9]);
                        temp.setCustomer_contact_no(RowData[10]);
                        temp.setCustomer_address_pin(RowData[11]);
                        temp.setType_of_complaint(RowData[12]);
                        temp.setRemarks_others(RowData[13]);
                        temp.setNature_of_complaint(RowData[14]);
                        temp.setComplaint_efforts_details(RowData[15]);
                        temp.setType_of_cement(RowData[16]);
                        temp.setName_of_the_plant(RowData[17]);
                        temp.setBatch_no(RowData[18]);
                        temp.setDate_of_supply(RowData[19]);
                        temp.setNo_of_bags_purchased(RowData[20]);
                        temp.setDate_of_usage(RowData[21]);
                        temp.setSupplied_by(RowData[22]);
                        temp.setCurrent_status_of_site(RowData[23]);
                        temp.setStorage_condition_of_cement(RowData[24]);
                        temp.setQuality_coarse_aggregates(RowData[25]);
                        temp.setQuality_fine_aggregates(RowData[26]);
                        temp.setQuality_of_water(RowData[27]);
                        temp.setQuality_of_admixture(RowData[28]);
                        temp.setDegree_quality_control(RowData[29]);
                        temp.setInvestigation_observations(RowData[31]);
                        temp.setRoot_cause_analysis(RowData[32]);
                        temp.setCorrections_suggested_technical_team(RowData[33]);
                        temp.setCustomer_is_convinced(RowData[34]);
                        temp.setStar_cement_reused(RowData[35]);
                        temp.setAction_plan_not_convinced(RowData[36]);
                        temp.setFollow_up_plan(RowData[37]);
                        temp.setManagers_recommendation(RowData[38]);
                        temp.setComplaint_status(RowData[39]);
                        temp.setExpected_date_of_closing(RowData[40]);
                        temp.setClosed_date(RowData[41]);
                        temp.setRemarks(RowData[42]);
                        temp.setUploaded_image(RowData[43]);
                        temp.setCCR_image(RowData[44]);
                        temp.setComplaint_image(RowData[45]);
                        temp.setBill2_image(RowData[46]);
                        temp.setBranch(RowData[47]);
                        temp.setDistrict(RowData[48]);
                        catalogueList.add(temp);
                    } 
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToComplaintMaster(catalogueList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isComplaintMasterTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isComplaintMasterTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_lead_generation_master() {
        Increment_lead_generation_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.lead_generation_masterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<LeadGenerationMaster> catalogueList = new ArrayList<>();
        ArrayList<Integer> a = new ArrayList<>();
        ArrayList<Integer> b = new ArrayList<>();
        ArrayList<String> c = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    a.add(1);
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    b.add(1);
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        c.add(RowData[0]);
                        LeadGenerationMaster temp = new LeadGenerationMaster();
                        temp.setLead_generation_id(RowData[0]);
                        temp.setEmp_code(RowData[1]);
                        temp.setLead_type(RowData[2]);
                        temp.setParty_name(RowData[3]);
                        temp.setBranch(RowData[4]);
                        temp.setDistrict(RowData[5]);
                        temp.setState(RowData[6]);
                        temp.setQty_req(RowData[7]);
                        temp.setProduct_packaging(RowData[8]);
                        temp.setExp_rate_per_bag(RowData[9]);
                        temp.setContact_person_name(RowData[10]);
                        temp.setContact_number(RowData[11]);
                        temp.setMail_id(RowData[12]);
                        temp.setMode(RowData[13]);
                        temp.setQuotation(RowData[14]);
                        temp.setPO(RowData[15]);
                        temp.setStatus(RowData[16]);
                        temp.setRemarks(RowData[17]);
                        temp.setAssigned_to(RowData[18]);
                        temp.setSelf_other(RowData[19]);
                        temp.setAcc_block_is_required(RowData[20]);
                        temp.setCategory_type_construction(RowData[21]);
                        temp.setNext_visit_date(RowData[22]);
                        temp.setLead_status(RowData[23]);
                        temp.setCurrent_brand_used(RowData[24]);
                        temp.setCurrent_price(RowData[25]);
                        temp.setR_timing(RowData[26]);
                        temp.setAction_on_lead(RowData[27]);
                        temp.setApproved_price(RowData[28]);
                        catalogueList.add(temp);
                    } 
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToleadGenerationMaster(catalogueList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isLeadGenerationMasterTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isLeadGenerationMasterTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void Increment_complaint_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isComplaintMasterTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_lead_generation_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isLeadGenerationMasterTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_quality_complaint_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isQualityComplaintMasterTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void Increment_MtlTestingFormat_master() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isMtlTestingMasterTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void _DOWNLOAD_quality_complaint_master() {
        Increment_quality_complaint_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.quality_complaint_masterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<QualityComplaint> catalogueList = new ArrayList<>();
        ArrayList<Integer> a = new ArrayList<>();
        ArrayList<Integer> b = new ArrayList<>();
        ArrayList<String> c = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    a.add(1);
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    b.add(1);
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        c.add(RowData[0]);
                        QualityComplaint temp = new QualityComplaint();
                        temp.setQualityComplaintId(RowData[0]);
                        temp.setMtlNo(RowData[1]);
                        temp.setEmpCode(RowData[2]);
                        temp.setComplaintSegment(RowData[3]);
                        temp.setComplaintCategory(RowData[4]);
                        temp.setComplaintReceiver(RowData[5]);
                        temp.setDateOfFirstVisitToCustomer(RowData[6]);
                        temp.setFirstVisitMadeSalesTeamName(RowData[7]);
                        temp.setFirSubmitted(RowData[8]);
                        temp.setFirstVisitMadeByTeTm(RowData[9]);
                        temp.setCustomerName(RowData[10]);
                        temp.setCustomerContactNo(RowData[11]);
                        temp.setCustomerAddressPin(RowData[12]);
                        temp.setTypeOfComplaint(RowData[13]);
                        temp.setRemarksOthers(RowData[14]);
                        temp.setNatureOfComplaint(RowData[15]);
                        temp.setComplaintEffortsDetails(RowData[16]);
                        temp.setTypeOfCement(RowData[17]);
                        temp.setNameOfThePlant(RowData[18]);
                        temp.setBatchNo(RowData[19]);
                        temp.setDateOfSupply(RowData[20]);
                        temp.setNoOfBagsPurchased(RowData[21]);
                        temp.setDateOfUsage(RowData[22]);
                        temp.setSuppliedBy(RowData[23]);
                        temp.setCurrentStatusOfSite(RowData[24]);
                        temp.setStorageConditionOfCement(RowData[25]);
                        temp.setWeightOfCementBags(RowData[26]);
                        temp.setQualityCoarseAggregates(RowData[27]);
                        temp.setQualityFineAggregates(RowData[28]);
                        temp.setQualityOfWater(RowData[29]);
                        temp.setQualityOfAdmixture(RowData[30]);
                        temp.setDegreeQualityControl(RowData[31]);
                        temp.setInvestigationObservations(RowData[32]);
                        temp.setRootCauseAnalysis(RowData[33]);
                        temp.setCorrectionsSuggestedTechnicalTeam(RowData[34]);
                        temp.setCustomerIsConvinced(RowData[35]);
                        temp.setStarCementReused(RowData[36]);
                        temp.setActionPlanNotConvinced(RowData[37]);
                        temp.setFollowUpPlan(RowData[38]);
                        temp.setManagersRecommendation(RowData[39]);
                        temp.setComplaintStatus(RowData[40]);
                        temp.setExpectedDateOfClosing(RowData[41]);
                        temp.setClosedDate(RowData[42]);
                        temp.setRemarks(RowData[43]);
                        temp.setFirImage(RowData[44]);
                        temp.setDownloadTime(RowData[45]);
                        temp.setCcrImage(RowData[46]);
                        temp.setComplaintImage(RowData[47]);
                        temp.setBill2Image(RowData[48]);
                        temp.setBranch(RowData[49]);
                        temp.setDistrict(RowData[50]);
                        temp.setTypeTestRequired1(RowData[51]);
                        temp.setTypeTestRequired2(RowData[52]);
                        temp.setTypeTestRequired3(RowData[53]);
                        temp.setTypeTestRequired4(RowData[54]);
                        temp.setTypeTestRequired5(RowData[55]);
                        temp.setTypeTestRequired6(RowData[56]);
                        temp.setOverallTestResult(RowData[57]);
                        temp.setForwordedTo(RowData[58]);
                        catalogueList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToQuality_complaintMaster(catalogueList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isQualityComplaintMasterTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isQualityComplaintMasterTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_MtlTestingFormat_master() {
        Increment_MtlTestingFormat_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.mtl_testing_masterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<MtlTestingFormat> catalogueList = new ArrayList<>();
        ArrayList<Integer> a = new ArrayList<>();
        ArrayList<Integer> b = new ArrayList<>();
        ArrayList<String> c = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    a.add(1);
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    b.add(1);
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        c.add(RowData[0]);
                        MtlTestingFormat temp = new MtlTestingFormat();
                        temp.setMtlTestingFormatId(RowData[0]);
                        temp.setMtlNo(RowData[1]);
                        temp.setEmpCode(RowData[2]);
                        temp.setRegion(RowData[3]);
                        temp.setBranch(RowData[4]);
                        temp.setDistrict(RowData[5]);
                        temp.setCustomerName(RowData[6]);
                        temp.setContactNumber(RowData[7]);
                        temp.setFullAddress(RowData[8]);
                        temp.setPettyContractorMasonName(RowData[9]);
                        temp.setPettyContractorMasonContactNo(RowData[10]);
                        temp.setConsultingEngineerName(RowData[11]);
                        temp.setConsultingEngineerNo(RowData[12]);
                        temp.setSiteSegment(RowData[13]);
                        temp.setServiceCategory(RowData[14]);
                        temp.setDhalaiDate(RowData[15]);
                        temp.setTypeOfConstruction(RowData[16]);
                        temp.setAreaSqr(RowData[17]);
                        temp.setCurrentStageConstruction(RowData[18]);
                        temp.setBrandTypeCementUsed(RowData[19]);
                        temp.setConsumedTillDateBag(RowData[20]);
                        temp.setFutureRequirementBag(RowData[21]);
                        temp.setLinkedAssociatedDealer(RowData[22]);
                        temp.setCoverBlockQty(RowData[23]);
                        temp.setCoverBlockPlacementInspection(RowData[24]);
                        temp.setPlan(RowData[25]);
                        temp.setStructuralDrawing(RowData[26]);
                        temp.setBbsInspection(RowData[27]);
                        temp.setStructuralInspection(RowData[28]);
                        temp.setFormworkInspection(RowData[29]);
                        temp.setAggregateInspection(RowData[30]);
                        temp.setSieveAnalysis(RowData[31]);
                        temp.setWaterPhTest(RowData[32]);
                        temp.setWaterTdsTest(RowData[33]);
                        temp.setSiltTest(RowData[34]);
                        temp.setMixProportionInspection(RowData[35]);
                        temp.setBallTest(RowData[36]);
                        temp.setSlumpTest(RowData[37]);
                        temp.setCubeTest(RowData[38]);
                        temp.setCubeTestResultAfter3days(RowData[39]);
                        temp.setCubeTestResultAfter7days(RowData[40]);
                        temp.setCubeTestResultAfter28days(RowData[41]);
                        temp.setCubeTestDate(RowData[42]);
                        temp.setCubeTestAfter3days(RowData[43]);
                        temp.setCubeTestAfter7days(RowData[44]);
                        temp.setCubeTestAfter28days(RowData[45]);
                        temp.setCubeTestDone(RowData[46]);
                        temp.setCubeTestRemarks(RowData[47]);
                        temp.setNdtTest(RowData[48]);
                        temp.setSlabSupervision(RowData[49]);
                        temp.setMixDesignConcrete(RowData[50]);
                        temp.setMtvServices(RowData[51]);
                        temp.setTechnicalServiceOthers(RowData[52]);
                        temp.setGiftGiven(RowData[53]);
                        temp.setYesServiceDetails(RowData[54]);
                        temp.setCubeTestDoneYn(RowData[55]);
                        temp.setPhoto(RowData[56]);
                        catalogueList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToMtl_Testing_Master(catalogueList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isMtlTestingMasterTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isMtlTestingMasterTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_site_lead_conversion_masterURL() {
        Increment_site_lead_conversion_masterURL();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.site_lead_conversion_masterURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + mIsInCremental + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<SteLeadConversionMaster> catalogueList = new ArrayList<>();
        ArrayList<Integer> a = new ArrayList<>();
        ArrayList<Integer> b = new ArrayList<>();
        ArrayList<String> c = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    a.add(1);
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    b.add(1);
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        c.add(RowData[0]);
                        SteLeadConversionMaster temp = new SteLeadConversionMaster();
                        temp.setSite_lead_conversion_id(RowData[0]);
                        temp.setEmp_code(RowData[1]);
                        temp.setCustomer_name(RowData[2]);
                        temp.setCustomer_contact_no(RowData[3]);
                        temp.setFull_address(RowData[4]);
                        temp.setPetty_contractor_head_mason_name(RowData[5]);
                        temp.setPetty_contractor_head_mason_contact_no(RowData[6]);
                        temp.setEngineer_name(RowData[7]);
                        temp.setEngineer_contact_no(RowData[8]);
                        temp.setEngineer_regd_in_star_stellar(RowData[9]);
                        temp.setSite_segment(RowData[10]);
                        temp.setVisit_type(RowData[11]);
                        temp.setProject_segment(RowData[12]);
                        temp.setType_of_construction(RowData[13]);
                        temp.setSite_potential_no_of_bags(RowData[14]);
                        temp.setCurrent_stage_of_construction(RowData[15]);
                        temp.setCement_brand_used(RowData[16]);
                        temp.setOther_brand(RowData[17]);
                        temp.setConsumed_till_date_no_of_bags(RowData[18]);
                        temp.setEstimated_requirement_no_of_bags(RowData[19]);
                        temp.setMeeting_person(RowData[20]);
                        temp.setDecision_maker(RowData[21]);
                        temp.setConversion(RowData[22]);
                        temp.setProduct(RowData[23]);
                        temp.setRequested_date_of_delivery(RowData[24]);
                        temp.setNo_of_bags_ordered(RowData[25]);
                        temp.setLead_forwarded_dealer_rssd_name(RowData[26]);
                        temp.setActual_date_of_delivery(RowData[27]);
                        temp.setReason_for_not_delivery(RowData[28]);
                        temp.setReasons_for_non_conversion(RowData[29]);
                        temp.setOther_remarks(RowData[30]);
                        temp.setOverall_remarks(RowData[31]);
                        temp.setBranch(RowData[32]);
                        temp.setDistrict(RowData[33]);
                        temp.setPrice_rsp_bags(RowData[34]);
                        catalogueList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToSiteLeadConversionMaster(catalogueList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isSteLeadConversionMasterUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSteLeadConversionMasterUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void Increment_site_lead_conversion_masterURL() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSteLeadConversionMasterUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void _DOWNLOAD_customer_master_mf_taging() {
        Increment_customer_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.customerDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=no" + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        ArrayList<CustomerDetails> customerList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        CustomerDetails temp = new CustomerDetails();
                        temp.setCustomerCode(RowData[0]);
                        temp.setCustomerName(RowData[1]);
                        temp.setRouteCode(RowData[2]);
                        temp.setEmpCode(RowData[3]);
                        temp.setCurrentBalance(RowData[4]);
                        temp.setCreditLimit(RowData[5]);
                        temp.setIsACEDNS(RowData[6]);
                        temp.setIsBlackList(RowData[7]);
                        temp.setTradeDiscount(RowData[8]);
                        temp.setCustomerType(RowData[9]);
                        temp.setRdsTag(RowData[10]);
                        temp.setSaudaValidityPeriod(RowData[11]);
                        temp.setAddress(RowData[12]);
                        temp.setPin(RowData[13]);
                        temp.setNumber(RowData[14]);
                        temp.setDnsCustCode(RowData[15]);
                        temp.setLandlineNo(RowData[16]);
                        temp.setOwnerName(RowData[17]);
                        temp.setOwnerPhone(RowData[18]);
                        temp.setCustClass(RowData[19]);
                        temp.setWeeklyClosingDay(RowData[20]);
                        temp.setCoverageType(RowData[21]);
                        temp.setTIN(RowData[22]);
                        temp.setPAN(RowData[23]);
                        temp.setMinimumStock(RowData[24]);
                        temp.setBranchCode(RowData[25]);
                        temp.setVisitDay(RowData[26]);
                        temp.setEmail(RowData[27]);
                        temp.setSaudaLimit(RowData[28]);
                        temp.setPendingQty(RowData[29]);
                        temp.setIncoTerms(RowData[30]);
                        temp.setLoadabilityTon(RowData[31]);
                        temp.setTransportMode(RowData[32]);
                        temp.setstate(RowData[33]);
                        temp.setSaudaType(RowData[34]);
                        temp.setzone(RowData[35]);
                        temp.setVisitSequence(RowData[36]);
                        temp.setactivated(RowData[37]);
                        temp.setactivated_customer_code(RowData[38]);
                        temp.setretailer_app(RowData[39]);
                        temp.setbase_latt(RowData[40]);
                        temp.setbase_longi(RowData[41]);
                        temp.setneed_location_update(RowData[42]);
                        temp.setciLogic(RowData[44]);
                        temp.setcategoryOfStore(RowData[45]);
                        temp.setinStoreActivityPossible(RowData[46]);
                        temp.setIsNewCustomer(RowData[47]);
                        temp.setownerImage(RowData[48]);
                        temp.setfirmName(RowData[49]);
                        temp.setoutletImage(RowData[50]);
                        temp.setgstImage(RowData[51]);
                        temp.setadharNo(RowData[52]);
                        temp.setadharImage(RowData[53]);
                        temp.setWhatsappNumber(RowData[54]);
                        temp.setDateOfBirth(RowData[55]);
                        temp.setDateOfAnniversary(RowData[56]);
                        temp.setSpouseDateOfBirth(RowData[57]);
                        temp.setFlag("1");
                        customerList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        dbHelper.getMenuDetailsObj();
        long insertStatus = dbHelper.insertToCustomerMaster(customerList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isCustomerTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "customer_master_mf");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isCustomerTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_route_master_mf() {
        Increment_route_master();
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.routeDetailsURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=no" + "&last_update_time=" + lastUpdate + "&data_download_time=" + dwnldDictTime;
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<RouteDetails> routeList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RouteDetails temp = new RouteDetails();
                        temp.setRouteCode(RowData[0]);
                        temp.setRouteName(RowData[1]);
                        temp.setReplacingRouteCode(RowData[2]);
                        temp.setbranch_code(RowData[3]);
                        routeList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertToRouteMaster(routeList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isRouteTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn == 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "route_plan_mf");
        } else if (noRows == 0) {
            Constants.isRouteTableUpdated = false;
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_KYC_MASTER() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.kyc_master_txt_incrementalURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes");
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<KycMaster> routePlanList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else if (line.indexOf("µ") > 0) {
                    String[] dataArray = line.split("µ");
                    accessStartDate = dataArray[0];
                    accessEndDate = dataArray[1];
                    period = dataArray[2];
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        KycMaster temp = new KycMaster();
                        temp.setKyc_id(RowData[0]);
                        temp.setEmp_code(RowData[1]);
                        temp.setWholesale_retail(RowData[2]);
                        temp.setFirm_name(RowData[3]);
                        temp.setAddress(RowData[4]);
                        temp.setPin(RowData[5]);
                        temp.setArea(RowData[6]);
                        temp.setCity(RowData[7]);
                        temp.setState(RowData[8]);
                        temp.setContact_person_name(RowData[9]);
                        temp.setMobile(RowData[10]);
                        temp.setGST(RowData[11]);
                        temp.setPAN(RowData[12]);
                        temp.setBirth_date(RowData[13]);
                        temp.setAnniversary_date(RowData[14]);
                        temp.setYears_business(RowData[15]);
                        temp.setSelling_brand(RowData[16]);
                        temp.setCustomer_other_qty(RowData[17]);
                        temp.setCustomer_agree(RowData[18]);
                        temp.setCustomer_p_category(RowData[19]);
                        temp.setCustomer_deal_business(RowData[20]);
                        temp.setCard_image(RowData[21]);
                        routePlanList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = -1;
        if (!routePlanList.isEmpty()) {
            insertStatus = dbHelper.InsertToKycMaster(routePlanList);
        }
        if (insertStatus == noRows || noRows == 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "kyc_master");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_SITE_LEAD_APPROVAL_MASTER() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.site_lead_download_approvalURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&incremental_download=no";
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<KycMaster> routePlanList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else if (line.indexOf("µ") > 0) {
                    String[] dataArray = line.split("µ");
                    accessStartDate = dataArray[0];
                    accessEndDate = dataArray[1];
                    period = dataArray[2];
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        KycMaster temp = new KycMaster();
                        temp.setKyc_id(RowData[0]);
                        temp.setEmp_code(RowData[1]);
                        temp.setWholesale_retail(RowData[2]);
                        temp.setFirm_name(RowData[3]);
                        temp.setAddress(RowData[4]);
                        temp.setPin(RowData[5]);
                        temp.setArea(RowData[6]);
                        temp.setCity(RowData[7]);
                        temp.setState(RowData[8]);
                        temp.setContact_person_name(RowData[9]);
                        temp.setMobile(RowData[10]);
                        temp.setGST(RowData[11]);
                        temp.setPAN(RowData[12]);
                        temp.setBirth_date(RowData[13]);
                        temp.setAnniversary_date(RowData[14]);
                        temp.setYears_business(RowData[15]);
                        temp.setSelling_brand(RowData[16]);
                        temp.setCustomer_other_qty(RowData[17]);
                        temp.setCustomer_agree(RowData[18]);
                        temp.setCustomer_p_category(RowData[19]);
                        temp.setCustomer_deal_business(RowData[20]);
                        temp.setCard_image(RowData[21]);
                        routePlanList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = -1;
        if (insertStatus == noRows || noRows == 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "site_lead_approval");
        } else {
            Constants.isDownLoadComplete = false;
        }
    }

    public void _DOWNLOAD_MIS_DASHBOARD() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.dshboardDataURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Download_txt(URL);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException ignored) {
        }
        ArrayList<DashboardData> routeList = new ArrayList<>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line;
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        DashboardData temp = new DashboardData();
                        temp.setEmp_code(RowData[0]);
                        temp.setEmp_name(RowData[1]);
                        temp.setData_date(RowData[2]);
                        temp.setAtt_time(RowData[3]);
                        temp.setChk_out_time(RowData[4]);
                        temp.setCounter_meet(RowData[5]);
                        temp.setMega_mason_meet(RowData[6]);
                        temp.setEngineers_meet(RowData[7]);
                        temp.setProfessional_meet(RowData[8]);
                        temp.setContractor_meet(RowData[9]);
                        temp.setDealer_subdealer_meet(RowData[10]);
                        temp.setComplaint(RowData[11]);
                        temp.setMason_meet(RowData[12]);
                        temp.setIHB_meet(RowData[13]);
                        temp.setSmall_engineers_meet(RowData[14]);
                        temp.setBig_contractor_meet(RowData[15]);
                        temp.setCatch_them_young(RowData[16]);
                        temp.setPc_traning_programme(RowData[17]);
                        temp.setCustomer_guidance_camp(RowData[18]);
                        temp.setSite_visit(RowData[19]);
                        temp.setComplaint_report(RowData[20]);
                        temp.setDhalai_service(RowData[21]);
                        temp.setSiteTracking(RowData[22]);
                        routeList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ignored) {
        }
        long insertStatus = dbHelper.insertDashBoardData(routeList);
        if (insertStatus == noRows && noRows > 0) {
            Constants.isRouteTableUpdated = false;
            decideNavigation();
        } else if (noRows == 0 && noColumn == 0) {
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "dashboard_data_download");
        } else if (noRows == 0) {
            Constants.isRouteTableUpdated = false;
            decideNavigation();
        } else {
            Constants.isDownLoadComplete = false;
        }
    }
}