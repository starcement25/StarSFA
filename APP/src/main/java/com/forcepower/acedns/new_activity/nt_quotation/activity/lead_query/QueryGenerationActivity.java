package com.forcepower.acedns.new_activity.nt_quotation.activity.lead_query;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.text.Editable;
import android.text.Html;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.ComponentActivity;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.ShowDataSetAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.ShowDistrictDataSetAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.ShowEmployeeDataSetAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.ShowPartyDataSetAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.ShowStateDataSetAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.CustomerFilterModel;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DistrictDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.EmployeeDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.PartyDataList;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.new_activity.khoj.adapter.LeadDataSetAdapter;
import com.forcepower.acedns.new_activity.khoj.data_set.LeadDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.StateDataSet;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.Utils;
import com.google.gson.Gson;

import org.json.JSONArray;
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
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import java.util.Objects;

import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class QueryGenerationActivity extends ComponentActivity implements View.OnClickListener, RadioGroup.OnCheckedChangeListener {
    Context mContext;
    Button backButton;
    LinearLayout leadTypeRadioGroupLayout;

    // New Query Generate
    LinearLayout leadGenerationLayout, salesOfficerDetailsLayout, otherSalesOfficeNameButtonLayout, exWorksButtonLayout, fosEditTextLayout;
    RadioGroup leadTypeRadioGroup, generateForRadioGroup;
    RadioButton newLeadRadioButton, existingLeadRadioButton, repeatLeadRadioButton, selfRadioButton, othersRadioButton;
    TextView textUniqueLeadID, textGenerateFor, textSalesOfficerName, textDateStamp, textTimeStamp, textLatitude, textLongitude, textSoldToPartyName, textSoldToPartyCode, textSoldToPartyAddress,
            textShipToPartyName, textShipToPartyCode, textShipToPartyAddress, textTotalPotentialOfSite, textQuotationQuantity, textCurrentBrandUsed, textExpectedRatePerBag, textCurrentPriceStarRsPerBag,
            textCurrentPriceCompetitorRsPerBag, textContactPersonName, textDesignation, textContactNumber, textMailId, textSalesOfficerRemarks, textFosSiding, textReferredBy;
    Button salesOfficerNameButton, soldToPartyNameButton, soldToPartyStateButton, soldToPartyDistrictsButton, shipToPartyNameButton, shipToPartyStateButton, shipToPartyDistrictsButton, segmentButton, leadSourceButton,
            productPackagingButton, modeOfPaymentButton, creditTermsButton, aacBlockRequiredNotButton, categoryTypeOfConstructionButton, leadStatusButton, nextVisitDateButton, requirementTypeButton,
            exWorksButton, assignedToButton, requirementTimingButton, shareLeadImageButton, leadActionButton;
    EditText editTextUniqueLeadID, editTextSalesOfficerName, editTextDateStamp, editTextTimeStamp, editTextLatitude, editTextLongitude, editTextSoldToPartyName, editTextSoldToPartyCode, editTextSoldToPartyAddress,
            editTextShipToPartyName, editTextShipToPartyCode, editTextShipToPartyAddress, editTextTotalPotentialOfSite, editTextQuotationQuantity, editTextCurrentBrandUsed, editTextExpectedRatePerBag,
            editTextCurrentPriceStarRsPerBag, editTextCurrentPriceCompetitorRsPerBag, editTextContactPersonName, editTextDesignation, editTextContactNumber, editTextMailId, editTextSalesOfficerRemarks,
            editTextFosSiding, editTextReferredBy;
    TextView soldToPartyStateButtonSelectText, soldToPartyDistrictsButtonSelectText, shipToPartyStateButtonSelectText, shipToPartyDistrictsButtonSelectText, segmentButtonSelectText, leadSourceButtonSelectText,
            productPackagingButtonSelectText, modeOfPaymentButtonSelectText, creditTermsButtonSelectText, aacBlockRequiredNotButtonSelectText, categoryTypeOfConstructionButtonSelectText, leadStatusButtonSelectText,
            nextVisitDateButtonSelectText, requirementTypeButtonSelectText, exWorksButtonSelectText, assignedToButtonSelectText, requirementTimingButtonSelectText;

    // Existing Query Generate
    LinearLayout existingGenerationLayout, existingExWorksButtonLayout, existingFosEditTextLayout;
    TextView existingTextSalesOfficerName, existingTextDateStamp, existingTextTimeStamp, existingTextLatitude, existingTextLongitude, existingTextSoldToPartyName,
            existingTextSoldToPartyCode, existingTextSoldToPartyAddress, existingTextSoldToPartyState, existingTextSoldToPartyDistricts, existingTextShipToPartyName, existingTextShipToPartyCode,
            existingTextShipToPartyAddress, existingTextShipToPartyState, existingTextShipToPartyDistricts, existingTextTotalPotentialOfSite, existingTextQuotationQuantity, existingTextCurrentBrandUsed, existingTextExpectedRatePerBag, existingTextCurrentPriceStarRsPerBag,
            existingTextCurrentPriceCompetitorRsPerBag, existingTextContactPersonName, existingTextDesignation, existingTextContactNumber, existingTextMailId, existingTextFosSiding, existingTextSalesOfficerRemarks,
            existingTextReferredBy;
    EditText existingEditTextSalesOfficerName, existingEditTextDateStamp, existingEditTextTimeStamp, existingEditTextLatitude, existingEditTextLongitude, existingEditTextSoldToPartyName,
            existingEditTextSoldToPartyCode, existingEditTextSoldToPartyAddress, existingEditTextSoldToPartyState, existingEditTextSoldToPartyDistricts, existingEditTextShipToPartyName, existingEditTextShipToPartyCode,
            existingEditTextShipToPartyAddress, existingEditTextShipToPartyState, existingEditTextShipToPartyDistricts, existingEditTextTotalPotentialOfSite, existingEditTextQuotationQuantity, existingEditTextCurrentBrandUsed, existingEditTextExpectedRatePerBag,
            existingEditTextCurrentPriceStarRsPerBag, existingEditTextCurrentPriceCompetitorRsPerBag, existingEditTextContactPersonName, existingEditTextDesignation, existingEditTextContactNumber, existingEditTextMailId,
            existingEditTextFosSiding, existingEditTextSalesOfficerRemarks, existingEditTextReferredBy;
    Button existingUniqueLeadIDButton, existingSegmentButton, existingLeadSourceButton, existingProductPackagingButton, existingModeOfPaymentButton, existingCreditTermsButton, existingAacBlockRequiredNotButton,
            existingCategoryTypeOfConstructionButton, existingLeadStatusButton, existingNextVisitDateButton, existingRequirementTypeButton, existingExWorksButton, existingAssignedToButton, existingRequirementTimingButton,
            existingShareLeadImageButton;
    TextView existingTextUniqueLeadID, existingSegmentButtonSelectText, existingLeadSourceButtonSelectText, existingProductPackagingButtonSelectText, existingModeOfPaymentButtonSelectText, existingCreditTermsButtonSelectText,
            existingAacBlockRequiredNotButtonSelectText, existingCategoryTypeOfConstructionButtonSelectText, existingLeadStatusButtonSelectText, existingNextVisitDateButtonSelectText,
            existingRequirementTypeButtonSelectText, existingExWorksButtonSelectText, existingAssignedToButtonSelectText, existingRequirementTimingButtonSelectText;
    Button saveAsDraftButton, moveToLeadButton;

    // Lead Status wise Filter Layout
    LinearLayout leadStatusRadioGroupLayout;
    RadioGroup leadStatusFilterRadioGroup;
    RadioButton hotStatusRadioButton, warmStatusRadioButton, coldStatusRadioButton;

    // Progress Bar
    ProgressDialog progressDialog;

    // Array List Data Set
    ArrayList<DataSet> segmentList = new ArrayList<>();
    ArrayList<DataSet> leadSourceList = new ArrayList<>();
    ArrayList<DataSet> modeOfPaymentList = new ArrayList<>();
    ArrayList<DataSet> creditTermsList = new ArrayList<>();
    ArrayList<DataSet> aacBlockRequiredCheckList = new ArrayList<>();
    ArrayList<DataSet> constructionTypeList = new ArrayList<>();
    ArrayList<DataSet> leadStatusList = new ArrayList<>();
    ArrayList<DataSet> requirementTypeList = new ArrayList<>();
    ArrayList<DataSet> exWorkList = new ArrayList<>();
    ArrayList<DataSet> requirementTimingList = new ArrayList<>();
    ArrayList<DataSet> leadActionList = new ArrayList<>();
    ArrayList<DataSet> productList = new ArrayList<>();
    ArrayList<EmployeeDataSet> employeeMasterDetailsList = new ArrayList<>();
    ArrayList<EmployeeDataSet> assignedToDetailsList = new ArrayList<>();
    ArrayList<PartyDataList> soldToPartyList = new ArrayList<>();
    ArrayList<PartyDataList> shipToPartyList = new ArrayList<>();
    ArrayList<StateDataSet> stateList = new ArrayList<>();
    ArrayList<DistrictDataSet> districtList = new ArrayList<>();
    ArrayList<LeadDataSet> existingLeadList = new ArrayList<>();
    ArrayList<LeadDataSet> allExistingLeadList = new ArrayList<>();

    // data store
    String leadId = "", officerFor = "", selectEmpCode = "", dateString = "", timeString = "", latitude = "", longitude = "", soldToPartyName = "", soldToPartyAddress = "", soldToPartyCode = "", soldToPartyState = "", soldToPartyDistricts = "", shipToPartyName = "", shipToPartyAddress = "", shipToPartyCode = "", shipToPartyState = "", shipToPartyDistricts = "", segment = "", leadSource = "", productPackaging = "", potentialOfSite = "", quotationQuantity = "", currentBrandUsed = "", expectedRatePerBag = "", currentPriceStarRsPerBag = "", currentPriceCompetitorRsPerBag = "", contactPersonName = "", contactPersonDesignation = "", contactPersonNumber = "", contactPersonMail = "", modeOfPayment = "", creditTerms = "", aacBlockRequiredStatus = "", constructionType = "", leadStatus = "", nextVisitDate = "", requirementType = "", exWorks = "", fosText = "", salesOfficerRemarks = "", referredBy = "", assignedTo = "", requirementTiming = "", filterFormDate = "", filterToDate = "", filterStatus = "";
    String TYPE_OF_LEAD = "", TYPE_OF_LEAD_STATUS = "";
    boolean isSalesOfficerNameSelect = false, isButtonPressForExistingLeadButton = false;

    NewDatabaseForSiteLead mNewDatabaseForSiteLead;

    // ==================== Override Function ==================== //
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_query_generation);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

        mContext = QueryGenerationActivity.this;
        mNewDatabaseForSiteLead=new NewDatabaseForSiteLead(mContext);

        dataSetSegmentList();

//        dataSet(Constants.employeeDetailObject.getEmpCode());

        init();
    }

    @Override
    public void onClick(View view) {
        if (view == backButton) {
            finish();
        }
        newLeadOnClickFunction(view);
        existingLeadOnClickFunction(view);


        //submit
        if (view == saveAsDraftButton) {
            if (TYPE_OF_LEAD.equalsIgnoreCase("new")) {
                requestForQueryGeneration("PENDING");
            } else if (TYPE_OF_LEAD.equalsIgnoreCase("existing")) {
                requestForUpdaterQueryGeneration("PENDING");
            } else {
                requestForQueryGeneration("PENDING");
            }
        }
        if (view == moveToLeadButton) {
            if (TYPE_OF_LEAD.equalsIgnoreCase("new")) {
                checkData();
            } else {
                checkDataForUpdate();
            }
        }
    }

    @Override
    public void onCheckedChanged(RadioGroup radioGroup, int i) {
        if (i == R.id.newLeadRadioButton) {
            TYPE_OF_LEAD = "new";
            TYPE_OF_LEAD_STATUS = "";
            runOnUiThread(() -> {
                clearAllDataFromExistingLead();
                cleanAllDataFromNewLeadGeneration();
                leadStatusFilterRadioGroup.clearCheck();
                leadGenerationLayout.setVisibility(VISIBLE);
                existingGenerationLayout.setVisibility(GONE);
                leadStatusRadioGroupLayout.setVisibility(GONE);
            });
        }
        if (i == R.id.existingLeadRadioButton) {
            TYPE_OF_LEAD = "existing";
            TYPE_OF_LEAD_STATUS = "";
            runOnUiThread(() -> {
                clearAllDataFromExistingLead();
                cleanAllDataFromNewLeadGeneration();
                leadStatusFilterRadioGroup.clearCheck();
                leadStatusRadioGroupLayout.setVisibility(VISIBLE);
                leadGenerationLayout.setVisibility(GONE);
            });
        }
        if (i == R.id.repeatLeadRadioButton) {
            TYPE_OF_LEAD = "repeat";
            TYPE_OF_LEAD_STATUS = "";
            runOnUiThread(() -> {
                clearAllDataFromExistingLead();
                cleanAllDataFromNewLeadGeneration();
                leadStatusFilterRadioGroup.clearCheck();
                leadStatusRadioGroupLayout.setVisibility(VISIBLE);
                leadGenerationLayout.setVisibility(GONE);
                getDefaultData();
            });
        }

        if (i == R.id.hotStatusRadioButton) {
            if (!TYPE_OF_LEAD_STATUS.equalsIgnoreCase("hot")) {
                clearAllDataFromExistingLead();
            }
            TYPE_OF_LEAD_STATUS = "hot";
            runOnUiThread(() -> {
                existingGenerationLayout.setVisibility(VISIBLE);
            });
        }
        if (i == R.id.warmStatusRadioButton) {
            if (!TYPE_OF_LEAD_STATUS.equalsIgnoreCase("warm")) {
                clearAllDataFromExistingLead();
            }
            TYPE_OF_LEAD_STATUS = "warm";
            runOnUiThread(() -> {
                existingGenerationLayout.setVisibility(VISIBLE);
            });
        }
        if (i == R.id.coldStatusRadioButton) {
            if (!TYPE_OF_LEAD_STATUS.equalsIgnoreCase("cold")) {
                clearAllDataFromExistingLead();
            }
            TYPE_OF_LEAD_STATUS = "cold";
            runOnUiThread(() -> {
                existingGenerationLayout.setVisibility(VISIBLE);
            });
        }

        if (i == R.id.selfRadioButton) {
            runOnUiThread(() -> {
                isSalesOfficerNameSelect = true;
                cleanAllDataFromNewLeadGeneration();
                officerFor = "SELF";
                selectEmpCode = Constants.employeeDetailObject.getEmpCode();
                otherSalesOfficeNameButtonLayout.setVisibility(GONE);
                salesOfficerDetailsLayout.setVisibility(VISIBLE);

                editTextSalesOfficerName.setText(Constants.employeeDetailObject.getEmpName());
                editTextDateStamp.setText(dateString);
                editTextTimeStamp.setText(timeString);
                editTextLatitude.setText(latitude);
                editTextLongitude.setText(longitude);

                _DOWNLOAD_sold_to_party(Constants.employeeDetailObject.getEmpCode());
//                dataSet(Constants.employeeDetailObject.getEmpCode());
            });
        }
        if (i == R.id.othersRadioButton) {
            runOnUiThread(() -> {
                isSalesOfficerNameSelect = false;
                cleanAllDataFromNewLeadGeneration();
                officerFor = "OTHER";
                selectEmpCode = "";
                otherSalesOfficeNameButtonLayout.setVisibility(VISIBLE);
                salesOfficerDetailsLayout.setVisibility(VISIBLE);

                editTextDateStamp.setText(dateString);
                editTextTimeStamp.setText(timeString);
                editTextLatitude.setText(latitude);
                editTextLongitude.setText(longitude);
                editTextSalesOfficerName.setText("");

                _DOWNLOAD_emp_master();
            });
        }
    }
    // ======================================== //


    // ==================== onClick Function ==================== //
    // New query onClick function
    private void newLeadOnClickFunction(View view) {
        /*
         * New Query Generate
         */
        // Sales Officer Name Select Button
        if (view == salesOfficerNameButton) {
            show_sale_officer_list_dialog(employeeMasterDetailsList, "Please select Sales Officer", "sales_officer_name");
        }
        // Sold to Party Name Select Button
        if (view == soldToPartyNameButton) {
            if (isSalesOfficerNameSelect) {
                show_party_name_list_dialog(soldToPartyList, "Select Sold to Party", "sold_to_party_name");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Sold to Party State Name Select Button
        if (view == soldToPartyStateButton) {
            if (isSalesOfficerNameSelect) {
                if (editTextSoldToPartyCode.getText().toString().trim().equalsIgnoreCase("")) {
                    show_state_list_dialog(stateList, "sold_party_state_name");
                } else {
                    Toast.makeText(mContext, "You can not update state.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Sold to Party District Name Select Button
        if (view == soldToPartyDistrictsButton) {
            if (isSalesOfficerNameSelect) {
                if (editTextSoldToPartyCode.getText().toString().trim().equalsIgnoreCase("")) {
                    show_district_list_dialog("sold_party_district_name");
                } else {
                    Toast.makeText(mContext, "You can not update district.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Ship to Party Name Select Button
        if (view == shipToPartyNameButton) {
            if (isSalesOfficerNameSelect) {
                show_party_name_list_dialog(shipToPartyList, "Select Ship to Party", "ship_party_name");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Ship to Party State Name Select Button
        if (view == shipToPartyStateButton) {
            if (isSalesOfficerNameSelect) {
                if (editTextShipToPartyCode.getText().toString().trim().equalsIgnoreCase("")) {
                    show_state_list_dialog(stateList, "ship_party_state_name");
                } else {
                    Toast.makeText(mContext, "You can not update state.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Ship to Party District Name Select Button
        if (view == shipToPartyDistrictsButton) {
            if (isSalesOfficerNameSelect) {
                if (editTextShipToPartyCode.getText().toString().trim().equalsIgnoreCase("")) {
                    show_district_list_dialog("ship_party_district_name");
                } else {
                    Toast.makeText(mContext, "You can not update district.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Segment Select Button
        if (view == segmentButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(segmentList, "segment");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Query Source Select Button
        if (view == leadSourceButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(leadSourceList, "lead_source");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Product Packaging Select Button
        if (view == productPackagingButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(productList, "product");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Mode of Payment Select Button
        if (view == modeOfPaymentButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(modeOfPaymentList, "mode_of_payment");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Credit Terms Select button
        if (view == creditTermsButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(creditTermsList, "credit_terms");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // AAC Block is Required or Not Select Button
        if (view == aacBlockRequiredNotButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(aacBlockRequiredCheckList, "aac_block_required_check");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Category Type of Construction Select Button
        if (view == categoryTypeOfConstructionButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(constructionTypeList, "construction_type");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Query Status Select Button
        if (view == leadStatusButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(leadStatusList, "lead_status");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Next Visit Date Select Button
        if (view == nextVisitDateButton) {
            if (isSalesOfficerNameSelect) {
                nextVisitDatePicker();
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Requirement Type Select Button
        if (view == requirementTypeButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(requirementTypeList, "requirement_type");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Ex Works Select Button
        if (view == exWorksButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(exWorkList, "ex_work");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Assigned To Select Button
        if (view == assignedToButton) {
            if (isSalesOfficerNameSelect) {
                show_sale_officer_list_dialog(assignedToDetailsList, "Please select Assigned Person", "assigned_to");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        // Requirement Timing Select Button
        if (view == requirementTimingButton) {
            if (isSalesOfficerNameSelect) {
                show_list_data_dialog(requirementTimingList, "requirement_timing");
            } else {
                Toast.makeText(mContext, "Please select sales officer name first.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == shareLeadImageButton) {
            Log.d("TAG", "newLeadOnClickFunction: Image button click");
        }
    }

    // existing query onClick function
    private void existingLeadOnClickFunction(View view) {
        /*
         * Existing Query Generate
         */
        // Existing Query Select Button
        if (view == existingUniqueLeadIDButton) {
            if (TYPE_OF_LEAD.equalsIgnoreCase("existing")) {
                existingLeadList.clear();
                for (int i = 0; i < allExistingLeadList.size(); i++) {
                    LeadDataSet obj = allExistingLeadList.get(i);
                    try {
                        if (obj.getFullData().getString("lead_status").equalsIgnoreCase(TYPE_OF_LEAD_STATUS)&& obj.getLeadStatus().equalsIgnoreCase("pending")) {
                            existingLeadList.add(obj);
                        }
                    } catch (JSONException e) {
                        throw new RuntimeException(e);
                    }
                }
                show_lead_list_dialog(existingLeadList, "Please select Existing Lead");
            } else if (TYPE_OF_LEAD.equalsIgnoreCase("repeat")) {
                existingLeadList.clear();
                for (int i = 0; i < allExistingLeadList.size(); i++) {
                    LeadDataSet obj = allExistingLeadList.get(i);
                    try {
                        if (obj.getFullData().getString("lead_status").equalsIgnoreCase(TYPE_OF_LEAD_STATUS) && obj.getLeadStatus().equalsIgnoreCase("yes")) {
                            existingLeadList.add(obj);
                        }
                    } catch (JSONException e) {
                        throw new RuntimeException(e);
                    }
                }
                show_lead_list_dialog(existingLeadList, "Please select Existing Lead");
            }

        }
        // Segment Select Button
        if (view == existingSegmentButton) {
            show_list_data_dialog1(segmentList, "segment");
        }
        // Query Source Select Button
        if (view == existingLeadSourceButton) {
            show_list_data_dialog1(leadSourceList, "lead_source");
        }
        // Product Package Select Button
        if (view == existingProductPackagingButton) {
            show_list_data_dialog1(productList, "product");
        }
        // Mode of Payment Select Button
        if (view == existingModeOfPaymentButton) {
            show_list_data_dialog1(modeOfPaymentList, "mode_of_payment");
        }
        // Credit Terms Select Button
        if (view == existingCreditTermsButton) {
            show_list_data_dialog1(creditTermsList, "credit_terms");
        }
        // AAC block is Required Select Button
        if (view == existingAacBlockRequiredNotButton) {
            show_list_data_dialog1(aacBlockRequiredCheckList, "aac_block_required_check");
        }
        // Category Type of Construction Select Button
        if (view == existingCategoryTypeOfConstructionButton) {
            show_list_data_dialog1(constructionTypeList, "construction_type");
        }
        // Query Status Select Button
        if (view == existingLeadStatusButton) {
            show_list_data_dialog1(leadStatusList, "lead_status");
        }
        // Next Visit Date Select Button
        if (view == existingNextVisitDateButton) {
            nextVisitDatePicker1();
        }
        // Requirement Type Select Button
        if (view == existingRequirementTypeButton) {
            show_list_data_dialog1(requirementTypeList, "requirement_type");
        }
        // Ex Works Select Button
        if (view == existingExWorksButton) {
            show_list_data_dialog1(exWorkList, "ex_work");
        }
        // Requirement Timing Select Button
        if (view == existingRequirementTimingButton) {
            show_list_data_dialog1(requirementTimingList, "requirement_timing");
        }
        if(view==existingAssignedToButton){
            show_sale_officer_list_dialog(assignedToDetailsList, "Please select Assigned Person", "assigned_to");
        }
    }
    // ======================================== //


    // ==================== Clear All Field ==================== //
    // Clean All Previous Data from new query
    private void cleanAllDataFromNewLeadGeneration() {
        exWorksButtonLayout.setVisibility(GONE);
        fosEditTextLayout.setVisibility(GONE);

        editTextSalesOfficerName.setText("");
        editTextDateStamp.setText("");
        editTextTimeStamp.setText("");
        editTextLatitude.setText("");
        editTextLongitude.setText("");
        editTextSoldToPartyName.setText("");
        editTextSoldToPartyCode.setText("");
        editTextSoldToPartyAddress.setText("");
        editTextShipToPartyName.setText("");
        editTextShipToPartyCode.setText("");
        editTextShipToPartyAddress.setText("");
        editTextTotalPotentialOfSite.setText("");
        editTextQuotationQuantity.setText("");
        editTextCurrentBrandUsed.setText("");
        editTextExpectedRatePerBag.setText("");
        editTextCurrentPriceStarRsPerBag.setText("");
        editTextCurrentPriceCompetitorRsPerBag.setText("");
        editTextContactPersonName.setText("");
        editTextDesignation.setText("");
        editTextContactNumber.setText("");
        editTextMailId.setText("");
        editTextSalesOfficerRemarks.setText("");
        editTextFosSiding.setText("");

        soldToPartyStateButtonSelectText.setVisibility(GONE);
        soldToPartyStateButtonSelectText.setText("");
        soldToPartyDistrictsButtonSelectText.setVisibility(GONE);
        soldToPartyDistrictsButtonSelectText.setText("");
        shipToPartyStateButtonSelectText.setVisibility(GONE);
        shipToPartyStateButtonSelectText.setText("");
        shipToPartyDistrictsButtonSelectText.setVisibility(GONE);
        shipToPartyDistrictsButtonSelectText.setText("");
        segmentButtonSelectText.setVisibility(GONE);
        segmentButtonSelectText.setText("");
        leadSourceButtonSelectText.setVisibility(GONE);
        leadSourceButtonSelectText.setText("");
        productPackagingButtonSelectText.setVisibility(GONE);
        productPackagingButtonSelectText.setText("");
        modeOfPaymentButtonSelectText.setVisibility(GONE);
        modeOfPaymentButtonSelectText.setText("");
        creditTermsButtonSelectText.setVisibility(GONE);
        creditTermsButtonSelectText.setText("");
        aacBlockRequiredNotButtonSelectText.setVisibility(GONE);
        aacBlockRequiredNotButtonSelectText.setText("");
        categoryTypeOfConstructionButtonSelectText.setVisibility(GONE);
        categoryTypeOfConstructionButtonSelectText.setText("");
        leadStatusButtonSelectText.setVisibility(GONE);
        leadStatusButtonSelectText.setText("");
        nextVisitDateButtonSelectText.setVisibility(GONE);
        nextVisitDateButtonSelectText.setText("");
        requirementTypeButtonSelectText.setVisibility(GONE);
        requirementTypeButtonSelectText.setText("");
        exWorksButtonSelectText.setVisibility(GONE);
        exWorksButtonSelectText.setText("");
        assignedToButtonSelectText.setVisibility(GONE);
        assignedToButtonSelectText.setText("");
        requirementTimingButtonSelectText.setVisibility(GONE);
        requirementTimingButtonSelectText.setText("");
    }

    // Clean All Previous Data from existing query
    private void clearAllDataFromExistingLead() {
        existingExWorksButtonLayout.setVisibility(GONE);
        existingFosEditTextLayout.setVisibility(GONE);

        existingEditTextSalesOfficerName.setText("");
        if(!TYPE_OF_LEAD.equalsIgnoreCase("repeat")){
            existingEditTextDateStamp.setText("");
            existingEditTextTimeStamp.setText("");
            existingEditTextLatitude.setText("");
            existingEditTextLongitude.setText("");
        }
        existingEditTextSoldToPartyName.setText("");
        existingEditTextSoldToPartyCode.setText("");
        existingEditTextSoldToPartyAddress.setText("");
        existingEditTextSoldToPartyState.setText("");
        existingEditTextSoldToPartyDistricts.setText("");
        existingEditTextShipToPartyName.setText("");
        existingEditTextShipToPartyCode.setText("");
        existingEditTextShipToPartyAddress.setText("");
        existingEditTextShipToPartyState.setText("");
        existingEditTextShipToPartyDistricts.setText("");
        existingEditTextTotalPotentialOfSite.setText("");
        existingEditTextQuotationQuantity.setText("");
        existingEditTextCurrentBrandUsed.setText("");
        existingEditTextExpectedRatePerBag.setText("");
        existingEditTextCurrentPriceStarRsPerBag.setText("");
        existingEditTextCurrentPriceCompetitorRsPerBag.setText("");
        existingEditTextContactPersonName.setText("");
        existingEditTextDesignation.setText("");
        existingEditTextContactNumber.setText("");
        existingEditTextMailId.setText("");
        existingEditTextFosSiding.setText("");
        existingEditTextSalesOfficerRemarks.setText("");
        existingEditTextReferredBy.setText("");

        existingTextUniqueLeadID.setVisibility(GONE);
        existingTextUniqueLeadID.setText("");
        existingSegmentButtonSelectText.setVisibility(GONE);
        existingSegmentButtonSelectText.setText("");
        existingLeadSourceButtonSelectText.setVisibility(GONE);
        existingLeadSourceButtonSelectText.setText("");
        existingProductPackagingButtonSelectText.setVisibility(GONE);
        existingProductPackagingButtonSelectText.setText("");
        existingModeOfPaymentButtonSelectText.setVisibility(GONE);
        existingModeOfPaymentButtonSelectText.setText("");
        existingCreditTermsButtonSelectText.setVisibility(GONE);
        existingCreditTermsButtonSelectText.setText("");
        existingAacBlockRequiredNotButtonSelectText.setVisibility(GONE);
        existingAacBlockRequiredNotButtonSelectText.setText("");
        existingCategoryTypeOfConstructionButtonSelectText.setVisibility(GONE);
        existingCategoryTypeOfConstructionButtonSelectText.setText("");
        existingLeadStatusButtonSelectText.setVisibility(GONE);
        existingLeadStatusButtonSelectText.setText("");
        existingNextVisitDateButtonSelectText.setVisibility(GONE);
        existingNextVisitDateButtonSelectText.setText("");
        existingRequirementTypeButtonSelectText.setVisibility(GONE);
        existingRequirementTypeButtonSelectText.setText("");
        existingExWorksButtonSelectText.setVisibility(GONE);
        existingExWorksButtonSelectText.setText("");
        existingAssignedToButtonSelectText.setVisibility(GONE);
        existingAssignedToButtonSelectText.setText("");
        existingRequirementTimingButtonSelectText.setVisibility(GONE);
        existingRequirementTimingButtonSelectText.setText("");
    }
    // ======================================== //


    // ==================== Init ==================== //
    // Primary init function
    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        leadTypeRadioGroupLayout = findViewById(R.id.leadTypeRadioGroupLayout);
        leadTypeRadioGroupLayout.setVisibility(VISIBLE);

        leadTypeRadioGroup = findViewById(R.id.leadTypeRadioGroup);
        leadTypeRadioGroup.setOnCheckedChangeListener(this);
        newLeadRadioButton = findViewById(R.id.newLeadRadioButton);
        newLeadRadioButton.setText(Html.fromHtml("New"));
        existingLeadRadioButton = findViewById(R.id.existingLeadRadioButton);
        existingLeadRadioButton.setText(Html.fromHtml("Existing"));
        repeatLeadRadioButton = findViewById(R.id.repeatLeadRadioButton);
        repeatLeadRadioButton.setText(Html.fromHtml("Repeat"));

        leadStatusRadioGroupLayout = findViewById(R.id.leadStatusRadioGroupLayout);
        leadStatusRadioGroupLayout.setVisibility(GONE);
        leadStatusFilterRadioGroup = findViewById(R.id.leadStatusFilterRadioGroup);
        leadStatusFilterRadioGroup.setOnCheckedChangeListener(this);
        hotStatusRadioButton = findViewById(R.id.hotStatusRadioButton);
        hotStatusRadioButton.setText(Html.fromHtml("HOT"));
        warmStatusRadioButton = findViewById(R.id.warmStatusRadioButton);
        warmStatusRadioButton.setText(Html.fromHtml("WARM"));
        coldStatusRadioButton = findViewById(R.id.coldStatusRadioButton);
        coldStatusRadioButton.setText(Html.fromHtml("COLD"));

        saveAsDraftButton = findViewById(R.id.saveAsDraftButton);
        saveAsDraftButton.setOnClickListener(this);

        moveToLeadButton = findViewById(R.id.moveToLeadButton);
        moveToLeadButton.setOnClickListener(this);

        try {
            initLayout();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ onCreate 3: " + e.getMessage());
        }
    }

    // Primary init function
    private void initLayout() {
        // ***New Query Generation
        leadGenerationLayout = findViewById(R.id.leadGenerationLayout);
        leadGenerationLayout.setVisibility(GONE);
        // Sales Officer Details
        salesOfficerDetailsLayout = findViewById(R.id.salesOfficerDetailsLayout);
        salesOfficerDetailsLayout.setVisibility(GONE);
        // Other Sales Office Name
        otherSalesOfficeNameButtonLayout = findViewById(R.id.otherSalesOfficeNameButtonLayout);
        otherSalesOfficeNameButtonLayout.setVisibility(GONE);
        // Ex Works Layout
        exWorksButtonLayout = findViewById(R.id.exWorksButtonLayout);
        exWorksButtonLayout.setVisibility(GONE);
        // FOS Layout
        fosEditTextLayout = findViewById(R.id.fosEditTextLayout);
        fosEditTextLayout.setVisibility(GONE);
        // init All other layout
        try {
            initLeadGeneration();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ onCreate 4: " + e.getMessage());
        }

        // ***Existing Query Generation
        existingGenerationLayout = findViewById(R.id.existingGenerationLayout);
        existingGenerationLayout.setVisibility(GONE);
        // Ex Works Layout
        existingExWorksButtonLayout = findViewById(R.id.existingExWorksButtonLayout);
        existingExWorksButtonLayout.setVisibility(GONE);
        // FOS Layout
        existingFosEditTextLayout = findViewById(R.id.existingFosEditTextLayout);
        existingFosEditTextLayout.setVisibility(GONE);
        // init All other layout
        try {
            initExistingGeneration();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ onCreate 5: " + e.getMessage());
        }

        new TRANS_EmployeeDetails_AsyncTask(mContext).execute();
    }

    // New Query Generation init
    private void initLeadGeneration() {
        // Unique Query Id
        textUniqueLeadID = findViewById(R.id.textUniqueLeadID);
        textUniqueLeadID.setText(Html.fromHtml("Unique Enquiry ID"));
        editTextUniqueLeadID = findViewById(R.id.editTextUniqueLeadID);
        editTextUniqueLeadID.setHint(Html.fromHtml("Auto Generated"));
        editTextUniqueLeadID.setEnabled(false);

        // Generate For Radio Button
        textGenerateFor = findViewById(R.id.textGenerateFor);
        textGenerateFor.setText(Html.fromHtml("Self / Others "));
        generateForRadioGroup = findViewById(R.id.generateForRadioGroup);
        selfRadioButton = findViewById(R.id.selfRadioButton);
        selfRadioButton.setText(Html.fromHtml("SELF"));
        othersRadioButton = findViewById(R.id.othersRadioButton);
        othersRadioButton.setText(Html.fromHtml("OTHERS"));
        generateForRadioGroup.setOnCheckedChangeListener(this);

        // Sales Officer Name Button
        salesOfficerNameButton = findViewById(R.id.salesOfficerNameButton);
        salesOfficerNameButton.setText(Html.fromHtml("Sales Officer Name"));
        salesOfficerNameButton.setOnClickListener(this);

        // Sales Officer Name
        textSalesOfficerName = findViewById(R.id.textSalesOfficerName);
        textSalesOfficerName.setText(Html.fromHtml("Sales Officer Name "));
        editTextSalesOfficerName = findViewById(R.id.editTextSalesOfficerName);
        editTextSalesOfficerName.setHint(Html.fromHtml("Sales Officer Name "));
        editTextSalesOfficerName.setEnabled(false);

        // Date Stamp
        textDateStamp = findViewById(R.id.textDateStamp);
        textDateStamp.setText(Html.fromHtml("Date Stamp "));
        editTextDateStamp = findViewById(R.id.editTextDateStamp);
        editTextDateStamp.setHint(Html.fromHtml("Auto Fetch"));
        editTextDateStamp.setEnabled(false);

        // Time Stamp
        textTimeStamp = findViewById(R.id.textTimeStamp);
        textTimeStamp.setText(Html.fromHtml("Time Stamp "));
        editTextTimeStamp = findViewById(R.id.editTextTimeStamp);
        editTextTimeStamp.setHint(Html.fromHtml("Auto Fetch"));
        editTextTimeStamp.setEnabled(false);

        // Latitude
        textLatitude = findViewById(R.id.textLatitude);
        textLatitude.setText(Html.fromHtml("Latitude "));
        editTextLatitude = findViewById(R.id.editTextLatitude);
        editTextLatitude.setHint(Html.fromHtml("Auto Fetch"));
        editTextLatitude.setEnabled(false);

        // Longitude
        textLongitude = findViewById(R.id.textLongitude);
        textLongitude.setText(Html.fromHtml("Longitude "));
        editTextLongitude = findViewById(R.id.editTextLongitude);
        editTextLongitude.setHint(Html.fromHtml("Auto Fetch"));
        editTextLongitude.setEnabled(false);

        // Sold to Party Name Button
        soldToPartyNameButton = findViewById(R.id.soldToPartyNameButton);
        soldToPartyNameButton.setText(Html.fromHtml("Sold to Party Name"));
        soldToPartyNameButton.setOnClickListener(this);


        // Sold to Party Name
        textSoldToPartyName = findViewById(R.id.textSoldToPartyName);
        textSoldToPartyName.setText(Html.fromHtml("Sold to Party Name "));
        editTextSoldToPartyName = findViewById(R.id.editTextSoldToPartyName);
        editTextSoldToPartyName.setHint(Html.fromHtml("Sold to Party Name "));

        // Sold to Party Code
        textSoldToPartyCode = findViewById(R.id.textSoldToPartyCode);
        textSoldToPartyCode.setText(Html.fromHtml("Sold to Party Code"));
        editTextSoldToPartyCode = findViewById(R.id.editTextSoldToPartyCode);
        editTextSoldToPartyCode.setHint(Html.fromHtml("Auto Generated"));
        editTextSoldToPartyCode.setEnabled(false);

        // Sold to Party Address
        textSoldToPartyAddress = findViewById(R.id.textSoldToPartyAddress);
        textSoldToPartyAddress.setText(Html.fromHtml("Sold to Party Address "));
        editTextSoldToPartyAddress = findViewById(R.id.editTextSoldToPartyAddress);
        editTextSoldToPartyAddress.setHint(Html.fromHtml("Sold to Party Address "));

        // Sold to party State Button
        soldToPartyStateButton = findViewById(R.id.soldToPartyStateButton);
        soldToPartyStateButton.setText(Html.fromHtml("Sold to Party State "));
        soldToPartyStateButton.setOnClickListener(this);
        soldToPartyStateButtonSelectText = findViewById(R.id.soldToPartyStateButtonSelectText);
        soldToPartyStateButtonSelectText.setVisibility(GONE);

        // Sold to Party Districts Button
        soldToPartyDistrictsButton = findViewById(R.id.soldToPartyDistrictsButton);
        soldToPartyDistrictsButton.setText(Html.fromHtml("Sold to Party Districts "));
        soldToPartyDistrictsButton.setOnClickListener(this);
        soldToPartyDistrictsButtonSelectText = findViewById(R.id.soldToPartyDistrictsButtonSelectText);
        soldToPartyDistrictsButtonSelectText.setVisibility(GONE);

        // Ship to Party Name Button
        shipToPartyNameButton = findViewById(R.id.shipToPartyNameButton);
        shipToPartyNameButton.setText(Html.fromHtml("Ship to Party Name"));
        shipToPartyNameButton.setOnClickListener(this);

        // Ship to Party Name
        textShipToPartyName = findViewById(R.id.textShipToPartyName);
        textShipToPartyName.setText(Html.fromHtml("Ship to Party Name "));
        editTextShipToPartyName = findViewById(R.id.editTextShipToPartyName);
        editTextShipToPartyName.setHint(Html.fromHtml("Ship to Party Name "));

        // Ship to Party Code
        textShipToPartyCode = findViewById(R.id.textShipToPartyCode);
        textShipToPartyCode.setText(Html.fromHtml("Ship to Party Code"));
        editTextShipToPartyCode = findViewById(R.id.editTextShipToPartyCode);
        editTextShipToPartyCode.setHint(Html.fromHtml("Auto Generated"));
        editTextShipToPartyCode.setEnabled(false);

        // Ship to Party Address
        textShipToPartyAddress = findViewById(R.id.textShipToPartyAddress);
        textShipToPartyAddress.setText(Html.fromHtml("Ship to Party Address "));
        editTextShipToPartyAddress = findViewById(R.id.editTextShipToPartyAddress);
        editTextShipToPartyAddress.setHint(Html.fromHtml("Ship to Party Address "));

        // Ship to Party State Button
        shipToPartyStateButton = findViewById(R.id.shipToPartyStateButton);
        shipToPartyStateButton.setText(Html.fromHtml("Ship to Party State "));
        shipToPartyStateButton.setOnClickListener(this);
        shipToPartyStateButtonSelectText = findViewById(R.id.shipToPartyStateButtonSelectText);
        shipToPartyStateButtonSelectText.setVisibility(GONE);

        // Ship to Party Districts Button
        shipToPartyDistrictsButton = findViewById(R.id.shipToPartyDistrictsButton);
        shipToPartyDistrictsButton.setText(Html.fromHtml("Ship to Party Districts "));
        shipToPartyDistrictsButton.setOnClickListener(this);
        shipToPartyDistrictsButtonSelectText = findViewById(R.id.shipToPartyDistrictsButtonSelectText);
        shipToPartyDistrictsButtonSelectText.setVisibility(GONE);

        // Segment Button
        segmentButton = findViewById(R.id.segmentButton);
        segmentButton.setText(Html.fromHtml("Segment "));
        segmentButton.setOnClickListener(this);
        segmentButtonSelectText = findViewById(R.id.segmentButtonSelectText);
        segmentButtonSelectText.setVisibility(GONE);

        // Query Source Button
        leadSourceButton = findViewById(R.id.leadSourceButton);
        leadSourceButton.setText(Html.fromHtml("Enquiry Source "));
        leadSourceButton.setOnClickListener(this);
        leadSourceButtonSelectText = findViewById(R.id.leadSourceButtonSelectText);
        leadSourceButtonSelectText.setVisibility(GONE);

        // Product List Button
        productPackagingButton = findViewById(R.id.productPackagingButton);
        productPackagingButton.setText(Html.fromHtml("Product + Packaging"));
        productPackagingButton.setOnClickListener(this);
        productPackagingButtonSelectText = findViewById(R.id.productPackagingButtonSelectText);
        productPackagingButtonSelectText.setVisibility(GONE);

        // Total Potential of Site (MT)
        textTotalPotentialOfSite = findViewById(R.id.textTotalPotentialOfSite);
        textTotalPotentialOfSite.setText(Html.fromHtml("Total Potential of Site (MT)"));
        editTextTotalPotentialOfSite = findViewById(R.id.editTextTotalPotentialOfSite);
        editTextTotalPotentialOfSite.setHint(Html.fromHtml("Total Qty Required (MT) "));
        editTextTotalPotentialOfSite.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Quotation Quantity (MT)
        textQuotationQuantity = findViewById(R.id.textQuotationQuantity);
        textQuotationQuantity.setText(Html.fromHtml("Quotation Quantity (MT)"));
        editTextQuotationQuantity = findViewById(R.id.editTextQuotationQuantity);
        editTextQuotationQuantity.setHint(Html.fromHtml("Monthly Qty Required (MT) "));
        editTextQuotationQuantity.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Current Brand Used
        textCurrentBrandUsed = findViewById(R.id.textCurrentBrandUsed);
        textCurrentBrandUsed.setText(Html.fromHtml("Current Brand Used "));
        editTextCurrentBrandUsed = findViewById(R.id.editTextCurrentBrandUsed);
        editTextCurrentBrandUsed.setHint(Html.fromHtml("Current Brand Used "));

        // Expected Rate per Bag
        textExpectedRatePerBag = findViewById(R.id.textExpectedRatePerBag);
        textExpectedRatePerBag.setText(Html.fromHtml("Expected Rate Per Bag "));
        editTextExpectedRatePerBag = findViewById(R.id.editTextExpectedRatePerBag);
        editTextExpectedRatePerBag.setHint(Html.fromHtml("Expected Rate Per Bag "));
        editTextExpectedRatePerBag.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Current Price Star Rs per Bag
        textCurrentPriceStarRsPerBag = findViewById(R.id.textCurrentPriceStarRsPerBag);
        textCurrentPriceStarRsPerBag.setText(Html.fromHtml("Current Price Star Rs. Per Bag "));
        editTextCurrentPriceStarRsPerBag = findViewById(R.id.editTextCurrentPriceStarRsPerBag);
        editTextCurrentPriceStarRsPerBag.setHint(Html.fromHtml("Current Price Star "));
        editTextCurrentPriceStarRsPerBag.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Current Price Competitor Rs per Bag
        textCurrentPriceCompetitorRsPerBag = findViewById(R.id.textCurrentPriceCompetitorRsPerBag);
        textCurrentPriceCompetitorRsPerBag.setText(Html.fromHtml("Current Price Competitor Rs. Per Bag "));
        editTextCurrentPriceCompetitorRsPerBag = findViewById(R.id.editTextCurrentPriceCompetitorRsPerBag);
        editTextCurrentPriceCompetitorRsPerBag.setHint(Html.fromHtml("Current Price Competitor "));
        editTextCurrentPriceCompetitorRsPerBag.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Contact Person Name
        textContactPersonName = findViewById(R.id.textContactPersonName);
        textContactPersonName.setText(Html.fromHtml("Contact Person Name "));
        editTextContactPersonName = findViewById(R.id.editTextContactPersonName);
        editTextContactPersonName.setHint(Html.fromHtml("Contact Person Name "));

        // Designation
        textDesignation = findViewById(R.id.textDesignation);
        textDesignation.setText(Html.fromHtml("Designation "));
        editTextDesignation = findViewById(R.id.editTextDesignation);
        editTextDesignation.setHint(Html.fromHtml("Designation "));

        // Contact Number
        textContactNumber = findViewById(R.id.textContactNumber);
        textContactNumber.setText(Html.fromHtml("Contact Number "));
        editTextContactNumber = findViewById(R.id.editTextContactNumber);
        editTextContactNumber.setHint(Html.fromHtml("Contact Number "));
        editTextContactNumber.setInputType(InputType.TYPE_CLASS_PHONE);

        // Mail Id
        textMailId = findViewById(R.id.textMailId);
        textMailId.setText(Html.fromHtml("Mail Id "));
        editTextMailId = findViewById(R.id.editTextMailId);
        editTextMailId.setHint(Html.fromHtml("Mail Id "));

        // Mode of Payment Button
        modeOfPaymentButton = findViewById(R.id.modeOfPaymentButton);
        modeOfPaymentButton.setText(Html.fromHtml("Mode Of Payment"));
        modeOfPaymentButton.setOnClickListener(this);
        modeOfPaymentButtonSelectText = findViewById(R.id.modeOfPaymentButtonSelectText);
        modeOfPaymentButtonSelectText.setVisibility(GONE);

        // Credit Terms Button
        creditTermsButton = findViewById(R.id.creditTermsButton);
        creditTermsButton.setText(Html.fromHtml("Credit Terms"));
        creditTermsButton.setOnClickListener(this);
        creditTermsButtonSelectText = findViewById(R.id.creditTermsButtonSelectText);
        creditTermsButtonSelectText.setVisibility(GONE);

        // AAC Block is Required ot Not Button
        aacBlockRequiredNotButton = findViewById(R.id.aacBlockRequiredNotButton);
        aacBlockRequiredNotButton.setText(Html.fromHtml("AAC Block is Required or Not"));
        aacBlockRequiredNotButton.setOnClickListener(this);
        aacBlockRequiredNotButtonSelectText = findViewById(R.id.aacBlockRequiredNotButtonSelectText);
        aacBlockRequiredNotButtonSelectText.setVisibility(GONE);

        // Category Type of Construction Button
        categoryTypeOfConstructionButton = findViewById(R.id.categoryTypeOfConstructionButton);
        categoryTypeOfConstructionButton.setText(Html.fromHtml("Category Type of Construction "));
        categoryTypeOfConstructionButton.setOnClickListener(this);
        categoryTypeOfConstructionButtonSelectText = findViewById(R.id.categoryTypeOfConstructionButtonSelectText);
        categoryTypeOfConstructionButtonSelectText.setVisibility(GONE);

        // Query status Button
        leadStatusButton = findViewById(R.id.leadStatusButton);
        leadStatusButton.setText(Html.fromHtml("Enquiry Status "));
        leadStatusButton.setOnClickListener(this);
        leadStatusButtonSelectText = findViewById(R.id.leadStatusButtonSelectText);
        leadStatusButtonSelectText.setVisibility(GONE);

        // Next Visit Date Button
        nextVisitDateButton = findViewById(R.id.nextVisitDateButton);
        nextVisitDateButton.setText(Html.fromHtml("Next Visit Date "));
        nextVisitDateButton.setOnClickListener(this);
        nextVisitDateButtonSelectText = findViewById(R.id.nextVisitDateButtonSelectText);
        nextVisitDateButtonSelectText.setVisibility(GONE);

        // Requirement Type Button
        requirementTypeButton = findViewById(R.id.requirementTypeButton);
        requirementTypeButton.setText(Html.fromHtml("Requirement Type "));
        requirementTypeButton.setOnClickListener(this);
        requirementTypeButtonSelectText = findViewById(R.id.requirementTypeButtonSelectText);
        requirementTypeButtonSelectText.setVisibility(GONE);

        // Ex Work Button
        exWorksButton = findViewById(R.id.exWorksButton);
        exWorksButton.setText(Html.fromHtml("Ex. Works "));
        exWorksButton.setOnClickListener(this);
        exWorksButtonSelectText = findViewById(R.id.exWorksButtonSelectText);
        exWorksButtonSelectText.setVisibility(GONE);

        // FOS
        textFosSiding = findViewById(R.id.textFosSiding);
        textFosSiding.setText(Html.fromHtml("FOS Siding"));
        editTextFosSiding = findViewById(R.id.editTextFosSiding);
        editTextFosSiding.setHint(Html.fromHtml("FOS Siding"));

        // Sales Officer Remarks
        textSalesOfficerRemarks = findViewById(R.id.textSalesOfficerRemarks);
        textSalesOfficerRemarks.setText(Html.fromHtml("Sales Officer Remarks"));
        editTextSalesOfficerRemarks = findViewById(R.id.editTextSalesOfficerRemarks);
        editTextSalesOfficerRemarks.setHint(Html.fromHtml("Sales Officer Remarks"));

        // Referred By
        textReferredBy = findViewById(R.id.textReferredBy);
        textReferredBy.setText(Html.fromHtml("Referred by"));
        editTextReferredBy = findViewById(R.id.editTextReferredBy);
        editTextReferredBy.setHint(Html.fromHtml("Referred by"));

        // Assigned To Button
        assignedToButton = findViewById(R.id.assignedToButton);
        assignedToButton.setText(Html.fromHtml("Assigned To "));
        assignedToButton.setOnClickListener(this);
        assignedToButtonSelectText = findViewById(R.id.assignedToButtonSelectText);
        assignedToButtonSelectText.setVisibility(GONE);

        // Requirement Timing Button
        requirementTimingButton = findViewById(R.id.requirementTimingButton);
        requirementTimingButton.setText(Html.fromHtml("Requirement Timing "));
        requirementTimingButton.setOnClickListener(this);
        requirementTimingButtonSelectText = findViewById(R.id.requirementTimingButtonSelectText);
        requirementTimingButtonSelectText.setVisibility(GONE);

        // Image Pick Button
        shareLeadImageButton = findViewById(R.id.shareLeadImageButton);
        shareLeadImageButton.setText(Html.fromHtml("Share Enquiry / Site Details (Image)"));
        shareLeadImageButton.setOnClickListener(this);

        // Query Action Button
        leadActionButton = findViewById(R.id.leadActionButton);
        leadActionButton.setText(Html.fromHtml("Enquiry Action "));
        leadActionButton.setOnClickListener(this);

        // Set Default Value
        getDefaultData();
    }

    // Existing Query Generation init
    private void initExistingGeneration() {
        // Unique Query Id
        existingTextUniqueLeadID = findViewById(R.id.existingTextUniqueLeadID);
        existingTextUniqueLeadID.setVisibility(GONE);
        existingUniqueLeadIDButton = findViewById(R.id.existingUniqueLeadIDButton);
        existingUniqueLeadIDButton.setText(Html.fromHtml("Search Enquiry id"));
        existingUniqueLeadIDButton.setOnClickListener(this);

        // Sales Officer Name
        existingTextSalesOfficerName = findViewById(R.id.existingTextSalesOfficerName);
        existingTextSalesOfficerName.setText(Html.fromHtml("Sales Officer Name "));
        existingEditTextSalesOfficerName = findViewById(R.id.existingEditTextSalesOfficerName);
        existingEditTextSalesOfficerName.setEnabled(false);

        // Date Stamp
        existingTextDateStamp = findViewById(R.id.existingTextDateStamp);
        existingTextDateStamp.setText(Html.fromHtml("Date Stamp "));
        existingEditTextDateStamp = findViewById(R.id.existingEditTextDateStamp);
        existingEditTextDateStamp.setEnabled(false);

        // Time Stamp
        existingTextTimeStamp = findViewById(R.id.existingTextTimeStamp);
        existingTextTimeStamp.setText(Html.fromHtml("Time Stamp "));
        existingEditTextTimeStamp = findViewById(R.id.existingEditTextTimeStamp);
        existingEditTextTimeStamp.setEnabled(false);

        // Latitude
        existingTextLatitude = findViewById(R.id.existingTextLatitude);
        existingTextLatitude.setText(Html.fromHtml("Latitude "));
        existingEditTextLatitude = findViewById(R.id.existingEditTextLatitude);
        existingEditTextLatitude.setEnabled(false);

        // Longitude
        existingTextLongitude = findViewById(R.id.existingTextLongitude);
        existingTextLongitude.setText(Html.fromHtml("Longitude "));
        existingEditTextLongitude = findViewById(R.id.existingEditTextLongitude);
        existingEditTextLongitude.setEnabled(false);

        // Sold to Party Name
        existingTextSoldToPartyName = findViewById(R.id.existingTextSoldToPartyName);
        existingTextSoldToPartyName.setText(Html.fromHtml("Sold to Party Name "));
        existingEditTextSoldToPartyName = findViewById(R.id.existingEditTextSoldToPartyName);
        existingEditTextSoldToPartyName.setEnabled(false);

        // Sold to Party Code
        existingTextSoldToPartyCode = findViewById(R.id.existingTextSoldToPartyCode);
        existingTextSoldToPartyCode.setText(Html.fromHtml("Sold to Party Code"));
        existingEditTextSoldToPartyCode = findViewById(R.id.existingEditTextSoldToPartyCode);
        existingEditTextSoldToPartyCode.setEnabled(false);

        // Sold to Party Address
        existingTextSoldToPartyAddress = findViewById(R.id.existingTextSoldToPartyAddress);
        existingTextSoldToPartyAddress.setText(Html.fromHtml("Sold to Party Address "));
        existingEditTextSoldToPartyAddress = findViewById(R.id.existingEditTextSoldToPartyAddress);
        existingEditTextSoldToPartyAddress.setEnabled(false);

        // Sold to Party State
        existingTextSoldToPartyState = findViewById(R.id.existingTextSoldToPartyState);
        existingTextSoldToPartyState.setText(Html.fromHtml("Sold to Party State "));
        existingEditTextSoldToPartyState = findViewById(R.id.existingEditTextSoldToPartyState);
        existingEditTextSoldToPartyState.setEnabled(false);

        // Sold to Party Districts
        existingTextSoldToPartyDistricts = findViewById(R.id.existingTextSoldToPartyDistricts);
        existingTextSoldToPartyDistricts.setText(Html.fromHtml("Sold to Party Districts "));
        existingEditTextSoldToPartyDistricts = findViewById(R.id.existingEditTextSoldToPartyDistricts);
        existingEditTextSoldToPartyDistricts.setEnabled(false);

        // Ship to Party Name
        existingTextShipToPartyName = findViewById(R.id.existingTextShipToPartyName);
        existingTextShipToPartyName.setText(Html.fromHtml("Ship to Party Name "));
        existingEditTextShipToPartyName = findViewById(R.id.existingEditTextShipToPartyName);
        existingEditTextShipToPartyName.setEnabled(false);

        // Ship to Party Code
        existingTextShipToPartyCode = findViewById(R.id.existingTextShipToPartyCode);
        existingTextShipToPartyCode.setText(Html.fromHtml("Ship to Party Code"));
        existingEditTextShipToPartyCode = findViewById(R.id.existingEditTextShipToPartyCode);
        existingEditTextShipToPartyCode.setEnabled(false);

        // Ship to Party Address
        existingTextShipToPartyAddress = findViewById(R.id.existingTextShipToPartyAddress);
        existingTextShipToPartyAddress.setText(Html.fromHtml("Ship to Party Address "));
        existingEditTextShipToPartyAddress = findViewById(R.id.existingEditTextShipToPartyAddress);
        existingEditTextShipToPartyAddress.setEnabled(false);

        // Ship to Party State
        existingTextShipToPartyState = findViewById(R.id.existingTextShipToPartyState);
        existingTextShipToPartyState.setText(Html.fromHtml("Ship to Party State "));
        existingEditTextShipToPartyState = findViewById(R.id.existingEditTextShipToPartyState);
        existingEditTextShipToPartyState.setEnabled(false);

        // Ship to Party Districts
        existingTextShipToPartyDistricts = findViewById(R.id.existingTextShipToPartyDistricts);
        existingTextShipToPartyDistricts.setText(Html.fromHtml("Ship to Party Districts "));
        existingEditTextShipToPartyDistricts = findViewById(R.id.existingEditTextShipToPartyDistricts);
        existingEditTextShipToPartyDistricts.setEnabled(false);

        // Segment Button
        existingSegmentButton = findViewById(R.id.existingSegmentButton);
        existingSegmentButton.setText(Html.fromHtml("Segment "));
        existingSegmentButton.setOnClickListener(this);
        existingSegmentButtonSelectText = findViewById(R.id.existingSegmentButtonSelectText);
        existingSegmentButtonSelectText.setVisibility(GONE);

        // Query Source Button
        existingLeadSourceButton = findViewById(R.id.existingLeadSourceButton);
        existingLeadSourceButton.setText(Html.fromHtml("Enquiry Source "));
        existingLeadSourceButton.setOnClickListener(this);
        existingLeadSourceButtonSelectText = findViewById(R.id.existingLeadSourceButtonSelectText);
        existingLeadSourceButtonSelectText.setVisibility(GONE);

        // Product Packaging Button
        existingProductPackagingButton = findViewById(R.id.existingProductPackagingButton);
        existingProductPackagingButton.setText(Html.fromHtml("Product + Packaging"));
        existingProductPackagingButton.setOnClickListener(this);
        existingProductPackagingButtonSelectText = findViewById(R.id.existingProductPackagingButtonSelectText);
        existingProductPackagingButtonSelectText.setVisibility(GONE);

        // Total Potential of Site
        existingTextTotalPotentialOfSite = findViewById(R.id.existingTextTotalPotentialOfSite);
        existingTextTotalPotentialOfSite.setText(Html.fromHtml("Total Potential of Site (MT)"));
        existingEditTextTotalPotentialOfSite = findViewById(R.id.existingEditTextTotalPotentialOfSite);
        existingEditTextTotalPotentialOfSite.setHint(Html.fromHtml("Total Qty Required (MT) "));

        // Quotation Quantity
        existingTextQuotationQuantity = findViewById(R.id.existingTextQuotationQuantity);
        existingTextQuotationQuantity.setText(Html.fromHtml("Quotation Quantity (MT)"));
        existingEditTextQuotationQuantity = findViewById(R.id.existingEditTextQuotationQuantity);
        existingEditTextQuotationQuantity.setHint(Html.fromHtml("Monthly Qty Required (MT) "));

        // Current Brand Used
        existingTextCurrentBrandUsed = findViewById(R.id.existingTextCurrentBrandUsed);
        existingTextCurrentBrandUsed.setText(Html.fromHtml("Current Brand Used "));
        existingEditTextCurrentBrandUsed = findViewById(R.id.existingEditTextCurrentBrandUsed);
        existingEditTextCurrentBrandUsed.setHint(Html.fromHtml("Current Brand Used "));

        // Expected Rate per Bag
        existingTextExpectedRatePerBag = findViewById(R.id.existingTextExpectedRatePerBag);
        existingTextExpectedRatePerBag.setText(Html.fromHtml("Expected Rate Per Bag "));
        existingEditTextExpectedRatePerBag = findViewById(R.id.existingEditTextExpectedRatePerBag);
        existingEditTextExpectedRatePerBag.setHint(Html.fromHtml("Expected Rate Per Bag "));

        // Current Price Star Rs per Bag
        existingTextCurrentPriceStarRsPerBag = findViewById(R.id.existingTextCurrentPriceStarRsPerBag);
        existingTextCurrentPriceStarRsPerBag.setText(Html.fromHtml("Current Price Star Rs. Per Bag "));
        existingEditTextCurrentPriceStarRsPerBag = findViewById(R.id.existingEditTextCurrentPriceStarRsPerBag);
        existingEditTextCurrentPriceStarRsPerBag.setHint(Html.fromHtml("Current Price Star "));

        // Current Price Competitor Rs per Bag
        existingTextCurrentPriceCompetitorRsPerBag = findViewById(R.id.existingTextCurrentPriceCompetitorRsPerBag);
        existingTextCurrentPriceCompetitorRsPerBag.setText(Html.fromHtml("Current Price Competitor Rs. Per Bag "));
        existingEditTextCurrentPriceCompetitorRsPerBag = findViewById(R.id.existingEditTextCurrentPriceCompetitorRsPerBag);
        existingEditTextCurrentPriceCompetitorRsPerBag.setHint(Html.fromHtml("Current Price Competitor "));

        // Contact Person Name
        existingTextContactPersonName = findViewById(R.id.existingTextContactPersonName);
        existingTextContactPersonName.setText(Html.fromHtml("Contact Person Name "));
        existingEditTextContactPersonName = findViewById(R.id.existingEditTextContactPersonName);
        existingEditTextContactPersonName.setHint(Html.fromHtml("Contact Person Name "));

        // Designation
        existingTextDesignation = findViewById(R.id.existingTextDesignation);
        existingTextDesignation.setText(Html.fromHtml("Designation "));
        existingEditTextDesignation = findViewById(R.id.existingEditTextDesignation);
        existingEditTextDesignation.setHint(Html.fromHtml("Designation "));

        // Contact Number
        existingTextContactNumber = findViewById(R.id.existingTextContactNumber);
        existingTextContactNumber.setText(Html.fromHtml("Contact Number "));
        existingEditTextContactNumber = findViewById(R.id.existingEditTextContactNumber);
        existingEditTextContactNumber.setHint(Html.fromHtml("Contact Number "));

        // Mail id
        existingTextMailId = findViewById(R.id.existingTextMailId);
        existingTextMailId.setText(Html.fromHtml("Mail Id "));
        existingEditTextMailId = findViewById(R.id.existingEditTextMailId);
        existingEditTextMailId.setHint(Html.fromHtml("Mail Id "));

        // Mod of Payment Button
        existingModeOfPaymentButton = findViewById(R.id.existingModeOfPaymentButton);
        existingModeOfPaymentButton.setText(Html.fromHtml("Mode Of Payment"));
        existingModeOfPaymentButton.setOnClickListener(this);
        existingModeOfPaymentButtonSelectText = findViewById(R.id.existingModeOfPaymentButtonSelectText);
        existingModeOfPaymentButtonSelectText.setVisibility(GONE);

        // Credit Terms Button
        existingCreditTermsButton = findViewById(R.id.existingCreditTermsButton);
        existingCreditTermsButton.setText(Html.fromHtml("Credit Terms"));
        existingCreditTermsButton.setOnClickListener(this);
        existingCreditTermsButtonSelectText = findViewById(R.id.existingCreditTermsButtonSelectText);
        existingCreditTermsButtonSelectText.setVisibility(GONE);

        // AAC Block Required or Not Button
        existingAacBlockRequiredNotButton = findViewById(R.id.existingAacBlockRequiredNotButton);
        existingAacBlockRequiredNotButton.setText(Html.fromHtml("AAC Block is Required or Not"));
        existingAacBlockRequiredNotButton.setOnClickListener(this);
        existingAacBlockRequiredNotButtonSelectText = findViewById(R.id.existingAacBlockRequiredNotButtonSelectText);
        existingAacBlockRequiredNotButtonSelectText.setVisibility(GONE);

        // Category Type of Construction Button
        existingCategoryTypeOfConstructionButton = findViewById(R.id.existingCategoryTypeOfConstructionButton);
        existingCategoryTypeOfConstructionButton.setText(Html.fromHtml("Category Type of Construction "));
        existingCategoryTypeOfConstructionButton.setOnClickListener(this);
        existingCategoryTypeOfConstructionButtonSelectText = findViewById(R.id.existingCategoryTypeOfConstructionButtonSelectText);
        existingCategoryTypeOfConstructionButtonSelectText.setVisibility(GONE);

        // Query Status Button
        existingLeadStatusButton = findViewById(R.id.existingLeadStatusButton);
        existingLeadStatusButton.setText(Html.fromHtml("Enquiry Status "));
        existingLeadStatusButton.setOnClickListener(this);
        existingLeadStatusButtonSelectText = findViewById(R.id.existingLeadStatusButtonSelectText);
        existingLeadStatusButtonSelectText.setVisibility(GONE);

        // Next Visit Date Button
        existingNextVisitDateButton = findViewById(R.id.existingNextVisitDateButton);
        existingNextVisitDateButton.setText(Html.fromHtml("Next Visit Date "));
        existingNextVisitDateButton.setOnClickListener(this);
        existingNextVisitDateButtonSelectText = findViewById(R.id.existingNextVisitDateButtonSelectText);
        existingNextVisitDateButtonSelectText.setVisibility(GONE);

        // Requirement Type Button
        existingRequirementTypeButton = findViewById(R.id.existingRequirementTypeButton);
        existingRequirementTypeButton.setText(Html.fromHtml("Requirement Type "));
        existingRequirementTypeButton.setOnClickListener(this);
        existingRequirementTypeButtonSelectText = findViewById(R.id.existingRequirementTypeButtonSelectText);
        existingRequirementTypeButtonSelectText.setVisibility(GONE);

        // Ex Works Button
        existingExWorksButton = findViewById(R.id.existingExWorksButton);
        existingExWorksButton.setText(Html.fromHtml("Ex. Works "));
        existingExWorksButton.setOnClickListener(this);
        existingExWorksButtonSelectText = findViewById(R.id.existingExWorksButtonSelectText);
        existingExWorksButtonSelectText.setVisibility(GONE);

        // FOS Siding
        existingTextFosSiding = findViewById(R.id.existingTextFosSiding);
        existingTextFosSiding.setText(Html.fromHtml("FOS Siding"));
        existingEditTextFosSiding = findViewById(R.id.existingEditTextFosSiding);
        existingEditTextFosSiding.setHint(Html.fromHtml("FOS Siding"));

        // Sales Officer Remarks
        existingTextSalesOfficerRemarks = findViewById(R.id.existingTextSalesOfficerRemarks);
        existingTextSalesOfficerRemarks.setText(Html.fromHtml("Sales Officer Remarks"));
        existingEditTextSalesOfficerRemarks = findViewById(R.id.existingEditTextSalesOfficerRemarks);
        existingEditTextSalesOfficerRemarks.setHint(Html.fromHtml("Sales Officer Remarks"));

        // Sales Officer Remarks
        existingTextReferredBy = findViewById(R.id.existingTextReferredBy);
        existingTextReferredBy.setText(Html.fromHtml("Referred by"));
        existingEditTextReferredBy = findViewById(R.id.existingEditTextReferredBy);
        existingEditTextReferredBy.setHint(Html.fromHtml("Referred by"));

        // Assigned To Button
        existingAssignedToButton = findViewById(R.id.existingAssignedToButton);
        existingAssignedToButton.setText(Html.fromHtml("Assigned To "));
        existingAssignedToButton.setOnClickListener(this);
        existingAssignedToButtonSelectText = findViewById(R.id.existingAssignedToButtonSelectText);
        existingAssignedToButtonSelectText.setVisibility(GONE);

        // Requirement Timing Button
        existingRequirementTimingButton = findViewById(R.id.existingRequirementTimingButton);
        existingRequirementTimingButton.setText(Html.fromHtml("Requirement Timing "));
        existingRequirementTimingButton.setOnClickListener(this);
        existingRequirementTimingButtonSelectText = findViewById(R.id.existingRequirementTimingButtonSelectText);
        existingRequirementTimingButtonSelectText.setVisibility(GONE);

        // Share Query Image Button
        existingShareLeadImageButton = findViewById(R.id.existingShareLeadImageButton);
        existingShareLeadImageButton.setText(Html.fromHtml("Share Enquiry / Site Details (Image)"));
        existingShareLeadImageButton.setOnClickListener(this);

        getDefaultData();
    }
    // ======================================== //


    // ==================== Default Data ==================== //
    @SuppressLint("SimpleDateFormat")
    private void getDefaultData() {
        leadId = "L" + Constants.employeeDetailObject.getEmpCode().replaceAll("^[A-Z]", "") + new SimpleDateFormat("yyMMddHHmm").format(new Date());

        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);
        dateString = year + "-" + (month + 1) + "-" + day;

        SimpleDateFormat sdf = new SimpleDateFormat("HH:mm:ss");
        timeString = sdf.format(new Date());

        LocationTracker locationTracker = new LocationTracker(this);
        locationTracker.checkLocationUpdateSharing();
        new Handler().postDelayed(() -> {
            latitude = Constants.currentLat;
            longitude = Constants.currentLong;
            setData();
            setDataForRepeat();
        }, 2000);
    }

    private void setData() {
        editTextUniqueLeadID.setText(leadId);
        editTextDateStamp.setText(dateString);
        editTextTimeStamp.setText(timeString);
        editTextLatitude.setText(latitude);
        editTextLongitude.setText(longitude);
    }

    private void setDataForRepeat() {
        existingEditTextDateStamp.setText(dateString);
        existingEditTextTimeStamp.setText(timeString);
        existingEditTextLatitude.setText(latitude);
        existingEditTextLongitude.setText(longitude);
        existingTextUniqueLeadID.setText(leadId);
    }
    // ======================================== //


    // ==================== Checking Data For Update ==================== //
    // Check for direct query to lead
    private void checkData() {
        soldToPartyName = editTextSoldToPartyName.getText().toString().trim();
        soldToPartyAddress = editTextSoldToPartyAddress.getText().toString().trim();
        shipToPartyName = editTextShipToPartyName.getText().toString().trim();
        shipToPartyAddress = editTextShipToPartyAddress.getText().toString().trim();
        potentialOfSite = editTextTotalPotentialOfSite.getText().toString().trim();
        quotationQuantity = editTextQuotationQuantity.getText().toString().trim();
        currentBrandUsed = editTextCurrentBrandUsed.getText().toString().trim();
        expectedRatePerBag = editTextExpectedRatePerBag.getText().toString().trim();
        currentPriceStarRsPerBag = editTextCurrentPriceStarRsPerBag.getText().toString().trim();
        currentPriceCompetitorRsPerBag = editTextCurrentPriceCompetitorRsPerBag.getText().toString().trim();
        contactPersonName = editTextContactPersonName.getText().toString().trim();
        contactPersonDesignation = editTextDesignation.getText().toString().trim();
        contactPersonNumber = editTextContactNumber.getText().toString().trim();
        contactPersonMail = editTextMailId.getText().toString().trim();
        fosText = editTextFosSiding.getText().toString().trim();
        salesOfficerRemarks = editTextSalesOfficerRemarks.getText().toString().trim();
        referredBy = editTextReferredBy.getText().toString().trim();

        if (officerFor.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select sales officer.", Toast.LENGTH_LONG).show();
        } else if (officerFor.equalsIgnoreCase("OTHER") && selectEmpCode.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select sales officer.", Toast.LENGTH_LONG).show();
        } else if (soldToPartyName.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter sold to party name.", Toast.LENGTH_LONG).show();
        } else if (soldToPartyAddress.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter sold to party address.", Toast.LENGTH_LONG).show();
        } else if (soldToPartyState.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select sold to party state.", Toast.LENGTH_LONG).show();
        } else if (soldToPartyDistricts.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select sold to party districts.", Toast.LENGTH_LONG).show();
        } else if (segment.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select segment.", Toast.LENGTH_LONG).show();
        } else if (leadSource.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select enquiry source.", Toast.LENGTH_LONG).show();
        } else if (currentBrandUsed.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter current brand used.", Toast.LENGTH_LONG).show();
        } else if (expectedRatePerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter expected rate per bag.", Toast.LENGTH_LONG).show();
        } else if (currentPriceStarRsPerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter current price star rs per bag.", Toast.LENGTH_LONG).show();
        } else if (currentPriceCompetitorRsPerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter current price competitor rs per bag.", Toast.LENGTH_LONG).show();
        } else if (contactPersonName.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter contact person name.", Toast.LENGTH_LONG).show();
        } else if (contactPersonDesignation.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter contact person designation.", Toast.LENGTH_LONG).show();
        } else if (contactPersonNumber.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter contact person phone number.", Toast.LENGTH_LONG).show();
        } else if (contactPersonNumber.length() != 10) {
            Toast.makeText(mContext, "Please enter contact person correct phone number.", Toast.LENGTH_LONG).show();
        } else if (contactPersonMail.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter contact person main id.", Toast.LENGTH_LONG).show();
        } else if (constructionType.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select construction type.", Toast.LENGTH_LONG).show();
        } else if (leadStatus.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select enquiry status.", Toast.LENGTH_LONG).show();
        } else if (nextVisitDate.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select next visit date.", Toast.LENGTH_LONG).show();
        } else if (requirementType.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select requirement type.", Toast.LENGTH_LONG).show();
        } else if (requirementType.equalsIgnoreCase("EXW") && exWorks.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select ex works.", Toast.LENGTH_LONG).show();
        } else if (assignedTo.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select assigned to.", Toast.LENGTH_LONG).show();
        } else if (requirementTiming.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select requirements timing.", Toast.LENGTH_LONG).show();
        } else {
            requestForLeadGeneration();
        }
    }

    // Check for existing or repeat query to lead
    private void checkDataForUpdate() {
        soldToPartyName = existingEditTextSoldToPartyName.getText().toString().trim();
        soldToPartyAddress = existingEditTextSoldToPartyAddress.getText().toString().trim();
        shipToPartyName = existingEditTextShipToPartyName.getText().toString().trim();
        shipToPartyAddress = existingEditTextShipToPartyAddress.getText().toString().trim();
        potentialOfSite = existingEditTextTotalPotentialOfSite.getText().toString().trim();
        quotationQuantity = existingEditTextQuotationQuantity.getText().toString().trim();
        currentBrandUsed = existingEditTextCurrentBrandUsed.getText().toString().trim();
        expectedRatePerBag = existingEditTextExpectedRatePerBag.getText().toString().trim();
        currentPriceStarRsPerBag = existingEditTextCurrentPriceStarRsPerBag.getText().toString().trim();
        currentPriceCompetitorRsPerBag = existingEditTextCurrentPriceCompetitorRsPerBag.getText().toString().trim();
        contactPersonName = existingEditTextContactPersonName.getText().toString().trim();
        contactPersonDesignation = existingEditTextDesignation.getText().toString().trim();
        contactPersonNumber = existingEditTextContactNumber.getText().toString().trim();
        contactPersonMail = existingEditTextMailId.getText().toString().trim();
        fosText = existingEditTextFosSiding.getText().toString().trim();
        salesOfficerRemarks = existingEditTextSalesOfficerRemarks.getText().toString().trim();
        referredBy = existingEditTextReferredBy.getText().toString().trim();
        soldToPartyState = existingEditTextSoldToPartyState.getText().toString().trim();
        soldToPartyDistricts = existingEditTextSoldToPartyDistricts.getText().toString().trim();

        if (soldToPartyName.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter sold to party name.", Toast.LENGTH_LONG).show();
        } else if (soldToPartyAddress.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter sold to party address.", Toast.LENGTH_LONG).show();
        } else if (soldToPartyState.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select sold to party state.", Toast.LENGTH_LONG).show();
        } else if (soldToPartyDistricts.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select sold to party districts.", Toast.LENGTH_LONG).show();
        } else if (segment.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select segment.", Toast.LENGTH_LONG).show();
        } else if (leadSource.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select enquiry source.", Toast.LENGTH_LONG).show();
        } else if (currentBrandUsed.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter current brand used.", Toast.LENGTH_LONG).show();
        } else if (expectedRatePerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter expected rate per bag.", Toast.LENGTH_LONG).show();
        } else if (currentPriceStarRsPerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter current price star rs per bag.", Toast.LENGTH_LONG).show();
        } else if (currentPriceCompetitorRsPerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter current price competitor rs per bag.", Toast.LENGTH_LONG).show();
        } else if (contactPersonName.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter contact person name.", Toast.LENGTH_LONG).show();
        } else if (contactPersonDesignation.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter contact person designation.", Toast.LENGTH_LONG).show();
        } else if (contactPersonNumber.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter contact person phone number.", Toast.LENGTH_LONG).show();
        } else if (contactPersonNumber.length() != 10) {
            Toast.makeText(mContext, "Please enter contact person correct phone number.", Toast.LENGTH_LONG).show();
        } else if (contactPersonMail.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter contact person main id.", Toast.LENGTH_LONG).show();
        } else if (constructionType.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select construction type.", Toast.LENGTH_LONG).show();
        } else if (leadStatus.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select enquiry status.", Toast.LENGTH_LONG).show();
        } else if (nextVisitDate.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select next visit date.", Toast.LENGTH_LONG).show();
        } else if (requirementType.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select requirement type.", Toast.LENGTH_LONG).show();
        } else if (requirementType.equalsIgnoreCase("EXW") && exWorks.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select ex works.", Toast.LENGTH_LONG).show();
        } else if (assignedTo.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select assigned to.", Toast.LENGTH_LONG).show();
        } else if (requirementTiming.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select requirements timing.", Toast.LENGTH_LONG).show();
        } else {
            requestForLeadGeneration();
        }
    }
    // ======================================== //


    // ==================== Main Request for this page ==================== //

    private String convertDate(String nextVisitDate) {
        if (nextVisitDate == null || nextVisitDate.trim().isEmpty()) {
            return "";
        }

        String[] possibleFormats = {
                "d/M/yyyy",
                "dd/MM/yyyy",
                "yyyy-MM-dd",
                "dd-MM-yyyy",
                "MM/dd/yyyy",
                "d-M-yyyy",
                "yyyy/MM/dd"
        };

        SimpleDateFormat outputFormat = new SimpleDateFormat("dd-MM-yyyy", Locale.getDefault());

        for (String format : possibleFormats) {
            try {
                SimpleDateFormat inputFormat = new SimpleDateFormat(format, Locale.getDefault());
                inputFormat.setLenient(false); // avoids wrong matches like 13/25/2026 silently parsing
                Date date = inputFormat.parse(nextVisitDate.trim());
                return outputFormat.format(date);
            } catch (ParseException ignored) {
                // try next format
            }
        }

        return ""; // none of the formats matched
    }

    // Request For New Query Generation
    private void requestForQueryGeneration(String status1) {
        progressDialogOpen("Uploading data ...");
        new Thread(() -> {
            try {
                //------------------------------
                // Build survey_location object
                JSONObject surveyLocation = new JSONObject();
                surveyLocation.put("emp_code", selectEmpCode);
                surveyLocation.put("trans_id", leadId);
                surveyLocation.put("latt", latitude);
                surveyLocation.put("longi", longitude);
                surveyLocation.put("date", dateString + " " + timeString);
                //------------------------------


                //------------------------------
                // Build survey_header object
                JSONObject surveyHeader = new JSONObject();
                surveyHeader.put("survey_type", "Lead Generation");
                surveyHeader.put("menu_name", "RA514");
                surveyHeader.put("mall_id", "");
                surveyHeader.put("mall_hs_name", "");
                surveyHeader.put("business_name", "");
                surveyHeader.put("contact_name", "");
                surveyHeader.put("phone_no", "");
                surveyHeader.put("questions_answered", "");
                surveyHeader.put("route_code", "");
                surveyHeader.put("check_in_time", "");
                surveyHeader.put("survey_id", leadId);
                //------------------------------


                //------------------------------
                // Build survey_output object
                JSONObject surveyOutput = new JSONObject();
                surveyOutput.put("lead_generation_id", leadId);
                surveyOutput.put("self_other", officerFor);
                surveyOutput.put("emp_code", selectEmpCode);
                surveyOutput.put("date", dateString);
                surveyOutput.put("time", timeString);
                surveyOutput.put("latitude", latitude);
                surveyOutput.put("longitude", longitude);
                surveyOutput.put("type_lead", segment);
                surveyOutput.put("lead_type", leadSource);
                surveyOutput.put("product_packaging", productPackaging.toUpperCase());
                surveyOutput.put("mode", modeOfPayment);
                surveyOutput.put("credit_terms", creditTerms);
                surveyOutput.put("acc_block_is_required", aacBlockRequiredStatus);
                surveyOutput.put("category_type_construction", constructionType);
                if(leadStatus.isEmpty()||leadStatus.isBlank()){
                    surveyOutput.put("lead_status", "COLD");
                }else{
                    surveyOutput.put("lead_status", leadStatus.toUpperCase());
                }
                surveyOutput.put("next_visit_date", convertDate(nextVisitDate));
                surveyOutput.put("assigned_to", assignedTo);
                surveyOutput.put("r_timing", requirementTiming);
                surveyOutput.put("sold_to_party", soldToPartyCode);
                surveyOutput.put("ship_to_party", shipToPartyCode);
                surveyOutput.put("payment", modeOfPayment);
                surveyOutput.put("party_name", soldToPartyName);
                surveyOutput.put("branch", soldToPartyAddress);
                surveyOutput.put("district", soldToPartyDistricts.toUpperCase());
                surveyOutput.put("state", soldToPartyState.toUpperCase());
                surveyOutput.put("incoterms", requirementType);
                surveyOutput.put("lead_action", status1.toUpperCase());
                if (requirementType.equalsIgnoreCase("exw")) {
                    surveyOutput.put("serving_location", exWorks);
                } else if (requirementType.equalsIgnoreCase("fos")) {
                    surveyOutput.put("serving_location", fosText);
                } else {
                    surveyOutput.put("serving_location", "");
                }
                surveyOutput.put("referred_by", referredBy);

                if(TYPE_OF_LEAD.equalsIgnoreCase("new")){
                    surveyOutput.put("current_brand_used", editTextCurrentBrandUsed.getText().toString().trim());
                    surveyOutput.put("customer_reference_no",editTextReferredBy.getText().toString().trim());
                    surveyOutput.put("month_qty", editTextQuotationQuantity.getText().toString().trim());
                    surveyOutput.put("current_price", editTextCurrentPriceStarRsPerBag.getText().toString().trim());
                    surveyOutput.put("current_price_competitor", editTextCurrentPriceCompetitorRsPerBag.getText().toString().trim());
                    surveyOutput.put("qty_req", editTextTotalPotentialOfSite.getText().toString().trim());
                    surveyOutput.put("exp_rate_per_bag", editTextExpectedRatePerBag.getText().toString().trim());
                    surveyOutput.put("lead_remarks", editTextSalesOfficerRemarks.getText().toString().trim());
                    surveyOutput.put("contact_person_name", editTextContactPersonName.getText().toString().trim());
                    surveyOutput.put("designation", editTextDesignation.getText().toString().trim());
                    surveyOutput.put("contact_number", editTextContactNumber.getText().toString().trim());
                    surveyOutput.put("mail_id", editTextMailId.getText().toString().trim());
                }else{
                    surveyOutput.put("current_brand_used", existingEditTextCurrentBrandUsed.getText().toString().trim());
                    surveyOutput.put("customer_reference_no",existingEditTextReferredBy.getText().toString().trim());
                    surveyOutput.put("month_qty", existingEditTextQuotationQuantity.getText().toString().trim());
                    surveyOutput.put("current_price", existingEditTextCurrentPriceStarRsPerBag.getText().toString().trim());
                    surveyOutput.put("current_price_competitor", existingEditTextCurrentPriceCompetitorRsPerBag.getText().toString().trim());
                    surveyOutput.put("qty_req", existingEditTextTotalPotentialOfSite.getText().toString().trim());
                    surveyOutput.put("exp_rate_per_bag", existingEditTextExpectedRatePerBag.getText().toString().trim());
                    surveyOutput.put("lead_remarks", existingEditTextSalesOfficerRemarks.getText().toString().trim());
                    surveyOutput.put("contact_person_name", existingEditTextContactPersonName.getText().toString().trim());
                    surveyOutput.put("designation", existingEditTextDesignation.getText().toString().trim());
                    surveyOutput.put("contact_number", existingEditTextContactNumber.getText().toString().trim());
                    surveyOutput.put("mail_id", existingEditTextMailId.getText().toString().trim());
                }
                //------------------------------


                //------------------------------
                // Build sold_to_party object
                JSONObject soldToParty = new JSONObject();
                soldToParty.put("sold_to_party", soldToPartyCode);
                soldToParty.put("sold_to_party_name", soldToPartyName);
                soldToParty.put("sold_to_party_address", soldToPartyAddress);
                soldToParty.put("sold_to_party_state", soldToPartyState.toUpperCase());
                soldToParty.put("sold_to_party_districts", soldToPartyDistricts.toUpperCase());
                //------------------------------


                //------------------------------
                // Build ship_to_party object
                JSONObject shipToParty = new JSONObject();
                shipToParty.put("ship_to_party", shipToPartyCode);
                shipToParty.put("ship_to_party_name", shipToPartyName);
                shipToParty.put("ship_to_party_address", shipToPartyAddress);
                shipToParty.put("ship_to_party_state", shipToPartyState.toUpperCase());
                shipToParty.put("ship_to_party_districts", shipToPartyDistricts.toUpperCase());
                //------------------------------


                //------------------------------
                // Build quantity_data object
                JSONObject quantityData = new JSONObject();
                quantityData.put("qty_req", potentialOfSite);
                quantityData.put("current_price", currentPriceStarRsPerBag);
                quantityData.put("current_price_competitor", currentPriceCompetitorRsPerBag);
                quantityData.put("month_qty", quotationQuantity);
                quantityData.put("current_brand_used", currentBrandUsed);
                quantityData.put("exp_rate_per_bag", expectedRatePerBag);
                //------------------------------


                //------------------------------
                // Build contact_person object
                JSONObject contactPerson = new JSONObject();
                contactPerson.put("contact_person_name", contactPersonName);
                contactPerson.put("designation", contactPersonDesignation);
                contactPerson.put("contact_number", contactPersonNumber);
                contactPerson.put("mail_id", contactPersonMail);
                //------------------------------


                //------------------------------
                // Build other_info object
                JSONObject otherInfo = new JSONObject();
                otherInfo.put("mode", modeOfPayment);
                otherInfo.put("credit_terms", creditTerms);
                otherInfo.put("acc_block_is_required", aacBlockRequiredStatus);
                otherInfo.put("category_type_construction", constructionType);
                otherInfo.put("lead_status", leadStatus);
                otherInfo.put("next_visit_date", convertDate(nextVisitDate));
                otherInfo.put("incoterms", requirementType);
                if (requirementType.equalsIgnoreCase("exw")) {
                    otherInfo.put("serving_location", exWorks);
                } else if (requirementType.equalsIgnoreCase("fos")) {
                    otherInfo.put("serving_location", fosText);
                } else {
                    otherInfo.put("serving_location", "");
                }
                otherInfo.put("lead_remarks", salesOfficerRemarks);
                otherInfo.put("assigned_to", assignedTo);
                otherInfo.put("r_timing", requirementTiming);
                //------------------------------


                // -----------------------------
                // Final combined JSON
                JSONObject mainObject = new JSONObject();
                mainObject.put("survey_location", surveyLocation);
                mainObject.put("survey_header", surveyHeader);
                mainObject.put("survey_output", surveyOutput);
                mainObject.put("survey_sold_to_party", soldToParty);
                mainObject.put("survey_ship_to_party", shipToParty);
                mainObject.put("survey_quantity_data", quantityData);
                mainObject.put("survey_contact_person", contactPerson);
                mainObject.put("survey_other_info", otherInfo);
                //------------------------------

                Log.d("TAG", "requestForLeadGeneration: " + mainObject.toString());

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadquerymaster/")
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response execute = client.newCall(request).execute();

                progressDialogClose();
                if(TYPE_OF_LEAD.equalsIgnoreCase("new")){
                    Log.d("TAG", "requestForLeadGeneration: 4");
                    if(status1.equalsIgnoreCase("yes")){
                        ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
                            ((QueryGenerationActivity) mContext).successMessageAndGotoPreviousPageForNewLeadGeneration1();
                        });
                    }else{
                        ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
                            ((QueryGenerationActivity) mContext).successMessageAndGotoPreviousPageForNewLeadGeneration();
                        });
                    }
                }else if(TYPE_OF_LEAD.equalsIgnoreCase("existing")) {
                    Log.d("TAG", "requestForLeadGeneration: 5");
                    ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
                        ((QueryGenerationActivity) mContext).successMessageAndCleanFieldForExistingLeadGeneration();
                    });
                }else{
                    Log.d("TAG", "requestForLeadGeneration: 6");
                    ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
                        ((QueryGenerationActivity) mContext).successMessageAndGotoPreviousPageForNewLeadGeneration1();
                    });
                }
            } catch (Exception ignored) {
            }
        }).start();
    }

    // Request For Query Update
    private void requestForUpdaterQueryGeneration(String lead_action) {
        progressDialogOpen("Uploading data ...");
        new Thread(() -> {
            try {
                JSONObject mainObject = new JSONObject();

                mainObject.put("type_lead", segment);
                mainObject.put("lead_type", leadSource);
                mainObject.put("product_packaging", productPackaging.toUpperCase());
                mainObject.put("qty_req", existingEditTextTotalPotentialOfSite.getText().toString().trim());
                mainObject.put("month_qty", existingEditTextQuotationQuantity.getText().toString().trim());
                mainObject.put("current_brand_used", existingEditTextCurrentBrandUsed.getText().toString().trim());
                mainObject.put("exp_rate_per_bag", existingEditTextExpectedRatePerBag.getText().toString().trim());
                mainObject.put("current_price", existingEditTextCurrentPriceStarRsPerBag.getText().toString().trim());
                mainObject.put("current_price_competitor", existingEditTextCurrentPriceCompetitorRsPerBag.getText().toString().trim());
                mainObject.put("contact_person_name", existingEditTextContactPersonName.getText().toString().trim());
                mainObject.put("designation", existingEditTextDesignation.getText().toString().trim());
                mainObject.put("contact_number", existingEditTextContactNumber.getText().toString().trim());
                mainObject.put("mail_id", existingEditTextMailId.getText().toString().trim());
                mainObject.put("mode", modeOfPayment);
                mainObject.put("payment", modeOfPayment);
                mainObject.put("credit_terms", creditTerms);
                mainObject.put("customer_reference_no", existingEditTextReferredBy.getText().toString().trim());
                mainObject.put("acc_block_is_required", aacBlockRequiredStatus);
                mainObject.put("category_type_construction", constructionType);
                mainObject.put("lead_status", leadStatus.toUpperCase());
                mainObject.put("next_visit_date", convertDate(nextVisitDate));
                mainObject.put("incoterms", requirementType);
                if (requirementType.equalsIgnoreCase("exw")) {
                    mainObject.put("serving_location", exWorks);
                } else if (requirementType.equalsIgnoreCase("fos")) {
                    mainObject.put("serving_location", fosText);
                } else {
                    mainObject.put("serving_location", "");
                }
                mainObject.put("lead_remarks", existingEditTextSalesOfficerRemarks.getText().toString().trim());
                mainObject.put("assigned_to", assignedTo);
                mainObject.put("r_timing", requirementTiming);
                mainObject.put("lead_action", lead_action.toUpperCase());
                mainObject.put("referred_by", referredBy);


//                surveyOutput.put("sold_to_party", soldToPartyCode);
//                surveyOutput.put("ship_to_party", shipToPartyCode);
//                surveyOutput.put("party_name", soldToPartyName);
//                surveyOutput.put("branch", soldToPartyAddress);
//                surveyOutput.put("district", soldToPartyDistricts.toUpperCase());
//                surveyOutput.put("state", soldToPartyState.toUpperCase());

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());
                Log.d("TAG", "requestForLeadGeneration: "+mainObject.toString());
                Log.d("TAG", "requestForLeadGeneration: "+BaseUrl.sbDevUrl + "api/leadquerymaster/" + existingTextUniqueLeadID.getText().toString().trim() + "/");

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadquerymaster/" + existingTextUniqueLeadID.getText().toString().trim() + "/")
                        .put(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();
                progressDialogClose();
                Log.d("TAG", "requestForLeadGeneration: "+response.body());
                ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
                    ((QueryGenerationActivity) mContext).successMessageAndCleanFieldForExistingLeadGeneration();
                });

            } catch (Exception ignored) {
            }
        }).start();
    }

    // Request For New Lead Generation
    private void requestForLeadGeneration() {
        progressDialogOpen("Uploading data ...");
        new Thread(() -> {
            try {
                //------------------------------
                // Build survey_location object
                JSONObject surveyLocation = new JSONObject();
                surveyLocation.put("emp_code", selectEmpCode);
                surveyLocation.put("trans_id", leadId);
                surveyLocation.put("latt", latitude);
                surveyLocation.put("longi", longitude);
                surveyLocation.put("date", dateString + " " + timeString);
                //------------------------------


                //------------------------------
                // Build survey_header object
                JSONObject surveyHeader = new JSONObject();
                surveyHeader.put("survey_type", "Lead Generation");
                surveyHeader.put("menu_name", "RA514");
                surveyHeader.put("mall_id", "");
                surveyHeader.put("mall_hs_name", "");
                surveyHeader.put("business_name", "");
                surveyHeader.put("contact_name", "");
                surveyHeader.put("phone_no", "");
                surveyHeader.put("questions_answered", "");
                surveyHeader.put("route_code", "");
                surveyHeader.put("check_in_time", "");
                surveyHeader.put("survey_id", leadId);
                //------------------------------


                //------------------------------
                // Build survey_output object
                JSONObject surveyOutput = new JSONObject();
                surveyOutput.put("lead_generation_id", leadId);
                surveyOutput.put("self_other", officerFor);
                surveyOutput.put("emp_code", selectEmpCode);
                surveyOutput.put("date", dateString);
                surveyOutput.put("time", timeString);
                surveyOutput.put("latitude", latitude);
                surveyOutput.put("longitude", longitude);
                surveyOutput.put("type_lead", segment);
                surveyOutput.put("lead_type", leadSource);
                surveyOutput.put("product_packaging", productPackaging.toUpperCase());
                surveyOutput.put("current_brand_used", currentBrandUsed);
                surveyOutput.put("contact_person_name", contactPersonName);
                surveyOutput.put("designation", contactPersonDesignation);
                surveyOutput.put("contact_number", contactPersonNumber);
                surveyOutput.put("mail_id", contactPersonMail);
                surveyOutput.put("mode", modeOfPayment);
                surveyOutput.put("credit_terms", creditTerms);
                surveyOutput.put("acc_block_is_required", aacBlockRequiredStatus);
                surveyOutput.put("category_type_construction", constructionType);
                surveyOutput.put("lead_status", leadStatus.toUpperCase());
                surveyOutput.put("next_visit_date", convertDate(nextVisitDate));
                surveyOutput.put("assigned_to", assignedTo);
                surveyOutput.put("r_timing", requirementTiming);
                surveyOutput.put("sold_to_party", soldToPartyCode);
                surveyOutput.put("ship_to_party", shipToPartyCode);
                surveyOutput.put("payment", modeOfPayment);
                surveyOutput.put("party_name", soldToPartyName);
                surveyOutput.put("branch", soldToPartyAddress);
                surveyOutput.put("district", soldToPartyDistricts.toUpperCase());
                surveyOutput.put("state", soldToPartyState.toUpperCase());
                surveyOutput.put("incoterms", requirementType);
                if (requirementType.equalsIgnoreCase("exw")) {
                    surveyOutput.put("serving_location", exWorks);
                } else if (requirementType.equalsIgnoreCase("fos")) {
                    surveyOutput.put("serving_location", fosText);
                } else {
                    surveyOutput.put("serving_location", "");
                }

                if(TYPE_OF_LEAD.equalsIgnoreCase("new")){
                    surveyOutput.put("customer_reference_no",editTextReferredBy.getText().toString().trim());
                    surveyOutput.put("month_qty", editTextQuotationQuantity.getText().toString().trim());
                    surveyOutput.put("current_price", editTextCurrentPriceStarRsPerBag.getText().toString().trim());
                    surveyOutput.put("current_price_competitor", editTextCurrentPriceCompetitorRsPerBag.getText().toString().trim());
                    surveyOutput.put("qty_req", editTextTotalPotentialOfSite.getText().toString().trim());
                    surveyOutput.put("exp_rate_per_bag", editTextExpectedRatePerBag.getText().toString().trim());
                    surveyOutput.put("lead_remarks", editTextSalesOfficerRemarks.getText().toString().trim());
                }else{
                    surveyOutput.put("customer_reference_no",existingEditTextReferredBy.getText().toString().trim());
                    surveyOutput.put("month_qty", existingEditTextQuotationQuantity.getText().toString().trim());
                    surveyOutput.put("current_price", existingEditTextCurrentPriceStarRsPerBag.getText().toString().trim());
                    surveyOutput.put("current_price_competitor", existingEditTextCurrentPriceCompetitorRsPerBag.getText().toString().trim());
                    surveyOutput.put("qty_req", existingEditTextTotalPotentialOfSite.getText().toString().trim());
                    surveyOutput.put("exp_rate_per_bag", existingEditTextExpectedRatePerBag.getText().toString().trim());
                    surveyOutput.put("lead_remarks", existingEditTextSalesOfficerRemarks.getText().toString().trim());
                }


                //------------------------------


                //------------------------------
                // Build sold_to_party object
                JSONObject soldToParty = new JSONObject();
                soldToParty.put("sold_to_party", soldToPartyCode);
                soldToParty.put("sold_to_party_name", soldToPartyName);
                soldToParty.put("sold_to_party_address", soldToPartyAddress);
                soldToParty.put("sold_to_party_state", soldToPartyState.toUpperCase());
                soldToParty.put("sold_to_party_districts", soldToPartyDistricts.toUpperCase());
                //------------------------------


                //------------------------------
                // Build ship_to_party object
                JSONObject shipToParty = new JSONObject();
                shipToParty.put("ship_to_party", shipToPartyCode);
                shipToParty.put("ship_to_party_name", shipToPartyName);
                shipToParty.put("ship_to_party_address", shipToPartyAddress);
                shipToParty.put("ship_to_party_state", shipToPartyState.toUpperCase());
                shipToParty.put("ship_to_party_districts", shipToPartyDistricts.toUpperCase());
                //------------------------------


                //------------------------------
                // Build quantity_data object
                JSONObject quantityData = new JSONObject();
                quantityData.put("qty_req", potentialOfSite);
                quantityData.put("current_price", currentPriceStarRsPerBag);
                quantityData.put("current_price_competitor", currentPriceCompetitorRsPerBag);
                quantityData.put("month_qty", quotationQuantity);
                quantityData.put("current_brand_used", currentBrandUsed);
                quantityData.put("exp_rate_per_bag", expectedRatePerBag);
                //------------------------------


                //------------------------------
                // Build contact_person object
                JSONObject contactPerson = new JSONObject();
                contactPerson.put("contact_person_name", contactPersonName);
                contactPerson.put("designation", contactPersonDesignation);
                contactPerson.put("contact_number", contactPersonNumber);
                contactPerson.put("mail_id", contactPersonMail);
                //------------------------------


                //------------------------------
                // Build other_info object
                JSONObject otherInfo = new JSONObject();
                otherInfo.put("mode", modeOfPayment);
                otherInfo.put("credit_terms", creditTerms);
                otherInfo.put("acc_block_is_required", aacBlockRequiredStatus);
                otherInfo.put("category_type_construction", constructionType);
                otherInfo.put("lead_status", leadStatus);
                otherInfo.put("next_visit_date", convertDate(nextVisitDate));
                otherInfo.put("incoterms", requirementType);
                if (requirementType.equalsIgnoreCase("exw")) {
                    otherInfo.put("serving_location", exWorks);
                } else if (requirementType.equalsIgnoreCase("fos")) {
                    otherInfo.put("serving_location", fosText);
                } else {
                    otherInfo.put("serving_location", "");
                }
                otherInfo.put("lead_remarks", salesOfficerRemarks);
                otherInfo.put("assigned_to", assignedTo);
                otherInfo.put("r_timing", requirementTiming);
                //------------------------------


                // -----------------------------
                // Final combined JSON
                JSONObject mainObject = new JSONObject();
                mainObject.put("survey_location", surveyLocation);
                mainObject.put("survey_header", surveyHeader);
                mainObject.put("survey_output", surveyOutput);
                mainObject.put("survey_sold_to_party", soldToParty);
                mainObject.put("survey_ship_to_party", shipToParty);
                mainObject.put("survey_quantity_data", quantityData);
                mainObject.put("survey_contact_person", contactPerson);
                mainObject.put("survey_other_info", otherInfo);
                //------------------------------

                Log.d("TAG", "requestForLeadGeneration: " + mainObject.toString());

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadmaster/")
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response execute = client.newCall(request).execute();
                progressDialogClose();
                if(TYPE_OF_LEAD.equalsIgnoreCase("existing")){
                    Log.d("TAG", "requestForLeadGeneration: 1");
                    ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
                        ((QueryGenerationActivity) mContext).requestForUpdaterQueryGeneration("YES");
                    });
                }else{
                    Log.d("TAG", "requestForLeadGeneration: 2");
                    ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
                        ((QueryGenerationActivity) mContext).requestForQueryGeneration("YES");
                    });
                }
            } catch (Exception ignored) {
            }
        }).start();
    }
    // ======================================== //


    // ==================== After Submit Successfully ==================== //
    // Success Message And Goto Previous Page For New Query Generation
    private void successMessageAndGotoPreviousPageForNewLeadGeneration() {
//        progressDialogClose();
        Toast.makeText(mContext, "New enquiry add successfully", Toast.LENGTH_LONG).show();
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }

    // Success Message And Clean Field For Existing Query Generation
    private void successMessageAndCleanFieldForExistingLeadGeneration() {
//        progressDialogClose();
        Toast.makeText(mContext, "Update enquiry successfully", Toast.LENGTH_LONG).show();
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }

    // Success Message And Goto Previous Page For New Query Generation
    private void successMessageAndGotoPreviousPageForNewLeadGeneration1() {
//        progressDialogClose();
        Toast.makeText(mContext, "New lead add successfully", Toast.LENGTH_LONG).show();
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }
    // ======================================== //


    // ==================== Dialog Show ==================== //
    // Employee Data Set List Show Dialog Popup
    public void show_sale_officer_list_dialog(ArrayList<EmployeeDataSet> dataSet, String titleValue, String value) {
        try {
            final ShowEmployeeDataSetAdapter adapterCust = new ShowEmployeeDataSetAdapter(mContext, R.layout.list_item_single_radio, dataSet);

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);
            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapterCust.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapterCust);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                if (value.equalsIgnoreCase("sales_officer_name")) {
                    selectEmpCode = Objects.requireNonNull(adapterCust.getItem(position)).getEmp_code();
                    editTextSalesOfficerName.setText(Objects.requireNonNull(adapterCust.getItem(position)).getEmp_name());
                    Objects.requireNonNull(adapterCust.getItem(position)).setSelect(true);
                    _DOWNLOAD_sold_to_party(Objects.requireNonNull(adapterCust.getItem(position)).getEmp_code());
//                    dataSet(Objects.requireNonNull(adapterCust.getItem(position)).getEmp_code());
                } else {
                    assignedTo = Objects.requireNonNull(adapterCust.getItem(position)).getEmp_code();
                    assignedToButtonSelectText.setVisibility(VISIBLE);
                    assignedToButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getEmp_name());
                    existingAssignedToButtonSelectText.setVisibility(VISIBLE);
                    existingAssignedToButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getEmp_name());
                }
            });

            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(GONE);
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    // Party Data Set List Show Dialog Popup
    public void show_party_name_list_dialog(ArrayList<PartyDataList> dataSet, String titleValue, String value) {
        try {
            final ShowPartyDataSetAdapter adapterCust = new ShowPartyDataSetAdapter(mContext, R.layout.list_item_single_radio, dataSet);

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);
            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapterCust.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapterCust);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                if (value.equalsIgnoreCase("sold_to_party_name")) {
                    soldToPartyName = Objects.requireNonNull(adapterCust.getItem(position)).getName();
                    soldToPartyAddress = Objects.requireNonNull(adapterCust.getItem(position)).getAddress();
                    soldToPartyCode = Objects.requireNonNull(adapterCust.getItem(position)).getCustomerCode();
                    soldToPartyState = Objects.requireNonNull(adapterCust.getItem(position)).getState();
                    soldToPartyDistricts = Objects.requireNonNull(adapterCust.getItem(position)).getDistrict();

                    editTextSoldToPartyName.setText(Objects.requireNonNull(adapterCust.getItem(position)).getName());
                    editTextSoldToPartyCode.setText(Objects.requireNonNull(adapterCust.getItem(position)).getCustomerCode());
                    editTextSoldToPartyAddress.setText(Objects.requireNonNull(adapterCust.getItem(position)).getAddress());

                    editTextSoldToPartyName.setEnabled(false);
                    editTextSoldToPartyCode.setEnabled(false);
                    editTextSoldToPartyAddress.setEnabled(false);

                    soldToPartyStateButtonSelectText.setVisibility(VISIBLE);
                    soldToPartyStateButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getState());

                    soldToPartyDistrictsButtonSelectText.setVisibility(VISIBLE);
                    soldToPartyDistrictsButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getDistrict());
                } else {
                    shipToPartyName = Objects.requireNonNull(adapterCust.getItem(position)).getName();
                    shipToPartyAddress = Objects.requireNonNull(adapterCust.getItem(position)).getAddress();
                    shipToPartyCode = Objects.requireNonNull(adapterCust.getItem(position)).getCustomerCode();
                    shipToPartyState = Objects.requireNonNull(adapterCust.getItem(position)).getState();
                    shipToPartyDistricts = Objects.requireNonNull(adapterCust.getItem(position)).getDistrict();

                    editTextShipToPartyName.setText(Objects.requireNonNull(adapterCust.getItem(position)).getName());
                    editTextShipToPartyCode.setText(Objects.requireNonNull(adapterCust.getItem(position)).getCustomerCode());
                    editTextShipToPartyAddress.setText(Objects.requireNonNull(adapterCust.getItem(position)).getAddress());

                    editTextShipToPartyName.setEnabled(false);
                    editTextShipToPartyCode.setEnabled(false);
                    editTextShipToPartyAddress.setEnabled(false);

                    shipToPartyStateButtonSelectText.setVisibility(VISIBLE);
                    shipToPartyStateButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getState());

                    shipToPartyDistrictsButtonSelectText.setVisibility(VISIBLE);
                    shipToPartyDistrictsButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getDistrict());
                }
            });

            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(GONE);
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    // State Data Set List Show Dialog Popup
    @SuppressLint("SetTextI18n")
    public void show_state_list_dialog(ArrayList<StateDataSet> dataSet, String value) {
        try {
            final Dialog mDestinationDialog = new Dialog(this, R.style.MyMaterialTheme);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_from_list1);
            mDestinationDialog.setCancelable(true);

            TextView title = mDestinationDialog.findViewById(R.id.title);
            title.setText("Please select");
            ListView dialogList = mDestinationDialog.findViewById(R.id.list);
            Button btn_cncl = mDestinationDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setVisibility(GONE);

            ImageView imageView1 = mDestinationDialog.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDestinationDialog.dismiss());
            final ShowStateDataSetAdapter pAdapter = new ShowStateDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                if (value.equalsIgnoreCase("sold_party_state_name")) {
                    soldToPartyStateButtonSelectText.setVisibility(VISIBLE);
                    soldToPartyStateButtonSelectText.setText(dataSet.get(position).getTitle());
                    soldToPartyState = dataSet.get(position).getValue();
                } else {
                    shipToPartyStateButtonSelectText.setVisibility(VISIBLE);
                    shipToPartyStateButtonSelectText.setText(dataSet.get(position).getTitle());
                    shipToPartyState = dataSet.get(position).getValue();
                }
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
    }

    // Filter District against the state name
    public ArrayList<DistrictDataSet> getDistrictDataSet(String value) {
        ArrayList<DistrictDataSet> data = new ArrayList<>();
        try {
            String searchKey;
            if (value.equalsIgnoreCase("sold_party_district_name")) {
                searchKey = soldToPartyState;
            } else {
                searchKey = shipToPartyState;
            }
            for (int i = 0; i < districtList.size(); i++) {
                if (districtList.get(i).getStateCode().equalsIgnoreCase(searchKey)) {
                    data.add(districtList.get(i));
                }
            }
        } catch (Exception ignored) {
        }
        return data;
    }

    // District Data Set List Show Dialog Popup
    @SuppressLint("SetTextI18n")
    public void show_district_list_dialog(String value) {
        try {
            ArrayList<DistrictDataSet> dataSet = getDistrictDataSet(value);
            final Dialog mDestinationDialog = new Dialog(this, R.style.MyMaterialTheme);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_from_list1);
            mDestinationDialog.setCancelable(true);

            TextView title = mDestinationDialog.findViewById(R.id.title);
            title.setText("Please select");
            ListView dialogList = mDestinationDialog.findViewById(R.id.list);
            Button btn_cncl = mDestinationDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setVisibility(GONE);
            ImageView imageView1 = mDestinationDialog.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDestinationDialog.dismiss());

            final ShowDistrictDataSetAdapter pAdapter = new ShowDistrictDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                if (value.equalsIgnoreCase("sold_party_district_name")) {
                    soldToPartyDistrictsButtonSelectText.setVisibility(VISIBLE);
                    soldToPartyDistrictsButtonSelectText.setText(dataSet.get(position).getValue());
                } else {
                    shipToPartyDistrictsButtonSelectText.setVisibility(VISIBLE);
                    shipToPartyDistrictsButtonSelectText.setText(dataSet.get(position).getValue());
                }
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
    }

    // Data Set List Show Dialog Popup
    @SuppressLint("SetTextI18n")
    public void show_list_data_dialog(ArrayList<DataSet> dataSet, String value) {
        try {
            final Dialog mDestinationDialog = new Dialog(this, R.style.MyMaterialTheme);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_from_list1);
            mDestinationDialog.setCancelable(true);

            TextView title = mDestinationDialog.findViewById(R.id.title);
            title.setText("Please select");
            ImageView imageView1 = mDestinationDialog.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDestinationDialog.dismiss());
            ListView dialogList = mDestinationDialog.findViewById(R.id.list);
            Button btn_cncl = mDestinationDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setVisibility(GONE);

            final ShowDataSetAdapter pAdapter = new ShowDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                switch (value) {
                    case "segment":
                        segmentButtonSelectText.setVisibility(VISIBLE);
                        segmentButtonSelectText.setText(dataSet.get(position).getValue());
                        segment = dataSet.get(position).getId();
                        break;
                    case "lead_source":
                        leadSourceButtonSelectText.setVisibility(VISIBLE);
                        leadSourceButtonSelectText.setText(dataSet.get(position).getValue());
                        leadSource = dataSet.get(position).getId();
                        break;
                    case "product":
                        productPackagingButtonSelectText.setVisibility(VISIBLE);
                        productPackagingButtonSelectText.setText(dataSet.get(position).getValue());
                        productPackaging = dataSet.get(position).getId();
                        break;
                    case "mode_of_payment":
                        modeOfPaymentButtonSelectText.setVisibility(VISIBLE);
                        modeOfPaymentButtonSelectText.setText(dataSet.get(position).getValue());
                        modeOfPayment = dataSet.get(position).getId();
                        break;
                    case "credit_terms":
                        creditTermsButtonSelectText.setVisibility(VISIBLE);
                        creditTermsButtonSelectText.setText(dataSet.get(position).getValue());
                        creditTerms = dataSet.get(position).getId();
                        break;
                    case "aac_block_required_check":
                        aacBlockRequiredNotButtonSelectText.setVisibility(VISIBLE);
                        aacBlockRequiredNotButtonSelectText.setText(dataSet.get(position).getValue());
                        aacBlockRequiredStatus = dataSet.get(position).getId();
                        break;
                    case "construction_type":
                        categoryTypeOfConstructionButtonSelectText.setVisibility(VISIBLE);
                        categoryTypeOfConstructionButtonSelectText.setText(dataSet.get(position).getValue());
                        constructionType = dataSet.get(position).getId();
                        break;
                    case "lead_status":
                        leadStatusButtonSelectText.setVisibility(VISIBLE);
                        leadStatusButtonSelectText.setText(dataSet.get(position).getValue());
                        leadStatus = dataSet.get(position).getId();
                        break;
                    case "requirement_type":
                        requirementTypeButtonSelectText.setVisibility(VISIBLE);
                        requirementTypeButtonSelectText.setText(dataSet.get(position).getValue());
                        requirementType = dataSet.get(position).getId();

                        if (dataSet.get(position).getId().equalsIgnoreCase("EXW")) {
                            exWorksButtonLayout.setVisibility(VISIBLE);
                            fosEditTextLayout.setVisibility(GONE);
                        } else if (dataSet.get(position).getId().equalsIgnoreCase("FOS")) {
                            exWorksButtonLayout.setVisibility(GONE);
                            fosEditTextLayout.setVisibility(VISIBLE);
                        } else {
                            exWorksButtonLayout.setVisibility(GONE);
                            fosEditTextLayout.setVisibility(GONE);
                        }
                        break;
                    case "ex_work":
                        exWorksButtonSelectText.setVisibility(VISIBLE);
                        exWorksButtonSelectText.setText(dataSet.get(position).getValue());
                        exWorks = dataSet.get(position).getId();
                        break;
                    case "requirement_timing":
                        requirementTimingButtonSelectText.setVisibility(VISIBLE);
                        requirementTimingButtonSelectText.setText(dataSet.get(position).getValue());
                        requirementTiming = dataSet.get(position).getId();
                        break;
                }
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
    }

    // Date Picker
    private void nextVisitDatePicker() {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                QueryGenerationActivity.this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    // Month is 0-based, so add 1
                    String date = selectedDay + "/" + (selectedMonth + 1) + "/" + selectedYear;
                    nextVisitDateButtonSelectText.setText(date);
                    nextVisitDateButtonSelectText.setVisibility(VISIBLE);
                    nextVisitDate = date;
                },
                year, month, day
        );
        datePickerDialog.getDatePicker().setMinDate(calendar.getTimeInMillis());
        datePickerDialog.show();
    }

    // Query Data Set List Show Dialog Popup
    @SuppressLint("CutPasteId")
    public void show_lead_list_dialog(ArrayList<LeadDataSet> dataSet, String titleValue) {
        try {
            final LeadDataSetAdapter adapterCust = new LeadDataSetAdapter(mContext, R.layout.lead_list_item_layout, dataSet);

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.existion_lead_list_with_filter);
            mDialogCustomer.setCancelable(false);

            LinearLayout filterLayout = mDialogCustomer.findViewById(R.id.filterLayout);
            filterLayout.setVisibility(GONE);
            filterLayout.setOnClickListener(v -> filterLayout.setVisibility(GONE));

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);

            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            ImageView filterIcon = mDialogCustomer.findViewById(R.id.filterIcon);
            filterIcon.setOnClickListener(v -> filterLayout.setVisibility(VISIBLE));

            EditText formDateButton = mDialogCustomer.findViewById(R.id.formDateButton);
            formDateButton.setOnClickListener(v -> {
                Calendar calendar = Calendar.getInstance();
                int year = calendar.get(Calendar.YEAR);
                int month = calendar.get(Calendar.MONTH);
                int day = calendar.get(Calendar.DAY_OF_MONTH);

                DatePickerDialog datePickerDialog = new DatePickerDialog(
                        QueryGenerationActivity.this,
                        (view, selectedYear, selectedMonth, selectedDay) -> {
                            // Month is 0-based, so add 1
                            String date = selectedDay + "/" + (selectedMonth + 1) + "/" + selectedYear;
                            formDateButton.setText(date);
                            filterFormDate = date;
                        },
                        year, month, day
                );
                datePickerDialog.show();
            });

            EditText toDateButton = mDialogCustomer.findViewById(R.id.toDateButton);
            toDateButton.setOnClickListener(v -> {
                Calendar calendar = Calendar.getInstance();
                int year = calendar.get(Calendar.YEAR);
                int month = calendar.get(Calendar.MONTH);
                int day = calendar.get(Calendar.DAY_OF_MONTH);

                DatePickerDialog datePickerDialog = new DatePickerDialog(
                        QueryGenerationActivity.this,
                        (view, selectedYear, selectedMonth, selectedDay) -> {
                            // Month is 0-based, so add 1
                            String date = selectedDay + "/" + (selectedMonth + 1) + "/" + selectedYear;
                            toDateButton.setText(date);
                            filterToDate = date;
                        },
                        year, month, day
                );
                datePickerDialog.show();
            });

            Spinner statusSpinner = mDialogCustomer.findViewById(R.id.statusSpinner);
            List<String> statusList = new ArrayList<>();
            statusList.add("Select Status");
            statusList.add("Pending");
            statusList.add("Hold");
            statusList.add("Revision");
            statusList.add("Accepted");
            statusList.add("Rejected");
            ArrayAdapter<String> adapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, statusList);
            adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            statusSpinner.setAdapter(adapter);
            statusSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                    filterStatus = parent.getItemAtPosition(position).toString();
                }

                @Override
                public void onNothingSelected(AdapterView<?> parent) {
                }
            });

            Button searchButton = mDialogCustomer.findViewById(R.id.searchButton);
            searchButton.setOnClickListener(v -> {
                EditText searchText1 = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
                Gson gson = new Gson();  // or any serializer
                CustomerFilterModel filterModel = new CustomerFilterModel(searchText1.getText().toString(), filterFormDate, filterToDate, filterStatus, "SO");
                adapterCust.getFilter().filter(gson.toJson(filterModel));
                filterLayout.setVisibility(GONE);
            });

            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    Gson gson = new Gson();  // or any serializer
                    CustomerFilterModel filterModel = new CustomerFilterModel(s.toString(), filterFormDate, filterToDate, filterStatus, "SO");
                    adapterCust.getFilter().filter(gson.toJson(filterModel));
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapterCust);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                showExistingLeadAllData(adapterCust.getItem(position));
                _DOWNLOAD_assigned_to(Constants.employeeDetailObject.getEmpCode());
            });

            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    // Data Set List Show Dialog Popup
    @SuppressLint("SetTextI18n")
    public void show_list_data_dialog1(ArrayList<DataSet> dataSet, String value) {
        try {
            final Dialog mDestinationDialog = new Dialog(this, R.style.MyMaterialTheme);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_from_list1);
            mDestinationDialog.setCancelable(true);

            TextView title = mDestinationDialog.findViewById(R.id.title);
            title.setText("Please select");
            ImageView imageView1 = mDestinationDialog.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDestinationDialog.dismiss());
            ListView dialogList = mDestinationDialog.findViewById(R.id.list);
            Button btn_cncl = mDestinationDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setVisibility(GONE);

            final ShowDataSetAdapter pAdapter = new ShowDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                switch (value) {
                    case "segment":
                        existingSegmentButtonSelectText.setVisibility(VISIBLE);
                        existingSegmentButtonSelectText.setText(dataSet.get(position).getValue());
                        segment = dataSet.get(position).getId();
                        break;
                    case "lead_source":
                        existingLeadSourceButtonSelectText.setVisibility(VISIBLE);
                        existingLeadSourceButtonSelectText.setText(dataSet.get(position).getValue());
                        leadSource = dataSet.get(position).getId();
                        break;
                    case "product":
                        existingProductPackagingButtonSelectText.setVisibility(VISIBLE);
                        existingProductPackagingButtonSelectText.setText(dataSet.get(position).getValue());
                        productPackaging = dataSet.get(position).getId();
                        break;
                    case "mode_of_payment":
                        existingModeOfPaymentButtonSelectText.setVisibility(VISIBLE);
                        existingModeOfPaymentButtonSelectText.setText(dataSet.get(position).getValue());
                        modeOfPayment = dataSet.get(position).getId();
                        break;
                    case "credit_terms":
                        existingCreditTermsButtonSelectText.setVisibility(VISIBLE);
                        existingCreditTermsButtonSelectText.setText(dataSet.get(position).getValue());
                        creditTerms = dataSet.get(position).getId();
                        break;
                    case "aac_block_required_check":
                        existingAacBlockRequiredNotButtonSelectText.setVisibility(VISIBLE);
                        existingAacBlockRequiredNotButtonSelectText.setText(dataSet.get(position).getValue());
                        aacBlockRequiredStatus = dataSet.get(position).getId();
                        break;
                    case "construction_type":
                        existingCategoryTypeOfConstructionButtonSelectText.setVisibility(VISIBLE);
                        existingCategoryTypeOfConstructionButtonSelectText.setText(dataSet.get(position).getValue());
                        constructionType = dataSet.get(position).getId();
                        break;
                    case "lead_status":
                        existingLeadStatusButtonSelectText.setVisibility(VISIBLE);
                        existingLeadStatusButtonSelectText.setText(dataSet.get(position).getValue());
                        leadStatus = dataSet.get(position).getId();
                        break;
                    case "requirement_type":
                        existingRequirementTypeButtonSelectText.setVisibility(VISIBLE);
                        existingRequirementTypeButtonSelectText.setText(dataSet.get(position).getValue());
                        requirementType = dataSet.get(position).getId();

                        if (dataSet.get(position).getId().equalsIgnoreCase("EXW")) {
                            existingExWorksButtonLayout.setVisibility(VISIBLE);
                            existingFosEditTextLayout.setVisibility(GONE);
                        } else if (dataSet.get(position).getId().equalsIgnoreCase("FOS")) {
                            existingExWorksButtonLayout.setVisibility(GONE);
                            existingFosEditTextLayout.setVisibility(VISIBLE);
                        } else {
                            existingExWorksButtonLayout.setVisibility(GONE);
                            existingFosEditTextLayout.setVisibility(GONE);
                        }
                        break;
                    case "ex_work":
                        existingExWorksButtonSelectText.setVisibility(VISIBLE);
                        existingExWorksButtonSelectText.setText(dataSet.get(position).getValue());
                        exWorks = dataSet.get(position).getId();
                        break;
                    case "requirement_timing":
                        existingRequirementTimingButtonSelectText.setVisibility(VISIBLE);
                        existingRequirementTimingButtonSelectText.setText(dataSet.get(position).getValue());
                        requirementTiming = dataSet.get(position).getId();
                        break;
                }
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
    }

    // Date Picker
    private void nextVisitDatePicker1() {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                QueryGenerationActivity.this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    // Month is 0-based, so add 1
                    String date = selectedDay + "/" + (selectedMonth + 1) + "/" + selectedYear;
                    existingNextVisitDateButtonSelectText.setText(date);
                    existingNextVisitDateButtonSelectText.setVisibility(VISIBLE);
                    nextVisitDate = date;
                },
                year, month, day
        );
        datePickerDialog.getDatePicker().setMinDate(calendar.getTimeInMillis());
        datePickerDialog.show();
    }
    // ======================================== //


    // ==================== Existing query data show ==================== //
    // function of get data from object with try-catch multi
    private String getDataMultiObject(JSONObject parent, String objectKey, String stringKey) {
        try {
            JSONObject nested = parent.optJSONObject(objectKey);
            return nested != null ? nested.get(stringKey).toString() : "";
        } catch (Exception e) {
            return "";
        }
    }

    // function of get data from object with try-catch single
    private String getDataSingleObject(JSONObject parent, String stringKey) {
        String returnData = "";
        try {
            returnData = parent.get(stringKey).toString();
        } catch (Exception ignored) {
        }
        return returnData;
    }

    // Existing query data show
    private void showExistingLeadAllData(LeadDataSet data) {
        try {
            JSONObject obj = data.getFullData();
            Log.d("TAG", "requestForLeadGeneration: "+obj.toString());
            existingEditTextSalesOfficerName.setText(getDataMultiObject(obj, "emp_details", "emp_name"));
            if (TYPE_OF_LEAD.equalsIgnoreCase("existing")) {
                existingEditTextDateStamp.setText(getDataSingleObject(obj, "download_time").split("T")[0]);
                existingEditTextTimeStamp.setText(getDataSingleObject(obj, "download_time").split("T")[1].replace("Z", ""));
                existingEditTextLatitude.setText(getDataSingleObject(obj, "latitude"));
                existingEditTextLongitude.setText(getDataSingleObject(obj, "longitude"));
                existingTextUniqueLeadID.setText(getDataSingleObject(obj, "lead_generation_id"));
            }
            existingEditTextSoldToPartyName.setText(getDataMultiObject(obj, "sold_to_party_details", "name"));
            existingEditTextSoldToPartyCode.setText(getDataSingleObject(obj, "sold_to_party"));
            existingEditTextSoldToPartyAddress.setText(getDataMultiObject(obj, "sold_to_party_details", "address"));
            existingEditTextSoldToPartyState.setText(getDataMultiObject(obj, "sold_to_party_details", "state"));
            existingEditTextSoldToPartyDistricts.setText(getDataMultiObject(obj, "sold_to_party_details", "districts"));
            existingEditTextShipToPartyName.setText(getDataMultiObject(obj, "ship_to_party_details", "name"));
            existingEditTextShipToPartyCode.setText(getDataSingleObject(obj, "ship_to_party"));
            existingEditTextShipToPartyAddress.setText(getDataMultiObject(obj, "ship_to_party_details", "address"));
            existingEditTextShipToPartyState.setText(getDataMultiObject(obj, "ship_to_party_details", "state"));
            existingEditTextShipToPartyDistricts.setText(getDataMultiObject(obj, "ship_to_party_details", "districts"));
            existingEditTextTotalPotentialOfSite.setText(getDataSingleObject(obj, "qty_req"));
            existingEditTextQuotationQuantity.setText(getDataSingleObject(obj, "month_qty"));
            existingEditTextCurrentBrandUsed.setText(getDataSingleObject(obj, "current_brand_used"));
            existingEditTextExpectedRatePerBag.setText(getDataSingleObject(obj, "exp_rate_per_bag"));
            existingEditTextCurrentPriceStarRsPerBag.setText(getDataSingleObject(obj, "current_price"));
            existingEditTextCurrentPriceCompetitorRsPerBag.setText(getDataSingleObject(obj, "current_price_competitor"));
            existingEditTextContactPersonName.setText(getDataSingleObject(obj, "contact_person_name"));
            existingEditTextReferredBy.setText(getDataSingleObject(obj, "customer_reference_no"));
            existingEditTextDesignation.setText(getDataSingleObject(obj, "designation"));
            existingEditTextContactNumber.setText(getDataSingleObject(obj, "contact_number"));
            existingEditTextMailId.setText(getDataSingleObject(obj, "mail_id"));
            existingEditTextFosSiding.setText(getDataSingleObject(obj, "serving_location"));
            existingEditTextSalesOfficerRemarks.setText(getDataSingleObject(obj, "lead_remarks"));
            existingSegmentButtonSelectText.setText(getDataSingleObject(obj, "type_lead"));
            existingLeadSourceButtonSelectText.setText(getDataSingleObject(obj, "lead_type"));
            existingProductPackagingButtonSelectText.setText(getDataSingleObject(obj, "product_packaging"));
            existingModeOfPaymentButtonSelectText.setText(getDataSingleObject(obj, "mode"));
            existingCreditTermsButtonSelectText.setText(getDataSingleObject(obj, "credit_terms"));
            existingAacBlockRequiredNotButtonSelectText.setText(getDataSingleObject(obj, "acc_block_is_required"));
            existingCategoryTypeOfConstructionButtonSelectText.setText(getDataSingleObject(obj, "category_type_construction"));
            existingLeadStatusButtonSelectText.setText(getDataSingleObject(obj, "lead_status"));
            existingNextVisitDateButtonSelectText.setText(getDataSingleObject(obj, "next_visit_date"));
            existingRequirementTypeButtonSelectText.setText(getDataSingleObject(obj, "incoterms"));
            existingAssignedToButtonSelectText.setText(getDataMultiObject(obj, "assigned_to_details", "emp_name"));
            existingRequirementTimingButtonSelectText.setText(getDataSingleObject(obj, "r_timing"));

            try {
                if (obj.getString("incoterms").equalsIgnoreCase("EXW")) {
                    existingExWorksButtonLayout.setVisibility(VISIBLE);
                    existingFosEditTextLayout.setVisibility(GONE);
                } else if (obj.getString("incoterms").equalsIgnoreCase("FOS")) {
                    existingExWorksButtonLayout.setVisibility(GONE);
                    existingFosEditTextLayout.setVisibility(VISIBLE);
                } else {
                    existingExWorksButtonLayout.setVisibility(GONE);
                    existingFosEditTextLayout.setVisibility(GONE);
                }
                existingExWorksButtonSelectText.setText(obj.getString("serving_location"));
                existingEditTextFosSiding.setText(obj.getString("serving_location"));
            } catch (Exception e) {
                existingExWorksButtonSelectText.setText("");
                existingEditTextFosSiding.setText("");
            }

            currentBrandUsed = getDataSingleObject(obj, "current_brand_used");
            contactPersonName = getDataSingleObject(obj, "contact_person_name");
            contactPersonDesignation = getDataSingleObject(obj, "designation");
            contactPersonNumber = getDataSingleObject(obj, "contact_number");
            contactPersonMail = getDataSingleObject(obj, "mail_id");
            assignedTo = getDataSingleObject(obj, "assigned_to");
            requirementTiming = getDataSingleObject(obj, "r_timing");
            selectEmpCode=getDataSingleObject(obj, "emp_code");
            soldToPartyCode=getDataSingleObject(obj, "sold_to_party");
            shipToPartyCode=getDataSingleObject(obj, "ship_to_party");
            if(selectEmpCode.equalsIgnoreCase(Constants.employeeDetailObject.getEmpCode())){
                officerFor = "SELF";
            }else{
                officerFor = "OTHER";
            }

            if (obj.getString("incoterms").equalsIgnoreCase("EXW")) {
                exWorks = obj.getString("serving_location");
                requirementType = "exw";
            } else if (obj.getString("incoterms").equalsIgnoreCase("FOS")) {
                fosText = obj.getString("serving_location");
                requirementType = "fos";
            }

            nextVisitDate = getDataSingleObject(obj, "next_visit_date");
            segment = getDataSingleObject(obj, "type_lead");
            leadSource = getDataSingleObject(obj, "lead_type");
            productPackaging = getDataSingleObject(obj, "product_packaging");
            modeOfPayment = getDataSingleObject(obj, "mode");
            creditTerms = getDataSingleObject(obj, "credit_terms");
            aacBlockRequiredStatus = getDataSingleObject(obj, "acc_block_is_required");
            constructionType = getDataSingleObject(obj, "category_type_construction");
            leadStatus = getDataSingleObject(obj, "lead_status");
            requirementType = getDataSingleObject(obj, "incoterms");
            exWorks = getDataSingleObject(obj, "serving_location");




            existingTextUniqueLeadID.setVisibility(VISIBLE);
            existingSegmentButtonSelectText.setVisibility(VISIBLE);
            existingLeadSourceButtonSelectText.setVisibility(VISIBLE);
            existingProductPackagingButtonSelectText.setVisibility(VISIBLE);
            existingModeOfPaymentButtonSelectText.setVisibility(VISIBLE);
            existingCreditTermsButtonSelectText.setVisibility(VISIBLE);
            existingAacBlockRequiredNotButtonSelectText.setVisibility(VISIBLE);
            existingCategoryTypeOfConstructionButtonSelectText.setVisibility(VISIBLE);
            existingLeadStatusButtonSelectText.setVisibility(VISIBLE);
            existingNextVisitDateButtonSelectText.setVisibility(VISIBLE);
            existingRequirementTypeButtonSelectText.setVisibility(VISIBLE);
            existingExWorksButtonSelectText.setVisibility(VISIBLE);
            existingAssignedToButtonSelectText.setVisibility(VISIBLE);
            existingRequirementTimingButtonSelectText.setVisibility(VISIBLE);



            _DOWNLOAD_assigned_to1(getDataSingleObject(obj, "emp_code"));
        } catch (Exception ignored) {
        }

        isButtonPressForExistingLeadButton = true;
        saveAsDraftButton.setVisibility(VISIBLE);
        moveToLeadButton.setVisibility(VISIBLE);
    }
    // ======================================== //


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
        ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }
    // ======================================== //


    // ==================== All Init API ==================== //
    // Employee Details API
    @SuppressLint("StaticFieldLeak")
    public class TRANS_EmployeeDetails_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String emp_code;

        public TRANS_EmployeeDetails_AsyncTask(Context context) {
            this.mContext = context;
            this.emp_code = Constants.employeeDetailObject.getEmpCode();
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
                    String url = BaseUrl.sbDevUrl + "api/employee/?emp_code=" + emp_code;
                    Log.d("TAG", "_DOWNLOAD_ doInBackground: "+url);
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
                if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt")) {
                    ((QueryGenerationActivity) mContext).runOnUiThread(() -> {
                        leadTypeRadioGroupLayout.setVisibility(View.VISIBLE);
                    });
                    new TRANS_OldLead_AsyncTask(mContext, "so").execute();
                } else {
                    progressDialogClose();
                    ((QueryGenerationActivity) mContext).runOnUiThread(QueryGenerationActivity.this::finish);
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ doInBackground error: "+e.getMessage());
                progressDialogClose();
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }
    // All Old Query API
    @SuppressLint("StaticFieldLeak")
    public class TRANS_OldLead_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String emp_code, dataTyps;

        public TRANS_OldLead_AsyncTask(Context context, String dataType) {
            this.mContext = context;
            this.emp_code = Constants.employeeDetailObject.getEmpCode();
            this.dataTyps = dataType;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.sbDevUrl + "api/leadquerymaster/?emp_code=" + emp_code;
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
                existingLeadList.clear();
                allExistingLeadList.clear();
                for (var i = 0; i < arr.length(); i++) {
                    LeadDataSet temp = new LeadDataSet();
                    temp.setLeadId(arr.getJSONObject(i).getString("lead_generation_id"));
                    if (arr.getJSONObject(i).isNull("lead_action") || arr.getJSONObject(i).getString("lead_action").trim().equalsIgnoreCase("")) {
                        temp.setLeadStatus("Pending");
                    } else {
                        temp.setLeadStatus(arr.getJSONObject(i).getString("lead_action"));
                    }

                    if (arr.getJSONObject(i).isNull("lead_action")) {
                        temp.setTitle("");
                    } else {
                        temp.setTitle(arr.getJSONObject(i).getString("party_name"));
                    }

                    if (arr.getJSONObject(i).isNull("sold_to_party")) {
                        temp.setSoldToPartyName("");
                    } else {
                        try {
                            temp.setSoldToPartyName(arr.getJSONObject(i).getJSONObject("sold_to_party_details").getString("name"));
                        } catch (Exception e) {
                            temp.setSoldToPartyName("");
                        }
                    }

                    if (arr.getJSONObject(i).isNull("ship_to_party")) {
                        temp.setShipToPartyName("");
                    } else {
                        try {
                            temp.setShipToPartyName(arr.getJSONObject(i).getJSONObject("ship_to_party_details").getString("name"));
                        } catch (Exception e) {
                            temp.setShipToPartyName("");
                        }
                    }

                    temp.setFullData(arr.getJSONObject(i));
                    existingLeadList.add(temp);
                    allExistingLeadList.add(temp);
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ doInBackground error1: " + e.getMessage());
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
            progressDialogClose();
        }
    }

    // DOWNLOAD TXT FUNCTION
    private void Download_txt(String URL, String data) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        URL url = null;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + data + ".txt");
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

    // All Sold to Party List API
    public void _DOWNLOAD_sold_to_party(String emp_code) {
        progressDialogOpen("Download Sold to Party list ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/ptblcustomermasterlist/?customer_type=sold&emp_code=" + emp_code;
        Log.d("TAG", "_DOWNLOAD_sold_to_party: " + URL);

        new Thread(() -> {
            Download_txt(URL, "sold_to_party");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "sold_to_party" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line;
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        Log.d("TAG", "_DOWNLOAD_sold_to_party: "+RowData.length);
                        PartyDataList temp = new PartyDataList();
                        temp.setCode(RowData[1]);
                        temp.setName(RowData[4]);
                        temp.setCustomerCode(RowData[1]);
                        temp.setPhoneNo(RowData[20]);
                        temp.setDistrict(RowData[14]);
                        temp.setState(RowData[11]);
                        temp.setAddress(RowData[9]);
                        soldToPartyList.add(temp);
                    }
                }
                buffer.close();

                // move off background thread before touching the dialog/UI
                runOnUiThread(() -> _DOWNLOAD_ship_to_party(emp_code));

            } catch (IOException ex) {
                runOnUiThread(this::progressDialogClose);
            }
        }).start();
    }

    // All Ship to Party List API
    public void _DOWNLOAD_ship_to_party(String emp_code) {
        progressDialogUpdate("Download Ship to Party list ..."); // fine if this method itself runs on UI thread
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/ptblcustomermasterlist/?customer_type=ship&emp_code=" + emp_code;
        Log.d("TAG", "_DOWNLOAD_ship_to_party: " + URL);

        new Thread(() -> {
            Download_txt(URL, "ship_to_party");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ship_to_party" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line;
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        PartyDataList temp = new PartyDataList();
                        temp.setCode(RowData[1]);
                        temp.setName(RowData[4]);
                        temp.setCustomerCode(RowData[1]);
                        temp.setPhoneNo(RowData[20]);
                        temp.setDistrict(RowData[14]);
                        temp.setState(RowData[11]);
                        temp.setAddress(RowData[9]);
                        shipToPartyList.add(temp);
                    }
                }
                buffer.close();

                runOnUiThread(() -> _DOWNLOAD_assigned_to(emp_code));

            } catch (IOException ex) {
                runOnUiThread(this::progressDialogClose);
            }
        }).start();
    }

    // All Assigned to List API
    public void _DOWNLOAD_assigned_to(String emp_code) {
        progressDialogUpdate("Download Assigned To list ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/employee_list/?emp_code=" + emp_code + "&level_type=hos";
        new Thread(() -> {
            Download_txt(URL, "assigned_to");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "assigned_to" + ".txt");
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
                            EmployeeDataSet temp = new EmployeeDataSet();
                            temp.setEmp_code(RowData[0]);
                            temp.setEmp_name(RowData[2]);
                            temp.setSelect(false);
                            assignedToDetailsList.add(temp);
                            temp = null;
                        }
                    }
                }
                buffer.close();
                runOnUiThread(() -> _DOWNLOAD_product_list());
            } catch (IOException ex) {
                runOnUiThread(this::progressDialogClose);
            }
        }).start();
    }

    // All Product List API
    public void _DOWNLOAD_product_list() {
        progressDialogUpdate("Download Product list ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/lead_product_list/";
        new Thread(() -> {
            Download_txt(URL, "product_list");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "product_list" + ".txt");
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
                            temp.setId(RowData[0]);
                            temp.setValue(RowData[0]);
                            temp.setSelect(false);
                            productList.add(temp);
                            temp = null;
                        }
                    }
                }
                buffer.close();
                runOnUiThread(() -> _DOWNLOAD_state_list());
            } catch (IOException ex) {
                runOnUiThread(this::progressDialogClose);
            }
        }).start();
    }

    // All State List API
    public void _DOWNLOAD_state_list() {
        progressDialogUpdate("Download State list ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/lead_state_list/";
        new Thread(() -> {
            Download_txt(URL, "state_list");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "state_list" + ".txt");
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
                            StateDataSet temp = new StateDataSet();
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            temp.setSelect(false);
                            stateList.add(temp);
                            temp = null;
                        }
                    }
                }
                buffer.close();
                runOnUiThread(() -> _DOWNLOAD_district_list());
            } catch (IOException ex) {
                runOnUiThread(this::progressDialogClose);
            }
        }).start();
    }

    // All District List API
    public void _DOWNLOAD_district_list() {
        progressDialogUpdate("Download District list ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/lead_district_list/";
        new Thread(() -> {
            Download_txt(URL, "district_list");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "district_list" + ".txt");
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
                            DistrictDataSet temp = new DistrictDataSet();
                            temp.setTitle(RowData[0]);
                            temp.setValue(RowData[0]);
                            temp.setStateCode(RowData[1]);
                            temp.setSelect(false);
                            districtList.add(temp);
                            temp = null;
                        }
                    }
                }
                buffer.close();
                runOnUiThread(this::progressDialogClose);
            } catch (IOException ex) {
                runOnUiThread(this::progressDialogClose);
            }
        }).start();
    }

    // All Other Employee details API
    public void _DOWNLOAD_emp_master() {
        progressDialogOpen("Download employee list ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/employee_list/?emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&level_type=so";
        new Thread(() -> {
            Download_txt(URL, "emp_data");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "emp_data" + ".txt");
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
                            EmployeeDataSet temp = new EmployeeDataSet();
                            temp.setEmp_code(RowData[0]);
                            temp.setEmp_name(RowData[2]);
                            temp.setSelect(false);
                            employeeMasterDetailsList.add(temp);
                            temp = null;
                        }
                    }
                }
                buffer.close();
                runOnUiThread(this::progressDialogClose);
            } catch (IOException ex) {
                runOnUiThread(this::progressDialogClose);
            }
        }).start();
    }

    // All Assigned to List API
    public void _DOWNLOAD_assigned_to1(String emp_code) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/employee_list/?emp_code=" + emp_code + "&level_type=hos";
        Log.d("TAG", "_DOWNLOAD_ assigned_to1: "+URL);
        new Thread(() -> {
            Download_txt(URL, "assigned_to");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "assigned_to" + ".txt");
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
                            EmployeeDataSet temp = new EmployeeDataSet();
                            temp.setEmp_code(RowData[0]);
                            temp.setEmp_name(RowData[2]);
                            temp.setSelect(false);
                            assignedToDetailsList.add(temp);
                            temp = null;
                        }
                    }
                }
                buffer.close();
            } catch (IOException ex) {
            }
        }).start();
    }
    // ======================================== //


    // ==================== Static Data List ==================== //
    // All DataSet List
    private void dataSet(String empCode){
        soldToPartyList= mNewDatabaseForSiteLead.getAllSoldToParty(empCode);
        shipToPartyList= mNewDatabaseForSiteLead.getAllShipToParty(empCode);
        assignedToDetailsList= mNewDatabaseForSiteLead.getAllAssignedTo(empCode);
        productList= mNewDatabaseForSiteLead.getAllProductList();
        stateList= mNewDatabaseForSiteLead.getAllStateList();
        districtList= mNewDatabaseForSiteLead.getAllDistrictList();
        employeeMasterDetailsList= mNewDatabaseForSiteLead.getAllEmployeeDetails();


        segmentList = mNewDatabaseForSiteLead.getAllSegmentList();
        leadSourceList =mNewDatabaseForSiteLead.getAllLeadSourceList();
        modeOfPaymentList = mNewDatabaseForSiteLead.getAllModeOfPaymentList();
        creditTermsList = mNewDatabaseForSiteLead.getAllCreditTermsList();
        aacBlockRequiredCheckList = mNewDatabaseForSiteLead.getAllAacBlockRequiredList();
        constructionTypeList = mNewDatabaseForSiteLead.getAllConstructionTypeList();
        leadStatusList = mNewDatabaseForSiteLead.getAllLeadStatusList();
        requirementTypeList= mNewDatabaseForSiteLead.getAllRequirementTypeList();
        exWorkList = mNewDatabaseForSiteLead.getAllExWorksList();
        requirementTimingList= mNewDatabaseForSiteLead.getAllRequirementTimingList();
        leadActionList= mNewDatabaseForSiteLead.getAllLeadActionList();
    }
    // All Segment List
    private void dataSetSegmentList() {
        segmentList = new ArrayList<>();

        String[][] data = {
                {"KEY", "Key"},
                {"NON-KEY", "Non-Key"},
                {"TENDER", "Tender"},
                {"ROE", "ROE"}
        };

        for (String[] entry : data) {
            segmentList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetLeadSourceList();
    }

    // All Query Source List
    private void dataSetLeadSourceList() {
        leadSourceList = new ArrayList<>();

        String[][] data = {
                {"TENDER / GOVERNMENT PROJECT INFO", "Tender / Government Project Info"},
                {"AGENCIES", "Agencies"},
                {"SITE VISIT", "Site Visit"},
                {"COMPANY SELF", "Company Self"},
                {"PHONE CALL", "Phone Call"},
                {"SALES TEAM GENERATED", "Sales Team Generated"},
                {"SOCIAL MEDIA", "Social Media"},
                {"ONLINE ADS", "Online Ads"},
                {"EMAIL CAMPAIGNS", "Email Campaigns"},
                {"WALK-IN AT STAR OFFICE", "Walk-in at Star Office"},
                {"MEETING", "Meeting"},
                {"REFERRAL FROM EXISTING CUSTOMER", "Referral from Existing Customer"},
                {"DEALER / RSAR REFERENCE", "Dealer / RSAR Reference"},
                {"EXHIBITIONS / TRADE FAIRS", "Exhibitions / Trade Fairs"},
                {"HOARDINGS / BANNERS", "Hoardings / Banners"},
                {"NEWSPAPER ADS", "Newspaper Ads"},
                {"CAMPAIGNS / PROMOTIONAL ACTIVITIES", "Campaigns / Promotional Activities"},
                {"OLD LEADS", "Old Leads"}
        };

        for (String[] entry : data) {
            leadSourceList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetModeOfPaymentList();
    }

    // All Mode of Payment List
    private void dataSetModeOfPaymentList() {
        modeOfPaymentList = new ArrayList<>();

        String[][] data = {
                {"ADVANCE", "Advance"},
                {"BANK GUARANTEE", "BG"},
                {"CLEAN CREDIT", "Clean Credit"},
                {"SECURITY CHEQUE", "Security Cheque"},
                {"POST DATED CHEQUE", "Post Dated Cheque"},
                {"OTHERS", "Others"}
        };

        for (String[] entry : data) {
            modeOfPaymentList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetCreditTermsList();
    }

    // All Credit Terms List
    private void dataSetCreditTermsList() {
        creditTermsList = new ArrayList<>();

        String[][] data = {
                {"7 DAYS", "7 Days"},
                {"15 DAYS", "15 Days"},
                {"30 DAYS", "30 Days"},
                {"45 DAYS", "45 Days"},
                {"60 DAYS", "60 Days"},
                {"75 DAYS", "75 Days"},
                {"90 DAYS", "90 Days"}
        };

        for (String[] entry : data) {
            creditTermsList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetAacBlockRequiredCheckList();
    }

    // All AAC Block Required Check List
    private void dataSetAacBlockRequiredCheckList() {
        aacBlockRequiredCheckList = new ArrayList<>();

        String[][] data = {
                {"YES", "Yes "},
                {"NO", "No "}
        };

        for (String[] entry : data) {
            aacBlockRequiredCheckList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetConstructionTypeList();
    }

    // All Construction Type List
    private void dataSetConstructionTypeList() {
        constructionTypeList = new ArrayList<>();

        String[][] data = {
                {"BUILDING PROJECTS - PUBLIC OR PRIVATE", "Building Projects - Public or Private"},
                {"INDUSTRIAL - MANUFACTURING", "Industrial - Manufacturing"},
                {"INDUSTRIAL - WAREHOUSING", "Industrial - Warehousing"},
                {"ROADS, BRIDGES AND HIGHWAYS", "Roads, Bridges and Highways"},
                {"WATER SUPPLY & DISTRIBUTION", "Water Supply & Distribution"},
                {"POWER PROJECTS", "Power Projects"},
                {"RAILWAY PROJECT OR SLEEPER MANUFACTURING", "Railway Project or Sleeper Manufacturing"},
                {"PRE-CAST INDUSTRIES (PIPE AND POLES)", "Pre-cast Industries (Pipe and Poles)"},
                {"OTHER", "Other"}
        };

        for (String[] entry : data) {
            constructionTypeList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetLeadStatusList();
    }

    // All Query Status List
    private void dataSetLeadStatusList() {
        leadStatusList = new ArrayList<>();

        String[][] data = {
                {"HOT", "Hot ( Immediate Requirement - within 7 days )"},
                {"WARM", "Warm ( Planned Required - within 8 to 14 days )"},
                {"COLD", "Cold ( Future requirement - after 15 or more days )"}
        };

        for (String[] entry : data) {
            leadStatusList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetRequirementTypeList();
    }

    // All Requirement Type List
    private void dataSetRequirementTypeList() {
        String[][] data = {
                {"FOR", "Free on Road (FOR)"},
                {"EXW", "Ex. Works (ExW)"},
                {"FOS", "Free on Siding (FOS)"}
        };

        for (String[] entry : data) {
            requirementTypeList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetExWorkList();
    }

    // All Ex Work List
    private void dataSetExWorkList() {
        String[][] data = {
                {"EX. LUMS PLANT", "Ex. LUMS Plant"},
                {"EX. GGU LINE-1/SCNEL", "Ex. GGU Line-1/SCNEL"},
                {"EX. SGU PLANT", "Ex. SGU Plant"},
                {"EX. BYRNIHAT DUMP", "Ex. Byrnihat Dump"},
                {"EX. JORABAT DUMP", "Ex. Jorabat Dump"},
                {"EX. FULERTAL DUMP", "Ex. Fulertal Dump"},
                {"EX. SILIGURI-2 DUMP", "Ex. Siliguri-2 Dump"},
                {"EX. VAIRENGTE DUMP", "Ex. Vairengte Dump"},
                {"EX. AIZWAL DUMP", "Ex. Aizwal Dump"},
                {"EX. SILCHAR DUMP", "Ex. Silchar Dump"}
        };

        for (String[] entry : data) {
            exWorkList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetRequirementTimingList();
    }

    // All Requirement Timing List
    private void dataSetRequirementTimingList() {
        String[][] data = {
                {"7 DAYS", "7 Days"},
                {"15 DAYS", "15 Days"},
                {"30 DAYS", "30 Days"},
                {"45 DAYS", "45 Days"},
                {"60 DAYS", "60 Days"},
                {"75 DAYS", "75 Days"},
                {"90 DAYS", "90 Days"}
        };

        for (String[] entry : data) {
            requirementTimingList.add(new DataSet(entry[0], entry[1], false));
        }

        dataSetLeadActionList();
    }

    // All QueryQuery Action List
    private void dataSetLeadActionList() {
        String[][] data = {
                {"YES", "Yes, send quotation"},
                {"NO", "No, not required quotation"},
                {"HOLD", "Hold"},
                {"REVISION", "Send back for revision"}
        };

        for (String[] entry : data) {
            leadActionList.add(new DataSet(entry[0], entry[1], false));
        }
    }
    // ======================================== //
}