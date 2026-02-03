package com.forcepower.acedns.activity.non_auth.main_menu.sis_summery;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.fragment.app.FragmentActivity;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.non_auth.main_menu.sis_summery.support.DataStore;
import com.forcepower.acedns.activity.non_auth.main_menu.sis_summery.support.TitleStore;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

public class NewSisSummeryActivity extends FragmentActivity {

    public Handler mReportHandler;
    public Handler mHandlerPrepareSaudaData;
    public ProgressDialog mProgressDialogPrepareData;
    ContentValues values;

    Button btnBack;
    Spinner spdate;

    //header
    TextView txtName, txtMonthYear;

    // Parameter One
    TextView tvParameter1;
    TextView header1p1, header1p2, header1p3, header1p4, header1p5;
    TextView value1p1, value1p2, value1p3, value1p4, value1p5;

    // Parameter Two
    TextView tvParameter2;
    TextView header2p1, header2p2, header2p3, header2p4, header2p5;
    TextView value2p1, value2p2, value2p3, value2p4, value2p5;

    // Parameter Three
    TextView tvParameter3;
    TextView header3p1, header3p2, header3p3, header3p4, header3p5;
    TextView value3p1, value3p2, value3p3, value3p4, value3p5;

    // Parameter Four
    TextView tvParameter4;
    TextView header4p1, header4p2, header4p3, header4p4, header4p5;
    TextView value4p1, value4p2, value4p3, value4p4, value4p5;

    // Parameter Five
    TextView tvParameter5;
    TextView header5p1, header5p2, header5p3, header5p4, header5p5;
    TextView value5p1, value5p2, value5p3, value5p4, value5p5;

    // Parameter Six
    TextView tvParameter6;
    TextView header6p1, header6p2, header6p3, header6p4, header6p5;
    TextView value6p1, value6p2, value6p3, value6p4, value6p5;

    // Parameter Seven
    TextView tvParameter7;
    TextView header7p1, header7p2, header7p3, header7p4, header7p5;
    TextView value7p1, value7p2, value7p3, value7p4, value7p5;

    // Parameter Other
    TextView header0p1, header0p2, header0p3, header0p4, header0p5, header0p6;
    TextView value0p1, value0p2, value0p3, value0p4, value0p5, value0p6;


    String httpResponse = "";
    Context mContext;

    private ArrayList<String> month = new ArrayList<>();

    private ArrayList<DataStore> dataStores;
    private ArrayList<TitleStore> titleStores;

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_new_sis_summery);

        initParameterOne();
        initParameterTwo();
        initParameterThree();
        initParameterFour();
        initParameterFive();
        initParameterSix();
        initParameterSeven();
        initParameterOther();

        mContext = NewSisSummeryActivity.this;

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        ConnectionDetector cd;
        cd = new ConnectionDetector(mContext);
        if (cd.isConnectingToInternet()) {
            PrepareCustomerData();
        } else {
            Toast.makeText(mContext, "Please Connect INTERNET For update Data", Toast.LENGTH_SHORT).show();
        }
    }

    private void initParameterOne() {
        tvParameter1 = findViewById(R.id.tvParameter1);

        header1p1 = findViewById(R.id.header1p1);
        header1p2 = findViewById(R.id.header1p2);
        header1p3 = findViewById(R.id.header1p3);
        header1p4 = findViewById(R.id.header1p4);
        header1p5 = findViewById(R.id.header1p5);

        value1p1 = findViewById(R.id.value1p1);
        value1p2 = findViewById(R.id.value1p2);
        value1p3 = findViewById(R.id.value1p3);
        value1p4 = findViewById(R.id.value1p4);
        value1p5 = findViewById(R.id.value1p5);
    }

    private void initParameterTwo() {
        tvParameter2 = findViewById(R.id.tvParameter2);

        header2p1 = findViewById(R.id.header2p1);
        header2p2 = findViewById(R.id.header2p2);
        header2p3 = findViewById(R.id.header2p3);
        header2p4 = findViewById(R.id.header2p4);
        header2p5 = findViewById(R.id.header2p5);

        value2p1 = findViewById(R.id.value2p1);
        value2p2 = findViewById(R.id.value2p2);
        value2p3 = findViewById(R.id.value2p3);
        value2p4 = findViewById(R.id.value2p4);
        value2p5 = findViewById(R.id.value2p5);
    }

    private void initParameterThree() {
        tvParameter3 = findViewById(R.id.tvParameter3);

        header3p1 = findViewById(R.id.header3p1);
        header3p2 = findViewById(R.id.header3p2);
        header3p3 = findViewById(R.id.header3p3);
        header3p4 = findViewById(R.id.header3p4);
        header3p5 = findViewById(R.id.header3p5);

        value3p1 = findViewById(R.id.value3p1);
        value3p2 = findViewById(R.id.value3p2);
        value3p3 = findViewById(R.id.value3p3);
        value3p4 = findViewById(R.id.value3p4);
        value3p5 = findViewById(R.id.value3p5);
    }

    private void initParameterFour() {
        tvParameter4 = findViewById(R.id.tvParameter4);

        header4p1 = findViewById(R.id.header4p1);
        header4p2 = findViewById(R.id.header4p2);
        header4p3 = findViewById(R.id.header4p3);
        header4p4 = findViewById(R.id.header4p4);
        header4p5 = findViewById(R.id.header4p5);

        value4p1 = findViewById(R.id.value4p1);
        value4p2 = findViewById(R.id.value4p2);
        value4p3 = findViewById(R.id.value4p3);
        value4p4 = findViewById(R.id.value4p4);
        value4p5 = findViewById(R.id.value4p5);
    }

    private void initParameterFive() {
        tvParameter5 = findViewById(R.id.tvParameter5);

        header5p1 = findViewById(R.id.header5p1);
        header5p2 = findViewById(R.id.header5p2);
        header5p3 = findViewById(R.id.header5p3);
        header5p4 = findViewById(R.id.header5p4);
        header5p5 = findViewById(R.id.header5p5);

        value5p1 = findViewById(R.id.value5p1);
        value5p2 = findViewById(R.id.value5p2);
        value5p3 = findViewById(R.id.value5p3);
        value5p4 = findViewById(R.id.value5p4);
        value5p5 = findViewById(R.id.value5p5);
    }

    private void initParameterSix() {
        tvParameter6 = findViewById(R.id.tvParameter6);

        header6p1 = findViewById(R.id.header6p1);
        header6p2 = findViewById(R.id.header6p2);
        header6p3 = findViewById(R.id.header6p3);
        header6p4 = findViewById(R.id.header6p4);
        header6p5 = findViewById(R.id.header6p5);

        value6p1 = findViewById(R.id.value6p1);
        value6p2 = findViewById(R.id.value6p2);
        value6p3 = findViewById(R.id.value6p3);
        value6p4 = findViewById(R.id.value6p4);
        value6p5 = findViewById(R.id.value6p5);
    }

    private void initParameterSeven() {
        tvParameter7 = findViewById(R.id.tvParameter7);

        header7p1 = findViewById(R.id.header7p1);
        header7p2 = findViewById(R.id.header7p2);
        header7p3 = findViewById(R.id.header7p3);
        header7p4 = findViewById(R.id.header7p4);
        header7p5 = findViewById(R.id.header7p5);

        value7p1 = findViewById(R.id.value7p1);
        value7p2 = findViewById(R.id.value7p2);
        value7p3 = findViewById(R.id.value7p3);
        value7p4 = findViewById(R.id.value7p4);
        value7p5 = findViewById(R.id.value7p5);
    }

    private void initParameterOther() {
        btnBack = findViewById(R.id.btnBack);
        spdate = findViewById(R.id.spdatemonth);
        txtName = findViewById(R.id.txtName);
        txtMonthYear = findViewById(R.id.txtMonthYear);

        header0p1 = findViewById(R.id.header0p1);
        header0p2 = findViewById(R.id.header0p2);
        header0p3 = findViewById(R.id.header0p3);
        header0p4 = findViewById(R.id.header0p4);
        header0p5 = findViewById(R.id.header0p5);
        header0p6 = findViewById(R.id.header0p6);

        value0p1 = findViewById(R.id.value0p1);
        value0p2 = findViewById(R.id.value0p2);
        value0p3 = findViewById(R.id.value0p3);
        value0p4 = findViewById(R.id.value0p4);
        value0p5 = findViewById(R.id.value0p5);
        value0p6 = findViewById(R.id.value0p6);

        btnBack.setOnClickListener(v -> finish());
    }

    public void PrepareCustomerData() {
        mProgressDialogPrepareData = new ProgressDialog(mContext);
        mProgressDialogPrepareData.setCancelable(false);
        mProgressDialogPrepareData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareData.show();
        new Thread() {
            public void run() {
                _DOWNLOAD_sis_details();
            }
        }.start();
    }

    private void showData(String monthDate) {
        Log.d("TAG", "_DOWNLOAD_ showFirstData: " + monthDate);

        String header_id="";
        for (DataStore ds : dataStores) {
            if(monthDate.equalsIgnoreCase(ds.getMonth_year())){
                header_id=ds.getHeader_id();
                showAllValueData(ds);
            }
        }

        for(TitleStore ts: titleStores){
            if(header_id.equalsIgnoreCase(ts.getHeader_id())){
                showAllTitleData(ts);
            }
        }
    }

    private void showAllValueData(DataStore ds){
        txtName.setText(ds.getName());
        txtMonthYear.setText(ds.getMonth_year());

        tvParameter1.setText(ds.getSales_volume_MT());
        value1p1.setText(ds.getSales_volume_TGT());
        value1p2.setText(ds.getSales_volume_ACH());
        value1p3.setText(ds.getSales_volume_ACH_percent());
        value1p4.setText(ds.getSales_volume_WGT_percent());
        value1p5.setText(ds.getSales_volume_SCORE_percent());

        tvParameter2.setText(ds.getMonthly_unique_visit());
        value2p1.setText(ds.getMonthly_unique_visit_TGT());
        value2p2.setText(ds.getMonthly_unique_visit_ACH());
        value2p3.setText(ds.getMonthly_unique_visit_ACH_percent());
        value2p4.setText(ds.getMonthly_unique_visit_WGT_percent());
        value2p5.setText(ds.getMonthly_unique_visit_SCORE_percent());

        tvParameter3.setText(ds.getDealer_appointment());
        value3p1.setText(ds.getDealer_appointment_TGT());
        value3p2.setText(ds.getDealer_appointment_ACH());
        value3p3.setText(ds.getDealer_appointment_ACH_percent());
        value3p4.setText(ds.getDealer_appointmen_WGT_percent());
        value3p5.setText(ds.getDealer_appointment_SCORE_percent());

        tvParameter4.setText(ds.getActive_dealer_count());
        value4p1.setText(ds.getActive_dealer_count_TGT());
        value4p2.setText(ds.getActive_dealer_count_ACH());
        value4p3.setText(ds.getActive_dealer_count_ACH_percent());
        value4p4.setText(ds.getActive_dealer_count_WGT_percent());
        value4p5.setText(ds.getActive_dealer_count_SCORE_percent());

        tvParameter5.setText(ds.getParamiter_five());
        value5p1.setText(ds.getFive_TGT());
        value5p2.setText(ds.getFive_ACH());
        value5p3.setText(ds.getFive_ACH_percent());
        value5p4.setText(ds.getFive_WGT_percent());
        value5p5.setText(ds.getFive_SCORE_percent());

        tvParameter6.setText(ds.getParamiter_six());
        value6p1.setText(ds.getSix_TGT());
        value6p2.setText(ds.getSix_ACH());
        value6p3.setText(ds.getSix_ACH_percent());
        value6p4.setText(ds.getSix_WGT_percent());
        value6p5.setText(ds.getSix_SCORE_percent());

        tvParameter7.setText(ds.getParamiter_seven());
        value7p1.setText(ds.getSeven_TGT());
        value7p2.setText(ds.getSeven_ACH());
        value7p3.setText(ds.getSeven_ACH_percent());
        value7p4.setText(ds.getSeven_WGT_percent());
        value7p5.setText(ds.getSeven_SCORE_percent());

        value0p1.setText(ds.getEarning_score_percent());
        value0p2.setText(ds.getPenalty_percent());
        value0p3.setText(ds.getFinal_score_percent());
        value0p4.setText(ds.getOTSI());
        value0p5.setText(ds.getSIS_earning_month());
        value0p6.setText(ds.getRemarks());
    }

    private void showAllTitleData(TitleStore ts){
        header1p1.setText(ts.getSales_volume_TGT());
        header1p2.setText(ts.getSales_volume_ACTUAL());
        header1p3.setText(ts.getSis_slab_percent());
        header1p4.setText(ts.getPremium_sales_conversion_target());
        header1p5.setText(ts.getSales_volume_SCORE_percent());

        header2p1.setText(ts.getMonthly_unique_visit_TGT());
        header2p2.setText(ts.getMonthly_unique_visit_ACH());
        header2p3.setText(ts.getActivities_sis_slab_percent());
        header2p4.setText(ts.getActivities());
        header2p5.setText(ts.getMonthly_unique_visit_SCORE_percent());

        header3p1.setText(ts.getDealer_appointment_ACTUAL());
        header3p2.setText(ts.getDealer_appointment_sis_slab_percent());
        header3p3.setText(ts.getInfluencer_registration());
        header3p4.setText(ts.getDealer_appointment_SCORE_percent());
        header3p5.setText(ts.getParameter_3_5());

        header4p1.setText(ts.getActive_dealer_count_TGT());
        header4p2.setText(ts.getActive_dealer_count_ACH());
        header4p3.setText(ts.getActive_dealer_count_sis_slab_percent());
        header4p4.setText(ts.getActive_dealer_growth());
        header4p5.setText(ts.getActive_dealer_count_SCORE_percent());

        header5p1.setText(ts.getFive_TGT());
        header5p2.setText(ts.getFive_ACH());
        header5p3.setText(ts.getFive_sis_slab_percent());
        header5p4.setText(ts.getActive_influencer_growth());
        header5p5.setText(ts.getFive_SCORE_percent());

        header6p1.setText(ts.getSix_ACTUAL());
        header6p2.setText(ts.getSix_slab_percent());
        header6p3.setText(ts.getSix_SCORE_percent());
        header6p4.setText(ts.getEarning_score_percent());
        header6p5.setText(ts.getPenalty_percent());

        header7p1.setText(ts.getSeven_TGT());
        header7p2.setText(ts.getSeven_ACH());
        header7p3.setText(ts.getSeven_sis_slab_percent());
        header7p4.setText(ts.getSeven_WGT());
        header7p5.setText(ts.getSeven_SCORE_percent());

        header0p1.setText(ts.getFinal_score_percent());
        header0p2.setText(ts.getH6_l1());
        header0p3.setText(ts.getH6_l2());
        header0p4.setText(ts.getH6_l3());
        header0p5.setText(ts.getH6_l4());
        header0p6.setText(ts.getRemarks());
    }

    private void setSpinner() {
        try {
            ArrayAdapter ad = new ArrayAdapter(this, android.R.layout.simple_spinner_item, month);

            ad.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

            spdate.setAdapter(ad);

            spdate.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                    showData(month.get(position));
                }

                @Override
                public void onNothingSelected(AdapterView<?> parent) {

                }
            });

            showData(month.get(0));
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ setSpinner: " + e.getMessage());
        }
    }

    public void commonNameValuePair() {
        values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
    }

    public void _DOWNLOAD_sis_header() {
        commonNameValuePair();
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(BaseUrl.baseUrl + "sis-summary-header.php?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode(), "");
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ SisSummeryActivity: " + BaseUrl.baseUrl + "sis-summary-header.php?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode());

        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            titleStores = new ArrayList<>();
            try {
                JSONObject obj = new JSONObject(httpResponse);
                if (obj.getString("process_status").equals("YES")) {
                    JSONArray dataArray = obj.getJSONArray("datavalue");

                    for (int i = 0; i < dataArray.length(); i++) {
                        JSONObject dataobj = dataArray.getJSONObject(i);
                        TitleStore temp = new TitleStore();

                        if (dataobj.isNull("header_id")) {
                            temp.setHeader_id("");
                        } else {
                            temp.setHeader_id(dataobj.getString("header_id"));
                        }
                        if (dataobj.isNull("emp_code")) {
                            temp.setEmp_code("");
                        } else {
                            temp.setEmp_code(dataobj.getString("emp_code"));
                        }
                        if (dataobj.isNull("name")) {
                            temp.setName("");
                        } else {
                            temp.setName(dataobj.getString("name"));
                        }
                        if (dataobj.isNull("month_year")) {
                            temp.setMonth_year("");
                        } else {
                            temp.setMonth_year(dataobj.getString("month_year"));
                        }

                        if (dataobj.isNull("parameter_1")) {
                            temp.setParameter_1("");
                        } else {
                            temp.setParameter_1(dataobj.getString("parameter_1"));
                        }
                        if (dataobj.isNull("sales_volume_TGT")) {
                            temp.setSales_volume_TGT("");
                        } else {
                            temp.setSales_volume_TGT(dataobj.getString("sales_volume_TGT"));
                        }
                        if (dataobj.isNull("sales_volume_ACTUAL")) {
                            temp.setSales_volume_ACTUAL("");
                        } else {
                            temp.setSales_volume_ACTUAL(dataobj.getString("sales_volume_ACTUAL"));
                        }
                        if (dataobj.isNull("sis_slab_percent")) {
                            temp.setSis_slab_percent("");
                        } else {
                            temp.setSis_slab_percent(dataobj.getString("sis_slab_percent"));
                        }
                        if (dataobj.isNull("premium_sales_conversion_target")) {
                            temp.setPremium_sales_conversion_target("");
                        } else {
                            temp.setPremium_sales_conversion_target(dataobj.getString("premium_sales_conversion_target"));
                        }
                        if (dataobj.isNull("sales_volume_SCORE_percent")) {
                            temp.setSales_volume_SCORE_percent("");
                        } else {
                            temp.setSales_volume_SCORE_percent(dataobj.getString("sales_volume_SCORE_percent"));
                        }

                        if (dataobj.isNull("parameter_2")) {
                            temp.setParameter_2("");
                        } else {
                            temp.setParameter_2(dataobj.getString("parameter_2"));
                        }
                        if (dataobj.isNull("monthly_unique_visit_TGT")) {
                            temp.setMonthly_unique_visit_TGT("");
                        } else {
                            temp.setMonthly_unique_visit_TGT(dataobj.getString("monthly_unique_visit_TGT"));
                        }
                        if (dataobj.isNull("monthly_unique_visit_ACH")) {
                            temp.setMonthly_unique_visit_ACH("");
                        } else {
                            temp.setMonthly_unique_visit_ACH(dataobj.getString("monthly_unique_visit_ACH"));
                        }
                        if (dataobj.isNull("activities_sis_slab_percent")) {
                            temp.setActivities_sis_slab_percent("");
                        } else {
                            temp.setActivities_sis_slab_percent(dataobj.getString("activities_sis_slab_percent"));
                        }
                        if (dataobj.isNull("activities")) {
                            temp.setActivities("");
                        } else {
                            temp.setActivities(dataobj.getString("activities"));
                        }
                        if (dataobj.isNull("monthly_unique_visit_SCORE_percent")) {
                            temp.setMonthly_unique_visit_SCORE_percent("");
                        } else {
                            temp.setMonthly_unique_visit_SCORE_percent(dataobj.getString("monthly_unique_visit_SCORE_percent"));
                        }

                        if (dataobj.isNull("parameter_3")) {
                            temp.setParameter_3("");
                        } else {
                            temp.setParameter_3(dataobj.getString("parameter_3"));
                        }
                        if (dataobj.isNull("dealer_appointment_ACTUAL")) {
                            temp.setDealer_appointment_ACTUAL("");
                        } else {
                            temp.setDealer_appointment_ACTUAL(dataobj.getString("dealer_appointment_ACTUAL"));
                        }
                        if (dataobj.isNull("dealer_appointment_sis_slab_percent")) {
                            temp.setDealer_appointment_sis_slab_percent("");
                        } else {
                            temp.setDealer_appointment_sis_slab_percent(dataobj.getString("dealer_appointment_sis_slab_percent"));
                        }
                        if (dataobj.isNull("influencer_registration")) {
                            temp.setInfluencer_registration("");
                        } else {
                            temp.setInfluencer_registration(dataobj.getString("influencer_registration"));
                        }
                        if (dataobj.isNull("dealer_appointment_SCORE_percent")) {
                            temp.setDealer_appointment_SCORE_percent("");
                        } else {
                            temp.setDealer_appointment_SCORE_percent(dataobj.getString("dealer_appointment_SCORE_percent"));
                        }
                        if (dataobj.isNull("parameter_3_5")) {
                            temp.setParameter_3_5("");
                        } else {
                            temp.setParameter_3_5(dataobj.getString("parameter_3_5"));
                        }

                        if (dataobj.isNull("parameter_4")) {
                            temp.setParameter_4("");
                        } else {
                            temp.setParameter_4(dataobj.getString("parameter_4"));
                        }
                        if (dataobj.isNull("active_dealer_count_TGT")) {
                            temp.setActive_dealer_count_TGT("");
                        } else {
                            temp.setActive_dealer_count_TGT(dataobj.getString("active_dealer_count_TGT"));
                        }
                        if (dataobj.isNull("active_dealer_count_ACH")) {
                            temp.setActive_dealer_count_ACH("");
                        } else {
                            temp.setActive_dealer_count_ACH(dataobj.getString("active_dealer_count_ACH"));
                        }
                        if (dataobj.isNull("active_dealer_count_sis_slab_percent")) {
                            temp.setActive_dealer_count_sis_slab_percent("");
                        } else {
                            temp.setActive_dealer_count_sis_slab_percent(dataobj.getString("active_dealer_count_sis_slab_percent"));
                        }
                        if (dataobj.isNull("active_dealer_growth")) {
                            temp.setActive_dealer_growth("");
                        } else {
                            temp.setActive_dealer_growth(dataobj.getString("active_dealer_growth"));
                        }
                        if (dataobj.isNull("active_dealer_count_SCORE_percent")) {
                            temp.setActive_dealer_count_SCORE_percent("");
                        } else {
                            temp.setActive_dealer_count_SCORE_percent(dataobj.getString("active_dealer_count_SCORE_percent"));
                        }

                        if (dataobj.isNull("paramiter_five")) {
                            temp.setParamiter_five("");
                        } else {
                            temp.setParamiter_five(dataobj.getString("paramiter_five"));
                        }
                        if (dataobj.isNull("five_TGT")) {
                            temp.setFive_TGT("");
                        } else {
                            temp.setFive_TGT(dataobj.getString("five_TGT"));
                        }
                        if (dataobj.isNull("five_ACH")) {
                            temp.setFive_ACH("");
                        } else {
                            temp.setFive_ACH(dataobj.getString("five_ACH"));
                        }
                        if (dataobj.isNull("five_sis_slab_percent")) {
                            temp.setFive_sis_slab_percent("");
                        } else {
                            temp.setFive_sis_slab_percent(dataobj.getString("five_sis_slab_percent"));
                        }
                        if (dataobj.isNull("active_influencer_growth")) {
                            temp.setActive_influencer_growth("");
                        } else {
                            temp.setActive_influencer_growth(dataobj.getString("active_influencer_growth"));
                        }
                        if (dataobj.isNull("five_SCORE_percent")) {
                            temp.setFive_SCORE_percent("");
                        } else {
                            temp.setFive_SCORE_percent(dataobj.getString("five_SCORE_percent"));
                        }

                        if (dataobj.isNull("paramiter_six")) {
                            temp.setParamiter_six("");
                        } else {
                            temp.setParamiter_six(dataobj.getString("paramiter_six"));
                        }
                        if (dataobj.isNull("six_ACTUAL")) {
                            temp.setSix_ACTUAL("");
                        } else {
                            temp.setSix_ACTUAL(dataobj.getString("six_ACTUAL"));
                        }
                        if (dataobj.isNull("six_slab_percent")) {
                            temp.setSix_slab_percent("");
                        } else {
                            temp.setSix_slab_percent(dataobj.getString("six_slab_percent"));
                        }
                        if (dataobj.isNull("six_SCORE_percent")) {
                            temp.setSix_SCORE_percent("");
                        } else {
                            temp.setSix_SCORE_percent(dataobj.getString("six_SCORE_percent"));
                        }

                        if (dataobj.isNull("earning_score_percent")) {
                            temp.setEarning_score_percent("");
                        } else {
                            temp.setEarning_score_percent(dataobj.getString("earning_score_percent"));
                        }
                        if (dataobj.isNull("penalty_percent")) {
                            temp.setPenalty_percent("");
                        } else {
                            temp.setPenalty_percent(dataobj.getString("penalty_percent"));
                        }
                        if (dataobj.isNull("final_score_percent")) {
                            temp.setFinal_score_percent("");
                        } else {
                            temp.setFinal_score_percent(dataobj.getString("final_score_percent"));
                        }

                        if (dataobj.isNull("h6_l1")) {
                            temp.setH6_l1("");
                        } else {
                            temp.setH6_l1(dataobj.getString("h6_l1"));
                        }
                        if (dataobj.isNull("h6_l2")) {
                            temp.setH6_l2("");
                        } else {
                            temp.setH6_l2(dataobj.getString("h6_l2"));
                        }
                        if (dataobj.isNull("h6_l3")) {
                            temp.setH6_l3("");
                        } else {
                            temp.setH6_l3(dataobj.getString("h6_l3"));
                        }
                        if (dataobj.isNull("h6_l4")) {
                            temp.setH6_l4("");
                        } else {
                            temp.setH6_l4(dataobj.getString("h6_l4"));
                        }
                        if (dataobj.isNull("remarks")) {
                            temp.setRemarks("");
                        } else {
                            temp.setRemarks(dataobj.getString("remarks"));
                        }

                        if (dataobj.isNull("paramiter_seven")) {
                            temp.setParamiter_seven("");
                        } else {
                            temp.setParamiter_seven(dataobj.getString("paramiter_seven"));
                        }
                        if (dataobj.isNull("seven_TGT")) {
                            temp.setSeven_TGT("");
                        } else {
                            temp.setSeven_TGT(dataobj.getString("seven_TGT"));
                        }
                        if (dataobj.isNull("seven_ACH")) {
                            temp.setSeven_ACH("");
                        } else {
                            temp.setSeven_ACH(dataobj.getString("seven_ACH"));
                        }
                        if (dataobj.isNull("seven_sis_slab_percent")) {
                            temp.setSeven_sis_slab_percent("");
                        } else {
                            temp.setSeven_sis_slab_percent(dataobj.getString("seven_sis_slab_percent"));
                        }
                        if (dataobj.isNull("seven_WGT")) {
                            temp.setSeven_WGT("");
                        } else {
                            temp.setSeven_WGT(dataobj.getString("seven_WGT"));
                        }
                        if (dataobj.isNull("seven_SCORE_percent")) {
                            temp.setSeven_SCORE_percent("");
                        } else {
                            temp.setSeven_SCORE_percent(dataobj.getString("seven_SCORE_percent"));
                        }

                        titleStores.add(temp);
                    }
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ sis_header: " + e.getMessage());
            }
        }
        runOnUiThread(this::setSpinner);
        mProgressDialogPrepareData.cancel();
    }

    public void _DOWNLOAD_sis_details() {
        commonNameValuePair();
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(BaseUrl.baseUrl + "sis-summary-details-v2.php?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode(), "");
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ SisSummeryActivity: " + BaseUrl.baseUrl + "sis-summary-details-v2.php?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode());

        if (httpResponse != null && !httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            dataStores = new ArrayList<>();
            month = new ArrayList<>();
            try {
                JSONObject obj = new JSONObject(httpResponse);
                if (obj.getString("process_status").equals("YES")) {
                    JSONArray dataArray = obj.getJSONArray("datavalue");
                    for (int i = 0; i < dataArray.length(); i++) {
                        JSONObject dataobj = dataArray.getJSONObject(i);
                        DataStore temp = new DataStore();

                        if (dataobj.isNull("emp_code")) {
                            temp.setEmp_code("");
                        } else {
                            temp.setEmp_code(dataobj.getString("emp_code"));
                        }
                        if (dataobj.isNull("name")) {
                            temp.setName("");
                        } else {
                            temp.setName(dataobj.getString("name"));
                        }
                        if (dataobj.isNull("month_year")) {
                            temp.setMonth_year("");
                        } else {
                            temp.setMonth_year(dataobj.getString("month_year"));
                            month.add(dataobj.getString("month_year"));
                        }

                        if (dataobj.isNull("sales_volume_MT")) {
                            temp.setSales_volume_MT("");
                        } else {
                            temp.setSales_volume_MT(dataobj.getString("sales_volume_MT"));
                        }
                        if (dataobj.isNull("sales_volume_TGT")) {
                            temp.setSales_volume_TGT("");
                        } else {
                            temp.setSales_volume_TGT(dataobj.getString("sales_volume_TGT"));
                        }

                        if (dataobj.isNull("sales_volume_ACH")) {
                            temp.setSales_volume_ACH("");
                        } else {
                            temp.setSales_volume_ACH(dataobj.getString("sales_volume_ACH"));
                        }

                        if (dataobj.isNull("sales_volume_ACH_percent")) {
                            temp.setSales_volume_ACH_percent("");
                        } else {
                            temp.setSales_volume_ACH_percent(dataobj.getString("sales_volume_ACH_percent"));
                        }

                        if (dataobj.isNull("sales_volume_WGT_percent")) {
                            temp.setSales_volume_WGT_percent("");
                        } else {
                            temp.setSales_volume_WGT_percent(dataobj.getString("sales_volume_WGT_percent"));
                        }

                        if (dataobj.isNull("sales_volume_SCORE_percent")) {
                            temp.setSales_volume_SCORE_percent("");
                        } else {
                            temp.setSales_volume_SCORE_percent(dataobj.getString("sales_volume_SCORE_percent"));
                        }

                        if (dataobj.isNull("monthly_unique_visit")) {
                            temp.setMonthly_unique_visit("");
                        } else {
                            temp.setMonthly_unique_visit(dataobj.getString("monthly_unique_visit"));
                        }

                        if (dataobj.isNull("monthly_unique_visit_SCORE_percent")) {
                            temp.setMonthly_unique_visit_SCORE_percent("");
                        } else {
                            temp.setMonthly_unique_visit_SCORE_percent(dataobj.getString("monthly_unique_visit_SCORE_percent"));
                        }

                        if (dataobj.isNull("monthly_unique_visit_TGT")) {
                            temp.setMonthly_unique_visit_TGT("");
                        } else {
                            temp.setMonthly_unique_visit_TGT(dataobj.getString("monthly_unique_visit_TGT"));
                        }

                        if (dataobj.isNull("monthly_unique_visit_ACH")) {
                            temp.setMonthly_unique_visit_ACH("");
                        } else {
                            temp.setMonthly_unique_visit_ACH(dataobj.getString("monthly_unique_visit_ACH"));
                        }

                        if (dataobj.isNull("monthly_unique_visit_ACH_percent")) {
                            temp.setMonthly_unique_visit_ACH_percent("");
                        } else {
                            temp.setMonthly_unique_visit_ACH_percent(dataobj.getString("monthly_unique_visit_ACH_percent"));
                        }

                        if (dataobj.isNull("monthly_unique_visit_WGT_percent")) {
                            temp.setMonthly_unique_visit_WGT_percent("");
                        } else {
                            temp.setMonthly_unique_visit_WGT_percent(dataobj.getString("monthly_unique_visit_WGT_percent"));
                        }

                        if (dataobj.isNull("monthly_unique_visit_SCORE_percent")) {
                            temp.setMonthly_unique_visit_SCORE_percent("");
                        } else {
                            temp.setMonthly_unique_visit_SCORE_percent(dataobj.getString("monthly_unique_visit_SCORE_percent"));
                        }

                        if (dataobj.isNull("dealer_appointment")) {
                            temp.setDealer_appointment("");
                        } else {
                            temp.setDealer_appointment(dataobj.getString("dealer_appointment"));
                        }

                        if (dataobj.isNull("dealer_appointment_TGT")) {
                            temp.setDealer_appointment_TGT("");
                        } else {
                            temp.setDealer_appointment_TGT(dataobj.getString("dealer_appointment_TGT"));
                        }

                        if (dataobj.isNull("dealer_appointment_ACH")) {
                            temp.setDealer_appointment_ACH("");
                        } else {
                            temp.setDealer_appointment_ACH(dataobj.getString("dealer_appointment_ACH"));
                        }

                        if (dataobj.isNull("dealer_appointment_ACH_percent")) {
                            temp.setDealer_appointment_ACH_percent("");
                        } else {
                            temp.setDealer_appointment_ACH_percent(dataobj.getString("dealer_appointment_ACH_percent"));
                        }

                        if (dataobj.isNull("dealer_appointmen_WGT_percent")) {
                            temp.setDealer_appointmen_WGT_percent("");
                        } else {
                            temp.setDealer_appointmen_WGT_percent(dataobj.getString("dealer_appointmen_WGT_percent"));
                        }

                        if (dataobj.isNull("dealer_appointment_SCORE_percent")) {
                            temp.setDealer_appointment_SCORE_percent("");
                        } else {
                            temp.setDealer_appointment_SCORE_percent(dataobj.getString("dealer_appointment_SCORE_percent"));
                        }


                        if (dataobj.isNull("active_dealer_count")) {
                            temp.setActive_dealer_count("");
                        } else {
                            temp.setActive_dealer_count(dataobj.getString("active_dealer_count"));
                        }

                        if (dataobj.isNull("active_dealer_count_TGT")) {
                            temp.setActive_dealer_count_TGT("");
                        } else {
                            temp.setActive_dealer_count_TGT(dataobj.getString("active_dealer_count_TGT"));
                        }

                        if (dataobj.isNull("active_dealer_count_ACH")) {
                            temp.setActive_dealer_count_ACH("");
                        } else {
                            temp.setActive_dealer_count_ACH(dataobj.getString("active_dealer_count_ACH"));
                        }

                        if (dataobj.isNull("active_dealer_count_ACH_percent")) {
                            temp.setActive_dealer_count_ACH_percent("");
                        } else {
                            temp.setActive_dealer_count_ACH_percent(dataobj.getString("active_dealer_count_ACH_percent"));
                        }

                        if (dataobj.isNull("active_dealer_count_WGT_percent")) {
                            temp.setActive_dealer_count_WGT_percent("");
                        } else {
                            temp.setActive_dealer_count_WGT_percent(dataobj.getString("active_dealer_count_WGT_percent"));
                        }

                        if (dataobj.isNull("active_dealer_count_SCORE_percent")) {
                            temp.setActive_dealer_count_SCORE_percent("");
                        } else {
                            temp.setActive_dealer_count_SCORE_percent(dataobj.getString("active_dealer_count_SCORE_percent"));
                        }


                        if (dataobj.isNull("paramiter_five")) {
                            temp.setParamiter_five("");
                        } else {
                            temp.setParamiter_five(dataobj.getString("paramiter_five"));
                        }

                        if (dataobj.isNull("five_TGT")) {
                            temp.setFive_TGT("");
                        } else {
                            temp.setFive_TGT(dataobj.getString("five_TGT"));
                        }

                        if (dataobj.isNull("five_ACH")) {
                            temp.setFive_ACH("");
                        } else {
                            temp.setFive_ACH(dataobj.getString("five_ACH"));
                        }

                        if (dataobj.isNull("five_ACH_percent")) {
                            temp.setFive_ACH_percent("");
                        } else {
                            temp.setFive_ACH_percent(dataobj.getString("five_ACH_percent"));
                        }

                        if (dataobj.isNull("five_WGT_percent")) {
                            temp.setFive_WGT_percent("");
                        } else {
                            temp.setFive_WGT_percent(dataobj.getString("five_WGT_percent"));
                        }

                        if (dataobj.isNull("five_SCORE_percent")) {
                            temp.setFive_SCORE_percent("");
                        } else {
                            temp.setFive_SCORE_percent(dataobj.getString("five_SCORE_percent"));
                        }


                        if (dataobj.isNull("paramiter_six")) {
                            temp.setParamiter_six("");
                        } else {
                            temp.setParamiter_six(dataobj.getString("paramiter_six"));
                        }

                        if (dataobj.isNull("six_TGT")) {
                            temp.setSix_TGT("");
                        } else {
                            temp.setSix_TGT(dataobj.getString("six_TGT"));
                        }

                        if (dataobj.isNull("six_ACH")) {
                            temp.setSix_ACH("");
                        } else {
                            temp.setSix_ACH(dataobj.getString("six_ACH"));
                        }

                        if (dataobj.isNull("six_ACH_percent")) {
                            temp.setSix_ACH_percent("");
                        } else {
                            temp.setSix_ACH_percent(dataobj.getString("six_ACH_percent"));
                        }

                        if (dataobj.isNull("six_WGT_percent")) {
                            temp.setSix_WGT_percent("");
                        } else {
                            temp.setSix_WGT_percent(dataobj.getString("six_WGT_percent"));
                        }

                        if (dataobj.isNull("six_SCORE_percent")) {
                            temp.setSix_SCORE_percent("");
                        } else {
                            temp.setSix_SCORE_percent(dataobj.getString("six_SCORE_percent"));
                        }


                        if (dataobj.isNull("earning_score_percent")) {
                            temp.setEarning_score_percent("");
                        } else {
                            temp.setEarning_score_percent(dataobj.getString("earning_score_percent"));
                        }

                        if (dataobj.isNull("penalty_percent")) {
                            temp.setPenalty_percent("");
                        } else {
                            temp.setPenalty_percent(dataobj.getString("penalty_percent"));
                        }

                        if (dataobj.isNull("final_score_percent")) {
                            temp.setFinal_score_percent("");
                        } else {
                            temp.setFinal_score_percent(dataobj.getString("final_score_percent"));
                        }

                        if (dataobj.isNull("OTSI")) {
                            temp.setOTSI("");
                        } else {
                            temp.setOTSI(dataobj.getString("OTSI"));
                        }

                        if (dataobj.isNull("SIS_earning_month")) {
                            temp.setSIS_earning_month("");
                        } else {
                            temp.setSIS_earning_month(dataobj.getString("SIS_earning_month"));
                        }

                        if (dataobj.isNull("remarks")) {
                            temp.setRemarks("");
                        } else {
                            temp.setRemarks(dataobj.getString("remarks"));
                        }

                        if (dataobj.isNull("header_id")) {
                            temp.setHeader_id("");
                        } else {
                            temp.setHeader_id(dataobj.getString("header_id"));
                        }


                        if (dataobj.isNull("paramiter_seven")) {
                            temp.setParamiter_seven("");
                        } else {
                            temp.setParamiter_seven(dataobj.getString("paramiter_seven"));
                        }

                        if (dataobj.isNull("seven_TGT")) {
                            temp.setSeven_TGT("");
                        } else {
                            temp.setSeven_TGT(dataobj.getString("seven_TGT"));
                        }

                        if (dataobj.isNull("seven_ACH")) {
                            temp.setSeven_ACH("");
                        } else {
                            temp.setSeven_ACH(dataobj.getString("seven_ACH"));
                        }

                        if (dataobj.isNull("seven_ACH_percent")) {
                            temp.setSeven_ACH_percent("");
                        } else {
                            temp.setSeven_ACH_percent(dataobj.getString("seven_ACH_percent"));
                        }

                        if (dataobj.isNull("seven_WGT_percent")) {
                            temp.setSeven_WGT_percent("");
                        } else {
                            temp.setSeven_WGT_percent(dataobj.getString("seven_WGT_percent"));
                        }

                        if (dataobj.isNull("seven_SCORE_percent")) {
                            temp.setSeven_SCORE_percent("");
                        } else {
                            temp.setSeven_SCORE_percent(dataobj.getString("seven_SCORE_percent"));
                        }

                        dataStores.add(temp);
                    }
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ sis_details: " + e.getMessage());
            }

        }

        _DOWNLOAD_sis_header();
    }
}