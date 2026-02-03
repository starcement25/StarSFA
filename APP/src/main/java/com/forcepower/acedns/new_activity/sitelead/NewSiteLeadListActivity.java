package com.forcepower.acedns.new_activity.sitelead;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.new_activity.sitelead.adapter.SiteLeadItemAdapter;
import com.forcepower.acedns.new_activity.sitelead.dataset.SiteLeadDataSet;
import com.forcepower.acedns.util.Utils;

import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.Calendar;

public class NewSiteLeadListActivity extends AceDnsParentActivity implements View.OnClickListener {
    Context mContext;
    private Button backButton;
    private LinearLayout siteLeadStarDateBtn, siteLeadEndDateBtn, siteLeadFilterBtn;
    private TextView siteLeadStarDateText, siteLeadEndDateText;

    private LinearLayout siteLeadPendingBtn, siteLeadApprovedBtn, siteLeadRejectedBtn;
    private TextView siteLeadPendingText, siteLeadApprovedText, siteLeadRejectedText;
    private RecyclerView siteLeadList;

    private String startDate = "";
    private String endDate = "";
    private String status = "pending";
    ArrayList<SiteLeadDataSet> allSiteLeadList = new ArrayList<>();
    ArrayList<SiteLeadDataSet> filterSiteLeadList = new ArrayList<>();
    ArrayList<SiteLeadDataSet> showSiteLeadList = new ArrayList<>();
    SiteLeadItemAdapter adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_site_lead_list);

        mContext = NewSiteLeadListActivity.this;
        init();
    }

    @Override
    public void onClick(View v) {
        if (backButton == v) {
            startActivity(new Intent(NewSiteLeadListActivity.this, MenuActivity.class));
        }
        if (siteLeadStarDateBtn == v) {
            dateTimePicker("start_date");
        }
        if (siteLeadEndDateBtn == v) {
            dateTimePicker("end_date");
        }
        if (siteLeadFilterBtn == v) {
            filterData();
        }
        if (siteLeadPendingBtn == v) {
            status = "pending";
            filterAgainstStatus("pending");
        }
        if (siteLeadApprovedBtn == v) {
            status = "approved";
            filterAgainstStatus("approved");
        }
        if (siteLeadRejectedBtn == v) {
            status = "rejected";
            filterAgainstStatus("rejected");
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        startDate = "";
        endDate = "";
        status = "pending";
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        siteLeadStarDateBtn = findViewById(R.id.siteLeadStarDateBtn);
        siteLeadEndDateBtn = findViewById(R.id.siteLeadEndDateBtn);
        siteLeadFilterBtn = findViewById(R.id.siteLeadFilterBtn);
        siteLeadStarDateText = findViewById(R.id.siteLeadStarDateText);
        siteLeadEndDateText = findViewById(R.id.siteLeadEndDateText);

        siteLeadPendingBtn = findViewById(R.id.siteLeadPendingBtn);
        siteLeadApprovedBtn = findViewById(R.id.siteLeadApprovedBtn);
        siteLeadRejectedBtn = findViewById(R.id.siteLeadRejectedBtn);
        siteLeadPendingText = findViewById(R.id.siteLeadPendingText);
        siteLeadApprovedText = findViewById(R.id.siteLeadApprovedText);
        siteLeadRejectedText = findViewById(R.id.siteLeadRejectedText);
        siteLeadList = findViewById(R.id.siteLeadList);

        backButton.setOnClickListener(this);
        siteLeadStarDateBtn.setOnClickListener(this);
        siteLeadEndDateBtn.setOnClickListener(this);
        siteLeadFilterBtn.setOnClickListener(this);
        siteLeadPendingBtn.setOnClickListener(this);
        siteLeadApprovedBtn.setOnClickListener(this);
        siteLeadRejectedBtn.setOnClickListener(this);

        showInList();

        _DOWNLOAD_AsmExistingSiteLeadList();
    }

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
                            siteLeadStarDateText.setText(date);
                            startDate = date;
                            break;
                        case "end_date":
                            siteLeadEndDateText.setText(date);
                            endDate = date;
                            break;
                    }
                },
                year, month, day
        );

        Calendar minCalendar = Calendar.getInstance();
        minCalendar.add(Calendar.DAY_OF_YEAR, -90);
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

    private void filterData() {
        if (!startDate.isEmpty() && !endDate.isEmpty()) {
            int check = filterAgainstBothDate();
            if (check == 1) {
                filterAgainstStatus(status);
            }
        } else if (!startDate.isEmpty()) {
            int check = filterAgainstStartDate();
            if (check == 1) {
                filterAgainstStatus(status);
            }
        } else if (!endDate.isEmpty()) {
            int check = filterAgainstEndDate();
            if (check == 1) {
                filterAgainstStatus(status);
            }
        } else {
            filterSiteLeadList = allSiteLeadList;
        }
    }

    private int filterAgainstBothDate() {
        filterSiteLeadList.clear();

        Calendar startCal = Calendar.getInstance();
        String[] startArr = startDate.split("-");
        startCal.set(Integer.parseInt(startArr[0]), Integer.parseInt(startArr[1]) - 1, Integer.parseInt(startArr[2]), 0, 0, 0);
        startCal.set(Calendar.MILLISECOND, 0);

        Calendar endCal = Calendar.getInstance();
        String[] endArr = endDate.split("-");
        endCal.set(Integer.parseInt(endArr[0]), Integer.parseInt(endArr[1]) - 1, Integer.parseInt(endArr[2]), 23, 59, 59);
        endCal.set(Calendar.MILLISECOND, 999);

        for (int i = 0; i < allSiteLeadList.size(); i++) {
            SiteLeadDataSet obj = allSiteLeadList.get(i);
            String createdDateStr = obj.getCreatedAt().split(" ")[0];
            String[] createdArr = createdDateStr.split("-");
            Calendar createdCal = Calendar.getInstance();
            createdCal.set(Integer.parseInt(createdArr[0]), Integer.parseInt(createdArr[1]) - 1, Integer.parseInt(createdArr[2]));
            if (!createdCal.before(startCal) && !createdCal.after(endCal)) {
                filterSiteLeadList.add(obj);
            }
        }

        return 1;
    }

    private int filterAgainstStartDate() {
        filterSiteLeadList.clear();

        Calendar startCal = Calendar.getInstance();
        String[] startArr = startDate.split("-");
        startCal.set(Integer.parseInt(startArr[0]), Integer.parseInt(startArr[1]) - 1, Integer.parseInt(startArr[2]), 0, 0, 0);
        startCal.set(Calendar.MILLISECOND, 0);

        for (int i = 0; i < allSiteLeadList.size(); i++) {
            SiteLeadDataSet obj = allSiteLeadList.get(i);
            String createdDateStr = obj.getCreatedAt().split(" ")[0];
            String[] createdArr = createdDateStr.split("-");
            Calendar createdCal = Calendar.getInstance();
            createdCal.set(Integer.parseInt(createdArr[0]), Integer.parseInt(createdArr[1]) - 1, Integer.parseInt(createdArr[2]));

            if (!createdCal.before(startCal)) {
                filterSiteLeadList.add(obj);
            }
        }

        return 1;
    }

    private int filterAgainstEndDate() {
        filterSiteLeadList.clear();

        Calendar endCal = Calendar.getInstance();
        String[] endArr = endDate.split("-");
        endCal.set(Integer.parseInt(endArr[0]), Integer.parseInt(endArr[1]) - 1, Integer.parseInt(endArr[2]), 23, 59, 59);
        endCal.set(Calendar.MILLISECOND, 999);

        for (int i = 0; i < allSiteLeadList.size(); i++) {
            SiteLeadDataSet obj = allSiteLeadList.get(i);
            String createdDateStr = obj.getCreatedAt().split(" ")[0];
            String[] createdArr = createdDateStr.split("-");
            Calendar createdCal = Calendar.getInstance();
            createdCal.set(Integer.parseInt(createdArr[0]), Integer.parseInt(createdArr[1]) - 1, Integer.parseInt(createdArr[2]));

            if (!createdCal.after(endCal)) {
                filterSiteLeadList.add(obj);
            }
        }

        return 1;
    }

    @SuppressLint("NotifyDataSetChanged")
    private void filterAgainstStatus(String value) {
        showSiteLeadList.clear();
        Log.d("TAG", "_DOWNLOAD_ value: " + value);
        for (int i = 0; i < filterSiteLeadList.size(); i++) {
            Log.d("TAG", "_DOWNLOAD_ list approval status: " + value);
            if (filterSiteLeadList.get(i).getApprovalStatus().equalsIgnoreCase(value)) {
                showSiteLeadList.add(filterSiteLeadList.get(i));
            }
        }
        runOnUiThread(() -> {
            if (adapter != null) {
                adapter.notifyDataSetChanged();
            }
        });
        runOnUiThread(() -> {
            if (value.equalsIgnoreCase("pending")) {
                siteLeadPendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
                siteLeadApprovedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
                siteLeadRejectedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            } else if (value.equalsIgnoreCase("approved")) {
                siteLeadPendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
                siteLeadApprovedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
                siteLeadRejectedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            } else if (value.equalsIgnoreCase("rejected")) {
                siteLeadPendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
                siteLeadApprovedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
                siteLeadRejectedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            }
            siteLeadPendingText.setTextColor(Color.argb(255, 0, 0, 0));
            siteLeadApprovedText.setTextColor(Color.argb(255, 0, 0, 0));
            siteLeadRejectedText.setTextColor(Color.argb(255, 0, 0, 0));
        });
    }

    public void showInList() {
        try {
            siteLeadList.setLayoutManager(new LinearLayoutManager(this));
            adapter = new SiteLeadItemAdapter(this, showSiteLeadList, (item, position) -> {
                try {
                    JSONObject obj = new JSONObject();
                    obj.put("id", item.getId());
                    obj.put("transactionId", item.getTransactionId());
                    obj.put("uniqueId", item.getUniqueId());
                    obj.put("visitDate", item.getVisitDate());
                    obj.put("empCode", item.getEmpCode());
                    obj.put("empName", item.getEmpName());
                    obj.put("zone", item.getZone());
                    obj.put("branch", item.getBranch());
                    obj.put("district", item.getDistrict());
                    obj.put("state", item.getState());
                    obj.put("longitude", item.getLongitude());
                    obj.put("latitude", item.getLatitude());
                    obj.put("customerName", item.getCustomerName());
                    obj.put("customerPhoneNo", item.getCustomerPhoneNo());
                    obj.put("address", item.getAddress());
                    obj.put("siteSegment", item.getSiteSegment());
                    obj.put("visitType", item.getVisitType());
                    obj.put("projectSegment", item.getProjectSegment());
                    obj.put("typeOfConst", item.getTypeOfConst());
                    obj.put("builtUpArea", item.getBuiltUpArea());
                    obj.put("noOfBag", item.getNoOfBag());
                    obj.put("conversion", item.getConversion());
                    obj.put("sitePriority", item.getSitePriority());
                    obj.put("counterCode", item.getCounterCode());
                    obj.put("createdAt", item.getCreatedAt());
                    obj.put("updatedAt", item.getUpdatedAt());
                    obj.put("newSiteLeadId", item.getNewSiteLeadId());
                    obj.put("newSiteLeadUniqueId", item.getNewSiteLeadUniqueId());
                    obj.put("pettyContractorRegistered", item.getPettyContractorRegistered());
                    obj.put("headMasonName", item.getHeadMasonName());
                    obj.put("contractorId", item.getContractorId());
                    obj.put("headMasonContact", item.getHeadMasonContact());
                    obj.put("engineerRegistered", item.getEngineerRegistered());
                    obj.put("engineerName", item.getEngineerName());
                    obj.put("engineerId", item.getEngineerId());
                    obj.put("engineerContact", item.getEngineerContact());
                    obj.put("meetingPerson", item.getMeetingPerson());
                    obj.put("decisionMaker", item.getDecisionMaker());
                    obj.put("currentStageOfConstruction", item.getCurrentStageOfConstruction());
                    obj.put("sitePotential", item.getSitePotential());
                    obj.put("consumedTillDate", item.getConsumedTillDate());
                    obj.put("balancePotential", item.getBalancePotential());
                    obj.put("siteCategory", item.getSiteCategory());
                    obj.put("brandUsed", item.getBrandUsed());
                    obj.put("pricePerBag", item.getPricePerBag());
                    obj.put("selectProduct", item.getSelectProduct());
                    obj.put("noOfBagsOrdered", item.getNoOfBagsOrdered());
                    obj.put("requestedDate", item.getRequestedDate());
                    obj.put("counterType", item.getCounterType());
                    obj.put("counterName", item.getCounterName());
                    obj.put("reasonForNonConversion", item.getReasonForNonConversion());
                    obj.put("weatherShieldDemo", item.getWeatherShieldDemo());
                    obj.put("approvalStatus", item.getApprovalStatus());
                    obj.put("approvalDateTime", item.getApprovalDateTime());
                    obj.put("asmName", item.getAsmName());
                    obj.put("asmId", item.getAsmId());
                    obj.put("actualDateOfDelivery", item.getActualDateOfDelivery());
                    obj.put("deliveryRemarks", item.getDeliveryRemarks());
                    obj.put("reasonForNotDelivery", item.getReasonForNotDelivery());
                    obj.put("siteStatus", item.getSiteStatus());
                    obj.put("balancePotentialManual", item.getBalancePotentialManual());
                    obj.put("floorCount", item.getFloorCount());
                    obj.put("remarks", item.getRemarks());

                    Intent intent = new Intent(NewSiteLeadListActivity.this, LeadDetailsActivity.class);
                    intent.putExtra("dataset", obj.toString());
                    startActivity(intent);
                } catch (Exception ignored) {
                }
            });
            siteLeadList.setAdapter(adapter);
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ showInList Exception : " + e);
        }
    }

    public void _DOWNLOAD_AsmExistingSiteLeadList() {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_get_asm_reqst_site_lead.php?asm_id=" + Constants.employeeDetailObject.getEmpCode();
        Log.d("TAG", "_DOWNLOAD_ AsmExistingSiteLeadList: " + URL);
        new Thread(() -> {
            Download_txt(URL);
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "AsmExistingSiteLeadList" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else if (line.indexOf("#") > 0) {
                        // Data not save in list
                        String a = "";
                    } else {
                        String[] RowData = (line + " ").split("\\^");
                        if (RowData.length == noColumn[0]) {
                            SiteLeadDataSet temp = new SiteLeadDataSet();
                            temp.setId(RowData[0]);
                            temp.setTransactionId(RowData[1]);
                            temp.setUniqueId(RowData[2]);
                            temp.setVisitDate(RowData[3]);
                            temp.setEmpCode(RowData[4]);
                            temp.setEmpName(RowData[5]);
                            temp.setZone(RowData[6]);
                            temp.setBranch(RowData[7]);
                            temp.setDistrict(RowData[8]);
                            temp.setState(RowData[9]);
                            temp.setLongitude(RowData[10]);
                            temp.setLatitude(RowData[11]);
                            temp.setCustomerName(RowData[12]);
                            temp.setCustomerPhoneNo(RowData[13]);
                            temp.setAddress(RowData[14]);
                            temp.setSiteSegment(RowData[15]);
                            temp.setVisitType(RowData[16]);
                            temp.setProjectSegment(RowData[17]);
                            temp.setTypeOfConst(RowData[18]);
                            temp.setBuiltUpArea(RowData[19]);
                            temp.setNoOfBag(RowData[20]);
                            temp.setConversion(RowData[21]);
                            temp.setSitePriority(RowData[22]);
                            temp.setCounterCode(RowData[23]);
                            temp.setCreatedAt(RowData[24]);
                            temp.setUpdatedAt(RowData[25]);
                            temp.setNewSiteLeadId(RowData[26]);
                            temp.setNewSiteLeadUniqueId(RowData[27]);
                            temp.setPettyContractorRegistered(RowData[28]);
                            temp.setHeadMasonName(RowData[29]);
                            temp.setContractorId(RowData[30]);
                            temp.setHeadMasonContact(RowData[31]);
                            temp.setEngineerRegistered(RowData[32]);
                            temp.setEngineerName(RowData[33]);
                            temp.setEngineerId(RowData[34]);
                            temp.setEngineerContact(RowData[35]);
                            temp.setMeetingPerson(RowData[36]);
                            temp.setDecisionMaker(RowData[37]);
                            temp.setCurrentStageOfConstruction(RowData[38]);
                            temp.setSitePotential(RowData[39]);
                            temp.setConsumedTillDate(RowData[40]);
                            temp.setBalancePotential(RowData[41]);
                            temp.setSiteCategory(RowData[42]);
                            temp.setBrandUsed(RowData[43]);
                            temp.setPricePerBag(RowData[44]);
                            temp.setSelectProduct(RowData[45]);
                            temp.setNoOfBagsOrdered(RowData[46]);
                            temp.setRequestedDate(RowData[47]);
                            temp.setCounterType(RowData[48]);
                            temp.setCounterName(RowData[49]);
                            temp.setReasonForNonConversion(RowData[50]);
                            temp.setWeatherShieldDemo(RowData[51]);
                            temp.setApprovalStatus(RowData[52]);
                            temp.setApprovalDateTime(RowData[53]);
                            temp.setAsmName(RowData[54]);
                            temp.setAsmId(RowData[55]);
                            temp.setActualDateOfDelivery(RowData[57]);
                            temp.setDeliveryRemarks(RowData[58]);
                            temp.setReasonForNotDelivery(RowData[59]);
                            temp.setSiteStatus(RowData[60]);
                            temp.setFloorCount(RowData[61]);
                            temp.setBalancePotentialManual(RowData[62].trim());
                            temp.setRemarks(RowData[63].trim());

                            allSiteLeadList.add(temp);
                            filterSiteLeadList.add(temp);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
            status = "pending";
            filterAgainstStatus("pending");
        }).start();
    }

    private void Download_txt(String URL) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile;
        InputStream is = null;
        java.net.URL url;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + "AsmExistingSiteLeadList.txt");
            if (outputFile.exists()) {
                outputFile.delete();
            }
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setConnectTimeout(0);
            c.connect();
            int responseCode = c.getResponseCode();
            if (responseCode == HttpURLConnection.HTTP_OK) {
                is = c.getInputStream();
                byte[] buffer = new byte[1024];
                int len1;
                while ((len1 = is.read(buffer)) != -1) {
                    fbo.write(buffer, 0, len1);
                }
                fbo.flush();
            }
        } catch (Exception ignored) {
        } finally {
            if (c != null) {
                c.disconnect();
            }
            if (fbo != null) {
                try {
                    fbo.close();
                } catch (IOException ignored) {
                }
            }
            if (is != null) {
                try {
                    is.close();
                } catch (IOException ignored) {
                }
            }
        }
    }
}