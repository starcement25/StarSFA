package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapterStockAudit;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_PendingRoutePlanBeforeOtherTxn;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitStockAuditTask;
import com.forcepower.acedns.bean.CustomerDetails;
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
import java.util.Iterator;
import java.util.List;

import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;

public class InSHopPromoterFormActivity extends AceDnsParentActivity {

    public static ImageView mImageViewHeaderLogo = null;
    public static TextView mTextViewVisit1 = null;
    public static TextView mTextViewVisit2 = null;
    public static TextView mTextViewVisit3 = null;
    public static TextView mTextViewVisitUom = null;
    Button btnAddToCart, btnCheckOut, btnNoOrder, btnBack, btnCustomer;
    LinearLayout filterLayout, visitLayout, mLinearLayoutSaleRate;

    EditText edQty;
    EditText mEditTextMrp;

    Dialog grpDialog, subGrpDialog, brandDialog,
            masterDialog, noOrderDialog, infoDialog;

    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;

    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;
    ArrayList<String> filterList;
    ArrayList<CustomerDetails> mCustomerDetails;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;

    ArrayList<Button> filterButtonList;
    ProductMasterDetails currentProductMasterObj;
    int filterNo;
    boolean lastGrpSelected, lastSubGroupSelected, lastBrandSelected,
            lastProductSelected = false;
    //	ProductMasterAdapter prodAdapter;
    RoutePlanTransAdapter RoutePlanAdapter;
    String lastStr = "";
    ArrayList<ProductMasterDetails> tempProductList;
    int lastProdPos = 0;
    List<String> hintRemarksValList;
    int selectedHintRemarksId = -1;
    ProductMasterWithQtyInputAdapterStockAudit ProductMasterWithQtyInputAdapterObject;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_in_shop_promoter_frm);
        RegisterActivities.registerActivity(this);

        Constants.isFromConfirmationActivity = false;
        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        mContext = InSHopPromoterFormActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Constants.selectedProductMasterList = new ArrayList<>();
        Constants.selectedProductMasterListStockAudit = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();

        filterButtonList = new ArrayList<Button>();

        InitializeView();
        drawFilterLayout();

        getCustomerListAndShow();

    }

    private void getCustomerListAndShow() {
        mCustomerDetails = mAceDnsDatabase.getEntireCustomerList();

        if (mCustomerDetails.size() > 0) {
            if (mCustomerDetails.size() > 1) {
                ShowChooseCustomerDialog();
            } else {
                Constants.selectedCustomer = mCustomerDetails.get(0);
                showMinimumStockIfAvailable();
                btnCustomer.setText(Constants.selectedCustomer.getCustomerName());
                btnCustomer.setEnabled(false);
                btnNoOrder.setEnabled(true);
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                btnAddToCart.setEnabled(true);
                btnCheckOut.setEnabled(true);
            }
        } else {
            Toast.makeText(mContext, "No customer found", Toast.LENGTH_LONG).show();
        }
    }

    @Override
    public void onResume() {
        super.onResume();

        if (Constants.isFromConfirmationActivity) {
            Constants.isFromConfirmationActivity = false;
            visitLayout.setVisibility(View.INVISIBLE);
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

    public void InitializeView() {
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));

        edQty = (EditText) findViewById(R.id.editTextQuantity);
        mEditTextMrp = (EditText) findViewById(R.id.editTextSaleRate);

        filterLayout = (LinearLayout) findViewById(R.id.filter_layout);
        mLinearLayoutSaleRate = (LinearLayout) findViewById(R.id.layoutsalerate);

        visitLayout = (LinearLayout) findViewById(R.id.visit_layout);
        mTextViewVisit1 = (TextView) findViewById(R.id.txt_visit1);
        mTextViewVisit2 = (TextView) findViewById(R.id.txt_visit2);
        mTextViewVisit3 = (TextView) findViewById(R.id.txt_visit3);

        mTextViewVisitUom = (TextView) findViewById(R.id.textViewUOM);

        btnAddToCart = (Button) findViewById(R.id.btn_continue);
        btnAddToCart.setEnabled(false);
        btnAddToCart.setOnClickListener(InSHopPromoterFormActivity.this);
        btnAddToCart.setTag(101);
        btnCheckOut = (Button) findViewById(R.id.btn_order_form);
        btnCheckOut.setTag(102);
        btnCheckOut.setEnabled(false);
        btnCheckOut.setOnClickListener(InSHopPromoterFormActivity.this);
        btnNoOrder = (Button) findViewById(R.id.no_ordr);
        btnNoOrder.setTag(104);
        btnNoOrder.setOnClickListener(InSHopPromoterFormActivity.this);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setTag(105);
        btnBack.setOnClickListener(InSHopPromoterFormActivity.this);

        btnCustomer = (Button) findViewById(R.id.select_customer);
        btnCustomer.setTag(107);
        btnCustomer.setOnClickListener(InSHopPromoterFormActivity.this);
    }

    public void drawFilterLayout() {
        filterList = mAceDnsDatabase.getFilterList(); // filterList =
        // [4(filter_no),dadu,baba,NA,chhele]
        for (int ii = 1; ii < filterList.size(); ii++) {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LayoutParams buttonLayoutParams = new LayoutParams(
                        LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LayoutParams buttonParams = new LayoutParams(
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
                    getVerticalValue();
                    showGrpDialog();
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
                        showSubGrpDialog(selectedGrp.getGroupCode());
                    } else {
                        Toast.makeText(InSHopPromoterFormActivity.this,
                                "Please select the parent category", 2000);
                    }
                    break;
                case 3:
                    lastProdPos = 0;
                    filterButtonList.get(3).setEnabled(true);
                    lastProductSelected = false;
                    if (selectedSubGrp != null) {
                        showBrandDialog(selectedSubGrp.getSubGrpCode());
                    } else {
                        Toast.makeText(InSHopPromoterFormActivity.this,
                                "Please select the parent category", 2000);
                    }
                    break;
                case 4:
                    switch (filterNo) {
                        case 1:
                            showMasterDialog("");
                            break;
                        case 2:
                            if (selectedGrp != null) {
                                showMasterDialog(selectedGrp.getGroupCode());
                            } else {
                                Toast.makeText(InSHopPromoterFormActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                showMasterDialog(selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(InSHopPromoterFormActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                showMasterDialog(selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(InSHopPromoterFormActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                    }
            }
        }
        if (clkdView == btnAddToCart) {
            // Continue Button
            if (currentProductMasterObj != null) {
                boolean qty_status = true;
                boolean mrp_status = true;
                if (edQty.getText().toString() != null
                        && edQty.getText().toString().length() > 0
                        && !edQty.getText().toString().equalsIgnoreCase("0")
                        && !edQty.getText().toString().equalsIgnoreCase(".")) {
                    currentProductMasterObj.setQty(edQty.getText().toString());
                    qty_status = true;
                } else {
                    qty_status = false;
                }

                if (Constants.userDetailsObj.getStockAuditRate().equalsIgnoreCase("yes")) {
                    if (mEditTextMrp.getText().toString() != null
                            && mEditTextMrp.getText().toString().length() > 0
                            && !mEditTextMrp.getText().toString().equalsIgnoreCase("0")
                            && !mEditTextMrp.getText().toString().equalsIgnoreCase(".")) {
                        currentProductMasterObj.setMrpCode(mEditTextMrp.getText().toString());
                        mrp_status = true;
                    } else {
                        mrp_status = false;
                    }
                }

                if (qty_status && mrp_status) {

                    if (Constants.userDetailsObj.getStockAuditRate().equalsIgnoreCase("yes")) {
                        mAceDnsDatabase.UpdateMRPDetails(currentProductMasterObj.getProdCode(), currentProductMasterObj.getMrpCode().trim());
                    }
                    edQty.setText("");
                    mEditTextMrp.setText("");

                    visitLayout.setVisibility(View.INVISIBLE);
                    Constants.selectedProductMasterList
                            .add(currentProductMasterObj);
                    Toast.makeText(InSHopPromoterFormActivity.this,
                            "Product has been added to cart.", Toast.LENGTH_LONG).show();
                    currentProductMasterObj = null;
                    switch (filterNo) {
                        case 1:
                            if (lastProductSelected) {
                                // No option but to submit
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
                    Toast.makeText(InSHopPromoterFormActivity.this,
                            "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(InSHopPromoterFormActivity.this,
                        "Please select a product", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == btnCheckOut) {
            if (Constants.selectedProductMasterListStockAudit != null && Constants.selectedProductMasterListStockAudit.size() > 0) {
                startActivity(new Intent(InSHopPromoterFormActivity.this,
                        InSHopPromoterConfirmActivity.class));
            } else {
                Toast.makeText(InSHopPromoterFormActivity.this,
                        "Please select a product", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == btnNoOrder) {
            btnNoOrder.setEnabled(false);
            if (Constants.orderFormDetailsObj.getHintsRemarks().equalsIgnoreCase("yes")) {
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
                ShowNoStockAuditDialog();
            }
        }
        if (clkdView == btnBack) {
            finish();
        }
        if (clkdView == btnCustomer) {
            getCustomerListAndShow();
        }
    }

    private void getVerticalValue() {
        try {
            String[] values;
            int max = 0;
            max = mAceDnsDatabase.GetVerticalValue();
            values = new String[max];
            for (int i = 0; i < Constants.mVerticalValueList.length; i++) {
                values[i] = Constants.mVerticalValueList[i];
            }

            if (values.length > 0) {
                Constants.mVerticalValue = values[0];
            }
        } catch (Exception e) {

        }

    }

    public void showInstructionWithHintDialog() {
        noOrderDialog = new Dialog(InSHopPromoterFormActivity.this,
                R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.hint_remarks_dialog);
        noOrderDialog.setCancelable(false);
        TextView title = (TextView) noOrderDialog.findViewById(R.id.title);
        TextView title2 = (TextView) noOrderDialog.findViewById(R.id.title2);
        title.setText("Please state the reason for no Stock Audit");
        title2.setText("Any specific Requirement?");
        final EditText edReason = (EditText) noOrderDialog
                .findViewById(R.id.ed_input);
        Button submit = (Button) noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                NoStockAuditLocalAndServerSavingProcess(edReason);

            }
        });
        RadioGroup rgp = (RadioGroup) noOrderDialog.findViewById(R.id.radiogroup);
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
        noOrderDialog.show();
    }

    /*
     * :::::::::::::::::::::::::::::: CREATING DIFFERENT DIALOGs
     * :::::::::::::::::::::::::::::::::
     */
    public void ShowChooseCustomerDialog() {
        final Dialog customerListDialog = new Dialog(InSHopPromoterFormActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);

        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(InSHopPromoterFormActivity.this, R.layout.customer_list_child, mCustomerDetails);
        TextView title = (TextView) customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = (EditText) customerListDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });

        ListView dialogList = (ListView) customerListDialog
                .findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {

                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                customerListDialog.cancel();
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                showMinimumStockIfAvailable();
                btnCustomer.setText(Constants.selectedCustomer.getCustomerName());
                btnCustomer.setEnabled(false);
                btnNoOrder.setEnabled(true);
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                btnAddToCart.setEnabled(true);
                btnCheckOut.setEnabled(true);
//				if(Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes")){
//					ShowSaudaDepoNameDialog();
//				}
            }
        });

        Button addCustomer = (Button) customerListDialog
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);

        customerListDialog.show();
    }

    public void ShowNoStockAuditDialog() {
        noOrderDialog = new Dialog(InSHopPromoterFormActivity.this,
                R.style.PauseDialog);
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
                NoStockAuditLocalAndServerSavingProcess(edReason);


            }
        });
        noOrderDialog.show();
    }

    private void NoStockAuditLocalAndServerSavingProcess(EditText edReason) {
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
            Boolean isSuccessInsertToLocationTable, isSuccessHintRemarksDetailsTable = true, isSuccessinsertNoStockAuditTable;
            mAceDnsTransactionDatabase.beginTransaction();
            if (selectedHintRemarksId != -1) {
                isSuccessHintRemarksDetailsTable = mAceDnsTransactionDatabase.insertToHintRemarksDetailsTable1("NS", timeStamp, hintRemarksValList.get(selectedHintRemarksId - 1));
            }
            isSuccessinsertNoStockAuditTable = mAceDnsTransactionDatabase.insertNoStockAuditTable1(timeStamp, reason);
            isSuccessInsertToLocationTable = mAceDnsTransactionDatabase.insertToLocationTable1("NS", timeStamp);

            if (isSuccessHintRemarksDetailsTable && isSuccessinsertNoStockAuditTable && isSuccessInsertToLocationTable) {
                mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
                if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && Constants.userDetailsObj.getstk_audit_irrespective_routeplan().equalsIgnoreCase("no")) {
                    boolean isExist = mAceDnsTransactionDatabase.IsUnuploadedRoutePlanExist();
                    if (true == isExist) {
                        new TRANS_PendingRoutePlanBeforeOtherTxn(InSHopPromoterFormActivity.this, "STOCK").execute();
                    } else {
                        new TRANS_SubmitStockAuditTask(InSHopPromoterFormActivity.this, true, "SUBMIT").execute();
                    }
                } else {
                    new TRANS_SubmitStockAuditTask(InSHopPromoterFormActivity.this, true, "SUBMIT").execute();
                }
            } else {
                btnNoOrder.setEnabled(true);
                mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
                Toast.makeText(mContext, "Sorry! memory related fatal exception found. Need to reenter data", Toast.LENGTH_LONG).show();
            }


        } else {
            Utils.showSettingsAlertToChangeTimeZone(mContext);
        }
    }

    public void showGrpDialog() {
        productGroupList = mAceDnsDatabase.getProductGroupListStockAudit(false);
        removeRepeatedGroupItems();
        if (productGroupList.size() > 0) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            grpDialog = new Dialog(InSHopPromoterFormActivity.this,
                    R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_from_list);
            grpDialog.setTitle("Please select an option");
            grpDialog.setCancelable(true);
            ImageView image_cancel = (ImageView) grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View view) {
                    grpDialog.cancel();
                }
            });
            TextView title = (TextView) grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) grpDialog.findViewById(R.id.list);
            ProductGrpAdapter adapter1 = new ProductGrpAdapter(
                    InSHopPromoterFormActivity.this, R.layout.product_list_child,
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
                            showMasterDialog(selectedGrp.getGroupCode());
                            break;
                        case 3:
                            showSubGrpDialog(selectedGrp.getGroupCode());
                            break;
                        case 4:
                            showSubGrpDialog(selectedGrp.getGroupCode());
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
            Toast.makeText(InSHopPromoterFormActivity.this,
                    "There are no items left.Please submit transaction.", 2000)
                    .show();
        }
    }

    public void showSubGrpDialog(String parentItem) {
        productSubGroupList = mAceDnsDatabase.getProductSubGroupListStockAudit(parentItem, false);
        removeRepeatedSubGroupItems();
        if (productSubGroupList.size() > 0) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGrpDialog = new Dialog(InSHopPromoterFormActivity.this,
                    R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_from_list);
            subGrpDialog.setCancelable(true);
            ImageView image_cancel = (ImageView) grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View view) {
                    grpDialog.cancel();
                }
            });
            TextView title = (TextView) subGrpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) subGrpDialog
                    .findViewById(R.id.list);
            ProductSubGrpAdapter adapter1 = new ProductSubGrpAdapter(
                    InSHopPromoterFormActivity.this, R.layout.product_list_child,
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
                            showMasterDialog(selectedSubGrp.getSubGrpCode());
                            break;
                        case 4:
                            showBrandDialog(selectedSubGrp.getSubGrpCode());
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
                    InSHopPromoterFormActivity.this,
                    "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    public void showBrandDialog(String parentItemId) {
        productBrandList = mAceDnsDatabase.getProductBrandListStockAudit(parentItemId,
                false);
        removeRepeatedBrandItems();
        if (productBrandList.size() > 0) {
            if (productBrandList.size() == 1) {
                lastBrandSelected = true;
            }
            brandDialog = new Dialog(InSHopPromoterFormActivity.this,
                    R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_from_list);
            brandDialog.setCancelable(true);
            ImageView image_cancel = (ImageView) grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View view) {
                    grpDialog.cancel();
                }
            });
            TextView title = (TextView) brandDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) brandDialog
                    .findViewById(R.id.list);
            ProductBrandAdapter adapter1 = new ProductBrandAdapter(
                    InSHopPromoterFormActivity.this, R.layout.product_list_child,
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
                    showMasterDialog(selectedBrand.getBrandCode());
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
                    InSHopPromoterFormActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    public void showMasterDialog(String parentItem) {
        productMasterList = mAceDnsDatabase.getProductMasterListStockAudit(parentItem, filterNo, false);
        removeRepeatedProductItems();
        if (productMasterList.size() > 0) {
            if (productMasterList.size() == 1) {
                lastProductSelected = true;
                if (lastGrpSelected) {
                    btnAddToCart.setEnabled(false);
                }
            }
            tempProductList = new ArrayList<ProductMasterDetails>();
            reInitialiseProductList();
            ProductMasterWithQtyInputAdapterObject = new ProductMasterWithQtyInputAdapterStockAudit(InSHopPromoterFormActivity.this, R.layout.product_list_item_with_quantity_input_stock_audit, tempProductList);
//			prodAdapter = new ProductMasterAdapter(StockAuditFormActivity.this,R.layout.product_list_child, tempProductList);

            masterDialog = new Dialog(InSHopPromoterFormActivity.this,
                    R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please provide quantity for products");

            View list_header_item_planwise_input_screen = masterDialog.findViewById(R.id.list_header_item_planwise_input_screen);
            list_header_item_planwise_input_screen.setVisibility(View.VISIBLE);
            TextView list_details = (TextView) masterDialog.findViewById(R.id.list_details);
            ImageView image_cancel = (ImageView) masterDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View view) {
                    masterDialog.cancel();
                }
            });
            LayoutParams params = (LayoutParams) list_details.getLayoutParams();
            params.weight = 4f;
            list_details.setLayoutParams(params);
            TextView etProdQty = (TextView) masterDialog.findViewById(R.id.etProdQty);
            LayoutParams params2 = (LayoutParams) etProdQty.getLayoutParams();
            params2.weight = 1f;
            etProdQty.setLayoutParams(params2);

            etProdQty.setVisibility(View.VISIBLE);
            TextView tv_last_month_purchase = (TextView) masterDialog.findViewById(R.id.tv_last_month_purchase);
            TextView tv_order_plan = (TextView) masterDialog.findViewById(R.id.tv_order_plan);
            tv_last_month_purchase.setVisibility(View.GONE);
            tv_order_plan.setVisibility(View.GONE);
            TextView etProdRate = (TextView) masterDialog.findViewById(R.id.etProdRate);
            etProdRate.setVisibility(View.GONE);

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
                    ProductMasterWithQtyInputAdapterObject.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            prodQtyRateListView = (ListView) masterDialog
                    .findViewById(R.id.list);
            prodQtyRateListView.setAdapter(ProductMasterWithQtyInputAdapterObject);
            if (lastProdPos != 0) {
                prodQtyRateListView.setSelection(lastProdPos - 1);
            } else {
                prodQtyRateListView.setSelection(0);
            }
            prodQtyRateListView.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    masterDialog.cancel();
                    lastProdPos = arg2;
                    currentProductMasterObj = tempProductList.get(arg2);
                    filterButtonList.get(3).setText(currentProductMasterObj.getDesc());
                    mTextViewVisitUom.setText(currentProductMasterObj.getUom1());

                    if (Constants.userDetailsObj.getPreviousStock()
                            .equalsIgnoreCase("yes")) {
                        ShowLastVisitDetails(currentProductMasterObj);
                    }
                }
            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setVisibility(View.GONE);

            Button btn_addToCart = (Button) masterDialog.findViewById(R.id.btn_addToCart);
            btn_addToCart.setVisibility(View.VISIBLE);
            btn_addToCart.setText("Add To Cart");
            btn_addToCart.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {

                    Boolean isValidQtyProvidedForAtLeastOneProduct = false;
                    for (int i = 0; i < Constants.selectedProductMasterList.size(); i++) {
                        String currentqty = Constants.selectedProductMasterList.get(i).getQty();
                        if (Utils.isNumeric(currentqty)) {
                            isValidQtyProvidedForAtLeastOneProduct = true;
                        }
                    }
                    if (isValidQtyProvidedForAtLeastOneProduct) {
                        removeItemsWithEmptyQty();
                        if (Constants.selectedProductMasterListStockAudit == null) {
                            Constants.selectedProductMasterListStockAudit = new ArrayList<>();
                        }
                        for (int i = 0; i < Constants.selectedProductMasterList.size(); i++) {
                            Constants.selectedProductMasterListStockAudit.add(Constants.selectedProductMasterList.get(i));
                        }
                        masterDialog.cancel();
                        startActivity(new Intent(InSHopPromoterFormActivity.this, InSHopPromoterConfirmActivity.class));
                    } else {
                        Toast.makeText(mContext, "Please provide quantity for at least one product.", Toast.LENGTH_SHORT).show();
                    }
                }
            });
            masterDialog.show();
        } else {
            Toast.makeText(
                    InSHopPromoterFormActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            filterButtonList.get(3).setText("");
        }
    }

    private void showMinimumStockIfAvailable() {
        String minimumStock = Constants.selectedCustomer.getMinimumStock();
        if (Constants.userDetailsObj.getMinimumStock().equalsIgnoreCase("yes") && Utils.isNumeric(minimumStock)) {
            LinearLayout min_stock_layout = (LinearLayout) findViewById(R.id.min_stock_layout);
            min_stock_layout.setVisibility(View.VISIBLE);
            TextView minimumStockTextView = (TextView) findViewById(R.id.minimumStockTextView);
            Constants.CurrentMinimumStockForCustomer = minimumStock;
            minimumStockTextView.setText(minimumStock + " " + Constants.userDetailsObj.getStockAuditUnit());
        }

    }

    /*
     * ::::::::::::::::::::::::::::::: REMOVING REPEATED ITEMS FROM SELECTION
     * LIST :::::::::::::::::::::::::::::::
     */

    public void removeRepeatedProductItems() {
        if (Constants.selectedProductMasterListStockAudit != null) {
            for (int kk = 0; kk < Constants.selectedProductMasterListStockAudit.size(); kk++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListStockAudit
                        .get(kk);
                for (int x = 0; x < productMasterList.size(); x++) {
                    if (productMasterList.get(x).getProdCode()
                            .equalsIgnoreCase(currentItem.getProdCode())) {
                        productMasterList.remove(productMasterList.get(x));
                    }
                }
            }
        }
    }

    public void removeRepeatedBrandItems() {
        if (Constants.selectedProductMasterListStockAudit != null) {
            for (int kk = 0; kk < Constants.selectedProductMasterListStockAudit.size(); kk++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListStockAudit.get(kk);
                for (int x = 0; x < productBrandList.size(); x++) {
                    if (productBrandList.get(x).getBrandCode().equalsIgnoreCase(currentItem.getBrndCode())) {
                        productBrandList.remove(productBrandList.get(x));
                    }
                }
            }
        }
    }

    public void removeRepeatedGroupItems() {
        if (Constants.selectedProductMasterListStockAudit != null) {
            for (int kk = 0; kk < Constants.selectedProductMasterListStockAudit.size(); kk++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListStockAudit.get(kk);
                for (int x = 0; x < productGroupList.size(); x++) {
                    if (productGroupList.get(x).getGroupCode().equalsIgnoreCase(currentItem.getGrpCode())) {
                        productGroupList.remove(productGroupList.get(x));
                    }
                }
            }
        }
    }

    public void removeRepeatedSubGroupItems() {

        if (Constants.selectedProductMasterListStockAudit != null) {
            for (int kk = 0; kk < Constants.selectedProductMasterListStockAudit.size(); kk++) {
                ProductMasterDetails currentItem = Constants.selectedProductMasterListStockAudit.get(kk);
                for (int x = 0; x < productSubGroupList.size(); x++) {
                    if (productSubGroupList.get(x).getSubGrpCode().equalsIgnoreCase(currentItem.getSubGrpCode())) {
                        productSubGroupList.remove(productSubGroupList.get(x));
                    }
                }
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

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            tempProductList.add(productMasterList.get(kk));
        }
    }

    public void ShowLastVisitDetails(ProductMasterDetails prodObj) {
        String lastVisitData = mAceDnsDatabase.getLastVisitData(prodObj);
        if (lastVisitData != null && lastVisitData.length() > 0) {
            String[] dataArray = lastVisitData.split(",");
            if (dataArray.length > 2) {
                visitLayout.setVisibility(View.VISIBLE);
                mTextViewVisit1.setText("Visit 1 \n" + dataArray[0]);
                mTextViewVisit2.setText("Visit 2 \n" + dataArray[1]);
                mTextViewVisit3.setText("Visit 3 \n" + dataArray[2]);
            } else {
                Utils.showToast(InSHopPromoterFormActivity.this,
                        "Last visit details are not available");
            }
        } else {
            Utils.showToast(InSHopPromoterFormActivity.this,
                    "Last visit details are not available");
        }

    }

    private void removeItemsWithEmptyQty() {
        if (Constants.selectedProductMasterList != null) {
            for (Iterator<ProductMasterDetails> iterator = Constants.selectedProductMasterList.iterator(); iterator.hasNext(); ) {
                ProductMasterDetails ProductMasterDetailsObject = iterator.next();
                String cyrrentQty = ProductMasterDetailsObject.getQty();
                if (!Utils.isNumeric(cyrrentQty)) {
                    // Remove the current element from the iterator and the list.
                    iterator.remove();
                }
            }
        }
    }

}
