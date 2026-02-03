package com.forcepower.acedns.activity.non_auth.main_menu.route_plan;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;
import androidx.fragment.app.FragmentTransaction;

import android.util.SparseBooleanArray;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.adapter.CustomerTypeAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRouteCustomerPlan;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.RoutePlanCustomer;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;
import java.util.Objects;

public class ActivityRoutePlan extends FragmentActivity implements OnClickListener {

    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewLogo = null;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    SimpleDateFormat formatter;
    SimpleDateFormat formatterbackend;
    boolean firstTimeCall = true;
    boolean routePlanEdited = false;
    Dialog infoDialog;
    Context mContext;
    ArrayList<RoutePlanMasterDetails> selectedRouteList;
    ArrayList<RoutePlanMasterDetails> routePlanList;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<RoutePlanCustomer> mRoutePlanCustomerList;
    RoutePlanDetails mRoutePlanDetails;
    String mTimeStamp = "";
    String mRoutePlan = "";
    String MODE = "";
    String[] routePlanPeriodValue = null;
    String[] monthArray = {"January", "February", "March", "April", "May",
            "June", "July", "August", "September", "October", "November", "December"};
    int checkedItemsCount = 0;
    private CaldroidFragment mCaldroidFragment;
    private Handler mHandler;
    private ProgressDialog mProgressDialog;

    @SuppressLint({"SimpleDateFormat", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        setContentView(R.layout.activity_route_plan);
        RegisterActivities.registerActivity(this);

        formatter = new SimpleDateFormat("dd-MM-yyyy");
        formatterbackend = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        mContext = ActivityRoutePlan.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        routePlanList = mAceDnsDatabase.getRoutePlanList();

        MODE = getIntent().getStringExtra("MODE");

        mRoutePlanDetails = mAceDnsDatabase.getRoutePlanDetailsObj();

        /*
         * Showing some informative Dialogues.
         */
        if (MODE.equalsIgnoreCase("CREATE")) {
            if (mRoutePlanDetails.getRoutePlanAccessPeriod().equalsIgnoreCase("yes")) {
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
        }

        InitializeView();

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String response = msg.getData().getString("message");
                assert response != null;
                if (response.equalsIgnoreCase("SubmitJobDone")) {
                    mProgressDialog.cancel();
                }
            }
        };

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

        mButtonBack.setOnClickListener(ActivityRoutePlan.this);
        mButtonSubmit.setOnClickListener(ActivityRoutePlan.this);

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
                ShowRoutePlansForToday(date);
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
                            ShowAccessMonthDialog("You are allowed to make PJP Plan for the Months :");
                        }
                    }
                    firstTimeCall = false;
                }
            }

            @Override
            public void onLongClickDate(Date date, View view) {
                Date todayDate = new Date();
                if (MODE.equalsIgnoreCase("CREATE")) {
                    if (date.before(getPreviousDate())) {
                        Utils.showToast(mContext, "RoutePlan cannot be done for previous Dates.");
                    } else {
                        boolean monthCondition = CheckDateValidity(date);
                        if (mRoutePlanDetails != null && mRoutePlanDetails.getRoutePlanDeviation().equalsIgnoreCase("yes")) {
                            if (!formatter.format(date).equalsIgnoreCase(formatter.format(todayDate))) {
                                if (monthCondition) {
                                    if (mRoutePlanDetails.getRoutePlanFlow().equalsIgnoreCase("no")) {
                                        AddOrEditRoutePlan(date, "");
                                    }
                                } else {
                                    ShowAccessMonthDialog("Please note You are allowed to make Route Plan for  the Months :");
                                }
                            } else {
                                Utils.showToast(mContext, "For same date - SelectPJP Deviation.");
                            }
                        } else {
                            if (monthCondition) {
                                assert mRoutePlanDetails != null;
                                if (mRoutePlanDetails.getRoutePlanFlow().equalsIgnoreCase("no")) {
                                    AddOrEditRoutePlan(date, "");
                                }
                            } else {
                                ShowAccessMonthDialog("Please note You are allowed to make Route Plan for  the Months :");
                            }
                        }
                    }
                }
            }
        };
        mCaldroidFragment.setCaldroidListener(listener);
        HighlightPlannedDate();
    }

    /*
     * OnClick Listner
     */
    @Override
    public void onClick(View arg0) {
        if (arg0 == mButtonBack) {
            if (routePlanEdited) {
                Utils.showToast(mContext, "Please submit the route plan");
            } else {
                finish();
            }
        } else if (arg0 == mButtonSubmit) {
            ArrayList<RoutePlanMasterDetails> unUploadedRoute = mAceDnsTransactionDatabase.getUnuploadedRoute();
            if (!unUploadedRoute.isEmpty()) {
                mButtonSubmit.setEnabled(false);
                new TRANS_SubmitRouteCustomerPlan(ActivityRoutePlan.this, true, "SUBMIT").execute();
            } else {
                Utils.showToast(mContext, "You haven't made any modification to the Route Plan.");
            }
        }
    }

    /*
     * Date Colouration Method
     */
    @SuppressLint({"SimpleDateFormat"})
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
    public void ShowRoutePlansForToday(Date date) {
        final String visitDate = formatter.format(date);
        ArrayList<RoutePlanMasterDetails> routeListForToday = mAceDnsTransactionDatabase.getPlanForToday(visitDate);
        if (routeListForToday != null && !routeListForToday.isEmpty()) {
            final Dialog planDialog = new Dialog(ActivityRoutePlan.this, R.style.PauseDialog);
            planDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            planDialog.setContentView(R.layout.select_multiple_from_list);
            planDialog.setCancelable(false);
            TextView title = planDialog.findViewById(R.id.title);
            title.setText("Route list of today plan");
            final ListView dialogList = planDialog.findViewById(R.id.list);
            final RoutePlanTransAdapter routeAdapter = new RoutePlanTransAdapter(ActivityRoutePlan.this, R.layout.route_list_child, routeListForToday, true);
            dialogList.setAdapter(routeAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                String routecode = Objects.requireNonNull(routeAdapter.getItem(arg2)).getRoutecode();
                mRoutePlan = Objects.requireNonNull(routeAdapter.getItem(arg2)).getRouteName();
                planDialog.cancel();
                ShowCustomerListiRoutePlan(visitDate, routecode);
            });
            Button submit = planDialog.findViewById(R.id.button1);
            submit.setVisibility(View.INVISIBLE);
            submit.setText("  OK  ");
            submit.setOnClickListener(arg0 -> planDialog.cancel());
            ImageView cancelDialog = planDialog.findViewById(R.id.image_cancel);
            cancelDialog.setVisibility(View.VISIBLE);
            cancelDialog.setOnClickListener(arg0 -> planDialog.cancel());
            planDialog.show();
        } else {
            ShowInfoDialog("You have no route Plans for this date. \n Long press date to create a route plan.");
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListiRoutePlan(final String visitdate, final String routecode) {
        //http://adanware.blogspot.in/2012/04/android-multiple-selection-listview.html
        mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(visitdate, routecode);
        final Dialog customerDialog = new Dialog(ActivityRoutePlan.this, R.style.PauseDialog);
        customerDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerDialog.setContentView(R.layout.select_multiple_from_list);
        customerDialog.setCancelable(false);
        TextView title = customerDialog.findViewById(R.id.title);
        title.setText("Customer list of route " + mRoutePlan);
        Button submit = customerDialog.findViewById(R.id.button1);
        submit.setVisibility(View.INVISIBLE);
        final ListView list = customerDialog.findViewById(R.id.list);
        final CustomerTypeAdapter adapterCust = new CustomerTypeAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
        list.setAdapter(adapterCust);
        list.setOnItemClickListener((parent, view, position, id) -> Toast.makeText(mContext, "Please long press date to add or edit route plan.", Toast.LENGTH_LONG).show());
        Button create_route = customerDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        ImageView cancelDialog = customerDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> customerDialog.cancel());
        customerDialog.show();
    }

    /*
     * Shown when a Date is long clicked
     */
    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void AddOrEditRoutePlan(final Date date, final String selectedRDS) {
        final ArrayList<RouteDetails> routeList;
        if (!selectedRDS.isEmpty()) {
            routeList = mAceDnsDatabase.getRouteListRDSWise(selectedRDS);
        } else {
            routeList = mAceDnsDatabase.getRouteList();
        }
        final String visitDate = formatter.format(date);
        final ArrayList<RoutePlanMasterDetails> routeListForToday = mAceDnsTransactionDatabase.getPlanForToday(visitDate);
        if (routeListForToday != null && !routeListForToday.isEmpty()) {
            final Dialog editDialog = new Dialog(ActivityRoutePlan.this, R.style.PauseDialog);
            editDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            editDialog.setContentView(R.layout.modify_route);
            editDialog.setCancelable(true);
            TextView title = editDialog.findViewById(R.id.title);
            title.setText("Edit : Route in existing plan");
            final ListView dialogList = editDialog.findViewById(R.id.list);
            final RoutePlanTransAdapter routeAdapter = new RoutePlanTransAdapter(ActivityRoutePlan.this, R.layout.route_list_child, routeListForToday, true);
            dialogList.setAdapter(routeAdapter);
            dialogList.setLongClickable(true);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mTimeStamp = new SimpleDateFormat("yyyyMMddHHmmss").format(Calendar.getInstance().getTime());
                String routecode = routeListForToday.get(arg2).getRoutecode();
                mRoutePlan = routeListForToday.get(arg2).getRouteName();
                editDialog.cancel();
                ShowCustomerListForDeleteDialog(date, visitDate, routecode);
            });

            dialogList.setOnItemLongClickListener((parent, view, position, id) -> true);

            Button add = editDialog.findViewById(R.id.button1);
            add.setText("Add New Route");
            add.setOnClickListener(arg0 -> {
                ArrayList<RouteDetails> newPlanList = new ArrayList<>();
                for (int ii = 0; ii < routeList.size(); ii++) {
                    String route_code = routeList.get(ii).getRouteCode();
                    if (!checkPlace(routeListForToday, route_code)) {
                        newPlanList.add(routeList.get(ii));
                    }
                }
                if (!newPlanList.isEmpty()) {
                    editDialog.cancel();
                    ShowRouteListDialog(date, newPlanList);
                } else {
                    editDialog.cancel();
                    Toast.makeText(mContext, "No more route to add in route plan", Toast.LENGTH_LONG).show();
                }
            });
            ImageView cancelDialog = editDialog.findViewById(R.id.image_cancel);
            cancelDialog.setOnClickListener(arg0 -> {
                if (!routeListForToday.isEmpty()) {
                    editDialog.cancel();
                } else {
                    Toast.makeText(mContext, "Please add new route", Toast.LENGTH_LONG).show();
                }
            });
            editDialog.show();
        } else {
            ShowRouteListDialog(date, routeList);
        }
    }

    /*
     * Called by addOrEditRoutePlan method to Add plan for a date
     */
    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void ShowRouteListDialog(final Date date, final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(ActivityRoutePlan.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        final RouteAdapter adapter = new RouteAdapter(ActivityRoutePlan.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, pos, arg3) -> {
            routeDialog.cancel();
            mTimeStamp = new SimpleDateFormat("yyyyMMddHHmmss").format(Calendar.getInstance().getTime());
            RouteDetails routeobj;
            selectedRouteList = new ArrayList<>();
            routeobj = adapter.getItem(pos);
            RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
            selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + mTimeStamp);
            selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
            assert routeobj != null;
            selectedObj.setRoutecode(routeobj.getRouteCode());
            selectedObj.setRouteName(routeobj.getRouteName());
            selectedObj.setVisitDate(formatter.format(date));
            selectedObj.setStatus("active");
            selectedObj.setCreateDate(formatterbackend.format(Calendar.getInstance().getTime()));
            selectedRouteList.add(selectedObj);

            mCaldroidFragment.setBackgroundResourceForDate(R.color.green, date);
            mCaldroidFragment.refreshView();
            String routecode = routeobj.getRouteCode();
            ShowCustomerListDialog(date, routecode, "");
            routeDialog.cancel();
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        ImageView cancelDialog = routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());

        Button create_route = routeDialog.findViewById(R.id.create_route);
        if (Constants.orderFormDetailsObj.getAddCustomerRouteCreation().equalsIgnoreCase("yes")) {
            create_route.setVisibility(View.VISIBLE);
        } else {
            create_route.setVisibility(View.GONE);
        }
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListForDeleteDialog(final Date date, final String visitdate, final String routecode) {
        //http://adanware.blogspot.in/2012/04/android-multiple-selection-listview.html
        mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(visitdate, routecode);
        final Dialog customerDialog = new Dialog(ActivityRoutePlan.this, R.style.PauseDialog);
        customerDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerDialog.setContentView(R.layout.select_multiple_from_list);
        customerDialog.setCancelable(false);
        TextView title = customerDialog.findViewById(R.id.title);
        title.setText("Edit : Existing customer of route " + mRoutePlan);
        Button submit = customerDialog.findViewById(R.id.button1);
        final ListView checkboxlist = customerDialog.findViewById(R.id.list);
        checkboxlist.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        final CustomerTypeAdapter adapterCust = new CustomerTypeAdapter(mContext, R.layout.multiple_customer_child, mCustomerDetailsList);
        checkboxlist.setAdapter(adapterCust);
        submit.setOnClickListener(arg0 -> {
            adapterCust.notifyDataSetChanged();
            mRoutePlanCustomerList = new ArrayList<>();
            final SparseBooleanArray checkedItems = checkboxlist.getCheckedItemPositions();
            checkedItemsCount = 0;
            for (int i = 0; i < checkboxlist.getCheckedItemPositions().size(); i++) {
                if (checkedItems.valueAt(i)) {
                    checkedItemsCount++;
                }
            }
            if (checkedItemsCount > 0) {
                AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(ActivityRoutePlan.this);
                alertDialogBuilder.setMessage("Are you sure that you want to delete selected customer?")
                        .setCancelable(false)
                        .setTitle("Warning")
                        .setPositiveButton("Yes", (dialog, id) -> {
                            mRoutePlanCustomerList = new ArrayList<>();
                            if (checkedItemsCount == mCustomerDetailsList.size()) {
                                for (int i = 0; i < checkedItemsCount; ++i) {
                                    int position = checkedItems.keyAt(i);
                                    if (checkedItems.valueAt(i)) {
                                        CustomerDetails customerObj = adapterCust.getItem(position);
                                        RoutePlanCustomer selectedObj = new RoutePlanCustomer();
                                        selectedObj.setTranSactionId("RP" + Constants.employeeDetailObject.getEmpCode() + mTimeStamp);
                                        assert customerObj != null;
                                        selectedObj.setRouteCode(customerObj.getRouteCode());
                                        selectedObj.setVisitDate(visitdate);
                                        selectedObj.setStatus("inactive");
                                        selectedObj.setCustomerCode(customerObj.getCustomerCode());
                                        mRoutePlanCustomerList.add(selectedObj);
                                    }
                                }
                                selectedRouteList = new ArrayList<>();
                                RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                                selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + mTimeStamp);
                                selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                                selectedObj.setRoutecode(routecode);
                                selectedObj.setRouteName(mRoutePlan);
                                selectedObj.setVisitDate(visitdate);
                                selectedObj.setStatus("inactive");
                                selectedObj.setCreateDate(formatterbackend.format(Calendar.getInstance().getTime()));
                                selectedRouteList.add(selectedObj);

                            } else {
                                for (int i = 0; i < checkedItemsCount; ++i) {
                                    int position = checkedItems.keyAt(i);
                                    if (checkedItems.valueAt(i)) {
                                        CustomerDetails customerObj = adapterCust.getItem(position);
                                        RoutePlanCustomer selectedObj = new RoutePlanCustomer();
                                        selectedObj.setTranSactionId("RP" + Constants.employeeDetailObject.getEmpCode() + mTimeStamp);
                                        assert customerObj != null;
                                        selectedObj.setRouteCode(customerObj.getRouteCode());
                                        selectedObj.setVisitDate(visitdate);
                                        selectedObj.setStatus("inactive");
                                        selectedObj.setCustomerCode(customerObj.getCustomerCode());
                                        mRoutePlanCustomerList.add(selectedObj);
                                    }
                                }
                                selectedRouteList = new ArrayList<>();
                                RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                                selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + mTimeStamp);
                                selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                                selectedObj.setRoutecode(routecode);
                                selectedObj.setRouteName(mRoutePlan);
                                selectedObj.setVisitDate(visitdate);
                                selectedObj.setStatus("active");
                                selectedObj.setCreateDate(formatterbackend.format(Calendar.getInstance().getTime()));
                                selectedRouteList.add(selectedObj);
                            }
                            dialog.cancel();
                            customerDialog.cancel();
                            if (!selectedRouteList.isEmpty() && !mRoutePlanCustomerList.isEmpty()) {
                                ModifyRoute();
                                mAceDnsTransactionDatabase.updateFlagInRouteTransaction(visitdate);
                                customerDialog.cancel();
                                routePlanEdited = true;
                            } else {
                                Utils.showToast(mContext, "Please select atleast one route");
                            }
                        })
                        .setNegativeButton("No", (dialog, id) -> {
                            dialog.cancel();
                            customerDialog.cancel();
                        });
                AlertDialog alertDialog = alertDialogBuilder.create();
                alertDialog.show();
            } else {
                Toast.makeText(mContext, "Please select customer", Toast.LENGTH_LONG).show();
            }
        });
        Button createroute = customerDialog.findViewById(R.id.create_route);
        createroute.setVisibility(View.VISIBLE);
        createroute.setText("ADD CUSTOMER");
        createroute.setOnClickListener(v -> {
            customerDialog.cancel();
            selectedRouteList = new ArrayList<>();
            RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
            selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + mTimeStamp);
            selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
            selectedObj.setRoutecode(routecode);
            selectedObj.setRouteName(mRoutePlan);
            selectedObj.setVisitDate(visitdate);
            selectedObj.setStatus("active");
            selectedObj.setCreateDate(formatterbackend.format(Calendar.getInstance().getTime()));
            selectedRouteList.add(selectedObj);
            ShowCustomerListDialog(date, routecode, "edit");
        });
        ImageView cancelDialog = customerDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> customerDialog.cancel());
        customerDialog.show();
    }

    @SuppressLint("SetTextI18n")
    private void ShowCustomerListDialog(final Date date, String routecode, String mode) {
        if (mode.equalsIgnoreCase("edit")) {
            mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforEditedRoutePlan(routecode, formatter.format(date));
        } else {
            mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(routecode);
        }
        if (!mCustomerDetailsList.isEmpty()) {
            final Dialog customerDialog = new Dialog(ActivityRoutePlan.this, R.style.PauseDialog);
            customerDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            customerDialog.setContentView(R.layout.select_multiple_from_list);
            customerDialog.setCancelable(false);
            TextView title = customerDialog.findViewById(R.id.title);
            title.setText("Please select customer");
            Button submit = customerDialog.findViewById(R.id.button1);
            final ListView checkboxlist = customerDialog.findViewById(R.id.list);
            checkboxlist.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
            final CustomerTypeAdapter adapterCust = new CustomerTypeAdapter(mContext, R.layout.multiple_customer_child, mCustomerDetailsList);
            checkboxlist.setAdapter(adapterCust);
            submit.setOnClickListener(arg0 -> {
                CustomerDetails customerObj;
                mRoutePlanCustomerList = new ArrayList<>();
                final SparseBooleanArray checkedItems = checkboxlist.getCheckedItemPositions();
                int checkedItemsCount = checkedItems.size();
                if (checkedItemsCount > 0) {
                    for (int i = 0; i < checkedItemsCount; ++i) {
                        int position = checkedItems.keyAt(i);
                        if (checkedItems.valueAt(i)) {
                            customerObj = adapterCust.getItem(position);
                            RoutePlanCustomer selectedObj = new RoutePlanCustomer();
                            selectedObj.setTranSactionId("RP" + Constants.employeeDetailObject.getEmpCode() + mTimeStamp);
                            assert customerObj != null;
                            selectedObj.setRouteCode(customerObj.getRouteCode());
                            selectedObj.setVisitDate(formatter.format(date));
                            selectedObj.setStatus("active");
                            selectedObj.setCustomerCode(customerObj.getCustomerCode());
                            mRoutePlanCustomerList.add(selectedObj);
                        }
                    }
                } else {
                    Utils.showToast(mContext, "Please select at-least one route");
                }
                if (!selectedRouteList.isEmpty() && !mRoutePlanCustomerList.isEmpty()) {
                    mCaldroidFragment.setBackgroundResourceForDate(R.color.green, date);
                    mCaldroidFragment.refreshView();
                    ModifyRoute();
                    mAceDnsTransactionDatabase.updateFlagInRouteTransaction(formatter.format(date));
                    customerDialog.cancel();
                    routePlanEdited = true;
                } else {
                    Utils.showToast(mContext, "Please select at-least one route");
                }
            });

            ImageView cancelDialog = customerDialog.findViewById(R.id.image_cancel);
            cancelDialog.setVisibility(View.VISIBLE);
            cancelDialog.setOnClickListener(arg0 -> customerDialog.cancel());

            Button create_route = customerDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            customerDialog.show();

        } else {
            Utils.showToast(mContext, "No customer exist of this route");
        }
    }

    /*
     * Shown at various times with diff msg
     */
    public void ShowInfoDialog(String msg) {
        infoDialog = new Dialog(ActivityRoutePlan.this, R.style.PauseDialog);
        infoDialog.setCancelable(false);
        infoDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        infoDialog.setContentView(R.layout.welcome_dialog);
        TextView txtMsg = infoDialog.findViewById(R.id.text);
        txtMsg.setText(msg);
        Button okk = infoDialog.findViewById(R.id.btn_ok);
        okk.setOnClickListener(arg0 -> infoDialog.cancel());
        infoDialog.show();
    }

    public boolean checkPlace(ArrayList<RoutePlanMasterDetails> routeListForToday, String route_code) {
        for (int kk = 0; kk < routeListForToday.size(); kk++) {
            if (routeListForToday.get(kk).getRoutecode().equalsIgnoreCase(route_code)) {
                return true;
            }
        }
        return false;
    }

    public Date getPreviousDate() {
        Calendar cal = Calendar.getInstance();
        cal.setTime(new Date());
        cal.add(Calendar.DATE, -1);
        return cal.getTime();
    }

    public void ModifyRoute() {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Saving Data.Please wait..");
        mProgressDialog.show();
        new Thread() {
            public void run() {
                mAceDnsTransactionDatabase.InsertToRoutePlanCustomerTransactionTable(mRoutePlanCustomerList);
                mAceDnsTransactionDatabase.insertToRoutePlanMasterTable(selectedRouteList);
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public boolean CheckDateValidity(Date selectedDate) {
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
                if ((monthVal == Integer.parseInt(s))
                        && differenceInMonth < 12) {
                    validity = true;
                }
            }
        } else {
            validity = true;
        }
        return validity;
    }

    @SuppressLint("SimpleDateFormat")
    public void ShowAccessMonthDialog(String parentText) {
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
        final Dialog routePlanAccessDialog = new Dialog(ActivityRoutePlan.this, R.style.PauseDialog);
        routePlanAccessDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanAccessDialog.setContentView(R.layout.select_from_list);
        routePlanAccessDialog.setCancelable(false);
        TextView title = routePlanAccessDialog.findViewById(R.id.title);
        title.setText(parentText);
        ListView dialogList = routePlanAccessDialog.findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(ActivityRoutePlan.this, R.layout.routeplan_access_dialog_child, accessMonthList);
        dialogList.setAdapter(adapter1);
        Button cancel = routePlanAccessDialog.findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(arg0 -> routePlanAccessDialog.cancel());
        routePlanAccessDialog.show();
    }
}
