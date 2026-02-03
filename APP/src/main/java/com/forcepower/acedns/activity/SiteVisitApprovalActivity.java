package com.forcepower.acedns.activity;

import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.SiteLeadApprovalAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSiteLeadChangeRequestAproval;
import com.forcepower.acedns.bean.SiteLeadApproval;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DateTimeFormatter;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.Calendar;

public class SiteVisitApprovalActivity extends AceDnsParentActivity {

    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;
    public Context mContext;
    Button mButtonBack,btn_pending,btn_approved,btn_reject,buttonDateFrom,buttonDateTo,buttonDateSubmit;

    Handler mHandler;
    ProgressDialog loader;

    String httpResponse = "",status = "site_lead_approval",list_status="pending";

    ListView lvRoutPlanList;
    ArrayList<SiteLeadApproval> routeList = null;
    ArrayList<SiteLeadApproval> routeList11 = null;
    ArrayList<SiteLeadApproval> routeListPending = null;
    ArrayList<SiteLeadApproval> routeListApproved = null;
    ArrayList<SiteLeadApproval> routeListRejected = null;
    int noRows = -1, noColumn = -1;
    String timeStamp = "";

    int type = 0;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_site_visit_approval);

        mContext = SiteVisitApprovalActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setOnClickListener(SiteVisitApprovalActivity.this);

        btn_pending = (Button) findViewById(R.id.btn_pending);
        btn_pending.setOnClickListener(SiteVisitApprovalActivity.this);

        btn_approved = (Button) findViewById(R.id.btn_approved);
        btn_approved.setOnClickListener(SiteVisitApprovalActivity.this);

        btn_reject = (Button) findViewById(R.id.btn_reject);
        btn_reject.setOnClickListener(SiteVisitApprovalActivity.this);


        buttonDateFrom = (Button) findViewById(R.id.buttonDateFrom);
        buttonDateFrom.setOnClickListener(SiteVisitApprovalActivity.this);

        buttonDateTo = (Button) findViewById(R.id.buttonDateTo);
        buttonDateTo.setOnClickListener(SiteVisitApprovalActivity.this);

        buttonDateSubmit= (Button) findViewById(R.id.buttonDateSubmit);
        buttonDateSubmit.setOnClickListener(SiteVisitApprovalActivity.this);

        //lvRoutPlanList = findViewById(R.id.linearLayoutParent);
        lvRoutPlanList = findViewById(R.id.lvRoutPlanList);
        routeListApproved = new ArrayList<>();
        routeListRejected = new ArrayList<>();
        routeListPending = new ArrayList<>();


        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();



        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    SiteVisitApprovalActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            if(routeList.size()>0){
                                for(int i=0;i<routeList.size();i++){
                                    if(routeList.get(i).getStatus().equalsIgnoreCase("pending")){
                                        routeListPending.add(routeList.get(i));
                                    }
                                    if(routeList.get(i).getStatus().equalsIgnoreCase("approved")){
                                        routeListApproved.add(routeList.get(i));
                                    }
                                    if(routeList.get(i).getStatus().equalsIgnoreCase("rejected")){
                                        routeListRejected.add(routeList.get(i));
                                    }
                                }
                                show(routeListPending);
                                list_status="pending";
                                //btn_pending.setBackground(getDrawable(R.drawable.norm_btn_bg));
                            }else{
                                Toast.makeText(mContext, "No Data", Toast.LENGTH_SHORT).show();
                            }
                        }
                    });
                }
            }
        };


        getData();

    }

    public void onClick(View clkdView) {

        if (clkdView == mButtonBack) {
            finish();
        }

        if (clkdView == btn_approved) {
            show(routeListApproved);
            list_status="approved";
           // btn_approved.setBackground(Color);
        }

        if (clkdView == btn_pending) {
            show(routeListPending);
            list_status="pending";
        }
        if (clkdView == btn_reject) {
            show(routeListRejected);
            list_status="rejected";
        }
        if (clkdView == buttonDateFrom) {
            type=1;
            openDatePicker();
        }
        if (clkdView == buttonDateTo) {
            type=2;
            openDatePicker();
        }
        if (clkdView == buttonDateSubmit) {
            loader = new ProgressDialog(mContext);
            loader.setMessage("Fetching Data.Please wait..");
            loader.show();
            routeList = new ArrayList<SiteLeadApproval>();
            routeListApproved = new ArrayList<>();
            routeListRejected = new ArrayList<>();
            routeListPending = new ArrayList<>();

            getData();

        }


    }

    private void getData(){
        new Thread() {
            public void run() {

                _DOWNLOAD_SITE_LEAD_APPROVAL_MASTER();

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }

        }.start();
    }

    public void _DOWNLOAD_SITE_LEAD_APPROVAL_MASTER() {

        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.site_lead_download_approvalURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&from=" + buttonDateFrom.getText().toString()
                + "&to=" + buttonDateTo.getText().toString()
                + "&incremental_download=no";

Log.d("_DOWNLOAD_", "_DOWNLOAD_ SiteVisitApprovalActivity: " + URL);
        Download_txt(URL);

        File csvFile = new File(Utils.getAppStoragePath(mContext)+ status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }
        routeList = new ArrayList<SiteLeadApproval>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line = "";
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SiteLeadApproval temp = new SiteLeadApproval();
                        temp.setTran_id(RowData[0]);
                        temp.setCust_name(RowData[1]);
                        temp.setCust_phone(RowData[2]);
                        temp.setAddress(RowData[3]);
                        temp.setDealer_name(RowData[4]);
                        temp.setDelear_code(RowData[5]);
                        temp.setProduct(RowData[6]);
                        temp.setBag(RowData[7]);
                        temp.setRequest_date(RowData[8]);
                        temp.setStatus(RowData[10]);
                        temp.setActual_delivery_date(RowData[11]);
                        temp.setReson(RowData[13]);
                        temp.setRemarks(RowData[12]);
                        temp.setVisit_type(RowData[9]);

                        routeList.add(temp);
                        temp = null;
                    }
                }
            }

            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }


    }


    private void Download_txt(String URL) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        java.net.URL url = null;

        try {
            outputFile = new File(Utils.getAppStoragePath(mContext)+ status + ".txt");
            if (outputFile.exists())
                outputFile.delete();
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.setConnectTimeout(0);
            c.connect();
            is = c.getInputStream();
            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = is.read(buffer)) != -1) {
                fbo.write(buffer, 0, len1);
            }

            fbo.flush();

        } catch (Exception e) {
            e.printStackTrace();
        } finally {

            if (c != null)
                c.disconnect();
            if (fbo != null)
                try {
                    fbo.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            if (is != null)
                try {
                    is.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }

        }
    }

    private void show(ArrayList<SiteLeadApproval> expList1){
        SiteLeadApprovalAdapter adapter1 = new SiteLeadApprovalAdapter(SiteVisitApprovalActivity.this, R.layout.item_list_site_lead_approval, expList1);
        lvRoutPlanList.setAdapter(adapter1);

    }
    Button buttonDate;
    public void sendApproval(int i,String status,String tranID){
        routeList11 = new ArrayList<>();
        SiteLeadApproval temp = new SiteLeadApproval();
        if(status.equalsIgnoreCase("pending")){
            temp.setTran_id(routeListPending.get(i).getTran_id());
        }else
        if(status.equalsIgnoreCase("approved")){

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.site_lead_approval_remarks_date_layout);
            mDialogCustomer.setCancelable(false);
            TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
            title.setText("Input Details of Approval");
            buttonDate = mDialogCustomer.findViewById(R.id.buttonDate);
            Button buttonSubmit = mDialogCustomer.findViewById(R.id.buttonSubmit);

            EditText ed_input_r1 = mDialogCustomer.findViewById(R.id.ed_input_r1);
            EditText ed_input_r2 = mDialogCustomer.findViewById(R.id.ed_input_r2);
            TextView tv_reason = mDialogCustomer.findViewById(R.id.tv_reason);
            ed_input_r2.setVisibility(View.GONE);
            tv_reason.setVisibility(View.GONE);
            ImageView back = mDialogCustomer.findViewById(R.id.back);
            back.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    mDialogCustomer.cancel();
                }
            });
            buttonDate.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    type = 3;
                    openDatePicker();
                }
            });

            buttonSubmit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if(buttonDate.getText().toString().contains("/")){
                        if(ed_input_r1.getText().toString().length()>0){
                            temp.setStatus(status);
                            temp.setTran_id(tranID);
                            temp.setActual_delivery_date(buttonDate.getText().toString());
                            temp.setRemarks(ed_input_r1.getText().toString());
                            temp.setReson(ed_input_r2.getText().toString());
                            routeList11.add(temp);
                            new TRANS_SubmitSiteLeadChangeRequestAproval(SiteVisitApprovalActivity.this, true, "SUBMIT",routeList11).execute();
                            mDialogCustomer.cancel();
                        }else{
                            Utils.showToast(mContext,"Provide Remarks");
                        }
                    }else{
                        Utils.showToast(mContext,"Select Actual Delivery Date");
                    }

                }
            });

            mDialogCustomer.show();
            //temp.setTran_id(routeListApproved.get(i).getTran_id());
        }else if(status.equalsIgnoreCase("rejected")){

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.site_lead_approval_remarks_date_layout);
            mDialogCustomer.setCancelable(false);
            TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
            title.setText("Input Details of Rejection");
            buttonDate = mDialogCustomer.findViewById(R.id.buttonDate);
            Button buttonSubmit = mDialogCustomer.findViewById(R.id.buttonSubmit);
            ImageView back = mDialogCustomer.findViewById(R.id.back);

            EditText ed_input_r1 = mDialogCustomer.findViewById(R.id.ed_input_r1);
            EditText ed_input_r2 = mDialogCustomer.findViewById(R.id.ed_input_r2);
            back.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    mDialogCustomer.cancel();
                }
            });

            buttonDate.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    type=3;
                    openDatePicker();
                }
            });
            buttonDate.setVisibility(View.GONE);
            buttonSubmit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if(ed_input_r1.getText().toString().length()>0 && ed_input_r2.getText().toString().length()>0){
                        temp.setStatus(status);
                        temp.setTran_id(tranID);
                        temp.setActual_delivery_date("");
                        temp.setRemarks(ed_input_r1.getText().toString());
                        temp.setReson(ed_input_r2.getText().toString());
                        routeList11.add(temp);
                        new TRANS_SubmitSiteLeadChangeRequestAproval(SiteVisitApprovalActivity.this, true, "SUBMIT",routeList11).execute();
                        mDialogCustomer.cancel();
                    }else{
                        Utils.showToast(mContext,"Provide Rejection Remarks and Reason");
                    }

                }
            });

            mDialogCustomer.show();
            //temp.setTran_id(routeListRejected.get(i).getTran_id());
        }else{

        }
   /*     temp.setStatus(status);

        routeList11.add(temp);

        new TRANS_SubmitSiteLeadChangeRequestAproval(SiteVisitApprovalActivity.this, true, "SUBMIT",routeList11).execute();
   */ }

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
                        chosenDateOfOrder[0] = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
                        chosenDateOfOrder[0] = new DateTimeFormatter().changeDateFormat("d/M/yyyy", chosenDateOfOrder[0], "yyyyMMdd");

                        //date_s[0]);
                        if(type==1)
                        {
                            buttonDateFrom.setText("" + new DateTimeFormatter().changeDateFormat("yyyyMMdd", chosenDateOfOrder[0], "dd/MM/yyyy"));
                        }else if(type==2)
                        {
                            buttonDateTo.setText("" + new DateTimeFormatter().changeDateFormat("yyyyMMdd", chosenDateOfOrder[0], "dd/MM/yyyy"));
                        }else{
                            buttonDate.setText("" + new DateTimeFormatter().changeDateFormat("yyyyMMdd", chosenDateOfOrder[0], "dd/MM/yyyy"));
                        }
                    }
                }, mYear, mMonth, mDay);
        datePickerDialog.setPermanentTitle("Select order date...");
        datePickerDialog.show();


        //datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis());
        if(type==1)
        {
            datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());
            c.add(Calendar.DAY_OF_MONTH, -30); // add date to 30 days later
            datePickerDialog.getDatePicker().setMinDate(c.getTimeInMillis());
            //datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis()- (30 * 24 * 60 * 60 * 1000));
        }else if(type==2)
        {
            datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());
            c.add(Calendar.DAY_OF_MONTH, -30); // add date to 30 days later
            datePickerDialog.getDatePicker().setMinDate(c.getTimeInMillis());
            //datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis()- (30 * 24 * 60 * 60 * 1000));
        }else{
            datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());
        }

        return date_s[0];

    }

}