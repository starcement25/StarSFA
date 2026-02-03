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
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class ReportSummerySalesActivity extends FragmentActivity implements OnClickListener {

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
    Handler mHandler;
    String reportDuration = "TODAY";
    String[] salesArray;
    TextView txtPurchase, txtSales, txtStockTransfer, txtStockHeader;
    LinearLayout layoutPurchase, layoutSales, layoutStockTransfer, layoutStock, layoutLoyalty;
    Date currentDate = new Date();
    ImageView imgPurchase, imgSales, imgStckTrnsfr;
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report_summery_sales);
        RegisterActivities.registerActivity(this);

        mContext = ReportSummerySalesActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        dateFormat1 = new SimpleDateFormat("yyyyMMdd");

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    ReportSummerySalesActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            setText();
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

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        //txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
//		txtCust = (TextView)findViewById(R.id.txt_cust_no);
//		txtOrdr = (TextView)findViewById(R.id.txt_order_no);
//		txtCollc = (TextView)findViewById(R.id.txt_collection_no);
//		txtAct = (TextView)findViewById(R.id.txt_activity_no);
//		txtNewCust = (TextView)findViewById(R.id.txt_new_cust_no);
//		txtProd = (TextView)findViewById(R.id.txt_productivity);

//		imgOrdr = (LinearLayout)findViewById(R.id.img_ordr);
//		imgCollc = (LinearLayout)findViewById(R.id.img_collc);
//		imgBzns = (LinearLayout)findViewById(R.id.img_bznz);

        imgPurchase = (ImageView) findViewById(R.id.img_purchase);
        imgSales = (ImageView) findViewById(R.id.img_sales);
        imgStckTrnsfr = (ImageView) findViewById(R.id.img_stock_transfer);

        String currentDate = new SimpleDateFormat("dd-MM-yyyy").format(Calendar.getInstance().getTime());
        txtStockHeader = (TextView) findViewById(R.id.txt_stock_header);
        txtStockHeader.setText("Stock as on " + currentDate);

        txtPurchase = (TextView) findViewById(R.id.txt_purchase);
        txtSales = (TextView) findViewById(R.id.txt_sales);
        txtStockTransfer = (TextView) findViewById(R.id.txt_stock_transfer);

        layoutPurchase = (LinearLayout) findViewById(R.id.purchase_layout);
        layoutSales = (LinearLayout) findViewById(R.id.sales_layout);
        layoutStockTransfer = (LinearLayout) findViewById(R.id.stock_transfer_layout);
        layoutStock = (LinearLayout) findViewById(R.id.stock_layout);

        layoutLoyalty = (LinearLayout) findViewById(R.id.img_loyalty);
        if (Constants.menuDetailsObj.getLoyalty().equalsIgnoreCase("yes")) {
            layoutLoyalty.setVisibility(View.VISIBLE);
        } else {
            layoutLoyalty.setVisibility(View.GONE);
        }
        layoutLoyalty.setOnClickListener(this);

//		layoutPurchase.setOnClickListener(this);
//		layoutSales.setOnClickListener(this);
//		layoutStockTransfer.setOnClickListener(this);
        layoutStock.setOnClickListener(this);

        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        showDurationLayout = (ImageView) findViewById(R.id.image_clk);
        hideDurationLayout = (ImageView) findViewById(R.id.image_go);
        showDurationLayout.setOnClickListener(this);
        hideDurationLayout.setOnClickListener(this);

        generateListData();

        durationLayout = (RelativeLayout) findViewById(R.id.duration_layout);
        customDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);

        startDatetxt = (TextView) findViewById(R.id.txt_start_date);
//		startDatetxt.setText(dateFormat.format(new Date()));
        endDateTxt = (TextView) findViewById(R.id.txt_end_date);
//		endDateTxt.setText(dateFormat.format(new Date()));

        startDatetxt.setText("");
        endDateTxt.setText("");

        btnBack = (Button) findViewById(R.id.back);
        btnStartDate = (Button) findViewById(R.id.btn_start_date);
        btnEndDate = (Button) findViewById(R.id.btn_end_date);
        btnDateDone = (Button) findViewById(R.id.btn_date_done);

        btnBack.setOnClickListener(this);
        btnStartDate.setOnClickListener(this);
        btnEndDate.setOnClickListener(this);
        btnDateDone.setOnClickListener(this);

        btnToday = (FrameLayout) findViewById(R.id.btn_today);
        btnMTD = (FrameLayout) findViewById(R.id.btn_mtd);
        btnCustom = (FrameLayout) findViewById(R.id.btn_custom);

        btnToday.setOnClickListener(this);
        btnMTD.setOnClickListener(this);
        btnCustom.setOnClickListener(this);
    }

    public void generateListData() {
        new Thread() {
            public void run() {
                if (reportDuration.equalsIgnoreCase("TODAY")) {
                    //String timeStamp = new SimpleDateFormat("yyyyMMdd").format(Calendar.getInstance().getTime());
                    String timeStamp = Constants.dateString;
                    salesArray = transDataHelperObj.getReportSummerySales(timeStamp);
                } else if (reportDuration.equalsIgnoreCase("MTD")) {
                    //String timeStamp = new SimpleDateFormat("yyyyMM").format(Calendar.getInstance().getTime());
                    String timeStamp = Constants.dateString.substring(0, 6);
                    salesArray = transDataHelperObj.getReportSummerySales(timeStamp);
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
            Intent intent = new Intent(ReportSummerySalesActivity.this, ReportSalesActivity.class);
            intent.putExtra("ShowFor", "PURCHASE");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            startActivity(intent);
        } else if (v == layoutSales) {
            Intent intent = new Intent(ReportSummerySalesActivity.this, ReportSalesActivity.class);
            intent.putExtra("ShowFor", "SALES");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            startActivity(intent);
        } else if (v == layoutStockTransfer) {
            Intent intent = new Intent(ReportSummerySalesActivity.this, ReportSalesActivity.class);
            intent.putExtra("ShowFor", "STOCK TRANSFER");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            startActivity(intent);
        } else if (v == layoutStock) {
            Intent intent = new Intent(ReportSummerySalesActivity.this, ReportSalesActivity.class);
            intent.putExtra("ShowFor", "STOCK");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            startActivity(intent);
        } else if (v == layoutLoyalty) {
            Intent intent = new Intent(ReportSummerySalesActivity.this, ReportActivityLoyalty.class);
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
                salesArray = transDataHelperObj.getCustomReportSummerySales(startDate, endDate);
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

        if (Double.parseDouble(salesArray[0]) <= 0) {
            layoutPurchase.setOnClickListener(null);
            imgPurchase.setVisibility(View.INVISIBLE);
        } else {
            imgPurchase.setVisibility(View.VISIBLE);
            layoutPurchase.setOnClickListener(ReportSummerySalesActivity.this);
        }
        if (Double.parseDouble(salesArray[1]) <= 0) {
            layoutSales.setOnClickListener(null);
            imgSales.setVisibility(View.INVISIBLE);
        } else {
            imgSales.setVisibility(View.VISIBLE);
            layoutSales.setOnClickListener(ReportSummerySalesActivity.this);
        }
        if (Double.parseDouble(salesArray[2]) <= 0) {
            layoutStockTransfer.setOnClickListener(null);
            imgStckTrnsfr.setVisibility(View.INVISIBLE);
        } else {
            imgStckTrnsfr.setVisibility(View.VISIBLE);
            layoutStockTransfer.setOnClickListener(ReportSummerySalesActivity.this);
        }
    }


}
