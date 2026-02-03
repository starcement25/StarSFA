package com.forcepower.acedns.activity;

import androidx.fragment.app.FragmentActivity;

import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.TimePickerDialog;
import android.content.Context;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.text.format.DateFormat;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.TimePicker;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_Telecaller_Form_TransactionTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.TelecallerCustomerMobileListAdapter;
import com.forcepower.acedns.adapter.TelecallerExixtingProductListAdapter;
import com.forcepower.acedns.api.clients.RestClient;
import com.forcepower.acedns.bean.CommonModel;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.TentFormDetails;
import com.forcepower.acedns.bean.TentProductList;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.Utils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class TelecallerActivity extends FragmentActivity implements DatePickerDialog.OnDateSetListener, TimePickerDialog.OnTimeSetListener{

    String[] connected = {"", "Switch off", "Ringing",
            "Wrong number", "Not reachable","Disconnected" };
    String[] alloca = {"", "GL", "Executive",
            "Business", "Owner","Area head" };


    LinearLayout llDemo,llReasonNo,llConnectYes,llDemoAchieveYes,llServiceInterestedYes,llServiceInterested,llDemoAchieveNO;
    Spinner spinnerConnected,spinnerAllocationTo;
    RadioGroup rgConnected,rgDemoAppTeken,rgIterestedForService;
    Button mButtonBack,buttonDate,btn_submit,buttonServiceDate,buttonDemoAppoinDate,buttonNumberList;

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase mAceDnsDatabase;

    int day, month, year, hour, minute;
    int myday, myMonth, myYear, myHour, myMinute;
    String tentFormId,rg_connected="",saleAcheived="",bookingDone="",demoAcheived="",payMood="",exchange="",bookingPayMode="",mobileNo="";
    EditText ed_name,ed_phone,ed_remarks,ed_capture_service_type,ed_amount_tobe_collected,ed_sales_name,ed_data_source,ed_data_source_date;
    Spinner spinnerMobile;
    String buttonText="",demoDate="",apDate="",serviceDate="",serviceInterested = "",
            next_appointment_date_time = "",existingProduct = "";

    TentFormDetails tempProduct=new TentFormDetails();

    ImageView imgPic,iv_check_chimney,iv_check_ro,iv_check_other,iv_check_uncheck_no_product;

    boolean iv_check_chimney_flg = false,iv_check_ro_flg = false,iv_check_chimney_ro_flg = false,iv_check_other_flg = false,iv_check_uncheck_no_product_flg = false,address_flg = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_telecaller);

        spinnerConnected = findViewById(R.id.spinnerdemoby);
        spinnerMobile = findViewById(R.id.spinnernumber);
        spinnerAllocationTo = findViewById(R.id.spinnerAllocationTo);
        rgConnected = (RadioGroup) findViewById(R.id.rgConnected);
        rgDemoAppTeken = (RadioGroup) findViewById(R.id.rgDemoAppTeken);
        rgIterestedForService = (RadioGroup) findViewById(R.id.rgIterestedForService);
        llReasonNo = (LinearLayout) findViewById(R.id.llReasonNo);
        llConnectYes = (LinearLayout) findViewById(R.id.llConnectYes);
        llDemoAchieveYes = (LinearLayout) findViewById(R.id.llDemoAchieveYes);
        llServiceInterestedYes =  (LinearLayout) findViewById(R.id.llServiceInterestedYes);
        llDemoAchieveNO  =  (LinearLayout) findViewById(R.id.llDemoAchieveNO);
        llServiceInterested  =  (LinearLayout) findViewById(R.id.llServiceInterested);
        mButtonBack = (Button) findViewById(R.id.back);
        buttonServiceDate = (Button) findViewById(R.id.buttonServiceDate);
        buttonDate =  (Button) findViewById(R.id.buttonDate);
        btn_submit =  (Button) findViewById(R.id.btn_submit);
        buttonNumberList = (Button) findViewById(R.id.buttonNumberList);
        buttonDemoAppoinDate =  (Button) findViewById(R.id.buttonDemoAppoinDate);
        ed_name = findViewById(R.id.ed_name);

        ed_phone = findViewById(R.id.ed_phone);
        ed_sales_name = findViewById(R.id.ed_sales_name);
        ed_data_source = findViewById(R.id.ed_data_source);
        ed_data_source_date = findViewById(R.id.ed_data_source_date);
        ed_remarks = findViewById(R.id.ed_remarks);
        ed_capture_service_type = findViewById(R.id.ed_capture_service_type);
        ed_amount_tobe_collected = findViewById(R.id.ed_amount_tobe_collected);

        iv_check_chimney = findViewById(R.id.iv_check_chimney);
        iv_check_ro = findViewById(R.id.iv_check_ro);
        iv_check_other = findViewById(R.id.iv_check_other);

        mContext = TelecallerActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dataHelperObj = new AceDnsTransactionDatabase(mContext);

        ArrayAdapter ad
                = new ArrayAdapter(
                TelecallerActivity.this,
                android.R.layout.simple_spinner_item,
                connected);
        ad.setDropDownViewResource(
                android.R.layout
                        .simple_spinner_dropdown_item);

        spinnerConnected.setAdapter(ad);

        ArrayAdapter adall
                = new ArrayAdapter(
                TelecallerActivity.this,
                android.R.layout.simple_spinner_item,
                alloca);
        adall.setDropDownViewResource(
                android.R.layout
                        .simple_spinner_dropdown_item);

        spinnerAllocationTo.setAdapter(adall);



        rgConnected.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();
                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){
                        llReasonNo.setVisibility(View.GONE);
                        llConnectYes.setVisibility(View.VISIBLE);
                        rg_connected = "yes";
                    }else{
                        rg_connected = "no";
                        llConnectYes.setVisibility(View.GONE);
                        llReasonNo.setVisibility(View.VISIBLE);
                    }
                }
            }
        });

        rgDemoAppTeken.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();
                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){
                        demoAcheived = "yes";
                        llDemoAchieveYes.setVisibility(View.VISIBLE);
                        llDemoAchieveNO.setVisibility(View.GONE);
                    }else{
                        demoAcheived = "no";
                        llDemoAchieveYes.setVisibility(View.GONE);
                        llDemoAchieveNO.setVisibility(View.VISIBLE);

                    }
                }
            }
        });

        rgIterestedForService.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();
                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){
                        serviceInterested = "yes";
                        llServiceInterestedYes.setVisibility(View.VISIBLE);
                    }else{
                        serviceInterested = "no";
                        llServiceInterestedYes.setVisibility(View.GONE);
                    }
                }
            }
        });

        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        Calendar cal = Calendar.getInstance();
        cal.add(Calendar.MONTH,3);
        String timeStamp = new SimpleDateFormat("yyyy-MM-dd").format(cal.getTime());
        //cal.getTime();
        buttonServiceDate.setText(""+timeStamp);
        serviceDate = timeStamp;

        buttonDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                buttonText = "date";
                Calendar calendar = Calendar.getInstance();
                year = calendar.get(Calendar.YEAR);
                month = calendar.get(Calendar.MONTH);
                day = calendar.get(Calendar.DAY_OF_MONTH);
                DatePickerDialog datePickerDialog = new DatePickerDialog(TelecallerActivity.this, (DatePickerDialog.OnDateSetListener) TelecallerActivity.this,year, month,day);
                datePickerDialog.show();

            }
        });

        buttonDemoAppoinDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                buttonText = "demo";
                Calendar calendar = Calendar.getInstance();
                year = calendar.get(Calendar.YEAR);
                month = calendar.get(Calendar.MONTH);
                day = calendar.get(Calendar.DAY_OF_MONTH);
                DatePickerDialog datePickerDialog = new DatePickerDialog(TelecallerActivity.this, (DatePickerDialog.OnDateSetListener) TelecallerActivity.this,year, month,day);
                datePickerDialog.show();

            }
        });

        buttonServiceDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                buttonText = "due";
                Calendar calendar = Calendar.getInstance();
                year = calendar.get(Calendar.YEAR);
                month = calendar.get(Calendar.MONTH);
                day = calendar.get(Calendar.DAY_OF_MONTH);
                DatePickerDialog datePickerDialog = new DatePickerDialog(TelecallerActivity.this, (DatePickerDialog.OnDateSetListener) TelecallerActivity.this,year, month,day);
                datePickerDialog.show();

            }
        });

        btn_submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                saveToDatabase();

            }
        });

        buttonNumberList.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                //showNumberSelection();


                propTelecaller_form_data(mContext);



            }
        });


        setModelSpinner();

        new GPSTracker(mContext);


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



    }

    @Override
    public void onDateSet(DatePicker view, int year, int month, int dayOfMonth) {
        myYear = year;
        myday = dayOfMonth;
        myMonth = month+1;
        demoDate = "";
        String dateTime = myYear+"-"+myMonth+"-"+myday;
        String chosenFromDate ;
        chosenFromDate =  new SimpleDateFormat("dd/MM/yyyy").format(Calendar.getInstance().getTime());
        String chosenDate = (dayOfMonth+1) + "/" + (month + 1) + "/" + year;
        //Utils.showToast(mContext,"Chochen" +chosenDate +" date" + chosenFromDate);
        String dateTimeForList = myday+"-"+myMonth+"-"+myYear;
        if(Utils.checkDatePick(chosenDate,chosenFromDate,"dd/MM/yyyy"))
        {
            if(buttonText.matches("date")) {
                buttonDate.setText(dateTime);
                apDate=dateTime;
            }else if(buttonText.matches("due")) {
                serviceDate=dateTime;
                buttonServiceDate.setText(dateTime);
                //TimePickerDialog timePickerDialog = new TimePickerDialog(TelecallerActivity.this, (TimePickerDialog.OnTimeSetListener) TelecallerActivity.this, hour, minute, DateFormat.is24HourFormat(this));
                //timePickerDialog.show();
            }else if(buttonText.matches("demo")) {
                demoDate=dateTime;
                buttonDemoAppoinDate.setText(dateTime);
                TimePickerDialog timePickerDialog = new TimePickerDialog(TelecallerActivity.this, (TimePickerDialog.OnTimeSetListener) TelecallerActivity.this, hour, minute, DateFormat.is24HourFormat(this));
                timePickerDialog.show();
            }else  if(btnText.matches("fromdate")) {
                dateTimeForList = (myday-1)+"-"+myMonth+"-"+myYear;
                fDate = dateTimeForList;
                buttonFromDate.setText(dateTime);
            }
            else if(btnText.matches("todate")) {
                dateTimeForList = (myday+1)+"-"+myMonth+"-"+myYear;
                tDate = dateTimeForList;
                buttonToDate.setText(dateTime);
                //DateRangeList(fDate,tDate);
            }
            //TimePickerDialog timePickerDialog = new TimePickerDialog(DemoFormActivity.this, (TimePickerDialog.OnTimeSetListener) DemoFormActivity.this, hour, minute, DateFormat.is24HourFormat(this));
            //timePickerDialog.show();
        }
        else
        {
            Utils.showToast(mContext,"Date Must Be Grater Than From today");
        }

    }

    @Override
    public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
        if(buttonText.matches("demo")) {
            if(hourOfDay<=9) {
                //demoDate = demoDate + " 0" + hourOfDay + ":0" + minute + ":00";
                if(minute<=9) {
                    demoDate = demoDate + " 0" + hourOfDay + ":0" + minute + ":00";
                }else{
                    demoDate = demoDate + " 0" + hourOfDay + ":" + minute + ":00";
                }
            } else{
                //demoDate = demoDate + " " + hourOfDay + ":" + minute + ":00";
                if(minute<=9) {
                    demoDate = demoDate + " " + hourOfDay + ":0" + minute + ":00";
                }else{
                    demoDate = demoDate + " " + hourOfDay + ":" + minute + ":00";
                }
            }
            buttonDemoAppoinDate.setText(demoDate);
        }
    }


    private void saveToDatabase(){
        tentFormId = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

        boolean isTransactionIDExist = false;
        do {
            isTransactionIDExist = dataHelperObj.IsTransactioIdExist(tentFormId);
            if (true == isTransactionIDExist) {
                tentFormId = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            }
        }
        while (true == isTransactionIDExist);

        String timeStamp = tentFormId;
        //new GPSTracker(mContext);

        String product = "";

        if(iv_check_chimney_flg==true){
          product = product + "Chimney,";
        }
        if(iv_check_ro_flg==true){
            product = product + "RO,";
        }
        if(iv_check_other_flg==true){
            product = product + "other";
        }



        if(!buttonNumberList.getText().toString().matches("Select Number")) {

            boolean r = false;

            if(rg_connected.matches("no")){
                r = dataHelperObj.AddTelecallerFormDetails(tentFormId,"","" + ed_name.getText().toString(), "" + mobileNo,
                        "" + rg_connected, "" + spinnerConnected.getSelectedItem().toString(),""+existingProduct,"","","","","","","","","","",""+ Constants.employeeDetailObject.getEmpCode(),"");
            }else {

                if(serviceInterested.matches("no")){

                }
                r = dataHelperObj.AddTelecallerFormDetails(tentFormId, "", "" + ed_name.getText().toString(), "" + mobileNo,
                        "" + rg_connected, "", "" + existingProduct,
                        "" + demoAcheived, "" + demoDate,
                        "" + spinnerAllocationTo.getSelectedItem().toString(), "" + serviceInterested, "" + serviceDate,
                        "" + ed_capture_service_type.getText().toString(), "" + ed_amount_tobe_collected.getText().toString(),
                        "" + serviceDate,
                        "" + apDate, "" + ed_remarks.getText().toString(), "" + Constants.employeeDetailObject.getEmpCode(),""+product);


                /*r = dataHelperObj.AddTelecallerFormDetails(tentFormId,"","" + ed_name.getText().toString(), "" + mobileNo.toString(),
                        "" + rg_connected, "" + spinnerConnected.getSelectedItem().toString(),"",
                        "","","","","","",
                        "","","","","");*/

            }
            if(r==true){

                dataHelperObj.insertToLocationTable1("TC", timeStamp);
            }

            if(HTTPUtils.isConnectionPossible(mContext))
            {
                String order_no = "TC" + Constants.employeeDetailObject.getEmpCode()
                        + timeStamp;
                ArrayList<Location> unUploadedTransactionDR = dataHelperObj.getUnuploadedTransactionPropello("telecaller_form", ""+order_no);
                TRANS_Telecaller_Form_TransactionTask sb2 = new TRANS_Telecaller_Form_TransactionTask(TelecallerActivity.this, unUploadedTransactionDR);
                sb2.execute();

                finish();

            }else {
                Utils.showToast(mContext, "Data Saved");
                finish();
            }
        }else{
            Utils.showToast(mContext, "Please add Phone Number");
        }

    }

    private void setModelSpinner(){

        List<String> model = dataHelperObj.getMobileTelecallerForm("");
        List<String> model1 = new ArrayList<>();
        List<String> model2 = new ArrayList<>();
        //Utils.showToast(mContext,""+model.size());
        for(int i=0;i<model.size();i++){
            String[] arrayOfData = model.get(i).toString().split("-");
            model1.add(""+arrayOfData[2]);
            model2.add(""+arrayOfData[0]+"-"+arrayOfData[1]);
        }

        if(model2.size()>0) {
            ArrayAdapter<String> adpay
                    = new ArrayAdapter<String>(
                    TelecallerActivity.this,
                    android.R.layout.simple_spinner_item,
                    model2);
            adpay.setDropDownViewResource(
                    android.R.layout
                            .simple_spinner_dropdown_item);

            spinnerMobile.setAdapter(adpay);


            spinnerMobile.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                    //Object item = parent.getItemAtPosition(position);
                    String name="";
                    if(spinnerMobile.getSelectedItem().toString().contains("-")){
                        String[] arrayOfData = spinnerMobile.getSelectedItem().toString().split("-");
                        mobileNo = arrayOfData[0];
                        name = arrayOfData[1];
                    }
                    //String name = dataHelperObj.getNameTeleForm(""+mobileNo);
                    //Utils.showToast(mContext,model1.get(position));
                    setExistingProduct(model1.get(position),mobileNo);

                    ed_name.setText(""+name);

                }
                public void onNothingSelected(AdapterView<?> parent) {
                }
            });
        }
    }

    private void setExistingProduct(String i,String mobile) {
        existingProduct = "";
        mobileNo = mobile;
        final ArrayList<TentProductList> expList1Temp = new ArrayList<>();
        try{
            //JSONArray array = new JSONArray(i);
            JSONArray productArray  =new JSONArray(i);
            for (int ii = 0; ii < productArray.length(); ii++) {
                JSONObject pobj = productArray.getJSONObject(ii);

                TentProductList tp = new TentProductList();
                tp.setDate(pobj.getString("product"));
                tp.setBrand(pobj.getString("brand"));
                tp.setLife(pobj.getString("life_of_product"));
                expList1Temp.add(tp);

                ;
            }
        //JSONObject obj = new JSONObject(i);
            Log.d("json_p", i.toString() + "");
        }catch (JSONException e){
            Log.d("ex", e.toString() + "");
        }
       /**/


        final ArrayList<TentProductList> expList1 = expList1Temp;//dataHelperObj.getExistingProductTelecallerForm(i,mobile);
        ListView dialogList = (ListView) findViewById(R.id.prodQtyRateListView);
        TelecallerExixtingProductListAdapter adapter1 = new TelecallerExixtingProductListAdapter(TelecallerActivity.this, expList1);
        dialogList.setAdapter(adapter1);
        setListViewHeightBasedOnItems(dialogList);
        if(!expList1.isEmpty()){
            if(expList1.get(0).getDate().matches("No Product")){
                existingProduct = expList1.get(0).getDate() ;
            }else{
                for(int ii=0;ii<expList1.size();ii++) {
                    existingProduct = existingProduct + "" + expList1.get(ii).getDate() + "#"+ expList1.get(ii).getBrand()+ "#"+ expList1.get(ii).getLife();
                    if((ii+1)== expList1.size()){

                    }else{
                        existingProduct = existingProduct + ",";
                    }
                }
            }

        }
        Log.d("existProduct", existingProduct);
    }


    ArrayList<CommonModel> expList2 = null;
    ArrayList<CommonModel> expList1 = null;
    CommonModel commonModel = null;
    Button buttonFromDate;
    Button buttonToDate;
    String btnText="";
    String fDate="",tDate="";
    public void showNumberSelection(){

        Dialog demoFormDialog;
        demoFormDialog = new Dialog(TelecallerActivity.this, R.style.PauseDialogTent);
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

        expList1 =new ArrayList<>();// dataHelperObj.getMobileCustTelecallerForm("KFE000220220513153811");


        if(!Constants.telecallerFormCust.isEmpty()) {
            for(int j=0;j<Constants.telecallerFormCust.size();j++){

                CommonModel cm = new CommonModel();
                cm.setCom1(Constants.telecallerFormCust.get(j).getMobile_no());
                cm.setCom2(Constants.telecallerFormCust.get(j).getCustomer_name());
                cm.setCom5(Constants.telecallerFormCust.get(j).getProductInterested());
                cm.setCom4(Constants.telecallerFormCust.get(j).getEmp_name());
                cm.setCom6(Constants.telecallerFormCust.get(j).getEnd_date_time());
                cm.setCom7(Constants.telecallerFormCust.get(j).getSource_type());
                cm.setCom11(Constants.telecallerFormCust.get(j).getDemo_tentative_date_time());
                cm.setCom3("");

                expList1.add(cm);

            }
        }


        expList2 = new ArrayList<CommonModel>();
        expList2.addAll(expList1);

        ListView listViewNumber = (ListView) demoFormDialog.findViewById(R.id.listViewNumberFor);
        TelecallerCustomerMobileListAdapter adapter1 = new TelecallerCustomerMobileListAdapter(TelecallerActivity.this, expList1);
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
                        String routeName = expList2.get(i).getCom1(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                expList1.add(expList2.get(i));
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
                buttonNumberList.setText(commonModel.getCom1());
                ed_name.setText(commonModel.getCom2());
                ed_sales_name.setText(""+commonModel.getCom4());
                ed_data_source.setText(""+commonModel.getCom7());
                ed_data_source_date.setText(""+commonModel.getCom6());
                setExistingProduct(commonModel.getCom5(),commonModel.getCom1());
                //tempProduct = commonModel.getCom5();
            }
        });


        buttonFromDate  = (Button) demoFormDialog.findViewById(R.id.buttonFromDate);
        buttonToDate  = (Button) demoFormDialog.findViewById(R.id.buttonToDate);
        Button buttonToDateSearch = (Button) demoFormDialog.findViewById(R.id.buttonToDateSearch);

        buttonToDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                btnText = "todate";
                Calendar calendar = Calendar.getInstance();
                year = calendar.get(Calendar.YEAR);
                month = calendar.get(Calendar.MONTH);
                day = calendar.get(Calendar.DAY_OF_MONTH);
                DatePickerDialog datePickerDialog = new DatePickerDialog(TelecallerActivity.this, (DatePickerDialog.OnDateSetListener) TelecallerActivity.this,year, month,day);
                datePickerDialog.show();

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
                DatePickerDialog datePickerDialog = new DatePickerDialog(TelecallerActivity.this, (DatePickerDialog.OnDateSetListener) TelecallerActivity.this,year, month,day);
                datePickerDialog.show();

            }
        });

        buttonToDateSearch.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
               /* expList1.clear();
                Date df = new Date(),dt = new Date(),demoDate = new Date();
                SimpleDateFormat sdf = new SimpleDateFormat("dd-MM-yyyy");
                for (int i = 0; i < expList2.size(); i++) {
                    String routeName = expList2.get(i).getCom11(); // it should be 'provider'..because we are use common code from Taxonomy

                    String substr=routeName.substring(0,10);
                    try {
                        demoDate = sdf.parse(substr);
                        df = sdf.parse(fDate);
                        dt = sdf.parse(tDate);
                    } catch (ParseException e) {
                        e.printStackTrace();
                    }
                    //Toast.makeText(mContext, ""+df, Toast.LENGTH_SHORT).show();
                    if(df.before(demoDate)) {
                        if(dt.after(demoDate)){
                            expList1.add(expList2.get(i));
                        }
                    }


                }
                adapter1.notifyDataSetChanged();*/
            }
        });




        demoFormDialog.show();

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
            //params.height = totalItemsHeight + totalDividersHeight + totalPadding;
            params.height = numberOfItems + 100 + totalDividersHeight + totalPadding;
            listView.setLayoutParams(params);
            listView.requestLayout();
            return true;

        } else {
            return false;
        }

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
            return true;
        } catch (NumberFormatException e) {
            System.out.println("Input String cannot be parsed to Integer.");
        }
        return false;
    }


    public boolean propTelecaller_form_data(Context mContext) {
        Utils.showProgressDialog(mContext, "Downloding Customer data..");
        Call<String> call = RestClient.getRestServiceString(mContext).prop_tele_form_data(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("PropelloTelecalerRes", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {

                        boolean flg = true;// isDoneInsertProp(mContext,jsonResult);
                        Log.d("PropelloTelecaler", jsonResult + "");

                        if(!Constants.telecallerFormCust.isEmpty()){
                            Constants.telecallerFormCust.clear();
                        }

                        try {
                            JSONObject obj = new JSONObject(jsonResult);
                            obj.getString("process_status");
                            String row_count = obj.getString("countrows");
                            if(obj.getString("process_status").equals("YES")){
                                //String datavalue = obj.getString("datavalue");

                                if(!Constants.telecallerFormCust.isEmpty()){
                                    Constants.telecallerFormCust.clear();
                                }

                                try {
                                    if(Constants.telecallerFormCust.size()>0){
                                        Constants.telecallerFormCust.clear();
                                    }
                                }catch (NullPointerException nullPointerException){

                                }

                                JSONArray dataArray  = obj.getJSONArray("datavalue");
                                if(dataArray.length() == Integer.parseInt(row_count)){
                                    for (int i = 0; i < dataArray.length(); i++) {
                                        JSONObject dataobj = dataArray.getJSONObject(i);
                                        TentFormDetails td=new TentFormDetails();
                                        String pdata="";
                                        td.setCustomer_name(dataobj.getString("customer_name"));
                                        td.setMobile_no(dataobj.getString("mobile_no"));
                                        //td.setDemo_tentative_date_time(dataobj.getString("demo_tentative_date_time"));
                                        td.setProductInterested(dataobj.getString("prod_data"));
                                        td.setEmp_name(dataobj.getString("emp_name"));
                                        td.setEnd_date_time(dataobj.getString("date_time"));
                                        td.setSource_type(dataobj.getString("source_type"));


                                        Constants.telecallerFormCust.add(td);
                                        /*JSONArray productArray  = dataobj.getJSONArray("prod_data");
                                        for (int ii = 0; ii < productArray.length(); ii++) {
                                            JSONObject pobj = productArray.getJSONObject(i);
                                            if(ii==0){
                                                pdata=""+ pdata +"" + pobj.getString("product");
                                            }else {
                                                pdata = "" + pdata + "," + pobj.getString("product");
                                            }
                                        }*/

                                        //Toast.makeText(mContext,"1 " +dataobj.getString("1") ,Toast.LENGTH_SHORT).show();
                                    }
                                }else{
                                    Toast.makeText(mContext,"Telecaller data downloading error",Toast.LENGTH_SHORT).show();
                                }

                            }

                        }catch (JSONException e){
                            Log.d("PropelloDemoForm ex", e.toString() + "");
                        } finally {
                            showNumberSelection();
                        }


                    }catch (Exception e){
                        Utils.cancelProgressDialog();
                    }

                }
                Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                Utils.cancelProgressDialog();
            }
        });
        return true;
    }

}