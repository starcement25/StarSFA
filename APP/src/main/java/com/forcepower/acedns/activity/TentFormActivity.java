package com.forcepower.acedns.activity;

import static com.forcepower.acedns.constants.Constants.currentLat;
import static com.forcepower.acedns.constants.Constants.currentLong;

import androidx.fragment.app.FragmentActivity;

import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.TimePickerDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.TextWatcher;
import android.text.format.DateFormat;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.TimePicker;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_TentKnockingAttachmentExportTask;
import com.forcepower.acedns.backgroundTask.TRANS_Tent_Form_TransactionTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.TentExixtingProductListAdapter;
import com.forcepower.acedns.api.clients.LocalStorage;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.TentProductList;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class TentFormActivity extends FragmentActivity implements DatePickerDialog.OnDateSetListener, TimePickerDialog.OnTimeSetListener{

    Button mButtonBack;
    Context mContext;
    ListView retailList;
    ArrayList<String> tempCustomerList;
    RadioGroup rgDemo;
    RadioButton rbYes,rbNo;
    Dialog demoFormDialog;
    SimpleDateFormat dateFormat;
    CaldroidListener listener;
    private CaldroidFragment dialogCaldroidFragment;

    Button btnDate,buttonDate,btnSubmit,buttonTime,btn_set_address,btnDemoDate;
    static final int REQUEST_IMAGE_CAPTURE = 1;
    static final int REQUEST_IMAGE_CAPTURE_START = 11;

    ImageView imgPic,iv_check_chimney,iv_check_ro,iv_check_other,iv_check_uncheck_no_product;
    String btn="";
    LinearLayout body_layout;
    EditText ed_phone,ed_name,ed_remarks;

    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase mAceDnsDatabase;

    String startingDateTime,endDateTime,startLat,startLon,endLat,endLon,startImg,endImg,imgUri,cName,mobile,altrMobile,
    demoInterested,tentativeDateTime,demoFor,existingProduct,strt,address_text="",mobile_c="";

    int day, month, year, hour, minute;
    int myday, myMonth, myYear, myHour, myMinute;
    boolean iv_check_chimney_flg = false,iv_check_ro_flg = false,iv_check_chimney_ro_flg = false,iv_check_other_flg = false,iv_check_uncheck_no_product_flg = false,address_flg = false;

    public static ArrayList<TentProductList> tempExistingProductlList;

    LocationTracker locationTracker;
    LocalStorage localStorage;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_tent_form);

        mButtonBack = (Button) findViewById(R.id.back);
        rgDemo = (RadioGroup) findViewById(R.id.rgDemo);
        ed_phone = findViewById(R.id.ed_phone);
        ed_name = findViewById(R.id.ed_name);
        ed_remarks = findViewById(R.id.ed_remarks);
        iv_check_chimney = findViewById(R.id.iv_check_chimney);
        iv_check_uncheck_no_product = findViewById(R.id.iv_check_uncheck_no_product);
        iv_check_ro = findViewById(R.id.iv_check_ro);
        iv_check_other = findViewById(R.id.iv_check_other);
        btnSubmit = findViewById(R.id.btn_next);
        btn_set_address = findViewById(R.id.btn_set_address);

        mContext = TentFormActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        localStorage = new LocalStorage(getApplicationContext());

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        retailList = (ListView) findViewById(R.id.prodQtyRateListView);
        body_layout = (LinearLayout) findViewById(R.id.body_layout);

        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();

            }
        });

        iv_check_chimney.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (iv_check_chimney_flg) {
                    iv_check_chimney_flg = false;
                    iv_check_chimney_ro_flg = false;
                    iv_check_chimney.setImageResource(R.drawable.uncheck);
                }else{
                    iv_check_chimney_flg = true;
                    iv_check_chimney_ro_flg = true;
                    iv_check_chimney.setImageResource(R.drawable.check);
                }
                address_flg = true;
            }
        });

        iv_check_ro.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (iv_check_ro_flg) {
                    iv_check_ro_flg = false;
                    iv_check_ro.setImageResource(R.drawable.uncheck);
                }else{
                    iv_check_ro_flg = true;
                    iv_check_ro.setImageResource(R.drawable.check);
                }
                address_flg = true;
            }
        });

        iv_check_other.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (iv_check_other_flg) {
                    iv_check_other_flg = false;
                    iv_check_other.setImageResource(R.drawable.uncheck);
                }else{
                    iv_check_other_flg = true;
                    iv_check_other.setImageResource(R.drawable.check);
                }
                address_flg = true;
            }
        });

        iv_check_uncheck_no_product.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (iv_check_uncheck_no_product_flg) {
                    iv_check_uncheck_no_product_flg = false;
                    iv_check_uncheck_no_product.setImageResource(R.drawable.uncheck);
                }else{
                    existingProduct();
                    iv_check_uncheck_no_product_flg = true;
                    iv_check_uncheck_no_product.setImageResource(R.drawable.check);
                }
            }
        });

        rgDemo.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);
                boolean isChecked = checkedRadioButton.isChecked();
                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){

                        int j = 0;
                        for(int i=0; i< Constants.tentExistingProductList.size();i++) {
                            if(Constants.tentExistingProductList.get(i).getBrand().toString().isEmpty()){

                            }else{
                                if(iv_check_uncheck_no_product_flg==true){
                                    j = j+1;
                                }
                              j = j+1;
                            }
                        }

                        if(ed_phone.getText().toString().isEmpty()){
                            Utils.showToast(mContext,"Enter 10 digit Mobile Number");
                        }else {
                            if(j==0){
                                if(!iv_check_uncheck_no_product_flg) {
                                    Utils.showToast(mContext, "Please Select Existing Product");
                                }else {
                                    showDemoDialog();
                                }
                            }else {
                                showDemoDialog();
                            }
                        }
                        demoInterested ="yes";
                    }else{
                        demoInterested ="no";
                    }

                }
            }
        });


        dateFormat = new SimpleDateFormat("dd-MM-yyyy");


        btnSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(localStorage.getTentID().isEmpty()){
                    Utils.showToast(mContext, "You do not start Tent.");
                    finish();
                }else {
                    if (ed_phone.getText().toString().length() == 10) {
                        String d = dataHelperObj.getTentFormMobile(ed_phone.getText().toString());

                        if(!d.isEmpty()){
                            Utils.showToast(mContext,"Mobile Number Already Exist");
                        }else {
                            if(address_flg){
                                if(!address_text.isEmpty()){
                                    submitToDatabase();
                                }else{
                                    Utils.showToast(mContext,"Please add address");
                                }
                            }else {
                                submitToDatabase();
                            }
                        }

                    } else {
                        Utils.showToast(mContext, "Provide Valid 10 digit mobile Number");
                    }
                }
            }
        });

        btn_set_address.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(iv_check_chimney_flg==true || iv_check_ro_flg == true || iv_check_other_flg == true) {
                    String mobile_no = ed_phone.getText().toString();
                    showAddCustDialog(mobile_no);
                }else{
                    Utils.showToast(mContext,"Please select Product for demo");
                }
            }
        });


        existingProduct();

        endImg ="";
        startImg = "";
        strt = "start";

        if(localStorage.getStartTime().isEmpty()) {
            String datetime = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());
            localStorage.setStartTime(""+datetime);
            startingDateTime = localStorage.getStartTime();
        }else{
            startingDateTime = localStorage.getStartTime();
        }


        //Utils.showToast(mContext,""+localStorage.getTentID());

        //ArrayList<Location> unUploadedTransactionDR = dataHelperObj.getUnuploadedTransactionPropello("tent_form", "TFE000220220512190035");
        //TRANS_Tent_Form_TransactionTask sb2 = new TRANS_Tent_Form_TransactionTask(TentFormActivity.this, unUploadedTransactionDR);
        //sb2.execute();
        //localStorage.setStartImage("");
        if(!localStorage.getStartImage().isEmpty()) {
            showEndConfirmation("close");
            //Constants.tentFormStratImage = localStorage.getStartImage();
        }else {
            showStartConfirmation();
        }
    }

    private void existingProduct(){

        final ArrayList<TentProductList> expList1 = new ArrayList<>();

        TentProductList te = new TentProductList();
        te.setDate("Chimney");
        te.setTotal("0");
        te.setBrand("");
        te.setLife("");
        expList1.add(te);
        te = new TentProductList();
        te.setDate("RO");
        te.setTotal("0");
        te.setBrand("");
        te.setLife("");
        expList1.add(te);
        te = new TentProductList();
        te.setDate("UV");
        te.setTotal("0");
        te.setBrand("");
        te.setLife("");
        expList1.add(te);
        te = new TentProductList();
        /*te.setDate("No Product");
        te.setTotal("0");
        te.setBrand("");
        te.setLife("");
        expList1.add(te);*/
        Constants.tentExistingProductList = new ArrayList<>();
        Constants.tentExistingProductList = expList1;
        ListView dialogList = (ListView) findViewById(R.id.prodQtyRateListView);
        TentExixtingProductListAdapter adapter1 = new TentExixtingProductListAdapter(TentFormActivity.this, expList1);
        dialogList.setAdapter(adapter1);

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

                EditText editText = (EditText) ((EditText) view).getText();
                String et = ((EditText) view).getText().toString();

                editText.addTextChangedListener(new TextWatcher() {
                    @Override
                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                    }

                    @Override
                    public void onTextChanged(CharSequence s, int start, int before, int count) {
//Utils.showToast(mContext,""+s);
                    }

                    @Override
                    public void afterTextChanged(Editable s) {

                    }
                });
                if(expList1.get(position).getTotal().equalsIgnoreCase("1"))
                {

                    expList1.get(position).setTotal("0");
                    //Utils.showToast(mContext,position+"-1");
                }
                else
                {
                    expList1.get(position).setTotal("1");
                    //Utils.showToast(mContext,position+"-0");
                }
                //
                adapter1.setFilter(expList1);
            }
        });


    }

    public void showStartConfirmation(){
        startImg = "";
        strt = "start";

        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        builder.setTitle("Want to start a new Tent");
        builder.setMessage("Are you sure?");

        builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
                // Do nothing but close the dialog
                if(localStorage.getTentID().isEmpty() || localStorage.getStartImage().isEmpty()) {

                   String tentFormId = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

                    boolean isTransactionIDExist = false;
                    do {
                        isTransactionIDExist = dataHelperObj.IsTransactioIdExist(tentFormId);
                        if (true == isTransactionIDExist) {
                            tentFormId = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        }
                    }
                    while (true == isTransactionIDExist);

                    if (localStorage.getTentID().isEmpty()) {

                        localStorage.setTentID("" + tentFormId);
                        Constants.tentFormIDCode = localStorage.getTentID();

                    }
                    showDatePicStart();
                    dialog.dismiss();
                }else{

                    Utils.showToast(mContext,"A Tent already Started");
                    dialog.dismiss();
                    finish();
                }

            }
        });

        builder.setNegativeButton("NO", new DialogInterface.OnClickListener() {

            @Override
            public void onClick(DialogInterface dialog, int which) {
                if(!localStorage.getTentID().isEmpty()){
                    Constants.tentFormIDCode = localStorage.getTentID();
                }else{
                    Utils.showToast(mContext,"No Tent Started");
                    finish();
                }
                // Do nothing
                dialog.dismiss();
            }
        });

        AlertDialog alert = builder.create();
        alert.show();

    }

    public void showEndConfirmation(String type){
        Constants.tentFormEndImage ="";

        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        builder.setTitle("Want to End this Tent");
        builder.setMessage("Are you sure?");

        builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
                showDatePicEnd(""+type);
                dialog.dismiss();
                if(!localStorage.getTentID().isEmpty()){
                    //localStorage.setTentID("");
                }
            }
        });

        builder.setNegativeButton("NO", new DialogInterface.OnClickListener() {

            @Override
            public void onClick(DialogInterface dialog, int which) {
                if(!localStorage.getTentID().isEmpty()){
                    if(type.matches("close")){

                    }else {
                        saveToDatabase("no");
                    }
                }else{
                    Utils.showToast(mContext,"No Tent Started");
                }
                // Do nothing
                dialog.dismiss();
            }
        });

        AlertDialog alert = builder.create();
        alert.show();

    }

    public void showDemoDialog() {

        demoFormDialog = new Dialog(TentFormActivity.this, R.style.PauseDialog);
        demoFormDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        demoFormDialog.setContentView(R.layout.select_demo_form);
        demoFormDialog.setCancelable(false);
        TextView title = (TextView) demoFormDialog.findViewById(R.id.title);
        title.setText("Please Fill Details");

        btnDate = (Button) demoFormDialog.findViewById(R.id.buttonDate);
        btnDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                //chooseDateDialog();
                Calendar calendar = Calendar.getInstance();
                year = calendar.get(Calendar.YEAR);
                month = calendar.get(Calendar.MONTH);
                day = calendar.get(Calendar.DAY_OF_MONTH);
                DatePickerDialog datePickerDialog = new DatePickerDialog(TentFormActivity.this, (DatePickerDialog.OnDateSetListener) TentFormActivity.this,year, month,day);
                //datePickerDialog.
                datePickerDialog.show();
            }
        });

        buttonTime= (Button) demoFormDialog.findViewById(R.id.buttonTime);
        buttonTime.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                Calendar c = Calendar.getInstance();
                hour = c.get(Calendar.HOUR);
                minute = c.get(Calendar.MINUTE);
                TimePickerDialog timePickerDialog = new TimePickerDialog(TentFormActivity.this, (TimePickerDialog.OnTimeSetListener) TentFormActivity.this, hour, minute, DateFormat.is24HourFormat(TentFormActivity.this));
                timePickerDialog.show();

            }
        });

            Button cancel = (Button) demoFormDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.VISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {

                    if(buttonTime.getText().toString().matches("Select Timem")){
                        Utils.showToast(mContext,"Please Select Time");
                    }else if(btnDate.getText().toString().matches("Select Date")){
                        Utils.showToast(mContext,"Please Select Date");
                    }else{
                        tentativeDateTime = ""+btnDate.getText().toString();
                        demoFormDialog.cancel();
                    }
                }
            });

            Button create_route = (Button) demoFormDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            create_route.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    //showCreateRouteDialog();
                    demoFormDialog.cancel();
                }
            });


        demoFormDialog.show();


    }

    private static String currentPic = "";
    Bitmap customerPicBitmap = null;
    String supportingAttachmentNameForCustomerImage="";

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        //Utils.showToast(mContext,resultCode+"");

        if (requestCode == REQUEST_IMAGE_CAPTURE)
        {
            if (resultCode == RESULT_OK) {
                if (data != null) {
                    if (requestCode == REQUEST_IMAGE_CAPTURE)
                    {
                        try {

                            Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();

                            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                            Bundle extras = data.getExtras();

                            if(currentPic.matches("")){
                                customerPicBitmap = (Bitmap) extras.get("data");
//                              //customerPicBitmap = Utils.getResizedBitmap(imageBitmap, 100, 100);
                                supportingAttachmentNameForCustomerImage = "TE"+Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                                String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                                storeImageInLocalStorageShowOnImageView(customerPicBitmap,true,imagePath);

                                Constants.tentFormEndImage = supportingAttachmentNameForCustomerImage;
                                dataHelperObj.insertToSupportingAttachTable(""+supportingAttachmentNameForCustomerImage, "tent_form");
                                localStorage.setEndImage(""+supportingAttachmentNameForCustomerImage);
                            }

                        } catch (Exception e) {


                            //if(Constants.employeeDetailObject==null)
                            //{
                                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                            //}
                            Toast.makeText(mContext, "Something went wrong while getting the image, please try again.."+e.toString(), Toast.LENGTH_SHORT).show();
                        }

                    } else {

                            Toast.makeText(mContext, "Something went wrong while getting the image, please try again.2", Toast.LENGTH_SHORT).show();

                    }


                } else {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.3", Toast.LENGTH_SHORT).show();
                }

            } else {
//                isUserPicTaken = false;
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.4", Toast.LENGTH_SHORT).show();
            }

        }else if (requestCode == REQUEST_IMAGE_CAPTURE_START)
        {
            try {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();

                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                Bundle extras = data.getExtras();

                if(currentPic.matches("")){
                    customerPicBitmap = (Bitmap) extras.get("data");
                    supportingAttachmentNameForCustomerImage = "TF"+ Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                    String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                    storeImageInLocalStorageShowOnImageView(customerPicBitmap,true,imagePath);
                    dataHelperObj.insertToSupportingAttachTable(""+supportingAttachmentNameForCustomerImage, "tent_form");
                    localStorage.setStartImage(""+supportingAttachmentNameForCustomerImage);
                }

            } catch (Exception e) {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.."+e.toString(), Toast.LENGTH_SHORT).show();
            }

        }

        super.onActivityResult(requestCode, resultCode, data);
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
            imgPic.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 100, 100));
        }
    }


    @Override
    public void onDateSet(DatePicker view, int year, int month, int dayOfMonth) {
        myYear = year;
        myday = dayOfMonth;//day;
        myMonth = month+1;
        Calendar c = Calendar.getInstance();
        hour = c.get(Calendar.HOUR);
        minute = c.get(Calendar.MINUTE);
        String chosenFromDate ;
        chosenFromDate =  new SimpleDateFormat("dd/MM/yyyy").format(Calendar.getInstance().getTime());
        String chosenDate = (dayOfMonth+1) + "/" + (month + 1) + "/" + year;
        //Utils.showToast(mContext,"Chochen" +chosenDate +" date" + chosenFromDate);
        if(Utils.checkIfToDateGraterThanFromDate(chosenFromDate,chosenDate,"dd/MM/yyyy"))
        {
            TimePickerDialog timePickerDialog = new TimePickerDialog(TentFormActivity.this, (TimePickerDialog.OnTimeSetListener) TentFormActivity.this, hour, minute, DateFormat.is24HourFormat(this));
            timePickerDialog.show();
        }
        else
        {
            Utils.showToast(mContext,"Date Must Be Grater Than From today");
        }


    }
    @Override
    public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
        myHour = hourOfDay;
        myMinute = minute;
        String myMonthS ="",mydayS="",hourOfDayS="",minuteS="";
        if(myMonth<=9){
            myMonthS = "0"+myMonth;
        }else{
            myMonthS = myMonth+"";
        }
        if(myday<=9) {
            mydayS = "0"+myday;
        }else{
            mydayS = ""+myday;
        }
        if(hourOfDay<=9) {
            hourOfDayS = "0"+hourOfDay;
        }else{
            hourOfDayS = ""+hourOfDay;
        }
        if(minute<=9) {
            minuteS = "0"+minute;
        }else{
            minuteS = ""+minute;
        }

        tentativeDateTime = ""+myYear + "-" + myMonthS + "-" + mydayS + " ";
        tentativeDateTime = tentativeDateTime + "" + hourOfDayS + ":" + minuteS + ":00";

        btnDate.setText(tentativeDateTime);
    }


    private void showDatePicEnd(String type){
        Dialog demoFormDialog;
        body_layout = (LinearLayout) findViewById(R.id.body_layout);
        body_layout.setAlpha(0.3F);

        demoFormDialog = new Dialog(TentFormActivity.this, R.style.PauseDialogTent);
        demoFormDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        demoFormDialog.setContentView(R.layout.select_time_pic_tent_form);
        demoFormDialog.setCancelable(false);
        TextView title = (TextView) demoFormDialog.findViewById(R.id.title);
        title.setText("Please Fill End Details");

        //Utils.showToast(mContext,currentLat);
        btnDate = (Button) demoFormDialog.findViewById(R.id.buttonDate);
        btnDate.setText("Select Date Time");
        String datetime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());
        btnDate.setText(datetime);

        endDateTime = datetime;

        imgPic = (ImageView) demoFormDialog.findViewById(R.id.customerPicImageView);

        Button buttonPic = (Button) demoFormDialog.findViewById(R.id.buttonPic);
        buttonPic.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                takePictureIntent.putExtra("android.intent.extras.CAMERA_FACING", 1);
                if (takePictureIntent.resolveActivity(getPackageManager()) != null)
                {
                    startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE);
                }
            }
        });

        Button cancel = (Button) demoFormDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.VISIBLE);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                body_layout.setAlpha(1.0F);

                    if(Constants.tentFormEndImage.isEmpty()){
                        Utils.showToast(mContext,"Please add a Picture");
                    }else {
                        //Utils.showToast(mContext,""+endImg);
                        if(type.matches("end")){
                            saveToDatabase("end");
                        }else{
                            saveToDatabase("close");
                        }

                        demoFormDialog.cancel();
                        //finish();
                    }
            }
        });

        Button create_route = (Button) demoFormDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        create_route.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                //showCreateRouteDialog();

                demoFormDialog.cancel();
            }
        });

        demoFormDialog.show();
    }

    private void showDatePicStart(){
        Dialog demoFormDialog;
        body_layout = (LinearLayout) findViewById(R.id.body_layout);
        body_layout.setAlpha(0.3F);

        demoFormDialog = new Dialog(TentFormActivity.this, R.style.PauseDialogTent);
        demoFormDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        demoFormDialog.setContentView(R.layout.select_time_pic_tent_form);
        demoFormDialog.setCancelable(false);
        TextView title = (TextView) demoFormDialog.findViewById(R.id.title);
        title.setText("Please Fill Start Details");

        //Utils.showToast(mContext,currentLat);
        btnDate = (Button) demoFormDialog.findViewById(R.id.buttonDate);
        btnDate.setText("Select Date Time");
        String datetime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());
        btnDate.setText(datetime);

        startingDateTime = datetime;

        imgPic = (ImageView) demoFormDialog.findViewById(R.id.customerPicImageView);

        Button buttonPic = (Button) demoFormDialog.findViewById(R.id.buttonPic);
        buttonPic.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                takePictureIntent.putExtra("android.intent.extras.CAMERA_FACING", 1);
                if (takePictureIntent.resolveActivity(getPackageManager()) != null)
                {
                    startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE_START);
                }
            }
        });

        Button cancel = (Button) demoFormDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.VISIBLE);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                body_layout.setAlpha(1.0F);

                if(!localStorage.getStartImage().isEmpty()) {
                    Constants.tentFormStratImage = localStorage.getStartImage();
                }

                    if(Constants.tentFormStratImage.isEmpty()){
                        Utils.showToast(mContext,"Please add a  Tent Start Picture");
                    }else {

                        demoFormDialog.cancel();
                    }

            }
        });

        Button create_route = (Button) demoFormDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        create_route.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                //showCreateRouteDialog();

                demoFormDialog.cancel();
            }
        });

        demoFormDialog.show();
    }


    public void showAddCustDialog(String mNo) {

        demoFormDialog = new Dialog(TentFormActivity.this, R.style.PauseDialog);
        demoFormDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        demoFormDialog.setContentView(R.layout.add_cust_demo_form);
        demoFormDialog.setCancelable(false);
        TextView title = (TextView) demoFormDialog.findViewById(R.id.title);
        title.setText("Please Fill Details");

        EditText ed_name,ed_phone_cust,ed_address;
        //ed_name = (EditText) demoFormDialog.findViewById(R.id.ed_name);
        ed_phone_cust = (EditText) demoFormDialog.findViewById(R.id.ed_phone_cust);
        ed_address = (EditText) demoFormDialog.findViewById(R.id.ed_address);

        ed_phone_cust.setText(mNo);

        Button cancel = (Button) demoFormDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.VISIBLE);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {

                String timeStamp = localStorage.getTentID();
                String cMpbile="";

                boolean r = false;
                if(ed_address.getText().toString().isEmpty()){
                    Utils.showToast(mContext,"Enter Customer Address");
                }else {
                    address_text = ed_address.getText().toString();
                    mobile_c = ed_phone_cust.getText().toString();
                    r = true;
                    address_flg = true;
                    /*if(iv_check_chimney_flg==true){
                        r = dataHelperObj.AddTentFormCust(timeStamp, "" + ed_address.getText().toString(), "" + ed_phone_cust.getText().toString(), "Chimney",""+mNo);
                    }
                    if(iv_check_ro_flg==true){
                        r = dataHelperObj.AddTentFormCust(timeStamp, "" + ed_address.getText().toString(), "" + ed_phone_cust.getText().toString(), "RO",""+mNo);
                    }
                    if(iv_check_other_flg==true){
                        r = dataHelperObj.AddTentFormCust(timeStamp, "" + ed_address.getText().toString(), "" + ed_phone_cust.getText().toString(), "Others",""+mNo);
                    }*/
                    //r = dataHelperObj.AddTentFormCust(timeStamp, "" + ed_address.getText().toString(), "" + ed_phone.getText().toString(), "Chimney");
                }
                if(r==true) {
                    //btn_set_address.setEnabled(false);
                    demoFormDialog.cancel();
                }

            }
        });

        Button create_route = (Button) demoFormDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        create_route.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                //showCreateRouteDialog();
                demoFormDialog.cancel();
            }
        });

        demoFormDialog.show();


    }

    private void showAddCustDialogShow(){
        showAddCustDialog("");
    }

    private void submitToDatabase(){
        String timeStamp = localStorage.getTentID();

        //Constants.tentFormStratImage = localStorage.getStartImage();
        //Constants.tentFormEndImage = localStorage.getEndImage();

        boolean r = false;
        if(iv_check_uncheck_no_product_flg==true){
            r = dataHelperObj.AddTentFormProduct(timeStamp,"No Product","","",""+ed_phone.getText().toString());

        }else {
            for (int i = 0; i < Constants.tentExistingProductList.size(); i++) {
                if (Constants.tentExistingProductList.get(i).getBrand().toString().isEmpty()) {


                } else {
                    r = dataHelperObj.AddTentFormProduct(timeStamp, "" + Constants.tentExistingProductList.get(i).getDate().toString(), "" + Constants.tentExistingProductList.get(i).getBrand().toString(), "" + Constants.tentExistingProductList.get(i).getLife().toString(), "" + ed_phone.getText().toString());

                }
            }
        }
        if(r==true) {
            strt = "end";
            showEndConfirmation("end");
        }
    }


    private void saveToDatabase(String end){

        new GPSTracker(mContext);
        if(!localStorage.getStartImage().isEmpty()) {
            Constants.tentFormStratImage = localStorage.getStartImage();
            //Constants.tentFormEndImage = localStorage.getEndImage();
        }

        String timeStamp = localStorage.getTentID();

            if(!address_text.isEmpty()) {
                if (iv_check_chimney_flg == true) {
                    dataHelperObj.AddTentFormCust(timeStamp, "" + address_text, "" + mobile_c, "Chimney", "" + ed_phone.getText().toString());
                }
                if (iv_check_ro_flg == true) {
                    dataHelperObj.AddTentFormCust(timeStamp, "" + address_text, "" + mobile_c, "RO", "" + ed_phone.getText().toString());
                }
                if (iv_check_other_flg == true) {
                    dataHelperObj.AddTentFormCust(timeStamp, "" + address_text, "" + mobile_c, "Others", "" + ed_phone.getText().toString());
                }
            }



        if(end.matches("end") || end.matches("no")){
            if(Constants.tentExistingProductList.size()>0 || iv_check_uncheck_no_product_flg == true) {

                boolean r = false;

                String datetime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());

                endDateTime = datetime;

                r = dataHelperObj.AddTentFormDetails(""+localStorage.getTentID(), "" + localStorage.getStartTime(), "" + endDateTime, "" + currentLat, "" + currentLong, "" + currentLat, "" + currentLong,
                        "" + Constants.tentFormStratImage, "" + Constants.tentFormEndImage, "" + ed_name.getText().toString(), "" + ed_phone.getText().toString(), "" + demoInterested, "" + tentativeDateTime, "" + ed_remarks.getText().toString());


                if(r==true){

                    dataHelperObj.insertToLocationTable1("TF", ""+localStorage.getTentID());
                }

                if(end.toString().matches("end")){
                    dataHelperObj.UpdateTentFormDetails("",""+localStorage.getEndImage(),""+localStorage.getStartTime());
                }

                if(HTTPUtils.isConnectionPossible(mContext))
                {
                    String order_no = "TF" + Constants.employeeDetailObject.getEmpCode()
                            + ""+localStorage.getTentID();

                    ArrayList<Location> unUploadedTransactionDR = dataHelperObj.getUnuploadedTransactionPropello("tent_form", ""+order_no);
                    TRANS_Tent_Form_TransactionTask sb2 = new TRANS_Tent_Form_TransactionTask(TentFormActivity.this, unUploadedTransactionDR);
                    sb2.execute();
                    new TRANS_TentKnockingAttachmentExportTask(mContext, "tent_form", "2", true,true).execute();
                    //finish();
                    if(end.toString().matches("end")){
                        localStorage.setEndImage("");
                        localStorage.setStartTime("");
                        localStorage.setStartImage("");
                        localStorage.setTentID("");
                    }
                }else {
                    Utils.showToast(mContext, "Data not Saved in server");
                    if(end.toString().matches("end")){
                        localStorage.setEndImage("");
                        localStorage.setStartTime("");
                        localStorage.setStartImage("");
                        localStorage.setTentID("");
                    }
                    finish();
                }
            }else{
                Utils.showToast(mContext, "Please provide tent data");
            }
        }else{

            String datetime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());

            endDateTime = datetime;
            boolean r = false;
            r = dataHelperObj.AddTentFormDetails(""+localStorage.getTentID(), "" + startingDateTime, "" + endDateTime, "" + currentLat, "" + currentLong, "" + currentLat, "" + currentLong,
                    "" + Constants.tentFormStratImage, "" + Constants.tentFormEndImage, "" + ed_name.getText().toString(), "" + ed_phone.getText().toString(), "" + demoInterested, "" + tentativeDateTime, "" + ed_remarks.getText().toString());


            if(r==true){

                dataHelperObj.insertToLocationTable1("TF", ""+localStorage.getTentID());
            }

            if(end.toString().matches("close")){
                dataHelperObj.UpdateTentFormDetails("",""+localStorage.getEndImage(),""+localStorage.getStartTime());
            }

            if(HTTPUtils.isConnectionPossible(mContext))
            {
                String order_no = "TF" + Constants.employeeDetailObject.getEmpCode()
                        + ""+localStorage.getTentID();

                ArrayList<Location> unUploadedTransactionDR = dataHelperObj.getUnuploadedTransactionPropello("tent_form", ""+order_no);
                TRANS_Tent_Form_TransactionTask sb2 = new TRANS_Tent_Form_TransactionTask(TentFormActivity.this, unUploadedTransactionDR);
                sb2.execute();
                new TRANS_TentKnockingAttachmentExportTask(mContext, "tent_form", "2", true,true).execute();
                //finish();
                if(end.toString().matches("close")){
                    localStorage.setEndImage("");
                    localStorage.setStartTime("");
                    localStorage.setStartImage("");
                    localStorage.setTentID("");
                }
            }else {
                Utils.showToast(mContext, "Data Saved");
                if (end.toString().matches("close")) {
                    localStorage.setEndImage("");
                    localStorage.setStartTime("");
                    localStorage.setStartImage("");
                    localStorage.setTentID("");
                }
                finish();

            }

            }



    }

    @Override
    protected void onDestroy() {
        super.onDestroy();

    }

}