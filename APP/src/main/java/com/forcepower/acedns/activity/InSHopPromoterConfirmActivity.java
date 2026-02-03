package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Typeface;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.InputType;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.util.PreferenceData;
import com.getkeepsafe.taptargetview.TapTarget;
import com.getkeepsafe.taptargetview.TapTargetView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.StockAuditConfirmAdapter;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

public class InSHopPromoterConfirmActivity extends AceDnsParentActivity {

    ListView productListView;
    StockAuditConfirmAdapter adapter;
    Button btnAddProduct, btnSubmit, btnBack, btnSaleType, btnRemarks, btnDiscount, btnVAT;
    AceDnsTransactionDatabase dataHelperObj;
    Context mContext;
    String remarks = "";
    ImageView imhLogo;
    TextView txtTotal, heading;
    Handler mHandler;
    ProgressDialog loader;
    GPSTracker gpstracker;
    List<String> hintRemarksValList;
    int selectedHintRemarksId = -1;
    int localDataSavingfailedAttempt = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_confirm);
        RegisterActivities.registerActivity(this);
        hintRemarksValList = new ArrayList<>();
        Constants.isFromConfirmationActivity = true;
        mContext = InSHopPromoterConfirmActivity.this;
        dataHelperObj = new AceDnsTransactionDatabase(mContext);


        initView();
        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    InSHopPromoterConfirmActivity.this.runOnUiThread(new Runnable() {
                        public void run() {

                            Constants.OrderTransactionTaskCalledFrom = "isp";
                            new TRANS_SubmitOrderTask(mContext, true).execute();
                        }
                    });
                }
            }
        };
    }


    public void initView() {
        txtTotal = (TextView) findViewById(R.id.txt_total);
        heading = (TextView) findViewById(R.id.textViewheading);
        txtTotal.setVisibility(View.GONE);
        heading.setVisibility(View.GONE);
        imhLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imhLogo.setVisibility(View.VISIBLE);
            imhLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imhLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        //txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        productListView = (ListView) findViewById(R.id.list_prod);
        adapter = new StockAuditConfirmAdapter(InSHopPromoterConfirmActivity.this, R.layout.ordr_confrm_list_child, Constants.selectedProductMasterListStockAudit);
        productListView.setAdapter(adapter);
        productListView.setOnItemClickListener(InSHopPromoterConfirmActivity.this);
        productListView.setOnItemLongClickListener(InSHopPromoterConfirmActivity.this);
        btnAddProduct = (Button) findViewById(R.id.btn_add);
        btnSubmit = (Button) findViewById(R.id.btn_order);
        btnBack = (Button) findViewById(R.id.back);
        btnSaleType = (Button) findViewById(R.id.btn_saletype);
        btnSaleType.setVisibility(View.GONE);
        btnRemarks = (Button) findViewById(R.id.btn_remarks);
        btnDiscount = (Button) findViewById(R.id.btn_discount);
        btnDiscount.setVisibility(View.GONE);
        btnVAT = (Button) findViewById(R.id.btn_vat);
        btnVAT.setVisibility(View.GONE);
        btnRemarks.setVisibility(View.GONE);

        btnSubmit.setOnClickListener(InSHopPromoterConfirmActivity.this);
        btnAddProduct.setOnClickListener(InSHopPromoterConfirmActivity.this);
        btnBack.setOnClickListener(InSHopPromoterConfirmActivity.this);
        btnRemarks.setOnClickListener(InSHopPromoterConfirmActivity.this);

        showOneTimeHelp("remarksButton", "Add remarks using this button.");


    }

    private void showOneTimeHelp(final String type, String header) {
        try {
            if (!PreferenceData.getHelpStockAuditConfirm(mContext).matches("disable") && Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
                int targetView = R.id.btn_add;
                if (type.matches("remarksButton")) {
                    targetView = R.id.btn_remarks;
                } else if (type.matches("submitButton")) {
                    targetView = R.id.btn_order;
                }

                TapTargetView.showFor(this,                 // `this` is an Activity
                        TapTarget.forView(findViewById(targetView), header, "Press the button to continue.")
                                // All options below are optional
                                .outerCircleColor(R.color.colorOrangeAppCommon)      // Specify a color for the outer circle
                                .outerCircleAlpha(0.76f)            // Specify the alpha amount for the outer circle
                                .targetCircleColor(R.color.white)   // Specify a color for the target circle
                                .titleTextSize(20)                  // Specify the size (in sp) of the title text
                                .titleTextColor(R.color.colorOrangeAppCommon)      // Specify the color of the title text
                                .descriptionTextSize(15)            // Specify the size (in sp) of the description text
                                .descriptionTextColor(R.color.colorOrangeAppCommon)  // Specify the color of the description text
                                .textColor(R.color.white)            // Specify a color for both the title and description text
                                .textTypeface(Typeface.SANS_SERIF)  // Specify a typeface f
                                // or the text
//						.dimColor(R.color.black)            // If set, will dim behind the view with 30% opacity of the given color
                                .drawShadow(false)                   // Whether to draw a drop shadow or not
                                .cancelable(false)                  // Whether tapping outside the outer circle dismisses the view
                                .tintTarget(false)                   // Whether to tint the target view's color
                                .transparentTarget(true)           // Specify whether the target is transparent (displays the content underneath)
//						.icon(getResources().getDrawable( R.drawable.add_prod ))                     // Specify a custom drawable to draw as the target
                                .targetRadius(50),                  // Specify the target radius (in dp)
                        new TapTargetView.Listener() {          // The listener can listen for regular clicks, long clicks or cancels
                            @Override
                            public void onTargetClick(TapTargetView view) {
                                super.onTargetClick(view);      // This call is optional
                                if (type.matches("remarksButton")) {
                                    showOneTimeHelp("submitButton", "Submit transactions using this button.");
                                } else if (type.matches("submitButton")) {
                                    showOneTimeHelp("addButton", "Add more items using this button.");
                                }
                            }
                        });
            }
        } catch (Exception e) {

        }

    }

    @Override
    public void onClick(View arg0) {

        if (arg0 == btnSubmit) {
            gpstracker = new GPSTracker(mContext);
            btnSubmit.setEnabled(false);

            saveStockData();
        } else if (arg0 == btnBack) {
            finish();
        } else if (arg0 == btnAddProduct) {
            finish();
        } else if (arg0 == btnRemarks) {
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
                showInstructionDialog();
            }
        }
    }

    private Boolean isPhysicalStockLessThanMinimumStock() {
        Boolean PhysicalStockLessThanMinimumStock = false;
        if (Constants.userDetailsObj.getMinimumStock().equalsIgnoreCase("yes")) {
            Double physicalStock = 0.0;
            for (int ii = 0; ii < Constants.selectedProductMasterListStockAudit.size(); ii++) {
                ProductMasterDetails masterObj = Constants.selectedProductMasterListStockAudit
                        .get(ii);
                physicalStock = Double.valueOf(masterObj.getQty()) + physicalStock;
            }
            if (physicalStock < Double.valueOf(Constants.CurrentMinimumStockForCustomer)) {
                PhysicalStockLessThanMinimumStock = true;
            }
        }

        return PhysicalStockLessThanMinimumStock;
    }

    public void showInstructionWithHintDialog()
    {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.hint_remarks_dialog);
        instructionDialog.setCancelable(false);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        TextView title2 = (TextView) instructionDialog.findViewById(R.id.title2);
        title.setText("Remarks if any?");
        title2.setText("Any specific Requirement?");
        final EditText edInst = (EditText) instructionDialog
                .findViewById(R.id.ed_input);
        edInst.setText(remarks);
        Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                instructionDialog.cancel();
                String instruction = "";
                instruction = edInst.getText().toString();
                remarks = instruction;
            }
        });
        RadioGroup rgp = (RadioGroup) instructionDialog.findViewById(R.id.radiogroup);
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
        instructionDialog.show();
    }


    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
        Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                instructionDialog.cancel();
                String instruction = "";
                instruction = edInst.getText().toString();
                remarks = instruction;
            }
        });
        instructionDialog.show();
    }


    @Override
    public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
        showEditQuantityDialog(arg2);
    }

    @Override
    public boolean onItemLongClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
        showDeleteItemDialog(arg2);
        return false;
    }


    public void showDeleteItemDialog(final int pos) {
        System.out.println("POSITION::::::::::" + pos);
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(InSHopPromoterConfirmActivity.this);
        alertDialogBuilder.setMessage("Are you sure you want to delete this item ?").setCancelable(false);
        alertDialogBuilder.setNegativeButton("OK", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                dialog.cancel();
                ProductMasterDetails deletedProductObj = Constants.selectedProductMasterListStockAudit.get(pos);
                Constants.selectedProductMasterListStockAudit.remove(Constants.selectedProductMasterListStockAudit.get(pos));
                if (Constants.selectedProductMasterListStockAudit.size() == 0) {
                    finish();
                    // Clear all Lists.
                } else {
                    switch (Integer.parseInt(Constants.productDetailsObj.getNoFilter())) {
                        case 1:
                            // Only ProductList. Item has already been removed.
                        case 2:
                            // ProductList and GroupList.
                            if (Constants.selectedGroupList.size() > 0) {
                                for (int kk = 0; kk < Constants.selectedGroupList.size(); kk++) {
                                    ProductGroupDetails currentItem = Constants.selectedGroupList.get(kk);
                                    if (deletedProductObj.getGrpCode().equalsIgnoreCase(currentItem.getGroupCode())) {
                                        Constants.selectedGroupList.remove(Constants.selectedGroupList.get(kk));
                                    }
                                }
                            }
                            break;
                        case 3:
                            // ProductList and GroupList and SubGroup List.
                            if (Constants.selectedSubGroupList.size() > 0) {
                                for (int kk = 0; kk < Constants.selectedSubGroupList.size(); kk++) {
                                    ProductSubGrpDetails currentSubGrp = Constants.selectedSubGroupList.get(kk);
                                    if (deletedProductObj.getSubGrpCode().equalsIgnoreCase(currentSubGrp.getSubGrpCode())) {
                                        Constants.selectedSubGroupList.remove(Constants.selectedSubGroupList.get(kk));
                                        String grpCode = currentSubGrp.getGrpCode();
                                        for (int x = 0; x < Constants.selectedGroupList.size(); x++) {
                                            ProductGroupDetails currentItem = Constants.selectedGroupList.get(x);
                                            if (grpCode.equalsIgnoreCase(currentItem.getGroupCode())) {
                                                Constants.selectedGroupList.remove(Constants.selectedGroupList.get(kk));
                                            }
                                        }
                                    }
                                }
                            }
                            break;
                        case 4:
                            // ProductList and GroupList and SubGroup List and BrandList.
                            if (Constants.selectedBrandList.size() > 0) {
                                for (int kk = 0; kk < Constants.selectedBrandList.size(); kk++) {
                                    ProductBrandDetails currentBrand = Constants.selectedBrandList.get(kk);
                                    if (deletedProductObj.getBrndCode().equalsIgnoreCase(currentBrand.getBrandCode())) {
                                        Constants.selectedBrandList.remove(Constants.selectedBrandList.get(kk));
                                        String subGrp = currentBrand.getSubGrpCode();
                                        for (int xx = 0; xx < Constants.selectedSubGroupList.size(); xx++) {
                                            ProductSubGrpDetails currentSubGrp = Constants.selectedSubGroupList.get(xx);
                                            if (subGrp.equalsIgnoreCase(currentSubGrp.getSubGrpCode())) {
                                                Constants.selectedSubGroupList.remove(Constants.selectedSubGroupList.get(xx));
                                                String grp = currentSubGrp.getGrpCode();
                                                for (int yy = 0; yy < Constants.selectedSubGroupList.size(); yy++) {
                                                    ProductGroupDetails currentGrp = Constants.selectedGroupList.get(yy);
                                                    if (grp.equalsIgnoreCase(currentGrp.getGroupCode())) {
                                                        Constants.selectedGroupList.remove(Constants.selectedGroupList.get(yy));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            break;
                    }
                    adapter.notifyDataSetChanged();
                }
            }
        });
        alertDialogBuilder.setPositiveButton("CANCEL", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                dialog.cancel();
            }
        });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }


    public void showEditQuantityDialog(final int position) {

        final Dialog edQtyDialog = new Dialog(InSHopPromoterConfirmActivity.this);
        edQtyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        edQtyDialog.setContentView(R.layout.edit_order_dialog);
        TextView title = (TextView) edQtyDialog.findViewById(R.id.title);
        title.setText("Provide valid inputs");

        final LinearLayout mrpLayout = (LinearLayout) edQtyDialog.findViewById(R.id.mrp_layout);
        final LinearLayout amt_layout = (LinearLayout) edQtyDialog.findViewById(R.id.amt_layout);
        amt_layout.setVisibility(View.GONE);
        mrpLayout.setVisibility(View.GONE);
        final LinearLayout discountLayout = (LinearLayout) edQtyDialog.findViewById(R.id.disc_layout);
        discountLayout.setVisibility(View.GONE);

        final EditText edQty = (EditText) edQtyDialog.findViewById(R.id.ed_qnt);
        edQty.setText(Constants.selectedProductMasterListStockAudit.get(position).getQty());
        edQty.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);

        Button submit = (Button) edQtyDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String qty = "";
                boolean boolQty = true;

                qty = edQty.getText().toString();

                if (Utils.isNumeric(qty)) {
                    Constants.selectedProductMasterListStockAudit.get(position).setQty(qty);
                } else {
                    boolQty = false;
                }
                if (boolQty == true) {
                    edQtyDialog.cancel();
                    adapter.notifyDataSetChanged();
                    Toast.makeText(InSHopPromoterConfirmActivity.this, "Quantity has been edited for this product.", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(InSHopPromoterConfirmActivity.this, "Please provide a valid input", Toast.LENGTH_SHORT).show();
                }
            }
        });
        edQtyDialog.show();
    }

    public void saveStockData() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                String timeStamp = Constants.dateString + new SimpleDateFormat("_HHmmss").format(Calendar.getInstance().getTime());
                timeStamp = timeStamp.replace("_", "");
                Boolean isSuccessInsertToOrderDetailsTable, isSuccessInsertToLocationTable, isSuccessInsertToOrderHeaderTable;
                dataHelperObj.beginTransaction();
                isSuccessInsertToOrderDetailsTable = dataHelperObj.insertToOrderDetailsTableForISP(timeStamp);
                isSuccessInsertToOrderHeaderTable = dataHelperObj.insertToOrderHeaderTable1("O", "", timeStamp, "", "", "", "", "ISP", "", "", "", "");
                isSuccessInsertToLocationTable = dataHelperObj.insertToLocationTable1("O", timeStamp);

                gpstracker.stopUsingGPS();
                if (isSuccessInsertToOrderDetailsTable && isSuccessInsertToLocationTable && isSuccessInsertToOrderHeaderTable) {
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                    Message msgObj = mHandler.obtainMessage();
                    Bundle b = new Bundle();
                    b.putString("message", "SubmitJobDone");
                    msgObj.setData(b);
                    mHandler.sendMessage(msgObj);
                } else {
                    dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                    Activity activity = (Activity) mContext;
                    activity.runOnUiThread(new Runnable() {
                        public void run() {
                            loader.cancel();
                            btnSubmit.setEnabled(true);
                            if (localDataSavingfailedAttempt == 0) {
                                Toast.makeText(mContext, "Oops! Something went wrong while saving data. please try again.", Toast.LENGTH_LONG).show();
                                localDataSavingfailedAttempt++;

                            } else if (localDataSavingfailedAttempt == 1) {
                                Toast.makeText(mContext, "Issue likely a bit serious. Try once again.", Toast.LENGTH_LONG).show();
                                localDataSavingfailedAttempt++;
                            } else {
                                Toast.makeText(mContext, "Sorry! memory related fatal exception found. Need to reenter data", Toast.LENGTH_LONG).show();
                                Intent intent = new Intent(mContext,
                                        MenuActivity.class);
                                startActivity(intent);
                            }


                        }
                    });


                }

            }
        }.start();
    }


}
