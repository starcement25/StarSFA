package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioGroup;
import android.widget.RadioGroup.OnCheckedChangeListener;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.OutstandingAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.OutstandingDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import static com.forcepower.acedns.constants.Constants.orderAuditType;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInCustomerInvoiceWise;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInRouteInvioiceWise;

public class CollectionActivity extends AceDnsParentActivity {

    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    ArrayList<RouteDetails> routeList;
    ImageView headerLogo;
    TextView invAmt, invNumbr, dueAmt;
    Button btnDetails, btnPayment, btnBack, btnCust, btnNoCollc;
    LinearLayout invoiceLayout, transLayout;
    ListView transList;

    OutstandingAdapter adapterTrans;
    ArrayList<CustomerDetails> customerList, tempCustomerList;
    ArrayList<OutstandingDetails> outstandingList;
    CustomerAdapter adapterCust;
    Dialog routeDialog, infoDialog, routePlanListDialog, customerListDialog, invoiceDialog, takeCashDialog, transEditDialog, noOrderDialog;
    String lastStr = "";
    int chosenCustPos;
    double receiptAmt;
    boolean discountRadioChecked;
    DecimalFormat currencyFormatter = new DecimalFormat("#.00");
    boolean onAccountType = false;
    boolean isMultipleDistPrimary=false;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_collection_form);
        RegisterActivities.registerActivity(this);

        mContext = CollectionActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);

        initView();

        if (NotCheckedOut(mContext)  && !Constants.CurrentOrderCollectionTransactionType.matches("TC")) {
            btnCust.setText(PreferenceData.getCheckInOutEmpName(mContext));
        }

//        showPaymentTypeDialog();
//        ChooseOrderType();
        orderAuditType = "Primary";
        showPaymentTypeDialog();
    }
//    public void ChooseOrderType() {
//
//        if (NotCheckedOut(mContext) && !Constants.CurrentOrderCollectionTransactionType.matches("TC"))
//        {
//            showPaymentTypeDialog();
//
//        }
//        else if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary") )
//        {
//            orderAuditType = "Secondary";
//            showPaymentTypeDialog();
//        }
//        else
//        {
//            final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
//            dialgoCondition.setCancelable(false);
//            dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
//            dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
//            dialgoCondition.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));
//
//            TextView txtMsg = (TextView) dialgoCondition.findViewById(R.id.title);
//            txtMsg.setText("Select Order Type.");
//
//            final RadioGroup radioSelectionGroup = (RadioGroup) dialgoCondition.findViewById(R.id.radioSelect);
//            final RadioButton radioEdit = (RadioButton) dialgoCondition.findViewById(R.id.radioEdit);
//            final RadioButton radioRedundant = (RadioButton) dialgoCondition.findViewById(R.id.radioRedundant);
//            radioEdit.setText("Primary");
//            radioRedundant.setText("Secondary");
//
//            radioSelectionGroup
//                    .setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
//                        public void onCheckedChanged(RadioGroup group, int checkedId) {
//                            RadioButton radioSelection = (RadioButton) dialgoCondition.findViewById(checkedId);
//                            if (radioSelection.getText().toString().trim().equalsIgnoreCase("Primary")) {
//                                orderAuditType = "Primary";
//                            } else {
//                                orderAuditType = "Secondary";
//                            }
//                            dialgoCondition.cancel();
//
//
//                        }
//                    });
//            dialgoCondition.show();
//        }
//
//    }
    public void initView() {
        Constants.selectedOutstandingList = new ArrayList<OutstandingDetails>();
        headerLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            headerLogo.setVisibility(View.VISIBLE);
            headerLogo.setImageBitmap(Constants.logoBmp);
        } else {
            headerLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
//		 txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        invoiceLayout = (LinearLayout) findViewById(R.id.invoice_layout);
        transLayout = (LinearLayout) findViewById(R.id.trans_list_layout);
        transList = (ListView) findViewById(R.id.list_trans);
        adapterTrans = new OutstandingAdapter(CollectionActivity.this, R.layout.receipt_child, Constants.selectedOutstandingList, true);
        transList.setAdapter(adapterTrans);
        transList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {

                showTransEditDialog(arg2);
            }
        });

        btnBack = (Button) findViewById(R.id.back);
        btnBack.setOnClickListener(CollectionActivity.this);

        btnPayment = (Button) findViewById(R.id.btn_pay);
        btnPayment.setOnClickListener(CollectionActivity.this);

        btnDetails = (Button) findViewById(R.id.btn_details);
        btnDetails.setEnabled(false);
        btnDetails.setOnClickListener(CollectionActivity.this);

        btnCust = (Button) findViewById(R.id.cust_btn);
        btnCust.setOnClickListener(CollectionActivity.this);

        btnNoCollc = (Button) findViewById(R.id.no_ordr);
        btnNoCollc.setEnabled(false);
        btnNoCollc.setOnClickListener(CollectionActivity.this);

        invAmt = (TextView) findViewById(R.id.txt_amt);
        dueAmt = (TextView) findViewById(R.id.txt_due);
        invNumbr = (TextView) findViewById(R.id.inv_no);
    }


    public void onClick(View clkdView) {
        if (clkdView == btnBack) {
            finish();
        } else if (clkdView == btnCust) {
            showChooseCustomerDialog();
        } else if (clkdView == btnDetails) {
            showChooseInvoiceDialog();
        } else if (clkdView == btnPayment) {
            if (Constants.selectedOutstandingList.size() > 0) {
                startActivity(new Intent(mContext, CollectionConfirmActivity.class));
            } else {
                Utils.showToast(mContext, "Please select Invoice for payment.");
            }
        } else if (clkdView == btnNoCollc) {
            showNoCollcDialog();
        }
    }


    /*************   DIALOG RELATED  PART  *********************/
    public void showRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> todayList)
    {
        if (todayList.size() > 0)
        {
            if(todayList.size()==1)
            {
                collectionCommonProcessRouteSelection(0, todayList);
            }
            else
            {
                final ArrayList<RoutePlanMasterDetails> todayListSearchArray = new ArrayList<>(todayList);
                routePlanListDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
                routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                routePlanListDialog.setContentView(R.layout.select_from_list);
                routePlanListDialog.setCancelable(false);
                ImageView back =  routePlanListDialog.findViewById(R.id.image_cancel);
                back.setVisibility(View.VISIBLE);
                back.setOnClickListener(arg0 ->
                {
                    routePlanListDialog.cancel();
                    finish();
                });
                TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
                title.setText("Please select a Route");
                ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
                final RoutePlanTransAdapter adapter1 = new RoutePlanTransAdapter(CollectionActivity.this, R.layout.route_list_child, todayList);
                dialogList.setAdapter(adapter1);
                final EditText autoCompleteTextView1 = (EditText) routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
                autoCompleteTextView1.setVisibility(View.VISIBLE);
                autoCompleteTextView1.addTextChangedListener(new TextWatcher() {

                    public void afterTextChanged(Editable s) {
                        //RoutePlanAdapter.getFilter().filter(s.toString());
                    }

                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                    }

                    public void onTextChanged(CharSequence s, int start, int before, int count) {

                        String searchString = autoCompleteTextView1.getText().toString();
                        int textLength = searchString.length();

                        //clear the initial data set
                        todayList.clear();
                        for (int i = 0; i < todayListSearchArray.size(); i++) {
                            String routeName = todayListSearchArray.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                            if (textLength <= routeName.length()) {
                                //compare the String in EditText with Names in the ArrayList
                                //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                                if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                    todayList.add(todayListSearchArray.get(i));
                                }
                            }
                        }
                        adapter1.notifyDataSetChanged();

                    }
                });
                dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                    routePlanListDialog.cancel();
                    int pos=arg2;
                    collectionCommonProcessRouteSelection(pos, todayList);
                });
                Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
                cancel.setVisibility(View.INVISIBLE);
                cancel.setOnClickListener(arg0 -> routePlanListDialog.cancel());

                routePlanListDialog.show();

            }

                   }

    }

    public void collectionCommonProcessRouteSelection(int pos, final ArrayList<RoutePlanMasterDetails> todayList) {
        RoutePlanMasterDetails detailsObj = todayList.get(pos);
        if (!onAccountType) {
            customerList = setupDataHelperObj.getCustomerListByRouteWithOS(detailsObj.getRoutecode());
        } else {
            customerList = setupDataHelperObj.getCustomerListByRouteForCollection(detailsObj.getRoutecode());
        }
        if (customerList.size() > 0)
        {
            tempCustomerList = new ArrayList<>();
            reInitialiseCustomerList();
            adapterCust = new CustomerAdapter(CollectionActivity.this, R.layout.customer_list_child, tempCustomerList);
            showChooseCustomerDialog();
            btnCust.setEnabled(false);
            //showInstrucDialog();
        } else {
            Utils.showToast(mContext, "No Customer has been found in this Route. \nPlease select a different Route");
            Handler mHandler = new Handler();
            mHandler.postDelayed(() -> showRoutePlanListDialog(todayList), 0010);
        }
    }
    public void collectionCommonProcessRouteSelectionDisstributor()
    {
        String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);

        customerList = setupDataHelperObj.GetDistributorCustomerListforRoutePlan(today);
        if (customerList.size() > 0)
        {
            tempCustomerList = new ArrayList<>();
            reInitialiseCustomerList();
            adapterCust = new CustomerAdapter(CollectionActivity.this, R.layout.customer_list_child, tempCustomerList);
            showChooseCustomerDialog();
            btnCust.setEnabled(false);
            //showInstrucDialog();
        } else {
            Utils.showToast(mContext, "No Customer has been found.");
        }
    }
    public void showRouteListDialog() {

        if (onAccountType) {
            routeList = setupDataHelperObj.getAllRouteForCollection();
        } else {
            routeList = setupDataHelperObj.getRouteForCollection();
        }
        if (routeList.size() > 0) {
            final ArrayList<RouteDetails> routeListSearchingArray = new ArrayList<>(routeList);
            routeDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
            routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            routeDialog.setContentView(R.layout.select_from_list);
            routeDialog.setCancelable(false);
            TextView title = (TextView) routeDialog.findViewById(R.id.title);
            title.setText("Please select a Route");
            ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
            final RouteAdapter adapter1 = new RouteAdapter(CollectionActivity.this, R.layout.route_list_child, routeList);
            dialogList.setAdapter(adapter1);
            final EditText autoCompleteTextView1 = (EditText) routeDialog.findViewById(R.id.autoCompleteTextView1);
            autoCompleteTextView1.setVisibility(View.VISIBLE);
            autoCompleteTextView1.addTextChangedListener(new TextWatcher() {

                public void afterTextChanged(Editable s) {
                    //RoutePlanAdapter.getFilter().filter(s.toString());
                }

                public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                }

                public void onTextChanged(CharSequence s, int start, int before, int count) {

                    String searchString = autoCompleteTextView1.getText().toString();
                    int textLength = searchString.length();

                    //clear the initial data set
                    routeList.clear();
                    for (int i = 0; i < routeListSearchingArray.size(); i++) {
                        String routeName = routeListSearchingArray.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                routeList.add(routeListSearchingArray.get(i));
                            }
                        }
                    }
                    adapter1.notifyDataSetChanged();

                }
            });
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    routeDialog.cancel();
                    RouteDetails detailsObj = routeList.get(arg2);
                    if (!onAccountType) {
                        customerList = setupDataHelperObj.getCustomerListByRouteWithOS(detailsObj.getRouteCode());
                    } else {
                        customerList = setupDataHelperObj.getCustomerListByRouteForCollection(detailsObj.getRouteCode());
                    }

                    if (customerList.size() > 0) {
                        tempCustomerList = new ArrayList<>();
                        reInitialiseCustomerList();
                        adapterCust = new CustomerAdapter(CollectionActivity.this, R.layout.customer_list_child, tempCustomerList);
                        //showInstrucDialog();
                        showChooseCustomerDialog();
                        btnCust.setEnabled(false);
                    } else {
                        Utils.showToast(mContext, "No Customer has been found in this Route.");
                        Handler mHandler = new Handler();
                        mHandler.postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                finish();
                            }
                        }, 2000);
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
            routeDialog.show();
        } else {
            Toast.makeText(mContext, "No Outstanding", Toast.LENGTH_LONG).show();
            btnCust.setEnabled(false);
            btnPayment.setEnabled(false);
            Handler mHandler = new Handler();
            mHandler.postDelayed(new Runnable() {
                @Override
                public void run() {
                    finish();
                }
            }, 2500);
        }
    }


    public void showCashAmtDialog(final OutstandingDetails currentObj, final boolean isshow) {
        takeCashDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
        takeCashDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        takeCashDialog.setContentView(R.layout.take_payment_dialog);
        takeCashDialog.setCancelable(false);
        TextView title = (TextView) takeCashDialog.findViewById(R.id.title);
        title.setText("Please enter receipt amount");
        TextView invNo = (TextView) takeCashDialog.findViewById(R.id.txt_inv);
        invNo.setText(currentObj.getInvoice_id());
        TextView date = (TextView) takeCashDialog.findViewById(R.id.txt_date);
        date.setText(currentObj.getDate());
        TextView invAmt = (TextView) takeCashDialog.findViewById(R.id.txt_amt);
        invAmt.setText(currencyFormatter.format(Float.parseFloat(currentObj.getInvoice_amount())));
        TextView dueAmt = (TextView) takeCashDialog.findViewById(R.id.txt_due);
        dueAmt.setText(currencyFormatter.format(Float.parseFloat(currentObj.getDue_amount())));

        final LinearLayout discountParentLayout = (LinearLayout) takeCashDialog.findViewById(R.id.discount_parent_layout);
        discountParentLayout.setVisibility(View.GONE);
        final LinearLayout discountLayout = (LinearLayout) takeCashDialog.findViewById(R.id.discount_layout);
        discountLayout.setVisibility(View.GONE);
        final EditText discountAmt = (EditText) takeCashDialog.findViewById(R.id.ed_discount);
        final EditText cashAmt = (EditText) takeCashDialog.findViewById(R.id.ed_cash);
        cashAmt.setText(currencyFormatter.format(Float.parseFloat(currentObj.getDue_amount())));
        receiptAmt = Double.parseDouble(currentObj.getDue_amount());
        currentObj.setReceiptAmt(String.valueOf(receiptAmt));
        currentObj.setDiscount("0");
        cashAmt.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
                receiptAmt = 0;
                currentObj.setReceiptAmt("");
                currentObj.setDiscount("");
            }
        });
//			InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
//			imm.showSoftInput(cashAmt, InputMethodManager.SHOW_IMPLICIT);
        cashAmt.setOnEditorActionListener(new OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                if (event == null) {
                    if (actionId == EditorInfo.IME_ACTION_DONE) {
                        InputMethodManager imm = (InputMethodManager) getSystemService(INPUT_METHOD_SERVICE);
                        imm.hideSoftInputFromWindow(cashAmt.getWindowToken(), 0);
                        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        String amt = cashAmt.getText().toString();
                        if (amt.length() > 0 && !amt.equalsIgnoreCase(".")) {
                            receiptAmt = Double.parseDouble(amt);
                            if (receiptAmt <= Double.parseDouble(currentObj.getDue_amount())) {
                                currentObj.setReceiptAmt(String.valueOf(receiptAmt));
                                if (receiptAmt < Double.parseDouble(currentObj.getDue_amount())) {
                                    discountParentLayout.setVisibility(View.VISIBLE);
                                } else {
                                    currentObj.setDiscount("0");
                                }
                            } else {
                                Utils.showToast(mContext, "Receipt amount cannot be greater than Due Amount.");
                            }
                        } else {
                            Utils.showToast(mContext, "Amount cannot be left blank.");
                        }

                        return true;
                    }
                }
                return false;
            }
        });
        RadioGroup discountOption = (RadioGroup) takeCashDialog.findViewById(R.id.rg_cust_options);
        discountOption.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                discountRadioChecked = true;
                int radioButtonID = group.getCheckedRadioButtonId();
                View radioButton = group.findViewById(radioButtonID);
                int selectedRadio = group.indexOfChild(radioButton);
                if (selectedRadio == 0) {
                    discountLayout.setVisibility(View.VISIBLE);
                } else {
                    discountLayout.setVisibility(View.GONE);
                }
            }
        });
        Button submit = (Button) takeCashDialog.findViewById(R.id.btn_submit);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                if (receiptAmt > 0) {
                    if (receiptAmt < Double.parseDouble(currentObj.getDue_amount())) {
                        if (discountRadioChecked) {
                            if (discountLayout.getVisibility() == View.VISIBLE) {
                                String discount = discountAmt.getText().toString();
                                if(!Utils.isNumeric(discount) || Double.parseDouble(discount)<=0){
                                    Utils.showToast(mContext, "Please enter valid Discount");
                                    return;
                                }
                                if (Double.parseDouble(discount) > Double.parseDouble(currentObj.getDue_amount()) - receiptAmt) {
                                    Utils.showToast(mContext, "Please enter valid Discount");
                                } else {
                                    currentObj.setDiscount(discount);
                                    Constants.selectedOutstandingList.add(currentObj);
                                    receiptAmt = 0;
                                    discountRadioChecked = false;
                                    adapterTrans.notifyDataSetChanged();
                                    transLayout.setVisibility(View.VISIBLE);
                                    takeCashDialog.cancel();
                                }
                            } else {
                                currentObj.setDiscount("0");
                                Constants.selectedOutstandingList.add(currentObj);
                                receiptAmt = 0;
                                discountRadioChecked = false;
                                adapterTrans.notifyDataSetChanged();
                                transLayout.setVisibility(View.VISIBLE);
                                takeCashDialog.cancel();
                            }
                        } else {
                            Utils.showToast(mContext, "Please select a Discount Option");
                        }
                    } else if (receiptAmt == Double.parseDouble(currentObj.getDue_amount())) {
                        currentObj.setDiscount("0");
                        Constants.selectedOutstandingList.add(currentObj);
                        receiptAmt = 0;
                        discountRadioChecked = false;
                        adapterTrans.notifyDataSetChanged();
                        transLayout.setVisibility(View.VISIBLE);
                        takeCashDialog.cancel();
                    }
                } else {
                    Utils.showToast(mContext, "Receipt Amt not set.Enter Amt and press Done Button");
                }
            }
        });
        Button cancel = (Button) takeCashDialog.findViewById(R.id.btn_cncl);
        if (true == isshow) {
            cancel.setVisibility(View.VISIBLE);
        } else {
            cancel.setVisibility(View.GONE);
        }
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                takeCashDialog.cancel();
            }
        });
        takeCashDialog.show();
    }

    public void showChooseInvoiceDialog() {
        removeRepeatedOutstandingItems();
        if (outstandingList.size() > 0) {
            invoiceDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
            invoiceDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            invoiceDialog.setContentView(R.layout.select_from_list);
            invoiceDialog.setCancelable(false);
            TextView title = (TextView) invoiceDialog.findViewById(R.id.title);
            title.setText("Please select an Invoice for Payment");
            ListView dialogList = (ListView) invoiceDialog.findViewById(R.id.list);
            OutstandingAdapter adapter1 = new OutstandingAdapter(CollectionActivity.this, R.layout.outstanding_list_child, outstandingList, false);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    invoiceDialog.cancel();
                    //selectedOutstandingObj = outstandingList.get(arg2);
                    OutstandingDetails currentObj = outstandingList.get(arg2);
                    showCashAmtDialog(currentObj, true);
                }
            });
            Button cancel = (Button) invoiceDialog.findViewById(R.id.btn_cncl);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    invoiceDialog.cancel();
                }
            });
            invoiceDialog.show();
        } else {
            Utils.showToast(mContext, "No Invoice is left for payment.");
        }
    }

    public void showTransEditDialog(final int position) {
        transEditDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
        transEditDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        transEditDialog.setContentView(R.layout.collection_edit_dialog);
        transEditDialog.setCancelable(false);
        TextView title = (TextView) transEditDialog.findViewById(R.id.title);
        title.setText("Select an option");
        Button edit = (Button) transEditDialog.findViewById(R.id.btn_edit);
        edit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                showCashAmtDialog(Constants.selectedOutstandingList.get(position), false);
                Constants.selectedOutstandingList.remove(Constants.selectedOutstandingList.get(position));
                transEditDialog.cancel();
            }
        });
        Button delete = (Button) transEditDialog.findViewById(R.id.btn_delete);
        delete.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                Constants.selectedOutstandingList.remove(Constants.selectedOutstandingList.get(position));
                outstandingList = setupDataHelperObj.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
                adapterTrans.notifyDataSetChanged();
                transEditDialog.cancel();
            }
        });
        transEditDialog.show();
    }

    public void showChooseCustomerDialog() {
        customerListDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.select_with_search);
        customerListDialog.setCancelable(false);
        TextView title = (TextView) customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = (EditText) customerListDialog.findViewById(R.id.autoCompleteTextView1);
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
                    reInitialiseCustomerList();
                }
                lastStr = str;
                filterCustomerArray(str.length(), str);
                adapterCust.notifyDataSetChanged();

                System.out.println("String::::::::" + str);
            }
        });
        ListView dialogList = (ListView) customerListDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                int pos=arg2;
                customerSelectionCOmmonnProcessForCollecrtion(pos);
            }
        });
        Button btnCancel = (Button) customerListDialog.findViewById(R.id.btn_ok);
        btnCancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                customerListDialog.cancel();
            }
        });
        customerListDialog.show();

    }

    public void customerSelectionCOmmonnProcessForCollecrtion(int pos) {
        CustomerDetails currentObj = tempCustomerList.get(pos);
        for (int ii = 0; ii < customerList.size(); ii++) {
            if (customerList.get(ii).getCustomerCode().equalsIgnoreCase(currentObj.getCustomerCode())) {
                chosenCustPos = ii;
            }
        }
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        customerListDialog.cancel();
        btnCust.setText(customerList.get(chosenCustPos).getCustomerName());
        btnCust.setEnabled(false);
        Constants.selectedCustomer = customerList.get(chosenCustPos);
        outstandingList = setupDataHelperObj.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
        double invoiceAmt = 0;
        double dueAmount = 0;
        if (outstandingList != null && outstandingList.size() > 0) {
            for (int ii = 0; ii < outstandingList.size(); ii++) {
                invoiceAmt = invoiceAmt + Double.parseDouble(outstandingList.get(ii).getInvoice_amount());
                dueAmount = dueAmount + Double.parseDouble(outstandingList.get(ii).getDue_amount());
            }
            invoiceLayout.setVisibility(View.VISIBLE);
            invAmt.setText("Total Invoice Amt :" + currencyFormatter.format(invoiceAmt));
            dueAmt.setText("Total Due Amt :" + currencyFormatter.format(dueAmount));
            invNumbr.setText("No of Invoice :" + outstandingList.size());
            btnDetails.setEnabled(true);
            btnNoCollc.setEnabled(true);
        }
        if (onAccountType) {
            showOnAccountTypeDialog();
        }
    }

    public void showNoCollcDialog() {
        noOrderDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.user_instruction_dialog);
        noOrderDialog.setCancelable(false);
        //noOrderDialog.setTitle("Please state the reason for no Order");
        TextView title = (TextView) noOrderDialog.findViewById(R.id.title);
        title.setText("Please state the reason for no Payment");
        final EditText edReason = (EditText) noOrderDialog.findViewById(R.id.ed_input);
        Button submit = (Button) noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                noOrderDialog.cancel();
                GPSTracker gpstracker = new GPSTracker(mContext);
                String reason = "";
                reason = edReason.getText().toString();
                String timeStamp = Constants.dateString + new SimpleDateFormat("_HHmmss").format(Calendar.getInstance().getTime());
                timeStamp = timeStamp.replace("_", "");
                Boolean isSuccessInsertToLocationTable, isSuccessInsertToPaymentHeaderForNoCollection;
                transDataHelperObj.beginTransaction();
                isSuccessInsertToPaymentHeaderForNoCollection = transDataHelperObj.insertToPaymentHeaderForNoCollection(reason, timeStamp);
                isSuccessInsertToLocationTable = transDataHelperObj.insertToLocationTable1("NC", timeStamp);
                if (gpstracker != null)
                    gpstracker.stopUsingGPS();
                if (isSuccessInsertToLocationTable && isSuccessInsertToPaymentHeaderForNoCollection) {
                    transDataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                    new TRANS_SubmitOrderTask(CollectionActivity.this, true).execute();
                } else {
                    btnNoCollc.setEnabled(true);
                    transDataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                    transDataHelperObj = new AceDnsTransactionDatabase(mContext);
                    Toast.makeText(mContext, "Sorry! memory related fatal exception found. Need to reenter data", Toast.LENGTH_LONG).show();
                }


            }
        });
        noOrderDialog.show();
    }


    public void reInitialiseCustomerList() {
        for (int kk = 0; kk < tempCustomerList.size(); kk++) {
            tempCustomerList.remove(tempCustomerList.get(kk));
        }
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
                //if(tempCashTransferList.get(ii).getCustomerName().substring(0,strCnt).equalsIgnoreCase(charVal)){
                if (tempCustomerList.get(ii).getCustomerName().toUpperCase().contains(charVal.toUpperCase())) {
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

    public void removeRepeatedOutstandingItems() {
        for (int kk = 0; kk < Constants.selectedOutstandingList.size(); kk++) {
            OutstandingDetails currentItem = Constants.selectedOutstandingList.get(kk);
            for (int x = 0; x < outstandingList.size(); x++) {
                if (outstandingList.get(x).getInvoice_id().equalsIgnoreCase(currentItem.getInvoice_id())) {
                    outstandingList.remove(outstandingList.get(x));
                }
            }
        }
    }


    public void showPaymentTypeDialog() {
        isMultipleDistPrimary=false;
        final String currentDate = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
        final Dialog payTypeDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
        payTypeDialog.setCancelable(false);
        payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        payTypeDialog.setContentView(R.layout.paytype_dialog);
        TextView txtMsg = (TextView) payTypeDialog.findViewById(R.id.title);
        txtMsg.setText("Select payment type");

        RadioGroup payTypeOption = (RadioGroup) payTypeDialog.findViewById(R.id.rg_pay_options);
        payTypeOption.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                int radioButtonID = group.getCheckedRadioButtonId();
                View radioButton = group.findViewById(radioButtonID);
                int selectedRadio = group.indexOfChild(radioButton);
                Boolean notCheckedOut = NotCheckedOut(mContext);
                if (selectedRadio == 0) {
                    onAccountType = false;
                    payTypeDialog.cancel();
                    if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                        ArrayList<RoutePlanMasterDetails> todayPlanList;

                        if (Constants.CurrentOrderCollectionTransactionType.matches("PC")) {
                            todayPlanList = transDataHelperObj.getPlanForTodayForCollection(currentDate, false);
                        } else//if(Constants.CurrentOrderCollectionTransactionType.matches("TC"))
                        {
                            todayPlanList = transDataHelperObj.getPlanForTodayForCollection(currentDate, true);
                        }

                        if (todayPlanList.size() > 0) {
                            if (notCheckedOut && !Constants.CurrentOrderCollectionTransactionType.matches("TC")) {
                                int positionOfCurrentCheckedInRoute = getPositionOfCurrentCheckedInRouteInvioiceWise(true, mContext, todayPlanList, null);
                                if (positionOfCurrentCheckedInRoute < 0) {
                                    Utils.showToast(mContext, "Current checked in customer does not have any outstanding.");
                                    finish();
                                } else {
                                    RoutePlanMasterDetails detailsObj = todayPlanList.get(positionOfCurrentCheckedInRoute);
                                    customerList = setupDataHelperObj.getCustomerListByRouteWithOS(detailsObj.getRoutecode());
                                    if (customerList.size() > 0) {
                                        btnCust.setEnabled(false);
                                        int positionOfCurrentCheckedInCustomer = getPositionOfCurrentCheckedInCustomerInvoiceWise(mContext, customerList);
                                        if (positionOfCurrentCheckedInCustomer < 0) {
                                            Utils.showToast(mContext, "Current checked in customer does not have any outstanding.");
                                            finish();
                                        } else {
                                            CustomerDetails currentObj = customerList.get(positionOfCurrentCheckedInCustomer);
                                            for (int ii = 0; ii < customerList.size(); ii++) {
                                                if (customerList.get(ii).getCustomerCode().equalsIgnoreCase(currentObj.getCustomerCode())) {
                                                    chosenCustPos = ii;
                                                }
                                            }
                                            btnCust.setText(customerList.get(chosenCustPos).getCustomerName());
                                            btnCust.setEnabled(false);
                                            Constants.selectedCustomer = customerList.get(chosenCustPos);
                                            outstandingList = setupDataHelperObj.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
                                            double invoiceAmt = 0;
                                            double dueAmount = 0;
                                            if (outstandingList != null && outstandingList.size() > 0) {
                                                for (int ii = 0; ii < outstandingList.size(); ii++) {
                                                    invoiceAmt = invoiceAmt + Double.parseDouble(outstandingList.get(ii).getInvoice_amount());
                                                    dueAmount = dueAmount + Double.parseDouble(outstandingList.get(ii).getDue_amount());
                                                }
                                                invoiceLayout.setVisibility(View.VISIBLE);
                                                invAmt.setText("Total Invoice Amt :" + currencyFormatter.format(invoiceAmt));
                                                dueAmt.setText("Total Due Amt :" + currencyFormatter.format(dueAmount));
                                                invNumbr.setText("No of Invoice :" + outstandingList.size());
                                                btnDetails.setEnabled(true);
                                                btnNoCollc.setEnabled(true);
                                            }
                                        }

                                        //showInstrucDialog();
                                    } else {
                                        Utils.showToast(mContext, "Current checked in customer does not have any outstanding.");
                                        finish();
                                    }
                                }

                            } else {
                                // Check if RoutePlanMaster contains customer with Outstanding
                                showRoutePlanListDialog(todayPlanList);
                            }

                        } else {
                            Toast.makeText(mContext, "No Outstanding", Toast.LENGTH_LONG).show();
                            Handler mHandler = new Handler();
                            mHandler.postDelayed(new Runnable() {
                                @Override
                                public void run() {
                                    finish();
                                }
                            }, 2500);
                        }
                    } else {
                        if (notCheckedOut && !Constants.CurrentOrderCollectionTransactionType.matches("TC")) {
                            final ArrayList<RouteDetails> routeList = setupDataHelperObj.getRouteForCollection();
                            int positionOfCurrentCheckedInRoute = getPositionOfCurrentCheckedInRouteInvioiceWise(true, mContext, null, routeList);
                            if (positionOfCurrentCheckedInRoute < 0) {
                                Utils.showToast(mContext, "Current checked in customer does not have any outstanding.");
                                finish();
                            } else {
                                RouteDetails detailsObj = routeList.get(positionOfCurrentCheckedInRoute);
                                customerList = setupDataHelperObj.getCustomerListByRouteWithOS(detailsObj.getRouteCode());

                                if (customerList.size() > 0) {
                                    int positionOfCurrentCheckedInCustomer = getPositionOfCurrentCheckedInCustomerInvoiceWise(mContext, customerList);
                                    if (positionOfCurrentCheckedInCustomer < 0) {
                                        Utils.showToast(mContext, "Current checked in customer does not have any outstanding.");
                                        finish();
                                    } else {
                                        CustomerDetails currentObj = customerList.get(positionOfCurrentCheckedInCustomer);
                                        for (int ii = 0; ii < customerList.size(); ii++) {
                                            if (customerList.get(ii).getCustomerCode().equalsIgnoreCase(currentObj.getCustomerCode())) {
                                                chosenCustPos = ii;
                                            }
                                        }

                                        btnCust.setText(customerList.get(chosenCustPos).getCustomerName());
                                        btnCust.setEnabled(false);
                                        Constants.selectedCustomer = customerList.get(chosenCustPos);
                                        outstandingList = setupDataHelperObj.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
                                        double invoiceAmt = 0;
                                        double dueAmount = 0;
                                        if (outstandingList != null && outstandingList.size() > 0) {
                                            for (int ii = 0; ii < outstandingList.size(); ii++) {
                                                invoiceAmt = invoiceAmt + Double.parseDouble(outstandingList.get(ii).getInvoice_amount());
                                                dueAmount = dueAmount + Double.parseDouble(outstandingList.get(ii).getDue_amount());
                                            }
                                            invoiceLayout.setVisibility(View.VISIBLE);
                                            invAmt.setText("Total Invoice Amt :" + currencyFormatter.format(invoiceAmt));
                                            dueAmt.setText("Total Due Amt :" + currencyFormatter.format(dueAmount));
                                            invNumbr.setText("No of Invoice :" + outstandingList.size());
                                            btnDetails.setEnabled(true);
                                            btnNoCollc.setEnabled(true);
                                        }
                                        btnCust.setEnabled(false);
                                    }

                                } else {
                                    Utils.showToast(mContext, "Current checked in customer does not have any outstanding.");
                                }
                            }

                        } else {
                            showRouteListDialog();
                        }

                    }
                } else {
                    onAccountType = true;
                    payTypeDialog.cancel();
                    if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes"))
                    {

                        ArrayList<RoutePlanMasterDetails> todayPlanList=new ArrayList<>();

                        if (Constants.CurrentOrderCollectionTransactionType.matches("PC"))
                        {
                            RoutePlanDetails routeplandetails;
                            routeplandetails = setupDataHelperObj.getRoutePlanDetailsObj();
                            if(orderAuditType.equalsIgnoreCase("primary"))
                            {
                                if (routeplandetails.getDistributorRoutePlanning().equalsIgnoreCase("yes") || routeplandetails.getDistributorRoutePlanningMultiple().equalsIgnoreCase("yes"))
                                {
                                    isMultipleDistPrimary=true;
                                }
                                else
                                {
                                    todayPlanList = transDataHelperObj.getPlanForTodayPrimaryOrder(currentDate);
                                }

                            }
                            else
                            {
                                if (routeplandetails.getDistributorRoutePlanning().equalsIgnoreCase("yes") || routeplandetails.getDistributorRoutePlanningMultiple().equalsIgnoreCase("yes"))
                                {
                                    todayPlanList = transDataHelperObj.getPlanForTodayForMultipleDistributorWiseRoutePlan(currentDate);
                                }
                                else
                                {
                                    todayPlanList = transDataHelperObj.getPlanForTodayInCollection(currentDate);
                                }

                            }
                        }
                        else//if(Constants.CurrentOrderCollectionTransactionType.matches("TC"))
                        {
                            todayPlanList = transDataHelperObj.getPlanNotForToday(currentDate);
                        }
                        if (notCheckedOut && !Constants.CurrentOrderCollectionTransactionType.matches("TC"))
                        {
//                            RoutePlanMasterDetails detailsObj = todayPlanList.get(getPositionOfCurrentCheckedInRoute(true, mContext, todayPlanList, null));
//                            customerList = setupDataHelperObj.getCustomerListByRouteForCollection(detailsObj.getRoutecode());
//                            CustomerDetails currentObj = customerList.get(getPositionOfCurrentCheckedInCustomer(mContext, customerList));
//                            for (int ii = 0; ii < customerList.size(); ii++) {
//                                if (customerList.get(ii).getCustomerCode().equalsIgnoreCase(currentObj.getCustomerCode())) {
//                                    chosenCustPos = ii;
//                                }
//                            }
                            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                            Constants.selectedCustomer = setupDataHelperObj.getCustomerDetailsById(PreferenceData.getCheckInOutEmpCode(mContext));
                            btnCust.setText(Constants.selectedCustomer.getCustomerName());
                            btnCust.setEnabled(false);

//                            Constants.selectedCustomer = customerList.get(chosenCustPos);
                            outstandingList = setupDataHelperObj.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
                            double invoiceAmt = 0;
                            double dueAmount = 0;
                            if (outstandingList != null && outstandingList.size() > 0) {
                                for (int ii = 0; ii < outstandingList.size(); ii++) {
                                    invoiceAmt = invoiceAmt + Double.parseDouble(outstandingList.get(ii).getInvoice_amount());
                                    dueAmount = dueAmount + Double.parseDouble(outstandingList.get(ii).getDue_amount());
                                }
                                invoiceLayout.setVisibility(View.VISIBLE);
                                invAmt.setText("Total Invoice Amt :" + currencyFormatter.format(invoiceAmt));
                                dueAmt.setText("Total Due Amt :" + currencyFormatter.format(dueAmount));
                                invNumbr.setText("No of Invoice :" + outstandingList.size());
                                btnDetails.setEnabled(true);
                                btnNoCollc.setEnabled(true);
                            }
                            if (onAccountType) {
                                showOnAccountTypeDialog();
                            }
                        }
                        else
                        {
                            if(isMultipleDistPrimary)
                            {
                                collectionCommonProcessRouteSelectionDisstributor();
                            }
                            else
                            {
                                showRoutePlanListDialog(todayPlanList);
                            }

                        }

                    }
                    else
                    {
                        if (notCheckedOut && !Constants.menuDetailsObj.getindependent_check_in_out().equalsIgnoreCase("yes")  && !Constants.CurrentOrderCollectionTransactionType.matches("TC"))
                        {

                            Constants.selectedCustomer = setupDataHelperObj.getCustomerDetailsById(PreferenceData.getCheckInOutEmpCode(mContext));
                            btnCust.setText(Constants.selectedCustomer.getCustomerName());
                            btnCust.setEnabled(false);
//                            Constants.selectedCustomer = customerList.get(chosenCustPos);
                            outstandingList = setupDataHelperObj.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
                            double invoiceAmt = 0;
                            double dueAmount = 0;
                            if (outstandingList != null && outstandingList.size() > 0) {
                                for (int ii = 0; ii < outstandingList.size(); ii++) {
                                    invoiceAmt = invoiceAmt + Double.parseDouble(outstandingList.get(ii).getInvoice_amount());
                                    dueAmount = dueAmount + Double.parseDouble(outstandingList.get(ii).getDue_amount());
                                }
                                invoiceLayout.setVisibility(View.VISIBLE);
                                invAmt.setText("Total Invoice Amt :" + currencyFormatter.format(invoiceAmt));
                                dueAmt.setText("Total Due Amt :" + currencyFormatter.format(dueAmount));
                                invNumbr.setText("No of Invoice :" + outstandingList.size());
                                btnDetails.setEnabled(true);
                                btnNoCollc.setEnabled(true);
                            }
                            if (onAccountType) {
                                showOnAccountTypeDialog();
                            }
                        } else {
                            showRouteListDialog();
                        }

                    }
                }
            }
        });
        Button cancel = (Button) payTypeDialog.findViewById(R.id.btn_cancel);
        cancel.setVisibility(View.VISIBLE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                payTypeDialog.cancel();
            }
        });
        payTypeDialog.show();
    }

    public void showOnAccountTypeDialog() {
        final Dialog onAccountDialog = new Dialog(CollectionActivity.this, R.style.PauseDialog);
        onAccountDialog.setCancelable(false);
        onAccountDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        onAccountDialog.setContentView(R.layout.on_account_dialog);
        TextView txtMsg = (TextView) onAccountDialog.findViewById(R.id.title);
        txtMsg.setText("Provide the details");
        final EditText edAmt = (EditText) onAccountDialog.findViewById(R.id.ed_amount);
        //final EditText edDetails = (EditText)onAccountDialog.findViewById(R.id.ed_details);
        Button submit = (Button) onAccountDialog.findViewById(R.id.btn_submit);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                String amount = "";
                amount = edAmt.getText().toString();
                if (amount.length() > 0) {
                    onAccountDialog.cancel();
                    OutstandingDetails detailObj = new OutstandingDetails();
                    detailObj.setCustomerCode(Constants.selectedCustomer.getCustomerCode());
                    detailObj.setCustomerName(Constants.selectedCustomer.getCustomerName());
                    detailObj.setDate("");
                    detailObj.setDiscount("0.00");
                    detailObj.setDue_amount("0.00");
                    detailObj.setInvoice_amount("0.00");
                    detailObj.setInvoice_id("");
                    detailObj.setRecId("");
                    detailObj.setReceiptAmt(amount);
                    Constants.selectedOutstandingList.add(detailObj);
                    startActivity(new Intent(mContext, CollectionConfirmActivity.class));
                    finish();
                } else {
                    Utils.showToast(mContext, "Please provide a valid Amount");
                }
            }
        });
        onAccountDialog.show();
    }

}
