package com.forcepower.acedns.activity.non_auth.main_menu.market_overview;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;

import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.ActivitySurveyDCA;
import com.forcepower.acedns.activity.ActivitySurveyOffer;
import com.forcepower.acedns.new_activity.nt_quotation.activity.lead_query.LeadQueryActivity;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.new_activity.khoj.NewKhojActivity;
import com.forcepower.acedns.adapter.MallAdapter;
import com.forcepower.acedns.adapter.MenuAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.backgroundTask.MASTER_LoadNonTradeCustomerTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitFootSoldier;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.MenuObj;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.new_activity.sitelead.NewSiteLeadActivity;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Objects;

public class ActivitySurveyLanding extends AceDnsParentActivity implements OnClickListener {

    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static GridView mGridViewMenu = null;
    public ProgressDialog mPrepareSurveyMenuProgressDialog;
    public Handler mPrepareSurveyMenuHandler;
    MenuAdapter mMenuAdapter;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    Context mContext;
    ArrayList<MenuObj> mMenuList;
    String[] mSurveyInputMenuList;
    String surveymenudetails = "";
    String[] mTypeList;
    ArrayList<MallMaster> mMallMasterList;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    ArrayList<RouteDetails> mRouteDetailsList;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    GPSTracker gpstracker;
    private String mMenu = "";
    private String mType = "";
    private String mPinCode = "";
    private String mMallID = "";
    private String mMenuType = "";
    String userType="";
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    private boolean isAttendanceGiven = false;

    String sale_access="";

    @SuppressLint("HandlerLeak")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey_landing);
        RegisterActivities.registerActivity(this);
        mContext = ActivitySurveyLanding.this;
        mNewDatabaseForSiteLead=new NewDatabaseForSiteLead(mContext);
        userType = mNewDatabaseForSiteLead.getEmpDesignation(Constants.employeeDetailObject.getEmpCode());
        mAceDnsDatabase = new AceDnsDatabase(ActivitySurveyLanding.this);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(ActivitySurveyLanding.this);

        sale_access=mAceDnsDatabase.getEmpSaleAccess(Constants.employeeDetailObject.getEmpCode());
        Log.d("TAG", "_DDDDD_ onCreate: "+Constants.employeeDetailObject.getEmpCode());
        Log.d("TAG", "_DDDDD_ onCreate: "+sale_access);

        surveymenudetails = getIntent().getStringExtra("SURVEYSUBMENUDETAILS");
        assert surveymenudetails != null;

        isAttendanceGiven = mAceDnsTransactionDatabase.getAttendanceForToday();

        InitializeView();
        ParseData(surveymenudetails);
        Constants.mNoOfCapture = 0;

        mButtonBack.setOnClickListener(v -> finish());

        mGridViewMenu.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            String menu = mMenuList.get(arg2).getFeatureName();
            DoOnClickJob(menu);
        });

        mPrepareSurveyMenuHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                ActivitySurveyLanding.this.runOnUiThread(() -> {
                    switch (dojob) {
                        case 1:
                            ShowSurveyMenuList();
                            break;
                        case 2:
                            if (mTypeList.length > 0) {
                                if (mTypeList.length == 1) {
                                    mType = mTypeList[0];
                                    PrepareSurveyMenuData(3);
                                } else {
                                    ShowSurveyTypeListDialog();
                                }
                            } else {
                                Utils.showToast(mContext, "You have no type details");
                            }
                            break;
                        case 3:
                            ShowMallHighStreetListDialog();
                            break;
                        case 4:
                            ShowAreaListDialog();
                            break;
                        case 5:
                            PrepareSurveyMenuData(6);
                            break;
                        case 6:
                            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                                if (!mRoutePlanListofToday.isEmpty()) {
                                    ShowTodayRoutePlanListDialog(mRoutePlanListofToday);
                                } else {
                                    Toast.makeText(mContext, "No route plan for today", Toast.LENGTH_SHORT).show();
                                }
                            } else {
                                if (!mRouteDetailsList.isEmpty()) {
                                    ShowRouteListDialog(mRouteDetailsList);
                                } else {
                                    Toast.makeText(mContext, "No route found.\n Please Synchronize Data", Toast.LENGTH_SHORT).show();
                                }
                            }
                            break;
                        case 7:
                            if (!mCustomerDetailsList.isEmpty()) {
                                ShowCustomerListDialog();
                            } else {
                                Toast.makeText(mContext, "No non trade customer found.\nPlease Synchronize Data", Toast.LENGTH_SHORT).show();
                            }
                            break;
                    }
                });
            }
        };
    }

    @Override
    public void onResume() {
        super.onResume();
        try {
            Utils.InitialiseSETUPTableData(ActivitySurveyLanding.this);
        } catch (Exception ignored) {}
    }

    @Override
    public void onClick(View v) {}

    @SuppressLint("SetTextI18n")
    private void InitializeView() {
        mButtonBack =  findViewById(R.id.back);
        mGridViewMenu =  findViewById(R.id.grid_menu);
        mImageViewHeaderLogo =  findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
        TextView txtVersion =  findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivitySurveyLanding.this) + "~" + Utils.getDBVersion(ActivitySurveyLanding.this));
    }

    private void ParseData(String data) {
        mMenuList = new ArrayList<>();
        Log.d("TAG", "_DOWNLOAD_ ParseData: "+data);
        if (data.contains(",")) {
            String[] surveymenu = data.split(",");
            for (String menuname : surveymenu) {
                MenuObj menuObj = new MenuObj();
                if(!userType.equalsIgnoreCase("asm")){
                    if (menuname.equalsIgnoreCase("new site lead and conversion tracking")) {
                        boolean dcmaccess = mAceDnsDatabase.MenuAccess("new_site_lead_and_conversion_tracking");
                        if (dcmaccess) {
                            menuObj.setResourceId(R.drawable.sitelead);
                            menuObj.setFeatureName(menuname);
                            mMenuList.add(menuObj);
                        }
                    }
                }

                if (menuname.equalsIgnoreCase("khoj")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("khoj");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.khoj);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("FS")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean fsaccess = mAceDnsDatabase.MenuAccess("FS");
                    if (fsaccess) {
                        menuObj.setResourceId(R.drawable.fs);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("DCE")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dceaccess = mAceDnsDatabase.MenuAccess("DCE");
                    if (dceaccess) {
                        menuObj.setResourceId(R.drawable.dce);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Facilitator Add")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.fa_add);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Customer Add")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.addcustomersurvey);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("DCM")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("DCM");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.dcm);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("OFFER")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("OFFER");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.offer);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("DCA")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("DCA");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.dca);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("KYC")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("KYC");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.kyc);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("ADD NEW PROSPECT")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("ADD NEW PROSPECT");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.new_prospect);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Client")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.client);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("DVR")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("DVR");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.dvr);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("NEW KYC")&&!sale_access.equalsIgnoreCase("BD")) {
                    menuObj.setResourceId(R.drawable.nkyc);
                    menuObj.setFeatureName(menuname);
                    mMenuList.add(menuObj);

                }

                if (menuname.equalsIgnoreCase("EXISTING KYC")&&!sale_access.equalsIgnoreCase("BD")) {
                    menuObj.setResourceId(R.drawable.kyc);
                    menuObj.setFeatureName(menuname);
                    mMenuList.add(menuObj);
                }

                if (menuname.equalsIgnoreCase("Site Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Site Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.sitevisit);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Farmer Visit")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Farmer Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.farmervisit);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Customer Visit")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Customer Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.cs_visit);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Customer Feedback")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.customer_feedback);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Technical Meets")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Technical Meets");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.tm);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("market") && mAceDnsDatabase.MenuAccess("market")&&!sale_access.equalsIgnoreCase("BD")) {
                    menuObj.setResourceId(R.drawable.market_survey);
                    menuObj.setFeatureName(menuname);
                    mMenuList.add(menuObj);
                }

                if (menuname.equalsIgnoreCase("market feedback") && mAceDnsDatabase.MenuAccess("market feedback")&&!sale_access.equalsIgnoreCase("BD")) {
                    menuObj.setResourceId(R.drawable.marketfeedback_survey);
                    menuObj.setFeatureName(menuname);
                    mMenuList.add(menuObj);
                }

                if (menuname.equalsIgnoreCase("ws") && mAceDnsDatabase.MenuAccess("ws")&&!sale_access.equalsIgnoreCase("BD")) {
                    menuObj.setResourceId(R.drawable.wholesaler_survey);
                    menuObj.setFeatureName(menuname);
                    mMenuList.add(menuObj);
                }

                if (menuname.equalsIgnoreCase("remarks") && mAceDnsDatabase.MenuAccess("remarks")&&!sale_access.equalsIgnoreCase("BD")) {
                    menuObj.setResourceId(R.drawable.remarks_survey);
                    menuObj.setFeatureName(menuname);
                    mMenuList.add(menuObj);
                }

                if (menuname.equalsIgnoreCase("Branding Verification")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Branding Verification");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.ohh);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Branding")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Branding");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.branding);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Branding inspection")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.branding_inspection);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Branding requisition")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.branding_requisition);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Technical meet requisition")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.technical_meet_requisition);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("POP requisition")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.pop_requisition);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("New IHB")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("New IHB");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.newihb);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Existing IHB")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Existing IHB");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.extihb);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("IHB Site & Complaint Visit")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("IHB Site & Complaint Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.ihbsitevisit);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("New Dealer")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("New Dealer");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.newdealer);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("New Sub Dealer")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("New Sub Dealer");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.newsubdealer);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

//                if (menuname.equalsIgnoreCase("Site Lead and Conversion Tracking")) {
//                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Site Lead and Conversion Tracking");
//                    if (dcmaccess) {
//                        menuObj.setResourceId(R.drawable.sitelead);
//                        menuObj.setFeatureName(menuname);
//                        mMenuList.add(menuObj);
//                    }
//                }

                if (menuname.equalsIgnoreCase("Dhalai Services")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Dhalai Services");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.dhalaiservice);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Complaint Report SFA")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Complaint Report SFA");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.complant);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Complaint Report")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Complaint Report");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.complant);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Counter Branding")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Counter Branding");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.retail_branding);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Corporate Branding")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Corporate Branding");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.corporate_branding);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Lead Generation")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Lead Generation");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.lead_generation);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("MTL Testing Format")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("MTL Testing Format");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.mtl_testing_format);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("MLE Site Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("MLE Site Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.mle_site_visit_format);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Quality Complaint")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Quality Complaint");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.quality_complaint);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Counter Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Counter Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.counter_visit);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Mason Skill Building Program")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Mason Skill Building Program");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.mason_sbp);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Influencer")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Influencer");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.influencer);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Mason Meet")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Mason Meet");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.mason);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }

                if (menuname.equalsIgnoreCase("Engineers Meet")&&!sale_access.equalsIgnoreCase("BD")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Engineers Meet");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.eng_meet);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }
            }
        }

        if (mMenuList != null) {
            mMenuAdapter = new MenuAdapter(ActivitySurveyLanding.this, R.layout.grid_child, mMenuList);
            mGridViewMenu.setAdapter(mMenuAdapter);
        } else {
            Utils.showToast(mContext, "No survey menu found. Please Synchronize Data");
        }
    }

    private void DoOnClickJob(String menu) {
        mMenu = menu;
        if (isAttendanceGiven&&menu.equalsIgnoreCase("new site lead and conversion tracking")) {
            Log.d("TAG", "DoOnClickJob: new site lead and conversion tracking");
            Intent intent = new Intent(ActivitySurveyLanding.this, NewSiteLeadActivity.class);
            intent.putExtra("SURVEYSUBMENUDETAILS", surveymenudetails);
            startActivity(intent);
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("khoj")) {
            Log.d("TAG", "DoOnClickJob: khoj");
            Intent intent = new Intent(ActivitySurveyLanding.this, NewKhojActivity.class);
            intent.putExtra("SURVEYSUBMENUDETAILS", surveymenudetails);
            startActivity(intent);
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("FS")) {
            Log.d("TAG", "DoOnClickJob: 1");
            Constants.mSurveyMainType = menu;
            if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
                Log.d("TAG", "DoOnClickJob: 2");
                PrepareSurveyMenuData(2);
            } else {
                Utils.showToast(mContext, "You have no survey type. Please Synchronize Data");
            }
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("OFFER")) {
            Log.d("TAG", "DoOnClickJob: 3");
            Constants.mSurveyMainType = menu;
            Intent intent = new Intent(mContext, ActivitySurveyOffer.class);
            intent.putExtra("SUBMENU", menu);
            startActivity(intent);
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("DCA")) {
            Log.d("TAG", "DoOnClickJob: 4");
            Constants.mSurveyMainType = menu;
            Intent intent = new Intent(ActivitySurveyLanding.this, ActivitySurveyDCA.class);
            intent.putExtra("SUBMENU", menu);
            startActivity(intent);
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("DCM")) {
            Log.d("TAG", "DoOnClickJob: 5");
            Utils.showToast(mContext, "This feature is not available");
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("New IHB")) {
            Log.d("TAG", "DoOnClickJob: 6");
            Constants.mSurveyMainType = menu;
            if (Constants.surveyFormDetailsObj.getSurveyRoutePlan().equalsIgnoreCase("yes")) {
                Log.d("TAG", "DoOnClickJob: 7");
                PrepareSurveyMenuData(6);
            } else {
                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "DoOnClickJob: 8");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 9");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 10");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 11");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 12");
                            gotoSurveyActivityWithData(mMenu);
                        }
                    }
                } else {
                    Log.d("TAG", "DoOnClickJob: 13");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 14");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 15");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 16");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 17");
                            goToSurveyActivity();
                        }
                    }
                }
            }
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("Existing IHB")) {
            Log.d("TAG", "DoOnClickJob: 18");
            Constants.mSurveyMainType = menu;
            if (Constants.surveyFormDetailsObj.getSurveyRoutePlan().equalsIgnoreCase("yes")) {
                Log.d("TAG", "DoOnClickJob: 19");
                PrepareSurveyMenuData(6);
            } else {
                Log.d("TAG", "DoOnClickJob: 20");
                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "DoOnClickJob: 21");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 22");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 23");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 24");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 25");
                            gotoSurveyActivityWithData(mMenu);
                        }
                    }
                } else {
                    Log.d("TAG", "DoOnClickJob: 26");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 27");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 28");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 29");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 30");
                            goToSurveyActivity();
                        }
                    }
                }
            }
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("New Dealer")) {
            Log.d("TAG", "DoOnClickJob: 31");
            Constants.mSurveyMainType = menu;
            if (Constants.surveyFormDetailsObj.getSurveyRoutePlan().equalsIgnoreCase("yes")) {
                Log.d("TAG", "DoOnClickJob: 32");
                PrepareSurveyMenuData(6);
            } else {
                Log.d("TAG", "DoOnClickJob: 33");
                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "DoOnClickJob: 34");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 35");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 36");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 37");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 38");
                            gotoSurveyActivityWithData(mMenu);
                        }
                    }
                } else {
                    Log.d("TAG", "DoOnClickJob: 39");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 40");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 41");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 42");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 43");
                            goToSurveyActivity();
                        }
                    }
                }
            }
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("New Sub Dealer")) {
            Log.d("TAG", "DoOnClickJob: 44");
            Constants.mSurveyMainType = menu;
            if (Constants.surveyFormDetailsObj.getSurveyRoutePlan().equalsIgnoreCase("yes")) {
                Log.d("TAG", "DoOnClickJob: 45");
                PrepareSurveyMenuData(6);
            } else {
                Log.d("TAG", "DoOnClickJob: 46");
                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "DoOnClickJob: 47");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 48");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 49");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 50");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 51");
                            gotoSurveyActivityWithData(mMenu);
                        }
                    }
                } else {
                    Log.d("TAG", "DoOnClickJob: 52");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 53");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 54");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 55");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 56");
                            goToSurveyActivity();
                        }
                    }
                }
            }
        }
        else if (isAttendanceGiven&&menu.equalsIgnoreCase("IHB Site & Complaint Visit")) {
            Log.d("TAG", "DoOnClickJob: 57");
            Constants.mSurveyMainType = menu;
            if (Constants.surveyFormDetailsObj.getSurveyRoutePlan().equalsIgnoreCase("yes")) {
                Log.d("TAG", "DoOnClickJob: 58");
                PrepareSurveyMenuData(6);
            } else {
                Log.d("TAG", "DoOnClickJob: 59");
                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "DoOnClickJob: 60");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 61");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 62");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 63");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 64");
                            gotoSurveyActivityWithData(mMenu);
                        }
                    }
                } else {
                    Log.d("TAG", "DoOnClickJob: 65");
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        Log.d("TAG", "DoOnClickJob: 66");
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        Log.d("TAG", "DoOnClickJob: 67");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "DoOnClickJob: 68");
                            gotoActivitySurveyList();
                        } else {
                            Log.d("TAG", "DoOnClickJob: 69");
                            goToSurveyActivity();
                        }
                    }
                }
            }
        }
        else {
            Log.d("TAG", "DoOnClickJob: 70");
            GoToNextStep(menu);
        }
    }

    private void GoToNextStep(String menu) {
        Log.d("TAG", "GoToNextStep: 1");
        Constants.mSurveyMainType = menu;
        if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
            Log.d("TAG", "GoToNextStep: 2");
            if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes") && Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(menu)) {
                Log.d("TAG", "GoToNextStep: 3");
                goTOSurveyMenuActivityWithData(menu);
            } else {
                Log.d("TAG", "GoToNextStep: 4");
                if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "GoToNextStep: 5");
                    gotoActivitySurveyList();
                } else {
                    Log.d("TAG", "GoToNextStep: 6");
                    gotoSurveyActivityWithData(menu);
                }
            }
        }
        else {
            Log.d("TAG", "GoToNextStep: 7");
            if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes") && Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(menu)) {
                Log.d("TAG", "GoToNextStep: 8");
                goTOSurveyMenuActivityWithData(menu);
            } else {
                Log.d("TAG", "GoToNextStep: 9");
                if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "GoToNextStep: 10");
                    gotoActivitySurveyList();
                } else {
                    Log.d("TAG", "GoToNextStep: 11");
                    goToSurveyActivity();
                }
            }
        }
    }

    private void gotoActivitySurveyList() {
        Intent intent = new Intent(ActivitySurveyLanding.this, SurveyActivityList.class);
        startActivity(intent);
    }

    private void goTOSurveyMenuActivityWithData(String menu) {
        if(isAttendanceGiven){
            Intent intent;
            mAceDnsDatabase.GetMenuName("", menu);
            if (Constants.surveyFormDetailsObj.getspecial_input_screen().equalsIgnoreCase("yes") && checkConditionForSpecialScreen()) {
                intent = new Intent(mContext, SurveyActivitySpecial.class);
                intent.putExtra("SUBMENUSPECIAL", menu);
            } else {
                intent = new Intent(mContext, SurveyMenuActivity.class);
            }
            intent.putExtra("SUBMENU", menu);
            startActivity(intent);
        }else{
            Utils.showToast(mContext, "Please give Attendance first");
        }
    }

    @SuppressLint("SimpleDateFormat")
    private void goToSurveyActivity() {
        if (Constants.surveyFormDetailsObj.getspecial_input_screen().equalsIgnoreCase("yes")) {
            Intent intent = new Intent(mContext, SurveyActivitySpecial.class);
            startActivity(intent);
        } else {
            Constants.mCheckInOutTimeSurvey = new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime());
            Intent intent = new Intent(mContext, SurveyActivity.class);
            startActivity(intent);
        }
    }

    @SuppressLint("SimpleDateFormat")
    private void gotoSurveyActivityWithData(String menu) {
        Log.d("TAG", "gotoSurveyActivityWithData: 1 "+menu);
        Intent intent;
        if (menu.equalsIgnoreCase("Lead Generation")) {
//            intent = new Intent(mContext, SurveyActivitySpecial.class);
//            intent = new Intent(mContext, LeadGenerationActivity.class);
            intent = new Intent(mContext, LeadQueryActivity.class);
            intent.putExtra("SUBMENU", menu);
            startActivity(intent);
        }else if(menu.equalsIgnoreCase("Branding Verification")||menu.equalsIgnoreCase("Counter Branding")||menu.equalsIgnoreCase("Corporate Branding")){
            Constants.mCheckInOutTimeSurvey = new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime());
            intent = new Intent(mContext, SurveyActivity.class);
            intent.putExtra("SUBMENU", menu);
            startActivity(intent);
        }else if(isAttendanceGiven){
            if (Constants.surveyFormDetailsObj.getspecial_input_screen().equalsIgnoreCase("yes")) {
                Log.d("TAG", "gotoSurveyActivityWithData: 2");
                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                    Log.d("TAG", "gotoSurveyActivityWithData: 3");
                    mAceDnsDatabase.GetMenuName("", menu);
                    if (checkConditionForSpecialScreen()) {
                        Log.d("TAG", "gotoSurveyActivityWithData: 4  :  " + menu);
                        intent = new Intent(mContext, SurveyActivitySpecial.class);
                    } else {
                        Log.d("TAG", "gotoSurveyActivityWithData: 5");
                        Constants.mCheckInOutTimeSurvey = new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime());
                        intent = new Intent(mContext, SurveyActivity.class);
                    }
                } else {
                    Log.d("TAG", "gotoSurveyActivityWithData: 6");
                    intent = new Intent(mContext, SurveyActivitySpecial.class);
                }

            } else {
                Log.d("TAG", "gotoSurveyActivityWithData: 7");
                Constants.mCheckInOutTimeSurvey = new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime());
                intent = new Intent(mContext, SurveyActivity.class);
            }
            intent.putExtra("SUBMENU", menu);
            startActivity(intent);
        }else{
            Utils.showToast(mContext, "Please give Attendance first");
        }
    }

    private boolean checkConditionForSpecialScreen() {
        boolean isNewExisitngMenuPresent = true;
        if (Constants.mSurveyMenuDetailsList.size() != 2) {
            if (Constants.mSurveyMenuDetailsList.size() == 3) {
                if (!Constants.mSurveyMenuDetailsList.get(0).getMenuName().equalsIgnoreCase("new") || !Constants.mSurveyMenuDetailsList.get(1).getMenuName().equalsIgnoreCase("existing") || !Constants.mSurveyMenuDetailsList.get(2).getMenuName().equalsIgnoreCase("Upcoming")) {
                    isNewExisitngMenuPresent = false;
                }
            } else {
                isNewExisitngMenuPresent = false;
            }
        } else if (!Constants.mSurveyMenuDetailsList.get(0).getMenuName().equalsIgnoreCase("new") || !Constants.mSurveyMenuDetailsList.get(1).getMenuName().equalsIgnoreCase("existing")) {
            isNewExisitngMenuPresent = false;
        }
        return isNewExisitngMenuPresent;
    }

    @SuppressLint("SetTextI18n")
    public void ShowSurveyTypeListDialog() {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title = mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a type");
        ListView dialogList = mDialogDepotName.findViewById(R.id.list);

        for (int count = 0; count < mTypeList.length; count++) {
            mTypeList[count] = mTypeList[count].toUpperCase();
        }

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.simple_list_child, R.id.list_details, mTypeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            mType = Objects.requireNonNull(adapter.getItem(pos)).toLowerCase();
            mDialogDepotName.cancel();
            PrepareSurveyMenuData(3);
        });

        ImageView back = mDialogDepotName.findViewById(R.id.image_cancel);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(arg0 -> mDialogDepotName.cancel());

        Button cancel = mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        mDialogDepotName.show();
    }

    public void PrepareSurveyMenuData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        int max = 0;
                        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
                            max = mAceDnsDatabase.GetMenuName(mType);
                        }
                        mSurveyInputMenuList = new String[max];
                        for (int i = 0; i < Constants.mSurveyMenuDetailsList.size(); i++) {
                            mSurveyInputMenuList[i] = Constants.mSurveyMenuDetailsList.get(i).getMenuName();
                        }
                        break;
                    case 2:
                        if (Constants.surveyFormDetailsObj.getSurveyTypeDetails().contains(",")) {
                            mTypeList = Constants.surveyFormDetailsObj.getSurveyTypeDetails().split(",");
                        } else {
                            mTypeList = new String[1];
                            mTypeList[0] = Constants.surveyFormDetailsObj.getSurveyTypeDetails();
                        }
                        break;
                    case 3:
                        mMallMasterList = mAceDnsDatabase.GetMallMasterData(mType);
                        break;
                    case 4:
                        mMallMasterList = mAceDnsDatabase.GetMallMasterData(mType, mPinCode);
                        break;
                    case 5:
                        boolean isFinished = false;
                        MASTER_LoadNonTradeCustomerTask downLoadSaudaAllocation = new MASTER_LoadNonTradeCustomerTask(mContext);
                        downLoadSaudaAllocation.execute();
                        while (!isFinished) {
                            if (downLoadSaudaAllocation.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                        break;
                    case 6:
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                            String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
                            mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForToday(today);
                        } else {
                            mRouteDetailsList = mAceDnsDatabase.getRouteList();
                        }
                        break;
                    case 7:
                        mCustomerDetailsList = mAceDnsDatabase.getNonTradeCustomerListByRoute(Constants.mSurveyRouteCode);
                        break;
                }

                Message msg = mPrepareSurveyMenuHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyMenuHandler.sendMessage(msg);
            }
        }.start();
    }

    @SuppressLint("SetTextI18n")
    public void ShowMallHighStreetListDialog() {
        if (!mMallMasterList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(ActivitySurveyLanding.this, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);

            TextView title = mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select a " + mType);

            ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);

            if (mType.equalsIgnoreCase("hi-street")) {
                Constants.mSurveyType = "pincode";
            } else {
                Constants.mSurveyType = "mall";
            }

            final MallAdapter malladapter = new MallAdapter(ActivitySurveyLanding.this, R.layout.mall_list, mMallMasterList);
            dialogList.setAdapter(malladapter);

            EditText searchText =  mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    malladapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                if (mType.equalsIgnoreCase("hi-street")) {
                    mPinCode = Objects.requireNonNull(malladapter.getItem(arg2)).getPincode();
                    PrepareSurveyMenuData(4);
                } else {
                    Constants.selectedMallMaster = malladapter.getItem(arg2);
                    assert Constants.selectedMallMaster != null;
                    mMallID = Constants.selectedMallMaster.getMallId();
                    PrepareSurveyMenuData(1);
                }
            });

            ImageView back = mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(arg0 -> mMallHighStreetListDialog.cancel());

            Button cancel = mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);
            mMallHighStreetListDialog.show();
        } else {
            Utils.showToast(mContext, "You have no " + mType + " asigned");
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowAreaListDialog() {
        if (!mMallMasterList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(ActivitySurveyLanding.this, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);

            TextView title = mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select a " + mType);

            ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);

            Constants.mSurveyType = "area";
            final MallAdapter malladapter = new MallAdapter(ActivitySurveyLanding.this, R.layout.mall_list, mMallMasterList);
            dialogList.setAdapter(malladapter);

            EditText searchText = mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    malladapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                Constants.selectedMallMaster = malladapter.getItem(arg2);
                assert Constants.selectedMallMaster != null;
                mMallID = Constants.selectedMallMaster.getMallId();
                PrepareSurveyMenuData(1);
            });

            ImageView back = mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(arg0 -> mMallHighStreetListDialog.cancel());

            Button cancel = mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);
            mMallHighStreetListDialog.show();
        } else {
            Utils.showToast(mContext, "You have no " + mType + " asigned");
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowTodayRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> routePlanListofToday) {
        final Dialog routePlanListDialog = new Dialog(ActivitySurveyLanding.this, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routePlanListDialog.findViewById(R.id.list);
        final RoutePlanTransAdapter adapter = new RoutePlanTransAdapter(ActivitySurveyLanding.this, R.layout.route_list_child, routePlanListofToday);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            Constants.mSurveyRouteCode = Objects.requireNonNull(adapter.getItem(arg2)).getRoutecode();
            if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                    goTOSurveyMenuActivityWithData(mMenu);
                } else {
                    if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                        gotoActivitySurveyList();
                    } else {
                        gotoSurveyActivityWithData(mMenu);
                    }
                }
            } else {
                if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                    goTOSurveyMenuActivityWithData(mMenu);
                } else {
                    if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                        gotoActivitySurveyList();
                    } else {
                        goToSurveyActivity();
                    }
                }
            }
            routePlanListDialog.cancel();
        });

        Button cancel = routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routePlanListDialog.cancel());
        Button create_route = routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a customer ");
        EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        ListView dialogList = mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            Constants.selectedCustomer = adapterCust.getItem(arg2);
            if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                    goTOSurveyMenuActivityWithData(mMenu);
                } else {
                    if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                        gotoActivitySurveyList();
                    } else {
                        gotoSurveyActivityWithData(mMenu);
                    }
                }
            } else {
                if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                    goTOSurveyMenuActivityWithData(mMenu);
                } else {
                    if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                        gotoActivitySurveyList();
                    } else {
                        goToSurveyActivity();
                    }
                }
            }
            mDialogCustomer.cancel();
        });

        Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(ActivitySurveyLanding.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        final RouteAdapter adapter = new RouteAdapter(ActivitySurveyLanding.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            Constants.mSurveyRouteCode = Objects.requireNonNull(adapter.getItem(arg2)).getRouteCode();
            if (Constants.mSurveyMainType.equalsIgnoreCase("New")) {
                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            gotoActivitySurveyList();
                        } else {
                            gotoSurveyActivityWithData(mMenu);
                        }
                    }
                } else {
                    if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                        goTOSurveyMenuActivityWithData(mMenu);
                    } else {
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            gotoActivitySurveyList();
                        } else {
                            goToSurveyActivity();
                        }
                    }
                }
            } else {
                PrepareSurveyMenuData(7);
            }
            routeDialog.cancel();
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowSurveyMenuList() {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title = mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a type");
        ListView dialogList = mDialogDepotName.findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.simple_list_child, R.id.list_details, mSurveyInputMenuList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            mDialogDepotName.cancel();
            mMenuType = adapter.getItem(pos);
            ShowFootSholdierInformation();
        });

        ImageView back = mDialogDepotName.findViewById(R.id.image_cancel);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(arg0 -> mDialogDepotName.cancel());

        Button cancel = mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        mDialogDepotName.show();
    }

    @SuppressLint({"SetTextI18n","SimpleDateFormat"})
    public void ShowFootSholdierInformation() {
        final Dialog checkoutDialog = new Dialog(ActivitySurveyLanding.this, R.style.PauseDialog);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.dialog_checked_out);
        checkoutDialog.setCancelable(false);
        TextView title = checkoutDialog.findViewById(R.id.title);
        title.setText("Please add business name");

        TextView customer_name = checkoutDialog.findViewById(R.id.customer_name);
        if (Constants.selectedMallMaster != null) {
            if (mType.equalsIgnoreCase("hi-street")) {
                customer_name.setText(Constants.selectedMallMaster.getArea());
            } else {
                customer_name.setText(Constants.selectedMallMaster.getMallName());
            }
        } else {
            customer_name.setText("");
        }
        final EditText remark_box = checkoutDialog.findViewById(R.id.remark_box);
        Button submit = checkoutDialog.findViewById(R.id.btn_submit);
        submit.setOnClickListener(v -> {
            if (!remark_box.getText().toString().trim().isEmpty()) {
                String businessname = remark_box.getText().toString();
                String mallorareaname;
                checkoutDialog.cancel();
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                if (Constants.selectedMallMaster != null) {
                    if (mType.equalsIgnoreCase("hi-street")) {
                        mallorareaname = Constants.selectedMallMaster.getArea();
                    } else {
                        mallorareaname = Constants.selectedMallMaster.getMallName();
                        mPinCode = "";
                    }
                    String timeStamps = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    String trans_id = "FS" + Constants.employeeDetailObject.getEmpCode() + timeStamps;
                    gpstracker = new GPSTracker(mContext);
                    boolean LocationEnabled = gpstracker.canGetLocation();
                    if (LocationEnabled) {
                        mAceDnsTransactionDatabase.InsertFootSoldier(trans_id, mMallID, mallorareaname, mPinCode, businessname, mType, mMenuType);
                        mAceDnsTransactionDatabase.insertToLocationTable("FS", timeStamps);
                        gpstracker.stopUsingGPS();
                    }
                    new TRANS_SubmitFootSoldier(mContext, false, "SUBMIT").execute();
                }
            } else {
                Utils.showToast(mContext, "Please type the business name");
            }
        });

        ImageView back =  checkoutDialog.findViewById(R.id.image_cancel);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(arg0 -> checkoutDialog.cancel());
        checkoutDialog.show();
    }
}
