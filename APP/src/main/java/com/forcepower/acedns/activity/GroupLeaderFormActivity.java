package com.forcepower.acedns.activity;

import static com.forcepower.acedns.constants.Constants.currentLat;
import static com.forcepower.acedns.constants.Constants.currentLong;

import androidx.fragment.app.FragmentActivity;

import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.TimePickerDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.os.Bundle;
import android.provider.MediaStore;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.TimePicker;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_GroupLeader_Form_TransactionTask;
import com.forcepower.acedns.backgroundTask.TRANS_TentKnockingAttachmentExportTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.GroupLeaderCustomerMobileListAdapter;
import com.forcepower.acedns.adapter.TelecallerCustomerMobileListAdapter;
import com.forcepower.acedns.bean.CommonModel;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class GroupLeaderFormActivity extends FragmentActivity implements DatePickerDialog.OnDateSetListener, TimePickerDialog.OnTimeSetListener{

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase mAceDnsDatabase;

    Button mButtonBack,buttonDate,btn_submitAll,buttonPicTentPhoto,buttonPicGL_morning,buttonPicDemoPhoto,buttonPicNight,buttonFromDate,buttonToDate;

    static final int REQUEST_IMAGE_CAPTURE = 1;
    static final int REQUEST_IMAGE_CAPTURE_MORNING = 11;
    static final int REQUEST_IMAGE_CAPTURE_TENT = 12;
    static final int REQUEST_IMAGE_CAPTURE_DEMO = 13;
    static final int REQUEST_IMAGE_CAPTURE_NIGHT = 14;
    private static String currentPic = "";
    Bitmap customerPicBitmap = null;
    String supportingAttachmentNameForCustomerImage="";
    String imageType="";

    ImageView imageViewGLMorning,imageViewTentPhoto,imageViewNightFieldMeeting,imageViewDemoPhoto,selectedImage;

    String morningImg = "", tentImg = "", demoImg = "",nightImg = "",morningDateTime = "",tentDateTime = "", demoDateTime = "", nightDateTime = "",btnText="",rg_demo="";
    int day, month, year, hour, minute;
    int myday, myMonth, myYear, myHour, myMinute;
    String fDate="",tDate="",custName="",cMobile="";

    RadioGroup rgConnected;

    TextView tvName,tvcMobile,tvDemoDate;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_group_leader_form);

        mContext = GroupLeaderFormActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dataHelperObj = new AceDnsTransactionDatabase(mContext);

        imageViewGLMorning = findViewById(R.id.imageViewGLMorning);
        imageViewTentPhoto = findViewById(R.id.imageViewTentPhoto);
        imageViewNightFieldMeeting = findViewById(R.id.imageViewNightFieldMeeting);
        imageViewDemoPhoto = findViewById(R.id.imageViewDemoPhoto);
        tvName = findViewById(R.id.tvCname);
        tvcMobile = findViewById(R.id.tvcMobile);
        tvDemoDate = findViewById(R.id.tvDemoDate);

        rgConnected = (RadioGroup) findViewById(R.id.rgConnected);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });


        buttonPicGL_morning = (Button) findViewById(R.id.buttonPicGL_morning);
        buttonPicTentPhoto = (Button) findViewById(R.id.buttonPicTentPhoto);
        buttonPicDemoPhoto = (Button) findViewById(R.id.buttonPicDemoPhoto);
        buttonPicNight = (Button) findViewById(R.id.buttonPicNight);
        btn_submitAll = (Button) findViewById(R.id.btn_submitAll);
        buttonFromDate  = (Button) findViewById(R.id.buttonFromDate);
        buttonToDate  = (Button) findViewById(R.id.buttonToDate);

        buttonPicGL_morning.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                if (takePictureIntent.resolveActivity(getPackageManager()) != null)
                {
                    imageType = "morning";
                    startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE_MORNING);
                }
            }
        });

        buttonPicTentPhoto.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                if (takePictureIntent.resolveActivity(getPackageManager()) != null)
                {
                    imageType = "tent";
                    startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE_TENT);
                }
            }
        });

        buttonPicDemoPhoto.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                if (takePictureIntent.resolveActivity(getPackageManager()) != null)
                {
                    imageType = "demo";
                    startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE_DEMO);
                }
            }
        });

        buttonPicNight.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                if (takePictureIntent.resolveActivity(getPackageManager()) != null)
                {
                    imageType = "night";
                    startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE_NIGHT);
                }
            }
        });

        btn_submitAll.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                if(!morningImg.isEmpty()){

                }
                if(!tvName.getText().toString().isEmpty()) {
                    custName = tvName.getText().toString();
                    cMobile = tvcMobile.getText().toString();
                    saveToDatabase();
                }else{
                    Utils.showToast(mContext,"Please Select a Customer");
                }
            }
        });


        buttonFromDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                btnText = "fromdate";
                Calendar calendar = Calendar.getInstance();
                year = calendar.get(Calendar.YEAR);
                month = calendar.get(Calendar.MONTH);
                day = calendar.get(Calendar.DAY_OF_MONTH);
                DatePickerDialog datePickerDialog = new DatePickerDialog(GroupLeaderFormActivity.this, (DatePickerDialog.OnDateSetListener) GroupLeaderFormActivity.this,year, month,day);
                datePickerDialog.show();

            }
        });

        buttonToDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                btnText = "todate";
                Calendar calendar = Calendar.getInstance();
                year = calendar.get(Calendar.YEAR);
                month = calendar.get(Calendar.MONTH);
                day = calendar.get(Calendar.DAY_OF_MONTH);
                DatePickerDialog datePickerDialog = new DatePickerDialog(GroupLeaderFormActivity.this, (DatePickerDialog.OnDateSetListener) GroupLeaderFormActivity.this,year, month,day);
                datePickerDialog.show();

            }
        });


        rgConnected.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();
                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){
                        rg_demo = "yes";
                    }else{
                        rg_demo = "no";
                    }
                }
            }
        });

    }

    private void storeImageInLocalStorageShowOnImageView(Bitmap customerPicBitmap, Boolean ShowImageOnUi, String imagePath, String imageTypeimg)
            throws FileNotFoundException {
        File outputFile = null;
        outputFile = new File(imagePath);
        if (outputFile.exists())
            outputFile.delete();
        FileOutputStream out = null;
        out = new FileOutputStream(outputFile);
        customerPicBitmap.compress(Bitmap.CompressFormat.JPEG, 100, out);
        if(imageType.matches("morning"))
        {
            imageViewGLMorning.setVisibility(View.VISIBLE);
            imageViewGLMorning.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 100, 100));

        }else if(imageType.matches("tent"))
        {
            imageViewTentPhoto.setVisibility(View.VISIBLE);
            imageViewTentPhoto.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 100, 100));

        }
        else if(imageType.matches("demo"))
        {
            imageViewDemoPhoto.setVisibility(View.VISIBLE);
            imageViewDemoPhoto.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 100, 100));

        }else if(imageType.matches("night"))
        {
            imageViewNightFieldMeeting.setVisibility(View.VISIBLE);
            imageViewNightFieldMeeting.setImageBitmap(Utils.getResizedBitmap(customerPicBitmap, 100, 100));

        }


    }


    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        //Utils.showToast(mContext,resultCode+"");

        if (requestCode == REQUEST_IMAGE_CAPTURE)
        {
            if (resultCode == RESULT_OK) {
                if (data != null) {



                } else {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.3", Toast.LENGTH_SHORT).show();
                }

            } else {
//                isUserPicTaken = false;
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.4", Toast.LENGTH_SHORT).show();
            }

        }else if (requestCode == REQUEST_IMAGE_CAPTURE_MORNING)
        {
            try {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();

                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                Bundle extras = data.getExtras();

                if(currentPic.matches("")){
                    customerPicBitmap = (Bitmap) extras.get("data");
                    supportingAttachmentNameForCustomerImage = "GL" + Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                    String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                    storeImageInLocalStorageShowOnImageView(customerPicBitmap,true,imagePath,imageType);
                    morningImg = supportingAttachmentNameForCustomerImage;
                    dataHelperObj.insertToSupportingAttachTable(""+supportingAttachmentNameForCustomerImage, "gl_form");
                }

            } catch (Exception e) {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.."+e.toString(), Toast.LENGTH_SHORT).show();
            }

            morningDateTime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());

        }else if (requestCode == REQUEST_IMAGE_CAPTURE_TENT)
        {
            try {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                Bundle extras = data.getExtras();
                if(currentPic.matches("")){
                    customerPicBitmap = (Bitmap) extras.get("data");
                    supportingAttachmentNameForCustomerImage = "GL" + Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                    String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                    storeImageInLocalStorageShowOnImageView(customerPicBitmap,true,imagePath,imageType);
                    tentImg = supportingAttachmentNameForCustomerImage;
                    dataHelperObj.insertToSupportingAttachTable(""+supportingAttachmentNameForCustomerImage, "gl_form");
                }

            } catch (Exception e) {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.."+e.toString(), Toast.LENGTH_SHORT).show();
            }

            tentDateTime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());

        }else if (requestCode == REQUEST_IMAGE_CAPTURE_DEMO)
        {
            try {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                Bundle extras = data.getExtras();
                if(currentPic.matches("")){
                    customerPicBitmap = (Bitmap) extras.get("data");
                    supportingAttachmentNameForCustomerImage = "GL" + Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                    String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                    storeImageInLocalStorageShowOnImageView(customerPicBitmap,true,imagePath,imageType);
                    demoImg = supportingAttachmentNameForCustomerImage;
                    dataHelperObj.insertToSupportingAttachTable(""+supportingAttachmentNameForCustomerImage, "gl_form");
                }

            } catch (Exception e) {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.."+e.toString(), Toast.LENGTH_SHORT).show();
            }

            demoDateTime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());

        }else if (requestCode == REQUEST_IMAGE_CAPTURE_NIGHT)
        {
            try {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                Bundle extras = data.getExtras();
                if(currentPic.matches("")){
                    customerPicBitmap = (Bitmap) extras.get("data");
                    supportingAttachmentNameForCustomerImage = "GL" + Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                    String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                    storeImageInLocalStorageShowOnImageView(customerPicBitmap,true,imagePath,imageType);
                    nightImg = supportingAttachmentNameForCustomerImage;
                    dataHelperObj.insertToSupportingAttachTable(""+supportingAttachmentNameForCustomerImage, "gl_form");
                }

            } catch (Exception e) {

                Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.."+e.toString(), Toast.LENGTH_SHORT).show();
            }

            nightDateTime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());

        }

        super.onActivityResult(requestCode, resultCode, data);
    }

    private void saveToDatabase(){

        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        new GPSTracker(mContext);
        if(!morningImg.isEmpty() || !demoImg.isEmpty() || !nightImg.isEmpty() || !tentImg.isEmpty()) {

            boolean r = false;

            String datetime =  new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().getTime());

           /* r = dataHelperObj.AddGroupLeaderFormDetails(timeStamp, ""+custName ,""+cMobile,""+rg_demo, "" +morningImg, "" + morningDateTime, "" + currentLat, "" + currentLong, "" + tentImg,
                    "" +tentDateTime, "" + currentLat, ""+currentLat , ""+currentLong , ""+demoImg , ""+demoDateTime , ""+currentLong,""+nightImg,""+nightDateTime,""+currentLat,""+currentLong,""+Constants.employeeDetailObject.getEmpCode());

*/
            r = dataHelperObj.AddGroupLeaderFormDetails(timeStamp, "" ,"","", "" +morningImg, "" + morningDateTime, "" + currentLat, "" + currentLong, "" + tentImg,
                    "" +tentDateTime, "" + currentLat, ""+currentLat , ""+currentLong , ""+demoImg , ""+demoDateTime , ""+currentLong,""+nightImg,""+nightDateTime,""+currentLat,""+currentLong,""+Constants.employeeDetailObject.getEmpCode());


            if(r==true){

                dataHelperObj.insertToLocationTable1("GL", timeStamp);
            }

            if(HTTPUtils.isConnectionPossible(mContext))
            {
                String order_no = "GL" + Constants.employeeDetailObject.getEmpCode()
                        + timeStamp;

                ArrayList<Location> unUploadedTransactionDR = dataHelperObj.getUnuploadedTransactionPropello("GL_form", ""+order_no);
                TRANS_GroupLeader_Form_TransactionTask sb2 = new TRANS_GroupLeader_Form_TransactionTask(GroupLeaderFormActivity.this, unUploadedTransactionDR);
                sb2.execute();
                new TRANS_TentKnockingAttachmentExportTask(mContext, "gl_form", "", true,true).execute();
                //finish();

            }else {
                Utils.showToast(mContext, "Data Saved");
                finish();
            }
        }else{
            Utils.showToast(mContext, "Please add minimum one");
        }

    }


    @Override
    public void onDateSet(DatePicker view, int year, int month, int dayOfMonth) {
        myYear = year;
        myday = dayOfMonth;
        myMonth = month+1;
        String dateTime = myYear+"-"+myMonth+"-"+myday;
        String chosenFromDate ;
        chosenFromDate =  new SimpleDateFormat("dd/MM/yyyy").format(Calendar.getInstance().getTime());
        String chosenDate = (dayOfMonth+1) + "/" + (month + 1) + "/" + year;
        //Utils.showToast(mContext,"Chochen" +chosenDate +" date" + chosenFromDate);
        //if(Utils.checkDatePick(chosenDate,chosenFromDate,"dd/MM/yyyy"))
        //{
            if(btnText.matches("fromdate")) {
                fDate = dateTime;
                buttonFromDate.setText(dateTime);
            }
            if(btnText.matches("todate")) {
                tDate = dateTime;
                buttonToDate.setText(dateTime);
                showNumberSelection(fDate,tDate);
            }
        /*}else
        {
            Utils.showToast(mContext,"Date Must Be Grater Than From today");
        }*/
    }

    @Override
    public void onTimeSet(TimePicker view, int hourOfDay, int minute) {

    }

    ArrayList<CommonModel> expList1Temp = new ArrayList<CommonModel>();
    private void getCustomerDetails(String fdatee,String tdate){

        final ArrayList<CommonModel> expList1 = dataHelperObj.getCustomerGroupLeaderForm(fdatee,tdate);
        expList1Temp.addAll(expList1);
        ListView dialogList = (ListView) findViewById(R.id.prodQtyRateListView);
        GroupLeaderCustomerMobileListAdapter adapter1 = new GroupLeaderCustomerMobileListAdapter(GroupLeaderFormActivity.this, expList1);
        dialogList.setAdapter(adapter1);

        setListViewHeightBasedOnItems(dialogList);

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

                //Utils.showToast(mContext,""+adapter1.getItem(position));
                CommonModel cm = adapter1.getItem(position);

               // modelTemp.get(position).setCom2(cm.getCom2());

                custName = cm.getCom1();
                cMobile = cm.getCom2();
                //ImageView selectedImagec = (ImageView) view.findViewById(R.id.iv_check_uncheck_no_product);
                for(int i=0; i<parent.getChildCount(); i++)
                {
                    if(i == position)
                    {
                        parent.getChildAt(i).setBackgroundColor(Color.LTGRAY);
                        //ImageView img =  view.findViewById(R.id.iv_check_uncheck_no_product);
                        //img.setBackgroundResource(R.id.uncheck);
                        expList1.get(position).setCom4("1");
                        //selectedImagec.setImageResource(R.drawable.check);
                    }
                    else
                    {
                        expList1.get(position).setCom4("0");
                        parent.getChildAt(i).setBackgroundColor(Color.TRANSPARENT);
                        //selectedImagec.setImageResource(R.drawable.uncheck);
                    }

                }
                adapter1.notifyDataSetChanged();
                //adapter1.setFilter(expList1);
                //Utils.showToast(mContext,""+custName);
            }
        });

        //Log.d("existProduct", expList1);
    }

    public static boolean setListViewHeightBasedOnItems(ListView listView) {

        ListAdapter listAdapter = listView.getAdapter();
        if (listAdapter != null) {

            int numberOfItems = listAdapter.getCount();

            // Get total height of all items.
            int totalItemsHeight = 0;
            for (int itemPos = 0; itemPos < numberOfItems; itemPos++) {
                View item = listAdapter.getView(itemPos, null, listView);
                float px = 500 * (listView.getResources().getDisplayMetrics().density);
                item.measure(View.MeasureSpec.makeMeasureSpec((int)px, View.MeasureSpec.AT_MOST), View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED));
                totalItemsHeight += item.getMeasuredHeight();
            }

            // Get total height of all item dividers.
            int totalDividersHeight = listView.getDividerHeight() *
                    (numberOfItems - 1);
            // Get padding
            int totalPadding = listView.getPaddingTop() + listView.getPaddingBottom();

            // Set list height.
            ViewGroup.LayoutParams params = listView.getLayoutParams();
            params.height = totalItemsHeight;// + totalDividersHeight + totalPadding;
            listView.setLayoutParams(params);
            listView.requestLayout();
            return true;

        } else {
            return false;
        }

    }

    ArrayList<CommonModel> expList2 = null;
    ArrayList<CommonModel> expList1 = null;
    CommonModel commonModel = null;
    public void showNumberSelection(String fdatee,String tdate){

        Dialog demoFormDialog;
        demoFormDialog = new Dialog(GroupLeaderFormActivity.this, R.style.PauseDialogTent);
        demoFormDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        demoFormDialog.setContentView(R.layout.select_tent_form_mobile);
        demoFormDialog.setCancelable(false);
        TextView title = (TextView) demoFormDialog.findViewById(R.id.title);
        title.setText("Please Select");

        //imgPic = (ImageView) demoFormDialog.findViewById(R.id.autoCompleteTextView1);

        Button buttonPic = (Button) demoFormDialog.findViewById(R.id.btn_submit);
        buttonPic.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {

            }
        });

        ImageView cancel = (ImageView) demoFormDialog.findViewById(R.id.image_cancel);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {

                demoFormDialog.cancel();

            }
        });

        expList1 = dataHelperObj.getCustomerGroupLeaderForm(fdatee,tdate);
        expList2 = new ArrayList<CommonModel>();
        expList2.addAll(expList1);

        ListView listViewNumber = (ListView) demoFormDialog.findViewById(R.id.listViewNumberFor);
        TelecallerCustomerMobileListAdapter adapter1 = new TelecallerCustomerMobileListAdapter(GroupLeaderFormActivity.this, expList1);
        listViewNumber.setAdapter(adapter1);

        //setListViewHeightBasedOnItems(listViewNumber);


        final EditText autoCompleteTextView1 = (EditText) demoFormDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.VISIBLE);
        autoCompleteTextView1.addTextChangedListener(new TextWatcher() {

            public void afterTextChanged(Editable s) {
                //RoutePlanAdapter.getFilter().filter(s.toString());
            }

            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            public void onTextChanged(CharSequence s, int start, int before, int count) {

                String searchString = autoCompleteTextView1.getText().toString();
                int textLength = searchString.length();

                if(searchString.isEmpty()){
                    expList1.clear();
                    expList1.addAll(expList2);

                    adapter1.notifyDataSetChanged();
                }else {
                    expList1.clear();
                    for (int i = 0; i < expList2.size(); i++) {
                        if(isNumeric(searchString)){
                            String routeName = expList2.get(i).getCom2(); // it should be 'provider'..because we are use common code from Taxonomy
                            if (textLength <= routeName.length()) {
                                if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                    expList1.add(expList2.get(i));
                                }
                            }
                        }else {
                            String routeName = expList2.get(i).getCom1(); // it should be 'provider'..because we are use common code from Taxonomy
                            if (textLength <= routeName.length()) {
                                if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                    expList1.add(expList2.get(i));
                                }
                            }
                        }
                    }
                    adapter1.notifyDataSetChanged();
                }
            }
        });


        listViewNumber.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                demoFormDialog.cancel();
                commonModel = expList1.get(position);
                tvName.setText(commonModel.getCom1());
                tvcMobile.setText(commonModel.getCom2());
                tvDemoDate.setText(commonModel.getCom3());
            }
        });


        demoFormDialog.show();

    }


    public static boolean isNumeric(String string) {
        int intValue;

        System.out.println(String.format("Parsing string: \"%s\"", string));

        if(string == null || string.equals("")) {
            System.out.println("String cannot be parsed, it is null or empty.");
            return false;
        }

        try {
            intValue = Integer.parseInt(string);
            //return true;
            return false;
        } catch (NumberFormatException e) {
            System.out.println("Input String cannot be parsed to Integer.");
        }
        return false;
    }

}