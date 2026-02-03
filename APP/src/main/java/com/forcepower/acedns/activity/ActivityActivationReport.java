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

import com.forcepower.acedns.adapter.ActivationReportAdapter;
import com.forcepower.acedns.adapter.EmployeeAdapter;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.EmployeeMasterDetails;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Objects;

public class ActivityActivationReport extends FragmentActivity implements OnClickListener {
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
    ActivationReportAdapter orderReportAdapter;
    ActivationReportAdapter orderReportAdapterLevel2;
    private int SELECTION = 0;
    private boolean isStartDate = false;
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mToday = "";
    private String mQuery = "";
    private String imeiType = "";
    private String imeiPref = "";
    private ArrayList<ProductMasterDetails> mOrderReportDetailsList;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterLowerLeavesDetails;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterDetailsList;

    @SuppressLint({"SimpleDateFormat", "SetTextI18n", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_activation_report);
        mContext = ActivityActivationReport.this;
        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyyMMdd");

        mToday = mDFormatBackEnd.format(currentDate);

        GETCurrentMonthYear();

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

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
                ActivityActivationReport.this.runOnUiThread(() -> {
                    switch (job) {
                        case 1:
                            orderReportAdapter = new ActivationReportAdapter(ActivityActivationReport.this, R.layout.retailerapp_attendance_report_child, mOrderReportDetailsList, false);
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
        showImeiTypeDialog();
    }

    @SuppressLint("SetTextI18n")
    public void showImeiTypeDialog() {
        final Dialog payTypeDialog = new Dialog(mContext, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.activationtype_dialog);
        TextView txtMsg = payTypeDialog.findViewById(R.id.title);
        txtMsg.setText("Select IMEI type");

        RadioGroup payTypeOption = payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener((group, checkedId) -> {
            int radioButtonID = group.getCheckedRadioButtonId();
            View radioButton = group.findViewById(radioButtonID);
            int selectedRadio = group.indexOfChild(radioButton);
            if (selectedRadio == 0) {
                imeiType = "registered";
                imeiPref = "Registered IMEI Activation Report";
            } else if (selectedRadio == 1) {
                imeiType = "unregistered";
                imeiPref = "Unregistered IMEI Activation Report";
            } else {
                imeiType = "unactivated";
                imeiPref = "Unactivated IMEI Report";
            }
            payTypeDialog.dismiss();
            customerEmpSelectionProcess();
        });
        Button cancel = payTypeDialog.findViewById(R.id.btn_cancel);
        cancel.setVisibility(View.VISIBLE);
        cancel.setOnClickListener(arg0 -> payTypeDialog.cancel());
        payTypeDialog.show();
    }

    private void customerEmpSelectionProcess() {
        Constants.selectedEmpRetailerApp = Constants.employeeDetailObject.getEmpCode();
        mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetailsWithOutVertical(Constants.selectedEmpRetailerApp);
        if (!mEmployeeMasterLowerLeavesDetails.isEmpty()) {
            Constants.retailerAppisLowerMostLevelEmp = false;
            ShowConditionofATA();
        } else {
            Constants.retailerAppisLowerMostLevelEmp = true;
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
            Constants.selectedEmpRetailerApp = mEmployeeMasterDetailsList.get(pos).getEmpCode();
            mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetailsWithOutVertical(Constants.selectedEmpRetailerApp);
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
        String imeiType = "";
        if (this.imeiType.matches("registered")) {
            imeiType = " AND CPB.stock_out_date!='0000-00-00' AND CPB.activation_date!='0000-00-00 00:00:00' ";
        } else if (this.imeiType.matches("unregistered")) {
            imeiType = " AND CPB.stock_out_date='0000-00-00' AND CPB.activation_date!='0000-00-00 00:00:00' ";
        } else {
            imeiType = " AND CPB.activation_date='0000-00-00 00:00:00' ";
        }
        mToday = mDFormatBackEnd.format(currentDate);
        switch (select) {
            case 1:
                mToday = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mToday);
                condition = "CPB.invoice_date='" + mToday + "' " + imeiType;
                dateRangeTV.setText(imeiPref + " On " + Utils.changeDateFormat("yyyy-MM-dd", "dd/MM/yyyy", mToday));
                break;
            case 2:
                GETCurrentMonthYear();
                condition = "substr(CPB.invoice_date,1,4)='" + mCurrentYear + "' AND substr(CPB.invoice_date,6,2)='" + mCurrentMonth + "' " + imeiType;
                dateRangeTV.setText(imeiPref + " On " + mCurrentMonth + "/" + mCurrentYear);
                break;
            case 3:
                mStartDate = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mStartDate);
                mEndDate = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mEndDate);
                condition = "CPB.invoice_date BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' " + imeiType;
                dateRangeTV.setText(imeiPref + " From " + Utils.changeDateFormat("yyyy-MM-dd", "dd/MM/yyyy", mStartDate) + " to " + Utils.changeDateFormat("yyyy-MM-dd", "dd/MM/yyyy", mEndDate));
                break;
        }
        return condition;
    }


    public void GETCurrentMonthYear() {
        mCurrentYear = Constants.dateString.substring(0, 4);
        mCurrentMonth = Constants.dateString.substring(4, 6);
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
            String CountOfClickedItem = ((TextView) arg1.findViewById(R.id.textViewBookedQtyTon)).getText().toString();
            if (Integer.parseInt(CountOfClickedItem) > 0) {
                String EmpNameOfClickedItem = ((TextView) arg1.findViewById(R.id.textViewSku)).getText().toString();
                String EmpCodeOfClickedItem = ((TextView) arg1.findViewById(R.id.invisibleProdCode)).getText().toString();
                ShowEmpAttendanceDetailsDialog(EmpCodeOfClickedItem, EmpNameOfClickedItem);
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
    public void ShowEmpAttendanceDetailsDialog(final String custCodeOfClickedItem, final String EmpNameOfClickedItem) {
        ArrayList<ProductMasterDetails> mOrderReportDetailsListLevel2 = mAceDnsDatabaseHelper.GetActivationReportByCustomerProduct(mQuery, custCodeOfClickedItem);
        final Dialog mDetailsDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar_Fullscreen);
        Objects.requireNonNull(mDetailsDialog.getWindow()).setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        mDetailsDialog.setContentView(R.layout.dialog_stock_report_retailer_imei);
        mDetailsDialog.setCancelable(true);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        TextView imeiLabel = mDetailsDialog.findViewById(R.id.imeiLabel);
        TextView imeiLabe2 = mDetailsDialog.findViewById(R.id.imeiLabe2);
        FrameLayout timeOfAttendance = mDetailsDialog.findViewById(R.id.timeOfAttendance);
        timeOfAttendance.setVisibility(View.VISIBLE);
        textViewTitleName.setText(EmpNameOfClickedItem);
        imeiLabe2.setText("IMEI Count");
        imeiLabel.setText("Product");

        ListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            String CountOfClickedItem = ((TextView) arg1.findViewById(R.id.textViewBookedQtyTon)).getText().toString();
            if (Integer.parseInt(CountOfClickedItem) > 0) {
                String prodName = ((TextView) arg1.findViewById(R.id.textViewSku)).getText().toString();
                String prodCodeOfClickedItem = ((TextView) arg1.findViewById(R.id.invisibleProdCode)).getText().toString();
                ShowImeiDialog(custCodeOfClickedItem, prodCodeOfClickedItem, prodName);
            }
        });
        orderReportAdapterLevel2 = new ActivationReportAdapter(ActivityActivationReport.this, R.layout.retailerapp_attendance_report_child, mOrderReportDetailsListLevel2, false);
        dialogList.setAdapter(orderReportAdapterLevel2);
        mDetailsDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowImeiDialog(final String custCodeOfClickedItem, String prodCodeOfClickedItem, final String EmpNameOfClickedItem) {
        ArrayList<ProductMasterDetails> mOrderReportDetailsListLevel3 = mAceDnsDatabaseHelper.GetActivationReportByCustomerProductImei(mQuery, custCodeOfClickedItem, prodCodeOfClickedItem);
        final Dialog mDetailsDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar_Fullscreen);
        Objects.requireNonNull(mDetailsDialog.getWindow()).setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        mDetailsDialog.setContentView(R.layout.dialog_stock_report_retailer_imei);
        mDetailsDialog.setCancelable(true);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        TextView imeiLabel = mDetailsDialog.findViewById(R.id.imeiLabel);
        TextView imeiLabe2 = mDetailsDialog.findViewById(R.id.imeiLabe2);
        FrameLayout timeOfAttendance = mDetailsDialog.findViewById(R.id.timeOfAttendance);
        textViewTitleName.setText(EmpNameOfClickedItem);
        imeiLabel.setText("IMEI");
        timeOfAttendance.setVisibility(View.VISIBLE);
        imeiLabe2.setText("Activation Date");

        ListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        orderReportAdapterLevel2 = new ActivationReportAdapter(ActivityActivationReport.this, R.layout.retailerapp_attendance_report_child, mOrderReportDetailsListLevel3, true);
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
                        String lowerleaves = mAceDnsDatabaseHelper.GetLowerleavesOfEmployee(Constants.selectedEmpRetailerApp);
                        mOrderReportDetailsList = mAceDnsDatabaseHelper.GetActivationReportByCustomer(mQuery, lowerleaves);
                        break;
                    case 2:
                        mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetailsWithOutVertical(Constants.selectedEmpRetailerApp);
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
