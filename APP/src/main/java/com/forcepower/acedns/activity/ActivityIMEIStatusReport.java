package com.forcepower.acedns.activity;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import androidx.fragment.app.FragmentActivity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.Date;

public class ActivityIMEIStatusReport extends FragmentActivity implements OnClickListener {

    public static Button mButtonBack = null;
    public static Button btn_search = null;
    public static ImageView mImageViewHeaderLogo = null;
    public static TextView etImeiStatus = null;

    public AceDnsDatabase mAceDnsDatabaseHelper;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    Date startDate, endDate;
    Date currentDate = new Date();

    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_imei_status_report);
        mContext = ActivityIMEIStatusReport.this;

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyyMMdd");

        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        InitializeView();

        mButtonBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        Utils.headerFooterIconChangesForRetailerApp(mContext,false);


    }



    public void InitializeView() {
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);
        mButtonBack = (Button) findViewById(R.id.back);

        etImeiStatus = (TextView) findViewById(R.id.imeiStatusTV);

        btn_search = (Button) findViewById(R.id.btn_search);
        btn_search.setOnClickListener(this);

        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    public void onClick(View v)
    {
        if (v == btn_search)
        {

            Constants.imei= etImeiStatus.getText().toString();
            if(Constants.imei.length()==15)
            {
                if(HTTPUtils.isConnectionPossible(mContext))
                {
                    Utils.showProgressDialog(mContext,"Getting IMEI Details.. Please Wait");
                    new Thread() {
                        public void run() {

                            new commonAsyncTaskMaster(mContext, "DOWNLOAD_imei_status");
                        }
                    }.start();
                }
                else
                {
                    Utils.showToast(mContext,"You need an active internet connection for searching.");
                }
            }
            else
            {
                Utils.showToast(mContext,"Please provide proper IMEI to search.");
            }

        }
    }

}