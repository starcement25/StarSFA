package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.SparseBooleanArray;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.PopupWindow;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BagainListForDOAdapter;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.DestinationAdapter;
import com.forcepower.acedns.adapter.IncotermsAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.adapter.SwapDaysAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_PendingRoutePlanBeforeOtherTxn;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNewCustomerDetailsTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourDaySwapTransactionTask;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.DestinationMaster;
import com.forcepower.acedns.bean.OutstandingDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.bean.SaudaDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.List;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.text.HtmlCompat;

import static com.forcepower.acedns.R.id.textViewRouteValue;
import static com.forcepower.acedns.constants.Constants.dateString;
import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;
import static com.forcepower.acedns.constants.Constants.isCheckInToNewCustomer;
import static com.forcepower.acedns.constants.Constants.isSecondaryFreightIncluded;
import static com.forcepower.acedns.constants.Constants.mDepotOrPlant;
import static com.forcepower.acedns.constants.Constants.mSaudaDepoCode;
import static com.forcepower.acedns.constants.Constants.orderAuditType;
import static com.forcepower.acedns.constants.Constants.orderFormDetailsObj;
import static com.forcepower.acedns.constants.Constants.selectedBargainList;
import static com.forcepower.acedns.constants.Constants.selectedCustomerDeliveryAddress;
import static com.forcepower.acedns.constants.Constants.selectedState;
import static com.forcepower.acedns.constants.Constants.taggedCustomerCode;
import static com.forcepower.acedns.constants.Constants.transitTime;
import static com.forcepower.acedns.constants.Constants.uomToBeShownForProduct;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.daysOfWeekExceptToday;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInRoute;
import static com.forcepower.acedns.util.Utils.toTitleCase;


public class ActivityDOFilter extends AppCompatActivity implements OnClickListener {
    Spinner customerSpinner;
    public boolean isCustomerChosen = false;
    public static Button mButtonBack = null;
    public static Button mButtonSubmit = null;
    public static Button mButtonOrderType = null;
    public static Button mButtonGst = null;
    public static Button mButtonFreight = null;
    public static Button mButtonDestination = null;
    public static Button mButtonNoOrder = null;

    public static TextView mTextViewCustomerName = null;
    public static TextView tv_vehicle_no = null;
    public static TextView mTextViewDealerName = null;
    public static TextView textViewShippingCustomerTv = null;
    public static TextView textViewProduct = null;
    public static TextView textViewBargainDate = null;
    public static TextView mTextViewRouteName = null;
    public static TextView textViewCustomerVisitDay = null;
    public static TextView mTextViewCreditLimit = null;

    public static TextView mTextViewOrderType = null;
    public static TextView textViewGst = null;
    public static TextView mTextViewFreight = null;
    public static TextView mTextViewDestination = null;

    public static TextView mTextViewNoofInvoice = null;
    public static TextView mTextViewInvoiceAmount = null;
    public static TextView mTextViewDueAmount = null;
    public static TextView mTextViewVerticalValue = null;
    public static TextView mTextViewBranchValue = null;
    public static TextView customerWiseTrgtAchvmntTvMonth = null;
    public static TextView customerWiseTrgtAchvmntTvTarget = null;
    public static TextView customerWiseTrgtAchvmntTvAchievement = null;
    public static EditText ed_po = null;


    public static FrameLayout mFrameLayoutSelectCustomer = null;
    public static FrameLayout mFrameLayoutAddCustomer = null;
    public static FrameLayout mFrameLayoutSelectRoute = null;
    public static FrameLayout frameLayoutSwapDay = null;

    public static LinearLayout mLinearLayoutOutstanding = null;
    public static LinearLayout mLinearLayoutOrderType = null;
    public static LinearLayout linearLayoutGst = null;
    public static LinearLayout mLinearLayoutFreight = null;
    public static LinearLayout mLinearLayoutDestination = null;
    public static LinearLayout customerWiseTrgtAchvmntLayout = null;
    public static ImageView mImageViewHeaderLogo = null;
    public String mRouteName = "";
    RoutePlanTransAdapter RoutePlanAdapter;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    ProgressDialog mStepProgressDialog;
    Handler mStepHandler;
    public String mSaudaType = "";
    ArrayList<BranchMasterDetails> saudaRDSList = new ArrayList<>();
    public String mSaudaDepoName = "";
    String swappedDay;
    String swappedDate;
    String todaysDate;
    String visitDayString = "Visit day : ";
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofTodayForSearching;
    ArrayList<RouteDetails> mRouteDetailsList;
    ArrayList<RouteDetails> mRouteDetailsListForVisitSequence;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<BranchMasterDetails> mBranchMasterDetailsList;
    ArrayList<DestinationMaster> mDestinationMasterList;
    RouteDetails mSelectedRouteDetails;
    RouteDetails mSelectedRouteDetailsVisitSequence;
    RoutePlanMasterDetails mSelectedTodayRouteDetails;
    List<String> hintRemarksValList;
    ArrayList<SaudaDetails> BargainValList;
    ArrayList<ProductMasterDetails> productList;
    int selectedHintRemarksId = -1;
    Boolean clickedOnSelectCustomerButton = false;
    private boolean isRouteOk = true;
    private boolean isBargainOk = true;
    private String mRouteCode = "";
    private String mRdsCode = "";
    private String mDealerName = "";
    public Handler mHandlerPrepareSaudaData;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public TextView textViewLoadability = null;
    public TextView textViewTransportMode = null;
    public static TextView mTextViewDepotName = null;
    public static TextView mTextViewRateType = null;
    ArrayList<CustomerDetails> getDistributorDetailsFromCustomerMasterForDeliverAddress;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_do_filter);
        RegisterActivities.registerActivity(this);
        Constants.doBackToShippingAddressList=false;
        Constants.deliveryDate = "";
        mContext = ActivityDOFilter.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Toolbar myToolbar = findViewById(R.id.my_toolbar);
        setSupportActionBar(myToolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayShowTitleEnabled(false);
        getDistributorDetailsFromCustomerMasterForDeliverAddress=new ArrayList<>();
        Constants.selectedCustomer = null;
        mSelectedRouteDetails = null;
        customerSpinner =  findViewById(R.id.customerSpinner);
        selectedBargainList=new ArrayList<>();
        InitializeView();
        ClearText();

        mButtonBack.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        mButtonDestination.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                FlowofOrder(5);
            }
        });

        mButtonNoOrder.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (Constants.selectedCustomer != null) {
                    mButtonNoOrder.setEnabled(false);
                    if (orderFormDetailsObj.getHintsRemarks().equalsIgnoreCase("yes")) {
                        hintRemarksValList = new ArrayList<>();
                        String hintRemarksValString = orderFormDetailsObj.getHintsRemarksVal();
                        if (hintRemarksValString.contains("#")) {
                            String[] arrayOfData = hintRemarksValString.split("#");
                            for (int i = 0; i < arrayOfData.length; i++) {
                                hintRemarksValList.add(arrayOfData[i]);
                            }
                        } else {
                            hintRemarksValList.add(hintRemarksValString);
                        }
                        showInstructionWithHintDialog();
                    } else {
                        ShowNoOrderDialog();
                    }

                } else {
                    Utils.showToast(mContext, "Please select the customer");
                }

            }
        });

        mButtonOrderType.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                ShowOrderTypeListDialog();
            }
        });
        mButtonGst.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                ShowGstTypeListDialog();
            }
        });

        mButtonFreight.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                ShowFreightTypeListDialog();
            }
        });

        mButtonSubmit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                if (Constants.selectedCustomer != null)
                {
                    if (isCustomerChosen == true && isBargainOk == true )
                    {
                        moveToNextScreen();
                    }
                    else
                    {
                        if (false == isCustomerChosen)
                        {
                            Utils.showToast(mContext, "Please Choose a customer to proceed.");
                        }
                        else if (false == isBargainOk)
                        {
                            Utils.showToast(mContext, "Problem in bargain data\nPlease contact admin");
                        }

                    }
                }
                else
                {
                    Utils.showToast(mContext, "Please select the customer");
                }
            }
        });


        mStepHandler = new Handler() {
            public void handleMessage(Message msg) {
                mStepProgressDialog.dismiss();
                final int step = msg.getData().getInt("STEP");
                ActivityDOFilter.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (step) {
                            case 1:
                                if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan") && !Constants.userDetailsObj.gettour_plan_daywise_distributor().equalsIgnoreCase("yes")) {
                                    if (NotCheckedOut(mContext)) {
                                        disableSelectRouteCustButtonsForCheckedInCustomers();

                                        isRouteOk = true;
                                        mSelectedTodayRouteDetails = mRoutePlanListofToday.get(getPositionOfCurrentCheckedInRoute(true, mContext, mRoutePlanListofToday, null));
                                        setRouteName();
                                        ChangeBackgroundColor(2);
                                        FlowofOrder(2);
                                    } else if (mRoutePlanListofToday.size() == 1) {
                                        isRouteOk = true;
                                        mSelectedTodayRouteDetails = mRoutePlanListofToday.get(0);
                                        setRouteName();
                                        ChangeBackgroundColor(2);
                                        FlowofOrder(2);
                                    } else {
                                        ShowTodayRoutePlanListDialog(mRoutePlanListofToday);
                                    }
                                } else {
                                    if (NotCheckedOut(mContext)) {
                                        disableSelectRouteCustButtonsForCheckedInCustomers();
                                        isRouteOk = true;
                                        mSelectedRouteDetails = mRouteDetailsList.get(getPositionOfCurrentCheckedInRoute(false, mContext, null, mRouteDetailsList));
                                        mRouteName = mSelectedRouteDetails.getRouteName();
                                        mTextViewRouteName.setVisibility(View.VISIBLE);
                                        mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
                                        ChangeBackgroundColor(2);
                                        FlowofOrder(2);
                                    } else if (mRouteDetailsList.size() > 1) {
                                        ShowRouteListDialog(mRouteDetailsList);
                                    } else if (mRouteDetailsList.size() == 1) {
                                        isRouteOk = true;
                                        mSelectedRouteDetails = mRouteDetailsList.get(0);
                                        mRouteName = mSelectedRouteDetails.getRouteName();
                                        mTextViewRouteName.setVisibility(View.VISIBLE);
                                        mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
                                        ChangeBackgroundColor(2);
                                        FlowofOrder(2);
                                    } else {
                                        Toast.makeText(mContext, "No route found.\n Please contact your admin", Toast.LENGTH_LONG).show();
                                        isRouteOk = false;
                                    }
                                }

                                break;
                            case 2:
                                    customerSelectionCommonProcess();
                                break;
                            case 3:
                                if (BargainValList.size() > 1) {
//                                    ShowBargainListDialog();
                                }
                                else if (BargainValList.size() == 1)
                                {

//                                    Constants.selectedBargain = BargainValList.get(0);
//                                    selectedBargainList=new ArrayList<>();
//                                    selectedBargainList.add(BargainValList.get(0));
//                                    showChosenBargainRelatedDetails();
                                }
                                else
                                {
                                    Toast.makeText(mContext, "No Bargain found.\n Please contact your admin", Toast.LENGTH_LONG).show();
                                    isBargainOk = false;
                                }
                                break;
                            case 4:

                                break;
                            case 5:
                                if (mDestinationMasterList.size() > 0) {
                                    ShowDestinationListDialog();
                                } else {
                                    Toast.makeText(mContext, "No destination found", Toast.LENGTH_LONG).show();
                                }
                                break;
                            case 7:
                                Toast.makeText(mContext, "Route swapped successfully.", Toast.LENGTH_SHORT).show();
                                textViewCustomerVisitDay.setText(visitDayString + Constants.dayOfWeekForCustomer);
                                emptySelectedCustomerDetails();
                                clickedOnSelectCustomerButton = false;
                                new TRANS_TourDaySwapTransactionTask(mContext, true).execute();
                                FlowofOrder(2);
                                break;
                        }
                    }
                });

            }
        };
        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                ActivityDOFilter.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                FlowofOrder(2);
                                break;
                        }
                    }
                });
            }
        };

//        ChangeBackgroundColor(1);
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
                        new commonAsyncTaskMaster(mContext, "bargain_transaction");
                        new commonAsyncTaskMaster(mContext, "product_master");
                        new commonAsyncTaskMaster(mContext, "load_distribution");
                        new commonAsyncTaskMaster(mContext, "branch_route_freight");
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
    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }
    private void moveToNextScreen()
    {
//        if(selectedBargainList.size()>0)
//        {
            Constants.marginPoNo =ed_po.getText().toString();
            Intent intent = new Intent(mContext, DOFormActivity.class);
            startActivity(intent);

    }

    public void setRouteName() {
        mRouteName = mSelectedTodayRouteDetails.getRouteName();
        mTextViewRouteName.setVisibility(View.VISIBLE);
        mTextViewRouteName.setText("Route : " + mSelectedTodayRouteDetails.getRouteName());
    }

    public void GoToRouteCustomerSelectionProcess() {

        if (orderAuditType.equalsIgnoreCase("primary") && !Constants.orderFormDetailsObj.getroute_wise_distributor().equalsIgnoreCase("yes"))
        {
            if (Constants.oderToCustomerRoute != null)
            {
                mSelectedTodayRouteDetails = Constants.oderToCustomerRoute;
                mRouteName = mSelectedTodayRouteDetails.getRouteName();
                mTextViewRouteName.setVisibility(View.VISIBLE);
                mTextViewRouteName.setText("Route : " + mRouteName);
                isRouteOk = true;
                FlowofOrder(2);
            }
            FlowofOrder(2);
        }
        else if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan"))
        {
            if (Constants.oderToCustomerRoute != null)
            {
                mSelectedTodayRouteDetails = Constants.oderToCustomerRoute;
                mRouteName = mSelectedTodayRouteDetails.getRouteName();
                mTextViewRouteName.setVisibility(View.VISIBLE);
                mTextViewRouteName.setText("Route : " + mRouteName);
                isRouteOk = true;
                FlowofOrder(2);
            }
            else
            {
                FlowofOrder(1);
            }
        }
        else
        {
            if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes"))
            {
                if (isAlreadySwappedForToday())
                {
                    Constants.dayOfWeekForCustomer = mAceDnsTransactionDatabase.getTheDaySwappedWithToday();
                }
                else if (isTodaySwappedWithAnyPreviousDay())
                {
                    Constants.dayOfWeekForCustomer = mAceDnsTransactionDatabase.getThePreviousDaySwappedWithToday();
                }
                else
                {
                    Constants.dayOfWeekForCustomer = Utils.dayOfWeek();
                }

                if (Constants.CurrentOrderCollectionTransactionType.matches("SO")) {
                    textViewCustomerVisitDay.setText(visitDayString + Constants.dayOfWeekForCustomer);
                    FlowofOrder(2);
                } else {
                    FlowofOrder(1);
                }


            } else {
                mSelectedRouteDetails = mAceDnsDatabase.getLastOrderRouteList();
                if (mSelectedRouteDetails != null) {
                    mRouteName = mSelectedRouteDetails.getRouteName();
                    mTextViewRouteName.setVisibility(View.VISIBLE);
                    mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
                    isRouteOk = true;
                    FlowofOrder(2);
                } else {
                    FlowofOrder(1);
                }
            }

        }
    }

    public void customerSelectionCommonProcess() {
        clickedOnSelectCustomerButton = false;
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
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l)
            {
                if(i==0)
                {
                    isCustomerChosen=false;
                }
                else
                {
                    Constants.selectedCustomer = mCustomerDetailsList.get(i);
                    mTextViewRateType. setText("Rate                                         : "+toTitleCase(Constants.selectedCustomer.getIncoTerms()));
                    textViewTransportMode. setText("Transport Mode                    : "+toTitleCase(Constants.selectedCustomer.getTransportMode()));
                    textViewLoadability  .setText("Load Size                               : "+toTitleCase(Constants.selectedCustomer.getLoadabilityTon())+" MT");
                    transitTime = 0;
                    transitTime = mAceDnsDatabase.getTransitTimeByRouteCode(Constants.selectedCustomer.getBranchCode(), Constants.selectedCustomer.getRouteCode());
                    if(Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("D"))
                    {
                        ed_po.setVisibility(View.GONE);
                    }
                    getDistributorDetailsFromCustomerMasterForDeliverAddress =mAceDnsDatabase.getDistributorDetailsFromCustomerMasterDO(Constants.selectedCustomer.getCustomerCode());
                    shippingAddressSelectionProcess();
                }
            }
            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
    }

    public void shippingAddressSelectionProcess() {
        if(getDistributorDetailsFromCustomerMasterForDeliverAddress.size()>0)
        {
            if(getDistributorDetailsFromCustomerMasterForDeliverAddress.size()==1)
            {
                Constants.selectedCustomerDeliveryAddress= getDistributorDetailsFromCustomerMasterForDeliverAddress.get(0);
                getCustomerDetails();
                isCustomerChosen=true;
            }
            else
            {
                ShowCustomerListDialog(getDistributorDetailsFromCustomerMasterForDeliverAddress);
            }
        }
        else
        {
            selectedCustomerDeliveryAddress=Constants.selectedCustomer;
            getCustomerDetails();
            isCustomerChosen=true;
        }
    }
    public void vehicle_number_list(View view)
    {
        if(mCustomerDetailsList.size() > 1)
        {
            openDialog(tv_vehicle_no);
        }
    }
    public void openDialog(final TextView textView)
    {
        try
        {
            final PopupWindow popup = new PopupWindow(this);
            View layout = getLayoutInflater().inflate(R.layout.list_item_dialog, null);
            popup.setContentView(layout);

            // Set content width and height
            popup.setHeight(WindowManager.LayoutParams.WRAP_CONTENT);
            popup.setWidth(textView.getWidth());

            // Closes the popup window when touch outside of it - when looses focus
            popup.setOutsideTouchable(true);
            popup.setFocusable(true);
            popup.setBackgroundDrawable(new BitmapDrawable());

            popup.showAsDropDown(textView, 0, 0);

//            participentListItemGlobal.clear();
//            participentListItemGlobal.addAll(participentListItemDefault);
//
//            final ListAdapter mAdapter = new ListAdapter(this, participentListItemGlobal);
            final ArrayList<String> spinnerArray = new ArrayList<>();
            for (int i = 0; i < mCustomerDetailsList.size(); i++) {
                spinnerArray.add(mCustomerDetailsList.get(i).getCustomerName());
            }
            final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_dropdown_item_1line, spinnerArray);
            spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
            ListView listView = (ListView) layout.findViewById(R.id.lvPopup);
            listView.setAdapter(spinnerArrayAdapter);
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    Constants.selectedCustomer = mCustomerDetailsList.get(i);
                    textView.setText(Constants.selectedCustomer.getCustomerName());
                    mTextViewRateType. setText("Rate                                         : "+toTitleCase(Constants.selectedCustomer.getIncoTerms()));
                    textViewTransportMode. setText("Transport Mode                    : "+toTitleCase(Constants.selectedCustomer.getTransportMode()));
                    textViewLoadability  .setText("Load Size                               : "+toTitleCase(Constants.selectedCustomer.getLoadabilityTon())+" MT");
                    transitTime = 0;
                    popup.dismiss();
                    transitTime = mAceDnsDatabase.getTransitTimeByRouteCode(Constants.selectedCustomer.getBranchCode(), Constants.selectedCustomer.getRouteCode());
                    if(Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("D"))
                    {
                        ed_po.setVisibility(View.GONE);
                    }
                    getDistributorDetailsFromCustomerMasterForDeliverAddress =mAceDnsDatabase.getDistributorDetailsFromCustomerMasterDO(Constants.selectedCustomer.getCustomerCode());
                    shippingAddressSelectionProcess();
//                    onItemSeelcted(i, textView);

                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    private void multipleUomDecision() {
        uomToBeShownForProduct = "uom1";
        try {
            if (orderFormDetailsObj.getcust_type_wise_UOM().equalsIgnoreCase("yes")) {
                String[] uomListForCustomerType = Constants.orderFormDetailsObj.getcust_type_wise_UOM_val().split(",");
                for (int i = 0; i < uomListForCustomerType.length; i++) {
                    String[] uomForCustTypeArray = uomListForCustomerType[i].split("#");
                    if (uomForCustTypeArray[0].equalsIgnoreCase(Constants.selectedCustomer.getCustomerType())) {
                        uomToBeShownForProduct = uomForCustTypeArray[1].toLowerCase();
                        break;
                    }
                }
            }
        } catch (Exception e) {

        }

    }

    private void disableSelectRouteCustButtonsForCheckedInCustomers() {
        mFrameLayoutSelectCustomer.setVisibility(View.GONE);
        mFrameLayoutSelectRoute.setVisibility(View.GONE);
        mFrameLayoutAddCustomer.setVisibility(View.GONE);
    }


    private void emptySelectedCustomerDetails() {
        Constants.selectedCustomer = null;
        mTextViewCustomerName.setText("");
        customerWiseTrgtAchvmntLayout.setVisibility(View.GONE);
    }
    public void InitializeView() {
        textViewLoadability = (TextView) findViewById(R.id.textViewLoadability);
        textViewTransportMode = (TextView) findViewById(R.id.textViewTransportMode);
        mTextViewDepotName = (TextView) findViewById(R.id.textViewDepotValue);
        mTextViewRateType = (TextView) findViewById(R.id.textViewRateTypeValue);
        mTextViewRouteName = (TextView) findViewById(textViewRouteValue);
        textViewCustomerVisitDay = (TextView) findViewById(R.id.textViewCustomerVisitDay);
        mTextViewCustomerName = (TextView) findViewById(R.id.textViewCustomerValue);
        tv_vehicle_no = (TextView) findViewById(R.id.tv_vehicle_no);
        mTextViewDealerName = (TextView) findViewById(R.id.textViewBargainNumber);
        textViewShippingCustomerTv = (TextView) findViewById(R.id.textViewShippingCustomerTv);
        textViewBargainDate = (TextView) findViewById(R.id.textViewBargainDate);
        textViewProduct = (TextView) findViewById(R.id.textViewProduct);

        mTextViewCreditLimit = (TextView) findViewById(R.id.textViewCreditLimitValue);

        mTextViewNoofInvoice = (TextView) findViewById(R.id.textViewInvoiceNo);
        mTextViewInvoiceAmount = (TextView) findViewById(R.id.textViewInvoice);
        mTextViewDueAmount = (TextView) findViewById(R.id.textViewDue);

        mTextViewVerticalValue = (TextView) findViewById(R.id.textViewVerticalValue);
        mTextViewBranchValue = (TextView) findViewById(R.id.textViewBranchValue);
        customerWiseTrgtAchvmntTvMonth = (TextView) findViewById(R.id.customerWiseTrgtAchvmntTvMonth);
        customerWiseTrgtAchvmntTvTarget = (TextView) findViewById(R.id.customerWiseTrgtAchvmntTvTarget);
        customerWiseTrgtAchvmntTvAchievement = (TextView) findViewById(R.id.customerWiseTrgtAchvmntTvAchievement);
        ed_po = findViewById(R.id.ed_po);

        mTextViewOrderType = (TextView) findViewById(R.id.textViewOrderType);
        textViewGst = (TextView) findViewById(R.id.textViewGst);
        mTextViewFreight = (TextView) findViewById(R.id.textViewFreight);
        mTextViewDestination = (TextView) findViewById(R.id.textViewDestination);

        mFrameLayoutSelectCustomer = (FrameLayout) findViewById(R.id.frameLayoutSelectCustomer);
        mFrameLayoutSelectRoute = (FrameLayout) findViewById(R.id.frameLayoutSelectRoute);
        mFrameLayoutAddCustomer = (FrameLayout) findViewById(R.id.frameLayoutAddCustomer);
        frameLayoutSwapDay = (FrameLayout) findViewById(R.id.frameLayoutSwapDay);

        mLinearLayoutOutstanding = (LinearLayout) findViewById(R.id.linearLayoutOutstanding);
        mLinearLayoutOrderType = (LinearLayout) findViewById(R.id.linearLayoutOrderType);
        linearLayoutGst = (LinearLayout) findViewById(R.id.linearLayoutGst);
        mLinearLayoutFreight = (LinearLayout) findViewById(R.id.linearLayoutFreight);
        mLinearLayoutDestination = (LinearLayout) findViewById(R.id.linearLayoutDestination);
        customerWiseTrgtAchvmntLayout = (LinearLayout) findViewById(R.id.customerWiseTrgtAchvmntLayout);

        mButtonSubmit = (Button) findViewById(R.id.btn_Submi);
        mButtonBack = (Button) findViewById(R.id.back);
        mButtonOrderType = (Button) findViewById(R.id.buttonOrderType);
        mButtonGst = (Button) findViewById(R.id.buttonGst);
        mButtonFreight = (Button) findViewById(R.id.buttonFreight);
        mButtonDestination = (Button) findViewById(R.id.buttonDestination);
        mButtonNoOrder = (Button) findViewById(R.id.no_ordr);

        if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes") && Constants.CurrentOrderCollectionTransactionType.matches("SO")) {
            mTextViewRouteName.setVisibility(View.GONE);
            textViewCustomerVisitDay.setVisibility(View.VISIBLE);
            frameLayoutSwapDay.setVisibility(View.VISIBLE);
            mFrameLayoutSelectRoute.setVisibility(View.GONE);
        } else {
            frameLayoutSwapDay.setVisibility(View.GONE);
        }

        if (orderFormDetailsObj.getAddCustomer().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("add_new_customer")) {
            mFrameLayoutAddCustomer.setVisibility(View.VISIBLE);
        } else {
            mFrameLayoutAddCustomer.setVisibility(View.GONE);
        }

        if (orderFormDetailsObj.getFreightComponent().trim().length() > 0) {
        } else {
            mLinearLayoutFreight.setVisibility(View.GONE);
        }

        if (orderFormDetailsObj.getOrderType().trim().length() > 0) {
        } else {
            mLinearLayoutOrderType.setVisibility(View.GONE);
        }
        if (orderFormDetailsObj.getTaxType().trim().length() > 0) {

        } else {
            linearLayoutGst.setVisibility(View.GONE);
        }

        if (false == orderFormDetailsObj.getDestination().equalsIgnoreCase("yes")) {
            mLinearLayoutDestination.setVisibility(View.GONE);
        } else {

        }

        mFrameLayoutSelectCustomer.setOnClickListener(this);
        mFrameLayoutSelectRoute.setOnClickListener(this);
        mFrameLayoutAddCustomer.setOnClickListener(this);
        frameLayoutSwapDay.setOnClickListener(this);
        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivityDOFilter.this) + "~"
                + Utils.getDBVersion(ActivityDOFilter.this));
        ChangeBackgroundColor(0);
    }

    public void ClearText() {
        mTextViewRouteName.setText("");
        mTextViewCustomerName.setText("");
        mTextViewDealerName.setText("");
//        mTextViewCreditLimit.setText("Available Credit Limit : ");
//        mTextViewNoofInvoice.setText("No of Invoices: ");
        mTextViewInvoiceAmount.setText("");
        mTextViewDueAmount.setText("");
        mTextViewVerticalValue.setText("");
        mTextViewBranchValue.setText("");
        mTextViewOrderType.setText("");
        textViewGst.setText("");
        mTextViewFreight.setText("");
        mTextViewDestination.setText("");
        customerWiseTrgtAchvmntLayout.setVisibility(View.GONE);
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
        if(Constants.doBackToShippingAddressList){
            shippingAddressSelectionProcess();
            Constants.doBackToShippingAddressList=false;
        }
    }

    @Override
    public void onClick(View v) {

        if (v == mFrameLayoutSelectCustomer) {
            ChooseOrderType();
            ChangeBackgroundColor(1);

        } else if (v == frameLayoutSwapDay) {
            if (isAlreadySwappedForToday()) {
                Toast.makeText(mContext, "You have already swapped for today.", Toast.LENGTH_SHORT).show();
            } else {
                ArrayList<String> days = daysOfWeekExceptToday();
                ShowNextSixDaysToSwapWithToday(days);
            }

        } else if (v == mFrameLayoutSelectRoute) {
            ChooseOrderType();
            ChangeBackgroundColor(1);
//			ClearText();
//			ChangeBackgroundColor(2);
//			FlowofOrder(1);
        } else if (v == mFrameLayoutAddCustomer) {
            if (mRouteCode.length() > 0) {
                Constants.isOrederToNewCustomer = true;
                isCheckInToNewCustomer = false;
                Intent intent = new Intent(ActivityDOFilter.this, AddNewCustActivity.class);
                intent.putExtra("ROUTENAME", mRouteName);
                intent.putExtra("ROUTECODE", mRouteCode);

                startActivity(intent);
            } else {
                Utils.showToast(mContext, "Please Select the route");
            }

        }
    }

    private boolean isAlreadySwappedForToday() {
        return mAceDnsTransactionDatabase.isAlreadySwappedForToday();
    }

    private boolean isTodaySwappedWithAnyPreviousDay() {
        return mAceDnsTransactionDatabase.isTodaySwappedWithAnyPreviousDay();
    }

    public void ChangeBackgroundColor(int select) {
        try {
            mFrameLayoutSelectCustomer.setBackgroundColor(Color.parseColor("#E0FFFF"));
            mFrameLayoutSelectRoute.setBackgroundColor(Color.parseColor("#E0FFFF"));

            switch (select) {
                case 1:
                    mFrameLayoutSelectCustomer.setBackgroundColor(Color.parseColor("#7EB5D6"));
                    break;
                case 2:
                    mFrameLayoutSelectRoute.setBackgroundColor(Color.parseColor("#7EB5D6"));
                    break;

            }
        } catch (Exception e) {

        }

    }
    public void OutstandingAmount()
    {
        mLinearLayoutOutstanding.setVisibility(View.VISIBLE);
        ArrayList<OutstandingDetails> outstandingList = mAceDnsDatabase.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
        double invoiceamount = 0;
        double dueamount = 0;
        if (outstandingList != null) {
            int size = outstandingList.size();
            if (size > 0) {
                for (int ii = 0; ii < size; ii++) {
                    if (outstandingList.get(ii).getInvoice_amount().length() > 0) {
                        invoiceamount = invoiceamount
                                + Double.parseDouble(outstandingList.get(ii)
                                .getInvoice_amount());
                    }
                    if (outstandingList.get(ii).getDue_amount().length() > 0) {
                        dueamount = dueamount
                                + Double.parseDouble(outstandingList.get(ii)
                                .getDue_amount());
                    }
                }
            }
        }
        if (outstandingList != null) {
            int size = outstandingList.size();
            if(size>0)
            {
                mTextViewNoofInvoice.setCompoundDrawablesWithIntrinsicBounds( R.drawable.ic_info_black_24dp, 0, 0, 0);
            }
            else
            {
                mTextViewNoofInvoice.setVisibility(View.GONE);
                mTextViewNoofInvoice.setVisibility(View.VISIBLE);
                mTextViewNoofInvoice.setCompoundDrawablesWithIntrinsicBounds( 0, 0, 0, 0);
            }
            mTextViewNoofInvoice.setText(""+ size);
        } else
        {
            mTextViewNoofInvoice.setText("0");

        }
        mTextViewInvoiceAmount.setText(Constants.defaultFormat.format(invoiceamount));
        mTextViewDueAmount.setText(Constants.defaultFormat.format(dueamount));
        mTextViewNoofInvoice.setOnClickListener(view ->
        {
            if(Utils.isNumeric(mTextViewNoofInvoice.getText().toString()) && Double.parseDouble(mTextViewNoofInvoice.getText().toString())>0)
            {
                showOutStandingDetails(outstandingList);
            }

        });
        mTextViewDueAmount.setOnClickListener(view ->
        {
            if(Utils.isNumeric(mTextViewDueAmount.getText().toString()) && Double.parseDouble(mTextViewDueAmount.getText().toString())>0)
            {
                showOutStandingDetails(outstandingList);
            }
        });
    }
    private void showOutStandingDetails( ArrayList<OutstandingDetails> outstandingList)
    {
        final Dialog incotermsSelectionDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        incotermsSelectionDialog.setContentView(R.layout.outstanding_details_list_material);
        incotermsSelectionDialog.setCancelable(false);

        LinearLayout totalLayout =  incotermsSelectionDialog.findViewById(R.id.totalLayout);
        totalLayout.setVisibility(View.GONE);
        Button btn_cncl =  incotermsSelectionDialog.findViewById(R.id.btn_cncl);
        btn_cncl.setOnClickListener(view -> incotermsSelectionDialog.dismiss());
        TextView invamountTv = incotermsSelectionDialog.findViewById(R.id.invamountTv);
        invamountTv.setText("Inv Amnt");
        TextView dueamountTv = incotermsSelectionDialog.findViewById(R.id.dueamountTv);
        dueamountTv.setText("Due Amnt");
        TextView title = incotermsSelectionDialog.findViewById(R.id.title);
        title.setText(HtmlCompat.fromHtml("Outstanding Details: <font color='#D7B56D'>"+Constants.selectedCustomer.getCustomerName()+"</font>",HtmlCompat.FROM_HTML_MODE_LEGACY));
        ListView dialogList = incotermsSelectionDialog.findViewById(R.id.list);
        OutstandingDetailsadpter PendingBargaindapterObject = new OutstandingDetailsadpter(mContext, R.layout.outstanding_details_list_item_material, outstandingList);
        dialogList.setAdapter(PendingBargaindapterObject);

        incotermsSelectionDialog.show();
    }

    public class OutstandingDetailsadpter extends ArrayAdapter<OutstandingDetails>
    {

        private final Context context;
        private final int resourceId;
        Activity activity;
        AceDnsDatabase mAceDnsDatabase;
        int sizeOfList = 0;

        private OutstandingDetailsadpter.ViewHolder viewHolder;
        public  ArrayList<OutstandingDetails> nameValuesProductListLocalDo;

        public OutstandingDetailsadpter(Context context, int resourceId, ArrayList<OutstandingDetails> nameValues) {
            super(context, resourceId, nameValues);
            this.context = context;
            activity = (Activity) context;
            nameValuesProductListLocalDo =new ArrayList<>();
            nameValuesProductListLocalDo = nameValues;
            this.resourceId = resourceId;
            mAceDnsDatabase = new AceDnsDatabase(context);
            sizeOfList = nameValues.size();
        }
        @Override
        public View getView(final int position, View convertView, ViewGroup parent) {

            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new OutstandingDetailsadpter.ViewHolder();
            viewHolder.txtViewProductDesc = convertView.findViewById(R.id.list_details);
            viewHolder.daysTV = convertView.findViewById(R.id.daysTV);
            viewHolder.rateTV = convertView.findViewById(R.id.rateTV);
            viewHolder.dateTV =  convertView.findViewById(R.id.dateTV);
            viewHolder.qtyTV =  convertView.findViewById(R.id.qtyTV);

            viewHolder.invisibleTVProdCode = convertView.findViewById(R.id.invisibleTVProdCode);
            convertView.setTag(viewHolder);
            String date = nameValuesProductListLocalDo.get(position).getDate();
            date=Utils.changeDateFormat("yyyy-MM-dd","dd-MM-yy",date);
            viewHolder.dateTV.setText(date);
            int slNo = position + 1;
            long days=Utils.DaybetweenDates(date,dateString,"dd-MM-yy","yyyyMMdd");
            viewHolder.txtViewProductDesc.setText(slNo+"");
            viewHolder.daysTV.setText(days+"");
            viewHolder.qtyTV.setText(defaultFormatWithComma.format(Double.parseDouble(nameValuesProductListLocalDo.get(position).getInvoice_amount())));
            viewHolder.rateTV.setText(defaultFormatWithComma.format(Double.parseDouble(nameValuesProductListLocalDo.get(position).getDue_amount())));
            return convertView;
        }

        public class ViewHolder {
            TextView txtViewProductDesc, invisibleTVProdCode, dateTV,qtyTV,rateTV,daysTV;
        }

    }
    public void AvailableCredit(String value) {
        mTextViewCreditLimit.setText("Available Credit Limit : " + value);
    }

    public void FlowofOrder(final int step) {
        mStepProgressDialog = new ProgressDialog(mContext);
        mStepProgressDialog.setMessage("Preparing Data.\nPlease wait..");
        mStepProgressDialog.setCancelable(false);
        mStepProgressDialog.show();
        new Thread() {
            public void run() {
                switch (step) {
                    case 1:
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan"))
                        {
                            String today = Constants.dateString.substring(6, 8) + "-"
                                    + Constants.dateString.substring(4, 6) + "-"
                                    + Constants.dateString.substring(0, 4);
                            if (Constants.CurrentOrderCollectionTransactionType.matches("SO"))
                            {
                                mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForToday(today);
                            }
                            //telephonic order
                            else//if (Constants.CurrentOrderCollectionTransactionType.matches("TO"))
                            {
                                if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes"))//if it is  tele tran and tour plan is day wise then select route for customers whose visit day are today
                                {
                                    mRoutePlanListofToday = mAceDnsTransactionDatabase.getRouteListForCustomersWhoseVisitDateToday(Constants.dayOfWeekForCustomer);
                                } else {
                                    mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanNotForToday(today);
                                }

                            }
                            mRoutePlanListofTodayForSearching = new ArrayList<>(mRoutePlanListofToday);
                        }
                        else
                        {
                            if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes"))//if it is  tele tran and tour plan is day wise then select route for customers whose visit day are not of today
                            {
                                mRouteDetailsList = mAceDnsTransactionDatabase.getRouteListForCustomersWhoseVisitDateNotToday2(Constants.dayOfWeekForCustomer);
                            } else {
                                mRouteDetailsList = mAceDnsDatabase.getRouteList();
                            }

                        }

                        break;
                    case 2:
                        mCustomerDetailsList = mAceDnsDatabase.getCustomerListForDeliveryOrder();

                        break;
                    case 3:

                        BargainValList = mAceDnsDatabase.getBargainListForChosenCustomer();
                        break;
                    case 4:
//                        productList = mAceDnsDatabase.getProductListForChosenBargain();
                        break;
                    case 5:
                        mDestinationMasterList = mAceDnsDatabase.getDestinationList();
                        break;


                    case 7:
                        saveSwapDataToDatabase();
                        break;
                }
                Message msgObj = mStepHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("STEP", step);
                msgObj.setData(b);
                mStepHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void ShowTodayRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> routePlanListofToday) {
        final Dialog routePlanListDialog = new Dialog(ActivityDOFilter.this, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
        RoutePlanAdapter = new RoutePlanTransAdapter(ActivityDOFilter.this, R.layout.route_list_child, routePlanListofToday);
        dialogList.setAdapter(RoutePlanAdapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routePlanListDialog.cancel();
                mSelectedTodayRouteDetails = routePlanListofToday.get(arg2);
                setRouteName();
                isRouteOk = true;
                FlowofOrder(2);
            }
        });
        ImageView image_cancel = (ImageView) routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanListDialog.cancel();
                finish();
            }
        });
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanListDialog.cancel();
            }
        });
        Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
        final EditText autoCompleteTextView1 = (EditText) routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
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
                routePlanListofToday.clear();
                for (int i = 0; i < mRoutePlanListofTodayForSearching.size(); i++) {
                    String routeName = mRoutePlanListofTodayForSearching.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                    if (textLength <= routeName.length()) {
                        //compare the String in EditText with Names in the ArrayList
                        //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                        if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                            routePlanListofToday.add(mRoutePlanListofTodayForSearching.get(i));
                        }
                    }
                }
                RoutePlanAdapter.notifyDataSetChanged();

            }
        });
    }

    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(ActivityDOFilter.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(ActivityDOFilter.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routeDialog.cancel();
                mSelectedRouteDetails = routeList.get(arg2);
                mRouteName = mSelectedRouteDetails.getRouteName();
                mTextViewRouteName.setVisibility(View.VISIBLE);
                mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
                isRouteOk = true;
                FlowofOrder(2);
            }
        });
        ImageView image_cancel = (ImageView) routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
                finish();
            }
        });
        Button cancel = (Button) routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
            }
        });
        Button create_route = (Button) routeDialog
                .findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    public void ShowRouteListDialogVisitSequence(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(ActivityDOFilter.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(ActivityDOFilter.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routeDialog.cancel();

                selectRouteFOrVisitSequenceAndGetCustomers(arg2);
            }
        });
        ImageView image_cancel = (ImageView) routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
                finish();
            }
        });
        Button cancel = (Button) routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
            }
        });
        Button create_route = (Button) routeDialog
                .findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    public void ShowRouteListDialogVisitDayWiseDistributorSelection(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(ActivityDOFilter.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(ActivityDOFilter.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                routeDialog.cancel();

                getCustomerDetailsForDistributorOfChosenRouteAndVisitDay(arg2);
            }
        });
        ImageView image_cancel = (ImageView) routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
                finish();
            }
        });
        Button cancel = (Button) routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
            }
        });
        Button create_route = (Button) routeDialog
                .findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    public void selectRouteFOrVisitSequenceAndGetCustomers(int arg2) {
        isRouteOk = true;
        mSelectedRouteDetailsVisitSequence = mRouteDetailsListForVisitSequence.get(arg2);
        mRouteName = mSelectedRouteDetailsVisitSequence.getRouteName();
        mTextViewRouteName.setVisibility(View.VISIBLE);
        mTextViewRouteName.setText("Route : " + mSelectedRouteDetailsVisitSequence.getRouteName());
        mCustomerDetailsList = mAceDnsDatabase.getCustomerListDayOfWeekWiseForRouteCode(Constants.dayOfWeekForCustomer, mSelectedRouteDetailsVisitSequence.getRouteCode());
        customerSelectionCommonProcess();
    }

    public void getCustomerDetailsForDistributorOfChosenRouteAndVisitDay(int arg2) {
        isRouteOk = true;
        mSelectedRouteDetails = mRouteDetailsList.get(arg2);
        mRouteName = mSelectedRouteDetails.getRouteName();
        mRouteCode = mSelectedRouteDetails.getRouteCode();
        mTextViewRouteName.setVisibility(View.VISIBLE);
        mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
        mCustomerDetailsList = mAceDnsDatabase.getDistributorDetailsFromCustomerMaster(mSelectedRouteDetails.getDistributorCode());
        customerSelectionCommonProcess();
    }

    public void ShowCustomerListDialog(ArrayList<CustomerDetails> getDistributorDetailsFromCustomerMaster) {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child_do_single_item, getDistributorDetailsFromCustomerMaster);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search_material);
        mDialogCustomer.setCancelable(false);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);

            title.setText("Choose customer for shipping order");

        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
                selectedCustomerDeliveryAddress= adapterCust.getItem(arg2);
                getCustomerDetails();
                isCustomerChosen=true;

            }
        });

        Button addnewcustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
            addnewcustomer.setVisibility(View.GONE);

        Button back = (Button) mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(View.GONE);
        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDialogCustomer.cancel();
            }
        });

        mDialogCustomer.show();
    }

    public void getCustomerDetails()
    {
        String routeName = mAceDnsTransactionDatabase.getRouteNameFromRouteCode(Constants.selectedCustomerDeliveryAddress.getRouteCode());
        textViewShippingCustomerTv.setText("Shipping to: " + Constants.selectedCustomerDeliveryAddress.getCustomerName());
        mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
        mTextViewRouteName.setText("To : " + routeName);
        exForSelectionProcess();
        AvailableCredit(Constants.selectedCustomer.getCreditLimit());
        OutstandingAmount();
        FlowofOrder(3);
    }
    private void exForSelectionProcess() {
//        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(verticalValueOfEmployee)) {
        String incoTermsOfCurentCustomer = Constants.selectedCustomer.getIncoTerms().trim();
//        mLoadabilityTon = Constants.selectedCustomer.getLoadabilityTon().trim();
//        textViewLoadabilityTons.setText("  Transport Mode : " + Constants.selectedCustomer.getTransportMode().trim());
//        textViewLoadabilityTons.setVisibility(View.VISIBLE);
        if (incoTermsOfCurentCustomer.contains(";"))//multiple incoterms
        {

            ArrayList<String> incotermsArrayMultiple = new ArrayList<String>(Arrays.asList(incoTermsOfCurentCustomer.split(";")));
            ShowIncotermsSelectionDialog(incotermsArrayMultiple);
        } else {
            incotermsSplitProcess(incoTermsOfCurentCustomer);
        }

//        }
    }
    private void incotermsSplitProcess(String incoTermsOfCurentCustomer)
    {
        Constants.incoTermsOfCurentCustomer=incoTermsOfCurentCustomer;
        Constants.isPrimaryFreightIncluded = false;
        isSecondaryFreightIncluded = false;
        Constants.isDepotCostIncluded = false;
        Constants.isMarginCostIncluded = false;
//        String incotermsPriceComponenet =Constants.saudaFormDetailsObj.getincoterms_price_components();
//        if(incotermsPriceComponenet.contains("#"))
//        {
//            String[] splittedPriceComp=incotermsPriceComponenet.split("#");
//            for(int i=0;i<splittedPriceComp.length;i++)
//            {
//                String currentComp=splittedPriceComp[i].toLowerCase();
//                if(currentComp.contains(incoTermsOfCurentCustomer.toLowerCase()))
//                {
//                    if(currentComp.contains("primary_freight"))
//                    {
//                        Constants.isPrimaryFreightIncluded=true;
//                    }
//                    if(currentComp.contains("secondary_freight"))
//                    {
//                        isSecondaryFreightIncluded=true;
//                    }
//                    if(currentComp.contains("depot_cost"))
//                    {
//                        Constants.isDepotCostIncluded=true;
//                    }
//                    if(currentComp.contains("margin_cost"))
//                    {
//                        Constants.isMarginCostIncluded=true;
//                    }
//                }
//            }
//        }
        if (incoTermsOfCurentCustomer.toLowerCase().contains("for depot") || incoTermsOfCurentCustomer.toLowerCase().contains("for plant") || incoTermsOfCurentCustomer.toLowerCase().contains("ex depot")
                || incoTermsOfCurentCustomer.toLowerCase().contains("ex plant"))
        {
            if (incoTermsOfCurentCustomer.contains(" "))
            {
                String[] inctermsArray = incoTermsOfCurentCustomer.split(" ");
                if (inctermsArray.length == 2)
                {
                    mSaudaType = inctermsArray[0].toUpperCase();
                    mDepotOrPlant = inctermsArray[1];
                    ShowSaudaDepoNameDialog();
                }
                else
                {
                    Utils.showToast(mContext, "Improper incoterms data found for selected customer. Please contact admin.");
                }
            }
        }
        else
        {
            Utils.showToast(mContext, "Improper incoterms data found for selected customer. Please contact admin.");
        }
    }
    public void ShowSaudaDepoNameDialog() {

        String plantDepotFilter = "";
//        if (Constants.saudaFormDetailsObj.getincoterms_vertical().contains(verticalValueOfEmployee)) {
//            if (mDepotOrPlant.equalsIgnoreCase("plant")) {
//                plantDepotFilter = " LOWER(is_plant)='yes' AND ";
//            } else if (mDepotOrPlant.equalsIgnoreCase("depot")) {
//                plantDepotFilter = " LOWER(is_plant)='no' AND";
//            }
//        }

        if (mSaudaType.matches("FOR"))
        {
            if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser))
            {
                saudaRDSList = mAceDnsDatabase.getSaudaRDSListWithRouteCode(Constants.selectedCustomer.getCustomerCode(), Constants.selectedCustomer.getRouteCode(), plantDepotFilter,Constants.selectedCustomer.getTransportMode(),Constants.selectedCustomer.getLoadabilityTon());
            }
            else
            {
                saudaRDSList = mAceDnsDatabase.getSaudaRDSListWithRouteCode(Constants.selectedCustomer.getCustomerCode(), Constants.selectedCustomer.getRouteCode(), plantDepotFilter,Constants.selectedCustomer.getTransportMode(),Constants.selectedCustomer.getLoadabilityTon());
            }


        }
        else {
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

                    mTextViewDepotName.setText(mDepotOrPlant + " Name               : " + mSaudaDepoName);

//                    mTextViewRateType     .setText("Rate                            : " + mSaudaType + " - " + mDepotOrPlant);
//                    if (mSaudaType.matches("FOR")) {
//                        freightRateSelectionProcess();
//                    } else {
//                    }
//                    isDepoSelected = true;
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
//                mTextViewRateType        .setText("Rate                                         : " + mSaudaType + " - " + mDepotOrPlant);
                mTextViewDepotName       .setText(mDepotOrPlant + " Name                         : " + mSaudaDepoName);
                // textViewTransportMode .setText("Transport Mode                  : "+toTitleCase(Constants.selectedCustomer.getTransportMode()));
//                if (mSaudaType.matches("FOR")) {
////                    freightRateSelectionProcess();
//                } else {
//                }
//                isDepoSelected = true;
            } else {
                Toast.makeText(mContext, "No " + mDepotOrPlant + " found.", Toast.LENGTH_LONG).show();
            }
        }
    }
    public void ShowIncotermsSelectionDialog(final ArrayList<String> incotermsArrayMultiple) {

        if (incotermsArrayMultiple.size() > 1) {
            final Dialog incotermsSelectionDialog = new Dialog(mContext,
                    R.style.MyMaterialTheme);
            incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            incotermsSelectionDialog.setContentView(R.layout.select_from_list_material);
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
            ListView dialogList = (ListView) incotermsSelectionDialog.findViewById(R.id.list);
            IncotermsAdapter branchadapter = new IncotermsAdapter(mContext, R.layout.list_item_single_radio ,incotermsArrayMultiple);
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
    public void ShowDestinationListDialog() {
        final Dialog mDestinationDialog = new Dialog(ActivityDOFilter.this,
                R.style.PauseDialog);
        mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDestinationDialog.setContentView(R.layout.select_with_search);
        mDestinationDialog.setCancelable(false);

        TextView title = (TextView) mDestinationDialog.findViewById(R.id.title);
        title.setText("Please select a destination");
        ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);

        //final ArrayAdapter<String> RoutePlanAdapter = new ArrayAdapter<String>(this,R.layout.activity_listview, mDestinationMasterList);

        final DestinationAdapter destinationAdapter = new DestinationAdapter(this, R.layout.customer_broker_list_child, mDestinationMasterList);

        dialogList.setAdapter(destinationAdapter);

        EditText searchText = (EditText) mDestinationDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                destinationAdapter.getFilter().filter(s.toString());
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
                                    int position, long arg3) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                Constants.selectedDestination = destinationAdapter.getItem(position);
                Constants.mDestinationCode = Constants.selectedDestination.getDestinationCode();
                mTextViewDestination.setText(Constants.selectedDestination.getDestinationName());
                mDestinationDialog.cancel();
            }
        });

        Button cancel = (Button) mDestinationDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mDestinationDialog.show();
    }

    public void ShowFreightTypeListDialog() {
        if (orderFormDetailsObj.getFreightComponent().trim().length() > 0) {

            if (orderFormDetailsObj.getFreightComponent().contains(",")) {
                final Dialog payTypeDialog = new Dialog(ActivityDOFilter.this, R.style.PauseDialog);
                payTypeDialog.setCancelable(false);
                payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                payTypeDialog.setContentView(R.layout.uom_dialog);
                TextView txtMsg = (TextView) payTypeDialog.findViewById(R.id.title);
                txtMsg.setText("Please select a freight");

                RadioButton uom1RadioButton = (RadioButton) payTypeDialog.findViewById(R.id.radio0);
                uom1RadioButton.setText("EX");

                RadioButton uom2RadioButton = (RadioButton) payTypeDialog.findViewById(R.id.radio1);
                uom2RadioButton.setText("FOR");

                RadioGroup payTypeOption = (RadioGroup) payTypeDialog.findViewById(R.id.rg_pay_options);
                payTypeOption.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                    @Override
                    public void onCheckedChanged(RadioGroup group, int checkedId) {
                        int radioButtonID = group.getCheckedRadioButtonId();
                        View radioButton = group.findViewById(radioButtonID);
                        int selectedRadio = group.indexOfChild(radioButton);

                        if (selectedRadio == 0) {
                            Constants.mFreightComponent = "EX";
                        } else {
                            Constants.mFreightComponent = "FOR";
                        }
                        mTextViewFreight.setText(Constants.mFreightComponent);
                        payTypeDialog.cancel();

                    }
                });
                Button cancel = (Button) payTypeDialog.findViewById(R.id.btn_cancel);
                cancel.setVisibility(View.GONE);
                payTypeDialog.show();
            } else {
                Constants.mFreightComponent = orderFormDetailsObj.getFreightComponent().trim();
                mTextViewFreight.setText(Constants.mFreightComponent);
            }
        } else {
            Utils.showToast(mContext, "You have no order type.\n Please conatct admin");
        }

    }


    public void ShowDealerChoseDialog(final ArrayList<CustomerDetails> mDealerNameList) {
        final Dialog mDestinationDialog = new Dialog(ActivityDOFilter.this,
                R.style.PauseDialog);
        mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDestinationDialog.setContentView(R.layout.select_with_search);
        mDestinationDialog.setCancelable(false);

        TextView title = (TextView) mDestinationDialog.findViewById(R.id.title);
        title.setText("Select dealer with whom order to be mapped");
        ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);


        final CustomerAdapter adapter1 = new CustomerAdapter(mContext, R.layout.routeplan_access_dialog_child, mDealerNameList);
        dialogList.setAdapter(adapter1);

        EditText searchText = (EditText) mDestinationDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                adapter1.getFilter().filter(s.toString());
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
                                    int position, long arg3) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDealerName = mDealerNameList.get(position).getCustomerName();
                taggedCustomerCode=mDealerNameList.get(position).getCustomerCode();
                if (mDealerName.matches("")) {
                    mTextViewDealerName.setVisibility(View.GONE);
                } else {
                    mTextViewDealerName.setText("Dealer : " + mDealerName);
                }

                mDestinationDialog.cancel();
            }
        });

        Button cancel = (Button) mDestinationDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mDestinationDialog.show();
    }


    public void ShowOrderTypeListDialog() {

        if (orderFormDetailsObj.getOrderType().trim().length() > 0) {

            if (orderFormDetailsObj.getOrderType().contains(",")) {

                String[] mOrderTypeList = orderFormDetailsObj.getOrderType().split(",");


                final Dialog mVerticalDialog = new Dialog(ActivityDOFilter.this,
                        R.style.PauseDialog);
                mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                mVerticalDialog.setContentView(R.layout.select_with_search);
                mVerticalDialog.setCancelable(false);

                TextView title = (TextView) mVerticalDialog.findViewById(R.id.title);
                title.setText("Please select an order type");
                ListView dialogList = (ListView) mVerticalDialog.findViewById(R.id.list);

                final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
                        R.layout.activity_masterview, mOrderTypeList);
                dialogList.setAdapter(adapter);

                EditText searchText = (EditText) mVerticalDialog
                        .findViewById(R.id.autoCompleteTextView1);
                searchText.addTextChangedListener(new TextWatcher() {
                    @Override
                    public void onTextChanged(CharSequence s, int arg1, int arg2,
                                              int arg3) {
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
                    public void onItemClick(AdapterView<?> arg0, View arg1,
                                            int position, long arg3) {
                        getWindow()
                                .setSoftInputMode(
                                        WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                        Constants.mOrderType = adapter.getItem(position);
                        mTextViewOrderType.setText(Constants.mOrderType);
                        mVerticalDialog.cancel();
                    }
                });

                Button cancel = (Button) mVerticalDialog.findViewById(R.id.btn_ok);
                cancel.setVisibility(View.GONE);
                mVerticalDialog.show();

            } else {
                Constants.mOrderType = orderFormDetailsObj.getOrderType().trim();
                mTextViewOrderType.setText(Constants.mOrderType);
            }

        } else {
            Utils.showToast(mContext, "You have no order type.\n Please conatct admin");
        }
    }

    public void ShowGstTypeListDialog() {
        String taxTypeString = orderFormDetailsObj.getTaxType();
        if (taxTypeString != null && taxTypeString.trim().length() > 0) {
            String[] taxTypesList = taxTypeString.split(",");

            final Dialog mVerticalDialog = new Dialog(ActivityDOFilter.this,
                    R.style.PauseDialog);
            mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mVerticalDialog.setContentView(R.layout.select_with_search);
            mVerticalDialog.setCancelable(false);

            TextView title = (TextView) mVerticalDialog.findViewById(R.id.title);
            title.setText("Please select GST type");
            ListView dialogList = (ListView) mVerticalDialog.findViewById(R.id.list);

            final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this,
                    R.layout.activity_listview, taxTypesList);
            dialogList.setAdapter(adapter);

            EditText searchText = (EditText) mVerticalDialog
                    .findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
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
                public void onItemClick(AdapterView<?> arg0, View arg1,
                                        int position, long arg3) {
                    getWindow()
                            .setSoftInputMode(
                                    WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    Constants.mChosenGstType = adapter.getItem(position);
                    textViewGst.setText(Constants.mChosenGstType);
                    mVerticalDialog.cancel();
                }
            });

            Button cancel = (Button) mVerticalDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);
            mVerticalDialog.show();
        }

    }

    public void ShowBargainListDialog() {
        final BagainListForDOAdapter adapterCust = new BagainListForDOAdapter(mContext,R.layout.customer_list_child_material,BargainValList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_bargain_search);
        mDialogCustomer.setCancelable(false);
        TextView title = mDialogCustomer.findViewById(R.id.title);

        title.setText("Choose Bargain for delivery order");

        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        final ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setChoiceMode(ListView.CHOICE_MODE_MULTIPLE);
        dialogList.setAdapter(adapterCust);

        Button addnewcustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setText("Done");
        addnewcustomer.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v)
            {
                SaudaDetails bargainItem;
                selectedBargainList=new ArrayList<>();
                final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
                int checkedItemsCount = checkedItems.size();
                if (checkedItemsCount > 0) {
                    for (int i = 0; i < checkedItemsCount; ++i) {
                        int position = checkedItems.keyAt(i);
                        if (checkedItems.valueAt(i))
                        {
                            bargainItem = adapterCust.getItem(position);
                            selectedBargainList.add(bargainItem);
                        }
                    }
                } else {
                    Utils.showToast(mContext, "Please select at least 1 bargain");
                }
                if(selectedBargainList.size()>0)
                {
                    mDialogCustomer.cancel();
                }
            }
        });

        Button back = (Button) mDialogCustomer.findViewById(R.id.back);
//        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                mDialogCustomer.cancel();
            }
        });

        mDialogCustomer.show();
    }

    public void showChosenProductRelatedDetails()
    {
        textViewProduct.setText("Product : " + Constants.selectedProductOfBargain.getDesc());
    }

    public void ShowNextSixDaysToSwapWithToday(ArrayList<String> days) {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title = (TextView) mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a day to swap with " + Constants.dayOfWeekForCustomer);
        ListView dialogList = (ListView) mDialogDepotName.findViewById(R.id.list);

        SwapDaysAdapter SwapAdapter = new SwapDaysAdapter(mContext,
                R.layout.route_list_child, days);
        dialogList.setAdapter(SwapAdapter);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View v,
                                    int position, long arg3) {
                mDialogDepotName.cancel();
                TextView swappedDayTV = (TextView) v.findViewById(R.id.list_details);
                swappedDay = swappedDayTV.getText().toString();
                swappedDate = Utils.getDateOfSelectedDay(position + 1);//yyyy-MM-dd
                todaysDate = Utils.getDateOfSelectedDay(0);//yyyy-MM-dd
                new GPSTracker(mContext);
                FlowofOrder(7);


            }
        });

        Button cancel = (Button) mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                mDialogDepotName.cancel();
            }
        });
//		cancel.setVisibility(View.INVISIBLE);
        mDialogDepotName.show();
    }

    public void ChooseOrderType() {

        if (NotCheckedOut(mContext)) {
            disableSelectRouteCustButtonsForCheckedInCustomers();
            mSelectedTodayRouteDetails = mAceDnsDatabase.getRouteDetailsByCustomerCode(PreferenceData.getCheckInOutEmpCode(mContext));
            setRouteName();
            isRouteOk = true;
            Constants.selectedCustomer = mAceDnsDatabase.getCustomerDetailsById(PreferenceData.getCheckInOutEmpCode(mContext));
            customerSelectionCommonProcess();
//			FlowofOrder(2);

        } else if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary") || Constants.isVanSales) {
            orderAuditType = "Secondary";
            GoToRouteCustomerSelectionProcess();
        }
        else
        {
            final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
            dialgoCondition.setCancelable(false);
            dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
            dialgoCondition.getWindow().setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

            TextView txtMsg = (TextView) dialgoCondition.findViewById(R.id.title);
            txtMsg.setText("Select Order Type.");

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
                            } else {
                                orderAuditType = "Secondary";
                            }
                            dialgoCondition.cancel();

                            GoToRouteCustomerSelectionProcess();
                        }
                    });
            dialgoCondition.show();
        }

    }

    public void ShowNoOrderDialog() {
        final Dialog noOrderDialog = new Dialog(ActivityDOFilter.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.user_instruction_dialog);
        noOrderDialog.setCancelable(false);
        TextView title = (TextView) noOrderDialog.findViewById(R.id.title);
        title.setText("Please state the reason for no order");
        final EditText edReason = (EditText) noOrderDialog
                .findViewById(R.id.ed_input);
        final Button submit = (Button) noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                noOrderDialog.cancel();

                Boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
                if (isTimeAutomatic) {
                    new GPSTracker(mContext);
                    submit.setEnabled(false);
                    String reason = "";
                    reason = edReason.getText().toString();
                    String timeStamp = Constants.dateString
                            + new SimpleDateFormat("_HHmmss").format(Calendar
                            .getInstance().getTime());
                    timeStamp = timeStamp.replace("_", "");
                    MakeLocalAndServerDataSavingProcess(reason, timeStamp, true);
                } else {
                    Utils.showSettingsAlertToChangeTimeZone(mContext);
                }

            }
        });
        noOrderDialog.show();
    }

    private void MakeLocalAndServerDataSavingProcess(String reason, String timeStamp, Boolean isSuccessHintRemarksTableInsertion) {
        CustomerVisitSequenceUpdationProcess();
        Boolean isSuccessInsertToLocationTable, isSuccessInsertToOrderHeaderTable;
        mAceDnsTransactionDatabase.beginTransaction();
        isSuccessInsertToOrderHeaderTable = mAceDnsTransactionDatabase.insertToOrderHeaderTable1("NO", reason, timeStamp, "", "", "", "", "NO", "", "", "", "");
        isSuccessInsertToLocationTable = mAceDnsTransactionDatabase.insertToLocationTable1("NO", timeStamp);
        if (isSuccessInsertToLocationTable && isSuccessInsertToOrderHeaderTable && isSuccessHintRemarksTableInsertion) {
            mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
            mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
            boolean isExist = mAceDnsTransactionDatabase.IsUnuploadedRoutePlanExist();
            int recordcount = mAceDnsTransactionDatabase.GetUnuploadedCustomerCount();
            if (recordcount > 0) {
                new TRANS_SubmitNewCustomerDetailsTask(ActivityDOFilter.this, true, true, "SYNC").execute();
            } else {
                if (isExist) {
                    new TRANS_PendingRoutePlanBeforeOtherTxn(ActivityDOFilter.this, "ORDER").execute();
                } else {
                    new TRANS_SubmitOrderTask(ActivityDOFilter.this, true).execute();
                }
            }
        } else {
            mButtonNoOrder.setEnabled(true);
            mAceDnsTransactionDatabase.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
            mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
            Toast.makeText(mContext, "Sorry! memory related fatal exception found. Need to reenter data", Toast.LENGTH_LONG).show();
        }


    }

    private void CustomerVisitSequenceUpdationProcess() {
        if (Constants.orderFormDetailsObj.getvisit_sequence().equalsIgnoreCase("yes")) {
            String visitSequenceValue = mAceDnsDatabase.getLastCustomerVisitSequenceForGivenDayAndRouteCode(Constants.dayOfWeekForCustomer, mSelectedRouteDetailsVisitSequence.getRouteCode());
            if (Utils.isNumeric(visitSequenceValue)) {
                int visitSequence = Integer.parseInt(visitSequenceValue) + 1;
                mAceDnsDatabase.updateVisitSequenceOfCurrentCustomerToLast(visitSequence);
            }
        }
    }

    public void showInstructionWithHintDialog() {
        final Dialog noOrderDialog = new Dialog(ActivityDOFilter.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.hint_remarks_dialog);
        noOrderDialog.setCancelable(false);
        TextView title = (TextView) noOrderDialog.findViewById(R.id.title);
        TextView title2 = (TextView) noOrderDialog.findViewById(R.id.title2);
        title.setText("Please state the reason for no order");
        title2.setText("Any specific Requirement?");
        final EditText edReason = (EditText) noOrderDialog
                .findViewById(R.id.ed_input);
        final Button submit = (Button) noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                noOrderDialog.cancel();

                Boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
                if (isTimeAutomatic) {
                    new GPSTracker(mContext);
                    submit.setEnabled(false);
                    String reason = "";
                    reason = edReason.getText().toString();
                    String timeStamp = Constants.dateString
                            + new SimpleDateFormat("_HHmmss").format(Calendar
                            .getInstance().getTime());
                    timeStamp = timeStamp.replace("_", "");
                    Boolean isSuccessHintRemarksTableInsertion = true;
                    if (selectedHintRemarksId != -1) {
                        isSuccessHintRemarksTableInsertion = mAceDnsTransactionDatabase.insertToHintRemarksDetailsTable1("NO", timeStamp, hintRemarksValList.get(selectedHintRemarksId - 1));
                    }
                    MakeLocalAndServerDataSavingProcess(reason, timeStamp, isSuccessHintRemarksTableInsertion);
                } else {
                    Utils.showSettingsAlertToChangeTimeZone(mContext);
                }

            }
        });
        RadioGroup rgp = (RadioGroup) noOrderDialog.findViewById(R.id.radiogroup);
        rgp.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(RadioGroup radioGroup, int id) {
                selectedHintRemarksId = id;
            }
        });

        RadioGroup.LayoutParams rprms;

        for (int i = 0; i < hintRemarksValList.size(); i++) {
            RadioButton radioButton = new RadioButton(this);
            radioButton.setText(hintRemarksValList.get(i));
            radioButton.setId(i + 1);
            radioButton.setTextColor(getResources().getColor(R.color.text_color));
            if (selectedHintRemarksId == i + 1) {
                radioButton.setChecked(true);
            }
            rprms = new RadioGroup.LayoutParams(WindowManager.LayoutParams.WRAP_CONTENT, WindowManager.LayoutParams.WRAP_CONTENT);
            rgp.addView(radioButton, rprms);
        }
        noOrderDialog.show();
    }

    public void saveSwapDataToDatabase() {
        String timeStamp = Constants.dateString
                + new SimpleDateFormat("HHmmss").format(Calendar
                .getInstance().getTime());

        mAceDnsTransactionDatabase.INSERTtoSwapTable(Constants.dayOfWeekForCustomer, swappedDay, todaysDate, swappedDate, timeStamp, "TS");
        mAceDnsTransactionDatabase.insertToLocationTable("TS", timeStamp);
        Constants.dayOfWeekForCustomer = swappedDay;
    }

}
