package com.forcepower.acedns.activity;

import android.app.Dialog;
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
import com.forcepower.acedns.bean.ReportSummery;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Date;

public class ReportSummeryActivity extends FragmentActivity implements OnClickListener {

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
    ReportSummery reportObj;
    TextView txtCust, txtOrdr, txtCollc, txtAct, txtNewCust, txtProd;
    LinearLayout layoutOrdr, layoutCollc, layoutBzns, layoutLoyalty;
    Date currentDate = new Date();
    ImageView imgOrder1, imgCollcs1, imgProspect1;
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report_summery);
        RegisterActivities.registerActivity(this);

        mContext = ReportSummeryActivity.this;
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
                    ReportSummeryActivity.this.runOnUiThread(new Runnable() {
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
        imgOrder1 = (ImageView) findViewById(R.id.img_ordr1);
        imgCollcs1 = (ImageView) findViewById(R.id.img_collc1);
        imgProspect1 = (ImageView) findViewById(R.id.img_pros);

        txtCust = (TextView) findViewById(R.id.txt_cust_no);
        txtOrdr = (TextView) findViewById(R.id.txt_order_no);
        txtCollc = (TextView) findViewById(R.id.txt_collection_no);
        txtAct = (TextView) findViewById(R.id.txt_activity_no);
        txtNewCust = (TextView) findViewById(R.id.txt_new_cust_no);
        txtProd = (TextView) findViewById(R.id.txt_productivity);

        layoutOrdr = (LinearLayout) findViewById(R.id.img_ordr);
        layoutCollc = (LinearLayout) findViewById(R.id.img_collc);
        layoutBzns = (LinearLayout) findViewById(R.id.img_bznz);
        layoutLoyalty = (LinearLayout) findViewById(R.id.img_loyalty);
        if (Constants.menuDetailsObj.getLoyalty().equalsIgnoreCase("yes")) {
            layoutLoyalty.setVisibility(View.VISIBLE);
        } else {
            layoutLoyalty.setVisibility(View.GONE);
        }
        layoutLoyalty.setOnClickListener(this);

//		imgOrdr.setOnClickListener(this);
//		imgCollc.setOnClickListener(this);
//		imgBzns.setOnClickListener(this);

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
                    reportObj = transDataHelperObj.getReportSummery(timeStamp);
                } else if (reportDuration.equalsIgnoreCase("MTD")) {
                    //String timeStamp = new SimpleDateFormat("yyyyMM").format(Calendar.getInstance().getTime());
                    String timeStamp = Constants.dateString.substring(0, 6);
                    reportObj = transDataHelperObj.getReportSummery(timeStamp);
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
        } else if (v == layoutOrdr) {
            Intent intent = new Intent(ReportSummeryActivity.this, ReportActivityGrpWise.class);
            intent.putExtra("ShowFor", "ORDER");
            intent.putExtra("reportDuration", reportDuration);
            intent.putExtra("startDate", startDate);
            intent.putExtra("endDate", endDate);
            startActivity(intent);
        } else if (v == layoutCollc) {
//			Intent intent = new Intent(ReportSummeryActivity.this,ReportActivity.class);
//			intent.putExtra("ShowFor", "COLLECTION");
//			intent.putExtra("reportDuration",reportDuration);
//			startActivity(intent);
            showCollectionDetailsDialog();
        } else if (v == layoutBzns) {
            Intent intent = new Intent(ReportSummeryActivity.this, ReportActivityGrpWise.class);
            intent.putExtra("ShowFor", "BZNS PROSPECT");
            intent.putExtra("reportDuration", reportDuration);
            startActivity(intent);
        } else if (v == layoutLoyalty) {
            Intent intent = new Intent(ReportSummeryActivity.this, ReportActivityLoyalty.class);
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
                reportObj = transDataHelperObj.getCustomReportSummery(startDate, endDate);
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
        txtCust.setText(reportObj.getNoCustVisitd());
        txtOrdr.setText(reportObj.getNoOrdrRcvd());
        txtCollc.setText(reportObj.getNoCollcRcvd());
        txtAct.setText(reportObj.getNoNoAct());
        txtNewCust.setText(reportObj.getNoNewCustVisitd());
        txtProd.setText(reportObj.getProdctvty());

        if (Double.parseDouble(reportObj.getNoOrdrRcvd()) <= 0) {
            layoutOrdr.setOnClickListener(null);
            imgOrder1.setVisibility(View.INVISIBLE);
        } else {
            imgOrder1.setVisibility(View.VISIBLE);
            layoutOrdr.setOnClickListener(ReportSummeryActivity.this);
        }
        if (Double.parseDouble(reportObj.getNoCollcRcvd()) <= 0) {
            layoutCollc.setOnClickListener(null);
            imgCollcs1.setVisibility(View.INVISIBLE);
        } else {
            imgCollcs1.setVisibility(View.VISIBLE);
            layoutCollc.setOnClickListener(ReportSummeryActivity.this);
        }
        if (Double.parseDouble(reportObj.getNoNewCustVisitd()) <= 0) {
            layoutBzns.setOnClickListener(null);
            imgProspect1.setVisibility(View.INVISIBLE);
        } else {
            imgProspect1.setVisibility(View.VISIBLE);
            layoutBzns.setOnClickListener(ReportSummeryActivity.this);
        }
    }

    public void showCollectionDetailsDialog() {
        final Dialog collcDetailsDialog = new Dialog(ReportSummeryActivity.this, R.style.PauseDialog);
        collcDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        collcDetailsDialog.setContentView(R.layout.collection_details_dialog);
        collcDetailsDialog.setCancelable(false);
        TextView title = (TextView) collcDetailsDialog.findViewById(R.id.title);
        TextView txtCash = (TextView) collcDetailsDialog.findViewById(R.id.cash);
        TextView txtCurrentCheque = (TextView) collcDetailsDialog.findViewById(R.id.current_chq);
        TextView txtPDC = (TextView) collcDetailsDialog.findViewById(R.id.pdc_chq);
        String[] collectionDetailsArray = new String[3];
        if (reportDuration.equalsIgnoreCase("TODAY")) {
            title.setText("Collection Summary for Today");
            collectionDetailsArray = transDataHelperObj.getCollectionDetails("TODAY");
        } else {
            title.setText("Collection Summary this Month");
            collectionDetailsArray = transDataHelperObj.getCollectionDetails("MTD");
        }
        txtCash.setText("Rs " + new DecimalFormat("0.00").format(Double.parseDouble(collectionDetailsArray[0])));
        txtCurrentCheque.setText("Rs " + new DecimalFormat("0.00").format(Double.parseDouble(collectionDetailsArray[1])));
        txtPDC.setText("Rs " + new DecimalFormat("0.00").format(Double.parseDouble(collectionDetailsArray[2])));
        Button btnNext = (Button) collcDetailsDialog.findViewById(R.id.btn_nxt);
        btnNext.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                collcDetailsDialog.cancel();
                Intent intent = new Intent(ReportSummeryActivity.this, ReportActivityGrpWise.class);
                intent.putExtra("ShowFor", "COLLECTION");
                intent.putExtra("reportDuration", reportDuration);
                startActivity(intent);
            }
        });
        collcDetailsDialog.show();
    }


}
