package com.forcepower.acedns.activity;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
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
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.currency;

public class ReportSalesActivity extends FragmentActivity implements OnClickListener {

    ImageView imgLogo, showDurationLayout, hideDurationLayout;
    Button btnBack, btnStartDate, btnEndDate, btnDateDone;
    //	FrameLayout btnCheckOut,btnCollection,btnProspect;
    FrameLayout btnToday, btnMTD, btnCustom, col1Layout, col2Layout, col3Layout;
    RelativeLayout durationLayout;
    LinearLayout customDateLayout;
    LinearLayout totalLayout;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    TextView col1Txt, col2Txt, col3Txt, startDatetxt, endDateTxt, txtTotal, txtTotalHeader;
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
    Double totalAmt = 0.00;
    Date currentDate = new Date();
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sales_report);
        RegisterActivities.registerActivity(this);

        mContext = ReportSalesActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        dateFormat1 = new SimpleDateFormat("yyyyMMdd");

        reportType = getIntent().getStringExtra("ShowFor");
//		if(showFor.equalsIgnoreCase("ORDER")){
//			reportType = "ORDER";
//		}else if(showFor.equalsIgnoreCase("COLLECTION")){
//			reportType = "COLLECTION";
//		}else{
//			reportType = "PROSPECT";
//		}
        reportDuration = getIntent().getStringExtra("reportDuration");
        startDate = getIntent().getStringExtra("startDate");
        endDate = getIntent().getStringExtra("endDate");

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    totalAmt = 0.00;
                    loader.cancel();
                    ReportSalesActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            reportList.removeAll(reportList);
                            for (int ii = 0; ii < dataList.size(); ii++) {
                                reportList.add(dataList.get(ii));
                                if (!dataList.get(ii).getAmount().equalsIgnoreCase("")) {
                                    totalAmt = totalAmt + Double.parseDouble(dataList.get(ii).getAmount());
                                }
                            }
                            adapter.notifyDataSetChanged();
                            DecimalFormat defaultFormat = new DecimalFormat("0.00");
                            txtTotal.setText("" + defaultFormat.format(totalAmt));
                            if (dataList.size() == 0) {
                                Utils.showToast(mContext, "No record found for this date.");
                            }
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

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {

            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };

        initView();

    }

    public void initView() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();

        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        //txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        txtTotal = (TextView) findViewById(R.id.txt_total);
        totalLayout = (LinearLayout) findViewById(R.id.total_order_layout);

        txtTotalHeader = (TextView) findViewById(R.id.txt_total_header);

        showDurationLayout = (ImageView) findViewById(R.id.image_clk);
        if (reportType.equalsIgnoreCase("STOCK")) {
            showDurationLayout.setVisibility(View.GONE);
        }
        hideDurationLayout = (ImageView) findViewById(R.id.image_go);
        showDurationLayout.setOnClickListener(this);
        hideDurationLayout.setOnClickListener(this);

        reportList = new ArrayList<ReportData>();
        dataList = new ArrayList<ReportData>();
        listReport = (ListView) findViewById(R.id.list_report);
        if (reportType.equalsIgnoreCase("STOCK")) {
            adapter = new ReportAdapter(mContext, R.layout.report_list_child, reportList, true);
        } else {
            adapter = new ReportAdapter(mContext, R.layout.report_list_child, reportList, "SALES");
        }
        listReport.setAdapter(adapter);
        listReport.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                if (!reportType.equalsIgnoreCase("STOCK")) {
                    if (reportList.get(arg2).getTransId().substring(0, 2).equalsIgnoreCase("NO")) {
                        Utils.showToast(mContext, "No Order was received.");
                    } else {
                        ReportData selectedObj = reportList.get(arg2);
                        Intent intent = new Intent(ReportSalesActivity.this, ReportDetailsSaleActivity.class);
                        intent.putExtra("CustomerName", selectedObj.getCustomerName());
                        intent.putExtra("ReportType", reportType);
                        intent.putExtra("SearchKey", selectedObj.getTransId());
                        intent.putExtra("TotalMoney", Double.parseDouble(selectedObj.getAmount()));
                        startActivity(intent);
                    }
                } else {
                    // Nothing to do. We already showed the stock....
                }
            }
        });
        if (reportDuration.equalsIgnoreCase("CUSTOM")) {
            showCustomReport();
        } else {
            generateListData();
        }
        durationLayout = (RelativeLayout) findViewById(R.id.duration_layout);
        customDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);

        col1Txt = (TextView) findViewById(R.id.txt_col1);
        col2Txt = (TextView) findViewById(R.id.txt_col2);
        col3Txt = (TextView) findViewById(R.id.txt_col3);

        startDatetxt = (TextView) findViewById(R.id.txt_start_date);
//		 startDatetxt.setText(dateFormat.format(new Date()));
        startDatetxt.setText("");
        endDateTxt = (TextView) findViewById(R.id.txt_end_date);
//		 endDateTxt.setText(dateFormat.format(new Date()));
        startDatetxt.setText("");
        endDateTxt.setText("");

        btnBack = (Button) findViewById(R.id.back);
        btnStartDate = (Button) findViewById(R.id.btn_start_date);
        btnEndDate = (Button) findViewById(R.id.btn_end_date);
        btnDateDone = (Button) findViewById(R.id.btn_date_done);

        col1Layout = (FrameLayout) findViewById(R.id.col1);
        col2Layout = (FrameLayout) findViewById(R.id.col2);
        col3Layout = (FrameLayout) findViewById(R.id.col3);
//		 btnCheckOut = (FrameLayout)findViewById(R.id.btn_order);
//		 btnCollection = (FrameLayout)findViewById(R.id.btn_collection);
//		 btnProspect = (FrameLayout)findViewById(R.id.btn_prospect);
        btnToday = (FrameLayout) findViewById(R.id.btn_today);
        btnMTD = (FrameLayout) findViewById(R.id.btn_mtd);
        btnCustom = (FrameLayout) findViewById(R.id.btn_custom);

        btnBack.setOnClickListener(this);
        btnStartDate.setOnClickListener(this);
        btnEndDate.setOnClickListener(this);
        btnDateDone.setOnClickListener(this);

//		 btnCheckOut.setOnClickListener(this);
//		 btnCollection.setOnClickListener(this);
//		 btnProspect.setOnClickListener(this);
        btnToday.setOnClickListener(this);
        btnMTD.setOnClickListener(this);
        btnCustom.setOnClickListener(this);

//		 if(!reportType.equalsIgnoreCase("STOCK")){
//				changeListViewBG(1);
//		 }else{
//				changeListViewBG(2);
//		 }

        if (reportType.equalsIgnoreCase("PURCHASE")) {
            changeListViewBG(3);
        } else if (reportType.equalsIgnoreCase("SALES")) {
            changeListViewBG(1);
        } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
            changeListViewBG(4);
        } else {// FOR STOCK
            changeListViewBG(2);
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
        }

//		else if(v == btnCheckOut){
//			loader.show();
//			reportType = "ORDER";
//			changeReportOptionBG(1);
//			changeListViewBG(1);
//			generateListData();
//		}else if(v == btnCollection){
//			loader.show();
//			reportType = "COLLECTION";
//			changeReportOptionBG(2);
//			changeListViewBG(2);
//			generateListData();
//		}else if(v == btnProspect){
//			loader.show();
//			reportType = "PROSPECT";
//			changeReportOptionBG(3);
//			changeListViewBG(3);
//			generateListData();
//		}

        else if (v == btnToday) {
            showDurationLayout.startAnimation(fadeIn);
            showDurationLayout.setVisibility(View.VISIBLE);
            durationLayout.startAnimation(bottomDown);
            durationLayout.setVisibility(View.GONE);
            startDate = "";
            endDate = "";
            loader.show();
            reportDuration = "TODAY";
            changeReportOptionBG(4);
            generateListData();
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
            case 1:
//			btnCheckOut.setBackgroundColor(Color.parseColor("#B6B6B4"));
//			btnCollection.setBackgroundColor(Color.parseColor("#E5E4E2"));
//			btnProspect.setBackgroundColor(Color.parseColor("#E5E4E2"));
                break;
            case 2:
//			btnCheckOut.setBackgroundColor(Color.parseColor("#E5E4E2"));
//			btnCollection.setBackgroundColor(Color.parseColor("#B6B6B4"));
//			btnProspect.setBackgroundColor(Color.parseColor("#E5E4E2"));
                break;
            case 3:
//			btnCheckOut.setBackgroundColor(Color.parseColor("#E5E4E2"));
//			btnCollection.setBackgroundColor(Color.parseColor("#E5E4E2"));
//			btnProspect.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
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


    public void changeListViewBG(int selectedLayoutId) {
        switch (selectedLayoutId) {
            case 1:
                col1Txt.setText("Date");
                col2Txt.setText("Customer Name");
                col3Txt.setText("Amount (" + currency + ")");
                col1Layout.setVisibility(View.VISIBLE);
                col2Layout.setVisibility(View.VISIBLE);
                col3Layout.setVisibility(View.VISIBLE);

                totalLayout.setVisibility(View.VISIBLE);
                txtTotalHeader.setText("Total : ");
                break;
            case 2:
                col1Txt.setText("Product Name");
                col2Txt.setText("");
                col3Txt.setText("Closing Stk");
                col1Layout.setVisibility(View.VISIBLE);
                col2Layout.setVisibility(View.INVISIBLE);
                col3Layout.setVisibility(View.VISIBLE);

                totalLayout.setVisibility(View.GONE);

                break;
            case 3:
                col1Txt.setText("Date");
                col2Txt.setText("Vendor Name");
                col3Txt.setText("Amount (" + currency + ")");
                col1Layout.setVisibility(View.VISIBLE);
                col2Layout.setVisibility(View.VISIBLE);
                col3Layout.setVisibility(View.VISIBLE);

                totalLayout.setVisibility(View.VISIBLE);
                txtTotalHeader.setText("Total : ");
                break;

            case 4:
                col1Txt.setText("Date");
                col2Txt.setText("RDS Name");
                col3Txt.setText("Amount (" + currency + ")");
                col1Layout.setVisibility(View.VISIBLE);
                col2Layout.setVisibility(View.VISIBLE);
                col3Layout.setVisibility(View.VISIBLE);

                totalLayout.setVisibility(View.VISIBLE);
                txtTotalHeader.setText("Total : ");
                break;
        }
    }


    public void generateListData() {
        new Thread() {
            public void run() {
                if (reportType.equalsIgnoreCase("PURCHASE")) {
                    if (reportDuration.equalsIgnoreCase("TODAY")) {
                        //String timeStamp = new SimpleDateFormat("yyyyMMdd").format(Calendar.getInstance().getTime());
                        String timeStamp = Constants.dateString;
                        dataList = transDataHelperObj.getSalesReportList("PURCHASE", timeStamp);
                    } else if (reportDuration.equalsIgnoreCase("MTD")) {
                        //String timeStamp = new SimpleDateFormat("yyyyMM").format(Calendar.getInstance().getTime());
                        String timeStamp = Constants.dateString.substring(0, 6);
                        dataList = transDataHelperObj.getSalesReportList("PURCHASE", timeStamp);
                    }
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    if (reportDuration.equalsIgnoreCase("TODAY")) {
                        //String timeStamp = new SimpleDateFormat("yyyyMMdd").format(Calendar.getInstance().getTime());
                        String timeStamp = Constants.dateString;
                        dataList = transDataHelperObj.getSalesReportList("SALES", timeStamp);
                    } else if (reportDuration.equalsIgnoreCase("MTD")) {
                        //String timeStamp = new SimpleDateFormat("yyyyMM").format(Calendar.getInstance().getTime());
                        String timeStamp = Constants.dateString.substring(0, 6);
                        dataList = transDataHelperObj.getSalesReportList("SALES", timeStamp);
                    }
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    if (reportDuration.equalsIgnoreCase("TODAY")) {
                        //String timeStamp = new SimpleDateFormat("yyyyMMdd").format(Calendar.getInstance().getTime());
                        String timeStamp = Constants.dateString;
                        dataList = transDataHelperObj.getSalesReportList("STOCK TRANSFER", timeStamp);
                    } else if (reportDuration.equalsIgnoreCase("MTD")) {
                        //String timeStamp = new SimpleDateFormat("yyyyMM").format(Calendar.getInstance().getTime());
                        String timeStamp = Constants.dateString.substring(0, 6);
                        dataList = transDataHelperObj.getSalesReportList("STOCK TRANSFER", timeStamp);
                    }
                } else {
                    dataList = transDataHelperObj.getSalesReportList("STOCK", "");
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
                    dataList = transDataHelperObj.getCustomSalesReportList("PURCHASE", startDate, endDate);
                } else if (reportType.equalsIgnoreCase("SALES")) {
                    dataList = transDataHelperObj.getCustomSalesReportList("SALES", startDate, endDate);
                } else if (reportType.equalsIgnoreCase("STOCK TRANSFER")) {
                    dataList = transDataHelperObj.getCustomSalesReportList("STOCK TRANSFER", startDate, endDate);
                } else {
                    dataList = transDataHelperObj.getCustomSalesReportList("STOCK", startDate, endDate);
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



