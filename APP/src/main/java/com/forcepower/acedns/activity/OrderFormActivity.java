package com.forcepower.acedns.activity;

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
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnFocusChangeListener;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
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
import android.widget.TextView.OnEditorActionListener;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
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
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.currency;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.mOrderPriceValidationType;
import static com.forcepower.acedns.constants.Constants.orderFormDetailsObjFixed;

public class OrderFormActivity extends AceDnsParentActivity {

    /*
     * Layout Views
     */
    public static ImageView mImageViewHeaderLogo = null;

    public static TextView mTextViewTotalOrderValue = null;
    public static TextView mTextViewNoofOrder = null;
    public static TextView mTextViewUOM1 = null;

    public static TextView mTextViewCustomerName = null;
    public static TextView mTextViewRouteName = null;

    public static Button mButtonBack = null;
    public static Button mButtonNoOrder = null;
    public static Button mButtonAddtoCart = null;
    public static Button mButtonCheckOut = null;

    public boolean isTD = true;
    public boolean isPremium = false;

    public String uom1 = "", uom2 = "", selecteduom = "";

    EditText edAvailCrdt, edAmount;
    LinearLayout bodyLayout, optionLayout, customerLayout, creditLimLayout,
            filterLayout, qty_mrpLayout, stk_tdLayout, amountLayout,
            tdNewLayout;
    /*
     * Custom Views
     */
    Spinner mrpSpinner;
    EditText edQty, edSaleRate, edClsngStk, edTd, refPrice;
    Dialog customerListDialog, customerAddDialog, grpDialog, subGrpDialog,
            brandDialog, masterDialog, mrpDialog, routeDialog,
            infoDialog, routePlanListDialog, uomDialog;

    RadioButton radioButton;

    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
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
    ArrayList<RouteDetails> routeList;

    ArrayList<Button> filterButtonList;
    ProductMasterDetails currentProductMasterObj;
    int filterNo;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected,
            lastProductSelected = false;
    ProductMasterAdapter prodAdapter;
    ProductMasterWithQtyInputAdapter ProductMasterWithQtyInputAdapterObject;
    ProductGrpAdapter groupAdapter;
    ProductSubGrpAdapter subGroupAdapter;
    ProductBrandAdapter brandAdapter;
    CustomerAdapter adapterCust;
    String lastStr = "";
    ArrayList<CustomerDetails> tempCustomerList;
    ArrayList<ProductMasterDetails> tempProductList;
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
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    RouteDetails selectedRoute;
    boolean rpeatedProductEntry = false;
    TextView txtTD;
    String[] values;
    Boolean isNavigatedFromSalesOption = false;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_form);
        RegisterActivities.registerActivity(this);

        carryInSales = getIntent().getBooleanExtra("CARRY_IN", false);
        if (getIntent().hasExtra("salesOption")) {
            isNavigatedFromSalesOption = true;
        }
        Constants.isFromConfirmationActivity = false;
        if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes"))//this is supposed to be a temp solution
        {
            filterNo = 1;
        } else {
            filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        }
        mContext = OrderFormActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Constants.selectedProductMasterList = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();

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

        if (Constants.nickName.contains("RKBK")) {
            ShowRoute();
        }

        DrawLayout();

        formatter = new SimpleDateFormat("dd-MM-yyyy");


        orderDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                OrderFormActivity.this.runOnUiThread(new Runnable() {
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
                                        Constants.mVerticalValue = values[0];
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
        cancel.setVisibility(View.GONE);
        payTypeDialog.show();
    }

    /**
     * This method is called to fetch route from local DB.
     */
    public void ShowRoute() {
        ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteList();
        int numberOfRoutes = routeList.size();
        if (numberOfRoutes == 1) {
            selectedRoute = routeList.get(0);

            routeCode = selectedRoute.getRouteCode();
            routeName = selectedRoute.getRouteName();
            mTextViewRouteName.setText("Route: " + routeName);
            customerList = mAceDnsDatabase
                    .getCustomerListByRoute(routeCode);
            if (customerList.size() > 0) {
                tempCustomerList = new ArrayList<>();
                reInitialiseCustomerList();
                adapterCust = new CustomerAdapter(OrderFormActivity.this,
                        R.layout.customer_list_child, tempCustomerList);
                showChooseCustomerDialog();
            } else {
                Toast.makeText(mContext, "No existing customer found.",
                        2000).show();
            }

        } else if (numberOfRoutes > 1) {
            showRouteListDialog(routeList);
        } else {
            Toast.makeText(mContext, "No route found, you can not use this feature.", Toast.LENGTH_SHORT).show();
            finish();
        }
    }

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
            customerLayout.setVisibility(View.VISIBLE);
            if (Constants.selectedProductMasterList.size() % 2 == 0) {
                customerLayout.setBackgroundColor(Color.YELLOW);
            } else {
                customerLayout.setBackgroundColor(Color.GREEN);
            }
            mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());

            CalculateTotal();
        }
        if (edQty != null) {
            edQty.setText("");
        }

        if (edTd != null && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("customer wise")) {
            edTd.setText("");
        }
        if (edSaleRate != null) {
            edSaleRate.setText("");
        }

        if (mrpSpinner != null) {
            String[] tempSpinnerArray = new String[0];
            spinnerAdapter = new ArrayAdapter(OrderFormActivity.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
            mrpSpinner.setAdapter(spinnerAdapter);
        }
        if (mImageViewHeaderLogo != null) {
            if (Constants.logoBmp != null) {
                mImageViewHeaderLogo.setVisibility(View.VISIBLE);
                mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
            } else {
                mImageViewHeaderLogo.setVisibility(View.GONE);
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

    /*
     * ::::::::::::::::::::::::::::::::::: VIEW RELATED OPERATIONS
     * ::::::::::::::::::::::::::::::::::::::::::::::
     */

    public void InitializeView() {
        amountLayout = (LinearLayout) findViewById(R.id.amount_layout);
        edAmount = (EditText) findViewById(R.id.ed_amt);
        edAmount.setTextSize(15);
        if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase("yes")) {
            amountLayout.setVisibility(View.VISIBLE);
            if (!Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
                edSaleRate.setEnabled(false);
            }
        } else {
            amountLayout.setVisibility(View.GONE);
        }
        edAmount.addTextChangedListener(new TextWatcher() {
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
                if (edSaleRate != null) {
                    if (s.length() > 0) {
                        edSaleRate.setEnabled(false);
                    } else {
                        edSaleRate.setEnabled(true);
                    }
                }
            }
        });

        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        bodyLayout = (LinearLayout) findViewById(R.id.body_layout);
        optionLayout = (LinearLayout) findViewById(R.id.option_layout);
        customerLayout = (LinearLayout) findViewById(R.id.customer_layout);
        if (Constants.selectedProductMasterList == null
                || Constants.selectedProductMasterList.size() == 0) {
            customerLayout.setVisibility(View.INVISIBLE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText("Ver~" + Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        creditLimLayout = (LinearLayout) findViewById(R.id.crdt_layout);
        filterLayout = (LinearLayout) findViewById(R.id.filter_layout);
        qty_mrpLayout = (LinearLayout) findViewById(R.id.qty_mrp_layout);
        stk_tdLayout = (LinearLayout) findViewById(R.id.stk_td_layout);
        tdNewLayout = (LinearLayout) findViewById(R.id.td_layout);

        mTextViewTotalOrderValue = (TextView) findViewById(R.id.txt_total);
        mTextViewNoofOrder = (TextView) findViewById(R.id.txt_order_count);
        mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
        CalculateTotal();
        edAvailCrdt = (EditText) findViewById(R.id.ed_avail_crdt);

        mButtonAddtoCart = (Button) findViewById(R.id.btn_continue);
        mButtonAddtoCart.setOnClickListener(OrderFormActivity.this);
        mButtonAddtoCart.setTag(101);


        mButtonCheckOut = (Button) findViewById(R.id.btn_order_form);
        mButtonCheckOut.setTag(102);
        mButtonCheckOut.setOnClickListener(OrderFormActivity.this);

        mButtonNoOrder = (Button) findViewById(R.id.no_ordr);
        mButtonNoOrder.setTag(104);
        mButtonNoOrder.setOnClickListener(OrderFormActivity.this);
        mButtonNoOrder.setEnabled(true);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setTag(105);
        mButtonBack.setOnClickListener(OrderFormActivity.this);

        mTextViewCustomerName = (TextView) findViewById(R.id.textViewCustomername);
        mTextViewRouteName = (TextView) findViewById(R.id.textViewRoute);
        if (!Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
            if (!Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes")
                    || !Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes") || !Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan")) {
                String customerName = Constants.selectedCustomer.getCustomerName();
                mTextViewCustomerName.setText("Customer : " + customerName);

                mTextViewRouteName.setText("Route : " + Constants.selectedCustomer.getRouteName());

            }
        }

    }

    public void DrawLayout() {
        drawFilterLayout();
        drawQtyMrpLayout();
        drawStkTdVatLayout();
        enableDisableLayout(false);
        if (mOrderPriceValidationType.matches("DOPS")) {
            mButtonAddtoCart.setVisibility(View.GONE);
            qty_mrpLayout.setVisibility(View.INVISIBLE);
            stk_tdLayout.setVisibility(View.INVISIBLE);
            tdNewLayout.setVisibility(View.INVISIBLE);
        }

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
                        LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayoutParams.setMargins(0, 0, 0, 5);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);


                buttonParams.gravity = Gravity.CENTER_VERTICAL;
                filterButton.setLayoutParams(buttonParams);
                filterButton.setTag(ii);
                filterButton.setHorizontallyScrolling(true);
                filterButton.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
                filterButton.setText(filterList.get(ii));
                filterButton.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
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

    public void drawQtyMrpLayout() {

        LinearLayout qtyLayout = new LinearLayout(OrderFormActivity.this);
        qtyLayout.setOrientation(LinearLayout.HORIZONTAL);
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(0,
                LayoutParams.WRAP_CONTENT, 1);
        qtyLayout.setLayoutParams(params);

        TextView txtQty = new TextView(mContext);
        txtQty.setText("Qty    : ");
        txtQty.setTextColor(Color.parseColor("#003399"));


        edQty = new EditText(mContext);
        edQty.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edQty.setEms(5);
        edQty.setTextSize(15);
        edQty.setGravity(Gravity.RIGHT);
        edQty.setImeOptions(EditorInfo.IME_ACTION_DONE);
        edQty.setTag("qty");
        edQty.setOnFocusChangeListener(new OnFocusChangeListener() {
            public void onFocusChange(View v, boolean hasFocus) {
                if (hasFocus) {
                    ((EditText) v).setText("");
                } else {
                }
            }
        });

        edQty.setOnEditorActionListener(new OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId,
                                          KeyEvent event) {
                if (event == null) {
                    if (actionId == EditorInfo.IME_ACTION_DONE) {
                        // edTd.requestFocus();
                        InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                        imm.hideSoftInputFromWindow(edQty.getWindowToken(), 0);
                        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        return true;
                    }
                }
                return false;
            }
        });

        qtyLayout.addView(txtQty);
        qtyLayout.addView(edQty);

        mTextViewUOM1 = new TextView(mContext);
        mTextViewUOM1.setText("");
        mTextViewUOM1.setTextColor(Color.parseColor("#000000"));
        mTextViewUOM1.setTextSize(10);
        qtyLayout.addView(mTextViewUOM1);
        qty_mrpLayout.addView(qtyLayout);

        if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {

            if (edAmount != null) {
                edAmount.setEnabled(false);
            }
            LinearLayout mrpLayout = new LinearLayout(OrderFormActivity.this);
            mrpLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(
                    0, LayoutParams.WRAP_CONTENT, 1);
            mrpLayout.setLayoutParams(params1);

            TextView txtMrp = new TextView(mContext);
            txtMrp.setText(" MRP : ");
            txtMrp.setTextColor(Color.parseColor("#003399"));
            mrpLayout.addView(txtMrp);

            mrpSpinner = new Spinner(mContext);
            mrpSpinner.setPadding(30, 3, 0, 3);
            mrpSpinner.setBackgroundResource(R.drawable.spinner_bg);
            mrpSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> arg0, View arg1,
                                           int arg2, long arg3) {
                    mrpSelected = arg2;
                }

                @Override
                public void onNothingSelected(AdapterView<?> arg0) {
                }
            });

            mrpLayout.addView(mrpSpinner);
            qty_mrpLayout.addView(mrpLayout);
        } else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
            LinearLayout mrpLayout = new LinearLayout(OrderFormActivity.this);
            mrpLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(
                    0, LayoutParams.WRAP_CONTENT, 1);
            mrpLayout.setLayoutParams(params1);

            TextView txtMrp = new TextView(mContext);
            txtMrp.setText(" Sale Rate : ");
            txtMrp.setTextColor(Color.parseColor("#003399"));
            mrpLayout.addView(txtMrp);
            if (Constants.orderFormDetailsObj.getSaleRateDrpdwn()
                    .equalsIgnoreCase("input")) {
                edSaleRate = new EditText(mContext);
                edSaleRate.setInputType(InputType.TYPE_CLASS_NUMBER
                        | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                edSaleRate.setEms(6);
                edSaleRate.setTextSize(15);
                edSaleRate.setImeOptions(EditorInfo.IME_ACTION_DONE);
                edSaleRate.setGravity(Gravity.RIGHT);
                edSaleRate.setTag("mrp");
                mrpLayout.addView(edSaleRate);
                qty_mrpLayout.addView(mrpLayout);

                if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase("yes")) {
                    edSaleRate.addTextChangedListener(new TextWatcher() {
                        @Override
                        public void onTextChanged(CharSequence arg0, int arg1,
                                                  int arg2, int arg3) {
                        }

                        @Override
                        public void beforeTextChanged(CharSequence arg0,
                                                      int arg1, int arg2, int arg3) {
                        }

                        @Override
                        public void afterTextChanged(Editable s) {
                            if (edAmount != null) {
                                if (s.length() > 0) {
                                    edAmount.setEnabled(false);
                                } else {
                                    edAmount.setEnabled(true);
                                }
                            }
                        }
                    });
                }
            }
            else
            {
                if (edAmount != null)
                {
                    edAmount.setEnabled(false);
                }
                mrpSpinner = new Spinner(mContext);
                mrpSpinner.setPadding(30, 3, 0, 3);
                mrpSpinner.setBackgroundResource(R.drawable.spinner_bg);
                mrpSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
                    @Override
                    public void onItemSelected(AdapterView<?> arg0,
                                               View arg1, int arg2, long arg3) {
                        mrpSelected = arg2;
                    }

                    @Override
                    public void onNothingSelected(AdapterView<?> arg0) {
                    }
                });

                mrpLayout.addView(mrpSpinner);
                qty_mrpLayout.addView(mrpLayout);
            }
        }
    }

    public void drawStkTdVatLayout() {
        if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes") || carryInSales)
        {

            LinearLayout stkLayout = new LinearLayout(OrderFormActivity.this);
            stkLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(
                    0, LayoutParams.WRAP_CONTENT, 1);
            stkLayout.setLayoutParams(params1);

            TextView txtStk = new TextView(mContext);
            txtStk.setText("Cl Stk : ");
            txtStk.setTextColor(Color.parseColor("#003399"));
            stkLayout.addView(txtStk);

            edClsngStk = new EditText(mContext);
            edClsngStk.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            edClsngStk.setEnabled(false);
            edClsngStk.setEms(5);
            edClsngStk.setTextSize(15);
            edClsngStk.setGravity(Gravity.RIGHT);
            edClsngStk.setTag("stk");
            edClsngStk.setOnFocusChangeListener(new OnFocusChangeListener() {
                public void onFocusChange(View v, boolean hasFocus) {
                    if (hasFocus) {
                        ((EditText) v).setText("");
                    } else {
                    }
                }
            });
            stkLayout.addView(edClsngStk);
            stk_tdLayout.addView(stkLayout);
        }

        if (mOrderPriceValidationType.matches("SPA")) {
            LinearLayout.LayoutParams parentparams = new LinearLayout.LayoutParams(
                    LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
            LinearLayout.LayoutParams childparams = new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT);
            LinearLayout.LayoutParams childparams2 = new LinearLayout.LayoutParams(200, ViewGroup.LayoutParams.WRAP_CONTENT);

            LinearLayout tdLayout = new LinearLayout(OrderFormActivity.this);
            tdNewLayout.setOrientation(LinearLayout.VERTICAL);
            tdNewLayout.setLayoutParams(parentparams);
            tdLayout.setOrientation(LinearLayout.HORIZONTAL);
            tdLayout.setLayoutParams(childparams);
            txtTD = new TextView(mContext);
            txtTD.setText("Rate  : ");
            txtTD.setTextColor(Color.parseColor("#003399"));
            tdLayout.addView(txtTD);

            refPrice = new EditText(mContext);
            refPrice.setLayoutParams(childparams2);
            refPrice.setEnabled(false);
            refPrice.setTextSize(15);
            refPrice.setGravity(Gravity.RIGHT);
            tdLayout.addView(refPrice);
            tdNewLayout.addView(tdLayout);
        }

        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase(
                "yes")
                && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase(
                "order value wise")) {

            if (Constants.orderFormDetailsObj.getPremium().equalsIgnoreCase(
                    "yes")) {
                LinearLayout.LayoutParams parentparams = new LinearLayout.LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
                tdNewLayout.setOrientation(LinearLayout.VERTICAL);
                tdNewLayout.setLayoutParams(parentparams);

                LinearLayout.LayoutParams childparams = new LinearLayout.LayoutParams(
                        LayoutParams.MATCH_PARENT, 0, 1);

                LinearLayout radiaoButtonLayout = new LinearLayout(OrderFormActivity.this);
                radiaoButtonLayout.setOrientation(LinearLayout.HORIZONTAL);
                radiaoButtonLayout.setLayoutParams(childparams);

                RadioGroup rgp = new RadioGroup(this);
                rgp.setOrientation(RadioGroup.HORIZONTAL);
                RadioGroup.LayoutParams rprms;

                for (int i = 0; i < 2; i++) {
                    radioButton = new RadioButton(this);
                    if (i == 0) {
                        radioButton.setText("TD");
                        radioButton.setTag(150);
                        radioButton.setTextSize(10);

                        //radioButton.setChecked(true);
                    } else {
                        radioButton.setText("Premium");
                        radioButton.setTag(151);
                        radioButton.setTextSize(10);
                    }
                    radioButton.setOnClickListener(this);
                    radioButton.setTextColor(Color.parseColor("#003399"));
                    rprms = new RadioGroup.LayoutParams(
                            LayoutParams.MATCH_PARENT,
                            LayoutParams.WRAP_CONTENT, 100f);
                    rgp.addView(radioButton, rprms);
                }

                rgp
                        .setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                            public void onCheckedChanged(RadioGroup group, int checkedId) {
                                //radioButton.setChecked(false);
                                //RadioButton radioSelection = (RadioButton) findViewById(checkedId);
                                radioButton = (RadioButton) findViewById(checkedId);
                                if (radioButton.getText().equals("TD")) {
                                    txtTD.setText("TD");
                                    isTD = true;
                                    isPremium = false;
                                } else {
                                    txtTD.setText("Premium");
                                    isTD = false;
                                    isPremium = true;
                                }
                            }
                        });

                radiaoButtonLayout.addView(rgp);
                tdNewLayout.addView(radiaoButtonLayout);

                LinearLayout tdinputLayout = new LinearLayout(OrderFormActivity.this);
                tdinputLayout.setOrientation(LinearLayout.VERTICAL);
                tdinputLayout.setLayoutParams(childparams);

                LinearLayout tdLayout = new LinearLayout(OrderFormActivity.this);
                tdLayout.setOrientation(LinearLayout.HORIZONTAL);
                tdLayout.setLayoutParams(childparams);

                txtTD = new TextView(mContext);
                txtTD.setText("TD  : ");
                txtTD.setTextColor(Color.parseColor("#003399"));
                tdLayout.addView(txtTD);

                edTd = new EditText(mContext);
                edTd.setInputType(InputType.TYPE_CLASS_NUMBER
                        | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                edTd.setImeOptions(EditorInfo.IME_ACTION_DONE);
                edTd.setPadding(4, 2, 4, 2);
                edTd.setEms(5);
                edTd.setTextSize(15);
                edTd.setGravity(Gravity.RIGHT);
                edTd.setTag("td");
                edTd.setOnFocusChangeListener(new OnFocusChangeListener() {
                    public void onFocusChange(View v, boolean hasFocus) {
                        if (hasFocus) {
                            ((EditText) v).setText("");
                        } else {
                        }
                    }
                });
                edTd.setOnEditorActionListener(new OnEditorActionListener() {
                    @Override
                    public boolean onEditorAction(TextView v, int actionId,
                                                  KeyEvent event) {
                        if (event == null) {
                            if (actionId == EditorInfo.IME_ACTION_DONE) {
                                // edTd.requestFocus();
                                InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                                imm.hideSoftInputFromWindow(edTd.getWindowToken(),
                                        0);
                                getWindow()
                                        .setSoftInputMode(
                                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                                return true;
                            }
                        }
                        return false;
                    }
                });
                tdLayout.addView(edTd);

                TextView txtPercnt = new TextView(mContext);
                if (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("percent")) {
                    txtPercnt.setText("%");
                } else {
                    txtPercnt.setText("");
                }
                txtPercnt.setTextColor(Color.parseColor("#003399"));
                tdLayout.addView(txtPercnt);

                //tdinputLayout.addView(tdLayout);
                tdNewLayout.addView(tdLayout);


            } else {
                LinearLayout tdLayout = new LinearLayout(OrderFormActivity.this);
                tdLayout.setOrientation(LinearLayout.HORIZONTAL);
                LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(
                        0, LayoutParams.WRAP_CONTENT, 1);
                tdLayout.setLayoutParams(params1);

                TextView txtTD = new TextView(mContext);
                txtTD.setText("TD  : ");
                txtTD.setTextColor(Color.parseColor("#003399"));
                tdLayout.addView(txtTD);

                edTd = new EditText(mContext);
                edTd.setInputType(InputType.TYPE_CLASS_NUMBER
                        | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                edTd.setImeOptions(EditorInfo.IME_ACTION_DONE);
                edTd.setPadding(4, 2, 4, 2);
                edTd.setEms(5);
                edTd.setTextSize(15);
                edTd.setGravity(Gravity.RIGHT);
                edTd.setTag("td");
                edTd.setOnFocusChangeListener(new OnFocusChangeListener() {
                    public void onFocusChange(View v, boolean hasFocus) {
                        if (hasFocus) {
                            ((EditText) v).setText("");
                        } else {
                        }
                    }
                });
                edTd.setOnEditorActionListener(new OnEditorActionListener() {
                    @Override
                    public boolean onEditorAction(TextView v, int actionId,
                                                  KeyEvent event) {
                        if (event == null) {
                            if (actionId == EditorInfo.IME_ACTION_DONE) {
                                // edTd.requestFocus();
                                InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                                imm.hideSoftInputFromWindow(edTd.getWindowToken(),
                                        0);
                                getWindow()
                                        .setSoftInputMode(
                                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                                return true;
                            }
                        }
                        return false;
                    }
                });
                tdLayout.addView(edTd);

                TextView txtPercnt = new TextView(mContext);
                if (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("percent")) {
                    txtPercnt.setText("%");
                } else {
                    txtPercnt.setText("");
                }
                txtPercnt.setTextColor(Color.parseColor("#003399"));
                tdLayout.addView(txtPercnt);
                tdNewLayout.addView(tdLayout);
            }
        }

        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")
                && (Constants.orderFormDetailsObj.getVatCalcOn()
                .equalsIgnoreCase("sku"))) {
            LinearLayout tdLayout = new LinearLayout(OrderFormActivity.this);
            tdLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(
                    0, LayoutParams.WRAP_CONTENT, 1);
            tdLayout.setLayoutParams(params1);

            TextView txtTD = new TextView(mContext);
            txtTD.setText("VAT : ");
            txtTD.setTextColor(Color.parseColor("#003399"));
            tdLayout.addView(txtTD);

            edTd = new EditText(mContext);
            edTd.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            edTd.setImeOptions(EditorInfo.IME_ACTION_DONE);
            edTd.setPadding(4, 2, 4, 2);
            edTd.setEms(5);
            edTd.setGravity(Gravity.RIGHT);
            edTd.setTag("vat");
            edTd.setOnFocusChangeListener(new OnFocusChangeListener() {
                public void onFocusChange(View v, boolean hasFocus) {
                    if (hasFocus) {
                        ((EditText) v).setText("");
                    }
                }
            });
            edTd.setOnEditorActionListener(new OnEditorActionListener() {
                @Override
                public boolean onEditorAction(TextView v, int actionId,
                                              KeyEvent event) {
                    if (event == null) {
                        if (actionId == EditorInfo.IME_ACTION_DONE) {
                            // edTd.requestFocus();
                            InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                            imm.hideSoftInputFromWindow(edTd.getWindowToken(),
                                    0);
                            getWindow()
                                    .setSoftInputMode(
                                            WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                            return true;
                        }
                    }
                    return false;
                }
            });
            tdLayout.addView(edTd);

            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase(
                    "percentage")) {
                TextView txtPercnt = new TextView(mContext);
                txtPercnt.setText("%");
                txtPercnt.setTextColor(Color.parseColor("#003399"));
                tdLayout.addView(txtPercnt);
            }

            tdNewLayout.addView(tdLayout);
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
        qty_mrpLayout.setEnabled(isShown);
        stk_tdLayout.setEnabled(isShown);
    }

    public void manageViewAvailableCredit(CustomerDetails customerObj) {
        if (Constants.orderFormDetailsObj.getCreditLimit().equalsIgnoreCase(
                "yes")) {
            if (customerObj.getCreditLimit() != null) {
                creditLimLayout.setVisibility(View.VISIBLE);
                edAvailCrdt.setText(customerObj.getCreditLimit());
            }
        }
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
            spinnerAdapter = new ArrayAdapter(OrderFormActivity.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
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
            ArrayAdapter adapter = new ArrayAdapter(OrderFormActivity.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
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
                        Toast.makeText(OrderFormActivity.this,
                                "Please select the parent category", 2000);
                    }
                    break;
                case 3:
                    lastProdPos = 0;
                    filterButtonList.get(3).setEnabled(true);
                    lastProductSelected = false;
                    if (selectedSubGrp != null) {
                        prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                    } else {
                        Toast.makeText(OrderFormActivity.this,
                                "Please select the parent category", 2000);
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
                                Toast.makeText(OrderFormActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(OrderFormActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                prepareOrderData(4, selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(OrderFormActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                    }
            }

        }
        if (clkdView == mButtonAddtoCart) {
            addToCartProcess();

        }
        if (clkdView == mButtonCheckOut) {
            checkoutProcess();
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
            Intent intent = new Intent(OrderFormActivity.this,
                    OrderConfirmationActivity.class);
            intent.putExtra("CARRY_IN", carryInSales);
            if (isNavigatedFromSalesOption) {
                intent.putExtra("salesOption", true);
            }
            startActivity(intent);
        } else {
            Toast.makeText(OrderFormActivity.this,
                    "Please select a product", Toast.LENGTH_LONG).show();
        }
    }

    private void addToCartProcess() {
        // Continue Button
        if (currentProductMasterObj != null) {
            if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
                currentProductMasterObj.setUom1(uom1);
                currentProductMasterObj.setUom2(uom2);
                currentProductMasterObj.setUomSelectedForProduct(selecteduom);
            } else {
                currentProductMasterObj.setUomSelectedForProduct(uom1);
            }

            boolean qty_status = true, mrp_status = true, tdStatus = true, vatStatus = true;
            if (mOrderPriceValidationType.matches("DOPS")) {
                //do nothing for now
            } else if (edQty.getText().toString() != null
                    && edQty.getText().toString().length() > 0
                    && !edQty.getText().toString().equalsIgnoreCase("0")
                    && !edQty.getText().toString().equalsIgnoreCase(".")
                    && Double.parseDouble(edQty.getText().toString()) != 0) {
                if (carryInSales) {
                    if ((Double.parseDouble(edQty.getText().toString()) <= Double
                            .parseDouble(edClsngStk.getText().toString()))) {
                        currentProductMasterObj.setQty(edQty.getText()
                                .toString());
                        qty_status = true;
                    } else {
                        qty_status = false;
                    }
                } else {
                    currentProductMasterObj.setQty(edQty.getText()
                            .toString());
                    qty_status = true;
                }
                if (currentProductMasterObj.getSelectedUOM() != -1) {
                    if (currentProductMasterObj.getSelectedUOM() == 0) {
                        double finalQty = Double.parseDouble(currentProductMasterObj.getQty())
                                * Double.parseDouble(currentProductMasterObj.getConversionFactor());
                        if (finalQty != Math.floor(finalQty)) {
                            qty_status = false;
                        }
                    }
                }
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
                                Toast.makeText(OrderFormActivity.this,
                                        "Trade Discount cannot be more than 100 %",
                                        2000).show();
                            }

                        }
                        if (isTD == false && isPremium == true) {
                            if (Double.parseDouble(premium) <= 100) {
                                currentProductMasterObj.setPremium(premium);
                                currentProductMasterObj.setIsTradeDiscount(false);
                            } else {
                                Toast.makeText(OrderFormActivity.this,
                                        "Premium cannot be more than 100 %",
                                        2000).show();
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
                customerLayout.setVisibility(View.VISIBLE);
                if (Constants.selectedProductMasterList.size() % 2 == 0) {
                    customerLayout.setBackgroundColor(Color.YELLOW);
                } else {
                    customerLayout.setBackgroundColor(Color.GREEN);
                }
                Constants.selectedProductMasterList
                        .add(currentProductMasterObj);
                mTextViewNoofOrder.setText(""
                        + Constants.selectedProductMasterList.size());
                CalculateTotal();

                if (carryInSales) {
                    mAceDnsTransactionDatabase
                            .reduceClStkProductWise(currentProductMasterObj);
                }
                if (!mOrderPriceValidationType.matches("DOPS")) {
                    Toast.makeText(OrderFormActivity.this, "Product has been added to cart.", Toast.LENGTH_LONG).show();

                }

                currentProductMasterObj = null;
                if (mrpSpinner != null) {
                    String[] tempSpinnerArray = new String[0];
                    spinnerAdapter = new ArrayAdapter(
                            OrderFormActivity.this,
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
                    Toast.makeText(OrderFormActivity.this, "Please provide valid quantity.", Toast.LENGTH_LONG).show();
                    mTextViewUOM1.setText(selecteduom);
                } else if (false == mrp_status) {
                    if (Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown") ||
                            Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                        Toast.makeText(OrderFormActivity.this, "Error in mrp data. Please contact admin.", Toast.LENGTH_LONG).show();
                    } else {
                        Toast.makeText(OrderFormActivity.this, "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                    }
                } else {
                    Toast.makeText(OrderFormActivity.this, "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                }
            }
        } else {
            Toast.makeText(OrderFormActivity.this, "Please select a product", Toast.LENGTH_LONG).show();
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
        if (edSaleRate != null) {
            edSaleRate.setText("");
        }
    }

    /*
     * :::::::::::::::::::::::::::::: CREATING DIFFERENT DIALOGs
     * :::::::::::::::::::::::::::::::::
     */

    public void showChooseCustomerDialog() {
        customerListDialog = new Dialog(OrderFormActivity.this,
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

                mTextViewCustomerName.setText("Customer: " + customerList.get(chosenCustPos).getCustomerName());
                Constants.selectedCustomer = customerList.get(chosenCustPos);
                enableDisableLayout(true);
                mButtonNoOrder.setEnabled(true);
                manageViewAvailableCredit(customerList.get(chosenCustPos));
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
        addCustomer.setVisibility(View.GONE);
        customerListDialog.show();
    }


    public void ShowNoOrderDialog() {
        final Dialog noOrderDialog = new Dialog(OrderFormActivity.this, R.style.PauseDialog);
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
                            new TRANS_PendingRoutePlanBeforeOtherTxn(OrderFormActivity.this, "ORDER").execute();
                        } else {
                            new TRANS_SubmitOrderTask(OrderFormActivity.this, true).execute();
                        }
                    } else {
                        new TRANS_SubmitOrderTask(OrderFormActivity.this, true).execute();
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
            groupAdapter = new ProductGrpAdapter(OrderFormActivity.this,
                    R.layout.product_list_child, tempProductGroupList);
            grpDialog = new Dialog(OrderFormActivity.this, R.style.PauseDialog);
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
            Toast.makeText(OrderFormActivity.this,
                    "There are no items left.Please submit order.", 2000)
                    .show();
        }
    }

    public void showSubGrpListDialog() {
        if (productSubGroupList.size() > 0) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGroupAdapter = new ProductSubGrpAdapter(OrderFormActivity.this,
                    R.layout.product_list_child, tempProductSubGroupList);
            subGrpDialog = new Dialog(OrderFormActivity.this,
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
                    OrderFormActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    public void showBrandListDialog() {
        if (productBrandList.size() > 0) {
            if (productBrandList.size() == 1) {
                lastBrandSelected = true;
            }
            brandAdapter = new ProductBrandAdapter(OrderFormActivity.this,
                    R.layout.product_list_child, tempProductBrandList);
            brandDialog = new Dialog(OrderFormActivity.this,
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
                    OrderFormActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
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
                ProductMasterWithQtyInputAdapterObject = new ProductMasterWithQtyInputAdapter(OrderFormActivity.this, R.layout.product_list_item_with_quantity_input, tempProductList);
            } else {
                prodAdapter = new ProductMasterAdapter(OrderFormActivity.this, R.layout.product_list_child, tempProductList);
            }


            masterDialog = new Dialog(OrderFormActivity.this, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please select a product");
            EditText searchText = (EditText) masterDialog.findViewById(R.id.autoCompleteTextView1);

            if (mOrderPriceValidationType.matches("DOPS")) {
                View list_header_item_planwise_input_screen = masterDialog.findViewById(R.id.list_header_item_planwise_input_screen);
                list_header_item_planwise_input_screen.setVisibility(View.VISIBLE);
                if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
                    TextView etProdQty = (TextView) masterDialog.findViewById(R.id.etProdQty);
                    etProdQty.setVisibility(View.VISIBLE);
                    TextView tv_last_month_purchase = (TextView) masterDialog.findViewById(R.id.tv_last_month_purchase);
                    TextView tv_order_plan = (TextView) masterDialog.findViewById(R.id.tv_order_plan);
                    tv_last_month_purchase.setVisibility(View.GONE);
                    tv_order_plan.setVisibility(View.GONE);
                } else {
                    TextView etProdRate = (TextView) masterDialog.findViewById(R.id.etProdRate);
                    etProdRate.setVisibility(View.GONE);
                }
                searchText.setVisibility(View.GONE);
                edQty.setVisibility(View.GONE);
                if (Constants.productDetailsObj.getFocusProduct().equalsIgnoreCase("yes")) {
//					title.setText(Html.fromHtml("Please provide valid inputs for each <font color='#F58322'> focused </font> product"));
                    title.setVisibility(View.GONE);
                    ImageView ivSideImage = (ImageView) masterDialog.findViewById(R.id.imageView1);
                    ivSideImage.setVisibility(View.GONE);
                    ImageView image_cancel = (ImageView) masterDialog.findViewById(R.id.image_cancel);
                    LinearLayout applogoLayout = (LinearLayout) masterDialog.findViewById(R.id.applogoLayout);
                    applogoLayout.setVisibility(View.VISIBLE);
                    image_cancel.setVisibility(View.VISIBLE);
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
                btn_addToCart.setVisibility(View.VISIBLE);
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
                                if (Constants.orderFormDetailsObj.getTradeDiscount()
                                        .equalsIgnoreCase("yes")
                                        && Constants.orderFormDetailsObj.getTdType()
                                        .equalsIgnoreCase("sku wise") && Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes")) {
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
                                } else {
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
                    OrderFormActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            filterButtonList.get(3).setText("");
        }
    }

    public void showUomListDialogForMultipleUom() {
        final Dialog payTypeDialog = new Dialog(OrderFormActivity.this, R.style.PauseDialog);
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
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                payTypeDialog.cancel();
            }
        });
        payTypeDialog.show();
    }

    public void showRouteListDialog(final ArrayList<RouteDetails> routeList) {
        routeDialog = new Dialog(OrderFormActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        RouteAdapter adapter1 = new RouteAdapter(OrderFormActivity.this,
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
                    adapterCust = new CustomerAdapter(OrderFormActivity.this,
                            R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found.",
                            2000).show();
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
        create_route.setVisibility(View.GONE);
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
            if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise")
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

                    if (Constants.orderFormDetailsObj.getTdCalc().equalsIgnoreCase("amount")) {

                        productamount = (qty * (mrp - discount)) + (premium * qty);
                        amount = amount + productamount;

                    } else {
                        productamount = ((mrp * qty) - (mrp * qty * discount / 100)) + (premium * qty);
                        amount = amount
                                + productamount;
                    }
                    Constants.selectedProductMasterList.get(ii).setAmount(String.valueOf(productamount));


                }
            } else if (Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise") && Constants.productDetailsObj.getProductQtyWiseTD().equalsIgnoreCase("yes")) {
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
                        productBrandList = mAceDnsDatabase.getProductBrandList(param, carryInSales);
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
                    case 5:
                        int max = 0;
                        max = mAceDnsDatabase.GetVerticalValue();
                        values = new String[max];
                        if (max > 0) {
                            for (int i = 0; i < Constants.mVerticalValueList.length; i++) {
                                values[i] = Constants.mVerticalValueList[i];
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
                OrderFormActivity.this, R.layout.simple_list_child, uomList);
        uomDialog = new Dialog(OrderFormActivity.this, R.style.PauseDialog);
        uomDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        uomDialog.setContentView(R.layout.choose_customer_search);
        uomDialog.setCancelable(false);
        TextView title = (TextView) uomDialog.findViewById(R.id.title);
        title.setText("Please select an UOM");
        EditText searchText = (EditText) uomDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(View.GONE);
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
                    Utils.showCommonAlertDialog(mContext, "Please Note!", "No rate found! Please contact admin.");
                }

            }
        });
        Button addCustomer = (Button) uomDialog.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        uomDialog.show();
    }

    public void ShowVericalValueList() {
        final Dialog mPincodeDialog = new Dialog(OrderFormActivity.this, R.style.PauseDialog);
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
                Constants.mVerticalValue = adapter.getItem(position);
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
                Toast.makeText(mContext, "No Depot found.", Toast.LENGTH_LONG).show();
            }
        }

    }
    public void ShowDepotListForBranchWiseMrp()
    {

        final ArrayList<BranchMasterDetails> branchMasterDetailsList = mAceDnsDatabase
                .getSaudaRDSListForOrder(Constants.selectedCustomer.getCustomerCode(), "");

        if (branchMasterDetailsList.size() > 1) {
            final Dialog mDialogDepotName = new Dialog(mContext,
                    R.style.PauseDialog);
            mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogDepotName.setContentView(R.layout.select_from_list);
            mDialogDepotName.setCancelable(false);

            TextView title = (TextView) mDialogDepotName.findViewById(R.id.title);
            title.setText("Please select a Branch");
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
                Toast.makeText(mContext, "No Depot found.", Toast.LENGTH_LONG).show();
            }
        }

    }

    public void ShowPreviousOrderDialog() {
        String productcode = currentProductMasterObj.getProdCode();
        String customercode = Constants.selectedCustomer.getCustomerCode();
        String data = mAceDnsDatabase.GetPreviousOrederData(customercode, productcode);

        if (data.length() > 0) {
            final Dialog mPreviousOrderDialog = new Dialog(OrderFormActivity.this,
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
            buttonCancel.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    mPreviousOrderDialog.cancel();
                }
            });

            mPreviousOrderDialog.show();
        }
    }

}
