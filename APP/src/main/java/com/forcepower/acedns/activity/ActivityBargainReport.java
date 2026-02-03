package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ExpandableListView;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BargainReportAdapter;
import com.forcepower.acedns.adapter.ExpandableListAdapterBargainReport;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.bean.OrderReportDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.math.BigDecimal;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;
import static com.forcepower.acedns.constants.Constants.listChildDataGlobal;
import static com.forcepower.acedns.constants.Constants.listDataHeaderGlobal;
import static com.forcepower.acedns.constants.Constants.mOrderReportDetailsListGlobal;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.tempProductList;

public class ActivityBargainReport extends AppCompatActivity implements OnClickListener {

    public static Button mButtonStartDate = null;
    public static Button mButtonEndDate = null;
    public static Button mButtonSubmitDate = null;
    public static ImageView mImageViewShowDuration = null;
    public static ImageView mImageViewHideDuration = null;
    public static TextView mTextViewStartDate = null;
    public static TextView mTextViewEndDate = null;
    public static TextView txt_total = null;
    public static TextView totalOrBalanceTv = null;
    public static TextView dateRangeTV = null;
    public static TextView openingStockTV = null;
    public static TextView prodTV = null;
    public static ListView mListViewList = null;
    public static FrameLayout mFrameLayoutToday = null;
    public static FrameLayout mFrameLayoutMTD = null;
    public static FrameLayout mFrameLayoutCustom = null;
    public static RelativeLayout mRelativeLayoutDuration = null;
    public static LinearLayout mCustomDateLayout = null;
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private String mCustomerName = "";
    private String currentSelectedCustomerCode = "";
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    public int SELECTION = 0;
    public AceDnsDatabase mAceDnsDatabaseHelper;
    String lastStr = "";
    ArrayList<ProductMasterDetails> productMasterList;
    BargainReportAdapter SalesReportAdapterObject;
    ExpandableListAdapterBargainReport bargainReport;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    ProductMasterAdapter prodAdapter;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ArrayList<OrderReportDetails> mOrderReportDetailsList;
    ArrayList<OrderReportDetails> mOrderReportDetailsListByCustomer;
    ArrayList<OrderReportDetails> mOrderReportDetailsList2ForOpeningStock;
    List<String> listDataHeader;
    HashMap<String, List<OrderReportDetails>> listChildData;
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
    private String mTotalAmount = "";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_bargain_report);
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
        mContext = ActivityBargainReport.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        Toolbar myToolbar = findViewById(R.id.my_toolbar);
        setSupportActionBar(myToolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayShowTitleEnabled(false);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        InitializeView();
        mToday = mDFormatBackEnd.format(currentDate);
        mTime = Constants.dateString;
        GETCurrentMonthYear(mTime);

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
                ActivityBargainReport.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (job) {
                            case 1:
                                SalesReportAdapterObject = new BargainReportAdapter(mContext, R.layout.bargain_primary_report_child, mOrderReportDetailsList);
                                mListViewList.setAdapter(SalesReportAdapterObject);
                                mListViewList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                                    @Override
                                    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                                        OrderReportDetails obj = SalesReportAdapterObject.getItem(position);
                                        mCustomerName = obj.getcustomerName();
                                        currentSelectedCustomerCode = obj.getcustomerCode();
                                        Constants.customerCode = currentSelectedCustomerCode;
                                        FetchSaudaTransactionLogData(2);

                                    }
                                });
                                break;
                            case 2:
                                if (listDataHeader.size() > 0) {
                                    ShowProductDetailsDialog(mCustomerName);
                                } else {
                                    Utils.showToast(mContext, "No record found");
                                }
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

        FetchSaudaTransactionLogData(1);
    }

    public void ShowProductDetailsDialog(final String titlename) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.AppBaseTheme);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_bargain_customer_wise_report);
        mDetailsDialog.setCancelable(true);

        TextView textViewQty = (TextView) mDetailsDialog.findViewById(R.id.textViewQtyTotal);
        Button back = (Button) mDetailsDialog.findViewById(R.id.back);

        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
            }
        });
        TotalCalculation2();
        textViewQty.setText(mTotalAmount);
        TextView textViewTitleName = (TextView) mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(titlename);

        ExpandableListView dialogList =  mDetailsDialog.findViewById(R.id.listdata);
//        final BargainReportAdapterCustomerWise orderreportAdapter = new BargainReportAdapterCustomerWise(mContext, R.layout.bargain_report_list_item, BargainReportDetailsList);
        final ExpandableListAdapterBargainReport orderreportAdapter = new ExpandableListAdapterBargainReport(mContext, listDataHeader, listChildData);
        dialogList.setAdapter(orderreportAdapter);

        dialogList.setOnGroupExpandListener(new ExpandableListView.OnGroupExpandListener() {
            int previousGroup = -1;

            @Override
            public void onGroupExpand(int groupPosition) {
                if(groupPosition != previousGroup)
                    dialogList.collapseGroup(previousGroup);
                previousGroup = groupPosition;
            }
        });
        mDetailsDialog.show();
    }
    private void TotalCalculation2()
    {
        for (Map.Entry mapElement : listChildData.entrySet())
        {
//            String key = (String)mapElement.getKey();
            ArrayList<OrderReportDetails> orderReportDetailsList= (ArrayList<OrderReportDetails>) mapElement.getValue();
            if (orderReportDetailsList != null && orderReportDetailsList.size() > 0)
            {
                BigDecimal qty = BigDecimal.valueOf(0);
                for (int count = 0; count < orderReportDetailsList.size(); count++) {
                    if (orderReportDetailsList.get(count).getAmount().trim().length() > 0)
                    {

                        BigDecimal bigD=new BigDecimal(orderReportDetailsList.get(count).getAmount());
                        qty = qty.add(bigD) ;
                    }
                }

                mTotalAmount = String.valueOf(qty);
                mTotalAmount=defaultFormatWithComma.format(Double.parseDouble(mTotalAmount));
            }
            else
            {
                mTotalAmount = "0";
            }
        }

    }
    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
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
                    } else//sb st
                    {
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
                    } else//sb st
                    {
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
        } catch (Exception e) {

        }

    }

    public void selectProd(View v) {
        productSelectionProcess();
    }


    public void InitializeView() {
        //mImageViewSaudaBooked=(ImageView) findViewById(R.id.imgageViewSaudaDetails);

        mImageViewShowDuration = (ImageView) findViewById(R.id.image_clk);
        mImageViewHideDuration = (ImageView) findViewById(R.id.image_go);

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);

        mRelativeLayoutDuration = (RelativeLayout) findViewById(R.id.duration_layout);
        mCustomDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);

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
        prodTV = (TextView) findViewById(R.id.prodTV);
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
                switch (whattodo) {
                    case 1:
                        if(HTTPUtils.isConnectionPossible(mContext)){
                            mOrderReportDetailsListGlobal=new ArrayList<>();
                            new commonAsyncTaskMaster(mContext, "bargainReportDownload");
                            mOrderReportDetailsList=new ArrayList<>(mOrderReportDetailsListGlobal);
                        }
                        else{
                            mOrderReportDetailsList = mAceDnsDatabaseHelper.GetbargainReportData(mQuery);
                        }


                        break;
                    case 2:
                        if(HTTPUtils.isConnectionPossible(mContext)){
                            mOrderReportDetailsListGlobal=new ArrayList<>();
                            listDataHeaderGlobal=new ArrayList<>();
                            listChildDataGlobal=new HashMap<>();
                            new commonAsyncTaskMaster(mContext, "bargainReportDownloadLevel2");
                            listDataHeader=new ArrayList<>(listDataHeaderGlobal);
                            listChildData=new HashMap<>(listChildDataGlobal);
                        }
                        else{
                            ArrayList<String> saudaTransList= mAceDnsDatabaseHelper.GetbargainReportDataByCustomer(mQuery,currentSelectedCustomerCode);

                            listDataHeader=new ArrayList<>();
                            listChildData=new HashMap<>();
                            for(int i=0;i<saudaTransList.size();i++)
                            {
                                int currentPost=i+1;
                                String saudaNo = saudaTransList.get(i);
                                String substringTrsnId = saudaNo.substring(7);
                                String dateInString= Utils.changeDateFormat("yyyyMMddhhmmss","dd/MM/yyyy hh:mm:ss", substringTrsnId);
                                String ListItemHeader = "Bargain " + currentPost + "- " + dateInString;
                                listDataHeader.add(ListItemHeader);
                                mOrderReportDetailsListByCustomer=mAceDnsDatabaseHelper.GetbargainReportDataBybargainId(saudaNo);
                                listChildData.put(ListItemHeader,mOrderReportDetailsListByCustomer);
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

    public void ShowSaleTypeSelectionDialog()//IN OUT BOTH
    {
        final Dialog payTypeDialog = new Dialog(mContext, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.sale_type_report_dialog);


        RadioGroup payTypeOption = (RadioGroup) payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
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
            }
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

    public String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                Constants.mode="customertoday";
                condition = "substr(SH.sauda_no,-14,8)='" + mToday + "'";
                dateRangeTV.setText("On " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mToday));
                conditionForOpeningStock = "substr(SH.sauda_no,-14,8) < '" + mToday + "'";
                break;
            case 2:
                Constants.mode="customermtd";
                condition = "substr(SH.sauda_no,-14,4)='" + mCurrentYear + "' AND substr(SH.sauda_no,-10,2)='" + mCurrentMonth + "'";
                dateRangeTV.setText("On " + mCurrentMonth + "/" + mCurrentYear);
                conditionForOpeningStock = "substr(SH.sauda_no,-14,8) < '" + mCurrentYear + mCurrentMonth + "01'";

                break;
            case 3:
                Constants.mode="customercustom";
                Constants.from_date =mStartDate;
                Constants.to_date=mEndDate;
                condition = "substr(SH.sauda_no,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'";
                dateRangeTV.setText("Between " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mStartDate) + " & " + Utils.changeDateFormat("yyyyMMdd", "dd/MM/yyyy", mEndDate));
                conditionForOpeningStock = "substr(SH.sauda_no,-14,8) < '" + mStartDate + "'";

                break;
        }
        return condition;
    }

    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
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

            prodQtyRateListView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {

                    ProductMasterDetails currentProductMasterObj = tempProductList.get(arg2);
                    selectedSku = currentProductMasterObj.getProdCode();
                    prodTV.setText(currentProductMasterObj.getDesc());
                    ShowSaleTypeSelectionDialog();
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
            Utils.showToast(mContext,"No product found! Please contact admin!");
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
