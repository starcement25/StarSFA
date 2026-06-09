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
import android.text.TextWatcher;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnFocusChangeListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ExpandableListView;
import android.widget.ExpandableListView.OnChildClickListener;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ExpandableListAdapterOld;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSaudaOreder;
import com.forcepower.acedns.bean.BrokerMaster;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMRPDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.bean.SaudaHeader;
import com.forcepower.acedns.bean.SelectedProductList;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.math.BigDecimal;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.List;

import static com.forcepower.acedns.constants.Constants.BrokerageCost;
import static com.forcepower.acedns.constants.Constants.HoneyCombCost;
import static com.forcepower.acedns.constants.Constants.lodabilityToneFORDEPOT;
import static com.forcepower.acedns.constants.Constants.mDepotOrPlant;
import static com.forcepower.acedns.constants.Constants.marginCost;
import static com.forcepower.acedns.constants.Constants.maxLiquidationDiscountForCurrentCustomer;
import static com.forcepower.acedns.constants.Constants.maxallocation;
import static com.forcepower.acedns.constants.Constants.selectedVerticalOfUser;

/**
 * An activity which will use for booking order of Forward Trading
 *
 * @author Sourav Das <souravd@coral.in>
 * @version 5.1.9
 */


public class SaudaActivity extends AceDnsParentActivity {
    public static ImageView mImageViewHeaderLogo = null;
    public static LinearLayout mLinearLayoutCartDetails = null;
    public static LinearLayout mLinearLayoutFreight = null;
    public static LinearLayout calculated_salerate_layout = null;
    public static LinearLayout check_td_premium_layout = null;
    public static LinearLayout mLinearLayoutQuantityMRP = null;
    public static LinearLayout mLinearLayoutStock = null;
    public static Button mButtonBack = null;
    public static Button mButtonAddtoCart = null;
    public static Button mButtonCheckOut = null;
    public static Button mButtonNoOrder = null;
    public static Button mButtonSelectProduct = null;
    public static EditText mEditTextClosingStock = null;
    public static EditText mEditTextTD = null;
    public static EditText mEditTextQuantity = null;
    public static EditText mEditTextSaleRate = null;
    public static EditText mEditSecondaryFreight = null;
    public static EditText ed_calculated_salerate = null;
    public static TextView mTextViewCustomerName = null;
    public static TextView textViewRoute = null;
    public static TextView mTextViewProductDetails = null;
    public static TextView mTextViewDepoDetails = null;
    public static TextView textViewVertical = null;
    public static TextView mTextViewTDorPremium = null;
    public static TextView mTextViewClosingStock = null;
    public static TextView mTextViewNoofOrder = null;
    public static TextView mTextViewUOMType = null;
    public static TextView mTextViewTotalOrderAmount = null;
    public static TextView mEditTextLiquidationDiscount = null;
    public static RadioGroup mRadioGroupTDorPremium = null;
    public static LinearLayout liquidationDiscountLayout = null;
    public static double TotalBalance = 100.0;
    public boolean isProductSelected = false;
    public boolean isTDinput = false;
    public boolean isPremiuminput = false;
    public boolean rpeatedProductEntry = false;
    public boolean carryInSales = false;
    public boolean shouldCheckSaudaDespatchOrigin = false;
    public Context mContext;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    double freightRate = 0, calculatedSaleRate = 0, depotCost = 0, primaryFreight = 0, basicRate;
    ProgressDialog ploader;
    Handler orderDataHandler;
    SaudaHeader mSaudaHeader;
    SaudaDetails mSaudaDetails;
    ProductMRPDetails currentProductMRPDetails;
    ExpandableListAdapterOld mExpandableListAdapterOld;
    ExpandableListView mExpandableListView;
    List<String> listDataHeader;
    HashMap<String, List<String>> listDataChild;
    HashMap<String, ArrayList<ProductMRPDetails>> mProductMasterList;
    ArrayList<SelectedProductList> mSelectedProductList;
    private String mCustomerName = "";
    private String mSaudaBookedType = "";
    private String mSaudaType = "";
    private String mSaudaDepoName = "";
    private String selectedFreightRate = "", mSaudaDespatchOrigin = "", verticalOfUser = "", selectedRouteName;
    private String mConversionFactor = "";
    private String mGroupCode = "";
    private String mMaxTD = "";
    private String mSaleRate = "";
    private String mMrpValue = "";
    private String mProductCode = "";
    private String mDnsProductCode = "";
    private String mProductName = "";
    private String mQuantity = "";
    private String mMrpCode = "";
    private String mTD = "";
    private String mVAT = "";
    private String mPremium = "";
    private String mAmount = "";
    private String mFreightCharge = "";
    private String mSaudaMode = "";
    private Boolean isBasicRateOk = true;
    private Boolean isDepotCostOk = true;
    private Boolean isPrimaryFreightOk = true;
    private Boolean isFreightRateOk = true;
    private Boolean isMarginCostOk = true;
    private String inputLiquidationDiscountForCurrentProduct = "";
    private int POSITION = 0; // psittion of the saudaallocationlistarray
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;

    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sauda);
        RegisterActivities.registerActivity(this);

        Constants.isFromConfirmationActivity = false;

        mContext = SaudaActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        mCustomerName = getIntent().getStringExtra("CUSTOMER");
        mSaudaBookedType = getIntent().getStringExtra("SAUDABOOKEDTYPE");

        String verticalValueOfEmployee = mAceDnsDatabase.VerticalValueOfEmployee(Constants.selectedCustomer.getEmpCode());
        if (verticalValueOfEmployee.matches("HBC:Rasoi:BIB")) {
            maxallocation = Double.parseDouble(getIntent().getStringExtra("setmaxAlloc"));
        } else {
            for (int i = 0; i < Constants.selectedAlocatedSaudaList.size(); i++) {
                maxallocation = maxallocation + Double.parseDouble(Constants.selectedAlocatedSaudaList.get(i).getBalance());
            }
        }
        mSaudaType = getIntent().getStringExtra("SAUDATYPE");
        mSaudaDepoName = getIntent().getStringExtra("DEPODETAILS");
        selectedFreightRate = getIntent().getStringExtra("FREIGHTRATE");
        mSaudaDespatchOrigin = getIntent().getStringExtra("mSaudaDespatchOrigin");
        verticalOfUser = getIntent().getStringExtra("selectedVerticalOfUser");
        selectedRouteName = getIntent().getStringExtra("selectedRouteName");
        shouldCheckSaudaDespatchOrigin = getIntent().getBooleanExtra("shouldCheckSaudaDespatchOrigin", false);

        Constants.mBrokerMasterList = new ArrayList<BrokerMaster>();
        Constants.selectedProductMasterList = new ArrayList<ProductMasterDetails>();
        Constants.selectedGroupList = new ArrayList<ProductGroupDetails>();
        Constants.selectedSubGroupList = new ArrayList<ProductSubGrpDetails>();
        Constants.selectedBrandList = new ArrayList<ProductBrandDetails>();

        Constants.selectedSaudaDetailsList = new ArrayList<SaudaDetails>();
        Constants.SelectedSkuCode = new ArrayList<String>();

        InitializeView();
        DrawLayout();
        enableOrDisableLiquidationDiscountlayout();
        isTDinput = true;
        isPremiuminput = false;
        SetData();
        mButtonSelectProduct.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                PrepareOrderData(1, "");
            }
        });

        orderDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                SaudaActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                SetProductHeaderAndProductDescription();
                                ShowProductGroupAndProductListDialog();
                                break;
                            case 2:
                                break;
                            case 3:
                                break;
                            case 4:
                                break;
                            case 5:
                                break;
                            case 6:
                                break;
                        }
                    }
                });
            }
        };


        DisableInput();

        mRadioGroupTDorPremium
                .setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                    public void onCheckedChanged(RadioGroup group, int checkedId) {
                        RadioButton radioSelection = (RadioButton) findViewById(checkedId);
                        if (radioSelection.getText().equals("TD")) {
                            mTextViewTDorPremium.setText("TD ");
                            isTDinput = true;
                            isPremiuminput = false;
                            try {
                                if (isProductSelected) {
                                    calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, Double.valueOf(mEditTextTD.getText().toString()), 0);
                                    ed_calculated_salerate.setText(String.format("%.2f", calculatedSaleRate));
                                }

                            } catch (Exception e) {

                            }

                        } else {
                            mTextViewTDorPremium.setText("Premium");
                            isTDinput = false;
                            isPremiuminput = true;
                            try {
                                if (isProductSelected) {
                                    calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, 0, Double.valueOf(mEditTextTD.getText().toString()));
                                    ed_calculated_salerate.setText(String.format("%.2f", calculatedSaleRate));
                                }

                            } catch (Exception e) {

                            }
                        }
                        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser) && (!isBasicRateOk || !isDepotCostOk || !isPrimaryFreightOk || !isFreightRateOk || !isMarginCostOk)) {
                            ed_calculated_salerate.setText("0.00");
                        }
                    }
                });

    }

    @Override
    public void onResume() {
        super.onResume();

        if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
            rpeatedProductEntry = true;
        } else {
            rpeatedProductEntry = false;
        }

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

    /**
     * Called when the activity is first created. Initializes the UI
     * for users interaction.
     */

    public void InitializeView() {
        mLinearLayoutFreight = (LinearLayout) findViewById(R.id.amount_layout);
        calculated_salerate_layout = (LinearLayout) findViewById(R.id.calculated_salerate_layout);
        check_td_premium_layout = (LinearLayout) findViewById(R.id.check_td_premium_layout);
        liquidationDiscountLayout = (LinearLayout) findViewById(R.id.liquidationDiscountLayout);
        mEditTextLiquidationDiscount = (TextView) findViewById(R.id.mEditTextLiquidationDiscount);
        mEditSecondaryFreight = (EditText) findViewById(R.id.ed_amt);
        ed_calculated_salerate = (EditText) findViewById(R.id.ed_calculated_salerate);

        mEditTextQuantity = (EditText) findViewById(R.id.mEditTextQuantity);
        mEditTextQuantity
                .setOnEditorActionListener(new OnEditorActionListener() {
                    @Override
                    public boolean onEditorAction(TextView v, int actionId,
                                                  KeyEvent event) {
                        if (event == null) {
                            if (actionId == EditorInfo.IME_ACTION_DONE) {
                                // edTd.requestFocus();
                                InputMethodManager imm = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
                                imm.hideSoftInputFromWindow(
                                        mEditTextQuantity.getWindowToken(), 0);
                                getWindow()
                                        .setSoftInputMode(
                                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                                return true;
                            }
                        }
                        return false;
                    }
                });
        mEditTextQuantity.setOnFocusChangeListener(new OnFocusChangeListener() {
            public void onFocusChange(View v, boolean hasFocus) {
                if (hasFocus) {
                    ((EditText) v).setText("");
                } else {
                }
            }
        });
        mTextViewTDorPremium = (TextView) findViewById(R.id.mTextViewTDorPremium);
        mEditTextTD = (EditText) findViewById(R.id.mEditTextTD);
        mEditTextTD.addTextChangedListener(new TextWatcher() {

            @Override
            public void afterTextChanged(Editable s) {
            }

            @Override
            public void beforeTextChanged(CharSequence s, int start,
                                          int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start,
                                      int before, int count) {
                try {
                    if (isProductSelected) {
                        if (s.length() != 0) {
                            if (isTDinput) {
                                calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, Double.parseDouble(s.toString()), 0);
                            } else//if(isPremiuminput)
                            {
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

                } catch (Exception e) {

                }
            }
        });
        mEditSecondaryFreight.setTextSize(15);

        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        //bodyLayout = (LinearLayout) findViewById(R.id.body_layout);
        mLinearLayoutCartDetails = (LinearLayout) findViewById(R.id.customer_layout);
        mLinearLayoutCartDetails.setVisibility(View.INVISIBLE);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));
        //filterLayout = (LinearLayout) findViewById(R.id.filter_layout);
        mLinearLayoutQuantityMRP = (LinearLayout) findViewById(R.id.qty_mrp_layout);
        mLinearLayoutStock = (LinearLayout) findViewById(R.id.stk_td_layout);

        mTextViewTotalOrderAmount = (TextView) findViewById(R.id.txt_total);
        mTextViewNoofOrder = (TextView) findViewById(R.id.txt_order_count);
        mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());

        mButtonAddtoCart = (Button) findViewById(R.id.btn_continue);
        mButtonAddtoCart.setOnClickListener(SaudaActivity.this);
        mButtonAddtoCart.setTag(101);
        mButtonAddtoCart.setEnabled(false);

        mButtonCheckOut = (Button) findViewById(R.id.btn_order_form);
        mButtonCheckOut.setTag(102);
        mButtonCheckOut.setEnabled(false);
        mButtonCheckOut.setOnClickListener(SaudaActivity.this);

        mButtonNoOrder = (Button) findViewById(R.id.no_ordr);
        mButtonNoOrder.setTag(104);
        mButtonNoOrder.setOnClickListener(SaudaActivity.this);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setTag(105);
        mButtonBack.setOnClickListener(SaudaActivity.this);

        mButtonSelectProduct = (Button) findViewById(R.id.buttonSelectProductDetails);


        textViewRoute = (TextView) findViewById(R.id.textViewRoute);
        mTextViewCustomerName = (TextView) findViewById(R.id.textViewCustomername);
        mTextViewProductDetails = (TextView) findViewById(R.id.textViewProductDetails);
        mTextViewDepoDetails = (TextView) findViewById(R.id.textViewDepoDetails);
        textViewVertical = (TextView) findViewById(R.id.textViewDespatchOrigin);

        mTextViewClosingStock = (TextView) findViewById(R.id.textViewCalculateedAllocation);

        textViewRoute.setText("");
        mTextViewCustomerName.setText("");
        mTextViewProductDetails.setText("");
        mTextViewDepoDetails.setText("");
        mTextViewClosingStock.setText("");

        mRadioGroupTDorPremium = (RadioGroup) findViewById(R.id.radioSelectType);

    }

    /**
     * Called when the activity is first created. Draw the UI dynamically
     * for users interaction.
     */
    public void DrawLayout() {
        EnableDisableLayout(false);
    }


    /**
     * Called when the activity is first created. set the name of the customer
     * who is going to booked sauda. Set the name of Depot from where product is
     * taken. set the type of Sauda.
     */
    public void SetData() {
        textViewRoute.setText("Route : " + selectedRouteName);
        mTextViewCustomerName.setText("Customer : " + mCustomerName);
        mTextViewDepoDetails.setText(mSaudaType + " : " + mSaudaDepoName);
        mSaudaMode = mSaudaType;
        textViewVertical.setVisibility(View.VISIBLE);
        textViewVertical.setText("Vertical : " + verticalOfUser);

    }

    private void calculatePrimarySecondaryFreightRateDepotCostAndShow() {
        isDepotCostOk = true;
        isPrimaryFreightOk = true;
        isFreightRateOk = true;
        isMarginCostOk = true;
        String currentDepotCost = currentProductMRPDetails.getDepotCost();
        String currentPrimaryFreight = currentProductMRPDetails.getPrimaryFreight();
        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser) && mDepotOrPlant.equalsIgnoreCase("plant")) {
            depotCost = 0.00;

        } else {
            if (Utils.isNumeric(currentDepotCost))
//			if(Utils.isNumeric(currentDepotCost) && Double.parseDouble(currentDepotCost)>0)
            {
                depotCost = Double.valueOf(currentDepotCost);
            } else {
                if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser)) {
                    isDepotCostOk = false;
                }
            }

        }
        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser) && mDepotOrPlant.equalsIgnoreCase("plant")) {
            primaryFreight = 0.00;
        } else {
            if (Utils.isNumeric(currentPrimaryFreight) && Double.parseDouble(currentPrimaryFreight) > 0) {
                primaryFreight = Double.valueOf(currentPrimaryFreight);
            } else {
                if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser)) {
                    isPrimaryFreightOk = false;
                }
            }
        }
        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser)) {
            String plant_name = mAceDnsDatabase.getPlantNameFromBranchCode();
            HoneyCombCost = mAceDnsDatabase.GetHoneyCombCostByProductCode(currentProductMRPDetails.getDnsProductCode(), plant_name);
            marginCost = mAceDnsDatabase.GetMarginCostByProductCode(currentProductMRPDetails.getDnsProductCode());
            if (!Utils.isNumeric(HoneyCombCost)) {
                HoneyCombCost = "0.00";
            }
            if (!Utils.isNumeric(marginCost)) {
                marginCost = "0.00";
                isMarginCostOk = false;
            }
        } else {
            HoneyCombCost = "0.00";
            marginCost = "0.00";
            isMarginCostOk = true;
        }


        depotCost = Math.round(depotCost * 100.0) / 100.0;
        primaryFreight = Math.round(primaryFreight * 100.0) / 100.0;

        if (mSaudaType.equalsIgnoreCase("FOR")) {
            mLinearLayoutFreight.setVisibility(View.VISIBLE);
            if (Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(selectedVerticalOfUser)) {

                mEditSecondaryFreight.setEnabled(false);
                if (selectedFreightRate != null && !selectedFreightRate.matches("")) {
                    String trackLoadQuantity = "";
                    if (mDepotOrPlant.equalsIgnoreCase("depot")) {
                        trackLoadQuantity = mAceDnsDatabase.GetTruckLoadQuantityByDnsProductCode(currentProductMRPDetails.getDnsProductCode(), lodabilityToneFORDEPOT, Constants.selectedCustomer.getTransportMode(), false);
                    } else {
                        trackLoadQuantity = mAceDnsDatabase.GetTruckLoadQuantityByDnsProductCode(currentProductMRPDetails.getDnsProductCode(), Constants.selectedCustomer.getLoadabilityTon(), Constants.selectedCustomer.getTransportMode(), false);
                    }
                    if (trackLoadQuantity != null && !trackLoadQuantity.matches("")) {
                        freightRate = Double.parseDouble(selectedFreightRate) / Double.parseDouble(trackLoadQuantity);
                        freightRate = Math.round(freightRate * 100.0) / 100.0;
                        mEditSecondaryFreight.setText(String.format("%.2f", freightRate));
                    } else {
                        isFreightRateOk = false;
                    }

                }
                mLinearLayoutFreight.setVisibility(View.GONE);
            } else {
                mEditSecondaryFreight.setEnabled(true);
            }

        } else {
            mLinearLayoutFreight.setVisibility(View.GONE);
        }

        ed_calculated_salerate.setEnabled(false);
//		mLinearLayoutFreight.setVisibility(View.VISIBLE);
        calculated_salerate_layout.setVisibility(View.VISIBLE);
        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes")) {
            check_td_premium_layout.setVisibility(View.VISIBLE);
        } else {
            check_td_premium_layout.setVisibility(View.GONE);
        }

        try {
            if (isProductSelected) {
                calculatedSaleRate = TotalSaleRate(Double.parseDouble(mSaleRate), freightRate, 0, 0);
                ed_calculated_salerate.setText(String.format("%.2f", calculatedSaleRate));
                if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser) && (!isBasicRateOk || !isDepotCostOk || !isPrimaryFreightOk || !isFreightRateOk)) {
                    ed_calculated_salerate.setText("0.00");
                }

            }

        } catch (Exception e) {

        }

    }

    public void EnableDisableLayout(boolean isShown) {
        //filterLayout.setEnabled(isShown);
        mLinearLayoutQuantityMRP.setEnabled(isShown);
        mLinearLayoutStock.setEnabled(isShown);
    }

    public void onClick(View clkdView) {

        if (clkdView == mButtonAddtoCart) {
            boolean isQty = false;
            boolean isTd = false;
            boolean isFreight = false;
            boolean isSaleRate = false;
            double amount = 0.0;
            Boolean LiquidationDiscountValid = true;
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
                } else {
                    inputLiquidationDiscountForCurrentProduct = "0";
                }
            } else {
                inputLiquidationDiscountForCurrentProduct = "0";
            }
            if (!LiquidationDiscountValid) {
                return;
            }
            Boolean isSecondaryFreightOk = true;

            if (mSaudaType.equalsIgnoreCase("FOR")) {
//					if(Constants.saudaFormDetailsObj.getincoterms_vertical().equalsIgnoreCase(selectedVerticalOfUser) && mDepotOrPlant.equalsIgnoreCase("plant"))
//					{
//						freightRate=0;
//					}
                if (!Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(selectedVerticalOfUser)) {
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
                    if (!Utils.isNumeric(freightRate + "") || !isFreightRateOk) {
                        isSecondaryFreightOk = false;
                        Toast.makeText(mContext, "Price is not updated! Please Synchronize Data.", Toast.LENGTH_SHORT).show();
                    }
                }
            } else {
                freightRate = 0;
            }

            if (!isSecondaryFreightOk) {
                return;
            }
            if (mProductMasterList.size() > 0) {
                String gettd = currentProductMRPDetails.getTD();
                mMaxTD = gettd;
                mGroupCode = currentProductMRPDetails.getGroupCode();
                mProductCode = currentProductMRPDetails.getProductCode();
                mDnsProductCode = currentProductMRPDetails.getDnsProductCode();
                mProductName = currentProductMRPDetails.getProductDescription();
                mMrpCode = currentProductMRPDetails.getMrpCode();

                if (mSaleRate.length() > 0) {
                    if (Double.parseDouble(mSaleRate) > 0) {
                        isSaleRate = true;
                    } else {
                        isSaleRate = false;
                    }
                } else {
                    isSaleRate = false;
                }

                if (mEditTextQuantity.getText().toString() != null
                        && mEditTextQuantity.getText().toString().length() > 0
                        && !mEditTextQuantity.getText().toString()
                        .equalsIgnoreCase("0")
                        && !mEditTextQuantity.getText().toString()
                        .equalsIgnoreCase(".")
                        && Double.parseDouble(mEditTextQuantity.getText().toString()) != 0) {

                    String inpqty = mEditTextQuantity.getText().toString().trim();

                    double maxAllo = Double.parseDouble(inpqty);
                    if (selectedVerticalOfUser.matches("HBC:Rasoi:BIB")) {

                        maxAllo = mAceDnsDatabase.calculatedValueC2M(currentProductMRPDetails.getProductCode(), maxAllo);
                        if (maxallocation > 0 && maxAllo <= maxallocation) {
                            maxallocation = maxallocation - maxAllo;
                            mQuantity = inpqty;
                            isQty = true;
                        } else {
                            Toast.makeText(SaudaActivity.this, "Exceed Quantity", Toast.LENGTH_LONG).show();
                        }
                    } else {
                        if (maxAllo > maxallocation) {
                            Toast.makeText(SaudaActivity.this, "Exceed Quantity", Toast.LENGTH_LONG).show();
                        } else {
                            mQuantity = inpqty;
                            isQty = true;
                        }
                    }

                } else {
                    Toast.makeText(SaudaActivity.this, "Invalid Quantity", 2000)
                            .show();
                }

                if (isTDinput == true && isPremiuminput == false) {
                    if (!mEditTextTD.getText().toString().equalsIgnoreCase(".")) {
                        if (mEditTextTD.getText().toString() == null
                                || mEditTextTD.getText().toString().length() == 0
                                || mEditTextTD.getText().toString()
                                .equalsIgnoreCase("0")) {
                            mTD = "0";
                            isTd = true;
                        } else {
                            String inptd = mEditTextTD.getText().toString();
                            String s = Constants.menuDetailsObj.getrun_time_TD_approval_vertical();
                            if (!selectedVerticalOfUser.contains(s))//skipping max td checking
                            {
                                if (Double.parseDouble(inptd) == Double
                                        .parseDouble(gettd)) {
                                    mTD = inptd;
                                    isTd = true;
                                } else if (Double.parseDouble(inptd) <= Double
                                        .parseDouble(gettd)) {
                                    mTD = inptd;
                                    isTd = true;

                                } else {
                                    Toast.makeText(SaudaActivity.this,
                                            "Exceed Trade Discount", Toast.LENGTH_LONG).show();
                                }
                            } else {
                                mTD = inptd;
                                isTd = true;
                            }

                        }

                    } else {
                        Toast.makeText(SaudaActivity.this,
                                "Invalid Trade Discount", Toast.LENGTH_LONG).show();
                    }
                }

                if (isTDinput == false && isPremiuminput == true) {
                    if (!mEditTextTD.getText().toString().equalsIgnoreCase(".")) {
                        if (mEditTextTD.getText().toString() == null
                                || mEditTextTD.getText().toString().length() == 0
                                || mEditTextTD.getText().toString()
                                .equalsIgnoreCase("0")) {
                            mTD = "0";
                            mPremium = "0";
                            isTd = true;
                        } else {
                            String inptd = mEditTextTD.getText().toString();
                            mTD = "0";
                            mPremium = inptd;
                            isTd = true;
                        }

                    } else {
                        Toast.makeText(SaudaActivity.this,
                                "Invalid Premium", Toast.LENGTH_LONG).show();
                    }
                }
                if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(selectedVerticalOfUser) && (!isBasicRateOk || !isDepotCostOk || !isPrimaryFreightOk || !isFreightRateOk || !isMarginCostOk)) {
                    isFreight = false;
                    if (!Constants.employeeDetailObject.getEmpCode().equalsIgnoreCase("E0042")) {
                        Utils.showToast(mContext, "Error: Please Synchronize Data.");
                    } else {
                        if (!isBasicRateOk) {
                            Utils.showToast(mContext, "Basic rate not updated.");
                        } else if (!isDepotCostOk) {
                            Utils.showToast(mContext, "Depot cost not updated.");
                        } else if (!isPrimaryFreightOk) {
                            Utils.showToast(mContext, "Primary freight not updated.");
                        } else if (!isPrimaryFreightOk) {
                            Utils.showToast(mContext, "Secondary freight not updated.");
                        } else {
                            Utils.showToast(mContext, "Margin cost not updated.");
                        }
                    }


                } else {
                    isFreight = true;
                }
                if (mSaudaMode.equalsIgnoreCase("FOR")) {


//						if (mEditSecondaryFreight.getText().toString() != null
//								&& mEditSecondaryFreight.getText().toString().length() > 0
//								&& !mEditSecondaryFreight.getText().toString()
//								.equalsIgnoreCase("0"))
//						{
//
//							mFreightCharge = mEditSecondaryFreight.getText().toString();
//							isFreight = true;
//						}
//						else
//						{
//							Toast.makeText(SaudaActivity.this,"Invalid Freight charge", Toast.LENGTH_LONG).show();
//
//						}

                    if (isQty == true && isTd == true && isFreight == true && isSaleRate == true
                            && isTDinput == true && isPremiuminput == false) {
                        amount = TotalAmount(Double.valueOf(mQuantity),
                                Double.valueOf(mSaleRate),
                                Double.valueOf(0.00),
                                Double.valueOf(mTD), 0.0);
                        BigDecimal aaa = new BigDecimal(amount);
                        aaa = Utils.round(aaa, 2);
                        mAmount = String.valueOf(aaa);
                        mPremium = "0";
                        boolean issucess = BookedSauda(mProductCode,
                                mProductName, mQuantity, mMrpCode, mTD,
                                mSaleRate, mVAT, mAmount, mFreightCharge, mPremium, "yes");
                        if (issucess == true) {
                            Constants.SelectedSkuCode.add(mProductCode);
                            REFRESHSaudaActivity(true);
                        }

                    } else if (isQty == true && isTd == true && isFreight == true && isSaleRate == true
                            && isTDinput == false && isPremiuminput == true) {
                        amount = TotalAmount(Double.valueOf(mQuantity),
                                Double.valueOf(mSaleRate),
                                Double.valueOf(0.00),
                                0.0, Double.valueOf(mPremium));
                        BigDecimal aaa = new BigDecimal(amount);
                        aaa = Utils.round(aaa, 2);
                        mAmount = String.valueOf(aaa);
                        boolean issucess = BookedSauda(mProductCode,
                                mProductName, mQuantity, mMrpCode, mTD,
                                mSaleRate, mVAT, mAmount, mFreightCharge, mPremium, "no");
                        if (issucess == true) {
                            Constants.SelectedSkuCode.add(mProductCode);
                            REFRESHSaudaActivity(true);
                        }
                    } else {
                        if (isSaleRate == false) {
                            Toast.makeText(SaudaActivity.this,
                                    "Sale rate of this product is 0\nPlease Synchronize Data", Toast.LENGTH_LONG).show();
                        } else {
                            Toast.makeText(SaudaActivity.this,
                                    "Please provide valid input", Toast.LENGTH_LONG).show();
                        }
                    }
                } else {
                    if (isFreight && isQty == true && isTd == true && isTDinput == true && isSaleRate == true && isPremiuminput == false) {
                        amount = TotalAmount(Double.valueOf(mQuantity),
                                Double.valueOf(mSaleRate), 0.0,
                                Double.valueOf(mTD), 0.0);
                        BigDecimal aaa = new BigDecimal(amount);
                        aaa = Utils.round(aaa, 2);
                        mAmount = String.valueOf(aaa);
                        mPremium = "0";
                        boolean issucess = BookedSauda(mProductCode,
                                mProductName, mQuantity, mMrpCode, mTD,
                                mSaleRate, mVAT, mAmount, mFreightCharge, mPremium, "yes");
                        if (issucess == true) {
                            Constants.SelectedSkuCode.add(mProductCode);
                            REFRESHSaudaActivity(true);
                        }
                    } else if (isFreight && isQty == true && isTd == true && isTDinput == false && isSaleRate == true && isPremiuminput == true) {
                        amount = TotalAmount(Double.valueOf(mQuantity),
                                Double.valueOf(mSaleRate), 0.0,
                                0.0, Double.valueOf(mPremium));
                        BigDecimal aaa = new BigDecimal(amount);
                        aaa = Utils.round(aaa, 2);
                        mAmount = String.valueOf(aaa);
                        boolean issucess = BookedSauda(mProductCode,
                                mProductName, mQuantity, mMrpCode, mTD,
                                mSaleRate, mVAT, mAmount, mFreightCharge, mPremium, "no");
                        if (issucess == true) {
                            Constants.SelectedSkuCode.add(mProductCode);
                            REFRESHSaudaActivity(true);
                        }
                    } else {
                        if (isSaleRate == false) {
                            Toast.makeText(SaudaActivity.this,
                                    "Sale rate of this product is 0\nPlease Synchronize Data", Toast.LENGTH_LONG).show();
                        } else {
                            if (!isFreight)
                                Toast.makeText(SaudaActivity.this, "Please provide valid input", Toast.LENGTH_LONG).show();
                        }
                    }
                }
            } else {
                Toast.makeText(SaudaActivity.this, "Please select a product",
                        2000).show();
            }

        }

        if (clkdView == mButtonCheckOut) {
            if (Constants.selectedSaudaDetailsList.size() > 0) {
                mTextViewClosingStock.setText("");
                mLinearLayoutCartDetails.setVisibility(View.INVISIBLE);
                Intent intent = new Intent(SaudaActivity.this,
                        SaudaConfirmationActivity.class);
                intent.putExtra("CARRY_IN", carryInSales);
                intent.putExtra("SAUDATYPE", mSaudaType);
                intent.putExtra("SAUDABOOKED", mSaudaBookedType);
                startActivity(intent);
            } else {
                Toast.makeText(SaudaActivity.this, "Please select a product",
                        2000).show();
            }
        }
        if (clkdView == mButtonNoOrder) {
            new GPSTracker(mContext);
            ShowNoSaudaDialog();
        }
        if (clkdView == mButtonBack) {
            if (Constants.selectedProductMasterList.size() > 0) {
                AceDnsTransactionDatabase dbObj = new AceDnsTransactionDatabase(
                        mContext);
                dbObj.decreaseBALSauda();
                dbObj.close();
            }
            finish();
        }
    }

    /**
     * This Function is Called when a single product is added to cart.
     * Initialize all the input to zero. Calculate the casebalance balance and upadte the allocation table.
     * for users interaction.
     */

    public void REFRESHSaudaActivity(Boolean showToast) {
        try {
            int sizeOfProductsInCart = Constants.selectedSaudaDetailsList.size();
            if (sizeOfProductsInCart > 0) {

                double casebalance = GETBalance(maxallocation,
                        Double.valueOf(mQuantity));
                double balance = CalculateUOM2(casebalance,
                        Double.valueOf(mConversionFactor));
                UPDATESaudaAllocation(POSITION, String.valueOf(balance));
                mLinearLayoutCartDetails.setVisibility(View.VISIBLE);
                mLinearLayoutCartDetails.setBackgroundColor(Color.YELLOW);

                mTextViewNoofOrder.setText("" + sizeOfProductsInCart);
                mTextViewTotalOrderAmount.setText(mAmount);
                mButtonCheckOut.setEnabled(true);
                if (showToast) {
                    Toast.makeText(SaudaActivity.this, "Product has been added to cart.", Toast.LENGTH_LONG).show();
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
        } catch (Exception e) {

        }

    }

    /**
     * @param customercode customercode who is not willing to take any sauda.
     * @param remarks      Reason of not doing the sauda.
     * @return boolean
     * @throws null pointer exception
     */
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
        } catch (Exception ex) {
            issuccess = false;
        }
        return issuccess;
    }

    /**
     * This Function is Called when a customer is not willing to do any sauda
     * This functin open a dialog where an employee can provide reason behind of not doing sauda.
     */

    public void ShowNoSaudaDialog() {
        final Dialog dialogNoSauda = new Dialog(SaudaActivity.this, R.style.PauseDialog);
        dialogNoSauda.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogNoSauda.setContentView(R.layout.user_instruction_dialog);
        dialogNoSauda.setCancelable(false);
        TextView title = (TextView) dialogNoSauda.findViewById(R.id.title);
        title.setText("Please state the reason for no Order");
        final EditText edReason = (EditText) dialogNoSauda
                .findViewById(R.id.ed_input);
        final Button submit = (Button) dialogNoSauda.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                dialogNoSauda.cancel();
                String reason = "";
                reason = edReason.getText().toString();
                if (NoSaudaHeaderData(
                        Constants.selectedCustomer.getCustomerCode(), reason) == true) {

                    String timeStamp = "";
                    timeStamp = Constants.dateString
                            + new SimpleDateFormat("HHmmss").format(Calendar
                            .getInstance().getTime());

                    mAceDnsTransactionDatabase.INSERTtoSaudaHeader(mSaudaHeader,
                            timeStamp, "NFT");
                    mAceDnsTransactionDatabase.insertToLocationTable("NFT", timeStamp);

                    new TRANS_SubmitSaudaOreder(mContext, true).execute();

                } else {
                    Toast.makeText(SaudaActivity.this, "Error in Sauda data",
                            2000).show();
                }
            }
        });
        dialogNoSauda.show();
    }

    /**
     * This Function is used to Show Product Group and product List in a dialog in UI
     */

    public void ShowProductGroupAndProductListDialog() {
        if (mSelectedProductList.size() > 0) {
            final Dialog dialogProduct = new Dialog(SaudaActivity.this, R.style.PauseDialog);
            dialogProduct.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialogProduct.setContentView(R.layout.product_expandable_list);
            dialogProduct.setTitle("Please select an option");
            dialogProduct.setCancelable(true);
            mExpandableListView = (ExpandableListView) dialogProduct
                    .findViewById(R.id.exp_list);
            mExpandableListView.setDividerHeight(2);
            mExpandableListView.setGroupIndicator(null);
            mExpandableListView.setClickable(true);

            mExpandableListAdapterOld = new ExpandableListAdapterOld(this, listDataHeader, listDataChild);
            mExpandableListView.setAdapter(mExpandableListAdapterOld);
            mExpandableListView.setOnChildClickListener(new OnChildClickListener() {
                @Override
                public boolean onChildClick(ExpandableListView parent, View v,
                                            int groupPosition, int childPosition, long id) {
                    String text = "Category :"
                            + listDataHeader.get(groupPosition)
                            + "\n"
                            + listDataChild.get(
                            listDataHeader.get(groupPosition)).get(
                            childPosition);
                    mTextViewProductDetails.setText(text);
                    isProductSelected = true;
                    GenerateProductDetials(groupPosition, childPosition);
                    mButtonAddtoCart.setEnabled(true);
                    EnableInput();
                    dialogProduct.cancel();
                    return false;
                }
            });
            dialogProduct.show();

        } else {
            Toast.makeText(SaudaActivity.this,
                    "There are no items left.Please submit order.", 2000)
                    .show();
        }
    }

    /**
     * This function Generate Product Details which will show in the UI.
     *
     * @param groupposition position of the product group
     * @param childposition position of the product
     */

    public void GenerateProductDetials(int groupposition, int childposition) {
        currentProductMRPDetails = mProductMasterList.get(listDataHeader.get(groupposition)).get(childposition);


        mConversionFactor = currentProductMRPDetails.getConversionFactor();

        if (Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {

            for (int count = 0; count < Constants.selectedAlocatedSaudaList.size(); count++) {
                if (currentProductMRPDetails.getGroupCode().equalsIgnoreCase(
                        Constants.selectedAlocatedSaudaList.get(count)
                                .getProductFilterCode())) {
                    if (Double.parseDouble(Constants.selectedAlocatedSaudaList
                            .get(count).getQty()) > 0) {
                        if (Constants.selectedAlocatedSaudaList.get(count)
                                .getBalance().equalsIgnoreCase("0")) {
                            POSITION = count;
                            TotalBalance = Double
                                    .parseDouble(Constants.selectedAlocatedSaudaList.get(count).getQty());

                            String conversionFactorTwo = currentProductMRPDetails.getConversionFactorTwo();
                            double conversionFactorTwoInDouble = 0.0;
                            if (Utils.isNumeric(currentProductMRPDetails.getConversionFactorTwo())) {
                                conversionFactorTwoInDouble = Double.parseDouble(conversionFactorTwo);
                            }
                            maxallocation = CalculateUOM1(TotalBalance, Double.parseDouble(mConversionFactor), conversionFactorTwoInDouble);

                        } else {
                            POSITION = count;
                            TotalBalance = Double
                                    .parseDouble(Constants.selectedAlocatedSaudaList
                                            .get(count).getBalance());
                            String conversionFactorTwo = currentProductMRPDetails.getConversionFactorTwo();
                            double conversionFactorTwoInDouble = 0.0;
                            if (Utils.isNumeric(currentProductMRPDetails.getConversionFactorTwo())) {
                                conversionFactorTwoInDouble = Double.parseDouble(conversionFactorTwo);
                            }
                            maxallocation = CalculateUOM1(TotalBalance, Double.parseDouble(mConversionFactor), conversionFactorTwoInDouble);
                        }
                        break;
                    } else {

                    }
                }
            }
        }

        if (maxallocation == 0.0) {
            mTextViewClosingStock.setText("");
            Toast.makeText(mContext, "Error Max Allocation is 0 ", Toast.LENGTH_LONG).show();

        } else {
            try {
                ShowSaudaRate();

                String VerticalValueOfEmployee = mAceDnsDatabase.VerticalValueOfEmployee(Constants.selectedCustomer.getEmpCode());
                if (VerticalValueOfEmployee.matches("HBC:Rasoi:BIB")) {
                    double max = mAceDnsDatabase.calculatedValueM2C(currentProductMRPDetails.getProductCode(), maxallocation);
                    max = Math.round(max * 100.0) / 100.0;
                    mTextViewClosingStock.setText(max + "");
                } else {
                    double max = maxallocation;
                    max = Math.round(max * 100.0) / 100.0;
                    mTextViewClosingStock.setText(max + "");
                }

                mTextViewClosingStock.setEnabled(false);
                calculatePrimarySecondaryFreightRateDepotCostAndShow();
            } catch (Exception e) {

            }

        }

    }

    /**
     * This Function remove the repeated product from the list
     * if skucode already exist
     *
     * @param skucode skucode of the particular product
     * @return yes if product is already exist
     */

    public boolean RemoveRepeatedProduct(String skucode) {
        boolean isRepeated = false;
        for (int count = 0; count < Constants.SelectedSkuCode.size(); count++) {
            if (skucode.equalsIgnoreCase(Constants.SelectedSkuCode.get(count))) {
                isRepeated = true;
                break;
            }
        }
        return isRepeated;
    }

    /**
     * This Function update the sauda allocation if a product is added to cart
     *
     * @param position position of the sauda allocation list
     * @param balance  balance of the product after adding to cart
     */

    public void UPDATESaudaAllocation(int position, String balance) {
        Constants.selectedAlocatedSaudaList.get(position).setBalance(balance);

    }

    /**
     * This Function return the balance after adding a product to cart
     *
     * @param totalallocation total allocation of the product
     * @param allocation      allocation of the product
     * @return double as a balance
     */

    public double GETBalance(double totalallocation, double allocation) {
        return (totalallocation - allocation);
    }

    /**
     * This Function calculate UOM two according to the conversion factor and return
     * the value after calculation.
     *
     * @param cases      quantity in case which is provided by the user
     * @param conversion factor
     *                   conversion factor of the product
     * @return double after doing the calculation
     */
    public double CalculateUOM2(double cases, double conversionfactor) {
        double UOM2 = (cases * conversionfactor);
        double finalValue = Math.round(UOM2 * 100.0) / 100.0;
        return finalValue;
    }

    /**
     * This Function calculate UOM one according to the conversion factor and return
     * the value after calculation.
     *
     * @param ltr        quantity in ltr which is provided by the user
     * @param conversion factor
     *                   conversion factor of the product
     * @return double after doing the calculation
     */
    public double CalculateUOM1(double ltr, double conversionfactor, double conversionfactorTwo) {

        if (!Constants.saudaFormDetailsObj.getsauda_allocation_app_vertical().contains(selectedVerticalOfUser))
//		if(Constants.menuDetailsObj.getTD_allocation_vertical()!=null && Constants.menuDetailsObj.getTD_allocation_vertical().contains(selectedVerticalOfUser))
        {
            String SaudaLimitForSelectedCustomerCode = Constants.selectedCustomer.getSaudaLimit();
            String PendingQuantityForSelectedCustomerCode = Constants.selectedCustomer.getPendingQty();
            double saudaLimit = 0.0, pendingQty = 0.0;
            if (Utils.isNumeric(SaudaLimitForSelectedCustomerCode)) {
                saudaLimit = Double.parseDouble(SaudaLimitForSelectedCustomerCode);
            }
            if (Utils.isNumeric(PendingQuantityForSelectedCustomerCode)) {
                pendingQty = Double.parseDouble(PendingQuantityForSelectedCustomerCode);
            }
            if (saudaLimit <= 0) {
                Utils.showToast(mContext, "Invalid or zero sauda limit. Please Synchronize Data.");
                return 0.00;
            }

            try {
                double totalQty = saudaLimit - pendingQty;
                totalQty = (totalQty * conversionfactorTwo) / conversionfactor;
                double finalValue = Math.round(totalQty * 100.0) / 100.0;
                return finalValue;
            } catch (Exception e) {
                return 0.00;
            }
        } else {
            double UOM1 = (ltr / conversionfactor);
            double finalValue = Math.round(UOM1 * 100.0) / 100.0;
            return finalValue;
        }

    }


    public void PrepareOrderData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.setCancelable(false);
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        mSelectedProductList = mAceDnsDatabase.GetSelectedProductList(Constants.selectedBranch.getBranchCode(), verticalOfUser);
                        break;
                    case 2:

                        break;
                    case 3:

                        break;
                    case 4:

                        break;

                    case 5:
                        break;

                    case 6:
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

    /**
     * This Function enable the input UI.
     */
    public void EnableInput() {
        mEditTextQuantity.setEnabled(true);
        mEditTextTD.setEnabled(true);
//		mEditSecondaryFreight.setEnabled(true);
    }

    /**
     * This Function disable the input UI.
     */
    public void DisableInput() {
        mEditTextQuantity.setEnabled(false);
        mEditTextTD.setEnabled(false);
        mEditSecondaryFreight.setEnabled(false);
//		mEditTextLiquidationDiscount.setEnabled(false);
    }


    public void ShowSaudaRate() {
        try {

            String value = currentProductMRPDetails.getSaleRate();
            if (Utils.isNumeric(value)) {
                basicRate = Double.valueOf(value);
                basicRate = Math.round(basicRate * 100.0) / 100.0;
                if (basicRate > 0) {
                    isBasicRateOk = true;
                } else {
                    isBasicRateOk = false;
                }
                if (Constants.orderFormDetailsObj.getMrp().equalsIgnoreCase("yes")) {
                    mMrpValue = value;
                    mEditTextSaleRate.setText(mMrpValue);
                }
                if (Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes")) {
                    mSaleRate = value;
                    mEditTextSaleRate.setText(mSaleRate);
                }

                if (value.length() > 0) {
                    if (Double.parseDouble(value) > 0) {
                    } else {
                        Toast.makeText(mContext, "Sale rate of this product is 0\nPlease Synchronize Data", Toast.LENGTH_LONG).show();
                    }
                } else {
                    Toast.makeText(mContext, "Sale rate of this product is 0\nPlease Synchronize Data", Toast.LENGTH_LONG).show();

                }
            } else {
                isBasicRateOk = false;
                Toast.makeText(mContext, "There was some problem getting sale rate.\nPlease Synchronize Data", Toast.LENGTH_SHORT).show();
            }


        } catch (Exception e) {

        }

    }

    /**
     * This Function save the data of the product to the collection buffer which is added to cart.
     *
     * @param productcode      code of the product
     * @param productname      name of the product
     * @param bookedqty        booked quantity of the product
     * @param mrpcode          mrp code of the product
     * @param tradediscount    trade discount of the product
     * @param salerate         sale rate of the product
     * @param vat              vat of the product
     * @param amount           calculated amount of the product
     * @param freightcharge    freight charge of the product
     * @param premium          premium price of the product
     * @param tdorpremiumcheck td or premium checking of the product
     * @return true if successfully saved to buffer.
     */
    public Boolean BookedSauda(String productcode, String productname,
                               String bookedqty, String mrpcode, String tradediscount,
                               String salerate, String vat, String amount, String freightcharge, String premium, String tdorpremiumcheck) {

        mSaudaDetails = new SaudaDetails();
        Boolean issaudabooked = false;
        mSaudaDetails.setGroupCode(mGroupCode);
        mSaudaDetails.setSkuCode(productcode);
        mSaudaDetails.setProductName(productname);
        mSaudaDetails.setQuantity(bookedqty);
        mSaudaDetails.setMrpCode(mrpcode);
        mSaudaDetails.setTD(tradediscount);
        mSaudaDetails.setMaxTD(mMaxTD);
        mSaudaDetails.setSaleRate(String.valueOf(basicRate));
        mSaudaDetails.setConversionFactor(mConversionFactor);
        mSaudaDetails.setVAT(vat);
        mSaudaDetails.setAmount(amount);
        mSaudaDetails.setFreightCharge(String.valueOf(freightRate));
        mSaudaDetails.setPremium(premium);
        mSaudaDetails.setTDorPremiumCheck(tdorpremiumcheck);
        mSaudaDetails.setPrimaryFreight(String.valueOf(primaryFreight));
        mSaudaDetails.setDepotCost(String.valueOf(depotCost));
        mSaudaDetails.setHoneyCombCost(String.valueOf(HoneyCombCost));
        mSaudaDetails.setMarginCost(String.valueOf(marginCost));
        mSaudaDetails.setLiquidTD(inputLiquidationDiscountForCurrentProduct);
        Constants.selectedSaudaDetailsList.add(mSaudaDetails);
        issaudabooked = true;
        return issaudabooked;
    }

    /**
     * This Function is Called to set product header and product description for expandable list view.
     * to show at UI.
     */
    public void SetProductHeaderAndProductDescription() {

        ArrayList<ProductGroupDetails> productgrpdetailslist = RemoveRepeatedProductGroup();

        mProductMasterList = new HashMap<String, ArrayList<ProductMRPDetails>>();
        listDataHeader = new ArrayList<String>();
        listDataChild = new HashMap<String, List<String>>();

        for (int count = 0; count < productgrpdetailslist.size(); count++) {
            String productgroupname = productgrpdetailslist.get(count).getGroupName();
            String productgroucode = productgrpdetailslist.get(count).getGroupCode();
            listDataHeader.add(productgroupname);

            List<String> productlist = new ArrayList<String>();
            ArrayList<ProductMRPDetails> mProductMRPDetails = new ArrayList<ProductMRPDetails>();

            for (int countproductlist = 0; countproductlist < mSelectedProductList
                    .size(); countproductlist++) {

                ProductMRPDetails productmrpdetails = new ProductMRPDetails();

                if (productgroucode.equalsIgnoreCase(mSelectedProductList.get(
                        countproductlist).getGroupCode())) {

                    String productskucode = mSelectedProductList
                            .get(countproductlist).getProductCode();
                    if (RemoveRepeatedProduct(productskucode) == false) {
                        productlist.add(mSelectedProductList.get(countproductlist)
                                .getProductDescription());

                        productmrpdetails.setGroupCode(mSelectedProductList
                                .get(countproductlist).getGroupCode());
                        productmrpdetails.setProductCode(mSelectedProductList
                                .get(countproductlist).getProductCode());
                        productmrpdetails.setDnsProductCode(mSelectedProductList
                                .get(countproductlist).getDnsProductCode());
                        productmrpdetails.setBasicRate(mSelectedProductList
                                .get(countproductlist).getBasicRate());
                        productmrpdetails.setPrimaryFreight(mSelectedProductList
                                .get(countproductlist).getPrimaryFreight());
                        productmrpdetails.setDepotCost(mSelectedProductList
                                .get(countproductlist).getDepotCost());
                        productmrpdetails.setProductDescription(mSelectedProductList
                                .get(countproductlist).getProductDescription());
                        productmrpdetails.setUOM1(mSelectedProductList
                                .get(countproductlist).getUOM1());
                        productmrpdetails.setUOM2(mSelectedProductList
                                .get(countproductlist).getUOM2());
                        productmrpdetails.setUOM3(mSelectedProductList
                                .get(countproductlist).getUOM3());
                        productmrpdetails.setConversionFactor(mSelectedProductList
                                .get(countproductlist).getConversionFactor());
                        productmrpdetails.setConversionFactorTwo(mSelectedProductList
                                .get(countproductlist).getConversionFactorTwo());
                        productmrpdetails.setTD(mSelectedProductList
                                .get(countproductlist).getTD());
                        productmrpdetails.setMrpCode(mSelectedProductList
                                .get(countproductlist).getMrpCode());
                        productmrpdetails.setSaleRate(mSelectedProductList
                                .get(countproductlist).getSaleRate());
                        productmrpdetails.setBranchCode(mSelectedProductList
                                .get(countproductlist).getBranchCode());

                        mProductMRPDetails.add(productmrpdetails);
                        Log.i("LIST DATA", mSelectedProductList.get(countproductlist)
                                .getProductDescription());

                    }
                }
            }
            mProductMasterList.put(productgroupname, mProductMRPDetails);
            listDataChild.put(productgroupname, productlist);
        }
    }

    /**
     * This Function removes repeated product group from the collection list and return the
     * filter list.
     *
     * @return collection of product group details
     */
    public ArrayList<ProductGroupDetails> RemoveRepeatedProductGroup() {
        ArrayList<ProductGroupDetails> mProductGroupDetailsList = new ArrayList<ProductGroupDetails>();
        String grpcode = "";
        for (int i = 0; i < mSelectedProductList.size(); i++) {
            ProductGroupDetails productgroupdetails = new ProductGroupDetails();

            if (grpcode.equalsIgnoreCase(mSelectedProductList.get(i)
                    .getGroupCode()) == false) {

                grpcode = mSelectedProductList.get(i).getGroupCode();
                productgroupdetails.setGroupCode(mSelectedProductList.get(i)
                        .getGroupCode());
                productgroupdetails.setGroupName(mSelectedProductList.get(i)
                        .getGroupName());
                mProductGroupDetailsList.add(productgroupdetails);
            }
        }
        return mProductGroupDetailsList;
    }

    /**
     * This Function calculate Total Amount of the product after added to cart return
     * the value after calculation.
     *
     * @param quantity      quantity in liter/case which is provided by the user
     * @param salerate      conversion factor of the product
     * @param freightcharge freight charge of the product
     * @param tradediscount trade discount of the product
     * @param premium       premium price of the product
     * @return double after doing the calculation
     */
    public double TotalAmount(double quantity, double salerate,
                              double freightcharge, double tradediscount, double premium) {
        double amount = quantity * (this.basicRate + freightRate + premium + depotCost + primaryFreight + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) - tradediscount - Double.parseDouble(inputLiquidationDiscountForCurrentProduct));
        double finalamount = Math.round(amount * 100.0) / 100.0;
        return finalamount;
    }

    public double TotalSaleRate(double basicRate,
                                double freightcharge, double tradediscount, double premium) {
        double amount = 0.0;
        if (this.basicRate > 0) {
            if (mSaudaType.equalsIgnoreCase("FOR") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(selectedVerticalOfUser)) {
                amount = this.basicRate + freightRate + premium + depotCost + primaryFreight + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) - tradediscount;
            } else {
                amount = this.basicRate + premium + depotCost + primaryFreight + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) - tradediscount;
            }
        }

        double finalamount = Math.round(amount * 100.0) / 100.0;
        return finalamount;
    }

    private void enableOrDisableLiquidationDiscountlayout() {
        if (Constants.saudaFormDetailsObj.getSpecialDiscountVertical().contains(selectedVerticalOfUser) && Utils.isNumeric(Constants.maxLiquidationDiscountForCurrentCustomer) && Double.parseDouble(Constants.maxLiquidationDiscountForCurrentCustomer) > 0) {
            liquidationDiscountLayout.setVisibility(View.VISIBLE);
        } else {
            liquidationDiscountLayout.setVisibility(View.GONE);
        }
    }

}
