package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BankAdapter;
import com.forcepower.acedns.adapter.CashTransferAdapter;
import com.forcepower.acedns.adapter.RDSAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCashTransferTask;
import com.forcepower.acedns.bean.CashTransferReceive;
import com.forcepower.acedns.bean.RDSDetails;
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

import static com.forcepower.acedns.constants.Constants.UnVerifiedCashReceiveList;
import static com.forcepower.acedns.constants.Constants.alreadyReceivedAmount;
import static com.forcepower.acedns.constants.Constants.cashTransferIdForReceive;
import static com.forcepower.acedns.constants.Constants.cashTransferOrReceive;
import static com.forcepower.acedns.constants.Constants.transferredAmount;

public class CashTransferOrReceiveActivity extends FragmentActivity implements View.OnClickListener {

    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    Dialog rdsListDialog, customerListDialog;
    EditText edAmt, ed_amt_received, ed_amt_transferred;
    Button btnRDS, btnSubmit, btnBack;
    ImageView imgLogo;
    ArrayList<RDSDetails> rdsList, tempRDSList;
    RDSAdapter rdsAdapter;
    BankAdapter adapter1;
    String lastStr = "", employeeCodeTaggedWithRds = "", despatcherCode;
    TextView textViewTransactionType, textViewTransactionDateTime;
    ArrayList<CashTransferReceive> tempCashTransferList;
    CashTransferAdapter adapterCust;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_cash_transfer);
        RegisterActivities.registerActivity(this);

        mContext = CashTransferOrReceiveActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);
        initView();
        if (cashTransferOrReceive.matches("CR")) {
            if (UnVerifiedCashReceiveList.size() == 1) {
                cashTransferIdForReceive = UnVerifiedCashReceiveList.get(0).getcash_trans_rcv_trans_id();
                transferredAmount = Double.parseDouble(UnVerifiedCashReceiveList.get(0).getdespatch_value());
                alreadyReceivedAmount = Double.parseDouble(UnVerifiedCashReceiveList.get(0).getrec_value());
                ed_amt_received.setText(alreadyReceivedAmount + "");
                ed_amt_transferred.setText(transferredAmount + "");
                textViewTransactionDateTime.setText("Transfer made on: " + Utils.changeDateFormat("MMddhhmmss", "dd/MM hh:mm:ss", UnVerifiedCashReceiveList.get(0).getcash_transfer_date_time()));

                despatcherCode = UnVerifiedCashReceiveList.get(0).getdespatcher_code();
            } else {
                tempCashTransferList = new ArrayList<>();
                reInitialiseCustomerList();
                adapterCust = new CashTransferAdapter(CashTransferOrReceiveActivity.this,
                        R.layout.customer_list_child, tempCashTransferList);
                showChooseCashTransferDialog();
            }
        }

    }

    public void initView() {
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        textViewTransactionType = (TextView) findViewById(R.id.textViewTransactionType);
        textViewTransactionDateTime = (TextView) findViewById(R.id.textViewTransactionDateTime);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        edAmt = (EditText) findViewById(R.id.ed_amt);
        ed_amt_received = (EditText) findViewById(R.id.ed_amt_received);
        ed_amt_transferred = (EditText) findViewById(R.id.ed_amt_transferred);
        edAmt.setBackgroundColor(Color.WHITE);

        btnRDS = (Button) findViewById(R.id.btn_rds);
        btnRDS.setOnClickListener(CashTransferOrReceiveActivity.this);
        btnSubmit = (Button) findViewById(R.id.btn_submit);
        btnSubmit.setOnClickListener(CashTransferOrReceiveActivity.this);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setOnClickListener(this);

        if (cashTransferOrReceive.matches("CR")) {
            LinearLayout rdsNameLayout = (LinearLayout) findViewById(R.id.rdsNameLayout);
            rdsNameLayout.setVisibility(View.GONE);
            LinearLayout amountRcvdLayout = (LinearLayout) findViewById(R.id.amountRcvdLayout);
            LinearLayout amountTransferredLayout = (LinearLayout) findViewById(R.id.amountTransferredLayout);
            amountRcvdLayout.setVisibility(View.VISIBLE);
            amountTransferredLayout.setVisibility(View.VISIBLE);
            TextView textViewTransferReceive = (TextView) findViewById(R.id.textViewTransferReceive);
            textViewTransferReceive.setText("Amount to be received");
            textViewTransactionType.setText("Transaction Type: Receive");
            textViewTransactionDateTime.setVisibility(View.VISIBLE);
        } else {
            textViewTransactionType.setText("Transaction Type: Transfer");
        }
    }

    public void showRDSBranchDialog() {
        rdsList = setupDataHelperObj.getRDSList();
        if (rdsList.size() > 0) {
            tempRDSList = new ArrayList<>();
            reInitialiseRDSList();
            rdsAdapter = new RDSAdapter(CashTransferOrReceiveActivity.this,
                    R.layout.customer_list_child, tempRDSList);

            rdsListDialog = new Dialog(CashTransferOrReceiveActivity.this,
                    R.style.PauseDialog);
            rdsListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            rdsListDialog.setContentView(R.layout.choose_customer_search);
            rdsListDialog.setCancelable(false);
            TextView title = (TextView) rdsListDialog.findViewById(R.id.title);
            title.setText("Please select a RDS");
            EditText searchText = (EditText) rdsListDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {

                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseRDSList();
                    }
                    lastStr = str;
                    filterRDSArray(str.length(), str);
                    rdsAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });

            ListView dialogList = (ListView) rdsListDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(rdsAdapter);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    RDSDetails currentObj = tempRDSList.get(arg2);
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    rdsListDialog.cancel();
                    btnRDS.setText(currentObj.getRdsName());
                    employeeCodeTaggedWithRds = currentObj.getemp_code();
                }
            });

            Button addCustomer = (Button) rdsListDialog
                    .findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.INVISIBLE);
            addCustomer.setText("    Cancel    ");
            rdsListDialog.show();
        } else {
            Toast.makeText(mContext, "No existing RDS found.", Toast.LENGTH_LONG).show();
        }

    }

    @Override
    public void onClick(View v) {
        if (v == btnRDS) {
            showRDSBranchDialog();
        } else if (v == btnBack) {
            finish();

        } else if (v == btnSubmit) {
            new GPSTracker(mContext);
            String amount = edAmt.getText().toString();
            String selectedRDS = btnRDS.getText().toString();
            if (!Utils.isNumeric(amount) || Double.parseDouble(amount) <= 0) {
                Utils.showToast(mContext, "Please provide valid amount");
                return;
            }
            double totalReceivedAmount = alreadyReceivedAmount + Double.parseDouble(amount);
            if (cashTransferOrReceive.matches("CT") && selectedRDS.matches("Choose RDS")) {
                Utils.showToast(mContext, "Please select the RDS.");
                return;
            }

            if (cashTransferOrReceive.matches("CR")) {

                if (totalReceivedAmount > transferredAmount) {
                    Utils.showToast(mContext, "Total received amount can not exceed transferred amount.");
                    return;
                }
            }

            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            transDataHelperObj.insertToLocationTable(cashTransferOrReceive, timeStamp);
            if (cashTransferOrReceive.matches("CT")) {
                transDataHelperObj.insertToCashTransfer(timeStamp, amount, employeeCodeTaggedWithRds, "0", "0", Constants.employeeDetailObject.getEmpCode());
            } else//if(cashTransferOrReceive.matches("CR"))
            {
                transDataHelperObj.insertToCashTransfer(timeStamp, String.valueOf(transferredAmount), Constants.employeeDetailObject.getEmpCode(), String.valueOf(amount), "1", despatcherCode);
                transDataHelperObj.UpdateTotalReceivedAmountForCashTransfer(String.valueOf(totalReceivedAmount));
                if (totalReceivedAmount == transferredAmount) {
                    //update the transferred amount column status to 1
                    transDataHelperObj.UpdateStatusFlagForCashTransfer();
                }

            }

            if (HTTPUtils.isConnectionPossible(mContext)) {
                new TRANS_SubmitCashTransferTask(mContext, true).execute();
            }
        }
    }

    public void reInitialiseRDSList() {
        tempRDSList.removeAll(tempRDSList);
        int size = tempRDSList.size();
        int size1 = rdsList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < rdsList.size(); kk++) {
            tempRDSList.add(rdsList.get(kk));
        }

    }

    public void filterRDSArray(int strCnt, String charVal) {
        int size = tempRDSList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempRDSList.get(ii).getRdsName().length() >= strCnt) {
                if (tempRDSList.get(ii).getRdsName().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempRDSList.remove(tempRDSList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempRDSList.remove(tempRDSList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void showChooseCashTransferDialog() {
        customerListDialog = new Dialog(CashTransferOrReceiveActivity.this,
                R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);
        TextView title = (TextView) customerListDialog.findViewById(R.id.title);
        title.setText("Please select a cash transfer");
        EditText searchText = (EditText) customerListDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2,
                                      int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

                String str = s.toString();
                if (lastStr.length() > str.length()) {
                    reInitialiseCustomerList();
                }
                lastStr = str;
                filterCustomerArray(str.length(), str);
                adapterCust.notifyDataSetChanged();

                System.out.println("String::::::::" + str);
            }
        });

        ListView dialogList = (ListView) customerListDialog
                .findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3)

            {

                CashTransferReceive currentObj = tempCashTransferList.get(arg2);
                cashTransferIdForReceive = currentObj.getcash_trans_rcv_trans_id();
                transferredAmount = Double.parseDouble(currentObj.getdespatch_value());
                alreadyReceivedAmount = Double.parseDouble(currentObj.getrec_value());
                ed_amt_received.setText(alreadyReceivedAmount + "");
                ed_amt_transferred.setText(transferredAmount + "");
                despatcherCode = currentObj.getdespatcher_code();
                textViewTransactionDateTime.setText("Transfer made on: " + Utils.changeDateFormat("MMddhhmmss", "dd/MM hh:mm:ss", currentObj.getcash_transfer_date_time()));
                customerListDialog.cancel();


            }
        });

        Button addCustomer = (Button) customerListDialog
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        customerListDialog.show();
    }


    public void reInitialiseCustomerList() {
        tempCashTransferList.removeAll(tempCashTransferList);
        int size = tempCashTransferList.size();
        int size1 = UnVerifiedCashReceiveList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < UnVerifiedCashReceiveList.size(); kk++) {
            tempCashTransferList.add(UnVerifiedCashReceiveList.get(kk));
        }

    }


    public void filterCustomerArray(int strCnt, String charVal) {
        int size = tempCashTransferList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempCashTransferList.get(ii).getcash_transfer_date_time().length() >= strCnt) {

                if (tempCashTransferList.get(ii).getcash_transfer_date_time().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempCashTransferList.remove(tempCashTransferList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempCashTransferList.remove(tempCashTransferList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

}
