package com.forcepower.acedns.activity;

import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.text.InputFilter;
import android.text.InputType;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemLongClickListener;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSaudaOreder;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.SaudaConfirmAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.SaudaHeader;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DecimalDigitsInputFilter;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.math.BigDecimal;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.BrokerageCost;
import static com.forcepower.acedns.constants.Constants.HoneyCombCost;
import static com.forcepower.acedns.constants.Constants.marginCost;
import static com.forcepower.acedns.constants.Constants.maxLiquidationDiscountForCurrentCustomer;
import static com.forcepower.acedns.constants.Constants.maxallocation;
import static com.forcepower.acedns.constants.Constants.selectedVerticalOfUser;

/**
 * An activity which will use for Confirm order of Forward Trading
 *
 * @author Sourav Das <souravd@coral.in>
 * @version 5.1.9
 */


public class SaudaConfirmationActivity extends FragmentActivity implements OnClickListener, OnItemClickListener, OnItemLongClickListener {

    public static String TAG = "SAUDA CONFIRMATION ACTIVITY";
    public static ListView mListViewProductDetails = null;

    public static Button mButtonAddProduct = null;
    public static Button mButtonSubmit = null;
    public static Button mButtonRemarks = null;
    public static Button mButtonTradeDiscount = null;
    public static Button mButtonSaleType = null;
    public static Button mButtonVAT = null;

    public static Button mButtonBack = null;

    public static TextView mTextViewTotal = null;

    public static ImageView mImageViewLogo = null;
    public boolean isRemarksBtnPressed = false;
    public boolean isTdInput = false;
    String mConversionFactor = "";
    int POSITION = 0;        //Position of the sauda allocation list
    double mMaxAllocation = 0.0;
    double mInputQuantity = 0.0;
    String mProductCode = "";
    String mGroupCode = "";
    String mQuantity = "";
    String mMaxTD = "";
    String mTotalValue = "";
    String mTradeDiscount = "0";
    SaudaHeader mSaudaHeader;// = new SaudaHeader();
    String mSaudaType = "";
    String mSaudaBooked = "";
    String mRemarks = "";
    String mValidity = "";
    String mCurrentDate = "";
    String mTodayDate = "";
    SaudaConfirmAdapter saudaConfirmAdapter;

    AceDnsTransactionDatabase dataHelperObj;
    Context mContext;
    String saleType = "", totalOrderValue = "";
    String insertStatus = "";
    Handler mHandler;
    ProgressDialog loader;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    String tagDistributorCode = "";
    Dialog customerListDialog;
    ArrayList<CustomerDetails> customerList, tempCustomerList;
    String lastStr = "";
    CustomerAdapter adapterCust;
    int chosenCustPos;
    String taggedCustomerCode = "";
    boolean carryInSales = false;
    CaldroidListener listener;
    Button multipleRemarksButton;
    SimpleDateFormat dateFormat;
    // DecimalFormat df;
    String billTotal = "0.00";
    String vat = "0.00";
    String remarksDate = "";
    String inputLiquidationDiscountForCurrentProduct = "0";
    Double freightRate = 0.0;
    double basicRate, secondaryFreight, primaryFreight, depotCost;
    private AceDnsDatabase mAceDnsDatabase;
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sauda_confirmation);
        RegisterActivities.registerActivity(this);

        carryInSales = getIntent().getBooleanExtra("CARRY_IN", false);
        mSaudaType = getIntent().getStringExtra("SAUDATYPE");
        mSaudaBooked = getIntent().getStringExtra("SAUDABOOKED");

        Constants.isFromConfirmationActivity = true;

        mContext = SaudaConfirmationActivity.this;
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        ViewInitialization();
        GetCurerentDate();

        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase(
                "yes")
                && Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase(
                "customer wise")) {
            mTradeDiscount = Constants.selectedCustomer.getTradeDiscount();
        }

        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase(
                "credit")) {
            saleType = "CREDIT";
        }

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    SaudaConfirmationActivity.this
                            .runOnUiThread(new Runnable() {
                                public void run() {
                                    new TRANS_SubmitSaudaOreder(mContext, true).execute();
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
                    String timeStamp = new SimpleDateFormat("HHmmss")
                            .format(Calendar.getInstance().getTime());
                    Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss")
                            .parse(Constants.dateString + timeStamp);
                    if (!date.after(currentDate)) {
                        dialogCaldroidFragment.dismiss();
                        multipleRemarksButton.setText(dateFormat.format(date));
                        remarksDate = new SimpleDateFormat("yyyyMMdd")
                                .format(date);
                    } else {
                        Utils.showToast(mContext,
                                "Future Dates cannot be selected.");
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
                    String timeStamp = new SimpleDateFormat("HHmmss")
                            .format(Calendar.getInstance().getTime());
                    Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss")
                            .parse(Constants.dateString + timeStamp);
                    if (!date.after(currentDate)) {
                        dialogCaldroidFragment.dismiss();
                        multipleRemarksButton.setText(dateFormat.format(date));
                        remarksDate = new SimpleDateFormat("yyyyMMdd")
                                .format(date);
                    } else {
                        Utils.showToast(mContext,
                                "Future Dates cannot be selected.");
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onCaldroidViewCreated() {

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

    public void ViewInitialization() {

        mImageViewLogo = (ImageView) findViewById(R.id.imagelogo);

        mListViewProductDetails = (ListView) findViewById(R.id.listView_product);

        mButtonAddProduct = (Button) findViewById(R.id.button_add);
        mButtonSubmit = (Button) findViewById(R.id.button_order_submit);
        mButtonRemarks = (Button) findViewById(R.id.button_remarks);
        mButtonBack = (Button) findViewById(R.id.button_back);
        mButtonTradeDiscount = (Button) findViewById(R.id.button_discount);
        mButtonSaleType = (Button) findViewById(R.id.button_saletype);
        mButtonVAT = (Button) findViewById(R.id.button_vat);

        mTextViewTotal = (TextView) findViewById(R.id.textView_total);

        mButtonAddProduct.setOnClickListener(SaudaConfirmationActivity.this);
        mButtonSubmit.setOnClickListener(SaudaConfirmationActivity.this);
        mButtonRemarks.setOnClickListener(SaudaConfirmationActivity.this);
        mButtonBack.setOnClickListener(SaudaConfirmationActivity.this);
        mButtonTradeDiscount.setOnClickListener(SaudaConfirmationActivity.this);
        mButtonSaleType.setOnClickListener(SaudaConfirmationActivity.this);
        mButtonVAT.setOnClickListener(SaudaConfirmationActivity.this);

        GrandTotalAmount();

        saudaConfirmAdapter = new SaudaConfirmAdapter(SaudaConfirmationActivity.this, R.layout.sauda_confirm_list_layout, Constants.selectedSaudaDetailsList, mSaudaType);

        mListViewProductDetails.setAdapter(saudaConfirmAdapter);
        mListViewProductDetails
                .setOnItemClickListener(SaudaConfirmationActivity.this);
        mListViewProductDetails
                .setOnItemLongClickListener(SaudaConfirmationActivity.this);

        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase(
                "credit")) {
            mButtonSaleType.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));

        if (Constants.orderFormDetailsObj.getTradeDiscount().equalsIgnoreCase(
                "yes")
                && Constants.orderFormDetailsObj.getTdType().equalsIgnoreCase(
                "order value wise")) {
            mButtonTradeDiscount.setVisibility(View.VISIBLE);
        } else {
            mButtonTradeDiscount.setVisibility(View.GONE);
        }
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")
                && Constants.orderFormDetailsObj.getVatCalcOn()
                .equalsIgnoreCase("ordervalue")) {
            String[] vatAvailArray = Constants.orderFormDetailsObj.getVatType()
                    .split(",");
            if (Arrays.asList(vatAvailArray).contains("SO")
                    || Arrays.asList(vatAvailArray).contains("SB")) {
                mButtonVAT.setVisibility(View.VISIBLE);
            } else {
                mButtonVAT.setVisibility(View.GONE);
            }
        } else {
            mButtonVAT.setVisibility(View.GONE);
        }
    }

    @Override
    public void onClick(View arg0) {
        if (arg0 == mButtonAddProduct) {
            finish();
        } else if (arg0 == mButtonSubmit) {


            if (Constants.selectedSaudaDetailsList.size() > 0) {
                mButtonSubmit.setEnabled(false);
                if (GenerateSaudaHeaderData() == true) {
                    new GPSTracker(mContext);
                    SaveSaudaDataToDatabase();
                } else {
                    mButtonSubmit.setEnabled(true);
                    Utils.showToast(mContext, "Error in Sauda Header Data");
                }
            } else {
                Utils.showToast(mContext, "There is no sauda in the cart");
            }

        } else if (arg0 == mButtonBack) {
            finish();
        } else if (arg0 == mButtonSaleType) {
            showSaleTypeDialog();
        } else if (arg0 == mButtonRemarks) {
            isRemarksBtnPressed = true;
            ShowRemarksDateDialog();
        } else if (arg0 == mButtonTradeDiscount) {
            ShowTradeDiscountDialog();
        } else if (arg0 == mButtonVAT) {
            showVATDialog();
        }
    }

    public void showVATDialog() {
        final Dialog vatDialog = new Dialog(mContext);
        vatDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        vatDialog.setContentView(R.layout.vat_dialog);
        TextView title = (TextView) vatDialog.findViewById(R.id.title);
        title.setText("Enter VAT amount");
        final EditText edInst = (EditText) vatDialog
                .findViewById(R.id.ed_input);
        edInst.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        Button submit = (Button) vatDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (edInst.getText().toString().length() > 0
                        && !edInst.getText().toString().equalsIgnoreCase(".")) {
                    vat = edInst.getText().toString();
                }
                saudaConfirmAdapter.notifyDataSetChanged();
                vatDialog.cancel();
            }
        });
        vatDialog.show();
    }

    public void ShowTradeDiscountDialog() {
        final Dialog discountDialog = new Dialog(mContext);
        discountDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        discountDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = (TextView) discountDialog.findViewById(R.id.title);
        title.setText("Enter Trade Discount ?");

        final EditText editTextTD = (EditText) discountDialog
                .findViewById(R.id.ed_input);
        editTextTD.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        Button submit = (Button) discountDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String td = "0";
                if (editTextTD.getText().toString().length() > 0
                        && !editTextTD.getText().toString()
                        .equalsIgnoreCase(".")) {
                    td = editTextTD.getText().toString();
                }
                if (Double.parseDouble(td) <= 100) {
                    discountDialog.cancel();
                    mTradeDiscount = td;
                    // calculateTotal();
                } else {
                    Toast.makeText(SaudaConfirmationActivity.this,
                            "Trade Discount cannot be more than 100 %", 2000)
                            .show();
                }
            }
        });
        discountDialog.show();
    }

    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        instructionDialog.setCancelable(false);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst = (EditText) instructionDialog
                .findViewById(R.id.ed_input);
        edInst.setText(mRemarks);
        Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                instructionDialog.cancel();
                String instruction = "";
                instruction = edInst.getText().toString();
                mRemarks = instruction;
            }
        });
        instructionDialog.show();
    }

    /**
     * This Function show the remarks  dialog.
     */
    public void ShowRemarksDateDialog() {
        final Dialog RemarksDialog = new Dialog(mContext);
        RemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        RemarksDialog.setContentView(R.layout.remarks_date_layout);
        RemarksDialog.setCancelable(false);
        TextView title = (TextView) RemarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");

        final EditText edittextRemarks = (EditText) RemarksDialog
                .findViewById(R.id.ed_input_r1);
//		final DatePicker picker = (DatePicker) RemarksDialog.findViewById(R.id.datePicker1);

        Button submit = (Button) RemarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

//				String validate = "";
//				StringBuilder builder = new StringBuilder();
//
//				if (picker.getMonth() >= 9) {
//					if(picker.getDayOfMonth()>=10)
//					{
//						validate = String.valueOf(picker.getYear())
//								+ String.valueOf(picker.getMonth() + 1)
//								+ String.valueOf(picker.getDayOfMonth());
//
//					}
//					else
//					{
//						validate = String.valueOf(picker.getYear())
//								+ String.valueOf(picker.getMonth() + 1)
//								+ "0"+String.valueOf(picker.getDayOfMonth());
//					}
//
//				} else {
//
//					if(picker.getDayOfMonth()>=10)
//					{
//						validate = String.valueOf(picker.getYear()) + "0"
//								+ String.valueOf(picker.getMonth() + 1)
//								+ String.valueOf(picker.getDayOfMonth());
//					}
//					else
//					{
//						validate = String.valueOf(picker.getYear()) + "0"
//								+ String.valueOf(picker.getMonth() + 1)+"0"
//								+ String.valueOf(picker.getDayOfMonth());
//					}
//
//				}

//				builder.append(picker.getYear() + "-");
//				builder.append(picker.getMonth() + 1 + "-");// month is 0 based
//				builder.append(picker.getDayOfMonth());
//				mValidity = builder.toString();

                if (edittextRemarks.getText().length() == 0
                        && !edittextRemarks.getText().toString()
                        .equalsIgnoreCase(".")) {
                    mRemarks = "";
                } else {
                    mRemarks = edittextRemarks.getText().toString();
                }

//				if (Integer.valueOf(validate) < Integer.valueOf(mCurrentDate)) {
//					Toast.makeText(SaudaConfirmationActivity.this,
//							"Please provide valid date", Toast.LENGTH_LONG).show();
//				} else {
					/*Toast.makeText(SaudaConfirmationActivity.this, mValidity,
							2000).show();*/
                RemarksDialog.cancel();
//				}

            }
        });
        RemarksDialog.show();
    }

    public void showMultiRemarksDialog() {
        final Dialog multiRemarksDialog = new Dialog(mContext);
        multiRemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        multiRemarksDialog.setContentView(R.layout.multi_instruction_dialog);
        multiRemarksDialog.setCancelable(false);
        TextView title = (TextView) multiRemarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst1 = (EditText) multiRemarksDialog
                .findViewById(R.id.ed_input_r1);
        multipleRemarksButton = (Button) multiRemarksDialog
                .findViewById(R.id.ed_input_r2);
        multipleRemarksButton.setText("");
        final EditText edInst3 = (EditText) multiRemarksDialog
                .findViewById(R.id.ed_input_r3);
        edInst3.setFilters(new InputFilter[]{new DecimalDigitsInputFilter(16,
                2)});
        String[] remarksVal = mRemarks.split(";");
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
                if (billTotal.length() > 0 && Double.parseDouble(billTotal) > 0) {
                    instruction = edInst1.getText().toString() + ";"
                            + multipleRemarksButton.getText().toString() + ";"
                            + billTotal;
                    mRemarks = instruction;
                    multiRemarksDialog.cancel();
                } else {
                    Utils.showToast(mContext, "Enter Non Zero Amount");
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

    public void showSaleTypeDialog() {
        final Dialog grpDialog = new Dialog(SaudaConfirmationActivity.this);
        grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        grpDialog.setContentView(R.layout.sale_type_dialog);
        grpDialog.setCancelable(false);
        TextView title = (TextView) grpDialog.findViewById(R.id.title);
        title.setText("Select a Sale Type.");
        final RadioButton radioCredit = (RadioButton) grpDialog
                .findViewById(R.id.rd_credit);
        final RadioButton radioCOD = (RadioButton) grpDialog
                .findViewById(R.id.rd_cod);
        final RadioButton radioPay = (RadioButton) grpDialog
                .findViewById(R.id.rd_pay);

        if (Constants.orderFormDetailsObj.getPayment_type().equalsIgnoreCase(
                "cash")) {
            radioCredit.setVisibility(View.GONE);
        } else {
            radioCredit.setVisibility(View.VISIBLE);
        }

        radioCredit.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView,
                                         boolean isChecked) {
                if (isChecked) {
                    radioCOD.setChecked(false);
                    radioPay.setChecked(false);
                    saleType = "CREDIT";
                    grpDialog.cancel();
                }
            }
        });
        radioCOD.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView,
                                         boolean isChecked) {
                if (isChecked) {
                    radioCredit.setChecked(false);
                    radioPay.setChecked(false);
                    saleType = "COD";
                    grpDialog.cancel();
                }
            }
        });
        radioPay.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView,
                                         boolean isChecked) {
                if (isChecked) {
                    radioCredit.setChecked(false);
                    radioCOD.setChecked(false);
                    saleType = "CASH";
                    grpDialog.cancel();
                }
            }
        });
        grpDialog.show();
    }

    @Override
    public void onItemClick(AdapterView<?> arg0, View arg1, int pos, long arg3) {
        mGroupCode = Constants.selectedSaudaDetailsList
                .get(pos).getGroupCode();
        mQuantity = Constants.selectedSaudaDetailsList
                .get(pos).getQuantity();
        mConversionFactor = Constants.selectedSaudaDetailsList
                .get(pos).getConversionFactor();
        mMaxTD = Constants.selectedSaudaDetailsList
                .get(pos).getMaxTD();
        if (Constants.selectedSaudaDetailsList.get(pos).getTDorPremiumCheck().equalsIgnoreCase("yes")) {
            isTdInput = true;
        } else {
            isTdInput = false;
        }

        GetDeletedITEM(mGroupCode, mQuantity);
        ShowEditSaudaDialog(pos);
    }

    @Override
    public boolean onItemLongClick(AdapterView<?> arg0, View arg1, int arg2,
                                   long arg3) {
        System.out.println("POSITION::::::::::" + arg2);
        mConversionFactor = Constants.selectedSaudaDetailsList
                .get(arg2).getConversionFactor();
        ShowDeleteItemDialog(arg2);
        return true;
    }

    /**
     * This Function show the Delete dialog.
     *
     * @param position position of list of selected product which is going to edit
     */
    public void ShowDeleteItemDialog(final int pos) {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(
                SaudaConfirmationActivity.this);
        alertDialogBuilder.setMessage(
                "Are you sure you want to delete this item ?").setCancelable(
                false);
        alertDialogBuilder.setNegativeButton("OK",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();

                        mProductCode = Constants.selectedSaudaDetailsList
                                .get(pos).getSkuCode();
                        if (selectedVerticalOfUser.matches("HBC:Rasoi:BIB")) {

                            double maxAllo = Double.parseDouble(Constants.selectedSaudaDetailsList.get(pos).getQuantity());
                            maxAllo = mAceDnsDatabase.calculatedValueC2M(mProductCode, maxAllo);
                            maxallocation = maxallocation + maxAllo;
                        }
                        RELEASEProduct(mProductCode);
                        mGroupCode = Constants.selectedSaudaDetailsList.get(pos).getGroupCode();
                        mQuantity = Constants.selectedSaudaDetailsList.get(pos).getQuantity();
                        GetDeletedITEM(mGroupCode, mQuantity);
                        Constants.selectedSaudaDetailsList
                                .remove(Constants.selectedSaudaDetailsList
                                        .get(pos));
                        GrandTotalAmount();
                        saudaConfirmAdapter.notifyDataSetChanged();

                    }
                });
        alertDialogBuilder.setPositiveButton("CANCEL",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        dialog.cancel();
                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    /**
     * This Function show the edit dialog.
     *
     * @param position position of list of selected product which is going to edit
     */
    public void ShowEditSaudaDialog(final int position) {

        double totalbalance = Double
                .parseDouble(Constants.selectedAlocatedSaudaList
                        .get(POSITION).getBalance());
        final double maxallocation = CalculateUOM1(totalbalance,
                Double.parseDouble(mConversionFactor));

        mMaxAllocation = maxallocation;

        final Dialog edQtyDialog = new Dialog(SaudaConfirmationActivity.this);

        edQtyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        edQtyDialog.setContentView(R.layout.edit_sauda_activity_layout);
        TextView title = (TextView) edQtyDialog.findViewById(R.id.title);
        title.setText("Provide valid inputs");

        final LinearLayout FreightLayout = (LinearLayout) edQtyDialog.findViewById(R.id.layoutFC);
        final LinearLayout liquidationDiscountLayout = (LinearLayout) edQtyDialog.findViewById(R.id.liquidationDiscountLayout);


        final TextView textViewTDorPremium = (TextView) edQtyDialog.findViewById(R.id.textViewTD);
        final TextView mEditTextLiquidationDiscount = (TextView) edQtyDialog.findViewById(R.id.mEditTextLiquidationDiscount);

        final EditText edQty = (EditText) edQtyDialog
                .findViewById(R.id.editTextQuantity);
        final EditText edSale = (EditText) edQtyDialog
                .findViewById(R.id.editTextSaleRate);
        final EditText edDisc = (EditText) edQtyDialog
                .findViewById(R.id.edittextTD);
        final EditText edFreight = (EditText) edQtyDialog
                .findViewById(R.id.editTextFC);

        edQty.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edSale.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edDisc.setInputType(InputType.TYPE_CLASS_NUMBER
                | InputType.TYPE_NUMBER_FLAG_DECIMAL);
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

        TextView txtTDVatTitle = (TextView) edQtyDialog
                .findViewById(R.id.txt_td_vat);
        if (Constants.orderFormDetailsObj.getVat().equalsIgnoreCase("yes")) {
            txtTDVatTitle.setText("VAT                     : ");
        }

        edQty.setText(Constants.selectedSaudaDetailsList.get(position)
                .getQuantity());
        basicRate = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getSaleRate());
        secondaryFreight = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getFreightCharge());
        primaryFreight = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getPrimaryFreight());
        depotCost = Double.parseDouble(Constants.selectedSaudaDetailsList.get(position).getDepotCost());
        HoneyCombCost = Constants.selectedSaudaDetailsList.get(position).getHoneyCombCost();
        marginCost = Constants.selectedSaudaDetailsList.get(position).getMarginCost();
        double saleRate = 0.0;
        if (mSaudaType.equalsIgnoreCase("FOR") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
            saleRate = basicRate + secondaryFreight + primaryFreight + depotCost + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost);
        } else {
            saleRate = basicRate + primaryFreight + depotCost + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost);
        }

        if (isTdInput == true) {
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
//		edFreight.setEnabled(false);
        edFreight.setText(Constants.selectedSaudaDetailsList.get(position)
                .getFreightCharge());

        Button submit = (Button) edQtyDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String qty = "";
                String salerate = "";
                String discount = "";
                String freightcharge = "";

                Double dquantity = 0.0;
                Double dsalerate = 0.0;
                Double ddiscount = 0.0;
                Double dfreightcharge = 0.0;
                Double dpremium = 0.0;

                Double dtotalamount = 0.0;

                boolean boolQty = true, boolDisc = true;
                int boolFreight = 0;
                Boolean LiquidationDiscountValid = true;
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
                Boolean isSecondaryFreightOk = true;
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
//					else
//					{
//						freightRate=0.0;
//					}
                } else {
                    freightRate = 0.0;
                }

                if (!isSecondaryFreightOk) {
                    return;
                }

                qty = edQty.getText().toString();
                salerate = edSale.getText().toString();
                discount = edDisc.getText().toString();

                if (qty.length() > 0 && !qty.equalsIgnoreCase("0")
                        && !qty.equalsIgnoreCase(".")) {
                    dquantity = Double.valueOf(qty);
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
                            Toast.makeText(SaudaConfirmationActivity.this, "Exceed Quantity", Toast.LENGTH_LONG).show();
                        }
                    } else {
                        if (dquantity > maxallocation) {
                            boolQty = false;
                            Toast.makeText(SaudaConfirmationActivity.this, "Exceed Quantity", Toast.LENGTH_LONG).show();
                        }
                    }


                } else {
                    boolQty = false;
                }

                dsalerate = Double.valueOf(salerate);

                if (FreightLayout.getVisibility() == View.VISIBLE) {
                    freightcharge = edFreight.getText().toString();
                    if (freightcharge.length() > 0
                            && !freightcharge.equalsIgnoreCase("0")
                            && !freightcharge.equalsIgnoreCase(".")) {
                        dfreightcharge = Double.valueOf(freightcharge);
                        boolFreight = 1;
                    } else {
                        Toast.makeText(SaudaConfirmationActivity.this,
                                "Please provide valid Freight", Toast.LENGTH_LONG).show();
                        boolFreight = 2;
                    }
                }

                if (isTdInput == true) {
                    if (discount.length() > 0 && !discount.equalsIgnoreCase(".")) {
                        if (!Constants.selectedVerticalOfUser.contains(Constants.menuDetailsObj.getrun_time_TD_approval_vertical()))//skipping max td checking
                        {
                            if (Double.valueOf(discount) <= Double.valueOf(mMaxTD)) {
                                ddiscount = Double.valueOf(discount);
                            } else {
                                boolDisc = false;
                                Toast.makeText(SaudaConfirmationActivity.this, "Exceed Trade Discount Limit", Toast.LENGTH_LONG).show();
                            }
                        } else {
                            ddiscount = Double.valueOf(discount);
                        }

                    } else {
                        boolDisc = false;
                    }

                } else {
                    if (discount.length() > 0 && !discount.equalsIgnoreCase(".")) {
                        dpremium = Double.valueOf(discount);
                    } else {
                        boolDisc = false;
                    }
                }

                if (isTdInput == true) {
                    if (boolQty == true && boolDisc == true && boolFreight == 0) {
                        edQtyDialog.cancel();
                        dtotalamount = TotalAmount(dquantity, dsalerate,
                                dfreightcharge, ddiscount, dpremium);
                        mInputQuantity = dquantity;
                        Constants.selectedSaudaDetailsList.get(position).setQuantity(qty);
                        Constants.selectedSaudaDetailsList.get(position).setAmount(String.valueOf(dtotalamount));
                        Constants.selectedSaudaDetailsList.get(position).setTD(discount);

                    } else if (boolQty == true && boolDisc == true
                            && boolFreight == 1) {
                        edQtyDialog.cancel();
                        dtotalamount = TotalAmount(dquantity, dsalerate,
                                dfreightcharge, ddiscount, dpremium);
                        mInputQuantity = dquantity;
                        Constants.selectedSaudaDetailsList.get(position).setQuantity(qty);
                        Constants.selectedSaudaDetailsList.get(position).setAmount(String.valueOf(dtotalamount));
                        Constants.selectedSaudaDetailsList.get(position).setTD(discount);
//						Constants.selectedSaudaDetailsList.get(position).setFreightCharge(freightcharge);

                    } else {
                        Toast.makeText(SaudaConfirmationActivity.this,
                                "Please provide a valid input", Toast.LENGTH_LONG).show();
                    }
                    GrandTotalAmount();
                } else {
                    if (boolQty == true && boolDisc == true && boolFreight == 0) {
                        edQtyDialog.cancel();
                        dtotalamount = TotalAmount(dquantity, dsalerate,
                                dfreightcharge, ddiscount, dpremium);
                        mInputQuantity = dquantity;
                        Constants.selectedSaudaDetailsList.get(position).setQuantity(qty);
                        Constants.selectedSaudaDetailsList.get(position).setAmount(String.valueOf(dtotalamount));
                        Constants.selectedSaudaDetailsList.get(position).setPremium(String.valueOf(dpremium));

                    } else if (boolQty == true && boolDisc == true && boolFreight == 1) {
                        edQtyDialog.cancel();
                        dtotalamount = TotalAmount(dquantity, dsalerate,
                                dfreightcharge, ddiscount, dpremium);
                        mInputQuantity = dquantity;
                        Constants.selectedSaudaDetailsList.get(position).setQuantity(qty);
                        Constants.selectedSaudaDetailsList.get(position).setAmount(String.valueOf(dtotalamount));
                        Constants.selectedSaudaDetailsList.get(position).setPremium(String.valueOf(dpremium));
                        Constants.selectedSaudaDetailsList.get(position).setFreightCharge(freightcharge);


                    } else {
                        Toast.makeText(SaudaConfirmationActivity.this,
                                "Please provide a valid input", Toast.LENGTH_LONG).show();
                    }
                    GrandTotalAmount();
                }
                REFRESHSaudaConfirmationActivity();
                saudaConfirmAdapter.notifyDataSetChanged();
            }
        });
        edQtyDialog.show();
    }

    public void ShowPrintingOptionDialog() {
        final Dialog printDialog = new Dialog(SaudaConfirmationActivity.this);
        printDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        printDialog.setContentView(R.layout.printing_dialog);
        printDialog.setCancelable(false);
        TextView title = (TextView) printDialog.findViewById(R.id.title);
        title.setText("Would you like to have a print of the Order ?");
        Button yes = (Button) printDialog.findViewById(R.id.btn_yes);
        Button no = (Button) printDialog.findViewById(R.id.btn_no);
        yes.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

                printDialog.cancel();
                String timeStamps = Constants.dateString
                        + new SimpleDateFormat("HHmmss").format(Calendar
                        .getInstance().getTime());
                Intent intent = new Intent(getApplicationContext(),
                        BluetoothChatActivity.class);
                Bundle bundle = new Bundle();
                bundle.putString("PrintFor", "ORDER");
                bundle.putString("ORDER_ID", "O"
                        + Constants.employeeDetailObject.getEmpCode()
                        + timeStamps);
                bundle.putString("CustomerName",
                        Constants.selectedCustomer.getCustomerName());
                intent.putExtras(bundle);
                startActivity(intent);
            }
        });
        no.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                printDialog.cancel();
                startActivity(new Intent(SaudaConfirmationActivity.this,
                        MenuActivity.class));
                finish();
            }
        });
        printDialog.show();
    }

    public void SaveSaudaDataToDatabase() {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                String timeStamp = "";
                timeStamp = Constants.dateString
                        + new SimpleDateFormat("HHmmss").format(Calendar
                        .getInstance().getTime());

                dataHelperObj.INSERTtoSaudaDetails(timeStamp);
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

    /**
     * This Function used to generate sauda header data which will be saved to buffer
     *
     * @return true
     * if successfully saved
     */

    public boolean GenerateSaudaHeaderData() {
        boolean issuccess = false;
        try {
            String brokerid = "";
            String customercode = Constants.selectedCustomer.getCustomerCode();
            String transfered = "";
            String tradediscount = "";
            String saudavalue = GrandTotalAmount();
            if (Constants.saudaFormDetailsObj.getSaudaBookedThrough()
                    .equalsIgnoreCase("both")) {
                if (mSaudaBooked.equalsIgnoreCase("Broker")) {
                    brokerid = Constants.selectedBroker.getBrokerId();
                }
            }
            String transactiontype = "OB";
            String vat = "";
            String branchcode = Constants.selectedBranch.getBranchCode();

//			if (isRemarksBtnPressed == false) {
//				mValidity = mTodayDate;
//			}

            issuccess = SaudaHeaderData(customercode, transfered, mRemarks,
                    tradediscount, saudavalue, brokerid, transactiontype, vat,
                    branchcode, mValidity);

        } catch (Exception ex) {
            issuccess = false;
        }
        return issuccess;

    }

    /**
     * This Function save the data of the all product to the collection buffer which are added to cart.
     *
     * @param customercode    code of the customer
     * @param transfered      transferred mode
     * @param remarks         remarks enter by the user
     * @param tradediscount   trade discount the product
     * @param saudavalue      total amount of all products
     * @param brokerid        selected broker id
     * @param transactiontype type of the transaction
     * @param vat             vat of the product
     * @param branchcode      code of the depot
     * @param saudavalidity   date of the sauda validity period
     * @return true if successfully saved to buffer.
     */

    public boolean SaudaHeaderData(String customercode, String transfered,
                                   String remarks, String tradediscount, String saudavalue,
                                   String brokerid, String transactiontype, String vat,
                                   String branchcode, String saudavalidity) {
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
        } catch (Exception ex) {
            issuccess = false;
        }
        return issuccess;
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
        //this.getWindow().setType(WindowManager.LayoutParams.TYPE_KEYGUARD);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK
                || keyCode == KeyEvent.KEYCODE_MENU
                || keyCode == KeyEvent.KEYCODE_HOME
                || keyCode == KeyEvent.KEYCODE_POWER) {
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

    /**
     * This Function called some method after deleting the product
     *
     * @param grpcode  Group code of the product
     * @param quantity Quantity of the product which is deleted.
     */
    public void GetDeletedITEM(String grpcode, String quantity) {
        POSITION = GETTSaudaAllocationPostion(grpcode);
        double relesaebalance = CalculateUOM2(Double.valueOf(quantity),
                Double.valueOf(mConversionFactor));
        String balance = Constants.selectedAlocatedSaudaList.get(POSITION).getBalance();
        double previousbalance = Double.valueOf(balance);
        UPDATESaudaAllocation(POSITION, previousbalance, relesaebalance);
    }

    /**
     * This Function position of the list where the group exist
     *
     * @param grpcode Group code of the product
     * @return int
     * position of the list where product group code found.
     */
    public int GETTSaudaAllocationPostion(String grpcode) {
        int position = 0;
        for (int count = 0; count < Constants.selectedAlocatedSaudaList.size(); count++) {
            if (Constants.selectedAlocatedSaudaList.get(count)
                    .getProductFilterCode().equalsIgnoreCase(grpcode)) {
                position = count;
            }
        }
        return position;
    }

    /**
     * This Function update the sauda allocation if a product is added to cart
     *
     * @param position        position of the sauda allocation list
     * @param previousbalance previous balance of the product after adding to cart
     * @param relasebalance   release balance of the product after adding to cart
     */
    public void UPDATESaudaAllocation(int position, double previousbalance, double relasebalance) {
        double balance = previousbalance + relasebalance;
        Constants.selectedAlocatedSaudaList.get(position).setBalance(String.valueOf(balance));
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
    public double CalculateUOM1(double ltr, double conversionfactor) {
        double UOM1 = (ltr / conversionfactor);
        double finalValue = Math.round(UOM1 * 100.0) / 100.0;
        return finalValue;
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
        double amount = quantity * (basicRate + primaryFreight + secondaryFreight + depotCost + Double.parseDouble(BrokerageCost) + Double.parseDouble(HoneyCombCost) + Double.parseDouble(marginCost) + premium - tradediscount - Double.parseDouble(inputLiquidationDiscountForCurrentProduct));

        double finalamount = Math.round(amount * 100.0) / 100.0;
        return finalamount;

    }

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
                dtotalamount += Double
                        .valueOf(Constants.selectedSaudaDetailsList.get(i)
                                .getAmount());
            }
            totalamount = defaultFormat.format(dtotalamount);

        } catch (Exception ex) {
            Log.e("SAUDA CONFIRM ACTIVITY", "Error in Total Amount " + ex.getMessage());
        }
        mTextViewTotal.setText(totalamount);
        return totalamount;
    }

    public void REFRESHSaudaConfirmationActivity() {
        double casebalance = GETBalance(mMaxAllocation,
                mInputQuantity);
        double balance = CalculateUOM2(casebalance,
                Double.valueOf(mConversionFactor));
        UPDATESaudaAllocation(POSITION, String.valueOf(balance));
        Toast.makeText(SaudaConfirmationActivity.this,
                "Order edited successfully", Toast.LENGTH_LONG).show();
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
