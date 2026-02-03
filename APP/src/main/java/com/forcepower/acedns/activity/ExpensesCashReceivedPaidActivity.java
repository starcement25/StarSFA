package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.backgroundTask.TRANS_CashDepositeReceiveTask;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.expenseOrReceivedOrPaidAlias;

public class ExpensesCashReceivedPaidActivity extends FragmentActivity implements OnClickListener {

    Dialog jobOptiondialog;
    String transactionType = "", amount = "", dateVal = "";
    Button btnDate, btnRemarks, btnSubmit, btnBack;
    TextView txtVersion;
    EditText edAmount;
    ImageView imgLogo;
    Context mContext;
    AceDnsTransactionDatabase helperobj;
    CaldroidListener listener;
    Date currentDate;
    SimpleDateFormat dateFormat;
    Button multipleRemarksButton;
    String remarks = "";
    String billTotal = "0.00";
    boolean rematrksDate = false;
    Handler mHandler;
    ProgressDialog loader;
    String remarksDateStr = "";
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_expenses_cash_received_paid);
        RegisterActivities.registerActivity(this);
        mContext = ExpensesCashReceivedPaidActivity.this;
        helperobj = new AceDnsTransactionDatabase(ExpensesCashReceivedPaidActivity.this);
        dateFormat = new SimpleDateFormat("yyyy-MM-dd");
        try {
            String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
        } catch (Exception e) {
            currentDate = new Date();
        }

        initView();
        if (expenseOrReceivedOrPaidAlias.matches("EX")) {
            transactionType = "EX";
            showJobOptionDialog();
        } else if (expenseOrReceivedOrPaidAlias.matches("CR")) {
            transactionType = "CR";
        } else //if(expenseOrReceivedOrPaidAlias.matches("CP"))
        {
            transactionType = "CP";
            showJobOptionDialog();

        }

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    new TRANS_CashDepositeReceiveTask(mContext, true).execute();
                }
            }
        };

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (rematrksDate) {
                    dialogCaldroidFragment.dismiss();
                    multipleRemarksButton.setText(dateFormat.format(date));
                    remarksDateStr = new SimpleDateFormat("yyyyMMdd").format(date);
                } else {
                    if (date.after(currentDate)) {
                        Utils.showToast(mContext, "Future dates cannot be selected");
                    } else {
                        btnDate.setText(dateFormat.format(date));
                        dialogCaldroidFragment.dismiss();
                    }
                }
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {
                if (rematrksDate) {
                    dialogCaldroidFragment.dismiss();
                    multipleRemarksButton.setText(dateFormat.format(date));
                    remarksDateStr = new SimpleDateFormat("yyyyMMdd").format(date);
                } else {
                    if (date.after(currentDate)) {
                        Utils.showToast(mContext, "Future dates cannot be selected");
                    } else {
                        btnDate.setText(dateFormat.format(date));
                        dialogCaldroidFragment.dismiss();
                    }
                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };
    }

    public void initView() {
        txtVersion = (TextView) findViewById(R.id.txt_version);
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }
        //txtVersion.setText("Ver~"+Utils.getAppVersion(LoadingAndFreightActivity.this));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        edAmount = (EditText) findViewById(R.id.et_payeee);

        btnDate = (Button) findViewById(R.id.bt_date);
        btnRemarks = (Button) findViewById(R.id.btn_remarks);
        btnSubmit = (Button) findViewById(R.id.btn_submit);
        btnBack = (Button) findViewById(R.id.back);

        btnDate.setOnClickListener(this);
        btnRemarks.setOnClickListener(this);
        btnSubmit.setOnClickListener(this);
        btnBack.setOnClickListener(this);

    }

    @Override
    public void onClick(View v) {
        if (v == btnDate) {
            rematrksDate = false;
            chooseDateDialog();
        } else if (v == btnSubmit) {
            doSubmitJob();
        } else if (v == btnBack) {
            finish();
        } else if (v == btnRemarks) {
            showMultiRemarksDialog();
        }
    }

    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }


    public void showJobOptionDialog() {
        jobOptiondialog = new Dialog(ExpensesCashReceivedPaidActivity.this, R.style.PauseDialog);
        jobOptiondialog.setCancelable(false);
        jobOptiondialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        jobOptiondialog.setContentView(R.layout.stockout_dialog);
        TextView txtMsg = (TextView) jobOptiondialog.findViewById(R.id.title);
        if (transactionType.equalsIgnoreCase("ex")) {
            txtMsg.setText("Select an Option.");
        } else {
            txtMsg.setText("Cash Paid to.");
        }

        Button branch = (Button) jobOptiondialog.findViewById(R.id.btn_branch);
        if (transactionType.equalsIgnoreCase("ex")) {
            branch.setText("DIRECT");
        } else//if(transactionType.equalsIgnoreCase("cp"))
        {
            branch.setText("BRANCH");
        }

        branch.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                if (transactionType.equalsIgnoreCase("ex")) {
                    transactionType = "EXD";
                } else {

                }

                jobOptiondialog.cancel();
            }
        });
        Button customer = (Button) jobOptiondialog.findViewById(R.id.btn_cust);
        if (transactionType.equalsIgnoreCase("ex")) {
            customer.setText("INDIRECT");
        } else//if(transactionType.equalsIgnoreCase("cp"))
        {
            customer.setText("BANK");
        }

        customer.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                if (transactionType.equalsIgnoreCase("ex")) {
                    transactionType = "EXID";
                } else {
                    transactionType = "CPB";
                }

                jobOptiondialog.cancel();
            }
        });
        jobOptiondialog.show();
    }

    private void showMultiRemarksDialog() {
        final Dialog checkoutDialog = new Dialog(mContext, R.style.PauseDialog);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.dialog_checked_out);
        checkoutDialog.setCancelable(true);
        TextView title = (TextView) checkoutDialog.findViewById(R.id.title);
        Button submit = (Button) checkoutDialog.findViewById(R.id.btn_submit);
        title.setText("Remarks if any?");

        TextView customer_name = (TextView) checkoutDialog.findViewById(R.id.customer_name);
        customer_name.setVisibility(View.GONE);

        final EditText remark_box = (EditText) checkoutDialog.findViewById(R.id.remark_box);
        remark_box.setText(remarks);
        submit.setText("OK");
        submit.setOnClickListener(new OnClickListener() {

            @Override
            public void onClick(View v) {
                remarks = remark_box.getText().toString();
                checkoutDialog.dismiss();
            }
        });
        checkoutDialog.show();
    }

    public void doSubmitJob() {
        amount = edAmount.getText().toString();
        dateVal = btnDate.getText().toString();

        if (amount.length() > 0 && !dateVal.equalsIgnoreCase("dd/MM/yy")) {
            saveFreightExpenseData();
        } else {
            Utils.showToast(ExpensesCashReceivedPaidActivity.this, "Please provide data for the mandatory fields, amount and date");
        }
    }

    public void saveFreightExpenseData() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                new GPSTracker(mContext);
                String timeStamp = "";
//				if(Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes") && remarksDateStr.length() != 0){
//					timeStamp = remarksDateStr + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
//				}else{
                timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
//				}
                String transId = expenseOrReceivedOrPaidAlias + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                helperobj.insertToLocationTable(expenseOrReceivedOrPaidAlias, timeStamp);
                helperobj.insertToCashDepositReceiveDetails(transId, transactionType, dateVal, amount, remarks);

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
        //this.getWindow().setType(WindowManager.LayoutParams.TYPE_KEYGUARD);
    }


    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_MENU || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

}
