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
import com.forcepower.acedns.adapter.SelfAppraisalBranchWiseListAdapter;
import com.forcepower.acedns.adapter.SelfAppraisalCustomerWiseListAdapter;
import com.forcepower.acedns.adapter.SelfAppraisalProductGroupWiseListAdapter;
import com.forcepower.acedns.bean.SelfAppraisalDetailsBranchWise;
import com.forcepower.acedns.bean.SelfAppraisalDetailsCustomerWise;
import com.forcepower.acedns.bean.SelfAppraisalDetailsProductGroupWise;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static com.forcepower.acedns.activity.SelfAppraisalLandingActivity.chosenVertical;

public class SelfAppraisalDetailsListActivity extends AceDnsParentActivity {
    String type = "", month = "";
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
        type = intent.getStringExtra("type");
        month = intent.getStringExtra("month");
        appraisalDetails = (TextView) findViewById(R.id.appraisalDetails);

        appraisalDetails.setText(type + " Tar-vs-Achv. for " + calculateMonthString());
//        String monthQueryForDB = calculateMonthString();
        String monthQueryForDB = month;
        if (type.matches("Customer wise") || type.matches("Employee wise"))
        {

            if(type.matches("Customer wise"))
            {
                selfAppraisalListCustomerWise = mAceDnsDatabase.getCustomerEmployeeWiseTargetForSingleMonth(monthQueryForDB, Constants.selectedCustomer.getCustomerCode(),chosenVertical,"self_appraisal_summary");
            }
            else
            {
                selfAppraisalListCustomerWise = mAceDnsDatabase.getCustomerEmployeeWiseTargetForSingleMonth(monthQueryForDB, Constants.selectedCustomer.getCustomerCode(),chosenVertical,"self_appraisal_emp_wise");
            }
//            targetListFromDB = mAceDnsDatabase.getTargetForAllMonthsCustomerWise("self_appraisal_summary", Constants.selectedCustomer.getCustomerCode(),chosenVertical);
            final SelfAppraisalCustomerWiseListAdapter adapter = new SelfAppraisalCustomerWiseListAdapter(mContext,
                    R.layout.self_appraisal_list_item, selfAppraisalListCustomerWise);
            selfAppraisalDetailsList.setAdapter(adapter);
        }

        else if (type.matches("Branch wise"))
        {
            TextView customerBranchNameHeader = (TextView) findViewById(R.id.customerBranchNameHeader);
            customerBranchNameHeader.setText("Branch Name");
            selfAppraisalListBranchWise = mAceDnsDatabase.getBranchWiseTargetForSingleMonth(monthQueryForDB);
            final SelfAppraisalBranchWiseListAdapter adapter = new SelfAppraisalBranchWiseListAdapter(mContext,
                    R.layout.self_appraisal_list_item, selfAppraisalListBranchWise);
            selfAppraisalDetailsList.setAdapter(adapter);
        }
        else if (type.matches("Product group wise"))
        {
            TextView customerBranchNameHeader = (TextView) findViewById(R.id.customerBranchNameHeader);
            customerBranchNameHeader.setText("Product Group-Employee");
            selfAppraisalListProductGroupWise = mAceDnsDatabase.getProductGroupWiseTargetForSingleMonth(monthQueryForDB);
            final SelfAppraisalProductGroupWiseListAdapter adapter = new SelfAppraisalProductGroupWiseListAdapter(mContext,
                    R.layout.self_appraisal_list_item, selfAppraisalListProductGroupWise);
            selfAppraisalDetailsList.setAdapter(adapter);
        }

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
