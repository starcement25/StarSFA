package com.forcepower.acedns.activity;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
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

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ReportAdapter;
import com.forcepower.acedns.adapter.ReportDetailsAdapter;
import com.forcepower.acedns.bean.ReportData;
import com.forcepower.acedns.bean.ReportDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class ActivityCollectionReport extends FragmentActivity implements OnClickListener {

    public static Button mButtonBack = null;
    public static Button mButtonStartDate = null;
    public static Button mButtonEndDate = null;
    public static Button mButtonSubmitDate = null;

    public static ImageView mImageViewHeaderLogo = null;
    public static ImageView mImageViewShowDuration = null;
    public static ImageView mImageViewHideDuration = null;


    public static TextView mTextViewStartDate = null;
    public static TextView mTextViewEndDate = null;
    public static ListView mListViewList = null;


    public static FrameLayout mFrameLayoutToday = null;
    public static FrameLayout mFrameLayoutMTD = null;
    public static FrameLayout mFrameLayoutCustom = null;

    public static RelativeLayout mRelativeLayoutDuration = null;

    public static LinearLayout mCustomDateLayout = null;


    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    public Context mContext;
    public ProgressDialog mProgressDialog;
    public Handler mReportHandler;
    public int SELECTION = 0;
    Date startDate, endDate;
    Date currentDate = new Date();
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    ArrayList<ReportData> mReportDataList;
    ArrayList<ReportData> mReportDataSubList;
    ArrayList<ReportDetails> mReportDetailsList;
    ReportAdapter mReportAdapter;
    private boolean isStartDate = false;
    private String mStartDate = "";
    private String mEndDate = "";
    private String mCustomerCode = "";
    private String mSearchKey = "";
    private String mCustomerName = "";
    private String mRemarks = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_collection_report);
        RegisterActivities.registerActivity(this);


        SELECTION = getIntent().getIntExtra("SELECTION", 0);
        mStartDate = getIntent().getStringExtra("STARTDATE");
        mEndDate = getIntent().getStringExtra("ENDDATE");

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mDFormatFrontEnd = new SimpleDateFormat("dd-MM-yyyy");
        mDFormatBackEnd = new SimpleDateFormat("yyyyMMdd");

        mContext = ActivityCollectionReport.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        InitializeView();

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
                    //mFStartDate=mDFormatFrontEnd.format(date);
                    mStartDate = mDFormatBackEnd.format(date);
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                    //mFEndDate=mDFormatFrontEnd.format(date);
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

        mListViewList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                if (mReportDataList != null) {
                    if (mReportDataList.get(position).getTransId().substring(0, 2).equalsIgnoreCase("NC")) {
                        String remarks = mAceDnsTransactionDatabase.getReportRemarks("P", mReportDataList.get(position).getTransId());
                        AlertDialog.Builder alert = new AlertDialog.Builder(ActivityCollectionReport.this);
                        alert.setMessage("No Payment was received. \nRemarks : " + remarks).setCancelable(false);
                        alert.setPositiveButton("    OK    ", new DialogInterface.OnClickListener() {
                            @Override
                            public void onClick(DialogInterface dialog, int which) {
                                dialog.dismiss();
                            }
                        });
                        alert.show();
                    } else {
                        mCustomerCode = mReportDataList.get(position).getTransId();
                        mCustomerName = mReportDataList.get(position).getCustomerName();
                        FetchSaudaTransactionLogData(2);
                    }
                }
            }
        });

        mReportHandler = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialog.cancel();
                final int job = msg.getData().getInt("JOBDONE");
                ActivityCollectionReport.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (job) {
                            case 1:
                                if (mReportDataList.size() > 0) {
                                    mReportAdapter = new ReportAdapter(mContext, R.layout.report_list_child, mReportDataList);
                                    mListViewList.setAdapter(mReportAdapter);
                                } else {
                                    mReportAdapter = new ReportAdapter(mContext, R.layout.report_list_child, mReportDataList);
                                    mListViewList.setAdapter(mReportAdapter);
                                    if (SELECTION == 1) {
                                        Utils.showToast(mContext, "No record for today");
                                    }
                                    if (SELECTION == 2) {
                                        Utils.showToast(mContext, "No record for this month");
                                    }
                                    if (SELECTION == 3) {
                                        Utils.showToast(mContext, "No record found");
                                    }
                                }
                                break;
                            case 2:
                                if (mReportDataSubList.size() > 0) {
                                    ShowDetailsDialog();
                                } else {
                                    Utils.showToast(mContext, "No record found");
                                }

                                break;
                            case 3:
                                if (mReportDetailsList.size() > 0) {
                                    ShowReportDetailsDialog();
                                } else {
                                    Utils.showToast(mContext, "No record found");
                                }
                                break;
                            case 4:
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
        ShowCollectionDetailsDialog();
    }


    public void InitializeView() {
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);
        //mImageViewSaudaBooked=(ImageView) findViewById(R.id.imgageViewSaudaDetails);

        mImageViewShowDuration = (ImageView) findViewById(R.id.image_clk);
        mImageViewHideDuration = (ImageView) findViewById(R.id.image_go);

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);

        mRelativeLayoutDuration = (RelativeLayout) findViewById(R.id.duration_layout);
        mCustomDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);

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

        mListViewList = (ListView) findViewById(R.id.listView);

        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }

    }


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
            FetchSaudaTransactionLogData(1);

        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            FetchSaudaTransactionLogData(1);

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
                    mTextViewStartDate.setText("");
                    mTextViewEndDate.setText("");
                    FetchSaudaTransactionLogData(1);
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Please choose the Dates again.");
            }
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


    public void FetchSaudaTransactionLogData(final int whattodo) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            public void run() {
                switch (whattodo) {
                    case 1:
                        if (SELECTION == 1) {
                            String timeStamp = Constants.dateString;
                            mReportDataList = mAceDnsTransactionDatabase.getReportListGrpWise("P", timeStamp);
                        }
                        if (SELECTION == 2) {
                            String timeStamp = Constants.dateString.substring(0, 6);
                            mReportDataList = mAceDnsTransactionDatabase.getReportListGrpWise("P", timeStamp);
                        }
                        if (SELECTION == 3) {
                            mReportDataList = mAceDnsTransactionDatabase.getCustomReportListGrpWise("P", mStartDate, mEndDate);
                        }
                        break;
                    case 2:
                        if (SELECTION == 1) {
                            String timeStamp = Constants.dateString;
                            mReportDataSubList = mAceDnsTransactionDatabase.getReportList("P", timeStamp, mCustomerCode);
                        }
                        if (SELECTION == 2) {
                            String timeStamp = Constants.dateString.substring(0, 6);
                            mReportDataSubList = mAceDnsTransactionDatabase.getReportList("P", timeStamp, mCustomerCode);
                        }
                        if (SELECTION == 3) {
                            mReportDataSubList = mAceDnsTransactionDatabase.getCustomReportList("P", mStartDate, mEndDate, mCustomerCode);
                        }

                        break;
                    case 3:
                        mRemarks = mAceDnsTransactionDatabase.getReportRemarks("P", mSearchKey);
                        mReportDetailsList = mAceDnsTransactionDatabase.getReportDetailsList("P", mSearchKey);

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


    public void ShowCollectionDetailsDialog() {
        final Dialog collcDetailsDialog = new Dialog(ActivityCollectionReport.this, R.style.PauseDialog);
        collcDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        collcDetailsDialog.setContentView(R.layout.collection_details_dialog);
        collcDetailsDialog.setCancelable(false);
        TextView title = (TextView) collcDetailsDialog.findViewById(R.id.title);
        TextView txtCash = (TextView) collcDetailsDialog.findViewById(R.id.cash);
        TextView txtCurrentCheque = (TextView) collcDetailsDialog.findViewById(R.id.current_chq);
        TextView txtPDC = (TextView) collcDetailsDialog.findViewById(R.id.pdc_chq);
        String[] collectionDetailsArray = new String[3];
        if (SELECTION == 1) {
            title.setText("Collection summary for today");
            collectionDetailsArray = mAceDnsTransactionDatabase.getCollectionDetails("TODAY");
        } else {
            title.setText("Collection summary this month");
            collectionDetailsArray = mAceDnsTransactionDatabase.getCollectionDetails("MTD");
        }
        txtCash.setText("Rs " + new DecimalFormat("0.00").format(Double.parseDouble(collectionDetailsArray[0])));
        txtCurrentCheque.setText("Rs " + new DecimalFormat("0.00").format(Double.parseDouble(collectionDetailsArray[1])));
        txtPDC.setText("Rs " + new DecimalFormat("0.00").format(Double.parseDouble(collectionDetailsArray[2])));
        Button btnNext = (Button) collcDetailsDialog.findViewById(R.id.btn_nxt);
        btnNext.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                collcDetailsDialog.cancel();
                FetchSaudaTransactionLogData(1);
            }
        });
        collcDetailsDialog.show();
    }

    public void ShowDetailsDialog() {
        final Dialog mDetailsDialog = new Dialog(ActivityCollectionReport.this, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.dialog_collection_list);
        mDetailsDialog.setCancelable(true);

        TextView textViewTitleName = (TextView) mDetailsDialog.findViewById(R.id.textViewTitle);
        textViewTitleName.setText(mCustomerName);

        ListView dialogList = (ListView) mDetailsDialog.findViewById(R.id.listView);
        final ReportAdapter orderreportAdapter = new ReportAdapter(mContext, R.layout.report_list_child, mReportDataSubList);
        dialogList.setAdapter(orderreportAdapter);

        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int position, long arg3) {
                mDetailsDialog.cancel();
                mSearchKey = mReportDataSubList.get(position).getTransId();
                FetchSaudaTransactionLogData(3);
            }
        });

        Button buttonBack = (Button) mDetailsDialog.findViewById(R.id.back);
        buttonBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
            }
        });

        mDetailsDialog.show();

    }

    public void ShowReportDetailsDialog() {
        final Dialog mDetailsDialog = new Dialog(ActivityCollectionReport.this, R.style.PauseDialog);
        mDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDetailsDialog.setContentView(R.layout.activity_report_details);
        mDetailsDialog.setCancelable(true);

        TextView textViewTitleName = (TextView) mDetailsDialog.findViewById(R.id.txt_name);
        textViewTitleName.setText(mCustomerName + "\n" + "Remarks- " + mRemarks);


        ListView dialogList = (ListView) mDetailsDialog.findViewById(R.id.list_report);

        final ReportDetailsAdapter orderreportAdapter = new ReportDetailsAdapter(mContext, R.layout.report_list_child, mReportDetailsList);
        dialogList.setAdapter(orderreportAdapter);


        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int position, long arg3) {
                mDetailsDialog.cancel();
            }
        });

        LinearLayout linearLayoutAmount = (LinearLayout) mDetailsDialog.findViewById(R.id.total_order_layout);
        linearLayoutAmount.setVisibility(View.GONE);

        ImageView buttonBack = (ImageView) mDetailsDialog.findViewById(R.id.back);
        buttonBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDetailsDialog.cancel();
            }
        });
        mDetailsDialog.show();
    }

}
