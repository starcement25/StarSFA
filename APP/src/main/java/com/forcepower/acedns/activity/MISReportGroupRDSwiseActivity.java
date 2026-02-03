package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;

import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ReportAdapter;
import com.forcepower.acedns.bean.ReportData;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class MISReportGroupRDSwiseActivity extends FragmentActivity implements OnClickListener {
    ImageView imgLogo, showDurationLayout, hideDurationLayout;
    Button btnBack, btnStartDate, btnEndDate, btnDateDone;
    FrameLayout btnToday, btnMTD, btnCustom, col1Layout, col2Layout, col3Layout;
    RelativeLayout durationLayout;
    LinearLayout customDateLayout;
    LinearLayout totalLayout, qtyLayout;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    TextView col1Txt, col2Txt, col3Txt, startDatetxt, endDateTxt,
            txtTotal, txtTotalHeader, txtTotalQty;
    ListView listReport;

    Context mContext;
    AceDnsTransactionDatabase transDataHelperObj;
    String reportType = "PURCHASE";
    String reportDuration = "TODAY";
    String startDate = "", endDate = "";
    Date startDate1, endDate1;
    ArrayList<ReportData> reportList, dataList;
    ReportAdapter adapter;
    ProgressDialog loader;
    Handler mHandler;
    CaldroidListener listener;
    boolean isStartDate = false;
    SimpleDateFormat dateFormat, dateFormat1;
    Double totalQty = 0.00, totalTransAmt;
    Date currentDate = new Date();
    String selectedRDSCode = "", selectedGroupCode;
    private CaldroidFragment dialogCaldroidFragment;

    @SuppressLint({"HandlerLeak", "SetTextI18n", "SimpleDateFormat"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sales_report);
        RegisterActivities.registerActivity(this);

        mContext = MISReportGroupRDSwiseActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        dateFormat1 = new SimpleDateFormat("yyyyMMdd");

        reportType = getIntent().getStringExtra("ShowFor");
        selectedRDSCode = getIntent().getStringExtra("RDSCode");
        selectedGroupCode = getIntent().getStringExtra("GroupCode");
        reportDuration = getIntent().getStringExtra("reportDuration");
        startDate = getIntent().getStringExtra("startDate");
        endDate = getIntent().getStringExtra("endDate");

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    totalQty = 0.00;
                    totalTransAmt = 0.00;
                    loader.cancel();
                    MISReportGroupRDSwiseActivity.this.runOnUiThread(() -> {
                        reportList.removeAll(reportList);
                        for (int ii = 0; ii < dataList.size(); ii++) {
                            reportList.add(dataList.get(ii));
                            if (!dataList.get(ii).getAmount().equalsIgnoreCase("")) {
                                totalQty = totalQty + Double.parseDouble(dataList.get(ii).getAmount());
                            }
                            if (!dataList.get(ii).getTransAmt().equalsIgnoreCase("")) {
                                totalTransAmt = totalTransAmt + Double.parseDouble(dataList.get(ii).getTransAmt());
                            }
                        }
                        adapter.notifyDataSetChanged();
                        DecimalFormat defaultFormat = new DecimalFormat("0.000");
                        DecimalFormat defaultFormat1 = new DecimalFormat("0.00");
                        txtTotalQty.setText(defaultFormat.format(totalQty));
                        txtTotal.setText(defaultFormat1.format(totalTransAmt));
                        if (dataList.isEmpty()) {
                            Utils.showToast(mContext, "No record found for this date.");
                        }
                    });
                }
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
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();

        imgLogo = findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        txtTotal = findViewById(R.id.txt_total);
        totalLayout = findViewById(R.id.total_order_layout);

        txtTotalHeader = findViewById(R.id.txt_total_header);

        txtTotalQty = findViewById(R.id.txt_qty);
        qtyLayout = findViewById(R.id.layout_qty);
        qtyLayout.setVisibility(View.VISIBLE);

        txtTotal.setTextSize(15);
        txtTotalQty.setTextSize(15);

        showDurationLayout = findViewById(R.id.image_clk);
        hideDurationLayout = findViewById(R.id.image_go);
        showDurationLayout.setOnClickListener(this);
        hideDurationLayout.setOnClickListener(this);

        reportList = new ArrayList<>();
        dataList = new ArrayList<>();
        listReport = findViewById(R.id.list_report);
        adapter = new ReportAdapter(mContext, R.layout.report_list_child, reportList, true);
        listReport.setAdapter(adapter);
        listReport.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            if (!reportType.equalsIgnoreCase("STOCK")) {
                if (reportList.get(arg2).getTransId().substring(0, 2).equalsIgnoreCase("NO")) {
                    Utils.showToast(mContext, "No Order was received.");
                } else {
                    Intent intent = new Intent(MISReportGroupRDSwiseActivity.this, MISReportActivity.class);
                    intent.putExtra("ShowFor", reportType);
                    intent.putExtra("reportDuration", reportDuration);
                    intent.putExtra("startDate", startDate);
                    intent.putExtra("endDate", endDate);
                    intent.putExtra("RDSCode", "'" + reportList.get(arg2).getTransId() + "'");
                    intent.putExtra("GroupCode", selectedGroupCode);
                    startActivity(intent);
                }
            }
        });
        if (reportDuration.equalsIgnoreCase("CUSTOM")) {
            showCustomReport();
        } else {
            generateListData();
        }
        durationLayout = findViewById(R.id.duration_layout);
        customDateLayout = findViewById(R.id.custom_date_layout);

        col1Txt = findViewById(R.id.txt_col1);
        col2Txt = findViewById(R.id.txt_col2);
        col3Txt = findViewById(R.id.txt_col3);

        startDatetxt = findViewById(R.id.txt_start_date);
        startDatetxt.setText("");
        endDateTxt = findViewById(R.id.txt_end_date);
        endDateTxt.setText("");

        btnBack = findViewById(R.id.back);
        btnStartDate = findViewById(R.id.btn_start_date);
        btnEndDate = findViewById(R.id.btn_end_date);
        btnDateDone = findViewById(R.id.btn_date_done);

        col1Layout = findViewById(R.id.col1);
        col2Layout = findViewById(R.id.col2);
        col3Layout = findViewById(R.id.col3);
        btnToday = findViewById(R.id.btn_today);
        ((TextView) btnToday.getChildAt(0)).setText("Yesterday");
        btnMTD = findViewById(R.id.btn_mtd);
        btnCustom = findViewById(R.id.btn_custom);

        btnBack.setOnClickListener(this);
        btnStartDate.setOnClickListener(this);
        btnEndDate.setOnClickListener(this);
        btnDateDone.setOnClickListener(this);
        btnToday.setOnClickListener(this);
        btnMTD.setOnClickListener(this);
        btnCustom.setOnClickListener(this);

        if (reportType.equalsIgnoreCase("PURCHASE")) {
            changeListViewBG(0);
        } else if (reportType.equalsIgnoreCase("SALES")) {
            changeListViewBG(0);
        } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
            changeListViewBG(0);
        } else {
            changeListViewBG(1);
        }
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
                if (endDate1.after(currentDate)) {
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
            startDate = "";
            endDate = "";
            loader.show();
            reportDuration = "TODAY";
            changeReportOptionBG(4);
            generateListData();
        } else if (v == btnMTD) {
            startDate = "";
            endDate = "";
            loader.show();
            reportDuration = "MTD";
            changeReportOptionBG(5);
            generateListData();
        } else if (v == btnCustom) {
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
        }
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

    @SuppressLint("SetTextI18n")
    public void changeListViewBG(int selectedLayoutId) {
        switch (selectedLayoutId) {
            case 0:
                col1Txt.setText("RDS Name");
                col2Txt.setText("Quantity");
                col3Txt.setText("Amount");
                col1Layout.setVisibility(View.VISIBLE);
                col2Layout.setVisibility(View.VISIBLE);
                col3Layout.setVisibility(View.VISIBLE);
                totalLayout.setVisibility(View.VISIBLE);
                txtTotalHeader.setText("Total");
                break;
            case 1:
                col1Txt.setText("Product Name");
                col2Txt.setText("NA");
                col3Txt.setText("Closing Stk");
                col1Layout.setVisibility(View.VISIBLE);
                col2Layout.setVisibility(View.INVISIBLE);
                col3Layout.setVisibility(View.VISIBLE);
                totalLayout.setVisibility(View.GONE);
                break;
        }
    }

    @SuppressLint("SimpleDateFormat")
    public void generateListData() {
        new Thread() {
            public void run() {
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
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    if (reportDuration.equalsIgnoreCase("TODAY")) {
                        dataList = transDataHelperObj.getMISReportListGroupRDSWise("PURCHASE", dateStr, selectedRDSCode, selectedGroupCode);
                    } else if (reportDuration.equalsIgnoreCase("MTD")) {
                        String timeStamp = Constants.dateString.substring(0, 6);
                        dataList = transDataHelperObj.getMISReportListGroupRDSWise("PURCHASE", timeStamp, selectedRDSCode, selectedGroupCode);
                    }
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    if (reportDuration.equalsIgnoreCase("TODAY")) {
                        dataList = transDataHelperObj.getMISReportListGroupRDSWise("SALES", dateStr, selectedRDSCode, selectedGroupCode);
                    } else if (reportDuration.equalsIgnoreCase("MTD")) {
                        String timeStamp = Constants.dateString.substring(0, 6);
                        dataList = transDataHelperObj.getMISReportListGroupRDSWise("SALES", timeStamp, selectedRDSCode, selectedGroupCode);
                    }
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    if (reportDuration.equalsIgnoreCase("TODAY")) {
                        dataList = transDataHelperObj.getMISReportListGroupRDSWise("STOCK TRANSFER", dateStr, selectedRDSCode, selectedGroupCode);
                    } else if (reportDuration.equalsIgnoreCase("MTD")) {
                        String timeStamp = Constants.dateString.substring(0, 6);
                        dataList = transDataHelperObj.getMISReportListGroupRDSWise("STOCK TRANSFER", timeStamp, selectedRDSCode, selectedGroupCode);
                    }
                } else {
                    dataList = transDataHelperObj.getMISReportListGroupRDSWise("STOCK", "", selectedRDSCode, selectedGroupCode);
                }
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
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
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    dataList = transDataHelperObj.getCustomMISReportListGroupRDSWise("PURCHASE", startDate, endDate, selectedRDSCode, selectedGroupCode);
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    dataList = transDataHelperObj.getCustomMISReportListGroupRDSWise("SALES", startDate, endDate, selectedRDSCode, selectedGroupCode);
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    dataList = transDataHelperObj.getCustomMISReportListGroupRDSWise("STOCK TRANSFER", startDate, endDate, selectedRDSCode, selectedGroupCode);
                } else {
                    dataList = transDataHelperObj.getCustomMISReportListGroupRDSWise("STOCK", startDate, endDate, selectedRDSCode, selectedGroupCode);
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



