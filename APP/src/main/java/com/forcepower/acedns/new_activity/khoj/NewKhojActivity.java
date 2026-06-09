package com.forcepower.acedns.new_activity.khoj;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.text.Editable;
import android.text.Html;
import android.text.InputFilter;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.ContextCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.new_activity.khoj.adapter.DataSetAdapter;
import com.forcepower.acedns.new_activity.khoj.adapter.DistrictAdapter;
import com.forcepower.acedns.new_activity.khoj.adapter.PersonAdapter;
import com.forcepower.acedns.new_activity.khoj.adapter.SiteAdapter;
import com.forcepower.acedns.new_activity.khoj.data_set.DataSet;
import com.forcepower.acedns.new_activity.khoj.data_set.DistrictDataSet;
import com.forcepower.acedns.new_activity.khoj.data_set.PersonDataSet;
import com.forcepower.acedns.new_activity.khoj.data_set.SiteDataSet;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.LocationTracker;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Objects;

public class NewKhojActivity extends AceDnsParentActivity implements View.OnClickListener {
    // Back Buttons
    Button backButton;
    ImageView customerInputBackButton;

    // Main Button
    Button submitButton;

    // New Site Lead
    LinearLayout newSiteLayout;

    LinearLayout otherBrandNameLayout;

    Button routeNameButton, meetingPersonButton, branchButton, stateButton, districtButton, engineerRegInStellerButton, siteSegmentButton, projectSegmentButton, constructionTypeButton, currentStageOfConstructionButton,
            cementBrandUsedButton, decisionMakerButton, productDemoButton, visitTypeButton, purchaseDealerNameButton, approvedByButton;
    TextView textRouteName, textMeetingPerson, textBranch, textState, textDistrict, textEngineerRegInSteller, textSiteSegment, textProjectSegment, textConstructionType, textCurrentStageOfConstruction, textCementBrandUsed,
            textDecisionMaker, textProductDemo, textVisitType, textPurchaseDealerName, textApprovedBy;

    TextView textCustomerName, textMeetingPersonContactNumber, textSiteName, textCustomerContactNumber, textFullAddress, textContractorName, textContractorNumber, textEngineerName, textEngineerNumber, textSitePotential,
            textOtherBrandName, textPricePerBag, textConsumedTillDate, textEstimatedRequirement, textBuildUpArea, textRemarks, textOrderQuantity;
    EditText editTextCustomerName, editTextMeetingPersonContactNumber, editTextSiteName, editTextCustomerContactNumber, editTextFullAddress, editTextContractorName, editTextContractorNumber, editTextEngineerName,
            editTextEngineerNumber, editTextSitePotential, editTextOtherBrandName, editTextPricePerBag, editTextConsumedTillDate, editTextEstimatedRequirement, editTextBuildUpArea, editTextRemarks, editTextOrderQuantity;

    LinearLayout newDeliveryDatePicker;
    TextView textDeliveryDate;
    TextView showTextDeliveryDate;

    // Existing Lead
    LinearLayout existingSiteLayout;

    LinearLayout editOtherBrandLayout;

    Button edRouteNameButton, edSiteNameNameButton, edContractorButton, edBranchButton, edStateButton, edDistrictButton, edEngineerInStellarButton, edSiteSegmentButton, edProjectSegmentButton, edConstructionTypeButton,
            edCurrentConstructionStatusButton, edCementUsedButton, edDecisionMarketButton, edProductDemoButton, edVisitTypeButton, edPurchaseDealerNameButton, edApprovedByButton;
    TextView edTextRouteName, edTextSiteNameName, edTextContractor, edTextBranch, edTextState, edTextDistrict, edTextEngineerInStellar, edTextSiteSegment, edTextProjectSegment, edTextConstructionType, edTextCurrentConstructionStatus,
            edTextCementUsed, edTextDecisionMarket, edTextProductDemo, edTextVisitType, edTextPurchaseDealerName, edTextApprovedBy;

    TextView edTextCustomerName, edTextMeetingPersonName, edTextMeetingPersonNumber, edTextFullAddress, edTextContractorName, edTextContractorNumber, edTextEngineerName, edTextEngineerNumber, edTextSitePotential,
            edTextOtherBrandName, edTextPricePerBag, edTextConsumedTillDate, edTextEstimatedRequirement, edTextBuildUpArea, edTextRemarks, edTextOrderQuantity;
    EditText edEditTextCustomerName, edEditTextMeetingPersonName, edEditTextMeetingPersonNumber, edEditTextFullAddress, edEditTextContractorName, edEditTextContractorNumber, edEditTextEngineerName, edEditTextEngineerNumber,
            edEditTextSitePotential, edEditTextOtherBrandName, edEditTextPricePerBag, edEditTextConsumedTillDate, edEditTextEstimatedRequirement, edEditTextBuildUpArea, edEditTextRemarks, edEditTextOrderQuantity;

    LinearLayout updateDeliveryDatePicker;
    TextView edTextDeliveryDate, updateCustomerNumber;
    TextView edShowTextDeliveryDate;

    // POPUP
    RelativeLayout popupLayout;

    // Customer Details Input Popup
    LinearLayout customerDetailsInputPopupLayout;
    ImageView popupBackIcon;
    Button popupRouteNameButton;
    TextView popupTextRouteName;
    EditText popupEditTextCustomerNumber;
    Button popupSubmitButton;

    // Customer Details Show Popup
    LinearLayout customerDetailsShowPopupLayout;
    TextView popupCustomerNumber, popupCustomerNumberAlreadyRegisterText;
    Button popupNewCreateButton, popupVisitExistingButton;

    //Customer Details Change Route Popup
    LinearLayout customerDetailsChangeRoutePopupLayout;
    ImageView popupChangeRouteBackIcon;
    TextView popupChangeRouteCustomerNumber;
    Button popupChangeRouteNameButton;
    TextView popupChangeTextRouteName;
    Button popupChangeAndUpdateSiteButton;


    //Other veriable
    Context mContext;
    ProgressDialog progressDialog;

    // first Popup Data set
    String customerContactNumber, dateOfDelivery, latitude = "", longitude = "";
    Boolean isAlreadyRegisterInSelectedRoute = false, isNewAddClick = false, isAlreadyRegisterInOtherRoute = false;

    ArrayList<DataSet> routeList = new ArrayList<>();
    DataSet selectRouteData = new DataSet();

    // New site
    ArrayList<PersonDataSet> meetingPersonDetailsList = new ArrayList<>();
    PersonDataSet selectMeetingPersonDetails = new PersonDataSet();

    ArrayList<DataSet> branchDataList = new ArrayList<>();
    DataSet selectBranchData = new DataSet();

    ArrayList<DataSet> stateDataList = new ArrayList<>();
    DataSet selectStateData = new DataSet();

    ArrayList<DistrictDataSet> districtDataList = new ArrayList<>();
    DistrictDataSet selectDistrictData = new DistrictDataSet();

    ArrayList<DataSet> isRegisterDataList = new ArrayList<>();
    DataSet selectIsRegisterData = new DataSet();

    ArrayList<DataSet> siteSegmentList = new ArrayList<>();
    DataSet selectSiteSegmentData = new DataSet();

    ArrayList<DataSet> projectSegmentList = new ArrayList<>();
    DataSet selectProjectSegmentData = new DataSet();

    ArrayList<DataSet> constructionTypeList = new ArrayList<>();
    DataSet selectConstructionTypeData = new DataSet();

    ArrayList<DataSet> currentStageOfConstructionList = new ArrayList<>();
    DataSet selectCurrentStageOfConstructionData = new DataSet();

    ArrayList<DataSet> currentBrandList = new ArrayList<>();
    DataSet selectCurrentBrandData = new DataSet();

    ArrayList<PersonDataSet> decisionMakerList = new ArrayList<>();
    PersonDataSet selectDecisionMakerData = new PersonDataSet();

    ArrayList<DataSet> productList = new ArrayList<>();
    DataSet selectProductData = new DataSet();

    ArrayList<DataSet> visitTypeList = new ArrayList<>();
    DataSet selectVisitTypeData = new DataSet();

    ArrayList<DataSet> visitTypeStarList = new ArrayList<>();
    DataSet selectVisitTypeStarData = new DataSet();

    ArrayList<DataSet> visitTypeNonStarList = new ArrayList<>();
    DataSet selectVisitTypeNonStarData = new DataSet();

    ArrayList<PersonDataSet> dealerList = new ArrayList<>();
    PersonDataSet selectDealerData = new PersonDataSet();

    ArrayList<PersonDataSet> approvedByList = new ArrayList<>();
    PersonDataSet selectApprovedByData = new PersonDataSet();

    ArrayList<SiteDataSet> listOfSite = new ArrayList<>();
    SiteDataSet siteDataSet = new SiteDataSet();


    String surveymenudetails;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_khoj);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

        surveymenudetails = getIntent().getStringExtra("SURVEYSUBMENUDETAILS");

        mContext = NewKhojActivity.this;
        init();

        LocationTracker locationTracker = new LocationTracker(this);
        locationTracker.checkLocationUpdateSharing();
        new Handler().postDelayed(() -> {
            latitude = Constants.currentLat;
            longitude = Constants.currentLong;
        }, 2000);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onClick(View view) {
        // Back Press
        if (view == backButton) {
            Log.d("TAG", "_DOWNLOAD_ onClick: backButton");
            gotoPreviousPage();
        }
        if (view == popupBackIcon) {
            Log.d("TAG", "_DOWNLOAD_ onClick: customerInputBackButton");
            gotoPreviousPage();
        }
        if (view == popupChangeRouteBackIcon) {
            openSecondStep();
        }

        // customer details input popup
        if (view == popupRouteNameButton) {
            showDataSetPopupDialog(routeList, "Select Route", 1, "popup_route_name");
        }
        if (view == popupSubmitButton) {
            popupCustomerNumber.setText("+91 " + popupEditTextCustomerNumber.getText().toString().trim());
            checkAndSearchCustomer();
        }

        // customer details show popup
        if (view == popupNewCreateButton) {
            isNewAddClick = true;
            textRouteName.setText(selectRouteData.getDataTitle());
            textRouteName.setVisibility(View.VISIBLE);
            editTextCustomerContactNumber.setText(popupEditTextCustomerNumber.getText().toString().trim());
            editTextCustomerContactNumber.setEnabled(false);
            new TRANS_MeetingPersonList_AsyncTask(NewKhojActivity.this).execute();
        }
        if (view == popupVisitExistingButton) {
            isNewAddClick = false;
            edTextRouteName.setText(selectRouteData.getDataTitle());
            edTextRouteName.setVisibility(View.VISIBLE);
            updateCustomerNumber.setText("+91 " + popupEditTextCustomerNumber.getText().toString().trim());
            if (isAlreadyRegisterInSelectedRoute) {
                new TRANS_SiteName_AsyncTask(NewKhojActivity.this).execute();
            } else {
                new TRANS_CustomerRouteNameList_AsyncTask(NewKhojActivity.this).execute();
            }
        }

        // customer change route popup
        if (view == popupChangeRouteNameButton) {
            showDataSetPopupDialog(routeList, "Select Route", 1, "popup_route_name_1");
        }
        if (view == popupChangeAndUpdateSiteButton) {
            updateCheckAndSearchCustomer();
        }

        // New site create
        if (view == routeNameButton) {
            Toast.makeText(mContext, "Already route set.", Toast.LENGTH_LONG).show();
        }
        if (view == meetingPersonButton) {
            showPersonDataSetPopupDialog(meetingPersonDetailsList, "Select Meeting Person", 1, "meeting_person");
        }
        if (view == branchButton) {
            showDataSetPopupDialog(branchDataList, "Select Branch", 1, "branch");
        }
        if (view == stateButton) {
            showDataSetPopupDialog(stateDataList, "Select State", 1, "state");
        }
        if (view == districtButton) {
            showDistrictListDialog("Select District");
        }
        if (view == engineerRegInStellerButton) {
            showDataSetPopupDialog(isRegisterDataList, "Select Status", 0, "reg_in_stellar");
        }
        if (view == siteSegmentButton) {
            showDataSetPopupDialog(siteSegmentList, "Select Site Segment", 1, "site_segment");
        }
        if (view == projectSegmentButton) {
            showDataSetPopupDialog(projectSegmentList, "Select Project Segment", 1, "project_segment");
        }
        if (view == constructionTypeButton) {
            showDataSetPopupDialog(constructionTypeList, "Select Type of Construction", 1, "construction_type");
        }
        if (view == currentStageOfConstructionButton) {
            showDataSetPopupDialog(currentStageOfConstructionList, "Select Current Stage", 1, "construction_stage");
        }
        if (view == cementBrandUsedButton) {
            showDataSetPopupDialog(currentBrandList, "Select Current Brand Used", 1, "cement_brand");
        }
        if (view == decisionMakerButton) {
            showPersonDataSetPopupDialog(decisionMakerList, "Select Decision Maker", 1, "decision_maker");
        }
        if (view == productDemoButton) {
            showDataSetPopupDialog(productList, "Select Product", 1, "product_demo");
        }
        if (view == visitTypeButton) {
            showDataSetPopupDialog(visitTypeList, "Select Visit Type", 1, "visit_type");
        }
        if (view == newDeliveryDatePicker) {
            nextVisitDatePicker();
        }
        if (view == purchaseDealerNameButton) {
            showPersonDataSetPopupDialog(dealerList, "Select Dealer/RSSD", 1, "dealer_rssd");
        }
        if (view == approvedByButton) {
            showPersonDataSetPopupDialog(approvedByList, "Select Approved by", 1, "approved_by");
        }

        // update site
        if (view == edRouteNameButton) {
            Toast.makeText(mContext, "Can not editable Route Name.", Toast.LENGTH_LONG).show();
        }
        if (view == edSiteNameNameButton) {
            showSiteListDialog(listOfSite, "Select Site Name", 1);
        }
        if (view == edContractorButton) {
            Toast.makeText(mContext, "Can not editable Contractor.", Toast.LENGTH_LONG).show();
        }
        if (view == edBranchButton) {
            Toast.makeText(mContext, "Can not editable Branch.", Toast.LENGTH_LONG).show();
        }
        if (view == edStateButton) {
            Toast.makeText(mContext, "Can not editable State.", Toast.LENGTH_LONG).show();
        }
        if (view == edDistrictButton) {
            Toast.makeText(mContext, "Can not editable District.", Toast.LENGTH_LONG).show();
        }
        if (view == edEngineerInStellarButton) {
            showDataSetPopupDialog(isRegisterDataList, "Select Status", 0, "ed_reg_in_stellar");
        }
        if (view == edSiteSegmentButton) {
            Toast.makeText(mContext, "Can not editable Site Segment.", Toast.LENGTH_LONG).show();
        }
        if (view == edProjectSegmentButton) {
            showDataSetPopupDialog(projectSegmentList, "Select Project Segment", 1, "ed_project_segment");
        }
        if (view == edConstructionTypeButton) {
            Toast.makeText(mContext, "Can not editable Construction Type.", Toast.LENGTH_LONG).show();
        }
        if (view == edCurrentConstructionStatusButton) {
            showDataSetPopupDialog(currentStageOfConstructionList, "Select Current Stage", 1, "ed_construction_stage");
        }
        if (view == edCementUsedButton) {
            showDataSetPopupDialog(currentBrandList, "Select Current Brand Used", 1, "ed_cement_brand");
        }
        if (view == edDecisionMarketButton) {
            Toast.makeText(mContext, "Can not editable Decision Maker.", Toast.LENGTH_LONG).show();
        }
        if (view == edProductDemoButton) {
            Toast.makeText(mContext, "Can not editable Product Demo.", Toast.LENGTH_LONG).show();
        }
        if (view == edVisitTypeButton) {
            showDataSetPopupDialog(visitTypeList, "Select Visit Type", 1, "ed_visit_type");
        }
        if (view == edPurchaseDealerNameButton) {
            Toast.makeText(mContext, "Can not editable Purchase Dealer or RSSD.", Toast.LENGTH_LONG).show();
        }
        if (view == edApprovedByButton) {
            Toast.makeText(mContext, "Can not editable Approved By.", Toast.LENGTH_LONG).show();
        }
        if (view == updateDeliveryDatePicker) {
            Toast.makeText(mContext, "Can not editable Delivery Date.", Toast.LENGTH_LONG).show();
        }


        // Main button of the page
        if (view == submitButton) {
            if (isNewAddClick) {
                checkAllDataForNewSiteUpload();
            } else {
                checkAllDataForUpdateSiteUpload();
            }
        }
    }

    // all main layout
    private void init() {
        newSiteLayout = findViewById(R.id.newSiteLayout);
        existingSiteLayout = findViewById(R.id.existingSiteLayout);
        popupLayout = findViewById(R.id.popupLayout);
        customerDetailsInputPopupLayout = findViewById(R.id.customerDetailsInputPopupLayout);
        customerDetailsShowPopupLayout = findViewById(R.id.customerDetailsShowPopupLayout);

        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        customerInputBackButton = findViewById(R.id.customerInputBackButton);
        customerInputBackButton.setOnClickListener(this);

        newSiteLayout.setVisibility(View.GONE);
        existingSiteLayout.setVisibility(View.GONE);
        popupLayout.setVisibility(View.GONE);
        customerDetailsInputPopupLayout.setVisibility(View.GONE);
        customerDetailsShowPopupLayout.setVisibility(View.GONE);

        submitButton = findViewById(R.id.submitButton);
        submitButton.setOnClickListener(this);

        initNewSiteAllView();
    }

    // new site create
    private void initNewSiteAllView() {
        routeNameButton = findViewById(R.id.routeNameButton);
        meetingPersonButton = findViewById(R.id.meetingPersonButton);
        branchButton = findViewById(R.id.branchButton);
        stateButton = findViewById(R.id.stateButton);
        districtButton = findViewById(R.id.districtButton);
        engineerRegInStellerButton = findViewById(R.id.engineerRegInStellerButton);
        siteSegmentButton = findViewById(R.id.siteSegmentButton);
        projectSegmentButton = findViewById(R.id.projectSegmentButton);
        constructionTypeButton = findViewById(R.id.constructionTypeButton);
        currentStageOfConstructionButton = findViewById(R.id.currentStageOfConstructionButton);
        cementBrandUsedButton = findViewById(R.id.cementBrandUsedButton);
        decisionMakerButton = findViewById(R.id.decisionMakerButton);
        productDemoButton = findViewById(R.id.productDemoButton);
        visitTypeButton = findViewById(R.id.visitTypeButton);
        purchaseDealerNameButton = findViewById(R.id.purchaseDealerNameButton);
        approvedByButton = findViewById(R.id.approvedByButton);

        textRouteName = findViewById(R.id.textRouteName);
        textMeetingPerson = findViewById(R.id.textMeetingPerson);
        textBranch = findViewById(R.id.textBranch);
        textState = findViewById(R.id.textState);
        textDistrict = findViewById(R.id.textDistrict);
        textEngineerRegInSteller = findViewById(R.id.textEngineerRegInSteller);
        textSiteSegment = findViewById(R.id.textSiteSegment);
        textProjectSegment = findViewById(R.id.textProjectSegment);
        textConstructionType = findViewById(R.id.textConstructionType);
        textCurrentStageOfConstruction = findViewById(R.id.textCurrentStageOfConstruction);
        textCementBrandUsed = findViewById(R.id.textCementBrandUsed);
        textDecisionMaker = findViewById(R.id.textDecisionMaker);
        textProductDemo = findViewById(R.id.textProductDemo);
        textVisitType = findViewById(R.id.textVisitType);
        textPurchaseDealerName = findViewById(R.id.textPurchaseDealerName);
        textApprovedBy = findViewById(R.id.textApprovedBy);

        textCustomerName = findViewById(R.id.textCustomerName);
        textMeetingPersonContactNumber = findViewById(R.id.textMeetingPersonContactNumber);
        textSiteName = findViewById(R.id.textSiteName);
        textCustomerContactNumber = findViewById(R.id.textCustomerContactNumber);
        textFullAddress = findViewById(R.id.textFullAddress);
        textContractorName = findViewById(R.id.textContractorName);
        textContractorNumber = findViewById(R.id.textContractorNumber);
        textEngineerName = findViewById(R.id.textEngineerName);
        textEngineerNumber = findViewById(R.id.textEngineerNumber);
        textSitePotential = findViewById(R.id.textSitePotential);
        textOtherBrandName = findViewById(R.id.textOtherBrandName);
        textPricePerBag = findViewById(R.id.textPricePerBag);
        textConsumedTillDate = findViewById(R.id.textConsumedTillDate);
        textEstimatedRequirement = findViewById(R.id.textEstimatedRequirement);
        textBuildUpArea = findViewById(R.id.textBuildUpArea);
        textRemarks = findViewById(R.id.textRemarks);
        textOrderQuantity = findViewById(R.id.textOrderQuantity);

        editTextCustomerName = findViewById(R.id.editTextCustomerName);
        editTextMeetingPersonContactNumber = findViewById(R.id.editTextMeetingPersonContactNumber);
        editTextSiteName = findViewById(R.id.editTextSiteName);
        editTextCustomerContactNumber = findViewById(R.id.editTextCustomerContactNumber);
        editTextFullAddress = findViewById(R.id.editTextFullAddress);
        editTextContractorName = findViewById(R.id.editTextContractorName);
        editTextContractorNumber = findViewById(R.id.editTextContractorNumber);
        editTextEngineerName = findViewById(R.id.editTextEngineerName);
        editTextEngineerNumber = findViewById(R.id.editTextEngineerNumber);
        editTextSitePotential = findViewById(R.id.editTextSitePotential);
        editTextOtherBrandName = findViewById(R.id.editTextOtherBrandName);
        editTextPricePerBag = findViewById(R.id.editTextPricePerBag);
        editTextConsumedTillDate = findViewById(R.id.editTextConsumedTillDate);
        editTextEstimatedRequirement = findViewById(R.id.editTextEstimatedRequirement);
        editTextBuildUpArea = findViewById(R.id.editTextBuildUpArea);
        editTextRemarks = findViewById(R.id.editTextRemarks);
        editTextOrderQuantity = findViewById(R.id.editTextOrderQuantity);

        newDeliveryDatePicker = findViewById(R.id.newDeliveryDatePicker);
        textDeliveryDate = findViewById(R.id.textDeliveryDate);
        showTextDeliveryDate = findViewById(R.id.showTextDeliveryDate);

        otherBrandNameLayout = findViewById(R.id.otherBrandNameLayout);
        otherBrandNameLayout.setVisibility(View.GONE);

        setTitleForNewSiteTextView();
    }

    private void setTitleForNewSiteTextView() {
        textCustomerName.setText(Html.fromHtml("Customer Name <font color='#FF0000'>*</font>"));
        textMeetingPersonContactNumber.setText(Html.fromHtml("Meeting Person Contact No. <font color='#FF0000'>*</font>"));
        textSiteName.setText(Html.fromHtml("Site Name <font color='#FF0000'>*</font>"));
        textCustomerContactNumber.setText(Html.fromHtml("Customer Contact No. <font color='#FF0000'>*</font>"));
        textFullAddress.setText(Html.fromHtml("Full Address <font color='#FF0000'>*</font>"));
        textContractorName.setText(Html.fromHtml("Petty Contractor - Head Mason Name"));
        textContractorNumber.setText(Html.fromHtml("Petty Contractor - Head Mason Contact No."));
        textEngineerName.setText(Html.fromHtml("Engineer Name"));
        textEngineerNumber.setText(Html.fromHtml("Engineer Contact No."));
        textSitePotential.setText(Html.fromHtml("Site Potential (No. of Bags) <font color='#FF0000'>*</font>"));
        textOtherBrandName.setText(Html.fromHtml("Name of the OTHER Brand"));
        textPricePerBag.setText(Html.fromHtml("Price Per Bag (Rs.)"));
        textConsumedTillDate.setText(Html.fromHtml("Consumed Till Date (No. of Bags)"));
        textEstimatedRequirement.setText(Html.fromHtml("Estimated Requirement (No. of Bags) <font color='#FF0000'>*</font>"));
        textBuildUpArea.setText(Html.fromHtml("Built up Area (Sq. ft) <font color='#FF0000'>*</font>"));
        textRemarks.setText(Html.fromHtml("Overall Remarks"));
        textDeliveryDate.setText(Html.fromHtml("Requested Date of Delivery"));
        showTextDeliveryDate.setText(Html.fromHtml("<font color='#666666'>DD-MM-YYYY</font>"));
        textOrderQuantity.setText(Html.fromHtml("No. of Bags Ordered"));

        setHintForNewSiteEditText();
    }

    private void setHintForNewSiteEditText() {
        editTextCustomerName.setHint(Html.fromHtml("<font color='#666666'>Customer Name (Mandatory)</font>"));
        editTextMeetingPersonContactNumber.setHint(Html.fromHtml("<font color='#666666'>Meeting Person Contact No. (Mandatory)</font>"));
        editTextSiteName.setHint(Html.fromHtml("<font color='#666666'>Site Name (Mandatory)</font>"));
        editTextCustomerContactNumber.setHint(Html.fromHtml("<font color='#666666'>+91 XXXXXXXXXX (Mandatory)</font>"));
        editTextFullAddress.setHint(Html.fromHtml("<font color='#666666'>Full Address (Mandatory)</font>"));
        editTextContractorName.setHint(Html.fromHtml("<font color='#666666'>Petty Contractor - Head Mason Name</font>"));
        editTextContractorNumber.setHint(Html.fromHtml("<font color='#666666'>Petty Contractor - Head Mason Contact No.</font>"));
        editTextEngineerName.setHint(Html.fromHtml("<font color='#666666'>Engineer Name</font>"));
        editTextEngineerNumber.setHint(Html.fromHtml("<font color='#666666'>Engineer Contact No.</font>"));
        editTextSitePotential.setHint(Html.fromHtml("<font color='#666666'>Site Potential (Mandatory)</font>"));
        editTextOtherBrandName.setHint(Html.fromHtml("<font color='#666666'>Name of the OTHER Brand</font>"));
        editTextPricePerBag.setHint(Html.fromHtml("<font color='#666666'>Price Per Bag</font>"));
        editTextConsumedTillDate.setHint(Html.fromHtml("<font color='#666666'>Consumed Till Date</font>"));
        editTextEstimatedRequirement.setHint(Html.fromHtml("<font color='#666666'>Estimated Requirement (Mandatory)</font>"));
        editTextBuildUpArea.setHint(Html.fromHtml("<font color='#666666'>Built up Area (Mandatory)</font>"));
        editTextRemarks.setHint(Html.fromHtml("<font color='#666666'>Remarks</font>"));
        editTextOrderQuantity.setHint(Html.fromHtml("<font color='#666666'>No. of Bags Ordered</font>"));

        editTextMeetingPersonContactNumber.setInputType(InputType.TYPE_CLASS_PHONE);
        editTextContractorNumber.setInputType(InputType.TYPE_CLASS_PHONE);
        editTextEngineerNumber.setInputType(InputType.TYPE_CLASS_PHONE);
        editTextSitePotential.setInputType(InputType.TYPE_CLASS_PHONE);
        editTextPricePerBag.setInputType(InputType.TYPE_CLASS_PHONE);
        editTextConsumedTillDate.setInputType(InputType.TYPE_CLASS_PHONE);
        editTextEstimatedRequirement.setInputType(InputType.TYPE_CLASS_PHONE);
        editTextBuildUpArea.setInputType(InputType.TYPE_CLASS_PHONE);
        editTextOrderQuantity.setInputType(InputType.TYPE_CLASS_PHONE);

        editTextMeetingPersonContactNumber.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});
        editTextContractorNumber.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});
        editTextEngineerNumber.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});
        editTextSitePotential.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});
        editTextPricePerBag.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});
        editTextConsumedTillDate.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});
        editTextEstimatedRequirement.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});
        editTextBuildUpArea.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});
        editTextOrderQuantity.setFilters(new InputFilter[]{new InputFilter.LengthFilter(10)});

        setHintForNewSiteButton();
    }

    private void setHintForNewSiteButton() {
        routeNameButton.setText(Html.fromHtml("Route Name <font color='#FF0000'>*</font>"));
        meetingPersonButton.setText(Html.fromHtml("Meeting Person <font color='#FF0000'>*</font>"));
        branchButton.setText(Html.fromHtml("Branch <font color='#FF0000'>*</font>"));
        stateButton.setText(Html.fromHtml("State <font color='#FF0000'>*</font>"));
        districtButton.setText(Html.fromHtml("District <font color='#FF0000'>*</font>"));
        engineerRegInStellerButton.setText(Html.fromHtml("Engineer Regd. In Star Stellar"));
        siteSegmentButton.setText(Html.fromHtml("Site Segment <font color='#FF0000'>*</font>"));
        projectSegmentButton.setText(Html.fromHtml("Project Segment <font color='#FF0000'>*</font>"));
        constructionTypeButton.setText(Html.fromHtml("Type of Construction <font color='#FF0000'>*</font>"));
        currentStageOfConstructionButton.setText(Html.fromHtml("Current Stage of Construction <font color='#FF0000'>*</font>"));
        cementBrandUsedButton.setText(Html.fromHtml("Cement Brand Used <font color='#FF0000'>*</font>"));
        decisionMakerButton.setText(Html.fromHtml("Decision Maker"));
        productDemoButton.setText(Html.fromHtml("Product Demo"));
        visitTypeButton.setText(Html.fromHtml("Visit Type <font color='#FF0000'>*</font>"));
        purchaseDealerNameButton.setText(Html.fromHtml("Source of Purchase Dealer/RSSD (Name)"));
        approvedByButton.setText(Html.fromHtml("To Be Approved By"));

        setVisibilityForNewSiteTextView();
    }

    private void setVisibilityForNewSiteTextView() {
        textRouteName.setVisibility(View.GONE);
        textMeetingPerson.setVisibility(View.GONE);
        textBranch.setVisibility(View.GONE);
        textState.setVisibility(View.GONE);
        textDistrict.setVisibility(View.GONE);
        textEngineerRegInSteller.setVisibility(View.GONE);
        textSiteSegment.setVisibility(View.GONE);
        textProjectSegment.setVisibility(View.GONE);
        textConstructionType.setVisibility(View.GONE);
        textCurrentStageOfConstruction.setVisibility(View.GONE);
        textCementBrandUsed.setVisibility(View.GONE);
        textDecisionMaker.setVisibility(View.GONE);
        textProductDemo.setVisibility(View.GONE);
        textVisitType.setVisibility(View.GONE);
        textPurchaseDealerName.setVisibility(View.GONE);
        textApprovedBy.setVisibility(View.GONE);

        onClickFunctionNewSiteSetup();
    }

    private void onClickFunctionNewSiteSetup() {
        routeNameButton.setOnClickListener(this);
        meetingPersonButton.setOnClickListener(this);
        branchButton.setOnClickListener(this);
        stateButton.setOnClickListener(this);
        districtButton.setOnClickListener(this);
        engineerRegInStellerButton.setOnClickListener(this);
        siteSegmentButton.setOnClickListener(this);
        projectSegmentButton.setOnClickListener(this);
        constructionTypeButton.setOnClickListener(this);
        currentStageOfConstructionButton.setOnClickListener(this);
        cementBrandUsedButton.setOnClickListener(this);
        decisionMakerButton.setOnClickListener(this);
        productDemoButton.setOnClickListener(this);
        visitTypeButton.setOnClickListener(this);
        newDeliveryDatePicker.setOnClickListener(this);
        purchaseDealerNameButton.setOnClickListener(this);
        approvedByButton.setOnClickListener(this);

        initExistingSiteAllView();
    }


    // update site
    private void initExistingSiteAllView() {
        edRouteNameButton = findViewById(R.id.edRouteNameButton);
        edSiteNameNameButton = findViewById(R.id.edSiteNameNameButton);
        edContractorButton = findViewById(R.id.edContractorButton);
        edBranchButton = findViewById(R.id.edBranchButton);
        edStateButton = findViewById(R.id.edStateButton);
        edDistrictButton = findViewById(R.id.edDistrictButton);
        edEngineerInStellarButton = findViewById(R.id.edEngineerInStellarButton);
        edSiteSegmentButton = findViewById(R.id.edSiteSegmentButton);
        edProjectSegmentButton = findViewById(R.id.edProjectSegmentButton);
        edConstructionTypeButton = findViewById(R.id.edConstructionTypeButton);
        edCurrentConstructionStatusButton = findViewById(R.id.edCurrentConstructionStatusButton);
        edCementUsedButton = findViewById(R.id.edCementUsedButton);
        edDecisionMarketButton = findViewById(R.id.edDecisionMarketButton);
        edProductDemoButton = findViewById(R.id.edProductDemoButton);
        edVisitTypeButton = findViewById(R.id.edVisitTypeButton);
        edPurchaseDealerNameButton = findViewById(R.id.edPurchaseDealerNameButton);
        edApprovedByButton = findViewById(R.id.edApprovedByButton);

        edTextRouteName = findViewById(R.id.edTextRouteName);
        edTextSiteNameName = findViewById(R.id.edTextSiteNameName);
        edTextContractor = findViewById(R.id.edTextContractor);
        edTextBranch = findViewById(R.id.edTextBranch);
        edTextState = findViewById(R.id.edTextState);
        edTextDistrict = findViewById(R.id.edTextDistrict);
        edTextEngineerInStellar = findViewById(R.id.edTextEngineerInStellar);
        edTextSiteSegment = findViewById(R.id.edTextSiteSegment);
        edTextProjectSegment = findViewById(R.id.edTextProjectSegment);
        edTextConstructionType = findViewById(R.id.edTextConstructionType);
        edTextCurrentConstructionStatus = findViewById(R.id.edTextCurrentConstructionStatus);
        edTextCementUsed = findViewById(R.id.edTextCementUsed);
        edTextDecisionMarket = findViewById(R.id.edTextDecisionMarket);
        edTextProductDemo = findViewById(R.id.edTextProductDemo);
        edTextVisitType = findViewById(R.id.edTextVisitType);
        edTextPurchaseDealerName = findViewById(R.id.edTextPurchaseDealerName);
        edTextApprovedBy = findViewById(R.id.edTextApprovedBy);

        edTextCustomerName = findViewById(R.id.edTextCustomerName);
        edTextMeetingPersonName = findViewById(R.id.edTextMeetingPersonName);
        edTextMeetingPersonNumber = findViewById(R.id.edTextMeetingPersonNumber);
        edTextFullAddress = findViewById(R.id.edTextFullAddress);
        edTextContractorName = findViewById(R.id.edTextContractorName);
        edTextContractorNumber = findViewById(R.id.edTextContractorNumber);
        edTextEngineerName = findViewById(R.id.edTextEngineerName);
        edTextEngineerNumber = findViewById(R.id.edTextEngineerNumber);
        edTextSitePotential = findViewById(R.id.edTextSitePotential);
        edTextOtherBrandName = findViewById(R.id.edTextOtherBrandName);
        edTextPricePerBag = findViewById(R.id.edTextPricePerBag);
        edTextConsumedTillDate = findViewById(R.id.edTextConsumedTillDate);
        edTextEstimatedRequirement = findViewById(R.id.edTextEstimatedRequirement);
        edTextBuildUpArea = findViewById(R.id.edTextBuildUpArea);
        edTextRemarks = findViewById(R.id.edTextRemarks);
        edTextOrderQuantity = findViewById(R.id.edTextOrderQuantity);

        edEditTextCustomerName = findViewById(R.id.edEditTextCustomerName);
        edEditTextMeetingPersonName = findViewById(R.id.edEditTextMeetingPersonName);
        edEditTextMeetingPersonNumber = findViewById(R.id.edEditTextMeetingPersonNumber);
        edEditTextFullAddress = findViewById(R.id.edEditTextFullAddress);
        edEditTextContractorName = findViewById(R.id.edEditTextContractorName);
        edEditTextContractorNumber = findViewById(R.id.edEditTextContractorNumber);
        edEditTextEngineerName = findViewById(R.id.edEditTextEngineerName);
        edEditTextEngineerNumber = findViewById(R.id.edEditTextEngineerNumber);
        edEditTextSitePotential = findViewById(R.id.edEditTextSitePotential);
        edEditTextOtherBrandName = findViewById(R.id.edEditTextOtherBrandName);
        edEditTextPricePerBag = findViewById(R.id.edEditTextPricePerBag);
        edEditTextConsumedTillDate = findViewById(R.id.edEditTextConsumedTillDate);
        edEditTextEstimatedRequirement = findViewById(R.id.edEditTextEstimatedRequirement);
        edEditTextBuildUpArea = findViewById(R.id.edEditTextBuildUpArea);
        edEditTextRemarks = findViewById(R.id.edEditTextRemarks);
        edEditTextOrderQuantity = findViewById(R.id.edEditTextOrderQuantity);

        updateDeliveryDatePicker = findViewById(R.id.updateDeliveryDatePicker);
        edTextDeliveryDate = findViewById(R.id.edTextDeliveryDate);
        edShowTextDeliveryDate = findViewById(R.id.edShowTextDeliveryDate);
        updateCustomerNumber = findViewById(R.id.updateCustomerNumber);

        editOtherBrandLayout = findViewById(R.id.editOtherBrandLayout);
        editOtherBrandLayout.setVisibility(View.GONE);

        setTitleForExistingSiteTextView();
    }

    private void setTitleForExistingSiteTextView() {
        edTextCustomerName.setText(Html.fromHtml("Customer Name <font color='#FF0000'>*</font>"));
        edTextMeetingPersonName.setText(Html.fromHtml("Meeting Person <font color='#FF0000'>*</font>"));
        edTextMeetingPersonNumber.setText(Html.fromHtml("Meeting Person Contact No. <font color='#FF0000'>*</font>"));
        edTextFullAddress.setText(Html.fromHtml("Full Address <font color='#FF0000'>*</font>"));
        edTextContractorName.setText(Html.fromHtml("Petty Contractor - Head Mason Name"));
        edTextContractorNumber.setText(Html.fromHtml("Petty Contractor - Head Mason Contact No."));
        edTextEngineerName.setText(Html.fromHtml("Engineer Name"));
        edTextEngineerNumber.setText(Html.fromHtml("Engineer Contact No."));
        edTextSitePotential.setText(Html.fromHtml("Site Potential (No. of Bags) <font color='#FF0000'>*</font>"));
        edTextOtherBrandName.setText(Html.fromHtml("Name of the OTHER Brand"));
        edTextPricePerBag.setText(Html.fromHtml("Price Per Bag (Rs.)"));
        edTextConsumedTillDate.setText(Html.fromHtml("Consumed Till Date (No. of Bags)"));
        edTextEstimatedRequirement.setText(Html.fromHtml("Estimated Requirement (No. of Bags) <font color='#FF0000'>*</font>"));
        edTextBuildUpArea.setText(Html.fromHtml("Built up Area (Sq. ft) <font color='#FF0000'>*</font>"));
        edTextRemarks.setText(Html.fromHtml("Overall Remarks"));
        edTextOrderQuantity.setText(Html.fromHtml("No. of Bags Ordered"));

        edTextDeliveryDate.setText(Html.fromHtml("Requested Date of Delivery"));

        setHintForExistingSiteEditText();
    }

    private void setHintForExistingSiteEditText() {
        edEditTextCustomerName.setHint(Html.fromHtml("<font color='#666666'>Customer Name (Mandatory)</font>"));
        edEditTextMeetingPersonName.setHint(Html.fromHtml("<font color='#666666'>Meeting Person (Mandatory)</font>"));
        edEditTextMeetingPersonNumber.setHint(Html.fromHtml("<font color='#666666'>Meeting Person Contact No. (Mandatory)</font>"));
        edEditTextFullAddress.setHint(Html.fromHtml("<font color='#666666'>Full Address (Mandatory)</font>"));
        edEditTextContractorName.setHint(Html.fromHtml("<font color='#666666'>Petty Contractor - Head Mason Name</font>"));
        edEditTextContractorNumber.setHint(Html.fromHtml("<font color='#666666'>Petty Contractor - Head Mason Contact No.</font>"));
        edEditTextEngineerName.setHint(Html.fromHtml("<font color='#666666'>Engineer Name</font>"));
        edEditTextEngineerNumber.setHint(Html.fromHtml("<font color='#666666'>Engineer Contact No.</font>"));
        edEditTextSitePotential.setHint(Html.fromHtml("<font color='#666666'>Site Potential (Mandatory)</font>"));
        edEditTextOtherBrandName.setHint(Html.fromHtml("<font color='#666666'>Name of the OTHER Brand</font>"));
        edEditTextPricePerBag.setHint(Html.fromHtml("<font color='#666666'>Price Per Bag</font>"));
        edEditTextConsumedTillDate.setHint(Html.fromHtml("<font color='#666666'>Consumed Till Date</font>"));
        edEditTextEstimatedRequirement.setHint(Html.fromHtml("<font color='#666666'>Estimated Requirement (Mandatory)</font>"));
        edEditTextBuildUpArea.setHint(Html.fromHtml("<font color='#666666'>Built up Area (Mandatory)</font>"));
        edEditTextRemarks.setHint(Html.fromHtml("<font color='#666666'>Remarks</font>"));
        edEditTextOrderQuantity.setHint(Html.fromHtml("<font color='#666666'>No. of Bags Ordered</font>"));

        edShowTextDeliveryDate.setHint(Html.fromHtml("<font color='#666666'>DD-MM-YYYY</font>"));

        edEditTextSitePotential.setInputType(InputType.TYPE_CLASS_PHONE);
        edEditTextEstimatedRequirement.setInputType(InputType.TYPE_CLASS_PHONE);

        edEditTextCustomerName.setEnabled(false);
        edEditTextMeetingPersonName.setEnabled(false);
        edEditTextMeetingPersonNumber.setEnabled(false);
        edEditTextContractorName.setEnabled(false);
        edEditTextContractorNumber.setEnabled(false);
        edEditTextEngineerName.setEnabled(false);
        edEditTextEngineerNumber.setEnabled(false);
        edEditTextPricePerBag.setEnabled(false);
        edEditTextConsumedTillDate.setEnabled(false);
        edEditTextBuildUpArea.setEnabled(false);
        edEditTextOrderQuantity.setEnabled(false);

        edEditTextCustomerName.setTextColor(Color.parseColor("#999999"));
        edEditTextMeetingPersonName.setTextColor(Color.parseColor("#999999"));
        edEditTextMeetingPersonNumber.setTextColor(Color.parseColor("#999999"));
        edEditTextContractorName.setTextColor(Color.parseColor("#999999"));
        edEditTextContractorNumber.setTextColor(Color.parseColor("#999999"));
        edEditTextEngineerName.setTextColor(Color.parseColor("#999999"));
        edEditTextEngineerNumber.setTextColor(Color.parseColor("#999999"));
        edEditTextPricePerBag.setTextColor(Color.parseColor("#999999"));
        edEditTextConsumedTillDate.setTextColor(Color.parseColor("#999999"));
        edEditTextBuildUpArea.setTextColor(Color.parseColor("#999999"));
        edEditTextOrderQuantity.setTextColor(Color.parseColor("#999999"));
        edShowTextDeliveryDate.setTextColor(Color.parseColor("#999999"));

        setHintForExistingSiteButton();
    }

    private void setHintForExistingSiteButton() {
        edRouteNameButton.setText(Html.fromHtml("Route Name <font color='#FF0000'>*</font>"));
        edSiteNameNameButton.setText(Html.fromHtml("Site Name <font color='#FF0000'>*</font>"));
        edContractorButton.setText(Html.fromHtml("Contractor"));
        edBranchButton.setText(Html.fromHtml("Branch <font color='#FF0000'>*</font>"));
        edStateButton.setText(Html.fromHtml("State <font color='#FF0000'>*</font>"));
        edDistrictButton.setText(Html.fromHtml("District <font color='#FF0000'>*</font>"));
        edEngineerInStellarButton.setText(Html.fromHtml("Engineer Regd. in Star Stellar"));
        edSiteSegmentButton.setText(Html.fromHtml("Site Segment <font color='#FF0000'>*</font>"));
        edProjectSegmentButton.setText(Html.fromHtml("Project Segment <font color='#FF0000'>*</font>"));
        edConstructionTypeButton.setText(Html.fromHtml("Type of Construction <font color='#FF0000'>*</font>"));
        edCurrentConstructionStatusButton.setText(Html.fromHtml("Current Stage of Construction <font color='#FF0000'>*</font>"));
        edCementUsedButton.setText(Html.fromHtml("Cement Brand Used <font color='#FF0000'>*</font>"));
        edDecisionMarketButton.setText(Html.fromHtml("Decision Maker"));
        edProductDemoButton.setText(Html.fromHtml("Product Demo"));
        edVisitTypeButton.setText(Html.fromHtml("Visit Type <font color='#FF0000'>*</font>"));
        edPurchaseDealerNameButton.setText(Html.fromHtml("Source of Purchase Dealer/RSSD (Name)"));
        edApprovedByButton.setText(Html.fromHtml("To Be Approved By"));

        edRouteNameButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edContractorButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edBranchButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edStateButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edDistrictButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edSiteSegmentButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edConstructionTypeButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edDecisionMarketButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edProductDemoButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edPurchaseDealerNameButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        edApprovedByButton.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));

        setVisibilityForExistingSiteTextView();
    }

    private void setVisibilityForExistingSiteTextView() {
        edTextRouteName.setVisibility(View.GONE);
        edTextSiteNameName.setVisibility(View.GONE);
        edTextContractor.setVisibility(View.GONE);
        edTextBranch.setVisibility(View.GONE);
        edTextState.setVisibility(View.GONE);
        edTextDistrict.setVisibility(View.GONE);
        edTextEngineerInStellar.setVisibility(View.GONE);
        edTextSiteSegment.setVisibility(View.GONE);
        edTextProjectSegment.setVisibility(View.GONE);
        edTextConstructionType.setVisibility(View.GONE);
        edTextCurrentConstructionStatus.setVisibility(View.GONE);
        edTextCementUsed.setVisibility(View.GONE);
        edTextDecisionMarket.setVisibility(View.GONE);
        edTextProductDemo.setVisibility(View.GONE);
        edTextVisitType.setVisibility(View.GONE);
        edTextPurchaseDealerName.setVisibility(View.GONE);
        edTextApprovedBy.setVisibility(View.GONE);

        onClickFunctionUpdateSiteSetup();
    }

    private void onClickFunctionUpdateSiteSetup() {
        edRouteNameButton.setOnClickListener(this);
        edSiteNameNameButton.setOnClickListener(this);
        edContractorButton.setOnClickListener(this);
        edBranchButton.setOnClickListener(this);
        edStateButton.setOnClickListener(this);
        edDistrictButton.setOnClickListener(this);
        edEngineerInStellarButton.setOnClickListener(this);
        edSiteSegmentButton.setOnClickListener(this);
        edProjectSegmentButton.setOnClickListener(this);
        edConstructionTypeButton.setOnClickListener(this);
        edCurrentConstructionStatusButton.setOnClickListener(this);
        edCementUsedButton.setOnClickListener(this);
        edDecisionMarketButton.setOnClickListener(this);
        edProductDemoButton.setOnClickListener(this);
        edVisitTypeButton.setOnClickListener(this);
        edPurchaseDealerNameButton.setOnClickListener(this);
        edApprovedByButton.setOnClickListener(this);
        updateDeliveryDatePicker.setOnClickListener(this);

        initCustomerDetailsInputPopup();
    }


    // customer details input popup
    private void initCustomerDetailsInputPopup() {
        popupBackIcon = findViewById(R.id.popupBackIcon);
        popupRouteNameButton = findViewById(R.id.popupRouteNameButton);
        popupTextRouteName = findViewById(R.id.popupTextRouteName);
        popupEditTextCustomerNumber = findViewById(R.id.popupEditTextCustomerNumber);
        popupSubmitButton = findViewById(R.id.popupSubmitButton);

        setTitleOrHintOfCustomerDetailsInputPopup();
    }

    private void setTitleOrHintOfCustomerDetailsInputPopup() {
        popupRouteNameButton.setText(Html.fromHtml("Route Name <font color='#FF0000'>*</font>"));
        popupTextRouteName.setVisibility(View.GONE);
        popupEditTextCustomerNumber.setHint(Html.fromHtml("Customer Contact No. <font color='#FF0000'>*</font>"));
        popupEditTextCustomerNumber.setInputType(InputType.TYPE_CLASS_PHONE);


        onClickFunctionCustomerDetailsInputPopup();
    }

    private void onClickFunctionCustomerDetailsInputPopup() {
        popupRouteNameButton.setOnClickListener(this);
        popupSubmitButton.setOnClickListener(this);
        popupBackIcon.setOnClickListener(this);
        initCustomerDetailsShowPopup();
    }


    // customer details show popup
    private void initCustomerDetailsShowPopup() {
        popupCustomerNumber = findViewById(R.id.popupCustomerNumber);
        popupCustomerNumberAlreadyRegisterText = findViewById(R.id.popupCustomerNumberAlreadyRegisterText);
        popupNewCreateButton = findViewById(R.id.popupNewCreateButton);
        popupVisitExistingButton = findViewById(R.id.popupVisitExistingButton);

        popupCustomerNumberAlreadyRegisterText.setVisibility(View.GONE);

        onClickFunctionCustomerDetailsShowPopup();
    }

    private void onClickFunctionCustomerDetailsShowPopup() {
        popupNewCreateButton.setOnClickListener(this);
        popupVisitExistingButton.setOnClickListener(this);

        initChangeRoutePopup();
    }


    // customer change route popup
    private void initChangeRoutePopup() {
        customerDetailsChangeRoutePopupLayout = findViewById(R.id.customerDetailsChangeRoutePopupLayout);
        popupChangeRouteBackIcon = findViewById(R.id.popupChangeRouteBackIcon);
        popupChangeRouteCustomerNumber = findViewById(R.id.popupChangeRouteCustomerNumber);
        popupChangeRouteNameButton = findViewById(R.id.popupChangeRouteNameButton);
        popupChangeTextRouteName = findViewById(R.id.popupChangeTextRouteName);
        popupChangeAndUpdateSiteButton = findViewById(R.id.popupChangeAndUpdateSiteButton);
        popupChangeRouteNameButton.setText(Html.fromHtml("Route Name <font color='#FF0000'>*</font>"));
        onClickFunctionChangeRoutePopup();
    }

    private void onClickFunctionChangeRoutePopup() {
        popupChangeRouteBackIcon.setOnClickListener(this);
        popupChangeRouteNameButton.setOnClickListener(this);
        popupChangeAndUpdateSiteButton.setOnClickListener(this);
        openFirstStep();
    }


    // Date Picker
    private void nextVisitDatePicker() {
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                NewKhojActivity.this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    // Month is 0-based, so add 1
                    String date = selectedDay + "/" + (selectedMonth + 1) + "/" + selectedYear;
                    dateOfDelivery = selectedYear + "-";
                    if (selectedMonth < 9) {
                        dateOfDelivery = dateOfDelivery + "0" + (selectedMonth + 1) + "-";
                    } else {
                        dateOfDelivery = dateOfDelivery + (selectedMonth + 1) + "-";
                    }
                    if (selectedDay < 10) {
                        dateOfDelivery = dateOfDelivery + "0" + selectedDay;
                    } else {
                        dateOfDelivery = dateOfDelivery + selectedDay;
                    }
                    showTextDeliveryDate.setText(date);
                },
                year, month, day
        );
        datePickerDialog.show();
    }


    // First Step
    private void openFirstStep() {
        runOnUiThread(() -> {
            newSiteLayout.setVisibility(View.GONE);
            existingSiteLayout.setVisibility(View.GONE);
            popupLayout.setVisibility(View.VISIBLE);
            customerDetailsInputPopupLayout.setVisibility(View.VISIBLE);
            customerDetailsShowPopupLayout.setVisibility(View.GONE);
            customerDetailsChangeRoutePopupLayout.setVisibility(View.GONE);
        });

        try {
            new TRANS_RouteNameList_AsyncTask(NewKhojActivity.this).execute();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ openFirstStep: " + e.getMessage());
        }
    }

    private void checkAndSearchCustomer() {
        if (selectRouteData != null && !selectRouteData.getDataId().isEmpty()) {
            if (popupEditTextCustomerNumber.getText().toString().trim().equalsIgnoreCase("")) {
                Toast.makeText(mContext, "Please enter customer contact number.", Toast.LENGTH_LONG).show();
            } else if (!popupEditTextCustomerNumber.getText().toString().trim().matches("\\d{10}")) {
                Toast.makeText(mContext, "Please enter valid 10 digit customer contact number.", Toast.LENGTH_LONG).show();
            } else {
                new TRANS_CheckCustomerInDatabase_AsyncTask(NewKhojActivity.this).execute();
            }
        } else {
            Toast.makeText(mContext, "Please select route.", Toast.LENGTH_LONG).show();
        }
    }


    // Second Step
    @SuppressLint("SetTextI18n")
    private void openSecondStep() {
        customerDetailsInputPopupLayout.setVisibility(View.GONE);
        customerDetailsShowPopupLayout.setVisibility(View.VISIBLE);
        customerDetailsChangeRoutePopupLayout.setVisibility(View.GONE);
        if (isAlreadyRegisterInSelectedRoute) {
            popupCustomerNumberAlreadyRegisterText.setVisibility(View.VISIBLE);
            popupVisitExistingButton.setVisibility(View.VISIBLE);
            popupVisitExistingButton.setText("Visit Existing");
        } else {
            if (isAlreadyRegisterInOtherRoute) {
                popupCustomerNumberAlreadyRegisterText.setVisibility(View.VISIBLE);
                popupVisitExistingButton.setVisibility(View.VISIBLE);
                popupVisitExistingButton.setText("Change Route");
            } else {
                popupCustomerNumberAlreadyRegisterText.setVisibility(View.GONE);
                popupVisitExistingButton.setVisibility(View.GONE);
            }
        }
    }


    // Third Step
    @SuppressLint("SetTextI18n")
    private void openThirdStep() {
        customerDetailsInputPopupLayout.setVisibility(View.GONE);
        customerDetailsShowPopupLayout.setVisibility(View.GONE);
        customerDetailsChangeRoutePopupLayout.setVisibility(View.VISIBLE);
        popupChangeRouteCustomerNumber.setText("+91 " + popupEditTextCustomerNumber.getText().toString().trim());
    }

    private void updateCheckAndSearchCustomer() {
        if (selectRouteData != null && !selectRouteData.getDataId().isEmpty()) {
            runOnUiThread(() -> {
                edTextRouteName.setText(selectRouteData.getDataTitle());
                edTextRouteName.setVisibility(View.VISIBLE);
            });
            new TRANS_SiteName_AsyncTask(NewKhojActivity.this).execute();
        } else {
            Toast.makeText(mContext, "Please select route.", Toast.LENGTH_LONG).show();
        }
    }


    // Fourth Step
    private void openNewSiteAddLayout() {
        newSiteLayout.setVisibility(View.VISIBLE);
        existingSiteLayout.setVisibility(View.GONE);
        popupLayout.setVisibility(View.GONE);
    }

    private void checkAllDataForNewSiteUpload() {
        String estimatedRequirement = editTextEstimatedRequirement.getText().toString().trim();
        String sitePotential = editTextSitePotential.getText().toString().trim();

        if (editTextCustomerName.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Customer Name", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectMeetingPersonDetails == null || selectMeetingPersonDetails.getPersonId().isEmpty()) {
            Toast.makeText(mContext, "Please select Meeting Person", Toast.LENGTH_LONG).show();
            return;
        }
        if (!editTextMeetingPersonContactNumber.getText().toString().trim().matches("\\d{10}")) {
            Toast.makeText(mContext, "Please enter Meeting Person Contact Number", Toast.LENGTH_LONG).show();
            return;
        }
        if (editTextSiteName.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Site Name", Toast.LENGTH_LONG).show();
            return;
        }
        if (textBranch.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select Branch", Toast.LENGTH_LONG).show();
            return;
        }
        if (textState.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select State", Toast.LENGTH_LONG).show();
            return;
        }
        if (textDistrict.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select District", Toast.LENGTH_LONG).show();
            return;
        }
        if (!editTextCustomerContactNumber.getText().toString().trim().matches("\\d{10}")) {
            Toast.makeText(mContext, "Please enter customer Contact Number", Toast.LENGTH_LONG).show();
            return;
        }
        if (editTextFullAddress.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Full Address", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectSiteSegmentData == null || selectSiteSegmentData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Site Segment", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectProjectSegmentData == null || selectProjectSegmentData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Project Segment", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectConstructionTypeData == null || selectConstructionTypeData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Type of Construction", Toast.LENGTH_LONG).show();
            return;
        }
        if (editTextSitePotential.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Site Potential", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectCurrentStageOfConstructionData == null || selectCurrentStageOfConstructionData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Current Stage of Construction", Toast.LENGTH_LONG).show();
            return;
        }
        if (editTextOtherBrandName.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select Cement Brand", Toast.LENGTH_LONG).show();
            return;
        }
        if (editTextEstimatedRequirement.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Estimated Requirement", Toast.LENGTH_LONG).show();
            return;
        }
        if (editTextBuildUpArea.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Build Up Area", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectVisitTypeData == null || selectVisitTypeData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Visit Type", Toast.LENGTH_LONG).show();
            return;
        }
        if (Integer.parseInt(estimatedRequirement) > Integer.parseInt(sitePotential)) {
            Toast.makeText(mContext, "Estimated requirement cannot be more than site potential.", Toast.LENGTH_LONG).show();
            return;
        }
        if(!editTextContractorNumber.getText().toString().trim().equalsIgnoreCase("")){
            if(!editTextContractorNumber.getText().toString().trim().matches("\\d{10}")) {
                Toast.makeText(mContext, "Please enter valid Petty Contractor Number", Toast.LENGTH_LONG).show();
                return;
            }
        }
        if(!editTextEngineerNumber.getText().toString().trim().equalsIgnoreCase("")){
            if(!editTextEngineerNumber.getText().toString().trim().matches("\\d{10}")) {
                Toast.makeText(mContext, "Please enter valid Engineer Number", Toast.LENGTH_LONG).show();
                return;
            }
        }
        if (selectVisitTypeData.getDataId().equalsIgnoreCase("Star Site")) {
            if (selectVisitTypeStarData == null || selectVisitTypeStarData.getDataId().isEmpty()) {
                Toast.makeText(mContext, "Please select Visit Type", Toast.LENGTH_LONG).show();
            } else {
                new TRANS_NewSiteAdd_AsyncTask(mContext).execute();
            }
        }
        else {
            if (selectVisitTypeNonStarData == null || selectVisitTypeNonStarData.getDataId().isEmpty()) {
                Toast.makeText(mContext, "Please select Visit Type", Toast.LENGTH_LONG).show();
            } else {
                new TRANS_NewSiteAdd_AsyncTask(mContext).execute();
            }
        }
    }


    // Fifth Step
    private void setFirstDataAsDefault() {
        runOnUiThread(() -> {
            newSiteLayout.setVisibility(View.GONE);
            existingSiteLayout.setVisibility(View.VISIBLE);
            popupLayout.setVisibility(View.GONE);

            JSONObject data = siteDataSet.getDataSet();

            try {
                dateOfDelivery = data.getString("date_of_delivery");

                edTextSiteNameName.setText(data.getString("site_name"));
                edTextState.setText(data.getString("state"));
                edTextDistrict.setText(data.getString("district"));
                edTextEngineerInStellar.setText(data.getString("engg_reg_star_stellar"));
                edTextSiteSegment.setText(data.getString("site_segment"));
                edTextProjectSegment.setText(data.getString("project_segment"));
                edTextConstructionType.setText(data.getString("type_of_construction"));
                edTextCurrentConstructionStatus.setText(data.getString("construction_stage"));
                edTextCementUsed.setText(data.getString("cement_brand"));
                edTextDecisionMarket.setText(data.getString("decision_maker"));
                edTextProductDemo.setText(data.getString("product_demo"));
                edTextVisitType.setText(data.getString("visit_type") + ", " + data.getString("visit_sub_type"));
                edTextCementUsed.setText(data.getString("cement_brand"));

                edEditTextCustomerName.setText(data.getString("cust_name"));
                edEditTextMeetingPersonName.setText(data.getString("meeting_person_type"));
                edEditTextMeetingPersonNumber.setText(data.getString("meeting_person_phone"));
                edEditTextFullAddress.setText(data.getString("address"));
                edEditTextContractorName.setText(data.getString("contractor_name"));
                edEditTextContractorNumber.setText(data.getString("contractor_phone"));
                edEditTextEngineerName.setText(data.getString("engineer_name"));
                edEditTextEngineerNumber.setText(data.getString("engineer_phone"));
                edEditTextSitePotential.setText(data.getString("site_potential"));
                edEditTextOtherBrandName.setText(data.getString("cement_brand"));
                edEditTextPricePerBag.setText(data.getString("price_per_bag"));
                edEditTextConsumedTillDate.setText(data.getString("consumed_till_date"));
                edEditTextEstimatedRequirement.setText(data.getString("estimated_req"));
                edEditTextBuildUpArea.setText(data.getString("built_up_area"));
                edEditTextRemarks.setText(data.getString("remarks"));
                edEditTextOrderQuantity.setText(data.getString("bags_ordered"));
                edShowTextDeliveryDate.setText(data.getString("date_of_delivery"));

                edTextSiteNameName.setVisibility(View.VISIBLE);
                edTextState.setVisibility(View.VISIBLE);
                edTextDistrict.setVisibility(View.VISIBLE);
                edTextEngineerInStellar.setVisibility(View.VISIBLE);
                edTextSiteSegment.setVisibility(View.VISIBLE);
                edTextProjectSegment.setVisibility(View.VISIBLE);
                edTextConstructionType.setVisibility(View.VISIBLE);
                edTextCurrentConstructionStatus.setVisibility(View.VISIBLE);
                edTextCementUsed.setVisibility(View.VISIBLE);
                edTextDecisionMarket.setVisibility(View.VISIBLE);
                edTextProductDemo.setVisibility(View.VISIBLE);
                edTextVisitType.setVisibility(View.VISIBLE);
                edTextCementUsed.setVisibility(View.VISIBLE);

            } catch (JSONException e) {
                throw new RuntimeException(e);
            }

        });
    }

    @SuppressLint("SetTextI18n")
    private void changeSiteData(SiteDataSet dataSet) {
        JSONObject data = dataSet.getDataSet();
        try {
            dateOfDelivery = data.getString("date_of_delivery");

            edTextSiteNameName.setText(data.getString("site_name"));
            edTextState.setText(data.getString("state"));
            edTextDistrict.setText(data.getString("district"));
            edTextEngineerInStellar.setText(data.getString("engg_reg_star_stellar"));
            edTextSiteSegment.setText(data.getString("site_segment"));
            edTextProjectSegment.setText(data.getString("project_segment"));
            edTextConstructionType.setText(data.getString("type_of_construction"));
            edTextCurrentConstructionStatus.setText(data.getString("construction_stage"));
            edTextDecisionMarket.setText(data.getString("decision_maker"));
            edTextProductDemo.setText(data.getString("product_demo"));
            edTextVisitType.setText(data.getString("visit_type") + ", " + data.getString("visit_sub_type"));
            edTextCementUsed.setText(data.getString("cement_brand"));

            edEditTextCustomerName.setText(data.getString("cust_name"));
            edEditTextMeetingPersonName.setText(data.getString("meeting_person_type"));
            edEditTextMeetingPersonNumber.setText(data.getString("meeting_person_phone"));
            edEditTextFullAddress.setText(data.getString("address"));
            edEditTextContractorName.setText(data.getString("contractor_name"));
            edEditTextContractorNumber.setText(data.getString("contractor_phone"));
            edEditTextEngineerName.setText(data.getString("engineer_name"));
            edEditTextEngineerNumber.setText(data.getString("engineer_phone"));
            edEditTextSitePotential.setText(data.getString("site_potential"));
            edEditTextOtherBrandName.setText(data.getString("cement_brand"));
            edEditTextPricePerBag.setText(data.getString("price_per_bag"));
            edEditTextConsumedTillDate.setText(data.getString("consumed_till_date"));
            edEditTextEstimatedRequirement.setText(data.getString("estimated_req"));
            edEditTextBuildUpArea.setText(data.getString("built_up_area"));
            edEditTextRemarks.setText(data.getString("remarks"));
            edEditTextOrderQuantity.setText(data.getString("bags_ordered"));
            edShowTextDeliveryDate.setText(data.getString("date_of_delivery"));


            for (int i = 0; i < meetingPersonDetailsList.size(); i++) {
                if (meetingPersonDetailsList.get(i).getPersonId().equalsIgnoreCase(data.getString("meeting_person_type"))) {
                    selectMeetingPersonDetails = meetingPersonDetailsList.get(i);
                    break;
                }
            }

            for (int i = 0; i < branchDataList.size(); i++) {
                if (branchDataList.get(i).getDataId().equalsIgnoreCase(data.getString("branch_code"))) {
                    edTextBranch.setText(branchDataList.get(i).getDataTitle());
                    selectBranchData = branchDataList.get(i);
                    break;
                }
            }

            for (int i = 0; i < stateDataList.size(); i++) {
                if (stateDataList.get(i).getDataId().equalsIgnoreCase(data.getString("state"))) {
                    selectStateData = stateDataList.get(i);
                    break;
                }
            }

            for (int i = 0; i < districtDataList.size(); i++) {
                if (districtDataList.get(i).getDistrictCode().equalsIgnoreCase(data.getString("district"))) {
                    selectDistrictData = districtDataList.get(i);
                    break;
                }
            }

            for (int i = 0; i < isRegisterDataList.size(); i++) {
                if (isRegisterDataList.get(i).getDataId().equalsIgnoreCase(data.getString("engg_reg_star_stellar"))) {
                    selectIsRegisterData = isRegisterDataList.get(i);
                    break;
                }
            }

            for (int i = 0; i < siteSegmentList.size(); i++) {
                if (siteSegmentList.get(i).getDataId().equalsIgnoreCase(data.getString("site_segment"))) {
                    selectSiteSegmentData = siteSegmentList.get(i);
                    break;
                }
            }

            for (int i = 0; i < projectSegmentList.size(); i++) {
                if (projectSegmentList.get(i).getDataId().equalsIgnoreCase(data.getString("project_segment"))) {
                    selectProjectSegmentData = projectSegmentList.get(i);
                    break;
                }
            }

            for (int i = 0; i < constructionTypeList.size(); i++) {
                if (constructionTypeList.get(i).getDataId().equalsIgnoreCase(data.getString("type_of_construction"))) {
                    selectConstructionTypeData = constructionTypeList.get(i);
                    break;
                }
            }

            for (int i = 0; i < currentStageOfConstructionList.size(); i++) {
                if (currentStageOfConstructionList.get(i).getDataId().equalsIgnoreCase(data.getString("construction_stage"))) {
                    selectCurrentStageOfConstructionData = currentStageOfConstructionList.get(i);
                    break;
                }
            }

            for (int i = 0; i < currentBrandList.size(); i++) {
                if (currentBrandList.get(i).getDataId().equalsIgnoreCase(data.getString("cement_brand"))) {
                    selectCurrentBrandData = currentBrandList.get(i);
                    break;
                }
            }

            for (int i = 0; i < decisionMakerList.size(); i++) {
                if (decisionMakerList.get(i).getPersonId().equalsIgnoreCase(data.getString("decision_maker"))) {
                    selectDecisionMakerData = decisionMakerList.get(i);
                    break;
                }
            }

            for (int i = 0; i < productList.size(); i++) {
                if (productList.get(i).getDataId().equalsIgnoreCase(data.getString("product_demo"))) {
                    selectProductData = productList.get(i);
                    break;
                }
            }

            for (int i = 0; i < visitTypeList.size(); i++) {
                if (visitTypeList.get(i).getDataId().equalsIgnoreCase(data.getString("visit_type"))) {
                    selectVisitTypeData = visitTypeList.get(i);
                    break;
                }
            }

            for (int i = 0; i < visitTypeStarList.size(); i++) {
                if (visitTypeStarList.get(i).getDataId().equalsIgnoreCase(data.getString("visit_sub_type"))) {
                    selectVisitTypeStarData = visitTypeStarList.get(i);
                    break;
                }
            }

            for (int i = 0; i < visitTypeNonStarList.size(); i++) {
                if (visitTypeNonStarList.get(i).getDataId().equalsIgnoreCase(data.getString("visit_sub_type"))) {
                    selectVisitTypeNonStarData = visitTypeNonStarList.get(i);
                    break;
                }
            }

            for (int i = 0; i < dealerList.size(); i++) {
                if (dealerList.get(i).getPersonId().equalsIgnoreCase(data.getString("rssd"))) {
                    edTextPurchaseDealerName.setText(dealerList.get(i).getPersonName());
                    break;
                }
            }

            for (int i = 0; i < approvedByList.size(); i++) {
                if (approvedByList.get(i).getPersonId().equalsIgnoreCase(data.getString("approved_by"))) {
                    edTextApprovedBy.setText(approvedByList.get(i).getPersonName());
                    break;
                }
            }

        } catch (Exception ignored) {
        }
    }

    private void checkAllDataForUpdateSiteUpload() {
        if (edEditTextCustomerName.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Customer Name", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectMeetingPersonDetails == null || selectMeetingPersonDetails.getPersonId().isEmpty()) {
            Toast.makeText(mContext, "Please select Meeting Person", Toast.LENGTH_LONG).show();
            return;
        }
        if (!edEditTextMeetingPersonNumber.getText().toString().trim().matches("\\d{10}")) {
            Toast.makeText(mContext, "Please enter Meeting Person Contact Number", Toast.LENGTH_LONG).show();
            return;
        }
        if (edEditTextFullAddress.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Full Address", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectSiteSegmentData == null || selectSiteSegmentData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Site Segment", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectProjectSegmentData == null || selectProjectSegmentData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Project Segment", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectConstructionTypeData == null || selectConstructionTypeData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Type of Construction", Toast.LENGTH_LONG).show();
            return;
        }
        if (edEditTextSitePotential.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Site Potential", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectCurrentStageOfConstructionData == null || selectCurrentStageOfConstructionData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Current Stage of Construction", Toast.LENGTH_LONG).show();
            return;
        }
        if (edEditTextOtherBrandName.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please select Cement Brand", Toast.LENGTH_LONG).show();
            return;
        }
        if (edEditTextEstimatedRequirement.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Estimated Requirement", Toast.LENGTH_LONG).show();
            return;
        }
        if (edEditTextBuildUpArea.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(mContext, "Please enter Build Up Area", Toast.LENGTH_LONG).show();
            return;
        }
        if (selectVisitTypeData == null || selectVisitTypeData.getDataId().isEmpty()) {
            Toast.makeText(mContext, "Please select Visit Type", Toast.LENGTH_LONG).show();
            return;
        }
        if (Integer.parseInt(edEditTextEstimatedRequirement.getText().toString().trim()) > Integer.parseInt(edEditTextSitePotential.getText().toString().trim())) {
            Toast.makeText(mContext, "Estimated requirement cannot be more than site potential.", Toast.LENGTH_LONG).show();
            return;
        }
        if(!edEditTextContractorNumber.getText().toString().trim().equalsIgnoreCase("")){
            if(!edEditTextContractorNumber.getText().toString().trim().matches("\\d{10}")) {
                Toast.makeText(mContext, "Please enter valid Petty Contractor Number", Toast.LENGTH_LONG).show();
                return;
            }
        }
        if(!edEditTextEngineerNumber.getText().toString().trim().equalsIgnoreCase("")){
            if(!edEditTextEngineerNumber.getText().toString().trim().matches("\\d{10}")) {
                Toast.makeText(mContext, "Please enter valid Engineer Number", Toast.LENGTH_LONG).show();
                return;
            }
        }
        if (selectVisitTypeData.getDataId().equalsIgnoreCase("Star Site")) {
            if (selectVisitTypeStarData == null || selectVisitTypeStarData.getDataId().isEmpty()) {
                Toast.makeText(mContext, "Please select Visit Type", Toast.LENGTH_LONG).show();
            } else {
                new TRANS_OldSiteUpdate_AsyncTask(mContext).execute();
            }
        }
        else {
            if (selectVisitTypeNonStarData == null || selectVisitTypeNonStarData.getDataId().isEmpty()) {
                Toast.makeText(mContext, "Please select Visit Type", Toast.LENGTH_LONG).show();
            } else {
                new TRANS_OldSiteUpdate_AsyncTask(mContext).execute();
            }
        }
    }


    // Common Dialog
    @SuppressLint("SetTextI18n")
    public void showDataSetPopupDialog(ArrayList<DataSet> dataSet, String titleValue, int isShowSearch, String popupType) {
        try {
            final DataSetAdapter adapter = new DataSetAdapter(mContext, R.layout.list_item_single_radio, dataSet);

            // Dialog create
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);

            // Dialog title set
            TextView title =  mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);

            // Search layout
            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            if (isShowSearch == 1) {
                searchLayout.setVisibility(View.VISIBLE);
            } else {
                searchLayout.setVisibility(View.GONE);
            }

            // Back Button
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            // Filter Data [EditText]
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            //List data show
            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                switch (popupType) {
                    case "popup_route_name_1":
                        selectRouteData = adapter.getItem(position);
                        popupChangeTextRouteName.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        popupChangeTextRouteName.setVisibility(View.VISIBLE);
                        break;
                    case "popup_route_name":
                        selectRouteData = adapter.getItem(position);
                        popupTextRouteName.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        popupTextRouteName.setVisibility(View.VISIBLE);
                        break;
                    case "branch":
                        selectBranchData = adapter.getItem(position);
                        textBranch.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textBranch.setVisibility(View.VISIBLE);
                        break;
                    case "state":
                        selectStateData = adapter.getItem(position);
                        textState.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textState.setVisibility(View.VISIBLE);
                        break;
                    case "reg_in_stellar":
                        selectIsRegisterData = adapter.getItem(position);
                        textEngineerRegInSteller.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textEngineerRegInSteller.setVisibility(View.VISIBLE);
                        break;
                    case "site_segment":
                        selectSiteSegmentData = adapter.getItem(position);
                        textSiteSegment.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textSiteSegment.setVisibility(View.VISIBLE);
                        break;
                    case "project_segment":
                        selectProjectSegmentData = adapter.getItem(position);
                        textProjectSegment.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textProjectSegment.setVisibility(View.VISIBLE);
                        break;
                    case "construction_type":
                        selectConstructionTypeData = adapter.getItem(position);
                        textConstructionType.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textConstructionType.setVisibility(View.VISIBLE);
                        break;
                    case "construction_stage":
                        selectCurrentStageOfConstructionData = adapter.getItem(position);
                        textCurrentStageOfConstruction.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textCurrentStageOfConstruction.setVisibility(View.VISIBLE);
                        break;
                    case "cement_brand":
                        selectCurrentBrandData = adapter.getItem(position);

                        textCementBrandUsed.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textCementBrandUsed.setVisibility(View.VISIBLE);

                        if (Objects.requireNonNull(adapter.getItem(position)).getDataTitle().equalsIgnoreCase("other")) {
                            otherBrandNameLayout.setVisibility(View.VISIBLE);
                            editTextOtherBrandName.setText("");
                        } else {
                            otherBrandNameLayout.setVisibility(View.GONE);
                            editTextOtherBrandName.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        }
                        break;
                    case "product_demo":
                        selectProductData = adapter.getItem(position);
                        textProductDemo.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textProductDemo.setVisibility(View.VISIBLE);
                        break;
                    case "visit_type":
                        selectVisitTypeData = adapter.getItem(position);
                        textVisitType.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        textVisitType.setVisibility(View.VISIBLE);
                        if (Objects.requireNonNull(adapter.getItem(position)).getDataTitle().equalsIgnoreCase("Star Site")) {
                            showDataSetPopupDialog(visitTypeStarList, "Select Visit Type", 1, "visit_type1");
                        } else {
                            showDataSetPopupDialog(visitTypeNonStarList, "Select Visit Type", 1, "visit_type2");
                        }
                        break;
                    case "visit_type1":
                        selectVisitTypeStarData = adapter.getItem(position);
                        textVisitType.setText(textVisitType.getText().toString() + ", " + Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        break;
                    case "visit_type2":
                        selectVisitTypeNonStarData = adapter.getItem(position);
                        textVisitType.setText(textVisitType.getText().toString() + ", " + Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        break;
                    case "ed_reg_in_stellar":
                        selectIsRegisterData = adapter.getItem(position);
                        edTextEngineerInStellar.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        edTextEngineerInStellar.setVisibility(View.VISIBLE);
                        break;
                    case "ed_project_segment":
                        selectProjectSegmentData = adapter.getItem(position);
                        edTextProjectSegment.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        edTextProjectSegment.setVisibility(View.VISIBLE);
                        break;
                    case "ed_construction_stage":
                        selectCurrentStageOfConstructionData = adapter.getItem(position);
                        edTextCurrentConstructionStatus.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        edTextCurrentConstructionStatus.setVisibility(View.VISIBLE);
                        break;
                    case "ed_cement_brand":
                        selectCurrentBrandData = adapter.getItem(position);

                        edTextCementUsed.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        edTextCementUsed.setVisibility(View.VISIBLE);

                        if (Objects.requireNonNull(adapter.getItem(position)).getDataTitle().equalsIgnoreCase("other")) {
                            editOtherBrandLayout.setVisibility(View.VISIBLE);
                            edEditTextOtherBrandName.setText("");
                        } else {
                            editOtherBrandLayout.setVisibility(View.GONE);
                            edEditTextOtherBrandName.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        }
                        break;
                    case "ed_visit_type":
                        selectVisitTypeData = adapter.getItem(position);
                        edTextVisitType.setText(Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        edTextVisitType.setVisibility(View.VISIBLE);
                        if (Objects.requireNonNull(adapter.getItem(position)).getDataTitle().equalsIgnoreCase("Star Site")) {
                            showDataSetPopupDialog(visitTypeStarList, "Select Visit Type", 1, "ed_visit_type1");
                        } else {
                            showDataSetPopupDialog(visitTypeNonStarList, "Select Visit Type", 1, "ed_visit_type2");
                        }
                        break;
                    case "ed_visit_type1":
                        selectVisitTypeStarData = adapter.getItem(position);
                        edTextVisitType.setText(edTextVisitType.getText().toString() + ", " + Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        break;
                    case "ed_visit_type2":
                        selectVisitTypeNonStarData = adapter.getItem(position);
                        edTextVisitType.setText(edTextVisitType.getText().toString() + ", " + Objects.requireNonNull(adapter.getItem(position)).getDataTitle());
                        break;
                }
            });

            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            mDialogCustomer.show();
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ showDataSetPopupDialog error: " + e.getMessage());
        }
    }

    public void showPersonDataSetPopupDialog(ArrayList<PersonDataSet> dataSet, String titleValue, int isShowSearch, String popupType) {
        try {
            final PersonAdapter adapter = new PersonAdapter(mContext, R.layout.list_item_single_radio, dataSet);

            // Dialog create
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);

            // Dialog title set
            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);

            // Search layout
            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            if (isShowSearch == 1) {
                searchLayout.setVisibility(View.VISIBLE);
            } else {
                searchLayout.setVisibility(View.GONE);
            }

            // Back Button
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            // Filter Data [EditText]
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            //List data show
            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                switch (popupType) {
                    case "meeting_person":
                        selectMeetingPersonDetails = adapter.getItem(position);
                        textMeetingPerson.setText(Objects.requireNonNull(adapter.getItem(position)).getPersonName());
                        textMeetingPerson.setVisibility(View.VISIBLE);
                        break;
                    case "decision_maker":
                        selectDecisionMakerData = adapter.getItem(position);
                        textDecisionMaker.setText(Objects.requireNonNull(adapter.getItem(position)).getPersonName());
                        textDecisionMaker.setVisibility(View.VISIBLE);
                        break;
                    case "dealer_rssd":
                        selectDealerData = adapter.getItem(position);
                        textPurchaseDealerName.setText(Objects.requireNonNull(adapter.getItem(position)).getPersonName());
                        textPurchaseDealerName.setVisibility(View.VISIBLE);
                        break;
                    case "approved_by":
                        selectApprovedByData = adapter.getItem(position);
                        textApprovedBy.setText(Objects.requireNonNull(adapter.getItem(position)).getPersonName());
                        textApprovedBy.setVisibility(View.VISIBLE);
                        break;
                }
            });

            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    public void showDistrictListDialog(String titleValue) {
        try {
            final DistrictAdapter adapter = new DistrictAdapter(mContext, R.layout.list_item_single_radio, getDistrictDataSet(selectStateData.getDataId()));

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);
            TextView title =  mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            EditText searchText =  mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            ListView dialogList =  mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                selectDistrictData = adapter.getItem(position);
                textDistrict.setText(Objects.requireNonNull(adapter.getItem(position)).getDistrictName());
                textDistrict.setVisibility(View.VISIBLE);
            });

            Button addCustomer =  mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            mDialogCustomer.show();
        } catch (Exception ignored) {}
    }

    public void showSiteListDialog(ArrayList<SiteDataSet> dataSet, String titleValue, int isShowSearch) {
        try {
            final SiteAdapter adapter = new SiteAdapter(mContext, R.layout.list_item_single_radio, dataSet);

            // Dialog create
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search_material1);
            mDialogCustomer.setCancelable(false);

            // Dialog title set
            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);

            // Search layout
            LinearLayout searchLayout =  mDialogCustomer.findViewById(R.id.searchLayout);
            if (isShowSearch == 1) {
                searchLayout.setVisibility(View.VISIBLE);
            } else {
                searchLayout.setVisibility(View.GONE);
            }

            // Back Button
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            // Filter Data [EditText]
            EditText searchText =  mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            //List data show
            ListView dialogList =  mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                siteDataSet = adapter.getItem(position);
                changeSiteData(Objects.requireNonNull(adapter.getItem(position)));
            });

            Button addCustomer =  mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    public ArrayList<DistrictDataSet> getDistrictDataSet(String value) {
        ArrayList<DistrictDataSet> data = new ArrayList<>();
        try {
            for (int i = 0; i < districtDataList.size(); i++) {
                if (districtDataList.get(i).getBranchCode().equalsIgnoreCase(value)) {
                    data.add(districtDataList.get(i));
                }
            }
        } catch (Exception ignored) {
        }
        return data;
    }


    // API Call And DataSet Save
    // Loader Function
    private void progressDialogOpen(String title) {
        runOnUiThread(() -> {
            progressDialog = new ProgressDialog(mContext);
            progressDialog.setMessage(title);
            progressDialog.setCancelable(false);
            progressDialog.show();
        });

    }

    private void progressDialogUpdate(String title) {
        runOnUiThread(() -> progressDialog.setMessage(title));

    }

    private void progressDialogClose() {
        runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }


    // API
    // 1st popup api
    @SuppressLint("StaticFieldLeak")
    public class TRANS_CheckCustomerInDatabase_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_CheckCustomerInDatabase_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Wait for a minute.\nWe are check the customer details ...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.siteDataList;
                    Log.d("TAG", "_DOWNLOAD_ CheckCustomerInDatabase: " + url);

                    JSONObject jo = new JSONObject();
                    jo.put("route_code", selectRouteData.getDataId());
                    jo.put("cust_phone", popupEditTextCustomerNumber.getText().toString().trim());
                    Log.d("TAG", "_DOWNLOAD_ CheckCustomerInDatabase: " + jo);

                    POST_result = HttpCalling.httpPostCallWithDecrypted(url, jo.toString()).trim();
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
                JSONObject obj = new JSONObject(result);
                customerContactNumber = popupEditTextCustomerNumber.getText().toString().trim();
                if (obj.getInt("is_register_in_this_route") == 1) {
                    isAlreadyRegisterInSelectedRoute = true;
                } else {
                    isAlreadyRegisterInSelectedRoute = false;
                }

                if (obj.getInt("is_already_register_in_other_route") == 1) {
                    isAlreadyRegisterInOtherRoute = true;
                } else {
                    isAlreadyRegisterInOtherRoute = false;
                }

                progressDialogClose();
                openSecondStep();
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    // init all data api
    @SuppressLint("StaticFieldLeak")
    public class TRANS_RouteNameList_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_RouteNameList_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Loading Route List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.routeNameEmployeeWise + "?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    Log.d("TAG", "_DOWNLOAD_ RouteNameList: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    JSONArray arr = obj.getJSONArray("data");
                    routeList.clear();
                    for (int i = 0; i < arr.length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataTitle(arr.getJSONObject(i).getString("route_name"));
                        temp.setDataId(arr.getJSONObject(i).getString("route_code"));
                        temp.setSelect(false);
                        routeList.add(temp);
                    }
                    progressDialogClose();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Route name list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Route name list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_CustomerRouteNameList_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_CustomerRouteNameList_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Loading Route List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.routeNameCustomerWise + "?cust_phone=" + popupEditTextCustomerNumber.getText().toString().trim();
                    Log.d("TAG", "_DOWNLOAD_ CustomerRouteNameList: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    routeList.clear();
                    JSONArray arr = obj.getJSONArray("data");
                    for (int i = 0; i < arr.length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataTitle(arr.getJSONObject(i).getString("route"));
                        temp.setDataId(arr.getJSONObject(i).getString("route_code"));
                        temp.setSelect(false);
                        routeList.add(temp);
                    }
                    progressDialogClose();
                    openThirdStep();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Route name list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Route name list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_SiteName_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_SiteName_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Loading Site Name List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.siteDetails;
                    Log.d("TAG", "_DOWNLOAD_ SiteName: " + url);

                    JSONObject jo = new JSONObject();
                    jo.put("route_code", selectRouteData.getDataId());
                    jo.put("cust_phone", popupEditTextCustomerNumber.getText().toString().trim());
                    Log.d("TAG", "_DOWNLOAD_ SiteName: " + jo);

                    POST_result = HttpCalling.httpPostCallWithDecrypted(url, jo.toString()).trim();
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    listOfSite.clear();
                    for (int i = 0; i < obj.getJSONArray("sites").length(); i++) {
                        SiteDataSet temp = new SiteDataSet();
                        temp.setCustomerNumber(popupEditTextCustomerNumber.getText().toString().trim());
                        temp.setDataSet(obj.getJSONArray("sites").getJSONObject(i).getJSONObject("latest_visit"));
                        temp.setSiteName(obj.getJSONArray("sites").getJSONObject(i).getJSONObject("latest_visit").getString("site_name"));
                        temp.setRouteId(selectRouteData.getDataId());
                        temp.setSiteId(obj.getJSONArray("sites").getJSONObject(i).getString("site_id"));

                        listOfSite.add(temp);
                    }

                    siteDataSet.setCustomerNumber(popupEditTextCustomerNumber.getText().toString().trim());
                    siteDataSet.setDataSet(obj.getJSONArray("sites").getJSONObject(obj.getJSONArray("sites").length() - 1).getJSONObject("latest_visit"));
                    siteDataSet.setSiteName(obj.getJSONArray("sites").getJSONObject(obj.getJSONArray("sites").length() - 1).getJSONObject("latest_visit").getString("site_name"));
                    siteDataSet.setRouteId(selectRouteData.getDataId());
                    siteDataSet.setSiteId(obj.getJSONArray("sites").getJSONObject(obj.getJSONArray("sites").length() - 1).getString("site_id"));

                    setFirstDataAsDefault();

                    new TRANS_MeetingPersonList_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Site list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }


            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Site list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_MeetingPersonList_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_MeetingPersonList_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            if (isNewAddClick) {
                progressDialogOpen("Loading Meeting Person List...");
            } else {
                progressDialogUpdate("Loading Meeting Person List...");
            }

        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.meetUpPerson;
                    Log.d("TAG", "_DOWNLOAD_ MeetingPersonList: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    meetingPersonDetailsList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        PersonDataSet temp = new PersonDataSet();
                        temp.setPersonId(obj.getJSONArray("value").getJSONObject(i).getString("meet_up_person"));
                        temp.setPersonName(obj.getJSONArray("value").getJSONObject(i).getString("meet_up_person"));
                        temp.setPersonContactNo(obj.getJSONArray("value").getJSONObject(i).getString("meet_up_person"));
                        temp.setSelect(false);
                        meetingPersonDetailsList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("meeting_person_type").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("meet_up_person"))) {
                                    selectMeetingPersonDetails = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_BranchList_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Meet up person list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Meet up person list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_BranchList_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_BranchList_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Branch List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.branchList + "?route_code=" + selectRouteData.getDataId();
                    Log.d("TAG", "_DOWNLOAD_ BranchList: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    branchDataList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("branch_code"));
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("branch_name"));
                        temp.setSelect(false);
                        branchDataList.add(temp);

                        if (!isNewAddClick) {
                            Log.d("TAG", "_DOWNLOAD_ onPostExecute: " + siteDataSet.getDataSet().getString("branch_code"));
                            Log.d("TAG", "_DOWNLOAD_ onPostExecute: " + obj.getJSONArray("value").getJSONObject(i).getString("branch_code"));
                            try {
                                if (siteDataSet.getDataSet().getString("branch_code").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("branch_code"))) {
                                    selectBranchData = temp;
                                    int finalI = i;
                                    ((NewKhojActivity) mContext).runOnUiThread(() -> {
                                        try {
                                            edTextBranch.setText(obj.getJSONArray("value").getJSONObject(finalI).getString("branch_name"));
                                            edTextBranch.setVisibility(View.VISIBLE);
                                        } catch (Exception e) {
                                            Log.d("TAG", "_DOWNLOAD_ onPostExecute: " + e.getMessage());
                                        }
                                    });
                                }
                            } catch (Exception e) {
                                Log.d("TAG", "_DOWNLOAD_ onPostExecute: " + e.getMessage());
                            }
                        }
                    }
                    new TRANS_StateList_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Branch list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Branch list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_StateList_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_StateList_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading State List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.stateList;
                    Log.d("TAG", "_DOWNLOAD_ StateList: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    stateDataList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("state_name"));
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("state_name"));
                        temp.setSelect(false);
                        stateDataList.add(temp);
                        if (!isNewAddClick) {
                            Log.d("TAG", "_DOWNLOAD_ onPostExecute: " + siteDataSet.getDataSet().getString("state"));
                            Log.d("TAG", "_DOWNLOAD_ onPostExecute: " + obj.getJSONArray("value").getJSONObject(i).getString("state_name"));

                            try {
                                if (siteDataSet.getDataSet().getString("state").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("state_name"))) {
                                    selectStateData = temp;
                                }
                            } catch (Exception e) {
                                Log.d("TAG", "_DOWNLOAD_ onPostExecute: " + e.getMessage());
                            }
                        }
                    }
                    new TRANS_DistrictList_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "State list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "State list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_DistrictList_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_DistrictList_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading District List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.districtList;
                    Log.d("TAG", "_DOWNLOAD_ DistrictList: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    districtDataList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DistrictDataSet temp = new DistrictDataSet();
                        temp.setBranchCode(obj.getJSONArray("value").getJSONObject(i).getString("state"));
                        temp.setDistrictCode(obj.getJSONArray("value").getJSONObject(i).getString("district_name"));
                        temp.setDistrictName(obj.getJSONArray("value").getJSONObject(i).getString("district_name"));
                        temp.setSelect(false);
                        districtDataList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("district").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("district_name"))) {
                                    selectDistrictData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    dataSetIsRegisterList();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "District list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "District list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    private void dataSetIsRegisterList() {
        isRegisterDataList.clear();
        DataSet a = new DataSet();
        a.setDataTitle("Yes");
        a.setDataId("YES");
        a.setSelect(false);
        isRegisterDataList.add(a);

        DataSet a1 = new DataSet();
        a1.setDataTitle("No");
        a1.setDataId("NO");
        a1.setSelect(false);
        isRegisterDataList.add(a1);

        if (!isNewAddClick) {
            try {
                if (siteDataSet.getDataSet().getString("engg_reg_star_stellar").equalsIgnoreCase("yes")) {
                    selectIsRegisterData = a;
                } else {
                    selectIsRegisterData = a1;
                }
            } catch (Exception ignored) {
            }
        }

        new TRANS_SiteSegment_AsyncTask(NewKhojActivity.this).execute();
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_SiteSegment_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_SiteSegment_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Site Segment List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.siteSegment;
                    Log.d("TAG", "_DOWNLOAD_ SiteSegment: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    siteSegmentList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("state_name"));
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("state_name"));
                        temp.setSelect(false);
                        siteSegmentList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("site_segment").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("state_name"))) {
                                    selectSiteSegmentData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_ProjectSegment_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Site Segment list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Site Segment list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_ProjectSegment_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_ProjectSegment_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Project Segment List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.projectSegment;
                    Log.d("TAG", "_DOWNLOAD_ ProjectSegment: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    projectSegmentList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("project_segment"));
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("project_segment"));
                        temp.setSelect(false);
                        projectSegmentList.add(temp);
                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("project_segment").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("project_segment"))) {
                                    selectProjectSegmentData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_ConstructionType_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Project Segment list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Project Segment list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_ConstructionType_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_ConstructionType_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Construction Type List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.constructionType;
                    Log.d("TAG", "_DOWNLOAD_ ConstructionType: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    constructionTypeList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("type_of_construction"));
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("type_of_construction"));
                        temp.setSelect(false);
                        constructionTypeList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("type_of_construction").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("type_of_construction"))) {
                                    selectConstructionTypeData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_ConstructionCurrentStatus_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Type of Construction list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Type of Construction list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_ConstructionCurrentStatus_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_ConstructionCurrentStatus_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Construction Current Status List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.constructionCurrentStage;
                    Log.d("TAG", "_DOWNLOAD_ ConstructionCurrentStatus: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    currentStageOfConstructionList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("current_stage_of_construction"));
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("current_stage_of_construction"));
                        temp.setSelect(false);
                        currentStageOfConstructionList.add(temp);
                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("construction_stage").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("current_stage_of_construction"))) {
                                    selectCurrentStageOfConstructionData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_CurrentBrand_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Construction Current Status list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Construction Current Status list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_CurrentBrand_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_CurrentBrand_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Cement Brand List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.otherCementBrand;
                    Log.d("TAG", "_DOWNLOAD_ CurrentBrand: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    currentBrandList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("cement_brand"));
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("cement_brand"));
                        temp.setSelect(false);
                        currentBrandList.add(temp);
                    }
                    new TRANS_DecisionMakerName_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Cement Brand list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Cement Brand list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_DecisionMakerName_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_DecisionMakerName_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Decision Maker List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.decisionMaker;
                    Log.d("TAG", "_DOWNLOAD_ DecisionMakerName: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    decisionMakerList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        PersonDataSet temp = new PersonDataSet();
                        temp.setPersonName(obj.getJSONArray("value").getJSONObject(i).getString("decision_maker"));
                        temp.setPersonId(obj.getJSONArray("value").getJSONObject(i).getString("decision_maker"));
                        temp.setPersonContactNo(obj.getJSONArray("value").getJSONObject(i).getString("decision_maker"));
                        temp.setSelect(false);
                        decisionMakerList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("decision_maker").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("decision_maker"))) {
                                    selectDecisionMakerData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_Product_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Decision Maker list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Decision Maker list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_Product_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_Product_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Product List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.productDemo;
                    Log.d("TAG", "_DOWNLOAD_ Product: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    productList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("product_demo"));
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("product_demo"));
                        temp.setSelect(false);
                        productList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("product_demo").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("product_demo"))) {
                                    selectProductData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_VisitType_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Product Demo list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Product Demo list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_VisitType_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_VisitType_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Visit Type List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.visitType;
                    Log.d("TAG", "_DOWNLOAD_ VisitType: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    visitTypeList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("visit_type"));
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("visit_type"));
                        temp.setSelect(false);
                        visitTypeList.add(temp);
                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("visit_type").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("visit_type"))) {
                                    selectVisitTypeData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_VisitTypeStar_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Visit Type list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Visit Type list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_VisitTypeStar_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_VisitTypeStar_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Visit Type List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.visitTypeStar;
                    Log.d("TAG", "_DOWNLOAD_ VisitTypeStar: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    visitTypeStarList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("visit_type_star"));
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("visit_type_star"));
                        temp.setSelect(false);
                        visitTypeStarList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("visit_sub_type").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("visit_type_star"))) {
                                    selectVisitTypeStarData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_VisitTypeNonStar_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Visit Type list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Visit Type list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_VisitTypeNonStar_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_VisitTypeNonStar_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Visit Type List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.visitTypeNonStar;
                    Log.d("TAG", "_DOWNLOAD_ VisitTypeNonStar: " + url);
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
                JSONObject obj = new JSONObject(result);
                // break the data and store in 'visitTypeList' list
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    visitTypeNonStarList.clear();
                    for (int i = 0; i < obj.getJSONArray("value").length(); i++) {
                        DataSet temp = new DataSet();
                        temp.setDataId(obj.getJSONArray("value").getJSONObject(i).getString("visit_type_non_star"));
                        temp.setDataTitle(obj.getJSONArray("value").getJSONObject(i).getString("visit_type_non_star"));
                        temp.setSelect(false);
                        visitTypeNonStarList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("visit_sub_type").equalsIgnoreCase(obj.getJSONArray("value").getJSONObject(i).getString("visit_type_non_star"))) {
                                    selectVisitTypeNonStarData = temp;
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_PurchaseDealer_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Visit Type list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
                //Call next api Dealer list
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_PurchaseDealer_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_PurchaseDealer_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Purchase Dealer or RSSD List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.rssdList + "?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    Log.d("TAG", "_DOWNLOAD_ PurchaseDealer: " + url);
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    dealerList.clear();
                    for (int i = 0; i < obj.getJSONArray("dealers").length(); i++) {
                        PersonDataSet temp = new PersonDataSet();
                        temp.setPersonContactNo(obj.getJSONArray("dealers").getJSONObject(i).getString("customer_name"));
                        temp.setPersonName(obj.getJSONArray("dealers").getJSONObject(i).getString("customer_name"));
                        temp.setPersonId(obj.getJSONArray("dealers").getJSONObject(i).getString("customer_code"));
                        temp.setSelect(false);
                        dealerList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("rssd").equalsIgnoreCase(obj.getJSONArray("dealers").getJSONObject(i).getString("customer_code"))) {
                                    selectDealerData = temp;
                                    int finalI = i;
                                    ((NewKhojActivity) mContext).runOnUiThread(new Runnable() {
                                        @Override
                                        public void run() {
                                            try {
                                                edTextPurchaseDealerName.setText(obj.getJSONArray("dealers").getJSONObject(finalI).getString("customer_name"));
                                                edTextPurchaseDealerName.setVisibility(View.VISIBLE);
                                            } catch (JSONException e) {
                                                throw new RuntimeException(e);
                                            }
                                        }
                                    });
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    new TRANS_ApprovedBy_AsyncTask(mContext).execute();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Purchase Dealer or RSSD list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Purchase Dealer or RSSD list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_ApprovedBy_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_ApprovedBy_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogUpdate("Loading Approved By List...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.approvedByList + "?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    Log.d("TAG", "_DOWNLOAD_ ApprovedBy: " + url);
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
                JSONObject obj = new JSONObject(result);
                // break the data and store in 'approvedByList' list
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    approvedByList.clear();
                    for (int i = 0; i < obj.getJSONArray("employees").length(); i++) {
                        PersonDataSet temp = new PersonDataSet();
                        temp.setPersonContactNo(obj.getJSONArray("employees").getJSONObject(i).getString("emp_name"));
                        temp.setPersonName(obj.getJSONArray("employees").getJSONObject(i).getString("emp_name"));
                        temp.setPersonId(obj.getJSONArray("employees").getJSONObject(i).getString("emp_code"));
                        temp.setSelect(false);
                        approvedByList.add(temp);

                        if (!isNewAddClick) {
                            try {
                                if (siteDataSet.getDataSet().getString("approved_by").equalsIgnoreCase(obj.getJSONArray("employees").getJSONObject(i).getString("emp_code"))) {
                                    selectApprovedByData = temp;
                                    int finalI = i;
                                    ((NewKhojActivity) mContext).runOnUiThread(() -> {
                                        try {
                                            edTextApprovedBy.setText(obj.getJSONArray("employees").getJSONObject(finalI).getString("emp_name"));
                                            edTextApprovedBy.setVisibility(View.VISIBLE);
                                        } catch (JSONException e) {
                                            throw new RuntimeException(e);
                                        }
                                    });
                                }
                            } catch (Exception ignored) {
                            }
                        }
                    }
                    progressDialogClose();
                    if (isNewAddClick) {
                        openNewSiteAddLayout();
                    }
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, "Approved By list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                progressDialogClose();
                Toast.makeText(mContext, "Approved By list not found. Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    // Main api (new and update)
    @SuppressLint("StaticFieldLeak")
    public class TRANS_NewSiteAdd_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_NewSiteAdd_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Wait for a moment...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.siteFormSubmit;
                    Log.d("TAG", "_DOWNLOAD_ NewSiteAdd: " + url);
                    JSONObject jo = new JSONObject();

                    if (selectRouteData == null || selectRouteData.getDataId().isEmpty()) {
                        jo.put("route_code", "");
                    } else {
                        jo.put("route_code", selectRouteData.getDataId());
                    }

                    if (selectMeetingPersonDetails == null || selectMeetingPersonDetails.getPersonId().isEmpty()) {
                        jo.put("meeting_person_type", "");
                    } else {
                        jo.put("meeting_person_type", selectMeetingPersonDetails.getPersonId());
                    }

                    if (selectBranchData == null || selectBranchData.getDataId().isEmpty()) {
                        jo.put("branch_code", "");
                    } else {
                        jo.put("branch_code", selectBranchData.getDataId());
                    }

                    if (selectStateData == null || selectStateData.getDataId().isEmpty()) {
                        jo.put("state", "");
                    } else {
                        jo.put("state", selectStateData.getDataId());
                    }

                    if (selectDistrictData == null || selectDistrictData.getDistrictCode().isEmpty()) {
                        jo.put("district", "");
                    } else {
                        jo.put("district", selectDistrictData.getDistrictCode());
                    }

                    if (selectIsRegisterData == null || selectIsRegisterData.getDataId().isEmpty()) {
                        jo.put("engg_reg_star_stellar", "");
                    } else {
                        jo.put("engg_reg_star_stellar", selectIsRegisterData.getDataId());
                    }

                    if (selectSiteSegmentData == null || selectSiteSegmentData.getDataId().isEmpty()) {
                        jo.put("site_segment", "");
                    } else {
                        jo.put("site_segment", selectSiteSegmentData.getDataId());
                    }

                    if (selectProjectSegmentData == null || selectProjectSegmentData.getDataId().isEmpty()) {
                        jo.put("project_segment", "");
                    } else {
                        jo.put("project_segment", selectProjectSegmentData.getDataId());
                    }

                    if (selectConstructionTypeData == null || selectConstructionTypeData.getDataId().isEmpty()) {
                        jo.put("type_of_construction", "");
                    } else {
                        jo.put("type_of_construction", selectConstructionTypeData.getDataId());
                    }

                    if (selectCurrentStageOfConstructionData == null || selectCurrentStageOfConstructionData.getDataId().isEmpty()) {
                        jo.put("construction_stage", "");
                    } else {
                        jo.put("construction_stage", selectCurrentStageOfConstructionData.getDataId());
                    }

                    if (selectDecisionMakerData == null || selectDecisionMakerData.getPersonId().isEmpty()) {
                        jo.put("decision_maker", "");
                    } else {
                        jo.put("decision_maker", selectDecisionMakerData.getPersonId());
                    }

                    if (selectProductData == null || selectProductData.getDataId().isEmpty()) {
                        jo.put("product_demo", "");
                    } else {
                        jo.put("product_demo", selectProductData.getDataId());
                    }

                    if (selectVisitTypeData == null || selectVisitTypeData.getDataId().isEmpty()) {
                        jo.put("visit_type", "");
                    } else {
                        jo.put("visit_type", selectVisitTypeData.getDataId());
                        if (selectVisitTypeData.getDataTitle().equalsIgnoreCase("Star Site")) {
                            if (selectVisitTypeStarData == null || selectVisitTypeStarData.getDataId().isEmpty()) {
                                jo.put("visit_sub_type", "");
                            } else {
                                jo.put("visit_sub_type", selectVisitTypeStarData.getDataId());
                            }

                        } else {
                            if (selectVisitTypeNonStarData == null || selectVisitTypeNonStarData.getDataId().isEmpty()) {
                                jo.put("visit_sub_type", "");
                            } else {
                                jo.put("visit_sub_type", selectVisitTypeNonStarData.getDataId());
                            }

                        }
                    }

                    if (selectDealerData == null || selectDealerData.getPersonId().isEmpty()) {
                        jo.put("rssd", "");
                    } else {
                        jo.put("rssd", selectDealerData.getPersonId());
                    }

                    if (selectApprovedByData == null || selectApprovedByData.getPersonId().isEmpty()) {
                        jo.put("approved_by", "");
                    } else {
                        jo.put("approved_by", selectApprovedByData.getPersonId());
                    }
                    jo.put("date_of_delivery", dateOfDelivery);

                    jo.put("cust_name", editTextCustomerName.getText().toString().trim());
                    jo.put("meeting_person_phone", editTextMeetingPersonContactNumber.getText().toString().trim());
                    jo.put("site_name", editTextSiteName.getText().toString().trim());
                    jo.put("latitude", latitude);
                    jo.put("longitude", longitude);
                    jo.put("cust_phone", editTextCustomerContactNumber.getText().toString().trim());
                    jo.put("address", editTextFullAddress.getText().toString().trim());
                    jo.put("contractor_name", editTextContractorName.getText().toString().trim());
                    jo.put("contractor_phone", editTextContractorNumber.getText().toString().trim());
                    jo.put("engineer_name", editTextEngineerName.getText().toString().trim());
                    jo.put("engineer_phone", editTextEngineerNumber.getText().toString().trim());
                    jo.put("site_potential", editTextSitePotential.getText().toString().trim());
                    jo.put("cement_brand", editTextOtherBrandName.getText().toString().trim());
                    jo.put("price_per_bag", editTextPricePerBag.getText().toString().trim());
                    jo.put("consumed_till_date", editTextConsumedTillDate.getText().toString().trim());
                    jo.put("estimated_req", editTextEstimatedRequirement.getText().toString().trim());
                    jo.put("built_up_area", editTextBuildUpArea.getText().toString().trim());
                    jo.put("remarks", editTextRemarks.getText().toString().trim());
                    jo.put("bags_ordered", editTextOrderQuantity.getText().toString().trim());
                    jo.put("visited_by", Constants.employeeDetailObject.getEmpCode());

                    Log.d("TAG", "_DOWNLOAD_ NewSiteAdd: " + jo);
                    POST_result = HttpCalling.httpPostCallWithDecrypted(url, jo.toString()).trim();
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
                JSONObject obj = new JSONObject(result);
                Log.d("TAG", "_DOWNLOAD_ 12121212: " + obj);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    progressDialogClose();
                    Toast.makeText(mContext, "Successfully added new site information...", Toast.LENGTH_LONG).show();
                    startTimerAndGoBack();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, obj.getString("error"), Toast.LENGTH_LONG).show();
                    startTimerAndGoBack();
                }
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_OldSiteUpdate_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;

        public TRANS_OldSiteUpdate_AsyncTask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen("Wait for a moment...");
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.siteFormSubmit;
                    Log.d("TAG", "_DOWNLOAD_ OldSiteUpdate: " + url);
                    JSONObject jo = new JSONObject();

                    if (selectRouteData == null || selectRouteData.getDataId().isEmpty()) {
                        jo.put("route_code", "");
                    } else {
                        jo.put("route_code", selectRouteData.getDataId());
                    }

                    if (selectMeetingPersonDetails == null || selectMeetingPersonDetails.getPersonId().isEmpty()) {
                        jo.put("meeting_person_type", "");
                    } else {
                        jo.put("meeting_person_type", selectMeetingPersonDetails.getPersonId());
                    }

                    if (selectBranchData == null || selectBranchData.getDataId().isEmpty()) {
                        jo.put("branch_code", "");
                    } else {
                        jo.put("branch_code", selectBranchData.getDataId());
                    }

                    if (selectStateData == null || selectStateData.getDataId().isEmpty()) {
                        jo.put("state", "");
                    } else {
                        jo.put("state", selectStateData.getDataId());
                    }

                    if (selectDistrictData == null || selectDistrictData.getDistrictCode().isEmpty()) {
                        jo.put("district", "");
                    } else {
                        jo.put("district", selectDistrictData.getDistrictCode());
                    }

                    if (selectIsRegisterData == null || selectIsRegisterData.getDataId().isEmpty()) {
                        jo.put("engg_reg_star_stellar", "");
                    } else {
                        jo.put("engg_reg_star_stellar", selectIsRegisterData.getDataId());
                    }

                    if (selectSiteSegmentData == null || selectSiteSegmentData.getDataId().isEmpty()) {
                        jo.put("site_segment", "");
                    } else {
                        jo.put("site_segment", selectSiteSegmentData.getDataId());
                    }

                    if (selectProjectSegmentData == null || selectProjectSegmentData.getDataId().isEmpty()) {
                        jo.put("project_segment", "");
                    } else {
                        jo.put("project_segment", selectProjectSegmentData.getDataId());
                    }

                    if (selectConstructionTypeData == null || selectConstructionTypeData.getDataId().isEmpty()) {
                        jo.put("type_of_construction", "");
                    } else {
                        jo.put("type_of_construction", selectConstructionTypeData.getDataId());
                    }

                    if (selectCurrentStageOfConstructionData == null || selectCurrentStageOfConstructionData.getDataId().isEmpty()) {
                        jo.put("construction_stage", "");
                    } else {
                        jo.put("construction_stage", selectCurrentStageOfConstructionData.getDataId());
                    }

                    if (selectDecisionMakerData == null || selectDecisionMakerData.getPersonId().isEmpty()) {
                        jo.put("decision_maker", "");
                    } else {
                        jo.put("decision_maker", selectDecisionMakerData.getPersonId());
                    }

                    if (selectProductData == null || selectProductData.getDataId().isEmpty()) {
                        jo.put("product_demo", "");
                    } else {
                        jo.put("product_demo", selectProductData.getDataId());
                    }

                    if (selectVisitTypeData == null || selectVisitTypeData.getDataId().isEmpty()) {
                        jo.put("visit_type", "");
                    } else {
                        jo.put("visit_type", selectVisitTypeData.getDataId());
                        if (selectVisitTypeData.getDataTitle().equalsIgnoreCase("Star Site")) {
                            if (selectVisitTypeStarData == null || selectVisitTypeStarData.getDataId().isEmpty()) {
                                jo.put("visit_sub_type", "");
                            } else {
                                jo.put("visit_sub_type", selectVisitTypeStarData.getDataId());
                            }

                        } else {
                            if (selectVisitTypeNonStarData == null || selectVisitTypeNonStarData.getDataId().isEmpty()) {
                                jo.put("visit_sub_type", "");
                            } else {
                                jo.put("visit_sub_type", selectVisitTypeNonStarData.getDataId());
                            }

                        }
                    }

                    if (selectDealerData == null || selectDealerData.getPersonId().isEmpty()) {
                        jo.put("rssd", "");
                    } else {
                        jo.put("rssd", selectDealerData.getPersonId());
                    }

                    if (selectApprovedByData == null || selectApprovedByData.getPersonId().isEmpty()) {
                        jo.put("approved_by", "");
                    } else {
                        jo.put("approved_by", selectApprovedByData.getPersonId());
                    }

                    jo.put("date_of_delivery", dateOfDelivery);

                    jo.put("cust_name", edEditTextCustomerName.getText().toString().trim());
                    jo.put("meeting_person_phone", edEditTextMeetingPersonNumber.getText().toString().trim());
                    jo.put("site_name", edTextSiteNameName.getText().toString().trim());
                    jo.put("latitude", latitude);
                    jo.put("longitude", longitude);
                    jo.put("cust_phone", popupEditTextCustomerNumber.getText().toString().trim());
                    jo.put("address", edEditTextFullAddress.getText().toString().trim());
                    jo.put("contractor_name", edEditTextContractorName.getText().toString().trim());
                    jo.put("contractor_phone", edEditTextContractorNumber.getText().toString().trim());
                    jo.put("engineer_name", edEditTextEngineerName.getText().toString().trim());
                    jo.put("engineer_phone", edEditTextEngineerNumber.getText().toString().trim());
                    jo.put("site_potential", edEditTextSitePotential.getText().toString().trim());
                    jo.put("cement_brand", edEditTextOtherBrandName.getText().toString().trim());
                    jo.put("price_per_bag", edEditTextPricePerBag.getText().toString().trim());
                    jo.put("consumed_till_date", edEditTextConsumedTillDate.getText().toString().trim());
                    jo.put("estimated_req", edEditTextEstimatedRequirement.getText().toString().trim());
                    jo.put("built_up_area", edEditTextBuildUpArea.getText().toString().trim());
                    jo.put("remarks", edEditTextRemarks.getText().toString().trim());
                    jo.put("bags_ordered", edEditTextOrderQuantity.getText().toString().trim());
                    jo.put("visited_by", Constants.employeeDetailObject.getEmpCode());

                    Log.d("TAG", "_DOWNLOAD_ OldSiteUpdate: " + jo);
                    POST_result = HttpCalling.httpPostCallWithDecrypted(url, jo.toString()).trim();
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
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("yes")) {
                    progressDialogClose();
                    Toast.makeText(mContext, "Successfully updated site information...", Toast.LENGTH_LONG).show();
                    startTimerAndGoBack();
                } else {
                    progressDialogClose();
                    Toast.makeText(mContext, obj.getString("error"), Toast.LENGTH_LONG).show();
                    startTimerAndGoBack();
                }
            } catch (Exception e) {
                progressDialogClose();
            }
        }
    }

    private void startTimerAndGoBack() {
        new Handler().postDelayed(() -> ((NewKhojActivity) mContext).runOnUiThread(this::gotoPreviousPage), 2000);
    }

    private void gotoPreviousPage() {
        finish();
    }
}