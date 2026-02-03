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
import android.widget.ArrayAdapter;
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
import com.forcepower.acedns.adapter.SurveyReportAdapter;
import com.forcepower.acedns.bean.SurveyReport;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class SurveyReportActivity extends FragmentActivity implements OnClickListener {

    public static Button mButtonBack = null;
    public static Button mButtonStartDate = null;
    public static Button mButtonEndDate = null;
    public static Button mButtonSubmitDate = null;

    public static ListView mListViewSurveyReport = null;

    public static ImageView mImageViewHeaderLogo = null;
    public static ImageView mImageViewShowDuration = null;
    public static ImageView mImageViewHideDuration = null;

    public static TextView mTextViewStartDate = null;
    public static TextView mTextViewEndDate = null;
    public static TextView mTextViewTotal = null;
    public static TextView mTextViewHeader = null;

    public static FrameLayout mFrameLayoutToday = null;
    public static FrameLayout mFrameLayoutMTD = null;
    public static FrameLayout mFrameLayoutCustom = null;

    public static RelativeLayout mRelativeLayoutDuration = null;
    public static LinearLayout mCustomDateLayout = null;

    public AceDnsDatabase mAceDnsDatabase;

    public Context mContext;

    public ProgressDialog mProgressDialog;

    public Handler mReportHandler;
    public boolean isStartDate = false;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd, mDFormatBackEnd2;
    SurveyReportAdapter SRAdapter;
    String[] mTypeList;
    ArrayList<SurveyReport> mSurveyReportList;
    private String mReportType = "";
    /*
     * This variable is used to select the report duration
     * Example SELECTION=0 for today
     */
    private int SELECTION = 0;
    private String mTime = "";
    private String mCurrentMonth = "";
    private String mCurrentYear = "";
    private String mStartDate = "";
    private String mStartDate2 = "";
    private String mEndDate = "";
    private String mEndDate2 = "";
    private String mToday = "";
    private String mToday2 = "";
    private String mQuery = "";
    private String mQuery2 = "";
    private String mQuery3 = "";
    private String mSurveyType = "";

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_survey_report);
        RegisterActivities.registerActivity(this);

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);
        mReportType = getIntent().getStringExtra("REPORT_TYPE");
        InitializeView();

        mSurveyReportList = new ArrayList<SurveyReport>();


        mContext = SurveyReportActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyy-MM-dd");
        mDFormatBackEnd2 = new SimpleDateFormat("yyyyMMdd");
        mToday = mDFormatBackEnd.format(currentDate);
        mToday2 = mDFormatBackEnd2.format(currentDate);

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
                    mStartDate2 = mDFormatBackEnd2.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    mEndDate = mDFormatBackEnd.format(date);
                    mEndDate2 = mDFormatBackEnd2.format(date);
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

        mReportHandler = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                SurveyReportActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (job) {
                            case 1:
                                if (mSurveyReportList.size() > 0) {
                                    ShowSurveyMenuList();
                                    CalCulateTotalSurvey();
                                } else {
                                    ShowSurveyMenuList();
                                    CalCulateTotalSurvey();
                                    switch (SELECTION) {
                                        case 1:
                                            Toast.makeText(mContext, "No record for today", Toast.LENGTH_LONG).show();
                                            break;
                                        case 2:
                                            Toast.makeText(mContext, "No record found in this month", Toast.LENGTH_LONG).show();
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
                                //ShowSaudaBookedProductGroupList();
                                break;
                            case 3:
                                //ShowSaudaBookedProductList();
                                break;
                            case 4:
                                // showMasterListDialog();
                                if (mTypeList.length > 0) {
                                    if (mTypeList.length == 1) {
                                        mSurveyType = mTypeList[0];
                                        SELECTION = 1;
                                        mQuery = BuildQuery(SELECTION);
                                        FetchSurveyData(1);
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


        if (Constants.surveyFormDetailsObj.getSurveyType().equalsIgnoreCase("yes")) {
            if (mReportType.equalsIgnoreCase("DCA")) {
                SELECTION = 1;
                mQuery = BuildQuery(SELECTION);
                FetchSurveyData(1);
            } else {
                FetchSurveyData(4);
            }
        } else {
            SELECTION = 1;
            mQuery = BuildQuery(SELECTION);
            FetchSurveyData(1);
        }
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
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int pos,
                                    long arg3) {
                mSurveyType = adapter.getItem(pos).toLowerCase();
                mDialogDepotName.cancel();
                SELECTION = 1;
                mQuery = BuildQuery(SELECTION);
                FetchSurveyData(1);
            }
        });
        mDialogDepotName.show();

    }

    public void GETCurrentMonthYear(String date) {
        mCurrentYear = date.substring(0, 4);
        mCurrentMonth = date.substring(4, 6);
    }

    public void InitializeView() {
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imageViewLogo);

        mImageViewShowDuration = (ImageView) findViewById(R.id.image_clk);
        mImageViewHideDuration = (ImageView) findViewById(R.id.image_go);

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);

        mRelativeLayoutDuration = (RelativeLayout) findViewById(R.id.duration_layout);
        mCustomDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);

        mListViewSurveyReport = (ListView) findViewById(R.id.listsurveyActivity);


        mButtonBack = (Button) findViewById(R.id.back);

        mFrameLayoutToday = (FrameLayout) findViewById(R.id.btn_today);
        mFrameLayoutMTD = (FrameLayout) findViewById(R.id.btn_mtd);
        mFrameLayoutCustom = (FrameLayout) findViewById(R.id.btn_custom);

        mFrameLayoutToday.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);

        mTextViewStartDate = (TextView) findViewById(R.id.txt_start_date);
        mTextViewEndDate = (TextView) findViewById(R.id.txt_end_date);
        mTextViewTotal = (TextView) findViewById(R.id.textViewTotalSurvey);

        mTextViewHeader = (TextView) findViewById(R.id.textViewAllocation);

        if (mReportType.equalsIgnoreCase("DCA")) {
            mTextViewHeader.setText("Total Audit");
        } else {
            mTextViewHeader.setText("Total Survey");
        }

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");
        mTextViewTotal.setText("");

        mButtonStartDate = (Button) findViewById(R.id.btn_start_date);
        mButtonEndDate = (Button) findViewById(R.id.btn_end_date);
        mButtonSubmitDate = (Button) findViewById(R.id.btn_date_done);

        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);
    }

    public String BuildQuery(int select) {
        String condition = "";
        switch (select) {
            case 1:
                condition = "substr(LO.date,1,10)='" + mToday + "' ";
                mQuery2 = "substr(survey_id,8,8)='" + mToday2 + "' ";
                mQuery3 = "substr(DCA_trans_id,-14,8)='" + mToday2 + "' ";
                break;
            case 2:
                condition = "substr(LO.date,1,4)='" + mCurrentYear + "' AND substr(LO.date,6,2)='" + mCurrentMonth + "' ";
                mQuery2 = "substr(survey_id,8,6)='" + mCurrentYear + mCurrentMonth + "' ";
                mQuery3 = "substr(DCA_trans_id,-14,6)='" + mCurrentYear + mCurrentMonth + "' ";
                break;
            case 3:
                condition = "substr(LO.date,1,10) BETWEEN '" + mStartDate + "' AND '" + mEndDate + "' ";
                mQuery2 = "substr(survey_id,8,8) BETWEEN '" + mStartDate2 + "' AND '" + mEndDate2 + "' ";
                mQuery3 = "substr(DCA_trans_id,-14,8) BETWEEN '" + mStartDate2 + "' AND '" + mEndDate2 + "' ";
                break;
        }
        return condition;
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

    public void ShowSurveyMenuList() {
        SRAdapter = new SurveyReportAdapter(SurveyReportActivity.this, R.layout.activity_survey_menu_child, mSurveyReportList);
        SRAdapter.notifyDataSetChanged();
        mListViewSurveyReport.setAdapter(SRAdapter);
        mListViewSurveyReport.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {

            }
        });

    }

    public void CalCulateTotalSurvey() {
        int total = 0;
        for (int count = 0; count < mSurveyReportList.size(); count++) {
            total += Integer.parseInt(mSurveyReportList.get(count).getTotal());
        }
        mTextViewTotal.setText(String.valueOf(total));
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
            FetchSurveyData(1);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            mQuery = BuildQuery(SELECTION);
            FetchSurveyData(1);
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
                    FetchSurveyData(1);
                    mTextViewStartDate.setText("");
                    mTextViewEndDate.setText("");
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Please choose the Dates again.");
            }
        }
    }

    public void FetchSurveyData(final int whattodo) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Fetching Data.\nPlease wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:

                        if (mReportType.equalsIgnoreCase("DCA")) {

                            mSurveyReportList = mAceDnsDatabase.GetDCAReportMenuDetails(mQuery3);
						/*if(mSurveyReportList.size()>0){
							for(int count=0;count<mSurveyReportList.size();count++){
								String id=mSurveyReportList.get(count).getMenuId();
								String total=mAceDnsDatabase.GetMenuWiseTotalSurvey(mQuery2,id);
								mSurveyReportList.get(count).setTotal(total);
							}
						}*/

                        } else {
                            mSurveyReportList = mAceDnsDatabase.GetSurveyReportMenuDetails(mQuery, mSurveyType);
                            if (mSurveyReportList.size() > 0) {
                                for (int count = 0; count < mSurveyReportList.size(); count++) {
                                    String id = mSurveyReportList.get(count).getMenuId();
                                    String total = mAceDnsDatabase.GetMenuWiseTotalSurvey(mQuery2, id);
                                    mSurveyReportList.get(count).setTotal(total);
                                }
                            }
                        }


                        break;
                    case 2:
                        //mSBPGList=mAceDnsDatabaseHelper.GetSauadaBookingProductGroupDetails(mCustomerCode,mQuery);
                        break;
                    case 3:
                        //mSBPList=mAceDnsDatabaseHelper.GetSauadaBookingProductDetails(mCustomerCode,mProductGroupCode,mQuery);
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
                Message msg = mReportHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("JOBDONE", whattodo);
                msg.setData(b);
                mReportHandler.sendMessage(msg);
            }
        }.start();
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
