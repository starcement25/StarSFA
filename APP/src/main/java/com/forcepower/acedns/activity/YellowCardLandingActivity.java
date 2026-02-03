package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
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
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_PendingRoutePlanBeforeOtherTxn;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitYellowCardTask;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.Utils;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;

import static com.forcepower.acedns.R.id.datePicker1;
import static com.forcepower.acedns.constants.Constants.dateString;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInCustomer;


public class YellowCardLandingActivity extends AceDnsParentActivity {
    public String mRouteName = "", previousValidationMonth, validationdate;//yyyy-MM-dd
    public String mRouteCode = "";
    public String mRdsCode = "";
    public String mDealerName = "";
    public String mProduct = "";
    public Boolean isCustomerChosen = false, isChallanIdGiven = false, isChallanDateGiven = false, isQuantityGiven = false, isUnitChosen = false;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    RoutePlanMasterDetails mSelectedTodayRouteDetails;
    Date validationDateInDateFormat;
    TextView textViewRouteValue, textViewCustomerValue, textViewDealerValue, challanIdTv, challandateTv, quantityTv, unitTv, selectRoutTV, selectCustomerTv, textViewDateValidation;
    Button challanIdBtn, addDateBtn, quantityBtn, unitBtn;
    int selectedUnit = -1;
    List<String> unitsValList;
    ImageView imgLogo;
    Handler mHandler;
    ProgressDialog loader;
    int localDataSavingFailedAttempt = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_yellow_card_landing);
        unitsValList = new ArrayList<>();
        unitsValList.add("PPC");
        unitsValList.add("PSC");
        unitsValList.add("ARC");
        //unitsValList.add("OPC");
        mContext = this;
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        initializeViews();
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        setMinDateForYellowCard();
        mRoutePlanListofToday = new ArrayList<>();
        mCustomerDetailsList = new ArrayList<>();
        mSelectedTodayRouteDetails = new RoutePlanMasterDetails();
        mSelectedTodayRouteDetails = new RoutePlanMasterDetails();
        getRoutPlanListFromDb();
        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    YellowCardLandingActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
//                            int recordcount=mAceDnsTransactionDatabase.GetUnuploadedCustomerCount();
                            boolean isExist = mAceDnsTransactionDatabase.IsUnuploadedRoutePlanExist();
                            if (isExist) {
                                new TRANS_PendingRoutePlanBeforeOtherTxn(mContext, "YELLOW CARD").execute();
                            } else {
                                new TRANS_SubmitYellowCardTask(mContext, true).execute();
                            }

                        }
                    });


                }
            }
        };
    }

    private void setMinDateForYellowCard() {
        textViewDateValidation.setVisibility(View.GONE);
//        String previousValidationMonth =Utils.changeDateFormat("yyyyMMdd","yyyy-MM-dd",dateString );
        previousValidationMonth = Utils.getPreviousMonthYearOfGivenDate("yyyyMMdd", "yyyy-MM", dateString);
        validationdate = mAceDnsDatabase.getYellowCardValidationMonthDate(previousValidationMonth);
        if (!validationdate.matches("")) {

            try {
                validationDateInDateFormat = new SimpleDateFormat("yyyyMMdd").parse(validationdate);
                textViewDateValidation.setText("Validation Date: " + validationdate);
            } catch (ParseException e) {
                e.printStackTrace();
            }
        }
    }

    private void initializeViews() {
        textViewRouteValue = (TextView) findViewById(R.id.textViewRouteValue);
        textViewCustomerValue = (TextView) findViewById(R.id.textViewCustomerValue);
        textViewDealerValue = (TextView) findViewById(R.id.textViewBargainNumber);
        textViewDateValidation = (TextView) findViewById(R.id.textViewDateValidation);
        challanIdTv = (TextView) findViewById(R.id.challanIdTv);
        challandateTv = (TextView) findViewById(R.id.challandateTv);
        quantityTv = (TextView) findViewById(R.id.quantityTv);
        unitTv = (TextView) findViewById(R.id.unitTv);
        selectRoutTV = (TextView) findViewById(R.id.selectRoutTV);
        selectRoutTV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                getRoutPlanListFromDb();
            }
        });
        selectCustomerTv = (TextView) findViewById(R.id.selectCustomerTv);
        selectCustomerTv.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                SelectCustomer();
            }
        });
        challanIdBtn = (Button) findViewById(R.id.challanIdBtn);
        challanIdBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                provideInputForYellowCards("id");
            }
        });
        addDateBtn = (Button) findViewById(R.id.addDateBtn);
        addDateBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                provideInputForYellowCards("date");
            }
        });
        quantityBtn = (Button) findViewById(R.id.quantityBtn);
        quantityBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                provideInputForYellowCards("quantity");
            }
        });
        unitBtn = (Button) findViewById(R.id.unitBtn);
        unitBtn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                provideInputForYellowCards("unit");
            }
        });
    }

    private void getRoutPlanListFromDb() {
        String today = dateString.substring(6, 8) + "-"
                + dateString.substring(4, 6) + "-"
                + dateString.substring(0, 4);
        mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForToday(today);

        //amitabha2715
        /*if (NotCheckedOut(mContext)) {
            mSelectedTodayRouteDetails = mRoutePlanListofToday.get(getPositionOfCurrentCheckedInRoute(true, mContext, mRoutePlanListofToday, null));
            mRouteName = mSelectedTodayRouteDetails.getRouteName();
            textViewRouteValue.setText("Route: " + mRouteName);
            mRouteCode = mSelectedTodayRouteDetails.getRoutecode();
            mCustomerDetailsList = mAceDnsDatabase.getRetailerSubDelearTypeCustomerListByRoute(mSelectedTodayRouteDetails.getRoutecode());
            SelectCustomer();
        } else*/ if (mRoutePlanListofToday.size() == 1) {
            mSelectedTodayRouteDetails = mRoutePlanListofToday.get(0);
            mRouteName = mSelectedTodayRouteDetails.getRouteName();
            textViewRouteValue.setText("Route: " + mRouteName);
            mRouteCode = mSelectedTodayRouteDetails.getRoutecode();
            mCustomerDetailsList = mAceDnsDatabase.getRetailerSubDelearTypeCustomerListByRoute(mSelectedTodayRouteDetails.getRoutecode());
            SelectCustomer();
        } else {
            ShowTodayRoutePlanListDialog(mRoutePlanListofToday);
        }
    }

    private void ShowTodayRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> routePlanListofToday) {
        if (routePlanListofToday.size() > 0) {
            final ArrayList<RoutePlanMasterDetails> routePlanListofTodaySearchingArray = new ArrayList<>(routePlanListofToday);
            final Dialog routePlanListDialog = new Dialog(mContext, R.style.PauseDialog);
            routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            routePlanListDialog.setContentView(R.layout.select_from_list);
            routePlanListDialog.setCancelable(false);
            TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
            title.setText("Please select a Route");
            ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
            final RoutePlanTransAdapter adapter = new RoutePlanTransAdapter(mContext, R.layout.route_list_child, routePlanListofToday);
            dialogList.setAdapter(adapter);
            final EditText autoCompleteTextView1 = (EditText) routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
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

                    //clear the initial data set
                    routePlanListofToday.clear();
                    for (int i = 0; i < routePlanListofTodaySearchingArray.size(); i++) {
                        String routeName = routePlanListofTodaySearchingArray.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                routePlanListofToday.add(routePlanListofTodaySearchingArray.get(i));
                            }
                        }
                    }
                    adapter.notifyDataSetChanged();

                }
            });
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    routePlanListDialog.cancel();
                    mSelectedTodayRouteDetails = routePlanListofToday.get(arg2);
                    mRouteName = mSelectedTodayRouteDetails.getRouteName();
                    textViewRouteValue.setText("Route: " + mRouteName);
                    mRouteCode = mSelectedTodayRouteDetails.getRoutecode();
                    mCustomerDetailsList = mAceDnsDatabase.getRetailerSubDelearTypeCustomerListByRoute(mSelectedTodayRouteDetails.getRoutecode());
                    SelectCustomer();
                }
            });
            Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.GONE);

            Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            routePlanListDialog.show();
        }

    }

    private void SelectCustomer() {
        if (NotCheckedOut(mContext) && mCustomerDetailsList.size() > 0) {
            Constants.selectedCustomer = mCustomerDetailsList.get(getPositionOfCurrentCheckedInCustomer(mContext, mCustomerDetailsList));
            Constants.selectedCustomer.setRouteName(mRouteName);
            mRdsCode = Constants.selectedCustomer.getRdsTag();
            DealerName();
            textViewCustomerValue.setText("Sub Dealer: " + Constants.selectedCustomer.getCustomerName());
            isCustomerChosen = true;
        } else if (mCustomerDetailsList.size() > 1) {
            ShowCustomerListDialog();

        } else if (mCustomerDetailsList.size() == 1) {
            Constants.selectedCustomer = mCustomerDetailsList.get(0);
            Constants.selectedCustomer.setRouteName(mRouteName);
            mRdsCode = Constants.selectedCustomer.getRdsTag();
            DealerName();
            textViewCustomerValue.setText("Sub Dealer: " + Constants.selectedCustomer.getCustomerName());
            isCustomerChosen = true;
        } else {
            Toast.makeText(mContext, "No customer found, please contact admin", Toast.LENGTH_SHORT).show();
            finish();
        }
    }

    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a customer of route " + mRouteName);
        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                Constants.selectedCustomer.setRouteName(mRouteName);
                mRdsCode = Constants.selectedCustomer.getRdsTag();
                DealerName();
                textViewCustomerValue.setText("Sub Dealer: " + Constants.selectedCustomer.getCustomerName());
                isCustomerChosen = true;
            }
        });

        Button addnewcustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setVisibility(View.GONE);


        mDialogCustomer.show();
    }

    public void provideInputForYellowCards(final String type) {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.yellow_card_inputs_dialog);
        instructionDialog.setCancelable(false);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        if (type.matches("id")) {
            title.setText("Challan Number");
            final LinearLayout idOrQuantity = (LinearLayout) instructionDialog.findViewById(R.id.idOrQuantity);
            idOrQuantity.setVisibility(View.VISIBLE);
            final EditText challanId = (EditText) instructionDialog.findViewById(R.id.challanId);
            challanId.setHint("number");
            if (isChallanIdGiven) {
                String challanNo = challanIdTv.getText().toString();
                challanId.setText(challanNo);
            }
        } else if (type.matches("quantity")) {
            title.setText("Quantity in Bags");
            final LinearLayout idOrQuantity = (LinearLayout) instructionDialog.findViewById(R.id.idOrQuantity);
            idOrQuantity.setVisibility(View.VISIBLE);
            final EditText challanId = (EditText) instructionDialog.findViewById(R.id.challanId);
            challanId.setRawInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            challanId.setHint("quantity");
            if (isQuantityGiven) {
                String quantity = quantityTv.getText().toString();
                challanId.setText(quantity);
            }
        } else if (type.matches("date")) {
            title.setText("Choose Date");
            final LinearLayout datePickerLL = (LinearLayout) instructionDialog.findViewById(R.id.datePickerLL);
            datePickerLL.setVisibility(View.VISIBLE);

            final DatePicker datePicker1 = (DatePicker) instructionDialog.findViewById(R.id.datePicker1);
            final Calendar cal = Calendar.getInstance();
            int year = cal.get(Calendar.YEAR);
            int month = cal.get(Calendar.MONTH);
            int day = cal.get(Calendar.DAY_OF_MONTH);
            if (isChallanDateGiven) {
                String lastGivenDate = challandateTv.getText().toString();
                String[] dateArray = lastGivenDate.split("-");
                year = Integer.parseInt(dateArray[2]);
                month = Integer.parseInt(dateArray[1]);
                month = month - 1;
                day = Integer.parseInt(dateArray[0]);
            }

            datePicker1.init(year, month, day, new DatePicker.OnDateChangedListener() {

                @Override
                public void onDateChanged(DatePicker view, int year,
                                          int monthOfYear, int dayOfMonth) {
                    Calendar selectedCal = Calendar.getInstance();
                    selectedCal.set(year, monthOfYear, dayOfMonth);
                    monthOfYear = monthOfYear + 1;
                    String monthOfYearText = monthOfYear + "";
                    String dayOfMonthText = dayOfMonth + "";
                    if (monthOfYearText.length() == 1) {
                        monthOfYearText = "0" + monthOfYearText;
                    }
                    if (dayOfMonthText.length() == 1) {
                        dayOfMonthText = "0" + dayOfMonthText;
                    }
                    long selectedMilli = selectedCal.getTimeInMillis();
                    try {
                        Date todaysDate = new SimpleDateFormat("yyyyMMdd").parse(dateString);
                        Date datePickerDate = new SimpleDateFormat("yyyyMMdd").parse(year + monthOfYearText + dayOfMonthText);
//                            Date datePickerDate = new Date(selectedMilli);
                        Date thisMonth = new SimpleDateFormat("yyyyMM").parse(Utils.changeDateFormat("yyyyMMdd", "yyyyMM", dateString));
                        if (datePickerDate.after(todaysDate))
                        {
//                            datePicker1.updateDate(cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH));
//                                Toast.makeText(mContext, "Can not choose future date", Toast.LENGTH_SHORT).show();
                        }
                        else
                        {
                            if (validationdate.matches("")) {
                                if (datePickerDate.before(thisMonth)) {
//                                    datePicker1.updateDate(cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH));
                                }

                            } else {
                                if (validationDateInDateFormat.before(todaysDate))//missed last date
                                {
                                    if (datePickerDate.before(thisMonth)) {
//                                        datePicker1.updateDate(cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH));
                                    }
                                } else {
                                    Date PrevMonthInDateFormat = new SimpleDateFormat("yyyy-MM").parse(previousValidationMonth);
                                    if (datePickerDate.before(PrevMonthInDateFormat)) {
//                                        datePicker1.updateDate(cal.get(Calendar.YEAR), cal.get(Calendar.MONTH), cal.get(Calendar.DAY_OF_MONTH));
                                    }
                                }
                            }
                        }
                    } catch (ParseException e) {
                        e.printStackTrace();
                    }


                }
            });

        } else {
            title.setText("Choose Product");
            final LinearLayout radioUnit = (LinearLayout) instructionDialog.findViewById(R.id.radioUnit);
            radioUnit.setVisibility(View.VISIBLE);
            RadioGroup rgp = (RadioGroup) instructionDialog.findViewById(R.id.radiogroup);
            rgp.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(RadioGroup radioGroup, int id) {
                    selectedUnit = id;
                    isUnitChosen = true;
                    unitTv.setText(unitsValList.get(selectedUnit - 1));
                    instructionDialog.dismiss();
                }
            });

            RadioGroup.LayoutParams rprms;
            if(Constants.menuDetailsObj.getYellow_card_product().isEmpty()){
                mProduct = "PPC#PSC#ARC";
            }else{
                mProduct = ""+Constants.menuDetailsObj.getYellow_card_product();
            }

            String [] mProductArray = mProduct.split("#");
            unitsValList = new ArrayList<>();
            for(int i = 0;i<mProductArray.length;i++){
                unitsValList.add(mProductArray[i]);
            }
            for (int i = 0; i < unitsValList.size(); i++) {
                RadioButton radioButton = new RadioButton(this);
                radioButton.setText(unitsValList.get(i));
                radioButton.setId(i + 1);
                radioButton.setTextColor(getResources().getColor(R.color.text_color));
                if (selectedUnit == i + 1) {
                    radioButton.setChecked(true);
                }
                rprms = new RadioGroup.LayoutParams(WindowManager.LayoutParams.WRAP_CONTENT, WindowManager.LayoutParams.WRAP_CONTENT);
                rgp.addView(radioButton, rprms);
            }
        }

        Button submit = (Button) instructionDialog.findViewById(R.id.btn_submit);
        if (type.matches("unit"))
        {
            submit.setVisibility(View.GONE);
        }
        else
        {
            submit.setOnClickListener(new View.OnClickListener()
            {
                @Override
                public void onClick(View v) {
                    if (type.matches("id"))
                    {
                        final EditText challanId = (EditText) instructionDialog.findViewById(R.id.challanId);
                        String challanIdVal = challanId.getText().toString();
                        if (challanIdVal.length() > 0) {
                            challanIdTv.setText(challanIdVal);
                            isChallanIdGiven = true;
                            instructionDialog.dismiss();
                        }
                    } else if (type.matches("quantity"))
                    {
                        final EditText challanId = (EditText) instructionDialog.findViewById(R.id.challanId);
                        String challanIdVal = challanId.getText().toString();
                        if (challanIdVal.length() > 0)
                        {
                            quantityTv.setText(challanIdVal);
                            isQuantityGiven = true;
                            instructionDialog.dismiss();
                        }
                    }
                    else if (type.matches("date"))
                    {
                        final DatePicker datePicker = (DatePicker) instructionDialog.findViewById(datePicker1);
                        int day = datePicker.getDayOfMonth();
                        int month = datePicker.getMonth() + 1;
                        int year = datePicker.getYear();
                        String monthString=month+"";
                        if(monthString.length()<2)
                        {
                            monthString="0"+monthString;
                        }
                        String strDate = day + "-" + month + "-" + year;//dd-MM-yyyy

                        String vola_month = month+"";
                        String vola_day = day+"";

                        if(vola_month.length() < 2)
                        {
                            vola_month = "0" + vola_month;
                        }
                        if(vola_day.length() < 2)
                        {
                            vola_day = "0" + vola_day;
                        }
                        String date_vola = year+""+""+vola_month+""+vola_day;
                        String date_new = year+"-"+vola_month+"-"+vola_day;

                        String strDate2 =  year+"-"+monthString;//yyyy-MM

                        if (strDate.length() > 0)
                        {
                            try
                            {
                                String lastDayOfCurrentMonth=Utils.GetLastDayOfMonth( "yyyyMMdd");
                                if(mAceDnsDatabase.isChosenYellowCardDateValid(strDate2,date_vola))
                                {
                                    isChallanDateGiven = true;
                                    challandateTv.setText(strDate);
                                }
                                else {
                                    String date = new SimpleDateFormat("yyyy-MM-dd").format(new Date());
                                    if (mAceDnsDatabase.isChosenYellowCardDateValidCustomer(Constants.selectedCustomer.getCustomerCode(), date, date_new)) {
                                        isChallanDateGiven = true;
                                        challandateTv.setText(strDate);
                                    } else {
                                        Utils.showToast(mContext, "Sorry invalid Challan Date; Please contact Admin");
                                    }
                                }


//                                Date userInputDate=new SimpleDateFormat("dd-MM-yyyy").parse(strDate);
//                                Date todaysDate=new SimpleDateFormat("yyyyMMdd").parse(dateString);
////                                Date minDate=new SimpleDateFormat("yyyyMMdd").parse(dateString);
//                                if(userInputDate.after(todaysDate))
//                                {
//                                    Toast.makeText(mContext, "Future dates can not be selected.", Toast.LENGTH_SHORT).show();
//                                    isChallanDateGiven=false;
//                                }
//                                else if(!validationdate.matches(""))
//                                {
//                                    Date minDate=new SimpleDateFormat("yyyy-MM-dd").parse(validationdate);
//                                    if(userInputDate.before(minDate))
//                                    {
////                                        Toast.makeText(mContext, "Can not choose a date older than min date.", Toast.LENGTH_SHORT).show();
//                                        isChallanDateGiven=false;
//                                    }
//                                    else
//                                    {
//
//                                    }
//                                }
//                                else
//                                {
//                                    isChallanDateGiven=true;
//                                }


                                instructionDialog.dismiss();

                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }

                        }
                    }


                }
            });
        }


        instructionDialog.show();
    }

    @Override
    public boolean onKeyUp(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            finish();
        }
        return super.onKeyUp(keyCode, event);
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

    public void finishCurrentActivity(View v) {
        finish();
    }

    public void Submit(View v) {

        Boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
        if (isTimeAutomatic) {
            if (isCustomerChosen && isChallanIdGiven && isChallanDateGiven && isQuantityGiven && isUnitChosen)
            {
                new GPSTracker(mContext);
                loader = new ProgressDialog(mContext);
                loader.setMessage("Saving Data.Please wait..");
                loader.show();
                new Thread() {
                    public void run() {
                        String timeStamp = dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        String customerCode = Constants.selectedCustomer.getCustomerCode();
                        String challanNo = challanIdTv.getText().toString();
                        String date = challandateTv.getText().toString();
                        date = Utils.changeDateFormat("dd-MM-yyyy", "yyyy-MM-dd", date);
                        String quantity = quantityTv.getText().toString();
                        String unit = unitTv.getText().toString();
                        Boolean isSuccessInsertToLocationTable, isSuccessinsertToYellowCardTable;
                        mAceDnsTransactionDatabase.beginTransaction();
                        isSuccessinsertToYellowCardTable = mAceDnsTransactionDatabase.insertToYellowCardTable("Y", timeStamp, customerCode, date, challanNo, quantity, unit);
                        isSuccessInsertToLocationTable = mAceDnsTransactionDatabase.insertToLocationTable1("Y", timeStamp);
                        if (isSuccessinsertToYellowCardTable && isSuccessInsertToLocationTable) {
                            mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                            Message msgObj = mHandler.obtainMessage();
                            Bundle b = new Bundle();
                            b.putString("message", "SubmitJobDone");
                            msgObj.setData(b);
                            mHandler.sendMessage(msgObj);
                        } else {
                            mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                            Activity activity = (Activity) mContext;
                            activity.runOnUiThread(new Runnable() {
                                public void run() {
                                    loader.cancel();
                                    if (localDataSavingFailedAttempt == 0) {
                                        Toast.makeText(mContext, "Oops! Something went wrong while saving data. please try again.", Toast.LENGTH_LONG).show();
                                        localDataSavingFailedAttempt++;
                                        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

                                    } else if (localDataSavingFailedAttempt == 1) {
                                        Toast.makeText(mContext, "Issue likely a bit serious. Try once again.", Toast.LENGTH_LONG).show();
                                        localDataSavingFailedAttempt++;
                                        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

                                    } else {
                                        Toast.makeText(mContext, "Sorry! memory related fatal exception found. Need to reenter data", Toast.LENGTH_LONG).show();
                                        Intent intent = new Intent(mContext,
                                                MenuActivity.class);
                                        startActivity(intent);
                                    }


                                }
                            });
                        }

                    }
                }.start();

            }
            else
            {
                Toast.makeText(mContext, "Please provide all the inputs properly", Toast.LENGTH_SHORT).show();
            }

        } else {
            Utils.showSettingsAlertToChangeTimeZone(mContext);
        }

    }

    public void DealerName() {
        if (mRdsCode.trim().length() > 0) {
            mDealerName = mAceDnsDatabase.getDealerName(mRdsCode);
            textViewDealerValue.setText("Linked Dealer: " + mDealerName);
        } else {
            textViewDealerValue.setText("Linked Dealer: ");
        }
    }

}
