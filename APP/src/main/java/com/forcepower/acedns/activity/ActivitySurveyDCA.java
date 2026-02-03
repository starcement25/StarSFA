package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.adapter.MallAdapter;
import com.forcepower.acedns.adapter.SurveyOutletAdapter;
import com.forcepower.acedns.backgroundTask.AUTH_GetOTP;
import com.forcepower.acedns.backgroundTask.DCA_QOIEVerification;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitFootSoldier;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyPublishTask;
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.bean.SurveyPublish;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.text.MessageFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;
import java.util.Objects;

import androidx.annotation.NonNull;
import androidx.core.content.FileProvider;

public class ActivitySurveyDCA extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static TextView mTitleText = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonUndo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;

    private final static String VERIFICATION_URL = "http://mystore.qoie.in/lipl_app_ws/send_credential_to_retailer.php";
    private final static String OTP_URL = "http://mystore.qoie.in/lipl_app_ws/otp_verification_by_lipl_executive.php";
    public File mImageFile;
    public int mWidth = 175;
    public int mHeight = 150;
    public String mImagePath = "";
    public String mSImageName = "";
    String[] mTypeList;
    String[] mSurveyInputMenuList;
    ArrayList<MallMaster> mMallMasterList;
    Uri mFileUri;
    Button button = null;
    ArrayList<CheckBox> mMatrixCheckBoxList;
    Boolean ClosedShopOptionChosen = false;
    private AceDnsDatabase mAceDnsDatabase = null;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase = null;
    private Context mContext;
    private String mMallorAreaName = "";
    private String mMallorHighStreetID = "";
    private String mPinCode = "";
    private String mSurveyId = "";
    private String mURLType = "";
    private String mArea = "";
    private String mOTP = "";
    private String mMobileNo = "";
    private ProgressDialog mPrepareSurveyMenuProgressDialog;
    private Handler mPrepareSurveyMenuHandler;
    private ArrayList<SurveyPublish> mSurveyPublishList;
    private ArrayList<SurveyPublish> mSurveyPublishOutPutList;
    private ArrayList<SurveyPublish> mSurveyPublishInputList;
    private List<EditText> mEditTextList = new ArrayList<>();
    private String mPhoneNumber = "";
    private String mContactPerson = "";
    private String fullAddress = "";
    private String additionalDetails = "";
    private String mMenuType = "";
    private String mType = "", currentSelectedOutlet = "";
    private boolean isOtp = false;
    private boolean isOTPtaken = false;

    public static Bitmap decodeScaledBitmapFromSdCard(String filePath, int reqWidth, int reqHeight) {
        final BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;
        BitmapFactory.decodeFile(filePath, options);
        options.inSampleSize = calculateInSampleSize(options, reqWidth, reqHeight);
        options.inJustDecodeBounds = false;
        return BitmapFactory.decodeFile(filePath, options);
    }

    public static int calculateInSampleSize(BitmapFactory.Options options, int reqWidth, int reqHeight) {
        final int height = options.outHeight;
        final int width = options.outWidth;
        int inSampleSize = 1;
        if (height > reqHeight || width > reqWidth) {
            final int heightRatio = Math.round((float) height / (float) reqHeight);
            final int widthRatio = Math.round((float) width / (float) reqWidth);
            inSampleSize = Math.min(heightRatio, widthRatio);
        }
        return inSampleSize;
    }

    @SuppressLint("HandlerLeak")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey);
        RegisterActivities.registerActivity(this);

        mContext = ActivitySurveyDCA.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        Constants.isQOIEOTP = false;
        Constants.isQOIEVERIFICATION = false;
        button = new Button(mContext);
        InitializeView();

        mButtonBack.setOnClickListener(v -> finish());
        mButtonSubmit.setOnClickListener(v -> {
            if (isOtp) {
                if (isOTPtaken) {
                    FetchDataFromEditText();
                } else {
                    Utils.showToast(mContext, "Please send the otp");
                }
            } else {
                FetchDataFromEditText();
            }
        });
        
        mPrepareSurveyMenuHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                ActivitySurveyDCA.this.runOnUiThread(() -> {
                    switch (dojob) {
                        case 1:
                            new TRANS_SubmitSurveyPublishTask(mContext, true, "DCA").execute();
                            break;
                        case 2:
                            if (mTypeList.length > 0) {
                                if (mTypeList.length == 1) {
                                    mType = mTypeList[0];
                                    PrepareSurveyMenuData(3);
                                } else {
                                    ShowSurveyTypeListDialog();
                                }
                            } else {
                                Utils.showToast(mContext, "You have no type details");
                            }
                            break;
                        case 3:
                            ShowMallHighStreetListDialog();
                            break;
                        case 4:
                            ShowAreaListDialog();
                            break;
                        case 5:
                            ShowOutletDialog();
                            break;
                        case 6:
                            if (!mSurveyPublishInputList.isEmpty()) {
                                GetExistingMobileNoContactPerson();
                                ShowConditionofSurvey();
                            } else {
                                Toast.makeText(mContext, "Error in creating layout.Plese Retry..",Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 7:
                            ShowSurveyMenuList();
                            break;
                        case 8:

                            if (mURLType.equalsIgnoreCase("VERIFICATION")) {
                                if (Constants.isQOIEVERIFICATION) {
                                    OTPVerification(0);
                                } else {
                                    Toast.makeText(mContext, "Sorry! the process was incomplete. Reason: " + Constants.QOIEVERIFICATIONStatus, Toast.LENGTH_LONG).show();
                                }
                            }
                            if (mURLType.equalsIgnoreCase("OTP")) {
                                if (Constants.isQOIEOTP) {
                                    mAceDnsTransactionDatabase.UpadateSurveyPublishStatus(mSurveyId);
                                    Intent intent = new Intent(mContext, MenuActivity.class);
                                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                                    mContext.startActivity(intent);
                                } else {
                                    Toast.makeText(mContext, "Sorry! the process was incomplete. Reason: " + Constants.QOIEVERIFICATIONStatus, Toast.LENGTH_LONG).show();
                                    OTPVerification(1);
                                }
                            }
                            break;
                    }
                });
            }
        };

        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
            PrepareSurveyMenuData(2);
        } else {
            Utils.showToast(mContext, "You have no survey type. Please contact admin");
        }
    }

    private void FetchDataFromEditText() {
        if (!mEditTextList.isEmpty()) {
            for (EditText editText : mEditTextList) {
                String tag = editText.getTag().toString();
                String value = editText.getText().toString();
                for (int countx = 0; countx < mSurveyPublishOutPutList.size(); countx++) {
                    if (tag.equalsIgnoreCase(mSurveyPublishOutPutList.get(countx).getRowId())) {
                        if (tag.equalsIgnoreCase("RA152") || tag.equalsIgnoreCase("RA018")) {
                            if (value.equalsIgnoreCase(mPhoneNumber)) {
                                mSurveyPublishOutPutList.get(countx).setValue(value);
                                mSurveyPublishOutPutList.get(countx).setStatus("UNCHANGED");
                            } else {
                                mSurveyPublishOutPutList.get(countx).setValue(value);
                                mSurveyPublishOutPutList.get(countx).setStatus("CHANGED");
                            }
                        } else {
                            if (mMatrixCheckBoxList != null) {
                                for (int county = 0; county < mMatrixCheckBoxList.size(); county++) {
                                    String rowid = mMatrixCheckBoxList.get(county).getTag().toString();
                                    if (tag.equalsIgnoreCase(rowid)) {
                                        if (!mMatrixCheckBoxList.get(county).isChecked()) {
                                            mSurveyPublishOutPutList.get(countx).setValue(value);
                                            mSurveyPublishOutPutList.get(countx).setStatus("UNCHANGED");
                                        } else {
                                            mSurveyPublishOutPutList.get(countx).setValue(value);
                                            mSurveyPublishOutPutList.get(countx).setStatus("CHANGED");
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                        break;
                    }
                }
            }
            if (Constants.isDCAImageExist) {
                ShowAlert();
            } else {
                ShowImageCaptureLayer();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void OTPVerification(int try_no) {
        final Dialog mDialogOTPName = new Dialog(mContext, R.style.PauseDialog);
        mDialogOTPName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOTPName.setContentView(R.layout.dialog_otp_verification);
        mDialogOTPName.setCancelable(false);
        ((TextView) mDialogOTPName.findViewById(R.id.title)).setText("Please provide the OTP");
        final EditText edvalue =  mDialogOTPName.findViewById(R.id.editTextOTP);

        Button submit =  mDialogOTPName.findViewById(R.id.btn_submit);
        if (0 == try_no) {
            submit.setText("SEND");
        } else if (1 == try_no) {
            submit.setText("RE SEND");
        }

        submit.setOnClickListener(v -> {
            if (!edvalue.getText().toString().isEmpty()) {
                if (edvalue.getText().toString().length() == 4) {
                    mOTP = edvalue.getText().toString();
                    mURLType = "OTP";
                    PrepareSurveyMenuData(8);
                } else {
                    edvalue.setError("Please provide a 4 digit OTP");
                }
            } else {
                edvalue.setError("Please provide a 4 digit OTP");
            }
        });

        Button cancel =  mDialogOTPName.findViewById(R.id.btn_cancel);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(v -> mDialogOTPName.cancel());
        mDialogOTPName.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowConditionofSurvey() {
        final Dialog dialgoCondition = new Dialog(ActivitySurveyDCA.this, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_survey_dca);
        TextView txtMsg =  dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select an option.");
        final RadioGroup radioSelectionGroup =  dialgoCondition.findViewById(R.id.radioSelect);

        radioSelectionGroup.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection =  dialgoCondition.findViewById(checkedId);
            String status = radioSelection.getText().toString().trim().toUpperCase();
            if (radioSelection.getText().toString().trim().equalsIgnoreCase("Proceed")) {
                ClosedShopOptionChosen = false;
                processForDcaProceed();
            } else {
                ClosedShopOptionChosen = true;
                processForDcaClosed(status);
            }
            dialgoCondition.cancel();
        });

        ImageView back =  dialgoCondition.findViewById(R.id.image_cancel);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(arg0 -> dialgoCondition.cancel());
        dialgoCondition.show();
    }

    @SuppressLint("SetTextI18n")
    public void processForDcaProceed() {
        final Dialog mDialogOTPName = new Dialog(mContext, R.style.PauseDialog);
        mDialogOTPName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOTPName.setContentView(R.layout.dialog_otp_verification_dca);
        mDialogOTPName.setCancelable(false);
        final TextView titletTv = ( mDialogOTPName.findViewById(R.id.title));
        final TextView titletTv2 = ( mDialogOTPName.findViewById(R.id.title2));
        final TextView titletTv3 = ( mDialogOTPName.findViewById(R.id.title3));
        final TextView titletTv4 = ( mDialogOTPName.findViewById(R.id.title4));

        titletTv.setText(currentSelectedOutlet);
        if (isDataProper(additionalDetails)) {
            titletTv4.setVisibility(View.VISIBLE);
            titletTv4.setText(Html.fromHtml("<font color='#F58322'>" + "Details: " + "</font>" + additionalDetails));
        } else {
            titletTv4.setVisibility(View.GONE);
        }
        
        titletTv2.setVisibility(View.VISIBLE);
        titletTv2.setText(Html.fromHtml("<font color='#F58322'>" + "Contact Person: " + "</font>" + mContactPerson));
        
        titletTv3.setVisibility(View.VISIBLE);
        titletTv3.setText(Html.fromHtml("<font color='#F58322'>" + "Address: " + "</font>" + fullAddress));

        final EditText edvalue =  mDialogOTPName.findViewById(R.id.editTextOTP);
        if (!mPhoneNumber.matches("NA") && !mPhoneNumber.matches("No")) {
            edvalue.setText(mPhoneNumber);
            edvalue.setSelection(mPhoneNumber.length());
        }

        Button submit =  mDialogOTPName.findViewById(R.id.btn_submit);
        submit.setText("SUBMIT");
        submit.setOnClickListener(v -> {
            if (!edvalue.getText().toString().isEmpty()) {
                if (edvalue.getText().toString().length() == 10) {
                    if (edvalue.getText().toString().startsWith("9") || edvalue.getText().toString().startsWith("8") ||
                            edvalue.getText().toString().startsWith("7")) {
                        mMobileNo = edvalue.getText().toString();
                        mURLType = "VERIFICATION";
                        PrepareSurveyMenuData(8);
                    }
                } else {
                    edvalue.setError("Please provide a 10 digit Phone Number");
                }
            } else {
                edvalue.setError("Please provide a 10 digit Phone Number");
            }
        });

        Button cancel =  mDialogOTPName.findViewById(R.id.btn_cancel);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(v -> mDialogOTPName.cancel());
        mDialogOTPName.show();
    }

    public void processForDcaClosed(String Status) {
        mSurveyPublishOutPutList = new ArrayList<>();
        for (int count = 0; count < mSurveyPublishInputList.size(); count++) {
            SurveyPublish obj = mSurveyPublishInputList.get(count);
            obj.setStatus(Status);
            mSurveyPublishOutPutList.add(obj);
        }
        new GPSTracker(mContext);
        PrepareSurveyMenuData(1);
    }

    public void onClick(View clkdView) {
        String tag = (String) clkdView.getTag();
        if (tag.equalsIgnoreCase("RA152") || tag.equalsIgnoreCase("RA018")) {
            GetMobileNo();
            if (Constants.LIPLMOBILENO.trim().length() == 10) {
                button.setEnabled(false);
                DisableEditText();
                Constants.isDCAOTP = true;
                isOTPtaken = true;
                new AUTH_GetOTP(mContext).execute(Constants.LIPLMOBILENO);
            } else {
                Utils.showToast(mContext, "Please provide valid mobile number");
            }
        }
    }

    private void GetMobileNo() {
        if (!mEditTextList.isEmpty()) {
            for (EditText editText : mEditTextList) {
                String tag = editText.getTag().toString();
                String value = editText.getText().toString();
                if (tag.equalsIgnoreCase("RA152") || tag.equalsIgnoreCase("RA018")) {
                    Constants.LIPLMOBILENO = value;
                }
            }
        }
    }

    private void DisableEditText() {
        if (!mEditTextList.isEmpty()) {
            for (int count = 0; count < mEditTextList.size(); count++) {
                EditText editText = mEditTextList.get(count);
                String tag = editText.getTag().toString();
                if (tag.equalsIgnoreCase("RA152") || tag.equalsIgnoreCase("RA018")) {
                    mEditTextList.get(count).setEnabled(false);
                }
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        TextView txtVersion =  findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivitySurveyDCA.this) + "~" + Utils.getDBVersion(ActivitySurveyDCA.this));
        mButtonUndo =  findViewById(R.id.btn_undo);
        mButtonBack =  findViewById(R.id.back);
        mButtonSubmit =  findViewById(R.id.btn_Submi);
        mTitleText =  findViewById(R.id.textViewTitle);
        mTitleText.setText("DCA");
        mButtonUndo.setVisibility(View.INVISIBLE);
    }

    @SuppressLint("SimpleDateFormat")
    public void SaveSurveyDCAtoDatabase() {
        String timeStamp;
        timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        mAceDnsTransactionDatabase.INSERTtoSurveyDCAOutput(timeStamp, mSurveyPublishOutPutList, mType);
        mAceDnsTransactionDatabase.insertToLocationTable("SUA", timeStamp);
        mAceDnsTransactionDatabase.UpadateSurveyPublishStatus(mSurveyId);
    }

    private void ShowAlert() {
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(ActivitySurveyDCA.this);
        AlertDG.setTitle("Information");
        AlertDG.setMessage("Do you want to take picture?");
        AlertDG.setPositiveButton("Yes", (dialog, which) -> ShowImageCaptureLayer());
        AlertDG.setNegativeButton("No", (dialog, which) -> {
            mButtonSubmit.setEnabled(false);
            if (ClosedShopOptionChosen) {
                finish();
            } else {
                ShowOTPDialog(Constants.LIPLMOBILENO);
            }
        });
        AlertDG.setCancelable(false);
        AlertDG.create().show();
    }

    private void ShowAlertForAddOutlet() {
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(ActivitySurveyDCA.this);
        AlertDG.setTitle("Information");
        AlertDG.setMessage("You have no outlet left.Do you want to add outlet?");
        AlertDG.setPositiveButton("Yes", (dialog, which) -> PrepareSurveyMenuData(7));
        AlertDG.setNegativeButton("No", (dialog, which) -> finish());
        AlertDG.setCancelable(false);
        AlertDG.create().show();
    }

    public void PrepareSurveyMenuData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        SaveSurveyDCAtoDatabase();
                        break;
                    case 2:
                        if (Constants.surveyFormDetailsObj.getSurveyTypeDetails().contains(",")) {
                            mTypeList = Constants.surveyFormDetailsObj.getSurveyTypeDetails().split(",");
                        } else {
                            mTypeList = new String[1];
                            mTypeList[0] = Constants.surveyFormDetailsObj.getSurveyTypeDetails();
                        }
                        break;
                    case 3:
                        mMallMasterList = mAceDnsDatabase.GetDCAMallMasterData(mType);
                        break;
                    case 4:
                        mSurveyPublishList = mAceDnsDatabase.GethighstreetAreaData(mPinCode);
                        break;
                    case 5:
                        mSurveyPublishList = mAceDnsDatabase.GetDCAOutletData(mType, mMallorHighStreetID, mArea);
                        break;
                    case 6:
                        mSurveyPublishInputList = mAceDnsDatabase.GetDCADrawLayoutData(mSurveyId);
                        break;
                    case 7:
                        int max = 0;
                        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
                            max = mAceDnsDatabase.GetMenuName(mType);
                        }
                        mSurveyInputMenuList = new String[max];
                        for (int i = 0; i < Constants.mSurveyMenuDetailsList.size(); i++) {
                            mSurveyInputMenuList[i] = Constants.mSurveyMenuDetailsList.get(i).getMenuName();
                        }
                        break;
                    case 8:
                        boolean isFinished = false;
                        String URL ;
                        DCA_QOIEVerification verification = null;
                        if (mURLType.equalsIgnoreCase("VERIFICATION")) {
                            URL = VERIFICATION_URL;
                            verification = new DCA_QOIEVerification(mContext, URL, mURLType);
                            verification.execute(mSurveyId, mMobileNo);
                        }
                        if (mURLType.equalsIgnoreCase("OTP")) {
                            URL = OTP_URL;
                            verification = new DCA_QOIEVerification(mContext, URL, mURLType);
                            verification.execute(mSurveyId, mOTP);
                        }
                        while (!isFinished) {
                            assert verification != null;
                            if (verification.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                        break;
                }
                Message msg = mPrepareSurveyMenuHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyMenuHandler.sendMessage(msg);
            }
        }.start();
    }

    @SuppressLint("SetTextI18n")
    public void ShowSurveyTypeListDialog() {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title =  mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a type");
        ListView dialogList =  mDialogDepotName.findViewById(R.id.list);

        for (int count = 0; count < mTypeList.length; count++) {
            mTypeList[count] = mTypeList[count].toUpperCase();
        }

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.simple_list_child, R.id.list_details, mTypeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            mType = Objects.requireNonNull(adapter.getItem(pos)).toLowerCase();
            mDialogDepotName.cancel();
            PrepareSurveyMenuData(3);
        });

        ImageView back =  mDialogDepotName.findViewById(R.id.image_cancel);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(arg0 -> mDialogDepotName.cancel());

        Button cancel =  mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        mDialogDepotName.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowMallHighStreetListDialog() {
        if (!mMallMasterList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(ActivitySurveyDCA.this, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);

            TextView title =  mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select a " + mType);

            ListView dialogList =  mMallHighStreetListDialog.findViewById(R.id.list);

            if (mType.equalsIgnoreCase("hi-street")) {
                Constants.mSurveyType = "pincode";
            } else {
                Constants.mSurveyType = "mall";
            }

            final MallAdapter malladapter = new MallAdapter(ActivitySurveyDCA.this, R.layout.mall_list, mMallMasterList);
            dialogList.setAdapter(malladapter);

            EditText searchText =  mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    malladapter.getFilter().filter(s.toString());
                }
                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
                @Override
                public void afterTextChanged(Editable s) {}
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                if (mType.equalsIgnoreCase("hi-street")) {
                    Constants.selectedMallMaster = malladapter.getItem(arg2);
                    assert Constants.selectedMallMaster != null;
                    mPinCode = Constants.selectedMallMaster.getPincode();
                    PrepareSurveyMenuData(4);
                } else {
                    Constants.selectedMallMaster = malladapter.getItem(arg2);
                    assert Constants.selectedMallMaster != null;
                    mMallorAreaName = Constants.selectedMallMaster.getMallName();
                    mMallorHighStreetID = Constants.selectedMallMaster.getMallId();
                    PrepareSurveyMenuData(5);
                }
            });

            ImageView back =  mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(arg0 -> mMallHighStreetListDialog.cancel());

            Button cancel =  mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);

            mMallHighStreetListDialog.show();
        } else {
            Utils.showToast(mContext, "You have no " + mType + " asigned");
        }
    }

    @SuppressLint("SetTextI18n")
    private void ShowOutletDialog() {
        if (!mSurveyPublishList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(ActivitySurveyDCA.this, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);

            TextView title =  mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select an outlet");

            ListView dialogList =  mMallHighStreetListDialog.findViewById(R.id.list);

            final SurveyOutletAdapter adapter = new SurveyOutletAdapter(ActivitySurveyDCA.this, R.layout.customer_broker_list_child, mSurveyPublishList);
            dialogList.setAdapter(adapter);

            EditText searchText =  mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }
                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
                @Override
                public void afterTextChanged(Editable s) {}
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                mSurveyId = Objects.requireNonNull(adapter.getItem(arg2)).getSurveyId();
                currentSelectedOutlet = Objects.requireNonNull(adapter.getItem(arg2)).getValue();
                PrepareSurveyMenuData(6);
            });

            ImageView back =  mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(arg0 -> mMallHighStreetListDialog.cancel());

            Button btnaddoutlet =  mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            btnaddoutlet.setText("Add Outlet");
            btnaddoutlet.setOnClickListener(v -> {
                mMallHighStreetListDialog.cancel();
                PrepareSurveyMenuData(7);
            });
            mMallHighStreetListDialog.show();
        } else {
            ShowAlertForAddOutlet();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowAreaListDialog() {
        if (!mSurveyPublishList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(ActivitySurveyDCA.this, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);

            TextView title =  mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select an area");

            ListView dialogList =  mMallHighStreetListDialog.findViewById(R.id.list);

            final SurveyOutletAdapter adapter = new SurveyOutletAdapter(ActivitySurveyDCA.this, R.layout.customer_broker_list_child, mSurveyPublishList);
            dialogList.setAdapter(adapter);

            EditText searchText =  mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    adapter.getFilter().filter(s.toString());
                }
                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
                @Override
                public void afterTextChanged(Editable s) {}
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                mArea = Objects.requireNonNull(adapter.getItem(arg2)).getValue();
                mMallorHighStreetID = Objects.requireNonNull(adapter.getItem(arg2)).getMallId();
                mMallorAreaName = mArea;
                PrepareSurveyMenuData(5);
            });

            ImageView back =  mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(arg0 -> mMallHighStreetListDialog.cancel());

            Button cancel =  mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);

            mMallHighStreetListDialog.show();
        } else {
            Utils.showToast(mContext, "You have no outlet assigned");
        }
    }

    private void GetExistingMobileNoContactPerson() {
        String rowId ;
        String value ;
        mPhoneNumber = "";
        for (int count = 0; count < mSurveyPublishInputList.size(); count++) {
            rowId = mSurveyPublishInputList.get(count).getRowId();
            value = mSurveyPublishInputList.get(count).getValue();

            //contact person first name
            if (rowId.equalsIgnoreCase("RA149") || rowId.equalsIgnoreCase("RA015")) {
                mContactPerson = value;
            }
            //contact person last name
            if (rowId.equalsIgnoreCase("RA150") || rowId.equalsIgnoreCase("RA016")) {
                mContactPerson = MessageFormat.format("{0} {1}", mContactPerson, value.trim());
            }

            if (rowId.equalsIgnoreCase("RA003") || rowId.equalsIgnoreCase("RA137")) {
                String shopNumber = value.trim();
                if (isDataProper(shopNumber)) {
                    additionalDetails = "shop no: " + shopNumber + ", ";
                }
            }
            if (rowId.equalsIgnoreCase("RA004") || rowId.equalsIgnoreCase("RA138")) {
                String floor = value.trim();
                if (isDataProper(floor)) {
                    additionalDetails = MessageFormat.format("{0}{1}, ", additionalDetails, floor);
                }
            }
            if (rowId.equalsIgnoreCase("RA139")) {
                String buildingName = value.trim();
                if (isDataProper(buildingName)) {
                    additionalDetails = additionalDetails + buildingName;
                }
            }

            if (rowId.equalsIgnoreCase("RA222") || rowId.equalsIgnoreCase("RA140")) {
                String streetNumber = value.trim();
                if (isDataProper(streetNumber)) {
                    fullAddress = streetNumber + ", ";
                }
            }
            if (rowId.equalsIgnoreCase("RA141")) {
                String streetAddress = value.trim();
                if (isDataProper(streetAddress)) {
                    fullAddress = MessageFormat.format("{0}{1}, ", fullAddress, streetAddress);
                }
            }
            if (rowId.equalsIgnoreCase("RA007") || rowId.equalsIgnoreCase("RA142")) {
                String landMark = value.trim();
                if (isDataProper(landMark)) {
                    fullAddress = fullAddress + landMark + ", ";
                }
            }
            if (rowId.equalsIgnoreCase("RA008") || rowId.equalsIgnoreCase("RA143")) {
                String area = value.trim();
                if (isDataProper(area)) {
                    fullAddress = fullAddress + area + ", ";
                }
            }
            if (rowId.equalsIgnoreCase("RA009") || rowId.equalsIgnoreCase("RA144")) {
                String city = value.trim();
                if (isDataProper(city)) {
                    fullAddress = fullAddress + city + ", ";
                }
            }
            if (rowId.equalsIgnoreCase("RA010") || rowId.equalsIgnoreCase("RA145")) {
                String pinCode = value.trim();
                if (isDataProper(pinCode)) {
                    fullAddress = fullAddress + pinCode + ", ";
                }
            }
            if (rowId.equalsIgnoreCase("RA011") || rowId.equalsIgnoreCase("RA146")) {
                String state = value.trim();
                if (isDataProper(state)) {
                    fullAddress = fullAddress + state;
                }
            }

            //mobile number
            if (rowId.equalsIgnoreCase("RA152") || rowId.equalsIgnoreCase("RA018")) {
                if (value.contains("#")) {
                    String[] splitstr = value.split("#");
                    if (splitstr.length > 0) {
                        value = splitstr[0].trim();
                    }
                }
                mPhoneNumber = value;
                isOtp = true;
            }
        }
        if (additionalDetails.endsWith(", ")) {
            additionalDetails = additionalDetails.substring(0, additionalDetails.length() - 2);
        }
        if (fullAddress.endsWith(", ")) {
            fullAddress = fullAddress.substring(0, fullAddress.length() - 2);
        }
    }

    public void onDestroy() {
        super.onDestroy();
        Constants.mOTP = null;
    }

    private void ShowOTPDialog(final String mobileno) {
        final Dialog mDialogOTPName = new Dialog(mContext, R.style.PauseDialog);
        mDialogOTPName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOTPName.setContentView(R.layout.dialog_otp_verification);
        mDialogOTPName.setCancelable(false);
        final EditText edvalue =  mDialogOTPName.findViewById(R.id.editTextOTP);
        edvalue.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                String otp = s.toString().trim();
                if (otp.length() == 4) {
                    if (Constants.mOTP != null && otp.matches(Constants.mOTP)) {
                        mDialogOTPName.cancel();
                        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        String libraryStatus = Utils.checkLibraryConditions(ActivitySurveyDCA.this);
                        if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                            mButtonSubmit.setEnabled(false);
                            PrepareSurveyMenuData(1);
                        }
                    }

                }
            }
            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}
            @Override
            public void afterTextChanged(Editable s) {}
        });
        Button submit =  mDialogOTPName.findViewById(R.id.btn_submit);
        submit.setOnClickListener(v -> {
            Constants.isRESENDOTPREQUEST = true;
            new AUTH_GetOTP(mContext).execute(mobileno);
        });

        Button cancel =  mDialogOTPName.findViewById(R.id.btn_cancel);
        cancel.setVisibility(View.VISIBLE);
        cancel.setOnClickListener(v -> {
            mDialogOTPName.cancel();
            ShowAlertDialogForConfirmation();
        });
        mDialogOTPName.show();
    }

    public void ShowAlertDialogForConfirmation() {
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(ActivitySurveyDCA.this);
        AlertDG.setTitle("Information");
        AlertDG.setMessage("All your information will be lost.\nDo you still want to exit?");
        AlertDG.setPositiveButton("Yes", (dialog, which) -> finish());
        AlertDG.setNegativeButton("No", (dialog, which) -> ShowOTPDialog(Constants.LIPLMOBILENO));
        AlertDG.setCancelable(false);
        AlertDG.create().show();
    }

    @SuppressLint("SimpleDateFormat")
    public void ShowImageCaptureLayer() {
        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
        mSImageName = imageName;
        mImagePath = Utils.getAppStoragePath(mContext) + imageName;
        mImageFile = new File(mImagePath);
        try {
            mImageFile.createNewFile();
        } catch (IOException ignored) {}

        if (android.os.Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            mFileUri = FileProvider.getUriForFile(mContext, BuildConfig.APPLICATION_ID + ".provider", mImageFile);
        } else {
            mFileUri = Uri.fromFile(mImageFile);
        }
        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, mFileUri);
        int TAKE_PHOTO_CODE = 0;
        startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            GPSTracker gpstracker = new GPSTracker(mContext);
            boolean LocationEnabled = gpstracker.canGetLocation();
            if (LocationEnabled) {
                saveImageAndGotoOtpProcess();
                gpstracker.stopUsingGPS();
            } else {
                gpstracker.showSettingsAlertToEnableLocation("Picture storing process interrupted.");
            }
        }
        if (resultCode == RESULT_CANCELED) {
            Constants.isSurveyImageTake = false;
        }
    }

    public void saveImageAndGotoOtpProcess() {
        Constants.isSurveyImageTake = true;
        try {
            Bitmap bitmap = decodeScaledBitmapFromSdCard(mImagePath, mWidth, mHeight);
            FileOutputStream out;
            try {
                out = new FileOutputStream(mImagePath);
                bitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
            } catch (FileNotFoundException ignored) { }
            mAceDnsTransactionDatabase.insertToSupportingAttachTable(mSImageName, "SURVEY_DCA");
            mButtonSubmit.setEnabled(false);
            SurveyPublish obj = new SurveyPublish();
            obj.setSurveyId(mSurveyId);
            obj.setRowId("IMAGE");
            obj.setValue(mSImageName);
            mSurveyPublishOutPutList.add(obj);
            if (ClosedShopOptionChosen) {
                finish();
            } else {
                ShowOTPDialog(Constants.LIPLMOBILENO);
            }
        } catch (Exception ex) {
            Toast.makeText(mContext, "Image is too large", Toast.LENGTH_LONG).show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowSurveyMenuList() {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title =  mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a type");
        ListView dialogList =  mDialogDepotName.findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.simple_list_child, R.id.list_details, mSurveyInputMenuList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            mDialogDepotName.cancel();
            mMenuType = adapter.getItem(pos);
            ShowFootSholdierInformation();
        });

        ImageView back =  mDialogDepotName.findViewById(R.id.image_cancel);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(arg0 -> mDialogDepotName.cancel());

        Button cancel =  mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        mDialogDepotName.show();
    }

    @SuppressLint({"SetTextI18n","SimpleDateFormat"})
    public void ShowFootSholdierInformation() {
        final Dialog checkoutDialog = new Dialog(ActivitySurveyDCA.this, R.style.PauseDialog);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.dialog_checked_out);
        checkoutDialog.setCancelable(true);
        TextView title =  checkoutDialog.findViewById(R.id.title);
        title.setText("Please add business name");

        TextView customer_name =  checkoutDialog.findViewById(R.id.customer_name);
        if (Constants.selectedMallMaster != null) {
            customer_name.setText(mMallorAreaName);
        } else {
            customer_name.setText("");
        }
        final EditText remark_box =  checkoutDialog.findViewById(R.id.remark_box);
        Button submit =  checkoutDialog.findViewById(R.id.btn_submit);
        submit.setOnClickListener(v -> {
            GPSTracker gpstracker = new GPSTracker(mContext);
            boolean LocationEnabled = gpstracker.canGetLocation();
            if (LocationEnabled) {
                if (!remark_box.getText().toString().trim().isEmpty()) {
                    String businessname = remark_box.getText().toString();
                    checkoutDialog.cancel();
                    getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    if (!mType.equalsIgnoreCase("hi-street")) {
                        mPinCode = "";
                    }
                    String timeStamps = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    String trans_id = "FS" + Constants.employeeDetailObject.getEmpCode() + timeStamps;
                    mAceDnsTransactionDatabase.InsertFootSoldier(trans_id, mMallorHighStreetID, mMallorAreaName, mPinCode, businessname, mType, mMenuType);
                    mAceDnsTransactionDatabase.InsertFsSurveyPublish(trans_id, mMallorHighStreetID, mMallorAreaName, mPinCode, businessname, mType);
                    mAceDnsTransactionDatabase.insertToLocationTable("FS", timeStamps);
                    gpstracker.stopUsingGPS();
                    new TRANS_SubmitFootSoldier(mContext, true, "SUBMIT").execute();

                } else {
                    Utils.showToast(mContext, "Please type the business name");
                }
            }
        });
        checkoutDialog.show();
    }

    private Boolean isDataProper(String data) {
        return data != null && !data.matches("") && !data.matches("null") && !data.matches("NA");
    }

}
