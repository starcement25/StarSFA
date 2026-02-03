package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.lifecycle.ViewModelProvider;
import androidx.viewpager2.widget.ViewPager2;

import com.forcepower.acedns.backgroundTask.TRANS_PendingRoutePlanBeforeOtherTxn;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNewCustomerDetailsTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourDaySwapTransactionTask;
import com.forcepower.acedns.fragments.FragmentOne;
import com.forcepower.acedns.fragments.SharedViewModel;
import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.DestinationAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.adapter.ViewPagerAdapter;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.DestinationMaster;
import com.forcepower.acedns.bean.OutstandingDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.bean.SelfAppraisalDetailsCustomerWise;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.List;

import static com.forcepower.acedns.R.id.textViewRouteValue;
import static com.forcepower.acedns.constants.Constants.isCheckInToNewCustomer;
import static com.forcepower.acedns.constants.Constants.mFreightComponentAmountFor;
import static com.forcepower.acedns.constants.Constants.orderAuditType;
import static com.forcepower.acedns.constants.Constants.orderFormDetailsObj;
import static com.forcepower.acedns.constants.Constants.taggedCustomerCode;
import static com.forcepower.acedns.constants.Constants.uomToBeShownForProduct;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInRoute;

public class ActivityOrderFilterAlternateDesign extends AppCompatActivity {
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonOrderType = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonGst = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonFreight = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonDestination = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonNoOrder = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCustomerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewDealerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewRouteName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewCustomerVisitDay = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCreditLimit = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewOrderType = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewGst = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewFreight = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewFreightAmount = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewDestination = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewNoofInvoice = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewInvoiceAmount = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewDueAmount = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewVerticalValue = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewBranchValue = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView customerWiseTrgtAchvmntTvMonth = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView customerWiseTrgtAchvmntTvTarget = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView customerWiseTrgtAchvmntTvAchievement = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutSelectCustomer = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutAddCustomer = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutSelectRoute = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout frameLayoutSwapDay = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutOutstanding = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutOrderType = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout linearLayoutGst = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutFreight = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutDestination = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout customerWiseTrgtAchvmntLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;

    TabLayout mTabs;
    View mIndicator;
    ViewPager2 mViewPager;
    private int indicatorWidth;
    RoutePlanDetails routeplandetails;
    public String mRouteName = "";
    RoutePlanTransAdapter RoutePlanAdapter;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    ProgressDialog mStepProgressDialog;
    Handler mStepHandler;
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
    int selectedHintRemarksId = -1;
    Boolean clickedOnSelectCustomerButton = false;
    private String[] mVerticalValueList;
    private String mRouteCode = "";
    private String mRdsCode = "";
    private String mDealerName = "";
    private SharedViewModel viewModel;
    public static String customerName = "";
    String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_filter_alternate_design);
        mContext = ActivityOrderFilterAlternateDesign.this;
        mSelectedRouteDetails = null;
        RegisterActivities.registerActivity(this);
        mFreightComponentAmountFor = "";
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        viewModel = new ViewModelProvider(this).get(SharedViewModel.class);

        Utils.cancelProgressDialog();
        InitializeView();
        ClearText();
        final List<String> fragmentTitleList = new ArrayList<>();
        ViewPagerAdapter adapter2 = new ViewPagerAdapter(this);
        adapter2.addFragment(FragmentOne.newInstance("Recommended", ""), "Recommended Counter");
        fragmentTitleList.add("Recommended Counter");
        mViewPager.setUserInputEnabled(false);
        mViewPager.setAdapter(adapter2);
        new TabLayoutMediator(mTabs, mViewPager, (tab, position) -> tab.setText(fragmentTitleList.get(position))).attach();
        mTabs.post(() -> {
            indicatorWidth = mTabs.getWidth() / mTabs.getTabCount();
            FrameLayout.LayoutParams indicatorParams = (FrameLayout.LayoutParams) mIndicator.getLayoutParams();
            indicatorParams.width = indicatorWidth;
            mIndicator.setLayoutParams(indicatorParams);
        });
        mViewPager.registerOnPageChangeCallback(new ViewPager2.OnPageChangeCallback() {
            @Override
            public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {
                FrameLayout.LayoutParams params = (FrameLayout.LayoutParams) mIndicator.getLayoutParams();
                float translationOffset = (positionOffset + position) * indicatorWidth;
                params.leftMargin = (int) translationOffset;
                mIndicator.setLayoutParams(params);
                super.onPageScrolled(position, positionOffset, positionOffsetPixels);
            }

            @Override
            public void onPageSelected(int position) {
                super.onPageSelected(position);
                Constants.CUSTOMER_INFO_FLAG = position;
                if (!customerName.matches("")) {
                    if (position == 0) {
                        if (!FragmentOne.recommendedCustomer.matches(""))
                            viewModel.setRefreshFlag(position + "");
                    } else if (position == 1) {
                        if (!FragmentOne.additionalCustomer.matches(""))
                            viewModel.setRefreshFlag(position + "");
                    } else if (position == 2) {
                        if (!FragmentOne.newCustomer.matches(""))
                            viewModel.setRefreshFlag(position + "");
                    }
                }

            }

            @Override
            public void onPageScrollStateChanged(int state) {
                super.onPageScrollStateChanged(state);
                Log.d("", "");
            }
        });
        routeplandetails = mAceDnsDatabase.getRoutePlanDetailsObj();
        mButtonBack.setOnClickListener(v -> finish());

        mButtonDestination.setOnClickListener(v -> FlowofOrder(5));

        mButtonNoOrder.setOnClickListener(v -> {
            if (Constants.selectedCustomeradditional == null) {
                Utils.showToast(mContext, "--Please select the customer");
                return;
            } else {
                Constants.selectedCustomer = Constants.selectedCustomeradditional;
            }
            mButtonNoOrder.setEnabled(false);
            if (orderFormDetailsObj.getHintsRemarks().equalsIgnoreCase("yes") || orderFormDetailsObj.getHintsRemarks().equalsIgnoreCase("no_order")) {
                hintRemarksValList = new ArrayList<>();
                String hintRemarksValString = orderFormDetailsObj.getHintsRemarksVal();
                if (hintRemarksValString.contains("#")) {
                    String[] arrayOfData = hintRemarksValString.split("#");
                    Collections.addAll(hintRemarksValList, arrayOfData);
                } else {
                    hintRemarksValList.add(hintRemarksValString);
                }
                showInstructionWithHintDialog();
            } else {
                ShowNoOrderDialog();
            }
        });

        mButtonOrderType.setOnClickListener(v -> ShowOrderTypeListDialog());
        mButtonGst.setOnClickListener(v -> ShowGstTypeListDialog());

        mButtonFreight.setOnClickListener(v -> ShowFreightTypeListDialog());

        mButtonSubmit.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            if (Constants.selectedCustomeradditional == null) {
                Utils.showToast(mContext, "Please select the customer");
                return;
            } else {
                Constants.selectedCustomer = Constants.selectedCustomeradditional;
            }
            ProcessAndMoveToNext();
        });


        mStepHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mStepProgressDialog.dismiss();
                final int step = msg.getData().getInt("STEP");
                ActivityOrderFilterAlternateDesign.this.runOnUiThread(() -> {
                    switch (step) {
                        case 1:
                            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan") && !Constants.userDetailsObj.gettour_plan_daywise_distributor().equalsIgnoreCase("yes")) {
                                if (NotCheckedOut(mContext) && !Constants.CurrentOrderCollectionTransactionType.matches("TO")) {
                                    disableSelectRouteCustButtonsForCheckedInCustomers();
                                    mSelectedTodayRouteDetails = mRoutePlanListofToday.get(getPositionOfCurrentCheckedInRoute(true, mContext, mRoutePlanListofToday, null));
                                    setRouteName();
                                    ChangeBackgroundColor(2);
                                    FlowofOrder(2);
                                } else if ((mRoutePlanListofToday.isEmpty())) {
                                    Utils.showToast(mContext, "No Route Found. ");
                                    finish();
                                } else if (mRoutePlanListofToday.size() == 1) {
                                    mSelectedTodayRouteDetails = mRoutePlanListofToday.get(0);
                                    setRouteName();
                                    ChangeBackgroundColor(2);
                                    FlowofOrder(2);
                                } else {
                                    ShowTodayRoutePlanListDialog(mRoutePlanListofToday);
                                }
                            } else {
                                if (NotCheckedOut(mContext) && !Constants.CurrentOrderCollectionTransactionType.matches("TO")) {
                                    disableSelectRouteCustButtonsForCheckedInCustomers();
                                    mSelectedRouteDetails = mRouteDetailsList.get(getPositionOfCurrentCheckedInRoute(false, mContext, null, mRouteDetailsList));
                                    mRouteName = mSelectedRouteDetails.getRouteName();
                                    mTextViewRouteName.setVisibility(View.VISIBLE);
                                    mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
                                    ChangeBackgroundColor(2);
                                    FlowofOrder(2);
                                } else if (mRouteDetailsList.size() > 1) {
                                    ShowRouteListDialog(mRouteDetailsList);
                                } else if (mRouteDetailsList.size() == 1) {
                                    mSelectedRouteDetails = mRouteDetailsList.get(0);
                                    mRouteName = mSelectedRouteDetails.getRouteName();
                                    mTextViewRouteName.setVisibility(View.VISIBLE);
                                    mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
                                    ChangeBackgroundColor(2);
                                    FlowofOrder(2);
                                } else {
                                    Toast.makeText(mContext, "No route found.\n Please contact your admin", Toast.LENGTH_LONG).show();
                                }
                            }
                            break;
                        case 2:
                            if ((NotCheckedOut(mContext) && !Constants.CurrentOrderCollectionTransactionType.matches("TO")) || orderAuditType.equalsIgnoreCase("primary")) {
                                customerSelectionCommonProcess();
                            } else {
                                if (!Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes") || Constants.CurrentOrderCollectionTransactionType.matches("TO") || clickedOnSelectCustomerButton) {
                                    customerSelectionCommonProcess();
                                }
                            }
                            break;
                        case 3:
                            if (mVerticalValueList.length > 1) {
                                ShowVericalValueListDialog();
                            } else if (mVerticalValueList.length == 1) {
                                Constants.mVerticalValue = mVerticalValueList[0];
                                mTextViewVerticalValue.setText("Vertical : " + Constants.mVerticalValue);
                                if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                                    FlowofOrder(4);
                                }
                            } else {
                                Toast.makeText(mContext, "No vertical found.\n Please contact your admin", Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 4:
                            if (mBranchMasterDetailsList.size() > 1) {
                                ShowBranchorDepoNameDialog();
                            } else if (mBranchMasterDetailsList.size() == 1) {
                                Constants.selectedBranch = mBranchMasterDetailsList.get(0);
                                mTextViewBranchValue.setText("Branch : " + Constants.selectedBranch.getBranchName());
                            } else {
                                Toast.makeText(mContext, "No branch/depot found.\n Please contact your admin", Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 5:
                            if (!mDestinationMasterList.isEmpty()) {
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
                });

            }
        };

        ChooseOrderType();
        ChangeBackgroundColor(1);

        try {
            if (!Constants.menuDetailsObj.getcust_class().isEmpty()) {
                custClassSelectionProcess();
            }
        } catch (Exception ignored) {
        }
    }

    public void ProcessAndMoveToNext() {
        if (Constants.productDetailsObj.getBranchWiseMRP().equalsIgnoreCase("yes")) {
            String branchListForCurrentEmployee = mAceDnsDatabase.GETBranchOfCurrentEmp().trim();
            if (branchListForCurrentEmployee.isEmpty()) {
                Utils.showToast(mContext, "This employee is not mapped with a branch\nPlease contact admin");
            } else if (branchListForCurrentEmployee.contains(",")) {
                String branchListForCurrentCust = mAceDnsDatabase.GETBranchOfCurrentCust(Constants.selectedCustomer.getCustomerCode());
                if (branchListForCurrentCust.isEmpty()) {
                    Utils.showToast(mContext, "This customer is not mapped with a branch\nPlease contact admin");
                } else if (branchListForCurrentCust.contains(",")) {
                    ShowBranchListForMrpDialog();
                } else {
                    Constants.selectedBranchForMrp = mAceDnsDatabase.getBranchListOfCurrentCust(Constants.selectedCustomer.getCustomerCode()).get(0);
                    if (mAceDnsDatabase.isEmployeeMappedWithCurrentBranch(Constants.selectedBranchForMrp.getBranchCode())) {
                        moveToNextScreen();
                    } else {
                        Utils.showToast(mContext, "Current User is not mapped with selected customer's branch\nPlease contact admin");
                    }
                }
            } else {
                Constants.selectedBranchForMrp = mAceDnsDatabase.getBranchListOfCurrentEmp().get(0);
                moveToNextScreen();
            }
        } else {
            moveToNextScreen();
        }
    }

    private void moveToNextScreen() {
        Intent intent = new Intent(ActivityOrderFilterAlternateDesign.this, OrderFormActivityAlternateDesign.class);
        startActivity(intent);
    }

    @SuppressLint("SetTextI18n")
    public void setRouteName() {
        mRouteName = mSelectedTodayRouteDetails.getRouteName();
        mTextViewRouteName.setVisibility(View.VISIBLE);
        mTextViewRouteName.setText("Route : " + mSelectedTodayRouteDetails.getRouteName());
    }

    @SuppressLint("SetTextI18n")
    public void customerSelectionCommonProcess() {
        clickedOnSelectCustomerButton = false;
        if (NotCheckedOut(mContext) && !Constants.CurrentOrderCollectionTransactionType.matches("TO")) {
            mrouteCustSelectionProcess();
        } else if (mCustomerDetailsList.size() > 1) {
            ShowCustomerListDialog();
        } else if (mCustomerDetailsList.size() == 1) {
            Constants.selectedCustomer = mCustomerDetailsList.get(0);
            multipleUomDecision();
            Constants.selectedCustomer.setRouteName(mRouteName);
            mRdsCode = Constants.selectedCustomer.getRdsTag();
            DealerName(mRdsCode);
            mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
            ShowSelfAppraisalDetailsForCurrentCustomer();
            AvailableCredit(Constants.selectedCustomer.getCreditLimit());
            if (Constants.menuDetailsObj.getOutstanding().equalsIgnoreCase("yes") && !Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("R")) {
                OutstandingAmount();
            } else {
                mLinearLayoutOutstanding.setVisibility(View.GONE);
            }
            if (!CheckCustomerInformation()) {
                ShowEditCustomerDialog();
            } else {
                if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes") && !orderFormDetailsObj.getInputScreenNormal().equalsIgnoreCase("yes")) {
                    FlowofOrder(3);
                } else {
                    if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                        FlowofOrder(4);
                    }
                }
            }
        } else {
            if (orderFormDetailsObj.getAddCustomer().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("add_new_customer") && !Constants.CurrentOrderCollectionTransactionType.matches("TO") && orderAuditType.equalsIgnoreCase("secondary")) {
                ShowAlertAddNewCustomer();
            } else {
                Toast.makeText(mContext, "No customer found.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void mrouteCustSelectionProcess() {
        multipleUomDecision();
        Constants.selectedCustomer.setRouteName(mRouteName);
        mRdsCode = Constants.selectedCustomer.getRdsTag();
        DealerName(mRdsCode);
        mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
        ShowSelfAppraisalDetailsForCurrentCustomer();
        AvailableCredit(Constants.selectedCustomer.getCreditLimit());
        if (Constants.menuDetailsObj.getOutstanding().equalsIgnoreCase("yes") && !Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("R")) {
            OutstandingAmount();
        }
        if (!CheckCustomerInformation()) {
            ShowEditCustomerDialog();
        } else {
            if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes") && !orderFormDetailsObj.getInputScreenNormal().equalsIgnoreCase("yes")) {
                FlowofOrder(3);
            } else {
                if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                    FlowofOrder(4);
                }
            }
        }
    }

    private void multipleUomDecision() {
        uomToBeShownForProduct = "uom1";
        try {
            if (orderFormDetailsObj.getcust_type_wise_UOM().equalsIgnoreCase("yes")) {
                String[] uomListForCustomerType = Constants.orderFormDetailsObj.getcust_type_wise_UOM_val().split(",");
                for (String s : uomListForCustomerType) {
                    String[] uomForCustTypeArray = s.split("#");
                    if (uomForCustTypeArray[0].equalsIgnoreCase(Constants.selectedCustomer.getCustomerType())) {
                        uomToBeShownForProduct = uomForCustTypeArray[1].toLowerCase();
                        break;
                    }
                }
            }
        } catch (Exception ignored) {
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

    private void ShowAlertAddNewCustomer() {
        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(ActivityOrderFilterAlternateDesign.this);
        alertDialogBuilder
                .setMessage("Do you want to add customer of route " + mRouteName + " ?")
                .setCancelable(false)
                .setPositiveButton("Yes", (dialog, id) -> {
                    Constants.isOrederToNewCustomer = true;
                    isCheckInToNewCustomer = false;
                    Intent intent = new Intent(ActivityOrderFilterAlternateDesign.this, AddNewCustActivity.class);
                    intent.putExtra("ROUTENAME", mRouteName);
                    intent.putExtra("ROUTECODE", mRouteCode);

                    startActivity(intent);
                    dialog.cancel();
                })
                .setNegativeButton("No", (dialog, id) -> dialog.cancel());
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void DealerName(String rdscode) {
        if (Constants.orderFormDetailsObj.getTagDistributor().equalsIgnoreCase("yes") && !rdscode.trim().isEmpty()) {
            ArrayList<CustomerDetails> mDealerNameList = mAceDnsDatabase.getDealerNameForOrder(mRdsCode);
            if (mDealerNameList.size() == 1) {
                int position = 0;
                mDealerName = mDealerNameList.get(position).getCustomerName();
                taggedCustomerCode = mDealerNameList.get(position).getCustomerCode();
                mTextViewDealerName.setText("Dealer : " + mDealerName);
            } else if (mDealerNameList.size() > 1) {
                ShowDealerChoseDialog(mDealerNameList);
            } else {
                mTextViewDealerName.setText("Dealer : ");
                mTextViewDealerName.setVisibility(View.GONE);
            }
        } else {
            mTextViewDealerName.setText("Dealer : ");
            mTextViewDealerName.setVisibility(View.GONE);
        }
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        mTabs = findViewById(R.id.tab);
        mIndicator = findViewById(R.id.indicator);
        mViewPager = findViewById(R.id.viewPagerOne);

        mTextViewRouteName = findViewById(textViewRouteValue);
        textViewCustomerVisitDay = findViewById(R.id.textViewCustomerVisitDay);
        mTextViewCustomerName = findViewById(R.id.textViewCustomerValue);
        mTextViewDealerName = findViewById(R.id.textViewBargainNumber);

        mTextViewCreditLimit = findViewById(R.id.textViewCreditLimitValue);

        mTextViewNoofInvoice = findViewById(R.id.textViewInvoiceNo);
        mTextViewInvoiceAmount = findViewById(R.id.textViewInvoice);
        mTextViewDueAmount = findViewById(R.id.textViewDue);

        mTextViewVerticalValue = findViewById(R.id.textViewVerticalValue);
        mTextViewBranchValue = findViewById(R.id.textViewBranchValue);
        customerWiseTrgtAchvmntTvMonth = findViewById(R.id.customerWiseTrgtAchvmntTvMonth);
        customerWiseTrgtAchvmntTvTarget = findViewById(R.id.customerWiseTrgtAchvmntTvTarget);
        customerWiseTrgtAchvmntTvAchievement = findViewById(R.id.customerWiseTrgtAchvmntTvAchievement);

        mTextViewOrderType = findViewById(R.id.textViewOrderType);
        textViewGst = findViewById(R.id.textViewGst);
        mTextViewFreight = findViewById(R.id.textViewFreight);
        textViewFreightAmount = findViewById(R.id.textViewFreightAmount);
        mTextViewDestination = findViewById(R.id.textViewDestination);

        mFrameLayoutSelectCustomer = findViewById(R.id.frameLayoutSelectCustomer);
        mFrameLayoutSelectRoute = findViewById(R.id.frameLayoutSelectRoute);
        mFrameLayoutAddCustomer = findViewById(R.id.frameLayoutAddCustomer);
        frameLayoutSwapDay = findViewById(R.id.frameLayoutSwapDay);

        mLinearLayoutOutstanding = findViewById(R.id.linearLayoutOutstanding);
        mLinearLayoutOrderType = findViewById(R.id.linearLayoutOrderType);
        linearLayoutGst = findViewById(R.id.linearLayoutGst);
        mLinearLayoutFreight = findViewById(R.id.linearLayoutFreight);
        mLinearLayoutDestination = findViewById(R.id.linearLayoutDestination);
        customerWiseTrgtAchvmntLayout = findViewById(R.id.customerWiseTrgtAchvmntLayout);

        mButtonSubmit = findViewById(R.id.btn_Submi);
        mButtonBack = findViewById(R.id.back);
        mButtonOrderType = findViewById(R.id.buttonOrderType);
        mButtonGst = findViewById(R.id.buttonGst);
        mButtonFreight = findViewById(R.id.buttonFreight);
        mButtonDestination = findViewById(R.id.buttonDestination);
        mButtonNoOrder = findViewById(R.id.no_ordr);

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

        if (orderFormDetailsObj.getFreightComponent().trim().isEmpty()) {
            mLinearLayoutFreight.setVisibility(View.GONE);
        }

        if (orderFormDetailsObj.getOrderType().trim().isEmpty()) {
            mLinearLayoutOrderType.setVisibility(View.GONE);
        }
        if (orderFormDetailsObj.getTaxType().trim().isEmpty()) {
            linearLayoutGst.setVisibility(View.GONE);
        }

        if (!orderFormDetailsObj.getDestination().equalsIgnoreCase("yes")) {
            mLinearLayoutDestination.setVisibility(View.GONE);
        }

        if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("no")) {
            mTextViewVerticalValue.setVisibility(View.GONE);
        }
        if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("no")) {
            mTextViewBranchValue.setVisibility(View.GONE);
        }

        mImageViewHeaderLogo = findViewById(R.id.imagelogo);

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivityOrderFilterAlternateDesign.this) + "~" + Utils.getDBVersion(ActivityOrderFilterAlternateDesign.this));
        ChangeBackgroundColor(0);
        if (orderFormDetailsObj.getCreditLimit().equalsIgnoreCase("no")) {
            mTextViewCreditLimit.setVisibility(View.GONE);
        }
        mLinearLayoutOutstanding.setVisibility(View.GONE);
    }

    public void ClearText() {
        mTextViewRouteName.setText("");
        mTextViewCustomerName.setText("");
        mTextViewDealerName.setText("");
        mTextViewCreditLimit.setText("");
        mTextViewNoofInvoice.setText("");
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
        } catch (Exception ignored) {
        }
    }

    public void OutstandingAmount() {
        mLinearLayoutOutstanding.setVisibility(View.VISIBLE);
        ArrayList<OutstandingDetails> outstandingList = mAceDnsDatabase.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
        double invoiceamount = 0;
        double dueamount = 0;
        if (outstandingList != null && !outstandingList.isEmpty()) {
            for (int ii = 0; ii < outstandingList.size(); ii++) {
                if (!outstandingList.get(ii).getInvoice_amount().isEmpty()) {
                    invoiceamount = invoiceamount + Double.parseDouble(outstandingList.get(ii).getInvoice_amount());
                }
                if (!outstandingList.get(ii).getDue_amount().isEmpty()) {
                    dueamount = dueamount + Double.parseDouble(outstandingList.get(ii).getDue_amount());
                }
            }
        }
        if (outstandingList != null) {
            mTextViewNoofInvoice.setText(String.valueOf(outstandingList.size()));
        } else {
            mTextViewNoofInvoice.setText(String.valueOf(0));
        }
        mTextViewInvoiceAmount.setText(Constants.defaultFormat.format(invoiceamount));
        mTextViewDueAmount.setText(Constants.defaultFormat.format(dueamount));
    }

    @SuppressLint("SetTextI18n")
    public void AvailableCredit(String value) {
        mTextViewCreditLimit.setText("Credit Limit : " + value);
    }

    @SuppressLint("SetTextI18n")
    public void FlowofOrder(final int step) {
        mStepProgressDialog = new ProgressDialog(mContext);
        mStepProgressDialog.setMessage("Preparing Data.\nPlease wait..");
        mStepProgressDialog.setCancelable(false);
        mStepProgressDialog.show();
        new Thread() {
            public void run() {
                switch (step) {
                    case 1:
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")) {
                            if (Constants.CurrentOrderCollectionTransactionType.matches("SO")) {
                                if (orderAuditType.equalsIgnoreCase("primary")) {
                                    mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForTodayPrimaryOrder(today);
                                } else {
                                    if (routeplandetails.getDistributorRoutePlanning().equalsIgnoreCase("yes") || routeplandetails.getDistributorRoutePlanningMultiple().equalsIgnoreCase("yes")) {
                                        mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForTodayForMultipleDistributorWiseRoutePlan(today);
                                    } else {
                                        mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForToday(today);
                                    }
                                }
                            } else {
                                if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes")) {
                                    mRoutePlanListofToday = mAceDnsTransactionDatabase.getRouteListForCustomersWhoseVisitDateToday(Constants.dayOfWeekForCustomer);
                                } else {
                                    mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanNotForToday(today);
                                }
                            }
                            mRoutePlanListofTodayForSearching = new ArrayList<>(mRoutePlanListofToday);
                        } else {
                            if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes")) {
                                mRouteDetailsList = mAceDnsTransactionDatabase.getRouteListForCustomersWhoseVisitDateNotToday2(Constants.dayOfWeekForCustomer);
                            } else {
                                mRouteDetailsList = mAceDnsDatabase.getRouteList();
                            }
                        }
                        break;
                    case 2:
                        String routeWiseDistSetup = orderFormDetailsObj.getroute_wise_distributor();
                        if (orderAuditType.equalsIgnoreCase("primary") && !routeWiseDistSetup.equalsIgnoreCase("yes")) {
                            String distributorRoutePlanningMultiple = routeplandetails.getDistributorRoutePlanningMultiple();
                            if (routeplandetails.getDistributorRoutePlanning().equalsIgnoreCase("yes") || distributorRoutePlanningMultiple.equalsIgnoreCase("yes")) {
                                mCustomerDetailsList = mAceDnsDatabase.GetDistributorCustomerListforRoutePlan(today);
                            } else {
                                mCustomerDetailsList = mAceDnsDatabase.getDistributorDetailsFromCustomerMaster();
                            }
                        } else if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("route_plan")) {
                            if (routeplandetails.getRouteCustomerPlanning().equalsIgnoreCase("yes")) {
                                String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
                                mRouteCode = mSelectedTodayRouteDetails.getRoutecode();
                                mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(today, mSelectedTodayRouteDetails.getRoutecode());
                            } else {
                                mRouteCode = mSelectedTodayRouteDetails.getRoutecode();
                                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedTodayRouteDetails.getRoutecode());
                            }
                        } else {
                            if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes")) {
                                if (Constants.CurrentOrderCollectionTransactionType.matches("SO")) {
                                    if (Constants.orderFormDetailsObj.getvisit_sequence().equalsIgnoreCase("yes")) {
                                        mRouteDetailsListForVisitSequence = mAceDnsDatabase.getRouteListForVisitSequenceAlreadyVisitedRoutes();
                                        if (!mRouteDetailsListForVisitSequence.isEmpty()) {
                                            boolean isCustomerFound = false;
                                            for (int i = 0; i < mRouteDetailsListForVisitSequence.size(); i++) {
                                                mSelectedRouteDetailsVisitSequence = mRouteDetailsListForVisitSequence.get(i);
                                                mRouteName = mSelectedRouteDetailsVisitSequence.getRouteName();
                                                ActivityOrderFilterAlternateDesign.this.runOnUiThread(() -> {
                                                    mTextViewRouteName.setVisibility(View.VISIBLE);
                                                    mTextViewRouteName.setText("Route : " + mSelectedRouteDetailsVisitSequence.getRouteName());
                                                });

                                                mCustomerDetailsList = new ArrayList<>();
                                                mCustomerDetailsList = mAceDnsDatabase.getCustomerListDayOfWeekWiseForRouteCode(Constants.dayOfWeekForCustomer, mSelectedRouteDetailsVisitSequence.getRouteCode());
                                                if (!mCustomerDetailsList.isEmpty()) {
                                                    isCustomerFound = true;
                                                    ActivityOrderFilterAlternateDesign.this.runOnUiThread(() -> {
                                                        mStepProgressDialog.dismiss();
                                                        customerSelectionCommonProcess();
                                                    });
                                                    break;
                                                }
                                            }
                                            if (!isCustomerFound) {
                                                showROutesForVisitSequence();
                                            }
                                        } else {
                                            showROutesForVisitSequence();
                                        }
                                    } else {
                                        if (Constants.userDetailsObj.gettour_plan_daywise_distributor().equalsIgnoreCase("yes")) {
                                            mRouteDetailsList = mAceDnsDatabase.getRouteListFromDistributorRouteRelationForTourPlanDayWise(Constants.dayOfWeekForCustomer);
                                            showRoutesForDayWiseDistributorRoutePlan();
                                        } else {
                                            mCustomerDetailsList = mAceDnsDatabase.getCustomerListDayOfWeekWise(Constants.dayOfWeekForCustomer);
                                            customerSelectionCommonProcess();
                                        }
                                    }
                                } else {
                                    mRouteCode = mSelectedRouteDetails.getRouteCode();
                                    mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRouteSkippingD(mSelectedRouteDetails.getRouteCode());
                                }
                            } else {
                                mRouteCode = mSelectedRouteDetails.getRouteCode();
                                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRouteDetails.getRouteCode());
                            }
                        }
                        break;
                    case 3:
                        int max;
                        max = mAceDnsDatabase.GetVerticalValue();
                        mVerticalValueList = new String[max];
                        System.arraycopy(Constants.mVerticalValueList, 0, mVerticalValueList, 0, Constants.mVerticalValueList.length);
                        break;
                    case 4:
                        mBranchMasterDetailsList = mAceDnsDatabase.getSaudaRDSListForOrder(Constants.selectedCustomer.getCustomerCode(), "");
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

    public void showCustomerInfo(View v) {
        String remarks_for_the_customer;
        if (Constants.CUSTOMER_INFO_FLAG == 0) {
            if (Constants.selectedCustomerRecommended == null)
                return;
            String remarks = mAceDnsDatabase.getLastRemarksOfCustomer(Constants.selectedCustomerRecommended.getCustomerCode());
            if (!remarks.isEmpty()) {
                remarks = " : " + remarks;
            }
            remarks_for_the_customer = Constants.selectedCustomerRecommended.getCustomerName() + ": " + Constants.selectedCustomerRecommended.getciLogic().split("-")[2] + remarks;
        } else {
            if (Constants.selectedCustomeradditional == null)
                return;
            String remarks = mAceDnsDatabase.getLastRemarksOfCustomer(Constants.selectedCustomeradditional.getCustomerCode());
            if (!remarks.isEmpty()) {
                remarks = " : " + remarks;
            }
            String customerLogic = Constants.selectedCustomeradditional.getciLogic();
            String logicText = "";
            if (customerLogic.contains("-")) {
                logicText = " : " + customerLogic.split("-")[2];
            }
            remarks_for_the_customer = Constants.selectedCustomeradditional.getCustomerName() + logicText + remarks;
        }
        Utils.showAlertDialogMaterial(remarks_for_the_customer, mContext, "Remarks");
    }

    public void showROutesForVisitSequence() {
        mRouteDetailsListForVisitSequence = mAceDnsDatabase.getRouteListForVisitSequence(Constants.dayOfWeekForCustomer);
        if (mRouteDetailsListForVisitSequence.isEmpty()) {
            Toast.makeText(mContext, "No route found.\n Please contact your admin", Toast.LENGTH_LONG).show();
        } else if (mRouteDetailsListForVisitSequence.size() == 1) {
            selectRouteFOrVisitSequenceAndGetCustomers(0);
        } else {
            ActivityOrderFilterAlternateDesign.this.runOnUiThread(new Runnable() {
                public void run() {
                    ShowRouteListDialogVisitSequence(mRouteDetailsListForVisitSequence);
                }
            });

        }
    }

    public void showRoutesForDayWiseDistributorRoutePlan() {
        if (mRouteDetailsList == null || mRouteDetailsList.isEmpty()) {
            Toast.makeText(mContext, "No route found.\n Please contact your admin", Toast.LENGTH_LONG).show();
        } else if (mRouteDetailsList.size() == 1) {
            ActivityOrderFilterAlternateDesign.this.runOnUiThread(() -> getCustomerDetailsForDistributorOfChosenRouteAndVisitDay(0));
        } else {
            ActivityOrderFilterAlternateDesign.this.runOnUiThread(() -> ShowRouteListDialogVisitDayWiseDistributorSelection(mRouteDetailsList));
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowTodayRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> routePlanListofToday) {
        final Dialog routePlanListDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routePlanListDialog.findViewById(R.id.list);
        RoutePlanAdapter = new RoutePlanTransAdapter(ActivityOrderFilterAlternateDesign.this, R.layout.route_list_child, routePlanListofToday);
        dialogList.setAdapter(RoutePlanAdapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routePlanListDialog.cancel();
            mSelectedTodayRouteDetails = routePlanListofToday.get(arg2);
            setRouteName();
            FlowofOrder(2);
        });
        ImageView image_cancel = routePlanListDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(arg0 -> {
            routePlanListDialog.cancel();
            finish();
        });
        Button cancel = routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routePlanListDialog.cancel());
        Button create_route = routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
        final EditText autoCompleteTextView1 = routePlanListDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.VISIBLE);
        autoCompleteTextView1.addTextChangedListener(new TextWatcher() {
            public void afterTextChanged(Editable s) {
            }

            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            public void onTextChanged(CharSequence s, int start, int before, int count) {
                String searchString = autoCompleteTextView1.getText().toString();
                int textLength = searchString.length();
                routePlanListofToday.clear();
                for (int i = 0; i < mRoutePlanListofTodayForSearching.size(); i++) {
                    String routeName = mRoutePlanListofTodayForSearching.get(i).getRouteName();
                    if (textLength <= routeName.length()) {
                        if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                            routePlanListofToday.add(mRoutePlanListofTodayForSearching.get(i));
                        }
                    }
                }
                RoutePlanAdapter.notifyDataSetChanged();
            }
        });
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(ActivityOrderFilterAlternateDesign.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routeDialog.cancel();
            mSelectedRouteDetails = routeList.get(arg2);
            mRouteName = mSelectedRouteDetails.getRouteName();
            mTextViewRouteName.setVisibility(View.VISIBLE);
            mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
            FlowofOrder(2);
        });
        ImageView image_cancel = routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(arg0 -> {
            routeDialog.cancel();
            finish();
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialogVisitSequence(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(ActivityOrderFilterAlternateDesign.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routeDialog.cancel();
            selectRouteFOrVisitSequenceAndGetCustomers(arg2);
        });
        ImageView image_cancel = routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(arg0 -> {
            routeDialog.cancel();
            finish();
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialogVisitDayWiseDistributorSelection(final ArrayList<RouteDetails> routeList) {
        final Dialog routeDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        RouteAdapter adapter = new RouteAdapter(ActivityOrderFilterAlternateDesign.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routeDialog.cancel();
            getCustomerDetailsForDistributorOfChosenRouteAndVisitDay(arg2);
        });
        ImageView image_cancel = routeDialog.findViewById(R.id.image_cancel);
        image_cancel.setVisibility(View.VISIBLE);
        image_cancel.setOnClickListener(arg0 -> {
            routeDialog.cancel();
            finish();
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void selectRouteFOrVisitSequenceAndGetCustomers(int arg2) {
        mSelectedRouteDetailsVisitSequence = mRouteDetailsListForVisitSequence.get(arg2);
        mRouteName = mSelectedRouteDetailsVisitSequence.getRouteName();
        mTextViewRouteName.setVisibility(View.VISIBLE);
        mTextViewRouteName.setText("Route : " + mSelectedRouteDetailsVisitSequence.getRouteName());
        mCustomerDetailsList = mAceDnsDatabase.getCustomerListDayOfWeekWiseForRouteCode(Constants.dayOfWeekForCustomer, mSelectedRouteDetailsVisitSequence.getRouteCode());
        customerSelectionCommonProcess();
    }

    @SuppressLint("SetTextI18n")
    public void getCustomerDetailsForDistributorOfChosenRouteAndVisitDay(int arg2) {
        mSelectedRouteDetails = mRouteDetailsList.get(arg2);
        mRouteName = mSelectedRouteDetails.getRouteName();
        mRouteCode = mSelectedRouteDetails.getRouteCode();
        mTextViewRouteName.setVisibility(View.VISIBLE);
        mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
        mCustomerDetailsList = mAceDnsDatabase.getDistributorDetailsFromCustomerMaster(mSelectedRouteDetails.getDistributorCode());
        customerSelectionCommonProcess();
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = mDialogCustomer.findViewById(R.id.title);

        if (orderAuditType.equalsIgnoreCase("primary")) {
            title.setText("Customers for primary order");
        } else if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes") && Constants.CurrentOrderCollectionTransactionType.matches("SO")) {
            title.setText(Constants.dayOfWeekForCustomer + "'s customers");
        } else {
            title.setText("Please select a customer of route " + mRouteName);
        }
        EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
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

        ListView dialogList = mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogCustomer.cancel();
            Constants.selectedCustomer = adapterCust.getItem(arg2);
            multipleUomDecision();
            Constants.selectedCustomer.setRouteName(mRouteName);
            custClassSelectionProcess();
            mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
            ShowSelfAppraisalDetailsForCurrentCustomer();
            if (Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes")) {
                String routeName = mAceDnsTransactionDatabase.getRouteNameFromRouteCode(Constants.selectedCustomer.getRouteCode());
                Constants.selectedCustomer.setRouteName(routeName);
                mTextViewRouteName.setVisibility(View.VISIBLE);
                mTextViewRouteName.setText("Route : " + routeName);
                textViewCustomerVisitDay.setVisibility(View.VISIBLE);
                textViewCustomerVisitDay.setText(visitDayString + Constants.dayOfWeekForCustomer);
            }
            mRdsCode = Constants.selectedCustomer.getRdsTag();
            DealerName(mRdsCode);
            AvailableCredit(Constants.selectedCustomer.getCreditLimit());
            if (Constants.menuDetailsObj.getOutstanding().equalsIgnoreCase("yes") && !Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("R")) {
                OutstandingAmount();
            }
            if (!CheckCustomerInformation()) {
                ShowEditCustomerDialog();
            } else {
                if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes") && !orderFormDetailsObj.getInputScreenNormal().equalsIgnoreCase("yes")) {
                    FlowofOrder(3);
                } else {
                    if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                        FlowofOrder(4);
                    }
                }
            }
        });

        Button addnewcustomer = mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setText("ADD");

        if (!orderAuditType.equalsIgnoreCase("primary") && orderFormDetailsObj.getAddCustomer().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("add_new_customer") && !Constants.userDetailsObj.getTourPlanDayWise().equalsIgnoreCase("yes") && !Constants.CurrentOrderCollectionTransactionType.matches("TO")) {
            addnewcustomer.setVisibility(View.VISIBLE);
        } else {
            addnewcustomer.setVisibility(View.GONE);
        }

        addnewcustomer.setOnClickListener(v -> {
            mDialogCustomer.cancel();
            if (!mRouteCode.isEmpty()) {
                Constants.isOrederToNewCustomer = true;
                isCheckInToNewCustomer = false;
                Intent intent = new Intent(ActivityOrderFilterAlternateDesign.this, AddNewCustActivity.class);
                intent.putExtra("ROUTENAME", mRouteName);
                intent.putExtra("ROUTECODE", mRouteCode);

                startActivity(intent);
            } else {
                Utils.showToast(mContext, "Please select the route");
            }
        });

        Button back = mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(v -> mDialogCustomer.cancel());

        mDialogCustomer.show();
    }

    @SuppressLint("SetTextI18n")
    public void custClassSelectionProcess() {
        String custClass = Constants.selectedCustomer.getCustClass().trim();
        if (custClass.matches("null") || custClass.matches("") || custClass.matches(" ")) {
            String classList = Constants.menuDetailsObj.getcust_class();
            ArrayList<String> custListToChooseFrom = new ArrayList<>();
            String[] Secondpart = classList.split(",");
            Collections.addAll(custListToChooseFrom, Secondpart);
            if (!custListToChooseFrom.isEmpty()) {
                LinearLayout LayoutTOAddradioButtonList;

                final Dialog grpDialog = new Dialog(mContext);
                grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                grpDialog.setContentView(R.layout.cust_class_selection_dialog);
                grpDialog.setCancelable(false);
                TextView title = grpDialog.findViewById(R.id.title);
                LayoutTOAddradioButtonList = grpDialog.findViewById(R.id.LayoutTOAddradioButtonList);
                title.setText("Select a class for " + Constants.selectedCustomer.getCustomerName());

                RadioGroup rgp = new RadioGroup(this);
                rgp.setOnCheckedChangeListener((group, checkedId) -> {
                    RadioButton radioSelection = grpDialog.findViewById(checkedId);
                    String chosenClass = radioSelection.getText().toString();
                    mAceDnsDatabase.updateCustomerClass(Constants.selectedCustomer.getCustomerCode(), chosenClass);
                    grpDialog.cancel();
                });
                RadioGroup.LayoutParams rprms;
                if (custListToChooseFrom.size() > 2) {
                    rgp.setOrientation(RadioGroup.VERTICAL);
                } else {
                    rgp.setOrientation(RadioGroup.HORIZONTAL);
                }
                for (int i = 0; i < custListToChooseFrom.size(); i++) {
                    RadioButton radioButton = new RadioButton(this);
                    radioButton.setText(custListToChooseFrom.get(i));
                    radioButton.setTag(i);
                    radioButton.setId(i);
                    radioButton.setTextColor(Color.BLUE);
                    rprms = new RadioGroup.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT);
                    rgp.addView(radioButton, rprms);
                }
                LayoutTOAddradioButtonList.addView(rgp);
                grpDialog.show();
            }

        }

    }

    @SuppressLint("SimpleDateFormat")
    private void ShowSelfAppraisalDetailsForCurrentCustomer() {
        try {
            if (Utils.currentCustomerWiseSelfAppraisalPresentInSetup(mContext)) {
                if (Constants.menuDetailsObj.getSelfAppraisalDetails().equalsIgnoreCase("yes") && mAceDnsDatabase.MenuAccess("self_appraisal")) {
                    customerWiseTrgtAchvmntLayout.setVisibility(View.VISIBLE);
                    SelfAppraisalDetailsCustomerWise TargetForCurrentMonth = mAceDnsDatabase.getTargetForCurrentMonth("self_appraisal_customer_wise", new SimpleDateFormat("MM").format(Calendar.getInstance().getTime()));
                    customerWiseTrgtAchvmntTvMonth.setText(new SimpleDateFormat("MMMM").format(Calendar.getInstance().getTime()));
                    customerWiseTrgtAchvmntTvTarget.setText(Constants.defaultFormat.format(Double.parseDouble(TargetForCurrentMonth.gettarget())));
                    customerWiseTrgtAchvmntTvAchievement.setText(Constants.defaultFormat.format(Double.parseDouble(TargetForCurrentMonth.getachievement())));
                }
            }
        } catch (Exception e) {
            customerWiseTrgtAchvmntLayout.setVisibility(View.GONE);
        }

    }

    @SuppressLint("SetTextI18n")
    public void ShowDestinationListDialog() {
        final Dialog mDestinationDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDestinationDialog.setContentView(R.layout.select_with_search);
        mDestinationDialog.setCancelable(false);

        TextView title = mDestinationDialog.findViewById(R.id.title);
        title.setText("Please select a destination");
        ListView dialogList = mDestinationDialog.findViewById(R.id.list);

        final DestinationAdapter destinationAdapter = new DestinationAdapter(this, R.layout.customer_broker_list_child, mDestinationMasterList);

        dialogList.setAdapter(destinationAdapter);

        EditText searchText = mDestinationDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                destinationAdapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            Constants.selectedDestination = destinationAdapter.getItem(position);
            assert Constants.selectedDestination != null;
            Constants.mDestinationCode = Constants.selectedDestination.getDestinationCode();
            mTextViewDestination.setText(Constants.selectedDestination.getDestinationName());
            mDestinationDialog.cancel();
        });

        Button cancel = mDestinationDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mDestinationDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowFreightTypeListDialog() {
        if (!orderFormDetailsObj.getFreightComponent().trim().isEmpty()) {
            if (orderFormDetailsObj.getFreightComponent().contains(",")) {
                final Dialog payTypeDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
                payTypeDialog.setCancelable(false);
                payTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                payTypeDialog.setContentView(R.layout.uom_dialog);
                TextView txtMsg = payTypeDialog.findViewById(R.id.title);
                txtMsg.setText("Freight:");

                RadioButton uom1RadioButton = payTypeDialog.findViewById(R.id.radio0);
                uom1RadioButton.setText("EX");

                RadioButton uom2RadioButton = payTypeDialog.findViewById(R.id.radio1);
                uom2RadioButton.setText("FOR");

                RadioGroup payTypeOption = payTypeDialog.findViewById(R.id.rg_pay_options);
                payTypeOption.setOnCheckedChangeListener((group, checkedId) -> {
                    int radioButtonID = group.getCheckedRadioButtonId();
                    View radioButton = group.findViewById(radioButtonID);
                    int selectedRadio = group.indexOfChild(radioButton);

                    if (selectedRadio == 0) {
                        Constants.mFreightComponent = "EX";
                        textViewFreightAmount.setVisibility(View.GONE);
                        mFreightComponentAmountFor = "";
                    } else {
                        Constants.mFreightComponent = "FOR";
                        showFreightAmount();
                    }
                    mTextViewFreight.setText(Constants.mFreightComponent);
                    payTypeDialog.cancel();

                });
                Button cancel = payTypeDialog.findViewById(R.id.btn_cancel);
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

    @SuppressLint("SetTextI18n")
    public void showFreightAmount() {
        final Dialog discountDialog = new Dialog(mContext);
        discountDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        discountDialog.setContentView(R.layout.user_instruction_dialog1);
        discountDialog.setCancelable(false);
        TextView title = discountDialog.findViewById(R.id.title);
        title.setText("Enter Freight Amount");
        final EditText edInst = discountDialog.findViewById(R.id.ed_input);
        edInst.setHint("00.00");
        edInst.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        Button submit = discountDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            String freightAmount = "";
            if (!edInst.getText().toString().isEmpty() && !edInst.getText().toString().equalsIgnoreCase(".")) {
                freightAmount = edInst.getText().toString();
                if (Utils.isNumeric(freightAmount)) {
                    mFreightComponentAmountFor = freightAmount;
                    textViewFreightAmount.setVisibility(View.VISIBLE);
                    textViewFreightAmount.setText("Amount: " + mFreightComponentAmountFor);
                    discountDialog.cancel();
                }
            }
        });
        discountDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowDealerChoseDialog(final ArrayList<CustomerDetails> mDealerNameList) {
        final Dialog mDestinationDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDestinationDialog.setContentView(R.layout.select_with_search);
        mDestinationDialog.setCancelable(false);

        TextView title = mDestinationDialog.findViewById(R.id.title);
        title.setText("Select dealer with whom order to be mapped");
        ListView dialogList = mDestinationDialog.findViewById(R.id.list);

        final CustomerAdapter adapter1 = new CustomerAdapter(mContext, R.layout.routeplan_access_dialog_child, mDealerNameList);
        dialogList.setAdapter(adapter1);

        EditText searchText = mDestinationDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter1.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDealerName = mDealerNameList.get(position).getCustomerName();
            taggedCustomerCode = mDealerNameList.get(position).getCustomerCode();
            if (mDealerName.matches("")) {
                mTextViewDealerName.setVisibility(View.GONE);
            } else {
                mTextViewDealerName.setText("Dealer : " + mDealerName);
            }
            mDestinationDialog.cancel();
        });

        Button cancel = mDestinationDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mDestinationDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowOrderTypeListDialog() {
        if (!orderFormDetailsObj.getOrderType().trim().isEmpty()) {
            if (orderFormDetailsObj.getOrderType().contains(",")) {
                String[] mOrderTypeList = orderFormDetailsObj.getOrderType().split(",");
                final Dialog mVerticalDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
                mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                mVerticalDialog.setContentView(R.layout.select_with_search);
                mVerticalDialog.setCancelable(false);

                TextView title = mVerticalDialog.findViewById(R.id.title);
                title.setText("Please select an order type");
                ListView dialogList = mVerticalDialog.findViewById(R.id.list);

                final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, R.layout.activity_masterview, mOrderTypeList);
                dialogList.setAdapter(adapter);

                EditText searchText = mVerticalDialog.findViewById(R.id.autoCompleteTextView1);
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

                dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                    getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                    Constants.mOrderType = adapter.getItem(position);
                    mTextViewOrderType.setText(Constants.mOrderType);
                    mVerticalDialog.cancel();
                });

                Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
                cancel.setVisibility(View.GONE);
                mVerticalDialog.show();

            } else {
                Constants.mOrderType = orderFormDetailsObj.getOrderType().trim();
                mTextViewOrderType.setText(Constants.mOrderType);
            }

        } else {
            Utils.showToast(mContext, "You have no order type.\n Please contact admin");
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowGstTypeListDialog() {
        String taxTypeString = orderFormDetailsObj.getTaxType();
        if (taxTypeString != null && !taxTypeString.trim().isEmpty()) {
            String[] taxTypesList = taxTypeString.split(",");

            final Dialog mVerticalDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
            mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mVerticalDialog.setContentView(R.layout.select_with_search);
            mVerticalDialog.setCancelable(false);

            TextView title = mVerticalDialog.findViewById(R.id.title);
            title.setText("Please select GST type");
            ListView dialogList = mVerticalDialog.findViewById(R.id.list);

            final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_listview, taxTypesList);
            dialogList.setAdapter(adapter);

            EditText searchText = mVerticalDialog.findViewById(R.id.autoCompleteTextView1);
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

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                Constants.mChosenGstType = adapter.getItem(position);
                textViewGst.setText(Constants.mChosenGstType);
                mVerticalDialog.cancel();
            });

            Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.GONE);
            mVerticalDialog.show();
        }

    }

    @SuppressLint("SetTextI18n")
    public void ShowVericalValueListDialog() {
        final Dialog mVerticalDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mVerticalDialog.setContentView(R.layout.select_with_search);
        mVerticalDialog.setCancelable(false);

        TextView title = mVerticalDialog.findViewById(R.id.title);
        title.setText("Please select a vertical");
        ListView dialogList = mVerticalDialog.findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, R.layout.activity_listview, mVerticalValueList);
        dialogList.setAdapter(adapter);

        EditText searchText = mVerticalDialog.findViewById(R.id.autoCompleteTextView1);
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

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            Constants.mVerticalValue = adapter.getItem(position);
            mTextViewVerticalValue.setText("Vertical : " + Constants.mVerticalValue);
            mVerticalDialog.cancel();
            if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                FlowofOrder(4);
            }
        });

        Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mVerticalDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowBranchorDepoNameDialog() {
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title = mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a Depot");
        ListView dialogList = mDialogDepotName.findViewById(R.id.list);

        BranchAdapter branchadapter = new BranchAdapter(mContext, R.layout.route_list_child, mBranchMasterDetailsList);
        dialogList.setAdapter(branchadapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            mDialogDepotName.cancel();
            Constants.selectedBranch = mBranchMasterDetailsList.get(arg2);
            mTextViewBranchValue.setText("Branch : " + Constants.selectedBranch.getBranchName());
        });

        Button cancel = mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        mDialogDepotName.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowBranchListForMrpDialog() {
        final ArrayList<BranchMasterDetails> branchListForMrp = mAceDnsDatabase.getBranchListOfCurrentEmp();
        final Dialog mDialogDepotName = new Dialog(mContext, R.style.PauseDialog);
        mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogDepotName.setContentView(R.layout.select_from_list);
        mDialogDepotName.setCancelable(false);

        TextView title = mDialogDepotName.findViewById(R.id.title);
        title.setText("Please select a Branch");
        ListView dialogList = mDialogDepotName.findViewById(R.id.list);

        BranchAdapter branchadapter = new BranchAdapter(mContext, R.layout.route_list_child, branchListForMrp);
        dialogList.setAdapter(branchadapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            mDialogDepotName.cancel();
            Constants.selectedBranchForMrp = branchListForMrp.get(arg2);
            if (mAceDnsDatabase.iscustomerMappedWithCurrentBranch(Constants.selectedCustomer.getCustomerCode(), Constants.selectedBranchForMrp.getBranchCode())) {
                moveToNextScreen();
            } else {
                Utils.showToast(mContext, "This customer is not mapped with selected branch\nPlease contact admin");
            }
        });

        Button cancel = mDialogDepotName.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        mDialogDepotName.show();
    }

    public void ChooseOrderType() {
        orderAuditType = "Secondary";
    }

    public Boolean CheckCustomerInformation() {
        if (orderFormDetailsObj.getCustomerInfoCheck().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
            return !Constants.selectedCustomer.getPin().trim().isEmpty() && !Constants.selectedCustomer.getNumber().trim().isEmpty() && !Constants.selectedCustomer.getAddress().trim().isEmpty();
        } else {
            return true;
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowEditCustomerDialog() {
        final Dialog customerEditDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        customerEditDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerEditDialog.setContentView(R.layout.add_customer_dialog);
        customerEditDialog.setCancelable(false);
        TextView title = customerEditDialog.findViewById(R.id.title);
        title.setText("Edit customer details");
        final EditText edName = customerEditDialog.findViewById(R.id.ed_name);
        edName.setText(Constants.selectedCustomer.getCustomerName());
        edName.setEnabled(false);

        final EditText edArea = customerEditDialog.findViewById(R.id.ed_area);
        edArea.setText(mSelectedRouteDetails.getRouteName());
        edArea.setEnabled(false);


        final EditText edAddress = customerEditDialog.findViewById(R.id.ed_address);
        edAddress.setText(Constants.selectedCustomer.getAddress().trim());

        final EditText edNumber = customerEditDialog.findViewById(R.id.ed_number);
        edNumber.setText(Constants.selectedCustomer.getNumber().trim());

        final EditText edPin = customerEditDialog.findViewById(R.id.ed_pin);
        edPin.setText(Constants.selectedCustomer.getPin().trim());

        LinearLayout tagLayout = customerEditDialog.findViewById(R.id.tag_layout);
        tagLayout.setVisibility(View.GONE);

        Button buttonSubmit = customerEditDialog.findViewById(R.id.btn_submit);
        Button buttonCancel = customerEditDialog.findViewById(R.id.btn_cancel);
        buttonSubmit.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            String number;
            String pin;
            String address;
            boolean bPin = false;
            boolean bNumber;
            boolean bAddress = false;
            number = edNumber.getText().toString();
            pin = edPin.getText().toString();
            address = edAddress.getText().toString();

            if (!address.isEmpty()) {
                bAddress = true;
            }
            if (pin.length() == 6) {
                bPin = true;
            }
            if (!number.isEmpty()) {
                if (number.length() == 10) {
                    bNumber = number.startsWith("9") || number.startsWith("8") ||
                            number.startsWith("7");
                } else {
                    Utils.showToast(ActivityOrderFilterAlternateDesign.this, "Please provide a 10 digit Phone Number");
                    bNumber = false;
                }
            } else {
                bNumber = false;
            }

            if (bPin && bNumber && bAddress) {
                customerEditDialog.cancel();
                mAceDnsDatabase.UpdateCustomerDetails(pin, number, address);
                if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes") && !orderFormDetailsObj.getInputScreenNormal().equalsIgnoreCase("yes")) {
                    FlowofOrder(3);
                } else {
                    if (Constants.productDetailsObj.getBranchWiseProduct().equalsIgnoreCase("yes") && !Constants.menuDetailsObj.getSaudaAllocation().equalsIgnoreCase("yes")) {
                        FlowofOrder(4);
                    }
                }
            } else {
                if (bPin && bAddress) {
                    Utils.showToast(ActivityOrderFilterAlternateDesign.this, "Invalid Phone Number");
                } else if (!bPin && bAddress && bNumber) {
                    Utils.showToast(ActivityOrderFilterAlternateDesign.this, "Invalid pin code");
                } else if (bPin && bNumber) {
                    Utils.showToast(ActivityOrderFilterAlternateDesign.this, "Plese provide address");
                } else {
                    Utils.showToast(ActivityOrderFilterAlternateDesign.this, "Please provide valid input");
                }
            }
        });
        buttonCancel.setOnClickListener(v -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            customerEditDialog.cancel();
        });

        customerEditDialog.show();
    }

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void ShowNoOrderDialog() {
        final Dialog noOrderDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.user_instruction_dialog);
        noOrderDialog.setCancelable(false);
        TextView title = noOrderDialog.findViewById(R.id.title);
        title.setText("Please state the reason for no order");
        final EditText edReason = noOrderDialog.findViewById(R.id.ed_input);
        final Button submit = noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            noOrderDialog.cancel();
            boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
            if (isTimeAutomatic) {
                new GPSTracker(mContext);
                submit.setEnabled(false);
                String reason = "";
                reason = edReason.getText().toString();
                String timeStamp = Constants.dateString + new SimpleDateFormat("_HHmmss").format(Calendar.getInstance().getTime());
                timeStamp = timeStamp.replace("_", "");
                MakeLocalAndServerDataSavingProcess(reason, timeStamp, true);
            } else {
                Utils.showSettingsAlertToChangeTimeZone(mContext);
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
                new TRANS_SubmitNewCustomerDetailsTask(ActivityOrderFilterAlternateDesign.this, true, true, "SYNC").execute();
            } else {
                if (isExist) {
                    new TRANS_PendingRoutePlanBeforeOtherTxn(ActivityOrderFilterAlternateDesign.this, "ORDER").execute();
                } else {
                    new TRANS_SubmitOrderTask(ActivityOrderFilterAlternateDesign.this, true).execute();
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

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    public void showInstructionWithHintDialog() {
        final Dialog noOrderDialog = new Dialog(ActivityOrderFilterAlternateDesign.this, R.style.PauseDialog);
        noOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        noOrderDialog.setContentView(R.layout.hint_remarks_dialog);
        noOrderDialog.setCancelable(false);
        TextView title = noOrderDialog.findViewById(R.id.title);
        TextView title2 = noOrderDialog.findViewById(R.id.title2);
        title.setText("Please state the reason for no order");
        title2.setText("Any specific Requirement?");
        final EditText edReason = noOrderDialog.findViewById(R.id.ed_input);
        final Button submit = noOrderDialog.findViewById(R.id.btn);
        submit.setOnClickListener(v -> {
            noOrderDialog.cancel();
            boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
            if (isTimeAutomatic) {
                new GPSTracker(mContext);
                submit.setEnabled(false);
                String reason = "";
                reason = edReason.getText().toString();
                String timeStamp = Constants.dateString + new SimpleDateFormat("_HHmmss").format(Calendar.getInstance().getTime());
                timeStamp = timeStamp.replace("_", "");
                Boolean isSuccessHintRemarksTableInsertion = true;
                if (selectedHintRemarksId != -1) {
                    isSuccessHintRemarksTableInsertion = mAceDnsTransactionDatabase.insertToHintRemarksDetailsTable1("NO", timeStamp, hintRemarksValList.get(selectedHintRemarksId - 1));
                }
                MakeLocalAndServerDataSavingProcess(reason, timeStamp, isSuccessHintRemarksTableInsertion);
            } else {
                Utils.showSettingsAlertToChangeTimeZone(mContext);
            }
        });
        RadioGroup rgp = noOrderDialog.findViewById(R.id.radiogroup);
        rgp.setOnCheckedChangeListener((radioGroup, id) -> selectedHintRemarksId = id);

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

    @SuppressLint("SimpleDateFormat")
    public void saveSwapDataToDatabase() {
        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

        mAceDnsTransactionDatabase.INSERTtoSwapTable(Constants.dayOfWeekForCustomer, swappedDay, todaysDate, swappedDate, timeStamp, "TS");
        mAceDnsTransactionDatabase.insertToLocationTable("TS", timeStamp);
        Constants.dayOfWeekForCustomer = swappedDay;
    }

}
