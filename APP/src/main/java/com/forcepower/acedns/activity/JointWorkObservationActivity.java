package com.forcepower.acedns.activity;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Spinner;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitJointWorkObservationTask;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.dateString;
import static com.forcepower.acedns.constants.Constants.selectedCustomer;
import static com.forcepower.acedns.constants.Constants.selectedEmp;


public class JointWorkObservationActivity extends AceDnsParentActivity {
    public String mRouteName = "";
    public String mRouteCode = "";
    LinearLayout routeLayout,customerLayout;
    public Boolean isCustomerChosen = false, isEmpChosen = false,isRouteChosen=false;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<EmployeeMasterDetails> EmployeeMasterDetailsList;
    ArrayList<RoutePlanMasterDetails> RoutePlanMasterDetailsList;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    EditText observationOnEmployeeTV,observationOnCustomerTV;
    ImageView imgLogo;
    Handler mHandler;
    ProgressDialog loader;
    Spinner customerSpinner,employeeSpinner,routeSpinner;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.joint_work_observation);
        mContext = this;
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        routeLayout = findViewById(R.id.routeLayout);
        customerLayout = findViewById(R.id.customerLayout);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));

        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        observationOnEmployeeTV =  findViewById(R.id.observationOnEmployeeTV);
        observationOnCustomerTV =  findViewById(R.id.observationOnCustomerTV);
        employeeSpinner =  findViewById(R.id.employeeSpinner);
        routeSpinner =  findViewById(R.id.routeSpinner);
        customerSpinner =  findViewById(R.id.customerSpinner);

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    JointWorkObservationActivity.this.runOnUiThread(new Runnable() {
                        public void run()
                        {
                            new TRANS_SubmitJointWorkObservationTask(mContext, true).execute();
                        }
                    });


                }
            }
        };
        showEmpList();
    }
    public void closeActivity(View v)
    {
        finish();
    }
    public void submitObservation(View v)
    {
        if(isEmpChosen && isRouteChosen && isCustomerChosen)
        {
            new GPSTracker(mContext);
            loader = new ProgressDialog(mContext);
            loader.setMessage("Saving Data.Please wait..");
            loader.show();
            new Thread()
            {
                public void run()
                {
                    String timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    String observationOnEmployee=observationOnEmployeeTV.getText().toString();
                    String observationOnCustomer=observationOnCustomerTV.getText().toString();
                    String emp=selectedEmp.getEmpCode();
                    String route=selectedCustomer.getRouteCode();
                    String customer=selectedCustomer.getCustomerCode();
                    mAceDnsTransactionDatabase.insertToJointWorkObservationTable("JW", timeStamp, customer, emp, route, observationOnCustomer, observationOnEmployee);
                    mAceDnsTransactionDatabase.insertToLocationTable("JW", timeStamp);
                    Message msgObj = mHandler.obtainMessage();
                    Bundle b = new Bundle();
                    b.putString("message", "SubmitJobDone");
                    msgObj.setData(b);
                    mHandler.sendMessage(msgObj);
                }

            }.start();


        }
        else
        {
            if(!isEmpChosen)
            {
                Utils.showToast(mContext,"Need to select employee.");
            }
            else if(!isRouteChosen)
            {
                Utils.showToast(mContext,"Proper route not found.");
            }
            else
            {
                Utils.showToast(mContext,"Proper customer not found.");
            }
        }
    }

    private void showEmpList()
    {
        EmployeeMasterDetailsList = mAceDnsDatabase.geJointWorkObservationEmpList();
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < EmployeeMasterDetailsList.size(); i++) {
            spinnerArray.add(EmployeeMasterDetailsList.get(i).getEmpName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        employeeSpinner.setAdapter(spinnerArrayAdapter);
        if (!EmployeeMasterDetailsList.isEmpty())
            employeeSpinner.setSelection(0);

        employeeSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
            {
                if(i==0)
                {
                    isEmpChosen=false;
                    routeLayout.setVisibility(View.GONE);
                    customerLayout.setVisibility(View.GONE);
                }
                else
                {
                    Constants.selectedEmp = EmployeeMasterDetailsList.get(i);
                    observationOnEmployeeTV.setHint("Observation on "+Constants.selectedEmp.getEmpName());
                    isEmpChosen=true;
                    getRouteList();
                }

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    private void getRouteList()
    {
        RoutePlanMasterDetailsList = mAceDnsDatabase.geJointWorkObservationRouteListByEmp();
        if(RoutePlanMasterDetailsList.size()>0)
        {
            routeLayout.setVisibility(View.VISIBLE);
            final ArrayList<String> spinnerArray = new ArrayList<>();
            for (int i = 0; i < RoutePlanMasterDetailsList.size(); i++) {
                spinnerArray.add(RoutePlanMasterDetailsList.get(i).getRouteName());
            }
            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            routeSpinner.setAdapter(spinnerArrayAdapter);
            if (!RoutePlanMasterDetailsList.isEmpty())
                routeSpinner.setSelection(0);

            routeSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
                {
                    Constants.selectedRoute = RoutePlanMasterDetailsList.get(i);
                    isRouteChosen=true;
                    geCustomerList();

                }

                @Override
                public void onNothingSelected(AdapterView<?> adapterView) {

                }
            });
        }
        else
        {
            routeLayout.setVisibility(View.GONE);
            isRouteChosen=false;
        }

    }

    private void geCustomerList()
    {
        mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRouteForJointWorkObservation(Constants.selectedRoute.getRoutecode());
        if(mCustomerDetailsList.size()>0)
        {
            customerLayout.setVisibility(View.VISIBLE);
            final ArrayList<String> spinnerArray = new ArrayList<>();
            for (int i = 0; i < mCustomerDetailsList.size(); i++) {
                spinnerArray.add(mCustomerDetailsList.get(i).getCustomerName());
            }
            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            customerSpinner.setAdapter(spinnerArrayAdapter);
            if (!mCustomerDetailsList.isEmpty())
                customerSpinner.setSelection(0);

            customerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
                {
                    Constants.selectedCustomer = mCustomerDetailsList.get(i);
                    observationOnCustomerTV.setHint("Observation on "+Constants.selectedCustomer.getCustomerName());
                    isCustomerChosen=true;

                }

                @Override
                public void onNothingSelected(AdapterView<?> adapterView) {

                }
            });
        }
        else
        {
            customerLayout.setVisibility(View.GONE);
            isCustomerChosen=false;
        }

    }


    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }
    }

}
