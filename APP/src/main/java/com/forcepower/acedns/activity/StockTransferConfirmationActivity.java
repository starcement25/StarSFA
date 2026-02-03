package com.forcepower.acedns.activity;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.text.InputFilter;
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
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.OrderConfirmAdapter;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DecimalDigitsInputFilter;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.currency;

public class StockTransferConfirmationActivity extends FragmentActivity implements OnClickListener, OnItemClickListener, OnItemLongClickListener {

    ListView productListView;
    OrderConfirmAdapter adapter;
    Button btnAddProduct, btnSubmit, btnBack, btnSaleType, btnRemarks, btnDiscount, btnVAT;
    AceDnsTransactionDatabase dataHelperObj;
    Context mContext;
    String remarks = "";
    ImageView imhLogo;
    TextView txtTotal, heading;
    Handler mHandler;
    ProgressDialog loader;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    String transType = "";
    String totalAmount = "0";
    CaldroidListener listener;
    Button multipleRemarksButton;
    SimpleDateFormat dateFormat;
    //DecimalFormat df;
//	String billTotal = "0.00";
    String vat = "0.00";
    String remarksDate = "";
    GPSTracker gpstracker;
    Boolean isNavigatedFromSalesOption = false;
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_confirm);
        RegisterActivities.registerActivity(this);

        transType = getIntent().getStringExtra("TRANS_TYPE");
        if (getIntent().hasExtra("salesOption")) {
            isNavigatedFromSalesOption = true;
        }
        //df = new DecimalFormat("#.00");

        Constants.isFromConfirmationActivity = true;
        mContext = StockTransferConfirmationActivity.this;
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        initView();

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    StockTransferConfirmationActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            new TRANS_SubmitOrderTask(StockTransferConfirmationActivity.this, true).execute();
                        }
                    });
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
                } catch (Exception e) {

                }
            }

            @Override
            public void onChangeMonth(int month, int year) {

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
                } catch (Exception e) {

                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };

        calculateTotal();
    }

    public void initView() {
        txtTotal = (TextView) findViewById(R.id.txt_total);
        heading = (TextView) findViewById(R.id.textViewheading);
        heading.setVisibility(View.GONE);
        imhLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imhLogo.setVisibility(View.VISIBLE);
            imhLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imhLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        // txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        productListView = (ListView) findViewById(R.id.list_prod);
        adapter = new OrderConfirmAdapter(StockTransferConfirmationActivity.this, R.layout.ordr_confrm_list_child, Constants.selectedProductMasterList, txtTotal, "0", transType);
        productListView.setAdapter(adapter);
        productListView.setOnItemClickListener(StockTransferConfirmationActivity.this);
        productListView.setOnItemLongClickListener(StockTransferConfirmationActivity.this);
        btnAddProduct = (Button) findViewById(R.id.btn_add);
        btnSubmit = (Button) findViewById(R.id.btn_order);
        btnBack = (Button) findViewById(R.id.back);
        btnSaleType = (Button) findViewById(R.id.btn_saletype);
        btnSaleType.setVisibility(View.GONE);
        btnRemarks = (Button) findViewById(R.id.btn_remarks);
        if (isNavigatedFromSalesOption) {
            btnRemarks.setVisibility(View.GONE);
        }
        btnDiscount = (Button) findViewById(R.id.btn_discount);
        btnVAT = (Button) findViewById(R.id.btn_vat);

        btnAddProduct.setOnClickListener(StockTransferConfirmationActivity.this);
        btnSubmit.setOnClickListener(StockTransferConfirmationActivity.this);
        btnBack.setOnClickListener(StockTransferConfirmationActivity.this);
        btnSaleType.setOnClickListener(StockTransferConfirmationActivity.this);
        btnRemarks.setOnClickListener(StockTransferConfirmationActivity.this);
        btnDiscount.setOnClickListener(StockTransferConfirmationActivity.this);
        btnVAT.setOnClickListener(StockTransferConfirmationActivity.this);

        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") &&
                Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("order value wise")) {
            btnDiscount.setVisibility(View.VISIBLE);
        } else {
            btnDiscount.setVisibility(View.GONE);
        }
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes") &&
                Constants.orderFormDetailsObj.getVatCalcOn().equalsIgnoreCase("ordervalue")) {
            String[] vatAvailArray = Constants.orderFormDetailsObj.getVatType().split(",");
            if (Arrays.asList(vatAvailArray).contains(transType)) {
                btnVAT.setVisibility(View.VISIBLE);
            } else {
                btnVAT.setVisibility(View.GONE);
            }
        } else {
            btnVAT.setVisibility(View.GONE);
        }
    }

    @Override
    public void onClick(View arg0) {
        if (arg0 == btnAddProduct) {
            finish();
        } else if (arg0 == btnSubmit) {
            if (isNavigatedFromSalesOption) {
                showMultiRemarksDialog();
            } else {
                datasavingProcess();
            }

        } else if (arg0 == btnBack) {
            finish();
        } else if (arg0 == btnRemarks) {
            if (Constants.orderFormDetailsObj.getInstruction().equalsIgnoreCase("yes")) {
                showInstructionDialog();
            } else {
                showMultiRemarksDialog();
            }
        } else if (arg0 == btnVAT) {
            showVATDialog();
        }
    }

    private void datasavingProcess() {
        gpstracker = new GPSTracker(mContext);
        btnSubmit.setEnabled(false);
        saveStockData();
    }

    public void showVATDialog() {
        final Dialog vatDialog = new Dialog(mContext);
        vatDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        vatDialog.setContentView(R.layout.vat_dialog);
        TextView title = (TextView) vatDialog.findViewById(R.id.title);
        title.setText("Enter VAT amount");
        final EditText edInst = (EditText) vatDialog.findViewById(R.id.ed_input);
        edInst.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        Button submit = (Button) vatDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (edInst.getText().toString().length() > 0 && !edInst.getText().toString().equalsIgnoreCase(".")) {
                    vat = edInst.getText().toString();
                }
                introduceVATInProduct();
                adapter.notifyDataSetChanged();
                vatDialog.cancel();
            }
        });
        vatDialog.show();
    }

    public void introduceVATInProduct() {
        double vatVal = Double.parseDouble(vat);
        double orderValue = calculateTotal();
//		double vatFraction = vatVal/orderValue;
        for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
            ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
            if (Constants.selectedProductMasterList.size() == 1) {
                currentObj.setVat(String.valueOf(vatVal));
                break;
            } else {
                double qty = Double.parseDouble(currentObj.getQty());
                double rate = Double.parseDouble(currentObj.getMrpValue());
//   		 	double vatValueProduct = vatFraction*qty*rate;
                double vatValueProduct = (qty * rate) * (vatVal / orderValue);
                currentObj.setVat(String.valueOf(vatValueProduct));
            }
        }
        calculateTotal();
    }

    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        instructionDialog.setCancelable(false);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
        edInst.setText(remarks);
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

    public void showMultiRemarksDialog() {

        final Dialog multiRemarksDialog = new Dialog(mContext);
        multiRemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        multiRemarksDialog.setContentView(R.layout.multi_instruction_dialog);
        multiRemarksDialog.setCancelable(false);
        TextView title = (TextView) multiRemarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst1 = (EditText) multiRemarksDialog.findViewById(R.id.ed_input_r1);
        multipleRemarksButton = (Button) multiRemarksDialog.findViewById(R.id.ed_input_r2);
        final EditText edInst3 = (EditText) multiRemarksDialog.findViewById(R.id.ed_input_r3);
        edInst3.setFilters(new InputFilter[]{new DecimalDigitsInputFilter(16, 2)});
        String[] remarksVal = remarks.split(";");
        if (remarksVal != null & remarksVal.length > 0)
            edInst1.setText(remarksVal[0]);
        if (remarksVal != null & remarksVal.length > 1)
            multipleRemarksButton.setText(remarksVal[1]);
        if (remarksVal != null & remarksVal.length > 2)
            edInst3.setText(remarksVal[2]);
        Button submit = (Button) multiRemarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String instruction = "", billTotal;
                billTotal = edInst3.getText().toString();
                if (Utils.isNumeric(billTotal)) {
//					if(Double.parseDouble(billTotal)>=Double.parseDouble(totalAmount))
//					{
                    String chosenDate = multipleRemarksButton.getText().toString();
                    if (!chosenDate.matches("dd-MM-yyyy")) {
                        multiRemarksDialog.cancel();
                        instruction = edInst1.getText().toString() + ";" + chosenDate + ";" + billTotal;
                        remarks = instruction;
                        if (isNavigatedFromSalesOption) {
                            datasavingProcess();
                        }
                    } else {
                        Utils.showToast(mContext, "Please choose a date");
                    }

//					}
//					else
//					{
//						Utils.showToast(mContext, "Given amount must be equal or higher than total amount")	;
//					}
                } else {
                    Utils.showToast(mContext, "Please provide valid input in bill total");
                }
            }
        });
        multipleRemarksButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                chooseDateDialog();
            }
        });
        multiRemarksDialog.show();
    }

    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
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
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(StockTransferConfirmationActivity.this);
        alertDialogBuilder.setMessage("Are you sure you want to delete this item ?").setCancelable(false);
        alertDialogBuilder.setNegativeButton("OK", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                dialog.cancel();
                ProductMasterDetails deletedProductObj = Constants.selectedProductMasterList.get(pos);
                Constants.selectedProductMasterList.remove(Constants.selectedProductMasterList.get(pos));
                calculateTotal();
                //Just opposite of Normal Scenario
                if (transType.equalsIgnoreCase("PB") || transType.equalsIgnoreCase("CR")) {
                    dataHelperObj.reduceClStkProductWise(deletedProductObj);
                } else if (transType.equalsIgnoreCase("ST") || transType.equalsIgnoreCase("CN")
                        || transType.equalsIgnoreCase("SR") || transType.equalsIgnoreCase("SA") || transType.equalsIgnoreCase("SH")) {
                    dataHelperObj.increaseClStkProductWise(deletedProductObj);
                }

                if (!(Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes") &&
                        Constants.orderFormDetailsObj.getVatCalcOn().equalsIgnoreCase("ordervalue"))) {
                    introduceVATInProduct();
                }
                if (Constants.selectedProductMasterList.size() == 0) {
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
        DecimalFormat defaultFormat = new DecimalFormat("0.00");
        final Double previousQty = Double.parseDouble(Constants.selectedProductMasterList.get(position).getQty());

        final Dialog edQtyDialog = new Dialog(StockTransferConfirmationActivity.this);
        edQtyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        edQtyDialog.setContentView(R.layout.edit_order_dialog);
        TextView title = (TextView) edQtyDialog.findViewById(R.id.title);
        title.setText("Provide valid inputs");

        final LinearLayout mrpLayout = (LinearLayout) edQtyDialog.findViewById(R.id.mrp_layout);
        final LinearLayout discountLayout = (LinearLayout) edQtyDialog.findViewById(R.id.disc_layout);
        final LinearLayout amountLayout = (LinearLayout) edQtyDialog.findViewById(R.id.amt_layout);

        TextView txtTDVatTitle = (TextView) edQtyDialog.findViewById(R.id.txt_td_vat);
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            txtTDVatTitle.setText("VAT                     : ");
        }

        final EditText edQty = (EditText) edQtyDialog.findViewById(R.id.ed_qnt);
        final EditText edMrp = (EditText) edQtyDialog.findViewById(R.id.ed_sale);
        final EditText edDisc = (EditText) edQtyDialog.findViewById(R.id.ed_discount);
        final EditText edAmt = (EditText) edQtyDialog.findViewById(R.id.ed_amount);

        if (Constants.selectedProductMasterList.get(position).isAmtEntered()) {
            edMrp.setEnabled(false);
            edAmt.setEnabled(true);
        } else {
            edMrp.setEnabled(true);
            edAmt.setEnabled(false);
        }

        edQty.setText(Constants.selectedProductMasterList.get(position).getQty());
//		edMrp.setText(Constants.selectedProductMasterList.get(position).getMrpValue());
        edMrp.setText(defaultFormat.format(Double.parseDouble(Constants.selectedProductMasterList.get(position).getMrpValue())));
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            edDisc.setText(Constants.selectedProductMasterList.get(position).getVat());
        } else {
            edDisc.setText(Constants.selectedProductMasterList.get(position).getTradeDiscnt());
        }

        if (Constants.orderFormDetailsObj.getAmount().equalsIgnoreCase("yes")) {
            amountLayout.setVisibility(View.VISIBLE);
//			edAmt.setText(Constants.selectedProductMasterList.get(position).getAmount());
            edAmt.setText(defaultFormat.format(Double.parseDouble(Constants.selectedProductMasterList.get(position).getAmount())));
        } else {
            amountLayout.setVisibility(View.GONE);
        }

        edQty.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edMrp.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edDisc.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edAmt.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);

        if (!(Constants.orderFormDetailsObj.getSaleRate().equalsIgnoreCase("yes") &&
                Constants.orderFormDetailsObj.getSaleRateDrpdwn().equalsIgnoreCase("input"))) {
            mrpLayout.setVisibility(View.GONE);
        }
        String[] vatArray = Constants.orderFormDetailsObj.getVatType().split(",");
        if (!(Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getVatCalcOn().equalsIgnoreCase("sku") && (Arrays.asList(vatArray).contains("SB") || Arrays.asList(vatArray).contains("SO"))) &&
                !(Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase("sku wise"))) {
            discountLayout.setVisibility(View.GONE);
        }
        if (transType.equalsIgnoreCase("ST") || transType.equalsIgnoreCase("CN") || transType.equalsIgnoreCase("CR") || transType.equalsIgnoreCase("SA")) {
            discountLayout.setVisibility(View.GONE);
        }

        TextView txtrateTitle = (TextView) edQtyDialog.findViewById(R.id.textView11);
        if (transType.equalsIgnoreCase("PB") || transType.equalsIgnoreCase("BT")) {
            txtrateTitle.setText("Purchase Rate   : ");
        }

        Button submit = (Button) edQtyDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String qty = "", mrp = "", discount = "", amount = "";
                boolean boolQty = true, boolMrp = true, boolDisc = true;

                qty = edQty.getText().toString();
                mrp = edMrp.getText().toString();
                discount = edDisc.getText().toString();

                if (qty.length() > 0 && !qty.equalsIgnoreCase("0") && !qty.equalsIgnoreCase(".")) {
                    Constants.selectedProductMasterList.get(position).setQty(qty);
                } else {
                    boolQty = false;
                }

                if (mrpLayout.getVisibility() == View.VISIBLE) {
                    if (mrp.length() > 0 && !mrp.equalsIgnoreCase("0") && !mrp.equalsIgnoreCase(".")) {
                        Constants.selectedProductMasterList.get(position).setMrpValue(mrp);
                    } else {
                        boolMrp = false;
                    }
                }

                if (amountLayout.getVisibility() == View.VISIBLE) {
                    amount = edAmt.getText().toString();
                    if (amount.length() > 0 && !amount.equalsIgnoreCase("0") && !amount.equalsIgnoreCase(".")) {
                        double diff = Double.parseDouble(amount) - Double.parseDouble(Constants.selectedProductMasterList.get(position).getAmount());
                        if (diff != 0) {// Amount was changed..
                            Constants.selectedProductMasterList.get(position).setAmount(amount);
                            Double amountD = Double.parseDouble(amount);
                            Double qtyD = Double.parseDouble((boolQty ? edQty.getText().toString() : "1"));
                            if (qtyD > 0) {
                                Constants.selectedProductMasterList.get(position).setMrpValue(String.valueOf(amountD / qtyD));
                            }
                        }
                    }
                }

                if (discountLayout.getVisibility() == View.VISIBLE) {
                    if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
                        if (discount.length() > 0 && !discount.equalsIgnoreCase("0") && !discount.equalsIgnoreCase(".")) {
                            if (Double.parseDouble(discount) <= 100) {
                                Constants.selectedProductMasterList.get(position).setTradeDiscnt(discount);
                            } else {
                                boolDisc = false;
                            }
                        } else {
                            Constants.selectedProductMasterList.get(position).setTradeDiscnt("0");
                        }
                    } else {
                        if (discount.length() > 0 && !discount.equalsIgnoreCase("0") && !discount.equalsIgnoreCase(".")) {
                            Constants.selectedProductMasterList.get(position).setVat(discount);
                        } else {
                            Constants.selectedProductMasterList.get(position).setVat("0");
                        }
                    }
                }


                if (boolQty == true && boolMrp == true && boolDisc == true) {
                    edQtyDialog.cancel();
                    calculateTotal();
                    if (!(Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes") &&
                            Constants.orderFormDetailsObj.getVatCalcOn().equalsIgnoreCase("ordervalue"))) {
                        introduceVATInProduct();
                    }

                    Double currentQuantity = Double.parseDouble(Constants.selectedProductMasterList.get(position).getQty());
                    Double diffInQty = currentQuantity - previousQty;
                    ProductMasterDetails masterObj = new ProductMasterDetails();
                    masterObj.setQty(String.valueOf(diffInQty));
                    masterObj.setProdCode(Constants.selectedProductMasterList.get(position).getProdCode());
                    if (diffInQty > 0) {
                        if (transType.equalsIgnoreCase("PB") || transType.equalsIgnoreCase("CR")) {
                            dataHelperObj.increaseClStkProductWise(masterObj);
                        } else if (transType.equalsIgnoreCase("ST") || transType.equalsIgnoreCase("CN")
                                || transType.equalsIgnoreCase("SR") || transType.equalsIgnoreCase("SA") || transType.equalsIgnoreCase("SH")) {
                            dataHelperObj.reduceClStkProductWise(masterObj);
                        }
                    } else {
                        if (transType.equalsIgnoreCase("PB") || transType.equalsIgnoreCase("CR")) {
                            dataHelperObj.reduceClStkProductWise(masterObj);
                        } else if (transType.equalsIgnoreCase("ST") || transType.equalsIgnoreCase("CN")
                                || transType.equalsIgnoreCase("SR") || transType.equalsIgnoreCase("SA") || transType.equalsIgnoreCase("SH")) {
                            dataHelperObj.increaseClStkProductWise(masterObj);
                        }
                    }

                    adapter.notifyDataSetChanged();
                    Toast.makeText(StockTransferConfirmationActivity.this, "Order has been edited for this product.", Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(StockTransferConfirmationActivity.this, "Please provide a valid input", Toast.LENGTH_LONG).show();
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
                String timeStamp = "";
                //Since this page is called only for SALES - We can use newTimestamp for all cases.
                if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes") && remarksDate.length() != 0) {
                    timeStamp = Constants.dateString + remarksDate + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                } else {
                    timeStamp = Constants.dateString + Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                }
                dataHelperObj.insertToOrderHeaderTableForStockin("O", remarks, timeStamp, "", "", "", "", transType, vat);
                dataHelperObj.insertToOrderDetailsTable(timeStamp);
                dataHelperObj.insertToTransactionLogTable(timeStamp, transType, remarks);
                dataHelperObj.insertToLocationTable("O", timeStamp);
                gpstracker.stopUsingGPS();
                if (transType.equalsIgnoreCase("PB") || transType.equalsIgnoreCase("CR")) {
                    dataHelperObj.increaseClStk();
                } else if (transType.equalsIgnoreCase("ST") || transType.equalsIgnoreCase("CN")
                        || transType.equalsIgnoreCase("SR") || transType.equalsIgnoreCase("SA") || transType.equalsIgnoreCase("SH")) {
                    dataHelperObj.reduceClStk();
                }

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();

    }

    public double calculateTotal() {
        double amount = 0;
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("no")) {
            for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                double qty = Double.parseDouble(currentObj.getQty());
                String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj.getMrpValue() : currentObj.getAmount();
                double mrp = Double.parseDouble(mrpVal);
                amount = amount + ((mrp * qty));
            }
            txtTotal.setText("" + defaultFormat.format(amount));
            totalAmount = amount + "";
        } else {
            if (Constants.orderFormDetailsObj.getVatDetails().equalsIgnoreCase("amount")) {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj.getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + vat);
                }
            } else {
                for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
                    ProductMasterDetails currentObj = Constants.selectedProductMasterList.get(ii);
                    double qty = Double.parseDouble(currentObj.getQty());
                    String mrpVal = currentObj.getMrpValue().length() > 0 ? currentObj.getMrpValue() : currentObj.getAmount();
                    double mrp = Double.parseDouble(mrpVal);
                    double vat = Double.parseDouble(currentObj.getVat());
                    amount = amount + ((mrp * qty) + (mrp * qty * vat / 100));
                }
            }
        }
        txtTotal.setText(currency + defaultFormat.format(amount));
        totalAmount = amount + "";
        return amount;
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
        //this.getWindow().setType(WindowManager.LayoutParams.TYPE_KEYGUARD);
    }


    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_MENU || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

}
