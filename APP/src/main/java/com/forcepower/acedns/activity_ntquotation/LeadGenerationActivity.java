package com.forcepower.acedns.activity_ntquotation;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
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
import androidx.core.content.ContextCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity_ntquotation.adapter.ShowDataSetAdapter;
import com.forcepower.acedns.activity_ntquotation.adapter.ShowDistrictDataSetAdapter;
import com.forcepower.acedns.activity_ntquotation.adapter.ShowEmployeeDataSetAdapter;
import com.forcepower.acedns.activity_ntquotation.adapter.ShowPartyDataSetAdapter;
import com.forcepower.acedns.activity_ntquotation.adapter.ShowStateDataSetAdapter;
import com.forcepower.acedns.activity_ntquotation.dataset.CustomerFilterModel;
import com.forcepower.acedns.activity_ntquotation.dataset.DataSet;
import com.forcepower.acedns.activity_ntquotation.dataset.DistrictDataSet;
import com.forcepower.acedns.activity_ntquotation.dataset.EmployeeDataSet;
import com.forcepower.acedns.activity_ntquotation.dataset.PartyDataList;
import com.forcepower.acedns.activity_ntquotation.dataset.StateDataSet;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.khoj.adapter.LeadDataSetAdapter;
import com.forcepower.acedns.new_activity.khoj.data_set.LeadDataSet;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.Utils;
import com.google.gson.Gson;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

import java.net.URL;
import java.util.List;
import java.util.Objects;

public class LeadGenerationActivity extends ComponentActivity implements View.OnClickListener, RadioGroup.OnCheckedChangeListener {
    Context mContext;
    Button backButton;

    LinearLayout leadTypeRadioGroupLayout;

    // New Lead Generate
    LinearLayout leadGenerationLayout, salesOfficerDetailsLayout, otherSalesOfficeNameButtonLayout, exWorksButtonLayout, fosEditTextLayout;
    RadioGroup leadTypeRadioGroup, generateForRadioGroup;
    RadioButton newLeadRadioButton, existingLeadRadioButton, selfRadioButton, othersRadioButton;
    TextView textUniqueLeadID, textGenerateFor, textSalesOfficerName, textDateStamp, textTimeStamp, textLatitude, textLongitude, textSoldToPartyName, textSoldToPartyCode, textSoldToPartyAddress,
            textShipToPartyName, textShipToPartyCode, textShipToPartyAddress, textTotalPotentialOfSite, textQuotationQuantity, textCurrentBrandUsed, textExpectedRatePerBag, textCurrentPriceStarRsPerBag,
            textCurrentPriceCompetitorRsPerBag, textContactPersonName, textDesignation, textContactNumber, textMailId, textSalesOfficerRemarks, textFosSiding;
    Button salesOfficerNameButton, soldToPartyNameButton, soldToPartyStateButton, soldToPartyDistrictsButton, shipToPartyNameButton, shipToPartyStateButton, shipToPartyDistrictsButton, segmentButton, leadSourceButton,
            productPackagingButton, modeOfPaymentButton, creditTermsButton, aacBlockRequiredNotButton, categoryTypeOfConstructionButton, leadStatusButton, nextVisitDateButton, requirementTypeButton,
            exWorksButton, assignedToButton, requirementTimingButton, shareLeadImageButton, leadActionButton;
    EditText editTextUniqueLeadID, editTextSalesOfficerName, editTextDateStamp, editTextTimeStamp, editTextLatitude, editTextLongitude, editTextSoldToPartyName, editTextSoldToPartyCode, editTextSoldToPartyAddress,
            editTextShipToPartyName, editTextShipToPartyCode, editTextShipToPartyAddress, editTextTotalPotentialOfSite, editTextQuotationQuantity, editTextCurrentBrandUsed, editTextExpectedRatePerBag,
            editTextCurrentPriceStarRsPerBag, editTextCurrentPriceCompetitorRsPerBag, editTextContactPersonName, editTextDesignation, editTextContactNumber, editTextMailId, editTextSalesOfficerRemarks,
            editTextFosSiding;
    TextView soldToPartyStateButtonSelectText, soldToPartyDistrictsButtonSelectText, shipToPartyStateButtonSelectText, shipToPartyDistrictsButtonSelectText, segmentButtonSelectText, leadSourceButtonSelectText,
            productPackagingButtonSelectText, modeOfPaymentButtonSelectText, creditTermsButtonSelectText, aacBlockRequiredNotButtonSelectText, categoryTypeOfConstructionButtonSelectText, leadStatusButtonSelectText,
            nextVisitDateButtonSelectText, requirementTypeButtonSelectText, exWorksButtonSelectText, assignedToButtonSelectText, requirementTimingButtonSelectText;

    // Existing Lead Generate
    LinearLayout existingGenerationLayout, existingExWorksButtonLayout, existingFosEditTextLayout;
    TextView existingTextSalesOfficerName, existingTextDateStamp, existingTextTimeStamp, existingTextLatitude, existingTextLongitude, existingTextSoldToPartyName,
            existingTextSoldToPartyCode, existingTextSoldToPartyAddress, existingTextSoldToPartyState, existingTextSoldToPartyDistricts, existingTextShipToPartyName, existingTextShipToPartyCode,
            existingTextShipToPartyAddress, existingTextShipToPartyState, existingTextShipToPartyDistricts, existingTextTotalPotentialOfSite, existingTextQuotationQuantity, existingTextCurrentBrandUsed, existingTextExpectedRatePerBag, existingTextCurrentPriceStarRsPerBag,
            existingTextCurrentPriceCompetitorRsPerBag, existingTextContactPersonName, existingTextDesignation, existingTextContactNumber, existingTextMailId, existingTextFosSiding, existingTextSalesOfficerRemarks;
    EditText existingEditTextSalesOfficerName, existingEditTextDateStamp, existingEditTextTimeStamp, existingEditTextLatitude, existingEditTextLongitude, existingEditTextSoldToPartyName,
            existingEditTextSoldToPartyCode, existingEditTextSoldToPartyAddress, existingEditTextSoldToPartyState, existingEditTextSoldToPartyDistricts, existingEditTextShipToPartyName, existingEditTextShipToPartyCode,
            existingEditTextShipToPartyAddress, existingEditTextShipToPartyState, existingEditTextShipToPartyDistricts, existingEditTextTotalPotentialOfSite, existingEditTextQuotationQuantity, existingEditTextCurrentBrandUsed, existingEditTextExpectedRatePerBag,
            existingEditTextCurrentPriceStarRsPerBag, existingEditTextCurrentPriceCompetitorRsPerBag, existingEditTextContactPersonName, existingEditTextDesignation, existingEditTextContactNumber, existingEditTextMailId,
            existingEditTextFosSiding, existingEditTextSalesOfficerRemarks;
    Button existingUniqueLeadIDButton, existingSegmentButton, existingLeadSourceButton, existingProductPackagingButton, existingModeOfPaymentButton, existingCreditTermsButton, existingAacBlockRequiredNotButton,
            existingCategoryTypeOfConstructionButton, existingLeadStatusButton, existingNextVisitDateButton, existingRequirementTypeButton, existingExWorksButton, existingAssignedToButton, existingRequirementTimingButton,
            existingShareLeadImageButton, existingLeadActionButton;
    TextView existingTextUniqueLeadID, existingSegmentButtonSelectText, existingLeadSourceButtonSelectText, existingProductPackagingButtonSelectText, existingModeOfPaymentButtonSelectText, existingCreditTermsButtonSelectText,
            existingAacBlockRequiredNotButtonSelectText, existingCategoryTypeOfConstructionButtonSelectText, existingLeadStatusButtonSelectText, existingNextVisitDateButtonSelectText,
            existingRequirementTypeButtonSelectText, existingExWorksButtonSelectText, existingAssignedToButtonSelectText, existingRequirementTimingButtonSelectText;
    Button submitButton;

    // HOS action Popup
    LinearLayout hosActionPopup;
    ImageView imageCancel;
    RadioGroup leadStatusRadioGroup;
    RadioButton yesRadioButton, noRadioButton, holdRadioButton, revisionRadioButton;
    LinearLayout hosQuoLayout, hosRemakrsLayout;
    EditText editTextSendingQuotaion, editTextHOSRemarks;
    Button hosSubmitButton;

    ProgressDialog progressDialog;

    AceDnsDatabase mAceDnsDatabase;

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

    String leadId = "";
    String officerFor = "";
    String selectEmpCode = "", dateString = "", timeString = "", latitude = "", longitude = "";
    String soldToPartyName = "", soldToPartyAddress = "", soldToPartyCode = "", soldToPartyState = "", soldToPartyDistricts = "";
    String shipToPartyName = "", shipToPartyAddress = "", shipToPartyCode = "", shipToPartyState = "", shipToPartyDistricts = "";
    String segment = "", leadSource = "", productPackaging = "";
    String potentialOfSite = "", quotationQuantity = "", currentBrandUsed = "", expectedRatePerBag = "", currentPriceStarRsPerBag = "", currentPriceCompetitorRsPerBag = "";
    String contactPersonName = "", contactPersonDesignation = "", contactPersonNumber = "", contactPersonMail = "";
    String modeOfPayment = "", creditTerms = "", aacBlockRequiredStatus = "", constructionType = "", leadStatus = "", nextVisitDate = "", requirementType = "";
    String exWorks = "", fosText = "";
    String salesOfficerRemarks = "";
    String assignedTo = "", requirementTiming = "";

    String TYPE_OF_USER = "";
    String TYPE_OF_LEAD = "";
    String LEAD_STATUS_UPDATE_FROM_HOS = "";

    boolean isSalesOfficerNameSelect = false;
    boolean isButtonPressForExistingLeadButton = false;

    String filterFormDate = "", filterToDate = "", filterStatus = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_generation);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

        mContext = LeadGenerationActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dataSetSegmentList();
        init();
    }

    @Override
    public void onClick(View view) {
        // Back Button
        if (view == backButton) {
            finish();
        }

        /*
         * Existing Lead Generate
         */
        // Popup view no action click
        if (view == hosActionPopup) {
            Log.d("TAG", "onClick: ");
        }
        // Popup close Button
        if (view == imageCancel) {
            runOnUiThread(() -> hosActionPopup.setVisibility(View.GONE));
        }
        // Popup Submit Button
        if (view == hosSubmitButton) {
            checkHosSubmitDataAndUpload();
        }

        newLeadOnClickFunction(view);
        existingLeadOnClickFunction(view);
    }

    @Override
    public void onCheckedChanged(RadioGroup radioGroup, int i) {
        if (i == R.id.newLeadRadioButton) {
            TYPE_OF_LEAD = "new";
            runOnUiThread(() -> {
                leadGenerationLayout.setVisibility(View.VISIBLE);
                existingGenerationLayout.setVisibility(View.GONE);
                clearAllDataFromExistingLead();
                cleanAllDataFromNewLeadGeneration();
            });
        }
        if (i == R.id.existingLeadRadioButton) {
            TYPE_OF_LEAD = "existing";
            runOnUiThread(() -> {
                leadGenerationLayout.setVisibility(View.GONE);
                existingGenerationLayout.setVisibility(View.VISIBLE);
                clearAllDataFromExistingLead();
                cleanAllDataFromNewLeadGeneration();
            });
        }

        if (i == R.id.selfRadioButton) {
            runOnUiThread(() -> {
                isSalesOfficerNameSelect = true;
                cleanAllDataFromNewLeadGeneration();
                officerFor = "SELF";
                selectEmpCode = Constants.employeeDetailObject.getEmpCode();
                otherSalesOfficeNameButtonLayout.setVisibility(View.GONE);
                salesOfficerDetailsLayout.setVisibility(View.VISIBLE);

                editTextSalesOfficerName.setText(Constants.employeeDetailObject.getEmpName());
                editTextDateStamp.setText(dateString);
                editTextTimeStamp.setText(timeString);
                editTextLatitude.setText(latitude);
                editTextLongitude.setText(longitude);

                _DOWNLOAD_sold_to_party(Constants.employeeDetailObject.getEmpCode());
            });
        }
        if (i == R.id.othersRadioButton) {
            runOnUiThread(() -> {
                isSalesOfficerNameSelect = false;
                cleanAllDataFromNewLeadGeneration();
                officerFor = "OTHER";
                selectEmpCode = "";
                otherSalesOfficeNameButtonLayout.setVisibility(View.VISIBLE);
                salesOfficerDetailsLayout.setVisibility(View.VISIBLE);

                editTextDateStamp.setText(dateString);
                editTextTimeStamp.setText(timeString);
                editTextLatitude.setText(latitude);
                editTextLongitude.setText(longitude);
                editTextSalesOfficerName.setText("");

                _DOWNLOAD_emp_master();
            });
        }

        if (i == R.id.yesRadioButton) {
            LEAD_STATUS_UPDATE_FROM_HOS = "YES";
            runOnUiThread(() -> {
                hosQuoLayout.setVisibility(View.VISIBLE);
                hosRemakrsLayout.setVisibility(View.VISIBLE);
            });
        }
        if (i == R.id.noRadioButton) {
            LEAD_STATUS_UPDATE_FROM_HOS = "NO";
            runOnUiThread(() -> {
                hosQuoLayout.setVisibility(View.GONE);
                hosRemakrsLayout.setVisibility(View.VISIBLE);
            });
        }
        if (i == R.id.holdRadioButton) {
            LEAD_STATUS_UPDATE_FROM_HOS = "HOLD";
            runOnUiThread(() -> {
                hosQuoLayout.setVisibility(View.GONE);
                hosRemakrsLayout.setVisibility(View.VISIBLE);
            });
        }
        if (i == R.id.revisionRadioButton) {
            LEAD_STATUS_UPDATE_FROM_HOS = "REVISION";
            runOnUiThread(() -> {
                hosQuoLayout.setVisibility(View.GONE);
                hosRemakrsLayout.setVisibility(View.VISIBLE);
            });
        }
    }

    private void newLeadOnClickFunction(View view) {
        /*
         * New Lead Generate
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
        // Lead Source Select Button
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
        // Lead Status Select Button
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
        //submit
        if (view == submitButton) {
            if (!TYPE_OF_USER.equalsIgnoreCase("hos")) {
                if (TYPE_OF_LEAD.equalsIgnoreCase("new")) {
                    checkData();
                } else {
                    checkDataForUpdate();
                }
            }
        }
    }

    private void existingLeadOnClickFunction(View view) {
        /*
         * Existing Lead Generate
         */
        // Existing Lead Select Button
        if (view == existingUniqueLeadIDButton) {
            show_lead_list_dialog(existingLeadList, "Please select Existing Lead");
        }
        // Segment Select Button
        if (view == existingSegmentButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(segmentList, "segment");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Lead Source Select Button
        if (view == existingLeadSourceButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(leadSourceList, "lead_source");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Product Package Select Button
        if (view == existingProductPackagingButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(productList, "product");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Mode of Payment Select Button
        if (view == existingModeOfPaymentButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(modeOfPaymentList, "mode_of_payment");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Credit Terms Select Button
        if (view == existingCreditTermsButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(creditTermsList, "credit_terms");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // AAC block is Required Select Button
        if (view == existingAacBlockRequiredNotButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(aacBlockRequiredCheckList, "aac_block_required_check");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Category Type of Construction Select Button
        if (view == existingCategoryTypeOfConstructionButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(constructionTypeList, "construction_type");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Lead Status Select Button
        if (view == existingLeadStatusButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(leadStatusList, "lead_status");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Next Visit Date Select Button
        if (view == existingNextVisitDateButton) {
            if (isButtonPressForExistingLeadButton) {
                nextVisitDatePicker1();
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Requirement Type Select Button
        if (view == existingRequirementTypeButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(requirementTypeList, "requirement_type");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Ex Works Select Button
        if (view == existingExWorksButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(exWorkList, "ex_work");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Requirement Timing Select Button
        if (view == existingRequirementTimingButton) {
            if (isButtonPressForExistingLeadButton) {
                show_list_data_dialog1(requirementTimingList, "requirement_timing");
            } else {
                Toast.makeText(this, "You can't able to update now.", Toast.LENGTH_LONG).show();
            }
        }
        // Lead Action Button
        if (view == existingLeadActionButton) {
            runOnUiThread(() -> hosActionPopup.setVisibility(View.VISIBLE));
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        leadTypeRadioGroupLayout = findViewById(R.id.leadTypeRadioGroupLayout);
        leadTypeRadioGroupLayout.setVisibility(View.GONE);

        leadTypeRadioGroup = findViewById(R.id.leadTypeRadioGroup);
        leadTypeRadioGroup.setOnCheckedChangeListener(this);
        newLeadRadioButton = findViewById(R.id.newLeadRadioButton);
        newLeadRadioButton.setText(Html.fromHtml("New Lead"));
        existingLeadRadioButton = findViewById(R.id.existingLeadRadioButton);
        existingLeadRadioButton.setText(Html.fromHtml("Existing Lead"));

        submitButton = findViewById(R.id.submitButton);
        submitButton.setOnClickListener(this);

        initLayout();
        initHOS();
    }

    private void initLayout() {
        // ***New Lead Generation
        leadGenerationLayout = findViewById(R.id.leadGenerationLayout);
        leadGenerationLayout.setVisibility(View.GONE);
        // Sales Officer Details
        salesOfficerDetailsLayout = findViewById(R.id.salesOfficerDetailsLayout);
        salesOfficerDetailsLayout.setVisibility(View.GONE);
        // Other Sales Office Name
        otherSalesOfficeNameButtonLayout = findViewById(R.id.otherSalesOfficeNameButtonLayout);
        otherSalesOfficeNameButtonLayout.setVisibility(View.GONE);
        // Ex Works Layout
        exWorksButtonLayout = findViewById(R.id.exWorksButtonLayout);
        exWorksButtonLayout.setVisibility(View.GONE);
        // FOS Layout
        fosEditTextLayout = findViewById(R.id.fosEditTextLayout);
        fosEditTextLayout.setVisibility(View.GONE);
        // init All other layout
        initLeadGeneration();

        // ***Existing Lead Generation
        existingGenerationLayout = findViewById(R.id.existingGenerationLayout);
        existingGenerationLayout.setVisibility(View.GONE);
        // Ex Works Layout
        existingExWorksButtonLayout = findViewById(R.id.existingExWorksButtonLayout);
        existingExWorksButtonLayout.setVisibility(View.GONE);
        // FOS Layout
        existingFosEditTextLayout = findViewById(R.id.existingFosEditTextLayout);
        existingFosEditTextLayout.setVisibility(View.GONE);
        // init All other layout
        initExistingGeneration();

        new TRANS_EmployeeDetails_AsyncTask(mContext).execute();
    }

    //Popup init HOS
    private void initHOS() {
        hosActionPopup = findViewById(R.id.hosActionPopup);
        imageCancel = findViewById(R.id.imageCancel);
        leadStatusRadioGroup = findViewById(R.id.leadStatusRadioGroup);
        yesRadioButton = findViewById(R.id.yesRadioButton);
        noRadioButton = findViewById(R.id.noRadioButton);
        holdRadioButton = findViewById(R.id.holdRadioButton);
        revisionRadioButton = findViewById(R.id.revisionRadioButton);
        hosQuoLayout = findViewById(R.id.hosQuoLayout);
        hosRemakrsLayout = findViewById(R.id.hosRemakrsLayout);
        editTextSendingQuotaion = findViewById(R.id.editTextSendingQuotaion);
        editTextHOSRemarks = findViewById(R.id.editTextHOSRemarks);
        hosSubmitButton = findViewById(R.id.hosSubmitButton);

        leadStatusRadioGroup.setOnCheckedChangeListener(this);
        hosActionPopup.setVisibility(View.GONE);

        hosActionPopup.setOnClickListener(this);
        imageCancel.setOnClickListener(this);
        hosSubmitButton.setOnClickListener(this);
    }

    // New Lead Generation
    private void initLeadGeneration() {
        // Unique Lead Id
        textUniqueLeadID = findViewById(R.id.textUniqueLeadID);
        textUniqueLeadID.setText(Html.fromHtml("Unique Lead ID"));
        editTextUniqueLeadID = findViewById(R.id.editTextUniqueLeadID);
        editTextUniqueLeadID.setHint(Html.fromHtml("Auto Generated"));
        editTextUniqueLeadID.setEnabled(false);

        // Generate For Radio Button
        textGenerateFor = findViewById(R.id.textGenerateFor);
        textGenerateFor.setText(Html.fromHtml("Self / Others <font color='#FF0000'>*</font>"));
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
        textSalesOfficerName.setText(Html.fromHtml("Sales Officer Name <font color='#FF0000'>*</font>"));
        editTextSalesOfficerName = findViewById(R.id.editTextSalesOfficerName);
        editTextSalesOfficerName.setHint(Html.fromHtml("Sales Officer Name (Mandatory)"));
        editTextSalesOfficerName.setEnabled(false);

        // Date Stamp
        textDateStamp = findViewById(R.id.textDateStamp);
        textDateStamp.setText(Html.fromHtml("Date Stamp <font color='#FF0000'>*</font>"));
        editTextDateStamp = findViewById(R.id.editTextDateStamp);
        editTextDateStamp.setHint(Html.fromHtml("Auto Fetch"));
        editTextDateStamp.setEnabled(false);

        // Time Stamp
        textTimeStamp = findViewById(R.id.textTimeStamp);
        textTimeStamp.setText(Html.fromHtml("Time Stamp <font color='#FF0000'>*</font>"));
        editTextTimeStamp = findViewById(R.id.editTextTimeStamp);
        editTextTimeStamp.setHint(Html.fromHtml("Auto Fetch"));
        editTextTimeStamp.setEnabled(false);

        // Latitude
        textLatitude = findViewById(R.id.textLatitude);
        textLatitude.setText(Html.fromHtml("Latitude <font color='#FF0000'>*</font>"));
        editTextLatitude = findViewById(R.id.editTextLatitude);
        editTextLatitude.setHint(Html.fromHtml("Auto Fetch"));
        editTextLatitude.setEnabled(false);

        // Longitude
        textLongitude = findViewById(R.id.textLongitude);
        textLongitude.setText(Html.fromHtml("Longitude <font color='#FF0000'>*</font>"));
        editTextLongitude = findViewById(R.id.editTextLongitude);
        editTextLongitude.setHint(Html.fromHtml("Auto Fetch"));
        editTextLongitude.setEnabled(false);

        // Sold to Party Name Button
        soldToPartyNameButton = findViewById(R.id.soldToPartyNameButton);
        soldToPartyNameButton.setText(Html.fromHtml("Sold to Party Name"));
        soldToPartyNameButton.setOnClickListener(this);


        // Sold to Party Name
        textSoldToPartyName = findViewById(R.id.textSoldToPartyName);
        textSoldToPartyName.setText(Html.fromHtml("Sold to Party Name <font color='#FF0000'>*</font>"));
        editTextSoldToPartyName = findViewById(R.id.editTextSoldToPartyName);
        editTextSoldToPartyName.setHint(Html.fromHtml("Sold to Party Name (Mandatory)"));

        // Sold to Party Code
        textSoldToPartyCode = findViewById(R.id.textSoldToPartyCode);
        textSoldToPartyCode.setText(Html.fromHtml("Sold to Party Code"));
        editTextSoldToPartyCode = findViewById(R.id.editTextSoldToPartyCode);
        editTextSoldToPartyCode.setHint(Html.fromHtml("Auto Generated"));
        editTextSoldToPartyCode.setEnabled(false);

        // Sold to Party Address
        textSoldToPartyAddress = findViewById(R.id.textSoldToPartyAddress);
        textSoldToPartyAddress.setText(Html.fromHtml("Sold to Party Address <font color='#FF0000'>*</font>"));
        editTextSoldToPartyAddress = findViewById(R.id.editTextSoldToPartyAddress);
        editTextSoldToPartyAddress.setHint(Html.fromHtml("Sold to Party Address (Mandatory)"));

        // Sold to party State Button
        soldToPartyStateButton = findViewById(R.id.soldToPartyStateButton);
        soldToPartyStateButton.setText(Html.fromHtml("Sold to Party State <font color='#FF0000'>*</font>"));
        soldToPartyStateButton.setOnClickListener(this);
        soldToPartyStateButtonSelectText = findViewById(R.id.soldToPartyStateButtonSelectText);
        soldToPartyStateButtonSelectText.setVisibility(View.GONE);

        // Sold to Party Districts Button
        soldToPartyDistrictsButton = findViewById(R.id.soldToPartyDistrictsButton);
        soldToPartyDistrictsButton.setText(Html.fromHtml("Sold to Party Districts <font color='#FF0000'>*</font>"));
        soldToPartyDistrictsButton.setOnClickListener(this);
        soldToPartyDistrictsButtonSelectText = findViewById(R.id.soldToPartyDistrictsButtonSelectText);
        soldToPartyDistrictsButtonSelectText.setVisibility(View.GONE);

        // Ship to Party Name Button
        shipToPartyNameButton = findViewById(R.id.shipToPartyNameButton);
        shipToPartyNameButton.setText(Html.fromHtml("Ship to Party Name"));
        shipToPartyNameButton.setOnClickListener(this);

        // Ship to Party Name
        textShipToPartyName = findViewById(R.id.textShipToPartyName);
        textShipToPartyName.setText(Html.fromHtml("Ship to Party Name <font color='#FF0000'>*</font>"));
        editTextShipToPartyName = findViewById(R.id.editTextShipToPartyName);
        editTextShipToPartyName.setHint(Html.fromHtml("Ship to Party Name (Mandatory)"));

        // Ship to Party Code
        textShipToPartyCode = findViewById(R.id.textShipToPartyCode);
        textShipToPartyCode.setText(Html.fromHtml("Ship to Party Code"));
        editTextShipToPartyCode = findViewById(R.id.editTextShipToPartyCode);
        editTextShipToPartyCode.setHint(Html.fromHtml("Auto Generated"));
        editTextShipToPartyCode.setEnabled(false);

        // Ship to Party Address
        textShipToPartyAddress = findViewById(R.id.textShipToPartyAddress);
        textShipToPartyAddress.setText(Html.fromHtml("Ship to Party Address <font color='#FF0000'>*</font>"));
        editTextShipToPartyAddress = findViewById(R.id.editTextShipToPartyAddress);
        editTextShipToPartyAddress.setHint(Html.fromHtml("Ship to Party Address (Mandatory)"));

        // Ship to Party State Button
        shipToPartyStateButton = findViewById(R.id.shipToPartyStateButton);
        shipToPartyStateButton.setText(Html.fromHtml("Ship to Party State <font color='#FF0000'>*</font>"));
        shipToPartyStateButton.setOnClickListener(this);
        shipToPartyStateButtonSelectText = findViewById(R.id.shipToPartyStateButtonSelectText);
        shipToPartyStateButtonSelectText.setVisibility(View.GONE);

        // Ship to Party Districts Button
        shipToPartyDistrictsButton = findViewById(R.id.shipToPartyDistrictsButton);
        shipToPartyDistrictsButton.setText(Html.fromHtml("Ship to Party Districts <font color='#FF0000'>*</font>"));
        shipToPartyDistrictsButton.setOnClickListener(this);
        shipToPartyDistrictsButtonSelectText = findViewById(R.id.shipToPartyDistrictsButtonSelectText);
        shipToPartyDistrictsButtonSelectText.setVisibility(View.GONE);

        // Segment Button
        segmentButton = findViewById(R.id.segmentButton);
        segmentButton.setText(Html.fromHtml("Segment <font color='#FF0000'>*</font>"));
        segmentButton.setOnClickListener(this);
        segmentButtonSelectText = findViewById(R.id.segmentButtonSelectText);
        segmentButtonSelectText.setVisibility(View.GONE);

        // Lead Source Button
        leadSourceButton = findViewById(R.id.leadSourceButton);
        leadSourceButton.setText(Html.fromHtml("Lead Source <font color='#FF0000'>*</font>"));
        leadSourceButton.setOnClickListener(this);
        leadSourceButtonSelectText = findViewById(R.id.leadSourceButtonSelectText);
        leadSourceButtonSelectText.setVisibility(View.GONE);

        // Product List Button
        productPackagingButton = findViewById(R.id.productPackagingButton);
        productPackagingButton.setText(Html.fromHtml("Product + Packaging"));
        productPackagingButton.setOnClickListener(this);
        productPackagingButtonSelectText = findViewById(R.id.productPackagingButtonSelectText);
        productPackagingButtonSelectText.setVisibility(View.GONE);

        // Total Potential of Site (MT)
        textTotalPotentialOfSite = findViewById(R.id.textTotalPotentialOfSite);
        textTotalPotentialOfSite.setText(Html.fromHtml("Total Potential of Site (MT)"));
        editTextTotalPotentialOfSite = findViewById(R.id.editTextTotalPotentialOfSite);
        editTextTotalPotentialOfSite.setHint(Html.fromHtml("Total Qty Required (MT) (Mandatory)"));
        editTextTotalPotentialOfSite.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Quotation Quantity (MT)
        textQuotationQuantity = findViewById(R.id.textQuotationQuantity);
        textQuotationQuantity.setText(Html.fromHtml("Quotation Quantity (MT)"));
        editTextQuotationQuantity = findViewById(R.id.editTextQuotationQuantity);
        editTextQuotationQuantity.setHint(Html.fromHtml("Monthly Qty Required (MT) (Mandatory)"));
        editTextQuotationQuantity.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Current Brand Used
        textCurrentBrandUsed = findViewById(R.id.textCurrentBrandUsed);
        textCurrentBrandUsed.setText(Html.fromHtml("Current Brand Used <font color='#FF0000'>*</font>"));
        editTextCurrentBrandUsed = findViewById(R.id.editTextCurrentBrandUsed);
        editTextCurrentBrandUsed.setHint(Html.fromHtml("Current Brand Used (Mandatory)"));

        // Expected Rate per Bag
        textExpectedRatePerBag = findViewById(R.id.textExpectedRatePerBag);
        textExpectedRatePerBag.setText(Html.fromHtml("Expected Rate Per Bag <font color='#FF0000'>*</font>"));
        editTextExpectedRatePerBag = findViewById(R.id.editTextExpectedRatePerBag);
        editTextExpectedRatePerBag.setHint(Html.fromHtml("Expected Rate Per Bag (Mandatory)"));
        editTextExpectedRatePerBag.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Current Price Star Rs per Bag
        textCurrentPriceStarRsPerBag = findViewById(R.id.textCurrentPriceStarRsPerBag);
        textCurrentPriceStarRsPerBag.setText(Html.fromHtml("Current Price Star Rs. Per Bag <font color='#FF0000'>*</font>"));
        editTextCurrentPriceStarRsPerBag = findViewById(R.id.editTextCurrentPriceStarRsPerBag);
        editTextCurrentPriceStarRsPerBag.setHint(Html.fromHtml("Current Price Star (Mandatory)"));
        editTextCurrentPriceStarRsPerBag.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Current Price Competitor Rs per Bag
        textCurrentPriceCompetitorRsPerBag = findViewById(R.id.textCurrentPriceCompetitorRsPerBag);
        textCurrentPriceCompetitorRsPerBag.setText(Html.fromHtml("Current Price Competitor Rs. Per Bag <font color='#FF0000'>*</font>"));
        editTextCurrentPriceCompetitorRsPerBag = findViewById(R.id.editTextCurrentPriceCompetitorRsPerBag);
        editTextCurrentPriceCompetitorRsPerBag.setHint(Html.fromHtml("Current Price Competitor (Mandatory)"));
        editTextCurrentPriceCompetitorRsPerBag.setInputType(InputType.TYPE_CLASS_NUMBER);

        // Contact Person Name
        textContactPersonName = findViewById(R.id.textContactPersonName);
        textContactPersonName.setText(Html.fromHtml("Contact Person Name <font color='#FF0000'>*</font>"));
        editTextContactPersonName = findViewById(R.id.editTextContactPersonName);
        editTextContactPersonName.setHint(Html.fromHtml("Contact Person Name (Mandatory)"));

        // Designation
        textDesignation = findViewById(R.id.textDesignation);
        textDesignation.setText(Html.fromHtml("Designation <font color='#FF0000'>*</font>"));
        editTextDesignation = findViewById(R.id.editTextDesignation);
        editTextDesignation.setHint(Html.fromHtml("Designation (Mandatory)"));

        // Contact Number
        textContactNumber = findViewById(R.id.textContactNumber);
        textContactNumber.setText(Html.fromHtml("Contact Number <font color='#FF0000'>*</font>"));
        editTextContactNumber = findViewById(R.id.editTextContactNumber);
        editTextContactNumber.setHint(Html.fromHtml("Contact Number (Mandatory)"));
        editTextContactNumber.setInputType(InputType.TYPE_CLASS_PHONE);

        // Mail Id
        textMailId = findViewById(R.id.textMailId);
        textMailId.setText(Html.fromHtml("Mail Id <font color='#FF0000'>*</font>"));
        editTextMailId = findViewById(R.id.editTextMailId);
        editTextMailId.setHint(Html.fromHtml("Mail Id (Mandatory)"));

        // Mode of Payment Button
        modeOfPaymentButton = findViewById(R.id.modeOfPaymentButton);
        modeOfPaymentButton.setText(Html.fromHtml("Mode Of Payment"));
        modeOfPaymentButton.setOnClickListener(this);
        modeOfPaymentButtonSelectText = findViewById(R.id.modeOfPaymentButtonSelectText);
        modeOfPaymentButtonSelectText.setVisibility(View.GONE);

        // Credit Terms Button
        creditTermsButton = findViewById(R.id.creditTermsButton);
        creditTermsButton.setText(Html.fromHtml("Credit Terms"));
        creditTermsButton.setOnClickListener(this);
        creditTermsButtonSelectText = findViewById(R.id.creditTermsButtonSelectText);
        creditTermsButtonSelectText.setVisibility(View.GONE);

        // AAC Block is Required ot Not Button
        aacBlockRequiredNotButton = findViewById(R.id.aacBlockRequiredNotButton);
        aacBlockRequiredNotButton.setText(Html.fromHtml("AAC Block is Required or Not"));
        aacBlockRequiredNotButton.setOnClickListener(this);
        aacBlockRequiredNotButtonSelectText = findViewById(R.id.aacBlockRequiredNotButtonSelectText);
        aacBlockRequiredNotButtonSelectText.setVisibility(View.GONE);

        // Category Type of Construction Button
        categoryTypeOfConstructionButton = findViewById(R.id.categoryTypeOfConstructionButton);
        categoryTypeOfConstructionButton.setText(Html.fromHtml("Category Type of Construction <font color='#FF0000'>*</font>"));
        categoryTypeOfConstructionButton.setOnClickListener(this);
        categoryTypeOfConstructionButtonSelectText = findViewById(R.id.categoryTypeOfConstructionButtonSelectText);
        categoryTypeOfConstructionButtonSelectText.setVisibility(View.GONE);

        // Lead status Button
        leadStatusButton = findViewById(R.id.leadStatusButton);
        leadStatusButton.setText(Html.fromHtml("Lead Status <font color='#FF0000'>*</font>"));
        leadStatusButton.setOnClickListener(this);
        leadStatusButtonSelectText = findViewById(R.id.leadStatusButtonSelectText);
        leadStatusButtonSelectText.setVisibility(View.GONE);

        // Next Visit Date Button
        nextVisitDateButton = findViewById(R.id.nextVisitDateButton);
        nextVisitDateButton.setText(Html.fromHtml("Next Visit Date <font color='#FF0000'>*</font>"));
        nextVisitDateButton.setOnClickListener(this);
        nextVisitDateButtonSelectText = findViewById(R.id.nextVisitDateButtonSelectText);
        nextVisitDateButtonSelectText.setVisibility(View.GONE);

        // Requirement Type Button
        requirementTypeButton = findViewById(R.id.requirementTypeButton);
        requirementTypeButton.setText(Html.fromHtml("Requirement Type <font color='#FF0000'>*</font>"));
        requirementTypeButton.setOnClickListener(this);
        requirementTypeButtonSelectText = findViewById(R.id.requirementTypeButtonSelectText);
        requirementTypeButtonSelectText.setVisibility(View.GONE);

        // Ex Work Button
        exWorksButton = findViewById(R.id.exWorksButton);
        exWorksButton.setText(Html.fromHtml("Ex. Works <font color='#FF0000'>*</font>"));
        exWorksButton.setOnClickListener(this);
        exWorksButtonSelectText = findViewById(R.id.exWorksButtonSelectText);
        exWorksButtonSelectText.setVisibility(View.GONE);

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

        // Assigned To Button
        assignedToButton = findViewById(R.id.assignedToButton);
        assignedToButton.setText(Html.fromHtml("Assigned To <font color='#FF0000'>*</font>"));
        assignedToButton.setOnClickListener(this);
        assignedToButtonSelectText = findViewById(R.id.assignedToButtonSelectText);
        assignedToButtonSelectText.setVisibility(View.GONE);

        // Requirement Timing Button
        requirementTimingButton = findViewById(R.id.requirementTimingButton);
        requirementTimingButton.setText(Html.fromHtml("Requirement Timing <font color='#FF0000'>*</font>"));
        requirementTimingButton.setOnClickListener(this);
        requirementTimingButtonSelectText = findViewById(R.id.requirementTimingButtonSelectText);
        requirementTimingButtonSelectText.setVisibility(View.GONE);

        // Image Pick Button
        shareLeadImageButton = findViewById(R.id.shareLeadImageButton);
        shareLeadImageButton.setText(Html.fromHtml("Share Lead / Site Details (Image)"));
        shareLeadImageButton.setOnClickListener(this);

        // Lead Action Button
        leadActionButton = findViewById(R.id.leadActionButton);
        leadActionButton.setText(Html.fromHtml("Lead Action <font color='#FF0000'>*</font>"));
        leadActionButton.setOnClickListener(this);

        // Set Default Value
        getDefaultData();
    }

    @SuppressLint("SimpleDateFormat")
    private void getDefaultData() {
        leadId = "L" + Constants.employeeDetailObject.getEmpCode().replaceAll("^[A-Z]", "") + new SimpleDateFormat("yyMMddHHmm").format(new Date());

        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);
        dateString = year + "-" + (month+1) + "-" + day;

        SimpleDateFormat sdf = new SimpleDateFormat("HH:mm:ss");
        timeString = sdf.format(new Date());

        LocationTracker locationTracker = new LocationTracker(this);
        locationTracker.checkLocationUpdateSharing();
        new Handler().postDelayed(() -> {
            latitude = Constants.currentLat;
            longitude = Constants.currentLong;
            setData();
        }, 2000);
    }

    private void setData() {
        editTextUniqueLeadID.setText(leadId);
        editTextDateStamp.setText(dateString);
        editTextTimeStamp.setText(timeString);
        editTextLatitude.setText(latitude);
        editTextLongitude.setText(longitude);
    }

    // Existing Lead Generation
    private void initExistingGeneration() {
        // Unique Lead Id
        existingTextUniqueLeadID = findViewById(R.id.existingTextUniqueLeadID);
        existingTextUniqueLeadID.setVisibility(View.GONE);
        existingUniqueLeadIDButton = findViewById(R.id.existingUniqueLeadIDButton);
        existingUniqueLeadIDButton.setText(Html.fromHtml("Search Lead id"));
        existingUniqueLeadIDButton.setOnClickListener(this);

        // Sales Officer Name
        existingTextSalesOfficerName = findViewById(R.id.existingTextSalesOfficerName);
        existingTextSalesOfficerName.setText(Html.fromHtml("Sales Officer Name <font color='#FF0000'>*</font>"));
        existingEditTextSalesOfficerName = findViewById(R.id.existingEditTextSalesOfficerName);
        existingEditTextSalesOfficerName.setEnabled(false);

        // Date Stamp
        existingTextDateStamp = findViewById(R.id.existingTextDateStamp);
        existingTextDateStamp.setText(Html.fromHtml("Date Stamp <font color='#FF0000'>*</font>"));
        existingEditTextDateStamp = findViewById(R.id.existingEditTextDateStamp);
        existingEditTextDateStamp.setEnabled(false);

        // Time Stamp
        existingTextTimeStamp = findViewById(R.id.existingTextTimeStamp);
        existingTextTimeStamp.setText(Html.fromHtml("Time Stamp <font color='#FF0000'>*</font>"));
        existingEditTextTimeStamp = findViewById(R.id.existingEditTextTimeStamp);
        existingEditTextTimeStamp.setEnabled(false);

        // Latitude
        existingTextLatitude = findViewById(R.id.existingTextLatitude);
        existingTextLatitude.setText(Html.fromHtml("Latitude <font color='#FF0000'>*</font>"));
        existingEditTextLatitude = findViewById(R.id.existingEditTextLatitude);
        existingEditTextLatitude.setEnabled(false);

        // Longitude
        existingTextLongitude = findViewById(R.id.existingTextLongitude);
        existingTextLongitude.setText(Html.fromHtml("Longitude <font color='#FF0000'>*</font>"));
        existingEditTextLongitude = findViewById(R.id.existingEditTextLongitude);
        existingEditTextLongitude.setEnabled(false);

        // Sold to Party Name
        existingTextSoldToPartyName = findViewById(R.id.existingTextSoldToPartyName);
        existingTextSoldToPartyName.setText(Html.fromHtml("Sold to Party Name <font color='#FF0000'>*</font>"));
        existingEditTextSoldToPartyName = findViewById(R.id.existingEditTextSoldToPartyName);
        existingEditTextSoldToPartyName.setEnabled(false);

        // Sold to Party Code
        existingTextSoldToPartyCode = findViewById(R.id.existingTextSoldToPartyCode);
        existingTextSoldToPartyCode.setText(Html.fromHtml("Sold to Party Code"));
        existingEditTextSoldToPartyCode = findViewById(R.id.existingEditTextSoldToPartyCode);
        existingEditTextSoldToPartyCode.setEnabled(false);

        // Sold to Party Address
        existingTextSoldToPartyAddress = findViewById(R.id.existingTextSoldToPartyAddress);
        existingTextSoldToPartyAddress.setText(Html.fromHtml("Sold to Party Address <font color='#FF0000'>*</font>"));
        existingEditTextSoldToPartyAddress = findViewById(R.id.existingEditTextSoldToPartyAddress);
        existingEditTextSoldToPartyAddress.setEnabled(false);

        // Sold to Party State
        existingTextSoldToPartyState = findViewById(R.id.existingTextSoldToPartyState);
        existingTextSoldToPartyState.setText(Html.fromHtml("Sold to Party State <font color='#FF0000'>*</font>"));
        existingEditTextSoldToPartyState = findViewById(R.id.existingEditTextSoldToPartyState);
        existingEditTextSoldToPartyState.setEnabled(false);

        // Sold to Party Districts
        existingTextSoldToPartyDistricts = findViewById(R.id.existingTextSoldToPartyDistricts);
        existingTextSoldToPartyDistricts.setText(Html.fromHtml("Sold to Party Districts <font color='#FF0000'>*</font>"));
        existingEditTextSoldToPartyDistricts = findViewById(R.id.existingEditTextSoldToPartyDistricts);
        existingEditTextSoldToPartyDistricts.setEnabled(false);

        // Ship to Party Name
        existingTextShipToPartyName = findViewById(R.id.existingTextShipToPartyName);
        existingTextShipToPartyName.setText(Html.fromHtml("Ship to Party Name <font color='#FF0000'>*</font>"));
        existingEditTextShipToPartyName = findViewById(R.id.existingEditTextShipToPartyName);
        existingEditTextShipToPartyName.setEnabled(false);

        // Ship to Party Code
        existingTextShipToPartyCode = findViewById(R.id.existingTextShipToPartyCode);
        existingTextShipToPartyCode.setText(Html.fromHtml("Ship to Party Code"));
        existingEditTextShipToPartyCode = findViewById(R.id.existingEditTextShipToPartyCode);
        existingEditTextShipToPartyCode.setEnabled(false);

        // Ship to Party Address
        existingTextShipToPartyAddress = findViewById(R.id.existingTextShipToPartyAddress);
        existingTextShipToPartyAddress.setText(Html.fromHtml("Ship to Party Address <font color='#FF0000'>*</font>"));
        existingEditTextShipToPartyAddress = findViewById(R.id.existingEditTextShipToPartyAddress);
        existingEditTextShipToPartyAddress.setEnabled(false);

        // Ship to Party State
        existingTextShipToPartyState = findViewById(R.id.existingTextShipToPartyState);
        existingTextShipToPartyState.setText(Html.fromHtml("Ship to Party State <font color='#FF0000'>*</font>"));
        existingEditTextShipToPartyState = findViewById(R.id.existingEditTextShipToPartyState);
        existingEditTextShipToPartyState.setEnabled(false);

        // Ship to Party Districts
        existingTextShipToPartyDistricts = findViewById(R.id.existingTextShipToPartyDistricts);
        existingTextShipToPartyDistricts.setText(Html.fromHtml("Ship to Party Districts <font color='#FF0000'>*</font>"));
        existingEditTextShipToPartyDistricts = findViewById(R.id.existingEditTextShipToPartyDistricts);
        existingEditTextShipToPartyDistricts.setEnabled(false);

        // Segment Button
        existingSegmentButton = findViewById(R.id.existingSegmentButton);
        existingSegmentButton.setText(Html.fromHtml("Segment <font color='#FF0000'>*</font>"));
        existingSegmentButton.setOnClickListener(this);
        existingSegmentButtonSelectText = findViewById(R.id.existingSegmentButtonSelectText);
        existingSegmentButtonSelectText.setVisibility(View.GONE);

        // Lead Source Button
        existingLeadSourceButton = findViewById(R.id.existingLeadSourceButton);
        existingLeadSourceButton.setText(Html.fromHtml("Lead Source <font color='#FF0000'>*</font>"));
        existingLeadSourceButton.setOnClickListener(this);
        existingLeadSourceButtonSelectText = findViewById(R.id.existingLeadSourceButtonSelectText);
        existingLeadSourceButtonSelectText.setVisibility(View.GONE);

        // Product Packaging Button
        existingProductPackagingButton = findViewById(R.id.existingProductPackagingButton);
        existingProductPackagingButton.setText(Html.fromHtml("Product + Packaging"));
        existingProductPackagingButton.setOnClickListener(this);
        existingProductPackagingButtonSelectText = findViewById(R.id.existingProductPackagingButtonSelectText);
        existingProductPackagingButtonSelectText.setVisibility(View.GONE);

        // Total Potential of Site
        existingTextTotalPotentialOfSite = findViewById(R.id.existingTextTotalPotentialOfSite);
        existingTextTotalPotentialOfSite.setText(Html.fromHtml("Total Potential of Site (MT)"));
        existingEditTextTotalPotentialOfSite = findViewById(R.id.existingEditTextTotalPotentialOfSite);
        existingEditTextTotalPotentialOfSite.setHint(Html.fromHtml("Total Qty Required (MT) (Mandatory)"));

        // Quotation Quantity
        existingTextQuotationQuantity = findViewById(R.id.existingTextQuotationQuantity);
        existingTextQuotationQuantity.setText(Html.fromHtml("Quotation Quantity (MT)"));
        existingEditTextQuotationQuantity = findViewById(R.id.existingEditTextQuotationQuantity);
        existingEditTextQuotationQuantity.setHint(Html.fromHtml("Monthly Qty Required (MT) (Mandatory)"));

        // Current Brand Used
        existingTextCurrentBrandUsed = findViewById(R.id.existingTextCurrentBrandUsed);
        existingTextCurrentBrandUsed.setText(Html.fromHtml("Current Brand Used <font color='#FF0000'>*</font>"));
        existingEditTextCurrentBrandUsed = findViewById(R.id.existingEditTextCurrentBrandUsed);
        existingEditTextCurrentBrandUsed.setHint(Html.fromHtml("Current Brand Used (Mandatory)"));

        // Expected Rate per Bag
        existingTextExpectedRatePerBag = findViewById(R.id.existingTextExpectedRatePerBag);
        existingTextExpectedRatePerBag.setText(Html.fromHtml("Expected Rate Per Bag <font color='#FF0000'>*</font>"));
        existingEditTextExpectedRatePerBag = findViewById(R.id.existingEditTextExpectedRatePerBag);
        existingEditTextExpectedRatePerBag.setHint(Html.fromHtml("Expected Rate Per Bag (Mandatory)"));

        // Current Price Star Rs per Bag
        existingTextCurrentPriceStarRsPerBag = findViewById(R.id.existingTextCurrentPriceStarRsPerBag);
        existingTextCurrentPriceStarRsPerBag.setText(Html.fromHtml("Current Price Star Rs. Per Bag <font color='#FF0000'>*</font>"));
        existingEditTextCurrentPriceStarRsPerBag = findViewById(R.id.existingEditTextCurrentPriceStarRsPerBag);
        existingEditTextCurrentPriceStarRsPerBag.setHint(Html.fromHtml("Current Price Star (Mandatory)"));

        // Current Price Competitor Rs per Bag
        existingTextCurrentPriceCompetitorRsPerBag = findViewById(R.id.existingTextCurrentPriceCompetitorRsPerBag);
        existingTextCurrentPriceCompetitorRsPerBag.setText(Html.fromHtml("Current Price Competitor Rs. Per Bag <font color='#FF0000'>*</font>"));
        existingEditTextCurrentPriceCompetitorRsPerBag = findViewById(R.id.existingEditTextCurrentPriceCompetitorRsPerBag);
        existingEditTextCurrentPriceCompetitorRsPerBag.setHint(Html.fromHtml("Current Price Competitor (Mandatory)"));

        // Contact Person Name
        existingTextContactPersonName = findViewById(R.id.existingTextContactPersonName);
        existingTextContactPersonName.setText(Html.fromHtml("Contact Person Name <font color='#FF0000'>*</font>"));
        existingEditTextContactPersonName = findViewById(R.id.existingEditTextContactPersonName);
        existingEditTextContactPersonName.setHint(Html.fromHtml("Contact Person Name (Mandatory)"));

        // Designation
        existingTextDesignation = findViewById(R.id.existingTextDesignation);
        existingTextDesignation.setText(Html.fromHtml("Designation <font color='#FF0000'>*</font>"));
        existingEditTextDesignation = findViewById(R.id.existingEditTextDesignation);
        existingEditTextDesignation.setHint(Html.fromHtml("Designation (Mandatory)"));

        // Contact Number
        existingTextContactNumber = findViewById(R.id.existingTextContactNumber);
        existingTextContactNumber.setText(Html.fromHtml("Contact Number <font color='#FF0000'>*</font>"));
        existingEditTextContactNumber = findViewById(R.id.existingEditTextContactNumber);
        existingEditTextContactNumber.setHint(Html.fromHtml("Contact Number (Mandatory)"));

        // Mail id
        existingTextMailId = findViewById(R.id.existingTextMailId);
        existingTextMailId.setText(Html.fromHtml("Mail Id <font color='#FF0000'>*</font>"));
        existingEditTextMailId = findViewById(R.id.existingEditTextMailId);
        existingEditTextMailId.setHint(Html.fromHtml("Mail Id (Mandatory)"));

        // Mod of Payment Button
        existingModeOfPaymentButton = findViewById(R.id.existingModeOfPaymentButton);
        existingModeOfPaymentButton.setText(Html.fromHtml("Mode Of Payment"));
        existingModeOfPaymentButton.setOnClickListener(this);
        existingModeOfPaymentButtonSelectText = findViewById(R.id.existingModeOfPaymentButtonSelectText);
        existingModeOfPaymentButtonSelectText.setVisibility(View.GONE);

        // Credit Terms Button
        existingCreditTermsButton = findViewById(R.id.existingCreditTermsButton);
        existingCreditTermsButton.setText(Html.fromHtml("Credit Terms"));
        existingCreditTermsButton.setOnClickListener(this);
        existingCreditTermsButtonSelectText = findViewById(R.id.existingCreditTermsButtonSelectText);
        existingCreditTermsButtonSelectText.setVisibility(View.GONE);

        // AAC Block Required or Not Button
        existingAacBlockRequiredNotButton = findViewById(R.id.existingAacBlockRequiredNotButton);
        existingAacBlockRequiredNotButton.setText(Html.fromHtml("AAC Block is Required or Not"));
        existingAacBlockRequiredNotButton.setOnClickListener(this);
        existingAacBlockRequiredNotButtonSelectText = findViewById(R.id.existingAacBlockRequiredNotButtonSelectText);
        existingAacBlockRequiredNotButtonSelectText.setVisibility(View.GONE);

        // Category Type of Construction Button
        existingCategoryTypeOfConstructionButton = findViewById(R.id.existingCategoryTypeOfConstructionButton);
        existingCategoryTypeOfConstructionButton.setText(Html.fromHtml("Category Type of Construction <font color='#FF0000'>*</font>"));
        existingCategoryTypeOfConstructionButton.setOnClickListener(this);
        existingCategoryTypeOfConstructionButtonSelectText = findViewById(R.id.existingCategoryTypeOfConstructionButtonSelectText);
        existingCategoryTypeOfConstructionButtonSelectText.setVisibility(View.GONE);

        // Lead Status Button
        existingLeadStatusButton = findViewById(R.id.existingLeadStatusButton);
        existingLeadStatusButton.setText(Html.fromHtml("Lead Status <font color='#FF0000'>*</font>"));
        existingLeadStatusButton.setOnClickListener(this);
        existingLeadStatusButtonSelectText = findViewById(R.id.existingLeadStatusButtonSelectText);
        existingLeadStatusButtonSelectText.setVisibility(View.GONE);

        // Next Visit Date Button
        existingNextVisitDateButton = findViewById(R.id.existingNextVisitDateButton);
        existingNextVisitDateButton.setText(Html.fromHtml("Next Visit Date <font color='#FF0000'>*</font>"));
        existingNextVisitDateButton.setOnClickListener(this);
        existingNextVisitDateButtonSelectText = findViewById(R.id.existingNextVisitDateButtonSelectText);
        existingNextVisitDateButtonSelectText.setVisibility(View.GONE);

        // Requirement Type Button
        existingRequirementTypeButton = findViewById(R.id.existingRequirementTypeButton);
        existingRequirementTypeButton.setText(Html.fromHtml("Requirement Type <font color='#FF0000'>*</font>"));
        existingRequirementTypeButton.setOnClickListener(this);
        existingRequirementTypeButtonSelectText = findViewById(R.id.existingRequirementTypeButtonSelectText);
        existingRequirementTypeButtonSelectText.setVisibility(View.GONE);

        // Ex Works Button
        existingExWorksButton = findViewById(R.id.existingExWorksButton);
        existingExWorksButton.setText(Html.fromHtml("Ex. Works <font color='#FF0000'>*</font>"));
        existingExWorksButton.setOnClickListener(this);
        existingExWorksButtonSelectText = findViewById(R.id.existingExWorksButtonSelectText);
        existingExWorksButtonSelectText.setVisibility(View.GONE);

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

        // Assigned To Button
        existingAssignedToButton = findViewById(R.id.existingAssignedToButton);
        existingAssignedToButton.setText(Html.fromHtml("Assigned To <font color='#FF0000'>*</font>"));
        existingAssignedToButton.setOnClickListener(this);
        existingAssignedToButtonSelectText = findViewById(R.id.existingAssignedToButtonSelectText);
        existingAssignedToButtonSelectText.setVisibility(View.GONE);

        // Requirement Timing Button
        existingRequirementTimingButton = findViewById(R.id.existingRequirementTimingButton);
        existingRequirementTimingButton.setText(Html.fromHtml("Requirement Timing <font color='#FF0000'>*</font>"));
        existingRequirementTimingButton.setOnClickListener(this);
        existingRequirementTimingButtonSelectText = findViewById(R.id.existingRequirementTimingButtonSelectText);
        existingRequirementTimingButtonSelectText.setVisibility(View.GONE);

        // Share Lead Image Button
        existingShareLeadImageButton = findViewById(R.id.existingShareLeadImageButton);
        existingShareLeadImageButton.setText(Html.fromHtml("Share Lead / Site Details (Image)"));
        existingShareLeadImageButton.setOnClickListener(this);

        // Lead Action Button
        existingLeadActionButton = findViewById(R.id.existingLeadActionButton);
        existingLeadActionButton.setText(Html.fromHtml("Lead Action <font color='#FF0000'>*</font>"));
        existingLeadActionButton.setOnClickListener(this);

        getDefaultData();
    }


    // ==================== After Submit Successfully ==================== //
    // Success Message And Goto Previous Page For New Lead Generation
    private void successMessageAndGotoPreviousPageForNewLeadGeneration() {
        progressDialogClose();
        Toast.makeText(mContext, "New lead add successfully", Toast.LENGTH_LONG).show();
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }

    // Success Message And Clean Field For Existing Lead Generation
    private void successMessageAndCleanFieldForExistingLeadGeneration() {
        progressDialogClose();
        Toast.makeText(mContext, "Update lead successfully", Toast.LENGTH_LONG).show();
        runOnUiThread(() -> {
            hosActionPopup.setVisibility(View.GONE);

            existingEditTextSalesOfficerName.setText("");
            existingEditTextDateStamp.setText("");
            existingEditTextTimeStamp.setText("");
            existingEditTextLatitude.setText("");
            existingEditTextLongitude.setText("");
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
            existingTextUniqueLeadID.setText("");
            existingSegmentButtonSelectText.setText("");
            existingLeadSourceButtonSelectText.setText("");
            existingProductPackagingButtonSelectText.setText("");
            existingModeOfPaymentButtonSelectText.setText("");
            existingCreditTermsButtonSelectText.setText("");
            existingAacBlockRequiredNotButtonSelectText.setText("");
            existingCategoryTypeOfConstructionButtonSelectText.setText("");
            existingLeadStatusButtonSelectText.setText("");
            existingNextVisitDateButtonSelectText.setText("");
            existingRequirementTypeButtonSelectText.setText("");
            existingExWorksButtonSelectText.setText("");
            existingAssignedToButtonSelectText.setText("");
            existingRequirementTimingButtonSelectText.setText("");


            existingTextUniqueLeadID.setVisibility(View.VISIBLE);
            existingSegmentButtonSelectText.setVisibility(View.VISIBLE);
            existingLeadSourceButtonSelectText.setVisibility(View.VISIBLE);
            existingProductPackagingButtonSelectText.setVisibility(View.VISIBLE);
            existingModeOfPaymentButtonSelectText.setVisibility(View.VISIBLE);
            existingCreditTermsButtonSelectText.setVisibility(View.VISIBLE);
            existingAacBlockRequiredNotButtonSelectText.setVisibility(View.VISIBLE);
            existingCategoryTypeOfConstructionButtonSelectText.setVisibility(View.VISIBLE);
            existingLeadStatusButtonSelectText.setVisibility(View.VISIBLE);
            existingNextVisitDateButtonSelectText.setVisibility(View.VISIBLE);
            existingRequirementTypeButtonSelectText.setVisibility(View.VISIBLE);
            existingExWorksButtonSelectText.setVisibility(View.VISIBLE);
            existingAssignedToButtonSelectText.setVisibility(View.VISIBLE);
            existingRequirementTimingButtonSelectText.setVisibility(View.VISIBLE);

        });
        if (TYPE_OF_USER.equalsIgnoreCase("hos")) {
            new TRANS_OldLead_AsyncTask(mContext, "hos").execute();
        } else {
            new TRANS_OldLead_AsyncTask(mContext, "so").execute();
        }
    }
    // ======================================== //


    // ==================== Checking Data For Update ==================== //
    // SO New Lead Generate Data Checking
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
            Toast.makeText(mContext, "Please select lead source.", Toast.LENGTH_LONG).show();
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
            Toast.makeText(mContext, "Please select lead status.", Toast.LENGTH_LONG).show();
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

    // SO Existing Lead Update Data Checking
    private void checkDataForUpdate() {
        potentialOfSite = existingEditTextTotalPotentialOfSite.getText().toString().trim();
        quotationQuantity = existingEditTextQuotationQuantity.getText().toString().trim();
        expectedRatePerBag = existingEditTextExpectedRatePerBag.getText().toString().trim();
        currentPriceStarRsPerBag = existingEditTextCurrentPriceStarRsPerBag.getText().toString().trim();
        currentPriceCompetitorRsPerBag = existingEditTextCurrentPriceCompetitorRsPerBag.getText().toString().trim();
        salesOfficerRemarks = existingEditTextSalesOfficerRemarks.getText().toString().trim();

        if (expectedRatePerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter expected rate per bag.", Toast.LENGTH_LONG).show();
        } else if (currentPriceStarRsPerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter current price star rs per bag.", Toast.LENGTH_LONG).show();
        } else if (currentPriceCompetitorRsPerBag.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter current price competitor rs per bag.", Toast.LENGTH_LONG).show();
        } else {
            requestForUpdaterLeadGeneration();
        }
    }

    // HOS Status Update Data Checking
    private void checkHosSubmitDataAndUpload() {
        if (LEAD_STATUS_UPDATE_FROM_HOS.equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select lead status for update.", Toast.LENGTH_LONG).show();
        } else if (editTextSendingQuotaion.getText().toString().trim().equalsIgnoreCase("") && LEAD_STATUS_UPDATE_FROM_HOS.equalsIgnoreCase("yes")) {
            Toast.makeText(mContext, "Please enter Sending Quotaion.", Toast.LENGTH_LONG).show();
        } else if (editTextHOSRemarks.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Remarks.", Toast.LENGTH_LONG).show();
        } else {
            requestForUpdaterLeadGenerationStatusHOS();
        }
    }
    // ======================================== //


    // ==================== Existing lead data show ==================== //
    // Existing lead data show
    private void showExistingLeadAllData(LeadDataSet data) {
        try {
            JSONObject obj = data.getFullData();

            existingEditTextSalesOfficerName.setText(getDataMultiObject(obj, "emp_details", "emp_name"));
            existingEditTextDateStamp.setText(getDataSingleObject(obj, "download_time").split("T")[0]);
            existingEditTextTimeStamp.setText(getDataSingleObject(obj, "download_time").split("T")[1].replace("Z", ""));
            existingEditTextLatitude.setText(getDataSingleObject(obj, "latitude"));
            existingEditTextLongitude.setText(getDataSingleObject(obj, "longitude"));
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
            existingEditTextDesignation.setText(getDataSingleObject(obj, "designation"));
            existingEditTextContactNumber.setText(getDataSingleObject(obj, "contact_number"));
            existingEditTextMailId.setText(getDataSingleObject(obj, "mail_id"));
            existingEditTextFosSiding.setText(getDataSingleObject(obj, "serving_location"));
            existingEditTextSalesOfficerRemarks.setText(getDataSingleObject(obj, "lead_remarks"));
            existingTextUniqueLeadID.setText(getDataSingleObject(obj, "lead_generation_id"));
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
                    existingExWorksButtonLayout.setVisibility(View.VISIBLE);
                    existingFosEditTextLayout.setVisibility(View.GONE);
                } else if (obj.getString("incoterms").equalsIgnoreCase("FOS")) {
                    existingExWorksButtonLayout.setVisibility(View.GONE);
                    existingFosEditTextLayout.setVisibility(View.VISIBLE);
                } else {
                    existingExWorksButtonLayout.setVisibility(View.GONE);
                    existingFosEditTextLayout.setVisibility(View.GONE);
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


            existingTextUniqueLeadID.setVisibility(View.VISIBLE);
            existingSegmentButtonSelectText.setVisibility(View.VISIBLE);
            existingLeadSourceButtonSelectText.setVisibility(View.VISIBLE);
            existingProductPackagingButtonSelectText.setVisibility(View.VISIBLE);
            existingModeOfPaymentButtonSelectText.setVisibility(View.VISIBLE);
            existingCreditTermsButtonSelectText.setVisibility(View.VISIBLE);
            existingAacBlockRequiredNotButtonSelectText.setVisibility(View.VISIBLE);
            existingCategoryTypeOfConstructionButtonSelectText.setVisibility(View.VISIBLE);
            existingLeadStatusButtonSelectText.setVisibility(View.VISIBLE);
            existingNextVisitDateButtonSelectText.setVisibility(View.VISIBLE);
            existingRequirementTypeButtonSelectText.setVisibility(View.VISIBLE);
            existingExWorksButtonSelectText.setVisibility(View.VISIBLE);
            existingAssignedToButtonSelectText.setVisibility(View.VISIBLE);
            existingRequirementTimingButtonSelectText.setVisibility(View.VISIBLE);
        } catch (Exception ignored) {
        }

        if (TYPE_OF_USER.equalsIgnoreCase("hos")) {
            isButtonPressForExistingLeadButton = false;
            editTextColorChangeFor_HOS_InExistingLead();
            buttonBackgroundFor_HOS_InExistingLead();
            buttonPressFor_HOS_InExistingLead(data);
        } else {
            if (data.getLeadStatus().equalsIgnoreCase("pending") || data.getLeadStatus().equalsIgnoreCase("REVISION")) {
                isButtonPressForExistingLeadButton = true;
                submitButton.setVisibility(View.VISIBLE);
                editTextColorChangeFor_SO_InExistingLeadWhenEditable();
                editTextFor_SO_InExistingLeadWhenEditable();
                buttonBackgroundFor_SO_InExistingLeadWhenEditable();
            } else {
                isButtonPressForExistingLeadButton = false;
                submitButton.setVisibility(View.GONE);
                editTextColorChangeFor_SO_InExistingLeadWhenNonEditable();
                editTextFor_SO_InExistingLeadWhenNonEditable();
                buttonBackgroundFor_SO_InExistingLeadWhenNonEditable();
            }
        }
    }

    // Existing lead data show for HOS
    private void editTextColorChangeFor_HOS_InExistingLead() {
        existingEditTextSalesOfficerName.setTextColor(Color.parseColor("#666666"));
        existingEditTextDateStamp.setTextColor(Color.parseColor("#666666"));
        existingEditTextTimeStamp.setTextColor(Color.parseColor("#666666"));
        existingEditTextLatitude.setTextColor(Color.parseColor("#666666"));
        existingEditTextLongitude.setTextColor(Color.parseColor("#666666"));
        existingEditTextSoldToPartyName.setTextColor(Color.parseColor("#666666"));
        existingEditTextSoldToPartyCode.setTextColor(Color.parseColor("#666666"));
        existingEditTextSoldToPartyAddress.setTextColor(Color.parseColor("#666666"));
        existingEditTextSoldToPartyState.setTextColor(Color.parseColor("#666666"));
        existingEditTextSoldToPartyDistricts.setTextColor(Color.parseColor("#666666"));
        existingEditTextShipToPartyName.setTextColor(Color.parseColor("#666666"));
        existingEditTextShipToPartyCode.setTextColor(Color.parseColor("#666666"));
        existingEditTextShipToPartyAddress.setTextColor(Color.parseColor("#666666"));
        existingEditTextShipToPartyState.setTextColor(Color.parseColor("#666666"));
        existingEditTextShipToPartyDistricts.setTextColor(Color.parseColor("#666666"));
        existingEditTextCurrentBrandUsed.setTextColor(Color.parseColor("#666666"));
        existingEditTextContactPersonName.setTextColor(Color.parseColor("#666666"));
        existingEditTextDesignation.setTextColor(Color.parseColor("#666666"));
        existingEditTextContactNumber.setTextColor(Color.parseColor("#666666"));
        existingEditTextMailId.setTextColor(Color.parseColor("#666666"));
        existingEditTextFosSiding.setTextColor(Color.parseColor("#666666"));
        existingEditTextSalesOfficerRemarks.setTextColor(Color.parseColor("#666666"));
        existingEditTextExpectedRatePerBag.setTextColor(Color.parseColor("#666666"));
        existingEditTextTotalPotentialOfSite.setTextColor(Color.parseColor("#666666"));
        existingEditTextQuotationQuantity.setTextColor(Color.parseColor("#666666"));
        existingEditTextCurrentPriceStarRsPerBag.setTextColor(Color.parseColor("#666666"));
        existingEditTextCurrentPriceCompetitorRsPerBag.setTextColor(Color.parseColor("#666666"));
    }

    private void buttonPressFor_HOS_InExistingLead(LeadDataSet data) {
        existingEditTextSalesOfficerName.setEnabled(false);
        existingEditTextDateStamp.setEnabled(false);
        existingEditTextTimeStamp.setEnabled(false);
        existingEditTextLatitude.setEnabled(false);
        existingEditTextLongitude.setEnabled(false);
        existingEditTextSoldToPartyName.setEnabled(false);
        existingEditTextSoldToPartyCode.setEnabled(false);
        existingEditTextSoldToPartyAddress.setEnabled(false);
        existingEditTextSoldToPartyState.setEnabled(false);
        existingEditTextSoldToPartyDistricts.setEnabled(false);
        existingEditTextShipToPartyName.setEnabled(false);
        existingEditTextShipToPartyCode.setEnabled(false);
        existingEditTextShipToPartyAddress.setEnabled(false);
        existingEditTextShipToPartyState.setEnabled(false);
        existingEditTextShipToPartyDistricts.setEnabled(false);
        existingEditTextCurrentBrandUsed.setEnabled(false);
        existingEditTextContactPersonName.setEnabled(false);
        existingEditTextDesignation.setEnabled(false);
        existingEditTextContactNumber.setEnabled(false);
        existingEditTextMailId.setEnabled(false);
        existingEditTextFosSiding.setEnabled(false);
        existingEditTextSalesOfficerRemarks.setEnabled(false);
        existingEditTextExpectedRatePerBag.setEnabled(false);
        existingEditTextTotalPotentialOfSite.setEnabled(false);
        existingEditTextQuotationQuantity.setEnabled(false);
        existingEditTextCurrentPriceStarRsPerBag.setEnabled(false);
        existingEditTextCurrentPriceCompetitorRsPerBag.setEnabled(false);

        if (data.getLeadStatus().equalsIgnoreCase("pending") || data.getLeadStatus().equalsIgnoreCase("REVISION") || data.getLeadStatus().equalsIgnoreCase("hold")) {
            existingLeadActionButton.setVisibility(View.VISIBLE);
        } else {
            existingLeadActionButton.setVisibility(View.GONE);
        }
    }

    private void buttonBackgroundFor_HOS_InExistingLead() {
        existingSegmentButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingLeadSourceButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingProductPackagingButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingModeOfPaymentButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingCreditTermsButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingAacBlockRequiredNotButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingCategoryTypeOfConstructionButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingLeadStatusButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingNextVisitDateButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingRequirementTypeButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingExWorksButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingAssignedToButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingRequirementTimingButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
    }

    // Existing lead data show for SO when EDITABLE
    private void editTextColorChangeFor_SO_InExistingLeadWhenEditable() {
        existingEditTextSalesOfficerName.setTextColor(Color.parseColor("#888888"));
        existingEditTextDateStamp.setTextColor(Color.parseColor("#888888"));
        existingEditTextTimeStamp.setTextColor(Color.parseColor("#888888"));
        existingEditTextLatitude.setTextColor(Color.parseColor("#888888"));
        existingEditTextLongitude.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyName.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyCode.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyAddress.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyState.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyDistricts.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyName.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyCode.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyAddress.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyState.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyDistricts.setTextColor(Color.parseColor("#888888"));
    }

    private void buttonBackgroundFor_SO_InExistingLeadWhenEditable() {
        existingSegmentButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingLeadSourceButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingProductPackagingButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingModeOfPaymentButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingCreditTermsButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingAacBlockRequiredNotButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingCategoryTypeOfConstructionButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingLeadStatusButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingNextVisitDateButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingRequirementTypeButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingExWorksButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingAssignedToButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        existingRequirementTimingButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
    }

    private void editTextFor_SO_InExistingLeadWhenEditable() {
        existingEditTextSalesOfficerName.setEnabled(false);
        existingEditTextDateStamp.setEnabled(false);
        existingEditTextTimeStamp.setEnabled(false);
        existingEditTextLatitude.setEnabled(false);
        existingEditTextLongitude.setEnabled(false);

        existingEditTextSoldToPartyName.setEnabled(false);
        existingEditTextSoldToPartyCode.setEnabled(false);
        existingEditTextSoldToPartyAddress.setEnabled(false);
        existingEditTextSoldToPartyState.setEnabled(false);
        existingEditTextSoldToPartyDistricts.setEnabled(false);

        existingEditTextShipToPartyName.setEnabled(false);
        existingEditTextShipToPartyCode.setEnabled(false);
        existingEditTextShipToPartyAddress.setEnabled(false);
        existingEditTextShipToPartyState.setEnabled(false);
        existingEditTextShipToPartyDistricts.setEnabled(false);

        existingEditTextCurrentBrandUsed.setEnabled(true);
        existingEditTextContactPersonName.setEnabled(true);
        existingEditTextDesignation.setEnabled(true);
        existingEditTextContactNumber.setEnabled(true);
        existingEditTextMailId.setEnabled(true);
        existingEditTextFosSiding.setEnabled(true);
        existingEditTextSalesOfficerRemarks.setEnabled(true);
        existingEditTextExpectedRatePerBag.setEnabled(true);
        existingEditTextTotalPotentialOfSite.setEnabled(true);
        existingEditTextQuotationQuantity.setEnabled(true);
        existingEditTextCurrentPriceStarRsPerBag.setEnabled(true);
        existingEditTextCurrentPriceCompetitorRsPerBag.setEnabled(true);
    }

    // Existing lead data show for SO when NON-EDITABLE
    private void editTextColorChangeFor_SO_InExistingLeadWhenNonEditable() {
        existingEditTextSalesOfficerName.setTextColor(Color.parseColor("#888888"));
        existingEditTextDateStamp.setTextColor(Color.parseColor("#888888"));
        existingEditTextTimeStamp.setTextColor(Color.parseColor("#888888"));
        existingEditTextLatitude.setTextColor(Color.parseColor("#888888"));
        existingEditTextLongitude.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyName.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyCode.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyAddress.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyState.setTextColor(Color.parseColor("#888888"));
        existingEditTextSoldToPartyDistricts.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyName.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyCode.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyAddress.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyState.setTextColor(Color.parseColor("#888888"));
        existingEditTextShipToPartyDistricts.setTextColor(Color.parseColor("#888888"));
        existingEditTextCurrentBrandUsed.setTextColor(Color.parseColor("#888888"));
        existingEditTextContactPersonName.setTextColor(Color.parseColor("#888888"));
        existingEditTextDesignation.setTextColor(Color.parseColor("#888888"));
        existingEditTextContactNumber.setTextColor(Color.parseColor("#888888"));
        existingEditTextMailId.setTextColor(Color.parseColor("#888888"));
    }

    private void buttonBackgroundFor_SO_InExistingLeadWhenNonEditable() {
        existingSegmentButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingLeadSourceButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingProductPackagingButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingModeOfPaymentButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingCreditTermsButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingAacBlockRequiredNotButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingCategoryTypeOfConstructionButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingLeadStatusButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingNextVisitDateButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingRequirementTypeButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingExWorksButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingAssignedToButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        existingRequirementTimingButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
    }

    private void editTextFor_SO_InExistingLeadWhenNonEditable() {
        existingEditTextSalesOfficerName.setEnabled(false);
        existingEditTextDateStamp.setEnabled(false);
        existingEditTextTimeStamp.setEnabled(false);
        existingEditTextLatitude.setEnabled(false);
        existingEditTextLongitude.setEnabled(false);
        existingEditTextSoldToPartyName.setEnabled(false);
        existingEditTextSoldToPartyCode.setEnabled(false);
        existingEditTextSoldToPartyAddress.setEnabled(false);
        existingEditTextSoldToPartyState.setEnabled(false);
        existingEditTextSoldToPartyDistricts.setEnabled(false);
        existingEditTextShipToPartyName.setEnabled(false);
        existingEditTextShipToPartyCode.setEnabled(false);
        existingEditTextShipToPartyAddress.setEnabled(false);
        existingEditTextShipToPartyState.setEnabled(false);
        existingEditTextShipToPartyDistricts.setEnabled(false);
        existingEditTextCurrentBrandUsed.setEnabled(false);
        existingEditTextContactPersonName.setEnabled(false);
        existingEditTextDesignation.setEnabled(false);
        existingEditTextContactNumber.setEnabled(false);
        existingEditTextMailId.setEnabled(false);
        existingEditTextFosSiding.setEnabled(false);
        existingEditTextSalesOfficerRemarks.setEnabled(false);
        existingEditTextExpectedRatePerBag.setEnabled(false);
        existingEditTextTotalPotentialOfSite.setEnabled(false);
        existingEditTextQuotationQuantity.setEnabled(false);
        existingEditTextCurrentPriceStarRsPerBag.setEnabled(false);
        existingEditTextCurrentPriceCompetitorRsPerBag.setEnabled(false);
    }
    // ======================================== //


    // ==================== JSON to String Fetch ==================== //
    // function of get data from object with try-catch single
    private String getDataSingleObject(JSONObject parent, String stringKey) {
        String returnData = "";
        try {
            returnData = parent.get(stringKey).toString();
        } catch (Exception ignored) {
        }
        return returnData;
    }

    // function of get data from object with try-catch multi
    private String getDataMultiObject(JSONObject parent, String objectKey, String stringKey) {
        try {
            JSONObject nested = parent.optJSONObject(objectKey);
            return nested != null ? nested.get(stringKey).toString() : "";
        } catch (Exception e) {
            return "";
        }
    }
    // ======================================== //


    // ==================== Clear All Field ==================== //
    // Clean All Previous Data from new lead
    private void cleanAllDataFromNewLeadGeneration() {
        exWorksButtonLayout.setVisibility(View.GONE);
        fosEditTextLayout.setVisibility(View.GONE);

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

        soldToPartyStateButtonSelectText.setVisibility(View.GONE);
        soldToPartyStateButtonSelectText.setText("");
        soldToPartyDistrictsButtonSelectText.setVisibility(View.GONE);
        soldToPartyDistrictsButtonSelectText.setText("");
        shipToPartyStateButtonSelectText.setVisibility(View.GONE);
        shipToPartyStateButtonSelectText.setText("");
        shipToPartyDistrictsButtonSelectText.setVisibility(View.GONE);
        shipToPartyDistrictsButtonSelectText.setText("");
        segmentButtonSelectText.setVisibility(View.GONE);
        segmentButtonSelectText.setText("");
        leadSourceButtonSelectText.setVisibility(View.GONE);
        leadSourceButtonSelectText.setText("");
        productPackagingButtonSelectText.setVisibility(View.GONE);
        productPackagingButtonSelectText.setText("");
        modeOfPaymentButtonSelectText.setVisibility(View.GONE);
        modeOfPaymentButtonSelectText.setText("");
        creditTermsButtonSelectText.setVisibility(View.GONE);
        creditTermsButtonSelectText.setText("");
        aacBlockRequiredNotButtonSelectText.setVisibility(View.GONE);
        aacBlockRequiredNotButtonSelectText.setText("");
        categoryTypeOfConstructionButtonSelectText.setVisibility(View.GONE);
        categoryTypeOfConstructionButtonSelectText.setText("");
        leadStatusButtonSelectText.setVisibility(View.GONE);
        leadStatusButtonSelectText.setText("");
        nextVisitDateButtonSelectText.setVisibility(View.GONE);
        nextVisitDateButtonSelectText.setText("");
        requirementTypeButtonSelectText.setVisibility(View.GONE);
        requirementTypeButtonSelectText.setText("");
        exWorksButtonSelectText.setVisibility(View.GONE);
        exWorksButtonSelectText.setText("");
        assignedToButtonSelectText.setVisibility(View.GONE);
        assignedToButtonSelectText.setText("");
        requirementTimingButtonSelectText.setVisibility(View.GONE);
        requirementTimingButtonSelectText.setText("");
    }

    // Clean All Previous Data from existing lead
    private void clearAllDataFromExistingLead() {
        existingExWorksButtonLayout.setVisibility(View.GONE);
        existingFosEditTextLayout.setVisibility(View.GONE);

        existingEditTextSalesOfficerName.setText("");
        existingEditTextDateStamp.setText("");
        existingEditTextTimeStamp.setText("");
        existingEditTextLatitude.setText("");
        existingEditTextLongitude.setText("");
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

        existingTextUniqueLeadID.setVisibility(View.GONE);
        existingTextUniqueLeadID.setText("");
        existingSegmentButtonSelectText.setVisibility(View.GONE);
        existingSegmentButtonSelectText.setText("");
        existingLeadSourceButtonSelectText.setVisibility(View.GONE);
        existingLeadSourceButtonSelectText.setText("");
        existingProductPackagingButtonSelectText.setVisibility(View.GONE);
        existingProductPackagingButtonSelectText.setText("");
        existingModeOfPaymentButtonSelectText.setVisibility(View.GONE);
        existingModeOfPaymentButtonSelectText.setText("");
        existingCreditTermsButtonSelectText.setVisibility(View.GONE);
        existingCreditTermsButtonSelectText.setText("");
        existingAacBlockRequiredNotButtonSelectText.setVisibility(View.GONE);
        existingAacBlockRequiredNotButtonSelectText.setText("");
        existingCategoryTypeOfConstructionButtonSelectText.setVisibility(View.GONE);
        existingCategoryTypeOfConstructionButtonSelectText.setText("");
        existingLeadStatusButtonSelectText.setVisibility(View.GONE);
        existingLeadStatusButtonSelectText.setText("");
        existingNextVisitDateButtonSelectText.setVisibility(View.GONE);
        existingNextVisitDateButtonSelectText.setText("");
        existingRequirementTypeButtonSelectText.setVisibility(View.GONE);
        existingRequirementTypeButtonSelectText.setText("");
        existingExWorksButtonSelectText.setVisibility(View.GONE);
        existingExWorksButtonSelectText.setText("");
        existingAssignedToButtonSelectText.setVisibility(View.GONE);
        existingAssignedToButtonSelectText.setText("");
        existingRequirementTimingButtonSelectText.setVisibility(View.GONE);
        existingRequirementTimingButtonSelectText.setText("");

        hosActionPopup.setVisibility(View.GONE);
        editTextSendingQuotaion.setText("");
        editTextHOSRemarks.setText("");
    }
    // ======================================== //


    // ==================== For New Lead ==================== //
    // Date Picker
    private void nextVisitDatePicker() {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                LeadGenerationActivity.this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    // Month is 0-based, so add 1
                    String date = selectedDay + "/" + (selectedMonth + 1) + "/" + selectedYear;
                    nextVisitDateButtonSelectText.setText(date);
                    nextVisitDateButtonSelectText.setVisibility(View.VISIBLE);
                    nextVisitDate = date;
                },
                year, month, day
        );
        datePickerDialog.getDatePicker().setMinDate(calendar.getTimeInMillis());
        datePickerDialog.show();
    }

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
                } else {
                    assignedTo = Objects.requireNonNull(adapterCust.getItem(position)).getEmp_code();
                    assignedToButtonSelectText.setVisibility(View.VISIBLE);
                    assignedToButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getEmp_name());
                }
            });

            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
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

                    soldToPartyStateButtonSelectText.setVisibility(View.VISIBLE);
                    soldToPartyStateButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getState());

                    soldToPartyDistrictsButtonSelectText.setVisibility(View.VISIBLE);
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

                    shipToPartyStateButtonSelectText.setVisibility(View.VISIBLE);
                    shipToPartyStateButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getState());

                    shipToPartyDistrictsButtonSelectText.setVisibility(View.VISIBLE);
                    shipToPartyDistrictsButtonSelectText.setText(Objects.requireNonNull(adapterCust.getItem(position)).getDistrict());
                }
            });

            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            mDialogCustomer.show();
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
            btn_cncl.setVisibility(View.GONE);

            final ShowDataSetAdapter pAdapter = new ShowDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                switch (value) {
                    case "segment":
                        segmentButtonSelectText.setVisibility(View.VISIBLE);
                        segmentButtonSelectText.setText(dataSet.get(position).getValue());
                        segment = dataSet.get(position).getId();
                        break;
                    case "lead_source":
                        leadSourceButtonSelectText.setVisibility(View.VISIBLE);
                        leadSourceButtonSelectText.setText(dataSet.get(position).getValue());
                        leadSource = dataSet.get(position).getId();
                        break;
                    case "product":
                        productPackagingButtonSelectText.setVisibility(View.VISIBLE);
                        productPackagingButtonSelectText.setText(dataSet.get(position).getValue());
                        productPackaging = dataSet.get(position).getId();
                        break;
                    case "mode_of_payment":
                        modeOfPaymentButtonSelectText.setVisibility(View.VISIBLE);
                        modeOfPaymentButtonSelectText.setText(dataSet.get(position).getValue());
                        modeOfPayment = dataSet.get(position).getId();
                        break;
                    case "credit_terms":
                        creditTermsButtonSelectText.setVisibility(View.VISIBLE);
                        creditTermsButtonSelectText.setText(dataSet.get(position).getValue());
                        creditTerms = dataSet.get(position).getId();
                        break;
                    case "aac_block_required_check":
                        aacBlockRequiredNotButtonSelectText.setVisibility(View.VISIBLE);
                        aacBlockRequiredNotButtonSelectText.setText(dataSet.get(position).getValue());
                        aacBlockRequiredStatus = dataSet.get(position).getId();
                        break;
                    case "construction_type":
                        categoryTypeOfConstructionButtonSelectText.setVisibility(View.VISIBLE);
                        categoryTypeOfConstructionButtonSelectText.setText(dataSet.get(position).getValue());
                        constructionType = dataSet.get(position).getId();
                        break;
                    case "lead_status":
                        leadStatusButtonSelectText.setVisibility(View.VISIBLE);
                        leadStatusButtonSelectText.setText(dataSet.get(position).getValue());
                        leadStatus = dataSet.get(position).getId();
                        break;
                    case "requirement_type":
                        requirementTypeButtonSelectText.setVisibility(View.VISIBLE);
                        requirementTypeButtonSelectText.setText(dataSet.get(position).getValue());
                        requirementType = dataSet.get(position).getId();

                        if (dataSet.get(position).getId().equalsIgnoreCase("EXW")) {
                            exWorksButtonLayout.setVisibility(View.VISIBLE);
                            fosEditTextLayout.setVisibility(View.GONE);
                        } else if (dataSet.get(position).getId().equalsIgnoreCase("FOS")) {
                            exWorksButtonLayout.setVisibility(View.GONE);
                            fosEditTextLayout.setVisibility(View.VISIBLE);
                        } else {
                            exWorksButtonLayout.setVisibility(View.GONE);
                            fosEditTextLayout.setVisibility(View.GONE);
                        }
                        break;
                    case "ex_work":
                        exWorksButtonSelectText.setVisibility(View.VISIBLE);
                        exWorksButtonSelectText.setText(dataSet.get(position).getValue());
                        exWorks = dataSet.get(position).getId();
                        break;
                    case "requirement_timing":
                        requirementTimingButtonSelectText.setVisibility(View.VISIBLE);
                        requirementTimingButtonSelectText.setText(dataSet.get(position).getValue());
                        requirementTiming = dataSet.get(position).getId();
                        break;
                }
            });

            mDestinationDialog.show();
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
            btn_cncl.setVisibility(View.GONE);

            ImageView imageView1 = mDestinationDialog.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDestinationDialog.dismiss());
            final ShowStateDataSetAdapter pAdapter = new ShowStateDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                if (value.equalsIgnoreCase("sold_party_state_name")) {
                    soldToPartyStateButtonSelectText.setVisibility(View.VISIBLE);
                    soldToPartyStateButtonSelectText.setText(dataSet.get(position).getTitle());
                    soldToPartyState = dataSet.get(position).getValue();
                } else {
                    shipToPartyStateButtonSelectText.setVisibility(View.VISIBLE);
                    shipToPartyStateButtonSelectText.setText(dataSet.get(position).getTitle());
                    shipToPartyState = dataSet.get(position).getValue();
                }
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
    }

    // Lead Data Set List Show Dialog Popup
    @SuppressLint("CutPasteId")
    public void show_lead_list_dialog(ArrayList<LeadDataSet> dataSet, String titleValue) {
        try {
            final LeadDataSetAdapter adapterCust = new LeadDataSetAdapter(mContext, R.layout.lead_list_item_layout, dataSet);

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.existion_lead_list_with_filter);
            mDialogCustomer.setCancelable(false);

            LinearLayout filterLayout = mDialogCustomer.findViewById(R.id.filterLayout);
            filterLayout.setVisibility(View.GONE);
            filterLayout.setOnClickListener(v -> filterLayout.setVisibility(View.GONE));

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);

            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            ImageView filterIcon = mDialogCustomer.findViewById(R.id.filterIcon);
            filterIcon.setOnClickListener(v -> filterLayout.setVisibility(View.VISIBLE));

            EditText formDateButton = mDialogCustomer.findViewById(R.id.formDateButton);
            formDateButton.setOnClickListener(v -> {
                Calendar calendar = Calendar.getInstance();
                int year = calendar.get(Calendar.YEAR);
                int month = calendar.get(Calendar.MONTH);
                int day = calendar.get(Calendar.DAY_OF_MONTH);

                DatePickerDialog datePickerDialog = new DatePickerDialog(
                        LeadGenerationActivity.this,
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
                        LeadGenerationActivity.this,
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
                CustomerFilterModel filterModel = new CustomerFilterModel(searchText1.getText().toString(), filterFormDate, filterToDate, filterStatus, TYPE_OF_USER);
                adapterCust.getFilter().filter(gson.toJson(filterModel));
                filterLayout.setVisibility(View.GONE);
            });

            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    Gson gson = new Gson();  // or any serializer
                    CustomerFilterModel filterModel = new CustomerFilterModel(s.toString(), filterFormDate, filterToDate, filterStatus, TYPE_OF_USER);
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
            btn_cncl.setVisibility(View.GONE);
            ImageView imageView1 = mDestinationDialog.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDestinationDialog.dismiss());

            final ShowDistrictDataSetAdapter pAdapter = new ShowDistrictDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                if (value.equalsIgnoreCase("sold_party_district_name")) {
                    soldToPartyDistrictsButtonSelectText.setVisibility(View.VISIBLE);
                    soldToPartyDistrictsButtonSelectText.setText(dataSet.get(position).getValue());
                } else {
                    shipToPartyDistrictsButtonSelectText.setVisibility(View.VISIBLE);
                    shipToPartyDistrictsButtonSelectText.setText(dataSet.get(position).getValue());
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
    // ======================================== //


    // ==================== For Existing Lead ==================== //
    // Date Picker
    private void nextVisitDatePicker1() {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                LeadGenerationActivity.this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    // Month is 0-based, so add 1
                    String date = selectedDay + "/" + (selectedMonth + 1) + "/" + selectedYear;
                    existingNextVisitDateButtonSelectText.setText(date);
                    existingNextVisitDateButtonSelectText.setVisibility(View.VISIBLE);
                    nextVisitDate = date;
                },
                year, month, day
        );
        datePickerDialog.getDatePicker().setMinDate(calendar.getTimeInMillis());
        datePickerDialog.show();
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
            btn_cncl.setVisibility(View.GONE);

            final ShowDataSetAdapter pAdapter = new ShowDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                switch (value) {
                    case "segment":
                        existingSegmentButtonSelectText.setVisibility(View.VISIBLE);
                        existingSegmentButtonSelectText.setText(dataSet.get(position).getValue());
                        segment = dataSet.get(position).getId();
                        break;
                    case "lead_source":
                        existingLeadSourceButtonSelectText.setVisibility(View.VISIBLE);
                        existingLeadSourceButtonSelectText.setText(dataSet.get(position).getValue());
                        leadSource = dataSet.get(position).getId();
                        break;
                    case "product":
                        existingProductPackagingButtonSelectText.setVisibility(View.VISIBLE);
                        existingProductPackagingButtonSelectText.setText(dataSet.get(position).getValue());
                        productPackaging = dataSet.get(position).getId();
                        break;
                    case "mode_of_payment":
                        existingModeOfPaymentButtonSelectText.setVisibility(View.VISIBLE);
                        existingModeOfPaymentButtonSelectText.setText(dataSet.get(position).getValue());
                        modeOfPayment = dataSet.get(position).getId();
                        break;
                    case "credit_terms":
                        existingCreditTermsButtonSelectText.setVisibility(View.VISIBLE);
                        existingCreditTermsButtonSelectText.setText(dataSet.get(position).getValue());
                        creditTerms = dataSet.get(position).getId();
                        break;
                    case "aac_block_required_check":
                        existingAacBlockRequiredNotButtonSelectText.setVisibility(View.VISIBLE);
                        existingAacBlockRequiredNotButtonSelectText.setText(dataSet.get(position).getValue());
                        aacBlockRequiredStatus = dataSet.get(position).getId();
                        break;
                    case "construction_type":
                        existingCategoryTypeOfConstructionButtonSelectText.setVisibility(View.VISIBLE);
                        existingCategoryTypeOfConstructionButtonSelectText.setText(dataSet.get(position).getValue());
                        constructionType = dataSet.get(position).getId();
                        break;
                    case "lead_status":
                        existingLeadStatusButtonSelectText.setVisibility(View.VISIBLE);
                        existingLeadStatusButtonSelectText.setText(dataSet.get(position).getValue());
                        leadStatus = dataSet.get(position).getId();
                        break;
                    case "requirement_type":
                        existingRequirementTypeButtonSelectText.setVisibility(View.VISIBLE);
                        existingRequirementTypeButtonSelectText.setText(dataSet.get(position).getValue());
                        requirementType = dataSet.get(position).getId();

                        if (dataSet.get(position).getId().equalsIgnoreCase("EXW")) {
                            existingExWorksButtonLayout.setVisibility(View.VISIBLE);
                            existingFosEditTextLayout.setVisibility(View.GONE);
                        } else if (dataSet.get(position).getId().equalsIgnoreCase("FOS")) {
                            existingExWorksButtonLayout.setVisibility(View.GONE);
                            existingFosEditTextLayout.setVisibility(View.VISIBLE);
                        } else {
                            existingExWorksButtonLayout.setVisibility(View.GONE);
                            existingFosEditTextLayout.setVisibility(View.GONE);
                        }
                        break;
                    case "ex_work":
                        existingExWorksButtonSelectText.setVisibility(View.VISIBLE);
                        existingExWorksButtonSelectText.setText(dataSet.get(position).getValue());
                        exWorks = dataSet.get(position).getId();
                        break;
                    case "requirement_timing":
                        existingRequirementTimingButtonSelectText.setVisibility(View.VISIBLE);
                        existingRequirementTimingButtonSelectText.setText(dataSet.get(position).getValue());
                        requirementTiming = dataSet.get(position).getId();
                        break;
                }
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
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
        ((LeadGenerationActivity) mContext).runOnUiThread(() -> {
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
                if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt_to")) {
                    TYPE_OF_USER = "HOS";
                    ((LeadGenerationActivity) mContext).runOnUiThread(() -> {
                        leadTypeRadioGroupLayout.setVisibility(View.GONE);
                        existingGenerationLayout.setVisibility(View.VISIBLE);
                        existingLeadActionButton.setVisibility(View.VISIBLE);
                        submitButton.setVisibility(View.GONE);
                    });
                    new TRANS_OldLead_AsyncTask(mContext, "hos").execute();
                } else if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt")) {
                    TYPE_OF_USER = "SO";
                    ((LeadGenerationActivity) mContext).runOnUiThread(() -> {
                        leadTypeRadioGroupLayout.setVisibility(View.VISIBLE);
                        existingLeadActionButton.setVisibility(View.GONE);
                    });
                    new TRANS_OldLead_AsyncTask(mContext, "so").execute();
                } else {
                    progressDialogClose();
                    ((LeadGenerationActivity) mContext).runOnUiThread(LeadGenerationActivity.this::finish);
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Please contact to Admin.", Toast.LENGTH_LONG).show();
            }
        }
    }

    // All Old Lead API
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
                    String url = BaseUrl.sbDevUrl + "api/leadmaster/?";
                    if (dataTyps.equalsIgnoreCase("hos")) {
                        url = url + "assigned_to=" + emp_code;
                    } else {
                        url = url + "emp_code=" + emp_code;
                    }
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
                }
            } catch (Exception e) {
                Toast.makeText(mContext, "Please contact to Admin.", Toast.LENGTH_LONG).show();
            }
            progressDialogClose();
        }
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
                progressDialogClose();
            } catch (IOException ex) {
                progressDialogClose();
            }
        }).start();
    }

    // All Sold to Party List API
    public void _DOWNLOAD_sold_to_party(String emp_code) {
        progressDialogOpen("Download Sold to Party list ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/ptblcustomermasterlist/?customer_type=sold&emp_code=" + emp_code;
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
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            PartyDataList temp = new PartyDataList();
                            temp.setCode(RowData[1]);
                            temp.setName(RowData[4]);
                            temp.setCustomerCode(RowData[1]);
                            temp.setPhoneNo(RowData[20]);
                            temp.setDistrict(RowData[14]);
                            temp.setState(RowData[11]);
                            temp.setAddress(RowData[9]);
                            soldToPartyList.add(temp);
                            temp = null;
                        }
                    }
                }
                buffer.close();
                _DOWNLOAD_ship_to_party(emp_code);
            } catch (IOException ex) {
                progressDialogClose();
            }
        }).start();
    }

    // All Ship to Party List API
    public void _DOWNLOAD_ship_to_party(String emp_code) {
        progressDialogUpdate("Download Ship to Party list ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.sbDevUrl + "api/ptblcustomermasterlist/?customer_type=ship&emp_code=" + emp_code;
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
                String line = "";
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            PartyDataList temp = new PartyDataList();
                            temp.setCode(RowData[1]);
                            temp.setName(RowData[4]);
                            temp.setCustomerCode(RowData[1]);
                            temp.setPhoneNo(RowData[20]);
                            temp.setDistrict(RowData[14]);
                            temp.setState(RowData[11]);
                            temp.setAddress(RowData[9]);
                            shipToPartyList.add(temp);
                            temp = null;
                        }
                    }
                }
                buffer.close();
                _DOWNLOAD_assigned_to(emp_code);
            } catch (IOException ex) {
                progressDialogClose();
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
                _DOWNLOAD_product_list();
            } catch (IOException ex) {
                progressDialogClose();
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
                _DOWNLOAD_state_list();
            } catch (IOException ex) {
                progressDialogClose();
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
                _DOWNLOAD_district_list();
            } catch (IOException ex) {
                progressDialogClose();
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
                progressDialogClose();
            } catch (IOException ex) {
                progressDialogClose();
            }
        }).start();
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

    // All Lead Source List
    private void dataSetLeadSourceList() {
        leadSourceList = new ArrayList<>();

        String[][] data = {
                {"ONLINE (WHATSAPP, E-MAIL, CALL)", "Online (WhatsApp, E-mail, Call)"},
                {"OFFLINE (VISIT TO CLIENT SITE/OFFICE)", "Offline (Visit to Client Site/Office)"},
                {"OFFLINE (CLIENT'S VISIT TO STAR OFFICE)", "Offline (Client's Visit to Star Office)"},
                {"ONLINE (TENDERS ETC.)", "Online (Tenders Etc.)"},
                {"PROJECT INFORMATION FROM OTHER SOURCES", "Project Information from Other Sources"}
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

    // All Lead Status List
    private void dataSetLeadStatusList() {
        leadStatusList = new ArrayList<>();

        String[][] data = {
                {"HOT", "Hot"},
                {"WARM", "Warm"},
                {"COLD", "Cold"}
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

    // All Lead Action List
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
    // ======================================== //


    // ==================== Main Request for this page ==================== //
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
                surveyOutput.put("qty_req", potentialOfSite);
                surveyOutput.put("current_brand_used", currentBrandUsed);
                surveyOutput.put("exp_rate_per_bag", expectedRatePerBag);
                surveyOutput.put("contact_person_name", contactPersonName);
                surveyOutput.put("designation", contactPersonDesignation);
                surveyOutput.put("contact_number", contactPersonNumber);
                surveyOutput.put("mail_id", contactPersonMail);
                surveyOutput.put("mode", modeOfPayment);
                surveyOutput.put("credit_terms", creditTerms);
                surveyOutput.put("acc_block_is_required", aacBlockRequiredStatus);
                surveyOutput.put("category_type_construction", constructionType);
                surveyOutput.put("lead_status", leadStatus);
                surveyOutput.put("next_visit_date", nextVisitDate);
                surveyOutput.put("lead_remarks", salesOfficerRemarks);
                surveyOutput.put("assigned_to", assignedTo);
                surveyOutput.put("r_timing", requirementTiming);
                surveyOutput.put("sold_to_party", soldToPartyCode);
                surveyOutput.put("ship_to_party", shipToPartyCode);
                surveyOutput.put("current_price", currentPriceStarRsPerBag);
                surveyOutput.put("current_price_competitor", currentPriceCompetitorRsPerBag);
                surveyOutput.put("payment", modeOfPayment);
                surveyOutput.put("party_name", soldToPartyName);
                surveyOutput.put("branch", soldToPartyAddress);
                surveyOutput.put("district", soldToPartyDistricts.toUpperCase());
                surveyOutput.put("state", soldToPartyState.toUpperCase());
                surveyOutput.put("month_qty", quotationQuantity);
                surveyOutput.put("incoterms", requirementType);
                if (requirementType.equalsIgnoreCase("exw")) {
                    surveyOutput.put("serving_location", exWorks);
                } else if (requirementType.equalsIgnoreCase("fos")) {
                    surveyOutput.put("serving_location", fosText);
                } else {
                    surveyOutput.put("serving_location", "");
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
                otherInfo.put("next_visit_date", nextVisitDate);
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

                Log.d("TAG", "requestForLeadGeneration: "+mainObject.toString());

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
                ((LeadGenerationActivity) mContext).runOnUiThread(() -> {
                    ((LeadGenerationActivity) mContext).successMessageAndGotoPreviousPageForNewLeadGeneration();
                });

            } catch (Exception ignored) {}
        }).start();
    }

    // Request For Lead Update By SO
    private void requestForUpdaterLeadGeneration() {
        progressDialogOpen("Uploading data ...");
        new Thread(() -> {
            try {
                JSONObject mainObject = new JSONObject();

                mainObject.put("type_lead", segment);
                mainObject.put("lead_type", leadSource);
                mainObject.put("product_packaging", productPackaging.toUpperCase());
                mainObject.put("qty_req", potentialOfSite);
                mainObject.put("month_qty", quotationQuantity);
                mainObject.put("current_brand_used", currentBrandUsed);
                mainObject.put("exp_rate_per_bag", expectedRatePerBag);
                mainObject.put("current_price", currentPriceStarRsPerBag);
                mainObject.put("current_price_competitor", currentPriceCompetitorRsPerBag);
                mainObject.put("contact_person_name", contactPersonName);
                mainObject.put("designation", contactPersonDesignation);
                mainObject.put("contact_number", contactPersonNumber);
                mainObject.put("mail_id", contactPersonMail);
                mainObject.put("mode", modeOfPayment);
                mainObject.put("credit_terms", creditTerms);
                mainObject.put("acc_block_is_required", aacBlockRequiredStatus);
                mainObject.put("category_type_construction", constructionType);
                mainObject.put("lead_status", leadStatus);
                mainObject.put("next_visit_date", nextVisitDate);
                mainObject.put("incoterms", requirementType);
                if (requirementType.equalsIgnoreCase("exw")) {
                    mainObject.put("serving_location", exWorks);
                } else if (requirementType.equalsIgnoreCase("fos")) {
                    mainObject.put("serving_location", fosText);
                } else {
                    mainObject.put("serving_location", "");
                }
                mainObject.put("lead_remarks", salesOfficerRemarks);
                mainObject.put("assigned_to", assignedTo);
                mainObject.put("r_timing", requirementTiming);
                mainObject.put("lead_action", "PENDING");

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadmaster/" + existingTextUniqueLeadID.getText().toString().trim() + "/")
                        .put(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();
                ((LeadGenerationActivity) mContext).runOnUiThread(() -> {
                    ((LeadGenerationActivity) mContext).successMessageAndCleanFieldForExistingLeadGeneration();
                });

            } catch (Exception ignored) {}
        }).start();
    }

    // Request For HOS Status Update
    private void requestForUpdaterLeadGenerationStatusHOS() {
        progressDialogOpen("Uploading data ...");
        new Thread(() -> {
            try {
                JSONObject mainObject = new JSONObject();
                mainObject.put("lead_action", LEAD_STATUS_UPDATE_FROM_HOS);
                mainObject.put("approved_price", editTextSendingQuotaion.getText().toString().trim());
                mainObject.put("remarks", editTextHOSRemarks.getText().toString().trim());

                // -----------------------------
                // Create request
                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                RequestBody body = RequestBody.create(mediaType, mainObject.toString());

                Request request = new Request.Builder()
                        .url(BaseUrl.sbDevUrl + "api/leadmaster/" + existingTextUniqueLeadID.getText().toString().trim() + "/")
                        .put(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();
                ((LeadGenerationActivity) mContext).runOnUiThread(() -> {
                    ((LeadGenerationActivity) mContext).successMessageAndCleanFieldForExistingLeadGeneration();
                });

            } catch (Exception ignored) {}
        }).start();
    }
    // ======================================== //
}