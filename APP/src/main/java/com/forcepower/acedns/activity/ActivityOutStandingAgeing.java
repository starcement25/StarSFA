package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import androidx.annotation.NonNull;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.OutstandingAgeingAdapter;
import com.forcepower.acedns.bean.OutstandingAgeing;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class ActivityOutStandingAgeing extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static ListView mListView = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextSearch = null;

    public Context mContext;
    ArrayList<OutstandingAgeing> mOutstandingAgeingList;
    OutstandingAgeingAdapter mOutstandingAgeingAdapter;
    ProgressDialog mProgressDialogOutstandingAgeing;
    Handler mHandlerOutstandingAgeing;
    private AceDnsDatabase mAceDnsDatabase;

    @SuppressLint("HandlerLeak")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_outstandingageing);
        RegisterActivities.registerActivity(this);

        mContext = ActivityOutStandingAgeing.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        InitializeView();

        mHandlerOutstandingAgeing = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogOutstandingAgeing.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                ActivityOutStandingAgeing.this.runOnUiThread(() -> {
                    if (jobToDo == 1) {
                        if (!mOutstandingAgeingList.isEmpty()) {
                            mOutstandingAgeingAdapter = new OutstandingAgeingAdapter(mContext, R.layout.outstanding_ageing_list_child, mOutstandingAgeingList);
                            mListView.setAdapter(mOutstandingAgeingAdapter);
                        } else {
                            Utils.showToast(mContext, "You have no outstanding");
                        }
                    }
                });
            }
        };

        PrepareOutstandingAgeingData(1);

        mEditTextSearch.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                if (mOutstandingAgeingList != null && mOutstandingAgeingAdapter != null) {
                    mOutstandingAgeingAdapter.getFilter().filter(s.toString());
                }
            }

            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        mButtonBack = findViewById(R.id.back);
        mButtonBack.setOnClickListener(ActivityOutStandingAgeing.this);
        mListView = findViewById(R.id.listoutsandingageing);
        mEditTextSearch = findViewById(R.id.editTextSearch);
    }

    public void onClick(View clkdView) {
        if (clkdView == mButtonBack) {
            finish();
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    public void PrepareOutstandingAgeingData(final int task) {
        mProgressDialogOutstandingAgeing = new ProgressDialog(mContext);
        mProgressDialogOutstandingAgeing.setCancelable(false);
        mProgressDialogOutstandingAgeing.setMessage("Fetching data from database.\nPlease wait..");
        mProgressDialogOutstandingAgeing.show();
        new Thread() {
            public void run() {
                if (task == 1) {
                    mOutstandingAgeingList = mAceDnsDatabase.GetOutstandingAgeingList();
                }
                Message msg = mHandlerOutstandingAgeing.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerOutstandingAgeing.sendMessage(msg);
            }
        }.start();
    }

}
