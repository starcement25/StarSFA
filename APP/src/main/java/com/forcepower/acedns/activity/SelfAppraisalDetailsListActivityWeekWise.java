package com.forcepower.acedns.activity;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SelfAppraisalCustomerWiseListAdapter;
import com.forcepower.acedns.bean.SelfAppraisalDetailsBranchWise;
import com.forcepower.acedns.bean.SelfAppraisalDetailsCustomerWise;
import com.forcepower.acedns.bean.SelfAppraisalDetailsProductGroupWise;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class SelfAppraisalDetailsListActivityWeekWise extends AceDnsParentActivity {
    String type = "", month = "",week="";
    TextView appraisalDetails;
    ArrayList<SelfAppraisalDetailsCustomerWise> selfAppraisalListCustomerWise;
    ArrayList<SelfAppraisalDetailsBranchWise> selfAppraisalListBranchWise;
    ArrayList<SelfAppraisalDetailsProductGroupWise> selfAppraisalListProductGroupWise;
    AceDnsDatabase mAceDnsDatabase;
    ListView selfAppraisalDetailsList;
    Context mContext;
    ImageView imgLogo;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_self_appraisal_details_list);
        selfAppraisalDetailsList = (ListView) findViewById(R.id.selfAppraisalDetailsList);
        mContext = this;
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Intent intent = getIntent();
//        type = intent.getStringExtra("type");
        week = intent.getStringExtra("week");
        month = intent.getStringExtra("month");
        appraisalDetails = (TextView) findViewById(R.id.appraisalDetails);

        appraisalDetails.setText(" Tar-vs-Achv. for " + calculateMonthString()+" "+week);
//        String monthQueryForDB = calculateMonthString();
        String monthQueryForDB = month;
        selfAppraisalListCustomerWise = mAceDnsDatabase.getEmployeeWiseWeeklyTargetForSingleMonth(month,week);
        final SelfAppraisalCustomerWiseListAdapter adapter = new SelfAppraisalCustomerWiseListAdapter(mContext,
                R.layout.self_appraisal_list_item, selfAppraisalListCustomerWise);
        selfAppraisalDetailsList.setAdapter(adapter);

    }

    private String calculateMonthString() {
        String monthLocal = "Jan";
        if (month.matches("01")) {
            monthLocal = "Jan";
        } else if (month.matches("02")) {
            monthLocal = "Feb";
        } else if (month.matches("03")) {
            monthLocal = "Mar";
        } else if (month.matches("04")) {
            monthLocal = "Apr";
        } else if (month.matches("05")) {
            monthLocal = "May";
        } else if (month.matches("06")) {
            monthLocal = "June";
        } else if (month.matches("07")) {
            monthLocal = "July";
        } else if (month.matches("08")) {
            monthLocal = "Aug";
        } else if (month.matches("09")) {
            monthLocal = "Sept";
        } else if (month.matches("10")) {
            monthLocal = "Oct";
        } else if (month.matches("11")) {
            monthLocal = "Nov";
        } else if (month.matches("12")) {
            monthLocal = "Dec";
        }
        return monthLocal;
    }

    @Override
    public boolean onKeyUp(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            finish();
        }
        return super.onKeyUp(keyCode, event);
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

    public void finishCurrentActivity(View v) {
        finish();
    }

}
