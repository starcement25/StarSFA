package com.forcepower.acedns.activity;

import androidx.fragment.app.FragmentActivity;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.Spinner;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.SisEmpData;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.JsonsReceiver;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.Calendar;

public class SisReportActivity extends FragmentActivity implements View.OnClickListener {

    Button btnBack;
    Context mContext;
    Spinner spdate,spCustomer;
    private ArrayList<String> emp;
    private ArrayList<String> month;
    AceDnsDatabase mAceDnsDatabaseHelper;
    ConnectionDetector cd;
    Boolean isInternetPresent = false;
    TextView txtbase_volume,txtVolAchive,txtAchivePercent,txtMonthUniqVistTGT,txtMonthlyUniqVstAchv,txtDealerAppoinmentTGT,txtDealerAppointmentAchv,txtActvDealerPMonth;
    TextView ActiveDealerCurrentMonth,txtSisEarned,txtPenaltyDeducted,txtSisNetEarn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sis_report);

        spdate = findViewById(R.id.spdatemonth);
        spCustomer = findViewById(R.id.spCustomer);
        btnBack = (Button) findViewById(R.id.back);
        txtbase_volume = (TextView) findViewById(R.id.txtbase_volume);
        txtVolAchive = (TextView) findViewById(R.id.txtVolAchive);
        txtAchivePercent = (TextView) findViewById(R.id.txtAchivePercent);
        txtMonthUniqVistTGT = (TextView) findViewById(R.id.txtMonthUniqVistTGT);
        txtMonthlyUniqVstAchv = (TextView) findViewById(R.id.txtMonthlyUniqVstAchv);
        txtDealerAppoinmentTGT = (TextView) findViewById(R.id.txtDealerAppoinmentTGT);
        txtDealerAppointmentAchv = (TextView) findViewById(R.id.txtDealerAppointmentAchv);
        txtActvDealerPMonth = (TextView) findViewById(R.id.txtActvDealerPMonth);
        ActiveDealerCurrentMonth = (TextView) findViewById(R.id.ActiveDealerCurrentMonth);
        txtSisEarned = (TextView) findViewById(R.id.txtSisEarned);
        txtPenaltyDeducted = (TextView) findViewById(R.id.txtPenaltyDeducted);
        txtSisNetEarn = (TextView) findViewById(R.id.txtSisNetEarn);

        mContext = SisReportActivity.this;
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        btnBack = (Button) findViewById(R.id.back);
        btnBack.setOnClickListener(this);
        emp = new ArrayList<String>();

        Calendar calendar = Calendar.getInstance();

        int year = calendar.get(Calendar.YEAR);

        month = mAceDnsDatabaseHelper.getSisMonth();//{ "Jan-"+year, "Feb-"+year,
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

        emp = mAceDnsDatabaseHelper.getEmpName();

        ArrayAdapter adp
                = new ArrayAdapter(
                this,
                android.R.layout.simple_spinner_item,
                emp);

        adp.setDropDownViewResource(
                android.R.layout
                        .simple_spinner_dropdown_item);

        spCustomer.setAdapter(adp);

        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

       /* spCustomer.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

                Toast.makeText(SisReportActivity.this,""+spdate.getSelectedItemPosition(),Toast.LENGTH_LONG).show();

            }
        });*/


        spCustomer.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                String name = spCustomer.getSelectedItem().toString();
                String month = spdate.getSelectedItem().toString();

                int iend = month.indexOf(",");

                String subMonth,subYear;
                if (iend != -1)
                {
                    subMonth= month.substring(0 , iend);
                    subYear = month.substring((iend+1) , month.length());
                }else{
                    subMonth = "";
                    subYear = "";
                }

                //Toast.makeText(SisReportActivity.this,""+subMonth + "--" + Constants.empSisList.get(i).getmEmployeeCode().toString(),Toast.LENGTH_LONG).show();



                try {

                    txtbase_volume.setText("");
                    txtVolAchive.setText("");
                    txtAchivePercent.setText("");
                    txtMonthUniqVistTGT.setText("");
                    txtMonthlyUniqVstAchv.setText("");
                    txtDealerAppoinmentTGT.setText("");
                    txtDealerAppointmentAchv.setText("");
                    txtActvDealerPMonth.setText("");
                    ActiveDealerCurrentMonth.setText("");
                    txtSisEarned.setText("");
                    txtPenaltyDeducted.setText("");
                    txtSisNetEarn.setText("");

                    String s[] = mAceDnsDatabaseHelper.getSisReport(subMonth,subYear,""+Constants.empSisList.get(i).getmEmployeeCode().toString());
                    SisEmpData sed= new SisEmpData();
                    if(s[4].equals("null")){
                        txtbase_volume.setText("");
                    }else{
                        txtbase_volume.setText(""+s[4]);
                    }
                    if(s[5].equals("null")){
                        txtVolAchive.setText("");
                    }else{
                        txtVolAchive.setText(s[5]);
                    }
                    if(s[6].equals("null")){
                        txtAchivePercent.setText("");
                    }else{
                        txtAchivePercent.setText(s[6]);
                    }
                    if(s[7].equals("null")){
                        txtMonthUniqVistTGT.setText("");
                    }else{
                        txtMonthUniqVistTGT.setText(s[7]);
                    }
                    if(s[8].equals("null")){
                        txtMonthlyUniqVstAchv.setText("");
                    }else{
                        txtMonthlyUniqVstAchv.setText(s[8]);
                    }
                    if(s[9].equals("null")){
                        txtDealerAppoinmentTGT.setText("");
                    }else{
                        txtDealerAppoinmentTGT.setText(s[9]);
                    }
                    if(s[10].equals("null")){
                        txtDealerAppointmentAchv.setText("");
                    }else{
                        txtDealerAppointmentAchv.setText(s[10]);
                    }
                    if(s[11].equals("null")){
                        ActiveDealerCurrentMonth.setText("");
                    }else{
                        ActiveDealerCurrentMonth.setText(s[11]);
                    }
                    if(s[12].equals("null")){
                        txtActvDealerPMonth.setText("");
                    }else{
                        txtActvDealerPMonth.setText(s[12]);
                    }
                    if(s[13].equals("null")){
                        txtSisEarned.setText("");
                    }else{
                        txtSisEarned.setText(s[13]);
                    }
                    if(s[14].equals("null")){
                        txtPenaltyDeducted.setText("");
                    }else{
                        txtPenaltyDeducted.setText(s[14]);
                    }
                    if(s[15].equals("null")){
                        txtSisNetEarn.setText("");
                    }else{
                        txtSisNetEarn.setText(s[15]);
                    }




                     //ArrayList<sis_app_vis> visible_gone= mAceDnsDatabaseHelper.getSisAppVisibilityData(Constants.empSisList.get(i).getmEmployeeCode().toString(),"ACTIVE DEALER (PREVIOUS MONTH)");

                    LinearLayout linDealerPreviousMonth,linDealerCurrentMonth,linDealerAppointAchv,linDealerAppointTGT;
                    View viewDealerPreviousMonth,viewDealerCurrentMonth,viewDealerAppointAchv,viewDealerAppointTGT;

                    linDealerPreviousMonth = findViewById(R.id.linDealerPreviousMonth);
                    viewDealerPreviousMonth = findViewById(R.id.viewDealerPreviousMonth);

                    linDealerCurrentMonth = findViewById(R.id.linDealerCurrentMonth);
                    viewDealerCurrentMonth = findViewById(R.id.viewDealerCurrentMonth);

                    linDealerAppointAchv = findViewById(R.id.linDealerAppointAchv);
                    viewDealerAppointAchv = findViewById(R.id.viewDealerAppointAchv);

                    linDealerAppointTGT = findViewById(R.id.linDealerAppointTGT);
                    viewDealerAppointTGT = findViewById(R.id.viewDealerAppointTGT);

                    boolean visible_gone= mAceDnsDatabaseHelper.getSisAppVisibilityData(Constants.empSisList.get(i).getmEmployeeCode().toString(),"ACTIVE DEALER (PREVIOUS MONTH)");

                     if(visible_gone){
                         txtActvDealerPMonth.setVisibility(View.GONE);
                         linDealerPreviousMonth.setVisibility(View.GONE);
                         viewDealerPreviousMonth.setVisibility(View.GONE);
                     }

                    visible_gone= mAceDnsDatabaseHelper.getSisAppVisibilityData(Constants.empSisList.get(i).getmEmployeeCode().toString(),"ACTIVE DEALER (CURRENT MONTH)");

                    if(visible_gone){
                        ActiveDealerCurrentMonth.setVisibility(View.GONE);
                        linDealerCurrentMonth.setVisibility(View.GONE);
                        viewDealerCurrentMonth.setVisibility(View.GONE);
                    }

                    visible_gone= mAceDnsDatabaseHelper.getSisAppVisibilityData(Constants.empSisList.get(i).getmEmployeeCode().toString(),"DEALER APPOINTMENT TGT");

                    if(visible_gone){
                        txtDealerAppoinmentTGT.setVisibility(View.GONE);
                        linDealerAppointTGT.setVisibility(View.GONE);
                        viewDealerAppointTGT.setVisibility(View.GONE);
                    }

                    visible_gone= mAceDnsDatabaseHelper.getSisAppVisibilityData(Constants.empSisList.get(i).getmEmployeeCode().toString(),"DEALER APPOINTMENT ACHV");

                    if(visible_gone){
                        txtDealerAppointmentAchv.setVisibility(View.GONE);
                        viewDealerAppointAchv.setVisibility(View.GONE);
                        linDealerAppointAchv.setVisibility(View.GONE);
                    }

                    //Your task here
                }catch (Exception e)
                {
                    e.printStackTrace();
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });


        cd = new ConnectionDetector(mContext);
        if(Constants.sis_emp_data_startTarget.toUpperCase().matches("YES")){
            isInternetPresent = cd.isConnectingToInternet();
            if (isInternetPresent)
            {
                try{
                    JsonsReceiver.getSisEmpData(mContext);
                }catch(Exception ignored){

                }


            }else{
                // Toast.makeText(mContext, "Please Connect Net For update Weightage", Toast.LENGTH_SHORT).show();
            }
        }

    }

    @Override
    public void onClick(View v) {
        if (v == btnBack) {
            //transDataHelperObj.closeDatabase();
            finish();
        }
    }

    @Override
    public void onPointerCaptureChanged(boolean hasCapture) {

    }
}