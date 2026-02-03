package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
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

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNewCustomerDetailsTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourAttachmentExportTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.MultipleBranchSelectionAdapter;
import com.forcepower.acedns.adapter.MultipleDistributorSelectionAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.text.MessageFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Objects;

import static com.forcepower.acedns.constants.Constants.currentLat;
import static com.forcepower.acedns.constants.Constants.currentLong;
import static com.forcepower.acedns.constants.Constants.isCheckInToNewCustomer;
import static com.forcepower.acedns.constants.Constants.isGettingCurrentLocation;
import static com.forcepower.acedns.constants.Constants.isOrederToNewCustomer;
import static com.forcepower.acedns.util.Utils.setCustomerCheckInDataInPreferences;

public class AddNewCustActivity extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSelectRoute = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonTagDistributor = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonTagBranch = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonOtherInformation = null;
    @SuppressLint("StaticFieldLeak")
    public static Button buttonAddPicFromGallery = null;
    @SuppressLint("StaticFieldLeak")
    public static Button buttonAddPicFromCamera = null;
    @SuppressLint("StaticFieldLeak")
    public static Button buttonAddPicOutlet = null;
    @SuppressLint("StaticFieldLeak")
    public static Button buttonAddPicOwner = null;
    @SuppressLint("StaticFieldLeak")
    public static Button buttonAddPicGst = null;
    @SuppressLint("StaticFieldLeak")
    public static Button buttonAddPicAdhar = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView customerPicImageView = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView outletPicImageView = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView ownerPicImageView = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView GstPicImageView = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView AdharPicImageView = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout userPic_layout = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutTag = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutTagBranch = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutCustomerDetails = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout customizeLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextCustomerName = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextAddress = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextPin = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextPhone = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextLandPhone = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText custemail = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText ed_firmname = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText ed_propname = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText ed_GstNumber = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText ed_AdharNumber = null;

    static final int REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG = 3;
    static final int REQUEST_IMAGE_CAPTURE = 1;
    static final int REQUEST_IMAGE_PICK = 2;
    private static String mPhoneNo = "";
    private static String currentPic = "";
    String supportingAttachmentNameForOutletImage = "";
    String supportingAttachmentNameForOwnerImage = "";
    String supportingAttachmentNameForGstImage = "";
    String supportingAttachmentNameForAdharImage = "";
    String supportingAttachmentNameForCustomerImage = "";
    @SuppressLint("SimpleDateFormat")
    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
    String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
    String imagePath = "";
    Bitmap customerPicBitmap = null;
    Bitmap customerPicBitmapForLocation = null;
    NewCustomerAdapter mNewCustomerAdapter;
    MultipleDistributorSelectionAdapter mNewCustomerAdapterMultipleDistributor;
    MultipleBranchSelectionAdapter MultipleBranchSelectionAdapterObject;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<CustomerDetails> mRoutWiseCustomerList;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    CustomerDetails newCustObj;
    Context mContext;


    private String mCustomerName = "";
    private String mAddress = "";
    private String mPinNo = "";
    private String mMobileNo = "";
    private String mRouteCode = "";
    private String mRouteName = "";
    private String mTagcustomerCode = "";
    private String mTagBranchCode = "";
    private String mOwnerName = "";
    private String mOwnerPhone = "";
    private String mCustomerClass = "";
    private String mClosedOn = "";
    private String custType = "R";
    private String CategoryOfStore = "";
    private String inStoreActivity = "";
    private String mCoverageType = "";
    private String mTIN = "";
    private String mPAN = "";
    private String meMail = "";
    private String firmname = "";
    private String GstNumber = "";
    private String AdharNumber = "";
    private String mTradeType = "";
    private boolean isMobilePhoneRepeating = false;
    private boolean isNewRoute = false;
    private boolean isOtherInfo = true;
    private boolean isUserPicTaken = true;
    private boolean isCameralaunched = false;
    LocationTracker LocationTrackerObject;

    List<String> listCLosingDay = new ArrayList<>();
    List<String> listCustType = new ArrayList<>();

    Spinner spinCustomerType;
    Spinner spinClosingDay;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_add_cust);
        RegisterActivities.registerActivity(this);
        newCustObj = new CustomerDetails();
        newCustObj.setcustomerImage("");
        newCustObj.setoutletImage("");
        newCustObj.setownerImage("");
        newCustObj.setgstImage("");
        newCustObj.setadharImage("");
        mContext = AddNewCustActivity.this;
        imagePath = Utils.getAppStoragePath(mContext) + imageName;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        LocationTrackerObject = new LocationTracker(mContext, "add new customer");

        listCLosingDay.add("SELECT");
        listCLosingDay.add("NONE");
        listCLosingDay.add("SUNDAY");
        listCLosingDay.add("MONDAY");
        listCLosingDay.add("TUESDAY");
        listCLosingDay.add("WEDNESDAY");
        listCLosingDay.add("THURSDAY");
        listCLosingDay.add("FRIDAY");
        listCLosingDay.add("SATURDAY");

        listCustType.add("SELECT");
        listCustType.add("Distributor");
        listCustType.add("Retailer");
        listCustType.add("Dealer");
        listCustType.add("Project");
        listCustType.add("Others");

        InitializeView();

        if (isOrederToNewCustomer) {
            mRouteName = getIntent().getStringExtra("ROUTENAME");
            mRouteCode = getIntent().getStringExtra("ROUTECODE");
            Constants.oderToCustomerRoute = new RoutePlanMasterDetails();
            Constants.oderToCustomerRoute.setRoutecode(mRouteCode);
            Constants.oderToCustomerRoute.setRouteName(mRouteName);
            if (Constants.orderFormDetailsObj.getTagDistributor().equalsIgnoreCase("yes")) {
                CustomerDetails obj = mAceDnsDatabase.getCustomerListByRouteForTagDistributor(mRouteCode);
                mTagcustomerCode = obj.getCustomerCode().trim();
                if (!mTagcustomerCode.isEmpty() && !Constants.orderFormDetailsObj.getmultiple_distributor().equalsIgnoreCase("yes")) {
                    mButtonTagDistributor.setText(obj.getCustomerName());
                    mButtonTagDistributor.setEnabled(false);
                }
            }
            isNewRoute = false;
            mButtonSelectRoute.setText(mRouteName);
            mButtonSelectRoute.setEnabled(false);
        }
        if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("yes")) {
            ShowOTPDialog();
        }
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        spinCustomerType = findViewById(R.id.spinCustomerType);
        spinClosingDay = findViewById(R.id.spinClosingDay);

        ArrayAdapter<String> dataAdapterlistCLosingDay = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, listCLosingDay);
        ArrayAdapter<String> dataAdapterlistCustType = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, listCustType);

        dataAdapterlistCLosingDay.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        dataAdapterlistCustType.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

        spinCustomerType.setAdapter(dataAdapterlistCustType);
        spinClosingDay.setAdapter(dataAdapterlistCLosingDay);

        spinCustomerType.setOnItemSelectedListener(new OnItemSelectedListener() {
            public void onItemSelected(AdapterView<?> arg0, View view1, int pos, long id) {
                custType = (String) spinCustomerType.getItemAtPosition(pos);
            }

            public void onNothingSelected(AdapterView<?> arg0) {
            }
        });
        spinClosingDay.setOnItemSelectedListener(new OnItemSelectedListener() {
            public void onItemSelected(AdapterView<?> arg0, View view1, int pos, long id) {
                mClosedOn = (String) spinClosingDay.getItemAtPosition(pos);
            }

            public void onNothingSelected(AdapterView<?> arg0) {
            }
        });

        customizeLayout = findViewById(R.id.customizeLayout);
        mImageViewLogo = findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewLogo.setVisibility(View.VISIBLE);
            mImageViewLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        mEditTextCustomerName = findViewById(R.id.ed_name);
        mEditTextCustomerName.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        mEditTextCustomerName.setInputType(InputType.TYPE_TEXT_FLAG_CAP_CHARACTERS);
        mEditTextAddress = findViewById(R.id.ed_address);
        mEditTextAddress.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        mEditTextAddress.setInputType(InputType.TYPE_TEXT_FLAG_CAP_CHARACTERS);
        mEditTextPin = findViewById(R.id.ed_pin);
        mEditTextPin.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        mEditTextPhone = findViewById(R.id.ed_phone);
        mEditTextPhone.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        custemail = findViewById(R.id.custemail);
        ed_firmname = findViewById(R.id.ed_firmname);
        ed_propname = findViewById(R.id.ed_propname);
        ed_GstNumber = findViewById(R.id.ed_GstNumber);
        ed_AdharNumber = findViewById(R.id.ed_AdharNumber);
        mEditTextLandPhone = findViewById(R.id.ed_land_phone);
        mEditTextLandPhone.setImeOptions(EditorInfo.IME_ACTION_DONE);
        mButtonSelectRoute = findViewById(R.id.buttonRoute);
        mButtonTagDistributor = findViewById(R.id.buttonTag);
        mButtonTagBranch = findViewById(R.id.buttonTagBranch);
        mButtonOtherInformation = findViewById(R.id.buttonOtherInformation);
        buttonAddPicFromGallery = findViewById(R.id.buttonAddPicFromGallery);
        buttonAddPicFromCamera = findViewById(R.id.buttonAddPicFromCamera);
        buttonAddPicOutlet = findViewById(R.id.buttonAddPicOutlet);
        buttonAddPicOwner = findViewById(R.id.buttonAddPicOwner);
        buttonAddPicGst = findViewById(R.id.buttonAddPicGst);
        buttonAddPicAdhar = findViewById(R.id.buttonAddPicAdhar);
        customerPicImageView = findViewById(R.id.customerPicImageView);
        outletPicImageView = findViewById(R.id.outletPicImageView);
        ownerPicImageView = findViewById(R.id.ownerPicImageView);
        GstPicImageView = findViewById(R.id.GstPicImageView);
        AdharPicImageView = findViewById(R.id.AdharPicImageView);
        userPic_layout = findViewById(R.id.userPic_layout);
        mLinearLayoutTag = findViewById(R.id.tag_layout);
        mLinearLayoutTagBranch = findViewById(R.id.tag_branch_layout);
        if (!Constants.orderFormDetailsObj.getTagDistributor().equalsIgnoreCase("yes")) {
            mLinearLayoutTag.setVisibility(View.GONE);
        }

        if (Constants.productDetailsObj.getBranchWiseMRP().equalsIgnoreCase("yes") || Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")) {
            mLinearLayoutTagBranch.setVisibility(View.VISIBLE);
        }
        if (Constants.orderFormDetailsObj.getAddCustomerDetails().equalsIgnoreCase("customize")) {
            customizeLayout.setVisibility(View.VISIBLE);
        } else {
            customizeLayout.setVisibility(View.GONE);
        }

        mLinearLayoutCustomerDetails = findViewById(R.id.otherinfo_layout);
        if (!Constants.orderFormDetailsObj.getAddCustomerDetails().equalsIgnoreCase("yes")) {
            mLinearLayoutCustomerDetails.setVisibility(View.GONE);
            isOtherInfo = true;
        } else {
            isOtherInfo = false;
        }

        mButtonBack = findViewById(R.id.back);
        mButtonSubmit = findViewById(R.id.btn_next);
        mButtonBack.setOnClickListener(this);
        mButtonSubmit.setOnClickListener(this);
        mButtonSelectRoute.setOnClickListener(this);
        mButtonTagDistributor.setOnClickListener(this);
        mButtonTagBranch.setOnClickListener(this);
        mButtonOtherInformation.setOnClickListener(this);
        buttonAddPicFromGallery.setOnClickListener(this);
        buttonAddPicFromCamera.setOnClickListener(this);
        buttonAddPicOutlet.setOnClickListener(this);
        buttonAddPicOwner.setOnClickListener(this);
        buttonAddPicGst.setOnClickListener(this);
        buttonAddPicAdhar.setOnClickListener(this);

        if (!Constants.orderFormDetailsObj.getAddCustomerTradeNonTrade().trim().isEmpty()) {
            if (Constants.orderFormDetailsObj.getAddCustomerTradeNonTrade().trim().equalsIgnoreCase("both")) {
                mLinearLayoutTag.setVisibility(View.GONE);
                ShowConditionofTradeNonTrade();
            }
        }

        if (Constants.orderFormDetailsObj.getAddCustomImage().equalsIgnoreCase("yes")) {
            LinearLayout addpic_layout = findViewById(R.id.addpic_layout);
            addpic_layout.setVisibility(View.VISIBLE);
        }

        mEditTextCustomerName.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                String cname = mEditTextCustomerName.getText().toString().trim();
                String nn = mAceDnsDatabase.getAddNewCustomerName(mRouteCode, mEditTextCustomerName.getText().toString().trim());
                if (nn.toLowerCase().matches(cname.toLowerCase())) {
                    Utils.showToast(mContext, nn + " - Exists");
                } else {
                    mButtonSubmit.setEnabled(true);
                }
            }
        });
    }

    @SuppressLint("SimpleDateFormat")
    @Override
    public void onClick(View v) {
        if (v == mButtonBack) {
            finish();
        } else if (v == mButtonSelectRoute) {
            ShowRouteListDialog();
        } else if (v == mButtonOtherInformation) {
            ShowCustomerDetailsDialog();
        } else if (v == buttonAddPicFromGallery) {
            Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
            intent.setType("image/*");
            startActivityForResult(intent, REQUEST_IMAGE_PICK);
        } else if (v == buttonAddPicFromCamera) {
            Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE);
            }
        } else if (v == buttonAddPicOutlet) {
            Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                currentPic = "outlet";
                startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE);
            }
        } else if (v == buttonAddPicOwner) {
            Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                currentPic = "owner";
                startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE);
            }
        } else if (v == buttonAddPicGst) {
            Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                currentPic = "gst";
                startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE);
            }
        } else if (v == buttonAddPicAdhar) {
            Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                currentPic = "adhar";
                startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE);
            }
        } else if (v == mButtonTagDistributor) {
            if (!mRouteCode.isEmpty()) {
                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRDSForTagDistributor(mRouteCode);
                if (!mCustomerDetailsList.isEmpty()) {
                    if (Constants.orderFormDetailsObj.getmultiple_distributor().equalsIgnoreCase("yes")) {
                        mNewCustomerAdapterMultipleDistributor = new MultipleDistributorSelectionAdapter(mContext, R.layout.multiple_route_child, mCustomerDetailsList);
                    } else {
                        mNewCustomerAdapter = new NewCustomerAdapter(AddNewCustActivity.this, R.layout.customer_list_child, mCustomerDetailsList);
                    }
                    ShowChooseCustomerDialog();
                } else {
                    Utils.showToast(mContext, "No distributor found on chosen route.\n Please contact admin.");
                }
            } else {
                Utils.showToast(AddNewCustActivity.this, "Please select route");
            }
        } else if (v == mButtonTagBranch) {
            ArrayList<BranchMasterDetails> branchList = mAceDnsDatabase.getBranchList();
            if (!branchList.isEmpty()) {
                MultipleBranchSelectionAdapterObject = new MultipleBranchSelectionAdapter(mContext, R.layout.multiple_route_child, branchList);
                ShowChooseTaggedBranchDialog();
            } else {
                Utils.showToast(mContext, "No branch found.\n Please contact admin.");
            }
        } else if (v == mButtonSubmit) {
            if (Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") && !isGettingCurrentLocation) {
                Utils.showToast(mContext, "Could not determine your location. Please Check Location Settings");
            } else {
                boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
                if (isTimeAutomatic) {
                    String customerName = mEditTextCustomerName.getText().toString().trim();
                    boolean isNameOK;
                    if (!customerName.isEmpty()) {
                        if (customerName.length() == 1 && mEditTextCustomerName.getText().toString().equalsIgnoreCase(".")) {
                            isNameOK = false;
                        } else {
                            mCustomerName = getFormatString(customerName.toUpperCase());
                            isNameOK = true;
                        }
                    } else {
                        isNameOK = false;
                    }
                    String customerAddress = mEditTextAddress.getText().toString().trim();
                    boolean isAddressOK;
                    if (!customerAddress.isEmpty()) {
                        if (customerAddress.length() == 1 && customerAddress.equalsIgnoreCase(".")) {
                            isAddressOK = false;
                        } else {
                            mAddress = getFormatString(customerAddress.toUpperCase());
                            isAddressOK = true;
                        }
                    } else {
                        isAddressOK = false;
                    }
                    boolean isPinOK;
                    if (!mEditTextPin.getText().toString().isEmpty()) {
                        if (mEditTextPin.getText().toString().length() == 6 && Integer.parseInt(mEditTextPin.getText().toString()) != 0 && !mEditTextPin.getText().toString().startsWith("0")) {
                            mPinNo = mEditTextPin.getText().toString();
                            isPinOK = true;
                        } else {
                            isPinOK = false;
                        }
                    } else {
                        isPinOK = false;
                    }
                    boolean isTagCustomerOK;
                    if (Constants.orderFormDetailsObj.getTagDistributor().equalsIgnoreCase("yes")) {
                        if (!Constants.orderFormDetailsObj.getAddCustomerTradeNonTrade().trim().isEmpty()) {
                            String type = Constants.orderFormDetailsObj.getAddCustomerTradeNonTrade().trim();
                            if (type.equalsIgnoreCase("both")) {
                                if (mTradeType.equalsIgnoreCase("Trade")) {
                                    isTagCustomerOK = !mTagcustomerCode.isEmpty();
                                } else {
                                    mTagcustomerCode = "";
                                    isTagCustomerOK = true;
                                }
                            } else {
                                isTagCustomerOK = !mTagcustomerCode.isEmpty();
                            }
                        } else {
                            isTagCustomerOK = !mTagcustomerCode.isEmpty();
                        }
                    } else {
                        isTagCustomerOK = true;
                    }
                    boolean isTagBranchOK;
                    if (Constants.productDetailsObj.getBranchWiseMRP().equalsIgnoreCase("yes") || Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")) {
                        isTagBranchOK = !mTagBranchCode.isEmpty();
                    } else {
                        isTagBranchOK = true;
                    }

                    boolean isRouteOK = !mRouteCode.isEmpty();

                    String mLandLineNo = mEditTextLandPhone.getText().toString();

                    String inputPhoneNumber = mEditTextPhone.getText().toString();
                    boolean isMobilePhoneOK;
                    if (Utils.isValidIndianMobile(inputPhoneNumber)) {
                        mMobileNo = inputPhoneNumber;
                        isMobilePhoneOK = true;
                        isMobilePhoneRepeating = mAceDnsDatabase.NotRepeatativeCustomerPhone(inputPhoneNumber);
                    } else {
                        isMobilePhoneOK = false;
                    }

                    if (isNameOK && isAddressOK && isPinOK && isRouteOK && isMobilePhoneOK && !isMobilePhoneRepeating && isTagCustomerOK && isUserPicTaken && isOtherInfo && isTagBranchOK) {
                        if (Constants.orderFormDetailsObj.getAddCustomerDetails().equalsIgnoreCase("customize")) {
                            meMail = custemail.getText().toString();
                            firmname = ed_firmname.getText().toString();
                            mOwnerName = ed_propname.getText().toString();
                            GstNumber = ed_GstNumber.getText().toString();
                            mTIN = GstNumber;
                            AdharNumber = ed_AdharNumber.getText().toString();
                        }
                        String propname = "";
                        if (isNewRoute) {
                            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                            newCustObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                            newCustObj.setCustomerCode("N" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                            newCustObj.setCustomerName(mCustomerName.toUpperCase());
                            newCustObj.setAddress(mAddress.toUpperCase());

                            if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("yes")) {
                                newCustObj.setNumber(mMobileNo + "#" + Constants.mOTP);
                            } else {
                                newCustObj.setNumber(mMobileNo);
                            }
                            newCustObj.setPin(mPinNo);
                            newCustObj.setNewRouteouteCode("NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                            newCustObj.setNewRouteName(mRouteName.toUpperCase());
                            newCustObj.setFlag("0");
                            newCustObj.setCheckFlag("1");

                            if (custType.equalsIgnoreCase("select")) {
                                custType = "R";
                            }
                            if (mClosedOn.equalsIgnoreCase("select")) {
                                mClosedOn = "";
                            }
                            newCustObj.setCustomerType(custType);
                            newCustObj.setRdsTag(mTagcustomerCode);
                            newCustObj.setBranchCode(mTagBranchCode);
                            newCustObj.setLandlineNo(mLandLineNo);
                            newCustObj.setOwnerName(mOwnerName.toUpperCase());
                            newCustObj.setOwnerPhone(mOwnerPhone);
                            newCustObj.setCustClass(mCustomerClass.toUpperCase());
                            newCustObj.setWeeklyClosingDay(mClosedOn.toUpperCase());
                            newCustObj.setcategoryOfStore(CategoryOfStore.toUpperCase());
                            newCustObj.setinStoreActivityPossible(inStoreActivity.toUpperCase());
                            newCustObj.setCoverageType(mCoverageType.toUpperCase());
                            newCustObj.setTIN(mTIN.toUpperCase());
                            newCustObj.setPAN(mPAN.toUpperCase());
                            newCustObj.setEmail(meMail);

                            newCustObj.setbase_latt("");
                            newCustObj.setbase_longi("");
                            newCustObj.setfirmName(firmname);
                            newCustObj.setpropName(propname);
                            newCustObj.setgstNo(GstNumber);
                            newCustObj.setadharNo(AdharNumber);

                            newCustObj.setImage("");
                            setCheckInCustomerData(newCustObj);
                            if (Constants.menuDetailsObj.getgeo_fencing_menu().contains("add_customer")) {
                                if (!isGettingCurrentLocation) {
                                    Utils.showToast(mContext, "Could not determine your location. Please Check Location Settings");
                                } else {
                                    launchCameraToTakeCustomerPictureThenSubmit();
                                }
                            } else {
                                localDbInsertServerSendProcess(timeStamp);
                            }
                        } else {
                            mRoutWiseCustomerList = mAceDnsDatabase.getCustomerListByRoute(mRouteCode);
                            if (CheckVAlidationonCustomer(mCustomerName)) {
                                Utils.showToast(mContext, "Customer name already exist in this route.\n Please provide another Customer Name");
                            } else {
                                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                newCustObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                                newCustObj.setCustomerCode("N" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                                newCustObj.setCustomerName(mCustomerName.toUpperCase());
                                newCustObj.setAddress(mAddress.toUpperCase());
                                if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("yes")) {
                                    newCustObj.setNumber(mMobileNo + "#" + Constants.mOTP);
                                } else {
                                    newCustObj.setNumber(mMobileNo);
                                }
                                newCustObj.setPin(mPinNo);
                                newCustObj.setNewRouteouteCode(mRouteCode);
                                newCustObj.setNewRouteName(mRouteName);
                                newCustObj.setFlag("0");
                                newCustObj.setCheckFlag("1");
                                newCustObj.setCustomerType("R");
                                newCustObj.setRdsTag(mTagcustomerCode);
                                newCustObj.setLandlineNo(mLandLineNo);
                                newCustObj.setOwnerName(mOwnerName.toUpperCase());
                                newCustObj.setOwnerPhone(mOwnerPhone);
                                newCustObj.setCustClass(mCustomerClass.toUpperCase());
                                newCustObj.setWeeklyClosingDay(mClosedOn.toUpperCase());
                                newCustObj.setCoverageType(mCoverageType.toUpperCase());
                                newCustObj.setcategoryOfStore(CategoryOfStore.toUpperCase());
                                newCustObj.setinStoreActivityPossible(inStoreActivity.toUpperCase());
                                newCustObj.setBranchCode(mTagBranchCode);
                                newCustObj.setTIN(mTIN.toUpperCase());
                                newCustObj.setPAN(mPAN.toUpperCase());
                                newCustObj.setEmail(meMail);
                                newCustObj.setbase_latt("");
                                newCustObj.setbase_longi("");

                                newCustObj.setfirmName(firmname);
                                newCustObj.setpropName(propname);
                                newCustObj.setgstNo(GstNumber);
                                newCustObj.setadharNo(AdharNumber);
                                newCustObj.setImage("");
                                setCheckInCustomerData(newCustObj);
                                if (Constants.menuDetailsObj.getgeo_fencing_menu().contains("add_customer")) {
                                    if (!isGettingCurrentLocation) {
                                        Utils.showToast(mContext, "Could not determine your location. Please Check Location Settings");
                                    } else {
                                        launchCameraToTakeCustomerPictureThenSubmit();
                                    }
                                } else {
                                    localDbInsertServerSendProcess(timeStamp);
                                }
                            }
                        }
                    } else {
                        if (!isNameOK) {
                            Utils.showToast(AddNewCustActivity.this, "Please provide a valid customer name");
                        } else if (!isAddressOK) {
                            Utils.showToast(AddNewCustActivity.this, "Please provide a valid address");
                        } else if (!isPinOK) {
                            Utils.showToast(AddNewCustActivity.this, "Please provide a valid pin");
                        } else if (!isRouteOK) {
                            Utils.showToast(AddNewCustActivity.this, "Please select route");
                        } else if (!isMobilePhoneOK) {
                            Utils.showToast(AddNewCustActivity.this, "Please provide a valid mobile number");
                        } else if (isMobilePhoneRepeating) {
                            String cname = mAceDnsDatabase.getCustomerByMobile(mEditTextPhone.getText().toString());
                            Utils.showToast(AddNewCustActivity.this, "Duplicate number associated with " + cname + " retailer.");
                        } else if (!isTagCustomerOK) {
                            Utils.showToast(AddNewCustActivity.this, "Please select tag distributor");
                        } else if (!isTagBranchOK) {
                            Utils.showToast(AddNewCustActivity.this, "Please select branch");
                        } else if (!isOtherInfo) {
                            Utils.showToast(AddNewCustActivity.this, "Please provide other information");
                        } else {
                            Utils.showToast(AddNewCustActivity.this, "Please provide valid inputs");
                        }
                    }
                } else {
                    Utils.showSettingsAlertToChangeTimeZone(mContext);
                }
            }
        }
    }

    private void launchCameraToTakeCustomerPictureThenSubmit() {
        Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
            isCameralaunched = true;
            startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG);
        }
    }

    public void localDbInsertServerSendProcess(String timeStamp) {
        if (Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes")) {
            if (newCustObj.getcategoryOfStore().isEmpty() && newCustObj.getCustClass().isEmpty()) {
                Utils.showToast(mContext, "Select Customer Class and Store Category");
            } else {
                String cname = mAceDnsDatabase.getCustomerByMobile(mEditTextPhone.getText().toString());
                if (!cname.matches("no")) {
                    Utils.showToast(AddNewCustActivity.this, "Duplicate number associated with " + cname + " retailer.");
                    return;
                } else {
                    newCustObj.setCustomerCode("N" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                    mAceDnsTransactionDatabase.insertToLocationTable("N", timeStamp);
                    mAceDnsTransactionDatabase.insertToCustomerMaster(newCustObj, true);
                }
                if (isNewRoute) {
                    RouteDetails routeObj = new RouteDetails();
                    newCustObj.setNewRouteouteCode("NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                    routeObj.setRouteCode("NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                    routeObj.setRouteName(mRouteName.toUpperCase());
                    ArrayList<RouteDetails> routeList = new ArrayList<>();
                    routeList.add(routeObj);
                    mAceDnsDatabase.insertToRouteMaster(routeList);
                }
                isCameralaunched = false;
                if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("yes")) {
                    ShowOTPDialog(mPhoneNo);
                } else {
                    sendCustomerDataToServer();
                }
            }
        } else {
            String cname = mAceDnsDatabase.getCustomerByMobile(mEditTextPhone.getText().toString());
            if (!cname.matches("no")) {
                Utils.showToast(AddNewCustActivity.this, "Duplicate number associated with " + cname + " retailer.");
                return;
            } else {
                newCustObj.setCustomerCode("N" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                mAceDnsTransactionDatabase.insertToLocationTable("N", timeStamp);
                mAceDnsTransactionDatabase.insertToCustomerMaster(newCustObj, true);
            }
            if (isNewRoute) {
                RouteDetails routeObj = new RouteDetails();
                newCustObj.setNewRouteouteCode("NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                routeObj.setRouteCode("NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                routeObj.setRouteName(mRouteName.toUpperCase());
                ArrayList<RouteDetails> routeList = new ArrayList<>();
                routeList.add(routeObj);
                mAceDnsDatabase.insertToRouteMaster(routeList);
            }
            isCameralaunched = false;
            if (Constants.orderFormDetailsObj.getNewCustomerOtp().equalsIgnoreCase("yes")) {
                ShowOTPDialog(mPhoneNo);
            } else {
                sendCustomerDataToServer();
            }
        }
    }

    public void setCheckInCustomerData(CustomerDetails newCustObj) {
        if (Constants.isOrederToNewCustomer && isCheckInToNewCustomer) {
            Constants.selectedCheckINCustomer = newCustObj;
        }
    }

    public void sendCustomerDataToServer() {
        if (isOrederToNewCustomer && isCheckInToNewCustomer) {
            setCustomerCheckInDataInPreferences(mContext, mRouteCode);
        }
        if (!supportingAttachmentNameForCustomerImage.matches("")) {
            new TRANS_TourAttachmentExportTask(mContext, "add_customer", "", false, false).execute();
        }
        if (!supportingAttachmentNameForOutletImage.matches("")) {
            new TRANS_TourAttachmentExportTask(mContext, "add_outlet", "", false, false).execute();
        }
        if (!supportingAttachmentNameForOwnerImage.matches("")) {
            new TRANS_TourAttachmentExportTask(mContext, "add_owner", "", false, false).execute();
        }
        if (!supportingAttachmentNameForGstImage.matches("")) {
            new TRANS_TourAttachmentExportTask(mContext, "add_gst", "", false, false).execute();
        }
        if (!supportingAttachmentNameForAdharImage.matches("")) {
            new TRANS_TourAttachmentExportTask(mContext, "add_adhar", "", false, false).execute();
        }
        new TRANS_SubmitNewCustomerDetailsTask(AddNewCustActivity.this, true, false, "SYNC").execute();
    }

    @SuppressLint("SetTextI18n")
    public void ShowConditionofTradeNonTrade() {
        final Dialog dialgoCondition = new Dialog(AddNewCustActivity.this, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
        Objects.requireNonNull(dialgoCondition.getWindow()).setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));

        TextView txtMsg = dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select an Option.");

        final RadioGroup radioSelectionGroup = dialgoCondition.findViewById(R.id.radioSelect);

        radioSelectionGroup.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection = dialgoCondition.findViewById(checkedId);
            if (radioSelection.getText().toString().trim().equalsIgnoreCase("trade")) {
                mTradeType = "Trade";
                if (Constants.orderFormDetailsObj.getTagDistributor().equalsIgnoreCase("yes")) {
                    mLinearLayoutTag.setVisibility(View.VISIBLE);
                }
            } else {
                mTradeType = "Non Trade";
                if (Constants.orderFormDetailsObj.getTagDistributor().equalsIgnoreCase("yes")) {
                    mLinearLayoutTag.setVisibility(View.GONE);
                }
            }
            dialgoCondition.cancel();
        });
        dialgoCondition.show();
    }

    public void onDestroy() {
        super.onDestroy();
        Constants.mOTP = null;
    }

    @SuppressLint("SetTextI18n")
    public void ShowOTPDialog(final String getMobileNO) {
        final Dialog mDialogOTPName = new Dialog(mContext, R.style.PauseDialog);
        mDialogOTPName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOTPName.setContentView(R.layout.dialog_otp_verification);
        mDialogOTPName.setCancelable(false);
        final EditText edvalue = mDialogOTPName.findViewById(R.id.editTextOTP);

        TextView title2OBJ = mDialogOTPName.findViewById(R.id.title2);
        title2OBJ.setText("for ID " + Constants.OTP_ID);

        Button btn_RESEND = mDialogOTPName.findViewById(R.id.btn_submit);
        btn_RESEND.setText("Re Send");
        btn_RESEND.setVisibility(View.VISIBLE);

        Button submit = mDialogOTPName.findViewById(R.id.btn_cancel);
        submit.setVisibility(View.VISIBLE);
        submit.setText("Submit");
        submit.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            String value = edvalue.getText().toString().trim();
            if (value.length() == 4) {
                if (!value.equals(Constants.mOTP)) {
                    Utils.showToast(AddNewCustActivity.this, "Invalid OTP");
                } else {
                    ConnectionDetector cd = new ConnectionDetector(AddNewCustActivity.this);
                    boolean isInternetPresent = cd.isConnectingToInternet();
                    if (isInternetPresent) {
                        sendCustomerDataToServer();
                    } else {
                        Utils.showToast(AddNewCustActivity.this, "No Internet Connection");
                    }
                }
            } else {
                Utils.showToast(AddNewCustActivity.this, "Provide 4 digit OTP");
            }
        });

        btn_RESEND.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            commonSendOTP(getMobileNO, mDialogOTPName, "FALSE");
        });
        mDialogOTPName.show();
    }

    public boolean CheckVAlidationonCustomer(String customername) {
        boolean isCustomerpresent = false;
        for (int count = 0; count < mRoutWiseCustomerList.size(); count++) {
            if (customername.equalsIgnoreCase(mRoutWiseCustomerList.get(count).getCustomerName())) {
                isCustomerpresent = true;
                break;
            }
        }
        return isCustomerpresent;
    }

    public String getFormatString(String unformatString) {
        String formatString;
        StringBuilder res = new StringBuilder();

        String[] strArr = unformatString.split(" ");
        for (String str : strArr) {
            char[] stringArray = str.trim().toCharArray();
            for (int ii = 0; ii < stringArray.length; ii++) {
                if (ii == 0) {
                    stringArray[ii] = Character.toUpperCase(stringArray[ii]);
                } else {
                    stringArray[ii] = Character.toLowerCase(stringArray[ii]);
                }
            }
            str = new String(stringArray);
            res.append(str).append(" ");
        }
        formatString = res.toString().trim();
        return formatString;
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialog() {
        final ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteListTOAddNewCustomer();
        final Dialog routeDialog = new Dialog(AddNewCustActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(true);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        final RouteAdapter adapter = new RouteAdapter(AddNewCustActivity.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routeDialog.cancel();
            isNewRoute = false;
            RouteDetails Obj = routeList.get(arg2);
            mRouteCode = Obj.getRouteCode();
            mRouteName = Obj.getRouteName();
            mButtonSelectRoute.setText(mRouteName);
            mButtonTagDistributor.setText("Select Distributor");
            mTagcustomerCode = "";
            String cname = mEditTextCustomerName.getText().toString().trim();
            String nn = mAceDnsDatabase.getAddNewCustomerName(mRouteCode, mEditTextCustomerName.getText().toString().trim());
            if (nn.toLowerCase().matches(cname.toLowerCase())) {
                Utils.showToast(mContext, nn + " - Exists");
            } else {
                mButtonSubmit.setEnabled(true);
            }
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route = routeDialog.findViewById(R.id.create_route);
        if (Constants.orderFormDetailsObj.getAddCustomerRouteCreation().equalsIgnoreCase("yes")) {
            create_route.setVisibility(View.VISIBLE);
        } else {
            create_route.setVisibility(View.GONE);
        }
        create_route.setOnClickListener(arg0 -> {
            ShowCreateRouteDialog(routeList);
            routeDialog.cancel();
        });
        routeDialog.show();
    }

    @SuppressLint({"SimpleDateFormat", "SetTextI18n"})
    public void ShowCreateRouteDialog(final ArrayList<RouteDetails> routelist) {
        final Dialog createRouteDialog = new Dialog(mContext);
        createRouteDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        createRouteDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = createRouteDialog.findViewById(R.id.title);
        title.setText("Provide the name of the new Route.");
        final EditText edInst = createRouteDialog.findViewById(R.id.ed_input);
        final Button submit = createRouteDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            String name;
            boolean isExist = false;
            name = edInst.getText().toString().trim();
            if (!edInst.getText().toString().trim().isEmpty()) {
                if (!name.isEmpty()) {
                    for (int count = 0; count < routelist.size(); count++) {
                        if (routelist.get(count).getRouteName().equalsIgnoreCase(name)) {
                            isExist = true;
                            break;
                        }
                    }
                    if (!isExist) {
                        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        mRouteCode = "NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                        mRouteName = name;
                        createRouteDialog.cancel();
                        mButtonSelectRoute.setText(mRouteName);
                        isNewRoute = true;
                    } else {
                        Toast.makeText(mContext, "Route name already exist. \n Please provide new route name", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Utils.showToast(mContext, "Name cannot be left blank");
                }
            } else {
                Utils.showToast(mContext, "Please provide valid input");
            }
        });
        createRouteDialog.show();
    }

    public void ShowOTPDialog() {
        final Dialog OTPDialog = new Dialog(AddNewCustActivity.this, R.style.PauseDialog);
        OTPDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        OTPDialog.setContentView(R.layout.activity_phoneno);
        OTPDialog.setCancelable(false);

        final EditText editTextPhone = OTPDialog.findViewById(R.id.ed_name);
        editTextPhone.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);

        Button buttonSubmit = OTPDialog.findViewById(R.id.btn);
        buttonSubmit.setOnClickListener(v -> {
            String inputPhoneNumber = editTextPhone.getText().toString();
            if (Utils.isValidIndianMobile(inputPhoneNumber)) {
                mPhoneNo = inputPhoneNumber;
                mEditTextPhone.setText(mPhoneNo);
                mEditTextPhone.setEnabled(false);
                commonSendOTP(mPhoneNo, OTPDialog, "TRUE");
            } else {
                Utils.showToast(AddNewCustActivity.this, "Please provide 10 digit Phone Number");
            }
        });

        Button buttonCancel = OTPDialog.findViewById(R.id.back);
        buttonCancel.setOnClickListener(v -> OTPDialog.dismiss());
        OTPDialog.show();
    }

    public void commonSendOTP(final String mPhoneNo, final Dialog OTPDialog, final String getMessage) {
        String parse_URL = BaseUrl.baseUrl + AceDnsWebServiceURL.add_customer_OTP;
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ AddNewCustActivity: " + parse_URL);
        StringRequest stringRequest = new StringRequest(Request.Method.POST, parse_URL,
                response -> {
                    String[] str_Response = response.split("#");
                    Constants.OTP_ID = str_Response[0];
                    Constants.mOTP = str_Response[1];
                    System.out.println("OTP" + Constants.mOTP);
                    if (getMessage.equals("TRUE")) {
                        OTPDialog.dismiss();
                    }
                },
                error -> Toast.makeText(AddNewCustActivity.this, error.toString(), Toast.LENGTH_LONG).show()) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("nick_name", Constants.nickName);
                params.put("mobile_no", mPhoneNo);
                return params;
            }
        };

        RequestQueue requestQueue = Volley.newRequestQueue(AddNewCustActivity.this);
        requestQueue.add(stringRequest);
    }

    private void ShowCustomerDetailsDialog() {
        final Dialog customerDetailsDialog = new Dialog(AddNewCustActivity.this, R.style.PauseDialog);
        customerDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerDetailsDialog.setContentView(R.layout.dialog_customer_details);
        customerDetailsDialog.setCancelable(false);

        final EditText editTextOwnerName = customerDetailsDialog.findViewById(R.id.ed_ownername);
        final EditText editTextOwnerPhone = customerDetailsDialog.findViewById(R.id.ed_ownernumber);
        final EditText editTextCoverageType = customerDetailsDialog.findViewById(R.id.ed_coveragetype);
        final EditText editTextTIN = customerDetailsDialog.findViewById(R.id.ed_tin);
        final EditText editTextPAN = customerDetailsDialog.findViewById(R.id.ed_pan);
        final EditText ed_email = customerDetailsDialog.findViewById(R.id.ed_email);
        final Spinner mSpinner = customerDetailsDialog.findViewById(R.id.spincategory);
        final Spinner spincategoryStore = customerDetailsDialog.findViewById(R.id.spincategoryStore);
        final Spinner spinInStoreActivity = customerDetailsDialog.findViewById(R.id.spinInStoreActivity);
        final Spinner spinCustClass = customerDetailsDialog.findViewById(R.id.spinCustClass);

        List<String> list = new ArrayList<>();
        list.add("SELECT");
        list.add("NONE");
        list.add("SUNDAY");
        list.add("MONDAY");
        list.add("TUESDAY");
        list.add("WEDNESDAY");
        list.add("THURSDAY");
        list.add("FRIDAY");
        list.add("SATURDAY");

        List<String> list2 = new ArrayList<>();
        list2.add("SELECT");
        list2.add("COSMETIC STORE");
        list2.add("PHARMACY");
        list2.add("GROCERY STORE");
        list2.add("GENERAL STORE");

        List<String> list3 = new ArrayList<>();
        list3.add("SELECT");
        list3.add("Y");
        list3.add("N");

        List<String> list4 = new ArrayList<>();
        list4.add("SELECT");
        list4.add("A");
        list4.add("B");
        list4.add("C");

        ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, list);
        ArrayAdapter<String> dataAdapter2 = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, list2);
        ArrayAdapter<String> dataAdapter3 = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, list3);
        ArrayAdapter<String> dataAdapter4 = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, list4);
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        dataAdapter2.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        dataAdapter3.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        dataAdapter4.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        mSpinner.setAdapter(dataAdapter);
        spincategoryStore.setAdapter(dataAdapter2);
        spinInStoreActivity.setAdapter(dataAdapter3);
        spinCustClass.setAdapter(dataAdapter4);

        mSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
            public void onItemSelected(AdapterView<?> arg0, View view1, int pos, long id) {
                mClosedOn = (String) mSpinner.getItemAtPosition(pos);
            }

            public void onNothingSelected(AdapterView<?> arg0) {
            }
        });
        spincategoryStore.setOnItemSelectedListener(new OnItemSelectedListener() {
            public void onItemSelected(AdapterView<?> arg0, View view1, int pos, long id) {
                CategoryOfStore = (String) spincategoryStore.getItemAtPosition(pos);
                Log.d("CategoryOfStore", CategoryOfStore);
            }

            public void onNothingSelected(AdapterView<?> arg0) {
            }
        });
        spinInStoreActivity.setOnItemSelectedListener(new OnItemSelectedListener() {
            public void onItemSelected(AdapterView<?> arg0, View view1, int pos, long id) {
                inStoreActivity = (String) spinInStoreActivity.getItemAtPosition(pos);
                Log.d("inStoreActivity", inStoreActivity);
            }

            public void onNothingSelected(AdapterView<?> arg0) {
            }
        });
        spinCustClass.setOnItemSelectedListener(new OnItemSelectedListener() {
            public void onItemSelected(AdapterView<?> arg0, View view1, int pos, long id) {
                mCustomerClass = (String) spinCustClass.getItemAtPosition(pos);
            }

            public void onNothingSelected(AdapterView<?> arg0) {
            }
        });
        Button back = customerDetailsDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> customerDetailsDialog.dismiss());

        Button save = customerDetailsDialog.findViewById(R.id.buttonSubmit);
        save.setOnClickListener(arg0 -> {
            mOwnerName = editTextOwnerName.getText().toString();
            mOwnerPhone = editTextOwnerPhone.getText().toString();
            mCoverageType = editTextCoverageType.getText().toString();
            mTIN = editTextTIN.getText().toString();
            mPAN = editTextPAN.getText().toString();
            meMail = ed_email.getText().toString();

            boolean isOwnerName = true;
            boolean isOwnerPhone = true;
            boolean isPAN = true;

            if (!mOwnerName.isEmpty()) {
                if (mOwnerName.equalsIgnoreCase(".") || mOwnerName.equalsIgnoreCase("0")) {
                    isOwnerName = false;
                }
            }

            if (mClosedOn.equalsIgnoreCase("SELECT")) {
                mClosedOn = "";
            }
            if (CategoryOfStore.equalsIgnoreCase("SELECT")) {
                CategoryOfStore = "";
            }
            if (inStoreActivity.equalsIgnoreCase("SELECT")) {
                inStoreActivity = "";
            }
            if (mCustomerClass.equalsIgnoreCase("SELECT")) {
                mCustomerClass = "";
            }
            if (!mOwnerPhone.isEmpty()) {
                if (mOwnerPhone.length() != 10) {
                    isOwnerPhone = false;
                }
            }
            if (!mPAN.isEmpty()) {
                if (mPAN.length() != 10) {
                    isPAN = false;
                }
            }
            if (isOwnerName && isOwnerPhone && isPAN) {
                isOtherInfo = true;
                customerDetailsDialog.cancel();
            } else {
                if (!isOwnerName) {
                    Utils.showToast(mContext, "Please provide valid owner name");
                }
                if (!isOwnerPhone) {
                    Utils.showToast(mContext, "Please provide valid phone no");
                }
                if (!isPAN) {
                    Utils.showToast(mContext, "Please provide valid pan no");
                }
            }
        });
        customerDetailsDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowChooseCustomerDialog() {
        final Dialog customerListDialog = new Dialog(AddNewCustActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.select_with_search);
        customerListDialog.setCancelable(false);
        TextView title = customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Distributor");
        EditText searchText = customerListDialog.findViewById(R.id.autoCompleteTextView1);

        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                if (Constants.orderFormDetailsObj.getmultiple_distributor().equalsIgnoreCase("yes")) {
                    mNewCustomerAdapterMultipleDistributor.getFilter().filter(s.toString());
                } else {
                    mNewCustomerAdapter.getFilter().filter(s.toString());
                }
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        final ListView dialogList = customerListDialog.findViewById(R.id.list);
        if (Constants.orderFormDetailsObj.getmultiple_distributor().equalsIgnoreCase("yes")) {
            dialogList.setAdapter(mNewCustomerAdapterMultipleDistributor);
            dialogList.setTextFilterEnabled(true);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        } else {
            dialogList.setAdapter(mNewCustomerAdapter);
        }

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            if (!Constants.orderFormDetailsObj.getmultiple_distributor().equalsIgnoreCase("yes")) {
                CustomerDetails obj = mNewCustomerAdapter.getItem(arg2);
                assert obj != null;
                mButtonTagDistributor.setText(obj.getCustomerName());
                mTagcustomerCode = obj.getCustomerCode();
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                customerListDialog.cancel();
            }
        });
        Button btnCancel = customerListDialog.findViewById(R.id.btn_ok);
        btnCancel.setOnClickListener(v -> customerListDialog.cancel());
        if (Constants.orderFormDetailsObj.getmultiple_distributor().equalsIgnoreCase("yes")) {
            Button btn_addToCart = customerListDialog.findViewById(R.id.btn_addToCart);
            btn_addToCart.setVisibility(View.VISIBLE);
            btn_addToCart.setText("Submit");
            btn_addToCart.setOnClickListener(v -> {
                final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                int checkedItemsCount = checkedItems.size();
                if (checkedItemsCount > 0) {
                    mTagcustomerCode = "";
                    String distName = "";
                    for (int i = 0; i < checkedItemsCount; ++i) {
                        int position = checkedItems.keyAt(i);
                        if (checkedItems.valueAt(i)) {
                            CustomerDetails obj = mNewCustomerAdapterMultipleDistributor.getItem(position);
                            assert obj != null;
                            String CurrentDistName = obj.getCustomerName();
                            String currentDistCode = obj.getCustomerCode();
                            if (mTagcustomerCode.matches("")) {
                                mTagcustomerCode = currentDistCode;
                                distName = CurrentDistName;
                            } else {
                                mTagcustomerCode = MessageFormat.format("{0},{1}", mTagcustomerCode, currentDistCode);
                                distName = MessageFormat.format("{0},{1}", distName, CurrentDistName);
                            }
                        }
                    }
                    mButtonTagDistributor.setText(distName);
                    customerListDialog.cancel();
                } else {
                    Utils.showToast(mContext, "Please select at least 1 distributor");
                }
            });
        }
        customerListDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowChooseTaggedBranchDialog() {
        final Dialog customerListDialog = new Dialog(AddNewCustActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.select_with_search);
        customerListDialog.setCancelable(false);
        TextView title = customerListDialog.findViewById(R.id.title);
        title.setText("Please select Branches");
        EditText searchText = customerListDialog.findViewById(R.id.autoCompleteTextView1);

        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                MultipleBranchSelectionAdapterObject.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        final ListView dialogList = customerListDialog.findViewById(R.id.list);
        dialogList.setAdapter(MultipleBranchSelectionAdapterObject);
        dialogList.setTextFilterEnabled(true);
        dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
        });
        Button btnCancel = customerListDialog.findViewById(R.id.btn_ok);
        btnCancel.setOnClickListener(v -> customerListDialog.cancel());

        Button btn_addToCart = customerListDialog.findViewById(R.id.btn_addToCart);
        btn_addToCart.setVisibility(View.VISIBLE);
        btn_addToCart.setText("Submit");
        btn_addToCart.setOnClickListener(v -> {
            final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
            int checkedItemsCount = checkedItems.size();
            if (checkedItemsCount > 0) {
                mTagBranchCode = "";
                String distName = "";
                for (int i = 0; i < checkedItemsCount; ++i) {
                    int position = checkedItems.keyAt(i);
                    if (checkedItems.valueAt(i)) {
                        BranchMasterDetails obj = MultipleBranchSelectionAdapterObject.getItem(position);
                        assert obj != null;
                        String CurrentDistName = obj.getBranchName();
                        String currentDistCode = obj.getBranchCode();
                        if (mTagBranchCode.matches("")) {
                            mTagBranchCode = currentDistCode;
                            distName = CurrentDistName;
                        } else {
                            mTagBranchCode = MessageFormat.format("{0},{1}", mTagBranchCode, currentDistCode);
                            distName = MessageFormat.format("{0},{1}", distName, CurrentDistName);
                        }
                    }
                }
                mButtonTagBranch.setText(distName);
                customerListDialog.cancel();
            } else {
                Utils.showToast(mContext, "Please select at least 1 branch");
            }
        });
        customerListDialog.show();
    }

    @SuppressLint("SimpleDateFormat")
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == REQUEST_IMAGE_CAPTURE || requestCode == REQUEST_IMAGE_PICK) {
            if (resultCode == RESULT_OK) {
                if (data != null) {
                    if (requestCode == REQUEST_IMAGE_CAPTURE) {
                        try {
                            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                            Bundle extras = data.getExtras();
                            Bitmap outletPicBitmap;
                            Bitmap ownerPicBitmap;
                            Bitmap gstPicBitmap;
                            Bitmap adharPicBitmap;
                            if (currentPic.matches("")) {
                                assert extras != null;
                                customerPicBitmap = (Bitmap) extras.get("data");
                                supportingAttachmentNameForCustomerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                                String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                                storeImageInLocalStorageShowOnImageView(customerPicBitmap, true, imagePath);
                                newCustObj.setcustomerImage(supportingAttachmentNameForCustomerImage);
                                mAceDnsTransactionDatabase.insertToSupportingAttachTable(supportingAttachmentNameForCustomerImage, "add_customer");
                            } else if (currentPic.matches("outlet")) {
                                assert extras != null;
                                outletPicBitmap = (Bitmap) extras.get("data");
                                supportingAttachmentNameForOutletImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                                String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForOutletImage;
                                storeImageInLocalStorageShowOnImageViewCustomize(outletPicBitmap, imagePath, outletPicImageView);
                                newCustObj.setoutletImage(supportingAttachmentNameForOutletImage);
                                mAceDnsTransactionDatabase.insertToSupportingAttachTable(supportingAttachmentNameForOutletImage, "add_outlet");
                            } else if (currentPic.matches("owner")) {
                                assert extras != null;
                                ownerPicBitmap = (Bitmap) extras.get("data");
                                supportingAttachmentNameForOwnerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                                String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForOwnerImage;
                                storeImageInLocalStorageShowOnImageViewCustomize(ownerPicBitmap, imagePath, ownerPicImageView);
                                newCustObj.setownerImage(supportingAttachmentNameForOwnerImage);
                                mAceDnsTransactionDatabase.insertToSupportingAttachTable(supportingAttachmentNameForOwnerImage, "add_owner");
                            } else if (currentPic.matches("gst")) {
                                assert extras != null;
                                gstPicBitmap = (Bitmap) extras.get("data");
                                supportingAttachmentNameForGstImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                                String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForGstImage;
                                storeImageInLocalStorageShowOnImageViewCustomize(gstPicBitmap, imagePath, GstPicImageView);
                                newCustObj.setgstImage(supportingAttachmentNameForGstImage);
                                mAceDnsTransactionDatabase.insertToSupportingAttachTable(supportingAttachmentNameForGstImage, "add_gst");
                            } else if (currentPic.matches("adhar")) {
                                assert extras != null;
                                adharPicBitmap = (Bitmap) extras.get("data");
                                supportingAttachmentNameForAdharImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                                String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForAdharImage;
                                storeImageInLocalStorageShowOnImageViewCustomize(adharPicBitmap, imagePath, AdharPicImageView);
                                newCustObj.setadharImage(supportingAttachmentNameForAdharImage);
                                mAceDnsTransactionDatabase.insertToSupportingAttachTable(supportingAttachmentNameForAdharImage, "add_adhar");
                            }
                        } catch (Exception e) {
                            Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                        }
                    } else {
                        try {
                            InputStream inputStream = mContext.getContentResolver().openInputStream(Objects.requireNonNull(data.getData()));
                            customerPicBitmap = BitmapFactory.decodeStream(inputStream);
                            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                            supportingAttachmentNameForCustomerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                            String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                            storeImageInLocalStorageShowOnImageView(customerPicBitmap, true, imagePath);
                            newCustObj.setcustomerImage(supportingAttachmentNameForCustomerImage);
                        } catch (Exception e) {
                            Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                        }
                    }
                } else {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                }
            } else {
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
            }
        }
        if (requestCode == REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG) {
            if (resultCode == RESULT_OK) {
                if (data != null) {
                    try {
                        Bundle extras = data.getExtras();
                        assert extras != null;
                        Bitmap imageBitmap = (Bitmap) extras.get("data");
                        assert imageBitmap != null;
                        customerPicBitmapForLocation = Utils.getResizedBitmap(imageBitmap, 100, 100);
                        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        supportingAttachmentNameForCustomerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                        String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                        storeImageInLocalStorageShowOnImageView(customerPicBitmapForLocation, false, imagePath);
                        newCustObj.setbase_latt(currentLat);
                        newCustObj.setbase_longi(currentLong);
                        newCustObj.setcustomerImage(supportingAttachmentNameForCustomerImage);
                        mAceDnsTransactionDatabase.insertToSupportingAttachTable(supportingAttachmentNameForCustomerImage, "add_customer");
                        localDbInsertServerSendProcess(timeStamp);
                    } catch (Exception e) {
                        Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                }
            } else {
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
            }
        } else if (requestCode == 9999) {
            if (resultCode == RESULT_CANCELED) {
                if (Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") || Constants.menuDetailsObj.getgeo_fencing_menu().contains("add_customer")) {
                    LocationTrackerObject.checkLocationUpdateSharing();
                }
            } else if (resultCode == RESULT_OK) {
                LocationTrackerObject.startLocationUpdates();
            }
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        LocationTrackerObject.checkLocationUpdateSharing();
    }

    @Override
    public void onPause() {
        super.onPause();
    }

    @Override
    public void onStop() {
        super.onStop();
        if (!isCameralaunched) {
            LocationTrackerObject.stopLocationUpdates();
        }
    }

    private void storeImageInLocalStorageShowOnImageView(Bitmap customerPicBitmap, Boolean ShowImageOnUi, String imagePath) throws FileNotFoundException {
        File outputFile;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out;
        out = new FileOutputStream(outputFile);
        customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
        if (ShowImageOnUi) {
            customerPicImageView.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 100, 100));
            userPic_layout.setVisibility(View.VISIBLE);
            isUserPicTaken = true;
        }
    }

    private void storeImageInLocalStorageShowOnImageViewCustomize(Bitmap customerPicBitmap, String imagePath, ImageView iv) throws FileNotFoundException {
        File outputFile;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out;
        out = new FileOutputStream(outputFile);
        customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
        if (true) {
            iv.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 200, 200));
        }
    }
}
