package com.forcepower.acedns.new_activity.sitelead;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.new_activity.sitelead.adapter.ShowDataSetAdapter;
import com.forcepower.acedns.new_activity.sitelead.dataset.DataSet;
import com.forcepower.acedns.util.Utils;

import org.json.JSONException;
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
import java.util.Objects;

import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class LeadDetailsActivity extends AceDnsParentActivity implements View.OnClickListener {
    Context mContext;
    private Button backButton, submitButton;
    private TextView asmTextSiteTransactionId, asmTextSiteUniqueId, asmTextSiteCreationDate, asmTextSiteVisitDate, asmTextSiteEmployeeCode, asmTextSiteEmployeeName, asmTextSiteZone, asmTextSiteState,
            asmTextSiteBranch, asmTextSiteDistrict, asmTextSiteLatitude, asmTextSiteLongitude, asmTextSiteCustomerName, asmTextSiteCustomerContactNumber, asmTextSiteFullAddress, asmTextSiteIsRegdInStarLink,
            asmTextSiteContractorName, asmTextSiteContractorContactNo, asmTextSiteIsRegdInStarStellar, asmTextSiteEngineerName, asmTextSiteEngineerContactNo, asmTextSiteMeetingPerson, asmTextSiteDecisionMaker,
            asmTextSiteSiteSegment, asmTextSiteVisitType, asmTextSiteProjectSegment, asmTextSiteTypeOfConstruction, asmTextSiteFloorCount, asmTextSiteCurrentStageOfConstruction, asmTextSiteBuiltUpArea,
            asmTextSiteSitePotential, asmTextSiteConsumedTillDate, asmTextSiteBalancePotential, asmTextSiteBalancePotentialManual, asmTextSiteSiteCategory, asmTextSiteBrandUsed, asmTextSitePricePerBag,
            asmTextSiteConversion, asmTextSiteSelectProduct, asmTextSiteOrderQty, asmTextSiteRequestedDateOfDelivery, asmTextSiteCounterType, asmTextSiteCounterName, asmTextSiteCounterCode,
            asmTextSiteDistrictReasonForNonConversion, asmTextSiteSitePriority, asmTextSiteWeatherShieldDemo, asmTextSiteRemarks, asmTextSiteSiteStatus;
    private LinearLayout asmLayoutSiteFloorCount;

    private LinearLayout asmStatusUpdatePopup;
    private LinearLayout layoutActualDateOfDeliveryASM, layoutDeliveryRemarksASM, layoutReasonForNotDeliveryASM;
    private Button buttonStatusASM, buttonActualDateOfDeliveryASM;
    private TextView textStatusASM, textActualDateOfDeliveryASM;
    private EditText edTextDeliveryRemarksASM, edTextReasonForNotDeliveryASM;
    private Button updateButton;

    ArrayList<DataSet> approvalStatusList = new ArrayList<>();
    String approvalStatus="";
    String actualDateOfDelivery="";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_details);
        mContext = LeadDetailsActivity.this;
        init();
    }

    @Override
    public void onClick(View view) {
        if (backButton == view) {
            startActivity(new Intent(LeadDetailsActivity.this, NewSiteLeadListActivity.class));
        }
        if (submitButton == view) {
            asmStatusUpdatePopup.setVisibility(View.VISIBLE);
        }
        if (updateButton == view) {
            checkAsmApprovePopupDetails();
        }
        if (buttonStatusASM == view) {
            showListDataDialog(approvalStatusList, "approval_status", "popup", "Select Approval Status", false);
        }
        if (buttonActualDateOfDeliveryASM == view) {
            dateTimePicker();
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        submitButton = findViewById(R.id.submitButton);
        updateButton = findViewById(R.id.updateButton);

        asmTextSiteTransactionId = findViewById(R.id.asmTextSiteTransactionId);
        asmTextSiteUniqueId = findViewById(R.id.asmTextSiteUniqueId);
        asmTextSiteCreationDate = findViewById(R.id.asmTextSiteCreationDate);
        asmTextSiteVisitDate = findViewById(R.id.asmTextSiteVisitDate);
        asmTextSiteEmployeeCode = findViewById(R.id.asmTextSiteEmployeeCode);
        asmTextSiteEmployeeName = findViewById(R.id.asmTextSiteEmployeeName);
        asmTextSiteZone = findViewById(R.id.asmTextSiteZone);
        asmTextSiteState = findViewById(R.id.asmTextSiteState);
        asmTextSiteBranch = findViewById(R.id.asmTextSiteBranch);
        asmTextSiteDistrict = findViewById(R.id.asmTextSiteDistrict);
        asmTextSiteLatitude = findViewById(R.id.asmTextSiteLatitude);
        asmTextSiteLongitude = findViewById(R.id.asmTextSiteLongitude);
        asmTextSiteCustomerName = findViewById(R.id.asmTextSiteCustomerName);
        asmTextSiteCustomerContactNumber = findViewById(R.id.asmTextSiteCustomerContactNumber);
        asmTextSiteFullAddress = findViewById(R.id.asmTextSiteFullAddress);
        asmTextSiteIsRegdInStarLink = findViewById(R.id.asmTextSiteIsRegdInStarLink);
        asmTextSiteContractorName = findViewById(R.id.asmTextSiteContractorName);
        asmTextSiteContractorContactNo = findViewById(R.id.asmTextSiteContractorContactNo);
        asmTextSiteIsRegdInStarStellar = findViewById(R.id.asmTextSiteIsRegdInStarStellar);
        asmTextSiteEngineerName = findViewById(R.id.asmTextSiteEngineerName);
        asmTextSiteEngineerContactNo = findViewById(R.id.asmTextSiteEngineerContactNo);
        asmTextSiteMeetingPerson = findViewById(R.id.asmTextSiteMeetingPerson);
        asmTextSiteDecisionMaker = findViewById(R.id.asmTextSiteDecisionMaker);
        asmTextSiteSiteSegment = findViewById(R.id.asmTextSiteSiteSegment);
        asmTextSiteVisitType = findViewById(R.id.asmTextSiteVisitType);
        asmTextSiteProjectSegment = findViewById(R.id.asmTextSiteProjectSegment);
        asmTextSiteTypeOfConstruction = findViewById(R.id.asmTextSiteTypeOfConstruction);
        asmTextSiteFloorCount = findViewById(R.id.asmTextSiteFloorCount);
        asmTextSiteCurrentStageOfConstruction = findViewById(R.id.asmTextSiteCurrentStageOfConstruction);
        asmTextSiteBuiltUpArea = findViewById(R.id.asmTextSiteBuiltUpArea);
        asmTextSiteSitePotential = findViewById(R.id.asmTextSiteSitePotential);
        asmTextSiteConsumedTillDate = findViewById(R.id.asmTextSiteConsumedTillDate);
        asmTextSiteBalancePotential = findViewById(R.id.asmTextSiteBalancePotential);
        asmTextSiteBalancePotentialManual = findViewById(R.id.asmTextSiteBalancePotentialManual);
        asmTextSiteSiteCategory = findViewById(R.id.asmTextSiteSiteCategory);
        asmTextSiteBrandUsed = findViewById(R.id.asmTextSiteBrandUsed);
        asmTextSitePricePerBag = findViewById(R.id.asmTextSitePricePerBag);
        asmTextSiteConversion = findViewById(R.id.asmTextSiteConversion);
        asmTextSiteSelectProduct = findViewById(R.id.asmTextSiteSelectProduct);
        asmTextSiteOrderQty = findViewById(R.id.asmTextSiteOrderQty);
        asmTextSiteRequestedDateOfDelivery = findViewById(R.id.asmTextSiteRequestedDateOfDelivery);
        asmTextSiteCounterType = findViewById(R.id.asmTextSiteCounterType);
        asmTextSiteCounterName = findViewById(R.id.asmTextSiteCounterName);
        asmTextSiteCounterCode = findViewById(R.id.asmTextSiteCounterCode);
        asmTextSiteDistrictReasonForNonConversion = findViewById(R.id.asmTextSiteDistrictReasonForNonConversion);
        asmTextSiteSitePriority = findViewById(R.id.asmTextSiteSitePriority);
        asmTextSiteWeatherShieldDemo = findViewById(R.id.asmTextSiteWeatherShieldDemo);
        asmTextSiteRemarks = findViewById(R.id.asmTextSiteRemarks);
        asmTextSiteSiteStatus = findViewById(R.id.asmTextSiteSiteStatus);
        asmLayoutSiteFloorCount = findViewById(R.id.asmLayoutSiteFloorCount);

        asmStatusUpdatePopup = findViewById(R.id.asmStatusUpdatePopup);
        layoutActualDateOfDeliveryASM = findViewById(R.id.layoutActualDateOfDeliveryASM);
        layoutDeliveryRemarksASM = findViewById(R.id.layoutDeliveryRemarksASM);
        layoutReasonForNotDeliveryASM = findViewById(R.id.layoutReasonForNotDeliveryASM);
        buttonStatusASM = findViewById(R.id.buttonStatusASM);
        buttonActualDateOfDeliveryASM = findViewById(R.id.buttonActualDateOfDeliveryASM);
        textStatusASM = findViewById(R.id.textStatusASM);
        textActualDateOfDeliveryASM = findViewById(R.id.textActualDateOfDeliveryASM);
        edTextDeliveryRemarksASM = findViewById(R.id.edTextDeliveryRemarksASM);
        edTextReasonForNotDeliveryASM = findViewById(R.id.edTextReasonForNotDeliveryASM);
        asmStatusUpdatePopup.setVisibility(View.GONE);
        textStatusASM.setVisibility(View.GONE);
        textActualDateOfDeliveryASM.setVisibility(View.GONE);
        layoutActualDateOfDeliveryASM.setVisibility(View.GONE);
        layoutDeliveryRemarksASM.setVisibility(View.GONE);
        layoutReasonForNotDeliveryASM.setVisibility(View.GONE);

        backButton.setOnClickListener(this);
        submitButton.setOnClickListener(this);
        updateButton.setOnClickListener(this);
        buttonStatusASM.setOnClickListener(this);
        buttonActualDateOfDeliveryASM.setOnClickListener(this);

        _DOWNLOAD_ApprovalStatusList();
        setAllData();
    }

    private void setAllData() {
        try {
            String jsonString = getIntent().getStringExtra("dataset");
            if (jsonString != null) {
                JSONObject obj = new JSONObject(jsonString);
                asmTextSiteTransactionId.setText(obj.getString("transactionId"));
                asmTextSiteUniqueId.setText(obj.getString("uniqueId"));
                asmTextSiteCreationDate.setText(obj.getString("createdAt"));
                asmTextSiteVisitDate.setText(obj.getString("visitDate"));
                asmTextSiteEmployeeCode.setText(obj.getString("empCode"));
                asmTextSiteEmployeeName.setText(obj.getString("empName"));
                asmTextSiteZone.setText(obj.getString("zone"));
                asmTextSiteState.setText(obj.getString("state"));
                asmTextSiteBranch.setText(obj.getString("branch"));
                asmTextSiteDistrict.setText(obj.getString("district"));
                asmTextSiteLatitude.setText(obj.getString("latitude"));
                asmTextSiteLongitude.setText(obj.getString("longitude"));
                asmTextSiteCustomerName.setText(obj.getString("customerName"));
                asmTextSiteCustomerContactNumber.setText(obj.getString("customerPhoneNo"));
                asmTextSiteFullAddress.setText(obj.getString("address"));
                asmTextSiteIsRegdInStarLink.setText(obj.getString("pettyContractorRegistered"));
                asmTextSiteContractorName.setText(obj.getString("headMasonName"));
                asmTextSiteContractorContactNo.setText(obj.getString("headMasonContact"));
                asmTextSiteIsRegdInStarStellar.setText(obj.getString("engineerRegistered"));
                asmTextSiteEngineerName.setText(obj.getString("engineerName"));
                asmTextSiteEngineerContactNo.setText(obj.getString("engineerContact"));
                asmTextSiteMeetingPerson.setText(obj.getString("meetingPerson"));
                asmTextSiteDecisionMaker.setText(obj.getString("decisionMaker"));
                asmTextSiteSiteSegment.setText(obj.getString("siteSegment"));
                asmTextSiteVisitType.setText(obj.getString("visitType"));
                asmTextSiteProjectSegment.setText(obj.getString("projectSegment"));
                asmTextSiteTypeOfConstruction.setText(obj.getString("typeOfConst"));
                asmTextSiteFloorCount.setText(obj.getString("floorCount"));
                asmTextSiteCurrentStageOfConstruction.setText(obj.getString("currentStageOfConstruction"));
                asmTextSiteBuiltUpArea.setText(obj.getString("builtUpArea"));
                asmTextSiteSitePotential.setText(obj.getString("sitePotential"));
                asmTextSiteConsumedTillDate.setText(obj.getString("consumedTillDate"));
                asmTextSiteBalancePotential.setText(obj.getString("balancePotential"));
                asmTextSiteBalancePotentialManual.setText(obj.getString("balancePotentialManual"));
                asmTextSiteSiteCategory.setText(obj.getString("siteCategory"));
                asmTextSiteBrandUsed.setText(obj.getString("brandUsed"));
                asmTextSitePricePerBag.setText(obj.getString("pricePerBag"));
                asmTextSiteConversion.setText(obj.getString("conversion"));
                asmTextSiteSelectProduct.setText(obj.getString("selectProduct"));
                asmTextSiteOrderQty.setText(obj.getString("noOfBagsOrdered"));
                asmTextSiteRequestedDateOfDelivery.setText(obj.getString("requestedDate"));
                asmTextSiteCounterType.setText(obj.getString("counterType"));
                asmTextSiteCounterName.setText(obj.getString("counterName"));
                asmTextSiteCounterCode.setText(obj.getString("counterCode"));
                asmTextSiteDistrictReasonForNonConversion.setText(obj.getString("reasonForNonConversion"));
                asmTextSiteSitePriority.setText(obj.getString("sitePriority"));
                asmTextSiteWeatherShieldDemo.setText(obj.getString("weatherShieldDemo"));
                asmTextSiteRemarks.setText(obj.getString("remarks"));
                asmTextSiteSiteStatus.setText(obj.getString("siteStatus"));
                if (obj.getString("approvalStatus").equalsIgnoreCase("pending")) {
                    submitButton.setVisibility(View.VISIBLE);
                } else {
                    submitButton.setVisibility(View.GONE);
                }
            }
        } catch (JSONException ignored) {
        }
    }

    private void dateTimePicker() {
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
                    textActualDateOfDeliveryASM.setVisibility(View.VISIBLE);
                    textActualDateOfDeliveryASM.setText(date);
                    actualDateOfDelivery = date;
                },
                year, month, day
        );
        datePickerDialog.getDatePicker().setMinDate(calendar.getTimeInMillis());
        datePickerDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void showListDataDialog(ArrayList<DataSet> dataSet, String value, String type, String titleValue, boolean isSearchable) {
        try {
            final ShowDataSetAdapter pAdapter = new ShowDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    pAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            if (isSearchable)
                searchLayout.setVisibility(View.VISIBLE);
            else
                searchLayout.setVisibility(View.GONE);

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                if (value.equalsIgnoreCase("approval_status")) {
                    if (type.equalsIgnoreCase("popup")) {
                        textStatusASM.setVisibility(View.VISIBLE);
                        textStatusASM.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("approved")) {
                            layoutActualDateOfDeliveryASM.setVisibility(View.VISIBLE);
                            layoutDeliveryRemarksASM.setVisibility(View.VISIBLE);
                            layoutReasonForNotDeliveryASM.setVisibility(View.GONE);
                        } else {
                            layoutActualDateOfDeliveryASM.setVisibility(View.GONE);
                            layoutDeliveryRemarksASM.setVisibility(View.GONE);
                            layoutReasonForNotDeliveryASM.setVisibility(View.VISIBLE);
                        }
                    }
                    approvalStatus = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    private void Download_txt(String URL) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile;
        InputStream is = null;
        java.net.URL url;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + "ApprovalStatusList" + ".txt");
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

    public void _DOWNLOAD_ApprovalStatusList() {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_approval_status_site_lead.php";
        new Thread(() -> {
            Download_txt(URL);
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ApprovalStatusList" + ".txt");
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
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            DataSet temp = new DataSet();
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            approvalStatusList.add(temp);
                        }
                    }
                }
                buffer.close();
            } catch (IOException ignored) {
            }
        }).start();
    }

    private void checkAsmApprovePopupDetails() {
        if (textStatusASM.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(this, "Please select Status.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textStatusASM.getText().toString().trim().equalsIgnoreCase("approved")) {
            if (textActualDateOfDeliveryASM.getText().toString().trim().equalsIgnoreCase("")) {
                Toast.makeText(this, "Please select Actual Date of Delivery.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextDeliveryRemarksASM.getText().toString().trim().equalsIgnoreCase("")) {
                Toast.makeText(this, "Please enter Remarks.", Toast.LENGTH_LONG).show();
                return;
            }
        } else {
            if (edTextReasonForNotDeliveryASM.getText().toString().trim().equalsIgnoreCase("")) {
                Toast.makeText(this, "Please enter Reason For Not Delivery.", Toast.LENGTH_LONG).show();
                return;
            }
        }
        requestForUpdateSiteLeadStatus();
    }

    private void requestForUpdateSiteLeadStatus() {
        new Thread(() -> {
            try {
                JSONObject obj = new JSONObject();
                obj.put("site_id", asmTextSiteUniqueId.getText().toString().trim());
                obj.put("approval_status", approvalStatus);
                obj.put("actual_date_of_delivery", actualDateOfDelivery);
                obj.put("delivery_remarks", edTextDeliveryRemarksASM.getText().toString().trim());
                obj.put("reason_for_not_delivery", edTextReasonForNotDeliveryASM.getText().toString().trim());

                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, obj.toString());

                Log.d("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking: " + obj);

                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/api_asm_approve_site_lead.php")
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response execute = client.newCall(request).execute();
                ((LeadDetailsActivity) mContext).runOnUiThread(() -> {
                    if (execute.isSuccessful()) {
                        // HTTP 200–299
                        ((LeadDetailsActivity) mContext).successMessageCleanAll();

                    } else if (execute.code() == 400) {
                        // HTTP 400 Bad Request
                        try {
                            assert execute.body() != null;
                            JSONObject json = new JSONObject(execute.body().toString());
                            ((LeadDetailsActivity) mContext).showError(json.optString("error"));
                        } catch (JSONException e) {
                            ((LeadDetailsActivity) mContext).showError("Bad Request! Please check the data.");
                        }
                    } else {
                        // Other errors
                        ((LeadDetailsActivity) mContext).showError("Something went wrong! Code: " + execute.code());
                    }
                });
            } catch (Exception e) {
                Log.e("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking Exception: " + e.getMessage(), e);
            }
        }).start();
    }

    private void showError(String message) {
        Toast.makeText(mContext, message, Toast.LENGTH_LONG).show();
    }

    private void successMessageCleanAll() {
        Toast.makeText(mContext, "Status updated successfully", Toast.LENGTH_LONG).show();
        new Handler(Looper.getMainLooper()).postDelayed(() -> {
            startActivity(new Intent(LeadDetailsActivity.this, NewSiteLeadListActivity.class));
        }, 2000);
    }
}