package com.forcepower.acedns.activity.non_auth.main_menu.route_plan;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.Date;

public class RoutePlanLandingActivity extends AceDnsParentActivity {
    LinearLayout approovalLayout, deviationLayout, createLayout;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    TextView txtVersion;
    ImageView imgLogo;
    Button btnCreatePlan, btnDeviationReq, btnApproovePlan, btnBack;
    SimpleDateFormat dateFormat;
    RoutePlanDetails routePlanDetailsObj;

    @SuppressLint("SimpleDateFormat")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_routeplan_landing);
        RegisterActivities.registerActivity(this);

        mContext = RoutePlanLandingActivity.this;
        setupDataHelperObj = new AceDnsDatabase(mContext);

        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        initView();
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void initView() {
        imgLogo = findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }
        txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        routePlanDetailsObj = setupDataHelperObj.getRoutePlanDetailsObj();
        /*
         * Check for RPlan Approval
         */
        approovalLayout = findViewById(R.id.approove_layout);
        if (routePlanDetailsObj.getRoutePlanApproval().equalsIgnoreCase("yes")) {
            approovalLayout.setVisibility(View.VISIBLE);
        } else {
            approovalLayout.setVisibility(View.GONE);
        }
        /*
         * Check for RPlan Deviation
         */
        deviationLayout = findViewById(R.id.deviation_layout);
        if (routePlanDetailsObj.getRoutePlanDeviation().equalsIgnoreCase("yes")) {
            deviationLayout.setVisibility(View.VISIBLE);
        } else {
            deviationLayout.setVisibility(View.GONE);
        }
        /*
         * Check for RPlan Create
         */
        createLayout = findViewById(R.id.create_layout);
        if (routePlanDetailsObj.getRoutePlanAccessPeriod().equalsIgnoreCase("yes")) {
            String[] accessPeriodArray = setupDataHelperObj.getRoutePlanAccessPeriod();
            if (accessPeriodArray != null && !accessPeriodArray[0].isEmpty() && !accessPeriodArray[1].isEmpty()) {
                try {
                    Date accessStartDate = new SimpleDateFormat("dd-MM-yyyy").parse(accessPeriodArray[0]);
                    Date accessEndDate = new SimpleDateFormat("dd-MM-yyyy").parse(accessPeriodArray[1]);
                    Date todayDate = new SimpleDateFormat("dd-MM-yyyy").parse(new SimpleDateFormat("dd-MM-yyyy").format(new Date()));
                    assert todayDate != null;
                    if (todayDate.after(accessStartDate) && todayDate.before(accessEndDate)) {
                        goToRoutPlanCreationPage();
                    } else {
                        createLayout.setVisibility(View.GONE);
                    }
                } catch (Exception e) {
                    System.out.println("Exception::::::::::" + e);
                }
            } else {
                createLayout.setVisibility(View.GONE);
            }
        } else {
            if (!routePlanDetailsObj.getRoutePlanDeviation().equalsIgnoreCase("yes") && !routePlanDetailsObj.getRoutePlanApproval().equalsIgnoreCase("yes")) {
                goToRoutPlanCreationPage();
            }
        }


        txtVersion = findViewById(R.id.txt_version);
        imgLogo = findViewById(R.id.imagelogo);
        btnBack =  findViewById(R.id.back);
        btnCreatePlan =  findViewById(R.id.btn_create);
        btnDeviationReq =  findViewById(R.id.btn_deviation);
        btnApproovePlan =  findViewById(R.id.btn_approove);
        btnBack.setOnClickListener(RoutePlanLandingActivity.this);
        btnCreatePlan.setOnClickListener(RoutePlanLandingActivity.this);
        btnDeviationReq.setOnClickListener(RoutePlanLandingActivity.this);
        btnApproovePlan.setOnClickListener(RoutePlanLandingActivity.this);
    }

    public void onClick(View clkdView) {
        if (clkdView == btnBack) {
            finish();
        } else if (clkdView == btnCreatePlan) {
            goToRoutPlanCreationPage();
        } else if (clkdView == btnDeviationReq) {
            Intent intent = new Intent(RoutePlanLandingActivity.this, RoutePlanActivity.class);
            intent.putExtra("MODE", "DEVIATE");
            startActivity(intent);
        } else if (clkdView == btnApproovePlan) {
            Utils.showToast(RoutePlanLandingActivity.this, "Feature not enabled");
        }
    }

    public void goToRoutPlanCreationPage() {
        Intent intent;
        if (routePlanDetailsObj.getDistributorRoutePlanning().equalsIgnoreCase("yes")) {
            intent = new Intent(RoutePlanLandingActivity.this, ActivityDistributorRoutePlan.class);
            intent.putExtra("MODE", "CREATE");
        } else if (routePlanDetailsObj.getDistributorRoutePlanningMultiple().equalsIgnoreCase("yes")) {
            intent = new Intent(RoutePlanLandingActivity.this, ActivityMultipleDistributorRoutePlan.class);
            intent.putExtra("MODE", "CREATE");
        } else {
            if (routePlanDetailsObj.getRouteCustomerPlanning().equalsIgnoreCase("yes")) {
                intent = new Intent(RoutePlanLandingActivity.this, ActivityRoutePlan.class);
                intent.putExtra("MODE", "CREATE");
            } else {
                intent = new Intent(RoutePlanLandingActivity.this, RoutePlanActivity.class);
                intent.putExtra("MODE", "CREATE");
            }
        }
        finish();
        startActivity(intent);
    }
}
