package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
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
import com.forcepower.acedns.adapter.PendingContractAdapter;
import com.forcepower.acedns.adapter.PendingContractCustomerAdapter;
import com.forcepower.acedns.bean.PendingContract;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.Objects;

public class ActivityPendingContract extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static ListView mListView = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextSearch = null;

    public Context mContext;
    ArrayList<PendingContract> mPendingContractList;
    ArrayList<PendingContract> mPendingContractCustomerList;
    PendingContractAdapter mPendingContractAdapter;
    ProgressDialog mProgressDialogPendingContarct;
    Handler mHandlerPendingContarct;
    private AceDnsDatabase mAceDnsDatabase;
    private String mBranchCode = "";
    private String mProductCode = "";
    private String mCustomerCode = "";
    private String mCustomerName = "";

    @SuppressLint("HandlerLeak")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pendingcontract);
        RegisterActivities.registerActivity(this);

        mContext = ActivityPendingContract.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        InitializeView();

        mHandlerPendingContarct = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogPendingContarct.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                ActivityPendingContract.this.runOnUiThread(() -> {
                    switch (jobToDo) {
                        case 1:
                            if (!mPendingContractList.isEmpty()) {
                                mPendingContractAdapter = new PendingContractAdapter(mContext, R.layout.pending_contract_list_child, mPendingContractList);
                                mListView.setAdapter(mPendingContractAdapter);
                            } else {
                                Utils.showToast(mContext, "You have no pending contract");
                            }
                            break;
                        case 2:
                            if (!mPendingContractCustomerList.isEmpty()) {
                                ShowPendingContractofCustomer();
                            } else {
                                Utils.showToast(mContext, "No record found");
                            }

                            break;
                    }
                });
            }
        };

        PrepareOrderStatusData(1);

        mListView.setOnItemClickListener((parent, view, position, id) -> {
            if (mPendingContractAdapter != null) {
                mBranchCode = Objects.requireNonNull(mPendingContractAdapter.getItem(position)).getBranchCode();
                mProductCode = Objects.requireNonNull(mPendingContractAdapter.getItem(position)).getProductCode();
                mCustomerCode = Objects.requireNonNull(mPendingContractAdapter.getItem(position)).getCustomerCode();
                mCustomerName = Objects.requireNonNull(mPendingContractAdapter.getItem(position)).getCustomerName();
                PrepareOrderStatusData(2);
                //Utils.showToast(mContext, mCustomerCode+"  "+mCustomerName);
            }
        });

        mEditTextSearch.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                if (mPendingContractList != null && mPendingContractAdapter != null) {
                    mPendingContractAdapter.getFilter().filter(s.toString());
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

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        mButtonBack = findViewById(R.id.back);
        mButtonBack.setOnClickListener(ActivityPendingContract.this);
        mListView = findViewById(R.id.listpendincontract);
        mEditTextSearch = findViewById(R.id.editTextSearch);
    }

    public void onClick(View clkdView) {
        if (clkdView == mButtonBack) {
            finish();
        }
    }

    public void PrepareOrderStatusData(final int task) {
        mProgressDialogPendingContarct = new ProgressDialog(mContext);
        mProgressDialogPendingContarct.setCancelable(false);
        mProgressDialogPendingContarct.setMessage("Fetching data from database.\nPlease wait..");
        mProgressDialogPendingContarct.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        mPendingContractList = mAceDnsDatabase.GetPendingContractAgeingList();
                        break;
                    case 2:
                        mPendingContractCustomerList = mAceDnsDatabase.GetPendingContractAgeingList(mBranchCode, mProductCode, mCustomerCode);
                        break;
                }
                Message msg = mHandlerPendingContarct.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPendingContarct.sendMessage(msg);
            }
        }.start();
    }

    public void ShowPendingContractofCustomer() {
        final Dialog mDialogPendingContractofCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogPendingContractofCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogPendingContractofCustomer.setContentView(R.layout.dialog_pending_contract);
        mDialogPendingContractofCustomer.setCancelable(true);

        TextView textviewcustomername = mDialogPendingContractofCustomer.findViewById(R.id.textViewCustomerName);
        textviewcustomername.setText(mCustomerName);

        Button buttonBack = mDialogPendingContractofCustomer.findViewById(R.id.back);
        buttonBack.setOnClickListener(v -> mDialogPendingContractofCustomer.cancel());

        ListView pendincontractproductList = mDialogPendingContractofCustomer.findViewById(R.id.listpendincontractproduct);

        PendingContractCustomerAdapter adapter = new PendingContractCustomerAdapter(mContext, R.layout.dialog_pending_contract_list, mPendingContractCustomerList);
        pendincontractproductList.setAdapter(adapter);
        pendincontractproductList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
        });
        mDialogPendingContractofCustomer.show();
    }
}
