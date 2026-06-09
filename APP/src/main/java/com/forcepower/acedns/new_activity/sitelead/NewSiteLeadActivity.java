package com.forcepower.acedns.new_activity.sitelead;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.text.Editable;
import android.text.Html;
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
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.ContextCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.api.clients.NetworkUtil;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.new_activity.sitelead.adapter.ShowCounterDataSetAdapter;
import com.forcepower.acedns.new_activity.sitelead.adapter.ShowDataSetAdapter;
import com.forcepower.acedns.new_activity.sitelead.adapter.ShowExistingSiteDataSetAdapter;
import com.forcepower.acedns.new_activity.sitelead.adapter.ShowProfileDataSetAdapter;
import com.forcepower.acedns.new_activity.sitelead.dataset.CounterNameDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.DataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.DistrictDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.ProductDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.ProfileDataSet;
import com.forcepower.acedns.new_activity.sitelead.dataset.SiteLeadDataSet;
import com.forcepower.acedns.newDataBase.sync.DataForDownloading;
import com.forcepower.acedns.newDataBase.sync.DataForUpload;
import com.forcepower.acedns.util.LocationTracker;
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
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.Date;
import java.util.Objects;

import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class NewSiteLeadActivity extends AceDnsParentActivity implements View.OnClickListener, RadioGroup.OnCheckedChangeListener {
    // ***Common Layout***
    private Button backButton, submitButton, filterButton;
    private LinearLayout siteLeadFormLayout, siteLeadApprovalLayout;

    // ***Primary Layout***
    private RadioGroup newSiteTypeRadioGroup;

    // ***New Site Lead Layout***
    private LinearLayout newSiteLayout;

    private LinearLayout layoutNewSitePettyContractorName, layoutNewSitePettyContractorContactNo, layoutNewSitePettyEngineerName, layoutNewSitePettyEngineerContactNo, layoutNewSiteOrderQuantity, layoutNewSiteCounterCode,
            layoutNewSiteAsmEmployeeId, layoutNewSiteTypeOfConstruction;
    private TextView textNewSiteTransactionId, textNewSiteUniqueSiteId, textNewSiteSiteCreationDate, textNewSiteVisitDate, textNewSiteEmployeeCode, textNewSiteEmployeeName, textNewSiteZone, textNewSiteLatitude,
            textNewSiteLongitude, textNewSiteCustomerName, textNewSiteCustomerContactNo, textNewSiteFullAddress, textNewSitePettyContractorName, textNewSitePettyContractorContactNo, textNewSitePettyEngineerName,
            textNewSitePettyEngineerContactNo, textNewSiteBuiltUpArea, textNewSiteSitePotential, textNewSiteConsumedTillDate, textNewSiteBalancePotential, textNewSiteBalancePotentialManual, textNewSiteSiteCategory,
            textNewSitePricePerBag, textNewSiteOrderQuantity, textNewSiteCounterCode, textNewSiteAsmEmployeeId, textNewSiteSiteRemarks;
    private EditText edTextNewSiteTransactionId, edTextNewSiteUniqueSiteId, edTextNewSiteSiteCreationDate, edTextNewSiteVisitDate, edTextNewSiteEmployeeCode, edTextNewSiteEmployeeName, edTextNewSiteZone,
            edTextNewSiteLatitude, edTextNewSiteLongitude, edTextNewSiteCustomerName, edTextNewSiteCustomerContactNo, edTextNewSiteFullAddress, edTextNewSitePettyContractorName, edTextNewSitePettyContractorContactNo,
            edTextNewSitePettyEngineerName, edTextNewSitePettyEngineerContactNo, edTextNewSiteBuiltUpArea, edTextNewSiteSitePotential, edTextNewSiteConsumedTillDate, edTextNewSiteBalancePotential,
            edTextNewSiteBalancePotentialManual, edTextNewSiteSiteCategory, edTextNewSitePricePerBag, edTextNewSiteOrderQuantity, edTextNewSiteCounterCode, edTextNewSiteAsmEmployeeId, edTextNewSiteSiteRemarks;

    private LinearLayout layoutNewSiteConversion, layoutNewSiteProduct, layoutNewSiteRequestDateOfDelivery, layoutNewSiteCounterType, layoutNewSiteCounterName, layoutNewSiteReasonsForNonConversion, layoutNewSiteAsmName,
            layoutExistingSiteTypeOfConstruction;
    private Button buttonNewSiteBranch, buttonNewSiteState, buttonNewSiteDistrict, buttonNewSiteIsReqdContractorLink, buttonNewSiteIsReqdEngineerStellar, buttonNewSiteMeetingPerson, buttonNewSiteDecisionMaker,
            buttonNewSiteSiteSegment, buttonNewSiteVisitType, buttonNewSiteProjectSegment, buttonNewSiteTypeOfConstruction, buttonNewSiteCurrentStageOfConstruction, buttonNewSiteBrandUsed, buttonNewSiteConversion,
            buttonNewSiteProduct, buttonNewSiteRequestDateOfDelivery, buttonNewSiteCounterType, buttonNewSiteCounterName, buttonNewSiteReasonsForNonConversion, buttonNewSiteSitePriority, buttonNewSiteWeatherShieldDemo,
            buttonNewSiteAsmName, buttonNewSiteSiteStatus;
    private TextView textNewSiteBranch, textNewSiteState, textNewSiteDistrict, textNewSiteIsReqdContractorLink, textNewSiteIsReqdEngineerStellar, textNewSiteMeetingPerson, textNewSiteDecisionMaker,
            textNewSiteSiteSegment, textNewSiteVisitType, textNewSiteProjectSegment, textNewSiteTypeOfConstruction, textNewSiteCurrentStageOfConstruction, textNewSiteBrandUsed, textNewSiteConversion,
            textNewSiteProduct, textNewSiteRequestDateOfDelivery, textNewSiteCounterType, textNewSiteCounterName, textNewSiteReasonsForNonConversion, textNewSiteSitePriority, textNewSiteWeatherShieldDemo,
            textNewSiteAsmName, textNewSiteSiteStatus;

    // ***Existing Site Lead Layout***
    private LinearLayout existingSiteLayout;

    private LinearLayout layoutExistingSitePettyContractorName, layoutExistingSitePettyContractorContactNo, layoutExistingSitePettyEngineerName, layoutExistingSitePettyEngineerContactNo, layoutExistingSiteOrderQuantity,
            layoutExistingSiteCounterCode, layoutExistingSiteDateAndTime, layoutExistingSiteAsmEmployeeId, layoutExistingSiteDeliveryRemarks, layoutExistingSiteReasonForNotDelivery;
    private TextView textExistingSiteTransactionId, textExistingSiteSiteCreationDate, textExistingSiteVisitDate, textExistingSiteEmployeeCode, textExistingSiteEmployeeName, textExistingSiteZone,
            textExistingSiteLatitude, textExistingSiteLongitude, textExistingSiteCustomerName, textExistingSiteCustomerContactNo, textExistingSiteFullAddress, textExistingSitePettyContractorName,
            textExistingSitePettyContractorContactNo, textExistingSitePettyEngineerName, textExistingSitePettyEngineerContactNo, textExistingSiteBuiltUpArea, textExistingSiteSitePotential,
            textExistingSiteConsumedTillDate, textExistingSiteBalancePotential, textExistingSiteBalancePotentialManual, textExistingSiteSiteCategory, textExistingSitePricePerBag, textExistingSiteOrderQuantity,
            textExistingSiteCounterCode, textExistingSiteDateAndTime, textExistingSiteAsmEmployeeId, textExistingSiteDeliveryRemarks, textExistingSiteReasonForNotDelivery, textExistingSiteSiteRemarks;
    private EditText edTextExistingSiteTransactionId, edTextExistingSiteSiteCreationDate, edTextExistingSiteVisitDate, edTextExistingSiteEmployeeCode, edTextExistingSiteEmployeeName, edTextExistingSiteZone,
            edTextExistingSiteLatitude, edTextExistingSiteLongitude, edTextExistingSiteCustomerName, edTextExistingSiteCustomerContactNo, edTextExistingSiteFullAddress, edTextExistingSitePettyContractorName,
            edTextExistingSitePettyContractorContactNo, edTextExistingSitePettyEngineerName, edTextExistingSitePettyEngineerContactNo, edTextExistingSiteBuiltUpArea, edTextExistingSiteSitePotential,
            edTextExistingSiteConsumedTillDate, edTextExistingSiteBalancePotential, edTextExistingSiteBalancePotentialManual, edTextExistingSiteSiteCategory, edTextExistingSitePricePerBag, edTextExistingSiteSiteRemarks,
            edTextExistingSiteOrderQuantity, edTextExistingSiteCounterCode, edTextExistingSiteDateAndTime, edTextExistingSiteAsmEmployeeId, edTextExistingSiteDeliveryRemarks, edTextExistingSiteReasonForNotDelivery;

    private LinearLayout layoutExistingSiteProduct, layoutExistingSiteRequestDateOfDelivery, layoutExistingSiteCounterType, layoutExistingSiteCounterName, layoutExistingSiteReasonsForNonConversion,
            layoutExistingSiteAsmName,layoutExistingSiteApprovalStatus;
    private Button buttonExistingUniqueId, buttonExistingSiteBranch, buttonExistingSiteState, buttonExistingSiteDistrict, buttonExistingSiteIsReqdContractorLink, buttonExistingSiteIsReqdEngineerStellar,
            buttonExistingSiteMeetingPerson, buttonExistingSiteDecisionMaker, buttonExistingSiteSiteSegment, buttonExistingSiteVisitType, buttonExistingSiteProjectSegment, buttonExistingSiteTypeOfConstruction,
            buttonExistingSiteCurrentStageOfConstruction, buttonExistingSiteBrandUsed, buttonExistingSiteConversion, buttonExistingSiteProduct, buttonExistingSiteRequestDateOfDelivery, buttonExistingSiteCounterType,
            buttonExistingSiteCounterName, buttonExistingSiteReasonsForNonConversion, buttonExistingSiteSitePriority, buttonExistingSiteWeatherShieldDemo, buttonExistingSiteApprovalStatus, buttonExistingSiteAsmName,
            buttonExistingSiteSiteStatus;
    private TextView textExistingUniqueId, textExistingSiteBranch, textExistingSiteState, textExistingSiteDistrict, textExistingSiteIsReqdContractorLink, textExistingSiteIsReqdEngineerStellar,
            textExistingSiteMeetingPerson, textExistingSiteDecisionMaker, textExistingSiteSiteSegment, textExistingSiteVisitType, textExistingSiteProjectSegment, textExistingSiteTypeOfConstruction,
            textExistingSiteCurrentStageOfConstruction, textExistingSiteBrandUsed, textExistingSiteConversion, textExistingSiteProduct, textExistingSiteRequestDateOfDelivery, textExistingSiteCounterType,
            textExistingSiteCounterName, textExistingSiteReasonsForNonConversion, textExistingSiteSitePriority, textExistingSiteWeatherShieldDemo, textExistingSiteApprovalStatus, textExistingSiteAsmName,
            textExistingSiteSiteStatus;

    // ***ASM Existing Site Lead Layout***
    private TextView asmTextSiteTransactionId, asmTextSiteUniqueId, asmTextSiteCreationDate, asmTextSiteVisitDate, asmTextSiteEmployeeCode, asmTextSiteEmployeeName, asmTextSiteZone, asmTextSiteState, asmTextSiteBranch,
            asmTextSiteDistrict, asmTextSiteLatitude, asmTextSiteLongitude, asmTextSiteCustomerName, asmTextSiteCustomerContactNumber, asmTextSiteFullAddress, asmTextSiteIsRegdInStarLink, asmTextSiteContractorName,
            asmTextSiteContractorContactNo, asmTextSiteIsRegdInStarStellar, asmTextSiteEngineerName, asmTextSiteEngineerContactNo, asmTextSiteMeetingPerson, asmTextSiteDecisionMaker, asmTextSiteSiteSegment,
            asmTextSiteVisitType, asmTextSiteProjectSegment, asmTextSiteTypeOfConstruction, asmTextSiteCurrentStageOfConstruction, asmTextSiteBuiltUpArea, asmTextSiteSitePotential, asmTextSiteConsumedTillDate,
            asmTextSiteBalancePotential, asmTextSiteSiteCategory, asmTextSiteBrandUsed, asmTextSitePricePerBag, asmTextSiteConversion, asmTextSiteSelectProduct, asmTextSiteOrderQty, asmTextSiteRequestedDateOfDelivery,
            asmTextSiteCounterType, asmTextSiteCounterName, asmTextSiteCounterCode, asmTextSiteDistrictReasonForNonConversion, asmTextSiteSitePriority, asmTextSiteWeatherShieldDemo, asmTextSiteSiteStatus,
            asmTextSiteBalancePotentialManual, asmTextSiteRemarks;

    private LinearLayout asmStatusUpdatePopup, asmStatusUpdatePopupDesign;
    private LinearLayout layoutActualDateOfDeliveryASM, layoutDeliveryRemarksASM, layoutReasonForNotDeliveryASM;
    private Button buttonStatusASM, buttonActualDateOfDeliveryASM;
    private TextView textStatusASM, textActualDateOfDeliveryASM;
    private EditText edTextDeliveryRemarksASM, edTextReasonForNotDeliveryASM;
    private Button updateButton;

    // ***Default Value***
    Context mContext;
    String branch = "", branchCode = "", state = "", district = "", isReqdContractorLink = "", isReqdEngineerStellar = "", meetingPerson = "", decisionMaker = "", siteSegment = "", visitType = "", projectSegment = "",
            typeOfConstruction = "", currentStageOfConstruction = "", brandUsed = "", conversion = "", product = "", requestDateOfDelivery = "", counterType = "", counterName = "", reasonsForNonConversion = "",
            priority = "", weatherShieldDemo = "", approvalStatus = "", asmName = "", actualDateOfDelivery = "", siteStatus = "", siteId = "", dateString = "", timeString = "";
    String transactionId = "", uniqueSiteId = "", siteCreationDate = "", visitDate = "", employeeCode = "", employeeName = "", zone = "", latitude = "", longitude = "", customerName = "", customerContactNo = "",
            fullAddress = "", pettyContractorId = "", pettyContractorName = "", pettyContractorContactNo = "", pettyEngineerId = "", pettyEngineerName = "", pettyEngineerContactNo = "", builtUpArea = "", sitePotential = "",
            consumedTillDate = "", balancePotential = "", balancePotentialManual = "", siteCategory = "", pricePerBag = "", orderQuantity = "", counterCode = "", dateAndTime = "", asmEmployeeId = "", deliveryRemarks = "",
            reasonForNotDelivery = "", remakrs = "";
    String siteEntryType = "";

    String userType = "";
    String branchCategory = "";
    int valueChecker = 1;
    int typeChecker = 0;

    ArrayList<DataSet> branchList = new ArrayList<>();
    ArrayList<DataSet> stateList = new ArrayList<>();
    ArrayList<DataSet> districtList = new ArrayList<>();
    ArrayList<DataSet> isReqdContractorLinkList = new ArrayList<>();
    ArrayList<DataSet> isReqdEngineerStellarList = new ArrayList<>();
    ArrayList<DataSet> meetingPersonList = new ArrayList<>();
    ArrayList<DataSet> decisionMakerList = new ArrayList<>();
    ArrayList<DataSet> siteSegmentList = new ArrayList<>();
    ArrayList<DataSet> visitTypeList = new ArrayList<>();
    ArrayList<DataSet> projectSegmentList = new ArrayList<>();
    ArrayList<DataSet> typeOfConstructionList = new ArrayList<>();
    ArrayList<DataSet> floorCountList = new ArrayList<>();
    ArrayList<DataSet> currentStageOfConstructionList = new ArrayList<>();
    ArrayList<DataSet> allBrandUsedList = new ArrayList<>();
    ArrayList<DataSet> brandUsedList = new ArrayList<>();
    ArrayList<DataSet> allConversionList = new ArrayList<>();
    ArrayList<DataSet> conversionList = new ArrayList<>();
    ArrayList<DataSet> productList = new ArrayList<>();
    ArrayList<DataSet> counterTypeList = new ArrayList<>();
    ArrayList<DataSet> reasonsForNonConversionList = new ArrayList<>();
    ArrayList<DataSet> priorityList = new ArrayList<>();
    ArrayList<DataSet> weatherShieldDemoList = new ArrayList<>();
    ArrayList<DataSet> approvalStatusList = new ArrayList<>();
    ArrayList<DataSet> asmNameList = new ArrayList<>();
    ArrayList<DataSet> siteStatusList = new ArrayList<>();
    ArrayList<DistrictDataSet> allDistrictList = new ArrayList<>();
    ArrayList<ProductDataSet> allProductList = new ArrayList<>();
    ArrayList<ProfileDataSet> contractorLinkList = new ArrayList<>();
    ArrayList<ProfileDataSet> engineerStellarList = new ArrayList<>();
    ArrayList<CounterNameDataSet> allCounterNameList = new ArrayList<>();
    ArrayList<CounterNameDataSet> counterNameList = new ArrayList<>();
    ArrayList<SiteLeadDataSet> existingSiteLeadList = new ArrayList<>();
    ArrayList<SiteLeadDataSet> asmExistingSiteLeadList = new ArrayList<>();

    SiteLeadDataSet selectedSiteInfo;
    ProgressDialog progressDialog;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    DataForDownloading mDataForDownloading;

    // ***Override function***
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_site_lead);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

        mContext = this;

        mNewDatabaseForSiteLead=new NewDatabaseForSiteLead(mContext);
        mDataForDownloading=new DataForDownloading(mContext);

        employeeName = mNewDatabaseForSiteLead.getEmpName(Constants.employeeDetailObject.getEmpCode());
        zone = mNewDatabaseForSiteLead.getEmpZone(Constants.employeeDetailObject.getEmpCode());
        userType = mNewDatabaseForSiteLead.getEmpDesignation(Constants.employeeDetailObject.getEmpCode());
        initCommon();
        callAllPredefineApi();
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onClick(View view) {
        onClickCommon(view);
        onClickNewSiteButton(view);
        onClickExistingSiteButton(view);
        onClickAsmStatusUpdatePopupButton(view);
    }

    @Override
    public void onCheckedChanged(RadioGroup radioGroup, int i) {
        if (i == R.id.newSiteRadioButton) {
            runOnUiThread(() -> {
                siteEntryType = "NewSite";
                newSiteLayout.setVisibility(VISIBLE);
                existingSiteLayout.setVisibility(GONE);
                clearNewSiteField();
                setNewSiteLeadVisibilitySetup();
                getDefaultData();
            });
        }
        if (i == R.id.existingSiteRadioButton) {
            runOnUiThread(() -> {
                siteEntryType = "ExistingSite";
                newSiteLayout.setVisibility(GONE);
                existingSiteLayout.setVisibility(VISIBLE);
                clearExistingSiteField();
                getDefaultData();
            });
        }
        if(i==R.id.switchSiteRadioButton){
            runOnUiThread(() -> {
                siteEntryType = "SwitchSite";
                newSiteLayout.setVisibility(GONE);
                existingSiteLayout.setVisibility(VISIBLE);
                clearExistingSiteField();
                getDefaultData();
            });
        }
    }

    @SuppressLint({"SetTextI18n", "DefaultLocale", "SimpleDateFormat"})
    private void onTextChangeFunction() {
        edTextNewSiteSitePotential.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                try {
                    float sitePotentialData = Float.parseFloat(s.toString());
                    float consumedTillDateData = 0;
                    if (!edTextNewSiteConsumedTillDate.getText().toString().isEmpty()) {
                        consumedTillDateData = Float.parseFloat(edTextNewSiteConsumedTillDate.getText().toString());
                    }
                    float balancePotentialData = sitePotentialData - consumedTillDateData;
                    edTextNewSiteBalancePotential.setText(String.format("%.0f", balancePotentialData));

                    if (balancePotentialData >= 1000) {
                        edTextNewSiteSiteCategory.setText("High");
                    } else if (balancePotentialData >= 200) {
                        edTextNewSiteSiteCategory.setText("Medium");
                    } else {
                        edTextNewSiteSiteCategory.setText("Low");
                    }

                } catch (Exception e) {
                    if (!s.toString().isEmpty()) {
                        Toast.makeText(NewSiteLeadActivity.this, "Please check Your Site Potential.", Toast.LENGTH_LONG).show();
                    }
                }
            }
        });
        edTextNewSiteConsumedTillDate.addTextChangedListener(new TextWatcher() {
            @SuppressLint("DefaultLocale")
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                try {
                    float sitePotentialData = 0;
                    float consumedTillDateData = Float.parseFloat(s.toString());
                    if (!edTextNewSiteSitePotential.getText().toString().isEmpty()) {
                        sitePotentialData = Float.parseFloat(edTextNewSiteSitePotential.getText().toString());
                    }
                    float balancePotentialData = sitePotentialData - consumedTillDateData;
                    edTextNewSiteBalancePotential.setText(String.format("%.0f", balancePotentialData));

                    if (balancePotentialData >= 1000) {
                        edTextNewSiteSiteCategory.setText("High");
                    } else if (balancePotentialData >= 200) {
                        edTextNewSiteSiteCategory.setText("Medium");
                    } else {
                        edTextNewSiteSiteCategory.setText("Low");
                    }

                } catch (Exception e) {
                    if (!s.toString().isEmpty()) {
                        Toast.makeText(NewSiteLeadActivity.this, "Please check Your Consumed Till Date.", Toast.LENGTH_LONG).show();
                    }
                }
            }
        });
        edTextNewSiteCustomerContactNo.addTextChangedListener(new TextWatcher() {
            @SuppressLint("DefaultLocale")
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                try {
                    if (s.toString().length() > 6) {
                        switch (s.toString().length()) {
                            case 7:
                                uniqueSiteId = new SimpleDateFormat("yyMMdd").format(new Date()) + s.toString().charAt(6) + "***";
                                break;
                            case 8:
                                uniqueSiteId = new SimpleDateFormat("yyMMdd").format(new Date()) + s.toString().substring(6, 8) + "**";
                                break;
                            case 9:
                                uniqueSiteId = new SimpleDateFormat("yyMMdd").format(new Date()) + s.toString().substring(6, 9) + "*";
                                break;
                            case 10:
                                uniqueSiteId = new SimpleDateFormat("yyMMdd").format(new Date()) + s.toString().substring(6, 10);
                                break;
                        }
                        edTextNewSiteUniqueSiteId.setText(uniqueSiteId);
                    }
                } catch (Exception ignored) {
                }
            }
        });
        edTextNewSiteOrderQuantity.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                textNewSiteAsmName.setText("");
                edTextNewSiteAsmEmployeeId.setText("");
                textNewSiteRequestDateOfDelivery.setText("");
                try {
                    if (Integer.parseInt(s.toString()) > 0) {
                        layoutNewSiteAsmEmployeeId.setVisibility(VISIBLE);
                        layoutNewSiteAsmName.setVisibility(VISIBLE);
                        layoutNewSiteRequestDateOfDelivery.setVisibility(VISIBLE);
                    } else {
                        layoutNewSiteAsmEmployeeId.setVisibility(GONE);
                        layoutNewSiteAsmName.setVisibility(GONE);
                        layoutNewSiteRequestDateOfDelivery.setVisibility(GONE);
                    }
                } catch (Exception e) {
                    layoutNewSiteAsmEmployeeId.setVisibility(GONE);
                    layoutNewSiteAsmName.setVisibility(GONE);
                    layoutNewSiteRequestDateOfDelivery.setVisibility(GONE);
                }
            }
        });
        edTextNewSiteBalancePotential.setEnabled(false);
        edTextNewSiteSiteCategory.setEnabled(false);

        edTextExistingSiteSitePotential.addTextChangedListener(new TextWatcher() {
            @SuppressLint("DefaultLocale")
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                try {
                    float sitePotentialData = Float.parseFloat(s.toString());
                    float consumedTillDateData = 0;
                    if (!edTextExistingSiteConsumedTillDate.getText().toString().isEmpty()) {
                        consumedTillDateData = Float.parseFloat(edTextExistingSiteConsumedTillDate.getText().toString());
                    }
                    float balancePotentialData = sitePotentialData - consumedTillDateData;
                    edTextExistingSiteBalancePotential.setText(String.format("%.0f", balancePotentialData));

                    if (balancePotentialData >= 1000) {
                        edTextExistingSiteSiteCategory.setText("High");
                    } else if (balancePotentialData >= 200) {
                        edTextExistingSiteSiteCategory.setText("Medium");
                    } else {
                        edTextExistingSiteSiteCategory.setText("Low");
                    }

                } catch (Exception e) {
                    if (!s.toString().isEmpty()) {
                        Toast.makeText(NewSiteLeadActivity.this, "Please check Your Site Potential.", Toast.LENGTH_LONG).show();
                    }
                }
            }
        });
        edTextExistingSiteConsumedTillDate.addTextChangedListener(new TextWatcher() {
            @SuppressLint("DefaultLocale")
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                try {
                    float sitePotentialData = 0;
                    float consumedTillDateData = Float.parseFloat(s.toString());
                    if (!edTextExistingSiteSitePotential.getText().toString().isEmpty()) {
                        sitePotentialData = Float.parseFloat(edTextExistingSiteSitePotential.getText().toString());
                    }
                    float balancePotentialData = sitePotentialData - consumedTillDateData;
                    edTextExistingSiteBalancePotential.setText(String.format("%.0f", balancePotentialData));
                    if (balancePotentialData >= 1000) {
                        edTextExistingSiteSiteCategory.setText("High");
                    } else if (balancePotentialData >= 200) {
                        edTextExistingSiteSiteCategory.setText("Medium");
                    } else {
                        edTextExistingSiteSiteCategory.setText("Low");
                    }

                } catch (Exception e) {
                    if (!s.toString().isEmpty()) {
                        Toast.makeText(NewSiteLeadActivity.this, "Please check Your Consumed Till Date.", Toast.LENGTH_LONG).show();
                    }
                }
            }
        });
        edTextExistingSiteOrderQuantity.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                textExistingSiteAsmName.setText("");
                edTextExistingSiteAsmEmployeeId.setText("");
                textExistingSiteRequestDateOfDelivery.setText("");
                try {
                    if (Integer.parseInt(s.toString()) > 0) {
                        layoutExistingSiteAsmEmployeeId.setVisibility(VISIBLE);
                        layoutExistingSiteAsmName.setVisibility(VISIBLE);
                        layoutExistingSiteRequestDateOfDelivery.setVisibility(VISIBLE);
                    } else {
                        layoutExistingSiteAsmEmployeeId.setVisibility(GONE);
                        layoutExistingSiteAsmName.setVisibility(GONE);
                        layoutExistingSiteRequestDateOfDelivery.setVisibility(GONE);
                    }
                } catch (Exception e) {
                    layoutExistingSiteAsmEmployeeId.setVisibility(GONE);
                    layoutExistingSiteAsmName.setVisibility(GONE);
                    layoutExistingSiteRequestDateOfDelivery.setVisibility(GONE);
                }
            }
        });
        edTextExistingSiteBalancePotential.setEnabled(false);
        edTextExistingSiteSiteCategory.setEnabled(false);
    }

    // ***Init function***
    private void initCommon() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        submitButton = findViewById(R.id.submitButton);
        submitButton.setOnClickListener(this);

        filterButton = findViewById(R.id.filterButton);
        filterButton.setOnClickListener(this);

        siteLeadFormLayout = findViewById(R.id.siteLeadFormLayout);
        siteLeadApprovalLayout = findViewById(R.id.siteLeadApprovalLayout);

        initPrimary();
    }

    @SuppressLint("SetTextI18n")
    private void initPrimary() {
        newSiteTypeRadioGroup = findViewById(R.id.newSiteTypeRadioGroup);
        RadioButton newSiteRadioButton = findViewById(R.id.newSiteRadioButton);
        RadioButton existingSiteRadioButton = findViewById(R.id.existingSiteRadioButton);
        RadioButton switchSiteRadioButton=findViewById(R.id.switchSiteRadioButton);
        newSiteTypeRadioGroup.setOnCheckedChangeListener(this);

        newSiteRadioButton.setText("New Site");
        existingSiteRadioButton.setText("Existing Site");
        switchSiteRadioButton.setText("Switch Site");

        newSiteLayout = findViewById(R.id.newSiteLayout);
        newSiteLayout.setVisibility(GONE);

        existingSiteLayout = findViewById(R.id.existingSiteLayout);
        existingSiteLayout.setVisibility(GONE);

        initNewSiteLeadEditFieldLinearLayout();
    }

    // New Site init function
    private void initNewSiteLeadEditFieldLinearLayout() {
        layoutNewSitePettyContractorName = findViewById(R.id.layoutNewSitePettyContractorName);
        layoutNewSitePettyContractorContactNo = findViewById(R.id.layoutNewSitePettyContractorContactNo);
        layoutNewSitePettyEngineerName = findViewById(R.id.layoutNewSitePettyEngineerName);
        layoutNewSitePettyEngineerContactNo = findViewById(R.id.layoutNewSitePettyEngineerContactNo);
        layoutNewSiteOrderQuantity = findViewById(R.id.layoutNewSiteOrderQuantity);
        layoutNewSiteCounterCode = findViewById(R.id.layoutNewSiteCounterCode);
        layoutNewSiteAsmEmployeeId = findViewById(R.id.layoutNewSiteAsmEmployeeId);

        layoutNewSiteAsmEmployeeId.setVisibility(GONE);

        initNewSiteLeadEditFieldTextView();
    }

    private void initNewSiteLeadEditFieldTextView() {
        textNewSiteTransactionId = findViewById(R.id.textNewSiteTransactionId);
        textNewSiteUniqueSiteId = findViewById(R.id.textNewSiteUniqueSiteId);
        textNewSiteSiteCreationDate = findViewById(R.id.textNewSiteSiteCreationDate);
        textNewSiteVisitDate = findViewById(R.id.textNewSiteVisitDate);
        textNewSiteEmployeeCode = findViewById(R.id.textNewSiteEmployeeCode);
        textNewSiteEmployeeName = findViewById(R.id.textNewSiteEmployeeName);
        textNewSiteZone = findViewById(R.id.textNewSiteZone);
        textNewSiteLatitude = findViewById(R.id.textNewSiteLatitude);
        textNewSiteLongitude = findViewById(R.id.textNewSiteLongitude);
        textNewSiteCustomerName = findViewById(R.id.textNewSiteCustomerName);
        textNewSiteCustomerContactNo = findViewById(R.id.textNewSiteCustomerContactNo);
        textNewSiteFullAddress = findViewById(R.id.textNewSiteFullAddress);
        textNewSitePettyContractorName = findViewById(R.id.textNewSitePettyContractorName);
        textNewSitePettyContractorContactNo = findViewById(R.id.textNewSitePettyContractorContactNo);
        textNewSitePettyEngineerName = findViewById(R.id.textNewSitePettyEngineerName);
        textNewSitePettyEngineerContactNo = findViewById(R.id.textNewSitePettyEngineerContactNo);
        textNewSiteBuiltUpArea = findViewById(R.id.textNewSiteBuiltUpArea);
        textNewSiteSitePotential = findViewById(R.id.textNewSiteSitePotential);
        textNewSiteConsumedTillDate = findViewById(R.id.textNewSiteConsumedTillDate);
        textNewSiteBalancePotential = findViewById(R.id.textNewSiteBalancePotential);
        textNewSiteBalancePotentialManual = findViewById(R.id.textNewSiteBalancePotentialManual);
        textNewSiteSiteCategory = findViewById(R.id.textNewSiteSiteCategory);
        textNewSitePricePerBag = findViewById(R.id.textNewSitePricePerBag);
        textNewSiteOrderQuantity = findViewById(R.id.textNewSiteOrderQuantity);
        textNewSiteCounterCode = findViewById(R.id.textNewSiteCounterCode);
        textNewSiteAsmEmployeeId = findViewById(R.id.textNewSiteAsmEmployeeId);
        textNewSiteSiteRemarks = findViewById(R.id.textNewSiteSiteRemarks);

        initNewSiteLeadEditFieldEditText();
    }

    private void initNewSiteLeadEditFieldEditText() {
        edTextNewSiteTransactionId = findViewById(R.id.edTextNewSiteTransactionId);
        edTextNewSiteUniqueSiteId = findViewById(R.id.edTextNewSiteUniqueSiteId);
        edTextNewSiteSiteCreationDate = findViewById(R.id.edTextNewSiteSiteCreationDate);
        edTextNewSiteVisitDate = findViewById(R.id.edTextNewSiteVisitDate);
        edTextNewSiteEmployeeCode = findViewById(R.id.edTextNewSiteEmployeeCode);
        edTextNewSiteEmployeeName = findViewById(R.id.edTextNewSiteEmployeeName);
        edTextNewSiteZone = findViewById(R.id.edTextNewSiteZone);
        edTextNewSiteLatitude = findViewById(R.id.edTextNewSiteLatitude);
        edTextNewSiteLongitude = findViewById(R.id.edTextNewSiteLongitude);
        edTextNewSiteCustomerName = findViewById(R.id.edTextNewSiteCustomerName);
        edTextNewSiteCustomerContactNo = findViewById(R.id.edTextNewSiteCustomerContactNo);
        edTextNewSiteFullAddress = findViewById(R.id.edTextNewSiteFullAddress);
        edTextNewSitePettyContractorName = findViewById(R.id.edTextNewSitePettyContractorName);
        edTextNewSitePettyContractorContactNo = findViewById(R.id.edTextNewSitePettyContractorContactNo);
        edTextNewSitePettyEngineerName = findViewById(R.id.edTextNewSitePettyEngineerName);
        edTextNewSitePettyEngineerContactNo = findViewById(R.id.edTextNewSitePettyEngineerContactNo);
        edTextNewSiteBuiltUpArea = findViewById(R.id.edTextNewSiteBuiltUpArea);
        edTextNewSiteSitePotential = findViewById(R.id.edTextNewSiteSitePotential);
        edTextNewSiteConsumedTillDate = findViewById(R.id.edTextNewSiteConsumedTillDate);
        edTextNewSiteBalancePotential = findViewById(R.id.edTextNewSiteBalancePotential);
        edTextNewSiteBalancePotentialManual = findViewById(R.id.edTextNewSiteBalancePotentialManual);
        edTextNewSiteSiteCategory = findViewById(R.id.edTextNewSiteSiteCategory);
        edTextNewSitePricePerBag = findViewById(R.id.edTextNewSitePricePerBag);
        edTextNewSiteOrderQuantity = findViewById(R.id.edTextNewSiteOrderQuantity);
        edTextNewSiteCounterCode = findViewById(R.id.edTextNewSiteCounterCode);
        edTextNewSiteAsmEmployeeId = findViewById(R.id.edTextNewSiteAsmEmployeeId);
        edTextNewSiteSiteRemarks = findViewById(R.id.edTextNewSiteSiteRemarks);

        initNewSiteLeadButtonFieldLinearLayout();
    }

    private void initNewSiteLeadButtonFieldLinearLayout() {
        layoutNewSiteConversion = findViewById(R.id.layoutNewSiteConversion);
        layoutNewSiteProduct = findViewById(R.id.layoutNewSiteProduct);
        layoutNewSiteRequestDateOfDelivery = findViewById(R.id.layoutNewSiteRequestDateOfDelivery);
        layoutNewSiteCounterType = findViewById(R.id.layoutNewSiteCounterType);
        layoutNewSiteCounterName = findViewById(R.id.layoutNewSiteCounterName);
        layoutNewSiteReasonsForNonConversion = findViewById(R.id.layoutNewSiteReasonsForNonConversion);
        layoutNewSiteAsmName = findViewById(R.id.layoutNewSiteAsmName);
        layoutNewSiteTypeOfConstruction = findViewById(R.id.layoutNewSiteTypeOfConstruction);

        layoutNewSiteAsmName.setVisibility(GONE);
        layoutNewSiteRequestDateOfDelivery.setVisibility(GONE);
        layoutNewSiteTypeOfConstruction.setVisibility(GONE);

        initNewSiteLeadButtonFieldButton();
    }

    private void initNewSiteLeadButtonFieldButton() {
        buttonNewSiteBranch = findViewById(R.id.buttonNewSiteBranch);
        buttonNewSiteState = findViewById(R.id.buttonNewSiteState);
        buttonNewSiteDistrict = findViewById(R.id.buttonNewSiteDistrict);
        buttonNewSiteIsReqdContractorLink = findViewById(R.id.buttonNewSiteIsReqdContractorLink);
        buttonNewSiteIsReqdEngineerStellar = findViewById(R.id.buttonNewSiteIsReqdEngineerStellar);
        buttonNewSiteMeetingPerson = findViewById(R.id.buttonNewSiteMeetingPerson);
        buttonNewSiteDecisionMaker = findViewById(R.id.buttonNewSiteDecisionMaker);
        buttonNewSiteSiteSegment = findViewById(R.id.buttonNewSiteSiteSegment);
        buttonNewSiteVisitType = findViewById(R.id.buttonNewSiteVisitType);
        buttonNewSiteProjectSegment = findViewById(R.id.buttonNewSiteProjectSegment);
        buttonNewSiteTypeOfConstruction = findViewById(R.id.buttonNewSiteTypeOfConstruction);
        buttonNewSiteCurrentStageOfConstruction = findViewById(R.id.buttonNewSiteCurrentStageOfConstruction);
        buttonNewSiteBrandUsed = findViewById(R.id.buttonNewSiteBrandUsed);
        buttonNewSiteConversion = findViewById(R.id.buttonNewSiteConversion);
        buttonNewSiteProduct = findViewById(R.id.buttonNewSiteProduct);
        buttonNewSiteRequestDateOfDelivery = findViewById(R.id.buttonNewSiteRequestDateOfDelivery);
        buttonNewSiteCounterType = findViewById(R.id.buttonNewSiteCounterType);
        buttonNewSiteCounterName = findViewById(R.id.buttonNewSiteCounterName);
        buttonNewSiteReasonsForNonConversion = findViewById(R.id.buttonNewSiteReasonsForNonConversion);
        buttonNewSiteSitePriority = findViewById(R.id.buttonNewSiteSitePriority);
        buttonNewSiteWeatherShieldDemo = findViewById(R.id.buttonNewSiteWeatherShieldDemo);
        buttonNewSiteAsmName = findViewById(R.id.buttonNewSiteAsmName);
        buttonNewSiteSiteStatus = findViewById(R.id.buttonNewSiteSiteStatus);

        initNewSiteLeadButtonFieldTextView();
    }

    private void initNewSiteLeadButtonFieldTextView() {
        textNewSiteBranch = findViewById(R.id.textNewSiteBranch);
        textNewSiteState = findViewById(R.id.textNewSiteState);
        textNewSiteDistrict = findViewById(R.id.textNewSiteDistrict);
        textNewSiteIsReqdContractorLink = findViewById(R.id.textNewSiteIsReqdContractorLink);
        textNewSiteIsReqdEngineerStellar = findViewById(R.id.textNewSiteIsReqdEngineerStellar);
        textNewSiteMeetingPerson = findViewById(R.id.textNewSiteMeetingPerson);
        textNewSiteDecisionMaker = findViewById(R.id.textNewSiteDecisionMaker);
        textNewSiteSiteSegment = findViewById(R.id.textNewSiteSiteSegment);
        textNewSiteVisitType = findViewById(R.id.textNewSiteVisitType);
        textNewSiteProjectSegment = findViewById(R.id.textNewSiteProjectSegment);
        textNewSiteTypeOfConstruction = findViewById(R.id.textNewSiteTypeOfConstruction);
        textNewSiteCurrentStageOfConstruction = findViewById(R.id.textNewSiteCurrentStageOfConstruction);
        textNewSiteBrandUsed = findViewById(R.id.textNewSiteBrandUsed);
        textNewSiteConversion = findViewById(R.id.textNewSiteConversion);
        textNewSiteProduct = findViewById(R.id.textNewSiteProduct);
        textNewSiteRequestDateOfDelivery = findViewById(R.id.textNewSiteRequestDateOfDelivery);
        textNewSiteCounterType = findViewById(R.id.textNewSiteCounterType);
        textNewSiteCounterName = findViewById(R.id.textNewSiteCounterName);
        textNewSiteReasonsForNonConversion = findViewById(R.id.textNewSiteReasonsForNonConversion);
        textNewSiteSitePriority = findViewById(R.id.textNewSiteSitePriority);
        textNewSiteWeatherShieldDemo = findViewById(R.id.textNewSiteWeatherShieldDemo);
        textNewSiteAsmName = findViewById(R.id.textNewSiteAsmName);
        textNewSiteSiteStatus = findViewById(R.id.textNewSiteSiteStatus);

        initNewSiteLeadButtonFieldTextViewVisibility();
    }

    private void initNewSiteLeadButtonFieldTextViewVisibility() {
        textNewSiteBranch.setVisibility(GONE);
        textNewSiteState.setVisibility(GONE);
        textNewSiteDistrict.setVisibility(GONE);
        textNewSiteIsReqdContractorLink.setVisibility(GONE);
        textNewSiteIsReqdEngineerStellar.setVisibility(GONE);
        textNewSiteMeetingPerson.setVisibility(GONE);
        textNewSiteDecisionMaker.setVisibility(GONE);
        textNewSiteSiteSegment.setVisibility(GONE);
        textNewSiteVisitType.setVisibility(GONE);
        textNewSiteProjectSegment.setVisibility(GONE);
        textNewSiteTypeOfConstruction.setVisibility(GONE);
        textNewSiteCurrentStageOfConstruction.setVisibility(GONE);
        textNewSiteBrandUsed.setVisibility(GONE);
        textNewSiteConversion.setVisibility(GONE);
        textNewSiteProduct.setVisibility(GONE);
        textNewSiteRequestDateOfDelivery.setVisibility(GONE);
        textNewSiteCounterType.setVisibility(GONE);
        textNewSiteCounterName.setVisibility(GONE);
        textNewSiteReasonsForNonConversion.setVisibility(GONE);
        textNewSiteSitePriority.setVisibility(GONE);
        textNewSiteWeatherShieldDemo.setVisibility(GONE);
        textNewSiteAsmName.setVisibility(GONE);
        textNewSiteSiteStatus.setVisibility(GONE);

        setTitleOfTextNewSiteLayout();
        onClickNewSiteButtonSetup();
        initExistingSiteLeadEditFieldLinearLayout();
    }

    // Existing Site init function
    private void initExistingSiteLeadEditFieldLinearLayout() {
        layoutExistingSitePettyContractorName = findViewById(R.id.layoutExistingSitePettyContractorName);
        layoutExistingSitePettyContractorContactNo = findViewById(R.id.layoutExistingSitePettyContractorContactNo);
        layoutExistingSitePettyEngineerName = findViewById(R.id.layoutExistingSitePettyEngineerName);
        layoutExistingSitePettyEngineerContactNo = findViewById(R.id.layoutExistingSitePettyEngineerContactNo);
        layoutExistingSiteOrderQuantity = findViewById(R.id.layoutExistingSiteOrderQuantity);
        layoutExistingSiteCounterCode = findViewById(R.id.layoutExistingSiteCounterCode);
        layoutExistingSiteDateAndTime = findViewById(R.id.layoutExistingSiteDateAndTime);
        layoutExistingSiteAsmEmployeeId = findViewById(R.id.layoutExistingSiteAsmEmployeeId);
        layoutExistingSiteDeliveryRemarks = findViewById(R.id.layoutExistingSiteDeliveryRemarks);
        layoutExistingSiteReasonForNotDelivery = findViewById(R.id.layoutExistingSiteReasonForNotDelivery);

        layoutExistingSiteAsmEmployeeId.setVisibility(GONE);

        initExistingSiteLeadEditFieldTextView();
    }

    private void initExistingSiteLeadEditFieldTextView() {
        textExistingSiteTransactionId = findViewById(R.id.textExistingSiteTransactionId);
        textExistingSiteSiteCreationDate = findViewById(R.id.textExistingSiteSiteCreationDate);
        textExistingSiteVisitDate = findViewById(R.id.textExistingSiteVisitDate);
        textExistingSiteEmployeeCode = findViewById(R.id.textExistingSiteEmployeeCode);
        textExistingSiteEmployeeName = findViewById(R.id.textExistingSiteEmployeeName);
        textExistingSiteZone = findViewById(R.id.textExistingSiteZone);
        textExistingSiteLatitude = findViewById(R.id.textExistingSiteLatitude);
        textExistingSiteLongitude = findViewById(R.id.textExistingSiteLongitude);
        textExistingSiteCustomerName = findViewById(R.id.textExistingSiteCustomerName);
        textExistingSiteCustomerContactNo = findViewById(R.id.textExistingSiteCustomerContactNo);
        textExistingSiteFullAddress = findViewById(R.id.textExistingSiteFullAddress);
        textExistingSitePettyContractorName = findViewById(R.id.textExistingSitePettyContractorName);
        textExistingSitePettyContractorContactNo = findViewById(R.id.textExistingSitePettyContractorContactNo);
        textExistingSitePettyEngineerName = findViewById(R.id.textExistingSitePettyEngineerName);
        textExistingSitePettyEngineerContactNo = findViewById(R.id.textExistingSitePettyEngineerContactNo);
        textExistingSiteBuiltUpArea = findViewById(R.id.textExistingSiteBuiltUpArea);
        textExistingSiteSitePotential = findViewById(R.id.textExistingSiteSitePotential);
        textExistingSiteConsumedTillDate = findViewById(R.id.textExistingSiteConsumedTillDate);
        textExistingSiteBalancePotential = findViewById(R.id.textExistingSiteBalancePotential);
        textExistingSiteBalancePotentialManual = findViewById(R.id.textExistingSiteBalancePotentialManual);
        textExistingSiteSiteCategory = findViewById(R.id.textExistingSiteSiteCategory);
        textExistingSitePricePerBag = findViewById(R.id.textExistingSitePricePerBag);
        textExistingSiteOrderQuantity = findViewById(R.id.textExistingSiteOrderQuantity);
        textExistingSiteCounterCode = findViewById(R.id.textExistingSiteCounterCode);
        textExistingSiteDateAndTime = findViewById(R.id.textExistingSiteDateAndTime);
        textExistingSiteAsmEmployeeId = findViewById(R.id.textExistingSiteAsmEmployeeId);
        textExistingSiteDeliveryRemarks = findViewById(R.id.textExistingSiteDeliveryRemarks);
        textExistingSiteReasonForNotDelivery = findViewById(R.id.textExistingSiteReasonForNotDelivery);
        textExistingSiteSiteRemarks = findViewById(R.id.textExistingSiteSiteRemarks);

        initExistingSiteLeadEditFieldEditText();
    }

    private void initExistingSiteLeadEditFieldEditText() {
        edTextExistingSiteTransactionId = findViewById(R.id.edTextExistingSiteTransactionId);
        edTextExistingSiteSiteCreationDate = findViewById(R.id.edTextExistingSiteSiteCreationDate);
        edTextExistingSiteVisitDate = findViewById(R.id.edTextExistingSiteVisitDate);
        edTextExistingSiteEmployeeCode = findViewById(R.id.edTextExistingSiteEmployeeCode);
        edTextExistingSiteEmployeeName = findViewById(R.id.edTextExistingSiteEmployeeName);
        edTextExistingSiteZone = findViewById(R.id.edTextExistingSiteZone);
        edTextExistingSiteLatitude = findViewById(R.id.edTextExistingSiteLatitude);
        edTextExistingSiteLongitude = findViewById(R.id.edTextExistingSiteLongitude);
        edTextExistingSiteCustomerName = findViewById(R.id.edTextExistingSiteCustomerName);
        edTextExistingSiteCustomerContactNo = findViewById(R.id.edTextExistingSiteCustomerContactNo);
        edTextExistingSiteFullAddress = findViewById(R.id.edTextExistingSiteFullAddress);
        edTextExistingSitePettyContractorName = findViewById(R.id.edTextExistingSitePettyContractorName);
        edTextExistingSitePettyContractorContactNo = findViewById(R.id.edTextExistingSitePettyContractorContactNo);
        edTextExistingSitePettyEngineerName = findViewById(R.id.edTextExistingSitePettyEngineerName);
        edTextExistingSitePettyEngineerContactNo = findViewById(R.id.edTextExistingSitePettyEngineerContactNo);
        edTextExistingSiteBuiltUpArea = findViewById(R.id.edTextExistingSiteBuiltUpArea);
        edTextExistingSiteSitePotential = findViewById(R.id.edTextExistingSiteSitePotential);
        edTextExistingSiteConsumedTillDate = findViewById(R.id.edTextExistingSiteConsumedTillDate);
        edTextExistingSiteBalancePotential = findViewById(R.id.edTextExistingSiteBalancePotential);
        edTextExistingSiteBalancePotentialManual = findViewById(R.id.edTextExistingSiteBalancePotentialManual);
        edTextExistingSiteSiteCategory = findViewById(R.id.edTextExistingSiteSiteCategory);
        edTextExistingSitePricePerBag = findViewById(R.id.edTextExistingSitePricePerBag);
        edTextExistingSiteOrderQuantity = findViewById(R.id.edTextExistingSiteOrderQuantity);
        edTextExistingSiteCounterCode = findViewById(R.id.edTextExistingSiteCounterCode);
        edTextExistingSiteDateAndTime = findViewById(R.id.edTextExistingSiteDateAndTime);
        edTextExistingSiteAsmEmployeeId = findViewById(R.id.edTextExistingSiteAsmEmployeeId);
        edTextExistingSiteDeliveryRemarks = findViewById(R.id.edTextExistingSiteDeliveryRemarks);
        edTextExistingSiteReasonForNotDelivery = findViewById(R.id.edTextExistingSiteReasonForNotDelivery);
        edTextExistingSiteSiteRemarks = findViewById(R.id.edTextExistingSiteSiteRemarks);

        initExistingSiteLeadButtonFieldLinearLayout();
    }

    private void initExistingSiteLeadButtonFieldLinearLayout() {
        layoutExistingSiteProduct = findViewById(R.id.layoutExistingSiteProduct);
        layoutExistingSiteRequestDateOfDelivery = findViewById(R.id.layoutExistingSiteRequestDateOfDelivery);
        layoutExistingSiteCounterType = findViewById(R.id.layoutExistingSiteCounterType);
        layoutExistingSiteCounterName = findViewById(R.id.layoutExistingSiteCounterName);
        layoutExistingSiteReasonsForNonConversion = findViewById(R.id.layoutExistingSiteReasonsForNonConversion);
        layoutExistingSiteAsmName = findViewById(R.id.layoutExistingSiteAsmName);
        layoutExistingSiteTypeOfConstruction = findViewById(R.id.layoutExistingSiteTypeOfConstruction);
        layoutExistingSiteApprovalStatus=findViewById(R.id.layoutExistingSiteApprovalStatus);

        layoutExistingSiteAsmName.setVisibility(GONE);
        layoutExistingSiteRequestDateOfDelivery.setVisibility(GONE);
        layoutExistingSiteTypeOfConstruction.setVisibility(GONE);

        initExistingSiteLeadButtonFieldButton();
    }

    private void initExistingSiteLeadButtonFieldButton() {
        buttonExistingUniqueId = findViewById(R.id.buttonExistingUniqueId);
        buttonExistingSiteBranch = findViewById(R.id.buttonExistingSiteBranch);
        buttonExistingSiteState = findViewById(R.id.buttonExistingSiteState);
        buttonExistingSiteDistrict = findViewById(R.id.buttonExistingSiteDistrict);
        buttonExistingSiteIsReqdContractorLink = findViewById(R.id.buttonExistingSiteIsReqdContractorLink);
        buttonExistingSiteIsReqdEngineerStellar = findViewById(R.id.buttonExistingSiteIsReqdEngineerStellar);
        buttonExistingSiteMeetingPerson = findViewById(R.id.buttonExistingSiteMeetingPerson);
        buttonExistingSiteDecisionMaker = findViewById(R.id.buttonExistingSiteDecisionMaker);
        buttonExistingSiteSiteSegment = findViewById(R.id.buttonExistingSiteSiteSegment);
        buttonExistingSiteVisitType = findViewById(R.id.buttonExistingSiteVisitType);
        buttonExistingSiteProjectSegment = findViewById(R.id.buttonExistingSiteProjectSegment);
        buttonExistingSiteTypeOfConstruction = findViewById(R.id.buttonExistingSiteTypeOfConstruction);
        buttonExistingSiteCurrentStageOfConstruction = findViewById(R.id.buttonExistingSiteCurrentStageOfConstruction);
        buttonExistingSiteBrandUsed = findViewById(R.id.buttonExistingSiteBrandUsed);
        buttonExistingSiteConversion = findViewById(R.id.buttonExistingSiteConversion);
        buttonExistingSiteProduct = findViewById(R.id.buttonExistingSiteProduct);
        buttonExistingSiteRequestDateOfDelivery = findViewById(R.id.buttonExistingSiteRequestDateOfDelivery);
        buttonExistingSiteCounterType = findViewById(R.id.buttonExistingSiteCounterType);
        buttonExistingSiteCounterName = findViewById(R.id.buttonExistingSiteCounterName);
        buttonExistingSiteReasonsForNonConversion = findViewById(R.id.buttonExistingSiteReasonsForNonConversion);
        buttonExistingSiteSitePriority = findViewById(R.id.buttonExistingSiteSitePriority);
        buttonExistingSiteWeatherShieldDemo = findViewById(R.id.buttonExistingSiteWeatherShieldDemo);
        buttonExistingSiteApprovalStatus = findViewById(R.id.buttonExistingSiteApprovalStatus);
        buttonExistingSiteAsmName = findViewById(R.id.buttonExistingSiteAsmName);
        buttonExistingSiteSiteStatus = findViewById(R.id.buttonExistingSiteSiteStatus);

        initExistingSiteLeadButtonFieldTextView();
    }

    private void initExistingSiteLeadButtonFieldTextView() {
        textExistingUniqueId = findViewById(R.id.textExistingUniqueId);
        textExistingSiteBranch = findViewById(R.id.textExistingSiteBranch);
        textExistingSiteState = findViewById(R.id.textExistingSiteState);
        textExistingSiteDistrict = findViewById(R.id.textExistingSiteDistrict);
        textExistingSiteIsReqdContractorLink = findViewById(R.id.textExistingSiteIsReqdContractorLink);
        textExistingSiteIsReqdEngineerStellar = findViewById(R.id.textExistingSiteIsReqdEngineerStellar);
        textExistingSiteMeetingPerson = findViewById(R.id.textExistingSiteMeetingPerson);
        textExistingSiteDecisionMaker = findViewById(R.id.textExistingSiteDecisionMaker);
        textExistingSiteSiteSegment = findViewById(R.id.textExistingSiteSiteSegment);
        textExistingSiteVisitType = findViewById(R.id.textExistingSiteVisitType);
        textExistingSiteProjectSegment = findViewById(R.id.textExistingSiteProjectSegment);
        textExistingSiteTypeOfConstruction = findViewById(R.id.textExistingSiteTypeOfConstruction);
        textExistingSiteCurrentStageOfConstruction = findViewById(R.id.textExistingSiteCurrentStageOfConstruction);
        textExistingSiteBrandUsed = findViewById(R.id.textExistingSiteBrandUsed);
        textExistingSiteConversion = findViewById(R.id.textExistingSiteConversion);
        textExistingSiteProduct = findViewById(R.id.textExistingSiteProduct);
        textExistingSiteRequestDateOfDelivery = findViewById(R.id.textExistingSiteRequestDateOfDelivery);
        textExistingSiteCounterType = findViewById(R.id.textExistingSiteCounterType);
        textExistingSiteCounterName = findViewById(R.id.textExistingSiteCounterName);
        textExistingSiteReasonsForNonConversion = findViewById(R.id.textExistingSiteReasonsForNonConversion);
        textExistingSiteSitePriority = findViewById(R.id.textExistingSiteSitePriority);
        textExistingSiteWeatherShieldDemo = findViewById(R.id.textExistingSiteWeatherShieldDemo);
        textExistingSiteApprovalStatus = findViewById(R.id.textExistingSiteApprovalStatus);
        textExistingSiteAsmName = findViewById(R.id.textExistingSiteAsmName);
        textExistingSiteSiteStatus = findViewById(R.id.textExistingSiteSiteStatus);

        initExistingSiteLeadButtonFieldTextViewVisibility();
    }

    private void initExistingSiteLeadButtonFieldTextViewVisibility() {
        textExistingSiteBranch.setVisibility(GONE);
        textExistingSiteState.setVisibility(GONE);
        textExistingSiteDistrict.setVisibility(GONE);
        textExistingSiteIsReqdContractorLink.setVisibility(GONE);
        textExistingSiteIsReqdEngineerStellar.setVisibility(GONE);
        textExistingSiteMeetingPerson.setVisibility(GONE);
        textExistingSiteDecisionMaker.setVisibility(GONE);
        textExistingSiteSiteSegment.setVisibility(GONE);
        textExistingSiteVisitType.setVisibility(GONE);
        textExistingSiteProjectSegment.setVisibility(GONE);
        textExistingSiteTypeOfConstruction.setVisibility(GONE);
        textExistingSiteCurrentStageOfConstruction.setVisibility(GONE);
        textExistingSiteBrandUsed.setVisibility(GONE);
        textExistingSiteConversion.setVisibility(GONE);
        textExistingSiteProduct.setVisibility(GONE);
        textExistingSiteRequestDateOfDelivery.setVisibility(GONE);
        textExistingSiteCounterType.setVisibility(GONE);
        textExistingSiteCounterName.setVisibility(GONE);
        textExistingSiteReasonsForNonConversion.setVisibility(GONE);
        textExistingSiteSitePriority.setVisibility(GONE);
        textExistingSiteWeatherShieldDemo.setVisibility(GONE);
        textExistingSiteApprovalStatus.setVisibility(GONE);
        textExistingSiteAsmName.setVisibility(GONE);
        textExistingSiteSiteStatus.setVisibility(GONE);

        initAsmSiteLead();
    }

    // ASM Site Approval init function
    private void initAsmSiteLead() {
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
        asmTextSiteSiteStatus = findViewById(R.id.asmTextSiteSiteStatus);
        asmTextSiteRemarks = findViewById(R.id.asmTextSiteRemarks);

        initAsmSiteLeadPopup();
    }

    private void initAsmSiteLeadPopup() {
        asmStatusUpdatePopup = findViewById(R.id.asmStatusUpdatePopup);
        asmStatusUpdatePopupDesign = findViewById(R.id.asmStatusUpdatePopupDesign);
        layoutActualDateOfDeliveryASM = findViewById(R.id.layoutActualDateOfDeliveryASM);
        layoutDeliveryRemarksASM = findViewById(R.id.layoutDeliveryRemarksASM);
        layoutReasonForNotDeliveryASM = findViewById(R.id.layoutReasonForNotDeliveryASM);
        buttonStatusASM = findViewById(R.id.buttonStatusASM);
        buttonActualDateOfDeliveryASM = findViewById(R.id.buttonActualDateOfDeliveryASM);
        textStatusASM = findViewById(R.id.textStatusASM);
        textActualDateOfDeliveryASM = findViewById(R.id.textActualDateOfDeliveryASM);
        edTextDeliveryRemarksASM = findViewById(R.id.edTextDeliveryRemarksASM);
        edTextReasonForNotDeliveryASM = findViewById(R.id.edTextReasonForNotDeliveryASM);
        updateButton = findViewById(R.id.updateButton);

        asmStatusUpdatePopup.setVisibility(GONE);
        layoutActualDateOfDeliveryASM.setVisibility(GONE);
        layoutDeliveryRemarksASM.setVisibility(GONE);
        layoutReasonForNotDeliveryASM.setVisibility(GONE);

        onClickAsmApprovedPopupButtonSetup();

        setTitleOfTextExistingSiteLayout();
        setNewSiteEditableNonEditableLayout();
        onClickExistingSiteButtonSetup();
        onTextChangeFunction();

        userCategoryWiseUiChange();
    }

    // ***ASM & Other UI***
    @SuppressLint("SetTextI18n")
    private void userCategoryWiseUiChange() {
        if (userType.equalsIgnoreCase("asm")) {
            siteLeadFormLayout.setVisibility(GONE);
            siteLeadApprovalLayout.setVisibility(VISIBLE);
            filterButton.setVisibility(VISIBLE);
            submitButton.setText("Update Status");
        } else {
            filterButton.setVisibility(GONE);
            siteLeadFormLayout.setVisibility(VISIBLE);
            siteLeadApprovalLayout.setVisibility(GONE);
            newSiteTypeRadioGroup.setVisibility(VISIBLE);
        }
    }

    // ***Set Title New Site***
    private void setTitleOfTextNewSiteLayout() {
        textNewSiteTransactionId.setText(Html.fromHtml("Transaction ID No <font color='#FF0000'>*</font>"));
        textNewSiteUniqueSiteId.setText(Html.fromHtml("Unique Site ID No <font color='#FF0000'>*</font>"));
        textNewSiteSiteCreationDate.setText(Html.fromHtml("Site Creation Date <font color='#FF0000'>*</font>"));
        textNewSiteVisitDate.setText(Html.fromHtml("Visit Date <font color='#FF0000'>*</font>"));
        textNewSiteEmployeeCode.setText(Html.fromHtml("Employee Code <font color='#FF0000'>*</font>"));
        textNewSiteEmployeeName.setText(Html.fromHtml("Employee Name <font color='#FF0000'>*</font>"));
        textNewSiteZone.setText(Html.fromHtml("Zone <font color='#FF0000'>*</font>"));
        textNewSiteLatitude.setText(Html.fromHtml("Latitude <font color='#FF0000'>*</font>"));
        textNewSiteLongitude.setText(Html.fromHtml("Longitude <font color='#FF0000'>*</font>"));
        textNewSiteCustomerName.setText(Html.fromHtml("Customer Name <font color='#FF0000'>*</font>"));
        textNewSiteCustomerContactNo.setText(Html.fromHtml("Customer Contact No. <font color='#FF0000'>*</font>"));
        textNewSiteFullAddress.setText(Html.fromHtml("Full Address <font color='#FF0000'>*</font>"));
        textNewSitePettyContractorName.setText(Html.fromHtml("Petty Contractor - Head Mason Name <font color='#FF0000'>*</font>"));
        textNewSitePettyContractorContactNo.setText(Html.fromHtml("Petty Contractor- Head Mason Contact No. <font color='#FF0000'>*</font>"));
        textNewSitePettyEngineerName.setText(Html.fromHtml("Engineer Name <font color='#FF0000'>*</font>"));
        textNewSitePettyEngineerContactNo.setText(Html.fromHtml("Engineer Contact No. <font color='#FF0000'>*</font>"));
        textNewSiteBuiltUpArea.setText(Html.fromHtml("Built-up Area (Sq. Ft.) <font color='#FF0000'>*</font>"));
        textNewSiteSitePotential.setText(Html.fromHtml("Site Potential (No. of Bags) <font color='#FF0000'>*</font>"));
        textNewSiteConsumedTillDate.setText(Html.fromHtml("Consumed Till Date (No. of Bags) <font color='#FF0000'>*</font>"));
        textNewSiteBalancePotential.setText(Html.fromHtml("Balance Potential (No. of Bags) <font color='#FF0000'>*</font>"));
        textNewSiteBalancePotentialManual.setText(Html.fromHtml("Balance Potential (No. of Bags) <font color='#FF0000'>*</font>"));
        textNewSiteSiteCategory.setText(Html.fromHtml("Site Category (Potential based) <font color='#FF0000'>*</font>"));
        textNewSitePricePerBag.setText(Html.fromHtml("Price Per Bag (RSP) <font color='#FF0000'>*</font>"));
        textNewSiteOrderQuantity.setText(Html.fromHtml("No. of Bags Ordered (new) <font color='#FF0000'>*</font>"));
        textNewSiteCounterCode.setText(Html.fromHtml("Counter Code <font color='#FF0000'>*</font>"));
        textNewSiteAsmEmployeeId.setText(Html.fromHtml("ASM Employee ID <font color='#FF0000'>*</font>"));
        textNewSiteSiteRemarks.setText(Html.fromHtml("Remarks"));

        setTitleOfButtonNewSiteLayout();
    }

    private void setTitleOfButtonNewSiteLayout() {
        buttonNewSiteBranch.setText(Html.fromHtml("Branch <font color='#FF0000'>*</font>"));
        buttonNewSiteState.setText(Html.fromHtml("State <font color='#FF0000'>*</font>"));
        buttonNewSiteDistrict.setText(Html.fromHtml("District <font color='#FF0000'>*</font>"));
        buttonNewSiteIsReqdContractorLink.setText(Html.fromHtml("Petty Contractor Regd. In Star Link <font color='#FF0000'>*</font>"));
        buttonNewSiteIsReqdEngineerStellar.setText(Html.fromHtml("Engineer Regd. In Star Stellar <font color='#FF0000'>*</font>"));
        buttonNewSiteMeetingPerson.setText(Html.fromHtml("Meeting Person <font color='#FF0000'>*</font>"));
        buttonNewSiteDecisionMaker.setText(Html.fromHtml("Decision Maker <font color='#FF0000'>*</font>"));
        buttonNewSiteSiteSegment.setText(Html.fromHtml("Site Segment <font color='#FF0000'>*</font>"));
        buttonNewSiteVisitType.setText(Html.fromHtml("Visit Type <font color='#FF0000'>*</font>"));
        buttonNewSiteProjectSegment.setText(Html.fromHtml("Project Segment <font color='#FF0000'>*</font>"));
        buttonNewSiteTypeOfConstruction.setText(Html.fromHtml("Type of Construction <font color='#FF0000'>*</font>"));
        buttonNewSiteCurrentStageOfConstruction.setText(Html.fromHtml("Current Stage of Construction <font color='#FF0000'>*</font>"));
        buttonNewSiteBrandUsed.setText(Html.fromHtml("Brand Used <font color='#FF0000'>*</font>"));
        buttonNewSiteConversion.setText(Html.fromHtml("Business Generation <font color='#FF0000'>*</font>"));
        buttonNewSiteProduct.setText(Html.fromHtml("Select Product <font color='#FF0000'>*</font>"));
        buttonNewSiteRequestDateOfDelivery.setText(Html.fromHtml("Requested Date of Delivery <font color='#FF0000'>*</font>"));
        buttonNewSiteCounterType.setText(Html.fromHtml("Counter Type <font color='#FF0000'>*</font>"));
        buttonNewSiteCounterName.setText(Html.fromHtml("Counter Name <font color='#FF0000'>*</font>"));
        buttonNewSiteReasonsForNonConversion.setText(Html.fromHtml("Reasons for non-conversion <font color='#FF0000'>*</font>"));
        buttonNewSiteSitePriority.setText(Html.fromHtml("Site Priority <font color='#FF0000'>*</font>"));
        buttonNewSiteWeatherShieldDemo.setText(Html.fromHtml("Weather Shield Demo <font color='#FF0000'>*</font>"));
        buttonNewSiteAsmName.setText(Html.fromHtml("ASM Name <font color='#FF0000'>*</font>"));
        buttonNewSiteSiteStatus.setText(Html.fromHtml("Site Status"));

        setButtonColorNewSiteLayout();
    }

    private void setButtonColorNewSiteLayout() {
        buttonNewSiteBranch.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteState.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteDistrict.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteIsReqdContractorLink.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteIsReqdEngineerStellar.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteMeetingPerson.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteDecisionMaker.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteSiteSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteVisitType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteProjectSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteTypeOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteCurrentStageOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteBrandUsed.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteProduct.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteRequestDateOfDelivery.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteCounterType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteCounterName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteReasonsForNonConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteSitePriority.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteWeatherShieldDemo.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteAsmName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        buttonNewSiteSiteStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
    }

    // ***Set Title Existing Site***
    private void setTitleOfTextExistingSiteLayout() {
        textExistingSiteTransactionId.setText(Html.fromHtml("Transaction ID No <font color='#FF0000'>*</font>"));
        textExistingSiteSiteCreationDate.setText(Html.fromHtml("Site Creation Date <font color='#FF0000'>*</font>"));
        textExistingSiteVisitDate.setText(Html.fromHtml("Visit Date <font color='#FF0000'>*</font>"));
        textExistingSiteEmployeeCode.setText(Html.fromHtml("Employee Code <font color='#FF0000'>*</font>"));
        textExistingSiteEmployeeName.setText(Html.fromHtml("Employee Name <font color='#FF0000'>*</font>"));
        textExistingSiteZone.setText(Html.fromHtml("Zone <font color='#FF0000'>*</font>"));
        textExistingSiteLatitude.setText(Html.fromHtml("Latitude <font color='#FF0000'>*</font>"));
        textExistingSiteLongitude.setText(Html.fromHtml("Longitude <font color='#FF0000'>*</font>"));
        textExistingSiteCustomerName.setText(Html.fromHtml("Customer Name <font color='#FF0000'>*</font>"));
        textExistingSiteCustomerContactNo.setText(Html.fromHtml("Customer Contact No. <font color='#FF0000'>*</font>"));
        textExistingSiteFullAddress.setText(Html.fromHtml("Full Address <font color='#FF0000'>*</font>"));
        textExistingSitePettyContractorName.setText(Html.fromHtml("Petty Contractor - Head Mason Name <font color='#FF0000'>*</font>"));
        textExistingSitePettyContractorContactNo.setText(Html.fromHtml("Petty Contractor- Head Mason Contact No. <font color='#FF0000'>*</font>"));
        textExistingSitePettyEngineerName.setText(Html.fromHtml("Engineer Name <font color='#FF0000'>*</font>"));
        textExistingSitePettyEngineerContactNo.setText(Html.fromHtml("Engineer Contact No. <font color='#FF0000'>*</font>"));
        textExistingSiteBuiltUpArea.setText(Html.fromHtml("Built-up Area (Sq. Ft.) <font color='#FF0000'>*</font>"));
        textExistingSiteSitePotential.setText(Html.fromHtml("Site Potential (No. of Bags) <font color='#FF0000'>*</font>"));
        textExistingSiteConsumedTillDate.setText(Html.fromHtml("Consumed Till Date (No. of Bags) <font color='#FF0000'>*</font>"));
        textExistingSiteBalancePotential.setText(Html.fromHtml("Balance Potential (No. of Bags) <font color='#FF0000'>*</font>"));
        textExistingSiteBalancePotentialManual.setText(Html.fromHtml("Balance Potential (No. of Bags) <font color='#FF0000'>*</font>"));
        textExistingSiteSiteCategory.setText(Html.fromHtml("Site Category (Potential based) <font color='#FF0000'>*</font>"));
        textExistingSitePricePerBag.setText(Html.fromHtml("Price Per Bag (RSP) <font color='#FF0000'>*</font>"));
        textExistingSiteOrderQuantity.setText(Html.fromHtml("No. of Bags Ordered (new) <font color='#FF0000'>*</font>"));
        textExistingSiteCounterCode.setText(Html.fromHtml("Counter Code <font color='#FF0000'>*</font>"));
        textExistingSiteDateAndTime.setText(Html.fromHtml("Date and time <font color='#FF0000'>*</font>"));
        textExistingSiteAsmEmployeeId.setText(Html.fromHtml("ASM Employee ID <font color='#FF0000'>*</font>"));
        textExistingSiteDeliveryRemarks.setText(Html.fromHtml("Delivery Remarks"));
        textExistingSiteReasonForNotDelivery.setText(Html.fromHtml("Reason For Not Delivery"));
        textExistingSiteSiteRemarks.setText(Html.fromHtml("Remarks"));

        setTitleOfButtonExistingSiteLayout();
    }

    private void setTitleOfButtonExistingSiteLayout() {
        buttonExistingUniqueId.setText(Html.fromHtml("Unique Id <font color='#FF0000'>*</font>"));
        buttonExistingSiteBranch.setText(Html.fromHtml("Branch <font color='#FF0000'>*</font>"));
        buttonExistingSiteState.setText(Html.fromHtml("State <font color='#FF0000'>*</font>"));
        buttonExistingSiteDistrict.setText(Html.fromHtml("District <font color='#FF0000'>*</font>"));
        buttonExistingSiteIsReqdContractorLink.setText(Html.fromHtml("Petty Contractor Regd. In Star Link <font color='#FF0000'>*</font>"));
        buttonExistingSiteIsReqdEngineerStellar.setText(Html.fromHtml("Engineer Regd. In Star Stellar <font color='#FF0000'>*</font>"));
        buttonExistingSiteMeetingPerson.setText(Html.fromHtml("Meeting Person <font color='#FF0000'>*</font>"));
        buttonExistingSiteDecisionMaker.setText(Html.fromHtml("Decision Maker <font color='#FF0000'>*</font>"));
        buttonExistingSiteSiteSegment.setText(Html.fromHtml("Site Segment <font color='#FF0000'>*</font>"));
        buttonExistingSiteVisitType.setText(Html.fromHtml("Visit Type <font color='#FF0000'>*</font>"));
        buttonExistingSiteProjectSegment.setText(Html.fromHtml("Project Segment <font color='#FF0000'>*</font>"));
        buttonExistingSiteTypeOfConstruction.setText(Html.fromHtml("Type of Construction <font color='#FF0000'>*</font>"));
        buttonExistingSiteCurrentStageOfConstruction.setText(Html.fromHtml("Current Stage of Construction <font color='#FF0000'>*</font>"));
        buttonExistingSiteBrandUsed.setText(Html.fromHtml("Brand Used <font color='#FF0000'>*</font>"));
        buttonExistingSiteConversion.setText(Html.fromHtml("Business Generation <font color='#FF0000'>*</font>"));
        buttonExistingSiteProduct.setText(Html.fromHtml("Select Product <font color='#FF0000'>*</font>"));
        buttonExistingSiteRequestDateOfDelivery.setText(Html.fromHtml("Requested Date of Delivery <font color='#FF0000'>*</font>"));
        buttonExistingSiteCounterType.setText(Html.fromHtml("Counter Type <font color='#FF0000'>*</font>"));
        buttonExistingSiteCounterName.setText(Html.fromHtml("Counter Name <font color='#FF0000'>*</font>"));
        buttonExistingSiteReasonsForNonConversion.setText(Html.fromHtml("Reasons for non-conversion <font color='#FF0000'>*</font>"));
        buttonExistingSiteSitePriority.setText(Html.fromHtml("Site Priority <font color='#FF0000'>*</font>"));
        buttonExistingSiteWeatherShieldDemo.setText(Html.fromHtml("Weather Shield Demo <font color='#FF0000'>*</font>"));
        buttonExistingSiteApprovalStatus.setText(Html.fromHtml("Approval Status <font color='#FF0000'>*</font>"));
        buttonExistingSiteAsmName.setText(Html.fromHtml("ASM Name <font color='#FF0000'>*</font>"));
        buttonExistingSiteSiteStatus.setText(Html.fromHtml("Site Status"));
    }

    // ***Set Site Editable-Non_editable Setup
    private void setNewSiteEditableNonEditableLayout() {
        edTextNewSiteTransactionId.setEnabled(false);
        edTextNewSiteUniqueSiteId.setEnabled(false);
        edTextNewSiteSiteCreationDate.setEnabled(false);
        edTextNewSiteVisitDate.setEnabled(false);
        edTextNewSiteEmployeeCode.setEnabled(false);
        edTextNewSiteEmployeeName.setEnabled(false);
        edTextNewSiteZone.setEnabled(false);
        edTextNewSiteLatitude.setEnabled(false);
        edTextNewSiteLongitude.setEnabled(false);
        edTextNewSiteCounterCode.setEnabled(false);
        edTextNewSiteAsmEmployeeId.setEnabled(false);

        edTextNewSiteTransactionId.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteUniqueSiteId.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteSiteCreationDate.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteVisitDate.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteEmployeeCode.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteEmployeeName.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteZone.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteLatitude.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteLongitude.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteCounterCode.setTextColor(Color.argb(255, 100, 100, 100));
        edTextNewSiteAsmEmployeeId.setTextColor(Color.argb(255, 100, 100, 100));
    }

    // ***Set From VISIBILITY Setup***
    private void setNewSiteLeadVisibilitySetup() {
        layoutNewSitePettyContractorName.setVisibility(GONE);
        layoutNewSitePettyContractorContactNo.setVisibility(GONE);

        layoutNewSitePettyEngineerName.setVisibility(GONE);
        layoutNewSitePettyEngineerContactNo.setVisibility(GONE);

        layoutNewSiteConversion.setVisibility(GONE);

        layoutNewSiteProduct.setVisibility(GONE);

        layoutNewSiteReasonsForNonConversion.setVisibility(GONE);
    }

    // ***Setup On Click function***
    private void onClickNewSiteButtonSetup() {
        buttonNewSiteBranch.setOnClickListener(this);
        buttonNewSiteState.setOnClickListener(this);
        buttonNewSiteDistrict.setOnClickListener(this);
        buttonNewSiteIsReqdContractorLink.setOnClickListener(this);
        buttonNewSiteIsReqdEngineerStellar.setOnClickListener(this);
        buttonNewSiteMeetingPerson.setOnClickListener(this);
        buttonNewSiteDecisionMaker.setOnClickListener(this);
        buttonNewSiteSiteSegment.setOnClickListener(this);
        buttonNewSiteVisitType.setOnClickListener(this);
        buttonNewSiteProjectSegment.setOnClickListener(this);
        buttonNewSiteTypeOfConstruction.setOnClickListener(this);
        buttonNewSiteCurrentStageOfConstruction.setOnClickListener(this);
        buttonNewSiteBrandUsed.setOnClickListener(this);
        buttonNewSiteConversion.setOnClickListener(this);
        buttonNewSiteProduct.setOnClickListener(this);
        buttonNewSiteRequestDateOfDelivery.setOnClickListener(this);
        buttonNewSiteCounterType.setOnClickListener(this);
        buttonNewSiteCounterName.setOnClickListener(this);
        buttonNewSiteReasonsForNonConversion.setOnClickListener(this);
        buttonNewSiteSitePriority.setOnClickListener(this);
        buttonNewSiteWeatherShieldDemo.setOnClickListener(this);
        buttonNewSiteAsmName.setOnClickListener(this);
        buttonNewSiteSiteStatus.setOnClickListener(this);
    }

    private void onClickExistingSiteButtonSetup() {
        buttonExistingUniqueId.setOnClickListener(this);
        buttonExistingSiteBranch.setOnClickListener(this);
        buttonExistingSiteState.setOnClickListener(this);
        buttonExistingSiteDistrict.setOnClickListener(this);
        buttonExistingSiteIsReqdContractorLink.setOnClickListener(this);
        buttonExistingSiteIsReqdEngineerStellar.setOnClickListener(this);
        buttonExistingSiteMeetingPerson.setOnClickListener(this);
        buttonExistingSiteDecisionMaker.setOnClickListener(this);
        buttonExistingSiteSiteSegment.setOnClickListener(this);
        buttonExistingSiteVisitType.setOnClickListener(this);
        buttonExistingSiteProjectSegment.setOnClickListener(this);
        buttonExistingSiteTypeOfConstruction.setOnClickListener(this);
        buttonExistingSiteCurrentStageOfConstruction.setOnClickListener(this);
        buttonExistingSiteBrandUsed.setOnClickListener(this);
        buttonExistingSiteConversion.setOnClickListener(this);
        buttonExistingSiteProduct.setOnClickListener(this);
        buttonExistingSiteRequestDateOfDelivery.setOnClickListener(this);
        buttonExistingSiteCounterType.setOnClickListener(this);
        buttonExistingSiteCounterName.setOnClickListener(this);
        buttonExistingSiteReasonsForNonConversion.setOnClickListener(this);
        buttonExistingSiteSitePriority.setOnClickListener(this);
        buttonExistingSiteWeatherShieldDemo.setOnClickListener(this);
        buttonExistingSiteApprovalStatus.setOnClickListener(this);
        buttonExistingSiteAsmName.setOnClickListener(this);
        buttonExistingSiteSiteStatus.setOnClickListener(this);
    }

    private void onClickAsmApprovedPopupButtonSetup() {
        buttonStatusASM.setOnClickListener(this);
        buttonActualDateOfDeliveryASM.setOnClickListener(this);
        updateButton.setOnClickListener(this);
        asmStatusUpdatePopup.setOnClickListener(this);
        asmStatusUpdatePopupDesign.setOnClickListener(this);
    }

    // ***On Click function***
    private void onClickCommon(View view) {
        if (view == backButton) {
            finish();
        }
        if (view == submitButton) {
            if (userType.equalsIgnoreCase("asm")) {
                asmStatusUpdatePopup.setVisibility(VISIBLE);
            } else {
                if (siteEntryType.equalsIgnoreCase("NewSite")) {
                    checkNewSiteLeadDetails();
                }
                else if (siteEntryType.equalsIgnoreCase("ExistingSite")){
                    checkExistingSiteLeadDetails();
                }
                else {
                    checkSwitchSiteLeadDetails();
                }
            }
        }
        if (view == filterButton) {
            showExistingSiteListDataDialog(asmExistingSiteLeadList, "Select Existing Site Lead", "asm");
        }
    }

    private void onClickNewSiteButton(View view) {
        if (view == buttonNewSiteBranch) {
            showListDataDialog(branchList, "branch", "new", "Select Branch", true);
        }
        if (view == buttonNewSiteState) {
            showListDataDialog(stateList, "state", "new", "Select State", true);
        }
        if (view == buttonNewSiteDistrict) {
            if (state.equalsIgnoreCase("")) {
                Toast.makeText(this, "Please select State before select district.", Toast.LENGTH_LONG).show();
            } else {
                districtList.clear();
                for (int i = 0; i < allDistrictList.size(); i++) {
                    if (allDistrictList.get(i).getStateName().equalsIgnoreCase(state)) {
                        DataSet obj = new DataSet();
                        obj.setValue(allDistrictList.get(i).getValue());
                        obj.setTitle(allDistrictList.get(i).getTitle());
                        districtList.add(obj);
                    }
                }
                showListDataDialog(districtList, "district", "new", "Select District", true);
            }
        }
        if (view == buttonNewSiteIsReqdContractorLink) {
            showListDataDialog(isReqdContractorLinkList, "contractor_link", "new", "Select Please", false);
        }
        if (view == buttonNewSiteIsReqdEngineerStellar) {
            showListDataDialog(isReqdEngineerStellarList, "engineer_stellar", "new", "Select Please", false);
        }
        if (view == buttonNewSiteMeetingPerson) {
            showListDataDialog(meetingPersonList, "meeting_person", "new", "Select Meeting Person", false);
        }
        if (view == buttonNewSiteDecisionMaker) {
            showListDataDialog(decisionMakerList, "decision_maker", "new", "Select Decision Maker", false);
        }
        if (view == buttonNewSiteSiteSegment) {
            showListDataDialog(siteSegmentList, "site_segment", "new", "Select Site Segment", false);
        }
        if (view == buttonNewSiteVisitType) {
            showListDataDialog(visitTypeList, "visit_type", "new", "Select Visit Type", false);
        }
        if (view == buttonNewSiteProjectSegment) {
            showListDataDialog(projectSegmentList, "project_segment", "new", "Select Project Segment", true);
        }
        if (view == buttonNewSiteTypeOfConstruction) {
            showListDataDialog(floorCountList, "type_of_construction", "new", "Select Type of Construction", true);
        }
        if (view == buttonNewSiteCurrentStageOfConstruction) {
            showListDataDialog(currentStageOfConstructionList, "current_stage_of_construction", "new", "Select Current Stage of Construction", true);
        }
        if (view == buttonNewSiteBrandUsed) {
            brandUsedList.clear();
            for (int i = 0; i < allBrandUsedList.size(); i++) {
                if (allBrandUsedList.get(i).getValue().equalsIgnoreCase(visitType)) {
                    DataSet obj = new DataSet();
                    obj.setValue(allBrandUsedList.get(i).getTitle());
                    obj.setTitle(allBrandUsedList.get(i).getTitle());
                    brandUsedList.add(obj);
                }
            }
            showListDataDialog(brandUsedList, "brand_used", "new", "Select Brand Used", true);
        }
        if (view == buttonNewSiteConversion) {
            try {
                if (visitType.equalsIgnoreCase("")) {
                    Toast.makeText(this, "Please select Visit Type before select Business Generation.", Toast.LENGTH_LONG).show();
                } else {
                    conversionList.clear();
                    Log.d("TAG", "_DOWNLOAD_ allConversionList: " + allConversionList.size());
                    for (int i = 0; i < allConversionList.size(); i++) {
                        if (allConversionList.get(i).getValue().equalsIgnoreCase(visitType)) {
                            Log.d("TAG", "_DOWNLOAD_ getValue: " + allConversionList.get(i).getValue());
                            DataSet obj = new DataSet();
                            obj.setValue(allConversionList.get(i).getTitle());
                            obj.setTitle(allConversionList.get(i).getTitle());
                            conversionList.add(obj);
                        }
                    }
                    Log.d("TAG", "_DOWNLOAD_ conversionList: " + conversionList.size());
                    showListDataDialog(conversionList, "conversion", "new", "Select Business Generation", false);
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ onClickNewSiteButton: " + e.getMessage());
            }
        }
        if (view == buttonNewSiteProduct) {
            if (visitType.equalsIgnoreCase("")) {
                Toast.makeText(this, "Please select Visit Type before select Product.", Toast.LENGTH_LONG).show();
            } else if (conversion.equalsIgnoreCase("")) {
                Toast.makeText(this, "Please select Business Generation before select Product.", Toast.LENGTH_LONG).show();
            } else {
                productList.clear();
                Log.d("TAG", "_DOWNLOAD_ buttonNewSiteProduct: " + allProductList.size() + "  " + visitType + "  " + conversion);
                for (int i = 0; i < allProductList.size(); i++) {
                    Log.d("TAG", "_DOWNLOAD_ buttonNewSiteProduct11: " + allProductList.get(i).getVisitType() + "  " + visitType);
                    Log.d("TAG", "_DOWNLOAD_ buttonNewSiteProduct22: " + allProductList.get(i).getConversionType() + "  " + conversion);
                    if (allProductList.get(i).getVisitType().equalsIgnoreCase(visitType) && allProductList.get(i).getConversionType().equalsIgnoreCase(conversion)) {
                        DataSet obj = new DataSet();
                        obj.setValue(allProductList.get(i).getName());
                        obj.setTitle(allProductList.get(i).getName());
                        productList.add(obj);
                    }
                }
                showListDataDialog(productList, "product", "new", "Select Product", false);
            }
        }
        if (view == buttonNewSiteRequestDateOfDelivery) {
            dateTimePicker("request_date_of_delivery", "new");
        }
        if (view == buttonNewSiteCounterType) {
            showListDataDialog(counterTypeList, "counter_type", "new", "Select Counter Type", false);
        }
        if (view == buttonNewSiteCounterName) {
            if (counterType.equalsIgnoreCase("")) {
                Toast.makeText(this, "Please select Counter Type before select Counter Name.", Toast.LENGTH_LONG).show();
            } else {
                counterNameList.clear();
                for (int i = 0; i < allCounterNameList.size(); i++) {
                    if (allCounterNameList.get(i).getType().equalsIgnoreCase(counterType)) {
                        counterNameList.add(allCounterNameList.get(i));
                    }
                }
                showCounterListDataDialog(counterNameList, "counter_name", "new", "Select Counter Name");
            }
        }
        if (view == buttonNewSiteReasonsForNonConversion) {
            showListDataDialog(reasonsForNonConversionList, "reasons_for_non_conversion", "new", "Select Reasons For Non-Business Generation", true);
        }
        if (view == buttonNewSiteSitePriority) {
            showListDataDialog(priorityList, "site_priority", "new", "Select Site Priority", false);
        }
        if (view == buttonNewSiteWeatherShieldDemo) {
            showListDataDialog(weatherShieldDemoList, "weather_shield_demo", "new", "Select Weather Shield Demo", false);
        }
        if (view == buttonNewSiteAsmName) {
            showListDataDialog(asmNameList, "asm_name", "new", "Select ASM Name", true);
        }
        if (view == buttonNewSiteSiteStatus) {
            showListDataDialog(siteStatusList, "site_status", "new", "Select Site Status", false);
        }
    }

    private void onClickExistingSiteButton(View view) {
        if (view == buttonExistingUniqueId) {
            Log.d("TAG", "_DOWNLOAD_ onClickExistingSiteButton: " + existingSiteLeadList.size());
            if(siteEntryType.equalsIgnoreCase("ExistingSite")){
                showExistingSiteListDataDialog(existingSiteLeadList, "Select Existing Site Lead", "other");
            }else{
                ArrayList<SiteLeadDataSet> dataSet = new ArrayList<>();
                for(int i=0;i<existingSiteLeadList.size();i++){
                    if(existingSiteLeadList.get(i).getVisitType().equalsIgnoreCase("star site")){
                        dataSet.add(existingSiteLeadList.get(i));
                    }
                }
                showExistingSiteListDataDialog(dataSet, "Select Existing Site Lead", "other");
            }
        }
        if (view == buttonExistingSiteBranch) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Branch Name.", Toast.LENGTH_LONG).show();
            }else {
                Toast.makeText(this, "For Existing Site you can't able to edit Branch Name.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteState) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit State Name.", Toast.LENGTH_LONG).show();
            }else {
                Toast.makeText(this, "For Existing Site you can't able to edit State Name.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteDistrict) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit District Name.", Toast.LENGTH_LONG).show();
            }else {
                Toast.makeText(this, "For Existing Site you can't able to edit District Name.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteIsReqdContractorLink) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(isReqdContractorLinkList, "contractor_link", "existing", "Select Please", false);
            }
        }
        if (view == buttonExistingSiteIsReqdEngineerStellar) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(isReqdEngineerStellarList, "engineer_stellar", "existing", "Select Please", false);
            }
        }
        if (view == buttonExistingSiteMeetingPerson) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Meeting Person.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(meetingPersonList, "meeting_person", "existing", "Select Meeting Person", false);
            }
        }
        if (view == buttonExistingSiteDecisionMaker) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Decision Maker.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(decisionMakerList, "decision_maker", "existing", "Select Decision Maker", false);
            }
        }
        if (view == buttonExistingSiteSiteSegment) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Site Segment.", Toast.LENGTH_LONG).show();
            }else {
                Toast.makeText(this, "For Existing Site you can't able to edit Site Segment.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteVisitType) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                textExistingSiteVisitType.setVisibility(VISIBLE);
                textExistingSiteVisitType.setText("Non Star Site");
                visitType = "Non Star Site";
                textExistingSiteConversion.setText("Converted to Non Star Site");
                buttonExistingSiteBrandUsed.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
                textExistingSiteBrandUsed.setText("");
                edTextExistingSiteConsumedTillDate.setText("");
                edTextExistingSiteBalancePotentialManual.setText("");
                layoutExistingSiteProduct.setVisibility(GONE);
                layoutExistingSiteOrderQuantity.setVisibility(GONE);
                layoutExistingSiteRequestDateOfDelivery.setVisibility(GONE);
                layoutExistingSiteCounterType.setVisibility(GONE);
                layoutExistingSiteCounterName.setVisibility(GONE);
                layoutExistingSiteCounterCode.setVisibility(GONE);
                layoutExistingSiteApprovalStatus.setVisibility(GONE);
                layoutExistingSiteAsmName.setVisibility(GONE);
                layoutExistingSiteAsmEmployeeId.setVisibility(GONE);
                layoutExistingSiteDeliveryRemarks.setVisibility(GONE);
                edTextExistingSitePricePerBag.setText("");
                edTextExistingSitePricePerBag.setEnabled(true);

                edTextExistingSiteConsumedTillDate.setTextColor(Color.argb(255, 0, 0, 0));
                edTextExistingSiteBalancePotentialManual.setTextColor(Color.argb(255, 0, 0, 0));
                edTextExistingSitePricePerBag.setTextColor(Color.argb(255, 0, 0, 0));

            }else {
                Toast.makeText(this, "For Existing Site you can't able to edit Visit Type.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteProjectSegment) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Project Segment.", Toast.LENGTH_LONG).show();
            }else {
                Toast.makeText(this, "For Existing Site you can't able to edit Project Segment.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteTypeOfConstruction) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Type of Construction.", Toast.LENGTH_LONG).show();
            }else {
                Toast.makeText(this, "For Existing Site you can't able to edit Type of Construction.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteCurrentStageOfConstruction) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Current Stage of Construction.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(currentStageOfConstructionList, "current_stage_of_construction", "existing", "Select Current Stage of Construction", true);
            }
        }
        if (view == buttonExistingSiteBrandUsed) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }

            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                brandUsedList.clear();
                for (int i = 0; i < allBrandUsedList.size(); i++) {
                    if(allBrandUsedList.get(i).getValue().equalsIgnoreCase("Non Star Site")){
                        DataSet obj = new DataSet();
                        obj.setValue(allBrandUsedList.get(i).getTitle());
                        obj.setTitle(allBrandUsedList.get(i).getValue());
                        brandUsedList.add(obj);
                    }
                }
                showListDataDialog(brandUsedList, "brand_used", "existing", "Select Brand Used", true);
            }
            else {
                brandUsedList.clear();
                for (int i = 0; i < allBrandUsedList.size(); i++) {
                    if(textExistingSiteVisitType.getText().toString().trim().equalsIgnoreCase("Star Site")){
                        if(allBrandUsedList.get(i).getValue().equalsIgnoreCase("Star Site")){
                            DataSet obj = new DataSet();
                            obj.setValue(allBrandUsedList.get(i).getTitle());
                            obj.setTitle(allBrandUsedList.get(i).getValue());
                            brandUsedList.add(obj);
                        }
                    }else {
                        DataSet obj = new DataSet();
                        obj.setValue(allBrandUsedList.get(i).getTitle());
                        obj.setTitle(allBrandUsedList.get(i).getValue());
                        brandUsedList.add(obj);
                    }
                }
                showListDataDialog(brandUsedList, "brand_used", "existing", "Select Brand Used", true);
            }
        }
        if (view == buttonExistingSiteConversion) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            try {
                if (visitType.equalsIgnoreCase("")) {
                    Toast.makeText(this, "Please select Visit Type before select Business Generation.", Toast.LENGTH_LONG).show();
                } else {
                    conversionList.clear();

                    if (visitType.equalsIgnoreCase("non star site") && branchCategory.equalsIgnoreCase("star site")) {
                        for (int i = 0; i < allConversionList.size(); i++) {
                            if (allConversionList.get(i).getValue().equalsIgnoreCase(visitType)) {
                                Log.d("TAG", "_DOWNLOAD_ getValue: " + allConversionList.get(i).getValue());
                                DataSet obj = new DataSet();
                                obj.setValue(allConversionList.get(i).getTitle());
                                obj.setTitle(allConversionList.get(i).getTitle());
                                conversionList.add(obj);
                            }
                        }
                    } else {
                        for (int i = 0; i < allConversionList.size(); i++) {
                            if (allConversionList.get(i).getValue().equalsIgnoreCase(branchCategory)) {
                                Log.d("TAG", "_DOWNLOAD_ getValue: " + allConversionList.get(i).getValue());
                                DataSet obj = new DataSet();
                                obj.setValue(allConversionList.get(i).getTitle());
                                obj.setTitle(allConversionList.get(i).getTitle());
                                conversionList.add(obj);
                            }
                        }
                    }

                    Log.d("TAG", "_DOWNLOAD_ conversionList: " + conversionList.size());
                    if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                        Toast.makeText(this, "For Switch Site you can't able to edit Business Generation.", Toast.LENGTH_LONG).show();
                    }else {
                        showListDataDialog(conversionList, "conversion", "existing", "Select Business Generation", false);
                    }
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ onClickNewSiteButton: " + e.getMessage());
            }
        }
        if (view == buttonExistingSiteProduct) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if (visitType.equalsIgnoreCase("")) {
                Toast.makeText(this, "Please select Visit Type before select Product.", Toast.LENGTH_LONG).show();
            } else if (conversion.equalsIgnoreCase("")) {
                Toast.makeText(this, "Please select Business Generation before select Product.", Toast.LENGTH_LONG).show();
            } else {
                productList.clear();
                Log.d("TAG", "_DOWNLOAD_ onClickExistingSiteButton: " + visitType);
                Log.d("TAG", "_DOWNLOAD_ onClickExistingSiteButton: " + conversion);
                for (int i = 0; i < allProductList.size(); i++) {
                    if (allProductList.get(i).getVisitType().equalsIgnoreCase(visitType) && allProductList.get(i).getConversionType().equalsIgnoreCase(conversion)) {
                        DataSet obj = new DataSet();
                        obj.setValue(allProductList.get(i).getName());
                        obj.setTitle(allProductList.get(i).getName());
                        productList.add(obj);
                    }
                }
                if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                    Toast.makeText(this, "For Switch Site you can't able to edit Product.", Toast.LENGTH_LONG).show();
                }else {
                    showListDataDialog(productList, "product", "existing", "Select Product", false);
                }
            }
        }
        if (view == buttonExistingSiteRequestDateOfDelivery) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit.", Toast.LENGTH_LONG).show();
            }else {
                dateTimePicker("request_date_of_delivery", "existing");
            }
        }
        if (view == buttonExistingSiteCounterType) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Counter Type.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(counterTypeList, "counter_type", "existing", "Select Counter Type", false);
            }
        }
        if (view == buttonExistingSiteCounterName) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if (counterType.equalsIgnoreCase("")) {
                Toast.makeText(this, "Please select Counter Type before select Counter Name.", Toast.LENGTH_LONG).show();
            } else {
                counterNameList.clear();
                for (int i = 0; i < allCounterNameList.size(); i++) {
                    if (allCounterNameList.get(i).getType().equalsIgnoreCase(counterType)) {
                        counterNameList.add(allCounterNameList.get(i));
                    }
                }
                if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                    Toast.makeText(this, "For Switch Site you can't able to edit Counter Name.", Toast.LENGTH_LONG).show();
                }else {
                    showCounterListDataDialog(counterNameList, "counter_name", "existing", "Select Counter Name");
                }
            }
        }
        if (view == buttonExistingSiteReasonsForNonConversion) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Reasons For Non-Business Generation.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(reasonsForNonConversionList, "reasons_for_non_conversion", "existing", "Select Reasons For Non-Business Generation", true);
            }
        }
        if (view == buttonExistingSiteSitePriority) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Site Priority.", Toast.LENGTH_LONG).show();
            }else {
                Toast.makeText(this, "For Existing Site you can't able to edit Site Priority.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteWeatherShieldDemo) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Weather Shield Demo.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(weatherShieldDemoList, "weather_shield_demo", "existing", "Select Weather Shield Demo", false);
            }
        }
        if (view == buttonExistingSiteApprovalStatus) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
//                showListDataDialog(approvalStatusList, "approval_status", "existing", "Select Approval Status",false);
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "You can not change the status of Approval.", Toast.LENGTH_LONG).show();
            }else {
                Toast.makeText(this, "You can not change the status of Approval.", Toast.LENGTH_LONG).show();
            }
        }
        if (view == buttonExistingSiteAsmName) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit ASM Name.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(asmNameList, "asm_name", "existing", "Select ASM Name", true);
            }
        }
        if (view == buttonExistingSiteSiteStatus) {
            if (approvalStatus.equalsIgnoreCase("pending") || approvalStatus.equalsIgnoreCase("rejected")) {
                Toast.makeText(this, "Please wait for ASM approval.", Toast.LENGTH_LONG).show();
                return;
            }
            if(siteEntryType.equalsIgnoreCase("SwitchSite")){
                Toast.makeText(this, "For Switch Site you can't able to edit Site Status.", Toast.LENGTH_LONG).show();
            }else {
                showListDataDialog(siteStatusList, "site_status", "existing", "Select Site Status", false);
            }
        }
    }

    private void onClickAsmStatusUpdatePopupButton(View view) {
        if (view == buttonStatusASM) {
            showListDataDialog(approvalStatusList, "approval_status", "popup", "Select Approval Status", false);
        }
        if (view == buttonActualDateOfDeliveryASM) {
            dateTimePicker("actual_date_of_delivery", "popup");
        }
        if (view == updateButton) {
            checkAsmApprovePopupDetails();
        }
        if (view == asmStatusUpdatePopup) {
            approvalStatus = "";
            actualDateOfDelivery = "";
            textStatusASM.setText("");
            textActualDateOfDeliveryASM.setText("");
            edTextDeliveryRemarksASM.setText("");
            edTextReasonForNotDeliveryASM.setText("");
            layoutActualDateOfDeliveryASM.setVisibility(GONE);
            layoutDeliveryRemarksASM.setVisibility(GONE);
            layoutReasonForNotDeliveryASM.setVisibility(GONE);
            asmStatusUpdatePopup.setVisibility(GONE);
        }
    }

    // ***Set Default Date***
    private void setDefaultDataInNewSiteLead() {
        runOnUiThread(() -> {
            edTextNewSiteTransactionId.setText(siteId);
            edTextNewSiteUniqueSiteId.setText(uniqueSiteId);
            edTextNewSiteSiteCreationDate.setText(dateString);
            edTextNewSiteVisitDate.setText(dateString);
            edTextNewSiteLatitude.setText(latitude);
            edTextNewSiteLongitude.setText(longitude);
            edTextNewSiteEmployeeCode.setText(Constants.employeeDetailObject.getEmpCode());
            edTextNewSiteEmployeeName.setText(employeeName);
            edTextNewSiteZone.setText(zone);

            edTextExistingSiteEmployeeCode.setText(Constants.employeeDetailObject.getEmpCode());
            edTextExistingSiteEmployeeName.setText(employeeName);
            edTextExistingSiteZone.setText(zone);
        });
    }

    // ***Clear All Field***
    private void clearNewSiteField() {
        runOnUiThread(() -> {
            edTextNewSiteTransactionId.setText("");
            edTextNewSiteUniqueSiteId.setText("");
            edTextNewSiteSiteCreationDate.setText("");
            edTextNewSiteVisitDate.setText("");
            edTextNewSiteEmployeeCode.setText("");
            edTextNewSiteEmployeeName.setText("");
            edTextNewSiteZone.setText("");
            edTextNewSiteLatitude.setText("");
            edTextNewSiteLongitude.setText("");
            edTextNewSiteCustomerName.setText("");
            edTextNewSiteCustomerContactNo.setText("");
            edTextNewSiteFullAddress.setText("");
            edTextNewSitePettyContractorName.setText("");
            edTextNewSitePettyContractorContactNo.setText("");
            edTextNewSitePettyEngineerName.setText("");
            edTextNewSitePettyEngineerContactNo.setText("");
            edTextNewSiteBuiltUpArea.setText("");
            edTextNewSiteSitePotential.setText("");
            edTextNewSiteConsumedTillDate.setText("");
            edTextNewSiteBalancePotential.setText("");
            edTextNewSiteBalancePotentialManual.setText("");
            edTextNewSiteSiteCategory.setText("");
            edTextNewSitePricePerBag.setText("");
            edTextNewSiteOrderQuantity.setText("");
            edTextNewSiteCounterCode.setText("");
            edTextNewSiteAsmEmployeeId.setText("");
            edTextNewSiteSiteRemarks.setText("");

            textNewSiteBranch.setText("");
            textNewSiteState.setText("");
            textNewSiteDistrict.setText("");
            textNewSiteIsReqdContractorLink.setText("");
            textNewSiteIsReqdEngineerStellar.setText("");
            textNewSiteMeetingPerson.setText("");
            textNewSiteDecisionMaker.setText("");
            textNewSiteSiteSegment.setText("");
            textNewSiteVisitType.setText("");
            textNewSiteProjectSegment.setText("");
            textNewSiteTypeOfConstruction.setText("");
            textNewSiteCurrentStageOfConstruction.setText("");
            textNewSiteBrandUsed.setText("");
            textNewSiteConversion.setText("");
            textNewSiteProduct.setText("");
            textNewSiteRequestDateOfDelivery.setText("");
            textNewSiteCounterType.setText("");
            textNewSiteCounterName.setText("");
            textNewSiteReasonsForNonConversion.setText("");
            textNewSiteSitePriority.setText("");
            textNewSiteWeatherShieldDemo.setText("");
            textNewSiteAsmName.setText("");
            textNewSiteSiteStatus.setText("");

            textNewSiteBranch.setVisibility(GONE);
            textNewSiteState.setVisibility(GONE);
            textNewSiteDistrict.setVisibility(GONE);
            textNewSiteIsReqdContractorLink.setVisibility(GONE);
            textNewSiteIsReqdEngineerStellar.setVisibility(GONE);
            textNewSiteMeetingPerson.setVisibility(GONE);
            textNewSiteDecisionMaker.setVisibility(GONE);
            textNewSiteSiteSegment.setVisibility(GONE);
            textNewSiteVisitType.setVisibility(GONE);
            textNewSiteProjectSegment.setVisibility(GONE);
            textNewSiteTypeOfConstruction.setVisibility(GONE);
            textNewSiteCurrentStageOfConstruction.setVisibility(GONE);
            textNewSiteBrandUsed.setVisibility(GONE);
            textNewSiteConversion.setVisibility(GONE);
            textNewSiteProduct.setVisibility(GONE);
            textNewSiteRequestDateOfDelivery.setVisibility(GONE);
            textNewSiteCounterType.setVisibility(GONE);
            textNewSiteCounterName.setVisibility(GONE);
            textNewSiteReasonsForNonConversion.setVisibility(GONE);
            textNewSiteSitePriority.setVisibility(GONE);
            textNewSiteWeatherShieldDemo.setVisibility(GONE);
            textNewSiteAsmName.setVisibility(GONE);
            textNewSiteSiteStatus.setVisibility(GONE);
        });
    }

    private void clearExistingSiteField() {
        runOnUiThread(() -> {
            edTextExistingSiteTransactionId.setText("");
            edTextExistingSiteSiteCreationDate.setText("");
            edTextExistingSiteVisitDate.setText("");
            edTextExistingSiteEmployeeCode.setText("");
            edTextExistingSiteEmployeeName.setText("");
            edTextExistingSiteZone.setText("");
            edTextExistingSiteLatitude.setText("");
            edTextExistingSiteLongitude.setText("");
            edTextExistingSiteCustomerName.setText("");
            edTextExistingSiteCustomerContactNo.setText("");
            edTextExistingSiteFullAddress.setText("");
            edTextExistingSitePettyContractorName.setText("");
            edTextExistingSitePettyContractorContactNo.setText("");
            edTextExistingSitePettyEngineerName.setText("");
            edTextExistingSitePettyEngineerContactNo.setText("");
            edTextExistingSiteBuiltUpArea.setText("");
            edTextExistingSiteSitePotential.setText("");
            edTextExistingSiteConsumedTillDate.setText("");
            edTextExistingSiteBalancePotential.setText("");
            edTextExistingSiteBalancePotentialManual.setText("");
            edTextExistingSiteSiteCategory.setText("");
            edTextExistingSitePricePerBag.setText("");
            edTextExistingSiteOrderQuantity.setText("");
            edTextExistingSiteCounterCode.setText("");
            edTextExistingSiteDateAndTime.setText("");
            edTextExistingSiteAsmEmployeeId.setText("");
            edTextExistingSiteDeliveryRemarks.setText("");
            edTextExistingSiteReasonForNotDelivery.setText("");
            edTextExistingSiteSiteRemarks.setText("");

            textExistingUniqueId.setText("");
            textExistingSiteBranch.setText("");
            textExistingSiteState.setText("");
            textExistingSiteDistrict.setText("");
            textExistingSiteIsReqdContractorLink.setText("");
            textExistingSiteIsReqdEngineerStellar.setText("");
            textExistingSiteMeetingPerson.setText("");
            textExistingSiteDecisionMaker.setText("");
            textExistingSiteSiteSegment.setText("");
            textExistingSiteVisitType.setText("");
            textExistingSiteProjectSegment.setText("");
            textExistingSiteTypeOfConstruction.setText("");
            textExistingSiteCurrentStageOfConstruction.setText("");
            textExistingSiteBrandUsed.setText("");
            textExistingSiteConversion.setText("");
            textExistingSiteProduct.setText("");
            textExistingSiteRequestDateOfDelivery.setText("");
            textExistingSiteCounterType.setText("");
            textExistingSiteCounterName.setText("");
            textExistingSiteReasonsForNonConversion.setText("");
            textExistingSiteSitePriority.setText("");
            textExistingSiteWeatherShieldDemo.setText("");
            textExistingSiteApprovalStatus.setText("");
            textExistingSiteAsmName.setText("");
            textExistingSiteSiteStatus.setText("");

            textExistingUniqueId.setVisibility(GONE);
            textExistingSiteBranch.setVisibility(GONE);
            textExistingSiteState.setVisibility(GONE);
            textExistingSiteDistrict.setVisibility(GONE);
            textExistingSiteIsReqdContractorLink.setVisibility(GONE);
            textExistingSiteIsReqdEngineerStellar.setVisibility(GONE);
            textExistingSiteMeetingPerson.setVisibility(GONE);
            textExistingSiteDecisionMaker.setVisibility(GONE);
            textExistingSiteSiteSegment.setVisibility(GONE);
            textExistingSiteVisitType.setVisibility(GONE);
            textExistingSiteProjectSegment.setVisibility(GONE);
            textExistingSiteTypeOfConstruction.setVisibility(GONE);
            textExistingSiteCurrentStageOfConstruction.setVisibility(GONE);
            textExistingSiteBrandUsed.setVisibility(GONE);
            textExistingSiteConversion.setVisibility(GONE);
            textExistingSiteProduct.setVisibility(GONE);
            textExistingSiteRequestDateOfDelivery.setVisibility(GONE);
            textExistingSiteCounterType.setVisibility(GONE);
            textExistingSiteCounterName.setVisibility(GONE);
            textExistingSiteReasonsForNonConversion.setVisibility(GONE);
            textExistingSiteSitePriority.setVisibility(GONE);
            textExistingSiteWeatherShieldDemo.setVisibility(GONE);
            textExistingSiteApprovalStatus.setVisibility(GONE);
            textExistingSiteAsmName.setVisibility(GONE);
            textExistingSiteSiteStatus.setVisibility(GONE);
        });
    }

    // ***Primary function***
    @SuppressLint("SimpleDateFormat")
    private void getDefaultData() {
        siteId = "SU" + Constants.employeeDetailObject.getEmpCode() + new SimpleDateFormat("yyyyMMddHHmmss").format(new Date());
        uniqueSiteId = new SimpleDateFormat("yyMMdd").format(new Date()) + "****";
        Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);
        dateString = year + "-";
        if (month < 10) {
            dateString = dateString + "0" + (month + 1) + "-";
        } else {
            dateString = dateString + (month + 1) + "-";
        }
        if (day < 10) {
            dateString = dateString + "0" + day;
        } else {
            dateString = dateString + day;
        }

        SimpleDateFormat sdf = new SimpleDateFormat("HH:mm:ss");
        timeString = sdf.format(new Date());

        LocationTracker locationTracker = new LocationTracker(this);
        locationTracker.checkLocationUpdateSharing();
        new Handler().postDelayed(() -> {
            latitude = "22.5214898";
            longitude = "88.3484784";
            latitude = Constants.currentLat;
            longitude = Constants.currentLong;
            setDefaultDataInNewSiteLead();
        }, 2000);
    }

    // ***Call All Predefine API***
    private void callAllPredefineApi() {
        branchList=mNewDatabaseForSiteLead.getAllBranch();
        stateList=mNewDatabaseForSiteLead.getAllState();
        allDistrictList=mNewDatabaseForSiteLead.getAllDistrict();
        isReqdContractorLinkList=mNewDatabaseForSiteLead.getAllReqContractorLink();
        contractorLinkList=mNewDatabaseForSiteLead.getAllContractorLink();
        isReqdEngineerStellarList=mNewDatabaseForSiteLead.getAllReqEngineerStellar();
        engineerStellarList=mNewDatabaseForSiteLead.getAllEngineerStellar();
        meetingPersonList=mNewDatabaseForSiteLead.getAllMeetingPerson();
        decisionMakerList=mNewDatabaseForSiteLead.getAllDecisionMaker();
        siteSegmentList=mNewDatabaseForSiteLead.getAllSiteSegment();
        visitTypeList=mNewDatabaseForSiteLead.getAllVisitType();
        projectSegmentList=mNewDatabaseForSiteLead.getAllProjectSegment();
        typeOfConstructionList=mNewDatabaseForSiteLead.getAllTypeOfConstruction();
        floorCountList=mNewDatabaseForSiteLead.getAllFloorCount();
        currentStageOfConstructionList=mNewDatabaseForSiteLead.getAllCurrentStageOfConstruction();
        allBrandUsedList=mNewDatabaseForSiteLead.getAllBrandUsed();
        allConversionList=mNewDatabaseForSiteLead.getAllConversion();
        allProductList=mNewDatabaseForSiteLead.getAllProduct();
        counterTypeList=mNewDatabaseForSiteLead.getAllCounterType();
        allCounterNameList=mNewDatabaseForSiteLead.getAllCounterName();
        reasonsForNonConversionList=mNewDatabaseForSiteLead.getAllReasonsForNonConversion();
        priorityList=mNewDatabaseForSiteLead.getAllPriority();
        weatherShieldDemoList=mNewDatabaseForSiteLead.getAllWeatherShieldDemo();
        approvalStatusList=mNewDatabaseForSiteLead.getAllApprovalStatus();
        asmNameList=mNewDatabaseForSiteLead.getAllASMName();
        siteStatusList=mNewDatabaseForSiteLead.getAllSiteStatus();

        Collections.sort(branchList, (o1, o2) ->
                o1.getValue().compareToIgnoreCase(o2.getValue())
        );
        Collections.sort(stateList, (o1, o2) ->
                o1.getValue().compareToIgnoreCase(o2.getValue())
        );
        Collections.sort(allDistrictList, (o1, o2) ->
                o1.getValue().compareToIgnoreCase(o2.getValue())
        );
        Collections.sort(contractorLinkList, (o1, o2) ->
                o1.getName().compareToIgnoreCase(o2.getName())
        );
        Collections.sort(engineerStellarList, (o1, o2) ->
                o1.getName().compareToIgnoreCase(o2.getName())
        );
        Collections.sort(allCounterNameList, (o1, o2) ->
                o1.getName().compareToIgnoreCase(o2.getName())
        );
        Collections.sort(asmNameList, (o1, o2) ->
                o1.getValue().compareToIgnoreCase(o2.getValue())
        );

        _DOWNLOAD_ExistingSiteLeadList();
        _DOWNLOAD_AsmExistingSiteLeadList();
    }

    // ***Progress Loader function***
    private void progressDialogOpen(String title) {
        progressDialog = new ProgressDialog(mContext);
        progressDialog.setMessage(title);
        progressDialog.setCancelable(false);
        progressDialog.show();
    }

    private void progressDialogUpdate(String title) {
        progressDialog.setMessage(title);
    }

    private void progressDialogClose() {
        ((NewSiteLeadActivity) mContext).runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }

    // ***Download TXT File and Save
    private void Download_txt(String URL, String data) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile;
        InputStream is = null;
        java.net.URL url;
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

    // ***ArrayList Item Set***
    public void _DOWNLOAD_ExistingSiteLeadList() {
        ArrayList<DataForUpload> dataList=mNewDatabaseForSiteLead.getAllSiteLead();
        for(int i=0;i<dataList.size();i++){
            DataForUpload dataForUpload=dataList.get(i);
            SiteLeadDataSet temp = new SiteLeadDataSet();
            temp.setId("");
            temp.setTransactionId(dataForUpload.getSite_transaction_id());
            temp.setUniqueId(dataForUpload.getSite_unique_id());
            temp.setVisitDate(dataForUpload.getSite_visit_date());
            temp.setEmpCode(dataForUpload.getEmployee_code());
            temp.setEmpName(dataForUpload.getEmployee_name());
            temp.setZone(dataForUpload.getZone());
            temp.setBranch(dataForUpload.getBranch());
            temp.setDistrict(dataForUpload.getDistrict());
            temp.setState(dataForUpload.getState());
            temp.setLongitude(dataForUpload.getLongitude());
            temp.setLatitude(dataForUpload.getLatitude());
            temp.setCustomerName(dataForUpload.getCustomer_name());
            temp.setCustomerPhoneNo(dataForUpload.getCustomer_contact_number());
            temp.setAddress(dataForUpload.getCustomer_full_address());
            temp.setSiteSegment(dataForUpload.getSite_segment());
            temp.setVisitType(dataForUpload.getVisit_type());
            temp.setProjectSegment(dataForUpload.getProject_segment());
            temp.setTypeOfConst(dataForUpload.getType_of_construction());
            temp.setBuiltUpArea(dataForUpload.getBuilt_up_area());
            temp.setNoOfBag("");
            temp.setConversion(dataForUpload.getConversion());
            temp.setSitePriority(dataForUpload.getSite_priority());
            temp.setCounterCode(dataForUpload.getCounter_code());
            temp.setCreatedAt(dataForUpload.getSite_creation_date());
            temp.setUpdatedAt("");
            temp.setNewSiteLeadId("");
            temp.setNewSiteLeadUniqueId("");
            temp.setPettyContractorRegistered(dataForUpload.getIs_register_contractor());
            temp.setHeadMasonName(dataForUpload.getContractor_name());
            temp.setContractorId("");
            temp.setHeadMasonContact(dataForUpload.getContractor_contact_number());
            temp.setEngineerRegistered(dataForUpload.getIs_register_engineer());
            temp.setEngineerName(dataForUpload.getEngineer_name());
            temp.setEngineerId("");
            temp.setEngineerContact(dataForUpload.getEngineer_contact_number());
            temp.setMeetingPerson(dataForUpload.getMeeting_person());
            temp.setDecisionMaker(dataForUpload.getDecision_maker());
            temp.setCurrentStageOfConstruction(dataForUpload.getCurrent_stage_of_construction());
            temp.setSitePotential(dataForUpload.getSite_potential());
            temp.setConsumedTillDate(dataForUpload.getConsumed_till_date());
            temp.setBalancePotential(dataForUpload.getBalance_potential());
            temp.setSiteCategory(dataForUpload.getSite_category());
            temp.setBrandUsed(dataForUpload.getBrand_used());
            temp.setPricePerBag(dataForUpload.getPrice_per_bag());
            temp.setSelectProduct(dataForUpload.getProduct_name());
            temp.setNoOfBagsOrdered(dataForUpload.getOrder_quantity());
            temp.setRequestedDate(dataForUpload.getRequested_date_of_delivery());
            temp.setCounterType(dataForUpload.getCounter_type());
            temp.setCounterName(dataForUpload.getCounter_name());
            temp.setReasonForNonConversion(dataForUpload.getReason_for_non_conversion());
            temp.setWeatherShieldDemo(dataForUpload.getWeather_shield_demo());
            temp.setApprovalStatus(dataForUpload.getApproval_status());
            temp.setApprovalDateTime(dataForUpload.getDate_time());
            temp.setAsmName(dataForUpload.getAsm_name());
            temp.setAsmId(dataForUpload.getAsm_employee_id());
            temp.setActualDateOfDelivery(dataForUpload.getActual_date_of_delivery());
            temp.setDeliveryRemarks(dataForUpload.getDelivery_remarks());
            temp.setReasonForNotDelivery(dataForUpload.getReason_for_not_delivery());
            temp.setSiteStatus(dataForUpload.getSite_status());
            temp.setFloorCount(dataForUpload.getFloor_count());
            temp.setBalancePotentialManual(dataForUpload.getBalance_potential_manual());
            temp.setRemarks(dataForUpload.getRemarks());
            existingSiteLeadList.add(temp);
        }
    }

    public void _DOWNLOAD_AsmExistingSiteLeadList() {
//        progressDialogUpdate("Downloading Existing Site Lead List ...");
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_get_asm_reqst_site_lead.php?asm_id=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            Download_txt(URL, "AsmExistingSiteLeadList");
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
                        String[] RowData = line.split("\\^");
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

                            asmExistingSiteLeadList.add(temp);
                        }
                    }
                }
                buffer.close();
//                progressDialogClose();
            } catch (IOException ex) {
//                progressDialogClose();
            }
        }).start();
    }

    // ***Select Popup Design***
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
                searchLayout.setVisibility(VISIBLE);
            else
                searchLayout.setVisibility(GONE);

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                if (value.equalsIgnoreCase("branch")) {
                    textNewSiteBranch.setVisibility(VISIBLE);
                    textNewSiteBranch.setText(Objects.requireNonNull(pAdapter.getItem(position)).getValue());
                    branch = Objects.requireNonNull(pAdapter.getItem(position)).getValue();
                    branchCode = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("state")) {
                    textNewSiteState.setVisibility(VISIBLE);
                    textNewSiteState.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    state = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("district")) {
                    textNewSiteDistrict.setVisibility(VISIBLE);
                    textNewSiteDistrict.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    district = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("contractor_link")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteIsReqdContractorLink.setVisibility(VISIBLE);
                        textNewSiteIsReqdContractorLink.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("yes") ||
                                Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("no")) {
                            layoutNewSitePettyContractorName.setVisibility(VISIBLE);
                            layoutNewSitePettyContractorContactNo.setVisibility(VISIBLE);
                        }
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("yes")) {
                            showProfileNameListDataDialog(contractorLinkList, "link", "new", "Select Contractor Name");
                            edTextNewSitePettyContractorName.setEnabled(false);
                            edTextNewSitePettyContractorContactNo.setEnabled(false);
                            textNewSitePettyContractorName.setText(Html.fromHtml("Petty Contractor - Head Mason Name <font color='#FF0000'>*</font>"));
                            textNewSitePettyContractorContactNo.setText(Html.fromHtml("Petty Contractor- Head Mason Contact No. <font color='#FF0000'>*</font>"));
                        } else if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("no")) {
                            edTextNewSitePettyContractorName.setEnabled(true);
                            edTextNewSitePettyContractorContactNo.setEnabled(true);
                            edTextNewSitePettyContractorName.setText("");
                            edTextNewSitePettyContractorContactNo.setText("");
                            textNewSitePettyContractorName.setText(Html.fromHtml("Petty Contractor - Head Mason Name"));
                            textNewSitePettyContractorContactNo.setText(Html.fromHtml("Petty Contractor- Head Mason Contact No."));
                        } else {
                            layoutNewSitePettyContractorName.setVisibility(GONE);
                            layoutNewSitePettyContractorContactNo.setVisibility(GONE);
                        }
                    } else {
                        textExistingSiteIsReqdContractorLink.setVisibility(VISIBLE);
                        textExistingSiteIsReqdContractorLink.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("yes") ||
                                Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("no")) {
                            layoutExistingSitePettyContractorName.setVisibility(VISIBLE);
                            layoutExistingSitePettyContractorContactNo.setVisibility(VISIBLE);
                        }
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("yes")) {
                            showProfileNameListDataDialog(contractorLinkList, "link", "existing", "Select Contractor Name");
                            edTextExistingSitePettyContractorName.setEnabled(false);
                            edTextExistingSitePettyContractorContactNo.setEnabled(false);
                            textNewSitePettyContractorName.setText(Html.fromHtml("Petty Contractor - Head Mason Name <font color='#FF0000'>*</font>"));
                            textNewSitePettyContractorContactNo.setText(Html.fromHtml("Petty Contractor- Head Mason Contact No. <font color='#FF0000'>*</font>"));
                        } else if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("no")) {
                            edTextExistingSitePettyContractorName.setEnabled(true);
                            edTextExistingSitePettyContractorContactNo.setEnabled(true);
                            edTextExistingSitePettyContractorName.setText("");
                            edTextExistingSitePettyContractorContactNo.setText("");
                            textNewSitePettyContractorName.setText(Html.fromHtml("Petty Contractor - Head Mason Name"));
                            textNewSitePettyContractorContactNo.setText(Html.fromHtml("Petty Contractor- Head Mason Contact No."));
                        } else {
                            layoutExistingSitePettyContractorName.setVisibility(GONE);
                            layoutExistingSitePettyContractorContactNo.setVisibility(GONE);
                        }
                    }
                    isReqdContractorLink = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("engineer_stellar")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteIsReqdEngineerStellar.setVisibility(VISIBLE);
                        textNewSiteIsReqdEngineerStellar.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("yes") ||
                                Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("no")) {
                            layoutNewSitePettyEngineerName.setVisibility(VISIBLE);
                            layoutNewSitePettyEngineerContactNo.setVisibility(VISIBLE);
                        }
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("yes")) {
                            showProfileNameListDataDialog(engineerStellarList, "engg", "new", "Select Engineer Name");
                            edTextNewSitePettyEngineerName.setEnabled(false);
                            edTextNewSitePettyEngineerContactNo.setEnabled(false);
                            textNewSitePettyEngineerName.setText(Html.fromHtml("Engineer Name <font color='#FF0000'>*</font>"));
                            textNewSitePettyEngineerContactNo.setText(Html.fromHtml("Engineer Contact No. <font color='#FF0000'>*</font>"));
                        } else if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("no")) {
                            edTextNewSitePettyEngineerName.setEnabled(true);
                            edTextNewSitePettyEngineerContactNo.setEnabled(true);
                            edTextNewSitePettyEngineerName.setText("");
                            edTextNewSitePettyEngineerContactNo.setText("");
                            textNewSitePettyEngineerName.setText(Html.fromHtml("Engineer Name"));
                            textNewSitePettyEngineerContactNo.setText(Html.fromHtml("Engineer Contact No."));
                        } else {
                            layoutNewSitePettyEngineerName.setVisibility(GONE);
                            layoutNewSitePettyEngineerContactNo.setVisibility(GONE);
                        }
                    } else {
                        textExistingSiteIsReqdEngineerStellar.setVisibility(VISIBLE);
                        textExistingSiteIsReqdEngineerStellar.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("yes") ||
                                Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("no")) {
                            layoutExistingSitePettyEngineerName.setVisibility(VISIBLE);
                            layoutExistingSitePettyEngineerContactNo.setVisibility(VISIBLE);
                        }
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("yes")) {
                            showProfileNameListDataDialog(engineerStellarList, "engg", "existing", "Select Engineer Name");
                            edTextExistingSitePettyEngineerName.setEnabled(false);
                            edTextExistingSitePettyEngineerContactNo.setEnabled(false);
                            textNewSitePettyEngineerName.setText(Html.fromHtml("Engineer Name <font color='#FF0000'>*</font>"));
                            textNewSitePettyEngineerContactNo.setText(Html.fromHtml("Engineer Contact No. <font color='#FF0000'>*</font>"));
                        } else if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("no")) {
                            edTextExistingSitePettyEngineerName.setEnabled(true);
                            edTextExistingSitePettyEngineerContactNo.setEnabled(true);
                            edTextExistingSitePettyEngineerName.setText("");
                            edTextExistingSitePettyEngineerContactNo.setText("");
                            textNewSitePettyEngineerName.setText(Html.fromHtml("Engineer Name"));
                            textNewSitePettyEngineerContactNo.setText(Html.fromHtml("Engineer Contact No."));
                        } else {
                            layoutExistingSitePettyEngineerName.setVisibility(GONE);
                            layoutExistingSitePettyEngineerContactNo.setVisibility(GONE);
                        }
                    }
                    isReqdEngineerStellar = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("meeting_person")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteMeetingPerson.setVisibility(VISIBLE);
                        textNewSiteMeetingPerson.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteMeetingPerson.setVisibility(VISIBLE);
                        textExistingSiteMeetingPerson.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    meetingPerson = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("decision_maker")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteDecisionMaker.setVisibility(VISIBLE);
                        textNewSiteDecisionMaker.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteDecisionMaker.setVisibility(VISIBLE);
                        textExistingSiteDecisionMaker.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    decisionMaker = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("site_segment")) {
                    textNewSiteSiteSegment.setVisibility(VISIBLE);
                    textNewSiteSiteSegment.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    siteSegment = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("visit_type")) {
                    if(type.equalsIgnoreCase("new")){
                        textNewSiteVisitType.setVisibility(VISIBLE);
                        textNewSiteVisitType.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        visitType = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                        layoutNewSiteConversion.setVisibility(VISIBLE);
                    }else{
                        textExistingSiteVisitType.setVisibility(VISIBLE);
                        textExistingSiteVisitType.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        visitType = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                        if(Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("non star site")){
                            textExistingSiteConversion.setText("Converted to Non Star Site");
                        }
                    }
                }
                if (value.equalsIgnoreCase("project_segment")) {
                    textNewSiteProjectSegment.setVisibility(VISIBLE);
                    textNewSiteProjectSegment.setText(Objects.requireNonNull(pAdapter.getItem(position)).getValue());
                    projectSegment = Objects.requireNonNull(pAdapter.getItem(position)).getValue();
                    if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("1")) {
                        layoutNewSiteTypeOfConstruction.setVisibility(VISIBLE);
                        typeOfConstruction = "";
                        typeChecker = 1;
                    } else {
                        layoutNewSiteTypeOfConstruction.setVisibility(GONE);
                        typeOfConstruction = "";
                        typeChecker = 0;
                    }
                }
                if (value.equalsIgnoreCase("type_of_construction")) {
                    textNewSiteTypeOfConstruction.setVisibility(VISIBLE);
                    textNewSiteTypeOfConstruction.setText(Objects.requireNonNull(pAdapter.getItem(position)).getValue());
                    typeOfConstruction = Objects.requireNonNull(pAdapter.getItem(position)).getValue();
                }
                if (value.equalsIgnoreCase("current_stage_of_construction")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteCurrentStageOfConstruction.setVisibility(VISIBLE);
                        textNewSiteCurrentStageOfConstruction.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteCurrentStageOfConstruction.setVisibility(VISIBLE);
                        textExistingSiteCurrentStageOfConstruction.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    currentStageOfConstruction = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("brand_used")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteBrandUsed.setVisibility(VISIBLE);
                        textNewSiteBrandUsed.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        brandUsed = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                    } else {
                        textExistingSiteBrandUsed.setVisibility(VISIBLE);
                        textExistingSiteBrandUsed.setText(Objects.requireNonNull(pAdapter.getItem(position)).getValue());
                        branchCategory = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                        Log.d("TAG", "_DOWNLOAD_ branchCategory: " + branchCategory);
                        brandUsed = Objects.requireNonNull(pAdapter.getItem(position)).getValue();
                        textExistingSiteVisitType.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                }
                if (value.equalsIgnoreCase("conversion")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteConversion.setVisibility(VISIBLE);
                        textNewSiteConversion.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());

                        if (visitType.equalsIgnoreCase("non star site") && Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("non converted")) {
                            layoutNewSiteProduct.setVisibility(GONE);
                            layoutNewSiteOrderQuantity.setVisibility(GONE);
                            layoutNewSiteRequestDateOfDelivery.setVisibility(GONE);
                            layoutNewSiteCounterType.setVisibility(GONE);
                            layoutNewSiteCounterName.setVisibility(GONE);
                            layoutNewSiteCounterCode.setVisibility(GONE);
                            layoutNewSiteReasonsForNonConversion.setVisibility(VISIBLE);
                        } else {
                            layoutNewSiteProduct.setVisibility(VISIBLE);
                            layoutNewSiteOrderQuantity.setVisibility(VISIBLE);
                            layoutNewSiteRequestDateOfDelivery.setVisibility(VISIBLE);
                            layoutNewSiteCounterType.setVisibility(VISIBLE);
                            layoutNewSiteCounterName.setVisibility(VISIBLE);
                            layoutNewSiteCounterCode.setVisibility(VISIBLE);
                            layoutNewSiteReasonsForNonConversion.setVisibility(GONE);
                        }

                        textNewSiteProduct.setText("");
                        edTextNewSiteOrderQuantity.setText("");
                        textNewSiteRequestDateOfDelivery.setText("");
                        textNewSiteCounterType.setText("");
                        textNewSiteCounterName.setText("");
                        edTextNewSiteCounterCode.setText("");
                        textNewSiteReasonsForNonConversion.setText("");

                        product = "";
                        orderQuantity = "";
                        requestDateOfDelivery = "";
                        counterType = "";
                        counterName = "";
                        counterCode = "";
                        reasonsForNonConversion = "";
                    } else {
                        textExistingSiteConversion.setVisibility(VISIBLE);
                        textExistingSiteConversion.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());

                        if (visitType.equalsIgnoreCase("non star site") && (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("non converted")||Objects.requireNonNull(pAdapter.getItem(position)).getTitle().equalsIgnoreCase("converted to non star site"))) {
                            layoutExistingSiteProduct.setVisibility(GONE);
                            layoutExistingSiteOrderQuantity.setVisibility(GONE);
                            layoutExistingSiteRequestDateOfDelivery.setVisibility(GONE);
                            layoutExistingSiteCounterType.setVisibility(GONE);
                            layoutExistingSiteCounterName.setVisibility(GONE);
                            layoutExistingSiteCounterCode.setVisibility(GONE);
                            layoutExistingSiteReasonsForNonConversion.setVisibility(VISIBLE);
                        } else {
                            layoutExistingSiteProduct.setVisibility(VISIBLE);
                            layoutExistingSiteOrderQuantity.setVisibility(VISIBLE);
                            layoutExistingSiteRequestDateOfDelivery.setVisibility(VISIBLE);
                            layoutExistingSiteCounterType.setVisibility(VISIBLE);
                            layoutExistingSiteCounterName.setVisibility(VISIBLE);
                            layoutExistingSiteCounterCode.setVisibility(VISIBLE);
                            layoutExistingSiteReasonsForNonConversion.setVisibility(GONE);
                        }

                        textExistingSiteProduct.setText("");
                        edTextExistingSiteOrderQuantity.setText("");
                        textExistingSiteRequestDateOfDelivery.setText("");
                        textExistingSiteCounterType.setText("");
                        textExistingSiteCounterName.setText("");
                        edTextExistingSiteCounterCode.setText("");
                        textExistingSiteReasonsForNonConversion.setText("");

                        product = "";
                        orderQuantity = "";
                        requestDateOfDelivery = "";
                        counterType = "";
                        counterName = "";
                        counterCode = "";
                        reasonsForNonConversion = "";
                    }
                    conversion = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("product")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteProduct.setVisibility(VISIBLE);
                        textNewSiteProduct.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteProduct.setVisibility(VISIBLE);
                        textExistingSiteProduct.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    product = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("counter_type")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteCounterType.setVisibility(VISIBLE);
                        textNewSiteCounterType.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteCounterType.setVisibility(VISIBLE);
                        textExistingSiteCounterType.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    counterType = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("reasons_for_non_conversion")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteReasonsForNonConversion.setVisibility(VISIBLE);
                        textNewSiteReasonsForNonConversion.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteReasonsForNonConversion.setVisibility(VISIBLE);
                        textExistingSiteReasonsForNonConversion.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    reasonsForNonConversion = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("site_priority")) {
                    textNewSiteSitePriority.setVisibility(VISIBLE);
                    textNewSiteSitePriority.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    priority = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("weather_shield_demo")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteWeatherShieldDemo.setVisibility(VISIBLE);
                        textNewSiteWeatherShieldDemo.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteWeatherShieldDemo.setVisibility(VISIBLE);
                        textExistingSiteWeatherShieldDemo.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    weatherShieldDemo = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("approval_status")) {
                    if (type.equalsIgnoreCase("popup")) {
                        textStatusASM.setVisibility(VISIBLE);
                        textStatusASM.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                        if (Objects.requireNonNull(pAdapter.getItem(position)).getTitle().toLowerCase().startsWith("approved")) {
                            layoutActualDateOfDeliveryASM.setVisibility(VISIBLE);
                            layoutDeliveryRemarksASM.setVisibility(VISIBLE);
                            layoutReasonForNotDeliveryASM.setVisibility(GONE);
                        } else {
                            layoutActualDateOfDeliveryASM.setVisibility(GONE);
                            layoutDeliveryRemarksASM.setVisibility(GONE);
                            layoutReasonForNotDeliveryASM.setVisibility(VISIBLE);
                        }
                    } else if (!type.equalsIgnoreCase("new")) {
                        textExistingSiteApprovalStatus.setVisibility(VISIBLE);
                        textExistingSiteApprovalStatus.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    approvalStatus = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
                if (value.equalsIgnoreCase("asm_name")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteAsmName.setVisibility(VISIBLE);
                        textNewSiteAsmName.setText(Objects.requireNonNull(pAdapter.getItem(position)).getValue());
                        edTextNewSiteAsmEmployeeId.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteAsmName.setVisibility(VISIBLE);
                        textExistingSiteAsmName.setText(Objects.requireNonNull(pAdapter.getItem(position)).getValue());
                        edTextExistingSiteAsmEmployeeId.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                }
                if (value.equalsIgnoreCase("site_status")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteSiteStatus.setVisibility(VISIBLE);
                        textNewSiteSiteStatus.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    } else {
                        textExistingSiteSiteStatus.setVisibility(VISIBLE);
                        textExistingSiteSiteStatus.setText(Objects.requireNonNull(pAdapter.getItem(position)).getTitle());
                    }
                    siteStatus = Objects.requireNonNull(pAdapter.getItem(position)).getTitle();
                }
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SetTextI18n")
    public void showProfileNameListDataDialog(ArrayList<ProfileDataSet> dataSet, String value, String type, String titleValue) {
        try {
            final ShowProfileDataSetAdapter pAdapter = new ShowProfileDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);

            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());
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

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                switch (value) {
                    case "link":
                        if (type.equalsIgnoreCase("new")) {
                            edTextNewSitePettyContractorName.setText(Objects.requireNonNull(pAdapter.getItem(position)).getName());
                            edTextNewSitePettyContractorContactNo.setText(Objects.requireNonNull(pAdapter.getItem(position)).getNumber());
                        } else {
                            edTextExistingSitePettyContractorName.setText(Objects.requireNonNull(pAdapter.getItem(position)).getName());
                            edTextExistingSitePettyContractorContactNo.setText(Objects.requireNonNull(pAdapter.getItem(position)).getNumber());
                        }
                        pettyContractorId = Objects.requireNonNull(pAdapter.getItem(position)).getCode();
                        break;
                    case "engg":
                        if (type.equalsIgnoreCase("new")) {
                            edTextNewSitePettyEngineerName.setText(Objects.requireNonNull(pAdapter.getItem(position)).getName());
                            edTextNewSitePettyEngineerContactNo.setText(Objects.requireNonNull(pAdapter.getItem(position)).getNumber());
                        } else {
                            edTextExistingSitePettyEngineerName.setText(Objects.requireNonNull(pAdapter.getItem(position)).getName());
                            edTextExistingSitePettyEngineerContactNo.setText(Objects.requireNonNull(pAdapter.getItem(position)).getNumber());
                        }
                        pettyEngineerId = Objects.requireNonNull(pAdapter.getItem(position)).getCode();
                        break;
                }
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SetTextI18n")
    public void showCounterListDataDialog(ArrayList<CounterNameDataSet> dataSet, String value, String type, String titleValue) {
        try {
            final ShowCounterDataSetAdapter pAdapter = new ShowCounterDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());
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

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                if (value.equals("counter_name")) {
                    if (type.equalsIgnoreCase("new")) {
                        textNewSiteCounterName.setVisibility(VISIBLE);
                        textNewSiteCounterName.setText(Objects.requireNonNull(pAdapter.getItem(position)).getName());
                        edTextNewSiteCounterCode.setText(Objects.requireNonNull(pAdapter.getItem(position)).getCode());
                    } else {
                        textExistingSiteCounterName.setVisibility(VISIBLE);
                        textExistingSiteCounterName.setText(Objects.requireNonNull(pAdapter.getItem(position)).getName());
                        edTextExistingSiteCounterCode.setText(Objects.requireNonNull(pAdapter.getItem(position)).getCode());
                    }
                    counterName = Objects.requireNonNull(pAdapter.getItem(position)).getName();
                    counterCode = Objects.requireNonNull(pAdapter.getItem(position)).getCode();
                }
            });

            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SetTextI18n")
    public void showExistingSiteListDataDialog(ArrayList<SiteLeadDataSet> dataSet, String titleValue, String type) {
        try {
            final ShowExistingSiteDataSetAdapter pAdapter = new ShowExistingSiteDataSetAdapter(this, R.layout.existing_site_lead_item, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());
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

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                if (type.equalsIgnoreCase("asm")) {
                    selectedSiteInfo = Objects.requireNonNull(pAdapter.getItem(position));
                    showAsmExistingSiteLeadInfo(Objects.requireNonNull(pAdapter.getItem(position)));
                } else {
                    if(siteEntryType.equalsIgnoreCase("ExistingSite")){
                        showExistingSiteLeadInfo(Objects.requireNonNull(pAdapter.getItem(position)));
                    }else{
                        showSwitchSiteLeadInfo(Objects.requireNonNull(pAdapter.getItem(position)));
                    }
                }
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    // ***Show Old Data Set***
    private void showExistingSiteLeadInfo(SiteLeadDataSet dataSet) {
        runOnUiThread(() -> {
            branchCode = dataSet.getBranch();
            for (int i = 0; i < branchList.size(); i++) {
                if (branchList.get(i).getTitle().equalsIgnoreCase(branchCode)) {
                    branch = branchList.get(i).getValue();
                    break;
                }
            }
            state = dataSet.getState();
            district = dataSet.getDistrict();
            isReqdContractorLink = dataSet.getPettyContractorRegistered();
            isReqdEngineerStellar = dataSet.getEngineerRegistered();
            meetingPerson = dataSet.getMeetingPerson();
            decisionMaker = dataSet.getDecisionMaker();
            siteSegment = dataSet.getSiteSegment();
            visitType = dataSet.getVisitType();
            projectSegment = dataSet.getProjectSegment();
            typeOfConstruction = dataSet.getTypeOfConst();
            currentStageOfConstruction = dataSet.getCurrentStageOfConstruction();
            brandUsed = dataSet.getBrandUsed();
            conversion = dataSet.getConversion();
            product = dataSet.getSelectProduct();
            requestDateOfDelivery = dataSet.getRequestedDate();
            counterType = dataSet.getCounterType();
            counterName = dataSet.getCounterName();
            reasonsForNonConversion = dataSet.getReasonForNonConversion();
            priority = dataSet.getSitePriority();
            weatherShieldDemo = dataSet.getWeatherShieldDemo();
            approvalStatus = dataSet.getApprovalStatus();
            asmName = dataSet.getAsmName();
            siteStatus = dataSet.getSiteStatus();
            transactionId = dataSet.getTransactionId();
            uniqueSiteId = dataSet.getUniqueId();
            siteCreationDate = dataSet.getCreatedAt().split(" ")[0];
            visitDate = dateString;
            employeeCode = dataSet.getEmpCode();
            employeeName = dataSet.getEmpName();
            zone = dataSet.getZone();
            latitude = dataSet.getLatitude();
            longitude = dataSet.getLongitude();
            customerName = dataSet.getCustomerName();
            customerContactNo = dataSet.getCustomerPhoneNo();
            fullAddress = dataSet.getAddress();
            pettyContractorId = dataSet.getContractorId();
            pettyContractorName = dataSet.getHeadMasonName();
            pettyContractorContactNo = dataSet.getHeadMasonContact();
            pettyEngineerId = dataSet.getEngineerId();
            pettyEngineerName = dataSet.getEngineerName();
            pettyEngineerContactNo = dataSet.getEngineerContact();
            builtUpArea = dataSet.getBuiltUpArea();
            sitePotential = dataSet.getSitePotential();
            consumedTillDate = dataSet.getConsumedTillDate();
            balancePotential = dataSet.getBalancePotential();
            balancePotentialManual = dataSet.getBalancePotentialManual();
            siteCategory = dataSet.getSiteCategory();
            pricePerBag = dataSet.getPricePerBag();
            orderQuantity = dataSet.getNoOfBagsOrdered();
            counterCode = dataSet.getCounterCode();
            dateAndTime = dataSet.getApprovalDateTime();
            asmEmployeeId = dataSet.getAsmId();
            deliveryRemarks = dataSet.getDeliveryRemarks();
            reasonForNotDelivery = dataSet.getReasonForNotDelivery();
            branchCategory = dataSet.getVisitType();
            remakrs = dataSet.getRemarks();
//            if(dataSet.getConversion().equalsIgnoreCase("converted")){
//                visitType="Star Site";
//            }

            Log.d("TAG", "_DOWNLOAD_ branchCategory: " + branchCategory);

            textExistingUniqueId.setText(uniqueSiteId);
            edTextExistingSiteTransactionId.setText(transactionId);
            edTextExistingSiteSiteCreationDate.setText(siteCreationDate);
            edTextExistingSiteVisitDate.setText(visitDate);
            edTextExistingSiteEmployeeCode.setText(employeeCode);
            edTextExistingSiteEmployeeName.setText(employeeName);
            edTextExistingSiteZone.setText(zone);
            textExistingSiteBranch.setText(branch);
            textExistingSiteState.setText(state);
            textExistingSiteDistrict.setText(district);
            edTextExistingSiteLatitude.setText(latitude);
            edTextExistingSiteLongitude.setText(longitude);
            edTextExistingSiteCustomerName.setText(customerName);
            edTextExistingSiteCustomerContactNo.setText(customerContactNo);
            edTextExistingSiteFullAddress.setText(fullAddress);
            textExistingSiteIsReqdContractorLink.setText(isReqdContractorLink);
            edTextExistingSitePettyContractorName.setText(pettyContractorName);
            edTextExistingSitePettyContractorContactNo.setText(pettyContractorContactNo);
            textExistingSiteIsReqdEngineerStellar.setText(isReqdEngineerStellar);
            edTextExistingSitePettyEngineerName.setText(pettyEngineerName);
            edTextExistingSitePettyEngineerContactNo.setText(pettyEngineerContactNo);
            textExistingSiteMeetingPerson.setText(meetingPerson);
            textExistingSiteDecisionMaker.setText(decisionMaker);
            textExistingSiteSiteSegment.setText(siteSegment);
            textExistingSiteVisitType.setText(visitType);
            textExistingSiteProjectSegment.setText(projectSegment);
            textExistingSiteTypeOfConstruction.setText(typeOfConstruction);
            textExistingSiteCurrentStageOfConstruction.setText(currentStageOfConstruction);
            edTextExistingSiteBuiltUpArea.setText(builtUpArea);
            edTextExistingSiteSitePotential.setText(sitePotential);
            edTextExistingSiteConsumedTillDate.setText(consumedTillDate);
            edTextExistingSiteBalancePotential.setText(balancePotential);
            edTextExistingSiteBalancePotentialManual.setText(balancePotentialManual);
            edTextExistingSiteSiteCategory.setText(siteCategory);
            textExistingSiteBrandUsed.setText(brandUsed);
            edTextExistingSitePricePerBag.setText(pricePerBag);
            textExistingSiteConversion.setText(conversion);
            textExistingSiteProduct.setText(product);
            edTextExistingSiteOrderQuantity.setText(orderQuantity);
            textExistingSiteRequestDateOfDelivery.setText(requestDateOfDelivery);
            textExistingSiteCounterType.setText(counterType);
            textExistingSiteCounterName.setText(counterName);
            edTextExistingSiteCounterCode.setText(counterCode);
            textExistingSiteReasonsForNonConversion.setText(reasonsForNonConversion);
            textExistingSiteSitePriority.setText(priority);
            textExistingSiteWeatherShieldDemo.setText(weatherShieldDemo);
            textExistingSiteApprovalStatus.setText(approvalStatus);
            edTextExistingSiteDateAndTime.setText(dateAndTime);
            textExistingSiteAsmName.setText(asmName);
            edTextExistingSiteAsmEmployeeId.setText(asmEmployeeId);
            edTextExistingSiteDeliveryRemarks.setText(deliveryRemarks);
            edTextExistingSiteReasonForNotDelivery.setText(reasonForNotDelivery);
            edTextExistingSiteSiteRemarks.setText(remakrs);
            textExistingSiteSiteStatus.setText(siteStatus);

//        noOfBag,createdAt,updatedAt,newSiteLeadId,newSiteLeadUniqueId,contractorId,engineerId,approvalDateTime

            textExistingUniqueId.setVisibility(VISIBLE);
            textExistingSiteBranch.setVisibility(VISIBLE);
            textExistingSiteState.setVisibility(VISIBLE);
            textExistingSiteDistrict.setVisibility(VISIBLE);
            textExistingSiteIsReqdContractorLink.setVisibility(VISIBLE);
            textExistingSiteIsReqdEngineerStellar.setVisibility(VISIBLE);
            textExistingSiteMeetingPerson.setVisibility(VISIBLE);
            textExistingSiteDecisionMaker.setVisibility(VISIBLE);
            textExistingSiteSiteSegment.setVisibility(VISIBLE);
            textExistingSiteVisitType.setVisibility(VISIBLE);
            textExistingSiteProjectSegment.setVisibility(VISIBLE);
            textExistingSiteTypeOfConstruction.setVisibility(VISIBLE);
            textExistingSiteCurrentStageOfConstruction.setVisibility(VISIBLE);
            textExistingSiteBrandUsed.setVisibility(VISIBLE);
            textExistingSiteConversion.setVisibility(VISIBLE);
            textExistingSiteProduct.setVisibility(VISIBLE);
            textExistingSiteRequestDateOfDelivery.setVisibility(VISIBLE);
            textExistingSiteCounterType.setVisibility(VISIBLE);
            textExistingSiteCounterName.setVisibility(VISIBLE);
            textExistingSiteReasonsForNonConversion.setVisibility(VISIBLE);
            textExistingSiteSitePriority.setVisibility(VISIBLE);
            textExistingSiteWeatherShieldDemo.setVisibility(VISIBLE);
            textExistingSiteApprovalStatus.setVisibility(VISIBLE);
            textExistingSiteAsmName.setVisibility(VISIBLE);
            textExistingSiteSiteStatus.setVisibility(VISIBLE);
            layoutExistingSiteApprovalStatus.setVisibility(VISIBLE);

            layoutExistingSitePettyContractorName.setVisibility(VISIBLE);
            layoutExistingSitePettyContractorContactNo.setVisibility(VISIBLE);
            layoutExistingSitePettyEngineerName.setVisibility(VISIBLE);
            layoutExistingSitePettyEngineerContactNo.setVisibility(VISIBLE);
            layoutExistingSiteProduct.setVisibility(VISIBLE);
            layoutExistingSiteOrderQuantity.setVisibility(VISIBLE);
            layoutExistingSiteCounterType.setVisibility(VISIBLE);
            layoutExistingSiteCounterName.setVisibility(VISIBLE);
            layoutExistingSiteCounterCode.setVisibility(VISIBLE);
            layoutExistingSiteReasonsForNonConversion.setVisibility(VISIBLE);
            layoutExistingSiteDateAndTime.setVisibility(VISIBLE);
            layoutExistingSiteDeliveryRemarks.setVisibility(VISIBLE);
            layoutExistingSiteReasonForNotDelivery.setVisibility(VISIBLE);

            for (int i = 0; i < projectSegmentList.size(); i++) {
                if (projectSegmentList.get(i).getValue().equalsIgnoreCase(projectSegment)) {
                    if (projectSegmentList.get(i).getTitle().equalsIgnoreCase("1")) {
                        layoutExistingSiteTypeOfConstruction.setVisibility(VISIBLE);
                    } else {
                        layoutExistingSiteTypeOfConstruction.setVisibility(GONE);
                    }
                }
            }

            if (dataSet.getPettyContractorRegistered().equalsIgnoreCase("Not Required")) {
                layoutExistingSitePettyContractorName.setVisibility(GONE);
                layoutExistingSitePettyContractorContactNo.setVisibility(GONE);
            } else if (dataSet.getPettyContractorRegistered().equalsIgnoreCase("yes")) {
                edTextExistingSitePettyContractorName.setEnabled(false);
                edTextExistingSitePettyContractorContactNo.setEnabled(false);
            }
            if (dataSet.getEngineerRegistered().equalsIgnoreCase("Not Available")) {
                layoutExistingSitePettyEngineerName.setVisibility(GONE);
                layoutExistingSitePettyEngineerContactNo.setVisibility(GONE);
            } else if (dataSet.getEngineerRegistered().equalsIgnoreCase("yes")) {
                edTextExistingSitePettyEngineerName.setEnabled(false);
                edTextExistingSitePettyEngineerContactNo.setEnabled(false);
            }
            if (dataSet.getConversion().equalsIgnoreCase("non converted")||dataSet.getConversion().equalsIgnoreCase("converted to non star site")) {
                layoutExistingSiteProduct.setVisibility(GONE);
                layoutExistingSiteOrderQuantity.setVisibility(GONE);
                layoutExistingSiteCounterType.setVisibility(GONE);
                layoutExistingSiteCounterName.setVisibility(GONE);
                layoutExistingSiteCounterCode.setVisibility(GONE);
            } else {
                layoutExistingSiteReasonsForNonConversion.setVisibility(GONE);
            }
            if (dataSet.getApprovalStatus().equalsIgnoreCase("rejected")) {
                layoutExistingSiteDateAndTime.setVisibility(GONE);
                layoutExistingSiteDeliveryRemarks.setVisibility(GONE);
            } else if (dataSet.getApprovalStatus().toLowerCase().startsWith("approved")) {
                layoutExistingSiteReasonForNotDelivery.setVisibility(GONE);
            } else {
                layoutExistingSiteDateAndTime.setVisibility(GONE);
                layoutExistingSiteDeliveryRemarks.setVisibility(GONE);
                layoutExistingSiteReasonForNotDelivery.setVisibility(GONE);
            }

            if (dataSet.getApprovalStatus().equalsIgnoreCase("rejected") ||
                    dataSet.getApprovalStatus().equalsIgnoreCase("pending") ||
                    dataSet.getSiteStatus().equalsIgnoreCase("close")) {
                submitButton.setVisibility(GONE);
            } else {
                submitButton.setVisibility(VISIBLE);
            }

            if (dataSet.getApprovalStatus().toLowerCase().startsWith("approved")) {
                makeExistingLayoutEditable();
            } else {
                makeExistingLayoutNonEditable();
            }
        });
    }

    private void showSwitchSiteLeadInfo(SiteLeadDataSet dataSet){
        runOnUiThread(() -> {
            branchCode = dataSet.getBranch();
            for (int i = 0; i < branchList.size(); i++) {
                if (branchList.get(i).getTitle().equalsIgnoreCase(branchCode)) {
                    branch = branchList.get(i).getValue();
                    break;
                }
            }
            state = dataSet.getState();
            district = dataSet.getDistrict();
            isReqdContractorLink = dataSet.getPettyContractorRegistered();
            isReqdEngineerStellar = dataSet.getEngineerRegistered();
            meetingPerson = dataSet.getMeetingPerson();
            decisionMaker = dataSet.getDecisionMaker();
            siteSegment = dataSet.getSiteSegment();
            visitType = dataSet.getVisitType();
            projectSegment = dataSet.getProjectSegment();
            typeOfConstruction = dataSet.getTypeOfConst();
            currentStageOfConstruction = dataSet.getCurrentStageOfConstruction();
            brandUsed = dataSet.getBrandUsed();
            conversion = dataSet.getConversion();
            product = dataSet.getSelectProduct();
            requestDateOfDelivery = dataSet.getRequestedDate();
            counterType = dataSet.getCounterType();
            counterName = dataSet.getCounterName();
            reasonsForNonConversion = dataSet.getReasonForNonConversion();
            priority = dataSet.getSitePriority();
            weatherShieldDemo = dataSet.getWeatherShieldDemo();
            approvalStatus = dataSet.getApprovalStatus();
            asmName = dataSet.getAsmName();
            siteStatus = dataSet.getSiteStatus();
            transactionId = dataSet.getTransactionId();
            uniqueSiteId = dataSet.getUniqueId();
            siteCreationDate = dataSet.getCreatedAt().split(" ")[0];
            visitDate = dateString;
            employeeCode = dataSet.getEmpCode();
            employeeName = dataSet.getEmpName();
            zone = dataSet.getZone();
            latitude = dataSet.getLatitude();
            longitude = dataSet.getLongitude();
            customerName = dataSet.getCustomerName();
            customerContactNo = dataSet.getCustomerPhoneNo();
            fullAddress = dataSet.getAddress();
            pettyContractorId = dataSet.getContractorId();
            pettyContractorName = dataSet.getHeadMasonName();
            pettyContractorContactNo = dataSet.getHeadMasonContact();
            pettyEngineerId = dataSet.getEngineerId();
            pettyEngineerName = dataSet.getEngineerName();
            pettyEngineerContactNo = dataSet.getEngineerContact();
            builtUpArea = dataSet.getBuiltUpArea();
            sitePotential = dataSet.getSitePotential();
            consumedTillDate = dataSet.getConsumedTillDate();
            balancePotential = dataSet.getBalancePotential();
            balancePotentialManual = dataSet.getBalancePotentialManual();
            siteCategory = dataSet.getSiteCategory();
            pricePerBag = dataSet.getPricePerBag();
            orderQuantity = dataSet.getNoOfBagsOrdered();
            counterCode = dataSet.getCounterCode();
            dateAndTime = dataSet.getApprovalDateTime();
            asmEmployeeId = dataSet.getAsmId();
            deliveryRemarks = dataSet.getDeliveryRemarks();
            reasonForNotDelivery = dataSet.getReasonForNotDelivery();
            branchCategory = dataSet.getVisitType();
            remakrs = dataSet.getRemarks();
            Log.d("TAG", "_DOWNLOAD_ branchCategory: " + branchCategory);

            textExistingUniqueId.setText(uniqueSiteId);
            edTextExistingSiteTransactionId.setText(transactionId);
            edTextExistingSiteSiteCreationDate.setText(siteCreationDate);
            edTextExistingSiteVisitDate.setText(visitDate);
            edTextExistingSiteEmployeeCode.setText(employeeCode);
            edTextExistingSiteEmployeeName.setText(employeeName);
            edTextExistingSiteZone.setText(zone);
            textExistingSiteBranch.setText(branch);
            textExistingSiteState.setText(state);
            textExistingSiteDistrict.setText(district);
            edTextExistingSiteLatitude.setText(latitude);
            edTextExistingSiteLongitude.setText(longitude);
            edTextExistingSiteCustomerName.setText(customerName);
            edTextExistingSiteCustomerContactNo.setText(customerContactNo);
            edTextExistingSiteFullAddress.setText(fullAddress);
            textExistingSiteIsReqdContractorLink.setText(isReqdContractorLink);
            edTextExistingSitePettyContractorName.setText(pettyContractorName);
            edTextExistingSitePettyContractorContactNo.setText(pettyContractorContactNo);
            textExistingSiteIsReqdEngineerStellar.setText(isReqdEngineerStellar);
            edTextExistingSitePettyEngineerName.setText(pettyEngineerName);
            edTextExistingSitePettyEngineerContactNo.setText(pettyEngineerContactNo);
            textExistingSiteMeetingPerson.setText(meetingPerson);
            textExistingSiteDecisionMaker.setText(decisionMaker);
            textExistingSiteSiteSegment.setText(siteSegment);
            textExistingSiteVisitType.setText(visitType);
            textExistingSiteProjectSegment.setText(projectSegment);
            textExistingSiteTypeOfConstruction.setText(typeOfConstruction);
            textExistingSiteCurrentStageOfConstruction.setText(currentStageOfConstruction);
            edTextExistingSiteBuiltUpArea.setText(builtUpArea);
            edTextExistingSiteSitePotential.setText(sitePotential);
            edTextExistingSiteConsumedTillDate.setText(consumedTillDate);
            edTextExistingSiteBalancePotential.setText(balancePotential);
            edTextExistingSiteBalancePotentialManual.setText(balancePotentialManual);
            edTextExistingSiteSiteCategory.setText(siteCategory);
            textExistingSiteBrandUsed.setText(brandUsed);
            edTextExistingSitePricePerBag.setText(pricePerBag);
            textExistingSiteConversion.setText(conversion);
            textExistingSiteProduct.setText(product);
            edTextExistingSiteOrderQuantity.setText(orderQuantity);
            textExistingSiteRequestDateOfDelivery.setText(requestDateOfDelivery);
            textExistingSiteCounterType.setText(counterType);
            textExistingSiteCounterName.setText(counterName);
            edTextExistingSiteCounterCode.setText(counterCode);
            textExistingSiteReasonsForNonConversion.setText(reasonsForNonConversion);
            textExistingSiteSitePriority.setText(priority);
            textExistingSiteWeatherShieldDemo.setText(weatherShieldDemo);
            textExistingSiteApprovalStatus.setText(approvalStatus);
            edTextExistingSiteDateAndTime.setText(dateAndTime);
            textExistingSiteAsmName.setText(asmName);
            edTextExistingSiteAsmEmployeeId.setText(asmEmployeeId);
            edTextExistingSiteDeliveryRemarks.setText(deliveryRemarks);
            edTextExistingSiteReasonForNotDelivery.setText(reasonForNotDelivery);
            edTextExistingSiteSiteRemarks.setText(remakrs);
            textExistingSiteSiteStatus.setText(siteStatus);

//        noOfBag,createdAt,updatedAt,newSiteLeadId,newSiteLeadUniqueId,contractorId,engineerId,approvalDateTime

            textExistingUniqueId.setVisibility(VISIBLE);
            textExistingSiteBranch.setVisibility(VISIBLE);
            textExistingSiteState.setVisibility(VISIBLE);
            textExistingSiteDistrict.setVisibility(VISIBLE);
            textExistingSiteIsReqdContractorLink.setVisibility(VISIBLE);
            textExistingSiteIsReqdEngineerStellar.setVisibility(VISIBLE);
            textExistingSiteMeetingPerson.setVisibility(VISIBLE);
            textExistingSiteDecisionMaker.setVisibility(VISIBLE);
            textExistingSiteSiteSegment.setVisibility(VISIBLE);
            textExistingSiteVisitType.setVisibility(VISIBLE);
            textExistingSiteProjectSegment.setVisibility(VISIBLE);
            textExistingSiteTypeOfConstruction.setVisibility(VISIBLE);
            textExistingSiteCurrentStageOfConstruction.setVisibility(VISIBLE);
            textExistingSiteBrandUsed.setVisibility(VISIBLE);
            textExistingSiteConversion.setVisibility(VISIBLE);
            textExistingSiteProduct.setVisibility(VISIBLE);
            textExistingSiteRequestDateOfDelivery.setVisibility(VISIBLE);
            textExistingSiteCounterType.setVisibility(VISIBLE);
            textExistingSiteCounterName.setVisibility(VISIBLE);
            textExistingSiteReasonsForNonConversion.setVisibility(VISIBLE);
            textExistingSiteSitePriority.setVisibility(VISIBLE);
            textExistingSiteWeatherShieldDemo.setVisibility(VISIBLE);
            textExistingSiteApprovalStatus.setVisibility(VISIBLE);
            textExistingSiteAsmName.setVisibility(VISIBLE);
            textExistingSiteSiteStatus.setVisibility(VISIBLE);
            layoutExistingSiteApprovalStatus.setVisibility(VISIBLE);

            layoutExistingSitePettyContractorName.setVisibility(VISIBLE);
            layoutExistingSitePettyContractorContactNo.setVisibility(VISIBLE);
            layoutExistingSitePettyEngineerName.setVisibility(VISIBLE);
            layoutExistingSitePettyEngineerContactNo.setVisibility(VISIBLE);
            layoutExistingSiteProduct.setVisibility(VISIBLE);
            layoutExistingSiteOrderQuantity.setVisibility(VISIBLE);
            layoutExistingSiteCounterType.setVisibility(VISIBLE);
            layoutExistingSiteCounterName.setVisibility(VISIBLE);
            layoutExistingSiteCounterCode.setVisibility(VISIBLE);
            layoutExistingSiteReasonsForNonConversion.setVisibility(VISIBLE);
            layoutExistingSiteDateAndTime.setVisibility(VISIBLE);
            layoutExistingSiteDeliveryRemarks.setVisibility(VISIBLE);
            layoutExistingSiteReasonForNotDelivery.setVisibility(VISIBLE);

            for (int i = 0; i < projectSegmentList.size(); i++) {
                if (projectSegmentList.get(i).getValue().equalsIgnoreCase(projectSegment)) {
                    if (projectSegmentList.get(i).getTitle().equalsIgnoreCase("1")) {
                        layoutExistingSiteTypeOfConstruction.setVisibility(VISIBLE);
                    } else {
                        layoutExistingSiteTypeOfConstruction.setVisibility(GONE);
                    }
                }
            }

            if (dataSet.getPettyContractorRegistered().equalsIgnoreCase("Not Required")) {
                layoutExistingSitePettyContractorName.setVisibility(GONE);
                layoutExistingSitePettyContractorContactNo.setVisibility(GONE);
            } else if (dataSet.getPettyContractorRegistered().equalsIgnoreCase("yes")) {
                edTextExistingSitePettyContractorName.setEnabled(false);
                edTextExistingSitePettyContractorContactNo.setEnabled(false);
            }
            if (dataSet.getEngineerRegistered().equalsIgnoreCase("Not Available")) {
                layoutExistingSitePettyEngineerName.setVisibility(GONE);
                layoutExistingSitePettyEngineerContactNo.setVisibility(GONE);
            } else if (dataSet.getEngineerRegistered().equalsIgnoreCase("yes")) {
                edTextExistingSitePettyEngineerName.setEnabled(false);
                edTextExistingSitePettyEngineerContactNo.setEnabled(false);
            }
            if (dataSet.getConversion().equalsIgnoreCase("non converted")||dataSet.getConversion().equalsIgnoreCase("converted to non star site")) {
                layoutExistingSiteProduct.setVisibility(GONE);
                layoutExistingSiteOrderQuantity.setVisibility(GONE);
                layoutExistingSiteCounterType.setVisibility(GONE);
                layoutExistingSiteCounterName.setVisibility(GONE);
                layoutExistingSiteCounterCode.setVisibility(GONE);
            } else {
                layoutExistingSiteReasonsForNonConversion.setVisibility(GONE);
            }
            if (dataSet.getApprovalStatus().equalsIgnoreCase("rejected")) {
                layoutExistingSiteDateAndTime.setVisibility(GONE);
                layoutExistingSiteDeliveryRemarks.setVisibility(GONE);
            } else if (dataSet.getApprovalStatus().toLowerCase().startsWith("approved")) {
                layoutExistingSiteReasonForNotDelivery.setVisibility(GONE);
            } else {
                layoutExistingSiteDateAndTime.setVisibility(GONE);
                layoutExistingSiteDeliveryRemarks.setVisibility(GONE);
                layoutExistingSiteReasonForNotDelivery.setVisibility(GONE);
            }

            if (dataSet.getApprovalStatus().equalsIgnoreCase("rejected") ||
                    dataSet.getApprovalStatus().equalsIgnoreCase("pending") ||
                    dataSet.getSiteStatus().equalsIgnoreCase("close")) {
                submitButton.setVisibility(GONE);
            } else {
                submitButton.setVisibility(VISIBLE);
            }

            if (dataSet.getApprovalStatus().toLowerCase().startsWith("approved")) {
                makeSwitchLayoutEditable();
            } else {
                makeSwitchLayoutNonEditable();
            }
        });
    }

    private void makeExistingLayoutEditable() {
        runOnUiThread(() -> {
            buttonExistingUniqueId.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteIsReqdContractorLink.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteIsReqdEngineerStellar.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteMeetingPerson.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteDecisionMaker.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteCurrentStageOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteBrandUsed.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteProduct.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteRequestDateOfDelivery.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteCounterType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteCounterName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteReasonsForNonConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteWeatherShieldDemo.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteAsmName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteSiteStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));

            buttonExistingSiteApprovalStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteBranch.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteState.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteDistrict.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSiteSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteVisitType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteProjectSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteTypeOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSitePriority.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));

            edTextExistingSiteTransactionId.setEnabled(false);
            edTextExistingSiteSiteCreationDate.setEnabled(false);
            edTextExistingSiteVisitDate.setEnabled(false);
            edTextExistingSiteEmployeeCode.setEnabled(false);
            edTextExistingSiteEmployeeName.setEnabled(false);
            edTextExistingSiteZone.setEnabled(false);
            edTextExistingSiteLatitude.setEnabled(false);
            edTextExistingSiteLongitude.setEnabled(false);
            edTextExistingSiteCustomerName.setEnabled(false);
            edTextExistingSiteCustomerContactNo.setEnabled(false);
            edTextExistingSiteFullAddress.setEnabled(false);
            edTextExistingSiteBuiltUpArea.setEnabled(false);
            edTextExistingSiteSitePotential.setEnabled(false);
            edTextExistingSiteBalancePotential.setEnabled(false);
            edTextExistingSiteSiteCategory.setEnabled(false);
            edTextExistingSiteCounterCode.setEnabled(false);
            edTextExistingSiteDateAndTime.setEnabled(false);
            edTextExistingSiteAsmEmployeeId.setEnabled(false);
            edTextExistingSiteDeliveryRemarks.setEnabled(false);
            edTextExistingSiteReasonForNotDelivery.setEnabled(false);
            edTextExistingSiteSiteRemarks.setEnabled(true);


            edTextExistingSiteTransactionId.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSiteCreationDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteVisitDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteEmployeeCode.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteEmployeeName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteZone.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteLatitude.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteLongitude.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCustomerName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCustomerContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteFullAddress.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteBuiltUpArea.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSitePotential.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteBalancePotential.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSiteCategory.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCounterCode.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteDateAndTime.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteAsmEmployeeId.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteDeliveryRemarks.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteReasonForNotDelivery.setTextColor(Color.argb(255, 100, 100, 100));
        });
    }

    private void makeExistingLayoutNonEditable() {
        runOnUiThread(() -> {
            buttonExistingUniqueId.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteIsReqdContractorLink.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteIsReqdEngineerStellar.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteMeetingPerson.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteDecisionMaker.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCurrentStageOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteBrandUsed.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteProduct.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteRequestDateOfDelivery.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCounterType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCounterName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteReasonsForNonConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteWeatherShieldDemo.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteApprovalStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteAsmName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSiteStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteBranch.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteState.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteDistrict.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSiteSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteVisitType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteProjectSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteTypeOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSitePriority.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));


            edTextExistingSiteTransactionId.setEnabled(false);
            edTextExistingSiteSiteCreationDate.setEnabled(false);
            edTextExistingSiteVisitDate.setEnabled(false);
            edTextExistingSiteEmployeeCode.setEnabled(false);
            edTextExistingSiteEmployeeName.setEnabled(false);
            edTextExistingSiteZone.setEnabled(false);
            edTextExistingSiteLatitude.setEnabled(false);
            edTextExistingSiteLongitude.setEnabled(false);
            edTextExistingSiteCustomerName.setEnabled(false);
            edTextExistingSiteCustomerContactNo.setEnabled(false);
            edTextExistingSiteFullAddress.setEnabled(false);
            edTextExistingSitePettyContractorName.setEnabled(false);
            edTextExistingSitePettyContractorContactNo.setEnabled(false);
            edTextExistingSitePettyEngineerName.setEnabled(false);
            edTextExistingSitePettyEngineerContactNo.setEnabled(false);
            edTextExistingSiteBuiltUpArea.setEnabled(false);
            edTextExistingSiteSitePotential.setEnabled(false);
            edTextExistingSiteBalancePotential.setEnabled(false);
            edTextExistingSiteSiteCategory.setEnabled(false);
            edTextExistingSitePricePerBag.setEnabled(false);
            edTextExistingSiteOrderQuantity.setEnabled(false);
            edTextExistingSiteCounterCode.setEnabled(false);
            edTextExistingSiteDateAndTime.setEnabled(false);
            edTextExistingSiteAsmEmployeeId.setEnabled(false);
            edTextExistingSiteDeliveryRemarks.setEnabled(false);
            edTextExistingSiteReasonForNotDelivery.setEnabled(false);
            edTextExistingSiteSiteRemarks.setEnabled(true);


            edTextExistingSiteTransactionId.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSiteCreationDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteVisitDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteEmployeeCode.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteEmployeeName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteZone.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteLatitude.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteLongitude.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCustomerName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCustomerContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteFullAddress.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyContractorName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyContractorContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyEngineerName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyEngineerContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteBuiltUpArea.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSitePotential.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteConsumedTillDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteBalancePotential.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSiteCategory.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePricePerBag.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteOrderQuantity.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCounterCode.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteDateAndTime.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteAsmEmployeeId.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteDeliveryRemarks.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteReasonForNotDelivery.setTextColor(Color.argb(255, 100, 100, 100));
        });
    }
    private void makeSwitchLayoutEditable() {
        runOnUiThread(() -> {
            buttonExistingUniqueId.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteIsReqdContractorLink.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteIsReqdEngineerStellar.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteMeetingPerson.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteDecisionMaker.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCurrentStageOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteBrandUsed.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteProduct.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteRequestDateOfDelivery.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCounterType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCounterName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteReasonsForNonConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteWeatherShieldDemo.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteApprovalStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteAsmName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSiteStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteBranch.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteState.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteDistrict.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSiteSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteVisitType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteProjectSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteTypeOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSitePriority.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));


            edTextExistingSiteTransactionId.setEnabled(false);
            edTextExistingSiteSiteCreationDate.setEnabled(false);
            edTextExistingSiteVisitDate.setEnabled(false);
            edTextExistingSiteEmployeeCode.setEnabled(false);
            edTextExistingSiteEmployeeName.setEnabled(false);
            edTextExistingSiteZone.setEnabled(false);
            edTextExistingSiteLatitude.setEnabled(false);
            edTextExistingSiteLongitude.setEnabled(false);
            edTextExistingSiteCustomerName.setEnabled(false);
            edTextExistingSiteCustomerContactNo.setEnabled(false);
            edTextExistingSiteFullAddress.setEnabled(false);
            edTextExistingSitePettyContractorName.setEnabled(false);
            edTextExistingSitePettyContractorContactNo.setEnabled(false);
            edTextExistingSitePettyEngineerName.setEnabled(false);
            edTextExistingSitePettyEngineerContactNo.setEnabled(false);
            edTextExistingSiteBuiltUpArea.setEnabled(false);
            edTextExistingSiteSitePotential.setEnabled(false);
            edTextExistingSiteBalancePotential.setEnabled(false);
            edTextExistingSiteSiteCategory.setEnabled(false);
            edTextExistingSitePricePerBag.setEnabled(false);
            edTextExistingSiteOrderQuantity.setEnabled(false);
            edTextExistingSiteCounterCode.setEnabled(false);
            edTextExistingSiteDateAndTime.setEnabled(false);
            edTextExistingSiteAsmEmployeeId.setEnabled(false);
            edTextExistingSiteDeliveryRemarks.setEnabled(false);
            edTextExistingSiteReasonForNotDelivery.setEnabled(false);
            edTextExistingSiteSiteRemarks.setEnabled(true);


            edTextExistingSiteTransactionId.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSiteCreationDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteVisitDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteEmployeeCode.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteEmployeeName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteZone.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteLatitude.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteLongitude.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCustomerName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCustomerContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteFullAddress.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyContractorName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyContractorContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyEngineerName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyEngineerContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteBuiltUpArea.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSitePotential.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteConsumedTillDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteBalancePotential.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSiteCategory.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePricePerBag.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteOrderQuantity.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCounterCode.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteDateAndTime.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteAsmEmployeeId.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteDeliveryRemarks.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteReasonForNotDelivery.setTextColor(Color.argb(255, 100, 100, 100));
        });
    }
    private void makeSwitchLayoutNonEditable() {
        runOnUiThread(() -> {
            buttonExistingUniqueId.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            buttonExistingSiteIsReqdContractorLink.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteIsReqdEngineerStellar.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteMeetingPerson.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteDecisionMaker.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCurrentStageOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteBrandUsed.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteProduct.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteRequestDateOfDelivery.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCounterType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteCounterName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteReasonsForNonConversion.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteWeatherShieldDemo.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteApprovalStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteAsmName.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSiteStatus.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteBranch.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteState.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteDistrict.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSiteSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteVisitType.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteProjectSegment.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteTypeOfConstruction.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            buttonExistingSiteSitePriority.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));


            edTextExistingSiteTransactionId.setEnabled(false);
            edTextExistingSiteSiteCreationDate.setEnabled(false);
            edTextExistingSiteVisitDate.setEnabled(false);
            edTextExistingSiteEmployeeCode.setEnabled(false);
            edTextExistingSiteEmployeeName.setEnabled(false);
            edTextExistingSiteZone.setEnabled(false);
            edTextExistingSiteLatitude.setEnabled(false);
            edTextExistingSiteLongitude.setEnabled(false);
            edTextExistingSiteCustomerName.setEnabled(false);
            edTextExistingSiteCustomerContactNo.setEnabled(false);
            edTextExistingSiteFullAddress.setEnabled(false);
            edTextExistingSitePettyContractorName.setEnabled(false);
            edTextExistingSitePettyContractorContactNo.setEnabled(false);
            edTextExistingSitePettyEngineerName.setEnabled(false);
            edTextExistingSitePettyEngineerContactNo.setEnabled(false);
            edTextExistingSiteBuiltUpArea.setEnabled(false);
            edTextExistingSiteSitePotential.setEnabled(false);
            edTextExistingSiteBalancePotential.setEnabled(false);
            edTextExistingSiteSiteCategory.setEnabled(false);
            edTextExistingSitePricePerBag.setEnabled(false);
            edTextExistingSiteOrderQuantity.setEnabled(false);
            edTextExistingSiteCounterCode.setEnabled(false);
            edTextExistingSiteDateAndTime.setEnabled(false);
            edTextExistingSiteAsmEmployeeId.setEnabled(false);
            edTextExistingSiteDeliveryRemarks.setEnabled(false);
            edTextExistingSiteReasonForNotDelivery.setEnabled(false);
            edTextExistingSiteSiteRemarks.setEnabled(true);


            edTextExistingSiteTransactionId.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSiteCreationDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteVisitDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteEmployeeCode.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteEmployeeName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteZone.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteLatitude.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteLongitude.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCustomerName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCustomerContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteFullAddress.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyContractorName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyContractorContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyEngineerName.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePettyEngineerContactNo.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteBuiltUpArea.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSitePotential.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteConsumedTillDate.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteBalancePotential.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteSiteCategory.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSitePricePerBag.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteOrderQuantity.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteCounterCode.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteDateAndTime.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteAsmEmployeeId.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteDeliveryRemarks.setTextColor(Color.argb(255, 100, 100, 100));
            edTextExistingSiteReasonForNotDelivery.setTextColor(Color.argb(255, 100, 100, 100));
        });
    }

    // ***Show ASM Site Lead Data Set***
    private void showAsmExistingSiteLeadInfo(SiteLeadDataSet dataSet) {
        runOnUiThread(() -> {
            String branchName = "";
            for (int i = 0; i < branchList.size(); i++) {
                if (branchList.get(i).getTitle().equalsIgnoreCase(dataSet.getBranch())) {
                    branchName = branchList.get(i).getValue();
                    break;
                }
            }

            asmTextSiteTransactionId.setText(dataSet.getTransactionId());
            asmTextSiteUniqueId.setText(dataSet.getUniqueId());
            asmTextSiteCreationDate.setText(dataSet.getCreatedAt().split(" ")[0]);
            asmTextSiteVisitDate.setText(dataSet.getVisitDate().split(" ")[0]);
            asmTextSiteEmployeeCode.setText(dataSet.getEmpCode());
            asmTextSiteEmployeeName.setText(dataSet.getEmpName());
            asmTextSiteZone.setText(dataSet.getZone());
            asmTextSiteState.setText(dataSet.getState());
            asmTextSiteBranch.setText(branchName);
            asmTextSiteDistrict.setText(dataSet.getDistrict());
            asmTextSiteLatitude.setText(dataSet.getLatitude());
            asmTextSiteLongitude.setText(dataSet.getLongitude());
            asmTextSiteCustomerName.setText(dataSet.getCustomerName());
            asmTextSiteCustomerContactNumber.setText(dataSet.getCustomerPhoneNo());
            asmTextSiteFullAddress.setText(dataSet.getAddress());
            asmTextSiteIsRegdInStarLink.setText(dataSet.getPettyContractorRegistered());
            asmTextSiteContractorName.setText(dataSet.getHeadMasonName());
            asmTextSiteContractorContactNo.setText(dataSet.getHeadMasonContact());
            asmTextSiteIsRegdInStarStellar.setText(dataSet.getEngineerRegistered());
            asmTextSiteEngineerName.setText(dataSet.getEngineerName());
            asmTextSiteEngineerContactNo.setText(dataSet.getEngineerContact());
            asmTextSiteMeetingPerson.setText(dataSet.getMeetingPerson());
            asmTextSiteDecisionMaker.setText(dataSet.getDecisionMaker());
            asmTextSiteSiteSegment.setText(dataSet.getSiteSegment());
            asmTextSiteVisitType.setText(dataSet.getVisitType());
            asmTextSiteProjectSegment.setText(dataSet.getProjectSegment());
            asmTextSiteTypeOfConstruction.setText(dataSet.getTypeOfConst());
            asmTextSiteCurrentStageOfConstruction.setText(dataSet.getCurrentStageOfConstruction());
            asmTextSiteBuiltUpArea.setText(dataSet.getBuiltUpArea());
            asmTextSiteSitePotential.setText(dataSet.getSitePotential());
            asmTextSiteConsumedTillDate.setText(dataSet.getConsumedTillDate());
            asmTextSiteBalancePotential.setText(dataSet.getBalancePotential());
            asmTextSiteBalancePotentialManual.setText(dataSet.getBalancePotentialManual());
            asmTextSiteRemarks.setText(dataSet.getRemarks());
            asmTextSiteSiteCategory.setText(dataSet.getSiteCategory());
            asmTextSiteBrandUsed.setText(dataSet.getBrandUsed());
            asmTextSitePricePerBag.setText(dataSet.getPricePerBag());
            asmTextSiteConversion.setText(dataSet.getConversion());
            asmTextSiteSelectProduct.setText(dataSet.getSelectProduct());
            asmTextSiteOrderQty.setText(dataSet.getNoOfBagsOrdered());
            asmTextSiteRequestedDateOfDelivery.setText(dataSet.getRequestedDate());
            asmTextSiteCounterType.setText(dataSet.getCounterType());
            asmTextSiteCounterName.setText(dataSet.getCounterName());
            asmTextSiteCounterCode.setText(dataSet.getCounterCode());
            asmTextSiteDistrictReasonForNonConversion.setText(dataSet.getReasonForNonConversion());
            asmTextSiteSitePriority.setText(dataSet.getSitePriority());
            asmTextSiteWeatherShieldDemo.setText(dataSet.getWeatherShieldDemo());
            asmTextSiteSiteStatus.setText(dataSet.getSiteStatus());

            if (dataSet.getApprovalStatus().toLowerCase().startsWith("approved") || dataSet.getApprovalStatus().equalsIgnoreCase("rejected")) {
                submitButton.setVisibility(GONE);
            } else {
                submitButton.setVisibility(VISIBLE);
            }
        });
    }

    // ***DateTimePicker Popup***
    private void dateTimePicker(String value, String type) {
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
                        case "request_date_of_delivery":
                            if (type.equalsIgnoreCase("new")) {
                                textNewSiteRequestDateOfDelivery.setVisibility(VISIBLE);
                                textNewSiteRequestDateOfDelivery.setText(date);
                            } else {
                                textExistingSiteRequestDateOfDelivery.setVisibility(VISIBLE);
                                textExistingSiteRequestDateOfDelivery.setText(date);
                            }
                            requestDateOfDelivery = date;
                            break;
                        case "actual_date_of_delivery":
                            if (type.equalsIgnoreCase("popup")) {
                                textActualDateOfDeliveryASM.setVisibility(VISIBLE);
                                textActualDateOfDeliveryASM.setText(date);
                            }
                            actualDateOfDelivery = date;
                            break;
                    }
                },
                year, month, day
        );
        if (!value.equalsIgnoreCase("request_date_of_delivery")) {
            datePickerDialog.getDatePicker().setMinDate(calendar.getTimeInMillis());
        }
        datePickerDialog.show();
    }

    // ***Check Site details***
    private void checkNewSiteLeadDetails() {
        if (textNewSiteBranch.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Branch.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteState.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select State.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteDistrict.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select District.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSiteCustomerName.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Customer Name.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSiteCustomerContactNo.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Customer Contact Number.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSiteCustomerContactNo.getText().toString().trim().length() != 10) {
            Toast.makeText(this, "Please enter Correct Customer Contact Number.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSiteFullAddress.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Customer Full Address.", Toast.LENGTH_LONG).show();
            return;
        }

        if (textNewSiteIsReqdContractorLink.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select is register contractor in StarLink.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSitePettyContractorName.getText().toString().trim().isEmpty() && textNewSiteIsReqdContractorLink.getText().toString().trim().equalsIgnoreCase("yes")) {
            Toast.makeText(this, "Please enter Contractor Name.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSitePettyContractorContactNo.getText().toString().trim().isEmpty() && textNewSiteIsReqdContractorLink.getText().toString().trim().equalsIgnoreCase("yes")) {
            Toast.makeText(this, "Please enter Contractor Contact Number.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSitePettyContractorContactNo.getText().toString().trim().length() != 10 && !edTextNewSitePettyContractorContactNo.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Correct Contractor Contact Number.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteIsReqdContractorLink.getText().toString().trim().equalsIgnoreCase("no") && (!edTextNewSitePettyContractorName.getText().toString().trim().isEmpty() || !edTextNewSitePettyContractorContactNo.getText().toString().trim().isEmpty())) {
            if (edTextNewSitePettyContractorName.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Contractor Name.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextNewSitePettyContractorContactNo.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Contractor Contact Number.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextNewSitePettyContractorContactNo.getText().toString().trim().length() != 10) {
                Toast.makeText(this, "Please enter Correct Contractor Contact Number.", Toast.LENGTH_LONG).show();
                return;
            }
        }

        if (textNewSiteIsReqdEngineerStellar.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select is register engineer in StarStellar.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSitePettyEngineerName.getText().toString().trim().isEmpty() && textNewSiteIsReqdEngineerStellar.getText().toString().trim().equalsIgnoreCase("yes")) {
            Toast.makeText(this, "Please enter Engineer Name.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSitePettyEngineerContactNo.getText().toString().trim().isEmpty() && textNewSiteIsReqdEngineerStellar.getText().toString().trim().equalsIgnoreCase("yes")) {
            Toast.makeText(this, "Please enter Engineer Contact Number.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSitePettyEngineerContactNo.getText().toString().trim().length() != 10 && !edTextNewSitePettyEngineerContactNo.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Correct Engineer Contact Number.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteIsReqdEngineerStellar.getText().toString().trim().equalsIgnoreCase("no") && (!edTextNewSitePettyEngineerName.getText().toString().trim().isEmpty() || !edTextNewSitePettyEngineerContactNo.getText().toString().trim().isEmpty())) {
            if (edTextNewSitePettyEngineerName.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Contractor Name.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextNewSitePettyEngineerContactNo.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Contractor Contact Number.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextNewSitePettyEngineerContactNo.getText().toString().trim().length() != 10) {
                Toast.makeText(this, "Please enter Correct Engineer Contact Number.", Toast.LENGTH_LONG).show();
                return;
            }
        }

        if (textNewSiteMeetingPerson.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Meeting Person.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteDecisionMaker.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Decision Maker.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteSiteSegment.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Site Segment.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteVisitType.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Visit Type.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteProjectSegment.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Project Segment.", Toast.LENGTH_LONG).show();
            return;
        }
        if (typeChecker == 1 && typeOfConstruction.isEmpty()) {
            Toast.makeText(this, "Please select Floor.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteCurrentStageOfConstruction.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Current Stage of Construction.", Toast.LENGTH_LONG).show();
            return;
        }

        if (edTextNewSiteBuiltUpArea.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Built-up Area.", Toast.LENGTH_LONG).show();
            return;
        }
        if (Integer.parseInt(edTextNewSiteBuiltUpArea.getText().toString()) <= 0) {
            Toast.makeText(this, "Please enter current Built-up Area.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSiteSitePotential.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Site Potential.", Toast.LENGTH_LONG).show();
            return;
        }
        if (Integer.parseInt(edTextNewSiteSitePotential.getText().toString()) <= 0) {
            Toast.makeText(this, "Please enter current Site Potential.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSiteConsumedTillDate.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Consumed Till Date.", Toast.LENGTH_LONG).show();
            return;
        }
        if (Integer.parseInt(edTextNewSiteConsumedTillDate.getText().toString()) < 0) {
            Toast.makeText(this, "Please enter current Consumed Till Date.", Toast.LENGTH_LONG).show();
            return;
        }
        if (Integer.parseInt(edTextNewSiteBalancePotential.getText().toString()) < 0) {
            Toast.makeText(this, "Site Potential can't lower then Consumed Till Date qty.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSiteBalancePotentialManual.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Site Potential.", Toast.LENGTH_LONG).show();
            return;
        }
        if (Integer.parseInt(edTextNewSiteBalancePotentialManual.getText().toString()) < 0) {
            Toast.makeText(this, "Site Potential can't lower then Consumed Till Date qty.", Toast.LENGTH_LONG).show();
            return;
        }
        if (Integer.parseInt(edTextNewSiteBalancePotential.getText().toString()) != Integer.parseInt(edTextNewSiteBalancePotentialManual.getText().toString()) && valueChecker == 1) {
            confirmationPopup(1);
            return;
        }
        if (edTextNewSiteSiteCategory.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Site Category.", Toast.LENGTH_LONG).show();
            return;
        }

        if (textNewSiteBrandUsed.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Brand Used.", Toast.LENGTH_LONG).show();
            return;
        }
        if (edTextNewSitePricePerBag.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please enter Price per Bag.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteConversion.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Business Generation.", Toast.LENGTH_LONG).show();
            return;
        }
        if (!textNewSiteConversion.getText().toString().trim().equalsIgnoreCase("non converted")) {
            if (textNewSiteProduct.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Product Name.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextNewSiteOrderQuantity.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Order Quantity.", Toast.LENGTH_LONG).show();
                return;
            }
            if (Integer.parseInt(edTextNewSiteOrderQuantity.getText().toString()) > Integer.parseInt(edTextNewSiteBalancePotential.getText().toString())) {
                Toast.makeText(this, "Order qty. can't more that Balance Potential.", Toast.LENGTH_LONG).show();
                return;
            }
            if (Integer.parseInt(edTextNewSiteOrderQuantity.getText().toString()) > 0 && textNewSiteRequestDateOfDelivery.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Requested Date of Delivery.", Toast.LENGTH_LONG).show();
                return;
            }
            if (textNewSiteCounterType.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Counter Type.", Toast.LENGTH_LONG).show();
                return;
            }
            if (textNewSiteCounterName.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Counter Name.", Toast.LENGTH_LONG).show();
                return;
            }
            if (Integer.parseInt(edTextNewSiteOrderQuantity.getText().toString()) > 0 &&(Integer.parseInt(edTextNewSitePricePerBag.getText().toString()) < 200 || Integer.parseInt(edTextNewSitePricePerBag.getText().toString()) > 999)) {
                Toast.makeText(this, "Please enter Price per Bag between 200 to 999.", Toast.LENGTH_LONG).show();
                return;
            }
        }

        if (textNewSiteReasonsForNonConversion.getText().toString().trim().isEmpty() && textNewSiteConversion.getText().toString().trim().equalsIgnoreCase("non converted")) {
            Toast.makeText(this, "Please select Reason for Non-Business Generation.", Toast.LENGTH_LONG).show();
            return;
        }

        if (textNewSiteSitePriority.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Site Priority.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteWeatherShieldDemo.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Weather Shield Demo.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textNewSiteSiteStatus.getText().toString().trim().isEmpty()) {
            Toast.makeText(this, "Please select Site Status.", Toast.LENGTH_LONG).show();
            return;
        }

        DataForUpload dataForUpload =new DataForUpload();
        dataForUpload.setSite_transaction_id(edTextNewSiteTransactionId.getText().toString().trim());
        dataForUpload.setSite_unique_id(edTextNewSiteUniqueSiteId.getText().toString().trim());
        dataForUpload.setSite_creation_date(edTextNewSiteSiteCreationDate.getText().toString().trim());
        dataForUpload.setSite_visit_date(edTextNewSiteVisitDate.getText().toString().trim());
        dataForUpload.setEmployee_code(edTextNewSiteEmployeeCode.getText().toString().trim());
        dataForUpload.setEmployee_name(edTextNewSiteEmployeeName.getText().toString().trim());
        dataForUpload.setZone(edTextNewSiteZone.getText().toString().trim());
        dataForUpload.setBranch(branchCode);
        dataForUpload.setState(textNewSiteState.getText().toString().trim());
        dataForUpload.setDistrict(textNewSiteDistrict.getText().toString().trim());
        dataForUpload.setLatitude(edTextNewSiteLatitude.getText().toString().trim());
        dataForUpload.setLongitude(edTextNewSiteLongitude.getText().toString().trim());
        dataForUpload.setCustomer_name(edTextNewSiteCustomerName.getText().toString().trim());
        dataForUpload.setCustomer_contact_number(edTextNewSiteCustomerContactNo.getText().toString().trim());
        dataForUpload.setCustomer_full_address(edTextNewSiteFullAddress.getText().toString().trim());
        dataForUpload.setIs_register_contractor(textNewSiteIsReqdContractorLink.getText().toString().trim());
        dataForUpload.setContractor_name(edTextNewSitePettyContractorName.getText().toString().trim());
        dataForUpload.setContractor_contact_number(edTextNewSitePettyContractorContactNo.getText().toString().trim());
        dataForUpload.setIs_register_engineer(textNewSiteIsReqdEngineerStellar.getText().toString().trim());
        dataForUpload.setEngineer_name(edTextNewSitePettyEngineerName.getText().toString().trim());
        dataForUpload.setEngineer_contact_number(edTextNewSitePettyEngineerContactNo.getText().toString().trim());
        dataForUpload.setMeeting_person(textNewSiteMeetingPerson.getText().toString().trim());
        dataForUpload.setDecision_maker(textNewSiteDecisionMaker.getText().toString().trim());
        dataForUpload.setSite_segment(textNewSiteSiteSegment.getText().toString().trim());
        dataForUpload.setVisit_type(textNewSiteVisitType.getText().toString().trim());
        dataForUpload.setProject_segment(textNewSiteProjectSegment.getText().toString().trim());
        dataForUpload.setType_of_construction(textNewSiteTypeOfConstruction.getText().toString().trim());
        dataForUpload.setFloor_count(typeOfConstruction);
        dataForUpload.setCurrent_stage_of_construction(textNewSiteCurrentStageOfConstruction.getText().toString().trim());
        dataForUpload.setBuilt_up_area(edTextNewSiteBuiltUpArea.getText().toString().trim());
        dataForUpload.setSite_potential(edTextNewSiteSitePotential.getText().toString().trim());
        dataForUpload.setConsumed_till_date(edTextNewSiteConsumedTillDate.getText().toString().trim());
        dataForUpload.setBalance_potential(edTextNewSiteBalancePotential.getText().toString().trim());
        dataForUpload.setBalance_potential_manual(edTextNewSiteBalancePotentialManual.getText().toString().trim());
        dataForUpload.setSite_category(edTextNewSiteSiteCategory.getText().toString().trim());
        dataForUpload.setBrand_used(textNewSiteBrandUsed.getText().toString().trim());
        dataForUpload.setPrice_per_bag(edTextNewSitePricePerBag.getText().toString().trim());
        dataForUpload.setConversion(textNewSiteConversion.getText().toString().trim());
        dataForUpload.setProduct_name(textNewSiteProduct.getText().toString().trim());
        dataForUpload.setOrder_quantity(edTextNewSiteOrderQuantity.getText().toString().trim());
        dataForUpload.setRequested_date_of_delivery(textNewSiteRequestDateOfDelivery.getText().toString().trim());
        dataForUpload.setCounter_type(textNewSiteCounterType.getText().toString().trim());
        dataForUpload.setCounter_name(textNewSiteCounterName.getText().toString().trim());
        dataForUpload.setCounter_code(edTextNewSiteCounterCode.getText().toString().trim());
        dataForUpload.setReason_for_non_conversion(textNewSiteReasonsForNonConversion.getText().toString().trim());
        dataForUpload.setSite_priority(textNewSiteSitePriority.getText().toString().trim());
        dataForUpload.setWeather_shield_demo(textNewSiteWeatherShieldDemo.getText().toString().trim());
        dataForUpload.setApproval_status("Pending");
        dataForUpload.setDate_time("");
        dataForUpload.setAsm_name(textNewSiteAsmName.getText().toString().trim());
        dataForUpload.setAsm_employee_id(edTextNewSiteAsmEmployeeId.getText().toString().trim());
        dataForUpload.setActual_date_of_delivery("");
        dataForUpload.setDelivery_remarks("");
        dataForUpload.setReason_for_not_delivery("");
        dataForUpload.setSite_status(textNewSiteSiteStatus.getText().toString().trim());
        dataForUpload.setRemarks(edTextNewSiteSiteRemarks.getText().toString().trim());

        mNewDatabaseForSiteLead.insertSiteLead(dataForUpload,0);

        requestForNewSiteLeadAndConversionTracking();
    }

    private void checkExistingSiteLeadDetails() {
        try {
            if (textExistingSiteIsReqdContractorLink.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select is register contractor in StarLink.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextExistingSitePettyContractorName.getText().toString().trim().isEmpty() && textExistingSiteIsReqdContractorLink.getText().toString().trim().equalsIgnoreCase("yes")) {
                Toast.makeText(this, "Please enter Contractor Name.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextExistingSitePettyContractorContactNo.getText().toString().trim().isEmpty() && textExistingSiteIsReqdContractorLink.getText().toString().trim().equalsIgnoreCase("yes")) {
                Toast.makeText(this, "Please enter Contractor Contact Number.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextExistingSitePettyContractorContactNo.getText().toString().trim().length() != 10 && !edTextExistingSitePettyContractorContactNo.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Correct Contractor Contact Number.", Toast.LENGTH_LONG).show();
                return;
            }
            if (textExistingSiteIsReqdContractorLink.getText().toString().trim().equalsIgnoreCase("no") && (!edTextExistingSitePettyContractorName.getText().toString().trim().isEmpty() || !edTextExistingSitePettyContractorContactNo.getText().toString().trim().isEmpty())) {
                if (edTextExistingSitePettyContractorName.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please enter Contractor Name.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (edTextExistingSitePettyContractorContactNo.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please enter Contractor Contact Number.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (edTextExistingSitePettyContractorContactNo.getText().toString().trim().length() != 10) {
                    Toast.makeText(this, "Please enter Correct Contractor Contact Number.", Toast.LENGTH_LONG).show();
                    return;
                }
            }

            if (textExistingSiteIsReqdEngineerStellar.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select is register engineer in StarStellar.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextExistingSitePettyEngineerName.getText().toString().trim().isEmpty() && textExistingSiteIsReqdEngineerStellar.getText().toString().trim().equalsIgnoreCase("yes")) {
                Toast.makeText(this, "Please enter Engineer Name.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextExistingSitePettyEngineerContactNo.getText().toString().trim().isEmpty() && textExistingSiteIsReqdEngineerStellar.getText().toString().trim().equalsIgnoreCase("yes")) {
                Toast.makeText(this, "Please enter Engineer Contact Number.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextExistingSitePettyEngineerContactNo.getText().toString().trim().length() != 10 && !edTextExistingSitePettyEngineerContactNo.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Correct Engineer Contact Number.", Toast.LENGTH_LONG).show();
                return;
            }
            if (textExistingSiteIsReqdEngineerStellar.getText().toString().trim().equalsIgnoreCase("no") && (!edTextExistingSitePettyEngineerName.getText().toString().trim().isEmpty() || !edTextExistingSitePettyEngineerContactNo.getText().toString().trim().isEmpty())) {
                if (edTextExistingSitePettyEngineerName.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please enter Contractor Name.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (edTextExistingSitePettyEngineerContactNo.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please enter Contractor Contact Number.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (edTextExistingSitePettyEngineerContactNo.getText().toString().trim().length() != 10) {
                    Toast.makeText(this, "Please enter Correct Engineer Contact Number.", Toast.LENGTH_LONG).show();
                    return;
                }
            }

            if (textExistingSiteMeetingPerson.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Meeting Person.", Toast.LENGTH_LONG).show();
                return;
            }
            if (textExistingSiteDecisionMaker.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Decision Maker.", Toast.LENGTH_LONG).show();
                return;
            }
            if (textExistingSiteCurrentStageOfConstruction.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Current Stage of Construction.", Toast.LENGTH_LONG).show();
                return;
            }

            if (edTextExistingSiteConsumedTillDate.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Consumed Till Date.", Toast.LENGTH_LONG).show();
                return;
            }
            if (Integer.parseInt(edTextExistingSiteConsumedTillDate.getText().toString()) < 0) {
                Toast.makeText(this, "Please enter current Consumed Till Date.", Toast.LENGTH_LONG).show();
                return;
            }
            if (Integer.parseInt(edTextExistingSiteBalancePotential.getText().toString()) < 0) {
                Toast.makeText(this, "Site Potential can't lower then Consumed Till Date qty.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextExistingSiteBalancePotentialManual.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Site Potential.", Toast.LENGTH_LONG).show();
                return;
            }
            if (Integer.parseInt(edTextExistingSiteBalancePotentialManual.getText().toString()) < 0) {
                Toast.makeText(this, "Site Potential can't lower then Consumed Till Date qty.", Toast.LENGTH_LONG).show();
                return;
            }
            if (Integer.parseInt(edTextExistingSiteBalancePotential.getText().toString()) != Integer.parseInt(edTextExistingSiteBalancePotentialManual.getText().toString()) && valueChecker == 1) {
                confirmationPopup(2);
                return;
            }

            if (textExistingSiteBrandUsed.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Brand Used.", Toast.LENGTH_LONG).show();
                return;
            }
            if (edTextExistingSitePricePerBag.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please enter Price per Bag.", Toast.LENGTH_LONG).show();
                return;
            }

            if (textExistingSiteConversion.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Business Generation.", Toast.LENGTH_LONG).show();
                return;
            }
            if (!textExistingSiteConversion.getText().toString().trim().equalsIgnoreCase("non converted")&&!textExistingSiteConversion.getText().toString().trim().equalsIgnoreCase("converted to non star site")) {
                if (textExistingSiteProduct.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please select Product Name.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (edTextExistingSiteOrderQuantity.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please enter Order Quantity.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (Integer.parseInt(edTextExistingSiteOrderQuantity.getText().toString()) > Integer.parseInt(edTextExistingSiteBalancePotential.getText().toString())) {
                    Toast.makeText(this, "Order qty. can't more that Balance Potential.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (Integer.parseInt(edTextExistingSiteOrderQuantity.getText().toString()) > 0 && textExistingSiteRequestDateOfDelivery.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please select Requested Date of Delivery.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (textExistingSiteCounterType.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please select Counter Type.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (textExistingSiteCounterName.getText().toString().trim().isEmpty()) {
                    Toast.makeText(this, "Please select Counter Name.", Toast.LENGTH_LONG).show();
                    return;
                }
                if (Integer.parseInt(edTextExistingSiteOrderQuantity.getText().toString()) > 0 &&(Integer.parseInt(edTextExistingSitePricePerBag.getText().toString()) < 200 || Integer.parseInt(edTextExistingSitePricePerBag.getText().toString()) > 999)) {
                    Toast.makeText(this, "Please enter Price per Bag between 200 to 999.", Toast.LENGTH_LONG).show();
                    return;
                }
            }

            if (textExistingSiteReasonsForNonConversion.getText().toString().trim().isEmpty() && (textExistingSiteConversion.getText().toString().trim().equalsIgnoreCase("non converted")||textExistingSiteConversion.getText().toString().trim().equalsIgnoreCase("converted to non star site"))) {
                Toast.makeText(this, "Please select Reason for Non-Business Generation.", Toast.LENGTH_LONG).show();
                return;
            }

            if (textExistingSiteWeatherShieldDemo.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Weather Shield Demo.", Toast.LENGTH_LONG).show();
                return;
            }
            if (textExistingSiteSiteStatus.getText().toString().trim().isEmpty()) {
                Toast.makeText(this, "Please select Site Status.", Toast.LENGTH_LONG).show();
                return;
            }


            String visit_type = "";
            if (textExistingSiteVisitType.getText().toString().trim().equalsIgnoreCase("non star site") &&
                    branchCategory.trim().equalsIgnoreCase("star site")) {
                visit_type = "Star Site";
            } else {
                visit_type = textExistingSiteVisitType.getText().toString().trim();
            }

            DataForUpload dataForUpload = new DataForUpload();
            dataForUpload.setSite_transaction_id(edTextExistingSiteTransactionId.getText().toString().trim());
            dataForUpload.setSite_unique_id(textExistingUniqueId.getText().toString().trim());
            dataForUpload.setSite_creation_date(edTextExistingSiteSiteCreationDate.getText().toString().trim());
            dataForUpload.setSite_visit_date(edTextExistingSiteVisitDate.getText().toString().trim());
            dataForUpload.setEmployee_code(edTextExistingSiteEmployeeCode.getText().toString().trim());
            dataForUpload.setEmployee_name(edTextExistingSiteEmployeeName.getText().toString().trim());
            dataForUpload.setZone(edTextExistingSiteZone.getText().toString().trim());
            dataForUpload.setBranch(textExistingSiteBranch.getText().toString().trim());
            dataForUpload.setState(textExistingSiteState.getText().toString().trim());
            dataForUpload.setDistrict(textExistingSiteDistrict.getText().toString().trim());
            dataForUpload.setLatitude(edTextExistingSiteLatitude.getText().toString().trim());
            dataForUpload.setLongitude(edTextExistingSiteLongitude.getText().toString().trim());
            dataForUpload.setCustomer_name(edTextExistingSiteCustomerName.getText().toString().trim());
            dataForUpload.setCustomer_contact_number(edTextExistingSiteCustomerContactNo.getText().toString().trim());
            dataForUpload.setCustomer_full_address(edTextExistingSiteFullAddress.getText().toString().trim());
            dataForUpload.setIs_register_contractor(textExistingSiteIsReqdContractorLink.getText().toString().trim());
            dataForUpload.setContractor_name(edTextExistingSitePettyContractorName.getText().toString().trim());
            dataForUpload.setContractor_contact_number(edTextExistingSitePettyContractorContactNo.getText().toString().trim());
            dataForUpload.setIs_register_engineer(textExistingSiteIsReqdEngineerStellar.getText().toString().trim());
            dataForUpload.setEngineer_name(edTextExistingSitePettyEngineerName.getText().toString().trim());
            dataForUpload.setEngineer_contact_number(edTextExistingSitePettyEngineerContactNo.getText().toString().trim());
            dataForUpload.setMeeting_person(textExistingSiteMeetingPerson.getText().toString().trim());
            dataForUpload.setDecision_maker(textExistingSiteDecisionMaker.getText().toString().trim());
            dataForUpload.setSite_segment(textExistingSiteSiteSegment.getText().toString().trim());
            dataForUpload.setVisit_type(visit_type);
            dataForUpload.setProject_segment(textExistingSiteProjectSegment.getText().toString().trim());
            dataForUpload.setType_of_construction(textExistingSiteTypeOfConstruction.getText().toString().trim());
            dataForUpload.setFloor_count(textExistingSiteTypeOfConstruction.getText().toString().trim());
            dataForUpload.setCurrent_stage_of_construction(textExistingSiteCurrentStageOfConstruction.getText().toString().trim());
            dataForUpload.setBuilt_up_area(edTextExistingSiteBuiltUpArea.getText().toString().trim());
            dataForUpload.setSite_potential(edTextExistingSiteSitePotential.getText().toString().trim());
            dataForUpload.setConsumed_till_date(edTextExistingSiteConsumedTillDate.getText().toString().trim());
            dataForUpload.setBalance_potential(edTextExistingSiteBalancePotential.getText().toString().trim());
            dataForUpload.setBalance_potential_manual(edTextExistingSiteBalancePotentialManual.getText().toString().trim());
            dataForUpload.setSite_category(edTextExistingSiteSiteCategory.getText().toString().trim());
            dataForUpload.setBrand_used(textExistingSiteBrandUsed.getText().toString().trim());
            dataForUpload.setPrice_per_bag(edTextExistingSitePricePerBag.getText().toString().trim());
            dataForUpload.setConversion(textExistingSiteConversion.getText().toString().trim());
            dataForUpload.setProduct_name(textExistingSiteProduct.getText().toString().trim());
            dataForUpload.setOrder_quantity(edTextExistingSiteOrderQuantity.getText().toString().trim());
            dataForUpload.setRequested_date_of_delivery(textExistingSiteRequestDateOfDelivery.getText().toString().trim());
            dataForUpload.setCounter_type(textExistingSiteCounterType.getText().toString().trim());
            dataForUpload.setCounter_name(textExistingSiteCounterName.getText().toString().trim());
            dataForUpload.setCounter_code(edTextExistingSiteCounterCode.getText().toString().trim());
            dataForUpload.setReason_for_non_conversion(textExistingSiteReasonsForNonConversion.getText().toString().trim());
            dataForUpload.setSite_priority(textExistingSiteSitePriority.getText().toString().trim());
            dataForUpload.setWeather_shield_demo(textExistingSiteWeatherShieldDemo.getText().toString().trim());
            dataForUpload.setApproval_status("");
            dataForUpload.setDate_time("");
            dataForUpload.setAsm_name(textExistingSiteAsmName.getText().toString().trim());
            dataForUpload.setAsm_employee_id(edTextExistingSiteAsmEmployeeId.getText().toString().trim());
            dataForUpload.setActual_date_of_delivery("");
            dataForUpload.setDelivery_remarks("");
            dataForUpload.setReason_for_not_delivery("");
            dataForUpload.setSite_status(textExistingSiteSiteStatus.getText().toString().trim());
            dataForUpload.setRemarks(edTextExistingSiteSiteRemarks.getText().toString().trim());

            mNewDatabaseForSiteLead.updateFullSiteLead(dataForUpload, 0);
        }catch (Exception e){
            Log.d("_DOWNLOAD_", "checkExistingSiteLeadDetails: "+e);
        }
        requestForUpdateSiteLeadAndConversionTracking();
    }

    private void checkSwitchSiteLeadDetails(){
        try{
            if(!visitType.equalsIgnoreCase("non star site")){
                Toast.makeText(this, "If the site can not convert to non-star then please update from existing site.", Toast.LENGTH_LONG).show();
                return;
            }
            if(edTextExistingSiteConsumedTillDate.getText().toString().trim().isEmpty()){
                Toast.makeText(this, "Please enter Consumed Till Date.", Toast.LENGTH_LONG).show();
                return;
            }
            if(Integer.parseInt(edTextExistingSiteConsumedTillDate.getText().toString().trim())>Integer.parseInt(edTextExistingSiteSitePotential.getText().toString().trim())){
                Toast.makeText(this, "Your Consumed Till Date is bigger than Site Potential.", Toast.LENGTH_LONG).show();
                return;
            }
            if(textExistingSiteBrandUsed.getText().toString().trim().isEmpty()){
                Toast.makeText(this, "Please select Current Brand Used.", Toast.LENGTH_LONG).show();
                return;
            }
            if(edTextExistingSitePricePerBag.getText().toString().trim().isEmpty()){
                Toast.makeText(this, "Please select Current Brand Price Per Bag.", Toast.LENGTH_LONG).show();
                return;
            }

            DataForUpload dataForUpload = new DataForUpload();
            dataForUpload.setSite_transaction_id(edTextExistingSiteTransactionId.getText().toString().trim());
            dataForUpload.setSite_unique_id(textExistingUniqueId.getText().toString().trim());
            dataForUpload.setSite_creation_date(edTextExistingSiteSiteCreationDate.getText().toString().trim());
            dataForUpload.setSite_visit_date(edTextExistingSiteVisitDate.getText().toString().trim());
            dataForUpload.setEmployee_code(edTextExistingSiteEmployeeCode.getText().toString().trim());
            dataForUpload.setEmployee_name(edTextExistingSiteEmployeeName.getText().toString().trim());
            dataForUpload.setZone(edTextExistingSiteZone.getText().toString().trim());
            dataForUpload.setBranch(textExistingSiteBranch.getText().toString().trim());
            dataForUpload.setState(textExistingSiteState.getText().toString().trim());
            dataForUpload.setDistrict(textExistingSiteDistrict.getText().toString().trim());
            dataForUpload.setLatitude(edTextExistingSiteLatitude.getText().toString().trim());
            dataForUpload.setLongitude(edTextExistingSiteLongitude.getText().toString().trim());
            dataForUpload.setCustomer_name(edTextExistingSiteCustomerName.getText().toString().trim());
            dataForUpload.setCustomer_contact_number(edTextExistingSiteCustomerContactNo.getText().toString().trim());
            dataForUpload.setCustomer_full_address(edTextExistingSiteFullAddress.getText().toString().trim());
            dataForUpload.setIs_register_contractor(textExistingSiteIsReqdContractorLink.getText().toString().trim());
            dataForUpload.setContractor_name(edTextExistingSitePettyContractorName.getText().toString().trim());
            dataForUpload.setContractor_contact_number(edTextExistingSitePettyContractorContactNo.getText().toString().trim());
            dataForUpload.setIs_register_engineer(textExistingSiteIsReqdEngineerStellar.getText().toString().trim());
            dataForUpload.setEngineer_name(edTextExistingSitePettyEngineerName.getText().toString().trim());
            dataForUpload.setEngineer_contact_number(edTextExistingSitePettyEngineerContactNo.getText().toString().trim());
            dataForUpload.setMeeting_person(textExistingSiteMeetingPerson.getText().toString().trim());
            dataForUpload.setDecision_maker(textExistingSiteDecisionMaker.getText().toString().trim());
            dataForUpload.setSite_segment(textExistingSiteSiteSegment.getText().toString().trim());
            dataForUpload.setVisit_type(textExistingSiteVisitType.getText().toString().trim());
            dataForUpload.setProject_segment(textExistingSiteProjectSegment.getText().toString().trim());
            dataForUpload.setType_of_construction(textExistingSiteTypeOfConstruction.getText().toString().trim());
            dataForUpload.setFloor_count(textExistingSiteTypeOfConstruction.getText().toString().trim());
            dataForUpload.setCurrent_stage_of_construction(textExistingSiteCurrentStageOfConstruction.getText().toString().trim());
            dataForUpload.setBuilt_up_area(edTextExistingSiteBuiltUpArea.getText().toString().trim());
            dataForUpload.setSite_potential(edTextExistingSiteSitePotential.getText().toString().trim());
            dataForUpload.setConsumed_till_date(edTextExistingSiteConsumedTillDate.getText().toString().trim());
            dataForUpload.setBalance_potential(edTextExistingSiteBalancePotential.getText().toString().trim());
            dataForUpload.setBalance_potential_manual(edTextExistingSiteBalancePotentialManual.getText().toString().trim());
            dataForUpload.setSite_category(edTextExistingSiteSiteCategory.getText().toString().trim());
            dataForUpload.setBrand_used(textExistingSiteBrandUsed.getText().toString().trim());
            dataForUpload.setPrice_per_bag(edTextExistingSitePricePerBag.getText().toString().trim());
            dataForUpload.setConversion(textExistingSiteConversion.getText().toString().trim());
            dataForUpload.setReason_for_non_conversion(textExistingSiteReasonsForNonConversion.getText().toString().trim());
            dataForUpload.setSite_priority(textExistingSiteSitePriority.getText().toString().trim());
            dataForUpload.setWeather_shield_demo(textExistingSiteWeatherShieldDemo.getText().toString().trim());
            dataForUpload.setApproval_status("");
            dataForUpload.setDate_time("");
            dataForUpload.setActual_date_of_delivery("");
            dataForUpload.setDelivery_remarks("");
            dataForUpload.setReason_for_not_delivery("");
            dataForUpload.setSite_status(textExistingSiteSiteStatus.getText().toString().trim());
            dataForUpload.setRemarks(edTextExistingSiteSiteRemarks.getText().toString().trim());

            if(siteEntryType.equalsIgnoreCase("ExistingSite")){
                dataForUpload.setProduct_name(textExistingSiteProduct.getText().toString().trim());
                dataForUpload.setOrder_quantity(edTextExistingSiteOrderQuantity.getText().toString().trim());
                dataForUpload.setRequested_date_of_delivery(textExistingSiteRequestDateOfDelivery.getText().toString().trim());
                dataForUpload.setCounter_type(textExistingSiteCounterType.getText().toString().trim());
                dataForUpload.setCounter_name(textExistingSiteCounterName.getText().toString().trim());
                dataForUpload.setCounter_code(edTextExistingSiteCounterCode.getText().toString().trim());
                dataForUpload.setAsm_name(textExistingSiteAsmName.getText().toString().trim());
                dataForUpload.setAsm_employee_id(edTextExistingSiteAsmEmployeeId.getText().toString().trim());
            }else{
                dataForUpload.setProduct_name("");
                dataForUpload.setOrder_quantity("");
                dataForUpload.setRequested_date_of_delivery("");
                dataForUpload.setCounter_type("");
                dataForUpload.setCounter_name("");
                dataForUpload.setCounter_code("");
                dataForUpload.setAsm_name("");
                dataForUpload.setAsm_employee_id("");
            }

            mNewDatabaseForSiteLead.updateFullSiteLead(dataForUpload, 0);
        }catch (Exception e){
            Log.d("_DOWNLOAD_", "checkSwitchSiteLeadDetails: "+e);
        }
        requestForUpdateSiteLeadAndConversionTracking();
    }

    private void confirmationPopup(int value) {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Sure...");
        builder.setMessage("Are you sure balance potential you enter that's correct?");
        builder.setPositiveButton("Yes", (dialog, which) -> {
            valueChecker = 0;
            if (value == 1)
                checkNewSiteLeadDetails();
            else
                checkExistingSiteLeadDetails();
        });
        builder.setNegativeButton("No", (dialog, which) -> dialog.dismiss());
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    private void checkAsmApprovePopupDetails() {
        if (textStatusASM.getText().toString().trim().equalsIgnoreCase("")) {
            Toast.makeText(this, "Please select Status.", Toast.LENGTH_LONG).show();
            return;
        }
        if (textStatusASM.getText().toString().trim().toLowerCase().startsWith("approved")) {
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

    // ***Site Add & Update***
    private void requestForNewSiteLeadAndConversionTracking() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (!NetworkUtil.isNetworkAvailable(mContext)) {
                runOnUiThread(() -> Toast.makeText(this, "Data Save. No Internet Connection.\nOnce you get your network SYNC your application", Toast.LENGTH_SHORT).show());
                new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
                return;
            }
        }
        progressDialogOpen("Uploading data ...");
        new Thread(() -> {
            try {
                JSONObject obj = new JSONObject();
                obj.put("site_transaction_id", edTextNewSiteTransactionId.getText().toString().trim());
                obj.put("site_unique_id", edTextNewSiteUniqueSiteId.getText().toString().trim());
                obj.put("site_creation_date", edTextNewSiteSiteCreationDate.getText().toString().trim());
                obj.put("site_visit_date", edTextNewSiteVisitDate.getText().toString().trim());
                obj.put("employee_code", edTextNewSiteEmployeeCode.getText().toString().trim());
                obj.put("employee_name", edTextNewSiteEmployeeName.getText().toString().trim());
                obj.put("zone", edTextNewSiteZone.getText().toString().trim());
                obj.put("branch", branchCode);
                obj.put("state", textNewSiteState.getText().toString().trim());
                obj.put("district", textNewSiteDistrict.getText().toString().trim());
                obj.put("latitude", edTextNewSiteLatitude.getText().toString().trim());
                obj.put("longitude", edTextNewSiteLongitude.getText().toString().trim());
                obj.put("customer_name", edTextNewSiteCustomerName.getText().toString().trim());
                obj.put("customer_contact_number", edTextNewSiteCustomerContactNo.getText().toString().trim());
                obj.put("customer_full_address", edTextNewSiteFullAddress.getText().toString().trim());
                obj.put("is_register_contractor", textNewSiteIsReqdContractorLink.getText().toString().trim());
                obj.put("contractor_name", edTextNewSitePettyContractorName.getText().toString().trim());
                obj.put("contractor_contact_number", edTextNewSitePettyContractorContactNo.getText().toString().trim());
                obj.put("is_register_engineer", textNewSiteIsReqdEngineerStellar.getText().toString().trim());
                obj.put("engineer_name", edTextNewSitePettyEngineerName.getText().toString().trim());
                obj.put("engineer_contact_number", edTextNewSitePettyEngineerContactNo.getText().toString().trim());
                obj.put("meeting_person", textNewSiteMeetingPerson.getText().toString().trim());
                obj.put("decision_maker", textNewSiteDecisionMaker.getText().toString().trim());
                obj.put("site_segment", textNewSiteSiteSegment.getText().toString().trim());
                obj.put("visit_type", textNewSiteVisitType.getText().toString().trim());
                obj.put("project_segment", textNewSiteProjectSegment.getText().toString().trim());
                obj.put("type_of_construction", textNewSiteTypeOfConstruction.getText().toString().trim());
                obj.put("floor_count", typeOfConstruction);
                obj.put("current_stage_of_construction", textNewSiteCurrentStageOfConstruction.getText().toString().trim());
                obj.put("built_up_area", edTextNewSiteBuiltUpArea.getText().toString().trim());
                obj.put("site_potential", edTextNewSiteSitePotential.getText().toString().trim());
                obj.put("consumed_till_date", edTextNewSiteConsumedTillDate.getText().toString().trim());
                obj.put("balance_potential", edTextNewSiteBalancePotential.getText().toString().trim());
                obj.put("balance_potential_manual", edTextNewSiteBalancePotentialManual.getText().toString().trim());
                obj.put("site_category", edTextNewSiteSiteCategory.getText().toString().trim());
                obj.put("brand_used", textNewSiteBrandUsed.getText().toString().trim());
                obj.put("price_per_bag", edTextNewSitePricePerBag.getText().toString().trim());
                obj.put("conversion", textNewSiteConversion.getText().toString().trim());
                obj.put("product_name", textNewSiteProduct.getText().toString().trim());
                obj.put("order_quantity", edTextNewSiteOrderQuantity.getText().toString().trim());
                obj.put("requested_date_of_delivery", textNewSiteRequestDateOfDelivery.getText().toString().trim());
                obj.put("counter_type", textNewSiteCounterType.getText().toString().trim());
                obj.put("counter_name", textNewSiteCounterName.getText().toString().trim());
                obj.put("counter_code", edTextNewSiteCounterCode.getText().toString().trim());
                obj.put("reason_for_non_conversion", textNewSiteReasonsForNonConversion.getText().toString().trim());
                obj.put("site_priority", textNewSiteSitePriority.getText().toString().trim());
                obj.put("weather_shield_demo", textNewSiteWeatherShieldDemo.getText().toString().trim());
                obj.put("approval_status", "Pending");
                obj.put("date_time", "");
                obj.put("asm_name", textNewSiteAsmName.getText().toString().trim());
                obj.put("asm_employee_id", edTextNewSiteAsmEmployeeId.getText().toString().trim());
                obj.put("actual_date_of_delivery", "");
                obj.put("delivery_remarks", "");
                obj.put("reason_for_not_delivery", "");
                obj.put("site_status", textNewSiteSiteStatus.getText().toString().trim());
                obj.put("remarks", edTextNewSiteSiteRemarks.getText().toString().trim());

                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, obj.toString());

                Log.d("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking: " + obj);

                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/api_site_lead_form_submit.php")
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();

                if (response.body() != null) {
                    String responseString = response.body().string();
                    try {
                        JSONObject jsonObject = new JSONObject(responseString);
                        if (jsonObject.optString("process_status").equalsIgnoreCase("no")) {
                            mNewDatabaseForSiteLead.deleteSiteLead(edTextNewSiteTransactionId.getText().toString().trim());
                            ((NewSiteLeadActivity) mContext).showError(jsonObject.optString("error"));
                        } else {
                            mDataForDownloading._DOWNLOAD_ExistingSiteLeadList(success -> {
                                ((NewSiteLeadActivity) mContext).successMessageAndGotoPreviousPageForNewSiteLead();
                            });
                        }
                    } catch (Exception e) {
                        mNewDatabaseForSiteLead.deleteSiteLead(edTextNewSiteTransactionId.getText().toString().trim());
                        ((NewSiteLeadActivity) mContext).showError(e.getMessage());
                    }
                }

//                ((NewSiteLeadActivity) mContext).runOnUiThread(() -> {
////                    ((NewSiteLeadActivity) mContext).successMessageAndGotoPreviousPageForNewSiteLead();
//                    if (execute.isSuccessful()) {
//                        // HTTP 200–299
//                        ((NewSiteLeadActivity) mContext).successMessageAndGotoPreviousPageForNewSiteLead();
//
//                    } else if (execute.code() == 400) {
//                        // HTTP 400 Bad Request
//                        try {
//                            JSONObject json = new JSONObject(execute.body().toString());
//                            ((NewSiteLeadActivity) mContext).showError(json.optString("error"));
//                        } catch (JSONException e) {
//                            ((NewSiteLeadActivity) mContext).showError("Bad Request! Please check the data.");
//                        }
//                    } else {
//                        // Other errors
//                        ((NewSiteLeadActivity) mContext).showError("Something went wrong! Code: " + execute.code());
//                    }
//                });

            } catch (Exception e) {
                Log.e("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking Exception: " + e.getMessage(), e);
            }
        }).start();
    }

    private void requestForUpdateSiteLeadAndConversionTracking() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (!NetworkUtil.isNetworkAvailable(mContext)) {
                runOnUiThread(() -> Toast.makeText(this, "Data Save. No Internet Connection.\nOnce you get your network SYNC your application", Toast.LENGTH_SHORT).show());
                new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
                return;
            }
        }
        progressDialogOpen("Updating data ...");
        new Thread(() -> {
            try {
                JSONObject obj = new JSONObject();
                obj.put("site_transaction_id", edTextExistingSiteTransactionId.getText().toString().trim());
                obj.put("site_unique_id", textExistingUniqueId.getText().toString().trim());
                obj.put("site_creation_date", edTextExistingSiteSiteCreationDate.getText().toString().trim());
                obj.put("site_visit_date", edTextExistingSiteVisitDate.getText().toString().trim());
                obj.put("employee_code", edTextExistingSiteEmployeeCode.getText().toString().trim());
                obj.put("employee_name", edTextExistingSiteEmployeeName.getText().toString().trim());
                obj.put("zone", edTextExistingSiteZone.getText().toString().trim());
                obj.put("branch", textExistingSiteBranch.getText().toString().trim());
                obj.put("state", textExistingSiteState.getText().toString().trim());
                obj.put("district", textExistingSiteDistrict.getText().toString().trim());
                obj.put("latitude", edTextExistingSiteLatitude.getText().toString().trim());
                obj.put("longitude", edTextExistingSiteLongitude.getText().toString().trim());
                obj.put("customer_name", edTextExistingSiteCustomerName.getText().toString().trim());
                obj.put("customer_contact_number", edTextExistingSiteCustomerContactNo.getText().toString().trim());
                obj.put("customer_full_address", edTextExistingSiteFullAddress.getText().toString().trim());
                obj.put("is_register_contractor", textExistingSiteIsReqdContractorLink.getText().toString().trim());
                obj.put("contractor_name", edTextExistingSitePettyContractorName.getText().toString().trim());
                obj.put("contractor_contact_number", edTextExistingSitePettyContractorContactNo.getText().toString().trim());
                obj.put("is_register_engineer", textExistingSiteIsReqdEngineerStellar.getText().toString().trim());
                obj.put("engineer_name", edTextExistingSitePettyEngineerName.getText().toString().trim());
                obj.put("engineer_contact_number", edTextExistingSitePettyEngineerContactNo.getText().toString().trim());
                obj.put("meeting_person", textExistingSiteMeetingPerson.getText().toString().trim());
                obj.put("decision_maker", textExistingSiteDecisionMaker.getText().toString().trim());
                obj.put("site_segment", textExistingSiteSiteSegment.getText().toString().trim());
                obj.put("project_segment", textExistingSiteProjectSegment.getText().toString().trim());
                obj.put("type_of_construction", textExistingSiteTypeOfConstruction.getText().toString().trim());
                obj.put("floor_count", textExistingSiteTypeOfConstruction.getText().toString().trim());
                obj.put("current_stage_of_construction", textExistingSiteCurrentStageOfConstruction.getText().toString().trim());
                obj.put("built_up_area", edTextExistingSiteBuiltUpArea.getText().toString().trim());
                obj.put("site_potential", edTextExistingSiteSitePotential.getText().toString().trim());
                obj.put("consumed_till_date", edTextExistingSiteConsumedTillDate.getText().toString().trim());
                obj.put("balance_potential", edTextExistingSiteBalancePotential.getText().toString().trim());
                obj.put("balance_potential_manual", edTextExistingSiteBalancePotentialManual.getText().toString().trim());
                obj.put("site_category", edTextExistingSiteSiteCategory.getText().toString().trim());
                obj.put("brand_used", textExistingSiteBrandUsed.getText().toString().trim());
                obj.put("price_per_bag", edTextExistingSitePricePerBag.getText().toString().trim());
                obj.put("reason_for_non_conversion", textExistingSiteReasonsForNonConversion.getText().toString().trim());
                obj.put("site_priority", textExistingSiteSitePriority.getText().toString().trim());
                obj.put("weather_shield_demo", textExistingSiteWeatherShieldDemo.getText().toString().trim());
                obj.put("site_status", textExistingSiteSiteStatus.getText().toString().trim());
                obj.put("conversion", textExistingSiteConversion.getText().toString().trim());
                obj.put("remarks", edTextExistingSiteSiteRemarks.getText().toString().trim());

                if (textExistingSiteVisitType.getText().toString().trim().equalsIgnoreCase("non star site") &&
                        branchCategory.trim().equalsIgnoreCase("star site")) {
                    obj.put("visit_type", "Star Site");
                } else {
                    obj.put("visit_type", textExistingSiteVisitType.getText().toString().trim());
                }


                if(siteEntryType.equalsIgnoreCase("ExistingSite")){
                    obj.put("product_name", textExistingSiteProduct.getText().toString().trim());
                    obj.put("order_quantity", edTextExistingSiteOrderQuantity.getText().toString().trim());
                    obj.put("requested_date_of_delivery", textExistingSiteRequestDateOfDelivery.getText().toString().trim());
                    obj.put("counter_type", textExistingSiteCounterType.getText().toString().trim());
                    obj.put("counter_name", textExistingSiteCounterName.getText().toString().trim());
                    obj.put("counter_code", edTextExistingSiteCounterCode.getText().toString().trim());
                    obj.put("approval_status", "");
                    obj.put("date_time", "");
                    obj.put("asm_name", textExistingSiteAsmName.getText().toString().trim());
                    obj.put("asm_employee_id", edTextExistingSiteAsmEmployeeId.getText().toString().trim());
                    obj.put("actual_date_of_delivery", "");
                    obj.put("delivery_remarks", "");
                    obj.put("reason_for_not_delivery", "");
                }else{
                    obj.put("product_name", "");
                    obj.put("order_quantity", "");
                    obj.put("requested_date_of_delivery", "");
                    obj.put("counter_type", "");
                    obj.put("counter_name", "");
                    obj.put("counter_code","");
                    obj.put("approval_status", "approved");
                    obj.put("date_time", "");
                    obj.put("asm_name","");
                    obj.put("asm_employee_id", "");
                    obj.put("actual_date_of_delivery", "");
                    obj.put("delivery_remarks", "");
                    obj.put("reason_for_not_delivery", "");
                }


                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, obj.toString());

                Log.d("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking: " + obj);

                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/api_site_lead_form_submit.php")
                        .post(body)
                        .addHeader("Content-Type", "application/json")
                        .build();

                Response response = client.newCall(request).execute();

                if (response.body() != null) {
                    String responseString = response.body().string();
                    try {
                        JSONObject jsonObject = new JSONObject(responseString);
                        if (jsonObject.optString("process_status").equalsIgnoreCase("no")) {
                            ((NewSiteLeadActivity) mContext).showError(jsonObject.optString("error"));
                        } else {
                            mDataForDownloading._DOWNLOAD_ExistingSiteLeadList(success -> {
                                ((NewSiteLeadActivity) mContext).successMessageAndGotoPreviousPageForUpdateSiteLead();
                            });
                        }
                    } catch (Exception e) {
                        ((NewSiteLeadActivity) mContext).showError(e.getMessage());
                    }
                }
            } catch (Exception e) {
                Log.e("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking Exception: " + e.getMessage(), e);
            }
        }).start();
    }

    private void requestForUpdateSiteLeadStatus() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (!NetworkUtil.isNetworkAvailable(mContext)) {
                runOnUiThread(() -> Toast.makeText(this, "No Internet Connection", Toast.LENGTH_SHORT).show());
                return;
            }
        }
        progressDialogOpen("Updating data ...");
        new Thread(() -> {
            try {
                JSONObject obj = new JSONObject();
                obj.put("site_id", selectedSiteInfo.getUniqueId());
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

                Response response = client.newCall(request).execute();

                if (response.body() != null) {
                    String responseString = response.body().string();
                    try {
                        JSONObject jsonObject = new JSONObject(responseString);
                        if (jsonObject.optString("process_status").equalsIgnoreCase("no")) {
                            ((NewSiteLeadActivity) mContext).showError(jsonObject.optString("error"));
                        } else {
                            ((NewSiteLeadActivity) mContext).successMessageCleanAll();
                        }
                    } catch (Exception e) {
                        ((NewSiteLeadActivity) mContext).showError(e.getMessage());
                    }
                }

//                ((NewSiteLeadActivity) mContext).runOnUiThread(() -> {
//                    if (execute.isSuccessful()) {
//                        // HTTP 200–299
//                        ((NewSiteLeadActivity) mContext).successMessageCleanAll();
//
//                    } else if (execute.code() == 400) {
//                        // HTTP 400 Bad Request
//                        try {
//                            JSONObject json = new JSONObject(execute.body().toString());
//                            ((NewSiteLeadActivity) mContext).showError(json.optString("error"));
//                        } catch (JSONException e) {
//                            ((NewSiteLeadActivity) mContext).showError("Bad Request! Please check the data.");
//                        }
//                    } else {
//                        // Other errors
//                        ((NewSiteLeadActivity) mContext).showError("Something went wrong! Code: " + execute.code());
//                    }
//                });
            } catch (Exception e) {
                Log.e("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking Exception: " + e.getMessage(), e);
            }
        }).start();
    }

    // ***Show Success Message and Goto Back Page***
    private void showError(String message) {
        progressDialogClose();
        runOnUiThread(() -> Toast.makeText(mContext, message, Toast.LENGTH_LONG).show());
    }

    private void successMessageAndGotoPreviousPageForNewSiteLead() {
        progressDialogClose();
        runOnUiThread(() -> Toast.makeText(mContext, "New Site lead add successfully", Toast.LENGTH_LONG).show());
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }

    private void successMessageAndGotoPreviousPageForUpdateSiteLead() {
        progressDialogClose();
        runOnUiThread(() -> Toast.makeText(mContext, "Update Site lead add successfully", Toast.LENGTH_LONG).show());
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }

    private void successMessageCleanAll() {
        progressDialogClose();
        runOnUiThread(() -> Toast.makeText(mContext, "Update Site lead add successfully", Toast.LENGTH_LONG).show());
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }
}