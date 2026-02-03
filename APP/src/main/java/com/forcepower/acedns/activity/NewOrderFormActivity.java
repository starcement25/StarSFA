package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
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
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.NewOrderAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
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

import androidx.annotation.NonNull;

public class NewOrderFormActivity extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonNoOrder = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonCheckOut = null;
    TextView orderNumber, totalOrder, txtUOM1;
    Button btnContinue, btnCustomer;
    EditText edAvailCrdt, edAmount;
    LinearLayout bodyLayout, optionLayout, customerLayout, creditLimLayout, filterLayout, qty_mrpLayout, stk_tdLayout, amountLayout, tdNewLayout;
    /*
     * Custom Views
     */
    Spinner mrpSpinner;
    EditText edQty, edSaleRate, edClsngStk, edTd;
    Dialog  grpDialog, subGrpDialog, brandDialog, masterDialog, routeDialog, routePlanListDialog, uomDialog;

    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;

    ProductGroupDetails mSelectedProductGroupDetails;
    ProductSubGrpDetails mSelectedProductSubGrpDetails;
    ProductBrandDetails mSelectedProductBrandDetails;
    ProductMasterDetails mCurrentProductMasterDetails;

    RouteDetails mSelectedRouteDetails;
    RoutePlanMasterDetails mSelectedRoutePlanMasterDetails;

    int chosenCustPos;
    int mrpSelected;

    ArrayList<MRPDetails> mrpSpinnerList;
    ArrayList<String> filterList;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<ProductGroupDetails> mProductGroupDetailsList;
    ArrayList<ProductSubGrpDetails> mProductSubGrpDetailsList;
    ArrayList<ProductBrandDetails> mProductBrandDetailsList;
    ArrayList<ProductMasterDetails> mProductMasterDetailsList;
    ArrayList<Button> filterButtonList;
    ArrayList<CustomerDetails> tempCustomerList;
    
    int filterNo;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected, lastProductSelected = false;
    CustomerAdapter mCustomerAdapter;
    String lastStr = "";
    int lastProdPos = 0;
    ArrayAdapter spinnerAdapter;
    boolean carryInSales = false;
    ProgressDialog ploader;
    Handler orderDataHandler;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    boolean rpeatedProductEntry = false;

    @SuppressLint("HandlerLeak")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_form);
        RegisterActivities.registerActivity(this);

        carryInSales = getIntent().getBooleanExtra("CARRY_IN", false);
        Constants.isFromConfirmationActivity = false;
        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        mContext = NewOrderFormActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Constants.selectedProductMasterList = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();
        filterButtonList = new ArrayList<>();

        initView();
        drawLayout();
        
        String currentDate = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);

        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
            ArrayList<RoutePlanMasterDetails> todayPlanList = mAceDnsTransactionDatabase.getPlanForToday(currentDate);
            if (todayPlanList.size() == 1) {
                mSelectedRoutePlanMasterDetails = todayPlanList.get(0);
                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRoutePlanMasterDetails.getRoutecode());
                if (!mCustomerDetailsList.isEmpty()) {
                    tempCustomerList = new ArrayList<>();
                    mCustomerAdapter = new CustomerAdapter(NewOrderFormActivity.this, R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_SHORT).show();
                }
            } else {
                showRoutePlanListDialog(todayPlanList);
            }
        } else {
            ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteList();
            if (routeList.size() == 1) {
                mSelectedRouteDetails = routeList.get(0);
                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRouteDetails.getRouteCode());
                if (!mCustomerDetailsList.isEmpty()) {
                    tempCustomerList = new ArrayList<>();
                    mCustomerAdapter = new CustomerAdapter(mContext, R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_SHORT).show();
                }
            } else if (routeList.size() > 1) {
                showRouteListDialog(routeList);
            }
        }

        orderDataHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                NewOrderFormActivity.this.runOnUiThread(() -> {
                    switch (jobToDo) {
                        case 1:
                            ShowProductGrpListDialog();
                            break;
                        case 2:
                            ShowProductSubGrpListDialog();
                            break;
                        case 3:
                            ShowProductBrandListDialog();
                            break;
                        case 4:
                            ShowMasterListDialog();
                            break;
                    }
                });
            }
        };
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onResume() {
        super.onResume();
        rpeatedProductEntry = Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes");

        if (Constants.selectedProductMasterList == null || Constants.selectedProductMasterList.isEmpty()) {
            customerLayout.setVisibility(View.INVISIBLE);
        } else {
            customerLayout.setVisibility(View.VISIBLE);
            if (Constants.selectedProductMasterList.size() % 2 == 0) {
                customerLayout.setBackgroundColor(Color.YELLOW);
            } else {
                customerLayout.setBackgroundColor(Color.GREEN);
            }
            orderNumber.setText("" + Constants.selectedProductMasterList.size());
            calculateTotal();
        }

        edQty.setText("");
        if (edTd != null && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("customer wise")) {
            edTd.setText("");
        }
        if (edSaleRate != null) {
            edSaleRate.setText("");
        }

        if (mrpSpinner != null) {
            String[] tempSpinnerArray = new String[0];
            spinnerAdapter = new ArrayAdapter(mContext, android.R.layout.simple_spinner_item, tempSpinnerArray);
            mrpSpinner.setAdapter(spinnerAdapter);
        }

        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
        if (Constants.isFromConfirmationActivity) {

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

    /*
     * ::::::::::::::::::::::::::::::::::: VIEW RELATED OPERATIONS
     * ::::::::::::::::::::::::::::::::::::::::::::::
     */
    @SuppressLint("SetTextI18n")
    public void initView() {
        amountLayout =  findViewById(R.id.amount_layout);
        edAmount =  findViewById(R.id.ed_amt);
        edAmount.setTextSize(15);
        if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase("yes")) {
            amountLayout.setVisibility(View.VISIBLE);
        } else {
            amountLayout.setVisibility(View.GONE);
        }
        edAmount.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

            @Override
            public void afterTextChanged(Editable s) {
                if (edSaleRate != null) {
                    edSaleRate.setEnabled(s.length() <= 0);
                }
            }
        });

        mImageViewHeaderLogo =  findViewById(R.id.imagelogo);

        bodyLayout =  findViewById(R.id.body_layout);
        optionLayout =  findViewById(R.id.option_layout);
        customerLayout =  findViewById(R.id.customer_layout);
        if (Constants.selectedProductMasterList == null || Constants.selectedProductMasterList.isEmpty()) {
            customerLayout.setVisibility(View.INVISIBLE);
        }
        TextView txtVersion =  findViewById(R.id.txt_version);
        // txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        creditLimLayout =  findViewById(R.id.crdt_layout);
        filterLayout =  findViewById(R.id.filter_layout);
        qty_mrpLayout =  findViewById(R.id.qty_mrp_layout);
        stk_tdLayout =  findViewById(R.id.stk_td_layout);
        tdNewLayout =  findViewById(R.id.td_layout);

        totalOrder =  findViewById(R.id.txt_total);
        orderNumber =  findViewById(R.id.txt_order_count);
        orderNumber.setText("" + Constants.selectedProductMasterList.size());
        calculateTotal();
        edAvailCrdt =  findViewById(R.id.ed_avail_crdt);

        btnContinue =  findViewById(R.id.btn_continue);
        btnContinue.setEnabled(false);
        btnContinue.setOnClickListener(NewOrderFormActivity.this);
        btnContinue.setTag(101);
        mButtonCheckOut =  findViewById(R.id.btn_order_form);
        mButtonCheckOut.setTag(102);
        mButtonCheckOut.setEnabled(false);
        mButtonCheckOut.setOnClickListener(NewOrderFormActivity.this);
        mButtonNoOrder =  findViewById(R.id.no_ordr);
        mButtonNoOrder.setTag(104);
        mButtonNoOrder.setOnClickListener(NewOrderFormActivity.this);
        mButtonBack =  findViewById(R.id.back);
        mButtonBack.setTag(105);
        mButtonBack.setOnClickListener(NewOrderFormActivity.this);

        btnCustomer =  findViewById(R.id.select_customer);
        btnCustomer.setTag(107);
        btnCustomer.setOnClickListener(NewOrderFormActivity.this);
    }

    public void drawLayout() {
        drawFilterLayout();
        drawQtyMrpLayout();
        drawStkTdVatLayout();
        enableDisableLayout(false);
    }

    @SuppressLint("RtlHardcoded")
    public void drawFilterLayout() {
        filterList = mAceDnsDatabase.getFilterList(); // filterList =
        // [4(filter_no),dadu,baba,NA,chhele]
        for (int ii = 1; ii < filterList.size(); ii++) {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
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
                filterButton.setSingleLine(true);
                filterButton.setOnClickListener(this);
                filterButton.setEnabled(false);
                buttonLayout.addView(filterButton);
                filterLayout.addView(buttonLayout);
                filterButtonList.add(filterButton);
            } else {
                filterButtonList.add(null);
            }
        }
    }

    @SuppressLint({"RtlHardcoded", "SetTextI18n"})
    public void drawQtyMrpLayout() {

        LinearLayout qtyLayout = new LinearLayout(NewOrderFormActivity.this);
        qtyLayout.setOrientation(LinearLayout.HORIZONTAL);
        LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
        qtyLayout.setLayoutParams(params);

        TextView txtQty = new TextView(mContext);
        txtQty.setText("Qty    : ");
        txtQty.setTextColor(Color.parseColor("#003399"));
        qtyLayout.addView(txtQty);

        edQty = new EditText(mContext);
        edQty.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edQty.setEms(5);
        edQty.setTextSize(15);
        edQty.setGravity(Gravity.RIGHT);
        edQty.setImeOptions(EditorInfo.IME_ACTION_DONE);
        edQty.setTag("qty");
        edQty.setOnFocusChangeListener((v, hasFocus) -> {
            if (hasFocus) {
                ((EditText) v).setText("");
            }
        });

        edQty.setOnEditorActionListener((v, actionId, event) -> {
            if (event == null) {
                if (actionId == EditorInfo.IME_ACTION_DONE) {
                    // edTd.requestFocus();
                    InputMethodManager imm = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
                    imm.hideSoftInputFromWindow(edQty.getWindowToken(), 0);
                    getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    return true;
                }
            }
            return false;
        });
        qtyLayout.addView(edQty);

        txtUOM1 = new TextView(mContext);
        txtUOM1.setText("");
        txtUOM1.setTextColor(Color.parseColor("#000000"));
        txtUOM1.setTextSize(10);
        qtyLayout.addView(txtUOM1);
        qty_mrpLayout.addView(qtyLayout);

        if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
            if (edAmount != null) {
                edAmount.setEnabled(false);
            }

            LinearLayout mrpLayout = new LinearLayout(NewOrderFormActivity.this);
            mrpLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
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
                public void onItemSelected(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    mrpSelected = arg2;
                }

                @Override
                public void onNothingSelected(AdapterView<?> arg0) {}
            });

            mrpLayout.addView(mrpSpinner);
            qty_mrpLayout.addView(mrpLayout);
        } else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
            LinearLayout mrpLayout = new LinearLayout(NewOrderFormActivity.this);
            mrpLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
            mrpLayout.setLayoutParams(params1);

            TextView txtMrp = new TextView(mContext);
            txtMrp.setText(" SaleRate : ");
            txtMrp.setTextColor(Color.parseColor("#003399"));
            mrpLayout.addView(txtMrp);
            if (Constants.orderFormDetailsObj.getSaleRateDrpdwn()
                    .equalsIgnoreCase("input")) {
                edSaleRate = new EditText(mContext);
                edSaleRate.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                edSaleRate.setEms(6);
                edSaleRate.setTextSize(15);
                edSaleRate.setImeOptions(EditorInfo.IME_ACTION_DONE);
                edSaleRate.setGravity(Gravity.RIGHT);
                edSaleRate.setTag("mrp");
                mrpLayout.addView(edSaleRate);
                qty_mrpLayout.addView(mrpLayout);

                if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase(
                        "yes")) {
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
                                edAmount.setEnabled(s.length() <= 0);
                            }
                        }
                    });
                }
            } else {
                if (edAmount != null) {
                    edAmount.setEnabled(false);
                }

                mrpSpinner = new Spinner(mContext);
                mrpSpinner.setPadding(30, 3, 0, 3);
                mrpSpinner.setBackgroundResource(R.drawable.spinner_bg);
                mrpSpinner.setOnItemSelectedListener(new OnItemSelectedListener() {
                            @Override
                            public void onItemSelected(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                                mrpSelected = arg2;
                            }

                            @Override
                            public void onNothingSelected(AdapterView<?> arg0) {}
                        });

                mrpLayout.addView(mrpSpinner);
                qty_mrpLayout.addView(mrpLayout);
            }

        }
    }

    @SuppressLint({"SetTextI18n", "RtlHardcoded"})
    public void drawStkTdVatLayout() {
        if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes") || carryInSales) {
            LinearLayout stkLayout = new LinearLayout(NewOrderFormActivity.this);
            stkLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
            stkLayout.setLayoutParams(params1);

            TextView txtStk = new TextView(mContext);
            txtStk.setText("Cl Stk : ");
            txtStk.setTextColor(Color.parseColor("#003399"));
            stkLayout.addView(txtStk);

            edClsngStk = new EditText(mContext);
            edClsngStk.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            edClsngStk.setEnabled(false);
            edClsngStk.setEms(5);
            edClsngStk.setTextSize(15);
            edClsngStk.setGravity(Gravity.RIGHT);
            edClsngStk.setTag("stk");
            edClsngStk.setOnFocusChangeListener((v, hasFocus) -> {
                if (hasFocus) {
                    ((EditText) v).setText("");
                }
            });
            stkLayout.addView(edClsngStk);
            stk_tdLayout.addView(stkLayout);
        }
        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise")) {
            LinearLayout tdLayout = new LinearLayout(NewOrderFormActivity.this);
            tdLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
            tdLayout.setLayoutParams(params1);

            TextView txtTD = new TextView(mContext);
            txtTD.setText("TD  : ");
            txtTD.setTextColor(Color.parseColor("#003399"));
            tdLayout.addView(txtTD);

            edTd = new EditText(mContext);
            edTd.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            edTd.setImeOptions(EditorInfo.IME_ACTION_DONE);
            edTd.setPadding(4, 2, 4, 2);
            edTd.setEms(5);
            edTd.setTextSize(15);
            edTd.setGravity(Gravity.RIGHT);
            edTd.setTag("td");
            edTd.setOnFocusChangeListener((v, hasFocus) -> {
                if (hasFocus) {
                    ((EditText) v).setText("");
                }
            });
            edTd.setOnEditorActionListener((v, actionId, event) -> {
                if (event == null) {
                    if (actionId == EditorInfo.IME_ACTION_DONE) {
                        // edTd.requestFocus();
                        InputMethodManager imm = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
                        imm.hideSoftInputFromWindow(edTd.getWindowToken(), 0);
                        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        return true;
                    }
                }
                return false;
            });
            tdLayout.addView(edTd);

            TextView txtPercnt = new TextView(mContext);
            txtPercnt.setText("%");
            txtPercnt.setTextColor(Color.parseColor("#003399"));
            tdLayout.addView(txtPercnt);

            tdNewLayout.addView(tdLayout);
        }
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getVatCalcOn().equalsIgnoreCase("sku"))) {
            LinearLayout tdLayout = new LinearLayout(NewOrderFormActivity.this);
            tdLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
            tdLayout.setLayoutParams(params1);

            TextView txtTD = new TextView(mContext);
            txtTD.setText("VAT : ");
            txtTD.setTextColor(Color.parseColor("#003399"));
            tdLayout.addView(txtTD);

            edTd = new EditText(mContext);
            edTd.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            edTd.setImeOptions(EditorInfo.IME_ACTION_DONE);
            edTd.setPadding(4, 2, 4, 2);
            edTd.setEms(5);
            edTd.setGravity(Gravity.RIGHT);
            edTd.setTag("vat");
            edTd.setOnFocusChangeListener((v, hasFocus) -> {
                if (hasFocus) {
                    ((EditText) v).setText("");
                }
            });
            edTd.setOnEditorActionListener((v, actionId, event) -> {
                if (event == null) {
                    if (actionId == EditorInfo.IME_ACTION_DONE) {
                        // edTd.requestFocus();
                        InputMethodManager imm = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
                        imm.hideSoftInputFromWindow(edTd.getWindowToken(), 0);
                        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        return true;
                    }
                }
                return false;
            });
            tdLayout.addView(edTd);

            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase("percentage")) {
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
            edClsngStk.setEnabled(false);
        }
    }

    public void enableDisableLayout(boolean isShown) {
        filterLayout.setEnabled(isShown);
        qty_mrpLayout.setEnabled(isShown);
        stk_tdLayout.setEnabled(isShown);
    }

    public void manageViewAvailableCredit(CustomerDetails customerObj) {
        if (Constants.orderFormDetailsObj.getCreditLimit().equalsIgnoreCase("yes")) {
            if (customerObj.getCreditLimit() != null) {
                creditLimLayout.setVisibility(View.VISIBLE);
                edAvailCrdt.setText(customerObj.getCreditLimit());
            }
        }
    }

    public void showMRPDialog(ProductMasterDetails productObj, String selectedUOM) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = mAceDnsDatabase.getMRPList(productCode, selectedUOM);
        String[] tempSpinnerArray = new String[mrpSpinnerList.size() + 1];
        for (int k = 0; k < mrpSpinnerList.size(); k++) {
            tempSpinnerArray[k] = mrpSpinnerList.get(k).getMrpValue();
        }
        tempSpinnerArray[tempSpinnerArray.length - 1] = "FOC";
        spinnerAdapter = new ArrayAdapter(NewOrderFormActivity.this,
                android.R.layout.simple_spinner_item, tempSpinnerArray);
        mrpSpinner.setAdapter(spinnerAdapter);
    }

    public void showSaleRateDialog(ProductMasterDetails productObj, String selectedUOM) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = mAceDnsDatabase.getMRPList(productCode, selectedUOM);
        String[] tempSpinnerArray = new String[mrpSpinnerList.size() + 1];
        for (int k = 0; k < mrpSpinnerList.size(); k++) {
            tempSpinnerArray[k] = mrpSpinnerList.get(k).getSaleRate();
        }
        tempSpinnerArray[tempSpinnerArray.length - 1] = "FOC";
        ArrayAdapter adapter = new ArrayAdapter(NewOrderFormActivity.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
        mrpSpinner.setAdapter(adapter);
    }

    /*
     * ::::::::::::::::::::::::::::::: LISTNER METHODS
     * ::::::::::::::::::::::::::::::::::::::::::
     */
    @SuppressLint("SetTextI18n")
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
                    if (mSelectedProductGroupDetails != null) {
                        prepareOrderData(2, mSelectedProductGroupDetails.getGroupCode());
                    } else {
                        Toast.makeText(NewOrderFormActivity.this,
                                "Please select the parent category", Toast.LENGTH_SHORT).show();
                    }
                    break;
                case 3:
                    lastProdPos = 0;
                    filterButtonList.get(3).setEnabled(true);
                    lastProductSelected = false;
                    if (mSelectedProductSubGrpDetails != null) {
                        prepareOrderData(3, mSelectedProductSubGrpDetails.getSubGrpCode());
                    } else {
                        Toast.makeText(NewOrderFormActivity.this,
                                "Please select the parent category", Toast.LENGTH_SHORT).show();
                    }
                    break;
                case 4:
                    switch (filterNo) {
                        case 1:
                            prepareOrderData(4, "");
                            break;
                        case 2:
                            if (mSelectedProductGroupDetails != null) {
                                prepareOrderData(4, mSelectedProductGroupDetails.getGroupCode());
                            } else {
                                Toast.makeText(NewOrderFormActivity.this,
                                        "Please select the parent category", Toast.LENGTH_SHORT).show();
                            }
                            break;
                        case 3:
                            if (mSelectedProductSubGrpDetails != null) {
                                prepareOrderData(4, mSelectedProductSubGrpDetails.getSubGrpCode());
                            } else {
                                Toast.makeText(NewOrderFormActivity.this,
                                        "Please select the parent category", Toast.LENGTH_SHORT).show();
                            }
                            break;
                        case 4:
                            if (mSelectedProductBrandDetails != null) {
                                prepareOrderData(4, mSelectedProductBrandDetails.getBrandCode());
                            } else {
                                Toast.makeText(NewOrderFormActivity.this,
                                        "Please select the parent category", Toast.LENGTH_SHORT).show();
                            }
                            break;
                    }
            }
        }
        if (clkdView == btnContinue) {
            // Continue Button
            if (mCurrentProductMasterDetails != null) {
                boolean qty_status, mrp_status = true, tdStatus = true;
                if (!edQty.getText().toString().isEmpty() && !edQty.getText().toString().equalsIgnoreCase("0") && !edQty.getText().toString().equalsIgnoreCase(".")) {
                    if (carryInSales) {
                        if ((Double.parseDouble(edQty.getText().toString()) <= Double.parseDouble(edClsngStk.getText().toString()))) {
                            mCurrentProductMasterDetails.setQty(edQty.getText().toString());
                            qty_status = true;
                        } else {
                            qty_status = false;
                        }
                    } else {
                        mCurrentProductMasterDetails.setQty(edQty.getText().toString());
                        qty_status = true;
                    }
                    if (mCurrentProductMasterDetails.getSelectedUOM() != -1) {
                        if (mCurrentProductMasterDetails.getSelectedUOM() == 0) {
                            double finalQty = Double.parseDouble(mCurrentProductMasterDetails.getQty()) * Double.parseDouble(mCurrentProductMasterDetails.getConversionFactor());
                            if (finalQty != Math.floor(finalQty)) {
                                qty_status = false;
                            }
                        }
                    }
                } else {
                    qty_status = false;
                }
                if (mrpSpinner != null) {
                    if (!mrpSpinner.getSelectedItem().toString().equalsIgnoreCase("FOC")) {
                        if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                            mCurrentProductMasterDetails.setMrpCode(mrpSpinnerList.get(mrpSelected).getMrpCode());
                            mCurrentProductMasterDetails.setMrpValue(mrpSpinnerList.get(mrpSelected).getMrpValue());
                            double slRt = Double.parseDouble(mrpSpinnerList.get(mrpSelected).getMrpValue());
                            double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                            if (qtyD > 0) {
                                mCurrentProductMasterDetails.setAmount(String.valueOf(slRt * qtyD));
                            }
                        } else {
                            mCurrentProductMasterDetails.setMrpCode(mrpSpinnerList.get(mrpSelected).getMrpCode());
                            mCurrentProductMasterDetails.setMrpValue(mrpSpinnerList.get(mrpSelected).getSaleRate());
                            double slRt = Double.parseDouble(mrpSpinnerList.get(mrpSelected).getSaleRate());
                            double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                            if (qtyD > 0) {
                                mCurrentProductMasterDetails.setAmount(String.valueOf(slRt * qtyD));
                            }
                        }
                    } else {
                        mCurrentProductMasterDetails.setMrpCode("FOC");
                        mCurrentProductMasterDetails.setMrpValue("0");
                        mCurrentProductMasterDetails.setAmount(String.valueOf(0));
                    }
                } else if (edSaleRate != null) {
                    if (!edSaleRate.getText().toString().isEmpty() && !edSaleRate.getText().toString().equalsIgnoreCase(".")) {
                        mCurrentProductMasterDetails.setMrpCode("0");
                        mCurrentProductMasterDetails.setMrpValue(edSaleRate.getText().toString());
                        double slRt = Double.parseDouble(edSaleRate.getText().toString());
                        double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                        if (qtyD > 0) {
                            mCurrentProductMasterDetails.setAmount(String.valueOf(slRt * qtyD));
                        }
                    } else {
                        if (edAmount != null && !edAmount.getText().toString().isEmpty() && !edAmount.getText().toString().equalsIgnoreCase(".")) {
                            double amountD = Double.parseDouble(edAmount.getText().toString());
                            double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                            if (qtyD > 0) {
                                mCurrentProductMasterDetails.setMrpCode("0");
                                mCurrentProductMasterDetails.setMrpValue(String.valueOf(amountD / qtyD));
                                mCurrentProductMasterDetails.setAmount(String.valueOf(amountD));
                                mCurrentProductMasterDetails.setAmtEntered(true);
                            } else {
                                mrp_status = false;
                            }
                        } else {
                            mrp_status = false;
                        }
                    }
                } else {
                    mCurrentProductMasterDetails.setMrpCode("0");
                    mCurrentProductMasterDetails.setMrpValue("0");
                }
                if (edTd != null) {
                    if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
                        mCurrentProductMasterDetails.setVat("0");
                        String td = "0";
                        if (!edTd.getText().toString().isEmpty() && !edTd.getText().toString().equalsIgnoreCase(".")) {
                            td = edTd.getText().toString();
                        }
                        if (Double.parseDouble(td) <= 100) {
                            mCurrentProductMasterDetails.setTradeDiscnt(td);
                        } else {
                            tdStatus = false;
                            Toast.makeText(NewOrderFormActivity.this, "Trade Discount cannot be more than 100 %", Toast.LENGTH_SHORT).show();
                        }
                    } else {
                        mCurrentProductMasterDetails.setTradeDiscnt("0");
                        String vat;
                        if (!edTd.getText().toString().isEmpty() && !edTd.getText().toString().equalsIgnoreCase(".")) {
                            vat = edTd.getText().toString();
                            mCurrentProductMasterDetails.setVat(vat);
                        }
                    }
                } else {
                    mCurrentProductMasterDetails.setTradeDiscnt("0");
                    mCurrentProductMasterDetails.setVat("0");
                }
                if (qty_status && mrp_status && tdStatus) {
                    customerLayout.setVisibility(View.VISIBLE);
                    if (Constants.selectedProductMasterList.size() % 2 == 0) {
                        customerLayout.setBackgroundColor(Color.YELLOW);
                    } else {
                        customerLayout.setBackgroundColor(Color.GREEN);
                    }
                    Constants.selectedProductMasterList.add(mCurrentProductMasterDetails);
                    orderNumber.setText("" + Constants.selectedProductMasterList.size());
                    calculateTotal();

                    if (carryInSales) {
                        mAceDnsTransactionDatabase.reduceClStkProductWise(mCurrentProductMasterDetails);
                    }

                    Toast.makeText(NewOrderFormActivity.this, "Product has been added to cart.", Toast.LENGTH_LONG).show();
                    mCurrentProductMasterDetails = null;
                    if (mrpSpinner != null) {
                        String[] tempSpinnerArray = new String[0];
                        spinnerAdapter = new ArrayAdapter(NewOrderFormActivity.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
                        mrpSpinner.setAdapter(spinnerAdapter);
                    }

                    switch (filterNo) {
                        case 1:
                            if (lastProductSelected) {
                                btnContinue.setEnabled(false);
                            }
                            break;
                        case 2:
                            if (lastGrpSelected && lastProductSelected) {
                                btnContinue.setEnabled(false);
                            }
                            break;
                        case 3:
                            if (lastGrpSelected && lastSubGroupSelected && lastProductSelected) {
                                btnContinue.setEnabled(false);
                            }
                            break;
                        case 4:
                            if (lastGrpSelected && lastSubGroupSelected && lastBrandSelected && lastProductSelected) {
                                btnContinue.setEnabled(false);
                            }
                            break;
                    }

                    switch (filterNo) {
                        case 1:
                            if (lastProductSelected) {
                                // No option but to submit
                                filterButtonList.get(3).setText("No product left..");
                                filterButtonList.get(3).setEnabled(false);
                            }
                        case 2:
                            if (lastProductSelected) {
                                Constants.selectedGroupList.add(mSelectedProductGroupDetails);
                                filterButtonList.get(3).setText("No product left.Select a different parent category.");
                                filterButtonList.get(3).setEnabled(false);
                            }
                        case 3:
                            if (lastProductSelected) {
                                Constants.selectedSubGroupList.add(mSelectedProductSubGrpDetails);
                                filterButtonList.get(3).setText("No product left.Select a different parent category.");
                                filterButtonList.get(3).setEnabled(false);
                                if (lastSubGroupSelected) {
                                    Constants.selectedGroupList.add(mSelectedProductGroupDetails);
                                    filterButtonList.get(1).setText("No product left.Select a different parent category.");
                                    filterButtonList.get(1).setEnabled(false);
                                }
                            }
                            break;
                        case 4:
                            if (lastProductSelected) {
                                Constants.selectedBrandList.add(mSelectedProductBrandDetails);
                                filterButtonList.get(3).setText("No product left.Select a different parent category.");
                                filterButtonList.get(3).setEnabled(false);
                                if (lastBrandSelected) {
                                    Constants.selectedSubGroupList.add(mSelectedProductSubGrpDetails);
                                    filterButtonList.get(2).setText("No product left.Select a different parent category.");
                                    filterButtonList.get(2).setEnabled(false);
                                    if (lastSubGroupSelected) {
                                        Constants.selectedGroupList.add(mSelectedProductGroupDetails);
                                        filterButtonList.get(1).setText("No product left.Select a different parent category.");
                                        filterButtonList.get(1).setEnabled(false);
                                    }
                                }
                            }
                            break;
                    }

                } else {
                    Toast.makeText(NewOrderFormActivity.this, "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(NewOrderFormActivity.this, "Please select a product", Toast.LENGTH_LONG).show();
            }
            edQty.setText("");
            txtUOM1.setText("");
            if (edAmount != null) {
                edAmount.setText("");
            }
            if (edTd != null && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("customer wise")) {
                edTd.setText("");
            }
            if (edSaleRate != null) {
                edSaleRate.setText("");
            }
        }
        if (clkdView == mButtonCheckOut) {
            // Order Button
            if (!Constants.selectedProductMasterList.isEmpty()) {
                Intent intent = new Intent(NewOrderFormActivity.this, OrderConfirmationActivity.class);
                intent.putExtra("CARRY_IN", carryInSales);
                startActivity(intent);
            } else {
                Toast.makeText(NewOrderFormActivity.this, "Please select a product", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == mButtonNoOrder) {
            ShowNoOrderDialog();
        }
        if (clkdView == mButtonBack) {
            if (carryInSales && !Constants.selectedProductMasterList.isEmpty()) {
                AceDnsTransactionDatabase dbObj = new AceDnsTransactionDatabase(mContext);
                dbObj.increaseClStk();
                dbObj.close();
            }
            finish();
        }
        if (clkdView == btnCustomer) {
            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRoutePlanMasterDetails.getRoutecode());
                if (!mCustomerDetailsList.isEmpty()) {
                    tempCustomerList = new ArrayList<>();
                    mCustomerAdapter = new CustomerAdapter(NewOrderFormActivity.this, R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_SHORT).show();
                    // showAddCustomerDialog();
                }
            } else {
                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRouteDetails.getRouteCode());
                if (!mCustomerDetailsList.isEmpty()) {
                    tempCustomerList = new ArrayList<>();
                    mCustomerAdapter = new CustomerAdapter(NewOrderFormActivity.this, R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_SHORT).show();
                    // showAddCustomerDialog();
                }
            }
        }
    }

    /*
     * :::::::::::::::::::::::::::::: CREATING DIFFERENT DIALOGs
     * :::::::::::::::::::::::::::::::::
     */
    @SuppressLint("SetTextI18n")
    public void showChooseCustomerDialog() {
        final Dialog customerListDialog = new Dialog(NewOrderFormActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);
        TextView title =  customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText =  customerListDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

            @Override
            public void afterTextChanged(Editable s) {
                lastStr = s.toString();
                mCustomerAdapter.notifyDataSetChanged();
            }
        });

        ListView dialogList =  customerListDialog.findViewById(R.id.list);
        dialogList.setAdapter(mCustomerAdapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            Constants.isSelectCustomer = true;
            CustomerDetails currentObj = tempCustomerList.get(arg2);
            for (int ii = 0; ii < mCustomerDetailsList.size(); ii++) {
                if (mCustomerDetailsList.get(ii).getCustomerCode().equalsIgnoreCase(currentObj.getCustomerCode())) {
                    chosenCustPos = ii;
                }
            }
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            customerListDialog.cancel();
            // custName.setText(customerList.get(chosenCustPos).getCustomerName());
            btnCustomer.setText(mCustomerDetailsList.get(chosenCustPos).getCustomerName());
            btnCustomer.setEnabled(false);
            Constants.selectedCustomer = mCustomerDetailsList.get(chosenCustPos);
            enableDisableLayout(true);
            mButtonNoOrder.setEnabled(true);
            manageViewAvailableCredit(mCustomerDetailsList.get(chosenCustPos));
            for (int i = 0; i < filterButtonList.size(); i++) {
                if (filterButtonList.get(i) != null) {
                    filterButtonList.get(i).setEnabled(true);
                }
            }
            btnContinue.setEnabled(true);
            mButtonCheckOut.setEnabled(true);
            if (edTd != null && Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("customer wise")) {
                edTd.setText(currentObj.getTradeDiscount());
                edTd.setEnabled(false);
            }
        });

        Button addCustomer =  customerListDialog.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        customerListDialog.show();
    }

    @SuppressLint({"SetTextI18n","SimpleDateFormat"})
    public void ShowNoOrderDialog() {
        final Dialog dialogNoOrder = new Dialog(NewOrderFormActivity.this, R.style.PauseDialog);
        dialogNoOrder.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogNoOrder.setContentView(R.layout.user_instruction_dialog);
        dialogNoOrder.setCancelable(false);
        TextView title =  dialogNoOrder.findViewById(R.id.title);
        title.setText("Please state the reason for no Order");
        final EditText edReason =  dialogNoOrder.findViewById(R.id.ed_input);
        final Button submit =  dialogNoOrder.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            dialogNoOrder.cancel();
            boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
            if (isTimeAutomatic) {
                new GPSTracker(mContext);
                String reason = "";
                reason = edReason.getText().toString();
                String timeStamp = Constants.dateString + new SimpleDateFormat("_HHmmss").format(Calendar.getInstance().getTime());
                timeStamp = timeStamp.replace("_", "");
                mAceDnsTransactionDatabase.insertToOrderHeaderTable("NO", reason, timeStamp, "", "", "", "", "NO", "", "", "", "");
                mAceDnsTransactionDatabase.insertToLocationTable("NO", timeStamp);
                new TRANS_SubmitOrderTask(NewOrderFormActivity.this, true).execute();
            } else {
                Utils.showSettingsAlertToChangeTimeZone(mContext);
            }
        });
        dialogNoOrder.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowProductGrpListDialog() {
        if (!mProductGroupDetailsList.isEmpty()) {
            if (mProductGroupDetailsList.size() == 1) {
                lastGrpSelected = true;
            }
            final ProductGrpAdapter productGrpAdapter = new ProductGrpAdapter(NewOrderFormActivity.this, R.layout.product_list_child, mProductGroupDetailsList);
            grpDialog = new Dialog(NewOrderFormActivity.this, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_with_search);
            grpDialog.setTitle("Please select an option");
            grpDialog.setCancelable(false);
            TextView title =  grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText =  grpDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                @Override
                public void afterTextChanged(Editable s) {
                    lastStr = s.toString();
                    productGrpAdapter.notifyDataSetChanged();
                }
            });
            ListView dialogList =  grpDialog.findViewById(R.id.list);
            dialogList.setAdapter(productGrpAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
                grpDialog.cancel();
                mSelectedProductGroupDetails = mProductGroupDetailsList.get(pos);
                filterButtonList.get(0).setText(mSelectedProductGroupDetails.getGroupName());

                switch (filterNo) {
                    case 2:
                        prepareOrderData(4, mSelectedProductGroupDetails.getGroupCode());
                        break;
                    case 3:
                        prepareOrderData(2, mSelectedProductGroupDetails.getGroupCode());
                        break;
                    case 4:
                        prepareOrderData(2, mSelectedProductGroupDetails.getGroupCode());
                        break;
                }

            });
            Button cancel =  grpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> grpDialog.cancel());
            grpDialog.show();
        } else {
            Toast.makeText(NewOrderFormActivity.this, "There are no items left.Please submit order.", Toast.LENGTH_SHORT).show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowProductSubGrpListDialog() {
        if (!mProductSubGrpDetailsList.isEmpty()) {
            if (mProductSubGrpDetailsList.size() == 1) {
                lastSubGroupSelected = true;
            }
            final ProductSubGrpAdapter productSubGrpAdapter = new ProductSubGrpAdapter(NewOrderFormActivity.this, R.layout.product_list_child, mProductSubGrpDetailsList);
            subGrpDialog = new Dialog(NewOrderFormActivity.this, R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_with_search);
            subGrpDialog.setCancelable(false);
            TextView title =  subGrpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText =  subGrpDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                @Override
                public void afterTextChanged(Editable s) {
                    lastStr = s.toString();
                    productSubGrpAdapter.notifyDataSetChanged();
                }
            });
            ListView dialogList =  subGrpDialog.findViewById(R.id.list);
            dialogList.setAdapter(productSubGrpAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                subGrpDialog.cancel();
                mSelectedProductSubGrpDetails = mProductSubGrpDetailsList.get(arg2);
                filterButtonList.get(1).setText(mSelectedProductSubGrpDetails.getSubGrpName());
                switch (filterNo) {
                    case 3:
                        prepareOrderData(4, mSelectedProductSubGrpDetails.getSubGrpCode());
                        break;
                    case 4:
                        prepareOrderData(3, mSelectedProductSubGrpDetails.getSubGrpCode());
                        break;
                }
            });
            Button cancel =  subGrpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> subGrpDialog.cancel());
            subGrpDialog.show();
        } else {
            Toast.makeText(NewOrderFormActivity.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_SHORT).show();
            Constants.selectedGroupList.add(mSelectedProductGroupDetails);
            filterButtonList.get(1).setText("");
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowProductBrandListDialog() {
        if (!mProductBrandDetailsList.isEmpty()) {
            if (mProductBrandDetailsList.size() == 1) {
                lastBrandSelected = true;
            }
            final ProductBrandAdapter productBrandAdapter = new ProductBrandAdapter(NewOrderFormActivity.this, R.layout.product_list_child, mProductBrandDetailsList);
            brandDialog = new Dialog(NewOrderFormActivity.this, R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_with_search);
            brandDialog.setCancelable(false);
            TextView title =  brandDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText =  brandDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                @Override
                public void afterTextChanged(Editable s) {
                    lastStr =s.toString();
                    productBrandAdapter.notifyDataSetChanged();
                }
            });
            ListView dialogList =  brandDialog.findViewById(R.id.list);
            dialogList.setAdapter(productBrandAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                brandDialog.cancel();
                mSelectedProductBrandDetails = mProductBrandDetailsList.get(arg2);
                filterButtonList.get(2).setText(mSelectedProductBrandDetails.getBrandName());
                prepareOrderData(4, mSelectedProductBrandDetails.getBrandCode());
            });
            Button cancel =  brandDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> brandDialog.cancel());
            brandDialog.show();
        } else {
            Toast.makeText(NewOrderFormActivity.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_SHORT).show();
            Constants.selectedSubGroupList.add(mSelectedProductSubGrpDetails);
            filterButtonList.get(2).setText("");
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowMasterListDialog() {
        if (!mProductMasterDetailsList.isEmpty()) {
            if (mProductMasterDetailsList.size() == 1) {
                lastProductSelected = true;
            }
            NewOrderAdapter mNewOrderAdapter = new NewOrderAdapter(NewOrderFormActivity.this, R.layout.list_child_layout, mProductMasterDetailsList);
            Log.i("Product List Size", String.valueOf(mProductMasterDetailsList.size()));

            masterDialog = new Dialog(NewOrderFormActivity.this);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_from_list);
            TextView title =  masterDialog.findViewById(R.id.title);
            title.setText("Input Quantity");
            ListView dialogList =  masterDialog.findViewById(R.id.list);
            dialogList.setAdapter(mNewOrderAdapter);
            dialogList.setDivider(null);
            if (lastProdPos != 0) {
                dialogList.setSelection(lastProdPos - 1);
            } else {
                dialogList.setSelection(0);
            }
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                masterDialog.cancel();
                lastProdPos = arg2;
                mCurrentProductMasterDetails = mProductMasterDetailsList.get(arg2);
                filterButtonList.get(3).setText(mCurrentProductMasterDetails.getDesc());
                if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes")) {
                    ArrayList<String> uomList = new ArrayList<String>();
                    uomList.add(mCurrentProductMasterDetails.getUom1());
                    uomList.add(mCurrentProductMasterDetails.getUom2());
                    showUOMDialog(uomList);
                } else {
                    txtUOM1.setText(mCurrentProductMasterDetails.getUom1());
                    if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                        showMRPDialog(mCurrentProductMasterDetails, "");
                    }
                    if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                        showSaleRateDialog(mCurrentProductMasterDetails, "");
                    }
                    if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes") || carryInSales) {
                        showClosingStock(mCurrentProductMasterDetails);
                    }
                }
            });
            Button btnSubmit =  masterDialog.findViewById(R.id.btn_cncl);
            btnSubmit.setText("Submit");
            btnSubmit.setOnClickListener(v -> masterDialog.cancel());

            masterDialog.show();
        } else {
            Toast.makeText(NewOrderFormActivity.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_SHORT).show();
            filterButtonList.get(3).setText("");
        }
    }

    @SuppressLint("SetTextI18n")
    public void showRouteListDialog(final ArrayList<RouteDetails> routeList) {
        routeDialog = new Dialog(NewOrderFormActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title =  routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList =  routeDialog.findViewById(R.id.list);
        RouteAdapter adapter1 = new RouteAdapter(NewOrderFormActivity.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routeDialog.cancel();
            mSelectedRouteDetails = routeList.get(arg2);
            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRouteDetails.getRouteCode());
            if (!mCustomerDetailsList.isEmpty()) {
                tempCustomerList = new ArrayList<>();
                mCustomerAdapter = new CustomerAdapter(NewOrderFormActivity.this, R.layout.customer_list_child, tempCustomerList);
                showChooseCustomerDialog();
            } else {
                Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_SHORT).show();
                // showAddCustomerDialog();
            }
        });
        Button cancel =  routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route =  routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void showRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> todayList) {
        routePlanListDialog = new Dialog(NewOrderFormActivity.this, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title =  routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList =  routePlanListDialog.findViewById(R.id.list);
        RoutePlanTransAdapter adapter1 = new RoutePlanTransAdapter(NewOrderFormActivity.this, R.layout.route_list_child, todayList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routePlanListDialog.cancel();
            mSelectedRoutePlanMasterDetails = todayList.get(arg2);
            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRoutePlanMasterDetails.getRoutecode());
            if (!mCustomerDetailsList.isEmpty()) {
                tempCustomerList = new ArrayList<>();
                mCustomerAdapter = new CustomerAdapter(NewOrderFormActivity.this, R.layout.customer_list_child, tempCustomerList);
                showChooseCustomerDialog();
            } else {
                Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_SHORT).show();
                // showAddCustomerDialog();
            }
        });
        Button cancel =  routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        cancel.setOnClickListener(arg0 -> routePlanListDialog.cancel());
        Button create_route =  routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
    }

    // Later we will consider the case where VAT = yes and TD =
    // order_value_wise.
    @SuppressLint("SetTextI18n")
    public void calculateTotal() {
        double amount = 0;
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = !currentObj.getMrpValue().isEmpty() ? currentObj.getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double discount = Double.parseDouble(currentObj.getTradeDiscnt());
                    amount = amount + ((mrp * qty) - (mrp * qty * discount / 100));
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = !currentObj.getMrpValue().isEmpty() ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    amount = amount + (mrp * qty);
                }
                amount = (amount)
                        - (amount * Double.parseDouble("0") / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase(
                    "amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = !currentObj.getMrpValue().isEmpty() ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + vat);
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = !currentObj.getMrpValue().isEmpty() ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }
        totalOrder.setText(currency + defaultFormat.format(amount));
    }

    public void prepareOrderData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        mProductGroupDetailsList = mAceDnsDatabase.getProductGroupList(carryInSales);
                        break;
                    case 2:
                        mProductSubGrpDetailsList = mAceDnsDatabase.getProductSubGroupList(param, carryInSales);
                        break;
                    case 3:
                        mProductBrandDetailsList = mAceDnsDatabase.getProductBrandList(param, carryInSales);
                        break;
                    case 4:
                        if (carryInSales || Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes")) {
                            mProductMasterDetailsList = mAceDnsDatabase.getProductMasterList(param, filterNo, carryInSales);
                        } else {
                            mProductMasterDetailsList = mAceDnsDatabase.getProductMasterListIgnoringStock(param, filterNo);
                        }
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

    @SuppressLint("SetTextI18n")
    public void showUOMDialog(final ArrayList<String> uomList) {
        SimpleStringAdapter adapterUOM = new SimpleStringAdapter(NewOrderFormActivity.this, R.layout.simple_list_child, uomList);
        uomDialog = new Dialog(NewOrderFormActivity.this, R.style.PauseDialog);
        uomDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        uomDialog.setContentView(R.layout.choose_customer_search);
        uomDialog.setCancelable(false);
        TextView title =  uomDialog.findViewById(R.id.title);
        title.setText("Please select an UOM");
        EditText searchText =  uomDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(View.GONE);
        ListView dialogList =  uomDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterUOM);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            String selectedUOM = uomList.get(arg2);
            mCurrentProductMasterDetails.setSelectedUOM(arg2);
            if (arg2 == 1) {
                edQty.setInputType(InputType.TYPE_CLASS_NUMBER);
            } else {
                edQty.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            }
            txtUOM1.setText(selectedUOM);
            uomDialog.cancel();
            if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                showMRPDialog(mCurrentProductMasterDetails, selectedUOM);
            }
            if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                showSaleRateDialog(mCurrentProductMasterDetails, selectedUOM);
            }
            if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes") || carryInSales) {
                showClosingStock(mCurrentProductMasterDetails);
            }
        });
        Button addCustomer =  uomDialog.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        uomDialog.show();
    }
}
