package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SaudaBookedCustomerAdapter;
import com.forcepower.acedns.adapter.SaudaProductAdapter;
import com.forcepower.acedns.adapter.SaudaProductGroupAdapter;
import com.forcepower.acedns.bean.SauadaBookingCustomerDetails;
import com.forcepower.acedns.bean.SaudaBookingProductDetails;
import com.forcepower.acedns.bean.SaudaBookingProductGroupDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class SauadaReportActivity extends FragmentActivity implements OnClickListener {


    public static Button mButtonBack = null;
    public static Button mButtonStartDate = null;
    public static Button mButtonEndDate = null;
    public static Button mButtonSubmitDate = null;


    public static ImageView mImageViewHeaderLogo = null;
    public static ImageView mImageViewSaudaBooked = null;
    public static ImageView mImageViewShowDuration = null;
    public static ImageView mImageViewHideDuration = null;

    public static TextView mTextViewNoofSauda = null;
    public static TextView mTextViewStartDate = null;
    public static TextView mTextViewEndDate = null;

    public static FrameLayout mFrameLayoutToday = null;
    public static FrameLayout mFrameLayoutMTD = null;
    public static FrameLayout mFrameLayoutCustom = null;

    public static RelativeLayout mRelativeLayoutDuration = null;
    public static LinearLayout mCustomDateLayout = null;

    public AceDnsDatabase mAceDnsDatabaseHelper;

    public Context mContext;

    public ProgressDialog mProgressDialog;

    public Handler mReportHandler;
    public boolean isStartDate = false;
    /*
     * This variable is used to select the report duration
     * Example SELECTION=0 for today
     */
    public int SELECTION = 0;
    public String mCustomerCode = "";
    public String mProductGroupCode = "";
    public String mTime = "";
    public String mCurrentMonth = "";
    public String mCurrentYear = "";
    public String mStartDate = "";
    public String mEndDate = "";
    public String mToday = "";
    public String mQuery = "";
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;


    ArrayList<SauadaBookingCustomerDetails> mSBCList;
    ArrayList<SaudaBookingProductGroupDetails> mSBPGList;
    ArrayList<SaudaBookingProductDetails> mSBPList;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sauda_report);
        RegisterActivities.registerActivity(this);
        mContext = SauadaReportActivity.this;

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");

        if (SELECTION == 3 && mStartDate.length() == 8 && mEndDate.length() == 8) {
            mStartDate = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mStartDate);
            mEndDate = Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", mEndDate);
        }


        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        InitializeView();


        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyy-MM-dd");

        mToday = mDFormatBackEnd.format(currentDate);

        mSBCList = new ArrayList<SauadaBookingCustomerDetails>();
        mSBPGList = new ArrayList<SaudaBookingProductGroupDetails>();
        mSBPList = new ArrayList<SaudaBookingProductDetails>();

        mTime = Constants.dateString;
        GETCurrentMonthYear(mTime);

        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });


        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate = date;
                    mTextViewStartDate.setText(mDFormatFrontEnd.format(date));
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    mEndDate = mDFormatBackEnd.format(date);
                }
                dialogCaldroidFragment.dismiss();
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {

            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };

        mImageViewSaudaBooked.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (mSBCList.size() > 0) {
                    ShowSaudaCustomerList();
                } else {
                    Toast.makeText(mContext, "Sauda booked for no customer", Toast.LENGTH_LONG).show();

                }
            }

        });

        mReportHandler = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                SauadaReportActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (job) {
                            case 1:
                                if (mSBCList.size() > 0) {
                                    mTextViewNoofSauda.setText(String.valueOf(mSBCList.size()));
                                } else {
                                    mTextViewNoofSauda.setText("0");
                                    switch (SELECTION) {
                                        case 1:
                                            Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                                            break;
                                        case 2:
                                            Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
                                            break;
                                        case 3:
                                            Toast.makeText(mContext, "No record found from " + mStartDate + " to"
                                                    + mEndDate, Toast.LENGTH_LONG).show();
                                            mStartDate = "";
                                            mEndDate = "";
                                            break;
                                    }
                                }

                                break;
                            case 2:
                                ShowSaudaBookedProductGroupList();
                                break;
                            case 3:
                                ShowSaudaBookedProductList();
                                break;
                            case 4:
                                // showMasterListDialog();
                                break;
                        }
                    }
                });
            }
        };

        if (SELECTION == 0) {
            SELECTION = 1;
        }
        ChangeBackground(SELECTION);
        mQuery = BuildQuery(SELECTION);
        FetchSaudaData(1);

    }

    public void ShowSaudaCustomerList() {

        if (mSBCList.size() > 0) {
            final Dialog mDialogCustomerList = new Dialog(SauadaReportActivity.this,
                    R.style.PauseDialog);
            mDialogCustomerList.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomerList.setContentView(R.layout.activity_list_layout);
            mDialogCustomerList.setCancelable(false);

            TextView title = (TextView) mDialogCustomerList.findViewById(R.id.title);
            title.setText("Please select a Customer");

            TextView Header1 = (TextView) mDialogCustomerList.findViewById(R.id.textViewHeader1);
            Header1.setText("Customer Name");

            ListView mDialogList = (ListView) mDialogCustomerList
                    .findViewById(R.id.list);

            SaudaBookedCustomerAdapter sbcadapter = new SaudaBookedCustomerAdapter(SauadaReportActivity.this,
                    R.layout.activity_sauda_report_customerinfo, mSBCList);
            mDialogList.setAdapter(sbcadapter);


            mDialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mDialogCustomerList.cancel();
                    mCustomerCode = mSBCList.get(arg2).getCustomerCode();
                    FetchSaudaData(2);
                }
            });

            Button cancel = (Button) mDialogCustomerList.findViewById(R.id.btn_cncl);
            cancel.setOnClickListener(new View.OnClickListener() {
                public void onClick(View v) {
                    mDialogCustomerList.cancel();
                }
            });

            mDialogCustomerList.show();
        }
    }

    public void ShowSaudaBookedProductGroupList() {

        if (mSBPGList.size() > 0) {
            final Dialog mDialogBookedProductGroupList = new Dialog(SauadaReportActivity.this,
                    R.style.PauseDialog);
            mDialogBookedProductGroupList.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogBookedProductGroupList.setContentView(R.layout.activity_list_layout);
            mDialogBookedProductGroupList.setCancelable(false);

            TextView title = (TextView) mDialogBookedProductGroupList.findViewById(R.id.title);
            title.setText("Please select a Product");

            TextView Header1 = (TextView) mDialogBookedProductGroupList.findViewById(R.id.textViewHeader1);
            Header1.setText("Product Group Name");

            ListView mDialogList = (ListView) mDialogBookedProductGroupList
                    .findViewById(R.id.list);

            SaudaProductGroupAdapter SPGAdapter = new SaudaProductGroupAdapter(SauadaReportActivity.this,
                    R.layout.activity_sauda_report_customerinfo, mSBPGList);
            mDialogList.setAdapter(SPGAdapter);


            mDialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mDialogBookedProductGroupList.cancel();
                    mProductGroupCode = mSBPGList.get(arg2).getProductGroupCode();
                    FetchSaudaData(3);
                }
            });

            Button cancel = (Button) mDialogBookedProductGroupList.findViewById(R.id.btn_cncl);
            cancel.setOnClickListener(new View.OnClickListener() {
                public void onClick(View v) {
                    mDialogBookedProductGroupList.cancel();
                }
            });

            mDialogBookedProductGroupList.show();
        }
    }

    public void ShowSaudaBookedProductList() {

        if (mSBPList.size() > 0) {
            final Dialog mDialogBookedProductList = new Dialog(SauadaReportActivity.this,
                    R.style.PauseDialog);
            mDialogBookedProductList.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogBookedProductList.setContentView(R.layout.activity_list_layout);
            mDialogBookedProductList.setCancelable(false);

            TextView title = (TextView) mDialogBookedProductList.findViewById(R.id.title);
            title.setText("Sauda Product");

            TextView Header1 = (TextView) mDialogBookedProductList.findViewById(R.id.textViewHeader1);
            Header1.setText("Product Name");

            TextView Header3 = (TextView) mDialogBookedProductList.findViewById(R.id.textViewHeader3);
            Header3.setVisibility(View.VISIBLE);

            ListView mDialogList = (ListView) mDialogBookedProductList
                    .findViewById(R.id.list);

            SaudaProductAdapter SPAdapter = new SaudaProductAdapter(SauadaReportActivity.this,
                    R.layout.activity_sauda_report_customerinfo, mSBPList);
            mDialogList.setAdapter(SPAdapter);


            mDialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mDialogBookedProductList.cancel();
                }
            });

            Button cancel = (Button) mDialogBookedProductList.findViewById(R.id.btn_cncl);
            cancel.setOnClickListener(new View.OnClickListener() {
                public void onClick(View v) {
                    mDialogBookedProductList.cancel();
                }
            });

            mDialogBookedProductList.show();
        }

    }

    public void InitializeView() {
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imageViewLogo);
        mImageViewSaudaBooked = (ImageView) findViewById(R.id.imgageViewSaudaDetails);

        mImageViewShowDuration = (ImageView) findViewById(R.id.image_clk);
        mImageViewHideDuration = (ImageView) findViewById(R.id.image_go);

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);

        mRelativeLayoutDuration = (RelativeLayout) findViewById(R.id.duration_layout);
        mCustomDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);


        mTextViewNoofSauda = (TextView) findViewById(R.id.textViewNoofSauda);
        mButtonBack = (Button) findViewById(R.id.back);

        mFrameLayoutToday = (FrameLayout) findViewById(R.id.btn_today);
        mFrameLayoutMTD = (FrameLayout) findViewById(R.id.btn_mtd);
        mFrameLayoutCustom = (FrameLayout) findViewById(R.id.btn_custom);

        mFrameLayoutToday.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);

        mTextViewStartDate = (TextView) findViewById(R.id.txt_start_date);
        mTextViewEndDate = (TextView) findViewById(R.id.txt_end_date);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");


        mButtonStartDate = (Button) findViewById(R.id.btn_start_date);
        mButtonEndDate = (Button) findViewById(R.id.btn_end_date);
        mButtonSubmitDate = (Button) findViewById(R.id.btn_date_done);

        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);
    }


    public void ChangeBackground(int select) {
        mFrameLayoutToday.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mFrameLayoutMTD.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mFrameLayoutCustom.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mCustomDateLayout.startAnimation(bottomUp);
        mCustomDateLayout.setVisibility(View.GONE);

        switch (select) {
            case 1:
                mFrameLayoutToday.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
            case 2:
                mFrameLayoutMTD.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
            case 3:
                mFrameLayoutCustom.setBackgroundColor(Color.parseColor("#B6B6B4"));
                mCustomDateLayout.startAnimation(bottomUp);
                mCustomDateLayout.setVisibility(View.VISIBLE);
                break;
        }
    }

    public void ChooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        Bundle bundle = new Bundle();
        bundle.putString(CaldroidFragment.DIALOG_TITLE, "Select a date");
        dialogCaldroidFragment.setArguments(bundle);
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    public String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                condition = "substr(LO.date,1,10)='" + mToday + "' ";
                break;
            case 2:
                condition = "substr(LO.date,1,4)='" + mCurrentYear + "' AND substr(LO.date,6,2)='" + mCurrentMonth + "' ";
                break;
            case 3:
                condition = "substr(LO.date,1,10) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
                break;
        }
        return condition;
    }

    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
    }


    public void FetchSaudaData(final int whattodo) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Fetching Data.\nPlease wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        mSBCList = mAceDnsDatabaseHelper.GetSauadaBookingCustomerDetails(mQuery);
                        break;
                    case 2:
                        mSBPGList = mAceDnsDatabaseHelper.GetSauadaBookingProductGroupDetails(mCustomerCode, mQuery);
                        break;
                    case 3:
                        mSBPList = mAceDnsDatabaseHelper.GetSauadaBookingProductDetails(mCustomerCode, mProductGroupCode, mQuery);
                        break;
                    case 4:
                        break;
                }
                Message msg = mReportHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("JOBDONE", whattodo);
                msg.setData(b);
                mReportHandler.sendMessage(msg);
            }
        }.start();
    }


    @Override
    public void onClick(View v) {
        if (v == mImageViewHideDuration) {
            mImageViewShowDuration.startAnimation(fadeIn);
            mImageViewShowDuration.setVisibility(View.VISIBLE);
            mRelativeLayoutDuration.startAnimation(bottomDown);
            mRelativeLayoutDuration.setVisibility(View.GONE);
        } else if (v == mImageViewShowDuration) {
            mImageViewShowDuration.startAnimation(fadeOut);
            mImageViewShowDuration.setVisibility(View.GONE);
            mRelativeLayoutDuration.startAnimation(bottomUp);
            mRelativeLayoutDuration.setVisibility(View.VISIBLE);
        } else if (v == mFrameLayoutToday) {
            SELECTION = 1;
            ChangeBackground(SELECTION);
            mQuery = BuildQuery(SELECTION);
            FetchSaudaData(1);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            mQuery = BuildQuery(SELECTION);
            FetchSaudaData(1);
        } else if (v == mFrameLayoutCustom) {
            SELECTION = 3;
            ChangeBackground(SELECTION);
        } else if (v == mButtonStartDate) {
            isStartDate = true;
            ChooseDateDialog();
        } else if (v == mButtonEndDate) {
            isStartDate = false;
            ChooseDateDialog();
        } else if (v == mButtonSubmitDate) {
            try {
                if (endDate.after(currentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else if (startDate.after(endDate)) {
                    Utils.showToast(mContext, "Start Date should be less than or equal to End Date");
                } else {
                    mCustomDateLayout.startAnimation(bottomUp);
                    mCustomDateLayout.setVisibility(View.GONE);
                    mImageViewShowDuration.startAnimation(fadeOut);
                    mImageViewShowDuration.setVisibility(View.GONE);
                    mRelativeLayoutDuration.startAnimation(bottomUp);
                    mRelativeLayoutDuration.setVisibility(View.VISIBLE);
                    SELECTION = 3;
                    mQuery = BuildQuery(SELECTION);
                    FetchSaudaData(1);
                    mTextViewStartDate.setText("");
                    mTextViewEndDate.setText("");
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Please choose the Dates again.");
            }
        }
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
