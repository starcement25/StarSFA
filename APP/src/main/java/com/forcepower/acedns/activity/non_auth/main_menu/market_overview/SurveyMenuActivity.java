package com.forcepower.acedns.activity.non_auth.main_menu.market_overview;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.adapter.FsOutletAdapter;
import com.forcepower.acedns.adapter.MallAdapter;
import com.forcepower.acedns.adapter.MenuAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRedudantSurvey;
import com.forcepower.acedns.bean.FsSurveyPublish;
import com.forcepower.acedns.bean.MallMaster;
import com.forcepower.acedns.bean.MenuObj;
import com.forcepower.acedns.bean.SurveyMenuDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.mSurveyMainType;
import static com.forcepower.acedns.constants.Constants.mSurveyMenuDetailsList;

/**
 * An activity which will show Survey Menu
 *
 * @author Sourav Das <souravd@coral.in>
 * @version 5.2.0
 */
public class SurveyMenuActivity extends AceDnsParentActivity {

    public static ImageView mImageViewHeaderLogo = null;
    public static TextView textView1 = null;
    public static GridView mGridViewMenu = null;
    public static Button mButtonBack = null;

    public ProgressDialog mPrepareSurveyMenuProgressDialog;
    public Handler mPrepareSurveyMenuHandler;
    Context mContext;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;

    ArrayList<MallMaster> mMallMasterList;
    ArrayList<FsSurveyPublish> mFsSurveyPublishList;
    String[] mMenuList;
    String[] mTypeList;
    private String mMenuID = "";
    private String mMenuName = "";
    private String mType = "";
    private String mPinCode = "";
    private String mMallId = "";
    private String mSubMenu = "";
    MenuAdapter mMenuAdapter;
    ArrayList<MenuObj> mMenuList1;
    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey_menu);
        Constants.shouldAskForOTP = false;
        RegisterActivities.registerActivity(this);
        mContext = SurveyMenuActivity.this;

        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        Constants.selectedFsSurveyPublish = null;

        mAceDnsDatabase.GETSurveyFormDetails();
        try {
            if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes")) {
                mSubMenu = getIntent().getStringExtra("SUBMENU");
            }
        } catch (Exception e) {
            //throw new RuntimeException(e);
        }

        InitializeView();

        mButtonBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (true == Constants.isDCAtoDCE) {
                    Constants.isDCAtoDCE = false;
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                } else {
                    finish();
                }
            }
        });

        mPrepareSurveyMenuHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                SurveyMenuActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                    ShowSurveyMenuList();
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
                                if (mFsSurveyPublishList.size() > 0) {
                                    ShowOutletLis();
                                } else {
                                    mButtonBack.setVisibility(View.VISIBLE);
                                    Utils.showToast(mContext, "You have no outlet asigned");
                                }
                                break;
                        }
                    }
                });
            }
        };

        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
            PrepareSurveyMenuData(2);
        } else {
            PrepareSurveyMenuData(1);
        }
    }

    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */
    public void InitializeView() {
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(SurveyMenuActivity.this) + "~"
                + Utils.getDBVersion(SurveyMenuActivity.this));
        mGridViewMenu = findViewById(R.id.listsurveymenu);
        mButtonBack = (Button) findViewById(R.id.back);

        if (true == Constants.isDCAtoDCE) {
            mButtonBack.setVisibility(View.GONE);
        }
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);
        textView1 =  findViewById(R.id.textView1);
        textView1.setText(mSurveyMainType);
    }
    private void ParseData() {
        mMenuList1 = new ArrayList<>();

            String[] surveymenu = mMenuList;
            for (int count = 0; count < surveymenu.length; count++)
            {
                String menuname = surveymenu[count];

                MenuObj menuObj = new MenuObj();
                if (menuname.equalsIgnoreCase("Counter Meet")) {
                    boolean fsaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (fsaccess == true) {
                        menuObj.setResourceId(R.drawable.countermeet);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Big COunter Meet")) {
                    boolean dceaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dceaccess == true) {
                        menuObj.setResourceId(R.drawable.bigcountermeet);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Mega Mason Meet"))
                {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true)
                    {
                        menuObj.setResourceId(R.drawable.mega);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Dhalai Meet"))
                {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true)
                    {
                        menuObj.setResourceId(R.drawable.dhalai);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Engineers Meet")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.eng_meet);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Professional Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.prof);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }

                else if (menuname.equalsIgnoreCase("Startech")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.startech);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }

                else if (menuname.equalsIgnoreCase("Contractor Meet")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.contractor);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Plant Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.plan);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Dealer/Subdealer Visit")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.deal_sub);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Complain")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.complain);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Complain")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.complain);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Mason Meet")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.mason);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("IHB Meet")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.ihb);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Small Engineers Meet")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.smalleng);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }

                else if (menuname.equalsIgnoreCase("BIG CONTRACTOR MEET")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.bigcont);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }

                else if (menuname.equalsIgnoreCase("CATCH THEM YOUNG")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.catchthemyoung);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }

                else if (menuname.equalsIgnoreCase("PC ONE DAY TRAINING PROGRAMME")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.pconedaytrainingprogram);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }

                else if (menuname.equalsIgnoreCase("Customer Guidance Camp")) {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true) {
                        menuObj.setResourceId(R.drawable.customerguidencecamp);
                        menuObj.setFeatureName(menuname);
                        mMenuList1.add(menuObj);
                    }
                }
            }

    }
    public void PrepareSurveyMenuData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {

                switch (task) {
                    case 1:
                        int max = 0;
                        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
                            max = mAceDnsDatabase.GetMenuName(mType);
                        } else {
                            if (Constants.surveyFormDetailsObj.getSurveyRoutePlan().equalsIgnoreCase("yes")) {
                                max = mAceDnsDatabase.GetMenuName("", mSubMenu);
                            } else {
                                max = mAceDnsDatabase.GetMenuName();
                            }
                        }
                        mMenuList = new String[max];
                        for (int i = 0; i < Constants.mSurveyMenuDetailsList.size(); i++) {
                            mMenuList[i] = Constants.mSurveyMenuDetailsList.get(i).getMenuName();
                        }
                        break;
                    case 2:
                        if (Constants.surveyFormDetailsObj.getSurveyTypeDetails().contains(",")) {
                            mTypeList = Constants.surveyFormDetailsObj.getSurveyTypeDetails().split("\\,");
                        } else {
                            mTypeList = new String[1];
                            mTypeList[0] = Constants.surveyFormDetailsObj.getSurveyTypeDetails();
                        }
                        break;

                    case 3:
                        mMallMasterList = mAceDnsDatabase.GetMallMasterData(mType);
                        break;
                    case 4:
                        mMallMasterList = mAceDnsDatabase.GetMallMasterData(mType, mPinCode);
                        break;

                    case 5:
                        mFsSurveyPublishList = mAceDnsDatabase.GetFsSurveyPublishData(mType.toLowerCase(), mMallId, mPinCode);
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

    public void ShowMallHighStreetListDialog() {
        if (mMallMasterList.size() > 0) {
            final Dialog mMallHighStreetListDialog = new Dialog(SurveyMenuActivity.this,
                    R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);

            TextView title = (TextView) mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select a " + mType);

            ListView dialogList = (ListView) mMallHighStreetListDialog
                    .findViewById(R.id.list);

            if (mType.equalsIgnoreCase("hi-street")) {
                Constants.mSurveyType = "pincode";
            } else {
                Constants.mSurveyType = "mall";
            }

            final MallAdapter malladapter = new MallAdapter(SurveyMenuActivity.this, R.layout.mall_list, mMallMasterList);
            dialogList.setAdapter(malladapter);


            EditText searchText = (EditText) mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1,
                                          int arg2, int arg3) {
                    malladapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mMallHighStreetListDialog.cancel();
                    if (mType.equalsIgnoreCase("hi-street")) {
                        mPinCode = malladapter.getItem(arg2).getPincode();
                        PrepareSurveyMenuData(4);
                    } else {
                        Constants.selectedMallMaster = malladapter.getItem(arg2);
                        mMallId = Constants.selectedMallMaster.getMallId();
                        PrepareSurveyMenuData(1);
                    }
                }
            });

            ImageView back = (ImageView) mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    mMallHighStreetListDialog.cancel();
                }
            });

            Button cancel = (Button) mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);

            mMallHighStreetListDialog.show();


        } else {
            Utils.showToast(mContext, "You have no " + mType + " asigned");
        }
    }

    public void ShowAreaListDialog() {
        if (mMallMasterList.size() > 0) {
            final Dialog mMallHighStreetListDialog = new Dialog(SurveyMenuActivity.this,
                    R.style.PauseDialog);
            mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mMallHighStreetListDialog.setContentView(R.layout.select_with_search);
            mMallHighStreetListDialog.setCancelable(false);

            TextView title = (TextView) mMallHighStreetListDialog.findViewById(R.id.title);
            title.setText("Please select a " + mType);

            ListView dialogList = (ListView) mMallHighStreetListDialog
                    .findViewById(R.id.list);

            Constants.mSurveyType = "area";
            final MallAdapter malladapter = new MallAdapter(SurveyMenuActivity.this, R.layout.mall_list, mMallMasterList);
            dialogList.setAdapter(malladapter);


            EditText searchText = (EditText) mMallHighStreetListDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1,
                                          int arg2, int arg3) {
                    malladapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mMallHighStreetListDialog.cancel();
                    Constants.selectedMallMaster = malladapter.getItem(arg2);
                    mMallId = Constants.selectedMallMaster.getMallId();
                    PrepareSurveyMenuData(1);
                }
            });

            ImageView back = (ImageView) mMallHighStreetListDialog.findViewById(R.id.image_cancel);
            back.setVisibility(View.VISIBLE);
            back.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    mMallHighStreetListDialog.cancel();
                }
            });

            Button cancel = (Button) mMallHighStreetListDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);

            mMallHighStreetListDialog.show();
        } else {
            Utils.showToast(mContext, "You have no " + mType + " asigned");
        }
    }

    private void ShowOutletLis() {

        final FsOutletAdapter fsOutletAdapter = new FsOutletAdapter(mContext, R.layout.customer_list_child, mFsSurveyPublishList);

        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(true);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select an outlet");
        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                fsOutletAdapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });
        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(fsOutletAdapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
                Constants.selectedFsSurveyPublish = fsOutletAdapter.getItem(arg2);
                ShowConditionofSurvey();
            }
        });

        Button addCustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);

        Button btnback = (Button) mDialogCustomer.findViewById(R.id.back);
        btnback.setVisibility(View.VISIBLE);
        btnback.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
            }
        });
        mDialogCustomer.show();
    }

    public void ShowConditionofSurvey() {
        final Dialog dialgoCondition = new Dialog(SurveyMenuActivity.this, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_survey);
        dialgoCondition.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
        TextView txtMsg = (TextView) dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select an option.");
        final RadioGroup radioSelectionGroup = (RadioGroup) dialgoCondition.findViewById(R.id.radioSelect);

        radioSelectionGroup.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {

            public void onCheckedChanged(RadioGroup group, int checkedId) {
                RadioButton radioSelection = (RadioButton) dialgoCondition.findViewById(checkedId);

                String status = radioSelection.getText().toString().toUpperCase();

                if (radioSelection.getText().toString().equalsIgnoreCase("Edit")) {
                    Intent intent = new Intent(SurveyMenuActivity.this, SurveyActivityList.class);

                    intent.putExtra("SURVEYMENUID", mMenuID);
                    intent.putExtra("SURVEYMADEAT", mType);
                    startActivity(intent);
                } else {
                    String timeStamps = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    String rid = "RT" + Constants.employeeDetailObject.getEmpCode() + timeStamps;
                    String fsid = Constants.selectedFsSurveyPublish.getFsSurveyId();
                    String mallid = Constants.selectedFsSurveyPublish.getMallId();
                    String businessname = Constants.selectedFsSurveyPublish.getBusinessName();
                    GPSTracker gpstracker = new GPSTracker(mContext);
                    Boolean LocationEnabled = gpstracker.canGetLocation();
                    if (LocationEnabled) {
                        mAceDnsTransactionDatabase.InsertRedudantTransaction(rid, fsid, mallid, businessname, status);
                        mAceDnsTransactionDatabase.insertToLocationTable("RT", timeStamps);
                        gpstracker.stopUsingGPS();
                        mAceDnsTransactionDatabase.UpadateFsSurveyPublishStatus(fsid);
                        new TRANS_SubmitRedudantSurvey(mContext, true).execute();
                    }
//					else
//					{
//						//ask user to enable gps
//						gpstracker.showSettingsAlertToChangeTimeZone();
//					}


                }
                dialgoCondition.cancel();
            }
        });
        dialgoCondition.show();
    }


    public void ShowSurveyTypeListDialog() {

        final Dialog mDialogDepotName = new Dialog(mContext,
                R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title = (TextView) mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a type");
        ListView dialogList = (ListView) mDialogDepotName
                .findViewById(R.id.list);

        for (int count = 0; count < mTypeList.length; count++) {
            mTypeList[count] = mTypeList[count].toUpperCase();
        }


        final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
                R.layout.simple_list_child, R.id.list_details, mTypeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int pos,
                                    long arg3) {
                mType = adapter.getItem(pos).toLowerCase();
                mDialogDepotName.cancel();
                PrepareSurveyMenuData(3);
            }
        });


        ImageView back = (ImageView) mDialogDepotName.findViewById(R.id.image_cancel);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                mDialogDepotName.cancel();
            }
        });

        Button cancel = (Button) mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);

        mDialogDepotName.show();

    }

    /**
     * This Function is used to Show Menu List in a List View. User can select menu from the list.
     * After selecting menu new SurveyActivityList activity will open
     */

    public void ShowSurveyMenuList()
    {
        ParseData();
        if (mMenuList1 != null) {
            mMenuAdapter = new MenuAdapter(mContext, R.layout.grid_child, mMenuList1);
            mGridViewMenu.setAdapter(mMenuAdapter);
        } else {
            Utils.showToast(mContext, "No survey menu found. Please contact admin");
        }
        mGridViewMenu.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                String menu = mMenuList1.get(arg2).getFeatureName();
                Constants.selectedFsSurveyPublish = null;
                int position= getPositionByMenuName(menu);
                mMenuID = Constants.mSurveyMenuDetailsList.get(position).getMenuId();
                if (Constants.surveyFormDetailsObj.getSurveyOutletMenu() != null
                        && Constants.surveyFormDetailsObj.getSurveyOutletMenu().length() > 0) {
                    String menuid = Constants.surveyFormDetailsObj.getSurveyOutletMenu().trim();
                    if (menuid.contains(",")) {
                        String[] parsemenu = menuid.split(",");
                        boolean ispresent = false;
                        for (int count = 0; count < parsemenu.length; count++) {
                            if (parsemenu[count].equalsIgnoreCase(mMenuID)) {
                                ispresent = true;
                                break;
                            }
                        }
                        if (ispresent) {
                            PrepareSurveyMenuData(5);
                        } else {
                            Intent intent = new Intent(SurveyMenuActivity.this, SurveyActivityList.class);
                            intent.putExtra("SURVEYMENUID", mMenuID);
                            intent.putExtra("SURVEYMADEAT", mType);
                            startActivity(intent);
                        }
                    } else {
                        if (menuid.equalsIgnoreCase(mMenuID)) {
                            PrepareSurveyMenuData(5);
                        } else {
                            Intent intent = new Intent(SurveyMenuActivity.this, SurveyActivityList.class);
                            intent.putExtra("SURVEYMENUID", mMenuID);
                            intent.putExtra("SURVEYMADEAT", mType);
                            startActivity(intent);
                        }
                    }
                } else {
                    if (Constants.surveyFormDetailsObj.getSurveyLayer().equalsIgnoreCase("yes")) {
                        Intent intent = new Intent(SurveyMenuActivity.this, SurveyActivityList.class);
                        intent.putExtra("SURVEYMENUID", mMenuID);
                        intent.putExtra("SURVEYMADEAT", mType);
                        startActivity(intent);
                    } else {
                        Intent intent;

                        if (Constants.surveyFormDetailsObj.getspecial_input_screen().equalsIgnoreCase("yes") && checkConditionForSpecialScreen(menu))
                        {
                            intent = new Intent(mContext, SurveyActivitySpecial.class);
                        }
                        else
                        {
                            intent = new Intent(mContext, SurveyActivity.class);
                        }
                        intent.putExtra("SURVEYMENUID", mMenuID);
                        intent.putExtra("SURVEYMADEAT", mType);
                        intent.putExtra("SUBMENU", mSubMenu);
                        intent.putExtra("mMenuName", menu);
                        startActivity(intent);
                    }
                }
            }
        });
    }

    private int getPositionByMenuName(String menu)
    {
        int position=0;
        for(int i=0;i< mSurveyMenuDetailsList.size();i++)
        {
            String currentMenuName=mSurveyMenuDetailsList.get(i).getMenuName();
            if(currentMenuName.equalsIgnoreCase(menu))
            {
                position=i;
            }
        }
        return position;
    }

    private boolean checkConditionForSpecialScreen(String menu) {
        ArrayList<SurveyMenuDetails> mSurveyMenuDetailsList= mAceDnsDatabase.GetMenuNameForSpecial("", menu);
        Boolean isNewExisitngMenuPresent=true;
        if(mSurveyMenuDetailsList.size()!=2){
            isNewExisitngMenuPresent=false;
        }
        else if(!mSurveyMenuDetailsList.get(0).getMenuName().equalsIgnoreCase("new") || !mSurveyMenuDetailsList.get(1).getMenuName().equalsIgnoreCase("existing")){
            isNewExisitngMenuPresent=false;
        }
        return isNewExisitngMenuPresent;
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

}
