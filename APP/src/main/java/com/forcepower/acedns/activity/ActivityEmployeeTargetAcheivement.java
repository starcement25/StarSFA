package com.forcepower.acedns.activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import com.github.mikephil.charting.animation.Easing;
import com.github.mikephil.charting.charts.PieChart;
import com.github.mikephil.charting.components.Legend;
import com.github.mikephil.charting.components.Legend.LegendPosition;
import com.github.mikephil.charting.data.Entry;
import com.github.mikephil.charting.data.PieData;
import com.github.mikephil.charting.data.PieDataSet;
import com.github.mikephil.charting.formatter.PercentFormatter;
import com.github.mikephil.charting.highlight.Highlight;
import com.github.mikephil.charting.listener.OnChartValueSelectedListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.EmployeeAdapter;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.EmployeeTargetJCP;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class ActivityEmployeeTargetAcheivement extends AceDnsParentActivity implements OnChartValueSelectedListener, OnClickListener {

    private static PieChart mChart1 = null;
    private static PieChart mChart2 = null;

    private static TextView mTextViewMonthlySales = null;
    private static TextView mTextViewYearlySales = null;

    private static TextView textViewRetailerValue = null;
    private static TextView textViewSSValue = null;
    private static TextView mTextViewDealer = null;
    private static TextView mTextViewSubDealer = null;
    private static TextView mTextViewIHB = null;
    private static TextView mTextViewOther = null;
    private static TextView mTextViewAchieved = null;

    private static ImageView mImageViewMonthly = null;
    private static ImageView mImageViewYearly = null;
    private static ImageView mImageViewHeaderLogo = null;

    private static Button mButtonSelectEmployee = null;

    private static Button mButtonBack = null;

    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    ArrayList<KeyValue> mKeyValueSalesList;
    ArrayList<KeyValue> mKeyValueCollectionList;
    EmployeeTargetJCP mEmployeeTargetJCPAchieved = null;
    SimpleDateFormat dateFormat;
    Date date = new Date();
    ArrayList<EmployeeMasterDetails> mEmployeeMasterDetailsList;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterLowerLeavesDetails;
    private AceDnsDatabase mAceDnsDatabase;
    private Context mContext;
    private ProgressDialog mProgressDialog;
    private Handler mReportHandler;
    private Typeface mTypeface;
    private double mMonthlyValue = 0;
    private double mYearlyValue = 0;
    private String mToday = "";
    private String mCurrentMonth = "";
    private String mCondition = "";
    private String mEmployeeCode = "";
    int firstTimeLodaingFlag=0;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.actvity_emp_target_acheivment);
        RegisterActivities.registerActivity(this);

        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        mToday = dateFormat.format(date);
        mCurrentMonth = mToday.substring(3);

        mContext = ActivityEmployeeTargetAcheivement.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        InitializeView();

        mReportHandler = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                switch (job) {
                    case 1:
                        SetJCPData();
                        PercentageCalculator();
                        SetPieChartData();
                        break;
                    case 2:

                        if (mEmployeeMasterDetailsList.size() > 0) {
                            if (mEmployeeMasterDetailsList.size() == 1) {
                                mEmployeeCode = mEmployeeMasterDetailsList.get(0).getEmpCode();
                                mButtonSelectEmployee.setText(mEmployeeMasterDetailsList.get(0).getEmpName());
                                FetchSaudaTransactionLogData(1, mEmployeeCode);
                            } else {
                                ShowEmployeeDialog();
                            }
                        } else {
                            Utils.showToast(mContext, "No employee report to you");
                        }
                        break;
                }
            }
        };

        mButtonSelectEmployee.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                FetchSaudaTransactionLogData(2, mEmployeeCode);
            }
        });

        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        mEmployeeCode = Constants.employeeDetailObject.getEmpCode();
        FetchSaudaTransactionLogData(1, mEmployeeCode);
    }

    @Override
    public void onValueSelected(Entry e, int dataSetIndex, Highlight h) {

    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    @Override
    public void onNothingSelected() {

    }

    private void SetJCPData() {

        mTextViewDealer.setText(mEmployeeTargetJCPAchieved.getDealer() + "/2");
        mTextViewSubDealer.setText(mEmployeeTargetJCPAchieved.getSubDealer() + "/2");
        mTextViewIHB.setText(mEmployeeTargetJCPAchieved.getIHB() + "/3");
        mTextViewOther.setText(mEmployeeTargetJCPAchieved.getOthers() + "/1");
        textViewSSValue.setText(mEmployeeTargetJCPAchieved.getSS() + "/1");
        textViewRetailerValue.setText(mEmployeeTargetJCPAchieved.getRetailer() + "/35");
        mTextViewAchieved.setText(mEmployeeTargetJCPAchieved.getAchievement() + "/38");
    }

    private void PercentageCalculator() {
        mTextViewMonthlySales.setText(defaultFormat.format(mMonthlyValue));
        mTextViewYearlySales.setText(defaultFormat.format(mYearlyValue));

        if (mMonthlyValue <= 50) {
            mImageViewMonthly.setImageDrawable(getResources().getDrawable(R.drawable.reds));
        } else if (mMonthlyValue > 80) {
            mImageViewMonthly.setImageDrawable(getResources().getDrawable(R.drawable.greens));
        } else {
            mImageViewMonthly.setImageDrawable(getResources().getDrawable(R.drawable.yellow));
        }

        if (mYearlyValue <= 50) {
            mImageViewYearly.setImageDrawable(getResources().getDrawable(R.drawable.reds));
        } else if (mYearlyValue > 80) {
            mImageViewYearly.setImageDrawable(getResources().getDrawable(R.drawable.greens));
        } else {
            mImageViewYearly.setImageDrawable(getResources().getDrawable(R.drawable.yellow));
        }
    }

    private void InitializeView() {

        mTextViewMonthlySales = (TextView) findViewById(R.id.textViewMonthValue);
        mTextViewYearlySales = (TextView) findViewById(R.id.textViewYearlyValue);

        textViewRetailerValue = (TextView) findViewById(R.id.textViewRetailerValue);
        textViewSSValue = (TextView) findViewById(R.id.textViewSSValue);
        mTextViewDealer = (TextView) findViewById(R.id.textViewBargainNumber);
        mTextViewSubDealer = (TextView) findViewById(R.id.textViewSubDealerValue);
        mTextViewIHB = (TextView) findViewById(R.id.textViewIHBValue);
        mTextViewIHB.setVisibility(View.GONE);
        mTextViewOther = (TextView) findViewById(R.id.textViewOtherValue);
        mTextViewAchieved = (TextView) findViewById(R.id.textViewAcheivedValue);

        mImageViewMonthly = (ImageView) findViewById(R.id.imageViewMonthly);
        mImageViewYearly = (ImageView) findViewById(R.id.imageViewYearly);
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonSelectEmployee = (Button) findViewById(R.id.btnSelectEmployee);

        // Chart1 Initialization
        mChart1 = (PieChart) findViewById(R.id.chart1);
        mChart1.setUsePercentValues(true);
        mChart1.setDescription("");
        mChart1.setExtraOffsets(5, 10, 5, 5);

        mChart1.setDragDecelerationFrictionCoef(0.95f);

        mTypeface = Typeface.createFromAsset(getAssets(), "OpenSans-Regular.ttf");
        mChart1.setCenterTextTypeface(Typeface.createFromAsset(getAssets(), "OpenSans-Light.ttf"));
        //mChart1.setCenterText("Sales MTD");

        mChart1.setDrawHoleEnabled(false);
        mChart1.setHoleColorTransparent(true);

        mChart1.setTransparentCircleColor(Color.WHITE);
        mChart1.setTransparentCircleAlpha(110);

        mChart1.setHoleRadius(58f);
        mChart1.setTransparentCircleRadius(61f);

        mChart1.setDrawCenterText(true);
        mChart1.setRotationAngle(0);
        mChart1.setRotationEnabled(true);
        mChart1.setHighlightPerTapEnabled(true);


        mChart1.setOnChartValueSelectedListener(this);

        mChart1.setDrawSliceText(false);
        mChart1.animateY(1400, Easing.EasingOption.EaseInOutQuad);

        Legend l = mChart1.getLegend();
        l.setPosition(LegendPosition.LEFT_OF_CHART);
        l.setXEntrySpace(8f);
        l.setYEntrySpace(1f);
        l.setTextSize(10f);
        l.setYOffset(0f);

        // Chart2 Initialization

        mChart2 = (PieChart) findViewById(R.id.chart2);
        mChart2.setUsePercentValues(true);
        mChart2.setDescription("");
        mChart2.setExtraOffsets(5, 10, 5, 5);

        mChart2.setDragDecelerationFrictionCoef(0.95f);

        mChart2.setCenterTextTypeface(Typeface.createFromAsset(getAssets(), "OpenSans-Light.ttf"));
        //mChart2.setCenterText("Collection MTD");

        mChart2.setDrawHoleEnabled(false);
        mChart2.setHoleColorTransparent(true);

        mChart2.setTransparentCircleColor(Color.WHITE);
        mChart2.setTransparentCircleAlpha(110);

        mChart2.setHoleRadius(58f);
        mChart2.setTransparentCircleRadius(61f);

        mChart2.setDrawCenterText(true);
        mChart2.setRotationAngle(0);
        mChart2.setRotationEnabled(true);
        mChart2.setHighlightPerTapEnabled(true);


        mChart2.setOnChartValueSelectedListener(this);

        mChart2.setDrawSliceText(false);
        mChart2.animateY(1400, Easing.EasingOption.EaseInOutQuad);

        Legend l2 = mChart2.getLegend();
        l2.setPosition(LegendPosition.LEFT_OF_CHART);
        l2.setXEntrySpace(8f);
        l2.setYEntrySpace(1f);
        l2.setTextSize(10f);
        l2.setYOffset(0f);

    }

    private void SetPieChartData() {
        // Set Chart1 Data
        ArrayList<Entry> yAxisVals1 = new ArrayList<Entry>();
        ArrayList<String> xAxisVals1 = new ArrayList<String>();

        int count = mKeyValueSalesList.size();
        for (int i = 0; i < count; i++) {
            double value = 0;
            if (mKeyValueSalesList.get(i).getValue() != null &&
                    mKeyValueSalesList.get(i).getValue().trim().length() > 0) {
                value = Double.parseDouble(mKeyValueSalesList.get(i).getValue());
            }
            yAxisVals1.add(new Entry((float) Math.round(value), i));
            xAxisVals1.add(mKeyValueSalesList.get(i).getKey().trim() + " (" + Math.round(value) + ")");
        }

        PieDataSet dataSet1 = new PieDataSet(yAxisVals1, "");
        dataSet1.setSliceSpace(3f);
        dataSet1.setSelectionShift(3f);
        dataSet1.setValueTextSize(10f);

        ArrayList<Integer> colors1 = new ArrayList<Integer>();
        colors1.add(Color.rgb(78, 127, 187));
        colors1.add(Color.rgb(190, 79, 76));
        dataSet1.setColors(colors1);
        PieData data1 = new PieData(xAxisVals1, dataSet1);
        data1.setValueFormatter(new PercentFormatter(new DecimalFormat("##.##")));
        data1.setValueTextSize(12f);
        data1.setValueTextColor(Color.BLACK);
        data1.setValueTypeface(mTypeface);
        mChart1.setData(data1);
        mChart1.highlightValues(null);
        mChart1.invalidate();

        // Set Chart2 Data

        ArrayList<Entry> yAxisVals2 = new ArrayList<Entry>();
        ArrayList<String> xAxisVals2 = new ArrayList<String>();

        count = mKeyValueCollectionList.size();
        for (int i = 0; i < count; i++) {
            double value = 0;
            if (mKeyValueCollectionList.get(i).getValue() != null &&
                    mKeyValueCollectionList.get(i).getValue().trim().length() > 0) {
                value = Double.parseDouble(mKeyValueCollectionList.get(i).getValue());
            }
            yAxisVals2.add(new Entry((float) Math.round(value), i));
            xAxisVals2.add(mKeyValueCollectionList.get(i).getKey().trim() + " (" + Math.round(value) + ")");
        }

        PieDataSet dataSet2 = new PieDataSet(yAxisVals2, "");
        dataSet2.setSliceSpace(3f);
        dataSet2.setSelectionShift(3f);
        dataSet2.setValueTextSize(10f);

        ArrayList<Integer> colors2 = new ArrayList<Integer>();
        colors2.add(Color.rgb(78, 127, 187));
        colors2.add(Color.rgb(190, 79, 76));
        dataSet2.setColors(colors2);
        PieData data2 = new PieData(xAxisVals2, dataSet2);
        data2.setValueFormatter(new PercentFormatter(new DecimalFormat("##.##")));
        data2.setValueTextSize(12f);
        data2.setValueTextColor(Color.BLACK);
        data2.setValueTypeface(mTypeface);
        mChart2.setData(data2);
        mChart2.highlightValues(null);
        mChart2.invalidate();
    }

    @Override
    public void onClick(View v) {

    }

    public void ShowConditionofATA(final String empcode) {
        final Dialog dialgoCondition = new Dialog(ActivityEmployeeTargetAcheivement.this, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_mis);
        TextView txtMsg = (TextView) dialgoCondition
                .findViewById(R.id.title);
        txtMsg.setText("Select an option.");
        final RadioGroup radioSelectionGroup = (RadioGroup) dialgoCondition.findViewById(R.id.radioSelect);

        radioSelectionGroup.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {

            public void onCheckedChanged(RadioGroup group, int checkedId) {
                RadioButton radioSelection = (RadioButton) dialgoCondition.findViewById(checkedId);

                if (radioSelection.getText().equals("Self")) {
                    FetchSaudaTransactionLogData(1, empcode);
                } else {
                    FetchSaudaTransactionLogData(2, empcode);
                }
                dialgoCondition.cancel();
            }
        });
        dialgoCondition.show();
    }

    public void ShowEmployeeDialog() {
        EmployeeAdapter adapter = new EmployeeAdapter(ActivityEmployeeTargetAcheivement.this, R.layout.customer_broker_list_child, mEmployeeMasterDetailsList);
        final Dialog dialogEmployeeList = new Dialog(ActivityEmployeeTargetAcheivement.this, R.style.PauseDialog);
        dialogEmployeeList.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogEmployeeList.setContentView(R.layout.select_from_list);
        dialogEmployeeList.setCancelable(false);
        TextView title = (TextView) dialogEmployeeList.findViewById(R.id.title);
        title.setText("Please select an employee");

        ListView list = (ListView) dialogEmployeeList.findViewById(R.id.list);
        list.setAdapter(adapter);
        list.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int pos, long arg3) {

                EmployeeMasterDetails obj = mEmployeeMasterDetailsList.get(pos);
                mButtonSelectEmployee.setText(obj.getEmpName());
                ShowConditionofATA(obj.getEmpCode().trim());
                dialogEmployeeList.cancel();
            }
        });

        Button cancel = (Button) dialogEmployeeList.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        dialogEmployeeList.show();

    }

    private void FetchSaudaTransactionLogData(final int whattodo, final String empcode) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {

                    case 1:

                        String lowerleaves = mAceDnsDatabase.GetLowerleavesOfEmployee(empcode);
                        if (lowerleaves.trim().length() > 1) {
                            mKeyValueSalesList = mAceDnsDatabase.GetEmployeeTargetVolume(lowerleaves, "MULTIPLE");
                            mKeyValueCollectionList = mAceDnsDatabase.GetEmployeeTargetCollection(lowerleaves, "MULTIPLE");

                            //mCondition="month='"+mCurrentMonth+"'";
                            mCondition = "SUBSTR(month,4,7)='" + mCurrentMonth + "'";
                            mMonthlyValue = mAceDnsDatabase.GetEmployeeTargetVolume(mCondition, lowerleaves, "MULTIPLE");

                            mCondition = "month='" + mToday + "'";
                            mYearlyValue = mAceDnsDatabase.GetEmployeeTargetVolume(mCondition, lowerleaves, "MULTIPLE");

                            mCondition = Constants.dateString;




                        } else {
                            mKeyValueSalesList = mAceDnsDatabase.GetEmployeeTargetVolume(empcode, "SINGLE");
                            mKeyValueCollectionList = mAceDnsDatabase.GetEmployeeTargetCollection(empcode, "SINGLE");

                            //mCondition="month='"+mCurrentMonth+"'";
                            mCondition = "SUBSTR(month,4,7)='" + mCurrentMonth + "'";
                            mMonthlyValue = mAceDnsDatabase.GetEmployeeTargetVolume(mCondition, empcode, "SINGLE");

                            mCondition = "month='" + mToday + "'";
                            mYearlyValue = mAceDnsDatabase.GetEmployeeTargetVolume(mCondition, empcode, "SINGLE");

                            mCondition = Constants.dateString;

                        }
                        if(firstTimeLodaingFlag==0)
                        {
                            mEmployeeTargetJCPAchieved = mAceDnsDatabase.GetEmployeeTargetJCP("" );
                            firstTimeLodaingFlag++;
                        }
                        else
                        {
                            mEmployeeTargetJCPAchieved = mAceDnsDatabase.GetEmployeeTargetJCP(" AND emp_code in("+lowerleaves+")" );
                        }
                        break;

                    case 2:
                        //mEmployeeMasterDetailsList=mAceDnsDatabase.GetEmployeeForETA(empcode);

                        mEmployeeMasterLowerLeavesDetails = mAceDnsDatabase.GetHierarchyEmployeeDetailsWithOutVertical(empcode);
                        if (mEmployeeMasterLowerLeavesDetails.size() > 0) {
                            mEmployeeMasterDetailsList = new ArrayList<EmployeeMasterDetails>();
                            EmployeeMasterDetails obj = null;
                            for (int count = 0; count < mEmployeeMasterLowerLeavesDetails.size(); count++) {
                                String emp_code = mEmployeeMasterLowerLeavesDetails.get(count).getEmpCode();
								/*mKeyValueSalesList=mAceDnsDatabase.GetEmployeeTargetVolume(emp_code);
								if(mKeyValueSalesList.size()>0){

								}*/
                                obj = new EmployeeMasterDetails();
                                obj.setEmpCode(mEmployeeMasterLowerLeavesDetails.get(count).getEmpCode());
                                obj.setEmpName(mEmployeeMasterLowerLeavesDetails.get(count).getEmpName());
                                //obj.setVerticalValue(quantity);
                                mEmployeeMasterDetailsList.add(obj);
                                obj = null;
                            }
                        } else {
                            //mEmployeeMasterDetails=mAceDnsDatabaseHelper.GetSTLEmployeeDetails(mQuery);
                            mEmployeeMasterDetailsList = new ArrayList<EmployeeMasterDetails>();
                        }


                        break;

                    case 3:


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
