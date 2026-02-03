package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
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
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BeatWiseTADATotalReportAdapter;
import com.forcepower.acedns.adapter.OrderReportAdapter;
import com.forcepower.acedns.bean.OrderReportDetails;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.dateString;

public class BeatWiseTADAReport extends FragmentActivity implements OnClickListener {
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
    public static TextView mTextViewHeaderCol = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewQty = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView totalTaTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView totalDaTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView totalTV = null;
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
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    Date startDate, endDate;
    Date currentDate = new Date();

    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;

    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    BeatWiseTADATotalReportAdapter BeatWiseTADATotalReportAdapterObj;
    private int SELECTION = 0;
    private boolean isStartDate = false;
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mQuery = "";
    private String mTotalQty = "";
    private double mTotalAmount = 0;
    private int mFilterNo = 0;
    private ArrayList<commonDatabaseHelper> VisitDateWiseBeatListTADA;

    @SuppressLint({"HandlerLeak", "SetTextI18n", "SimpleDateFormat"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_beat_wise_ta_da);
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

        String mTime = Constants.dateString;
        GETCurrentMonthYear(mTime);

        mContext = BeatWiseTADAReport.this;
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        InitializeView();
        mFilterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());

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
                BeatWiseTADAReport.this.runOnUiThread(() -> {
                    if (job == 1) {
                        BeatWiseTADATotalReportAdapterObj = new BeatWiseTADATotalReportAdapter(BeatWiseTADAReport.this, R.layout.ta_da_report_list_item, VisitDateWiseBeatListTADA);
                        mListViewList.setAdapter(BeatWiseTADATotalReportAdapterObj);
                        totalTaTV.setText("TA-" + Constants.totalTa);
                        totalDaTV.setText("DA-" + Constants.totalDa);
                        totalTV.setText(String.valueOf(Constants.totalTaDa));
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

        mTextViewStartDate = findViewById(R.id.txt_start_date);
        mTextViewEndDate = findViewById(R.id.txt_end_date);
        mTextViewHeaderCol = findViewById(R.id.textViewHeader);

        mTextViewQty = findViewById(R.id.textViewQtyTotal);
        totalTaTV = findViewById(R.id.totalTaTV);
        totalDaTV = findViewById(R.id.totalDaTV);
        totalTV = findViewById(R.id.totalTV);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");

        mButtonStartDate = findViewById(R.id.btn_start_date);
        mButtonEndDate = findViewById(R.id.btn_end_date);
        mButtonSubmitDate = findViewById(R.id.btn_date_done);

        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);

        mListViewList = findViewById(R.id.listView);
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
        return switch (select) {
            case 1 -> {
                String todaysDate = Utils.changeDateFormat("yyyyMMdd", "dd-MM-yyyy", dateString);
                yield " visit_date='" + todaysDate + "' ";
            }
            case 2 ->
                    " substr(visit_date,-4,4)='" + mCurrentYear + "' AND substr(visit_date,-7,2)='" + mCurrentMonth + "' ";
            case 3 ->
                    " substr(visit_date,-4,4)||substr(visit_date,-7,2)||substr(visit_date,-10,2) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
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

    public void FetchSaudaTransactionLogData(final int whattodo) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                if (whattodo == 1) {
                    String finalQuery = "select distinct visit_date from route_plan_transaction where " + mQuery + " order by visit_date";
                    VisitDateWiseBeatListTADA = mAceDnsDatabaseHelper.GetvisitDateWIseBeatTADA(finalQuery);
                }
                Message msg = mReportHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("JOBDONE", whattodo);
                msg.setData(b);
                mReportHandler.sendMessage(msg);
            }
        }.start();
    }

    @SuppressLint("SetTextI18n")
    public void ShowProductDetailsDialog(final String titlename, final int step, ArrayList<OrderReportDetails> orderReportDetailsList) {
        final Dialog mDetailsDialog = new Dialog(BeatWiseTADAReport.this, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_order_chart);
        mDetailsDialog.setCancelable(true);

        TextView textViewQty = mDetailsDialog.findViewById(R.id.textViewQtyTotal);
        TextView textViewAmount = mDetailsDialog.findViewById(R.id.textViewAmountTotal);
        Button back = mDetailsDialog.findViewById(R.id.back);

        back.setOnClickListener(v -> mDetailsDialog.cancel());

        TotalCalculation(orderReportDetailsList);
        textViewQty.setText(mTotalQty);
        textViewAmount.setText(defaultFormat.format(mTotalAmount));

        TextView textViewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(titlename);

        TextView textViewHeaderName = mDetailsDialog.findViewById(R.id.textViewHeader);
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

        ListView dialogList = mDetailsDialog.findViewById(R.id.listdata);
        final OrderReportAdapter orderreportAdapter = new OrderReportAdapter(BeatWiseTADAReport.this, R.layout.order_report_list_item, orderReportDetailsList, false);
        dialogList.setAdapter(orderreportAdapter);

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            mDetailsDialog.cancel();
            switch (mFilterNo) {
                case 4:
                    if (step == 1) {
                        OrderReportDetails obj = orderreportAdapter.getItem(position);
                        assert obj != null;
                        FetchSaudaTransactionLogData(5);
                    }
                    if (step == 2) {
                        OrderReportDetails obj = orderreportAdapter.getItem(position);
                        assert obj != null;
                        FetchSaudaTransactionLogData(2);
                    }
                    break;
                case 3:
                    OrderReportDetails obj = orderreportAdapter.getItem(position);
                    assert obj != null;
                    FetchSaudaTransactionLogData(2);
                    break;
            }
        });
        mDetailsDialog.show();
    }

    private void TotalCalculation(ArrayList<OrderReportDetails> orderReportDetailsList) {
        try {
            if (orderReportDetailsList != null && !orderReportDetailsList.isEmpty()) {
                double qty = 0;
                double amount = 0;
                for (int count = 0; count < orderReportDetailsList.size(); count++) {
                    if (!orderReportDetailsList.get(count).getQuantity().trim().isEmpty()) {
                        qty += Double.parseDouble(orderReportDetailsList.get(count).getQuantity().trim());
                    }
                    if (!orderReportDetailsList.get(count).getAmount().trim().isEmpty()) {
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
