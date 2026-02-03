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

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BargainProductInputAdapter;
import com.forcepower.acedns.adapter.IncotermsAdapter;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitSaudaOreder;
import com.forcepower.acedns.bean.ProductMRPDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.bean.SaudaHeader;
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
import java.util.Objects;

import static com.forcepower.acedns.activity.ActivityBargainFilter.mLoadabilityTon;
import static com.forcepower.acedns.activity.ActivityBargainFilter.selectedRouteCode;
import static com.forcepower.acedns.activity.ActivityBargainFilter.verticalValueOfEmployee;
import static com.forcepower.acedns.adapter.BargainProductInputAdapter.nameValuesProductListLocal;
import static com.forcepower.acedns.constants.Constants.BrokerageCost;
import static com.forcepower.acedns.constants.Constants.HoneyCombCost;
import static com.forcepower.acedns.constants.Constants.defaultFormat3;
import static com.forcepower.acedns.constants.Constants.isSecondaryFreightIncluded;
import static com.forcepower.acedns.constants.Constants.lodabilityToneFORDEPOT;
import static com.forcepower.acedns.constants.Constants.mChosenUomType;
import static com.forcepower.acedns.constants.Constants.mDepotOrPlant;
import static com.forcepower.acedns.constants.Constants.mSaudaDepoCode;
import static com.forcepower.acedns.constants.Constants.marginCost;
import static com.forcepower.acedns.constants.Constants.maxLiquidationDiscountForCurrentCustomer;
import static com.forcepower.acedns.constants.Constants.maxallocation;
import static com.forcepower.acedns.constants.Constants.remainingAllocation;
import static com.forcepower.acedns.constants.Constants.remainingAllocationTV;
import static com.forcepower.acedns.constants.Constants.selectedSSOfCustomer;
import static com.forcepower.acedns.constants.Constants.selectedVerticalOfUser;

public class BargainActivity extends AppCompatActivity implements OnClickListener {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutCartDetails = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutFreight = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout calculated_salerate_layout = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout check_td_premium_layout = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutQuantityMRP = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutStock = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonAddtoCart = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonCheckOut = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonNoOrder = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSelectProduct = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextTD = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextQuantity = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextSaleRate = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditSecondaryFreight = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText ed_calculated_salerate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView uomTvHeader = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCustomerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewRoute = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView customerInfoTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView ssInfoTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView totalAllocationTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewProductDetails = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewDepoDetails = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewVertical = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewTDorPremium = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewClosingStock = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewNoofOrder = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView uomTv = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewAllocation = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewTotalOrderAmount = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mEditTextLiquidationDiscount = null;
    @SuppressLint("StaticFieldLeak")
    public static RadioGroup mRadioGroupTDorPremium = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout liquidationDiscountLayout = null;

    BargainProductInputAdapter ProductMasterWithQtyInputAdapterObjectAlternateDesignObject;
    ListView prodQtyRateListView;
    Spinner customerSpinner;
    double minCapacityOfCurrentTransportMode=0.0;
    public boolean isProductSelected = false;
    public boolean isTDinput = false;
    public boolean isPremiuminput = false;
    public boolean rpeatedProductEntry = false;
    public boolean carryInSales = false;
    public boolean shouldCheckSaudaDespatchOrigin = false;
    public Context mContext;
    double freightRate = 0, calculatedSaleRate = 0, depotCost = 0, primaryFreight = 0, basicRate;
    ProgressDialog ploader;
    Handler orderDataHandler;
    SaudaHeader mSaudaHeader;
    SaudaDetails mSaudaDetails;
    ProductMRPDetails currentProductMRPDetails;
    HashMap<String, ArrayList<ProductMRPDetails>> mProductMasterList;
    ArrayList<ProductMasterDetails> productMasterList;
    ArrayList<String> mSelectedUomList;
    private String mCustomerName = "";
    private String mSaudaBookedType = "";
    private String mSaudaType = "";
    private String mSaudaDepoName = "";
    public static String selectedFreightRate = "", mSaudaDespatchOrigin = "", verticalOfUser = "", selectedRouteName;
    private final String mSaleRate = "0";
    private String mSaudaMode = "";
    private final Boolean isBasicRateOk = true;
    private Boolean isDepotCostOk = true;
    private Boolean isPrimaryFreightOk = true;
    private Boolean isFreightRateOk = true;
    private Boolean isMarginCostOk = true;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;

    @SuppressLint({"SetTextI18n","DefaultLocale","HandlerLeak"})
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_bargain_material);
        RegisterActivities.registerActivity(this);

        Toolbar myToolbar = findViewById(R.id.my_toolbar);
        setSupportActionBar(myToolbar);
        Objects.requireNonNull(getSupportActionBar()).setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayShowTitleEnabled(false);

        Constants.isFromConfirmationActivity = false;

        mContext = BargainActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        mCustomerName = getIntent().getStringExtra("CUSTOMER");
        mSaudaBookedType = getIntent().getStringExtra("SAUDABOOKEDTYPE");
        maxallocation = Double.parseDouble(getIntent().getStringExtra("setmaxAlloc"));
        remainingAllocation=maxallocation;
        mSaudaType = getIntent().getStringExtra("SAUDATYPE");
        mSaudaDepoName = getIntent().getStringExtra("DEPODETAILS");
        mSaudaDespatchOrigin = getIntent().getStringExtra("mSaudaDespatchOrigin");
        verticalOfUser = getIntent().getStringExtra("selectedVerticalOfUser");
        selectedRouteName = getIntent().getStringExtra("selectedRouteName");
        shouldCheckSaudaDespatchOrigin = getIntent().getBooleanExtra("shouldCheckSaudaDespatchOrigin", false);

        Constants.mBrokerMasterList = new ArrayList<>();
        Constants.selectedProductMasterList = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();

        Constants.selectedSaudaDetailsList = new ArrayList<>();
        Constants.SelectedSkuCode = new ArrayList<>();

        InitializeView();
        if (Constants.menuDetailsObj.getTDAllocation() != null && Constants.menuDetailsObj.getTDAllocation().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("TD_allocation_app")) {
            uomTvHeader.setText("Disc/Prm");
        } else {
            uomTvHeader.setVisibility(View.GONE);
        }

        DrawLayout();
        enableOrDisableLiquidationDiscountlayout();
        customerInfoTV.setText("Customer: "+mCustomerName );
        if(selectedSSOfCustomer!=null){
            ssInfoTV.setVisibility(View.VISIBLE);
            ssInfoTV.setText("SS: "+selectedSSOfCustomer.getCustomerName());
        }

        isTDinput = true;
        isPremiuminput = false;
        SetData();
        mButtonSelectProduct.setOnClickListener(v -> PrepareOrderData(1));

        orderDataHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                BargainActivity.this.runOnUiThread(() -> {
                    if (jobToDo == 1) {
                        ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new BargainProductInputAdapter(mContext, R.layout.list_item__product_with_quantity_input_material, productMasterList);
                        prodQtyRateListView.setEmptyView(findViewById(R.id.empty_text_view));
                        prodQtyRateListView.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
                    }
                });
            }
        };

        DisableInput();
        mRadioGroupTDorPremium.setVisibility(View.GONE);
        mTextViewTDorPremium.setText("TD ");
        isTDinput = true;
        isPremiuminput = false;
        mRadioGroupTDorPremium.setOnCheckedChangeListener((group, checkedId) -> {
                    RadioButton radioSelection =  findViewById(checkedId);
                    if (radioSelection.getText().equals("TD")) {
                        mTextViewTDorPremium.setText("TD ");
                        isTDinput = true;
                        isPremiuminput = false;
                        try {
                            if (isProductSelected) {
                                calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, Double.parseDouble(mEditTextTD.getText().toString()), 0);
                                ed_calculated_salerate.setText(String.format("%.2f", calculatedSaleRate));
                            }
                        } catch (Exception ignored) {}
                    } else {
                        mTextViewTDorPremium.setText("Premium");
                        isTDinput = false;
                        isPremiuminput = true;
                        try {
                            if (isProductSelected) {
                                calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, 0, Double.parseDouble(mEditTextTD.getText().toString()));
                                ed_calculated_salerate.setText(String.format("%.2f", calculatedSaleRate));
                            }
                        } catch (Exception ignored) {}
                    }
                    if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser) && (!isBasicRateOk || !isDepotCostOk || !isPrimaryFreightOk || !isFreightRateOk || !isMarginCostOk)) {
                        ed_calculated_salerate.setText("0.00");
                    }
                });
        if(mAceDnsDatabase.ismcxOpenOrCLoseForCurrentCustomerAndSku("", Constants.selectedCustomer.getCustomerCode())) {
            mSelectedUomList = mAceDnsDatabase.GetUniqueUom1ListBargainForMcxOpenCustomer(verticalOfUser);
        } else {
            mSelectedUomList = mAceDnsDatabase.GetUniqueUom1ListBargain(verticalOfUser);
        }

        totalAllocationTV.setText("Total Allocation : "+defaultFormat3.format(maxallocation) +" MT");
        remainingAllocationTV.setText("Remaining Allocation : "+defaultFormat3.format(maxallocation)+" MT" );
        if(mSelectedUomList.size()==1) {
            mChosenUomType=mSelectedUomList.get(0);
            PrepareOrderData(1);
        } else if(mSelectedUomList.size()>1) {
            showUom1ChooseDialog();
        } else {
            Utils.showToast(mContext,"Proper Uom not found for products. Please contact admin");
        }
    }
    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }
    @SuppressLint("SetTextI18n")
    private void showUom1ChooseDialog() {
        final Dialog incotermsSelectionDialog = new Dialog(mContext, R.style.PauseDialog);
        incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        incotermsSelectionDialog.setContentView(R.layout.select_from_list);
        incotermsSelectionDialog.setCancelable(false);
        Button btn_cncl =  incotermsSelectionDialog.findViewById(R.id.btn_cncl);
        btn_cncl.setOnClickListener(view -> incotermsSelectionDialog.dismiss());
        TextView title =  incotermsSelectionDialog.findViewById(R.id.title);
        title.setText("Please select Uom for bargain products");
        ListView dialogList =  incotermsSelectionDialog.findViewById(R.id.list);
        IncotermsAdapter branchadapter = new IncotermsAdapter(mContext, R.layout.list_item_single_radio ,mSelectedUomList);
        dialogList.setAdapter(branchadapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            incotermsSelectionDialog.cancel();
            mChosenUomType = mSelectedUomList.get(arg2);
            uomTv.setText("("+mChosenUomType+")");
            textViewAllocation.setText("Max Allocation("+mChosenUomType+")");
            PrepareOrderData(1);
        });
        incotermsSelectionDialog.show();
    }
    @Override
    public void onResume() {
        super.onResume();
        rpeatedProductEntry = Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes");
        if (mEditTextQuantity != null) {
            mEditTextQuantity.setText("");
        }
        if (mEditTextSaleRate != null) {
            mEditTextSaleRate.setText("");
        }
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
        REFRESHSaudaActivity(false);
    }
    @SuppressLint({"SetTextI18n","DefaultLocale"})
    public void InitializeView() {
        customerSpinner =  findViewById(R.id.customerSpinner);
        prodQtyRateListView =  findViewById(R.id.prodQtyRateListView);
        mLinearLayoutFreight =  findViewById(R.id.amount_layout);
        calculated_salerate_layout =  findViewById(R.id.calculated_salerate_layout);
        check_td_premium_layout =  findViewById(R.id.check_td_premium_layout);
        liquidationDiscountLayout =  findViewById(R.id.liquidationDiscountLayout);
        mEditTextLiquidationDiscount =  findViewById(R.id.mEditTextLiquidationDiscount);
        mEditSecondaryFreight =  findViewById(R.id.ed_amt);
        ed_calculated_salerate =  findViewById(R.id.ed_calculated_salerate);
        mEditTextQuantity =  findViewById(R.id.mEditTextQuantity);
        mTextViewTDorPremium =  findViewById(R.id.mTextViewTDorPremium);
        mEditTextTD =  findViewById(R.id.mEditTextTD);
        mImageViewHeaderLogo =  findViewById(R.id.imagelogo);
        mLinearLayoutCartDetails =  findViewById(R.id.customer_layout);
        TextView txtVersion =  findViewById(R.id.txt_version);
        mLinearLayoutQuantityMRP =  findViewById(R.id.qty_mrp_layout);
        mLinearLayoutStock =  findViewById(R.id.stk_td_layout);
        mTextViewTotalOrderAmount =  findViewById(R.id.txt_total);
        uomTv =  findViewById(R.id.uomTv);
        textViewAllocation =  findViewById(R.id.textViewAllocation);
        mTextViewNoofOrder =  findViewById(R.id.txt_order_count);
        mButtonSelectProduct =  findViewById(R.id.buttonSelectProductDetails);
        customerInfoTV =  findViewById(R.id.customerInfoTV);
        ssInfoTV =  findViewById(R.id.ssInfoTV);
        totalAllocationTV =  findViewById(R.id.totalAllocationTV);
        remainingAllocationTV =  findViewById(R.id.remainingAllocationTV);
        textViewRoute =  findViewById(R.id.textViewRoute);
        mTextViewCustomerName =  findViewById(R.id.textViewCustomername);
        uomTvHeader =  findViewById(R.id.uomTvHeader);
        mTextViewProductDetails =  findViewById(R.id.textViewProductDetails);
        mTextViewDepoDetails =  findViewById(R.id.textViewDepoDetails);
        textViewVertical =  findViewById(R.id.textViewDespatchOrigin);
        mTextViewClosingStock =  findViewById(R.id.textViewCalculateedAllocation);
        mRadioGroupTDorPremium =  findViewById(R.id.radioSelectType);

        mButtonAddtoCart =  findViewById(R.id.btn_continue);
        mButtonAddtoCart.setOnClickListener(BargainActivity.this);
        mButtonAddtoCart.setTag(101);
        mButtonAddtoCart.setEnabled(false);

        mButtonCheckOut =  findViewById(R.id.btn_order_form);
        mButtonCheckOut.setTag(102);
        mButtonCheckOut.setOnClickListener(BargainActivity.this);

        mButtonNoOrder =  findViewById(R.id.no_ordr);
        mButtonNoOrder.setTag(104);
        mButtonNoOrder.setOnClickListener(BargainActivity.this);

        mButtonBack =  findViewById(R.id.back);
        mButtonBack.setTag(105);
        mButtonBack.setOnClickListener(BargainActivity.this);

        mLinearLayoutCartDetails.setVisibility(View.INVISIBLE);
        
        mEditTextQuantity.setOnEditorActionListener((v, actionId, event) -> {
            if (event == null) {
                if (actionId == EditorInfo.IME_ACTION_DONE) {
                    InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                    imm.hideSoftInputFromWindow(mEditTextQuantity.getWindowToken(), 0);
                    getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    return true;
                }
            }
            return false;
        });
        mEditTextQuantity.setOnFocusChangeListener((v, hasFocus) -> {
            if (hasFocus) {
                ((EditText) v).setText("");
            }
        });
        mEditTextTD.addTextChangedListener(new TextWatcher() {
            @Override
            public void afterTextChanged(Editable s) {}

            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                try {
                    if (isProductSelected) {
                        if (!s.toString().isEmpty()) {
                            if (isTDinput) {
                                calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, Double.parseDouble(s.toString()), 0);
                            } else {
                                calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, 0, Double.parseDouble(s.toString()));
                            }
                            ed_calculated_salerate.setText(String.format("%.2f", calculatedSaleRate));
                        } else {
                            calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, 0, 0);
                            ed_calculated_salerate.setText(String.format("%.2f", calculatedSaleRate));
                        }
                    }
                    if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser) && (!isBasicRateOk || !isDepotCostOk || !isPrimaryFreightOk || !isFreightRateOk) || !isMarginCostOk) {
                        ed_calculated_salerate.setText("0.00");
                    }
                } catch (Exception ignored) {}
            }
        });
        mEditSecondaryFreight.setTextSize(15);

        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
        
        textViewRoute.setText("");
        mTextViewCustomerName.setText("");
        mTextViewProductDetails.setText("");
        mTextViewDepoDetails.setText("");
        mTextViewClosingStock.setText("");
    }
    public void DrawLayout() {
        EnableDisableLayout(false);
    }
    @SuppressLint("SetTextI18n")
    public void SetData() {
        textViewRoute.setText("Route : " + selectedRouteName);
        mTextViewCustomerName.setText("Customer : " + mCustomerName);
        mTextViewDepoDetails.setText(mSaudaType + " : " + mSaudaDepoName);
        mSaudaMode = mSaudaType;
        textViewVertical.setVisibility(View.VISIBLE);
        textViewVertical.setText("Vertical : " + verticalOfUser);
    }
    public void EnableDisableLayout(boolean isShown) {
        mLinearLayoutQuantityMRP.setEnabled(isShown);
        mLinearLayoutStock.setEnabled(isShown);
    }
    public void onClick(View clkdView) {
        if (clkdView == mButtonAddtoCart) {
            boolean isFreight = false;
            boolean isSaleRate = false;
            boolean LiquidationDiscountValid = true;
            String inputLiquidationDiscountForCurrentProduct ;
            if (Constants.saudaFormDetailsObj.getSpecialDiscountVertical().contains(selectedVerticalOfUser) && Utils.isNumeric(maxLiquidationDiscountForCurrentCustomer) && Double.parseDouble(maxLiquidationDiscountForCurrentCustomer) > 0) {
                inputLiquidationDiscountForCurrentProduct = mEditTextLiquidationDiscount.getText().toString();
                if (!inputLiquidationDiscountForCurrentProduct.matches("")) {
                    if (!Utils.isNumeric(inputLiquidationDiscountForCurrentProduct)) {
                        LiquidationDiscountValid = false;
                        Toast.makeText(mContext, "Please provide valid liquidation discount value!", Toast.LENGTH_SHORT).show();
                    } else if (Double.parseDouble(inputLiquidationDiscountForCurrentProduct) > Double.parseDouble(maxLiquidationDiscountForCurrentCustomer)) {
                        LiquidationDiscountValid = false;
                        Toast.makeText(mContext, "Input discount amount exceeds maximum allowed discount!", Toast.LENGTH_SHORT).show();
                    }
                }
            }
            if (!LiquidationDiscountValid) {
                return;
            }
            boolean isSecondaryFreightOk = true;
            if (mSaudaType.equalsIgnoreCase("FOR")) {
                    String freighInString = mEditSecondaryFreight.getText().toString();
                    if (!Utils.isNumeric(freighInString)) {
                        isSecondaryFreightOk = false;
                        Toast.makeText(mContext, "Please provide proper freight rate.", Toast.LENGTH_SHORT).show();
                    } else if (Double.parseDouble(freighInString) <= 0) {
                        isSecondaryFreightOk = false;
                        Toast.makeText(mContext, "Please provide proper freight rate.", Toast.LENGTH_SHORT).show();
                    } else {
                        freightRate = Double.parseDouble(freighInString);
                        freightRate = Math.round(freightRate * 100.0) / 100.0;
                    }
            } else {
                freightRate = 0;
            }
            if (!isSecondaryFreightOk) {
                return;
            }
            if (!mProductMasterList.isEmpty()) {
                String gettd = currentProductMRPDetails.getTD();
                if ( !mEditTextQuantity.getText().toString().isEmpty() && !mEditTextQuantity.getText().toString().equalsIgnoreCase("0") && !mEditTextQuantity.getText().toString().equalsIgnoreCase(".") && Double.parseDouble(mEditTextQuantity.getText().toString()) != 0) {
                    String inpqty = mEditTextQuantity.getText().toString().trim();
                    double maxAllo = Double.parseDouble(inpqty);
                    if(!mChosenUomType.equalsIgnoreCase("loose")) {
                        maxAllo = mAceDnsDatabase.calculatedValueC2MBargain(currentProductMRPDetails.getProductCode(), maxAllo);
                    }
                        if (maxallocation > 0 && maxAllo <= maxallocation) {
                            maxallocation = maxallocation - maxAllo;
                        } else {
                            Toast.makeText(BargainActivity.this, "Exceed Quantity", Toast.LENGTH_LONG).show();
                        }
                } else {
                    Toast.makeText(BargainActivity.this, "Invalid Quantity", Toast.LENGTH_LONG).show();
                }
                if (isTDinput && !isPremiuminput) {
                    if (!mEditTextTD.getText().toString().equalsIgnoreCase(".")) {
                        if (!mEditTextTD.getText().toString().isEmpty() && !mEditTextTD.getText().toString().equalsIgnoreCase("0")) {
                            String inptd = mEditTextTD.getText().toString();
                            String s = Constants.menuDetailsObj.getrun_time_TD_approval_vertical();
                            if (!selectedVerticalOfUser.contains(s)) {
                                if (Double.parseDouble(inptd) != Double.parseDouble(gettd)) {
                                    if (!(Double.parseDouble(inptd) <= Double.parseDouble(gettd))) {
                                        Toast.makeText(BargainActivity.this, "Exceed Trade Discount", Toast.LENGTH_LONG).show();
                                    }
                                }
                            }
                        }
                    } else {
                        Toast.makeText(BargainActivity.this, "Invalid Trade Discount", Toast.LENGTH_LONG).show();
                    }
                }
                if (!isTDinput && isPremiuminput) {
                    if (mEditTextTD.getText().toString().equalsIgnoreCase(".")) {
                        Toast.makeText(BargainActivity.this, "Invalid Premium", Toast.LENGTH_LONG).show();
                    }
                }
                if ((!isBasicRateOk || !isDepotCostOk || !isPrimaryFreightOk || !isFreightRateOk || !isMarginCostOk)) {
                    if (!Constants.employeeDetailObject.getEmpCode().equalsIgnoreCase("E0002")) {
                        Utils.showToast(mContext, "Error: Please contact admin.");
                    } else {
                        if (!isBasicRateOk) {
                            Utils.showToast(mContext, "Basic rate not updated.");
                        } else if (!isDepotCostOk) {
                            Utils.showToast(mContext, "Depot cost not updated.");
                        } else if (!isPrimaryFreightOk) {
                            Utils.showToast(mContext, "Primary freight not updated.");
                        }  else {
                            Utils.showToast(mContext, "Margin cost not updated.");
                        }
                    }
                } else {
                    isFreight = true;
                }
                if (mSaudaMode.equalsIgnoreCase("FOR")) {
                    if (!isSaleRate) {
                        Toast.makeText(BargainActivity.this, "Sale rate of this product is 0\nPlease contact admin", Toast.LENGTH_LONG).show();
                    } else {
                        Toast.makeText(BargainActivity.this, "Please provide valid input", Toast.LENGTH_LONG).show();
                    }
                } else {
                    if (!isSaleRate) {
                        Toast.makeText(BargainActivity.this, "Sale rate of this product is 0\nPlease contact admin", Toast.LENGTH_LONG).show();
                    } else {
                        if (!isFreight)
                            Toast.makeText(BargainActivity.this, "Please provide valid input", Toast.LENGTH_LONG).show();
                    }
                }
            } else {
                Toast.makeText(BargainActivity.this, "Please select a product", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == mButtonCheckOut) {
            if(remainingAllocation>-1) {
                Constants.selectedSaudaDetailsList = new ArrayList<>();
                double totalInputQty=0.0;
                for(int i=0;i<nameValuesProductListLocal.size();i++) {
                    ProductMasterDetails productMasterDetails=nameValuesProductListLocal.get(i);
                    String qty = productMasterDetails.getQty();
                    String td = productMasterDetails.getTradeDiscnt();
                    String tdLimit = productMasterDetails.gettradeDiscntLimit();

                    String rate = productMasterDetails.getRate();
                    String brokerageCost = productMasterDetails.getbrokerageCost();
                    String brokerageCostSS = productMasterDetails.getbrokerageCostSS();
                    if(!Utils.isNumeric(td)) {
                        td="0";
                    }
                    if (Utils.isNumeric(qty) && Double.parseDouble(qty)>0  && Utils.isNumeric(rate)) {
                        double qtyInDouble = Double.parseDouble(qty);
                        if (Constants.menuDetailsObj.getTDAllocation() != null && Constants.menuDetailsObj.getTDAllocation().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("TD_allocation_app")) {
                            if(Utils.isNumeric(td) && Double.parseDouble(td)<0) {
                                if(Double.parseDouble(td.replace("-",""))>Double.parseDouble(tdLimit)) {
                                    Utils.showToast(mContext,"Unapproved discount, Please contact admin");
                                    return;
                                }
                            }
                        }
                        isDepotCostOk = true;
                        isPrimaryFreightOk = true;
                        isFreightRateOk = true;
                        isMarginCostOk = true;
                         double freightInDouble = Double.parseDouble(productMasterDetails.getFreight());
                        double rateInDouble = Double.parseDouble(rate);
                        double brokerageCostInDouble = Double.parseDouble(productMasterDetails.getbrokerageCost());
                        double brokerageCostSSInDouble = Double.parseDouble(productMasterDetails.getbrokerageCostSS());
                        double additionalPremium = Double.parseDouble(productMasterDetails.getadditionalPremium());
                        double additionalTD = Double.parseDouble(productMasterDetails.getadditionalTD());
                        double primaryFreight = Double.parseDouble(productMasterDetails.getprimaryFreight());
                        double depotCost = Double.parseDouble(productMasterDetails.getdepotCost());
                        double marginCost = Double.parseDouble(productMasterDetails.getmarginCost());
                        double tdInDouble = Double.parseDouble(td);
                        rateInDouble = Math.round(rateInDouble * 100D) / 100D;
                        freightInDouble = Math.round(freightInDouble * 100D) / 100D;

                        double amount = qtyInDouble * ((rateInDouble + freightInDouble+primaryFreight+depotCost+marginCost+brokerageCostInDouble+brokerageCostSSInDouble+ tdInDouble+additionalPremium)-additionalTD);
                        mSaudaDetails = new SaudaDetails();
                        mSaudaDetails.setGroupCode("");
                        mSaudaDetails.setSkuCode(productMasterDetails.getProdCode());
                        mSaudaDetails.setProductName(productMasterDetails.getDesc());
                        mSaudaDetails.setQuantity(qty);
                        mSaudaDetails.setMrpCode(productMasterDetails.getMrpCode());
                        mSaudaDetails.setTD("0");
                        mSaudaDetails.setMaxTD("0");
                        mSaudaDetails.setSaleRate(rate);
                        mSaudaDetails.setBrokarageCost(brokerageCost);
                        mSaudaDetails.setBrokarageCostSS(brokerageCostSS);
                        mSaudaDetails.setTD(td);
                        mSaudaDetails.setConversionFactor("0");
                        mSaudaDetails.setVAT(productMasterDetails.getVatRate());
                        mSaudaDetails.setAmount(amount+"");
                        mSaudaDetails.setFreightCharge(freightInDouble +"");
                        mSaudaDetails.setPremium("0");
                        mSaudaDetails.setTDorPremiumCheck("0");
                        mSaudaDetails.setPrimaryFreight(String.valueOf(primaryFreight));
                        mSaudaDetails.setDepotCost(String.valueOf(depotCost));
                        mSaudaDetails.setHoneyCombCost(String.valueOf(HoneyCombCost));
                        mSaudaDetails.setMarginCost(String.valueOf(marginCost));
                        mSaudaDetails.setadditionalPremium(String.valueOf(additionalPremium));
                        mSaudaDetails.setadditionalTD(String.valueOf(additionalTD));
                        mSaudaDetails.setLiquidTD("0");
                        totalInputQty=totalInputQty+qtyInDouble;
                        Constants.selectedSaudaDetailsList.add(mSaudaDetails);
                    }
                }
                String totalAlloc=totalAllocationTV.getText().toString().replace(" MT","").replace("Total Allocation : ","");
                String remainingAlloc=remainingAllocationTV.getText().toString().replace(" MT","").replace("Remaining Allocation : ","");
                if(Double.parseDouble(remainingAlloc)<=0) {
                    Constants.selectedSaudaDetailsList=new ArrayList<>();
                    Utils.showToast(mContext,"Total Input Qty must be <= "+totalAlloc+" MT (Maximum Limit)" );
                    return;
                }
                if (!Constants.selectedSaudaDetailsList.isEmpty()) {
                    mTextViewClosingStock.setText("");
                    mLinearLayoutCartDetails.setVisibility(View.INVISIBLE);
                    Intent intent = new Intent(BargainActivity.this, BargainConfirmationActivity.class);
                    intent.putExtra("CARRY_IN", carryInSales);
                    intent.putExtra("SAUDATYPE", mSaudaType);
                    intent.putExtra("SAUDABOOKED", mSaudaBookedType);
                    startActivity(intent);
                } else {
                    Toast.makeText(mContext, "Please provide input for one product at least.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(mContext, "Max Allocation exceeded. Please modify your input", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == mButtonNoOrder) {
            new GPSTracker(mContext);
            ShowNoSaudaDialog();
        }
        if (clkdView == mButtonBack) {
            if (!Constants.selectedProductMasterList.isEmpty()) {
                AceDnsTransactionDatabase dbObj = new AceDnsTransactionDatabase(mContext);
                dbObj.decreaseBALSauda();
                dbObj.close();
            }
            finish();
        }
    }
    @SuppressLint("SetTextI18n")
    public void REFRESHSaudaActivity(Boolean showToast) {
        try {
            int sizeOfProductsInCart = Constants.selectedSaudaDetailsList.size();
            if (sizeOfProductsInCart > 0) {
                mLinearLayoutCartDetails.setVisibility(View.VISIBLE);
                mLinearLayoutCartDetails.setBackgroundColor(Color.YELLOW);

                mTextViewNoofOrder.setText("" + sizeOfProductsInCart);
                mTextViewTotalOrderAmount.setText("");
                mButtonCheckOut.setEnabled(true);
                if (showToast) {
                    Toast.makeText(BargainActivity.this, "Product has been added to cart.", Toast.LENGTH_LONG).show();
                }
                if (mEditTextQuantity != null)
                    mEditTextQuantity.setText("");
                if (mEditTextSaleRate != null)
                    mEditTextSaleRate.setText("");
                if (mEditTextTD != null)
                    mEditTextTD.setText("0");
                if (mEditSecondaryFreight != null)
                    mEditSecondaryFreight.setText("");
                if (mTextViewProductDetails != null)
                    mTextViewProductDetails.setText("");
                if (mTextViewClosingStock != null)
                    mTextViewClosingStock.setText("");
                isProductSelected = false;
                mButtonAddtoCart.setEnabled(false);
                DisableInput();
                ed_calculated_salerate.setText("");
            }
        } catch (Exception ignored) {}
    }
    public boolean NoSaudaHeaderData(String customercode, String remarks) {
        boolean issuccess = false;
        try {
            mSaudaHeader = new SaudaHeader();
            mSaudaHeader.setCustomerCode(customercode);
            mSaudaHeader.setTransfered("");
            mSaudaHeader.setRemarks(remarks);
            mSaudaHeader.setTD("");
            mSaudaHeader.setSaudaValue("");
            mSaudaHeader.setBrokerId("");
            mSaudaHeader.setTransactionType("NFT");
            mSaudaHeader.setVAT("");
            mSaudaHeader.setBranchCode("");
            mSaudaHeader.setSaudaValidity("");
            issuccess = true;
        } catch (Exception ignored) {}
        return issuccess;
    }
    @SuppressLint({"SimpleDateFormat", "SetTextI18n"})
    public void ShowNoSaudaDialog() {
        final Dialog dialogNoSauda = new Dialog(BargainActivity.this, R.style.PauseDialog);
        dialogNoSauda.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogNoSauda.setContentView(R.layout.user_instruction_dialog);
        dialogNoSauda.setCancelable(false);
        TextView title =  dialogNoSauda.findViewById(R.id.title);
        title.setText("Please state the reason for no Order");
        final EditText edReason =  dialogNoSauda.findViewById(R.id.ed_input);
        final Button submit =  dialogNoSauda.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            dialogNoSauda.cancel();
            String reason = "";
            reason = edReason.getText().toString();
            if (NoSaudaHeaderData(Constants.selectedCustomer.getCustomerCode(), reason)) {
                String timeStamp = "";
                timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                mAceDnsTransactionDatabase.INSERTtoSaudaHeader(mSaudaHeader, timeStamp, "NFT");
                mAceDnsTransactionDatabase.insertToLocationTable("NFT", timeStamp);
                new TRANS_SubmitSaudaOreder(mContext, true).execute();
            } else {
                Toast.makeText(BargainActivity.this, "Error in Sauda data", Toast.LENGTH_LONG).show();
            }
        });
        dialogNoSauda.show();
    }
    private void freightRateSelectionProcess() {
        if(isSecondaryFreightIncluded) {
            selectedFreightRate = mAceDnsDatabase.GetFreightRateByBranchCodeFromBranchRouteFreightMasterForbargain(mSaudaDepoCode, selectedRouteCode, mLoadabilityTon, verticalValueOfEmployee,Constants.selectedCustomer.getTransportMode());
            if (mDepotOrPlant.equalsIgnoreCase("depot")) {
                lodabilityToneFORDEPOT = mAceDnsDatabase.GetLoadabilityTonForDepotSAUDA(Constants.selectedBranch.getBranchCode(), Constants.selectedCustomer.getRouteCode(), verticalValueOfEmployee, "branch_route_freight");
            } else {
                selectedFreightRate="0";
            }
        } else {
            selectedFreightRate="0";
        }
    }
    public void PrepareOrderData(final int doWhat) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.setCancelable(false);
        ploader.show();
        new Thread() {
            public void run() {
                if (doWhat == 1) {
                    freightRateSelectionProcess();
                    minCapacityOfCurrentTransportMode = mAceDnsDatabase.GetMinCapacityOfTransportMode(mSaudaDepoCode, selectedRouteCode, Constants.selectedCustomer.getTransportMode());
                    productMasterList = new ArrayList<>();
                    if (mAceDnsDatabase.ismcxOpenOrCLoseForCurrentCustomerAndSku("", Constants.selectedCustomer.getCustomerCode())) {
                        productMasterList = mAceDnsDatabase.GetSelectedProductListBargainMcxOpen(Constants.selectedBranch.getBranchCode(), verticalOfUser);
                    } else {
                        productMasterList = mAceDnsDatabase.GetSelectedProductListBargain(Constants.selectedBranch.getBranchCode(), verticalOfUser);
                    }
                }
                Message msgObj = orderDataHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("WHAT TO SHOW", doWhat);
                msgObj.setData(b);
                orderDataHandler.sendMessage(msgObj);
            }
        }.start();
    }
    public void DisableInput() {
        mEditTextQuantity.setEnabled(false);
        mEditTextTD.setEnabled(false);
        mEditSecondaryFreight.setEnabled(false);
    }
    public double TotalSaleRate(double ignoredBasicRate, double ignoredFreightcharge, double tradediscount, double premium) {
        double amount = 0.0;
        if (this.basicRate > 0) {
            if (mSaudaType.equalsIgnoreCase("FOR") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(selectedVerticalOfUser)) {
                amount = this.basicRate + freightRate + premium + depotCost + primaryFreight + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) - tradediscount;
            } else {
                amount = this.basicRate + premium + depotCost + primaryFreight + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) - tradediscount;
            }
        }
        return Math.round(amount * 100.0) / 100.0;
    }
    private void enableOrDisableLiquidationDiscountlayout() {
        if (Constants.saudaFormDetailsObj.getSpecialDiscountVertical().contains(selectedVerticalOfUser) && Utils.isNumeric(Constants.maxLiquidationDiscountForCurrentCustomer) && Double.parseDouble(Constants.maxLiquidationDiscountForCurrentCustomer) > 0) {
            liquidationDiscountLayout.setVisibility(View.VISIBLE);
        }
    }
}
