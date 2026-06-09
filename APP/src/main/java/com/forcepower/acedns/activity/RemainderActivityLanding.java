package com.forcepower.acedns.activity;

import static android.view.View.GONE;
import static com.forcepower.acedns.constants.Constants.mSurveyMenuDetailsList;

import androidx.fragment.app.FragmentActivity;

import android.app.Dialog;
import android.os.Message;
import android.view.View;
import android.view.View.OnClickListener;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CommonModelListAdapter;
import com.forcepower.acedns.adapter.KeyValueAdapter;
import com.forcepower.acedns.adapter.OrderMenuAdapter;
import com.forcepower.acedns.adapter.OutletAdapter;
import com.forcepower.acedns.adapter.SurveyDisplayMenuAdapter;
import com.forcepower.acedns.adapter.SurveyReportAdapter;
import com.forcepower.acedns.bean.CommonModel;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.bean.MenuObj;
import com.forcepower.acedns.bean.OutletDetails;
import com.forcepower.acedns.bean.SurveyMenuDetails;
import com.forcepower.acedns.bean.SurveyReport;
import com.forcepower.acedns.bean.SurveyReportSumary;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

public class RemainderActivityLanding extends FragmentActivity implements OnClickListener  {

    public static ImageView mImageViewHeaderLogo = null;
    public static Button mButtonBack = null;
    public static GridView mGridViewMenu = null;

    OrderMenuAdapter mMenuAdapter;
    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;

    Context mContext;
    ArrayList<MenuObj> mMenuList;
    ArrayList<OutletDetails> mOutletDetailsList;
    ArrayList<SurveyReport> mSurveyReportList;
    ArrayList<KeyValue> mSurveyDetailsList;
    SurveyReportSumary mSurveyReportSumary = null;
    String[] mTypeList;
    private int SELECTION = 0;
    private String mTimeStamp = "";
    private String mStartDate = "";
    private String mEndDate = "";
    private String mFStartDate = "";
    private String mFEndDate = "";
    private String mType = "";
    private String mTypeSubMenu = "";
    private String mTypeDisplayToHeader = "";
    private String surveymenudetails = "";
    private String mSurveyType = "";
    private String mQuery = "";
    private String mQuery2 = "";
    private String mSurveyID = "";
    private String mSurveySubMenuID = "";
    private ProgressDialog mPrepareSurveyMenuProgressDialog;
    private Handler mPrepareSurveyMenuHandler;
    private Handler mPrepareSurveyOutletHandler;
    private Handler mDCEReportHandler;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey_report_landing);
        RegisterActivities.registerActivity(this);
        mContext = RemainderActivityLanding.this;
        mAceDnsDatabase = new AceDnsDatabase(RemainderActivityLanding.this);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(RemainderActivityLanding.this);
        surveymenudetails = getIntent().getStringExtra("SURVEYSUBMENUDETAILS");
        if(Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("site")){
            surveymenudetails = "Site Visit,";
            if(Constants.nickName.equalsIgnoreCase("DURO")){
                surveymenudetails = "Site Visit,Facilitator Add,";
            }
        }
        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");
        mFStartDate = getIntent().getStringExtra("FSTARTDATE");
        mFEndDate = getIntent().getStringExtra("FENDDATE");
        InitializeView();
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        mGridViewMenu.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                String menu = mMenuList.get(arg2).getFeatureName();
                int number = 0;
                if (mMenuList.get(arg2).getCount().trim().length() > 0) {
                    number = Integer.parseInt(mMenuList.get(arg2).getCount());
                }
                if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("site")) {
                    if (Constants.nickName.equalsIgnoreCase("DURO")){

                    }
                    remainderSiteDialog(menu);

                }else {
                    DoOnClickJob(menu, number);
                }
            }
        });

        mPrepareSurveyMenuHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                RemainderActivityLanding.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                break;
                            case 2:
                                break;
                            case 3:
                                break;
                        }
                        ParseData(surveymenudetails);
                        if (mMenuList != null) {
                            mMenuAdapter = new OrderMenuAdapter(RemainderActivityLanding.this, R.layout.grid_child_value, mMenuList);
                            mGridViewMenu.setAdapter(mMenuAdapter);
                            mMenuAdapter.notifyDataSetChanged();
                        } else {
                            Utils.showToast(mContext, "No report menu found. Please Synchronize Data");
                        }
                    }
                });
            }
        };


        mPrepareSurveyOutletHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int dojob = threadmsg.getData().getInt("JOBALLOCATE");
                RemainderActivityLanding.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                if (mOutletDetailsList != null && mOutletDetailsList.size() > 0) {
                                    ShowOutletDeatails();
                                } else {
                                    Utils.showToast(mContext, "No record found.");
                                }
                                break;
                            case 2:
                                if (mOutletDetailsList != null && mOutletDetailsList.size() > 0) {
                                    ShowOutletDeatails();
                                } else {
                                    Utils.showToast(mContext, "No record found.");
                                }
                                break;
                            case 3:
                                if (mOutletDetailsList != null && mOutletDetailsList.size() > 0) {
                                    ShowOutletDeatails();
                                } else {
                                    Utils.showToast(mContext, "No record found.");
                                }
                                break;

                            case 4:
                                if (mSurveyDetailsList != null && mSurveyDetailsList.size() > 0) {
                                    //ShowOutletDeatails();
                                    ShowSurveyDeatails();
                                } else {
                                    Utils.showToast(mContext, "No record found.");
                                }

                                break;
                            case 5:
                                if (mSurveyMenuDetailsList != null && mSurveyMenuDetailsList.size() > 0) {
                                    ShowSurveyDeatailsListSubMenu();
                                } else {
                                    Utils.showToast(mContext, "No record found.");
                                }

                                break;
                        }

                    }
                });
            }
        };

        mDCEReportHandler = new Handler() {
            public void handleMessage(Message msg) {
                mPrepareSurveyMenuProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                RemainderActivityLanding.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (job) {
                            case 1:
                                if (mSurveyReportList.size() > 0) {
                                    ShowSurveyMenuList();
                                } else {
                                    switch (SELECTION) {
                                        case 1:
                                            Toast.makeText(mContext, "No record for today", Toast.LENGTH_SHORT).show();
                                            break;
                                        case 2:
                                            Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_SHORT).show();
                                            break;
                                        case 3:
                                            Toast.makeText(mContext, "No record found from " + mFStartDate + " to " + mFEndDate, Toast.LENGTH_SHORT).show();
                                            break;
                                    }
                                }
                                break;
                            case 2:
                                if (mSurveyReportList.size() > 0) {
                                    ShowSurveyMenuList();
                                } else {
                                    switch (SELECTION) {
                                        case 1:
                                            Toast.makeText(mContext, "No record for today", Toast.LENGTH_SHORT).show();
                                            break;
                                        case 2:
                                            Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_SHORT).show();
                                            break;
                                        case 3:
                                            Toast.makeText(mContext, "No record found from " + mFStartDate + " to " + mFEndDate, Toast.LENGTH_SHORT).show();
                                            break;
                                    }
                                }
                                break;
                            case 3:
                                if (mSurveyReportList.size() > 0) {
                                    ShowSurveyMenuList();
                                } else {
                                    switch (SELECTION) {
                                        case 1:
                                            Toast.makeText(mContext, "No record for today", Toast.LENGTH_SHORT).show();
                                            break;
                                        case 2:
                                            Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_SHORT).show();
                                            break;
                                        case 3:
                                            Toast.makeText(mContext, "No record found from " + mFStartDate + " to " + mFEndDate, Toast.LENGTH_SHORT).show();
                                            break;
                                    }
                                }
                                break;
                            case 4:
                                // showMasterListDialog();
                                if (mTypeList.length > 0) {
                                    if (mTypeList.length == 1) {
                                        mSurveyType = mTypeList[0];
                                        mQuery = BuildQuery(SELECTION);
                                        FetchSurveyDCEData(SELECTION);
                                    } else {
                                        ShowSurveyTypeListDialog();
                                    }
                                } else {
                                    Utils.showToast(mContext, "You have no type details");
                                }
                                break;
                        }
                    }
                });
            }
        };

        if (Constants.surveyFormDetailsObj.getSurveySubMenu().equalsIgnoreCase("yes"))
        {
            PrepareSurveyMenuData(SELECTION);
        }
        else
        {
            mType="All Survey";
            PrepareSurveyOutletData(SELECTION);
        }

    }

    @Override
    public void onClick(View v) {


    }

    private void InitializeView() {
        mButtonBack = (Button) findViewById(R.id.back);
        mGridViewMenu = (GridView) findViewById(R.id.grid_menu);
		/*if(mMenuList!=null){
			mMenuAdapter = new OrderMenuAdapter(ActivitySurveyReportLanding.this, R.layout.grid_child,mMenuList);
			mGridViewMenu.setAdapter(mMenuAdapter);
		}else{
			Utils.showToast(mContext, "No survey menu found. Please Synchronize Data");
		}*/

        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(GONE);
        }

    }

    private void ParseData(String data)
    {
        mMenuList = new ArrayList<MenuObj>();
        if (data.contains(",")) {
            String[] surveymenu = data.split(",");
            for (int count = 0; count < surveymenu.length; count++) {
                String menuname = surveymenu[count];

                MenuObj menuObj = new MenuObj();
                if (menuname.equalsIgnoreCase("Site Visit"))
                {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess("Site Visit");
                    if (dcmaccess == true)
                    {
                        menuObj.setResourceId(R.drawable.sitevisit);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getNoSiteVisit());
                        mMenuList.add(menuObj);
                    }
                }
                else if (menuname.equalsIgnoreCase("Facilitator Add"))
                {
                    boolean dcmaccess = mAceDnsDatabase.MenuAccess(menuname);
                    if (dcmaccess == true)
                    {
                        menuObj.setResourceId(R.drawable.fa_add);
                        menuObj.setFeatureName(menuname);
                        menuObj.setCount(mSurveyReportSumary.getnoFacilitaorAdd());
                        mMenuList.add(menuObj);
                    }
                }

            }
        }
    }

    public void ShowSurveyMenuList() {
        final Dialog mMallHighStreetListDialog = new Dialog(RemainderActivityLanding.this, R.style.PauseDialog);
        mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mMallHighStreetListDialog.setContentView(R.layout.select_from_list);
        mMallHighStreetListDialog.setCancelable(false);

        TextView title = (TextView) mMallHighStreetListDialog.findViewById(R.id.title);
        title.setText("Menu List");
        ListView dialogList = (ListView) mMallHighStreetListDialog.findViewById(R.id.list);
        final SurveyReportAdapter adapter = new SurveyReportAdapter(RemainderActivityLanding.this, R.layout.activity_survey_menu_child, mSurveyReportList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int arg2, long arg3) {
                mMallHighStreetListDialog.cancel();
                if (mSurveyReportList.get(arg2).getMenuId().equalsIgnoreCase("RA115")) {
                    PrepareSurveyOutletData(SELECTION);
                }
            }
        });

        Button btncancel = (Button) mMallHighStreetListDialog.findViewById(R.id.btn_cncl);
        btncancel.setVisibility(GONE);
        mMallHighStreetListDialog.show();

    }

    private void DoOnClickJob(String menu, int number) {
        mType = menu;
        if(Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(menu) )
        {
            PrepareSurveyOutletData(5);
        }
        else
        {
            PrepareSurveyOutletData(1);
        }


    }

    private void ShowSurveyDeatails()
    {
        final Dialog mMallHighStreetListDialog = new Dialog(mContext,  android.R.style.Theme_DeviceDefault_Light_NoActionBar_Fullscreen);
        mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mMallHighStreetListDialog.setContentView(R.layout.select_from_list);
        mMallHighStreetListDialog.setCancelable(false);

        TextView title = (TextView) mMallHighStreetListDialog.findViewById(R.id.title);
        title.setText(mTypeDisplayToHeader);
        ListView dialogList = (ListView) mMallHighStreetListDialog.findViewById(R.id.list);
        dialogList.setDivider(null);
        KeyValueAdapter adapter = new KeyValueAdapter(mContext, R.layout.keyvalue_child, mSurveyDetailsList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {

            }
        });

        Button btncancel = (Button) mMallHighStreetListDialog.findViewById(R.id.btn_cncl);
        btncancel.setText("Back to List");
        btncancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mMallHighStreetListDialog.cancel();
            }
        });
        mMallHighStreetListDialog.show();
    }
    private void ShowSurveyDeatailsListSubMenu()
    {
        final Dialog mMallHighStreetListDialog = new Dialog(RemainderActivityLanding.this, R.style.PauseDialog);
        mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mMallHighStreetListDialog.setContentView(R.layout.select_from_list);
        mMallHighStreetListDialog.setCancelable(false);

        TextView title = (TextView) mMallHighStreetListDialog.findViewById(R.id.title);

        title.setText("List : " + mType);
        mTypeDisplayToHeader=mType;

        ListView dialogList = (ListView) mMallHighStreetListDialog.findViewById(R.id.list);
       /* if(Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("site")){
            ArrayList<SurveyMenuDetails> mSurveyMenuDetailsList= new ArrayList<SurveyMenuDetails>();
            SurveyMenuDetails temp = new SurveyMenuDetails();
            temp.setMenuID("0");
            temp.setMenuName("Site Visit");
            mSurveyMenuDetailsList.add(temp);
        }*/
        final SurveyDisplayMenuAdapter adapter = new SurveyDisplayMenuAdapter(RemainderActivityLanding.this, R.layout.customer_broker_list_child, mSurveyMenuDetailsList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                SurveyMenuDetails obj = adapter.getItem(arg2);
                mSurveySubMenuID = obj.getMenuId();
                mTypeDisplayToHeader=mType+":"+obj.getMenuName();
                PrepareSurveyOutletData(SELECTION);

            }
        });

        Button btncancel = (Button) mMallHighStreetListDialog.findViewById(R.id.btn_cncl);
        btncancel.setText("Back to Menu");
        btncancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mMallHighStreetListDialog.cancel();
                if(mType.equalsIgnoreCase("all survey"))
                {
                    finish();
                }
            }
        });
        mMallHighStreetListDialog.show();
    }

    private void ShowOutletDeatails() {
        final Dialog mMallHighStreetListDialog = new Dialog(RemainderActivityLanding.this, R.style.PauseDialog);
        mMallHighStreetListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mMallHighStreetListDialog.setContentView(R.layout.select_from_list);
        mMallHighStreetListDialog.setCancelable(false);

        TextView title = (TextView) mMallHighStreetListDialog.findViewById(R.id.title);
        if (mType.equalsIgnoreCase("FS") || mType.equalsIgnoreCase("DCE") || mType.equalsIgnoreCase("DCA"))
        {
            title.setText("List : Outlet");
        }
        else if(mType.equalsIgnoreCase("Customer Add"))
        {
            title.setText("List : Existing Customer");
        }
        else
        {
            title.setText("List : " + mType);
            mTypeDisplayToHeader=mType;
        }
        ListView dialogList = (ListView) mMallHighStreetListDialog.findViewById(R.id.list);
        final OutletAdapter adapter = new OutletAdapter(RemainderActivityLanding.this, R.layout.customer_broker_list_child, mOutletDetailsList);
        dialogList.setAdapter(adapter);

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                OutletDetails obj = adapter.getItem(arg2);
                mSurveyID = obj.getSurveyID();
                if (mType.equalsIgnoreCase("New IHB"))
                {
                    PrepareSurveyOutletData(4);
                }
                else if (mType.equalsIgnoreCase("Existing IHB"))
                {
                    PrepareSurveyOutletData(4);
                }
                else if (mType.equalsIgnoreCase("IHB Site & Complaint Visit"))
                {
                    PrepareSurveyOutletData(4);
                } else if (mType.equalsIgnoreCase("New Dealer"))
                {
                    PrepareSurveyOutletData(4);
                }
                else if (mType.equalsIgnoreCase("New Sub Dealer"))
                {
                    PrepareSurveyOutletData(4);
                }
                else if (mType.equalsIgnoreCase("All Survey"))
                {
                    PrepareSurveyOutletData(4);
                }
                else if (mType.equalsIgnoreCase("KYC"))
                {
                    PrepareSurveyOutletData(4);
                }
                else if (mType.equalsIgnoreCase("Technical Meets"))
                {
                    PrepareSurveyOutletData(4);
                }
                else if (mType.equalsIgnoreCase("Branding"))
                {
                    PrepareSurveyOutletData(4);
                }
                else if (mType.equalsIgnoreCase("Site Visit") || mType.equalsIgnoreCase("Facilitator Add") || mType.equalsIgnoreCase("Customer Add") ||  mType.equalsIgnoreCase("Branding Verification") ||  mType.equalsIgnoreCase("farmer visit") )
                {
                    PrepareSurveyOutletData(4);
                }
                else
                {
                    mMallHighStreetListDialog.cancel();
                }
            }
        });

        Button btncancel = (Button) mMallHighStreetListDialog.findViewById(R.id.btn_cncl);
        btncancel.setText("Back to Menu");
        btncancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mMallHighStreetListDialog.cancel();
                if(mType.equalsIgnoreCase("all survey"))
                {
                    finish();
                }
            }
        });
        mMallHighStreetListDialog.show();
    }

    public void ShowSurveyTypeListDialog() {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
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
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int pos,
                                    long arg3) {
                mSurveyType = adapter.getItem(pos).toLowerCase();
                mDialogDepotName.cancel();
                mQuery = BuildQuery(SELECTION);
                FetchSurveyDCEData(SELECTION);
            }
        });
        Button btncancel = (Button) mDialogDepotName.findViewById(R.id.btn_cncl);
        btncancel.setVisibility(GONE);
        mDialogDepotName.show();

    }

    private String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                mTimeStamp = Constants.dateString;
                condition = "substr(LO.trans_id,-14,8)='" + mTimeStamp + "'";
                mQuery2 = "substr(survey_id,-14,8)='" + mTimeStamp + "'";
                break;
            case 2:
                mTimeStamp = Constants.dateString.substring(0, 6);
                condition = "substr(LO.trans_id,-14,6)='" + mTimeStamp + "'";
                mQuery2 = "substr(survey_id,-14,6)='" + mTimeStamp + "'";
                break;
            case 3:
                condition = "substr(LO.trans_id,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'";
                mQuery2 = "substr(survey_id,-14,8) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "'";
                break;
        }
        return condition;
    }

    public void PrepareSurveyMenuData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Please wait..");
        mPrepareSurveyMenuProgressDialog.setCancelable(false);
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 0:
                        mTimeStamp = Constants.dateString;
                        Date c = Calendar.getInstance().getTime();
                        System.out.println("Current time => " + c);

                        SimpleDateFormat df = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
                        String formattedDate = df.format(c);
                        mSurveyReportSumary = mAceDnsTransactionDatabase.getSurveyReportSummeryRemainderSyl(formattedDate);
                        break;
                    case 2:
                        mTimeStamp = Constants.dateString.substring(0, 6);
                        mSurveyReportSumary = mAceDnsTransactionDatabase.getSurveyReportSummery(mTimeStamp);
                        break;
                    case 3:
                        mSurveyReportSumary = mAceDnsTransactionDatabase.getSurveyReportSummery(mStartDate, mEndDate);
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


    public void PrepareSurveyOutletData(final int task) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Please wait..");
        mPrepareSurveyMenuProgressDialog.setCancelable(false);
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        mTimeStamp = Constants.dateString;
                        Date c = Calendar.getInstance().getTime();
                        System.out.println("Current time => " + c);

                        SimpleDateFormat df = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
                        String formattedDate = df.format(c);
                        if(Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(mType) )
                        {
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetailsWithSubMenu(mType, mTimeStamp,mSurveySubMenuID);
                        }
                        else
                        {
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetailsRemainder(mType, formattedDate);
                        }

                        break;
                    case 2:
                        mTimeStamp = Constants.dateString.substring(0, 6);
                        if(Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(mType) )
                        {
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetailsWithSubMenu(mType, mTimeStamp,mSurveySubMenuID);
                        }
                        else
                        {
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetails(mType, mTimeStamp);
                        }

                        break;
                    case 3:
                        if(Constants.surveyFormDetailsObj.getmenu_disp_sub_menu().contains(mType) )
                        {
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetailsWithSubMenu(mType, mStartDate, mEndDate,mSurveySubMenuID);
                        }
                        else
                        {
                            mOutletDetailsList = mAceDnsDatabase.GetOutletDetails(mType, mStartDate, mEndDate);
                        }

                        break;
                    case 4:
                        mSurveyDetailsList = mAceDnsDatabase.GetSurveyDetails(mSurveyID);
                        break;
                    case 5:
                        mTimeStamp = Constants.dateString;
                        mAceDnsDatabase.GetMenuName();
                        break;
                }

                Message msg = mPrepareSurveyOutletHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyOutletHandler.sendMessage(msg);
            }
        }.start();
    }


    public void FetchSurveyDCEData(final int whattodo) {
        mPrepareSurveyMenuProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyMenuProgressDialog.setMessage("Please wait..");
        mPrepareSurveyMenuProgressDialog.setCancelable(false);
        mPrepareSurveyMenuProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        mSurveyReportList = mAceDnsDatabase.GetSurveyReportMenuDetails(mQuery, mSurveyType);
                        if (mSurveyReportList.size() > 0) {
                            for (int count = 0; count < mSurveyReportList.size(); count++) {
                                String id = mSurveyReportList.get(count).getMenuId();
                                String total = mAceDnsDatabase.GetMenuWiseTotalSurvey(mQuery2, id);
                                mSurveyReportList.get(count).setTotal(total);
                            }
                        }
                        break;
                    case 2:
                        mSurveyReportList = mAceDnsDatabase.GetSurveyReportMenuDetails(mQuery, mSurveyType);
                        if (mSurveyReportList.size() > 0) {
                            for (int count = 0; count < mSurveyReportList.size(); count++) {
                                String id = mSurveyReportList.get(count).getMenuId();
                                String total = mAceDnsDatabase.GetMenuWiseTotalSurvey(mQuery2, id);
                                mSurveyReportList.get(count).setTotal(total);
                            }
                        }
                        break;

                    case 3:
                        mSurveyReportList = mAceDnsDatabase.GetSurveyReportMenuDetails(mQuery, mSurveyType);
                        if (mSurveyReportList.size() > 0) {
                            for (int count = 0; count < mSurveyReportList.size(); count++) {
                                String id = mSurveyReportList.get(count).getMenuId();
                                String total = mAceDnsDatabase.GetMenuWiseTotalSurvey(mQuery2, id);
                                mSurveyReportList.get(count).setTotal(total);
                            }
                        }
                        break;
                    case 4:
                        if (Constants.surveyFormDetailsObj.getSurveyTypeDetails().contains(",")) {
                            mTypeList = Constants.surveyFormDetailsObj.getSurveyTypeDetails().split("\\,");
                        } else {
                            mTypeList = new String[1];
                            mTypeList[0] = Constants.surveyFormDetailsObj.getSurveyTypeDetails();
                        }
                        break;
                }
                Message msg = mDCEReportHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("JOBDONE", whattodo);
                msg.setData(b);
                mDCEReportHandler.sendMessage(msg);
            }
        }.start();
    }

    public void remainderSiteDialog(String menu){
        Dialog routeDialog;

        SurveyReportSumary mSurveyReportSumary = null,mSurveyReportSumaryT = null;
        routeDialog = new Dialog(RemainderActivityLanding.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.remainder_site_visit_dialog);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Today's Reminder");
        TextView siteVisit = (TextView) routeDialog.findViewById(R.id.txtSiteVisitCount);
        TextView siteVisitUp = (TextView) routeDialog.findViewById(R.id.txtSiteVisitUpcommingCount);
        siteVisitUp.setVisibility(GONE);
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        Date c = Calendar.getInstance().getTime();
        System.out.println("Current time => " + c);

        SimpleDateFormat df = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());
        String formattedDate = df.format(c);
        ArrayList<CommonModel> data
                = mAceDnsTransactionDatabase.getSurveyReportSummerySiteVisitRemainderCounter(formattedDate,menu);
        //mSurveyReportSumaryT = mAceDnsTransactionDatabase.getSurveyReportSummerySiteVisitRemainderCounter("no","Site Visit");

       // siteVisit.setText("Site Visit Reminder - "+data.size());

        mSurveyReportSumary = mAceDnsTransactionDatabase.upcommingVisitCount(formattedDate,menu);
        siteVisit.setText(menu +" Reminder - "+data.size()+"\n Upcomming "+mSurveyReportSumary.getNoUpcommingVisit());


        data
                = mAceDnsTransactionDatabase.getSurveyReportSummerySiteVisitRemainderCounterWithUpcomming(formattedDate,menu);


        CommonModelListAdapter adapter1 = new CommonModelListAdapter(RemainderActivityLanding.this, data);
        dialogList.setAdapter(adapter1);
        //final SurveyDisplayMenuAdapter adapter = new SurveyDisplayMenuAdapter(MenuActivity.this, R.layout.customer_broker_list_child, mSurveyMenuDetailsList);
        //dialogList.setAdapter(adapter);

        ImageView cancelDialog = (ImageView) routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
            }
        });

        routeDialog.show();

    }


}
