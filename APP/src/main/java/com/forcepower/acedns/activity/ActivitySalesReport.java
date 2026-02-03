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
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.SalesReportAdapter;
import com.forcepower.acedns.bean.OrderReportDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.tempProductList;

public class ActivitySalesReport extends FragmentActivity implements OnClickListener {
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
    public static TextView prodTV = null;
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
    SalesReportAdapter SalesReportAdapterObject;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    ProductMasterAdapter prodAdapter;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ArrayList<OrderReportDetails> mOrderReportDetailsList;
    ArrayList<OrderReportDetails> mOrderReportDetailsList2ForOpeningStock;
    private boolean isStartDate = false;
    private String mStartDate = "";
    private String mEndDate = "";
    private String saleType = "";
    private String conditionForOpeningStock = "";
    private String mTime = "";
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mToday = "";
    private String mQuery = "";
    private String selectedSku = "";

    @SuppressLint({"SimpleDateFormat", "SetTextI18n", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sale_report);
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

        mContext = ActivitySalesReport.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        TextView txtVersion =  findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        InitializeView();
        mToday = mDFormatBackEnd.format(currentDate);
        mTime = Constants.dateString;
        GETCurrentMonthYear(mTime);
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
                ActivitySalesReport.this.runOnUiThread(() -> {
                    if (job == 1) {
                        SalesReportAdapterObject = new SalesReportAdapter(mContext, R.layout.sale_report_child, mOrderReportDetailsList);
                        mListViewList.setAdapter(SalesReportAdapterObject);
                        calculateTotalAndShow();
                    }
                });
            }
        };

        if (SELECTION == 0) {
            SELECTION = 1;
        }
        ChangeBackground(SELECTION);

        productSelectionProcess();
    }

    private void productSelectionProcess() {
        productMasterList = mAceDnsDatabaseHelper.getAllProductsOfProductMaster();
        if (productMasterList.size() == 1) {
            selectedSku = productMasterList.get(0).getProdCode();
            prodTV.setText(productMasterList.get(0).getDesc());
            ShowSaleTypeSelectionDialog();
        } else {
            showMasterListDialog();
        }
    }

    @SuppressLint("SetTextI18n")
    private void calculateTotalAndShow() {
        try {
            openingStockTV.setText("Opening Stock: ");
            txt_total.setText("0.00");
            double totalQty = 0, totalIn = 0, totalOut = 0;
            for (int i = 0; i < mOrderReportDetailsList.size(); i++) {
                OrderReportDetails currentItem = mOrderReportDetailsList.get(i);
                double qtyForCurrentItem = Double.parseDouble(currentItem.getAmount());
                if (saleType.equalsIgnoreCase("in") || saleType.equalsIgnoreCase("out")) {
                    totalQty = totalQty + qtyForCurrentItem;
                } else {
                    if (currentItem.getType().equalsIgnoreCase("pb") || currentItem.getType().equalsIgnoreCase("bt")) {
                        totalIn = totalIn + qtyForCurrentItem;
                    } else {
                        totalOut = totalOut + qtyForCurrentItem;
                    }
                }
            }
            String amountPref = "";
            if (saleType.equalsIgnoreCase("out")) {
                amountPref = "-";
            }
            txt_total.setText(amountPref + defaultFormat.format(totalQty));
            if (saleType.equalsIgnoreCase("both")) {
                totalOrBalanceTv.setText("Balance");
                mOrderReportDetailsList2ForOpeningStock = mAceDnsDatabaseHelper.GetOpeningStockSales(conditionForOpeningStock, selectedSku);
                double totalQtyOS = 0;
                for (int i = 0; i < mOrderReportDetailsList2ForOpeningStock.size(); i++) {
                    OrderReportDetails currentItem = mOrderReportDetailsList2ForOpeningStock.get(i);
                    double qtyForCurrentItem = Double.parseDouble(currentItem.getAmount());
                    if (currentItem.getType().equalsIgnoreCase("pb") || currentItem.getType().equalsIgnoreCase("bt")) {
                        totalQtyOS = totalQtyOS + qtyForCurrentItem;
                    } else {
                        totalQtyOS = totalQtyOS - qtyForCurrentItem;
                    }
                }
                double closingStock = totalQtyOS + totalIn - totalOut;
                openingStockTV.setText("Opening Stock: " + defaultFormat.format(totalQtyOS));
                txt_total.setText(defaultFormat.format(closingStock));
            } else {
                totalOrBalanceTv.setText("Total");
                openingStockTV.setText("Opening Stock: N/A");
            }
        } catch (Exception ignored) {}
    }

    public void selectProd(View v) {
        productSelectionProcess();
    }


    public void InitializeView() {
        mImageViewHeaderLogo =  findViewById(R.id.imagelogo);
        mImageViewShowDuration =  findViewById(R.id.image_clk);
        mImageViewHideDuration =  findViewById(R.id.image_go);
        mRelativeLayoutDuration =  findViewById(R.id.duration_layout);
        mCustomDateLayout =  findViewById(R.id.custom_date_layout);
        mButtonBack =  findViewById(R.id.back);
        mFrameLayoutToday =  findViewById(R.id.btn_today);
        mFrameLayoutMTD =  findViewById(R.id.btn_mtd);
        mFrameLayoutCustom =  findViewById(R.id.btn_custom);
        mTextViewStartDate =  findViewById(R.id.txt_start_date);
        mTextViewEndDate =  findViewById(R.id.txt_end_date);
        txt_total =  findViewById(R.id.txt_total);
        totalOrBalanceTv =  findViewById(R.id.totalOrBalanceTv);
        prodTV =  findViewById(R.id.prodTV);
        dateRangeTV =  findViewById(R.id.dateRangeTV);
        openingStockTV =  findViewById(R.id.openingStockTV);
        mButtonStartDate =  findViewById(R.id.btn_start_date);
        mButtonEndDate =  findViewById(R.id.btn_end_date);
        mButtonSubmitDate =  findViewById(R.id.btn_date_done);
        mListViewList =  findViewById(R.id.listView);

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
            FetchSaudaTransactionLogData(1);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
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
                    mTextViewStartDate.setText("");
                    mTextViewEndDate.setText("");
                    FetchSaudaTransactionLogData(1);
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

    public void FetchSaudaTransactionLogData(final int whattodo) {
        mQuery = BuildQuery(SELECTION);
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                if (whattodo == 1) {
                    mOrderReportDetailsList = mAceDnsDatabaseHelper.GetSalesReportData(mQuery, selectedSku);
                }
                Message msg = mReportHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("JOBDONE", whattodo);
                msg.setData(b);
                mReportHandler.sendMessage(msg);
            }
        }.start();
    }

    public void ShowSaleTypeSelectionDialog() {
        final Dialog payTypeDialog = new Dialog(mContext, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.sale_type_report_dialog);
        
        RadioGroup payTypeOption =  payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener((group, checkedId) -> {
            int radioButtonID = group.getCheckedRadioButtonId();
            View radioButton = group.findViewById(radioButtonID);
            int selectedRadio = group.indexOfChild(radioButton);

            if (selectedRadio == 0) {
                saleType = "IN";
            } else if (selectedRadio == 1) {
                saleType = "OUT";
            } else {
                saleType = "BOTH";
            }
            payTypeDialog.cancel();

            FetchSaudaTransactionLogData(1);
        });
        payTypeDialog.show();
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
        String transactionType = "";
        if (saleType.equalsIgnoreCase("in")) {
            transactionType = "'PB','BT'";
        } else if (saleType.equalsIgnoreCase("out")) {
            transactionType = "'SB','ST'";
        } else//both
        {
            transactionType = "'PB','BT','SB','ST'";
        }
        String condition = "";
        switch (select) {
            case 1:
                condition = "substr(OH.order_no,-14,8)='" + mToday + "' AND OH.transaction_type IN(" + transactionType + ")";
                dateRangeTV.setText("On " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mToday));
                conditionForOpeningStock = "substr(OH.order_no,-14,8) < '" + mToday + "'";
                break;
            case 2:
                condition = "substr(OH.order_no,-14,4)='" + mCurrentYear + "' AND substr(OH.order_no,-10,2)='" + mCurrentMonth + "' AND OH.transaction_type IN(" + transactionType + ")";
                dateRangeTV.setText("On " + mCurrentMonth + "/" + mCurrentYear);
                conditionForOpeningStock = "substr(OH.order_no,-14,8) < '" + mCurrentYear + mCurrentMonth + "01'";

                break;
            case 3:
                condition = "substr(OH.order_no,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' AND OH.transaction_type IN(" + transactionType + ")";
                dateRangeTV.setText("Between " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mStartDate) + " & " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mEndDate));
                conditionForOpeningStock = "substr(OH.order_no,-14,8) < '" + mStartDate + "'";

                break;
        }
        return condition;
    }

    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
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
            TextView title =  masterDialog.findViewById(R.id.title);
            title.setText("Please select a product");
            EditText searchText =  masterDialog.findViewById(R.id.autoCompleteTextView1);

            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
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
            prodQtyRateListView =  masterDialog.findViewById(R.id.list);

            prodQtyRateListView.setAdapter(prodAdapter);

            prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                ProductMasterDetails currentProductMasterObj = tempProductList.get(arg2);
                selectedSku = currentProductMasterObj.getProdCode();
                prodTV.setText(currentProductMasterObj.getDesc());
                ShowSaleTypeSelectionDialog();
                masterDialog.cancel();
            });
            Button btnCancel =  masterDialog.findViewById(R.id.btn_ok);
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
