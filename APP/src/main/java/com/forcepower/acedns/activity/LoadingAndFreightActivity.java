package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.text.InputFilter;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitFreightExpenseTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;

import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DecimalDigitsInputFilter;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class LoadingAndFreightActivity extends FragmentActivity implements OnClickListener {

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
        setContentView(R.layout.activity_loading_freight);
        RegisterActivities.registerActivity(this);
        mContext = LoadingAndFreightActivity.this;
        helperobj = new AceDnsTransactionDatabase(LoadingAndFreightActivity.this);
        dateFormat = new SimpleDateFormat("yyyy-MM-dd");
        try {
            String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
        } catch (Exception e) {
            currentDate = new Date();
        }

        initView();
        showJobOptionDialog();

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    new TRANS_SubmitFreightExpenseTask(mContext, true).execute();
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
        jobOptiondialog = new Dialog(LoadingAndFreightActivity.this, R.style.PauseDialog);
        jobOptiondialog.setCancelable(false);
        jobOptiondialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        jobOptiondialog.setContentView(R.layout.stockout_dialog);
        TextView txtMsg = (TextView) jobOptiondialog.findViewById(R.id.title);
        txtMsg.setText("Select an Option.");
        Button branch = (Button) jobOptiondialog.findViewById(R.id.btn_branch);
        branch.setText("Loading");
        branch.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                transactionType = "LOADING";
                jobOptiondialog.cancel();
            }
        });
        Button customer = (Button) jobOptiondialog.findViewById(R.id.btn_cust);
        customer.setText("Freight");
        customer.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                transactionType = "FREIGHT";
                jobOptiondialog.cancel();
            }
        });
        jobOptiondialog.show();
    }

    public void showMultiRemarksDialog() {
        final Dialog multiRemarksDialog = new Dialog(mContext);
        multiRemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        multiRemarksDialog.setContentView(R.layout.multi_instruction_dialog);
        multiRemarksDialog.setCancelable(false);
        TextView title = (TextView) multiRemarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst1 = (EditText) multiRemarksDialog.findViewById(R.id.ed_input_r1);
        multipleRemarksButton = (Button) multiRemarksDialog.findViewById(R.id.ed_input_r2);
        final EditText edInst3 = (EditText) multiRemarksDialog.findViewById(R.id.ed_input_r3);
        edInst3.setFilters(new InputFilter[]{new DecimalDigitsInputFilter(16, 2)});
        Button submit = (Button) multiRemarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                multiRemarksDialog.cancel();
                String instruction = "";
                billTotal = edInst3.getText().toString();
                instruction = edInst1.getText().toString() + ";" + multipleRemarksButton.getText().toString() + ";" + billTotal;
                remarks = instruction;
            }
        });
        multipleRemarksButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                rematrksDate = true;
                chooseDateDialog();
            }
        });
        multiRemarksDialog.show();
    }

    public void doSubmitJob() {
        amount = edAmount.getText().toString();
        dateVal = btnDate.getText().toString();

        if (transactionType.length() > 0 && amount.length() > 0 && !dateVal.equalsIgnoreCase("dd/MM/yyyy")) {
            saveFreightExpenseData();
        } else {
            Utils.showToast(LoadingAndFreightActivity.this, "Please fill all the mandatory  fields");
        }
    }

    public void saveFreightExpenseData() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {

                String timeStamp = "";
                if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes") && remarksDateStr.length() != 0) {
                    timeStamp = remarksDateStr + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                } else {
                    timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                }
                String freight_exp_id = "TR" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                helperobj.insertToLocationTable("TR", timeStamp);
                helperobj.insertToFreightExpense(freight_exp_id, transactionType, dateVal, amount, remarks);

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
