package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.adapter.MenuAdapter;
import com.forcepower.acedns.bean.MenuObj;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.market_feedback.MarketFeedbackSBGActivity;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import com.forcepower.acedns.R;

import java.util.ArrayList;

public class ActivityMarketFeedbackLanding extends Activity implements OnClickListener {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static GridView mGridViewMenu = null;

    MenuAdapter mMenuAdapter;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    ArrayList<MenuObj> mMenuList;
    String marketfeedbacksubmenudetails = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey_landing);
        RegisterActivities.registerActivity(this);
        mContext = ActivityMarketFeedbackLanding.this;
        mAceDnsDatabase = new AceDnsDatabase(ActivityMarketFeedbackLanding.this);
        marketfeedbacksubmenudetails = Constants.marketFeedbackDetailsObj.getMfSubMenuDetails();

        ParseData(marketfeedbacksubmenudetails);
        InitializeView();

        mButtonBack.setOnClickListener(v -> finish());

        mGridViewMenu.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            String menu = mMenuList.get(arg2).getFeatureName();
            DoOnClickJob(menu);
        });
    }

    @Override
    public void onClick(View v) {
    }

    @SuppressLint("SetTextI18n")
    private void InitializeView() {
        mButtonBack = findViewById(R.id.back);
        mGridViewMenu = findViewById(R.id.grid_menu);
        if (mMenuList != null) {
            mMenuAdapter = new MenuAdapter(ActivityMarketFeedbackLanding.this, R.layout.grid_child, mMenuList);
            mGridViewMenu.setAdapter(mMenuAdapter);
        } else {
            Utils.showToast(mContext, "No market feedback menu found. Please Synchronize Data");
        }

        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivityMarketFeedbackLanding.this) + "~" + Utils.getDBVersion(ActivityMarketFeedbackLanding.this));
    }

    private void ParseData(String data) {
        mMenuList = new ArrayList<>();
        if (data.contains(",")) {
            String[] surveymenu = data.split(",");
            for (String menuname : surveymenu) {
                MenuObj menuObj = new MenuObj();
                if (menuname.equalsIgnoreCase("mf_stock")) {
                    boolean mfstkaccess = mAceDnsDatabase.MenuAccess("mf_stock");
                    if (mfstkaccess) {
                        menuObj.setResourceId(R.drawable.mfs);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }
                if (menuname.equalsIgnoreCase("WSP")) {
                    boolean mfstkaccess = mAceDnsDatabase.MenuAccess("wsp");
                    if (mfstkaccess) {
                        menuObj.setResourceId(R.drawable.wsp);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }
                if (menuname.equalsIgnoreCase("RSP")) {
                    boolean mfstkaccess = mAceDnsDatabase.MenuAccess("rsp");
                    if (mfstkaccess) {
                        menuObj.setResourceId(R.drawable.rsp);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }
                if (menuname.equalsIgnoreCase("mf_price")) {
                    boolean mfprcaccess = mAceDnsDatabase.MenuAccess("mf_price");
                    if (mfprcaccess) {
                        menuObj.setResourceId(R.drawable.mfp);
                        menuObj.setFeatureName(menuname);
                        mMenuList.add(menuObj);
                    }
                }
            }
        }
    }

    private void DoOnClickJob(String menu) {
        if (menu.equalsIgnoreCase("mf_stock")) {
            Log.d("TAG", "doOnItemClickJob 11 : ActivityMarketFeedbackStock");
            Intent intent = new Intent(ActivityMarketFeedbackLanding.this, ActivityMarketFeedbackStock.class);
            startActivity(intent);
        }
        if (menu.equalsIgnoreCase("WSP") || menu.equalsIgnoreCase("RSP")) {
            Intent intent = new Intent(ActivityMarketFeedbackLanding.this, ActivityMarketFeedbackWSPRSP.class);
            intent.putExtra("menuType", menu);
            startActivity(intent);
        } else if (menu.equalsIgnoreCase("mf_price")) {
            Intent intent = new Intent(ActivityMarketFeedbackLanding.this, ActivityMarketFeedBack.class);
            startActivity(intent);
        }
    }
}
