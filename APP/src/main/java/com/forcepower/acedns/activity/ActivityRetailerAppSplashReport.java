package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Html;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioGroup;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.EmployeeAdapter;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.splashScreenDetailsList;

import androidx.annotation.NonNull;

public class ActivityRetailerAppSplashReport extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    private static TextView zeroOutletDetails = null;
    @SuppressLint("StaticFieldLeak")
    private static TextView textViewSaleDetails = null;
    @SuppressLint("StaticFieldLeak")
    private static TextView textViewStockDetails = null;
    @SuppressLint("StaticFieldLeak")
    private static TextView textViewAttendanceDetails = null;
    @SuppressLint("StaticFieldLeak")
    private static LinearLayout attendanceLayout = null;
    @SuppressLint("StaticFieldLeak")
    private static LinearLayout saleLayout = null;
    @SuppressLint("StaticFieldLeak")
    private static LinearLayout zeroOutletLayout = null;
    @SuppressLint("StaticFieldLeak")
    private static LinearLayout stockLayout = null;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonSelectEmployee = null;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonBack = null;

    SimpleDateFormat dateFormat;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterDetailsList;
    private Context mContext;
    private ProgressDialog mProgressDialog;
    private String zeroOutlet = "";

    @SuppressLint({"SimpleDateFormat", "HandlerLeak", "SetTextI18n"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.actvity_retailer_app_splash_report);
        RegisterActivities.registerActivity(this);

        dateFormat = new SimpleDateFormat("dd-MM-yyyy");

        mContext = ActivityRetailerAppSplashReport.this;
        AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(mContext);

        InitializeView();
        String todaysAttendance = splashScreenDetailsList.get(0).getattendanceToday();
        String closingStock = splashScreenDetailsList.get(0).getclosingStock();
        String monthlySale = splashScreenDetailsList.get(0).gettotalSaleMtd();
        String forTodaySale = splashScreenDetailsList.get(0).gettotalSaleFtd();
        zeroOutlet = splashScreenDetailsList.get(0).getzeroOutlet();
        new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                if (job == 2) {
                    if (!mEmployeeMasterDetailsList.isEmpty()) {
                        if (mEmployeeMasterDetailsList.size() != 1) {
                            ShowEmployeeDialog();
                        }
                    } else {
                        Utils.showToast(mContext, "No employee report to you");
                    }
                }
            }
        };

        mButtonBack.setOnClickListener(v -> finish());
        textViewAttendanceDetails.setText(todaysAttendance + "/" + mAceDnsDatabase.EmpCount());
        attendanceLayout.setOnClickListener(v -> {
            ActivityReportLanding.SELECTION = 2;
            Intent intent = new Intent(mContext, ActivityAttendanceReport.class);
            intent.putExtra("SELECTION", ActivityReportLanding.SELECTION);
            intent.putExtra("STARTDATE", ActivityReportLanding.mStartDate);
            intent.putExtra("ENDDATE", ActivityReportLanding.mEndDate);
            startActivity(intent);
        });

        textViewStockDetails.setText(closingStock);

        textViewSaleDetails.setText(Html.fromHtml("FTD  - " + forTodaySale + "<br> MTD- " + monthlySale));
        saleLayout.setOnClickListener(v -> {
            ActivityReportLanding.SELECTION = 2;
            Intent intent = new Intent(mContext, ActivityStockReport.class);
            intent.putExtra("SELECTION", ActivityReportLanding.SELECTION);
            intent.putExtra("STARTDATE", ActivityReportLanding.mStartDate);
            intent.putExtra("ENDDATE", ActivityReportLanding.mEndDate);
            startActivity(intent);
        });
        stockLayout.setOnClickListener(v -> {
            ActivityReportLanding.SELECTION = 2;
            Intent intent = new Intent(mContext, ActivityStockReport.class);
            intent.putExtra("SELECTION", ActivityReportLanding.SELECTION);
            intent.putExtra("STARTDATE", ActivityReportLanding.mStartDate);
            intent.putExtra("ENDDATE", ActivityReportLanding.mEndDate);
            startActivity(intent);
        });

        zeroOutletDetails.setText(zeroOutlet);
        zeroOutletLayout.setOnClickListener(v -> {
            if (Utils.isNumeric(zeroOutlet) && Double.parseDouble(zeroOutlet) > 0) {
                Utils.showProgressDialog(mContext, "Updating Data Please Wait..");
                new Thread() {
                    public void run() {
                        new commonAsyncTaskMaster(mContext, "zero_outlet");
                    }
                }.start();
            }

        });
        Utils.headerFooterIconChangesForRetailerApp(mContext, false);
    }

    @Override
    public void onResume() {
        super.onResume();
    }

    private void InitializeView() {
        textViewStockDetails = findViewById(R.id.textViewStockDetails);
        textViewSaleDetails = findViewById(R.id.textViewSaleDetails);
        zeroOutletDetails = findViewById(R.id.zeroOutletDetails);
        textViewAttendanceDetails = findViewById(R.id.textViewAttendanceDetails);
        attendanceLayout = findViewById(R.id.attendanceLayout);
        saleLayout = findViewById(R.id.saleLayout);
        zeroOutletLayout = findViewById(R.id.zeroOutletLayout);
        stockLayout = findViewById(R.id.stockLayout);
        mButtonBack = findViewById(R.id.back);
        mButtonSelectEmployee = findViewById(R.id.btnSelectEmployee);
    }

    @SuppressLint("SetTextI18n")
    public void ShowConditionofATA() {
        final Dialog dialgoCondition = new Dialog(ActivityRetailerAppSplashReport.this, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_mis);
        TextView txtMsg = dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select an option.");
        final RadioGroup radioSelectionGroup = dialgoCondition.findViewById(R.id.radioSelect);

        radioSelectionGroup.setOnCheckedChangeListener((group, checkedId) -> {
            dialgoCondition.cancel();
        });
        dialgoCondition.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowEmployeeDialog() {
        EmployeeAdapter adapter = new EmployeeAdapter(ActivityRetailerAppSplashReport.this, R.layout.customer_broker_list_child, mEmployeeMasterDetailsList);
        final Dialog dialogEmployeeList = new Dialog(ActivityRetailerAppSplashReport.this, R.style.PauseDialog);
        dialogEmployeeList.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogEmployeeList.setContentView(R.layout.select_from_list);
        dialogEmployeeList.setCancelable(false);
        TextView title = dialogEmployeeList.findViewById(R.id.title);
        title.setText("Please select an employee");

        ListView list = dialogEmployeeList.findViewById(R.id.list);
        list.setAdapter(adapter);
        list.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            EmployeeMasterDetails obj = mEmployeeMasterDetailsList.get(pos);
            mButtonSelectEmployee.setText(obj.getEmpName());
            ShowConditionofATA();
            dialogEmployeeList.cancel();
        });

        Button cancel = dialogEmployeeList.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        dialogEmployeeList.show();

    }
}
