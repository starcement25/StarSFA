package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
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

import androidx.annotation.NonNull;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSurveyOfferTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.KeyValueCheckAdapter;
import com.forcepower.acedns.adapter.KeyValueNormalAdapter;
import com.forcepower.acedns.adapter.ListCheckAdapter;
import com.forcepower.acedns.adapter.MallAdapter;
import com.forcepower.acedns.adapter.SurveyOutletAdapter;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.bean.MallSurveyRelation;
import com.forcepower.acedns.bean.SurveyDetails;
import com.forcepower.acedns.bean.SurveyInput;
import com.forcepower.acedns.bean.SurveyPublish;
import com.forcepower.acedns.bean.SurveyTableView;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DateTimeFormatter;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.MessageFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Objects;
import java.util.Set;

public class ActivitySurveyOffer extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    private static TextView mTitleText = null;
    @SuppressLint("StaticFieldLeak")
    private static LinearLayout mParentLayout = null;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonUndo = null;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonSubmit = null;

    public ArrayList<Button> mButtonList;
    public ArrayList<CheckBox> mCheckBoxList = new ArrayList<>();
    public ArrayList<TextView> mTextViewList = new ArrayList<>();
    public ArrayList<String> mNonRepeat;
    public AceDnsDatabase mAceDnsDatabase;
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    public ProgressDialog mPrepareSurveyProgressDialog;
    public Handler mPrepareSurveyHandler;
    public Handler mPrepareDataSaveHandler;
    public Context mContext;

    public boolean mCategory = false;
    public boolean mSubCategory = false;
    public boolean mIsTableView = false;
    public boolean mIsTittle = false;
    EditText mEdiTextDynamic = null;
    HashMap<String, ArrayList<CheckBox>> mCheckBoxMatrixListContainer = new HashMap<>();
    ArrayList<CheckBox> mMatrixCheckBoxList;
    HashMap<String, ArrayList<RadioGroup>> mRadioGroupMatrixListContainer = new HashMap<>();
    ArrayList<RadioGroup> mMatrixRadioGroupList;
    String[] mTypeList;
    ArrayList<MallMaster> mMallMasterList;
    Boolean isStartDateSet = false;
    RadioButton radioButton;
    String mDateValue = "";
    String mDateValueStart = "";
    int spliton = 0, mYear, mMonth, mDay;
    private ArrayList<SurveyDetails> mInputTimeSurveyDetailsList;
    private ArrayList<SurveyDetails> mUndoSurveyList;
    private ArrayList<MallSurveyRelation> mMallSurveyRelationList;
    private ArrayList<SurveyTableView> mSurveyTableViewList;
    private ArrayList<KeyValue> mKeyValueImageList;
    private ArrayList<KeyValue> mKeyValueSubList;
    private ArrayList<KeyValue> mKeyValueList;
    private final List<EditText> mEditTextList = new ArrayList<>();
    private ArrayList<SurveyPublish> mSurveyPublishList;
    private ArrayList<SurveyInput> mSurveyInputList;
    private String mSurveyId = "";
    private String mMallorHighStreetID = "", currentSelectedOutlet = "";
    private String mPinCode = "";
    private String mArea = "";
    private String currentDisplayName = "";
    private String mCheckValue = "";
    private String mDependentDisplayName = "";
    private String mDisplayName = "";
    private final String mMenuID = "";
    private final String mSurveyType = "";
    private String mPredefinedValue = "";
    private String mMallColumnName = "";
    private String mSubtableInfo = "";
    private String mEditType = "";
    private String mWhereClause = "";
    private String mSubRowID = "";
    private String mSubTableName = "";
    private String mSubColumnName = "";
    private String mSubDependent = "";
    private final String mLayout = "";
    private String mType = "";
    private String mSelectedType = "";
    private String mParentType = "";
    private String mDependentRowId = "";
    private String mFinalRowID = "";
    private final String mActionstring = "";
    private String mCondition = "";
    private String[] values;
    private String[] subvalues;
    private String mSImageName = "";
    private String mTableName = "";
    private String mSendColumn = "";
    private String mShowColumn = "";
    private String mColumnName = "";
    private String mMessage = "";
    private String mDependent = "";
    private final String mSubMenu = "";
    private String boolStringType1 = "";
    private String boolStringType2 = "";
    private String mDecision = "";
    private String mSubActionTag = "";
    private boolean isRound = false;
    private final int TAKE_PHOTO_CODE = 0;
    private ProgressDialog mPrepareSurveyMenuProgressDialog;
    private Handler mPrepareSurveyMenuHandler;
    private boolean mIsButtonEnable = true;
    private String mActionPressed = "";
    private String mRoundValue = "";

    @SuppressLint("HandlerLeak")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey);
        RegisterActivities.registerActivity(this);

        Constants.mFinalSurveyList = new ArrayList<>();
        mContext = ActivitySurveyOffer.this;
        InitializeView();

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
                ActivitySurveyOffer.this.runOnUiThread(() -> {
                    if (dojob == 1) {
                        mAceDnsDatabase.DeleteSurveyTempOutData();
                        new TRANS_SubmitSurveyOfferTask(mContext, true, output -> {
                            System.gc();
                            ShowAddAnotherOfferDialog();
                        }).execute();
                    }
                });
            }
        };

        mPrepareSurveyHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                ActivitySurveyOffer.this.runOnUiThread(() -> {
                    switch (dojob) {
                        case 1:
                            if (!mSurveyInputList.isEmpty()) {
                                DrawLayout();
                                if (!mIsTittle) {
                                    mTitleText.setText(mSubMenu);
                                }
                                if (mIsTableView) {
                                    mSurveyTableViewList = mAceDnsDatabase.GetSurveyTableView();
                                    if (mSurveyTableViewList.isEmpty()) {
                                        Toast.makeText(mContext, "Error in table view data.\nPlease Synchronize Data", Toast.LENGTH_SHORT).show();
                                    }
                                }
                            } else {
                                Toast.makeText(mContext, "Error in creating layout.Please Retry..", Toast.LENGTH_SHORT).show();
                            }
                            break;
                        case 2:
                            if (!mCategory && mSubCategory) {
                                if (!mCondition.trim().isEmpty()) {
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
                        case 6, 8:
                            if (values != null) {
                                ShowList(mType);
                            } else {
                                Utils.showToast(mContext, "No data found. Please Synchronize Data");
                            }
                            break;
                        case 7, 9:
                            if (mKeyValueList != null && !mKeyValueList.isEmpty()) {
                                ShowList(mType, mDecision);
                            } else {
                                Utils.showToast(mContext, "No data found. Please Synchronize Data");
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
                    }
                });
            }
        };

        mPrepareSurveyMenuHandler = new Handler() {
            public void handleMessage(@NonNull Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                ActivitySurveyOffer.this.runOnUiThread(() -> {
                    switch (dojob) {
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
                            if (!mSurveyInputList.isEmpty()) {
                                DrawLayout();
                            } else {
                                Toast.makeText(mContext, "Error in creating layout.Plese Retry..", Toast.LENGTH_LONG).show();
                            }
                            break;
                    }
                });
            }
        };

        if (Constants.isAddingAnotherOffer) {
            Constants.isAddingAnotherOffer = false;
            mMallorHighStreetID = Constants.mMallorHighStreetID;
            currentSelectedOutlet = Constants.currentSelectedOutlet;
            mSelectedType = Constants.mSelectedType;
            mType = Constants.mType;
            mSurveyId = Constants.mSurveyId;
            PrepareSurveyMenuData(6);
        } else {
            if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
                PrepareSurveyMenuData(2);
            } else {
                Utils.showToast(mContext, "You have no survey type. Please Synchronize Data");
            }
        }

        mButtonUndo.setOnClickListener(v -> {
            String rowid;
            String value;
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
                                    for (String s : splittemp) {
                                        mCondition = MessageFormat.format("{0}{1}", mCondition, "'" + s + "',");
                                    }
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

                if (mEditType.equalsIgnoreCase("tickbox") && value.equalsIgnoreCase("yes")) {
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
            boolean check;
            boolean checkvalidation = false;
            GetCheckBoxMatrixListContainer();
            GetRadioGroupMatrixListContainer();
            if (!mEditTextList.isEmpty()) {
                for (EditText editText : mEditTextList) {
                    String tag = editText.getTag().toString();
                    String value = editText.getText().toString();
                    boolean checksurveyvalue = SetSurveyValue(tag, value);
                    if (checksurveyvalue) {
                        checkvalidation = CheckSurveyValidation(tag, value);
                        if (isRound) {
                            SetSurveyValue(tag, mRoundValue);
                            isRound = false;
                        }
                    }
                    if (!checkvalidation) {
                        break;
                    }
                }
                check = CheckSurveyMandatory();
                if (check && checkvalidation) {
                    RemoveDuplicateData();
                    Constants.mFinalSurveyList.addAll(mInputTimeSurveyDetailsList);
                    if (InsertSurveyTemporary()) {
                        mButtonSubmit.setEnabled(false);
                        SaveDatatoDatabase(1);
                    }
                }

            } else {
                check = CheckSurveyMandatory();
                if (check) {
                    RemoveDuplicateData();
                    Constants.mFinalSurveyList.addAll(mInputTimeSurveyDetailsList);
                    if (InsertSurveyTemporary()) {
                        mButtonSubmit.setEnabled(false);
                        SaveDatatoDatabase(1);
                    }
                }
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public void ShowSurveyTypeListDialog() {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);
        TextView title = mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a type");
        ListView dialogList = mDialogDepotName.findViewById(R.id.list);
        for (int count = 0; count < mTypeList.length; count++) {
            mTypeList[count] = mTypeList[count].toUpperCase();
        }
        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.simple_list_child, R.id.list_details, mTypeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            mType = Objects.requireNonNull(adapter.getItem(pos)).toLowerCase();
            mSelectedType = Objects.requireNonNull(adapter.getItem(pos)).toLowerCase();
            mDialogDepotName.cancel();
            PrepareSurveyMenuData(3);
        });

        ImageView back = mDialogDepotName.findViewById(R.id.image_cancel);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(arg0 -> mDialogDepotName.cancel());
        Button cancel = mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        mDialogDepotName.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowMallHighStreetListDialog() {
        if (!mMallMasterList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(mContext, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);
            TextView title = mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select a " + mType);
            ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);
            if (mType.equalsIgnoreCase("hi-street")) {
                Constants.mSurveyType = "pincode";
            } else {
                Constants.mSurveyType = "mall";
            }
            final MallAdapter malladapter = new MallAdapter(mContext, R.layout.mall_list, mMallMasterList);
            dialogList.setAdapter(malladapter);

            EditText searchText = mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    malladapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                if (mType.equalsIgnoreCase("hi-street")) {
                    Constants.selectedMallMaster = malladapter.getItem(arg2);
                    assert Constants.selectedMallMaster != null;
                    mPinCode = Constants.selectedMallMaster.getPincode();
                    mMallorHighStreetID = mPinCode;
                    PrepareSurveyMenuData(4);
                } else {
                    Constants.selectedMallMaster = malladapter.getItem(arg2);
                    assert Constants.selectedMallMaster != null;
                    mMallorHighStreetID = Constants.selectedMallMaster.getMallId();
                    PrepareSurveyMenuData(5);
                }
            });

            ImageView back = mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(arg0 -> mMallHighStreetListDialog.cancel());

            Button cancel = mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);

            mMallHighStreetListDialog.show();
        } else {
            Utils.showToast(mContext, "You have no " + mType + " assigned");
            mButtonSubmit.setVisibility(View.GONE);
        }
    }

    @SuppressLint("SetTextI18n")
    private void ShowOutletDialog() {
        if (!mSurveyPublishList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(mContext, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);
            TextView title = mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select an outlet");
            ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);
            final SurveyOutletAdapter adapter = new SurveyOutletAdapter(mContext, R.layout.customer_broker_list_child, mSurveyPublishList);
            dialogList.setAdapter(adapter);
            EditText searchText = mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
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

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                mSurveyId = Objects.requireNonNull(adapter.getItem(arg2)).getSurveyId();
                currentSelectedOutlet = Objects.requireNonNull(adapter.getItem(arg2)).getValue();
                PrepareSurveyMenuData(6);
            });

            ImageView back = mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(arg0 -> mMallHighStreetListDialog.cancel());

            Button btnaddoutlet = mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            btnaddoutlet.setText("Add Outlet");
            btnaddoutlet.setOnClickListener(v -> {
                mMallHighStreetListDialog.cancel();
                PrepareSurveyMenuData(7);
            });
            mMallHighStreetListDialog.show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowAreaListDialog() {
        if (!mSurveyPublishList.isEmpty()) {
            final Dialog mMallHighStreetListDialog = new Dialog(mContext, R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);
            TextView title = mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select an area");
            ListView dialogList = mMallHighStreetListDialog.findViewById(R.id.list);

            final SurveyOutletAdapter adapter = new SurveyOutletAdapter(mContext, R.layout.customer_broker_list_child, mSurveyPublishList);
            dialogList.setAdapter(adapter);
            EditText searchText = mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int i, int i1, int i2) {
                    adapter.getFilter().filter(s.toString());
                }

                @Override
                public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                }

                @Override
                public void afterTextChanged(Editable editable) {
                }
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mMallHighStreetListDialog.cancel();
                mArea = Objects.requireNonNull(adapter.getItem(arg2)).getValue();
                mMallorHighStreetID = Objects.requireNonNull(adapter.getItem(arg2)).getMallId();
                PrepareSurveyMenuData(5);
            });

            ImageView back = mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(arg0 -> mMallHighStreetListDialog.cancel());

            Button cancel = mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);

            mMallHighStreetListDialog.show();
        } else {
            Utils.showToast(mContext, "You have no outlet assigned");
            mButtonSubmit.setVisibility(View.GONE);
        }
    }

    public void RemoveDuplicateData() {
        String rowid;
        for (int count = 0; count < mSurveyInputList.size(); count++) {
            rowid = mSurveyInputList.get(count).getSurveyRowId();
            for (int removeindex = 0; removeindex < Constants.mFinalSurveyList.size(); removeindex++) {
                if (rowid.trim().equalsIgnoreCase(Constants.mFinalSurveyList.get(removeindex).getRowId().trim())) {
                    Constants.mFinalSurveyList.remove(removeindex);
                }
            }
        }
    }

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
        new GPSTracker(mContext);
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

    @SuppressLint("SimpleDateFormat")
    public void SaveSurveyDataTODatabase() {
        String timeStamp;
        timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        mAceDnsTransactionDatabase.INSERTtoOffers(timeStamp, Constants.mFinalSurveyList, mMallorHighStreetID, currentSelectedOutlet, mSelectedType, mSurveyId);
        mAceDnsTransactionDatabase.insertToLocationTable("OFR", timeStamp);
    }

    public boolean SetSurveyValue(String rowID, String value) {
        String rowid;
        boolean isSucess = true;
        boolean isvalue = false;
        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            if (!isvalue) {
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
                    } else {
                        mInputTimeSurveyDetailsList.get(count).setValue(value);
                    }
                    break;
                }
            }
        }
        return isSucess;
    }

    @SuppressLint({"SetTextI18n", "UseCompatLoadingForDrawables"})
    @SuppressWarnings("deprecation")
    public void DrawLayout() {
        String type;
        String rowid;
        String displayname;
        String tablename;
        String mandatory;
        String actionId;
        String action;
        String validation;
        LinearLayout.LayoutParams Params = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        mButtonList = new ArrayList<>();
        if (!mUndoSurveyList.isEmpty()) {
            mButtonUndo.setVisibility(View.VISIBLE);
        }
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
            mSurveyDetails.setRowId(rowid);
            mSurveyDetails.setType(type);
            mSurveyDetails.setMandatory(mandatory);
            mSurveyDetails.setActionId(actionId);
            mSurveyDetails.setAction(action);
            mSurveyDetails.setValidation(validation);
            mSurveyDetails.setDisplayName(displayname);
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
            } else if (type.equalsIgnoreCase("bool")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
            } else if (type.equalsIgnoreCase("date")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
            } else if (type.equalsIgnoreCase("dynamicview")) {
                LinearLayout.LayoutParams childlayoutparam = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);
                LinearLayout tabchildlayoutheader = new LinearLayout(this);
                tabchildlayoutheader.setPadding(1, 2, 1, 0);
                tabchildlayoutheader.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutheader.setLayoutParams(childlayoutparam);
                tabchildlayoutheader.setBackgroundColor(Color.parseColor("#DCE8F6"));
                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(displayname);
                textviewdisplayname.setTextColor(Color.BLUE);
                textviewdisplayname.setTypeface(null, Typeface.BOLD);
                textviewdisplayname.setPadding(2, 0, 0, 2);
                tabchildlayoutheader.addView(textviewdisplayname);
                mParentLayout.addView(tabchildlayoutheader);
                LinearLayout.LayoutParams qtparam = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 24f);
                //XAxis desig
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
            } else if (type.equalsIgnoreCase("radiomatrix")) {
                LinearLayout.LayoutParams childlayoutparam = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);
                LinearLayout tabchildlayoutheader = new LinearLayout(this);
                tabchildlayoutheader.setPadding(1, 2, 1, 0);
                tabchildlayoutheader.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutheader.setLayoutParams(childlayoutparam);
                tabchildlayoutheader.setBackgroundColor(Color.parseColor("#DCE8F6"));
                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(displayname);
                textviewdisplayname.setTextColor(Color.BLUE);
                textviewdisplayname.setTypeface(null, Typeface.BOLD);
                textviewdisplayname.setPadding(2, 0, 0, 2);
                tabchildlayoutheader.addView(textviewdisplayname);
                mParentLayout.addView(tabchildlayoutheader);
                //String xAxis="Yes:No";
                String xAxis = ":";
                String[] xVals = xAxis.split(":");
                String[] yVals = action.split(":");
                LinearLayout.LayoutParams qtparam = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 24f);
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
                //YAxis design
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
            } else if (type.equalsIgnoreCase("checkboxmatrix")) {
                //childlayout.addView(NewtextView(rowid));
                LinearLayout.LayoutParams childlayoutparam = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);
                LinearLayout tabchildlayoutheader = new LinearLayout(this);
                tabchildlayoutheader.setPadding(1, 2, 1, 0);
                tabchildlayoutheader.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutheader.setLayoutParams(childlayoutparam);
                tabchildlayoutheader.setBackgroundColor(Color.parseColor("#DCE8F6"));
                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(displayname);
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
                //LinearLayout.LayoutParams childlayoutparam = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT,100f);
                LinearLayout.LayoutParams qtparam = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 24f);
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
            } else if (type.equalsIgnoreCase("masterview")) {
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
            } else if (type.equalsIgnoreCase("tableview")) {
                childlayout.addView(NewtextView(rowid));
                AddNewButton(displayname, String.valueOf(rowid));
                mSurveyDetails.setTableName(tablename);
                mIsTableView = true;
            } else if (CheckColonPresentInType(type)) {
                TextView textviewdisplayname = new TextView(this);
                textviewdisplayname.setText(displayname);
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
                mTitleText.setText(displayname);
                mIsTittle = true;
            } else if (type.equalsIgnoreCase("double")) {
                String hinttext = "";
                if (displayname.contains("#")) {
                    String[] splitdisp = displayname.split("#");
                    if (splitdisp[1].equalsIgnoreCase("OTP")) {
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
                    } else {
                        hinttext = displayname;
                    }
                    if (CheckPreDefinedData(rowid)) {
                        FillPreDefinedData(mMallColumnName);
                        mMallColumnName = "";
                    }
                }
                childlayout.addView(AddEditText(rowid, "double", hinttext));
            } else if (type.equalsIgnoreCase("rating")) {
                TextView textviewdisplayname = new TextView(this);
                if (displayname.contains("#")) {
                    String[] splitdisp = displayname.split("#");
                    String disp = "";
                    for (String s : splitdisp) {
                        disp = MessageFormat.format("{0}{1}", disp, s + "\n");
                    }
                    textviewdisplayname.setText(disp);
                } else {
                    textviewdisplayname.setText(displayname);
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
            } else if (type.equalsIgnoreCase("tickbox")) {
                CheckBox checkbox = new CheckBox(this);
                checkbox.setText(displayname);
                checkbox.setTag(rowid);
                checkbox.setOnClickListener(this);
                checkbox.setTextColor(Color.BLUE);
                mCheckBoxList.add(checkbox);
                mParentLayout.addView(checkbox);
                // No need to draw
            } else {
                String hinttext;
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = displayname + " (Mandatory)";
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

                childlayout.addView(AddEditText(rowid, "text", hinttext));
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
                    }
                    SetSurveyValue(rowid, mPredefinedValue);
                    SetTextViewText(rowid, mPredefinedValue);
                    mPredefinedValue = "";
                }
            }
        }
        mButtonSubmit.setVisibility(View.VISIBLE);
    }

    public void PrepareSurveyMenuData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 2:
                        if (Constants.surveyFormDetailsObj.getSurveyTypeDetails().contains(",")) {
                            mTypeList = Constants.surveyFormDetailsObj.getSurveyTypeDetails().split(",");
                        } else {
                            mTypeList = new String[1];
                            mTypeList[0] = Constants.surveyFormDetailsObj.getSurveyTypeDetails();
                        }
                        break;
                    case 3:
                        mMallMasterList = mAceDnsDatabase.GetOfferMallMasterData(mType);
                        break;
                    case 4:
                        mSurveyPublishList = mAceDnsDatabase.GethighstreetAreaData(mPinCode);
                        break;
                    case 5:
                        mSurveyPublishList = mAceDnsDatabase.GetDCAOutletDataForOffers(mType, mMallorHighStreetID, mArea);
                        break;
                    case 6:
                        mSurveyInputList = mAceDnsDatabase.GetOffersSurveyInputLayoutWise(mType);
                        Constants.mType = mType;
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
    public void InitializeView() {
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivitySurveyOffer.this) + "~" + Utils.getDBVersion(ActivitySurveyOffer.this));
        mParentLayout = findViewById(R.id.linearLayoutParent);
        mButtonUndo = findViewById(R.id.btn_undo);
        mButtonBack = findViewById(R.id.back);
        mButtonSubmit = findViewById(R.id.btn_Submi);
        mButtonSubmit.setVisibility(View.GONE);
        mTitleText = findViewById(R.id.textViewTitle);
        mTitleText.setText("Offer");
        mButtonUndo.setVisibility(View.INVISIBLE);
        mButtonUndo.setBackgroundResource(R.drawable.edit);
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
                        if (mCategory && !mSubCategory) {
                            int max = mAceDnsDatabase.Get_Survey_Master_Table_Category_Details(mTableName, mColumnName);
                            values = new String[max];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        } else if (!mCategory && mSubCategory) {
                            mDependentRowId = mFinalRowID;
                            if (!mCondition.trim().isEmpty()) {
                                int max = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mTableName, mColumnName, mDependent, mCondition);
                                values = new String[max];
                                System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                            }
                        } else {
                            int max = mAceDnsDatabase.GetSurveyTableDetails(params, "");
                            values = new String[max];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        }
                        break;
                    case 3:
                        mMallSurveyRelationList = mAceDnsDatabase.GetMallSurveyRelation(mMenuID, mSurveyType);
                        break;
                    case 4:
                        int max = mAceDnsDatabase.GetSurveyTableDetails(params, "");
                        values = new String[max];
                        System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        break;
                    case 5:
                        if (!mCondition.trim().isEmpty()) {
                            int maxx = mAceDnsDatabase.Get_Survey_Master_Table_SubCategory_Details(mSubTableName, mSubColumnName, mSubDependent, mCondition);
                            subvalues = new String[maxx];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, subvalues, 0, Constants.mSurveyLayoutList.length);
                        }
                        break;
                    case 6:
                        int maxx = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase6(mTableName, mColumnName, "");
                        if (maxx > 0) {
                            values = new String[maxx];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        } else {
                            values = null;
                        }
                        break;
                    case 8:
                        int catcount = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsClause(mTableName, mColumnName, mWhereClause);
                        mWhereClause = "";
                        if (catcount > 0) {
                            values = new String[catcount];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        } else {
                            values = null;
                        }
                        break;
                    case 9:
                        mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCondition(mTableName, mSendColumn, mShowColumn, mWhereClause);
                        mWhereClause = "";
                        break;
                    case 10:
                        String floor = Constants.selectedMallMaster.getFloor().trim();
                        if (!floor.isEmpty()) {
                            if (floor.contains(";")) {
                                values = floor.split(";");
                            } else {
                                values = new String[1];
                                values[0] = floor;
                            }
                        }
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

    @Override
    public void onResume() {
        super.onResume();
    }

    @SuppressLint({"SetTextI18n", "UseCompatLoadingForDrawables"})
    @SuppressWarnings("deprecation")
    public void AddNewButton(String displayname, String id) {
        LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
        LinearLayout buttonLayout = new LinearLayout(mContext);
        buttonLayout.setLayoutParams(buttonLayoutParams);
        Button button = new Button(mContext);
        LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
        buttonParams.gravity = Gravity.CENTER_VERTICAL;
        button.setLayoutParams(buttonParams);
        button.setTag(id);
        if (!mIsButtonEnable) {
            button.setEnabled(false);
            mIsButtonEnable = true;
        }
        button.setHorizontallyScrolling(true);
        button.setGravity(Gravity.CENTER);
        button.setText("Select " + displayname);
        button.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
        button.setSingleLine(false);
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
        title.setText("Please select an option");
        final ListView List = grpDialog.findViewById(R.id.list);
        ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_listview, values);
        List.setAdapter(adapter);
        List.setOnItemClickListener((parent, view, position, id) -> grpDialog.cancel());
        grpDialog.show();
    }

    @SuppressLint("SetTextI18n")
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
        } else {
            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_multiple_from_list);
            grpDialog.setCancelable(false);
            TextView title = grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            final ListView List = grpDialog.findViewById(R.id.list);
            KeyValueCheckAdapter adapter;
            KeyValueNormalAdapter adapter1;
            if (type.equalsIgnoreCase("checkbox")) {
                List.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
                adapter = new KeyValueCheckAdapter(this, R.layout.activity_check_list, mKeyValueList);
                List.setAdapter(adapter);
            } else {
                adapter1 = new KeyValueNormalAdapter(this, R.layout.activity_listview, mKeyValueList);
                List.setAdapter(adapter1);
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
                    KeyValue obj = mKeyValueList.get(position);
                    if (checksaving.equalsIgnoreCase("SAVE")) {
                        SetTextViewText(mFinalRowID, obj.getValue());
                        SetSurveyValue(mFinalRowID, obj.getKey());
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
            if (type.equalsIgnoreCase("radio")) {
                submit.setVisibility(View.GONE);
            }

            submit.setOnClickListener(arg0 -> {
                if (type.equalsIgnoreCase("checkbox")) {
                    KeyValue obj;
                    String key = "";
                    String value = "";
                    final SparseBooleanArray checkedItems = List.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            obj = mKeyValueList.get(position);
                            key = MessageFormat.format("{0}{1}", key, obj.getKey() + ";");
                            value = MessageFormat.format("{0}{1}", value, obj.getValue() + ";");
                        }
                        if (checksaving.equalsIgnoreCase("SAVE")) {
                            SetTextViewText(mFinalRowID, value);
                            SetSurveyValue(mFinalRowID, key);
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
                    }
                    grpDialog.cancel();
                } else {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowList(final String type) {
        if (values.length == 1) {
            String val = values[0];
            SetTextViewText(mFinalRowID, val);
            SetSurveyValue(mFinalRowID, val);
        } else {
            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_multiple_from_list);
            grpDialog.setCancelable(false);
            TextView title = grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");

            final ListView List = grpDialog.findViewById(R.id.list);

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
                final ListCheckAdapter adapter = new ListCheckAdapter(this, R.layout.activity_check_list, values);
                List.setAdapter(adapter);
            } else {
                ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_masterview, values);
                List.setAdapter(adapter);
            }

            RelativeLayout chkAllLayout = grpDialog.findViewById(R.id.select_all_layout);
            if (type.equalsIgnoreCase("checkbox")) {
                chkAllLayout.setVisibility(View.VISIBLE);
            }

            if (type.equalsIgnoreCase("checkbox")) {
                if (Constants.isMallSurveyRelationDataAvailable) {
                    String menuItem = "";
                    for (int i = 0; i < List.getCount(); i++) {
                        menuItem = (String) List.getItemAtPosition(i);
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
                if (!type.equalsIgnoreCase("checkbox")) {
                    String val = values[position].trim();
                    if (val.equalsIgnoreCase("Others") && Constants.surveyFormDetailsObj.getSurveyOtherText().equalsIgnoreCase("yes")) {
                        ShowActionLayout(val, "", "Y", "", "");
                    } else {
                        SetTextViewText(mFinalRowID, val);
                        SetSurveyValue(mFinalRowID, val);
                        if (!mActionstring.isEmpty()) {
                            if (mActionstring.equalsIgnoreCase("no_repetition")) {
                                mNonRepeat.add(val);
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
                    final SparseBooleanArray checkedItems = List.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        String val = "";
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                val = MessageFormat.format("{0}{1}", val, values[position] + "; ");

                                if (mCategory && !mSubCategory) {
                                    mCondition = MessageFormat.format("{0}{1}", mCondition, "'" + values[position] + "',");
                                }
                            }
                        }

                        if (mCondition.endsWith(",")) {
                            mCondition = mCondition.substring(0, mCondition.length() - 1);
                        }
                        Log.i("CONDITION", mCondition);
                        SetTextViewText(mFinalRowID, val);
                        SetSurveyValue(mFinalRowID, val);
                    }
                    grpDialog.cancel();
                    if (!mSubtableInfo.isEmpty()) {
                        if (ParseSubTablename(mSubtableInfo)) {
                            PrepareSurveyData(5, "");
                        }
                    }
                } else {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
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
            ParseandShowAction(tag, "", "");
        }

        if (isaction && mParentType.equalsIgnoreCase("bool")) {
            ParseandShowAction(tag);
        }
        if (isaction && mParentType.equalsIgnoreCase("date")) {
            ParseandShowAction(tag);
        }

        if (!isaction && mParentType.equalsIgnoreCase("checkbox")) {
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
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                if (CheckSurveyValidation(tag, "check") && !mCheckValue.isEmpty()) {
                    String[] splittablename = tablename.split("#");
                    tablename = splittablename[1];
                    mCategory = false;
                    mSubCategory = false;
                    mCondition = "";
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
            GetTableViewData(tag);
        }

        if (!isaction && mParentType.equalsIgnoreCase("masterview")) {
            String tablename = GetTableName(tag);
            if (tablename.contains("#")) {
                String[] splitablename = tablename.split("#");
                if (splitablename.length == 3) {
                    mTableName = splitablename[0];
                    mColumnName = splitablename[1];
                    mType = splitablename[2];
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

    }

    public String GetTableName(String rowID) {
        String tablename = "";
        String rowid;
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
                currentDisplayName = mInputTimeSurveyDetailsList.get(count).getDisplayName();
                if (actionstring.contains("#")) {
                    String[] RowData = actionstring.split("#");
                    if (RowData.length > 0) {
                        String actiondisplayname = RowData[0];
                        String actiontype = RowData[1];
                        String actionmandatory = RowData[2];
                        String actionvalidation;
                        String tablename;

                        if (actiondisplayname.contains(";")) {
                            String[] splitdisplay = actiondisplayname.split(";");
                            String actionsubdisplayname = splitdisplay[0];
                            String noofsubdisplay = splitdisplay[1];

                            String subactionmandatory;
                            String subactionno;

                            if (actionmandatory.contains(";")) {
                                String[] splitmandatory = actionstring.split(";");
                                subactionmandatory = splitmandatory[0];
                                subactionno = splitmandatory[1];
                            } else {
                                subactionmandatory = actionmandatory;
                                subactionno = "0";
                            }

                            if (!actiontype.equalsIgnoreCase("checkbox") && !actiontype.equalsIgnoreCase("radio")) {
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
                    if (actionstring.equalsIgnoreCase("Click")) {
                        validation = mInputTimeSurveyDetailsList.get(count).getValidation().trim();
                        SetMaxNoofImage(rowID, validation);
                        ShowImageCaptureLayer(rowID);
                    } else if (actionstring.equalsIgnoreCase("floor")) {
                        PrepareSurveyData(10, "");
                    } else {
                        if (currentDisplayName.matches("End Date")) {
                            if (isStartDateSet)
                                openDatePicker(rowID);
                            else {
                                Toast.makeText(mContext, "You need to set Start Date first", Toast.LENGTH_SHORT).show();
                            }
                        } else {
                            openDatePicker(rowID);
                        }
                    }
                }
                break;
            }
        }
    }

    public void ParseandShowAction(String rowID, String noneed1, String noneed2) {
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
                        for (int countx = 0; countx < actionsplit.length; countx++) {
                            if (countx == 0) {
                                displayname = actionsplit[countx];
                            } else if (countx == 1) {
                                typelist = actionsplit[countx];
                            }
                        }
                        if (mEdiTextDynamic != null && !mEdiTextDynamic.getText().toString().isEmpty()) {
                            int repeat = Integer.parseInt(mEdiTextDynamic.getText().toString());
                            ShowSubActionLayout(displayname, typelist, repeat);
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
                    for (String s : actionsplit) {
                        if (s.trim().startsWith(value)) {
                            String[] subaction = s.split(":");
                            if (subaction.length == 2) {
                                if (subaction[1] != null && !subaction[1].trim().isEmpty()) {
                                    if (subaction[1].trim().contains("@")) {
                                        String[] subactionlist = subaction[1].split("@");
                                        ArrayList<SurveyInput> surveyInputlist = new ArrayList<>();
                                        for (String string : subactionlist) {
                                            String[] subactiondetaislis = string.split("#");
                                            SurveyInput obj = new SurveyInput();
                                            for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {
                                                if (actiondetailscount == 0) {
                                                    obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 1) {
                                                    obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 2) {
                                                    obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 3) {
                                                    obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 4) {
                                                    obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 5) {
                                                    obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 6) {
                                                    obj.setSurveyClause(subactiondetaislis[actiondetailscount]);
                                                }
                                            }
                                            surveyInputlist.add(obj);
                                        }
                                        ShowSubActionLayout(surveyInputlist, rowID);

                                    } else {
                                        if (subaction[1].contains("#")) {
                                            String[] subactiondetaislis = subaction[1].split("#");
                                            ArrayList<SurveyInput> surveyInputlist = new ArrayList<>();
                                            SurveyInput obj = new SurveyInput();
                                            for (int actiondetailscount = 0; actiondetailscount < subactiondetaislis.length; actiondetailscount++) {
                                                if (actiondetailscount == 0) {
                                                    obj.setSurveyDisplayName(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 1) {
                                                    obj.setSurveyType(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 2) {
                                                    obj.setSurveyMadatory(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 3) {
                                                    obj.setSurveyTableName(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 4) {
                                                    obj.setSurveyValidation(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 5) {
                                                    obj.setSurveySurveyType(subactiondetaislis[actiondetailscount]);
                                                } else if (actiondetailscount == 6) {
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
                break;
            }
        }
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
                        String val = "";
                        for (int count = 0; count < displayno; count++) {
                            if (isValueOK) {
                                for (EditText editText : mEditTextList) {
                                    String tag = editText.getTag().toString();
                                    if (tag.equalsIgnoreCase(String.valueOf(count))) {
                                        if (count < mandatoryno) {
                                            if (!editText.getText().toString().isEmpty()) {
                                                val = MessageFormat.format("{0}{1}", val, editText.getText().toString() + "; ");
                                            } else {
                                                isValueOK = false;
                                                Toast.makeText(mContext, "Please provide valid inputs", Toast.LENGTH_SHORT).show();
                                                break;
                                            }
                                        } else {
                                            if (!editText.getText().toString().isEmpty()) {
                                                val = MessageFormat.format("{0}{1}", val, editText.getText().toString() + "; ");
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (isValueOK) {
                            SetSurveyValue(mFinalRowID, val);
                            SetTextViewText(mFinalRowID, val);
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
    public void ShowSubActionLayout(String displayname, String type, int repeat) {
        int tagcount = 0;
        final List<EditText> editTextList = new ArrayList<>();
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_sub_action);
        grpDialog.setCancelable(false);
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
                    String[] splitdisplay = displayname.split(";");
                    String[] splittype = type.split(";");
                    if (splitdisplay.length == splittype.length) {
                        spliton = splitdisplay.length;
                        for (int set = 0; set < splitdisplay.length; set++) {
                            EditText editText = new EditText(this);
                            editText.setTag(tagcount);
                            editText.setLayoutParams(params);
                            if (splittype[set].trim().equalsIgnoreCase("double")) {
                                editText.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                            } else {
                                editText.setInputType(InputType.TYPE_TEXT_FLAG_CAP_WORDS | InputType.TYPE_TEXT_FLAG_MULTI_LINE | InputType.TYPE_TEXT_FLAG_AUTO_CORRECT);
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
                            tabchildlayoutx.addView(editText);
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
                        editText.setInputType(InputType.TYPE_TEXT_FLAG_CAP_WORDS | InputType.TYPE_TEXT_FLAG_MULTI_LINE | InputType.TYPE_TEXT_FLAG_AUTO_CORRECT);
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
                    tabchildlayoutx.addView(editText);
                    mSubParentLayout.addView(tabchildlayoutx);
                    tagcount++;
                }
            } else {
                Utils.showToast(mContext, "Error in data Please Synchronize Data");
            }
        }

        Button submit = grpDialog.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(arg0 -> {
            String value = "";
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            if (!editTextList.isEmpty()) {
                int count = 1;
                for (EditText editText : editTextList) {
                    if (count % spliton == 0) {
                        value = MessageFormat.format("{0}{1}", value, editText.getText().toString() + "#");
                    } else {
                        value = MessageFormat.format("{0}{1}", value, editText.getText().toString() + ";");
                    }
                    count++;
                }
            }
            SetSurveyValue(mFinalRowID, value);
            SetTextViewText(mFinalRowID, value);
            grpDialog.cancel();
        });

        grpDialog.show();

    }

    public void ShowSubActionLayout(final ArrayList<SurveyInput> surveyInputList, final String rowid) {
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_sub_action);
        grpDialog.setCancelable(false);
        mKeyValueSubList = new ArrayList<>();
        TextView title = grpDialog.findViewById(R.id.title);
        title.setText(mDisplayName);
        final LinearLayout mSubParentLayout = grpDialog.findViewById(R.id.linearLayoutParent);
        for (int count = 0; count < surveyInputList.size(); count++) {
            String displayname = surveyInputList.get(count).getSurveyDisplayName();
            String type = surveyInputList.get(count).getSurveyType();
            String mandatory = surveyInputList.get(count).getSurveyMadatory();
            KeyValue obj = new KeyValue();
            obj.setKey(rowid + count);
            obj.setValue("");
            obj.setType(type);
            mKeyValueSubList.add(obj);
            LinearLayout childlayout = new LinearLayout(this);
            String hinttext;
            childlayout.setOrientation(LinearLayout.VERTICAL);
            childlayout.setPadding(5, 5, 5, 5);
            if (type.equalsIgnoreCase("double")) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                childlayout.addView(AddEditText(rowid + count, type, hinttext));
            } else if (type.equalsIgnoreCase("radio")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(displayname);
                btn.setOnClickListener(v -> Log.i("TAG", "The index is" + btn.getText()));
                childlayout.addView(btn);
            } else if (type.equalsIgnoreCase("checkbox")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(displayname);
                btn.setOnClickListener(v -> Log.i("TAG", "The index is" + btn.getText()));
                childlayout.addView(btn);
            } else if (type.equalsIgnoreCase("masterview")) {
                final Button btn = new Button(this);
                btn.setTag(rowid + count);
                btn.setId(count);
                btn.setText(displayname);
                btn.setOnClickListener(v -> {
                    int count1 = btn.getId();
                    mSubActionTag = (String) btn.getTag();
                    String tablenamedetails = surveyInputList.get(count1).getSurveyTableName();
                    String clause = surveyInputList.get(count1).getSurveyClause().trim();
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
                                            mWhereClause = MessageFormat.format("{0}{1}", mWhereClause, splitwhere[0] + "='" + Constants.mSurveyRouteCode + "' ");
                                        } else {
                                            mWhereClause = MessageFormat.format("{0}{1}", mWhereClause, splitwhere[0] + "='" + splitwhere[1] + "' ");
                                        }
                                        if (clausecount != noofclause.length - 1) {
                                            mWhereClause = MessageFormat.format("{0}AND ", mWhereClause);
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
                        } else {
                            Utils.showToast(mContext, "No data found.Please Synchronize Data");
                        }
                    } else {
                        Utils.showToast(mContext, "No data found.Please Synchronize Data");
                    }
                    Log.i("TAG", "The index is" + btn.getText());
                });
                childlayout.addView(btn);

            } else if (type.trim().isEmpty()) {
                if (mandatory.equalsIgnoreCase("Y")) {
                    hinttext = "*" + displayname;
                } else {
                    hinttext = displayname;
                }
                childlayout.addView(AddEditText(rowid + count, type, hinttext));
            }
            mSubParentLayout.addView(childlayout);
        }
        Button submit = grpDialog.findViewById(R.id.btn_Submi);
        submit.setOnClickListener(arg0 -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            String val = "";
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
            for (int countx = 0; countx < surveyInputList.size(); countx++) {
                if (surveyInputList.get(countx).getSurveyMadatory().trim().equalsIgnoreCase("Y")) {
                    String key = rowid + countx;
                    for (int county = 0; county < mKeyValueSubList.size(); county++) {
                        if (key.equalsIgnoreCase(mKeyValueSubList.get(county).getKey())) {
                            if (mKeyValueSubList.get(county).getValue().isEmpty()) {
                                isValueOK = false;
                            }
                            break;
                        }
                    }
                }
            }
            if (isValueOK) {
                for (int count = 0; count < mKeyValueSubList.size(); count++) {
                    val = MessageFormat.format("{0}{1}", val, mKeyValueSubList.get(count).getEnteredValue() + "#");
                    mActionPressed = MessageFormat.format("{0}{1}", mActionPressed, mKeyValueSubList.get(count).getValue() + "#");
                }
                if (val.endsWith("#") && mActionPressed.endsWith("#")) {
                    val = val.substring(0, val.length() - 1);
                    mActionPressed = mActionPressed.substring(0, mActionPressed.length() - 1);
                }
                SetSurveyValue(mFinalRowID, mActionPressed);
                SetTextViewText(mFinalRowID, val);
                mActionPressed = "";
                grpDialog.cancel();
            } else {
                Utils.showToast(mContext, "Please provide input");
            }
        });
        grpDialog.show();
    }

    public void ChangeVisibility(String rowID, String checkedvalue) {
        String rowid;
        String validationText;

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            rowid = mInputTimeSurveyDetailsList.get(count).getRowId();
            validationText = mInputTimeSurveyDetailsList.get(count).getValidation().trim();
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

    @SuppressLint("SetTextI18n")
    public void ShowActionLayout(String displayname, final String actiontype, final String mandatory, final String tablename, final String validation) {
        final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.activty_ation);
        grpDialog.setCancelable(false);
        TextView title = grpDialog.findViewById(R.id.textView1);
        title.setText(displayname);
        final EditText mEditText = grpDialog.findViewById(R.id.editText1);
        if (actiontype.equalsIgnoreCase("double")) {
            mEditText.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
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
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
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
                            Toast.makeText(mContext, "Please provide valid Input", Toast.LENGTH_SHORT).show();
                        }
                    } else if (!editval.isEmpty()) {
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
    private EditText AddEditText(String tag, String edtype, String hinttext) {
        EditText editText = new EditText(this);
        editText.setTag(tag);
        LayoutParams params = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);

        editText.setLayoutParams(params);
        if (edtype.equalsIgnoreCase("double")) {
            editText.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        } else {
            editText.setInputType(InputType.TYPE_CLASS_TEXT | InputType.TYPE_TEXT_FLAG_MULTI_LINE | InputType.TYPE_TEXT_FLAG_AUTO_CORRECT);
        }
        editText.setSingleLine(false);
        editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        editText.setTextColor(Color.GRAY);
        editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
        editText.setTypeface(null, Typeface.NORMAL);
        if (!mPredefinedValue.trim().isEmpty()) {
            editText.setText(mPredefinedValue);
            editText.setEnabled(false);
            mPredefinedValue = "";
        } else {
            editText.setHint(hinttext);
        }

        editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
        editText.setHintTextColor(Color.LTGRAY);
        mEditTextList.add(editText);
        return editText;
    }

    private TextView NewtextView(String tag) {
        LinearLayout.LayoutParams Params = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 1);
        TextView textView = new TextView(this);
        textView.setLayoutParams(Params);
        textView.setTag(tag);
        mTextViewList.add(textView);
        return textView;
    }

    public boolean CheckSurveyValidation(String rowID, String value) {
        String rowid;
        String validationText;
        String displayname;
        boolean isSucess = true;

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
                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                            }
                        } else {
                            if (validationText.startsWith("RA")) {
                                if (validationText.contains("#")) {
                                    String[] validationsplit = validationText.split("#");
                                    if (CheckSurveyDependentOnOtherInputValue(validationsplit[0])) {
                                        if (validationsplit[1].equalsIgnoreCase(">=")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!(Double.parseDouble(mCheckValue) >= Double.parseDouble(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, mDependentDisplayName + " value should be greater or equal than " + displayname, Toast.LENGTH_SHORT).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, mDependentDisplayName + " value should be greater or equal than " + displayname, Toast.LENGTH_SHORT).show();
                                            }
                                        } else if (validationsplit[1].equalsIgnoreCase("<=")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!(Double.parseDouble(mCheckValue) <= Double.parseDouble(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, mDependentDisplayName + " value should be less or equal than " + displayname, Toast.LENGTH_SHORT).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, mDependentDisplayName + " value should be less or equal than " + displayname, Toast.LENGTH_SHORT).show();
                                            }
                                        } else if (validationsplit[1].equalsIgnoreCase("=")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!Integer.valueOf(mCheckValue).equals(Integer.valueOf(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, displayname + " value should be equal to " + mDependentDisplayName, Toast.LENGTH_SHORT).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                }

                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, displayname + " value should be equal to " + mDependentDisplayName, Toast.LENGTH_SHORT).show();
                                            }
                                        } else if (validationsplit[1].equalsIgnoreCase("<")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!(Double.parseDouble(mCheckValue) < Double.parseDouble(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, mDependentDisplayName + " value should be less than " + displayname, Toast.LENGTH_SHORT).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, mDependentDisplayName + " value should be less than " + displayname, Toast.LENGTH_SHORT).show();
                                            }
                                        } else if (validationsplit[1].equalsIgnoreCase(">")) {
                                            if (!mCheckValue.isEmpty()) {
                                                if (!value.isEmpty()) {
                                                    if (!(Double.parseDouble(mCheckValue) > Double.parseDouble(value))) {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, mDependentDisplayName + " value should be greater than " + displayname, Toast.LENGTH_SHORT).show();
                                                    }
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, mDependentDisplayName + " value should be greater than " + displayname, Toast.LENGTH_SHORT).show();
                                            }
                                        } else {
                                            if (value.isEmpty()) {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                            }
                                        }
                                    }
                                } else {
                                    if (CheckSurveyDependentOnOtherInputValue(validationText)) {
                                        if (value.isEmpty()) {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                        }
                                    }
                                }
                            } else if (validationText.startsWith("Round")) {
                                if (validationText.contains("#")) {
                                    DecimalFormat defaultFormat = new DecimalFormat("0.00");
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
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                }
                                            } else {
                                                if (value.trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                    isRound = true;
                                                    double val = Double.parseDouble(value);
                                                    mRoundValue = defaultFormat.format(val);
                                                } else {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                }
                                            }
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                        }
                                    }
                                }
                            } else {
                                if (validationText.contains(".")) {
                                    String[] items = value.split("\\.");
                                    if (items.length < 2) {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                    }
                                } else if (validationText.contains("@")) {
                                    String[] items = value.split("@");
                                    if (items.length >= 2) {
                                        int lengths = items.length;
                                        String[] dotitems = items[lengths - 1].split("\\.");
                                        if (dotitems.length >= 2) {
                                            if (dotitems[0].trim().isEmpty()) {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                            }
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                        }
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                    }
                                } else {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                }
                            }
                        }
                    } else {
                        if (!value.isEmpty()) {
                            if (ContainsOnlyNumbers(validationText)) {
                                if (Integer.parseInt(validationText) != value.length()) {
                                    isSucess = false;
                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                }
                            } else {
                                if (validationText.startsWith("RA")) {
                                    if (validationText.contains(":")) {
                                        String[] validationsplit = validationText.split(":");
                                        if (CheckSurveyDependentOnOtherInputValue(validationsplit[0])) {
                                            if (value.length() < Integer.parseInt(validationsplit[1])) {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                            }
                                        }
                                    }
                                } else if (validationText.startsWith("Round")) {
                                    if (validationText.contains("#")) {
                                        DecimalFormat defaultFormat = new DecimalFormat("0.00");
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
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                    }
                                                } else {
                                                    if (value.trim().length() <= Integer.parseInt(roundsplit[0].trim())) {
                                                        isRound = true;
                                                        double val = Double.parseDouble(value);
                                                        mRoundValue = defaultFormat.format(val);
                                                    } else {
                                                        isSucess = false;
                                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if (validationText.contains(".")) {
                                        String[] items = value.split("\\.");
                                        if (items.length < 2) {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                        }
                                    } else if (validationText.contains("@")) {
                                        String[] items = value.split("@");
                                        if (items.length >= 2) {
                                            int lengths = items.length;
                                            String[] dotitems = items[lengths - 1].split("\\.");
                                            if (dotitems.length >= 2) {
                                                if (dotitems[0].trim().isEmpty()) {
                                                    isSucess = false;
                                                    Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                                }
                                            } else {
                                                isSucess = false;
                                                Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                            }
                                        } else {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                        }
                                    } else {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                    }
                                }
                            }
                        } else {
                            if (validationText.startsWith("RA")) {
                                if (validationText.contains(":")) {
                                    String[] validationsplit = validationText.split(":");
                                    if (CheckSurveyDependentOnOtherInputValue(validationsplit[0])) {
                                        if (value.length() < Integer.parseInt(validationsplit[1])) {
                                            isSucess = false;
                                            Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                        }
                                    }
                                } else {
                                    if (CheckSurveyDependentOnOtherInputValue(validationText)) {
                                        isSucess = false;
                                        Toast.makeText(mContext, "Please provide valid input in " + displayname, Toast.LENGTH_SHORT).show();
                                    }
                                }
                            }
                        }
                    }
                    break;
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

        for (int count = 0; count < mInputTimeSurveyDetailsList.size(); count++) {
            value = mInputTimeSurveyDetailsList.get(count).getValue();
            displayname = mInputTimeSurveyDetailsList.get(count).getDisplayName();
            if (mInputTimeSurveyDetailsList.get(count).getMandatory().equalsIgnoreCase("Y")) {
                if (value.isEmpty()) {
                    Toast.makeText(mContext, "Please provide mandatory input in " + displayname, Toast.LENGTH_SHORT).show();
                    isSucess = false;
                    break;
                }
            }
        }
        return isSucess;
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
                if (boolStringType1.trim().equalsIgnoreCase("Y") && boolStringType2.trim().equalsIgnoreCase("N")) {
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
                    Log.i("Status", mCategory + "&" + mSubCategory);
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
                    mColumnName = splitablename[1];
                    mDependent = splitablename[2];
                    mMessage = splitablename[3];
                    if (!mTableName.isEmpty() && !mColumnName.isEmpty() && !mDependent.trim().isEmpty()) {
                        Log.i("Status", mCategory + "&" + mSubCategory);
                        mCategory = false;
                        mSubCategory = true;
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

    public void ShowImageCaptureLayer(String rowid) {
        if (!(ImageValidation(rowid))) {
            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
        } else {
            launchCameraToTakeImage();
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
                Map.Entry<String, ArrayList<RadioGroup>> entry = iterator.next();
                String rowid = entry.getKey();
                List<RadioGroup> values = entry.getValue();
                String val = "";
                for (ROW = 0; ROW < values.size(); ROW++) {
                    int selectedid = values.get(ROW).getCheckedRadioButtonId();
                    if (selectedid > 0) {
                        val = MessageFormat.format("{0}{1}", val, ROW + "," + ((RadioButton) findViewById(selectedid)).getText().toString() + "#");
                    } else {
                        val = MessageFormat.format("{0}{1}", val, ROW + ",#");
                    }
                }
                if (val.endsWith("#")) {
                    val = val.substring(0, val.length() - 1);
                }
                SetSurveyValue(rowid, val);
            }
        }
    }

    public void GetCheckBoxMatrixListContainer() {
        if (mCheckBoxMatrixListContainer != null) {
            Set<Entry<String, ArrayList<CheckBox>>> set = mCheckBoxMatrixListContainer.entrySet();
            Iterator<Entry<String, ArrayList<CheckBox>>> iterator = set.iterator();
            int ROW;
            while (iterator.hasNext()) {
                Map.Entry<String, ArrayList<CheckBox>> entry = iterator.next();
                String rowid = entry.getKey();
                List<CheckBox> values = entry.getValue();
                String val = "";
                for (ROW = 0; ROW < values.size(); ROW++) {
                    val = MessageFormat.format("{0}{1}", val, values.get(ROW).getTag() + "," + values.get(ROW).isChecked() + "#");
                }
                if (val.endsWith("#")) {
                    val = val.substring(0, val.length() - 1);
                }
                SetSurveyValue(rowid, val);
            }
        }
    }

    public void GetTableViewData(String rowId) {
        for (int count = 0; count < mSurveyTableViewList.size(); count++) {
            SurveyTableView obj = mSurveyTableViewList.get(count);
            if (obj.getRowId().equalsIgnoreCase(rowId)) {
                mType = obj.getType();
                if (!obj.getDependentOn().trim().isEmpty()) {
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

    public void ParseTableViewData(String data) {
        if (data.contains("/")) {
            String[] splitstring = data.split("/");
            if (splitstring.length > 0) {
                values = new String[splitstring.length];
                System.arraycopy(splitstring, 0, values, 0, splitstring.length);
            } else {
                values = new String[1];
                values[0] = data;
            }
        } else {
            values = new String[1];
            values[0] = data;
        }
        ShowList(mType);
    }

    public void FillPreDefinedData(String columnename) {
        String mMallId = "";
        mPredefinedValue = mAceDnsDatabase.GetMallMasterValue(columnename, mMallId);
    }

    @SuppressLint("SimpleDateFormat")
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK) {
            SetNoofImageCaptured(mFinalRowID, mSImageName);
            Constants.isSurveyImageTake = true;
            ImageView mImageViewCaptureImage = new ImageView(this);
            try {
                Bundle extras = data.getExtras();
                assert extras != null;
                Bitmap imageBitmap = (Bitmap) extras.get("data");
                assert imageBitmap != null;
                int mHeight = 150;
                int mWidth = 175;
                imageBitmap = Utils.getResizedBitmap(imageBitmap, mWidth, mHeight);
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                String imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                if (mSImageName.matches("")) {
                    mSImageName = imageName;
                } else {
                    mSImageName = mSImageName + ";" + imageName;
                }
                Utils.SaveImageToExternalStorage(imageBitmap, Utils.getAppStoragePath(mContext), imageName);
                mAceDnsTransactionDatabase.insertToSupportingAttachTable(imageName, "SURVEY_OFFER");
                LinearLayout.LayoutParams parms = new LinearLayout.LayoutParams(mWidth, mHeight);
                mImageViewCaptureImage.setLayoutParams(parms);
                mImageViewCaptureImage.setImageBitmap(imageBitmap);
                SetSurveyValue(mFinalRowID, mSImageName);
                mParentLayout.addView(mImageViewCaptureImage);
            } catch (Exception ex) {
                Toast.makeText(mContext, "Image is too large", Toast.LENGTH_SHORT).show();
            }
        }
        if (resultCode == RESULT_CANCELED) {
            Constants.isSurveyImageTake = false;
        }
        if (requestCode == TAKE_PHOTO_CODE && resultCode == RESULT_OK) {
            if (!(ImageValidation(mFinalRowID))) {
                Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
            } else {
                AlertDialog.Builder AlertDG = new AlertDialog.Builder(ActivitySurveyOffer.this);
                AlertDG.setTitle("Information");
                AlertDG.setMessage("Do you want to take another pic?");
                AlertDG.setPositiveButton("Yes", (dialog, which) -> launchCameraToTakeImage());
                AlertDG.setNegativeButton("No", (dialog, which) -> {
                });
                AlertDG.setCancelable(true);
                AlertDG.create().show();
            }
        }
    }

    public void openDatePicker(final String rowID) {
        // Get Current Date
        final Calendar c = Calendar.getInstance();
        mYear = c.get(Calendar.YEAR);
        mMonth = c.get(Calendar.MONTH);
        mDay = c.get(Calendar.DAY_OF_MONTH);
        //launch datepicker modal
        DatePickerDialog datePickerDialog = new DatePickerDialog(this, (view, year, monthOfYear, dayOfMonth) -> {
            mDateValue = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
            if (currentDisplayName.matches("Start Date")) {
                mDateValueStart = mDateValue;
                isStartDateSet = true;
                SetTextViewText(rowID, mDateValue);
                SetSurveyValue(rowID, mDateValue);
            } else {
                if (DateTimeFormatter.isDateOneGraterThanDate2("dd/MM/yyyy", mDateValue, mDateValueStart)) {
                    SetTextViewText(rowID, mDateValue);
                    SetSurveyValue(rowID, mDateValue);
                } else {
                    Toast.makeText(mContext, "End date must be grater than start date", Toast.LENGTH_SHORT).show();
                }
            }

        }, mYear, mMonth, mDay);
        datePickerDialog.show();
        if (currentDisplayName.matches("End Date")) {
            datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis() - 1000);
        }
    }

    public void ShowAddAnotherOfferDialog() {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(mContext);
        alertDialogBuilder.setMessage("Do you want to add another offer?").setCancelable(false);
        alertDialogBuilder.setNegativeButton("YES", (dialog, id) -> {
            dialog.cancel();
            Constants.isAddingAnotherOffer = true;
            Constants.mMallorHighStreetID = mMallorHighStreetID;
            Constants.currentSelectedOutlet = currentSelectedOutlet;
            Constants.mSelectedType = mSelectedType;
            Constants.mSurveyId = mSurveyId;
            mSImageName = "";
            Intent intent = new Intent(mContext, ActivitySurveyOffer.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            mContext.startActivity(intent);
        });
        alertDialogBuilder.setPositiveButton("NO", (dialog, id) -> {
            dialog.cancel();
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            mContext.startActivity(intent);
        });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    private void launchCameraToTakeImage() {
        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
    }

    public interface AsyncResponse {
        void processFinish(Object output);
    }
}
