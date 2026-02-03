package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;
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
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.OrderConfirmAdapter;
import com.forcepower.acedns.adapter.OrderConfirmAdapterFreebies;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapterStockReturn;
import com.forcepower.acedns.backgroundTask.TRANS_CashDepositeReceiveTask;
import com.forcepower.acedns.backgroundTask.TRANS_PendingRoutePlanBeforeOtherTxn;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitCustomerClassUpdationTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNewCustomerDetailsTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitStockAuditTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitStockReturnTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourAttachmentExportTask;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.RDSDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DecimalDigitsInputFilter;
import com.forcepower.acedns.util.EnglishNumberToWords;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.PrintTextFormatter;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;
import java.util.Iterator;
import java.util.List;

import androidx.fragment.app.FragmentActivity;

public class OrderConfirmationActivity extends FragmentActivity implements
        OnClickListener, OnItemClickListener, OnItemLongClickListener {
    AceDnsDatabase mAceDnsDatabase;
    ListView productListView, list_freebie_prod;
    OrderConfirmAdapter adapter;
    Button btnAddProduct, btnSubmit, btnBack, btnSaleType, btnRemarks, btnDiscount, btnVAT;
    AceDnsTransactionDatabase dataHelperObj;
    Context mContext;
    String remarks = "";
    String saleType = "", trdDisc = "0", totalOrderValue = "", chequeNo = "", bankName = "", currentInvoiceNumber = "", totalOrderQty = "";
    ImageView imhLogo;
    TextView txtTotal;
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
    String billTotal = "0.00";
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
    static final int REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG = 3;
    String supportingAttachmentNameForCustomerImage="";
    Bitmap customerPicBitmapForLocation = null;
    private boolean isCameralaunched = false;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_confirm);
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
        mContext = OrderConfirmationActivity.this;
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        PrintTextFormatterObject = new PrintTextFormatter(mContext);
        initView();
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Constants.isCheckedIn= Utils.NotCheckedOut(mContext);
        Constants.selectedProductStockReturn=new ArrayList<>();
        if(!Constants.isCheckedIn)
        {
            LocationTrackerObject=new LocationTracker(mContext,"order confirm");
        }
        else
        {
            Utils.setCheckInOutLatLongAccuracyToLocationLatLong(mContext);
        }
        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes")
                && Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("customer wise")) {
            trdDisc = Constants.selectedCustomer.getTradeDiscount();
        }

        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase("credit")) {
            saleType = "CREDIT";
        }
        CalculateTotal();
        if(Constants.isVanSales)
        {
            mButtonStkReturn = (Button) findViewById(R.id.stk_return);
            mButtonStkReturn.setVisibility(View.VISIBLE);
            mButtonStkReturn.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v)
                {
                    vanSalesReturnConfirmationDone=true;
                    showReturnProductList();

                }
            });
        }

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    OrderConfirmationActivity.this.runOnUiThread(new Runnable() {
                        public void run()
                        {
                                if (Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes")
                                        && Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes"))
                                {
                                    Constants.OrderTransactionTaskCalledFrom = "order";
                                    new TRANS_SubmitOrderTask(OrderConfirmationActivity.this, false, true).execute();
                                    Handler mHandler = new Handler();
                                    mHandler.postDelayed(new Runnable() {
                                        @Override
                                        public void run() {
                                            String timeStamps = Constants.dateString
                                                    + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                                            Intent intent = new Intent(getApplicationContext(), BluetoothChatActivity.class);
                                            if(Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("BT INTERNAL"))
                                            {
                                                intent = new Intent(getApplicationContext(), BluetoothPrintSeocndProcessActivity.class);
                                            }
                                            Bundle bundle = new Bundle();
                                            bundle.putString("PrintFor", "ORDER");
                                            bundle.putString("ORDER_ID", "O" + Constants.employeeDetailObject.getEmpCode() + timeStamps);
                                            bundle.putString("CustomerName", Constants.selectedCustomer.getCustomerName());
                                            if (Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan")) {
                                                if (android.os.Build.VERSION.SDK_INT < android.os.Build.VERSION_CODES.JELLY_BEAN) {
                                                    Toast.makeText(mContext, "Sorry, you must use a device with jelly bean or higher version to print using wifi", Toast.LENGTH_SHORT).show();
                                                    intent = new Intent(mContext, MenuActivity.class);
                                                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                                                } else {
                                                    bundle.putString("wlanPrintDataSalesBill", currentInvoiceNumber + "");
                                                    bundle.putString("wlanPrintDataMoneyReceipt", finalPrintStringMoneyReceipt + "");
                                                    bundle.putBoolean("shouldPrintFreightBill", shouldPrintFreightBill);

                                                    if (shouldPrintFreightBill) {
                                                        bundle.putString("finalPrintStringFreightBill", finalPrintStringFreightBill + "");
                                                    }
                                                }

                                            }


                                            intent.putExtras(bundle);
                                            startActivity(intent);
                                        }
                                    }, 3000);
                                }
                                else if (Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes")
                                        && Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("no"))
                                {
                                    Constants.OrderTransactionTaskCalledFrom = "order";
                                    if(!supportingAttachmentNameForCustomerImage.matches(""))
                                    {
                                        new TRANS_TourAttachmentExportTask(mContext, "order", "", false,false).execute();
                                    }
                                    new TRANS_SubmitOrderTask(mContext, false).execute();
                                    Handler mHandler = new Handler();
                                    mHandler.postDelayed(new Runnable() {
                                        @Override
                                        public void run() {
                                            showPrintingOptionDialog();
                                        }
                                    }, 3000);
                                }
                                else
                                {
                                    dataHelperObj = new AceDnsTransactionDatabase(mContext);
                                    int recordcount = dataHelperObj.GetUnuploadedCustomerCount();
                                    boolean isExist = dataHelperObj.IsUnuploadedRoutePlanExist();
                                    dataHelperObj.closeDatabase();
                                    if (recordcount > 0) {
                                        new TRANS_SubmitNewCustomerDetailsTask(mContext, true, true, "SYNC").execute();
                                    } else {
                                        //new TRANS_SubmitOrderTask(OrderConfirmationActivity.this,true).execute();
                                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {

                                            if (isExist) {
                                                new TRANS_PendingRoutePlanBeforeOtherTxn(mContext, "ORDER").execute();
                                            } else {
                                                Constants.OrderTransactionTaskCalledFrom = "order";
                                                if(!supportingAttachmentNameForCustomerImage.matches(""))
                                                {
                                                    new TRANS_TourAttachmentExportTask(mContext, "order", "", false,false).execute();
                                                }
                                                new TRANS_SubmitOrderTask(mContext, true).execute();
                                            }
                                        } else {
                                            Constants.OrderTransactionTaskCalledFrom = "order";
                                            if(!supportingAttachmentNameForCustomerImage.matches(""))
                                            {
                                                new TRANS_TourAttachmentExportTask(mContext, "order", "", false,false).execute();
                                            }
                                            new TRANS_SubmitOrderTask(mContext, true).execute();
                                        }
                                    }
                                }
                            if(Constants.isVanSales)
                            {
                                new TRANS_SubmitStockReturnTask(mContext, true, "SYNC").execute();
                            }

                            else if (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes"))
                            {
                                if (Constants.orderAuditType.equalsIgnoreCase("primary") && Constants.retailercareOrderOrAudit.equalsIgnoreCase("order"))
                                {

                                }
                                else
                                {
                                    new TRANS_SubmitStockAuditTask(mContext, true, "SYNC").execute();
                                }

                            }
                            new TRANS_SubmitCustomerClassUpdationTask(mContext, false).execute();

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


        if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes")) {
            removeAndAddFilter2Schemes();
            Constants.freeBiesAdapter = new OrderConfirmAdapterFreebies(OrderConfirmationActivity.this, R.layout.ordr_confrm_list_child, Constants.selectedProductMasterListFreebies);
//			productListView.addFooterView(list_freebie_prod);
            list_freebie_prod.setVisibility(View.VISIBLE);
            list_freebie_prod.setAdapter(Constants.freeBiesAdapter);
        }

        new GPSTracker(mContext);
    }
    private void launchCameraToTakeCustomerPictureThenSubmit()
    {
        Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        if (takePictureIntent.resolveActivity(getPackageManager()) != null)
        {
            isCameralaunched=true;
            startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG);
        }
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
            Toast.makeText(mContext, "No product found in DB, Please contact admin.", Toast.LENGTH_SHORT).show();
        }
    }


    private void removeAndAddFilter2Schemes() {
        if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes")) {
            for (int i = 0; i < Constants.selectedProductMasterListFreebies.size(); i++) {
                if (Constants.selectedProductMasterListFreebies.get(i).getfreebieFilter().equalsIgnoreCase("2")) {
                    Constants.selectedProductMasterListFreebies.remove(i);
                    i--;
                }
            }
            if (Constants.schemeListForProductGroups.size() > 0) {
                for (int i = 0; i < Constants.schemeListForProductGroups.size(); i++) {
                    SchemeFreebiesDetails item = Constants.schemeListForProductGroups.get(i);
                    double totalQty = 0;
                    for (int i2 = 0; i2 < Constants.selectedProductMasterList.size(); i2++) {
                        ProductMasterDetails currentProduct = Constants.selectedProductMasterList.get(i2);
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
        if(!Constants.isCheckedIn && !isCameralaunched)
        {
            LocationTrackerObject.stopLocationUpdates();
        }
    }

    @Override
    public void onStop()
    {
        super.onStop();
        if(!Constants.isCheckedIn && !isCameralaunched)
        {
            LocationTrackerObject.stopLocationUpdates();
        }
    }
    public void initView() {
        txtTotal = (TextView) findViewById(R.id.txt_total);
        imhLogo = (ImageView) findViewById(R.id.imagelogo);
        list_freebie_prod = (ListView) findViewById(R.id.list_freebie_prod);
        productListView = (ListView) findViewById(R.id.list_prod);
        adapter = new OrderConfirmAdapter(OrderConfirmationActivity.this,
                R.layout.ordr_confrm_list_child,
                Constants.selectedProductMasterList, txtTotal, trdDisc,
                carryInSales ? "SB" : "SO");

        productListView.setAdapter(adapter);
        productListView.setLongClickable(true);
        productListView.setOnItemClickListener(OrderConfirmationActivity.this);
        productListView
                .setOnItemLongClickListener(OrderConfirmationActivity.this);

        btnAddProduct = (Button) findViewById(R.id.btn_add);
        btnSubmit = (Button) findViewById(R.id.btn_order);
        btnBack = (Button) findViewById(R.id.back);
        btnSaleType = (Button) findViewById(R.id.btn_saletype);
        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase("credit")) {
            btnSaleType.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        // txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        btnRemarks = (Button) findViewById(R.id.btn_remarks);
        if (isNavigatedFromSalesOption) {
            btnRemarks.setVisibility(View.GONE);
        }
        btnDiscount = (Button) findViewById(R.id.btn_discount);
        btnVAT = (Button) findViewById(R.id.btn_vat);

        btnAddProduct.setOnClickListener(OrderConfirmationActivity.this);
        btnSubmit.setOnClickListener(OrderConfirmationActivity.this);
        btnBack.setOnClickListener(OrderConfirmationActivity.this);
        btnSaleType.setOnClickListener(OrderConfirmationActivity.this);
        btnRemarks.setOnClickListener(OrderConfirmationActivity.this);
        btnDiscount.setOnClickListener(OrderConfirmationActivity.this);
        btnVAT.setOnClickListener(OrderConfirmationActivity.this);

        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase(
                "yes")
                && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))) {
            btnDiscount.setVisibility(View.VISIBLE);
        } else {
            btnDiscount.setVisibility(View.GONE);
        }
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")
                && Constants.orderFormDetailsObj.getVatCalcOn()
                .equalsIgnoreCase("ordervalue")) {
            String[] vatAvailArray = Constants.orderFormDetailsObj.getVatType()
                    .split(",");
            if (Arrays.asList(vatAvailArray).contains("SO")
                    || Arrays.asList(vatAvailArray).contains("SB")) {
                btnVAT.setVisibility(View.VISIBLE);
            } else {
                btnVAT.setVisibility(View.GONE);
            }
        } else {
            btnVAT.setVisibility(View.GONE);
        }
    }

    @Override
    public void onClick(View arg0) {
        if (arg0 == btnAddProduct) {
            finish();
        } else if (arg0 == btnSubmit)
        {

            if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")) {
                if (HttpCalling.isConnectionPossible(mContext)){
                    orderSubmitProcess();
                }else{
                    Utils.showToast(mContext,"You need an active internet connection to use this feature.");
                }
            }else {

                if(!Constants.isVanSales)
                {
                    if (Constants.nickName.equalsIgnoreCase("goldstonet")) {
                        if (!remarks.isEmpty()) {
                            orderSubmitProcess();
                        }else{
                            Utils.showToast(mContext,"Please give a Remark");
                        }
                    }else {
                        orderSubmitProcess();
                    }
                }
                else
                {
                    if(vanSalesReturnConfirmationDone)
                    {
                        orderSubmitProcess();
                    }
                    else
                    {
                        showDeleteItemDialogItemReturn();
                    }
                }
            }



        } else if (arg0 == btnBack) {
            finish();
        } else if (arg0 == btnSaleType) {
            showSaleTypeDialog();
        } else if (arg0 == btnRemarks) {
            if (Constants.orderFormDetailsObj.getInstruction()
                    .equalsIgnoreCase("yes")) {
                if (Constants.orderFormDetailsObj.getHintsRemarks().equalsIgnoreCase("yes")  || Constants.orderFormDetailsObj.getHintsRemarks().equalsIgnoreCase("order")) {
                    hintRemarksValList = new ArrayList<>();
                    String hintRemarksValString = Constants.orderFormDetailsObj.getHintsRemarksVal();
                    if (hintRemarksValString.contains("#")) {
                        String[] arrayOfData = hintRemarksValString.split("#");
                        for (int i = 0; i < arrayOfData.length; i++) {
                            hintRemarksValList.add(arrayOfData[i]);
                        }
                    } else {
                        hintRemarksValList.add(hintRemarksValString);
                    }
                    showInstructionWithHintDialog();
                } else {
                    showInstructionDialog();
                }

            } else {
                showMultiRemarksDialog();
            }
        } else if (arg0 == btnDiscount) {
            showDiscountDialog();
        } else if (arg0 == btnVAT) {
            showVATDialog();
        }
    }

    public void orderSubmitProcess() {
        if (Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") && !Constants.isGettingCurrentLocation)
        {
            Utils.showToast(mContext,"Could not determine your location. Please Check Location Settings");
        }
        else
        {
            if (saleType.length() > 0)
            {
                if (!isNavigatedFromSalesOption)
                {
                    submitOrderProcess();
                }
                else
                {
                    showMultiRemarksDialog();
                }
            }
            else
            {
                Utils.showToast(mContext, "Please select Sale Type");
            }
        }
    }

    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        super.onActivityResult(requestCode, resultCode, data);
        switch (requestCode)
        {
            case 9999:
                if (resultCode == RESULT_CANCELED)
                {
                    if(Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") ||  Constants.menuDetailsObj.getgeo_fencing_menu().contains("order"))
                    {
                        LocationTrackerObject.checkLocationUpdateSharing();
                    }

                }
                else if (resultCode == RESULT_OK)
                {
                    LocationTrackerObject.startLocationUpdates();
                }
                break;
            case REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG:

                if (resultCode == RESULT_OK)
                {
                    if (data != null)
                    {
                        try
                        {
                            Bundle extras = data.getExtras();
                            Bitmap imageBitmap = (Bitmap) extras.get("data");
                            customerPicBitmapForLocation = Utils.getResizedBitmap(imageBitmap, 100, 100);
                            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                            supportingAttachmentNameForCustomerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                            String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                            Utils.storeImageInLocalStorageShowOnImageView(customerPicBitmapForLocation,imagePath);
                            mAceDnsDatabase.updateCustomerLatLongiImage(Constants.selectedCustomer.getCustomerCode(),supportingAttachmentNameForCustomerImage);
                            Constants.selectedCustomer.setbase_latt(Constants.currentLat);
                            Constants.selectedCustomer.setbase_longi(Constants.currentLong);
                            dataHelperObj.insertToSupportingAttachTable(supportingAttachmentNameForCustomerImage, "order");
                            saveOrderDataToDatabase();
                        }
                        catch (Exception e)
                        {
                            Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                        }
                    }
                    else
                    {
                        Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                    }
                }
                else
                {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
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
            if (Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes")
                    && Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan")) {
                showFreightBillPrintingOptionDialog();
            }
            else
            {
                if (Constants.menuDetailsObj.getgeo_fencing_menu().contains("order"))
                {
                    if(Utils.isNumeric(Constants.selectedCustomer.getbase_latt()) && Double.parseDouble(Constants.selectedCustomer.getbase_latt())>0 && Utils.isNumeric(Constants.selectedCustomer.getbase_longi())&& Double.parseDouble(Constants.selectedCustomer.getbase_longi())>0)
                    {
                        saveOrderDataToDatabase();
                    }
                    else
                    {
                        if(!Constants.isGettingCurrentLocation)
                        {
                            saveOrderDataToDatabase();
                        }
                        else
                        {
                            launchCameraToTakeCustomerPictureThenSubmit();
                        }
                    }

                }
                else
                {
                    saveOrderDataToDatabase();
                }

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
                    saveOrderDataToDatabase();
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
        for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
            ProductMasterDetails currentObj = Constants.selectedProductMasterList
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
        edInst.setText(trdDisc+"");

        edInst.setHint("00." +
                "00");
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
                    Toast.makeText(OrderConfirmationActivity.this,
                            "Trade Discount cannot be more than 100%", Toast.LENGTH_SHORT)
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
        if (Constants.nickName.equalsIgnoreCase("goldstonet")) {
            title.setText("Destination , STPC Code & Subdealer Code & Name*");
        }
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
        final Dialog grpDialog = new Dialog(OrderConfirmationActivity.this);
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

    public double CalculateTotal() {
        double amount = 0, quantity = 0;
        double productamount = 0;
        Double amountFinal = 0.0;

        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no"))
        {
            if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))
                    && !Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getTdInputDropDown().equalsIgnoreCase("input"))
            {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++)
                {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
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
                    Double amnt= mrp*qty;
                    if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("sku wise#amount"))
                    {
                        productamount=amnt - discount;
                        amount = amount + productamount;
                    } else {
                        productamount=amnt - ((discount*amnt)/100);
                        amount = amount + productamount;
                    }

                    Constants.selectedProductMasterList.get(ii).setAmount(String.valueOf(productamount));
                }

            }
            else if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes")
                    && !(Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")) && !Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes"))

            {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++)
                {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
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
                    if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("sku wise#amount"))
                    {
                        productamount = (qty * (mrp - discount)) + (premium * qty);
                        amount = amount + productamount;
                    } else {
                        productamount = ((mrp * qty) - (mrp * qty * discount / 100)) + (premium * qty);
                        amount = amount + productamount;
                    }

                    Constants.selectedProductMasterList.get(ii).setAmount(String.valueOf(productamount));

                }
            }
            else if ((Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")) && Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes")) {

                if (!Constants.mOrderPriceValidationType.matches("DOPS")) {
                    for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                        ProductMasterDetails currentObj = Constants.selectedProductMasterList
                                .get(ii);
                        double qty = Double.parseDouble(currentObj.getQty());
                        quantity = quantity + qty;
                        String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                                .getMrpValue() : currentObj.getAmount();
                        double mrp = Double.parseDouble(mrpVal);
                        amount = amount + (mrp * qty);
                        Boolean qtyEligibleForTD = Utils.checkIfGivenQuantityEligibleForTD(mContext, currentObj.getQty(), currentObj.getProdCode(), Constants.selectedCustomer.getBranchCode(), Constants.selectedCustomer.getCustClass());
                        Constants.selectedProductMasterList.get(ii).setQuantityEligibleForTD(qtyEligibleForTD);
                        Constants.selectedProductMasterList.get(ii).setTDPercent(Constants.currentTdPercent);
                        productamount = qty * mrp;
                        if (qtyEligibleForTD) {

                            amount = productamount - (productamount * (Double.parseDouble(Constants.currentTdPercent) / 100));
                            Constants.selectedProductMasterList.get(ii).setAmount(Constants.defaultFormat.format(amount));
                            amountFinal = amountFinal + amount;
                        } else {
                            Constants.selectedProductMasterList.get(ii).setAmount(Constants.defaultFormat.format(productamount));
                            amountFinal = amountFinal + productamount;
                        }
                    }
                }

            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double produtAmount = mrp * qty;
//					produtAmount = calculateSchemesTotalAmount(ii, qty, produtAmount);
                    String formattedAmount = Constants.defaultFormat.format(produtAmount);
                    Constants.selectedProductMasterList.get(ii).setAmount(formattedAmount);
                    amount = amount + produtAmount;
                    quantity = quantity + qty;
                }
                totalOrderQty=String.valueOf(quantity);
                amount = (amount) - (amount * Double.parseDouble(trdDisc) / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase("amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    double currentAmount = (mrp * qty) + vat;
                    Constants.selectedProductMasterList.get(ii).setAmount(Constants.defaultFormat.format(currentAmount));
                    amount = amount + currentAmount;
                    quantity = quantity + qty;
                }
                totalOrderQty=String.valueOf(quantity);
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    double currentamount = (mrp * qty) + (mrp * qty * vat / 100);
                    Constants.selectedProductMasterList.get(ii).setAmount(Constants.defaultFormat.format(currentamount));
                    amount = amount + currentamount;
                    quantity = quantity + qty;
                }
                totalOrderQty=String.valueOf(quantity);
            }
        }
        if (Constants.mOrderPriceValidationType.matches("DOPS")) {
            Double amountTotal = 0.0, qtyTotal = 0.0;
            for (int ii = 0; ii < Constants.selectedProductMasterList
                    .size(); ii++) {
                amountTotal = Double.parseDouble(Constants.selectedProductMasterList.get(ii).getAmount()) + amountTotal;
                qtyTotal = Double.parseDouble(Constants.selectedProductMasterList.get(ii).getQty()) + qtyTotal;
            }
            totalOrderValue = "" + Constants.defaultFormat.format(amountTotal);
            totalOrderQty = "" + Constants.defaultFormat.format(qtyTotal);
            txtTotal.setText(Constants.currency + " " + Constants.defaultFormat.format(amountTotal));
            totalAmount = amountTotal + "";
        } else {
            totalOrderValue = "" + Constants.defaultFormat.format(amount);
            totalOrderQty = "" + Constants.defaultFormat.format(quantity);
            if(Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise")))
            {
                if(Utils.isNumeric(trdDisc))
                {

                    if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("order value wise#amount"))
                    {
                        amount=amount-Double.parseDouble(trdDisc);
                    }
                    else
                    {
                        amount=amount - ((Double.parseDouble(trdDisc)*amount)/100);
                    }
                }
            }
            txtTotal.setText(Constants.currency + " " + Constants.defaultFormat.format(amount));
            totalAmount = amount + "";
        }

        if (!Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") && !Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
            double qty = 0.0;
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails currentObj = Constants.selectedProductMasterList
                        .get(ii);
                qty = qty + Double.parseDouble(currentObj.getQty());
            }
            TextView textViewheading = (TextView) findViewById(R.id.textViewheading);
            textViewheading.setText("Total Volume :");
            txtTotal.setText(Constants.defaultFormat.format(qty));
        }
        return amount;
    }


    @Override
    public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3)
    {
        if (Constants.orderAuditType.equalsIgnoreCase("primary") && Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && Constants.retailercareOrderOrAudit.equalsIgnoreCase("audit"))
        {

        }
        else
        {
            ShowEditQuantityDialog(arg2);
        }

    }

    @Override
    public boolean onItemLongClick(AdapterView<?> arg0, View arg1, int arg2,
                                   long arg3) {
        showDeleteItemDialog(arg2);
        return true;
    }

    public void showDeleteItemDialog(final int pos) {
        System.out.println("POSITION::::::::::" + pos);
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                OrderConfirmationActivity.this);
        alertDialogBuilder.setMessage(
                "Are you sure you want to delete this item ?").setCancelable(
                false);
        alertDialogBuilder.setNegativeButton("OK",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();
                        ProductMasterDetails deletedProductObj = Constants.selectedProductMasterList.get(pos);

                        Constants.selectedProductMasterList.remove(deletedProductObj);
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
                        if (Constants.selectedProductMasterList.size() == 0) {
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
                OrderConfirmationActivity.this);
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
            Constants.selectedProductMasterList.get(i).setMrpCode(mrpSpinnerList.get(0).getMrpCode());
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

        Constants.selectedProductMasterList.get(i).setMrpValue(mrpValue);
        return mrpValue;
    }

    public void ShowEditQuantityDialog(final int position) {
        final DecimalFormat defaultFormat = new DecimalFormat("0.00");

        final boolean isTD = Constants.selectedProductMasterList.get(position)
                .getIsTradeDiscount();

        final Double previousQty = Double
                .parseDouble(Constants.selectedProductMasterList.get(position)
                        .getQty());
        final Dialog edQtyDialog = new Dialog(OrderConfirmationActivity.this);
        edQtyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        edQtyDialog.setContentView(R.layout.edit_order_dialog);
        TextView title = (TextView) edQtyDialog.findViewById(R.id.title);
        title.setText("Provide valid inputs");

        final LinearLayout mrpLayout = (LinearLayout) edQtyDialog
                .findViewById(R.id.mrp_layout);
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

        final EditText edQty = (EditText) edQtyDialog.findViewById(R.id.ed_qnt);
        if (Constants.orderAuditType.equalsIgnoreCase("primary") && Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && Constants.retailercareOrderOrAudit.equalsIgnoreCase("audit"))
        {
            edQty.setEnabled(false);
        }
        final EditText edMrp = (EditText) edQtyDialog
                .findViewById(R.id.ed_sale);
        final EditText edDisc = (EditText) edQtyDialog
                .findViewById(R.id.ed_discount);
        final EditText edAmt = (EditText) edQtyDialog
                .findViewById(R.id.ed_amount);

        if (Constants.selectedProductMasterList.get(position).isAmtEntered()) {
            edMrp.setEnabled(false);
            edAmt.setEnabled(true);
        } else {
            edMrp.setEnabled(true);
            edAmt.setEnabled(false);
        }

        edQty.setText(Constants.selectedProductMasterList.get(position).getQty());
        // edMrp.setText(Constants.selectedProductMasterList.get(position).getMrpValue());
        edMrp.setText(defaultFormat.format(Double
                .parseDouble(Constants.selectedProductMasterList.get(position)
                        .getMrpValue())));
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            edDisc.setText(Constants.selectedProductMasterList.get(position)
                    .getVat());
        } else {
            if (Constants.orderFormDetailsObj.getPremium().equalsIgnoreCase("yes")) {
                if (true == isTD) {
                    edDisc.setText(Constants.selectedProductMasterList.get(position)
                            .getTradeDiscnt());
                } else {
                    edDisc.setText(Constants.selectedProductMasterList.get(position)
                            .getPremium());
                }

            } else {
                edDisc.setText(Constants.selectedProductMasterList.get(position)
                        .getTradeDiscnt());
            }

        }

        if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase("yes")) {
            amountLayout.setVisibility(View.VISIBLE);
            edAmt.setText(defaultFormat.format(Double.parseDouble(Constants.selectedProductMasterList.get(position).getAmount())));
        } else {
            amountLayout.setVisibility(View.GONE);
        }

        edQty.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edMrp.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edDisc.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edAmt.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);

        if (!(Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase(
                "yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn()
                .equalsIgnoreCase("input"))) {
            mrpLayout.setVisibility(View.GONE);
        }
        String[] vatArray = Constants.orderFormDetailsObj.getVatType().split(
                ",");
        if (Constants.menuDetailsObj.getSaudaAllocation().matches("yes")) {
            discountLayout.setVisibility(View.GONE);
        } else if (!(Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")
                && Constants.orderFormDetailsObj.getVatCalcOn()
                .equalsIgnoreCase("sku") && (Arrays.asList(vatArray)
                .contains("SB") || Arrays.asList(vatArray).contains("SO")))
                && !(Constants.orderFormDetailsObj.getTradeDiscount()
                .equalsIgnoreCase("yes") &&(Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")))) {
            discountLayout.setVisibility(View.GONE);
        } else if (Constants.orderFormDetailsObj.getTradeDiscount()
                .equalsIgnoreCase("yes")
                && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")) && Constants.orderFormDetailsObj.getTdInputDropDown().equalsIgnoreCase("dropdown")) {
            discountLayout.setVisibility(View.GONE);
        } else if (Constants.orderFormDetailsObj.getInputScreenNormal().equalsIgnoreCase("yes")) {
            discountLayout.setVisibility(View.GONE);
        }
        final Spinner uomSpinner = (Spinner) edQtyDialog.findViewById(R.id.uomSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<String>();
        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
            String selectedUom = Constants.selectedProductMasterList.get(position).getUomSelectedForProduct();
            String Uom1 = Constants.selectedProductMasterList.get(position).getUom1();
            String Uom2 = Constants.selectedProductMasterList.get(position).getUom2();
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
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                String qty = "", mrp = "", discount = "", amount = "";
                boolean boolQty = true, boolMrp = true, boolDisc = true;

                qty = edQty.getText().toString();
                mrp = edMrp.getText().toString();
                discount = edDisc.getText().toString();

                if(Constants.isVanSales)
                {
                    if(Utils.isNumeric(qty) && Double.parseDouble(qty)>Double.parseDouble(Constants.selectedProductMasterList.get(position).getClosingStk()))
                    {
                        Utils.showToast(mContext,"Insufficient Allocation.");
                        return;
                    }

                }

                if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
                    int selectedItemPosition = uomSpinner.getSelectedItemPosition();
                    Constants.selectedProductMasterList.get(position).setUomSelectedForProduct(spinnerArray.get(selectedItemPosition));

                    if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes")) {
                        if (selectedItemPosition == 0) {
                            setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), position, Constants.selectedProductMasterList.get(position).getProdCode(), Constants.selectedProductMasterList.get(position).getUom1());
                        } else {
                            setMrpValue(Constants.selectedCustomer.getCustomerType(), Constants.productDetailsObj.getMultipleRate(), position, Constants.selectedProductMasterList.get(position).getProdCode(), Constants.selectedProductMasterList.get(position).getUom2());
                        }
                    } else if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                        String currentMrpValue = "";
                        if (selectedItemPosition == 0) {
                            currentMrpValue = mAceDnsDatabase.getMRPList(Constants.selectedProductMasterList.get(position).getProdCode(), "").get(0).getMrpValue();
                        } else {
                            currentMrpValue = mAceDnsDatabase.getMRPListForOrder(Constants.selectedProductMasterList.get(position).getProdCode(), "", Constants.selectedProductMasterList.get(position).getConversionFactor()).get(0).getMrpValue();
                        }
                        Constants.selectedProductMasterList.get(position).setMrpValue(currentMrpValue);
                    }

                }

                if (qty.length() > 0 && !qty.equalsIgnoreCase("0") && !qty.equalsIgnoreCase(".")) {
                    Constants.selectedProductMasterList.get(position).setQty(qty);
                    if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")) && Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes")) {
                        boolean qtyEligibleForTD = Utils.checkIfGivenQuantityEligibleForTD(mContext, qty, Constants.selectedProductMasterList.get(position).getProdCode(), Constants.selectedCustomer.getBranchCode(), Constants.selectedCustomer.getCustClass());
                        Constants.selectedProductMasterList.get(position).setQuantityEligibleForTD(qtyEligibleForTD);
                        Constants.selectedProductMasterList.get(position).setTDPercent(Constants.currentTdPercent);
                        Double productamount = Double.parseDouble(qty) * Double.parseDouble(Constants.selectedProductMasterList.get(position).getMrpValue());
                        if (qtyEligibleForTD) {

                            Double amnt = productamount - (productamount * (Double.parseDouble(Constants.currentTdPercent) / 100));
                            Constants.selectedProductMasterList.get(position).setAmount(defaultFormat.format(amnt));
                        } else {
                            Constants.selectedProductMasterList.get(position).setAmount(defaultFormat.format(productamount));
                        }
                    }
                } else {
                    boolQty = false;
                }

                if (Constants.selectedProductMasterList.get(position).isAmtEntered()) {
                    if (amountLayout.getVisibility() == View.VISIBLE) {
                        amount = edAmt.getText().toString();
                        if (amount.length() > 0
                                && !amount.equalsIgnoreCase("0")
                                && !amount.equalsIgnoreCase(".")) {
                            Constants.selectedProductMasterList.get(position)
                                    .setAmount(amount);
                            Double amountD = Double.parseDouble(amount);
                            Double qtyD = Double.parseDouble((boolQty ? edQty
                                    .getText().toString() : "1"));
                            if (qtyD > 0) {
                                Constants.selectedProductMasterList.get(
                                        position).setMrpValue(
                                        String.valueOf(amountD / qtyD));
                            }
                        }
                    }
                } else {
                    if (mrpLayout.getVisibility() == View.VISIBLE) {
                        if (mrp.length() > 0 && !mrp.equalsIgnoreCase("0")
                                && !mrp.equalsIgnoreCase(".")) {
                            Constants.selectedProductMasterList.get(position)
                                    .setMrpValue(mrp);
                            Double mrpD = Double.parseDouble(mrp);
                            Double qtyD = Double.parseDouble((boolQty ? edQty
                                    .getText().toString() : "1"));
                            if (qtyD > 0) {
                                Constants.selectedProductMasterList.get(
                                        position).setAmount(
                                        String.valueOf(mrpD * qtyD));
                            }
                        } else {
                            boolMrp = false;
                        }
                    }
                }
                if (discountLayout.getVisibility() == View.VISIBLE) {
                    if (Constants.orderFormDetailsObj.getVat()
                            .equalsIgnoreCase("no")) {
                        if (discount.length() > 0
                                && !discount.equalsIgnoreCase("0")
                                && !discount.equalsIgnoreCase(".")) {

                            if (Constants.orderFormDetailsObj.getPremium().equalsIgnoreCase("yes")) {
                                if (true == isTD) {
                                    if (Double.parseDouble(discount) <= 100) {
                                        Constants.selectedProductMasterList.get(
                                                position).setTradeDiscnt(discount);
                                    } else {
                                        boolDisc = false;
                                    }
                                } else {
                                    if (Double.parseDouble(discount) <= 100) {
                                        Constants.selectedProductMasterList.get(
                                                position).setPremium(discount);
                                    } else {
                                        boolDisc = false;
                                        Toast.makeText(OrderConfirmationActivity.this,
                                                "Premium cannot be more than 100 ",
                                                Toast.LENGTH_SHORT).show();
                                    }
                                }

                            } else {
                                if (Double.parseDouble(discount) <= 100) {
                                    Constants.selectedProductMasterList.get(
                                            position).setTradeDiscnt(discount);
                                } else {
                                    boolDisc = false;
                                }
                            }


                        } else {
                            Constants.selectedProductMasterList.get(position)
                                    .setTradeDiscnt("0");
                        }
                    } else {
                        if (discount.length() > 0
                                && !discount.equalsIgnoreCase("0")
                                && !discount.equalsIgnoreCase(".")) {
                            Constants.selectedProductMasterList.get(position)
                                    .setVat(discount);
                        } else {
                            Constants.selectedProductMasterList.get(position)
                                    .setVat("0");
                        }
                    }
                }

                if (boolQty == true && boolMrp == true && boolDisc == true) {
                    edQtyDialog.cancel();
                    CalculateTotal();
                    if (!(Constants.orderFormDetailsObj.getVat()
                            .equalsIgnoreCase("yes") && Constants.orderFormDetailsObj
                            .getVatCalcOn().equalsIgnoreCase("ordervalue"))) {
                        introduceVATInProduct();
                    }

                    Double currentQuantity = Double
                            .parseDouble(Constants.selectedProductMasterList
                                    .get(position).getQty());
                    Double diffInQty = currentQuantity - previousQty;
                    ProductMasterDetails masterObj = new ProductMasterDetails();
                    masterObj.setQty(String.valueOf(diffInQty));
                    masterObj.setProdCode(Constants.selectedProductMasterList
                            .get(position).getProdCode());
                    if (diffInQty > 0) {
                        if (carryInSales) {
                            dataHelperObj.reduceClStkProductWise(masterObj);
                        }
                    } else {
                        if (carryInSales) {
                            dataHelperObj.increaseClStkProductWise(masterObj);
                        }
                    }

                    adapter.notifyDataSetChanged();
                    Toast.makeText(OrderConfirmationActivity.this,
                            "Order has been edited for this product.", Toast.LENGTH_SHORT)
                            .show();
                } else {
                    Toast.makeText(OrderConfirmationActivity.this,
                            "Please provide a valid input", Toast.LENGTH_SHORT).show();
                }

                freebieUpdateProcess(Constants.selectedProductMasterList.get(position));
                removeAndAddFilter2Schemes();
            }
        });
        edQtyDialog.show();
    }

    private void freebieUpdateProcess(ProductMasterDetails productMasterDetails) {
        deleteFreeBiesForCurrentProduct(productMasterDetails);
        addFreeBiesForCurrentProduct(productMasterDetails);
    }

    public void deleteFreeBiesForCurrentProduct(ProductMasterDetails productMasterDetails) {
        if (productMasterDetails.getSchemePresent()) {
            final ArrayList<String> schemesOnCurrentProduct = new ArrayList<>(Arrays.asList(productMasterDetails.getSchemeIds().split(",")));
            for (int i = 0; i < Constants.selectedProductMasterListFreebies.size(); i++) {
                ProductMasterDetails FreeProductsOnCurrentItem = Constants.selectedProductMasterListFreebies.get(i);
                if (schemesOnCurrentProduct.contains(FreeProductsOnCurrentItem.getSchemeIds())) {
                    Constants.selectedProductMasterListFreebies.remove(i);
                    i--;
                }
            }
            Constants.freeBiesAdapter.notifyDataSetChanged();
        }
    }

    public void addFreeBiesForCurrentProduct(ProductMasterDetails productMasterDetails) {
        String qty = productMasterDetails.getQty();
        String rate = productMasterDetails.getMrpValue();
        double amnt = Double.parseDouble(qty) * Double.parseDouble(rate);
        if (productMasterDetails.getSchemePresent()) {
            final ArrayList<String> schemesOnCurrentProduct = new ArrayList<>(Arrays.asList(productMasterDetails.getSchemeIds().split(",")));
            for (int i = 0; i < schemesOnCurrentProduct.size(); i++) {
                ArrayList<SchemeFreebiesDetails> FreeProductsOnCurrentItem = Constants.schemeListForCurrentProducts.get(schemesOnCurrentProduct.get(i));
                for (int x = 0; x < FreeProductsOnCurrentItem.size(); x++) {
                    SchemeFreebiesDetails item = FreeProductsOnCurrentItem.get(x);
                    if (!item.getqty().matches("0"))//offer on qty
                    {
                        if (Utils.doesQuantityMatchesQtySlab(qty, item.getqty())) {
                            if (!item.getfreebiesProdCode().trim().matches(""))//free product
                            {
                                Utils.addQtyProductToList(item, Double.parseDouble(qty), amnt, mContext);
                            } else//offer on total amount
                            {
                                amnt = Utils.getAmnt(amnt, item);
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
                                amnt = Utils.getAmnt(amnt, item);
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
            Constants.freeBiesAdapter.notifyDataSetChanged();
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

        int sizeOfTotalItemsInCart = Constants.selectedProductMasterList.size();

        Double vatRate = 4.00;
        Double AdditionalVatRate = 1.00;
        Double AdditionalVat = 0.00;
        grandTotal = 0;

        for (int i = 0; i < sizeOfTotalItemsInCart; i++) {
            int Sl = i + 1;
            String currentProductDescription = Constants.selectedProductMasterList.get(i).getDesc();
            vatRate = Double.parseDouble(Constants.selectedProductMasterList.get(i).getVatRate());

            if (shouldPrintFreightBill) {
                freightCost = freightCost + Double.parseDouble(Constants.selectedProductMasterList.get(i).getFreightCost());
            }


            AdditionalVatRate = Double.parseDouble(Constants.selectedProductMasterList.get(i).getAdditionalVatRate());
            Double currentQuantity = Double.parseDouble(Constants.selectedProductMasterList.get(i).getQty());
            Double currentRate = Double.parseDouble(Constants.selectedProductMasterList.get(i).getMrpValue().length() > 0 ? Constants.selectedProductMasterList.get(i).getMrpValue() : Constants.selectedProductMasterList.get(i).getAmount());
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
        final Dialog printDialog = new Dialog(OrderConfirmationActivity.this);
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
        no.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                printDialog.cancel();
                startActivity(new Intent(OrderConfirmationActivity.this,
                        MenuActivity.class));
                finish();
            }
        });
        printDialog.show();
    }

    public void showFreightBillPrintingOptionDialog() {
        final Dialog printDialog = new Dialog(OrderConfirmationActivity.this);
        printDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        printDialog.setContentView(R.layout.printing_dialog);
        printDialog.setCancelable(false);
        TextView title = (TextView) printDialog.findViewById(R.id.title);
        title.setText("Would you like to generate a freight bill for this Order?");
        Button yes = (Button) printDialog.findViewById(R.id.btn_yes);
        Button no = (Button) printDialog.findViewById(R.id.btn_no);
        yes.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

                printDialog.cancel();
                shouldPrintFreightBill = true;
                makeDataSavingAndPrintingProcess();


            }
        });
        no.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                printDialog.cancel();
                shouldPrintFreightBill = false;
                makeDataSavingAndPrintingProcess();
            }
        });
        printDialog.show();
    }

    public void saveOrderDataToDatabase()
    {
        isCameralaunched=false;
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {


                if (!Constants.isSelectCustomer) {
                    dataHelperObj.insertToCustomerMaster(Constants.selectedCustomer,false);
                    if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("D")) {
                        RDSDetails rdsObj = new RDSDetails();
                        rdsObj.setRdsCode(Constants.selectedCustomer.getCustomerCode());
                        rdsObj.setRdsName(Constants.selectedCustomer.getCustomerName());
                        rdsObj.setRdsType("D");
                        ArrayList<RDSDetails> rdsList = new ArrayList<RDSDetails>();
                        rdsList.add(rdsObj);
                        AceDnsDatabase dbObj = new AceDnsDatabase(mContext);
                        dbObj.insertToRDSMaster(rdsList);
                    }
                }
                // There can be existing customer with routeCode starting N but
                // they have newRouteCode blank.
                if ((!Constants.isSelectCustomer)
                        && (Constants.selectedCustomer.getNewRouteCode().startsWith("N"))) {
                    RouteDetails routeObj = new RouteDetails();
                    routeObj.setRouteCode(Constants.selectedCustomer
                            .getNewRouteCode());
                    routeObj.setRouteName(Constants.selectedCustomer
                            .getNewRouteName());
                    ArrayList<RouteDetails> routeList = new ArrayList<RouteDetails>();
                    routeList.add(routeObj);
                    AceDnsDatabase dbObj = new AceDnsDatabase(mContext);
                    dbObj.insertToRouteMaster(routeList);
                }
                String timeStamp = "";
                if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
                    if (remarksDate.length() != 0) {
                        timeStamp = Constants.dateString
                                + remarksDate
                                + new SimpleDateFormat("HHmmss")
                                .format(Calendar.getInstance()
                                        .getTime());
                    } else {
                        timeStamp = Constants.dateString
                                + Constants.dateString
                                + new SimpleDateFormat("HHmmss")
                                .format(Calendar.getInstance()
                                        .getTime());
                    }
                } else {
                    timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                }

                /*timeStamp="20161126145721";*/

                boolean isTransactionIDExist = false;
                do {
                    isTransactionIDExist = dataHelperObj.IsTransactioIdExist(timeStamp);
                    if (true == isTransactionIDExist) {
                        timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    }
                }
                while (true == isTransactionIDExist);

                //storing bank name and cheque number for
                if (saleType.matches("CHEQUE")) {
                    remarks = remarks + ";" + chequeNo + ";" + bankName;
                }
                String orderHeaderPrefix="O";
                Boolean isSuccessReduceClStk = true, isSuccessInsertToOrderHeaderTable = true, isSuccessInsertToTransactionLogTable = true,
                        isSuccessInsertToOrderDetailsTable = true, isSuccessInsertToInvoiceInformationTable = true,
                        isSuccessInsertToHintRemarksDetailsTable = true, isSuccessInsertToLocationTable = true,isSuccessUpdateIsNewCustomer=true, IsSuccessInsertToPaymentHeaderTableFromOrder = true,
                        isSuccessInsertToPaymentDetailsTableFromOrder = true, isSuccessInsertToLocationTableForPayment = true, isSuccessInserttoCallDuration = true, isSuccessInserttoOrderSummaryTable = true, isSuccessInserttoSchemeSummaryTable = true, isSuccessStockAuditWithOrderSubmitProcess = true, isSuccessVanStockReturnProcess=true;
                dataHelperObj.beginTransaction();
                if (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes"))
                {
                    if (Constants.orderAuditType.equalsIgnoreCase("primary") && Constants.retailercareOrderOrAudit.equalsIgnoreCase("order"))
                    {

                    }
                    else
                    {
                        isSuccessStockAuditWithOrderSubmitProcess = submitStockAudit(timeStamp);
                    }

                }
                if (Constants.isVanSales)
                {
                    isSuccessVanStockReturnProcess = submitStockReturn(timeStamp,orderHeaderPrefix);
                }

                removeEmptyQtyDataForOrder();
                if(Constants.selectedProductMasterList.size()>0)
                {
                    if (carryInSales)
                    {
                        isSuccessReduceClStk = dataHelperObj.reduceClStk1();

                        if(Utils.isNumeric(totalOrderQty) && Double.parseDouble(totalOrderQty)>0)
                        {
                            orderHeaderPrefix="O";
                            isSuccessInsertToOrderHeaderTable = dataHelperObj.insertToOrderHeaderTable1(orderHeaderPrefix, remarks, timeStamp, saleType, trdDisc, totalOrderValue, Constants.taggedCustomerCode, "SB", vat, "", "", "");
                        }
                        else
                        {
                            orderHeaderPrefix="NO";
                            isSuccessInsertToOrderHeaderTable = dataHelperObj.insertToOrderHeaderTable1(orderHeaderPrefix, remarks, timeStamp, "", "", "", "", "NO", "", "", "", "");
                        }

                        isSuccessInsertToTransactionLogTable = dataHelperObj.insertToTransactionLogTable1(timeStamp, "SB", remarks);
                    }
                    else
                    {

                        if(Utils.isNumeric(totalOrderQty) && Double.parseDouble(totalOrderQty)>0)
                        {
                            orderHeaderPrefix="O";
                            isSuccessInsertToOrderHeaderTable = dataHelperObj.insertToOrderHeaderTable1("O", remarks,
                                    timeStamp, saleType, trdDisc, totalOrderValue,
                                    Constants.taggedCustomerCode, Constants.CurrentOrderCollectionTransactionType, vat, "", "", "");
                            isSuccessInsertToTransactionLogTable = dataHelperObj.insertToTransactionLogTable1(timeStamp, Constants.CurrentOrderCollectionTransactionType,
                                    remarks);
                        }
                        else
                        {
                            orderHeaderPrefix="NO";
                            isSuccessInsertToOrderHeaderTable = dataHelperObj.insertToOrderHeaderTable1("NO", remarks, timeStamp, "", "", "", "", "NO", "", "", "", "");
                        }

                        Constants.mDestinationCode = "";
                        Constants.mOrderType = "";
                        Constants.mFreightComponent = "";
                    }
                    isSuccessUpdateIsNewCustomer = dataHelperObj.updateICustomerNew();
                    isSuccessInsertToOrderDetailsTable = dataHelperObj.insertToOrderDetailsTable1(timeStamp);
                    if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes"))
                    {
                        isSuccessInserttoOrderSummaryTable = dataHelperObj.insertToOrderSummaryTable(timeStamp);
                        if (Constants.selectedProductMasterListSchemeOffers != null && !Constants.selectedProductMasterListSchemeOffers.isEmpty())
                        {
                            isSuccessInserttoSchemeSummaryTable = dataHelperObj.insertToSchemeSummaryTable(timeStamp, Constants.selectedProductMasterListSchemeOffers);
                        }
                    }

                    if (Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan"))
                    {
                        if (shouldPrintFreightBill)
                        {
                            isSuccessInsertToInvoiceInformationTable = dataHelperObj.insertToInvoiceInformationTable(timeStamp, generateNewInvoiceNumber(), currentDateandTime, freightCost + "");
                        }
                        else
                        {
                            isSuccessInsertToInvoiceInformationTable = dataHelperObj.insertToInvoiceInformationTable(timeStamp, generateNewInvoiceNumber(), currentDateandTime, null);
                        }
                    }


                    if (selectedHintRemarksId != -1)
                    {
                        isSuccessInsertToHintRemarksDetailsTable = dataHelperObj.insertToHintRemarksDetailsTable1("O", timeStamp, hintRemarksValList.get(selectedHintRemarksId - 1));
                    }
                    Constants.CurrentOrderTransactionId = "O" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                    if (Constants.orderAuditType.equalsIgnoreCase("primary") && Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes") && Constants.retailercareOrderOrAudit.equalsIgnoreCase("audit"))
                    {

                    }
                    else
                    {
                        isSuccessInsertToLocationTable = dataHelperObj.insertToLocationTable1(orderHeaderPrefix, timeStamp);
                        isSuccessInserttoCallDuration = dataHelperObj.inserttoCallDurationTable(orderHeaderPrefix, timeStamp);
                    }

                    if (saleType.equalsIgnoreCase("CASH"))
                    {
                        IsSuccessInsertToPaymentHeaderTableFromOrder = dataHelperObj.insertToPaymentHeaderTableFromOrder1(remarks, timeStamp, trdDisc);
                        isSuccessInsertToPaymentDetailsTableFromOrder = dataHelperObj.insertToPaymentDetailsTableFromOrder1(timeStamp, trdDisc);
                        isSuccessInsertToLocationTableForPayment = dataHelperObj.insertToLocationTable1("P", timeStamp);
                    }
                }

                if (isSuccessReduceClStk && isSuccessInsertToOrderHeaderTable && isSuccessInsertToTransactionLogTable && isSuccessInsertToOrderDetailsTable && isSuccessUpdateIsNewCustomer  && isSuccessInsertToInvoiceInformationTable && isSuccessInsertToHintRemarksDetailsTable &&
                        isSuccessInsertToLocationTable && isSuccessInserttoCallDuration && IsSuccessInsertToPaymentHeaderTableFromOrder && isSuccessInsertToPaymentDetailsTableFromOrder && isSuccessInsertToLocationTableForPayment && isSuccessInserttoOrderSummaryTable && isSuccessInserttoSchemeSummaryTable && isSuccessInserttoSchemeSummaryTable && isSuccessStockAuditWithOrderSubmitProcess && isSuccessVanStockReturnProcess)
                {
                    Constants.mChosenGstType = "";//clearing chosen gst type
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                    if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes"))
                    {
                        dataHelperObj = new AceDnsTransactionDatabase(mContext);
                        String transactionAlias = "CC";
                        String transId = transactionAlias + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                        dataHelperObj.insertToLocationTable(transactionAlias, timeStamp);
                        dataHelperObj.insertToCashDepositReceiveDetails(transId, transactionAlias, Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", Constants.dateString), Constants.totalAmountCollection + "", remarks);
                        new TRANS_CashDepositeReceiveTask(mContext, false).execute();
                    }
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
        Iterator<ProductMasterDetails> iter = Constants.selectedProductMasterList.iterator();
        while (iter.hasNext())
        {
            String qty = iter.next().getQty();
            if (Double.parseDouble(qty) <= 0)
            {
                iter.remove();
            }
        }
    }

    private Boolean submitStockAudit(String timeStamp) {
        boolean insertionSuccessful = true;
        boolean locationInsertionSuccess = dataHelperObj.insertToLocationTable1("S", timeStamp);
        boolean stockTableInsertionSuccess = dataHelperObj.insertToStockAuditTableFromOrder(timeStamp, remarks);
        if (!locationInsertionSuccess || !stockTableInsertionSuccess) {
            insertionSuccessful = false;
        }

        return insertionSuccessful;
    }
    private Boolean submitStockReturn(String timeStamp,String orderHeaderPrefix) {
        boolean insertionSuccessful = true;
        boolean locationInsertionSuccess = dataHelperObj.insertToLocationTable1("SR", timeStamp);
        boolean stockTableInsertionSuccess = dataHelperObj.insertToStockReturn(timeStamp, remarks,orderHeaderPrefix);
        if (!locationInsertionSuccess || !stockTableInsertionSuccess) {
            insertionSuccessful = false;
        }

        return insertionSuccessful;
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
