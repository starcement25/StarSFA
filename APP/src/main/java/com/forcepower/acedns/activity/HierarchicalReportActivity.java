package com.forcepower.acedns.activity;

import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;

import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioGroup;

import android.widget.TextView;


import androidx.appcompat.app.AppCompatActivity;

import androidx.recyclerview.widget.RecyclerView;
import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.AreaAdapter;

import com.forcepower.acedns.adapter.CoverageAdapter;
import com.forcepower.acedns.adapter.ForceInFieldDetailsAdapter;
import com.forcepower.acedns.adapter.ProductAdapter;
import com.forcepower.acedns.adapter.ProductivityAdapter;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;


public class HierarchicalReportActivity extends AppCompatActivity {
    public static TextView forceInFieldTodayTV = null;
    public static TextView forceInFieldMonthTV = null;
    public static TextView areaTodayTV = null;
    public static TextView areaMonthTV = null;
    public static TextView coverageTodayTV = null;
    public static TextView coverageMonthTV = null;
    public static TextView productivityTodayTV = null;
    public static TextView productivityMonthTV = null;
    public static TextView productTodayTV = null;
    public static TextView productMonthTV = null;
    public static TextView valueTodayTV = null;
    public static TextView valueMonthTV = null;
    public static TextView weightageTodayTV = null;
    public static TextView weightageMonthTV = null;
    public static EditText ed_po = null;
    public static TextView textViewInvoiceNo = null;
    public static TextView textViewInvoice = null;
    public static TextView textViewBargainNumber = null;
    public static TextView bargainLimitTv = null;
    public static TextView mTextViewBrokerName = null;
    public static TextView textViewTitleDirectorBroker = null;
    public static TextView mTextViewRouteName = null;
    public static TextView mTextViewCustomerName = null;

    public static TextView textViewLoadabilityTons = null;

    public static TextView textViewDespatchOriginValue = null;
    public static TextView textViewChosenVertical = null;
    public TextView textViewLoadability = null;
    public TextView textViewTransportMode = null;
    public static TextView mTextViewDepotName = null;
    public static TextView mTextViewRateType = null;
    public static LinearLayout poNoLayout = null;
    public static LinearLayout productivityLayout = null;
    public static LinearLayout productLayout = null;
    public static LinearLayout valueLayout = null;
    public static LinearLayout routeCustomerLayout = null;
    public static LinearLayout saudaBookedThroughLayout = null;
    public static LinearLayout despatchOriginSelectLayout = null;
    public static LinearLayout despatchOriginValueLayout = null;
    public static LinearLayout layoutverticalSpinner = null;
    public static RadioGroup mRadioGroupSBT = null;
    public static RadioGroup mRadioGroupRBO = null;
    public static RadioGroup radioGrpDespatchOrigin = null;
    public static Button mButtonSubmit = null;
    public static Button btn_StartDate = null;
    public static Button btn_EndDate = null;
    public static Button mButtonBack = null;
    public static ImageView mImageViewHeaderLogo = null;
    public String mCustomerName = "";
    public static String mLoadabilityTon = "";
    public String mSaudaType = "";
    public boolean isBrokerDataTaken = false;
    public boolean isDepoSelected = false;
    public AceDnsDatabase mAceDnsDatabase;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    Context mContext;
    public static String selectedFreightRate = "", selectedRouteCode = "";
    public static String verticalValueOfEmployee = "";
    Boolean isFirstLaunch=true;
    public static String startDate="";
    public static String endDate="";
    @Override
    public void onCreate(Bundle savedInstanceState)
    {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_hierarchical_report);
        RegisterActivities.registerActivity(this);
        InitializeView();
        ClearData();

        mContext = HierarchicalReportActivity.this;
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        Constants.mBrokerMasterList = new ArrayList<>();
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                HierarchicalReportActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                launchStartDateSelector();
//                                loadData();
                                break;
                        }
                    }
                });
            }
        };

        mButtonSubmit.setOnClickListener(v -> {
            finish();
        });
        btn_StartDate.setOnClickListener(v -> {
            launchStartDateSelector();
        });
        btn_EndDate.setOnClickListener(v -> {

            try {
                if(!startDate.matches(new SimpleDateFormat("dd/MM/yyyy").format(new Date()))){
                    launchEndDateSelector();
                }
            } catch (Exception e) {
                e.printStackTrace();
            }

        });
        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        try
        {
            if(HTTPUtils.isConnectionPossible(mContext))
            {
                PrepareCustomerData(1);
            }
            else
            {
                Utils.showToast(mContext,"Data Connection unavailable so could not load latest data.");
                launchStartDateSelector();
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void launchStartDateSelector() {
        // Get Current Date
        final Calendar c = Calendar.getInstance();
        int mYear = c.get(Calendar.YEAR);
        int mMonth = c.get(Calendar.MONTH);
        int mDay = c.get(Calendar.DAY_OF_MONTH);
        //launch datepicker modal
        DatePickerDialog datePickerDialog = new DatePickerDialog(this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
//                        try {
                        startDate = String.format("%02d", dayOfMonth) + "/" + String.format("%02d", (monthOfYear + 1)) + "/" + year;//04/02/2021
                        try {
                            if(startDate.matches(new SimpleDateFormat("dd/MM/yyyy").format(new Date()))){
                                endDate=startDate;
                                btn_StartDate.setText("START DATE: "+startDate);
                                btn_EndDate.setText("END DATE: "+endDate);
                                loadData();
                            }
                            else{
                                btn_StartDate.setText("START DATE: "+startDate);
                                launchEndDateSelector();
                            }

                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }
                }, mYear, mMonth, mDay);
        datePickerDialog.setCancelable(false);
        datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());
        datePickerDialog.getDatePicker().setMinDate(Utils.getStartOfFinancialYear().getTime());
        datePickerDialog.show();
    }
    private void launchEndDateSelector() {
        // Get Current Date
        final Calendar c = Calendar.getInstance();
        int mYear = c.get(Calendar.YEAR);
        int mMonth = c.get(Calendar.MONTH);
        int mDay = c.get(Calendar.DAY_OF_MONTH);
        //launch datepicker modal
        DatePickerDialog datePickerDialog = new DatePickerDialog(this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
//                        try {
                        endDate = String.format("%02d", dayOfMonth) + "/" + String.format("%02d", (monthOfYear + 1)) + "/" + year;//04/02/2021
                        btn_EndDate.setText("END DATE: "+endDate);
                        loadData();
                    }
                }, mYear, mMonth, mDay);
        datePickerDialog.setCancelable(false);
        datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());
        try{
            datePickerDialog.getDatePicker().setMinDate(new SimpleDateFormat("dd/MM/yyyy").parse(startDate).getTime());
        }
        catch (Exception e){

        }

        datePickerDialog.show();
    }
    public void backButtonClicked(View v){
        finish();
    }
    private void loadData()
    {
        forceInFieldTodayTV.setText(mAceDnsDatabase.AttCountTodayMonth(1) +"/"+mAceDnsDatabase.EmpCount()+" ");
        forceInFieldMonthTV.setText(mAceDnsDatabase.AttCountTodayMonth(2) +"/"+mAceDnsDatabase.EmpCount()+" ");//AttCountTodayMonth
        areaTodayTV.setText(mAceDnsDatabase.RouteCountTodayMonth(1) +"/"+mAceDnsDatabase.RouteCount()+" ");
        areaMonthTV.setText(mAceDnsDatabase.RouteCountTodayMonth(2) +"/"+mAceDnsDatabase.RouteCount()+" ");
        coverageTodayTV.setText(mAceDnsDatabase.actualCoverageCountTodayMonth(1) +"/"+mAceDnsDatabase.totalCoverageCountTodayMonth(1)+" ");
        coverageMonthTV.setText(mAceDnsDatabase.actualCoverageCountTodayMonth(2) +"/"+mAceDnsDatabase.totalCoverageCountTodayMonth(2)+" ");//actualCoverageCountTodayMonth
        productivityTodayTV.setText(mAceDnsDatabase.actualProductivityCountTodayMonth(1) +"/"+mAceDnsDatabase.totalProductivityCountTodayMonth(1)+" ");
        productivityMonthTV.setText(mAceDnsDatabase.actualProductivityCountTodayMonth(2) +"/"+mAceDnsDatabase.totalProductivityCountTodayMonth(2)+" ");//actualProductivityCountTodayMonth totalProductivityCountTodayMonth
        productTodayTV.setText(mAceDnsDatabase.actualProductCountTodayMonth(1) +"/"+mAceDnsDatabase.prodCount()+" ");
        productMonthTV.setText(mAceDnsDatabase.actualProductCountTodayMonth(2) +"/"+mAceDnsDatabase.prodCount()+" ");//actualProductCountTodayMonth
        valueTodayTV.setText(defaultFormatWithComma.format(mAceDnsDatabase.actualValueCountTodayMonth(1))+" ");
        valueMonthTV.setText(defaultFormatWithComma.format(mAceDnsDatabase.actualValueCountTodayMonth(2))+" ");//actualValueCountTodayMonth

        weightageTodayTV.setText(defaultFormatWithComma.format(mAceDnsDatabase.actualWeightageCountTodayMonth(1))+" ");
        weightageMonthTV.setText(defaultFormatWithComma.format(mAceDnsDatabase.actualWeightageCountTodayMonth(2))+" ");

        forceInFieldTodayTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getEmpAttendanceList("day");
            if(empActivityList.size()>0)
                showForceInFieldList(empActivityList,"Today");

        });
        forceInFieldMonthTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getEmpAttendanceList("month");
            if(empActivityList.size()>0)
                showForceInFieldList(empActivityList,"Current Month");

        });
        areaTodayTV.setOnClickListener(v -> {
        ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getRouteListToday("day");
        if(empActivityList.size()>0)
            showAreaList(empActivityList,"Today");

    });
        areaMonthTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getRouteListToday("month");
            if(empActivityList.size()>0)
                showAreaList(empActivityList,"Current Month");
        });
        coverageTodayTV.setOnClickListener(v -> {

            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getCoverageListToday("day");
            if(empActivityList.size()>0)
                showCoverageList(empActivityList,"Today");

        });
        coverageMonthTV.setOnClickListener(v -> {

            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getCoverageListToday("month");
            if(empActivityList.size()>0)
                showCoverageList(empActivityList,"Current Month");

        });
        productivityTodayTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getProductivityListToday("day");
            if(empActivityList.size()>0)
                showProductivityList(empActivityList,"Today");

        });
        productivityMonthTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getProductivityListToday("month");
            if(empActivityList.size()>0)
                showProductivityList(empActivityList,"Current Month");

        });
        productTodayTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getProductListToday("day");
            if(empActivityList.size()>0)
                showProductList(empActivityList,"Today");

        });
        productMonthTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getProductListToday("month");
            if(empActivityList.size()>0)
                showProductList(empActivityList,"Current Month");

        });
        valueTodayTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getProductivityListToday("day");
            if(empActivityList.size()>0)
                showProductivityList(empActivityList,"Today");

        });
        valueMonthTV.setOnClickListener(v -> {
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getProductivityListToday("month");
            if(empActivityList.size()>0)
                showProductivityList(empActivityList,"Current Month");

        });

    }

    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        new commonAsyncTaskMaster(mContext, "order_status");
                        break;
                }

                Message msg = mHandlerPrepareSaudaData.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPrepareSaudaData.sendMessage(msg);
            }
        }.start();
    }

    public void InitializeView() {
        forceInFieldTodayTV = findViewById(R.id.forceInFieldTodayTV);
        forceInFieldMonthTV = findViewById(R.id.forceInFieldMonthTV);
        areaTodayTV = findViewById(R.id.areaTodayTV);
        areaMonthTV = findViewById(R.id.areaMonthTV);
        coverageTodayTV = findViewById(R.id.coverageTodayTV);
        coverageMonthTV = findViewById(R.id.coverageMonthTV);
        productivityTodayTV = findViewById(R.id.productivityTodayTV);
        productivityMonthTV = findViewById(R.id.productivityMonthTV);
        productTodayTV = findViewById(R.id.productTodayTV);
        productMonthTV = findViewById(R.id.productMonthTV);
        valueTodayTV = findViewById(R.id.valueTodayTV);
        weightageTodayTV = findViewById(R.id.weightageTodayTV);
        valueMonthTV = findViewById(R.id.valueMonthTV);
        weightageMonthTV = findViewById(R.id.weightageMonthTV);
        ed_po = findViewById(R.id.ed_po);
        textViewInvoice = (TextView) findViewById(R.id.textViewInvoice);
        textViewInvoiceNo = (TextView) findViewById(R.id.textViewInvoiceNo);
        textViewBargainNumber = (TextView) findViewById(R.id.textViewBargainNumber);
        bargainLimitTv = (TextView) findViewById(R.id.bargainLimitTv);
        mTextViewBrokerName = (TextView) findViewById(R.id.textViewBrokerOrDirectValue);
        textViewTitleDirectorBroker = findViewById(R.id.textViewTitleDirectorBroker);
        mTextViewRouteName = (TextView) findViewById(R.id.textViewRouteValue);
        mTextViewCustomerName = (TextView) findViewById(R.id.textViewCustomerValue);

        textViewLoadabilityTons = (TextView) findViewById(R.id.textViewLoadabilityTons);

        textViewDespatchOriginValue = (TextView) findViewById(R.id.textViewDespatchOriginValue);
        textViewChosenVertical = (TextView) findViewById(R.id.textViewChosenVertical);
        textViewLoadability = (TextView) findViewById(R.id.textViewLoadability);
        textViewTransportMode = (TextView) findViewById(R.id.textViewTransportMode);
        mTextViewDepotName = (TextView) findViewById(R.id.textViewDepotValue);
        mTextViewRateType = (TextView) findViewById(R.id.textViewRateTypeValue);
        productivityLayout = (LinearLayout) findViewById(R.id.productivityLayout);
        productLayout = (LinearLayout) findViewById(R.id.productLayout);
        valueLayout = (LinearLayout) findViewById(R.id.valueLayout);
        saudaBookedThroughLayout = (LinearLayout) findViewById(R.id.saudaBookedThroughLayout);
        poNoLayout = (LinearLayout) findViewById(R.id.poNoLayout);
        routeCustomerLayout = (LinearLayout) findViewById(R.id.routeCustomerLayout);
        despatchOriginSelectLayout = (LinearLayout) findViewById(R.id.despatchOriginLayout);
        despatchOriginValueLayout = (LinearLayout) findViewById(R.id.despatchOriginValueLayout);
        layoutverticalSpinner = (LinearLayout) findViewById(R.id.layoutverticalSpinner);


        mRadioGroupSBT = (RadioGroup) findViewById(R.id.radioSelectSBT);
        mRadioGroupRBO = (RadioGroup) findViewById(R.id.radioSelectRBO);
        radioGrpDespatchOrigin = (RadioGroup) findViewById(R.id.radioGrpDespatchOrigin);

        mRadioGroupSBT.setEnabled(false);
        mRadioGroupRBO.setEnabled(false);

        mButtonSubmit = (Button) findViewById(R.id.btn_Submi);
        btn_StartDate = (Button) findViewById(R.id.btn_StartDate);
        btn_EndDate = (Button) findViewById(R.id.btn_EndDate);
        mButtonBack = (Button) findViewById(R.id.back);

        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));

        textViewTitleDirectorBroker.setText("Bargain Booked Through");
        bargainLimitTv.setText("Bargain Limit (MT)    ");
    }


    public void ClearData() {
        mTextViewBrokerName.setText("");
        mTextViewRouteName.setText("");
        mTextViewCustomerName.setText("");
        mTextViewDepotName.setText("");
        mTextViewRateType.setText("");
        isBrokerDataTaken = false;
        isDepoSelected = false;
        //mRadioGroupRBO.clearCheck();
    }

    @Override
    public void onResume() {
        super.onResume();
        if (mImageViewHeaderLogo != null) {
            if (Constants.logoBmp != null) {
                mImageViewHeaderLogo.setVisibility(View.VISIBLE);
                mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
            } else {
                mImageViewHeaderLogo.setVisibility(View.GONE);
            }
        }
    }

    public void showProductivityList(ArrayList<commonDatabaseHelper> empActivityList,String dayOrMonth)
    {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.activity_productivity_hierarchical_report);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        mDetailsDialog.setCancelable(true);
        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.title);
        Button btn_generate =  mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setText("OK");
        btn_generate.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        textViewTitleName.setText("Productivity Details: "+dayOrMonth);

        RecyclerView dialogList =  mDetailsDialog.findViewById(R.id.list);
        RecyclerView.Adapter  ReportAdapterObject = new ProductivityAdapter(mContext, R.layout.list_item_productivity_report_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);

        mDetailsDialog.show();
    }
    public void showProductList(ArrayList<commonDatabaseHelper> empActivityList,String dayOrMonth)
    {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.activity_product_hierarchical_report);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        mDetailsDialog.setCancelable(true);
        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.title);
        Button btn_generate =  mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setText("OK");
        btn_generate.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        textViewTitleName.setText("Product Details: "+dayOrMonth);

        RecyclerView dialogList =  mDetailsDialog.findViewById(R.id.list);
        RecyclerView.Adapter  ReportAdapterObject = new ProductAdapter(mContext, R.layout.list_item_product_report_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);

        mDetailsDialog.show();
    }
    public void showForceInFieldList(ArrayList<commonDatabaseHelper> empActivityList,String dayOrMonth)
    {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.hierarchcal_report_details);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        mDetailsDialog.setCancelable(true);
        TextView text1 =  mDetailsDialog.findViewById(R.id.text1);
        TextView text2 =  mDetailsDialog.findViewById(R.id.text2);
        text1.setText("Name");
        text2.setText("Att Time");

        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.title);
        Button btn_generate =  mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setText("OK");
        btn_generate.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        textViewTitleName.setText("Force In Field: "+dayOrMonth);

        ListView dialogList = (ListView) mDetailsDialog.findViewById(R.id.list);
        ForceInFieldDetailsAdapter ReportAdapterObject = new ForceInFieldDetailsAdapter(mContext, R.layout.list_item_hierarchical_report_light_material, R.layout.list_item_hierarchical_report_dark_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);

        mDetailsDialog.show();
    }
    public void showAreaList(ArrayList<commonDatabaseHelper> empActivityList,String dayOrMonth)
    {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.hierarchcal_report_details);
        TextView text1 =  mDetailsDialog.findViewById(R.id.text1);
        FrameLayout secondDataLayout =  mDetailsDialog.findViewById(R.id.secondDataLayout);
        text1.setText("Route");
        secondDataLayout.setVisibility(View.GONE);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        mDetailsDialog.setCancelable(true);
        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.title);
        Button btn_generate =  mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setText("OK");
        btn_generate.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        textViewTitleName.setText("Area to visit: "+dayOrMonth);

        ListView dialogList = (ListView) mDetailsDialog.findViewById(R.id.list);
        AreaAdapter ReportAdapterObject = new AreaAdapter(mContext, R.layout.list_item_hierarchical_report_light_material, R.layout.list_item_hierarchical_report_dark_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);

        mDetailsDialog.show();
    }
    public void showCoverageList(ArrayList<commonDatabaseHelper> empActivityList,String dayOrMonth)
    {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.activity_coverage_hierarchical_report);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        mDetailsDialog.setCancelable(true);
        TextView item1Tv =  mDetailsDialog.findViewById(R.id.item1Tv);
        TextView item2Tv =  mDetailsDialog.findViewById(R.id.item2Tv);
        TextView item3Tv =  mDetailsDialog.findViewById(R.id.item3Tv);
        TextView item4Tv =  mDetailsDialog.findViewById(R.id.item4Tv);
        TextView item5Tv =  mDetailsDialog.findViewById(R.id.item5Tv);
        TextView item6Tv =  mDetailsDialog.findViewById(R.id.item6Tv);
        TextView item7Tv =  mDetailsDialog.findViewById(R.id.item7Tv);
        TextView item8Tv =  mDetailsDialog.findViewById(R.id.item8Tv);
        TextView item9Tv =  mDetailsDialog.findViewById(R.id.item9Tv);

        item1Tv.setText(Constants.totalData.getItem0());
        item2Tv.setText(Constants.totalData.getItem1());
        item3Tv.setText(Constants.totalData.getItem2());
        item4Tv.setText(Constants.totalData.getItem3());
        item5Tv.setText(Constants.totalData.getItem4());
        item6Tv.setText(Constants.totalData.getItem5());
        item7Tv.setText(defaultFormatWithComma.format(Double.parseDouble(Constants.totalData.getItem6())));
        item8Tv.setText(defaultFormatWithComma.format(Double.parseDouble(Constants.totalData.getItem7())));
        item9Tv.setText(defaultFormatWithComma.format(Double.parseDouble(Constants.totalData.getItem8())));

        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.title);
        Button btn_generate =  mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setText("OK");
        btn_generate.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        textViewTitleName.setText("Coverage Details:"+dayOrMonth);

        RecyclerView dialogList =  mDetailsDialog.findViewById(R.id.list);
        RecyclerView.Adapter  ReportAdapterObject = new CoverageAdapter(mContext, R.layout.list_item_coverage_report_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);

        mDetailsDialog.show();
    }

}