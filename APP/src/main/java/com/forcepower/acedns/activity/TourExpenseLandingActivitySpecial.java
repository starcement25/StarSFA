package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import static com.forcepower.acedns.constants.Constants.cashTransferOrReceive;
import static com.forcepower.acedns.constants.Constants.expenseOrReceivedOrPaidAlias;

public class TourExpenseLandingActivitySpecial extends AceDnsParentActivity {

    private static Button mButtonBack = null;

    private static LinearLayout layout_cash_received = null;
    private static LinearLayout layout_cash_paid = null;
    private static LinearLayout layout_expense = null;
    private static LinearLayout mTravelLayout = null;
    private static LinearLayout mFoodLayout = null;
    private static LinearLayout mLodgeingLayout = null;
    private static LinearLayout mFreightLayout = null;
    private static LinearLayout mCashDepositeLayout = null;
    private static LinearLayout mCashTransferLayout = null;
    private static ImageView mImageViewLogo = null;
    Context mContext;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_tour_landing_special);
        mContext = this;
        RegisterActivities.registerActivity(this);
        initView();
    }


    public void initView() {
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext)
                + "~" + Utils.getDBVersion(mContext));
        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setOnClickListener(TourExpenseLandingActivitySpecial.this);

        ImageView imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        layout_cash_received = (LinearLayout) findViewById(R.id.layout_cash_received);
        layout_cash_paid = (LinearLayout) findViewById(R.id.layout_cash_paid);
        layout_expense = (LinearLayout) findViewById(R.id.layout_expense);
        mTravelLayout = (LinearLayout) findViewById(R.id.layout_travel);
        mFoodLayout = (LinearLayout) findViewById(R.id.layout_food);
        mLodgeingLayout = (LinearLayout) findViewById(R.id.layout_lodging);
        mFreightLayout = (LinearLayout) findViewById(R.id.layout_freight);
        mCashDepositeLayout = (LinearLayout) findViewById(R.id.layout_cash_deposit);
        mCashTransferLayout = (LinearLayout) findViewById(R.id.layout_cash_transfer);

        mImageViewLogo = (ImageView) findViewById(R.id.imagelogo);

        if (Constants.logoBmp != null) {
            mImageViewLogo.setVisibility(View.VISIBLE);
            mImageViewLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewLogo.setVisibility(View.GONE);
        }
        layout_cash_received.setOnClickListener(TourExpenseLandingActivitySpecial.this);
        layout_cash_paid.setOnClickListener(TourExpenseLandingActivitySpecial.this);
        layout_expense.setOnClickListener(TourExpenseLandingActivitySpecial.this);
        mTravelLayout.setOnClickListener(TourExpenseLandingActivitySpecial.this);
        mFoodLayout.setOnClickListener(TourExpenseLandingActivitySpecial.this);
        mLodgeingLayout.setOnClickListener(TourExpenseLandingActivitySpecial.this);
        mFreightLayout.setOnClickListener(TourExpenseLandingActivitySpecial.this);
        mCashDepositeLayout.setOnClickListener(TourExpenseLandingActivitySpecial.this);
        mCashTransferLayout.setOnClickListener(TourExpenseLandingActivitySpecial.this);
//		if (Constants.menuDetailsObj.getLoadingFreight().equalsIgnoreCase("yes")) {
//			mFreightLayout.setVisibility(View.VISIBLE);
//		} else {
//			mFreightLayout.setVisibility(View.GONE);
//		}
    }

    @Override
    public void onClick(View arg0) {
        if (arg0 == layout_cash_received) {
            expenseOrReceivedOrPaidAlias = "CR";
            startActivity(new Intent(mContext, ExpensesCashReceivedPaidActivity.class));
        }
        if (arg0 == layout_cash_paid) {
            expenseOrReceivedOrPaidAlias = "CP";
            startActivity(new Intent(mContext, ExpensesCashReceivedPaidActivity.class));
        }
        if (arg0 == layout_expense) {
            expenseOrReceivedOrPaidAlias = "EX";
            startActivity(new Intent(mContext, ExpensesCashReceivedPaidActivity.class));
        }
        if (arg0 == mTravelLayout) {
            startActivity(new Intent(mContext, TravelExpensesActivity.class));
        } else if (arg0 == mFoodLayout) {
            startActivity(new Intent(mContext, FoodingExpenseActivity.class));
        } else if (arg0 == mButtonBack) {
            finish();
        } else if (arg0 == mLodgeingLayout) {
            startActivity(new Intent(mContext, LodgingExpenseActivity.class));
        } else if (arg0 == mFreightLayout) {
            startActivity(new Intent(mContext, LoadingAndFreightActivity.class));
        } else if (arg0 == mCashDepositeLayout) {
            startActivity(new Intent(mContext, CashDepositActivity.class));
        } else if (arg0 == mCashTransferLayout) {
            ShowTransFerOrReceiveSelectionDialog();

        }
    }

    public void ShowTransFerOrReceiveSelectionDialog()//CT or CR
    {
        final Dialog payTypeDialog = new Dialog(mContext, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.uom_dialog);
        TextView txtMsg = (TextView) payTypeDialog.findViewById(R.id.title);
        txtMsg.setText("Please select a Type");

        RadioButton uom1RadioButton = (RadioButton) payTypeDialog.findViewById(R.id.radio0);
        uom1RadioButton.setText("Transfer");

        RadioButton uom2RadioButton = (RadioButton) payTypeDialog.findViewById(R.id.radio1);
        uom2RadioButton.setText("Receive");

        RadioGroup payTypeOption = (RadioGroup) payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                int radioButtonID = group.getCheckedRadioButtonId();
                View radioButton = group.findViewById(radioButtonID);
                int selectedRadio = group.indexOfChild(radioButton);
                payTypeDialog.cancel();
                if (selectedRadio == 0) {
                    cashTransferOrReceive = "CT";
                    startActivity(new Intent(mContext, CashTransferOrReceiveActivity.class));
                } else {
                    cashTransferOrReceive = "CR";
                    if (HTTPUtils.isConnectionPossible(mContext)) {
                        Utils.showProgressDialog(mContext, "Updating data..");
                        new Thread() {
                            public void run() {
                                new commonAsyncTaskMaster(mContext, "cash_transfer_master");
                            }
                        }.start();

                    } else {
                        Utils.showToast(mContext, "You need to have an active internet connection to use this feature.");
                    }
                }


            }
        });
        Button cancel = (Button) payTypeDialog.findViewById(R.id.btn_cancel);
        cancel.setVisibility(View.GONE);
        payTypeDialog.show();
    }

    @Override
    public void onBackPressed() {
        finish();
    }


}
