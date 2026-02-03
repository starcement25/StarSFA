package com.forcepower.acedns.new_activity.khoj;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.TextView;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;

import org.json.JSONException;
import org.json.JSONObject;

public class KhojDetailsActivity extends AceDnsParentActivity implements View.OnClickListener {

    public Button backButton;
    public TextView customerName, customerPhoneNumber, meetPersonName, meetPersonContactNumber, siteName, routeName, branchName, stateName, districtName, fullAddress, contractorName, contractorPhoneNumber,
            engineerName, engineerPhoneNumber, regdStatus, siteSegment, projectSegment, constructionType, currentStage, cementBrand, sitePotential, pricePerBag, consumedTillNow, estimatedRequirement,
            builtUpArea, decisionMaker, productDemo, visitType, deliveryDate, orderQty, remarks, dealerName, approvedBy;


    Context mContext;

    String surveymenudetails, mStartDate, mEndDate, mFStartDate, mFEndDate;
    int SELECTION;

    @SuppressLint("SetTextI18n")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_khoj_details);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

        mContext = KhojDetailsActivity.this;

        init();

        surveymenudetails = getIntent().getStringExtra("SURVEYSUBMENUDETAILS");
        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");
        mFStartDate = getIntent().getStringExtra("FSTARTDATE");
        mFEndDate = getIntent().getStringExtra("FENDDATE");
        String data = getIntent().getStringExtra("DataSet");


        try {
            JSONObject obj1 = new JSONObject(data);
            Log.d("TAG", "_DOWNLOAD_ onCreate: " + obj1.getJSONObject("obj").getJSONObject("nameValuePairs").getString("id"));
            JSONObject obj = obj1.getJSONObject("obj").getJSONObject("nameValuePairs");

            String cust_name = obj.isNull("cust_name") ? "- - -" : obj.getString("cust_name");
            String site_code = obj.isNull("site_code") ? "XXXXXXXXXX" : obj.getString("site_code").split("-")[0];
            String meeting_person_type = obj.isNull("meeting_person_type") ? "- - -" : obj.getString("meeting_person_type");
            String meeting_person_phone = obj.isNull("meeting_person_phone") ? "XXXXXXXXXX" : obj.getString("meeting_person_phone");
            String site_name = obj.isNull("site_name") ? "- - -" : obj.getString("site_name");
            String route_name = obj.isNull("route_name") ? "- - -" : obj.getString("route_name");
            String branch_name = obj.isNull("branch_name") ? "- - -" : obj.getString("branch_name");
            String state = obj.isNull("state") ? "- - -" : obj.getString("state");
            String district = obj.isNull("district") ? "- - -" : obj.getString("district");
            String address = obj.isNull("address") ? "- - -" : obj.getString("address");
            String contractor_name = obj.isNull("contractor_name") ? "- - -" : obj.getString("contractor_name");
            String contractor_phone = obj.isNull("contractor_phone") ? "XXXXXXXXXX" : obj.getString("contractor_phone");
            String engineer_name = obj.isNull("engineer_name") ? "- - -" : obj.getString("engineer_name");
            String engineer_phone = obj.isNull("engineer_phone") ? "XXXXXXXXXX" : obj.getString("engineer_phone");
            String engg_reg_star_stellar = obj.isNull("engg_reg_star_stellar") ? "- - -" : obj.getString("engg_reg_star_stellar");
            String site_segment = obj.isNull("site_segment") ? "- - -" : obj.getString("site_segment");
            String project_segment = obj.isNull("project_segment") ? "- - -" : obj.getString("project_segment");
            String type_of_construction = obj.isNull("type_of_construction") ? "- - -" : obj.getString("type_of_construction");
            String construction_stage = obj.isNull("construction_stage") ? "- - -" : obj.getString("construction_stage");
            String cement_brand = obj.isNull("cement_brand") ? "- - -" : obj.getString("cement_brand");
            String site_potential = obj.isNull("site_potential") ? "- - -" : obj.getString("site_potential");
            String price_per_bag = obj.isNull("price_per_bag") ? "- - -" : obj.getString("price_per_bag")+"/-";
            String consumed_till_date = obj.isNull("consumed_till_date") ? "- - -" : obj.getString("consumed_till_date");
            String estimated_req = obj.isNull("estimated_req") ? "- - -" : obj.getString("estimated_req");
            String built_up_area = obj.isNull("built_up_area") ? "- - -" : obj.getString("built_up_area");
            String decision_maker = obj.isNull("decision_maker") ? "- - -" : obj.getString("decision_maker");
            String product_demo = obj.isNull("product_demo") ? "- - -" : obj.getString("product_demo");
            String visit_type = obj.isNull("visit_type") ? "- - -" : obj.getString("visit_type");
            String visit_sub_type = obj.isNull("visit_sub_type") ? "- - -" : obj.getString("visit_sub_type");
            String date_of_delivery = obj.isNull("date_of_delivery") ? "- - -" : obj.getString("date_of_delivery");
            String bags_ordered = obj.isNull("bags_ordered") ? "- - -" : obj.getString("bags_ordered");
            String remarks1 = obj.isNull("remarks") ? "- - -" : obj.getString("remarks");
            String rssd_name = obj.isNull("rssd_name") ? "- - -" : obj.getString("rssd_name");
            String approved_by_name = obj.isNull("approved_by_name") ? "- - -" : obj.getString("approved_by_name");

            customerName.setText(cust_name);
            customerPhoneNumber.setText("+91 " + site_code);
            meetPersonName.setText(meeting_person_type);
            meetPersonContactNumber.setText("+91 " + meeting_person_phone);
            siteName.setText(site_name);
            routeName.setText(route_name);
            branchName.setText(branch_name);
            stateName.setText(state);
            districtName.setText(district);
            fullAddress.setText(address);
            contractorName.setText(contractor_name);
            contractorPhoneNumber.setText("+91 " + contractor_phone);
            engineerName.setText(engineer_name);
            engineerPhoneNumber.setText("+91 " + engineer_phone);
            regdStatus.setText(engg_reg_star_stellar);
            siteSegment.setText(site_segment);
            projectSegment.setText(project_segment);
            constructionType.setText(type_of_construction);
            currentStage.setText(construction_stage);
            cementBrand.setText(cement_brand);
            sitePotential.setText(site_potential + " Bags");
            pricePerBag.setText(price_per_bag);
            consumedTillNow.setText(consumed_till_date);
            estimatedRequirement.setText(estimated_req + " Bags");
            builtUpArea.setText(built_up_area + " Sq. ft");
            decisionMaker.setText(decision_maker);
            productDemo.setText(product_demo);
            visitType.setText(visit_type + ", " + visit_sub_type);
            deliveryDate.setText(date_of_delivery);
            orderQty.setText(bags_ordered + " Bags");
            remarks.setText(remarks1);
            dealerName.setText(rssd_name);
            approvedBy.setText(approved_by_name);
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }
    }


    @Override
    public void onClick(View view) {
        if (view == backButton) {
            finish();
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);

        customerName = findViewById(R.id.customerName);
        customerPhoneNumber = findViewById(R.id.customerPhoneNumber);
        meetPersonName = findViewById(R.id.meetPersonName);
        meetPersonContactNumber = findViewById(R.id.meetPersonContactNumber);
        siteName = findViewById(R.id.siteName);
        routeName = findViewById(R.id.routeName);
        branchName = findViewById(R.id.branchName);
        stateName = findViewById(R.id.stateName);
        districtName = findViewById(R.id.districtName);
        fullAddress = findViewById(R.id.fullAddress);
        contractorName = findViewById(R.id.contractorName);
        contractorPhoneNumber = findViewById(R.id.contractorPhoneNumber);
        engineerName = findViewById(R.id.engineerName);
        engineerPhoneNumber = findViewById(R.id.engineerPhoneNumber);
        regdStatus = findViewById(R.id.regdStatus);
        siteSegment = findViewById(R.id.siteSegment);
        projectSegment = findViewById(R.id.projectSegment);
        constructionType = findViewById(R.id.constructionType);
        currentStage = findViewById(R.id.currentStage);
        cementBrand = findViewById(R.id.cementBrand);
        sitePotential = findViewById(R.id.sitePotential);
        pricePerBag = findViewById(R.id.pricePerBag);
        consumedTillNow = findViewById(R.id.consumedTillNow);
        estimatedRequirement = findViewById(R.id.estimatedRequirement);
        builtUpArea = findViewById(R.id.builtUpArea);
        decisionMaker = findViewById(R.id.decisionMaker);
        productDemo = findViewById(R.id.productDemo);
        visitType = findViewById(R.id.visitType);
        deliveryDate = findViewById(R.id.deliveryDate);
        orderQty = findViewById(R.id.orderQty);
        remarks = findViewById(R.id.remarks);
        dealerName = findViewById(R.id.dealerName);
        approvedBy = findViewById(R.id.approvedBy);

        backButton.setOnClickListener(this);
    }
}