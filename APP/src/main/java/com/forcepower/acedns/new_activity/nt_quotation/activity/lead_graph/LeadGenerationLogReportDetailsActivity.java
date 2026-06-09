package com.forcepower.acedns.new_activity.nt_quotation.activity.lead_graph;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.ComponentActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.LeadLogListAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.LogDataSet;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerMasterTableDataSet;
import com.forcepower.acedns.newDataBase.data_set.LeadListMasterTableDataSet;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import org.json.JSONArray;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;
import java.util.TimeZone;

public class LeadGenerationLogReportDetailsActivity extends ComponentActivity implements View.OnClickListener {
    Context mContext;
    private Button backButton;
    private RecyclerView leadLogList;
    private TextView leadIdText, soldToPartyText, shipToPartyText, contactPersonNameText, contactPersonNoText, leadQtyText, createDateText, closeDateText;
    ArrayList<LogDataSet> dataSets = new ArrayList<>();
    LeadLogListAdapter adapter;
    ProgressDialog progressDialog;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_generation_log_report_details);
        mContext = LeadGenerationLogReportDetailsActivity.this;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        init();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
    }

    @SuppressLint("SetTextI18n")
    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        leadLogList = findViewById(R.id.leadLogList);
        leadIdText = findViewById(R.id.leadIdText);
        soldToPartyText = findViewById(R.id.soldToPartyText);
        shipToPartyText = findViewById(R.id.shipToPartyText);
        contactPersonNameText = findViewById(R.id.contactPersonNameText);
        contactPersonNoText = findViewById(R.id.contactPersonNoText);
        leadQtyText = findViewById(R.id.leadQtyText);
        createDateText = findViewById(R.id.createDateText);
        closeDateText = findViewById(R.id.closeDateText);
        dummyLogDetails();

        createDateText.setText("11 Feb 2026 10:20 AM");
        closeDateText.setText("11 March 2026 07:20 AM");
    }

    private void dummyLogDetails() {
        LeadListMasterTableDataSet leadData = mNewDatabaseForSiteLead.getLeadGenerationDetails(getIntent().getStringExtra("lead_id"));
        CustomerMasterTableDataSet soldData = mNewDatabaseForSiteLead.getCustomerDetails(leadData.getSold_to_party());
        CustomerMasterTableDataSet shipData = mNewDatabaseForSiteLead.getCustomerDetails(leadData.getShip_to_party());
        leadIdText.setText(leadData.getLead_generation_id());
        soldToPartyText.setText(soldData.getCust_name());
        shipToPartyText.setText(shipData.getCust_name());
        contactPersonNameText.setText(leadData.getContact_person_name());
        contactPersonNoText.setText(leadData.getContact_number());
        leadQtyText.setText(leadData.getMonth_qty() + " MT");
        new TRANS_LeadLogDetails_AsyncTask(mContext, getIntent().getStringExtra("lead_id")).execute();
    }

    private void setDataInList() {
        try {
            leadLogList.setLayoutManager(new LinearLayoutManager(this));
            adapter = new LeadLogListAdapter(this, dataSets);
            leadLogList.setAdapter(adapter);
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

    public class TRANS_LeadLogDetails_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String leadId;

        public TRANS_LeadLogDetails_AsyncTask(Context context,String leadId) {
            this.mContext = context;
            this.leadId = leadId;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Wait for a while ...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.sbDevUrl + "api/leadmaster_log/?ordering=created_at&lead_generation_id=" + leadId;
                    POST_result = HttpCalling.httpGetCallWithTextResponse(url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                JSONArray arr = new JSONArray(result);
                for(int i=0;i<arr.length();i++){
                    JSONObject obj=arr.getJSONObject(i);
                    LogDataSet a = new LogDataSet();
                    a.setValue(formatDate(obj.getString("created_at")));
                    switch (obj.getInt("lead_quotation_status")){
                        case 1:
                            a.setTitle("Lead Create");
                            break;
                        case 2:
                            a.setTitle("HOS Hold the Lead");
                            break;
                        case 3:
                            a.setTitle("HOS send for Revision");
                            break;
                        case 4:
                            a.setTitle("HOS Reject the Lead");
                            break;
                        case 5:
                            a.setTitle("HOS Approved");
                            break;
                        case 6:
                            a.setTitle("MIS Create Price Approval");
                            break;
                        case 7:
                            a.setTitle("MIS Send to COO");
                            break;
                        case 8:
                            a.setTitle("COO send for Revision");
                            break;
                        case 9:
                            a.setTitle("COO Reject the Lead");
                            break;
                        case 10:
                            a.setTitle("COO Approved");
                            break;
                        case 11:
                            a.setTitle("MIS Send the Lead to SAP");
                            break;
                        case 12:
                            a.setTitle("Lost Order");
                            break;
                        case 13:
                            a.setTitle("SAP create Quotation");
                            break;
                        case 14:
                            a.setTitle("SAP Received PO from Customer");
                            break;
                        case 15:
                            a.setTitle("SAP Create Contract");
                            break;
                        case 16:
                            a.setTitle("SAP Create SO");
                            break;
                    }
                    dataSets.add(a);
                }
                ((LeadGenerationLogReportDetailsActivity) mContext).runOnUiThread(() -> {
                    ((LeadGenerationLogReportDetailsActivity) mContext).setDataInList();
                });
                progressDialogClose();
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }
    public String formatDate(String inputDate) {
        try {
            // Input format (ISO)
            SimpleDateFormat inputFormat = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", Locale.getDefault());
            inputFormat.setTimeZone(TimeZone.getTimeZone("UTC"));

            Date date = inputFormat.parse(inputDate);

            // Output format
            SimpleDateFormat outputFormat = new SimpleDateFormat("dd MMM, yyyy hh:mm a", Locale.getDefault());
            outputFormat.setTimeZone(TimeZone.getDefault()); // convert to local time

            return outputFormat.format(date);

        } catch (Exception e) {
            e.printStackTrace();
            return inputDate;
        }
    }


    // ==================== Loader Dialog ==================== //
    // Progress Dialog open
    private void progressDialogOpen(String title) {
        progressDialog = new ProgressDialog(mContext);
        progressDialog.setMessage(title);
        progressDialog.setCancelable(false);
        progressDialog.show();
    }

    // Progress Dialog update
    private void progressDialogUpdate(String title) {
        progressDialog.setMessage(title);
    }

    // Progress Dialog close
    private void progressDialogClose() {
        ((LeadGenerationLogReportDetailsActivity) mContext).runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }
    // ======================================== //
}