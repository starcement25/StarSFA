package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.RetailerStockOutAdapter;
import com.forcepower.acedns.adapter.RetailerStockOutIemiListAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRetailerStockOutTask;
import com.forcepower.acedns.bean.BillingInformationStockSummaryData;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.currentLat;
import static com.forcepower.acedns.constants.Constants.currentLong;
import static com.forcepower.acedns.constants.Constants.mOrderPriceValidationType;
import static com.forcepower.acedns.constants.Constants.retailerStockOutProductIemiListByCustomer;
import static com.forcepower.acedns.constants.Constants.retailerStockOutProductList;
import static com.forcepower.acedns.constants.Constants.retailerStockOutProductListByCustomer;
import static com.forcepower.acedns.constants.Constants.retailerStockOutProductListStockOut;
import static com.forcepower.acedns.constants.Constants.retailerappTransactionReason;


public class RetailerStockOutActivitySpecial extends AceDnsParentActivity {
    Context mContext;
    //    Spinner customerSpinner;
    AceDnsDatabase mAceDnsDatabase;
    RetailerStockOutAdapter RetailerStockOutAdapterObject;
    ListView dialogList;
    public static String selectedCustomerCode;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    Button btnBack;
    RetailerStockOutIemiListAdapter ProductMasterWithQtyInputAdapterObject;
    LocationTracker LocationTrackerObject;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_retailer_stock_out_activity_special);
        mContext = this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        LocationTrackerObject=new LocationTracker(mContext,"stock out special");
//        customerSpinner = (Spinner) findViewById(R.id.customerSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        retailerStockOutProductListStockOut = new ArrayList<>();
        retailerStockOutProductIemiListByCustomer = new ArrayList<>();

        btnBack = (Button) findViewById(R.id.back);
        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        Utils.headerFooterIconChangesForRetailerApp(mContext,false);
        getProductListForStockOut(0);
    }

    private void getProductListForStockOut(int i) {
        selectedCustomerCode = retailerStockOutProductList.get(i).getcustomerCode();
        retailerStockOutProductListByCustomer = mAceDnsDatabase.getproductListByCustomerCode(selectedCustomerCode);
        RetailerStockOutAdapterObject = new RetailerStockOutAdapter(mContext, R.layout.product_list_child, retailerStockOutProductListByCustomer);
        dialogList = (ListView) findViewById(R.id.prodListView);
        dialogList.setAdapter(RetailerStockOutAdapterObject);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                String productCode = retailerStockOutProductListByCustomer.get(i).getProductCode();
                String productName = retailerStockOutProductListByCustomer.get(i).getproductName();
                retailerStockOutProductIemiListByCustomer = mAceDnsDatabase.getproductIemiListByCustomerCodeProductCode(selectedCustomerCode, productCode);
                removeRepeatedProductItems();
                if (retailerStockOutProductIemiListByCustomer.size() > 0) {
                    showMasterListDialog(productName);
                } else {
                    Utils.showToast(mContext, "No items left in the selected product. Please choose another.");
                }

            }
        });
    }

    public void showMasterListDialog(String productName)
    {
         final ArrayList<BillingInformationStockSummaryData> retailerStockOutProductIemiListByCustomerStatic=new ArrayList<>(retailerStockOutProductIemiListByCustomer);

        ProductMasterWithQtyInputAdapterObject = new RetailerStockOutIemiListAdapter(mContext, R.layout.list_item__product_with_quantity_input_alternate_design, retailerStockOutProductIemiListByCustomer);

        Constants.retailerStockOutmasterDialog = new Dialog(mContext, R.style.PauseDialog);
        Constants.retailerStockOutmasterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        Constants.retailerStockOutmasterDialog.setContentView(R.layout.select_with_search);
        Constants.retailerStockOutmasterDialog.setCancelable(false);
        TextView title = (TextView) Constants.retailerStockOutmasterDialog.findViewById(R.id.title);
        title.setText("Choose IMEI For " + productName);
        EditText searchText = (EditText) Constants.retailerStockOutmasterDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.setInputType(InputType.TYPE_CLASS_NUMBER);
        searchText.setHint("Type here to search");
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2,
                                      int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s)
            {

                String searchText = s.toString();
                if (searchText.length()==0)
                {
                    retailerStockOutProductIemiListByCustomer=new ArrayList<BillingInformationStockSummaryData>(retailerStockOutProductIemiListByCustomerStatic);
                }
                else
                {
                    retailerStockOutProductIemiListByCustomer=new ArrayList<BillingInformationStockSummaryData>();
                    for(int i=0;i<retailerStockOutProductIemiListByCustomerStatic.size();i++)
                    {
                        BillingInformationStockSummaryData billingInformationStockSummaryDataItem = retailerStockOutProductIemiListByCustomerStatic.get(i);
                        if(billingInformationStockSummaryDataItem.getimei().contains(searchText))
                        {
                            retailerStockOutProductIemiListByCustomer.add(billingInformationStockSummaryDataItem);
                        }
                    }
                }
                ProductMasterWithQtyInputAdapterObject = new RetailerStockOutIemiListAdapter(mContext, R.layout.list_item__product_with_quantity_input_alternate_design, retailerStockOutProductIemiListByCustomer);
                dialogList.setAdapter(ProductMasterWithQtyInputAdapterObject);
            }
        });

        View list_header_item_planwise_input_screen = Constants.retailerStockOutmasterDialog.findViewById(R.id.list_header_item_planwise_input_screen);
        list_header_item_planwise_input_screen.setVisibility(View.VISIBLE);
        TextView etProdQty = (TextView) Constants.retailerStockOutmasterDialog.findViewById(R.id.etProdQty);
        TextView etAdd = (TextView) Constants.retailerStockOutmasterDialog.findViewById(R.id.etAdd);
        etAdd.setVisibility(View.VISIBLE);
        etProdQty.setVisibility(View.GONE);
        etAdd.setText("Add");
        TextView tv_last_month_purchase = (TextView) Constants.retailerStockOutmasterDialog.findViewById(R.id.tv_last_month_purchase);
        TextView list_details = (TextView) Constants.retailerStockOutmasterDialog.findViewById(R.id.list_details);
        TextView etProdRate = (TextView) Constants.retailerStockOutmasterDialog.findViewById(R.id.etProdRate);
        TextView tv_iemi = (TextView) Constants.retailerStockOutmasterDialog.findViewById(R.id.tv_iemi);
        TextView tv_order_plan = (TextView) Constants.retailerStockOutmasterDialog.findViewById(R.id.tv_order_plan);
        tv_last_month_purchase.setVisibility(View.GONE);
        list_details.setVisibility(View.GONE);
        etProdRate.setVisibility(View.GONE);
        tv_order_plan.setVisibility(View.GONE);
        tv_iemi.setVisibility(View.VISIBLE);

        ImageView ivSideImage = (ImageView) Constants.retailerStockOutmasterDialog.findViewById(R.id.imageView1);
        ivSideImage.setVisibility(View.GONE);
        ImageView image_cancel = (ImageView) Constants.retailerStockOutmasterDialog.findViewById(R.id.image_cancel);
        LinearLayout applogoLayout = (LinearLayout) Constants.retailerStockOutmasterDialog.findViewById(R.id.applogoLayout);
        applogoLayout.setVisibility(View.VISIBLE);
        image_cancel.setVisibility(View.VISIBLE);

        image_cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Constants.retailerStockOutmasterDialog.cancel();
            }
        });

        dialogList = (ListView) Constants.retailerStockOutmasterDialog.findViewById(R.id.list);

        dialogList.setAdapter(ProductMasterWithQtyInputAdapterObject);
        Button btnCancel = (Button) Constants.retailerStockOutmasterDialog.findViewById(R.id.btn_ok);
        btnCancel.setText("DONE");
        btnCancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Constants.retailerStockOutmasterDialog.cancel();
            }
        });
        Button btn_addToCart = (Button) Constants.retailerStockOutmasterDialog.findViewById(R.id.btn_addToCart);
        if (mOrderPriceValidationType.matches("DOPS")) {
            btn_addToCart.setVisibility(View.VISIBLE);
        }

        btn_addToCart.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

            }
        });

        Constants.retailerStockOutmasterDialog.show();

    }
    public void removeRepeatedProductItems() {
        if (Constants.retailerStockOutProductListStockOut != null) {
            for (int kk = 0; kk < Constants.retailerStockOutProductListStockOut.size(); kk++) {
                BillingInformationStockSummaryData currentItem = Constants.retailerStockOutProductListStockOut.get(kk);
                for (int x = 0; x < retailerStockOutProductIemiListByCustomer.size(); x++) {
                    if (retailerStockOutProductIemiListByCustomer.get(x).getimei()
                            .equalsIgnoreCase(currentItem.getimei())) {
                        retailerStockOutProductIemiListByCustomer.remove(retailerStockOutProductIemiListByCustomer.get(x));
                    }
                }
            }
        }
    }

    public void submitOrder(View v)
    {
        if (Constants.retailerStockOutProductListStockOut.size() > 0)
        {
            Constants.selectedCustomer=mAceDnsDatabase.getCustomerDetailsRetailerApp();
            String customerLat=Constants.selectedCustomer.getbase_latt();
            String customerLong=Constants.selectedCustomer.getbase_longi();
            float distance=Utils.linearDistanceBetweenTwoLatLong(customerLat,customerLong,currentLat,currentLong);
            if(Utils.isNumeric(Constants.orderFormDetailsObj.getGEO_fencing_variance()) && distance>Double.parseDouble(Constants.orderFormDetailsObj.getGEO_fencing_variance()))
            {
                Utils.showToast(mContext,"You are not under coverage area.");
                return;
            }

            mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            Boolean isSuccessInsertStockOutDetailsUpdateCustomerProductBilling, isSuccessInsertToLocationTable;
            mAceDnsTransactionDatabase.beginTransaction();
            isSuccessInsertStockOutDetailsUpdateCustomerProductBilling = mAceDnsTransactionDatabase.insertStockOutDetailsUpdateCustomerProductBilling(timeStamp,true);
            isSuccessInsertToLocationTable = mAceDnsTransactionDatabase.insertToLocationTable1("SO", timeStamp);
            if (isSuccessInsertStockOutDetailsUpdateCustomerProductBilling && isSuccessInsertToLocationTable) {
                mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    retailerappTransactionReason="sendDataAfterTransaction";
                    new TRANS_SubmitRetailerStockOutTask(mContext, true).execute();
                }
                else
                {
                    Utils.showToast(mContext, "Order saved Successfully but could not be sent to server due to poor connectivity.");
                    finish();
                }
            } else {
                mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                Utils.showToast(mContext, "Something went Wrong while storing data. Transaction failed. Please contact admin!");
            }

        }
        else {
            Utils.showToast(mContext, "Please add item in cart first.");
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
    public void onResume()
    {
        super.onResume();
        LocationTrackerObject.checkLocationUpdateSharing();
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
}
