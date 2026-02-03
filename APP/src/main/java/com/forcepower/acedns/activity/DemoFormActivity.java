package com.forcepower.acedns.activity;

import static com.forcepower.acedns.constants.Constants.currentLat;
import static com.forcepower.acedns.constants.Constants.currentLong;

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
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.TimePicker;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_Demo_Form_TransactionTask;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.DemoModelListAdapter;
import com.forcepower.acedns.adapter.TelecallerCustomerMobileListAdapter;
import com.forcepower.acedns.adapter.TentExixtingProductListAdapter;
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
import com.forcepower.acedns.util.JsonsReceiver;
import com.forcepower.acedns.util.Utils;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class DemoFormActivity extends AceDnsParentActivity implements DatePickerDialog.OnDateSetListener, TimePickerDialog.OnTimeSetListener{

    String[] demoby = {"", "Self", "GL",
            "BO", "Area head" };

    String[] payType = { "Cash", "Card",
            "Cheque", "Finance", "Online" };

    int day, month, year, hour, minute;
    int myday, myMonth, myYear, myHour, myMinute;
    int salebal,adbal,duebal,totalPay=0;

    String demoData="";

    Button mButtonBack,buttonDate,btn_submit,buttonNextDueDate,btnPaymentDetails,buttonModel,buttonNumberList;
    RadioGroup rgDemo,rgBookingDone,rgSalesAchieved,rgPayMood,rgExchangeifany,rgBookedPamentMode;
    LinearLayout llDemo,llBookingDoneYes,llAppointDate,llDataSource,llSalesPersons,llFutureDate;

    EditText ed_phone,ed_name,ed_product_interested,ed_Sale_Price,ed_booked_product_Model,ed_Booked_Sale_Price,ed_adv_Price,
            ed_payment_due,ed_appoin_date,ed_data_source,ed_sales_name;

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase mAceDnsDatabase;

    Spinner spinnerdemoby,spinnerModel,spinnerPaymentDetails;

    String dateTime="",tentFormId,saleAcheived="",bookingDone="",demoAcheived="",payMood="",exchange="",bookingPayMode="";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_demo_form);

        mButtonBack = (Button) findViewById(R.id.back);
        buttonDate = (Button) findViewById(R.id.buttonDate);
        btn_submit = (Button) findViewById(R.id.btn_submit);
        buttonModel = (Button) findViewById(R.id.buttonModel);
        buttonNumberList  = (Button) findViewById(R.id.buttonNumberList);
        btnPaymentDetails = (Button) findViewById(R.id.btnPaymentDetails);
        rgDemo = (RadioGroup) findViewById(R.id.rgDemo);
        rgBookingDone = (RadioGroup) findViewById(R.id.rgBookingDone);
        rgExchangeifany = (RadioGroup) findViewById(R.id.rgExchangeifany);
        rgBookedPamentMode = (RadioGroup) findViewById(R.id.rgBookedPamentMode);
        rgPayMood= (RadioGroup) findViewById(R.id.rgPamentMode);
        rgSalesAchieved
                = (RadioGroup) findViewById(R.id.rgSalesAchieved);
        llDemo = (LinearLayout) findViewById(R.id.llSaleAchievedYes);
        llFutureDate = (LinearLayout) findViewById(R.id.llSaleAchievedNo);
        llBookingDoneYes = (LinearLayout) findViewById(R.id.llBookingDoneYes);
        llAppointDate = (LinearLayout) findViewById(R.id.llAppointDate);
        llDataSource = (LinearLayout) findViewById(R.id.llDataSource);
        llSalesPersons = (LinearLayout) findViewById(R.id.llSalesPersons);

        ed_phone = findViewById(R.id.ed_phone);
        ed_name = findViewById(R.id.ed_name);
        ed_product_interested = findViewById(R.id.ed_product_interested);
        ed_data_source = findViewById(R.id.ed_data_source);
        ed_sales_name = findViewById(R.id.ed_sales_name);

        ed_Sale_Price = findViewById(R.id.ed_Sale_Price);
        ed_booked_product_Model= findViewById(R.id.ed_booked_product_Model);
        ed_Booked_Sale_Price = findViewById(R.id.ed_Booked_Sale_Price);
        ed_adv_Price = findViewById(R.id.ed_adv_Price);
        ed_payment_due = findViewById(R.id.ed_payment_due);
        ed_appoin_date = findViewById(R.id.ed_appoin_date);

        mContext = DemoFormActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        spinnerdemoby = findViewById(R.id.spinnerdemoby);
        spinnerPaymentDetails = findViewById(R.id.spinnerPaymentDetails);
        spinnerModel = findViewById(R.id.spinnerModel);

        ArrayAdapter ad
                = new ArrayAdapter(
                DemoFormActivity.this,
                android.R.layout.simple_spinner_item,
                demoby);
        ad.setDropDownViewResource(
                android.R.layout
                        .simple_spinner_dropdown_item);

        spinnerdemoby.setAdapter(ad);


        ArrayAdapter adpay
                = new ArrayAdapter(
                DemoFormActivity.this,
                android.R.layout.simple_spinner_item,
                payType);
        adpay.setDropDownViewResource(
                android.R.layout
                        .simple_spinner_dropdown_item);

        spinnerPaymentDetails.setAdapter(adpay);


        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();

            }
        });

        btn_submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(ed_phone.getText().toString().isEmpty()){
                    Utils.showToast(mContext,"Enter Phone Number");
                }else if(ed_name.getText().toString().isEmpty()){
                    Utils.showToast(mContext,"Enter Name");
                }else if(ed_product_interested.getText().toString().isEmpty()){
                    Utils.showToast(mContext,"Enter Product Interested");
                }else{
                    if(payMood.matches("Full Payment") || payMood.matches("Booking Done")){
                        if(ed_Booked_Sale_Price.getText().toString().isEmpty()){
                            Utils.showToast(mContext,"Enter Sale Amount");
                        }else if(ed_adv_Price.getText().toString().isEmpty()){
                            Utils.showToast(mContext,"Enter Advanced Amount");
                        }else if(ed_payment_due.getText().toString().isEmpty()){
                            Utils.showToast(mContext,"Enter Balance Due");
                        }else{
                            if(!HTTPUtils.isConnectionPossible(mContext) )
                            {
                                Utils.showToast(mContext,"You must have an active internet connection to use this feature.");
                            }
                            else
                            {
                                saveToDatabase();
                            }
                        }

                    }else {
                        if (!HTTPUtils.isConnectionPossible(mContext)) {
                            Utils.showToast(mContext, "You must have an active internet connection to use this feature.");
                        } else {
                            saveToDatabase();
                        }
                    }
                }
            }
        });

        buttonDate.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                btnText = "time";
                Calendar calendar = Calendar.getInstance();
                year = calendar.get(Calendar.YEAR);
                month = calendar.get(Calendar.MONTH);
                day = calendar.get(Calendar.DAY_OF_MONTH);
                DatePickerDialog datePickerDialog = new DatePickerDialog(DemoFormActivity.this, (DatePickerDialog.OnDateSetListener) DemoFormActivity.this,year, month,day);
                datePickerDialog.show();

            }
        });

        rgSalesAchieved.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();

                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){
                        llDemo.setVisibility(View.VISIBLE);
                        llFutureDate.setVisibility(View.GONE);
                        saleAcheived = "yes";
                    }else{
                        saleAcheived = "no";
                        llDemo.setVisibility(View.GONE);
                        llFutureDate.setVisibility(View.VISIBLE);
                    }

                }
            }
        });
        rgBookedPamentMode.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();

                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){

                        bookingPayMode = "yes";
                    }else{
                        bookingPayMode = "no";

                    }

                }
            }
        });
        rgPayMood.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();

                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){
                        llBookingDoneYes.setVisibility(View.VISIBLE);
                        payMood = "Full Payment";
                    }else{
                        llBookingDoneYes.setVisibility(View.VISIBLE);
                        payMood = "Booking Done";

                    }

                }
            }
        });

        rgExchangeifany.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();

                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){

                        exchange = "yes";
                    }else{
                        exchange = "no";

                    }

                }
            }
        });


        rgBookingDone.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener()
        {
            public void onCheckedChanged(RadioGroup group, int checkedId)
            {
                RadioButton checkedRadioButton = (RadioButton)group.findViewById(checkedId);

                boolean isChecked = checkedRadioButton.isChecked();
                if (isChecked)
                {
                    if(checkedRadioButton.getText().toString().matches("YES")){
                        llBookingDoneYes.setVisibility(View.VISIBLE);
                        bookingDone = "yes";
                    }else{
                        bookingDone = "no";
                        llBookingDoneYes.setVisibility(View.GONE);
                    }
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

                        demoAcheived = "yes";
                    }else{
                        demoAcheived = "no";

                    }
                }
            }
        });

        ed_phone.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {

            }

            @Override
            public void afterTextChanged(Editable s) {
                    if(ed_phone.getText().toString().length()>0){
                        if(ed_phone.getText().toString().length()==10){

                            Constants.employeeDetailObject = mAceDnsDatabase.getEmployeeObj();
                            //ArrayList<TentFormDetails> unUploadedCus = dataHelperObj.getTentFormCustomerByPhone(ed_phone.getText().toString());

                            //if(!unUploadedCus.isEmpty()) {
                            if(!Constants.demoFormCust.isEmpty()) {
                                for(int j=0;j<Constants.demoFormCust.size();j++){
                                    if(Constants.demoFormCust.get(j).getMobile_no().matches(ed_phone.getText().toString())){
                                        ed_name.setText(Constants.demoFormCust.get(j).getCustomer_name());
                                        llAppointDate.setVisibility(View.VISIBLE);
                                        llDataSource.setVisibility(View.VISIBLE);
                                        llSalesPersons.setVisibility(View.VISIBLE);
                                        ed_appoin_date.setText(""+Constants.demoFormCust.get(j).getDemo_tentative_date_time());
                                        ed_product_interested.setText(Constants.demoFormCust.get(j).getProductInterested());

                                        ed_product_interested.setEnabled(false);
                                        ed_name.setEnabled(false);
                                        ed_appoin_date.setEnabled(false);
                                    }

                                }
                               /* ed_name.setText(unUploadedCus.get(0).getCustomer_name());
                                ArrayList<TentProduct> unUploadedPro = dataHelperObj.getDemoFormProduct(unUploadedCus.get(0).getTent_form_id(),unUploadedCus.get(0).getMobile_no());
                                String prod="";
                                for(int iii=1;iii<=unUploadedPro.size();iii++) {
                                    TentProduct detailsObjP = unUploadedPro.get((iii-1));
                                    if(iii==unUploadedPro.size()){
                                        prod = prod + "," + detailsObjP.getLife_of_product();
                                    }else if(iii==1){
                                        prod = detailsObjP.getLife_of_product();
                                    }else {
                                        prod = prod + "," + detailsObjP.getLife_of_product();
                                    }
                                }*/
                                //ed_product_interested.setText(prod);

                                //ed_appoin_date.setText(""+unUploadedCus.get(0).getDemo_tentative_date_time());
                                if(!ed_product_interested.getText().toString().isEmpty()) {
                                    //setModelSpinner();
                                }else{

                                }
                            }
//Utils.showToast(mContext,ed_phone.getText().toString());
                        }
                    }else{
                        ed_name.setText("");
                        llAppointDate.setVisibility(View.GONE);
                        llDataSource.setVisibility(View.GONE);
                        llSalesPersons.setVisibility(View.GONE);
                    }
            }
        });

        new GPSTracker(mContext);

        ed_adv_Price.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                

            }

            @Override
            public void afterTextChanged(Editable s) {
                try {
                    if(!ed_Booked_Sale_Price.getText().toString().isEmpty()) {
                        int sal = Integer.parseInt(ed_Booked_Sale_Price.getText().toString());
                        if(!ed_adv_Price.getText().toString().isEmpty()){
                            int adv = Integer.parseInt(ed_adv_Price.getText().toString());
                            duebal = sal - adv;
                            ed_payment_due.setText(""+duebal);
                        }else{
                            ed_payment_due.setText("");
                        }
                    }else{
                        ed_adv_Price.setText("");
                        Utils.showToast(mContext,"Enter Sale Price");
                    }
                }catch(Exception e){

                }

            }
        });

        ed_product_interested.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {


            }

            @Override
            public void afterTextChanged(Editable s) {
                try {
                    if(!ed_product_interested.getText().toString().isEmpty()) {
                        setModelSpinner();
                    }else{

                    }
                }catch(Exception e){

                }

            }
        });

        btnPaymentDetails.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(!ed_Booked_Sale_Price.getText().toString().isEmpty()) {
                    getPaymentDetails();
                    ed_adv_Price.setEnabled(false);
                    ed_payment_due.setEnabled(false);
                }else{
                    Utils.showToast(mContext,"Enter Sale Price");
                }
            }
        });

        buttonModel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                openModel();
            }
        });

        buttonNumberList.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                if(!HTTPUtils.isConnectionPossible(mContext) )
                {
                    Utils.showToast(mContext,"You must have an active internet connection to use this feature.");
                }
                else
                {
                    //showNumberSelection();
                    propDemo_form_data(mContext);
                }

            }
        });

    }


    private void openModel(){
        String s = ed_product_interested.getText().toString();
        String ss="";
        if (s.contains(",")) {
            String[] arrayOfData = s.split(",");
            for (int i = 1; i <= arrayOfData.length; i++) {
                if(i==arrayOfData.length) {
                    if (arrayOfData[(i - 1)].matches("CHIMNEY")) {
                        ss = ss + "'Chimney'";
                    } else {
                        ss = ss + "'" + arrayOfData[(i - 1)] + "'";
                    }
                }else{
                    if (arrayOfData[(i - 1)].matches("CHIMNEY")) {
                        ss = ss + "'Chimney',";
                    } else {
                        ss = ss + "'" + arrayOfData[(i - 1)] + "',";
                    }
                }
            }
        } else {
            if(s.toUpperCase().matches("CHIMNEY")){
                ss= "'Chimney'";
            }else {
                ss= "'"+s+"'";
            }

        }

        ArrayList<CommonModel> model = dataHelperObj.getModelListDemoForm(""+ss);

        ArrayList<CommonModel> modelTemp = model;

        Dialog demoFormDialog;

        demoFormDialog = new Dialog(DemoFormActivity.this, R.style.PauseDialogTent);
        demoFormDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        demoFormDialog.setContentView(R.layout.select_demo_form_model);
        demoFormDialog.setCancelable(false);
        TextView title = (TextView) demoFormDialog.findViewById(R.id.title);
        title.setText("Please Select");


        Button btnSubmit = (Button) demoFormDialog.findViewById(R.id.btn_submit);
        btnSubmit.setVisibility(View.VISIBLE);
        btnSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                String mo = "";
                for(int i=1; i<= modelTemp.size();i++){

                    if (modelTemp.get((i - 1)).getCom2().toString().matches("1")) {
                        if(i==modelTemp.size()){
                            mo = mo + modelTemp.get((i - 1)).getCom1().toString();
                        }else {
                            mo = mo + modelTemp.get((i - 1)).getCom1().toString()+",";
                        }
                    }
                }
                buttonModel.setText(mo);
                demoFormDialog.cancel();
            }
        });

        ImageView back = (ImageView) demoFormDialog.findViewById(R.id.image_cancel);
        //back.setVisibility(View.VISIBLE);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                //showCreateRouteDialog();

                demoFormDialog.cancel();
            }
        });

        ListView listView = (ListView) demoFormDialog.findViewById(R.id.listViewModel);

        DemoModelListAdapter adapter1 = new DemoModelListAdapter(DemoFormActivity.this, model);
        listView.setAdapter(adapter1);

        listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

                //Utils.showToast(mContext,""+adapter1.getItem(position));
                CommonModel cm = adapter1.getItem(position);

                modelTemp.get(position).setCom2(cm.getCom2());

                /*for(int i=0; i<parent.getChildCount(); i++)
                {
                    if(i == position)
                    {
                        parent.getChildAt(i).setBackgroundColor(Color.LTGRAY);
                    }
                    else
                    {
                        parent.getChildAt(i).setBackgroundColor(Color.TRANSPARENT);
                    }

                }*/
            }
        });


        demoFormDialog.show();

    }

    String fDate="",tDate="";

    @Override
    public void onDateSet(DatePicker view, int year, int month, int dayOfMonth) {
        myYear = year;
        myday = dayOfMonth;
        myMonth = month+1;
        dateTime = myYear+"-"+myMonth+"-"+myday;
        String dateTimeForList = myday+"-"+myMonth+"-"+myYear;
        Calendar c = Calendar.getInstance();
        hour = c.get(Calendar.HOUR);
        minute = c.get(Calendar.MINUTE);
        /*TimePickerDialog timePickerDialog = new TimePickerDialog(DemoFormActivity.this, (TimePickerDialog.OnTimeSetListener) DemoFormActivity.this, hour, minute, DateFormat.is24HourFormat(this));
        timePickerDialog.show();*/

        String chosenFromDate ;
        chosenFromDate =  new SimpleDateFormat("dd/MM/yyyy").format(Calendar.getInstance().getTime());
        String chosenDate = (dayOfMonth+1) + "/" + (month + 1) + "/" + year;
        //Utils.showToast(mContext,"Chochen" +chosenDate +" date" + chosenFromDate);

        if(btnText.matches("fromdate")) {
            dateTimeForList = (myday-1)+"-"+myMonth+"-"+myYear;
            fDate = dateTimeForList;
            buttonFromDate.setText(dateTime);
        }
        else if(btnText.matches("todate")) {
            dateTimeForList = (myday+1)+"-"+myMonth+"-"+myYear;
            tDate = dateTimeForList;
            buttonToDate.setText(dateTime);
            //DateRangeList(fDate,tDate);
        }else {

            if (Utils.checkDatePick(chosenDate, chosenFromDate, "dd/MM/yyyy")) {
                TimePickerDialog timePickerDialog = new TimePickerDialog(DemoFormActivity.this, (TimePickerDialog.OnTimeSetListener) DemoFormActivity.this, hour, minute, DateFormat.is24HourFormat(this));
                timePickerDialog.show();
            } else {
                Utils.showToast(mContext, "Date Must Be Grater Than From today");
            }
        }

    }
    @Override
    public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
        myHour = hourOfDay;
        myMinute = minute;
        dateTime = dateTime + " "+ myHour + ":"+myMinute;
        //buttonDate.setText(dateTime);

        String n;
        n=myYear+""+myMonth+""+myday;
        try {
            String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
            Date currentDaten = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + n);
            if (!currentDaten.before(currentDate)) {
                //dialogCaldroidFragment.dismiss();
                //btnDate.setText(dateFormat.format(date));
                //remarksDate = new SimpleDateFormat("yyyyMMdd").format(date);
                buttonDate.setText(""+dateTime.toString());

            } else {
                Utils.showToast(mContext, "Past Dates cannot be selected.");
            }
        } catch (Exception e) {

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

        //String timeStamp = tentFormId;
        new GPSTracker(mContext);
        if(!ed_phone.getText().toString().isEmpty()) {

            boolean r = false;

            r = dataHelperObj.AddDemoFormDetails(tentFormId,""+ currentLat,""+currentLong,""+ ed_name.getText().toString(),""+ed_product_interested.getText().toString(),""+demoAcheived,""+dateTime,
                    ""+spinnerdemoby.getSelectedItem().toString(),
                    ""+saleAcheived,
                    ""+payMood,
                    ""+payMood,
                    ""+buttonModel.getText().toString(),
                    ""+ed_Booked_Sale_Price.getText().toString(),
                    ""+exchange,
                    ""+spinnerPaymentDetails.getSelectedItem().toString(),""+bookingDone,""+buttonModel.getText().toString(),""+ed_Booked_Sale_Price.getText().toString(),""+ed_adv_Price.getText().toString(),
                    ""+bookingPayMode,""+ed_payment_due.getText().toString());


            if(r==true){

                dataHelperObj.insertToLocationTable1("DF", tentFormId);
            }

            if(HTTPUtils.isConnectionPossible(mContext))
            {
                String order_no = "DF" + Constants.employeeDetailObject.getEmpCode()
                        + tentFormId;
                ArrayList<Location> unUploadedTransactionDR = dataHelperObj.getUnuploadedTransactionPropello("demo_form", ""+order_no);
                TRANS_Demo_Form_TransactionTask sb2 = new TRANS_Demo_Form_TransactionTask(DemoFormActivity.this, unUploadedTransactionDR);
                sb2.execute();

                finish();

            }else {

                finish();
            }
        }else{
            Utils.showToast(mContext, "Please add Phone Number");
        }

    }

    private void setModelSpinner(){
        String s = ed_product_interested.getText().toString();
        String ss="";
        if (s.contains(",")) {
            String[] arrayOfData = s.split(",");
            for (int i = 1; i <= arrayOfData.length; i++) {
                if(i==arrayOfData.length) {
                    if (arrayOfData[(i - 1)].matches("CHIMNEY")) {
                        ss = ss + "'Chimney'";
                    } else {
                        ss = ss + "'" + arrayOfData[(i - 1)] + "'";
                    }
                }else{
                    if (arrayOfData[(i - 1)].matches("CHIMNEY")) {
                        ss = ss + "'Chimney',";
                    } else {
                        ss = ss + "'" + arrayOfData[(i - 1)] + "',";
                    }
                }
            }
        } else {
            if(s.toUpperCase().matches("CHIMNEY")){
                ss= "'Chimney'";
            }else {
                ss= "'"+s+"'";
            }

        }

        List<String> model = dataHelperObj.getModelDemoForm(""+ss);

        //Utils.showToast(mContext,""+model.size());

        if(model.size()>0) {
            ArrayAdapter<String> adpay
                    = new ArrayAdapter<String>(
                    DemoFormActivity.this,
                    android.R.layout.simple_spinner_item,
                    model);
            adpay.setDropDownViewResource(
                    android.R.layout
                            .simple_spinner_dropdown_item);

            spinnerModel.setAdapter(adpay);
        }
    }
    private void getPaymentDetails1(){

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
        TentExixtingProductListAdapter adapter1 = new TentExixtingProductListAdapter(DemoFormActivity.this, expList1);
        dialogList.setAdapter(adapter1);

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

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

    private void getPaymentDetails(){
        Dialog demoFormDialog;
        /*body_layout = (LinearLayout) findViewById(R.id.body_layout);
        body_layout.setAlpha(0.3F);*/

        demoFormDialog = new Dialog(DemoFormActivity.this, R.style.PauseDialogTent);
        demoFormDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        demoFormDialog.setContentView(R.layout.payment_details);
        demoFormDialog.setCancelable(false);
        TextView title = (TextView) demoFormDialog.findViewById(R.id.title);
        title.setText("Please Fill Payment Details");

        EditText etCash = (EditText) demoFormDialog.findViewById(R.id.etCash);
        EditText etCard = (EditText) demoFormDialog.findViewById(R.id.etCard);
        EditText etCheque = (EditText) demoFormDialog.findViewById(R.id.etCheque);
        EditText etFinance = (EditText) demoFormDialog.findViewById(R.id.etFinance);
        EditText etOnline = (EditText) demoFormDialog.findViewById(R.id.etOnline);

        Button cancel = (Button) demoFormDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.VISIBLE);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {

                if(etCash.getText().toString().isEmpty() && etCard.getText().toString().isEmpty() &&
                        etCheque.getText().toString().isEmpty() && etFinance.getText().toString().isEmpty() &&
                        etOnline.getText().toString().isEmpty() ){
                    Utils.showToast(mContext,"Please add payment");
                }else {

                    int cash =0,card=0,cheque=0,finance=0,online=0;
                    if(!etCash.getText().toString().isEmpty()) {
                        cash = Integer.parseInt(etCash.getText().toString());
                    }
                    if(!etCard.getText().toString().isEmpty()) {
                        card = Integer.parseInt(etCard.getText().toString());
                    }
                    if(!etCheque.getText().toString().isEmpty()) {
                        cheque = Integer.parseInt(etCheque.getText().toString());
                    }
                    if(!etFinance.getText().toString().isEmpty()) {
                        finance = Integer.parseInt(etFinance.getText().toString());
                    }
                    if(!etOnline.getText().toString().isEmpty()) {
                        online = Integer.parseInt(etOnline.getText().toString());
                    }

                    totalPay =  cash + card + cheque + finance + online;

                    ed_adv_Price.setText(""+totalPay);

                    if(!etOnline.getText().toString().isEmpty()) {
                        online = Integer.parseInt(etOnline.getText().toString());
                    }
                    int sal =0;
                    if(!ed_Booked_Sale_Price.getText().toString().isEmpty()) {
                        sal = Integer.parseInt(ed_Booked_Sale_Price.getText().toString());
                    }
                        duebal = sal - totalPay;
                        ed_payment_due.setText(""+duebal);
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


    ArrayList<CommonModel> expList2 = null;
    ArrayList<CommonModel> expList1 = null;
    CommonModel commonModel = null;
    String btnText="";
    Button buttonFromDate;
    Button buttonToDate;
    ListView listViewNumber;
    TelecallerCustomerMobileListAdapter adapter1;
    public void showNumberSelection(){

        Dialog demoFormDialog;
        demoFormDialog = new Dialog(DemoFormActivity.this, R.style.PauseDialogTent);
        demoFormDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        demoFormDialog.setContentView(R.layout.select_tent_form_mobile);
        demoFormDialog.setCancelable(false);
        TextView title = (TextView) demoFormDialog.findViewById(R.id.title);
        title.setText("Please Select");

        listViewNumber = (ListView) demoFormDialog.findViewById(R.id.listViewNumberFor);
        //imgPic = (ImageView) demoFormDialog.findViewById(R.id.autoCompleteTextView1);

        Button buttonPic = (Button) demoFormDialog.findViewById(R.id.btn_submit);
        buttonPic.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {

               /* String smsNumber = "919800970578";
                Intent sendIntent = new Intent("android.intent.action.MAIN");
                sendIntent.putExtra("satya", smsNumber + "@s.whatsapp.net");
                sendIntent.putExtra(Intent.EXTRA_TEXT, demoData);
                sendIntent.setAction(Intent.ACTION_SEND);
                sendIntent.setPackage("com.whatsapp");
                sendIntent.setType("text/plain");
                startActivity(sendIntent);*/

            }
        });

        ImageView cancel = (ImageView) demoFormDialog.findViewById(R.id.image_cancel);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {

                demoFormDialog.cancel();

            }
        });

        //expList1 =new ArrayList<>();// dataHelperObj.getMobileCustTelecallerForm("KFE000220220513153811");

        //propDemo_form_data(mContext);



        expList1 =new ArrayList<>();

        if(!Constants.demoFormCust.isEmpty()) {
            for(int j=0;j<Constants.demoFormCust.size();j++){
                CommonModel cm = new CommonModel();
                cm.setCom1(Constants.demoFormCust.get(j).getMobile_no());
                cm.setCom2(Constants.demoFormCust.get(j).getCustomer_name());
                cm.setCom5(Constants.demoFormCust.get(j).getProductInterested());
                cm.setCom7(Constants.demoFormCust.get(j).getSource_type());
                cm.setCom8(Constants.demoFormCust.get(j).getEmp_name());
                cm.setCom11(Constants.demoFormCust.get(j).getDemo_tentative_date_time());
                cm.setCom3("");

                expList1.add(cm);
            }
        }


        expList2 = new ArrayList<CommonModel>();
        expList2.addAll(expList1);

        adapter1 = new TelecallerCustomerMobileListAdapter(DemoFormActivity.this, expList1);
        listViewNumber.setAdapter(adapter1);



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
               // buttonNumberList.setText(commonModel.getCom1());
                ed_name.setText(commonModel.getCom2());
                ed_phone.setText(commonModel.getCom1());
                ed_data_source.setText(commonModel.getCom7());
                ed_sales_name.setText(commonModel.getCom8());
                //setExistingProduct(commonModel.getCom5(),commonModel.getCom1());
                //tempProduct = commonModel.getCom5();
                ed_product_interested.setEnabled(false);
                ed_name.setEnabled(false);
                ed_appoin_date.setEnabled(false);
                ed_data_source.setEnabled(false);
                ed_phone.setEnabled(false);
                ed_sales_name.setEnabled(false);
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
                DatePickerDialog datePickerDialog = new DatePickerDialog(DemoFormActivity.this, (DatePickerDialog.OnDateSetListener) DemoFormActivity.this,year, month,day);
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
                DatePickerDialog datePickerDialog = new DatePickerDialog(DemoFormActivity.this, (DatePickerDialog.OnDateSetListener) DemoFormActivity.this,year, month,day);
                datePickerDialog.show();

            }
        });

        buttonToDateSearch.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                expList1.clear();
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
                adapter1.notifyDataSetChanged();
            }
        });





        demoFormDialog.show();

    }

    private void DateRangeList(String fd,String td){
        expList1 =new ArrayList<>();// dataHelperObj.getMobileCustTelecallerForm("KFE000220220513153811");
        JsonsReceiver.prop_demo_form_data(mContext);
        if(!expList1.isEmpty()) {
            expList1.clear();
        }
        if(!Constants.demoFormCust.isEmpty()) {


            for(int j=0;j<Constants.demoFormCust.size();j++){
                SimpleDateFormat sdf = new SimpleDateFormat("dd-MM-yyyy");
                Date df = new Date(),dt = new Date(),demoDate = new Date();
                try{
                    df = sdf.parse(fd);
                    dt = sdf.parse(td);
                    demoDate = sdf.parse(Constants.demoFormCust.get(j).getDemo_tentative_date_time());
                    Toast.makeText(mContext, ""+Constants.demoFormCust.get(j).getDemo_tentative_date_time(), Toast.LENGTH_SHORT).show();
                }catch(ParseException ex){
                    Toast.makeText(mContext, ""+ ex.toString(), Toast.LENGTH_SHORT).show();
                }
               if(df.after(demoDate) && dt.after(demoDate)) {
                    CommonModel cm = new CommonModel();
                    cm.setCom1(Constants.demoFormCust.get(j).getMobile_no());
                    cm.setCom2(Constants.demoFormCust.get(j).getCustomer_name());
                    cm.setCom5(Constants.demoFormCust.get(j).getProductInterested());
                    cm.setCom7(Constants.demoFormCust.get(j).getSource_type());
                    cm.setCom8(Constants.demoFormCust.get(j).getEmp_name());
                    cm.setCom3("");

                    expList1.add(cm);
                }

            }
        }


        expList2 = new ArrayList<CommonModel>();
        expList2.addAll(expList1);

        TelecallerCustomerMobileListAdapter adapter1 = new TelecallerCustomerMobileListAdapter(DemoFormActivity.this, expList1);
        listViewNumber.setAdapter(adapter1);

    }
    boolean rflg = false;
    public boolean propDemo_form_data(Context mContext) {

        Utils.showProgressDialog(mContext, "Downloding Customer data..");
        Call<String> call = RestClient.getRestServiceString(mContext).prop_demo_form_data(""+ Constants.nickName,""+Constants.employeeDetailObject.getEmpCode());
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                Log.d("PropelloDemoForm :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();
                    demoData = jsonResult;
                    try {

                        boolean flg = true;// isDoneInsertProp(mContext,jsonResult);
                        //Log.d("PropelloDemoForm :=>", jsonResult + "");

                        if(!Constants.demoFormCust.isEmpty()){
                            Constants.demoFormCust.clear();
                        }

                        try {
                            JSONObject obj = new JSONObject(jsonResult);
                            obj.getString("process_status");
                            String row_count = obj.getString("countrows");
                            if(obj.getString("process_status").equals("YES")){
                                //String datavalue = obj.getString("datavalue");
                                if(!Constants.demoFormCust.isEmpty()){
                                    Constants.demoFormCust.clear();
                                }

                                JSONArray dataArray  = obj.getJSONArray("datavalue");
                                if(dataArray.length() == Integer.parseInt(row_count)){
                                    for (int i = 0; i < dataArray.length(); i++) {
                                        JSONObject dataobj = dataArray.getJSONObject(i);
                                        TentFormDetails td=new TentFormDetails();
                                        String pdata="";
                                        td.setCustomer_name(dataobj.getString("customer_name"));
                                        td.setMobile_no(dataobj.getString("mobile_no"));
                                        td.setDemo_tentative_date_time(dataobj.getString("demo_tentative_date_time"));
                                        td.setSource_type(dataobj.getString("source_type"));
                                        td.setEmp_name(dataobj.getString("emp_name"));
                                        if(!dataobj.getString("prod_data").toUpperCase().matches("NULL")) {
                                            JSONArray productArray  = dataobj.getJSONArray("prod_data");

                                            for (int ii = 0; ii < productArray.length(); ii++) {

                                                JSONObject pobj = productArray.getJSONObject(ii);
                                                if (ii == 0) {
                                                    pdata = "" + pdata + "" + pobj.getString("product");
                                                } else {
                                                    pdata = "" + pdata + "," + pobj.getString("product");
                                                }
                                            }
                                        }
                                        td.setProductInterested(pdata);
                                        Constants.demoFormCust.add(td);
                                        //Toast.makeText(mContext,"1 " +dataobj.getString("1") ,Toast.LENGTH_SHORT).show();

                                    }
                                }else{
                                    Toast.makeText(mContext,"customer_product_stock downloading error",Toast.LENGTH_SHORT).show();
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