package com.forcepower.acedns.activity;

import android.animation.Animator;
import android.animation.ValueAnimator;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.PointF;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.util.Pair;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.View;
import android.view.Window;
import android.view.animation.AccelerateDecelerateInterpolator;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import com.androidplot.Region;
import com.androidplot.ui.Anchor;
import com.androidplot.ui.HorizontalPositioning;
import com.androidplot.ui.SeriesBundle;
import com.androidplot.ui.SeriesRenderer;
import com.androidplot.ui.Size;
import com.androidplot.ui.SizeMode;
import com.androidplot.ui.TextOrientation;
import com.androidplot.ui.VerticalPositioning;
import com.androidplot.ui.widget.TextLabelWidget;
import com.androidplot.util.PixelUtils;
import com.androidplot.util.SeriesUtils;
import com.androidplot.xy.BarFormatter;
import com.androidplot.xy.BarRenderer;
import com.androidplot.xy.BoundaryMode;
import com.androidplot.xy.ScalingXYSeries;
import com.androidplot.xy.SimpleXYSeries;
import com.androidplot.xy.StepMode;
import com.androidplot.xy.XYGraphWidget;
import com.androidplot.xy.XYPlot;
import com.androidplot.xy.XYSeries;
import com.androidplot.xy.XYSeriesFormatter;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.SelfAppraisalDetails;
import com.forcepower.acedns.bean.SelfAppraisalDetailsCustomerWise;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.text.FieldPosition;
import java.text.Format;
import java.text.ParsePosition;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import androidx.annotation.NonNull;
import androidx.core.content.ContextCompat;

public class SelfAppraisalLandingActivity extends AceDnsParentActivity {
    XYPlot plot;
    private static final String NO_SELECTION_TXT = "Touch bar to select.";
    ArrayList<String> xLabels;
    ArrayList<Number> xValues;

    List<Number> targetList;
    List<Number> achievementList;
    List<Integer> colorList, legendColors;
    List<String> legendLabels;
    Context mContext;
    AceDnsDatabase mAceDnsDatabase;
    Calendar c = Calendar.getInstance();
    int year = c.get(Calendar.YEAR);
    int currentMonth = c.get(Calendar.MONTH);
    public static String currentTargetAchievementType = "";
    public String currentTargetAchievementTypeRoute = "";
    TextView switchStatusTv;
    ImageView imgLogo;
    SelfAppraisalDetails selfAppraisalSetup;
    ArrayAdapter<String> spinnerArrayAdapter;
    ArrayList<CustomerDetails> customerList;
    ArrayList<RouteDetails> routrList;
    ArrayList<String> spinnerArrayCustomers;
    ArrayList<String> spinnerArrayRoute;
    //ArrayList<String> spinnerArrayRoute;
    ArrayList<String> spinnerArrayVerticals;
    ArrayList<String> spinnerArrayFYear;
    String selectedFYear="";
    public static String chosenVertical="";
    String chosenMonth = "";
    String title = "";//tar or achv
    private XYSeries series1;
    private XYSeries series2;
    TextView custEmpNameTc,routeNameTc;
    String chosenItemPrevious ="";

    private MyBarFormatter formatterTarget;

    private MyBarFormatter formatterAchievement;

    private MyBarFormatter selectionFormatter;

    private TextLabelWidget selectionWidget;

    private Pair<Integer, XYSeries> selection;
    private ProgressDialog mPrepareSurveyMenuProgressDialog;
    private Handler mPrepareSurveyMenuHandler;
    LinearLayout infoLayout,lLRoute;
    private static final int FIRST_FISCAL_MONTH = Calendar.APRIL;
    private Calendar calendarDate;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_self_appraisal_landing);
        chosenVertical="";
        mContext = this;
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(this);
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        infoLayout =  findViewById(R.id.infoLayout);
        lLRoute =  findViewById(R.id.lLRoute);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        switchStatusTv = (TextView) findViewById(R.id.switchStatus);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        colorList = new ArrayList<>();
        custEmpNameTc = (TextView) findViewById(R.id.custEmpNameTc);
        routeNameTc = (TextView) findViewById(R.id.custEmpRouteNameTc);
        createChart();
        routrList = mAceDnsDatabase.getRouteAllList();
        Constants.selectedRouteWise = routrList.get(0);
//        Constants.selectedRouteWise.setRouteName("");
        ArrayList<String> categories = getSelfAppraisalCatagories();
        if(categories.size()==0)
        {
            Utils.showToast(mContext,"Proper data not found.");
            finish();
        }
        else if(categories.size()==1)
        {
            currentTargetAchievementType = categories.get(0);
            dataloadingProcess();
        }
        else
        {
          //  selectTargetActualType(categories);
        }
        //Utils.showToast(mContext,"Proper ...");
        mPrepareSurveyMenuHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                if(mPrepareSurveyMenuProgressDialog != null && mPrepareSurveyMenuProgressDialog.isShowing())
                    mPrepareSurveyMenuProgressDialog.dismiss();

                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SelfAppraisalLandingActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                updatePlot();
                                break;
                            case 2:
                                updatePlot();
                                break;

                        }

                    }
                });
            }
        };

        //showCustomerDialog();

        calendarDate= Calendar.getInstance();


        int financial = getFiscalYear();
        int financialPrevious = financial - 1;

        String financialYear = String.valueOf(financial) + "-" + String.valueOf(financial+1);;
        String financialYearPrevious = String.valueOf(financialPrevious) + "-" + String.valueOf(financial);

//        String financialYear = String.valueOf(financialPrevious) + "-" + String.valueOf(financial);;
//        String financialYearPrevious = String.valueOf(financialPrevious-1) + "-" + String.valueOf(financialPrevious);

        //Utils.showToast(mContext,financialYear+"/"+financialYearPrevious);
        Constants.selectedFY = "1";
        selfAppraisalSetup = mAceDnsDatabase.getTargetAchievementSetupDetails();
        if(selfAppraisalSetup.getPrevious_year().equalsIgnoreCase("yes")) {
            showYearsDialog("FY " + financialYearPrevious,"FY "+financialYear, categories);
        }
    }

    public int getFiscalMonth() {
        int month = calendarDate.get(Calendar.MONTH);
        int result = ((month - FIRST_FISCAL_MONTH - 1) % 12) + 1;
        if (result < 0) {
            result += 12;
        }
        return result;
    }
    public int getFiscalYear() {
        int month = calendarDate.get(Calendar.MONTH);
        int year = calendarDate.get(Calendar.YEAR);
        return (month >= FIRST_FISCAL_MONTH) ? year : year - 1;
    }

    public int getCalendarMonth() {
        return calendarDate.get(Calendar.MONTH);
    }

    public int getCalendarYear() {
        return calendarDate.get(Calendar.YEAR);
    }

    class MyBarFormatter extends BarFormatter {

        public MyBarFormatter(int fillColor, int borderColor) {
            super(fillColor, borderColor);
        }

        @Override
        public Class<? extends SeriesRenderer> getRendererClass() {
            return MyBarRenderer.class;
        }

        @Override
        public SeriesRenderer doGetRendererInstance(XYPlot plot) {
            return new MyBarRenderer(plot);
        }
    }
    class MyBarRenderer extends BarRenderer<MyBarFormatter> {

        public MyBarRenderer(XYPlot plot) {
            super(plot);
        }
        public BarOrientation getBarOrientation() {
            return BarRenderer.BarOrientation.SIDE_BY_SIDE;
        }
        /**
         * Implementing this method to allow us to inject our
         * special selection getFormatter.
         * @param index index of the point being rendered.
         * @param series XYSeries to which the point being rendered belongs.
         * @return
         */
        @Override
        public MyBarFormatter getFormatter(int index, XYSeries series) {
            if (selection != null &&
                    selection.second == series &&
                    selection.first == index) {
                return selectionFormatter;
            } else {
                return getFormatter(series);
            }
        }
    }
    private void MakeTargetAchivementDataLoadingProcess() {
        showStatusOfSelection();
        if (currentTargetAchievementType.toLowerCase().matches("customer wise") || currentTargetAchievementType.matches("Employee wise"))
        {
            RadioGroup mRadioGroupSBTA = (RadioGroup) findViewById(R.id.radioSelect);
            RadioButton radioCustomerEmp = (RadioButton) findViewById(R.id.radioCustomerEmp);
            mRadioGroupSBTA.setVisibility(View.VISIBLE);
            radioCustomerEmp.setVisibility(View.VISIBLE);
//            radioCustomerEmp.setChecked(true);
            if(currentTargetAchievementType.matches("Employee wise"))
            {
                radioCustomerEmp.setText("Employee");
            }

            radioCustomerEmp.setOnClickListener(new View.OnClickListener()
            {
                @Override
                public void onClick(View v) {
//                    showCustomerSpinner();
                    showCustomerDialog();
                }
            });
            if(currentTargetAchievementTypeRoute.matches("customer wise")){
                radioCustomerEmp.setVisibility(View.VISIBLE);
            }
             if(currentTargetAchievementTypeRoute.matches("customer_wise@route_wise")){
                RadioButton radioCustomerEmpRoute = (RadioButton) findViewById(R.id.radioCustomerEmpRoute);
                mRadioGroupSBTA.setVisibility(View.VISIBLE);
                 radioCustomerEmp.setVisibility(View.GONE);
                radioCustomerEmpRoute.setVisibility(View.VISIBLE);

                radioCustomerEmpRoute.setOnClickListener(new View.OnClickListener()
                {
                    @Override
                    public void onClick(View v) {
                        showCustomerRouteDialog();
                        radioCustomerEmp.setVisibility(View.VISIBLE);
                    }
                });


            if (selfAppraisalSetup.getvertical_wise().contains("yes"))
            {
                RadioGroup mRadioGroupSBT = (RadioGroup) findViewById(R.id.radioSelect);
                RadioButton mRadiobtnVertical = (RadioButton) findViewById(R.id.radioVertical);
                mRadioGroupSBT.setVisibility(View.VISIBLE);
                mRadiobtnVertical.setVisibility(View.VISIBLE);
                mRadiobtnVertical.setOnClickListener(new View.OnClickListener()
                {
                    @Override
                    public void onClick(View v) {
//                        showVerticalSpinner();
                        generateDataInVerticalList();
                        if(spinnerArrayVerticals.size()==0)
                        {
                            Utils.showToast(mContext,"Proper data not found.");
                            finish();
                        }
                        else
                        {
                            ShowVerticalSelectDialog();
                        }

                    }
                });
            }


        }
        else
        {


            }
//            prepareTargetAchievementList();
//            updatePlot();
        }
    }

    public void ShowVerticalSelectDialog( )
    {

        final Dialog routePlanListDialog = new Dialog(mContext, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Type");
        ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
//        ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, categories);
        SimpleStringAdapter dataAdapter = new SimpleStringAdapter(mContext, R.layout.simple_list_child, spinnerArrayVerticals);
        dialogList.setAdapter(dataAdapter);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int i,
                                    long arg3) {
                routePlanListDialog.cancel();
                chosenVertical= spinnerArrayVerticals.get(i);
                showStatusOfSelection();
                loadChartCustomerWise();
            }
        });
        ImageView image_cancel = (ImageView) routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.GONE);
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
        final EditText autoCompleteTextView1 = (EditText) routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.GONE);

    }

    public void showStatusOfSelection()
    {
        final TextView empCustTextView = (TextView) findViewById(R.id.empCustTextView);
        empCustTextView.setText(currentTargetAchievementType.split(" ")[0]+": ");
        switchStatusTv.setText(currentTargetAchievementType+" "+chosenVertical + " Tar-vs-Achv.");
    }

    public void selectTargetActualType(final ArrayList<String> categories) {
        final Dialog routePlanListDialog = new Dialog(mContext, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Type1");
        ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
//        ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, categories);
        SimpleStringAdapter dataAdapter = new SimpleStringAdapter(mContext, R.layout.simple_list_child, categories);
        dialogList.setAdapter(dataAdapter);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routePlanListDialog.cancel();
                currentTargetAchievementType = categories.get(arg2);
                dataloadingProcess();

            }
        });
        ImageView image_cancel = (ImageView) routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.GONE);
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
        final EditText autoCompleteTextView1 = (EditText) routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.GONE);

    }

    public void dataloadingProcess() {
        MakeTargetAchivementDataLoadingProcess();

        if(currentTargetAchievementType.contains("Customer") || currentTargetAchievementType.contains("Employee"))
        {
            CustomerDetails cd=new CustomerDetails();
            if(currentTargetAchievementType.contains("Customer"))
            {
                cd.setCustomerName("All Customers");
            }
            else
            {
                cd.setCustomerName("All Employees");
            }
            cd.setCustomerCode("");
            Constants.selectedCustomer = cd;
            custEmpNameTc.setText( Constants.selectedCustomer.getCustomerName());
            showStatusOfSelection();
            loadChartCustomerWise();
        }
        else
        {
            loadChartProdGroupWise();
        }
    }

    public void generateDataIncustomerOrEmpList() {
        //customerList = mAceDnsDatabase.getCustomerListFromSelfAppraisalSummary();
        try {
            if (Constants.selectedRouteWise.getRouteName().isEmpty() || Constants.selectedRouteWise.getRouteName().equals("All Route")) {
                customerList = mAceDnsDatabase.getCustomerListFromSelfAppraisalSummary();
            }else{
                customerList = mAceDnsDatabase.getCustomerListFromSelfAppraisalSummary();
                //Utils.showToast(mContext,"Select Route First");
            }
            spinnerArrayCustomers = new ArrayList<>();
            for (int i = 0; i < customerList.size(); i++) {
                spinnerArrayCustomers.add(customerList.get(i).getCustomerName());
            }
        }catch(Exception e){
            showCustomerRouteDialog();
        }
    }

    public void generateDataRouteList() {
        routrList = mAceDnsDatabase.getRouteAllList();
        spinnerArrayRoute = new ArrayList<>();
        for (int i = 0; i < routrList.size(); i++) {
            spinnerArrayRoute.add(routrList.get(i).getRouteName());
        }
    }


    public void showCustomerRouteDialog( )
    {
        generateDataRouteList();
        final Dialog routePlanListDialog = new Dialog(mContext, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select Route");
        ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
//        ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, categories);
        SimpleStringAdapter dataAdapter = new SimpleStringAdapter(mContext, R.layout.simple_list_child, spinnerArrayRoute);
        dialogList.setAdapter(dataAdapter);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int i,
                                    long arg3) {
                routePlanListDialog.cancel();
                Constants.selectedRouteWise = routrList.get(i);
                routeNameTc.setText( Constants.selectedRouteWise.getRouteName());
               // showStatusOfSelection();
                //loadChartCustomerWise();
            }
        });
        ImageView image_cancel = (ImageView) routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.GONE);
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
        final EditText autoCompleteTextView1 = (EditText) routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.GONE);

    }

    public void showYearsDialog(String cy,String py ,ArrayList<String> categories)
    {
        spinnerArrayFYear = new ArrayList<>();
        spinnerArrayFYear.add(cy);
        spinnerArrayFYear.add(py);
        final Dialog routePlanListDialog = new Dialog(mContext, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select Financial year");
        ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
//        ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, categories);
        SimpleStringAdapter dataAdapter = new SimpleStringAdapter(mContext, R.layout.simple_list_child_fy, spinnerArrayFYear);
        dialogList.setAdapter(dataAdapter);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int i,
                                    long arg3) {
                routePlanListDialog.cancel();
                selectedFYear= spinnerArrayFYear.get(i);
                Constants.selectedFY = ""+i;
                //showStatusOfSelection();
                //loadChartCustomerWise();
                //Utils.showToast(mContext,selectedFYear);
                currentTargetAchievementType = categories.get(0);
                dataloadingProcess();

            }
        });
        ImageView image_cancel = (ImageView) routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.GONE);
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
        final EditText autoCompleteTextView1 = (EditText) routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.GONE);

    }

    public void showCustomerDialog( )
    {
        generateDataIncustomerOrEmpList();
        final Dialog routePlanListDialog = new Dialog(mContext, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select "+currentTargetAchievementType.split(" ")[0]);
        ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
//        ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, categories);
        SimpleStringAdapter dataAdapter = new SimpleStringAdapter(mContext, R.layout.simple_list_child, spinnerArrayCustomers);
        dialogList.setAdapter(dataAdapter);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int i,
                                    long arg3) {
                routePlanListDialog.cancel();
                Constants.selectedCustomer = customerList.get(i);
                custEmpNameTc.setText( Constants.selectedCustomer.getCustomerName());
                showStatusOfSelection();
                loadChartCustomerWise();
            }
        });
        ImageView image_cancel = (ImageView) routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.GONE);
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
        final EditText autoCompleteTextView1 = (EditText) routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.GONE);

    }
    public void generateDataInVerticalList()
    {
        if(currentTargetAchievementType.matches("Employee wise"))
        {
            spinnerArrayVerticals = mAceDnsDatabase.getVertivcalListFromSelfAppraisalSummary("self_appraisal_emp_wise");
        }
        else
        {
            spinnerArrayVerticals = mAceDnsDatabase.getVertivcalListFromSelfAppraisalSummary("self_appraisal_summary");
        }

    }


    public void loadChartCustomerWise() {
        PrepareSelfAppraisalMenuData(1);
    }
    public void loadChartProdGroupWise() {
        PrepareSelfAppraisalMenuData(2);
    }
    public void PrepareSelfAppraisalMenuData(final int task) {

        if(mPrepareSurveyMenuProgressDialog != null && mPrepareSurveyMenuProgressDialog.isShowing())
            mPrepareSurveyMenuProgressDialog.dismiss();

        mPrepareSurveyMenuProgressDialog = new ProgressDialog(this);
        mPrepareSurveyMenuProgressDialog.setMessage("Please wait..");
        mPrepareSurveyMenuProgressDialog.setCancelable(false);
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        prepareTargetAchievementListCustomerEmpWise();
                        break;
                    case 2:

                        prepareTargetAchievementList();
                        break;

                    case 3:

                        prepareTargetAchievementList();
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
    @NonNull
    private ArrayList<String> getSelfAppraisalCatagories() {
        selfAppraisalSetup = new SelfAppraisalDetails();
        ArrayList<String> categories = new ArrayList<>();
        selfAppraisalSetup = mAceDnsDatabase.getTargetAchievementSetupDetails();
        String multipleTargetAchievement = selfAppraisalSetup.getMultipleTargetAchievement();
        if (multipleTargetAchievement.matches("yes")) {
            String multipleTargetAchievementVal = selfAppraisalSetup.getMultipleTargetAchievementVal();
            if (multipleTargetAchievementVal.toLowerCase().contains("customer_wise")) {
                categories.add("Customer wise");
            }
            if (multipleTargetAchievementVal.contains("branch_wise")) {
                categories.add("Branch wise");
            }
            if (multipleTargetAchievementVal.contains("employee_wise")) {
                categories.add("Employee wise");
            }

            if (multipleTargetAchievementVal.contains("customer_wise@route_wise")) {
                currentTargetAchievementTypeRoute = "customer_wise@route_wise";
                lLRoute.setVisibility(View.VISIBLE);
                categories.add("Route wise");
            }


        } else if (selfAppraisalSetup.getProductGroupWise().contains("yes")) {
            categories.add("Product group wise");
        } else if (selfAppraisalSetup.getCustomerWise().contains("yes")) {
            categories.add("Customer wise");
        } else if (selfAppraisalSetup.getBranchWise().contains("yes")) {
            categories.add("Branch wise");
        }
        else if (selfAppraisalSetup.getEmployeeWise().contains("yes")) {
            categories.add("Employee wise");
        }

        if (selfAppraisalSetup.getMultipleTargetAchievementVal().contains("customer_wise@route_wise")) {
            currentTargetAchievementTypeRoute = "customer_wise@route_wise";
        }

        return categories;
    }


    private void createChart()
    {
        AddValuesToBarEntryLabels();
        plot = (XYPlot) findViewById(R.id.selfAppraisalBarChart);
        plot.getGraph().getLineLabelStyle(XYGraphWidget.Edge.BOTTOM).setFormat(new GraphXLabelFormat());

        formatterTarget = new MyBarFormatter(ContextCompat.getColor(mContext, R.color.blue), Color.LTGRAY);
        formatterTarget.setMarginLeft(PixelUtils.dpToPix(1));
        formatterTarget.setMarginRight(PixelUtils.dpToPix(1));
        formatterAchievement = new MyBarFormatter(ContextCompat.getColor(mContext, R.color.colorGreen), Color.LTGRAY);
        formatterAchievement.setMarginLeft(PixelUtils.dpToPix(1));
        formatterAchievement.setMarginRight(PixelUtils.dpToPix(1));
        selectionFormatter = new MyBarFormatter(R.color.colorOrangeAppCommon, Color.WHITE);
        Size size = new Size(
                PixelUtils.dpToPix(100), SizeMode.ABSOLUTE,
                PixelUtils.dpToPix(100), SizeMode.ABSOLUTE);

        selectionWidget = new TextLabelWidget(plot.getLayoutManager(), NO_SELECTION_TXT,
                size,
                TextOrientation.HORIZONTAL);

        selectionWidget.getLabelPaint().setTextSize(PixelUtils.dpToPix(16));

        // add a dark, semi-transparent background to the selection label widget:
        Paint p = new Paint();
        p.setARGB(100, 0, 0, 0);
        selectionWidget.setBackgroundPaint(p);
        selectionWidget.position(
                0, HorizontalPositioning.RELATIVE_TO_CENTER,
                PixelUtils.dpToPix(10), VerticalPositioning.ABSOLUTE_FROM_TOP,
                Anchor.TOP_MIDDLE);
        selectionWidget.pack();

        // reduce the number of range labels
//        plot.setLinesPerRangeLabel(3);
        plot.setRangeLowerBoundary(0, BoundaryMode.FIXED);
        plot.getGraph().setBackgroundPaint(null);
        plot.getGraph().setGridBackgroundPaint(null);
        plot.getGraph().setDomainGridLinePaint(null);
        plot.getGraph().setRangeGridLinePaint(null);

        plot.getGraph().setPaddingLeft(150);
//        plot.setLinesPerDomainLabel(3);
        plot.setOnTouchListener(new View.OnTouchListener() {
            @Override
            public boolean onTouch(View view, MotionEvent motionEvent) {
                if (motionEvent.getAction() == MotionEvent.ACTION_DOWN) {
                    onPlotClicked(new PointF(motionEvent.getX(), motionEvent.getY()));
                }
                return true;
            }
        });
    }
    private void onPlotClicked(PointF point) {

        // make sure the point lies within the graph area.  we use gridrect
        // because it accounts for margins and padding as well.
        if (plot.containsPoint(point.x, point.y)) {
            Number x = plot.getXVal(point);
            Number y = plot.getYVal(point);

            selection = null;
            double xDistance = 0;
            double yDistance = 0;

            // find the closest value to the selection:
            for (SeriesBundle<XYSeries, ? extends XYSeriesFormatter> sfPair : plot
                    .getRegistry().getSeriesAndFormatterList()) {
                XYSeries series = sfPair.getSeries();
                for (int i = 0; i < series.size(); i++) {
                    Number thisX = series.getX(i);
                    Number thisY = series.getY(i);
                    if (thisX != null && thisY != null) {
                        double thisXDistance =
                                Region.measure(x, thisX).doubleValue();
                        double thisYDistance =
                                Region.measure(y, thisY).doubleValue();
                        if (selection == null) {
                            selection = new Pair<>(i, series);
                            xDistance = thisXDistance;
                            yDistance = thisYDistance;
                        } else if (thisXDistance < xDistance) {
                            selection = new Pair<>(i, series);
                            xDistance = thisXDistance;
                            yDistance = thisYDistance;
                        } else if (thisXDistance == xDistance &&
                                thisYDistance < yDistance &&
                                thisY.doubleValue() >= y.doubleValue()) {
                            selection = new Pair<>(i, series);
                            xDistance = thisXDistance;
                            yDistance = thisYDistance;
                        }
                    }
                }
            }

        } else {
            // if the press was outside the graph area, deselect:
            selection = null;
        }

        if (selection == null)
        {
            selectionWidget.setText(NO_SELECTION_TXT);
        }
        else
        {

            title = selection.second.getTitle();//tar or achv
//            Number y = selection.second.getY(selection.first);
            Number x = selection.second.getX(selection.first);
            chosenMonth = xLabels.get((Integer) x);
            Number y=0;
            if(title.equalsIgnoreCase("tar"))
            {
                y=targetList.get((Integer) x);
            }
            else
            {

                y=achievementList.get((Integer) x);
            }
            String chosenItemCurrent = title + "-" + chosenMonth;
            if(chosenItemCurrent.matches(chosenItemPrevious) )
            {
                if((int)y>0)
                {
                    Intent intent=new Intent(mContext,SelfAppraisalDetailsListActivity.class);
                    intent.putExtra("type",currentTargetAchievementType);
                    int monthIndex=xLabels.indexOf(chosenMonth);
                    String finalMonthString=monthIndex+"";
                    if(finalMonthString.length()==1)
                    {
                        finalMonthString="0"+finalMonthString;
                    }
                    intent.putExtra("month", finalMonthString);
                    startActivity(intent);
                }

            }
            else
            {
                selectionWidget.setText(title + " For  "+ chosenMonth +": " + y);
                this.chosenItemPrevious = chosenItemCurrent;
            }

        }
        plot.redraw();
    }

    public void AddValuesToBarEntryLabels() {
        xValues = new ArrayList<>();
        xLabels = new ArrayList<>();
        xLabels.add("");
        xLabels.add("Jan");
        xLabels.add("Feb");
        xLabels.add("Mar");
        xLabels.add("Apr");
        xLabels.add("May");
        xLabels.add("June");
        xLabels.add("July");
        xLabels.add("Aug");
        xLabels.add("Sept");
        xLabels.add("Oct");
        xLabels.add("Nov");
        xLabels.add("Dec");
        xLabels.add("");
        for(int i=0;i<14;i++)
        {
            xValues.add(i);
        }


    }
    class GraphXLabelFormat extends Format {

        @Override
        public StringBuffer format(Object arg0, StringBuffer arg1, FieldPosition arg2) {
            // TODO Auto-generated method stub

            int parsedInt = Math.round(Float.parseFloat(arg0.toString()));
            Log.d("test", parsedInt + " " + arg1 + " " + arg2);
            String labelString = xLabels.get(parsedInt);
            arg1.append(labelString);
            return arg1;
        }

        @Override
        public Object parseObject(String arg0, ParsePosition arg1) {
            // TODO Auto-generated method stub
            return xLabels.indexOf(arg0);
        }
    }
    private void updatePlot()
    {
        try
        {

            // Remove all current series from each plot
            if(plot!=null)
                plot.clear();

            // Setup our Series with the selected number of elements
            series1 = new SimpleXYSeries(xValues,targetList , "Tar");
            series2 = new SimpleXYSeries(xValues,achievementList,"Achv");

            plot.setDomainBoundaries(0, series1.size(), BoundaryMode.AUTO);
            double boundary = SeriesUtils.minMax(series1, series2).getMaxY().doubleValue();
            plot.setRangeUpperBoundary(boundary, BoundaryMode.FIXED);
            plot.setDomainStep(StepMode.INCREMENT_BY_VAL,1);
            plot.setRangeStep(StepMode.INCREMENT_BY_VAL, boundary/3);

            // add a new series' to the xyplot:
//        plot.addSeries(series1, formatterTarget);
//        plot.addSeries(series2, formatterAchievement);
            final ScalingXYSeries scalingSeries1 = new ScalingXYSeries(series1, 0, ScalingXYSeries.Mode.Y_ONLY);
            plot.addSeries(scalingSeries1, formatterTarget);

            final ScalingXYSeries scalingSeries2 = new ScalingXYSeries(series2, 0, ScalingXYSeries.Mode.Y_ONLY);
            plot.addSeries(scalingSeries2, formatterAchievement);
            // Setup the BarRenderer with our selected options
            MyBarRenderer renderer = plot.getRenderer(MyBarRenderer.class);
            Object selectedItem1 = BarRenderer.BarOrientation.SIDE_BY_SIDE;
            renderer.setBarOrientation((BarRenderer.BarOrientation) selectedItem1);
            Object selectedItem = BarRenderer.BarGroupWidthMode.FIXED_WIDTH;

            final BarRenderer.BarGroupWidthMode barGroupWidthMode = (BarRenderer.BarGroupWidthMode) selectedItem;
            renderer.setBarGroupWidth(barGroupWidthMode, 130f);
            // animate a scale value from a starting val of 0 to a final value of 1:
            ValueAnimator animator = ValueAnimator.ofFloat(0, 1);

            // use an animation pattern that begins and ends slowly:
            animator.setInterpolator(new AccelerateDecelerateInterpolator());

            animator.addUpdateListener(new ValueAnimator.AnimatorUpdateListener() {
                @Override
                public void onAnimationUpdate(ValueAnimator valueAnimator) {
                    double scale = valueAnimator.getAnimatedFraction();
                    scalingSeries1.setScale(scale);
                    scalingSeries2.setScale(scale);
                    plot.redraw();
                }
            });
            animator.addListener(new Animator.AnimatorListener() {
                @Override
                public void onAnimationStart(Animator animator) {

                }

                @Override
                public void onAnimationEnd(Animator animator) {
                    plot.redraw();
                }

                @Override
                public void onAnimationCancel(Animator animator) {

                }

                @Override
                public void onAnimationRepeat(Animator animator) {

                }
            });

            // the animation will run for 1.5 seconds:
            animator.setDuration(1500);
            animator.start();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }


    public void prepareTargetAchievementList() {
        targetList = new ArrayList<>();
        achievementList = new ArrayList<>();
        targetList.add(0);
        achievementList.add(0);
        ArrayList<SelfAppraisalDetailsCustomerWise> targetListFromDB = new ArrayList<>();
        Log.d("TAG", "prepareTargetAchievementList: currentTargetAchievementType");
        if (currentTargetAchievementType.toLowerCase().matches("customer wise"))
        {
            targetListFromDB = mAceDnsDatabase.getTargetForAllMonths("self_appraisal_customer_wise");
        } else if (currentTargetAchievementType.matches("Branch wise")) {
            targetListFromDB = mAceDnsDatabase.getTargetForAllMonths("self_appraisal_branch_wise");
        } else if (currentTargetAchievementType.matches("Product group wise")) {
            targetListFromDB = mAceDnsDatabase.getTargetForAllMonths("self_appraisal_productgroup_wise");
        }

        int i = 0;
        while (i < targetListFromDB.size())
        {
            String gettarget = targetListFromDB.get(i).gettarget();
            int parsedInttar = Math.round(Float.parseFloat(gettarget));
            targetList.add(Integer.valueOf(parsedInttar));
            String getachievement = targetListFromDB.get(i).getachievement();
            int parsedIntach = Math.round(Float.parseFloat(getachievement));
            achievementList.add(Integer.valueOf(parsedIntach));
            i++;
        }
        targetList.add(0);
        achievementList.add(0);
    }

    public void prepareTargetAchievementListCustomerEmpWise() {
        targetList = new ArrayList<>();
        achievementList = new ArrayList<>();
        targetList.add(0);
        achievementList.add(0);
        ArrayList<SelfAppraisalDetailsCustomerWise> targetListFromDB = new ArrayList<>();
        if(currentTargetAchievementType.toLowerCase().matches("customer wise"))
        {
            targetListFromDB = mAceDnsDatabase.getTargetForAllMonthsCustomerWise("self_appraisal_summary", Constants.selectedCustomer.getCustomerCode(),chosenVertical);
        }
        else
        {
            targetListFromDB = mAceDnsDatabase.getTargetForAllMonthsCustomerWise("self_appraisal_emp_wise", Constants.selectedCustomer.getCustomerCode(),chosenVertical);
        }

        int i = 0;
        while (i < targetListFromDB.size()) {
            String gettarget = targetListFromDB.get(i).gettarget();
            int target= Integer.parseInt(gettarget);
            targetList.add(target);
            achievementList.add(Integer.valueOf(targetListFromDB.get(i).getachievement()));
            i++;
        }
        targetList.add(0);
        achievementList.add(0);

    }

    public void prepareTargetAchievementListProdGrpWise()
    {
        runOnUiThread(new Runnable() {

            @Override
            public void run() {

                // Stuff that updates the UI
                try
                {
                    infoLayout.setVisibility(View.GONE);
                    targetList = new ArrayList<>();
                    achievementList = new ArrayList<>();
                    targetList.add(0);
                    achievementList.add(0);
                    ArrayList<SelfAppraisalDetailsCustomerWise> targetListFromDB = new ArrayList<>();
//            targetListFromDB = mAceDnsDatabase.getTargetForAllMonthsProdGrpWise("self_appraisal_summary", Constants.selectedCustomer.getCustomerCode(),chosenVertical);
                    targetListFromDB = mAceDnsDatabase.getTargetForAllMonths("self_appraisal_productgroup_wise");

                    int i = 0;
                    while (i < targetListFromDB.size()) {
                        String gettarget = targetListFromDB.get(i).gettarget();
                        int target= Integer.parseInt(gettarget);
                        targetList.add(target);
                        achievementList.add(Integer.valueOf(targetListFromDB.get(i).getachievement()));
                        i++;
                    }
                    targetList.add(0);
                    achievementList.add(0);
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }
            }
        });

    }

    @Override
    public boolean onKeyUp(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            finish();
        }
        return super.onKeyUp(keyCode, event);
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }
    }

    public void finishCurrentActivity(View v) {
        finish();
    }

}
