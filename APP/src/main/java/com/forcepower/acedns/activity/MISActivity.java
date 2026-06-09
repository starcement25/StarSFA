package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

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
import com.github.mikephil.charting.utils.ColorTemplate;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.MisSKUAdapter;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.bean.MisDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.MessageFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;


public class MISActivity extends FragmentActivity implements OnChartValueSelectedListener, OnClickListener {
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonEndDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmitDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonPrevious = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewShowDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHideDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewEndDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewVerticalValue = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewPackingSizeValue = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mEmployeeDetails = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutToday = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutMTD = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutCustom = null;
    @SuppressLint("StaticFieldLeak")
    public static RelativeLayout mRelativeLayoutDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mCustomDateLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static RadioGroup mRadioGroupType = null;

    public static PieChart mChart = null;

    public static String mType = "";
    public static String mDisplayType = "";
    public static String mMessage = "";
    public static int JOB = 0;
    public AceDnsDatabase mAceDnsDatabaseHelper;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    public int SELECTION = 0;
    public String mProductGroupCode = "";
    public String mProductGroupName = "";
    public String mEmployeeCode = "";
    public String mEmployeeName = "";
    public String mZoneName = "";
    public String mStateName = "";
    public String mPlantName = "";
    public String mBranchName = "";
    public String mBranchCode = "";
    public String mTime = "";
    public String mCurrentMonth = "";
    public String mCurrentYear = "";
    public String mStartDate = "";
    public String mEndDate = "";
    public String mFStartDate = "";
    public String mFEndDate = "";
    public String mToday = "";
    public String mQuery = "";
    public String mPackSize = "";
    public double mCaseTotal = 0;
    public double mMTTotal = 0;
    DecimalFormat defaultFormat = new DecimalFormat("0");
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ArrayList<ProductGroupDetails> mProductGroupDetails;
    ArrayList<KeyValue> mKeyValueZoneList;
    ArrayList<KeyValue> mKeyValueStateList;
    ArrayList<KeyValue> mKeyValuePlantList;
    ArrayList<MisDetails> mMisDetailsList;
    ArrayList<String> mEmpCodeList;
    ArrayList<String> mEmpNameList;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterDetails;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterLowerLeavesDetails;
    ArrayList<BranchMasterDetails> mBranchMasterDetails;
    private boolean isStartDate = false;
    private boolean isNextLevelValid = false;
    private String[] mVerticalValueList;
    private String[] mPackingSizeList;
    private Typeface mTypeface;

    @SuppressLint({"HandlerLeak","SetTextI18n","SimpleDateFormat"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_mis_report);
        RegisterActivities.registerActivity(this);

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyy-MM-dd");

        mToday = mDFormatBackEnd.format(currentDate);

        mTime = Constants.dateString;
        GETCurrentMonthYear(mTime);

        mContext = MISActivity.this;
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);

        mType = "sku";
        mDisplayType = "OIL CATEGORY";

        InitializeView();

        mRadioGroupType.setOnCheckedChangeListener((group, checkedId) -> {
                    RadioButton radioSelection =  findViewById(checkedId);
                    if (radioSelection.getText().equals("Oil Category")) {
                        mType = "sku";
                        JOB = 0;
                        mDisplayType = "OIL CATEGORY";
                        mEmployeeDetails.setVisibility(View.GONE);
                        mEmployeeDetails.setText("");
                        FetchSaudaTransactionLogData(7);
                    }
                    if (radioSelection.getText().equals("State")) {
                        mType = "state";
                        JOB = 0;
                        mDisplayType = "State";
                        mEmployeeDetails.setVisibility(View.GONE);
                        mEmployeeDetails.setText("");
                        FetchSaudaTransactionLogData(1);
                    }
                    if (radioSelection.getText().equals("Zone")) {
                        mType = "zone";
                        JOB = 0;
                        mDisplayType = "Zone";
                        mEmployeeDetails.setVisibility(View.GONE);
                        mEmployeeDetails.setText("");
                        FetchSaudaTransactionLogData(1);
                    }
                    if (radioSelection.getText().equals("Plant")) {
                        mType = "plant";
                        JOB = 0;
                        mDisplayType = "Plant";
                        mEmployeeDetails.setVisibility(View.GONE);
                        mEmployeeDetails.setText("");
                        FetchSaudaTransactionLogData(1);
                    }
                    if (radioSelection.getText().equals("Employee")) {
                        mType = "employee";
                        JOB = 0;
                        mDisplayType = "Employee";
                        mEmployeeDetails.setVisibility(View.VISIBLE);
                        mEmployeeDetails.setText("");
                        mEmployeeCode = Constants.employeeDetailObject.getEmpCode();
                        mEmployeeName = Constants.employeeDetailObject.getEmpName();
                        mEmpCodeList = new ArrayList<>();
                        mEmpCodeList.add(mEmployeeCode);
                        mEmpNameList = new ArrayList<>();
                        mEmpNameList.add(mEmployeeName);
                        FetchSaudaTransactionLogData(1);
                    }
                });

        mButtonBack.setOnClickListener(v -> finish());

        mButtonPrevious.setOnClickListener(v -> PreviousState());

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate = date;
                    mTextViewStartDate.setText(mDFormatFrontEnd.format(date));
                    mFStartDate = mDFormatFrontEnd.format(date);
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    mFEndDate = mDFormatFrontEnd.format(date);
                    mEndDate = mDFormatBackEnd.format(date);
                }
                dialogCaldroidFragment.dismiss();
            }
        };

        mReportHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                if (job != 2) {
                    JOB = job;
                }
                if (JOB == 1) {
                    if (mType.equalsIgnoreCase("employee")) {
                        if (mEmpCodeList.size() > 1) {
                            mButtonPrevious.setVisibility(View.VISIBLE);
                        } else {
                            mButtonPrevious.setVisibility(View.INVISIBLE);
                        }

                    } else {
                        mButtonPrevious.setVisibility(View.INVISIBLE);
                    }
                } else {
                    mButtonPrevious.setVisibility(View.VISIBLE);
                }

                MISActivity.this.runOnUiThread(() -> {
                    switch (job) {
                        case 1:
                            if (mType.equalsIgnoreCase("sku")) {
                                if (!mProductGroupDetails.isEmpty()) {
                                    SetPieChartData(1);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("employee")) {
                                if (!mEmployeeMasterDetails.isEmpty()) {
                                    SetPieChartData(1);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("state")) {
                                if (!mKeyValueStateList.isEmpty()) {
                                    SetPieChartData(1);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("plant")) {
                                if (!mKeyValuePlantList.isEmpty()) {
                                    SetPieChartData(1);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("zone")) {
                                if (!mKeyValueZoneList.isEmpty()) {
                                    SetPieChartData(1);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            break;
                        case 2:
                            if (!mMisDetailsList.isEmpty()) {
                                BuildMessage();
                                String footer = "";
                                if (mType.equalsIgnoreCase("sku")) {
                                    footer = mProductGroupName;
                                }
                                if (mType.equalsIgnoreCase("employee")) {
                                    footer = mEmployeeName + "->" + mProductGroupName;
                                }
                                if (mType.equalsIgnoreCase("state")) {
                                    footer = mStateName + "->" + mBranchName + "->" + mProductGroupName;
                                }
                                if (mType.equalsIgnoreCase("plant")) {
                                    footer = mPlantName + "->" + mBranchName + "->" + mProductGroupName;
                                }
                                if (mType.equalsIgnoreCase("zone")) {
                                    footer = mZoneName + "->" + mStateName + "->" + mBranchName + "->" + mProductGroupName;
                                }
                                ShowSKUSaudaMisListDialog(footer);
                            } else {
                                switch (SELECTION) {
                                    case 1:
                                        Toast.makeText(mContext, "No " + mDisplayType + " wise " + mProductGroupName + " record found today", Toast.LENGTH_LONG).show();
                                        break;
                                    case 2:
                                        Toast.makeText(mContext, "No " + mDisplayType + " wise " + mProductGroupName + " record found in this month", Toast.LENGTH_LONG).show();
                                        break;
                                    case 3:
                                        Toast.makeText(mContext, "No " + mDisplayType + " wise " + mProductGroupName + " record found from " + mFStartDate + " to " + mFEndDate, Toast.LENGTH_LONG).show();
                                        break;
                                }
                            }
                            break;
                        case 3:
                            if (mType.equalsIgnoreCase("employee")) {
                                if (!mProductGroupDetails.isEmpty()) {
                                    mEmpCodeList.add(mEmployeeCode);
                                    mEmpNameList.add(mEmployeeName);
                                    SetEmpdetails();
                                    SetPieChartData(3);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        JOB = 1;
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("state")) {
                                if (!mBranchMasterDetails.isEmpty()) {
                                    SetPieChartData(3);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("plant")) {
                                if (!mBranchMasterDetails.isEmpty()) {
                                    SetPieChartData(3);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("zone")) {
                                if (!mKeyValueStateList.isEmpty()) {
                                    SetPieChartData(3);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            break;
                        case 4:
                            if (mType.equalsIgnoreCase("zone")) {
                                if (!mBranchMasterDetails.isEmpty()) {
                                    SetPieChartData(4);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("plant")) {
                                if (!mProductGroupDetails.isEmpty()) {
                                    SetPieChartData(4);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            if (mType.equalsIgnoreCase("state")) {
                                if (!mProductGroupDetails.isEmpty()) {
                                    SetPieChartData(4);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            break;
                        case 5:
                            if (mType.equalsIgnoreCase("zone")) {
                                if (!mProductGroupDetails.isEmpty()) {
                                    SetPieChartData(5);
                                    if (mChart != null) {
                                        mChart.setVisibility(View.VISIBLE);
                                    }
                                } else {
                                    if (mChart != null) {
                                        mChart.setVisibility(View.INVISIBLE);
                                    }
                                    ClearAndShowToast();
                                }
                            }
                            break;
                        case 6:
                            if (mVerticalValueList.length > 1) {
                                ShowVericalValueListDialog();
                            } else if (mVerticalValueList.length == 1) {
                                Constants.mVerticalValue = mVerticalValueList[0];
                                mTextViewVerticalValue.setText("Vertical : " + Constants.mVerticalValue);
                                FetchSaudaTransactionLogData(7);
                            } else {
                                Toast.makeText(mContext, "No vertical found.\n Please Synchronize Data",Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 7:
                            if (mPackingSizeList.length > 1) {
                                ShowPackSizeDialog();
                            } else if (mPackingSizeList.length == 1) {
                                mPackSize = mPackingSizeList[0];
                                mTextViewPackingSizeValue.setText("Pack Size : " + mPackSize);
                                FetchSaudaTransactionLogData(1);
                            } else {
                                Toast.makeText(mContext, "No vertical found.\n Please Synchronize Data", Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 8:
                            if (isNextLevelValid) {
                                isNextLevelValid = false;
                                mEmpCodeList.add(mEmployeeCode);
                                mEmpNameList.add(mEmployeeName);
                                SetEmpdetails();
                                FetchSaudaTransactionLogData(1);
                            } else {
                                JOB = 1;
                                Toast.makeText(mContext, "No record found of " + mEmployeeName + " lower hierarchy", Toast.LENGTH_LONG).show();
                            }
                            break;
                    }
                });
            }
        };

        SELECTION = 1;
        mQuery = BuildQuery(SELECTION);
        FetchSaudaTransactionLogData(6);
    }

    @Override
    public void onValueSelected(Entry e, int dataSetIndex, Highlight h) {
        if (e == null)
            return;

        if (mType.equalsIgnoreCase("sku")) {
            if (!mProductGroupDetails.isEmpty()) {
                mProductGroupCode = mProductGroupDetails.get(e.getXIndex()).getGroupCode();
                mProductGroupName = mProductGroupDetails.get(e.getXIndex()).getGroupName();
                if (!mType.isEmpty()) {
                    FetchSaudaTransactionLogData(2);
                } else {
                    Utils.showToast(mContext, "Please select type");
                }
            }
        }

        if (mType.equalsIgnoreCase("employee")) {
            if (JOB == 1) {
                if (!mEmployeeMasterDetails.isEmpty()) {
                    mEmployeeCode = mEmployeeMasterDetails.get(e.getXIndex()).getEmpCode();
                    mEmployeeName = mEmployeeMasterDetails.get(e.getXIndex()).getEmpName();

                    ArrayList<EmployeeMasterDetails> emplist = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetails(mEmployeeCode);
                    if (!emplist.isEmpty()) {
                        ShowConditionofMis();
                    } else {
                        FetchSaudaTransactionLogData(3);
                    }
                }
            }
            if (JOB == 3) {
                if (!mProductGroupDetails.isEmpty()) {
                    mProductGroupCode = mProductGroupDetails.get(e.getXIndex()).getGroupCode();
                    mProductGroupName = mProductGroupDetails.get(e.getXIndex()).getGroupName();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(2);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }

        }

        if (mType.equalsIgnoreCase("state")) {

            if (JOB == 1) {
                if (!mKeyValueStateList.isEmpty()) {
                    mStateName = mKeyValueStateList.get(e.getXIndex()).getKey();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(3);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }

            if (JOB == 3) {
                if (!mBranchMasterDetails.isEmpty()) {
                    mBranchName = mBranchMasterDetails.get(e.getXIndex()).getBranchName();
                    mBranchCode = mBranchMasterDetails.get(e.getXIndex()).getBranchCode();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(4);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }

            if (JOB == 4) {
                if (!mProductGroupDetails.isEmpty()) {
                    mProductGroupCode = mProductGroupDetails.get(e.getXIndex()).getGroupCode();
                    mProductGroupName = mProductGroupDetails.get(e.getXIndex()).getGroupName();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(2);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }
        }

        if (mType.equalsIgnoreCase("plant")) {

            if (JOB == 1) {
                if (!mKeyValuePlantList.isEmpty()) {
                    mPlantName = mKeyValuePlantList.get(e.getXIndex()).getKey();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(3);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }

            if (JOB == 3) {
                if (!mBranchMasterDetails.isEmpty()) {
                    mBranchName = mBranchMasterDetails.get(e.getXIndex()).getBranchName();
                    mBranchCode = mBranchMasterDetails.get(e.getXIndex()).getBranchCode();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(4);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }

            if (JOB == 4) {
                if (!mProductGroupDetails.isEmpty()) {
                    mProductGroupCode = mProductGroupDetails.get(e.getXIndex()).getGroupCode();
                    mProductGroupName = mProductGroupDetails.get(e.getXIndex()).getGroupName();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(2);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }

        }

        if (mType.equalsIgnoreCase("zone")) {

            if (JOB == 1) {
                if (!mKeyValueZoneList.isEmpty()) {
                    mZoneName = mKeyValueZoneList.get(e.getXIndex()).getKey();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(3);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }
            if (JOB == 3) {
                if (!mKeyValueStateList.isEmpty()) {
                    mStateName = mKeyValueStateList.get(e.getXIndex()).getKey();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(4);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }
            if (JOB == 4) {
                if (!mBranchMasterDetails.isEmpty()) {
                    mBranchName = mBranchMasterDetails.get(e.getXIndex()).getBranchName();
                    mBranchCode = mBranchMasterDetails.get(e.getXIndex()).getBranchCode();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(5);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }

            }

            if (JOB == 5) {
                if (!mProductGroupDetails.isEmpty()) {
                    mProductGroupCode = mProductGroupDetails.get(e.getXIndex()).getGroupCode();
                    mProductGroupName = mProductGroupDetails.get(e.getXIndex()).getGroupName();
                    if (!mType.isEmpty()) {
                        FetchSaudaTransactionLogData(2);
                    } else {
                        Utils.showToast(mContext, "Please select type");
                    }
                }
            }
        }
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
    public void onNothingSelected() {}

    public void ClearAndShowToast() {
        switch (SELECTION) {
            case 1:
                if (mType.equalsIgnoreCase("employee")) {
                    Toast.makeText(mContext, mEmployeeName + " has no record found today", Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                }
                break;
            case 2:
                if (mType.equalsIgnoreCase("employee")) {
                    Toast.makeText(mContext, mEmployeeName + " has no record found in this month", Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                }
                break;
            case 3:
                if (mType.equalsIgnoreCase("employee")) {
                    Toast.makeText(mContext, mEmployeeName + " has no record found from " + mFStartDate + " to " + mFEndDate, Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(mContext, "No record found from " + mFStartDate + " to " + mFEndDate, Toast.LENGTH_LONG).show();
                }
                break;
        }
    }
    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        mImageViewHeaderLogo =  findViewById(R.id.imagelogo);

        TextView txtVersion =  findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(MISActivity.this) + "~" + Utils.getDBVersion(MISActivity.this));

        mImageViewShowDuration =  findViewById(R.id.image_clk);
        mImageViewHideDuration =  findViewById(R.id.image_go);

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);

        mRelativeLayoutDuration =  findViewById(R.id.duration_layout);
        mCustomDateLayout =  findViewById(R.id.custom_date_layout);


        mButtonBack =  findViewById(R.id.back);
        mButtonPrevious =  findViewById(R.id.buttonChartBack);
        mButtonPrevious.setVisibility(View.INVISIBLE);

        mFrameLayoutToday =  findViewById(R.id.btn_today);
        mFrameLayoutMTD =  findViewById(R.id.btn_mtd);
        mFrameLayoutCustom =  findViewById(R.id.btn_custom);

        mFrameLayoutToday.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);

        mTextViewStartDate =  findViewById(R.id.txt_start_date);
        mTextViewEndDate =  findViewById(R.id.txt_end_date);
        mTextViewVerticalValue =  findViewById(R.id.textViewVerticalValue);
        mTextViewPackingSizeValue =  findViewById(R.id.textViewPackSize);
        mTextViewDate =  findViewById(R.id.textViewDate);
        mEmployeeDetails =  findViewById(R.id.textViewEmployeeDetails);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");
        mTextViewVerticalValue.setText("");
        mTextViewPackingSizeValue.setText("");
        mTextViewDate.setText("");
        mEmployeeDetails.setText("");

        mButtonStartDate =  findViewById(R.id.btn_start_date);
        mButtonEndDate =  findViewById(R.id.btn_end_date);
        mButtonSubmitDate =  findViewById(R.id.btn_date_done);

        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);
        mRadioGroupType =  findViewById(R.id.radioGroupType);
        
        ///////////////////////////
        mChart =  findViewById(R.id.chart1);
        mChart.setUsePercentValues(true);
        mChart.setDescription("");
        mChart.setExtraOffsets(5, 10, 5, 5);

        mChart.setDragDecelerationFrictionCoef(0.95f);

        mTypeface = Typeface.createFromAsset(getAssets(), "OpenSans-Regular.ttf");
        mChart.setCenterTextTypeface(Typeface.createFromAsset(getAssets(), "OpenSans-Light.ttf"));
        mChart.setCenterText("Product Group");

        mChart.setDrawHoleEnabled(true);
        mChart.setHoleColorTransparent(true);

        mChart.setTransparentCircleColor(Color.WHITE);
        mChart.setTransparentCircleAlpha(110);

        mChart.setHoleRadius(58f);
        mChart.setTransparentCircleRadius(61f);

        mChart.setDrawCenterText(true);
        mChart.setRotationAngle(0);
        mChart.setRotationEnabled(true);
        mChart.setHighlightPerTapEnabled(true);

        mChart.setOnChartValueSelectedListener(this);
        
        mChart.setDrawSliceText(false);
        mChart.animateY(1400, Easing.EasingOption.EaseInOutQuad);
        
        Legend l = mChart.getLegend();
        l.setPosition(LegendPosition.LEFT_OF_CHART);
        l.setXEntrySpace(10f);
        l.setYEntrySpace(1f);
        l.setTextSize(12f);
        l.setYOffset(0f);
    }

    private void SetPieChartData(int job) {
        ArrayList<Entry> yAxisVals = new ArrayList<>();
        ArrayList<String> xAxisVals = new ArrayList<>();

        if (mType.equalsIgnoreCase("sku")) {
            if (1 == job) {
                int count = mProductGroupDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mProductGroupDetails.get(i).getVerticalValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mProductGroupDetails.get(i).getGroupName().trim() + " (" + Math.round(value) + ")");
                }
            }
        }

        if (mType.equalsIgnoreCase("employee")) {
            if (1 == job) {
                int count = mEmployeeMasterDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mEmployeeMasterDetails.get(i).getVerticalValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mEmployeeMasterDetails.get(i).getEmpName().trim() + " (" + Math.round(value) + ")");
                }
            }

            if (3 == job) {
                int count = mProductGroupDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mProductGroupDetails.get(i).getVerticalValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mProductGroupDetails.get(i).getGroupName().trim() + " (" + Math.round(value) + ")");
                }
            }
        }

        if (mType.equalsIgnoreCase("state")) {
            if (1 == job) {
                int count = mKeyValueStateList.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mKeyValueStateList.get(i).getValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mKeyValueStateList.get(i).getKey().trim() + " (" + Math.round(value) + ")");
                }
            }

            if (3 == job) {
                int count = mBranchMasterDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mBranchMasterDetails.get(i).getValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mBranchMasterDetails.get(i).getBranchName().trim() + " (" + Math.round(value) + ")");
                }
            }

            if (4 == job) {
                int count = mProductGroupDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mProductGroupDetails.get(i).getVerticalValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mProductGroupDetails.get(i).getGroupName().trim() + " (" + Math.round(value) + ")");
                }
            }
        }

        if (mType.equalsIgnoreCase("plant")) {
            if (1 == job) {
                int count = mKeyValuePlantList.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mKeyValuePlantList.get(i).getValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mKeyValuePlantList.get(i).getKey().trim() + " (" + Math.round(value) + ")");
                }
            }

            if (3 == job) {
                int count = mBranchMasterDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mBranchMasterDetails.get(i).getValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mBranchMasterDetails.get(i).getBranchName().trim() + " (" + Math.round(value) + ")");
                }
            }

            if (4 == job) {
                int count = mProductGroupDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mProductGroupDetails.get(i).getVerticalValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mProductGroupDetails.get(i).getGroupName().trim() + " (" + Math.round(value) + ")");
                }
            }
        }

        if (mType.equalsIgnoreCase("zone")) {
            if (1 == job) {
                int count = mKeyValueZoneList.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mKeyValueZoneList.get(i).getValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mKeyValueZoneList.get(i).getKey().trim() + " (" + Math.round(value) + ")");
                }
            }

            if (3 == job) {
                int count = mKeyValueStateList.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mKeyValueStateList.get(i).getValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mKeyValueStateList.get(i).getKey().trim() + " (" + Math.round(value) + ")");
                }
            }

            if (4 == job) {
                int count = mBranchMasterDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mBranchMasterDetails.get(i).getValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mBranchMasterDetails.get(i).getBranchName().trim() + " (" + Math.round(value) + ")");
                }
            }

            if (5 == job) {
                int count = mProductGroupDetails.size();
                for (int i = 0; i < count; i++) {
                    double value = Double.parseDouble(mProductGroupDetails.get(i).getVerticalValue());
                    yAxisVals.add(new Entry((float) Math.round(value), i));
                    xAxisVals.add(mProductGroupDetails.get(i).getGroupName().trim() + " (" + Math.round(value) + ")");
                }
            }
        }

        PieDataSet dataSet = new PieDataSet(yAxisVals, "");
        dataSet.setSliceSpace(5f);
        dataSet.setSelectionShift(8f);
        dataSet.setValueTextSize(12f);
        
        ArrayList<Integer> colors = new ArrayList<>();

        for (int c : ColorTemplate.VORDIPLOM_COLORS)
            colors.add(c);

        for (int c : ColorTemplate.JOYFUL_COLORS)
            colors.add(c);

        for (int c : ColorTemplate.COLORFUL_COLORS)
            colors.add(c);

        for (int c : ColorTemplate.LIBERTY_COLORS)
            colors.add(c);

        for (int c : ColorTemplate.PASTEL_COLORS)
            colors.add(c);

        colors.add(ColorTemplate.getHoloBlue());

        dataSet.setColors(colors);
        
        PieData data = new PieData(xAxisVals, dataSet);

        data.setValueFormatter(new PercentFormatter(new DecimalFormat("##.##")));

        data.setValueTextSize(12f);
        data.setValueTextColor(Color.BLACK);
        data.setValueTypeface(mTypeface);
        mChart.setData(data);

        mChart.highlightValues(null);
        mChart.invalidate();
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
            ChangeBackground(SELECTION);
            mQuery = BuildQuery(SELECTION);
            mEmployeeCode = Constants.employeeDetailObject.getEmpCode();
            mEmployeeName = Constants.employeeDetailObject.getEmpName();
            mEmpCodeList = new ArrayList<>();
            mEmpCodeList.add(mEmployeeCode);
            mEmpNameList = new ArrayList<>();
            mEmpNameList.add(mEmployeeName);
            mEmployeeDetails.setText("");
            FetchSaudaTransactionLogData(1);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            mQuery = BuildQuery(SELECTION);
            mEmployeeCode = Constants.employeeDetailObject.getEmpCode();
            mEmployeeName = Constants.employeeDetailObject.getEmpName();
            mEmpCodeList = new ArrayList<>();
            mEmpCodeList.add(mEmployeeCode);
            mEmpNameList = new ArrayList<>();
            mEmpNameList.add(mEmployeeName);
            mEmployeeDetails.setText("");
            FetchSaudaTransactionLogData(1);
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
                    mQuery = BuildQuery(SELECTION);
                    mEmployeeCode = Constants.employeeDetailObject.getEmpCode();
                    mEmployeeName = Constants.employeeDetailObject.getEmpName();
                    mEmpCodeList = new ArrayList<>();
                    mEmpCodeList.add(mEmployeeCode);
                    mEmpNameList = new ArrayList<>();
                    mEmpNameList.add(mEmployeeName);
                    mEmployeeDetails.setText("");
                    FetchSaudaTransactionLogData(1);
                    mTextViewStartDate.setText("");
                    mTextViewEndDate.setText("");
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
    @SuppressLint("SetTextI18n")
    public String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                condition = "substr(STL.sauda_date,1,10)='" + mToday + "' ";
                mTextViewDate.setText("Record for today");
                break;
            case 2:
                condition = "substr(STL.sauda_date,1,4)='" + mCurrentYear + "' AND substr(STL.sauda_date,6,2)='" + mCurrentMonth + "' ";
                mTextViewDate.setText("Record for MTD");
                break;
            case 3:
                condition = "substr(STL.sauda_date,1,10) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
                mTextViewDate.setText("Record from " + mFStartDate + " to " + mFEndDate);
                break;
        }
        return condition;
    }

    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
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
    @SuppressLint("SetTextI18n")
    public void ShowSKUSaudaMisListDialog(String footer) {
        CalculateTotal();
        final Dialog mSaudaBrokerDialog = new Dialog(MISActivity.this, R.style.PauseDialog);
        mSaudaBrokerDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mSaudaBrokerDialog.setContentView(R.layout.sku_dialog_chart);
        mSaudaBrokerDialog.setCancelable(true);

        TextView textViewOilGroupName =  mSaudaBrokerDialog.findViewById(R.id.textviewOilGroupName);
        textViewOilGroupName.setText(mProductGroupName);

        TextView textViewTotalCase =  mSaudaBrokerDialog.findViewById(R.id.textViewCaseTotal);
        textViewTotalCase.setText(defaultFormat.format(mCaseTotal));

        TextView textViewTotalMt =  mSaudaBrokerDialog.findViewById(R.id.textViewMTTotal);
        textViewTotalMt.setText(defaultFormat.format(mMTTotal));


        TextView textViewMessage =  mSaudaBrokerDialog.findViewById(R.id.textViewMessage);
        textViewMessage.setText(footer + "\n" + mMessage);

        ListView dialogList =  mSaudaBrokerDialog.findViewById(R.id.listdata);

        final MisSKUAdapter misadapter = new MisSKUAdapter(MISActivity.this, R.layout.sku_child, mMisDetailsList);
        dialogList.setAdapter(misadapter);
        
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> mSaudaBrokerDialog.cancel());
        mSaudaBrokerDialog.show();

    }
    
    public void BuildMessage() {
        switch (SELECTION) {
            case 1:
                mMessage = "Today's sales";
                break;
            case 2:
                mMessage = "Current month's sales";
                break;
            case 3:
                mMessage = "Sales from " + mFStartDate + " to " + mFEndDate;
                break;
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowPackSizeDialog() {
        final Dialog mPackSizeDialog = new Dialog(MISActivity.this, R.style.PauseDialog);
        mPackSizeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mPackSizeDialog.setContentView(R.layout.select_with_search);
        mPackSizeDialog.setCancelable(false);
        TextView title =  mPackSizeDialog.findViewById(R.id.title);
        title.setText("Please select type of packing size");
        ListView dialogList =  mPackSizeDialog.findViewById(R.id.list);
        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_listview, mPackingSizeList);
        dialogList.setAdapter(adapter);
        EditText searchText =  mPackSizeDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }
            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
            @Override
            public void afterTextChanged(Editable s) {}
        });

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mPackSize = adapter.getItem(position);
            mTextViewPackingSizeValue.setText("Pack Size : " + mPackSize);
            mPackSizeDialog.cancel();
            FetchSaudaTransactionLogData(1);
        });

        Button cancel =  mPackSizeDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mPackSizeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowVericalValueListDialog() {
        final Dialog mVerticalDialog = new Dialog(MISActivity.this, R.style.PauseDialog);
        mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mVerticalDialog.setContentView(R.layout.select_with_search);
        mVerticalDialog.setCancelable(false);

        TextView title =  mVerticalDialog.findViewById(R.id.title);
        title.setText("Please select a vertical");
        ListView dialogList =  mVerticalDialog.findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_listview, mVerticalValueList);
        dialogList.setAdapter(adapter);

        EditText searchText =  mVerticalDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }
            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
            @Override
            public void afterTextChanged(Editable s) {}
        });

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            Constants.mVerticalValue = adapter.getItem(position);
            mTextViewVerticalValue.setText("Vertical : " + Constants.mVerticalValue);
            mVerticalDialog.cancel();
            FetchSaudaTransactionLogData(7);
        });

        Button cancel =  mVerticalDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mVerticalDialog.show();
    }

    public void PreviousState() {
        if (mType.equalsIgnoreCase("employee")) {
            if (JOB == 1) {
                if (mEmpCodeList.size() >= 2 && mEmpNameList.size() >= 2) {
                    mEmployeeCode = mEmpCodeList.get(mEmpCodeList.size() - 2);
                    mEmployeeName = mEmpNameList.get(mEmpNameList.size() - 2);
                    mEmpCodeList.remove(mEmpCodeList.size() - 1);
                    mEmpNameList.remove(mEmpNameList.size() - 1);
                    FetchSaudaTransactionLogData(1);
                }
                SetEmpdetails();
            }
            if (JOB == 3) {
                if (mEmpCodeList.size() >= 2 && mEmpNameList.size() >= 2) {
                    mEmployeeCode = mEmpCodeList.get(mEmpCodeList.size() - 2);
                    mEmployeeName = mEmpNameList.get(mEmpNameList.size() - 2);
                    mEmpCodeList.remove(mEmpCodeList.size() - 1);
                    mEmpNameList.remove(mEmpNameList.size() - 1);
                    FetchSaudaTransactionLogData(1);
                }
                SetEmpdetails();
            }
        }
        if (mType.equalsIgnoreCase("state")) {
            if (JOB == 3) {
                FetchSaudaTransactionLogData(1);
            }
            if (JOB == 4) {
                FetchSaudaTransactionLogData(3);
            }
        }

        if (mType.equalsIgnoreCase("plant")) {
            if (JOB == 3) {
                FetchSaudaTransactionLogData(1);
            }
            if (JOB == 4) {
                FetchSaudaTransactionLogData(3);
            }
        }

        if (mType.equalsIgnoreCase("zone")) {
            if (JOB == 3) {
                FetchSaudaTransactionLogData(1);
            }
            if (JOB == 4) {
                FetchSaudaTransactionLogData(3);
            }
            if (JOB == 5) {
                FetchSaudaTransactionLogData(4);
            }
        }
    }

    public void CalculateTotal() {
        String mt;
        String cases;
        mMTTotal = 0;
        mCaseTotal = 0;
        for (int count = 0; count < mMisDetailsList.size(); count++) {
            cases = mMisDetailsList.get(count).getBookedQtyCase();
            mt = mMisDetailsList.get(count).getBookedQtyTon();
            if (!cases.isEmpty() && !mt.isEmpty()) {
                mMTTotal += Double.parseDouble(mt);
                mCaseTotal += Double.parseDouble(cases);
            }
        }
    }


    public void FetchSaudaTransactionLogData(final int whattodo) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Fetching Data.\nPlease wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        if (mType.equalsIgnoreCase("sku")) {
                            mProductGroupDetails = mAceDnsDatabaseHelper.GetSaudaBookedProductGroupDetails(mQuery, mType, mPackSize);
                        }
                        if (mType.equalsIgnoreCase("employee")) {
                            mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetails(mEmployeeCode);
                            if (!mEmployeeMasterLowerLeavesDetails.isEmpty()) {
                                mEmployeeMasterDetails = new ArrayList<>();
                                EmployeeMasterDetails obj = null;
                                for (int count = 0; count < mEmployeeMasterLowerLeavesDetails.size(); count++) {
                                    String lowerleaves = mEmployeeMasterLowerLeavesDetails.get(count).getLowerLeaves();
                                    String quantity = mAceDnsDatabaseHelper.getQuantity(mQuery, lowerleaves);
                                    if (!quantity.trim().isEmpty()) {
                                        obj = new EmployeeMasterDetails();
                                        obj.setEmpCode(mEmployeeMasterLowerLeavesDetails.get(count).getEmpCode());
                                        obj.setEmpName(mEmployeeMasterLowerLeavesDetails.get(count).getEmpName());
                                        obj.setVerticalValue(quantity);
                                        mEmployeeMasterDetails.add(obj);
                                    }
                                }
                            } else {
                                mEmployeeMasterDetails = new ArrayList<>();
                            }
                        }
                        if (mType.equalsIgnoreCase("state")) {
                            mKeyValueStateList = mAceDnsDatabaseHelper.GetSTLStateDetails(mQuery, "");
                        }
                        if (mType.equalsIgnoreCase("plant")) {
                            mKeyValuePlantList = mAceDnsDatabaseHelper.GetSTLPlantDetails(mQuery);
                        }
                        if (mType.equalsIgnoreCase("zone")) {
                            mKeyValueZoneList = mAceDnsDatabaseHelper.GetSTLZoneDetails(mQuery);
                        }
                        break;
                    case 2:
                        if (mType.equalsIgnoreCase("sku")) {
                            mMisDetailsList = mAceDnsDatabaseHelper.GetMisDetailsData(mType, mQuery, mProductGroupCode, "", "", "", "", "");
                        }
                        if (mType.equalsIgnoreCase("employee")) {
                            mMisDetailsList = mAceDnsDatabaseHelper.GetMisDetailsData(mType, mQuery, mProductGroupCode, mEmployeeCode, "", "", "", "");
                        }
                        if (mType.equalsIgnoreCase("state")) {
                            mMisDetailsList = mAceDnsDatabaseHelper.GetMisDetailsData(mType, mQuery, mProductGroupCode, "", mStateName, mBranchCode, "", "");
                        }
                        if (mType.equalsIgnoreCase("plant")) {
                            mMisDetailsList = mAceDnsDatabaseHelper.GetMisDetailsData(mType, mQuery, mProductGroupCode, "", "", mBranchCode, "", mPlantName);
                        }
                        if (mType.equalsIgnoreCase("zone")) {
                            mMisDetailsList = mAceDnsDatabaseHelper.GetMisDetailsData(mType, mQuery, mProductGroupCode, "", mStateName, mBranchCode, mZoneName, "");
                        }
                        break;
                    case 3:
                        if (mType.equalsIgnoreCase("employee")) {
                            mProductGroupDetails = mAceDnsDatabaseHelper.GetSaudaBookedProductGroupDetails(mQuery, mType, mEmployeeCode, "", "", "", "", mPackSize);
                        }
                        if (mType.equalsIgnoreCase("state")) {
                            mBranchMasterDetails = mAceDnsDatabaseHelper.GetSTLBranchDetails(mQuery, mStateName, "");
                        }
                        if (mType.equalsIgnoreCase("plant")) {
                            mBranchMasterDetails = mAceDnsDatabaseHelper.GetSTLBranchDetails(mQuery, "", mPlantName);
                        }
                        if (mType.equalsIgnoreCase("zone")) {
                            mKeyValueStateList = mAceDnsDatabaseHelper.GetSTLStateDetails(mQuery, mZoneName);
                        }
                        break;
                    case 4:
                        if (mType.equalsIgnoreCase("state")) {
                            mProductGroupDetails = mAceDnsDatabaseHelper.GetSaudaBookedProductGroupDetails(mQuery, mType, mEmployeeCode, "", mStateName, "", mBranchCode, mPackSize);
                        }
                        if (mType.equalsIgnoreCase("plant")) {
                            mProductGroupDetails = mAceDnsDatabaseHelper.GetSaudaBookedProductGroupDetails(mQuery, mType, mEmployeeCode, "", "", mPlantName, mBranchCode, mPackSize);
                        }
                        if (mType.equalsIgnoreCase("zone")) {
                            mBranchMasterDetails = mAceDnsDatabaseHelper.GetSTLBranchDetails(mQuery, mStateName, "");
                        }
                        break;
                    case 5:
                        if (mType.equalsIgnoreCase("zone")) {
                            mProductGroupDetails = mAceDnsDatabaseHelper.GetSaudaBookedProductGroupDetails(mQuery, mType, mEmployeeCode, mZoneName, mStateName, "", mBranchCode, mPackSize);
                        }
                        break;
                    case 6:
                        int max ;
                        max = mAceDnsDatabaseHelper.GetVerticalValue();
                        mVerticalValueList = new String[max];
                        System.arraycopy(Constants.mVerticalValueList, 0, mVerticalValueList, 0, Constants.mVerticalValueList.length);
                        break;
                    case 7:
                        int maxx ;
                        maxx = mAceDnsDatabaseHelper.GetPackSize();
                        mPackingSizeList = new String[maxx + 1];
                        mPackingSizeList[0] = "All";
                        System.arraycopy(Constants.mVerticalValueList, 0, mPackingSizeList, 1, Constants.mVerticalValueList.length);
                        break;
                    case 8:
                        mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetails(mEmployeeCode);
                        if (!mEmployeeMasterLowerLeavesDetails.isEmpty()) {
                            for (int count = 0; count < mEmployeeMasterLowerLeavesDetails.size(); count++) {
                                String lowerleaves = mEmployeeMasterLowerLeavesDetails.get(count).getLowerLeaves();
                                String quantity = mAceDnsDatabaseHelper.getQuantity(mQuery, lowerleaves);
                                if (!quantity.trim().isEmpty()) {
                                    isNextLevelValid = true;
                                }
                            }
                        } else {
                            isNextLevelValid = false;
                        }
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

    public void SetEmpdetails() {
        String details = "";
        if (mEmpNameList != null && !mEmpNameList.isEmpty()) {
            for (int count = 0; count < mEmpNameList.size(); count++) {
                if (count == mEmpNameList.size() - 1) {
                    details += mEmpNameList.get(count);
                } else {
                    details = MessageFormat.format("{0}{1}", details, mEmpNameList.get(count) + "->");
                }
            }
        }
        mEmployeeDetails.setText(details);
    }

    @SuppressLint("SetTextI18n")
    public void ShowConditionofMis() {
        final Dialog dialgoCondition = new Dialog(MISActivity.this, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_mis);
        TextView txtMsg =  dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select an option.");
        final RadioGroup radioSelectionGroup =  dialgoCondition.findViewById(R.id.radioSelect);
        radioSelectionGroup.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection =  dialgoCondition.findViewById(checkedId);
            if (radioSelection.getText().equals("Self")) {
                FetchSaudaTransactionLogData(3);
            } else {
                FetchSaudaTransactionLogData(8);
            }
            dialgoCondition.cancel();
        });
        dialgoCondition.show();
    }
}
