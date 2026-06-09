package com.forcepower.acedns.activity;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ProductStockOutRetailerAppAdapter;
import com.forcepower.acedns.adapter.RetailerStockOutIemiListAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRetailerStockOutTask;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.carttv;
import static com.forcepower.acedns.constants.Constants.qtySrate;
import static com.forcepower.acedns.constants.Constants.retailerStockOutProductListStockOut;
import static com.forcepower.acedns.constants.Constants.stockOutProdGroupRetailerApp;
import static com.forcepower.acedns.constants.Constants.tempProductList;


public class RetailerStockOutActivity extends AceDnsParentActivity {
    Context mContext;
    ArrayList<ProductMasterDetails> productMasterList;
    //    Spinner customerSpinner;
    AceDnsDatabase mAceDnsDatabase;
    ProductStockOutRetailerAppAdapter RetailerStockOutAdapterObject;
    ListView dialogList;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    Button btnBack;
    RetailerStockOutIemiListAdapter ProductMasterWithQtyInputAdapterObject;
    LocationTracker LocationTrackerObject;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_retailer_stock_out_activity);
        mContext = this;
        carttv = (TextView) findViewById(R.id.carttv);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        LocationTrackerObject=new LocationTracker(mContext,"stock out normal");
//        customerSpinner = (Spinner) findViewById(R.id.customerSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        retailerStockOutProductListStockOut = new ArrayList<>();
        Constants.selectedProductMasterList = new ArrayList<>();
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                finish();
            }
        });
        Button mButtonSubmit = (Button) findViewById(R.id.btn_order_form);
        mButtonSubmit.setText("Submit");
        mButtonSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                if (Constants.selectedProductMasterList.size() > 0)
                {
                    mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
                    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    Boolean isSuccessInsertStockOutDetailsUpdateCustomerProductBilling, isSuccessInsertToLocationTable;
                    mAceDnsTransactionDatabase.beginTransaction();
                    isSuccessInsertStockOutDetailsUpdateCustomerProductBilling = mAceDnsTransactionDatabase.insertStockOutDetailsUpdateCustomerProductBilling(timeStamp,false);
                    isSuccessInsertToLocationTable = mAceDnsTransactionDatabase.insertToLocationTable1("SO", timeStamp);
                    if (isSuccessInsertStockOutDetailsUpdateCustomerProductBilling && isSuccessInsertToLocationTable) {
                        mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
                        if (HTTPUtils.isConnectionPossible(mContext)) {
                            new TRANS_SubmitRetailerStockOutTask(mContext, true).execute();
                        } else {
                            Utils.showToast(mContext, "Order saved Successfully but could not be sent to server due to poor connectivity.");
                            finish();
                        }
                    } else {
                        mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
                        Utils.showToast(mContext, "Something went Wrong while storing data. Transaction failed. Please Synchronize Data!");
                    }
                }
                else
                {
                    Utils.showToast(mContext, "Please add item in cart first.");
                }

            }
        });
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        Utils.headerFooterIconChangesForRetailerApp(mContext,false);
        getProductListForStockOut();
    }
    public static void changeCartCount() {
        if (carttv != null && Constants.selectedProductMasterList != null) {
            carttv.setText(Constants.selectedProductMasterList.size() + "");
        }
    }
    private void getProductListForStockOut()
    {

        productMasterList = mAceDnsDatabase.getProductMasterListRetailerAppStockOut(stockOutProdGroupRetailerApp);
        tempProductList = new ArrayList<>();
        reInitialiseProductList();
        RetailerStockOutAdapterObject = new ProductStockOutRetailerAppAdapter(mContext, R.layout.product_list_child_retailer_stock_out, tempProductList);
        dialogList = (ListView) findViewById(R.id.prodListView);
        dialogList.setAdapter(RetailerStockOutAdapterObject);
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
                } else {
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
    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            tempProductList.add(productMasterList.get(kk));
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
