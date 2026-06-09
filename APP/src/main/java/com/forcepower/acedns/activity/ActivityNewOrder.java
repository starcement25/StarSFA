package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.TypedValue;
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
import android.widget.FrameLayout;
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
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Set;

import static com.forcepower.acedns.constants.Constants.defaultFormat;

import androidx.annotation.NonNull;

public class ActivityNewOrder extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;

    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewTotalOrderValue = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewNoofOrder = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewUOM1 = null;

    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCustomerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewRouteName = null;

    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonNoOrder = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonAddtoCart = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonCheckOut = null;

    public boolean isTD = true;
    public boolean isPremium = false;
    EditText edAmount;
    LinearLayout customerLayout, filterLayout, qty_mrpLayout, stk_tdLayout, amountLayout, tdNewLayout;
    Spinner mrpSpinner;
    EditText edQty, edSaleRate, edClsngStk, edTd;
    Dialog grpDialog, subGrpDialog, brandDialog, masterDialog, uomDialog;
    RadioButton radioButton;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;
    int mrpSelected;
    ArrayList<MRPDetails> mrpSpinnerList;
    ArrayList<String> filterList;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;
    ArrayList<ProductMasterDetails> productMasterSizeList;
    ArrayList<Button> filterButtonList;
    ProductMasterDetails currentProductMasterObj;
    int filterNo;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected, lastProductSelected = false;
    ProductMasterAdapter prodAdapter;
    ProductGrpAdapter groupAdapter;
    ProductSubGrpAdapter subGroupAdapter;
    ProductBrandAdapter brandAdapter;
    String lastStr = "";
    ArrayList<ProductMasterDetails> tempProductList;
    ArrayList<ProductGroupDetails> tempProductGroupList;
    ArrayList<ProductSubGrpDetails> tempProductSubGroupList;
    ArrayList<ProductBrandDetails> tempProductBrandList;
    int lastProdPos = 0;
    ArrayAdapter spinnerAdapter;
    String trdDisc = "0";
    boolean carryInSales = false;
    ProgressDialog ploader;
    Handler orderDataHandler;
    boolean rpeatedProductEntry = false;
    TextView txtTD;
    String[] values;
    HashMap<String, ArrayList<EditText>> mProductMasterInputList;
    HashMap<String, Spinner> mProductMasterInputListUom;
    private String mProductGroupCode = "";
    private String mDnsProductCode = "", uom1 = "", uom2 = "";
    private int mCartCount = 0;
    private String mProductGroupName = "";
    private String mProductName = "";

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
        mContext = ActivityNewOrder.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Constants.selectedProductMasterList = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();

        filterButtonList = new ArrayList<>();

        InitializeView();
        DrawLayout();

        orderDataHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                ActivityNewOrder.this.runOnUiThread(() -> {
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
                                Constants.mVerticalValue = values[0];
                            }
                            break;
                        case 6:
                            if (mProductName.contains("-")) {
                                String[] mSplitProductName = mProductName.split("-");
                                if (mSplitProductName.length >= 2) {
                                    mProductName = mSplitProductName[0].trim();
                                }
                            }
                            String productdetails = "Category - " + mProductGroupName + "\nProduct - " + mProductName;
                            if (!productMasterSizeList.isEmpty()) {
                                RemoveRepeatedProduct();
                            }
                            if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes")) {
                                ShowProductListDialogForUomSelect(productdetails);
                            } else {
                                ShowProductListDialog(productdetails);
                            }
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
            mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
            CalculateTotal();
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
            spinnerAdapter = new ArrayAdapter(ActivityNewOrder.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
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
     * ::::::::::::::::::::::::::::::::::: VIEW RELATED OPERATIONS :::::::::::::::::::::::::::::::::::
     */

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        amountLayout = findViewById(R.id.amount_layout);
        edAmount = findViewById(R.id.ed_amt);
        edAmount.setTextSize(15);
        if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase("yes")) {
            amountLayout.setVisibility(View.VISIBLE);
        } else {
            amountLayout.setVisibility(View.GONE);
        }
        edAmount.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                if (edSaleRate != null) {
                    edSaleRate.setEnabled(s.toString().isEmpty());
                }
            }
        });

        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        customerLayout = findViewById(R.id.customer_layout);
        if (Constants.selectedProductMasterList == null || Constants.selectedProductMasterList.isEmpty()) {
            customerLayout.setVisibility(View.INVISIBLE);
        }
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        filterLayout = findViewById(R.id.filter_layout);
        qty_mrpLayout = findViewById(R.id.qty_mrp_layout);
        stk_tdLayout = findViewById(R.id.stk_td_layout);
        tdNewLayout = findViewById(R.id.td_layout);

        mTextViewTotalOrderValue = findViewById(R.id.txt_total);
        mTextViewNoofOrder = findViewById(R.id.txt_order_count);
        mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
        CalculateTotal();

        mButtonAddtoCart = findViewById(R.id.btn_continue);
        mButtonAddtoCart.setOnClickListener(ActivityNewOrder.this);
        mButtonAddtoCart.setTag(101);
        mButtonAddtoCart.setVisibility(View.GONE);

        mButtonCheckOut = findViewById(R.id.btn_order_form);
        mButtonCheckOut.setTag(102);
        mButtonCheckOut.setOnClickListener(ActivityNewOrder.this);
        mButtonCheckOut.setBackgroundColor(Color.parseColor("#FB9433"));
        mButtonCheckOut.setTextSize(13);
        mButtonCheckOut.setText("CHECK OUT");
        mButtonCheckOut.setTypeface(null, Typeface.BOLD);
        mButtonCheckOut.setPadding(0, 12, 0, 12);

        mButtonNoOrder = findViewById(R.id.no_ordr);
        mButtonNoOrder.setTag(104);
        mButtonNoOrder.setOnClickListener(ActivityNewOrder.this);
        mButtonNoOrder.setEnabled(true);

        mButtonBack = findViewById(R.id.back);
        mButtonBack.setTag(105);
        mButtonBack.setOnClickListener(ActivityNewOrder.this);

        mTextViewCustomerName = findViewById(R.id.textViewCustomername);
        mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());

        mTextViewRouteName = findViewById(R.id.textViewRoute);
        mTextViewRouteName.setText("Route : " + Constants.selectedCustomer.getRouteName());
    }

    public void DrawLayout() {
        drawFilterLayout();
        drawQtyMrpLayout();
        drawStkTdVatLayout();
        enableDisableLayout(false);
    }

    @SuppressLint({"RtlHardcoded", "UseCompatLoadingForDrawables"})
    public void drawFilterLayout() {
        filterList = mAceDnsDatabase.getFilterList();
        for (int ii = 1; ii < filterList.size(); ii++) {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
                buttonParams.gravity = Gravity.CENTER_VERTICAL;
                filterButton.setLayoutParams(buttonParams);
                filterButton.setTag(ii);
                filterButton.setHorizontallyScrolling(true);
                filterButton.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
                filterButton.setText(filterList.get(ii));
                filterButton.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
                filterButton.setSingleLine(true);
                filterButton.setOnClickListener(this);
                buttonLayout.addView(filterButton);
                filterLayout.addView(buttonLayout);
                filterButtonList.add(filterButton);
            } else {
                filterButtonList.add(null);
            }
        }
    }

    @SuppressLint({"SetTextI18n", "RtlHardcoded"})
    public void drawQtyMrpLayout() {
        LinearLayout qtyLayout = new LinearLayout(ActivityNewOrder.this);
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
                    InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                    imm.hideSoftInputFromWindow(edQty.getWindowToken(), 0);
                    getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    return true;
                }
            }
            return false;
        });
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
            LinearLayout mrpLayout = new LinearLayout(ActivityNewOrder.this);
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
                public void onNothingSelected(AdapterView<?> arg0) {
                }
            });

            mrpLayout.addView(mrpSpinner);
            qty_mrpLayout.addView(mrpLayout);
        } else if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
            LinearLayout mrpLayout = new LinearLayout(ActivityNewOrder.this);
            mrpLayout.setOrientation(LinearLayout.HORIZONTAL);
            LinearLayout.LayoutParams params1 = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
            mrpLayout.setLayoutParams(params1);

            TextView txtMrp = new TextView(mContext);
            txtMrp.setText(" Sale Rate : ");
            txtMrp.setTextColor(Color.parseColor("#003399"));
            mrpLayout.addView(txtMrp);
            if (Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("input")) {
                edSaleRate = new EditText(mContext);
                edSaleRate.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
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
                        public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                        }

                        @Override
                        public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                        }

                        @Override
                        public void afterTextChanged(Editable s) {
                            if (edAmount != null) {
                                edAmount.setEnabled(s.toString().isEmpty());
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
                    public void onNothingSelected(AdapterView<?> arg0) {
                    }
                });
                mrpLayout.addView(mrpSpinner);
                qty_mrpLayout.addView(mrpLayout);
            }

        }
    }

    @SuppressLint({"SetTextI18n", "RtlHardcoded"})
    public void drawStkTdVatLayout() {
        if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes") || carryInSales) {
            LinearLayout stkLayout = new LinearLayout(ActivityNewOrder.this);
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
            if (Constants.orderFormDetailsObj.getPremium().equalsIgnoreCase("yes")) {
                LinearLayout.LayoutParams parentparams = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
                tdNewLayout.setOrientation(LinearLayout.VERTICAL);
                tdNewLayout.setLayoutParams(parentparams);

                LinearLayout.LayoutParams childparams = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, 0, 1);

                LinearLayout radiaoButtonLayout = new LinearLayout(ActivityNewOrder.this);
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
                    } else {
                        radioButton.setText("Premium");
                        radioButton.setTag(151);
                        radioButton.setTextSize(10);
                    }
                    radioButton.setOnClickListener(this);
                    radioButton.setTextColor(Color.parseColor("#003399"));
                    rprms = new RadioGroup.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);
                    rgp.addView(radioButton, rprms);
                }

                rgp.setOnCheckedChangeListener((group, checkedId) -> {
                    radioButton = findViewById(checkedId);
                    if (radioButton.getText().equals("TD")) {
                        txtTD.setText("TD");
                        isTD = true;
                        isPremium = false;
                    } else {
                        txtTD.setText("Premium");
                        isTD = false;
                        isPremium = true;
                    }
                });

                radiaoButtonLayout.addView(rgp);
                tdNewLayout.addView(radiaoButtonLayout);

                LinearLayout tdinputLayout = new LinearLayout(ActivityNewOrder.this);
                tdinputLayout.setOrientation(LinearLayout.VERTICAL);
                tdinputLayout.setLayoutParams(childparams);

                LinearLayout tdLayout = new LinearLayout(ActivityNewOrder.this);
                tdLayout.setOrientation(LinearLayout.HORIZONTAL);
                tdLayout.setLayoutParams(childparams);

                txtTD = new TextView(mContext);
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
                            InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                            imm.hideSoftInputFromWindow(edTd.getWindowToken(), 0);
                            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                            return true;
                        }
                    }
                    return false;
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
            } else {
                LinearLayout tdLayout = new LinearLayout(ActivityNewOrder.this);
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
                            InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                            imm.hideSoftInputFromWindow(edTd.getWindowToken(), 0);
                            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                            return true;
                        }
                    }
                    return false;
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

        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes") && (Constants.orderFormDetailsObj.getVatCalcOn().equalsIgnoreCase("sku"))) {
            LinearLayout tdLayout = new LinearLayout(ActivityNewOrder.this);
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
                        InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
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
        qty_mrpLayout.setVisibility(View.INVISIBLE);
        stk_tdLayout.setVisibility(View.INVISIBLE);
        tdNewLayout.setVisibility(View.INVISIBLE);
    }

    public void showMRPDialog(ProductMasterDetails productObj, String selectedUOM) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = mAceDnsDatabase.getMRPList(productCode, selectedUOM);
        String[] tempSpinnerArray = new String[mrpSpinnerList.size()];
        for (int k = 0; k < mrpSpinnerList.size(); k++) {
            tempSpinnerArray[k] = mrpSpinnerList.get(k).getMrpValue();
        }
        spinnerAdapter = new ArrayAdapter(ActivityNewOrder.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
        mrpSpinner.setAdapter(spinnerAdapter);
    }

    public void showSaleRateDialog(ProductMasterDetails productObj, String selectedUOM) {
        String productCode = productObj.getProdCode();
        mrpSpinnerList = mAceDnsDatabase.getMRPList(productCode, selectedUOM);
        String[] tempSpinnerArray = new String[mrpSpinnerList.size()];
        for (int k = 0; k < mrpSpinnerList.size(); k++) {
            tempSpinnerArray[k] = mrpSpinnerList.get(k).getSaleRate();
        }
        ArrayAdapter adapter = new ArrayAdapter(ActivityNewOrder.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
        mrpSpinner.setAdapter(adapter);
    }

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
                    if (selectedGrp != null) {
                        mProductGroupCode = selectedGrp.getGroupCode();
                        mProductGroupName = selectedGrp.getGroupName();
                        prepareOrderData(2, selectedGrp.getGroupCode());
                    } else {
                        Toast.makeText(ActivityNewOrder.this, "Please select the parent category", Toast.LENGTH_LONG).show();
                    }
                    break;
                case 3:
                    lastProdPos = 0;
                    filterButtonList.get(3).setEnabled(true);
                    lastProductSelected = false;
                    if (selectedSubGrp != null) {
                        prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                    } else {
                        Toast.makeText(ActivityNewOrder.this, "Please select the parent category", Toast.LENGTH_LONG).show();
                    }
                    break;
                case 4:
                    switch (filterNo) {
                        case 1:
                            prepareOrderData(4, "");
                            break;
                        case 2:
                            if (selectedGrp != null) {
                                mProductGroupCode = selectedGrp.getGroupCode();
                                mProductGroupName = selectedGrp.getGroupName();
                                prepareOrderData(4, selectedGrp.getGroupCode());
                            } else {
                                Toast.makeText(ActivityNewOrder.this, "Please select the parent category", Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(ActivityNewOrder.this, "Please select the parent category", Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                prepareOrderData(4, selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(ActivityNewOrder.this, "Please select the parent category", Toast.LENGTH_LONG).show();
                            }
                            break;
                    }
            }
        }
        if (clkdView == mButtonAddtoCart) {
            if (currentProductMasterObj != null) {
                boolean qty_status, mrp_status = true, tdStatus = true;
                if (!edQty.getText().toString().isEmpty() && !edQty.getText().toString().equalsIgnoreCase("0") && !edQty.getText().toString().equalsIgnoreCase(".") && Double.parseDouble(edQty.getText().toString()) != 0) {
                    if (carryInSales) {
                        if ((Double.parseDouble(edQty.getText().toString()) <= Double.parseDouble(edClsngStk.getText().toString()))) {
                            currentProductMasterObj.setQty(edQty.getText().toString());
                            qty_status = true;
                        } else {
                            qty_status = false;
                        }
                    } else {
                        currentProductMasterObj.setQty(edQty.getText().toString());
                        qty_status = true;
                    }
                    if (currentProductMasterObj.getSelectedUOM() != -1) {
                        if (currentProductMasterObj.getSelectedUOM() == 0) {
                            double finalQty = Double.parseDouble(currentProductMasterObj.getQty()) * Double.parseDouble(currentProductMasterObj.getConversionFactor());
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
                            currentProductMasterObj.setMrpCode(mrpSpinnerList.get(mrpSelected).getMrpCode());
                            currentProductMasterObj.setMrpValue(mrpSpinnerList.get(mrpSelected).getMrpValue());
                            double slRt = Double.parseDouble(mrpSpinnerList.get(mrpSelected).getMrpValue());
                            double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                            if (qtyD > 0) {
                                currentProductMasterObj.setAmount(defaultFormat.format(slRt * qtyD));
                            }
                        } else {
                            currentProductMasterObj.setMrpCode(mrpSpinnerList.get(mrpSelected).getMrpCode());
                            currentProductMasterObj.setMrpValue(mrpSpinnerList.get(mrpSelected).getSaleRate());
                            double slRt = Double.parseDouble(mrpSpinnerList.get(mrpSelected).getSaleRate());
                            double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
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
                    if (!edSaleRate.getText().toString().isEmpty() && !edSaleRate.getText().toString().equalsIgnoreCase(".")) {
                        currentProductMasterObj.setMrpCode("0");
                        currentProductMasterObj.setMrpValue(edSaleRate.getText().toString());
                        double slRt = Double.parseDouble(edSaleRate.getText().toString());
                        double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
                        if (qtyD > 0) {
                            currentProductMasterObj.setAmount(defaultFormat.format(slRt * qtyD));
                        }
                    } else {
                        if (edAmount != null && !edAmount.getText().toString().isEmpty() && !edAmount.getText().toString().equalsIgnoreCase(".")) {
                            double amountD = Double.parseDouble(edAmount.getText().toString());
                            double qtyD = Double.parseDouble((qty_status ? edQty.getText().toString() : "1"));
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
                if (edTd != null) {
                    if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
                        currentProductMasterObj.setVat("0");
                        String td = "0";
                        String premium = "0";
                        if (!edTd.getText().toString().isEmpty() && !edTd.getText().toString().equalsIgnoreCase(".")) {
                            td = edTd.getText().toString();
                            premium = edTd.getText().toString();
                        }
                        if (isTD && !isPremium) {
                            if (Double.parseDouble(td) <= 100) {
                                currentProductMasterObj.setTradeDiscnt(td);
                                currentProductMasterObj.setIsTradeDiscount(true);
                            } else {
                                tdStatus = false;
                                Toast.makeText(ActivityNewOrder.this, "Trade Discount cannot be more than 100 %", Toast.LENGTH_LONG).show();
                            }
                        }
                        if (!isTD && isPremium) {
                            if (Double.parseDouble(premium) <= 100) {
                                currentProductMasterObj.setPremium(premium);
                                currentProductMasterObj.setIsTradeDiscount(false);
                            } else {
                                Toast.makeText(ActivityNewOrder.this, "Premium cannot be more than 100 %", Toast.LENGTH_LONG).show();
                            }
                        }
                    } else {
                        currentProductMasterObj.setTradeDiscnt("0");
                        String vat;
                        if (!edTd.getText().toString().isEmpty() && !edTd.getText().toString().equalsIgnoreCase(".")) {
                            vat = edTd.getText().toString();
                            currentProductMasterObj.setVat(vat);
                        }
                    }
                } else {
                    currentProductMasterObj.setTradeDiscnt("0");
                    currentProductMasterObj.setVat("0");
                }
                if (qty_status && mrp_status && tdStatus) {
                    customerLayout.setVisibility(View.VISIBLE);
                    if (Constants.selectedProductMasterList.size() % 2 == 0) {
                        customerLayout.setBackgroundColor(Color.YELLOW);
                    } else {
                        customerLayout.setBackgroundColor(Color.GREEN);
                    }
                    Constants.selectedProductMasterList.add(currentProductMasterObj);
                    mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
                    CalculateTotal();

                    if (carryInSales) {
                        mAceDnsTransactionDatabase.reduceClStkProductWise(currentProductMasterObj);
                    }

                    Toast.makeText(ActivityNewOrder.this, "Product has been added to cart.", Toast.LENGTH_LONG).show();
                    currentProductMasterObj = null;
                    if (mrpSpinner != null) {
                        String[] tempSpinnerArray = new String[0];
                        spinnerAdapter = new ArrayAdapter(ActivityNewOrder.this, android.R.layout.simple_spinner_item, tempSpinnerArray);
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
                            if (lastGrpSelected && lastSubGroupSelected && lastProductSelected) {
                                mButtonAddtoCart.setEnabled(false);
                            }
                            break;
                        case 4:
                            if (lastGrpSelected && lastSubGroupSelected && lastBrandSelected && lastProductSelected) {
                                mButtonAddtoCart.setEnabled(false);
                            }
                            break;
                    }

                    switch (filterNo) {
                        case 1:
                            if (lastProductSelected) {
                                filterButtonList.get(3).setText("No product left..");
                                filterButtonList.get(3).setEnabled(false);
                            }
                        case 2:
                            if (lastProductSelected) {
                                Constants.selectedGroupList.add(selectedGrp);
                                filterButtonList.get(3).setText("No product left.Select a different parent category.");
                                filterButtonList.get(3).setEnabled(false);
                            }
                        case 3:
                            if (lastProductSelected) {
                                Constants.selectedSubGroupList.add(selectedSubGrp);
                                filterButtonList.get(3).setText("No product left.Select a different parent category.");
                                filterButtonList.get(3).setEnabled(false);
                                if (lastSubGroupSelected) {
                                    Constants.selectedGroupList.add(selectedGrp);
                                    filterButtonList.get(1).setText("No product left.Select a different parent category.");
                                    filterButtonList.get(1).setEnabled(false);
                                }
                            }
                            break;
                        case 4:
                            if (lastProductSelected) {
                                Constants.selectedBrandList.add(selectedBrand);
                                filterButtonList.get(3).setText("No product left.Select a different parent category.");
                                filterButtonList.get(3).setEnabled(false);
                                if (lastBrandSelected) {
                                    Constants.selectedSubGroupList.add(selectedSubGrp);
                                    filterButtonList.get(2).setText("No product left.Select a different parent category.");
                                    filterButtonList.get(2).setEnabled(false);
                                    if (lastSubGroupSelected) {
                                        Constants.selectedGroupList.add(selectedGrp);
                                        filterButtonList.get(1).setText("No product left.Select a different parent category.");
                                        filterButtonList.get(1).setEnabled(false);
                                    }
                                }
                            }
                            break;
                    }

                } else {
                    if (!qty_status) {
                        Toast.makeText(ActivityNewOrder.this, "Please provide valid quantity.", Toast.LENGTH_LONG).show();
                    } else if (!mrp_status) {
                        if (Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown") || Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                            Toast.makeText(ActivityNewOrder.this, "Error in mrp data. Please Synchronize Data.", Toast.LENGTH_LONG).show();
                        } else {
                            Toast.makeText(ActivityNewOrder.this, "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                        }
                    } else {
                        Toast.makeText(ActivityNewOrder.this, "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                    }
                }
            } else {
                Toast.makeText(ActivityNewOrder.this, "Please select a product", Toast.LENGTH_LONG).show();
            }
            edQty.setText("");
            mTextViewUOM1.setText("");
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
            if (!Constants.selectedProductMasterList.isEmpty()) {
                Intent intent = new Intent(ActivityNewOrder.this, OrderConfirmationActivity.class);
                intent.putExtra("CARRY_IN", carryInSales);
                startActivity(intent);
            } else {
                Toast.makeText(ActivityNewOrder.this, "Please select a product", Toast.LENGTH_LONG).show();
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
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void ShowNoOrderDialog() {
        final Dialog noOrderDialog = new Dialog(ActivityNewOrder.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.user_instruction_dialog);
        noOrderDialog.setCancelable(false);
        TextView title = noOrderDialog.findViewById(R.id.title);
        title.setText("Please state the reason for no order");
        final EditText edReason = noOrderDialog.findViewById(R.id.ed_input);
        final Button submit = noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            noOrderDialog.cancel();
            new GPSTracker(mContext);
            boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
            if (isTimeAutomatic) {
                String reason = "";
                reason = edReason.getText().toString();
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                mAceDnsTransactionDatabase.insertToOrderHeaderTable("NO", reason, timeStamp, "", "", "", "", "NO", "", "", "", "");
                mAceDnsTransactionDatabase.insertToLocationTable("NO", timeStamp);
                new TRANS_SubmitOrderTask(ActivityNewOrder.this, true).execute();
            } else {
                Utils.showSettingsAlertToChangeTimeZone(mContext);
            }
        });
        noOrderDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void showGrpListDialog() {
        if (!productGroupList.isEmpty()) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            groupAdapter = new ProductGrpAdapter(ActivityNewOrder.this, R.layout.product_list_child, tempProductGroupList);
            grpDialog = new Dialog(ActivityNewOrder.this, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_with_search);
            grpDialog.setTitle("Please select an option");
            grpDialog.setCancelable(false);
            TextView title = grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = grpDialog.findViewById(R.id.autoCompleteTextView1);
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
                        reInitialiseProductGroupList();
                    }
                    lastStr = str;
                    filterProductGroupArray(str.length(), str);
                    groupAdapter.notifyDataSetChanged();
                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = grpDialog.findViewById(R.id.list);
            dialogList.setAdapter(groupAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                grpDialog.cancel();
                selectedGrp = tempProductGroupList.get(arg2);
                mProductGroupCode = selectedGrp.getGroupCode();
                mProductGroupName = selectedGrp.getGroupName();
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

            });
            Button cancel = grpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> grpDialog.cancel());
            grpDialog.show();
        } else {
            Toast.makeText(ActivityNewOrder.this, "There are no items left.Please submit order.", Toast.LENGTH_LONG).show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void showSubGrpListDialog() {
        if (!productSubGroupList.isEmpty()) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGroupAdapter = new ProductSubGrpAdapter(ActivityNewOrder.this, R.layout.product_list_child, tempProductSubGroupList);
            subGrpDialog = new Dialog(ActivityNewOrder.this, R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_with_search);
            subGrpDialog.setCancelable(false);
            TextView title = subGrpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = subGrpDialog.findViewById(R.id.autoCompleteTextView1);
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
                        reInitialiseProductSubGroupList();
                    }
                    lastStr = str;
                    filterProductSubGroupArray(str.length(), str);
                    subGroupAdapter.notifyDataSetChanged();
                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = subGrpDialog.findViewById(R.id.list);
            dialogList.setAdapter(subGroupAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                subGrpDialog.cancel();
                selectedSubGrp = tempProductSubGroupList.get(arg2);
                filterButtonList.get(1).setText(selectedSubGrp.getSubGrpName());
                switch (filterNo) {
                    case 3:
                        prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                        break;
                    case 4:
                        prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                        break;
                }
            });
            Button cancel = subGrpDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> subGrpDialog.cancel());
            subGrpDialog.show();
        } else {
            Toast.makeText(ActivityNewOrder.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    @SuppressLint("SetTextI18n")
    public void showBrandListDialog() {
        if (!productBrandList.isEmpty()) {
            if (productBrandList.size() == 1) {
                lastBrandSelected = true;
            }
            brandAdapter = new ProductBrandAdapter(ActivityNewOrder.this, R.layout.product_list_child, tempProductBrandList);
            brandDialog = new Dialog(ActivityNewOrder.this, R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_with_search);
            brandDialog.setCancelable(false);
            TextView title = brandDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = brandDialog.findViewById(R.id.autoCompleteTextView1);
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
                        reInitialiseProductBrandList();
                    }
                    lastStr = str;
                    filterProductBrandArray(str.length(), str);
                    brandAdapter.notifyDataSetChanged();
                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = brandDialog.findViewById(R.id.list);
            dialogList.setAdapter(brandAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                brandDialog.cancel();
                selectedBrand = tempProductBrandList.get(arg2);
                filterButtonList.get(2).setText(selectedBrand.getBrandName());
                prepareOrderData(4, selectedBrand.getBrandCode());
            });
            Button cancel = brandDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(arg0 -> brandDialog.cancel());
            brandDialog.show();
        } else {
            Toast.makeText(ActivityNewOrder.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    @SuppressLint("SetTextI18n")
    public void showMasterListDialog() {
        if (!productMasterList.isEmpty()) {
            if (productMasterList.size() == 1) {
                lastProductSelected = true;
            }
            prodAdapter = new ProductMasterAdapter(ActivityNewOrder.this, R.layout.product_list_child, tempProductList);
            masterDialog = new Dialog(ActivityNewOrder.this, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = masterDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = masterDialog.findViewById(R.id.autoCompleteTextView1);
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
                        reInitialiseProductList();
                    }
                    lastStr = str;
                    filterProductArray(str.length(), str);
                    prodAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = masterDialog.findViewById(R.id.list);
            dialogList.setAdapter(prodAdapter);
            if (lastProdPos != 0) {
                dialogList.setSelection(lastProdPos - 1);
            } else {
                dialogList.setSelection(0);
            }
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                masterDialog.cancel();
                lastProdPos = arg2;
                currentProductMasterObj = tempProductList.get(arg2);
                mDnsProductCode = currentProductMasterObj.getDnsProdCode();
                mProductName = currentProductMasterObj.getDesc().trim();
                uom1 = currentProductMasterObj.getUom1().trim();
                uom2 = currentProductMasterObj.getUom2().trim();
                filterButtonList.get(3).setText(currentProductMasterObj.getDesc());
                if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
                    prepareOrderData(6, "");
                } else {
                    if (Constants.productDetailsObj.getUomWiseMRP().equalsIgnoreCase("yes")) {
                        ArrayList<String> uomList = new ArrayList<>();
                        uomList.add(currentProductMasterObj.getUom1());
                        uomList.add(currentProductMasterObj.getUom2());
                        showUOMDialog(uomList);
                    } else {
                        if (Constants.productDetailsObj.getSecondaryUnit().equalsIgnoreCase("yes")) {
                            mTextViewUOM1.setText(currentProductMasterObj.getSecondaryUnit());
                        } else {
                            mTextViewUOM1.setText(currentProductMasterObj.getUom1());
                        }
                        if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                            showMRPDialog(currentProductMasterObj, "");
                        }
                        if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                            showSaleRateDialog(currentProductMasterObj, "");
                        }
                        if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes") || carryInSales) {
                            showClosingStock(currentProductMasterObj);
                        }
                        if (Constants.orderFormDetailsObj.getPreviousOrder().equalsIgnoreCase("yes")) {
                            ShowPreviousOrderDialog();
                        }
                    }
                }
            });
            Button btnCancel = masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(v -> masterDialog.cancel());

            masterDialog.show();
        } else {
            Toast.makeText(ActivityNewOrder.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            filterButtonList.get(3).setText("");
        }
    }

    public void RemoveRepeatedSpecialProductItems() {
        for (int count = 0; count < Constants.selectedProductMasterList.size(); count++) {
            ProductMasterDetails currentItem = Constants.selectedProductMasterList.get(count);
            String dnsprodcode = currentItem.getDnsProdCode();

            int packSize = 0;

            for (int innercount = 0; innercount < Constants.selectedProductMasterList.size(); innercount++) {
                if (dnsprodcode.equalsIgnoreCase(Constants.selectedProductMasterList.get(innercount).getDnsProdCode())) {
                    packSize += 1;
                }
            }
            for (int x = 0; x < productMasterList.size(); x++) {
                ProductMasterDetails obj = productMasterList.get(x);
                if (dnsprodcode.equalsIgnoreCase(obj.getDnsProdCode())) {
                    int totalpacksize = Integer.parseInt(obj.getPackSize());
                    if (packSize == totalpacksize) {
                        productMasterList.remove(x);
                    }
                }
            }
        }
    }

    public void removeRepeatedProductItems() {
        for (int kk = 0; kk < Constants.selectedProductMasterList.size(); kk++) {
            ProductMasterDetails currentItem = Constants.selectedProductMasterList.get(kk);
            for (int x = 0; x < productMasterList.size(); x++) {
                if (productMasterList.get(x).getProdCode().equalsIgnoreCase(currentItem.getProdCode())) {
                    productMasterList.remove(productMasterList.get(x));
                }
            }
        }
        int size = productMasterList.size();
        System.out.println(size);
    }

    public void removeRepeatedBrandItems() {
        for (int kk = 0; kk < Constants.selectedBrandList.size(); kk++) {
            ProductBrandDetails currentItem = Constants.selectedBrandList.get(kk);
            for (int x = 0; x < productBrandList.size(); x++) {
                if (productBrandList.get(x).getBrandCode().equalsIgnoreCase(currentItem.getBrandCode())) {
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
                if (productGroupList.get(x).getGroupCode().equalsIgnoreCase(currentItem.getGroupCode())) {
                    productGroupList.remove(productGroupList.get(x));
                }
            }
        }
    }

    public void removeRepeatedSubGroupItems() {
        for (int kk = 0; kk < Constants.selectedSubGroupList.size(); kk++) {
            ProductSubGrpDetails currentItem = Constants.selectedSubGroupList.get(kk);
            for (int x = 0; x < productSubGroupList.size(); x++) {
                if (productSubGroupList.get(x).getSubGrpCode().equalsIgnoreCase(currentItem.getSubGrpCode())) {
                    productSubGroupList.remove(productSubGroupList.get(x));
                }
            }
        }
    }

    public void filterProductArray(int strCnt, String charVal) {
        int size = tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductList.get(ii).getDesc().length() >= strCnt) {
                if (!tempProductList.get(ii).getDesc().toUpperCase().contains(charVal.toUpperCase())) {
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
                if (!tempProductGroupList.get(ii).getGroupName().toUpperCase().contains(charVal.toUpperCase())) {
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
                if (!tempProductSubGroupList.get(ii).getSubGrpName().toUpperCase().contains(charVal.toUpperCase())) {
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
                if (!tempProductBrandList.get(ii).getBrandName().toUpperCase().contains(charVal.toUpperCase())) {
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

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempProductList.addAll(productMasterList);
    }

    public void reInitialiseProductGroupList() {
        tempProductGroupList.removeAll(tempProductGroupList);
        int size = tempProductGroupList.size();
        int size1 = productGroupList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempProductGroupList.addAll(productGroupList);

    }

    public void reInitialiseProductSubGroupList() {
        tempProductSubGroupList.removeAll(tempProductSubGroupList);
        int size = tempProductSubGroupList.size();
        int size1 = productSubGroupList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempProductSubGroupList.addAll(productSubGroupList);
    }

    public void reInitialiseProductBrandList() {
        tempProductBrandList.removeAll(tempProductBrandList);
        int size = tempProductBrandList.size();
        int size1 = productBrandList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        tempProductBrandList.addAll(productBrandList);
    }

    @SuppressLint("SetTextI18n")
    public void CalculateTotal() {
        double amount = 0;
        double productamount;
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && !Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = !currentObj.getMrpValue().isEmpty() ? currentObj.getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double discount = Double.parseDouble(currentObj.getTradeDiscnt());
                    double premium = 0;
                    if (!currentObj.getPremium().trim().isEmpty()) {
                        premium = Double.parseDouble(currentObj.getPremium());
                    }

                    if (Constants.orderFormDetailsObj.getTdCalc().equalsIgnoreCase("amount")) {
                        productamount = (qty * (mrp - discount)) + (premium * qty);
                    } else {
                        productamount = ((mrp * qty) - (mrp * qty * discount / 100)) + (premium * qty);
                    }
                    amount = amount + productamount;
                    Constants.selectedProductMasterList.get(ii).setAmount(String.valueOf(productamount));
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = !currentObj.getMrpValue().isEmpty() ? currentObj.getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    amount = amount + (mrp * qty);
                }
                amount = (amount) - (amount * Double.parseDouble(trdDisc) / 100);
            }
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase("amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = !currentObj.getMrpValue().isEmpty() ? currentObj.getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + vat);
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = !currentObj.getMrpValue().isEmpty() ? currentObj.getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }
        mTextViewTotalOrderValue.setText(" Rs. " + defaultFormat.format(amount));
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
                        tempProductGroupList = new ArrayList<>();
                        reInitialiseProductGroupList();
                        break;
                    case 2:
                        productSubGroupList = mAceDnsDatabase.getProductSubGroupList(param, carryInSales);
                        if (!rpeatedProductEntry) {
                            removeRepeatedSubGroupItems();
                        }
                        tempProductSubGroupList = new ArrayList<>();
                        reInitialiseProductSubGroupList();
                        break;
                    case 3:
                        productBrandList = mAceDnsDatabase.getProductBrandList(param, carryInSales);
                        if (!rpeatedProductEntry) {
                            removeRepeatedBrandItems();
                        }
                        tempProductBrandList = new ArrayList<>();
                        reInitialiseProductBrandList();
                        break;
                    case 4:
                        if (carryInSales || Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes")) {
                            productMasterList = mAceDnsDatabase.getProductMasterList(param, filterNo, carryInSales);
                        } else {
                            productMasterList = mAceDnsDatabase.getSpecialProductMasterListIgnoringStock(param);
                        }
                        if (!rpeatedProductEntry) {
                            if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
                                RemoveRepeatedSpecialProductItems();
                            } else {
                                removeRepeatedProductItems();
                            }
                        }
                        tempProductList = new ArrayList<>();
                        reInitialiseProductList();
                        break;
                    case 5:
                        int max;
                        max = mAceDnsDatabase.GetVerticalValue();
                        values = new String[max];
                        System.arraycopy(Constants.mVerticalValueList, 0, values, 0, Constants.mVerticalValueList.length);
                        break;
                    case 6:
                        productMasterSizeList = mAceDnsDatabase.getSpecialProductMasterSizeListIgnoringStock(mProductGroupCode, mDnsProductCode);
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
        SimpleStringAdapter adapterUOM = new SimpleStringAdapter(ActivityNewOrder.this, R.layout.simple_list_child, uomList);
        uomDialog = new Dialog(ActivityNewOrder.this, R.style.PauseDialog);
        uomDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        uomDialog.setContentView(R.layout.choose_customer_search);
        uomDialog.setCancelable(false);
        TextView title = uomDialog.findViewById(R.id.title);
        title.setText("Please select an UOM");
        EditText searchText = uomDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.setVisibility(View.GONE);
        ListView dialogList = uomDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterUOM);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            String selectedUOM = uomList.get(arg2);
            currentProductMasterObj.setSelectedUOM(arg2);
            if (arg2 == 1) {
                edQty.setInputType(InputType.TYPE_CLASS_NUMBER);
            } else {
                edQty.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            }
            if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes") && Constants.menuDetailsObj.getOrder().equalsIgnoreCase("yes")) {
                mTextViewUOM1.setText("Pcs.");
            } else {
                mTextViewUOM1.setText(selectedUOM);
            }
            uomDialog.cancel();
            if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                showMRPDialog(currentProductMasterObj, selectedUOM);
            }
            if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("dropdown")) {
                showSaleRateDialog(currentProductMasterObj, selectedUOM);
            }
            if (Constants.orderFormDetailsObj.getClosingStk().equalsIgnoreCase("yes") || carryInSales) {
                showClosingStock(currentProductMasterObj);
            }
        });
        Button addCustomer = uomDialog.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        uomDialog.show();
    }

    private void RemoveRepeatedProduct() {
        if (Constants.selectedProductMasterList != null && !Constants.selectedProductMasterList.isEmpty()) {
            for (int countx = 0; countx < Constants.selectedProductMasterList.size(); countx++) {
                String prodcode = Constants.selectedProductMasterList.get(countx).getProdCode().trim();
                for (int county = 0; county < productMasterSizeList.size(); county++) {
                    if (prodcode.equalsIgnoreCase(productMasterSizeList.get(county).getProdCode())) {
                        productMasterSizeList.remove(county);
                        break;
                    }
                }
            }
        }
    }


    @SuppressLint({"UseCompatLoadingForDrawables", "SetTextI18n"})
    private void ShowProductListDialog(String productdetails) {
        if (!productMasterSizeList.isEmpty()) {
            RemoveRepeatedProduct();
            mProductMasterInputList = new HashMap<>();
            mProductMasterInputListUom = new HashMap<>();
            ArrayList<EditText> editTextList;

            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.dialog_order_list);
            grpDialog.setCancelable(false);

            TextView textViewProductDetails = grpDialog.findViewById(R.id.productdetails);
            textViewProductDetails.setText(productdetails);

            final LinearLayout mSubParentLayout = grpDialog.findViewById(R.id.linearLayoutParent);
            mSubParentLayout.setPadding(3, 0, 3, 0);

            LinearLayout.LayoutParams childlayoutparam = new LinearLayout.LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT, 100f);
            LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 25f);

            childlayoutparam.setMargins(0, 0, 0, 5);
            params.setMargins(0, 1, 3, 0);
            for (int count = 0; count < productMasterSizeList.size(); count++) {
                editTextList = new ArrayList<>();
                String productcode = productMasterSizeList.get(count).getProdCode();
                LinearLayout tabchildlayoutx = new LinearLayout(this);
                tabchildlayoutx.setPadding(2, 2, 2, 2);
                tabchildlayoutx.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutx.setLayoutParams(childlayoutparam);
                tabchildlayoutx.setBackgroundColor(Color.parseColor("#DCE8F6"));

                TextView textView = new TextView(this);
                textView.setLayoutParams(params);
                textView.setText(productMasterSizeList.get(count).getPackSize());
                textView.setTextColor(Color.BLACK);
                tabchildlayoutx.addView(textView);

                for (int set = 0; set < 3; set++) {
                    EditText editText = new EditText(this);
                    editText.setTag(productcode + set);
                    editText.setLayoutParams(params);
                    editText.setInputType(InputType.TYPE_CLASS_NUMBER);
                    editText.setSingleLine(true);
                    editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
                    editText.setTextColor(Color.GRAY);
                    editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
                    editText.setTypeface(null, Typeface.NORMAL);
                    editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
                    editText.setHintTextColor(Color.LTGRAY);
                    editText.setHint("0");
                    tabchildlayoutx.addView(editText);
                    editTextList.add(editText);
                }
                mSubParentLayout.addView(tabchildlayoutx);
                mProductMasterInputList.put(productcode, editTextList);
            }

            Button back = grpDialog.findViewById(R.id.back);
            back.setOnClickListener(v -> grpDialog.cancel());

            FrameLayout submit = grpDialog.findViewById(R.id.colsubmit);
            submit.setOnClickListener(arg0 -> {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mCartCount = 0;
                FetchandValidateData();

                if (mCartCount > 0) {
                    customerLayout.setVisibility(View.VISIBLE);
                    if (Constants.selectedProductMasterList.size() % 2 == 0) {
                        customerLayout.setBackgroundColor(Color.YELLOW);
                    } else {
                        customerLayout.setBackgroundColor(Color.GREEN);
                    }
                    mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        } else {
            Utils.showToast(mContext, "Error in data. Please Synchronize Data");
        }
    }

    @SuppressLint({"UseCompatLoadingForDrawables", "SetTextI18n"})
    private void ShowProductListDialogForUomSelect(String productdetails) {
        if (!productMasterSizeList.isEmpty()) {
            RemoveRepeatedProduct();
            mProductMasterInputList = new HashMap<>();
            mProductMasterInputListUom = new HashMap<>();
            ArrayList<EditText> editTextList;

            final Dialog grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.dialog_order_list);
            grpDialog.setCancelable(false);

            TextView textViewProductDetails = grpDialog.findViewById(R.id.productdetails);
            textViewProductDetails.setText(productdetails);

            final LinearLayout mSubParentLayout = grpDialog.findViewById(R.id.linearLayoutParent);
            mSubParentLayout.setPadding(3, 0, 3, 0);

            LinearLayout.LayoutParams childlayoutparam = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);

            final float scale = mContext.getResources().getDisplayMetrics().density;
            int pixels1 = (int) (50 * scale + 0.5f);
            int pixels2 = (int) (160 * scale + 0.5f);

            LinearLayout.LayoutParams params = new LinearLayout.LayoutParams(pixels1, 100);
            LinearLayout.LayoutParams paramsTV = new LinearLayout.LayoutParams(pixels1, 100);
            LinearLayout.LayoutParams paramsSpinner = new LinearLayout.LayoutParams(pixels2, 120);

            childlayoutparam.setMargins(0, 0, 0, 5);
            params.setMargins(0, 1, 3, 0);
            paramsSpinner.setMargins(0, 1, 3, 0);

            for (int count = 0; count < productMasterSizeList.size(); count++) {
                editTextList = new ArrayList<>();
                String productcode = productMasterSizeList.get(count).getProdCode();
                LinearLayout tabchildlayoutx = new LinearLayout(this);
                tabchildlayoutx.setPadding(2, 2, 2, 2);
                tabchildlayoutx.setOrientation(LinearLayout.HORIZONTAL);
                tabchildlayoutx.setLayoutParams(childlayoutparam);
                tabchildlayoutx.setBackgroundColor(Color.parseColor("#DCE8F6"));

                TextView textView = new TextView(this);
                textView.setLayoutParams(paramsTV);
                textView.setText(productMasterSizeList.get(count).getPackSize());

                textView.setTextColor(Color.BLACK);
                tabchildlayoutx.addView(textView);
                for (int set = 0; set < 4; set++) {
                    if (set < 3) {
                        EditText editText = new EditText(this);
                        editText.setTag(productcode + set);
                        editText.setLayoutParams(params);
                        editText.setInputType(InputType.TYPE_CLASS_NUMBER);
                        editText.setSingleLine(true);
                        editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
                        editText.setTextColor(Color.GRAY);
                        editText.setTypeface(null, Typeface.NORMAL);
                        editText.setBackgroundDrawable(getResources().getDrawable(R.drawable.edit_text_background));
                        editText.setHintTextColor(Color.LTGRAY);
                        editText.setHint("0");
                        tabchildlayoutx.addView(editText);
                        editTextList.add(editText);
                        mProductMasterInputList.put(productcode, editTextList);
                    } else {
                        ArrayList<String> spinnerArray = new ArrayList<>();
                        spinnerArray.add(uom1);
                        spinnerArray.add(uom2);
                        Spinner spinner = new Spinner(mContext);
                        spinner.setLayoutParams(paramsSpinner);
                        ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_dropdown_item, spinnerArray);
                        spinner.setAdapter(spinnerArrayAdapter);
                        tabchildlayoutx.addView(spinner);
                        mProductMasterInputListUom.put(productcode, spinner);
                    }
                }
                mSubParentLayout.addView(tabchildlayoutx);
            }

            Button back = grpDialog.findViewById(R.id.back);
            back.setOnClickListener(v -> grpDialog.cancel());

            FrameLayout submit = grpDialog.findViewById(R.id.colsubmit);
            submit.setOnClickListener(arg0 -> {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mCartCount = 0;
                FetchandValidateData();
                if (mCartCount > 0) {
                    customerLayout.setVisibility(View.VISIBLE);
                    if (Constants.selectedProductMasterList.size() % 2 == 0) {
                        customerLayout.setBackgroundColor(Color.YELLOW);
                    } else {
                        customerLayout.setBackgroundColor(Color.GREEN);
                    }
                    mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
                    grpDialog.cancel();
                }
            });

            grpDialog.show();
        } else {
            Utils.showToast(mContext, "Error in data. Please Synchronize Data");
        }
    }

    public void FetchandValidateData() {
        if (mProductMasterInputList != null) {
            Set<Entry<String, ArrayList<EditText>>> set = mProductMasterInputList.entrySet();
            Iterator<Entry<String, ArrayList<EditText>>> iterator = set.iterator();
            Iterator<Entry<String, ArrayList<EditText>>> iterator2 = set.iterator();
            boolean areallDataOk = true;
            while (iterator2.hasNext()) {
                Map.Entry<String, ArrayList<EditText>> entry = iterator2.next();
                List<EditText> edittextList = entry.getValue();
                String quantity = "";
                String mrp = "";
                for (int count = 0; count < edittextList.size(); count++) {
                    if (count == 0) {
                        quantity = edittextList.get(count).getText().toString();
                    }
                    if (count == 1) {
                        mrp = edittextList.get(count).getText().toString();
                    }
                }
                if (!quantity.isEmpty() && mrp.isEmpty()) {
                    Toast.makeText(ActivityNewOrder.this, "Please provide valid rate", Toast.LENGTH_LONG).show();
                    areallDataOk = false;
                } else if (quantity.isEmpty() && !mrp.isEmpty()) {
                    Toast.makeText(ActivityNewOrder.this, "Please provide valid quantity", Toast.LENGTH_LONG).show();
                    areallDataOk = false;
                }
            }
            if (areallDataOk) {
                while (iterator.hasNext()) {
                    Map.Entry<String, ArrayList<EditText>> entry = iterator.next();
                    String productcode = entry.getKey();
                    List<EditText> edittextList = entry.getValue();
                    String quantity = "";
                    String mrp = "";
                    String td = "0";
                    boolean qtystatus, mrpstatus = true, tdStatus = true;
                    for (int count = 0; count < edittextList.size(); count++) {
                        if (count == 0) {
                            quantity = edittextList.get(count).getText().toString();
                        }
                        if (count == 1) {
                            mrp = edittextList.get(count).getText().toString();
                        }
                        if (count == 2) {
                            td = edittextList.get(count).getText().toString();
                        }
                    }

                    qtystatus = !quantity.isEmpty() && !quantity.equalsIgnoreCase("0") && !quantity.equalsIgnoreCase(".") && Double.parseDouble(quantity) != 0;

                    if (qtystatus) {
                        if (mrp.isEmpty() || mrp.equalsIgnoreCase("0") || mrp.equalsIgnoreCase(".") || Double.parseDouble(mrp) == 0) {
                            mrpstatus = false;
                            Toast.makeText(ActivityNewOrder.this, "Please provide valid sale rate", Toast.LENGTH_LONG).show();
                        }
                    } else {
                        mrpstatus = false;
                    }

                    if (qtystatus && mrpstatus) {
                        if (!td.isEmpty() && !td.equalsIgnoreCase(".")) {
                            if (!(Double.parseDouble(td) <= 100)) {
                                tdStatus = false;
                                Toast.makeText(ActivityNewOrder.this, "Trade Discount cannot be more than 100 %", Toast.LENGTH_LONG).show();
                            }
                        } else {
                            td = "0";
                        }
                    }
                    String selecteduom = "";
                    if (Constants.orderFormDetailsObj.getMultipleUom().equalsIgnoreCase("yes") && mProductMasterInputListUom != null && !mProductMasterInputListUom.isEmpty()) {
                        Spinner spinnerUom = mProductMasterInputListUom.get(productcode);
                        assert spinnerUom != null;
                        selecteduom = spinnerUom.getSelectedItem().toString();
                    }

                    if (qtystatus && mrpstatus && tdStatus) {
                        mCartCount += 1;
                        String productdesc = mProductName + " - " + GetPackSize(productcode);
                        quantity = defaultFormat.format(Double.valueOf(quantity));
                        mrp = defaultFormat.format(Double.valueOf(mrp));
                        td = defaultFormat.format(Double.valueOf(td));
                        AddtoCart(productcode, quantity, mrp, td, productdesc, selecteduom);
                    }
                }
            }
        }
    }

    private String GetPackSize(String prodcode) {
        String packsize = "";
        for (int count = 0; count < productMasterSizeList.size(); count++) {
            if (prodcode.equalsIgnoreCase(productMasterSizeList.get(count).getProdCode())) {
                packsize = productMasterSizeList.get(count).getPackSize();
            }
        }
        return packsize;
    }

    private void AddtoCart(String productcode, String quantity, String salerate, String td, String desc, String selecteduom) {
        currentProductMasterObj = new ProductMasterDetails();
        currentProductMasterObj.setProdCode(productcode);
        currentProductMasterObj.setDesc(desc);
        currentProductMasterObj.setQty(quantity);
        currentProductMasterObj.setMrpCode("0");
        currentProductMasterObj.setUom1(uom1);
        currentProductMasterObj.setUom2(uom2);
        currentProductMasterObj.setMrpCode("0");

        currentProductMasterObj.setMrpValue(salerate);
        currentProductMasterObj.setTradeDiscnt(td);
        currentProductMasterObj.setDnsProdCode(mDnsProductCode);
        currentProductMasterObj.setUomSelectedForProduct(selecteduom);
        Constants.selectedProductMasterList.add(currentProductMasterObj);
        CalculateTotal();
    }

    @SuppressLint("SetTextI18n")
    public void ShowPreviousOrderDialog() {
        String productcode = currentProductMasterObj.getProdCode();
        String customercode = Constants.selectedCustomer.getCustomerCode();
        String data = mAceDnsDatabase.GetPreviousOrederData(customercode, productcode);

        if (!data.isEmpty()) {
            final Dialog mPreviousOrderDialog = new Dialog(ActivityNewOrder.this, R.style.PauseDialog);
            mPreviousOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mPreviousOrderDialog.setContentView(R.layout.activity_note);
            mPreviousOrderDialog.setCancelable(false);

            TextView textViewTitle = mPreviousOrderDialog.findViewById(R.id.title);
            textViewTitle.setText("Previous order history");

            TextView textViewPreviousBooking1st = mPreviousOrderDialog.findViewById(R.id.textView1st);
            TextView textViewPreviousBooking2nd = mPreviousOrderDialog.findViewById(R.id.textView2nd);
            TextView textViewPreviousBooking3rd = mPreviousOrderDialog.findViewById(R.id.textView3rd);

            textViewPreviousBooking1st.setText("");
            textViewPreviousBooking2nd.setText("");
            textViewPreviousBooking3rd.setText("");

            if (data.contains(",")) {
                String[] previousorderdata = data.split(",");
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

            Button buttonCancel = mPreviousOrderDialog.findViewById(R.id.buttonCancel);
            buttonCancel.setOnClickListener(v -> mPreviousOrderDialog.cancel());

            mPreviousOrderDialog.show();
        }
    }
}
