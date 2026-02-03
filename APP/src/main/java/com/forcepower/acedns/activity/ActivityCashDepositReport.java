package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
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

public class ActivityCashDepositReport extends FragmentActivity implements OnClickListener {

    public static Button mButtonBack = null;
    public static Button mButtonStartDate = null;
    public static Button mButtonEndDate = null;
    public static Button mButtonSubmitDate = null;

    public static ImageView mImageViewHeaderLogo = null;
    public static ImageView mImageViewShowDuration = null;
    public static ImageView mImageViewHideDuration = null;


    public static TextView mTextViewStartDate = null;
    public static TextView mTextViewEndDate = null;
    public static TextView mTextViewQty = null;

    public static ListView mListViewList = null;


    public static FrameLayout mFrameLayoutToday = null;
    public static FrameLayout mFrameLayoutMTD = null;
    public static FrameLayout mFrameLayoutCustom = null;

    public static RelativeLayout mRelativeLayoutDuration = null;
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
    private String mTime = "";
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mToday = "";
    private String mQuery = "";
    private String mTotalQty = "";
    private ArrayList<OrderReportDetails> mOrderReportDetailsList;
    private ArrayList<YellowCard> mYellowCardCustomwerWiseDetailsList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_cash_deposit_report);
        mContext = ActivityCashDepositReport.this;
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

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        InitializeView();

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
                mCustomerName = obj.getName();
                currentSelectedCustomerCode = obj.getCode();
                FetchSaudaTransactionLogData(2);

            }
        });


        mReportHandler = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivityCashDepositReport.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (job) {
                            case 1:
                                orderReportAdapter = new OrderReportAdapter(ActivityCashDepositReport.this, R.layout.sku_child, mOrderReportDetailsList, true);
                                mListViewList.setAdapter(orderReportAdapter);
                                TotalCalculation(mOrderReportDetailsList);
                                mTextViewQty.setText(mTotalQty);

                                break;
                            case 2:
                                if (mYellowCardCustomwerWiseDetailsList.size() > 0) {
                                    ShowProductDetailsDialog(mCustomerName, mYellowCardCustomwerWiseDetailsList);
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
        mQuery = BuildQuery(SELECTION);
        FetchSaudaTransactionLogData(1);
    }

    public String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                condition = "substr(YCD.yellow_card_no,-14,8)='" + mToday + "' ";
                break;
            case 2:
                condition = "substr(YCD.yellow_card_no,-14,4)='" + mCurrentYear + "' AND substr(YCD.yellow_card_no,-10,2)='" + mCurrentMonth + "' ";

                break;
            case 3:
                condition = "substr(YCD.yellow_card_no,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";

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

    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
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

        mTextViewQty = (TextView) findViewById(R.id.textViewQtyTotal);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");


        mButtonStartDate = (Button) findViewById(R.id.btn_start_date);
        mButtonEndDate = (Button) findViewById(R.id.btn_end_date);
        mButtonSubmitDate = (Button) findViewById(R.id.btn_date_done);

        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);

        mListViewList = (ListView) findViewById(R.id.listView);

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
        final Dialog mDetailsDialog = new Dialog(ActivityCashDepositReport.this, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_yellow_card_customer_wise_report);
        mDetailsDialog.setCancelable(true);

        TextView textViewQty = (TextView) mDetailsDialog.findViewById(R.id.textViewQtyTotal);
        Button back = (Button) mDetailsDialog.findViewById(R.id.back);

        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
            }
        });
        TotalCalculation2(yellowReportDetailsList);
        textViewQty.setText(mTotalQty);
        TextView textViewTitleName = (TextView) mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(titlename);

        ListView dialogList = (ListView) mDetailsDialog.findViewById(R.id.listdata);
        final YellowCardReportAdapter orderreportAdapter = new YellowCardReportAdapter(ActivityCashDepositReport.this, R.layout.yellow_card_report_list_item, yellowReportDetailsList);
        dialogList.setAdapter(orderreportAdapter);

        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int position, long arg3) {
                mDetailsDialog.cancel();
            }
        });
        mDetailsDialog.show();
    }

    private void TotalCalculation(ArrayList<OrderReportDetails> orderReportDetailsList) {
        if (orderReportDetailsList != null && orderReportDetailsList.size() > 0) {
            double qty = 0;
            for (int count = 0; count < orderReportDetailsList.size(); count++) {
                if (orderReportDetailsList.get(count).getQuantity().trim().length() > 0) {
                    qty += Double.parseDouble(orderReportDetailsList.get(count).getQuantity().trim());
                }
            }
            mTotalQty = String.valueOf(qty);
        } else {
            mTotalQty = "0";

        }
    }

    private void TotalCalculation2(ArrayList<YellowCard> orderReportDetailsList) {
        if (orderReportDetailsList != null && orderReportDetailsList.size() > 0) {
            double qty = 0;
            for (int count = 0; count < orderReportDetailsList.size(); count++) {
                if (orderReportDetailsList.get(count).getqty().trim().length() > 0) {
                    qty += Double.parseDouble(orderReportDetailsList.get(count).getqty().trim());
                }
            }
            mTotalQty = String.valueOf(qty);
        } else {
            mTotalQty = "0";
        }
    }
}
