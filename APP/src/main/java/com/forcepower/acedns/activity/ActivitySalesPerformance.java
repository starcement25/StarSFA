package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SalesPerformanceAdapter;
import com.forcepower.acedns.bean.SalesPerformance;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Objects;

public class ActivitySalesPerformance extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static ListView mListView = null;

    private static String mType = "";
    public Context mContext;
    ProgressDialog mProgressDialogSalesPerformance;
    Handler mHandlerSalesPerformance;
    ArrayList<SalesPerformance> mSalesPerformanceList;
    ArrayList<SalesPerformance> mSalesPerformanceCustomerList;
    ArrayList<SalesPerformance> mSalesPerformanceProductGroupList;
    ArrayList<SalesPerformance> mSalesPerformanceProductList;
    SalesPerformanceAdapter mSalesPerformanceAdapter;
    private AceDnsDatabase mAceDnsDatabase;
    private String mEmployeeName = "";
    private String mEmployeeCode = "";
    private String mCustomerCode = "";
    private String mProductGroupCode = "";
    private String mMTDTotal = "";
    private String mYTDTotal = "";

    @SuppressLint("HandlerLeak")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sales_performance_employee);
        RegisterActivities.registerActivity(this);

        mContext = ActivitySalesPerformance.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        InitializeView();

        mHandlerSalesPerformance = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogSalesPerformance.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                ActivitySalesPerformance.this.runOnUiThread(() -> {
                    switch (jobToDo) {
                        case 1:
                            if (!mSalesPerformanceList.isEmpty()) {
                                if (mSalesPerformanceList.size() == 1) {
                                    mEmployeeCode = mSalesPerformanceList.get(0).getEmployeeCode();
                                    mEmployeeName = mSalesPerformanceList.get(0).getEmployeeName();
                                    mSalesPerformanceAdapter = new SalesPerformanceAdapter(mContext, R.layout.sales_performance_child, mSalesPerformanceList);
                                    mListView.setAdapter(mSalesPerformanceAdapter);
                                    ShowSalesPerformanceSelectionDialog();
                                } else {
                                    mSalesPerformanceAdapter = new SalesPerformanceAdapter(mContext, R.layout.sales_performance_child, mSalesPerformanceList);
                                    mListView.setAdapter(mSalesPerformanceAdapter);
                                }
                            } else {
                                Utils.showToast(mContext, "No record found");
                            }
                            break;
                        case 2:
                            if (!mSalesPerformanceCustomerList.isEmpty()) {
                                if (mSalesPerformanceCustomerList.size() == 1) {
                                    mCustomerCode = mSalesPerformanceCustomerList.get(0).getCustomerCode();
                                    mType = "P";
                                    PrepareSalesPerformanceData(3);
                                } else {
                                    mType = "C";
                                    ShowSalesPerformanceList(mType);
                                }
                            } else {
                                Utils.showToast(mContext, "No record found");
                            }
                            break;
                        case 3:
                            if (!mSalesPerformanceCustomerList.isEmpty()) {
                                ShowSalesPerformanceList(mType);
                            } else {
                                Utils.showToast(mContext, "No record found");
                            }
                            break;
                        case 4:
                            if (!mSalesPerformanceProductGroupList.isEmpty()) {
                                ShowSalesPerformanceProductGroupList();
                            } else {
                                Utils.showToast(mContext, "No record found");
                            }
                            break;
                        case 5:
                            if (!mSalesPerformanceProductList.isEmpty()) {
                                ShowSalesPerformanceProductList();
                            } else {
                                Utils.showToast(mContext, "No record found");
                            }
                            break;
                    }
                });
            }
        };

        PrepareSalesPerformanceData(1);

        mListView.setOnItemClickListener((parent, view, position, id) -> {
            if (mSalesPerformanceAdapter != null) {
                mEmployeeCode = Objects.requireNonNull(mSalesPerformanceAdapter.getItem(position)).getEmployeeCode();
                mEmployeeName = Objects.requireNonNull(mSalesPerformanceAdapter.getItem(position)).getEmployeeName();
                ShowSalesPerformanceSelectionDialog();
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);

        mButtonBack = findViewById(R.id.back);
        mButtonBack.setOnClickListener(ActivitySalesPerformance.this);
        mListView = findViewById(R.id.listsalesprformance);
    }

    public void onClick(View clkdView) {
        if (clkdView == mButtonBack) {
            finish();
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    public void PrepareSalesPerformanceData(final int task) {
        mProgressDialogSalesPerformance = new ProgressDialog(mContext);
        mProgressDialogSalesPerformance.setCancelable(false);
        mProgressDialogSalesPerformance.setMessage("Fetching data from database.\nPlease wait..");
        mProgressDialogSalesPerformance.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        mSalesPerformanceList = mAceDnsDatabase.GetSalesPerformanceList();
                        break;
                    case 2:
                        mSalesPerformanceCustomerList = mAceDnsDatabase.GetSalesPerformanceList(mEmployeeCode);
                        break;
                    case 3:
                        mSalesPerformanceCustomerList = mAceDnsDatabase.GetSalesPerformanceList(mEmployeeCode, mCustomerCode);
                        break;
                    case 4:
                        mSalesPerformanceProductGroupList = mAceDnsDatabase.GetSalesPerformanceProductGroupWiseList(mEmployeeCode);
                        break;
                    case 5:
                        mSalesPerformanceProductList = mAceDnsDatabase.GetSalesPerformanceProductWiseList(mEmployeeCode, mCustomerCode, mProductGroupCode);
                        break;
                }

                Message msg = mHandlerSalesPerformance.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerSalesPerformance.sendMessage(msg);
            }
        }.start();
    }

    public void ShowSalesPerformanceSelectionDialog() {
        final Dialog salesSelectionDialog = new Dialog(ActivitySalesPerformance.this, R.style.PauseDialog);
        salesSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        salesSelectionDialog.setContentView(R.layout.radio_sales_performance_selection_dialog);
        salesSelectionDialog.setCancelable(false);

        RadioGroup mRadioGroupSBT = salesSelectionDialog.findViewById(R.id.radioSelectSalesPerformance);

        mRadioGroupSBT.setOnCheckedChangeListener((group, checkedId) -> {
            final RadioButton radioSelection = salesSelectionDialog.findViewById(checkedId);
            if (radioSelection.getText().toString().equalsIgnoreCase("Customer")) {
                salesSelectionDialog.cancel();
                mType = "C";
                PrepareSalesPerformanceData(2);
            } else {
                mCustomerCode = "";
                salesSelectionDialog.cancel();
                PrepareSalesPerformanceData(4);
            }
        });

        salesSelectionDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowSalesPerformanceList(final String type) {
        TotalofMTDandYTD(mSalesPerformanceCustomerList);
        final Dialog mDialogPendingContractofCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogPendingContractofCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogPendingContractofCustomer.setContentView(R.layout.activity_sales_customer);
        mDialogPendingContractofCustomer.setCancelable(true);

        TextView textviewemployeename = mDialogPendingContractofCustomer.findViewById(R.id.textViewEmployeeName);
        TextView textviewvalue = mDialogPendingContractofCustomer.findViewById(R.id.textViewValueType);

        if (type.equalsIgnoreCase("C")) {
            textviewvalue.setText("Customer Name");
            textviewemployeename.setText("Sales Performance of\n" + mEmployeeName);
        } else {
            textviewemployeename.setText("Product group wise \nsales performance of\n" + mEmployeeName);
            textviewvalue.setText("Product Group Name");
        }

        TextView textviewmtdtotal = mDialogPendingContractofCustomer.findViewById(R.id.textViewMTDTotal);
        textviewmtdtotal.setText(mMTDTotal);

        TextView textviewytdtotal = mDialogPendingContractofCustomer.findViewById(R.id.textViewYTDTotal);
        textviewytdtotal.setText(mYTDTotal);

        Button buttonback = mDialogPendingContractofCustomer.findViewById(R.id.back);
        buttonback.setOnClickListener(v -> mDialogPendingContractofCustomer.cancel());

        ListView pendincontractproductList = mDialogPendingContractofCustomer.findViewById(R.id.listpendincontract);

        final SalesPerformanceAdapter salesPerformanceAdapter = new SalesPerformanceAdapter(mContext, R.layout.sales_performance_child, mSalesPerformanceCustomerList);
        pendincontractproductList.setAdapter(salesPerformanceAdapter);
        pendincontractproductList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            mDialogPendingContractofCustomer.cancel();
            if (type.equalsIgnoreCase("C")) {
                mCustomerCode = Objects.requireNonNull(salesPerformanceAdapter.getItem(pos)).getEmployeeCode();
                mType = "P";
                PrepareSalesPerformanceData(3);
            } else {
                mProductGroupCode = Objects.requireNonNull(salesPerformanceAdapter.getItem(pos)).getEmployeeName();
                PrepareSalesPerformanceData(5);
            }
        });


        EditText searchText = mDialogPendingContractofCustomer.findViewById(R.id.editTextSearch);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                salesPerformanceAdapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        mDialogPendingContractofCustomer.show();
    }

    public void TotalofMTDandYTD(ArrayList<SalesPerformance> salesPerformanceList) {
        if (salesPerformanceList != null && !salesPerformanceList.isEmpty()) {
            double mtd = 0;
            double ytd = 0;
            for (int count = 0; count < salesPerformanceList.size(); count++) {
                SalesPerformance obj = salesPerformanceList.get(count);
                if (!obj.getMTDSale().isEmpty()) {
                    mtd += Double.parseDouble(obj.getMTDSale());
                } else {
                    mtd += 0;
                }

                if (!obj.getYTDSale().isEmpty()) {
                    ytd += Double.parseDouble(obj.getYTDSale());
                } else {
                    ytd += 0;
                }
            }
            mMTDTotal = new DecimalFormat("0.00").format(mtd);
            mYTDTotal = new DecimalFormat("0.00").format(ytd);
        } else {
            mMTDTotal = "0.00";
            mYTDTotal = "0.00";
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowSalesPerformanceProductList() {
        TotalofMTDandYTD(mSalesPerformanceProductList);

        final Dialog mDialogPendingContractofCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogPendingContractofCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogPendingContractofCustomer.setContentView(R.layout.activity_sales_customer);
        mDialogPendingContractofCustomer.setCancelable(true);

        TextView textviewemployeename = mDialogPendingContractofCustomer.findViewById(R.id.textViewEmployeeName);
        textviewemployeename.setText(mEmployeeName);

        TextView textviewvalue = mDialogPendingContractofCustomer.findViewById(R.id.textViewValueType);
        textviewvalue.setText("Product Name");

        TextView textviewmtdtotal = mDialogPendingContractofCustomer.findViewById(R.id.textViewMTDTotal);
        textviewmtdtotal.setText(mMTDTotal);

        TextView textviewytdtotal = mDialogPendingContractofCustomer.findViewById(R.id.textViewYTDTotal);
        textviewytdtotal.setText(mYTDTotal);


        Button buttonback = mDialogPendingContractofCustomer.findViewById(R.id.back);
        buttonback.setOnClickListener(v -> mDialogPendingContractofCustomer.cancel());

        ListView pendincontractproductList = mDialogPendingContractofCustomer.findViewById(R.id.listpendincontract);

        final SalesPerformanceAdapter salesPerformanceAdapter = new SalesPerformanceAdapter(mContext, R.layout.sales_performance_child, mSalesPerformanceProductList);
        pendincontractproductList.setAdapter(salesPerformanceAdapter);
        pendincontractproductList.setOnItemClickListener((arg0, arg1, pos, arg3) -> mDialogPendingContractofCustomer.cancel());

        EditText searchText = mDialogPendingContractofCustomer.findViewById(R.id.editTextSearch);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                salesPerformanceAdapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        mDialogPendingContractofCustomer.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowSalesPerformanceProductGroupList() {
        TotalofMTDandYTD(mSalesPerformanceProductGroupList);

        final Dialog mDialogPendingContractofCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogPendingContractofCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogPendingContractofCustomer.setContentView(R.layout.activity_sales_customer);
        mDialogPendingContractofCustomer.setCancelable(true);

        TextView textviewemployeename = mDialogPendingContractofCustomer.findViewById(R.id.textViewEmployeeName);
        textviewemployeename.setText("Sales Performance of\n" + mEmployeeName);

        TextView textviewvalue = mDialogPendingContractofCustomer.findViewById(R.id.textViewValueType);
        textviewvalue.setText("Product Group Name");

        TextView textviewmtdtotal = mDialogPendingContractofCustomer.findViewById(R.id.textViewMTDTotal);
        textviewmtdtotal.setText(mMTDTotal);

        TextView textviewytdtotal = mDialogPendingContractofCustomer.findViewById(R.id.textViewYTDTotal);
        textviewytdtotal.setText(mYTDTotal);


        Button buttonback = mDialogPendingContractofCustomer.findViewById(R.id.back);
        buttonback.setOnClickListener(v -> mDialogPendingContractofCustomer.cancel());

        ListView pendincontractproductList = mDialogPendingContractofCustomer.findViewById(R.id.listpendincontract);

        final SalesPerformanceAdapter salesPerformanceAdapter = new SalesPerformanceAdapter(mContext, R.layout.sales_performance_child, mSalesPerformanceProductGroupList);
        pendincontractproductList.setAdapter(salesPerformanceAdapter);
        pendincontractproductList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            mProductGroupCode = Objects.requireNonNull(salesPerformanceAdapter.getItem(pos)).getEmployeeName();
            PrepareSalesPerformanceData(5);
            mDialogPendingContractofCustomer.cancel();
        });

        EditText searchText = mDialogPendingContractofCustomer.findViewById(R.id.editTextSearch);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                salesPerformanceAdapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        mDialogPendingContractofCustomer.show();
    }

}
