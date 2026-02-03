package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
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

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.SisSummary;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

import java.util.ArrayList;

public class BdSisActivity extends FragmentActivity {
    public Handler mReportHandler;
    public Handler mHandlerPrepareSaudaData;
    Button btnBack;
    public ProgressDialog mProgressDialogPrepareData;
    String httpResponse = "";
    Context mContext;
    Spinner spdate, spCustomer;
    private ArrayList<String> month;
    AceDnsDatabase mAceDnsDatabaseHelper;
    TextView txtempCode, txtName, txtMonthYear, tvParameter1, txtTGT, txtACH, txtACH_percent, txtWtgPercent;
    TextView txtScorePercent1, txtParameter2, txtTGT2, txtACH2, txtAchPercent2, txtWgtPercent2, txtScorePercent, txtEarningScorePercent, txtpenaltyPercent;
    TextView txtDealerapp, txtTgt3, txtACH3, txtACHPercent3, txtWTGPercent3, txtScorePercent3, txtFinalScorePercent, txtOtsi, txtSisEarningForTheMonth, txtRemarks, txtCount4, txtACH4,
            txtACHPercent4, txtTgt4, txtScorePercent4, txtWTGPercent4, txtCount5, txtACH5, txtACHPercent5, txtTgt5, txtScorePercent5, txtWTGPercent5, txtCount6, txtACH6, txtACHPercent6,
            txtTgt6, txtScorePercent6, txtWTGPercent6, txtDealerActive;

    TextView header1p4, header2p4, header1p5, header2p5, header1p6, header1p3, header1p1, header2p1, header3p1, header4p1, header5p1, header1p2, header2p2, header3p2, header4p2, header5p2,
            header2p3, header3p3, header4p3, header3p4, header4p4, header5p4, header3p5, header4p5, header5p5, header2p6, header3p6, headerEarningScore, headerPenalty, headerFinalScore, headerRemarks;

    @SuppressLint({"SetTextI18n", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_bd_sis);

        spCustomer = findViewById(R.id.spCustomer);
        spdate = findViewById(R.id.spdatemonth);
        btnBack = findViewById(R.id.back);
        txtempCode = findViewById(R.id.txtempCode);
        txtName = findViewById(R.id.tvCustName);
        txtMonthYear = findViewById(R.id.txtMonthYear);
        tvParameter1 = findViewById(R.id.tvParameter1);
        txtTGT = findViewById(R.id.txtTGT);
        txtACH = findViewById(R.id.txtACH);
        txtACH_percent = findViewById(R.id.txtACH_percent);
        txtWtgPercent = findViewById(R.id.txtWtgPercent);
        txtScorePercent1 = findViewById(R.id.txtScorePercent1);
        txtParameter2 = findViewById(R.id.txtParameter2);
        txtTGT2 = findViewById(R.id.txtTGT2);
        txtACH2 = findViewById(R.id.txtACH2);
        txtAchPercent2 = findViewById(R.id.txtAchPercent2);
        txtWgtPercent2 = findViewById(R.id.txtWgtPercent2);
        txtScorePercent = findViewById(R.id.txtScorePercent);
        txtDealerapp = findViewById(R.id.txtDealerapp);
        txtTgt3 = findViewById(R.id.txtTgt3);
        txtACH3 = findViewById(R.id.txtACH3);
        txtACHPercent3 = findViewById(R.id.txtACHPercent3);
        txtWTGPercent3 = findViewById(R.id.txtWTGPercent3);
        txtEarningScorePercent = findViewById(R.id.txtEarningScorePercent);
        txtpenaltyPercent = findViewById(R.id.txtpenaltyPercent);
        txtScorePercent3 = findViewById(R.id.txtScorePercent3);
        txtFinalScorePercent = findViewById(R.id.txtFinalScorePercent);
        txtOtsi = findViewById(R.id.txtOtsi);
        txtSisEarningForTheMonth = findViewById(R.id.txtSisEarningForTheMonth);
        txtRemarks = findViewById(R.id.txtRemarks);
        txtCount4 = findViewById(R.id.txtCount4);
        txtACH4 = findViewById(R.id.txtACH4);
        txtACHPercent4 = findViewById(R.id.txtACHPercent4);
        txtTgt4 = findViewById(R.id.txtTgt4);
        txtScorePercent4 = findViewById(R.id.txtScorePercent4);
        txtWTGPercent4 = findViewById(R.id.txtWTGPercent4);
        txtDealerActive = findViewById(R.id.txtDealerActive);

        txtCount5 = findViewById(R.id.txtCount5);
        txtACH5 = findViewById(R.id.txtACH5);
        txtACHPercent5 = findViewById(R.id.txtACHPercent5);
        txtTgt5 = findViewById(R.id.txtTgt5);
        txtScorePercent5 = findViewById(R.id.txtScorePercent5);
        txtWTGPercent5 = findViewById(R.id.txtWTGPercent5);

        txtCount6 = findViewById(R.id.txtCount6);
        txtACH6 = findViewById(R.id.txtACH6);
        txtACHPercent6 = findViewById(R.id.txtACHPercent6);
        txtTgt6 = findViewById(R.id.txtTgt6);
        txtScorePercent6 = findViewById(R.id.txtScorePercent6);
        txtWTGPercent6 = findViewById(R.id.txtWTGPercent6);

        header1p4 = findViewById(R.id.header1p4);
        header2p4 = findViewById(R.id.header2p4);
        header1p5 = findViewById(R.id.header1p5);
        header2p5 = findViewById(R.id.header2p5);
        header1p6 = findViewById(R.id.header1p6);
        header1p3 = findViewById(R.id.header1p3);

        header1p1 = findViewById(R.id.header1p1);
        header2p1 = findViewById(R.id.header2p1);
        header3p1 = findViewById(R.id.header3p1);
        header4p1 = findViewById(R.id.header4p1);
        header5p1 = findViewById(R.id.header5p1);

        header1p2 = findViewById(R.id.header1p2);
        header2p2 = findViewById(R.id.header2p2);
        header3p2 = findViewById(R.id.header3p2);
        header4p2 = findViewById(R.id.header4p2);
        header5p2 = findViewById(R.id.header5p2);

        header2p3 = findViewById(R.id.header2p3);
        header3p3 = findViewById(R.id.header3p3);
        header4p3 = findViewById(R.id.header4p3);

        header3p4 = findViewById(R.id.header3p4);
        header4p4 = findViewById(R.id.header4p4);
        header5p4 = findViewById(R.id.header5p4);

        header3p5 = findViewById(R.id.header3p5);
        header4p5 = findViewById(R.id.header4p5);
        header5p5 = findViewById(R.id.header5p5);

        header3p6 = findViewById(R.id.header3p6);
        header2p6 = findViewById(R.id.header2p6);
        headerEarningScore = findViewById(R.id.headerEarningScore);
        headerPenalty = findViewById(R.id.headerPenalty);
        headerFinalScore = findViewById(R.id.headerFinalScore);
        headerRemarks = findViewById(R.id.headerRemarks);

        mContext = BdSisActivity.this;
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        btnBack.setOnClickListener(v -> finish());

        mReportHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mProgressDialogPrepareData.cancel();
                final int dojob = threadmsg.getData().getInt("JOB");
                BdSisActivity.this.runOnUiThread(() -> {
                    switch (dojob) {
                        case 1:
                            try {
                                PrepareCustomerData(2);
                            } catch (Exception ignored) {}
                            break;
                        case 2:
                            try {
                                setSpiner();
                                setData(spdate.getSelectedItem().toString());
                            } catch (Exception ignored) {}
                            break;
                    }
                });
            }
        };

        ConnectionDetector cd;
        cd = new ConnectionDetector(mContext);
        if (cd.isConnectingToInternet()) {
            PrepareCustomerData(1);
        } else {
            Toast.makeText(mContext, "Please Connect INTERNET For update Data", Toast.LENGTH_SHORT).show();
        }
        month = mAceDnsDatabaseHelper.getBdSisSummaryMonth();
        ArrayAdapter ad = new ArrayAdapter(this, android.R.layout.simple_spinner_item, month);
        ad.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

        spdate.setAdapter(ad);

        spdate.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                setData(spdate.getSelectedItem().toString());
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) { }
        });

    }

    private void setSpiner() {
        month = mAceDnsDatabaseHelper.getBdSisSummaryMonth();
        ArrayAdapter ad = new ArrayAdapter(this, android.R.layout.simple_spinner_item, month);
        ad.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
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
                        _DOWNLOAD_sis_details();
                        break;
                    case 2:
                        _DOWNLOAD_bd_sis_header();
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


    @SuppressLint("SetTextI18n")
    private void setData(String dateMonth) {
        try {
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
            ArrayList<SisSummary> detailList;
            detailList = mAceDnsDatabaseHelper.getBdSisSummaryDetails(dateMonth);

            if (detailList.get(0).getEmp_code().equals("null")) {
                txtempCode.setText("");
            } else {
                txtempCode.setText(detailList.get(0).getEmp_code());
            }
            if (detailList.get(0).getName().equals("null")) {
                txtName.setText("");
            } else {
                txtName.setText(detailList.get(0).getName());
            }
            if (detailList.get(0).getMonth_year().equals("null")) {
                txtMonthYear.setText("");
            } else {
                txtMonthYear.setText(detailList.get(0).getMonth_year());
            }
            if (detailList.get(0).getSales_volume_MT().equals("null")) {
                tvParameter1.setText("");
            } else {
                tvParameter1.setText(detailList.get(0).getSales_volume_MT() + " :-");
            }
            if (detailList.get(0).getSales_volume_TGT().equals("null")) {
                txtTGT.setText("");
            } else {
                txtTGT.setText(detailList.get(0).getSales_volume_TGT());
            }
            if (detailList.get(0).getSales_volume_ACH().equals("null")) {
                txtACH.setText("");
            } else {
                txtACH.setText(detailList.get(0).getSales_volume_ACH());
            }
            if (detailList.get(0).getSales_volume_ACH_percent().equals("null")) {
                txtACH_percent.setText("");
            } else {
                txtACH_percent.setText(detailList.get(0).getSales_volume_ACH_percent());
            }
            if (detailList.get(0).getSales_volume_WGT_percent().equals("null")) {
                txtWtgPercent.setText("");
            } else {
                txtWtgPercent.setText(detailList.get(0).getSales_volume_WGT_percent());
            }
            if (detailList.get(0).getSales_volume_SCORE_percent().equals("null")) {
                txtScorePercent1.setText("");
            } else {
                txtScorePercent1.setText(detailList.get(0).getSales_volume_SCORE_percent());
            }
            if (detailList.get(0).getMonthly_unique_visit().equals("null")) {
                txtParameter2.setText("");
            } else {
                txtParameter2.setText(detailList.get(0).getMonthly_unique_visit() + " :- ");
            }
            if (detailList.get(0).getMonthly_unique_visit_TGT().equals("null")) {
                txtTGT2.setText("");
            } else {
                txtTGT2.setText(detailList.get(0).getMonthly_unique_visit_TGT());
            }
            if (detailList.get(0).getMonthly_unique_visit_ACH().equals("null")) {
                txtACH2.setText("");
            } else {
                txtACH2.setText(detailList.get(0).getMonthly_unique_visit_ACH());
            }

            if (detailList.get(0).getMonthly_unique_visit_ACH_percent().equals("null")) {
                txtAchPercent2.setText("");
            } else {
                txtAchPercent2.setText(detailList.get(0).getMonthly_unique_visit_ACH_percent());
            }
            if (detailList.get(0).getMonthly_unique_visit_WGT_percent().equals("null")) {
                txtWgtPercent2.setText("");
            } else {
                txtWgtPercent2.setText(detailList.get(0).getMonthly_unique_visit_WGT_percent());
            }
            if (detailList.get(0).getMonthly_unique_visit_SCORE_percent().equals("null")) {
                txtScorePercent.setText("");
            } else {
                txtScorePercent.setText(detailList.get(0).getMonthly_unique_visit_SCORE_percent());
            }
            if (detailList.get(0).getDealer_appointment().equals("null")) {
                txtDealerapp.setText("");
            } else {
                txtDealerapp.setText(detailList.get(0).getDealer_appointment());
            }
            if (detailList.get(0).getDealer_appointment_TGT().equals("null")) {
                txtTgt3.setText("");
            } else {
                txtTgt3.setText(detailList.get(0).getDealer_appointment_TGT());
            }
            if (detailList.get(0).getDealer_appointment_ACH().equals("null")) {
                txtACH3.setText("");
            } else {
                txtACH3.setText(detailList.get(0).getDealer_appointment_ACH());
            }
            if (detailList.get(0).getDealer_appointment_ACH_percent().equals("null")) {
                txtACHPercent3.setText("");
            } else {
                txtACHPercent3.setText(detailList.get(0).getDealer_appointment_ACH_percent());
            }
            if (detailList.get(0).getDealer_appointment_WGT_percent().equals("null")) {
                txtWTGPercent3.setText("");
            } else {
                txtWTGPercent3.setText(detailList.get(0).getDealer_appointment_WGT_percent());
            }
            if (detailList.get(0).getDealer_appointment_SCORE_percent().equals("null")) {
                txtScorePercent3.setText("");
            } else {
                txtScorePercent3.setText(detailList.get(0).getDealer_appointment_SCORE_percent());
            }
            if (detailList.get(0).getEarning_score_percent().equals("null")) {
                txtEarningScorePercent.setText("");
            } else {
                txtEarningScorePercent.setText(detailList.get(0).getFinal_score_percent());
            }
            if (detailList.get(0).getPenalty_percent().equals("null")) {
                txtpenaltyPercent.setText("");
            } else {
                txtpenaltyPercent.setText(detailList.get(0).getPenalty_percent());
            }
            if (detailList.get(0).getFinal_score_percent().equals("null")) {
                txtFinalScorePercent.setText("");
            } else {
                txtFinalScorePercent.setText(detailList.get(0).getFinal_score_percent());
            }
            if (detailList.get(0).getOTSI().equals("null")) {
                txtOtsi.setText("");
            } else {
                txtOtsi.setText(detailList.get(0).getOTSI());
            }
            if (detailList.get(0).getSIS_earning_month().equals("null")) {
                txtSisEarningForTheMonth.setText("");
            } else {
                txtSisEarningForTheMonth.setText(detailList.get(0).getSIS_earning_month());
            }
            if (detailList.get(0).getRemarks().equals("null")) {
                txtRemarks.setText("");
            } else {
                txtRemarks.setText(detailList.get(0).getRemarks());
            }

            if (detailList.get(0).getActive_dealer_count().equals("null")) {
                txtDealerActive.setText("");
            } else {
                txtDealerActive.setText(detailList.get(0).getActive_dealer_count());
            }
            if (detailList.get(0).getActive_dealer_count_ACH().equals("null")) {
                txtACH4.setText("");
            } else {
                txtACH4.setText(detailList.get(0).getActive_dealer_count_ACH());
            }
            if (detailList.get(0).getActive_dealer_count_ACH_percent().equals("null")) {
                txtACHPercent4.setText("");
            } else {
                txtACHPercent4.setText(detailList.get(0).getActive_dealer_count_ACH_percent());
            }
            if (detailList.get(0).getActive_dealer_count_TGT().equals("null")) {
                txtTgt4.setText("");
            } else {
                txtTgt4.setText(detailList.get(0).getActive_dealer_count_TGT());
            }
            if (detailList.get(0).getActive_dealer_count_SCORE_percent().equals("null")) {
                txtScorePercent4.setText("");
            } else {
                txtScorePercent4.setText(detailList.get(0).getActive_dealer_count_SCORE_percent());
            }
            if (detailList.get(0).getActive_dealer_count_WGT_percent().equals("null")) {
                txtWTGPercent4.setText("");
            } else {
                txtWTGPercent4.setText(detailList.get(0).getActive_dealer_count_WGT_percent());
            }

            if (detailList.get(0).getParamiter_five().equals("null")) {
                txtCount5.setText("");
            } else {
                txtCount5.setText(detailList.get(0).getParamiter_five());
            }
            if (detailList.get(0).getFive_ACH().equals("null")) {
                txtACH5.setText("");
            } else {
                txtACH5.setText(detailList.get(0).getFive_ACH());
            }
            if (detailList.get(0).getFive_percent().equals("null")) {
                txtACHPercent5.setText("");
            } else {
                txtACHPercent5.setText(detailList.get(0).getFive_percent());
            }
            if (detailList.get(0).getFive_TGT().equals("null")) {
                txtTgt5.setText("");
            } else {
                txtTgt5.setText(detailList.get(0).getFive_TGT());
            }
            if (detailList.get(0).getFive_SCORE_percent().equals("null")) {
                txtScorePercent5.setText("");
            } else {
                txtScorePercent5.setText(detailList.get(0).getFive_SCORE_percent());
            }
            if (detailList.get(0).getFive_WGT_percent().equals("null")) {
                txtWTGPercent5.setText("");
            } else {
                txtWTGPercent5.setText(detailList.get(0).getFive_WGT_percent());
            }


            if (detailList.get(0).getParamiter_six().equals("null")) {
                txtCount6.setText("");
            } else {
                txtCount6.setText(detailList.get(0).getParamiter_six());
            }
            if (detailList.get(0).getSix_ACH().equals("null")) {
                txtACH6.setText("");
            } else {
                txtACH6.setText(detailList.get(0).getSix_ACH());
            }
            if (detailList.get(0).getSix_percent().equals("null")) {
                txtACHPercent6.setText("");
            } else {
                txtACHPercent6.setText(detailList.get(0).getSix_percent());
            }
            if (detailList.get(0).getSix_TGT().equals("null")) {
                txtTgt6.setText("");
            } else {
                txtTgt6.setText(detailList.get(0).getSix_TGT());
            }
            if (detailList.get(0).getPenalty_percent().equals("null")) {
                txtScorePercent6.setText("");
            } else {
                txtScorePercent6.setText(detailList.get(0).getPenalty_percent());
            }
            if (detailList.get(0).getEarning_score_percent().equals("null")) {
                txtWTGPercent6.setText("");
            } else {
                txtWTGPercent6.setText(detailList.get(0).getEarning_score_percent());
            }


            ArrayList<SisSummary> headerlList;
            headerlList = mAceDnsDatabaseHelper.getBdSisSummaryheader(detailList.get(0).getHeader_id());

            header1p1.setText(headerlList.get(0).getSales_volume_TGT());
            header2p1.setText(headerlList.get(0).getSales_volume_ACH());
            header3p1.setText(headerlList.get(0).getSales_volume_ACH_percent());
            header4p1.setText(headerlList.get(0).getSales_volume_WGT_percent());
            header5p1.setText(headerlList.get(0).getSales_volume_SCORE_percent());

            header1p2.setText(headerlList.get(0).getMonthly_unique_visit_TGT());
            header2p2.setText(headerlList.get(0).getMonthly_unique_visit_ACH());
            header3p2.setText(headerlList.get(0).getMonthly_unique_visit_ACH_percent());
            header4p2.setText(headerlList.get(0).getMonthly_unique_visit_WGT_percent());
            header5p2.setText(headerlList.get(0).getMonthly_unique_visit_SCORE_percent());

            header2p3.setText(headerlList.get(0).getDealer_appointment_ACH());
            header3p3.setText(headerlList.get(0).getDealer_appointment_ACH_percent());
            header4p3.setText(headerlList.get(0).getDealer_appointment_WGT_percent());

            header1p4.setText(headerlList.get(0).getActive_dealer_count_TGT());
            header2p4.setText(headerlList.get(0).getActive_dealer_count_ACH());
            header3p4.setText(headerlList.get(0).getActive_dealer_count_ACH_percent());
            header4p4.setText(headerlList.get(0).getActive_dealer_count_WGT_percent());
            header5p4.setText(headerlList.get(0).getActive_dealer_count_SCORE_percent());

            header3p5.setText(headerlList.get(0).getFive_percent());
            header4p5.setText(headerlList.get(0).getFive_WGT_percent());
            header5p5.setText(headerlList.get(0).getFive_SCORE_percent());

            header3p6.setText(headerlList.get(0).getSix_percent());
            header2p6.setText(headerlList.get(0).getSix_ACH());

            headerEarningScore.setText(headerlList.get(0).getEarning_score_percent());
            headerPenalty.setText(headerlList.get(0).getPenalty_percent());
            headerFinalScore.setText(headerlList.get(0).getFinal_score_percent());
            headerRemarks.setText(headerlList.get(0).getRemarks());


            header1p5.setText(headerlList.get(0).getFive_TGT());
            header2p5.setText(headerlList.get(0).getFive_ACH());
            header1p6.setText(headerlList.get(0).getSix_TGT());

            header1p3.setText(headerlList.get(0).getDealer_appointment_TGT());
        } catch (Exception ignored) {}
    }

    ContentValues values;
    AceDnsDatabase mAceDnsDatabase;

    public void commonNameValuePair() {
        values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
    }

    public void _DOWNLOAD_sis_details() {
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        commonNameValuePair();
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(BaseUrl.baseUrl + "sis-summary-details-bd.php?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode(), "");
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ BdSisActivity: " + BaseUrl.baseUrl + "sis-summary-details-bd.php?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode());
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ BdSisActivity result: " + httpResponse);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {

            long insertStatus = mAceDnsDatabase.insertToBdSisSummeryDetails(httpResponse);
            if (insertStatus != 1) {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (!PhoneStateChangeListener.ringing) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_bd_sis_header() {
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        commonNameValuePair();
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(BaseUrl.baseUrl + "sis-summary-header-bd.php?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode(), "");
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ BdSisActivity: " + BaseUrl.baseUrl + "sis-summary-header-bd.php?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode());
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ BdSisActivity result: " + httpResponse);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {

            long insertStatus = mAceDnsDatabase.insertToBdSisSummeryheader(httpResponse);
            if (insertStatus != 1) {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (!PhoneStateChangeListener.ringing) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

}