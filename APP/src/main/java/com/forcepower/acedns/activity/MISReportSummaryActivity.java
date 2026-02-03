package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;

import android.text.method.ScrollingMovementMethod;
import android.util.SparseBooleanArray;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.Scroller;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.MessageFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class MISReportSummaryActivity extends FragmentActivity implements OnClickListener {
    Context mContext;
    AceDnsTransactionDatabase transDataHelperObj;
    SimpleDateFormat dateFormat, dateFormat1;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    ImageView imgLogo, showDurationLayout, hideDurationLayout;
    Button btnBack, btnStartDate, btnEndDate, btnDateDone;
    TextView startDatetxt, endDateTxt;
    String startDate = "", endDate = "";
    Date startDate1, endDate1;
    boolean isStartDate = false;
    ProgressDialog loader;
    RelativeLayout durationLayout;
    LinearLayout customDateLayout;
    FrameLayout btnToday, btnMTD, btnCustom;
    Handler mHandler, misHandler, grpHandler;
    String reportDuration = "TODAY";
    String[] salesArray;
    TextView txtPurchase, txtSales, txtStockTransfer;
    LinearLayout layoutPurchase, layoutSales, layoutStockTransfer, layoutStock, layoutLoyalty;
    String selectedBranchCode, selectedRDSCode = "", selectedGroupCode = "";
    Button btnBranch, btnRds, btnGroup;
    ArrayList<String> misOptionList;
    private CaldroidFragment dialogCaldroidFragment;

    @SuppressLint({"HandlerLeak", "SimpleDateFormat"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report_summery_mis);
        RegisterActivities.registerActivity(this);

        mContext = MISReportSummaryActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        dateFormat1 = new SimpleDateFormat("yyyyMMdd");

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    MISReportSummaryActivity.this.runOnUiThread(() -> setText());
                }
            }
        };

        misHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                final String aResponse = msg.getData().getString("message");
                loader.cancel();
                MISReportSummaryActivity.this.runOnUiThread(() -> showOptionForMISReport(aResponse));
            }
        };

        grpHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                loader.cancel();
                MISReportSummaryActivity.this.runOnUiThread(() -> showOptionForMISReportGroupWise());
            }
        };


        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate1 = date;
                    startDatetxt.setText(dateFormat.format(date));
                    startDate = dateFormat1.format(date);
                } else {
                    endDate1 = date;
                    endDateTxt.setText(dateFormat.format(date));
                    endDate = dateFormat1.format(date);
                }
                dialogCaldroidFragment.dismiss();
            }
        };
        initView();
    }

    @SuppressLint("SetTextI18n")
    public void initView() {
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        btnBranch = findViewById(R.id.btn_branch);
        btnBranch.setScroller(new Scroller(MISReportSummaryActivity.this));
        btnBranch.setVerticalScrollBarEnabled(true);
        btnBranch.setMovementMethod(new ScrollingMovementMethod());
        btnRds = findViewById(R.id.btn_rds);
        btnRds.setScroller(new Scroller(MISReportSummaryActivity.this));
        btnRds.setVerticalScrollBarEnabled(true);
        btnRds.setMovementMethod(new ScrollingMovementMethod());
        btnGroup = findViewById(R.id.btn_group);
        btnGroup.setScroller(new Scroller(MISReportSummaryActivity.this));
        btnGroup.setVerticalScrollBarEnabled(true);
        btnGroup.setMovementMethod(new ScrollingMovementMethod());
        btnGroup.setOnClickListener(this);
        btnBranch.setOnClickListener(this);
        btnRds.setOnClickListener(this);

        txtPurchase = findViewById(R.id.txt_purchase);
        txtSales = findViewById(R.id.txt_sales);
        txtStockTransfer = findViewById(R.id.txt_stock_transfer);

        layoutPurchase = findViewById(R.id.purchase_layout);
        layoutSales = findViewById(R.id.sales_layout);
        layoutStockTransfer = findViewById(R.id.stock_transfer_layout);
        layoutStock = findViewById(R.id.stock_layout);

        layoutLoyalty = findViewById(R.id.img_loyalty);
        layoutLoyalty.setVisibility(View.GONE);
        layoutLoyalty.setOnClickListener(this);

        layoutPurchase.setOnClickListener(this);
        layoutSales.setOnClickListener(this);
        layoutStockTransfer.setOnClickListener(this);
        layoutStock.setOnClickListener(this);

        imgLogo = findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        showDurationLayout = findViewById(R.id.image_clk);
        hideDurationLayout = findViewById(R.id.image_go);
        showDurationLayout.setOnClickListener(this);
        hideDurationLayout.setOnClickListener(this);

        durationLayout = findViewById(R.id.duration_layout);
        customDateLayout = findViewById(R.id.custom_date_layout);

        startDatetxt = findViewById(R.id.txt_start_date);
        startDatetxt.setText("");
        endDateTxt = findViewById(R.id.txt_end_date);
        endDateTxt.setText("");


        btnBack = findViewById(R.id.back);
        btnStartDate = findViewById(R.id.btn_start_date);
        btnEndDate = findViewById(R.id.btn_end_date);
        btnDateDone = findViewById(R.id.btn_date_done);

        btnBack.setOnClickListener(this);
        btnStartDate.setOnClickListener(this);
        btnEndDate.setOnClickListener(this);
        btnDateDone.setOnClickListener(this);

        btnToday = findViewById(R.id.btn_today);
        btnMTD = findViewById(R.id.btn_mtd);
        btnCustom = findViewById(R.id.btn_custom);

        btnToday.setOnClickListener(this);
        btnMTD.setOnClickListener(this);
        btnCustom.setOnClickListener(this);

        showMISOptions("");
    }

    @SuppressLint("SimpleDateFormat")
    public void generateListData(final String rdsCode, final String groupCode) {
        new Thread() {
            public void run() {
                if (reportDuration.equalsIgnoreCase("TODAY")) {
                    Date date;
                    try {
                        date = new SimpleDateFormat("yyyyMMdd").parse(Constants.dateString);
                    } catch (Exception e) {
                        date = new Date();
                    }
                    Calendar cal = Calendar.getInstance();
                    cal.setTime(date);
                    cal.add(Calendar.DAY_OF_YEAR, -1);
                    Date oneDayBefore = cal.getTime();
                    String dateStr = new SimpleDateFormat("yyyyyMMdd").format(oneDayBefore);
                    dateStr = dateStr.substring(1);
                    salesArray = transDataHelperObj.getMISReportSummerySales(dateStr, rdsCode, groupCode);
                } else if (reportDuration.equalsIgnoreCase("MTD")) {
                    String timeStamp = Constants.dateString.substring(0, 6);
                    salesArray = transDataHelperObj.getMISReportSummerySales(timeStamp, rdsCode, groupCode);
                }
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    @Override
    public void onClick(View v) {
        if (v == btnBack) {
            transDataHelperObj.closeDatabase();
            finish();
        } else if (v == btnStartDate) {
            isStartDate = true;
            chooseDateDialog();
        } else if (v == btnEndDate) {
            isStartDate = false;
            chooseDateDialog();
        } else if (v == btnDateDone) {
            try {
                if (endDate1.after(new Date())) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else if (startDate1.after(endDate1)) {
                    Utils.showToast(mContext, "Start Date should be less than or equal to End Date");
                } else {
                    showDurationLayout.startAnimation(fadeIn);
                    showDurationLayout.setVisibility(View.VISIBLE);
                    durationLayout.startAnimation(bottomDown);
                    durationLayout.setVisibility(View.GONE);
                    loader = new ProgressDialog(mContext);
                    loader.setMessage("Fetching Data.Please wait..");
                    loader.show();
                    showCustomReport();
                    endDateTxt.setText("");
                    startDatetxt.setText("");
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Please choose the Dates again.");
            }
        } else if (v == btnToday) {
            showDurationLayout.startAnimation(fadeIn);
            showDurationLayout.setVisibility(View.VISIBLE);
            durationLayout.startAnimation(bottomDown);
            durationLayout.setVisibility(View.GONE);
            startDate = "";
            endDate = "";
            loader.show();
            reportDuration = "TODAY";
            changeReportOptionBG(4);
            generateListData(selectedRDSCode, selectedGroupCode);
        } else if (v == btnMTD) {
            showDurationLayout.startAnimation(fadeIn);
            showDurationLayout.setVisibility(View.VISIBLE);
            durationLayout.startAnimation(bottomDown);
            durationLayout.setVisibility(View.GONE);
            startDate = "";
            endDate = "";
            loader.show();
            reportDuration = "MTD";
            changeReportOptionBG(5);
            generateListData(selectedRDSCode, selectedGroupCode);
        } else if (v == btnCustom) {
            startDate = "";
            endDate = "";
            reportDuration = "CUSTOM";
            changeReportOptionBG(6);
        } else if (v == hideDurationLayout) {
            showDurationLayout.startAnimation(fadeIn);
            showDurationLayout.setVisibility(View.VISIBLE);
            durationLayout.startAnimation(bottomDown);
            durationLayout.setVisibility(View.GONE);
        } else if (v == showDurationLayout) {
            showDurationLayout.startAnimation(fadeOut);
            showDurationLayout.setVisibility(View.GONE);
            durationLayout.startAnimation(bottomUp);
            durationLayout.setVisibility(View.VISIBLE);
        } else if (v == layoutPurchase) {
            Intent intent = new Intent(MISReportSummaryActivity.this, MISReportGroupwiseActivity.class);
            intent.putExtra("ShowFor", "PURCHASE");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            intent.putExtra("RDSCode", selectedRDSCode);
            intent.putExtra("GroupCode", selectedGroupCode);
            startActivity(intent);
        } else if (v == layoutSales) {
            Intent intent = new Intent(MISReportSummaryActivity.this, MISReportGroupwiseActivity.class);
            intent.putExtra("ShowFor", "SALES");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            intent.putExtra("RDSCode", selectedRDSCode);
            intent.putExtra("GroupCode", selectedGroupCode);
            startActivity(intent);
        } else if (v == layoutStockTransfer) {
            Intent intent = new Intent(MISReportSummaryActivity.this, MISReportGroupwiseActivity.class);
            intent.putExtra("ShowFor", "STOCK TRANSFER");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            intent.putExtra("RDSCode", selectedRDSCode);
            intent.putExtra("GroupCode", selectedGroupCode);
            startActivity(intent);
        } else if (v == layoutStock) {
            Intent intent = new Intent(MISReportSummaryActivity.this, MISReportGroupwiseActivity.class);
            intent.putExtra("ShowFor", "STOCK");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            intent.putExtra("RDSCode", selectedRDSCode);
            intent.putExtra("GroupCode", selectedGroupCode);
            startActivity(intent);
        } else if (v == btnBranch) {
            showMISOptions("");
        } else if (v == btnRds) {
            showMISOptions(selectedBranchCode);
        } else if (v == btnGroup) {
            showMISGroupOptions(selectedRDSCode);
        } else if (v == layoutLoyalty) {
            Intent intent = new Intent(MISReportSummaryActivity.this, ReportActivityLoyalty.class);
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            startActivity(intent);
        }
    }

    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        Bundle bundle = new Bundle();
        bundle.putString(CaldroidFragment.DIALOG_TITLE, "Select a date");
        dialogCaldroidFragment.setArguments(bundle);
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    public void showCustomReport() {
        new Thread() {
            public void run() {
                salesArray = transDataHelperObj.getMISCustomReportSummerySales(startDate, endDate, selectedRDSCode, selectedGroupCode);
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
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

    public void changeReportOptionBG(int selectedLayoutId) {
        switch (selectedLayoutId) {
            case 4:
                btnToday.setBackgroundColor(Color.parseColor("#B6B6B4"));
                btnMTD.setBackgroundColor(Color.parseColor("#E5E4E2"));
                btnCustom.setBackgroundColor(Color.parseColor("#E5E4E2"));
                customDateLayout.startAnimation(bottomDown);
                customDateLayout.setVisibility(View.GONE);
                break;
            case 5:
                btnToday.setBackgroundColor(Color.parseColor("#E5E4E2"));
                btnMTD.setBackgroundColor(Color.parseColor("#B6B6B4"));
                btnCustom.setBackgroundColor(Color.parseColor("#E5E4E2"));
                customDateLayout.startAnimation(bottomDown);
                customDateLayout.setVisibility(View.GONE);
                break;
            case 6:
                btnToday.setBackgroundColor(Color.parseColor("#E5E4E2"));
                btnMTD.setBackgroundColor(Color.parseColor("#E5E4E2"));
                btnCustom.setBackgroundColor(Color.parseColor("#B6B6B4"));
                customDateLayout.startAnimation(bottomUp);
                customDateLayout.setVisibility(View.VISIBLE);
                break;
        }
    }

    public void setText() {
        txtPurchase.setText(salesArray[0]);
        txtSales.setText(salesArray[1]);
        txtStockTransfer.setText(salesArray[2]);
    }

    @SuppressLint("SetTextI18n")
    public void showOptionForMISReport(final String parameter) {
        final SimpleStringAdapter adapterMISOption;
        adapterMISOption = new SimpleStringAdapter(MISReportSummaryActivity.this, R.layout.multiple_cust_child, misOptionList, "hideRest");
        final Dialog misOptionDialog = new Dialog(MISReportSummaryActivity.this, R.style.PauseDialog);
        misOptionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        misOptionDialog.setContentView(R.layout.select_multiple_from_list);
        misOptionDialog.setCancelable(false);
        TextView title = misOptionDialog.findViewById(R.id.title);
        title.setText("Please select an Option");
        final ListView dialogList = misOptionDialog.findViewById(R.id.list);
        dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        dialogList.setAdapter(adapterMISOption);
        RelativeLayout chkAllLayout = misOptionDialog.findViewById(R.id.select_all_layout);
        chkAllLayout.setVisibility(View.VISIBLE);
        final CheckBox chkSelectAll = misOptionDialog.findViewById(R.id.chk_all);
        chkSelectAll.setOnClickListener(v -> {
            if (chkSelectAll.isChecked()) {
                for (int i = 0; i <= dialogList.getCount(); i++) {
                    dialogList.setItemChecked(i, true);
                }
            } else {
                for (int i = 0; i <= dialogList.getCount(); i++) {
                    dialogList.setItemChecked(i, false);
                }
            }
        });
        Button submit = misOptionDialog.findViewById(R.id.button1);
        submit.setOnClickListener(arg0 -> {
            String selectedCodes = "";
            String selectedNames = "";

            if (chkSelectAll.isChecked()) {
                for (int i = 0; i < adapterMISOption.getCount(); i++) {
                    String val = adapterMISOption.getItem(i);
                    assert val != null;
                    String[] valueArray = val.split("\\*");
                    String selectedName = valueArray[0];
                    String selectedCode = valueArray[1];
                    selectedCodes = MessageFormat.format("{0}''{1}'',", selectedCodes, selectedCode);
                    selectedNames = MessageFormat.format("{0}{1},", selectedNames, selectedName);
                }
                selectedCodes = selectedCodes.substring(0, selectedCodes.length() - 1);
                if (parameter.isEmpty()) {
                    btnBranch.setText("All");
                    selectedBranchCode = selectedCodes;
                    showMISOptions(selectedBranchCode);
                } else {
                    selectedRDSCode = selectedCodes;
                    btnRds.setText("All");
                    showMISGroupOptions(selectedRDSCode);
                }
                misOptionDialog.cancel();
            } else {
                final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                int checkedItemsCount = checkedItems.size();
                if (checkedItemsCount > 0) {
                    for (int i = 0; i < checkedItemsCount; ++i) {
                        int position = checkedItems.keyAt(i);
                        if (checkedItems.valueAt(i)) {
                            String val = adapterMISOption.getItem(position);
                            assert val != null;
                            String[] valueArray = val.split("\\*");
                            String selectedName = valueArray[0];
                            String selectedCode = valueArray[1];
                            selectedCodes = MessageFormat.format("{0}''{1}'',", selectedCodes, selectedCode);
                            selectedNames = MessageFormat.format("{0}{1},", selectedNames, selectedName);
                        }
                    }
                    selectedCodes = selectedCodes.substring(0, selectedCodes.length() - 1);
                    selectedNames = selectedNames.substring(0, selectedNames.length() - 1);
                    if (parameter.isEmpty()) {
                        btnBranch.setText(selectedNames);
                        selectedBranchCode = selectedCodes;
                        showMISOptions(selectedBranchCode);
                    } else {
                        selectedRDSCode = selectedCodes;
                        btnRds.setText(selectedNames);
                        showMISGroupOptions(selectedRDSCode);
                    }
                    misOptionDialog.cancel();
                } else {
                    Utils.showToast(mContext, "Please select an option");
                }
            }

        });
        misOptionDialog.show();
    }

    public void showMISOptions(final String parameter) {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                if (parameter.isEmpty()) {
                    misOptionList = transDataHelperObj.getMISListForBranch();
                } else {
                    misOptionList = transDataHelperObj.getMISListForRDS(parameter);
                }
                Message msgObj = misHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", parameter);
                msgObj.setData(b);
                misHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void showMISGroupOptions(final String rdsCodes) {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                misOptionList = transDataHelperObj.getMISListGroupWise(rdsCodes);
                Message msgObj = grpHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", rdsCodes);
                msgObj.setData(b);
                grpHandler.sendMessage(msgObj);
            }
        }.start();
    }

    @SuppressLint("SetTextI18n")
    public void showOptionForMISReportGroupWise() {
        final SimpleStringAdapter adapterMISOption;
        adapterMISOption = new SimpleStringAdapter(MISReportSummaryActivity.this, R.layout.multiple_cust_child, misOptionList, "hideRest");
        final Dialog misOptionDialog = new Dialog(MISReportSummaryActivity.this, R.style.PauseDialog);
        misOptionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        misOptionDialog.setContentView(R.layout.select_multiple_from_list);
        misOptionDialog.setCancelable(false);
        TextView title = misOptionDialog.findViewById(R.id.title);
        title.setText("Please select an Option");
        final ListView dialogList = misOptionDialog.findViewById(R.id.list);
        dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        dialogList.setAdapter(adapterMISOption);
        RelativeLayout chkAllLayout = misOptionDialog.findViewById(R.id.select_all_layout);
        chkAllLayout.setVisibility(View.VISIBLE);
        final CheckBox chkSelectAll = misOptionDialog.findViewById(R.id.chk_all);
        chkSelectAll.setOnClickListener(v -> {
            if (chkSelectAll.isChecked()) {
                for (int i = 0; i <= dialogList.getCount(); i++) {
                    dialogList.setItemChecked(i, true);
                }
            } else {
                for (int i = 0; i <= dialogList.getCount(); i++) {
                    dialogList.setItemChecked(i, false);
                }
            }
        });
        Button submit = misOptionDialog.findViewById(R.id.button1);
        submit.setOnClickListener(arg0 -> {
            String selectedCodes = "";
            String selectedNames = "";
            if (chkSelectAll.isChecked()) {
                for (int i = 0; i < adapterMISOption.getCount(); ++i) {
                    String val = adapterMISOption.getItem(i);
                    assert val != null;
                    String[] valueArray = val.split("\\*");
                    String selectedName = valueArray[0];
                    String selectedCode = valueArray[1];
                    selectedCodes = MessageFormat.format("{0}''{1}'',", selectedCodes, selectedCode);
                    selectedNames = MessageFormat.format("{0}{1},", selectedNames, selectedName);
                }
                selectedCodes = selectedCodes.substring(0, selectedCodes.length() - 1);
                selectedGroupCode = selectedCodes;
                loader = new ProgressDialog(mContext);
                loader.setMessage("Fetching Data.Please wait..");
                loader.show();
                btnGroup.setText("All");
                if (reportDuration.equalsIgnoreCase("TODAY")) {
                    generateListData(selectedRDSCode, selectedGroupCode);
                } else {
                    showCustomReport();
                }
                misOptionDialog.cancel();
            } else {
                final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                int checkedItemsCount = checkedItems.size();
                if (checkedItemsCount > 0) {
                    for (int i = 0; i < checkedItemsCount; ++i) {
                        int position = checkedItems.keyAt(i);
                        if (checkedItems.valueAt(i)) {
                            String val = adapterMISOption.getItem(position);
                            assert val != null;
                            String[] valueArray = val.split("\\*");
                            String selectedName = valueArray[0];
                            String selectedCode = valueArray[1];
                            selectedCodes = MessageFormat.format("{0}''{1}'',", selectedCodes, selectedCode);
                            selectedNames = MessageFormat.format("{0}{1},", selectedNames, selectedName);
                        }
                    }
                    selectedCodes = selectedCodes.substring(0, selectedCodes.length() - 1);
                    selectedNames = selectedNames.substring(0, selectedNames.length() - 1);
                    selectedGroupCode = selectedCodes;
                    loader = new ProgressDialog(mContext);
                    loader.setMessage("Fetching Data.Please wait..");
                    loader.show();
                    btnGroup.setText(selectedNames);
                    if (reportDuration.equalsIgnoreCase("TODAY")) {
                        generateListData(selectedRDSCode, selectedGroupCode);
                    } else {
                        showCustomReport();
                    }
                    misOptionDialog.cancel();
                } else {
                    Utils.showToast(mContext, "Please select an option");
                }
            }
        });
        misOptionDialog.show();
    }
}
