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
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
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
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.currency;

public class StockReturnActivity extends AceDnsParentActivity {

    ImageView headerLogo;
    Button btnContinue, btnOrder, btnNoOrder, btnBack, btnCustomer;
    LinearLayout filterLayout, priceLayout, clstkLayout, customerLayout,
            amountLayout;
    EditText edQty, edAmount;
    Dialog grpDialog, subGrpDialog,
            brandDialog, masterDialog,
            customerListDialog, routeDialog,
            routePlanListDialog;
    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    Spinner mrpSpinner;
    EditText edSaleRate;
    ArrayAdapter spinnerAdapter;
    int mrpSelected;
    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;
    ArrayList<MRPDetails> mrpSpinnerList;
    int chosenCustPos;
    EditText edClsngStk;
    TextView orderNumber, totalOrder;
    ArrayList<String> filterList;
    ArrayList<CustomerDetails> customerList, tempCustomerList;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;

    ArrayList<Button> filterButtonList;
    ProductMasterDetails currentProductMasterObj;
    int filterNo;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected,
            lastProductSelected = false;
    ProductMasterAdapter prodAdapter;
    CustomerAdapter adapterCust;
    String lastStr = "";
    ArrayList<ProductMasterDetails> tempProductList;
    int lastProdPos = 0;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    String transType = "";
    Handler orderDataHandler;
    ProgressDialog ploader;
    TextView txtUOM1;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_stockout);
        RegisterActivities.registerActivity(this);

        Constants.isFromConfirmationActivity = false;
        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        mContext = StockReturnActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);
        Constants.selectedProductMasterList = new ArrayList<ProductMasterDetails>();
        Constants.selectedGroupList = new ArrayList<ProductGroupDetails>();
        Constants.selectedSubGroupList = new ArrayList<ProductSubGrpDetails>();
        Constants.selectedBrandList = new ArrayList<ProductBrandDetails>();

        filterButtonList = new ArrayList<Button>();

        initView();
        drawFilterLayout();

        showStockReturnOptionDialog();

        orderDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                StockReturnActivity.this.runOnUiThread(new Runnable() {
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

    @Override
    public void onResume() {
        super.onResume();

        if (Constants.selectedProductMasterList == null
                || Constants.selectedProductMasterList.size() == 0) {
            customerLayout.setVisibility(View.INVISIBLE);
        } else {
            customerLayout.setVisibility(View.VISIBLE);
            if (Constants.selectedProductMasterList.size() % 2 == 0) {
                customerLayout.setBackgroundColor(Color.YELLOW);
            } else {
                customerLayout.setBackgroundColor(Color.GREEN);
            }
            orderNumber
                    .setText("" + Constants.selectedProductMasterList.size());
            calculateTotal();
        }

        if (Constants.isFromConfirmationActivity) {
            Constants.isFromConfirmationActivity = false;
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

    public void initView() {

        amountLayout = (LinearLayout) findViewById(R.id.amount_layout);
        edAmount = (EditText) findViewById(R.id.ed_amt);
        if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase("yes")) {
            amountLayout.setVisibility(View.VISIBLE);
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

        headerLogo = (ImageView) findViewById(R.id.imagelogo);
        txtUOM1 = (TextView) findViewById(R.id.txt_uom);
        if (Constants.logoBmp != null) {
            headerLogo.setVisibility(View.VISIBLE);
            headerLogo.setImageBitmap(Constants.logoBmp);
        } else {
            headerLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        // txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        edQty = (EditText) findViewById(R.id.ed_qty);
        filterLayout = (LinearLayout) findViewById(R.id.filter_layout);

        priceLayout = (LinearLayout) findViewById(R.id.price_layout);
        clstkLayout = (LinearLayout) findViewById(R.id.clstklayout);
        drawPriceLayout();
        drawClStkLayout();

        customerLayout = (LinearLayout) findViewById(R.id.customer_layout);
        if (Constants.selectedProductMasterList == null
                || Constants.selectedProductMasterList.size() == 0) {
            customerLayout.setVisibility(View.INVISIBLE);
        }

        totalOrder = (TextView) findViewById(R.id.txt_total);
        orderNumber = (TextView) findViewById(R.id.txt_order_count);
        orderNumber.setText("" + Constants.selectedProductMasterList.size());
        calculateTotal();

        btnContinue = (Button) findViewById(R.id.btn_continue);
        btnContinue.setEnabled(false);
        btnContinue.setOnClickListener(StockReturnActivity.this);
        btnContinue.setTag(101);
        btnOrder = (Button) findViewById(R.id.btn_order_form);
        btnOrder.setTag(102);
        btnOrder.setEnabled(false);
        btnOrder.setOnClickListener(StockReturnActivity.this);
        btnNoOrder = (Button) findViewById(R.id.no_ordr);
        btnNoOrder.setVisibility(View.INVISIBLE);
        btnNoOrder.setTag(104);
        btnNoOrder.setOnClickListener(StockReturnActivity.this);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setTag(105);
        btnBack.setOnClickListener(StockReturnActivity.this);

        btnCustomer = (Button) findViewById(R.id.select_customer);
        btnCustomer.setTag(107);
        btnCustomer.setOnClickListener(StockReturnActivity.this);
    }

    public void drawClStkLayout() {
        LinearLayout stkLayout = new LinearLayout(StockReturnActivity.this);
        stkLayout.setOrientation(LinearLayout.HORIZONTAL);
        LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(0,
                LayoutParams.WRAP_CONTENT, 1);
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
        edClsngStk.setGravity(Gravity.RIGHT);
        edClsngStk.setTag("stk");
        stkLayout.addView(edClsngStk);
        clstkLayout.addView(stkLayout);

    }

    public void drawPriceLayout() {
        if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {

            if (edAmount != null) {
                edAmount.setEnabled(false);
            }

            LinearLayout mrpLayout = new LinearLayout(StockReturnActivity.this);
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
            priceLayout.addView(mrpLayout);
        } else if (Constants.orderFormDetailsObj.getSaleRate()
                .equalsIgnoreCase("yes")) {
            LinearLayout mrpLayout = new LinearLayout(StockReturnActivity.this);
            mrpLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(
                    0, LayoutParams.WRAP_CONTENT, 1);
            mrpLayout.setLayoutParams(params1);

            TextView txtMrp = new TextView(mContext);
            txtMrp.setText(" SaleRate : ");
            txtMrp.setTextColor(Color.parseColor("#003399"));
            mrpLayout.addView(txtMrp);
            if (Constants.orderFormDetailsObj.getSaleRateDrpdwn()
                    .equalsIgnoreCase("input")) {
                edSaleRate = new EditText(mContext);
                edSaleRate.setInputType(InputType.TYPE_CLASS_NUMBER
                        | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                edSaleRate.setEms(6);
                edSaleRate.setImeOptions(EditorInfo.IME_ACTION_DONE);
                edSaleRate.setGravity(Gravity.RIGHT);
                edSaleRate.setTag("mrp");
                mrpLayout.addView(edSaleRate);
                priceLayout.addView(mrpLayout);

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
                                if (s.length() > 0) {
                                    edAmount.setEnabled(false);
                                } else {
                                    edAmount.setEnabled(true);
                                }
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
                mrpSpinner
                        .setOnItemSelectedListener(new OnItemSelectedListener() {
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
                priceLayout.addView(mrpLayout);
            }

        }
    }

    public void drawFilterLayout() {
        filterList = setupDataHelperObj.getFilterList(); // filterList =
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
                filterButton.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
                filterButton.setText(filterList.get(ii));
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
                        Toast.makeText(StockReturnActivity.this,
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
                        Toast.makeText(StockReturnActivity.this,
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
                                Toast.makeText(StockReturnActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(StockReturnActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                prepareOrderData(4, selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(StockReturnActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                    }
            }
        }
        if (clkdView == btnContinue) {
            // Continue Button
            if (currentProductMasterObj != null) {
                boolean qty_status = true, mrp_status = true;
                if (edQty.getText().toString() != null
                        && edQty.getText().toString().length() > 0
                        && !edQty.getText().toString().equalsIgnoreCase("0")
                        && !edQty.getText().toString().equalsIgnoreCase(".")) {
                    Double qtyGiven = Double.parseDouble(edQty.getText()
                            .toString());
                    Double clStkGiven = Double.parseDouble(edClsngStk.getText()
                            .toString());
                    if ((transType.equalsIgnoreCase("SR"))
                            || (transType.equalsIgnoreCase("SA"))) {
                        if (qtyGiven <= clStkGiven) {
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
                } else {
                    qty_status = false;
                }
                if (mrpSpinner != null) {
                    if (Constants.orderFormDetailsObj.getMrp()
                            .equalsIgnoreCase("yes")) {
                        currentProductMasterObj.setMrpCode(mrpSpinnerList.get(
                                mrpSelected).getMrpCode());
                        currentProductMasterObj.setMrpValue(mrpSpinnerList.get(
                                mrpSelected).getMrpValue());
                        Double slRt = Double.parseDouble(mrpSpinnerList.get(
                                mrpSelected).getMrpValue());
                        Double qtyD = Double.parseDouble((qty_status ? edQty
                                .getText().toString() : "1"));
                        if (qtyD > 0) {
                            currentProductMasterObj.setAmount(String
                                    .valueOf(slRt * qtyD));
                        }
                    } else {
                        currentProductMasterObj.setMrpCode(mrpSpinnerList.get(
                                mrpSelected).getMrpCode());
                        currentProductMasterObj.setMrpValue(mrpSpinnerList.get(
                                mrpSelected).getSaleRate());
                        Double slRt = Double.parseDouble(mrpSpinnerList.get(
                                mrpSelected).getSaleRate());
                        Double qtyD = Double.parseDouble((qty_status ? edQty
                                .getText().toString() : "1"));
                        if (qtyD > 0) {
                            currentProductMasterObj.setAmount(String
                                    .valueOf(slRt * qtyD));
                        }
                    }
                } else if (edSaleRate != null) {
                    if (edSaleRate.getText().toString().length() > 0
                            && !edSaleRate.getText().toString()
                            .equalsIgnoreCase(".")) {
                        currentProductMasterObj.setMrpCode("0");
                        currentProductMasterObj.setMrpValue(edSaleRate
                                .getText().toString());
                        Double slRt = Double.parseDouble(edSaleRate.getText()
                                .toString());
                        Double qtyD = Double.parseDouble((qty_status ? edQty
                                .getText().toString() : "1"));
                        if (qtyD > 0) {
                            currentProductMasterObj.setAmount(String
                                    .valueOf(slRt * qtyD));
                        }
                    } else {
                        if (edAmount != null
                                && edAmount.getText().toString().length() > 0
                                && !edAmount.getText().toString()
                                .equalsIgnoreCase(".")) {
                            Double amountD = Double.parseDouble(edAmount
                                    .getText().toString());
                            Double qtyD = Double
                                    .parseDouble((qty_status ? edQty.getText()
                                            .toString() : "1"));
                            if (qtyD > 0) {
                                currentProductMasterObj.setMrpCode("0");
                                currentProductMasterObj.setMrpValue(String
                                        .valueOf(amountD / qtyD));
                                currentProductMasterObj.setAmount(String
                                        .valueOf(amountD));
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
                currentProductMasterObj.setTradeDiscnt("0");
                if (qty_status && mrp_status) {
                    edQty.setText("");
                    txtUOM1.setText("");
                    customerLayout.setVisibility(View.VISIBLE);
                    if (Constants.selectedProductMasterList.size() % 2 == 0) {
                        customerLayout.setBackgroundColor(Color.YELLOW);
                    } else {
                        customerLayout.setBackgroundColor(Color.GREEN);
                    }
                    Constants.selectedProductMasterList
                            .add(currentProductMasterObj);
                    orderNumber.setText(""
                            + Constants.selectedProductMasterList.size());

                    if (transType.equalsIgnoreCase("CR")) {
                        transDataHelperObj
                                .increaseClStkProductWise(currentProductMasterObj);
                    } else {
                        transDataHelperObj
                                .reduceClStkProductWise(currentProductMasterObj);
                    }

                    calculateTotal();
                    Toast.makeText(StockReturnActivity.this,
                            "Product has been added to cart.", Toast.LENGTH_LONG).show();
                    currentProductMasterObj = null;

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
                            if (lastGrpSelected && lastSubGroupSelected
                                    && lastProductSelected) {
                                btnContinue.setEnabled(false);
                            }
                            break;
                        case 4:
                            if (lastGrpSelected && lastSubGroupSelected
                                    && lastBrandSelected && lastProductSelected) {
                                btnContinue.setEnabled(false);
                            }
                            break;
                    }

                    switch (filterNo) {
                        case 1:
                            if (lastProductSelected) {
                                // No option but to submit
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
                    Toast.makeText(StockReturnActivity.this,
                            "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(StockReturnActivity.this,
                        "Please select a product", Toast.LENGTH_LONG).show();
            }
            if (edSaleRate != null) {
                edSaleRate.setText("");
            }
            if (edAmount != null) {
                edAmount.setText("");
            }
        }
        if (clkdView == btnOrder) {
            // Order Button
            if (Constants.selectedProductMasterList.size() > 0) {
                Intent intent = new Intent(StockReturnActivity.this,
                        StockTransferConfirmationActivity.class);
                intent.putExtra("TRANS_TYPE", transType);
                intent.putExtra("salesOption", true);
                startActivity(intent);
            } else {
                Toast.makeText(StockReturnActivity.this,
                        "Please select a product", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == btnNoOrder) {
        }
        if (clkdView == btnBack) {
            if (Constants.selectedProductMasterList.size() > 0) {
                if (transType.equalsIgnoreCase("CR")) {
                    transDataHelperObj.reduceClStk();
                } else {
                    transDataHelperObj.increaseClStk();
                }
            }
            finish();
        }
        if (clkdView == btnCustomer) {
        }
    }

    /*
     * :::::::::::::::::::::::::::::: CREATING DIFFERENT DIALOGs
     * :::::::::::::::::::::::::::::::::
     */

    public void showGrpListDialog() {
        if (productGroupList.size() > 0) {
            // if(productGroupList.size() == 1){
            // lastGrpSelected = true;
            // }
            grpDialog = new Dialog(StockReturnActivity.this,
                    R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_from_list);
            grpDialog.setTitle("Please select an option");
            grpDialog.setCancelable(false);
            TextView title = (TextView) grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) grpDialog.findViewById(R.id.list);
            ProductGrpAdapter adapter1 = new ProductGrpAdapter(
                    StockReturnActivity.this, R.layout.product_list_child,
                    productGroupList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    grpDialog.cancel();
                    selectedGrp = productGroupList.get(arg2);
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
            Button cancel = (Button) grpDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        } else {
            Toast.makeText(StockReturnActivity.this,
                    "There are no items left.Please submit order.", 2000)
                    .show();
        }
    }

    public void showSubGrpListDialog() {
        if (productSubGroupList.size() > 0) {
            // if(productSubGroupList.size() == 1){
            // lastSubGroupSelected = true;
            // }
            subGrpDialog = new Dialog(StockReturnActivity.this,
                    R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_from_list);
            subGrpDialog.setCancelable(false);
            TextView title = (TextView) subGrpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) subGrpDialog
                    .findViewById(R.id.list);
            ProductSubGrpAdapter adapter1 = new ProductSubGrpAdapter(
                    StockReturnActivity.this, R.layout.product_list_child,
                    productSubGroupList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    subGrpDialog.cancel();
                    selectedSubGrp = productSubGroupList.get(arg2);
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
            Button cancel = (Button) subGrpDialog.findViewById(R.id.btn_cncl);
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
                    StockReturnActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    public void showBrandListDialog() {
        if (productBrandList.size() > 0) {
            // if(productBrandList.size() == 1){
            // lastBrandSelected= true;
            // }
            brandDialog = new Dialog(StockReturnActivity.this,
                    R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_from_list);
            brandDialog.setCancelable(false);
            TextView title = (TextView) brandDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) brandDialog
                    .findViewById(R.id.list);
            ProductBrandAdapter adapter1 = new ProductBrandAdapter(
                    StockReturnActivity.this, R.layout.product_list_child,
                    productBrandList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    brandDialog.cancel();
                    selectedBrand = productBrandList.get(arg2);
                    filterButtonList.get(2).setText(
                            selectedBrand.getBrandName());
                    // showMasterDialog(selectedBrand.getBrandCode());
                    prepareOrderData(4, selectedBrand.getBrandCode());
                }
            });
            Button cancel = (Button) brandDialog.findViewById(R.id.btn_cncl);
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
                    StockReturnActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    public void showMasterListDialog() {
        if (productMasterList.size() > 0) {
            // if(outstandingList.size() == 1){
            // lastProductSelected = true;
            // }
            prodAdapter = new ProductMasterAdapter(StockReturnActivity.this,
                    R.layout.product_list_child, tempProductList);
            masterDialog = new Dialog(StockReturnActivity.this,
                    R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = (EditText) masterDialog
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
                        reInitialiseProductList();
                    }
                    lastStr = str;
                    filterProductArray(str.length(), str);
                    prodAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) masterDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(prodAdapter);
            if (lastProdPos != 0) {
                dialogList.setSelection(lastProdPos - 1);
            } else {
                dialogList.setSelection(0);
            }
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    masterDialog.cancel();
                    lastProdPos = arg2;
                    currentProductMasterObj = tempProductList.get(arg2);
                    txtUOM1.setText(currentProductMasterObj.getUom1());
                    filterButtonList.get(3).setText(
                            currentProductMasterObj.getDesc());
                    if (Constants.orderFormDetailsObj.getMrp()
                            .equalsIgnoreCase("yes")) {
                        showMRPDialog(currentProductMasterObj);
                    }
                    if (Constants.orderFormDetailsObj.getSaleRate()
                            .equalsIgnoreCase("yes")
                            && Constants.orderFormDetailsObj
                            .getSaleRateDrpdwn().equalsIgnoreCase(
                                    "dropdown")) {
                        showSaleRateDialog(currentProductMasterObj);
                    }
                    showClosingStock(currentProductMasterObj);
                }
            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    masterDialog.cancel();
                }
            });

            masterDialog.show();
        } else {
            Toast.makeText(
                    StockReturnActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            filterButtonList.get(3).setText("");
        }
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

    // public void filterBranchArray(int strCnt,String charVal){
    // int size = tempBranchList.size();
    // for(int ii=0;ii<size;ii++){
    // if(tempBranchList.get(ii).getBranchName().length() >= strCnt){
    // //if(tempCashTransferList.get(ii).getCustomerName().substring(0,strCnt).equalsIgnoreCase(charVal)){
    // /* String[] wordList =
    // tempCashTransferList.get(ii).getCustomerName().split(" ");
    // Boolean found = checkMatch(wordList,strCnt,charVal);*/
    // if(tempBranchList.get(ii).getBranchName().toUpperCase().contains(charVal.toUpperCase())){
    // // Keep this item in ArrayList
    // }else{
    // tempBranchList.remove(tempBranchList.get(ii));
    // size = size-1;
    // ii = ii-1;
    // }
    // }else{
    // tempBranchList.remove(tempBranchList.get(ii));
    // size = size-1;
    // ii = ii-1;
    // }
    // }
    // }

    public void filterProductArray(int strCnt, String charVal) {
        int size = tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductList.get(ii).getDesc().length() >= strCnt) {
                // if(tempProductList.get(ii).getDesc().substring(0,strCnt).equalsIgnoreCase(charVal)){
                /*
                 * String[] wordList =
                 * tempProductList.get(ii).getDesc().split(" "); Boolean found =
                 * checkMatch(wordList,strCnt,charVal);
                 */
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

    // public void reInitialiseBranchList(){
    // tempBranchList.removeAll(tempBranchList);
    // int size = tempBranchList.size();
    // int size1 = branchList.size();
    // System.out.println("SIZE" + size + "_____" + size1);
    // for(int kk=0;kk<branchList.size();kk++){
    // tempBranchList.add(branchList.get(kk));
    // }
    //
    // }

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            tempProductList.add(productMasterList.get(kk));
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

    public void showMRPDialog(ProductMasterDetails productObj) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = setupDataHelperObj.getMRPList(productCode, "");
        String[] tempSpinnerArray = new String[mrpSpinnerList.size()];
        for (int k = 0; k < mrpSpinnerList.size(); k++) {
            tempSpinnerArray[k] = mrpSpinnerList.get(k).getMrpValue();
        }
        spinnerAdapter = new ArrayAdapter(StockReturnActivity.this,
                android.R.layout.simple_spinner_item, tempSpinnerArray);
        mrpSpinner.setAdapter(spinnerAdapter);
    }

    public void showSaleRateDialog(ProductMasterDetails productObj) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = setupDataHelperObj.getMRPList(productCode, "");
        String[] tempSpinnerArray = new String[mrpSpinnerList.size()];
        for (int k = 0; k < mrpSpinnerList.size(); k++) {
            tempSpinnerArray[k] = mrpSpinnerList.get(k).getSaleRate();
        }
        ArrayAdapter adapter = new ArrayAdapter(StockReturnActivity.this,
                android.R.layout.simple_spinner_item, tempSpinnerArray);
        mrpSpinner.setAdapter(adapter);
    }

    public void showClosingStock(ProductMasterDetails productObj) {
        if (edClsngStk != null && productObj.getClosingStk() != null) {
            edClsngStk.setText(productObj.getClosingStk());
            edClsngStk.setEnabled(false);
        }
    }

    public void calculateTotal() {
        double amount = 0;
        for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
            ProductMasterDetails currentObj = Constants.selectedProductMasterList
                    .get(ii);
            double qty = Double.parseDouble(currentObj.getQty());
            String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                    .getMrpValue() : currentObj.getAmount();
            double mrp = Double.parseDouble(mrpVal);
            double discount = Double.parseDouble(currentObj.getTradeDiscnt());
            amount = amount + ((mrp * qty) - (mrp * qty * discount / 100));
        }
        totalOrder.setText(currency + defaultFormat.format(amount));
    }

    public void showRouteListDialog(final ArrayList<RouteDetails> routeList) {
        routeDialog = new Dialog(StockReturnActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        RouteAdapter adapter1 = new RouteAdapter(StockReturnActivity.this,
                R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routeDialog.cancel();
                RouteDetails detailsObj = routeList.get(arg2);
                customerList = setupDataHelperObj
                        .getCustomerListByRoute(detailsObj.getRouteCode());
                if (customerList.size() > 0) {
                    tempCustomerList = new ArrayList<CustomerDetails>();
                    reInitialiseCustomerList();
                    adapterCust = new CustomerAdapter(StockReturnActivity.this,
                            R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Utils.showToast(mContext,
                            "No customer found for CarryIn Returns");
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
        create_route.setVisibility(View.VISIBLE);
        routeDialog.show();
    }

    public void showRoutePlanListDialog(
            final ArrayList<RoutePlanMasterDetails> todayList) {
        routePlanListDialog = new Dialog(StockReturnActivity.this,
                R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog
                .findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routePlanListDialog
                .findViewById(R.id.list);
        RoutePlanTransAdapter adapter1 = new RoutePlanTransAdapter(
                StockReturnActivity.this, R.layout.route_list_child, todayList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routePlanListDialog.cancel();
                RoutePlanMasterDetails detailsObj = todayList.get(arg2);
                customerList = setupDataHelperObj
                        .getCustomerListByRoute(detailsObj.getRoutecode());
                if (customerList.size() > 0) {
                    tempCustomerList = new ArrayList<CustomerDetails>();
                    reInitialiseCustomerList();
                    adapterCust = new CustomerAdapter(StockReturnActivity.this,
                            R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Utils.showToast(mContext,
                            "No customer found for CarryIn Returns");
                }
            }
        });
        Button cancel = (Button) routePlanListDialog
                .findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanListDialog.cancel();
            }
        });
        Button create_route = (Button) routePlanListDialog
                .findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
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

    public void showChooseCustomerDialog() {
        customerListDialog = new Dialog(StockReturnActivity.this,
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
                btnCustomer.setText(customerList.get(chosenCustPos)
                        .getCustomerName());
                btnCustomer.setEnabled(false);
                Constants.selectedCustomer = customerList.get(chosenCustPos);
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                btnContinue.setEnabled(true);
                btnOrder.setEnabled(true);
            }
        });

        Button addCustomer = (Button) customerListDialog
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        customerListDialog.show();
    }

    public void prepareOrderData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        if (transType.equalsIgnoreCase("SR")
                                || transType.equalsIgnoreCase("SA")
                                || transType.equalsIgnoreCase("SH")) {
                            productGroupList = setupDataHelperObj
                                    .getProductGroupList(true);
                        } else {
                            productGroupList = setupDataHelperObj
                                    .getProductGroupList(false);
                        }
                        // removeRepeatedGroupItems();
                        break;
                    case 2:
                        if (transType.equalsIgnoreCase("SR")
                                || transType.equalsIgnoreCase("SA")
                                || transType.equalsIgnoreCase("SH")) {
                            productSubGroupList = setupDataHelperObj
                                    .getProductSubGroupList(param, true);
                        } else {
                            productSubGroupList = setupDataHelperObj
                                    .getProductSubGroupList(param, false);
                        }
                        // removeRepeatedSubGroupItems();
                        break;
                    case 3:
                        if (transType.equalsIgnoreCase("SR")
                                || transType.equalsIgnoreCase("SA")
                                || transType.equalsIgnoreCase("SH")) {
                            productBrandList = setupDataHelperObj
                                    .getProductBrandList(param, true);
                        } else {
                            productBrandList = setupDataHelperObj
                                    .getProductBrandList(param, false);
                        }
                        // removeRepeatedBrandItems();
                        break;
                    case 4:
                        if (transType.equalsIgnoreCase("SR")
                                || transType.equalsIgnoreCase("SA")
                                || transType.equalsIgnoreCase("SH")) {
                            productMasterList = setupDataHelperObj
                                    .getProductMasterList(param, filterNo, true);
                        } else {
                            productMasterList = setupDataHelperObj
                                    .getProductMasterList(param, filterNo, false);
                        }
                        // removeRepeatedProductItems();
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

    public void showStockReturnOptionDialog() {
        final Dialog stockReturnOptionDialog = new Dialog(
                StockReturnActivity.this);
        stockReturnOptionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        stockReturnOptionDialog
                .setContentView(R.layout.stock_return_dialog_new);
        stockReturnOptionDialog.setCancelable(false);
        TextView title = (TextView) stockReturnOptionDialog
                .findViewById(R.id.title);
        title.setText("Select an Option.");
        final RadioButton radioStockReturn = (RadioButton) stockReturnOptionDialog
                .findViewById(R.id.rd_stock_return);
        final RadioButton radioCarryInReturn = (RadioButton) stockReturnOptionDialog
                .findViewById(R.id.rd_carry_in_return);
        final RadioButton radioLeakage = (RadioButton) stockReturnOptionDialog
                .findViewById(R.id.rd_leakage);

        radioStockReturn
                .setOnCheckedChangeListener(new OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(CompoundButton buttonView,
                                                 boolean isChecked) {
                        if (isChecked) {
                            radioCarryInReturn.setChecked(false);
                            radioLeakage.setChecked(false);
                            Utils.showToast(mContext, "Menu is disabled");
                            stockReturnOptionDialog.cancel();
                            finish();

                        }
                    }
                });
        radioCarryInReturn
                .setOnCheckedChangeListener(new OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(CompoundButton buttonView,
                                                 boolean isChecked) {
                        if (isChecked) {
                            radioStockReturn.setChecked(false);
                            radioLeakage.setChecked(false);
                            stockReturnOptionDialog.cancel();
                            transType = "CR";
                            clstkLayout.setVisibility(View.INVISIBLE);
                            String currentDate = Constants.dateString
                                    .substring(6, 8)
                                    + "-"
                                    + Constants.dateString.substring(4, 6)
                                    + "-"
                                    + Constants.dateString.substring(0, 4);
                            if (Constants.menuDetailsObj.getRoutePlan()
                                    .equalsIgnoreCase("yes")) {
                                ArrayList<RoutePlanMasterDetails> todayPlanList = transDataHelperObj
                                        .getPlanForToday(currentDate);
                                if (todayPlanList.size() == 1) {
                                    RoutePlanMasterDetails detailsObj = todayPlanList
                                            .get(0);
                                    customerList = setupDataHelperObj
                                            .getCustomerListByRoute(detailsObj
                                                    .getRoutecode());
                                    if (customerList.size() > 0) {
                                        tempCustomerList = new ArrayList<CustomerDetails>();
                                        reInitialiseCustomerList();
                                        adapterCust = new CustomerAdapter(
                                                StockReturnActivity.this,
                                                R.layout.customer_list_child,
                                                tempCustomerList);
                                        showChooseCustomerDialog();
                                    } else {
                                        Utils.showToast(mContext,
                                                "No customer found for CarryIn Returns");
                                    }
                                } else {
                                    showRoutePlanListDialog(todayPlanList); // RoutePlanMaster
                                    // will
                                    // contain
                                    // data
                                    // thats
                                    // why
                                    // we
                                    // are
                                    // here
                                    // from
                                    // Menu
                                    // page.
                                }
                            } else {
                                // No concept of Route Plan. Show all customer.
                                ArrayList<RouteDetails> routeList = setupDataHelperObj
                                        .getRouteList();
                                if (routeList.size() == 1) {
                                    RouteDetails detailsObj = routeList.get(0);
                                    customerList = setupDataHelperObj
                                            .getCustomerListByRoute(detailsObj
                                                    .getRouteCode());
                                    if (customerList.size() > 0) {
                                        tempCustomerList = new ArrayList<CustomerDetails>();
                                        reInitialiseCustomerList();
                                        adapterCust = new CustomerAdapter(
                                                StockReturnActivity.this,
                                                R.layout.customer_list_child,
                                                tempCustomerList);
                                        showChooseCustomerDialog();
                                    } else {
                                        Utils.showToast(mContext,
                                                "No customer found for CarryIn Returns");
                                    }
                                } else {
                                    showRouteListDialog(routeList);
                                }
                            }
                        }
                    }
                });
        radioLeakage.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView,
                                         boolean isChecked) {
                if (isChecked) {
                    radioStockReturn.setChecked(false);
                    radioCarryInReturn.setChecked(false);
                    stockReturnOptionDialog.cancel();

                    showShortageOptionDialog();
                }
            }
        });
        stockReturnOptionDialog.show();
    }

    public void showShortageOptionDialog() {
        final Dialog shortageOptionDialog = new Dialog(StockReturnActivity.this);
        shortageOptionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        shortageOptionDialog.setContentView(R.layout.stock_return_dialog_new);
        shortageOptionDialog.setCancelable(false);
        TextView title = (TextView) shortageOptionDialog
                .findViewById(R.id.title);
        title.setText("Select an Option.");
        final RadioButton radioNormalShortage = (RadioButton) shortageOptionDialog
                .findViewById(R.id.rd_stock_return);
        radioNormalShortage.setText("Normal Shortage");
        final RadioButton radioBTShortageReturn = (RadioButton) shortageOptionDialog
                .findViewById(R.id.rd_carry_in_return);
        radioBTShortageReturn.setText("BT Shortage");
        final RadioButton radioLeakage = (RadioButton) shortageOptionDialog
                .findViewById(R.id.rd_leakage);
        radioLeakage.setVisibility(View.GONE);
        radioNormalShortage
                .setOnCheckedChangeListener(new OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(CompoundButton buttonView,
                                                 boolean isChecked) {
                        if (isChecked) {
                            shortageOptionDialog.cancel();
                            transType = "SH";
                            btnCustomer.setVisibility(View.GONE);
                            Constants.selectedCustomer = new CustomerDetails();
                            for (int i = 0; i < filterButtonList.size(); i++) {
                                if (filterButtonList.get(i) != null) {
                                    filterButtonList.get(i).setEnabled(true);
                                }
                            }
                            btnContinue.setEnabled(true);
                            btnOrder.setEnabled(true);
                        }
                    }
                });
        radioBTShortageReturn
                .setOnCheckedChangeListener(new OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(CompoundButton buttonView,
                                                 boolean isChecked) {
                        if (isChecked) {
                            shortageOptionDialog.cancel();
                            transType = "SA";
                            Intent intent = new Intent(
                                    StockReturnActivity.this,
                                    StockInFromBranch.class);
                            intent.putExtra("isShortageMenu", true);
                            startActivity(intent);
                            finish();
                        }
                    }
                });
        shortageOptionDialog.show();
    }

}
