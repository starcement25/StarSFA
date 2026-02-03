package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BankAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCashDepositTask;
import com.forcepower.acedns.bean.BankDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.R.id.btn_bank;

public class CashDepositActivity extends FragmentActivity implements View.OnClickListener {

    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    Dialog bankDialog;
    ArrayList<BankDetails> bankList, tempBankList;
    ArrayList<String> bankNameList;
    EditText edAmt;
    Button btnBank, btnSubmit, btnBack;
    ImageView imgLogo;
    BankDetails selectdBank;
    BankAdapter adapter1;
    String lastStr = "", remarks = "";
    TextView dateHeader;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_cash_deposit);
        RegisterActivities.registerActivity(this);

        mContext = CashDepositActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);

        initView();
    }

    public void initView() {
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        dateHeader = (TextView) findViewById(R.id.txt_date_header);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        //txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        edAmt = (EditText) findViewById(R.id.ed_amt);
        edAmt.setBackgroundColor(Color.WHITE);

        btnBank = (Button) findViewById(btn_bank);
        btnBank.setOnClickListener(CashDepositActivity.this);
        btnSubmit = (Button) findViewById(R.id.btn_submit);
        btnSubmit.setOnClickListener(CashDepositActivity.this);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setOnClickListener(this);
    }

    public void chooseBankDialog() {
        bankDialog = new Dialog(CashDepositActivity.this);
        bankDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        bankDialog.setContentView(R.layout.select_with_search);
        bankList = setupDataHelperObj.getBankList();
        if (bankList != null) {
            bankNameList = new ArrayList<>();
            for (int ii = 0; ii < bankList.size(); ii++) {
                bankNameList.add(bankList.get(ii).getBankName());
            }
            tempBankList = new ArrayList<>();
            reInitialiseBankList();
            adapter1 = new BankAdapter(CashDepositActivity.this, R.layout.bank_list_child, tempBankList);
            TextView title = (TextView) bankDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = (EditText) bankDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseBankList();
                    }
                    lastStr = str;
                    filterBankArray(str.length(), str);
                    adapter1.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) bankDialog.findViewById(R.id.list);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    bankDialog.cancel();
                    selectdBank = tempBankList.get(arg2);
                    btnBank.setText(tempBankList.get(arg2).getBankName());
                }
            });
            Button cancel = (Button) bankDialog.findViewById(R.id.btn_ok);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    bankDialog.cancel();
                }
            });
            bankDialog.show();
        } else {
            Utils.showToast(mContext, "No bank details found. Please contact admin.");
        }

    }

    @Override
    public void onClick(View v) {
        if (v == btnBank) {
            chooseBankDialog();
        } else if (v == btnBack) {
            finish();

        } else if (v == btnSubmit) {
            new GPSTracker(mContext);
            String amount = edAmt.getText().toString();
            String selectedBank = btnBank.getText().toString();
            if (!Utils.isNumeric(amount) || Double.parseDouble(amount) <= 0) {
                Utils.showToast(mContext, "Please provide valid amount");
                return;
            }

            if (selectedBank.matches("Select Bank")) {
                Utils.showToast(mContext, "Please select the bank where cash will be deposited.");
                return;
            }
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            transDataHelperObj.insertToLocationTable("CD", timeStamp);
            transDataHelperObj.insertToCashDeposit(timeStamp, amount, selectedBank);
            if (HTTPUtils.isConnectionPossible(mContext)) {
                new TRANS_SubmitCashDepositTask(mContext, true).execute();
            }


        }
    }

    public void reInitialiseBankList() {
        tempBankList.removeAll(tempBankList);
        int size = tempBankList.size();
        int size1 = bankList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < bankList.size(); kk++) {
            tempBankList.add(bankList.get(kk));
        }
    }

    public void filterBankArray(int strCnt, String charVal) {
        int size = tempBankList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempBankList.get(ii).getBankName().length() >= strCnt) {
                if (tempBankList.get(ii).getBankName().toUpperCase().contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempBankList.remove(tempBankList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempBankList.remove(tempBankList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
    }


    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_MENU || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

}
