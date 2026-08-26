package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;

import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.new_activity.sitelead.NewSiteLeadDetailsActivity;
import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.KeyValueAdapter;
import com.forcepower.acedns.adapter.OrderMenuAdapter;
import com.forcepower.acedns.adapter.OutletAdapter;
import com.forcepower.acedns.adapter.SurveyDisplayMenuAdapter;
import com.forcepower.acedns.adapter.SurveyReportAdapter;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.bean.MenuObj;
import com.forcepower.acedns.bean.OutletDetails;
import com.forcepower.acedns.bean.SurveyMenuDetails;
import com.forcepower.acedns.bean.SurveyReport;
import com.forcepower.acedns.bean.SurveyReportSumary;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.new_activity.khoj.adapter.DataObjSetAdapter;
import com.forcepower.acedns.new_activity.khoj.data_set.DataObjSet;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.google.gson.Gson;

import java.util.ArrayList;
import java.util.Objects;

import static com.forcepower.acedns.constants.Constants.mSurveyMenuDetailsList;

import org.json.JSONArray;
import org.json.JSONObject;

public class ActivitySurveyReportLanding extends FragmentActivity implements OnClickListener {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static GridView mGridViewMenu = null;

    OrderMenuAdapter mMenuAdapter;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;

    Context mContext;
    ArrayList<MenuObj> mMenuList;
    ArrayList<OutletDetails> mOutletDetailsList;
    ArrayList<SurveyReport> mSurveyReportList;
    ArrayList<KeyValue> mSurveyDetailsList;
    SurveyReportSumary mSurveyReportSumary = null;
    String[] mTypeList;
    private int SELECTION = 0;
    private String mTimeStamp = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mFStartDate = "";
    private String mFEndDate = "";
    private String mType = "";
    private String mTypeDisplayToHeader = "";
    private String surveymenudetails = "";
    private String mSurveyType = "";
    private String mQuery = "";
    private String mQuery2 = "";
    private String mSurveyID = "";
    private String mSurveySubMenuID = "";
    private ProgressDialog mPrepareSurveyMenuProgressDialog;
    private Handler mPrepareSurveyMenuHandler;
    private Handler mPrepareSurveyOutletHandler;
    private Handler mDCEReportHandler;
    ProgressDialog progressDialog;

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey_report_landing);
        Log.d("TAG", "onCreate: HIT");
        RegisterActivities.registerActivity(this);
        mContext = ActivitySurveyReportLanding.this;
        mAceDnsDatabase = new AceDnsDatabase(ActivitySurveyReportLanding.this);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(ActivitySurveyReportLanding.this);
        surveymenudetails = getIntent().getStringExtra("SURVEYSUBMENUDETAILS");
        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");
        mFStartDate = getIntent().getStringExtra("FSTARTDATE");
        mFEndDate = getIntent().getStringExtra("FENDDATE");
        InitializeView();
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mButtonBack.setOnClickListener(v -> finish());

        mGridViewMenu.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            String menu = mMenuList.get(arg2).getFeatureName();
            int number = 0;
            if (!mMenuList.get(arg2).getCount().trim().isEmpty()) {
                number = Integer.parseInt(mMenuList.get(arg2).getCount());
            }
            Log.d("TAG", "_DOWNLOAD_ DoOnClickJob: "+mMenuList.get(arg2).getFeatureName());
            Log.d("TAG", "_DOWNLOAD_ DoOnClickJob: "+mMenuList.get(arg2).getCount());
            Log.d("TAG", "_DOWNLOAD_ DoOnClickJob: "+mMenuList.get(arg2).getResourceId());
            DoOnClickJob(menu, number);
        });

        mPrepareSurveyMenuHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                ActivitySurveyReportLanding.this.runOnUiThread(() -> {
                    ParseData(surveymenudetails);
                    if (mMenuList != null) {
                        mMenuAdapter = new OrderMenuAdapter(ActivitySurveyReportLanding.this, R.layout.grid_child_value, mMenuList);
                        mGridViewMenu.setAdapter(mMenuAdapter);
                        mMenuAdapter.notifyDataSetChanged();
                    } else {
                        Utils.showToast(mContext, "No report menu found. Please Synchronize Data");
                    }
                });
            }
        };


        mPrepareSurveyOutletHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                ActivitySurveyReportLanding.this.runOnUiThread(() -> {
                    switch (dojob) {
                        case 1, 3, 2:
                            Log.d("TAG", "aaaaaaaaaaa: call 1 "+mOutletDetailsList.size());
                            if (mOutletDetailsList != null && !mOutletDetailsList.isEmpty()) {
                                ShowOutletDeatails();
                            } else {
                                Utils.showToast(mContext, "No record found.");
                            }
                            break;
                        case 4:
                            Log.d("TAG", "aaaaaaaaaaa: call 2");
                            if (mSurveyDetailsList != null && !mSurveyDetailsList.isEmpty()) {
                                ShowSurveyDeatails();
                            } else {
                                Utils.showToast(mContext, "No record found.");
                            }
                            break;
                        case 5:
                            Log.d("TAG", "aaaaaaaaaaa: call 3");
                            if (mSurveyMenuDetailsList != null && !mSurveyMenuDetailsList.isEmpty()) {
                                ShowSurveyDeatailsListSubMenu();
                            } else {
                                Utils.showToast(mContext, "No record found.");
                            }
                            break;
                    }

                });
            }
        };

        mDCEReportHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivitySurveyReportLanding.this.runOnUiThread(() -> {
                    switch (job) {
                        case 1, 2, 3:
                            if (!mSurveyReportList.isEmpty()) {
                                ShowSurveyMenuList();
                            } else {
                                switch (SELECTION) {
                                    case 1:
                                        Log.d("TAG", "aaaaaaaaaaa: call 4");
                                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                                        break;
                                    case 2:
                                        Log.d("TAG", "aaaaaaaaaaa: call 5");
                                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                                        break;
                                    case 3:
                                        Log.d("TAG", "aaaaaaaaaaa: call 6");
                                        Toast.makeText(mContext, "No record found from " + mFStartDate + " to " + mFEndDate, Toast.LENGTH_LONG).show();
                                        break;
                                }
                            }
                            break;
                        case 4:
                            // showMasterListDialog();
                            if (mTypeList.length > 0) {
                                if (mTypeList.length == 1) {
                                    mSurveyType = mTypeList[0];
                                    mQuery = BuildQuery(SELECTION);
                                    FetchSurveyDCEData(SELECTION);
                                } else {
                                    ShowSurveyTypeListDialog();
                                }
                            } else {
                                Utils.showToast(mContext, "You have no type details");
                            }
                            break;
                    }
                });
            }
        };

        if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
            Log.d("TAG", "aaaaaaaaaaa: 20");
            PrepareSurveyMenuData(SELECTION);
        } else {
            Log.d("TAG", "aaaaaaaaaaa: 21");
            mType = "All Survey";
            PrepareSurveyOutletData(SELECTION);
        }
    }

    @Override
    public void onClick(View v) {
    }

    private void InitializeView() {
        mButtonBack = findViewById(R.id.back);
        mGridViewMenu = findViewById(R.id.grid_menu);
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    // Loader Function
    private void progressDialogOpen(String title) {
        runOnUiThread(() -> {
            progressDialog = new ProgressDialog(mContext);
            progressDialog.setMessage(title);
            progressDialog.setCancelable(false);
            progressDialog.show();
        });
    }

    private void progressDialogClose() {
        runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_newSiteCount_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_newSiteCount_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.baseUrl +  "misreport/api_get_count_new_site_lead.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    Log.d("TAG", "_DOWNLOAD_ count: " + url);
                    String a = HttpCalling.httpGetCallWithTextResponse(url).trim();

                    JSONObject obj = new JSONObject(a);
                    Log.d("TAG", "_DOWNLOAD_ count: " + a);
                    if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                        POST_result = obj.getInt("count_visit") + "";
                    } else {
                        POST_result = "0";
                    }
                } catch (Exception e) {
                    POST_result = "0";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                boolean dcmaccess = mAceDnsDatabase.MenuAccess("New Site Lead and Conversion Tracking");
                if (dcmaccess) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.sitelead);
                    menuObj.setFeatureName("New Site Lead and Conversion Tracking");
                    menuObj.setCount(String.valueOf(result));
                    mMenuList.add(menuObj);
                    mMenuAdapter.notifyDataSetChanged();
                }
            } catch (Exception ignored) {}
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_count_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_count_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Loading please wait...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.khojCount + "?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    Log.d("TAG", "_DOWNLOAD_ count: " + url);
                    String a = HttpCalling.httpGetCallWithTextResponse(url).trim();

                    JSONObject obj = new JSONObject(a);

                    if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                        POST_result = obj.getInt("count_visit") + "";
                    } else {
                        POST_result = "0";
                    }
                } catch (Exception e) {
                    POST_result = "0";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                boolean dcmaccess = mAceDnsDatabase.MenuAccess("Khoj");
                if (dcmaccess) {
                    MenuObj menuObj = new MenuObj();
                    menuObj.setResourceId(R.drawable.khoj);
                    menuObj.setFeatureName("Khoj");
                    menuObj.setCount(String.valueOf(result));
                    mMenuList.add(menuObj);
                    mMenuAdapter.notifyDataSetChanged();
                }
                progressDialogClose();
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    private void ParseData(String data) {
        mMenuList = new ArrayList<>();
        if (data.contains(",")) {
            String[] surveymenu = data.split(",");
            for (String menuname : surveymenu) {
                Log.d("TAG", "_DOWNLOAD_ ParseData: " + menuname);
                MenuObj menuObj = new MenuObj();
                if(menuname.equalsIgnoreCase("New Site Lead and Conversion Tracking")) {
                    new TRANS_newSiteCount_AsyncTask(mContext).execute();
                }else if (menuname.equalsIgnoreCase("Khoj")) {
                    new TRANS_count_AsyncTask(mContext).execute();
                } else if (menuname.equalsIgnoreCase("FS")) {
                    boolean fsaccess = mAceDnsDatabase.MenuAccess("FS");
                    if (fsaccess) {
                        menuObj.setResourceId(R.drawable.fs);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoFS());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("DCE")) {
                    boolean dceaccess = mAceDnsDatabase.MenuAccess("DCE");
                    if (dceaccess) {
                        menuObj.setResourceId(R.drawable.dce);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoDCE());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("DCM")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("DCM");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.dcm);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("DCA")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("DCA");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.dca);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoDCA());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("KYC")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("KYC");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.kyc);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoKYC());
                        mMenuList.add(menuObj);
                    }
                }
//                else if (menuname.equalsIgnoreCase("Site Visit")) {
//                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Site Visit");
//                    if (dcmaccess) {
//                        menuObj.setResourceId(R.drawable.sitevisit);
//                        menuObj.setFeatureName(menuname);
//                        menuObj.setCount(mSurveyReportSumary.getNoSiteVisit());
//                        mMenuList.add(menuObj);
//                    }
//                }
                else if (menuname.equalsIgnoreCase("Farmer Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Farmer Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.farmervisit);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getnoFarmerVisit());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Facilitator Add")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.fa_add);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getnoFacilitaorAdd());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Customer Add")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.addcustomersurvey);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getnoCustomerAdd());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Technical Meets")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Technical Meets");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.tm);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoTechnicalMeet());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Branding Verification")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.ohh);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getnoBrandingVerifiction());
                        mMenuList.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Dhalai Services")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Dhalai Services");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.dhalaiservice);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoDhalaiServices());
                        mMenuList.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Branding")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Branding");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.branding);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoBranding());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("New IHB")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("New IHB");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.newihb);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoNewIHB());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Existing IHB")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Existing IHB");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.extihb);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoExistingIHB());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("IHB Site & Complaint Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("IHB Site & Complaint Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.ihbsitevisit);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoNewIHBSiteVisit());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("New Dealer")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("New Dealer");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.newdealer);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoNewDealer());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("New Sub Dealer")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("New Sub Dealer");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.newsubdealer);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoNewSubDealer());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("customer feedback")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.customer_feedback);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoCustomerFeedback());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Corporate Branding")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.corporate_branding);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoCorporateBranding());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Counter Branding")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.retail_branding);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoCounterBranding());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Lead Generation")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Lead Generation");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.lead_generation);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoLeadGeneration());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("MTL Testing Format")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("MTL Testing Format");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.mtl_testing_format);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoMTLTestingFormat());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Mason Skill Building Program")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Mason Skill Building Program");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.mason_sbp);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoMasonSkillBuildProgram());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Influencer")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Influencer");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.influencer);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoInfluencer());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Quality Complaint")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Quality Complaint");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.quality_complaint_report);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoQualityComplaint());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("MLE Site Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("MLE Site Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.mle_site_visit_report);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoMLESiteVisit());
                        mMenuList.add(menuObj);
                    }
                } else if (menuname.equalsIgnoreCase("Counter Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Counter Visit");
                    if (dcmaccess) {
                        menuObj.setResourceId(R.drawable.counter_visit);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoCounterVisit());
                        mMenuList.add(menuObj);
                    }
                }
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowSurveyMenuList() {
        final Dialog mMallHighStreetListDialog = new Dialog(ActivitySurveyReportLanding.this, R.style.PauseDialog);
        mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mMallHighStreetListDialog.setContentView(R.layout.select_from_list);
        mMallHighStreetListDialog.setCancelable(false);

        TextView title = mMallHighStreetListDialog.findViewById(R.id.title);
        title.setText("Menu List");
        ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);
        final SurveyReportAdapter adapter = new SurveyReportAdapter(ActivitySurveyReportLanding.this, R.layout.activity_survey_menu_child, mSurveyReportList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            mMallHighStreetListDialog.cancel();
            if (mSurveyReportList.get(arg2).getMenuId().equalsIgnoreCase("RA115")) {
                PrepareSurveyOutletData(SELECTION);
            }
        });

        Button btncancel = mMallHighStreetListDialog.findViewById(R.id.btn_cncl);
        btncancel.setVisibility(View.GONE);
        mMallHighStreetListDialog.show();
    }

    private void DoOnClickJob(String menu, int number) {
        mType = menu;
        Log.d("TAG", "_DOWNLOAD_ DoOnClickJob: " + menu + "  " + number);
        if (menu.equalsIgnoreCase("khoj")) {
            if (number == 0) {
                Toast.makeText(mContext, "No Data Found", Toast.LENGTH_LONG).show();
            } else {
                new TRANS_RouteNameList_AsyncTask(mContext).execute();
            }
        } else if(menu.equalsIgnoreCase("New Site Lead and Conversion Tracking")){
            if (number == 0) {
                Toast.makeText(mContext, "No Data Found", Toast.LENGTH_LONG).show();
            } else {
                Intent intent = new Intent(ActivitySurveyReportLanding.this, NewSiteLeadDetailsActivity.class);
                startActivity(intent);
            }
        } else {
            Log.d("TAG", "DoOnClickJob: "+Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(menu));
            if (Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(menu)) {
                PrepareSurveyOutletData(5);
                Log.d("TAG", "DoOnClickJob: 5");
            } else {
                PrepareSurveyOutletData(SELECTION);
                Log.d("TAG", "DoOnClickJob: "+SELECTION);
            }
        }
    }

    @SuppressLint("SetTextI18n")
    private void ShowSurveyDeatails() {
        final Dialog mMallHighStreetListDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar_Fullscreen);
        mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mMallHighStreetListDialog.setContentView(R.layout.select_from_list);
        mMallHighStreetListDialog.setCancelable(false);

        TextView title = mMallHighStreetListDialog.findViewById(R.id.title);
        title.setText(mTypeDisplayToHeader);
        if (mTypeDisplayToHeader.matches("Branding Verification")) {
            title.setText("OOH & WALL WRAP");
        }
        if (mTypeDisplayToHeader.matches("Counter Branding")) {
            title.setText("Retail branding");
        }
        ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);
        dialogList.setDivider(null);
        KeyValueAdapter adapter = new KeyValueAdapter(mContext, R.layout.keyvalue_child, mSurveyDetailsList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
        });

        Button btncancel = mMallHighStreetListDialog.findViewById(R.id.btn_cncl);
        btncancel.setText("Back to List");
        btncancel.setOnClickListener(v -> mMallHighStreetListDialog.cancel());
        mMallHighStreetListDialog.show();
    }

    @SuppressLint("SetTextI18n")
    private void ShowSurveyDeatailsListSubMenu() {
        final Dialog mMallHighStreetListDialog = new Dialog(ActivitySurveyReportLanding.this, R.style.PauseDialog);
        mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mMallHighStreetListDialog.setContentView(R.layout.select_from_list);
        mMallHighStreetListDialog.setCancelable(false);

        TextView title = mMallHighStreetListDialog.findViewById(R.id.title);

        title.setText("List : " + mType);
        mTypeDisplayToHeader = mType;
        if (mTypeDisplayToHeader.matches("Branding Verification")) {
            title.setText("OOH & WALL WRAP");
        }
        if (mTypeDisplayToHeader.matches("Counter Branding")) {
            title.setText("Retail branding");
        }
        ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);
        final SurveyDisplayMenuAdapter adapter = new SurveyDisplayMenuAdapter(ActivitySurveyReportLanding.this, R.layout.customer_broker_list_child, mSurveyMenuDetailsList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            SurveyMenuDetails obj = adapter.getItem(arg2);
            assert obj != null;
            mSurveySubMenuID = obj.getMenuId();
            mTypeDisplayToHeader = mType + ":" + obj.getMenuName();
            PrepareSurveyOutletData(SELECTION);
        });

        Button btncancel = mMallHighStreetListDialog.findViewById(R.id.btn_cncl);
        btncancel.setText("Back to Menu");
        btncancel.setOnClickListener(v -> {
            mMallHighStreetListDialog.cancel();
            if (mType.equalsIgnoreCase("all survey")) {
                finish();
            }
        });
        mMallHighStreetListDialog.show();
    }

    @SuppressLint("SetTextI18n")
    private void ShowOutletDeatails() {
        final Dialog mMallHighStreetListDialog = new Dialog(ActivitySurveyReportLanding.this, R.style.PauseDialog);
        mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mMallHighStreetListDialog.setContentView(R.layout.select_from_list);
        mMallHighStreetListDialog.setCancelable(false);

        TextView title = mMallHighStreetListDialog.findViewById(R.id.title);

        Log.d("TAG", "aaaaaaaaaaa: "+mType);
        if (mType.equalsIgnoreCase("FS") || mType.equalsIgnoreCase("DCE") || mType.equalsIgnoreCase("DCA")) {
            title.setText("List : Outlet");
        } else if (mType.equalsIgnoreCase("Customer Add")) {
            title.setText("List : Existing Customer");
        } else {
            title.setText("List : " + mType);
            mTypeDisplayToHeader = mType;
            if (mTypeDisplayToHeader.matches("Branding Verification")) {
                title.setText("OOH & WALL WRAP");
            }
            if (mTypeDisplayToHeader.matches("Counter Branding")) {
                title.setText("Retail branding");
            }
        }
        if (Constants.nickName.equalsIgnoreCase("coral")) {
            title.setText("List : Plumber Meet");
        }
        ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);
        final OutletAdapter adapter = new OutletAdapter(ActivitySurveyReportLanding.this, R.layout.customer_broker_list_child, mOutletDetailsList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            OutletDetails obj = adapter.getItem(arg2);
            assert obj != null;
            mSurveyID = obj.getSurveyID();
            if (mType.equalsIgnoreCase("New IHB")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("Existing IHB")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("IHB Site & Complaint Visit")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("New Dealer")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("New Sub Dealer")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("All Survey")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("KYC")) {
                PrepareSurveyOutletData(4);
            }
            else if(mType.equalsIgnoreCase("Dhalai Services")){
                PrepareSurveyOutletData(4);
            }
            else if (mType.equalsIgnoreCase("Technical Meets")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("Branding")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("Counter Branding")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("Corporate Branding") || mType.equalsIgnoreCase("Lead Generation") || mType.equalsIgnoreCase("Influencer") || mType.equalsIgnoreCase("Mason Skill Building Program") || mType.equalsIgnoreCase("MLE Site Visit") || mType.equalsIgnoreCase("MTL Testing Format") || mType.equalsIgnoreCase("Quality Complaint") || mType.equalsIgnoreCase("Counter Visit")) {
                PrepareSurveyOutletData(4);
            } else if (mType.equalsIgnoreCase("Site Visit") || mType.equalsIgnoreCase("Facilitator Add") || mType.equalsIgnoreCase("Customer Add") || mType.equalsIgnoreCase("Branding Verification") || mType.equalsIgnoreCase("farmer visit")) {
                PrepareSurveyOutletData(4);
            } else {
                mMallHighStreetListDialog.cancel();
            }
        });

        Button btncancel = mMallHighStreetListDialog.findViewById(R.id.btn_cncl);
        btncancel.setText("Back to Menu");
        btncancel.setOnClickListener(v -> {
            mMallHighStreetListDialog.cancel();
            if (mType.equalsIgnoreCase("all survey")) {
                finish();
            }
        });
        mMallHighStreetListDialog.show();
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
            mSurveyType = Objects.requireNonNull(adapter.getItem(pos)).toLowerCase();
            mDialogDepotName.cancel();
            mQuery = BuildQuery(SELECTION);
            FetchSurveyDCEData(SELECTION);
        });
        Button btncancel = mDialogDepotName.findViewById(R.id.btn_cncl);
        btncancel.setVisibility(View.GONE);
        mDialogDepotName.show();
    }

    private String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                mTimeStamp = Constants.dateString;
                condition = "substr(LO.trans_id,-14,8)='" + mTimeStamp + "'";
                mQuery2 = "substr(survey_id,-14,8)='" + mTimeStamp + "'";
                break;
            case 2:
                mTimeStamp = Constants.dateString.substring(0, 6);
                condition = "substr(LO.trans_id,-14,6)='" + mTimeStamp + "'";
                mQuery2 = "substr(survey_id,-14,6)='" + mTimeStamp + "'";
                break;
            case 3:
                condition = "substr(LO.trans_id,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'";
                mQuery2 = "substr(survey_id,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'";
                break;
        }
        return condition;
    }

    public void PrepareSurveyMenuData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Please wait..");
        mPrepareSurveyMenuProgressDialog.setCancelable(false);
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        Log.d("TAG", "_DOWNLOAD_ 111");
                        mTimeStamp = Constants.dateString;
                        mSurveyReportSumary = mAceDnsTransactionDatabase.getSurveyReportSummery(mTimeStamp);
                        break;
                    case 2:
                        Log.d("TAG", "_DOWNLOAD_ 222");
                        mTimeStamp = Constants.dateString.substring(0, 6);
                        mSurveyReportSumary = mAceDnsTransactionDatabase.getSurveyReportSummery(mTimeStamp);
                        break;
                    case 3:
                        Log.d("TAG", "_DOWNLOAD_ 333");
                        mSurveyReportSumary = mAceDnsTransactionDatabase.getSurveyReportSummery(mStartDate, mEndDate);
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

    public void PrepareSurveyOutletData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Please wait..");
        mPrepareSurveyMenuProgressDialog.setCancelable(false);
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        Log.d("TAG", "mOutletDetailsList: 10");
                        mTimeStamp = Constants.dateString;
                        if (Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(mType)) {
                            Log.d("TAG", "mOutletDetailsList: 10a "+mType);
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetailsWithSubMenu(mType, mTimeStamp, mSurveySubMenuID);
                        } else {
                            Log.d("TAG", "mOutletDetailsList: 10b "+mType);
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetails(mType, mTimeStamp);
                        }
                        break;
                    case 2:
                        Log.d("TAG", "mOutletDetailsList: 11");
                        mTimeStamp = Constants.dateString.substring(0, 6);
                        if (Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(mType)) {
                            Log.d("TAG", "mOutletDetailsList: 11a "+mType);
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetailsWithSubMenu(mType, mTimeStamp, mSurveySubMenuID);
                        } else {
                            Log.d("TAG", "mOutletDetailsList: 11b "+mType);
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetails(mType, mTimeStamp);
                        }
                        break;
                    case 3:
                        Log.d("TAG", "mOutletDetailsList: 12");
                        if (Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(mType)) {
                            Log.d("TAG", "mOutletDetailsList: 12a "+mType);
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetailsWithSubMenu(mType, mStartDate, mEndDate, mSurveySubMenuID);
                        } else {
                            Log.d("TAG", "mOutletDetailsList: 12b "+mType);
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetails(mType, mStartDate, mEndDate);
                        }
                        break;
                    case 4:
                        Log.d("TAG", "aaaaaaaaaaa: "+mSurveyID);
                        mSurveyDetailsList = mAceDnsDatabase.GetSurveyDetails(mSurveyID);
                        break;
                    case 5:
                        mTimeStamp = Constants.dateString;
                        if (Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains("Technical Meets")) {
                            mAceDnsDatabase.GetMenuNameList("Technical Meets");
                        } else {
                            mAceDnsDatabase.GetMenuName();
                        }
                        break;
                }
                Message msg = mPrepareSurveyOutletHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyOutletHandler.sendMessage(msg);
            }
        }.start();
    }

    public void FetchSurveyDCEData(final int whattodo) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Please wait..");
        mPrepareSurveyMenuProgressDialog.setCancelable(false);
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1, 2, 3:
                        mSurveyReportList = mAceDnsDatabase.GetSurveyReportMenuDetails(mQuery, mSurveyType);
                        if (!mSurveyReportList.isEmpty()) {
                            for (int count = 0; count < mSurveyReportList.size(); count++) {
                                String id = mSurveyReportList.get(count).getMenuId();
                                String total = mAceDnsDatabase.GetMenuWiseTotalSurvey(mQuery2, id);
                                mSurveyReportList.get(count).setTotal(total);
                            }
                        }
                        break;
                    case 4:
                        if (Constants.surveyFormDetailsObj.getSurveyTypeDetails().contains(",")) {
                            mTypeList = Constants.surveyFormDetailsObj.getSurveyTypeDetails().split(",");
                        } else {
                            mTypeList = new String[1];
                            mTypeList[0] = Constants.surveyFormDetailsObj.getSurveyTypeDetails();
                        }
                        break;
                }
                Message msg = mDCEReportHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("JOBDONE", whattodo);
                msg.setData(b);
                mDCEReportHandler.sendMessage(msg);
            }
        }.start();
    }

    // Khoj popup and other function
    ArrayList<DataObjSet> dataSet = new ArrayList<>();

    @SuppressLint("StaticFieldLeak")
    public class TRANS_RouteNameList_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_RouteNameList_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Loading Khoj List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.khojDetails + "?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    Log.d("TAG", "_DOWNLOAD_ RouteNameList: " + url);
                    POST_result = HttpCalling.httpGetCallWithTextResponse(url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    JSONArray arr = obj.getJSONArray("sites");
                    dataSet.clear();
                    for (int i = 0; i < arr.length(); i++) {
                        DataObjSet temp = new DataObjSet();
                        temp.setName(arr.getJSONObject(i).getJSONObject("visit_list").getString("district") + " " + arr.getJSONObject(i).getJSONObject("visit_list").getString("updated_at"));
                        temp.setObj(arr.getJSONObject(i).getJSONObject("visit_list"));
                        dataSet.add(temp);
                    }
                    progressDialogClose();
                    showDataSetPopupDialog(dataSet, "Select project khoj");
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Route name list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Route name list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    public void showDataSetPopupDialog(ArrayList<DataObjSet> dataSet, String titleValue) {
        try {
            final DataObjSetAdapter adapter = new DataObjSetAdapter(mContext, R.layout.list_item_single_radio, dataSet);

            // Dialog create
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);

            // Dialog title set
            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);

            // Search layout
            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            searchLayout.setVisibility(View.GONE);

            // Back Button
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            // Filter Data [EditText]
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            //List data show
            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                Intent intent = new Intent(mContext, NewSiteLeadDetailsActivity.class);
//                Intent intent = new Intent(mContext, KhojDetailsActivity.class);
                intent.putExtra("SURVEYSUBMENUDETAILS", surveymenudetails);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                intent.putExtra("FSTARTDATE", mFStartDate);
                intent.putExtra("FENDDATE", mFEndDate);
                if (dataSet != null && position >= 0 && position < dataSet.size()) {
                    intent.putExtra("DataSet", new Gson().toJson(dataSet.get(position)));
                } else {
                    Toast.makeText(mContext, "Invalid selection", Toast.LENGTH_SHORT).show();
                }
                startActivity(intent);
            });
            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            mDialogCustomer.show();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ showDataSetPopupDialog error: " + e.getMessage());
        }
    }
}
