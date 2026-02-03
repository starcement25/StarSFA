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
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.OrderReportAdapter;
import com.forcepower.acedns.adapter.YellowCardReportAdapter;
import com.forcepower.acedns.bean.OrderReportDetails;
import com.forcepower.acedns.bean.YellowCard;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class ActivityYellowCardReport extends FragmentActivity implements OnClickListener {
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
    public static TextView mTextViewQty = null;
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
    OrderReportAdapter orderReportAdapter;
    private int SELECTION = 0;
    private boolean isStartDate = false;
    private String mCustomerName = "";
    private String currentSelectedCustomerCode = "";
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mToday = "";
    private String mQuery = "";
    private String mTotalQty = "";
    private ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private ArrayList<YellowCard> mYellowCardCustomwerWiseDetailsList;

    @SuppressLint({"HandlerLeak", "SetTextI18n", "SimpleDateFormat"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_yellow_card_report);
        mContext = ActivityYellowCardReport.this;
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

        mListViewList.setOnItemClickListener((parent, view, position, id) -> {
            OrderReportDetails obj = orderReportAdapter.getItem(position);
            assert obj != null;
            mCustomerName = obj.getName();
            currentSelectedCustomerCode = obj.getCode();
            FetchSaudaTransactionLogData(2);
        });

        mReportHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivityYellowCardReport.this.runOnUiThread(() -> {
                    switch (job) {
                        case 1:
                            orderReportAdapter = new OrderReportAdapter(ActivityYellowCardReport.this, R.layout.sku_child, mOrderReportDetailsList, true);
                            mListViewList.setAdapter(orderReportAdapter);
                            TotalCalculation(mOrderReportDetailsList);
                            mTextViewQty.setText(mTotalQty);
                            break;
                        case 2:
                            if (!mYellowCardCustomwerWiseDetailsList.isEmpty()) {
                                ShowProductDetailsDialog(mCustomerName, mYellowCardCustomwerWiseDetailsList);
                            } else {
                                Utils.showToast(mContext, "No record found");
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
        FetchSaudaTransactionLogData(1);
    }

    public String BuildQuery(int select) {
        return switch (select) {
            case 1 -> "substr(YCD.yellow_card_no,-14,8)='" + mToday + "' ";
            case 2 ->
                    "substr(YCD.yellow_card_no,-14,4)='" + mCurrentYear + "' AND substr(YCD.yellow_card_no,-10,2)='" + mCurrentMonth + "' ";
            case 3 ->
                    "substr(YCD.yellow_card_no,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
            default -> "";
        };
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

    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
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
        mTextViewQty = findViewById(R.id.textViewQtyTotal);
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

        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
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
                        mOrderReportDetailsList = mAceDnsDatabaseHelper.GetYellowCardCustomerData(mQuery);
                        break;
                    case 2:
                        mYellowCardCustomwerWiseDetailsList = mAceDnsDatabaseHelper.GetYellowCardProductData(mQuery, currentSelectedCustomerCode);
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

    public void ChooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        Bundle bundle = new Bundle();
        bundle.putString(CaldroidFragment.DIALOG_TITLE, "Select Date");
        dialogCaldroidFragment.setArguments(bundle);
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
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

    public void ShowProductDetailsDialog(final String titlename, ArrayList<YellowCard> yellowReportDetailsList) {
        final Dialog mDetailsDialog = new Dialog(ActivityYellowCardReport.this, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_yellow_card_customer_wise_report);
        mDetailsDialog.setCancelable(true);

        TextView textViewQty = mDetailsDialog.findViewById(R.id.textViewQtyTotal);
        Button back = mDetailsDialog.findViewById(R.id.back);

        back.setOnClickListener(v -> mDetailsDialog.cancel());
        TotalCalculation2(yellowReportDetailsList);
        textViewQty.setText(mTotalQty);
        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(titlename);

        ListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        final YellowCardReportAdapter orderreportAdapter = new YellowCardReportAdapter(ActivityYellowCardReport.this, R.layout.yellow_card_report_list_item, yellowReportDetailsList);
        dialogList.setAdapter(orderreportAdapter);

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> mDetailsDialog.cancel());
        mDetailsDialog.show();
    }

    private void TotalCalculation(ArrayList<OrderReportDetails> orderReportDetailsList) {
        if (orderReportDetailsList != null && !orderReportDetailsList.isEmpty()) {
            double qty = 0;
            for (int count = 0; count < orderReportDetailsList.size(); count++) {
                if (!orderReportDetailsList.get(count).getQuantity().trim().isEmpty()) {
                    qty += Double.parseDouble(orderReportDetailsList.get(count).getQuantity().trim());
                }
            }
            mTotalQty = String.valueOf(qty);
        } else {
            mTotalQty = "0";

        }
    }

    private void TotalCalculation2(ArrayList<YellowCard> orderReportDetailsList) {
        if (orderReportDetailsList != null && !orderReportDetailsList.isEmpty()) {
            double qty = 0;
            for (int count = 0; count < orderReportDetailsList.size(); count++) {
                if (!orderReportDetailsList.get(count).getqty().trim().isEmpty()) {
                    qty += Double.parseDouble(orderReportDetailsList.get(count).getqty().trim());
                }
            }
            mTotalQty = String.valueOf(qty);
        } else {
            mTotalQty = "0";
        }
    }
}
