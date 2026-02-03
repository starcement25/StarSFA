package com.forcepower.acedns.activity;

import androidx.fragment.app.FragmentActivity;

import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.TargetRouteWiseLandingAdapter;
import com.forcepower.acedns.bean.TargetAchievementRouteCategorywise;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;
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
import java.util.List;

public class TargetAchieveLandingActivity extends FragmentActivity {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase mAceDnsDatabase;
    String httpResponse = "",status = "target_achievement_route_categorywise";
    ContentValues values;
    int noRows = -1, noColumn = -1;
    String timeStamp = "";
    AceDnsDatabase dbHelper;
    ConnectionDetector cd;

    ListView lvTargetList;
    ArrayList<TargetAchievementRouteCategorywise> routeList = null;
    ArrayList<TargetAchievementRouteCategorywise> routeList11 = null;
    ArrayList<TargetAchievementRouteCategorywise> customerList = new ArrayList<>();

    Handler mHandler;
    ProgressDialog loader;

    List<String> month = new ArrayList<String>();
    List<String> emp = new ArrayList<String>();
    Spinner monthSpinner,spCustomer;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_target_achieve_landing);
        mContext = TargetAchieveLandingActivity.this;
        dbHelper = new AceDnsDatabase(mContext);

        lvTargetList = (ListView) findViewById(R.id.lvRoutPlanList);
        monthSpinner = (Spinner) findViewById(R.id.spdatemonth);
        spCustomer = (Spinner) findViewById(R.id.spCustomer);


        month.add("Jan");month.add("Feb");month.add("Mar");month.add("April");month.add("May");month.add("Jun");
        month.add("July");month.add("Aug");month.add("Sept");month.add("Oct");month.add("Nov");month.add("Dec");
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(this, android.R.layout.simple_spinner_item, month);
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        monthSpinner.setAdapter(dataAdapter);

        Button back = findViewById(R.id.back);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        Button submit = findViewById(R.id.submit);
        submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                getDataFromDB();
            }
        });

        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        cd = new ConnectionDetector(mContext);
        if (cd.isConnectingToInternet()) {
            loader.show();
        }

        routeList = new ArrayList<>();

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    TargetAchieveLandingActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            if(customerList.size()>0){
                                getDataFromDB();
                                setCust();
                                //show(routeList);
                            }else{
                                Toast.makeText(mContext, "No Data", Toast.LENGTH_SHORT).show();
                            }
                        }
                    });
                }
            }
        };

        cd = new ConnectionDetector(mContext);
        if (cd.isConnectingToInternet()) {
            getData();
            emp = dbHelper.getSelf_appraisal_cust();
            ArrayAdapter<String> dataAdapteremp = new ArrayAdapter<String>(this, android.R.layout.simple_spinner_item, emp);
            dataAdapteremp.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            spCustomer.setAdapter(dataAdapteremp);
        }else {
            loader.cancel();

           emp = dbHelper.getSelf_appraisal_cust();

            ArrayAdapter<String> dataAdapteremp = new ArrayAdapter<String>(this, android.R.layout.simple_spinner_item, emp);
            dataAdapteremp.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            spCustomer.setAdapter(dataAdapteremp);

            getDataFromDB();
        }


        spCustomer.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                routeList = new ArrayList<>();
                getDataFromDB();
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(TargetAchieveLandingActivity.this) + "~"
                + Utils.getDBVersion(TargetAchieveLandingActivity.this));


    }

    private void getData(){
        new Thread() {
            public void run() {

                DOWNLOAD_target_achievement_route_categorywise();



                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }

        }.start();
    }


    public void DOWNLOAD_target_achievement_route_categorywise() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.targetAchievementDetailsRouteWise
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
Log.d("_DOWNLOAD_", "_DOWNLOAD_ TargetAchieveLandingActivity: " + URL);
        Download_txt(URL);

        File csvFile = new File(Utils.getAppStoragePath(mContext)+ status + ".txt");
        customerList = new ArrayList<>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }
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
                        TargetAchievementRouteCategorywise temp = new TargetAchievementRouteCategorywise();
                        temp.setEmp_code(RowData[0]);
                        temp.setEmp_name(RowData[1]);
                        temp.setRoute_code(RowData[2]);
                        temp.setRoute_name(RowData[3]);
                        temp.setRoute_no(RowData[4]);
                        temp.setArea(RowData[5]);
                        temp.setDTS_target(RowData[6]);
                        temp.setDTS_achievement(RowData[7]);
                        temp.setDW_achievement(RowData[8]);
                        temp.setDW_target(RowData[9]);
                        temp.setOthers_target(RowData[10]);
                        temp.setOthers_achievement(RowData[11]);
                        temp.setMonth((RowData[12]));
                        customerList.add(temp);
                        //routeList.add(temp);
                    }
                }

            }
            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        Log.i("target", "achieve " + noRows
                + " records for \n Customer Details..");
        long insertStatus = dbHelper.insertToSelf_appraisal_route_product_group_wise(customerList);

        if (insertStatus == noRows && noRows > 0) {
            Constants.isTargetTableUpdated = false;

            //decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new commonAsyncTaskMaster(mContext, "target_achievement_route_categorywise");
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isTargetTableUpdated = false;
            //decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
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


    private void show(ArrayList<TargetAchievementRouteCategorywise> expList1){

        TargetRouteWiseLandingAdapter adapter1 = new TargetRouteWiseLandingAdapter(TargetAchieveLandingActivity.this, R.layout.targer_achieve_landing_list_item, expList1);
        lvTargetList.setAdapter(adapter1);


    }

    private void getDataFromDB(){
        //routeList = new ArrayList<>();
        String m = "";
        String mm = String.valueOf(monthSpinner.getSelectedItem());
        if(mm.matches("Jan")){
            m="01";
        }else if(mm.matches("Feb")){
            m="02";
        }
        else if(mm.matches("Mar")){
            m="03";
        }
        else if(mm.matches("April")){
            m="04";
        }
        else if(mm.matches("May")){
            m="05";
        }
        else if(mm.matches("Jun")){
            m="06";
        }
        else if(mm.matches("July")){
            m="07";
        }
        else if(mm.matches("Aug")){
            m="08";
        }
        else if(mm.matches("Sept")){
            m="09";
        }
        else if(mm.matches("Oct")){
            m="10";
        }
        else if(mm.matches("Nov")){
            m="11";
        }else if(mm.matches("Dec")){
            m="12";
        }
        routeList = dbHelper.getSelf_appraisal_route_product_group_wise(""+m,""+String.valueOf(spCustomer.getSelectedItem()));

        show(routeList);

    }

    private void setCust(){
        emp = dbHelper.getSelf_appraisal_cust();
        ArrayAdapter<String> dataAdapteremp = new ArrayAdapter<String>(this, android.R.layout.simple_spinner_item, emp);
        dataAdapteremp.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spCustomer.setAdapter(dataAdapteremp);
    }
}