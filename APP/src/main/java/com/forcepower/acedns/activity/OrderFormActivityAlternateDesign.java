package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.CatalogueVerticalSelectionAdapter;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapterAlternateDesign;
import com.forcepower.acedns.adapter.ProductSpinnerAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.api.clients.LocalStorage;
import com.forcepower.acedns.backgroundTask.TRANS_PendingRoutePlanBeforeOtherTxn;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.SchemeFreebiesDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.HashMap;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;
import static com.forcepower.acedns.constants.Constants.SchemeCheckedBeforeSubmittingOrder;
import static com.forcepower.acedns.constants.Constants.carttv;
import static com.forcepower.acedns.constants.Constants.currency;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.mOrderPriceValidationType;
import static com.forcepower.acedns.constants.Constants.mVerticalValue;
import static com.forcepower.acedns.constants.Constants.mVerticalValueList;
import static com.forcepower.acedns.constants.Constants.orderFormDetailsObj;
import static com.forcepower.acedns.constants.Constants.orderFormDetailsObjFixed;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.schemeListForCurrentProducts;
import static com.forcepower.acedns.constants.Constants.schemeListForProductGroups;
import static com.forcepower.acedns.constants.Constants.schemeOnTotalOrder;
import static com.forcepower.acedns.constants.Constants.selectedProductMasterListFreebies;
import static com.forcepower.acedns.constants.Constants.selectedProductMasterListSchemeOffers;
import static com.forcepower.acedns.constants.Constants.tempProductList;

public class OrderFormActivityAlternateDesign extends AceDnsParentActivity {

    public static ImageView mImageViewHeaderLogo = null;

    public static TextView mTextViewTotalOrderValue = null;
    public static TextView mTextViewNoofOrder = null;
    public static TextView mTextViewUOM1 = null;

    public static TextView etWeightage = null;

    TextView etProdRate ;
    public static TextView mTextViewCustomerName = null;
    public static TextView mTextViewRouteName = null;
    public static TextView stkQty = null;
    public static TextView list_details = null;
    public static TextView uomTv = null;

    public static Button mButtonBack = null;
    public static Button mButtonNoOrder = null;
    public static Button mButtonAddtoCart = null;
    public static Button mButtonCheckOut = null;
    public static Button schemeAvailableBtn = null;

    public boolean isTD = true;
    public boolean isPremium = false;
    String sp = "";

    public String uom1 = "", uom2 = "", selecteduom = "";
    LocalStorage localStorage;

    EditText edAmount;
    LinearLayout bodyLayout, optionLayout, customerLayout,
            filterLayout;
    /*
     * Custom Views
     */
    Spinner mrpSpinner;
    EditText edQty, edSaleRate, edClsngStk, edTd, refPrice;
    Dialog customerListDialog, grpDialog, subGrpDialog,
            brandDialog, masterDialog, mrpDialog, routeDialog,
            infoDialog, routePlanListDialog, uomDialog;

    RadioButton radioButton;

    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    Activity activity;
    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;

    int chosenCustPos;
    int mrpSelected;
    ArrayList<MRPDetails> mrpSpinnerList;
    ArrayList<String> filterList;
    ArrayList<CustomerDetails> customerList;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;
    ArrayList<ProductMasterDetails> productMasterListSpecial;
    ProductMasterDetails currentProductMasterObj;
    ArrayList<RouteDetails> routeList;

    ArrayList<Button> filterButtonList;
    ProductMasterDetails currentProductMasterObjSpecial;
    int filterNo;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected,
            lastProductSelected = false;
    ProductMasterAdapter prodAdapter;
    ProductMasterWithQtyInputAdapter ProductMasterWithQtyInputAdapterObject;
    ProductMasterWithQtyInputAdapterAlternateDesign ProductMasterWithQtyInputAdapterObjectAlternateDesignObject;
    ProductGrpAdapter groupAdapter;
    ProductSubGrpAdapter subGroupAdapter;
    ProductBrandAdapter brandAdapter;
    CustomerAdapter adapterCust;
    String lastStr = "";
    ArrayList<CustomerDetails> tempCustomerList;
    ArrayList<ProductGroupDetails> tempProductGroupList;
    ArrayList<ProductSubGrpDetails> tempProductSubGroupList;
    ArrayList<ProductBrandDetails> tempProductBrandList;
    SimpleDateFormat formatter;
    String routeCode = "", routeName = "", customerType = "";
    int lastProdPos = 0;
    ArrayAdapter spinnerAdapter;
    String trdDisc = "0", vat = "0";
    boolean carryInSales = false;
    ProgressDialog ploader;
    Handler orderDataHandler;
    Handler productDataHandler;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    RouteDetails selectedRoute;
    boolean rpeatedProductEntry = false;
    String[] values;
    Boolean isNavigatedFromSalesOption = false;
    EditText etPwd,etPwdH;
    Spinner prodSubGrpSpinner,prodSpinner;
    LinearLayout prod_brand_layout;

    public static void changeCartCount() {
        if (carttv != null && Constants.selectedProductMasterList != null) {
            carttv.setText(Constants.selectedProductMasterList.size() + "");
        }
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_form_alternate_design);
        SchemeCheckedBeforeSubmittingOrder = true;

        //getStateWiseWeitage();

        //localStorage = new LocalStorage(OrderFormActivityAlternateDesign.this);
        //Utils.showToast(OrderFormActivityAlternateDesign.this, Constants.selectedCustomer.getCustomerType() +"/"+Constants.orderTypePrimarySeconder);
        if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")) {
            if (HttpCalling.isConnectionPossible(OrderFormActivityAlternateDesign.this)){

                //JsonsReceiver.getCustomerProductStock(OrderFormActivityAlternateDesign.this);
            }else{
                Utils.showToast(OrderFormActivityAlternateDesign.this,"You need an active internet connection to use this feature.");
                //finish();
            }
        }
        carttv = (TextView) findViewById(R.id.carttv);
        RegisterActivities.registerActivity(this);
        Constants.nameValuesProductList = new ArrayList<>();
        carryInSales = getIntent().getBooleanExtra("CARRY_IN", false);
        if (getIntent().hasExtra("salesOption")) {
            isNavigatedFromSalesOption = true;
        }
        Constants.isFromConfirmationActivity = false;
        qtySrate = null;
        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());

        mContext = OrderFormActivityAlternateDesign.this;
        activity = (Activity) mContext;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Constants.selectedProductMasterList = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();

        schemeListForCurrentProducts = new HashMap<>();
        schemeOnTotalOrder = new SchemeFreebiesDetails();
        selectedProductMasterListFreebies = new ArrayList<>();
        selectedProductMasterListSchemeOffers = new ArrayList<>();
        prodQtyRateListView = (ListView) findViewById(R.id.prodQtyRateListView);
        filterButtonList = new ArrayList<>();
        InitializeView();




        if (Constants.orderFormDetailsObj.getInputScreenPlanwise().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getInputScreenPriceValidation().equalsIgnoreCase("yes")) {
            //choose price validation type
            ShowPriceTypeSelectionDialog();
        } else if (Constants.orderFormDetailsObj.getInputScreenPlanwise().equalsIgnoreCase("yes")) {
            mOrderPriceValidationType = "DOPS";
            drawlayoutandothercommonprocess();
        } else if (Constants.orderFormDetailsObj.getInputScreenPriceValidation().equalsIgnoreCase("yes")) {
            OverwriteOrderFormSetupFosSpaPriceValidation();
            drawlayoutandothercommonprocess();
        } else//if both no
        {
            mOrderPriceValidationType = "";
            drawlayoutandothercommonprocess();
        }
        if (Constants.menuDetailsObj.getscheme().equalsIgnoreCase("yes")) {
            mAceDnsDatabase.getSchemeListForFilter2();
            if (schemeListForProductGroups.size() > 0) {
                schemeAvailableBtn = (Button) findViewById(R.id.schemeAvailableBtn);
                schemeAvailableBtn.setVisibility(VISIBLE);
            }
        }
        productDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("get products");
                OrderFormActivityAlternateDesign.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:

                                prodQtyRateListView.setEmptyView(findViewById(R.id.empty_text_view));
                                prodQtyRateListView.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
                                break;

                        }
                    }
                });
            }
        };
        if(Constants.orderFormDetailsObj.getmrp_sale_rate_text()!=null && !Constants.orderFormDetailsObj.getmrp_sale_rate_text().matches("null") &&  !Constants.orderFormDetailsObj.getmrp_sale_rate_text().equalsIgnoreCase("") && !Constants.orderFormDetailsObj.getmrp_sale_rate_text().equalsIgnoreCase("no"))
        {
            etProdRate.setText(Constants.orderFormDetailsObj.getmrp_sale_rate_text());
        }
        etWeightage = findViewById(R.id.etWeightage);
        if(Constants.weightage.matches("no")){
            etWeightage.setVisibility(GONE);
        }
        if(Constants.weightage.matches("yes")){
            etWeightage.setVisibility(VISIBLE);
            //getCategoryData();
            //getStateWiseWeitage();
        }

        //etPwd
    }

    //if price validation is SPA, then sale rate will always be yes and input no matter what the server sends
    private void OverwriteOrderFormSetupFosSpaPriceValidation() {
        mOrderPriceValidationType = "SPA";
        Constants.orderFormDetailsObj.setMrp("no");
        Constants.orderFormDetailsObj.setSaleRate("yes");
        Constants.orderFormDetailsObj.setSaleRateDrpdwn("input");
        Constants.orderFormDetailsObj.setTradeDiscount("no");
        Constants.orderFormDetailsObj.setPremium("no");
    }

    private void drawlayoutandothercommonprocess() {
        if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
            LinearLayout Verticallayout = (LinearLayout) findViewById(R.id.Verticallayout);
            Verticallayout.setVisibility(VISIBLE);
            final Spinner verticalSpinner = (Spinner) findViewById(R.id.verticalSpinner);
            mAceDnsDatabase.GetVerticalValue();
            final ArrayList<String> verticalArray = new ArrayList<>(Arrays.asList(mVerticalValueList));
            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, verticalArray);
            spinnerArrayAdapter.setDropDownViewResource(R.layout.custom_spinner_dropdown_light);
            verticalSpinner.setAdapter(spinnerArrayAdapter);
            int verticalIndexOfCurrentSelectedVertical = 0;
            for (int i = 0; i < verticalArray.size(); i++) {
                if (verticalArray.get(i).equalsIgnoreCase(mVerticalValue)) {
                    verticalIndexOfCurrentSelectedVertical = i;
                    break;
                }
            }
            verticalSpinner.setSelection(verticalIndexOfCurrentSelectedVertical);


            verticalSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                    mVerticalValue = verticalArray.get(i);
                    schemeListForCurrentProducts = new HashMap<>();
                    DrawLayout();
                }

                @Override
                public void onNothingSelected(AdapterView<?> adapterView) {

                }
            });

        } else {
            DrawLayout();
        }


        formatter = new SimpleDateFormat("dd-MM-yyyy");


        orderDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                OrderFormActivityAlternateDesign.this.runOnUiThread(new Runnable() {
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
                            case 5:
                                if (values.length > 0) {
                                    if (values.length > 1) {
                                        ShowVericalValueList();
                                    } else {
                                        mVerticalValue = values[0];
                                        if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")) {
                                            ShowSaudaDepoNameDialog();
                                        }
                                    }
                                }
                                break;
                        }
                    }
                });
            }
        };
    }

    public void ShowPriceTypeSelectionDialog()//SPA or DOPS
    {
        final Dialog payTypeDialog = new Dialog(mContext, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.uom_dialog);
        TextView txtMsg = (TextView) payTypeDialog.findViewById(R.id.title);
        txtMsg.setText("Please select a Type");

        RadioButton uom1RadioButton = (RadioButton) payTypeDialog.findViewById(R.id.radio0);
        uom1RadioButton.setText("SPA");

        RadioButton uom2RadioButton = (RadioButton) payTypeDialog.findViewById(R.id.radio1);
        uom2RadioButton.setText("DOPS");

        RadioGroup payTypeOption = (RadioGroup) payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                int radioButtonID = group.getCheckedRadioButtonId();
                View radioButton = group.findViewById(radioButtonID);
                int selectedRadio = group.indexOfChild(radioButton);

                if (selectedRadio == 0) {
                    OverwriteOrderFormSetupFosSpaPriceValidation();
                } else {
                    mOrderPriceValidationType = "DOPS";
                }
                payTypeDialog.cancel();
                drawlayoutandothercommonprocess();

            }
        });
        Button cancel = (Button) payTypeDialog.findViewById(R.id.btn_cancel);
        cancel.setVisibility(GONE);
        payTypeDialog.show();
    }

    /*
     * ::::::::::::::::::::::::::::::::::: VIEW RELATED OPERATIONS
     * ::::::::::::::::::::::::::::::::::::::::::::::
     */

    @Override
    public void onResume() {
        super.onResume();

        if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
            rpeatedProductEntry = true;
        } else {
            rpeatedProductEntry = false;
        }

        if (Constants.selectedProductMasterList == null || Constants.selectedProductMasterList.size() == 0) {
            customerLayout.setVisibility(View.INVISIBLE);
        } else {
//			customerLayout.setVisibility(View.VISIBLE);
            if (Constants.selectedProductMasterList.size() % 2 == 0) {
                customerLayout.setBackgroundColor(Color.YELLOW);
            } else {
                customerLayout.setBackgroundColor(Color.GREEN);
            }
            mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());

            CalculateTotal();
        }

        if (edTd != null && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("customer wise")) {
            edTd.setText("");
        }
        if (edSaleRate != null) {
            edSaleRate.setText("");
        }

        if (mrpSpinner != null) {
            String[] tempSpinnerArray = new String[0];
            spinnerAdapter = new ArrayAdapter(OrderFormActivityAlternateDesign.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
            mrpSpinner.setAdapter(spinnerAdapter);
        }
        if (mImageViewHeaderLogo != null) {
            if (Constants.logoBmp != null) {
                mImageViewHeaderLogo.setVisibility(VISIBLE);
                mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
            } else {
                mImageViewHeaderLogo.setVisibility(GONE);
            }
        }

        if (Constants.isFromConfirmationActivity) {
            if (filterButtonList != null && filterButtonList.size() > 3) {
                switch (filterNo) {
                    case 1:
                        filterButtonList.get(3).setEnabled(true);
                        filterButtonList.get(3).setText(filterList.get(4));
                        break;
                    case 2:
                        filterButtonList.get(0).setEnabled(true);
                        filterButtonList.get(0).setText(filterList.get(1));
                        filterButtonList.get(3).setEnabled(false);
                        filterButtonList.get(3).setText(filterList.get(4));
                        break;
                    case 3:
                        filterButtonList.get(0).setEnabled(true);
                        filterButtonList.get(0).setText(filterList.get(1));
                        filterButtonList.get(1).setEnabled(false);
                        filterButtonList.get(1).setText(filterList.get(2));
                        filterButtonList.get(3).setEnabled(false);
                        filterButtonList.get(3).setText(filterList.get(4));
                        break;
                    case 4:
                        filterButtonList.get(0).setEnabled(true);
                        filterButtonList.get(0).setText(filterList.get(1));
                        filterButtonList.get(1).setEnabled(false);
                        filterButtonList.get(1).setText(filterList.get(2));
                        filterButtonList.get(2).setEnabled(false);
                        filterButtonList.get(2).setText(filterList.get(3));
                        filterButtonList.get(3).setEnabled(false);
                        filterButtonList.get(3).setText(filterList.get(4));
                        break;
                }
            }

        }
    }

    public void InitializeView() {
        etProdRate =  findViewById(R.id.etProdRate);
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        bodyLayout = (LinearLayout) findViewById(R.id.body_layout);
        optionLayout = (LinearLayout) findViewById(R.id.option_layout);
        customerLayout = (LinearLayout) findViewById(R.id.customer_layout);
        if (Constants.selectedProductMasterList == null || Constants.selectedProductMasterList.size() == 0) {
            customerLayout.setVisibility(View.INVISIBLE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText("Ver~" + Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        filterLayout = (LinearLayout) findViewById(R.id.filter_layout);


        mTextViewTotalOrderValue = (TextView) findViewById(R.id.txt_total);
        mTextViewNoofOrder = (TextView) findViewById(R.id.txt_order_count);
        mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
        CalculateTotal();

        mButtonAddtoCart = (Button) findViewById(R.id.btn_continue);
        mButtonAddtoCart.setOnClickListener(OrderFormActivityAlternateDesign.this);
        mButtonAddtoCart.setTag(101);


        mButtonCheckOut = (Button) findViewById(R.id.btn_order_form);
        mButtonCheckOut.setTag(102);
        mButtonCheckOut.setOnClickListener(OrderFormActivityAlternateDesign.this);

        mButtonNoOrder = (Button) findViewById(R.id.no_ordr);
        mButtonNoOrder.setTag(104);
        mButtonNoOrder.setOnClickListener(OrderFormActivityAlternateDesign.this);
        mButtonNoOrder.setEnabled(true);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setTag(105);
        mButtonBack.setOnClickListener(OrderFormActivityAlternateDesign.this);

        mTextViewCustomerName  = findViewById(R.id.textViewCustomername);
        uomTv = findViewById(R.id.uomTv);
         if(Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("no_uom")){
             uomTv.setVisibility(GONE);
        }
         if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")){
             uomTv.setVisibility(VISIBLE);
             uomTv.setText("Stock");
         }
        mTextViewRouteName = (TextView) findViewById(R.id.textViewRoute);
        stkQty = (TextView) findViewById(R.id.stkQty);
        if (Constants.menuDetailsObj.getretailer_care().equalsIgnoreCase("yes")  || Constants.isVanSales) {
            stkQty.setVisibility(VISIBLE);

        }
        if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            list_details = (TextView) findViewById(R.id.list_details);
            list_details.setText("Size");
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) list_details.getLayoutParams();
            lay.weight = 0.3f;
            list_details.setLayoutParams(lay);
        }
        if (Constants.nickName.equalsIgnoreCase("CORAL")) {
            list_details = (TextView) findViewById(R.id.list_details);
            list_details.setText("Colour");
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) list_details.getLayoutParams();
            lay.weight = 0.3f;
            list_details.setLayoutParams(lay);
        }
        if (!Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
            if (!Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes")
                    || !Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes") || !Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan")) {
                String customerName = Constants.selectedCustomer.getCustomerName();
                mTextViewCustomerName.setText(customerName);

                mTextViewRouteName.setText("Route : " + Constants.selectedCustomer.getRouteName());
            }
        }
        prod_brand_layout = (LinearLayout) findViewById(R.id.prod_brand_layout);
        etPwd = findViewById(R.id.etPwd);
        etPwdH = findViewById(R.id.etPwdH);
        if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes"))
        {
            etPwdH.setVisibility(VISIBLE);
            etPwd.setVisibility(VISIBLE);



            etPwdH.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                }

                @Override
                public void onTextChanged(CharSequence s, int start, int before, int count) {
                    Constants.sylHight = s.toString();
                }

                @Override
                public void afterTextChanged(Editable s) {

                }
            });

            etPwd.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int start, int count, int after) {

                }

                @Override
                public void onTextChanged(CharSequence s, int start, int before, int count) {
                    Constants.sylWeidth = s.toString();
                }

                @Override
                public void afterTextChanged(Editable s) {

                }
            });


        }else{
            etPwdH.setVisibility(GONE);
            etPwd.setVisibility(GONE);
        }

    }

    public void DrawLayout() {
//		removeRepeatedGroupItems();
        if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) >= 2)
        {
            productGroupList = mAceDnsDatabase.getProductGroupList(carryInSales);
            showProductGroupSpinner();
        }
        else
        {
            if (orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes"))
            {
                showSkuSpinner();
            }
            else
            {

                getProductsShowOnList();
            }
        }

        drawFilterLayout();
        enableDisableLayout(false);
        if (mOrderPriceValidationType.matches("DOPS")) {
            mButtonAddtoCart.setVisibility(GONE);
        }

    }

    public void showProductGroupSpinner() {
        final Spinner prodGrpSpinner = (Spinner) findViewById(R.id.prodGrpSpinner);
        prodGrpSpinner.setVisibility(VISIBLE);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < productGroupList.size(); i++) {
            spinnerArray.add(productGroupList.get(i).getGroupName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(R.layout.custom_spinner_dropdown_light);
        prodGrpSpinner.setAdapter(spinnerArrayAdapter);
        prodGrpSpinner.setSelection(0);

        prodGrpSpinner.setOnItemSelectedListener(new OnItemSelectedListener()
        {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                selectedGrp = productGroupList.get(i);
                if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) == 2)
                {
                    if (orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes"))
                    {
                        showSkuSpinner();
                    }
                    else
                    {
                        getProductsShowOnList();
                    }
                }
                else if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) >= 3)
                {
                    showProductSubGroupSpinner();
                }

                if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes"))
                {
                    sp = prodGrpSpinner.getSelectedItem().toString();
                    if (sp.equalsIgnoreCase("flush door")) {
                        etPwdH.setVisibility(VISIBLE);
                        etPwd.setVisibility(VISIBLE);
                        prod_brand_layout.setVisibility(GONE);
                        etPwdH.setText("");
                        etPwd.setText("");
                    }else{
                        etPwdH.setText("");
                        etPwd.setText("");
                        etPwdH.setVisibility(GONE);
                        etPwd.setVisibility(GONE);
                        prod_brand_layout.setVisibility(VISIBLE);

                    }
                    //Utils.showToast(mContext,sp);
                }



            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    public void showProductSubGroupSpinner() {
        productSubGroupList = mAceDnsDatabase.getProductSubGroupList(selectedGrp.getGroupCode(), carryInSales);
        LinearLayout prod_sub_grp_layout = (LinearLayout) findViewById(R.id.prod_sub_grp_layout);
        prod_sub_grp_layout.setVisibility(VISIBLE);
        prodSubGrpSpinner = (Spinner) findViewById(R.id.prodSubGrpSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < productSubGroupList.size(); i++) {
            spinnerArray.add(productSubGroupList.get(i).getSubGrpName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(R.layout.custom_spinner_dropdown_light);
        prodSubGrpSpinner.setAdapter(spinnerArrayAdapter);
        prodSubGrpSpinner.setSelection(0);

        prodSubGrpSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
            {
                selectedSubGrp = productSubGroupList.get(i);
                if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) == 4)
                {
                    if(sp.equalsIgnoreCase("flush door")){
                        getProductsShowOnList();
                    }else {
                        showProductBrandSpinner();
                    }
                }
                else
                {
                    if (orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes"))
                    {
                        showSkuSpinner();
                    }
                    else
                    {
                        getProductsShowOnList();
                    }
                }

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });

        if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes") && prodSubGrpSpinner.getSelectedItem().toString().equalsIgnoreCase("flush door"))
        {
            prod_sub_grp_layout.setVisibility(GONE);
        }



    }

    public void showProductBrandSpinner() {
        productBrandList = mAceDnsDatabase.getProductBrandList(selectedSubGrp.getSubGrpCode(), carryInSales);
//        productSubGroupList = mAceDnsDatabase.getProductSubGroupList(selectedGrp.getGroupCode(), carryInSales);
        LinearLayout prod_sub_grp_layout = (LinearLayout) findViewById(R.id.prod_brand_layout);
        prod_sub_grp_layout.setVisibility(VISIBLE);
        final Spinner prodBrandSpinner = (Spinner) findViewById(R.id.prodBrandSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < productBrandList.size(); i++) {
            spinnerArray.add(productBrandList.get(i).getBrandName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(R.layout.custom_spinner_dropdown_light);
        prodBrandSpinner.setAdapter(spinnerArrayAdapter);
        prodBrandSpinner.setSelection(0);

        prodBrandSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                selectedBrand = productBrandList.get(i);
                if (orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes"))
                {
                    showSkuSpinner();
                }
                else
                {

                    getProductsShowOnList();
                }

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    public void showSkuSpinner() {
        productMasterListSpecial = new ArrayList<>();
        if (Constants.productDetailsObj.getprod_size().equalsIgnoreCase("yes"))
        {
            productMasterListSpecial = mAceDnsDatabase.getProductMasterListAlternateDesignGrpByDesc(selectedGrp.getGroupCode(), selectedSubGrp.getSubGrpCode());
        }
        else
        {
            if(Constants.productDetailsObj.getNoFilter().equalsIgnoreCase("1"))
            {
                productMasterListSpecial = mAceDnsDatabase.getProductMasterListAlternateDesign("");
            }
            else
            {
                productMasterListSpecial = mAceDnsDatabase.getProductMasterListAlternateDesign(selectedGrp.getGroupCode());
            }

        }
        LinearLayout prod_layout = (LinearLayout) findViewById(R.id.prod_layout);
        prod_layout.setVisibility(VISIBLE);
        prodSpinner = (Spinner) findViewById(R.id.prodSpinner);
        for (int i = 0; i < productMasterListSpecial.size(); i++) {
            String descOfCurrentSku = productMasterListSpecial.get(i).getDesc();
            if (!Constants.productDetailsObj.getprod_size().equalsIgnoreCase("yes")) {
                descOfCurrentSku = descOfCurrentSku.split("-")[0].trim();
            }
            productMasterListSpecial.get(i).setDesc(descOfCurrentSku);
        }

        ProductSpinnerAdapter adapter = new ProductSpinnerAdapter((OrderFormActivityAlternateDesign) activity, R.layout.product_spinner_layout, productMasterListSpecial);
        prodSpinner.setAdapter(adapter);
        prodSpinner.setSelection(0);

        prodSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                currentProductMasterObjSpecial = productMasterListSpecial.get(i);

                getSkuWithSizeShowOnList();

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });


    }

    public void getSkuWithSizeShowOnList() {
        productMasterList = new ArrayList<>();
        productMasterList = mAceDnsDatabase.getSpecialProductMasterSizeListIgnoringStock1(selectedGrp.getGroupCode(), currentProductMasterObjSpecial.getDnsProdCode(), currentProductMasterObjSpecial.getDesc());

        removeRepeatedProductItems();
        tempProductList = new ArrayList<>();
        reInitialiseProductList();

        ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterAlternateDesign(OrderFormActivityAlternateDesign.this, R.layout.list_item__product_with_quantity_input_alternate_design, tempProductList);
        prodQtyRateListView = (ListView) findViewById(R.id.prodQtyRateListView);
        prodQtyRateListView.setEmptyView(findViewById(R.id.empty_text_view));
        prodQtyRateListView.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
    }

    public void getProductsShowOnList() {
        prepareProdData(1, "");
    }

    public void prepareProdData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        productMasterList = new ArrayList<>();
                        int filtersFromSetup = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
                        if (filtersFromSetup == 1)
                        {
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesign("");

                        }
                        else if (filtersFromSetup == 2 && selectedGrp != null)
                        {
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesign(selectedGrp.getGroupCode());
                        }
                        else if (filtersFromSetup == 3 && selectedSubGrp != null)
                        {
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesign(selectedSubGrp.getSubGrpCode());
                        }
                        else if (filtersFromSetup == 4 && selectedBrand != null)
                        {
                            //
                            //
                            if (sp.equalsIgnoreCase("flush door")){
                                productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesign("Flush Door");

                        }else{
                            //
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesign(selectedBrand.getBrandCode());
                            }
                        }else if (filtersFromSetup == 4 && sp.equalsIgnoreCase("flush door"))
                        {
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesign("Flush Door");
                        }

                        if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")){
                            //if(JsonsReceiver.getCustomerProductStock(mContext)) {
                            /*try {
                                Thread.sleep(1000);

                            } catch (InterruptedException e) {
                                // TODO Auto-generated catch block
                                e.printStackTrace();
                            }*/
                                productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesignCustomerStock("");
                            //}
                        }

                        removeRepeatedProductItems();

                        tempProductList = new ArrayList<>();
                        reInitialiseProductList();
                        ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterAlternateDesign(OrderFormActivityAlternateDesign.this, R.layout.list_item__product_with_quantity_input_alternate_design, tempProductList);
                        break;
                }
                Message msgObj = productDataHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("get products", doWhat);
                msgObj.setData(b);
                productDataHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void drawFilterLayout() {
        filterList = mAceDnsDatabase.getFilterList();// filterList =[4(filter_no),dadu,baba,NA,chhele]

        for (int ii = 1; ii < filterList.size(); ii++)
        {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LayoutParams buttonLayoutParams = new LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayoutParams.setMargins(0, 0, 0, 5);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LayoutParams buttonParams = new LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);


                buttonParams.gravity = Gravity.CENTER_VERTICAL;
                filterButton.setLayoutParams(buttonParams);
                filterButton.setTag(ii);
                filterButton.setHorizontallyScrolling(true);
                filterButton.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
                filterButton.setText(filterList.get(ii));
                //filterButton.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
                filterButton.setSingleLine(true);
                filterButton.setOnClickListener(this);
                //filterButton.setEnabled(false);
                buttonLayout.addView(filterButton);
                filterLayout.addView(buttonLayout);
                filterButtonList.add(filterButton);
            } else {
                filterButtonList.add(null);
            }
        }
    }

    private void makeEditTextDataChangeProcess(String currentInput) {
        try {
            if (qtySrate != null) {
                String existingInput = qtySrate.getText().toString().trim();
                if (!currentInput.matches("DOT")) {
                    if (existingInput.length() == 0) {
                        qtySrate.setText(currentInput);
                    } else {
                        qtySrate.setText(existingInput + currentInput);
                    }
                    qtySrate.setSelection(qtySrate.getText().length());
                } else  {
//					if(!isQtySelected)
//					{
                    if (existingInput.length() == 0) {
                        qtySrate.setText(".");
                        qtySrate.setSelection(qtySrate.getText().length());
                    } else {
                        if (!existingInput.contains(".")) {
                            qtySrate.setText(existingInput + ".");
                            qtySrate.setSelection(qtySrate.getText().length());
                        }
                    }

//					}


                }

            }
        } catch (Exception e) {

        }


    }

    public void Clicked0(View v) {
        makeEditTextDataChangeProcess("0");
    }

    public void Clicked1(View v) {
        makeEditTextDataChangeProcess("1");
    }

    public void Clicked2(View v) {
        makeEditTextDataChangeProcess("2");
    }

    public void Clicked3(View v) {
        makeEditTextDataChangeProcess("3");
    }

    public void Clicked4(View v) {
        makeEditTextDataChangeProcess("4");
    }

    public void Clicked5(View v) {
        makeEditTextDataChangeProcess("5");
    }

    public void Clicked6(View v) {
        makeEditTextDataChangeProcess("6");
    }

    public void Clicked7(View v) {
        makeEditTextDataChangeProcess("7");
    }

    public void Clicked8(View v) {
        makeEditTextDataChangeProcess("8");
    }

    public void Clicked9(View v) {
        makeEditTextDataChangeProcess("9");
    }

    public void DOTClicked(View v) {
        makeEditTextDataChangeProcess("DOT");
    }

    public void DELClicked(View v) {
        try {
            if (qtySrate != null) {
                String existingInput = qtySrate.getText().toString().trim();
                if (existingInput.length() > 0) {
                    qtySrate.setText(existingInput.substring(0, existingInput.length() - 1));
                }
                qtySrate.setSelection(qtySrate.getText().length());
            }
        } catch (Exception e) {

        }

    }

    public void showClosingStock(ProductMasterDetails productObj) {
        if (edClsngStk != null && productObj.getClosingStk() != null) {
            edClsngStk.setText(productObj.getClosingStk());
            edClsngStk.setBackgroundColor(Color.WHITE);
            edClsngStk.setEnabled(false);
        }
    }

    public void enableDisableLayout(boolean isShown) {
        filterLayout.setEnabled(isShown);
    }

    public void showMRPDialog(ProductMasterDetails productObj, String selectedUOM, Boolean shouldDivideByConversionFactor) {
        String productCode = productObj.getProdCode();
        if (shouldDivideByConversionFactor) {
            mrpSpinnerList = mAceDnsDatabase.getMRPListForOrder(productCode, selectedUOM, productObj.getConversionFactor());

        } else {
            mrpSpinnerList = mAceDnsDatabase.getMRPList(productCode, selectedUOM);
        }
        if (mrpSpinnerList.size() > 0) {
            String[] tempSpinnerArray = new String[mrpSpinnerList.size()];
            for (int k = 0; k < mrpSpinnerList.size(); k++) {
                tempSpinnerArray[k] = mrpSpinnerList.get(k).getMrpValue();
            }
            spinnerAdapter = new ArrayAdapter(OrderFormActivityAlternateDesign.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
            mrpSpinner.setAdapter(spinnerAdapter);
        }

    }

    public void showSaleRateDialog(ProductMasterDetails productObj, String selectedUOM) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = mAceDnsDatabase.getMRPList(productCode, selectedUOM);
        if (mrpSpinnerList.size() > 0) {
            customerType = Constants.selectedCustomer.getCustomerType();

            String[] tempSpinnerArray = new String[mrpSpinnerList.size()];
            for (int k = 0; k < mrpSpinnerList.size(); k++) {
                if (Constants.productDetailsObj.getMultipleRate().equalsIgnoreCase("yes")) {

                    if (customerType.matches("R") || customerType.matches("")) {
                        tempSpinnerArray[k] = mrpSpinnerList.get(k).getSaleRate();
                    } else if (customerType.matches("D")) {
                        tempSpinnerArray[k] = mrpSpinnerList.get(k).getDistributorRate();
                    } else if (customerType.matches("WS")) {
                        tempSpinnerArray[k] = mrpSpinnerList.get(k).getWSRate();
                    } else if (customerType.matches("SS")) {
                        tempSpinnerArray[k] = mrpSpinnerList.get(k).getSSRate();
                    } else if (customerType.matches("DEPOT")) {
                        tempSpinnerArray[k] = mrpSpinnerList.get(k).getDepotRate();
                    }
                } else {
                    tempSpinnerArray[k] = mrpSpinnerList.get(k).getSaleRate();
                }

            }
            ArrayAdapter adapter = new ArrayAdapter(OrderFormActivityAlternateDesign.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
            mrpSpinner.setAdapter(adapter);
        }

    }

    /*
     * ::::::::::::::::::::::::::::::: LISTNER METHODS
     * ::::::::::::::::::::::::::::::::::::::::::
     */

    public void onClick(View clkdView) {

        if (clkdView instanceof Button) {
            int tag = (Integer) clkdView.getTag();
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
                        Toast.makeText(OrderFormActivityAlternateDesign.this,
                                "Please select the parent category", Toast.LENGTH_SHORT);
                    }
                    break;
                case 3:
                    lastProdPos = 0;
                    filterButtonList.get(3).setEnabled(true);
                    lastProductSelected = false;
                    if (selectedSubGrp != null) {
                        prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                    } else {
                        Toast.makeText(OrderFormActivityAlternateDesign.this,
                                "Please select the parent category", Toast.LENGTH_SHORT);
                    }
                    break;
                case 4:
                    switch (filterNo) {
                        case 1:
                            prepareOrderData(4, "");
                            break;
                        case 2:
                            if (selectedGrp != null) {
                                prepareOrderData(4, selectedGrp.getGroupCode());
                            } else {
                                Toast.makeText(OrderFormActivityAlternateDesign.this,
                                        "Please select the parent category", Toast.LENGTH_SHORT);
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(OrderFormActivityAlternateDesign.this,
                                        "Please select the parent category", Toast.LENGTH_SHORT);
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                prepareOrderData(4, selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(OrderFormActivityAlternateDesign.this,
                                        "Please select the parent category", Toast.LENGTH_SHORT);
                            }
                            break;
                    }
            }

        }
        if (clkdView == mButtonAddtoCart) {
            addToCartProcess();

        }
        if (clkdView == mButtonCheckOut) {
            if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes") && Constants.selectedCustomer.getCustomerType().toLowerCase().matches("r")) {
                if (HttpCalling.isConnectionPossible(OrderFormActivityAlternateDesign.this)){
                    checkoutProcess();
                }else{
                    Utils.showToast(OrderFormActivityAlternateDesign.this,"You need an active internet connection to use this feature.");
                }
            }else {

                checkoutProcess();
            }
        }
        if (clkdView == mButtonNoOrder) {
            ShowNoOrderDialog();
        }
        if (clkdView == mButtonBack) {
            if (carryInSales && Constants.selectedProductMasterList.size() > 0) {
                AceDnsTransactionDatabase dbObj = new AceDnsTransactionDatabase(
                        mContext);
                dbObj.increaseClStk();
                dbObj.close();
            }
            finish();
        }
    }

    private void checkoutProcess() {
        if (Constants.selectedProductMasterList.size() > 0) {
            Intent intent = new Intent(OrderFormActivityAlternateDesign.this, OrderConfirmationActivity.class);
            intent.putExtra("CARRY_IN", carryInSales);
            if (isNavigatedFromSalesOption) {
                intent.putExtra("salesOption", true);
            }
            startActivity(intent);
        } else {
            Toast.makeText(OrderFormActivityAlternateDesign.this,
                    "Please add at least one product--", Toast.LENGTH_LONG).show();
        }
    }

    private void addToCartProcess() {
        // Continue Button
        if (currentProductMasterObj != null) {

            if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
            } else if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
                currentProductMasterObj.setUom1(uom1);
                currentProductMasterObj.setUom2(uom2);
                currentProductMasterObj.setUomSelectedForProduct(selecteduom);
            } else {
                currentProductMasterObj.setUomSelectedForProduct(uom1);
            }

            boolean qty_status = true, mrp_status = true, tdStatus = true, vatStatus = true;
            if (mOrderPriceValidationType.matches("DOPS")) {
                //do nothing for now
            } else {
                qty_status = false;
            }
            if (mOrderPriceValidationType.matches("DOPS")) {
                //do nothing for now
            } else if (mrpSpinner != null) {
                if (!mrpSpinner.getSelectedItem().toString().equalsIgnoreCase("FOC")) {
                    if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                        currentProductMasterObj.setMrpCode(mrpSpinnerList.get(mrpSelected).getMrpCode());
                        currentProductMasterObj.setMrpValue(mrpSpinnerList.get(mrpSelected).getMrpValue());
                        Double slRt = Double.parseDouble(mrpSpinnerList.get(mrpSelected).getMrpValue());
                        Double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                        if (qtyD > 0) {
                            currentProductMasterObj.setAmount(defaultFormat.format(slRt * qtyD));
                        }
                    } else {
                        currentProductMasterObj.setMrpCode(mrpSpinnerList.get(mrpSelected).getMrpCode());
                        String currentSaleRate = "";
                        if (Constants.productDetailsObj.getMultipleRate().equalsIgnoreCase("yes")) {

                            if (customerType.matches("R") || customerType.matches("")) {
                                currentSaleRate = mrpSpinnerList.get(mrpSelected).getSaleRate();
                            } else if (customerType.matches("D")) {
                                currentSaleRate = mrpSpinnerList.get(mrpSelected).getDistributorRate();
                            } else if (customerType.matches("WS")) {
                                currentSaleRate = mrpSpinnerList.get(mrpSelected).getWSRate();
                            } else if (customerType.matches("SS")) {
                                currentSaleRate = mrpSpinnerList.get(mrpSelected).getSSRate();
                            } else if (customerType.matches("DEPOT")) {
                                currentSaleRate = mrpSpinnerList.get(mrpSelected).getDepotRate();
                            }
                        } else {
                            currentSaleRate = mrpSpinnerList.get(mrpSelected).getSaleRate();
                        }

//							String currentSaleRate = mrpSpinnerList.get(mrpSelected).getSaleRate();
                        currentProductMasterObj.setMrpValue(currentSaleRate);
                        Double slRt = Double.parseDouble(currentSaleRate);
                        Double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                        if (qtyD > 0) {
                            currentProductMasterObj.setAmount(defaultFormat.format(slRt * qtyD));
                        }
                    }
                } else {
                    currentProductMasterObj.setMrpCode("FOC");
                    currentProductMasterObj.setMrpValue("0");
                    currentProductMasterObj.setAmount(String.valueOf(0));
                }
            } else if (edSaleRate != null) {
                if (edSaleRate.getText().toString().length() > 0 && !edSaleRate.getText().toString().equalsIgnoreCase(".")) {
                    currentProductMasterObj.setMrpCode("0");
                    currentProductMasterObj.setMrpValue(edSaleRate.getText().toString());
                    Double slRt = Double.parseDouble(edSaleRate.getText().toString());
                    Double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                    if (qtyD > 0) {
                        currentProductMasterObj.setAmount(defaultFormat.format(slRt * qtyD));
                    }
                } else {
                    if (edAmount != null
                            && edAmount.getText().toString().length() > 0
                            && !edAmount.getText().toString().equalsIgnoreCase(".")) {
                        Double amountD = Double.parseDouble(edAmount.getText().toString());
                        Double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                        if (qtyD > 0) {
                            currentProductMasterObj.setMrpCode("0");
                            currentProductMasterObj.setMrpValue(String.valueOf(amountD / qtyD));
                            currentProductMasterObj.setAmount(String.valueOf(amountD));
                            currentProductMasterObj.setAmtEntered(true);
                        } else {
                            mrp_status = false;
                        }
                    } else {
                        mrp_status = false;
                    }
                }
            } else {
                currentProductMasterObj.setMrpCode("0");
                currentProductMasterObj.setMrpValue("0");
            }
            if (!mOrderPriceValidationType.matches("DOPS")) {
                if (edTd != null) {
                    if (Constants.orderFormDetailsObj.getVat()
                            .equalsIgnoreCase("no")) {
                        currentProductMasterObj.setVat("0");
                        String td = "0";
                        String premium = "0";
                        if (edTd.getText().toString() != null
                                && edTd.getText().toString().length() > 0
                                && !edTd.getText().toString()
                                .equalsIgnoreCase(".")) {
                            td = edTd.getText().toString();
                            premium = edTd.getText().toString();
                        }

                        if (isTD == true && isPremium == false) {
                            if (Double.parseDouble(td) <= 100) {
                                currentProductMasterObj.setTradeDiscnt(td);
                                currentProductMasterObj.setIsTradeDiscount(true);
                            } else {
                                tdStatus = false;
                                Toast.makeText(OrderFormActivityAlternateDesign.this,
                                        "Trade Discount cannot be more than 100 %",
                                        Toast.LENGTH_SHORT).show();
                            }

                        }
                        if (isTD == false && isPremium == true) {
                            if (Double.parseDouble(premium) <= 100) {
                                currentProductMasterObj.setPremium(premium);
                                currentProductMasterObj.setIsTradeDiscount(false);
                            } else {
                                Toast.makeText(OrderFormActivityAlternateDesign.this,
                                        "Premium cannot be more than 100 %",
                                        Toast.LENGTH_SHORT).show();
                            }
                        }


                    } else {
                        currentProductMasterObj.setTradeDiscnt("0");
                        String vat = "0";
                        if (edTd.getText().toString() != null
                                && edTd.getText().toString().length() > 0
                                && !edTd.getText().toString()
                                .equalsIgnoreCase(".")) {
                            vat = edTd.getText().toString();
                            currentProductMasterObj.setVat(vat);
                        }
                    }
                } else {
                    currentProductMasterObj.setTradeDiscnt("0");
                    currentProductMasterObj.setVat("0");
                }
            }

            if (qty_status && mrp_status && tdStatus) {
//                customerLayout.setVisibility(View.VISIBLE);
                if (Constants.selectedProductMasterList.size() % 2 == 0) {
                    customerLayout.setBackgroundColor(Color.YELLOW);
                } else {
                    customerLayout.setBackgroundColor(Color.GREEN);
                }
                Constants.selectedProductMasterList.add(currentProductMasterObj);
                mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
                CalculateTotal();

                if (carryInSales) {
                    mAceDnsTransactionDatabase
                            .reduceClStkProductWise(currentProductMasterObj);
                }
                if (!mOrderPriceValidationType.matches("DOPS")) {
                    Toast.makeText(OrderFormActivityAlternateDesign.this, "Product has been added to cart.", Toast.LENGTH_SHORT).show();

                }

                currentProductMasterObj = null;
                if (mrpSpinner != null) {
                    String[] tempSpinnerArray = new String[0];
                    spinnerAdapter = new ArrayAdapter(
                            OrderFormActivityAlternateDesign.this,
                            android.R.layout.simple_spinner_item,
                            tempSpinnerArray);
                    mrpSpinner.setAdapter(spinnerAdapter);
                }

                switch (filterNo) {
                    case 1:
                        if (lastProductSelected) {
                            mButtonAddtoCart.setEnabled(false);
                        }
                        break;
                    case 2:
                        if (lastGrpSelected && lastProductSelected) {
                            mButtonAddtoCart.setEnabled(false);
                        }
                        break;
                    case 3:
                        if (lastGrpSelected && lastSubGroupSelected
                                && lastProductSelected) {
                            mButtonAddtoCart.setEnabled(false);
                        }
                        break;
                    case 4:
                        if (lastGrpSelected && lastSubGroupSelected
                                && lastBrandSelected && lastProductSelected) {
                            mButtonAddtoCart.setEnabled(false);
                        }
                        break;
                }

                switch (filterNo) {
                    case 1:
                        if (lastProductSelected) {
                            // No option but to submit
                            filterButtonList.get(3)
                                    .setText("No product left..");
                            filterButtonList.get(3).setEnabled(false);
                        }
                    case 2:
                        if (lastProductSelected) {
                            Constants.selectedGroupList.add(selectedGrp);
                            filterButtonList
                                    .get(3)
                                    .setText(
                                            "No product left.Select a different parent category.");
                            filterButtonList.get(3).setEnabled(false);
                        }
                    case 3:
                        if (lastProductSelected) {
                            Constants.selectedSubGroupList.add(selectedSubGrp);
                            filterButtonList
                                    .get(3)
                                    .setText(
                                            "No product left.Select a different parent category.");
                            filterButtonList.get(3).setEnabled(false);
                            if (lastSubGroupSelected) {
                                Constants.selectedGroupList.add(selectedGrp);
                                filterButtonList
                                        .get(1)
                                        .setText(
                                                "No product left.Select a different parent category.");
                                filterButtonList.get(1).setEnabled(false);
                            }
                        }
                        break;
                    case 4:
                        if (lastProductSelected) {
                            Constants.selectedBrandList.add(selectedBrand);
                            filterButtonList
                                    .get(3)
                                    .setText(
                                            "No product left.Select a different parent category.");
                            filterButtonList.get(3).setEnabled(false);
                            if (lastBrandSelected) {
                                Constants.selectedSubGroupList
                                        .add(selectedSubGrp);
                                filterButtonList
                                        .get(2)
                                        .setText(
                                                "No product left.Select a different parent category.");
                                filterButtonList.get(2).setEnabled(false);
                                if (lastSubGroupSelected) {
                                    Constants.selectedGroupList
                                            .add(selectedGrp);
                                    filterButtonList
                                            .get(1)
                                            .setText(
                                                    "No product left.Select a different parent category.");
                                    filterButtonList.get(1).setEnabled(false);
                                }
                            }

                        }
                        break;
                }

            } else {
                if (false == qty_status) {
                    Toast.makeText(OrderFormActivityAlternateDesign.this, "Please provide valid quantity.", Toast.LENGTH_SHORT).show();
                    mTextViewUOM1.setText(selecteduom);
                } else if (false == mrp_status) {
                    if (Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown") ||
                            Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                        Toast.makeText(OrderFormActivityAlternateDesign.this, "Error in mrp data. Please Synchronize Data.", Toast.LENGTH_SHORT).show();
                    } else {
                        Toast.makeText(OrderFormActivityAlternateDesign.this, "Please provide valid inputs.", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(OrderFormActivityAlternateDesign.this, "Please provide valid inputs.", Toast.LENGTH_SHORT).show();
                }
            }
        } else {
            Toast.makeText(OrderFormActivityAlternateDesign.this, "Please select a product", Toast.LENGTH_SHORT).show();
        }
        edQty.setText("");
        mTextViewUOM1.setText("");
        if (edAmount != null) {
            edAmount.setText("");
        }
        if (edTd != null
                && !Constants.orderFormDetailsObj.getTdType()
                .equalsIgnoreCase("customer wise")) {
            edTd.setText("");
        }
    }

    /*
     * :::::::::::::::::::::::::::::: CREATING DIFFERENT DIALOGs
     * :::::::::::::::::::::::::::::::::
     */

    public void showChooseCustomerDialog() {
        customerListDialog = new Dialog(OrderFormActivityAlternateDesign.this,
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
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
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

                System.out.println("String::::::::" + str);
            }
        });

        ListView dialogList = (ListView) customerListDialog
                .findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                Constants.isSelectCustomer = true;
                CustomerDetails currentObj = tempCustomerList.get(arg2);
                for (int ii = 0; ii < customerList.size(); ii++) {
                    if (customerList.get(ii).getCustomerCode()
                            .equalsIgnoreCase(currentObj.getCustomerCode())) {
                        chosenCustPos = ii;
                    }
                }
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                customerListDialog.cancel();

                mTextViewCustomerName.setText(customerList.get(chosenCustPos).getCustomerName());
                Constants.selectedCustomer = customerList.get(chosenCustPos);
                enableDisableLayout(true);
                mButtonNoOrder.setEnabled(true);
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }

                if (edTd != null
                        && Constants.orderFormDetailsObj.getTdType()
                        .equalsIgnoreCase("customer wise")) {
                    edTd.setText(currentObj.getTradeDiscount());
                    edTd.setEnabled(false);
                }

                if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
                    prepareOrderData(5, "");
                } else {
                    if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")) {
                        ShowSaudaDepoNameDialog();
                    }
                }
            }
        });

        Button addCustomer = (Button) customerListDialog
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(GONE);
        customerListDialog.show();
    }


    public void ShowNoOrderDialog() {
        final Dialog noOrderDialog = new Dialog(OrderFormActivityAlternateDesign.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.user_instruction_dialog);
        noOrderDialog.setCancelable(false);
        TextView title = (TextView) noOrderDialog.findViewById(R.id.title);
        title.setText("Please state the reason for no order");
        final EditText edReason = (EditText) noOrderDialog
                .findViewById(R.id.ed_input);
        final Button submit = (Button) noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                noOrderDialog.cancel();
                Boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
                if (isTimeAutomatic) {
                    new GPSTracker(mContext);
                    String reason = "";
                    reason = edReason.getText().toString();
                    String timeStamp = Constants.dateString
                            + new SimpleDateFormat("_HHmmss").format(Calendar
                            .getInstance().getTime());
                    timeStamp = timeStamp.replace("_", "");
                    mAceDnsTransactionDatabase.insertToOrderHeaderTable("NO", reason,
                            timeStamp, "", "", "", "", "NO", "", "", "", "");
                    mAceDnsTransactionDatabase.insertToLocationTable("NO", timeStamp);

                    if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                        boolean isExist = mAceDnsTransactionDatabase.IsUnuploadedRoutePlanExist();
                        if (true == isExist) {
                            new TRANS_PendingRoutePlanBeforeOtherTxn(OrderFormActivityAlternateDesign.this, "ORDER").execute();
                        } else {
                            new TRANS_SubmitOrderTask(OrderFormActivityAlternateDesign.this, true).execute();
                        }
                    } else {
                        new TRANS_SubmitOrderTask(OrderFormActivityAlternateDesign.this, true).execute();
                    }
                } else {
                    Utils.showSettingsAlertToChangeTimeZone(mContext);
                }


            }
        });
        noOrderDialog.show();
    }

    /*
     *
     */

    public void showGrpListDialog() {
        if (productGroupList.size() > 0) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            groupAdapter = new ProductGrpAdapter(OrderFormActivityAlternateDesign.this,
                    R.layout.product_list_child, tempProductGroupList);
            grpDialog = new Dialog(OrderFormActivityAlternateDesign.this, R.style.PauseDialog);
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
            dialogList.setOnItemClickListener(new OnItemClickListener() {
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
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        } else {
            Toast.makeText(OrderFormActivityAlternateDesign.this,
                    "There are no items left.Please submit order.", Toast.LENGTH_SHORT)
                    .show();
        }
    }

    public void showSubGrpListDialog() {
        if (productSubGroupList.size() > 0) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGroupAdapter = new ProductSubGrpAdapter(OrderFormActivityAlternateDesign.this,
                    R.layout.product_list_child, tempProductSubGroupList);
            subGrpDialog = new Dialog(OrderFormActivityAlternateDesign.this,
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
            dialogList.setOnItemClickListener(new OnItemClickListener() {
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
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    subGrpDialog.cancel();
                }
            });
            subGrpDialog.show();
        } else {
            Toast.makeText(
                    OrderFormActivityAlternateDesign.this,
                    "There are no items left in this category. Please choose a different category",
                    Toast.LENGTH_SHORT).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    public void showBrandListDialog() {
        if (productBrandList.size() > 0) {
            if (productBrandList.size() == 1) {
                lastBrandSelected = true;
            }
            brandAdapter = new ProductBrandAdapter(OrderFormActivityAlternateDesign.this,
                    R.layout.product_list_child, tempProductBrandList);
            brandDialog = new Dialog(OrderFormActivityAlternateDesign.this,
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
            dialogList.setOnItemClickListener(new OnItemClickListener() {
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
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    brandDialog.cancel();
                }
            });
            brandDialog.show();
        } else {
            Toast.makeText(
                    OrderFormActivityAlternateDesign.this,
                    "There are no items left in this category. Please choose a different category",
                    Toast.LENGTH_SHORT).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    public void showMasterListDialog() {
        if (productMasterList.size() > 0) {
            if (productMasterList.size() == 1) {
                lastProductSelected = true;
            }
            if (mOrderPriceValidationType.matches("DOPS")) {
                ProductMasterWithQtyInputAdapterObject = new ProductMasterWithQtyInputAdapter(OrderFormActivityAlternateDesign.this, R.layout.product_list_item_with_quantity_input, tempProductList);
            } else {
                prodAdapter = new ProductMasterAdapter(OrderFormActivityAlternateDesign.this, R.layout.product_list_child, tempProductList);
            }

            masterDialog = new Dialog(OrderFormActivityAlternateDesign.this, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please select a product");
            EditText searchText = (EditText) masterDialog.findViewById(R.id.autoCompleteTextView1);

            if (mOrderPriceValidationType.matches("DOPS")) {
                View list_header_item_planwise_input_screen = masterDialog.findViewById(R.id.list_header_item_planwise_input_screen);
                list_header_item_planwise_input_screen.setVisibility(VISIBLE);
                if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
                    TextView etProdQty = (TextView) masterDialog.findViewById(R.id.etProdQty);
                    etProdQty.setVisibility(VISIBLE);
                    TextView tv_last_month_purchase = (TextView) masterDialog.findViewById(R.id.tv_last_month_purchase);
                    TextView tv_order_plan = (TextView) masterDialog.findViewById(R.id.tv_order_plan);
                    tv_last_month_purchase.setVisibility(GONE);
                    tv_order_plan.setVisibility(GONE);
                } else {
                    TextView etProdRate = (TextView) masterDialog.findViewById(R.id.etProdRate);
                    etProdRate.setVisibility(GONE);
                }
                searchText.setVisibility(GONE);
                if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
//					title.setText(Html.fromHtml("Please provide valid inputs for each <font color='#F58322'> focused </font> product"));
                    title.setVisibility(GONE);
                    ImageView ivSideImage = (ImageView) masterDialog.findViewById(R.id.imageView1);
                    ivSideImage.setVisibility(GONE);
                    ImageView image_cancel = (ImageView) masterDialog.findViewById(R.id.image_cancel);
                    LinearLayout applogoLayout = (LinearLayout) masterDialog.findViewById(R.id.applogoLayout);
                    applogoLayout.setVisibility(VISIBLE);
                    image_cancel.setVisibility(VISIBLE);
                    image_cancel.setOnClickListener(new OnClickListener() {
                        @Override
                        public void onClick(View view) {
                            masterDialog.cancel();
                        }
                    });
                } else {
                    title.setText("Please provide quantity for each product");
                }

            }
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
            prodQtyRateListView = (ListView) masterDialog.findViewById(R.id.list);

            if (mOrderPriceValidationType.matches("DOPS")) {
                prodQtyRateListView.setAdapter(ProductMasterWithQtyInputAdapterObject);
            } else {
                prodQtyRateListView.setAdapter(prodAdapter);
            }


            if (lastProdPos != 0) {
                prodQtyRateListView.setSelection(lastProdPos - 1);
            } else {
                prodQtyRateListView.setSelection(0);
            }
            prodQtyRateListView.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {

                    lastProdPos = arg2;
                    currentProductMasterObj = tempProductList.get(arg2);
                    masterDialog.cancel();
                    uom1 = currentProductMasterObj.getUom1();
                    uom2 = currentProductMasterObj.getUom2();
                    if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes")) {
                        ArrayList<String> uomList = new ArrayList<String>();
                        uomList.add(uom1);
                        uomList.add(uom2);
                        showUOMDialog(uomList);
                        filterButtonList.get(3).setText(currentProductMasterObj.getDesc());
                    } else {
                        if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                            showMRPDialog(currentProductMasterObj, "", false);
                        }
                        if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                            showSaleRateDialog(currentProductMasterObj, "");
                        }
                        if (Constants.productDetailsObj.getSecondaryUnit().equalsIgnoreCase("yes")) {
                            mTextViewUOM1.setText(currentProductMasterObj.getSecondaryUnit());
                        } else {
                            mTextViewUOM1.setText(currentProductMasterObj.getUom1());
                        }

                        if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes") || carryInSales) {
                            showClosingStock(currentProductMasterObj);
                        }
                        if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
                            showUomListDialogForMultipleUom();
                        } else {
                            if (Constants.orderFormDetailsObj.getPreviousOrder().equalsIgnoreCase("yes")) {

                                ShowPreviousOrderDialog();
                            }
                        }

                        filterButtonList.get(3).setText(currentProductMasterObj.getDesc());
                        if (mOrderPriceValidationType.matches("SPA")) {
                            MRPDetails mrpListObj = mAceDnsDatabase.getMRPByProdCode(currentProductMasterObj.getProdCode());
                            if (orderFormDetailsObjFixed.getSaleRate().equalsIgnoreCase("yes")) {
                                refPrice.setText(mrpListObj.getSaleRate());
                            } else if (orderFormDetailsObjFixed.getMrp().equalsIgnoreCase("yes")) {
                                refPrice.setText(mrpListObj.getMrpValue());
                            } else {
                                refPrice.setText("0");
                            }
                        }

                    }
                }
            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    masterDialog.cancel();
                }
            });
            Button btn_addToCart = (Button) masterDialog.findViewById(R.id.btn_addToCart);
            if (mOrderPriceValidationType.matches("DOPS")) {
                btn_addToCart.setVisibility(VISIBLE);
            }

            btn_addToCart.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {

                    Boolean isEmptyInputPresentInList = false;
                    for (int i = 0; i < Constants.nameValuesProductList.size(); i++) {
                        try {
                            String currentqty = Constants.nameValuesProductList.get(i).getQty();
                            String currentRate = Constants.nameValuesProductList.get(i).getMrpValue();
                            if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
                                if (Constants.nameValuesProductList.get(i).getFocus().equalsIgnoreCase("y") && (!Utils.isNumeric(currentqty) || !Utils.isNumeric(currentRate))) {
                                    isEmptyInputPresentInList = true;
                                }
                            } else if (!Utils.isNumeric(currentqty)) {
                                isEmptyInputPresentInList = true;
                            }
                        } catch (Exception e) {
                            isEmptyInputPresentInList = true;
                        }

                    }
                    if (!isEmptyInputPresentInList) {
                        Double amountFinal = 0.0, productamount = 0.0;
                        masterDialog.cancel();
                        for (int i = 0; i < Constants.nameValuesProductList.size(); i++) {
                            lastProdPos = i;
                            String currentmrpValue = Constants.nameValuesProductList.get(i).getMrpValue();
                            String currentqty = Constants.nameValuesProductList.get(i).getQty();


                            if (Utils.isNumeric(currentmrpValue) && Utils.isNumeric(currentqty)) {
                                currentProductMasterObj = Constants.nameValuesProductList.get(i);
                                currentProductMasterObj.setQty(currentqty);
                                currentProductMasterObj.setMrpCode(Constants.nameValuesProductList.get(i).getMrpCode());

                                currentProductMasterObj.setMrpValue(currentmrpValue);
                                filterButtonList.get(3).setText(currentProductMasterObj.getDesc());
                                uom1 = currentProductMasterObj.getUom1();
                                uom2 = currentProductMasterObj.getUom2();
                                if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")) && Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes"))
                                {
                                    boolean qtyEligibleForTD = Utils.checkIfGivenQuantityEligibleForTD(mContext, currentqty, Constants.nameValuesProductList.get(i).getProdCode(), Constants.selectedCustomer.getBranchCode(), Constants.selectedCustomer.getCustClass());
                                    currentProductMasterObj.setQuantityEligibleForTD(qtyEligibleForTD);
                                    currentProductMasterObj.setTDPercent(Constants.currentTdPercent);
                                    productamount = Double.parseDouble(currentqty) * Double.parseDouble(currentmrpValue);
                                    if (qtyEligibleForTD) {

                                        Double amount = productamount - (productamount * (Double.parseDouble(Constants.currentTdPercent) / 100));
                                        amountFinal = amountFinal + amount;
                                        currentProductMasterObj.setAmount(defaultFormat.format(amount));
                                    } else {
                                        currentProductMasterObj.setAmount(defaultFormat.format(productamount));
                                        amountFinal = amountFinal + productamount;
                                    }
                                }
                                else
                                {
                                    double quantityInDOuble = Double.parseDouble(currentqty);
                                    double amnt = quantityInDOuble * Double.parseDouble(currentmrpValue);
                                    currentProductMasterObj.setAmount(defaultFormat.format(amnt));
                                    amountFinal = amountFinal + amnt;
                                }
                                addToCartProcess();
                            }


                        }
                        mTextViewTotalOrderValue.setText(currency + defaultFormat.format(amountFinal));
                        checkoutProcess();
                    } else {
                        if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
                            Toast.makeText(mContext, "Please provide valid input for all the focused products", Toast.LENGTH_SHORT).show();
                        } else {
                            Toast.makeText(mContext, "Please provide quantity for all the products even if it is zero", Toast.LENGTH_SHORT).show();
                        }

                    }

                }
            });

            masterDialog.show();
        } else {
            Toast.makeText(
                    OrderFormActivityAlternateDesign.this,
                    "There are no items left in this category. Please choose a different category",
                    Toast.LENGTH_SHORT).show();
            filterButtonList.get(3).setText("");
        }
    }

    public void showUomListDialogForMultipleUom() {
        final Dialog payTypeDialog = new Dialog(OrderFormActivityAlternateDesign.this, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.uom_dialog);
        TextView txtMsg = (TextView) payTypeDialog.findViewById(R.id.title);
        txtMsg.setText("Select UOM");

        RadioButton uom1RadioButton = (RadioButton) payTypeDialog.findViewById(R.id.radio0);
        uom1RadioButton.setText(uom1);

        RadioButton uom2RadioButton = (RadioButton) payTypeDialog.findViewById(R.id.radio1);
        uom2RadioButton.setText(uom2);

        RadioGroup payTypeOption = (RadioGroup) payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                int radioButtonID = group.getCheckedRadioButtonId();
                View radioButton = group.findViewById(radioButtonID);
                int selectedRadio = group.indexOfChild(radioButton);
                if (selectedRadio == 0) {
                    selecteduom = uom1;
                    showMRPDialog(currentProductMasterObj, "", false);
                } else {
                    selecteduom = uom2;
                    showMRPDialog(currentProductMasterObj, "", true);
                }


                mTextViewUOM1.setText(selecteduom);
                payTypeDialog.cancel();
                if (Constants.orderFormDetailsObj.getPreviousOrder().equalsIgnoreCase("yes")) {

                    ShowPreviousOrderDialog();
                }

            }
        });
        Button cancel = (Button) payTypeDialog.findViewById(R.id.btn_cancel);
        cancel.setVisibility(GONE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                payTypeDialog.cancel();
            }
        });
        payTypeDialog.show();
    }

    public void showRouteListDialog(final ArrayList<RouteDetails> routeList) {
        routeDialog = new Dialog(OrderFormActivityAlternateDesign.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        RouteAdapter adapter1 = new RouteAdapter(OrderFormActivityAlternateDesign.this,
                R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routeDialog.cancel();
                selectedRoute = routeList.get(arg2);

                routeCode = selectedRoute.getRouteCode();
                routeName = selectedRoute.getRouteName();
                mTextViewRouteName.setText("Route: " + routeName);
                customerList = mAceDnsDatabase
                        .getCustomerListByRoute(selectedRoute.getRouteCode());
                if (customerList.size() > 0) {
                    tempCustomerList = new ArrayList<>();
                    reInitialiseCustomerList();
                    adapterCust = new CustomerAdapter(OrderFormActivityAlternateDesign.this,
                            R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found.",
                            Toast.LENGTH_SHORT).show();
                    // showAddCustomerDialog();
                }
            }
        });
        Button cancel = (Button) routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
            }
        });
        Button create_route = (Button) routeDialog
                .findViewById(R.id.create_route);
        create_route.setVisibility(GONE);
        routeDialog.show();
    }

    /*
     * ::::::::::::::::::::::::::::::: REMOVING REPEATED ITEMS FROM SELECTION
     * LIST :::::::::::::::::::::::::::::::
     */

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
            ProductGroupDetails currentItem = Constants.selectedGroupList.get(kk);
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

    public void filterCustomerArray(int strCnt, String charVal) {
        int size = tempCustomerList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempCustomerList.get(ii).getCustomerName().length() >= strCnt) {
                // if(tempCashTransferList.get(ii).getCustomerName().substring(0,strCnt).equalsIgnoreCase(charVal)){
                /*
                 * String[] wordList =
                 * tempCashTransferList.get(ii).getCustomerName().split(" ");
                 * Boolean found = checkMatch(wordList,strCnt,charVal);
                 */
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

    public void reInitialiseCustomerList() {
        tempCustomerList.removeAll(tempCustomerList);
        int size = tempCustomerList.size();
        int size1 = customerList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < customerList.size(); kk++) {
            tempCustomerList.add(customerList.get(kk));
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

    public boolean checkMatch(String[] wordList, int strCnt, String charVal) {
        for (String word : wordList) {
            if (word.length() >= strCnt
                    && word.substring(0, strCnt).equalsIgnoreCase(charVal))
                return true;
        }
        return false;
    }

    // Later we will consider the case where VAT = yes and TD =
    // order_value_wise.
    public void CalculateTotal() {
        double amount = 0;
        double productamount = 0;
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && !(Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise"))
                    && !Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes"))

            {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
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

                    if (Constants.orderFormDetailsObj.getTdCalc().toLowerCase().contains("sku wise#amount"))
                    {
                        productamount = (qty * (mrp - discount)) + (premium * qty);
                        amount = amount + productamount;

                    } else {
                        productamount = ((mrp * qty) - (mrp * qty * discount / 100)) + (premium * qty);
                        amount = amount
                                + productamount;
                    }
                    Constants.selectedProductMasterList.get(ii).setAmount(String.valueOf(productamount));


                }
            } else
            if ((Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") || Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise and order value wise")) && Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes"))
                {
                if (!mOrderPriceValidationType.matches("DOPS")) {
                    for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                        ProductMasterDetails currentObj = Constants.selectedProductMasterList
                                .get(ii);
                        double qty = Double.parseDouble(currentObj.getQty());
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
                            Constants.selectedProductMasterList.get(ii).setAmount(defaultFormat.format(amount));
                        } else {
                            Constants.selectedProductMasterList.get(ii).setAmount(defaultFormat.format(productamount));
                        }
                    }
                } else {

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
                    amount = amount + (mrp * qty);
                }
                amount = (amount)
                        - (amount * Double.parseDouble(trdDisc) / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase("amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);

                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + vat);
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }
        mTextViewTotalOrderValue.setText(currency + defaultFormat.format(amount));

        if (!Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes") && !Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
            double qty = 0.0;
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                qty = qty + Double.parseDouble(currentObj.getQty());
            }
            mTextViewTotalOrderValue.setText("Vol. " + qty);
        }
    }


    public void prepareOrderData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        productGroupList = mAceDnsDatabase.getProductGroupList(carryInSales);
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

                        removeRepeatedProductItems();

                        tempProductList = new ArrayList<ProductMasterDetails>();
                        reInitialiseProductList();
                        break;
                    case 5:
                        int max = 0;
                        max = mAceDnsDatabase.GetVerticalValue();
                        values = new String[max];
                        if (max > 0) {
                            for (int i = 0; i < mVerticalValueList.length; i++) {
                                values[i] = mVerticalValueList[i];
                            }
                        }

                        break;
                    case 6://for multiple uom yes

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

    public void showUOMDialog(final ArrayList<String> uomList) {
        SimpleStringAdapter adapterUOM = new SimpleStringAdapter(
                OrderFormActivityAlternateDesign.this, R.layout.simple_list_child, uomList);
        uomDialog = new Dialog(OrderFormActivityAlternateDesign.this, R.style.PauseDialog);
        uomDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        uomDialog.setContentView(R.layout.choose_customer_search);
        uomDialog.setCancelable(false);
        TextView title = (TextView) uomDialog.findViewById(R.id.title);
        title.setText("Please select an UOM");
        EditText searchText = (EditText) uomDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(GONE);
        ListView dialogList = (ListView) uomDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterUOM);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                String selectedUOM = uomList.get(arg2);
                currentProductMasterObj.setSelectedUOM(arg2);
                if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase(
                        "yes")) {
                    showMRPDialog(currentProductMasterObj, selectedUOM, false);
                }
                if (Constants.orderFormDetailsObj.getSaleRate()
                        .equalsIgnoreCase("yes")
                        && Constants.orderFormDetailsObj.getSaleRateDrpdwn()
                        .equalsIgnoreCase("dropdown")) {
                    showSaleRateDialog(currentProductMasterObj, selectedUOM);
                }
                if (currentProductMasterObj != null) {
                    if (arg2 == 1) {
                        edQty.setInputType(InputType.TYPE_CLASS_NUMBER);
                    } else {
                        edQty.setInputType(InputType.TYPE_CLASS_NUMBER
                                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                    }
                    if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") &&
                            Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes")) {
                        mTextViewUOM1.setText("Pcs.");
                    } else {
                        mTextViewUOM1.setText(selectedUOM);
                    }
                    uomDialog.cancel();

                    if (Constants.orderFormDetailsObj.getClosingStk()
                            .equalsIgnoreCase("yes") || carryInSales) {
                        showClosingStock(currentProductMasterObj);
                    }
                } else {
                    Utils.showCommonAlertDialog(mContext, "Please Note!", "No rate found! Please Synchronize Data.");
                }

            }
        });
        Button addCustomer = (Button) uomDialog.findViewById(R.id.btn_add);
        addCustomer.setVisibility(GONE);
        uomDialog.show();
    }

    public void ShowVericalValueList() {
        final Dialog mPincodeDialog = new Dialog(OrderFormActivityAlternateDesign.this, R.style.PauseDialog);
        mPincodeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mPincodeDialog.setContentView(R.layout.select_with_search);
        mPincodeDialog.setCancelable(false);

        TextView title = (TextView) mPincodeDialog.findViewById(R.id.title);
        title.setText("Please select a vertical");
        ListView dialogList = (ListView) mPincodeDialog.findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
                R.layout.activity_listview, values);
        dialogList.setAdapter(adapter);

        EditText searchText = (EditText) mPincodeDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int position, long arg3) {
                mVerticalValue = adapter.getItem(position);
                mPincodeDialog.cancel();
                if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")) {
                    ShowSaudaDepoNameDialog();
                }
            }
        });

        Button cancel = (Button) mPincodeDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mPincodeDialog.show();
    }

    public void ShowSaudaDepoNameDialog() {

        final ArrayList<BranchMasterDetails> branchMasterDetailsList = mAceDnsDatabase
                .getSaudaRDSListForOrder(Constants.selectedCustomer.getCustomerCode(), "");

        if (branchMasterDetailsList.size() > 1) {
            final Dialog mDialogDepotName = new Dialog(mContext,
                    R.style.PauseDialog);
            mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogDepotName.setContentView(R.layout.select_from_list);
            mDialogDepotName.setCancelable(false);

            TextView title = (TextView) mDialogDepotName.findViewById(R.id.title);
            title.setText("Please select a Depot");
            ListView dialogList = (ListView) mDialogDepotName.findViewById(R.id.list);

            BranchAdapter branchadapter = new BranchAdapter(mContext,
                    R.layout.route_list_child, branchMasterDetailsList);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mDialogDepotName.cancel();
                    Constants.selectedBranch = branchMasterDetailsList.get(arg2);

                }
            });

            Button cancel = (Button) mDialogDepotName.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            mDialogDepotName.show();

        } else {
            if (branchMasterDetailsList.size() == 1) {
                Constants.selectedBranch = branchMasterDetailsList.get(0);
            } else {
                Toast.makeText(mContext, "No Depot found.", Toast.LENGTH_SHORT).show();
            }
        }

    }

    public void ShowPreviousOrderDialog() {
        String productcode = currentProductMasterObj.getProdCode();
        String customercode = Constants.selectedCustomer.getCustomerCode();
        String data = mAceDnsDatabase.GetPreviousOrederData(customercode, productcode);

        if (data.length() > 0) {
            final Dialog mPreviousOrderDialog = new Dialog(OrderFormActivityAlternateDesign.this,
                    R.style.PauseDialog);
            mPreviousOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mPreviousOrderDialog.setContentView(R.layout.activity_note);
            mPreviousOrderDialog.setCancelable(true);

            TextView textViewTitle = (TextView) mPreviousOrderDialog.findViewById(R.id.title);
            textViewTitle.setText("Previous order history");


            TextView textViewPreviousBooking1st = (TextView) mPreviousOrderDialog.findViewById(R.id.textView1st);
            TextView textViewPreviousBooking2nd = (TextView) mPreviousOrderDialog.findViewById(R.id.textView2nd);
            TextView textViewPreviousBooking3rd = (TextView) mPreviousOrderDialog.findViewById(R.id.textView3rd);
            textViewPreviousBooking1st.setText("");
            textViewPreviousBooking2nd.setText("");
            textViewPreviousBooking3rd.setText("");

            if (data.contains(",")) {
                String[] previousorderdata = data.split("\\,");
                for (int count = 0; count < previousorderdata.length; count++) {
                    if (count == 0) {
                        textViewPreviousBooking1st.setText("1st\n" + previousorderdata[count]);
                    }
                    if (count == 1) {
                        textViewPreviousBooking2nd.setText("2nd\n" + previousorderdata[count]);
                    }
                    if (count == 2) {
                        textViewPreviousBooking3rd.setText("3rd\n" + previousorderdata[count]);
                    }
                }
            } else {
                textViewPreviousBooking1st.setText("1st\n" + data);
            }

            Button buttonCancel = (Button) mPreviousOrderDialog.findViewById(R.id.buttonCancel);
            buttonCancel.setOnClickListener(new OnClickListener() {

                @Override
                public void onClick(View v) {
                    mPreviousOrderDialog.cancel();
                }
            });

            mPreviousOrderDialog.show();
        }
    }


    public void showOverallSchemes(View v) {

        ArrayList<String> SchemesOnProdGroup = new ArrayList<>();
        for (int i = 0; i < schemeListForProductGroups.size(); i++) {
            String textOne = "", textTwo = "", finalSchemeText = "";
//			ArrayList<SchemeFreebiesDetails> FreeProductsOnCurrentItem=schemeListForCurrentProducts.get(schemesOnCurrentProduct.get(i));
            SchemeFreebiesDetails item = schemeListForProductGroups.get(i);

            if (textOne.matches("")) {
                if (!item.getqty().matches("0")) {
                    textOne = " Buy " + item.getqty() + " " + item.getProductUomDisplayValue() + " " + item.getProdGroupDesc() + " get ";
                } else {
                    textOne = "Buy " + item.getamount() + " " + item.getProdGroupDesc() + " get ";
                }
            }

            if (!item.getfreebiesProdCode().trim().matches("") || !item.getfreebiesProdDesc().trim().matches("")) {
//                        textTwo=item.getFreebieQty()+" quantity of "+item.getfreebiesProdDesc()+" free.";
                if (textTwo.matches("")) {
                    textTwo = item.getFreebieQty() + " " + item.getfreebieUomDisplayValue() + " of " + item.getfreebiesProdDesc() + " free.";
                } else {
                    textTwo = textTwo + " Also " + item.getFreebieQty() + " " + item.getfreebieUomDisplayValue() + " of " + item.getfreebiesProdDesc() + " free.";
                }

            } else if (!item.getvaluePercent().matches("0")) {
//                            textTwo=item.getvaluePercent()+" % off.";
                if (textTwo.matches("")) {
                    textTwo = item.getvaluePercent() + " % off.";
                } else {
                    textTwo = textTwo + " Also " + item.getvaluePercent() + " % off.";
                }
            } else if (!item.getvalueAmount().matches("0")) {
//                            textTwo=item.getvalueAmount()+" amount off.";
                if (textTwo.matches("")) {
                    textTwo = item.getvalueAmount() + " amount off.";
                } else {
                    textTwo = textTwo + " Also " + item.getvalueAmount() + " amount off.";
                }
            } else {
//                            textTwo= item.getvalueAmount()+" quantity extra.";
                if (textTwo.matches("")) {
                    textTwo = item.getvalueAmount() + " quantity extra.";
                } else {
                    textTwo = textTwo + " Also " + item.getvalueAmount() + " quantity extra.";
                }
            }
//                    textTwo=textTwo+"\n";
//                    finalSchemeText=finalSchemeText+textOne+textTwo+" Scheme valid till "+Utils.changeDateFormat("yyyy-MM-dd","MM-dd-yyyy",item.getendDate())+".";
//                    finalSchemeText=finalSchemeText+textOne+textTwo+".".replace(">="," ").replace("<=","").replace("&&"," to ");

            finalSchemeText = textOne + textTwo.replace(">=", " ").replace("<=", "").replace("&&", " to ");
            finalSchemeText = finalSchemeText.replace("&&", " to ");
            finalSchemeText = finalSchemeText.replace(">=", " ");
            finalSchemeText = finalSchemeText.replace("<=", " ");
            finalSchemeText = finalSchemeText.trim().replace("  ", " ");
            SchemesOnProdGroup.add(finalSchemeText);
        }
        ShowProducGroupWiseSchemesDialog(SchemesOnProdGroup);

    }

    public void ShowProducGroupWiseSchemesDialog(final ArrayList<String> verticalList) {
        final CatalogueVerticalSelectionAdapter adapterCust = new CatalogueVerticalSelectionAdapter(mContext, R.layout.customer_list_child, verticalList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Schemes on groups. ");
        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(GONE);
        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
            }
        });

        Button back = (Button) mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                mDialogCustomer.cancel();
            }
        });
        Button addnewcustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setVisibility(GONE);


        mDialogCustomer.show();
    }

    public void changeSylText() {
        if (Constants.surveyFormDetailsObj.getFollow_up_menu().equalsIgnoreCase("yes"))
        {
            etPwd.setText("");
            etPwdH.setText("");
        }

    }

}
