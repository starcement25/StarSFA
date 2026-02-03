package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;

import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.WholesaleAdapter;
import com.forcepower.acedns.bean.WholeSaleInfo;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class ActivityWholeSaleReport extends FragmentActivity {
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static ListView mListViewList = null;

    WholesaleAdapter mWholesaleAdapter = null;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private Context mContext;
    private ProgressDialog mProgressDialog;
    private Handler mReportHandler;
    private int SELECTION = 0;
    private String mQuery = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mFStartDate = "";
    private String mFEndDate = "";
    private ArrayList<WholeSaleInfo> mWholeSaleInfoList = null;

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_wholesale_report);
        RegisterActivities.registerActivity(this);
        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");
        mFStartDate = getIntent().getStringExtra("FSTARTDATE");
        mFEndDate = getIntent().getStringExtra("FENDDATE");

        mContext = ActivityWholeSaleReport.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        InitializeView();
        mButtonBack.setOnClickListener(v -> finish());

        mListViewList.setOnItemClickListener((parent, view, position, id) -> {
        });

        mReportHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivityWholeSaleReport.this.runOnUiThread(() -> {
                    if (job == 1) {
                        if (!mWholeSaleInfoList.isEmpty()) {
                            mWholesaleAdapter = new WholesaleAdapter(mContext, R.layout.wholesale_report_child, mWholeSaleInfoList);
                            mListViewList.setAdapter(mWholesaleAdapter);
                        } else {
                            mWholesaleAdapter = new WholesaleAdapter(mContext, R.layout.wholesale_report_child, mWholeSaleInfoList);
                            mListViewList.setAdapter(mWholesaleAdapter);
                            if (SELECTION == 1) {
                                Utils.showToast(mContext, "No record for today");
                            }
                            if (SELECTION == 2) {
                                Utils.showToast(mContext, "No record for this month");
                            }
                            if (SELECTION == 3) {
                                Utils.showToast(mContext, "No record from " + mFStartDate + " to " + mFEndDate);
                            }
                        }
                    }
                });
            }
        };
        if (SELECTION == 0) {
            SELECTION = 1;
        }
        BuildQuery();
        FetchSaudaTransactionLogData(1);
    }

    public void InitializeView() {
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        mButtonBack = findViewById(R.id.back);
        mListViewList = findViewById(R.id.listView);
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    private void BuildQuery() {
        if (SELECTION == 1) {
            String timeStamp = Constants.dateString;
            mQuery = "SUBSTR(wholesale_trans_id,-14,8)='" + timeStamp + "'";
        }
        if (SELECTION == 2) {
            String timeStamp = Constants.dateString.substring(0, 6);
            mQuery = "SUBSTR(wholesale_trans_id,-14,6)='" + timeStamp + "'";
        }
        if (SELECTION == 3) {
            mQuery = "SUBSTR(wholesale_trans_id,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'";
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
                    mWholeSaleInfoList = mAceDnsTransactionDatabase.GETWholeSaleInfo(mQuery);
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
