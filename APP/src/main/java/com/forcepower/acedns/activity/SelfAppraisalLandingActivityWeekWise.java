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

import androidx.core.content.ContextCompat;

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
import android.widget.ListView;
import android.widget.Spinner;
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
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.SelfAppraisalDetails;
import com.forcepower.acedns.bean.SelfAppraisalDetailsCustomerWise;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.text.FieldPosition;
import java.text.Format;
import java.text.ParsePosition;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import static com.forcepower.acedns.constants.Constants.selectedMonth;
import static com.forcepower.acedns.constants.Constants.selectedMonthAchv;
import static com.forcepower.acedns.constants.Constants.selectedMonthTarget;

public class SelfAppraisalLandingActivityWeekWise extends AceDnsParentActivity {
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
    ImageView imgLogo;
    SelfAppraisalDetails selfAppraisalSetup;
    ArrayAdapter<String> spinnerArrayAdapter;
    ArrayList<CustomerDetails> customerList;
    ArrayList<String> spinnerArrayCustomers;
    ArrayList<String> spinnerArrayMonths;
    ArrayList<String> spinnerArrayVerticals;
    public static String chosenVertical="";
    String chosenWeek = "";
    String title = "";//tar or achv
    private XYSeries series1;
    private XYSeries series2;
    String chosenItemPrevious ="";

    TextView targetTV;
    TextView achvTv;
    private MyBarFormatter formatterTarget;

    private MyBarFormatter formatterAchievement;

    private MyBarFormatter selectionFormatter;

    private TextLabelWidget selectionWidget;

    private Pair<Integer, XYSeries> selection;
    private ProgressDialog mPrepareSurveyMenuProgressDialog;
    private Handler mPrepareSurveyMenuHandler;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_self_appraisal_landing_weekwise);
        chosenVertical="";
        mContext = this;
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        colorList = new ArrayList<>();
//        ArrayList<String> categories = getSelfAppraisalCatagories();
//        if(categories.size()==1)
//        {
//            currentTargetAchievementType = categories.get(0);
//            MakeTargetAchivementDataLoadingProcess();
//        }
//        else
//        {
//            selectTargetActualType(categories);
//        }

        targetTV = findViewById(R.id.targetTV);
        achvTv = findViewById(R.id.achvTv);
        mPrepareSurveyMenuHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SelfAppraisalLandingActivityWeekWise.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                updatePlot();
                                break;

                        }

                    }
                });
            }
        };
        createChart();
        showMonthSpinner();
        showEmpSpinner();
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
            return BarOrientation.SIDE_BY_SIDE;
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
    public void showMonthSpinner() {
        final Spinner customerSpinner = (Spinner) findViewById(R.id.customerSpinner);
        spinnerArrayMonths=new ArrayList<>();
        spinnerArrayMonths.add("Jan");
        spinnerArrayMonths.add("Feb");
        spinnerArrayMonths.add("Mar");
        spinnerArrayMonths.add("Apr");
        spinnerArrayMonths.add("May");
        spinnerArrayMonths.add("June");
        spinnerArrayMonths.add("July");
        spinnerArrayMonths.add("Aug");
        spinnerArrayMonths.add("Sept");
        spinnerArrayMonths.add("Oct");
        spinnerArrayMonths.add("Nov");
        spinnerArrayMonths.add("Dec");
        spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArrayMonths);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        customerSpinner.setAdapter(spinnerArrayAdapter);

        customerSpinner.setSelection(0);
        customerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
            {
                targetList = new ArrayList<>();
                achievementList = new ArrayList<>();
                chosenWeek =spinnerArrayMonths.get(i);
                selectedMonth=convertMonthSteingToInt(spinnerArrayMonths.get(i));
                dataLoadingProcessForweekEMpWiseTargetAchievement();

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    public void showEmpSpinner() {
        final Spinner customerSpinner = (Spinner) findViewById(R.id.empSpinner);
        final ArrayList<EmployeeMasterDetails> employeeMasterDetailsList = mAceDnsDatabase.GetEmployeeListForWeekWiseTargetAchievement();
        ArrayList spinnerArrayEmp =new ArrayList<>();
        for(int i=0;i<employeeMasterDetailsList.size();i++)
        {
            spinnerArrayEmp.add(employeeMasterDetailsList.get(i).getEmpName());
        }

        spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArrayEmp);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        customerSpinner.setAdapter(spinnerArrayAdapter);

        customerSpinner.setSelection(0);
        customerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
            {
                targetList = new ArrayList<>();
                achievementList = new ArrayList<>();
                Constants.selectedEmpCode=employeeMasterDetailsList.get(i).getEmpCode();

                dataLoadingProcessForweekEMpWiseTargetAchievement();

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    public void dataLoadingProcessForweekEMpWiseTargetAchievement() {
        ArrayList<SelfAppraisalDetailsCustomerWise> targetListFromDB=mAceDnsDatabase.getWeeklyTargetForChosenMonth();

        if(targetListFromDB.size()>0)
        {
            int i2 = 0;
            while (i2 < targetListFromDB.size())
            {
                String gettarget = targetListFromDB.get(i2).gettarget();
                int parsedInttar = Math.round(Float.parseFloat(gettarget));
                targetList.add(Integer.valueOf(parsedInttar));
                String getachievement = targetListFromDB.get(i2).getachievement();
                int parsedIntach = Math.round(Float.parseFloat(getachievement));
                achievementList.add(Integer.valueOf(parsedIntach));

                i2++;
            }
            targetTV.setText("Total Target : "+selectedMonthTarget);
            achvTv.setText("Total Achievement : "+selectedMonthAchv);
            updatePlot();
        }
//        else
//        {
//          Utils.showToast(mContext,"No data found for chosen month.");
//        }
    }

    public String convertMonthSteingToInt(String input2)
    {
        switch(input2) {
            case "January":
            case "Jan":
                input2 = "01";
                break;

            case "Febuary":
            case "Feb":
                input2 = "02";
                break;

            case "March":
            case "Mar":
                input2 = "03";
                break;

            case "April":
            case "Apr":
                input2 = "04";
                break;

            case "May":
                input2 = "05";
                break;

            case "June":
            case "Jun":
                input2 = "06";
                break;

            case "July":
            case "Jul":
                input2 = "07";
                break;

            case "August":
            case "Aug":
                input2 = "08";
                break;

            case "September":
            case "Sep":
            case "Sept":
                input2 = "09";
                break;

            case "October":
            case "Oct":
                input2 = "10";
                break;

            case "November":
            case "Nov":
                input2 = "11";
                break;

            case "December":
            case "Dec":
                input2 = "12";
                break;
        }
        return input2;
    }
    public void ShowVerticalSelectDialog( )
    {
        generateDataInVerticalList();
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

    public void generateDataIncustomerOrEmpList() {
        customerList = mAceDnsDatabase.getCustomerListFromSelfAppraisalSummary();
        spinnerArrayCustomers = new ArrayList<>();
        for (int i = 0; i < customerList.size(); i++) {
            spinnerArrayCustomers.add(customerList.get(i).getCustomerName());
        }
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
//        prepareTargetAchievementListCustomerEmpWise();
        PrepareSelfAppraisalMenuData(1);
//        updatePlot();
    }
    public void PrepareSelfAppraisalMenuData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Please wait..");
        mPrepareSurveyMenuProgressDialog.setCancelable(false);
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        prepareTargetAchievementListCustomerEmpWise();
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
//        plot.setLinesPerRangeLabel(1);
        plot.setRangeLowerBoundary(0, BoundaryMode.FIXED);
        plot.getGraph().setBackgroundPaint(null);
        plot.getGraph().setGridBackgroundPaint(null);
        plot.getGraph().setDomainGridLinePaint(null);
        plot.getGraph().setRangeGridLinePaint(null);

        plot.getGraph().setPaddingLeft(150);
//        plot.setLinesPerDomainLabel(1);
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
            chosenWeek = xLabels.get((Integer) x);
            Number y=0;
            if(title.equalsIgnoreCase("tar"))
            {
                y=targetList.get((Integer) x);
            }
            else
            {

                y=achievementList.get((Integer) x);
            }
            String chosenItemCurrent = title + "-" + chosenWeek;
            if(chosenItemCurrent.matches(chosenItemPrevious) )
            {
                if((int)y>0)
                {
                    Intent intent=new Intent(mContext,SelfAppraisalDetailsListActivityWeekWise.class);
                    intent.putExtra("week", chosenWeek);
                    intent.putExtra("month", selectedMonth);
                    startActivity(intent);
                }

            }
            else
            {
                selectionWidget.setText(title + " For  "+ chosenWeek +": " + y);
                this.chosenItemPrevious = chosenItemCurrent;
            }

        }
        plot.redraw();
    }

    public void AddValuesToBarEntryLabels() {
        xValues = new ArrayList<>();
        xLabels = new ArrayList<>();
        xLabels.add("");
        xLabels.add("Week 1");
        xLabels.add("Week 2");
        xLabels.add("Week 3");
        xLabels.add("Week 4");
        xLabels.add("");
        for(int i=0;i<6;i++)
        {
            xValues.add(i);
        }


    }
    class GraphXLabelFormat extends Format {

        @Override
        public StringBuffer format(Object arg0, StringBuffer arg1, FieldPosition arg2) {
            // TODO Auto-generated method stub

            int parsedInt = Math.round(Float.parseFloat(arg0.toString()));
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
        // Remove all current series from each plot
        try
        {
            plot.clear();

        }
        catch (Exception e)
        {

        }

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
        renderer.setBarGroupWidth(barGroupWidthMode, 210f);
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


    public void prepareTargetAchievementList() {
        targetList = new ArrayList<>();
        achievementList = new ArrayList<>();
        ArrayList<SelfAppraisalDetailsCustomerWise> targetListFromDB = new ArrayList<>();


        if (currentTargetAchievementType.matches("Customer wise"))
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
            targetList.add(Integer.valueOf(targetListFromDB.get(i).gettarget()));
            achievementList.add(Integer.valueOf(targetListFromDB.get(i).getachievement()));

            i++;
        }
    }

    public void prepareTargetAchievementListCustomerEmpWise() {
        targetList = new ArrayList<>();
        achievementList = new ArrayList<>();
        ArrayList<SelfAppraisalDetailsCustomerWise> targetListFromDB = new ArrayList<>();
        if(currentTargetAchievementType.matches("Customer wise"))
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

