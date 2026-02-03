package com.forcepower.acedns.activity;

import static android.view.View.GONE;

import static com.forcepower.acedns.R.id.autoCompleteTextView1;
import static com.forcepower.acedns.R.id.btn_ok;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;
import android.util.SparseBooleanArray;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class LuxSiteDisplayVisitActivity extends AceDnsParentActivity {

    ConnectionDetector cd;
    boolean isSubmit = false;
    ProgressDialog loader;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    private Handler mHandler;
    private ProgressDialog mProgressDialog;

    Button btnBack,btnNotOk,buttonOk,button_site_name,buttonSubmit;

    TextView tvSiteName,textViewissue;

    ArrayList<String> accessMonthList = null;

    LinearLayout llissue,ll_visit,ll_take_pic,ok_notok_cont_layout,ll_rectified;

    private int TAKE_PHOTO_CODE_FUEL = 1;
    private int TAKE_PHOTO_CODE = 0;
    private int mWidth = 175;
    private int mHeight = 150;
    Uri imageUri;
    String imageName;
    int attachmentType, maxImageLimit=5,numberOfImageAdded = 0,numberOfImageAddedDa = 0;
    ArrayList<String> AttachmentNames = new ArrayList<>();
    ImageView attachmentImageView2, attachmentImageView3,attachmentImageView4,attachmentImageView5;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lux_site_display_visit);

        btnBack = findViewById(R.id.back);
        tvSiteName = findViewById(R.id.textViewSitename);
        btnNotOk = findViewById(R.id.buttonNotOk);
        buttonOk = findViewById(R.id.buttonOk);
        llissue = findViewById(R.id.llissue);
        ll_visit = findViewById(R.id.ll_visit);
        ok_notok_cont_layout = findViewById(R.id.ok_notok_cont_layout);
        ll_take_pic  = findViewById(R.id.ll_take_pic);
        textViewissue = findViewById(R.id.textViewissue);
        button_site_name = findViewById(R.id.button_site_name);
        ll_rectified = findViewById(R.id.ll_rectified);
        buttonSubmit = findViewById(R.id.buttonSubmit);

        mContext = LuxSiteDisplayVisitActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        cd = new ConnectionDetector(mContext);

        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        buttonSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        if (cd.isConnectingToInternet()) {
              //  loader.show();
        }
        //custData();
        //ShowAccessMonthDialog("Select Site");

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    mProgressDialog.cancel();
                }
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    mProgressDialog.cancel();
                    //InitializeView();
                }
                if (aResponse.equalsIgnoreCase("JobDoneM")) {
                    loader.cancel();
                    mAceDnsDatabase.GetMarketFeedbackDetailsAll();

                }
            }
        };

        try {
            TextView txtVersion = (TextView) findViewById(R.id.txt_version);
            txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                    + Utils.getDBVersion(mContext));
        }catch (Exception e){

        }

        btnNotOk.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ShowNotOhReasonnDialog("Select Issues");
                ll_rectified.setVisibility(View.VISIBLE);
                ll_take_pic.setVisibility(View.VISIBLE);
            }
        });

        button_site_name.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ShowAccessMonthDialog("Select Site");
            }
        });
        buttonOk.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ll_take_pic.setVisibility(View.VISIBLE);
                ll_rectified.setVisibility(GONE);
            }
        });


    }


    public void custData(){

        mProgressDialog = new ProgressDialog(LuxSiteDisplayVisitActivity.this);
        mProgressDialog.setMessage("Preparing Data. Please wait..");
        mProgressDialog.show();

        new Thread() {
            public void run() {
                //customerList11 = JsonsReceiver.starsaathi_ledger_customer_list(mContext);
                chooseCustomerDialog();
                //getCustData();
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();


    }


    public void chooseCustomerDialog() {
        ArrayList<CustomerDetails> customerList = mAceDnsDatabase
                .getCustomerListWithOS();
        if (customerList != null && customerList.size() > 0) {
            final CustomerAdapter adapterCust = new CustomerAdapter(
                    LuxSiteDisplayVisitActivity.this, R.layout.multiple_cust_child,
                    customerList);
            final Dialog custDialog = new Dialog(LuxSiteDisplayVisitActivity.this,
                    R.style.PauseDialog);
            custDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            custDialog.setContentView(R.layout.select_multiple_from_list);
            custDialog.setCancelable(false);
            TextView title = (TextView) custDialog.findViewById(R.id.title);
            title.setText("Please select Site");
            final ListView dialogList = (ListView) custDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            dialogList.setAdapter(adapterCust);
            RelativeLayout chkAllLayout = (RelativeLayout) custDialog
                    .findViewById(R.id.select_all_layout);
            chkAllLayout.setVisibility(View.VISIBLE);
            final CheckBox chkSelectAll = (CheckBox) custDialog
                    .findViewById(R.id.chk_all);
            chkSelectAll.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (chkSelectAll.isChecked()) {
                        for (int i = 0; i <= dialogList.getCount(); i++) {
                            dialogList.setItemChecked(i, true);
                        }
                    } else {
                        for (int i = 0; i <= dialogList.getCount(); i++) {
                            dialogList.setItemChecked(i, false);
                        }
                    }
                }
            });
            Button submit = (Button) custDialog.findViewById(R.id.button1);
            submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    String selectedCodes = "";
                    String selectedNames = "";

                    if (chkSelectAll.isChecked()) {
                        for (int i = 0; i < adapterCust.getCount(); i++) {
                            CustomerDetails detailsObj = adapterCust.getItem(i);
                            String selectedName = detailsObj.getCustomerName();
                            String selectedCode = detailsObj.getCustomerCode();
                            selectedCodes = selectedCodes + "'" + selectedCode
                                    + "',";
                            selectedNames = selectedNames + selectedName + ",";
                        }
                        selectedCodes = selectedCodes.substring(0,
                                selectedCodes.length() - 1);
                        selectedNames = selectedNames.substring(0,
                                selectedNames.length() - 1);
                        //prepareOutstandingData(selectedCodes);

                        custDialog.cancel();
                    } else {
                        final SparseBooleanArray checkedItems = dialogList
                                .getCheckedItemPositions();
                        int checkedItemsCount = checkedItems.size();
                        if (checkedItemsCount > 0) {
                            for (int i = 0; i < checkedItemsCount; ++i) {
                                int position = checkedItems.keyAt(i);
                                if (checkedItems.valueAt(i)) {
                                    CustomerDetails detailsObj = adapterCust
                                            .getItem(position);
                                    String selectedName = detailsObj
                                            .getCustomerName();
                                    String selectedCode = detailsObj
                                            .getCustomerCode();
                                    selectedCodes = selectedCodes + "'"
                                            + selectedCode + "',";
                                    selectedNames = selectedNames
                                            + selectedName + ",";
                                }
                            }
                            selectedCodes = selectedCodes.substring(0,
                                    selectedCodes.length() - 1);
                            selectedNames = selectedNames.substring(0,
                                    selectedNames.length() - 1);

                            //prepareOutstandingData(selectedCodes);
                            custDialog.cancel();
                        } else {
                            Utils.showToast(LuxSiteDisplayVisitActivity.this,
                                    "Please select an option");
                        }
                    }

                }
            });
            custDialog.show();
        } else {
            Utils.showToast(LuxSiteDisplayVisitActivity.this, "No data Found");
        }
    }


    public void ShowAccessMonthDialog(String parentText) {
        String year = new SimpleDateFormat("yyyy").format(Calendar
                .getInstance().getTime());
        int month = new Date().getMonth();
        accessMonthList = new ArrayList<String>();

        accessMonthList.add("Site1");
        accessMonthList.add("Site2");
        accessMonthList.add("Site3");
        accessMonthList.add("Site4");

        final Dialog routePlanAccessDialog = new Dialog(LuxSiteDisplayVisitActivity.this,
                R.style.PauseDialog);
        routePlanAccessDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanAccessDialog.setContentView(R.layout.select_from_list);
        routePlanAccessDialog.setCancelable(false);
        TextView title = (TextView) routePlanAccessDialog
                .findViewById(R.id.title);
        title.setText(parentText);
        ListView dialogList = (ListView) routePlanAccessDialog
                .findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(
                LuxSiteDisplayVisitActivity.this, R.layout.routeplan_access_dialog_child,
                accessMonthList);
        dialogList.setAdapter(adapter1);
        Button cancel = (Button) routePlanAccessDialog
                .findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanAccessDialog.cancel();
            }
        });

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int arg2, long arg3) {

                ll_visit.setVisibility(View.VISIBLE);
                ok_notok_cont_layout.setVisibility(View.VISIBLE);
                button_site_name.setText("" + accessMonthList.get(arg2).toString());
                routePlanAccessDialog.cancel();
            }
        });


        routePlanAccessDialog.show();
    }


    public void ShowNotOhReasonnDialog(String parentText) {
        String year = new SimpleDateFormat("yyyy").format(Calendar
                .getInstance().getTime());
        int month = new Date().getMonth();
        accessMonthList = new ArrayList<String>();

        accessMonthList.add("A");
        accessMonthList.add("B");
        accessMonthList.add("C");
        accessMonthList.add("D");

        final Dialog routePlanAccessDialog = new Dialog(LuxSiteDisplayVisitActivity.this,
                R.style.PauseDialog);
        routePlanAccessDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanAccessDialog.setContentView(R.layout.select_from_list);
        routePlanAccessDialog.setCancelable(false);
        TextView title = (TextView) routePlanAccessDialog
                .findViewById(R.id.title);
        title.setText(parentText);
        ListView dialogList = (ListView) routePlanAccessDialog
                .findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(
                LuxSiteDisplayVisitActivity.this, R.layout.site_visit_issue_dialog_child,
                accessMonthList);
        dialogList.setAdapter(adapter1);
        Button cancel = (Button) routePlanAccessDialog
                .findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanAccessDialog.cancel();
            }
        });

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int arg2, long arg3) {
                llissue.setVisibility(View.VISIBLE);
                ok_notok_cont_layout.setVisibility(View.VISIBLE);
                ll_take_pic.setVisibility(View.VISIBLE);
                textViewissue.setText("Site Issue : " + accessMonthList.get(arg2).toString());
                routePlanAccessDialog.cancel();
            }
        });


        routePlanAccessDialog.show();
    }


    public void addAttachment(View v)//Other_Attachment
    {
        attachmentType=1;
        if (numberOfImageAdded < maxImageLimit)
        {
            launchCameraToTakeImage(TAKE_PHOTO_CODE);
        } else {
            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
        }
    }

    private void launchCameraToTakeImage(int CODE)
    {
        try
        {
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".png";

            File file = new File(Utils.getAppStoragePath(mContext)+imageName);
            imageUri = Uri.fromFile(file);

            /*Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            intent.putExtra(MediaStore.EXTRA_OUTPUT, imageUri);
            startActivityForResult(intent, CODE);*/
            Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                //takePictureIntent.putExtra(MediaStore.EXTRA_OUTPUT, imageUri);
                startActivityForResult(takePictureIntent, CODE);
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

//        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
//        startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK)
        {
            try {
                Bundle extras = data.getExtras();
                Bitmap fuelPicBitmap = null;
                fuelPicBitmap = (Bitmap) extras.get("data");
                String imagePath = Utils.getAppStoragePath(mContext) + imageName;
                ImageView attachmentImageView1 = (ImageView) findViewById(R.id.attachmentImageView1);
                storeImageInLocalStorageShowOnImageViewCustomize(fuelPicBitmap,true,imagePath,attachmentImageView1);
                //mAceDnsTransactionDatabase.insertToSupportingAttachTable(FuelBillAttachmentName, "add_fuel_bill");

                Bitmap imageBitmap = MediaStore.Images.Media.getBitmap(getContentResolver(), imageUri);
                imageBitmap = Utils.getResizedBitmap(imageBitmap, 75, 50);
                AttachmentNames.add( imageName);
                if (numberOfImageAdded == 0)
                {
                    attachmentImageView1 = (ImageView) findViewById(R.id.attachmentImageView1);
                    attachmentImageView1.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    if(numberOfImageAdded<maxImageLimit)
                        showAddAnotherImageDialog();
                }
                else if (numberOfImageAdded == 1)
                {
                    attachmentImageView2 = (ImageView) findViewById(R.id.attachmentImageView2);
                    attachmentImageView2.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    if(numberOfImageAdded<maxImageLimit)
                        showAddAnotherImageDialog();
                }
                else if (numberOfImageAdded == 2)
                {
                    attachmentImageView3 = (ImageView) findViewById(R.id.attachmentImageView3);
                    attachmentImageView3.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                }
                else if (numberOfImageAdded == 3)
                {
                    attachmentImageView4 = (ImageView) findViewById(R.id.attachmentImageView4);
                    attachmentImageView4.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                }
                else if (numberOfImageAdded == 4)
                {
                    attachmentImageView5 = (ImageView) findViewById(R.id.attachmentImageView5);
                    attachmentImageView5.setImageBitmap(imageBitmap);
                    numberOfImageAdded++;
                    Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
                }
            } catch (Exception e) {
                e.printStackTrace();
            }

        }

    }

    private void showAddAnotherImageDialog() {
        Constants.isSurveyImageTake = true;
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext);
        AlertDG.setTitle("Please Note");
        AlertDG.setMessage("Do you want to take another pic?");
        AlertDG.setPositiveButton("Yes", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
                launchCameraToTakeImage(TAKE_PHOTO_CODE);
            }
        });
        AlertDG.setNegativeButton("No", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
            }
        });
        AlertDG.setCancelable(true);
        AlertDG.create().show();
    }

    private void storeImageInLocalStorageShowOnImageViewCustomize(Bitmap customerPicBitmap,Boolean ShowImageOnUi,String imagePath,ImageView iv)
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
            iv.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 200, 200));
        }
    }

}