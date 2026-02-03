package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
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

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.BrokerAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.SaudaRouteAdapter;
import com.forcepower.acedns.adapter.StateAdapter;
import com.forcepower.acedns.backgroundTask.DownLoadSaudaAllocation;
import com.forcepower.acedns.backgroundTask.MASTER_LoadSaudaMRP;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.SaudaFormDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.util.ArrayList;
import java.util.Arrays;

import static com.forcepower.acedns.constants.Constants.BrokerageCost;
import static com.forcepower.acedns.constants.Constants.lodabilityToneFORDEPOT;
import static com.forcepower.acedns.constants.Constants.mDepotOrPlant;
import static com.forcepower.acedns.constants.Constants.mSaudaDepoCode;
import static com.forcepower.acedns.constants.Constants.selectedState;
import static com.forcepower.acedns.util.Utils.decimal3round;

/**
 * An activity which will use for selecting broker, route, customer depot for forward trading
 *
 * @author Sourav Das <souravd@coral.in>
 * @version 5.2.8.4
 */

public class SaudaFilterActivity extends AceDnsParentActivity {

    public static TextView mTextViewBrokerName = null;
    public static TextView mTextViewRouteName = null;
    public static TextView mTextViewCustomerName = null;
    public static TextView mTextViewDepotName = null;
    public static TextView textViewLoadabilityTons = null;
    public static TextView mTextViewRateType = null;
    public static TextView textViewDespatchOriginValue = null;
    public static TextView textViewChosenVertical = null;
    public static LinearLayout routeCustomerLayout = null;
    public static LinearLayout despatchOriginSelectLayout = null;
    public static LinearLayout despatchOriginValueLayout = null;
    public static LinearLayout layoutverticalSpinner = null;
    public static RadioGroup mRadioGroupSBT = null;
    public static RadioGroup mRadioGroupRBO = null;
    public static RadioGroup radioGrpDespatchOrigin = null;
    public static Button mButtonSubmit = null;
    public static Button mButtonBack = null;
    public static ImageView mImageViewHeaderLogo = null;
    public String mSaudaBookedType = "";
    public String mSaudaDespatchOrigin = "";
    public String mCustomerName = "";
    public String mLoadabilityTon = "";
    public String mSaudaType = "";
    public String mSaudaDepoName = "";
    public String verticalOfUser = "";
    public boolean isBrokerDataTaken = false;
    public boolean isDepoSelected = false;
    public boolean isFreightRateSelected = false;
    public AceDnsDatabase mAceDnsDatabase;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    public boolean shouldCheckSaudaDespatchOrigin = false;
    public boolean isSaudaDespatchOriginSelected = true;
    int spinnerFlag = 0;
    RouteDetails mRouteDetails;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    Context mContext;
    String selectedFreightRate = "", selectedRouteCode = "";

    ArrayList<BranchMasterDetails> saudaRDSList = new ArrayList<>();
    Spinner spinner;
    double maxAlloc = 0.0;
    String verticalValueOfEmployee = "";

    /**
     * Called when the activity is first created. Initializes the activity with necessary UI
     * for users interaction.
     */

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sauda_filter);
        RegisterActivities.registerActivity(this);
        InitializeView();
        ClearData();

        mContext = SaudaFilterActivity.this;

        Constants.mBrokerMasterList = new ArrayList<>();
        mAceDnsDatabase = new AceDnsDatabase(SaudaFilterActivity.this);
        GetSaudaSetupMasterData();
        verticalValueOfEmployee = mAceDnsDatabase.getVerticalValueOfLoggedInEmployee();
        LinearLayout ExForSelectionLayout = (LinearLayout) findViewById(R.id.ExForSelectionLayout);
        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(verticalValueOfEmployee)) {
            ExForSelectionLayout.setVisibility(View.GONE);
        } else {
            ExForSelectionLayout.setVisibility(View.VISIBLE);
        }
        /**
         * This radio group is used to selecting weather the booking is direct or broker.
         * According to this selection dialog will show.
         */
        mRadioGroupSBT
                .setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                    public void onCheckedChanged(RadioGroup group, int checkedId) {
                        ClearData();

                        RadioButton radioSelection = (RadioButton) findViewById(checkedId);
                        if (radioSelection.getText().equals("Broker")) {
                            mSaudaBookedType = "Broker";
                            Constants.mBrokerMasterList = mAceDnsDatabase.GetBrokerMaster();
                            ShowBrokerMaster();
                        } else {
                            mSaudaBookedType = "Direct";
                            mTextViewBrokerName.setText(mSaudaBookedType);
                            BrokerageCost = "0.00";
                            ShowRoute();
                        }
                    }
                });

        /**
         * This radio group is used to selecting weather is rate based on EX or FOR.
         * According to this selection layout will dynamically design in SaudaActivity class.
         */
        mRadioGroupRBO.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                if (isBrokerDataTaken == true) {
                    RadioButton radioSelection = (RadioButton) findViewById(checkedId);
                    if (radioSelection.getText().toString().equalsIgnoreCase("EX")) {
                        mSaudaType = "EX";
                        ShowSaudaDepoNameDialog();
                    } else {
                        mSaudaType = "FOR";
                        ShowSaudaDepoNameDialog();
                    }
                } else {
                    //mRadioGroupRBO.clearCheck(checkedId);
                    Toast.makeText(mContext, "Please Select Sauda booked through ", Toast.LENGTH_LONG).show();
                }
            }
        });

        radioGrpDespatchOrigin.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                                                              public void onCheckedChanged(RadioGroup group, int checkedId) {
                                                                  despatchOriginValueLayout.setVisibility(View.VISIBLE);
                                                                  RadioButton radioSelection = (RadioButton) findViewById(checkedId);
                                                                  mSaudaDespatchOrigin = radioSelection.getText().toString();
                                                                  textViewDespatchOriginValue.setText("Selected Despatch Origin: " + mSaudaDespatchOrigin);
                                                                  isSaudaDespatchOriginSelected = true;
                                                              }
                                                          }
        );

        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                SaudaFilterActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
//							if (GetSaudaSetupMasterData() == true)
//							{

                                Toast.makeText(mContext, "Please select first Sauda booked through", Toast.LENGTH_LONG).show();
                                initializeSaudaBookTypeData();

                                if (verticalValueOfEmployee.contains(",")) {

                                    layoutverticalSpinner.setVisibility(View.VISIBLE);
                                    String[] verticalArray = verticalValueOfEmployee.split(",");

                                    spinner = (Spinner) findViewById(R.id.spinner);
                                    ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, verticalArray);

                                    // Drop down layout style - list view with radio button
                                    dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

                                    // attaching data RoutePlanAdapter to spinner
                                    spinner.setAdapter(dataAdapter);
                                    showDespatchOriginOption(verticalArray[0]);
                                    spinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                                        @Override
                                        public void onItemSelected(AdapterView<?> adapterView, View view, int position, long l) {
                                            if (spinnerFlag > 0) {
                                                String selectedCatagory = adapterView.getItemAtPosition(position).toString();
                                                showDespatchOriginOption(selectedCatagory);
                                                ShowRoute();

                                            } else {
                                                spinnerFlag++;
                                            }

                                        }

                                        @Override
                                        public void onNothingSelected(AdapterView<?> adapterView) {

                                        }
                                    });
                                } else {
                                    layoutverticalSpinner.setVisibility(View.GONE);
                                    showDespatchOriginOption(verticalValueOfEmployee);
                                }

//							}
//							else
//							{
//								Toast.makeText(mContext, "Error in Fetching Data", Toast.LENGTH_LONG).show();
//							}
                                break;
                        }
                    }
                });
            }
        };


        mButtonSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if ((mSaudaType.matches("EX") || mSaudaType.matches("FOR")) && isBrokerDataTaken && isDepoSelected && isSaudaDespatchOriginSelected) {

                    if (mSaudaType.matches("FOR") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(verticalValueOfEmployee)) {
                        if (isFreightRateSelected) {
                            getVauesAndGotoNextPage();
                        } else {
                            if (!Constants.employeeDetailObject.getEmpCode().equalsIgnoreCase("E0042")) {
                                Utils.showToast(mContext, "Error: Please contact admin.");
                            } else {
                                Toast.makeText(mContext, "Did not get proper freight rate, please contact admin.", Toast.LENGTH_LONG).show();
                            }

                        }

                    } else {
                        getVauesAndGotoNextPage();
                    }
                } else if (!isSaudaDespatchOriginSelected) {
                    Toast.makeText(mContext, "Please Select Despatch Origin ", Toast.LENGTH_LONG).show();
                } else if (isBrokerDataTaken == false && isDepoSelected == true) {
                    Toast.makeText(mContext, "Please Select Sauda booked through ", Toast.LENGTH_LONG).show();
                } else if (isBrokerDataTaken == true && isDepoSelected == false) {
                    Toast.makeText(mContext, "Please Select Rate based on", Toast.LENGTH_LONG).show();
                } else {
                    Utils.showToast(mContext, "Improper sauda data. Please contact admin.");
                }
            }
        });

        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        try {
            PrepareCustomerData(1);
        } catch (Exception e) {
            e.printStackTrace();
        }

    }

    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        new commonAsyncTaskMaster(mContext, "customer_master");
                        new commonAsyncTaskMaster(mContext, "branch_route_freight");
                        new commonAsyncTaskMaster(mContext, "load_distribution");
                        new commonAsyncTaskMaster(mContext, "honeycomb_cost");
                        new commonAsyncTaskMaster(mContext, "margin_cost");


                        MASTER_LoadSaudaMRP downLoadMrpDetails = new MASTER_LoadSaudaMRP(mContext);
                        downLoadMrpDetails.execute();

                        if (Constants.saudaFormDetailsObj.getsauda_allocation_app_vertical().contains(verticalValueOfEmployee)) {
                            DownLoadSaudaAllocation downLoadSaudaAllocation = new DownLoadSaudaAllocation(mContext);
                            downLoadSaudaAllocation.execute();
                        }

                        break;
                }

                Message msg = mHandlerPrepareSaudaData.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPrepareSaudaData.sendMessage(msg);
            }
        }.start();
    }

    private void showDespatchOriginOption(String verticalOfUser) {
        this.verticalOfUser = verticalOfUser;
        Constants.selectedVerticalOfUser = verticalOfUser;
        if (Constants.saudaFormDetailsObj.getsauda_rate_dependent_on_despatch_point()
                .equalsIgnoreCase("yes") && Constants.saudaFormDetailsObj.getsauda_rate_dependent_on_despatch_point_verticlewise()
                .equalsIgnoreCase("yes") && verticalOfUser.contains(Constants.saudaFormDetailsObj.getsauda_rate_dependent_on_despatch_point_verticle_val())) {
            textViewChosenVertical.setVisibility(View.VISIBLE);
            textViewChosenVertical.setText("Vertical: " + verticalOfUser);
            despatchOriginSelectLayout.setVisibility(View.VISIBLE);
            radioGrpDespatchOrigin.setEnabled(true);
            shouldCheckSaudaDespatchOrigin = true;
            isSaudaDespatchOriginSelected = false;
        } else {
            textViewChosenVertical.setVisibility(View.GONE);
            despatchOriginSelectLayout.setVisibility(View.GONE);
            shouldCheckSaudaDespatchOrigin = false;
            isSaudaDespatchOriginSelected = true;
        }
    }

    private void initializeSaudaBookTypeData() {
        if (Constants.saudaFormDetailsObj.getSaudaBookedThrough()
                .equalsIgnoreCase("BOTH")) {
            mRadioGroupSBT.setEnabled(true);
        } else if ((Constants.saudaFormDetailsObj.getSaudaBookedThrough().equalsIgnoreCase("Yes"))) {
            mSaudaBookedType = "Broker";
            Constants.mBrokerMasterList = mAceDnsDatabase.GetBrokerMaster();
            ShowBrokerMaster();
        } else {
            mSaudaBookedType = "Direct";
            mTextViewBrokerName.setText(mSaudaBookedType);
            ShowRoute();
        }
    }

    private void getVauesAndGotoNextPage() {
        if (maxAlloc > 0) {
            mCustomerName = Constants.selectedCustomer.getCustomerName();
            Intent intent = new Intent(SaudaFilterActivity.this, SaudaActivity.class);
            intent.putExtra("CUSTOMER", mCustomerName);
            intent.putExtra("SAUDABOOKEDTYPE", mSaudaBookedType);
            intent.putExtra("SAUDATYPE", mSaudaType);
            intent.putExtra("DEPODETAILS", mSaudaDepoName);
            intent.putExtra("FREIGHTRATE", selectedFreightRate);
            intent.putExtra("shouldCheckSaudaDespatchOrigin", shouldCheckSaudaDespatchOrigin);
            intent.putExtra("mSaudaDespatchOrigin", mSaudaDespatchOrigin);
            intent.putExtra("mSaudaDespatchOrigin", mSaudaDespatchOrigin);
            intent.putExtra("selectedVerticalOfUser", verticalValueOfEmployee);
            intent.putExtra("selectedRouteName", mRouteDetails.getRouteName());
            intent.putExtra("setmaxAlloc", maxAlloc + "");
            startActivity(intent);
        } else {
            Toast.makeText(mContext, "Insufficient allocation, Please update your sauda limit.", Toast.LENGTH_SHORT).show();
        }
    }

    /**
     * Called when the activity is first created. Initializes the UI
     * for users interaction.
     */
    public void InitializeView() {

        mTextViewBrokerName = (TextView) findViewById(R.id.textViewBrokerOrDirectValue);
        mTextViewRouteName = (TextView) findViewById(R.id.textViewRouteValue);
        mTextViewCustomerName = (TextView) findViewById(R.id.textViewCustomerValue);
        mTextViewDepotName = (TextView) findViewById(R.id.textViewDepotValue);
        textViewLoadabilityTons = (TextView) findViewById(R.id.textViewLoadabilityTons);
        mTextViewRateType = (TextView) findViewById(R.id.textViewRateTypeValue);
        textViewDespatchOriginValue = (TextView) findViewById(R.id.textViewDespatchOriginValue);
        textViewChosenVertical = (TextView) findViewById(R.id.textViewChosenVertical);


        routeCustomerLayout = (LinearLayout) findViewById(R.id.routeCustomerLayout);
        despatchOriginSelectLayout = (LinearLayout) findViewById(R.id.despatchOriginLayout);
        despatchOriginValueLayout = (LinearLayout) findViewById(R.id.despatchOriginValueLayout);
        layoutverticalSpinner = (LinearLayout) findViewById(R.id.layoutverticalSpinner);


        mRadioGroupSBT = (RadioGroup) findViewById(R.id.radioSelectSBT);
        mRadioGroupRBO = (RadioGroup) findViewById(R.id.radioSelectRBO);
        radioGrpDespatchOrigin = (RadioGroup) findViewById(R.id.radioGrpDespatchOrigin);

        mRadioGroupSBT.setEnabled(false);
        mRadioGroupRBO.setEnabled(false);

        mButtonSubmit = (Button) findViewById(R.id.btn_Submi);
        mButtonBack = (Button) findViewById(R.id.back);

        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(SaudaFilterActivity.this) + "~"
                + Utils.getDBVersion(SaudaFilterActivity.this));


    }

    /**
     * Clear the data of UI.
     * Sets the flag
     */
    public void ClearData() {
        mTextViewBrokerName.setText("");
        mTextViewRouteName.setText("");
        mTextViewCustomerName.setText("");
        mTextViewDepotName.setText("");
        mTextViewRateType.setText("");
        isBrokerDataTaken = false;
        isDepoSelected = false;
        //mRadioGroupRBO.clearCheck();
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

    /**
     * This method is called to fetch route from local DB.
     */
    public void ShowRoute() {
        ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteListForSaudaOrBargain("sauda");
        if (routeList.size() == 1) {
            mRouteDetails = routeList.get(0);
            mTextViewRouteName.setText("Route: " + mRouteDetails.getRouteName());
            selectedRouteCode = mRouteDetails.getRouteCode();
            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRouteForSauda(mRouteDetails.getRouteCode());


            if (mCustomerDetailsList.size() > 1) {
                ShowCustomerListDialog();
            } else {
                if (mCustomerDetailsList.size() == 1) {
                    mTextViewCustomerName.setText("Customer : " + mCustomerDetailsList.get(0).getCustomerName());
                    routeCustomerLayout.setVisibility(View.VISIBLE);
                    Constants.selectedCustomer = mCustomerDetailsList.get(0);
                    customerPhoneEmailUpdateProcess();
                    maxAllocatiopnCalculationProcess();
                    isBrokerDataTaken = true;
                    setMaxLiquidationDiscount();
                } else {
                    Toast.makeText(mContext, "No existing customer found.",
                            2000).show();
                }
            }
        } else if (routeList.size() > 1) {
            ShowRouteListDialog(routeList);
        }
    }

    private void customerPhoneEmailUpdateProcess() {
        if (Constants.surveyFormDetailsObj.getCustomerEmailUpdate().equalsIgnoreCase("yes")) {
            String mCustomerEmail = "", mCustomerPhone = "";
            try {
                mCustomerEmail = Constants.selectedCustomer.getEmail().trim();
            } catch (Exception e) {

            }
            try {
                mCustomerPhone = Constants.selectedCustomer.getNumber().trim();
            } catch (Exception e) {

            }

            if (mCustomerEmail.matches("")) {
                showEmailOrPhoneDialog("Email");
            }
            if (mCustomerPhone.matches("")) {
                showEmailOrPhoneDialog("Mobile");
            }
        }
    }

    /**
     * This method is called to show broker dialog.
     * An user can select broker from the dialog list.
     */
    public void ShowBrokerMaster() {
        if (Constants.mBrokerMasterList.size() > 0) {
            final Dialog mSaudaBrokerDialog = new Dialog(SaudaFilterActivity.this,
                    R.style.PauseDialog);
            mSaudaBrokerDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mSaudaBrokerDialog.setContentView(R.layout.select_with_search);
            mSaudaBrokerDialog.setCancelable(false);

            TextView title = (TextView) mSaudaBrokerDialog.findViewById(R.id.title);
            title.setText("Please select a Broker");
            ListView dialogList = (ListView) mSaudaBrokerDialog
                    .findViewById(R.id.list);

            final BrokerAdapter brokeradapter = new BrokerAdapter(SaudaFilterActivity.this,
                    R.layout.customer_broker_list_child, Constants.mBrokerMasterList);
            dialogList.setAdapter(brokeradapter);


            EditText searchText = (EditText) mSaudaBrokerDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1,
                                          int arg2, int arg3) {
                    brokeradapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mSaudaBrokerDialog.cancel();
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    Constants.selectedBroker = brokeradapter.getItem(arg2);
                    mTextViewBrokerName.setText("Broker: " + Constants.selectedBroker.getBrokerName());
                    ShowRoute();
                }
            });

            Button cancel = (Button) mSaudaBrokerDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            mSaudaBrokerDialog.show();
        }
    }

    /**
     * This method is called to show Route dialog.
     * An user can select route from the dialog list.
     */
    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {

        final Dialog mDialogRoute = new Dialog(SaudaFilterActivity.this, R.style.PauseDialog);
        mDialogRoute.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogRoute.setContentView(R.layout.select_with_search);
        mDialogRoute.setCancelable(false);
        TextView title = (TextView) mDialogRoute.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) mDialogRoute.findViewById(R.id.list);
        final SaudaRouteAdapter adapter = new SaudaRouteAdapter(SaudaFilterActivity.this,
                R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);

        EditText searchText = (EditText) mDialogRoute.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1,
                                      int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });


        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogRoute.cancel();
                mRouteDetails = adapter.getItem(arg2);
                mTextViewRouteName.setText("Route: " + mRouteDetails.getRouteName());
                selectedRouteCode = mRouteDetails.getRouteCode();
                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRouteForSauda(mRouteDetails.getRouteCode());

                if (mCustomerDetailsList.size() > 1) {
                    ShowCustomerListDialog();
                } else {
                    if (mCustomerDetailsList.size() == 1) {
                        mTextViewCustomerName.setText("Customer : "
                                + mCustomerDetailsList.get(0).getCustomerName());
                        Constants.selectedCustomer = mCustomerDetailsList.get(0);
                        routeCustomerLayout.setVisibility(View.VISIBLE);
                        customerPhoneEmailUpdateProcess();
                        isBrokerDataTaken = true;
                        setMaxLiquidationDiscount();

                        exForSelectionProcess();

                        if (!Constants.saudaFormDetailsObj.getsauda_allocation_app_vertical().contains(verticalValueOfEmployee)) {
                            calculateMaxSaudaLimitPendingQty();
                        } else {
                            maxAlloc = 0.0;
                            for (int i = 0; i < Constants.selectedAlocatedSaudaList.size(); i++) {
                                maxAlloc = maxAlloc + Double.parseDouble(Constants.selectedAlocatedSaudaList.get(i).getBalance());
                            }
                        }
                    } else {
                        Toast.makeText(mContext, "No existing customer found.",
                                2000).show();
                    }
                }
            }
        });

        Button cancel = (Button) mDialogRoute.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mDialogRoute.show();
    }

    private void exForSelectionProcess() {
        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(verticalValueOfEmployee)) {
            String incoTermsOfCurentCustomer = Constants.selectedCustomer.getIncoTerms().trim();
            mLoadabilityTon = Constants.selectedCustomer.getLoadabilityTon().trim();
            textViewLoadabilityTons.setText("  Transport Mode : " + Constants.selectedCustomer.getTransportMode().trim());
            textViewLoadabilityTons.setVisibility(View.VISIBLE);
            if (incoTermsOfCurentCustomer.contains(";"))//multiple incoterms
            {

                ArrayList<String> incotermsArrayMultiple = new ArrayList<String>(Arrays.asList(incoTermsOfCurentCustomer.split(";")));
                ShowIncotermsSelectionDialog(incotermsArrayMultiple);
            } else {
                incotermsSplitProcess(incoTermsOfCurentCustomer);
            }

        }
    }

    private void incotermsSplitProcess(String incoTermsOfCurentCustomer) {
        if (incoTermsOfCurentCustomer.toLowerCase().contains("for depot") || incoTermsOfCurentCustomer.toLowerCase().contains("for plant") || incoTermsOfCurentCustomer.toLowerCase().contains("ex depot")
                || incoTermsOfCurentCustomer.toLowerCase().contains("ex plant")) {
            if (incoTermsOfCurentCustomer.contains(" ")) {
                String[] inctermsArray = incoTermsOfCurentCustomer.split(" ");
                if (inctermsArray.length == 2) {
                    mSaudaType = inctermsArray[0].toUpperCase();
                    mDepotOrPlant = inctermsArray[1];
                    ShowSaudaDepoNameDialog();
                } else {
                    Utils.showToast(mContext, "Improper incoterms data found for selected customer. Please contact admin.");
                }
            }
        } else {
            Utils.showToast(mContext, "Improper incoterms data found for selected customer. Please contact admin.");
        }
    }

    //if current customer's liquidation discount is valid or higher than 0 then allow user to input
    private void setMaxLiquidationDiscount() {
        Constants.maxLiquidationDiscountForCurrentCustomer = Constants.selectedCustomer.getTradeDiscount();
    }


    public void ShowIncotermsSelectionDialog(final ArrayList<String> incotermsArrayMultiple) {

        if (incotermsArrayMultiple.size() > 1) {
            final Dialog incotermsSelectionDialog = new Dialog(mContext,
                    R.style.PauseDialog);
            incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            incotermsSelectionDialog.setContentView(R.layout.select_from_list);
            incotermsSelectionDialog.setCancelable(false);

            Button btn_cncl = (Button) incotermsSelectionDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    incotermsSelectionDialog.dismiss();
                }
            });
            TextView title = (TextView) incotermsSelectionDialog.findViewById(R.id.title);
            title.setText("Please select incoterms");
            ListView dialogList = (ListView) incotermsSelectionDialog
                    .findViewById(R.id.list);

            StateAdapter branchadapter = new StateAdapter(mContext,
                    R.layout.route_list_child, incotermsArrayMultiple, false);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    incotermsSelectionDialog.cancel();
                    selectedState = incotermsArrayMultiple.get(arg2);
                    incotermsSplitProcess(incotermsArrayMultiple.get(arg2));
                }
            });

            incotermsSelectionDialog.show();

        } else {
            if (incotermsArrayMultiple.size() == 1) {
                incotermsSplitProcess(incotermsArrayMultiple.get(0));
            } else {
                Toast.makeText(mContext, "Improper incoterms data.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    /**
     * This method is called to show customer dialog.
     * An user can select customer from the dialog list.
     */
    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext,
                R.layout.customer_list_child, mCustomerDetailsList);

        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = (EditText) mDialogCustomer
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

        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                Constants.isSelectCustomer = true;
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
                routeCustomerLayout.setVisibility(View.VISIBLE);
                customerPhoneEmailUpdateProcess();
                isBrokerDataTaken = true;
                setMaxLiquidationDiscount();
                exForSelectionProcess();


                //Amitabha
                maxAllocatiopnCalculationProcess();
            }
        });

        Button addCustomer = (Button) mDialogCustomer
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();
    }

    private void maxAllocatiopnCalculationProcess() {
        if (!Constants.saudaFormDetailsObj.getsauda_allocation_app_vertical().contains(verticalValueOfEmployee)) {
            calculateMaxSaudaLimitPendingQty();
        } else {
            maxAlloc = 0.0;
            for (int i = 0; i < Constants.selectedAlocatedSaudaList.size(); i++) {
                maxAlloc = maxAlloc + Double.parseDouble(Constants.selectedAlocatedSaudaList.get(i).getBalance());
            }
        }
    }

    /**
     * This method is called to show Depot dialog.
     * An user can select depot from the dialog list.
     */
    public void ShowSaudaDepoNameDialog() {

        String plantDepotFilter = "";
        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(verticalValueOfEmployee)) {
            if (mDepotOrPlant.equalsIgnoreCase("plant")) {
                plantDepotFilter = " LOWER(is_plant)='yes' AND ";
            } else if (mDepotOrPlant.equalsIgnoreCase("depot")) {
                plantDepotFilter = " LOWER(is_plant)='no' AND";
            }
        }

        if (mSaudaType.matches("FOR") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
            saudaRDSList = mAceDnsDatabase.getSaudaRDSListWithRouteCode(Constants.selectedCustomer.getCustomerCode(), selectedRouteCode, plantDepotFilter,Constants.selectedCustomer.getTransportMode(),Constants.selectedCustomer.getLoadabilityTon());
        } else {
            saudaRDSList = mAceDnsDatabase.getSaudaRDSList(Constants.selectedCustomer.getCustomerCode(), plantDepotFilter);
        }


        if (saudaRDSList.size() > 1) {
            final Dialog mDialogDepotName = new Dialog(mContext,
                    R.style.PauseDialog);
            mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogDepotName.setContentView(R.layout.select_from_list);
            mDialogDepotName.setCancelable(false);

            TextView title = (TextView) mDialogDepotName.findViewById(R.id.title);
            title.setText("Please select a " + mDepotOrPlant);
            ListView dialogList = (ListView) mDialogDepotName.findViewById(R.id.list);

            BranchAdapter branchadapter = new BranchAdapter(mContext,
                    R.layout.route_list_child, saudaRDSList);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int arg2, long arg3) {
                    mDialogDepotName.cancel();
                    Constants.selectedBranch = saudaRDSList.get(arg2);
                    mSaudaDepoName = saudaRDSList.get(arg2).getBranchName();
                    mSaudaDepoCode = saudaRDSList.get(arg2).getBranchCode();
                    mTextViewRateType.setText("  Rate : " + mSaudaType + " - " + mDepotOrPlant);
                    mTextViewDepotName.setText("  " + mDepotOrPlant + " Name : " + mSaudaDepoName);
                    if (mSaudaType.matches("FOR")) {
                        freightRateSelectionProcess();
                    } else {
                    }
                    isDepoSelected = true;
                }
            });

            Button cancel = (Button) mDialogDepotName.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);

            mDialogDepotName.show();

        } else {
            if (saudaRDSList.size() == 1) {
                Constants.selectedBranch = saudaRDSList.get(0);
                mSaudaDepoName = saudaRDSList.get(0).getBranchName();
                mSaudaDepoCode = saudaRDSList.get(0).getBranchCode();
                mTextViewRateType.setText("  Rate : " + mSaudaType + " - " + mDepotOrPlant);
                mTextViewDepotName.setText("  " + mDepotOrPlant + " Name : " + mSaudaDepoName);
                if (mSaudaType.matches("FOR")) {
                    freightRateSelectionProcess();
                } else {
                }
                isDepoSelected = true;
            } else {
                Toast.makeText(mContext, "No " + mDepotOrPlant + " found.", Toast.LENGTH_LONG).show();
            }
        }
    }

    public void showEmailOrPhoneDialog(final String emailOrPhone) {
        final Dialog DialogObject = new Dialog(mContext);
        DialogObject.requestWindowFeature(Window.FEATURE_NO_TITLE);
        DialogObject.setContentView(R.layout.user_instruction_dialog1);
        DialogObject.setCancelable(false);
        ViewGroup.LayoutParams params = DialogObject.getWindow().getAttributes();
        params.width = ViewGroup.LayoutParams.MATCH_PARENT;
        params.height = ViewGroup.LayoutParams.WRAP_CONTENT;
        DialogObject.getWindow().setAttributes((android.view.WindowManager.LayoutParams) params);

        TextView title = (TextView) DialogObject.findViewById(R.id.title);

        final EditText edInst = (EditText) DialogObject.findViewById(R.id.ed_input);
        edInst.setHint("Customer " + emailOrPhone + "(Mandatory)");
        if (emailOrPhone.matches("Email")) {
            title.setText("Please provide Email address for " + Constants.selectedCustomer.getCustomerName());
            edInst.setInputType(InputType.TYPE_TEXT_VARIATION_EMAIL_ADDRESS);
        } else {
            title.setText("Please provide Mobile No. for " + Constants.selectedCustomer.getCustomerName());
            edInst.setInputType(InputType.TYPE_CLASS_PHONE);
        }

        Button submit = (Button) DialogObject.findViewById(R.id.btn);
        submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                String input = edInst.getText().toString();
                if (input.length() > 0) {
                    if (emailOrPhone.matches("Email")) {
                        if (Utils.isValidMail(input)) {
                            Constants.selectedCustomer.setEmail(input);
                            mAceDnsDatabase.updateCustomerMasterWithEmailOrPhone(emailOrPhone);
                            DialogObject.dismiss();
                        } else {
                            Utils.showToast(mContext, "Please provide proper email address.");
                        }
                    } else {
                        if (Utils.isValidIndianMobile(input)) {
                            Constants.selectedCustomer.setNumber(input);
                            mAceDnsDatabase.updateCustomerMasterWithEmailOrPhone(emailOrPhone);
                            DialogObject.dismiss();
                        } else {
                            Utils.showToast(mContext, "Please provide proper mobile number.");
                        }
                    }

                }

            }
        });
        DialogObject.show();
    }

    private void calculateMaxSaudaLimitPendingQty() {
        double saudaLimit = 0.0, pendingQty = 0.0;
        String SelectedCustomerCode = Constants.selectedCustomer.getCustomerCode();
        String SaudaLimitForSelectedCustomerCode = Constants.selectedCustomer.getSaudaLimit();
        String PendingQuantityForSelectedCustomerCode = mAceDnsDatabase.getPendingQuantityOfCustomer(SelectedCustomerCode);

        if (Utils.isNumeric(SaudaLimitForSelectedCustomerCode)) {
            saudaLimit = Double.parseDouble(SaudaLimitForSelectedCustomerCode);
        }
        if (Utils.isNumeric(PendingQuantityForSelectedCustomerCode)) {
            pendingQty = Double.parseDouble(PendingQuantityForSelectedCustomerCode);
        }
        maxAlloc = saudaLimit - pendingQty;

        set3Data(saudaLimit, pendingQty, maxAlloc);
    }

    private void freightRateSelectionProcess() {
        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(verticalValueOfEmployee) && mLoadabilityTon.matches("")) {
            isFreightRateSelected = false;
            Utils.showToast(mContext, "Could not calculate freight rate. Improper loadability value. Please contact admin!");
        } else {
            selectedFreightRate = mAceDnsDatabase.GetFreightRateByBranchCodeFromBranchRouteFreightMaster(mSaudaDepoCode, selectedRouteCode, mLoadabilityTon, verticalValueOfEmployee);
            if (mDepotOrPlant.equalsIgnoreCase("depot")) {
                lodabilityToneFORDEPOT = mAceDnsDatabase.GetLoadabilityTonForDepotSAUDA(Constants.selectedBranch.getBranchCode(), Constants.selectedCustomer.getRouteCode(), verticalValueOfEmployee, "branch_route_freight");
            }

            if (!selectedFreightRate.matches("") && Utils.isNumeric(selectedFreightRate)) {
                isFreightRateSelected = true;
            } else {
                isFreightRateSelected = false;
            }
        }


    }

    /**
     * This Function is used to fetch the sauda setup and master data.
     *
     * @return yes if successfully fetch the data
     */
    public boolean GetSaudaSetupMasterData() {
        boolean issucess = true;
        try {
            Constants.saudaFormDetailsObj = new SaudaFormDetails();
            Constants.selectedAlocatedSaudaList = new ArrayList<>();
            Constants.saudaFormDetailsObj = mAceDnsDatabase.GETSaudaFormDetails();
            Constants.selectedAlocatedSaudaList = mAceDnsDatabase.GETSaudaAllocation();
        } catch (Exception ex) {
            issucess = false;
        }
        return issucess;
    }

    public void set3Data(double saudaLimit, double pendingQty, double maxAlloc) {
        LinearLayout sauda_limit_layout = (LinearLayout) findViewById(R.id.sauda_limit_layout);
        LinearLayout pending_qty_layout = (LinearLayout) findViewById(R.id.pending_qty_layout);
        LinearLayout allocation_cont_layout = (LinearLayout) findViewById(R.id.allocation_cont_layout);
        sauda_limit_layout.setVisibility(View.VISIBLE);
        pending_qty_layout.setVisibility(View.VISIBLE);
        allocation_cont_layout.setVisibility(View.VISIBLE);
        TextView textViewCalculatedSaudaLimit = (TextView) findViewById(R.id.textViewCalculatedSaudaLimit);
        TextView textViewCalculatedPendingQty = (TextView) findViewById(R.id.textViewCalculatedPendingQty);
        TextView textViewCalculateedAllocation = (TextView) findViewById(R.id.textViewCalculateedAllocation);

//            saudaLimit = Math.round(saudaLimit * 100.0) / 100.0;
        if (saudaLimit != 0.0) {
            saudaLimit = decimal3round(saudaLimit, 3);
            textViewCalculatedSaudaLimit.setText(saudaLimit + "");
        } else
            textViewCalculatedSaudaLimit.setText("0.00");

//
        if (pendingQty != 0.0) {
            pendingQty = decimal3round(pendingQty, 3);
            textViewCalculatedPendingQty.setText(pendingQty + "");
        } else
            textViewCalculatedPendingQty.setText("0.00");

//
        if (maxAlloc != 0.0) {
            maxAlloc = decimal3round(maxAlloc, 3);
            textViewCalculateedAllocation.setText(maxAlloc + "");
        } else
            textViewCalculateedAllocation.setText("0.00");
    }

}
