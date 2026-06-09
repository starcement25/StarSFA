package com.forcepower.acedns.activity;

import android.app.AlertDialog;
import android.app.DatePickerDialog;
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
import android.os.StrictMode;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitTravelFoodingLodgingExpenseTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourAttachmentExportTask;
import com.forcepower.acedns.bean.TravelExpCategory;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DateTimeFormatter;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Map;

import static com.forcepower.acedns.activity.non_auth.main_menu.market_overview.SurveyActivityList.mAceDnsTransactionDatabase;
import static com.forcepower.acedns.util.Utils.SaveImageToExternalStorage;

public class TourMultiTravelExpenseLandingActivity extends AceDnsParentActivity {
    ArrayList<TravelExpCategory> mTravelExpCategoryList;
    ArrayList<String> transportModeList;
    AceDnsDatabase mAceDnsDatabase;
    int mYear, mMonth, mDay, mHour, mMinute, numberOfImageAdded = 0,numberOfImageAddedDa = 0,numberOfImageAddedTravel = 0,numberOfImageAddedLodging = 0, attachmentType, maxImageLimit=3;
    Button btn_date, btn_transport_mode,btn_date_to,btn_transport_mode_type;
    EditText et_particulars,et_from,et_to,et_from_kms,et_to_kms, et_local_conv, et_transport_fare,et_transport_fare2,et_transport_fare3, et_fooding_allowance, et_hotel_charges, et_other_expenses, et_remarks;
    ImageView attachmentImageView1, attachmentImageView2, attachmentImageView3;
    Context mContext;
    String date, dateTo,tourType="EX", placeFrom, placeTo,fromKms,toKms, particulars, localConv, travelMode,travelMode2,travelMode3, transportFare,transportFare2,transportFare3, FoodingAllowance, hotelCharges, otherExpenses, remarks;
    ProgressDialog mProgressDialog;
    Uri imageUri;
    String imageName;
    String FuelBillAttachmentName="";
    ArrayList<String> AttachmentNames = new ArrayList<>();
    HashMap<String, Bitmap> Other_Attachment = new HashMap<>();
    HashMap<String, Bitmap> DA_Attachment = new HashMap<>();
    HashMap<String, Bitmap> Lodging_Attachment = new HashMap<>();
    HashMap<String, Bitmap> Trans_Attachment = new HashMap<>();
    Handler mHandler;
    ImageView imgLogo;
    private int TAKE_PHOTO_CODE_FUEL = 1;
    private int TAKE_PHOTO_CODE = 0;
    private int SELECT_PHOTO = 2;

    private int mWidth = 175;
    private int mHeight = 150;
    RadioGroup radio_tour_type;
    LinearLayout lodgingAttachmentLayout, travelAttachmentLayout, daAttachmentLayout, toDateLayout,lodgingExpenseLayout,fromKmsLayout,toKmsLayout,fairLayout,convLayout,fuelBillAttachmentLL,miscLayout;
    LinearLayout kmTravelLayout,dailyAsistanceLayout;
    EditText et_daily_assistance,et_km_travel;
    String km_travel,daily_assistance;
    LinearLayout llFare2,llFare3;
    TextView tv_fare1,tv_fare2,tv_fare3;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_tour_multi_travel_expense_landing);
        mContext = this;
        StrictMode.VmPolicy.Builder builder = new StrictMode.VmPolicy.Builder();
        StrictMode.setVmPolicy(builder.build());
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        fuelBillAttachmentLL = (LinearLayout) findViewById(R.id.fuelBillAttachmentLL);
        miscLayout = (LinearLayout) findViewById(R.id.miscLayout);
        lodgingAttachmentLayout = (LinearLayout) findViewById(R.id.lodgingAttachmentLayout);
        lodgingExpenseLayout = (LinearLayout) findViewById(R.id.lodgingExpenseLayout);
        llFare2 = (LinearLayout) findViewById(R.id.llFare2);
        llFare3 = (LinearLayout) findViewById(R.id.llFare3);
        kmTravelLayout = (LinearLayout) findViewById(R.id.kmTravelLayout);
        fromKmsLayout = (LinearLayout) findViewById(R.id.fromKmsLayout);
        fromKmsLayout = (LinearLayout) findViewById(R.id.fromKmsLayout);
        toKmsLayout = (LinearLayout) findViewById(R.id.toKmsLayout);
        fairLayout = (LinearLayout) findViewById(R.id.fairLayout);
        convLayout = (LinearLayout) findViewById(R.id.convLayout);
        travelAttachmentLayout = (LinearLayout) findViewById(R.id.travelAttachmentLayout);
        daAttachmentLayout = (LinearLayout) findViewById(R.id.daAttachmentLayout);
//        daAttachmentLayout.setVisibility(View.VISIBLE);
        toDateLayout = (LinearLayout) findViewById(R.id.toDateLayout);
        btn_date = (Button) findViewById(R.id.btn_date);
        btn_date_to = (Button) findViewById(R.id.btn_date_to);
        btn_transport_mode = (Button) findViewById(R.id.btn_transport_mode);
        btn_transport_mode_type = (Button) findViewById(R.id.btn_transport_mode_type);
        et_particulars = (EditText) findViewById(R.id.et_particulars);
        et_particulars.setFocusable(false);
        et_from = (EditText) findViewById(R.id.et_from);
        tv_fare1 = (TextView) findViewById(R.id.tv_fare1);
        tv_fare2 = (TextView) findViewById(R.id.tv_fare2);
        tv_fare3 = (TextView) findViewById(R.id.tv_fare3);
        et_to = (EditText) findViewById(R.id.et_to);
        et_from_kms = (EditText) findViewById(R.id.et_from_kms);
        et_to_kms = (EditText) findViewById(R.id.et_to_kms);
        et_local_conv = (EditText) findViewById(R.id.et_local_conv);
        et_daily_assistance = (EditText) findViewById(R.id.et_daily_assistance);
        et_km_travel= (EditText) findViewById(R.id.et_km_travel);;
        if (Constants.menuDetailsObj.getfuel_bill_attachment().equalsIgnoreCase("yes"))
        {
            fuelBillAttachmentLL.setVisibility(View.VISIBLE);
            miscLayout.setVisibility(View.VISIBLE);
        }
        if (Constants.menuDetailsObj.getDoctor_visit().toLowerCase().equalsIgnoreCase("yes"))
        {
            dailyAsistanceLayout.setVisibility(View.VISIBLE);
            kmTravelLayout.setVisibility(View.VISIBLE);
        }
        et_local_conv.addTextChangedListener(new TextWatcher() {

            @Override
            public void afterTextChanged(Editable s) {}

            @Override
            public void beforeTextChanged(CharSequence s, int start,
                                          int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                calculateTotalFare();
            }
        });
        et_transport_fare = (EditText) findViewById(R.id.et_transport_fare);
        et_transport_fare2 = (EditText) findViewById(R.id.et_transport_fare2);
        et_transport_fare3 = (EditText) findViewById(R.id.et_transport_fare3);
        et_transport_fare.addTextChangedListener(new TextWatcher() {

            @Override
            public void afterTextChanged(Editable s) {}

            @Override
            public void beforeTextChanged(CharSequence s, int start,
                                          int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                calculateTotalFare();
            }
        });
        et_transport_fare2.addTextChangedListener(new TextWatcher() {

            @Override
            public void afterTextChanged(Editable s) {}

            @Override
            public void beforeTextChanged(CharSequence s, int start,
                                          int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                calculateTotalFare();
            }
        });
        et_transport_fare3.addTextChangedListener(new TextWatcher() {

            @Override
            public void afterTextChanged(Editable s) {}

            @Override
            public void beforeTextChanged(CharSequence s, int start,
                                          int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                calculateTotalFare();
            }
        });
        et_fooding_allowance = (EditText) findViewById(R.id.et_fooding_allowance);
        et_fooding_allowance.addTextChangedListener(new TextWatcher() {

            @Override
            public void afterTextChanged(Editable s) {}

            @Override
            public void beforeTextChanged(CharSequence s, int start,
                                          int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                calculateTotalFare();
//                if(tourType.matches("EX"))
//                {
//                    daAttachmentLayout.setVisibility(View.VISIBLE);
//                    String daAllowance=et_fooding_allowance.getText().toString();
//                    if(Utils.isNumeric(daAllowance))
//                    {
//                        String fooding_in_station=mAceDnsDatabase.GetTaTdLimitByColumnName("fooding_in_station");
//                        if(Utils.isNumeric(fooding_in_station))
//                        {
//                            if(Double.parseDouble(daAllowance)>Double.parseDouble(fooding_in_station))
//                            {
//                                showMessageDialog("Your D/A limit is " + fooding_in_station);
//                            }
//                        }
//                    }
//                }
//                else
//                {
//                    String daAllowance=et_fooding_allowance.getText().toString();
//                    if(Utils.isNumeric(daAllowance))
//                    {
//                        String ownArrangementLimit=mAceDnsDatabase.GetTaTdLimitByColumnName("own_arrangement_per_day");
//                        if(Utils.isNumeric(ownArrangementLimit))
//                        {
//                            if(Double.parseDouble(daAllowance)>Double.parseDouble(ownArrangementLimit))
//                            {
//                                daAttachmentLayout.setVisibility(View.VISIBLE);
//                            }
//                            else
//                            {
//                                daAttachmentLayout.setVisibility(View.GONE);
//                            }
//                        }
//                    }
//                }
            }
        });
        et_hotel_charges = (EditText) findViewById(R.id.et_hotel_charges);
        et_hotel_charges.addTextChangedListener(new TextWatcher() {

            @Override
            public void afterTextChanged(Editable s) {}

            @Override
            public void beforeTextChanged(CharSequence s, int start,
                                          int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                calculateTotalFare();
//                if(tourType.matches("OS"))
//                {
//                    String daAllowance=et_hotel_charges.getText().toString();
//                    if(Utils.isNumeric(daAllowance))
//                    {
//                        String lodging_per_day=mAceDnsDatabase.GetTaTdLimitByColumnName("lodging_per_day");
//                        if(Utils.isNumeric(lodging_per_day))
//                        {
//                            if(Double.parseDouble(daAllowance)>Double.parseDouble(lodging_per_day))
//                            {
//                                showMessageDialog("Your hotel charges limit is " + lodging_per_day);
//                            }
//                        }
//                    }
//                }
//                else
//                {
////                    String daAllowance=et_hotel_charges.getText().toString();
////                    if(Utils.isNumeric(daAllowance))
////                    {
////                        String ownArrangementLimit=mAceDnsDatabase.GetTaTdLimitByColumnName("own_arrangement_per_day");
////                        if(Utils.isNumeric(ownArrangementLimit))
////                        {
////                            if(Double.parseDouble(daAllowance)>Double.parseDouble(ownArrangementLimit))
////                            {
////                                daAttachmentLayout.setVisibility(View.VISIBLE);
////                            }
////                            else
////                            {
////                                daAttachmentLayout.setVisibility(View.GONE);
////                            }
////                        }
////                    }
//                }
            }
        });

        et_other_expenses = (EditText) findViewById(R.id.et_other_expenses);
        et_other_expenses.addTextChangedListener(new TextWatcher() {

            @Override
            public void afterTextChanged(Editable s) {}

            @Override
            public void beforeTextChanged(CharSequence s, int start,
                                          int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count)
            {
                calculateTotalFare();
            }
        });
        et_remarks = (EditText) findViewById(R.id.et_remarks);

        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        Other_Attachment = new HashMap<>();
        DA_Attachment = new HashMap<>();
        Lodging_Attachment = new HashMap<>();
        Trans_Attachment = new HashMap<>();
        radio_tour_type = (RadioGroup) findViewById(R.id.radio_tour_type);
        radio_tour_type.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                if (checkedId == R.id.radio0)
                {
                    tourType="EX";
                    toDateLayout.setVisibility(View.GONE);
                    lodgingAttachmentLayout.setVisibility(View.GONE);
                    lodgingExpenseLayout.setVisibility(View.GONE);
                    daAttachmentLayout.setVisibility(View.VISIBLE);
                }
                else if (checkedId == R.id.radio1)
                {
                    tourType="OS"; //AMITABHA2715 CHANGED TO OS FROM FOR
                    toDateLayout.setVisibility(View.VISIBLE);
                    lodgingAttachmentLayout.setVisibility(View.VISIBLE);
                    lodgingExpenseLayout.setVisibility(View.VISIBLE);
                }
            }
        });
        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone"))
                {
                    mProgressDialog.cancel();
//                    finish();
                    new TRANS_SubmitTravelFoodingLodgingExpenseTask(mContext, true).execute();
                    if( !FuelBillAttachmentName.matches(""))
                    {
                        new TRANS_TourAttachmentExportTask(mContext, "add_fuel_bill", "", false,false).execute();
                    }
                }
            }
        };
    }

    public void calculateTotalFare() {
        String transportFare = et_transport_fare.getText().toString();
        String transportFare1 = et_transport_fare2.getText().toString();
        String transportFare2 = et_transport_fare3.getText().toString();

        String FoodingAllowance = et_fooding_allowance.getText().toString();
        String hotelCharges = et_hotel_charges.getText().toString();
        String otherExpenses = et_other_expenses.getText().toString();
        String localExpenses = et_local_conv.getText().toString();
        double transportFareD = 0.0,transportFareD2 = 0.0,transportFareD3 = 0.0, FoodingAllowanceD = 0.0, hotelChargesD = 0.0, otherExpensesD = 0.0, localExpensesD = 0.0;
        if (Utils.isNumeric(transportFare)) {
            transportFareD = Double.parseDouble(transportFare);
        }
        if (Utils.isNumeric(transportFare1)) {
            transportFareD2 = Double.parseDouble(transportFare1);
        }
        if (Utils.isNumeric(transportFare2)) {
            transportFareD3 = Double.parseDouble(transportFare2);
        }
        if (Utils.isNumeric(FoodingAllowance)) {
            FoodingAllowanceD = Double.parseDouble(FoodingAllowance);
        }
        if (Utils.isNumeric(hotelCharges)) {
            hotelChargesD = Double.parseDouble(hotelCharges);
        }
        if (Utils.isNumeric(otherExpenses)) {
            otherExpensesD = Double.parseDouble(otherExpenses);
        }
        if (Utils.isNumeric(localExpenses)) {
            localExpensesD = Double.parseDouble(localExpenses);
        }
        double total = transportFareD +transportFareD2 +transportFareD3 + FoodingAllowanceD + hotelChargesD + otherExpensesD + localExpensesD;
        et_particulars.setText(total + "");
    }

    public void chooseDate(View v) {
        openDatePicker(1);
    }
    public void chooseToDate(View v) {
        openDatePicker(2);
    }


    private void openDatePicker(final int type) {
        // Get Current Date
        final Calendar c = Calendar.getInstance();
        mYear = c.get(Calendar.YEAR);
        mMonth = c.get(Calendar.MONTH);
        mDay = c.get(Calendar.DAY_OF_MONTH);
        //launch datepicker modal
        DatePickerDialog datePickerDialog = new DatePickerDialog(this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth)
                    {
                        if(type==1)
                        {
                            //btn_date.setText(dayOfMonth + "/" + (monthOfYear + 1) + "/" + year);
                            //Constants.menuDetailsObj.getDsr_pdf().toLowerCase().matches("yes")
                            try {
                                //if(Constants.menuDetailsObj.getTA_DA_km_tracking_mode().matches("OWN#PUBLIC")){
                                if(Constants.menuDetailsObj.getDsr_pdf().toLowerCase().matches("yes")){
                                    String chosenFromDate="";
                                    chosenFromDate =  new SimpleDateFormat("dd/MM/yyyy").format(Calendar.getInstance().getTime());
                                    String chosenDate = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
                                    try {
                                        chosenDate =  new SimpleDateFormat("dd/MM/yyyy").format(chosenDate);
                                    }catch (Exception e){
                                        btn_date.setText(chosenFromDate);
                                    }
                                    String m = "01",d="01";
                                    if(dayOfMonth<10){
                                        d= "0"+dayOfMonth;
                                    }else{
                                        d= ""+dayOfMonth;
                                    }
                                    if((monthOfYear+1)<10){
                                        m= "0"+(monthOfYear+1);
                                    }else{
                                        m= ""+(monthOfYear+1);
                                    }
                                    chosenDate = d + "/" + m + "/" + year;
                                    //if(Utils.checkIfToDateGraterThanFromDate(chosenFromDate,chosenDate,"dd/MM/yyyy"))
                                    if(chosenFromDate.matches(chosenDate))
                                    {
                                        btn_date.setText(chosenDate);
                                    }else
                                    {
                                        //btn_date.setText(chosenDate);
                                        Utils.showToast(mContext,"Date Must Be today");
                                    }

                                }else{
                                    btn_date.setText(dayOfMonth + "/" + (monthOfYear + 1) + "/" + year);
                                }
                            }catch (Exception e){
                                Log.d("tourDate", "onDateSet: "+e.toString());
                            }

                        }
                        else
                        {
                            String chosenFromDate = btn_date.getText().toString();
                            if(chosenFromDate.contains("/"))
                            {
                                String chosenDate = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
                                if(Utils.checkIfToDateGraterThanFromDate(chosenFromDate,chosenDate,"dd/MM/yyyy"))
                                {
                                    btn_date_to.setText(chosenDate);
                                }
                                else
                                {
                                    Utils.showToast(mContext,"TO Date Must Be Grater Than From Date");
                                }

                            }
                            else
                            {
                                Utils.showToast(mContext,"Please Select From Date First.");
                            }
                        }
                    }
                }, mYear, mMonth, mDay);
        datePickerDialog.show();
        if(type==1)
        {
            datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());
            datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis()- (1 * 24 * 60 * 60 * 1000));
        }
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
    public void addAttachmentFuelBill(View v)
    {
        Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        if (takePictureIntent.resolveActivity(getPackageManager()) != null)
        {
            startActivityForResult(takePictureIntent, TAKE_PHOTO_CODE_FUEL);
        }

    }
    public void addAttachmentDa(View v) //DA_Attachment
    {
        attachmentType=2;
        if (numberOfImageAddedDa < maxImageLimit)
        {
            launchCameraToTakeImage(TAKE_PHOTO_CODE);
        } else {
            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
        }
    }
    public void addAttachmentLodging(View v) //Lodging_Attachment
    {
        attachmentType=3;
        if (numberOfImageAddedLodging < maxImageLimit)
        {
            launchCameraToTakeImage(TAKE_PHOTO_CODE);
        } else {
            Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
        }
    }
    public void addAttachmentTravel(View v) //Trans_Attachment
    {
        attachmentType=4;
        if (numberOfImageAddedTravel < maxImageLimit)
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
            imageName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpg";

            File file = new File(Utils.getAppStoragePath(mContext)+imageName);
            imageUri = Uri.fromFile(file);

            /*Intent intent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
            intent.putExtra(MediaStore.EXTRA_OUTPUT, imageUri);
            startActivityForResult(intent, CODE);*/
            if(Constants.nickName.equalsIgnoreCase("NIMBUS")){
                Intent photoPickerIntent = new Intent(Intent.ACTION_GET_CONTENT);
                photoPickerIntent.setType("image/*");
                startActivityForResult(photoPickerIntent, SELECT_PHOTO);
            }else {
                Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                if (takePictureIntent.resolveActivity(getPackageManager()) != null) {
                    //takePictureIntent.putExtra(MediaStore.EXTRA_OUTPUT, imageUri);
                    startActivityForResult(takePictureIntent, CODE);
                }
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

//        Intent cameraIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
//        startActivityForResult(cameraIntent, TAKE_PHOTO_CODE);
    }
    private void storeImageInLocalStorageShowOnImageViewCustomize(Bitmap customerPicBitmap,Boolean ShowImageOnUi,String imagePath,ImageView iv)
            throws FileNotFoundException {
        File outputFile = null;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out = null;
        out = new FileOutputStream(outputFile);
        //customerPicBitmap = Utils.getResizedBitmap(customerPicBitmap, 900, 900);
        if(Constants.nickName.equalsIgnoreCase("NIMBUS")){
            customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 40, out);
        }else {
            customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
        }
        //ByteArrayOutputStream bytes = new ByteArrayOutputStream();
        //customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 90, bytes);
        //customerPicBitmap.

      /*  File image = File.createTempFile(
                imageFileName,  *//* prefix *//*
                ".jpg",         *//* suffix *//*
                storageDir      *//* directory *//*
        );*/

       // customerPicBitmap.compress(Bitmap.CompressFormat.PNG, 100,
       //         new FileOutputStream(outputFile));


        if(ShowImageOnUi)
        {
            //iv.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 600, 600));
            iv.setImageBitmap(customerPicBitmap);
        }
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK)
        {
            if (requestCode == TAKE_PHOTO_CODE_FUEL)
            {
                if (data != null)
                {

                    try {
                        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        Bundle extras = data.getExtras();
                        Bitmap fuelPicBitmap = null;

                        fuelPicBitmap = (Bitmap) extras.get("data");

                        FuelBillAttachmentName = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                        String imagePath = Utils.getAppStoragePath(mContext) + FuelBillAttachmentName;
                        ImageView fuelBillattachmentImageView= findViewById(R.id.fuelBillattachmentImageView);
                        storeImageInLocalStorageShowOnImageViewCustomize(fuelPicBitmap,true,imagePath,fuelBillattachmentImageView);
                        mAceDnsTransactionDatabase.insertToSupportingAttachTable(FuelBillAttachmentName, "add_fuel_bill");


                    } catch (Exception e) {
                        Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                    }




                }
                else
                {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                }

            }else if(requestCode == SELECT_PHOTO){

                try {
                   /* Uri selectedImageUri = data.getData();
                    selectedImagePath = getPath(selectedImageUri);


                    File bitmapFile = new File(selectedImageUri.toString());
                    Bitmap bitmap = BitmapFactory.decodeFile(bitmapFile);
*/

                    Uri imageUri = data.getData();
                    Bitmap fuelPicBitmap = MediaStore.Images.Media.getBitmap(this.getContentResolver(), imageUri);


                    fuelPicBitmap = Utils.getResizedBitmap(fuelPicBitmap, 1200, 900);
                    /*Bundle extras = data.getExtras();
                    Bitmap fuelPicBitmap = null;*/
                    //fuelPicBitmap = (Bitmap) extras.get("data");
                    FuelBillAttachmentName = Constants.employeeDetailObject.getEmpCode() + imageName + "";
                    String imagePath = Utils.getAppStoragePath(mContext) + imageName;
                    ImageView fuelBillattachmentImageView= findViewById(R.id.fuelBillattachmentImageView);
                    storeImageInLocalStorageShowOnImageViewCustomize(fuelPicBitmap,true,imagePath,fuelBillattachmentImageView);
                    mAceDnsTransactionDatabase.insertToSupportingAttachTable(FuelBillAttachmentName, "add_fuel_bill");

                    Bitmap imageBitmap = MediaStore.Images.Media.getBitmap(getContentResolver(), imageUri);
                    //imageBitmap = Utils.getResizedBitmap(imageBitmap, 900, 900);


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
                        Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
                    }




                }catch (Exception e){
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                }
            }
            else
            {
                try {
                    Bundle extras = data.getExtras();
                    Bitmap fuelPicBitmap = null;
                    fuelPicBitmap = (Bitmap) extras.get("data");
                    String imagePath = Utils.getAppStoragePath(mContext) + imageName;
                    ImageView fuelBillattachmentImageView= findViewById(R.id.fuelBillattachmentImageView);
                    storeImageInLocalStorageShowOnImageViewCustomize(fuelPicBitmap,true,imagePath,fuelBillattachmentImageView);
                    //mAceDnsTransactionDatabase.insertToSupportingAttachTable(FuelBillAttachmentName, "add_fuel_bill");

                    Bitmap imageBitmap = MediaStore.Images.Media.getBitmap(getContentResolver(), imageUri);
                    imageBitmap = Utils.getResizedBitmap(imageBitmap, 900, 900);
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
                        Toast.makeText(mContext, "Maximum image is taken", Toast.LENGTH_SHORT).show();
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                }
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
    public void chooseTravelMode(View v) {
        if(!btn_transport_mode_type.getText().toString().equals("SELECT")){
            ShowTravelCategoryDialog();
        }else{
            Utils.showToast(mContext,"Select Fare mode first");
        }

    }

    public void chooseTravelModeType(View v) {
        ShowTravelCategoryDialogType();
    }

    public void submit(View v)
    {
        date = btn_date.getText().toString();
        dateTo = btn_date_to.getText().toString();
        placeFrom = et_from.getText().toString();
        placeTo = et_to.getText().toString();
        fromKms = et_from_kms.getText().toString();
        toKms = et_to_kms.getText().toString();
        travelMode = tv_fare1.getText().toString();
        travelMode2 = tv_fare2.getText().toString();
        travelMode3 = tv_fare3.getText().toString();
        particulars = et_particulars.getText().toString();

        localConv = et_local_conv.getText().toString();
        transportFare = et_transport_fare.getText().toString();
        transportFare2 = et_transport_fare2.getText().toString();
        transportFare3 = et_transport_fare3.getText().toString();
        FoodingAllowance = et_fooding_allowance.getText().toString();
        hotelCharges = et_hotel_charges.getText().toString();
        otherExpenses = et_other_expenses.getText().toString();
        remarks = et_remarks.getText().toString();
        daily_assistance= et_daily_assistance.getText().toString();
        km_travel = et_km_travel.getText().toString();

        if (date.matches("dd/MM/yyyy")) {
            Toast.makeText(mContext, "Date field is mandatory", Toast.LENGTH_SHORT).show();
        }else if (travelMode.matches("MODE")) {
            Toast.makeText(mContext, "Please select mode", Toast.LENGTH_SHORT).show();
        } else if (!localConv.matches("") || !transportFare.matches("") || !FoodingAllowance.matches("") || !hotelCharges.matches("") || !otherExpenses.matches("")) {
            saveTravelExpenseData();
        } else {
            Toast.makeText(mContext, "Minimum one expense details is mandatory", Toast.LENGTH_SHORT).show();
        }

    }

    public void ShowTravelCategoryDialogType() {
        mTravelExpCategoryList = new ArrayList<>();
        mTravelExpCategoryList = mAceDnsDatabase.getTravelCatList();
        if (mTravelExpCategoryList != null) {
            final Dialog dialog = new Dialog(mContext);
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setContentView(R.layout.select_from_list);
            transportModeList = new ArrayList<String>();
            /*for (int ii = 0; ii < mTravelExpCategoryList.size(); ii++) {
                transportModeList.add(mTravelExpCategoryList.get(ii).getCategoryName());
            }*/
            transportModeList.add("FARE 1");
            transportModeList.add("FARE 2");
            transportModeList.add("FARE 3");
            TextView title = (TextView) dialog.findViewById(R.id.title);
            title.setText("Please select Fare mode");
            ListView dialogList = (ListView) dialog.findViewById(R.id.list);
            SimpleStringAdapter adapter1 = new SimpleStringAdapter(mContext, R.layout.simple_list_child, transportModeList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                   // TravelExpCategory selectedCat = mTravelExpCategoryList.get(i);
                    String categoryName = transportModeList.get(i).toString();

                    btn_transport_mode_type.setText(categoryName);
                    if(categoryName.equalsIgnoreCase("FARE 2")){
                        llFare2.setVisibility(View.VISIBLE);
                    }
                    if(categoryName.equalsIgnoreCase("FARE 3")){
                        llFare3.setVisibility(View.VISIBLE);
                    }

                    dialog.cancel();
                }
            });

            Button cancel = (Button) dialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            dialog.show();
        } else {
            Utils.showToast(mContext, "No travel category found. Please Synchronize Data");
        }

    }

    public void ShowTravelCategoryDialog() {
        mTravelExpCategoryList = new ArrayList<>();
        mTravelExpCategoryList = mAceDnsDatabase.getTravelCatList();
        if (mTravelExpCategoryList != null) {
            final Dialog dialog = new Dialog(mContext);
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setContentView(R.layout.select_from_list);
            transportModeList = new ArrayList<String>();
            for (int ii = 0; ii < mTravelExpCategoryList.size(); ii++) {
                transportModeList.add(mTravelExpCategoryList.get(ii).getCategoryName());
            }
            TextView title = (TextView) dialog.findViewById(R.id.title);
            title.setText("Please select transport mode");
            ListView dialogList = (ListView) dialog.findViewById(R.id.list);
            SimpleStringAdapter adapter1 = new SimpleStringAdapter(mContext, R.layout.simple_list_child, transportModeList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    TravelExpCategory selectedCat = mTravelExpCategoryList.get(i);
                    String categoryName = selectedCat.getCategoryName().toLowerCase();

                   // btn_transport_mode.setText(categoryName);

                    if(btn_transport_mode_type.getText().toString().equals("FARE 1")){
                        tv_fare1.setText(categoryName);
                    }else if(btn_transport_mode_type.getText().toString().equals("FARE 2")){
                        tv_fare2.setText(categoryName);
                    }else if(btn_transport_mode_type.getText().toString().equals("FARE 3")){
                        tv_fare3.setText(categoryName);
                    }

                    dialog.cancel();
                }
            });

            Button cancel = (Button) dialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            dialog.show();
        } else {
            Utils.showToast(mContext, "No travel category found. Please Synchronize Data");
        }

    }

    public void saveTravelExpenseData()
    {
        new GPSTracker(mContext);
        if (Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") && (Constants.currentLat=="0.0" || Constants.currentLong=="0.0"))
        {
            Utils.showToast(mContext,"Please turn on location sharing first.");
        }
        else
        {
            new GPSTracker(mContext);
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Saving Data.Please wait..");
            mProgressDialog.show();
            new Thread() {
                public void run() {
                    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    String id = Constants.employeeDetailObject.getEmpCode() + timeStamp;
                    String transport_attachment = "";
                    String fooding_attachment = "";
                    String lodging_attachment = "";
//                    String other_attachment = parseAndInsert("TRANS_ATT", Trans_Attachment);
                    String other_attachment = parseAndInsertFromArrayList( AttachmentNames);
                    EditText et_misc=findViewById(R.id.et_misc);
//                    mAceDnsTransactionDatabase.insertToTourTravelExpense(id, new DateTimeFormatter().changeDateFormat("dd/MM/yyyy", date, "yyyy-MM-dd"), placeFrom, placeTo, particulars, localConv, travelMode, transportFare, FoodingAllowance, hotelCharges, otherExpenses, remarks, attachmentIdSemecolonSeparated);
                    mAceDnsTransactionDatabase.insertToTourExpenseDetails1("TT" + id,tourType, new DateTimeFormatter().changeDateFormat("dd/MM/yyyy", date, "yyyy-MM-dd"), new DateTimeFormatter().changeDateFormat("dd/MM/yyyy", dateTo, "yyyy-MM-dd"),"","", placeFrom, placeTo, particulars, localConv, travelMode, transportFare, FoodingAllowance, hotelCharges, otherExpenses, remarks,
                            transport_attachment, fooding_attachment, lodging_attachment, other_attachment,FuelBillAttachmentName,et_misc.getText().toString(),daily_assistance,""+km_travel,""+travelMode2,""+travelMode3,""+transportFare2,""+transportFare3);
                    mAceDnsTransactionDatabase.insertToLocationTable("TT", timeStamp);

                    Message msgObj = mHandler.obtainMessage();
                    Bundle b = new Bundle();
                    b.putString("message", "SubmitJobDone");
                    msgObj.setData(b);
                    mHandler.sendMessage(msgObj);
                }
            }.start();
        }

    }

    public String parseAndInsert(String type, HashMap<String, Bitmap> local_Attachment)
    {
        String attachmentIdSemecolonSeparated = "";
        try
        {
            for (Map.Entry<String, Bitmap> entry : local_Attachment.entrySet())
            {
                String currentAttachmentName = entry.getKey();
                SaveImageToExternalStorage(entry.getValue(), Utils.getAppStoragePath(mContext), currentAttachmentName);
                mAceDnsTransactionDatabase.insertToSupportingAttachTable(currentAttachmentName, "TOUR_TRAVEL");
                if (attachmentIdSemecolonSeparated.matches(""))
                {
                    attachmentIdSemecolonSeparated = currentAttachmentName;
                }
                else
                {
                    attachmentIdSemecolonSeparated = attachmentIdSemecolonSeparated + ";" + currentAttachmentName;
                }
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return attachmentIdSemecolonSeparated;
    }
    public String parseAndInsertFromArrayList(ArrayList<String> AttachmentNames)
    {
        String attachmentIdSemecolonSeparated = "";
        try
        {
            for(int i = 0; i< AttachmentNames.size(); i++)
            {
                String currentAttachmentName=AttachmentNames.get(i);
                mAceDnsTransactionDatabase.insertToSupportingAttachTable(currentAttachmentName, "TOUR_TRAVEL");
                if (attachmentIdSemecolonSeparated.matches(""))
                {
                    attachmentIdSemecolonSeparated = currentAttachmentName;
                }
                else
                {
                    attachmentIdSemecolonSeparated = attachmentIdSemecolonSeparated + ";" + currentAttachmentName;
                }
            }

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return attachmentIdSemecolonSeparated;
    }

    public void finishCurrentActivity(View v) {
        finish();
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }
    }

}
