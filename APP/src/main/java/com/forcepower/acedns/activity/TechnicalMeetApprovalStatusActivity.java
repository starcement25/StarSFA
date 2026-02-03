package com.forcepower.acedns.activity;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.drawable.BitmapDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.PopupWindow;
import android.widget.TextView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.TechnicalMeetApprovalStatusAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitTMApprovalTask;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.TreeMap;
import static com.forcepower.acedns.constants.Constants.dateString;

public class TechnicalMeetApprovalStatusActivity extends AceDnsParentActivity
{
    ProgressDialog loader;
    Handler mHandler;
    EditText etReasonToCloseOthers;
    String chosenMeetDate="";
    Context mContext;
    public ImageView mImageViewHeaderLogo = null;
    public Button btn_Submi = null;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    public  TextView tv_meet_date = null;
    public static String remarks="",status="",exFor="";

    public AceDnsDatabase mAceDnsDatabase;
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<BranchMasterDetails> mBranchDetailsList;
    public static ArrayList<commonDatabaseHelper> UnapprovedTechnicalMeetsList;
    public static int posOfChosenMeeting=-1;
    TreeMap<String,ArrayList<commonDatabaseHelper>> OrderListForChosenCustomer;
    ListView meetApprovalListVIew;
    TextView textViewTitle ;;
    public static ArrayList<commonDatabaseHelper> ChosenOrderList;
    TechnicalMeetApprovalStatusAdapter TechnicalMeetApprovalAdapterObject;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_technical_meet_approval_status);
        RegisterActivities.registerActivity(this);
        status="";
        remarks="";
        exFor="";
        mContext = TechnicalMeetApprovalStatusActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        tv_meet_date =  findViewById(R.id.tv_meet_date);
        meetApprovalListVIew = findViewById(R.id.meetApprovalListVIew);
        textViewTitle = findViewById(R.id.textViewTitle);
        textViewTitle.setText("Technical Meet Status");
        UnapprovedTechnicalMeetsList=new ArrayList();
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        btn_Submi = findViewById(R.id.btn_Submi);
        btn_Submi.setVisibility(View.GONE);
        btn_Submi.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v)
            {
                saveTODb();
            }
        });
        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                TechnicalMeetApprovalStatusActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                try
                                {
                                    UnapprovedTechnicalMeetsList=new ArrayList<>();
                                    UnapprovedTechnicalMeetsList= mAceDnsDatabase.geAllApprovedRejectedMeetToday();
                                    if(UnapprovedTechnicalMeetsList.size()>0)
                                    {
                                        TechnicalMeetApprovalAdapterObject=new TechnicalMeetApprovalStatusAdapter(mContext,R.layout.list_item_technical_meet_approval);
                                        meetApprovalListVIew.setAdapter(TechnicalMeetApprovalAdapterObject);
                                        meetApprovalListVIew.setOnItemClickListener(new android.widget.AdapterView.OnItemClickListener() {
                                        @Override
                                        public void onItemClick(AdapterView<?> parent, View view,int position, long id) {
                                            posOfChosenMeeting= position;
                                            if(UnapprovedTechnicalMeetsList.get(posOfChosenMeeting).getItem5().equalsIgnoreCase("Approved"))
                                            {
                                                Intent intent = new Intent(mContext, TechnicalMeetApprovalStatusConfirmationActivity.class);
                                                startActivity(intent);
                                            }
                                            else if(UnapprovedTechnicalMeetsList.get(posOfChosenMeeting).getItem5().equalsIgnoreCase("Rejected"))
                                            {
                                                Utils.showToast(mContext,"Sorry, you can not start a rejected meeting.");
                                            }
                                            else
                                            {
                                                Utils.showToast(mContext,"Sorry, selected meeting is already started.");
                                            }
                                        }
                                    });
                                    }
                                }
                                catch (Exception e)
                                {
                                    e.printStackTrace();
                                }



                                break;
                        }
                    }
                });
            }


        };

        try
        {
            if(!HTTPUtils.isConnectionPossible(mContext) )
            {
                Utils.showToast(mContext,"You must have an active internet connection to use this feature.");
                finish();

            }
            else
            {
                PrepareCustomerData(1);
            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }



    @Override
    public void onResume()
    {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }
    public void gotoback(View v)
    {
        finish();
    }
    public void saveTODb()
    {
        if(!HTTPUtils.isConnectionPossible(mContext) )
        {
            Utils.showToast(mContext,"You must have an active internet connection to submit.");
        }
        else
        {
            if(UnapprovedTechnicalMeetsList.size()>0)
            {
                Boolean isAtleastOneStatusChanged=false;
                for (int ii = 0; ii < UnapprovedTechnicalMeetsList.size(); ii++)
                {
                    commonDatabaseHelper obj = UnapprovedTechnicalMeetsList.get(ii);
                    String approvalStatus = obj.getItem5();
                    if(approvalStatus.equalsIgnoreCase("approved") || approvalStatus.equalsIgnoreCase("Rejected"))
                    {
                        isAtleastOneStatusChanged=true;
                        break;
                    }
                }
                if(isAtleastOneStatusChanged)
                {
                    new GPSTracker(mContext);

                    String timeStamp = "";
                    timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    try
                    {
                        mAceDnsTransactionDatabase.updateTechnicalMeetApprovalData(UnapprovedTechnicalMeetsList,Utils.changeDateFormat("yyyyMMddHHmmss","yyyy-MM-dd HH:mm:ss",timeStamp));
                        new TRANS_SubmitTMApprovalTask(mContext, true).execute();
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
                else
                {
                    Utils.showToast(mContext,"You must approve or reject at least one technical meet before submiting.");
                }

            }
            else
            Utils.showToast(mContext,"No Item to Submit");
        }

    }
    public void chooseMeetDate(View view)
    {
        openMeetDateDialog(tv_meet_date);
    }
    public void openMeetDateDialog(final TextView textView)
    {
        try
        {
            final PopupWindow popup = new PopupWindow(this);
            View layout = getLayoutInflater().inflate(R.layout.list_item_dialog, null);
            popup.setContentView(layout);

            // Set content width and height
            popup.setHeight(WindowManager.LayoutParams.WRAP_CONTENT);
            popup.setWidth(textView.getWidth());

            // Closes the popup window when touch outside of it - when looses focus
            popup.setOutsideTouchable(true);
            popup.setFocusable(true);
            popup.setBackgroundDrawable(new BitmapDrawable());

            popup.showAsDropDown(textView, 0, 0);

             ArrayList<String> spinnerArray = new ArrayList<>();
            spinnerArray = mAceDnsDatabase.getDatesWithTechnicalMeet();

            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_dropdown_item_1line, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            ListView listView = (ListView) layout.findViewById(R.id.lvPopup);
            listView.setAdapter(spinnerArrayAdapter);
            ArrayList<String> finalSpinnerArray = spinnerArray;
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    chosenMeetDate = finalSpinnerArray.get(i);
                    textView.setText(chosenMeetDate);


                    popup.dismiss();
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
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
                        new commonAsyncTaskMaster(mContext, "technical_meet_approval_status");
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

}
