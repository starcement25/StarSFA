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
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.StockBalanceReportAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
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
import static com.forcepower.acedns.constants.Constants.tempProductList;

public class ActivityStockBalanceReport extends FragmentActivity implements OnClickListener {
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
    public static TextView txt_total = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView totalOrBalanceTv = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView dateRangeTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView openingStockTV = null;
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

    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    public int SELECTION = 0;
    public AceDnsDatabase mAceDnsDatabaseHelper;
    String lastStr = "";
    ArrayList<ProductMasterDetails> productMasterList;
    StockBalanceReportAdapter ReportAdapterObject;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    ProductMasterAdapter prodAdapter;
    ArrayAdapter<String> spinnerArrayAdapter;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ArrayList<ProductMasterDetails> mOrderReportDetailsList;
    ArrayList<ProductMasterDetails> mOrderReportDetailsListDialog;
    ArrayList<String> spinnerArrayCustomers;
    private boolean isStartDate = false;
    private String mStartDate = "";
    private String mEndDate = "";
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mQuery = "";

    @SuppressLint({"SimpleDateFormat", "SetTextI18n", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_stock_balance_report);
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

        mContext = ActivityStockBalanceReport.this;
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
                ActivityStockBalanceReport.this.runOnUiThread(() -> {
                    if (job == 1) {
                        ReportAdapterObject = new StockBalanceReportAdapter(mContext, R.layout.stock_balance_report_child, mOrderReportDetailsList, false);
                        mListViewList.setAdapter(ReportAdapterObject);
                        mListViewList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                            String prodCodeOfClickedItem = ((TextView) arg1.findViewById(R.id.invisibleProdCode)).getText().toString();
                            String prodDescOfClickedItem = ((TextView) arg1.findViewById(R.id.prodDescTV)).getText().toString();
                            ShowProductDetailsDialog(prodCodeOfClickedItem, prodDescOfClickedItem);
                        });
                    }
                });
            }
        };

        if (SELECTION == 0) {
            SELECTION = 1;
        }
        ChangeBackground(SELECTION);
        Utils.headerFooterIconChangesForRetailerApp(mContext, false);

        showCustomerSpinner();
    }

    public void ShowProductDetailsDialog(final String prodCode, final String prodDesc) {
        mOrderReportDetailsListDialog = mAceDnsDatabaseHelper.GetStockBalanceDataByProdCode(mQuery, prodCode);
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_stock_balance_report);
        mDetailsDialog.setCancelable(true);
        Button back = mDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(prodDesc);

        ListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        StockBalanceReportAdapter ReportAdapterObject = new StockBalanceReportAdapter(mContext, R.layout.stock_balance_report_child, mOrderReportDetailsListDialog, true);
        dialogList.setAdapter(ReportAdapterObject);

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> mDetailsDialog.cancel());
        mDetailsDialog.show();
    }

    public void showCustomerSpinner() {
        final ArrayList<CustomerDetails> customerList = mAceDnsDatabaseHelper.getCustomerListStockBalanceReport();
        if (!customerList.isEmpty()) {
            final Spinner customerSpinner = findViewById(R.id.customerSpinner);
            spinnerArrayCustomers = new ArrayList<>();
            for (int i = 0; i < customerList.size(); i++) {
                spinnerArrayCustomers.add(customerList.get(i).getCustomerName());
            }
            spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArrayCustomers);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            customerSpinner.setAdapter(spinnerArrayAdapter);

            customerSpinner.setSelection(0);
            customerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                    Constants.selectedCustomer = customerList.get(i);
                    FetchTransactionData(1);
                }

                @Override
                public void onNothingSelected(AdapterView<?> adapterView) {
                }
            });
        } else {
            Utils.showToast(mContext, "No relevant data found.");
            finish();
        }
    }

    private void productSelectionProcess() {
        productMasterList = mAceDnsDatabaseHelper.getAllProductsOfProductMaster();
        if (productMasterList.size() != 1) {
            showMasterListDialog();
        }
    }

    public void selectProd(View v) {
        productSelectionProcess();
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
        mTextViewStartDate = findViewById(R.id.txt_start_date);
        mTextViewEndDate = findViewById(R.id.txt_end_date);
        txt_total = findViewById(R.id.txt_total);
        totalOrBalanceTv = findViewById(R.id.totalOrBalanceTv);
        dateRangeTV = findViewById(R.id.dateRangeTV);
        openingStockTV = findViewById(R.id.openingStockTV);
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
            FetchTransactionData(1);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            FetchTransactionData(1);
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
                    FetchTransactionData(1);
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

    public void FetchTransactionData(final int whattodo) {
        mQuery = BuildQuery(SELECTION);
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                if (whattodo == 1) {
                    mOrderReportDetailsList = mAceDnsDatabaseHelper.GetStockBalanceData(mQuery);
                }
                Message msg = mReportHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("JOBDONE", whattodo);
                msg.setData(b);
                mReportHandler.sendMessage(msg);
            }
        }.start();
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

    public String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                String mToday = mDFormatBackEnd.format(currentDate);
                mToday = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mToday);
                condition = "substr(allocation_date,1,10)='" + mToday + "' ";
                break;
            case 2:
                GETCurrentMonthYear();
                condition = "substr(allocation_date,1,4)='" + mCurrentYear + "' AND substr(allocation_date,6,2)='" + mCurrentMonth + "' ";
                break;
            case 3:
                mStartDate = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mStartDate);
                mEndDate = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mEndDate);
                condition = "substr(allocation_date,1,10) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
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

            prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                masterDialog.cancel();
            });
            Button btnCancel = masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(v -> masterDialog.cancel());

            masterDialog.show();
        } else {
            Toast.makeText(mContext, "No product found! Please Synchronize Data!", Toast.LENGTH_LONG).show();
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
