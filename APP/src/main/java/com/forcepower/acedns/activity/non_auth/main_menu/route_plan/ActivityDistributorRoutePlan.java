package com.forcepower.acedns.activity.non_auth.main_menu.route_plan;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;
import androidx.fragment.app.FragmentTransaction;

import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.SparseBooleanArray;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitRoutePlanTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerTypeAdapter;
import com.forcepower.acedns.adapter.JointWorkEmployeeAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;
import java.util.Objects;

public class ActivityDistributorRoutePlan extends FragmentActivity implements OnClickListener {
    ArrayList<EmployeeMasterDetails> selectedEmpList;
    RouteDetails mRouteDetails;
    JointWorkEmployeeAdapter JointWorkEmployeeAdapterObj;
    String singleOrJointWork = "";
    Date dateFinal;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewLogo = null;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    SimpleDateFormat formatter;
    SimpleDateFormat formatter1;
    boolean firstTimeCall = true;
    boolean routePlanEdited = false;
    Context mContext;
    ArrayList<RoutePlanMasterDetails> selectedRouteList;
    ArrayList<RoutePlanMasterDetails> routePlanList;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    RoutePlanDetails routePlanDetailsObj;
    String mNewRouteName = "";
    String mNewRouteCode = "";
    String mCustomerName = "";
    String mCustomerCode = "";
    String mVisitDate = "";
    String mTimeStamp = "";
    String MODE = "";
    String[] routePlanPeriodValue = null;

    //If Flow =2 Edit
    String[] monthArray = {"January", "February", "March", "April", "May",
            "June", "July", "August", "September", "October", "November",
            "December"};
    private CaldroidFragment mCaldroidFragment;
    private Handler mHandler;
    private ProgressDialog mProgressDialog;

    @SuppressLint({"SimpleDateFormat", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_route_plan);
        RegisterActivities.registerActivity(this);

        formatter = new SimpleDateFormat("dd-MM-yyyy");
        formatter1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        mContext = ActivityDistributorRoutePlan.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        routePlanList = mAceDnsDatabase.getRoutePlanList();

        MODE = getIntent().getStringExtra("MODE");

        routePlanDetailsObj = mAceDnsDatabase.getRoutePlanDetailsObj();

        /*
         * Showing some informative Dialogues.
         */
        if (MODE.equalsIgnoreCase("CREATE")) {
            if (routePlanDetailsObj.getRoutePlanAccessPeriod().equalsIgnoreCase("yes")) {
                String[] accessPeriodArray = mAceDnsDatabase.getRoutePlanAccessPeriod();
                if (accessPeriodArray != null && !accessPeriodArray[0].isEmpty() && !accessPeriodArray[1].isEmpty()) {
                    try {
                        String periodValue = accessPeriodArray[2];
                        routePlanPeriodValue = periodValue.split(",");
                        ShowAccessMonthDialog("You are allowed to make PJP Plan for the Months :");
                    } catch (Exception e) {
                        System.out.println("Exception::::::::::" + e);
                    }
                }
            }
        } else if (MODE.equalsIgnoreCase("DEVIATE")) {
            routePlanPeriodValue = new String[0];
            ShowAccessMonthDialog("You are allowed to make PJP Deviation Request for Today only.");
        }

        mTimeStamp = new SimpleDateFormat("yyyyMMddHHmmss").format(Calendar.getInstance().getTime());
        InitializeView();

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    mProgressDialog.cancel();
                    Constants.isNewRoute = true;
                    new TRANS_SubmitRoutePlanTask(ActivityDistributorRoutePlan.this, true, "SUBMIT").execute();
                }
            }
        };

    }

    @Override
    public void onClick(View arg0) {
        if (arg0 == mButtonBack) {
            if (routePlanEdited) {
                Utils.showToast(mContext, "Please submit the route plan");
            } else {
                finish();
            }
        } else if (arg0 == mButtonSubmit) {
            if (routePlanEdited) {
                routePlanEdited = false;
                mButtonSubmit.setEnabled(false);
                SaveRoutePlan();
            } else {
                Utils.showToast(mContext, "You haven't made any modification to the Route Plan");
            }
        }
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_MENU || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    public void InitializeView() {
        mImageViewLogo = findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewLogo.setVisibility(View.VISIBLE);
            mImageViewLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewLogo.setVisibility(View.GONE);
        }
        mButtonBack = findViewById(R.id.back);
        mButtonSubmit = findViewById(R.id.submit);

        mButtonSubmit.setVisibility(View.GONE);

        mButtonBack.setOnClickListener(ActivityDistributorRoutePlan.this);
        mButtonSubmit.setOnClickListener(ActivityDistributorRoutePlan.this);

        mCaldroidFragment = new CaldroidFragment();

        Bundle args = new Bundle();
        Calendar cal = Calendar.getInstance();
        args.putInt(CaldroidFragment.MONTH, cal.get(Calendar.MONTH) + 1);
        args.putInt(CaldroidFragment.YEAR, cal.get(Calendar.YEAR));
        args.putBoolean(CaldroidFragment.ENABLE_SWIPE, true);
        args.putBoolean(CaldroidFragment.SIX_WEEKS_IN_CALENDAR, true);
        mCaldroidFragment.setArguments(args);
        FragmentTransaction t = getSupportFragmentManager().beginTransaction();
        t.replace(R.id.calendar1, mCaldroidFragment);
        t.commit();

        CaldroidListener listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                ShowDistributorRoutePlansForToday(date);
            }

            @Override
            public void onChangeMonth(int month, int year) {
                if (MODE.equalsIgnoreCase("CREATE")) {
                    if (!firstTimeCall && routePlanPeriodValue != null) {
                        boolean errorShow = true;
                        for (String s : routePlanPeriodValue) {
                            if (month == Integer.parseInt(s)) {
                                errorShow = false;
                                break;
                            }
                        }
                        if (errorShow) {
                            ShowAccessMonthDialog("You are allowed to make PJP Plan for the months :");
                        }
                    }
                    firstTimeCall = false;
                } else if (MODE.equalsIgnoreCase("DEVIATE")) {
                    if ((new Date().getMonth() + 1) != month) {
                        ShowAccessMonthDialog("You are allowed to make PJP Deviation Request for today only.");
                    }
                }
            }

            @Override
            public void onLongClickDate(Date date, View view) {
                if (Constants.menuDetailsObj.getjoint_work().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("joint_work")) {
                    dateFinal = date;
                    showWorkingALoneOrWithPartnerDialog();
                } else {
                    multipleDistributorSelectionProcess(date);
                }
            }
        };
        mCaldroidFragment.setCaldroidListener(listener);
        HighlightPlannedDate();
    }

    @SuppressLint("SetTextI18n")
    public void showWorkingALoneOrWithPartnerDialog() {
        final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
        Objects.requireNonNull(dialgoCondition.getWindow()).setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

        TextView txtMsg = dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select Work Type.");

        final RadioGroup radioSelectionGroup = dialgoCondition.findViewById(R.id.radioSelect);
        final RadioButton radioEdit = dialgoCondition.findViewById(R.id.radioEdit);
        final RadioButton radioRedundant = dialgoCondition.findViewById(R.id.radioRedundant);
        radioEdit.setText("Working alone");
        radioRedundant.setText("With others");
        radioSelectionGroup
                .setOnCheckedChangeListener((group, checkedId) -> {
                    RadioButton radioSelection = dialgoCondition.findViewById(checkedId);
                    if (radioSelection.getText().toString().trim().equalsIgnoreCase("Working alone")) {
                        singleOrJointWork = "Working alone";
                        multipleDistributorSelectionProcess(dateFinal);
                    } else {
                        singleOrJointWork = "With others";
                        showEmployeeToWorkWithDialog(dateFinal);
                    }
                    dialgoCondition.cancel();

                });
        dialgoCondition.show();
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void showEmployeeToWorkWithDialog(final Date date) {
        Dialog routeDialog;
        ArrayList<EmployeeMasterDetails> employeeList;
        employeeList = mAceDnsTransactionDatabase.getJointWorkEmpForToday(formatter.format(date));
        JointWorkEmployeeAdapterObj = new JointWorkEmployeeAdapter(mContext, R.layout.multiple_route_child, employeeList);

        routeDialog = new Dialog(mContext, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_multiple_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select employees to work with");
        Button submit = routeDialog.findViewById(R.id.button1);
        final EditText autoCompleteTextView1 = routeDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.VISIBLE);
        final ListView dialogList = routeDialog.findViewById(R.id.list);
        dialogList.setTextFilterEnabled(true);
        dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);

        dialogList.setAdapter(JointWorkEmployeeAdapterObj);
        submit.setOnClickListener(arg0 -> {
            mRouteDetails = new RouteDetails();
            selectedEmpList = new ArrayList<>();
            final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
            int checkedItemsCount = checkedItems.size();
            if (checkedItemsCount > 0) {
                for (int i = 0; i < checkedItemsCount; ++i) {
                    int position = checkedItems.keyAt(i);
                    if (checkedItems.valueAt(i)) {
                        EmployeeMasterDetails ed = JointWorkEmployeeAdapterObj.getItem(position);
                        selectedEmpList.add(ed);
                    }
                }
            } else {
                Utils.showToast(mContext, "Please select at least 1 employee");
            }
            if (!selectedEmpList.isEmpty()) {
                String timeStamp = new SimpleDateFormat("yyyyMMddHHmmss").format(Calendar.getInstance().getTime());
                ArrayList<RoutePlanMasterDetails> routeList = new ArrayList<>();
                for (int i = 0; i < selectedEmpList.size(); i++) {
                    RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                    selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                    selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                    selectedObj.setRoutecode(selectedEmpList.get(i).getroute_code());
                    String routeName = selectedEmpList.get(i).getroute_name();
                    selectedObj.setRouteName(routeName);
                    selectedObj.setVisitDate(formatter.format(date));
                    selectedObj.setCreateDate(formatter1.format(Calendar.getInstance().getTime()));
                    selectedObj.setStatus("active");
                    selectedObj.setWorkingWIth(selectedEmpList.get(i).getEmpCode());
                    routeList.add(selectedObj);
                }
                mAceDnsTransactionDatabase.insertToRoutePlanMasterTable(routeList);
                routeDialog.cancel();
                routePlanEdited = true;
                new TRANS_SubmitRoutePlanTask(mContext, true, "SUBMIT").execute();
            } else {
                Utils.showToast(mContext, "Please select at least 1 employee");
            }
        });
        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);

        ImageView cancelDialog = routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());

        routeDialog.show();
    }

    public void multipleDistributorSelectionProcess(Date date) {
        Date todayDate = new Date();
        if (MODE.equalsIgnoreCase("CREATE")) {
            if (date.before(getPreviousDate())) {
                Utils.showToast(mContext, "RoutePlan cannot be done for previous Dates.");
            } else {
                boolean monthCondition = CheckDateValidity(date);
                if (routePlanDetailsObj != null && routePlanDetailsObj.getRoutePlanDeviation().equalsIgnoreCase("yes")) {
                    if (!formatter.format(date).equalsIgnoreCase(formatter.format(todayDate))) {
                        if (monthCondition) {
                            if (routePlanDetailsObj.getRoutePlanFlow().equalsIgnoreCase("no")) {
                                DistributorInExistingRoutePlan(date);
                            }
                        } else {
                            ShowAccessMonthDialog("Please note you are allowed to make route plan for the months");
                        }
                    } else {
                        Utils.showToast(mContext, "For same date - SelectPJP Deviation.");
                    }
                } else {
                    if (monthCondition) {
                        assert routePlanDetailsObj != null;
                        if (routePlanDetailsObj.getRoutePlanFlow().equalsIgnoreCase("no")) {
                            DistributorInExistingRoutePlan(date);
                        }
                    } else {
                        ShowAccessMonthDialog("Please note you are allowed to make route plan for the months");
                    }
                }
            }
        }
    }

    /*
     * Date Colouration Method
     */
    @SuppressLint("SimpleDateFormat")
    public void HighlightPlannedDate() {
        if (routePlanList != null && !routePlanList.isEmpty()) {
            for (int ii = 0; ii < routePlanList.size(); ii++) {
                try {
                    Date date = formatter.parse(routePlanList.get(ii).getVisitDate());
                    if (new SimpleDateFormat("dd-MM-yyyy").format(Calendar.getInstance().getTime()).equalsIgnoreCase(routePlanList.get(ii).getVisitDate())) {
                        mCaldroidFragment.setBackgroundResourceForDate(R.color.green, date);
                        mCaldroidFragment.refreshView();
                    } else {
                        assert date != null;
                        if (date.before(getPreviousDate())) {
                            mCaldroidFragment.setBackgroundResourceForDate(R.color.red, date);
                            mCaldroidFragment.refreshView();
                        } else if (date.after(new Date())) {
                            mCaldroidFragment.setBackgroundResourceForDate(R.color.blue, date);
                            mCaldroidFragment.refreshView();
                        }
                    }
                } catch (Exception e) {
                    System.out.println("Exception::::::::" + e.getMessage());
                }
            }
        }
    }

    /***************** DIALOG METHODS ****************/
    /*
     * Shown when a Date is clicked
     */
    @SuppressLint("SetTextI18n")
    public void ShowDistributorRoutePlansForToday(final Date date) {
        final String visitdate = formatter.format(date);
        ArrayList<CustomerDetails> customerList = mAceDnsDatabase.GetDistributorCustomerListforRoutePlan(visitdate);
        if (customerList != null && !customerList.isEmpty()) {
            final Dialog planDialog = new Dialog(ActivityDistributorRoutePlan.this, R.style.PauseDialog);
            planDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            planDialog.setContentView(R.layout.select_multiple_from_list);
            planDialog.setCancelable(false);
            TextView title = planDialog.findViewById(R.id.title);
            title.setText("Distributor list of today plan");
            final ListView list = planDialog.findViewById(R.id.list);
            final CustomerTypeAdapter adapter = new CustomerTypeAdapter(mContext, R.layout.customer_list_child, customerList);
            list.setAdapter(adapter);
            list.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                Constants.selectedCustomer = adapter.getItem(position);
                assert Constants.selectedCustomer != null;
                mCustomerName = Constants.selectedCustomer.getCustomerName();
                mCustomerCode = Constants.selectedCustomer.getCustomerCode();
                ArrayList<RouteDetails> routelist = mAceDnsDatabase.getPlanForToday(visitdate, mCustomerCode);
                if (!routelist.isEmpty()) {
                    ShowRoutePlanListDialog(date, routelist, "show");
                }
                planDialog.cancel();
            });
            Button submit = planDialog.findViewById(R.id.button1);
            submit.setVisibility(View.INVISIBLE);
            submit.setText("OK");
            submit.setOnClickListener(arg0 -> planDialog.cancel());

            ImageView cancelDialog = planDialog.findViewById(R.id.image_cancel);
            cancelDialog.setVisibility(View.VISIBLE);
            cancelDialog.setOnClickListener(arg0 -> planDialog.cancel());
            planDialog.show();
        } else {
            ShowInfoDialog("You have no route plan for this date.\nLong press date to create a route plan.");
        }
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    private void AddNewRoute(final Date date) {
        mVisitDate = formatter.format(date);
        final Dialog routeDialog = new Dialog(mContext, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.date_dialog);
        routeDialog.setCancelable(false);
        Button submit = routeDialog.findViewById(R.id.buttonDone);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Enter route name");
        final EditText eroutename = routeDialog.findViewById(R.id.editTextDate);
        eroutename.setInputType(InputType.TYPE_TEXT_FLAG_CAP_CHARACTERS);
        submit.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            String routename = eroutename.getText().toString().trim();
            if (!routename.equalsIgnoreCase(".") && !routename.isEmpty()) {
                if (InvalidRouteName(routename)) {
                    Utils.showToast(mContext, "Route name already exist.\nPlease enter another route name");
                } else {
                    mNewRouteName = routename.toUpperCase();
                    String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    mNewRouteCode = "NRT/" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                    RouteDetails routeObj = new RouteDetails();
                    routeObj.setRouteCode(mNewRouteCode);
                    routeObj.setRouteName(mNewRouteName);
                    routeObj.setEmployeeCode(Constants.employeeDetailObject.getEmpCode());
                    routeObj.setDistributorCode(mCustomerCode);
                    ArrayList<RouteDetails> routeList = new ArrayList<>();
                    routeList.add(routeObj);
                    mAceDnsDatabase.insertToRouteMaster(routeList, "");
                    mAceDnsDatabase.InsertToDistributorRouteMaster(routeList);
                    Constants.isNewRoute = true;
                    routeDialog.cancel();
                    ArrayList<RouteDetails> routelist = mAceDnsDatabase.getDistributorRouteList(mCustomerCode, mVisitDate);
                    ShowRouteListDialog(date, routelist);
                }
            } else {
                Utils.showToast(mContext, "Please provide valid route name");
            }
        });

        ImageView cancelDialog = routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());
        routeDialog.show();
    }

    private boolean InvalidRouteName(String routename) {
        ArrayList<RouteDetails> routelist = mAceDnsDatabase.getRouteList();
        boolean isrouteexist = false;
        for (int count = 0; count < routelist.size(); count++) {
            if (routelist.get(count).getRouteName().equalsIgnoreCase(routename)) {
                isrouteexist = true;
                break;
            }
        }
        return isrouteexist;
    }

    /*
     * Shown when a Date is long clicked
     */
    @SuppressLint("SetTextI18n")
    public void DistributorInExistingRoutePlan(final Date date) {
        final String visitDate = formatter.format(date);
        ArrayList<CustomerDetails> mCustomerList = mAceDnsDatabase.GetDistributorCustomerListforRoutePlan(visitDate);
        if (mCustomerList != null && !mCustomerList.isEmpty()) {
            final Dialog editDialog = new Dialog(ActivityDistributorRoutePlan.this, R.style.PauseDialog);
            editDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            editDialog.setContentView(R.layout.modify_route);
            editDialog.setCancelable(false);
            TextView title = editDialog.findViewById(R.id.title);
            title.setText("Distributor in existing plan");
            final ListView dialogList = editDialog.findViewById(R.id.list);
            final NewCustomerAdapter adapter = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerList);
            dialogList.setAdapter(adapter);

            dialogList.setOnItemClickListener((parent, view, position, id) -> {
                Constants.selectedCustomer = adapter.getItem(position);
                assert Constants.selectedCustomer != null;
                mCustomerName = Constants.selectedCustomer.getCustomerName();
                mCustomerCode = Constants.selectedCustomer.getCustomerCode();
                ArrayList<RouteDetails> routelist = mAceDnsDatabase.getPlanForToday(visitDate, mCustomerCode);
                if (!routelist.isEmpty()) {
                    ShowRoutePlanListDialog(date, routelist, "edit");
                } else {
                    //New Code
                    ArrayList<RouteDetails> routeList = mAceDnsDatabase.getDistributorRouteList(mCustomerCode, visitDate);
                    if (!routeList.isEmpty()) {
                        ShowRouteListDialog(date, routeList);
                    } else {
                        ShowAlert(date);
                    }
                }
                editDialog.cancel();
            });

            Button add = editDialog.findViewById(R.id.button1);
            add.setVisibility(View.INVISIBLE);
            ImageView cancelDialog = editDialog.findViewById(R.id.image_cancel);
            cancelDialog.setOnClickListener(arg0 -> editDialog.cancel());
            editDialog.show();
        } else {
            ShowDistributorCustomerListDialog(date);
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowDistributorCustomerListDialog(final Date date) {
        mVisitDate = formatter.format(date);
        mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlanSingleOrMultipleDistributor("");
        if (!mCustomerDetailsList.isEmpty()) {
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.choose_customer_search);
            mDialogCustomer.setCancelable(false);
            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText("Please select a distributor");
            final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
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
            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(adapterCust);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                assert Constants.selectedCustomer != null;
                mCustomerName = Constants.selectedCustomer.getCustomerName();
                mCustomerCode = Constants.selectedCustomer.getCustomerCode();
                ArrayList<RouteDetails> routelist = mAceDnsDatabase.getDistributorRouteList(mCustomerCode, mVisitDate);
                ShowRouteListDialog(date, routelist);
            });
            Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            mDialogCustomer.show();

        } else {
            Utils.showToast(mContext, "No distributor found.\nPlease Synchronize Data");
        }
    }

    private void ShowAlert(final Date date) {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(ActivityDistributorRoutePlan.this);
        alertDialogBuilder
                .setMessage("Do you want to create new route for route plan?")
                .setCancelable(false)
                .setTitle("Warning")
                .setPositiveButton("Yes",
                        (dialog, id) -> {
                            AddNewRoute(date);
                            dialog.cancel();
                        })
                .setNegativeButton("No", (dialog, id) -> dialog.cancel());
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    private void ShowRoutePlanListDialog(final Date date, final ArrayList<RouteDetails> routeList, final String mode) {
        final String visitDate = formatter.format(date);
        final Dialog routeDialog = new Dialog(ActivityDistributorRoutePlan.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        if (mode.equalsIgnoreCase("show")) {
            title.setText("Route plan list of " + mCustomerName);
        } else {
            title.setText("Edit : Route plan list of " + mCustomerName);
        }
        ListView dialogList = routeDialog.findViewById(R.id.list);
        final RouteAdapter adapter = new RouteAdapter(ActivityDistributorRoutePlan.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {

            if (mode.equalsIgnoreCase("edit")) {
                AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(ActivityDistributorRoutePlan.this);
                alertDialogBuilder
                        .setMessage("Are you sure that you want to delete this route??")
                        .setCancelable(false)
                        .setTitle("Warning")
                        .setPositiveButton("Yes", (dialog, id) -> {
                            String timeStamp = new SimpleDateFormat("yyyyMMddHHmmss").format(Calendar.getInstance().getTime());
                            selectedRouteList = new ArrayList<>();
                            RouteDetails mRouteDetails = adapter.getItem(arg2);
                            RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                            selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                            selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                            assert mRouteDetails != null;
                            selectedObj.setRoutecode(mRouteDetails.getRouteCode());
                            selectedObj.setRouteName(mRouteDetails.getRouteName());
                            selectedObj.setVisitDate(formatter.format(date));
                            selectedObj.setDistributorCode(mCustomerCode);
                            selectedObj.setCreateDate(formatter1.format(Calendar.getInstance().getTime()));
                            selectedObj.setStatus("inactive");
                            selectedRouteList.add(selectedObj);

                            mAceDnsTransactionDatabase.updateFlagInRouteTransaction(visitDate);
                            routeList.remove(routeList.get(arg2));
                            adapter.notifyDataSetChanged();
                            routePlanEdited = true;

                            if (!selectedRouteList.isEmpty()) {
                                mCaldroidFragment.setBackgroundResourceForDate(R.color.green, date);
                                mCaldroidFragment.refreshView();
                                SaveRoutePlan();
                                routePlanEdited = true;
                            }
                            dialog.cancel();
                        })
                        .setNegativeButton("No", (dialog, id) -> dialog.cancel());
                AlertDialog alertDialog = alertDialogBuilder.create();
                alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                alertDialog.show();
            }
        });
        Button newroute = routeDialog.findViewById(R.id.btn_cncl);
        if (mode.equalsIgnoreCase("show")) {
            newroute.setVisibility(View.INVISIBLE);
        }
        newroute.setText("Add Route in Route Plan");
        newroute.setOnClickListener(arg0 -> {
            routeDialog.cancel();
            ArrayList<RouteDetails> routelist = mAceDnsDatabase.getDistributorRouteList(mCustomerCode, visitDate);
            if (!routelist.isEmpty()) {
                ShowRouteListDialog(date, routelist);
            } else {
                ShowAlert(date);
            }
        });
        ImageView cancelDialog = routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());

        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    private void ShowRouteListDialog(final Date date, ArrayList<RouteDetails> routeItemsList) {
        final Dialog routeDialog = new Dialog(ActivityDistributorRoutePlan.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_multiple_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Distributor " + mCustomerName + " route list");
        Button submit = routeDialog.findViewById(R.id.button1);
        final ListView dialogList = routeDialog.findViewById(R.id.list);
        dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        final RouteAdapter routeAdapter = new RouteAdapter(ActivityDistributorRoutePlan.this, R.layout.multiple_route_child, routeItemsList);
        dialogList.setAdapter(routeAdapter);
        submit.setOnClickListener(arg0 -> {
            RouteDetails obj;
            selectedRouteList = new ArrayList<>();
            final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
            int checkedItemsCount = checkedItems.size();
            if (checkedItemsCount > 0) {
                for (int i = 0; i < checkedItemsCount; ++i) {
                    int position = checkedItems.keyAt(i);
                    if (checkedItems.valueAt(i)) {
                        obj = routeAdapter.getItem(position);
                        RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                        selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + mTimeStamp);
                        selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                        assert obj != null;
                        selectedObj.setRoutecode(obj.getRouteCode());
                        selectedObj.setRouteName(obj.getRouteName());
                        selectedObj.setVisitDate(formatter.format(date));
                        selectedObj.setDistributorCode(mCustomerCode);
                        selectedObj.setStatus("active");
                        selectedObj.setCreateDate(formatter1.format(Calendar.getInstance().getTime()));
                        selectedRouteList.add(selectedObj);
                    }
                }
            } else {
                Utils.showToast(mContext, "Please select route");
            }
            if (!selectedRouteList.isEmpty()) {
                routePlanEdited = true;
                mAceDnsTransactionDatabase.updateFlagInRouteTransaction(formatter.format(date));

                SaveRoutePlan();
                routeDialog.cancel();
            } else {
                Utils.showToast(mContext, "Please select route");
            }

        });
        Button createroute = routeDialog.findViewById(R.id.create_route);
        if (Constants.orderFormDetailsObj.getAddCustomerRouteCreation().equalsIgnoreCase("yes")) {
            createroute.setVisibility(View.VISIBLE);
        } else {
            createroute.setVisibility(View.GONE);
        }
        createroute.setOnClickListener(v -> {
            AddNewRoute(date);
            routeDialog.cancel();
        });

        ImageView cancelDialog = routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());
        routeDialog.show();
    }

    /*
     * Shown at various times with diff msg
     */
    public void ShowInfoDialog(String msg) {
        final Dialog infoDialog = new Dialog(ActivityDistributorRoutePlan.this, R.style.PauseDialog);
        infoDialog.setCancelable(false);
        infoDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        infoDialog.setContentView(R.layout.welcome_dialog);
        TextView txtMsg = infoDialog.findViewById(R.id.text);
        txtMsg.setText(msg);
        Button okk = infoDialog.findViewById(R.id.btn_ok);
        okk.setOnClickListener(arg0 -> infoDialog.cancel());
        infoDialog.show();
    }

    public Date getPreviousDate() {
        Calendar cal = Calendar.getInstance();
        cal.setTime(new Date());
        cal.add(Calendar.DATE, -1);
        return cal.getTime();
    }

    public void SaveRoutePlan() {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Saving Data.Please wait..");
        mProgressDialog.show();
        new Thread() {
            public void run() {
                mAceDnsTransactionDatabase.insertToRoutePlanMasterTable(selectedRouteList);
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    private boolean CheckDateValidity(Date selectedDate) {
        Date todayDate = new Date();
        Calendar cal1 = new GregorianCalendar();
        cal1.setTime(todayDate);
        Calendar cal2 = new GregorianCalendar();
        cal2.setTime(selectedDate);
        long time1Millis = cal1.getTimeInMillis();
        long time2Millis = cal2.getTimeInMillis();
        double d1 = ((double) time1Millis) / (1000 * 60 * 60 * 24);
        double d2 = ((double) time2Millis) / (1000 * 60 * 60 * 24);
        double differenceInMonth = Math.floor((d2 - d1) / 30);
        int monthVal = selectedDate.getMonth() + 1;
        boolean validity = false;
        if (routePlanPeriodValue != null && routePlanPeriodValue.length > 0) {
            for (String s : routePlanPeriodValue) {
                if ((monthVal == Integer.parseInt(s)) && differenceInMonth < 12) {
                    validity = true;
                }
            }
        } else {
            validity = true;
        }
        return validity;
    }

    @SuppressLint("SimpleDateFormat")
    private void ShowAccessMonthDialog(String parentText) {
        String year = new SimpleDateFormat("yyyy").format(Calendar.getInstance().getTime());
        int month = new Date().getMonth();
        ArrayList<String> accessMonthList = new ArrayList<>();
        for (String s : routePlanPeriodValue) {
            if ((Integer.parseInt(s) - 1) > month) {
                accessMonthList.add(monthArray[Integer.parseInt(s) - 1] + ", " + year);
            } else {
                accessMonthList.add(monthArray[Integer.parseInt(s) - 1] + ", " + (Integer.parseInt(year) + 1));
            }
        }
        final Dialog routePlanAccessDialog = new Dialog(ActivityDistributorRoutePlan.this, R.style.PauseDialog);
        routePlanAccessDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanAccessDialog.setContentView(R.layout.select_from_list);
        routePlanAccessDialog.setCancelable(false);
        TextView title = routePlanAccessDialog.findViewById(R.id.title);
        title.setText(parentText);
        ListView dialogList = routePlanAccessDialog.findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(ActivityDistributorRoutePlan.this, R.layout.routeplan_access_dialog_child, accessMonthList);
        dialogList.setAdapter(adapter1);
        Button cancel = routePlanAccessDialog.findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(arg0 -> routePlanAccessDialog.cancel());
        routePlanAccessDialog.show();
    }
}
