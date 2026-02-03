package com.forcepower.acedns.activity;

import android.app.DatePickerDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;


import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.DashboardData;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.DateTimeFormatter;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class DashboardActivity extends AceDnsParentActivity {

    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;
    public Context mContext;
    Button mButtonBack,btn_pending,btn_approved,btn_reject,buttonDateFrom,buttonDateTo,buttonDateSubmit;

    Handler mHandler;
    ProgressDialog loader;
    int noRows = -1, noColumn = -1;

    public Handler mReportHandler;
    public Handler mHandlerPrepareSaudaData;
    public ProgressDialog mProgressDialogPrepareData;
    String httpResponse = "";

    ListView lvRoutPlanList;
    ArrayList<DashboardData> routeList = null;
    int type=1;
    Spinner sp_emp;
    private ArrayList<String> emp;
    //AceDnsDatabase mAceDnsDatabase;

    TextView txtCounterMeet,txtMegaMasonMeet,txtEngMeet,txtProMeet,txtConMeet,txtdealerSubdealerMeet,txtComplaint,txtMasonMeet,txtIHBMeet,txtSmallEngineerMeet,txtBigContractorMeet,txtCatchYoung,txtPctraining,txtTgt3,attendance_time,chk_out_time,txtDhalaiServices,txtComplaintReport,txtSiteVisit,txtSiteTracking;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        //requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dashboard);

        mContext = DashboardActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setOnClickListener(DashboardActivity.this);


        buttonDateSubmit = (Button) findViewById(R.id.buttonDateSubmit);
        buttonDateSubmit.setOnClickListener(DashboardActivity.this);

        buttonDateFrom = (Button) findViewById(R.id.buttonDateFrom);
        buttonDateFrom.setOnClickListener(DashboardActivity.this);
        lvRoutPlanList = findViewById(R.id.lvRoutPlanList);
        sp_emp = findViewById(R.id.sp_emp);

        txtCounterMeet = findViewById(R.id.txtCounterMeet);
        txtCatchYoung = findViewById(R.id.txtCatchYoung);
        txtMegaMasonMeet = findViewById(R.id.txtMegaMasonMeet);
        txtEngMeet = findViewById(R.id.txtEngMeet);
        txtProMeet = findViewById(R.id.txtProMeet);
        txtConMeet = findViewById(R.id.txtConMeet);
        txtdealerSubdealerMeet = findViewById(R.id.txtdealerSubdealerMeet);
        txtComplaint = findViewById(R.id.txtComplaint);
        txtMasonMeet = findViewById(R.id.txtMasonMeet);
        txtIHBMeet = findViewById(R.id.txtIHBMeet);
        txtSmallEngineerMeet = findViewById(R.id.txtSmallEngineerMeet);
        txtBigContractorMeet = findViewById(R.id.txtBigContractorMeet);
        txtPctraining = findViewById(R.id.txtPctraining);
        txtTgt3 = findViewById(R.id.txtTgt3);
        attendance_time = findViewById(R.id.attendance_time);
        chk_out_time = findViewById(R.id.chk_out_time);
        txtComplaintReport = findViewById(R.id.txtComplaintReport);
        txtDhalaiServices = findViewById(R.id.txtDhalaiServices);
        txtSiteVisit = findViewById(R.id.txtSiteVisit);
        txtSiteTracking = findViewById(R.id.txtSiteTracking);


        mReportHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mProgressDialogPrepareData.cancel();
                final int dojob = threadmsg.getData().getInt("JOB");
                DashboardActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                try{
                                    setSpiner();
                                    //setData(spdate.getSelectedItem().toString(),"c");
                                    //PrepareCustomerData(1);

                                }catch (Exception e){

                                }

                                break;

                            case 2:
                                try{


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
                        new commonAsyncTaskMaster(mContext, "dashboard_data_download");
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

    public void onClick(View clkdView) {

        if (clkdView == mButtonBack) {
            finish();
        }
        if (clkdView == buttonDateFrom) {
            type=1;
            openDatePicker();
        }
        if (clkdView == buttonDateSubmit) {
            if(buttonDateFrom.getText().toString().equalsIgnoreCase("Select Date")){
                Utils.showToast(mContext, "Please Select Date");
            }else{
                show();
            }

        }
    }

    private String openDatePicker() {
        int mYear, mMonth, mDay;
        final String[] chosenDateOfOrder = {""};
        final String[] date_s = {""};
        // Get Current Date
        final Calendar c = Calendar.getInstance();
        mYear = c.get(Calendar.YEAR);
        mMonth = c.get(Calendar.MONTH);
        mDay = c.get(Calendar.DAY_OF_MONTH);
        //launch datepicker modal
        Utils.CustomDatePickerDialogWithPermanentTitle datePickerDialog = new Utils.CustomDatePickerDialogWithPermanentTitle(this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
                        chosenDateOfOrder[0] =  year+ "-" + (monthOfYear + 1) + "-" + dayOfMonth;
                        chosenDateOfOrder[0] = new DateTimeFormatter().changeDateFormat("yyyy-M-d", chosenDateOfOrder[0], "yyyyMMdd");

                        //date_s[0]);n
                        if(type==1)
                        {
                            buttonDateFrom.setText("" + new DateTimeFormatter().changeDateFormat("yyyyMMdd", chosenDateOfOrder[0], "yyyy-MM-dd"));
                        }
                    }
                }, mYear, mMonth, mDay);
        datePickerDialog.setPermanentTitle("Select order date...");
        datePickerDialog.show();

        long miliSecsDate = milliseconds ("2024-07-21");
        datePickerDialog.getDatePicker().setMinDate(miliSecsDate);
        if(type==1)
        {
            //datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());
            //c.add(Calendar.DAY_OF_MONTH, -30); // add date to 30 days later
            //datePickerDialog.getDatePicker().setMinDate(c.getTimeInMillis());
            //datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis()- (30 * 24 * 60 * 60 * 1000));
        }

        return date_s[0];

    }

    private void show() {
       // SiteLeadApprovalAdapter adapter1 = new SiteLeadApprovalAdapter(DashboardActivity.this, R.layout.item_list_site_lead_approval, expList1);
       // lvRoutPlanList.setAdapter(adapter1);
        //emp = mAceDnsDatabase.getDashEmpData();
        DashboardData data = mAceDnsDatabase.getDashEmpData(sp_emp.getSelectedItem().toString(),buttonDateFrom.getText().toString());

        attendance_time.setText(data.getAtt_time());
        chk_out_time.setText(data.getChk_out_time());
        txtCatchYoung.setText(data.getCatch_them_young());
        txtMegaMasonMeet.setText(data.getMega_mason_meet());
        txtEngMeet.setText(data.getEngineers_meet());
        txtProMeet.setText(data.getProfessional_meet());
        txtConMeet.setText(data.getContractor_meet());

        txtdealerSubdealerMeet.setText(data.getDealer_subdealer_meet());
        txtComplaint.setText(data.getComplaint());
        txtMasonMeet.setText(data.getMason_meet());
        txtIHBMeet.setText(data.getIHB_meet());
        txtSmallEngineerMeet.setText(data.getSmall_engineers_meet());
        txtBigContractorMeet.setText(data.getBig_contractor_meet());
        txtPctraining.setText(data.getPc_traning_programme());
        txtTgt3.setText(data.getCustomer_guidance_camp());
        txtCounterMeet.setText(data.getCounter_meet());
        txtDhalaiServices.setText(data.getDhalai_service());
        txtComplaintReport.setText(data.getComplaint_report());
        txtSiteVisit.setText(data.getSite_visit());
        txtSiteTracking.setText(data.getSiteTracking());


    }

    private void setSpiner(){
        emp = mAceDnsDatabase.getDashEmp();
        ArrayAdapter ad
                = new ArrayAdapter(
                this,
                android.R.layout.simple_spinner_item,
                emp);

        ad.setDropDownViewResource(
                android.R.layout
                        .simple_spinner_dropdown_item);

        sp_emp.setAdapter(ad);
    }

    public long milliseconds(String date)
    {
        //String date_ = date;
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        try
        {
            Date mDate = sdf.parse(date);
            long timeInMilliseconds = mDate.getTime();
            System.out.println("Date in milli :: " + timeInMilliseconds);
            return timeInMilliseconds;
        }
        catch (ParseException e)
        {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }

        return 0;
    }

}