package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.fragment.app.FragmentActivity;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.OrderReportAdapter;
import com.forcepower.acedns.adapter.VanSalesReportAdapter;
import com.forcepower.acedns.bean.OrderReportDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;


public class ActivityCallCentreReport extends FragmentActivity implements OnClickListener {


    public static Button mButtonBack = null;
    public static Button mButtonStartDate = null;
    public static Button mButtonEndDate = null;
    public static Button mButtonSubmitDate = null;

    public static ImageView mImageViewHeaderLogo = null;
    public static ImageView mImageViewShowDuration = null;
    public static ImageView mImageViewHideDuration = null;


    public static TextView mTextViewStartDate = null;
    public static TextView mTextViewEndDate = null;
    public static TextView mTextViewHeaderCol = null;
    public static TextView mTextViewQty = null;
    public static TextView mTextViewAmount = null;

    public static ListView mListViewList = null;

    public static FrameLayout mFrameLayoutToday = null;
    public static FrameLayout mFrameLayoutMTD = null;
    public static FrameLayout mFrameLayoutCustom = null;

    public static RelativeLayout mRelativeLayoutDuration = null;
    public static LinearLayout mCustomDateLayout = null;

    public static RadioGroup mRadioGroupType = null;
    public AceDnsDatabase mAceDnsDatabaseHelper;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    Date startDate, endDate;
    Date currentDate = new Date();

    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;

    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    VanSalesReportAdapter orderReportAdapter;
    private int SELECTION = 0;
    private boolean isStartDate = false;
    private String mCustomerCode = "";
    private String mCustomerName = "";
    private String mRouteCode = "";
    private String mRouteName = "";
    private String mTime = "";
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mToday = "";
    private String mQuery = "";
    private String mQuery1 = "";
    private String mType = "";
    private String mSubType = "";
    private String mTotalQty = "";
    private double mTotalAmount = 0;
    private int mFilterNo = 0;
    private int mStep = 0;
    private String mProductGroupName = "";
    private String mProductGroupCode = "";
    private String mProductSubGroupName = "";
    private String mProductSubGroupCode = "";
    private String mProductBrandName = "";
    private String mProductBrandCode = "";
    private ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private ArrayList<OrderReportDetails> mOrderReportSubDetailsList;
    private ArrayList<OrderReportDetails> mOrderReportProductSubGroupDetailsList;
    private ArrayList<OrderReportDetails> mOrderReportProductBrandDetailsList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_van_sales_report);
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

        mToday = mDFormatBackEnd.format(currentDate);

        mTime = Constants.dateString;
        GETCurrentMonthYear(mTime);

        mContext = ActivityCallCentreReport.this;
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        InitializeView();
        mFilterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());


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
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
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

        mListViewList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                OrderReportDetails obj = orderReportAdapter.getItem(position);

                if (mType.equalsIgnoreCase("customer")) {
                    mCustomerCode = obj.getCode();
                    mCustomerName = obj.getName();
                    FetchSaudaTransactionLogData(2);
                }
            }
        });


        mReportHandler = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivityCallCentreReport.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (job) {
                            case 1:

                                    orderReportAdapter = new VanSalesReportAdapter(ActivityCallCentreReport.this, R.layout.order_report_list_item, mOrderReportDetailsList, true);
                                    mListViewList.setAdapter(orderReportAdapter);
                                    TotalCalculation(mOrderReportDetailsList);
                                    mTextViewQty.setText(mTotalQty);

                                break;
                            case 2:
                                    ShowDetailsDialog(mCustomerName, mType, "", mOrderReportSubDetailsList);

                                break;
                            case 3:
                                ShowDetailsDialog(mRouteName, mType, "", mOrderReportSubDetailsList);
                                break;
                            case 4:
                                if (mType.equalsIgnoreCase("product")) {
                                    if (mOrderReportProductSubGroupDetailsList.size() > 0) {
                                        if (mFilterNo == 4) {
                                            mStep = 1;
                                        }
                                        if (mFilterNo == 3) {
                                            mStep = 0;
                                        }
                                        ShowProductDetailsDialog(mProductGroupName, mStep, mOrderReportProductSubGroupDetailsList);
                                    } else {
                                        Utils.showToast(mContext, "No record found");
                                    }
                                }
                                break;
                            case 5:

                                if (mType.equalsIgnoreCase("product")) {
                                    if (mOrderReportProductBrandDetailsList.size() > 0) {
                                        mStep = 2;
                                        ShowProductDetailsDialog(mProductSubGroupName, mStep, mOrderReportProductBrandDetailsList);
                                    } else {
                                        Utils.showToast(mContext, "No record found");
                                    }
                                }
                                break;
                            case 6:

                                break;
                            case 7:

                                break;
                            case 8:

                                break;
                        }
                    }
                });
            }
        };

        mType = "customer";
        if (SELECTION == 0) {
            SELECTION = 1;
        }
        ChangeBackground(SELECTION);
        mQuery = BuildQuery(SELECTION);
        FetchSaudaTransactionLogData(1);
    }


    public void InitializeView() {
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
        mTextViewHeaderCol = (TextView) findViewById(R.id.textViewHeader);

        mTextViewQty = (TextView) findViewById(R.id.textViewQtyTotal);
        mTextViewAmount = (TextView) findViewById(R.id.textViewAmountTotal);


        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");


        mButtonStartDate = (Button) findViewById(R.id.btn_start_date);
        mButtonEndDate = (Button) findViewById(R.id.btn_end_date);
        mButtonSubmitDate = (Button) findViewById(R.id.btn_date_done);

        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);
        mRadioGroupType = (RadioGroup) findViewById(R.id.radioGroupType);
        mRadioGroupType.setVisibility(View.GONE);
        mListViewList = (ListView) findViewById(R.id.listView);

        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }

    }

    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
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
            mQuery = BuildQuery(SELECTION);
            FetchSaudaTransactionLogData(1);

        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            mQuery = BuildQuery(SELECTION);
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

    public String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                condition = "substr(cpi.order_no,-14,8)='" + mToday + "' ";
                break;
            case 2:
                condition = "substr(cpi.order_no,-14,4)='" + mCurrentYear + "' AND substr(cpi.order_no,-10,2)='" + mCurrentMonth + "' ";
                break;
            case 3:
                condition = "substr(cpi.order_no,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
                break;
        }
        return condition;
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


    public void FetchSaudaTransactionLogData(final int whattodo) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        mOrderReportDetailsList = mAceDnsDatabaseHelper.GetCallCentreCustomerData(mQuery);

                        break;
                    case 2:
                            mOrderReportSubDetailsList = mAceDnsDatabaseHelper.GetCallCentreProductData(mQuery,mCustomerCode);

                        break;
                    case 3:
                        if (mType.equalsIgnoreCase("route")) {
                            mOrderReportSubDetailsList = mAceDnsDatabaseHelper.GetOrderCustomerData(mRouteCode, mQuery);

                        }
                        break;
                    case 4:
                        if (mType.equalsIgnoreCase("product")) {
                            mOrderReportProductSubGroupDetailsList = mAceDnsDatabaseHelper.GetOrderProductSubGroupData(mQuery1, mProductGroupCode);
                        }
                        break;
                    case 5:
                        if (mType.equalsIgnoreCase("product")) {
                            mOrderReportProductBrandDetailsList = mAceDnsDatabaseHelper.GetOrderProductBrandData(mQuery1, mProductGroupCode, mProductSubGroupCode);
                        }

                        break;

                    case 6:

                        break;

                    case 7:

                        break;

                    case 8:


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

    public void ShowProductDetailsDialog(final String titlename, final int step, ArrayList<OrderReportDetails> orderReportDetailsList) {
        final Dialog mDetailsDialog = new Dialog(ActivityCallCentreReport.this, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_order_chart);
        mDetailsDialog.setCancelable(true);

        TextView textViewQty = (TextView) mDetailsDialog.findViewById(R.id.textViewQtyTotal);
        TextView textViewAmount = (TextView) mDetailsDialog.findViewById(R.id.textViewAmountTotal);
        Button back = (Button) mDetailsDialog.findViewById(R.id.back);

        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
            }
        });

        TotalCalculation(orderReportDetailsList);
        textViewQty.setText(mTotalQty);
        textViewAmount.setText(defaultFormat.format(mTotalAmount));

        TextView textViewTitleName = (TextView) mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(titlename);

        TextView textViewHeaderName = (TextView) mDetailsDialog.findViewById(R.id.textViewHeader);
        switch (mFilterNo) {
            case 4:
                if (step == 1) {
                    textViewHeaderName.setText("Product Sub Group");
                }
                if (step == 2) {
                    textViewHeaderName.setText("Product Brand");
                }
                break;
            case 3:
                textViewHeaderName.setText("Product Sub Group");
                break;
        }

        ListView dialogList = (ListView) mDetailsDialog.findViewById(R.id.listdata);
        final OrderReportAdapter orderreportAdapter = new OrderReportAdapter(ActivityCallCentreReport.this, R.layout.order_report_list_item, orderReportDetailsList, false);
        dialogList.setAdapter(orderreportAdapter);

        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int position, long arg3) {
                mDetailsDialog.cancel();
                switch (mFilterNo) {
                    case 4:
                        if (step == 1) {
                            OrderReportDetails obj = orderreportAdapter.getItem(position);
                            mProductSubGroupCode = obj.getCode();
                            mProductSubGroupName = obj.getName();
                            obj = null;
                            FetchSaudaTransactionLogData(5);
                        }
                        if (step == 2) {
                            OrderReportDetails obj = orderreportAdapter.getItem(position);
                            mProductBrandCode = obj.getCode();
                            mProductBrandName = obj.getName();
                            obj = null;
                            FetchSaudaTransactionLogData(2);
                        }
                        break;
                    case 3:
                        OrderReportDetails obj = orderreportAdapter.getItem(position);
                        mProductSubGroupCode = obj.getCode();
                        mProductSubGroupName = obj.getName();
                        obj = null;
                        FetchSaudaTransactionLogData(2);
                        break;
                    case 2:
                        break;
                    case 1:
                        break;
                }
            }
        });
        mDetailsDialog.show();
    }


    public void ShowDetailsDialog(final String titlename, final String type, final String datewiserecord, ArrayList<OrderReportDetails> orderReportDetailsList) {
        final Dialog mDetailsDialog = new Dialog(mContext, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_order_chart);
        mDetailsDialog.setCancelable(true);

        TextView textViewQty = (TextView) mDetailsDialog.findViewById(R.id.textViewQtyTotal);
        FrameLayout col3 = (FrameLayout) mDetailsDialog.findViewById(R.id.col3);
        FrameLayout col3Footer = (FrameLayout) mDetailsDialog.findViewById(R.id.col3Footer);
        col3.setVisibility(View.GONE);
        col3Footer.setVisibility(View.GONE);
        TextView textViewAmount = (TextView) mDetailsDialog.findViewById(R.id.textViewAmountTotal);
        Button back = (Button) mDetailsDialog.findViewById(R.id.back);

        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
            }
        });

        TotalCalculation(orderReportDetailsList);
        textViewQty.setText(mTotalQty);
        textViewAmount.setText(defaultFormat.format(mTotalAmount));

        TextView textViewTitleName = (TextView) mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(titlename);

        TextView textViewHeaderName = (TextView) mDetailsDialog.findViewById(R.id.textViewHeader);
        if (type.equalsIgnoreCase("customer")) {
            textViewHeaderName.setText("SKU");
        }
        if (type.equalsIgnoreCase("product")) {
            textViewHeaderName.setText("SKU");
        }
        if (type.equalsIgnoreCase("route")) {
            if (mSubType.equalsIgnoreCase("customer")) {
                textViewHeaderName.setText("SKU");
                mSubType = "sku";
            } else {
                textViewHeaderName.setText("Customer");
                mSubType = "customer";
            }
        }
		
		/*TextView textViewMessage = (TextView) mDetailsDialog.findViewById(R.id.textViewMessage);
		textViewMessage.setText(datewiserecord);*/

        ListView dialogList = (ListView) mDetailsDialog.findViewById(R.id.listdata);

        final VanSalesReportAdapter orderreportAdapter = new VanSalesReportAdapter(mContext, R.layout.order_report_list_item, orderReportDetailsList, false);
        dialogList.setAdapter(orderreportAdapter);


        mDetailsDialog.show();

    }

    private void TotalCalculation(ArrayList<OrderReportDetails> orderReportDetailsList) {
        try {
            if (orderReportDetailsList != null && orderReportDetailsList.size() > 0) {
                double qty = 0;
                double amount = 0;
                for (int count = 0; count < orderReportDetailsList.size(); count++) {
                    if (orderReportDetailsList.get(count).getQuantity().trim().length() > 0) {
                        qty += Double.parseDouble(orderReportDetailsList.get(count).getQuantity().trim());
                    }
                    if (orderReportDetailsList.get(count).getAmount().trim().length() > 0) {
                        amount += Double.parseDouble(orderReportDetailsList.get(count).getAmount().trim());
                    }
                }
                int qtyInt = (int) qty;
                mTotalQty = String.valueOf(qtyInt);
                mTotalAmount = amount;
            } else {
                mTotalQty = "0";
                mTotalAmount = 0;
            }
        } catch (Exception e) {
            mTotalQty = "0";
        }

    }
}
