package com.forcepower.acedns.activity;

import android.app.AlertDialog;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.QuotationProductsListAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitQuotationDetailsTask;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.QuotationDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.QuotationDetailsList;
import static com.forcepower.acedns.constants.Constants.defaultFormat;

public class QuotationAddActivity extends AceDnsParentActivity {
    static AceDnsDatabase mAceDnsDatabase;
    ArrayList<String> filterList;
    int filterNo, chosenCustPos, lastProdPos = 0;
    Context mContext;
    ProductMasterAdapter prodAdapter;
    ProductGrpAdapter groupAdapter;
    ProductSubGrpAdapter subGroupAdapter;
    ProductBrandAdapter brandAdapter;
    LinearLayout filterLayout;
    ArrayList<Button> filterButtonList;
    String currentProdDescription, currentProductCode, quotationValidTill, lastStr = "", contactPersonName = "", contactPersonPhone = "", contactPersonEmail = "";
    QuotationProductsListAdapter QuotationProductsListAdapterObject;
    ListView quotationListViewObject;
    TextView txt_total, currentProdDescTV;
    EditText customerNameET, customerAddressET, customerPhoneET, customerEmailET;
    ArrayList<CustomerDetails> customerList;
    ArrayList<CustomerDetails> tempCustomerList;
    CustomerAdapter adapterCust;
    Button quotationValidityDate, back;
    DecimalFormat formatInt = new DecimalFormat("00");
    Handler orderDataHandler;
    ProgressDialog ploader;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected,
            lastProductSelected = false;
    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;
    ArrayList<ProductMasterDetails> tempProductList;
    ArrayList<ProductGroupDetails> tempProductGroupList;
    ArrayList<ProductSubGrpDetails> tempProductSubGroupList;
    ArrayList<ProductBrandDetails> tempProductBrandList;
    boolean carryInSales = false;
    boolean rpeatedProductEntry = false;
    AceDnsTransactionDatabase dataHelperObj;
    int localDataSavingFailedAttempt = 0;
    ListView dialogList;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_quotation_add);
        mContext = this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        tempCustomerList = new ArrayList<>();
        customerList = mAceDnsDatabase.getCustomerList();

        txt_total = (TextView) findViewById(R.id.txt_total);
        Utils.getAppVersionDbVersion((TextView) findViewById(R.id.txt_version), mContext);
        Utils.getAppLogo((ImageView) findViewById(R.id.imagelogo));
        customerNameET = (EditText) findViewById(R.id.customerNameET);
        customerAddressET = (EditText) findViewById(R.id.customerAddressET);
        customerAddressET.setEnabled(false);
        customerPhoneET = (EditText) findViewById(R.id.customerPhoneET);
        customerPhoneET.setEnabled(false);
        customerEmailET = (EditText) findViewById(R.id.customerEmailET);
        quotationValidityDate = (Button) findViewById(R.id.quotationValidityDate);
        back = (Button) findViewById(R.id.back);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
        if (customerList.size() < 1) {
            Utils.showToast(mContext, "No customer found in database.");
        } else if (customerList.size() == 1) {
            customerNameET.setText(customerList.get(0).getCustomerName());
            customerNameET.setSelection(customerNameET.getText().length());
            customerAddressET.setText(customerList.get(0).getAddress());
            customerAddressET.setSelection(customerAddressET.getText().length());
            customerPhoneET.setText(customerList.get(0).getNumber());
            customerPhoneET.setSelection(customerPhoneET.getText().length());
            customerEmailET.setText(customerList.get(0).getEmail());
        } else {
            reInitialiseCustomerList();
            adapterCust = new CustomerAdapter(mContext, R.layout.customer_list_child, tempCustomerList);
            showChooseCustomerDialog();
        }

        QuotationDetailsList = new ArrayList<>();
        Constants.selectedProductMasterList = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();
        filterButtonList = new ArrayList<>();

        if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes"))//this is supposed to be a temp solution
        {
            filterNo = 1;
        } else {
            filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        }
        QuotationProductsListAdapterObject = new QuotationProductsListAdapter(mContext, R.layout.quotation_list_item, QuotationDetailsList);
        quotationListViewObject = (ListView) findViewById(R.id.quotationListView);
        quotationListViewObject.setOnItemLongClickListener(new AdapterView.OnItemLongClickListener() {
            @Override
            public boolean onItemLongClick(AdapterView<?> arg0, View arg1,
                                           int pos, long id) {
                showRemoveItemAlertDialog(pos);
                return true;
            }
        });
        quotationListViewObject.setAdapter(QuotationProductsListAdapterObject);

        orderDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                QuotationAddActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                showGrpListDialog();
                                break;
                            case 2:
                                showSubGrpListDialog();
                                break;
                            case 3:
                                showBrandListDialog();
                                break;
                            case 4:
                                showMasterListDialog();
                                break;
                        }
                    }
                });
            }
        };
    }

    public void chooseValidity(View v) {
        openDatePicker();
    }

    public void AddItem(View v) {
        AddQuotationDialog();
    }

    public void submitQuotation(View v) {
        if (QuotationDetailsList.size() > 0) {
            if (customerNameET.getText().toString().trim().length() < 1) {
                Utils.showToast(mContext, "Please provide customer name.");
                return;
            }
//            int phoneNumberLength = customerPhoneET.getText().toString().trim().length();
//            if(phoneNumberLength <8)
//            {
//                Utils.showToast(mContext,"Please provide proper customer phone number.");
//                return;
//            }
            if (!Utils.isValidMail(customerEmailET.getText().toString().trim())) {
                Utils.showToast(mContext, "Please provide proper email.");
                return;
            }
            AddContactPersonDetailsDialog();
        } else {
            Toast.makeText(mContext, "Please add at least one item.", Toast.LENGTH_SHORT).show();
        }

    }

    public void AddQuotationDialog() {
        final Dialog addQuotationDialog = new Dialog(QuotationAddActivity.this, R.style.PauseDialog);
        addQuotationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        addQuotationDialog.setContentView(R.layout.dialog_add_new_quotation);
        addQuotationDialog.setCancelable(true);
        filterLayout = (LinearLayout) addQuotationDialog.findViewById(R.id.filter_layout);
        final TextView et_qty = (TextView) addQuotationDialog.findViewById(R.id.et_qty);
        final TextView et_price = (TextView) addQuotationDialog.findViewById(R.id.et_price);
        final TextView et_taxes = (TextView) addQuotationDialog.findViewById(R.id.et_taxes);
        currentProdDescTV = (TextView) addQuotationDialog.findViewById(R.id.currentProdDescTV);

        drawFilterLayout();

        Button btn_submit = (Button) addQuotationDialog.findViewById(R.id.btn_submit);
        btn_submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                String qtyInput = et_qty.getText().toString();
                String priceInput = et_price.getText().toString();
//                String taxInput=et_taxes.getText().toString();
                String taxInput = "0.0";
                if (!Utils.isNumeric(qtyInput)) {
                    Toast.makeText(mContext, "Please provide valid quantity.", Toast.LENGTH_SHORT).show();
                    return;
                }
                if (!Utils.isNumeric(priceInput)) {
                    Toast.makeText(mContext, "Please provide valid price input.", Toast.LENGTH_SHORT).show();
                    return;
                }
                if (!Utils.isNumeric(taxInput)) {
                    Toast.makeText(mContext, "Please provide valid tax input.", Toast.LENGTH_SHORT).show();
                    return;
                }
                double qtyInDouble = Double.parseDouble(qtyInput);
                double priceInDouble = Double.parseDouble(priceInput);
                double taxInDouble = Double.parseDouble(taxInput);
                QuotationDetails QuotationDetailsObject = new QuotationDetails();
                QuotationDetailsObject.setQuotationProductCode(currentProductCode);
                QuotationDetailsObject.setQuotationProductDesc(currentProdDescription);
                QuotationDetailsObject.setQuotationQuantity(defaultFormat.format(qtyInDouble));
                QuotationDetailsObject.setQuotationProductRate(defaultFormat.format(priceInDouble));
                QuotationDetailsObject.setQuotationProductTax(defaultFormat.format(taxInDouble));

                double amountWithoutTax = qtyInDouble * priceInDouble;
                double tax = amountWithoutTax * (taxInDouble / 100);

                double amount = amountWithoutTax + tax;
                QuotationDetailsObject.setQuotationProductAmount(defaultFormat.format(amount));
                QuotationDetailsList.add(QuotationDetailsObject);
                refreshListViewAndTotalAmount();
                currentProdDescription = "";
                currentProductCode = "";
                addQuotationDialog.dismiss();
            }
        });
        addQuotationDialog.show();
    }

    public void AddContactPersonDetailsDialog() {
        final Dialog QuotationContactPersonDialog = new Dialog(QuotationAddActivity.this, R.style.PauseDialog);
        QuotationContactPersonDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        QuotationContactPersonDialog.setContentView(R.layout.dialog_quotation_contact_person);
        QuotationContactPersonDialog.setCancelable(true);
        filterLayout = (LinearLayout) QuotationContactPersonDialog.findViewById(R.id.filter_layout);
        final TextView et_name = (TextView) QuotationContactPersonDialog.findViewById(R.id.et_name);
        final TextView et_phone = (TextView) QuotationContactPersonDialog.findViewById(R.id.et_phone);
        final TextView et_email = (TextView) QuotationContactPersonDialog.findViewById(R.id.et_email);
        et_name.setText(Constants.employeeDetailObject.getEmpName().trim());
        Button btn_submit = (Button) QuotationContactPersonDialog.findViewById(R.id.btn_submit);
        btn_submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                contactPersonName = et_name.getText().toString().trim();
                contactPersonPhone = et_phone.getText().toString().trim();
                contactPersonEmail = et_email.getText().toString().trim();
                if (contactPersonName.length() < 1) {
                    Utils.showToast(mContext, "Please provide contact person name.");
                    return;
                }
                if (contactPersonPhone.length() < 8) {
                    Utils.showToast(mContext, "Please provide proper contact person phone number.");
                    return;
                }
                if (!Utils.isValidMail(contactPersonEmail)) {
                    Utils.showToast(mContext, "Please provide proper contact person email.");
                    return;
                }
                QuotationContactPersonDialog.dismiss();
                Boolean isSuccessInsertToQuotationHeaderTable, isSuccessInsertToQuotationDetailsTable, isSuccessInsertToLocationTable;
                dataHelperObj = new AceDnsTransactionDatabase(mContext);
                new GPSTracker(mContext);
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                String prefix = "Q";
                dataHelperObj.beginTransaction();
                isSuccessInsertToLocationTable = dataHelperObj.insertToLocationTable1(prefix, timeStamp);
                isSuccessInsertToQuotationHeaderTable = dataHelperObj.insertToQuotationHeaderTable(prefix,
                        timeStamp, new SimpleDateFormat("dd/MM/yyyy").format(Calendar.getInstance().getTime()), customerNameET.getText().toString(),
                        customerAddressET.getText().toString(), customerEmailET.getText().toString(), customerPhoneET.getText().toString(), contactPersonName, contactPersonPhone, contactPersonEmail, quotationValidTill, txt_total.getText().toString());
                isSuccessInsertToQuotationDetailsTable = dataHelperObj.insertToQuotationDetailsTable(timeStamp);
                if (isSuccessInsertToLocationTable && isSuccessInsertToQuotationHeaderTable && isSuccessInsertToQuotationDetailsTable) {
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                } else {
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                    if (localDataSavingFailedAttempt == 0) {
                        Toast.makeText(mContext, "Oops! Something went wrong while saving data. please try again.", Toast.LENGTH_LONG).show();
                        localDataSavingFailedAttempt++;

                    } else if (localDataSavingFailedAttempt == 1) {
                        Toast.makeText(mContext, "Issue likely a bit serious. Try once again.", Toast.LENGTH_LONG).show();
                        localDataSavingFailedAttempt++;
//								dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false,true);
                    } else {
//								dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false,true);
                        Toast.makeText(mContext, "Sorry! memory related fatal exception found. Need to reenter data", Toast.LENGTH_LONG).show();
                        Intent intent = new Intent(mContext,
                                MenuActivity.class);
                        startActivity(intent);
                    }
                }
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    new TRANS_SubmitQuotationDetailsTask(mContext, true).execute();
                } else {
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                    mContext.startActivity(intent);
                }

            }
        });
        QuotationContactPersonDialog.show();
    }

    private void refreshListViewAndTotalAmount() {
        QuotationProductsListAdapterObject.notifyDataSetChanged();
        calculateTotalAmountAndSetOnTextView();
    }

    public void drawFilterLayout() {
        if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes"))//this is supposed to be a temp solution
        {
            filterList = new ArrayList<>();
            filterList.add("1");
            filterList.add("NA");
            filterList.add("NA");
            filterList.add("NA");
            filterList.add("BRAND FORM");
        } else {
            filterList = mAceDnsDatabase.getFilterList();// filterList =[4(filter_no),dadu,baba,NA,chhele]
        }


        for (int ii = 1; ii < filterList.size(); ii++) {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(
                        LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayoutParams.setMargins(0, 0, 0, 5);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(
                        LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);


                buttonParams.gravity = Gravity.CENTER_VERTICAL;
                filterButton.setLayoutParams(buttonParams);
                filterButton.setTag(ii);
                filterButton.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        makeButtonClickProcess(view);
                    }
                });
                filterButton.setHorizontallyScrolling(true);
                filterButton.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
                filterButton.setText(filterList.get(ii));
                filterButton.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
                filterButton.setSingleLine(true);
                //filterButton.setEnabled(false);
                buttonLayout.addView(filterButton);
                filterLayout.addView(buttonLayout);
                filterButtonList.add(filterButton);
            } else {
                filterButtonList.add(null);
            }
        }
    }

    private void makeButtonClickProcess(View view) {
        int tag = (Integer) view.getTag();
        switch (tag) {
            case 1:
                switch (filterNo) {
                    case 2:
                        lastProdPos = 0;
                        filterButtonList.get(3).setEnabled(true);
                        break;
                    case 3:
                        lastProdPos = 0;
                        filterButtonList.get(1).setEnabled(true);
                        filterButtonList.get(3).setEnabled(true);
                        break;
                    case 4:
                        lastProdPos = 0;
                        filterButtonList.get(3).setEnabled(true);
                        filterButtonList.get(2).setEnabled(true);
                        filterButtonList.get(1).setEnabled(true);
                        break;
                }
                lastSubGroupSelected = false;
                lastBrandSelected = false;
                lastProductSelected = false;
                prepareOrderData(1, "");
                break;
            case 2:
                switch (filterNo) {
                    case 3:
                        lastProdPos = 0;
                        filterButtonList.get(3).setEnabled(true);
                        break;
                    case 4:
                        lastProdPos = 0;
                        filterButtonList.get(3).setEnabled(true);
                        filterButtonList.get(2).setEnabled(true);
                        break;
                }
                lastBrandSelected = false;
                lastProductSelected = false;
                if (selectedGrp != null) {
                    prepareOrderData(2, selectedGrp.getGroupCode());
                } else {
                    Toast.makeText(mContext,
                            "Please select the parent category", Toast.LENGTH_LONG);
                }
                break;
            case 3:
                lastProdPos = 0;
                filterButtonList.get(3).setEnabled(true);
                lastProductSelected = false;
                if (selectedSubGrp != null) {
                    prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                } else {
                    Toast.makeText(mContext,
                            "Please select the parent category", Toast.LENGTH_LONG);
                }
                break;
//            case 4:
//                switch (filterNo)
//                {
//                    case 1:
//                        prepareOrderData(4, "");
//                        break;
//                    case 2:
//                        if (selectedGrp != null) {
//                            prepareOrderData(4, selectedGrp.getGroupCode());
//                        } else {
//                            Toast.makeText(mContext,
//                                    "Please select the parent category", Toast.LENGTH_LONG);
//                        }
//                        break;
//                    case 3:
//                        if (selectedSubGrp != null) {
//                            prepareOrderData(4, selectedSubGrp.getSubGrpCode());
//                        } else {
//                            Toast.makeText(mContext,
//                                    "Please select the parent category", Toast.LENGTH_LONG);
//                        }
//                        break;
//                    case 4:
//                        if (selectedBrand != null) {
//                            prepareOrderData(4, selectedBrand.getBrandCode());
//                        } else {
//                            Toast.makeText(mContext,
//                                    "Please select the parent category", Toast.LENGTH_LONG);
//                        }
//                        break;
//                }
        }
    }

    private void calculateTotalAmountAndSetOnTextView() {
        double amount = 0.00;
        for (int i = 0; i < QuotationDetailsList.size(); i++) {
            amount = amount + Double.parseDouble(QuotationDetailsList.get(i).getQuotationProductAmount());
        }
        txt_total.setText(defaultFormat.format(amount));
    }

    public void reInitialiseCustomerList() {
        tempCustomerList = new ArrayList<>(customerList);
    }

    public void showChooseCustomerDialog() {
        final Dialog customerListDialog = new Dialog(mContext,
                R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);
        TextView title = (TextView) customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = (EditText) customerListDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2,
                                      int arg3) {
                Log.d("arg0", arg0.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

                String str = s.toString();
                if (str.length() == 0) {
                    reInitialiseCustomerList();
                } else {
                    filterCustomerArray(str.length(), str);
                }
//                if (lastStr.length() > str.length())
//                {
//                    reInitialiseCustomerList();
//                }
//                lastStr = str;
//                filterCustomerArray(str.length(), str);
//                adapterCust.notifyDataSetChanged();
                adapterCust = new CustomerAdapter(mContext, R.layout.customer_list_child, tempCustomerList);
                dialogList.setAdapter(adapterCust);
            }
        });

        dialogList = (ListView) customerListDialog
                .findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                customerNameET.setText(tempCustomerList.get(arg2).getCustomerName());
                customerNameET.setSelection(customerNameET.getText().length());
                customerAddressET.setText(tempCustomerList.get(arg2).getAddress());
                customerAddressET.setSelection(customerAddressET.getText().length());
                customerPhoneET.setText(tempCustomerList.get(arg2).getNumber());
                customerPhoneET.setSelection(customerPhoneET.getText().length());
                customerEmailET.setText(tempCustomerList.get(arg2).getEmail());
                customerListDialog.cancel();
            }
        });

        Button addCustomer = (Button) customerListDialog
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        customerListDialog.show();
    }

    public void filterCustomerArray(int strCnt, String charVal) {
        tempCustomerList = new ArrayList<>();
        int size = customerList.size();
        for (int ii = 0; ii < size; ii++) {
            if (customerList.get(ii).getCustomerName().toUpperCase().contains(charVal.toUpperCase())) {
                tempCustomerList.add(customerList.get(ii));
            }
        }

    }

    private void openDatePicker() {
        // Get Current Date
        final Calendar c = Calendar.getInstance();
        int mYear = c.get(Calendar.YEAR);
        int mMonth = c.get(Calendar.MONTH);
        int mDay = c.get(Calendar.DAY_OF_MONTH);
        //launch datepicker modal
        DatePickerDialog datePickerDialog = new DatePickerDialog(this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
                        quotationValidTill = formatInt.format(dayOfMonth) + "/" + formatInt.format((monthOfYear + 1)) + "/" + year;
                        quotationValidityDate.setText(quotationValidTill);
                    }
                }, mYear, mMonth, mDay);
        datePickerDialog.show();
        datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis() - 1000);

    }

    private void showRemoveItemAlertDialog(final int pos) {
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext);
        AlertDG.setTitle("Please Note");
        AlertDG.setMessage("Do you really want to remove current item?");
        AlertDG.setPositiveButton("Yes", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
                QuotationDetailsList.remove(pos);
                refreshListViewAndTotalAmount();
            }
        });
        AlertDG.setNegativeButton("No", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
            }
        });
        AlertDG.setCancelable(true);
        AlertDG.create().show();
    }

    public void prepareOrderData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        productGroupList = mAceDnsDatabase
                                .getProductGroupList(carryInSales);
                        if (!rpeatedProductEntry) {
                            removeRepeatedGroupItems();
                        }
                        tempProductGroupList = new ArrayList<ProductGroupDetails>();
                        reInitialiseProductGroupList();
                        break;
                    case 2:
                        productSubGroupList = mAceDnsDatabase.getProductSubGroupList(param, carryInSales);
                        if (!rpeatedProductEntry) {
                            removeRepeatedSubGroupItems();
                        }
                        tempProductSubGroupList = new ArrayList<ProductSubGrpDetails>();
                        reInitialiseProductSubGroupList();
                        break;
                    case 3:
                        productBrandList = mAceDnsDatabase.getProductBrandList(
                                param, carryInSales);
                        if (!rpeatedProductEntry) {
                            removeRepeatedBrandItems();
                        }
                        tempProductBrandList = new ArrayList<ProductBrandDetails>();
                        reInitialiseProductBrandList();
                        break;
                    case 4:
                        if (carryInSales || Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes")) {
                            productMasterList = mAceDnsDatabase.getProductMasterList(param, filterNo, carryInSales);
                        } else {
                            productMasterList = mAceDnsDatabase.getProductMasterListIgnoringStock(param, filterNo);
                        }
                        if (!rpeatedProductEntry) {
                            removeRepeatedProductItems();
                        }
                        tempProductList = new ArrayList<ProductMasterDetails>();
                        reInitialiseProductList();
                        break;
                }
                Message msgObj = orderDataHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("WHAT TO SHOW", doWhat);
                msgObj.setData(b);
                orderDataHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void showGrpListDialog() {
        if (productGroupList.size() > 0) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            groupAdapter = new ProductGrpAdapter(mContext,
                    R.layout.product_list_child, tempProductGroupList);
            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_with_search);
            grpDialog.setTitle("Please select a product group");
            grpDialog.setCancelable(false);
            TextView title = (TextView) grpDialog.findViewById(R.id.title);
            title.setText("Please select a product group");
            EditText searchText = (EditText) grpDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {

                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductGroupList();
                    }
                    lastStr = str;
                    filterProductGroupArray(str.length(), str);
                    groupAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) grpDialog.findViewById(R.id.list);
            dialogList.setAdapter(groupAdapter);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    grpDialog.cancel();
                    selectedGrp = tempProductGroupList.get(arg2);
                    filterButtonList.get(0).setText(selectedGrp.getGroupName());

                    switch (filterNo) {
                        case 2:
                            prepareOrderData(4, selectedGrp.getGroupCode());
                            break;
                        case 3:
                            prepareOrderData(2, selectedGrp.getGroupCode());
                            break;
                        case 4:
                            prepareOrderData(2, selectedGrp.getGroupCode());
                            break;
                    }

                }
            });
            Button cancel = (Button) grpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        } else {
            Toast.makeText(mContext,
                    "There are no items left.Please submit order.", Toast.LENGTH_LONG)
                    .show();
        }
    }

    public void showSubGrpListDialog() {
        if (productSubGroupList.size() > 0) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGroupAdapter = new ProductSubGrpAdapter(mContext,
                    R.layout.product_list_child, tempProductSubGroupList);
            final Dialog subGrpDialog = new Dialog(mContext,
                    R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_with_search);
            subGrpDialog.setCancelable(false);
            TextView title = (TextView) subGrpDialog.findViewById(R.id.title);
            title.setText("Please select a product sub group");
            EditText searchText = (EditText) subGrpDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {

                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductSubGroupList();
                    }
                    lastStr = str;
                    filterProductSubGroupArray(str.length(), str);
                    subGroupAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) subGrpDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(subGroupAdapter);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    subGrpDialog.cancel();
                    selectedSubGrp = tempProductSubGroupList.get(arg2);
                    filterButtonList.get(1).setText(
                            selectedSubGrp.getSubGrpName());
                    switch (filterNo) {
                        case 3:
                            prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            break;
                        case 4:
                            prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                            break;
                    }
                }
            });
            Button cancel = (Button) subGrpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    subGrpDialog.cancel();
                }
            });
            subGrpDialog.show();
        } else {
            Toast.makeText(
                    mContext,
                    "There are no items left in this category. Please choose a different category",
                    Toast.LENGTH_LONG).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    public void showBrandListDialog() {
        if (productBrandList.size() > 0) {
            if (productBrandList.size() == 1) {
                lastBrandSelected = true;
            }
            brandAdapter = new ProductBrandAdapter(mContext,
                    R.layout.product_list_child, tempProductBrandList);
            final Dialog brandDialog = new Dialog(mContext,
                    R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_with_search);
            brandDialog.setCancelable(false);
            TextView title = (TextView) brandDialog.findViewById(R.id.title);
            title.setText("Please select a brand");
            EditText searchText = (EditText) brandDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {

                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductBrandList();
                    }
                    lastStr = str;
                    filterProductBrandArray(str.length(), str);
                    brandAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) brandDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(brandAdapter);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    brandDialog.cancel();
                    selectedBrand = tempProductBrandList.get(arg2);
                    filterButtonList.get(2).setText(
                            selectedBrand.getBrandName());
                    prepareOrderData(4, selectedBrand.getBrandCode());
                }
            });
            Button cancel = (Button) brandDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    brandDialog.cancel();
                }
            });
            brandDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    public void showMasterListDialog() {
        if (productMasterList.size() > 0) {
            if (productMasterList.size() == 1) {
                lastProductSelected = true;
            }
            prodAdapter = new ProductMasterAdapter(mContext, R.layout.product_list_child, tempProductList);

            final Dialog masterDialog = new Dialog(mContext, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please select a product");
            EditText searchText = (EditText) masterDialog.findViewById(R.id.autoCompleteTextView1);

            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductList();
                    }
                    lastStr = str;
                    filterProductArray(str.length(), str);
                    prodAdapter.notifyDataSetChanged();
                }
            });
            dialogList = (ListView) masterDialog.findViewById(R.id.list);
            dialogList.setAdapter(prodAdapter);

            if (lastProdPos != 0) {
                dialogList.setSelection(lastProdPos - 1);
            } else {
                dialogList.setSelection(0);
            }
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {

                    lastProdPos = arg2;
                    ProductMasterDetails currentProductMasterObj = tempProductList.get(arg2);
                    currentProdDescription = currentProductMasterObj.getDesc();
                    currentProductCode = currentProductMasterObj.getProdCode();
                    currentProdDescTV.setText(currentProdDescription);
                    masterDialog.cancel();

                }
            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    masterDialog.cancel();
                }
            });
            Button btn_addToCart = (Button) masterDialog.findViewById(R.id.btn_addToCart);
            if (Constants.orderFormDetailsObj.getInputScreenPlanwise().equalsIgnoreCase("yes")) {
                btn_addToCart.setVisibility(View.VISIBLE);
            }

            masterDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            filterButtonList.get(3).setText("");
        }
    }

    public void removeRepeatedProductItems() {
        for (int kk = 0; kk < Constants.selectedProductMasterList.size(); kk++) {
            ProductMasterDetails currentItem = Constants.selectedProductMasterList
                    .get(kk);
            for (int x = 0; x < productMasterList.size(); x++) {
                if (productMasterList.get(x).getProdCode()
                        .equalsIgnoreCase(currentItem.getProdCode())) {
                    productMasterList.remove(productMasterList.get(x));
                }
            }
        }
        int size = productMasterList.size();
        System.out.println(size);
    }

    public void removeRepeatedBrandItems() {
        for (int kk = 0; kk < Constants.selectedBrandList.size(); kk++) {
            ProductBrandDetails currentItem = Constants.selectedBrandList
                    .get(kk);
            for (int x = 0; x < productBrandList.size(); x++) {
                if (productBrandList.get(x).getBrandCode()
                        .equalsIgnoreCase(currentItem.getBrandCode())) {
                    productBrandList.remove(productBrandList.get(x));
                }
            }
        }
        int size = productBrandList.size();
        System.out.println(size);
    }

    public void removeRepeatedGroupItems() {
        for (int kk = 0; kk < Constants.selectedGroupList.size(); kk++) {
            ProductGroupDetails currentItem = Constants.selectedGroupList
                    .get(kk);
            for (int x = 0; x < productGroupList.size(); x++) {
                if (productGroupList.get(x).getGroupCode()
                        .equalsIgnoreCase(currentItem.getGroupCode())) {
                    productGroupList.remove(productGroupList.get(x));
                }
            }
        }
    }

    public void removeRepeatedSubGroupItems() {
        for (int kk = 0; kk < Constants.selectedSubGroupList.size(); kk++) {
            ProductSubGrpDetails currentItem = Constants.selectedSubGroupList
                    .get(kk);
            for (int x = 0; x < productSubGroupList.size(); x++) {
                if (productSubGroupList.get(x).getSubGrpCode()
                        .equalsIgnoreCase(currentItem.getSubGrpCode())) {
                    productSubGroupList.remove(productSubGroupList.get(x));
                }
            }
        }
    }

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            tempProductList.add(productMasterList.get(kk));
        }

    }

    public void reInitialiseProductGroupList() {
        tempProductGroupList.removeAll(tempProductGroupList);
        int size = tempProductGroupList.size();
        int size1 = productGroupList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productGroupList.size(); kk++) {
            tempProductGroupList.add(productGroupList.get(kk));
        }

    }

    public void reInitialiseProductSubGroupList() {
        tempProductSubGroupList.removeAll(tempProductSubGroupList);
        int size = tempProductSubGroupList.size();
        int size1 = productSubGroupList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productSubGroupList.size(); kk++) {
            tempProductSubGroupList.add(productSubGroupList.get(kk));
        }

    }

    public void reInitialiseProductBrandList() {
        tempProductBrandList.removeAll(tempProductBrandList);
        int size = tempProductBrandList.size();
        int size1 = productBrandList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productBrandList.size(); kk++) {
            tempProductBrandList.add(productBrandList.get(kk));
        }

    }

    public void filterProductArray(int strCnt, String charVal) {
        int size = tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductList.get(ii).getDesc().length() >= strCnt) {
                if (tempProductList.get(ii).getDesc().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductList.remove(tempProductList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductList.remove(tempProductList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductGroupArray(int strCnt, String charVal) {
        int size = tempProductGroupList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductGroupList.get(ii).getGroupName().length() >= strCnt) {
                if (tempProductGroupList.get(ii).getGroupName().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductGroupList.remove(tempProductGroupList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductGroupList.remove(tempProductGroupList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductSubGroupArray(int strCnt, String charVal) {
        int size = tempProductSubGroupList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductSubGroupList.get(ii).getSubGrpName().length() >= strCnt) {
                if (tempProductSubGroupList.get(ii).getSubGrpName()
                        .toUpperCase().contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductSubGroupList.remove(tempProductSubGroupList
                            .get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductSubGroupList.remove(tempProductSubGroupList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterProductBrandArray(int strCnt, String charVal) {
        int size = tempProductBrandList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductBrandList.get(ii).getBrandName().length() >= strCnt) {
                if (tempProductBrandList.get(ii).getBrandName().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductBrandList.remove(tempProductBrandList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductBrandList.remove(tempProductBrandList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

}
