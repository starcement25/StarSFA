package com.forcepower.acedns.activity.non_auth.main_menu.market_overview;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
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

import androidx.annotation.NonNull;
import androidx.core.content.FileProvider;
import androidx.core.text.HtmlCompat;

import android.text.Editable;
import android.text.Html;
import android.text.InputType;
import android.text.TextUtils;
import android.text.TextWatcher;
import android.text.method.DigitsKeyListener;
import android.util.DisplayMetrics;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.adapter.KeyValueCheckAdapter;
import com.forcepower.acedns.adapter.KeyValueNormalAdapter;
import com.forcepower.acedns.adapter.ListSearchCheckAdapter;
import com.forcepower.acedns.backgroundTask.AUTH_GetOTP;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyTask;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.bean.MallSurveyRelation;
import com.forcepower.acedns.bean.SurveyDetails;
import com.forcepower.acedns.bean.SurveyInput;
import com.forcepower.acedns.bean.SurveyRoot;
import com.forcepower.acedns.bean.SurveyTableView;
import com.forcepower.acedns.bean.commonDatabaseHelper;
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
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Locale;
import java.util.Map.Entry;
import java.util.Objects;
import java.util.Set;

import static android.view.View.GONE;
import static com.forcepower.acedns.constants.Constants.CurrentCheckBoxItemDependantOn;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.isCurrentCheckBoxItemDependant;
import static com.forcepower.acedns.constants.Constants.isShowValueSendValueDifferentForChcekBOx;
import static com.forcepower.acedns.constants.Constants.masterApiCallingFlag;
import static com.forcepower.acedns.constants.Constants.sendColumnDataForSurveyCheckBox;
import static com.forcepower.acedns.constants.Constants.shouldUpdateCustomerMasterWithEmail;

public class SurveyActivity extends AceDnsParentActivity {
    public static ArrayList<KeyValue> mKeyValueList_SearchWithInput;

    @SuppressLint("StaticFieldLeak")
    private static TextView mTitleText = null;
    @SuppressLint("StaticFieldLeak")
    private static LinearLayout mParentLayout = null;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonBack = null, mButtonUndo = null, mButtonSubmit = null;
    public RadioButton radioButton;

    private ArrayList<String> tableViewListWithGoneStatus;
    private ArrayList<SurveyInput> mSurveyInputList;
    private ArrayList<SurveyDetails> mInputTimeSurveyDetailsList, mUndoSurveyList;
    private ArrayList<MallSurveyRelation> mMallSurveyRelationList;
    private ArrayList<SurveyTableView> mSurveyTableViewList;
    private ArrayList<KeyValue> mKeyValueImageList, mKeyValueSubList, mKeyValueList;
    private final List<EditText> mEditTextList = new ArrayList<>();
    private List<String> mMultilevelTableViewData = new ArrayList<>();

    public ArrayList<commonDatabaseHelper> masterViewListForSubACtion;
    public ArrayList<Button> mButtonList;
    public ArrayList<CheckBox> mCheckBoxList = new ArrayList<>(), mMatrixCheckBoxList;
    public ArrayList<DatePicker> mdatePickerListForSubActionLayout;
    public ArrayList<TextView> mTextViewList;
    public ArrayList<String> mNonRepeat;
    public ArrayList<RadioGroup> mMatrixRadioGroupList;
    public ArrayList<SurveyInput> surveyInputListForSubActionMasterView;

    public HashMap<String, ArrayList<CheckBox>> mCheckBoxMatrixListContainer = new HashMap<>();
    public HashMap<String, ArrayList<RadioGroup>> mRadioGroupMatrixListContainer = new HashMap<>();

    public AceDnsDatabase mAceDnsDatabase;
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    public ProgressDialog mPrepareSurveyProgressDialog;
    public Handler mPrepareSurveyHandler, mPrepareDataSaveHandler;
    public Context mContext;

    public boolean mCategory = false, mSubCategory = false, mIsOTP = false, mIsBranch = false, isDependangtDataEmpty = false, mIsTittle = false, mIsTableView = false, mIsItemZeroTableView = false;
    public String ItemZeroTableViewRowId = "", dependantConditionSqlQueryForRadioType = "", mDateValue = "";
    public int spliton = 0, mYear, mMonth, mDay;

    public EditText doubleTextView, doubleTextView2, mEdiTextDynamic = null;
    public DatePicker datePicker;
    public Uri mFileUri;
    public GPSTracker gpstracker;
    private File mImageFile;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;

    private String currentRowId = "", mCheckValue = "", mDependentDisplayName = "", mDisplayName = "", mMenuID = "", mMallId = "", mSurveyType = "", mPredefinedValue = "",
            mPredefinedValueEditTextDialog = "", mPredefinedValueEditTextDialog2 = "", mMallColumnName = "", mSubtableInfo = "", mEditType = "", mWhereClause = "",
            actionStringOfCurrentSelectedItem = "", tempValForDependentSurvey = "", currentTableHeader = "", currentItemDisplayName = "", mSubRowID = "", mSubTableName = "",
            mSubColumnName = "", mSubDependent = "", mLayout = "", mType = "", mDependentRowId = "", mFinalRowID = "", mCondition = "", mImagePath = "", mImageName = "",
            mSImageName = "", mTableName = "", mSendColumn = "", mShowColumn = "", mColumnName = "", mCustomerSelectionBasis = "", mCustomerSelectionBasisFilter = "",
            mMessage = "", mDependent = "", mSubMenu = "", mOTPRowID = "", mOTPPhoneNo = "", boolStringType1 = "", boolStringType2 = "", mDecision = "", mSubActionTag = "",
            mActionPressed = "", mRoundValue = "";
    private final String mActionstring = "";
    public static String mParentType = "";
    private String[] values, subvalues;
    private boolean ismultipleMasterViewInSubACtion = false, isRound = false, mIsButtonEnable = true;
    private final int TAKE_PHOTO_CODE = 0;

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

    @SuppressLint({"SetTextI18n", "HandlerLeak"})
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        shouldUpdateCustomerMasterWithEmail = false;
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey);
        mContext = SurveyActivity.this;
        tableViewListWithGoneStatus = new ArrayList<>();
        InitializeView();
        RegisterActivities.registerActivity(this);
        mTextViewList = new ArrayList<>();
        mMultilevelTableViewData = new ArrayList<>();
        if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
            mSubMenu = getIntent().getStringExtra("SUBMENU");
            Log.d("TAG", "titleFoundAndGetData 1 : " + mSubMenu);
            mTitleText.setText(mSubMenu);
            if (mSubMenu.matches("Branding Verification")) {
                Log.d("TAG", "titleFoundAndGetData 2 : " + mSubMenu);
                mTitleText.setText("OOH & WALL WRAP");
            }
            if (mSubMenu.matches("Counter Branding")) {
                Log.d("TAG", "titleFoundAndGetData 3 : " + mSubMenu);
                mTitleText.setText("Retail branding");
            }
            if(mSubMenu.equalsIgnoreCase("Counter Visit"))
                mTitleText.setText("MTL Counter Visit");
        }

        if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("no") && !Constants.surveyFormDetailsObj.getSurveySubMenuDetails().isEmpty()) {
            mSubMenu = getIntent().getStringExtra("SUBMENU");
            Log.d("TAG", "titleFoundAndGetData 4 : " + mSubMenu);
            mTitleText.setText(Constants.surveyFormDetailsObj.getSurveySubMenuDetails());
        }

        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
            mLayout = getIntent().getStringExtra("SURVEY");
        } else {
            Constants.mFinalSurveyList = new ArrayList<>();
        }

        if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes") && Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(Constants.mSurveyMainType)) {
            mMenuID = getIntent().getStringExtra("SURVEYMENUID");
            if (getIntent().hasExtra("mMenuName")) {
                String mMenuName = getIntent().getStringExtra("mMenuName");
                Log.d("TAG", "titleFoundAndGetData 5 : " + mSubMenu);
                mTitleText.setText(mMenuName);
                assert mMenuName != null;
                if (mMenuName.matches("Branding Verification")) {
                    Log.d("TAG", "titleFoundAndGetData 6 : " + mSubMenu);
                    mTitleText.setText("OOH & WALL WRAP");
                }
            }
        }

        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
            mSurveyType = getIntent().getStringExtra("SURVEYMADEAT");
            mMallId = Constants.selectedMallMaster.getMallId();
        }

        mSurveyInputList = new ArrayList<>();
        mInputTimeSurveyDetailsList = new ArrayList<>();
        mUndoSurveyList = new ArrayList<>();
        mKeyValueImageList = new ArrayList<>();
        mNonRepeat = new ArrayList<>();
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        mPrepareDataSaveHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SurveyActivity.this.runOnUiThread(() -> {
                    if (dojob == 1) {
                        mAceDnsDatabase.DeleteSurveyTempOutData();
                        new TRANS_SubmitSurveyTask(mContext, true, "SUBMIT").execute();
                    }
                });
            }
        };

        mPrepareSurveyHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SurveyActivity.this.runOnUiThread(() -> {
                    switch (dojob) {
                        case 1:
                            if (!mSurveyInputList.isEmpty()) {
                                DrawLayout();
                                if (mIsTableView) {
                                    mSurveyTableViewList = mAceDnsDatabase.GetSurveyTableView();
                                    if (mSurveyTableViewList.isEmpty()) {
                                        Toast.makeText(mContext, "Error in table view data.\nPlease Synchronize Data", Toast.LENGTH_LONG).show();
                                    }
                                }
                            } else {
                                Toast.makeText(mContext, "Error in creating layout.Please Retry..", Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 2:
                            if (isDependangtDataEmpty) {
                                Utils.showToast(mContext, "Please select values for " + CurrentCheckBoxItemDependantOn);
                                isDependangtDataEmpty = false;
                                CurrentCheckBoxItemDependantOn = "";
                            } else if (!mCategory && mSubCategory) {
                                if (!mCondition.trim().isEmpty()) {
                                    if (values.length > 0) {
                                        Log.d("TAG", "__TAG__ handleMessage: 4");
                                        ShowList(mType);
                                    } else {
                                        SetTextViewText(mFinalRowID, "");
                                        SetSurveyValue(mFinalRowID, "");
                                        Toast.makeText(mContext, "No record found", Toast.LENGTH_LONG).show();
                                    }
                                } else {
                                    if (mMessage.equalsIgnoreCase("0")) {
                                        Toast.makeText(mContext, "Please provide valid input", Toast.LENGTH_LONG).show();
                                    } else {
                                        Toast.makeText(mContext, "Please select " + mMessage, Toast.LENGTH_LONG).show();
                                    }
                                }
                            } else {
                                Log.d("TAG", "__TAG__ handleMessage: 3");
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
                        case 6, 8:
                            if (values != null) {
                                Log.d("TAG", "__TAG__ handleMessage: 2");
                                ShowList(mType);
                            } else {
                                Utils.showToast(mContext, "No data found. Please Synchronize Data");
                            }
                            break;
                        case 7, 9, 11, 12:
                            if (mKeyValueList != null && !mKeyValueList.isEmpty()) {
                                ShowList(mType, mDecision);
                            } else {
                                Utils.showToast(mContext, "No data found. Please Synchronize Data");
                            }
                            break;
                        case 10:
                            if (values != null) {
                                Log.d("TAG", "__TAG__ handleMessage: 1");
                                ShowList(mParentType);
                                mParentType = "";
                            } else {
                                Utils.showToast(mContext, "No data found. Please Synchronize Data");
                            }
                            break;
                    }
                });
            }
        };

        mButtonUndo.setOnClickListener(v -> {
            String rowid, value;
            for (int count = 0; count < mUndoSurveyList.size(); count++) {
                rowid = mUndoSurveyList.get(count).getRowId();
                value = mUndoSurveyList.get(count).getValue();
                SetSurveyValue(rowid, value);

                String tablename = GetTableName(rowid).trim();
                if (!tablename.isEmpty()) {
                    if (tablename.contains("@")) {
                        String[] splisubtablename = tablename.split("@");
                        mSubtableInfo = splisubtablename[1];
                        if (mSubtableInfo.contains("#")) {
                            String[] splitablename = mSubtableInfo.split("#");
                            if (splitablename.length > 0) {
                                mSubRowID = splitablename[0];
                                mSubTableName = splitablename[1];
                                mSubColumnName = splitablename[2];
                                mSubDependent = splitablename[3];
                                mMessage = splitablename[4];
                                String tempvalue = value.trim();
                                if (tempvalue.endsWith(";")) {
                                    tempvalue = tempvalue.substring(0, tempvalue.length() - 1);
                                }
                                if (tempvalue.contains(";")) {
                                    String[] splittemp = tempvalue.split(";");
                                    StringBuilder a = new StringBuilder();
                                    for (String s : splittemp) {
                                        a.append(mCondition).append("'").append(s).append("',");
                                    }
                                    mCondition = a.toString();
                                } else {
                                    mCondition = "'" + tempvalue + "'";
                                }
                                if (mCondition.endsWith(",")) {
                                    mCondition = mCondition.substring(0, mCondition.length() - 1);
                                }
                                int max = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mSubTableName, mSubColumnName, mSubDependent, mCondition);
                                if (max > 0) {
                                    ChangeMadatory(mSubRowID, "Y");
                                } else {
                                    ChangeMadatory(mSubRowID, "N");
                                }

                                mSubRowID = "";
                                mSubTableName = "";
                                mSubColumnName = "";
                                mSubDependent = "";
                                mCondition = "";
                                mSubtableInfo = "";
                            }
                        }
                    }
                }

                if (mEditType.equalsIgnoreCase("tickbox")
                        && value.equalsIgnoreCase("yes")) {
                    for (int cc = 0; cc < mCheckBoxList.size(); cc++) {
                        if (rowid.equalsIgnoreCase(mCheckBoxList.get(cc).getTag().toString())) {
                            mCheckBoxList.get(cc).setChecked(true);
                        }
                    }
                }
                SetTextViewText(rowid, value);
                SetEditTextText(rowid, value);
            }
        });

        mButtonBack.setOnClickListener(v -> {
            Constants.mNoOfCapture = 0;
            finish();
        });

        mButtonSubmit.setOnClickListener(v -> {
            boolean check, checkvalidation = false;
            GetCheckBoxMatrixListContainer();
            GetRadioGroupMatrixListContainer();
            Log.d("TAG", "submit : 1");
            if (!mEditTextList.isEmpty()) {
                Log.d("TAG", "submit : 2");
                for (EditText editText : mEditTextList) {
                    Log.d("TAG", "submit : 2-1");
                    String tag = editText.getTag().toString();
                    String value = editText.getText().toString();
                    boolean checksurveyvalue = SetSurveyValue(tag, value);
                    if (checksurveyvalue) {
                        Log.d("TAG", "submit : 2-2");
                        checkvalidation = CheckSurveyValidation(tag, value);
                        if (isRound) {
                            Log.d("TAG", "submit : 2-3");
                            SetSurveyValue(tag, mRoundValue);
                            isRound = false;
                        }
                    }
                    if (!checkvalidation) {
                        Log.d("TAG", "submit : 2-4");
                        break;
                    }
                    if (tag.equalsIgnoreCase(mOTPRowID)) {
                        Log.d("TAG", "submit : 2-5");
                        mOTPPhoneNo = value;
                    }
                }
                if (!checkvalidation) {
                    Log.d("TAG", "submit : 2-6");
                    return;
                }
                if (CheckSurveyMandatory()) {
                    Log.d("TAG", "submit : 2-7");
                    RemoveDuplicateData();
                    Constants.mFinalSurveyList.addAll(mInputTimeSurveyDetailsList);
                    if (InsertSurveyTemporary()) {
                        Log.d("TAG", "submit : 2-7-1");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "submit : 2-7-1-1");
                            SetSurveyStatus();
                            if (Constants.surveyFormDetailsObj.getSurveyOTP().equalsIgnoreCase("yes") && mIsOTP) {
                                Log.d("TAG", "submit : 2-7-1-1-1");
                                Constants.SurveyRowID = mOTPRowID;
                                if (!mOTPPhoneNo.equalsIgnoreCase(Constants.LIPLMOBILENO)) {
                                    Log.d("TAG", "submit : 2-7-2");
                                    new AUTH_GetOTP(mContext).execute(mOTPPhoneNo);
                                } else {
                                    Log.d("TAG", "submit : 2-7-3");
                                    finish();
                                }
                            } else {
                                Log.d("TAG", "submit : 2-8");
                                finish();
                            }
                        } else {
                            Log.d("TAG", "submit : 2-9");
                            mButtonSubmit.setEnabled(false);
                            SaveDatatoDatabase(1);
                        }
                    }
                }
            } else {
                Log.d("TAG", "submit : 3");
                check = CheckSurveyMandatory();
                if (check) {
                    Log.d("TAG", "submit : 3-1");
                    RemoveDuplicateData();
                    Constants.mFinalSurveyList.addAll(mInputTimeSurveyDetailsList);
                    if (InsertSurveyTemporary()) {
                        Log.d("TAG", "submit : 3-2");
                        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                            Log.d("TAG", "submit : 3-3");
                            SetSurveyStatus();
                            finish();
                        } else {
                            Log.d("TAG", "submit : 3-4");
                            mButtonSubmit.setEnabled(false);
                            SaveDatatoDatabase(1);
                        }
                    }
                }
            }
        });

        if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes") && Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(Constants.mSurveyMainType)
                && Constants.surveyFormDetailsObj.getSurveyMallSurveyRelation().equalsIgnoreCase("yes")) {
            PrepareSurveyData(3, mLayout);
        } else {
            PrepareSurveyData(1, mLayout);
        }

        ConnectionDetector cd;
        cd = new ConnectionDetector(mContext);
        if (cd.isConnectingToInternet()) {
            masterApiCallingFlag = false;
            new Thread() {
                public void run() {
                    new commonAsyncTaskMaster(mContext, "vendor-details-download");
                }
            }.start();
        }

        try {
            if (Constants.nickName.equalsIgnoreCase("sai") && cd.isConnectingToInternet())
                PrepareCustomerData(1);
        } catch (Exception e) {
            Log.d("TAG", "onCreate: " + e.getMessage());
        }

        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
            }
        };
    }

    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {
                if (task == 1) {
                    new commonAsyncTaskMaster(mContext, "customer_master");
                }
                Message msg = mHandlerPrepareSaudaData.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPrepareSaudaData.sendMessage(msg);
            }
        }.start();
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

    /**
     * This method is used to delete the duplicate data from the Collection Buffer
     */
    public void RemoveDuplicateData() {
        String rowid;
        for (int count = 0; count < mSurveyInputList.size(); count++) {
            rowid = mSurveyInputList.get(count).getSurveyRowId();
            for (int removeindex = 0; removeindex < Constants.mFinalSurveyList.size(); removeindex++) {
                if (rowid.trim().equalsIgnoreCase(Constants.mFinalSurveyList.get(removeindex).getRowId().trim())) {
                    SurveyDetails remove = Constants.mFinalSurveyList.remove(removeindex);
                }
            }
        }
    }

    /**
     * This method is used to insert temporary data to local database
     */
    public boolean InsertSurveyTemporary() {
        boolean issucess;
        if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
            mAceDnsDatabase.DeleteSurveyTempOutData(mLayout);
            issucess = mAceDnsDatabase.INSERTToSurveyTempOutData(mInputTimeSurveyDetailsList, mLayout);
        } else {
            mAceDnsDatabase.DeleteSurveyTempOutData();
            issucess = mAceDnsDatabase.INSERTToSurveyTempOutData(mInputTimeSurveyDetailsList, "");
        }
        return issucess;
    }

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
    @SuppressLint("SimpleDateFormat")
    public void SaveSurveyDataTODatabase() {

        String timeStamp;
        timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

        mAceDnsTransactionDatabase.INSERTtoSurveyOutput(timeStamp, "SU");
        String trans_id = "SU" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
        if (shouldUpdateCustomerMasterWithEmail) {
            HashMap<String, String> customerCodeEmailPhone = getStringStringHashMap();
            mAceDnsTransactionDatabase.updateCustomerEmailPhone(customerCodeEmailPhone);
        }

        mAceDnsTransactionDatabase.InsertSurveyHeader(trans_id, Constants.mSurveyMainType, mMenuID, "", "", "", "", "", "", Constants.mSurveyRouteCode);
        mAceDnsTransactionDatabase.insertToLocationTable("SU", timeStamp);
        gpstracker.stopUsingGPS();

    }

    private HashMap<String, String> getStringStringHashMap() {
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
        return customerCodeEmailPhone;
    }

    /**
     * This method is used set survey value of particular <b>rowID</b>
     *
     * @param rowID rowid of the layer
     * @param value user input value
     * @return true if successfully added
     */
    public boolean SetSurveyValue(String rowID, String value) {
        String rowid;
        boolean isSucess = true;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mEditType = mInputTimeSurveyDetailsList.get(count).getType();
                if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("Y")) {
                    if (!value.isEmpty()) {
                        mInputTimeSurveyDetailsList.get(count).setValue(value);
                    } else {
                        mInputTimeSurveyDetailsList.get(count).setValue(value);
                        isSucess = false;
                    }
                } else if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("DEPENDENT")) {
                    if (!value.isEmpty()) {
                        mInputTimeSurveyDetailsList.get(count).setValue(value);
                    } else {
                        mInputTimeSurveyDetailsList.get(count).setValue(value);
                        isSucess = false;
                    }
                } else {
                    mInputTimeSurveyDetailsList.get(count).setValue(value);
                }
                break;
            }
        }
        return isSucess;
    }

    public boolean SetTextViewTextDealer(String fRowID, String val, String key) {
        String rowid;
        boolean isSucess = false;
        boolean isvalue = false;
        String displayName;

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            if (!isvalue) {
                rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
                if (fRowID.equalsIgnoreCase(rowid)) {
                    displayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                    if (displayName.contains("Dealer")) {
                        isSucess = true;
                    }
                }
            }
        }
        return isSucess;
    }

    /**
     * This method is used to draw the dynamic layer
     */
    @SuppressLint({"UseCompatLoadingForDrawables", "SetTextI18n"})
    @SuppressWarnings("deprecation")
    public void DrawLayout() {
        String type, rowid, displayname, tablename, mandatory, actionId, action, validation;

        LayoutParams Params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        mButtonList = new ArrayList<>();

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

            Log.d("TAG", "PrepareSurveyData: "+count+": "+type+" <=> "+rowid+" <=> "+displayname+" <=> "+tablename+" <=> "+mandatory+" <=> "+actionId+" <=> "+action+" <=> "+validation);

            mSurveyDetails.setRowId(rowid);
            mSurveyDetails.setType(type);
            mSurveyDetails.setMandatory(mandatory);
            mSurveyDetails.setActionId(actionId);
            mSurveyDetails.setAction(action);
            mSurveyDetails.setValidation(validation);
            mSurveyDetails.setDisplayName(displayname);

            if (mandatory.equalsIgnoreCase("Y")) {
                displayname = displayname + "<font color='red'>*</font>";
            }

            if (type.equalsIgnoreCase("radio")) {
                childlayout.addView(NewtextView(rowid));
                if (displayname.contains("#")) {
                    String[] finaldisplayname = displayname.split("#");
                    displayname = finaldisplayname[0];
                }

                if (CheckPreDefinedData(rowid)) {
                    FillPreDefinedData(mMallColumnName);
                    mMallColumnName = "";
                    SetSurveyValue(rowid, mPredefinedValue);
                    SetTextViewText(rowid, mPredefinedValue);
                    if (!mPredefinedValue.trim().isEmpty()) {
                        mIsButtonEnable = false;
                    }
                    mPredefinedValue = "";
                }


                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);


            } else if (type.equalsIgnoreCase("imageview")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);

            } else if (type.equalsIgnoreCase("heading")) {
                childlayout.addView(NewtextViewHeading(rowid, displayname, 17f));
            } else if (type.equalsIgnoreCase("bool")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
            } else if (type.equalsIgnoreCase("date")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));

            } else if (type.equalsIgnoreCase("dynamicview")) {
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
                mEdiTextDynamic.addTextChangedListener(new GenericTextWatcher(rowid, action, validation));
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

            } else if (type.equalsIgnoreCase("radiomatrix")) {

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
                String[] xVals = xAxis.split(":");
                String[] yVals = action.split(":");

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

                for (String xVal : xVals) {

                    TextView textviewheader = new TextView(this);
                    textviewheader.setText(xVal);
                    textviewheader.setTextColor(Color.parseColor("#003399"));
                    textviewheader.setLayoutParams(qtparam);
                    tabchildlayoutx.addView(textviewheader);

                }
                childlayout.addView(tabchildlayoutx);

                mMatrixRadioGroupList = new ArrayList<>();


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

            } else if (type.equalsIgnoreCase("checkboxmatrix")) {
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

                String[] xyAxis = action.split("#");
                String xAxis = xyAxis[0];
                String yAxis = xyAxis[1];
                String[] xVals = xAxis.split(":");
                String[] yVals = yAxis.split(":");

                LayoutParams qtparam = new LayoutParams(0, LayoutParams.WRAP_CONTENT, 24f);

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

                for (String xVal : xVals) {

                    TextView textviewheader = new TextView(this);
                    textviewheader.setText(xVal);
                    textviewheader.setTextColor(Color.parseColor("#003399"));
                    textviewheader.setLayoutParams(qtparam);
                    tabchildlayoutx.addView(textviewheader);

                }
                childlayout.addView(tabchildlayoutx);
                //YAxis design

                mMatrixCheckBoxList = new ArrayList<>();
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
                        checkbox.setTag(yAxisCount + "," + xAxixCount);
                        //editTextValue.setTag(String.valueOf(count)+"QTY");
                        tabchildlayouty.addView(checkbox);
                        mMatrixCheckBoxList.add(checkbox);
                    }

                    childlayout.addView(tabchildlayouty);
                }
                mCheckBoxMatrixListContainer.put(rowid, mMatrixCheckBoxList);


            } else if (type.equalsIgnoreCase("masterview") || type.equalsIgnoreCase("relationalview")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);

            } else if (type.equalsIgnoreCase("masterviewjoin")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);

            } else if (type.equalsIgnoreCase("checkbox")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);

            } else if (type.equalsIgnoreCase("dependentradio")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);

            } else if (type.equalsIgnoreCase("tableview") || type.equalsIgnoreCase("dependenttableview") || type.equalsIgnoreCase("multileveltableview")) {
                if (!mIsItemZeroTableView) {
                    childlayout.addView(NewtextView(rowid));
                    AddNewButton(displayname, String.valueOf(rowid));

                } else {
                    boolean isCurrentTableViewDependantOnFirstTableView = false;
                    mSurveyTableViewList = mAceDnsDatabase.GetSurveyTableView();
                    if (!mSurveyTableViewList.isEmpty()) {

                        for (int x = 0; x < mSurveyTableViewList.size(); x++) {
                            SurveyTableView obj = mSurveyTableViewList.get(x);
                            if (obj.getRowId().equalsIgnoreCase(rowid)) {
                                if (obj.getDependentOn().equalsIgnoreCase(ItemZeroTableViewRowId)) {
                                    isCurrentTableViewDependantOnFirstTableView = true;
                                    break;
                                }
                            }
                        }

                    }
                    if (!isCurrentTableViewDependantOnFirstTableView) {
                        childlayout.addView(NewtextView(rowid));
                        AddNewButton(displayname, String.valueOf(rowid));

                    } else {
                        childlayout.addView(NewtextViewWithGoneStatus(rowid));
                        AddNewButtonWithGoneStatus(displayname, rowid);
                        tableViewListWithGoneStatus.add(rowid);
                    }
                }
                mSurveyDetails.setTableName(tablename);
                if (count == 0) {
                    mIsItemZeroTableView = true;
                    ItemZeroTableViewRowId = rowid;
                }
                mIsTableView = true;

            } else if (type.equalsIgnoreCase("tableviewtableview")) {
                if (!mIsItemZeroTableView) {
                    childlayout.addView(NewtextView(rowid));
                    AddNewButton(displayname, String.valueOf(rowid));

                } else {
                    boolean isCurrentTableViewDependantOnFirstTableView = false;
                    mSurveyTableViewList = mAceDnsDatabase.GetSurveyTableView();
                    if (!mSurveyTableViewList.isEmpty()) {

                        for (int x = 0; x < mSurveyTableViewList.size(); x++) {
                            SurveyTableView obj = mSurveyTableViewList.get(x);
                            if (obj.getRowId().equalsIgnoreCase(rowid)) {
                                if (obj.getDependentOn().equalsIgnoreCase(ItemZeroTableViewRowId)) {
                                    isCurrentTableViewDependantOnFirstTableView = true;
                                    break;
                                }
                            }
                        }

                    }
                    if (!isCurrentTableViewDependantOnFirstTableView) {
                        childlayout.addView(NewtextView(rowid));
                        AddNewButton(displayname, String.valueOf(rowid));

                    } else {
                        childlayout.addView(NewtextViewWithGoneStatus(rowid));
                        AddNewButtonWithGoneStatus(displayname, rowid);
                        tableViewListWithGoneStatus.add(rowid);
                    }
                }
                mSurveyDetails.setTableName(tablename);
                if (count == 0) {
                    mIsItemZeroTableView = true;
                    ItemZeroTableViewRowId = rowid;
                }
                mIsTableView = true;

            } else if (CheckColonPresentInType(type)) {
                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(Html.fromHtml(displayname));
                textviewdisplayname.setTextColor(Color.BLUE);
                mParentLayout.addView(textviewdisplayname);

                RadioGroup rgp = new RadioGroup(this);
                rgp.setOrientation(RadioGroup.HORIZONTAL);
                RadioGroup.LayoutParams rprms;
                for (int i = 0; i < 2; i++) {
                    radioButton = new RadioButton(this);
                    if (i == 0) {
                        radioButton.setText(boolStringType1);
                        radioButton.setTag(rowid);
                    } else {
                        radioButton.setText(boolStringType2);
                        radioButton.setTag(rowid);
                    }
                    radioButton.setOnClickListener(this);
                    radioButton.setTextColor(Color.BLUE);
                    rprms = new RadioGroup.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT);
                    rgp.addView(radioButton, rprms);
                }
                childlayout.addView(rgp);
                childlayout.addView(NewtextView(rowid));
            } else if (type.equalsIgnoreCase("layer")) {
                mTitleText.setText(Html.fromHtml(displayname));
                mIsTittle = true;
            } else if (type.equalsIgnoreCase("double")) {
                String hinttext = "";
                if (displayname.contains("#")) {
                    String[] splitdisp = displayname.split("#");
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
                childlayout.addView(NewtextViewHeading(rowid, displayname, 13f));
                childlayout.addView(AddEditText(rowid, "double", hinttext, action, validation));
                childlayout.addView(AddEmptyViewUnderEditText());
            } else if (type.equalsIgnoreCase("checkinview")) {
                String hinttext = "";
                mPredefinedValue = PreferenceData.getCheckInOutEmpName(mContext);
                childlayout.addView(AddEditText(rowid, "text", hinttext));
                childlayout.addView(AddEmptyViewUnderEditText());
            } else if (type.equalsIgnoreCase("rating")) {
                TextView textviewdisplayname = new TextView(this);
                if (displayname.contains("#")) {
                    String[] splitdisp = displayname.split("#");
                    StringBuilder disp = new StringBuilder();
                    for (String s : splitdisp) {
                        disp.append(s).append("\n");
                    }
                    textviewdisplayname.setText(disp.toString());
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
            } else if (type.equalsIgnoreCase("menu")) {
                Log.d("TAG", "DrawLayout: No need MENU");
            } else if (type.equalsIgnoreCase("tickbox")) {
                CheckBox checkbox = new CheckBox(this);
                checkbox.setText(Html.fromHtml(displayname));
                checkbox.setTag(rowid);
                checkbox.setOnClickListener(this);
                checkbox.setTextColor(Color.BLUE);
                mCheckBoxList.add(checkbox);
                mParentLayout.addView(checkbox);
                // No need to draw
            } else if (type.equalsIgnoreCase("conditonalview")) {
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
                childlayout.addView(NewtextViewHeading(rowid, displayname, 13f));
                childlayout.addView(AddEditTextDisabled(rowid, "text", hinttext));
                childlayout.addView(AddEmptyViewUnderEditText());
            } else {
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
                childlayout.addView(AddEditText(rowid, "text", hinttext, action, validation));
                childlayout.addView(AddEmptyViewUnderEditText());
            }
            mParentLayout.addView(childlayout);

            if (!type.equalsIgnoreCase("layer") && !type.equalsIgnoreCase("menu")) {
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

    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */
    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(SurveyActivity.this) + "~" + Utils.getDBVersion(SurveyActivity.this));
        mParentLayout = findViewById(R.id.linearLayoutParent);
        mButtonUndo = findViewById(R.id.btn_undo);
        mButtonBack = findViewById(R.id.back);
        mButtonSubmit = findViewById(R.id.btn_Submi);
        mTitleText = findViewById(R.id.textViewTitle);
        mButtonUndo.setVisibility(View.INVISIBLE);
    }

    public void PrepareSurveyData(final int task, final String params) {
        mPrepareSurveyProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSurveyProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {

                    case 1:
                        Log.d("TAG", "PrepareSurveyData: 1");
                        if (Constants.surveyFormDetailsObj.getSurveyMenu().equalsIgnoreCase("yes") && Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(Constants.mSurveyMainType)) {
                            if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                                Log.d("TAG", "PrepareSurveyData: 1-1");
                                mSurveyInputList = mAceDnsDatabase.GetSurveyInputLayoutWise(params, mMenuID);
                            } else {
                                Log.d("TAG", "PrepareSurveyData: 1-2");
                                mSurveyInputList = mAceDnsDatabase.GetSurveyInputSubMenuWise(mSubMenu, mMenuID);
                            }
                        } else {
                            if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                                Log.d("TAG", "PrepareSurveyData: 1-3");
                                mSurveyInputList = mAceDnsDatabase.GetSurveyInputLayoutWise(params);
                            } else {
                                if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                                    Log.d("TAG", "PrepareSurveyData: 1-4");
                                    mSurveyInputList = mAceDnsDatabase.GetSurveyInputSubMenuWise(mSubMenu);
                                } else {
                                    Log.d("TAG", "PrepareSurveyData: 1-5");
                                    mSurveyInputList = mAceDnsDatabase.GetSurveyInputLayoutWise();
                                }
                            }
                        }
                        mUndoSurveyList = mAceDnsDatabase.GetSurveyTempOutput(params);
                        break;
                    case 2:
                        Log.d("TAG", "PrepareSurveyData: 2");
                        if (isCurrentCheckBoxItemDependant) {
                            Log.d("TAG", "PrepareSurveyData: 2-1");
                            mDependentRowId = mFinalRowID;
                            getDependantValueWithRowName();
                            isCurrentCheckBoxItemDependant = false;
                            if (!isDependangtDataEmpty && !mCondition.trim().isEmpty()) {
                                Log.d("TAG", "PrepareSurveyData: 2-2");
                                int max = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mTableName, mColumnName, mShowColumn, mDependent, mCondition);
                                values = new String[max];
                                System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                            }
                        } else if (mCategory && !mSubCategory) {
                            Log.d("TAG", "PrepareSurveyData: 2-3");
                            int max = mAceDnsDatabase.Get_Survey_Master_Table_Category_Details(mTableName, mColumnName, mShowColumn);
                            values = new String[max];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        } else if (!mCategory && mSubCategory) {
                            Log.d("TAG", "PrepareSurveyData: 2-4");
                            mDependentRowId = mFinalRowID;
                            if (!mCondition.trim().isEmpty()) {
                                Log.d("TAG", "PrepareSurveyData: 2-5");
                                int max = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mTableName, mColumnName, mShowColumn, mDependent, mCondition);
                                values = new String[max];
                                System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                            }
                        } else {
                            Log.d("TAG", "PrepareSurveyData: 2-6");
                            int max = mAceDnsDatabase.GetSurveyTableDetails(params, dependantConditionSqlQueryForRadioType);
                            values = new String[max];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        }
                        break;

                    case 3:
                        Log.d("TAG", "PrepareSurveyData: 3");
                        mMallSurveyRelationList = mAceDnsDatabase.GetMallSurveyRelation(mMenuID, mSurveyType);
                        break;

                    case 4:
                        Log.d("TAG", "PrepareSurveyData: 4");
                        int max = mAceDnsDatabase.GetSurveyTableDetails(params, "");
                        values = new String[max];
                        System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        break;

                    case 5:
                        Log.d("TAG", "PrepareSurveyData: 5");
                        if (!mCondition.trim().isEmpty()) {
                            Log.d("TAG", "PrepareSurveyData: 5-1");
                            int maxx = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mSubTableName, mSubColumnName, mSubDependent, mCondition);
                            subvalues = new String[maxx];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, subvalues, 0, Constants.mSurveyLayoutList.length);
                        }
                        break;

                    case 6:
                        Log.d("TAG", "PrepareSurveyData: 6: "+mTableName);
                        Log.d("TAG", "PrepareSurveyData: 6: "+mColumnName);
                        Log.d("TAG", "PrepareSurveyData: 6: "+mCustomerSelectionBasisFilter);
                        int maxx = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase6(mTableName, mColumnName, mCustomerSelectionBasisFilter);
                        if (maxx > 0) {
                            Log.d("TAG", "PrepareSurveyData: 6-1");
                            values = new String[maxx];
                            Log.d("TAG", "PrepareSurveyData: 6: "+values);
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        } else {
                            Log.d("TAG", "PrepareSurveyData: 6-2");
                            values = null;
                        }
                        break;

                    case 7:
                        Log.d("TAG", "PrepareSurveyData: 7: "+mTableName);
                        Log.d("TAG", "PrepareSurveyData: 7: "+mSendColumn);
                        Log.d("TAG", "PrepareSurveyData: 7: "+mShowColumn);
                        Log.d("TAG", "PrepareSurveyData: 7: "+mCustomerSelectionBasis);
                        Log.d("TAG", "PrepareSurveyData: 7: "+mCustomerSelectionBasisFilter);
                        mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7(mTableName, mSendColumn, mShowColumn, mCustomerSelectionBasis, mCustomerSelectionBasisFilter);
                        break;

                    case 8:
                        Log.d("TAG", "PrepareSurveyData: 8");
                        int catcount = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsClause(mTableName, mColumnName, mWhereClause);
                        mWhereClause = "";
                        if (catcount > 0) {
                            Log.d("TAG", "PrepareSurveyData: 8-1");
                            values = new String[catcount];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        } else {
                            Log.d("TAG", "PrepareSurveyData: 8-2");
                            values = null;
                        }
                        break;
                    case 9:
                        Log.d("TAG", "PrepareSurveyData: 9");
                        mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCondition(mTableName, mSendColumn, mShowColumn, mWhereClause);
                        mWhereClause = "";
                        break;

                    case 10:
                        Log.d("TAG", "PrepareSurveyData: 10");
                        String floor = Constants.selectedMallMaster.getFloor().trim();

                        if (!floor.isEmpty()) {
                            Log.d("TAG", "PrepareSurveyData: 10-1");
                            if (floor.contains(";")) {
                                Log.d("TAG", "PrepareSurveyData: 10-2");
                                values = floor.split(";");
                            } else {
                                Log.d("TAG", "PrepareSurveyData: 10-3");
                                values = new String[1];
                                values[0] = floor;
                            }
                        }
                        break;
                    case 11:
                        Log.d("TAG", "PrepareSurveyData: 11");
                        mKeyValueList = mAceDnsDatabase.GetMasterTableDetailsRelationalView(params);
                        break;

                    case 12:
                        Log.d("TAG", "PrepareSurveyData: 12");
                        mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase12(mTableName, mSendColumn, mShowColumn, mCustomerSelectionBasis, mCustomerSelectionBasisFilter);
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
            if (!value.isEmpty()) {


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

    @Override
    public void onResume() {
        super.onResume();
    }

    @SuppressLint("UseCompatLoadingForDrawables")
    @SuppressWarnings("deprecation")
    public void AddNewButton(String displayname, String id) {
        Log.d("TAG", "__BUTTON__ AddNewButton: " + displayname + "\n" + id);
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
        if (!mIsButtonEnable) {
            button.setEnabled(false);
            mIsButtonEnable = true;
        }
        button.setHorizontallyScrolling(true);
        button.setGravity(Gravity.CENTER);
        button.setText(Html.fromHtml(displayname));
        button.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
        button.setSingleLine(false);
        button.setOnClickListener(this);
        buttonLayout.addView(button);
        mParentLayout.addView(buttonLayout);
        mButtonList.add(button);
    }

    @SuppressLint("UseCompatLoadingForDrawables")
    public void AddNewButtonWithGoneStatus(String displayname, String id) {
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
        if (!mIsButtonEnable) {
            button.setEnabled(false);
            mIsButtonEnable = true;
        }
        button.setHorizontallyScrolling(true);
        button.setGravity(Gravity.CENTER);
        button.setText(Html.fromHtml(displayname));
        button.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
        button.setSingleLine(false);
        button.setVisibility(GONE);
        button.setOnClickListener(this);
        buttonLayout.addView(button);
        mParentLayout.addView(buttonLayout);
        mButtonList.add(button);
    }

    @SuppressLint("SetTextI18n")
    public void ShowSingelDualList() {
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.select_multiple_from_list);
        grpDialog.setCancelable(false);
        TextView title = grpDialog.findViewById(R.id.title);

        currentTableHeader = "an option A";

        title.setText("Please select " + currentTableHeader);

        final ListView List = grpDialog.findViewById(R.id.list);
        ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_masterview, values);
        List.setAdapter(adapter);

        List.setOnItemClickListener((parent, view, position, id) -> grpDialog.cancel());
        grpDialog.show();

    }

    @SuppressLint({"SetTextI18n", "CutPasteId"})
    public void ShowList(final String type, final String checksaving) {
        if (mKeyValueList.size() == 1) {
            if (checksaving.equalsIgnoreCase("SAVE")) {
                SetTextViewText(mFinalRowID, mKeyValueList.get(0).getValue());
                SetSurveyValue(mFinalRowID, mKeyValueList.get(0).getKey());
            } else {
                if (mKeyValueSubList != null) {
                    for (int count = 0; count < mKeyValueSubList.size(); count++) {
                        if (mSubActionTag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {
                            mKeyValueSubList.get(count).setValue(mKeyValueList.get(0).getKey());
                            mKeyValueSubList.get(count).setEnteredValue(mKeyValueList.get(0).getValue());
                            break;
                        }
                    }
                }
            }
        } else if (mKeyValueList.size() > 1) {
            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_multiple_from_list);

            grpDialog.setCancelable(false);
            TextView title = grpDialog.findViewById(R.id.title);
            TextView autoCompleteTextView1 = grpDialog.findViewById(R.id.autoCompleteTextView1);
            ImageView image_cancel = grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(v -> grpDialog.dismiss());
            if (currentItemDisplayName.matches("")) {
                currentItemDisplayName = "an option B";
            }
            title.setText("Please select " + currentItemDisplayName);
            autoCompleteTextView1.setHint("Type Here to Search");

            final ListView List = grpDialog.findViewById(R.id.list);
            KeyValueCheckAdapter adapter;
            KeyValueNormalAdapter adapter1;
            final ArrayList<KeyValue> mKeyValueList_Search = new ArrayList<>();
            for (int i = 0; i < mKeyValueList.size(); i++) {
                mKeyValueList_Search.add(new KeyValue(mKeyValueList.get(i).getValue(), mKeyValueList.get(i).getKey(), mKeyValueList.get(i).getType()));
            }
            if (type.equalsIgnoreCase("checkbox")) {
                List.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
                adapter = new KeyValueCheckAdapter(this, R.layout.activity_check_list, mKeyValueList);
                List.setAdapter(adapter);
            } else {
                final EditText autoCompleteTextView1OBJ = grpDialog.findViewById(R.id.autoCompleteTextView1);
                autoCompleteTextView1OBJ.setVisibility(View.VISIBLE);

                adapter1 = new KeyValueNormalAdapter(this, R.layout.activity_listview, mKeyValueList_Search);
                List.setAdapter(adapter1);


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
            RelativeLayout chkAllLayout = grpDialog.findViewById(R.id.select_all_layout);
            if (type.equalsIgnoreCase("checkbox")) {
                chkAllLayout.setVisibility(View.VISIBLE);
            }

            final CheckBox chkSelectAll = grpDialog.findViewById(R.id.chk_all);
            chkSelectAll.setOnClickListener(v -> {
                if (chkSelectAll.isChecked()) {
                    for (int i = 0; i < List.getCount(); i++) {
                        List.setItemChecked(i, true);
                    }
                } else {
                    for (int i = 0; i < List.getCount(); i++) {
                        List.setItemChecked(i, false);
                    }
                }
            });

            List.setOnItemClickListener((parent, view, position, id) -> {
                if (!type.equalsIgnoreCase("checkbox")) {
                    KeyValue obj = mKeyValueList_Search.get(position);
                    String value = obj.getValue();
                    String key = obj.getKey();
                    if (checksaving.equalsIgnoreCase("SAVE")) {
                        if (shouldUpdateCustomerMasterWithEmail) {//show customer email and ph number if present
                            if (mFinalRowID.matches("RA001")) {
                                try {
                                    CustomerDetails custDetails = mAceDnsDatabase.getCustomerDetailsByCode(key);
                                    String email = custDetails.getEmail().trim();
                                    if (!email.isEmpty()) {
                                        SetEditTextText("RA002", email);//email
                                        SetTextViewText("RA002", email);//email
                                    } else {
                                        SetEditTextText("RA002", "");//email
                                        SetTextViewText("RA002", "");//email
                                    }

                                    String number = custDetails.getNumber().trim();
                                    if (!number.isEmpty()) {
                                        SetEditTextText("RA003", number);//ph
                                        SetSurveyValue("RA003", number);//ph
                                    } else {
                                        SetEditTextText("RA003", "");//ph
                                        SetSurveyValue("RA003", "");//ph
                                    }
                                } catch (Exception e) {
                                    Log.d("TAG", "ShowList: " + e.getMessage());
                                }
                            }
                        }
                        SetTextViewText(mFinalRowID, value);
                        if (mFinalRowID.equalsIgnoreCase("RA822")) {
                            String fKey = mAceDnsDatabase.getCustDnsCode(key);
                            SetTextViewText(mFinalRowID, value + "(" + fKey + ")");
                        }
                        if (mFinalRowID.equalsIgnoreCase("RA414")) {
                            SetTextViewText(mFinalRowID, value + ";" + key);
                        } else if (SetTextViewTextDealer(mFinalRowID, value, key)) {
                            String fKey = mAceDnsDatabase.getCustDnsCode(key);
                            SetTextViewText(mFinalRowID, value + ";" + fKey);
                        }
                        if (obj.getType().equalsIgnoreCase("imageview")) {
                            SetSurveyValue(mFinalRowID, value);
                        } else {
                            SetSurveyValue(mFinalRowID, key);
                        }

                        ifSomeOtherMasterViewDependantOnThisViewThenResetThem(mFinalRowID);
                    } else {
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
                    grpDialog.cancel();
                    if (ismultipleMasterViewInSubACtion) {
                        ismultipleMasterViewInSubACtion = false;
                        mSubActionTag = masterViewListForSubACtion.get(1).getItem0();
                        onSubActionLayoutButtonClickForMasterView(Integer.parseInt(masterViewListForSubACtion.get(1).getItem1()), surveyInputListForSubActionMasterView);
                    }
                }
            });

            Button submit = grpDialog.findViewById(R.id.button1);
            if (type.equalsIgnoreCase("radio")) {
                submit.setVisibility(View.GONE);
            }

            submit.setOnClickListener(arg0 -> {
                if (type.equalsIgnoreCase("checkbox")) {
                    KeyValue obj;
                    StringBuilder key = new StringBuilder();
                    StringBuilder value = new StringBuilder();
                    final SparseBooleanArray checkedItems = List.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            obj = mKeyValueList.get(position);
                            key.append(obj.getKey()).append(";");
                            value.append(obj.getValue()).append(";");
                        }
                        if (checksaving.equalsIgnoreCase("SAVE")) {
                            SetTextViewText(mFinalRowID, value.toString());
                            SetSurveyValue(mFinalRowID, key.toString());
                        } else {
                            if (mKeyValueSubList != null) {
                                for (int count = 0; count < mKeyValueSubList.size(); count++) {
                                    if (mSubActionTag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {
                                        mKeyValueSubList.get(count).setValue(key.toString());
                                        mKeyValueSubList.get(count).setEnteredValue(value.toString());
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
            });
            grpDialog.show();
        }
    }

    @SuppressLint({"SetTextI18n", "CutPasteId"})
    public void ShowListWithInput(String hint, String validation) {
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.select_multiple_from_list);

        grpDialog.setCancelable(false);
        TextView title = grpDialog.findViewById(R.id.title);
        TextView autoCompleteTextView1 = grpDialog.findViewById(R.id.autoCompleteTextView1);
        ImageView image_cancel = grpDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(GONE);
        image_cancel.setOnClickListener(v -> grpDialog.dismiss());
        title.setText(HtmlCompat.fromHtml("Please provide input for\n <font color='#D7B56D'>" + hint + "</font>", HtmlCompat.FROM_HTML_MODE_LEGACY));
        autoCompleteTextView1.setHint("Type Here to Search");

        final ListView List = grpDialog.findViewById(R.id.list);
        ProductMasterWithQtyInputAdapter adapter1;
        ArrayList<KeyValue> mKeyValueList_SearchWithInput = new ArrayList<>();
        for (int i = 0; i < mKeyValueList.size(); i++) {
            mKeyValueList_SearchWithInput.add(new KeyValue(mKeyValueList.get(i).getValue(), mKeyValueList.get(i).getKey(), mKeyValueList.get(i).getType()));
        }

        final EditText autoCompleteTextView1OBJ = grpDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1OBJ.setVisibility(View.VISIBLE);

        adapter1 = new ProductMasterWithQtyInputAdapter(this, R.layout.list_item_with_input, mKeyValueList_SearchWithInput, hint);
        List.setAdapter(adapter1);


        final ProductMasterWithQtyInputAdapter finalAdapter = adapter1;
        autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {
            public void afterTextChanged(Editable s) {
            }

            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            public void onTextChanged(CharSequence s, int start, int before, int count) {
                finalAdapter.filter(s.toString());
            }
        });


        List.setOnItemClickListener((parent, view, position, id) -> {
        });

        Button submit = grpDialog.findViewById(R.id.button1);

        submit.setOnClickListener(arg0 -> {
            boolean isInputGiven = false;
            StringBuilder value = new StringBuilder();
            int itotalIputGiven = 0;
            for (int i = 0; i < mKeyValueList_SearchWithInput.size(); i++) {
                KeyValue currentObj = mKeyValueList_SearchWithInput.get(i);
                if (Utils.isNumeric(currentObj.getEnteredValue()) && Double.parseDouble(currentObj.getEnteredValue()) > 0) {
                    isInputGiven = true;
                    if (value.toString().matches("")) {
                        value = new StringBuilder(currentObj.getValue() + "#" + currentObj.getEnteredValue());
                    } else {
                        value.append("@").append(currentObj.getValue()).append("#").append(currentObj.getEnteredValue());
                    }
                    itotalIputGiven++;
                }
            }
            if (isInputGiven) {
                if (Utils.isNumeric(validation)) {
                    if (itotalIputGiven > Double.parseDouble(validation)) {
                        Utils.showToast(mContext, "Maximum number of input given is " + validation + ".");
                        return;
                    }
                }
                value.insert(0, "YES:");

                SetSurveyValue(mFinalRowID, value.toString());
                SetTextViewText(mFinalRowID, value.toString());
                grpDialog.cancel();
            } else {
                Utils.showToast(mContext, "You have to provide input for at least one " + hint + ".");
            }
        });
        grpDialog.show();
    }

    public static class ProductMasterWithQtyInputAdapter extends BaseAdapter {

        private final int resourceId;
        Context mContext;

        private final ArrayList<KeyValue> al_Search_data;
        AceDnsDatabase mAceDnsDatabase;
        int ShowColumnCount = 3;
        String hint = "";

        public ProductMasterWithQtyInputAdapter(Context context, int resourceId, ArrayList<KeyValue> al_OLD_dataOBJ, String hint) {
            this.mContext = context;
            mKeyValueList_SearchWithInput = al_OLD_dataOBJ;
            this.hint = hint;
            this.resourceId = resourceId;
            this.al_Search_data = new ArrayList<>();
            this.al_Search_data.addAll(mKeyValueList_SearchWithInput);
            mAceDnsDatabase = new AceDnsDatabase(context);
        }

        @Override
        public KeyValue getItem(int position) {
            return mKeyValueList_SearchWithInput.get(position);
        }

        @Override
        public int getCount() {
            return mKeyValueList_SearchWithInput.size();
        }

        @Override
        public long getItemId(int position) {
            return position;
        }

        @SuppressLint("ViewHolder")
        @Override
        public View getView(int position, View convertView, ViewGroup parent) {
            LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            ViewHolder viewHolder = new ViewHolder();
            viewHolder.txtView = convertView.findViewById(R.id.label);
            viewHolder.qtyET = convertView.findViewById(R.id.etProdQty);
            viewHolder.invisibleTVProdCode = convertView.findViewById(R.id.invisibleTVProdCode);
            viewHolder.qtyET.setHint(hint);
            convertView.setTag(viewHolder);
            viewHolder.qtyET.addTextChangedListener(new GenericTextWatcherQty(position));
            String value = mKeyValueList_SearchWithInput.get(position).getValue();
            viewHolder.txtView.setText(value);
            String currentqty = mKeyValueList_SearchWithInput.get(position).getEnteredValue();
            if (Utils.isNumeric(currentqty) && Double.parseDouble(currentqty) > 0)
                viewHolder.qtyET.setText(currentqty);
            return convertView;
        }

        private record GenericTextWatcherQty(int pos) implements TextWatcher {

            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
            }

            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
            }

            public void afterTextChanged(Editable editable) {
                String text = editable.toString();
                mKeyValueList_SearchWithInput.get(pos).setEnteredValue(text);
            }
        }

        // Filter Class
        public void filter(String charText) {
            mKeyValueList_SearchWithInput.clear();
            if (charText.isEmpty()) {
                mKeyValueList_SearchWithInput.addAll(al_Search_data);
            } else {
                for (KeyValue wp : al_Search_data) {
                    String searchItem = wp.getValue().toLowerCase(Locale.getDefault());
                    if (ShowColumnCount == 4) {
                        searchItem = searchItem + ", " + wp.getmShowColumn1().toLowerCase(Locale.getDefault());
                    } else if (ShowColumnCount == 5) {
                        searchItem = searchItem + ", " + wp.getmShowColumn1().toLowerCase(Locale.getDefault()) + ", " + wp.getmShowColumn1().toLowerCase(Locale.getDefault());
                    }
                    if (searchItem.contains(charText)) {
                        mKeyValueList_SearchWithInput.add(wp);
                    }
                }
            }
            notifyDataSetChanged();
        }

        public static class ViewHolder {
            TextView txtView, invisibleTVProdCode;
            EditText qtyET;
        }
    }

    private void ifSomeOtherMasterViewDependantOnThisViewThenResetThem(String rowIdOnWhichDependant) {
        for (int count = 0; count < mSurveyInputList.size(); count++) {

            SurveyInput surveyInputList = mSurveyInputList.get(count);
            String type = surveyInputList.getSurveyType();

            if (type.equalsIgnoreCase("masterview")) {
                String rowid = surveyInputList.getSurveyRowId();
                String tablename = surveyInputList.getSurveyTableName();
                if (tablename.contains("#")) {
                    String[] splitablename = tablename.split("#");
                    if (splitablename.length == 3 || splitablename.length == 4 || splitablename.length == 5) {
                        mTableName = splitablename[0];
                        mColumnName = splitablename[1];
                        mType = splitablename[2];
                        if (splitablename.length == 4) {
                            mCustomerSelectionBasis = splitablename[3];
                        }

                        if (splitablename.length == 5) {
                            String dataFetchLogic = splitablename[4];
                            if (dataFetchLogic.contains("&")) {
                                String[] dataFetchLogicArray = dataFetchLogic.split("&");
                                for (String s : dataFetchLogicArray) {
                                    String[] mCustomerSelectionBasisFilterArray = s.split(";");
                                    if (mCustomerSelectionBasisFilterArray.length == 2) {
                                        String dataSelectionLogicValue = mCustomerSelectionBasisFilterArray[1];

                                        if (dataSelectionLogicValue.contains("row_id-")) {
                                            String dependingOnRowId = dataSelectionLogicValue.split("-")[1];
                                            if (dependingOnRowId.matches(rowIdOnWhichDependant)) {
                                                SetTextViewText(rowid, "");
                                                SetSurveyValue(rowid, "");
                                            }
                                        } else {
                                            this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCustomerSelectionBasisFilterArray[1] + "' ";
                                        }
                                    }
                                }
                            } else {
                                String[] mCustomerSelectionBasisFilterArray = dataFetchLogic.split(";");
                                if (mCustomerSelectionBasisFilterArray.length == 2) {
                                    String dataSelectionLogicValue = mCustomerSelectionBasisFilterArray[1];
                                    if (dataSelectionLogicValue.contains("row_id-")) {
                                        String dependingOnRowId = dataSelectionLogicValue.split("-")[1];
                                        if (dependingOnRowId.matches(rowIdOnWhichDependant)) {
                                            SetTextViewText(rowid, "");
                                            SetSurveyValue(rowid, "");
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowListDependentViewClick(final String type) {
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.select_multiple_from_list);
        grpDialog.setCancelable(false);
        TextView title = grpDialog.findViewById(R.id.title);
        final EditText autoCompleteTextView1OBJ = grpDialog.findViewById(R.id.autoCompleteTextView1);
        if (currentItemDisplayName.matches("")) {
            currentItemDisplayName = "an option C";
        }
        title.setText("Please select " + currentItemDisplayName);

        final ListView List = grpDialog.findViewById(R.id.list);
        KeyValueCheckAdapter adapter;

        final ArrayList<KeyValue> mKeyValueList_Search = new ArrayList<>(mKeyValueList);

        if (type.equalsIgnoreCase("checkbox")) {
            List.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            adapter = new KeyValueCheckAdapter(this, R.layout.activity_check_list, mKeyValueList_Search);
            List.setAdapter(adapter);
        } else {
            final KeyValueNormalAdapter adapter1;
            adapter1 = new KeyValueNormalAdapter(this, R.layout.activity_listview, mKeyValueList_Search);
            List.setAdapter(adapter1);
            autoCompleteTextView1OBJ.setVisibility(View.VISIBLE);
            autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {

                public void afterTextChanged(Editable s) {

                }

                public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                }

                public void onTextChanged(CharSequence charText, int start, int before, int count) {

                    mKeyValueList_Search.clear();
                    String text = charText.toString().toLowerCase(Locale.getDefault());  // FIX

                    if (text.isEmpty()) {
                        mKeyValueList_Search.addAll(mKeyValueList);
                    } else {
                        for (KeyValue wp : mKeyValueList) {
                            if (wp.getValue().toLowerCase(Locale.getDefault()).contains(text)) {
                                mKeyValueList_Search.add(wp);
                            }
                        }
                    }

                    adapter1.notifyDataSetChanged();

                }
            });

        }
        RelativeLayout chkAllLayout = grpDialog.findViewById(R.id.select_all_layout);
        if (type.equalsIgnoreCase("checkbox")) {
            chkAllLayout.setVisibility(View.VISIBLE);
        }

        final CheckBox chkSelectAll = grpDialog.findViewById(R.id.chk_all);
        chkSelectAll.setOnClickListener(v -> {
            if (chkSelectAll.isChecked()) {
                for (int i = 0; i < List.getCount(); i++) {
                    List.setItemChecked(i, true);
                }
            } else {
                for (int i = 0; i < List.getCount(); i++) {
                    List.setItemChecked(i, false);
                }
            }
        });


        List.setOnItemClickListener((parent, view, position, id) -> {
            if (!type.equalsIgnoreCase("checkbox")) {
                HideSoftKeyBoard(autoCompleteTextView1OBJ);
                KeyValue obj = mKeyValueList_Search.get(position);
                if (mDecision.equalsIgnoreCase("SAVE")) {
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
        });

        Button submit = grpDialog.findViewById(R.id.button1);
        submit.setVisibility(View.GONE);

        submit.setOnClickListener(arg0 -> {
            if (type.equalsIgnoreCase("checkbox")) {
                KeyValue obj = null;
                StringBuilder key = new StringBuilder();
                StringBuilder value = new StringBuilder();
                final SparseBooleanArray checkedItems = List.getCheckedItemPositions();
                int checkedItemsCount = checkedItems.size();
                if (checkedItemsCount > 0) {
                    for (int i = 0; i < checkedItemsCount; ++i) {
                        int position = checkedItems.keyAt(i);
                        obj = mKeyValueList.get(position);
                        key.append(obj.getKey()).append(";");
                        value.append(obj.getValue()).append(";");
                    }
                    if (mDecision.equalsIgnoreCase("SAVE")) {
                        SetTextViewText(mFinalRowID, value.toString());
                        SetSurveyValue(mFinalRowID, key.toString());
                    } else {
                        if (mKeyValueSubList != null) {
                            for (int count = 0; count < mKeyValueSubList.size(); count++) {
                                if (mSubActionTag.equalsIgnoreCase(mKeyValueSubList.get(count).getKey())) {
                                    mKeyValueSubList.get(count).setValue(key.toString());
                                    mKeyValueSubList.get(count).setEnteredValue(value.toString());
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
        });
        grpDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowList(final String type) {
        final ArrayList<String> al_rootMain = new ArrayList<>();
        final ArrayList<String> al_rootNAMESearch = new ArrayList<>();
        if (values.length == 1 && !type.equalsIgnoreCase("date") && !type.equalsIgnoreCase("double") && !type.equalsIgnoreCase("")) {
            String val = values[0];
            SetTextViewText(mFinalRowID, val);
            SetSurveyValue(mFinalRowID, val);
        }
        else {
            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_multiple_from_list);
            grpDialog.setCancelable(false);
            currentTableHeader = "an option D";

            TextView title = grpDialog.findViewById(R.id.title);
            title.setText("Please select " + currentTableHeader);

            ImageView image_cancel = grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(v -> grpDialog.dismiss());

            if (Constants.nickName.equalsIgnoreCase("STAR") && mFinalRowID.equalsIgnoreCase("RA176")) {
                if (values[0].equalsIgnoreCase("Yes") && values[1].equalsIgnoreCase("No")) {
                    title.setText("Please select Conversion (Yes/No)");
                }
            }
            Log.d("TAG", "__TAG__ ShowList: "+type);
            final ListView List = grpDialog.findViewById(R.id.list);
            if (type.contains("/") || type.equalsIgnoreCase("double") || type.equalsIgnoreCase("")) {
                doubleTextView = grpDialog.findViewById(R.id.doubleEditText);
                doubleTextView2 = grpDialog.findViewById(R.id.doubleEditText2);
                doubleTextView.setVisibility(View.VISIBLE);
                doubleTextView.setHint(mPredefinedValueEditTextDialog);
                List.setVisibility(View.GONE);
                if (type.contains("/")) {
                    String val1 = "", val2 = "";
                    doubleTextView2.setVisibility(View.VISIBLE);

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
            else if (type.equalsIgnoreCase("date")) {
                datePicker = grpDialog.findViewById(R.id.datePicker);
                doubleTextView = grpDialog.findViewById(R.id.doubleEditText);
                doubleTextView2 = grpDialog.findViewById(R.id.doubleEditText2);
                doubleTextView.setVisibility(View.GONE);
                datePicker.setVisibility(View.VISIBLE);
                title.setText("Please select " + mPredefinedValueEditTextDialog);
                List.setVisibility(View.GONE);

            }
            final EditText autoCompleteTextView1OBJ = grpDialog.findViewById(R.id.autoCompleteTextView1);
            if (type.equalsIgnoreCase("checkbox")) {
                List.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
                if (CheckPreDefinedData(mFinalRowID)) {
                    FillPreDefinedData(mMallColumnName);
                    mMallColumnName = "";
                    Constants.isMallSurveyRelationDataAvailable = !mPredefinedValue.isEmpty();
                }

                if (Constants.isMallSurveyRelationDataAvailable) {
                    if (mPredefinedValue.contains(",")) {
                        Constants.mGeneralFacilityList = mPredefinedValue.split(",");
                    } else {
                        Constants.mGeneralFacilityList = new String[1];
                        Constants.mGeneralFacilityList[0] = mPredefinedValue;
                    }
                }

                ArrayList<SurveyRoot> al_rootNAME = new ArrayList<>();

                for (String value : values) {
                    al_rootNAME.add(new SurveyRoot(value));
                }

                final ListSearchCheckAdapter adapterOBJ = new ListSearchCheckAdapter(this, R.layout.activity_check_list, al_rootNAME, mFinalRowID, mTextViewList);
                List.setAdapter(adapterOBJ);

                autoCompleteTextView1OBJ.setVisibility(View.VISIBLE);
                autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {

                    public void afterTextChanged(Editable s) {

                    }

                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                    }

                    public void onTextChanged(CharSequence s, int start, int before, int count) {
                        Log.d("TAG", "_DOWNLOAD_ onTextChanged: "+s.toString());
                        adapterOBJ.filter(s.toString());
                    }
                });


            }
            else {
                for (String value : values) {
                    al_rootMain.add(value);
                    al_rootNAMESearch.add(value);
                }
                final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_masterview, al_rootNAMESearch);
                List.setAdapter(adapter);
                if (!type.contains("/") && !type.equalsIgnoreCase("double") && !type.equalsIgnoreCase("date") && !type.equalsIgnoreCase("")) {
                    autoCompleteTextView1OBJ.setVisibility(View.VISIBLE);
                    autoCompleteTextView1OBJ.addTextChangedListener(new TextWatcher() {
                        public void afterTextChanged(Editable s) {

                        }

                        public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                        }

                        public void onTextChanged(CharSequence charText, int start, int before, int count) {
                            Log.d("TAG", "_DOWNLOAD_ onTextChanged 1111: "+charText.toString());
                            al_rootNAMESearch.clear();
                            if (charText.toString().isEmpty()) {
                                al_rootNAMESearch.addAll(al_rootMain);
                            } else {
                                for (String wp : al_rootMain) {
                                    if (wp.toLowerCase()
                                            .contains(charText.toString())) {
                                        al_rootNAMESearch.add(wp);
                                    }
                                }
                            }
                            adapter.notifyDataSetChanged();
                        }
                    });
                }
            }
            RelativeLayout chkAllLayout = grpDialog.findViewById(R.id.select_all_layout);
            if (type.equalsIgnoreCase("checkbox")) {
                chkAllLayout.setVisibility(View.VISIBLE);
            }

            if (type.equalsIgnoreCase("checkbox")) {
                if (Constants.isMallSurveyRelationDataAvailable) {
                    String menuItem = "";
                    for (int i = 0; i < List.getCount(); i++) {
                        Object currentObject = List.getItemAtPosition(i);
                        if (currentObject instanceof SurveyRoot) {
                            SurveyRoot rootObject = (SurveyRoot) List.getItemAtPosition(i);
                            menuItem = rootObject.getName();
                        } else {
                            menuItem = (String) List.getItemAtPosition(i);
                        }
                        for (int count = 0; count < Constants.mGeneralFacilityList.length; count++) {
                            if (menuItem.equalsIgnoreCase(Constants.mGeneralFacilityList[count])) {
                                List.setItemChecked(i, true);
                            }
                        }
                    }
                }
            }

            final CheckBox chkSelectAll = grpDialog.findViewById(R.id.chk_all);
            chkSelectAll.setOnClickListener(v -> {
                if (chkSelectAll.isChecked()) {
                    for (int i = 0; i < List.getCount(); i++) {
                        List.setItemChecked(i, true);
                    }
                } else {
                    for (int i = 0; i < List.getCount(); i++) {
                        List.setItemChecked(i, false);
                    }
                }
            });


            List.setOnItemClickListener((parent, view, position, id) -> {
                HideSoftKeyBoard(autoCompleteTextView1OBJ);

                if (!type.equalsIgnoreCase("checkbox")) {
                    String val = al_rootNAMESearch.get(position).trim();
                    if (mIsItemZeroTableView && ItemZeroTableViewRowId.equalsIgnoreCase(mFinalRowID)) {
                        for (int i = 0; i < tableViewListWithGoneStatus.size(); i++) {
                            String currentRowIdWithGoneStatus = tableViewListWithGoneStatus.get(i);
                            for (int y = 0; y < mTextViewList.size(); y++) {
                                TextView currentTV = mTextViewList.get(y);
                                if (currentTV.getTag().equals(currentRowIdWithGoneStatus)) {
                                    currentTV.setVisibility(View.GONE);
                                    break;
                                }
                            }
                            for (int z = 0; z < mButtonList.size(); z++) {
                                Button currentButton = mButtonList.get(z);
                                if (currentButton.getTag().equals(currentRowIdWithGoneStatus)) {
                                    currentButton.setVisibility(View.GONE);
                                    break;
                                }
                            }

                        }
                        for (int i = 0; i < tableViewListWithGoneStatus.size(); i++) {
                            String currentRowIdWithGoneStatus = tableViewListWithGoneStatus.get(i);

                            for (int x = 0; x < mSurveyTableViewList.size(); x++) {
                                SurveyTableView obj = mSurveyTableViewList.get(x);
                                if (obj.getRowId().equalsIgnoreCase(currentRowIdWithGoneStatus) && obj.getDependentOn().equalsIgnoreCase(ItemZeroTableViewRowId) && obj.getDependentValue().equalsIgnoreCase(val)) {
                                    for (int y = 0; y < mTextViewList.size(); y++) {
                                        TextView currentTV = mTextViewList.get(y);
                                        if (currentTV.getTag().equals(currentRowIdWithGoneStatus)) {
                                            currentTV.setVisibility(View.VISIBLE);
                                            break;
                                        }
                                    }
                                    for (int z = 0; z < mButtonList.size(); z++) {
                                        Button currentButton = mButtonList.get(z);
                                        if (currentButton.getTag().equals(currentRowIdWithGoneStatus)) {
                                            currentButton.setVisibility(View.VISIBLE);
                                            break;
                                        }
                                    }
                                    break;
                                }

                            }
                        }

                    }

                    if (val.equalsIgnoreCase("Others") && Constants.surveyFormDetailsObj.getSurveyOtherText().equalsIgnoreCase("yes")) {
                        ShowActionLayout(val, "", "Y", "", "");
                    } else {
                        if (Constants.nickName.equalsIgnoreCase("STAR") && mFinalRowID.matches("RA415")) {
                            if (val.equalsIgnoreCase("Other Reason")) {
                                SetMandatoryChangeBlank("RA416", "Y");
                                for (int z = 0; z < mEditTextList.size(); z++) {
                                    EditText currentButton = mEditTextList.get(z);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                }
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                }
                            } else {
                                SetMandatoryChangeBlank("RA416", "N");
                                for (int z = 0; z < mEditTextList.size(); z++) {
                                    EditText currentButton = mEditTextList.get(z);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                    }
                                }

                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA416", "00:00");
                                        SetTextViewTextBlank("RA416");
                                    }
                                }
                            }
                        }
                        String hide = getValidationByRowId(mFinalRowID);

                        if (hide.contains("hide:")) {
                            String[] hideArray = hide.split("&");
                            String[] hideArray1 = hideArray[0].split(":");
                            String rows = hideArray1[1];
                            String value = hideArray[1];
                            String val1 = al_rootNAMESearch.get(position).trim();

                            String[] rowArray = rows.split(";");

                            for (String s : rowArray) {
                                String row;
                                String mandatoty;
                                if (s.contains(",")) {
                                    String[] row1Array = s.split(",");
                                    row = row1Array[0];
                                    mandatoty = row1Array[1];
                                } else {
                                    row = s;
                                    mandatoty = "Y";
                                }
                                if (value.equalsIgnoreCase(val1)) {
                                    SetMandatoryChangeBlank(row, mandatoty);
                                    for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                        TextView currentButton = mTextViewList.get(tx);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(View.VISIBLE);
                                        }
                                    }
                                    for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                        EditText currentButton = mEditTextList.get(tx);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(View.VISIBLE);
                                            SetSurveyValue(row, " ");
                                            currentButton.setText("");
                                        }
                                    }
                                    for (int z = 0; z < mButtonList.size(); z++) {
                                        Button currentButton = mButtonList.get(z);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(View.VISIBLE);
                                        }
                                    }
                                } else {
                                    SetMandatoryChangeBlank(row, "N");
                                    for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                        TextView currentButton = mTextViewList.get(tx);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(GONE);
                                        }
                                    }
                                    for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                        EditText currentButton = mEditTextList.get(tx);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(GONE);
                                            SetSurveyValue(row, " ");
                                        }
                                    }
                                    for (int z = 0; z < mButtonList.size(); z++) {
                                        Button currentButton = mButtonList.get(z);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(GONE);
                                        }
                                    }
                                }
                            }

                        }

                        if (hide.contains("hidee:")) {
                            String[] hideArray = hide.split("&");
                            String[] hideArray1 = hideArray[0].split(":");
                            String rows = hideArray1[1];
                            String value = hideArray[1];
                            String val1 = al_rootNAMESearch.get(position).trim();

                            String[] rowArray = rows.split(";");

                            for (String s : rowArray) {
                                String row;
                                String mandatoty;
                                if (s.contains(",")) {
                                    String[] row1Array = s.split(",");
                                    row = row1Array[0];
                                    mandatoty = row1Array[1];
                                } else {
                                    row = s;
                                    mandatoty = "Y";
                                }
                                val1 = val1 + ",";
                                if (value.contains(val1)) {
                                    SetMandatoryChangeBlank(row, mandatoty);
                                    for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                        TextView currentButton = mTextViewList.get(tx);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(View.VISIBLE);
                                        }
                                    }
                                    for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                        EditText currentButton = mEditTextList.get(tx);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(View.VISIBLE);
                                            SetSurveyValue(row, " ");
                                            currentButton.setText("");
                                        }
                                    }
                                    for (int z = 0; z < mButtonList.size(); z++) {
                                        Button currentButton = mButtonList.get(z);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(View.VISIBLE);
                                        }
                                    }
                                } else {
                                    SetMandatoryChangeBlank(row, "N");
                                    for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                        TextView currentButton = mTextViewList.get(tx);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(GONE);
                                        }
                                    }
                                    for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                        EditText currentButton = mEditTextList.get(tx);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(GONE);
                                            SetSurveyValue(row, " ");
                                        }
                                    }
                                    for (int z = 0; z < mButtonList.size(); z++) {
                                        Button currentButton = mButtonList.get(z);
                                        if (currentButton.getTag().equals(row)) {
                                            currentButton.setVisibility(GONE);
                                        }
                                    }
                                }
                            }
                        }

                        if (hide.contains("show:")) {
                            String[] showArray = hide.split("@");
                            for (int j = 0; j < showArray.length; j++) {
                                String[] hideArray = showArray[j].split("&");
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
                                    } else {
                                        row = rowArray[i];
                                        mandatoty = "Y";
                                    }
                                    if (value.equalsIgnoreCase(val1)) {
                                        SetMandatoryChangeBlank(row, mandatoty);
                                        for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                            TextView currentButton = mTextViewList.get(tx);
                                            if (currentButton.getTag().equals(row)) {
                                                currentButton.setVisibility(View.VISIBLE);
                                            }
                                        }
                                        for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                            EditText currentButton = mEditTextList.get(tx);
                                            if (currentButton.getTag().equals(row)) {
                                                currentButton.setVisibility(View.VISIBLE);
                                                SetSurveyValue(row, " ");
                                                currentButton.setText("");
                                            }
                                        }
                                        for (int z = 0; z < mButtonList.size(); z++) {
                                            Button currentButton = mButtonList.get(z);
                                            if (currentButton.getTag().equals(row)) {
                                                currentButton.setVisibility(View.VISIBLE);
                                            }
                                        }
                                        j = showArray.length;
                                    } else {
                                        SetMandatoryChangeBlank(row, "N");
                                        for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                            TextView currentButton = mTextViewList.get(tx);
                                            if (currentButton.getTag().equals(row)) {
                                                currentButton.setVisibility(GONE);
                                            }
                                        }
                                        for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                            EditText currentButton = mEditTextList.get(tx);
                                            if (currentButton.getTag().equals(row)) {
                                                currentButton.setVisibility(GONE);
                                                SetSurveyValue(row, " ");
                                            }
                                        }
                                        for (int z = 0; z < mButtonList.size(); z++) {
                                            Button currentButton = mButtonList.get(z);
                                            if (currentButton.getTag().equals(row)) {
                                                currentButton.setVisibility(GONE);
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (Constants.nickName.equalsIgnoreCase("STAR") && mFinalRowID.matches("RA176")) {
                            mMultilevelTableViewData.add(val);

                            if (val.equalsIgnoreCase("Non Star Site")) {
                                ParseTableViewData("YES/NO");
                                SetMandatoryChangeBlank("RA416", "N");
                            } else if (val.equalsIgnoreCase("Star Site")) {
                                ParseTableViewData("Retention site/Upgrade to Premium");
                                SetMandatoryChangeBlank("RA413", "Y");
                                SetMandatoryChangeBlank("RA411", "Y");
                                SetMandatoryChangeBlank("RA416", "N");
                                SetMandatoryChangeBlank("RA412", "Y");
                                SetMandatoryChangeBlank("RA414", "Y");
                                SetMandatoryChangeBlank("RA415", "N");

                            } else if (val.equalsIgnoreCase("No")) {

                                SetMandatoryChangeBlank("RA413", "N");
                                SetMandatoryChangeBlank("RA411", "N");
                                SetMandatoryChangeBlank("RA416", "N");
                                SetMandatoryChangeBlank("RA412", "N");
                                SetMandatoryChangeBlank("RA414", "N");
                                SetMandatoryChangeBlank("RA415", "Y");
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA415", "00:00");
                                    }
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA412", "00:00");
                                    }
                                    if (currentButton.getTag().equals("RA413")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA413", "00:00");
                                    }
                                    if (currentButton.getTag().equals("RA414")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA414", "00:00");
                                        SetTextViewTextBlank("RA414");
                                    }
                                }

                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(GONE);
                                        SetTextViewTextBlank("RA412");
                                    }
                                    if (currentButton.getTag().equals("RA413")) {
                                        currentButton.setVisibility(GONE);
                                        SetTextViewTextBlank("RA413");
                                    }
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                        SetTextViewTextBlank("RA416");
                                    }
                                }

                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals("RA415")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA411")) {
                                        currentButton.setVisibility(GONE);
                                    }
                                    if (currentButton.getTag().equals("RA414")) {
                                        currentButton.setVisibility(GONE);
                                    }
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA412", "00:00");
                                    }

                                }
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                        SetTextViewTextBlank("RA416");
                                    }
                                }

                            } else if (val.equalsIgnoreCase("Yes")) {
                                SetMandatoryChangeBlank("RA411", "Y");
                                SetMandatoryChangeBlank("RA416", "N");
                                SetMandatoryChangeBlank("RA412", "Y");
                                SetMandatoryChangeBlank("RA413", "Y");
                                SetMandatoryChangeBlank("RA414", "Y");
                                SetMandatoryChangeBlank("RA415", "N");
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA416", "00:00");
                                    }
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA413")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                }
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA413")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                    }
                                }

                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals("RA415")) {
                                        currentButton.setVisibility(GONE);
                                    }
                                    if (currentButton.getTag().equals("RA411")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA414")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                }

                            } else if (val.equalsIgnoreCase("Retention site") || val.equalsIgnoreCase("Upgrade to Premium")) {
                                SetMandatoryChangeBlank("RA411", "Y");
                                SetMandatoryChangeBlank("RA416", "N");
                                SetMandatoryChangeBlank("RA412", "Y");
                                SetMandatoryChangeBlank("RA413", "Y");
                                SetMandatoryChangeBlank("RA414", "Y");
                                SetMandatoryChangeBlank("RA415", "N");
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA416", "00:00");
                                    }
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA413")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                }
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA413")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(GONE);
                                        SetTextViewTextBlank("RA416");
                                    }
                                }

                                for (int z = 0; z < mButtonList.size(); z++) {
                                    Button currentButton = mButtonList.get(z);
                                    if (currentButton.getTag().equals("RA415")) {
                                        currentButton.setVisibility(GONE);
                                    }
                                    if (currentButton.getTag().equals("RA411")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA414")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                }

                            } else if (val.equalsIgnoreCase("Other Reasons")) {
                                SetMandatoryChangeBlank("RA416", "Y");
                                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                                    TextView currentButton = mTextViewList.get(tx);
                                    if (currentButton.getTag().equals("RA412")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA412", "00:00");
                                    }
                                    if (currentButton.getTag().equals("RA413")) {
                                        currentButton.setVisibility(GONE);
                                        SetSurveyValue("RA413", "00:00");
                                    }
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                }
                                for (int tx = 0; tx < mEditTextList.size(); tx++) {
                                    EditText currentButton = mEditTextList.get(tx);
                                    if (currentButton.getTag().equals("RA416")) {
                                        currentButton.setVisibility(View.VISIBLE);
                                    }
                                }
                            }
                            GetMultiLevelTableViewData(val);
                        } else if (!mParentType.matches("multileveltableview")) {
                            SetTextViewText(mFinalRowID, val);
                            SetSurveyValue(mFinalRowID, val);
                            if (!mActionstring.isEmpty()) {
                                if (mActionstring.equalsIgnoreCase("no_repetition")) {
                                    mNonRepeat.add(val);
                                }
                            }
                        }

                    }
                    if (mParentType.matches("multileveltableview")) {
                        mMultilevelTableViewData.add(val);
                        GetMultiLevelTableViewData(val);
                    } else if (mParentType.matches("dependenttableview") && actionStringOfCurrentSelectedItem != null && actionStringOfCurrentSelectedItem.contains(":")) {

                        String[] splittedActionString = actionStringOfCurrentSelectedItem.split(":");
                        if (splittedActionString.length > 0) {
                            String dependentSurveyInputString = splittedActionString[1];
                            String[] dependentSurveyInputStringSplitted = dependentSurveyInputString.split("#");
                            if (dependentSurveyInputStringSplitted.length >= 5 && splittedActionString[0].matches(val)) {
                                tempValForDependentSurvey = val;
                                String[] SplittedTableDetails = dependentSurveyInputStringSplitted[3].split("%");
                                mTableName = SplittedTableDetails[0];
                                mSendColumn = SplittedTableDetails[1];
                                mShowColumn = SplittedTableDetails[2];
                                mDecision = "SAVE";
                                mType = dependentSurveyInputStringSplitted[5];
                                mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7(mTableName, mSendColumn, mShowColumn, mCustomerSelectionBasis, "");
                                if (mKeyValueList != null && !mKeyValueList.isEmpty()) {
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
            });

            Button submit = grpDialog.findViewById(R.id.button1);
            if (type.equalsIgnoreCase("radio")) {
                submit.setVisibility(View.GONE);
            }

            submit.setOnClickListener(arg0 -> {
                if (type.equalsIgnoreCase("checkbox")) {
                    if (mCategory && !mSubCategory) {
                        mCondition = "";
                        if (!mDependentRowId.trim().isEmpty()) {
                            SetTextViewText(mDependentRowId, "");
                        }
                        mDependentRowId = "";
                    }
                    final SparseBooleanArray checkedItems = List
                            .getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        StringBuilder val = new StringBuilder();
                        StringBuilder valId = new StringBuilder();
                        StringBuilder a1 = new StringBuilder();
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                val.append(values[position]).append(";");
                                if (isShowValueSendValueDifferentForChcekBOx && sendColumnDataForSurveyCheckBox != null) {
                                    valId.append(sendColumnDataForSurveyCheckBox.get(position)).append(";");
                                }

                                if (mCategory && !mSubCategory) {
                                    if (isShowValueSendValueDifferentForChcekBOx && sendColumnDataForSurveyCheckBox != null) {
                                        a1.append("'").append(sendColumnDataForSurveyCheckBox.get(position)).append("',");
                                    } else {
                                        a1.append("'").append(values[position]).append("',");
                                    }

                                } else if (!mCategory && mSubCategory) {
                                    if (isShowValueSendValueDifferentForChcekBOx && sendColumnDataForSurveyCheckBox != null) {
                                        a1.append("'").append(sendColumnDataForSurveyCheckBox.get(position)).append("',");
                                    } else {
                                        a1.append("'").append(values[position]).append("',");
                                    }
                                }
                            }
                        }
                        mCondition = a1.toString();

                        if (mCondition.endsWith(",")) {
                            mCondition = mCondition.substring(0,
                                    mCondition.length() - 1);
                        }
                        if (mParentType.matches("multileveltableview")) {
                            mMultilevelTableViewData.add(val.toString());
                            SetTextViewText(mFinalRowID, TextUtils.join(", ", mMultilevelTableViewData));
                            SetSurveyValue(mFinalRowID, TextUtils.join("#", mMultilevelTableViewData));
                        } else if (mParentType.matches("checkbox")) {
                            SetTextViewText(mFinalRowID, val.toString());
                            if (isShowValueSendValueDifferentForChcekBOx && sendColumnDataForSurveyCheckBox != null) {
                                SetSurveyValue(mFinalRowID, valId.toString());
                                isShowValueSendValueDifferentForChcekBOx = false;
                            } else {
                                SetSurveyValue(mFinalRowID, val.toString());
                            }
                        } else {
                            SetTextViewText(mFinalRowID, val.toString());
                            SetSurveyValue(mFinalRowID, val.toString());
                        }

                    }
                    grpDialog.cancel();
                    if (!mSubtableInfo.isEmpty()) {
                        if (ParseSubTablename(mSubtableInfo)) {
                            PrepareSurveyData(5, "");
                        }
                    }

                } else if (type.contains("/") || type.equalsIgnoreCase("double") || type.equalsIgnoreCase("")) {
                    String val = doubleTextView.getText().toString();
                    String val2 = "";
                    String type1 = "", type2 = "";
                    boolean isInputOk = true;
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


                } else if (type.equalsIgnoreCase("date")) {
                    int day = datePicker.getDayOfMonth();
                    int month = datePicker.getMonth();
                    int year = datePicker.getYear();
                    Calendar calendar = Calendar.getInstance();
                    calendar.set(year, month, day);

                    @SuppressLint("SimpleDateFormat") SimpleDateFormat sdf = new SimpleDateFormat("dd/MM/yyyy");
                    String val = sdf.format(calendar.getTime());
                    mMultilevelTableViewData.add(val);
                    SetTextViewText(mFinalRowID, TextUtils.join(", ", mMultilevelTableViewData));
                    SetSurveyValue(mFinalRowID, TextUtils.join("#", mMultilevelTableViewData));
                    grpDialog.cancel();
                } else {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        }
    }

    private void HideSoftKeyBoard(EditText autoCompleteTextView1OBJ) {
        try {
            InputMethodManager imm = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
            imm.hideSoftInputFromWindow(autoCompleteTextView1OBJ.getWindowToken(), 0);
        } catch (Exception e) {
            Log.d("TAG", "HideSoftKeyBoard: " + e.getMessage());
        }
    }

    public void onClick(View clkdView) {
        String tag = (String) clkdView.getTag();
        mFinalRowID = tag;
        mParentType = "";
        boolean isaction = CheckActionId(tag);
        mParentType = mType;
        if (isaction && mParentType.equalsIgnoreCase("Y:N")) {
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
            ParseandShowAction(tag);
        }
        if (isaction && mParentType.equalsIgnoreCase("dynamicview")) {
            ParseandShowActionForDynamicView(tag);
        }

        if (isaction && mParentType.equalsIgnoreCase("bool")) {
            ParseandShowAction(tag);
        }
        if (isaction && mParentType.equalsIgnoreCase("date")) {
            ParseandShowAction(tag);
        }

        if (isaction && mParentType.equalsIgnoreCase("tableview")) {
            if (Constants.nickName.equalsIgnoreCase("STAR") && tag.equalsIgnoreCase("RA176")) {
                mMultilevelTableViewData = new ArrayList<>();
                SetTextViewTextBlank("RA415");
                SetTextViewTextBlank("RA411");
                SetTextViewTextBlank("RA414");
            }
            if (Constants.nickName.equalsIgnoreCase("STAR")) {
                if (tag.equalsIgnoreCase("RA411") || tag.equalsIgnoreCase("RA412") || tag.equalsIgnoreCase("RA413") || tag.equalsIgnoreCase("RA414") || tag.equalsIgnoreCase("RA415") || tag.equalsIgnoreCase("RA416")) {
                    if (getGivenRowIdValue("RA176").isEmpty()) {
                        Utils.showToast(mContext, "Select VISIT TYPE");
                    } else {
                        GetTableViewData(tag);
                    }
                } else {
                    GetTableViewData(tag);
                }
            } else {
                GetTableViewData(tag);
            }
        }

        if (!isaction && mParentType.equalsIgnoreCase("checkbox")) {
            isShowValueSendValueDifferentForChcekBOx = false;
            isCurrentCheckBoxItemDependant = false;
            String tablename = GetTableName(tag);
            if (ParseTablename(tablename)) {
                PrepareSurveyData(2, tablename);
            } else {
                mCategory = false;
                mSubCategory = false;
                mCondition = "";
                PrepareSurveyData(2, tablename);
            }
        }

        if (!isaction && mParentType.equalsIgnoreCase("radio")) {
            dependantConditionSqlQueryForRadioType = "";
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                if (CheckSurveyValidation(tag, "check") && !mCheckValue.isEmpty()) {
                    String[] splittablename = tablename.split("#");
                    tablename = splittablename[1];
                    mCategory = false;
                    mSubCategory = false;
                    mCondition = "";
                    if (splittablename.length == 4) {
                        String selectColumnForRadioType = splittablename[2];
                        String WhereColumnForRadioType = splittablename[3];
                        dependantConditionSqlQueryForRadioType = "SELECT DISTINCT " + selectColumnForRadioType + " FROM " + tablename + " WHERE " + WhereColumnForRadioType + "='" + mCheckValue + "'";
                    }
                    PrepareSurveyData(2, tablename);
                } else {
                    Utils.showToast(mContext, "Please select " + mDependentDisplayName);
                }
            } else {
                mCategory = false;
                mSubCategory = false;
                mCondition = "";
                PrepareSurveyData(2, tablename);
            }
        }

        if (!isaction && mParentType.equalsIgnoreCase("tickbox")) {
            boolean checked = ((CheckBox) clkdView).isChecked();
            if (checked) {
                SetSurveyValue(mFinalRowID, "yes");
            } else {
                SetSurveyValue(mFinalRowID, "");
            }
        }


        if (!isaction && CheckColonPresentInType(mParentType)) {
            boolean checked = ((RadioButton) clkdView).isChecked();
            if (checked) {
                String val = ((RadioButton) clkdView).getText().toString();
                SetSurveyValue(mFinalRowID, val.toLowerCase());
                ChangeVisibility(mFinalRowID, val.toLowerCase());
            }
        }

        if (isaction && CheckColonPresentInType(mParentType) && !mParentType.equalsIgnoreCase("Y:N")) {
            boolean checked = ((RadioButton) clkdView).isChecked();
            if (checked) {
                String val = ((RadioButton) clkdView).getText().toString();
                ParseandShowAction(mFinalRowID, val);
            }
        }

        if (!isaction && mParentType.equalsIgnoreCase("rating")) {
            boolean checked = ((RadioButton) clkdView).isChecked();
            if (checked) {
                String val = ((RadioButton) clkdView).getText().toString();
                SetSurveyValue(mFinalRowID, val);
                SetTextViewText(mFinalRowID, val);
            }
        }

        if (!isaction && mParentType.equalsIgnoreCase("tableview")) {

            if (Constants.nickName.equalsIgnoreCase("STAR")) {
                if (tag.equalsIgnoreCase("RA411") || tag.equalsIgnoreCase("RA412") || tag.equalsIgnoreCase("RA413") || tag.equalsIgnoreCase("RA414") || tag.equalsIgnoreCase("RA415") || tag.equalsIgnoreCase("RA416")) {
                    if (getGivenRowIdValue("RA176").isEmpty()) {
                        Utils.showToast(mContext, "Select VISIT TYPE");
                    } else {
                        GetTableViewData(tag);
                    }
                } else {
                    GetTableViewData(tag);
                }
            } else {
                GetTableViewData(tag);
            }
        }
        if (mParentType.equalsIgnoreCase("tableviewtableview")) {
            getActionOfCurrentItem(tag);
            String ss = actionStringOfCurrentSelectedItem;

            if (ss.contains("@")) {
                String[] splirowID = ss.split("@");
                String row = splirowID[1];
                String action1 = splirowID[0];

                if (getGivenRowIdValue(row).isEmpty()) {

                    Utils.showToast(mContext, "Please select " + getDisplayNameByRowId(row));
                    return;
                } else {

                    if (action1.contains("#")) {
                        String[] splittedActionString = action1.split("#");

                        if (splittedActionString.length > 0) {
                            for (int i = 0; i < splittedActionString.length; i++) {
                                String dependentSurveyInputString = splittedActionString[i];
                                String[] dependentSurveyInputStringSplitted = dependentSurveyInputString.split(":");
                                if (dependentSurveyInputStringSplitted[0].contains("/")) {
                                    for (int i1 = 0; i1 < dependentSurveyInputStringSplitted.length; i1++) {
                                        String[] finVal = dependentSurveyInputStringSplitted[0].split("/");
                                        String finVal1 = finVal[i1];
                                        if (getGivenRowIdValue(row).equalsIgnoreCase(finVal1)) {
                                            ParseTableViewData(dependentSurveyInputStringSplitted[i]);
                                        }
                                    }
                                } else {
                                    for (int i1 = 0; i1 < dependentSurveyInputStringSplitted.length; i1++) {
                                        String finVal = dependentSurveyInputStringSplitted[i1];
                                        if (getGivenRowIdValue(row).equalsIgnoreCase(finVal)) {
                                            ParseTableViewData(dependentSurveyInputStringSplitted[i1 + 1]);
                                        }
                                    }
                                }

                            }

                        }
                    }
                    return;
                }
            }
        }
        if (mParentType.equalsIgnoreCase("dependenttableview")) {
            getActionOfCurrentItem(tag);
            GetTableViewData(tag);
        }
        if (mParentType.equalsIgnoreCase("multileveltableview")) {
            mMultilevelTableViewData = new ArrayList<>();
            getActionOfCurrentItem(tag);
            currentRowId = tag;
            GetTableViewDataMultiLevel(tag);
        }


        if (!isaction && mParentType.equalsIgnoreCase("masterview")) {
            boolean isDataOk = true;
            String MessageForDataNotOk = "";
            mCustomerSelectionBasisFilter = "";
            mCustomerSelectionBasis = "";
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                String[] splitablename = tablename.split("#");
                if (splitablename.length == 3 || splitablename.length == 4 || splitablename.length == 5) {
                    mTableName = splitablename[0];
                    mColumnName = splitablename[1];
                    mIsBranch = false;
                    mType = splitablename[2];
                    if (splitablename.length == 4) {
                        mCustomerSelectionBasis = splitablename[3];
                        if (splitablename[3].contains("&!=")) {
                            String[] splitablecondition = splitablename[3].split("&!=");
                            mCustomerSelectionBasis = splitablecondition[0];
                            if (splitablecondition[1].contains("loginemployee")) {
                                mCustomerSelectionBasisFilter = "WHERE emp_code!='" + Constants.employeeDetailObject.getEmpCode() + "'";
                                if (splitablecondition[0].contains("sale_access;Primary")) {
                                    mCustomerSelectionBasisFilter = "WHERE sale_access='Primary' AND emp_code!='" + Constants.employeeDetailObject.getEmpCode() + "'";
                                }

                            }
                        } else if (splitablename[3].contains(";")) {
                            mIsBranch = true;
                        }
                    }

                    if (splitablename.length == 5) {

                        String dataFetchLogic = splitablename[4];
                        if (dataFetchLogic.contains("&")) {
                            String[] dataFetchLogicArray = dataFetchLogic.split("&");
                            for (String s : dataFetchLogicArray) {
                                String[] mCustomerSelectionBasisFilterArray = s.split(";");
                                if (mCustomerSelectionBasisFilterArray.length == 2) {
                                    String dataSelectionLogicValue = mCustomerSelectionBasisFilterArray[1];
                                    if (dataSelectionLogicValue.equalsIgnoreCase("loginempcode"))//hardcoded
                                    {
                                        mCustomerSelectionBasisFilterArray[1] = Constants.employeeDetailObject.getEmpCode();
                                    }
                                    if (dataSelectionLogicValue.contains("row_id-")) {
                                        String dependingOnRowId = dataSelectionLogicValue.split("-")[1];
                                        if (CheckGivenRowIdHasValue(dependingOnRowId)) {
                                            if (this.mCustomerSelectionBasisFilter.matches("")) {
                                                this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCheckValue + "' ";
                                            } else {
                                                this.mCustomerSelectionBasisFilter = this.mCustomerSelectionBasisFilter + " AND " + mCustomerSelectionBasisFilterArray[0] + "='" + mCheckValue + "' ";
                                            }
                                        } else {
                                            isDataOk = false;
                                            MessageForDataNotOk = "Please select " + mDependentDisplayName + " first.";
                                            break;
                                        }
                                    } else {
                                        this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCustomerSelectionBasisFilterArray[1] + "' ";
                                    }
                                }
                            }
                        } else {
                            String[] mCustomerSelectionBasisFilterArray = dataFetchLogic.split(";");
                            if (mCustomerSelectionBasisFilterArray.length == 2) {
                                String dataSelectionLogicValue = mCustomerSelectionBasisFilterArray[1];
                                if (dataSelectionLogicValue.equalsIgnoreCase("loginempcode")) {
                                    mCustomerSelectionBasisFilterArray[1] = Constants.employeeDetailObject.getEmpCode();
                                }
                                if (dataSelectionLogicValue.contains("row_id-")) {
                                    String dependingOnRowId = dataSelectionLogicValue.split("-")[1];
                                    if (CheckGivenRowIdHasValue(dependingOnRowId)) {
                                        this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCheckValue + "' ";
                                        if (mCustomerSelectionBasisFilterArray[0].equalsIgnoreCase("branch_code")) {
                                            this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + " like '%" + mCheckValue + "%' ";
                                        }
                                    } else {
                                        isDataOk = false;
                                        MessageForDataNotOk = "Please select " + mDependentDisplayName + " first.";
                                    }
                                } else {
                                    this.mCustomerSelectionBasisFilter = " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCustomerSelectionBasisFilterArray[1] + "' ";
                                }
                            }
                        }

                    }
                    if (isDataOk) {
                        if (mColumnName.contains("%")) {
                            if (!mIsBranch) {
                                String[] splitColunName = mColumnName.split("%");
                                mSendColumn = splitColunName[0];
                                mShowColumn = splitColunName[1];
                                mDecision = "SAVE";
                                PrepareSurveyData(7, "");
                            } else {
                                String[] splitColunName = mColumnName.split("%");
                                mSendColumn = splitColunName[0];
                                mShowColumn = splitColunName[1];
                                mDecision = "SAVE";
                                PrepareSurveyData(12, "");
                            }
                        } else {
                            PrepareSurveyData(6, "");
                        }
                    } else {
                        Utils.showToast(mContext, MessageForDataNotOk);
                    }


                } else {
                    Utils.showToast(mContext, "No data found.Please Synchronize Data");
                }
            } else {
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        }
        if (!isaction && mParentType.equalsIgnoreCase("imageview")) {
            mCustomerSelectionBasisFilter = "";
            mCustomerSelectionBasis = "";
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                String[] splitablename = tablename.split("#");
                if (splitablename.length == 3 || splitablename.length == 4 || splitablename.length == 5) {
                    mTableName = splitablename[0];
                    mColumnName = splitablename[1];
                    mType = splitablename[2];
                    if (splitablename.length == 4) {
                        mCustomerSelectionBasis = splitablename[3];
                    }

                    if (splitablename.length == 5) {
                        String[] mCustomerSelectionBasisFilterArray = splitablename[4].split(";");
                        if (mCustomerSelectionBasisFilterArray.length == 2) {
                            if (mCustomerSelectionBasisFilterArray[1].equalsIgnoreCase("loginempcode"))//hardcoded
                            {
                                mCustomerSelectionBasisFilterArray[1] = Constants.employeeDetailObject.getEmpCode();
                            }
                            this.mCustomerSelectionBasisFilter = " WHERE lower(acedns)=y AND " + mCustomerSelectionBasisFilterArray[0] + "='" + mCustomerSelectionBasisFilterArray[1] + "' ";
                        }
                    }

                    if (mColumnName.contains("%")) {
                        String[] splitColunName = mColumnName.split("%");
                        mSendColumn = splitColunName[0];
                        mShowColumn = splitColunName[1];
                        mDecision = "SAVE";
                        PrepareSurveyData(7, "");
                    } else {
                        PrepareSurveyData(6, "");
                    }
                } else {
                    Utils.showToast(mContext, "No data found.Please Synchronize Data");
                }
            } else {
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        }

        if (!isaction && mParentType.equalsIgnoreCase("relationalview")) {
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                if (CheckSurveyValidation(tag, "check") && !mCheckValue.isEmpty()) {
                    String[] splitablename = tablename.split("#");
                    if (splitablename.length == 3 || splitablename.length == 4 || splitablename.length == 5) {
                        String joinedTableName = splitablename[0];
                        String[] joinedTableNameSplitted = joinedTableName.split(";");
                        mTableName = joinedTableNameSplitted[0];
                        String mTableName2 = joinedTableNameSplitted[1];

                        String joinedColumnName = splitablename[1];
                        String[] joinedColumnNameSplitted = joinedColumnName.split("%");
                        mSendColumn = joinedColumnNameSplitted[0];
                        mShowColumn = joinedColumnNameSplitted[1];

                        String joinedColumnNameFilter = splitablename[2];
                        String[] joinedColumnNameFilterSplitted = joinedColumnNameFilter.split(";");
                        String joinedColumnNameFilter1 = joinedColumnNameFilterSplitted[0];
                        String joinedColumnNameFilter2 = joinedColumnNameFilterSplitted[1];

                        mType = splitablename[3];
                        String joinedColumnNameFilter3 = splitablename[4];


                        String queryToGetmasterData = "SELECT DISTINCT " + mSendColumn + ", " + mShowColumn + " FROM " + mTableName + ", " + mTableName2 + " WHERE " + joinedColumnNameFilter1 + "=" + joinedColumnNameFilter2
                                + " AND " + joinedColumnNameFilter3 + "='" + mCheckValue + "'";

                        mDecision = "SAVE";
                        PrepareSurveyData(11, queryToGetmasterData);

                    } else {
                        Utils.showToast(mContext, "No data found.Please Synchronize Data");
                    }
                } else {
                    Utils.showToast(mContext, "Please select " + mDependentDisplayName);
                }


            } else {
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        }

        if (!isaction && mParentType.equalsIgnoreCase("masterviewjoin")) {
            mCustomerSelectionBasisFilter = "";
            mCustomerSelectionBasis = "";
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                String[] splitablename = tablename.split("#");
                if (splitablename.length == 3 || splitablename.length == 4 || splitablename.length == 5 || splitablename.length == 8) {
                    mTableName = splitablename[0];
                    mColumnName = splitablename[1];
                    mType = splitablename[2];
                    if (splitablename.length == 4) {
                        mCustomerSelectionBasis = splitablename[3];
                    }

                    if (splitablename.length == 8) {

                        String dataFetchLogic = splitablename[4];
                        if (dataFetchLogic.contains("@")) {
                            String[] dataFetchTableLogicArray = dataFetchLogic.split("@");
                            String table2 = dataFetchTableLogicArray[1];
                            String[] dataFetchLogicArray = splitablename[7].split("&");

                            String ss = "INNER JOIN " + table2 + " ON " + table2 + "." + splitablename[5] + " = " + splitablename[6] + "." + dataFetchLogicArray[0];
                            for (String s : dataFetchLogicArray) {
                                String[] mCustomerSelectionBasisFilterArray = s.split(";");
                                if (mCustomerSelectionBasisFilterArray.length == 2) {
                                    if (mCustomerSelectionBasisFilterArray[1].contains("row_id-")) {
                                        String dependingOnRowId = mCustomerSelectionBasisFilterArray[1].split("-")[1];
                                        if (CheckGivenRowIdHasValue(dependingOnRowId)) {
                                            this.mCustomerSelectionBasisFilter = ss + " WHERE " + mCustomerSelectionBasisFilterArray[0] + "='" + mCheckValue + "' ";
                                        }
                                    }

                                }
                            }
                        }
                    }

                    if (mColumnName.contains("%")) {
                        String[] splitColunName = mColumnName.split("%");
                        mSendColumn = mTableName + "." + splitColunName[0];
                        mShowColumn = mTableName + "." + splitColunName[1];
                        mDecision = "SAVE";
                        PrepareSurveyData(7, "");
                    } else {
                        PrepareSurveyData(6, "");
                    }


                } else {
                    Utils.showToast(mContext, "No data found.Please Synchronize Data");
                }
            } else {
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
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
        String actionstring;
        String rowid;
        String validation;
        mType = "";

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                actionstring = mInputTimeSurveyDetailsList.get(count).getAction();
                if (actionstring.contains("#")) {
                    String[] RowData = actionstring.split("#");
                    if (RowData.length > 0) {
                        String actiondisplayname = RowData[0];
                        String actiontype = RowData[1];
                        String actionmandatory = RowData[2];
                        String actionvalidation = "";
                        String tablename = "";

                        if (actiondisplayname.contains(";")) {
                            String[] splitdisplay = actiondisplayname.split(";");
                            String actionsubdisplayname = splitdisplay[0];
                            String noofsubdisplay = splitdisplay[1];

                            String subactionmandatory = "";
                            String subactionno = "";

                            if (actionmandatory.contains(";")) {
                                String[] splitmandatory = actionstring.split(";");
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
                                ShowSubActionLayout(actionsubdisplayname, Integer.parseInt(noofsubdisplay), actiontype, subactionmandatory, Integer.parseInt(subactionno), "");
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
                    if (actionstring.equalsIgnoreCase("Click") || actionstring.contains("Click:")) {
                        SetMaxNoofImage(rowID, validation);
                        ShowImageCaptureLayer(rowID);
                    } else if (actionstring.equalsIgnoreCase("floor")) {
                        PrepareSurveyData(10, "");
                    } else {
                        openDatePicker(rowID, actionstring, validation);
//						ChooseDateDialog(rowID,actionstring);
                    }
                }
                break;
            }
        }
    }

    public void ParseandShowActionForDynamicView(String rowID) {
        String actionstring;
        String rowid;
        mType = "";


        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                actionstring = mInputTimeSurveyDetailsList.get(count).getAction().trim();
                if (actionstring.contains("#")) {
                    String[] actionsplit = actionstring.split("#");
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
                        if (mEdiTextDynamic != null && !mEdiTextDynamic.getText().toString().isEmpty()) {
                            int repeat = Integer.parseInt(mEdiTextDynamic.getText().toString());
                            ShowSubActionLayoutDynamicView(displayname, typelist, mandatory, repeat);
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

    public void ParseandShowAction(String rowID, String value) {
        mActionPressed = value;
        String actionstring;
        String rowid;
        mType = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mDisplayName = mInputTimeSurveyDetailsList.get(count).getDisplayName().trim() + " : " + value;
                actionstring = mInputTimeSurveyDetailsList.get(count).getAction().trim();
                if (actionstring.contains("$")) {
                    String[] actionsplit = actionstring.split("\\$");
                    if (actionsplit.length > 0) {
                        for (String s : actionsplit) {
                            if (s.trim().startsWith(value)) {
                                String[] subaction = s.split(":");
                                if (subaction.length == 2) {
                                    if (subaction[1] != null && !subaction[1].trim().isEmpty()) {
                                        if (subaction[1].trim().contains("@")) {
                                            String[] subactionlist = subaction[1].split("@");
                                            ArrayList<SurveyInput> surveyInputlist = new ArrayList<>();
                                            for (int subactioncount = 0; subactioncount < subactionlist.length; subactioncount++) {
                                                String[] subactiondetaislis = subactionlist[subactioncount].split("#");
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
                                            }
                                            if (!surveyInputlist.get(0).getSurveyType().equalsIgnoreCase("masterviewinput")) {
                                                ShowSubActionLayout(surveyInputlist, rowID);
                                            } else {
                                                showMasterViewInput(surveyInputlist);
                                            }


                                        } else {
                                            if (subaction[1].contains("#")) {
                                                String[] subactiondetaislis = subaction[1].split("#");
                                                ArrayList<SurveyInput> surveyInputlist = new ArrayList<>();
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
                                                ShowSubActionLayout(surveyInputlist, rowID);
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
                }
                break;
            }
        }
    }

    private void showMasterViewInput(ArrayList<SurveyInput> surveyInputList) {
        String tablenamedetails = surveyInputList.get(0).getSurveyTableName();
        String clause = surveyInputList.get(0).getSurveyClause().trim();
        String displayname = surveyInputList.get(0).getSurveyDisplayName();
        String validation = surveyInputList.get(1).getSurveyTableName();
        if (tablenamedetails.contains("%")) {
            String[] splitablename = tablenamedetails.split("\\%");
            if (splitablename.length == 3) {
                mTableName = splitablename[0];
                mSendColumn = splitablename[1];
                mShowColumn = splitablename[2];
                mDecision = "FORWARD";
                if (!clause.isEmpty()) {
                    if (clause.contains("&&")) {
                        String[] noofclause = clause.split("\\&&");
                        for (int clausecount = 0; clausecount < noofclause.length; clausecount++) {
                            String[] splitwhere = noofclause[clausecount].split(";");
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
                        String[] splitclause = clause.split(";");
                        mWhereClause = splitclause[0] + "='" + splitclause[1] + "'";
                        PrepareSurveyData(9, "");
                    }
                } else {
                    mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7(mTableName, mSendColumn, mShowColumn, mCustomerSelectionBasis, mCustomerSelectionBasisFilter);
                    if (mKeyValueList != null && !mKeyValueList.isEmpty()) {
                        ShowListWithInput(displayname, validation);
                    } else {
                        Utils.showToast(mContext, "No data found. Please Synchronize Data");
                    }
                }
            } else {
                Utils.showToast(mContext, "No data found.Please Synchronize Data");
            }
        } else {
            Utils.showToast(mContext, "No data found.Please Synchronize Data");
        }
    }

    public void ChooseDateDialog(final String rowid, final String action) {

        final Dialog dateDialog = new Dialog(mContext, R.style.PauseDialog);
        dateDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dateDialog.setContentView(R.layout.date_dialog);
        dateDialog.setCancelable(true);

        Button submit = dateDialog.findViewById(R.id.buttonDone);
        final EditText date = dateDialog.findViewById(R.id.editTextDate);
        date.setHint(action);

        submit.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            boolean isOk = false;
            mDateValue = date.getText().toString();
            if (mDateValue.contains("/")) {
                String[] splitdate = mDateValue.split("/");
                if (splitdate.length == 3) {
                    boolean isdate = ContainsOnlyNumbers(splitdate[0]);
                    boolean ismonth = ContainsOnlyNumbers(splitdate[1]);
                    boolean isyear = ContainsOnlyNumbers(splitdate[2]);
                    if (isdate && ismonth && isyear) {
                        String day = splitdate[0];
                        String month = splitdate[1];
                        int year = Integer.parseInt(splitdate[2]);

                        if (day.equals("31") && (month.equals("4") || month.equals("6") || month.equals("9") || month.equals("11") || month.equals("04") || month.equals("06") || month.equals("09"))) {
                            isOk = false; // only 1,3,5,7,8,10,12 has 31 days
                        } else if (month.equals("2") || month.equals("02")) {
                            //leap year
                            if (year % 4 == 0) {
                                isOk = Integer.parseInt(day) <= 29;
                            } else {
                                isOk = Integer.parseInt(day) <= 28;
                            }
                        } else if (Integer.parseInt(day) > 31 || Integer.parseInt(day) < 1) {
                            isOk = false;
                        } else isOk = Integer.parseInt(month) <= 12;

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
                (view, year, monthOfYear, dayOfMonth) -> {
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
                        } else if (validation.matches("p_c_date")) {

                            isValidationPassed = comparisonResult == 1;

                        }
                        if (isValidationPassed) {
                            SetTextViewText(rowID, mDateValue);
                            SetSurveyValue(rowID, mDateValue);
                        } else {
                            Toast.makeText(mContext, "Please provide proper date", Toast.LENGTH_SHORT).show();
                        }

                    } catch (Exception ignored) {

                    }


                }, mYear, mMonth, mDay);
        datePickerDialog.setCancelable(true);
        datePickerDialog.show();

        if (validation.matches("p_c_date")) {
            datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());
            c.add(Calendar.DAY_OF_MONTH, -1); // add date to 30 days later
            datePickerDialog.getDatePicker().setMinDate(c.getTimeInMillis());
        }

//		if(currentDisplayName.matches("End Date"))
//		{
//			datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis() - 1000);
//		}
    }

    public boolean CheckActionId(String rowID) {
        boolean isAction = false;
        String rowid;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mType = mInputTimeSurveyDetailsList.get(count).getType();
                String action = mInputTimeSurveyDetailsList.get(count).getActionId().trim();
                int j = action.length();
                if (j > 1) {
                    isAction = true;
                }
                break;
            }
        }
        return isAction;
    }

    public void ShowSubActionLayout(String displayname, final int displayno, final String actiontype, final String submandatory, final int mandatoryno, final String tablename) {

        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_sub_action);
        grpDialog.setCancelable(false);


        final LinearLayout mSubParentLayout = grpDialog.findViewById(R.id.linearLayoutParent);

        for (int count = 0; count < displayno; count++) {

            LinearLayout childlayout = new LinearLayout(this);
            String hinttext = "";
            childlayout.setOrientation(LinearLayout.VERTICAL);
            childlayout.setPadding(5, 5, 5, 5);

            if (count < mandatoryno) {
                hinttext = "*" + displayname + " " + (count + 1);
            } else {
                hinttext = displayname + " " + (count + 1);
            }
            childlayout.addView(AddEditText(String.valueOf(count), actiontype, hinttext));
            childlayout.addView(AddEmptyViewUnderEditText());
            mSubParentLayout.addView(childlayout);
        }

        Button submit = grpDialog.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(arg0 -> {
            if (!tablename.isEmpty()) {
                grpDialog.cancel();
            } else {
                if (submandatory.equalsIgnoreCase("Y")) {
                    boolean isValueOK = true;
                    if (!mEditTextList.isEmpty()) {
                        StringBuilder val = new StringBuilder();

                        for (int count = 0; count < displayno; count++) {
                            if (isValueOK) {
                                for (EditText editText : mEditTextList) {
                                    String tag = editText.getTag()
                                            .toString();
                                    if (tag.equalsIgnoreCase(String
                                            .valueOf(count))) {
                                        if (count < mandatoryno) {
                                            if (!editText.getText().toString().isEmpty()) {
                                                val.append(editText.getText().toString()).append("; ");
                                            } else {
                                                isValueOK = false;
                                                Toast.makeText(mContext, "Please provide valid inputs", Toast.LENGTH_LONG).show();
                                                break;
                                            }
                                        } else {
                                            if (!editText.getText().toString().isEmpty()) {
                                                val.append(editText.getText().toString()).append("; ");
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (isValueOK) {
                            SetSurveyValue(mFinalRowID, val.toString());
                            SetTextViewText(mFinalRowID, val.toString());
                            grpDialog.cancel();
                        }
                    }
                } else {
                    if (!mEditTextList.isEmpty()) {
                        StringBuilder val = new StringBuilder();
                        for (int count = 0; count < displayno; count++) {
                            for (EditText editText : mEditTextList) {
                                String tag = editText.getTag().toString();
                                if (tag.equalsIgnoreCase(String.valueOf(count))) {
                                    if (!editText.getText().toString().isEmpty()) {
                                        val.append(editText.getText().toString()).append("; ");
                                    }
                                }
                            }
                        }
                        SetSurveyValue(mFinalRowID, val.toString());
                        SetTextViewText(mFinalRowID, val.toString());
                    }
                    grpDialog.cancel();
                }
            }
        });
        grpDialog.show();
    }

    @SuppressLint("UseCompatLoadingForDrawables")
    @SuppressWarnings("deprecation")
    public void ShowSubActionLayoutDynamicView(String displayname, String type, String madatory, int repeat) {
        int tagcount = 0;

        final List<EditText> editTextList = new ArrayList<>();
        final List<String> mandatoryList = new ArrayList<>();
        final List<String> displaynameList = new ArrayList<>();
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_sub_action);
        grpDialog.setCancelable(false);
        final ImageView imageViewBack = grpDialog.findViewById(R.id.imageViewBack);
        imageViewBack.setVisibility(View.VISIBLE);
        imageViewBack.setOnClickListener(v -> grpDialog.cancel());
        final LinearLayout mSubParentLayout = grpDialog.findViewById(R.id.linearLayoutParent);
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

            if (!displayname.trim().isEmpty()) {
                if (displayname.contains(";")) {
                    String[] madatoryArray = madatory.split(";");
                    String[] splitdisplay = displayname.split(";");
                    String[] splittype = type.split(";");
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
                                        radioButton.setOnClickListener(view -> {
                                            int tag = (int) view.getTag();
                                            RadioButton b = (RadioButton) view;
                                            String buttonText = b.getText().toString();
                                            editTextList.get(tag).setText(buttonText);
                                        });

                                    } else {
                                        radioButton.setText(boolStringType2);
                                        radioButton.setTag(tagcount);
                                        radioButton.setOnClickListener(view -> {
                                            int tag = (int) view.getTag();
                                            RadioButton b = (RadioButton) view;
                                            String buttonText = b.getText().toString();
                                            editTextList.get(tag).setText(buttonText);
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

        final Button submit = grpDialog.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(arg0 -> {
            StringBuilder value = new StringBuilder();
            boolean AllDataValid = true;
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            if (!editTextList.isEmpty()) {

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
                        value.append(currentEditTextValue).append("#");
                    } else {
                        value.append(currentEditTextValue).append(";");
                    }
                    count++;
                }
            }
            if (AllDataValid) {
                SetSurveyValue(mFinalRowID, value.toString());
                SetTextViewText(mFinalRowID, value.toString());

                DisableDynamicViewButtonEditTextByRowId();
                grpDialog.cancel();
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

    public void ShowSubActionLayout(final ArrayList<SurveyInput> surveyInputList, final String rowid) {
        mdatePickerListForSubActionLayout = new ArrayList<>();
        DisplayMetrics metrics = getResources().getDisplayMetrics();

        int DeviceTotalWidth = metrics.widthPixels;
        int DeviceTotalHeight = metrics.heightPixels;


        int countFOrMasterView;
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_sub_action);
        Objects.requireNonNull(grpDialog.getWindow()).setLayout(DeviceTotalWidth * 95 / 100, DeviceTotalHeight * 45 / 100);
        grpDialog.setCancelable(false);
        final ImageView imageViewBack = grpDialog.findViewById(R.id.imageViewBack);
        //imageViewBack.setVisibility(GONE);

        mKeyValueSubList = new ArrayList<>();
        TextView title = grpDialog.findViewById(R.id.title);
        title.setText(mDisplayName);
        final LinearLayout mSubParentLayout = grpDialog.findViewById(R.id.linearLayoutParent);
        masterViewListForSubACtion = new ArrayList<>();
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
            if (type.equalsIgnoreCase("double")) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                childlayout.addView(AddEditText(rowid + count, type, hinttext));
                childlayout.addView(AddEmptyViewUnderEditText());
            } else if (type.equalsIgnoreCase("radio")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(Html.fromHtml(displayname));
                btn.setOnClickListener(v -> Log.i("TAG", "The index is" + btn.getText()));
                childlayout.addView(btn);
            } else if (type.equalsIgnoreCase("checkbox")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(Html.fromHtml(displayname));
                btn.setOnClickListener(v -> Log.i("TAG", "The index is" + btn.getText()));
                childlayout.addView(btn);

            } else if (type.equalsIgnoreCase("masterview")) {
                countFOrMasterView = count;
                mSubActionTag = rowid + count;
                commonDatabaseHelper objCommon = new commonDatabaseHelper();
                objCommon.setItem0(mSubActionTag);
                objCommon.setItem1(countFOrMasterView + "");
                masterViewListForSubACtion.add(objCommon);

            } else if (type.equalsIgnoreCase("date")) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                childlayout.addView(NewtextViewHeading(rowid, hinttext, 13f));
                childlayout.addView(AddDatePicker(rowid + count));
                //childlayout.addView(AddEditText(String.valueOf(rowid + count), type, hinttext));
                //childlayout.addView(AddEmptyViewUnderEditText());
            } else if (type.trim().length() == 0) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                childlayout.addView(NewtextViewHeading(rowid, hinttext, 13f));
                childlayout.addView(AddEditText(rowid + count, type, hinttext));
                childlayout.addView(AddEmptyViewUnderEditText());
            }
            mSubParentLayout.addView(childlayout);
        }
        Button submit = grpDialog.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(arg0 -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            StringBuilder val = new StringBuilder();
            mActionPressed += ":";
            boolean isValueOK = true;
            if (!mEditTextList.isEmpty()) {
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
            if (!mdatePickerListForSubActionLayout.isEmpty()) {

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
                            if (mKeyValueSubList.get(county).getValue().length() <= 0) {
                                isValueOK = false;
                            }
                            break;
                        }
                    }
                }
            }
            if (isValueOK) {
                for (int count = 0; count < mKeyValueSubList.size(); count++) {
                    val.append(mKeyValueSubList.get(count).getEnteredValue()).append("#");
                    mActionPressed += mKeyValueSubList.get(count).getValue() + "#";
                }
                if (val.toString().endsWith("#") && mActionPressed.endsWith("#")) {
                    val = new StringBuilder(val.substring(0, val.length() - 1));
                    mActionPressed = mActionPressed.substring(0, mActionPressed.length() - 1);
                }
                SetSurveyValue(mFinalRowID, mActionPressed);
                SetTextViewText(mFinalRowID, val.toString());
                mActionPressed = "";
                grpDialog.cancel();
            } else {
                Utils.showToast(mContext, "Please provide input");
                //grpDialog.cancel();
            }
        });

        imageViewBack.setOnClickListener(v -> grpDialog.dismiss());

        grpDialog.show();
        if (!masterViewListForSubACtion.isEmpty()) {
            ismultipleMasterViewInSubACtion = false;
            grpDialog.getWindow().setLayout(DeviceTotalWidth * 95 / 100, DeviceTotalHeight * 45 / 100);
            mSubActionTag = masterViewListForSubACtion.get(0).getItem0();
            onSubActionLayoutButtonClickForMasterView(Integer.parseInt(masterViewListForSubACtion.get(0).getItem1()), surveyInputList);
            if (masterViewListForSubACtion.size() > 1) {
                ismultipleMasterViewInSubACtion = true;
                surveyInputListForSubActionMasterView = surveyInputList;
            }
        }
//        if (typeForMasterView.matches("masterview")) {
//            //grpDialog.getWindow().setLayout(600,390);
//            grpDialog.getWindow().setLayout(DeviceTotalWidth * 95 / 100, DeviceTotalHeight * 45 / 100);
//            onSubActionLayoutButtonClickForMasterView(countFOrMasterView, surveyInputList);
//        }

    }

    private void onSubActionLayoutButtonClickForMasterView(int count, ArrayList<SurveyInput> surveyInputList) {
        String tablenamedetails = surveyInputList.get(count).getSurveyTableName();
        String clause = surveyInputList.get(count).getSurveyClause().trim();
        currentItemDisplayName = surveyInputList.get(count).getSurveyDisplayName().trim();
        if (tablenamedetails.contains("%")) {
            String[] splitablename = tablenamedetails.split("%");
            if (splitablename.length == 3) {
                mTableName = splitablename[0];
                mSendColumn = splitablename[1];
                mShowColumn = splitablename[2];
                mDecision = "FORWARD";
                if (!clause.isEmpty()) {
                    if (clause.contains("&&")) {
                        String[] noofclause = clause.split("&&");
                        for (int clausecount = 0; clausecount < noofclause.length; clausecount++) {
                            String[] splitwhere = noofclause[clausecount].split(";");
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
                        String[] splitclause = clause.split(";");
                        mWhereClause = splitclause[0] + "='" + splitclause[1] + "'";
                        PrepareSurveyData(9, "");
                    }
                } else {
                    PrepareSurveyData(7, "");
                }
            } else if (splitablename.length == 4) {
                mTableName = splitablename[0];
                mSendColumn = splitablename[1];
                mShowColumn = splitablename[2];
                mDecision = "FORWARD";
                if (!clause.isEmpty()) {
                    if (clause.contains("&&")) {
                        String[] noofclause = clause.split("&&");
                        for (int clausecount = 0; clausecount < noofclause.length; clausecount++) {
                            String[] splitwhere = noofclause[clausecount].split(";");
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
                        if (clause.contains("!=")) {
                            String[] splitclause = clause.split("!=");
                            String[] cls = splitclause[0].split(";");
                            mWhereClause = cls[0] + "!='" + splitclause[1] + "'";
                            PrepareSurveyData(9, "");
                        } else {
                            String[] splitclause = clause.split(";");
                            mWhereClause = splitclause[0] + "='" + splitclause[1] + "'";
                            PrepareSurveyData(9, "");
                        }
                    }
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
            if (!validationText.isEmpty()) {
                if (validationText.startsWith("RA") && validationText.contains("#")) {
                    String[] splitvalidation = validationText.split("#");
                    if (splitvalidation[0].equalsIgnoreCase(rowID) && splitvalidation[1].equals(checkedvalue)) {
                        for (int i = 0; i < mButtonList.size(); i++) {
                            if (mButtonList.get(i).getTag().toString().equalsIgnoreCase(rowid)) {
                                mButtonList.get(i).setVisibility(View.VISIBLE);
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

    public DatePicker AddDatePicker(String id) {
        DisplayMetrics metrics = getResources().getDisplayMetrics();


//        buttonLayout.setLayoutParams(buttonLayoutParams);
        DatePicker datePicker = new DatePicker(mContext);
        LayoutParams buttonParams = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        buttonParams.gravity = Gravity.CENTER_VERTICAL;
        datePicker.setLayoutParams(buttonParams);
        datePicker.setScaleX(0.7f);
        datePicker.setScaleY(0.7f);
        datePicker.setTag(id);
        datePicker.setPadding(0, -5, 0, -5);
//        datePicker.setHorizontallyScrolling(true);
//        datePicker.setGravity(Gravity.CENTER);
//        datePicker.setText(displayname);
//        datePicker.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
//        datePicker.setSingleLine(false);
//        datePicker.setOnClickListener(this);
//        buttonLayout.addView(datePicker);
//        mParentLayout.addView(buttonLayout);
        mdatePickerListForSubActionLayout.add(datePicker);
        return datePicker;

    }

    @SuppressLint("SetTextI18n")
    public void ShowActionLayout(String displayname, final String actiontype, final String mandatory, final String tablename, final String validation) {

        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_ation);
        grpDialog.setCancelable(false);

        TextView title = grpDialog.findViewById(R.id.textView1);
        title.setText(Html.fromHtml(displayname));

        final EditText mEditText = grpDialog.findViewById(R.id.editText1);
        if (actiontype.equalsIgnoreCase("double")) {
            mEditText.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        }
        Button mSelectButton = grpDialog.findViewById(R.id.button2);
        mSelectButton.setText("Select " + displayname);

        if (!tablename.isEmpty()) {
            mEditText.setVisibility(View.GONE);
        } else {
            mSelectButton.setVisibility(View.GONE);
        }

        mSelectButton.setOnClickListener(v -> {
            mType = actiontype;
            PrepareSurveyData(2, tablename);
        });

        Button submit = grpDialog.findViewById(R.id.button1);
        submit.setOnClickListener(arg0 -> {

            getWindow()
                    .setSoftInputMode(
                            WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

            if (!tablename.isEmpty()) {
                grpDialog.cancel();
            } else {
                if (mandatory.equalsIgnoreCase("Y")) {
                    String editval = mEditText.getText().toString();
                    if (ContainsOnlyNumbers(validation)) {
                        if (editval.length() == Integer.parseInt(validation)) {
                            SetSurveyValue(mFinalRowID, editval);
                            SetTextViewText(mFinalRowID, editval);
                            grpDialog.cancel();
                        } else {
                            Toast.makeText(mContext, "Please provide valid Input", Toast.LENGTH_LONG).show();
                        }
                    } else if (!editval.isEmpty()) {
                        SetSurveyValue(mFinalRowID, editval);
                        SetTextViewText(mFinalRowID, editval);
                        grpDialog.cancel();
                    } else {
                        Toast.makeText(mContext, "Please provide valid Input", Toast.LENGTH_LONG).show();
                    }
                } else {
                    SetSurveyValue(mFinalRowID, mEditText.getText().toString());
                    SetTextViewText(mFinalRowID, mEditText.getText().toString());
                    grpDialog.cancel();
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

    private void SetTextViewTextBlank(String tag) {
        for (int count = 0; count < mTextViewList.size(); count++) {
            if (mTextViewList.get(count).getTag().toString().equalsIgnoreCase(tag)) {
                mTextViewList.get(count).setText("");
                mTextViewList.get(count).setTextColor(Color.parseColor("#FF9600"));
                mTextViewList.get(count).setTypeface(null, Typeface.BOLD);
                //mTextViewList.get(count).setVisibility(GONE);
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

    private void SetEditTextText(String tag, String text) {
        for (int count = 0; count < mEditTextList.size(); count++) {
            if (mEditTextList.get(count).getTag().toString().equalsIgnoreCase(tag)) {
                mEditTextList.get(count).setText(text);
                break;
            }
        }
    }

    @SuppressLint("UseCompatLoadingForDrawables")
    @SuppressWarnings("deprecation")
    private EditText AddEditTextDisabled(String tag, String edtype, String hinttext) {
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
//					|InputType.TYPE_TEXT_FLAG_CAP_SENTENCES
                    | InputType.TYPE_TEXT_FLAG_AUTO_CORRECT);

        }
        editText.setSingleLine(false);
        editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        editText.setTextColor(Color.GRAY);
        editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
        editText.setTypeface(null, Typeface.NORMAL);
        if (!mPredefinedValue.trim().isEmpty()) {

            //write down the code for otp
            editText.setText(mPredefinedValue);
            mPredefinedValue = "";
        } else {
            editText.setHint(Html.fromHtml(hinttext));
        }
        editText.setEnabled(false);
        editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
        editText.setHintTextColor(Color.LTGRAY);
        editText.addTextChangedListener(new GenericTextWatcher(editText.getTag().toString(), "", ""));
        mEditTextList.add(editText);
        return editText;
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
//					|InputType.TYPE_TEXT_FLAG_CAP_SENTENCES
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
        editText.addTextChangedListener(new GenericTextWatcher(editText.getTag().toString(), "", ""));
        mEditTextList.add(editText);
        return editText;
    }

    private EditText AddEditText(String tag, String edtype, String hinttext, String action, String validation) {
        EditText editText = new EditText(this);
        editText.setTag(tag);
        LayoutParams params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        editText.setLayoutParams(params);
        if (edtype.equalsIgnoreCase("double")) {
            if (validation.equalsIgnoreCase("numeric")) {
                editText.setInputType(InputType.TYPE_CLASS_NUMBER);
            } else {
                editText.setInputType(InputType.TYPE_CLASS_NUMBER
                        | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            }
        } else {
            editText.setInputType(InputType.TYPE_CLASS_TEXT
                    | InputType.TYPE_TEXT_FLAG_MULTI_LINE
//					|InputType.TYPE_TEXT_FLAG_CAP_SENTENCES
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
        if (hinttext.toLowerCase().contains("bags") && !hinttext.toLowerCase().contains("price per bags")) {
            editText.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            editText.setKeyListener(DigitsKeyListener.getInstance("0123456789"));
        }


        editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
        editText.setHintTextColor(Color.LTGRAY);
        editText.addTextChangedListener(new GenericTextWatcher(editText.getTag().toString(), action, validation));
        mEditTextList.add(editText);
        if (Constants.nickName.equalsIgnoreCase("NIMBUS") && tag.equalsIgnoreCase("RA016")) {
            //editText.setVisibility(GONE);
        }
        return editText;
    }

    private class GenericTextWatcher implements TextWatcher {

        private final String tag;
        private final String action;
        private final String validation;

        private GenericTextWatcher(String tag, String action, String validation) {
            this.tag = tag;
            this.action = action;
            this.validation = validation;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String userInput = editable.toString();
            //changing data for conditional view
            if (action.contains("#") && (action.contains("<=") || action.contains(">=")) && action.contains("RA"))//RA043#<=9:12;>=10&<=20:20;>=21&<=40:30;>=41:50
            {
                String[] splittedAction = action.split("#");
                String RowId = splittedAction[0];
                Boolean IsCurrentRowIdConditionalView = false;
                for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
                    String currentrowid = mInputTimeSurveyDetailsList.get(count).getRowId();
                    if (RowId.equalsIgnoreCase(currentrowid)) {
                        String currentType = mInputTimeSurveyDetailsList.get(count).getType();
                        if (currentType.equalsIgnoreCase("conditonalview")) {
                            IsCurrentRowIdConditionalView = true;
                            break;
                        }

                    }
                }
                if (IsCurrentRowIdConditionalView) {
                    if (userInput.isEmpty()) {
                        SetSurveyValue(RowId, "");
                        SetEditTextText(RowId, "");
                    } else {
                        String finalValue = "";
                        String actionStringSecondPart = splittedAction[1];//<=9:12;>=10&<=20:20;>=21&<=40:30;>=41:50
                        if (actionStringSecondPart.contains(";")) {
                            String[] actionStringSecondPartSplitted = actionStringSecondPart.split(";");//<=9:12  >=10&<=20:20  >=21&<=40:30  >=41:50
                            for (int x = 0; x < actionStringSecondPartSplitted.length; x++) {
                                if (actionStringSecondPartSplitted[x].contains(":")) {
                                    String[] finalValueSplitted = actionStringSecondPartSplitted[x].split(":");
                                    String logic = finalValueSplitted[0];
                                    String value = finalValueSplitted[1];
                                    if (Utils.doesQuantityMatchesQtySlabModified(userInput, logic)) {
                                        finalValue = value;
                                        break;
                                    }
                                }
                            }
                        }
                        SetSurveyValue(RowId, finalValue);
                        SetEditTextText(RowId, finalValue);
                    }

                }

            }

            String validatioOfCurrentRow = validation;
            //changing for <=rowId validation
            if (!validatioOfCurrentRow.isEmpty() && validatioOfCurrentRow.contains("<=") && validatioOfCurrentRow.contains("RA"))//<=RA043
            {

                String currentRowIdForValidation = validatioOfCurrentRow.replace("<=", "");//RA043
                String currentvalueForRow = "";
                for (int countX = 0; countX < mEditTextList.size(); countX++) {
                    if (mEditTextList.get(countX).getTag().toString().equalsIgnoreCase(currentRowIdForValidation)) {
                        currentvalueForRow = mEditTextList.get(countX).getText().toString();
                        break;
                    }
                }
                if (Utils.isNumeric(userInput)) {
                    if (currentvalueForRow.isEmpty()) {
                        mEdiTextDynamic.removeTextChangedListener(this);
                        mEdiTextDynamic.setText("");
                        String displaynameCurrent = getDisplayNameByRowId(currentRowIdForValidation);
                        Utils.showToast(mContext, "Please provide input for " + displaynameCurrent + " first.");
                        mEdiTextDynamic.addTextChangedListener(new GenericTextWatcher(tag, action, validation));
                    } else if (Double.parseDouble(userInput) > Double.parseDouble(currentvalueForRow)) {
                        mEdiTextDynamic.removeTextChangedListener(this);
                        mEdiTextDynamic.setText("");
                        String displaynameCurrent = getDisplayNameByRowId(currentRowIdForValidation);
                        Utils.showToast(mContext, "Value can not be grater than " + displaynameCurrent + " 's value.");
                        mEdiTextDynamic.addTextChangedListener(new GenericTextWatcher(tag, action, validation));
                    }
                }


            }

            if (Constants.nickName.equalsIgnoreCase("NIMBUS") && Utils.isNumeric(userInput) && !tag.equals("RA016") && !mFinalRowID.equals("RA016") && !mFinalRowID.equals("RA013") && !mFinalRowID.equals("RA014") && !mFinalRowID.equals("RA001")) {
                String currentvalueForRow = "";
                double total = 0.0, tt = 0.0;
                for (int countX = 0; countX < mEditTextList.size(); countX++) {
                    currentvalueForRow = mEditTextList.get(countX).getText().toString();
                    if (Utils.isNumeric(currentvalueForRow)) {
                        if (!mEditTextList.get(countX).getTag().toString().equalsIgnoreCase(mFinalRowID)) {
                            if (countX != 13) {
                                tt = tt + Double.parseDouble(currentvalueForRow);
                            }
                            //currentvalueForRow=mEditTextList.get(countX).getText().toString();

                        }

                    }

                   /* if (mEditTextList.get(countX).getTag().toString().equalsIgnoreCase(currentRowIdForValidation)) {
                        currentvalueForRow=mEditTextList.get(countX).getText().toString();
                        break;
                    }*/
                }
                total = tt;//+ Double.parseDouble(userInput);
                //Utils.showToast(mContext,""+total);

                SetSurveyValue(mFinalRowID, "" + total);

                for (int tx = 0; tx < mTextViewList.size(); tx++) {
                    TextView currentButton = mTextViewList.get(tx);
                    if (currentButton.getTag().equals("RA016")) {
                        //mTextViewList.get(tx).setText("Total : "+total);
                        mEditTextList.get(13).setText("" + total);
                        //mEditTextList.get(tx).setText(""+total);
                        mEditTextList.get(13).setEnabled(false);

                        //Utils.showToast(mContext,""+mEditTextList.get(tx).getTag().toString());

                    }
                }
               /* for(int tx = 0; tx < mEditTextList.size(); tx++) {
                    //EditText currentButton = mEditTextList.get(tx);
                    if (mEditTextList.get(tx).getTag().toString().equals("RA016")) {

                        mEditTextList.get(tx).setText(""+total);
                        //mEditTextList.get(tx).setEnabled(false);

                        //Utils.showToast(mContext,""+mEditTextList.get(tx).getTag().toString());

                    }
                }*/
            }

        }
    }

    private String getDisplayNameByRowId(String currentRowIdForValidation) {
        String displayName = "";
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            String currentrowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (currentRowIdForValidation.equalsIgnoreCase(currentrowid)) {
                displayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                break;
            }
        }
        return displayName;
    }

    private View AddEmptyViewUnderEditText() {
        View viewObject = new View(this);
        LayoutParams params = new LayoutParams(LayoutParams.MATCH_PARENT, 24);
        viewObject.setBackgroundColor(getResources().getColor(R.color.transparent));
        viewObject.setLayoutParams(params);
        return viewObject;
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

    private TextView NewtextViewWithGoneStatus(String tag) {
        LayoutParams Params = new LayoutParams(
                LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 1);
        TextView textView = new TextView(this);
        textView.setLayoutParams(Params);
        textView.setTag(tag);
        textView.setVisibility(GONE);
        mTextViewList.add(textView);
        return textView;
    }

    private TextView NewtextViewHeading(String tag, String displayname, float size) {
        LayoutParams Params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 1);
//        Params.setMargins(left, top, right, bottom);
        Params.setMargins(0, 0, 0, 0);
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
        String rowid = "";
        String validationText = "";
        String displayname = "";
        boolean isSucess = true;
        boolean isvalue = false;

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            validationText = mInputTimeSurveyDetailsList.get(count).getValidation().trim();
            displayname = mInputTimeSurveyDetailsList.get(count).getDisplayName();
            if (!validationText.isEmpty()) {
                if (rowID.equalsIgnoreCase(rowid)) {
                    if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("Y")) {
                        if (ContainsOnlyNumbers(validationText)) {
                            if (Integer.parseInt(validationText) != value.length()) {
                                isSucess = false;
                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                            }
                        } else if (validationText.matches("mobile")) {
                            if (value.length() != 10) {
                                isSucess = false;
                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                            }
                        } else if (validationText.matches("email")) {
                            if (!Utils.isValidMail(value)) {
                                isSucess = false;
                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                            }
                        } else {
                            if (validationText.startsWith("RA")) {
                                if (validationText.contains("#")) {
                                    String[] validationsplit = validationText.split("#");
                                    if (CheckGivenRowIdHasValue(validationsplit[0])) {
                                        if (validationsplit[1].equalsIgnoreCase(">=")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!(Double.parseDouble(mCheckValue) >= Double.parseDouble(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, mDependentDisplayName + " value should be greater or equal than " + displayname, Toast.LENGTH_LONG).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, mDependentDisplayName + " value should be greater or equal than " + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        } else if (validationsplit[1].equalsIgnoreCase("<=")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!(Double.parseDouble(mCheckValue) <= Double.parseDouble(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, mDependentDisplayName + " value should be less or equal than " + displayname, Toast.LENGTH_LONG).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, mDependentDisplayName + " value should be less or equal than " + displayname, Toast.LENGTH_LONG).show();
                                            }

                                        } else if (validationsplit[1].equalsIgnoreCase("=")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!Integer.valueOf(mCheckValue).equals(Integer.valueOf(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, displayname + " value should be equal to " + mDependentDisplayName, Toast.LENGTH_LONG).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                }

                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, displayname + " value should be equal to " + mDependentDisplayName, Toast.LENGTH_LONG).show();
                                            }
                                        } else if (validationsplit[1].equalsIgnoreCase("<")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!(Double.parseDouble(mCheckValue) < Double.parseDouble(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, mDependentDisplayName + " value should be less than " + displayname, Toast.LENGTH_LONG).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, mDependentDisplayName + " value should be less than " + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        } else if (validationsplit[1].equalsIgnoreCase(">")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!(Double.parseDouble(mCheckValue) > Double.parseDouble(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, mDependentDisplayName + " value should be greater than " + displayname, Toast.LENGTH_LONG).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, mDependentDisplayName + " value should be greater than " + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        } else {
                                            if (value.isEmpty()) {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        }
                                    }
                                } else {
                                    if (CheckGivenRowIdHasValue(validationText)) {
                                        if (value.isEmpty()) {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                        }
                                    }
                                }
                            } else if (validationText.startsWith("Round")) {
                                if (validationText.contains("#")) {
                                    String[] validation = validationText.split("#");
                                    if (validation[1].trim().contains(";")) {
                                        String[] roundsplit = validation[1].trim().split(";");
                                        if (!value.trim().isEmpty()) {
                                            if (value.contains(".")) {
                                                String[] splitvalue = value.split("\\.");
                                                if (splitvalue[0].trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                    isRound = true;
                                                    double val = Double.parseDouble(value);
                                                    mRoundValue = defaultFormat.format(val);
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                }
                                            } else {
                                                if (value.trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                    isRound = true;
                                                    double val = Double.parseDouble(value);
                                                    mRoundValue = defaultFormat.format(val);
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                }
                                            }
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
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
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                    }
                                } else if (validationText.contains("<=")) {
                                    String[] items = validationText.split("=");
                                    if (value.length() <= Integer.parseInt(items[1])) {
                                        isSucess = true;
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input(" + validationText + " characters) in " + displayname, Toast.LENGTH_LONG).show();
                                    }
                                } else if (validationText.contains(">=")) {
                                    String[] items = validationText.split("=");
                                    if (value.length() >= Integer.parseInt(items[1])) {
                                        isSucess = true;
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input(" + validationText + " characters) in " + displayname, Toast.LENGTH_LONG).show();
                                    }
                                } else if (validationText.contains("numeric")) {
                                    if (value.length() >= 0) {
                                        isSucess = true;
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
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
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                        }
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                    }
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                }
                            }
                        }
                    } else {
                        if (value.length() > 0) {
                            if (ContainsOnlyNumbers(validationText)) {
                                if (Integer.valueOf(validationText) == value
                                        .length()) {
                                    isSucess = true;
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in "
                                            + displayname, Toast.LENGTH_LONG).show();
                                }
                            } else if (validationText.matches("mobile")) {
                                if (Utils.isValidIndianMobile(value)) {
                                    isSucess = true;
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                }

                            } else if (validationText.matches("email")) {
                                if (Utils.isValidMail(value)) {
                                    isSucess = true;
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                }
                            } else {
                                if (validationText.startsWith("RA")) {
                                    if (validationText.contains(":")) {
                                        String[] validationsplit = validationText.split("\\:");
                                        if (CheckGivenRowIdHasValue(validationsplit[0])) {
                                            if (value.length() >= Integer.parseInt(validationsplit[1])) {
                                                isSucess = true;
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in "
                                                        + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        } else {
                                            isSucess = true;
                                        }
                                    } else if (validationText.contains("#")) {
                                        String[] validationsplit = validationText.split("\\#");
                                        if (validationsplit[0].equalsIgnoreCase("RA415") && validationsplit[1].equalsIgnoreCase("Other Reason")) {
                                            isSucess = true;
                                        } else if (CheckGivenRowIdHasValue(validationText)) {
                                            if (value.length() > 0) {
                                                isSucess = true;
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in "
                                                        + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        } else {
                                            isSucess = true;
                                        }

                                    } else {
                                        if (CheckGivenRowIdHasValue(validationText)) {
                                            if (value.length() > 0) {
                                                isSucess = true;
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in "
                                                        + displayname, Toast.LENGTH_LONG).show();
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
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                    }
                                                } else {
                                                    if (value.trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                        isSucess = true;
                                                        isRound = true;
                                                        double val = Double.valueOf(value);
                                                        mRoundValue = defaultFormat.format(val);
                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                                    }
                                                }
                                            } else {
                                                isSucess = true;
                                                //Toast.makeText(mContext,"Please provide valid input in "+ displayname + "",2000).show();
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
                                            Toast.makeText(mContext, "Please provide valid input in "
                                                    + displayname, Toast.LENGTH_LONG).show();
                                        }
                                    } else if (validationText.contains("<=")) {
                                        String[] items = validationText.split("=");
                                        if (value.length() <= Integer.parseInt(items[1])) {
                                            isSucess = true;
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input(" + validationText + " characters) in " + displayname, Toast.LENGTH_LONG).show();
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                        }
                                    } else if (validationText.contains(">=")) {
                                        String[] items = validationText.split("=");
                                        if (value.length() >= Integer.parseInt(items[1])) {
                                            isSucess = true;
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input(" + validationText + " characters) in " + displayname, Toast.LENGTH_LONG).show();
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
                                                    Toast.makeText(mContext, "Please provide valid input in "
                                                            + displayname, Toast.LENGTH_LONG).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in "
                                                        + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in "
                                                    + displayname, Toast.LENGTH_LONG).show();
                                        }
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in "
                                                + displayname, Toast.LENGTH_LONG).show();
                                    }
                                }

                            }
                        } else {
                            if (validationText.startsWith("RA")) {

                                if (validationText.contains(":")) {
                                    String[] validationsplit = validationText.split("\\:");
                                    if (CheckGivenRowIdHasValue(validationsplit[0])) {
                                        if (value.length() >= Integer.parseInt(validationsplit[1])) {
                                            isSucess = true;
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in "
                                                    + displayname, Toast.LENGTH_LONG).show();
                                        }
                                    } else {
                                        isSucess = true;
                                    }
                                } else {

                                    if (validationText.contains("#")) {
                                        String[] validationsplit = validationText.split("#");
                                        if (validationsplit[0].equalsIgnoreCase("RA415") && validationsplit[1].equalsIgnoreCase("Other Reason")) {
                                            isSucess = true;
                                        } else if (CheckGivenRowIdHasValue(validationText)) {
                                            if (value.length() > 0) {
                                                isSucess = true;
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in "
                                                        + displayname, Toast.LENGTH_LONG).show();
                                            }
                                        } else {
                                            isSucess = true;
                                        }

                                    } else if (CheckGivenRowIdHasValue(validationText)) {
                                        if (value.length() > 0) {
                                            isSucess = true;
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_LONG).show();
                                        }
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

        //////////////////////  RA001!=  ////////////////////

        for (int count = 0; count < mSurveyInputList.size(); count++) {
            if (!isvalue) {
                rowid = mSurveyInputList.get(count).getSurveyRowId();
                validationText = mSurveyInputList.get(count).getSurveyValidation().trim();
                displayname = mSurveyInputList.get(count).getSurveyDisplayName();
                if (!validationText.isEmpty()) {

                    if (validationText.contains("!=")) {
                        String[] splitablename = validationText.split("!=");
                        String checkRowId = splitablename[0];
                        if (CheckGivenRowIdHasValue(checkRowId)) {
                            if (CheckGivenRowIdHasValue(rowid)) {
                                isSucess = true;
                            } else {
                                isSucess = false;
                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                            }
                        }
                    }
                }
            }
        }
        return isSucess;
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

    public boolean CheckSurveyMandatory() {
        String value;
        String displayname;
        boolean isSucess = true;
        boolean isvalue = false;

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            if (!isvalue) {
                value = mInputTimeSurveyDetailsList.get(count).getValue();
                displayname = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("Y")) {
                    if (!value.isEmpty()) {
                        String actionString = mInputTimeSurveyDetailsList.get(count).getAction();
                        if (actionString.contains("Click:")) {
                            String rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
                            if (!ImageMinValidation(rowid)) {
                                Toast.makeText(mContext, "Please provide minimum input in " + displayname, Toast.LENGTH_LONG).show();
                                isSucess = false;
                                break;
                            }
                        }
                    } else {
                        Toast.makeText(mContext, "Please provide mandatory input in " + displayname, Toast.LENGTH_LONG).show();
                        isSucess = false;
                        break;
                    }
                } else if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("DEPENDENT")) {
                    String valid_txt = mInputTimeSurveyDetailsList.get(count).getValidation();
                    String checkRowID = "";

                    if (valid_txt.contains("#")) {
                        String[] splitablename = valid_txt.split("#");
                        if (splitablename.length > 0) {
                            checkRowID = splitablename[0];
                            valid_txt = splitablename[1];
                        }
                    }

                    for (int count1 = 0; count1 < mInputTimeSurveyDetailsList.size(); count1++) {
                        String value1 = mInputTimeSurveyDetailsList.get(count1).getValue();
                        if (checkRowID.matches(mInputTimeSurveyDetailsList.get(count1).getRowId())) {
                            if (valid_txt.contains(value1)) {
                                if (value.isEmpty()) {
                                    Toast.makeText(mContext, "Please provide mandatory input in " + displayname, Toast.LENGTH_LONG).show();
                                    isSucess = false;
                                    isvalue = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        return isSucess;
    }

    public boolean CheckGivenRowIdHasValue(String rowID) {
        String rowid = "";
        boolean isSucess = true;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mDependentDisplayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                if (!mInputTimeSurveyDetailsList.get(count).getValue().trim().isEmpty()) {
                    mCheckValue = mInputTimeSurveyDetailsList.get(count).getValue();
                } else {
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
                obj = null;
            }
        } else {
            KeyValue obj = new KeyValue();
            obj.setKey(rowid);
            obj.setValue(value);
            mKeyValueImageList.add(obj);
            obj = null;
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

    private boolean ImageMinValidation(String rowid) {
        boolean isempty = false;
        for (int count = 0; count < mKeyValueImageList.size(); count++) {
            if (rowid.equalsIgnoreCase(mKeyValueImageList.get(count).getKey())) {
                getActionOfCurrentItem(rowid);
                String ss = actionStringOfCurrentSelectedItem;
                int max = 1;
                if (ss.contains("Click:")) {
                    max = Integer.parseInt(ss.split(":")[1]);
                }
                //int max = Integer.parseInt(mKeyValueImageList.get(count).getValue());
                int captured = Integer.parseInt(mKeyValueImageList.get(count).getEnteredValue());
                isempty = captured == max;
            }
        }
        return isempty;
    }

    public void ShowImageCaptureLayer(String rowid) {
        if (!(ImageValidation(rowid))) {
            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_LONG).show();
        } else {
            @SuppressLint("SimpleDateFormat") String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
            mSImageName = imageName;
            mImageName = imageName + "; ";
            mImagePath = Utils.getAppStoragePath(mContext) + imageName;
            mImageFile = new File(mImagePath);
            try {
                boolean newFile = mImageFile.createNewFile();
            } catch (IOException e) {
                Log.d("TAG", "ShowImageCaptureLayer: " + e.getMessage());
            }

//			mFileUri = Uri.fromFile(mImageFile);
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
        String rowid = "";
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
            int ROW = 0;
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
            int ROW = 0;
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
                    if (CheckGivenRowIdHasValue(dependentrowid)) {
                        String depedentvalue = obj.getDependentValue();
                        if (mCheckValue.equalsIgnoreCase(depedentvalue)) {
                            String data = obj.getValue();
                            if (!data.trim().isEmpty()) {
                                ParseTableViewData(data);
                                break;
                            } else {
                                Utils.showToast(mContext, "Error in table view data.\nPlease Synchronize Data");
                                break;
                            }
                        }
                    } else {
                        Utils.showToast(mContext, "Please select " + mDependentDisplayName);
                        break;
                    }
                } else {
                    String data = obj.getValue();
                    if (!data.trim().isEmpty()) {
                        ParseTableViewData(data);
                        break;
                    } else {
                        Utils.showToast(mContext, "Error in table view data.\nPlease Synchronize Data");
                        break;
                    }
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
                    if (CheckGivenRowIdHasValue(dependentrowid)) {
                        String depedentvalue = obj.getDependentValue();
                        if (mCheckValue.equalsIgnoreCase(depedentvalue)) {
                            String data = obj.getValue();
                            if (!data.trim().isEmpty()) {
                                ParseTableViewData(data);
                                break;
                            } else {
                                Utils.showToast(mContext, "Error in table view data.\nPlease Synchronize Data");
                                break;
                            }
                        }
                    } else {
                        Utils.showToast(mContext, "Please select " + mDependentDisplayName);
                        break;
                    }
                } else {
                    String data = obj.getValue();
                    if (!data.trim().isEmpty()) {
                        ParseTableViewData(data);
                        break;
                    } else {
                        Utils.showToast(mContext, "Error in table view data.\nPlease Synchronize Data");
                        break;
                    }
                }
            }
        }
    }

    public void GetMultiLevelTableViewData(String value) {
        boolean isDependantDataFound = false;
        StringBuilder data = new StringBuilder();
        StringBuilder data1 = new StringBuilder();
        int numberOfMatch = 0;
        mType = "";
        for (int count = 0; count < mSurveyTableViewList.size(); count++) {
            SurveyTableView obj = mSurveyTableViewList.get(count);
            if (obj.getDependentValue().equalsIgnoreCase(value) && obj.getRowId().equalsIgnoreCase(currentRowId)) {
                if (!obj.getValue().trim().isEmpty()) {
                    numberOfMatch++;
                    if (numberOfMatch == 1) {
                        data1 = new StringBuilder(obj.getType());
                        data = new StringBuilder(obj.getValue());
                    } else {
                        data1.append("/").append(obj.getType());
                        data.append("/").append(obj.getValue());
                    }
                    isDependantDataFound = true;
                }
            }
        }
        mType = data1.toString();

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

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            SetNoofImageCaptured(mFinalRowID, mImageName);
            Constants.isSurveyImageTake = true;
            ImageView mImageViewCaptureImage = new ImageView(this);
            try {
                int mWidthForDisplay = 175;
                int mHeightForDisplay = 150;
                Bitmap bitmapToBeShown = decodeScaledBitmapFromSdCard(mImagePath, mWidthForDisplay, mHeightForDisplay);
                Bitmap bitmap = Utils.decodeBitmapFromSdCard(mImagePath);
                LayoutParams parms = new LayoutParams(mWidthForDisplay, mHeightForDisplay);
                int currentImageWidth = bitmap.getWidth();
                int currentImageHeight = bitmap.getHeight();
                double imageResolution = ((double) (currentImageWidth * currentImageHeight) / 1000000);
                if (imageResolution > 12) {
                    bitmap = Bitmap.createScaledBitmap(bitmap, 4000, 3000, true);
                }
                mImageViewCaptureImage.setLayoutParams(parms);
                mImageViewCaptureImage.setImageBitmap(bitmapToBeShown);
                FileOutputStream out;
                try {
                    out = new FileOutputStream(mImagePath);
                    bitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
                } catch (FileNotFoundException e) {
                    Log.d("TAG", "onActivityResult: " + e.getMessage());
                }
                mAceDnsTransactionDatabase.insertToSupportingAttachTable(mSImageName, "SURVEY");
                mImageName = GetImageName(mFinalRowID);
                SetSurveyValue(mFinalRowID, mImageName);
                mParentLayout.addView(mImageViewCaptureImage);
            } catch (Exception ex) {
                Toast.makeText(mContext, "Image is too large", Toast.LENGTH_LONG).show();
            }
        }
        if (resultCode == RESULT_CANCELED) {
            Constants.isSurveyImageTake = false;
            Log.i("Camera canceled", "Cancel");
        }
        if (requestCode == TAKE_PHOTO_CODE && resultCode == RESULT_OK) {
            if (!(ImageValidation(mFinalRowID))) {
                Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_LONG).show();
            } else {
                Constants.isSurveyImageTake = true;
                AlertDialog.Builder AlertDG = new AlertDialog.Builder(SurveyActivity.this);
                AlertDG.setTitle("Information");
                AlertDG.setMessage("Do you want to take another picture?");
                AlertDG.setPositiveButton("Yes", (dialog, which) -> {
                    @SuppressLint("SimpleDateFormat") String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                    mSImageName = imageName;
                    mImageName = imageName + "; ";
                    mImagePath = Utils.getAppStoragePath(mContext) + imageName;
                    mImageFile = new File(mImagePath);
                    try {
                        boolean newFile = mImageFile.createNewFile();
                    } catch (IOException e) {
                        Log.d("TAG", "onClick: " + e.getMessage());
                    }
                    try {
                        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                            mFileUri = FileProvider.getUriForFile(mContext, BuildConfig.APPLICATION_ID + ".provider", mImageFile);
                        } else {
                            mFileUri = Uri.fromFile(mImageFile);
                        }
                    } catch (Exception e) {
                        Log.d("TAG", "onClick: " + e.getMessage());
                    }
                    Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                    cameraIntent.putExtra(MediaStore.EXTRA_OUTPUT, mFileUri);
                    startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
                });
                AlertDG.setNegativeButton("No", (dialog, which) -> {
                    mImageName = GetImageName(mFinalRowID);
                    SetSurveyValue(mFinalRowID, mImageName);
                });
                AlertDG.setCancelable(true);
                AlertDG.create().show();
            }
        }
    }

    public String getGivenRowIdValue(String rowID) {
        String rowid;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            if (rowID.equalsIgnoreCase(rowid)) {
                mDependentDisplayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                if (!mInputTimeSurveyDetailsList.get(count).getValue().trim().isEmpty()) {
                    mCheckValue = mInputTimeSurveyDetailsList.get(count).getValue();
                } else {
                    mCheckValue = "";
                }
                break;
            }
        }
        return mCheckValue;
    }
}