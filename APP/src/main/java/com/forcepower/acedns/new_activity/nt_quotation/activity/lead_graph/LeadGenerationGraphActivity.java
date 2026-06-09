package com.forcepower.acedns.new_activity.nt_quotation.activity.lead_graph;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.activity.ComponentActivity;

import com.forcepower.acedns.R;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.sync.DataForDownloadingLead;
import com.forcepower.acedns.util.ConnectionDetector;

public class LeadGenerationGraphActivity extends ComponentActivity implements View.OnClickListener {
    Context mContext;

    private Button backButton, syncButton;

    private LinearLayout leadFunnelRepresentationButton, contractCreationButton;
    private ImageView leadFunnelIcon, contractCreationIcon;
    private LinearLayout leadFunnelDesign, contractFunnelDesign;

    private LinearLayout leadCreateButton, leadQualifiedButton, quotationCreationButton, quotationApprovedButton, quotationSentButton, customerResponseButton;
    private TextView leadCreateCountLead, leadQualifiedCountLead, quotationCreationCountLead, quotationApprovedCountLead, quotationSentCountLead, poReceivedCountLead, lostOrderCountLead;
    private TextView leadCreateQtyLead, leadQualifiedQtyLead, quotationCreationQtyLead, quotationApprovedQtyLead, quotationSentQtyLead, poReceivedQtyLead, lostOrderQtyLead;

    private LinearLayout contractCreationFunnelButton, soCreatedButton, fulfilmentButton, closedButton;
    private TextView contractCreationFunnelCountLead, soCreatedCountLead, fulfilmentCountLead, closedCountLead;
    private TextView contractCreationFunnelQtyLead, soCreatedQtyLead, fulfilmentQtyLead, closedQtyLead;

    ProgressDialog mProgressDialogAgeing;
    ConnectionDetector cd;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;

    // ==================== Override Function ==================== //
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_generation_graph);
        mContext = LeadGenerationGraphActivity.this;
        cd = new ConnectionDetector(mContext);
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        mNewDatabaseForSiteLead.createDatabaseTableForLeadFunnel();
        initPrimary();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
        if (v == syncButton) {
            mProgressDialogAgeing = new ProgressDialog(mContext);
            mProgressDialogAgeing.setMessage("Downloading Data ...");
            mProgressDialogAgeing.show();

            DataForDownloadingLead mDataForDownloadingLead = new DataForDownloadingLead(mContext);
            mDataForDownloadingLead.addAllFormDataForLead(success -> {
                mProgressDialogAgeing.dismiss();
                showDataInFunnel();
            });
        }

        if (v == leadFunnelRepresentationButton) {
            if (leadFunnelDesign.getVisibility() == View.VISIBLE) {
                leadFunnelDesign.setVisibility(GONE);
            } else {
                leadFunnelDesign.setVisibility(VISIBLE);
            }
            contractFunnelDesign.setVisibility(GONE);
        }
        if (v == contractCreationButton) {
            if (contractFunnelDesign.getVisibility() == View.VISIBLE) {
                contractFunnelDesign.setVisibility(GONE);
            } else {
                contractFunnelDesign.setVisibility(VISIBLE);
            }
            leadFunnelDesign.setVisibility(GONE);
        }

        if (v == leadCreateButton) {
            gotoNextPage(1, "LEAD CREATE", false, false, false);
        }
        if (v == leadQualifiedButton) {
            gotoNextPage(5, "LEAD QUALIFIED", true, false, false);
        }
        if (v == quotationCreationButton) {
            gotoNextPage(6, "QUOTATION CREATION", false, false, false);
        }
        if (v == quotationApprovedButton) {
            gotoNextPage(10, "QUOTATION APPROVED", false, false, false);
        }
        if (v == quotationSentButton) {
            gotoNextPage(11, "QUOTATION SENT", false, false, false);
        }
        if (v == customerResponseButton) {
            gotoNextPage(12, "CUSTOMER RESPONSE", false, false, true);
        }

        if (v == contractCreationFunnelButton) {
            gotoNextPage(15, "CONTRACT CREATION", false, false, false);
        }
        if (v == soCreatedButton) {
            gotoNextPage(16, "SO CREATED", false, false, false);
        }
        if (v == fulfilmentButton) {
            gotoNextPage(16, "FULFILMENT", false, false, false);
        }
        if (v == closedButton) {
            gotoNextPage(16, "CLOSED", false, true, false);
        }
    }
    // ======================================== //


    // ==================== Init Function ==================== //
    private void initPrimary() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);
        syncButton = findViewById(R.id.syncButton);
        syncButton.setOnClickListener(this);

        leadFunnelRepresentationButton = findViewById(R.id.leadFunnelRepresentationButton);
        leadFunnelRepresentationButton.setOnClickListener(this);
        leadFunnelIcon = findViewById(R.id.leadFunnelIcon);
        leadFunnelDesign = findViewById(R.id.leadFunnelDesign);
        leadFunnelDesign.setVisibility(GONE);

        contractCreationButton = findViewById(R.id.contractCreationButton);
        contractCreationButton.setOnClickListener(this);
        contractCreationIcon = findViewById(R.id.contractCreationIcon);
        contractFunnelDesign = findViewById(R.id.contractFunnelDesign);
        contractFunnelDesign.setVisibility(GONE);
        initLeadFunnel();
    }

    private void initLeadFunnel() {
        leadCreateButton = findViewById(R.id.leadCreateButton);
        leadCreateButton.setOnClickListener(this);
        leadCreateCountLead = findViewById(R.id.leadCreateCountLead);
        leadCreateQtyLead = findViewById(R.id.leadCreateQtyLead);

        leadQualifiedButton = findViewById(R.id.leadQualifiedButton);
        leadQualifiedButton.setOnClickListener(this);
        leadQualifiedCountLead = findViewById(R.id.leadQualifiedCountLead);
        leadQualifiedQtyLead = findViewById(R.id.leadQualifiedQtyLead);

        quotationCreationButton = findViewById(R.id.quotationCreationButton);
        quotationCreationButton.setOnClickListener(this);
        quotationCreationCountLead = findViewById(R.id.quotationCreationCountLead);
        quotationCreationQtyLead = findViewById(R.id.quotationCreationQtyLead);

        quotationApprovedButton = findViewById(R.id.quotationApprovedButton);
        quotationApprovedButton.setOnClickListener(this);
        quotationApprovedCountLead = findViewById(R.id.quotationApprovedCountLead);
        quotationApprovedQtyLead = findViewById(R.id.quotationApprovedQtyLead);

        quotationSentButton = findViewById(R.id.quotationSentButton);
        quotationSentButton.setOnClickListener(this);
        quotationSentCountLead = findViewById(R.id.quotationSentCountLead);
        quotationSentQtyLead = findViewById(R.id.quotationSentQtyLead);

        customerResponseButton = findViewById(R.id.customerResponseButton);
        customerResponseButton.setOnClickListener(this);
        poReceivedCountLead = findViewById(R.id.poReceivedCountLead);
        poReceivedQtyLead = findViewById(R.id.poReceivedQtyLead);
        lostOrderCountLead = findViewById(R.id.lostOrderCountLead);
        lostOrderQtyLead = findViewById(R.id.lostOrderQtyLead);
        initContractFunnel();
    }

    private void initContractFunnel() {
        contractCreationFunnelButton = findViewById(R.id.contractCreationFunnelButton);
        contractCreationFunnelButton.setOnClickListener(this);
        contractCreationFunnelCountLead = findViewById(R.id.contractCreationFunnelCountLead);
        contractCreationFunnelQtyLead = findViewById(R.id.contractCreationFunnelQtyLead);

        soCreatedButton = findViewById(R.id.soCreatedButton);
        soCreatedButton.setOnClickListener(this);
        soCreatedCountLead = findViewById(R.id.soCreatedCountLead);
        soCreatedQtyLead = findViewById(R.id.soCreatedQtyLead);

        fulfilmentButton = findViewById(R.id.fulfilmentButton);
        fulfilmentButton.setOnClickListener(this);
        fulfilmentCountLead = findViewById(R.id.fulfilmentCountLead);
        fulfilmentQtyLead = findViewById(R.id.fulfilmentQtyLead);

        closedButton = findViewById(R.id.closedButton);
        closedButton.setOnClickListener(this);
        closedCountLead = findViewById(R.id.closedCountLead);
        closedQtyLead = findViewById(R.id.closedQtyLead);

        showDataInFunnel();
    }
    // ======================================== //


    // ==================== Next Page Function ==================== //
    private void gotoNextPage(int level, String title, boolean isShowLeadStatus, boolean isShowDateFilter, boolean isShowPOandLost) {
        Intent intent = new Intent(LeadGenerationGraphActivity.this, LeadGenerationReportActivity.class);
        intent.putExtra("level", level);
        intent.putExtra("title", title);
        intent.putExtra("isShowLeadStatus", isShowLeadStatus);
        intent.putExtra("isShowDateFilter", isShowDateFilter);
        intent.putExtra("isShowPOandLost", isShowPOandLost);
        startActivity(intent);
    }
    // ======================================== //


    // ==================== Show Function ==================== //
    private void showDataInFunnel() {
        leadCreateCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(1, true)));
        leadCreateQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(1, true)));

        leadQualifiedCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(5, true)));
        leadQualifiedQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(5, true)));

        quotationCreationCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(6, true)));
        quotationCreationQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(6, true)));

        quotationApprovedCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(10, true)));
        quotationApprovedQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(10, true)));

        quotationSentCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(11, true)));
        quotationSentQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(11, true)));

        poReceivedCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(15, true)));
        poReceivedQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(15, true)));

        lostOrderCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(12, false)));
        lostOrderQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(12, false)));


        contractCreationFunnelCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(20, true)));
        contractCreationFunnelQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(20, true)));

        soCreatedCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(21, false)));
        soCreatedQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(21, false)));

        fulfilmentCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(21, false)));
        fulfilmentQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(21, false)));

        closedCountLead.setText(String.valueOf(mNewDatabaseForSiteLead.getCountLeadListMasterTableData(21, false)));
        closedQtyLead.setText(String.valueOf(mNewDatabaseForSiteLead.getTotalQtyListMasterTableData(21, false)));
    }
    // ======================================== //
}