package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;

import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitRetailerStockOutTask;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.util.HttpCalling;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.OrderMenuAdapter;
import com.forcepower.acedns.bean.MenuObj;
import com.forcepower.acedns.bean.ReportSummery;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.masterApiCallingFlag;
import static com.forcepower.acedns.constants.Constants.retailerappTransactionReason;

import org.json.JSONObject;

public class ActivityReportLanding extends FragmentActivity implements OnClickListener {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewShowDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHideDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonEndDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmitDate = null;
    @SuppressLint("StaticFieldLeak")
    public static RelativeLayout mRelativeLayoutDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mCustomDateLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewEndDate = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutToday = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutMTD = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutCustom = null;
    @SuppressLint("StaticFieldLeak")
    public static GridView mGridViewMenu = null;

    public static int SELECTION = 0;
    public static String mStartDate = "";
    public static String mEndDate = "";
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    OrderMenuAdapter mMenuAdapter;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    Context mContext;
    ArrayList<MenuObj> mMenuList;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ReportSummery mReportSummeryObj;
    private boolean isStartDate = false;
    private boolean isStartDateTaken = false;
    private boolean isEndDateTaken = false;
    private String mFStartDate = "";
    private String mFEndDate = "";
    private String mTimeStamp = "";
    ProgressDialog progressDialog;

    @SuppressLint({"SimpleDateFormat", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report_landing);
        RegisterActivities.registerActivity(this);

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyyMMdd");

        mContext = ActivityReportLanding.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        InitializeView();

        Utils.headerFooterIconChangesForRetailerApp(mContext, false);
        mReportHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialog.cancel();
                ActivityReportLanding.this.runOnUiThread(() -> {
                    PrepareMenuList();
                    if (mMenuList != null) {
                        mMenuAdapter = new OrderMenuAdapter(ActivityReportLanding.this, R.layout.grid_child_value, mMenuList);
                        mGridViewMenu.setAdapter(mMenuAdapter);
                        mMenuAdapter.notifyDataSetChanged();
                    } else {
                        Utils.showToast(mContext, "No report menu found. Please Synchronize Data");
                    }
                });
            }
        };

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    isStartDateTaken = true;
                    startDate = date;
                    mTextViewStartDate.setText(mDFormatFrontEnd.format(date));
                    mFStartDate = mDFormatFrontEnd.format(date);
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    isEndDateTaken = true;
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    mFEndDate = mDFormatFrontEnd.format(date);
                    mEndDate = mDFormatBackEnd.format(date);
                }

                if (isStartDateTaken && isEndDateTaken) {
                    if (endDate.after(currentDate)) {
                        Utils.showToast(mContext, "Future dates cannot be selected");
                    } else if (startDate.after(endDate)) {
                        Utils.showToast(mContext, "Start Date should be less than or equal to End Date");
                    } else {
                        dialogCaldroidFragment.dismiss();
                    }
                } else {
                    dialogCaldroidFragment.dismiss();
                }
            }
        };

        mButtonBack.setOnClickListener(v -> finish());

        mGridViewMenu.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            String menu = mMenuList.get(arg2).getFeatureName();
            int number = 0;
            if (!mMenuList.get(arg2).getCount().trim().isEmpty()) {
                number = Integer.parseInt(mMenuList.get(arg2).getCount());
            }
            DoOnClickJob(menu, number);
        });
        SELECTION = 1;
        FetchSaudaTransactionLogData(1);
    }

    private void DoOnClickJob(String menu, int number) {
        if (menu.equalsIgnoreCase("ORDER_REPORT")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityOrderReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("ORDER_APPROVAL_REPORT")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityOrderApprovalReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("BEAT_WISE_TA_DA")) {
            Intent intent = new Intent(ActivityReportLanding.this, BeatWiseTADAReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("vanSales")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivityVanSalesReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("callcentre")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivityCallCentreReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("sis_emp_data")) {
            Intent intent = new Intent(ActivityReportLanding.this, SisReportActivity.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("sis_summary_data")) {
            Intent intent = new Intent(ActivityReportLanding.this, SisSummeryActivity.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("beat_wise_activity")) {
            Intent intent = new Intent(ActivityReportLanding.this, BeatWiseActivity.class);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("tour_exp")) {
            Intent intent = new Intent(ActivityReportLanding.this, TourExpReportActivity.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("odometer")) {
            Intent intent = new Intent(ActivityReportLanding.this, OdometerReportActivity.class);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("stockist_visit")) {
            Intent intent = new Intent(ActivityReportLanding.this, StockistVisitReportActivity.class);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("STOCK_REPORT")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityStockAuditReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("FeedBack")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityMFSReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("COLLECTION_REPORT")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityCollectionReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("yellowCard_report")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityYellowCardReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("check_in_out")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityCheckInOutReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("sales_report")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivitySalesReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("ledger_report")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivityLedgerReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("bargain")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivityBargainReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("do")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivityDOReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("outstanding_report")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivityOutstandingReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("CASH_BALANCE")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivityCashBalanceReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("CASH_DEPOSIT")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityCashDepositReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("CASH_TRANSACTION")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityYellowCardReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("BUSINESS_PROSPECT_REPORT")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityBusinessProspectReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("SAUDA_REPORT")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, SauadaReportActivity.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("SURVEY_REPORT")) {
            if (number > 0) {
                Log.d("TAG", "_DOWNLOAD_ SURVEY_REPORT: " + Constants.surveyFormDetailsObj);
                Intent intent = new Intent(ActivityReportLanding.this, ActivitySurveyReportLanding.class);
                if (Constants.surveyFormDetailsObj != null) {
                    intent.putExtra("SURVEYSUBMENUDETAILS", Constants.surveyFormDetailsObj.getSurveySubMenuDetails());
                } else {
                    mAceDnsDatabase.GETSurveyFormDetails();
                    intent.putExtra("SURVEYSUBMENUDETAILS", Constants.surveyFormDetailsObj.getSurveySubMenuDetails());
                }
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                intent.putExtra("FSTARTDATE", mFStartDate);
                intent.putExtra("FENDDATE", mFEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_SHORT).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_SHORT).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("NO_ACTIVITY_REPORT")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityNoActivityReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
        if (menu.equalsIgnoreCase("STOCK_BALANCE_REPORT")) {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Utils.showProgressDialog(mContext, "Updating Stock Balance Data..");
                new Thread() {
                    public void run() {
                        masterApiCallingFlag = false;
                        new commonAsyncTaskMaster(mContext, "stock_balance_details");
                    }
                }.start();
            } else {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityStockBalanceReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            }
        }
        if (menu.equalsIgnoreCase("STOCK_REPORT_RETAILER")) {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                retailerappTransactionReason = "sendDataBeforeReport";
                Constants.retailerappReportType = "stockreport";
                new TRANS_SubmitRetailerStockOutTask(mContext, true).execute();
            } else {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityStockReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            }
        }
        if (menu.equalsIgnoreCase("ACTIVATION_REPORT")) {
            Intent intent = new Intent(ActivityReportLanding.this, ActivityActivationReport.class);
            intent.putExtra("SELECTION", SELECTION);
            intent.putExtra("STARTDATE", mStartDate);
            intent.putExtra("ENDDATE", mEndDate);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("ATTENDANCE_REPORT")) {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                Utils.showProgressDialog(mContext, "Updating Data..Please Wait..");
                new Thread() {
                    public void run() {
                        masterApiCallingFlag = false;
                        new commonAsyncTaskMaster(mContext, "attendance_checkout_details");
                    }
                }.start();
            } else {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityAttendanceReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                startActivity(intent);
            }
        }
        if (menu.equalsIgnoreCase("WHOLESALE_INFO")) {
            if (number > 0) {
                Intent intent = new Intent(ActivityReportLanding.this, ActivityWholeSaleReport.class);
                intent.putExtra("SELECTION", SELECTION);
                intent.putExtra("STARTDATE", mStartDate);
                intent.putExtra("ENDDATE", mEndDate);
                intent.putExtra("FSTARTDATE", mFStartDate);
                intent.putExtra("FENDDATE", mFEndDate);
                startActivity(intent);
            } else {
                switch (SELECTION) {
                    case 1:
                        Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                        break;
                    case 2:
                        Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                        break;
                    case 3:
                        Toast.makeText(mContext, "No record found from " + mStartDate + " to " + mEndDate, Toast.LENGTH_LONG).show();
                        break;
                }
            }
        }
//        if (menu.equalsIgnoreCase("DCR_REPORT")) {}
    }

    @SuppressLint("SetTextI18n")
    private void InitializeView() {
        mImageViewShowDuration = findViewById(R.id.image_clk);
        mImageViewHideDuration = findViewById(R.id.image_go);
        mRelativeLayoutDuration = findViewById(R.id.duration_layout);
        mCustomDateLayout = findViewById(R.id.custom_date_layout);
        mButtonBack = findViewById(R.id.back);
        mGridViewMenu = findViewById(R.id.grid_menu);
        mFrameLayoutToday = findViewById(R.id.btn_today);
        mFrameLayoutMTD = findViewById(R.id.btn_mtd);
        mFrameLayoutCustom = findViewById(R.id.btn_custom);
        mTextViewStartDate = findViewById(R.id.txt_start_date);
        mTextViewEndDate = findViewById(R.id.txt_end_date);
        mButtonStartDate = findViewById(R.id.btn_start_date);
        mButtonEndDate = findViewById(R.id.btn_end_date);
        mButtonSubmitDate = findViewById(R.id.btn_date_done);
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);
        mFrameLayoutToday.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);
        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);

        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivityReportLanding.this) + "~" + Utils.getDBVersion(ActivityReportLanding.this));
    }

    private void PrepareMenuList() {
        mMenuList = new ArrayList<>();
        boolean order = mAceDnsDatabase.MenuAccess("order");
        boolean collection = mAceDnsDatabase.MenuAccess("collection");
        boolean business_prospect = mAceDnsDatabase.MenuAccess("business_prospect");
        boolean saudaaccess = mAceDnsDatabase.MenuAccess("sauda");
        boolean surveyaccess = mAceDnsDatabase.MenuAccess("survey");
        boolean wholesalemenu = mAceDnsDatabase.MenuAccess("wholesaler_info");
        boolean feedback = mAceDnsDatabase.MenuAccess("market_feedback");
        boolean yellowCard = mAceDnsDatabase.MenuAccess("yellow_card");

        if ((Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes") && order)
                || (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("retailer_care"))
                || (Constants.menuDetailsObj.getvan_sales().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("van_sales"))) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("ORDER_REPORT");
            menuObj.setResourceId(R.drawable.order2);
            menuObj.setCount(mReportSummeryObj.getNoOrdrRcvd());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getapp_order_approval().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("app_order_approval")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("ORDER_APPROVAL_REPORT");
            menuObj.setResourceId(R.drawable.app_order_approval);
            menuObj.setCount(mReportSummeryObj.getorderApproval());
            mMenuList.add(menuObj);
        }
        if (mAceDnsDatabase.isdataPresentInBeatWiseTADATable()) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("BEAT_WISE_TA_DA");
            menuObj.setResourceId(R.drawable.tour_exp);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getvan_sales().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("van_sales")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("vanSales");
            menuObj.setResourceId(R.drawable.vansales);
            mMenuList.add(menuObj);
        }
        if (Constants.nickName.equalsIgnoreCase("magik")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("callcentre");
            menuObj.setResourceId(R.drawable.callcentre);
            mMenuList.add(menuObj);
        }
        if ((Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("stk_audit") || (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("retailer_care")))) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("STOCK_REPORT");
            menuObj.setResourceId(R.drawable.stk_audit);
            menuObj.setCount(mReportSummeryObj.getNoofStockAudit());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes") && collection) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("COLLECTION_REPORT");
            menuObj.setResourceId(R.drawable.collection2);
            menuObj.setCount(mReportSummeryObj.getNoCollcRcvd());
            mMenuList.add(menuObj);
        }
        if ((Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")) && business_prospect) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("BUSINESS_PROSPECT_REPORT");
            menuObj.setResourceId(R.drawable.business2);
            menuObj.setCount(mReportSummeryObj.getNoNewCustVisitd());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") && saudaaccess) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("SAUDA_REPORT");
            menuObj.setResourceId(R.drawable.sauda2);
            menuObj.setCount(mReportSummeryObj.getSaudaBooking());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getSurvey().equalsIgnoreCase("yes") && surveyaccess) {
            new TRANS_count_AsyncTask(mContext, mReportSummeryObj.getNoofsurvey()).execute();
        }
        if (Constants.menuDetailsObj.getWholeSaleInfo().equalsIgnoreCase("yes") && wholesalemenu) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("WHOLESALE_INFO");
            menuObj.setResourceId(R.drawable.wholesale);
            menuObj.setCount(mReportSummeryObj.getNoofwholesale());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getMarketFeedback().equalsIgnoreCase("yes") && feedback) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("FeedBack");
            menuObj.setResourceId(R.drawable.feedback);
            menuObj.setCount(mReportSummeryObj.getNoofMFS());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getYellowCard().equalsIgnoreCase("yes") && yellowCard) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("yellowCard_report");
            menuObj.setResourceId(R.drawable.yellow_card_report);
            menuObj.setCount(mReportSummeryObj.getnoofYellowCardDetails());
            mMenuList.add(menuObj);
        }
        if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("sales_report");
            menuObj.setResourceId(R.drawable.stock_report);
            mMenuList.add(menuObj);

            MenuObj menuObj2 = new MenuObj();
            menuObj2.setFeatureName("outstanding_report");
            menuObj2.setResourceId(R.drawable.outstanding_report);
            mMenuList.add(menuObj2);

            MenuObj menuObj3 = new MenuObj();
            menuObj3.setFeatureName("ledger_report");
            menuObj3.setResourceId(R.drawable.ledger_report);
            mMenuList.add(menuObj3);

        }
        if (Constants.menuDetailsObj.getbargain().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("bargain")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("bargain");
            menuObj.setResourceId(R.drawable.bargain_report);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getDO().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("do")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("do");
            menuObj.setResourceId(R.drawable.deliveryorder_report);
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getCheckInOut().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("check_in_out")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("check_in_out");
            menuObj.setResourceId(R.drawable.checkinout_report);
            menuObj.setCount(mReportSummeryObj.getnoofCheckInOut());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("seperated") && mAceDnsDatabase.MenuAccess("tour_expense")) {
            MenuObj menuObj3 = new MenuObj();
            menuObj3.setFeatureName("CASH_BALANCE");
            menuObj3.setResourceId(R.drawable.cashbalance);
            mMenuList.add(menuObj3);
        }
        if (!Constants.menuDetailsObj.getretailer_app().equalsIgnoreCase("yes")) {
            if (!Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("logistics")) {
                if (isOrderOn() || isStockAuditOn() || isCollectionOn() || isRetailerCareOn()) {
                    if (!Constants.menuDetailsObj.getDoctor_visit().toLowerCase().matches("yes")) {
                        MenuObj menuObj = new MenuObj();
                        menuObj.setFeatureName("NO_ACTIVITY_REPORT");
                        menuObj.setResourceId(R.drawable.noactivity);
                        menuObj.setCount(mReportSummeryObj.getNoNoAct());
                        mMenuList.add(menuObj);
                    }
                }
            }
        } else {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("STOCK_REPORT_RETAILER");
            menuObj.setResourceId(R.drawable.stock_report_icon);
            mMenuList.add(menuObj);

            menuObj = new MenuObj();
            menuObj.setFeatureName("ATTENDANCE_REPORT");
            menuObj.setResourceId(R.drawable.attendance_report);
            mMenuList.add(menuObj);

            menuObj = new MenuObj();
            menuObj.setFeatureName("ACTIVATION_REPORT");
            menuObj.setResourceId(R.drawable.activation_report);
            mMenuList.add(menuObj);
        }

        if (Constants.menuDetailsObj.getBeat_wise_activity().equalsIgnoreCase("yes")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("beat_wise_activity");
            menuObj.setResourceId(R.drawable.cgroup);
            menuObj.setCount(mReportSummeryObj.getorderApproval());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getTourExp().equalsIgnoreCase("yes")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("tour_exp");
            menuObj.setResourceId(R.drawable.tour_exp);
            menuObj.setCount(mReportSummeryObj.getTour_expense());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getOdometer().equalsIgnoreCase("yes")) {
            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("odometer");
            menuObj.setResourceId(R.drawable.odometer);
            menuObj.setCount(mReportSummeryObj.getorderApproval());
            mMenuList.add(menuObj);
        }
        if (Constants.menuDetailsObj.getStockist_visit().toLowerCase().matches("yes")) {
            MenuObj menuObj3 = new MenuObj();
            menuObj3.setFeatureName("stockist_visit");
            menuObj3.setResourceId(R.drawable.stockist);
            mMenuList.add(menuObj3);
        }
    }

    private void progressDialogOpen() {
        runOnUiThread(() -> {
            progressDialog = new ProgressDialog(mContext);
            progressDialog.setMessage("Loading please wait...");
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
    public class TRANS_count_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String conte1;

        public TRANS_count_AsyncTask(Context context, String t) {
            this.mContext = context;
            this.conte1 = t;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen();
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
//                MenuObj menuObj = new MenuObj();
//                menuObj.setFeatureName("SURVEY_REPORT");
//                if (Constants.surveyFormDetailsObj.getsurvey_menu_name().equalsIgnoreCase("opportunity")) {
//                    menuObj.setResourceId(R.drawable.opp);
//                } else {
//                    if (Constants.nickName.equalsIgnoreCase("nimbus")) {
//                        menuObj.setResourceId(R.drawable.installationexp);
//                    } else if (Constants.nickName.equalsIgnoreCase("coral")) {
//                        menuObj.setResourceId(R.drawable.plumber);
//                    } else {
//                        menuObj.setResourceId(R.drawable.marketoverview);
//                    }
//                }
//                menuObj.setResourceId(R.drawable.marketoverview);
//                menuObj.setCount(String.valueOf(Integer.parseInt(result) + Integer.parseInt(conte1)));
//                mMenuList.add(menuObj);
//                mMenuAdapter.notifyDataSetChanged();
                progressDialogClose();
                new TRANS_newSiteCount_AsyncTask(mContext, String.valueOf(Integer.parseInt(result) + Integer.parseInt(conte1))).execute();
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_newSiteCount_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String conte1;

        public TRANS_newSiteCount_AsyncTask(Context context, String t) {
            this.mContext = context;
            this.conte1 = t;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen();
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
                MenuObj menuObj = new MenuObj();
                menuObj.setFeatureName("SURVEY_REPORT");
//                if (Constants.surveyFormDetailsObj.getsurvey_menu_name().equalsIgnoreCase("opportunity")) {
//                    menuObj.setResourceId(R.drawable.opp);
//                } else {
//                    if (Constants.nickName.equalsIgnoreCase("nimbus")) {
//                        menuObj.setResourceId(R.drawable.installationexp);
//                    } else if (Constants.nickName.equalsIgnoreCase("coral")) {
//                        menuObj.setResourceId(R.drawable.plumber);
//                    } else {
//                        menuObj.setResourceId(R.drawable.marketoverview);
//                    }
//                }
                menuObj.setResourceId(R.drawable.marketoverview);
                menuObj.setCount(String.valueOf(Integer.parseInt(result) + Integer.parseInt(conte1)));
                mMenuList.add(menuObj);
                mMenuAdapter.notifyDataSetChanged();
                progressDialogClose();
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    public boolean isRetailerCareOn() {
        return Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes");
    }

    public boolean isCollectionOn() {
        return Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("collection");
    }

    public boolean isStockAuditOn() {
        return Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("stk_audit");
    }

    public boolean isOrderOn() {
        return Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("order");
    }

    public void ChangeBackground(int select) {
        mFrameLayoutToday.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mFrameLayoutMTD.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mFrameLayoutCustom.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mCustomDateLayout.startAnimation(bottomUp);
        mCustomDateLayout.setVisibility(View.GONE);

        switch (select) {
            case 1:
                mFrameLayoutToday.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
            case 2:
                mFrameLayoutMTD.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
            case 3:
                mFrameLayoutCustom.setBackgroundColor(Color.parseColor("#B6B6B4"));
                mCustomDateLayout.startAnimation(bottomUp);
                mCustomDateLayout.setVisibility(View.VISIBLE);
                break;
        }
    }

    @Override
    public void onClick(View v) {
        if (v == mImageViewHideDuration) {
            mImageViewShowDuration.startAnimation(fadeIn);
            mImageViewShowDuration.setVisibility(View.VISIBLE);
            mRelativeLayoutDuration.startAnimation(bottomDown);
            mRelativeLayoutDuration.setVisibility(View.GONE);
        } else if (v == mImageViewShowDuration) {
            mImageViewShowDuration.startAnimation(fadeOut);
            mImageViewShowDuration.setVisibility(View.GONE);
            mRelativeLayoutDuration.startAnimation(bottomUp);
            mRelativeLayoutDuration.setVisibility(View.VISIBLE);
        } else if (v == mFrameLayoutToday) {
            SELECTION = 1;
            isStartDateTaken = false;
            isEndDateTaken = false;
            ChangeBackground(SELECTION);
            FetchSaudaTransactionLogData(SELECTION);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            isStartDateTaken = false;
            isEndDateTaken = false;
            ChangeBackground(SELECTION);
            FetchSaudaTransactionLogData(SELECTION);
        } else if (v == mFrameLayoutCustom) {
            SELECTION = 3;
            ChangeBackground(SELECTION);
        } else if (v == mButtonStartDate) {
            isStartDate = true;
            ChooseDateDialog();
        } else if (v == mButtonEndDate) {
            isStartDate = false;
            ChooseDateDialog();
        } else if (v == mButtonSubmitDate) {
            try {
                if (endDate.after(currentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else if (startDate.after(endDate)) {
                    Utils.showToast(mContext, "Start Date should be less than or equal to End Date");
                } else {
                    mCustomDateLayout.startAnimation(bottomUp);
                    mCustomDateLayout.setVisibility(View.GONE);
                    mImageViewShowDuration.startAnimation(fadeOut);
                    mImageViewShowDuration.setVisibility(View.GONE);
                    mRelativeLayoutDuration.startAnimation(bottomUp);
                    mRelativeLayoutDuration.setVisibility(View.VISIBLE);
                    SELECTION = 3;
                    mTextViewStartDate.setText("");
                    mTextViewEndDate.setText("");
                    isStartDateTaken = false;
                    isEndDateTaken = false;
                    FetchSaudaTransactionLogData(SELECTION);
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Please choose the Dates again.");
            }
        }
    }

    public void ChooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        Bundle bundle = new Bundle();
        bundle.putString(CaldroidFragment.DIALOG_TITLE, "Select a date");
        dialogCaldroidFragment.setArguments(bundle);
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    public void FetchSaudaTransactionLogData(final int whattodo) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();

        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        mTimeStamp = Constants.dateString;
                        mReportSummeryObj = mAceDnsTransactionDatabase.getReportSummery(mTimeStamp);
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoCustVisitd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoOrdrRcvd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoCollcRcvd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoNoAct());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoNewCustVisitd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getProdctvty());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getSaudaBooking());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofsurvey());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofwholesale());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofStockAudit());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getnoofYellowCardDetails());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofMFS());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getnoofCheckInOut());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getorderApproval());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getTour_expense());
                        break;
                    case 2:
                        mTimeStamp = Constants.dateString.substring(0, 6);
                        mReportSummeryObj = mAceDnsTransactionDatabase.getReportSummery(mTimeStamp);
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoCustVisitd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoOrdrRcvd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoCollcRcvd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoNoAct());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoNewCustVisitd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getProdctvty());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getSaudaBooking());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofsurvey());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofwholesale());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofStockAudit());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getnoofYellowCardDetails());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofMFS());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getnoofCheckInOut());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getorderApproval());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getTour_expense());
                        break;
                    case 3:
                        mReportSummeryObj = mAceDnsTransactionDatabase.getCustomReportSummery(mStartDate, mEndDate);
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoCustVisitd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoOrdrRcvd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoCollcRcvd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoNoAct());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoNewCustVisitd());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getProdctvty());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getSaudaBooking());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofsurvey());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofwholesale());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofStockAudit());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getnoofYellowCardDetails());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getNoofMFS());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getnoofCheckInOut());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getorderApproval());
                        Log.d("TAG", "ActivityReportLanding1 run: "+mReportSummeryObj.getTour_expense());
                        break;
                }
                Message msg = mReportHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("JOBDONE", whattodo);
                msg.setData(b);
                mReportHandler.sendMessage(msg);
            }
        }.start();
    }
}
