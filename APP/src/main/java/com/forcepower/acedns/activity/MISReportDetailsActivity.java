package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.Window;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ReportDetailsSalesAdapter;
import com.forcepower.acedns.bean.ReportDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.currency;

import androidx.annotation.NonNull;

public class MISReportDetailsActivity extends AceDnsParentActivity {
    Context mContext;
    AceDnsTransactionDatabase transDataHelperObj;
    Handler mHandler;
    ArrayList<ReportDetails> reportList, dataList;
    String reportType = "";
    String customerName = "";
    String serachKey = "";

    ListView listReport;
    TextView col1Txt, col2Txt, col3Txt, txtTotal, txtTotalHeader, txtTotalQty;
    FrameLayout col1Layout, col2Layout, col3Layout;
    LinearLayout totalLayout, qtyLayout;
    ImageView btnBack;
    ProgressDialog loader;
    ReportDetailsSalesAdapter adapter;
    TextView txtCustomerName;
    Double totalAmt = 0.00;
    Double totalFromLast = 0.00;
    DecimalFormat defaultFormat = new DecimalFormat("0.000");
    DecimalFormat defaultFormat1 = new DecimalFormat("0.00");
    String selectedRDSCode = "";
    String reportDuration = "TODAY";
    String startDate = "", endDate = "";

    @SuppressLint("HandlerLeak")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report_details);
        RegisterActivities.registerActivity(this);

        mContext = MISReportDetailsActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);

        customerName = getIntent().getStringExtra("CustomerName");
        reportType = getIntent().getStringExtra("ReportType");
        serachKey = getIntent().getStringExtra("SearchKey");
        totalFromLast = getIntent().getDoubleExtra("TotalMoney", 0.00);
        selectedRDSCode = getIntent().getStringExtra("RDSCode");
        reportDuration = getIntent().getStringExtra("reportDuration");
        startDate = getIntent().getStringExtra("startDate");
        endDate = getIntent().getStringExtra("endDate");

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    totalAmt = 0.00;
                    MISReportDetailsActivity.this.runOnUiThread(() -> {
                        reportList.removeAll(reportList);
                        for (int ii = 0; ii < dataList.size(); ii++) {
                            reportList.add(dataList.get(ii));
                            totalAmt = totalAmt + Double.parseDouble(dataList.get(ii).getAmount());
                        }
                        adapter.notifyDataSetChanged();
                        txtTotal.setText(defaultFormat1.format(totalAmt));
                        txtCustomerName.setText(customerName);
                    });
                }
            }
        };

        initView();
        changeListViewBG(1);
    }

    @SuppressLint("SetTextI18n")
    public void initView() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();

        txtCustomerName = findViewById(R.id.txt_name);
        txtCustomerName.setText(customerName);
        totalLayout = findViewById(R.id.total_order_layout);
        reportList = new ArrayList<>();
        dataList = new ArrayList<>();
        listReport = findViewById(R.id.list_report);
        adapter = new ReportDetailsSalesAdapter(mContext, R.layout.report_list_child, reportList, true);
        listReport.setAdapter(adapter);
        generateListData();
        col1Txt = findViewById(R.id.txt_col1);
        col2Txt = findViewById(R.id.txt_col2);
        col3Txt = findViewById(R.id.txt_col3);
        txtTotal = findViewById(R.id.txt_total);
        txtTotalQty = findViewById(R.id.txt_qty);
        qtyLayout = findViewById(R.id.layout_qty);
        qtyLayout.setVisibility(View.VISIBLE);
        txtTotal.setTextSize(15);
        txtTotalQty.setTextSize(15);
        txtTotalQty.setText(defaultFormat.format(totalFromLast));
        txtTotalHeader = findViewById(R.id.txt_total_header);
        btnBack = findViewById(R.id.back);
        col1Layout = findViewById(R.id.col1);
        col2Layout = findViewById(R.id.col2);
        col3Layout = findViewById(R.id.col3);
        btnBack.setOnClickListener(this);
    }

    @Override
    public void onClick(View v) {
        if (v == btnBack) {
            transDataHelperObj.closeDatabase();
            finish();
        }
    }

    @SuppressLint("SimpleDateFormat")
    public void generateListData() {
        new Thread() {
            public void run() {
                dataList.removeAll(dataList);
                if (reportDuration.equalsIgnoreCase("CUSTOM")) {
                    dataList = transDataHelperObj.getCustomMISReportDetailsList(serachKey, selectedRDSCode, reportType, startDate, endDate);
                } else {
                    String timeStamp = "";
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
                        timeStamp = dateStr.substring(1);
                    } else {
                        timeStamp = Constants.dateString.substring(0, 6);
                    }
                    dataList = transDataHelperObj.getMISReportDetailsList(serachKey, selectedRDSCode, reportType, timeStamp);
                }
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    @SuppressLint("SetTextI18n")
    public void changeListViewBG(int selectedLayoutId) {
        if (selectedLayoutId == 1) {
            col1Txt.setText("Customer");
            col2Txt.setText("Qty");
            col3Txt.setText("Amount (" + currency + ")");
            col1Layout.setVisibility(View.VISIBLE);
            col2Layout.setVisibility(View.VISIBLE);
            col3Layout.setVisibility(View.VISIBLE);
            totalLayout.setVisibility(View.VISIBLE);
            if (reportType.equalsIgnoreCase("PURCHASE")) {
                txtTotalHeader.setText("Total");
            } else if (reportType.equalsIgnoreCase("SALES")) {
                txtTotalHeader.setText("Total");
            } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                txtTotalHeader.setText("Total");
            }
        }
    }
}
