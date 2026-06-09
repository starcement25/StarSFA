package com.forcepower.acedns.new_activity.nt_quotation.activity.lead_graph;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.activity.ComponentActivity;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.LeadListAdapter;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.LeadListMasterTableDataSet;

import java.util.ArrayList;
import java.util.Calendar;

public class LeadGenerationReportActivity extends ComponentActivity implements View.OnClickListener {
    Context mContext;
    private Button backButton;
    private RecyclerView leadGenerationList;
    private TextView headerTitle;

    // Lead Status Wise Filter (HOT-WARM-COLD)
    private LinearLayout leadStatusFilterLayout;
    private LinearLayout hotLeadButton, warmLeadButton, coldLeadButton;
    private TextView hotLeadButtonText, warmLeadButtonText, coldLeadButtonText;

    // Customer Response Wise Filter (PO RECEIVE-LOST ORDER)
    private LinearLayout customerResponseFilterLayout;
    private LinearLayout poReceivedButton, lostOrderButton;
    private TextView poReceivedButtonText, lostOrderButtonText;

    // Close Fulfilled Wise Filter (AGAINST DATE FILTER)
    private LinearLayout closedFilterLayout;
    private TextView totalLeadText;
    private TextView last30DaysCountText, last3MonthsCountText, ytdCountText;
    private LinearLayout dateWiseFilterButton;
    private LinearLayout startDateButton, endDateButton;
    private TextView startDateButtonText, endDateButtonText;

    // Lost Lead Popup
    private LinearLayout lostOrderReasonPopupLayout;
    private EditText editTextLostOrderReason;
    private Button submitButton;
    private ImageView closeLostLeadReasonButton;


    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    ArrayList<LeadListMasterTableDataSet> allLeadList = new ArrayList<>();
    ArrayList<LeadListMasterTableDataSet> showLeadList = new ArrayList<>();
    LeadListMasterTableDataSet lostLeadDetails;
    LeadListAdapter adapter;
    int hotCount = 0, warmCount = 0, coldCount = 0, lostOrderCount = 0;
    int totalLeadCount = 0, last30DaysCount = 0, last3MonthsCount = 0, ytdCount = 0;
    String startDate = "", endDate = "";

    // ==================== Override Function ==================== //
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_generation_report);
        mContext = LeadGenerationReportActivity.this;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        allLeadList = mNewDatabaseForSiteLead.getAllLeadListMasterTableData(getIntent().getIntExtra("level", 0));
        primaryFunction();
        initPrimary();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }

        if (v == hotLeadButton) {
            getLeadListDataLeadStatusWise("hot");
        }
        if (v == warmLeadButton) {
            getLeadListDataLeadStatusWise("warm");
        }
        if (v == coldLeadButton) {
            getLeadListDataLeadStatusWise("cold");
        }

        if (v == poReceivedButton) {
            getLeadListDataCustomerResponseWise("po");
        }
        if (v == lostOrderButton) {
            getLeadListDataCustomerResponseWise("lost");
        }

        if (v == dateWiseFilterButton) {
            filterData();
        }
        if (v == startDateButton) {
            dateTimePicker("start_date");
        }
        if (v == endDateButton) {
            dateTimePicker("end_date");
        }

        if(v==submitButton){

        }
        if(v==closeLostLeadReasonButton){
            lostOrderReasonPopupLayout.setVisibility(GONE);
        }
    }
    // ======================================== //


    // ==================== init Function ==================== //
    // Primary Init Function
    private void initPrimary() {
        backButton = findViewById(R.id.backButton);
        leadGenerationList = findViewById(R.id.leadGenerationList);
        headerTitle = findViewById(R.id.headerTitle);
        headerTitle.setText(getIntent().getStringExtra("title"));
        backButton.setOnClickListener(this);

        initLeadStatus();
        initCustomerResponse();
        initCloseResponse();
        initLostLeadPopup();
        if (!getIntent().getBooleanExtra("isShowLeadStatus", false) && !getIntent().getBooleanExtra("isShowDateFilter", false) && !getIntent().getBooleanExtra("isShowPOandLost", false)) {
            showLeadList = new ArrayList<>(allLeadList);
            setDataInList();
        }

        getLeadListDataLeadStatusWise("hot");
        getLeadListDataCustomerResponseWise("po");
    }

    // Lead Status Wise Filter (HOT-WARM-COLD)
    private void initLeadStatus() {
        leadStatusFilterLayout = findViewById(R.id.leadStatusFilterLayout);
        hotLeadButton = findViewById(R.id.hotLeadButton);
        warmLeadButton = findViewById(R.id.warmLeadButton);
        coldLeadButton = findViewById(R.id.coldLeadButton);
        hotLeadButtonText = findViewById(R.id.hotLeadButtonText);
        warmLeadButtonText = findViewById(R.id.warmLeadButtonText);
        coldLeadButtonText = findViewById(R.id.coldLeadButtonText);

        hotLeadButton.setOnClickListener(this);
        warmLeadButton.setOnClickListener(this);
        coldLeadButton.setOnClickListener(this);
        if (getIntent().getBooleanExtra("isShowLeadStatus", false)) {
            leadStatusFilterLayout.setVisibility(VISIBLE);
            getLeadListDataLeadStatusWise("hot");
        } else {
            leadStatusFilterLayout.setVisibility(GONE);
        }
    }

    // Customer Response Wise Filter (PO RECEIVE-LOST ORDER)
    private void initCustomerResponse() {
        customerResponseFilterLayout = findViewById(R.id.customerResponseFilterLayout);
        poReceivedButton = findViewById(R.id.poReceivedButton);
        lostOrderButton = findViewById(R.id.lostOrderButton);
        poReceivedButtonText = findViewById(R.id.poReceivedButtonText);
        lostOrderButtonText = findViewById(R.id.lostOrderButtonText);

        poReceivedButton.setOnClickListener(this);
        lostOrderButton.setOnClickListener(this);

        if (getIntent().getBooleanExtra("isShowPOandLost", false)) {
            customerResponseFilterLayout.setVisibility(VISIBLE);
            getLeadListDataCustomerResponseWise("po");
        } else {
            customerResponseFilterLayout.setVisibility(GONE);
        }
    }

    // Close Fulfilled Wise Filter (AGAINST DATE FILTER)
    private void initCloseResponse() {
        closedFilterLayout = findViewById(R.id.closedFilterLayout);

        totalLeadText = findViewById(R.id.totalLeadText);
        last30DaysCountText = findViewById(R.id.last30DaysCountText);
        last3MonthsCountText = findViewById(R.id.last3MonthsCountText);
        ytdCountText = findViewById(R.id.ytdCountText);

        dateWiseFilterButton = findViewById(R.id.dateWiseFilterButton);
        startDateButton = findViewById(R.id.startDateButton);
        endDateButton = findViewById(R.id.endDateButton);
        startDateButtonText = findViewById(R.id.startDateButtonText);
        endDateButtonText = findViewById(R.id.endDateButtonText);

        dateWiseFilterButton.setOnClickListener(this);
        startDateButton.setOnClickListener(this);
        endDateButton.setOnClickListener(this);

        if (getIntent().getBooleanExtra("isShowDateFilter", false)) {
            closedFilterLayout.setVisibility(VISIBLE);
        } else {
            closedFilterLayout.setVisibility(GONE);
        }
        inactiveAllClose();

        showLeadList = new ArrayList<>(allLeadList);
        setDataInList();
    }

    // Lost Lead Popup
    private void initLostLeadPopup(){
        lostOrderReasonPopupLayout=findViewById(R.id.lostOrderReasonPopupLayout);
        editTextLostOrderReason=findViewById(R.id.editTextLostOrderReason);
        submitButton=findViewById(R.id.submitButton);
        closeLostLeadReasonButton=findViewById(R.id.closeLostLeadReasonButton);

        lostOrderReasonPopupLayout.setVisibility(GONE);

        submitButton.setOnClickListener(this);
        closeLostLeadReasonButton.setOnClickListener(this);
    }
    // ======================================== //


    // ==================== Primary Function ==================== //
    private void primaryFunction() {
        for (int i = 0; i < allLeadList.size(); i++) {
            if (allLeadList.get(i).getLead_status().equalsIgnoreCase("hot")) {
                hotCount++;
            } else if (allLeadList.get(i).getLead_status().equalsIgnoreCase("warm")) {
                warmCount++;
            } else {
                coldCount++;
            }
            if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("12")) {
                lostOrderCount++;
            }
        }
        totalLeadCount = allLeadList.size();
        last30DaysCount = countLast30DaysLeads();
        last3MonthsCount = countLast90DaysLeads();
        ytdCount = countLast365DaysLeads();
    }

    // Lead Status Wise Filter (HOT-WARM-COLD)
    @SuppressLint("SetTextI18n")
    private void inactiveAllLeadStatus() {
        hotLeadButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.set_blank));
        warmLeadButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.set_blank));
        coldLeadButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.set_blank));
        hotLeadButtonText.setTextColor(Color.parseColor("#000000"));
        warmLeadButtonText.setTextColor(Color.parseColor("#000000"));
        coldLeadButtonText.setTextColor(Color.parseColor("#000000"));
        hotLeadButtonText.setText("Hot (" + hotCount + ")");
        warmLeadButtonText.setText("warm (" + warmCount + ")");
        coldLeadButtonText.setText("cold (" + coldCount + ")");
    }

    // Customer Response Wise Filter (PO RECEIVE-LOST ORDER)
    @SuppressLint("SetTextI18n")
    private void inactiveAllCustomerResponse() {
        poReceivedButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.set_blank));
        lostOrderButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.set_blank));
        poReceivedButtonText.setTextColor(Color.parseColor("#000000"));
        lostOrderButtonText.setTextColor(Color.parseColor("#000000"));
        lostOrderButtonText.setText("Lost Order (" + lostOrderCount + ")");
    }

    // Close Fulfilled Wise Filter (AGAINST DATE FILTER)
    @SuppressLint("SetTextI18n")
    private void inactiveAllClose() {
        totalLeadText.setText("Total Lead : " + totalLeadCount);
        last30DaysCountText.setText(last30DaysCount + "");
        last3MonthsCountText.setText(last3MonthsCount + "");
        ytdCountText.setText(ytdCount + "");
    }
    // ======================================== //


    // ==================== Lead Count Function ==================== //
    // Count Last 30 Days Leads
    private int countLast30DaysLeads() {
        int count = 0;
        try {
            Calendar startCal = Calendar.getInstance();
            startCal.add(Calendar.DAY_OF_YEAR, -30);
            startCal.set(Calendar.HOUR_OF_DAY, 0);
            startCal.set(Calendar.MINUTE, 0);
            startCal.set(Calendar.SECOND, 0);
            startCal.set(Calendar.MILLISECOND, 0);

            Calendar endCal = Calendar.getInstance(); // today


            for (int i = 0; i < allLeadList.size(); i++) {
                LeadListMasterTableDataSet obj = allLeadList.get(i);
                String downloadTimeStr = obj.getDownload_time().split(" ")[0]; // get date part only
                String[] arr = downloadTimeStr.split("-");

                Calendar createdCal = Calendar.getInstance();
                createdCal.set(Integer.parseInt(arr[0]), Integer.parseInt(arr[1]) - 1, Integer.parseInt(arr[2]));
                createdCal.set(Calendar.HOUR_OF_DAY, 0);
                createdCal.set(Calendar.MINUTE, 0);
                createdCal.set(Calendar.SECOND, 0);
                createdCal.set(Calendar.MILLISECOND, 0);

                if (!createdCal.before(startCal) && !createdCal.after(endCal)) {
                    count++;
                }
            }
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ countLast30DaysLeads: " + e.getMessage());
        }
        return count;
    }

    // Count Last 90 Days Leads
    private int countLast90DaysLeads() {
        int count = 0;
        try {
            Calendar startCal = Calendar.getInstance();
            startCal.add(Calendar.DAY_OF_YEAR, -90);
            startCal.set(Calendar.HOUR_OF_DAY, 0);
            startCal.set(Calendar.MINUTE, 0);
            startCal.set(Calendar.SECOND, 0);
            startCal.set(Calendar.MILLISECOND, 0);

            Calendar endCal = Calendar.getInstance(); // today


            for (int i = 0; i < allLeadList.size(); i++) {
                LeadListMasterTableDataSet obj = allLeadList.get(i);
                String downloadTimeStr = obj.getDownload_time().split(" ")[0]; // get date part only
                String[] arr = downloadTimeStr.split("-");

                Calendar createdCal = Calendar.getInstance();
                createdCal.set(Integer.parseInt(arr[0]), Integer.parseInt(arr[1]) - 1, Integer.parseInt(arr[2]));
                createdCal.set(Calendar.HOUR_OF_DAY, 0);
                createdCal.set(Calendar.MINUTE, 0);
                createdCal.set(Calendar.SECOND, 0);
                createdCal.set(Calendar.MILLISECOND, 0);

                if (!createdCal.before(startCal) && !createdCal.after(endCal)) {
                    count++;
                }
            }
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ countLast90DaysLeads: " + e.getMessage());
        }
        return count;
    }

    // Count Last 365 Days Leads
    private int countLast365DaysLeads() {
        int count = 0;
        try {
            Calendar startCal = Calendar.getInstance();
            startCal.add(Calendar.DAY_OF_YEAR, -365);
            startCal.set(Calendar.HOUR_OF_DAY, 0);
            startCal.set(Calendar.MINUTE, 0);
            startCal.set(Calendar.SECOND, 0);
            startCal.set(Calendar.MILLISECOND, 0);

            Calendar endCal = Calendar.getInstance(); // today


            for (int i = 0; i < allLeadList.size(); i++) {
                LeadListMasterTableDataSet obj = allLeadList.get(i);
                String downloadTimeStr = obj.getDownload_time().split(" ")[0]; // get date part only
                String[] arr = downloadTimeStr.split("-");

                Calendar createdCal = Calendar.getInstance();
                createdCal.set(Integer.parseInt(arr[0]), Integer.parseInt(arr[1]) - 1, Integer.parseInt(arr[2]));
                createdCal.set(Calendar.HOUR_OF_DAY, 0);
                createdCal.set(Calendar.MINUTE, 0);
                createdCal.set(Calendar.SECOND, 0);
                createdCal.set(Calendar.MILLISECOND, 0);

                if (!createdCal.before(startCal) && !createdCal.after(endCal)) {
                    count++;
                }
            }
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ countLast365DaysLeads: " + e.getMessage());
        }
        return count;
    }
    // ======================================== //


    // ==================== Filter Function ==================== //
    // Lead Status Wise Filter (HOT-WARM-COLD)
    private void getLeadListDataLeadStatusWise(String value) {
        Log.d("TAG", "_DOWNLOAD_ getLeadListDataLeadStatusWise: " + allLeadList.size());
        if (value.equalsIgnoreCase("hot")) {
            inactiveAllLeadStatus();
            hotLeadButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.active_button));
            hotLeadButtonText.setTextColor(Color.parseColor("#FFFFFF"));
            showLeadList.clear();
            for (int i = 0; i < allLeadList.size(); i++) {
                if (allLeadList.get(i).getLead_status().equalsIgnoreCase("hot")) {
                    showLeadList.add(allLeadList.get(i));
                }
            }
        } else if (value.equalsIgnoreCase("warm")) {
            inactiveAllLeadStatus();
            warmLeadButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.active_button));
            warmLeadButtonText.setTextColor(Color.parseColor("#FFFFFF"));
            showLeadList.clear();
            for (int i = 0; i < allLeadList.size(); i++) {
                if (allLeadList.get(i).getLead_status().equalsIgnoreCase("warm")) {
                    showLeadList.add(allLeadList.get(i));
                }
            }
        } else {
            inactiveAllLeadStatus();
            coldLeadButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.active_button));
            coldLeadButtonText.setTextColor(Color.parseColor("#FFFFFF"));
            showLeadList.clear();
            for (int i = 0; i < allLeadList.size(); i++) {
                if (allLeadList.get(i).getLead_status().equalsIgnoreCase("cold")) {
                    showLeadList.add(allLeadList.get(i));
                }
            }
        }
        setDataInList();
    }

    // Customer Response Wise Filter (PO RECEIVE-LOST ORDER)
    private void getLeadListDataCustomerResponseWise(String value) {
        if (value.equalsIgnoreCase("po")) {
            inactiveAllCustomerResponse();
            poReceivedButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.active_button));
            poReceivedButtonText.setTextColor(Color.parseColor("#FFFFFF"));
            showLeadList.clear();
            for (int i = 0; i < allLeadList.size(); i++) {
                if (!allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("12")) {
                    showLeadList.add(allLeadList.get(i));
                }
            }
        } else {
            inactiveAllCustomerResponse();
            lostOrderButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.active_button));
            lostOrderButtonText.setTextColor(Color.parseColor("#FFFFFF"));
            showLeadList.clear();
            for (int i = 0; i < allLeadList.size(); i++) {
                if (allLeadList.get(i).getLead_quotation_status().equalsIgnoreCase("12")) {
                    showLeadList.add(allLeadList.get(i));
                }
            }
        }
        setDataInList();
    }

    // Close Fulfilled Wise Filter (AGAINST DATE FILTER)
    private void dateTimePicker(String value) {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    String date = selectedYear + "-";
                    if (selectedMonth + 1 < 10) {
                        date = date + "0" + (selectedMonth + 1) + "-";
                    } else {
                        date = date + (selectedMonth + 1) + "-";
                    }
                    if (selectedDay < 10) {
                        date = date + "0" + selectedDay;
                    } else {
                        date = date + selectedDay;
                    }
                    switch (value) {
                        case "start_date":
                            startDateButtonText.setText(date);
                            startDate = date;
                            break;
                        case "end_date":
                            endDateButtonText.setText(date);
                            endDate = date;
                            break;
                    }
                },
                year, month, day
        );

        Calendar minCalendar = Calendar.getInstance();
        minCalendar.add(Calendar.DAY_OF_YEAR, -1000);
        Calendar maxCalendar = Calendar.getInstance();

        if (value.equalsIgnoreCase("start_date") && !endDate.isEmpty()) {
            maxCalendar = Calendar.getInstance();
            maxCalendar.set(Calendar.YEAR, Integer.parseInt(endDate.split("-")[0]));
            maxCalendar.set(Calendar.MONTH, Integer.parseInt(endDate.split("-")[1]) - 1);
            maxCalendar.set(Calendar.DAY_OF_MONTH, Integer.parseInt(endDate.split("-")[2]));
        }

        if (value.equalsIgnoreCase("end_date") && !startDate.isEmpty()) {
            minCalendar = Calendar.getInstance();
            minCalendar.set(Calendar.YEAR, Integer.parseInt(startDate.split("-")[0]));
            minCalendar.set(Calendar.MONTH, Integer.parseInt(startDate.split("-")[1]) - 1);
            minCalendar.set(Calendar.DAY_OF_MONTH, Integer.parseInt(startDate.split("-")[2]));
        }

        datePickerDialog.getDatePicker().setMinDate(minCalendar.getTimeInMillis());
        datePickerDialog.getDatePicker().setMaxDate(maxCalendar.getTimeInMillis());
        datePickerDialog.show();
    }
    // ======================================== //


    // ==================== Filter Data Against ==================== //
    // Filter Data
    private void filterData() {
        if (!startDate.isEmpty() && !endDate.isEmpty()) {
            int check = filterAgainstBothDate();
            if (check == 1) {
                setDataInList();
            }
        } else if (!startDate.isEmpty()) {
            int check = filterAgainstStartDate();
            if (check == 1) {
                setDataInList();
            }
        } else if (!endDate.isEmpty()) {
            int check = filterAgainstEndDate();
            if (check == 1) {
                setDataInList();
            }
        } else {
            showLeadList = new ArrayList<>(allLeadList);
            setDataInList();
        }
    }

    // Filter Against Both Date
    private int filterAgainstBothDate() {
        showLeadList.clear();

        Calendar startCal = Calendar.getInstance();
        String[] startArr = startDate.split("-");
        startCal.set(Integer.parseInt(startArr[0]), Integer.parseInt(startArr[1]) - 1, Integer.parseInt(startArr[2]), 0, 0, 0);
        startCal.set(Calendar.MILLISECOND, 0);

        Calendar endCal = Calendar.getInstance();
        String[] endArr = endDate.split("-");
        endCal.set(Integer.parseInt(endArr[0]), Integer.parseInt(endArr[1]) - 1, Integer.parseInt(endArr[2]), 23, 59, 59);
        endCal.set(Calendar.MILLISECOND, 999);

        for (int i = 0; i < allLeadList.size(); i++) {
            LeadListMasterTableDataSet obj = allLeadList.get(i);
            String downloadTimeStr = obj.getDownload_time().split(" ")[0]; // get date part only
            String[] arr = downloadTimeStr.split("-");

            Calendar createdCal = Calendar.getInstance();
            createdCal.set(Integer.parseInt(arr[0]), Integer.parseInt(arr[1]) - 1, Integer.parseInt(arr[2]));
            createdCal.set(Calendar.HOUR_OF_DAY, 0);
            createdCal.set(Calendar.MINUTE, 0);
            createdCal.set(Calendar.SECOND, 0);
            createdCal.set(Calendar.MILLISECOND, 0);

            if (!createdCal.before(startCal) && !createdCal.after(endCal)) {
                showLeadList.add(obj);
            }
        }

        return 1;
    }

    // Filter Against Start Date
    private int filterAgainstStartDate() {
        showLeadList.clear();

        Calendar startCal = Calendar.getInstance();
        String[] startArr = startDate.split("-");
        startCal.set(Integer.parseInt(startArr[0]), Integer.parseInt(startArr[1]) - 1, Integer.parseInt(startArr[2]), 0, 0, 0);
        startCal.set(Calendar.MILLISECOND, 0);
        for (int i = 0; i < allLeadList.size(); i++) {
            LeadListMasterTableDataSet obj = allLeadList.get(i);
            String downloadTimeStr = obj.getDownload_time().split(" ")[0]; // get date part only
            String[] arr = downloadTimeStr.split("-");

            Calendar createdCal = Calendar.getInstance();
            createdCal.set(Integer.parseInt(arr[0]), Integer.parseInt(arr[1]) - 1, Integer.parseInt(arr[2]));
            createdCal.set(Calendar.HOUR_OF_DAY, 0);
            createdCal.set(Calendar.MINUTE, 0);
            createdCal.set(Calendar.SECOND, 0);
            createdCal.set(Calendar.MILLISECOND, 0);

            if (!createdCal.before(startCal)) {
                showLeadList.add(obj);
            }
        }

        return 1;
    }

    // Filter Against End Date
    private int filterAgainstEndDate() {
        showLeadList.clear();

        Calendar endCal = Calendar.getInstance();
        String[] endArr = endDate.split("-");
        endCal.set(Integer.parseInt(endArr[0]), Integer.parseInt(endArr[1]) - 1, Integer.parseInt(endArr[2]), 23, 59, 59);
        endCal.set(Calendar.MILLISECOND, 999);
        for (int i = 0; i < allLeadList.size(); i++) {
            LeadListMasterTableDataSet obj = allLeadList.get(i);
            String downloadTimeStr = obj.getDownload_time().split(" ")[0]; // get date part only
            String[] arr = downloadTimeStr.split("-");

            Calendar createdCal = Calendar.getInstance();
            createdCal.set(Integer.parseInt(arr[0]), Integer.parseInt(arr[1]) - 1, Integer.parseInt(arr[2]));
            createdCal.set(Calendar.HOUR_OF_DAY, 0);
            createdCal.set(Calendar.MINUTE, 0);
            createdCal.set(Calendar.SECOND, 0);
            createdCal.set(Calendar.MILLISECOND, 0);

            if (!createdCal.after(endCal)) {
                showLeadList.add(obj);
            }
        }

        return 1;
    }
    // ======================================== //


    // ==================== Show List ==================== //
    // SHOW IN LIST PAGE
    private void setDataInList() {
        Log.d("TAG", "_DOWNLOAD_ setDataInList: " + showLeadList.size());
        leadGenerationList.setLayoutManager(new LinearLayoutManager(this));
        adapter = new LeadListAdapter(this, showLeadList, new LeadListAdapter.OnActionClickListener() {
            @Override
            public void onDetailsClicked(LeadListMasterTableDataSet item, int position) {
                try {
                    Intent intent = new Intent(LeadGenerationReportActivity.this, LeadGenerationReportDetailsActivity.class);
                    intent.putExtra("lead_id", item.getLead_generation_id());
                    startActivity(intent);
                } catch (Exception ignored) {
                }
            }

            @Override
            public void onLogDetailsClicked(LeadListMasterTableDataSet item, int position) {
                try {
                    Intent intent = new Intent(LeadGenerationReportActivity.this, LeadGenerationLogReportDetailsActivity.class);
                    intent.putExtra("lead_id", item.getLead_generation_id());
                    startActivity(intent);
                } catch (Exception ignored) {
                }
            }

            @Override
            public void onLostReasonClicked(LeadListMasterTableDataSet item, int position) {
                try {
                    lostLeadDetails=item;
                    lostOrderReasonPopupLayout.setVisibility(VISIBLE);
                } catch (Exception ignored) {
                }
            }
        });
        leadGenerationList.setAdapter(adapter);
    }
    // ======================================== //
}