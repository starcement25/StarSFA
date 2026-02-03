package com.forcepower.acedns.activity;

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
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;

import java.text.DecimalFormat;
import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.currency;

public class ReportDetailsSaleActivity extends AceDnsParentActivity {

    Context mContext;
    AceDnsTransactionDatabase transDataHelperObj;
    Handler mHandler;
    ArrayList<ReportDetails> reportList, dataList;
    String reportType = "", customerName = "", serachKey = "", billDate = "", billNo = "", billValue = "";

    ListView listReport;
    TextView col1Txt, col2Txt, col3Txt, txtTotal, txtTotalHeader;
    FrameLayout col1Layout, col2Layout, col3Layout;
    LinearLayout totalLayout;
    ImageView btnBack;
    ProgressDialog loader;
    ReportDetailsSalesAdapter adapter;
    TextView txtCustomerName;
    Double totalAmt = 0.00;
    Double totalFromLast = 0.00;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report_details);
        RegisterActivities.registerActivity(this);

        mContext = ReportDetailsSaleActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);

        customerName = getIntent().getStringExtra("CustomerName");
        reportType = getIntent().getStringExtra("ReportType");
        serachKey = getIntent().getStringExtra("SearchKey");
        totalFromLast = getIntent().getDoubleExtra("TotalMoney", 0.00);

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    totalAmt = 0.00;
                    ReportDetailsSaleActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            reportList.removeAll(reportList);
                            for (int ii = 0; ii < dataList.size(); ii++) {
                                reportList.add(dataList.get(ii));
                                if (!dataList.get(ii).getAmount().equalsIgnoreCase("")) {
                                    totalAmt = totalAmt + Double.parseDouble(dataList.get(ii).getAmount());
                                }
//	            	        		totalAmt = Double.parseDouble(dataList.get(ii).getAmount());
                            }
                            adapter.notifyDataSetChanged();
                            txtTotal.setText("" + defaultFormat.format(totalAmt));
                            txtCustomerName.setText(customerName + "\n" + billNo + "\n" + billDate + "\n" + billValue);
                        }
                    });
                }
            }
        };

        initView();
        changeListViewBG(1);
//			if(reportType.equalsIgnoreCase("ORDER")){
//				changeListViewBG(1);
//			}else if(reportType.equalsIgnoreCase("COLLECTION")){
//				changeListViewBG(2);
//			}else{
//				changeListViewBG(3);
//			}
    }

    public void initView() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();

        txtCustomerName = (TextView) findViewById(R.id.txt_name);
        txtCustomerName.setText(customerName);

        totalLayout = (LinearLayout) findViewById(R.id.total_order_layout);

        reportList = new ArrayList<ReportDetails>();
        dataList = new ArrayList<ReportDetails>();
        listReport = (ListView) findViewById(R.id.list_report);
        adapter = new ReportDetailsSalesAdapter(mContext, R.layout.report_list_child, reportList);
        listReport.setAdapter(adapter);
        generateListData();

        col1Txt = (TextView) findViewById(R.id.txt_col1);
        col2Txt = (TextView) findViewById(R.id.txt_col2);
        col3Txt = (TextView) findViewById(R.id.txt_col3);
        txtTotal = (TextView) findViewById(R.id.txt_total);
        txtTotalHeader = (TextView) findViewById(R.id.txt_total_header);

        btnBack = (ImageView) findViewById(R.id.back);

        col1Layout = (FrameLayout) findViewById(R.id.col1);
        col2Layout = (FrameLayout) findViewById(R.id.col2);
        col3Layout = (FrameLayout) findViewById(R.id.col3);

        btnBack.setOnClickListener(this);
    }

    @Override
    public void onClick(View v) {
        if (v == btnBack) {
            transDataHelperObj.closeDatabase();
            finish();
        }
    }

    public void generateListData() {
        new Thread() {
            public void run() {
                dataList.removeAll(dataList);
                dataList = transDataHelperObj.getSaleReportDetailsList(serachKey);
                String remarks = transDataHelperObj.getSaleRemarks(serachKey);
                String[] remarkArray = remarks.split(";");
                if (remarkArray != null && remarkArray.length > 0) {
                    billNo = remarkArray[0];
                }
                if (remarkArray != null && remarkArray.length > 1) {
                    billDate = remarkArray[1];
                }
                if (remarkArray != null && remarkArray.length > 2) {
                    billValue = remarkArray[2];
                }
//				if(reportType.equalsIgnoreCase("ORDER")){
//						dataList = transDataHelperObj.getReportDetailsList("O",serachKey);
//				}else if(reportType.equalsIgnoreCase("COLLECTION")){
//						dataList = transDataHelperObj.getReportDetailsList("P",serachKey);
//						System.out.println("Size of dataList" + dataList.size());
//				}else{
//						dataList = transDataHelperObj.getReportDetailsList("DC",serachKey);
//				}

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void changeListViewBG(int selectedLayoutId) {
        switch (selectedLayoutId) {
            case 1:
                col1Txt.setText("Item");
                col2Txt.setText("Qty");
                col3Txt.setText("Amount (" + currency + ")");
                col1Layout.setVisibility(View.VISIBLE);
                col2Layout.setVisibility(View.VISIBLE);
                col3Layout.setVisibility(View.VISIBLE);

                totalLayout.setVisibility(View.VISIBLE);

                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    txtTotalHeader.setText("Total : ");
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    txtTotalHeader.setText("Total : ");
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    txtTotalHeader.setText("Total : ");
                }
                break;
//		case 2:
//			 col1Txt.setText("INV No");
//			 col2Txt.setText("INV Amt");
//			 col3Txt.setText("Rcvd Amt");
//			 col1Layout.setVisibility(col1Layout.VISIBLE);
//			 col2Layout.setVisibility(col2Layout.VISIBLE);
//			 col3Layout.setVisibility(col3Layout.VISIBLE);
//			 
//			 totalLayout.setVisibility(totalLayout.VISIBLE);
//			 txtTotalHeader.setText("Total Collection Amt : ");
//			break;
//		case 3:
//			 col1Txt.setText("Item");
//			 col2Txt.setText("NA");
//			 col3Txt.setText("NA");
//			 col1Layout.setVisibility(col1Layout.VISIBLE);
//			 col2Layout.setVisibility(col2Layout.GONE);
//			 col3Layout.setVisibility(col3Layout.GONE);
//			 
//			 totalLayout.setVisibility(totalLayout.GONE);
//			break;
        }
    }


    public double calculateAmt(ReportDetails currentObj) {
        Double total = 0.00;
        double discount = 0.00;
        double qty = Double.parseDouble(currentObj.getQty());
        double mrp = Double.parseDouble(currentObj.getAmount());
        if (currentObj.getTD() != null) {
            discount = Double.parseDouble(currentObj.getTD());
        }
        total = total + ((mrp * qty) - (mrp * qty * discount / 100));
        return total;
    }

}
