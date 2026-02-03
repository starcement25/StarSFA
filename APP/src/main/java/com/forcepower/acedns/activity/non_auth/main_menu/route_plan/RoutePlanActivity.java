package com.forcepower.acedns.activity.non_auth.main_menu.route_plan;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.SparseBooleanArray;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.adapter.JointWorkEmployeeAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRoutePlanChangeRequest;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitRoutePlanTask;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.GregorianCalendar;
import java.util.Objects;

import androidx.annotation.NonNull;
import androidx.fragment.app.FragmentActivity;
import androidx.fragment.app.FragmentTransaction;

import static com.forcepower.acedns.R.id.submit;

public class RoutePlanActivity extends FragmentActivity implements OnClickListener {
    Date dateFinal;
    ArrayList<RouteDetails> routeListFinal = null;
    ArrayList<RoutePlanMasterDetails> routeListForTodayFinal;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewLogo = null;
    ArrayList<RouteDetails> routeListForSearching;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    String singleOrJointWork = "";
    RouteDetails mRouteDetails;
    RoutePlanDetails mRoutePlanDetails;
    RouteAdapter routeAdapterForAllROutes;
    JointWorkEmployeeAdapter JointWorkEmployeeAdapterObj;
    // private CaldroidFragment dialogCaldroidFragment;
    SimpleDateFormat formatter, formatter1;
    Dialog routeDialog;
    ArrayList<RoutePlanMasterDetails> selectedRouteList;
    ArrayList<EmployeeMasterDetails> selectedEmpList;
    String timeStamp = "";
    ArrayList<RoutePlanMasterDetails> routePlanList;
    boolean routePlanEdited = false;
    String[] routePlanPeriodValue = null;
    String[] monthArray = {"January", "February", "March", "April", "May",
            "June", "July", "August", "September", "October", "November",
            "December"};
    Date accessStartDate = null, accessEndDate = null;
    //String routePlanMonthStr = "";
    String MODE = "";
    boolean firstTimeCall = true;
    private CaldroidFragment mCaldroidFragment;
    private Handler mHandler;
    private ProgressDialog mProgressDialog;
    String routePlanApproval = "";
    ConnectionDetector cd;
    boolean isSubmit = false;
    ProgressDialog loader;

    EditText etReasonToCloseOthers;
    String currentItemReasonToCLose = "";


    @SuppressLint({"SimpleDateFormat", "HandlerLeak"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_route_plan);
        RegisterActivities.registerActivity(this);

        formatter = new SimpleDateFormat("dd-MM-yyyy");
        formatter1 = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        mContext = RoutePlanActivity.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        routePlanList = mAceDnsDatabase.getRoutePlanList();

        MODE = getIntent().getStringExtra("MODE");

        mRoutePlanDetails = mAceDnsDatabase.getRoutePlanDetailsObj();

        routePlanApproval = mRoutePlanDetails.getRoutePlanApproval();
        cd = new ConnectionDetector(mContext);
        mAceDnsDatabase.GetMarketFeedbackDetailsAll();
        //routePlanApproval = "yes";
        //Utils.showToast(mContext,routePlanApproval);

        /*
         * Showing some informative Dialogues.
         */
        if (MODE.equalsIgnoreCase("CREATE")) {
            if (mRoutePlanDetails.getRoutePlanAccessPeriod().equalsIgnoreCase("yes")) {
                String[] accessPeriodArray = mAceDnsDatabase.getRoutePlanAccessPeriod();
                if (accessPeriodArray != null && !accessPeriodArray[0].isEmpty() && !accessPeriodArray[1].isEmpty()) {
                    try {
                        accessStartDate = formatter.parse(accessPeriodArray[0]);
                        accessEndDate = formatter.parse(accessPeriodArray[1]);
                        String periodValue = accessPeriodArray[2];
                        routePlanPeriodValue = periodValue.split(",");
                        ShowAccessMonthDialog("You are allowed to make PJP Plan for the Months :");
                    } catch (Exception e) {
                        System.out.println("Exception::::::::::" + e);
                    }
                }
            }
        } else if (MODE.equalsIgnoreCase("DEVIATE")) {
            accessStartDate = null;
            accessEndDate = null;
            routePlanPeriodValue = new String[0];
            ShowAccessMonthDialog("You are allowed to make PJP Deviation Request for Today only.");
        }

        timeStamp = new SimpleDateFormat("yyyyMMddHHmmss").format(Calendar.getInstance().getTime());


        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Route Data.Please wait..");
        if (cd.isConnectingToInternet()) {
            if (routePlanApproval.toLowerCase().matches("customize")) {
                loader.show();
            }

        }

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    mProgressDialog.cancel();
                }
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    InitializeView();
                }
                if (aResponse.equalsIgnoreCase("JobDoneM")) {
                    loader.cancel();
                    mAceDnsDatabase.GetMarketFeedbackDetailsAll();

                }
            }
        };

        cd = new ConnectionDetector(mContext);
        if (cd.isConnectingToInternet()) {
            if (routePlanApproval.toLowerCase().matches("customize")) {
                getData();
            }
        }
        //Constants.marketFeedbackDetailsObj.setMf_tagging("yes");
        InitializeView();

        if (cd.isConnectingToInternet()) {
            if (Constants.marketFeedbackDetailsObjNewRoute.getMf_tagging().equalsIgnoreCase("yes") && Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("SURVEY")) {
                loader.show();
                getDataTag();
            }
        } else {
            mAceDnsDatabase.GetMarketFeedbackDetailsAll();
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

    @SuppressLint({"SetTextI18n","SimpleDateFormat"})
    public void showWorkingALoneOrWithPartnerDialog() {
        final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
        Objects.requireNonNull(dialgoCondition.getWindow()).setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

        TextView txtMsg =  dialgoCondition.findViewById(R.id.title);
        txtMsg.setText("Select Work Type.");

        final RadioGroup radioSelectionGroup =  dialgoCondition.findViewById(R.id.radioSelect);
        final RadioButton radioEdit =  dialgoCondition.findViewById(R.id.radioEdit);
        final RadioButton radioRedundant =  dialgoCondition.findViewById(R.id.radioRedundant);
        radioEdit.setText("Working alone");
        radioRedundant.setText("With others");
        radioSelectionGroup.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection =  dialgoCondition.findViewById(checkedId);
            if (radioSelection.getText().toString().trim().equalsIgnoreCase("Working alone")) {
                singleOrJointWork = "Working alone";
                getRouteListAndShow(routeListFinal, routeListForTodayFinal, null, dateFinal);
            } else {
                singleOrJointWork = "With others";
                showEmployeeToWorkWithDialog(dateFinal);
            }
            dialgoCondition.cancel();

        });
        dialgoCondition.show();
    }

    public void InitializeView() {
        mImageViewLogo =  findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            mImageViewLogo.setVisibility(View.VISIBLE);
            mImageViewLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewLogo.setVisibility(View.GONE);
        }
        mButtonBack =  findViewById(R.id.back);
        mButtonSubmit =  findViewById(submit);

        mButtonBack.setOnClickListener(RoutePlanActivity.this);
        mButtonSubmit.setOnClickListener(RoutePlanActivity.this);

        // https://github.com/roomorama/Caldroid
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
                // String text = "month: " + month + " year: " + year;
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
                } else if (MODE.equalsIgnoreCase("DEVIATE")) {
                    if ((new Date().getMonth() + 1) != month) {
                        ShowAccessMonthDialog("You are allowed to make PJP Deviation Request for Today only.");
                    }
                }
            }

            @SuppressLint("SimpleDateFormat")
            @Override
            public void onLongClickDate(Date date, View view) {
                Date todayDate = new Date();
                if (MODE.equalsIgnoreCase("CREATE")) {
                    if (date.before(getPreviousDate())) {
                        Utils.showToast(mContext, "RoutePlan cannot be done for Previous Dates.");
                    } else {
                        boolean monthCondition = checkDateValidity(date);
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
                } else if (MODE.equalsIgnoreCase("DEVIATE")) {
                    if (new SimpleDateFormat("yyyyMMdd").format(todayDate).equals(new SimpleDateFormat("yyyyMMdd").format(date))) {
                        AddOrEditRoutePlan(date, "");
                    } else {
                        ShowAccessMonthDialog("You are allowed to make PJP Deviation Request for Today only.");
                    }
                }
            }
        };
        mCaldroidFragment.setCaldroidListener(listener);
        highlightPlannedDate();
    }

    /*
     * OnClick Listner
     */
    @Override
    public void onClick(View arg0) {
        if (arg0 == mButtonBack) {
            finish();

        } else if (arg0 == mButtonSubmit) {
            new GPSTracker(mContext);
            if (Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes") && (Objects.equals(Constants.currentLat, "0.0") || Objects.equals(Constants.currentLong, "0.0"))) {
                Utils.showToast(mContext, "Please turn on location sharing first.");
            } else {

                if (routePlanApproval.toLowerCase().matches("customize1")) {
                    if (!cd.isConnectingToInternet()) {
                        Toast.makeText(mContext, "Internet connection not available. You can not submit without an active internet connection on your device.", Toast.LENGTH_LONG).show();
                    }
                } else {
                    routePlanEdited = false;
                    ArrayList<RoutePlanMasterDetails> unUploadedRoute = mAceDnsTransactionDatabase.getUnuploadedRoute();
                    if (!unUploadedRoute.isEmpty()) {
                        mButtonSubmit.setEnabled(false);
                        new TRANS_SubmitRoutePlanTask(RoutePlanActivity.this, true, "SUBMIT").execute();
                    } else {
                        if (routePlanApproval.toLowerCase().matches("customize")) {
                            if (isSubmit) {
                                Intent intent = new Intent(mContext, MenuActivity.class);
                                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                                mContext.startActivity(intent);
                            } else {
                                Utils.showToast(mContext, "You haven't made any modification to the Route Plan.");
                            }
                        } else {
                            Utils.showToast(mContext, "You haven't made any modification to the Route Plan.");
                        }
                    }
                }
            }

        }
    }

    /*
     * Date Colouration Method
     */
    @SuppressLint("SimpleDateFormat")
    public void highlightPlannedDate() {
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
        String visitdate = formatter.format(date);
        final ArrayList<RoutePlanMasterDetails> routePlanForVisitDate = mAceDnsTransactionDatabase.getPlanForTodayInRoutePlan(visitdate);
        if (routePlanForVisitDate != null && !routePlanForVisitDate.isEmpty()) {
            final ArrayList<RoutePlanMasterDetails> routePlanForVisitDateSearchArray = new ArrayList<>(routePlanForVisitDate);
            final Dialog routePlanDialog = new Dialog(RoutePlanActivity.this, R.style.PauseDialog);
            routePlanDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            routePlanDialog.setContentView(R.layout.select_multiple_from_list);
            routePlanDialog.setCancelable(false);
            TextView title =  routePlanDialog.findViewById(R.id.title);
            title.setText("You have following places to visit");
            final ListView dialogList =  routePlanDialog.findViewById(R.id.list);
            final RoutePlanTransAdapter routeAdapter = new RoutePlanTransAdapter(RoutePlanActivity.this, R.layout.route_list_child, routePlanForVisitDate, true);
            dialogList.setAdapter(routeAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> ShowInfoDialog("Long press date to delete or modify this Route Plan."));
            Button submit =  routePlanDialog.findViewById(R.id.button1);
            submit.setText("Cancel");
            submit.setOnClickListener(arg0 -> routePlanDialog.cancel());
            routePlanDialog.show();

            final EditText autoCompleteTextView1 =  routePlanDialog.findViewById(R.id.autoCompleteTextView1);
            autoCompleteTextView1.setVisibility(View.VISIBLE);
            autoCompleteTextView1.addTextChangedListener(new TextWatcher() {
                public void afterTextChanged(Editable s) {}

                public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

                public void onTextChanged(CharSequence s, int start, int before, int count) {
                    String searchString = autoCompleteTextView1.getText().toString();
                    int textLength = searchString.length();
                    //clear the initial data set
                    routePlanForVisitDate.clear();
                    for (int i = 0; i < routePlanForVisitDateSearchArray.size(); i++) {
                        String routeName = routePlanForVisitDateSearchArray.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                routePlanForVisitDate.add(routePlanForVisitDateSearchArray.get(i));
                            }
                        }
                    }
                    routeAdapter.notifyDataSetChanged();
                }
            });
        } else {
            ShowInfoDialog("You have no Route Plan for selected date. Long press on date to create Route Plan.");
        }
    }

    /*
     * Shown when a Date is long clicked
     */
    @SuppressLint("SetTextI18n")
    public void AddOrEditRoutePlan(final Date date, final String selectedRDS) {

        final ArrayList<RouteDetails> routeList;
        if (!selectedRDS.isEmpty()) {
            routeList = mAceDnsDatabase.getRouteListRDSWise(selectedRDS);
        } else {
            if (Constants.marketFeedbackDetailsObjNewRoute.getMf_tagging().equalsIgnoreCase("yes")) {
                if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("SURVEY")) {
                    routeList = mAceDnsDatabase.getRouteListForMfgTag();
                } else {
                    routeList = mAceDnsDatabase.getRouteList();
                }
            } else {
                routeList = mAceDnsDatabase.getRouteList();
            }
        }

        routeListForSearching = new ArrayList<>(routeList);
        final String visitDate = formatter.format(date);
        final ArrayList<RoutePlanMasterDetails> routeListForToday = mAceDnsTransactionDatabase.getPlanForTodayInRoutePlan(visitDate);
        if (routeListForToday != null && !routeListForToday.isEmpty()) {
            final ArrayList<RoutePlanMasterDetails> routeListForTodaySearchArray = new ArrayList<>(routeListForToday);
            final Dialog editDialog = new Dialog(RoutePlanActivity.this, R.style.PauseDialog);
            editDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            editDialog.setContentView(R.layout.modify_route);
            editDialog.setCancelable(false);
            TextView title =  editDialog.findViewById(R.id.title);
            title.setText("Routes in existing plan");
            final ListView dialogList =  editDialog.findViewById(R.id.list);
            final RoutePlanTransAdapter routeAdapter = new RoutePlanTransAdapter(RoutePlanActivity.this, R.layout.route_list_child, routeListForToday, true);
            dialogList.setAdapter(routeAdapter);
            final EditText autoCompleteTextView1 =  editDialog.findViewById(R.id.autoCompleteTextView1);
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
                    routeListForToday.clear();
                    for (int i = 0; i < routeListForTodaySearchArray.size(); i++) {
                        String routeName = routeListForTodaySearchArray.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                routeListForToday.add(routeListForTodaySearchArray.get(i));
                            }
                        }
                    }
                    routeAdapter.notifyDataSetChanged();

                }
            });
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                Date getTodaysDate = getTodaysDate();
                if (!date.after(getTodaysDate)) {
                    Toast.makeText(mContext, "Sorry, you can not delete a route plan created for today. Only future route plans can be deleted.", Toast.LENGTH_LONG).show();
                } else {
                    AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(RoutePlanActivity.this);
                    alertDialogBuilder.setTitle("Warning");
                    alertDialogBuilder.setMessage("Are you sure that you want to delete this route?")
                            .setCancelable(false)
                            .setPositiveButton("Yes", (dialog, id) -> {
                                //mAceDnsTransactionDatabase.UpdateStatusRouteTransaction(visitDate,routeListForToday.get(arg2).getRoutecode());
                                selectedRouteList = new ArrayList<>();
                                RoutePlanMasterDetails mRouteDetails = routeAdapter.getItem(position);
                                RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                                selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                                selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                                assert mRouteDetails != null;
                                selectedObj.setRoutecode(mRouteDetails.getRoutecode());
                                selectedObj.setRouteName(mRouteDetails.getRouteName());
                                selectedObj.setVisitDate(formatter.format(date));
                                selectedObj.setCreateDate(formatter1.format(Calendar.getInstance().getTime()));
                                selectedObj.setStatus("inactive");
                                selectedRouteList.add(selectedObj);
                                
                                mAceDnsTransactionDatabase.updateFlagInRouteTransaction(visitDate);
                                routeListForToday.remove(routeListForToday.get(position));
                                routeAdapter.notifyDataSetChanged();
                                routePlanEdited = true;

                                if (!selectedRouteList.isEmpty()) {
                                    mCaldroidFragment.setBackgroundResourceForDate(R.color.green, date);
                                    mCaldroidFragment.refreshView();
                                    ModifyRoute();
                                    mAceDnsTransactionDatabase.updateFlagInRouteTransaction(formatter.format(date));
                                    routePlanEdited = true;
                                }

                                dialog.cancel();
                            })
                            .setNegativeButton("No", (dialog, id) -> dialog.cancel());
                    AlertDialog alertDialog = alertDialogBuilder.create();
                    alertDialog.requestWindowFeature(Window.FEATURE_ACTION_BAR);
                    alertDialog.show();
                }
            });
            Button add =  editDialog.findViewById(R.id.button1);
            add.setText("Add New Routes in Route Plan");
            if (routePlanApproval.toLowerCase().matches("customize")) {
                add.setText("Add New Routes Request");
            }
            add.setOnClickListener(arg0 -> {
                if (Constants.menuDetailsObj.getjoint_work().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("joint_work")) {
                    editDialog.cancel();
                    if (routePlanApproval.toLowerCase().matches("customize")) {
                        add.setText("Request");
                    } else {
                        showWorkingALoneOrWithPartnerDialog();
                    }
                    dateFinal = date;
                    routeListFinal = routeList;
                    routeListForTodayFinal = routeListForToday;
                } else {
                    getRouteListAndShow(routeList, routeListForToday, editDialog, date);
                }
            });
            ImageView cancelDialog = editDialog.findViewById(R.id.image_cancel);
            cancelDialog.setOnClickListener(arg0 -> editDialog.cancel());
            editDialog.show();
        } else {
            if (Constants.menuDetailsObj.getjoint_work().equalsIgnoreCase("yes")) {
//                editDialog.cancel();
                showWorkingALoneOrWithPartnerDialog();
                dateFinal = date;
                routeListFinal = routeList;
                routeListForTodayFinal = routeListForToday;
            } else {
                showRouteListDialog(date, routeList, "new");
            }
        }
    }

    @SuppressLint("WrongConstant")
    public void getRouteListAndShow(ArrayList<RouteDetails> routeList, ArrayList<RoutePlanMasterDetails> routeListForToday, Dialog editDialog, Date date) {
        routePlanEdited = true;
        ArrayList<RouteDetails> newPlanList = new ArrayList<>();
        for (int ii = 0; ii < routeList.size(); ii++) {
            String route_code = routeList.get(ii).getRouteCode();
            if (!checkPlace(routeListForToday, route_code)) {
                newPlanList.add(routeList.get(ii));
            }
        }
        if (!newPlanList.isEmpty()) {
            if (editDialog != null)
                editDialog.cancel();
            showRouteListDialog(date, newPlanList, "update");
        } else {
            if (editDialog != null)
                editDialog.cancel();
            Toast.makeText(mContext, "No route lefts to add in route plan", Toast.LENGTH_LONG).show();
        }
    }

    /*
     * Called by addOrEditRoutePlan method to Add plan for a date
     */
    @SuppressLint("SetTextI18n")
    public void showRouteListDialog(final Date date, final ArrayList<RouteDetails> routeItemsList, String newRoute) {
        routeAdapterForAllROutes = new RouteAdapter(RoutePlanActivity.this, R.layout.multiple_route_child, routeItemsList);
        routeListForSearching = new ArrayList<>(routeItemsList);
        routeDialog = new Dialog(RoutePlanActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_multiple_from_list);
        routeDialog.setCancelable(false);
        TextView title =  routeDialog.findViewById(R.id.title);
        title.setText("Please select Route");
        LinearLayout ll =  routeDialog.findViewById(R.id.llRemarks);
        EditText etRemarks =  routeDialog.findViewById(R.id.etRemarks);

        if (!newRoute.matches("new")) {
            if (mRoutePlanDetails.getRouteplanremarks().matches("yes")) {
                ll.setVisibility(View.VISIBLE);
            }
        }
        Button submit =  routeDialog.findViewById(R.id.button1);

        if (routePlanApproval.toLowerCase().matches("customize")) {
            submit.setText("Route Plan Request");
            ll.setVisibility(View.VISIBLE);
        }

        final EditText autoCompleteTextView1 =  routeDialog.findViewById(R.id.autoCompleteTextView1);
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
                routeItemsList.clear();
                for (int i = 0; i < routeListForSearching.size(); i++) {
                    String routeName = routeListForSearching.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                    if (textLength <= routeName.length()) {
                        //compare the String in EditText with Names in the ArrayList
                        //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                        if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                            routeItemsList.add(routeListForSearching.get(i));
                        }
                    }
                }
                routeAdapterForAllROutes.notifyDataSetChanged();
            }
        });


        final ListView dialogList =  routeDialog.findViewById(R.id.list);
        dialogList.setTextFilterEnabled(true);
        if (routePlanApproval.toLowerCase().matches("customize")) {
            dialogList.setChoiceMode(ListView.CHOICE_MODE_SINGLE);
        } else {
            dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        }
        dialogList.setAdapter(routeAdapterForAllROutes);
        submit.setOnClickListener(arg0 -> {
            mRouteDetails = new RouteDetails();
            selectedRouteList = new ArrayList<>();
            final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
            int checkedItemsCount = checkedItems.size();
            if (checkedItemsCount > 0) {
                if (newRoute.matches("update") && mRoutePlanDetails.getRouteplanremarks().matches("yes")) {
                    if (!etRemarks.getText().toString().isEmpty()) {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                mRouteDetails = routeAdapterForAllROutes.getItem(position);
                                RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                                selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                                selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                                selectedObj.setRoutecode(mRouteDetails.getRouteCode());
                                selectedObj.setRouteName(mRouteDetails.getRouteName());
                                selectedObj.setVisitDate(formatter.format(date));
                                selectedObj.setCreateDate(formatter1.format(Calendar.getInstance().getTime()));
                                selectedObj.setStatus("active");
                                selectedObj.setRemarks(etRemarks.getText().toString());
                                selectedRouteList.add(selectedObj);
                            }
                        }
                    } else {
                        Utils.showToast(mContext, "Please Enter Remarks");
                    }
                } else {
                    if (routePlanApproval.toLowerCase().matches("customize")) {
                        if (!etRemarks.getText().toString().isEmpty()) {
                            for (int i = 0; i < checkedItemsCount; ++i) {
                                int position = checkedItems.keyAt(i);
                                if (checkedItems.valueAt(i)) {
                                    mRouteDetails = routeAdapterForAllROutes.getItem(position);
                                    RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                                    selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                                    selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                                    selectedObj.setRoutecode(mRouteDetails.getRouteCode());
                                    selectedObj.setRouteName(mRouteDetails.getRouteName());
                                    selectedObj.setVisitDate(formatter.format(date));
                                    selectedObj.setCreateDate(formatter1.format(Calendar.getInstance().getTime()));
                                    selectedObj.setStatus("active");
                                    if (routePlanApproval.toLowerCase().matches("customize")) {
                                        selectedObj.setRemarks(etRemarks.getText().toString());
                                    }
                                    selectedRouteList.add(selectedObj);
                                }
                            }
                        } else {
                            Utils.showToast(mContext, "Please Enter Remarks");
                        }
                    } else {
                        for (int i = 0; i < checkedItemsCount; ++i) {
                            int position = checkedItems.keyAt(i);
                            if (checkedItems.valueAt(i)) {
                                mRouteDetails = routeAdapterForAllROutes.getItem(position);
                                RoutePlanMasterDetails selectedObj = new RoutePlanMasterDetails();
                                selectedObj.setTranId("RP" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                                selectedObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                                selectedObj.setRoutecode(mRouteDetails.getRouteCode());
                                selectedObj.setRouteName(mRouteDetails.getRouteName());
                                selectedObj.setVisitDate(formatter.format(date));
                                selectedObj.setCreateDate(formatter1.format(Calendar.getInstance().getTime()));
                                selectedObj.setStatus("active");
                                if (routePlanApproval.toLowerCase().matches("customize")) {
                                    selectedObj.setRemarks(etRemarks.getText().toString());
                                }
                                selectedRouteList.add(selectedObj);
                            }
                        }
                    }
                }
            } else {
                Utils.showToast(mContext, "Please select atleast 1 route");
            }
            if (!selectedRouteList.isEmpty()) {
                if (routePlanApproval.toLowerCase().matches("customize")) {
                    //Utils.showToast(mContext, "route--" + selectedRouteList.size());
                    mCaldroidFragment.setBackgroundResourceForDate(R.color.green, date);
                    mCaldroidFragment.refreshView();
                    ModifyRoute();
                    routeDialog.cancel();
                    routePlanEdited = true;
                } else {
                    mCaldroidFragment.setBackgroundResourceForDate(R.color.green, date);
                    mCaldroidFragment.refreshView();
                    ModifyRoute();
                    mAceDnsTransactionDatabase.updateFlagInRouteTransaction(formatter.format(date));
                    routeDialog.cancel();
                    routePlanEdited = true;
                }
            }
        });
        Button create_route =  routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);

        ImageView cancelDialog =  routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());

        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void showEmployeeToWorkWithDialog(final Date date) {
        ArrayList<EmployeeMasterDetails> employeeList;
        employeeList = mAceDnsTransactionDatabase.getJointWorkEmpForToday(formatter.format(date));
        JointWorkEmployeeAdapterObj = new JointWorkEmployeeAdapter(mContext, R.layout.multiple_route_child, employeeList);

        routeDialog = new Dialog(RoutePlanActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_multiple_from_list);
        routeDialog.setCancelable(false);
        TextView title =  routeDialog.findViewById(R.id.title);
        title.setText("Please select employees to work with");
        Button submit =  routeDialog.findViewById(R.id.button1);
        final EditText autoCompleteTextView1 =  routeDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.GONE);
        autoCompleteTextView1.setVisibility(View.VISIBLE);
        final ListView dialogList =  routeDialog.findViewById(R.id.list);
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
                new TRANS_SubmitRoutePlanTask(RoutePlanActivity.this, true, "SUBMIT").execute();
            } else {
                Utils.showToast(mContext, "Please select at least 1 employee");
            }
        });
        Button create_route =  routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);

        ImageView cancelDialog =  routeDialog.findViewById(R.id.image_cancel);
        cancelDialog.setVisibility(View.VISIBLE);
        cancelDialog.setOnClickListener(arg0 -> routeDialog.cancel());

        routeDialog.show();
    }

    /*
     * Shown at various times with diff msg
     */
    @SuppressLint("SetTextI18n")
    public void showOfficeVisitLeaveRequestReasonDialog(ArrayList<Integer> officVisitLeaveRequestPos) {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setCancelable(false);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title =  instructionDialog.findViewById(R.id.title);
        title.setText("Reason for Office Visit/Leave Request?");
        final EditText edInst =  instructionDialog.findViewById(R.id.ed_input);
        final Button submit =  instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            final String remarks = edInst.getText().toString();
            for (int ii = 0; ii < officVisitLeaveRequestPos.size(); ii++) {
                int pos = officVisitLeaveRequestPos.get(ii);
                selectedRouteList.get(pos).setRemarks(remarks);
            }
            instructionDialog.cancel();
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
        });
        instructionDialog.show();
    }

    public void ShowInfoDialog(String msg) {
        final Dialog infoDialog = new Dialog(RoutePlanActivity.this, R.style.PauseDialog);
        infoDialog.setCancelable(false);
        infoDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        infoDialog.setContentView(R.layout.welcome_dialog);
        TextView txtMsg =  infoDialog.findViewById(R.id.text);
        txtMsg.setText(msg);
        Button okk =  infoDialog.findViewById(R.id.btn_ok);
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

    public Date getTodaysDate() {
        Calendar cal = Calendar.getInstance();
        cal.setTime(new Date());
        return cal.getTime();
    }

    public void ModifyRoute() {
        if (MODE.equalsIgnoreCase("DEVIATE")) {
            ShowRemarksDateDialog();
        } else {
            ArrayList<Integer> officVisitLeaveRequestPos = new ArrayList<>();

            for (int i = 0; i < selectedRouteList.size(); i++) {
                String routeName = selectedRouteList.get(i).getRouteName();
                if (routeName.equalsIgnoreCase("office Visit") || routeName.equalsIgnoreCase("leave request")) {
                    officVisitLeaveRequestPos.add(i);
                }

            }
            if (!officVisitLeaveRequestPos.isEmpty()) {
                routeDialog.cancel();
                showOfficeVisitLeaveRequestReasonDialog(officVisitLeaveRequestPos);
            } else {

                if (routePlanApproval.toLowerCase().matches("customize")) {
                    //Toast.makeText(mContext, "cc", Toast.LENGTH_SHORT).show();
                    if (cd.isConnectingToInternet()) {
                        isSubmit = true;
                        new TRANS_SubmitRoutePlanChangeRequest(RoutePlanActivity.this, true, "SUBMIT", selectedRouteList).execute();
                    } else {
                        Toast.makeText(mContext, "Internet connection not available. You can not submit without an active internet connection on your device.", Toast.LENGTH_LONG).show();
                    }

                } else {
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
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title =  instructionDialog.findViewById(R.id.title);
        title.setText("Reason for Deviation ?");
        final EditText edInst =  instructionDialog.findViewById(R.id.ed_input);
        final Button submit =  instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            final String remarks = edInst.getText().toString();
            instructionDialog.cancel();
            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Saving Data.Please wait..");
            mProgressDialog.show();
            new Thread() {
                public void run() {
                    for (int ii = 0; ii < selectedRouteList.size(); ii++) {
                        selectedRouteList.get(ii).setRemarks(remarks);
                    }
                    mAceDnsTransactionDatabase.insertToRoutePlanMasterTable(selectedRouteList);
                    Message msgObj = mHandler.obtainMessage();
                    Bundle b = new Bundle();
                    b.putString("message", "SubmitJobDone");
                    msgObj.setData(b);
                    mHandler.sendMessage(msgObj);
                }
            }.start();
        });
        instructionDialog.show();
    }

    public void ShowRemarksDateDialog() {
        final Dialog mDialogOrderWithDate = new Dialog(mContext, R.style.PauseDialog);
        mDialogOrderWithDate.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOrderWithDate.setContentView(R.layout.dialog_order_deviation_reason);
        mDialogOrderWithDate.setCancelable(false);

        etReasonToCloseOthers =  mDialogOrderWithDate.findViewById(R.id.ed_input);
        final RadioGroup RGReasonToClose =  mDialogOrderWithDate.findViewById(R.id.RGReasonToClose);
        RGReasonToClose.setOnCheckedChangeListener((group1, checkedId1) -> {
            RadioButton radioSelection =  mDialogOrderWithDate.findViewById(checkedId1);
            currentItemReasonToCLose = radioSelection.getText().toString();
            if (currentItemReasonToCLose.toLowerCase().contains("others")) {
                etReasonToCloseOthers.setVisibility(View.VISIBLE);
            } else {
                etReasonToCloseOthers.setVisibility(View.GONE);
            }
        });

        Button backButton =  mDialogOrderWithDate.findViewById(R.id.backButton);
        backButton.setOnClickListener(view -> mDialogOrderWithDate.dismiss());
        Button btn_submit =  mDialogOrderWithDate.findViewById(R.id.btn_submit);
        btn_submit.setOnClickListener(view -> {
            String remarks;
            if (currentItemReasonToCLose.equalsIgnoreCase("Others(Text Typing)")) {
                remarks = etReasonToCloseOthers.getText().toString().trim();
                if (remarks.isEmpty()) {
                    Utils.showToast(mContext, "Please type specific reason in the text field.");
                    return;
                }
            } else {
                remarks = currentItemReasonToCLose;
            }

            mProgressDialog = new ProgressDialog(mContext);
            mProgressDialog.setMessage("Saving Data.Please wait..");
            mProgressDialog.show();
            new Thread() {
                public void run() {
                    for (int ii = 0; ii < selectedRouteList.size(); ii++) {
                        selectedRouteList.get(ii).setRemarks(remarks);
                    }
                    mAceDnsTransactionDatabase.insertToRoutePlanMasterTable(selectedRouteList);
                    Message msgObj = mHandler.obtainMessage();
                    Bundle b = new Bundle();
                    b.putString("message", "SubmitJobDone");
                    msgObj.setData(b);
                    mHandler.sendMessage(msgObj);
                }
            }.start();
            mDialogOrderWithDate.dismiss();

        });

        mDialogOrderWithDate.show();
    }

    public boolean checkDateValidity(Date selectedDate) {
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
        int monthVal = selectedDate.getMonth() + 1;// We are considering 1-12 as
        // month. getMonth()
        // considers 0-11
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
        final Dialog routePlanAccessDialog = new Dialog(RoutePlanActivity.this, R.style.PauseDialog);
        routePlanAccessDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanAccessDialog.setContentView(R.layout.select_from_list);
        routePlanAccessDialog.setCancelable(false);
        TextView title =  routePlanAccessDialog.findViewById(R.id.title);
        title.setText(parentText);
        ListView dialogList =  routePlanAccessDialog.findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(RoutePlanActivity.this, R.layout.routeplan_access_dialog_child, accessMonthList);
        dialogList.setAdapter(adapter1);
        Button cancel =  routePlanAccessDialog.findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(arg0 -> routePlanAccessDialog.cancel());
        routePlanAccessDialog.show();
    }

    private void getData() {
        new Thread() {
            public void run() {
                new commonAsyncTaskMaster(mContext, "route_plan");
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    private void getDataTag() {
        new Thread() {
            public void run() {
                new commonAsyncTaskMaster(mContext, "market_feedback_tagging");
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDoneM");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }

        }.start();
    }
}
