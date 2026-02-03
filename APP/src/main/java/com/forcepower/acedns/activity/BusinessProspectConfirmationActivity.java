package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.drawable.ColorDrawable;
import android.location.LocationManager;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.provider.Settings;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.util.SparseBooleanArray;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.BuildConfig;
import com.forcepower.acedns.R;
import com.forcepower.acedns.TRANS_BusinessProspectCustomizeTransactionTask;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitBusinessProspect;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.orderAuditType;

public class BusinessProspectConfirmationActivity extends AceDnsParentActivity {

    ArrayList<CustomerDetails> customerList, tempCustomerList;
    ArrayList<ProductMasterDetails> productList;
    ArrayList<ProductMasterDetails> selectedProductList;
    Context mContext;
    AceDnsDatabase setupDataHelperObj;
    AceDnsTransactionDatabase transDataHelperObj;
    Button btnBack, btnSubmit, btnTag, btnProduct;
    ListView productListView;
    ImageView imgLogo;
    LinearLayout tagLayout;
    String lastStr = "";
    CustomerAdapter adapterCust;
    Dialog customerListDialog, productListDialog;
    int chosenCustPos;
    Handler mHandler;
    ProgressDialog pd;
    ProductMasterAdapter selectedProductAdapter;
    String name = "", drcat = "", address = "", pin = "", routeCode = "", phone = "", remarks = "", tagCustCode = "", routeName = "", custType = "R", prosType;
    Handler saveHandler;
    ProgressDialog loader;
    boolean isNewRoute = false;
    /****       R&D      ****/
    ArrayList<ProductGroupDetails> productGroupList, selectedGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList, selectedProductSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList, selectedProductBrandList;
    Dialog grpDialog, subGrpDialog, brandDialog;
    int filterNo;
    ProgressDialog ploader;
    Handler itemHandler;
    GPSTracker gpstracker;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_prospect_prod);
        RegisterActivities.registerActivity(this);

        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());

        name = getIntent().getStringExtra("NAME");
        address = getIntent().getStringExtra("ADDRESS");
        pin = getIntent().getStringExtra("PIN");
        routeCode = getIntent().getStringExtra("ROUTE CODE");
        routeName = getIntent().getStringExtra("ROUTE NAME");
        phone = getIntent().getStringExtra("PHONE");
        //	isNewRoute = getIntent().getBooleanExtra("NEW ROUTE", false);
        //	custType = getIntent().getStringExtra("CUST TYPE");
        prosType = getIntent().getStringExtra("PROS TYPE");

        if (Constants.menuDetailsObj.getDoctor_visit().matches("yes")) {
            drcat = getIntent().getStringExtra("DRCAT");
        }

        mContext = BusinessProspectConfirmationActivity.this;
        setupDataHelperObj = new AceDnsDatabase(mContext);
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);

        initView();

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    pd.cancel();
                    if (Constants.orderFormDetailsObj.getTagged_distributor_business_prospect().equalsIgnoreCase("yes")) {
                        if (Constants.mBusinessProspectType.matches("existing")) {

                            tagCustCode = Constants.mBusinessProspectTaggedCustomerCode;
                            btnTag.setText(setupDataHelperObj.getDealerName(tagCustCode));
                            btnTag.setEnabled(false);
                            btnProduct.setEnabled(false);
                            showInstructionDialog();
                            return;
                        }
                        if (customerList.size() > 0)
                        {
                            tempCustomerList = new ArrayList<>();
                            reInitialiseCustomerList();
                            adapterCust = new CustomerAdapter(BusinessProspectConfirmationActivity.this, R.layout.customer_list_child, tempCustomerList);

                            if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary")) {
                                orderAuditType = "Secondary";
                                showChooseCustomerDialog();
                            }
                            else
                            {
                                final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
                                dialgoCondition.setCancelable(false);
                                dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
                                dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
                                dialgoCondition.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));

                                TextView txtMsg = (TextView) dialgoCondition.findViewById(R.id.title);
                                txtMsg.setText("Select Prospect Type.");

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
                                                    btnTag.setVisibility(View.GONE);
                                                    tagCustCode = "";
                                                    productSelectionProcess();
                                                } else {
                                                    orderAuditType = "Secondary";
                                                    showChooseCustomerDialog();
                                                }
                                                dialgoCondition.cancel();


                                            }
                                        });
                                dialgoCondition.show();
                            }

                        }
                        else {
                            //This is the situation where tag_dist = yes but no dist found.
                            btnTag.setEnabled(false);
                            tagCustCode = "    ";
                            productSelectionProcess();
                        }
                    } else {
                        productSelectionProcess();
                    }
                }
            }
        };


        saveHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("ProspectJobDone")) {
                    loader.cancel();
                    BusinessProspectConfirmationActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            if (Constants.mBusinessProspectType.matches("existing")) {
                                new TRANS_SubmitBusinessProspect(BusinessProspectConfirmationActivity.this, true, "SUBMIT", "DE").execute();
                            } else {
                                new TRANS_SubmitBusinessProspect(BusinessProspectConfirmationActivity.this, true, "SUBMIT", "DC").execute();
                            }
                            if(Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("customized")){
                                ArrayList<Location> unUploadedTransaction = transDataHelperObj.getUnuploadedTransaction("business_prospect_customize", "");
                                if (unUploadedTransaction.size() > 0) {
                                new TRANS_BusinessProspectCustomizeTransactionTask(mContext, unUploadedTransaction).execute();
                                }
                            }

                        }
                    });
                }
            }
        };

        itemHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                ((Activity) mContext).runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                showGrpListDialog1();
                                break;
                            case 2:
                                showSubGrpListDialog1();
                                break;
                            case 3:
                                showBrandListDialog1();
                                break;
                            case 4:
                                showProductListDialog1();
                                break;
                            case 5:

                                break;

                        }

                    }
                });
            }
        };

        loadDetails();


    }

    public void initView() {
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
//		 txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        tagLayout = (LinearLayout) findViewById(R.id.tag_layout);
        if (custType.equalsIgnoreCase("R")) {
            if (!Constants.orderFormDetailsObj.getTagged_distributor_business_prospect().equalsIgnoreCase("yes")) {
                tagLayout.setVisibility(View.GONE);
            }
//			 if(isNewRoute){
//				 tagLayout.setVisibility(tagLayout.GONE); 
//			 }
        } else {
            tagLayout.setVisibility(View.GONE);
        }


        productListView = (ListView) findViewById(R.id.prod_list);
        selectedProductList = new ArrayList<ProductMasterDetails>();
        selectedProductAdapter = new ProductMasterAdapter(BusinessProspectConfirmationActivity.this, R.layout.prod_list_child, selectedProductList);
        productListView.setAdapter(selectedProductAdapter);
        productListView.setOnItemLongClickListener(BusinessProspectConfirmationActivity.this);

        btnBack = (Button) findViewById(R.id.back);
        btnSubmit = (Button) findViewById(R.id.btn_next);
        btnTag = (Button) findViewById(R.id.btn_tag);
        btnProduct = (Button) findViewById(R.id.btn_prod);
        btnBack.setOnClickListener(this);
        btnSubmit.setOnClickListener(this);
        btnTag.setOnClickListener(this);
        btnProduct.setOnClickListener(this);

    }


    @Override
    public void onClick(View v) {
        if (v == btnBack) {
            finish();
        } else if (v == btnTag)
        {
            tempCustomerList = new ArrayList<>();
            reInitialiseCustomerList();
            adapterCust = new CustomerAdapter(BusinessProspectConfirmationActivity.this, R.layout.customer_list_child, tempCustomerList);
            if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary")) {
                orderAuditType = "Secondary";
                showChooseCustomerDialog();
            }
            else
            {
                final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
                dialgoCondition.setCancelable(false);
                dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
                dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
                dialgoCondition.getWindow().setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));

                TextView txtMsg = (TextView) dialgoCondition.findViewById(R.id.title);
                txtMsg.setText("Select Prospect Type.");

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
                                    btnTag.setText("Not Applicable");
                                    tagCustCode = "";
                                } else {
                                    orderAuditType = "Secondary";
                                    showChooseCustomerDialog();
                                }
                                dialgoCondition.cancel();


                            }
                        });
                dialgoCondition.show();
            }
        }
        else if (v == btnSubmit) {
            if (selectedProductList.size() > 0) {
                if (Constants.orderFormDetailsObj.getTagged_distributor_business_prospect().equalsIgnoreCase("yes") && !isNewRoute && custType.equalsIgnoreCase("R")) {
                    if (tagCustCode.length() > 0 && !orderAuditType.equalsIgnoreCase("primary")) {
                        showInstructionDialog();
                    } else {
                        Utils.showToast(mContext, "Please tag a Customer.");
                    }
                } else {
                    showInstructionDialog();
                }
            } else {
                Utils.showToast(mContext, "Please select a Product.");
            }
        } else if (v == btnProduct) {
            productSelectionProcess();
        }
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

    @Override
    public boolean onItemLongClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
        showDeleteItemDialog(arg2);
        return false;
    }


    public void showChooseCustomerDialog() {
        customerListDialog = new Dialog(BusinessProspectConfirmationActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.select_with_search);
        customerListDialog.setCancelable(false);
        TextView title = (TextView) customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Distributor");
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
                CustomerDetails currentObj = tempCustomerList.get(arg2);
                for (int ii = 0; ii < customerList.size(); ii++) {
                    if (customerList.get(ii).getCustomerCode().equalsIgnoreCase(currentObj.getCustomerCode())) {
                        chosenCustPos = ii;
                    }
                }
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                customerListDialog.cancel();
                btnTag.setText(customerList.get(chosenCustPos).getCustomerName());
                tagCustCode = customerList.get(chosenCustPos).getCustomerCode();
                //showProductListDialog();
                productSelectionProcess();
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

    private void productSelectionProcess() {
        if (filterNo == 1) {
            prepareOrderData(4, "SELECT * FROM product_master WHERE acedns = 'Y' AND black_list = 'N'");
        } else {
            prepareOrderData(1, "");
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


    public void loadDetails() {
        pd = new ProgressDialog(mContext);
        pd.setMessage("Fetching data.Please wait..");
        pd.show();
        new Thread() {
            public void run() {
                customerList = setupDataHelperObj.getCustomerByEmployeeAndRouteForProspect(routeCode);
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }


    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        instructionDialog.setCancelable(false);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Please provide remarks.");
        final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
        final Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {

                remarks = edInst.getText().toString();
                gpstracker = new GPSTracker(mContext);
                //new GPSTracker(mContext);
                final LocationManager manager = (LocationManager)mContext.getSystemService(LOCATION_SERVICE);
                Utils.isDevOn(mContext);
                if ( manager.isProviderEnabled( LocationManager.GPS_PROVIDER)){
                    if (!Constants.currentLat.isEmpty()) {
                        if (Constants.isDeveloperOn && !BuildConfig.DEBUG) {
                            Utils.showToast(mContext, "Please Disable Developer mode");
                            startActivity(new Intent(android.provider.Settings.ACTION_APPLICATION_DEVELOPMENT_SETTINGS));
                            finish();
                        } else {
                            saveProspectData();
                            btnSubmit.setEnabled(false);
                            submit.setEnabled(false);
                        }
                    }else {
                        Utils.showToast(mContext,"Could not get your Location try again");
                    }
                }else {
                    showSettingsToEnableLocation("Please enable your location");
                }
                instructionDialog.cancel();
            }
        });
        instructionDialog.show();
    }


    public void showDeleteItemDialog(final int pos) {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(BusinessProspectConfirmationActivity.this);
        alertDialogBuilder.setMessage("Are you sure you want to delete this item ?").setCancelable(false);
        alertDialogBuilder.setNegativeButton("OK", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                dialog.cancel();
                selectedProductList.remove(selectedProductList.get(pos));
                selectedProductAdapter.notifyDataSetChanged();
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

    public void saveProspectData() {
        //new GPSTracker(mContext);
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

                if (Constants.mBusinessProspectType.matches("new")) {
                    String newProspectPrefix = "DC";
                    String customer_code_new_prospect = newProspectPrefix + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                    transDataHelperObj.insertToProspectCustomerMaster(customer_code_new_prospect, name, address, pin, routeCode, phone, tagCustCode,drcat);
                    transDataHelperObj.insertToProspectCustDetails(newProspectPrefix, selectedProductList, timeStamp);
                    if(Constants.menuDetailsObj.getDoctor_visit().matches("yes")){
                        transDataHelperObj.insertToProspectCustDrHeader(customer_code_new_prospect, timeStamp, name, remarks, drcat,newProspectPrefix);
                    }else {
                        transDataHelperObj.insertToProspectCustHeader(customer_code_new_prospect, timeStamp, name, remarks,Constants.businessProspectCheckIn ,newProspectPrefix);
                    }
                    transDataHelperObj.insertToLocationTable(newProspectPrefix, timeStamp);
                } else {
//					Constants.mBusinessProspectCustomerCode;
                    String newProspectPrefix = "DE";
//					transDataHelperObj.insertToProspectCustDetails(newProspectPrefix,selectedProductList,timeStamp);
                    if(Constants.menuDetailsObj.getDoctor_visit().matches("yes")){
                        transDataHelperObj.insertToProspectCustDrHeader(Constants.mBusinessProspectCustomerCode, timeStamp, name, remarks, drcat,newProspectPrefix);
                    }else {
                        transDataHelperObj.insertToProspectCustHeader(Constants.mBusinessProspectCustomerCode, timeStamp, name, remarks,Constants.businessProspectCheckIn , newProspectPrefix);
                    }
                    transDataHelperObj.insertToLocationTable(newProspectPrefix, timeStamp);
                }

                //if (gpstracker != null)
                   // gpstracker.stopUsingGPS();
                if (isNewRoute) {
                    RouteDetails rootobj = new RouteDetails();
                    rootobj.setRouteCode(routeCode);
                    rootobj.setRouteName(routeName);
                    ArrayList<RouteDetails> routeList = new ArrayList<RouteDetails>();
                    routeList.add(rootobj);
                    setupDataHelperObj.insertToRouteMaster(routeList);
                }
                Message msgObj = saveHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "ProspectJobDone");
                msgObj.setData(b);
                saveHandler.sendMessage(msgObj);
            }
        }.start();
    }


    /***   GROUP    ***/
    public void showGrpListDialog1() {
        if (productGroupList.size() > 0) {
            grpDialog = new Dialog(mContext, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_multiple_from_list);
            grpDialog.setTitle("Please select an Option");
            grpDialog.setCancelable(false);
            TextView title = (TextView) grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            final ListView dialogList = (ListView) grpDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            final ProductGrpAdapter adapter1 = new ProductGrpAdapter(mContext, R.layout.multiple_product_child, productGroupList);
            dialogList.setAdapter(adapter1);
            Button submit = (Button) grpDialog.findViewById(R.id.button1);
            submit.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                selectedGroupList.add(adapter1.getItem(position));
                            }
                        }
                    } else {
                        Utils.showToast(mContext, "Please select atleast 1 Group");
                    }
                    // Main checking.
                    if (selectedGroupList.size() > 0) {
                        grpDialog.cancel();
                        switch (filterNo) {
                            case 2:
                                String str = "";
                                for (int ii = 0; ii < selectedGroupList.size(); ii++) {
                                    str = str + "'" + selectedGroupList.get(ii).getGroupCode() + "',";
                                }
                                str = str.substring(0, str.length() - 1);
                                String query = "SELECT * FROM product_master WHERE product_group_code IN (" + str + ") AND acedns = 'Y' AND black_list = 'N'";
                                Log.d("TAG", "_DOWNLOAD_ product_master: " + query);
                                prepareOrderData(4, query);
                                break;
                            case 3:
                                prepareOrderData(2, "");
                                break;
                            case 4:
                                prepareOrderData(2, "");
                                break;
                        }
                    } else {
                        Utils.showToast(mContext, "Please select atleast 1 Product");
                    }
                }
            });
            grpDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left.Please submit order.", Toast.LENGTH_SHORT).show();
        }
    }

    /***   GROUP    ***/
    /***   SUB GROUP    ***/
    public void showSubGrpListDialog1() {
        if (productSubGroupList.size() > 0) {
            subGrpDialog = new Dialog(mContext, R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_multiple_from_list);
            subGrpDialog.setCancelable(false);
            TextView title = (TextView) subGrpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            final ListView dialogList = (ListView) subGrpDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            final ProductSubGrpAdapter adapter1 = new ProductSubGrpAdapter(mContext, R.layout.multiple_product_child, productSubGroupList);
            dialogList.setAdapter(adapter1);
            Button submit = (Button) subGrpDialog.findViewById(R.id.button1);
            submit.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                selectedProductSubGroupList.add(adapter1.getItem(position));
                            }
                        }
                    } else {
                        Utils.showToast(mContext, "Please select atleast 1 SubGroup");
                    }
                    // Main checking.
                    if (selectedProductSubGroupList.size() > 0) {
                        subGrpDialog.cancel();
                        switch (filterNo) {
                            case 3:
                                String str = "";
                                for (int ii = 0; ii < selectedProductSubGroupList.size(); ii++) {
                                    str = str + "'" + selectedProductSubGroupList.get(ii).getSubGrpCode() + "',";
                                }
                                str = str.substring(0, str.length() - 1);
                                String query = "SELECT * FROM product_master WHERE product_sub_group_code IN (" + str + ") AND acedns = 'Y' AND black_list = 'N'";
                                Log.d("TAG", "_DOWNLOAD_ product_master: " + query);
                                prepareOrderData(4, query);
                                break;
                            case 4:
                                prepareOrderData(3, "");
                                break;
                        }
                    } else {
                        Utils.showToast(mContext, "Please select atleast 1 SubGroup");
                    }
                }
            });
            subGrpDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left in this category. Please choose a different category", Toast.LENGTH_SHORT).show();
        }
    }

    /***   SUB GROUP    ***/
    /***   BRAND    ***/
    public void showBrandListDialog1() {
        if (productBrandList.size() > 0) {
            brandDialog = new Dialog(mContext, R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_multiple_from_list);
            brandDialog.setCancelable(false);
            TextView title = (TextView) brandDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            final ListView dialogList = (ListView) brandDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            final ProductBrandAdapter adapter1 = new ProductBrandAdapter(mContext, R.layout.multiple_product_child, productBrandList);
            dialogList.setAdapter(adapter1);
            Button submit = (Button) brandDialog.findViewById(R.id.button1);
            submit.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                selectedProductBrandList.add(adapter1.getItem(position));
                            }
                        }
                    } else {
                        Utils.showToast(mContext, "Please select atleast 1 SubGroup");
                    }
                    // Main checking.
                    if (selectedProductBrandList.size() > 0) {
                        brandDialog.cancel();
                        String str = "";
                        for (int ii = 0; ii < selectedProductBrandList.size(); ii++) {
                            str = str + "'" + selectedProductBrandList.get(ii).getBrandCode() + "',";
                        }
                        str = str.substring(0, str.length() - 1);
                        String query = "SELECT * FROM product_master PM WHERE PM.product_brand_code IN (" + str + ") AND acedns = 'Y' AND black_list = 'N'";
                        Log.d("TAG", "_DOWNLOAD_ product_master: " + query);
                        prepareOrderData(4, query);
                    } else {
                        Utils.showToast(mContext, "Please select atleast 1 SubGroup");
                    }
                }
            });
            brandDialog.show();
        } else {
            Toast.makeText(mContext, "There are no items left in this category. Please choose a different category", Toast.LENGTH_SHORT).show();
        }
    }

    /***   BRAND    ***/
    /***   PRODUCT    ***/
    public void showProductListDialog1() {
        //http://adanware.blogspot.in/2012/04/android-multiple-selection-listview.html
        if (productList.size() > 0) {
            productListDialog = new Dialog(BusinessProspectConfirmationActivity.this, R.style.PauseDialog);
            productListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            productListDialog.setContentView(R.layout.select_multiple_from_list);
            productListDialog.setCancelable(false);
            TextView title = (TextView) productListDialog.findViewById(R.id.title);
            title.setText("Please select a Product");
            Button submit = (Button) productListDialog.findViewById(R.id.button1);
            final ListView dialogList = (ListView) productListDialog.findViewById(R.id.list);
            dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            final ProductMasterAdapter productAdapter = new ProductMasterAdapter(BusinessProspectConfirmationActivity.this, R.layout.multiple_product_child, productList);
            dialogList.setAdapter(productAdapter);
            submit.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                    int checkedItemsCount = checkedItems.size();
                    if (checkedItemsCount > 0) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                selectedProductList.add(productAdapter.getItem(position));
                            }
                        }
                    } else {
                        Utils.showToast(mContext, "Please select atleast 1 Product");
                    }
                    if (selectedProductList.size() > 0) {
                        selectedProductAdapter.notifyDataSetChanged();
                        productListDialog.cancel();
                    } else {
                        Utils.showToast(mContext, "Please select atleast 1 Product");
                    }
                }
            });
            productListDialog.show();
        } else {
            Utils.showToast(mContext, "No Products. Please check another category.");
        }
    }

    public void removeRepeatedProductItems() {
        for (int kk = 0; kk < selectedProductList.size(); kk++) {
            ProductMasterDetails currentItem = selectedProductList.get(kk);
            for (int x = 0; x < productList.size(); x++) {
                if (productList.get(x).getProdCode().equalsIgnoreCase(currentItem.getProdCode())) {
                    productList.remove(productList.get(x));
                }
            }
        }
    }

    /***   PRODUCT    ***/

    public void prepareOrderData(final int doWhat, final String query) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        selectedGroupList = new ArrayList<ProductGroupDetails>();
                        productGroupList = setupDataHelperObj.getProductGroupList(false);
                        //removeRepeatedGroupItems();
                        break;
                    case 2:
                        selectedProductSubGroupList = new ArrayList<ProductSubGrpDetails>();
                        productSubGroupList = setupDataHelperObj.getProductSubGroupListForProspect(selectedGroupList);
                        //removeRepeatedSubGroupItems();
                        break;
                    case 3:
                        selectedProductBrandList = new ArrayList<ProductBrandDetails>();
                        productBrandList = setupDataHelperObj.getProductBrandListForProspect(selectedProductSubGroupList);
                        //removeRepeatedBrandItems();
                        break;
                    case 4:
                        productList = setupDataHelperObj.getProdMasterListForProspect(query);
                        removeRepeatedProductItems();
                        break;

                }
                Message msgObj = itemHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("WHAT TO SHOW", doWhat);
                msgObj.setData(b);
                itemHandler.sendMessage(msgObj);
            }
        }.start();
    }


    public void showSettingsToEnableLocation(String titleMessage) {
        AlertDialog.Builder alertDialog = new AlertDialog.Builder(mContext);

        // Setting Dialog Title
        alertDialog.setTitle(titleMessage);

        // Setting Dialog Message
        alertDialog.setMessage("Could not determine your location, Please Enable your location.");

        // On pressing Settings button
        alertDialog.setPositiveButton("OK", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
                Intent intent = new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS);
                mContext.startActivity(intent);
            }
        });

        // on pressing cancel button
        alertDialog.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int which) {
                dialog.cancel();
            }
        });

        // Showing Alert Message
        alertDialog.show();
    }


}
