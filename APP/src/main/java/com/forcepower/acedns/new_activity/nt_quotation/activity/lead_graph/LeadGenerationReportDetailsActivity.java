package com.forcepower.acedns.new_activity.nt_quotation.activity.lead_graph;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.activity.ComponentActivity;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerMasterTableDataSet;
import com.forcepower.acedns.newDataBase.data_set.LeadListMasterTableDataSet;

public class LeadGenerationReportDetailsActivity extends ComponentActivity implements View.OnClickListener {
    Context mContext;
    private Button backButton;
    private TextView textLeadId, textSalesOfficerName, textDateStamp, textTimeStamp, textLatitude, textLongitude, textSoldToPartyName, textSoldToPartyCode, textSoldToPartyAddress, textSoldToPartyState,
            textSoldToPartyDistricts, textShipToPartyName, textShipToPartyCode, textShipToPartyAddress, textShipToPartyState, textShipToPartyDistricts, textSegment, textLeadSource, textProductPackaging,
            textTotalPotentialOfSite, textQuotationQuantity, textCurrentBrandUsed, textExpectedRatePerBag, textCurrentPriceStarRsPerBag, textCurrentPriceCompetitorRsPerBag, textContactPersonName, textDesignation,
            textContactNumber, textMailId, textModeOfPayment, textCreditTerms, textAacBlockIsRequiredOrNot, textCategoryTypeOfConstruction, textLeadStatus, textNextVisitDate, textRequirementType, textExWorks,
            textFosSiding, textSalesOfficerRemarks, textAssignedTo, textRequirementTiming;
    private LinearLayout exWorksLayout, fosLayout;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;

    // ==================== Override Function ==================== //
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_generation_report_details);
        mContext = LeadGenerationReportDetailsActivity.this;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        init();
        initLeadDetails();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
    }
    // ======================================== //


    // ==================== init Function ==================== //
    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        exWorksLayout = findViewById(R.id.exWorksLayout);
        fosLayout = findViewById(R.id.fosLayout);
    }

    private void initLeadDetails() {
        textLeadId = findViewById(R.id.textLeadId);
        textSalesOfficerName = findViewById(R.id.textSalesOfficerName);
        textDateStamp = findViewById(R.id.textDateStamp);
        textTimeStamp = findViewById(R.id.textTimeStamp);
        textLatitude = findViewById(R.id.textLatitude);
        textLongitude = findViewById(R.id.textLongitude);
        textSoldToPartyName = findViewById(R.id.textSoldToPartyName);
        textSoldToPartyCode = findViewById(R.id.textSoldToPartyCode);
        textSoldToPartyAddress = findViewById(R.id.textSoldToPartyAddress);
        textSoldToPartyState = findViewById(R.id.textSoldToPartyState);
        textSoldToPartyDistricts = findViewById(R.id.textSoldToPartyDistricts);
        textShipToPartyName = findViewById(R.id.textShipToPartyName);
        textShipToPartyCode = findViewById(R.id.textShipToPartyCode);
        textShipToPartyAddress = findViewById(R.id.textShipToPartyAddress);
        textShipToPartyState = findViewById(R.id.textShipToPartyState);
        textShipToPartyDistricts = findViewById(R.id.textShipToPartyDistricts);
        textSegment = findViewById(R.id.textSegment);
        textLeadSource = findViewById(R.id.textLeadSource);
        textProductPackaging = findViewById(R.id.textProductPackaging);
        textTotalPotentialOfSite = findViewById(R.id.textTotalPotentialOfSite);
        textQuotationQuantity = findViewById(R.id.textQuotationQuantity);
        textCurrentBrandUsed = findViewById(R.id.textCurrentBrandUsed);
        textExpectedRatePerBag = findViewById(R.id.textExpectedRatePerBag);
        textCurrentPriceStarRsPerBag = findViewById(R.id.textCurrentPriceStarRsPerBag);
        textCurrentPriceCompetitorRsPerBag = findViewById(R.id.textCurrentPriceCompetitorRsPerBag);
        textContactPersonName = findViewById(R.id.textContactPersonName);
        textDesignation = findViewById(R.id.textDesignation);
        textContactNumber = findViewById(R.id.textContactNumber);
        textMailId = findViewById(R.id.textMailId);
        textModeOfPayment = findViewById(R.id.textModeOfPayment);
        textCreditTerms = findViewById(R.id.textCreditTerms);
        textAacBlockIsRequiredOrNot = findViewById(R.id.textAacBlockIsRequiredOrNot);
        textCategoryTypeOfConstruction = findViewById(R.id.textCategoryTypeOfConstruction);
        textLeadStatus = findViewById(R.id.textLeadStatus);
        textNextVisitDate = findViewById(R.id.textNextVisitDate);
        textRequirementType = findViewById(R.id.textRequirementType);
        textExWorks = findViewById(R.id.textExWorks);
        textFosSiding = findViewById(R.id.textFosSiding);
        textSalesOfficerRemarks = findViewById(R.id.textSalesOfficerRemarks);
        textAssignedTo = findViewById(R.id.textAssignedTo);
        textRequirementTiming = findViewById(R.id.textRequirementTiming);

        showLeadDetails();
    }
    // ======================================== //


    // ==================== Show Lead ==================== //
    private void showLeadDetails() {
        try {
            LeadListMasterTableDataSet leadData = mNewDatabaseForSiteLead.getLeadGenerationDetails(getIntent().getStringExtra("lead_id"));
            CustomerMasterTableDataSet soldData = mNewDatabaseForSiteLead.getCustomerDetails(leadData.getSold_to_party());
            CustomerMasterTableDataSet shipData = mNewDatabaseForSiteLead.getCustomerDetails(leadData.getShip_to_party());

            textLeadId.setText(leadData.getLead_generation_id());
            textSalesOfficerName.setText(Constants.employeeDetailObject.getEmpName());
            textDateStamp.setText(leadData.getDownload_time());
            textTimeStamp.setText(leadData.getDownload_time());
            textLatitude.setText(leadData.getLatitude());
            textLongitude.setText(leadData.getLongitude());
            textSoldToPartyName.setText(soldData.getCust_name());
            textSoldToPartyCode.setText(leadData.getSold_to_party());
            textSoldToPartyAddress.setText(soldData.getAddress());
            textSoldToPartyState.setText(soldData.getState());
            textSoldToPartyDistricts.setText(soldData.getDistrict());
            textShipToPartyName.setText(shipData.getCust_name());
            textShipToPartyCode.setText(leadData.getShip_to_party());
            textShipToPartyAddress.setText(shipData.getAddress());
            textShipToPartyState.setText(shipData.getState());
            textShipToPartyDistricts.setText(shipData.getDistrict());
            textSegment.setText(leadData.getType_lead());
            textLeadSource.setText(leadData.getLead_type());
            textProductPackaging.setText(leadData.getProduct_packaging());
            textTotalPotentialOfSite.setText(leadData.getQty_req());
            textQuotationQuantity.setText(leadData.getMonth_qty());
            textCurrentBrandUsed.setText(leadData.getCurrent_brand_used());
            textExpectedRatePerBag.setText(leadData.getExp_rate_per_bag());
            textCurrentPriceStarRsPerBag.setText(leadData.getCurrent_price());
            textCurrentPriceCompetitorRsPerBag.setText(leadData.getCurrent_price_competitor());
            textContactPersonName.setText(leadData.getContact_person_name());
            textDesignation.setText(leadData.getDesignation());
            textContactNumber.setText(leadData.getContact_number());
            textMailId.setText(leadData.getMail_id());
            textModeOfPayment.setText(leadData.getMode());
            textCreditTerms.setText(leadData.getCredit_terms());
            textAacBlockIsRequiredOrNot.setText(leadData.getAcc_block_is_required());
            textCategoryTypeOfConstruction.setText(leadData.getCategory_type_construction());
            textLeadStatus.setText(leadData.getLead_status());
            textNextVisitDate.setText(leadData.getNext_visit_date());
            textRequirementType.setText(leadData.getIncoterms());
            textExWorks.setText(leadData.getServing_location());
            textFosSiding.setText(leadData.getServing_location());
            textSalesOfficerRemarks.setText(leadData.getLead_remarks());
            textAssignedTo.setText(leadData.getAssigned_to());
            textRequirementTiming.setText(leadData.getR_timing());

            if (leadData.getIncoterms().equalsIgnoreCase("FOR")) {
                exWorksLayout.setVisibility(GONE);
                fosLayout.setVisibility(GONE);
            } else if (leadData.getIncoterms().equalsIgnoreCase("FOS")) {
                exWorksLayout.setVisibility(GONE);
                fosLayout.setVisibility(VISIBLE);
            } else {
                exWorksLayout.setVisibility(VISIBLE);
                fosLayout.setVisibility(GONE);
            }

        } catch (Exception ignored) {
        }
    }
    // ======================================== //
}