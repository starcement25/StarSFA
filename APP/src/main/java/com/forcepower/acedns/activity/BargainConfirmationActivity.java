package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;

import android.text.InputType;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemLongClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitSaudaOreder;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BargainConfirmAdapter;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.bean.SaudaHeader;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.math.BigDecimal;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.Objects;

import static java.lang.Double.parseDouble;
import static com.forcepower.acedns.constants.Constants.BrokerageCost;
import static com.forcepower.acedns.constants.Constants.HoneyCombCost;
import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;
import static com.forcepower.acedns.constants.Constants.mChosenUomType;
import static com.forcepower.acedns.constants.Constants.marginCost;
import static com.forcepower.acedns.constants.Constants.maxLiquidationDiscountForCurrentCustomer;
import static com.forcepower.acedns.constants.Constants.maxallocation;
import static com.forcepower.acedns.constants.Constants.selectedCustomer;
import static com.forcepower.acedns.constants.Constants.selectedSaudaDetailsList;
import static com.forcepower.acedns.constants.Constants.selectedVerticalOfUser;
import static com.forcepower.acedns.constants.Constants.totalOrderAmount;

public class BargainConfirmationActivity extends AppCompatActivity implements OnClickListener, OnItemClickListener, OnItemLongClickListener {
    @SuppressLint("StaticFieldLeak")
    public static ListView mListViewProductDetails = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonAddProduct = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonRemarks = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonTradeDiscount = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSaleType = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonVAT = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView totalQtyTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView totalAmountTV = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewTotal = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewLogo = null;

    public static String TAG = "SAUDA CONFIRMATION ACTIVITY";
    public boolean isTdInput = false;
    String mConversionFactor = "";
    int POSITION = 0;
    double mMaxAllocation = 0.0;
    double mInputQuantity = 0.0;
    String mProductCode = "";
    String mGroupCode = "";
    String mQuantity = "";
    String mMaxTD = "";
    String mTradeDiscount = "0";
    SaudaHeader mSaudaHeader;
    String mSaudaType = "";
    String mSaudaBooked = "";
    String mRemarks = "";
    String mValidity = "";
    String mCurrentDate = "";
    String mTodayDate = "";
    BargainConfirmAdapter bargainConfirmAdapter;
    AceDnsTransactionDatabase dataHelperObj;
    Context mContext;
    String saleType = "";
    Handler mHandler;
    ProgressDialog loader;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    boolean carryInSales = false;
    CaldroidListener listener;
    Button multipleRemarksButton;
    SimpleDateFormat dateFormat;
    String vat = "0.00";
    String remarksDate = "";
    String inputLiquidationDiscountForCurrentProduct = "0";
    Double freightRate = 0.0;
    double basicRate, secondaryFreight, primaryFreight, depotCost, additionalPremium, additionalTD;
    private AceDnsDatabase mAceDnsDatabase;
    private CaldroidFragment dialogCaldroidFragment;

    @SuppressLint({"SimpleDateFormat", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_bargain_confirmation);
        RegisterActivities.registerActivity(this);
        Toolbar myToolbar = findViewById(R.id.my_toolbar);
        setSupportActionBar(myToolbar);
        Objects.requireNonNull(getSupportActionBar()).setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayShowTitleEnabled(false);
        carryInSales = getIntent().getBooleanExtra("CARRY_IN", false);
        mSaudaType = getIntent().getStringExtra("SAUDATYPE");
        mSaudaBooked = getIntent().getStringExtra("SAUDABOOKED");

        Constants.isFromConfirmationActivity = true;

        mContext = BargainConfirmationActivity.this;
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        ViewInitialization();
        GetCurerentDate();

        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("customer wise")) {
            mTradeDiscount = Constants.selectedCustomer.getTradeDiscount();
        }

        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase("credit")) {
            saleType = "CREDIT";
        }

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    BargainConfirmationActivity.this.runOnUiThread(() -> new TRANS_SubmitSaudaOreder(mContext, true).execute());
                }
            }
        };

        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                try {
                    String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
                    if (!date.after(currentDate)) {
                        dialogCaldroidFragment.dismiss();
                        multipleRemarksButton.setText(dateFormat.format(date));
                        remarksDate = new SimpleDateFormat("yyyyMMdd").format(date);
                    } else {
                        Utils.showToast(mContext, "Future Dates cannot be selected.");
                    }
                } catch (Exception ignored) {
                }
            }

            @Override
            public void onLongClickDate(Date date, View view) {
                try {
                    String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
                    if (!date.after(currentDate)) {
                        dialogCaldroidFragment.dismiss();
                        multipleRemarksButton.setText(dateFormat.format(date));
                        remarksDate = new SimpleDateFormat("yyyyMMdd").format(date);
                    } else {
                        Utils.showToast(mContext, "Future Dates cannot be selected.");
                    }
                } catch (Exception ignored) {
                }
            }
        };
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewLogo.setVisibility(View.VISIBLE);
            mImageViewLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewLogo.setVisibility(View.GONE);
        }
    }

    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }

    @SuppressLint("SetTextI18n")
    public void ViewInitialization() {
        mImageViewLogo = findViewById(R.id.imagelogo);
        mListViewProductDetails = findViewById(R.id.listView_product);
        mButtonAddProduct = findViewById(R.id.button_add);
        mButtonSubmit = findViewById(R.id.button_order_submit);
        mButtonRemarks = findViewById(R.id.button_remarks);
        mButtonBack = findViewById(R.id.button_back);
        mButtonTradeDiscount = findViewById(R.id.button_discount);
        mButtonSaleType = findViewById(R.id.button_saletype);
        mButtonVAT = findViewById(R.id.button_vat);
        mTextViewTotal = findViewById(R.id.textView_total);
        mButtonAddProduct.setOnClickListener(BargainConfirmationActivity.this);
        mButtonSubmit.setOnClickListener(BargainConfirmationActivity.this);
        mButtonRemarks.setOnClickListener(BargainConfirmationActivity.this);
        mButtonBack.setOnClickListener(BargainConfirmationActivity.this);
        mButtonTradeDiscount.setOnClickListener(BargainConfirmationActivity.this);
        mButtonSaleType.setOnClickListener(BargainConfirmationActivity.this);
        mButtonVAT.setOnClickListener(BargainConfirmationActivity.this);
        GrandTotalAmount();
        bargainConfirmAdapter = new BargainConfirmAdapter(mContext, R.layout.bargain_confirm_list_layout, Constants.selectedSaudaDetailsList, mSaudaType);
        mListViewProductDetails.setAdapter(bargainConfirmAdapter);
        mButtonSaleType.setVisibility(View.GONE);
        TextView textView_heading = findViewById(R.id.textView_heading);
        textView_heading.setText("Approx Bargain Value : ");
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mButtonTradeDiscount.setVisibility(View.GONE);
        mButtonVAT.setVisibility(View.GONE);
        totalOrderAmount = 0.00;
        double totalOrderQty = 0.0;
        double totalQtyLoose = 0.0, totalQtyCase = 0.0;
        for (int i = 0; i < selectedSaudaDetailsList.size(); i++) {
            SaudaDetails saudaDetails = selectedSaudaDetailsList.get(i);
            double saleRate = parseDouble(saudaDetails.getSaleRate());
            double secondaryFreight = parseDouble(saudaDetails.getFreightCharge());
            double additionalPremium = parseDouble(saudaDetails.getadditionalPremium());
            double additionalTD = parseDouble(saudaDetails.getadditionalTD());
            double brokerageCost = parseDouble(saudaDetails.getBrokarageCost());
            double brokerageCostSS = parseDouble(saudaDetails.getBrokarageCostSS());
            double td = parseDouble(saudaDetails.getTD());
            double qty = parseDouble(saudaDetails.getQuantity());
            saleRate = saleRate + secondaryFreight + brokerageCost + brokerageCostSS + td + additionalPremium - additionalTD;
            double amountBeforeVat = saleRate * qty;
            double vatAmount = 0.00, totalAmount;
            double vatRateInDouble = 0.00;

            if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
                String vatRate = saudaDetails.getVat();
                if (Utils.isNumeric(vatRate) && Double.parseDouble(vatRate) > 0) {
                    vatRateInDouble = Double.parseDouble(vatRate);
                }
                if (vatRateInDouble > 0) {
                    vatAmount = (amountBeforeVat * vatRateInDouble) / 100;
                }
            }
            totalAmount = (amountBeforeVat + vatAmount);
            totalOrderQty = totalOrderQty + qty;
            totalOrderAmount = totalOrderAmount + totalAmount;
            if (!mChosenUomType.equalsIgnoreCase("loose")) {
                double totalQtyCaseCurrent = Double.parseDouble(saudaDetails.getQuantity());
                double totalQtyLooseCurrent = mAceDnsDatabase.calculatedValueC2MBargain(saudaDetails.getSkuCode(), totalQtyCaseCurrent);
                totalQtyLoose = totalQtyLoose + totalQtyLooseCurrent;
                totalQtyCase = totalQtyCase + totalQtyCaseCurrent;
            } else {
                double totalQtyLooseCurrent = Double.parseDouble(saudaDetails.getQuantity());
                double totalQtyCaseCurrent = mAceDnsDatabase.calculatedValueM2CBargain(saudaDetails.getSkuCode(), totalQtyLooseCurrent);
                totalQtyLoose = totalQtyLoose + totalQtyLooseCurrent;
                totalQtyCase = totalQtyCase + totalQtyCaseCurrent;
            }
        }
        totalQtyTV = findViewById(R.id.totalQtyTV);
        totalAmountTV = findViewById(R.id.totalAmountTV);
        totalAmountTV.setText("Approx Value: " + defaultFormatWithComma.format(totalOrderAmount));
        String uom = mChosenUomType;
        if (uom.equalsIgnoreCase("loose")) {
            totalQtyTV.setText(defaultFormat.format(totalQtyLoose));
        } else {
            totalQtyTV.setText(defaultFormat.format(totalQtyLoose) + "/" + (int) totalQtyCase);
        }
    }

    @SuppressLint("SetTextI18n")
    private void showRemarksDialog() {
        final Dialog checkoutDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        checkoutDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        checkoutDialog.setContentView(R.layout.remarks_material);
        checkoutDialog.setCancelable(false);
        TextView title = checkoutDialog.findViewById(R.id.title);
        Button submit = checkoutDialog.findViewById(R.id.btn_submit);
        title.setText("Narration");

        final ImageView back = checkoutDialog.findViewById(R.id.back);
        back.setOnClickListener(v -> {
            checkoutDialog.cancel();

        });
        submit.setOnClickListener(v -> {
            final EditText remarks_box = checkoutDialog.findViewById(R.id.remark_box);
            Constants.bargainNarration = remarks_box.getText().toString().trim();
            checkoutDialog.cancel();
            if (selectedCustomer.getCustomerType().equalsIgnoreCase("D") || selectedCustomer.getCustomerType().equalsIgnoreCase("SS")) {
                mButtonSubmit.setEnabled(false);
                if (GenerateSaudaHeaderData()) {
                    new GPSTracker(mContext);
                    SaveSaudaDataToDatabase();
                } else {
                    mButtonSubmit.setEnabled(true);
                    Utils.showToast(mContext, "Error in Bargain Data");
                }
            } else {
                ShowPoConfirmationDialog();
            }
        });
        checkoutDialog.show();
    }

    public void ShowPoConfirmationDialog() {
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.CustomMaterialDialogTheme);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.po_confirm_dialog);
        mDialogCustomer.setCancelable(false);

        Button btn_add = mDialogCustomer.findViewById(R.id.btn_add);
        final EditText poET = mDialogCustomer.findViewById(R.id.ed_po);
        poET.setText(Constants.marginPoNo);
        btn_add.setOnClickListener(v -> {
            Constants.marginPoNo = poET.getText().toString().trim();
            if (selectedCustomer.getCustomerType().equalsIgnoreCase("c") && Constants.marginPoNo.length() < 3) {
                Utils.showToast(mContext, "Must provide proper PO no. for chosen customer.");
                return;
            }
            mDialogCustomer.cancel();
            mButtonSubmit.setEnabled(false);
            if (GenerateSaudaHeaderData()) {
                new GPSTracker(mContext);
                SaveSaudaDataToDatabase();
            } else {
                mButtonSubmit.setEnabled(true);
                Utils.showToast(mContext, "Error in Bargain Data");
            }
        });
        mDialogCustomer.show();
    }

    @Override
    public void onClick(View arg0) {
        if (arg0 == mButtonAddProduct) {
            finish();
        } else if (arg0 == mButtonSubmit) {
            showRemarksDialog();
        } else if (arg0 == mButtonBack) {
            finish();
        } else if (arg0 == mButtonSaleType) {
            showSaleTypeDialog();
        } else if (arg0 == mButtonRemarks) {
            ShowRemarksDateDialog();
        } else if (arg0 == mButtonTradeDiscount) {
            ShowTradeDiscountDialog();
        } else if (arg0 == mButtonVAT) {
            showVATDialog();
        }
    }

    @SuppressLint("SetTextI18n")
    public void showVATDialog() {
        final Dialog vatDialog = new Dialog(mContext);
        vatDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        vatDialog.setContentView(R.layout.vat_dialog);
        TextView title = vatDialog.findViewById(R.id.title);
        title.setText("Enter VAT amount");
        final EditText edInst = vatDialog.findViewById(R.id.ed_input);
        edInst.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        Button submit = vatDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            if (!edInst.getText().toString().isEmpty() && !edInst.getText().toString().equalsIgnoreCase(".")) {
                vat = edInst.getText().toString();
            }
            bargainConfirmAdapter.notifyDataSetChanged();
            vatDialog.cancel();
        });
        vatDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowTradeDiscountDialog() {
        final Dialog discountDialog = new Dialog(mContext);
        discountDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        discountDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = discountDialog.findViewById(R.id.title);
        title.setText("Enter Trade Discount ?");
        final EditText editTextTD = discountDialog.findViewById(R.id.ed_input);
        editTextTD.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        Button submit = discountDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            String td = "0";
            if (!editTextTD.getText().toString().isEmpty() && !editTextTD.getText().toString().equalsIgnoreCase(".")) {
                td = editTextTD.getText().toString();
            }
            if (Double.parseDouble(td) <= 100) {
                discountDialog.cancel();
                mTradeDiscount = td;
            } else {
                Toast.makeText(BargainConfirmationActivity.this, "Trade Discount cannot be more than 100 %", Toast.LENGTH_LONG).show();
            }
        });
        discountDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        instructionDialog.setCancelable(false);
        TextView title = instructionDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst = instructionDialog.findViewById(R.id.ed_input);
        edInst.setText(mRemarks);
        Button submit = instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            instructionDialog.cancel();
            String instruction = "";
            instruction = edInst.getText().toString();
            mRemarks = instruction;
        });
        instructionDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowRemarksDateDialog() {
        final Dialog RemarksDialog = new Dialog(mContext);
        RemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        RemarksDialog.setContentView(R.layout.remarks_date_layout_material);
        RemarksDialog.setCancelable(false);
        TextView title = RemarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any?");
        final EditText edittextRemarks = RemarksDialog.findViewById(R.id.ed_input_r1);
        Button submit = RemarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            if (edittextRemarks.getText().toString().isEmpty() && !edittextRemarks.getText().toString().equalsIgnoreCase(".")) {
                mRemarks = "";
            } else {
                mRemarks = edittextRemarks.getText().toString();
            }
            RemarksDialog.cancel();
        });
        RemarksDialog.show();
    }

    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    @SuppressLint("SetTextI18n")
    public void showSaleTypeDialog() {
        final Dialog grpDialog = new Dialog(BargainConfirmationActivity.this);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.sale_type_dialog);
        grpDialog.setCancelable(false);
        TextView title = grpDialog.findViewById(R.id.title);
        title.setText("Select a Sale Type.");
        final RadioButton radioCredit = grpDialog.findViewById(R.id.rd_credit);
        final RadioButton radioCOD = grpDialog.findViewById(R.id.rd_cod);
        final RadioButton radioPay = grpDialog.findViewById(R.id.rd_pay);

        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase("cash")) {
            radioCredit.setVisibility(View.GONE);
        } else {
            radioCredit.setVisibility(View.VISIBLE);
        }

        radioCredit.setOnCheckedChangeListener((buttonView, isChecked) -> {
            if (isChecked) {
                radioCOD.setChecked(false);
                radioPay.setChecked(false);
                saleType = "CREDIT";
                grpDialog.cancel();
            }
        });
        radioCOD.setOnCheckedChangeListener((buttonView, isChecked) -> {
            if (isChecked) {
                radioCredit.setChecked(false);
                radioPay.setChecked(false);
                saleType = "COD";
                grpDialog.cancel();
            }
        });
        radioPay.setOnCheckedChangeListener((buttonView, isChecked) -> {
            if (isChecked) {
                radioCredit.setChecked(false);
                radioCOD.setChecked(false);
                saleType = "CASH";
                grpDialog.cancel();
            }
        });
        grpDialog.show();
    }

    @Override
    public void onItemClick(AdapterView<?> arg0, View arg1, int pos, long arg3) {
        mGroupCode = Constants.selectedSaudaDetailsList.get(pos).getGroupCode();
        mQuantity = Constants.selectedSaudaDetailsList.get(pos).getQuantity();
        mConversionFactor = Constants.selectedSaudaDetailsList.get(pos).getConversionFactor();
        mMaxTD = Constants.selectedSaudaDetailsList.get(pos).getMaxTD();
        isTdInput = Constants.selectedSaudaDetailsList.get(pos).getTDorPremiumCheck().equalsIgnoreCase("yes");

        GetDeletedITEM(mGroupCode, mQuantity);
        ShowEditSaudaDialog(pos);
    }

    @Override
    public boolean onItemLongClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
        System.out.println("POSITION::::::::::" + arg2);
        mConversionFactor = Constants.selectedSaudaDetailsList.get(arg2).getConversionFactor();
        ShowDeleteItemDialog(arg2);
        return true;
    }

    public void ShowDeleteItemDialog(final int pos) {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(BargainConfirmationActivity.this);
        alertDialogBuilder.setMessage("Are you sure you want to delete this item ?").setCancelable(false);
        alertDialogBuilder.setNegativeButton("OK",
                (dialog, id) -> {
                    dialog.cancel();
                    mProductCode = Constants.selectedSaudaDetailsList.get(pos).getSkuCode();
                    if (selectedVerticalOfUser.matches("HBC:Rasoi:BIB")) {
                        double maxAllo = Double.parseDouble(Constants.selectedSaudaDetailsList.get(pos).getQuantity());
                        maxAllo = mAceDnsDatabase.calculatedValueC2M(mProductCode, maxAllo);
                        maxallocation = maxallocation + maxAllo;
                    }
                    RELEASEProduct(mProductCode);
                    mGroupCode = Constants.selectedSaudaDetailsList.get(pos).getGroupCode();
                    mQuantity = Constants.selectedSaudaDetailsList.get(pos).getQuantity();
                    GetDeletedITEM(mGroupCode, mQuantity);
                    Constants.selectedSaudaDetailsList.remove(Constants.selectedSaudaDetailsList.get(pos));
                    GrandTotalAmount();
                    bargainConfirmAdapter.notifyDataSetChanged();
                });
        alertDialogBuilder.setPositiveButton("CANCEL", (dialog, id) -> dialog.cancel());
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowEditSaudaDialog(final int position) {
        double totalbalance = Double.parseDouble(Constants.selectedAlocatedSaudaList.get(POSITION).getBalance());
        final double maxallocation = CalculateUOM1(totalbalance, Double.parseDouble(mConversionFactor));

        mMaxAllocation = maxallocation;

        final Dialog edQtyDialog = new Dialog(BargainConfirmationActivity.this);

        edQtyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        edQtyDialog.setContentView(R.layout.edit_sauda_activity_layout);
        TextView title = edQtyDialog.findViewById(R.id.title);
        title.setText("Provide valid inputs");

        final LinearLayout FreightLayout = edQtyDialog.findViewById(R.id.layoutFC);
        final LinearLayout liquidationDiscountLayout = edQtyDialog.findViewById(R.id.liquidationDiscountLayout);

        final TextView textViewTDorPremium = edQtyDialog.findViewById(R.id.textViewTD);
        final TextView mEditTextLiquidationDiscount = edQtyDialog.findViewById(R.id.mEditTextLiquidationDiscount);

        final EditText edQty = edQtyDialog.findViewById(R.id.editTextQuantity);
        final EditText edSale = edQtyDialog.findViewById(R.id.editTextSaleRate);
        final EditText edDisc = edQtyDialog.findViewById(R.id.edittextTD);
        final EditText edFreight = edQtyDialog.findViewById(R.id.editTextFC);

        edQty.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edSale.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edDisc.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edFreight.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        mEditTextLiquidationDiscount.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);

        if (Constants.saudaFormDetailsObj.getSpecialDiscountVertical().contains(Constants.selectedVerticalOfUser) && Utils.isNumeric(maxLiquidationDiscountForCurrentCustomer) && Double.parseDouble(maxLiquidationDiscountForCurrentCustomer) > 0) {
            liquidationDiscountLayout.setVisibility(View.VISIBLE);
            mEditTextLiquidationDiscount.setText(Constants.selectedSaudaDetailsList.get(position).getLiquidTD());
        } else {
            liquidationDiscountLayout.setVisibility(View.GONE);
        }
        if (mSaudaType.equalsIgnoreCase("FOR")) {
            if (Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
                edFreight.setEnabled(false);
                FreightLayout.setVisibility(View.GONE);
            } else {
                edFreight.setEnabled(true);
                FreightLayout.setVisibility(View.VISIBLE);
            }
        } else {
            FreightLayout.setVisibility(View.GONE);
        }

        edSale.setEnabled(false);

        TextView txtTDVatTitle = edQtyDialog.findViewById(R.id.txt_td_vat);
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            txtTDVatTitle.setText("VAT                     : ");
        }

        edQty.setText(Constants.selectedSaudaDetailsList.get(position).getQuantity());
        basicRate = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getSaleRate());
        secondaryFreight = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getFreightCharge());
        primaryFreight = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getPrimaryFreight());
        depotCost = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getDepotCost());
        additionalPremium = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getadditionalPremium());
        additionalTD = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getadditionalTD());
        HoneyCombCost = Constants.selectedSaudaDetailsList.get(position).getHoneyCombCost();
        marginCost = Constants.selectedSaudaDetailsList.get(position).getMarginCost();
        double saleRate;
        if (mSaudaType.equalsIgnoreCase("FOR") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
            saleRate = (basicRate + secondaryFreight + primaryFreight + depotCost + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) + additionalPremium) - additionalTD;
        } else {
            saleRate = (basicRate + primaryFreight + depotCost + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) + additionalPremium) - additionalTD;
        }

        if (isTdInput) {
            textViewTDorPremium.setText("Trade Discount");
            double td = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getTD());
            edDisc.setText(defaultFormat.format(td));
            saleRate = saleRate - td;
        } else {
            textViewTDorPremium.setText("Premium");
            double premium = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getPremium());
            edDisc.setText(defaultFormat.format(premium));
            saleRate = saleRate + premium;
        }

        BigDecimal aaa = new BigDecimal(saleRate);
        aaa = Utils.round(aaa, 2);
        edSale.setText(defaultFormat.format(aaa));
        edFreight.setText(Constants.selectedSaudaDetailsList.get(position).getFreightCharge());

        Button submit = edQtyDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            String qty;
            String salerate;
            String discount;
            String freightcharge = "";

            double dquantity = 0.0;
            double dsalerate;
            double ddiscount = 0.0;
            double dfreightcharge = 0.0;
            Double dpremium = 0.0;

            Double dtotalamount;

            boolean boolQty = true, boolDisc = true;
            int boolFreight = 0;
            boolean LiquidationDiscountValid = true;
            if (Constants.saudaFormDetailsObj.getSpecialDiscountVertical().contains(Constants.selectedVerticalOfUser) && Utils.isNumeric(maxLiquidationDiscountForCurrentCustomer) && Double.parseDouble(maxLiquidationDiscountForCurrentCustomer) > 0) {
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
            }
            if (!LiquidationDiscountValid) {
                return;
            }
            Constants.selectedSaudaDetailsList.get(position).setLiquidTD(inputLiquidationDiscountForCurrentProduct);
            boolean isSecondaryFreightOk = true;
            if (mSaudaType.equalsIgnoreCase("FOR")) {
                if (!Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
                    String freighInString = edFreight.getText().toString();
                    if (!Utils.isNumeric(freighInString)) {
                        isSecondaryFreightOk = false;
                        Toast.makeText(mContext, "Please provide valid freight rate!", Toast.LENGTH_SHORT).show();
                    } else if (Double.parseDouble(freighInString) <= 0) {
                        isSecondaryFreightOk = false;
                        Toast.makeText(mContext, "Please check secondary freight value!", Toast.LENGTH_SHORT).show();
                    } else {
                        freightRate = Double.parseDouble(freighInString);
                        freightRate = Math.round(freightRate * 100.0) / 100.0;
                    }
                    Constants.selectedSaudaDetailsList.get(position).setFreightCharge(freightRate + "");
                    secondaryFreight = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getFreightCharge());
                }
            } else {
                freightRate = 0.0;
            }

            if (!isSecondaryFreightOk) {
                return;
            }

            qty = edQty.getText().toString();
            salerate = edSale.getText().toString();
            discount = edDisc.getText().toString();

            if (!qty.isEmpty() && !qty.equalsIgnoreCase("0") && !qty.equalsIgnoreCase(".")) {
                dquantity = Double.parseDouble(qty);
                if (selectedVerticalOfUser.matches("HBC:Rasoi:BIB")) {
                    String skuCodeCurrent = Constants.selectedSaudaDetailsList.get(position).getSkuCode();
                    double maxAllocExistingInput = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getQuantity());
                    maxAllocExistingInput = mAceDnsDatabase.calculatedValueC2M(skuCodeCurrent, maxAllocExistingInput);

                    double maxAllocCurrentInput = Double.parseDouble(qty);
                    maxAllocCurrentInput = mAceDnsDatabase.calculatedValueC2M(skuCodeCurrent, maxAllocCurrentInput);

                    double maxallocationCurrent = Constants.maxallocation + maxAllocExistingInput;
                    if (maxallocationCurrent > 0 && maxAllocCurrentInput <= maxallocationCurrent) {
                        maxallocationCurrent = maxallocationCurrent - maxAllocCurrentInput;
                        Constants.maxallocation = maxallocationCurrent;
                    } else {
                        boolQty = false;
                        Toast.makeText(BargainConfirmationActivity.this, "Exceed Quantity", Toast.LENGTH_LONG).show();
                    }
                } else {
                    if (dquantity > maxallocation) {
                        boolQty = false;
                        Toast.makeText(BargainConfirmationActivity.this, "Exceed Quantity", Toast.LENGTH_LONG).show();
                    }
                }
            } else {
                boolQty = false;
            }

            dsalerate = Double.parseDouble(salerate);

            if (FreightLayout.getVisibility() == View.VISIBLE) {
                freightcharge = edFreight.getText().toString();
                if (!freightcharge.isEmpty() && !freightcharge.equalsIgnoreCase("0") && !freightcharge.equalsIgnoreCase(".")) {
                    dfreightcharge = Double.parseDouble(freightcharge);
                    boolFreight = 1;
                } else {
                    Toast.makeText(BargainConfirmationActivity.this, "Please provide valid Freight", Toast.LENGTH_LONG).show();
                    boolFreight = 2;
                }
            }

            if (isTdInput) {
                if (!discount.isEmpty() && !discount.equalsIgnoreCase(".")) {
                    if (!Constants.selectedVerticalOfUser.contains(Constants.menuDetailsObj.getrun_time_TD_approval_vertical())) {
                        if (Double.parseDouble(discount) <= Double.parseDouble(mMaxTD)) {
                            ddiscount = Double.parseDouble(discount);
                        } else {
                            boolDisc = false;
                            Toast.makeText(BargainConfirmationActivity.this, "Exceed Trade Discount Limit", Toast.LENGTH_LONG).show();
                        }
                    } else {
                        ddiscount = Double.parseDouble(discount);
                    }
                } else {
                    boolDisc = false;
                }
            } else {
                if (!discount.isEmpty() && !discount.equalsIgnoreCase(".")) {
                    dpremium = Double.valueOf(discount);
                } else {
                    boolDisc = false;
                }
            }

            if (isTdInput) {
                if (boolQty && boolDisc && boolFreight == 0) {
                    edQtyDialog.cancel();
                    dtotalamount = TotalAmount(dquantity, dsalerate, dfreightcharge, ddiscount, dpremium);
                    mInputQuantity = dquantity;
                    Constants.selectedSaudaDetailsList.get(position).setQuantity(qty);
                    Constants.selectedSaudaDetailsList.get(position).setAmount(String.valueOf(dtotalamount));
                    Constants.selectedSaudaDetailsList.get(position).setTD(discount);
                } else if (boolQty && boolDisc && boolFreight == 1) {
                    edQtyDialog.cancel();
                    dtotalamount = TotalAmount(dquantity, dsalerate, dfreightcharge, ddiscount, dpremium);
                    mInputQuantity = dquantity;
                    Constants.selectedSaudaDetailsList.get(position).setQuantity(qty);
                    Constants.selectedSaudaDetailsList.get(position).setAmount(String.valueOf(dtotalamount));
                    Constants.selectedSaudaDetailsList.get(position).setTD(discount);
                } else {
                    Toast.makeText(BargainConfirmationActivity.this, "Please provide a valid input", Toast.LENGTH_LONG).show();
                }
                GrandTotalAmount();
            } else {
                if (boolQty && boolDisc && boolFreight == 0) {
                    edQtyDialog.cancel();
                    dtotalamount = TotalAmount(dquantity, dsalerate, dfreightcharge, ddiscount, dpremium);
                    mInputQuantity = dquantity;
                    Constants.selectedSaudaDetailsList.get(position).setQuantity(qty);
                    Constants.selectedSaudaDetailsList.get(position).setAmount(String.valueOf(dtotalamount));
                    Constants.selectedSaudaDetailsList.get(position).setPremium(String.valueOf(dpremium));

                } else if (boolQty && boolDisc && boolFreight == 1) {
                    edQtyDialog.cancel();
                    dtotalamount = TotalAmount(dquantity, dsalerate, dfreightcharge, ddiscount, dpremium);
                    mInputQuantity = dquantity;
                    Constants.selectedSaudaDetailsList.get(position).setQuantity(qty);
                    Constants.selectedSaudaDetailsList.get(position).setAmount(String.valueOf(dtotalamount));
                    Constants.selectedSaudaDetailsList.get(position).setPremium(String.valueOf(dpremium));
                    Constants.selectedSaudaDetailsList.get(position).setFreightCharge(freightcharge);
                } else {
                    Toast.makeText(BargainConfirmationActivity.this, "Please provide a valid input", Toast.LENGTH_LONG).show();
                }
                GrandTotalAmount();
            }
            REFRESHSaudaConfirmationActivity();
            bargainConfirmAdapter.notifyDataSetChanged();
        });
        edQtyDialog.show();
    }

    @SuppressLint("SimpleDateFormat")
    public void SaveSaudaDataToDatabase() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                String timeStamp = "";
                timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

                dataHelperObj.INSERTtoSaudaDetailsForBargain(timeStamp);
                dataHelperObj.INSERTtoSaudaHeader(mSaudaHeader, timeStamp, "FT");
                dataHelperObj.insertToLocationTable("FT", timeStamp);
                dataHelperObj.UPDATESaudaAllocationDB(Constants.selectedAlocatedSaudaList);

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public boolean GenerateSaudaHeaderData() {
        boolean issuccess = false;
        try {
            String brokerid = "";
            String customercode = Constants.selectedCustomer.getCustomerCode();
            String transfered = "";
            String tradediscount = "";
            String saudavalue = GrandTotalAmount();
            if (Constants.saudaFormDetailsObj.getSaudaBookedThrough().equalsIgnoreCase("both")) {
                if (mSaudaBooked.equalsIgnoreCase("Broker")) {
                    brokerid = Constants.selectedBroker.getBrokerId();
                }
            }
            String transactiontype = "OB";
            String vat = "";
            String branchcode = Constants.selectedBranch.getBranchCode();
            issuccess = SaudaHeaderData(customercode, transfered, mRemarks, tradediscount, saudavalue, brokerid, transactiontype, vat, branchcode, mValidity);

        } catch (Exception ignored) {
        }
        return issuccess;
    }

    public boolean SaudaHeaderData(String customercode, String transfered, String remarks, String tradediscount, String saudavalue, String brokerid,
                                   String transactiontype, String vat, String branchcode, String saudavalidity) {
        boolean issuccess = false;
        try {
            mSaudaHeader = new SaudaHeader();
            mSaudaHeader.setCustomerCode(customercode);
            mSaudaHeader.setTransfered(transfered);
            mSaudaHeader.setRemarks(remarks);
            mSaudaHeader.setTD(tradediscount);
            mSaudaHeader.setSaudaValue(saudavalue);
            mSaudaHeader.setBrokerId(brokerid);
            mSaudaHeader.setTransactionType(transactiontype);
            mSaudaHeader.setVAT(vat);
            mSaudaHeader.setBranchCode(branchcode);
            mSaudaHeader.setSaudaValidity(saudavalidity);
            issuccess = true;
        } catch (Exception ignored) {
        }
        return issuccess;
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_MENU || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    public Date getPreviousDate() {
        Calendar cal = Calendar.getInstance();
        cal.setTime(new Date());
        cal.add(Calendar.DATE, -1);
        return cal.getTime();
    }

    public void GetDeletedITEM(String grpcode, String quantity) {
        POSITION = GETTSaudaAllocationPostion(grpcode);
        double relesaebalance = CalculateUOM2(Double.parseDouble(quantity), Double.parseDouble(mConversionFactor));
        String balance = Constants.selectedAlocatedSaudaList.get(POSITION).getBalance();
        double previousbalance = Double.parseDouble(balance);
        UPDATESaudaAllocation(POSITION, previousbalance, relesaebalance);
    }

    public int GETTSaudaAllocationPostion(String grpcode) {
        int position = 0;
        for (int count = 0; count < Constants.selectedAlocatedSaudaList.size(); count++) {
            if (Constants.selectedAlocatedSaudaList.get(count).getProductFilterCode().equalsIgnoreCase(grpcode)) {
                position = count;
            }
        }
        return position;
    }

    public void UPDATESaudaAllocation(int position, double previousbalance, double relasebalance) {
        double balance = previousbalance + relasebalance;
        Constants.selectedAlocatedSaudaList.get(position).setBalance(String.valueOf(balance));
    }

    public void UPDATESaudaAllocation(int position, String balance) {
        Constants.selectedAlocatedSaudaList.get(position).setBalance(balance);
    }

    public double GETBalance(double totalallocation, double allocation) {
        return (totalallocation - allocation);
    }

    public double CalculateUOM2(double cases, double conversionfactor) {
        double UOM2 = (cases * conversionfactor);
        return Math.round(UOM2 * 100.0) / 100.0;
    }

    public double CalculateUOM1(double ltr, double conversionfactor) {
        double UOM1 = (ltr / conversionfactor);
        return Math.round(UOM1 * 100.0) / 100.0;
    }

    public double TotalAmount(double quantity, double ignoredSalerate, double ignoredFreightcharge, double tradediscount, double premium) {
        double amount = quantity * (basicRate + primaryFreight + secondaryFreight + depotCost + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) + premium + additionalPremium - additionalTD - tradediscount - Double.parseDouble(inputLiquidationDiscountForCurrentProduct));
        return Math.round(amount * 100.0) / 100.0;
    }

    @SuppressLint("SimpleDateFormat")
    public void GetCurerentDate() {
        SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyyMMdd");
        mCurrentDate = simpleDateFormat.format(new Date());
        simpleDateFormat = new SimpleDateFormat("yyyy-MM-dd");
        mTodayDate = simpleDateFormat.format(new Date());
    }

    public String GrandTotalAmount() {
        String totalamount = "";
        double dtotalamount = 0.0;
        try {
            for (int i = 0; i < Constants.selectedSaudaDetailsList.size(); i++) {
                dtotalamount += Double.parseDouble(Constants.selectedSaudaDetailsList.get(i).getAmount());
            }
            totalamount = defaultFormat.format(dtotalamount);
        } catch (Exception ignored) {
        }
        mTextViewTotal.setText(totalamount);
        return totalamount;
    }

    public void REFRESHSaudaConfirmationActivity() {
        double casebalance = GETBalance(mMaxAllocation, mInputQuantity);
        double balance = CalculateUOM2(casebalance, Double.parseDouble(mConversionFactor));
        UPDATESaudaAllocation(POSITION, String.valueOf(balance));
        Toast.makeText(BargainConfirmationActivity.this, "Order edited successfully", Toast.LENGTH_LONG).show();
    }

    public void RELEASEProduct(String productcode) {
        for (int count = 0; count < Constants.SelectedSkuCode.size(); count++) {
            if (productcode.equalsIgnoreCase(Constants.SelectedSkuCode.get(count))) {
                Constants.SelectedSkuCode.remove(count);
                break;
            }
        }
    }
}
