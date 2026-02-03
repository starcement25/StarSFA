package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
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
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.KeyValueAdapter;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class ActivityNoActivityReport extends FragmentActivity implements OnClickListener {
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
    public static FrameLayout activity_date_layout = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView activity_date_tv = null;
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
    @SuppressLint("StaticFieldLeak")
    public static RadioGroup mRadioGroupType = null;

    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;

    Date startDate, endDate;
    Date currentDate = new Date();

    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;

    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ArrayList<KeyValue> mKeyValueList;
    KeyValueAdapter mKeyValueAdapter;
    RadioButton radio1, radio2, radio3, radio4;
    private int SELECTION = 1;
    private boolean isStartDate = false;
    private String mStartDate = "";
    private String mEndDate = "";
    private String mFStartDate = "";
    private String mFEndDate = "";
    private String mType = "";
    private String mTimestamp = "";

    @SuppressLint({"SimpleDateFormat", "SetTextI18n", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_noactivity_report);
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
        mTimestamp = Constants.dateString;
        mContext = ActivityNoActivityReport.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);


        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        InitializeView();

        mRadioGroupType.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection = findViewById(checkedId);
            if (radioSelection.getText().equals("Order")) {
                mType = "order";
                ChangeBackground(SELECTION);
                activity_date_layout.setVisibility(View.VISIBLE);
                FetchSaudaTransactionLogData(SELECTION);
            }
            if (radioSelection.getText().equals("Collection")) {
                mType = "Collection";
                ChangeBackground(SELECTION);
                activity_date_layout.setVisibility(View.GONE);
                FetchSaudaTransactionLogData(SELECTION);
            }
            if (radioSelection.getText().equals("Sauda")) {
                mType = "sauda";
                ChangeBackground(SELECTION);
                activity_date_layout.setVisibility(View.GONE);
                FetchSaudaTransactionLogData(SELECTION);
            }
            if (radioSelection.getText().equals("Stock")) {
                mType = "stock";
                ChangeBackground(SELECTION);
                activity_date_layout.setVisibility(View.GONE);
                FetchSaudaTransactionLogData(SELECTION);
            }
        });

        mButtonBack.setOnClickListener(v -> finish());


        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate = date;
                    mTextViewStartDate.setText(mDFormatFrontEnd.format(date));
                    mFStartDate = mDFormatFrontEnd.format(date);
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    mFEndDate = mDFormatFrontEnd.format(date);
                    mEndDate = mDFormatBackEnd.format(date);
                }
                dialogCaldroidFragment.dismiss();
            }
        };

        mListViewList.setOnItemClickListener((parent, view, position, id) -> {

        });


        mReportHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivityNoActivityReport.this.runOnUiThread(() -> {
                    switch (job) {
                        case 1:
                            if (mKeyValueList.size() <= 0) {
                                if (mType.equalsIgnoreCase("order")) {
                                    Utils.showToast(mContext, "Number of  no order for today is 0");
                                }
                                if (mType.equalsIgnoreCase("Collection")) {
                                    Utils.showToast(mContext, "Number of  no collection for today is 0");
                                }
                                if (mType.equalsIgnoreCase("sauda")) {
                                    Utils.showToast(mContext, "Number of  no sauda for today is 0");
                                }
                                if (mType.equalsIgnoreCase("stock")) {
                                    Utils.showToast(mContext, "Number of  no stock for today is 0");
                                }
                            }
                            break;
                        case 2:
                            if (mKeyValueList.size() <= 0) {
                                if (mType.equalsIgnoreCase("order")) {
                                    Utils.showToast(mContext, "Number of  no order for this month is 0");
                                }
                                if (mType.equalsIgnoreCase("Collection")) {
                                    Utils.showToast(mContext, "Number of  no collection for this month is 0");
                                }
                                if (mType.equalsIgnoreCase("sauda")) {
                                    Utils.showToast(mContext, "Number of  no sauda for this month is 0");
                                }
                                if (mType.equalsIgnoreCase("stock")) {
                                    Utils.showToast(mContext, "Number of  no stock for this month is 0");
                                }
                            }
                            break;
                        case 3:
                            if (mKeyValueList.size() <= 0) {
                                if (mType.equalsIgnoreCase("order")) {
                                    Utils.showToast(mContext, "Number of  no order from " + mFStartDate + " to " + mFEndDate + " is 0");
                                }
                                if (mType.equalsIgnoreCase("Collection")) {
                                    Utils.showToast(mContext, "Number of  no collection from " + mFStartDate + " to " + mFEndDate + " is 0");
                                }
                                if (mType.equalsIgnoreCase("sauda")) {
                                    Utils.showToast(mContext, "Number of  no sauda from " + mFStartDate + " to " + mFEndDate + " is 0");
                                }
                                if (mType.equalsIgnoreCase("stock")) {
                                    Utils.showToast(mContext, "Number of  no stock from " + mFStartDate + " to " + mFEndDate + " is 0");
                                }
                            }
                            break;
                    }
                    if (mKeyValueList != null) {
                        if (mType.equalsIgnoreCase("order")) {
                            mKeyValueAdapter = new KeyValueAdapter(mContext, R.layout.keyvalue_child, mKeyValueList, true);
                        } else {
                            mKeyValueAdapter = new KeyValueAdapter(mContext, R.layout.keyvalue_child, mKeyValueList);
                        }

                        mListViewList.setAdapter(mKeyValueAdapter);
                        mKeyValueAdapter.notifyDataSetChanged();
                    }
                });
            }
        };

        mType = "order";
        if (SELECTION == 0) {
            SELECTION = 1;
        }
        ChangeBackground(SELECTION);
        FetchSaudaTransactionLogData(SELECTION);
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

        activity_date_layout = findViewById(R.id.activity_date_layout);
        activity_date_tv = findViewById(R.id.activity_date_tv);
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

        mRadioGroupType = findViewById(R.id.radioGroupType);
        AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(mContext);
        boolean order = mAceDnsDatabase.MenuAccess("order");
        if (Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes") && order) {
            radio1 = findViewById(R.id.radio1);
            radio1.setVisibility(View.VISIBLE);
        }

        boolean collection = mAceDnsDatabase.MenuAccess("collection");
        if (Constants.menuDetailsObj.getCollection().equalsIgnoreCase("yes") && collection) {
            radio2 = findViewById(R.id.radio2);
            radio2.setVisibility(View.VISIBLE);
        }

        boolean saudaaccess = mAceDnsDatabase.MenuAccess("sauda");
        if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") && saudaaccess) {
            radio3 = findViewById(R.id.radio3);
            radio3.setVisibility(View.VISIBLE);
        }

        boolean stock_audit = mAceDnsDatabase.MenuAccess("stk_audit");
        if (Constants.menuDetailsObj.getStkAudit().equalsIgnoreCase("yes") && stock_audit) {
            radio4 = findViewById(R.id.radio4);
            radio4.setVisibility(View.VISIBLE);
        }

        mListViewList = findViewById(R.id.listView);

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
            FetchSaudaTransactionLogData(SELECTION);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            FetchSaudaTransactionLogData(SELECTION);
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
                    FetchSaudaTransactionLogData(SELECTION);
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


    public void FetchSaudaTransactionLogData(final int whattodo) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        mTimestamp = Constants.dateString;
                        mKeyValueList = mAceDnsTransactionDatabase.GetNoActivityMtdandToday(mType, mTimestamp);
                        break;
                    case 2:
                        mTimestamp = Constants.dateString.substring(0, 6);
                        mKeyValueList = mAceDnsTransactionDatabase.GetNoActivityMtdandToday(mType, mTimestamp);
                        break;
                    case 3:
                        mKeyValueList = mAceDnsTransactionDatabase.GetNoActivityMtdandToday(mType, mStartDate, mEndDate);
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
}
