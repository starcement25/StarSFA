package com.forcepower.acedns.activity;

import static com.forcepower.acedns.constants.Constants.masterApiCallingFlag;

import androidx.fragment.app.FragmentActivity;

import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyTask;
import com.forcepower.acedns.bean.CommonHelper;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.bean.SisSummary;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.parser.RoutePlanDetailsXMLParsing;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.JsonsReceiver;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;
import com.forcepower.acedns.util.commonAsyncTaskSETUP;
import com.forcepower.acedns.constants.BaseUrl;

import java.util.ArrayList;

public class SisSummeryActivity extends FragmentActivity {

    public Handler mReportHandler;
    public Handler mHandlerPrepareSaudaData;
    Button btnBack;
    public ProgressDialog mProgressDialogPrepareData;
    String httpResponse = "";
    Context mContext;
    Spinner spdate,spCustomer;
    private ArrayList<String> emp;
    private ArrayList<String> month;
    AceDnsDatabase mAceDnsDatabaseHelper;
    ConnectionDetector cd;
    Boolean isInternetPresent = false;
    TextView txtempCode,txtName,txtMonthYear,tvParameter1,txtTGT,txtACH,txtACH_percent,txtWtgPercent;
    TextView txtScorePercent1,txtParameter2,txtTGT2,txtACH2,txtAchPercent2,txtWgtPercent2,txtScorePercent,txtEarningScorePercent,txtpenaltyPercent;
    TextView txtDealerapp,txtTgt3,txtACH3,txtACHPercent3,txtWTGPercent3,txtScorePercent3,txtFinalScorePercent,txtOtsi,txtSisEarningForTheMonth,txtRemarks,txtCount4,txtACH4,txtACHPercent4,txtTgt4,txtScorePercent4,txtWTGPercent4,txtCount5,txtACH5,txtACHPercent5,txtTgt5,txtScorePercent5,txtWTGPercent5,txtCount6,txtACH6,txtACHPercent6,txtTgt6,txtScorePercent6,txtWTGPercent6,txtDealerActive;
    TextView header1p4,header2p4,header1p5,header2p5,header1p6,header1p3,header1p1,header2p1,header3p1,header4p1,header5p1,header1p2,header2p2,header3p2,header4p2,header5p2,header2p3,header3p3,header4p3,header5p3,header3p4,header4p4,header5p4,header3p5,header4p5,header5p5,header2p6,header3p6,header6p1,header6p2,header6p3,header6p4,header6p5,header6p6,header6p7,header6p8,header6p9,header6p10,header6p11,header4p6,header5p6;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_sis_summery);

        spCustomer = findViewById(R.id.spCustomer);
        spdate = findViewById(R.id.spdatemonth);
        btnBack = (Button) findViewById(R.id.back);
        txtempCode = (TextView) findViewById(R.id.txtempCode);
        txtName = (TextView) findViewById(R.id.tvCustName);
        txtMonthYear = (TextView) findViewById(R.id.txtMonthYear);
        tvParameter1 = (TextView) findViewById(R.id.tvParameter1);
        txtTGT = (TextView) findViewById(R.id.txtTGT);
        txtACH = (TextView) findViewById(R.id.txtACH);
        txtACH_percent = (TextView) findViewById(R.id.txtACH_percent);
        txtWtgPercent = (TextView) findViewById(R.id.txtWtgPercent);
        txtScorePercent1 = (TextView) findViewById(R.id.txtScorePercent1);
        txtParameter2 = (TextView) findViewById(R.id.txtParameter2);
        txtTGT2 = (TextView) findViewById(R.id.txtTGT2);
        txtACH2 = (TextView) findViewById(R.id.txtACH2);
        txtAchPercent2 = (TextView) findViewById(R.id.txtAchPercent2);
        txtWgtPercent2 = (TextView) findViewById(R.id.txtWgtPercent2);
        txtScorePercent = (TextView) findViewById(R.id.txtScorePercent);
        txtDealerapp = (TextView) findViewById(R.id.txtDealerapp);
        txtTgt3 = (TextView) findViewById(R.id.txtTgt3);
        txtACH3 = (TextView) findViewById(R.id.txtACH3);
        txtACHPercent3 = (TextView) findViewById(R.id.txtACHPercent3);
        txtWTGPercent3 = (TextView) findViewById(R.id.txtWTGPercent3);
        txtEarningScorePercent = (TextView) findViewById(R.id.txtEarningScorePercent);
        txtpenaltyPercent = (TextView) findViewById(R.id.txtpenaltyPercent);
        txtScorePercent3 = (TextView) findViewById(R.id.txtScorePercent3);
        txtFinalScorePercent = (TextView) findViewById(R.id.txtFinalScorePercent);
        txtOtsi = (TextView) findViewById(R.id.txtOtsi);
        txtSisEarningForTheMonth = (TextView) findViewById(R.id.txtSisEarningForTheMonth);
        txtRemarks = (TextView) findViewById(R.id.txtRemarks);
        txtCount4 = (TextView) findViewById(R.id.txtCount4);
        txtACH4 = (TextView) findViewById(R.id.txtACH4);
        txtACHPercent4 = (TextView) findViewById(R.id.txtACHPercent4);
        txtTgt4 = (TextView) findViewById(R.id.txtTgt4);
        txtScorePercent4 = (TextView) findViewById(R.id.txtScorePercent4);
        txtWTGPercent4 = (TextView) findViewById(R.id.txtWTGPercent4);
        txtDealerActive= (TextView) findViewById(R.id.txtDealerActive);

        txtCount5 = (TextView) findViewById(R.id.txtCount5);
        txtACH5 = (TextView) findViewById(R.id.txtACH5);
        txtACHPercent5 = (TextView) findViewById(R.id.txtACHPercent5);
        txtTgt5 = (TextView) findViewById(R.id.txtTgt5);
        txtScorePercent5 = (TextView) findViewById(R.id.txtScorePercent5);
        txtWTGPercent5 = (TextView) findViewById(R.id.txtWTGPercent5);

        txtCount6 = (TextView) findViewById(R.id.txtCount6);
        txtACH6 = (TextView) findViewById(R.id.txtACH6);
        txtACHPercent6 = (TextView) findViewById(R.id.txtACHPercent6);
        txtTgt6 = (TextView) findViewById(R.id.txtTgt6);
        txtScorePercent6 = (TextView) findViewById(R.id.txtScorePercent6);
        txtWTGPercent6 = (TextView) findViewById(R.id.txtWTGPercent6);


        header1p4 = (TextView) findViewById(R.id.header1p4);
        header2p4 = (TextView) findViewById(R.id.header2p4);
        header1p5 = (TextView) findViewById(R.id.header1p5);
        header2p5 = (TextView) findViewById(R.id.header2p5);
        //header1p6 = (TextView) findViewById(R.id.header1p6);
        header1p3 = (TextView) findViewById(R.id.header1p3);

        header1p1 = (TextView) findViewById(R.id.header1p1);
        header2p1 = (TextView) findViewById(R.id.header2p1);
        header3p1 = (TextView) findViewById(R.id.header3p1);
        header4p1 = (TextView) findViewById(R.id.header4p1);
        header5p1 = (TextView) findViewById(R.id.header5p1);
        header6p1 = (TextView) findViewById(R.id.header6p1);

        header1p2 = (TextView) findViewById(R.id.header1p2);
        header2p2 = (TextView) findViewById(R.id.header2p2);
        header3p2 = (TextView) findViewById(R.id.header3p2);
        header4p2 = (TextView) findViewById(R.id.header4p2);
        header5p2 = (TextView) findViewById(R.id.header5p2);
        header6p2 = (TextView) findViewById(R.id.header6p2);

        header2p3 = (TextView) findViewById(R.id.header2p3);
        header3p3 = (TextView) findViewById(R.id.header3p3);
        header4p3 = (TextView) findViewById(R.id.header4p3);
        header5p3 = (TextView) findViewById(R.id.header5p3);
        header6p3 = (TextView) findViewById(R.id.header6p3);

        header1p4 = (TextView) findViewById(R.id.header1p4);
        header2p4 = (TextView) findViewById(R.id.header2p4);
        header3p4 = (TextView) findViewById(R.id.header3p4);
        header4p4 = (TextView) findViewById(R.id.header4p4);
        header5p4 = (TextView) findViewById(R.id.header5p4);
        header6p4 = (TextView) findViewById(R.id.header6p4);

        header1p5 = (TextView) findViewById(R.id.header1p5);
        header2p5 = (TextView) findViewById(R.id.header2p5);
        header3p5 = (TextView) findViewById(R.id.header3p5);
        header4p5 = (TextView) findViewById(R.id.header4p5);
        header5p5 = (TextView) findViewById(R.id.header5p5);
        header6p5 = (TextView) findViewById(R.id.header6p5);

        header3p6 = (TextView) findViewById(R.id.header3p6);
        header2p6 = (TextView) findViewById(R.id.header2p6);

        //headerEarningScore = (TextView) findViewById(R.id.header6p10);
        //headerPenalty = (TextView) findViewById(R.id.headerPenalty);
        //headerFinalScore = (TextView) findViewById(R.id.header6p9);
        //headerRemarks = (TextView) findViewById(R.id.header6p11);

        header6p6 = (TextView) findViewById(R.id.header6p6);
        header6p7 = (TextView) findViewById(R.id.header6p7);
        header6p8 = (TextView) findViewById(R.id.header6p8);
        header6p9 = (TextView) findViewById(R.id.header6p9);
        header6p10 = (TextView) findViewById(R.id.header6p10);
        header6p11 = (TextView) findViewById(R.id.header6p11);


        mContext = SisSummeryActivity.this;
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));


        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });


        mReportHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mProgressDialogPrepareData.cancel();
                final int dojob = threadmsg.getData().getInt("JOB");
                SisSummeryActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                try{
                                    //setSpiner();
                                    //setData(spdate.getSelectedItem().toString(),"c");
                                    PrepareCustomerData(2);

                                }catch (Exception e){

                                }

                                break;
                            case 2:
                                try{
                                    setSpiner();
                                    setData(spdate.getSelectedItem().toString(),"c");

                                }catch (Exception e){

                                }

                                break;
                        }
                    }
                });
            }
        };



        ConnectionDetector cd;
        cd = new ConnectionDetector(mContext);
        if(cd.isConnectingToInternet())
        {
            PrepareCustomerData(1);
        }else{
            //setData(spdate.getSelectedItem().toString(),"e");
             Toast.makeText(mContext, "Please Connect INTERNET For update Data", Toast.LENGTH_SHORT).show();
        }

        //month = mAceDnsDatabaseHelper.getSisMonth();
        month = mAceDnsDatabaseHelper.getSisSummaryMonth();//{ "Jan-"+year, "Feb-"+year,
        //"March-"+year, "April-"+year,
        //"May-"+year, "Jun-"+year,"July-"+year,"Aug-"+year,"Sept-"+year,"Oct-"+year,"Nov-"+year,"Dec-"+year };

        ArrayAdapter ad
                = new ArrayAdapter(
                this,
                android.R.layout.simple_spinner_item,
                month);

        ad.setDropDownViewResource(
                android.R.layout
                        .simple_spinner_dropdown_item);

        spdate.setAdapter(ad);

        spdate.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                setData(spdate.getSelectedItem().toString(),"sp");
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

    }

    private void setSpiner(){
        month = mAceDnsDatabaseHelper.getSisSummaryMonth();//{ "Jan-"+year, "Feb-"+year,
        //"March-"+year, "April-"+year,
        //"May-"+year, "Jun-"+year,"July-"+year,"Aug-"+year,"Sept-"+year,"Oct-"+year,"Nov-"+year,"Dec-"+year };

        ArrayAdapter ad
                = new ArrayAdapter(
                this,
                android.R.layout.simple_spinner_item,
                month);

        ad.setDropDownViewResource(
                android.R.layout
                        .simple_spinner_dropdown_item);

        spdate.setAdapter(ad);
    }
    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareData = new ProgressDialog(mContext);
        mProgressDialogPrepareData.setCancelable(false);
        mProgressDialogPrepareData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareData.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        //new commonAsyncTaskMaster(mContext, "customer_master");
                        _DOWNLOAD_sis_details();
                        break;

                    case 2:
                        //new commonAsyncTaskMaster(mContext, "customer_master");
                        _DOWNLOAD_sis_header();
                        break;
                }

                Message msg = mReportHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mReportHandler.sendMessage(msg);
            }
        }.start();
    }


    private void setData(String dateMonth,String s){
        try {
            //Toast.makeText(mContext, ""+s, Toast.LENGTH_SHORT).show();
            txtempCode.setText("");
            txtName.setText("");
            txtMonthYear.setText("");
            tvParameter1.setText("");
            txtTGT.setText("");
            txtACH.setText("");
            txtACH_percent.setText("");
            txtWtgPercent.setText("");
            txtScorePercent1.setText("");
            txtParameter2.setText("");
            txtTGT2.setText("");
            txtACH2.setText("");
            txtAchPercent2.setText("");
            txtWgtPercent2.setText("");
            txtScorePercent.setText("");
            txtEarningScorePercent.setText("");
            txtpenaltyPercent.setText("");
            txtFinalScorePercent.setText("");
            txtOtsi.setText("");
            txtSisEarningForTheMonth.setText("");
            txtRemarks.setText("");
            txtCount4.setText("");
            txtDealerActive.setText("");
            txtACH4.setText("");
            txtACHPercent4.setText("");
            txtTgt4.setText("");
            txtScorePercent4.setText("");
            txtScorePercent4.setText("");

            txtCount5.setText("");
            txtACH5.setText("");
            txtACHPercent5.setText("");
            txtTgt5.setText("");
            txtScorePercent5.setText("");
            txtScorePercent5.setText("");

            txtCount6.setText("");
            txtACH6.setText("");
            txtACHPercent6.setText("");
            txtTgt6.setText("");
            txtScorePercent6.setText("");
            txtScorePercent6.setText("");
            //txtCount4,txtACH4,txtACHPercent4,txtTgt4,txtScorePercent4,txtScorePercent4
            ArrayList<SisSummary> detailList = new ArrayList<SisSummary>();
            detailList = mAceDnsDatabaseHelper.getSisSummaryDetails(dateMonth);

            if(detailList.get(0).getEmp_code().equals("null")){
                txtempCode.setText("");
            }else{
                txtempCode.setText(""+detailList.get(0).getEmp_code());
            }
            if(detailList.get(0).getName().equals("null")){
                txtName.setText("");
            }else{
                txtName.setText(detailList.get(0).getName());
            }
            if(detailList.get(0).getMonth_year().equals("null")){
                txtMonthYear.setText("");
            }else{
                txtMonthYear.setText(detailList.get(0).getMonth_year());
            }
            if(detailList.get(0).getSales_volume_MT().equals("null")){
                tvParameter1.setText("");
            }else{
                tvParameter1.setText(detailList.get(0).getSales_volume_MT() + " :-");
            }
            if(detailList.get(0).getSales_volume_TGT().equals("null")){
                txtTGT.setText("");
            }else{
                txtTGT.setText(detailList.get(0).getSales_volume_TGT());
            }
            if(detailList.get(0).getSales_volume_ACH().equals("null")){
                txtACH.setText("");
            }else{
                txtACH.setText(detailList.get(0).getSales_volume_ACH());
            }
            if(detailList.get(0).getSales_volume_ACH_percent().equals("null")){
                txtACH_percent.setText("");
            }else{
                txtACH_percent.setText(detailList.get(0).getSales_volume_ACH_percent());
            }
            if(detailList.get(0).getSales_volume_WGT_percent().equals("null")){
                txtWtgPercent.setText("");
            }else{
                txtWtgPercent.setText(detailList.get(0).getSales_volume_WGT_percent());
            }
            if(detailList.get(0).getSales_volume_SCORE_percent().equals("null")){
                txtScorePercent1.setText("");
            }else{
                txtScorePercent1.setText(detailList.get(0).getSales_volume_SCORE_percent());
            }
            if(detailList.get(0).getMonthly_unique_visit().equals("null")){
                txtParameter2.setText("");
            }else{
                txtParameter2.setText(detailList.get(0).getMonthly_unique_visit() +" :- ");
            }
            if(detailList.get(0).getMonthly_unique_visit_TGT().equals("null")){
                txtTGT2.setText("");
            }else{
                txtTGT2.setText(detailList.get(0).getMonthly_unique_visit_TGT());
            }
            if(detailList.get(0).getMonthly_unique_visit_ACH().equals("null")){
                txtACH2.setText("");
            }else{
                txtACH2.setText(detailList.get(0).getMonthly_unique_visit_ACH());
            }

            if(detailList.get(0).getMonthly_unique_visit_ACH_percent().equals("null")){
                txtAchPercent2.setText("");
            }else{
                txtAchPercent2.setText(detailList.get(0).getMonthly_unique_visit_ACH_percent());
            }
            if(detailList.get(0).getMonthly_unique_visit_WGT_percent().equals("null")){
                txtWgtPercent2.setText("");
            }else{
                txtWgtPercent2.setText(detailList.get(0).getMonthly_unique_visit_WGT_percent());
            }
            if(detailList.get(0).getMonthly_unique_visit_SCORE_percent().equals("null")){
                txtScorePercent.setText("");
            }else{
                txtScorePercent.setText(detailList.get(0).getMonthly_unique_visit_SCORE_percent());
            }
            if(detailList.get(0).getDealer_appointment().equals("null")){
                txtDealerapp.setText("");
            }else{
                txtDealerapp.setText(detailList.get(0).getDealer_appointment());
            }
            if(detailList.get(0).getDealer_appointment_TGT().equals("null")){
                txtTgt3.setText("");
            }else{
                txtTgt3.setText(detailList.get(0).getDealer_appointment_TGT());
            }
            if(detailList.get(0).getDealer_appointment_ACH().equals("null")){
                txtACH3.setText("");
            }else{
                txtACH3.setText(detailList.get(0).getDealer_appointment_ACH());
            }
            if(detailList.get(0).getDealer_appointment_ACH_percent().equals("null")){
                txtACHPercent3.setText("");
            }else{
                txtACHPercent3.setText(detailList.get(0).getDealer_appointment_ACH_percent());
            }
            if(detailList.get(0).getDealer_appointment_WGT_percent().equals("null")){
                txtWTGPercent3.setText("");
            }else{
                txtWTGPercent3.setText(detailList.get(0).getDealer_appointment_WGT_percent());
            }
            if(detailList.get(0).getDealer_appointment_SCORE_percent().equals("null")){
                txtScorePercent3.setText("");
            }else{
                txtScorePercent3.setText(detailList.get(0).getDealer_appointment_SCORE_percent());
            }
            if(detailList.get(0).getEarning_score_percent().equals("null")){
                txtEarningScorePercent.setText("");
            }else{
                txtEarningScorePercent.setText(detailList.get(0).getEarning_score_percent());
            }
            if(detailList.get(0).getPenalty_percent().equals("null")){
                txtpenaltyPercent.setText("");
            }else{
                txtpenaltyPercent.setText(detailList.get(0).getPenalty_percent());
            }
            if(detailList.get(0).getFinal_score_percent().equals("null")){
                txtFinalScorePercent.setText("");
            }else{
                txtFinalScorePercent.setText(detailList.get(0).getFinal_score_percent());
            }
            if(detailList.get(0).getOTSI().equals("null")){
                txtOtsi.setText("");
            }else{
                txtOtsi.setText(detailList.get(0).getOTSI());
            }
            if(detailList.get(0).getSIS_earning_month().equals("null")){
                txtSisEarningForTheMonth.setText("");
            }else{
                txtSisEarningForTheMonth.setText(detailList.get(0).getSIS_earning_month());
            }
            if(detailList.get(0).getRemarks().equals("null")){
                txtRemarks.setText("");
            }else{
                txtRemarks.setText(detailList.get(0).getRemarks());
            }

            if(detailList.get(0).getActive_dealer_count().equals("null")){
                txtDealerActive.setText("");
            }else{
                txtDealerActive.setText(detailList.get(0).getActive_dealer_count());
            }
            if(detailList.get(0).getActive_dealer_count_ACH().equals("null")){
                txtACH4.setText("");
            }else{
                txtACH4.setText(detailList.get(0).getActive_dealer_count_ACH());
            }
            if(detailList.get(0).getActive_dealer_count_ACH_percent().equals("null")){
                txtACHPercent4.setText("");
            }else{
                txtACHPercent4.setText(detailList.get(0).getActive_dealer_count_ACH_percent());
            }
            if(detailList.get(0).getActive_dealer_count_TGT().equals("null")){
                txtTgt4.setText("");
            }else{
                txtTgt4.setText(detailList.get(0).getActive_dealer_count_TGT());
            }
            if(detailList.get(0).getActive_dealer_count_SCORE_percent().equals("null")){
                txtScorePercent4.setText("");
            }else{
                txtScorePercent4.setText(detailList.get(0).getActive_dealer_count_SCORE_percent());
            }
            if(detailList.get(0).getActive_dealer_count_WGT_percent().equals("null")){
                txtWTGPercent4.setText("");
            }else{
                txtWTGPercent4.setText(detailList.get(0).getActive_dealer_count_WGT_percent());
            }

            if(detailList.get(0).getParamiter_five().equals("null")){
                txtCount5.setText("");
            }else{
                txtCount5.setText(detailList.get(0).getParamiter_five());
            }
            if(detailList.get(0).getFive_ACH().equals("null")){
                txtACH5.setText("");
            }else{
                txtACH5.setText(detailList.get(0).getFive_ACH());
            }
            if(detailList.get(0).getFive_percent().equals("null")){
                txtACHPercent5.setText("");
            }else{
                txtACHPercent5.setText(detailList.get(0).getFive_percent());
            }
            if(detailList.get(0).getFive_TGT().equals("null")){
                txtTgt5.setText("");
            }else{
                txtTgt5.setText(detailList.get(0).getFive_TGT());
            }
            if(detailList.get(0).getFive_SCORE_percent().equals("null")){
                txtScorePercent5.setText("");
            }else{
                txtScorePercent5.setText(detailList.get(0).getFive_SCORE_percent());
            }
            if(detailList.get(0).getFive_WGT_percent().equals("null")){
                txtWTGPercent5.setText("");
            }else{
                txtWTGPercent5.setText(detailList.get(0).getFive_WGT_percent());
            }


            if(detailList.get(0).getParamiter_six().equals("null")){
                txtCount6.setText("");
            }else{
                txtCount6.setText(detailList.get(0).getParamiter_six());
            }
            if(detailList.get(0).getSix_ACH().equals("null")){
                txtACH6.setText("");
            }else{
                txtACH6.setText(detailList.get(0).getSix_ACH());
            }
            if(detailList.get(0).getSix_percent().equals("null")){
                txtACHPercent6.setText("");
            }else{
                txtACHPercent6.setText(detailList.get(0).getSix_percent());
            }
            if(detailList.get(0).getSix_TGT().equals("null")){
                txtTgt6.setText("");
            }else{
                txtTgt6.setText(detailList.get(0).getSix_TGT());
            }
            if(detailList.get(0).getSix_SCORE_percent().equals("null")){
                txtScorePercent6.setText("");
            }else{
                txtScorePercent6.setText(detailList.get(0).getSix_SCORE_percent());
            }
            if(detailList.get(0).getSix_WGT_percent().equals("null")){
                txtWTGPercent6.setText("");
            }else{
                txtWTGPercent6.setText(detailList.get(0).getSix_WGT_percent());
            }


            ArrayList<CommonHelper> headerlList = new ArrayList<CommonHelper>();
            headerlList = mAceDnsDatabaseHelper.getSisSummaryheader(detailList.get(0).getHeader_id());

            header1p1.setText(headerlList.get(0).getItem5());
            header1p2.setText(headerlList.get(0).getItem6());
            header1p3.setText(headerlList.get(0).getItem7());
            header1p4.setText(headerlList.get(0).getItem8());
            header1p5.setText(headerlList.get(0).getItem9());

            header2p1.setText(headerlList.get(0).getItem11());
            header2p2.setText(headerlList.get(0).getItem12());
            header2p3.setText(headerlList.get(0).getItem13());
            header2p4.setText(headerlList.get(0).getItem14());
            header2p5.setText(headerlList.get(0).getItem15());

            header3p1.setText(headerlList.get(0).getItem17());
            header3p2.setText(headerlList.get(0).getItem18());
            header3p3.setText(headerlList.get(0).getItem19());
            header3p4.setText(headerlList.get(0).getItem20());
            header3p5.setText(headerlList.get(0).getItem21());

            header4p1.setText(headerlList.get(0).getItem23());
            header4p2.setText(headerlList.get(0).getItem24());
            header4p3.setText(headerlList.get(0).getItem25());
            header4p4.setText(headerlList.get(0).getItem26());
            header4p5.setText(headerlList.get(0).getItem27());

            header5p1.setText(headerlList.get(0).getItem29());
            header5p2.setText(headerlList.get(0).getItem30());
            header5p3.setText(headerlList.get(0).getItem31());
            header5p4.setText(headerlList.get(0).getItem32());
            header5p5.setText(headerlList.get(0).get_item33());

            header6p1.setText(headerlList.get(0).get_item35());
            header6p2.setText(headerlList.get(0).get_item36());
            header6p3.setText(headerlList.get(0).get_item37());
            header6p4.setText(headerlList.get(0).get_item38());
            header6p5.setText(headerlList.get(0).get_item39());

            //headerEarningScore.setText(headerlList.get(0).getItem44());
            //headerPenalty.setText(headerlList.get(0).getItem29());
            //headerFinalScore.setText(headerlList.get(0).getItem30());
            //headerRemarks.setText(headerlList.get(0).getItem45());

            header6p6.setText(headerlList.get(0).get_item40());
            header6p7.setText(headerlList.get(0).get_item41());
            header6p8.setText(headerlList.get(0).get_item42());
            header6p9.setText(headerlList.get(0).get_item43());
            header6p10.setText(headerlList.get(0).getItem44());
            header6p11.setText(headerlList.get(0).getItem45());
            //header1p5.setText(headerlList.get(0).getFive_TGT());
            //header2p5.setText(headerlList.get(0).getFive_ACH());
            //header1p6.setText(headerlList.get(0).getSix_TGT());

            //header1p3.setText(headerlList.get(0).getDealer_appointment_TGT());


            //Your task here
        }catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    ContentValues values;
    AceDnsDatabase mAceDnsDatabase;
    public void commonNameValuePair() {
        values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
        //values.put("mode", "SETUP");
    }

    public void _DOWNLOAD_sis_header() {
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        commonNameValuePair();
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(BaseUrl.baseUrl + "sis-summary-header.php?nick_name="+Constants.nickName+"&emp_code="+Constants.employeeDetailObject.getEmpCode(), "");
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ SisSummeryActivity: " + BaseUrl.baseUrl + "sis-summary-header.php?nick_name="+Constants.nickName+"&emp_code="+Constants.employeeDetailObject.getEmpCode());
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ SisSummeryActivity: " + httpResponse);
        if (httpResponse.length() > 0 && !httpResponse.equalsIgnoreCase("Network Failure")) {

            if (httpResponse != null) {
                long insertStatus = mAceDnsDatabase.insertToSisSummeryheader(httpResponse);
                if (insertStatus == 1) {
                    //Constants.isRoutePlanDetailsUpdated = false;
                    //decideNavigation_route_plan_details(routeObj.getLastUpdateTime());
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                //new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_sis_details() {
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        commonNameValuePair();
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(BaseUrl.baseUrl + "sis-summary-details-v2.php?nick_name="+Constants.nickName+"&emp_code="+Constants.employeeDetailObject.getEmpCode(), "");
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ SisSummeryActivity: " + BaseUrl.baseUrl + "sis-summary-details-v2.php?nick_name="+Constants.nickName+"&emp_code="+Constants.employeeDetailObject.getEmpCode());
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ SisSummeryActivity: " + httpResponse);
        if (httpResponse.length() > 0 && !httpResponse.equalsIgnoreCase("Network Failure")) {

            if (httpResponse != null) {
                long insertStatus = mAceDnsDatabase.insertToSisSummeryDetails(httpResponse);
                if (insertStatus == 1) {
                    //Constants.isRoutePlanDetailsUpdated = false;
                    //decideNavigation_route_plan_details(routeObj.getLastUpdateTime());
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                //new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

}