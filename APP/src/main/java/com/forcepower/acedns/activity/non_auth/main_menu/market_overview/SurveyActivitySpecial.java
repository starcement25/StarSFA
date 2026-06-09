package com.forcepower.acedns.activity.non_auth.main_menu.market_overview;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.Html;
import android.text.InputType;
import android.text.TextUtils;
import android.text.TextWatcher;
import android.util.DisplayMetrics;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.adapter.HistoryViewAdapter;
import com.forcepower.acedns.adapter.HistoryViewInputAdapter;
import com.forcepower.acedns.adapter.KeyValueCheckAdapter;
import com.forcepower.acedns.adapter.KeyValueNormalAdapter;
import com.forcepower.acedns.adapter.ListSearchCheckAdapter;
import com.forcepower.acedns.adapter.SingleItemAdapter;
import com.forcepower.acedns.backgroundTask.AUTH_GetOTP;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyTask;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.bean.MallSurveyRelation;
import com.forcepower.acedns.bean.SiteMasterDetails;
import com.forcepower.acedns.bean.SurveyDetails;
import com.forcepower.acedns.bean.SurveyInput;
import com.forcepower.acedns.bean.SurveyRoot;
import com.forcepower.acedns.bean.SurveyTableView;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.text.MessageFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Locale;
import java.util.Map.Entry;
import java.util.Set;
import java.util.concurrent.TimeUnit;

import androidx.core.content.FileProvider;

import static com.forcepower.acedns.constants.Constants.CurrentCheckBoxItemDependantOn;
import static com.forcepower.acedns.constants.Constants.closeSubActionDialog;
import static com.forcepower.acedns.constants.Constants.currentSiteValueForEdit;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;
import static com.forcepower.acedns.constants.Constants.isCurrentCheckBoxItemDependant;
import static com.forcepower.acedns.constants.Constants.isShowValueSendValueDifferentForChcekBOx;
import static com.forcepower.acedns.constants.Constants.sendColumnDataForSurveyCheckBox;

//Lead Generation
//button_background

public class SurveyActivitySpecial extends AceDnsParentActivity {

    @SuppressLint("StaticFieldLeak")
    private static TextView mTitleText = null;

    @SuppressLint("StaticFieldLeak")
    private static LinearLayout mParentLayout = null;
    Spinner customerSpinner;
    View viewSpinner;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonSubmit = null;
    public ArrayList<RadioGroup> mRadioGrpList;
    public ArrayList<Button> mButtonList;
    public ArrayList<DatePicker> mdatePickerListForSubActionLayout;
    public ArrayList<CheckBox> mCheckBoxList = new ArrayList<CheckBox>();
    public ArrayList<TextView> mTextViewList;
    public ArrayList<ImageView> mImageViewList;
    public ArrayList<String> mNonRepeat;
    public AceDnsDatabase mAceDnsDatabase;
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    public ProgressDialog mPrepareSurveyProgressDialog;
    public Handler mPrepareSurveyHandler;
    public Handler mPrepareDataSaveHandler;
    public Context mContext;
    public boolean mCategory = false;
    public boolean mSubCategory = false;
    public boolean mIsOTP = false;
    //private ArrayList<SurveyDetails> mSurveyDetailsPredefinedList;
    public boolean mIsTableView = false;
    public boolean mIsTittle = false;
    public boolean isDependangtDataEmpty = false;
    int countFinal = 0;

    EditText doubleTextView, doubleTextView2;
    SurveyDetails mSurveyInputListDependend;
    EditText mEdiTextDynamic = null;
    HashMap<String, ArrayList<CheckBox>> mCheckBoxMatrixListContainer = new HashMap<String, ArrayList<CheckBox>>();
    ArrayList<CheckBox> mMatrixCheckBoxList;
    HashMap<String, ArrayList<RadioGroup>> mRadioGroupMatrixListContainer = new HashMap<String, ArrayList<RadioGroup>>();
    ArrayList<RadioGroup> mMatrixRadioGroupList;
    Uri mFileUri;
    RadioButton radioButton;
    String dependantConditionSqlQueryForRadioType = "";
    String mDateValue = "";
    int spliton = 0;
    // Connection detector class
    GPSTracker gpstracker;
    ArrayList<SiteMasterDetails> siteMasterList;
    Dialog grpDialogSubAction;
    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */

    int mYear, mMonth, mDay;
    private File mImageFile;
    private int mWidth = 175;
    private int mHeight = 150;
    private ArrayList<SurveyInput> mSurveyInputList;
    private ArrayList<SurveyDetails> mInputTimeSurveyDetailsList;
    private ArrayList<SurveyDetails> mUndoSurveyList;
    private ArrayList<MallSurveyRelation> mMallSurveyRelationList;
    private ArrayList<SurveyTableView> mSurveyTableViewList;
    private ArrayList<KeyValue> mKeyValueImageList;
    private ArrayList<KeyValue> mKeyValueSubList;
    private ArrayList<KeyValue> mKeyValueList;
    private List<EditText> mEditTextList = new ArrayList<EditText>();
    private List<EditText> mEditTextOnDialogList = new ArrayList<EditText>();
    private List<String> mMultilevelTableViewData = new ArrayList<String>();
    private String currentRowId = "";
    private String mCheckValue = "";
    private String mDependentDisplayName = "";
    private String mDisplayName = "";
    private String mMenuID = "";
    private String mMallId = "";
    private String mSurveyType = "";
    private String mPredefinedValue = "";
    private String mPredefinedValueEditTextDialog = "", mPredefinedValueEditTextDialog2 = "";
    private String mMallColumnName = "";
    private String mSubtableInfo = "";
    private String mEditType = "";
    private String mWhereClause = "";
    private String actionStringOfCurrentSelectedItem = "";
    private String tempValForDependentSurvey = "";
    private String currentTableHeader = "";
    private String currentItemDisplayName = "";
    private String mSubRowID = "";
    private String mSubTableName = "";
    private String mSubColumnName = "";
    private String finalDisplay = "";
    //private String mSubMessage		=	"";
    private String mSubDependent = "";
    private String mLayout = "";
    private String mType = "";
    public static String mParentType = "";
    public static String mParentActionValue = "";
    public static Boolean mParentActionPresent = false;
    private String mDependentRowId = "";
    private String mFinalRowID = "";
    private String mActionstring = "";
    private String mCondition = "";
    private String dialogHeaderSearchTypeDynamicText = "Please select an option";
    //private String mValidation		=	"";
    private String[] values;
    private String[] valuesQty;
    private String[] subvalues;
    private String mImagePath = "";
    private String mImageName = "";
    private String mSImageName = "";
    private String mTableName = "";
    private String mTableName2 = "";//for relationalview
    private String mInputColumn = "";
    private String mSendColumn = "";
    private String mShowColumn = "";
    private String mShowColumn1 = "";
    private String mShowColumn2 = "";
    private String mShowColumn3 = "";
    private String mShowColumn4 = "";
    private String mShowColumn5 = "";
    private String mShowColumn6 = "";
    private int mShowColumnCount = 0;
    private String mColumnName = "";
    private String mColumnName2 = "";//for relationalview
    private String mCustomerSelectionBasis = "";//string that tells us we shoud select all cusomer or customers for today's route code
    private String mCustomerSelectionBasisFilter = "";//additional filter for master table, example: cust_type;D that means where cust_type='D'
    private String mMessage = "";
    private String mDependent = "";
    private String mSubMenu = "";
    private String mOTPRowID = "";
    private String mOTPPhoneNo = "";
    private String boolStringType1 = "";
    private String boolStringType2 = "";
    private String mDecision = "";
    private String mSubActionTag = "";
    private boolean isRound = false;
    private int TAKE_PHOTO_CODE = 0;
    private boolean mIsButtonEnable = true;
    private String mActionPressed = "";
    private String mActionPressedExistingValue = "";
    private String mRoundValue = "";
    ArrayAdapter<String> spinnerArrayAdapter;
    ArrayList<String> spinnerArrayCustomers;
    Activity a;

    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;

    Handler mHandler;
    ProgressDialog loader;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        Constants.shouldUpdateCustomerMasterWithEmail = false;
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey_special);
        RegisterActivities.registerActivity(this);
        mContext = SurveyActivitySpecial.this;
        a = (Activity) mContext;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        currentSiteValueForEdit = "";
        siteMasterList = new ArrayList<>();
        closeSubActionDialog = false;
        mImageViewList = new ArrayList<>();
        TextView txtVersion =  findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(SurveyActivitySpecial.this) + "~"
                + Utils.getDBVersion(SurveyActivitySpecial.this));
        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
            mLayout = getIntent().getStringExtra("SURVEY");
        } else {
            Constants.mFinalSurveyList = new ArrayList<SurveyDetails>();
        }

        if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
            mMenuID = getIntent().getStringExtra("SURVEYMENUID");
        }
        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
            mSurveyType = getIntent().getStringExtra("SURVEYMADEAT");
            mMallId = Constants.selectedMallMaster.getMallId();
        }
//        if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
//
//        }
        Constants.isUpcoming = "no";
        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
            mAceDnsDatabase.GetMenuName(mType);
        } else {
            if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                mSubMenu = getIntent().getStringExtra("SUBMENU");
                mAceDnsDatabase.GetMenuName("", mSubMenu);
            } else {
                mAceDnsDatabase.GetMenuName();
            }
        }
        mButtonBack =  findViewById(R.id.back);
        mButtonBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                Constants.mNoOfCapture = 0;
                finish();
            }
        });
        mTitleText =  findViewById(R.id.textViewTitle);
        if (mSubMenu.equalsIgnoreCase("customer add")) {
            mTitleText.setText("Existing Customer");
        } else {
            if(mSubMenu.equalsIgnoreCase("Quality Complaint"))
                mTitleText.setText("MTL Quality Complaint");
            else
                mTitleText.setText(mSubMenu);
        }

        if (Constants.mSurveyMenuDetailsList.size() > 0) {
            Log.d("TAG", "onCreate: 1");
            addRadioButtonWithBottomBorder();
//                                    ShowList(mType, mDecision);
        } else {
            Log.d("TAG", "onCreate: 2");
            Utils.showToast(mContext, "No data found. Please Synchronize Data");
            finish();
        }

        try {
            ConnectionDetector cd;
            cd = new ConnectionDetector(mContext);
            if (Constants.nickName.equalsIgnoreCase("sai") && cd.isConnectingToInternet()) {
                Log.d("TAG", "onCreate: 3");
                PrepareCustomerData(1);
            }
            if (mSubMenu.equalsIgnoreCase("Complaint Report") && cd.isConnectingToInternet()) {
                Log.d("TAG", "onCreate: 4");
                PrepareCustomerData(2);
            }
            if (mSubMenu.equalsIgnoreCase("Site Lead and Conversion Tracking") && cd.isConnectingToInternet()) {
                Log.d("TAG", "onCreate: 5");
                PrepareCustomerData(3);
            }
            if (Constants.nickName.equalsIgnoreCase("SHAKTI") && mSubMenu.equalsIgnoreCase("KYC") && cd.isConnectingToInternet()) {
                Log.d("TAG", "onCreate: 6");
                PrepareCustomerData(4);
            }
            if (mSubMenu.equalsIgnoreCase("Lead Generation") && cd.isConnectingToInternet()) {
                Log.d("TAG", "onCreate: 7");
                PrepareCustomerData(5);
            }
            if (mSubMenu.equalsIgnoreCase("Quality Complaint") && cd.isConnectingToInternet()) {
                Log.d("TAG", "onCreate: 8");
                PrepareCustomerData(6);
            }
            if (mSubMenu.equalsIgnoreCase("MTL Testing Format") && cd.isConnectingToInternet()) {
                Log.d("TAG", "onCreate: 9");
                PrepareCustomerData(7);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();

            }
        };
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            SetNoofImageCaptured(mFinalRowID, mImageName);
            Constants.isSurveyImageTake = true;
            ImageView mImageViewCaptureImage = getImageViewByTag(mFinalRowID);
            try {
//                Bitmap bitmap = decodeScaledBitmapFromSdCard(mImagePath, mWidth, mHeight);
                Bitmap bitmap = Utils.decodeBitmapFromSdCard(mImagePath);
                LayoutParams parms = new LayoutParams(mWidth, mHeight);
                mImageViewCaptureImage.setLayoutParams(parms);
                mImageViewCaptureImage.setImageBitmap(bitmap);
                FileOutputStream out = null;
                try {
                    out = new FileOutputStream(mImagePath);
                    bitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);

                } catch (FileNotFoundException e) {
                    e.printStackTrace();
                }
                mAceDnsTransactionDatabase.insertToSupportingAttachTable(mSImageName, "SURVEY");
                mImageName = GetImageName(mFinalRowID);
                SetSurveyValue(mFinalRowID, mImageName);
//                mParentLayout.addView(mImageViewCaptureImage);
            } catch (Exception ex) {
                Toast.makeText(mContext, "Image is too large", Toast.LENGTH_SHORT).show();
            }
        }
        if (resultCode == RESULT_CANCELED) {
            Constants.isSurveyImageTake = false;
            Log.i("Camera canceled", "Cancel");
        }
        if (requestCode == TAKE_PHOTO_CODE && resultCode == RESULT_OK) {

            if (false == (ImageValidation(mFinalRowID))) {
                Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
            } else {
                Constants.isSurveyImageTake = true;
                AlertDialog.Builder AlertDG = new AlertDialog.Builder(SurveyActivitySpecial.this);
                AlertDG.setTitle("Information");
                AlertDG.setMessage("Do you want to take another pics?");
                AlertDG.setPositiveButton("Yes", new DialogInterface.OnClickListener() {

                    public void onClick(DialogInterface dialog, int which) {
                        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                        mSImageName = imageName;
                        mImageName = imageName + "; ";
                        mImagePath = Utils.getAppStoragePath(mContext) + imageName;
                        mImageFile = new File(mImagePath);
                        try {
                            mImageFile.createNewFile();
                        } catch (IOException e) {
                        }

//						mFileUri = Uri.fromFile(mImageFile);


                        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                            mFileUri = FileProvider.getUriForFile(mContext,
                                    BuildConfig.APPLICATION_ID + ".provider",
                                    mImageFile);
                        } else {
                            mFileUri = Uri.fromFile(mImageFile);

                        }


                        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                        cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, mFileUri);
                        startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
                    }
                });
                AlertDG.setNegativeButton("No", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        mImageName = GetImageName(mFinalRowID);
                        SetSurveyValue(mFinalRowID, mImageName);
                    }
                });
                AlertDG.setCancelable(true);
                AlertDG.create().show();
            }
        }
    }

    @Override
    public void onResume() {
        super.onResume();
    }

    public static Bitmap decodeScaledBitmapFromSdCard(String filePath, int reqWidth, int reqHeight) {
        // First decode with inJustDecodeBounds=true to check dimensions
        final BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;
        BitmapFactory.decodeFile(filePath, options);
        // Calculate inSampleSize
        options.inSampleSize = calculateInSampleSize(options, reqWidth, reqHeight);
        // Decode bitmap with inSampleSize set
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
            inSampleSize = heightRatio < widthRatio ? heightRatio : widthRatio;
        }
        return inSampleSize;
    }

    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        new commonAsyncTaskMaster(mContext, "customer_master");

                    case 2:
                        new commonAsyncTaskMaster(mContext, "complaint_master");

                    case 3:
                        new commonAsyncTaskMaster(mContext, "site_lead_conversion_master");

                    case 4:
                        new commonAsyncTaskMaster(mContext, "kyc_master");

                    case 5:
                        new commonAsyncTaskMaster(mContext, "lead_generation_master");

                    case 6:
                        new commonAsyncTaskMaster(mContext, "quality_complaint_master");

                    case 7:
                        new commonAsyncTaskMaster(mContext, "mtl_testing_master");

                        break;
                }

                Message msg = mHandlerPrepareSaudaData.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPrepareSaudaData.sendMessage(msg);
            }
        }.start();
    }

    public void surveyActivityINitializationProcess() {
        mTextViewList = new ArrayList<>();
        mMultilevelTableViewData = new ArrayList<>();

        mPrepareDataSaveHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mPrepareSurveyProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SurveyActivitySpecial.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                mAceDnsDatabase.DeleteSurveyTempOutData();
                                new TRANS_SubmitSurveyTask(mContext, true, "SUBMIT").execute();
                                break;
                        }
                    }
                });
            }
        };


        mPrepareSurveyHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mPrepareSurveyProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SurveyActivitySpecial.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                if (mSurveyInputList.size() > 0) {
                                    Log.d("TAG", "run: DrawLayout");
                                    DrawLayout();
                                    if (false == mIsTittle) {
//                                        mTitleText.setText(mSubMenu);
                                    }

                                    if (mIsTableView) {
                                        mSurveyTableViewList = mAceDnsDatabase.GetSurveyTableView();
                                        if (mSurveyTableViewList.size() > 0) {

                                        } else {
                                            Toast.makeText(mContext, "Error in table view data.\nPlease Synchronize Data", Toast.LENGTH_SHORT).show();
                                        }
                                    }
                                } else {
                                    Toast.makeText(mContext, "Error in creating layout.Plese Retry..", Toast.LENGTH_SHORT).show();
                                }
                                break;
                            case 2:
                                if (isDependangtDataEmpty) {
                                    Utils.showToast(mContext, "Please select values for " + CurrentCheckBoxItemDependantOn);
                                    isDependangtDataEmpty = false;
                                    CurrentCheckBoxItemDependantOn = "";
                                } else if (mCategory == false && mSubCategory == true) {
                                    if (mCondition.trim().length() > 0) {
                                        if (values.length > 0) {
                                            ShowList(mType);
                                        } else {
                                            SetTextViewText(mFinalRowID, "");
                                            SetSurveyValue(mFinalRowID, "");
                                            Toast.makeText(mContext, "No record found", Toast.LENGTH_SHORT).show();
                                        }
                                    } else {
                                        if (mMessage.equalsIgnoreCase("0")) {
                                            Toast.makeText(mContext, "Please provide valid input", Toast.LENGTH_SHORT).show();
                                        } else {
                                            Toast.makeText(mContext, "Please select " + mMessage, Toast.LENGTH_SHORT).show();
                                        }
                                    }
                                } else {
                                    ShowList(mType);
                                }
                                break;

                            case 3:
                                PrepareSurveyData(1, mLayout);
                                break;

                            case 4:
                                ShowSingelDualList();
                                break;

                            case 5:
                                mSubtableInfo = "";
                                if (subvalues != null) {
                                    if (subvalues.length > 0) {
                                        ChangeMadatory(mSubRowID, "Y");
                                    } else {
                                        ChangeMadatory(mSubRowID, "N");
                                    }
                                }
                                break;
                            case 6:
                                if (values != null) {
                                    ShowList(mType);
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                }
                                break;

                            case 7:
                                Log.d("TAG", "mKeyValueList: 1");
                                if (mKeyValueList != null && mKeyValueList.size() > 0) {
                                    ShowList(mType, mDecision);
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                }
                                break;

                            case 8:
                                if (values != null) {
                                    ShowList(mType);
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                }
                                break;
                            case 9:
                                Log.d("TAG", "mKeyValueList: 2");
                                if (mKeyValueList != null && mKeyValueList.size() > 1) {
                                    ShowList(mType, mDecision);
                                } else if (mKeyValueList != null && mKeyValueList.size() == 1) {
                                    KeyValue obj = mKeyValueList.get(0);
                                    setValuesOfSurvey(obj, mDecision);
                                    grpDialogSubAction.cancel();
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                    grpDialogSubAction.cancel();
                                }
                                break;

                            case 10:
                                if (values != null) {
                                    ShowList(mParentType);
                                    mParentType = "";
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                }
                                break;
                            case 11:
                                Log.d("TAG", "mKeyValueList: 3");
                                if (mKeyValueList != null && mKeyValueList.size() > 0) {
                                    ShowList(mType, mDecision);
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                }
                                break;
                            case 12:
                                if (Constants.mSurveyMenuDetailsList.size() > 0) {
                                    addRadioButtonWithBottomBorder();
//                                    ShowList(mType, mDecision);
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                    finish();
                                }
                                break;
                            case 13:
                                Log.d("TAG", "mKeyValueList: 4");
                                if (mKeyValueList != null && mKeyValueList.size() > 0) {
                                    ShowHistoryViewList();
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                }
                                break;
                            case 14:
                                Log.d("TAG", "mKeyValueList: 5");
                                if (mKeyValueList != null && mKeyValueList.size() > 0) {
                                    ShowHistoryViewInputList();
                                } else {
                                    Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                }
                                break;

                        }
                    }
                });
            }
        };

        mButtonSubmit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                Log.d("TAG", "onClick: mButtonSubmit");
                boolean check = false;
                boolean checkvalidation = false;

                GetCheckBoxMatrixListContainer();

                GetRadioGroupMatrixListContainer();
                check = CheckSurveyMandatory();
                if (check) {
                    if (mEditTextList.size() > 0) {
                        for (EditText editText : mEditTextList) {
                            String tag = editText.getTag().toString();
                            String value = editText.getText().toString();
//                            boolean checksurveyvalue = SetSurveyValue(tag, value);
//                        if (checksurveyvalue == true) {
                            checkvalidation = CheckSurveyValidation(tag, value);
                            check = CheckSurveyMandatory();
                            if (true == isRound) {
                                SetSurveyValue(tag, mRoundValue);
                                isRound = false;
                            }
//                        }
                            if (checkvalidation == false) {
                                break;
                            }
                            if (tag.equalsIgnoreCase(mOTPRowID)) {
                                mOTPPhoneNo = value;
                            }

                        }
                        if (checkvalidation) {

                            if (check == true && checkvalidation == true) {
                                RemoveDuplicateData();
                                for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
                                    Constants.mFinalSurveyList.add(mInputTimeSurveyDetailsList.get(count));
                                }
                                if (InsertSurveyTemporary() == true) {
                                    if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                                        SetSurveyStatus();
                                        if (Constants.surveyFormDetailsObj.getSurveyOTP().equalsIgnoreCase("yes") && mIsOTP == true) {
                                            Constants.SurveyRowID = mOTPRowID;
                                            if (mOTPPhoneNo.equalsIgnoreCase(Constants.LIPLMOBILENO) == false) {
                                                new AUTH_GetOTP(mContext).execute(mOTPPhoneNo);
                                            } else {
                                                finish();
                                            }
                                        } else {
                                            finish();
                                        }
                                    } else {
                                        mButtonSubmit.setEnabled(false);
                                        SaveDatatoDatabase(1);
                                    }
                                }
                            }
                        }


                    } else {
                        check = CheckSurveyMandatory();
                        if (check == true) {
                            RemoveDuplicateData();
                            for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
                                Constants.mFinalSurveyList.add(mInputTimeSurveyDetailsList.get(count));
                            }
                            if (InsertSurveyTemporary() == true) {
                                if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                                    SetSurveyStatus();
                                    finish();
                                } else {
                                    mButtonSubmit.setEnabled(false);
                                    SaveDatatoDatabase(1);
                                }
                            }
                        }
                    }
                }


            }
        });


        if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes") && Constants.surveyFormDetailsObj.getSurveyMallSurveyRelation().equalsIgnoreCase("yes")) {
            PrepareSurveyData(3, mLayout);
        } else {
            PrepareSurveyData(1, mLayout);
        }
    }

    private void addRadioButtonWithBottomBorder() {
        RadioGroup rgp = (RadioGroup) findViewById(R.id.menuradiogrp);
        rgp.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                // checkedId is the RadioButton selected
                RadioButton rb = (RadioButton) findViewById(checkedId);
                mMenuID = rb.getTag().toString();
                Constants.isUpcoming = rb.getText().toString();
                mSurveyInputList = new ArrayList<SurveyInput>();
                mInputTimeSurveyDetailsList = new ArrayList<SurveyDetails>();
                mUndoSurveyList = new ArrayList<SurveyDetails>();
                mKeyValueImageList = new ArrayList<KeyValue>();

                mNonRepeat = new ArrayList<String>();
                InitializeView();
                mParentLayout.removeAllViews();
                mEditTextList = new ArrayList<EditText>();
                customerSpinner.setVisibility(View.GONE);
                viewSpinner.setVisibility(View.GONE);
                surveyActivityINitializationProcess();
            }
        });
        RadioGroup.LayoutParams rprms;
        for (int i = 0; i < Constants.mSurveyMenuDetailsList.size(); i++) {
            RadioButton radioButton = new RadioButton(this);
            radioButton.setText(Constants.mSurveyMenuDetailsList.get(i).getMenuName());
            radioButton.setId(i + 1);
            radioButton.setTypeface(null, Typeface.BOLD);
            radioButton.setTag(Constants.mSurveyMenuDetailsList.get(i).getMenuId());
            radioButton.setTextColor(getResources().getColor(R.color.black));
            rprms = new RadioGroup.LayoutParams(0, WindowManager.LayoutParams.WRAP_CONTENT, 1);
            rgp.addView(radioButton, rprms);
        }

    }

    /**
     * This method is called when the <b>Submit Button</b> pressed from the UI.\n
     * If the mandatory input is given, it set status "DONE" in layer list.
     */

    public void SetSurveyStatus() {
        for (int count = 0; count < Constants.mSurveyStatusList.size(); count++) {
            String layoutname = Constants.mSurveyStatusList.get(count).getSurveyLayout();
            if (layoutname.equalsIgnoreCase(mLayout)) {
                Constants.mSurveyStatusList.get(count).setStatus("DONE");
                break;
            }
        }
    }

    public void ShowHistoryViewList() {
        final Dialog mDetailsDialog = new Dialog(mContext, android.R.style.Theme_Black_NoTitleBar_Fullscreen);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_history_view_list);
        mDetailsDialog.setCancelable(false);
        Button back =  mDetailsDialog.findViewById(R.id.back);

        TextView txt_total = mDetailsDialog.findViewById(R.id.txt_total);
        if (mFinalRowID.equalsIgnoreCase("RA066"))//boq history
        {
            LinearLayout totalLayout = mDetailsDialog.findViewById(R.id.totalLayout);
            totalLayout.setVisibility(VISIBLE);
        }


        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
                if (mShowColumnCount == 6) {
//                    a.setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                }
            }
        });

        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(mDependentDisplayName);

        TextView textViewHeader =  mDetailsDialog.findViewById(R.id.textViewHeader);
        TextView txt_col2 =  mDetailsDialog.findViewById(R.id.txt_col2);
        TextView txt_col3 =  mDetailsDialog.findViewById(R.id.txt_col3);
        TextView txt_col4 =  mDetailsDialog.findViewById(R.id.txt_col4);
        TextView txt_col5 =  mDetailsDialog.findViewById(R.id.txt_col5);
        TextView txt_col6 =  mDetailsDialog.findViewById(R.id.txt_col6);
        FrameLayout col4 = (FrameLayout) mDetailsDialog.findViewById(R.id.col4);
        FrameLayout col5 = (FrameLayout) mDetailsDialog.findViewById(R.id.col5);
        FrameLayout col6 = (FrameLayout) mDetailsDialog.findViewById(R.id.col6);
        textViewHeader.setText(mShowColumn1.replace("_", " ").toUpperCase());
        txt_col2.setText(mShowColumn2.replace("_", " ").toUpperCase());
        txt_col3.setText(mShowColumn3.replace("_", " ").toUpperCase());
        if (mShowColumnCount == 6) {
            col4.setVisibility(VISIBLE);
            col5.setVisibility(VISIBLE);
            col6.setVisibility(VISIBLE);
            txt_col4.setText(mShowColumn4.replace("_", " ").toUpperCase());
            txt_col5.setText(mShowColumn5.replace("_", " ").toUpperCase());
            txt_col6.setText(mShowColumn6.replace("_", " ").toUpperCase());
//            a.setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
        }


		/*TextView textViewMessage =  mDetailsDialog.findViewById(R.id.textViewMessage);
		textViewMessage.setText(datewiserecord);*/

        ListView dialogList =  mDetailsDialog.findViewById(R.id.listdata);
        Log.d("TAG", "mKeyValueList: 6");
        final HistoryViewAdapter orderreportAdapter = new HistoryViewAdapter(mContext, R.layout.history_view_list_item, mKeyValueList, mShowColumnCount);
        dialogList.setAdapter(orderreportAdapter);
        if (mFinalRowID.equalsIgnoreCase("RA066"))//boq history
        {
            Double total = 0.00;
            Log.d("TAG", "mKeyValueList: 7");
            for (int i = 0; i < mKeyValueList.size(); i++) {
                String totalVal = mKeyValueList.get(i).getmmShowColumn6();
                if (Utils.isNumeric(totalVal)) {
                    total = total + Double.parseDouble(totalVal);
                }
            }

            txt_total.setText(defaultFormatWithComma.format(total));
        }


        mDetailsDialog.show();

    }

    public void ShowHistoryViewInputList() {
        final Dialog mDetailsDialog = new Dialog(mContext, android.R.style.Theme_Black_NoTitleBar_Fullscreen);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_history_view_input_list);
        mDetailsDialog.setCancelable(false);
        Button back =  mDetailsDialog.findViewById(R.id.back);
        Button btn_submit =  mDetailsDialog.findViewById(R.id.btn_submit);
        TextView textviewTitleName = mDetailsDialog.findViewById(R.id.textviewTitleName);
        textviewTitleName.setText(finalDisplay);

        back.setOnClickListener(v -> mDetailsDialog.cancel());
        btn_submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
                String displayText = "", sendText = "";
                for (int i = 0; i < HistoryViewInputAdapter.mOrderReportDetailsList.size(); i++) {
                    String enteredValue = HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getEnteredValue();
                    if (Utils.isNumeric(enteredValue)) {
                        if (displayText.equalsIgnoreCase("")) {
                            displayText = HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getmShowColumn2() + "#" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getmShowColumn4() + "#" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getEnteredValue();
                            sendText = HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getmShowColumn1() + "#" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getmShowColumn4() + "#" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getEnteredValue();
                        } else {
                            displayText = displayText + ";" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getmShowColumn2() + "#" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getmShowColumn4() + "#" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getEnteredValue();
                            sendText = sendText + ";" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getmShowColumn1() + "#" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getmShowColumn4() + "#" + HistoryViewInputAdapter.mOrderReportDetailsList.get(i).getEnteredValue();
                        }
                    }
                }
                SetSurveyValue(mFinalRowID, sendText);
                SetTextViewText(mFinalRowID, displayText);

            }
        });

        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(mDependentDisplayName);

        ListView dialogList =  mDetailsDialog.findViewById(R.id.listdata);
        Log.d("TAG", "mKeyValueList: 8");
        final HistoryViewInputAdapter orderreportAdapter = new HistoryViewInputAdapter(mContext, R.layout.history_view_input_list_item, mKeyValueList);
        dialogList.setAdapter(orderreportAdapter);

        mDetailsDialog.show();

    }

    public void ShowSingleCoumnViewListWithKeyValue(String displayHeader, ArrayList<KeyValue> displayList) {
        final Dialog mDetailsDialog = new Dialog(mContext, android.R.style.Theme_Black_NoTitleBar_Fullscreen);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_history_view_list);
        mDetailsDialog.setCancelable(false);
        Button back =  mDetailsDialog.findViewById(R.id.back);


        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
            }
        });

        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.textviewTitleName);
        textViewTitleName.setText(displayHeader);
        FrameLayout col1 = (FrameLayout) mDetailsDialog.findViewById(R.id.col1);
        FrameLayout col2 = (FrameLayout) mDetailsDialog.findViewById(R.id.col2);
        FrameLayout col3 = (FrameLayout) mDetailsDialog.findViewById(R.id.col3);
        col1.setVisibility(View.GONE);
        col2.setVisibility(View.GONE);
        col3.setVisibility(View.GONE);


        ListView dialogList =  mDetailsDialog.findViewById(R.id.listdata);

        final SingleItemAdapter orderreportAdapter = new SingleItemAdapter(mContext, R.layout.history_view_list_item, displayList);
        dialogList.setAdapter(orderreportAdapter);

        mDetailsDialog.show();

    }

    /**
     * This method is used to delete the duplicate data from the Collection Buffer
     */
    public void RemoveDuplicateData() {
        String rowid = "";
        for (int count = 0; count < mSurveyInputList.size(); count++) {
            rowid = mSurveyInputList.get(count).getSurveyRowId();
            for (int removeindex = 0; removeindex < Constants.mFinalSurveyList
                    .size(); removeindex++) {
                if (rowid.trim().equalsIgnoreCase(
                        Constants.mFinalSurveyList.get(removeindex).getRowId()
                                .trim())) {
                    Constants.mFinalSurveyList.remove(removeindex);
                }
            }
        }
    }

    /**
     * This method is used to insert temporary data to local database
     */
    public boolean InsertSurveyTemporary() {
        boolean issucess = false;
        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
            mAceDnsDatabase.DeleteSurveyTempOutData(mLayout);
            issucess = mAceDnsDatabase.INSERTToSurveyTempOutData(mInputTimeSurveyDetailsList, mLayout);
        } else {
            mAceDnsDatabase.DeleteSurveyTempOutData();
            issucess = mAceDnsDatabase.INSERTToSurveyTempOutData(mInputTimeSurveyDetailsList, "");
        }
        return issucess;
    }

    //    1=>task
    public void SaveDatatoDatabase(final int task) {
        gpstracker = new GPSTracker(mContext);
        mPrepareSurveyProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyProgressDialog.setMessage("Saving data to database.\n Please wait...");
        mPrepareSurveyProgressDialog.setCancelable(false);
        mPrepareSurveyProgressDialog.show();
        new Thread() {
            public void run() {

                SaveSurveyDataTODatabase();
                Message msg = mPrepareDataSaveHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareDataSaveHandler.sendMessage(msg);
            }
        }.start();
    }

    /**
     * This Function is used to save the <b>Survey Data</b> to <b>Local Database</b>.
     */
    public void SaveSurveyDataTODatabase() {

        String timeStamp = "";
        timeStamp = Constants.dateString
                + new SimpleDateFormat("HHmmss").format(Calendar
                .getInstance().getTime());

        mAceDnsTransactionDatabase.INSERTtoSurveyOutputWithSpecialValues(timeStamp, "SU");
        String trans_id = "SU" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        if (Constants.shouldUpdateCustomerMasterWithEmail) {
            HashMap<String, String> customerCodeEmailPhone = new HashMap<>();
            customerCodeEmailPhone.put("code", "");
            customerCodeEmailPhone.put("email", "");
            customerCodeEmailPhone.put("phone", "");
            for (int count = 0; count < Constants.mFinalSurveyList.size(); count++) {
                SurveyDetails masterObj = Constants.mFinalSurveyList.get(count);
                String rowId = masterObj.getRowId();
                String Value = masterObj.getValue();
                if (rowId.matches("RA001")) {
                    customerCodeEmailPhone.put("code", Value);
                } else if (rowId.matches("RA002")) {
                    customerCodeEmailPhone.put("email", Value);
                } else if (rowId.matches("RA003")) {
                    customerCodeEmailPhone.put("phone", Value);
                }
            }
            mAceDnsTransactionDatabase.updateCustomerEmailPhone(customerCodeEmailPhone);
        }

        mAceDnsTransactionDatabase.InsertSurveyHeader(trans_id, Constants.mSurveyMainType, mMenuID, "", "", "", "", "", "", Constants.mSurveyRouteCode);
        mAceDnsTransactionDatabase.insertToLocationTable("SU", timeStamp);
        gpstracker.stopUsingGPS();

    }

    /**
     * This method is used set survey value of particular <b>rowID</b>
     *
     * @param rowID rowid of the layer
     * @param value user input value
     * @return true if successfully added
     */
    public boolean SetSurveyValue(String rowID, String value) {
        String rowid = "";
        boolean isSucess = true;
        boolean isvalue = false;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            if (isvalue == false) {
                rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
                if (rowID.equalsIgnoreCase(rowid)) {
                    mEditType = mInputTimeSurveyDetailsList.get(count).getType();
                    if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("Y")) {
                        if (value.length() > 0) {
                            mInputTimeSurveyDetailsList.get(count).setValue(value);
                        } else {
                            mInputTimeSurveyDetailsList.get(count).setValue(value);
                            isSucess = false;
                        }
                    } else {
                        mInputTimeSurveyDetailsList.get(count).setValue(value);
                    }
                    isvalue = true;
                    break;
                }
            }
        }
        return isSucess;
    }

    //check if other row values are depending on this or not
    public boolean isCurrentRowIndependent(String rowID) {
        String rowidCurrentItem = "";
        boolean isIndependentValue = false;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            SurveyDetails surveyDetailsCurrentItem = mInputTimeSurveyDetailsList.get(count);

            rowidCurrentItem = surveyDetailsCurrentItem.getRowId();
            if (rowID.equalsIgnoreCase(rowidCurrentItem)) {
                if (surveyDetailsCurrentItem.getinsert_table_detail().equalsIgnoreCase("independent")) {
                    isIndependentValue = true;
                    break;
                }
            }
        }
        return isIndependentValue;
    }

    public boolean SetSurveyValueToMasterViewEditDependingOnIndependentView(String rowID, String value) {
        String rowidCurrentItem = "";
        boolean isIndependentValue = false;
        if (Constants.nickName.equalsIgnoreCase("DURO") && rowID.equalsIgnoreCase("RA047")) {
            Log.d("TAG", "ABCD: 1");
            String mVal = getSurveyValueByRowId(value);
            if (!mVal.equalsIgnoreCase("yes")) {
                SetMandatoryChangeBlank("RA230", "N");
                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                    TextView currentButton = mTextViewList.get(tx);
                    if (currentButton.getTag().equals("RA230")) {
                        currentButton.setVisibility(GONE);
                    }
                }
                for (int tx = 0; tx < mButtonList.size(); tx++) {
                    Button currentButton = mButtonList.get(tx);
                    if (currentButton.getTag().equals("RA230")) {
                        currentButton.setVisibility(GONE);
                    }
                }
                for (int z = 0; z < mButtonList.size(); z++) {
                    Button currentButton = mButtonList.get(z);
                    if (currentButton.getTag().equals("RA230")) {
                        currentButton.setVisibility(GONE);
                    }
                }
            }
            else {
                for (int z = 0; z < mButtonList.size(); z++) {
                    Button currentButton = mButtonList.get(z);
                    if (currentButton.getTag().equals("RA230")) {
                        currentButton.setVisibility(VISIBLE);
                    }
                }
                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                    TextView currentButton = mTextViewList.get(tx);
                    if (currentButton.getTag().equals("RA230")) {
                        currentButton.setVisibility(VISIBLE);
                    }
                }
            }
        }

        if (rowID.equalsIgnoreCase("RA728")) {
            String hide = getValidationByRowId(mFinalRowID);
            String mDate = getValueById(value);
            Log.d("TAG", "ABCD: 2: "+hide);
            Log.d("TAG", "ABCD: 2: "+mDate);
            if (hide.contains("days:")) {
                String[] hideArray = hide.split("&");
                String[] hideArray1 = hideArray[0].split(":");
                String rows = hideArray1[1];
                String[] rowArray = rows.split(";");
                boolean isRowDone = true;
                for (int i = 0; i < rowArray.length; i++) {
                    String row = "", field = "";
                    String mandatoty = "N";
                    int dys = 0;
                    int comparisonResult = 0;

                    if (rowArray[i].contains(",")) {
                        String[] row1Array = rowArray[i].split(",");
                        row = row1Array[0];
                        mandatoty = row1Array[1];
                        dys = Integer.parseInt(row1Array[2]);
                        field = row1Array[3];
                    }
                    else {
                        row = rowArray[i];
                        mandatoty = "Y";
                    }

                    boolean isRow = true;
                    Date c = Calendar.getInstance().getTime();
                    SimpleDateFormat df = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
                    String formattedDate = df.format(c);
                    Log.d("TAG", "abc: "+formattedDate);
                    try {
                        comparisonResult = new Date().compareTo(df.parse(mDate));
                        comparisonResult = Integer.parseInt(getDateDiff(mDate, formattedDate));
                        Log.d("TAG", "abc: "+comparisonResult);
                    } catch (ParseException e) {}

                    if (comparisonResult < dys && isRowDone) {
                        isRow = true;
                    }
                    else {
                        isRow = false;
                    }

                    if (isRow) {
                        Log.d("TAG", "abc: "+mandatoty+"  "+isRow);
                        SetMandatoryChangeYes(row, mandatoty);
                        for (int tx = 0; tx < mEditTextList.size(); tx++) {
                            EditText currentButton = mEditTextList.get(tx);
                            if (currentButton.getTag().equals(row)) {
                                String mData = getValueByFiId(field, value);
                                Log.d("TAG", "aaa: "+mData);
                                if (mData.trim().isEmpty()) {
                                    Log.d("TAG", "bbb: hit");
                                    currentButton.setEnabled(true);
                                }
                                else {
                                    Log.d("TAG", "ccc: hit");
                                    currentButton.setEnabled(false);
                                }
                                String todate = getDateValAdd(mDate, dys + "");
                                currentButton.setHint(" Up to " + todate);
                            }
                        }
                        isRowDone = false;
                    }
                    else {
                        Log.d("TAG", "abc: N  "+isRow);
                        SetMandatoryChangeBlank(row, "N");
                        for (int tx = 0; tx < mEditTextList.size(); tx++) {
                            EditText currentButton = mEditTextList.get(tx);
                            if (currentButton.getTag().equals(row)) {
                                currentButton.setEnabled(false);
                                String todate = getDateValAdd(mDate, dys + "");
                                currentButton.setHint(" Up To " + todate);
                            }
                        }
                    }
                }
            }
        }

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            Log.d("TAG", "ABCD: 3");
            SurveyDetails surveyDetailsCurrentItem = mInputTimeSurveyDetailsList.get(count);

            rowidCurrentItem = surveyDetailsCurrentItem.getRowId();
            String insertTableDetail = surveyDetailsCurrentItem.getinsert_table_detail();
            if (insertTableDetail.contains("masterviewedit") || insertTableDetail.contains("masterviewdisplay"))//demo value...  masterviewedit#site_master#address#site_id;RA024
            {
                String[] insertTableDetailsSplitted = insertTableDetail.split("#");
                if (insertTableDetailsSplitted.length == 4) {
                    String[] currentWhereCondition = insertTableDetailsSplitted[3].split(";");// demo... site_id;RA024
                    if (currentWhereCondition.length == 2 && currentWhereCondition[1].equalsIgnoreCase(rowID)) {
                        String tableName = insertTableDetailsSplitted[1];

                        String columnNameToSelect = insertTableDetailsSplitted[2];
                        String columnNameWhere = currentWhereCondition[0];
                        String type = surveyDetailsCurrentItem.getType().trim();
                        if (type.equalsIgnoreCase("double") || type.equalsIgnoreCase("") || type.equalsIgnoreCase(" ")) {
                            String sqlQuery = "Select " + columnNameToSelect + " From " + tableName + " Where " + columnNameWhere + "='" + value + "'";
                            String defaultValue = mAceDnsDatabase.GetSurveyValueFromQuery(sqlQuery);
                            if (insertTableDetail.contains("masterviewedit")) {
                                SetEditTextTextAndDisableIfNeeded(surveyDetailsCurrentItem.getRowId(), defaultValue, false);
                            } else {
                                SetEditTextTextAndDisableIfNeeded(surveyDetailsCurrentItem.getRowId(), defaultValue, true);
                            }

                        }
                        if (type.equalsIgnoreCase("floor")) {
                            String sqlQuery = "Select " + columnNameToSelect + " From " + tableName + " Where " + columnNameWhere + "='" + value + "'";
                            String defaultValue = mAceDnsDatabase.GetSurveyValueFromQuery(sqlQuery);
                            if (surveyDetailsCurrentItem.getValidation().trim().length() > 1) {
                                if (insertTableDetail.contains("masterviewedit")) {
                                    SetEditTextTextAndDisableIfNeeded(surveyDetailsCurrentItem.getRowId(), defaultValue, false);
                                } else {
                                    SetEditTextTextAndDisableIfNeeded(surveyDetailsCurrentItem.getRowId(), defaultValue, true);
                                }
                            } else {
                                SetTextViewText(surveyDetailsCurrentItem.getRowId(), defaultValue);
                            }
                        } else if (type.equalsIgnoreCase("masterview")) {
                            if (Constants.nickName.equalsIgnoreCase("DURO") && rowID.equalsIgnoreCase("RA230")) {
                                String mVal = getSurveyValueByRowId("RA229");
                                if (!mVal.equalsIgnoreCase("yes")) {
                                    SetMandatoryChangeBlank("RA230", "N");
                                    for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                        TextView currentButton = mTextViewList.get(tx);
                                        if (currentButton.getTag().equals("RA230")) {
                                            currentButton.setVisibility(GONE);
                                            //SetTextViewTextBlank(row);

                                        }
                                    }

                                    for (int tx = 0; tx < mButtonList.size(); tx++) {
                                        Button currentButton = mButtonList.get(tx);
                                        if (currentButton.getTag().equals("RA230")) {
                                            currentButton.setVisibility(GONE);
                                            //SetTextViewTextBlank(row);

                                        }
                                    }
                                    for (int z = 0; z < mButtonList.size(); z++) {
                                        Button currentButton = mButtonList.get(z);
                                        if (currentButton.getTag().equals("RA230")) {
                                            currentButton.setVisibility(GONE);

                                            // break;
                                        }
                                    }
                                }
                            }

                            masterVIewDataLoadingProcess(rowidCurrentItem, false);
                            Log.d("TAG", "mKeyValueList: 9");
                            mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7(mTableName, mSendColumn, mShowColumn, mCustomerSelectionBasis, mCustomerSelectionBasisFilter);
                            if (mKeyValueList != null && mKeyValueList.size() > 0) {
                                Log.d("TAG", "mKeyValueList: 10");
                                String sqlQuery = "Select " + columnNameToSelect + " From " + tableName + " Where " + columnNameWhere + "='" + value + "'";
                                String defaultValueSendFromLocal = mAceDnsDatabase.GetSurveyValueFromQuery(sqlQuery);
                                String defaultShowValue = "", defaultSendValue = "";
                                if (defaultValueSendFromLocal.contains(";")) {
                                    Log.d("TAG", "mKeyValueList: 11");
                                    String[] defaultValueSplitted = defaultValueSendFromLocal.split(";");
                                    List<String> efaultValueSplittedList = new ArrayList<String>(Arrays.asList(defaultValueSplitted));

                                    for (int i = 0; i < mKeyValueList.size(); i++) {
                                        Log.d("TAG", "mKeyValueList: 12 "+i);
                                        if (efaultValueSplittedList.contains(mKeyValueList.get(i).getKey())) {
                                            Log.d("TAG", "mKeyValueList: 13");
                                            if (defaultShowValue.contains(";")) {
                                                Log.d("TAG", "mKeyValueList: 14");
                                                defaultShowValue = defaultShowValue + mKeyValueList.get(i).getValue() + ";";
                                                defaultSendValue = defaultSendValue + mKeyValueList.get(i).getKey() + ";";
                                            } else {
                                                Log.d("TAG", "mKeyValueList: 15");
                                                defaultShowValue = mKeyValueList.get(i).getValue() + ";";
                                                defaultSendValue = mKeyValueList.get(i).getKey() + ";";
                                            }
                                        }
                                    }
                                } else if (defaultValueSendFromLocal.trim().length() > 1) {
                                    Log.d("TAG", "mKeyValueList: 16");

                                    for (int i = 0; i < mKeyValueList.size(); i++) {
                                        Log.d("TAG", "mKeyValueList: 17 "+i);
                                        if (defaultValueSendFromLocal.matches(mKeyValueList.get(i).getKey())) {
                                            Log.d("TAG", "mKeyValueList: 18");

                                            defaultShowValue = mKeyValueList.get(i).getValue();
                                            defaultSendValue = mKeyValueList.get(i).getKey();
                                            break;
                                        }
                                    }
//                                    defaultShowValue=defaultValueSendFromLocal;
//                                    defaultSendValue=defaultValueSendFromLocal;
                                }

                                SetTextViewText(rowidCurrentItem, defaultShowValue);
                                SetSurveyValue(rowidCurrentItem, defaultSendValue);
                                if (insertTableDetail.contains("masterviewdisplay")) {
                                    disableButton(surveyDetailsCurrentItem.getRowId());
                                }
                            }
                        } else if (type.equalsIgnoreCase("tableview") || type.equalsIgnoreCase("date") || type.equalsIgnoreCase("multileveltableview"))//masterviewedit#site_master#address#site_id;RA024
                        {
                            String tableName2 = "", columnNameToJoin = "", columnNameToDisplay = "";
                            if (tableName.contains("&")) {
                                String[] tableNameSplitted = tableName.split("&");
                                tableName = tableNameSplitted[0];
                                tableName2 = tableNameSplitted[1];
                            }
                            if (columnNameToSelect.contains("&")) {
                                String[] columnNameToSelectSplitted = columnNameToSelect.split("&");
                                columnNameToSelect = columnNameToSelectSplitted[0];
                                columnNameToJoin = columnNameToSelectSplitted[1];
                                columnNameToDisplay = columnNameToSelectSplitted[2];

                            }
                            String sqlQuery = "Select " + columnNameToSelect + " From " + tableName + " Where " + columnNameWhere + "='" + value + "'";

                            String defaultValue = mAceDnsDatabase.GetSurveyValueFromQuery(sqlQuery);
                            String defaultValueShow = defaultValue;
                            if (defaultValueShow.endsWith(":")) {
                                defaultValueShow = defaultValueShow.substring(0, defaultValueShow.length() - 1);
                            }
                            if (!tableName2.matches("") && !columnNameToJoin.matches("") && !columnNameToDisplay.matches("")) {
                                if (defaultValueShow.contains(":")) {
                                    String[] ShowValueSplitted = defaultValueShow.split(":");
                                    String sqlQuery2 = "Select " + columnNameToDisplay + " From " + tableName2 + " Where " + columnNameToJoin + "='" + ShowValueSplitted[1] + "'";
                                    defaultValueShow = mAceDnsDatabase.GetSurveyValueFromQuery(sqlQuery2);
                                    defaultValueShow = ShowValueSplitted[0] + ":" + defaultValueShow;
                                }

                            }
                            SetTextViewText(rowidCurrentItem, defaultValueShow);
                            SetSurveyValue(rowidCurrentItem, defaultValue);
                            if (insertTableDetail.contains("masterviewdisplay")) {
                                disableButton(surveyDetailsCurrentItem.getRowId());
                            }
                        }

                    }
                }

            }

        }

        return isIndependentValue;
    }

    public boolean EnableDisableHistoryView(String rowID, String value) {
        String rowidCurrentItem = "";
        boolean isIndependentValue = false;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            SurveyDetails surveyDetailsCurrentItem = mInputTimeSurveyDetailsList.get(count);
//            if(surveyDetailsCurrentItem.getType().equalsIgnoreCase("historyview") || )
//            {
            String validation = surveyDetailsCurrentItem.getValidation();
            String rowIdCurrent = surveyDetailsCurrentItem.getRowId();
            if (validation.contains("#")) {
                String[] validationArray = validation.split("#");
                if (validationArray.length == 2) {
                    String rowid = validationArray[0];
                    if (rowID.equalsIgnoreCase(rowid)) {
                        String desiredValue = validationArray[1];
                        if (value.equalsIgnoreCase(desiredValue))//enable historyview
                        {
                            makeButtonVisibleOrInvisible(rowIdCurrent, 1);
                        } else {
                            makeButtonVisibleOrInvisible(rowIdCurrent, 2);
                        }
                    }

                }
//                }
            }
        }
        return isIndependentValue;
    }

    /**
     * This method is used to draw the dynamic layer
     */
    @SuppressWarnings("deprecation")
    public void DrawLayout() {
        String type = "";
        String rowid = "";
        String displayname = "";
        String tablename = "";
        String mandatory = "";
        String actionId = "";
        String action = "";
        String validation = "";
        String insert_table_detail = "";

        LayoutParams Params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        mButtonList = new ArrayList<Button>();
        mRadioGrpList = new ArrayList<>();

        for (int count = 0; count < mSurveyInputList.size(); count++) {
            SurveyDetails mSurveyDetails = new SurveyDetails();
            LinearLayout childlayout = new LinearLayout(this);
            childlayout.setLayoutParams(Params);
            childlayout.setPadding(0, 0, 0, 5);
            childlayout.setOrientation(LinearLayout.VERTICAL);

            type = mSurveyInputList.get(count).getSurveyType();
            rowid = mSurveyInputList.get(count).getSurveyRowId();
            displayname = mSurveyInputList.get(count).getSurveyDisplayName();
            tablename = mSurveyInputList.get(count).getSurveyTableName();
            mandatory = mSurveyInputList.get(count).getSurveyMadatory();
            actionId = mSurveyInputList.get(count).getSurveyActionId();
            action = mSurveyInputList.get(count).getSurveyAction();
            validation = mSurveyInputList.get(count).getSurveyValidation();
            insert_table_detail = mSurveyInputList.get(count).getinsert_table_detail();

            if (rowid.equalsIgnoreCase("RA060"))//quotaion generated
            {
                Utils.ShowAlertDialogCommon(mContext, "Alert!", "On choosing Quotation generated yes, this form will be disabled", "OK");
            }

            mSurveyDetails.setRowId(rowid);
            mSurveyDetails.setType(type);
            mSurveyDetails.setMandatory(mandatory);
            mSurveyDetails.setActionId(actionId);
            mSurveyDetails.setAction(action);
            mSurveyDetails.setValidation(validation);
            mSurveyDetails.setDisplayName(displayname);
            mSurveyDetails.setinsert_table_detail(insert_table_detail);

            Log.d("TAG", "DrawLayout: "+count+"   "+rowid);

            if (mandatory.equalsIgnoreCase("Y")) {
                displayname = displayname + "<font color='red'>*</font>";
            }
            else {
                displayname = displayname;
            }


            if (type.equalsIgnoreCase("radio")) {
                childlayout.addView(NewtextView(rowid));
                if (displayname.contains("#")) {
                    String[] finaldisplayname = displayname.split("\\#");
                    displayname = finaldisplayname[0];
                }

                if (CheckPreDefinedData(rowid)) {
                    FillPreDefinedData(mMallColumnName);
                    mMallColumnName = "";
                    SetSurveyValue(rowid, mPredefinedValue);
                    SetTextViewText(rowid, mPredefinedValue);
                    if (mPredefinedValue.trim().length() > 0) {
                        mIsButtonEnable = false;
                    }
                    mPredefinedValue = "";
                }


                AddNewButton(displayname, String.valueOf(rowid));
                if (action.equalsIgnoreCase("click")) {
                    AddNewImageView(rowid);
                }
                mSurveyDetails.setTableName(tablename);

                Log.d("TAG", "radio: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("imageview")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);

                Log.d("TAG", "imageview: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("heading")) {
                childlayout.addView(NewtextViewHeading(rowid, displayname, 17f));

                Log.d("TAG", "heading: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("bool")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));

                Log.d("TAG", "bool: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("date")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));

                Log.d("TAG", "date: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("dynamicview")) {
                LayoutParams childlayoutparam = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);


                LinearLayout tabchildlayoutheader = new LinearLayout(this);
                tabchildlayoutheader.setPadding(1, 2, 1, 0);
                tabchildlayoutheader.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutheader.setLayoutParams(childlayoutparam);
                tabchildlayoutheader.setBackgroundColor(Color.parseColor("#DCE8F6"));

                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(Html.fromHtml(displayname));
                textviewdisplayname.setTextColor(Color.BLUE);
                textviewdisplayname.setTypeface(null, Typeface.BOLD);
                textviewdisplayname.setPadding(2, 0, 0, 2);
                tabchildlayoutheader.addView(textviewdisplayname);
                mParentLayout.addView(tabchildlayoutheader);

                LayoutParams qtparam = new LayoutParams(0, LayoutParams.WRAP_CONTENT, 24f);

                //XAxis design

                LinearLayout tabchildlayoutx = new LinearLayout(this);
                tabchildlayoutx.setPadding(1, 2, 1, 0);
                tabchildlayoutx.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutx.setLayoutParams(childlayoutparam);
                tabchildlayoutx.setBackgroundColor(Color.parseColor("#DCE8F6"));

                mEdiTextDynamic = new EditText(this);
                mEdiTextDynamic.setTag(rowid);
                mEdiTextDynamic.setLayoutParams(qtparam);
                mEdiTextDynamic.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                mEdiTextDynamic.setSingleLine(false);
                mEdiTextDynamic.setImeOptions(EditorInfo.IME_ACTION_NEXT);
                mEdiTextDynamic.setTextColor(Color.GRAY);
                mEdiTextDynamic.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
                mEdiTextDynamic.setTypeface(null, Typeface.NORMAL);
                mEdiTextDynamic.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
                mEdiTextDynamic.setHintTextColor(Color.LTGRAY);
                mEdiTextDynamic.setHint("Enter Value");
                tabchildlayoutx.addView(mEdiTextDynamic);


                Button button = new Button(mContext);
                button.setLayoutParams(qtparam);
                button.setTag(rowid);
                button.setHorizontallyScrolling(true);
                button.setGravity(Gravity.CENTER);
                button.setText("OK");
                button.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
                button.setSingleLine(false);
                button.setOnClickListener(this);
                mButtonList.add(button);
                tabchildlayoutx.addView(button);
                childlayout.addView(tabchildlayoutx);

                Log.d("TAG", "dynamicview: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("radiomatrix")) {

                LayoutParams childlayoutparam = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);


                LinearLayout tabchildlayoutheader = new LinearLayout(this);
                tabchildlayoutheader.setPadding(1, 2, 1, 0);
                tabchildlayoutheader.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutheader.setLayoutParams(childlayoutparam);
                tabchildlayoutheader.setBackgroundColor(Color.parseColor("#DCE8F6"));

                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(Html.fromHtml(displayname));
                textviewdisplayname.setTextColor(Color.BLUE);
                textviewdisplayname.setTypeface(null, Typeface.BOLD);
                textviewdisplayname.setPadding(2, 0, 0, 2);
                tabchildlayoutheader.addView(textviewdisplayname);
                mParentLayout.addView(tabchildlayoutheader);


                //String xAxis="Yes:No";
                String xAxis = ":";
                String[] xVals = xAxis.split("\\:");
                String[] yVals = action.split("\\:");

                LayoutParams qtparam = new LayoutParams(0, LayoutParams.WRAP_CONTENT, 24f);

                //XAxis design

                LinearLayout tabchildlayoutx = new LinearLayout(this);
                tabchildlayoutx.setPadding(1, 2, 1, 0);
                tabchildlayoutx.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutx.setLayoutParams(childlayoutparam);
                tabchildlayoutx.setBackgroundColor(Color.parseColor("#DCE8F6"));


                TextView textvieblank = new TextView(this);
                textvieblank.setText("    ");
                textvieblank.setTextColor(Color.parseColor("#003399"));
                textvieblank.setLayoutParams(qtparam);
                tabchildlayoutx.addView(textvieblank);

                for (int xAxixCount = 0; xAxixCount < xVals.length; xAxixCount++) {

                    TextView textviewheader = new TextView(this);
                    textviewheader.setText(xVals[xAxixCount]);
                    textviewheader.setTextColor(Color.parseColor("#003399"));
                    textviewheader.setLayoutParams(qtparam);
                    tabchildlayoutx.addView(textviewheader);

                }
                childlayout.addView(tabchildlayoutx);
                //YAxis design

                mMatrixRadioGroupList = new ArrayList<RadioGroup>();


                for (int yAxisCount = 0; yAxisCount < yVals.length; yAxisCount++) {

                    LinearLayout tabchildlayouty = new LinearLayout(this);
                    tabchildlayouty.setPadding(1, 2, 1, 0);
                    tabchildlayouty.setOrientation(LinearLayout.HORIZONTAL);
                    tabchildlayouty.setLayoutParams(childlayoutparam);
                    tabchildlayouty.setBackgroundColor(Color.parseColor("#DCE8F6"));

                    TextView textviewyName = new TextView(this);
                    textviewyName.setText(yVals[yAxisCount]);
                    textviewyName.setTextColor(Color.parseColor("#003399"));
                    textviewyName.setLayoutParams(qtparam);
                    tabchildlayouty.addView(textviewyName);

                    RadioGroup rgp = new RadioGroup(this);
                    rgp.setOrientation(RadioGroup.HORIZONTAL);
                    RadioGroup.LayoutParams rprms;

                    for (int i = 0; i < 2; i++) {
                        radioButton = new RadioButton(this);
                        if (i == 0) {
                            radioButton.setText("Y");
                            radioButton.setTag(String.valueOf(yAxisCount));
                        } else {
                            radioButton.setText("N");
                            radioButton.setTag(String.valueOf(yAxisCount));
                        }
                        //radioButton.setOnClickListener(this);
                        radioButton.setTextColor(Color.BLUE);
                        radioButton.setLayoutParams(qtparam);
                        rprms = new RadioGroup.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);
                        rgp.addView(radioButton, rprms);
                    }
                    tabchildlayouty.addView(rgp);
                    mMatrixRadioGroupList.add(rgp);
                    childlayout.addView(tabchildlayouty);
                }
                mRadioGroupMatrixListContainer.put(rowid, mMatrixRadioGroupList);

                Log.d("TAG", "radiomatrix: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("checkboxmatrix")) {

                //childlayout.addView(NewtextView(rowid));

                LayoutParams childlayoutparam = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);


                LinearLayout tabchildlayoutheader = new LinearLayout(this);
                tabchildlayoutheader.setPadding(1, 2, 1, 0);
                tabchildlayoutheader.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutheader.setLayoutParams(childlayoutparam);
                tabchildlayoutheader.setBackgroundColor(Color.parseColor("#DCE8F6"));

                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(Html.fromHtml(displayname));
                textviewdisplayname.setTextColor(Color.BLUE);
                textviewdisplayname.setTypeface(null, Typeface.BOLD);
                textviewdisplayname.setPadding(2, 0, 0, 2);
                tabchildlayoutheader.addView(textviewdisplayname);
                mParentLayout.addView(tabchildlayoutheader);

                String[] xyAxis = action.split("\\#");
                String xAxis = xyAxis[0];
                String yAxis = xyAxis[1];
                String[] xVals = xAxis.split("\\:");
                String[] yVals = yAxis.split("\\:");

                //LinearLayout.LayoutParams childlayoutparam = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT,100f);
                LayoutParams qtparam = new LayoutParams(0, LayoutParams.WRAP_CONTENT, 24f);

                //XAxis design

                LinearLayout tabchildlayoutx = new LinearLayout(this);
                tabchildlayoutx.setPadding(1, 2, 1, 0);
                tabchildlayoutx.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutx.setLayoutParams(childlayoutparam);
                tabchildlayoutx.setBackgroundColor(Color.parseColor("#DCE8F6"));


                TextView textvieblank = new TextView(this);
                textvieblank.setText("    ");
                textvieblank.setTextColor(Color.parseColor("#003399"));
                textvieblank.setLayoutParams(qtparam);
                tabchildlayoutx.addView(textvieblank);

                for (int xAxixCount = 0; xAxixCount < xVals.length; xAxixCount++) {

                    TextView textviewheader = new TextView(this);
                    textviewheader.setText(xVals[xAxixCount]);
                    textviewheader.setTextColor(Color.parseColor("#003399"));
                    textviewheader.setLayoutParams(qtparam);
                    tabchildlayoutx.addView(textviewheader);

                }
                childlayout.addView(tabchildlayoutx);
                //YAxis design


                mMatrixCheckBoxList = new ArrayList<CheckBox>();
                for (int yAxisCount = 0; yAxisCount < yVals.length; yAxisCount++) {


                    LinearLayout tabchildlayouty = new LinearLayout(this);
                    tabchildlayouty.setPadding(1, 2, 1, 0);
                    tabchildlayouty.setOrientation(LinearLayout.HORIZONTAL);
                    tabchildlayouty.setLayoutParams(childlayoutparam);
                    tabchildlayouty.setBackgroundColor(Color.parseColor("#DCE8F6"));

                    TextView textviewyName = new TextView(this);
                    textviewyName.setText(yVals[yAxisCount]);
                    textviewyName.setTextColor(Color.parseColor("#003399"));
                    textviewyName.setLayoutParams(qtparam);
                    tabchildlayouty.addView(textviewyName);

                    for (int xAxixCount = 0; xAxixCount < xVals.length; xAxixCount++) {

                        CheckBox checkbox = new CheckBox(this);
                        checkbox.setLayoutParams(qtparam);
                        checkbox.setTag(String.valueOf(yAxisCount) + "," + String.valueOf(xAxixCount));
                        //editTextValue.setTag(String.valueOf(count)+"QTY");
                        tabchildlayouty.addView(checkbox);
                        mMatrixCheckBoxList.add(checkbox);
                    }

                    childlayout.addView(tabchildlayouty);
                }
                mCheckBoxMatrixListContainer.put(rowid, mMatrixCheckBoxList);


                Log.d("TAG", "checkboxmatrix: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("masterview") || type.equalsIgnoreCase("relationalview")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);
                Log.d("TAG", "masterview: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("historyview") || type.equalsIgnoreCase("historyviewinput")) {
                if (validation.contains("#")) {
                    AddNewInvisibleButton(displayname, String.valueOf(rowid));
                } else {
                    AddNewButton(displayname, String.valueOf(rowid));
                }
                childlayout.addView(NewtextView(rowid));

                mSurveyDetails.setTableName(tablename);
                Log.d("TAG", "historyview: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("checkbox")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);

                Log.d("TAG", "checkbox: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("dependentradio")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);

                Log.d("TAG", "dependentradio: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("tableview") || type.equalsIgnoreCase("dependenttableview") || type.equalsIgnoreCase("multileveltableview")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);
                mIsTableView = true;

                Log.d("TAG", "tableview: " + displayname + "  " + rowid);
            }
            else if (CheckColonPresentInType(type)) {
                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(Html.fromHtml(displayname));
                textviewdisplayname.setTextColor(Color.BLUE);
                mParentLayout.addView(textviewdisplayname);

                RadioGroup rgp = new RadioGroup(this);

                RadioGroup.LayoutParams rprms;
                String[] RowData = type.split("\\:");
                if (RowData.length > 2) {
                    rgp.setOrientation(RadioGroup.VERTICAL);
                } else {
                    rgp.setOrientation(RadioGroup.HORIZONTAL);
                }
                for (int i = 0; i < RowData.length; i++) {
                    radioButton = new RadioButton(this);
                    radioButton.setText(RowData[i]);
                    radioButton.setTag(rowid);
                    radioButton.setOnClickListener(this);
                    radioButton.setTextColor(Color.BLUE);
                    rprms = new RadioGroup.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT);
                    rgp.addView(radioButton, rprms);
                }
                rgp.setTag(rowid);
                childlayout.addView(rgp);
                mRadioGrpList.add(rgp);
                childlayout.addView(NewtextView(rowid));

                Log.d("TAG", "OOOOOO: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("layer")) {
                mTitleText.setText(Html.fromHtml(displayname));
                mIsTittle = true;
                Log.d("TAG", "layer: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("double")) {
                String hinttext = "";
                if (displayname.contains("#")) {
                    String[] splitdisp = displayname.split("\\#");
                    if (splitdisp[1].equalsIgnoreCase("OTP")) {
                        mIsOTP = true;
                        mOTPRowID = rowid;
                        if (mandatory.equalsIgnoreCase("Y")) {
                            hinttext = splitdisp[0] + " (Mandatory)";
                        } else {
                            hinttext = splitdisp[0];
                        }
                        if (CheckPreDefinedData(rowid)) {
                            FillPreDefinedData(mMallColumnName);
                            mMallColumnName = "";
                        }
                    }

                } else {
                    if (mandatory.equalsIgnoreCase("Y")) {
                        hinttext = displayname + " (Mandatory)";
                        hinttext = hinttext.replace("*", " ");
                    } else {
                        hinttext = displayname;
                    }
                    if (CheckPreDefinedData(rowid)) {
                        FillPreDefinedData(mMallColumnName);
                        mMallColumnName = "";
                    }

                }
                childlayout.addView(NewtextViewHeading(rowid, displayname, 13f));
                childlayout.addView(AddEditText(rowid, "double", hinttext));
                Log.d("TAG", "double: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("floor")) {
                if (validation.trim().length() > 0) {
                    String hinttext = "";
                    if (displayname.contains("#")) {
                        String[] splitdisp = displayname.split("\\#");
                        if (splitdisp[1].equalsIgnoreCase("OTP")) {
                            mIsOTP = true;
                            mOTPRowID = rowid;
                            if (mandatory.equalsIgnoreCase("Y")) {
                                hinttext = splitdisp[0] + " (Mandatory)";
                                hinttext = hinttext.replace("*", " ");
                            } else {
                                hinttext = splitdisp[0];
                            }
                            if (CheckPreDefinedData(rowid)) {
                                FillPreDefinedData(mMallColumnName);
                                mMallColumnName = "";
                            }
                        }

                    } else {
                        if (mandatory.equalsIgnoreCase("Y")) {
                            hinttext = displayname + " (Mandatory)";
                            hinttext = hinttext.replace("*", " ");
                        } else {
                            hinttext = displayname;
                        }
                        if (CheckPreDefinedData(rowid)) {
                            FillPreDefinedData(mMallColumnName);
                            mMallColumnName = "";
                        }

                    }
                    childlayout.addView(NewtextViewHeading("", displayname, 13f));
//                    String=getDisplayNameFromValidation();
                    childlayout.addView(AddEditTextWithTextChangeListenerForFloor(rowid, validation, "double", hinttext));
                } else {
                    childlayout.addView(NewtextView(rowid));
                    SetTextViewText(rowid, displayname);
                }

                Log.d("TAG", "floor: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("checkinview")) {
                String hinttext = "";
                mPredefinedValue = PreferenceData.getCheckInOutEmpName(mContext);
                childlayout.addView(AddEditText(rowid, "text", hinttext));

                Log.d("TAG", "checkinview: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("rating")) {
                TextView textviewdisplayname = new TextView(this);
                if (displayname.contains("#")) {
                    String[] splitdisp = displayname.split("\\#");
                    String disp = "";
                    for (int cc = 0; cc < splitdisp.length; cc++) {
                        disp += splitdisp[cc] + "\n";
                    }
                    textviewdisplayname.setText(disp);
                } else {
                    textviewdisplayname.setText(Html.fromHtml(displayname));
                }

                textviewdisplayname.setTextColor(Color.BLUE);
                mParentLayout.addView(textviewdisplayname);

                RadioGroup rgp = new RadioGroup(this);
                rgp.setOrientation(RadioGroup.HORIZONTAL);
                RadioGroup.LayoutParams rprms;

                for (int i = 1; i <= Integer.parseInt(validation); i++) {
                    radioButton = new RadioButton(this);
                    radioButton.setText(String.valueOf(i));
                    radioButton.setTag(rowid);
                    radioButton.setOnClickListener(this);
                    radioButton.setTextColor(Color.BLUE);
                    rprms = new RadioGroup.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT);
                    rgp.addView(radioButton, rprms);
                }
                childlayout.addView(rgp);
                childlayout.addView(NewtextView(rowid));

                Log.d("TAG", "rating: " + displayname + "  " + rowid);
            }
            else if (type.equalsIgnoreCase("menu")) {}
            else if (type.equalsIgnoreCase("tickbox")) {
                CheckBox checkbox = new CheckBox(this);
                checkbox.setText(Html.fromHtml(displayname));
                checkbox.setTag(rowid);
                checkbox.setOnClickListener(this);
                checkbox.setTextColor(Color.BLUE);
                mCheckBoxList.add(checkbox);
                mParentLayout.addView(checkbox);
                // No need to draw
                Log.d("TAG", "tickbox: " + displayname + "  " + rowid);
            }
            else {
                String hinttext = "";
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = displayname + " (Mandatory)";
                    hinttext = hinttext.replace("*", " ");
                } else {
                    hinttext = displayname;
                }
                if (CheckPreDefinedData(rowid)) {
                    FillPreDefinedData(mMallColumnName);
                    mMallColumnName = "";
                }
                if (rowid.equalsIgnoreCase("RA002") || rowid.equalsIgnoreCase("RA136")) {
                    if (Constants.selectedFsSurveyPublish != null) {
                        mPredefinedValue = Constants.selectedFsSurveyPublish.getBusinessName();
                    }
                }
                childlayout.addView(NewtextViewHeading(rowid, displayname, 13f));
                childlayout.addView(AddEditText(rowid, "text", hinttext));
                Log.d("TAG", "other: " + displayname + "  " + rowid);
            }
            mParentLayout.addView(childlayout);

            if (type.equalsIgnoreCase("layer") == false && type.equalsIgnoreCase("menu") == false) {
                mInputTimeSurveyDetailsList.add(mSurveyDetails);
            }

            if (type.equalsIgnoreCase("radio") || type.equalsIgnoreCase("checkbox")) {
                if (CheckPreDefinedData(rowid)) {
                    FillPreDefinedData(mMallColumnName);
                    mMallColumnName = "";
                    if (type.equalsIgnoreCase("checkbox")) {
                        if (mPredefinedValue.contains(",")) {
                            mPredefinedValue = mPredefinedValue.replace(",", ";");
                        }
                        mPredefinedValue += ";";
                        SetSurveyValue(rowid, mPredefinedValue);
                        SetTextViewText(rowid, mPredefinedValue);
                    } else {
                        SetSurveyValue(rowid, mPredefinedValue);
                        SetTextViewText(rowid, mPredefinedValue);
                    }
                    mPredefinedValue = "";
                }
            }
        }

    }

    public void onClick(View clkdView) {
        Log.d("TAG", "onClick: mType value " + mType);
        String tag = (String) clkdView.getTag();
        mFinalRowID = tag;
        mParentType = "";
        mParentActionValue = "";
        mParentActionPresent = false;
        boolean isaction = CheckActionId(tag);
        mParentActionPresent = isaction;
        mParentType = mType;

        if (isaction && mParentType.equalsIgnoreCase("Y:N")) {
            Log.d("TAG", "onClick: 1");
            boolean checked = ((RadioButton) clkdView).isChecked();
            if (checked) {
                String val = ((RadioButton) clkdView).getText().toString();
                if (val.equalsIgnoreCase("NO")) {
                    Constants.isSurveyImageTake = false;
                    SetSurveyValue(mFinalRowID, val);
                    SetTextViewText(mFinalRowID, "");
                } else {
                    ParseandShowAction(tag);
                }
            }
        }
        if (isaction && mParentType.equalsIgnoreCase("radio")) {
            Log.d("TAG", "onClick: 2");
            ParseandShowAction(tag);
        }
        if (isaction && mParentType.equalsIgnoreCase("dynamicview")) {
            Log.d("TAG", "onClick: 3");
            ParseandShowActionForDynamicView(tag, "", "");
        }
        if (isaction && mParentType.equalsIgnoreCase("bool")) {
            Log.d("TAG", "onClick: 4");
            ParseandShowAction(tag);
        }
        if (isaction && mParentType.equalsIgnoreCase("date")) {
            Log.d("TAG", "onClick: 5");
            ParseandShowAction(tag);
        }
        if (isaction && CheckColonPresentInType(mParentType) && !mParentType.equalsIgnoreCase("Y:N")) {
            Log.d("TAG", "onClick: 6");
            boolean checked = ((RadioButton) clkdView).isChecked();
            if (checked) {
                Log.d("TAG", "onClick: 7");
                String val = ((RadioButton) clkdView).getText().toString();
                ParseandShowAction(mFinalRowID, val);
            }
        }

        if (!isaction && mParentType.equalsIgnoreCase("checkbox")) {
            Log.d("TAG", "onClick: 8");
            isShowValueSendValueDifferentForChcekBOx = false;
            isCurrentCheckBoxItemDependant = false;
            String tablename = GetTableName(tag);
            if (ParseTablename(tablename)) {
                Log.d("TAG", "onClick: 9");
                PrepareSurveyData(2, tablename);
            }
            else {
                Log.d("TAG", "onClick: 10");
                mCategory = false;
                mSubCategory = false;
                mCondition = "";
                PrepareSurveyData(2, tablename);
            }
        }
        if (!isaction && mParentType.equalsIgnoreCase("radio")) {
            Log.d("TAG", "onClick: 11");
            dependantConditionSqlQueryForRadioType = "";
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                Log.d("TAG", "onClick: 12");
                if (CheckSurveyValidation(tag, "check") && !mCheckValue.isEmpty()) {
                    Log.d("TAG", "onClick: 13");
                    String[] splittablename = tablename.split("\\#");
                    tablename = splittablename[1];
                    mCategory = false;
                    mSubCategory = false;
                    mCondition = "";
                    if (splittablename.length == 4) {
                        Log.d("TAG", "onClick: 14");
                        String selectColumnForRadioType = splittablename[2];
                        String WhereColumnForRadioType = splittablename[3];
                        dependantConditionSqlQueryForRadioType = "SELECT DISTINCT " + selectColumnForRadioType + " FROM " + tablename + " WHERE " + WhereColumnForRadioType + "='" + mCheckValue + "'";
                    }
                    PrepareSurveyData(2, tablename);
                }
                else {
                    Log.d("TAG", "onClick: 15");
                    Utils.showToast(mContext, "Please select " + mDependentDisplayName);
                }
            }
            else {
                Log.d("TAG", "onClick: 16");
                mCategory = false;
                mSubCategory = false;
                mCondition = "";
                PrepareSurveyData(2, tablename);
            }
        }
        if (!isaction && mParentType.equalsIgnoreCase("tickbox")) {
            Log.d("TAG", "onClick: 17");
            boolean checked = ((CheckBox) clkdView).isChecked();
            if (checked) {
                Log.d("TAG", "onClick: 18");
                SetSurveyValue(mFinalRowID, "yes");
            }
            else {
                Log.d("TAG", "onClick: 19");
                SetSurveyValue(mFinalRowID, "");
            }
        }
        if (!isaction && CheckColonPresentInType(mParentType)) {
            Log.d("TAG", "onClick: 20");
            boolean checked = ((RadioButton) clkdView).isChecked();
            if (checked) {
                Log.d("TAG", "onClick: 21");
                String val = ((RadioButton) clkdView).getText().toString();
                SetSurveyValue(mFinalRowID, val.toLowerCase());
                ChangeVisibility(mFinalRowID, val.toLowerCase());
            }
        }
        if (!isaction && mParentType.equalsIgnoreCase("rating")) {
            Log.d("TAG", "onClick: 22");
            boolean checked = ((RadioButton) clkdView).isChecked();
            if (checked) {
                Log.d("TAG", "onClick: 23");
                String val = ((RadioButton) clkdView).getText().toString();
                SetSurveyValue(mFinalRowID, val);
                SetTextViewText(mFinalRowID, val);
            }
        }
        if (!isaction && mParentType.equalsIgnoreCase("historyview")) {
            Log.d("TAG", "onClick: 24");
            historyViewDataLoadingProcess(tag);
        }
        if (!isaction && mParentType.equalsIgnoreCase("imageview")) {
            Log.d("TAG", "onClick: 25");
            mCustomerSelectionBasisFilter = "";
            mCustomerSelectionBasis = "";
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                Log.d("TAG", "onClick: 26");
                String[] splitablename = tablename.split("\\#");
                if (splitablename.length == 3 || splitablename.length == 4 || splitablename.length == 5) {
                    Log.d("TAG", "onClick: 27");
                    mTableName = splitablename[0];
                    mColumnName = splitablename[1];
                    mType = splitablename[2];
                    if (splitablename.length == 4) {
                        Log.d("TAG", "onClick: 28");
                        mCustomerSelectionBasis = splitablename[3];
                    }
                    if (splitablename.length == 5) {
                        Log.d("TAG", "onClick: 29");
                        String mCustomerSelectionBasisFilter = splitablename[4];
                        String[] mCustomerSelectionBasisFilterArray = splitablename[4].split("\\;");
                        if (mCustomerSelectionBasisFilterArray.length == 2) {
                            Log.d("TAG", "onClick: 30");
                            if (mCustomerSelectionBasisFilterArray[1].equalsIgnoreCase("loginempcode"))
                            {
                                Log.d("TAG", "onClick: 31");
                                mCustomerSelectionBasisFilterArray[1] = Constants.employeeDetailObject.getEmpCode();
                            }
                            this.mCustomerSelectionBasisFilter = " WHERE lower(acedns)=y AND " + mCustomerSelectionBasisFilterArray[0] + "='" + mCustomerSelectionBasisFilterArray[1] + "' ";
                        }
                    }

                    if (mColumnName.contains("%")) {
                        Log.d("TAG", "onClick: 32");
                        String[] splitColunName = mColumnName.split("\\%");
                        mSendColumn = splitColunName[0];
                        mShowColumn = splitColunName[1];
                        mDecision = "SAVE";
                        PrepareSurveyData(7, "");
                    }
                    else {
                        Log.d("TAG", "onClick: 33");
                        PrepareSurveyData(6, "");
                    }
                }
                else {
                    Log.d("TAG", "onClick: 34");
                    Utils.showToast(mContext, "No data found.Please Synchronize Data");
                }
            }
            else {
                Log.d("TAG", "onClick: 35");
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        }
        if (!isaction && mParentType.equalsIgnoreCase("relationalview")) {
            Log.d("TAG", "onClick: 36");
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                Log.d("TAG", "onClick: 37");
                if (CheckSurveyValidation(tag, "check") && mCheckValue.length() > 0) {
                    Log.d("TAG", "onClick: 38");
                    String[] splitablename = tablename.split("\\#");
                    if (splitablename.length == 3 || splitablename.length == 4 || splitablename.length == 5) {
                        Log.d("TAG", "onClick: 39");
                        String joinedTableName = splitablename[0];
                        String[] joinedTableNameSplitted = joinedTableName.split("\\;");
                        mTableName = joinedTableNameSplitted[0];
                        mTableName2 = joinedTableNameSplitted[1];

                        String joinedColumnName = splitablename[1];
                        String[] joinedColumnNameSplitted = joinedColumnName.split("\\%");
                        mSendColumn = joinedColumnNameSplitted[0];
                        mShowColumn = joinedColumnNameSplitted[1];

                        String joinedColumnNameFilter = splitablename[2];
                        String[] joinedColumnNameFilterSplitted = joinedColumnNameFilter.split("\\;");
                        String joinedColumnNameFilter1 = joinedColumnNameFilterSplitted[0];
                        String joinedColumnNameFilter2 = joinedColumnNameFilterSplitted[1];

                        mType = splitablename[3];
                        String joinedColumnNameFilter3 = splitablename[4];

                        String queryToGetmasterData = "SELECT DISTINCT " + mSendColumn + ", " + mShowColumn + " FROM " + mTableName + ", " + mTableName2 + " WHERE " + joinedColumnNameFilter1 + "=" + joinedColumnNameFilter2
                                + " AND " + joinedColumnNameFilter3 + "='" + mCheckValue + "'";

                        mDecision = "SAVE";
                        PrepareSurveyData(11, queryToGetmasterData);

                    }
                    else {
                        Log.d("TAG", "onClick: 40");
                        Utils.showToast(mContext, "No data found.Please Synchronize Data");
                    }
                }
                else {
                    Log.d("TAG", "onClick: 41");
                    Utils.showToast(mContext, "Please select " + mDependentDisplayName);
                }
            }
            else {
                Log.d("TAG", "onClick: 42");
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        }

        if (mParentType.equalsIgnoreCase("tableview")) {
            Log.d("TAG", "onClick: 43");
            getActionOfCurrentItem(tag);
            GetTableViewData(tag);
        }
        if (mParentType.equalsIgnoreCase("dependenttableview")) {
            Log.d("TAG", "onClick: 44");
            getActionOfCurrentItem(tag);
            GetTableViewData(tag);
        }
        if (mParentType.equalsIgnoreCase("multileveltableview")) {
            Log.d("TAG", "onClick: 45");
            mMultilevelTableViewData = new ArrayList<>();
            getActionOfCurrentItem(tag);
            currentRowId = tag;
            GetTableViewDataMultiLevel(tag);

        }
        if (mParentType.equalsIgnoreCase("masterview")) {
            Log.d("TAG", "onClick: 46");
            masterVIewDataLoadingProcess(tag, true);
        }
        if (mParentType.equalsIgnoreCase("historyviewinput")) {
            Log.d("TAG", "onClick: 47");
            historyViewInputDataLoadingProcess(tag);
            Button b = (Button) clkdView;
            finalDisplay = b.getText().toString();
        }
    }

    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */
    public void InitializeView() {
        customerSpinner =  findViewById(R.id.customerSpinner);
        viewSpinner =  findViewById(R.id.viewSpinner);

        mParentLayout =  findViewById(R.id.linearLayoutParent);

        mButtonSubmit =  findViewById(R.id.btn_Submi);
    }

    public void PrepareSurveyData(final int task, final String params) {
        mPrepareSurveyProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSurveyProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {

                    case 1:

                        if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes")) {
                            if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                                mSurveyInputList = mAceDnsDatabase.GetSurveyInputLayoutWise(params, mMenuID);
                            } else {
                                mSurveyInputList = mAceDnsDatabase.GetSurveyInputSubMenuWise(mSubMenu, mMenuID);
                            }
                        } else {
                            if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                                mSurveyInputList = mAceDnsDatabase.GetSurveyInputLayoutWise(params);
                            } else {
                                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                                    mSurveyInputList = mAceDnsDatabase.GetSurveyInputSubMenuWise(mSubMenu);
                                } else {
                                    mSurveyInputList = mAceDnsDatabase.GetSurveyInputLayoutWise();
                                }
                            }
                        }
                        mUndoSurveyList = mAceDnsDatabase.GetSurveyTempOutput(params);
                        break;
                    case 2:
                        if (isCurrentCheckBoxItemDependant) {
                            mDependentRowId = mFinalRowID;
                            getDependantValueWithRowName();
                            isCurrentCheckBoxItemDependant = false;
                            if (!isDependangtDataEmpty && mCondition.trim().length() > 0) {
                                int max = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mTableName, mColumnName, mShowColumn, mDependent, mCondition);
                                values = new String[max];
                                for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
                                    values[i] = Constants.mSurveyLayoutList[i];
                                }
                            }
                        } else if (mCategory == true && mSubCategory == false) {
                            int max = mAceDnsDatabase.Get_Survey_Master_Table_Category_Details(mTableName, mColumnName, mShowColumn);
                            values = new String[max];
                            for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
                                values[i] = Constants.mSurveyLayoutList[i];
                            }
                        } else if (mCategory == false && mSubCategory == true) {
                            mDependentRowId = mFinalRowID;
                            if (mCondition.trim().length() > 0) {
                                int max = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mTableName, mColumnName, mShowColumn, mDependent, mCondition);
                                values = new String[max];
                                for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
                                    values[i] = Constants.mSurveyLayoutList[i];
                                }
                            }
                        } else {
                            int max = mAceDnsDatabase.GetSurveyTableDetails(params, dependantConditionSqlQueryForRadioType);
                            values = new String[max];
                            for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
                                values[i] = Constants.mSurveyLayoutList[i];
                            }
                        }
                        break;

                    case 3:
                        mMallSurveyRelationList = mAceDnsDatabase.GetMallSurveyRelation(mMenuID, mSurveyType);
                        break;

                    case 4:
                        int max = mAceDnsDatabase.GetSurveyTableDetails(params, "");
                        values = new String[max];
                        for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
                            values[i] = Constants.mSurveyLayoutList[i];
                        }
                        break;

                    case 5:
                        if (mCondition.trim().length() > 0) {
                            int maxx = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mSubTableName, mSubColumnName, mSubDependent, mCondition);
                            subvalues = new String[maxx];
                            for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
                                subvalues[i] = Constants.mSurveyLayoutList[i];
                            }
                        }
                        break;

                    case 6:
                        int maxx = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase6(mTableName, mColumnName, mCustomerSelectionBasisFilter);
                        if (maxx > 0) {
                            values = new String[maxx];
                            for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
                                values[i] = Constants.mSurveyLayoutList[i];
                            }
                        } else {
                            values = null;
                        }
                        break;

                    case 7:
                        Log.d("TAG", "mKeyValueList: 19");
                        mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7(mTableName, mSendColumn, mShowColumn, mCustomerSelectionBasis, mCustomerSelectionBasisFilter);
                        Log.d("TAG", "run: "+mKeyValueList);

                        break;

                    case 8:
                        int catcount = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsClause(mTableName, mColumnName, mWhereClause);
                        mWhereClause = "";
                        if (catcount > 0) {
                            values = new String[catcount];
                            for (int i = 0; i < Constants.mSurveyLayoutList.length; i++) {
                                values[i] = Constants.mSurveyLayoutList[i];
                            }
                        } else {
                            values = null;
                        }
                        break;
                    case 9:
                        Log.d("TAG", "mKeyValueList: 20");
                        mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsConditionSpecial(mTableName, mSendColumn, mShowColumn, mShowColumn1, mShowColumn2, mShowColumnCount, mWhereClause);
                        mWhereClause = "";
                        break;

                    case 10:
                        String floor = Constants.selectedMallMaster.getFloor().trim();

                        if (floor.length() > 0) {
                            if (floor.contains(";")) {
                                values = floor.split(";");
                            } else {
                                values = new String[1];
                                values[0] = floor;
                            }
                        }
                        break;
                    case 11:
                        Log.d("TAG", "mKeyValueList: 21");
                        mKeyValueList = mAceDnsDatabase.GetMasterTableDetailsRelationalView(params);
                        break;
                    case 12:
                        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
                            mAceDnsDatabase.GetMenuName(mType);
                        } else {
                            if (Constants.surveyFormDetailsObj.getSurveyRoutePlan().equalsIgnoreCase("yes")) {
                                mAceDnsDatabase.GetMenuName("", mSubMenu);
                            } else {
                                mAceDnsDatabase.GetMenuName();
                            }
                        }
                        break;
                    case 13:
                        Log.d("TAG", "mKeyValueList: 22");
                        mKeyValueList = mAceDnsDatabase.GetHistoryViewData(mTableName, mShowColumn1, mShowColumn2, mShowColumn3, mShowColumn4, mShowColumn5, mShowColumn6, mShowColumnCount, mCustomerSelectionBasisFilter);
                        break;
                    case 14:
                        Log.d("TAG", "mKeyValueList: 23");
                        mKeyValueList = mAceDnsDatabase.GetHistoryViewInputData(params);
                        break;
                }
                Message msg = mPrepareSurveyHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyHandler.sendMessage(msg);
            }
        }.start();
    }

    private void getDependantValueWithRowName() {
        isDependangtDataEmpty = false;
        int i = -1;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            String displayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();
            if (CurrentCheckBoxItemDependantOn.equalsIgnoreCase(displayName)) {
                i = count;
            }
        }
        if (i > -1) {
            String value = mInputTimeSurveyDetailsList.get(i).getValue().trim();
            if (value.length() > 0) {
//				mCondition= value.replace(";",",");
//				if (mCondition.endsWith(","))
//				{
//					mCondition = mCondition.substring(0, mCondition.length() - 1);
//				}

                mCondition = value;
                if (mCondition.endsWith(";")) {
                    mCondition = mCondition.substring(0, mCondition.length() - 1);
                }
                mCondition = Utils.convertCommaSeparatedListToProperFormat2(mCondition, ";");
            } else {
                mCondition = "";
                isDependangtDataEmpty = true;
            }

        } else {
            mCondition = "";
            isDependangtDataEmpty = true;
        }
    }

    @SuppressWarnings("deprecation")
    public void AddNewButton(String displayname, String id) {
        LayoutParams buttonLayoutParams = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
        buttonLayoutParams.setMargins(0, 3, 0, 0);
        LinearLayout buttonLayout = new LinearLayout(mContext);
        buttonLayout.setLayoutParams(buttonLayoutParams);
        Button button = new Button(mContext);
        LayoutParams buttonParams = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        buttonParams.gravity = Gravity.CENTER_VERTICAL;
        button.setLayoutParams(buttonParams);
        button.setTag(id);
        if (false == mIsButtonEnable) {
            button.setEnabled(false);
            mIsButtonEnable = true;
        } else if (ismasterviewDisplayOnly(id)) {
            button.setEnabled(false);
        }
        button.setHorizontallyScrolling(true);
        button.setGravity(Gravity.CENTER);
//        button.setText("hello world");
        button.setText(Html.fromHtml(displayname));
        button.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
        button.setSingleLine(false);
        button.setOnClickListener(this);
        buttonLayout.addView(button);
        mParentLayout.addView(buttonLayout);
        mButtonList.add(button);
    }

    public DatePicker AddDatePicker(String displayname, String id) {

        DisplayMetrics metrics = getResources().getDisplayMetrics();
        DatePicker datePicker = new DatePicker(mContext);
        LayoutParams buttonParams = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        buttonParams.gravity = Gravity.CENTER_VERTICAL;
        datePicker.setLayoutParams(buttonParams);
        datePicker.setScaleX(0.7f);
        datePicker.setScaleY(0.7f);
        datePicker.setTag(id);
        datePicker.setPadding(0, -5, 0, -5);
        mdatePickerListForSubActionLayout.add(datePicker);
        return datePicker;

    }

    public void AddNewImageView(String id) {

        ImageView mImageViewCaptureImage = new ImageView(mContext);
        mImageViewCaptureImage.setTag(id);
        mParentLayout.addView(mImageViewCaptureImage);
        mImageViewList.add(mImageViewCaptureImage);
    }

    public void AddNewInvisibleButton(String displayname, String id) {
        LayoutParams buttonLayoutParams = new LayoutParams(
                LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
        LinearLayout buttonLayout = new LinearLayout(mContext);
        buttonLayout.setLayoutParams(buttonLayoutParams);
        Button button = new Button(mContext);
        LayoutParams buttonParams = new LayoutParams(
                LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        buttonParams.gravity = Gravity.CENTER_VERTICAL;
        button.setLayoutParams(buttonParams);
        button.setTag(id);
        if (false == mIsButtonEnable) {
            button.setEnabled(false);
            mIsButtonEnable = true;
        } else if (ismasterviewDisplayOnly(id)) {
            button.setEnabled(false);
        }
        button.setHorizontallyScrolling(true);
        button.setGravity(Gravity.CENTER);
        button.setText("hi button");
//        button.setText(Html.fromHtml(displayname));
        button.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
        button.setSingleLine(false);
        button.setOnClickListener(this);
        button.setVisibility(View.GONE);
        buttonLayout.addView(button);
        mParentLayout.addView(buttonLayout);
        mButtonList.add(button);
    }

    private boolean ismasterviewDisplayOnly(String rowId) {
        String rowidCurrentItem = "";
        boolean isIndependentValue = false;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            SurveyDetails surveyDetailsCurrentItem = mInputTimeSurveyDetailsList.get(count);

            rowidCurrentItem = surveyDetailsCurrentItem.getRowId();
            if (rowId.equalsIgnoreCase(rowidCurrentItem)) {
                if (surveyDetailsCurrentItem.getinsert_table_detail().equalsIgnoreCase("masterviewdisplay")) {
                    isIndependentValue = true;
                    break;
                }
            }
        }
        return isIndependentValue;
    }

    public void ShowSingelDualList() {
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.select_multiple_from_list);
        grpDialog.setCancelable(false);
        TextView title =  grpDialog.findViewById(R.id.title);
        final ImageView imageViewBack =  grpDialog.findViewById(R.id.image_cancel);
        imageViewBack.setVisibility(VISIBLE);
        imageViewBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                grpDialog.cancel();
            }
        });
        currentTableHeader = "an option";

        title.setText("Please select " + currentTableHeader);

        final ListView List =  grpDialog.findViewById(R.id.list);
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, R.layout.activity_masterview, values);
        List.setAdapter(adapter);

        List.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view,
                                    int position, long id) {
                grpDialog.cancel();
            }
        });
        grpDialog.show();

    }

    public void ShowList(final String type, final String checksaving) {
        Log.d("TAG", "mKeyValueList: 24");
        if (mKeyValueList.size() == 1) {
            Log.d("TAG", "mKeyValueList: 25");
            KeyValue obj = mKeyValueList.get(0);
            setValuesOfSurvey(obj, checksaving);
        } else if (mKeyValueList.size() > 1) {
            Log.d("TAG", "mKeyValueList: 26");
            ArrayList<KeyValue> mKeyValueListSearchArray = new ArrayList<>(mKeyValueList);
            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_multiple_from_list);

            grpDialog.setCancelable(false);
            TextView title =  grpDialog.findViewById(R.id.title);
            TextView autoCompleteTextView1 =  grpDialog.findViewById(R.id.autoCompleteTextView1);
            ImageView image_cancel =  grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(VISIBLE);
            image_cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    grpDialog.dismiss();
                }
            });
            if (actionStringOfCurrentSelectedItem.toLowerCase().contains("#y#")) {
                image_cancel.setVisibility(View.GONE);
            }
            title.setText(dialogHeaderSearchTypeDynamicText);
            dialogHeaderSearchTypeDynamicText = "Please select an option";
            autoCompleteTextView1.setHint("Type Here to Search");

            final ListView List =  grpDialog.findViewById(R.id.list);
            KeyValueCheckAdapter adapterForCheckedItem = null;
            KeyValueNormalAdapter adapter1 = null;
            final ArrayList<KeyValue> mKeyValueList_Search = new ArrayList();
            final EditText autoCompleteTextView1OBJ =  grpDialog.findViewById(R.id.autoCompleteTextView1);
            autoCompleteTextView1OBJ.setVisibility(VISIBLE);
            Log.d("TAG", "mKeyValueList: 35");
            for (int i = 0; i < mKeyValueList.size(); i++) {
                Log.d("TAG", "mKeyValueList: 36 "+i);
                if (mShowColumnCount == 5) {
                    Log.d("TAG", "mKeyValueList: 37 "+i);
                    mKeyValueList_Search.add(new KeyValue(mKeyValueList.get(i).getValue(), mKeyValueList.get(i).getmShowColumn1(), mKeyValueList.get(i).getmShowColumn2(), mKeyValueList.get(i).getKey(), mKeyValueList.get(i).getType()));
                } else if (mShowColumnCount == 4) {
                    Log.d("TAG", "mKeyValueList: 38 "+i);
                    mKeyValueList_Search.add(new KeyValue(mKeyValueList.get(i).getValue(), mKeyValueList.get(i).getmShowColumn1(), "", mKeyValueList.get(i).getKey(), mKeyValueList.get(i).getType()));
                } else {
                    Log.d("TAG", "mKeyValueList: 39 "+i);
                    mKeyValueList_Search.add(new KeyValue(mKeyValueList.get(i).getValue(), mKeyValueList.get(i).getKey(), mKeyValueList.get(i).getType()));
                }

            }
            if (type.equalsIgnoreCase("checkbox")) {

                List.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
                Log.d("TAG", "mKeyValueList: 40");
                adapterForCheckedItem = new KeyValueCheckAdapter(this, R.layout.activity_check_list, mKeyValueList);
                List.setAdapter(adapterForCheckedItem);
                KeyValueCheckAdapter finalAdapterForCheckedItem = adapterForCheckedItem;
                autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {

                    public void afterTextChanged(Editable s) {

                    }

                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                    }

                    public void onTextChanged(CharSequence s, int start, int before, int count) {

                        finalAdapterForCheckedItem.filter(s.toString());

                    }
                });
            } else {

                adapter1 = new KeyValueNormalAdapter(this, R.layout.activity_listview, mKeyValueList_Search, mShowColumnCount);
                List.setAdapter(adapter1);
                mShowColumnCount = 0;

                final KeyValueNormalAdapter finalAdapter = adapter1;
                autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {

                    public void afterTextChanged(Editable s) {

                    }

                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                    }

                    public void onTextChanged(CharSequence s, int start, int before, int count) {

                        finalAdapter.filter(s.toString());

                    }
                });
            }
            RelativeLayout chkAllLayout =  grpDialog.findViewById(R.id.select_all_layout);
            if (type.equalsIgnoreCase("checkbox")) {
                chkAllLayout.setVisibility(VISIBLE);
            }

            final CheckBox chkSelectAll =  grpDialog.findViewById(R.id.chk_all);
            chkSelectAll.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (chkSelectAll.isChecked()) {
                        for (int i = 0; i < List.getCount(); i++) {
                            List.setItemChecked(i, true);
                        }
                    } else {
                        for (int i = 0; i < List.getCount(); i++) {
                            List.setItemChecked(i, false);
                        }
                    }
                }
            });


            List.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> parent, View view,
                                        int position, long id) {
                    if (type.equalsIgnoreCase("checkbox")) {

                    } else {
                        Log.d("TAG", "mKeyValueList select: "+position);
                        KeyValue obj = mKeyValueList_Search.get(position);
                        setValuesOfSurvey(obj, checksaving);
                        grpDialog.cancel();
                        if (closeSubActionDialog) {
                            closeSubActionDialog = false;
                            closeSUbActionLayOut(Constants.surveyInputListSubActionDialog, Constants.rowidSubActionDialog, grpDialogSubAction);
                        }
                    }
                }
            });

            Button submit =  grpDialog.findViewById(R.id.button1);
            if (type.equalsIgnoreCase("radio")) {
                submit.setVisibility(View.GONE);
            }
            if (actionStringOfCurrentSelectedItem.toLowerCase().contains("#y#") && !type.equalsIgnoreCase("checkbox")) {
                submit.setVisibility(View.GONE);
            }
            submit.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    if (type.equalsIgnoreCase("checkbox")) {
                        try {
                            KeyValue obj = null;
                            String key = "";
                            String value = "";
                            final SparseBooleanArray checkedItems = List.getCheckedItemPositions();
                            int checkedItemsCount = checkedItems.size();
                            if (checkedItemsCount > 0) {
                                for (int i = 0; i < checkedItemsCount; ++i) {
                                    Log.d("TAG", "mKeyValueList: 41 "+i);
                                    int position = checkedItems.keyAt(i);
                                    obj = mKeyValueList.get(position);
                                    key += obj.getKey() + ";";
                                    value += obj.getValue() + ";";
                                }
                                if (checksaving.equalsIgnoreCase("SAVE")) {
                                    SetTextViewText(mFinalRowID, value);
                                    SetSurveyValue(mFinalRowID, key);
                                } else {
                                    //Do Something
                                    if (mKeyValueSubList != null) {
                                        for (int count = 0; count < mKeyValueSubList.size(); count++) {
                                            if (mSubActionTag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {
                                                mKeyValueSubList.get(count).setValue(key);
                                                mKeyValueSubList.get(count).setEnteredValue(value);
                                                break;
                                            }
                                        }
                                    }

                                }
                            }


                            grpDialog.cancel();
                            if (mParentType.equalsIgnoreCase("masterview") && mParentActionPresent) {
                                if (mParentActionValue.contains("masterviewdisplay") && mParentActionValue.contains("#")) { //mParentActionValue  -----   masterviewdisplay#Nature of work#facilitator_master#nature_of_work#f_code
                                    String[] splittedActionString = mParentActionValue.split("#");
                                    if (splittedActionString.length == 5) {
                                        String displayHeader = splittedActionString[1];
                                        String tableName = splittedActionString[2];
                                        String columnName = splittedActionString[3];
                                        String selectColumn = splittedActionString[4];
                                        ArrayList<KeyValue> displayList = mAceDnsDatabase.GetMasterListForChosenValueInMasterView(tableName, columnName, selectColumn, key, value);
                                        if (displayList.size() > 0) {
                                            ShowSingleCoumnViewListWithKeyValue(displayHeader, displayList);
                                        }

                                    }
                                }

                                if (mParentType.matches("masterview") && mParentActionValue != null && mParentActionValue.contains("any")) {

                                    if (value.contains(";")) {
                                        mActionPressedExistingValue = "";
                                        String[] valSplitted = value.split(";");
                                        for (String s : valSplitted) {
                                            String currentVal = s.trim();
                                            if (!currentVal.matches("")) {
                                                ParseandShowActionCheckedItems(mFinalRowID, currentVal);
                                            }
                                        }

                                    }

                                }


                            }
                        } catch (Exception e) {
                            Log.d("", "" + e.toString());
                        }

                    } else {
                        grpDialog.cancel();
                    }
                }
            });
            grpDialog.show();
        }
    }

    public void setValuesOfSurvey(KeyValue obj, String checksaving) {
        if (checksaving.equalsIgnoreCase("SAVE")) {
            Log.d("TAG", "setValuesOfSurvey: 1");
            SetTextViewText(mFinalRowID, obj.getValue());
            try {
                Log.d("TAG", "setValuesOfSurvey: 2");
                String acedns = getAcednsByRowId(mFinalRowID);
                if (acedns.equalsIgnoreCase("V")) {
                    Log.d("TAG", "setValuesOfSurvey: 3");
                    String valid = getValidationByRowId(mFinalRowID);
                    if (valid.contains("text:")) {
                        Log.d("TAG", "setValuesOfSurvey: 4");
                        String t[] = valid.split(":");
                        SetEditTextTextAndDisableIfNeeded(t[1], obj.getValue(), true);
                    }
                }
            } catch (Exception e) {
                Log.i("TAG", "setValuesOfSurvey: " + e.toString());
            }

            if (obj.getType().equalsIgnoreCase("imageview")) {
                Log.d("TAG", "setValuesOfSurvey: 5");
                SetSurveyValue(mFinalRowID, obj.getValue());
            } else {
                Log.d("TAG", "setValuesOfSurvey: 6");
                SetSurveyValue(mFinalRowID, obj.getKey());
            }

            if (isCurrentRowIndependent(mFinalRowID)) {
                Log.d("TAG", "setValuesOfSurvey: 7");
                currentSiteValueForEdit = obj.getValue();
                SetSurveyValueToMasterViewEditDependingOnIndependentView(mFinalRowID, obj.getKey());
            }
        }
        else {
            Log.d("TAG", "setValuesOfSurvey: 8");
            if (mKeyValueSubList != null) {
                Log.d("TAG", "setValuesOfSurvey: 9");
                for (int count = 0; count < mKeyValueSubList.size(); count++) {
                    Log.d("TAG", "setValuesOfSurvey: 10 "+count);
                    if (mSubActionTag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {
                        Log.d("TAG", "setValuesOfSurvey: 11 "+mSubActionTag);
                        mKeyValueSubList.get(count).setValue(obj.getKey());
                        String value = obj.getValue();
                        currentSiteValueForEdit = value;
                        mKeyValueSubList.get(count).setEnteredValue(value);
                        break;
                    }
                }
            }
            if (isCurrentRowIndependent(mFinalRowID)) {
                Log.d("TAG", "setValuesOfSurvey: 12");
                SetSurveyValueToMasterViewEditDependingOnIndependentView(mFinalRowID, obj.getKey());
            }
        }
    }

    public void ShowListDependentViewClick(final String type) {
        final ArrayList<KeyValue> mKeyValueList_Search = new ArrayList();
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.select_multiple_from_list);
        grpDialog.setCancelable(false);
        TextView title =  grpDialog.findViewById(R.id.title);
        final EditText autoCompleteTextView1OBJ =  grpDialog.findViewById(R.id.autoCompleteTextView1);
        if (currentItemDisplayName.matches("")) {
            currentItemDisplayName = "an option";
        }
        title.setText("Please select " + currentItemDisplayName);
        ImageView image_cancel =  grpDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(VISIBLE);
        image_cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                grpDialog.dismiss();
            }
        });
        final ListView List =  grpDialog.findViewById(R.id.list);
        KeyValueCheckAdapter adapter = null;

        Log.d("TAG", "mKeyValueList: 27");

        mKeyValueList_Search.addAll(mKeyValueList);

        if (type.equalsIgnoreCase("checkbox")) {
            List.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            adapter = new KeyValueCheckAdapter(this, R.layout.activity_check_list, mKeyValueList_Search);
            List.setAdapter(adapter);
        } else {
            final KeyValueNormalAdapter adapter1;
            adapter1 = new KeyValueNormalAdapter(this, R.layout.activity_listview, mKeyValueList_Search);
            List.setAdapter(adapter1);
            autoCompleteTextView1OBJ.setVisibility(VISIBLE);
            autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {

                public void afterTextChanged(Editable s) {

                }

                public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                }

                public void onTextChanged(CharSequence charText, int start, int before, int count) {

                    mKeyValueList_Search.clear();
                    if (charText.length() == 0) {
                        Log.d("TAG", "mKeyValueList: 28");
                        mKeyValueList_Search.addAll(mKeyValueList);
                    } else {
                        Log.d("TAG", "mKeyValueList: 29");
                        for (KeyValue wp : mKeyValueList) {
                            if (wp.getValue().toLowerCase(Locale.getDefault())
                                    .contains(charText.toString().toLowerCase(Locale.getDefault()))) {
                                mKeyValueList_Search.add(wp);
                            }
                        }
                    }
                    adapter1.notifyDataSetChanged();

                }
            });

        }
        RelativeLayout chkAllLayout =  grpDialog.findViewById(R.id.select_all_layout);
        if (type.equalsIgnoreCase("checkbox")) {
            chkAllLayout.setVisibility(VISIBLE);
        }

        final CheckBox chkSelectAll =  grpDialog.findViewById(R.id.chk_all);
        chkSelectAll.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (chkSelectAll.isChecked()) {
                    for (int i = 0; i < List.getCount(); i++) {
                        List.setItemChecked(i, true);
                    }
                } else {
                    for (int i = 0; i < List.getCount(); i++) {
                        List.setItemChecked(i, false);
                    }
                }
            }
        });


        List.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view,
                                    int position, long id) {
                if (type.equalsIgnoreCase("checkbox")) {

                } else {
                    HideSoftKeyBoard(autoCompleteTextView1OBJ);
                    KeyValue obj = mKeyValueList_Search.get(position);
                    if (mDecision.equalsIgnoreCase("SAVE")) {
//							DrawLayoutDependentView(mFinalRowID);
                        SetTextViewText(mFinalRowID, currentItemDisplayName + ": " + obj.getValue());
                        SetSurveyValue(mFinalRowID, tempValForDependentSurvey + "#" + obj.getKey());
                    } else {
                        if (mKeyValueSubList != null) {
                            for (int count = 0; count < mKeyValueSubList.size(); count++) {
                                if (mSubActionTag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {
                                    mKeyValueSubList.get(count).setValue(obj.getKey());
                                    mKeyValueSubList.get(count).setEnteredValue(obj.getValue());
                                    break;
                                }
                            }
                        }
                    }
                    grpDialog.cancel();
                }
            }
        });

        Button submit =  grpDialog.findViewById(R.id.button1);
//			if(type.equalsIgnoreCase("radio")){
        submit.setVisibility(View.GONE);
//			}

        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                if (type.equalsIgnoreCase("checkbox")) {
                    KeyValue obj = null;
                    String key = "";
                    String value = "";
                    final SparseBooleanArray checkedItems = List.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        Log.d("TAG", "mKeyValueList: 30");
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            obj = mKeyValueList.get(position);
                            key += obj.getKey() + ";";
                            value += obj.getValue() + ";";
                        }
                        if (mDecision.equalsIgnoreCase("SAVE")) {
                            SetTextViewText(mFinalRowID, value);
                            SetSurveyValue(mFinalRowID, key);
                        } else {
                            //Do Something
                            if (mKeyValueSubList != null) {
                                for (int count = 0; count < mKeyValueSubList.size(); count++) {
                                    if (mSubActionTag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {
                                        mKeyValueSubList.get(count).setValue(key);
                                        mKeyValueSubList.get(count).setEnteredValue(value);
                                        break;
                                    }
                                }
                            }

                        }
                    }
                    grpDialog.cancel();
                } else {
                    grpDialog.cancel();
                }
            }
        });
        grpDialog.show();
//		}
    }

    public void ShowQty(int i) {
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.select_multiple_from_list);

        grpDialog.setCancelable(false);

        TextView title =  grpDialog.findViewById(R.id.title);
        title.setText("Please Enter Quantity");
        final ImageView imageViewBack =  grpDialog.findViewById(R.id.image_cancel);
        imageViewBack.setVisibility(View.GONE);
        final EditText autoCompleteTextView1OBJ =  grpDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1OBJ.setVisibility(View.GONE);
        doubleTextView =  grpDialog.findViewById(R.id.doubleEditText);

        doubleTextView.setVisibility(VISIBLE);
        doubleTextView.setHint("Enter Quantity(Bag)");
        doubleTextView.setInputType(InputType.TYPE_CLASS_NUMBER);

        Button submit =  grpDialog.findViewById(R.id.button1);

        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

                if (!doubleTextView.getText().toString().isEmpty()) {
                    valuesQty[i] = doubleTextView.getText().toString();
                    doubleTextView.setText("");
                    grpDialog.cancel();
                } else {
                    Utils.showToast(mContext, "Please Quantity First");

                }


            }
        });

        grpDialog.show();

    }

    public void ShowList(final String type) {
        //mCondition="";
        final ArrayList<String> al_rootMain = new ArrayList();
        final ArrayList<String> al_rootNAMESearch = new ArrayList();
        if (values.length == 1 && !type.equalsIgnoreCase("double") && !type.equalsIgnoreCase("")) {
            String val = values[0];
            SetTextViewText(mFinalRowID, val);
            SetSurveyValue(mFinalRowID, val);
        } 
        else {
            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_multiple_from_list);
            grpDialog.setCancelable(false);
            currentTableHeader = "an option";

            TextView title =  grpDialog.findViewById(R.id.title);
            title.setText("Please select " + currentTableHeader);
            final ImageView imageViewBack =  grpDialog.findViewById(R.id.image_cancel);
            imageViewBack.setVisibility(VISIBLE);
            imageViewBack.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    grpDialog.cancel();
                }
            });

            final ListView List =  grpDialog.findViewById(R.id.list);
            if (type.contains("/") || type.equalsIgnoreCase("double") || type.equalsIgnoreCase("")) {
                doubleTextView =  grpDialog.findViewById(R.id.doubleEditText);
                doubleTextView2 =  grpDialog.findViewById(R.id.doubleEditText2);
                doubleTextView.setVisibility(VISIBLE);
                doubleTextView.setHint(mPredefinedValueEditTextDialog);
                List.setVisibility(View.GONE);
                if (type.contains("/")) {
                    String val1 = "", val2 = "";
                    doubleTextView2.setVisibility(VISIBLE);

                    doubleTextView2.setHint(mPredefinedValueEditTextDialog2);
                    String[] splittedType = type.split("/");
                    if (splittedType.length == 2) {
                        val1 = splittedType[0];
                        val2 = splittedType[1];
                    }
                    if (val1.equalsIgnoreCase("double")) {
                        doubleTextView.setInputType(InputType.TYPE_CLASS_NUMBER
                                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                    }
                    if (val2.equalsIgnoreCase("double")) {
                        doubleTextView2.setInputType(InputType.TYPE_CLASS_NUMBER
                                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                    }
                } else {
                    if (type.equalsIgnoreCase("double")) {
                        doubleTextView.setInputType(InputType.TYPE_CLASS_NUMBER
                                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                    }
                }


            }

            final EditText autoCompleteTextView1OBJ =  grpDialog.findViewById(R.id.autoCompleteTextView1);
            if (type.equalsIgnoreCase("checkbox")) {
                List.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
                if (CheckPreDefinedData(mFinalRowID)) {
                    FillPreDefinedData(mMallColumnName);
                    mMallColumnName = "";
                    if (mPredefinedValue.length() > 0) {
                        Constants.isMallSurveyRelationDataAvailable = true;
                    } 
                    else {
                        Constants.isMallSurveyRelationDataAvailable = false;
                    }
                }

                if (true == Constants.isMallSurveyRelationDataAvailable) {
                    if (mPredefinedValue.contains(",")) {
                        Constants.mGeneralFacilityList = mPredefinedValue.split("\\,");
                    } 
                    else {
                        Constants.mGeneralFacilityList = new String[1];
                        Constants.mGeneralFacilityList[0] = mPredefinedValue;
                    }
                }


                ArrayList<SurveyRoot> al_rootNAME = new ArrayList();
                valuesQty = new String[values.length];
                for (int i = 0; i < values.length; i++) {
                    al_rootNAME.add(new SurveyRoot(values[i]));
                    valuesQty[i] = "";
                }
                final ListSearchCheckAdapter adapterOBJ = new ListSearchCheckAdapter(this, R.layout.activity_check_list, al_rootNAME, mFinalRowID, mTextViewList);
                List.setAdapter(adapterOBJ);
                autoCompleteTextView1OBJ.setHint("Type Here to Search");
                autoCompleteTextView1OBJ.setVisibility(VISIBLE);
                autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {

                    public void afterTextChanged(Editable s) {

                    }

                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                    }

                    public void onTextChanged(CharSequence s, int start, int before, int count) {

                        adapterOBJ.filter(s.toString());

                    }
                });
                
            } 
            else {
                for (int i = 0; i < values.length; i++) {
                    al_rootMain.add(values[i]);
                    al_rootNAMESearch.add(values[i]);
                }
                final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_masterview, al_rootNAMESearch);
                List.setAdapter(adapter);

                autoCompleteTextView1OBJ.setVisibility(VISIBLE);
                autoCompleteTextView1OBJ.setHint("Type Here to Search");
                autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {
                    public void afterTextChanged(Editable s) {

                    }

                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                    }

                    public void onTextChanged(CharSequence charText, int start, int before, int count) {

                        al_rootNAMESearch.clear();
                        if (charText.length() == 0) {
                            al_rootNAMESearch.addAll(al_rootMain);
                        } else {
                            for (String wp : al_rootMain) {
                                if (wp.toLowerCase(Locale.getDefault())
                                        .contains(charText.toString().toLowerCase(Locale.getDefault()))) {
                                    al_rootNAMESearch.add(wp);
                                }
                            }
                        }
                        adapter.notifyDataSetChanged();
                    }
                });

            }


            RelativeLayout chkAllLayout =  grpDialog.findViewById(R.id.select_all_layout);
            if (type.equalsIgnoreCase("checkbox")) {
                chkAllLayout.setVisibility(VISIBLE);
            }

            if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {
                chkAllLayout.setVisibility(View.GONE);
                doubleTextView =  grpDialog.findViewById(R.id.doubleEditText);
                if (type.equalsIgnoreCase("checkbox")) {
                    doubleTextView.setVisibility(View.GONE);
                    doubleTextView.setHint("Enter Quantity");
                }
            }

            if (type.equalsIgnoreCase("checkbox")) {
                if (true == Constants.isMallSurveyRelationDataAvailable) {
                    String menuItem = "";
                    for (int i = 0; i < List.getCount(); i++) {
//						List.setItemChecked(i, true);
//						menuItem=(String)List.getItemAtPosition(i);
                        Object currentObject = List.getItemAtPosition(i);
                        if (currentObject instanceof SurveyRoot) {
                            SurveyRoot rootObject = (SurveyRoot) List.getItemAtPosition(i);
                            menuItem = rootObject.getName();
                        } else {
                            menuItem = (String) List.getItemAtPosition(i);
                        }
//						menuItem=al_rootNAMESearch.get(i)+"";
                        for (int count = 0; count < Constants.mGeneralFacilityList.length; count++) {
                            if (menuItem.equalsIgnoreCase(Constants.mGeneralFacilityList[count])) {
                                List.setItemChecked(i, true);
                            }
                        }
                    }
                }
            }

            final CheckBox chkSelectAll =  grpDialog.findViewById(R.id.chk_all);
            chkSelectAll.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (chkSelectAll.isChecked()) {
                        for (int i = 0; i < List.getCount(); i++) {
                            List.setItemChecked(i, true);
                        }
                    } else {
                        for (int i = 0; i < List.getCount(); i++) {
                            List.setItemChecked(i, false);
                        }
                    }
                }
            });


            List.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> parent, View view,
                                        int position, long id) {
                    HideSoftKeyBoard(autoCompleteTextView1OBJ);
                    String hide = getValidationByRowId(mFinalRowID);

                    if (hide.contains("hide12:")) {
                        Log.d("TAG", "onItemClick: List.setOnItemClickListener: hide12");

                        String[] hideArray = hide.split("&");
                        String[] hideArray1 = hideArray[0].split(":");
                        String rows = hideArray1[1];
                        String value = hideArray[1];
                        String val = al_rootNAMESearch.get(position).trim();

                        String[] rowArray = rows.split(";");

                        for (int i = 0; i < rowArray.length; i++) {
                            String row = rowArray[i];

                            if (value.equalsIgnoreCase(val)) {
                                SetMandatoryChangeBlank(row, "Y");
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);
                                        //SetTextViewTextBlank(row);

                                    }
                                }
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);
                                        SetSurveyValue(row, " ");
                                        //break;
                                    }
                                }
                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);

                                        // break;
                                    }
                                }
                            } 
                            else {
                                SetMandatoryChangeBlank(row, "N");
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);
                                        SetTextViewTextBlank(row);

                                    }
                                }
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);
                                        currentButton.setText("");
                                        SetSurveyValue(row, " ");
                                        //break;
                                    }
                                }
                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);

                                        // break;
                                    }
                                }
                            }
                        }

                    }

                    if (hide.contains("hide:")) {
                        Log.d("TAG", "onItemClick: List.setOnItemClickListener: hide");

                        String[] hideArray = hide.split("&");
                        String[] hideArray1 = hideArray[0].split(":");
                        String rows = hideArray1[1];
                        String value = hideArray[1];
                        String val1 = al_rootNAMESearch.get(position).trim();

                        String[] rowArray = rows.split(";");

                        for (int i = 0; i < rowArray.length; i++) {
                            String row = "";
                            String mandatoty = "N";
                            if (rowArray[i].contains(",")) {
                                String[] row1Array = rowArray[i].split(",");
                                row = row1Array[0];
                                mandatoty = row1Array[1];
                            } 
                            else {
                                row = rowArray[i];
                                mandatoty = "Y";
                            }
                            if (value.equalsIgnoreCase(val1)) {
                                SetMandatoryChangeYes(row, mandatoty);
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);
                                        //SetTextViewTextBlank(row);

                                    }
                                }
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);
                                        SetSurveyValue(row, " ");
                                        currentButton.setText("");
                                        //break;
                                    }
                                }
                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);

                                        // break;
                                    }
                                }
                            } 
                            else {
                                SetMandatoryChangeBlank(row, "N");
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);
                                        //SetTextViewTextBlank(row);

                                    }
                                }
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue(row, " ");
                                        //break;
                                    }
                                }
                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);

                                        // break;
                                    }
                                }
                            }
                        }

                    }
                    
                    if (hide.contains("days:")) {
                        Log.d("TAG", "onItemClick: List.setOnItemClickListener: days");

                        //String[] hideArray = hide.split("&");
                        String[] hideArray1 = hide.split(":");
                        String rows = hideArray1[1];
                        //String value = hideArray[1];
                        //String val1 = al_rootNAMESearch.get(position).trim();

                        String[] rowArray = rows.split(";");

                        for (int i = 0; i < rowArray.length; i++) {
                            String row = "";
                            String mandatoty = "N";
                            int dys = 0;
                            int comparisonResult = 0;
                            if (rowArray[i].contains(",")) {
                                String[] row1Array = rowArray[i].split(",");
                                row = row1Array[0];
                                mandatoty = row1Array[1];
                                dys = Integer.parseInt(row1Array[2]);
                            } 
                            else {
                                row = rowArray[i];
                                mandatoty = "Y";
                            }
                            boolean isRow = true;
                            Date c = Calendar.getInstance().getTime();
                            SimpleDateFormat df = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
                            String formattedDate = df.format(c);
                            try {
                                comparisonResult = new Date().compareTo(df.parse(formattedDate));
                            } catch (ParseException e) {
                                //throw new RuntimeException(e);
                                isRow = false;
                            }
                            if (dys > comparisonResult) {
                                isRow = true;
                            } else {
                                isRow = false;
                            }
                            if (isRow) {
                                SetMandatoryChangeYes(row, mandatoty);
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);
                                        //SetTextViewTextBlank(row);

                                    }
                                }
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);
                                        SetSurveyValue(row, " ");
                                        currentButton.setText("");
                                        //break;
                                    }
                                }
                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(VISIBLE);

                                        // break;
                                    }
                                }
                            } else {
                                SetMandatoryChangeBlank(row, "N");
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);
                                        //SetTextViewTextBlank(row);

                                    }
                                }
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue(row, " ");
                                        //break;
                                    }
                                }
                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals(row)) {
                                        currentButton.setVisibility(GONE);

                                        // break;
                                    }
                                }
                            }
                        }

                    }
                    
                    if (type.equalsIgnoreCase("checkbox")) {
                        Log.d("TAG", "onItemClick: List.setOnItemClickListener: checkbox");

                        if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {
                            ShowQty(position);
                        }
                    }

                    else {
                        Log.d("TAG", "onItemClick: List.setOnItemClickListener: other");

                        String val = al_rootNAMESearch.get(position).trim();
                        if (val.equalsIgnoreCase("Others") && Constants.surveyFormDetailsObj.getSurveyOtherText().equalsIgnoreCase("yes")) {
                            //Utils.showToast(mContext, "Hit the clause");
                            ShowActionLayout(val, "", "Y", "", "");
                        } else {
                            if (!mParentType.matches("multileveltableview")) {
                                SetTextViewText(mFinalRowID, val);
                                SetSurveyValue(mFinalRowID, val);
                                if (mActionstring.length() > 0) {
                                    if (mActionstring.equalsIgnoreCase("no_repetition")) {
                                        mNonRepeat.add(val);
                                    }
                                }
                            }

                        }
                        if (mParentType.matches("multileveltableview")) {
                            mMultilevelTableViewData.add(val);
                            GetMultiLevelTableViewData(val);
                        }
                        if (mParentType.matches("tableview") && actionStringOfCurrentSelectedItem != null && actionStringOfCurrentSelectedItem.contains(":")) {
                            ParseandShowAction(mFinalRowID, val);
                        } else if (mParentType.matches("dependenttableview") && actionStringOfCurrentSelectedItem != null && actionStringOfCurrentSelectedItem.contains(":")) {

                            String[] splittedActionString = actionStringOfCurrentSelectedItem.split(":");
                            if (splittedActionString.length > 0) {
                                String dependentSurveyInputString = splittedActionString[1];
                                String[] dependentSurveyInputStringSplitted = dependentSurveyInputString.split("#");
//								removeDuplicateItemFromSurveyDetailsList(tempRowIdForDependentSurvey);
                                if (dependentSurveyInputStringSplitted.length >= 5 && splittedActionString[0].matches(val)) {
                                    tempValForDependentSurvey = val;
                                    String[] SplittedTableDetails = dependentSurveyInputStringSplitted[3].split("%");
                                    mTableName = SplittedTableDetails[0];
                                    mSendColumn = SplittedTableDetails[1];
                                    mShowColumn = SplittedTableDetails[2];
                                    mDecision = "SAVE";
//									currentTableHeader=dependentSurveyInputStringSplitted[0];
                                    mType = dependentSurveyInputStringSplitted[5];
                                    Log.d("TAG", "mKeyValueList: 31");
                                    mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7(mTableName, mSendColumn, mShowColumn, mCustomerSelectionBasis, "");
                                    if (mKeyValueList != null && mKeyValueList.size() > 0) {
                                        Log.d("TAG", "mKeyValueList: 32");
                                        currentItemDisplayName = dependentSurveyInputStringSplitted[0];
                                        ShowListDependentViewClick(mType);
                                    } else {
                                        Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                    }


                                }

                            }
                        } else if (mParentType.matches("dependenttableview") && actionStringOfCurrentSelectedItem != null && actionStringOfCurrentSelectedItem.contains(":")) {

                            String[] splittedActionString = actionStringOfCurrentSelectedItem.split(":");
                            if (splittedActionString.length > 0) {
                                String dependentSurveyInputString = splittedActionString[1];
                                String[] dependentSurveyInputStringSplitted = dependentSurveyInputString.split("#");
//								removeDuplicateItemFromSurveyDetailsList(tempRowIdForDependentSurvey);
                                if (dependentSurveyInputStringSplitted.length >= 5 && splittedActionString[0].matches(val)) {
                                    tempValForDependentSurvey = val;
                                    String[] SplittedTableDetails = dependentSurveyInputStringSplitted[3].split("%");
                                    mTableName = SplittedTableDetails[0];
                                    mSendColumn = SplittedTableDetails[1];
                                    mShowColumn = SplittedTableDetails[2];
                                    mDecision = "SAVE";
//									currentTableHeader=dependentSurveyInputStringSplitted[0];
                                    mType = dependentSurveyInputStringSplitted[5];
                                    Log.d("TAG", "mKeyValueList: 33");
                                    mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7(mTableName, mSendColumn, mShowColumn, mCustomerSelectionBasis, "");
                                    if (mKeyValueList != null && mKeyValueList.size() > 0) {
                                        Log.d("TAG", "mKeyValueList: 34");
                                        currentItemDisplayName = dependentSurveyInputStringSplitted[0];
                                        ShowListDependentViewClick(mType);
                                    } else {
                                        Utils.showToast(mContext, "No data found. Please Synchronize Data");
                                    }


                                }

                            }
                        }
                        grpDialog.cancel();
                    }
                }
            });

            Button submit =  grpDialog.findViewById(R.id.button1);
            if (type.equalsIgnoreCase("radio")) {
                submit.setVisibility(View.GONE);
            }

            submit.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    if (type.equalsIgnoreCase("checkbox")) {
                        if (mCategory == true && mSubCategory == false) {
                            mCondition = "";
                            if (mDependentRowId.trim().length() > 0) {
                                SetTextViewText(mDependentRowId, "");
                            }
                            mDependentRowId = "";
                        }
//						else if(mCategory==false && mSubCategory==true)
//						{
//							mCondition ="";
//						}

                        final SparseBooleanArray checkedItems = List
                                .getCheckedItemPositions();
                        int checkedItemsCount = checkedItems.size();
                        if (checkedItemsCount > 0) {
                            String val = "";
                            String valId = "";//if shown value and send value are different
                            for (int i = 0; i < checkedItemsCount; ++i) {
                                int position = checkedItems.keyAt(i);
                                if (checkedItems.valueAt(i)) {
                                    if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {
                                        val += values[position] + "^" + valuesQty[position] + ";";
                                    } else {
                                        val += values[position] + ";";
                                    }
                                    if (isShowValueSendValueDifferentForChcekBOx && sendColumnDataForSurveyCheckBox != null) {
                                        if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {
                                            valId += sendColumnDataForSurveyCheckBox.get(position) + "^" + valuesQty[position] + ";";

                                        } else {
                                            valId += sendColumnDataForSurveyCheckBox.get(position) + ";";
                                        }

                                    }

                                    if (mCategory == true && mSubCategory == false) {
                                        if (isShowValueSendValueDifferentForChcekBOx && sendColumnDataForSurveyCheckBox != null) {
                                            mCondition += "'" + sendColumnDataForSurveyCheckBox.get(position) + "',";
                                        } else {
                                            if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {

                                            } else {
                                                mCondition += "'" + values[position] + "',";
                                            }

                                        }

                                    } else if (mCategory == false && mSubCategory == true) {
                                        if (isShowValueSendValueDifferentForChcekBOx && sendColumnDataForSurveyCheckBox != null) {
                                            mCondition += "'" + sendColumnDataForSurveyCheckBox.get(position) + "',";
                                        } else {
                                            mCondition += "'" + values[position] + "',";
                                        }
                                    } else {

                                    }
                                }
                            }

                            if (mCondition.endsWith(",")) {
                                mCondition = mCondition.substring(0,
                                        mCondition.length() - 1);
                            }
                            if (mParentType.matches("multileveltableview")) {
                                mMultilevelTableViewData.add(val);
                                SetTextViewText(mFinalRowID, TextUtils.join(", ", mMultilevelTableViewData));
                                SetSurveyValue(mFinalRowID, TextUtils.join("#", mMultilevelTableViewData));
                            } else if (mParentType.matches("checkbox")) {
                                SetTextViewText(mFinalRowID, val);
                                if (isShowValueSendValueDifferentForChcekBOx && sendColumnDataForSurveyCheckBox != null) {
                                    SetSurveyValue(mFinalRowID, valId);
                                    isShowValueSendValueDifferentForChcekBOx = false;
                                } else {
                                    SetSurveyValue(mFinalRowID, val);
                                }

                            } else {
                                SetTextViewText(mFinalRowID, val);
                                SetSurveyValue(mFinalRowID, val);
                            }
                            if (mParentType.matches("tableview") && actionStringOfCurrentSelectedItem != null && actionStringOfCurrentSelectedItem.contains(":")) {

                                if (val.contains(";")) {
                                    mActionPressedExistingValue = "";
                                    String[] valSplitted = val.split(";");
                                    for (String s : valSplitted) {
                                        String currentVal = s.trim();
                                        if (!currentVal.matches("")) {
                                            ParseandShowActionCheckedItems(mFinalRowID, currentVal);
                                        }
                                    }

                                }

                            }
                            if (mParentType.matches("masterview") && actionStringOfCurrentSelectedItem != null && actionStringOfCurrentSelectedItem.contains("any")) {

                                if (val.contains(";")) {
                                    mActionPressedExistingValue = "";
                                    String[] valSplitted = val.split(";");
                                    for (String s : valSplitted) {
                                        String currentVal = s.trim();
                                        if (!currentVal.matches("")) {
                                            ParseandShowActionCheckedItems(mFinalRowID, currentVal);
                                        }
                                    }

                                }

                            }
                        }
                        grpDialog.cancel();

                        if (mSubtableInfo.length() > 0) {
                            if (true == ParseSubTablename(mSubtableInfo)) {
                                PrepareSurveyData(5, "");
                            }
                        }

                    } else if (type.contains("/") || type.equalsIgnoreCase("double") || type.equalsIgnoreCase("")) {
                        String val = doubleTextView.getText().toString();
                        String val2 = "";
                        String type1 = "", type2 = "";
                        Boolean isInputOk = true;
                        if (type.contains("/")) {
                            val2 = doubleTextView2.getText().toString();
                            String[] splittedType = type.split("/");
                            if (splittedType.length > 1) {
                                type1 = splittedType[0];
                                type2 = splittedType[1];
                            }
                            if (type1.equalsIgnoreCase("double")) {
                                if (!Utils.isNumeric(val))
                                    isInputOk = false;
                            } else {
                                if (val.trim().length() <= 0)
                                    isInputOk = false;
                            }
                            if (type2.equalsIgnoreCase("double")) {
                                if (!Utils.isNumeric(val2))
                                    isInputOk = false;
                            } else {
                                if (val2.trim().length() <= 0)
                                    isInputOk = false;
                            }
                            if (isInputOk) {
                                val = val + " | " + val2;
                            }
                        } else if (type.equalsIgnoreCase("double")) {
                            if (!Utils.isNumeric(val))
                                isInputOk = false;
                        } else {
                            if (val.trim().length() <= 0)
                                isInputOk = false;
                        }
                        if (isInputOk) {
                            mMultilevelTableViewData.add(val);
                            SetTextViewText(mFinalRowID, TextUtils.join(", ", mMultilevelTableViewData));
                            SetSurveyValue(mFinalRowID, TextUtils.join("#", mMultilevelTableViewData));
                            grpDialog.cancel();
                        } else {
                            Toast.makeText(mContext, "Please provide proper input.", Toast.LENGTH_SHORT).show();
                        }


                    } else {
                        grpDialog.cancel();
                    }
                }
            });
            grpDialog.show();
        }

    }

    private void HideSoftKeyBoard(EditText autoCompleteTextView1OBJ) {
        try {
            InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
            imm.hideSoftInputFromWindow(autoCompleteTextView1OBJ.getWindowToken(), 0);
        } catch (Exception e) {

        }
    }

    public void masterVIewDataLoadingProcess(String tag, Boolean CallDB) {
        Log.d("TAG", "masterVIewDataLoadingProcess: 1: "+tag);
        Boolean isDataOk = true;
        String MessageForDataNotOk = "";
        mCustomerSelectionBasisFilter = "";
        mCustomerSelectionBasis = "";
        String tablename = GetTableName(tag);
        Log.d("TAG", "masterVIewDataLoadingProcess: 2: "+tablename);
        if (tablename.contains("#")) {
            Log.d("TAG", "masterVIewDataLoadingProcess: 3");
            String[] splitablename = tablename.split("\\#");
            if (splitablename.length == 3 || splitablename.length == 4 || splitablename.length == 5) {
                Log.d("TAG", "masterVIewDataLoadingProcess: 4");
                mTableName = splitablename[0];
                mColumnName = splitablename[1];
                mType = splitablename[2];
                if (splitablename.length == 4) {
                    Log.d("TAG", "masterVIewDataLoadingProcess: 5");
                    mCustomerSelectionBasis = splitablename[3];
                    if (mTableName.equalsIgnoreCase("emp_master") || mTableName.equalsIgnoreCase("lead_generation_master")) {
                        Log.d("TAG", "masterVIewDataLoadingProcess: 6");
                        try {
                            if (splitablename[3].contains(";")) {
                                Log.d("TAG", "masterVIewDataLoadingProcess: 7");
                                String[] mEmpSelectionBasisFilterArray = splitablename[3].split("\\;");
                                this.mCustomerSelectionBasisFilter = " WHERE " + mEmpSelectionBasisFilterArray[0] + "='" + mEmpSelectionBasisFilterArray[1] + "' ";
                            }
                        } catch (Exception e) {
                            Log.d("TAG", "masterVIewDataLoadingProcess: 8");
                        }
                    }
                }

                if (splitablename.length == 5) {
                    Log.d("TAG", "masterVIewDataLoadingProcess: 9");
                    String dataFetchLogic = splitablename[4];
                    if (dataFetchLogic.contains("&") && CallDB) {
                        Log.d("TAG", "masterVIewDataLoadingProcess: 10");
                        String[] dataFetchLogicArray = dataFetchLogic.split("\\&");
                        for (int i = 0; i < dataFetchLogicArray.length; i++) {
                            Log.d("TAG", "masterVIewDataLoadingProcess: 11 "+i);
                            String[] mCustomerSelectionBasisFilterArray = dataFetchLogicArray[i].split("\\;");
                            if (mCustomerSelectionBasisFilterArray.length == 2) {
                                Log.d("TAG", "masterVIewDataLoadingProcess: 12");
                                String dataSelectionLogicValue = mCustomerSelectionBasisFilterArray[1];
                                if (dataSelectionLogicValue.equalsIgnoreCase("loginempcode"))//hardcoded
                                {
                                    Log.d("TAG", "masterVIewDataLoadingProcess: 13");
                                    mCustomerSelectionBasisFilterArray[1] = Constants.employeeDetailObject.getEmpCode();
                                }
                                if (dataSelectionLogicValue.contains("row_id-")) {
                                    Log.d("TAG", "masterVIewDataLoadingProcess: 14");
                                    String dependingOnRowId = dataSelectionLogicValue.split("-")[1];
                                    if (CheckGivenRowIdHasValue(dependingOnRowId)) {
                                        Log.d("TAG", "masterVIewDataLoadingProcess: 15");
                                        if (this.mCustomerSelectionBasisFilter.matches("")) {
                                            Log.d("TAG", "masterVIewDataLoadingProcess: 16");
                                            this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCheckValue + "' ";
                                        } else {
                                            Log.d("TAG", "masterVIewDataLoadingProcess: 17");
                                            this.mCustomerSelectionBasisFilter = this.mCustomerSelectionBasisFilter + " AND " + mCustomerSelectionBasisFilterArray[0] + "='" + mCheckValue + "' ";
                                        }
                                    } else {
                                        Log.d("TAG", "masterVIewDataLoadingProcess: 18");
                                        isDataOk = false;
                                        MessageForDataNotOk = "Please select " + mDependentDisplayName + " first.";
                                        break;
                                    }
                                } else {
                                    Log.d("TAG", "masterVIewDataLoadingProcess: 19");
                                    this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCustomerSelectionBasisFilterArray[1] + "' ";
                                }
                            }
                        }
                    } else {
                        Log.d("TAG", "masterVIewDataLoadingProcess: 20");
                        mCustomerSelectionBasisFilter = splitablename[4];
                        String[] mCustomerSelectionBasisFilterArray = splitablename[4].split("\\;");
                        if (mCustomerSelectionBasisFilterArray.length == 2) {
                            Log.d("TAG", "masterVIewDataLoadingProcess: 21");
                            if (mCustomerSelectionBasisFilterArray[1].equalsIgnoreCase("loginempcode"))//hardcoded
                            {
                                Log.d("TAG", "masterVIewDataLoadingProcess: 22");
                                mCustomerSelectionBasisFilterArray[1] = Constants.employeeDetailObject.getEmpCode();
                            }
                            if (mCustomerSelectionBasisFilterArray[1].contains("row_id-")) {
                                Log.d("TAG", "masterVIewDataLoadingProcess: 23");
                                String dependingOnRowId = mCustomerSelectionBasisFilterArray[1].split("-")[1];
                                if (CheckGivenRowIdHasValue(dependingOnRowId)) {
                                    Log.d("TAG", "masterVIewDataLoadingProcess: 24");
                                    if (mCheckValue.equalsIgnoreCase("NEW")) {
                                        Log.d("TAG", "masterVIewDataLoadingProcess: 25");
                                        this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='' OR " + mCustomerSelectionBasisFilterArray[0] + "=' ' OR " + mCustomerSelectionBasisFilterArray[0] + " is NULL";
                                    } else {
                                        Log.d("TAG", "masterVIewDataLoadingProcess: 26");
                                        this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCheckValue + "' ";
                                    }
                                } else {
                                    Log.d("TAG", "masterVIewDataLoadingProcess: 27");
                                    this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCustomerSelectionBasisFilterArray[1] + "' ";
                                }
                            } else {
                                Log.d("TAG", "masterVIewDataLoadingProcess: 28");
                                this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCustomerSelectionBasisFilterArray[1] + "' ";
                            }
                        }
                    }

                }
                if (isDataOk) {
                    Log.d("TAG", "masterVIewDataLoadingProcess: 29");
                    if (mColumnName.contains("%")) {
                        Log.d("TAG", "masterVIewDataLoadingProcess: 30");
                        String[] splitColunName = mColumnName.split("\\%");
                        mSendColumn = splitColunName[0];
                        mShowColumn = splitColunName[1];
                        mShowColumn = multipleColumnQueryBuildUp(mShowColumn);

                        if (tag.equalsIgnoreCase("RA728")) {
                            Log.d("TAG", "masterVIewDataLoadingProcess: 31");
                            this.mCustomerSelectionBasisFilter = " WHERE DATE(substr(cube_test_date,7,4)||'-'||substr(cube_test_date,4,2)||'-'||substr(cube_test_date,1,2))  BETWEEN DATE(?,'-31 days') AND DATE(?) ";
                        }
                        mDecision = "SAVE";
                        if (CallDB) {
                            Log.d("TAG", "masterVIewDataLoadingProcess: 32");
                            PrepareSurveyData(7, "");
                        }

                    } else {
                        Log.d("TAG", "masterVIewDataLoadingProcess: 33");
                        if (CallDB)
                            PrepareSurveyData(6, "");
                    }
                } else {
                    Log.d("TAG", "masterVIewDataLoadingProcess: 34");
                    Utils.showToast(mContext, MessageForDataNotOk);
                }

            } else {
                Log.d("TAG", "masterVIewDataLoadingProcess: 35");
                if (CallDB) {
                    Log.d("TAG", "masterVIewDataLoadingProcess: 36");
                    Utils.showToast(mContext, "No data found.Please Synchronize Data");
                }
            }
        } else {
            Log.d("TAG", "masterVIewDataLoadingProcess: 37");
            if (CallDB) {
                Log.d("TAG", "masterVIewDataLoadingProcess: 38");
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        }
    }

    public boolean CheckGivenRowIdHasValue(String rowID) {
        String rowid = "";
        boolean isSucess = true;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mDependentDisplayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                if (mInputTimeSurveyDetailsList.get(count).getValue().trim().length() > 0) {
                    mCheckValue = mInputTimeSurveyDetailsList.get(count).getValue();
                    isSucess = true;
                } else {
                    isSucess = false;
                }
                break;
            }
        }
        return isSucess;
    }

    public String getDisplayNameByRowId(String rowID) {
        String rowid = "";
        String displayName = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                displayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();

                break;
            }
        }
        return displayName;
    }

    public String getValueByRowId(String rowID) {
        String rowid = "";
        String displayName = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                displayName = mInputTimeSurveyDetailsList.get(count).getValue();

                break;
            }
        }
        return displayName;
    }

    public String multipleColumnQueryBuildUp(String mShowColumn) {
        if (mShowColumn.contains("&")) {
            String[] showColumnArray = mShowColumn.split("&");
            mShowColumn = "";
            for (int i = 0; i < showColumnArray.length; i++) {
                if (mShowColumn.matches("")) {
                    mShowColumn = showColumnArray[i];
                } else {
                    mShowColumn = mShowColumn + "||', '||" + showColumnArray[i];
                }
            }
        }
        return mShowColumn;
    }

    public void historyViewDataLoadingProcess(String tag) {
        mCustomerSelectionBasisFilter = "";
        mCustomerSelectionBasis = "";
        String tablename = GetTableName(tag);
        if (tablename.contains("#")) {
            String[] splitablename = tablename.split("\\#");
            if (splitablename.length == 3) {
                mTableName = splitablename[0];
                mColumnName = splitablename[1];
                mType = splitablename[2];

                if (mColumnName.contains("%")) {
                    String[] splitColunName = mColumnName.split("\\%");
                    mShowColumnCount = splitColunName.length;
                    if (mShowColumnCount == 3 || mShowColumnCount == 6) {

                        mShowColumn1 = splitColunName[0];
                        mShowColumn2 = splitColunName[1];
                        mShowColumn3 = splitColunName[2];
                        if (mShowColumnCount == 6) {
                            mShowColumn4 = splitColunName[3];
                            mShowColumn5 = splitColunName[4];
                            mShowColumn6 = splitColunName[5];
                        }


                        mDecision = "SAVE";
                        if (mType.contains("&&")) {
                            Boolean isDataOk = true;
                            String[] mTypeSplitted = mType.split("&&");
                            for (int x = 0; x < mTypeSplitted.length; x++) {
                                if (mTypeSplitted[x].contains(";")) {
                                    String[] splitColunNameWhere = mTypeSplitted[x].split(";");
                                    if (splitColunNameWhere.length == 2) {
                                        String columnName = splitColunNameWhere[0];
                                        String valueofRowId = splitColunNameWhere[1];
                                        if (CheckSurveyDependentOnOtherInputValue(valueofRowId)) {
                                            if (mCheckValue.contains(":")) {
                                                String[] checkinValueSplitted = mCheckValue.split(":");
                                                mCheckValue = checkinValueSplitted[checkinValueSplitted.length - 1];
                                            }
                                            if (valueofRowId.equalsIgnoreCase("RA025")) {
                                                columnName = "lower(" + columnName + ")";
                                                mCheckValue = mCheckValue.toLowerCase();
                                            } else if (valueofRowId.equalsIgnoreCase("RA065"))//ROUND(replace(area,'ha',''),2) = 3
                                            {
                                                columnName = "ROUND(replace(" + columnName + ",'ha',''),2)";
                                            }
                                            if (x == 0) {
                                                mCustomerSelectionBasisFilter = " WHERE " + columnName + " = '" + mCheckValue + "' ";
                                            } else {
                                                if (valueofRowId.equalsIgnoreCase("RA065")) {
                                                    mCustomerSelectionBasisFilter = mCustomerSelectionBasisFilter + " AND " + columnName + " =" + mCheckValue + " ";
                                                } else {
                                                    mCustomerSelectionBasisFilter = mCustomerSelectionBasisFilter + " AND " + columnName + " = '" + mCheckValue + "' ";
                                                }
                                            }
                                        } else {
                                            Utils.showToast(mContext, "Please select " + mDependentDisplayName + " first");
                                            isDataOk = false;
                                            break;
                                        }

                                    } else {
                                        Utils.showToast(mContext, "No data found.Please Synchronize Data");
                                        isDataOk = false;
                                        break;
                                    }
                                } else {
                                    Utils.showToast(mContext, "No data found.Please Synchronize Data");
                                    isDataOk = false;
                                    break;

                                }
                            }
                            if (isDataOk) {
                                PrepareSurveyData(13, "");
                            }

                        } else {
                            if (mType.contains(";")) {
                                String[] splitColunNameWhere = mType.split(";");
                                if (splitColunNameWhere.length == 2) {
                                    String columnName = splitColunNameWhere[0];
                                    String valueofRowId = splitColunNameWhere[1];
                                    if (CheckSurveyDependentOnOtherInputValue(valueofRowId)) {
                                        if (mCheckValue.contains(":")) {
                                            String[] checkinValueSplitted = mCheckValue.split(":");
                                            mCheckValue = checkinValueSplitted[checkinValueSplitted.length - 1];
                                        }
                                        if (valueofRowId.equalsIgnoreCase("RA065")) {
                                            mCheckValue = mCheckValue + " ha";
                                        }
                                        mCustomerSelectionBasisFilter = " WHERE " + columnName + " = '" + mCheckValue + "' ";
                                        PrepareSurveyData(13, "");
                                    } else {
                                        Utils.showToast(mContext, "Please select " + mDependentDisplayName + " first");
                                    }

                                } else {
                                    Utils.showToast(mContext, "No data found.Please Synchronize Data");
                                }
                            } else {
                                Utils.showToast(mContext, "No data found.Please Synchronize Data");
                            }
                        }


                    } else {
                        Utils.showToast(mContext, "No data found.Please Synchronize Data");
                    }


                } else {
                    Utils.showToast(mContext, "No data found.Please Synchronize Data");
                }
            } else {
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        } else {
            Utils.showToast(mContext, "No data found.Please Synchronize Data");
        }
    }

    public void historyViewInputDataLoadingProcess(String tag) {
        mCustomerSelectionBasisFilter = "";
        mCustomerSelectionBasis = "";
        String miTYpeVal = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            String currentRowId = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (currentRowId.equalsIgnoreCase("RA025")) {
                miTYpeVal = mInputTimeSurveyDetailsList.get(count).getValue().trim();
            }
        }
        if (miTYpeVal.length() > 0 && miTYpeVal.contains("#")) {
            String[] miTypeSplitted = miTYpeVal.split("#");

            String query = "Select distinct prod_code,prod_desc,uom,rate From additional_material WHERE lower(mi_type) = '" + miTypeSplitted[0].toLowerCase() + "'  order by prod_desc";
//            String query="Select distinct "+mSendColumn+", "+mShowColumn+" From "+mTableName+" WHERE lower(mi_type) = '"+miTYpeVal.toLowerCase()+"'  AND lower(area) != '"+areaVal.toLowerCase()+"' order by "+mShowColumn;
            PrepareSurveyData(14, query);


        } else {
            Utils.showToast(mContext, "Please select MI Type first");
        }

    }

    private void getActionOfCurrentItem(String rowID) {
        String rowid = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                actionStringOfCurrentSelectedItem = mInputTimeSurveyDetailsList.get(count).getAction();
            }
        }
    }

    public String GetTableName(String rowID) {
        String tablename = "";
        String rowid = "";
        mType = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                tablename = mInputTimeSurveyDetailsList.get(count).getTableName();
                mType = mInputTimeSurveyDetailsList.get(count).getType();
                break;
            }
        }
        return tablename;
    }

    public void ParseandShowAction(String rowID) {
        String actionstring = "";
        String rowid = "";
        String validation = "0";
        mType = "";

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                actionstring = mInputTimeSurveyDetailsList.get(count).getAction();
                Log.d("TAG", "ParseandShowAction: "+actionstring);
                if (actionstring.contains("#")) {
                    String[] RowData = actionstring.split("\\#");
                    if (RowData.length > 0) {
                        String actiondisplayname = RowData[0];
                        String actiontype = RowData[1];
                        String actionmandatory = RowData[2];
                        String actionvalidation = "";
                        String tablename = "";

                        if (actiondisplayname.contains(";")) {
                            String[] splitdisplay = actiondisplayname.split("\\;");
                            String actionsubdisplayname = splitdisplay[0];
                            String noofsubdisplay = splitdisplay[1];

                            String subactionmandatory = "";
                            String subactionno = "";

                            if (actionmandatory.contains(";")) {
                                String[] splitmandatory = actionstring.split("\\;");
                                subactionmandatory = splitmandatory[0];
                                subactionno = splitmandatory[1];
                            } else {
                                subactionmandatory = actionmandatory;
                                subactionno = "0";
                            }

                            if (actiontype.equalsIgnoreCase("checkbox")) {

                            } else if (actiontype.equalsIgnoreCase("radio")) {

                            } else {
                                actionvalidation = RowData[4];
                                ShowSubActionLayout(actionsubdisplayname,
                                        Integer.valueOf(noofsubdisplay),
                                        actiontype, subactionmandatory,
                                        Integer.valueOf(subactionno), "", actionvalidation);
                            }

                        } else {
                            if (actiontype.equalsIgnoreCase("checkbox")) {
                                tablename = RowData[3];
                                mType = actiontype;
                                PrepareSurveyData(2, tablename);
                            } else if (actiontype.equalsIgnoreCase("radio")) {
                                tablename = RowData[3];
                                mType = actiontype;
                                PrepareSurveyData(2, tablename);
                            } else {
                                actionvalidation = RowData[4];
                                ShowActionLayout(actiondisplayname, actiontype, actionmandatory, "", actionvalidation);
                            }
                        }
                    }
                } else {
                    validation = mInputTimeSurveyDetailsList.get(count).getValidation().trim();
                    if (actionstring.equalsIgnoreCase("Click")) {
                        SetMaxNoofImage(rowID, validation);
                        ShowImageCaptureLayer(rowID);
                    }
                    else if (actionstring.equalsIgnoreCase("floor")) {
                        PrepareSurveyData(10, "");
                    }
                    else {
                        openDatePicker(rowID, actionstring, validation);
                    }
                }
                break;
            }
        }
    }

    public void ParseandShowActionForDynamicView(String rowID, String noneed1, String noneed2) {
        String actionstring = "";
        String rowid = "";
        mType = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                actionstring = mInputTimeSurveyDetailsList.get(count).getAction().trim();
                if (actionstring.contains("#")) {
                    String[] actionsplit = actionstring.split("\\#");
                    if (actionsplit.length > 0) {
                        String displayname = "";
                        String typelist = "";
                        String mandatory = "";
                        String validation = "";
                        for (int countx = 0; countx < actionsplit.length; countx++) {
                            if (countx == 0) {// Display Name
                                displayname = actionsplit[countx];
                            } else if (countx == 1) {// Type
                                typelist = actionsplit[countx];
                            } else if (countx == 2) {// Mandatory
                                mandatory = actionsplit[countx];
                            } else if (countx == 3) {// Validation
                                validation = actionsplit[countx];
                            }
                        }
                        if (mEdiTextDynamic != null && mEdiTextDynamic.getText().toString().length() > 0) {
                            int repeat = Integer.parseInt(mEdiTextDynamic.getText().toString());
                            ShowSubActionLayoutDynamicView(displayname, typelist, mandatory, validation, repeat);
                        } else {
                            Utils.showToast(mContext, "Please provide the input");
                        }
                    } else {
                        Utils.showToast(mContext, "Error in data.Please Synchronize Data");
                    }
                }
            }
        }
    }

    public void ParseandShowAction(String rowID, String value) {
        mActionPressed = value;
        dialogHeaderSearchTypeDynamicText = "Please select " + mActionPressed;
        String actionstring = "";
        String rowid = "";
        mType = "";
        if (rowID.equalsIgnoreCase("RA060"))//quotation generated
        {
            if (value.equalsIgnoreCase("yes")) {
                String areaVal = "", miTYpeVal = "";
                for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
                    String currentRowId = mInputTimeSurveyDetailsList.get(count).getRowId();
                    if (currentRowId.equalsIgnoreCase("RA065")) {
                        areaVal = mInputTimeSurveyDetailsList.get(count).getValue().trim();
                    }
                    if (currentRowId.equalsIgnoreCase("RA025")) {
                        miTYpeVal = mInputTimeSurveyDetailsList.get(count).getValue().trim();
                    }
                }
                if (Utils.isNumeric(areaVal) && miTYpeVal.length() > 0) {
//                    areaVal=areaVal+" ha";//CAST('3.02' as decimal)
                    String query = "SELECT sum(amount) FROM BOQ_master WHERE lower(mi_type) = '" + miTYpeVal.toLowerCase() + "' and  ROUND(replace(area,'ha',''),2) = " + areaVal + "  ORDER BY sl_no ASC";
                    String val = mAceDnsDatabase.getValueForQuotaion(query);
                    if (Utils.isNumeric(val) && Double.parseDouble(val) > 0) {
                        val = defaultFormatWithComma.format(Double.parseDouble(val));
                    } else//clear checked item
                    {
                        for (int count = 0; count < mRadioGrpList.size(); count++) {
                            if (mRadioGrpList.get(count).getTag().toString().equalsIgnoreCase("RA060")) {
                                mRadioGrpList.get(count).clearCheck();
                                break;
                            }
                        }
                        Utils.showToast(mContext, "Valid boq data unavailable. Quotation could not be created.");
                        return;
                    }
                    SetSurveyValue(mFinalRowID, "yes");
                    //disable row if yes
                    for (int count = 0; count < mRadioGrpList.size(); count++) {
                        RadioGroup radioGroupCurrent = mRadioGrpList.get(count);
                        if (radioGroupCurrent.getTag().toString().equalsIgnoreCase("RA060")) {
                            for (int i = 0; i < radioGroupCurrent.getChildCount(); i++) {
                                radioGroupCurrent.getChildAt(i).setEnabled(false);
                            }
                            break;
                        }
                    }
                    disableEnableControls(false, (ViewGroup) mParentLayout);
//                    mParentLayout.
//                    mParentLayout.removeAllViews();
                    mParentLayout.setOnTouchListener(new View.OnTouchListener() {
                        @Override
                        public boolean onTouch(View v, MotionEvent event) {
                            return true;
                        }
                    });

//                    ChangeVisibility(mFinalRowID, val.toLowerCase());
                    //        viewHolder.clstkTV.setText(HtmlCompat.fromHtml("<font color='#D7B56D'>"+ nameValuesProductListLocalDo.get(position).getClosingStk()+"</font>",HtmlCompat.FROM_HTML_MODE_LEGACY));
                    Utils.ShowAlertDialogCommon(mContext, "Quotation:YES!", "Reference to your BOQ the Quotation has dully generated and sent to your official email ID and a copy to your HO. Please check your mail. Your Quotation Value is <font color='#D7B56D'>Rs. " + val + " + GST</font> as applicable. Thanks for your contribution", "OK");
//                    Utils.ShowAlertDialogCommon(mContext, "Please Note!", "No customer found for auction! Please Synchronize Data.", "OK");
                } else if (!Utils.isNumeric(areaVal)) {
                    for (int count = 0; count < mRadioGrpList.size(); count++) {
                        if (mRadioGrpList.get(count).getTag().toString().equalsIgnoreCase("RA060")) {
                            mRadioGrpList.get(count).clearCheck();
                            break;
                        }
                    }
                    Utils.showToast(mContext, "Please select area first");
                } else {
                    for (int count = 0; count < mRadioGrpList.size(); count++) {
                        if (mRadioGrpList.get(count).getTag().toString().equalsIgnoreCase("RA060")) {
                            mRadioGrpList.get(count).clearCheck();
                            break;
                        }
                    }
                    Utils.showToast(mContext, "Please select MI Type first");
                }

            } else {
                SetSurveyValue(mFinalRowID, "no");
            }
        } else {
            for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
                rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
                if (rowID.equalsIgnoreCase(rowid)) {
                    mDisplayName = mInputTimeSurveyDetailsList.get(count).getDisplayName().trim() + " : " + value;
                    actionstring = mInputTimeSurveyDetailsList.get(count).getAction().trim();
                    actionStringOfCurrentSelectedItem = actionstring;
                    if (actionstring.contains("$")) {
                        String[] actionsplit = actionstring.split("\\$");
                        if (actionsplit.length > 0) {
                            for (int actioncount = 0; actioncount < actionsplit.length; actioncount++) {
                                if (actionsplit[actioncount].trim().toLowerCase().startsWith(value.toLowerCase())) {
                                    String[] subaction = actionsplit[actioncount].split("\\:");
                                    if (subaction.length == 2) {
                                        if (subaction[1] != null && subaction[1].trim().length() > 0) {
                                            if (subaction[1].trim().contains("@")) {
                                                String[] subactionlist = subaction[1].split("\\@");
                                                ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                                for (int subactioncount = 0; subactioncount < subactionlist.length; subactioncount++) {
                                                    String[] subactiondetaislis = subactionlist[subactioncount].split("\\#");
                                                    SurveyInput obj = new SurveyInput();
                                                    for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                                        if (actiondetailscount == 0) {// Display Name
                                                            obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 1) {// Type
                                                            obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 2) {// Mandatory
                                                            obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 3) {// Table Details
                                                            obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 4) {// Validation
                                                            obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 5) {// Sub Type
                                                            obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 6) {// Clause
                                                            obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 7) {//Row_id_dependency_clause
                                                            obj.setRow_id_dependency_clause(subactiondetaislis[actiondetailscount]);
                                                        }
                                                    }
                                                    surveyInputlist.add(obj);
                                                    obj = null;
                                                }
                                                ShowSubActionLayout(surveyInputlist, rowID, false, "");

                                            } else {
                                                if (subaction[1].contains("#")) {
                                                    String[] subactiondetaislis = subaction[1].split("\\#");
                                                    ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                                    SurveyInput obj = new SurveyInput();
                                                    for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                                        if (actiondetailscount == 0) {// Display Name
                                                            obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 1) {// Type
                                                            obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 2) {// Mandatory
                                                            obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 3) {// Table Name
                                                            obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 4) {// validation
                                                            obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 5) {// Sub Type
                                                            obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 6) {// Clause
                                                            obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                                        } else if (actiondetailscount == 7) {//Row_id_dependency_clause
                                                            obj.setRow_id_dependency_clause(subactiondetaislis[actiondetailscount]);
                                                        }
                                                    }
                                                    surveyInputlist.add(obj);
                                                    obj = null;
                                                    ShowSubActionLayout(surveyInputlist, rowID, false, "");
                                                }
                                            }

                                        } else {
                                            SetTextViewText(rowID, value);
                                            SetSurveyValue(rowID, value);
                                        }
                                    } else {
                                        SetTextViewText(rowID, value);
                                        SetSurveyValue(rowID, value);
                                    }
                                }
                            }
                        }
                    } else {
                        if (actionstring.trim().startsWith(value)) {
                            String[] subaction = actionstring.split("\\:");
                            if (subaction.length == 2) {
                                if (subaction[1] != null && subaction[1].trim().length() > 0) {
                                    if (subaction[1].trim().contains("@")) {
                                        String[] subactionlist = subaction[1].split("\\@");
                                        ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                        for (int subactioncount = 0; subactioncount < subactionlist.length; subactioncount++) {
                                            String[] subactiondetaislis = subactionlist[subactioncount].split("\\#");
                                            SurveyInput obj = new SurveyInput();
                                            for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                                if (actiondetailscount == 0) {// Display Name
                                                    obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 1) {// Type
                                                    obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 2) {// Mandatory
                                                    obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 3) {// Table Details
                                                    obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 4) {// Validation
                                                    obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 5) {// Sub Type
                                                    obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 6) {// Clause
                                                    obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                                }
                                            }
                                            surveyInputlist.add(obj);
                                            obj = null;
                                        }
                                        ShowSubActionLayout(surveyInputlist, rowID, false, "");

                                    } else {
                                        if (subaction[1].contains("#")) {
                                            String[] subactiondetaislis = subaction[1].split("\\#");
                                            ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                            SurveyInput obj = new SurveyInput();
                                            for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                                if (actiondetailscount == 0) {// Display Name
                                                    obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 1) {// Type
                                                    obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 2) {// Mandatory
                                                    obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 3) {// Table Name
                                                    obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 4) {// validation
                                                    obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 5) {// Sub Type
                                                    obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 6) {// Clause
                                                    obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                                }
                                            }
                                            surveyInputlist.add(obj);
                                            obj = null;
                                            ShowSubActionLayout(surveyInputlist, rowID, false, "");
                                        }
                                    }

                                } else {
                                    SetTextViewText(rowID, value);
                                    SetSurveyValue(rowID, value);
                                }
                            } else {
                                SetTextViewText(rowID, value);
                                SetSurveyValue(rowID, value);
                            }
                        }


                    }
                    break;
                }
            }
            if (isCurrentRowIndependent(mFinalRowID)) {
                EnableDisableHistoryView(rowID, value);
            }
        }

    }

    private void disableEnableControls(boolean enable, ViewGroup vg) {
        for (int i = 0; i < vg.getChildCount(); i++) {
            View child = vg.getChildAt(i);
            child.setEnabled(enable);
            if (child instanceof ViewGroup) {
                disableEnableControls(enable, (ViewGroup) child);
            }
        }
    }

    public void ParseandShowActionCheckedItems(String rowID, String value) {
        mActionPressed = value;
        dialogHeaderSearchTypeDynamicText = "Please select " + mActionPressed;
        String actionstring = "";
        String rowid = "";
        mType = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mDisplayName = mInputTimeSurveyDetailsList.get(count).getDisplayName().trim() + " : " + value;
                actionstring = mInputTimeSurveyDetailsList.get(count).getAction().trim();
                actionStringOfCurrentSelectedItem = actionstring;
                if (actionstring.contains("$")) {
                    String[] actionsplit = actionstring.split("\\$");
                    if (actionsplit.length > 0) {
                        for (int actioncount = 0; actioncount < actionsplit.length; actioncount++) {
                            if (actionsplit[actioncount].trim().startsWith(value)) {
                                String[] subaction = actionsplit[actioncount].split("\\:");
                                if (subaction.length == 2) {
                                    if (subaction[1] != null && subaction[1].trim().length() > 0) {
                                        if (subaction[1].trim().contains("@")) {
                                            String[] subactionlist = subaction[1].split("\\@");
                                            ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                            for (int subactioncount = 0; subactioncount < subactionlist.length; subactioncount++) {
                                                String[] subactiondetaislis = subactionlist[subactioncount].split("\\#");
                                                SurveyInput obj = new SurveyInput();
                                                for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                                    if (actiondetailscount == 0) {// Display Name
                                                        obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 1) {// Type
                                                        obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 2) {// Mandatory
                                                        obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 3) {// Table Details
                                                        obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 4) {// Validation
                                                        obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 5) {// Sub Type
                                                        obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 6) {// Clause
                                                        obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                                    }
                                                }
                                                surveyInputlist.add(obj);
                                                obj = null;
                                            }
                                            ShowSubActionLayoutCheckedItem(surveyInputlist, rowID, true, value);

                                        } else {
                                            if (subaction[1].contains("#")) {
                                                String[] subactiondetaislis = subaction[1].split("\\#");
                                                ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                                SurveyInput obj = new SurveyInput();
                                                for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                                    if (actiondetailscount == 0) {// Display Name
                                                        obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 1) {// Type
                                                        obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 2) {// Mandatory
                                                        obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 3) {// Table Name
                                                        obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 4) {// validation
                                                        obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 5) {// Sub Type
                                                        obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                                    } else if (actiondetailscount == 6) {// Clause
                                                        obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                                    }
                                                }
                                                surveyInputlist.add(obj);
                                                obj = null;
                                                ShowSubActionLayoutCheckedItem(surveyInputlist, rowID, true, value);
                                            }
                                        }

                                    } else {
                                        SetTextViewText(rowID, value);
                                        SetSurveyValue(rowID, value);
                                    }
                                } else {
                                    SetTextViewText(rowID, value);
                                    SetSurveyValue(rowID, value);
                                }
                            }
                        }
                    }
                } else {
                    if (actionstring.trim().startsWith(value)) {
                        String[] subaction = actionstring.split("\\:");
                        if (subaction.length == 2) {
                            if (subaction[1] != null && subaction[1].trim().length() > 0) {
                                if (subaction[1].trim().contains("@")) {
                                    String[] subactionlist = subaction[1].split("\\@");
                                    ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                    for (int subactioncount = 0; subactioncount < subactionlist.length; subactioncount++) {
                                        String[] subactiondetaislis = subactionlist[subactioncount].split("\\#");
                                        SurveyInput obj = new SurveyInput();
                                        for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                            if (actiondetailscount == 0) {// Display Name
                                                obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 1) {// Type
                                                obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 2) {// Mandatory
                                                obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 3) {// Table Details
                                                obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 4) {// Validation
                                                obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 5) {// Sub Type
                                                obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 6) {// Clause
                                                obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                            }
                                        }
                                        surveyInputlist.add(obj);
                                        obj = null;
                                    }
                                    ShowSubActionLayout(surveyInputlist, rowID, false, "");

                                } else {
                                    if (subaction[1].contains("#")) {
                                        String[] subactiondetaislis = subaction[1].split("\\#");
                                        ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                        SurveyInput obj = new SurveyInput();
                                        for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                            if (actiondetailscount == 0) {// Display Name
                                                obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 1) {// Type
                                                obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 2) {// Mandatory
                                                obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 3) {// Table Name
                                                obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 4) {// validation
                                                obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 5) {// Sub Type
                                                obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 6) {// Clause
                                                obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                            }
                                        }
                                        surveyInputlist.add(obj);
                                        obj = null;
                                        ShowSubActionLayout(surveyInputlist, rowID, false, "");
                                    }
                                }

                            } else {
                                SetTextViewText(rowID, value);
                                SetSurveyValue(rowID, value);
                            }
                        } else {
                            SetTextViewText(rowID, value);
                            SetSurveyValue(rowID, value);
                        }
                    }


                    if (actionstring.trim().startsWith("any")) {
                        String[] subaction = actionstring.split("\\:");
                        if (subaction.length == 2) {
                            if (subaction[1] != null && subaction[1].trim().length() > 0) {
                                if (subaction[1].trim().contains("@")) {
                                    String[] subactionlist = subaction[1].split("\\@");
                                    ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                    for (int subactioncount = 0; subactioncount < subactionlist.length; subactioncount++) {
                                        String[] subactiondetaislis = subactionlist[subactioncount].split("\\#");
                                        SurveyInput obj = new SurveyInput();
                                        for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                            if (actiondetailscount == 0) {// Display Name
                                                obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 1) {// Type
                                                obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 2) {// Mandatory
                                                obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 3) {// Table Details
                                                obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 4) {// Validation
                                                obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 5) {// Sub Type
                                                obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 6) {// Clause
                                                obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                            }
                                        }
                                        surveyInputlist.add(obj);
                                        obj = null;
                                    }
                                    //ShowSubActionLayout(surveyInputlist, rowID,true,value);
                                    ShowSubActionLayoutCheckedItem(surveyInputlist, rowID, true, value);

                                } else {
                                    if (subaction[1].contains("#")) {
                                        String[] subactiondetaislis = subaction[1].split("\\#");
                                        ArrayList<SurveyInput> surveyInputlist = new ArrayList<SurveyInput>();
                                        SurveyInput obj = new SurveyInput();
                                        for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {

                                            if (actiondetailscount == 0) {// Display Name
                                                obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 1) {// Type
                                                obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 2) {// Mandatory
                                                obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 3) {// Table Name
                                                obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 4) {// validation
                                                obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 5) {// Sub Type
                                                obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                            } else if (actiondetailscount == 6) {// Clause
                                                obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                            }
                                        }
                                        surveyInputlist.add(obj);
                                        obj = null;

                                        ShowSubActionLayout(surveyInputlist, rowID, true, value);
                                        //ShowSubActionLayoutCheckedItem(surveyInputlist, rowID,true,value);
                                    }
                                }

                            } else {
                                SetTextViewText(rowID, value);
                                SetSurveyValue(rowID, value);
                            }
                        } else {
                            SetTextViewText(rowID, value);
                            SetSurveyValue(rowID, value);
                        }
                    }

                }
                break;
            }
        }
        if (isCurrentRowIndependent(mFinalRowID)) {
            EnableDisableHistoryView(rowID, value);
        }
    }

    public void ChooseDateDialog(final String rowid, final String action) {

        final Dialog dateDialog = new Dialog(mContext, R.style.PauseDialog);
        dateDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dateDialog.setContentView(R.layout.date_dialog);
        dateDialog.setCancelable(true);

        Button submit =  dateDialog.findViewById(R.id.buttonDone);
        final EditText date =  dateDialog.findViewById(R.id.editTextDate);
        date.setHint(action);
        final ImageView imageViewBack =  dateDialog.findViewById(R.id.image_cancel);
        imageViewBack.setVisibility(VISIBLE);
        imageViewBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                dateDialog.cancel();
            }
        });
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                boolean isOk = false;
                mDateValue = date.getText().toString();
                if (mDateValue.contains("/")) {
                    String[] splitdate = mDateValue.split("/");
                    if (splitdate.length == 3) {
                        boolean isdate = ContainsOnlyNumbers(splitdate[0]);
                        boolean ismonth = ContainsOnlyNumbers(splitdate[1]);
                        boolean isyear = ContainsOnlyNumbers(splitdate[2]);
                        if (true == isdate && true == ismonth && true == isyear) {
                            String day = splitdate[0];
                            String month = splitdate[1];
                            int year = Integer.parseInt(splitdate[2]);

                            if (day.equals("31") &&
                                    (month.equals("4") || month.equals("6") || month.equals("9") ||
                                            month.equals("11") || month.equals("04") || month.equals("06") ||
                                            month.equals("09"))) {
                                isOk = false; // only 1,3,5,7,8,10,12 has 31 days
                            } else if (month.equals("2") || month.equals("02")) {
                                //leap year
                                if (year % 4 == 0) {
                                    if (Integer.parseInt(day) > 29) {
                                        isOk = false;
                                    } else {
                                        isOk = true;
                                    }
                                } else {
                                    if (Integer.parseInt(day) > 28) {
                                        isOk = false;
                                    } else {
                                        isOk = true;
                                    }
                                }
                            } else if (Integer.parseInt(day) > 31 || Integer.parseInt(day) < 1) {
                                isOk = false;
                            } else if (Integer.parseInt(month) > 12) {
                                isOk = false;
                            } else {
                                isOk = true;
                            }

                        } else {
                            isOk = false;
                        }

                    } else {
                        isOk = false;

                    }
                } else {
                    isOk = false;
                }

                if (isOk) {
                    SetTextViewText(rowid, mDateValue);
                    SetSurveyValue(rowid, mDateValue);
                    dateDialog.cancel();
                } else {
                    Utils.showToast(mContext, "Please provide valid date");
                }

            }
        });

        dateDialog.show();
    }

    public void openDatePicker(final String rowID, final String actionstring, final String validation) {
        // Get Current Date
        final Calendar c = Calendar.getInstance();
        mYear = c.get(Calendar.YEAR);
        mMonth = c.get(Calendar.MONTH);
        mDay = c.get(Calendar.DAY_OF_MONTH);
        //launch datepicker modal
        DatePickerDialog datePickerDialog = new DatePickerDialog(this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
                        try {
                            mDateValue = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
                            mDateValue = Utils.changeDateFormat("dd/M/yyyy", actionstring, mDateValue);
                            SimpleDateFormat formatter = new SimpleDateFormat(actionstring);

                            Boolean isValidationPassed = true;
                            int comparisonResult = new Date().compareTo(formatter.parse(mDateValue));
                            if (validation.matches("=current_date")) {
                                if (comparisonResult != 0) {
                                    isValidationPassed = false;
                                }
                            } else if (validation.matches(">=current_date")) {

                                if (comparisonResult > 0) {
                                    isValidationPassed = false;
                                }
                            } else if (validation.matches(">current_date")) {

                                if (comparisonResult >= 0) {
                                    isValidationPassed = false;
                                }
                            } else if (validation.matches("<=current_date")) {

                                if (comparisonResult < 0) {
                                    isValidationPassed = false;
                                }
                            } else if (validation.matches("<current_date")) {

                                if (comparisonResult <= 0) {
                                    isValidationPassed = false;
                                }
                            }

                            if (validation.matches("=current_date") & !isValidationPassed) {
                                Date c = Calendar.getInstance().getTime();
                                SimpleDateFormat df = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
                                String formattedDate = df.format(c);
                                try {
                                    comparisonResult = new Date().compareTo(df.parse(mDateValue));
                                    comparisonResult = Integer.parseInt(getDateDiff(mDateValue, formattedDate));
                                    if (comparisonResult == 0) {
                                        isValidationPassed = true;
                                    } else {
                                        Toast.makeText(mContext, "Please Provide Current Date only", Toast.LENGTH_SHORT).show();
                                    }
                                } catch (ParseException e) {

                                }
                            }

                            if (isValidationPassed) {
                                SetTextViewText(rowID, mDateValue);
                                SetSurveyValue(rowID, mDateValue);
                            } else {
                                if (validation.matches("<=current_date")) {
                                    Utils.showToast(mContext, "Future dates can not be selected");
                                } else {
                                    Toast.makeText(mContext, "Please provide proper date", Toast.LENGTH_SHORT).show();
                                }

                            }

                        } catch (Exception e) {

                        }


                    }
                }, mYear, mMonth, mDay);
        datePickerDialog.setCancelable(true);
        datePickerDialog.show();
    }

    public boolean CheckActionId(String rowID) {
        boolean isAction = false;
        String rowid = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mType = mInputTimeSurveyDetailsList.get(count).getType();
                String action = mInputTimeSurveyDetailsList.get(count).getActionId().trim();
                int j = action.length();
                if (j > 1) {
                    isAction = true;
                    mParentActionValue = mInputTimeSurveyDetailsList.get(count).getAction().trim();
//                    mParentActionValue="masterviewdisplay#Nature of work#facilitator_master#nature_of_work#f_code";
                    break;
                } else {
                    isAction = false;
                    break;
                }
            }
        }
        return isAction;
    }

    public void ShowSubActionLayout(String displayname, final int displayno, final String actiontype, final String submandatory, final int mandatoryno, final String tablename, final String validation) {

        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_sub_action);
        grpDialog.setCancelable(false);

        final LinearLayout mSubParentLayout =  grpDialog.findViewById(R.id.linearLayoutParent);
        final ImageView imageViewBack =  grpDialog.findViewById(R.id.imageViewBack);
        imageViewBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                grpDialog.cancel();
            }
        });

        for (int count = 0; count < displayno; count++) {

            LinearLayout childlayout = new LinearLayout(this);
            String hinttext = "";
            childlayout.setOrientation(LinearLayout.VERTICAL);
            childlayout.setPadding(5, 5, 5, 5);

            if (count < mandatoryno) {
                hinttext = "*" + displayname + " " + String.valueOf(count + 1);
            } else {
                hinttext = displayname + " " + String.valueOf(count + 1);
            }
            childlayout.addView(AddEditText(String.valueOf(count), actiontype, hinttext));
            mSubParentLayout.addView(childlayout);
        }

        Button submit =  grpDialog.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                if (tablename.length() > 0) {
                    grpDialog.cancel();
                } else {
                    if (submandatory.equalsIgnoreCase("Y")) {
                        boolean isValueOK = true;
                        if (mEditTextList.size() > 0) {
                            String val = "";

                            for (int count = 0; count < displayno; count++) {
                                if (isValueOK == true) {
                                    for (EditText editText : mEditTextList) {
                                        String tag = editText.getTag()
                                                .toString();
                                        if (tag.equalsIgnoreCase(String
                                                .valueOf(count))) {
                                            if (count < mandatoryno) {
                                                if (editText.getText().toString().length() > 0) {
                                                    val += editText.getText().toString() + "; ";
                                                } else {
                                                    isValueOK = false;
                                                    Toast.makeText(mContext, "Please provide valid inputs", Toast.LENGTH_SHORT).show();
                                                    break;
                                                }
                                            } else {
                                                if (editText.getText().toString().length() > 0) {
                                                    val += editText.getText().toString() + "; ";
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if (isValueOK == true) {
                                SetSurveyValue(mFinalRowID, val);
                                SetTextViewText(mFinalRowID, val);
                                grpDialog.cancel();
                            }
                        }
                    } else {
                        if (mEditTextList.size() > 0) {
                            String val = "";
                            for (int count = 0; count < displayno; count++) {
                                for (EditText editText : mEditTextList) {
                                    String tag = editText.getTag().toString();
                                    if (tag.equalsIgnoreCase(String.valueOf(count))) {
                                        if (editText.getText().toString().length() > 0) {
                                            val += editText.getText().toString() + "; ";
                                        }
                                    }
                                }
                            }
                            SetSurveyValue(mFinalRowID, val);
                            SetTextViewText(mFinalRowID, val);
                        }
                        grpDialog.cancel();
                    }
                }
            }
        });
        grpDialog.show();
    }

    @SuppressWarnings("deprecation")
    public void ShowSubActionLayoutDynamicView(String displayname, String type, String madatory, String validation, int repeat) {
        int tagcount = 0;

        final List<EditText> editTextList = new ArrayList<EditText>();
        final List<String> mandatoryList = new ArrayList<>();
        final List<String> displaynameList = new ArrayList<>();
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_sub_action);
        grpDialog.setCancelable(false);
        final LinearLayout mSubParentLayout =  grpDialog.findViewById(R.id.linearLayoutParent);
        final ImageView imageViewBack =  grpDialog.findViewById(R.id.imageViewBack);
        imageViewBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                grpDialog.cancel();
            }
        });
        mSubParentLayout.setPadding(3, 0, 3, 0);
        LayoutParams params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        params.setMargins(0, 1, 0, 0);

        LayoutParams paramsl = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        paramsl.setMargins(0, 0, 0, 6);
        for (int count = 0; count < repeat; count++) {
            //XAxis design
            LinearLayout tabchildlayoutx = new LinearLayout(this);
            tabchildlayoutx.setPadding(2, 2, 2, 2);
            tabchildlayoutx.setOrientation(LinearLayout.VERTICAL);
            tabchildlayoutx.setLayoutParams(paramsl);
            tabchildlayoutx.setBackgroundColor(Color.parseColor("#DCE8F6"));

            if (displayname.trim().length() > 0) {
                if (displayname.contains(";")) {
                    String[] madatoryArray = madatory.split("\\;");
                    String[] splitdisplay = displayname.split("\\;");
                    String[] splittype = type.split("\\;");
                    if (splitdisplay.length == splittype.length) {
                        spliton = splitdisplay.length;
                        for (int set = 0; set < splitdisplay.length; set++) {
                            if (CheckColonPresentInType(splittype[set].trim())) {

                                EditText editText = new EditText(this);
                                editText.setTag(tagcount);
                                editTextList.add(editText);
                                displaynameList.add(splitdisplay[set]);
                                mandatoryList.add(madatoryArray[set]);
                                TextView textviewdisplayname = new TextView(this);
                                textviewdisplayname.setText(splitdisplay[set]);
                                textviewdisplayname.setTextColor(Color.BLUE);
                                tabchildlayoutx.addView(textviewdisplayname);

                                final RadioGroup rgp = new RadioGroup(this);
                                rgp.setOrientation(RadioGroup.HORIZONTAL);
                                RadioGroup.LayoutParams rprms;

                                for (int i = 0; i < 2; i++) {
                                    radioButton = new RadioButton(this);
                                    if (i == 0) {
                                        radioButton.setText(boolStringType1);
                                        radioButton.setTag(tagcount);
                                        radioButton.setOnClickListener(new OnClickListener() {
                                            @Override
                                            public void onClick(View view) {
                                                int tag = (int) view.getTag();
                                                RadioButton b = (RadioButton) view;
                                                String buttonText = b.getText().toString();
                                                editTextList.get(tag).setText(buttonText);
                                            }
                                        });

                                    } else {
                                        radioButton.setText(boolStringType2);
                                        radioButton.setTag(tagcount);
                                        radioButton.setOnClickListener(new OnClickListener() {
                                            @Override
                                            public void onClick(View view) {
                                                int tag = (int) view.getTag();
                                                RadioButton b = (RadioButton) view;
                                                String buttonText = b.getText().toString();
                                                editTextList.get(tag).setText(buttonText);
                                            }
                                        });
                                    }
                                    radioButton.setTextColor(Color.BLUE);
                                    rprms = new RadioGroup.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT);
                                    rgp.addView(radioButton, rprms);
                                    rgp.setTag(tagcount);
                                }
                                tabchildlayoutx.addView(rgp);
                                tabchildlayoutx.addView(NewtextView(mFinalRowID));
                            } else {
                                EditText editText = new EditText(this);
                                editText.setTag(tagcount);
                                editText.setLayoutParams(params);
                                if (splittype[set].trim().equalsIgnoreCase("double")) {
                                    editText.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                                } else {
                                    editText.setInputType(InputType.TYPE_TEXT_FLAG_CAP_WORDS
                                            | InputType.TYPE_TEXT_FLAG_MULTI_LINE
                                            | InputType.TYPE_TEXT_FLAG_AUTO_CORRECT);
                                }
                                editText.setSingleLine(false);
                                editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
                                editText.setTextColor(Color.GRAY);
                                editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
                                editText.setTypeface(null, Typeface.NORMAL);
                                editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
                                editText.setHintTextColor(Color.LTGRAY);
                                editText.setHint(splitdisplay[set]);
                                editTextList.add(editText);
                                displaynameList.add(splitdisplay[set]);
                                mandatoryList.add(madatoryArray[set]);
                                tabchildlayoutx.addView(editText);
                            }

                            tagcount++;
                        }
                        mSubParentLayout.addView(tabchildlayoutx);

                    } else {
                        Utils.showToast(mContext, "Error in data Please Synchronize Data");
                    }
                } else {
                    //For single display
                    EditText editText = new EditText(this);
                    editText.setTag(tagcount);
                    editText.setLayoutParams(params);
                    if (type.trim().equalsIgnoreCase("double")) {
                        editText.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                    } else {
                        editText.setInputType(InputType.TYPE_TEXT_FLAG_CAP_WORDS
                                | InputType.TYPE_TEXT_FLAG_MULTI_LINE
                                | InputType.TYPE_TEXT_FLAG_AUTO_CORRECT);
                    }
                    editText.setSingleLine(false);
                    editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
                    editText.setTextColor(Color.GRAY);
                    editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
                    editText.setTypeface(null, Typeface.NORMAL);
                    editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
                    editText.setHintTextColor(Color.LTGRAY);
                    editText.setHint(displayname);
                    editTextList.add(editText);
                    displaynameList.add(displayname);
                    mandatoryList.add(madatory);
                    tabchildlayoutx.addView(editText);
                    mSubParentLayout.addView(tabchildlayoutx);
                    tagcount++;
                }
            } else {
                Utils.showToast(mContext, "Error in data Please Synchronize Data");
            }
        }

        final Button submit =  grpDialog.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                String value = "";
                Boolean AllDataValid = true;
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                if (editTextList.size() > 0) {

                    int count = 1;
                    for (EditText editText : editTextList) {
                        String currentEditTextValue = editText.getText().toString().trim();
                        if (currentEditTextValue.length() <= 0 && mandatoryList.get(count - 1).equalsIgnoreCase("Y")) {
                            if (AllDataValid) {
                                Toast.makeText(mContext, "Please provide input for mandatory field " + displaynameList.get(count - 1), Toast.LENGTH_SHORT).show();
                            }

                            AllDataValid = false;

                        }
                        if (count % spliton == 0) {
                            value += currentEditTextValue + "#";
                        } else {
                            value += currentEditTextValue + ";";
                        }
                        count++;
                    }
                }
                if (AllDataValid) {
                    SetSurveyValue(mFinalRowID, value);
                    SetTextViewText(mFinalRowID, value);

                    DisableDynamicViewButtonEditTextByRowId();
                    grpDialog.cancel();
                } else {
                    value = "";
                }

            }
        });

        grpDialog.show();

    }

    private void DisableDynamicViewButtonEditTextByRowId() {
        mEdiTextDynamic.setEnabled(false);
        for (int i = 0; i < mButtonList.size(); i++) {
            if (mButtonList.get(i).getTag().toString().equalsIgnoreCase(mFinalRowID)) {
                mButtonList.get(i).setEnabled(false);

                break;
            }
        }
    }

    private void makeButtonVisibleOrInvisible(String rowId, int enableOrDisavle) {
        for (int i = 0; i < mButtonList.size(); i++) {
            if (mButtonList.get(i).getTag().toString().equalsIgnoreCase(rowId)) {
                if (enableOrDisavle == 1) {
                    mButtonList.get(i).setVisibility(VISIBLE);
                } else {
                    mButtonList.get(i).setVisibility(View.GONE);
                }

                break;
            }
        }
    }

    public void ShowSubActionLayout(final ArrayList<SurveyInput> surveyInputList, final String rowid, final Boolean ConcatenateValueForCheckBox, final String value) {
        mdatePickerListForSubActionLayout = new ArrayList<>();
        DisplayMetrics metrics = getResources().getDisplayMetrics();

        int DeviceTotalWidth = metrics.widthPixels;
        int DeviceTotalHeight = metrics.heightPixels;


        int countFOrMasterView = 0;
        String typeForMasterView = "";
        grpDialogSubAction = new Dialog(mContext);
        grpDialogSubAction.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialogSubAction.setContentView(R.layout.activty_sub_action);
        grpDialogSubAction.getWindow().setLayout(DeviceTotalWidth * 95 / 100, DeviceTotalHeight);
        grpDialogSubAction.setCancelable(false);
        mKeyValueSubList = new ArrayList<KeyValue>();
        TextView title =  grpDialogSubAction.findViewById(R.id.title);
        final ImageView imageViewBack =  grpDialogSubAction.findViewById(R.id.imageViewBack);
        imageViewBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                grpDialogSubAction.cancel();
            }
        });
        if (actionStringOfCurrentSelectedItem.toLowerCase().contains("#y#")) {
            imageViewBack.setVisibility(View.GONE);
        }
        title.setText(mDisplayName);
        final LinearLayout mSubParentLayout =  grpDialogSubAction.findViewById(R.id.linearLayoutParent);
        for (int count = 0; count < surveyInputList.size(); count++) {
            String displayname = surveyInputList.get(count).getSurveyDisplayName();
            String type = surveyInputList.get(count).getSurveyType();
            String mandatory = surveyInputList.get(count).getSurveyMadatory();
            KeyValue obj = new KeyValue();
            obj.setKey(rowid + count);
            obj.setValue("");
            obj.setType(type);
            mKeyValueSubList.add(obj);
            obj = null;
            LinearLayout childlayout = new LinearLayout(this);
            String hinttext = "";
            childlayout.setOrientation(LinearLayout.VERTICAL);
            childlayout.setPadding(5, 5, 5, 5);
            String tag = String.valueOf(rowid + count) + "$" + value;
            if (type.equalsIgnoreCase("double")) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                if (ConcatenateValueForCheckBox) {
                    for (int i = 0; i < mEditTextList.size(); i++) {
                        String tagOfCurrentItem = String.valueOf(mEditTextList.get(i).getTag());
                        if ((tag).matches(tagOfCurrentItem)) {
                            mEditTextList.remove(i);
                        }

                    }
                    childlayout.addView(AddEditText(tag, type, hinttext));
                } else {
                    childlayout.addView(AddEditText(String.valueOf(rowid + count), type, hinttext));
                }


            } else if (type.equalsIgnoreCase("radio")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(Html.fromHtml(displayname));
                btn.setOnClickListener(new OnClickListener() {
                    public void onClick(View v) {
                        Log.i("TAG", "The index is" + btn.getText());
                    }
                });
                childlayout.addView(btn);
            } else if (type.equalsIgnoreCase("checkbox")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(Html.fromHtml(displayname));
                btn.setOnClickListener(new OnClickListener() {
                    public void onClick(View v) {
                        Log.i("TAG", "The index is" + btn.getText());
                    }
                });
                childlayout.addView(btn);

            } else if (type.equalsIgnoreCase("masterview")) {
                countFOrMasterView = count;
                typeForMasterView = type;
                mSubActionTag = rowid + count;

            } else if (type.equalsIgnoreCase("date")) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
//                if(ConcatenateValueForCheckBox)
//                {
//                    childlayout.addView(AddEditText(tag, type, hinttext));
                childlayout.addView(NewtextViewHeading(tag, displayname, 13f));
                childlayout.addView(AddDatePicker(displayname, String.valueOf(rowid + count)));
//
//
//                }
//                else
//                {
//                    childlayout.addView(AddEditText(String.valueOf(rowid + count), type, hinttext));
//                }

            } else if (type.trim().length() == 0) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                if (ConcatenateValueForCheckBox) {
                    childlayout.addView(AddEditText(tag, type, hinttext));

                } else {
                    childlayout.addView(AddEditText(String.valueOf(rowid + count), type, hinttext));
                }

            }
            mSubParentLayout.addView(childlayout);
        }
        Button submit =  grpDialogSubAction.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                if (ConcatenateValueForCheckBox) {
                    closeSUbActionLayOutCheckedItem(surveyInputList, rowid, grpDialogSubAction, value);
                } else {
                    closeSUbActionLayOut(surveyInputList, rowid, grpDialogSubAction);
                }

            }
        });
        grpDialogSubAction.show();
        if (typeForMasterView.matches("masterview")) {
            Constants.surveyInputListSubActionDialog = surveyInputList;
            Constants.rowidSubActionDialog = rowid;
            if (surveyInputList.size() == 1) {
                closeSubActionDialog = true;
                grpDialogSubAction.getWindow().setLayout(DeviceTotalWidth * 95 / 100, DeviceTotalHeight * 45 / 100);
            } else
                closeSubActionDialog = false;
            //grpDialogSubAction.getWindow().setLayout(600,390);

            onSubActionLayoutButtonClickForMasterView(countFOrMasterView, surveyInputList);
        }

    }

    public void ShowSubActionLayoutCheckedItem(final ArrayList<SurveyInput> surveyInputList, final String rowid, final Boolean ConcatenateValueForCheckBox, final String value) {
        DisplayMetrics metrics = getResources().getDisplayMetrics();
        mdatePickerListForSubActionLayout = new ArrayList<>();
        int DeviceTotalWidth = metrics.widthPixels;
        int DeviceTotalHeight = metrics.heightPixels;


        int countFOrMasterView = 0;
        String typeForMasterView = "";
        final Dialog grpDialogSubAction = new Dialog(mContext, R.style.PauseDialog);
        grpDialogSubAction.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialogSubAction.setContentView(R.layout.activty_sub_action);
        // grpDialogSubAction.getWindow().setLayout(DeviceTotalWidth * 100 / 100, DeviceTotalHeight * 50 / 100);
        grpDialogSubAction.setCancelable(false);
        mKeyValueSubList = new ArrayList<KeyValue>();
        TextView title =  grpDialogSubAction.findViewById(R.id.title);
        final ImageView imageViewBack =  grpDialogSubAction.findViewById(R.id.imageViewBack);
        imageViewBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                grpDialogSubAction.cancel(); // for duro 17jan25
            }
        });
        imageViewBack.setVisibility(GONE);
        title.setText(mDisplayName);
        final LinearLayout mSubParentLayout =  grpDialogSubAction.findViewById(R.id.linearLayoutParent);
        for (int count = 0; count < surveyInputList.size(); count++) {
            String displayname = surveyInputList.get(count).getSurveyDisplayName();
            String type = surveyInputList.get(count).getSurveyType();
            String mandatory = surveyInputList.get(count).getSurveyMadatory();
            KeyValue obj = new KeyValue();
            obj.setKey(rowid + count);
            obj.setValue("");
            obj.setType(type);
            mKeyValueSubList.add(obj);
            obj = null;
            LinearLayout childlayout = new LinearLayout(this);
            String hinttext = "";
            childlayout.setOrientation(LinearLayout.VERTICAL);
            childlayout.setPadding(5, 5, 5, 5);
            String tag = String.valueOf(rowid + count) + "$" + value;
            if (type.equalsIgnoreCase("double")) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                if (ConcatenateValueForCheckBox) {
                    removeRepeatedEditTextFromArrayList(tag);
                    childlayout.addView(AddEditText(tag, type, hinttext));
                } else {
                    childlayout.addView(AddEditText(String.valueOf(rowid + count), type, hinttext));
                }


            } else if (type.equalsIgnoreCase("radio")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(Html.fromHtml(displayname));
                btn.setOnClickListener(new OnClickListener() {
                    public void onClick(View v) {
                        Log.i("TAG", "The index is" + btn.getText());
                    }
                });
                childlayout.addView(btn);
            } else if (type.equalsIgnoreCase("checkbox")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(Html.fromHtml(displayname));
                btn.setOnClickListener(new OnClickListener() {
                    public void onClick(View v) {
                        Log.i("TAG", "The index is" + btn.getText());
                    }
                });
                childlayout.addView(btn);

            } else if (type.equalsIgnoreCase("date")) {
                //ParseandShowAction(tag);
                //childlayout.addView(NewtextView(rowid));
                //AddNewButton(displayname, String.valueOf(rowid));
                childlayout.addView(NewtextViewHeading(tag, displayname, 13f));
                childlayout.addView(AddDatePicker(displayname, String.valueOf(rowid + count)));
//

            } else if (type.equalsIgnoreCase("masterview")) {
                countFOrMasterView = count;
                typeForMasterView = type;
                mSubActionTag = rowid + count;

            } else if (type.trim().length() == 0) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                if (ConcatenateValueForCheckBox) {
                    removeRepeatedEditTextFromArrayList(tag);
                    childlayout.addView(AddEditText(tag, type, hinttext));

                } else {
                    childlayout.addView(AddEditText(String.valueOf(rowid + count), type, hinttext));
                }

            }
            mSubParentLayout.addView(childlayout);
        }
        Button submit =  grpDialogSubAction.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                if (ConcatenateValueForCheckBox) {
                    closeSUbActionLayOutCheckedItem(surveyInputList, rowid, grpDialogSubAction, value);
                } else {
                    closeSUbActionLayOut(surveyInputList, rowid, grpDialogSubAction);
                }


            }
        });
        grpDialogSubAction.show();
        if (typeForMasterView.matches("masterview")) {
            Constants.surveyInputListSubActionDialog = surveyInputList;
            Constants.rowidSubActionDialog = rowid;
            closeSubActionDialog = true;
            //grpDialogSubAction.getWindow().setLayout(600,390);
            grpDialogSubAction.getWindow().setLayout(DeviceTotalWidth * 95 / 100, DeviceTotalHeight * 45 / 100);
            onSubActionLayoutButtonClickForMasterView(countFOrMasterView, surveyInputList);
        }

    }

    public void removeRepeatedEditTextFromArrayList(String tag) {

        List<EditText> foundItems = new ArrayList<>();
        for (EditText currentItem : mEditTextList) {
            String currenttag = currentItem.getTag().toString();
            if (currenttag.equals(tag)) {
                foundItems.add(currentItem);
            }
        }
        mEditTextList.removeAll(foundItems);
    }

    public void closeSUbActionLayOut(ArrayList<SurveyInput> surveyInputList, String rowid, Dialog grpDialog) {
        String val = "";
        mActionPressed += ":";
        boolean isValueOK = true;
        if (mEditTextList.size() > 0) {
            for (int count = 0; count < mKeyValueSubList.size(); count++) {
                for (EditText editText : mEditTextList) {
                    String tag = editText.getTag().toString();
                    if (tag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {
                        mKeyValueSubList.get(count).setValue(editText.getText().toString());
                        mKeyValueSubList.get(count).setEnteredValue(editText.getText().toString());
                    }
                }
            }
        }
        if (mdatePickerListForSubActionLayout.size() > 0) {

            for (int count = 0; count < mKeyValueSubList.size(); count++) {
                for (DatePicker datePicker : mdatePickerListForSubActionLayout) {
                    String tag = datePicker.getTag().toString();
                    if (tag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {

                        int day = datePicker.getDayOfMonth();
                        int month = datePicker.getMonth();
                        int year = datePicker.getYear();
                        Calendar calendar = Calendar.getInstance();
                        calendar.set(year, month, day);

                        SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy");
                        String chosenDate = sdf.format(calendar.getTime());

                        mKeyValueSubList.get(count).setValue(chosenDate);
                        mKeyValueSubList.get(count).setEnteredValue(chosenDate);
                    }
                }
            }

        }

        for (int countx = 0; countx < surveyInputList.size(); countx++) {
            if (surveyInputList.get(countx).getSurveyMadatory().trim().equalsIgnoreCase("Y")) {
                String key = rowid + countx;
                for (int county = 0; county < mKeyValueSubList.size(); county++) {
                    if (key.equalsIgnoreCase(mKeyValueSubList.get(county).getKey())) {
                        if (mKeyValueSubList.get(county).getValue().length() > 0) {

                        } else {
                            isValueOK = false;
                        }
                        break;
                    }
                }
            }
        }
        boolean isDialog = false;
        if (true == isValueOK) {
            for (int count = 0; count < mKeyValueSubList.size(); count++) {
                val += mKeyValueSubList.get(count).getEnteredValue() + "#";
                mActionPressed += mKeyValueSubList.get(count).getValue() + "#";
            }
            if (val.endsWith("#") && mActionPressed.endsWith("#")) {
                val = val.substring(0, val.length() - 1);
                mActionPressed = mActionPressed.substring(0, mActionPressed.length() - 1);
            }
            if (mActionPressed.contains(":")) {
                val = mActionPressed.split(":")[0] + ":" + val;
            }
            SetSurveyValue(mFinalRowID, mActionPressed);
            SetTextViewText(mFinalRowID, val);
            mActionPressed = "";
            isDialog = true;
        }
        if (isDialog) {
            if (grpDialog != null) {
                grpDialog.cancel();
            }
        }
    }

    public void closeSUbActionLayOutCheckedItem(ArrayList<SurveyInput> surveyInputList, String rowid, Dialog grpDialog, final String value) {
        String val = "";
        countFinal = 0;
        String mActionPressed = value;
        mActionPressed += ":";
        boolean isValueOK = true;
        if (mEditTextList.size() > 0) {
            mKeyValueSubList = new ArrayList<>();
            for (int count = 0; count < surveyInputList.size(); count++) {
                String displayname = surveyInputList.get(count).getSurveyDisplayName();
                String type = surveyInputList.get(count).getSurveyType();
                String mandatory = surveyInputList.get(count).getSurveyMadatory();
                String validations = surveyInputList.get(count).getSurveyValidation();
                KeyValue obj = new KeyValue();
                obj.setKey(rowid + count);
                obj.setValue("");
                obj.setType(type);
                obj.setdisplayName(displayname);
                obj.setValidation(validations);
                mKeyValueSubList.add(obj);
                obj = null;
            }
            for (int count = 0; count < mKeyValueSubList.size(); count++) {
                for (EditText editText : mEditTextList) {
                    String tag = editText.getTag().toString();
                    if (tag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey() + "$" + value)) {
                        mKeyValueSubList.get(count).setValue(editText.getText().toString());
                        mKeyValueSubList.get(count).setEnteredValue(editText.getText().toString());
                        countFinal = countFinal + 1;
                    }
                }
            }
        }
        for (int countx = 0; countx < surveyInputList.size(); countx++) {
            if (!isValueOK)
                break;
            if (surveyInputList.get(countx).getSurveyMadatory().trim().equalsIgnoreCase("Y")) {
                String key = rowid + countx;
                for (int county = 0; county < mKeyValueSubList.size(); county++) {
                    if (key.equalsIgnoreCase(mKeyValueSubList.get(county).getKey())) {
                        if (mKeyValueSubList.get(county).getValue().length() > 0) {

                            if (mKeyValueSubList.get(county).getValidation().length() > 0 && Utils.isNumeric(mKeyValueSubList.get(county).getValidation())) {
                                if (Double.parseDouble(mKeyValueSubList.get(county).getValidation()) < Double.parseDouble(mKeyValueSubList.get(county).getValue())) {

                                } else {
                                    isValueOK = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + mKeyValueSubList.get(county).getdisplayName(), Toast.LENGTH_SHORT).show();
                                    break;
                                }
                            }

                        } else {
                            isValueOK = false;
                            Toast.makeText(mContext, "Please provide mandatory input in " + mKeyValueSubList.get(county).getdisplayName(), Toast.LENGTH_SHORT).show();
                            break;
                        }

                    }
                }
            }
        }
        if (mdatePickerListForSubActionLayout.size() > 0) {

            for (int count = 0; count < mKeyValueSubList.size(); count++) {
                for (DatePicker datePicker : mdatePickerListForSubActionLayout) {
                    String tag = datePicker.getTag().toString();
                    if (tag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {

                        int day = datePicker.getDayOfMonth();
                        int month = datePicker.getMonth();
                        int year = datePicker.getYear();
                        Calendar calendar = Calendar.getInstance();
                        calendar.set(year, month, day);

                        SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy");
                        String chosenDate = sdf.format(calendar.getTime());

                        mKeyValueSubList.get(count).setValue(chosenDate);
                        mKeyValueSubList.get(count).setEnteredValue(chosenDate);
                        countFinal = countFinal + 1;
                    }
                }
            }

        }
        boolean isDialog = false;
        if (isValueOK) {
            try {
                for (int count = 0; count < countFinal; count++) {
                    val += mKeyValueSubList.get(count).getEnteredValue() + "#";
                    mActionPressed += mKeyValueSubList.get(count).getValue() + "#";
                }
            } catch (Exception e) {
                Log.d("err-5897-", e.toString());
                // throw new RuntimeException(e);
            }

            if (val.endsWith("#") && mActionPressed.endsWith("#")) {
                val = val.substring(0, val.length() - 1);
                mActionPressed = mActionPressed.substring(0, mActionPressed.length() - 1);
            }
            if (!mActionPressedExistingValue.equalsIgnoreCase("")) {
                mActionPressedExistingValue = mActionPressedExistingValue + "$" + mActionPressed;
            } else {
                mActionPressedExistingValue = mActionPressed;
            }
            SetSurveyValue(mFinalRowID, mActionPressedExistingValue);
            SetTextViewText(mFinalRowID, mActionPressedExistingValue);
            mActionPressed = "";
            isDialog = true;

        }
        if (isDialog) {
            try {
                if (grpDialog != null) {
                    grpDialog.cancel();
                }
            } catch (Exception e) {
                //throw new RuntimeException(e);
                Log.d("err-5917", e.toString());
            }
        }

    }

    private void onSubActionLayoutButtonClickForMasterView(int count, ArrayList<SurveyInput> surveyInputList) {
        String tablenamedetails = surveyInputList.get(count).getSurveyTableName();
        String clause = surveyInputList.get(count).getSurveyClause().trim();
        String rowIdDependencyClause = surveyInputList.get(count).getRow_id_dependency_clause().trim();
        if (tablenamedetails.contains("%")) {
            String[] splitablename = tablenamedetails.split("\\%");
            mShowColumnCount = splitablename.length;
            if (mShowColumnCount >= 3) {
                mTableName = splitablename[0];
                mSendColumn = splitablename[1];
                mShowColumn = splitablename[2];
                if (mShowColumnCount == 4) {
                    mShowColumn1 = splitablename[3];
                }
                if (mShowColumnCount == 5) {
                    mShowColumn1 = splitablename[3];
                    mShowColumn2 = splitablename[4];
                }

                mDecision = "FORWARD";
                if (clause.length() > 0) {
                    if (clause.contains("&&")) {
                        String[] noofclause = clause.split("\\&&");
                        for (int clausecount = 0; clausecount < noofclause.length; clausecount++) {
                            String[] splitwhere = noofclause[clausecount].split("\\;");
                            if (splitwhere[1].trim().equalsIgnoreCase("routeplan")) {
                                mWhereClause += splitwhere[0] + "='" + Constants.mSurveyRouteCode + "' ";
                            } else {
                                mWhereClause += splitwhere[0] + "='" + splitwhere[1] + "' ";
                            }
                            if (clausecount != noofclause.length - 1) {
                                mWhereClause += "AND ";
                            }
                        }
                        PrepareSurveyData(9, "");
                    } else {
                        String[] splitclause = clause.split("\\;");
                        if (splitclause[1].contains("!=")) {
                            mWhereClause = splitclause[0] + "!='" + splitclause[1].replace("!=", "") + "'";
                        } else {
                            mWhereClause = splitclause[0] + " like '%" + splitclause[1] + "%'";
                        }
                        //row_id-RA228
                        if (splitclause.length > 2) {
                            if (splitclause[3].contains("row_id-")) {
                                String[] splitclauser = clause.split("-");
                                String[] mCustomerSelectionBasisFilterArray = splitclause[2].split("\\;");
                                String dependingOnRowId = splitclause[3].split("-")[1];
                                if (CheckGivenRowIdHasValue(dependingOnRowId)) {

                                    if (mCheckValue.equalsIgnoreCase("NEW")) {
                                        //this.mCustomerSelectionBasisFilter ="";
                                        mWhereClause = mWhereClause + " AND ( " + mCustomerSelectionBasisFilterArray[0] + "='' OR " + mCustomerSelectionBasisFilterArray[0] + "=' ' OR " + mCustomerSelectionBasisFilterArray[0] + " is NULL ) ";
                                    } else {
                                        mWhereClause = mWhereClause + " AND " + mCustomerSelectionBasisFilterArray[0] + "='" + mCheckValue + "' ";
                                    }
                                } else {

                                    mWhereClause = splitclause[0] + "='" + splitclause[1] + "'";
                                }
                            }
                        }
                        PrepareSurveyData(9, "");
                    }
                } else if (rowIdDependencyClause.contains("row_id")) {
                    //branch_code;row_id-RA165
                    if (rowIdDependencyClause.contains(";")) {
                        String[] splittedRowIdDependencyClause = rowIdDependencyClause.split(";");
                        String whereColumn = splittedRowIdDependencyClause[0];//where column
                        String splittedRowIdDependencyClauseItemOne = splittedRowIdDependencyClause[1];
                        String rowId = splittedRowIdDependencyClauseItemOne.split("-")[1];
                        String value = getValueByRowId(rowId);
                        if (!value.matches("")) {
                            String finalWhereCondition = " where " + whereColumn + "='" + value + "'";
                            mCustomerSelectionBasisFilter = finalWhereCondition;
                        }

                    }
                    PrepareSurveyData(7, "");

                } else {
                    PrepareSurveyData(7, "");
                }
            } else {
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        } else {
            Utils.showToast(mContext, "No data found.Please Synchronize Data");
        }
    }

    public void ChangeVisibility(String rowID, String checkedvalue) {
        String rowid = "";
        String validationText = "";

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            validationText = mInputTimeSurveyDetailsList.get(count)
                    .getValidation().trim();
            if (validationText.length() > 0) {
                if (validationText.startsWith("RA") && validationText.contains("#")) {
                    String[] splitvalidation = validationText.split("\\#");
                    if (splitvalidation[0].equalsIgnoreCase(rowID) && splitvalidation[1].equals(checkedvalue)) {
                        for (int i = 0; i < mButtonList.size(); i++) {
                            if (mButtonList.get(i).getTag().toString().equalsIgnoreCase(rowid)) {
                                mButtonList.get(i).setVisibility(VISIBLE);
                                break;
                            }
                        }
                    } else {
                        for (int i = 0; i < mButtonList.size(); i++) {
                            if (mButtonList.get(i).getTag().toString().equalsIgnoreCase(rowid)) {
                                mButtonList.get(i).setVisibility(View.INVISIBLE);
                                SetSurveyValue(rowid, "00:00");
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    public void ShowActionLayout(String displayname, final String actiontype, final String mandatory, final String tablename, final String validation) {

        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_ation);
        grpDialog.setCancelable(false);

        TextView title =  grpDialog.findViewById(R.id.textView1);
        title.setText(Html.fromHtml(displayname));
        final ImageView imageViewBack =  grpDialog.findViewById(R.id.imageViewBack);
        imageViewBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                grpDialog.cancel();
            }
        });
        final EditText mEditText =  grpDialog.findViewById(R.id.editText1);
        if (actiontype.equalsIgnoreCase("double")) {
            mEditText.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        }
        Button mSelectButton =  grpDialog.findViewById(R.id.button2);
        mSelectButton.setText("Select " + displayname);

        if (tablename.length() > 0) {
            mEditText.setVisibility(View.GONE);
        } else {
            mSelectButton.setVisibility(View.GONE);
        }

        mSelectButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mType = actiontype;
                PrepareSurveyData(2, tablename);
            }
        });

        Button submit =  grpDialog.findViewById(R.id.button1);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {

                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

                if (tablename.length() > 0) {
                    grpDialog.cancel();
                } else {
                    if (mandatory.equalsIgnoreCase("Y")) {
                        String editval = mEditText.getText().toString();
                        if (ContainsOnlyNumbers(validation) == true) {
                            if (editval.length() == Integer.valueOf(validation)) {
                                SetSurveyValue(mFinalRowID, editval);
                                SetTextViewText(mFinalRowID, editval);
                                grpDialog.cancel();
                            } else {
                                Toast.makeText(mContext, "Please provide valid Input", Toast.LENGTH_SHORT).show();
                            }
                        } else if (editval.length() > 0) {
                            SetSurveyValue(mFinalRowID, editval);
                            SetTextViewText(mFinalRowID, editval);
                            grpDialog.cancel();
                        } else {
                            Toast.makeText(mContext, "Please provide valid Input", Toast.LENGTH_SHORT).show();
                        }
                    } else {
                        SetSurveyValue(mFinalRowID, mEditText.getText().toString());
                        SetTextViewText(mFinalRowID, mEditText.getText().toString());
                        grpDialog.cancel();
                    }
                }
            }
        });
        grpDialog.show();
    }

    private void SetTextViewText(String tag, String text) {
        for (int count = 0; count < mTextViewList.size(); count++) {
            if (mTextViewList.get(count).getTag().toString().equalsIgnoreCase(tag)) {
                mTextViewList.get(count).setText(text);
                mTextViewList.get(count).setTextColor(Color.parseColor("#FF9600"));
                mTextViewList.get(count).setTypeface(null, Typeface.BOLD);
                break;
            }
        }
    }

    private void SetEditTextTextAndDisableIfNeeded(String tag, String text, Boolean disable) {
        for (int count = 0; count < mEditTextList.size(); count++) {
            if (mEditTextList.get(count).getTag().toString().equalsIgnoreCase(tag)) {
                mEditTextList.get(count).setText(text);
                if (disable) {
                    mEditTextList.get(count).setEnabled(false);
                }

                break;
            }
        }
    }

    private void disableButton(String tag) {
        for (int count = 0; count < mButtonList.size(); count++) {
            if (mButtonList.get(count).getTag().toString().equalsIgnoreCase(tag)) {
                mButtonList.get(count).setEnabled(false);
                break;
            }
        }
    }

    @SuppressWarnings("deprecation")
    private EditText AddEditText(String tag, String edtype, String hinttext) {
        EditText editText = new EditText(this);
        editText.setTag(tag);
        LayoutParams params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        editText.setLayoutParams(params);
        if (edtype.equalsIgnoreCase("double")) {
            editText.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        } else {
            editText.setInputType(InputType.TYPE_CLASS_TEXT
                    | InputType.TYPE_TEXT_FLAG_MULTI_LINE
                    | InputType.TYPE_TEXT_FLAG_AUTO_CORRECT);
        }
        editText.setSingleLine(false);
        editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        editText.setTextColor(Color.GRAY);
        editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
        editText.setTypeface(null, Typeface.NORMAL);

        if (mPredefinedValue.trim().length() > 0) {
            editText.setText(mPredefinedValue);
            editText.setEnabled(false);
            mPredefinedValue = "";
        } else {
            editText.setHint(Html.fromHtml(hinttext));
        }

        editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
        editText.setHintTextColor(Color.LTGRAY);
        editText.addTextChangedListener(new GenericTextWatcher(editText.getTag().toString()));
        mEditTextList.add(editText);
        return editText;
    }

    private EditText AddEditTextWithTextChangeListenerForFloor(String tag, String validation, String edtype, String hinttext) {
        EditText editText = new EditText(this);
        editText.setTag(tag);
        LayoutParams params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        editText.setLayoutParams(params);
        if (edtype.equalsIgnoreCase("double")) {
            editText.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        } else {
            editText.setInputType(InputType.TYPE_CLASS_TEXT
                    | InputType.TYPE_TEXT_FLAG_MULTI_LINE
                    | InputType.TYPE_TEXT_FLAG_AUTO_CORRECT);

        }
        editText.setSingleLine(false);
        editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        editText.setTextColor(Color.GRAY);
        editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
        editText.setTypeface(null, Typeface.NORMAL);
        if (mPredefinedValue.trim().length() > 0) {

            //write down the code for otp
            editText.setText(mPredefinedValue);
            editText.setEnabled(false);
            mPredefinedValue = "";
        } else {
            editText.setHint(Html.fromHtml(hinttext));
        }

        editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
        editText.setHintTextColor(Color.LTGRAY);
        editText.addTextChangedListener(new GenericTextWatcherFloor(editText.getTag().toString(), validation));
        mEditTextList.add(editText);
        return editText;
    }

    private class GenericTextWatcher implements TextWatcher {

        private String tag;

        private GenericTextWatcher(String tag) {
            this.tag = tag;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            SetSurveyValue(tag, text);
        }
    }

    private class GenericTextWatcherFloor implements TextWatcher {

        private String tag, validation;

        private GenericTextWatcherFloor(String tag, String validation) {
            this.tag = tag;
            this.validation = validation;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            if (text.length() == 0) {
                SetSurveyValue(tag, text);
                SetSurveyValue(validation, text);
                SetTextViewText(validation, text);
            } else {
                SetSurveyValue(tag, text);
//            text=String.format("%.1f", text);
                if (text.contains(".") && !text.endsWith(".")) {

                    String[] arrayOfText = text.split("\\.");
                    String textBeforeDecimal = arrayOfText[0];
                    String textAfterDecimal = arrayOfText[1];
                    if (textAfterDecimal.length() > 1) {
                        String firstCharacterAfterDecimal = String.valueOf(textAfterDecimal.charAt(0));
                        String FirstCharacterAfterPoint = firstCharacterAfterDecimal;
                        text = textBeforeDecimal + "." + FirstCharacterAfterPoint + "0";
                    } else {
                        text = text + "0";
                    }

                }
                SetSurveyValue(validation, text);
                SetTextViewText(validation, text);
            }
        }
    }

    private TextView NewtextView(String tag) {
        LayoutParams Params = new LayoutParams(
                LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 1);
        TextView textView = new TextView(this);
        textView.setLayoutParams(Params);
        textView.setTag(tag);
        mTextViewList.add(textView);
        return textView;
    }

    private TextView NewtextViewHeading(String tag, String displayname, float size) {
        LayoutParams Params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 1);
//        Params.setMargins(left, top, right, bottom);
        Params.setMargins(0, 5, 0, 0);
        TextView textView = new TextView(this);
        textView.setLayoutParams(Params);
        textView.setTextSize(size);
        textView.setTextColor(getResources().getColor(R.color.black));
        textView.setText(Html.fromHtml(displayname));
        textView.setTag(tag);
        mTextViewList.add(textView);
        return textView;
    }

    public boolean CheckSurveyValidation(String rowID, String value) {
        String rowid,validationText,displayname;
        boolean isSucess = true;
        boolean isvalue = false;

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            if (!isvalue) {
                rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
                validationText = mInputTimeSurveyDetailsList.get(count).getValidation().trim();
                displayname = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                if (!validationText.isEmpty()) {
                    if (rowID.equalsIgnoreCase(rowid)) {
                        if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("Y")) {
                            if (ContainsOnlyNumbers(validationText)) {
                                if (Integer.parseInt(validationText) == value.length()) {
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                    break;
                                }
                            } else if (validationText.matches("mobile")) {
                                if (value.length() == 10) {
                                    isSucess = true;
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                }

                            } else if (validationText.contains("mobile!=duplicate")) {
                                if (value.length() == 10) {
//                                        isSucess = true;
                                    isSucess = mobileDuplicateValidation(validationText, value);
                                    if (!isSucess) {
                                        Utils.showToast(mContext, "Mobile number Already Exist " + "" + "");
                                        break;
                                    }

                                } else {
                                    isSucess = false;
                                    Utils.showToast(mContext, "Please provide valid input in " + displayname + "");
                                    break;
                                }
                            } else if (validationText.matches("email")) {
                                if (Utils.isValidMail(value)) {
                                    isSucess = true;
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                    break;
                                }
                            } else if (validationText.matches("alphanumeric")) {
                                if (Character.isAlphabetic(value.charAt(0))) {
                                    isSucess = true;
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "In " + displayname + ", first character must be alphabetic", Toast.LENGTH_SHORT).show();
                                    break;
                                }
                            } else {
                                if (validationText.startsWith("RA")) {
                                    if (validationText.contains("#")) {
                                        String[] validationsplit = validationText.split("\\#");
                                        if (true == CheckSurveyDependentOnOtherInputValue(validationsplit[0])) {
                                            if (validationsplit[1].equalsIgnoreCase(">=")) {
                                                if (mCheckValue.length() > 0) {
                                                    if (value.length() > 0) {
                                                        if (Double.valueOf(mCheckValue) >= Double.valueOf(value)) {
                                                            isSucess = true;
                                                        } else {
                                                            isSucess = false;
                                                            Toast.makeText(mContext, mDependentDisplayName + " value should be greater or equal than " + displayname, Toast.LENGTH_SHORT).show();
                                                            break;
                                                        }
                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                        break;
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, mDependentDisplayName + " value should be greater or equal than " + displayname, Toast.LENGTH_SHORT).show();
                                                    break;
                                                }
                                            } else if (validationsplit[1].equalsIgnoreCase("<=")) {
                                                if (mCheckValue.length() > 0) {
                                                    if (value.length() > 0) {
                                                        if (Double.valueOf(mCheckValue) <= Double.valueOf(value)) {
                                                            isSucess = true;
                                                        } else {
                                                            isSucess = false;
                                                            Toast.makeText(mContext, mDependentDisplayName + " value should be less or equal than " + displayname, Toast.LENGTH_SHORT).show();
                                                            break;
                                                        }
                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                        break;
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, mDependentDisplayName + " value should be less or equal than " + displayname, Toast.LENGTH_SHORT).show();
                                                    break;
                                                }

                                            } else if (validationsplit[1].equalsIgnoreCase("=")) {
                                                if (mCheckValue.length() > 0) {
                                                    if (value.length() > 0) {
                                                        if (Integer.valueOf(mCheckValue) == Integer.valueOf(value)) {
                                                            isSucess = true;
                                                        } else {
                                                            isSucess = false;
                                                            Toast.makeText(mContext, displayname + " value should be equal to " + mDependentDisplayName, Toast.LENGTH_SHORT).show();
                                                            break;
                                                        }
                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                        break;
                                                    }

                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, displayname + " value should be equal to " + mDependentDisplayName, Toast.LENGTH_SHORT).show();
                                                    break;
                                                }


                                            } else if (validationsplit[1].equalsIgnoreCase("<")) {
                                                if (mCheckValue.length() > 0) {
                                                    if (value.length() > 0) {
                                                        if (Double.valueOf(mCheckValue) < Double.valueOf(value)) {
                                                            isSucess = true;
                                                        } else {
                                                            isSucess = false;
                                                            Toast.makeText(mContext, mDependentDisplayName + " value should be less than " + displayname, Toast.LENGTH_SHORT).show();
                                                            break;
                                                        }

                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                        break;
                                                    }

                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, mDependentDisplayName + " value should be less than " + displayname, Toast.LENGTH_SHORT).show();
                                                    break;
                                                }

                                            } else if (validationsplit[1].equalsIgnoreCase(">")) {
                                                if (mCheckValue.length() > 0) {
                                                    if (value.length() > 0) {
                                                        if (Double.valueOf(mCheckValue) > Double.valueOf(value)) {
                                                            isSucess = true;
                                                        } else {
                                                            isSucess = false;
                                                            Toast.makeText(mContext, mDependentDisplayName + " value should be greater than " + displayname, Toast.LENGTH_SHORT).show();
                                                            break;
                                                        }

                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                        break;
                                                    }

                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, mDependentDisplayName + " value should be greater than " + displayname, Toast.LENGTH_SHORT).show();
                                                    break;
                                                }

                                            } else {
                                                if (value.length() > 0) {
                                                    isSucess = true;
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                    break;
                                                }
                                            }
                                        } else {
                                            isSucess = true;
                                        }
                                    } else {
                                        if (true == CheckSurveyDependentOnOtherInputValue(validationText)) {
                                            if (value.length() > 0) {
                                                isSucess = true;
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                break;
                                            }
                                        }
                                    }
                                } else if (validationText.startsWith("Round")) {
                                    if (validationText.contains("#")) {

                                        String[] validation = validationText.split("\\#");
                                        if (validation[1].trim().contains(";")) {
                                            String[] roundsplit = validation[1].trim().split("\\;");
                                            if (value.trim().length() > 0) {
                                                if (value.contains(".")) {
                                                    String[] splitvalue = value.split("\\.");
                                                    if (splitvalue[0].trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                        isSucess = true;
                                                        isRound = true;
                                                        double val = Double.valueOf(value);
                                                        mRoundValue = defaultFormat.format(val);

                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                        break;
                                                    }
                                                } else {
                                                    if (value.trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                        isSucess = true;
                                                        isRound = true;
                                                        double val = Double.valueOf(value);
                                                        mRoundValue = defaultFormat.format(val);
                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                        break;
                                                    }
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                break;
                                            }
                                        }
                                    }
                                } else if (validationText.contains("greater")) {
                                    String[] items = validationText.split("<");
                                    if (Double.parseDouble(items[1]) < Double.parseDouble(value)) {
                                        isSucess = true;
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                        break;
                                    }
                                } else {
                                    if (validationText.contains(".")) {
                                        String[] items = value.split(".");
                                        if (items.length >= 2) {
                                            isSucess = true;
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                            break;
                                        }
                                    } else if (validationText.contains("@")) {
                                        String[] items = value.split("@");
                                        if (items.length >= 2) {
                                            int lengths = items.length;
                                            String[] dotitems = items[lengths - 1].split("\\.");
                                            if (dotitems.length >= 2) {
                                                if (dotitems[0].trim().length() > 0) {
                                                    isSucess = true;
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                    break;
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                break;
                                            }
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                            break;
                                        }
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                        break;
                                    }
                                }
                            }
                        } else {
                            if (value.length() > 0) {
                                if (ContainsOnlyNumbers(validationText) == true) {
                                    if (Integer.valueOf(validationText) == value
                                            .length()) {
                                        isSucess = true;
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                        break;
                                    }
                                } else if (validationText.matches("mobile")) {
                                    if (value.length() == 10) {
                                        isSucess = true;
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                        break;
                                    }
                                } else if (validationText.contains("mobile!=duplicate")) {
                                    if (value.length() == 10) {
//                                        isSucess = true;
                                        isSucess = mobileDuplicateValidation(validationText, value);
                                        if (!isSucess) {
                                            Utils.showToast(mContext, "Please provide valid input in " + displayname + "");
                                            break;
                                        }

                                    } else {
                                        isSucess = false;
                                        Utils.showToast(mContext, "Please provide valid input in " + displayname + "");
                                        break;
                                    }
                                } else if (validationText.matches("email")) {
                                    if (Utils.isValidMail(value)) {
                                        isSucess = true;
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                        break;
                                    }
                                } else if (validationText.matches("alphanumeric")) {
                                    if (Character.isAlphabetic(value.charAt(0))) {
                                        isSucess = true;
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "In " + displayname + ", first character must be alphabetic", Toast.LENGTH_SHORT).show();
                                        break;
                                    }
                                } else {
                                    if (validationText.startsWith("RA")) {
                                        if (validationText.contains(":")) {
                                            String[] validationsplit = validationText.split("\\:");
                                            if (true == CheckSurveyDependentOnOtherInputValue(validationsplit[0])) {
                                                if (value.length() >= Integer.parseInt(validationsplit[1])) {
                                                    isSucess = true;
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                    break;
                                                }
                                            } else {
                                                isSucess = true;
                                            }
                                        } else {
                                            if (true == CheckSurveyDependentOnOtherInputValue(validationText)) {
                                                if (value.length() > 0) {
                                                    isSucess = true;
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                    break;
                                                }
                                            } else {
                                                isSucess = true;
                                            }
                                        }
                                    } else if (validationText.startsWith("Round")) {
                                        if (validationText.contains("#")) {
                                            String[] validation = validationText.split("\\#");
                                            if (validation[1].trim().contains(";")) {
                                                String[] roundsplit = validation[1].trim().split("\\;");
                                                if (value.trim().length() > 0) {
                                                    if (value.contains(".")) {
                                                        String[] splitvalue = value.split("\\.");
                                                        if (splitvalue[0].trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                            isSucess = true;
                                                            isRound = true;
                                                            double val = Double.valueOf(value);
                                                            mRoundValue = defaultFormat.format(val);

                                                        } else {
                                                            isSucess = false;
                                                            Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                            break;
                                                        }
                                                    } else {
                                                        if (value.trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                            isSucess = true;
                                                            isRound = true;
                                                            double val = Double.valueOf(value);
                                                            mRoundValue = defaultFormat.format(val);
                                                        } else {
                                                            isSucess = false;
                                                            Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                            break;
                                                        }
                                                    }
                                                } else {
                                                    isSucess = true;
                                                    //Toast.makeText(mContext,"Please provide valid input in "+ displayname + "",Toast.LENGTH_SHORT).show();
                                                }
                                            }
                                        }
                                    } else {
                                        if (validationText.contains(".")) {
                                            String[] items = value.split("\\.");
                                            if (items.length >= 2) {
                                                isSucess = true;
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                break;
                                            }
                                        } else if (validationText.contains("@")) {
                                            String[] items = value.split("@");
                                            if (items.length >= 2) {
                                                int lengths = items.length;
                                                String[] dotitems = items[lengths - 1].split("\\.");
                                                if (dotitems.length >= 2) {
                                                    if (dotitems[0].trim().length() > 0) {
                                                        isSucess = true;
                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                        break;
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                    break;
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                break;
                                            }
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                            break;
                                        }
                                    }

                                }
                            } else {
                                if (validationText.startsWith("RA")) {

                                    if (validationText.contains(":")) {
                                        String[] validationsplit = validationText.split("\\:");
                                        if (true == CheckSurveyDependentOnOtherInputValue(validationsplit[0])) {
                                            if (value.length() >= Integer.parseInt(validationsplit[1])) {
                                                isSucess = true;
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                break;
                                            }
                                        } else {
                                            isSucess = true;
                                        }
                                    } else {
                                        if (true == CheckSurveyDependentOnOtherInputValue(validationText)) {
                                            if (value.length() > 0) {
                                                isSucess = true;
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname + "", Toast.LENGTH_SHORT).show();
                                                break;
                                            }
                                        } else {
                                            isSucess = true;
                                        }
                                    }
                                }
                            }
                        }
                        isvalue = true;
                        break;
                    }
                }
            }
        }
        return isSucess;
    }

    public boolean mobileDuplicateValidation(String validationText, String value) {
//        validationText="mobile!=duplicate-site_lead_conversion_master-customer_contact_no";
        boolean isSuccess = true;
        String query = "";
        // mobile!=duplicate FOR new and mobile!=duplicate#row_id-Ra0000 for existing
        if (validationText.contains("#")) {//existing....check database for any value except current customer
            String rowId = validationText.split("#")[1].split("-")[1]; //mobile!=duplicate#row_id-Ra0000
            String customerName = getDisplayNameByRowId(rowId);
            query = "Select count(customer_name) from customer_master where customer_name!='" + customerName + "' AND phone_no='" + value + "'";

        }
        //new customer
        else {//new- if any customer with current mobile number exists then return false
            if (validationText.contains("-")) {//mobile!=duplicate*site_lead_conversion_master*customer_contact_no
                String[] validationTextSplitted = validationText.split("-");
                String tableName = validationTextSplitted[1];
                String columnName = validationTextSplitted[2];
                query = "Select count(customer_name) from " + tableName + " where  " + columnName + "='" + value + "'";
            } else {
                query = "Select count(customer_name) from customer_master where  phone_no='" + value + "'";
            }

        }
        int countFromDB = Integer.parseInt(mAceDnsDatabase.GetSurveyValueFromQuery(query));
        if (countFromDB > 0) {
            isSuccess = false;
        }
        return isSuccess;
    }

    public boolean ContainsOnlyNumbers(String str) {
        if (!str.trim().isEmpty()) {
            for (int i = 0; i < str.length(); i++) {
                if (!Character.isDigit(str.charAt(i)))
                    return false;
            }
            return true;
        } else {
            return false;
        }
    }

    @SuppressLint("SimpleDateFormat")
    public boolean CheckSurveyMandatory() {
        String value = "";
        String displayname = "";
        boolean isSucess = true;
        boolean isvalue = false;

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            String validation = mInputTimeSurveyDetailsList.get(count).getValidation();
            value = mInputTimeSurveyDetailsList.get(count).getValue();
            displayname = mInputTimeSurveyDetailsList.get(count).getDisplayName();
            if (!isvalue) {

                if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("Y")) {
                    if (value.isEmpty()) {
                        Toast.makeText(mContext, "Please provide mandatory input in " + displayname, Toast.LENGTH_SHORT).show();
                        isSucess = false;
                        break;
                    }
                } else if (validation.toLowerCase().contains("mandatory#y")){
                    //>current_date&mandatory#Y#RA090#Follow up;High priority;Hold
                    if (validation.contains("&")) {
                        String[] SPlittedCalidationString = validation.split("&");
                        validation = SPlittedCalidationString[1];
                        if (SPlittedCalidationString[1].equalsIgnoreCase(">current_date")) {
                            if (value.matches("") || !value.contains("/")) {
                                value = Utils.getTomorrowsDate("dd/MM/yyyy");
                                mInputTimeSurveyDetailsList.get(count).setValue(value);
                                if (validation.contains("popup"))
                                    Toast.makeText(mContext, "Please not," + displayname + " has been automatically updated to next date. ", Toast.LENGTH_LONG).show();
                            } else {
                                try {
                                    SimpleDateFormat inputFormatForChosenDate = new SimpleDateFormat("dd/MM/yyyy");
                                    Date dateInputByUser = inputFormatForChosenDate.parse(value);
                                    assert dateInputByUser != null;
                                    if (!dateInputByUser.after(new Date())) {
                                        value = Utils.getTomorrowsDate("dd/MM/yyyy");
                                        mInputTimeSurveyDetailsList.get(count).setValue(value);
                                        if (validation.contains("popup"))
                                            Toast.makeText(mContext, "Please not," + displayname + " has been automatically updated to next date. ", Toast.LENGTH_LONG).show();
                                    }
                                } catch (Exception e) {
                                    value = Utils.getTomorrowsDate("dd/MM/yyyy");
                                    mInputTimeSurveyDetailsList.get(count).setValue(value);
                                    if (validation.contains("popup"))
                                        Toast.makeText(mContext, "Please not," + displayname + " has been automatically updated to next date. ", Toast.LENGTH_LONG).show();
                                }
                            }
                        }

                    }
                    String[] validationTypeSplitted = validation.split("#");
                    if (validationTypeSplitted.length == 4) {
                        String RowIdToCheckValue = validationTypeSplitted[2];
                        String ValuesToBeChecked = validationTypeSplitted[3].toLowerCase();
                        ArrayList<String> valuesArrayList = new ArrayList<>();
                        if (ValuesToBeChecked.contains(";")) {
                            String[] valuesArr = ValuesToBeChecked.split(";");
                            valuesArrayList = new ArrayList<>(Arrays.asList(valuesArr));
                        } else {
                            valuesArrayList.add(ValuesToBeChecked);
                        }
                        Boolean isValueGivenValuesSelectedForRowID = isAnyOfGivenValuesSelectedForRowID(RowIdToCheckValue, valuesArrayList);
                        if (isValueGivenValuesSelectedForRowID) {
                            if (value.trim().isEmpty() || value.matches(" ")) {
                                Toast.makeText(mContext, "Please provide mandatory input in " + displayname, Toast.LENGTH_SHORT).show();
                                isSucess = false;
                                break;
                            }
                        }
                    }

                } else if (validation.toLowerCase().contains(">current_date&popup"))//>current_date&popup
                {
                    if (value.matches("") || !value.contains("/")) {
                        value = Utils.getTomorrowsDate("dd/MM/yyyy");
                        mInputTimeSurveyDetailsList.get(count).setValue(value);
                        Toast.makeText(mContext, "Please not," + displayname + " has been automatically updated to next date. ", Toast.LENGTH_LONG).show();
                    } else {
                        try {
                             SimpleDateFormat inputFormatForChosenDate = new SimpleDateFormat("dd/MM/yyyy");
                            Date dateInputByUser = inputFormatForChosenDate.parse(value);
                            assert dateInputByUser != null;
                            if (!dateInputByUser.after(new Date())) {
                                value = Utils.getTomorrowsDate("dd/MM/yyyy");
                                mInputTimeSurveyDetailsList.get(count).setValue(value);
                                Toast.makeText(mContext, "Please not," + displayname + " has been automatically updated to next date. ", Toast.LENGTH_LONG).show();
                            }
                        } catch (Exception e) {
                            value = Utils.getTomorrowsDate("dd/MM/yyyy");
                            mInputTimeSurveyDetailsList.get(count).setValue(value);
                            Toast.makeText(mContext, "Please not," + displayname + " has been automatically updated to next date. ", Toast.LENGTH_LONG).show();
                        }
                    }

                }
            }
        }
        return isSucess;
    }

    private Boolean isAnyOfGivenValuesSelectedForRowID(String rowIdToCheckValue, ArrayList<String> valuesArrayList) {
        boolean isValueGivenValuesSelectedForRowID = false;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            if (mInputTimeSurveyDetailsList.get(count).getRowId().equalsIgnoreCase(rowIdToCheckValue)) {
                String value = mInputTimeSurveyDetailsList.get(count).getValue().toLowerCase();
                if (valuesArrayList.contains(value)) {
                    isValueGivenValuesSelectedForRowID = true;
                    break;
                }
            }
        }
        return isValueGivenValuesSelectedForRowID;
    }

    public boolean CheckSurveyDependentOnOtherInputValue(String rowID) {
        String rowid;
        boolean isSucess = true;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mDependentDisplayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                if (!mInputTimeSurveyDetailsList.get(count).getValue().isEmpty()) {
                    mCheckValue = mInputTimeSurveyDetailsList.get(count).getValue();
                } else {
                    mCheckValue = "";
                    isSucess = false;
                }
                break;
            }
        }
        return isSucess;
    }

    public boolean CheckColonPresentInType(String type) {
        boolean ispresent = false;
        if (type.contains(":")) {
            String[] RowData = type.split(":");
            if (RowData.length > 0) {
                boolStringType1 = RowData[0];
                boolStringType2 = RowData[1];
                if (boolStringType1.trim().equalsIgnoreCase("Y") &&
                        boolStringType2.trim().equalsIgnoreCase("N")) {
                    boolStringType1 = "Yes";
                    boolStringType2 = "No";
                } else {
                    boolStringType1 = boolStringType1.toUpperCase();
                    boolStringType2 = boolStringType2.toUpperCase();
                }
            }
            ispresent = true;
        }

        return ispresent;
    }

    private boolean ParseSubTablename(String tablename) {
        boolean ispasingrequired = false;
        mSubTableName = "";
        mSubColumnName = "";
        mSubDependent = "";

        if (tablename.contains("#")) {
            String[] splitablename = tablename.split("#");
            if (splitablename.length > 0) {
                mSubRowID = splitablename[0];
                mSubTableName = splitablename[1];
                mSubColumnName = splitablename[2];
                mSubDependent = splitablename[3];
                mMessage = splitablename[4];
                ispasingrequired = true;
            }
        }

        return ispasingrequired;
    }

    private boolean ParseTablename(String tablename) {
        boolean ispasingrequired = false;
        mTableName = "";
        mColumnName = "";
        mDependent = "";
        mShowColumn = "";

        if (tablename.contains("@")) {
            String[] splisubtablename = tablename.split("@");
            mSubtableInfo = splisubtablename[1];
            String[] splitablename = splisubtablename[0].split("#");
            if (splitablename.length > 0) {
                mTableName = splitablename[0];
                mColumnName = splitablename[1];
                mDependent = splitablename[2];
                mMessage = splitablename[3];

                if (!mTableName.isEmpty() && !mColumnName.isEmpty() && !mDependent.trim().isEmpty()) {
                    mCategory = false;
                    mSubCategory = true;
                } else {
                    mCategory = true;
                    mSubCategory = false;
                }
                ispasingrequired = true;
            }
        } else {
            if (tablename.contains("#")) {
                String[] splitablename = tablename.split("#");
                if (splitablename.length > 0) {
                    mTableName = splitablename[0];
                    if (splitablename[1].contains("%")) {
                        String[] splittedSendShow = splitablename[1].split("%");
                        mColumnName = splittedSendShow[0];
                        mShowColumn = splittedSendShow[1];
                        isShowValueSendValueDifferentForChcekBOx = true;
                    } else {
                        mColumnName = splitablename[1];
                        mShowColumn = "";
                    }

                    mDependent = splitablename[2];
                    mMessage = splitablename[3];

                    if (!mTableName.isEmpty() && !mColumnName.isEmpty() && !mDependent.trim().isEmpty()) {
                        mCategory = false;
                        mSubCategory = true;
                        CurrentCheckBoxItemDependantOn = mMessage;
                        isCurrentCheckBoxItemDependant = true;
                    } else {
                        mCategory = true;
                        mSubCategory = false;
                    }
                    ispasingrequired = true;
                }
            }
        }
        return ispasingrequired;
    }

    private void SetMaxNoofImage(String rowid, String value) {
        if (!mKeyValueImageList.isEmpty()) {
            boolean isexist = false;
            for (int count = 0; count < mKeyValueImageList.size(); count++) {
                if (rowid.equalsIgnoreCase(mKeyValueImageList.get(count).getKey())) {
                    isexist = true;
                    break;
                }
            }
            if (!isexist) {
                KeyValue obj = new KeyValue();
                obj.setKey(rowid);
                obj.setValue(value);
                mKeyValueImageList.add(obj);
            }
        } else {
            KeyValue obj = new KeyValue();
            obj.setKey(rowid);
            obj.setValue(value);
            mKeyValueImageList.add(obj);
        }
    }

    private void SetNoofImageCaptured(String rowid, String imagename) {
        for (int count = 0; count < mKeyValueImageList.size(); count++) {
            if (rowid.equalsIgnoreCase(mKeyValueImageList.get(count).getKey())) {
                int captured = Integer.parseInt(mKeyValueImageList.get(count).getEnteredValue());
                captured += 1;
                String image = mKeyValueImageList.get(count).getImageName().trim();
                image += imagename + "; ";
                mKeyValueImageList.get(count).setImageName(image);
                mKeyValueImageList.get(count).setEnteredValue(String.valueOf(captured));
                break;
            }
        }
    }

    private String GetImageName(String rowid) {
        String imagename = "";
        for (int count = 0; count < mKeyValueImageList.size(); count++) {
            if (rowid.equalsIgnoreCase(mKeyValueImageList.get(count).getKey())) {
                imagename = mKeyValueImageList.get(count).getImageName().trim();
                break;
            }
        }
        return imagename;
    }

    private boolean ImageValidation(String rowid) {
        boolean isempty = false;
        for (int count = 0; count < mKeyValueImageList.size(); count++) {
            if (rowid.equalsIgnoreCase(mKeyValueImageList.get(count).getKey())) {
                int max = Integer.parseInt(mKeyValueImageList.get(count).getValue());
                int captured = Integer.parseInt(mKeyValueImageList.get(count).getEnteredValue());
                isempty = captured < max;
            }
        }
        return isempty;
    }

    @SuppressLint("SimpleDateFormat")
    public void ShowImageCaptureLayer(String rowid) {
        if (!(ImageValidation(rowid))) {
            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
        } else {
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
            mSImageName = imageName;
            mImageName = imageName + "; ";
            mImagePath = Utils.getAppStoragePath(mContext) + imageName;
            mImageFile = new File(mImagePath);
            try {
                mImageFile.createNewFile();
            } catch (IOException ignored) {
            }

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                mFileUri = FileProvider.getUriForFile(mContext, BuildConfig.APPLICATION_ID + ".provider", mImageFile);
            } else {
                mFileUri = Uri.fromFile(mImageFile);
            }
            Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, mFileUri);
            startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
        }
    }

    public boolean CheckPreDefinedData(String rowid) {
        boolean ismatch = false;
        if (mMallSurveyRelationList != null) {
            for (int count = 0; count < mMallSurveyRelationList.size(); count++) {
                if (mMallSurveyRelationList.get(count).getRowId().equalsIgnoreCase(rowid)) {
                    ismatch = true;
                    mMallColumnName = mMallSurveyRelationList.get(count).getMallInfo();
                    break;
                }
            }
        }
        return ismatch;
    }

    public void ChangeMadatory(String rowID, String mandatory) {
        String rowid;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowid.equalsIgnoreCase(rowID)) {
                mInputTimeSurveyDetailsList.get(count).setMandatory(mandatory);
            }
        }
    }

    public void GetRadioGroupMatrixListContainer() {
        if (mRadioGroupMatrixListContainer != null) {
            Set<Entry<String, ArrayList<RadioGroup>>> set = mRadioGroupMatrixListContainer.entrySet();
            Iterator<Entry<String, ArrayList<RadioGroup>>> iterator = set.iterator();
            int ROW;
            while (iterator.hasNext()) {
                Entry<String, ArrayList<RadioGroup>> entry = iterator.next();
                String rowid = entry.getKey();
                List<RadioGroup> values = entry.getValue();
                StringBuilder val = new StringBuilder();
                for (ROW = 0; ROW < values.size(); ROW++) {
                    int selectedid = values.get(ROW).getCheckedRadioButtonId();
                    if (selectedid > 0) {
                        val.append(ROW).append(",").append(((RadioButton) findViewById(selectedid)).getText().toString()).append("#");
                    } else {
                        val.append(ROW).append(",#");
                    }
                }
                if (val.toString().endsWith("#")) {
                    val = new StringBuilder(val.substring(0, val.length() - 1));
                }
                SetSurveyValue(rowid, val.toString());
            }
        }
    }

    public void GetCheckBoxMatrixListContainer() {
        if (mCheckBoxMatrixListContainer != null) {
            Set<Entry<String, ArrayList<CheckBox>>> set = mCheckBoxMatrixListContainer.entrySet();
            Iterator<Entry<String, ArrayList<CheckBox>>> iterator = set.iterator();
            int ROW;
            while (iterator.hasNext()) {
                Entry<String, ArrayList<CheckBox>> entry = iterator.next();
                String rowid = entry.getKey();
                List<CheckBox> values = entry.getValue();
                StringBuilder val = new StringBuilder();
                for (ROW = 0; ROW < values.size(); ROW++) {
                    val.append(values.get(ROW).getTag()).append(",").append(values.get(ROW).isChecked()).append("#");
                }
                if (val.toString().endsWith("#")) {
                    val = new StringBuilder(val.substring(0, val.length() - 1));
                }
                SetSurveyValue(rowid, val.toString());
            }
        }
    }

    public void GetTableViewDataMultiLevel(String rowId) {
        for (int count = 0; count < mSurveyTableViewList.size(); count++) {
            SurveyTableView obj = mSurveyTableViewList.get(count);
            if (obj.getRowId().equalsIgnoreCase(rowId) && obj.getDependentValue().trim().matches("")) {
                mType = obj.getType();
                if (obj.getDependentOn() != null && !obj.getDependentOn().trim().isEmpty()) {
                    String dependentrowid = obj.getDependentOn();
                    if (CheckSurveyDependentOnOtherInputValue(dependentrowid)) {
                        String depedentvalue = obj.getDependentValue();
                        if (mCheckValue.equalsIgnoreCase(depedentvalue)) {
                            String data = obj.getValue();
                            if (!data.trim().isEmpty()) {
                                ParseTableViewData(data);
                            } else {
                                Utils.showToast(mContext, "Error in table view data.\nPlease Synchronize Data");
                            }
                            break;
                        }
                    } else {
                        Utils.showToast(mContext, "Please select " + mDependentDisplayName);
                        break;
                    }
                } else {
                    String data = obj.getValue();
                    if (!data.trim().isEmpty()) {
                        ParseTableViewData(data);
                    } else {
                        Utils.showToast(mContext, "Error in table view data.\nPlease Synchronize Data");
                    }
                    break;
                }
            }
        }
    }

    public void GetTableViewData(String rowId) {
        for (int count = 0; count < mSurveyTableViewList.size(); count++) {
            SurveyTableView obj = mSurveyTableViewList.get(count);
            if (obj.getRowId().equalsIgnoreCase(rowId)) {
                mType = obj.getType();
                if (obj.getDependentOn() != null && !obj.getDependentOn().trim().isEmpty()) {
                    String dependentrowid = obj.getDependentOn();
                    if (CheckSurveyDependentOnOtherInputValue(dependentrowid)) {
                        String depedentvalue = obj.getDependentValue();
                        if (mCheckValue.equalsIgnoreCase(depedentvalue)) {
                            String data = obj.getValue();
                            if (!data.trim().isEmpty()) {
                                ParseTableViewData(data);
                            } else {
                                Utils.showToast(mContext, "Error in table view data.\nPlease Synchronize Data");
                            }
                            break;
                        }
                    } else {
                        Utils.showToast(mContext, "Please select " + mDependentDisplayName);
                        break;
                    }
                } else {
                    String data = obj.getValue();
                    if (!data.trim().isEmpty()) {
                        ParseTableViewData(data);
                    } else {
                        Utils.showToast(mContext, "Error in table view data.\nPlease Synchronize Data");
                    }
                    break;
                }
            }
        }
    }

    public void GetMultiLevelTableViewData(String value) {
        boolean isDependantDataFound = false;
        StringBuilder data = new StringBuilder();
        int numberOfMatch = 0;
        mType = "";
        for (int count = 0; count < mSurveyTableViewList.size(); count++) {
            SurveyTableView obj = mSurveyTableViewList.get(count);
            if (obj.getDependentValue().equalsIgnoreCase(value) && obj.getRowId().equalsIgnoreCase(currentRowId)) {
                if (!obj.getValue().trim().isEmpty()) {
                    numberOfMatch++;
                    if (numberOfMatch == 1) {
                        mType = obj.getType();
                        data = new StringBuilder(obj.getValue());
                    } else {
                        mType = MessageFormat.format("{0}/{1}", mType, obj.getType());
                        data.append("/").append(obj.getValue());
                    }
                    isDependantDataFound = true;
                }
            }
        }
        if (isDependantDataFound) {
            ParseTableViewData(data.toString());
        } else {
            SetTextViewText(mFinalRowID, TextUtils.join(", ", mMultilevelTableViewData));
            SetSurveyValue(mFinalRowID, TextUtils.join("#", mMultilevelTableViewData));
        }
    }

    public void ParseTableViewData(String data) {
        if (data.contains("/")) {
            String[] splitstring = data.split("/");
            if (splitstring.length > 0) {
                values = new String[splitstring.length];
                System.arraycopy(splitstring, 0, values, 0, splitstring.length);
                if (splitstring.length > 1) {
                    mPredefinedValueEditTextDialog = splitstring[0];
                    mPredefinedValueEditTextDialog2 = splitstring[1];
                }
            } else {
                values = new String[1];
                values[0] = data;
                mPredefinedValueEditTextDialog = data;
            }
        } else {
            values = new String[1];
            values[0] = data;
            mPredefinedValueEditTextDialog = data;
        }
        ShowList(mType.trim());
    }

    public void FillPreDefinedData(String columnename) {
        mPredefinedValue = mAceDnsDatabase.GetMallMasterValue(columnename, mMallId);
    }

    private ImageView getImageViewByTag(String mFinalRowID) {
        ImageView myImageView = null;
        for (int count = 0; count < mImageViewList.size(); count++) {
            if (mImageViewList.get(count).getTag().toString().equalsIgnoreCase(mFinalRowID)) {
                myImageView = mImageViewList.get(count);
                break;
            }
        }
        return myImageView;
    }

    private void SetTextViewTextBlank(String tag) {
        for (int count = 0; count < mTextViewList.size(); count++) {
            if (mTextViewList.get(count).getTag().toString().equalsIgnoreCase(tag)) {
                mTextViewList.get(count).setText("");
                mTextViewList.get(count).setTextColor(Color.parseColor("#FF9600"));
                mTextViewList.get(count).setTypeface(null, Typeface.BOLD);
                break;
            }
        }
    }

    private void SetMandatoryChangeBlank(String tag, String mandatory) {
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            if (mInputTimeSurveyDetailsList.get(count).getRowId().equalsIgnoreCase(tag)) {
                mInputTimeSurveyDetailsList.get(count).setMandatory(mandatory);
                mInputTimeSurveyDetailsList.get(count).setValidation("");
                break;
            }
        }
    }

    private void SetMandatoryChangeYes(String tag, String mandatory) {
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            if (mInputTimeSurveyDetailsList.get(count).getRowId().equalsIgnoreCase(tag)) {
                mInputTimeSurveyDetailsList.get(count).setMandatory(mandatory);
                String validation = mAceDnsDatabase.getSurveyValidationByID(tag);
                mInputTimeSurveyDetailsList.get(count).setValidation(validation);
                break;
            }
        }
    }

    private String getValidationByRowId(String rowID) {
        String validation = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            String currentrowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(currentrowid)) {
                validation = mInputTimeSurveyDetailsList.get(count).getValidation();
                break;
            }
        }
        return validation;
    }

    private String getSurveyValueByRowId(String rowID) {
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            String currentrowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(currentrowid)) {
                break;
            }
        }
        String defaultValue = "";
        String sqlQuery = "Select f_conversion From facilitator_master Where f_code='" + rowID + "'";
        defaultValue = mAceDnsDatabase.GetSurveyValueFromQuery(sqlQuery);
        return defaultValue;
    }

    private String getValueById(String id) {
        String defaultValue;
        String sqlQuery = "Select cube_test_date From mtl_testing_format Where mtl_testing_format_id='" + id + "'";
        defaultValue = mAceDnsDatabase.GetSurveyValueFromQuery(sqlQuery);
        return defaultValue;
    }

    private String getValueByFiId(String fi, String id) {
        String defaultValue;
        String sqlQuery = "Select " + fi + " From mtl_testing_format Where mtl_testing_format_id='" + id + "'";
        defaultValue = mAceDnsDatabase.GetSurveyValueFromQuery(sqlQuery);
        return defaultValue;
    }

    @SuppressLint("SimpleDateFormat")
    private String getDateValAdd(String dateValue, String val) {
        String defaultValue = "";
         SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy");
        try {
            Date date = sdf.parse(dateValue);
            Calendar calendar = Calendar.getInstance();
            calendar.setTime(date);
            calendar.add(Calendar.DAY_OF_YEAR, Integer.parseInt(val));
            Date newDate = calendar.getTime();
            defaultValue = sdf.format(newDate);
        } catch (ParseException e) {
            Log.d("TAG", "getDateValAdd: " + e.getMessage());
        }
        return defaultValue;
    }

    private String getAcednsByRowId(String rowID) {
        String validation = "";
        try {
            for (int count = 0; count < mSurveyInputList.size(); count++) {
                String currentrowid = mSurveyInputList.get(count).getSurveyRowId();
                if (rowID.equalsIgnoreCase(currentrowid)) {
                    validation = mSurveyInputList.get(count).getAceDns();
                    break;
                }
            }
            return validation;
        } catch (Exception e) {
            return "N";
        }

    }

    @SuppressLint("SimpleDateFormat")
    public String getDateDiff(String startDateString, String endDateString) {
         SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy");
        long diffInDays = 0;
        try {
            Date startDate = sdf.parse(startDateString);
            Date endDate = sdf.parse(endDateString);
            assert startDate != null;
            assert endDate != null;
            long diffInMillis = endDate.getTime() - startDate.getTime();
            diffInDays = TimeUnit.MILLISECONDS.toDays(diffInMillis);
            return diffInDays + "";
        } catch (ParseException e) {
            Log.d("TAG", "getDateDiff: " + e.getMessage());
        }
        return diffInDays + "";
    }
}