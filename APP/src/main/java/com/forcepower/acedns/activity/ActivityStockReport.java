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

import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
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

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.EmployeeAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.StockReportAdapter;
import com.forcepower.acedns.adapter.StockReportAdapterImei;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.retailerAppisLowerMostLevelEmp;
import static com.forcepower.acedns.constants.Constants.selectedEmpRetailerApp;
import static com.forcepower.acedns.constants.Constants.tempProductList;

public class ActivityStockReport extends FragmentActivity implements OnClickListener {
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
    public static TextView mTextViewStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewEndDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewTotalOpeningStock = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewTotalBilledQty = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewTotalStockOutSelf = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewTotalStockOutOthers = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewTotalClosingStock = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView dateRangeTV = null;
    @SuppressLint("StaticFieldLeak")
    public static ListView mListViewList = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout stkOutOthersLayout = null;
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

    private String conditionForOpeningStock = "";
    private String conditionForOpeningStock2 = "", dateRangeActivationDate = "", DateRangeActivationDateOpeningStock = "";
    private String conditionStockOutQtySelf = "", conditionStockOutQtyOthers = "";
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    public int SELECTION = 0;
    public AceDnsDatabase mAceDnsDatabaseHelper;
    String lastStr = "";
    ArrayList<ProductMasterDetails> productMasterList;
    StockReportAdapter ReportAdapterObject;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    ProductMasterAdapter prodAdapter;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ArrayList<ProductMasterDetails> mOrderReportDetailsList;
    ArrayList<ProductMasterDetails> mOrderReportDetailsListDialog;
    private boolean isStartDate = false;
    private String mStartDate = "";
    private String mEndDate = "";
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mQuery = "";
    private String mQueryIMEI = "";
    public static int TotalOpeningStock = 0, TotalBilledQty = 0, TotalStockOutSelf = 0, TotalStockOutOthers = 0, TotalClosingStock = 0;
    ArrayList<CustomerDetails> customerList;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterLowerLeavesDetails;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterDetailsList;

    @SuppressLint({"SimpleDateFormat", "SetTextI18n", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_stock_report_retailer);
        RegisterActivities.registerActivity(this);
        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyyMMdd");

        mContext = ActivityStockReport.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        InitializeView();

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
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
                ActivityStockReport.this.runOnUiThread(() -> {
                    switch (job) {
                        case 2:
                            if (!mEmployeeMasterDetailsList.isEmpty()) {
                                ShowEmployeeDialog();
                            } else {
                                showCustomerWiseStockList();
                            }
                            break;
                        case 3:
                            ReportAdapterObject = new StockReportAdapter(mContext, R.layout.stock_report_child_retailer, mOrderReportDetailsList, false, "customer");
                            stkOutOthersLayout.setVisibility(View.VISIBLE);
                            textViewTotalOpeningStock.setText(TotalOpeningStock + "");
                            textViewTotalBilledQty.setText(TotalBilledQty + "");
                            textViewTotalStockOutSelf.setText(TotalStockOutSelf + "");
                            textViewTotalStockOutOthers.setText(TotalStockOutOthers + "");
                            textViewTotalClosingStock.setText(TotalClosingStock + "");
                            mListViewList.setAdapter(ReportAdapterObject);
                            mListViewList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                                String CustCodeOfClickedItem = ((TextView) arg1.findViewById(R.id.invisibleProdCode)).getText().toString();
                                ShowProductDetailsDialog(CustCodeOfClickedItem);
                            });
                            break;
                    }
                });
            }
        };

        if (SELECTION == 0) {
            SELECTION = 1;
        }
        ChangeBackground(SELECTION);

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
            showCustomerWiseStockList();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowProductDetailsDialog(final String customerCode) {
        CustomerDetails custDetails = mAceDnsDatabaseHelper.getCustomerDetailsByCode(customerCode);
        mOrderReportDetailsListDialog = mAceDnsDatabaseHelper.GetStockDataRetailerAppProductWise(mQuery, custDetails, conditionStockOutQtySelf, conditionStockOutQtyOthers, conditionForOpeningStock, conditionForOpeningStock2, dateRangeActivationDate, DateRangeActivationDateOpeningStock);
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_stock_report_retailer);
        mDetailsDialog.setCancelable(true);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        TextView custProdlabel = mDetailsDialog.findViewById(R.id.custProdlabel);
        textViewTitleName.setText(custDetails.getCustomerName());
        custProdlabel.setText("Model");

        ListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        StockReportAdapter ReportAdapterObject = new StockReportAdapter(mContext, R.layout.stock_report_child_retailer, mOrderReportDetailsListDialog, false, "product");
        dialogList.setAdapter(ReportAdapterObject);

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            String prodCodeOfClickedItem = ((TextView) arg1.findViewById(R.id.invisibleProdCode)).getText().toString();
            String prodDescOfClickedItem = ((TextView) arg1.findViewById(R.id.custnameTV)).getText().toString();
            String closingStock = ((TextView) arg1.findViewById(R.id.closingStockTV)).getText().toString();
            String customerRds = ((TextView) arg1.findViewById(R.id.invisibleProdCode2)).getText().toString().trim();
            if (Integer.parseInt(closingStock) > 0) {
                ShowProductDetailsEmieDialog(prodCodeOfClickedItem, prodDescOfClickedItem, customerCode, customerRds);
            }
        });
        mDetailsDialog.show();
    }

    public void ShowProductDetailsEmieDialog(final String prodCode, final String prodDesc, final String custCode, final String customerRds) {
        mOrderReportDetailsListDialog = mAceDnsDatabaseHelper.GetStockOutIemiDataByProdCode(mQueryIMEI, prodCode, custCode, customerRds);
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_stock_report_retailer_imei);
        mDetailsDialog.setCancelable(true);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(prodDesc);

        ListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        StockReportAdapterImei ReportAdapterObject = new StockReportAdapterImei(mContext, R.layout.stock_report_child_retailer_imei, mOrderReportDetailsListDialog);
        dialogList.setAdapter(ReportAdapterObject);

        mDetailsDialog.show();
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
            testLowerLevelEmployeeOrNot();
            if (!mEmployeeMasterLowerLeavesDetails.isEmpty()) {
                retailerAppisLowerMostLevelEmp = true;
                ShowConditionofATA();
            } else {
                retailerAppisLowerMostLevelEmp = false;
                showCustomerWiseStockList();
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
                testLowerLevelEmployeeOrNot();
                showCustomerWiseStockList();
            } else {
                FetchTransactionData(2);
            }
            dialgoCondition.cancel();
        });
        dialgoCondition.show();
    }

    private void testLowerLevelEmployeeOrNot() {
        mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetailsWithOutVertical(selectedEmpRetailerApp);
        retailerAppisLowerMostLevelEmp = mEmployeeMasterLowerLeavesDetails.isEmpty();
    }

    public void showCustomerWiseStockList() {
        String lowerleaves = mAceDnsDatabaseHelper.GetLowerleavesOfEmployee(selectedEmpRetailerApp);
        customerList = mAceDnsDatabaseHelper.getCustomerListByEmpCodeForStockOutSummary(lowerleaves);
        if (!customerList.isEmpty()) {
            FetchTransactionData(3);
        } else {
            Utils.showToast(mContext, "No relevant data found.");
            customerEmpSelectionProcess();
        }
    }

    public void InitializeView() {
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        mImageViewShowDuration = findViewById(R.id.image_clk);
        mImageViewHideDuration = findViewById(R.id.image_go);
        mRelativeLayoutDuration = findViewById(R.id.duration_layout);
        mCustomDateLayout = findViewById(R.id.custom_date_layout);
        mButtonBack = findViewById(R.id.back);
        mFrameLayoutToday = findViewById(R.id.btn_today);
        mFrameLayoutMTD = findViewById(R.id.btn_mtd);
        mFrameLayoutCustom = findViewById(R.id.btn_custom);
        stkOutOthersLayout = findViewById(R.id.stkOutOthersLayout);
        mTextViewStartDate = findViewById(R.id.txt_start_date);
        mTextViewEndDate = findViewById(R.id.txt_end_date);
        textViewTotalOpeningStock = findViewById(R.id.textViewTotalOpeningStock);
        textViewTotalBilledQty = findViewById(R.id.textViewTotalBilledQty);
        textViewTotalStockOutSelf = findViewById(R.id.textViewTotalStockOutSelf);
        textViewTotalStockOutOthers = findViewById(R.id.textViewTotalStockOutOthers);
        textViewTotalClosingStock = findViewById(R.id.textViewTotalClosingStock);
        dateRangeTV = findViewById(R.id.dateRangeTV);
        mButtonStartDate = findViewById(R.id.btn_start_date);
        mButtonEndDate = findViewById(R.id.btn_end_date);
        mButtonSubmitDate = findViewById(R.id.btn_date_done);
        mListViewList = findViewById(R.id.listView);

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);
        mFrameLayoutToday.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);
        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");

        mListViewList.setEmptyView(findViewById(R.id.empty_text_view));
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    public void FetchTransactionData(final int whattodo) {
        mQuery = BuildQuery(SELECTION);
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 2:
                        mEmployeeMasterLowerLeavesDetails = mAceDnsDatabaseHelper.GetHierarchyEmployeeDetailsWithOutVertical(selectedEmpRetailerApp);
                        mEmployeeMasterDetailsList = new ArrayList<>();
                        if (!mEmployeeMasterLowerLeavesDetails.isEmpty()) {
                            retailerAppisLowerMostLevelEmp = true;
                            EmployeeMasterDetails obj;
                            for (int count = 0; count < mEmployeeMasterLowerLeavesDetails.size(); count++) {
                                obj = new EmployeeMasterDetails();
                                obj.setEmpCode(mEmployeeMasterLowerLeavesDetails.get(count).getEmpCode());
                                obj.setEmpName(mEmployeeMasterLowerLeavesDetails.get(count).getEmpName());
                                mEmployeeMasterDetailsList.add(obj);
                            }
                        } else {
                            retailerAppisLowerMostLevelEmp = false;
                        }
                        break;
                    case 3:
                        TotalOpeningStock = 0;
                        TotalBilledQty = 0;
                        TotalStockOutSelf = 0;
                        TotalStockOutOthers = 0;
                        TotalClosingStock = 0;
                        mOrderReportDetailsList = mAceDnsDatabaseHelper.GetStockDataRetailerAppCustomerWise(mQuery, customerList, conditionStockOutQtySelf, conditionStockOutQtyOthers, conditionForOpeningStock, conditionForOpeningStock2, dateRangeActivationDate, DateRangeActivationDateOpeningStock);
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
            FetchTransactionData(3);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            FetchTransactionData(3);
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
                    FetchTransactionData(3);
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

    @SuppressLint("SetTextI18n")
    public String BuildQuery(int select) {
        String mToday = mDFormatBackEnd.format(currentDate);
        String condition = "";
        switch (select) {
            case 1:
                mToday = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mToday);
                condition = "CPB.invoice_date='" + mToday + "' ";
                conditionStockOutQtySelf = "CPB.stock_out_date!='0000-00-00' AND CPB.stock_out_date='" + mToday + "'";
                conditionStockOutQtyOthers = "CPB.stock_out_date!='0000-00-00' AND CPB.stock_out_date='" + mToday + "' ";
                conditionForOpeningStock = "stock_out_date  < '" + mToday + "'";
                conditionForOpeningStock2 = "invoice_date < '" + mToday + "'";
                dateRangeActivationDate = "CPB.stock_out_date='0000-00-00' AND substr(CPB.activation_date,1,10)='" + mToday + "'";
                DateRangeActivationDateOpeningStock = "substr(activation_date,1,10) < '" + mToday + "'";
                dateRangeTV.setText("Stock Report On " + Utils.changeDateFormat("yyyy-MM-dd", "dd/MM/yyyy", mToday));
                mQueryIMEI = "CPB.activation_date='0000-00-00 00:00:00' AND CPB.stock_out_date='0000-00-00' ";
                break;
            case 2:
                GETCurrentMonthYear();
                condition = "substr(CPB.invoice_date,1,4)='" + mCurrentYear + "' AND substr(CPB.invoice_date,6,2)='" + mCurrentMonth + "' ";
                conditionStockOutQtySelf = "CPB.stock_out_date!='0000-00-00' AND substr(CPB.stock_out_date,1,4)='" + mCurrentYear + "' AND substr(CPB.stock_out_date,6,2)='" + mCurrentMonth + "'";
                conditionStockOutQtyOthers = "CPB.stock_out_date!='0000-00-00' AND substr(CPB.stock_out_date,1,4)='" + mCurrentYear + "' AND substr(CPB.stock_out_date,6,2)='" + mCurrentMonth + "' ";
                conditionForOpeningStock = "stock_out_date < '" + mCurrentYear + "-" + mCurrentMonth + "-01'";
                conditionForOpeningStock2 = "invoice_date < '" + mCurrentYear + "-" + mCurrentMonth + "-01'";
                dateRangeActivationDate = "CPB.stock_out_date='0000-00-00' AND CPB.activation_date!='0000-00-00 00:00:00' AND substr(CPB.activation_date,1,4)='" + mCurrentYear + "' AND substr(CPB.activation_date,6,2)='" + mCurrentMonth + "'";
                DateRangeActivationDateOpeningStock = "substr(activation_date,1,10)< '" + mCurrentYear + "-" + mCurrentMonth + "-01'";
                dateRangeTV.setText("Stock Report On " + mCurrentMonth + "/" + mCurrentYear);
                mQueryIMEI = " CPB.activation_date='0000-00-00 00:00:00' AND CPB.stock_out_date='0000-00-00' ";
                break;
            case 3:
                mStartDate = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mStartDate);
                mEndDate = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mEndDate);
                condition = "Stock Report CPB.invoice_date BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
                conditionStockOutQtySelf = "CPB.stock_out_date!='0000-00-00' AND CPB.stock_out_date BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'  AND  CPB.customer_code=CPB.stock_out_customer_code ";
                conditionStockOutQtyOthers = "CPB.stock_out_date!='0000-00-00' AND CPB.stock_out_date BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'";
                conditionForOpeningStock = "stock_out_date < '" + mStartDate + "'";
                conditionForOpeningStock2 = "invoice_date < '" + mStartDate + "'";
                dateRangeActivationDate = "CPB.stock_out_date='0000-00-00' AND CPB.activation_date!='0000-00-00 00:00:00'  AND substr(CPB.activation_date,1,10) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'";
                DateRangeActivationDateOpeningStock = "substr(activation_date,1,10) < '" + mStartDate + "'";
                dateRangeTV.setText("From " + Utils.changeDateFormat("yyyy-MM-dd", "dd/MM/yyyy", mStartDate) + " to " + Utils.changeDateFormat("yyyy-MM-dd", "dd/MM/yyyy", mEndDate));
                mQueryIMEI = " CPB.activation_date='0000-00-00 00:00:00' AND CPB.stock_out_date='0000-00-00' ";
                break;
        }
        return condition;
    }

    public void GETCurrentMonthYear() {
        mCurrentYear = Constants.dateString.substring(0, 4);
        mCurrentMonth = Constants.dateString.substring(4, 6);
    }

    @SuppressLint("SetTextI18n")
    public void showMasterListDialog() {
        if (!productMasterList.isEmpty()) {
            tempProductList = new ArrayList<>(productMasterList);
            prodAdapter = new ProductMasterAdapter(mContext, R.layout.product_list_child, tempProductList);

            final Dialog masterDialog = new Dialog(mContext, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = masterDialog.findViewById(R.id.title);
            title.setText("Please select a product");
            EditText searchText = masterDialog.findViewById(R.id.autoCompleteTextView1);

            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductList();
                    }
                    lastStr = str;
                    filterProductArray(str.length(), str);
                    prodAdapter.notifyDataSetChanged();
                }
            });
            prodQtyRateListView = masterDialog.findViewById(R.id.list);

            prodQtyRateListView.setAdapter(prodAdapter);

            prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> masterDialog.cancel());
            Button btnCancel = masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(v -> masterDialog.cancel());

            masterDialog.show();
        } else {
            Toast.makeText(mContext, "No product found! Please contact admin!", Toast.LENGTH_LONG).show();
            finish();
        }
    }

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        tempProductList.addAll(productMasterList);
    }

    public void filterProductArray(int strCnt, String charVal) {
        int size = tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductList.get(ii).getDesc().length() >= strCnt) {
                if (!tempProductList.get(ii).getDesc().toUpperCase().contains(charVal.toUpperCase())) {
                    tempProductList.remove(tempProductList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductList.remove(tempProductList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }
}
