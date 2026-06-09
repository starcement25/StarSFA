package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.InputFilter;
import android.text.InputType;
import android.text.SpannableStringBuilder;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemLongClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitDOTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;
//import com.shagi.materialdatepicker.date.DatePickerFragmentDialog;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.DoConfirmAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapterStockReturn;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DecimalDigitsInputFilter;
import com.forcepower.acedns.util.EnglishNumberToWords;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.PrintTextFormatter;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;
import java.util.Iterator;
import java.util.List;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.text.HtmlCompat;

import static com.forcepower.acedns.constants.Constants.currency;
import static com.forcepower.acedns.constants.Constants.defaultFormat;
import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;
import static com.forcepower.acedns.constants.Constants.deliveryDate;
import static com.forcepower.acedns.constants.Constants.doAmount;
import static com.forcepower.acedns.constants.Constants.freeBiesAdapter;
import static com.forcepower.acedns.constants.Constants.isGettingCurrentLocation;
import static com.forcepower.acedns.constants.Constants.isVanSales;
import static com.forcepower.acedns.constants.Constants.mChosenUomType;
import static com.forcepower.acedns.constants.Constants.mOrderPriceValidationType;
import static com.forcepower.acedns.constants.Constants.schemeListForCurrentProducts;
import static com.forcepower.acedns.constants.Constants.schemeListForProductGroups;
import static com.forcepower.acedns.constants.Constants.selectedProductMasterList;
import static com.forcepower.acedns.constants.Constants.selectedProductMasterListFreebies;
import static com.forcepower.acedns.constants.Constants.totalOrderAmount;
import static com.forcepower.acedns.constants.Constants.transitTime;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.doesQuantityMatchesQtySlab;
import static com.forcepower.acedns.util.Utils.getAmnt;
import static com.forcepower.acedns.util.Utils.setCheckInOutLatLongAccuracyToLocationLatLong;

public class DOConfirmationActivity extends AppCompatActivity implements
        OnClickListener, OnItemClickListener, OnItemLongClickListener {
    AceDnsDatabase mAceDnsDatabase;
    ProgressDialog mStepProgressDialog;
    ListView productListView, list_freebie_prod;
    DoConfirmAdapter adapter;
    String mRemarks = "";
    Button btnAddProduct, btnSubmit, btnBack, btnSaleType, btnRemarks, btnDiscount, btnVAT,btn_pick_date;
    AceDnsTransactionDatabase dataHelperObj;
    Context mContext;
    String remarks = "";
    String saleType = "", trdDisc = "0", totalOrderValue = "", chequeNo = "", bankName = "", currentInvoiceNumber = "", totalOrderQty = "";
    ImageView imhLogo;
    TextView txtTotal;
    TextView txt_totalQty;
    Dialog masterDialog;
    Handler mHandler;
    ProgressDialog loader;
    Dialog customerListDialog;
    ArrayList<CustomerDetails> customerList, tempCustomerList;
    String lastStr = "";
    CustomerAdapter adapterCust;
    int chosenCustPos;
    ProductMasterWithQtyInputAdapterStockReturn adapterReturn;
    boolean carryInSales = false;
    CaldroidListener listener;
    Button multipleRemarksButton;
    SimpleDateFormat dateFormat;
    // DecimalFormat df;
    String vat = "0.00", currentDateandTime = "";
    String remarksDate = "";
    SpannableStringBuilder finalPrintStringSalesBill, finalPrintStringSalesBillVatString, finalPrintStringMoneyReceipt, finalPrintStringFreightBill;
    PrintTextFormatter PrintTextFormatterObject;
    double grandTotal = 0;
    double totalPriceWithoutVat = 0.00;
    DecimalFormat df = new DecimalFormat("#.00");
    ArrayList<Double> vatRateList;
    ArrayList<Double> TotalAmountForEachVatRate;
    Boolean shouldPrintFreightBill = false,vanSalesReturnConfirmationDone=false;
    double freightServiceTaxPercentage = 14.00, freightSwachhBharatPercent = .50;
    double freightCost = 0;
    List<String> hintRemarksValList;
    int selectedHintRemarksId = -1;
    int localDataSavingFailedAttempt = 0;
    EditText edInst1;
    Boolean isNavigatedFromSalesOption = false;
    String totalAmount = "0";
    private CaldroidFragment dialogCaldroidFragment;
    LocationTracker LocationTrackerObject;
    public static Button mButtonStkReturn = null;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_do_confirm);

        Toolbar myToolbar = findViewById(R.id.my_toolbar);
        setSupportActionBar(myToolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayShowTitleEnabled(false);
        mContext = DOConfirmationActivity.this;
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        PrintTextFormatterObject = new PrintTextFormatter(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        hintRemarksValList = new ArrayList<>();
        RegisterActivities.registerActivity(this);
        finalPrintStringSalesBill = new SpannableStringBuilder();
        finalPrintStringMoneyReceipt = new SpannableStringBuilder();
        finalPrintStringSalesBillVatString = new SpannableStringBuilder();
        carryInSales = getIntent().getBooleanExtra("CARRY_IN", false);
        if (getIntent().hasExtra("salesOption")) {
            isNavigatedFromSalesOption = true;
        }
        // df = new DecimalFormat("#.00");
        Constants.isFromConfirmationActivity = true;
        vatRateList = new ArrayList<>();
        TotalAmountForEachVatRate = new ArrayList<>();

        initView();

        Constants.isCheckedIn=NotCheckedOut(mContext);
        Constants.selectedProductStockReturn=new ArrayList<>();
        if(!Constants.isCheckedIn)
        {
            LocationTrackerObject=new LocationTracker(mContext,"order confirm");
        }
        else
        {
            setCheckInOutLatLongAccuracyToLocationLatLong(mContext);
        }
        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes")
                && Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("customer wise")) {
            trdDisc = Constants.selectedCustomer.getTradeDiscount();
        }

        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase("credit")) {
            saleType = "CREDIT";
        }
        CalculateTotal();
        if(isVanSales)
        {
            mButtonStkReturn = (Button) findViewById(R.id.stk_return);
            mButtonStkReturn.setVisibility(View.VISIBLE);
            mButtonStkReturn.setOnClickListener(v -> {
                vanSalesReturnConfirmationDone=true;
                showReturnProductList();

            });
        }

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    DOConfirmationActivity.this.runOnUiThread(new Runnable() {
                        public void run()
                        {

                    new TRANS_SubmitDOTask(mContext, true, "SUBMIT").execute();

                        }
                    });
                }
            }
        };

        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                try {
                    String timeStamp = new SimpleDateFormat("HHmmss")
                            .format(Calendar.getInstance().getTime());
                    Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss")
                            .parse(Constants.dateString + timeStamp);
                    if (!date.after(currentDate)) {
                        dialogCaldroidFragment.dismiss();
                        multipleRemarksButton.setText(dateFormat.format(date));
                        remarksDate = new SimpleDateFormat("yyyyMMdd")
                                .format(date);
                    } else {
                        Utils.showToast(mContext,
                                "Future Dates cannot be selected.");
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {
                try {
                    String timeStamp = new SimpleDateFormat("HHmmss")
                            .format(Calendar.getInstance().getTime());
                    Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss")
                            .parse(Constants.dateString + timeStamp);
                    if (!date.after(currentDate)) {
                        dialogCaldroidFragment.dismiss();
                        multipleRemarksButton.setText(dateFormat.format(date));
                        remarksDate = new SimpleDateFormat("yyyyMMdd")
                                .format(date);
                    } else {
                        Utils.showToast(mContext, "Future Dates cannot be selected.");
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };


    }

    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }
    private void showReturnProductList()
    {
        ArrayList<ProductMasterDetails> productMasterListForReturn=mAceDnsDatabase.getProductMasterListStockReturn();
        if(productMasterListForReturn.size()>0)
        {
            adapterReturn = new ProductMasterWithQtyInputAdapterStockReturn(mContext, R.layout.product_list_item_with_quantity_input_stock_return, productMasterListForReturn);

            masterDialog = new Dialog(mContext, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please provide quantity for return");

            View list_header_item_planwise_input_screen = masterDialog.findViewById(R.id.list_header_item_planwise_input_screen);

            list_header_item_planwise_input_screen.setVisibility(View.VISIBLE);
            TextView list_details = (TextView) masterDialog.findViewById(R.id.list_details);
            LinearLayout.LayoutParams params = (LinearLayout.LayoutParams) list_details.getLayoutParams();
            params.weight = 4f;
            list_details.setLayoutParams(params);
            TextView etProdQty = (TextView) masterDialog.findViewById(R.id.etProdQty);
            LinearLayout.LayoutParams params2 = (LinearLayout.LayoutParams) etProdQty.getLayoutParams();
            params2.weight = 1f;
            etProdQty.setLayoutParams(params2);

            etProdQty.setVisibility(View.VISIBLE);
            TextView tv_last_month_purchase = (TextView) masterDialog.findViewById(R.id.tv_last_month_purchase);
            TextView tv_order_plan = (TextView) masterDialog.findViewById(R.id.tv_order_plan);
            tv_last_month_purchase.setVisibility(View.GONE);
            tv_order_plan.setVisibility(View.GONE);
            TextView etProdRate = (TextView) masterDialog.findViewById(R.id.etProdRate);
            etProdRate.setText("Add");
            etProdRate.setLayoutParams(params2);
            ImageView image_cancel = (ImageView) masterDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View view) {
                    masterDialog.cancel();
                }
            });
            EditText searchText = (EditText) masterDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.setVisibility(View.GONE);

            ListView dialogList = (ListView) masterDialog.findViewById(R.id.list);
            dialogList.setAdapter(adapterReturn);
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setVisibility(View.GONE);

            Button btn_addToCart = (Button) masterDialog.findViewById(R.id.btn_addToCart);
            btn_addToCart.setVisibility(View.VISIBLE);
            btn_addToCart.setText("CANCEL");
            btn_addToCart.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v)
                {
                    masterDialog.cancel();
                }
            });
            masterDialog.show();
        }
        else
        {
            Toast.makeText(mContext, "No product found in DB, Please Synchronize Data.", Toast.LENGTH_LONG).show();
        }
    }


    private void removeAndAddFilter2Schemes() {
        if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes")) {
            for (int i = 0; i < selectedProductMasterListFreebies.size(); i++) {
                if (selectedProductMasterListFreebies.get(i).getfreebieFilter().equalsIgnoreCase("2")) {
                    selectedProductMasterListFreebies.remove(i);
                    i--;
                }
            }
            if (schemeListForProductGroups.size() > 0) {
                for (int i = 0; i < schemeListForProductGroups.size(); i++) {
                    SchemeFreebiesDetails item = schemeListForProductGroups.get(i);
                    double totalQty = 0;
                    for (int i2 = 0; i2 < selectedProductMasterList.size(); i2++) {
                        ProductMasterDetails currentProduct = selectedProductMasterList.get(i2);
                        if (currentProduct.getGrpCode().equalsIgnoreCase(item.getprodCode())) {
                            String qtyForCurrentProd = currentProduct.getQty();
                            if (Utils.isNumeric(qtyForCurrentProd)) {
                                totalQty = totalQty + Double.parseDouble(qtyForCurrentProd);
                            }

                        }

                        Utils.addQtyProductToList(item, totalQty, 0, mContext);
//						if(doesQuantityMatchesQtySlab(totalQty+"",item.getqty()))
//						{
//							selectedProductMasterListFreebies.add(schemeListForProductGroups);
//						}
                    }
                }
            }
        }
    }

    @Override
    public void onResume()
    {
        super.onResume();
        if(!Constants.isCheckedIn)
        {
            LocationTrackerObject.checkLocationUpdateSharing();
        }

        if (Constants.logoBmp != null) {
            imhLogo.setVisibility(View.VISIBLE);
            imhLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imhLogo.setVisibility(View.GONE);
        }
    }
    @Override
    public void onPause()
    {
        super.onPause();
        if(!Constants.isCheckedIn)
        {
            LocationTrackerObject.stopLocationUpdates();
        }
    }

    @Override
    public void onStop()
    {
        super.onStop();
        if(!Constants.isCheckedIn)
        {
            LocationTrackerObject.stopLocationUpdates();
        }
    }
    public void initView() {
        txtTotal = (TextView) findViewById(R.id.txt_total);
        txt_totalQty = (TextView) findViewById(R.id.txt_totalQty);
        imhLogo = (ImageView) findViewById(R.id.imagelogo);
        list_freebie_prod = (ListView) findViewById(R.id.list_freebie_prod);
        productListView = (ListView) findViewById(R.id.list_prod);
        adapter = new DoConfirmAdapter(DOConfirmationActivity.this, R.layout.do_confrm_list_child, selectedProductMasterList, txtTotal, trdDisc, carryInSales ? "SB" : "SO");
        calculateTotalAmountAndShow();
        productListView.setAdapter(adapter);
        productListView.setLongClickable(true);
        productListView.setOnItemClickListener(DOConfirmationActivity.this);
        productListView
                .setOnItemLongClickListener(DOConfirmationActivity.this);

        btn_pick_date = (Button) findViewById(R.id.btn_pick_date);
        btnAddProduct = (Button) findViewById(R.id.btn_add);
        btnSubmit = (Button) findViewById(R.id.btn_order);
        btnBack = (Button) findViewById(R.id.back);
        btnSaleType = (Button) findViewById(R.id.btn_saletype);
        btnSaleType.setVisibility(View.GONE);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        // txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        btnRemarks = (Button) findViewById(R.id.btn_remarks);

        btnDiscount = (Button) findViewById(R.id.btn_discount);
        btnVAT = (Button) findViewById(R.id.btn_vat);

        btn_pick_date.setOnClickListener(DOConfirmationActivity.this);
        btnAddProduct.setOnClickListener(DOConfirmationActivity.this);
        btnSubmit.setOnClickListener(DOConfirmationActivity.this);
        btnBack.setOnClickListener(DOConfirmationActivity.this);
        btnSaleType.setOnClickListener(DOConfirmationActivity.this);
        btnRemarks.setOnClickListener(DOConfirmationActivity.this);
        btnDiscount.setOnClickListener(DOConfirmationActivity.this);
        btnVAT.setOnClickListener(DOConfirmationActivity.this);

        btnDiscount.setVisibility(View.GONE);
        btnVAT.setVisibility(View.GONE);
    }

    @Override
    public void onClick(View arg0) {
        if (arg0 == btnAddProduct)
        {
            finish();
        }
        if (arg0 == btn_pick_date)
        {
            final Calendar c = Calendar.getInstance();
            int mYear = c.get(Calendar.YEAR);
            int mMonth = c.get(Calendar.MONTH);
            int mDay = c.get(Calendar.DAY_OF_MONTH);
//            DatePickerFragmentDialog datePickerFragmentDialog=DatePickerFragmentDialog.newInstance((view, year, monthOfYear, dayOfMonth) -> {
//                    txtTodayDate.setText(dayOfMonth + "/" + (monthOfYear + 1) + "/" + year);
//                Utils.showToast(mContext,dayOfMonth + "/" + (monthOfYear + 1) + "/" + year);
//                Constants.deliveryDate = year+"-"+ (monthOfYear + 1) +"-"+dayOfMonth;
//
//
//            },mYear, mMonth, mDay);
//            datePickerFragmentDialog.show(getSupportFragmentManager(),null);
//            int transitTimePLusOneDay = 1 + transitTime;
//            datePickerFragmentDialog.setMinDate(System.currentTimeMillis() + (transitTimePLusOneDay *24) * 60 * 60 * 1000);
//            datePickerFragmentDialog.setMinDate(System.currentTimeMillis() + ((11)*24) * 60 * 60 * 1000);
//            datePickerFragmentDialog.setYearRange(mYear,mYear+1);
//            datePickerFragmentDialog.setCancelColor(getResources().getColor(R.color.colorOrangeAppGreyDark));
//            datePickerFragmentDialog.setOkColor(getResources().getColor(R.color.colorOrangeAppGreyDark));
//            datePickerFragmentDialog.setAccentColor(getResources().getColor(R.color.colorOrangeAppGreyDark));
//            datePickerFragmentDialog.setOkText("ok");
//            datePickerFragmentDialog.setCancelText("cancel");
        }

        else if (arg0 == btnSubmit)
        {
            orderSubmitProcess();

        }
        else if (arg0 == btnBack)
        {
            finish();
        }
        else if (arg0 == btnRemarks)
        {
            ShowRemarksDateDialog();
        }
    }
    /**
     * This Function show the remarks  dialog.
     */
    public void ShowRemarksDateDialog() {
        final Dialog RemarksDialog = new Dialog(mContext);
        RemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        RemarksDialog.setContentView(R.layout.remarks_date_layout);
        RemarksDialog.setCancelable(false);
        TextView title = (TextView) RemarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");

        final EditText edittextRemarks = (EditText) RemarksDialog.findViewById(R.id.ed_input_r1);
//		final DatePicker picker = (DatePicker) RemarksDialog.findViewById(R.id.datePicker1);

        Button submit = (Button) RemarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

                if (edittextRemarks.getText().length() == 0
                        && !edittextRemarks.getText().toString()
                        .equalsIgnoreCase(".")) {
                    mRemarks = "";
                } else {
                    mRemarks = edittextRemarks.getText().toString();
                }
                RemarksDialog.cancel();

            }
        });
        RemarksDialog.show();
    }
    public void orderSubmitProcess() {
        if (Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") && !isGettingCurrentLocation)
        {
            Utils.showToast(mContext,"Could not determine your location. Please Check Location Settings");
        }
        else
        {
            submitOrderProcess();
        }
    }

    protected void onActivityResult(int requestCode, int resultCode, Intent imageReturnedIntent)
    {
        super.onActivityResult(requestCode, resultCode, imageReturnedIntent);
        switch (requestCode)
        {
            case 9999:
                if (resultCode == RESULT_CANCELED)
                {
                    if(Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes"))
                    {
                        LocationTrackerObject.checkLocationUpdateSharing();
                    }

                }
                else if (resultCode == RESULT_OK)
                {
                    LocationTrackerObject.startLocationUpdates();
                }
                break;
        }
    }

    private void submitOrderProcess()
    {
                Constants.transactionEndTime = Calendar.getInstance().getTime();
                Boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
                if (isTimeAutomatic)
                {
                    if(HTTPUtils.isConnectionPossible(mContext))
                    {
                        saveOrderDataToDatabase();
                    }
                    else
                    {
                        Utils.showToast(mContext,"You need an active internet connection to use this feature.");
                    }
                }
                else
                {
                    Utils.showSettingsAlertToChangeTimeZone(mContext);
                }


    }

    private void makeDataSavingAndPrintingProcess() {
        if (Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") && (Constants.currentLat=="0.0" || Constants.currentLong=="0.0"))
				{
                    SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
                    currentDateandTime = sdf.format(new Date());
                    currentInvoiceNumber = generateNewInvoiceNumber();
//		PDFCreator pdfc=new PDFCreator(mContext,currentInvoiceNumber,false);
//		pdfc.createPdf(Constants.selectedCustomer.getCustomerName(),currentDateandTime,Constants.selectedProductMasterList);
                    calculatePrintStringSalesBill();
                    calculatePrintStringMoneyReceipt();
                    if (shouldPrintFreightBill) {
                        calculatePrintStringFreightBill();
                    }
                    if(HTTPUtils.isConnectionPossible(mContext))
                    {
                        saveOrderDataToDatabase();
                    }
                    else
                    {
                        Utils.showToast(mContext,"You need an active internet connection to use this feature.");
                    }
                }
                else
                {
                    Utils.showToast(mContext,"Please enable location sharing to complete transaction");
                }

    }

    public void showVATDialog() {
        final Dialog vatDialog = new Dialog(mContext);
        vatDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        vatDialog.setContentView(R.layout.vat_dialog);
        TextView title = (TextView) vatDialog.findViewById(R.id.title);
        title.setText("Enter VAT amount");
        final EditText edInst = (EditText) vatDialog
                .findViewById(R.id.ed_input);
        edInst.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        Button submit = (Button) vatDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (edInst.getText().toString().length() > 0
                        && !edInst.getText().toString().equalsIgnoreCase(".")) {
                    vat = edInst.getText().toString();
                }
                introduceVATInProduct();
                adapter.notifyDataSetChanged();
                vatDialog.cancel();
            }
        });
        vatDialog.show();
    }

    public void introduceVATInProduct() {
        double vatVal = Double.parseDouble(vat);
        double orderValue = CalculateTotal();
        // double vatFraction = vatVal/orderValue;
        // vatFraction = Math.round(vatFraction * 100.0) / 100.0;
        for (int ii = 0; ii < selectedProductMasterList.size(); ii++) {
            ProductMasterDetails currentObj = selectedProductMasterList
                    .get(ii);
            double qty = Double.parseDouble(currentObj.getQty());
            double rate = Double.parseDouble(currentObj.getMrpValue());
            // double vatValueProduct = vatFraction*qty*rate;
            double vatValueProduct = (qty * rate) * (vatVal / orderValue);
            currentObj.setVat(String.valueOf(vatValueProduct));
        }
        CalculateTotal();
    }

    public void showDiscountDialog() {
        final Dialog discountDialog = new Dialog(mContext);
        discountDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        discountDialog.setContentView(R.layout.user_instruction_dialog1);
        TextView title = (TextView) discountDialog.findViewById(R.id.title);
        title.setText("Enter Trade Discount ?");
        final EditText edInst = (EditText) discountDialog.findViewById(R.id.ed_input);
        edInst.setHint("00.00");
        edInst.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        Button submit = (Button) discountDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String td = "0";
                if (edInst.getText().toString().length() > 0
                        && !edInst.getText().toString().equalsIgnoreCase(".")) {
                    td = edInst.getText().toString();
                }
                if (Double.parseDouble(td) <= 100) {
                    discountDialog.cancel();
                    trdDisc = td;
                    CalculateTotal();
                } else {
                    Toast.makeText(DOConfirmationActivity.this,
                            "Trade Discount cannot be more than 100%", 2000)
                            .show();
                }
            }
        });
        discountDialog.show();
    }

    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        instructionDialog.setCancelable(false);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Remarks if any?");
        final EditText edInst = (EditText) instructionDialog
                .findViewById(R.id.ed_input);
        edInst.setText(remarks);
        Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                instructionDialog.cancel();
                String instruction = "";
                instruction = edInst.getText().toString();
                remarks = instruction;
            }
        });
        instructionDialog.show();
    }

    public void showInstructionWithHintDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.hint_remarks_dialog);
        instructionDialog.setCancelable(false);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        TextView title2 = (TextView) instructionDialog.findViewById(R.id.title2);
        title.setText("Remarks if any?");
        title2.setText("Any specific Requirement?");
        final EditText edInst = (EditText) instructionDialog
                .findViewById(R.id.ed_input);
        edInst.setText(remarks);
        Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                instructionDialog.cancel();
                String instruction = "";
                instruction = edInst.getText().toString();
                remarks = instruction;
            }
        });
        RadioGroup rgp = (RadioGroup) instructionDialog.findViewById(R.id.radiogroup);
        rgp.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup radioGroup, int id) {
                selectedHintRemarksId = id;
            }
        });

        RadioGroup.LayoutParams rprms;

        for (int i = 0; i < hintRemarksValList.size(); i++) {
            RadioButton radioButton = new RadioButton(this);
            radioButton.setText(hintRemarksValList.get(i));
            radioButton.setId(i + 1);
            radioButton.setTextColor(getResources().getColor(R.color.text_color));
            if (selectedHintRemarksId == i + 1) {
                radioButton.setChecked(true);
            }
            rprms = new RadioGroup.LayoutParams(WindowManager.LayoutParams.WRAP_CONTENT, WindowManager.LayoutParams.WRAP_CONTENT);
            rgp.addView(radioButton, rprms);
        }
        instructionDialog.show();
    }

    public void showChequeNumberBankNameDialog() {
        final Dialog multiRemarksDialog = new Dialog(mContext);
        multiRemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        multiRemarksDialog.setContentView(R.layout.multi_instruction_dialog);
        multiRemarksDialog.setCancelable(false);
        TextView title = (TextView) multiRemarksDialog.findViewById(R.id.title);
        title.setText("Please provide cheque details.");
        final EditText edInst1 = (EditText) multiRemarksDialog.findViewById(R.id.ed_input_r1);
        edInst1.setInputType(InputType.TYPE_CLASS_TEXT);
        final TextView tv1edInst1 = (TextView) multiRemarksDialog.findViewById(R.id.textView1);
        tv1edInst1.setText("Bank Name: ");
        final LinearLayout lldate = (LinearLayout) multiRemarksDialog.findViewById(R.id.lldate);
        lldate.setVisibility(View.GONE);
        final TextView textView3 = (TextView) multiRemarksDialog.findViewById(R.id.textView3);
        textView3.setText("Cheque No: ");

        multipleRemarksButton = (Button) multiRemarksDialog
                .findViewById(R.id.ed_input_r2);
        multipleRemarksButton.setText("");
        final EditText edInst3 = (EditText) multiRemarksDialog
                .findViewById(R.id.ed_input_r3);
        edInst3.setInputType(InputType.TYPE_CLASS_NUMBER);

        Button submit = (Button) multiRemarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                String instruction = "", billTotal;
                bankName = edInst1.getText().toString();
                chequeNo = edInst3.getText().toString();

                if (bankName.length() > 0 && chequeNo.length() > 0) {

                    multiRemarksDialog.cancel();
                } else {
                    Utils.showToast(mContext, "Please enter proper input");
                }
            }
        });
        multiRemarksDialog.show();
    }

    public void showMultiRemarksDialog() {
        final Dialog multiRemarksDialog = new Dialog(mContext);
        multiRemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        multiRemarksDialog.setContentView(R.layout.multi_instruction_dialog);
        multiRemarksDialog.setCancelable(false);
        TextView title = (TextView) multiRemarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        edInst1 = (EditText) multiRemarksDialog.findViewById(R.id.ed_input_r1);


        multipleRemarksButton = (Button) multiRemarksDialog
                .findViewById(R.id.ed_input_r2);
        final EditText edInst3 = (EditText) multiRemarksDialog
                .findViewById(R.id.ed_input_r3);
        edInst3.setFilters(new InputFilter[]{new DecimalDigitsInputFilter(16,
                2)});
        String[] remarksVal = remarks.split(";");
        if (remarksVal != null & remarksVal.length > 0)
            edInst1.setText(remarksVal[0]);
        if (remarksVal != null & remarksVal.length > 1)
            multipleRemarksButton.setText(remarksVal[1]);
        if (remarksVal != null & remarksVal.length > 2)
            edInst3.setText(remarksVal[2]);

        Button submit = (Button) multiRemarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                String instruction = "", billTotal;
                billTotal = edInst3.getText().toString();
                if (Utils.isNumeric(billTotal)) {
//						if(Double.parseDouble(billTotal)>=Double.parseDouble(totalAmount))
//						{
                    String chosenDate = multipleRemarksButton.getText().toString();
                    if (!chosenDate.matches("dd-MM-yyyy")) {
                        Boolean isInputsOk = true;
                        String BIlNo = edInst1.getText().toString().trim();
                        if ((Constants.nickName.equalsIgnoreCase("rkbk") || Constants.nickName.equalsIgnoreCase("rkbkt")) && BIlNo.length() != 5) {
                            isInputsOk = false;
                            Utils.showToast(mContext, "Length of bill no must be 5");
                        }
                        if (isInputsOk) {
                            instruction = BIlNo + ";"
                                    + chosenDate + ";"
                                    + billTotal;
                            remarks = instruction;
                            multiRemarksDialog.cancel();
                            if (isNavigatedFromSalesOption) {
                                submitOrderProcess();
                            }
                        }

                    } else {
                        Utils.showToast(mContext, "Please choose a date");
                    }

//						}
//						else
//						{
//							Utils.showToast(mContext, "Given amount must be equal or higher than total amount")	;
//						}

                } else {
                    Utils.showToast(mContext, "Please provide valid input in bill total");
                }

            }
        });
        multipleRemarksButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

                chooseDateDialog();
            }
        });
//		Utils.showHideSoftKeyBoard(edInst1,mContext,"show");
        multiRemarksDialog.show();
//		edInst1.requestFocus();
        final InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
        edInst1.postDelayed(new Runnable() {
            @Override
            public void run() {
                edInst1.requestFocus();
                imm.showSoftInput(edInst1, 0);
            }
        }, 100);
//		imm.showSoftInput(edInst1, InputMethodManager.SHOW_IMPLICIT);
//		Utils.showHideSoftKeyBoard(edInst1,mContext,"show");
    }

    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    public void showSaleTypeDialog() {
        final Dialog grpDialog = new Dialog(DOConfirmationActivity.this);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.sale_type_dialog);
        grpDialog.setCancelable(false);
        TextView title = (TextView) grpDialog.findViewById(R.id.title);
        title.setText("Select a Sale Type.");
        final RadioButton radioCredit = (RadioButton) grpDialog
                .findViewById(R.id.rd_credit);
        final RadioButton radioCOD = (RadioButton) grpDialog
                .findViewById(R.id.rd_cod);
        final RadioButton radioPay = (RadioButton) grpDialog
                .findViewById(R.id.rd_pay);
        final RadioButton radioCheque = (RadioButton) grpDialog
                .findViewById(R.id.rd_cheque);
        if (Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan")) {
            radioCheque.setVisibility(View.VISIBLE);
        }

        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase(
                "cash")) {
            radioCredit.setVisibility(View.GONE);
        } else {
            radioCredit.setVisibility(View.VISIBLE);
        }

        radioCredit.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView,
                                         boolean isChecked) {
                if (isChecked) {
                    radioCOD.setChecked(false);
                    radioPay.setChecked(false);
                    radioCheque.setChecked(false);
                    saleType = "CREDIT";
                    grpDialog.cancel();
                }
            }
        });
        radioCOD.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView,
                                         boolean isChecked) {
                if (isChecked) {
                    radioCredit.setChecked(false);
                    radioPay.setChecked(false);
                    radioCheque.setChecked(false);
                    saleType = "COD";
                    grpDialog.cancel();
                }
            }
        });
        radioPay.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView,
                                         boolean isChecked) {
                if (isChecked) {
                    radioCredit.setChecked(false);
                    radioCOD.setChecked(false);
                    radioCheque.setChecked(false);
                    saleType = "CASH";
                    grpDialog.cancel();
                }
            }
        });
        radioCheque.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView,
                                         boolean isChecked) {
                if (isChecked) {
                    radioCredit.setChecked(false);
                    radioCOD.setChecked(false);
                    radioPay.setChecked(false);
                    saleType = "CHEQUE";
                    grpDialog.cancel();
                    showChequeNumberBankNameDialog();
                }
            }
        });
        grpDialog.show();
    }
    private void calculateTotalAmountAndShow()
    {
        totalOrderAmount=0.0;
        double totalQtyLoose=0.0,totalQtyCase=0.0;
        for(int x=0;x<selectedProductMasterList.size();x++)
        {
            ProductMasterDetails productMasterDetails = selectedProductMasterList.get(x);
            totalOrderAmount=totalOrderAmount+Double.parseDouble(productMasterDetails.getAmount());
            if(!mChosenUomType.equalsIgnoreCase("loose"))
            {
                double totalQtyCaseCurrent= Double.parseDouble(productMasterDetails.getQty());
                double totalQtyLooseCurrent = mAceDnsDatabase.calculatedValueC2MBargain(productMasterDetails.getProdCode(),totalQtyCaseCurrent);
                totalQtyLoose=totalQtyLoose+totalQtyLooseCurrent;
                totalQtyCase=totalQtyCase+totalQtyCaseCurrent;
            }
            else
            {
                double totalQtyLooseCurrent= Double.parseDouble(productMasterDetails.getQty());
                double totalQtyCaseCurrent = mAceDnsDatabase.calculatedValueM2CBargain(productMasterDetails.getProdCode(),totalQtyLooseCurrent);
                totalQtyLoose=totalQtyLoose+totalQtyLooseCurrent;
                totalQtyCase=totalQtyCase+totalQtyCaseCurrent;
            }
        }

        txtTotal.setText("Value: "+defaultFormatWithComma.format(totalOrderAmount));
        String uom=mChosenUomType;

        if(uom.equalsIgnoreCase("loose"))
        {
            txt_totalQty.setText(defaultFormat.format(totalQtyLoose));
        }
        else
        {
            txt_totalQty.setText(defaultFormat.format(totalQtyLoose)+"/"+(int)totalQtyCase);
        }
//        txt_totalQty.setText(defaultFormat.format(totalQtyLoose)+"/"+(int)totalQtyCase);
    }
    public double CalculateTotal() {
        double amount = 0, quantity = 0;
        double productamount = 0;
        Double amountFinal = 0.0;
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes")
                    && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") && !Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes"))

            {
                for (int ii = 0; ii < selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double discount = Double.parseDouble(currentObj
                            .getTradeDiscnt());
                    double premium = 0;
                    if (currentObj.getPremium().trim().length() > 0) {
                        premium = Double.parseDouble(currentObj
                                .getPremium());
                    }
                    quantity = quantity + qty;
                    if (Constants.orderFormDetailsObj.getTdCalc().equalsIgnoreCase("amount")) {
                        productamount = (qty * (mrp - discount)) + (premium * qty);
                        amount = amount + productamount;
                    } else {
                        productamount = ((mrp * qty) - (mrp * qty * discount / 100)) + (premium * qty);
                        amount = amount + productamount;
                    }

                    selectedProductMasterList.get(ii).setAmount(String.valueOf(productamount));

                }
            } else if (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") && Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes")) {

                if (!mOrderPriceValidationType.matches("DOPS")) {
                    for (int ii = 0; ii < selectedProductMasterList.size(); ii++) {
                        ProductMasterDetails currentObj = selectedProductMasterList
                                .get(ii);
                        double qty = Double.parseDouble(currentObj.getQty());
                        quantity = quantity + qty;
                        String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                                .getMrpValue() : currentObj.getAmount();
                        double mrp = Double.parseDouble(mrpVal);
                        amount = amount + (mrp * qty);
                        Boolean qtyEligibleForTD = Utils.checkIfGivenQuantityEligibleForTD(mContext, currentObj.getQty(), currentObj.getProdCode(), Constants.selectedCustomer.getBranchCode(), Constants.selectedCustomer.getCustClass());
                        selectedProductMasterList.get(ii).setQuantityEligibleForTD(qtyEligibleForTD);
                        selectedProductMasterList.get(ii).setTDPercent(Constants.currentTdPercent);
                        productamount = qty * mrp;
                        if (qtyEligibleForTD) {

                            amount = productamount - (productamount * (Double.parseDouble(Constants.currentTdPercent) / 100));
                            selectedProductMasterList.get(ii).setAmount(defaultFormat.format(amount));
                            amountFinal = amountFinal + amount;
                        } else {
                            selectedProductMasterList.get(ii).setAmount(defaultFormat.format(productamount));
                            amountFinal = amountFinal + productamount;
                        }
                    }
                }

            } else {
                for (int ii = 0; ii < selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double produtAmount = mrp * qty;
//					produtAmount = calculateSchemesTotalAmount(ii, qty, produtAmount);
                    String formattedAmount = defaultFormat.format(produtAmount);
                    selectedProductMasterList.get(ii).setAmount(formattedAmount);
                    amount = amount + produtAmount;
                    quantity = quantity + qty;
                }
                totalOrderQty=String.valueOf(quantity);
                amount = (amount) - (amount * Double.parseDouble(trdDisc) / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase("amount")) {
                for (int ii = 0; ii < selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    double currentAmount = (mrp * qty) + vat;
                    selectedProductMasterList.get(ii).setAmount(defaultFormat.format(currentAmount));
                    amount = amount + currentAmount;
                    quantity = quantity + qty;
                }
                totalOrderQty=String.valueOf(quantity);
            } else {
                for (int ii = 0; ii < selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    double currentamount = (mrp * qty) + (mrp * qty * vat / 100);
                    selectedProductMasterList.get(ii).setAmount(defaultFormat.format(currentamount));
                    amount = amount + currentamount;
                    quantity = quantity + qty;
                }
                totalOrderQty=String.valueOf(quantity);
            }
        }
        if (mOrderPriceValidationType.matches("DOPS")) {
            Double amountTotal = 0.0, qtyTotal = 0.0;
            for (int ii = 0; ii < selectedProductMasterList
                    .size(); ii++) {
                amountTotal = Double.parseDouble(selectedProductMasterList.get(ii).getAmount()) + amountTotal;
                qtyTotal = Double.parseDouble(selectedProductMasterList.get(ii).getQty()) + qtyTotal;
            }
            totalOrderValue = "" + defaultFormat.format(amountTotal);
            totalOrderQty = "" + defaultFormat.format(qtyTotal);
            txtTotal.setText(currency + " " + defaultFormatWithComma.format(amountTotal));
            totalAmount = amountTotal + "";
        } else {
            totalOrderValue = "" + defaultFormat.format(amount);
            totalOrderQty = "" + defaultFormat.format(quantity);
            txtTotal.setText(currency + " " + defaultFormatWithComma.format(amount));
            totalAmount = amount + "";
        }

        if (!Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") && !Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
            double qty = 0.0;
            for (int ii = 0; ii < selectedProductMasterList.size(); ii++) {
                ProductMasterDetails currentObj = selectedProductMasterList
                        .get(ii);
                qty = qty + Double.parseDouble(currentObj.getQty());
            }
            TextView textViewheading = (TextView) findViewById(R.id.textViewheading);
            textViewheading.setText("Total Volume :");
            txtTotal.setText(defaultFormatWithComma.format(qty));
        }
        return amount;
    }


    @Override
    public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
//        ShowEditQuantityDialog(arg2);
    }

    @Override
    public boolean onItemLongClick(AdapterView<?> arg0, View arg1, int arg2,
                                   long arg3) {
//        showDeleteItemDialog(arg2);
        return true;
    }

    public void showDeleteItemDialog(final int pos)
    {
        System.out.println("POSITION::::::::::" + pos);
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                DOConfirmationActivity.this);
        alertDialogBuilder.setMessage(
                "Are you sure you want to delete this item ?").setCancelable(
                false);
        alertDialogBuilder.setNegativeButton("OK",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();
                        ProductMasterDetails deletedProductObj = selectedProductMasterList.get(pos);

                        selectedProductMasterList.remove(deletedProductObj);
                        deleteFreeBiesForCurrentProduct(deletedProductObj);
                        removeAndAddFilter2Schemes();
                        CalculateTotal();


                        if (carryInSales) {
                            dataHelperObj.increaseClStkProductWise(deletedProductObj);
                        }

                        if (!(Constants.orderFormDetailsObj.getVat()
                                .equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
                                .getVatCalcOn().equalsIgnoreCase("ordervalue"))) {
                            introduceVATInProduct();
                        }
                        if (selectedProductMasterList.size() == 0) {
                            finish();
                            // Clear all Lists.
                        } else {
                            switch (Integer
                                    .parseInt(Constants.productDetailsObj
                                            .getNoFilter())) {
                                case 1:
                                    // Only ProductList. Item has already been
                                    // removed.
                                case 2:
                                    // ProductList and GroupList.
                                    if (Constants.selectedGroupList.size() > 0) {
                                        for (int kk = 0; kk < Constants.selectedGroupList
                                                .size(); kk++) {
                                            ProductGroupDetails currentItem = Constants.selectedGroupList
                                                    .get(kk);
                                            if (deletedProductObj
                                                    .getGrpCode()
                                                    .equalsIgnoreCase(
                                                            currentItem
                                                                    .getGroupCode())) {
                                                Constants.selectedGroupList
                                                        .remove(Constants.selectedGroupList
                                                                .get(kk));
                                            }
                                        }
                                    }
                                    break;
                                case 3:
                                    // ProductList and GroupList and SubGroup List.
                                    if (Constants.selectedSubGroupList.size() > 0) {
                                        for (int kk = 0; kk < Constants.selectedSubGroupList
                                                .size(); kk++) {
                                            ProductSubGrpDetails currentSubGrp = Constants.selectedSubGroupList
                                                    .get(kk);
                                            if (deletedProductObj
                                                    .getSubGrpCode()
                                                    .equalsIgnoreCase(
                                                            currentSubGrp
                                                                    .getSubGrpCode())) {
                                                Constants.selectedSubGroupList
                                                        .remove(Constants.selectedSubGroupList
                                                                .get(kk));
                                                String grpCode = currentSubGrp
                                                        .getGrpCode();
                                                if (Constants.selectedGroupList
                                                        .size() > 0) {
                                                    for (int x = 0; x < Constants.selectedGroupList
                                                            .size(); x++) {
                                                        ProductGroupDetails currentItem = Constants.selectedGroupList
                                                                .get(x);
                                                        if (grpCode
                                                                .equalsIgnoreCase(currentItem
                                                                        .getGroupCode())) {
                                                            Constants.selectedGroupList
                                                                    .remove(Constants.selectedGroupList
                                                                            .get(kk));
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 4:

                                    if (Constants.selectedBrandList.size() > 0) {
                                        for (int kk = 0; kk < Constants.selectedBrandList
                                                .size(); kk++) {
                                            ProductBrandDetails currentBrand = Constants.selectedBrandList
                                                    .get(kk);
                                            if (deletedProductObj
                                                    .getBrndCode()
                                                    .equalsIgnoreCase(
                                                            currentBrand
                                                                    .getBrandCode())) {
                                                Constants.selectedBrandList
                                                        .remove(Constants.selectedBrandList
                                                                .get(kk));
                                                String subGrp = currentBrand
                                                        .getSubGrpCode();
                                                if (Constants.selectedSubGroupList
                                                        .size() > 0) {
                                                    for (int xx = 0; xx < Constants.selectedSubGroupList
                                                            .size(); xx++) {
                                                        ProductSubGrpDetails currentSubGrp = Constants.selectedSubGroupList
                                                                .get(xx);
                                                        if (subGrp
                                                                .equalsIgnoreCase(currentSubGrp
                                                                        .getSubGrpCode())) {
                                                            Constants.selectedSubGroupList
                                                                    .remove(Constants.selectedSubGroupList
                                                                            .get(xx));
                                                            String grp = currentSubGrp
                                                                    .getGrpCode();
                                                            if (Constants.selectedGroupList
                                                                    .size() > 0) {
                                                                for (int yy = 0; yy < Constants.selectedGroupList
                                                                        .size(); yy++) {
                                                                    ProductGroupDetails currentGrp = Constants.selectedGroupList
                                                                            .get(yy);
                                                                    if (grp.equalsIgnoreCase(currentGrp
                                                                            .getGroupCode())) {
                                                                        Constants.selectedGroupList
                                                                                .remove(Constants.selectedGroupList
                                                                                        .get(yy));
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    break;
                            }
                            adapter.notifyDataSetChanged();
                        }
                    }
                });
        alertDialogBuilder.setPositiveButton("CANCEL",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();
                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    public void showDeleteItemDialogItemReturn()
    {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                DOConfirmationActivity.this);
        alertDialogBuilder.setMessage(
                "Do you have any return item?").setCancelable(
                false);
        alertDialogBuilder.setNegativeButton("NO",
                new DialogInterface.OnClickListener()
                {
                    public void onClick(DialogInterface dialog, int id)
                    {
                        dialog.cancel();
                        orderSubmitProcess();
                    }
                });
        alertDialogBuilder.setPositiveButton("YES",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();
                        vanSalesReturnConfirmationDone=true;
                        showReturnProductList();

                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    public String setMrpValue(String customerType, String getMultipleRate, int i, String prodCode, String selectedUom) {
        String mrpValue = "0";
        ArrayList<MRPDetails> mrpSpinnerList = mAceDnsDatabase.getMRPList(prodCode, selectedUom);
        if (mrpSpinnerList != null && mrpSpinnerList.size() > 0) {
            selectedProductMasterList.get(i).setMrpCode(mrpSpinnerList.get(0).getMrpCode());
            if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                mrpValue = mrpSpinnerList.get(0).getMrpValue();

            } else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {

                if (getMultipleRate.equalsIgnoreCase("yes")) {
                    if (customerType.matches("R") || customerType.matches("")) {
                        mrpValue = mrpSpinnerList.get(0).getSaleRate();
                    } else if (customerType.matches("D")) {
                        mrpValue = mrpSpinnerList.get(0).getDistributorRate();

                    } else if (customerType.matches("WS")) {
                        mrpValue = mrpSpinnerList.get(0).getWSRate();

                    } else if (customerType.matches("SS")) {
                        mrpValue = mrpSpinnerList.get(0).getSSRate();
                    } else if (customerType.matches("DEPOT")) {
                        mrpValue = mrpSpinnerList.get(0).getDepotRate();
                    }
                } else {
                    mrpValue = mrpSpinnerList.get(0).getSaleRate();
                }
            }
        }

        selectedProductMasterList.get(i).setMrpValue(mrpValue);
        return mrpValue;
    }

    public void ShowEditQuantityDialog(final int position) {
        final DecimalFormat defaultFormat = new DecimalFormat("0.00");

        final ProductMasterDetails productMasterDetails = selectedProductMasterList.get(position);
        final boolean isTD = productMasterDetails
                .getIsTradeDiscount();

        final Double previousQty = Double
                .parseDouble(productMasterDetails
                        .getQty());
        final Dialog edQtyDialog = new Dialog(DOConfirmationActivity.this);
        edQtyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        edQtyDialog.setContentView(R.layout.edit_delivery_order_dialog);
        TextView title = (TextView) edQtyDialog.findViewById(R.id.title);
        title.setText("Provide valid quantity");

        final LinearLayout discountLayout = (LinearLayout) edQtyDialog
                .findViewById(R.id.disc_layout);
        final LinearLayout amountLayout = (LinearLayout) edQtyDialog
                .findViewById(R.id.amt_layout);

        TextView txtTDVatTitle = (TextView) edQtyDialog
                .findViewById(R.id.txt_td_vat);
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            txtTDVatTitle.setText("VAT");
        }

        if (Constants.orderFormDetailsObj.getPremium().equalsIgnoreCase("yes")) {
            if (true == isTD) {
                txtTDVatTitle.setText("TD");
            } else {
                txtTDVatTitle.setText("Premium");
            }
        }

        final TextView totalStockTV =  edQtyDialog.findViewById(R.id.totalStockTV);
        final TextView balnceQtyTV =  edQtyDialog.findViewById(R.id.balnceQtyTV);
        final View totalStockView =  edQtyDialog.findViewById(R.id.totalStockView);
        final String qty = productMasterDetails.getQty();
        final String rate = productMasterDetails.getMrpValue();
        if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes"))
        {
            totalStockTV.setVisibility(View.VISIBLE);
            totalStockView.setVisibility(View.VISIBLE);
            totalStockTV.setText(HtmlCompat.fromHtml("Total Stock : <font color='#D7B56D'>"+productMasterDetails.getClosingStk()+"</font>",HtmlCompat.FROM_HTML_MODE_LEGACY));
        }
        else
        {
            totalStockTV.setVisibility(View.GONE);
            totalStockView.setVisibility(View.GONE);
        }
        Double QtyRemaining=Double.parseDouble(productMasterDetails.gettotalBargainQuantity());
        for(int x=0;x<Constants.selectedProductMasterList.size();x++)
        {
            ProductMasterDetails currentItem = Constants.selectedProductMasterList.get(x);
            if(currentProductPresentInList(currentItem.getProdCode(),productMasterDetails.getparentProdCode()))
            {
                if(Utils.isNumeric(currentItem.getQty()) && Double.parseDouble(currentItem.getQty())>0)
                {
                    QtyRemaining=QtyRemaining-Double.parseDouble(currentItem.getQty());
                }
            }
        }
        balnceQtyTV.setText(HtmlCompat.fromHtml("Balance Quantity : <font color='#D7B56D'>"+defaultFormat.format(QtyRemaining)+" " +productMasterDetails.getUom1()+"</font>",HtmlCompat.FROM_HTML_MODE_LEGACY));

        final EditText edQty = (EditText) edQtyDialog.findViewById(R.id.ed_qnt);
        final EditText edMrp = (EditText) edQtyDialog
                .findViewById(R.id.ed_sale);
        final EditText edDisc = (EditText) edQtyDialog
                .findViewById(R.id.ed_discount);
        final EditText edAmt = (EditText) edQtyDialog
                .findViewById(R.id.ed_amount);

        if (productMasterDetails.isAmtEntered()) {
            edMrp.setEnabled(false);
            edAmt.setEnabled(true);
        } else {
            edMrp.setEnabled(true);
            edAmt.setEnabled(false);
        }

        edQty.setText(productMasterDetails
                .getQty());
        // edMrp.setText(Constants.selectedProductMasterList.get(position).getMrpValue());
        edMrp.setText(defaultFormat.format(Double
                .parseDouble(productMasterDetails
                        .getMrpValue())));
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            edDisc.setText(productMasterDetails
                    .getVat());
        } else {
            if (Constants.orderFormDetailsObj.getPremium().equalsIgnoreCase("yes")) {
                if (true == isTD) {
                    edDisc.setText(productMasterDetails
                            .getTradeDiscnt());
                } else {
                    edDisc.setText(productMasterDetails
                            .getPremium());
                }

            } else {
                edDisc.setText(productMasterDetails
                        .getTradeDiscnt());
            }

        }

//        if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase("yes")) {
//            amountLayout.setVisibility(View.VISIBLE);
//            edAmt.setText(defaultFormat.format(Double.parseDouble(selectedProductMasterList.get(position).getAmount())));
//        } else {
            amountLayout.setVisibility(View.GONE);
//        }

        edQty.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edMrp.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edDisc.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edAmt.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);

            discountLayout.setVisibility(View.GONE);

        final Spinner uomSpinner = (Spinner) edQtyDialog.findViewById(R.id.uomSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<String>();
        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
            String selectedUom = productMasterDetails.getUomSelectedForProduct();
            String Uom1 = productMasterDetails.getUom1();
            String Uom2 = productMasterDetails.getUom2();
            final LinearLayout uomLayout = (LinearLayout) edQtyDialog
                    .findViewById(R.id.uom_layout);
            uomLayout.setVisibility(View.VISIBLE);

            spinnerArray.add(Uom1);
            spinnerArray.add(Uom2);


            ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_dropdown_item, spinnerArray);
            uomSpinner.setAdapter(spinnerArrayAdapter);

            ArrayAdapter myAdap = (ArrayAdapter) uomSpinner.getAdapter(); //cast to an ArrayAdapter

            int spinnerPosition = myAdap.getPosition(selectedUom);
            uomSpinner.setSelection(spinnerPosition);

        }

        Button submit = (Button) edQtyDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                String qtyInput = "";

                qtyInput = edQty.getText().toString();
                double qtyInputInDouble = Double.parseDouble(qtyInput);
                if(Utils.isNumeric(qtyInput) )
                {
                    if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes"))
                    {
                        if(Utils.isNumeric(productMasterDetails.getClosingStk()))
                        {
                            if(qtyInputInDouble>Double.parseDouble(productMasterDetails.getClosingStk()))
                            {
                                Toast.makeText(mContext, "Insufficient Stock.", Toast.LENGTH_SHORT).show();
                                return;
                            }
                        }
                        totalStockTV.setVisibility(View.VISIBLE);
                        totalStockView.setVisibility(View.VISIBLE);
                        totalStockTV.setText(HtmlCompat.fromHtml("Total Stock : <font color='#D7B56D'>"+productMasterDetails.getClosingStk()+"</font>",HtmlCompat.FROM_HTML_MODE_LEGACY));
                    }
                    if(isMaxQtyForDeliveryNotExceeded(Double.parseDouble(qty), qtyInputInDouble,productMasterDetails.gettotalBargainQuantity(),productMasterDetails.getparentProdCode()))
                    {
                        double amnt = Double.parseDouble(qty) * Double.parseDouble(rate);
                        productMasterDetails.setQty(qtyInput);
                        productMasterDetails.setAmount(defaultFormat.format(amnt));
                        adapter.notifyDataSetChanged();
                        calculateTotalAmountAndShow();

                        edQtyDialog.cancel();

                        CalculateTotal();
                    }
                    else
                    {
                        Toast.makeText(mContext, "Exceeded max Quantity.", Toast.LENGTH_SHORT).show();
                    }
                }
                else
                {
                    Toast.makeText(mContext, "Provide Proper Quantity.", Toast.LENGTH_SHORT).show();
                }

            }
        });
        edQtyDialog.show();
    }
    public boolean isMaxQtyForDeliveryNotExceeded(Double currentQty,Double inputQty,String maxavailableQty,String parentProcCode)
    {
        Double maxQtyAvailable=0.0,maxQtyAddedToCart=inputQty;
        if(Utils.isNumeric(maxavailableQty))
        {
            maxQtyAvailable=Double.parseDouble(maxavailableQty)+currentQty;
        }
        for(int i=0;i<Constants.selectedProductMasterList.size();i++)
        {

            Double qtyForCurrentItem=0.0;
            ProductMasterDetails currentItem = Constants.selectedProductMasterList.get(i);
            if(Utils.isNumeric(currentItem.getQty()))
            {
                if(currentProductPresentInList(currentItem.getProdCode(),parentProcCode))
                {
                    qtyForCurrentItem=Double.parseDouble(currentItem.getQty());
                    maxQtyAddedToCart=maxQtyAddedToCart+qtyForCurrentItem;
                }

            }
        }
        if(maxQtyAddedToCart<=maxQtyAvailable)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public boolean currentProductPresentInList(String prodCode,String parentProdCode)
    {
        ArrayList<ProductMasterDetails> productMasterList = mAceDnsDatabase.getProductMasterListDO();
        Boolean currentProductPresentInList=false;
        for(int i=0;i<productMasterList.size();i++)
        {
            String currentProdCode=productMasterList.get(i).getProdCode();
            if(currentProdCode.matches(prodCode))
            {
                currentProductPresentInList= true;
                break;

            }
        }
        return currentProductPresentInList;
    }

    public void deleteFreeBiesForCurrentProduct(ProductMasterDetails productMasterDetails) {
        if (productMasterDetails.getSchemePresent()) {
            final ArrayList<String> schemesOnCurrentProduct = new ArrayList<>(Arrays.asList(productMasterDetails.getSchemeIds().split(",")));
            for (int i = 0; i < selectedProductMasterListFreebies.size(); i++) {
                ProductMasterDetails FreeProductsOnCurrentItem = selectedProductMasterListFreebies.get(i);
                if (schemesOnCurrentProduct.contains(FreeProductsOnCurrentItem.getSchemeIds())) {
                    selectedProductMasterListFreebies.remove(i);
                    i--;
                }
            }
            freeBiesAdapter.notifyDataSetChanged();
        }
    }

    public void addFreeBiesForCurrentProduct(ProductMasterDetails productMasterDetails) {
        String qty = productMasterDetails.getQty();
        String rate = productMasterDetails.getMrpValue();
        double amnt = Double.parseDouble(qty) * Double.parseDouble(rate);
        if (productMasterDetails.getSchemePresent()) {
            final ArrayList<String> schemesOnCurrentProduct = new ArrayList<>(Arrays.asList(productMasterDetails.getSchemeIds().split(",")));
            for (int i = 0; i < schemesOnCurrentProduct.size(); i++) {
                ArrayList<SchemeFreebiesDetails> FreeProductsOnCurrentItem = schemeListForCurrentProducts.get(schemesOnCurrentProduct.get(i));
                for (int x = 0; x < FreeProductsOnCurrentItem.size(); x++) {
                    SchemeFreebiesDetails item = FreeProductsOnCurrentItem.get(x);
                    if (!item.getqty().matches("0"))//offer on qty
                    {
                        if (doesQuantityMatchesQtySlab(qty, item.getqty())) {
                            if (!item.getfreebiesProdCode().trim().matches(""))//free product
                            {
                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, mContext);
                            } else//offer on total amount
                            {
                                amnt = getAmnt(amnt, item);
                            }

                        } else {
                            if (!item.getfreebiesProdCode().trim().matches(""))//free product
                            {
                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, mContext);
                            }
                        }
                    } else {
                        if (amnt >= Double.parseDouble(item.getamount())) {
                            if (!item.getfreebiesProdCode().trim().matches(""))//free product
                            {
                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, mContext);
                            } else {
                                amnt = getAmnt(amnt, item);
                            }

                        } else {
                            if (!item.getfreebiesProdCode().trim().matches(""))//free product
                            {
                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, mContext);
                            }
                        }
                    }
                }
            }
            freeBiesAdapter.notifyDataSetChanged();
        }
    }

    private void calculatePrintStringSalesBill() {
        finalPrintStringSalesBill = new SpannableStringBuilder();
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printRKBKHeaderLineSalesBill());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printRKBKAddress());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());


        finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineTwo(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(Constants.selectedCustomer.getCustomerName(), 42)));

//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        //TODO take this date time function to a separate class
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        currentDateandTime = sdf.format(new Date());
        currentInvoiceNumber = generateNewInvoiceNumber();
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printInvoiceDateLineSalesBill(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceNumber, 48), currentDateandTime));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineSix());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

        int sizeOfTotalItemsInCart = selectedProductMasterList.size();

        Double vatRate = 4.00;
        Double AdditionalVatRate = 1.00;
        Double AdditionalVat = 0.00;
        grandTotal = 0;

        for (int i = 0; i < sizeOfTotalItemsInCart; i++) {
            int Sl = i + 1;
            String currentProductDescription = selectedProductMasterList.get(i).getDesc();
            vatRate = Double.parseDouble(selectedProductMasterList.get(i).getVatRate());

            if (shouldPrintFreightBill) {
                freightCost = freightCost + Double.parseDouble(selectedProductMasterList.get(i).getFreightCost());
            }


            AdditionalVatRate = Double.parseDouble(selectedProductMasterList.get(i).getAdditionalVatRate());
            Double currentQuantity = Double.parseDouble(selectedProductMasterList.get(i).getQty());
            Double currentRate = Double.parseDouble(selectedProductMasterList.get(i).getMrpValue().length() > 0 ? selectedProductMasterList.get(i).getMrpValue() : selectedProductMasterList.get(i).getAmount());
            Double currentAmount = currentQuantity * currentRate;

            if (vatRateList.size() == 0) {
                vatRateList.add(vatRate);
                TotalAmountForEachVatRate.add(currentAmount);
            } else {
                int position = compareVatRateListWithCurrentItem(vatRate);
                if (position == -1) {
                    vatRateList.add(vatRate);
                    TotalAmountForEachVatRate.add(currentAmount);
                } else {
                    Double presentAmount = TotalAmountForEachVatRate.get(position);
                    TotalAmountForEachVatRate.set(position, presentAmount + currentAmount);
                }
            }

            finalPrintStringSalesBill.append(PrintTextFormatterObject.addOneColumn(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(Sl + ".", 3), PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentProductDescription, 15), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(vatRate), 6), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(currentQuantity), 8), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(currentRate), 8), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(currentAmount), 10)));

            grandTotal = grandTotal + currentAmount;
        }
        for (int l = 0; l < 1 - sizeOfTotalItemsInCart; l++) {
            finalPrintStringSalesBill.append(PrintTextFormatterObject.addOneColumn(PrintTextFormatterObject.addSpace(3), PrintTextFormatterObject.addSpace(15), PrintTextFormatterObject.addSpace(6), PrintTextFormatterObject.addSpace(8), PrintTextFormatterObject.addSpace(8), PrintTextFormatterObject.addSpace(10)));
        }

        AdditionalVat = grandTotal * (AdditionalVatRate / 100);
        totalPriceWithoutVat = grandTotal;
        for (int i = 0; i < vatRateList.size(); i++) {
            Double currentVatRate = vatRateList.get(i);
            Double amountForCurrentVatRate = TotalAmountForEachVatRate.get(i);
            String formattedVatRate = df.format(currentVatRate);
            if (formattedVatRate.matches(".00")) {
                formattedVatRate = "0.00";
            }
            String vatDesc = "VAT @" + formattedVatRate + "% on amount " + df.format(amountForCurrentVatRate);
            Double VatAmountCurrent = amountForCurrentVatRate * (currentVatRate / 100);
            String formattedVatAmount = df.format(VatAmountCurrent);
            if (formattedVatAmount.matches(".00")) {
                formattedVatAmount = "0.00";
            }

            finalPrintStringSalesBillVatString.append(PrintTextFormatterObject.printLineVateRateLoop(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(vatDesc, 39), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(formattedVatAmount, 12)));
            totalPriceWithoutVat = totalPriceWithoutVat - VatAmountCurrent;
        }
        String formattedAdditionalVat = "";
        if (AdditionalVat > 0) {
            formattedAdditionalVat = df.format(AdditionalVat);
        } else {
            formattedAdditionalVat = "0.00";
        }
        finalPrintStringSalesBillVatString.append(PrintTextFormatterObject.printLineEightAdditionalVat(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(formattedAdditionalVat, 12), PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(df.format(AdditionalVatRate) + "%", 6)));
        totalPriceWithoutVat = totalPriceWithoutVat - AdditionalVat;


        finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

//		finalPrintStringSalesBill.append(PrintTextFormatterObject.printVatBreakUpString());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printGrandTotal(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(grandTotal), 12)));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printTotalPriceWithoutVat(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(totalPriceWithoutVat), 12)));
        finalPrintStringSalesBill.append(finalPrintStringSalesBillVatString);

//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));


//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));


        String grandTotalInWords = new EnglishNumberToWords().convert((long) grandTotal);
        double grandTotalPaisa = Double.parseDouble(df.format(grandTotal).split("\\.")[1]);
        String grandTotalInWordsPaisa = new EnglishNumberToWords().convert((long) grandTotalPaisa);
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineSevenAmountInWords(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(grandTotalInWords + "", 49)));
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineSevenAmountInWords(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(grandTotalInWords+" Rupees and "+grandTotalInWordsPaisa+" Paisa Only",49)));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));


        finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineEandOE());

    }

    private void calculatePrintStringMoneyReceipt() {
        finalPrintStringMoneyReceipt = new SpannableStringBuilder();

        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKHeaderLineSalesBill2());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLine());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKAddress());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLine());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine3(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(Constants.selectedCustomer.getCustomerName(), 31)));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printDateFreight(new SimpleDateFormat("dd/MM/yy").format(new Date())));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine4(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(df.format(grandTotal), 38)));

//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printLineTwo(PrintTextFormatterObject.printDateFreight(new SimpleDateFormat("dd/MM/yy").format(new Date()))));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printLineTwo(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(Constants.selectedCustomer.getCustomerName(),42)));

//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKHeaderLineMoneyReceipt());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKAddress());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printInvoiceMoneyReceipt(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceNumber,51)));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin());

//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());

//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine5());
        String paymentTypeString = calculatePaymentDetailsLine();
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine6(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(paymentTypeString, 57)));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine7(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceNumber, 43)));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine8());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine9());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine10());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printLineEandOE());

    }

    private String calculatePaymentDetailsLine() {
        String paymentTypeString = "";
        if (saleType.matches("CREDIT")) {
            paymentTypeString = "By " + saleType;
        } else if (saleType.matches("COD")) {
            paymentTypeString = "By CASH ON DELIVERY";
        } else if (saleType.matches("CASH")) {
            paymentTypeString = "By CASH";
        } else//CHEQUE
        {
            paymentTypeString = "By " + saleType + " No. " + chequeNo + " of " + bankName.toUpperCase();
        }

        return paymentTypeString;
    }


    public void showPrintingOptionDialog() {
        final Dialog printDialog = new Dialog(DOConfirmationActivity.this);
        printDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        printDialog.setContentView(R.layout.printing_dialog);
        printDialog.setCancelable(false);
        TextView title = (TextView) printDialog.findViewById(R.id.title);
        title.setText("Would you like to have a print of the Order?");
        Button yes = (Button) printDialog.findViewById(R.id.btn_yes);
        Button no = (Button) printDialog.findViewById(R.id.btn_no);
        yes.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

                printDialog.cancel();

            }
        });
        no.setOnClickListener(v -> {
            printDialog.cancel();
            startActivity(new Intent(DOConfirmationActivity.this,
                    MenuActivity.class));
            finish();
        });
        printDialog.show();
    }

    public void saveOrderDataToDatabase() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {

                String timeStamp = "";

                timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

                /*timeStamp="20161126145721"; yyyyMMddHHmmss*/

                boolean isTransactionIDExist = false;
                do
                    {
                    isTransactionIDExist = dataHelperObj.IsTransactioIdExist(timeStamp);
                    if (true == isTransactionIDExist) {
                        timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    }
                }
                while (true == isTransactionIDExist);

                String orderHeaderPrefix="DO";
                Boolean  isSuccessInsertToTransactionLogTable = true,
                        isSuccessInsertToDoTransactionTable = true, isSuccessInsertToLocationTable = true,isSuccessInserttoCallDuration = true;
                dataHelperObj.beginTransaction();

                removeEmptyQtyDataForOrder();
                if(selectedProductMasterList.size()>0)
                {
                    Constants.mDestinationCode = "";
                    Constants.mOrderType = "";
                    Constants.mFreightComponent = "";
                    if(deliveryDate.equalsIgnoreCase(""))
                    {
                        final Calendar c = Calendar.getInstance();
                        int mYear2 = c.get(Calendar.YEAR);
                        int mMonth2 = c.get(Calendar.MONTH)+1;
                        int mDay2 = c.get(Calendar.DAY_OF_MONTH);
                        deliveryDate = mYear2+"-"+ (mMonth2 + 1) +"-"+mDay2;
                        deliveryDate=Utils.addDaysToDate(deliveryDate,"yyyy-MM-dd",transitTime);
                    }
                    isSuccessInsertToDoTransactionTable = dataHelperObj.insertToDOTransactionTable(timeStamp);

                    Constants.CurrentOrderTransactionId = "DO" + Constants.employeeDetailObject.getEmpCode() + timeStamp;

                    isSuccessInsertToLocationTable = dataHelperObj.insertToLocationTable1(orderHeaderPrefix, timeStamp);
                    isSuccessInserttoCallDuration = dataHelperObj.inserttoCallDurationTable(orderHeaderPrefix, timeStamp);
                }

                if ( isSuccessInsertToTransactionLogTable && isSuccessInsertToDoTransactionTable && isSuccessInsertToLocationTable && isSuccessInserttoCallDuration )
                {
                    doAmount=txtTotal.getText().toString();
                    Constants.doNo="DO" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                    Constants.doDate=Utils.changeDateFormat("yyyyMMddHHmmss","dd/MM/yyyy HH:mm:ss",timeStamp);
                    Constants.mChosenGstType = "";//clearing chosen gst type
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                    Message msgObj = mHandler.obtainMessage();
                    Bundle b = new Bundle();
                    b.putString("message", "SubmitJobDone");
                    msgObj.setData(b);
                    mHandler.sendMessage(msgObj);
                }
                else
                {
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                    Activity activity = (Activity) mContext;
                    activity.runOnUiThread(new Runnable() {
                        public void run() {
                            loader.cancel();
                            btnSubmit.setEnabled(true);
                            if (localDataSavingFailedAttempt == 0)
                            {
                                Toast.makeText(mContext, "Oops! Something went wrong while saving data. please try again.", Toast.LENGTH_LONG).show();
                                localDataSavingFailedAttempt++;

                            }
                            else if (localDataSavingFailedAttempt == 1)
                            {
                                Toast.makeText(mContext, "Issue likely a bit serious. Try once again.", Toast.LENGTH_LONG).show();
                                localDataSavingFailedAttempt++;
                            }
                            else
                            {
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

    public void removeEmptyQtyDataForOrder() {
        Iterator<ProductMasterDetails> iter = selectedProductMasterList.iterator();
        while (iter.hasNext())
        {
            String qty = iter.next().getQty();
            if (Double.parseDouble(qty) <= 0)
            {
                iter.remove();
            }
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
        for (int ii = 0; ii < size; ii++) {
            if (tempCustomerList.get(ii).getCustomerName().length() >= strCnt) {
                if (tempCustomerList.get(ii).getCustomerName().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
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

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
        //this.getWindow().setType(WindowManager.LayoutParams.TYPE_KEYGUARD);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK
                || keyCode == KeyEvent.KEYCODE_MENU
                || keyCode == KeyEvent.KEYCODE_HOME
                || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    int compareVatRateListWithCurrentItem(Double vatRate) {
        int position = -1;
        for (int i = 0; i < vatRateList.size(); i++) {
            Double aDouble = vatRateList.get(i);
            if (String.valueOf(aDouble).matches(vatRate + "")) {
                position = i;
            }

        }

        return position;
    }

    private String generateNewInvoiceNumber() {
        String invoiceNumber = "";
//		String nickNameOfRds=dataHelperObj.getNickNameOfRdsFromEmployeeCode();
        String nickNameOfRds = "LKO";
        String chronologicalNumber = getChronologicalNumber();
        invoiceNumber = nickNameOfRds + "/" + getFinancialYear() + "/" + Constants.mVerticalValue + "/" + Constants.employeeDetailObject.getEmpCode() + "-" + chronologicalNumber;
//		invoiceNumber=Constants.nickName+"/"+nickNameOfRds+"/"+getFinancialYear()+"/"+Constants.mVerticalValue+"/"+Constants.employeeDetailObject.getEmpCode()+"-"+ chronologicalNumber;
        return invoiceNumber;
    }

    private String getChronologicalNumber() {
        String formattedChronologicalNumber = "";
        int currentNumber = dataHelperObj.getMaxChronologicalNumberFromInvoiceTable();
        if (currentNumber == -1) {
            formattedChronologicalNumber = "00001";
        } else {
            currentNumber = currentNumber + 1;
            if (String.valueOf(currentNumber).length() < 5) {

                DecimalFormat dfChronologicalNumber = new DecimalFormat("00000");
                formattedChronologicalNumber = dfChronologicalNumber.format(currentNumber);
            } else {
                formattedChronologicalNumber = currentNumber + "";
            }
        }


        return formattedChronologicalNumber;
    }

    private String getFinancialYear() {
        SimpleDateFormat sdf = new SimpleDateFormat("yy");
        int currentYear = Integer.parseInt(sdf.format(new Date()));
        int previousYear = currentYear - 1;
        return previousYear + "-" + currentYear;
    }

    private void calculatePrintStringFreightBill() {
        finalPrintStringFreightBill = new SpannableStringBuilder();
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printRKBKHeaderLineSalesBill3());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printRKBKAddress());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());

        //TODO take this date time function to a separate class
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        currentDateandTime = sdf.format(new Date());


        String invoiceAndDate = currentInvoiceNumber + " on Date " + currentDateandTime;

        finalPrintStringFreightBill.append(PrintTextFormatterObject.printLineTwo(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(Constants.selectedCustomer.getCustomerName(), 42)));//
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillHeaderLine());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

//		freightCost = getFreightCostOnGivenDate();
        String formattedGrandTotal = df.format(freightCost);
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillHeaderLine1(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned("Being the amount " + formattedGrandTotal, 57)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillHeaderLine2(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(invoiceAndDate, 56), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(formattedGrandTotal, 18)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));


        double serviceVatAmount = freightCost * (freightServiceTaxPercentage / 100);
        double serviceSwacchVatAmount = freightCost * (freightSwachhBharatPercent / 100);
        double AmountExcludingVat = freightCost - (serviceVatAmount + serviceSwacchVatAmount);

        String vatLine = "Add Service Tax @" + freightServiceTaxPercentage + "%";
        String vatLine2 = "Add Swachh Bharat Cess @" + freightSwachhBharatPercent + "%";

        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillVat1(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(vatLine, 40), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(serviceVatAmount), 17)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillVat1(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(vatLine2, 40), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(serviceSwacchVatAmount), 17)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillVat1(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned("Total", 40), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(AmountExcludingVat), 17)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

        String grandTotalInWords = new EnglishNumberToWords().convert((long) freightCost);
        double grandTotalPaisa = Double.parseDouble(formattedGrandTotal.split("\\.")[1]);
        String grandTotalInWordsPaisa = new EnglishNumberToWords().convert((long) grandTotalPaisa);
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.printLineSevenAmountInWordsFreight(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(grandTotalInWords+" Rupees and "+grandTotalInWordsPaisa+" Paisa Only",54)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printLineSevenAmountInWordsFreight(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(grandTotalInWords + "", 54)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

        finalPrintStringFreightBill.append(PrintTextFormatterObject.printLineEandOE());
    }

}
