package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.os.Bundle;
import android.text.Editable;
import android.text.InputFilter;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitLoyaltyTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.LoyaltyCustomerAdapter;
import com.forcepower.acedns.adapter.RedeemeAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.bean.LoyaltyCustomerDetails;
import com.forcepower.acedns.bean.LoyaltyPurchaseDetails;
import com.forcepower.acedns.bean.RDSDetails;
import com.forcepower.acedns.bean.RedeemeDetails;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class LoyaltyProgrammeActivity extends AceDnsParentActivity {

    Button submit, btnBack, btnVertical;
    EditText accuPoints, amount, selectCustomer;
    ImageView headerLogo, searchOpt;
    LinearLayout verticalLayout;
    TextView txtDetails;

    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;

    Dialog customerListDialog, outletDialog, verticalDialog, customerAddDialog;

    ArrayList<LoyaltyCustomerDetails> customerList, tempCustomerList;
    ArrayList<String> verticalList;
    RDSDetails selectedRds;
    String lastStr = "", selectedVertical = "";
    LoyaltyCustomerAdapter adapterCust;
    LoyaltyCustomerDetails selectedCustomer;
    String searchCriteria = "card";
    boolean newCustomer = false;
    String newCustomerName = "", newCustomerNumber = "", vehicleType = "Two Wheeler", vehicleNo = "  ";
    SchemeFreebiesDetails selectedScheme;
    LoyaltyPurchaseDetails loyaltyPurchaseStatus;
    EditText edVehicle;
    LinearLayout hsdLayout, hsdChildLayout, redeemeLayout;
    CheckBox redeemCB;
    Spinner vehicleTypeSpinner;
    String redeemePoints = "0", earnedPoints = "0";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_loyalty);
        RegisterActivities.registerActivity(this);

        mContext = LoyaltyProgrammeActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);

        initView();

        selectedRds = setupDataHelperObj.getRDSDetails(Constants.employeeDetailObject.getEmpCode());
        if (selectedRds == null) {
            Utils.directOutsideTheApplication(mContext, "RDS information is not available. Please Synchronize Data.", false);
        }
        if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
            showVerticalDialog();
        } else {
            customerList = setupDataHelperObj.getLoyaltyCustomerList();
            if (customerList.size() > 0) {
                showChooseCustomerDialog();
            } else {
                showAddCustomerDialog("");
            }
        }

    }

    public void initView() {
        headerLogo = (ImageView) findViewById(R.id.imagelogo);
        txtDetails = (TextView) findViewById(R.id.txt_details);
        if (Constants.logoBmp != null) {
            headerLogo.setVisibility(View.VISIBLE);
            headerLogo.setImageBitmap(Constants.logoBmp);
        } else {
            headerLogo.setVisibility(View.GONE);
        }

        redeemeLayout = (LinearLayout) findViewById(R.id.layout_redeeme);
        redeemCB = (CheckBox) findViewById(R.id.cb_redeeme);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        //txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        verticalLayout = (LinearLayout) findViewById(R.id.vertical_layout);
        if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
            verticalLayout.setVisibility(View.VISIBLE);
        } else {
            verticalLayout.setVisibility(View.GONE);
        }

        edVehicle = (EditText) findViewById(R.id.ed_vehicle);
        hsdLayout = (LinearLayout) findViewById(R.id.layout_hsd);

        vehicleTypeSpinner = (Spinner) findViewById(R.id.spinner1);
        final String[] carArray = {"Two Wheeler", "Car", "SUV", "Truck", "Tanker", "Trailer"};
        @SuppressWarnings("unchecked")
        ArrayAdapter spinnerAdapter = new ArrayAdapter(LoyaltyProgrammeActivity.this, R.layout.spinner_item, carArray);
        vehicleTypeSpinner.setAdapter(spinnerAdapter);
        vehicleTypeSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                vehicleType = carArray[arg2];
            }

            @Override
            public void onNothingSelected(AdapterView<?> arg0) {
            }
        });


        btnVertical = (Button) findViewById(R.id.select_vertical);
        selectCustomer = (EditText) findViewById(R.id.ed_cust);
        btnBack = (Button) findViewById(R.id.back);
        submit = (Button) findViewById(R.id.btn_submit);
        accuPoints = (EditText) findViewById(R.id.ed_point);
        amount = (EditText) findViewById(R.id.ed_amt);
        amount.setImeOptions(EditorInfo.IME_ACTION_DONE);
        amount.setOnEditorActionListener(new OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                if (event == null) {
                    String purchaseAmt = "";
                    purchaseAmt = amount.getText().toString();
                    if (actionId == EditorInfo.IME_ACTION_DONE) {
                        if (selectedScheme != null) {
                            if (Double.parseDouble(purchaseAmt) <= Double.parseDouble(selectedScheme.getSchemeValue())) {
                                if (loyaltyPurchaseStatus != null) {
                                    if (Double.parseDouble(purchaseAmt) <= (Double.parseDouble(selectedScheme.getSchemeValue()) - Double.parseDouble(loyaltyPurchaseStatus.getPurchaseValue()))) {
                                        // Entered Amt is okk.
                                    } else {
                                        amount.setText("");
                                        Toast.makeText(LoyaltyProgrammeActivity.this, "Given Amount should be less than " + (Double.parseDouble(selectedScheme.getSchemeValue()) - Double.parseDouble(loyaltyPurchaseStatus.getPurchaseValue())), Toast.LENGTH_SHORT).show();
                                    }
                                }
                            } else {
                                amount.setText("");
                                Toast.makeText(LoyaltyProgrammeActivity.this, "Given Amount should be less than " + selectedScheme.getSchemeValue(), Toast.LENGTH_SHORT).show();
                            }
                        }
                        InputMethodManager imm = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
                        imm.hideSoftInputFromWindow(amount.getWindowToken(), 0);
                        return true;
                    }
                }
                return false;
            }
        });

        btnVertical.setOnClickListener(this);
        btnBack.setOnClickListener(this);
        selectCustomer.setOnClickListener(this);
        submit.setOnClickListener(this);
    }

    public void onClick(View clkdView) {
        if (clkdView == btnBack) {
            finish();
        } else if (clkdView == submit) {
            showRedeemDialog();
        } else if (clkdView == btnVertical) {
            showVerticalDialog();
        }
    }


    public void showVerticalDialog() {
        initialiseVerticalList();
        SimpleStringAdapter adapterVertical = new SimpleStringAdapter(LoyaltyProgrammeActivity.this, R.layout.simple_list_child, verticalList);
        verticalDialog = new Dialog(LoyaltyProgrammeActivity.this, R.style.PauseDialog);
        verticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        verticalDialog.setContentView(R.layout.choose_customer_search);
        verticalDialog.setCancelable(false);
        TextView title = (TextView) verticalDialog.findViewById(R.id.title);
        title.setText("Please select a Vertical");
        EditText searchText = (EditText) verticalDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(View.GONE);
        ListView dialogList = (ListView) verticalDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterVertical);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                selectedVertical = verticalList.get(arg2);
                if (selectedVertical.equalsIgnoreCase("FMCG")) {
                    hsdLayout.setVisibility(View.GONE);
                    vehicleNo = "    ";
                    vehicleType = "    ";
                } else {
                    hsdLayout.setVisibility(View.VISIBLE);
                }

                btnVertical.setText(selectedVertical);
                selectedScheme = setupDataHelperObj.getSupportedScheme(selectedVertical);
                customerList = setupDataHelperObj.getLoyaltyCustomerList();
                if (customerList.size() > 0) {
                    verticalDialog.cancel();
                    showChooseCustomerDialog();
                } else {
                    verticalDialog.cancel();
                    showAddCustomerDialog("");
                }
            }
        });
        Button addCustomer = (Button) verticalDialog.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        verticalDialog.show();
    }


    public void showChooseCustomerDialog() {
        tempCustomerList = new ArrayList<LoyaltyCustomerDetails>();
        searchCriteria = "card";
        reInitialiseCustomerList();
        adapterCust = new LoyaltyCustomerAdapter(LoyaltyProgrammeActivity.this, R.layout.customer_list_child, tempCustomerList);
        customerListDialog = new Dialog(LoyaltyProgrammeActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);
        TextView title = (TextView) customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Customer");
        final EditText searchText = (EditText) customerListDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.setSingleLine(true);
        int maxLength = 8;
        InputFilter[] fArray = new InputFilter[1];
        fArray[0] = new InputFilter.LengthFilter(maxLength);
        searchText.setFilters(fArray);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                String str = s.toString();
                if (lastStr.length() > str.length()) {
                    reInitialiseCustomerList();
                }
                lastStr = str;
                filterCustomerArray(str.length(), str);
                adapterCust.notifyDataSetChanged();
            }
        });

        ListView dialogList = (ListView) customerListDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                selectedCustomer = tempCustomerList.get(arg2);
                loyaltyPurchaseStatus = setupDataHelperObj.getLoyaltyPurchaseDetails(selectedCustomer.getCardNumber(), selectedVertical);
                if (loyaltyPurchaseStatus != null) {
                    getActualRewardPoint();
                }
                vehicleNo = selectedCustomer.getVehicleNo();
                vehicleType = selectedCustomer.getCardType();
                accuPoints.setText(selectedCustomer.getRewardPoint());
                if (canRedeeme()) {
                    redeemeLayout.setVisibility(View.VISIBLE);
                }
                customerListDialog.cancel();
                selectCustomer.setText(selectedCustomer.getCardHolderName());
                txtDetails.setText("Card Number : " + selectedCustomer.getCardNumber());

                selectCustomer.setEnabled(false);
            }
        });
        Button addCustomer = (Button) customerListDialog.findViewById(R.id.btn_add);
        addCustomer.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                customerListDialog.cancel();
                showAddCustomerDialog(searchText.getText().toString() != null ? searchText.getText().toString() : "");
            }
        });
        final ImageView searchOpt = (ImageView) customerListDialog.findViewById(R.id.imageView2);
        searchOpt.setTag(R.drawable.loyalty);
        searchOpt.setVisibility(View.VISIBLE);
        searchOpt.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                ImageView imageView = (ImageView) view;
                Integer integer = (Integer) imageView.getTag();
                integer = (integer == null ? 0 : integer);
                switch (integer) {
                    case R.drawable.loyalty:
                        imageView.setBackgroundResource(R.drawable.cust_bg);
                        imageView.setTag(R.drawable.cust_bg);
                        searchCriteria = "name";
                        break;
                    case R.drawable.cust_bg:
                        imageView.setBackgroundResource(R.drawable.phone);
                        imageView.setTag(R.drawable.phone);
                        searchCriteria = "phone";
                        break;
                    case R.drawable.phone:
                        imageView.setBackgroundResource(R.drawable.loyalty);
                        imageView.setTag(R.drawable.loyalty);
                        searchCriteria = "card";
                        break;
                }
            }
        });
        customerListDialog.show();
    }

    public void showAddCustomerDialog(final String details) {
        boolean show = true;
        for (int i = 0; i < details.length(); i++) {
            char charAt2 = details.charAt(i);
            if (Character.isLetter(charAt2) || Character.isDigit(charAt2)) {
                System.out.println(charAt2 + "is a alphabet");
            } else {
                show = false;
            }
        }
        if (show) {
            Constants.isSelectCustomer = false;
            customerAddDialog = new Dialog(LoyaltyProgrammeActivity.this, R.style.PauseDialog);
            customerAddDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            customerAddDialog.setContentView(R.layout.add_loyalty_customer_dialog);
            customerAddDialog.setCancelable(false);
            TextView title = (TextView) customerAddDialog.findViewById(R.id.title);
            TextView txtPhone = (TextView) customerAddDialog.findViewById(R.id.txt_phone);
            txtPhone.setText("Phone No");
            TextView txtAddress = (TextView) customerAddDialog.findViewById(R.id.txt_address);
            txtAddress.setText("Address");
            title.setText("Please provide the details of the new Customer");
            final EditText edName = (EditText) customerAddDialog.findViewById(R.id.ed_name);
            final EditText edNumber = (EditText) customerAddDialog.findViewById(R.id.ed_number);
            edNumber.setInputType(InputType.TYPE_CLASS_TEXT);
            int maxLength11 = 8;
            InputFilter[] fArray11 = new InputFilter[1];
            fArray11[0] = new InputFilter.LengthFilter(maxLength11);
            edNumber.setFilters(fArray11);
            final EditText edPhone = (EditText) customerAddDialog.findViewById(R.id.ed_area);
            edPhone.setInputType(InputType.TYPE_CLASS_NUMBER);
            int maxLength = 10;
            InputFilter[] fArray = new InputFilter[1];
            fArray[0] = new InputFilter.LengthFilter(maxLength);
            edPhone.setFilters(fArray);
            if (searchCriteria.equalsIgnoreCase("name")) {
                edName.setText(details);
            } else if (searchCriteria.equalsIgnoreCase("card")) {
                edNumber.setText(details);
            } else if (searchCriteria.equalsIgnoreCase("phone")) {
                edPhone.setText(details);
            }
            final EditText edAddress = (EditText) customerAddDialog.findViewById(R.id.ed_pin);
            edAddress.setInputType(InputType.TYPE_CLASS_TEXT);
            int maxLength1 = 20;
            InputFilter[] fArray1 = new InputFilter[1];
            fArray1[0] = new InputFilter.LengthFilter(maxLength1);
            edAddress.setFilters(fArray1);


            Button btnSubmit = (Button) customerAddDialog.findViewById(R.id.btn_submit);
            Button btnCancel = (Button) customerAddDialog.findViewById(R.id.btn_cancel);
            btnSubmit.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    newCustomer = true;
                    String name = "", number = "", phone = "", address = "";
                    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    name = edName.getText().toString().replace("\n", "");
                    name = getFormatString(name);
                    number = edNumber.getText().toString().replace("\n", "");
                    phone = edPhone.getText().toString().replace("\n", "");
                    address = edAddress.getText().toString().replace("\n", "");

                    boolean numberEntered = true;
                    for (int i = 0; i < number.length(); i++) {
                        char charAt2 = number.charAt(i);
                        if (Character.isLetter(charAt2) || Character.isDigit(charAt2)) {
                            System.out.println(charAt2 + "is a alphabet");
                        } else {
                            numberEntered = false;
                        }
                    }


                    if (numberEntered && name.length() > 0 && number.length() > 0 && phone.length() > 0 && address.length() > 0 && vehicleNo.length() > 0 && vehicleType.length() > 0) {
                        newCustomerName = name.replace("\n", "");
                        ;
                        newCustomerNumber = number.replace("\n", "");
                        ;
                        selectCustomer.setText(number);
                        selectCustomer.setEnabled(false);
                        txtDetails.setText("Customer Name : " + name);
                        customerAddDialog.cancel();

                        LoyaltyCustomerDetails newObj = new LoyaltyCustomerDetails();
                        String custCode = "L" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                        newObj.setCardHolderCode(custCode);
                        newObj.setCardHolderName(newCustomerName);
                        newObj.setCardNumber(newCustomerNumber);
                        String currentDate = Constants.dateString.substring(0, 4) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(6, 8) + " ";
                        String timeStamp11 = currentDate + new SimpleDateFormat("HH:mm:ss").format(Calendar.getInstance().getTime());
                        newObj.setLastUpdate(timeStamp11);
                        newObj.setPurchaseValue("0");
                        newObj.setRewardPoint("0");
                        newObj.setRedeemed("no");
                        newObj.setPhone(phone);
                        newObj.setAddress(address);
                        newObj.setCardType("");
                        newObj.setVehicleNo("");
                        try {
                            Date todatDate = new SimpleDateFormat("yyyyMMdd").parse(Constants.dateString);
                            Calendar cal = Calendar.getInstance();
                            cal.setTime(todatDate);
                            cal.add(Calendar.YEAR, 1);
                            String expiryDate = new SimpleDateFormat("yyyy-MM-dd").format(cal.getTime());
                            newObj.setCardExpDate(expiryDate);
                        } catch (Exception e) {
                            newObj.setCardExpDate("2016-03-31");
                        }
                        transDataHelperObj.insertToCardHolder(newObj);
                    } else {
                        Toast.makeText(mContext, "Provide a valid Customer Name,Card Number and Phone Number", Toast.LENGTH_LONG).show();
                    }
                }
            });
            btnCancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    customerAddDialog.cancel();
                }
            });

            customerAddDialog.show();
        } else {
            Toast.makeText(mContext, "Loyalty Card Number contains invalid characters.Enter again.", Toast.LENGTH_LONG).show();
        }
    }


    public void reInitialiseCustomerList() {
        tempCustomerList.removeAll(tempCustomerList);
        int size = tempCustomerList.size();
        int size1 = customerList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < customerList.size(); kk++) {
            tempCustomerList.add(customerList.get(kk));
        }

    }


    public void filterCustomerArray(int strCnt, String charVal) {
        int size = tempCustomerList.size();
        boolean status = false;
        for (int ii = 0; ii < size; ii++) {
            if (searchCriteria.equalsIgnoreCase("name")) {
                if (tempCustomerList.get(ii).getCardHolderName().length() >= strCnt) {
                    if (tempCustomerList.get(ii).getCardHolderName().length() == strCnt) {
                        status = true;
                    }
                    if (tempCustomerList.get(ii).getCardHolderName().substring(0, strCnt).equalsIgnoreCase(charVal)) {
                    } else {
                        tempCustomerList.remove(tempCustomerList.get(ii));
                        size = size - 1;
                        ii = ii - 1;
                    }
                } else {
                    tempCustomerList.remove(tempCustomerList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else if (searchCriteria.equalsIgnoreCase("card")) {
                if (tempCustomerList.get(ii).getCardNumber().length() >= strCnt) {
                    if (tempCustomerList.get(ii).getCardNumber().length() == strCnt) {
                        status = true;
                    }
                    if (tempCustomerList.get(ii).getCardNumber().substring(0, strCnt).equalsIgnoreCase(charVal)) {
                    } else {
                        tempCustomerList.remove(tempCustomerList.get(ii));
                        size = size - 1;
                        ii = ii - 1;
                    }
                } else {
                    tempCustomerList.remove(tempCustomerList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else if (searchCriteria.equalsIgnoreCase("phone")) {
                if (tempCustomerList.get(ii).getPhone().length() >= strCnt) {
                    if (tempCustomerList.get(ii).getPhone().length() == strCnt) {
                        status = true;
                    }
                    if (tempCustomerList.get(ii).getPhone().substring(0, strCnt).equalsIgnoreCase(charVal)) {
                    } else {
                        tempCustomerList.remove(tempCustomerList.get(ii));
                        size = size - 1;
                        ii = ii - 1;
                    }
                } else {
                    tempCustomerList.remove(tempCustomerList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            }
        }

        if (tempCustomerList.size() == 1 && status) {
            if (searchCriteria.equalsIgnoreCase("name")) {
                customerListDialog.cancel();
                selectedCustomer = tempCustomerList.get(0);
                loyaltyPurchaseStatus = setupDataHelperObj.getLoyaltyPurchaseDetails(selectedCustomer.getCardNumber(), selectedVertical);
                if (loyaltyPurchaseStatus != null) {
                    getActualRewardPoint();
                }
                accuPoints.setText(selectedCustomer.getRewardPoint());
                if (canRedeeme()) {
                    redeemeLayout.setVisibility(View.VISIBLE);
                }

                selectCustomer.setText(selectedCustomer.getCardHolderName());
                txtDetails.setText("Card Number : " + selectedCustomer.getCardNumber());
                selectCustomer.setEnabled(false);
            } else if (searchCriteria.equalsIgnoreCase("card")) {
                customerListDialog.cancel();
                selectedCustomer = tempCustomerList.get(0);
                loyaltyPurchaseStatus = setupDataHelperObj.getLoyaltyPurchaseDetails(selectedCustomer.getCardNumber(), selectedVertical);
                if (loyaltyPurchaseStatus != null) {
                    getActualRewardPoint();
                }
                accuPoints.setText(selectedCustomer.getRewardPoint());
                if (canRedeeme()) {
                    redeemeLayout.setVisibility(View.VISIBLE);
                }

                selectCustomer.setText(selectedCustomer.getCardHolderName());
                txtDetails.setText("Card Number : " + selectedCustomer.getCardNumber());
                selectCustomer.setEnabled(false);
            } else if (searchCriteria.equalsIgnoreCase("phone")) {
                customerListDialog.cancel();
                selectedCustomer = tempCustomerList.get(0);
                loyaltyPurchaseStatus = setupDataHelperObj.getLoyaltyPurchaseDetails(selectedCustomer.getCardNumber(), selectedVertical);
                if (loyaltyPurchaseStatus != null) {
                    getActualRewardPoint();
                }
                accuPoints.setText(selectedCustomer.getRewardPoint());
                if (canRedeeme()) {
                    redeemeLayout.setVisibility(View.VISIBLE);
                }
                selectCustomer.setText(selectedCustomer.getCardHolderName());
                txtDetails.setText("Card Number : " + selectedCustomer.getCardNumber());
                selectCustomer.setEnabled(false);
            }
            //loyaltyPurchaseStatus = mAceDnsDatabase.getLoyaltyPurchaseDetails(selectedCustomer.getCardNumber(),selectedVertical);
        }
    }


    public void initialiseVerticalList() {
        verticalList = new ArrayList<String>();
        String[] verticalArray = Constants.userDetailsObj.getVerticalFieldsValue().split(",");
        for (int ii = 0; ii < verticalArray.length; ii++) {
            verticalList.add(verticalArray[ii]);
        }
    }


    public String getFormatString(String unformatString) {
        String formatString = "";
        StringBuffer res = new StringBuffer();

        String[] strArr = unformatString.split(" ");
        for (String str : strArr) {
            char[] stringArray = str.trim().toCharArray();
            for (int ii = 0; ii < stringArray.length; ii++) {
                if (ii == 0) {
                    stringArray[ii] = Character.toUpperCase(stringArray[ii]);
                } else {
                    stringArray[ii] = Character.toLowerCase(stringArray[ii]);
                }
            }
            str = new String(stringArray);
            res.append(str).append(" ");
        }
        formatString = res.toString().trim();
        return formatString;
    }

    public void showRedeemDialog() {

        if (newCustomer == true) {
            redeemePoints = "0";
            submitLoyaltyData();
        } else {
            final ArrayList<RedeemeDetails> redeemList = setupDataHelperObj.getRedeemeDetails(selectedCustomer.getRewardPoint());

            if (redeemList.size() > 0 && redeemCB.isChecked()) {
                RedeemeAdapter adapterRedeem = new RedeemeAdapter(LoyaltyProgrammeActivity.this, R.layout.redeeme_child, redeemList);
                final Dialog redeemeDialog = new Dialog(LoyaltyProgrammeActivity.this, R.style.PauseDialog);
                redeemeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                redeemeDialog.setContentView(R.layout.choose_customer_search);
                redeemeDialog.setCancelable(false);
                TextView title = (TextView) redeemeDialog.findViewById(R.id.title);
                title.setText("Would you like to Redeeme your Points ?");
                EditText searchText = (EditText) redeemeDialog.findViewById(R.id.autoCompleteTextView1);
                searchText.setVisibility(View.GONE);
                ListView dialogList = (ListView) redeemeDialog.findViewById(R.id.list);
                dialogList.setAdapter(adapterRedeem);
                dialogList.setOnItemClickListener(new OnItemClickListener() {
                    @Override
                    public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                        redeemePoints = String.valueOf(redeemList.get(arg2).getPoints());
                        redeemeDialog.cancel();
                        submitLoyaltyData();
                    }
                });
                Button addCustomer = (Button) redeemeDialog.findViewById(R.id.btn_add);
                addCustomer.setText("  May be later  ");
                addCustomer.setOnClickListener(new OnClickListener() {
                    @Override
                    public void onClick(View v) {
                        redeemeDialog.cancel();
                        submitLoyaltyData();
                    }
                });
                redeemeDialog.show();
            } else {
                submitLoyaltyData();
            }

        }


    }

    public void submitLoyaltyData() {
        if (selectedRds != null) {
            if (selectedCustomer != null || newCustomer) {
                String purchaseAmt = "";
                purchaseAmt = amount.getText().toString();
                if (!selectedVertical.equalsIgnoreCase("FMCG")) {
                    vehicleNo = edVehicle.getText().toString();
                }
                if (purchaseAmt.length() > 0 && Double.parseDouble(purchaseAmt) > 0) {
                    earnedPoints = calculatePointsEarned(purchaseAmt);
                    if (selectedScheme != null && Double.parseDouble(purchaseAmt) <= Double.parseDouble(selectedScheme.getSchemeValue())) {
                        if (loyaltyPurchaseStatus != null && Double.parseDouble(purchaseAmt) <= (Double.parseDouble(selectedScheme.getSchemeValue()) - Double.parseDouble(loyaltyPurchaseStatus.getPurchaseValue()))) {
                            if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
                                if (selectedVertical.length() > 0) {
                                    submit.setEnabled(false);
                                    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                    transDataHelperObj.insertToLocationTable("L", timeStamp);
                                    transDataHelperObj.insertToCardTransaction(timeStamp, selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber,
                                            selectedRds.getRdsCode(), purchaseAmt, selectedVertical, vehicleNo, vehicleType,
                                            earnedPoints, redeemePoints);
                                    String newPurchase = getNewPurchase(purchaseAmt);
                                    String newRewardPoint = getNewRewardPoint(earnedPoints);
                                    String newRedeemedPoint = getNewRedeemedPoint(redeemePoints);
                                    transDataHelperObj.updateLoyaltyPurchase(loyaltyPurchaseStatus.getLoyaltyCardNo(), selectedVertical, newPurchase, newRewardPoint, newRedeemedPoint);
                                    new TRANS_SubmitLoyaltyTask(LoyaltyProgrammeActivity.this).execute();
                                } else {
                                    Toast.makeText(LoyaltyProgrammeActivity.this, "Please provide a Vertical", Toast.LENGTH_SHORT).show();
                                }
                            } else {
                                submit.setEnabled(false);
                                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                transDataHelperObj.insertToLocationTable("L", timeStamp);
                                transDataHelperObj.insertToCardTransaction(timeStamp, selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber,
                                        selectedRds.getRdsCode(), purchaseAmt, selectedVertical, vehicleNo, vehicleType,
                                        earnedPoints, redeemePoints);
                                String newPurchase = getNewPurchase(purchaseAmt);
                                String newRewardPoint = getNewRewardPoint(earnedPoints);
                                String newRedeemedPoint = getNewRedeemedPoint(redeemePoints);
                                transDataHelperObj.updateLoyaltyPurchase(loyaltyPurchaseStatus.getLoyaltyCardNo(), selectedVertical, newPurchase, newRewardPoint, newRedeemedPoint);
                                new TRANS_SubmitLoyaltyTask(LoyaltyProgrammeActivity.this).execute();
                            }
                        } else if (loyaltyPurchaseStatus == null) {
                            if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
                                if (selectedVertical.length() > 0) {
                                    submit.setEnabled(false);
                                    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                    transDataHelperObj.insertToLocationTable("L", timeStamp);
                                    transDataHelperObj.insertToCardTransaction(timeStamp, selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber,
                                            selectedRds.getRdsCode(), purchaseAmt, selectedVertical, vehicleNo, vehicleType,
                                            earnedPoints, redeemePoints);
                                    transDataHelperObj.insertLoyaltyPurchase(selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber, selectedVertical, purchaseAmt, earnedPoints, redeemePoints);
                                    new TRANS_SubmitLoyaltyTask(LoyaltyProgrammeActivity.this).execute();
                                } else {
                                    Toast.makeText(LoyaltyProgrammeActivity.this, "Please provide a Vertical", Toast.LENGTH_SHORT).show();
                                }
                            } else {
                                submit.setEnabled(false);
                                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                transDataHelperObj.insertToLocationTable("L", timeStamp);
                                transDataHelperObj.insertToCardTransaction(timeStamp, selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber,
                                        selectedRds.getRdsCode(), purchaseAmt, selectedVertical, vehicleNo, vehicleType,
                                        earnedPoints, redeemePoints);
                                transDataHelperObj.insertLoyaltyPurchase(selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber, selectedVertical, purchaseAmt, earnedPoints, redeemePoints);
                                new TRANS_SubmitLoyaltyTask(LoyaltyProgrammeActivity.this).execute();
                            }
                        } else {
                            Toast.makeText(LoyaltyProgrammeActivity.this, "Given Amount should be less than " + (Double.parseDouble(selectedScheme.getSchemeValue()) - Double.parseDouble(loyaltyPurchaseStatus.getPurchaseValue())), Toast.LENGTH_SHORT).show();
                        }
                    } else if (selectedScheme == null) {
                        if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
                            if (selectedVertical.length() > 0) {
                                submit.setEnabled(false);
                                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                transDataHelperObj.insertToLocationTable("L", timeStamp);
                                transDataHelperObj.insertToCardTransaction(timeStamp, selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber,
                                        selectedRds.getRdsCode(), purchaseAmt, selectedVertical, vehicleNo, vehicleType,
                                        earnedPoints, redeemePoints);
                                if (loyaltyPurchaseStatus != null) {
                                    String newPurchase = getNewPurchase(purchaseAmt);
                                    String newRewardPoint = getNewRewardPoint(earnedPoints);
                                    String newRedeemedPoint = getNewRedeemedPoint(redeemePoints);
                                    transDataHelperObj.updateLoyaltyPurchase(loyaltyPurchaseStatus.getLoyaltyCardNo(), selectedVertical, newPurchase, newRewardPoint, newRedeemedPoint);
                                } else {
                                    transDataHelperObj.insertLoyaltyPurchase(selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber, selectedVertical, purchaseAmt, earnedPoints, redeemePoints);
                                }
                                new TRANS_SubmitLoyaltyTask(LoyaltyProgrammeActivity.this).execute();
                            } else {
                                Toast.makeText(LoyaltyProgrammeActivity.this, "Please provide a Vertical", Toast.LENGTH_SHORT).show();
                            }
                        } else {
                            submit.setEnabled(false);
                            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                            transDataHelperObj.insertToLocationTable("L", timeStamp);
                            transDataHelperObj.insertToCardTransaction(timeStamp, selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber,
                                    selectedRds.getRdsCode(), purchaseAmt, selectedVertical, vehicleNo, vehicleType,
                                    earnedPoints, redeemePoints);
                            if (loyaltyPurchaseStatus != null) {
                                String newPurchase = getNewPurchase(purchaseAmt);
                                String newRewardPoint = getNewRewardPoint(earnedPoints);
                                String newRedeemedPoint = getNewRedeemedPoint(redeemePoints);
                                transDataHelperObj.updateLoyaltyPurchase(loyaltyPurchaseStatus.getLoyaltyCardNo(), selectedVertical, newPurchase, newRewardPoint, newRedeemedPoint);
                            } else {
                                transDataHelperObj.insertLoyaltyPurchase(selectedCustomer != null ? selectedCustomer.getCardNumber() : newCustomerNumber, selectedVertical, purchaseAmt, earnedPoints, redeemePoints);
                            }
                            new TRANS_SubmitLoyaltyTask(LoyaltyProgrammeActivity.this).execute();
                        }
                    } else {
                        Toast.makeText(LoyaltyProgrammeActivity.this, "Given Amount should be less than " + selectedScheme.getSchemeValue(), Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(LoyaltyProgrammeActivity.this, "Please provide a valid Vehicle No and Amount", Toast.LENGTH_SHORT).show();
                }
            } else {
                Toast.makeText(LoyaltyProgrammeActivity.this, "Please select a Customer", Toast.LENGTH_SHORT).show();
            }
        }
    }

    public String calculatePointsEarned(String purchaseAmount) {
        double amt = Double.parseDouble(purchaseAmount);
        double point = amt / 100;
        return String.valueOf(point);
    }

    public String getNewPurchase(String purchaseAmount) {
        double amt = Double.parseDouble(purchaseAmount);
        double oldAmt = Double.parseDouble(loyaltyPurchaseStatus.getPurchaseValue());
        double point = amt + oldAmt;
        return String.valueOf(point);
    }

    public String getNewRewardPoint(String reward) {
        double amt = Double.parseDouble(reward);
        double oldAmt = Double.parseDouble(loyaltyPurchaseStatus.getRwrdPoint());
        double point = amt + oldAmt;
        return String.valueOf(point);
    }

    public String getNewRedeemedPoint(String redeemed) {
        double amt = Double.parseDouble(redeemed);
        double oldAmt = Double.parseDouble(loyaltyPurchaseStatus.getRdmdPoint());
        double point = amt + oldAmt;
        return String.valueOf(point);
    }

    public void getActualRewardPoint() {
        double rwrdPoint = Double.parseDouble(loyaltyPurchaseStatus.getRwrdPoint());
        double rdmdPoint = Double.parseDouble(loyaltyPurchaseStatus.getRdmdPoint());
        selectedCustomer.setRewardPoint(String.valueOf(rwrdPoint - rdmdPoint));
    }

    public boolean canRedeeme() {
        final ArrayList<RedeemeDetails> redeemList = setupDataHelperObj.getRedeemeDetails(selectedCustomer.getRewardPoint());
        if (redeemList.size() > 0) {
            return true;
        }
        return false;
    }
}