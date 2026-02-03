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
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.text.HtmlCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductMasterWithQtyInputAdapterOrderApproval;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderApprovalTask;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.TreeMap;

import static com.forcepower.acedns.constants.Constants.prodQtyRateListView;

public class OrderApprovalActivity extends AceDnsParentActivity {
    RadioGroup.OnCheckedChangeListener cancelChangeListener = null;
    RadioGroup.OnCheckedChangeListener modifyCheckChangedListener = null;
    ProgressDialog loader;
    Handler mHandler;
    EditText etReasonToCloseOthers;
    String currentItemReasonToCLose = "";
    Context mContext;
    public ImageView mImageViewHeaderLogo = null;
    public Button mButtonBack = null;
    public Button btn_Submi = null;
    public TextView orderByTv;
    public TextView orderDateTv;
    public TextView orderFreightTv;
    public TextView orderDestinationTv;
    public TextView orderPlantName;
    public TextView orderDumpTv;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    Spinner branchSpinner;
    Spinner customerSpinner;
    Spinner orderSpinner;
    ImageView editSku;
    ImageView editDest;
    ImageView editPlant;
    ImageView editDump;
    ImageView editOrderFor;
    public EditText orderForET = null;
    public EditText ConsigneeAddressET = null;
    public EditText etQty = null;
    public EditText etDump = null;
    public static String remarks = "", status = "", exFor = "";
    RadioGroup exForRG;
    RadioGroup statusRG;
    RadioGroup statusCancelRG;
    public AceDnsDatabase mAceDnsDatabase;
    public AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<BranchMasterDetails> mBranchDetailsList;
    TreeMap<String, ArrayList<commonDatabaseHelper>> OrderListForChosenCustomer;
    Boolean isCustomerChosen = false;
    public static ArrayList<commonDatabaseHelper> ChosenOrderList;
    ListView prodList;
    LinearLayout editableLayout;
    LinearLayout orderForLayout;
    LinearLayout orderByLayout, linearLayoutApproval;
    String chosenBranchCode = "";

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_approval);
        RegisterActivities.registerActivity(this);
        status = "";
        remarks = "";
        exFor = "";
        mContext = OrderApprovalActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        prodList =  findViewById(R.id.prodList);
        customerSpinner = findViewById(R.id.customerSpinner);
        branchSpinner = findViewById(R.id.branchSpinner);
        editSku = findViewById(R.id.editSku);
        editDump = findViewById(R.id.editDump);
        editOrderFor = findViewById(R.id.editOrderFor);
        orderSpinner = findViewById(R.id.orderSpinner);
        mButtonBack = findViewById(R.id.back);

        TextView txtVersion =  findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        mButtonBack.setOnClickListener(v -> finish());
        btn_Submi = findViewById(R.id.btn_Submi);
        btn_Submi.setOnClickListener(v -> saveTODb());

        editOrderFor.setOnClickListener(v -> {
            if (status.equalsIgnoreCase("modify")) {
                Dialog masterDialog;
                ArrayList<ProductMasterDetails> productMasterList = mAceDnsDatabase.getDumpMasterListForOrderApproval(Constants.selectedCustomer.getBranchCode());
                if (!productMasterList.isEmpty()) {
                    ArrayList<ProductMasterDetails> tempProductList = new ArrayList<>(productMasterList);
                    ProductMasterAdapter prodAdapter = new ProductMasterAdapter(mContext, R.layout.product_list_child_alternate, tempProductList);

                    masterDialog = new Dialog(mContext, R.style.PauseDialog);
                    masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                    masterDialog.setContentView(R.layout.select_with_search);
                    masterDialog.setCancelable(false);
                    ImageView imageView1 = masterDialog.findViewById(R.id.imageView1);
                    imageView1.setClickable(true);
                    imageView1.setImageResource(R.drawable.back_bg);
                    imageView1.setOnClickListener(view -> masterDialog.cancel());
                    TextView title =  masterDialog.findViewById(R.id.title);
                    title.setText("Please select a Dump");
                    EditText searchText =  masterDialog.findViewById(R.id.autoCompleteTextView1);
                    searchText.addTextChangedListener(new TextWatcher() {
                        @Override
                        public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                        @Override
                        public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                        @Override
                        public void afterTextChanged(Editable s) {
                            String str = s.toString();
                            tempProductList.removeAll(tempProductList);
                            int size = productMasterList.size();
                            for (int ii = 0; ii < size; ii++) {
                                if (productMasterList.get(ii).getDesc().length() > 1 && productMasterList.get(ii).getDesc().toUpperCase().contains(str.toUpperCase())) {
                                    tempProductList.add(productMasterList.get(ii));
                                }
                                prodAdapter.notifyDataSetChanged();
                            }
                        }
                    });
                    
                    prodQtyRateListView =  masterDialog.findViewById(R.id.list);
                    
                    prodQtyRateListView.setAdapter(prodAdapter);

                    prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                        String prodCode = tempProductList.get(arg2).getProdCode();
                        String prodDEsc = tempProductList.get(arg2).getDesc();
                        orderDumpTv.setText(prodDEsc);
                        for (int i = 0; i < ChosenOrderList.size(); i++) {
                            ChosenOrderList.get(i).setchangedDumpCode(prodCode);
                        }
                        masterDialog.cancel();
                    });
                    Button btnCancel =  masterDialog.findViewById(R.id.btn_ok);
                    btnCancel.setVisibility(View.GONE);
                    
                    masterDialog.show();
                } else {
                    Toast.makeText(mContext, "No Dump found.", Toast.LENGTH_LONG).show();
                }
            }
        });

        editDump.setOnClickListener(v -> {
            if (status.equalsIgnoreCase("modify")) {
                Dialog masterDialog;
                ArrayList<ProductMasterDetails> productMasterList = mAceDnsDatabase.getDumpMasterListForOrderApproval(Constants.selectedCustomer.getBranchCode());
                if (!productMasterList.isEmpty()) {
                    ArrayList<ProductMasterDetails> tempProductList = new ArrayList<>(productMasterList);
                    ProductMasterAdapter prodAdapter = new ProductMasterAdapter(mContext, R.layout.product_list_child_alternate, tempProductList);
                    masterDialog = new Dialog(mContext, R.style.PauseDialog);
                    masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                    masterDialog.setContentView(R.layout.select_with_search);
                    masterDialog.setCancelable(false);
                    ImageView imageView1 = masterDialog.findViewById(R.id.imageView1);
                    imageView1.setClickable(true);
                    imageView1.setImageResource(R.drawable.back_bg);
                    imageView1.setOnClickListener(view -> masterDialog.cancel());
                    TextView title =  masterDialog.findViewById(R.id.title);
                    title.setText("Please select a Dump");
                    EditText searchText =  masterDialog.findViewById(R.id.autoCompleteTextView1);
                    searchText.addTextChangedListener(new TextWatcher() {
                        @Override
                        public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                        @Override
                        public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                        @Override
                        public void afterTextChanged(Editable s) {
                            String str = s.toString();
                            tempProductList.removeAll(tempProductList);
                            int size = productMasterList.size();
                            for (int ii = 0; ii < size; ii++) {
                                if (productMasterList.get(ii).getDesc().length() > 1 && productMasterList.get(ii).getDesc().toUpperCase().contains(str.toUpperCase())) {
                                    tempProductList.add(productMasterList.get(ii));
                                }
                                prodAdapter.notifyDataSetChanged();
                            }
                        }
                    });
                    
                    prodQtyRateListView =  masterDialog.findViewById(R.id.list);
                    
                    prodQtyRateListView.setAdapter(prodAdapter);

                    prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                        String prodCode = tempProductList.get(arg2).getProdCode();
                        String prodDEsc = tempProductList.get(arg2).getDesc();
                        orderDumpTv.setText(prodDEsc);
                        for (int i = 0; i < ChosenOrderList.size(); i++) {
                            ChosenOrderList.get(i).setchangedDumpCode(prodCode);
                        }
                        masterDialog.cancel();
                    });
                    Button btnCancel =  masterDialog.findViewById(R.id.btn_ok);
                    btnCancel.setVisibility(View.GONE);
                    
                    masterDialog.show();
                } else {
                    Toast.makeText(mContext, "No Dump found.", Toast.LENGTH_LONG).show();
                }
            }
        });
        editDest = findViewById(R.id.editDest);
        editDest.setOnClickListener(v -> {
            if (exFor.equalsIgnoreCase("for") || exFor.equalsIgnoreCase("ex")) {
                Dialog masterDialog;
                ArrayList<ProductMasterDetails> productMasterList = mAceDnsDatabase.getDestinationMasterListForOrderApproval(exFor, Constants.selectedCustomer.getBranchCode());
                if (!productMasterList.isEmpty()) {
                    ArrayList<ProductMasterDetails> tempProductList = new ArrayList<>(productMasterList);
                    ProductMasterAdapter prodAdapter = new ProductMasterAdapter(mContext, R.layout.product_list_child_alternate, tempProductList);
                    masterDialog = new Dialog(mContext, R.style.PauseDialog);
                    masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                    masterDialog.setContentView(R.layout.select_with_search);
                    masterDialog.setCancelable(false);
                    ImageView imageView1 = masterDialog.findViewById(R.id.imageView1);
                    imageView1.setClickable(true);
                    imageView1.setImageResource(R.drawable.back_bg);
                    imageView1.setOnClickListener(view -> masterDialog.cancel());
                    TextView title =  masterDialog.findViewById(R.id.title);
                    title.setText("Please select a Destination");
                    EditText searchText =  masterDialog.findViewById(R.id.autoCompleteTextView1);
                    searchText.addTextChangedListener(new TextWatcher() {
                        @Override
                        public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                        @Override
                        public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                        @Override
                        public void afterTextChanged(Editable s) {
                            String str = s.toString();
                            tempProductList.removeAll(tempProductList);
                            int size = productMasterList.size();
                            for (int ii = 0; ii < size; ii++) {
                                if (productMasterList.get(ii).getDesc().length() > 1 && productMasterList.get(ii).getDesc().toUpperCase().contains(str.toUpperCase())) {
                                    tempProductList.add(productMasterList.get(ii));
                                }
                                prodAdapter.notifyDataSetChanged();
                            }
                        }
                    });
                    
                    prodQtyRateListView =  masterDialog.findViewById(R.id.list);
                    
                    prodQtyRateListView.setAdapter(prodAdapter);

                    prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                        String prodCode = tempProductList.get(arg2).getProdCode();
                        String prodDEsc = tempProductList.get(arg2).getDesc();
                        orderDestinationTv.setText(prodDEsc);
                        for (int i = 0; i < ChosenOrderList.size(); i++) {
                            ChosenOrderList.get(i).setchangedDnsDestinationCode(prodCode);
                        }
                        masterDialog.cancel();
                    });
                    Button btnCancel =  masterDialog.findViewById(R.id.btn_ok);
                    btnCancel.setVisibility(View.GONE);
                    masterDialog.show();
                } else {
                    Toast.makeText(mContext, "No Destination found.", Toast.LENGTH_LONG).show();
                }
            } else {
                Toast.makeText(mContext, "Please select EX or FOR first.", Toast.LENGTH_LONG).show();
            }
        });
        editPlant = findViewById(R.id.editPlant);
        editPlant.setOnClickListener(v -> {
            if (status.equalsIgnoreCase("modify")) {
                Dialog masterDialog;
                ArrayList<ProductMasterDetails> productMasterList = mAceDnsDatabase.getDumpMasterListForOrderApprovalPlantList(Constants.selectedCustomer.getBranchCode());
                ArrayList<ProductMasterDetails> tempProductList = new ArrayList<>(productMasterList);
                if (!productMasterList.isEmpty()) {
                    ProductMasterAdapter prodAdapter = new ProductMasterAdapter(mContext, R.layout.product_list_child_alternate, tempProductList);
                    masterDialog = new Dialog(mContext, R.style.PauseDialog);
                    masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                    masterDialog.setContentView(R.layout.select_with_search);
                    masterDialog.setCancelable(false);
                    TextView title =  masterDialog.findViewById(R.id.title);
                    ImageView imageView1 = masterDialog.findViewById(R.id.imageView1);
                    imageView1.setClickable(true);
                    imageView1.setImageResource(R.drawable.back_bg);
                    imageView1.setOnClickListener(view -> masterDialog.cancel());
                    title.setText("Please select a Plant");
                    EditText searchText =  masterDialog.findViewById(R.id.autoCompleteTextView1);
                    searchText.addTextChangedListener(new TextWatcher() {
                        @Override
                        public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                        @Override
                        public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                        @Override
                        public void afterTextChanged(Editable s) {
                            String str = s.toString();
                            tempProductList.removeAll(tempProductList);
                            int size = productMasterList.size();
                            for (int ii = 0; ii < size; ii++) {
                                if (productMasterList.get(ii).getDesc().length() > 1 && productMasterList.get(ii).getDesc().toUpperCase().contains(str.toUpperCase())) {
                                    tempProductList.add(productMasterList.get(ii));
                                }
                                prodAdapter.notifyDataSetChanged();
                            }
                        }
                    });

                    prodQtyRateListView =  masterDialog.findViewById(R.id.list);
                    
                    prodQtyRateListView.setAdapter(prodAdapter);

                    prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                        String prodCode = tempProductList.get(arg2).getProdCode();
                        String prodDEsc = tempProductList.get(arg2).getDesc();
                        orderPlantName.setText(prodDEsc);
                        for (int i = 0; i < ChosenOrderList.size(); i++) {
                            ChosenOrderList.get(i).setplantName(prodCode);
                        }
                        masterDialog.cancel();
                    });
                    Button btnCancel =  masterDialog.findViewById(R.id.btn_ok);
                    btnCancel.setVisibility(View.GONE);
                    masterDialog.show();
                } else {
                    Toast.makeText(mContext, "No Plant found.", Toast.LENGTH_LONG).show();
                }
            }
        });
        orderDateTv = findViewById(R.id.orderDateTv);
        orderFreightTv = findViewById(R.id.orderFreightTv);
        orderByTv = findViewById(R.id.orderByTv);
        orderPlantName = findViewById(R.id.orderPlantName);
        editableLayout = findViewById(R.id.editableLayout);
        orderDestinationTv = findViewById(R.id.orderDestinationTv);
        orderDumpTv = findViewById(R.id.orderDumpTv);
        orderForET = findViewById(R.id.orderForET);
        ConsigneeAddressET = findViewById(R.id.ConsigneeAddressET);
        orderForLayout = findViewById(R.id.orderForLayout);
        orderByLayout = findViewById(R.id.orderByLayout);
        linearLayoutApproval = findViewById(R.id.linearLayoutApproval);
        etQty = findViewById(R.id.etQty);
        etDump = findViewById(R.id.etDump);
        exForRG = findViewById(R.id.exForRG);
        exForRG.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection =  findViewById(checkedId);
            exFor = radioSelection.getText().toString();
            ProductMasterWithQtyInputAdapterOrderApproval ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterOrderApproval(mContext, R.layout.list_item_product_edit_order_approval);
            prodList.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);

        });
        modifyCheckChangedListener = (group, checkedId) -> {
            RadioButton radioSelection =  findViewById(checkedId);
            if (isCustomerChosen) {
                status = radioSelection.getText().toString();
                statusCancelRG.setOnCheckedChangeListener(null);
                statusCancelRG.clearCheck();
                if (cancelChangeListener != null)
                    statusCancelRG.setOnCheckedChangeListener(cancelChangeListener);
                if (status.equalsIgnoreCase("modify")) {
                    exForRG.setVisibility(View.VISIBLE);
                    editDest.setVisibility(View.VISIBLE);
                    editDump.setVisibility(View.VISIBLE);
                    editPlant.setVisibility(View.VISIBLE);
                    if (orderFreightTv.getText().toString().equalsIgnoreCase("ex")) {
                        exForRG.check(R.id.exRB);
                    } else if (orderFreightTv.getText().toString().equalsIgnoreCase("for")) {
                        exForRG.check(R.id.forRB);
                    }
                } else {
                    exForRG.setVisibility(View.GONE);
                    editDest.setVisibility(View.GONE);
                    editDump.setVisibility(View.GONE);
                    editPlant.setVisibility(View.GONE);
                }
                orderForOrderByOnOffProcess();
                ProductMasterWithQtyInputAdapterOrderApproval ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterOrderApproval(mContext, R.layout.list_item_product_edit_order_approval);
                prodList.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);

            } else {
                Utils.showToast(mContext, "Choose customer first.");
            }


        };
        cancelChangeListener = (group, checkedId) -> {
            RadioButton radioSelection =  findViewById(checkedId);
            if (isCustomerChosen) {
                statusRG.setOnCheckedChangeListener(null);
                statusRG.clearCheck();
                if (modifyCheckChangedListener != null)
                    statusRG.setOnCheckedChangeListener(modifyCheckChangedListener);
                status = radioSelection.getText().toString();
                orderForOrderByOnOffProcess();
                exForRG.setVisibility(View.GONE);
                editDest.setVisibility(View.GONE);
                editDump.setVisibility(View.GONE);
                editPlant.setVisibility(View.GONE);
                ProductMasterWithQtyInputAdapterOrderApproval ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterOrderApproval(mContext, R.layout.list_item_product_edit_order_approval);
                prodList.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
                ShowRemarksDateDialog();
            } else {
                Utils.showToast(mContext, "Choose customer first.");
            }


        };
        statusRG = findViewById(R.id.statusRG);
        statusCancelRG = findViewById(R.id.statusCancelRG);

        statusCancelRG.setOnCheckedChangeListener(cancelChangeListener);


        statusRG
                .setOnCheckedChangeListener(modifyCheckChangedListener);
        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                OrderApprovalActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                mBranchDetailsList = mAceDnsDatabase.GETBranchListOrderApproval();
                                final ArrayList<String> spinnerArray = new ArrayList<>();
                                for (int i = 0; i < mBranchDetailsList.size(); i++) {
                                    spinnerArray.add(mBranchDetailsList.get(i).getBranchName());
                                }
                                final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
                                spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                                branchSpinner.setAdapter(spinnerArrayAdapter);
                                if (!mBranchDetailsList.isEmpty())
                                    branchSpinner.setSelection(0);

                                branchSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                                    @Override
                                    public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                                        if (i == 0) {

                                        } else {
                                            chosenBranchCode = mBranchDetailsList.get(i).getBranchCode();
                                            getCustomerOrderList();
                                        }

                                    }

                                    @Override
                                    public void onNothingSelected(AdapterView<?> adapterView) {

                                    }
                                });

                                break;
                        }
                    }
                });
            }


        };
        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    OrderApprovalActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            new TRANS_SubmitOrderApprovalTask(mContext, true).execute();
                        }
                    });


                }
            }
        };
        try {
            if (!HTTPUtils.isConnectionPossible(mContext)) {
//                getCustomerOrderList();
                Utils.showToast(mContext, "You must have an active internet connection to use this feature.");
                finish();

            } else {
                PrepareCustomerData(1);
            }

        } catch (Exception e) {
            e.printStackTrace();
        }


        String appOrderApproval = mAceDnsTransactionDatabase.getAppOrderApprovalAdditional();

        if (appOrderApproval.matches("no")) {
            orderByLayout.setVisibility(View.GONE);
            linearLayoutApproval.setVisibility(View.GONE);

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

    public void saveTODb() {
        if (!HTTPUtils.isConnectionPossible(mContext)) {
            Utils.showToast(mContext, "You must have an active internet connection to submit.");
        } else {
            if (!status.equalsIgnoreCase("")) {
                if (status.equalsIgnoreCase("modify") || status.equalsIgnoreCase("authorize")) {
                    if (orderDestinationTv.getText().toString().length() < 2) {
                        Utils.showToast(mContext, "Destination can not be blank... ");
                        return;
                    }
                    if (orderDumpTv.getText().toString().length() < 2 && orderPlantName.getText().toString().length() < 2) {
                        Utils.showToast(mContext, "You must select at least anyone between dump and plant to proceed... ");
                        return;
                    }
                }


                new GPSTracker(mContext);
                loader = new ProgressDialog(mContext);
                loader.setMessage("Saving Data.Please wait..");
                loader.show();
                new Thread() {
                    public void run() {
                        String timeStamp = "";
                        timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        mAceDnsTransactionDatabase.updateOrderApprovalData(ChosenOrderList, timeStamp);
                        mAceDnsTransactionDatabase.insertToLocationTable("TA", timeStamp);
                        Message msgObj = mHandler.obtainMessage();
                        Bundle b = new Bundle();
                        b.putString("message", "SubmitJobDone");
                        msgObj.setData(b);
                        mHandler.sendMessage(msgObj);
                    }

                }.start();
            } else
                Utils.showToast(mContext, "Please select Authorize, Modify or Cancel before submitting");
        }

    }

    public void ShowRemarksDateDialog() {
        final Dialog mDialogOrderWithDate = new Dialog(mContext, R.style.PauseDialog);
        mDialogOrderWithDate.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOrderWithDate.setContentView(R.layout.dialog_order_cancel_reason);
        mDialogOrderWithDate.setCancelable(false);

        etReasonToCloseOthers =  mDialogOrderWithDate.findViewById(R.id.ed_input);
        final RadioGroup RGReasonToClose = (RadioGroup) mDialogOrderWithDate.findViewById(R.id.RGReasonToClose);
        RGReasonToClose.setOnCheckedChangeListener((RadioGroup.OnCheckedChangeListener) (group1, checkedId1) -> {

            RadioButton radioSelection =  mDialogOrderWithDate.findViewById(checkedId1);

            currentItemReasonToCLose = radioSelection.getText().toString();
            if (currentItemReasonToCLose.toLowerCase().contains("others")) {
                etReasonToCloseOthers.setVisibility(View.VISIBLE);
            } else {
                etReasonToCloseOthers.setVisibility(View.GONE);
            }
        });

        Button backButton =  mDialogOrderWithDate.findViewById(R.id.backButton);
        backButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {

                status = "";
                statusCancelRG.setOnCheckedChangeListener(null);
                statusCancelRG.clearCheck();
                if (cancelChangeListener != null) {
                    statusCancelRG.setOnCheckedChangeListener(cancelChangeListener);
                }
                mDialogOrderWithDate.dismiss();
            }
        });
        Button btn_submit =  mDialogOrderWithDate.findViewById(R.id.btn_submit);
        btn_submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (currentItemReasonToCLose.equalsIgnoreCase("Others(Text Typing)")) {
                    remarks = etReasonToCloseOthers.getText().toString().trim();
                    if (remarks.length() < 1) {
                        Utils.showToast(mContext, "Please type specific reason in the text field.");
                        return;
                    }
                } else {
                    remarks = currentItemReasonToCLose;
                }
                mDialogOrderWithDate.dismiss();
                saveTODb();
            }
        });

        mDialogOrderWithDate.show();
    }
    @SuppressLint("SetTextI18n")
    private void orderForOrderByOnOffProcess() {
        if (status.equalsIgnoreCase("modify")) {
            ConsigneeAddressET.setEnabled(true);
        } else {
            ConsigneeAddressET.setEnabled(false);
        }
        ConsigneeAddressET.setText(ChosenOrderList.get(0).getItem22());
        ConsigneeAddressET.addTextChangedListener(new TextWatcher() {

            public void afterTextChanged(Editable s) {

                String text = s.toString();
                for (int i = 0; i < ChosenOrderList.size(); i++) {
                    ChosenOrderList.get(i).setchangedConsigneeAddress(text);
                }
            }

            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            public void onTextChanged(CharSequence s, int start, int before, int count) {

            }
        });

        if (ChosenOrderList.get(0).getItem21().length() > 2) {
            orderByLayout.setVisibility(View.VISIBLE);
            orderByTv.setText(ChosenOrderList.get(0).getItem21());
        } else {
            orderByLayout.setVisibility(View.GONE);
        }
        if (ChosenOrderList.get(0).getItem3().length() < 2 && ChosenOrderList.get(0).getItem6().length() < 2) {
            orderForLayout.setVisibility(View.GONE);
        } else {
            orderForLayout.setVisibility(View.VISIBLE);
            if (ChosenOrderList.get(0).getItem3().length() > 2) {
                editOrderFor.setVisibility(View.GONE);
                orderForET.setText(ChosenOrderList.get(0).getItem3());
                orderForET.addTextChangedListener(new TextWatcher() {

                    public void afterTextChanged(Editable s) {

                        String text = s.toString();
                        for (int i = 0; i < ChosenOrderList.size(); i++) {
                            ChosenOrderList.get(i).setchangedOrderFor(text);
                        }
                    }

                    public void beforeTextChanged(CharSequence s, int start, int count, int after) {
                    }

                    public void onTextChanged(CharSequence s, int start, int before, int count) {

                    }
                });
                if (status.equalsIgnoreCase("modify")) {
                    orderForET.setEnabled(true);
//                ConsigneeAddressET.setEnabled(true);
                } else {
                    orderForET.setEnabled(false);
//                ConsigneeAddressET.setEnabled(false);
                }

            } else {
                CustomerDetails selectedSSOfCustomer = mAceDnsDatabase.getCustomerDetailsByCode(ChosenOrderList.get(0).getItem6());
                orderForET.setEnabled(false);
                orderForET.setText(selectedSSOfCustomer.getCustomerName());
                for (int i2 = 0; i2 < ChosenOrderList.size(); i2++) {
                    ChosenOrderList.get(i2).setchangedSubDealerCode(ChosenOrderList.get(0).getItem6());
                }
                if (status.equalsIgnoreCase("modify")) {
                    editOrderFor.setVisibility(View.VISIBLE);
                } else {
                    editOrderFor.setVisibility(View.GONE);
                }

                editOrderFor.setOnClickListener(v -> {
                    if (status.equalsIgnoreCase("modify")) {
                        Dialog masterDialog;
                        ArrayList<ProductMasterDetails> productMasterList = mAceDnsDatabase.getSubDealerListForOrderApproval(Constants.selectedCustomer.getCustomerCode());
                        if (!productMasterList.isEmpty()) {
                            ArrayList<ProductMasterDetails> tempProductList = new ArrayList<>(productMasterList);
                            ProductMasterAdapter prodAdapter = new ProductMasterAdapter(mContext, R.layout.product_list_child_alternate, tempProductList);

                            masterDialog = new Dialog(mContext, R.style.PauseDialog);
                            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                            masterDialog.setContentView(R.layout.select_with_search);
                            masterDialog.setCancelable(false);
                            TextView title =  masterDialog.findViewById(R.id.title);
                            ImageView imageView1 = masterDialog.findViewById(R.id.imageView1);
                            imageView1.setClickable(true);
                            imageView1.setImageResource(R.drawable.back_bg);
                            imageView1.setOnClickListener(view -> masterDialog.cancel());

                            title.setText("Please select a Sub Dealer");
                            EditText searchText =  masterDialog.findViewById(R.id.autoCompleteTextView1);
                            searchText.addTextChangedListener(new TextWatcher() {
                                @Override
                                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                                @Override
                                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {}

                                @Override
                                public void afterTextChanged(Editable s) {
                                    String str = s.toString();
                                    tempProductList.removeAll(tempProductList);
                                    int size = productMasterList.size();
                                    for (int ii = 0; ii < size; ii++) {
                                        if (productMasterList.get(ii).getDesc().length() > 1 && productMasterList.get(ii).getDesc().toUpperCase().contains(str.toUpperCase())) {
                                            tempProductList.add(productMasterList.get(ii));
                                        }
                                        prodAdapter.notifyDataSetChanged();
                                    }
                                }
                            });
                            prodQtyRateListView =  masterDialog.findViewById(R.id.list);
                            prodQtyRateListView.setAdapter(prodAdapter);
                            prodQtyRateListView.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                                String prodCode = tempProductList.get(arg2).getProdCode();
                                String prodDEsc = tempProductList.get(arg2).getDesc();
                                orderForET.setText(prodDEsc);
                                for (int i = 0; i < ChosenOrderList.size(); i++) {
                                    ChosenOrderList.get(i).setchangedSubDealerCode(prodCode);
                                }
                                masterDialog.cancel();
                            });
                            Button btnCancel =  masterDialog.findViewById(R.id.btn_ok);
                            btnCancel.setVisibility(View.GONE);
                            masterDialog.show();
                        } else {
                            Toast.makeText(mContext, "No Sub Dealer found.", Toast.LENGTH_LONG).show();
                        }
                    }
                });
            }
        }
    }
    private void getCustomerOrderList() {
        mCustomerDetailsList = mAceDnsDatabase.getCustomerListForOrderApproval(chosenBranchCode);
        orderSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                String firstKey = (String) OrderListForChosenCustomer.keySet().toArray()[i];
                ChosenOrderList = OrderListForChosenCustomer.get(firstKey);
                try {
                    ProductMasterWithQtyInputAdapterOrderApproval ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterOrderApproval(mContext, R.layout.list_item_product_edit_order_approval);
                    prodList.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
                } catch (Exception ignored) {
                }
                orderForOrderByOnOffProcess();
                orderDateTv.setText(Utils.changeDateFormat("yyyy-MM-dd hh:mm:ss", "dd/MM/yyyy hh:mm:ss", ChosenOrderList.get(0).getItem2()));
                orderFreightTv.setText(ChosenOrderList.get(0).getItem12());
                orderDestinationTv.setText(ChosenOrderList.get(0).getItem14());
                orderDumpTv.setText(ChosenOrderList.get(0).getItem19());
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {
            }
        });

        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < mCustomerDetailsList.size(); i++) {
            spinnerArray.add(mCustomerDetailsList.get(i).getCustomerName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        customerSpinner.setAdapter(spinnerArrayAdapter);
        if (!mCustomerDetailsList.isEmpty())
            customerSpinner.setSelection(0);

        customerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                Constants.selectedCustomer = mCustomerDetailsList.get(i);
                isCustomerChosen = true;
                statusRG.setVisibility(View.VISIBLE);
                statusCancelRG.setVisibility(View.VISIBLE);
                OrderListForChosenCustomer = mAceDnsDatabase.getPendingOrderListByCustomerCode(Constants.selectedCustomer.getCustomerCode());
                final ArrayList<commonDatabaseHelper> spinnerArray2 = new ArrayList<>();
                for (int i2 = 0; i2 < OrderListForChosenCustomer.size(); i2++) {
                    String firstKey = (String) OrderListForChosenCustomer.keySet().toArray()[i2];
                    ArrayList<commonDatabaseHelper> commonDatabaseHelpers = OrderListForChosenCustomer.get(firstKey);
                    assert commonDatabaseHelpers != null;
                    String date = commonDatabaseHelpers.get(0).getItem2();
                    String desc = firstKey + "- " + Utils.changeDateFormat("yyyy-MM-dd hh:mm:ss", "dd/MM/yyyy hh:mm:ss", date);
                    String color;
                    commonDatabaseHelper cdbh = new commonDatabaseHelper();
                    if (commonDatabaseHelpers.get(0).getItem3().length() < 2 && commonDatabaseHelpers.get(0).getItem6().length() < 2) {
                        color = "green";//sales
                    } else if (commonDatabaseHelpers.get(0).getItem3().length() > 2) {
                        color = "blue";//other
                    } else {
                        color = "yellow";//sub dealer
                    }
                    cdbh.setItem0(desc);
                    cdbh.setItem1(color);
                    spinnerArray2.add(cdbh);
                }
                OrderSpinnerAdapter spinnerArrayAdapter = new OrderSpinnerAdapter(spinnerArray2);
                orderSpinner.setAdapter(spinnerArrayAdapter);
                if (!spinnerArray2.isEmpty())
                    orderSpinner.setSelection(0);
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {
            }
        });
    }
    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {
                if (task == 1) {
                    new commonAsyncTaskMaster(mContext, "order_approval");
                    new commonAsyncTaskMaster(mContext, "branch_dump");
                    new commonAsyncTaskMaster(mContext, "branch_destination");
                }
                Message msg = mHandlerPrepareSaudaData.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPrepareSaudaData.sendMessage(msg);
            }
        }.start();
    }
    public class OrderSpinnerAdapter extends BaseAdapter {
        ArrayList<commonDatabaseHelper> orderData;
        LayoutInflater inflter;

        public OrderSpinnerAdapter(ArrayList<commonDatabaseHelper> orderData) {

            this.orderData = orderData;
            inflter = (LayoutInflater.from(mContext));
        }

        @Override
        public int getCount() {
            return orderData.size();
        }

        @Override
        public Object getItem(int i) {
            return null;
        }

        @Override
        public long getItemId(int i) {
            return 0;
        }

        @SuppressLint({"ViewHolder", "InflateParams"})
        @Override
        public View getView(int i, View view, ViewGroup viewGroup) {
            view = inflter.inflate(R.layout.product_spinner_layout, null);
            TextView names = view.findViewById(R.id.text1);
            names.setText(orderData.get(i).getItem0());
            String color = "";
            if (orderData.get(i).getItem1().equalsIgnoreCase("green")) {
                color = "#4a973a";
            } else if (orderData.get(i).getItem1().equalsIgnoreCase("blue")) {
                color = "#003399";
            } else {
                color = "#FFC000";
            }
            names.setText(HtmlCompat.fromHtml("<font color='" + color + "'>" + orderData.get(i).getItem0() + "</font>", HtmlCompat.FROM_HTML_MODE_LEGACY));
            return view;
        }
    }
}
