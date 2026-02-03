package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;

import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.AttendanceReportAdapter;
import com.forcepower.acedns.adapter.EmployeeAdapter;
import com.forcepower.acedns.bean.AttendanceReportDetails;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Objects;

import static com.forcepower.acedns.constants.Constants.retailerAppisLowerMostLevelEmp;
import static com.forcepower.acedns.constants.Constants.selectedEmpRetailerApp;

public class ActivityAttendanceReport extends FragmentActivity implements OnClickListener {
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonEndDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmitDate = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewShowDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHideDuration = null;

    public static String showingTimeOrCount = "";
    @SuppressLint("StaticFieldLeak")
    public static TextView timeOrCountLabel = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView dateRangeTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewEndDate = null;
    @SuppressLint("StaticFieldLeak")
    public static ListView mListViewList = null;

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

    public AceDnsDatabase mAceDnsDatabaseHelper;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    AttendanceReportAdapter orderReportAdapter;
    AttendanceReportAdapter orderReportAdapterLevel2;
    private int SELECTION = 0;
    private boolean isStartDate = false;
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mToday = "";
    private String mQuery = "";
    private ArrayList<AttendanceReportDetails> mOrderReportDetailsList;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterLowerLeavesDetails;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterDetailsList;

    @SuppressLint({"SetTextI18n", "HandlerLeak", "SimpleDateFormat"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_attendance_report);
        mContext = ActivityAttendanceReport.this;
        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyyMMdd");

        mToday = mDFormatBackEnd.format(currentDate);

        String mTime = Constants.dateString;
        GETCurrentMonthYear(mTime);

        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        InitializeView();

        mButtonBack.setOnClickListener(v -> finish());

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate = date;
                    mTextViewStartDate.setText(mDFormatFrontEnd.format(date));
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    mEndDate = mDFormatBackEnd.format(date);
                }
                dialogCaldroidFragment.dismiss();
            }
        };


        mReportHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivityAttendanceReport.this.runOnUiThread(() -> {
                    switch (job) {
                        case 1:
                            if (SELECTION == 1 || (SELECTION == 3 && (mStartDate.matches(mEndDate)))) {
                                timeOrCountLabel.setText("Time");
                                showingTimeOrCount = "Time";
                            } else {
                                timeOrCountLabel.setText("Count");
                                showingTimeOrCount = "Count";
                            }
                            orderReportAdapter = new AttendanceReportAdapter(ActivityAttendanceReport.this, R.layout.retailerapp_attendance_report_child, mOrderReportDetailsList, false);
                            mListViewList.setAdapter(orderReportAdapter);
                            break;
                        case 2:
                            if (!mEmployeeMasterDetailsList.isEmpty()) {
                                ShowEmployeeDialog();
                            } else {
                                FetchSaudaTransactionLogData(1);
                            }
                            break;
                    }
                });
            }
        };
        if (SELECTION == 0) {
            SELECTION = 1;
        }
        ChangeBackground(SELECTION);
        mQuery = BuildQuery(SELECTION);
        Utils.headerFooterIconChangesForRetailerApp(mContext, false);
        customerEmpSelectionProcess();
    }

    private void customerEmpSelectionProcess() {
        selectedEmpRetailerApp = Constants.employeeDetailObject.getEmpCode();
        mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetailsWithOutVertical(selectedEmpRetailerApp);
        if (!mEmployeeMasterLowerLeavesDetails.isEmpty()) {
            retailerAppisLowerMostLevelEmp = false;
            ShowConditionofATA();
        } else {
            retailerAppisLowerMostLevelEmp = true;
            FetchSaudaTransactionLogData(1);
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowEmployeeDialog() {
        EmployeeAdapter adapter = new EmployeeAdapter(mContext, R.layout.customer_broker_list_child, mEmployeeMasterDetailsList);
        final Dialog dialogEmployeeList = new Dialog(mContext, R.style.PauseDialog);
        dialogEmployeeList.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogEmployeeList.setContentView(R.layout.select_from_list);
        dialogEmployeeList.setCancelable(false);
        TextView title = dialogEmployeeList.findViewById(R.id.title);
        title.setText("Please select an employee");

        ListView list = dialogEmployeeList.findViewById(R.id.list);
        list.setAdapter(adapter);
        list.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            selectedEmpRetailerApp = mEmployeeMasterDetailsList.get(pos).getEmpCode();
            mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetailsWithOutVertical(selectedEmpRetailerApp);
            if (!mEmployeeMasterLowerLeavesDetails.isEmpty()) {
                ShowConditionofATA();
            } else {
                FetchSaudaTransactionLogData(1);
            }
            dialogEmployeeList.cancel();
        });

        Button cancel = dialogEmployeeList.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        dialogEmployeeList.show();

    }

    @SuppressLint("SetTextI18n")
    public void ShowConditionofATA() {
        final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_mis);
        TextView txtMsg = dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select an option.");
        final RadioGroup radioSelectionGroup = dialgoCondition.findViewById(R.id.radioSelect);

        radioSelectionGroup.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection = dialgoCondition.findViewById(checkedId);
            if (radioSelection.getText().equals("Self")) {
                FetchSaudaTransactionLogData(1);
            } else {
                FetchSaudaTransactionLogData(2);
            }
            dialgoCondition.cancel();
        });
        dialgoCondition.show();
    }

    @SuppressLint("SetTextI18n")
    public String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                condition = "substr(trans_id ,-14,8)='" + mToday + "' ";
                dateRangeTV.setText("Attendance Report On " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mToday));
                break;
            case 2:
                condition = "substr(trans_id ,-14,4)='" + mCurrentYear + "' AND substr(trans_id ,-10,2)='" + mCurrentMonth + "' ";
                dateRangeTV.setText("Attendance Report On " + mCurrentMonth + "/" + mCurrentYear);

                break;
            case 3:
                condition = "substr(trans_id ,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
                dateRangeTV.setText("Attendance Report From " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mStartDate) + " to " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mEndDate));
                break;
        }
        return condition;
    }


    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
    }

    public void InitializeView() {
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        mImageViewShowDuration = findViewById(R.id.image_clk);
        mImageViewHideDuration = findViewById(R.id.image_go);

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);

        mRelativeLayoutDuration = findViewById(R.id.duration_layout);
        mCustomDateLayout = findViewById(R.id.custom_date_layout);
        mButtonBack = findViewById(R.id.back);
        mFrameLayoutToday = findViewById(R.id.btn_today);
        mFrameLayoutMTD = findViewById(R.id.btn_mtd);
        mFrameLayoutCustom = findViewById(R.id.btn_custom);

        mFrameLayoutToday.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);

        dateRangeTV = findViewById(R.id.dateRangeTV);
        timeOrCountLabel = findViewById(R.id.timeOrCountLabel);
        mTextViewStartDate = findViewById(R.id.txt_start_date);
        mTextViewEndDate = findViewById(R.id.txt_end_date);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");

        mButtonStartDate = findViewById(R.id.btn_start_date);
        mButtonEndDate = findViewById(R.id.btn_end_date);
        mButtonSubmitDate = findViewById(R.id.btn_date_done);

        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);

        mListViewList = findViewById(R.id.listView);
        mListViewList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            if (SELECTION == 2 || (SELECTION == 3 && (!mStartDate.matches(mEndDate)))) {
                String attendanceCountOfClickedItem = ((TextView) arg1.findViewById(R.id.textViewBookedQtyTon)).getText().toString();
                if (Integer.parseInt(attendanceCountOfClickedItem) > 0) {
                    String EmpNameOfClickedItem = ((TextView) arg1.findViewById(R.id.textViewSku)).getText().toString();
                    String EmpCodeOfClickedItem = ((TextView) arg1.findViewById(R.id.invisibleProdCode)).getText().toString();
                    ShowEmpAttendanceDetailsDialog(EmpCodeOfClickedItem, EmpNameOfClickedItem);
                }
            }
        });
        mListViewList.setEmptyView(findViewById(R.id.empty_text_view));
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowEmpAttendanceDetailsDialog(final String EmpCodeOfClickedItem, final String EmpNameOfClickedItem) {
        ArrayList<AttendanceReportDetails> mOrderReportDetailsListLevel2 = mAceDnsDatabaseHelper.GetAttendanceReport2(mQuery, "'" + EmpCodeOfClickedItem + "'");
        final Dialog mDetailsDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar_Fullscreen);
        Objects.requireNonNull(mDetailsDialog.getWindow()).setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        mDetailsDialog.setContentView(R.layout.dialog_stock_report_retailer_imei);
        mDetailsDialog.setCancelable(true);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        TextView imeiLabel = mDetailsDialog.findViewById(R.id.imeiLabel);
        FrameLayout timeOfAttendance = mDetailsDialog.findViewById(R.id.timeOfAttendance);
        timeOfAttendance.setVisibility(View.VISIBLE);
        textViewTitleName.setText(EmpNameOfClickedItem);
        imeiLabel.setText("Date");
        ListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        orderReportAdapterLevel2 = new AttendanceReportAdapter(ActivityAttendanceReport.this, R.layout.retailerapp_attendance_report_child, mOrderReportDetailsListLevel2, true);
        dialogList.setAdapter(orderReportAdapterLevel2);
        mDetailsDialog.show();
    }

    public void FetchSaudaTransactionLogData(final int whattodo) {
        mQuery = BuildQuery(SELECTION);
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        String lowerleaves = mAceDnsDatabaseHelper.GetLowerleavesOfEmployee(selectedEmpRetailerApp);
                        if (SELECTION == 1 || (SELECTION == 3 && (mStartDate.matches(mEndDate)))) {
                            mOrderReportDetailsList = mAceDnsDatabaseHelper.GetAttendanceReport(mQuery, lowerleaves);
                        } else {
                            mOrderReportDetailsList = mAceDnsDatabaseHelper.GetAttendanceReportGroupByEmp(mQuery, lowerleaves);
                        }
                        break;
                    case 2:
                        mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetailsWithOutVertical(selectedEmpRetailerApp);
                        mEmployeeMasterDetailsList = new ArrayList<>();
                        if (!mEmployeeMasterLowerLeavesDetails.isEmpty()) {
                            EmployeeMasterDetails obj;
                            for (int count = 0; count < mEmployeeMasterLowerLeavesDetails.size(); count++) {
                                obj = new EmployeeMasterDetails();
                                obj.setEmpCode(mEmployeeMasterLowerLeavesDetails.get(count).getEmpCode());
                                obj.setEmpName(mEmployeeMasterLowerLeavesDetails.get(count).getEmpName());
                                mEmployeeMasterDetailsList.add(obj);
                            }
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
            customerEmpSelectionProcess();
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            customerEmpSelectionProcess();
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
                    customerEmpSelectionProcess();
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
}
