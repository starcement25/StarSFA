package com.forcepower.acedns.activity;

import androidx.fragment.app.FragmentActivity;

import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
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

import com.forcepower.acedns.adapter.TourExpReportAdapter;
import com.forcepower.acedns.bean.TourExReport;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;

public class TourExpReportActivity extends FragmentActivity implements View.OnClickListener {

    Dialog routePlanListDialog, routeDialog;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    Button buttonRoute;

    ArrayList<TourExReport> expList;
    TourExpReportAdapter adapterExp;
    TextView textViewAmountTotal;

    public static ImageView mImageViewShowDuration = null;
    public static ImageView mImageViewHideDuration = null;

    Animation bottomUp, bottomDown, fadeIn, fadeOut;

    public static RelativeLayout mRelativeLayoutDuration = null;
    public static LinearLayout mCustomDateLayout = null;

    private int SELECTION = 0;

    public static FrameLayout mFrameLayoutToday = null;
    public static FrameLayout mFrameLayoutMTD = null;
    public static FrameLayout mFrameLayoutCustom = null;

    //public static Button mButtonBack = null;
    public static Button mButtonStartDate = null;
    public static Button mButtonEndDate = null;
    public static Button mButtonSubmitDate = null;

    public static TextView mTextViewStartDate = null;
    public static TextView mTextViewEndDate = null;

    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;

    private boolean isStartDate = false;

    Date startDate, endDate;
    Date currentDate = new Date();

    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    private String mStartDate = "";
    private String mEndDate = "";
    private String mToday = "";

    private String routeCode = "";


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_tour_exp_report);

        textViewAmountTotal = findViewById(R.id.textViewAmountTotal);

        mContext = TourExpReportActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);


        mImageViewShowDuration = (ImageView) findViewById(R.id.image_clk);
        mImageViewHideDuration = (ImageView) findViewById(R.id.image_go);

        mFrameLayoutToday = (FrameLayout) findViewById(R.id.btn_today);
        mFrameLayoutMTD = (FrameLayout) findViewById(R.id.btn_mtd);
        mFrameLayoutCustom = (FrameLayout) findViewById(R.id.btn_custom);


        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mRelativeLayoutDuration = (RelativeLayout) findViewById(R.id.duration_layout);
        mCustomDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);

        mTextViewStartDate = (TextView) findViewById(R.id.txt_start_date);
        mTextViewEndDate = (TextView) findViewById(R.id.txt_end_date);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");

        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");


        mButtonStartDate = (Button) findViewById(R.id.btn_start_date);
        mButtonEndDate = (Button) findViewById(R.id.btn_end_date);
        mButtonSubmitDate = (Button) findViewById(R.id.btn_date_done);

        mDFormatFrontEnd = new SimpleDateFormat("yyyy-MM-dd");
        mDFormatBackEnd = new SimpleDateFormat("yyyy-MM-dd");

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutToday.setOnClickListener(this);
        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);


        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate = date;
                    mTextViewStartDate.setText(mDFormatFrontEnd.format(date));
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    mEndDate = mDFormatBackEnd.format(date);
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
        ChangeBackground(SELECTION);
        if(SELECTION==3){
            showD("r",mStartDate,mEndDate);
            //mStartDate =
            mTextViewStartDate.setText(mStartDate);
            mTextViewEndDate.setText(mStartDate);
        }else if(SELECTION==2){
            String date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
            show("mtd",date,date);
        }else{
        showToday();
        }
        //
    }

    public void finishCurrentActivity(View v) {
        finish();
    }

    private void showToday(){
        String date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());

        final ArrayList<TourExReport> expList1 = mAceDnsDatabase.getTourExp("t",date,date);

        ListView dialogList = (ListView) findViewById(R.id.listView);
        TourExpReportAdapter adapter1 = new TourExpReportAdapter(TourExpReportActivity.this, R.layout.tour_exp_list_child_report, expList1);
        dialogList.setAdapter(adapter1);

        double sum = 0;
        for(int i = 0; i < expList1.size(); i++) {
            sum += Double.parseDouble(expList1.get(i).getTotal().toString());
        }

        textViewAmountTotal.setText(""+sum);

    }



    @Override
    public void onClick(View v) {
        if (v == mImageViewHideDuration) {
            mImageViewShowDuration.startAnimation(fadeIn);
            mImageViewShowDuration.setVisibility(View.VISIBLE);
            mRelativeLayoutDuration.startAnimation(bottomDown);
            mRelativeLayoutDuration.setVisibility(View.GONE);
        } else if (v == mImageViewShowDuration) {
            mImageViewShowDuration.startAnimation(fadeOut);
            mImageViewShowDuration.setVisibility(View.GONE);
            mRelativeLayoutDuration.startAnimation(bottomUp);
            mRelativeLayoutDuration.setVisibility(View.VISIBLE);
        } else if (v == mFrameLayoutToday) {
            SELECTION = 1;
            ChangeBackground(SELECTION);
            showToday();
            //mQuery = BuildQuery(SELECTION);
            //FetchSaudaTransactionLogData(1);

        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            String date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
            show("mtd",date,date);
            //mQuery = BuildQuery(SELECTION);
            //FetchSaudaTransactionLogData(1);

        } else if (v == mFrameLayoutCustom) {
            SELECTION = 3;
            ChangeBackground(SELECTION);
        } else if (v == mButtonStartDate) {
            isStartDate = true;
            ChooseDateDialog();
        } else if (v == mButtonEndDate) {
            isStartDate = false;
            ChooseDateDialog();
        } else if (v == mButtonSubmitDate) {
            try {
                if (endDate.after(currentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else if (startDate.after(endDate)) {
                    Utils.showToast(mContext, "Start Date should be less than or equal to End Date");
                } else {
                    mCustomDateLayout.startAnimation(bottomUp);
                    mCustomDateLayout.setVisibility(View.GONE);
                    mImageViewShowDuration.startAnimation(fadeOut);
                    mImageViewShowDuration.setVisibility(View.GONE);
                    mRelativeLayoutDuration.startAnimation(bottomUp);
                    mRelativeLayoutDuration.setVisibility(View.VISIBLE);
                    SELECTION = 3;
                    show("r",mTextViewStartDate.getText().toString(),mTextViewEndDate.getText().toString());
                    //showCustomerWithDate(""+routeCode,mTextViewStartDate.getText().toString(),mTextViewEndDate.getText().toString());
                    //mQuery = BuildQuery(SELECTION);

                    //Utils.showToast(mContext, "Please date set." + mTextViewStartDate.getText().toString());
                    mTextViewStartDate.setText("");
                    mTextViewEndDate.setText("");
                    //FetchSaudaTransactionLogData(1);
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Please choose the Dates again.");
            }
        }
    }


    public void ChangeBackground(int select) {
        mFrameLayoutToday.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mFrameLayoutMTD.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mFrameLayoutCustom.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mCustomDateLayout.startAnimation(bottomUp);
        mCustomDateLayout.setVisibility(View.GONE);

        switch (select) {
            case 1:
                mFrameLayoutToday.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
            case 2:
                mFrameLayoutMTD.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
            case 3:
                mFrameLayoutCustom.setBackgroundColor(Color.parseColor("#B6B6B4"));
                mCustomDateLayout.startAnimation(bottomUp);
                mCustomDateLayout.setVisibility(View.VISIBLE);
                break;
        }
    }

    public void ChooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        Bundle bundle = new Bundle();
        bundle.putString(CaldroidFragment.DIALOG_TITLE, "Select a date");
        dialogCaldroidFragment.setArguments(bundle);
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    private void show(String mtd,String ds,String de){
        final ArrayList<TourExReport> expList1 = mAceDnsDatabase.getTourExp(mtd,ds,de);

        ListView dialogList = (ListView) findViewById(R.id.listView);
        TourExpReportAdapter adapter1 = new TourExpReportAdapter(TourExpReportActivity.this, R.layout.tour_exp_list_child_report, expList1);
        dialogList.setAdapter(adapter1);

        double sum = 0;
        for(int i = 0; i < expList1.size(); i++) {
            sum += Double.parseDouble(expList1.get(i).getTotal().toString());
        }

        textViewAmountTotal.setText(""+sum);

    }

    private void showD(String mtd,String ds,String de){
        final ArrayList<TourExReport> expList1 = mAceDnsDatabase.getTourExpD(mtd,ds,de);

        ListView dialogList = (ListView) findViewById(R.id.listView);
        TourExpReportAdapter adapter1 = new TourExpReportAdapter(TourExpReportActivity.this, R.layout.tour_exp_list_child_report, expList1);
        dialogList.setAdapter(adapter1);

        double sum = 0;
        for(int i = 0; i < expList1.size(); i++) {
            sum += Double.parseDouble(expList1.get(i).getTotal().toString());
        }

        textViewAmountTotal.setText(""+sum);

    }

}