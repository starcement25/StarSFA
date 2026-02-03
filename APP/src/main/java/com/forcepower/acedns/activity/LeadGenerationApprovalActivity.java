package com.forcepower.acedns.activity;

import static android.view.View.GONE;

import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CommonTwoDataAdapter;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.LeadGenerationListAdapter;
import com.forcepower.acedns.adapter.SiteLeadApprovalAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSiteLeadChangeRequestAproval;
import com.forcepower.acedns.bean.CommonHelper;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.SiteLeadApproval;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DateTimeFormatter;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

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

public class LeadGenerationApprovalActivity extends AceDnsParentActivity {


    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;
    public Context mContext;
    Button mButtonBack,btn_pending,btn_approved,btn_reject,btn_new,buttonDateFrom,buttonDateTo,buttonDateSubmit,btnEmp;

    Handler mHandler;
    ProgressDialog loader;

    String httpResponse = "",status = "site_lead_approval",list_status="pending";

    ListView lvRoutPlanList;
    ArrayList<CommonHelper> routeList = null;

    int noRows = -1, noColumn = -1;
    String timeStamp = "",action_on_lead = "new",selected_emp="";

    int type = 0;
    LeadGenerationListAdapter adapter1;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_generation_approval);


        mContext = LeadGenerationApprovalActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setOnClickListener(LeadGenerationApprovalActivity.this);

        btn_pending = (Button) findViewById(R.id.btn_pending);
        btn_pending.setOnClickListener(LeadGenerationApprovalActivity.this);

        btn_new = (Button) findViewById(R.id.btn_new);
        btn_new.setOnClickListener(LeadGenerationApprovalActivity.this);

        btn_approved = (Button) findViewById(R.id.btn_approved);
        btn_approved.setOnClickListener(LeadGenerationApprovalActivity.this);

        btn_reject = (Button) findViewById(R.id.btn_reject);
        btn_reject.setOnClickListener(LeadGenerationApprovalActivity.this);


        buttonDateFrom = (Button) findViewById(R.id.buttonDateFrom);
        buttonDateFrom.setOnClickListener(LeadGenerationApprovalActivity.this);

        buttonDateTo = (Button) findViewById(R.id.buttonDateTo);
        buttonDateTo.setOnClickListener(LeadGenerationApprovalActivity.this);

        buttonDateSubmit= (Button) findViewById(R.id.buttonDateSubmit);
        buttonDateSubmit.setOnClickListener(LeadGenerationApprovalActivity.this);

        btnEmp= (Button) findViewById(R.id.btnEmp);
        btnEmp.setOnClickListener(LeadGenerationApprovalActivity.this);



        //lvRoutPlanList = findViewById(R.id.linearLayoutParent);
        lvRoutPlanList = findViewById(R.id.lvRoutPlanList);

        routeList = new ArrayList<CommonHelper>();


        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();



        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    LeadGenerationApprovalActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            if(routeList.size()>0){
                                show(routeList);
                                list_status="pending";
                                //btn_pending.setBackground(getDrawable(R.drawable.norm_btn_bg));
                            }else{
                                try {
                                    routeList = new ArrayList<>();
                                    show(routeList);
                                    adapter1.notifyDataSetChanged();
                                }catch (Exception e){}

                                Toast.makeText(mContext, "No Data", Toast.LENGTH_SHORT).show();
                            }
                        }
                    });
                }
                if (aResponse.equalsIgnoreCase("JobDoneEmp")) {
                    loader.cancel();
                    LeadGenerationApprovalActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            if(routeList.size()>0){
                                chooseCustomerDialogStar(routeList);
                                //show(routeList);
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
        if (clkdView == btnEmp) {
            action_on_lead="new";

            routeList = new ArrayList<>();
            getDataEmp();
            //routeList = mAceDnsDatabase.getEmpNameNT();


        }
        if (clkdView == btn_new) {
            action_on_lead="new";
            loader = new ProgressDialog(mContext);
            loader.setMessage("Fetching Data.Please wait..");
            loader.show();
            routeList = new ArrayList<CommonHelper>();

            getData();

        }
        if (clkdView == btn_approved) {

            action_on_lead="Revision";
            loader = new ProgressDialog(mContext);
            loader.setMessage("Fetching Data.Please wait..");
            loader.show();
            routeList = new ArrayList<CommonHelper>();


            getData();
        }

        if (clkdView == btn_pending) {

            action_on_lead="Accept";
            loader = new ProgressDialog(mContext);
            loader.setMessage("Fetching Data.Please wait..");
            loader.show();
            routeList = new ArrayList<CommonHelper>();


            getData();
        }
        if (clkdView == btn_reject) {

            action_on_lead="Closed";
            loader = new ProgressDialog(mContext);
            loader.setMessage("Fetching Data.Please wait..");
            loader.show();
            routeList = new ArrayList<CommonHelper>();


            getData();
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
            routeList = new ArrayList<CommonHelper>();


            getData();

        }


    }

    @Override
    public void onRestart() {
        super.onRestart();
        routeList = new ArrayList<>();
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();
        routeList = new ArrayList<CommonHelper>();
        getData();
    }
    private void getData(){
        new Thread() {
            public void run() {

                _DOWNLOAD_LEAD_GENERATION_APPROVAL_MASTER();

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }

        }.start();
    }

    private void getDataEmp(){
        new Thread() {
            public void run() {

                //_DOWNLOAD_LEAD_GENERATION_APPROVAL_MASTER();
                _DOWNLOAD_LEAD_GENERATION_EMP();

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDoneEmp");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }

        }.start();
    }

    public void _DOWNLOAD_LEAD_GENERATION_APPROVAL_MASTER() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.lead_generation_download_approvalURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&from=" + buttonDateFrom.getText().toString()
                + "&to=" + buttonDateTo.getText().toString()
                + "&incremental_download=no"
                + "&selected_emp="+selected_emp
                + "&action_on_lead="+action_on_lead;
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(URL, "");
Log.d("_DOWNLOAD_", "_DOWNLOAD_ LeadGenerationApprovalActivity: " + URL);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ LeadGenerationApprovalActivity result: " + httpResponse);
        if (httpResponse.length() > 0 && !httpResponse.equalsIgnoreCase("Network Failure")) {

            if (httpResponse != null) {
                JSONObject obj = null;
                try {
                    obj = new JSONObject(httpResponse);
                    obj.getString("process_status");

                    if(obj.getString("process_status").equals("YES")){
                        String row_count = obj.getString("countrows");
                        JSONArray dataArray  = obj.getJSONArray("datavalue");
                        if(dataArray.length() == Integer.parseInt(row_count)){
                            for (int i = 0; i < dataArray.length(); i++) {
                                CommonHelper c = new CommonHelper();
                                JSONObject dataobj = dataArray.getJSONObject(i);
                                 c.setItem1(dataobj.getString("emp_name"));
                                c.setItem2(dataobj.getString("party_name"));
                                c.setItem3(dataobj.getString("month_year"));
                                c.setItem4(dataobj.getString("lead_generation_id"));
                                c.setItem5(dataobj.getString("next_visit_date"));
                                c.setItem6(dataobj.getString("category_type_construction"));
                                c.setItem7(dataobj.getString("lead_status"));
                                c.setItem8(dataobj.getString("s_id"));

                                routeList.add(c);
                            }

                        }
                    }
                } catch (JSONException e) {
                    throw new RuntimeException(e);
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


    public void _DOWNLOAD_LEAD_GENERATION_EMP() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.emp_for_lead_generation_download_approvalURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(URL, "");
Log.d("_DOWNLOAD_", "_DOWNLOAD_ LeadGenerationApprovalActivity: " + URL);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ LeadGenerationApprovalActivity result: " + httpResponse);
        if (httpResponse.length() > 0 && !httpResponse.equalsIgnoreCase("Network Failure")) {

            if (httpResponse != null) {
                JSONObject obj = null;
                try {
                    obj = new JSONObject(httpResponse);
                    obj.getString("process_status");

                    if(obj.getString("process_status").equals("YES")){
                        String row_count = obj.getString("countrows");
                        JSONArray dataArray  = obj.getJSONArray("datavalue");
                        if(dataArray.length() == Integer.parseInt(row_count)){
                            for (int i = 0; i < dataArray.length(); i++) {
                                CommonHelper c = new CommonHelper();
                                JSONObject dataobj = dataArray.getJSONObject(i);
                                c.setItem1(dataobj.getString("emp_name"));
                                c.setItem2(dataobj.getString("emp_code"));

                                routeList.add(c);
                            }

                        }
                    }
                } catch (JSONException e) {
                    throw new RuntimeException(e);
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


    private void show(ArrayList<CommonHelper> expList1){
        adapter1 = new LeadGenerationListAdapter(LeadGenerationApprovalActivity.this, R.layout.item_list_lg_for, expList1);
        lvRoutPlanList.setAdapter(adapter1);

    }
    Button buttonDate;

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

    public void chooseCustomerDialogStar(ArrayList<CommonHelper> _customerList) {
        ArrayList<CommonHelper> customerList = _customerList;

        //JsonsReceiver.getStarSaathiLedgerCust(mContext);
        if (customerList != null && customerList.size() > 0) {
            final CommonTwoDataAdapter adapterCust = new CommonTwoDataAdapter(
                    LeadGenerationApprovalActivity.this, R.layout.multiple_cust_child,
                    customerList);
            final Dialog custDialog = new Dialog(LeadGenerationApprovalActivity.this,
                    R.style.PauseDialog);
            custDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            custDialog.setContentView(R.layout.select_multiple_from_list);
            custDialog.setCancelable(false);
            TextView title = (TextView) custDialog.findViewById(R.id.title);
            title.setText("Please select party");
            final ListView dialogList = (ListView) custDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_SINGLE);
            dialogList.setAdapter(adapterCust);
            RelativeLayout chkAllLayout = (RelativeLayout) custDialog
                    .findViewById(R.id.select_all_layout);
            chkAllLayout.setVisibility(View.GONE);
            final CheckBox chkSelectAll = (CheckBox) custDialog
                    .findViewById(R.id.chk_all);
            chkSelectAll.setVisibility(GONE);

            Button submit = (Button) custDialog.findViewById(R.id.button1);
            submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    String selectedCodes = "";
                    String selectedNames = "";
                    String cdns = "";

                    final SparseBooleanArray checkedItems = dialogList
                            .getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount == 1) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                CommonHelper detailsObj = adapterCust
                                        .getItem(position);
                                String selectedName = detailsObj
                                        .getItem1();
                                String selectedCode = detailsObj
                                        .getItem2();
                                cdns = detailsObj.getItem2();
                                selectedCodes = selectedCode;// + "'"
                                //       + selectedCode + "',";
                                selectedNames = selectedNames
                                        + selectedName + ",";
                            }
                        }
                        // selectedCodes = selectedCodes.substring(0,
                        //         selectedCodes.length() - 1);
                        selectedNames = selectedNames.substring(0,
                                selectedNames.length() - 1);
                        selected_emp = selectedCodes;
                       // Utils.showToast(mContext,"You1 --" + selectedCodes);
                       /* Intent intent = new Intent(mContext, StarOutStandingActivity.class);
                        intent.putExtra("cid", selectedCodes);
                        intent.putExtra("cdnsid", cdns);
                        startActivity(intent);*/
                        //prepareOutstandingData(selectedCodes);
                        btnEmp.setText(""+selectedNames);
                        routeList = new ArrayList<>();
                        loader = new ProgressDialog(mContext);
                        loader.setMessage("Fetching Data.Please wait..");
                        loader.show();
                        routeList = new ArrayList<CommonHelper>();
                        getData();
                        custDialog.cancel();
                    } else {
                        custDialog.cancel();
                        Utils.showToast(LeadGenerationApprovalActivity.this,
                                "Please select an option");
                    }


                }
            });

            EditText searchText = (EditText) custDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    adapterCust.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {

                }
            });

            custDialog.show();
        } else {
            Utils.showToast(LeadGenerationApprovalActivity.this, "No Employee Found");
        }
    }



}