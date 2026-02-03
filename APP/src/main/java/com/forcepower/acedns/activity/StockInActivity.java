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
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.VendorAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitStockAuditTask;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.VendorDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.currency;

public class StockInActivity extends AceDnsParentActivity {

    ImageView headerLogo;
    Button btnContinue, btnOrder, btnNoOrder, btnBack, btnCustomer;
    LinearLayout filterLayout, priceLayout, customerLayout, vatLayout,
            amountLayout;
    EditText edQty, edAmount;
    Dialog grpDialog, subGrpDialog, brandDialog,
            masterDialog, noOrderDialog, stockInOptionDialog,
            vendorListDialog, branchHQDialog;
    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    Spinner mrpSpinner;
    EditText edSaleRate, edVat;
    ArrayAdapter spinnerAdapter;
    int mrpSelected;
    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;
    ArrayList<MRPDetails> mrpSpinnerList;
    TextView orderNumber, totalOrder;
    ArrayList<String> filterList;
    ArrayList<BranchMasterDetails> branchHQList, tempBranchHQList;
    ArrayList<VendorDetails> vendorList, tempVendorList;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;
    VendorAdapter adapterVendor;
    ArrayList<Button> filterButtonList;
    ProductMasterDetails currentProductMasterObj;
    int filterNo;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected,
            lastProductSelected = false;
    ProductMasterAdapter prodAdapter;
    BranchAdapter adapterBranchHQ;
    String lastStr = "";
    ArrayList<ProductMasterDetails> tempProductList;
    int lastProdPos = 0;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    String transType = "";
    ProgressDialog ploader;
    Handler orderDataHandler;
    TextView txtUOM1;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_stockin);
        RegisterActivities.registerActivity(this);

        Constants.isFromConfirmationActivity = false;
        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        mContext = StockInActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);
        Constants.selectedProductMasterList = new ArrayList<ProductMasterDetails>();
        Constants.selectedGroupList = new ArrayList<ProductGroupDetails>();
        Constants.selectedSubGroupList = new ArrayList<ProductSubGrpDetails>();
        Constants.selectedBrandList = new ArrayList<ProductBrandDetails>();

        filterButtonList = new ArrayList<Button>();

        initView();
        drawFilterLayout();

        showStockInOptionDialog();

        orderDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                StockInActivity.this.runOnUiThread(new Runnable() {
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

        vatLayout = (LinearLayout) findViewById(R.id.vat_layout);
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")
                && Constants.orderFormDetailsObj.getVatCalcOn()
                .equalsIgnoreCase("sku")) {
            vatLayout.setVisibility(View.VISIBLE);
        } else {
            vatLayout.setVisibility(View.INVISIBLE);
        }
        edVat = (EditText) findViewById(R.id.ed_vat);

        priceLayout = (LinearLayout) findViewById(R.id.price_layout);
        drawPriceLayout();

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
        btnContinue.setOnClickListener(StockInActivity.this);
        btnContinue.setTag(101);
        btnOrder = (Button) findViewById(R.id.btn_order_form);
        btnOrder.setTag(102);
        btnOrder.setEnabled(false);
        btnOrder.setOnClickListener(StockInActivity.this);
        btnNoOrder = (Button) findViewById(R.id.no_ordr);
        btnNoOrder.setVisibility(View.INVISIBLE);
        btnNoOrder.setTag(104);
        btnNoOrder.setOnClickListener(StockInActivity.this);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setTag(105);
        btnBack.setOnClickListener(StockInActivity.this);

        btnCustomer = (Button) findViewById(R.id.select_customer);
        btnCustomer.setTag(107);
        btnCustomer.setOnClickListener(StockInActivity.this);
    }

    public void drawPriceLayout() {
        if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {

            if (edAmount != null) {
                edAmount.setEnabled(false);
            }

            LinearLayout mrpLayout = new LinearLayout(StockInActivity.this);
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
            LinearLayout mrpLayout = new LinearLayout(StockInActivity.this);
            mrpLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(
                    0, LayoutParams.WRAP_CONTENT, 1);
            mrpLayout.setLayoutParams(params1);

            TextView txtMrp = new TextView(mContext);
            txtMrp.setText(" Purc.Rate : ");
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
                    // showGrpDialog();
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
                        Toast.makeText(StockInActivity.this,
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
                        Toast.makeText(StockInActivity.this,
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
                                Toast.makeText(StockInActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(StockInActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                prepareOrderData(4, selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(StockInActivity.this,
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
                    currentProductMasterObj.setQty(edQty.getText().toString());
                    qty_status = true;
                } else {
                    qty_status = false;
                }
                if (mrpSpinner != null) {
                    if (!mrpSpinner.getSelectedItem().toString()
                            .equalsIgnoreCase("FOC")) {
                        if (Constants.orderFormDetailsObj.getMrp()
                                .equalsIgnoreCase("yes")) {
                            currentProductMasterObj.setMrpCode(mrpSpinnerList
                                    .get(mrpSelected).getMrpCode());
                            currentProductMasterObj.setMrpValue(mrpSpinnerList
                                    .get(mrpSelected).getMrpValue());
                            Double slRt = Double.parseDouble(mrpSpinnerList
                                    .get(mrpSelected).getMrpValue());
                            Double qtyD = Double
                                    .parseDouble((qty_status ? edQty.getText()
                                            .toString() : "1"));
                            if (qtyD > 0) {
                                currentProductMasterObj.setAmount(String
                                        .valueOf(slRt * qtyD));
                            }
                        } else {
                            currentProductMasterObj.setMrpCode(mrpSpinnerList
                                    .get(mrpSelected).getMrpCode());
                            currentProductMasterObj.setMrpValue(mrpSpinnerList
                                    .get(mrpSelected).getSaleRate());
                            Double slRt = Double.parseDouble(mrpSpinnerList
                                    .get(mrpSelected).getSaleRate());
                            Double qtyD = Double
                                    .parseDouble((qty_status ? edQty.getText()
                                            .toString() : "1"));
                            if (qtyD > 0) {
                                currentProductMasterObj.setAmount(String
                                        .valueOf(slRt * qtyD));
                            }
                        }
                    } else {
                        currentProductMasterObj.setMrpCode("FOC");
                        currentProductMasterObj.setMrpValue("0");
                        currentProductMasterObj.setAmount(String.valueOf(0));
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
                if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase(
                        "yes")) {
                    if (edVat.getText().toString().length() > 0
                            && !edVat.getText().toString()
                            .equalsIgnoreCase("0")
                            && !edVat.getText().toString()
                            .equalsIgnoreCase(".")) {
                        currentProductMasterObj.setVat(edVat.getText()
                                .toString());
                    } else {
                        currentProductMasterObj.setVat("0");
                    }
                } else {
                    currentProductMasterObj.setVat("0");
                }
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
                    calculateTotal();
                    Toast.makeText(StockInActivity.this,
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
                    Toast.makeText(StockInActivity.this,
                            "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(StockInActivity.this, "Please select a product",
                        2000).show();
            }
            if (edSaleRate != null) {
                edSaleRate.setText("");
            }
            if (edAmount != null) {
                edAmount.setText("");
            }
            edVat.setText("");
        }
        if (clkdView == btnOrder) {
            // Order Button
            if (Constants.selectedProductMasterList.size() > 0) {
                Intent intent = new Intent(StockInActivity.this,
                        StockTransferConfirmationActivity.class);
                intent.putExtra("TRANS_TYPE", transType);
                intent.putExtra("salesOption", true);
                startActivity(intent);
            } else {
                Toast.makeText(StockInActivity.this, "Please select a product",
                        2000).show();
            }
        }
        if (clkdView == btnNoOrder) {
            showNoOrderDialog();
        }
        if (clkdView == btnBack) {
            finish();
        }
        if (clkdView == btnCustomer) {
            // showChooseCustomerDialog();
        }
    }

    /*
     * :::::::::::::::::::::::::::::: CREATING DIFFERENT DIALOGs
     * :::::::::::::::::::::::::::::::::
     */

    public void showNoOrderDialog() {
        noOrderDialog = new Dialog(StockInActivity.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.user_instruction_dialog);
        noOrderDialog.setCancelable(false);
        TextView title = (TextView) noOrderDialog.findViewById(R.id.title);
        title.setText("Please state the reason for no Stock Audit");
        final EditText edReason = (EditText) noOrderDialog
                .findViewById(R.id.ed_input);
        Button submit = (Button) noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                noOrderDialog.cancel();
                String reason = "";
                reason = edReason.getText().toString();
                String timeStamp = Constants.dateString
                        + new SimpleDateFormat("_HHmmss").format(Calendar
                        .getInstance().getTime());
                timeStamp = timeStamp.replace("_", "");
                transDataHelperObj.insertNoStockAuditTable(timeStamp, reason);
                transDataHelperObj.insertToLocationTable("NS", timeStamp);
                new TRANS_SubmitStockAuditTask(StockInActivity.this, true, "SUBMIT")
                        .execute();
            }
        });
        noOrderDialog.show();
    }

    public void showGrpListDialog() {
        if (productGroupList.size() > 0) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            grpDialog = new Dialog(StockInActivity.this, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_from_list);
            grpDialog.setTitle("Please select an option");
            grpDialog.setCancelable(false);
            TextView title = (TextView) grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) grpDialog.findViewById(R.id.list);
            ProductGrpAdapter adapter1 = new ProductGrpAdapter(
                    StockInActivity.this, R.layout.product_list_child,
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
            Toast.makeText(StockInActivity.this,
                    "There are no items left.Please submit order.", 2000)
                    .show();
        }
    }

    public void showSubGrpListDialog() {
        if (productSubGroupList.size() > 0) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGrpDialog = new Dialog(StockInActivity.this, R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_from_list);
            subGrpDialog.setCancelable(false);
            TextView title = (TextView) subGrpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) subGrpDialog
                    .findViewById(R.id.list);
            ProductSubGrpAdapter adapter1 = new ProductSubGrpAdapter(
                    StockInActivity.this, R.layout.product_list_child,
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
                    StockInActivity.this,
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
            brandDialog = new Dialog(StockInActivity.this, R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_from_list);
            brandDialog.setCancelable(false);
            TextView title = (TextView) brandDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) brandDialog
                    .findViewById(R.id.list);
            ProductBrandAdapter adapter1 = new ProductBrandAdapter(
                    StockInActivity.this, R.layout.product_list_child,
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
                    StockInActivity.this,
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
            prodAdapter = new ProductMasterAdapter(StockInActivity.this,
                    R.layout.product_list_child, tempProductList);
            masterDialog = new Dialog(StockInActivity.this, R.style.PauseDialog);
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
                    filterButtonList.get(3).setText(
                            currentProductMasterObj.getDesc());
                    txtUOM1.setText(currentProductMasterObj.getUom1());
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
                    StockInActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            filterButtonList.get(3).setText("");
        }
    }

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

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            tempProductList.add(productMasterList.get(kk));
        }

    }

    public void showMRPDialog(ProductMasterDetails productObj) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = setupDataHelperObj.getMRPList(productCode, "");
        String[] tempSpinnerArray = new String[mrpSpinnerList.size() + 1];
        for (int k = 0; k < mrpSpinnerList.size(); k++) {
            tempSpinnerArray[k] = mrpSpinnerList.get(k).getMrpValue();
        }
        tempSpinnerArray[tempSpinnerArray.length - 1] = "FOC";
        spinnerAdapter = new ArrayAdapter(StockInActivity.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
        mrpSpinner.setAdapter(spinnerAdapter);
    }

    public void showSaleRateDialog(ProductMasterDetails productObj) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = setupDataHelperObj.getMRPList(productCode, "");
        String[] tempSpinnerArray = new String[mrpSpinnerList.size() + 1];
        for (int k = 0; k < mrpSpinnerList.size(); k++) {
            tempSpinnerArray[k] = mrpSpinnerList.get(k).getSaleRate();
        }
        tempSpinnerArray[tempSpinnerArray.length - 1] = "FOC";
        ArrayAdapter adapter = new ArrayAdapter(StockInActivity.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
        mrpSpinner.setAdapter(adapter);
    }

    public void calculateTotal() {
        double amount = 0;
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails currentObj = Constants.selectedProductMasterList
                        .get(ii);
                double qty = Double.parseDouble(currentObj.getQty());
                String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                        .getMrpValue() : currentObj.getAmount();
                double mrp = Double.parseDouble(mrpVal);
                double discount = Double.parseDouble(currentObj
                        .getTradeDiscnt());
                amount = amount + ((mrp * qty) - (mrp * qty * discount / 100));
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase(
                    "amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList
                        .size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList
                            .get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
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
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj
                            .getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }
        totalOrder.setText(currency + defaultFormat.format(amount));
    }

    public void showStockInOptionDialog() {
        stockInOptionDialog = new Dialog(StockInActivity.this,
                R.style.PauseDialog);
        stockInOptionDialog.setCancelable(false);
        stockInOptionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        stockInOptionDialog.setContentView(R.layout.stockout_dialog);
        TextView txtMsg = (TextView) stockInOptionDialog
                .findViewById(R.id.title);
        txtMsg.setText("Select an Option.");
        Button branch = (Button) stockInOptionDialog
                .findViewById(R.id.btn_branch);
        branch.setText("HQ/ \nInterBranch");
        branch.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                stockInOptionDialog.cancel();
                if (Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
                    Utils.showToast(mContext, "Feature not enabled.");
                    finish();
                } else {
                    transType = "BT";
                    if (Constants.orderFormDetailsObj.getBranchRDSTransfer()
                            .equalsIgnoreCase("yes")) {
                        Intent intent = new Intent(StockInActivity.this,
                                StockInFromBranch.class);
                        startActivity(intent);
                        finish();
                    } else {
                        showBranchHQDialog();
                    }
                }
            }
        });
        Button customer = (Button) stockInOptionDialog
                .findViewById(R.id.btn_cust);
        customer.setText("VENDOR/ \nPurchaseBill");
        customer.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                stockInOptionDialog.cancel();
                transType = "PB";
                showVendorDialog();
            }
        });
        stockInOptionDialog.show();
    }

    public void showVendorDialog() {
        vendorList = setupDataHelperObj.getVendorForStockOut();
        if (vendorList.size() > 0) {
            tempVendorList = new ArrayList<VendorDetails>();
            reInitialiseVendorList();
            adapterVendor = new VendorAdapter(StockInActivity.this,
                    R.layout.customer_list_child, tempVendorList);

            vendorListDialog = new Dialog(StockInActivity.this,
                    R.style.PauseDialog);
            vendorListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            vendorListDialog.setContentView(R.layout.choose_customer_search);
            vendorListDialog.setCancelable(false);
            TextView title = (TextView) vendorListDialog
                    .findViewById(R.id.title);
            title.setText("Please select a Vendor");
            EditText searchText = (EditText) vendorListDialog
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
                        reInitialiseVendorList();
                    }
                    lastStr = str;
                    filterVendorArray(str.length(), str);
                    adapterVendor.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });

            ListView dialogList = (ListView) vendorListDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(adapterVendor);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    VendorDetails currentObj = tempVendorList.get(arg2);
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    vendorListDialog.cancel();
                    btnCustomer.setText(currentObj.getVendorName());
                    btnCustomer.setEnabled(false);
                    Constants.selectedVendor = currentObj;
                    btnNoOrder.setEnabled(true);
                    for (int i = 0; i < filterButtonList.size(); i++) {
                        if (filterButtonList.get(i) != null) {
                            filterButtonList.get(i).setEnabled(true);
                        }
                    }
                    btnContinue.setEnabled(true);
                    btnOrder.setEnabled(true);
                }
            });

            Button addCustomer = (Button) vendorListDialog
                    .findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.INVISIBLE);
            addCustomer.setText("    Cancel    ");
            vendorListDialog.show();
        } else {
            Toast.makeText(mContext, "No existing Vendor found.", Toast.LENGTH_LONG).show();
        }

    }

    public void showBranchHQDialog() {
        branchHQList = setupDataHelperObj.getBranchForStockIn();
        if (branchHQList.size() > 0) {
            tempBranchHQList = new ArrayList<BranchMasterDetails>();
            reInitialiseBranchHQList();
            adapterBranchHQ = new BranchAdapter(StockInActivity.this,
                    R.layout.customer_list_child, tempBranchHQList);

            branchHQDialog = new Dialog(StockInActivity.this,
                    R.style.PauseDialog);
            branchHQDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            branchHQDialog.setContentView(R.layout.choose_customer_search);
            branchHQDialog.setCancelable(false);
            TextView title = (TextView) branchHQDialog.findViewById(R.id.title);
            title.setText("Please select an Option");
            EditText searchText = (EditText) branchHQDialog
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
                        reInitialiseVendorList();
                    }
                    lastStr = str;
                    filterBranchHQArray(str.length(), str);
                    adapterBranchHQ.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });

            ListView dialogList = (ListView) branchHQDialog
                    .findViewById(R.id.list);
            dialogList.setAdapter(adapterBranchHQ);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    BranchMasterDetails currentObj = tempBranchHQList.get(arg2);
                    if (currentObj.getHq().equalsIgnoreCase("no")) {
                        getWindow()
                                .setSoftInputMode(
                                        WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        branchHQDialog.cancel();
                        Constants.selectedBranch = currentObj;
                        Intent intent = new Intent(StockInActivity.this,
                                StockInFromBranch.class);
                        startActivity(intent);
                        finish();
                    } else {
                        getWindow()
                                .setSoftInputMode(
                                        WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        branchHQDialog.cancel();

                        btnCustomer.setText(currentObj.getBranchName());
                        btnCustomer.setEnabled(false);
                        Constants.selectedBranch = currentObj;
                        btnNoOrder.setEnabled(true);
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

            Button addCustomer = (Button) branchHQDialog
                    .findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.INVISIBLE);
            addCustomer.setText("    Cancel    ");
            branchHQDialog.show();
        } else {
            Toast.makeText(mContext, "No existing Branch/HQ found.", 2000)
                    .show();
            finish();
        }

    }


    public void filterVendorArray(int strCnt, String charVal) {
        int size = tempVendorList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempVendorList.get(ii).getVendorName().length() >= strCnt) {
                if (tempVendorList.get(ii).getVendorName().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempVendorList.remove(tempVendorList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempVendorList.remove(tempVendorList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void filterBranchHQArray(int strCnt, String charVal) {
        int size = tempBranchHQList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempBranchHQList.get(ii).getBranchName().length() >= strCnt) {
                if (tempBranchHQList.get(ii).getBranchName().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempBranchHQList.remove(tempBranchHQList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempBranchHQList.remove(tempBranchHQList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void reInitialiseVendorList() {
        tempVendorList.removeAll(tempVendorList);
        int size = tempVendorList.size();
        int size1 = vendorList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < vendorList.size(); kk++) {
            tempVendorList.add(vendorList.get(kk));
        }

    }

    public void reInitialiseBranchHQList() {
        tempBranchHQList.removeAll(tempBranchHQList);
        int size = tempBranchHQList.size();
        int size1 = branchHQList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < branchHQList.size(); kk++) {
            tempBranchHQList.add(branchHQList.get(kk));
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
                        productGroupList = setupDataHelperObj
                                .getProductGroupList(false);
                        // removeRepeatedGroupItems();
                        break;
                    case 2:
                        productSubGroupList = setupDataHelperObj
                                .getProductSubGroupList(param, false);
                        // removeRepeatedSubGroupItems();
                        break;
                    case 3:
                        productBrandList = setupDataHelperObj.getProductBrandList(
                                param, false);
                        // removeRepeatedBrandItems();
                        break;
                    case 4:
                        productMasterList = setupDataHelperObj
                                .getProductMasterListIgnoringStock(param, filterNo);
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

}
