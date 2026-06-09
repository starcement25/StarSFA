package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapterRetailerStockIn;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRetailerStockInTask;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class RetailerStockInActivity extends AceDnsParentActivity {

    public static ImageView mImageViewHeaderLogo = null;
    public static TextView mTextViewVisitUom = null;
    Button btnCheckOut, btnBack, btnCustomer, dotButton;
    LinearLayout filterLayout, mLinearLayoutSaleRate;
    LocationTracker LocationTrackerObject;
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
    TextView textViewCustomerValue;
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
    ProductMasterWithQtyInputAdapterRetailerStockIn ProductMasterWithQtyInputAdapterObject;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_retailer_stock_in);
        RegisterActivities.registerActivity(this);

        Constants.isFromConfirmationActivity = false;
        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        mContext = RetailerStockInActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        LocationTrackerObject=new LocationTracker(mContext,"retailer stock in");
        Constants.selectedProductMasterList = new ArrayList<>();
        Constants.selectedProductMasterListStockAudit = new ArrayList<>();
        Constants.selectedGroupList = new ArrayList<>();
        Constants.selectedSubGroupList = new ArrayList<>();
        Constants.selectedBrandList = new ArrayList<>();
        filterButtonList = new ArrayList<>();
        InitializeView();
        textViewCustomerValue.setText("Customer :" + mAceDnsDatabase.getCustomerNameRetailer());
        drawFilterLayout();
        for (int i = 0; i < filterButtonList.size(); i++) {
            if (filterButtonList.get(i) != null) {
                filterButtonList.get(i).setEnabled(true);
            }
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        Utils.headerFooterIconChangesForRetailerApp(mContext,false);
        showMasterDialog("");
    }

    private void StockAuditCustomerListGenerationProcess() {
        String currentDate = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && Constants.userDetailsObj.getstk_audit_irrespective_routeplan().equalsIgnoreCase("no")) {
            ArrayList<RoutePlanMasterDetails> routePlanMasterDetails = mAceDnsTransactionDatabase.getPlanForTodaysStockAudit(currentDate);
            if (Utils.NotCheckedOut(mContext)) {
                stockAuditProcessForCheckedInCustomer(routePlanMasterDetails);

            } else if (routePlanMasterDetails.size() == 1) {
                String todaysRouteListFormatted = formatRouteList(routePlanMasterDetails, null);
                getCustomerListAndShow(todaysRouteListFormatted);
            } else if (routePlanMasterDetails.size() > 1) {
                ShowTodayRoutePlanListDialog(routePlanMasterDetails);
//				String todaysRouteListFormatted=formatRouteList(routePlanMasterDetails,null);
//				getCustomerListAndShow(todaysRouteListFormatted);
            } else {
                Utils.showToast(mContext, "No customer found.");
                finish();
            }
        } else {
            if (Utils.NotCheckedOut(mContext)) {
                final ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteListForStockAudit();
                RouteDetails detailsObj = routeList.get(Utils.getPositionOfCurrentCheckedInRoute(true, mContext, null, routeList));
                mCustomerDetails = mAceDnsDatabase.getCustomerListByRouteForStockAuditForCheckedInCustomer("'" + detailsObj.getRouteCode() + "'");
                Constants.selectedCustomer = mCustomerDetails.get(Utils.getPositionOfCurrentCheckedInCustomer(mContext, mCustomerDetails));
                btnCustomer.setText(Constants.selectedCustomer.getCustomerName());
                btnCustomer.setEnabled(false);
                ;
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
            } else {

                final ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteListForStockAudit();
                if (routeList.size() == 1) {
                    String todaysRouteListFormatted = formatRouteList(null, routeList);
                    getCustomerListAndShow(todaysRouteListFormatted);
                }
                if (routeList.size() > 1) {
                    ShowRouteListDialog(routeList);
                } else {
                    Utils.showToast(mContext, "No customer found.");
                }
            }

        }
    }

    protected void onActivityResult(int requestCode, int resultCode, Intent imageReturnedIntent)
    {
        super.onActivityResult(requestCode, resultCode, imageReturnedIntent);
        switch (requestCode)
        {
            case 9999:
                if (resultCode == RESULT_CANCELED)
                {
                    if(LocationTracker.transactionType.equalsIgnoreCase("attendance") || Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes"))
                    {
                        LocationTrackerObject.checkLocationUpdateSharing();
                    }

                }
                else if (resultCode == RESULT_OK)
                {
                    LocationTrackerObject.startLocationUpdates();
                }
                break;
        }
    }

    @Override
    public void onPause()
    {
        super.onPause();
        LocationTrackerObject.stopLocationUpdates();
    }

    @Override
    public void onStop()
    {
        super.onStop();
        LocationTrackerObject.stopLocationUpdates();
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
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
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
        image_cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
                finish();
            }
        });
        Button cancel = (Button) routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new View.OnClickListener() {
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
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
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
        image_cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanListDialog.cancel();
                finish();
            }
        });
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new View.OnClickListener() {
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
        RoutePlanMasterDetails detailsObj = routePlanMasterDetails.get(Utils.getPositionOfCurrentCheckedInRoute(true, mContext, routePlanMasterDetails, null));
        mCustomerDetails = mAceDnsDatabase.getCustomerListByRouteForStockAuditForCheckedInCustomer("'" + detailsObj.getRoutecode() + "'");

        Constants.selectedCustomer = mCustomerDetails.get(Utils.getPositionOfCurrentCheckedInCustomer(mContext, mCustomerDetails));
        btnCustomer.setText(Constants.selectedCustomer.getCustomerName());
        btnCustomer.setEnabled(false);
        for (int i = 0; i < filterButtonList.size(); i++) {
            if (filterButtonList.get(i) != null) {
                filterButtonList.get(i).setEnabled(true);
            }
        }
        btnCheckOut.setEnabled(true);

    }

    private void getCustomerListAndShow(String todaysRouteListFormatted) {
        mCustomerDetails = mAceDnsDatabase.getCustomerListByRouteForStockAudit(todaysRouteListFormatted);

        if (mCustomerDetails.size() > 0) {
            if (mCustomerDetails.size() > 1) {
                ShowChooseCustomerDialog();
            } else {
                Constants.selectedCustomer = mCustomerDetails.get(0);
                btnCustomer.setText(Constants.selectedCustomer.getCustomerName());
                btnCustomer.setEnabled(false);
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                btnCheckOut.setEnabled(true);

            }
        } else {
            Toast.makeText(mContext, "No customer found", Toast.LENGTH_LONG).show();
        }
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
        LocationTrackerObject.checkLocationUpdateSharing();
        if (Constants.isFromConfirmationActivity) {
            Constants.isFromConfirmationActivity = false;
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

        mTextViewVisitUom = (TextView) findViewById(R.id.textViewUOM);

        btnCheckOut = (Button) findViewById(R.id.btn_order_form);
        btnCheckOut.setText("Submit");
        btnCheckOut.setTag(102);
        btnCheckOut.setOnClickListener(RetailerStockInActivity.this);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setTag(105);
        btnBack.setOnClickListener(RetailerStockInActivity.this);

        dotButton = (Button) findViewById(R.id.dotButton);
        dotButton.setVisibility(View.GONE);
        btnCustomer = (Button) findViewById(R.id.select_customer);
        btnCustomer.setTag(107);
        btnCustomer.setOnClickListener(RetailerStockInActivity.this);

        textViewCustomerValue = (TextView) findViewById(R.id.textViewCustomerValue);
    }

    public void drawFilterLayout() {
        filterList = mAceDnsDatabase.getFilterList(); // filterList =
        // [4(filter_no),dadu,baba,NA,chhele]
        for (int ii = 1; ii < filterList.size(); ii++) {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(
                        LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(
                        LinearLayout.LayoutParams.MATCH_PARENT, LinearLayout.LayoutParams.WRAP_CONTENT);
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
                        Toast.makeText(RetailerStockInActivity.this,
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
                        Toast.makeText(RetailerStockInActivity.this,
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
                                Toast.makeText(RetailerStockInActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                showMasterDialog(selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(RetailerStockInActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                showMasterDialog(selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(RetailerStockInActivity.this,
                                        "Please select the parent category", 2000);
                            }
                            break;
                    }
            }
        }
        if (clkdView == btnCheckOut) {
//            submitProcess();
            addtocartProcess();
        }
        if (clkdView == btnBack) {
            finish();
        }
        if (clkdView == btnCustomer) {
            StockAuditCustomerListGenerationProcess();
        }
    }

    private void submitProcess() {
        if (Constants.selectedProductMasterList != null && Constants.selectedProductMasterList.size() > 0) {
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            Boolean isSuccessInsertRequisitionOutDetails, isSuccessInsertToLocationTable;
            mAceDnsTransactionDatabase.beginTransaction();
            isSuccessInsertRequisitionOutDetails = mAceDnsTransactionDatabase.insertRequisitionDetails(timeStamp);
            isSuccessInsertToLocationTable = mAceDnsTransactionDatabase.insertToLocationTable1("RD", timeStamp);
            if (isSuccessInsertRequisitionOutDetails && isSuccessInsertToLocationTable) {
                mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    new TRANS_SubmitRetailerStockInTask(mContext, true).execute();
                } else {
                    Utils.showToast(mContext, "Order saved Successfully but could not be sent to server due to poor connectivity.");
                    finish();
                }


            } else {
                mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                Utils.showToast(mContext, "Something went Wrong while storing data. Transaction failed. Please Synchronize Data!");
            }
        } else {
            Toast.makeText(RetailerStockInActivity.this, "Please add product in cart.", Toast.LENGTH_LONG).show();
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

    /*
     * :::::::::::::::::::::::::::::: CREATING DIFFERENT DIALOGs
     * :::::::::::::::::::::::::::::::::
     */
    public void ShowChooseCustomerDialog() {
        final Dialog customerListDialog = new Dialog(RetailerStockInActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);

        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(RetailerStockInActivity.this, R.layout.customer_list_child, mCustomerDetails);
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
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {

                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                customerListDialog.cancel();
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                btnCustomer.setText(Constants.selectedCustomer.getCustomerName());
                btnCustomer.setEnabled(false);
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                btnCheckOut.setEnabled(true);
            }
        });

        Button addCustomer = (Button) customerListDialog
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);

        customerListDialog.show();
    }


    public void showGrpDialog() {
        productGroupList = mAceDnsDatabase.getProductGroupListStockAudit(false);
        removeRepeatedGroupItems();
        if (productGroupList.size() > 0) {
            if (productGroupList.size() == 1) {
                lastGrpSelected = true;
            }
            grpDialog = new Dialog(RetailerStockInActivity.this,
                    R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_from_list);
            grpDialog.setTitle("Please select an option");
            grpDialog.setCancelable(true);
            ImageView image_cancel = (ImageView) grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    grpDialog.cancel();
                }
            });
            TextView title = (TextView) grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) grpDialog.findViewById(R.id.list);
            ProductGrpAdapter adapter1 = new ProductGrpAdapter(
                    RetailerStockInActivity.this, R.layout.product_list_child,
                    productGroupList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
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
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        } else {
            Toast.makeText(RetailerStockInActivity.this,
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
            subGrpDialog = new Dialog(RetailerStockInActivity.this,
                    R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_from_list);
            subGrpDialog.setCancelable(true);
            ImageView image_cancel = (ImageView) grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
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
                    RetailerStockInActivity.this, R.layout.product_list_child,
                    productSubGroupList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
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
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    subGrpDialog.cancel();
                }
            });
            subGrpDialog.show();
        } else {
            Toast.makeText(
                    RetailerStockInActivity.this,
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
            brandDialog = new Dialog(RetailerStockInActivity.this,
                    R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_from_list);
            brandDialog.setCancelable(true);
            ImageView image_cancel = (ImageView) grpDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(View.VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
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
                    RetailerStockInActivity.this, R.layout.product_list_child,
                    productBrandList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
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
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    brandDialog.cancel();
                }
            });
            brandDialog.show();
        } else {
            Toast.makeText(
                    RetailerStockInActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    public void showMasterDialog(String parentItem) {
        productMasterList = mAceDnsDatabase.getProductMasterListRetailserStockin("", filterNo);
        removeRepeatedProductItems();
        if (productMasterList.size() > 0) {
            if (productMasterList.size() == 1) {
                lastProductSelected = true;
            }
            tempProductList = new ArrayList<>();
            reInitialiseProductList();
            ProductMasterWithQtyInputAdapterObject = new ProductMasterWithQtyInputAdapterRetailerStockIn(RetailerStockInActivity.this, R.layout.product_list_item_with_quantity_input_stock_in, tempProductList);

            masterDialog = new Dialog(RetailerStockInActivity.this,
                    R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please provide quantity for products");

            View list_header_item_planwise_input_screen = masterDialog.findViewById(R.id.list_header_item_planwise_input_screen);

//            if(Constants.userDetailsObj.getstk_audit_msl().equalsIgnoreCase("yes"))
//            {
            TextView tv_msl = (TextView) masterDialog.findViewById(R.id.tv_msl);
            tv_msl.setVisibility(View.VISIBLE);
//            }
            tv_msl.setText("Alloc");
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
            image_cancel.setOnClickListener(new View.OnClickListener() {
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
            Constants.prodQtyRateListView = (ListView) masterDialog.findViewById(R.id.list);
            Constants.retailerRequisitionList = (ListView) findViewById(R.id.retailerRequisitionList);
//            dialogList.setAdapter(ProductMasterWithQtyInputAdapterObject);
            Constants.retailerRequisitionList.setAdapter(ProductMasterWithQtyInputAdapterObject);

//            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
//                @Override
//                public void onItemClick(AdapterView<?> arg0, View arg1,
//                                        int arg2, long arg3) {
//                    masterDialog.cancel();
//                    lastProdPos = arg2;
//                    currentProductMasterObj = tempProductList.get(arg2);
//                    filterButtonList.get(3).setText(currentProductMasterObj.getDesc());
//                    mTextViewVisitUom.setText(currentProductMasterObj.getUom1());
//
//                }
//            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setVisibility(View.GONE);

            Button btn_addToCart = (Button) masterDialog.findViewById(R.id.btn_addToCart);
            btn_addToCart.setVisibility(View.VISIBLE);
            btn_addToCart.setText("Add To Cart");
            btn_addToCart.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    addtocartProcess();
                }
            });
//            masterDialog.show();
        } else {
            Toast.makeText(
                    RetailerStockInActivity.this,
                    "There are no items left in this category. Please choose a different category",
                    2000).show();
            filterButtonList.get(3).setText("");
        }
    }

    private void addtocartProcess() {
        Boolean isValidNumericQtyProvidedForAllProducts = true;
        Boolean InputQuantityHigerthanAllocatedQuantity = false;
        String productForWhichInputQtyIsHigherThanAllocQty = "";
        for (int i = 0; i < Constants.selectedProductMasterList.size(); i++) {
            String currentqty = Constants.selectedProductMasterList.get(i).getQty();
            String allocqty = Constants.selectedProductMasterList.get(i).getallocation_qty();
            if (!Utils.isNumeric(currentqty)) {
                isValidNumericQtyProvidedForAllProducts = false;
                break;
            }
            if (!InputQuantityHigerthanAllocatedQuantity) {
                if (Utils.isNumeric(currentqty) && Double.parseDouble(currentqty) > 0) {
                    if (Double.parseDouble(currentqty) > Double.parseDouble(allocqty)) {
                        InputQuantityHigerthanAllocatedQuantity = true;
                        productForWhichInputQtyIsHigherThanAllocQty = Constants.selectedProductMasterList.get(i).getDesc();
                    }
                }
            }

        }
        if (isValidNumericQtyProvidedForAllProducts) {
            if (InputQuantityHigerthanAllocatedQuantity) {
                Toast.makeText(mContext, "Input quantity is higher than allocated quantity for " + productForWhichInputQtyIsHigherThanAllocQty, Toast.LENGTH_SHORT).show();
            } else {
                submitProcess();
            }

        } else {
            Toast.makeText(mContext, "Please provide quantity for all products even if it is zero.", Toast.LENGTH_SHORT).show();
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

    public void DELClicked(View v) {
        try {
            if (Constants.qtySrate != null) {
                String existingInput = Constants.qtySrate.getText().toString().trim();
                if (existingInput.length() > 0) {
                    Constants.qtySrate.setText(existingInput.substring(0, existingInput.length() - 1));
                }
                Constants.qtySrate.setSelection(Constants.qtySrate.getText().length());
            }
        } catch (Exception e) {

        }

    }

    private void makeEditTextDataChangeProcess(String currentInput) {
        try {
            if (Constants.qtySrate != null) {
                String existingInput = Constants.qtySrate.getText().toString().trim();
                if (!currentInput.matches("DOT")) {
                    if (existingInput.length() == 0) {
                        Constants.qtySrate.setText(currentInput);
                    } else {
                        Constants.qtySrate.setText(existingInput + currentInput);
                    }
                    Constants.qtySrate.setSelection(Constants.qtySrate.getText().length());
                } else {
//					if(!isQtySelected)
//					{
                    if (existingInput.length() == 0) {
                        Constants.qtySrate.setText(".");
                        Constants.qtySrate.setSelection(Constants.qtySrate.getText().length());
                    } else {
                        if (!existingInput.contains(".")) {
                            Constants.qtySrate.setText(existingInput + ".");
                            Constants.qtySrate.setSelection(Constants.qtySrate.getText().length());
                        }
                    }

//					}


                }

            }
        } catch (Exception e) {

        }


    }


}
