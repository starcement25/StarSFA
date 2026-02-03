package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.os.Handler;
import android.provider.MediaStore;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.TechnicalMeetApprovalStatusConfirmAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitTMAttendanceTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourAttachmentExportTask;
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

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.dateString;

public class TechnicalMeetApprovalStatusConfirmationActivity extends AceDnsParentActivity
{
    ProgressDialog loader;
    Handler mHandler;
    EditText etReasonToCloseOthers;
    String chosenMeetDate="";
    Context mContext;
    public ImageView customerPicImageView ;
    public ImageView mImageViewHeaderLogo = null;
    public Button btn_Submi = null;
    public Button btn_photo = null;
    public Button btn_remarks = null;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    public  TextView createdByTv = null;
    public  TextView meetWithTv = null;
    public static String remarks="",photoName="",exFor="";

    public AceDnsDatabase mAceDnsDatabase;
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<BranchMasterDetails> mBranchDetailsList;
    public static ArrayList<commonDatabaseHelper> mansonNamePhoneList;
    ListView meetApprovalListVIew;
    TextView textViewTitle ;;
    public static ArrayList<commonDatabaseHelper> ChosenOrderList;
    TechnicalMeetApprovalStatusConfirmAdapter  TechnicalMeetApprovalAdapterObject;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_technical_meet_approval_status_confirm);
        RegisterActivities.registerActivity(this);
        mContext = TechnicalMeetApprovalStatusConfirmationActivity.this;
        photoName="";
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        customerPicImageView = findViewById(R.id.customerPicImageView);
        createdByTv =  findViewById(R.id.createdByTv);
        meetWithTv =  findViewById(R.id.meetWithTv);
        meetApprovalListVIew = findViewById(R.id.meetApprovalListVIew);
        textViewTitle = findViewById(R.id.textViewTitle);
        textViewTitle.setText("Technical Meet Status");
        createdByTv.setText("Created by : "+TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList.get(TechnicalMeetApprovalStatusActivity.posOfChosenMeeting).getItem6());
        meetWithTv.setText("Meet With : "+TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList.get(TechnicalMeetApprovalStatusActivity.posOfChosenMeeting).getItem4());
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mansonNamePhoneList=new ArrayList<>();
        String mansonListHashSeparated=TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList.get(TechnicalMeetApprovalStatusActivity.posOfChosenMeeting).getItem7();
        String[] splittedMansonList=mansonListHashSeparated.split("#");
        for(int i=0;i<splittedMansonList.length;i++)
        {
            commonDatabaseHelper listItem=new commonDatabaseHelper();
            String[] splitedInnerList = splittedMansonList[i].split(";");
            listItem.setItem0(splitedInnerList[0]);//name
            listItem.setItem1(splitedInnerList[1]);//phone
            listItem.setItem2("Present");//status
            mansonNamePhoneList.add(listItem);
        }
        TechnicalMeetApprovalAdapterObject=new TechnicalMeetApprovalStatusConfirmAdapter(mContext,R.layout.list_item_technical_meet_approval);
        meetApprovalListVIew.setAdapter(TechnicalMeetApprovalAdapterObject);
        btn_Submi = findViewById(R.id.btn_Submi);
        btn_Submi.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v)
            {
                saveTODb();
            }
        });
        btn_photo = findViewById(R.id.btn_photo);
        btn_photo.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v)
            {
                Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                if (takePictureIntent.resolveActivity(getPackageManager()) != null)
                {
                    startActivityForResult(takePictureIntent, 1);
                }
            }
        });
        btn_remarks = findViewById(R.id.btn_remarks);
        btn_remarks.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v)
            {
                showRemarksDialog();
            }
        });
    }
    public void saveTODb()
    {
        if(!HTTPUtils.isConnectionPossible(mContext) )
        {
            Utils.showToast(mContext,"You must have an active internet connection to submit.");
        }
        else
        {
            if( !photoName.matches(""))
            {
                String meetId=TechnicalMeetApprovalStatusActivity.UnapprovedTechnicalMeetsList.get(TechnicalMeetApprovalStatusActivity.posOfChosenMeeting).getItem0();
                String mansonAttendance="";
                for(int x=0;x<mansonNamePhoneList.size();x++)
                {
                    if(mansonAttendance.matches(""))
                    {
                        mansonAttendance=mansonNamePhoneList.get(x).getItem0()+";"+mansonNamePhoneList.get(x).getItem1()+";"+mansonNamePhoneList.get(x).getItem2();
                    }
                    else
                    {
                        mansonAttendance=mansonAttendance+"#"+mansonNamePhoneList.get(x).getItem0()+";"+mansonNamePhoneList.get(x).getItem1()+";"+mansonNamePhoneList.get(x).getItem2();
                    }

                }
                new GPSTracker(mContext);
                String timeStamp = "";
                timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                try
                {
                    mAceDnsTransactionDatabase.updateTechnicalMeetAttendance(meetId,mansonAttendance,Utils.changeDateFormat("yyyyMMddHHmmss","yyyy-MM-dd HH:mm:ss",timeStamp),photoName,remarks);
                    new TRANS_SubmitTMAttendanceTask(mContext, true).execute();
                    new TRANS_TourAttachmentExportTask(mContext, "add_tm_pic", "", false,false).execute();
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }
            }
            else
            {
                Utils.showToast(mContext,"You must add a photo before submitting.");
            }

        }

    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == 1) {
            if (resultCode == RESULT_OK) {
                if (data != null) {
                    try {
                        String timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        Bundle extras = data.getExtras();

                        Bitmap customerPicBitmap = (Bitmap) extras.get("data");
//                              customerPicBitmap = Utils.getResizedBitmap(imageBitmap, 100, 100);
                        photoName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                        String imagePath = Utils.getAppStoragePath(mContext) + photoName;
                        storeImageInLocalStorageShowOnImageView(customerPicBitmap, true, imagePath);
                        mAceDnsTransactionDatabase.insertToSupportingAttachTable(photoName, "add_tm_pic");

                    } catch (Exception e) {
                        Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                    }

                } else {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                }


            } else {
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
            }

        }
    }
    private void showRemarksDialog()
    {

        final Dialog checkoutDialog = new Dialog(mContext);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.remarks_dialog);
        checkoutDialog.setCancelable(false);
        TextView title = (TextView) checkoutDialog.findViewById(R.id.title);
        Button submit = (Button) checkoutDialog.findViewById(R.id.btn_submit);
        title.setText("Remarks");
        final ImageView back =  checkoutDialog.findViewById(R.id.back);
        back.setOnClickListener((View.OnClickListener) v -> {
            checkoutDialog.cancel();

        });
        submit.setOnClickListener((View.OnClickListener) v -> {

             EditText remarks_box =  checkoutDialog.findViewById(R.id.ed_input);
            remarks = remarks_box.getText().toString().trim();
            checkoutDialog.cancel();

        });
        checkoutDialog.show();

    }
    private void storeImageInLocalStorageShowOnImageView(Bitmap customerPicBitmap,Boolean ShowImageOnUi,String imagePath)
            throws FileNotFoundException {
        File outputFile = null;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out = null;
        out = new FileOutputStream(outputFile);
        customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
        if(ShowImageOnUi)
        {
            customerPicImageView.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 100, 100));
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

}
