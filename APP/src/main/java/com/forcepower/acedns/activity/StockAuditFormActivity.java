package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
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
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapterStockAudit;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_PendingRoutePlanBeforeOtherTxn;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitStockAuditTask;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
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

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Iterator;
import java.util.List;

import static android.view.View.VISIBLE;
import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;
import static com.forcepower.acedns.constants.Constants.mVerticalValue;
import static com.forcepower.acedns.constants.Constants.mVerticalValueList;
import static com.forcepower.acedns.constants.Constants.orderAuditType;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.selectedProductMasterList;
import static com.forcepower.acedns.constants.Constants.selectedProductMasterListStockAudit;
import static com.forcepower.acedns.constants.Constants.userDetailsObj;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInCustomer;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInRoute;

public class StockAuditFormActivity extends AceDnsParentActivity
{
    ProgressDialog ploader;
    public static ImageView mImageViewHeaderLogo = null;
    public static TextView mTextViewVisit1 = null;
    public static TextView mTextViewVisit2 = null;
    public static TextView mTextViewVisit3 = null;
    public static TextView mTextViewVisitUom = null;
    public static TextView mTextViewWeightage = null;
    Handler productDataHandler;
    boolean carryInSales = false;
    Button btnAddToCart, btnCheckOut, btnNoOrder, btnBack, btnCustomer;
    LinearLayout filterLayout, visitLayout, mLinearLayoutSaleRate;
    EditText edQty;
    EditText mEditTextMrp;

    Dialog grpDialog, subGrpDialog, brandDialog,
            masterDialog, noOrderDialog, infoDialog;
    ArrayList<BranchMasterDetails> mBranchMasterDetailsList;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    Activity activity;
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
            lastProductSelected = false,isMfdDateSelected=true;
    //	ProductMasterAdapter prodAdapter;
    RoutePlanTransAdapter RoutePlanAdapter;
    String lastStr = "";
    public static String mfgDate="";
    int lastProdPos = 0;
    List<String> hintRemarksValList;
    int selectedHintRemarksId = -1;
    ProductMasterWithQtyInputAdapterStockAudit ProductMasterWithQtyInputAdapterObject;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_stk_adt_frm);
        RegisterActivities.registerActivity(this);
        Constants.selectedCustomer=null;
        Constants.isFromConfirmationActivity = false;
        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        mContext = StockAuditFormActivity.this;
        activity = (Activity) mContext;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        selectedProductMasterList = new ArrayList<>();
        selectedProductMasterListStockAudit = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();

        filterButtonList = new ArrayList<>();
        prodQtyRateListView = (ListView) findViewById(R.id.prodQtyRateListView);
        InitializeView();
        if (userDetailsObj.getVerticalFields().equalsIgnoreCase("yes")) {
            mAceDnsDatabase.GetVerticalValue();
            LinearLayout Verticallayout = (LinearLayout) findViewById(R.id.Verticallayout);
            Verticallayout.setVisibility(VISIBLE);
            final Spinner verticalSpinner = (Spinner) findViewById(R.id.verticalSpinner);

            final ArrayList<String> verticalArray = new ArrayList<>(Arrays.asList(mVerticalValueList));
            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, verticalArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            verticalSpinner.setAdapter(spinnerArrayAdapter);
            int verticalIndexOfCurrentSelectedVertical = 0;
            verticalSpinner.setSelection(verticalIndexOfCurrentSelectedVertical);


            verticalSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                @Override
                public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                    mVerticalValue = verticalArray.get(i);
//                    drawFilterLayoutandProceedToRouteCustomerSelection();
                    DrawLayout();
                }

                @Override
                public void onNothingSelected(AdapterView<?> adapterView) {

                }
            });

        } else {
//            drawFilterLayoutandProceedToRouteCustomerSelection();
            DrawLayout();
        }

        mAceDnsDatabase.getSettingWeight();

        //Toast.makeText(mContext, ""+mAceDnsDatabase.getSettingWeight(), Toast.LENGTH_SHORT).show();
        productDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("get products");
                StockAuditFormActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:

//                                dialogList.setEmptyView(findViewById(R.id.empty_text_view));
                                prodQtyRateListView.setAdapter(ProductMasterWithQtyInputAdapterObject);
                                break;

                        }
                    }
                });
            }
        };
    }

    public void ChooseAuditType() {
        if (NotCheckedOut(mContext))
        {
            StockAuditCustomerListGenerationProcess();
//            Constants.selectedCustomer = mAceDnsDatabase.getCustomerDetailsById(PreferenceData.getCheckInOutEmpCode(mContext));
//            showSelectedCustomerDetails();
        }
        else if (!userDetailsObj.getstk_audit_cust_type().contains(","))//example D#customer_master
        {
            if (userDetailsObj.getstk_audit_cust_type().contains("#"))
            {
                String custType = Constants.userDetailsObj.getstk_audit_cust_type().split("#")[0];
                if (custType.equalsIgnoreCase("D") || custType.equalsIgnoreCase("Dealer")) {
                    orderAuditType = "Primary";
                } else//if(custType.equalsIgnoreCase("R"))
                {
                    orderAuditType = "Secondary";
                }
            }
            else {
                if (userDetailsObj.getstk_audit_cust_type().equalsIgnoreCase("D") || userDetailsObj.getstk_audit_cust_type().equalsIgnoreCase("Dealer")) {
                    orderAuditType = "Primary";
                } else//if(Constants.userDetailsObj.getstk_audit_cust_type().equalsIgnoreCase("R"))
                {
                    orderAuditType = "Secondary";
                }
            }
            StockAuditCustomerListGenerationProcess();
        } else//example D#customer_master,R#customer_master
        {
            final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
            dialgoCondition.setCancelable(false);
            dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
            dialgoCondition.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));

            TextView txtMsg = (TextView) dialgoCondition.findViewById(R.id.title);
            txtMsg.setText("Select Audit Type.");

            final RadioGroup radioSelectionGroup = (RadioGroup) dialgoCondition.findViewById(R.id.radioSelect);
            final RadioButton radioEdit = (RadioButton) dialgoCondition.findViewById(R.id.radioEdit);
            final RadioButton radioRedundant = (RadioButton) dialgoCondition.findViewById(R.id.radioRedundant);
            radioEdit.setText("Primary");
            radioRedundant.setText("Secondary");

            radioSelectionGroup
                    .setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                        public void onCheckedChanged(RadioGroup group, int checkedId) {
                            RadioButton radioSelection = (RadioButton) dialgoCondition.findViewById(checkedId);
                            if (radioSelection.getText().toString().trim().equalsIgnoreCase("Primary")) {
                                orderAuditType = "Primary";
                            } else {
                                orderAuditType = "Secondary";
                            }
                            dialgoCondition.cancel();

                            StockAuditCustomerListGenerationProcess();
                        }
                    });
            dialgoCondition.show();
        }

    }
    public void Clicked0(View v) {
        makeEditTextDataChangeProcess("0");
    }

    public void Clicked1(View v) {
        makeEditTextDataChangeProcess("1");
    }

    public void Clicked2(View v) {
        makeEditTextDataChangeProcess("2");
    }

    public void Clicked3(View v) {
        makeEditTextDataChangeProcess("3");
    }

    public void Clicked4(View v) {
        makeEditTextDataChangeProcess("4");
    }

    public void Clicked5(View v) {
        makeEditTextDataChangeProcess("5");
    }

    public void Clicked6(View v) {
        makeEditTextDataChangeProcess("6");
    }

    public void Clicked7(View v) {
        makeEditTextDataChangeProcess("7");
    }

    public void Clicked8(View v) {
        makeEditTextDataChangeProcess("8");
    }

    public void Clicked9(View v) {
        makeEditTextDataChangeProcess("9");
    }

    public void DOTClicked(View v) {
        makeEditTextDataChangeProcess("DOT");
    }
    private void makeEditTextDataChangeProcess(String currentInput) {
        try {
            if (qtySrate != null) {
                String existingInput = qtySrate.getText().toString().trim();
                if (!currentInput.matches("DOT")) {
                    if (existingInput.length() == 0) {
                        qtySrate.setText(currentInput);
                    } else {
                        qtySrate.setText(existingInput + currentInput);
                    }
                    qtySrate.setSelection(qtySrate.getText().length());
                } else  {
//					if(!isQtySelected)
//					{
                    if (existingInput.length() == 0) {
                        qtySrate.setText(".");
                        qtySrate.setSelection(qtySrate.getText().length());
                    } else {
                        if (!existingInput.contains(".")) {
                            qtySrate.setText(existingInput + ".");
                            qtySrate.setSelection(qtySrate.getText().length());
                        }
                    }

//					}


                }

            }
        } catch (Exception e) {

        }


    }

    public void DELClicked(View v) {
        try {
            if (qtySrate != null) {
                String existingInput = qtySrate.getText().toString().trim();
                if (existingInput.length() > 0) {
                    qtySrate.setText(existingInput.substring(0, existingInput.length() - 1));
                }
                qtySrate.setSelection(qtySrate.getText().length());
            }
        } catch (Exception e) {

        }

    }
    private void StockAuditCustomerListGenerationProcess() {
        String currentDate = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && userDetailsObj.getstk_audit_irrespective_routeplan().equalsIgnoreCase("no"))
        {
            RoutePlanDetails routeplandetails;
            routeplandetails = mAceDnsDatabase.getRoutePlanDetailsObj();
            ArrayList<RoutePlanMasterDetails> routePlanMasterDetails;
            if (routeplandetails.getDistributorRoutePlanning().equalsIgnoreCase("yes") || routeplandetails.getDistributorRoutePlanningMultiple().equalsIgnoreCase("yes"))
            {
                if(orderAuditType.equalsIgnoreCase("Primary"))
                {
                    mCustomerDetails = mAceDnsDatabase.GetDistributorCustomerListforRoutePlan(currentDate);
                    showSelectCustomerDialog();
                    return;
                }
                else
                {
                    routePlanMasterDetails = mAceDnsTransactionDatabase.getPlanForTodayForMultipleDistributorWiseRoutePlan(currentDate);
                }

            }
            else
            {
                routePlanMasterDetails = mAceDnsTransactionDatabase.getPlanForTodaysStockAudit(currentDate);
            }


            if (NotCheckedOut(mContext))
            {
                stockAuditProcessForCheckedInCustomer(routePlanMasterDetails);
            }

            else if (routePlanMasterDetails.size() == 1) {
                String todaysRouteListFormatted = formatRouteList(routePlanMasterDetails, null);
                getCustomerListAndShow(todaysRouteListFormatted);
            } else if (routePlanMasterDetails.size() > 1) {
                ShowTodayRoutePlanListDialog(routePlanMasterDetails);
            } else {

                finish();
            }
        } else {
            if (NotCheckedOut(mContext))
            {
//                final ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteListForStockAudit();
//                RouteDetails detailsObj = routeList.get(getPositionOfCurrentCheckedInRoute(true, mContext, null, routeList));
//                mCustomerDetails = mAceDnsDatabase.getCustomerListByRouteForStockAuditForCheckedInCustomer("'" + detailsObj.getRouteCode() + "'");
//                Constants.selectedCustomer = mCustomerDetails.get(getPositionOfCurrentCheckedInCustomer(mContext, mCustomerDetails));
                Constants.selectedCustomer = mAceDnsDatabase.getCustomerDetailsById(PreferenceData.getCheckInOutEmpCode(mContext));
                showSelectedCustomerDetails();
            }
            else {

                final ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteListForStockAudit();
                if (routeList.size() == 1) {
                    String todaysRouteListFormatted = formatRouteList(null, routeList);
                    getCustomerListAndShow(todaysRouteListFormatted);
                }
                if (routeList.size() > 1) {
                    ShowRouteListDialog(routeList);
                } else {

                }
            }

        }
    }

    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {

        final Dialog routeDialog = new Dialog(mContext, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(mContext, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routeDialog.cancel();
                final ArrayList<RouteDetails> routeListFinal = new ArrayList<>();
                routeListFinal.add(routeList.get(arg2));
                String todaysRouteListFormatted = formatRouteList(null, routeListFinal);
                getCustomerListAndShow(todaysRouteListFormatted);
            }
        });
        ImageView image_cancel = (ImageView) routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
                finish();
            }
        });
        Button cancel = (Button) routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
            }
        });
        Button create_route = (Button) routeDialog
                .findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    public void ShowTodayRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> routePlanListofToday) {
        final ArrayList<RoutePlanMasterDetails> mRoutePlanListofTodayForSearching = new ArrayList<>(routePlanListofToday);
        final Dialog routePlanListDialog = new Dialog(mContext, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
        RoutePlanAdapter = new RoutePlanTransAdapter(mContext, R.layout.route_list_child, routePlanListofToday);
        dialogList.setAdapter(RoutePlanAdapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routePlanListDialog.cancel();
                ArrayList<RoutePlanMasterDetails> routePlanMasterDetails = new ArrayList<>();
                routePlanMasterDetails.add(routePlanListofToday.get(arg2));
                String todaysRouteListFormatted = formatRouteList(routePlanMasterDetails, null);
                getCustomerListAndShow(todaysRouteListFormatted);

            }
        });
        ImageView image_cancel = (ImageView) routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanListDialog.cancel();
                finish();
            }
        });
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanListDialog.cancel();
            }
        });
        Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
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
                routePlanListofToday.clear();
                for (int i = 0; i < mRoutePlanListofTodayForSearching.size(); i++) {
                    String routeName = mRoutePlanListofTodayForSearching.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                    if (textLength <= routeName.length()) {
                        //compare the String in EditText with Names in the ArrayList
                        //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                        if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                            routePlanListofToday.add(mRoutePlanListofTodayForSearching.get(i));
                        }
                    }
                }
                RoutePlanAdapter.notifyDataSetChanged();

            }
        });
    }

    private void stockAuditProcessForCheckedInCustomer(ArrayList<RoutePlanMasterDetails> routePlanMasterDetails) {
        RoutePlanMasterDetails detailsObj = routePlanMasterDetails.get(getPositionOfCurrentCheckedInRoute(true, mContext, routePlanMasterDetails, null));
        mCustomerDetails = mAceDnsDatabase.getCustomerListByRouteForStockAuditForCheckedInCustomer("'" + detailsObj.getRoutecode() + "'");

        Constants.selectedCustomer = mCustomerDetails.get(getPositionOfCurrentCheckedInCustomer(mContext, mCustomerDetails));
        showSelectedCustomerDetails();
    }

    private void getCustomerListAndShow(String todaysRouteListFormatted) {
        mCustomerDetails = mAceDnsDatabase.getCustomerListByRouteForStockAuditSecondary(todaysRouteListFormatted);
        showSelectCustomerDialog();
    }

    private void showSelectCustomerDialog() {
        if (mCustomerDetails.size() > 0) {
            if (mCustomerDetails.size() > 1) {
                ShowChooseCustomerDialog();
            } else {
                Constants.selectedCustomer = mCustomerDetails.get(0);
                showSelectedCustomerDetails();

            }
        } else {
            Toast.makeText(mContext, "No customer found", Toast.LENGTH_LONG).show();
        }
    }

    private void showSelectedCustomerDetails() {
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
        if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes"))
        {
             mBranchMasterDetailsList = mAceDnsDatabase.getSaudaRDSListForOrder(Constants.selectedCustomer.getCustomerCode(), "");
            if (mBranchMasterDetailsList.size() > 1) {
                ShowBranchorDepoNameDialog();
            } else if (mBranchMasterDetailsList.size() == 1) {
                Constants.selectedBranch = mBranchMasterDetailsList.get(0);
            } else {
                Toast.makeText(mContext, "No branch/depot found.\n Please Synchronize Data",
                        2000).show();

            }
        }
    }
    public void ShowBranchorDepoNameDialog() {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title = (TextView) mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a Depot");
        ListView dialogList = (ListView) mDialogDepotName.findViewById(R.id.list);

        BranchAdapter branchadapter = new BranchAdapter(mContext,
                R.layout.route_list_child, mBranchMasterDetailsList);
        dialogList.setAdapter(branchadapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int arg2, long arg3) {
                mDialogDepotName.cancel();
                Constants.selectedBranch = mBranchMasterDetailsList.get(arg2);
            }
        });

        Button cancel = (Button) mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        mDialogDepotName.show();
    }

    private String formatRouteList(ArrayList<RoutePlanMasterDetails> routePlanMasterDetails, ArrayList<RouteDetails> routeList) {
        String formatRouteList = "";
        if (routePlanMasterDetails != null) {
            for (int i = 0; i < routePlanMasterDetails.size(); i++) {
                String routeCode = routePlanMasterDetails.get(i).getRoutecode();
                if (formatRouteList.matches("")) {
                    formatRouteList = "'" + routeCode + "'";
                } else {
                    formatRouteList = formatRouteList + "," + "'" + routeCode + "'";
                }
            }
        } else {
            for (int i = 0; i < routeList.size(); i++) {
                String routeCode = routeList.get(i).getRouteCode();
                if (formatRouteList.matches("")) {
                    formatRouteList = "'" + routeCode + "'";
                } else {
                    formatRouteList = formatRouteList + "," + "'" + routeCode + "'";
                }
            }
        }
        return formatRouteList;
    }

    @Override
    public void onResume() {
        super.onResume();

//        enableOrDisableFilters();
    }

    public void enableOrDisableFilters() {
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
        filterLayout = (LinearLayout) findViewById(R.id.filter_layout);
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


        mLinearLayoutSaleRate = (LinearLayout) findViewById(R.id.layoutsalerate);

        visitLayout = (LinearLayout) findViewById(R.id.visit_layout);
        mTextViewVisit1 = (TextView) findViewById(R.id.txt_visit1);
        mTextViewVisit2 = (TextView) findViewById(R.id.txt_visit2);
        mTextViewVisit3 = (TextView) findViewById(R.id.txt_visit3);

        mTextViewVisitUom = (TextView) findViewById(R.id.textViewUOM);

        btnAddToCart = (Button) findViewById(R.id.btn_continue);
        btnAddToCart.setEnabled(false);
        btnAddToCart.setOnClickListener(StockAuditFormActivity.this);
        btnAddToCart.setTag(101);
        btnCheckOut = (Button) findViewById(R.id.btn_order_form);
        btnCheckOut.setTag(102);
//        btnCheckOut.setEnabled(false);
        btnCheckOut.setOnClickListener(StockAuditFormActivity.this);
        btnNoOrder = (Button) findViewById(R.id.no_ordr);
        btnNoOrder.setTag(104);
        btnNoOrder.setOnClickListener(StockAuditFormActivity.this);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setTag(105);
        btnBack.setOnClickListener(StockAuditFormActivity.this);
        btnCustomer = (Button) findViewById(R.id.select_customer);
        btnCustomer.setTag(107);
        btnCustomer.setOnClickListener(StockAuditFormActivity.this);
        if (NotCheckedOut(mContext))
        {
            btnCustomer.setEnabled(false);
            Constants.selectedCustomer = mAceDnsDatabase.getCustomerDetailsById(PreferenceData.getCheckInOutEmpCode(mContext));
            showSelectedCustomerDetails();
        }
        mTextViewWeightage = findViewById(R.id.etWeightage);
        if(Constants.weightage.matches("yes")){
            mTextViewWeightage.setVisibility(VISIBLE);

        }else{
            mTextViewWeightage.setVisibility(View.GONE);
        }

    }

    public void drawFilterLayoutandProceedToRouteCustomerSelection() {
        if (filterLayout.getChildCount() > 0) {
            filterLayout.removeAllViews();
        }
        filterButtonList = new ArrayList<>();

        filterList = mAceDnsDatabase.getFilterList(); // filterList =
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
//				filterButton.setEnabled(false);
                buttonLayout.addView(filterButton);
                filterLayout.addView(buttonLayout);
                filterButtonList.add(filterButton);
            } else {
                filterButtonList.add(null);
            }
        }
        enableOrDisableFilters();
        ChooseAuditType();
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
//				getVerticalValue();
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
                        Toast.makeText(StockAuditFormActivity.this,
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
                        Toast.makeText(StockAuditFormActivity.this,
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
                                Toast.makeText(StockAuditFormActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                showMasterDialog(selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(StockAuditFormActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                showMasterDialog(selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(StockAuditFormActivity.this,
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

                if (userDetailsObj.getStockAuditRate().equalsIgnoreCase("yes")) {
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

                    if (userDetailsObj.getStockAuditRate().equalsIgnoreCase("yes")) {
                        mAceDnsDatabase.UpdateMRPDetails(currentProductMasterObj.getProdCode(), currentProductMasterObj.getMrpCode().trim());
                    }
                    edQty.setText("");
                    mEditTextMrp.setText("");

                    visitLayout.setVisibility(View.INVISIBLE);
                    selectedProductMasterList
                            .add(currentProductMasterObj);
                    Toast.makeText(StockAuditFormActivity.this,
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
                    Toast.makeText(StockAuditFormActivity.this,
                            "Please provide valid inputs.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(StockAuditFormActivity.this,
                        "Please select a product", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == btnCheckOut)
        {
            if(Constants.selectedCustomer!=null)
            {
                if (selectedProductMasterList != null && selectedProductMasterList.size() > 0)
                {
                    selectedProductMasterListStockAudit=new ArrayList<>(selectedProductMasterList);
                    startActivity(new Intent(StockAuditFormActivity.this, StockAuditConfirmActivity.class));
                }
                else
                {
                    Toast.makeText(StockAuditFormActivity.this, "Please add at least one product to cart.", Toast.LENGTH_LONG).show();
                }
            }
            else
            {
                Toast.makeText(mContext, "Please select a customer first.", Toast.LENGTH_LONG).show();
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
        if (clkdView == btnCustomer)
        {
            ChooseAuditType();

        }
    }
    public void DrawLayout() {
//		removeRepeatedGroupItems();
        if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) >= 2)
        {
            productGroupList = mAceDnsDatabase.getProductGroupList(carryInSales);
            showProductGroupSpinner();
        }
        else
        {
            getProductsShowOnList();
        }


    }
    public void showProductGroupSpinner() {
        final LinearLayout prod_grp_layout = findViewById(R.id.prod_grp_layout);
        prod_grp_layout.setVisibility(VISIBLE);
        final Spinner prodGrpSpinner = (Spinner) findViewById(R.id.prodGrpSpinner);
        prodGrpSpinner.setVisibility(VISIBLE);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < productGroupList.size(); i++) {
            spinnerArray.add(productGroupList.get(i).getGroupName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        prodGrpSpinner.setAdapter(spinnerArrayAdapter);
        prodGrpSpinner.setSelection(0);

        prodGrpSpinner.setOnItemSelectedListener(new AdapterView. OnItemSelectedListener()
        {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                selectedGrp = productGroupList.get(i);
                if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) == 2)
                {
                        getProductsShowOnList();
                }
                else if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) >= 3)
                {
                    showProductSubGroupSpinner();
                }

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    public void showProductSubGroupSpinner() {
        productSubGroupList = mAceDnsDatabase.getProductSubGroupList(selectedGrp.getGroupCode(), carryInSales);
        LinearLayout prod_sub_grp_layout = (LinearLayout) findViewById(R.id.prod_sub_grp_layout);
        prod_sub_grp_layout.setVisibility(VISIBLE);
        final Spinner prodSubGrpSpinner = (Spinner) findViewById(R.id.prodSubGrpSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < productSubGroupList.size(); i++) {
            spinnerArray.add(productSubGroupList.get(i).getSubGrpName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        prodSubGrpSpinner.setAdapter(spinnerArrayAdapter);
        prodSubGrpSpinner.setSelection(0);

        prodSubGrpSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
            {
                selectedSubGrp = productSubGroupList.get(i);
                if (Integer.parseInt(Constants.productDetailsObj.getNoFilter()) == 4)
                {
                    showProductBrandSpinner();
                }
                else
                {
                        getProductsShowOnList();
                }

            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    public void showProductBrandSpinner() {
        productBrandList = mAceDnsDatabase.getProductBrandList(selectedSubGrp.getSubGrpCode(), carryInSales);
//        productSubGroupList = mAceDnsDatabase.getProductSubGroupList(selectedGrp.getGroupCode(), carryInSales);
        LinearLayout prod_sub_grp_layout = (LinearLayout) findViewById(R.id.prod_brand_layout);
        prod_sub_grp_layout.setVisibility(VISIBLE);
        final Spinner prodBrandSpinner = (Spinner) findViewById(R.id.prodBrandSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < productBrandList.size(); i++) {
            spinnerArray.add(productBrandList.get(i).getBrandName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        prodBrandSpinner.setAdapter(spinnerArrayAdapter);
        prodBrandSpinner.setSelection(0);

        prodBrandSpinner.setOnItemSelectedListener(new AdapterView. OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                selectedBrand = productBrandList.get(i);
                    getProductsShowOnList();
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }



    public void getProductsShowOnList() {
        prepareProdData(1, "");
    }
    public void prepareProdData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        productMasterList = new ArrayList<>();
                        int filtersFromSetup = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
                        if (filtersFromSetup == 1)
                        {
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesignStock("");

                        }
                        else if (filtersFromSetup == 2 && selectedGrp != null)
                        {
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesignStock(selectedGrp.getGroupCode());
                        }
                        else if (filtersFromSetup == 3 && selectedSubGrp != null)
                        {
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesignStock(selectedSubGrp.getSubGrpCode());
                        }
                        else if (filtersFromSetup == 4 && selectedBrand != null)
                        {
                            productMasterList = mAceDnsDatabase.getProductMasterListAlternateDesignStock(selectedBrand.getBrandCode());
                        }
                        removeRepeatedProductItems();

                        Constants.tempProductList = new ArrayList<>();
                        reInitialiseProductList();
                        ProductMasterWithQtyInputAdapterObject = new ProductMasterWithQtyInputAdapterStockAudit(mContext, R.layout.list_item__product_with_quantity_input_alternate_design, Constants.tempProductList);
                        break;
                }
                Message msgObj = productDataHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("get products", doWhat);
                msgObj.setData(b);
                productDataHandler.sendMessage(msgObj);
            }
        }.start();
    }
    public void showInstructionWithHintDialog() {
        noOrderDialog = new Dialog(StockAuditFormActivity.this,
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
        final Dialog customerListDialog = new Dialog(StockAuditFormActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);

        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(StockAuditFormActivity.this, R.layout.customer_list_child, mCustomerDetails);
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
                showSelectedCustomerDetails();
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
        noOrderDialog = new Dialog(StockAuditFormActivity.this,
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
                if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && userDetailsObj.getstk_audit_irrespective_routeplan().equalsIgnoreCase("no")) {
                    boolean isExist = mAceDnsTransactionDatabase.IsUnuploadedRoutePlanExist();
                    if (true == isExist) {
                        new TRANS_PendingRoutePlanBeforeOtherTxn(StockAuditFormActivity.this, "STOCK").execute();
                    } else {
                        new TRANS_SubmitStockAuditTask(StockAuditFormActivity.this, true, "SUBMIT").execute();
                    }
                } else {
                    new TRANS_SubmitStockAuditTask(StockAuditFormActivity.this, true, "SUBMIT").execute();
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
//		removeRepeatedGroupItems();
        if (productGroupList.size() > 0) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            grpDialog = new Dialog(StockAuditFormActivity.this,
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
                    StockAuditFormActivity.this, R.layout.product_list_child,
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
            Toast.makeText(StockAuditFormActivity.this,
                    "There are no items left.Please submit transaction.", 2000)
                    .show();
        }
    }

    public void showSubGrpDialog(String parentItem) {
        productSubGroupList = mAceDnsDatabase.getProductSubGroupListStockAudit(parentItem, false);
//		removeRepeatedSubGroupItems();
        if (productSubGroupList.size() > 0) {
            if (productSubGroupList.size() == 1) {
                lastSubGroupSelected = true;
            }
            subGrpDialog = new Dialog(StockAuditFormActivity.this,
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
                    StockAuditFormActivity.this, R.layout.product_list_child,
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
                    StockAuditFormActivity.this,
                    "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    public void showBrandDialog(String parentItemId) {
        productBrandList = mAceDnsDatabase.getProductBrandListStockAudit(parentItemId,
                false);
//		removeRepeatedBrandItems();
        if (productBrandList.size() > 0) {
            if (productBrandList.size() == 1) {
                lastBrandSelected = true;
            }
            brandDialog = new Dialog(StockAuditFormActivity.this,
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
                    StockAuditFormActivity.this, R.layout.product_list_child,
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
                    StockAuditFormActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    public void showMasterDialog(String parentItem)
    {
        productMasterList = mAceDnsDatabase.getProductMasterListStockAudit(parentItem, filterNo, false);
        removeRepeatedProductItems();
        if (productMasterList.size() > 0) {
            if (productMasterList.size() == 1) {
                lastProductSelected = true;
                if (lastGrpSelected) {
                    btnAddToCart.setEnabled(false);
                }
            }
            Constants.tempProductList = new ArrayList<>();
            reInitialiseProductList();
            String productsUom="";
            if(Constants.tempProductList.size()>0)
            {
                productsUom=Constants.tempProductList.get(0).getUom1();
            }
            ProductMasterWithQtyInputAdapterObject = new ProductMasterWithQtyInputAdapterStockAudit(StockAuditFormActivity.this, R.layout.product_list_item_with_quantity_input_stock_audit, Constants.tempProductList);

        masterDialog = new Dialog(StockAuditFormActivity.this,
                R.style.PauseDialog);
        masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        masterDialog.setContentView(R.layout.select_with_search);
        masterDialog.setCancelable(false);
        TextView title = (TextView) masterDialog.findViewById(R.id.title);
        title.setText("Please provide quantity in "+ productsUom);

        View list_header_item_planwise_input_screen = masterDialog.findViewById(R.id.list_header_item_planwise_input_screen);

        if (userDetailsObj.getstk_audit_msl().equalsIgnoreCase("yes")) {
            View tv_msl = masterDialog.findViewById(R.id.tv_msl);
            tv_msl.setVisibility(View.VISIBLE);
        }
        list_header_item_planwise_input_screen.setVisibility(View.VISIBLE);
        TextView list_details = (TextView) masterDialog.findViewById(R.id.list_details);
        LinearLayout.LayoutParams params = (LinearLayout.LayoutParams) list_details.getLayoutParams();
        params.weight = 4f;
        list_details.setLayoutParams(params);
        TextView etProdQty = (TextView) masterDialog.findViewById(R.id.etProdQty);
        LinearLayout.LayoutParams params2 = (LinearLayout.LayoutParams) etProdQty.getLayoutParams();
        params2.weight = 1f;
        etProdQty.setLayoutParams(params2);

        etProdQty.setVisibility(View.VISIBLE);
        TextView tv_last_month_purchase = (TextView) masterDialog.findViewById(R.id.tv_last_month_purchase);
        TextView tv_order_plan = (TextView) masterDialog.findViewById(R.id.tv_order_plan);
        tv_last_month_purchase.setVisibility(View.GONE);
        tv_order_plan.setVisibility(View.GONE);
        TextView etProdRate = (TextView) masterDialog.findViewById(R.id.etProdRate);
        etProdRate.setVisibility(View.GONE);
        ImageView image_cancel = (ImageView) masterDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                masterDialog.cancel();
            }
        });
        EditText searchText = (EditText) masterDialog.findViewById(R.id.autoCompleteTextView1);
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
        prodQtyRateListView = (ListView) masterDialog.findViewById(R.id.list);
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
                currentProductMasterObj = Constants.tempProductList.get(arg2);
                filterButtonList.get(3).setText(currentProductMasterObj.getDesc());
                mTextViewVisitUom.setText(currentProductMasterObj.getUom1());

                if (userDetailsObj.getPreviousStock()
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
                for (int i = 0; i < selectedProductMasterList.size(); i++) {
                    String currentqty = selectedProductMasterList.get(i).getQty();
                    if (Utils.isNumeric(currentqty)) {
                        isValidQtyProvidedForAtLeastOneProduct = true;
                    }
                }
                if (isValidQtyProvidedForAtLeastOneProduct) {
                    removeItemsWithEmptyQty();
                    if (selectedProductMasterListStockAudit == null) {
                        selectedProductMasterListStockAudit = new ArrayList<>();
                    }
                    for (int i = 0; i < selectedProductMasterList.size(); i++) {
                        selectedProductMasterListStockAudit.add(selectedProductMasterList.get(i));
                    }
                    masterDialog.cancel();
                    startActivity(new Intent(StockAuditFormActivity.this, StockAuditConfirmActivity.class));
                } else {
                    Toast.makeText(mContext, "Please provide quantity for at least one product.", Toast.LENGTH_SHORT).show();
                }
            }
        });
        masterDialog.show();
    } else {
        Toast.makeText(
                StockAuditFormActivity.this,
                "There are no items left in this category. Please choose a different category",
                2000).show();
        filterButtonList.get(3).setText("");
    }
    }

    private void showMinimumStockIfAvailable() {
        String minimumStock = Constants.selectedCustomer.getMinimumStock();
        if (userDetailsObj.getMinimumStock().equalsIgnoreCase("yes") && Utils.isNumeric(minimumStock)) {
            LinearLayout min_stock_layout = (LinearLayout) findViewById(R.id.min_stock_layout);
            min_stock_layout.setVisibility(View.VISIBLE);
            TextView minimumStockTextView = (TextView) findViewById(R.id.minimumStockTextView);
            Constants.CurrentMinimumStockForCustomer = minimumStock;
            minimumStockTextView.setText(minimumStock + " " + userDetailsObj.getStockAuditUnit());
        }

    }

    /*
     * ::::::::::::::::::::::::::::::: REMOVING REPEATED ITEMS FROM SELECTION
     * LIST :::::::::::::::::::::::::::::::
     */

    public void removeRepeatedProductItems() {
        if (selectedProductMasterList != null) {
            for (int kk = 0; kk < selectedProductMasterList.size(); kk++) {
                ProductMasterDetails currentItem = selectedProductMasterList.get(kk);
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
        if (selectedProductMasterListStockAudit != null) {
            for (int kk = 0; kk < selectedProductMasterListStockAudit.size(); kk++) {
                ProductMasterDetails currentItem = selectedProductMasterListStockAudit.get(kk);
                for (int x = 0; x < productBrandList.size(); x++) {
                    if (productBrandList.get(x).getBrandCode().equalsIgnoreCase(currentItem.getBrndCode())) {
                        productBrandList.remove(productBrandList.get(x));
                    }
                }
            }
        }
    }

    public void removeRepeatedGroupItems() {
        if (selectedProductMasterListStockAudit != null) {
            for (int kk = 0; kk < selectedProductMasterListStockAudit.size(); kk++) {
                ProductMasterDetails currentItem = selectedProductMasterListStockAudit.get(kk);
                for (int x = 0; x < productGroupList.size(); x++) {
                    if (productGroupList.get(x).getGroupCode().equalsIgnoreCase(currentItem.getGrpCode())) {
                        productGroupList.remove(productGroupList.get(x));
                    }
                }
            }
        }
    }

    public void removeRepeatedSubGroupItems() {

        if (selectedProductMasterListStockAudit != null) {
            for (int kk = 0; kk < selectedProductMasterListStockAudit.size(); kk++) {
                ProductMasterDetails currentItem = selectedProductMasterListStockAudit.get(kk);
                for (int x = 0; x < productSubGroupList.size(); x++) {
                    if (productSubGroupList.get(x).getSubGrpCode().equalsIgnoreCase(currentItem.getSubGrpCode())) {
                        productSubGroupList.remove(productSubGroupList.get(x));
                    }
                }
            }
        }
    }

    public void filterProductArray(int strCnt, String charVal) {
        int size = Constants.tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (Constants.tempProductList.get(ii).getDesc().length() >= strCnt) {

                if (Constants.tempProductList.get(ii).getDesc().toUpperCase()
                        .contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    Constants.tempProductList.remove(Constants.tempProductList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                Constants.tempProductList.remove(Constants.tempProductList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void reInitialiseProductList() {
        Constants.tempProductList.removeAll(Constants.tempProductList);
        int size = Constants.tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            Constants.tempProductList.add(productMasterList.get(kk));
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
                Utils.showToast(StockAuditFormActivity.this,
                        "Last visit details are not available");
            }
        } else {
            Utils.showToast(StockAuditFormActivity.this,
                    "Last visit details are not available");
        }

    }

    private void removeItemsWithEmptyQty() {
        if (selectedProductMasterList != null) {
            for (Iterator<ProductMasterDetails> iterator = selectedProductMasterList.iterator(); iterator.hasNext(); ) {
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
