package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitReplaceMentOrder;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.NewProductMasterAdapter;
import com.forcepower.acedns.adapter.SaudaRouteAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.OrderHeader;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class ActivityProductReplacement extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutCartDetails = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonNoReplaceMent = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSelectCustomer = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSelectProduct = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonAddtoCart = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonRemarks = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextQuantity = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewNoofOrder = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewTotalOrderAmount = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCustomerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewRouteName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewProductName = null;

    public boolean isProductSelected = false;
    public boolean isCustomerSelected = false;
    public boolean isQuantityValid = false;
    public Context mContext;
    RouteDetails mRouteDetails;
    ArrayList<ProductMasterDetails> mSelectedProductList;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<String> mSelectedProductCode;
    ProgressDialog ploader;
    Handler orderDataHandler;
    OrderHeader mOrderHeader;
    ProductMasterDetails mProductMasterDetails;
    private String mCustomerName = "";
    private String mRouteName = "";
    private String mRemarks = "";
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;

    @SuppressLint("HandlerLeak")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_product_replacement);
        RegisterActivities.registerActivity(this);

        Constants.isFromConfirmationActivity = false;

        mContext = ActivityProductReplacement.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        Constants.selectedProductMasterList = new ArrayList<>();
        mSelectedProductCode = new ArrayList<>();
        InitializeView();

        mButtonSelectProduct.setOnClickListener(v -> PrepareOrderData(1, ""));

        mButtonSelectCustomer.setOnClickListener(v -> ShowRoute());

        orderDataHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                ActivityProductReplacement.this.runOnUiThread(() -> {
                    switch (jobToDo) {
                        case 1:
                            ShowProductList();
                            break;
                        case 2:
                            new TRANS_SubmitReplaceMentOrder(mContext, true).execute();
                            break;
                    }
                });
            }
        };
    }

    @Override
    public void onResume() {
        super.onResume();
        if (mEditTextQuantity != null) {
            mEditTextQuantity.setText("");
        }

        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        mEditTextQuantity = findViewById(R.id.editTextQuantity);

        mEditTextQuantity.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        mEditTextQuantity.setImeOptions(EditorInfo.IME_ACTION_DONE);

        mImageViewHeaderLogo = findViewById(R.id.imagelogo);

        mLinearLayoutCartDetails = findViewById(R.id.customer_layout);
        mLinearLayoutCartDetails.setVisibility(View.INVISIBLE);

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        mTextViewTotalOrderAmount = findViewById(R.id.txt_total);
        mTextViewNoofOrder = findViewById(R.id.txt_order_count);
        mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());

        mTextViewCustomerName = findViewById(R.id.textViewCustomername);
        mTextViewRouteName = findViewById(R.id.textViewRouteName);
        mTextViewProductName = findViewById(R.id.textViewProductDetails);
        mTextViewProductName.setText("");

        mButtonAddtoCart = findViewById(R.id.buttonAddtoCart);
        mButtonAddtoCart.setOnClickListener(ActivityProductReplacement.this);
        mButtonAddtoCart.setEnabled(false);

        mButtonRemarks = findViewById(R.id.buttonRemarks);
        mButtonRemarks.setOnClickListener(ActivityProductReplacement.this);
        mButtonRemarks.setEnabled(false);

        mButtonSubmit = findViewById(R.id.buttonSubmit);
        mButtonSubmit.setOnClickListener(ActivityProductReplacement.this);
        mButtonSubmit.setEnabled(false);

        mButtonNoReplaceMent = findViewById(R.id.no_ordr);
        mButtonNoReplaceMent.setOnClickListener(ActivityProductReplacement.this);
        mButtonNoReplaceMent.setVisibility(View.INVISIBLE);

        mButtonBack = findViewById(R.id.back);
        mButtonBack.setOnClickListener(ActivityProductReplacement.this);

        mButtonSelectCustomer = findViewById(R.id.buttonSelectCustomer);
        mButtonSelectProduct = findViewById(R.id.buttonSelectProduct);

    }

    public void WriteCustomerDetails(String customername, String routename) {
        mTextViewCustomerName.setText(customername);
        mTextViewRouteName.setText(routename);
        mButtonSelectCustomer.setEnabled(false);
        isCustomerSelected = true;
    }

    public void ShowRoute() {
        ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteList();
        if (routeList.size() == 1) {
            mRouteDetails = routeList.get(0);
            mRouteName = mRouteDetails.getRouteName();
            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mRouteDetails.getRouteCode());
            if (mCustomerDetailsList.size() > 1) {
                ShowCustomerListDialog();
            } else {
                if (mCustomerDetailsList.size() == 1) {
                    Constants.selectedCustomer = mCustomerDetailsList.get(0);
                    mCustomerName = Constants.selectedCustomer.getCustomerName();
                    WriteCustomerDetails(mCustomerName, mRouteName);
                } else {
                    Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_LONG).show();
                }
            }
        } else if (routeList.size() > 1) {
            ShowRouteListDialog(routeList);
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {
        final Dialog mDialogRoute = new Dialog(ActivityProductReplacement.this, R.style.PauseDialog);
        mDialogRoute.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogRoute.setContentView(R.layout.select_with_search);
        mDialogRoute.setCancelable(false);
        TextView title = mDialogRoute.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = mDialogRoute.findViewById(R.id.list);
        final SaudaRouteAdapter adapter = new SaudaRouteAdapter(ActivityProductReplacement.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);

        EditText searchText = mDialogRoute.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogRoute.cancel();
            mRouteDetails = adapter.getItem(arg2);
            assert mRouteDetails != null;
            mRouteName = mRouteDetails.getRouteName();
            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mRouteDetails.getRouteCode());
            if (mCustomerDetailsList.size() > 1) {
                ShowCustomerListDialog();
            } else {
                if (mCustomerDetailsList.size() == 1) {
                    Constants.selectedCustomer = mCustomerDetailsList.get(0);
                    mCustomerName = Constants.selectedCustomer.getCustomerName();
                    WriteCustomerDetails(mCustomerName, mRouteName);
                } else {
                    Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_LONG).show();
                }
            }
        });

        Button cancel = mDialogRoute.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mDialogRoute.show();
    }

    public void onClick(View clkdView) {
        if (clkdView == mButtonAddtoCart) {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            String mQuantity = mEditTextQuantity.getText().toString();
            if (!mQuantity.isEmpty()) {
                isQuantityValid = !mQuantity.equalsIgnoreCase("0") && !mQuantity.equalsIgnoreCase(".");
            } else {
                isQuantityValid = false;
            }

            if (isProductSelected && isCustomerSelected && isQuantityValid) {
                if (ReplacemnetProductDetails(mProductMasterDetails.getProdCode(), mQuantity)) {
                    RegreshReplaceProductActivity();
                    mSelectedProductCode.add(mProductMasterDetails.getProdCode());
                }
            } else {
                if (!isProductSelected) {
                    Toast.makeText(ActivityProductReplacement.this, "Please select a product", Toast.LENGTH_LONG).show();
                }
                if (!isCustomerSelected) {
                    Toast.makeText(ActivityProductReplacement.this, "Please select customer", Toast.LENGTH_LONG).show();
                }
                if (!isQuantityValid) {
                    Toast.makeText(ActivityProductReplacement.this, "Please provide valid quantity", Toast.LENGTH_LONG).show();
                }
            }
        }
        if (clkdView == mButtonSubmit) {
            if (!Constants.selectedProductMasterList.isEmpty()) {
                mLinearLayoutCartDetails.setVisibility(View.INVISIBLE);
                mButtonRemarks.setEnabled(false);
                PrepareOrderData(2, "DATABASE");
            } else {
                Toast.makeText(ActivityProductReplacement.this, "Please select a product", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == mButtonNoReplaceMent) {
            ShowNoReplacementDialog();
        }
        if (clkdView == mButtonBack) {
            finish();
        }
        if (clkdView == mButtonRemarks) {
            ShowRemarksDialog();
        }
    }

    @SuppressLint("SetTextI18n")
    public void RegreshReplaceProductActivity() {
        mLinearLayoutCartDetails.setVisibility(View.VISIBLE);
        mLinearLayoutCartDetails.setBackgroundColor(Color.YELLOW);
        mTextViewNoofOrder.setText("" + Constants.selectedProductMasterList.size());
        mTextViewTotalOrderAmount.setText("");
        mButtonSubmit.setEnabled(true);
        Toast.makeText(ActivityProductReplacement.this, "Product has been added to cart.", Toast.LENGTH_LONG).show();
        mEditTextQuantity.setText("");
        mTextViewProductName.setText("");
        isProductSelected = false;
        isQuantityValid = false;
        mButtonAddtoCart.setEnabled(false);
        mButtonRemarks.setEnabled(true);
        mButtonSubmit.setEnabled(true);
    }

    public boolean SaveReplacementOrderHeaderData(String customercode, String remarks, String transactiontype) {
        boolean issuccess;
        try {
            mOrderHeader = new OrderHeader();
            mOrderHeader.setOrderNo("");
            mOrderHeader.setCustomerCode(customercode);
            mOrderHeader.setTransferred(0);
            mOrderHeader.setSalesType("");
            mOrderHeader.setFlag(0);
            mOrderHeader.setInstruction(remarks);
            mOrderHeader.setTrdDiscnt("");
            mOrderHeader.setOrder_value("");
            mOrderHeader.setTag_distributor_code("");
            mOrderHeader.setTransaction_type(transactiontype);
            mOrderHeader.setVAT("");
            mOrderHeader.setBranchCode("");
            issuccess = true;
        } catch (Exception ex) {
            issuccess = false;
        }
        return issuccess;
    }

    @SuppressLint("SetTextI18n")
    public void ShowRemarksDialog() {
        final Dialog dialogRemarks = new Dialog(ActivityProductReplacement.this, R.style.PauseDialog);
        dialogRemarks.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogRemarks.setContentView(R.layout.user_instruction_dialog);
        dialogRemarks.setCancelable(false);
        TextView title = dialogRemarks.findViewById(R.id.title);
        title.setText("Please enter the remarks");
        final EditText edReason = dialogRemarks.findViewById(R.id.ed_input);
        edReason.setText(mRemarks);
        final Button submit = dialogRemarks.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            dialogRemarks.cancel();
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mRemarks = edReason.getText().toString();
        });
        dialogRemarks.show();
    }

    public void RemoveRepeatedProduct() {
        if (!mSelectedProductCode.isEmpty()) {
            String prodcode = "";
            for (int count = 0; count < mSelectedProductList.size(); count++) {
                prodcode = mSelectedProductList.get(count).getProdCode();
                for (int count2 = 0; count2 < mSelectedProductCode.size(); count2++) {
                    if (prodcode.equalsIgnoreCase(mSelectedProductCode.get(count2))) {
                        mSelectedProductList.remove(count);
                    }
                }
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowProductList() {
        if (!mSelectedProductList.isEmpty()) {
            final NewProductMasterAdapter productmasteradapter = new NewProductMasterAdapter(mContext, R.layout.product_list_child, mSelectedProductList);
            final Dialog productmasterDialog = new Dialog(ActivityProductReplacement.this, R.style.PauseDialog);
            productmasterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            productmasterDialog.setContentView(R.layout.select_with_search);
            productmasterDialog.setCancelable(false);
            TextView title = productmasterDialog.findViewById(R.id.title);
            title.setText("Please select a product");

            ListView dialogList = productmasterDialog.findViewById(R.id.list);
            dialogList.setAdapter(productmasteradapter);

            EditText searchText = productmasterDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    productmasteradapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mProductMasterDetails = productmasteradapter.getItem(arg2);
                assert mProductMasterDetails != null;
                mTextViewProductName.setText(mProductMasterDetails.getDesc());
                isProductSelected = true;
                mButtonAddtoCart.setEnabled(true);
                productmasterDialog.cancel();
            });
            Button cancel = productmasterDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            productmasterDialog.show();

        } else {
            Utils.showToast(mContext, "No products found");
        }
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void ShowNoReplacementDialog() {
        final Dialog dialogNoReplacement = new Dialog(ActivityProductReplacement.this, R.style.PauseDialog);
        dialogNoReplacement.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogNoReplacement.setContentView(R.layout.user_instruction_dialog);
        dialogNoReplacement.setCancelable(false);
        TextView title = dialogNoReplacement.findViewById(R.id.title);
        title.setText("Please state the reason for no replacement");
        final EditText edReason = dialogNoReplacement.findViewById(R.id.ed_input);
        final Button submit = dialogNoReplacement.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            dialogNoReplacement.cancel();
            String reason = "";
            reason = edReason.getText().toString();
            if (SaveReplacementOrderHeaderData(Constants.selectedCustomer.getCustomerCode(), reason, "NRP")) {
                String timeStamp = "";
                timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                mAceDnsTransactionDatabase.insertToOrderHeaderTable("NRP", timeStamp, mOrderHeader);
                mAceDnsTransactionDatabase.insertToLocationTable("NRP", timeStamp);
            } else {
                Toast.makeText(ActivityProductReplacement.this, "Error in Sauda data", Toast.LENGTH_LONG).show();
            }
        });
        dialogNoReplacement.show();
    }

    @SuppressLint("SimpleDateFormat")
    public void SaveReplacementDatatoDatabase() {
        if (SaveReplacementOrderHeaderData(Constants.selectedCustomer.getCustomerCode(), mRemarks, "RP")) {
            String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            mAceDnsTransactionDatabase.insertToOrderDetailsTable("RP", timeStamp);
            mAceDnsTransactionDatabase.insertToOrderHeaderTable("RP", timeStamp, mOrderHeader);
            mAceDnsTransactionDatabase.insertToLocationTable("RP", timeStamp);
        } else {
            Toast.makeText(ActivityProductReplacement.this, "Error in Sauda data", Toast.LENGTH_LONG).show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListDialog() {
        CustomerAdapter adapterCust = new CustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);

        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        ListView dialogList = mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            Constants.isSelectCustomer = true;
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogCustomer.cancel();
            Constants.selectedCustomer = mCustomerDetailsList.get(arg2);
            mCustomerName = Constants.selectedCustomer.getCustomerName();
            WriteCustomerDetails(mCustomerName, mRouteName);
        });

        Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();
    }

    public void PrepareOrderData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        if (!param.isEmpty()) {
            ploader.setMessage("Saving data to database. Data.Please wait..");
        } else {
            ploader.setMessage("Fetching Data.Please wait..");
        }
        ploader.setCancelable(false);
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        mSelectedProductList = mAceDnsDatabase.GetSelectedProductList();
                        RemoveRepeatedProduct();
                        break;
                    case 2:
                        SaveReplacementDatatoDatabase();
                        break;
                }
                Message msgObj = orderDataHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("WHAT TO SHOW", doWhat);
                msgObj.setData(b);
                orderDataHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public Boolean ReplacemnetProductDetails(String skucode, String quantity) {
        mProductMasterDetails = new ProductMasterDetails();
        mProductMasterDetails.setProdCode(skucode);
        mProductMasterDetails.setQty(quantity);
        mProductMasterDetails.setMrpCode("");
        mProductMasterDetails.setTD("");
        mProductMasterDetails.setMrpValue("");
        mProductMasterDetails.setVat("");
        mProductMasterDetails.setAmount("");
        Constants.selectedProductMasterList.add(mProductMasterDetails);
        return true;
    }
}
