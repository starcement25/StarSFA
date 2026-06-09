package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
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
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.LedgerReportAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.OrderDetails;
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

public class ActivityLedgerReport extends FragmentActivity implements OnClickListener {

    public static Button mButtonBack = null;
    public static Button mButtonStartDate = null;
    public static Button mButtonEndDate = null;
    public static Button mButtonSubmitDate = null;
    public static ImageView mImageViewHeaderLogo = null;
    public static ImageView mImageViewShowDuration = null;
    public static ImageView mImageViewHideDuration = null;
    public static TextView mTextViewStartDate = null;
    public static TextView mTextViewEndDate = null;
    public static TextView txt_total = null;
    public static TextView totalOrBalanceTv = null;
    public static TextView dateRangeTV = null;
    public static TextView openingStockTV = null;
    public static ListView mListViewList = null;
    public static FrameLayout mFrameLayoutToday = null;
    public static FrameLayout mFrameLayoutMTD = null;
    public static FrameLayout mFrameLayoutCustom = null;
    public static RelativeLayout mRelativeLayoutDuration = null;
    public static LinearLayout mCustomDateLayout = null;
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    public int SELECTION = 0;
    public AceDnsDatabase mAceDnsDatabaseHelper;
    String lastStr = "";
    ArrayList<ProductMasterDetails> productMasterList;
    LedgerReportAdapter ReportAdapterObject;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    ProductMasterAdapter prodAdapter;
    ArrayAdapter<String> spinnerArrayAdapter;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ArrayList<OrderDetails> mOrderReportDetailsList;
    ArrayList<ProductMasterDetails> mOrderReportDetailsListDialog;
    ArrayList<String> spinnerArrayCustomers;
    private boolean isStartDate = false;
    private String mStartDate = "";
    private String mEndDate = "";
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mToday = "";
    private String mQuery = "";
    private String mQuery2 = "";
    private String conditionForOpeningStock = "";
    private String conditionForOpeningStock2 = "";
    private String debtorOrCreditor = "";
    private String selectedSku = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_ledger_report);
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

        mContext = ActivityLedgerReport.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        InitializeView();

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mButtonBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate = date;
                    mTextViewStartDate.setText(mDFormatFrontEnd.format(date));
                    //mFStartDate=mDFormatFrontEnd.format(date);
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    //mFEndDate=mDFormatFrontEnd.format(date);
                    mEndDate = mDFormatBackEnd.format(date);
                }
                dialogCaldroidFragment.dismiss();
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {

            }

            @Override
            public void onCaldroidViewCreated() {

            }
        };


        mReportHandler = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivityLedgerReport.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (job) {
                            case 1:
                                ReportAdapterObject = new LedgerReportAdapter(mContext, R.layout.ledger_report_child, mOrderReportDetailsList, false);
                                mListViewList.setAdapter(ReportAdapterObject);
                                calculateTotalAndShow();
//								mListViewList.setOnItemClickListener(new OnItemClickListener() {
//									@Override
//									public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,long arg3)
//									{
//										String prodCodeOfClickedItem=((TextView) arg1.findViewById(R.id.invisibleProdCode)).getText().toString();
//										String prodDescOfClickedItem=((TextView) arg1.findViewById(R.id.prodDescTV)).getText().toString();
//										ShowProductDetailsDialog(prodCodeOfClickedItem,prodDescOfClickedItem);
//									}
//								});
                                break;

                        }
                    }
                });
            }
        };

        if (SELECTION == 0) {
            SELECTION = 1;
        }
        ChangeBackground(SELECTION);

        ShowDebtorCreditorDialog();


    }

    private void calculateTotalAndShow() {
        try {
            openingStockTV.setText("0.00 ");
            txt_total.setText("0.00");
            double totalIn = 0, totalOut = 0;
            for (int i = 0; i < mOrderReportDetailsList.size(); i++) {
                OrderDetails currentItem = mOrderReportDetailsList.get(i);
                if (Utils.isNumeric(currentItem.getAmount())) {
                    double qtyForCurrentItem = Double.parseDouble(currentItem.getAmount());
                    if (currentItem.getType().equalsIgnoreCase("P")) {

                        totalOut = totalOut + qtyForCurrentItem;
                    } else//pb,sb
                    {
                        totalIn = totalIn + qtyForCurrentItem;
                    }
                }

            }

            double totalOS = mAceDnsDatabaseHelper.GetOpeningStockLedger(conditionForOpeningStock, conditionForOpeningStock2);
            double closingStock = totalOS + totalIn - totalOut;
            openingStockTV.setText(defaultFormat.format(totalOS));
            txt_total.setText(defaultFormat.format(closingStock));
        } catch (Exception e) {

        }

    }

    public void showCustomerSpinner() {
        final Spinner customerSpinner = (Spinner) findViewById(R.id.customerSpinner);
        TextView debtorCreditorTV = findViewById(R.id.debtorCreditorTV);
        debtorCreditorTV.setText(debtorOrCreditor + " : ");
        mQuery = BuildQuery(SELECTION);
        final ArrayList<CustomerDetails> customerList = mAceDnsDatabaseHelper.getCustomerListAsDebtorOrCreditor(debtorOrCreditor, mQuery, mQuery2);
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
    }

    public void ShowDebtorCreditorDialog() {
        final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
        dialgoCondition.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));

        RadioButton radioEdit = dialgoCondition.findViewById(R.id.radioEdit);
        radioEdit.setText("Debtor");
        RadioButton radioRedundant = dialgoCondition.findViewById(R.id.radioRedundant);

        radioRedundant.setText("Creditor");
        TextView txtMsg = (TextView) dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select an Option.");

        final RadioGroup radioSelectionGroup = (RadioGroup) dialgoCondition.findViewById(R.id.radioSelect);


        radioSelectionGroup
                .setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                    public void onCheckedChanged(RadioGroup group, int checkedId) {
                        RadioButton radioSelection = (RadioButton) dialgoCondition
                                .findViewById(checkedId);
                        debtorOrCreditor = radioSelection.getText().toString().trim();
                        dialgoCondition.cancel();
                        showCustomerSpinner();
                    }
                });
        dialgoCondition.show();
    }


    private void productSelectionProcess() {
        productMasterList = mAceDnsDatabaseHelper.getAllProductsOfProductMaster();
        if (productMasterList.size() == 1) {
            selectedSku = productMasterList.get(0).getProdCode();
        } else {
            showMasterListDialog();
        }
    }

    public void selectProd(View v) {
        productSelectionProcess();
    }


    public void InitializeView() {
        openingStockTV = findViewById(R.id.openingStockTV);
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);
        //mImageViewSaudaBooked=(ImageView) findViewById(R.id.imgageViewSaudaDetails);

        mImageViewShowDuration = (ImageView) findViewById(R.id.image_clk);
        mImageViewHideDuration = (ImageView) findViewById(R.id.image_go);

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);

        mRelativeLayoutDuration = (RelativeLayout) findViewById(R.id.duration_layout);
        mCustomDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);

        mButtonBack = (Button) findViewById(R.id.back);

        mFrameLayoutToday = (FrameLayout) findViewById(R.id.btn_today);
        mFrameLayoutMTD = (FrameLayout) findViewById(R.id.btn_mtd);
        mFrameLayoutCustom = (FrameLayout) findViewById(R.id.btn_custom);

        mFrameLayoutToday.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);

        mTextViewStartDate = (TextView) findViewById(R.id.txt_start_date);
        mTextViewEndDate = (TextView) findViewById(R.id.txt_end_date);
        txt_total = (TextView) findViewById(R.id.txt_total);
        totalOrBalanceTv = (TextView) findViewById(R.id.totalOrBalanceTv);
        dateRangeTV = (TextView) findViewById(R.id.dateRangeTV);
        openingStockTV = (TextView) findViewById(R.id.openingStockTV);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");

        mButtonStartDate = (Button) findViewById(R.id.btn_start_date);
        mButtonEndDate = (Button) findViewById(R.id.btn_end_date);
        mButtonSubmitDate = (Button) findViewById(R.id.btn_date_done);

        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);

        mListViewList = (ListView) findViewById(R.id.listView);
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
            showCustomerSpinner();

        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            showCustomerSpinner();

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
                    showCustomerSpinner();
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

        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        mOrderReportDetailsList = mAceDnsDatabaseHelper.GetDebtorCreditorData(mQuery, mQuery2);

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
        String transactionType = "";
        if (debtorOrCreditor.equalsIgnoreCase("Debtor")) {
            transactionType = "AND transaction_type IN('SB' ) ";
        } else {
            transactionType = "AND transaction_type IN('PB' ) ";
        }
        String condition = "";
        switch (select) {
            case 1:
                mToday = mDFormatBackEnd.format(currentDate);

                condition = "substr(order_no,7,8)='" + mToday + "' " + transactionType;
                mQuery2 = "substr(receipt_id,7,8)='" + mToday + "' ";
                conditionForOpeningStock = "substr(order_no,7,8) < '" + mToday + "'" + transactionType;
                conditionForOpeningStock2 = "substr(receipt_id,7,8) < '" + mToday + "'";
                dateRangeTV.setText("On " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mToday));
                break;
            case 2:
                GETCurrentMonthYear();
                condition = "substr(order_no,7,4)='" + mCurrentYear + "' AND substr(order_no,11,2)='" + mCurrentMonth + "' " + transactionType;
                mQuery2 = "substr(receipt_id,7,4)='" + mCurrentYear + "' AND substr(receipt_id,11,2)='" + mCurrentMonth + "' ";
                conditionForOpeningStock = "substr(order_no,7,8) < '" + mCurrentYear + "" + mCurrentMonth + "01'" + transactionType;
                conditionForOpeningStock2 = "substr(receipt_id,7,8) < '" + mCurrentYear + "" + mCurrentMonth + "01'";
                dateRangeTV.setText("On " + mCurrentMonth + "/" + mCurrentYear);


                break;
            case 3:
                condition = "substr(order_no,7,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' " + transactionType;
                mQuery2 = "substr(receipt_id,7,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
                conditionForOpeningStock = "substr(order_no,7,8) < '" + mStartDate + "'" + transactionType;
                conditionForOpeningStock2 = "substr(receipt_id,7,8) < '" + mStartDate + "'";
                dateRangeTV.setText("From " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mStartDate) + " to " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mEndDate));
                break;
        }
        return condition;
    }

    public void GETCurrentMonthYear() {
        mCurrentYear = Constants.dateString.substring(0, 4);
        mCurrentMonth = Constants.dateString.substring(4, 6);
    }

    public void showMasterListDialog() {
        if (productMasterList.size() > 0) {
            tempProductList = new ArrayList<>(productMasterList);
            prodAdapter = new ProductMasterAdapter(mContext, R.layout.product_list_child, tempProductList);

            final Dialog masterDialog = new Dialog(mContext, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please select a product");
            EditText searchText = (EditText) masterDialog.findViewById(R.id.autoCompleteTextView1);

            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
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
            prodQtyRateListView = (ListView) masterDialog.findViewById(R.id.list);

            prodQtyRateListView.setAdapter(prodAdapter);

            prodQtyRateListView.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {

                    ProductMasterDetails currentProductMasterObj = tempProductList.get(arg2);
                    selectedSku = currentProductMasterObj.getProdCode();

                    masterDialog.cancel();

                }
            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    masterDialog.cancel();
                }
            });

            masterDialog.show();
        } else {
            Toast.makeText(mContext, "No product found! Please Synchronize Data!", Toast.LENGTH_LONG).show();
            finish();
        }
    }

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            tempProductList.add(productMasterList.get(kk));
        }

    }

    public void filterProductArray(int strCnt, String charVal) {
        int size = tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductList.get(ii).getDesc().length() >= strCnt) {
                if (tempProductList.get(ii).getDesc().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
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
