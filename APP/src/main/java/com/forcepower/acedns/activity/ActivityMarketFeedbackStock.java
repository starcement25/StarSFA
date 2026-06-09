package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Typeface;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.util.Log;
import android.util.TypedValue;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.view.inputmethod.InputMethodManager;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CompetitorColorAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.bean.MarketFeedbackStockAudit;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.List;
import java.util.Objects;

import static com.forcepower.acedns.R.id.autoCompleteTextView1;
import static com.forcepower.acedns.constants.Constants.orderAuditType;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInRoute;

import androidx.annotation.NonNull;

public class ActivityMarketFeedbackStock extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCompetitorName = null;

    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCustomerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewDealerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewRouteName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewUOM = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewQty = null;

    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCol1 = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCol2 = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCol3 = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCol4 = null;

    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonNoMarketFeedback = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonCompetitorName = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonCompetitorNameType = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonAddtoCart = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonCheckOut = null;

    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextQuantity = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextDiscount = null;

    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutChild = null;
    @SuppressLint("StaticFieldLeak")
    private static int mColumnCount = 0;

    ProgressDialog mStepProgressDialog;
    Handler mStepHandler;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    ArrayList<RouteDetails> mRouteDetailsList;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    MarketFeedback mMarketFeedback;
    ArrayList<String> mCompetitorNameList;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;

    RouteDetails mSelectedRouteDetails;
    RoutePlanMasterDetails mSelectedTodayRouteDetails;
    MarketFeedbackStockAudit mMarketFeedbackStockAudit;
    private List<EditText> mEditTextList;
    private boolean isRouteOk = true;
    private boolean isCustomerOk = true;
    private String mRouteName = "";
    private String mDealerName = "";
    private String mRdsCode = "";
    private String mCompetitorName = "";
    private String mSelectedCompetitor = "";
    private String mSelectedProductType = "";
    String mButtonCompetitorNameTypeString = "Competitor";
    private String mUom = "";
    private String mExFor = "";
    int pp = 0;

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_market_feedback_stock);
        RegisterActivities.registerActivity(this);

        mContext = ActivityMarketFeedbackStock.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mNewDatabaseForSiteLead=new NewDatabaseForSiteLead(mContext);

        Constants.selectedFeedBackList = new ArrayList<>();

        Constants.selectedCustomer = null;
        mSelectedRouteDetails = null;

        Constants.mMarketFeedbackStockAuditList = new ArrayList<>();

        RegisterActivities.registerActivity(this);
        InitializeView();
        ClearText();

        mButtonBack.setOnClickListener(v -> finish());

        mButtonCompetitorName.setOnClickListener(v -> {
            if (isRouteOk && isCustomerOk) {
                FlowofOrder(3);
            } else {
                if (!isRouteOk) {
                    Utils.showToast(mContext, "Error in route data. Please Synchronize Data");
                } else {
                    Utils.showToast(mContext, "Error in customer data. Please Synchronize Data");
                }
            }
        });

        mButtonCompetitorNameType.setOnClickListener(v -> {
            if (isRouteOk && isCustomerOk) {
                mCompetitorName = "Competitor,OWN";
                ParseCompetitorNameList(mCompetitorName);
                if (!mCompetitorNameList.isEmpty()) {
                    ShowCompetitorTypeList();
                } else {
                    Utils.showToast(mContext, "No competitor left. Please check out & submit data");
                }
            } else {
                if (!isRouteOk) {
                    Utils.showToast(mContext, "Error in route data. Please Synchronize Data");
                } else {
                    Utils.showToast(mContext, "Error in customer data. Please Synchronize Data");
                }
            }
        });

        mButtonAddtoCart.setOnClickListener(v -> {
            String inpqty;

            if (!mSelectedCompetitor.isEmpty() && mEditTextList != null && !mEditTextList.isEmpty()) {
                inpqty = mEditTextQuantity.getText().toString();
                mMarketFeedbackStockAudit = new MarketFeedbackStockAudit();
                mMarketFeedbackStockAudit.setCompetitorName(mSelectedCompetitor);
                mMarketFeedbackStockAudit.setQuantity(inpqty);
                mMarketFeedbackStockAudit.setDiscount(mEditTextDiscount.getText().toString());
                mMarketFeedbackStockAudit.setProductType(mSelectedProductType);
                mMarketFeedback.setmProductType(mSelectedProductType);
                int count;
                for (count = 0; count < mEditTextList.size(); count++) {
                    String value = mEditTextList.get(count).getText().toString().trim();
                    if (count == 0) {
                        try {
                            if (Integer.parseInt(value) < 250 || Integer.parseInt(value) > 700) {
                                Utils.showToast(mContext, "Billing/Bag value must be with in 250 to 700");
                                return;
                            } else {
                                mMarketFeedback.setPtd(value);
                            }
                        } catch (Exception e) {
                            Utils.showToast(mContext, "Billing/Bag value must be with in 250 to 700");
                            return;
                        }
                    }
                    if (count == 1) {
                        try {
                            if (Integer.parseInt(value) < 250 || Integer.parseInt(value) > 700) {
                                Utils.showToast(mContext, "WSP/Bag value must be with in 250 to 700");
                                return;
                            } else {
                                mMarketFeedback.setPtr(value);
                            }
                        } catch (Exception e) {
                            Utils.showToast(mContext, "WSP/Bag value must be with in 250 to 700");
                            return;
                        }
                    }
                    if (count == 2) {
                        try {
                            if (Integer.parseInt(value) < 250 || Integer.parseInt(value) > 700) {
                                Utils.showToast(mContext, "RSP value must be with in 250 to 700");
                                return;
                            } else {
                                mMarketFeedback.setPtc(value);
                            }
                        } catch (Exception e) {
                            Utils.showToast(mContext, "RSP value must be with in 250 to 700");
                            return;
                        }
                    }
                    if (count == 3) {
                        mMarketFeedback.setPv(value);
                    }
                }

                if(mEditTextQuantity.getText().toString().trim().isEmpty()){
                    Utils.showToast(mContext, "You have to enter the quantity");
                    return;
                }

                if (Constants.menuDetailsObj.getMf_mandatory_details().toLowerCase().contains("yes")) {
                    if (!mMarketFeedback.getPtc().isEmpty() && !mMarketFeedback.getPtd().isEmpty() && !mMarketFeedback.getPtr().isEmpty()) {
                        if (Integer.parseInt(mMarketFeedback.getPtc()) >= 0 && Integer.parseInt(mMarketFeedback.getPtd()) >= 0 && Integer.parseInt(mMarketFeedback.getPtr()) >= 0) {
                            int checker = 0;
                            for (int i = 0; i < Constants.mMarketFeedbackStockAuditList.size(); i++) {
                                if (Constants.mMarketFeedbackStockAuditList.get(i).getCompetitorName().equalsIgnoreCase(mSelectedCompetitor)) {
                                    checker = 1;
                                }
                            }
                            if (checker == 1) {
                                Utils.showToast(mContext, "You already added " + mSelectedCompetitor + " .");
                            } else {
                                Constants.mMarketFeedbackStockAuditList.add(mMarketFeedbackStockAudit);
                                Constants.selectedFeedBackList.add(mMarketFeedback);
                                ReFreshData();
                            }
                        } else {
                            Utils.showToast(mContext, "All Value Should be Greater than 0 Or 0");
                        }
                    } else {
                        Utils.showToast(mContext, "Please Add All Value");
                    }
                } else {
                    Constants.mMarketFeedbackStockAuditList.add(mMarketFeedbackStockAudit);
                    Constants.selectedFeedBackList.add(mMarketFeedback);
                    ReFreshData();
                }

                int a=mNewDatabaseForSiteLead.updateCustomerCompetitorQuantity(Constants.selectedCustomer.getCustomerCode(),mSelectedCompetitor,mEditTextQuantity.getText().toString().trim());
            } else {
                Utils.showToast(mContext, "Please select competitor");
            }
        });

        mButtonCheckOut.setOnClickListener(v -> {
            if (Constants.mMarketFeedbackStockAuditList != null && !Constants.mMarketFeedbackStockAuditList.isEmpty()) {
                if (Constants.nickName.equalsIgnoreCase("STAR")) {
                    Boolean isDataOk = checkCompetitorOkOrNot();
                    if (isDataOk) {
                        Intent intent = new Intent(ActivityMarketFeedbackStock.this, ActivityMarketFeedbackStockConfirmation.class);
                        startActivity(intent);
                    } else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("Exclusive Dealer")) {
                        Utils.showToast(mContext, "Add minimum One MANDATORY STAR");
                    } else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("NON STAR")) {
                        Utils.showToast(mContext, "Add minimum One BENCHMARK COMPETITOR/OTHER COMPETITOR");
                    } else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SUB DEALER")) {
                        Utils.showToast(mContext, "Add minimum MANDATORY STAR & BENCHMARK COMPETITOR/OTHER COMPETITOR");
                    } else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("DEALER")) {
                        Utils.showToast(mContext, "Add minimum One MANDATORY STAR & BENCHMARK COMPETITOR/OTHER COMPETITOR");
                    }
                } else {
                    Intent intent = new Intent(ActivityMarketFeedbackStock.this, ActivityMarketFeedbackStockConfirmation.class);
                    startActivity(intent);
                }
            } else {
                Utils.showToast(mContext, "Please add to cart");
            }
        });

        mStepHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mStepProgressDialog.dismiss();
                final int step = msg.getData().getInt("STEP");
                ActivityMarketFeedbackStock.this.runOnUiThread(() -> {
                    switch (step) {
                        case 1:
                            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                                if (NotCheckedOut(mContext)) {
                                    isRouteOk = true;
                                    mSelectedTodayRouteDetails = mRoutePlanListofToday.get(getPositionOfCurrentCheckedInRoute(true, mContext, mRoutePlanListofToday, null));
                                    mRouteName = mSelectedTodayRouteDetails.getRouteName();
                                    mTextViewRouteName.setText("Route : " + mSelectedTodayRouteDetails.getRouteName());
                                    FlowofOrder(2);
                                } else if (mRoutePlanListofToday.size() == 1) {
                                    isRouteOk = true;
                                    mSelectedTodayRouteDetails = mRoutePlanListofToday.get(0);
                                    mRouteName = mSelectedTodayRouteDetails.getRouteName();
                                    mTextViewRouteName.setText("Route : " + mSelectedTodayRouteDetails.getRouteName());
                                    FlowofOrder(2);
                                } else if (mRoutePlanListofToday.size() > 1) {
                                    ShowTodayRoutePlanListDialog(mRoutePlanListofToday);
                                } else {
                                    Toast.makeText(mContext, "No route found.\n Please Synchronize Data", Toast.LENGTH_SHORT).show();
                                    isRouteOk = false;
                                }
                            } else {
                                if (NotCheckedOut(mContext)) {
                                    isRouteOk = true;
                                    mSelectedRouteDetails = mRouteDetailsList.get(getPositionOfCurrentCheckedInRoute(false, mContext, null, mRouteDetailsList));
                                    mRouteName = mSelectedRouteDetails.getRouteName();
                                    mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
                                    FlowofOrder(2);
                                } else if (mRouteDetailsList.size() > 1) {
                                    ShowRouteListDialog(mRouteDetailsList);
                                } else if (mRouteDetailsList.size() == 1) {
                                    isRouteOk = true;
                                    mSelectedRouteDetails = mRouteDetailsList.get(0);
                                    mRouteName = mSelectedRouteDetails.getRouteName();
                                    mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
                                    FlowofOrder(2);
                                } else {
                                    Toast.makeText(mContext, "No route found.\n Please Synchronize Data", Toast.LENGTH_SHORT).show();
                                    isRouteOk = false;
                                }
                            }
                            break;
                        case 2:
                            if (NotCheckedOut(mContext)) {
                                Constants.selectedCustomer = new CustomerDetails();
                                for (int i = 0; i < mCustomerDetailsList.size(); i++) {
                                    CustomerDetails currentCustomer = mCustomerDetailsList.get(i);
                                    if (currentCustomer.getCustomerCode().equalsIgnoreCase(PreferenceData.getCheckInOutEmpCode(mContext))) {
                                        Constants.selectedCustomer = currentCustomer;
                                        isCustomerOk = true;
                                        if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("Sub Dealer")) {
                                            mRdsCode = Constants.selectedCustomer.getRdsTag().trim();
                                            DealerName(mRdsCode);
                                        } else {
                                            mTextViewDealerName.setText("Dealer : ");
                                        }
                                        Constants.selectedCustomer.setRouteName(mRouteName);
                                        break;
                                    }
                                }
                                Constants.selectedCustomer.setCustomerCode(PreferenceData.getCheckInOutEmpCode(mContext));
                                Constants.selectedCustomer.setCustomerName(PreferenceData.getCheckInOutEmpName(mContext));
                                Constants.selectedCustomer.setCustomerType(PreferenceData.getCheckInOutEmpType(mContext));
                                Log.d("TAG", "mTextViewCustomerName : 3");
                                mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
                            } else {
                                if (mCustomerDetailsList.size() > 1) {
                                    ShowCustomerListDialog();
                                } else if (mCustomerDetailsList.size() == 1) {
                                    isCustomerOk = true;
                                    Constants.selectedCustomer = mCustomerDetailsList.get(0);
                                    if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("Sub Dealer")) {
                                        mRdsCode = Constants.selectedCustomer.getRdsTag().trim();
                                        DealerName(mRdsCode);
                                    } else {
                                        mTextViewDealerName.setText("Dealer : ");
                                    }
                                    Constants.selectedCustomer.setRouteName(mRouteName);
                                    Log.d("TAG", "mTextViewCustomerName : 4");
                                    mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
                                } else {
                                    Toast.makeText(mContext, "No customer found.\n Please Synchronize Data", Toast.LENGTH_SHORT).show();
                                    isCustomerOk = false;
                                }
                            }
                            break;
                        case 3:
                            if (!mCompetitorName.trim().isEmpty()) {
                                ParseCompetitorNameList(mCompetitorName);
                                if (!mCompetitorNameList.isEmpty()) {
                                    ShowCompetitorNameList();
                                } else {
                                    Utils.showToast(mContext, "No competitor left. Please check out & submit data");
                                }
                            } else {
                                Utils.showToast(mContext, "No competitor found. Please Synchronize Data");
                            }

                            break;
                        case 4:
                            mTextViewUOM.setText(mUom);
                            ParseCompanyList(Constants.selectedCustomer.getCustomerCode(), Constants.selectedCustomer.getRouteCode(), mSelectedCompetitor);
                            DrawView();
                            break;
                        case 5:
                            break;
                        case 6:
                            Log.d("TAG", "_DOWNLOAD_ Dealer name and Code:  "+mDealerName+" || "+Constants.selectedCustomer.getCustomerCode());
                            mTextViewDealerName.setText("Dealer : " + mDealerName);
                            break;
                    }
                });
            }
        };
        FlowofOrder(1);
    }

    @SuppressLint({"SetTextI18n"})
    public void ChooseOrderType() {
        if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary") || Constants.isVanSales) {
            orderAuditType = "Secondary";
            FlowofOrder(1);
        } else {
            final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
            dialgoCondition.setCancelable(false);
            dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
            Objects.requireNonNull(dialgoCondition.getWindow()).setBackgroundDrawable(new ColorDrawable(android.graphics.Color.TRANSPARENT));

            TextView txtMsg = dialgoCondition.findViewById(R.id.title);
            txtMsg.setText("Select Transaction Type.");

            final RadioGroup radioSelectionGroup = dialgoCondition.findViewById(R.id.radioSelect);
            final RadioButton radioEdit = dialgoCondition.findViewById(R.id.radioEdit);
            final RadioButton radioRedundant = dialgoCondition.findViewById(R.id.radioRedundant);
            radioEdit.setText("Primary");
            radioRedundant.setText("Secondary");

            radioSelectionGroup.setOnCheckedChangeListener((group, checkedId) -> {
                RadioButton radioSelection = dialgoCondition.findViewById(checkedId);
                if (radioSelection.getText().toString().trim().equalsIgnoreCase("Primary")) {
                    orderAuditType = "Primary";
                } else {
                    orderAuditType = "Secondary";
                }
                dialgoCondition.cancel();
                FlowofOrder(1);
            });
            dialgoCondition.show();
        }

    }

    public void ReFreshData() {
        mEditTextDiscount.setText("");
        mEditTextQuantity.setText("");
        mTextViewCompetitorName.setText("");
        mLinearLayoutChild.removeAllViews();
        mSelectedCompetitor = "";
        mSelectedProductType = "";
    }

    public void ParseCompetitorNameList(String name) {
        String[] RowData = name.split(",");
        mCompetitorNameList = new ArrayList<>();
        String competitorname = "";
        for (String rowDatum : RowData) {
            competitorname = rowDatum.trim();
            if (Constants.mMarketFeedbackStockAuditList != null && !Constants.mMarketFeedbackStockAuditList.isEmpty()) {
                if (!RemoveRepeatedCompetotorName(competitorname)) {
                    mCompetitorNameList.add(competitorname);
                }
            } else {
                mCompetitorNameList.add(competitorname);
            }
        }
    }

    public void ParseCompanyList(String customercode, String routecode, String name) {
        mMarketFeedback = new MarketFeedback();
        mMarketFeedback.setProductGroup("");
        mMarketFeedback.setRouteCode(routecode);
        mMarketFeedback.setCopmpetitorName(name);
        mMarketFeedback.setCustomerCode(customercode);
    }

    public boolean RemoveRepeatedCompetotorName(String competiorname) {
        boolean isok = false;
        for (int count = 0; count < Constants.mMarketFeedbackStockAuditList.size(); count++) {
            if (competiorname.equalsIgnoreCase(Constants.mMarketFeedbackStockAuditList.get(count).getCompetitorName())) {
                isok = true;
                break;
            }
        }
        return isok;
    }

    @SuppressLint({"SetTextI18n"})
    public void ShowCompetitorNameList() {
        final Dialog mVerticalDialog = new Dialog(ActivityMarketFeedbackStock.this, R.style.PauseDialog);
        mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mVerticalDialog.setContentView(R.layout.select_with_search);
        mVerticalDialog.setCancelable(false);

        TextView title = mVerticalDialog.findViewById(R.id.title);
        title.setText("Please select a competitor");
        ListView dialogList = mVerticalDialog.findViewById(R.id.list);

        Collections.sort(Constants.competitorPoductType, new Comparator<MarketFeedbackStockAudit>() {
            @Override
            public int compare(MarketFeedbackStockAudit o1, MarketFeedbackStockAudit o2) {
                String p1 = o1.getProductType() != null ? o1.getProductType() : "";
                String p2 = o2.getProductType() != null ? o2.getProductType() : "";

                // Custom priority order
                int priority1 = getPriority(p1);
                int priority2 = getPriority(p2);

                // First compare by priority
                if (priority1 != priority2) {
                    return Integer.compare(priority1, priority2);
                }

                // If same priority, sort alphabetically by competitorName
                String c1 = o1.getCompetitorName() != null ? o1.getCompetitorName() : "";
                String c2 = o2.getCompetitorName() != null ? o2.getCompetitorName() : "";
                return c1.compareToIgnoreCase(c2);
            }

            private int getPriority(String productType) {
                if (productType.equalsIgnoreCase("MANDATORY STAR")) return 1;
                if (productType.equalsIgnoreCase("BENCHMARK COMPETITOR")) return 2;
                return 3; // Others
            }
        });
        for (MarketFeedbackStockAudit item : Constants.competitorPoductType) {
            Log.d("AFTER_SORT", "_DOWNLOAD_ Name: " + item.getCompetitorName() + " | Type: " + item.getProductType());
        }

        final CompetitorColorAdapter adapter = new CompetitorColorAdapter(this, R.layout.activity_masterview, Constants.competitorPoductType);
        dialogList.setAdapter(adapter);

        EditText searchText = mVerticalDialog.findViewById(autoCompleteTextView1);
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
            mSelectedCompetitor = Objects.requireNonNull(adapter.getItem(position)).getCompetitorName().toUpperCase();
            mTextViewCompetitorName.setText(mSelectedCompetitor);
            mSelectedProductType = Objects.requireNonNull(adapter.getItem(position)).getProductType().toUpperCase();

            try{
                String qty=mNewDatabaseForSiteLead.getQuantityAgainstCustomerAndCompetitor(Constants.selectedCustomer.getCustomerCode(),mSelectedCompetitor);
                mEditTextQuantity.setText(qty);
            }catch (Exception ignored){}

            FlowofOrder(4);
            mVerticalDialog.cancel();
        });

        Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mVerticalDialog.show();
    }

    @SuppressLint({"SetTextI18n"})
    public void ShowCompetitorTypeList() {
        final Dialog mVerticalDialog = new Dialog(ActivityMarketFeedbackStock.this, R.style.PauseDialog);
        mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mVerticalDialog.setContentView(R.layout.select_with_search);
        mVerticalDialog.setCancelable(false);

        TextView title = mVerticalDialog.findViewById(R.id.title);
        title.setText("Please select a Option");
        ListView dialogList = mVerticalDialog.findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_masterview, mCompetitorNameList);
        dialogList.setAdapter(adapter);
        EditText searchText = mVerticalDialog.findViewById(autoCompleteTextView1);
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
            mButtonCompetitorNameTypeString = Objects.requireNonNull(adapter.getItem(position)).toUpperCase();
            FlowofOrder(3);
            mVerticalDialog.cancel();
        });

        Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mVerticalDialog.show();
    }

    @SuppressLint({"SetTextI18n"})
    public void DealerName(String rdscode) {
        if (!rdscode.trim().isEmpty()) {
            FlowofOrder(6);
        } else {
            mTextViewDealerName.setText("Dealer : ");
        }
    }

    @SuppressLint({"SetTextI18n"})
    public void InitializeView() {
        mTextViewRouteName = findViewById(R.id.textViewDepoDetails);
        mTextViewCustomerName = findViewById(R.id.textViewCustomername);
        mTextViewDealerName = findViewById(R.id.textViewDealerName);
        mTextViewCompetitorName = findViewById(R.id.textViewCompetitorName);
        mTextViewUOM = findViewById(R.id.textViewUOM);
        mTextViewQty = findViewById(R.id.textView1);

        mButtonNoMarketFeedback = findViewById(R.id.no_ordr);
        mButtonBack = findViewById(R.id.back);
        mButtonAddtoCart = findViewById(R.id.buttonAddtoCart);
        mButtonCheckOut = findViewById(R.id.buttonCheckOut);
        mButtonCompetitorName = findViewById(R.id.buttonCompetitorName);
        mButtonCompetitorNameType = findViewById(R.id.buttonCompetitorNameType);

        mEditTextQuantity = findViewById(R.id.editTextQuantity);
        mEditTextDiscount = findViewById(R.id.editTextDiscount);

        mImageViewHeaderLogo = findViewById(R.id.imagelogo);

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivityMarketFeedbackStock.this) + "~" + Utils.getDBVersion(ActivityMarketFeedbackStock.this));

        mButtonNoMarketFeedback.setVisibility(View.GONE);

        mTextViewCol1 = findViewById(R.id.txt_col1);
        mTextViewCol2 = findViewById(R.id.txt_col2);
        mTextViewCol3 = findViewById(R.id.txt_col3);
        mTextViewCol4 = findViewById(R.id.txt_col4);

        mLinearLayoutChild = findViewById(R.id.linearLayoutChild);

        mColumnCount = 0;
        if (!Constants.marketFeedbackDetailsObj.getMfCol1().trim().isEmpty()) {
            mTextViewCol1.setText(Constants.marketFeedbackDetailsObj.getMfCol1());
            mColumnCount += 1;
        } else {
            mTextViewCol1.setVisibility(View.GONE);
        }

        if (!Constants.marketFeedbackDetailsObj.getMfCol2().trim().isEmpty()) {
            mTextViewCol2.setText(Constants.marketFeedbackDetailsObj.getMfCol2());
            mColumnCount += 1;
        } else {
            mTextViewCol2.setVisibility(View.GONE);
        }

        if (!Constants.marketFeedbackDetailsObj.getMfCol3().trim().isEmpty()) {
            mTextViewCol3.setText(Constants.marketFeedbackDetailsObj.getMfCol3());
            mColumnCount += 1;
        } else {
            mTextViewCol3.setVisibility(View.GONE);
        }

        if (!Constants.marketFeedbackDetailsObj.getMfCol4().trim().isEmpty()) {
            mTextViewCol4.setText(Constants.marketFeedbackDetailsObj.getMfCol4());
            mColumnCount += 1;
        } else {
            mTextViewCol4.setVisibility(View.GONE);
        }

        if (Constants.marketFeedbackDetailsObj.getMf_sub_menu_qty_unit().contains("MT") || Constants.marketFeedbackDetailsObj.getMf_sub_menu_qty_unit().contains("mt")) {
            mTextViewQty.setText("Quantity");
        }
        if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {
            mTextViewQty.setText("Monthly Sale Qty (in Bags)");
            mButtonCompetitorNameType.setVisibility(View.VISIBLE);
            mButtonCompetitorName.setText("Product");
        }
    }

    public void ClearText() {
        mTextViewRouteName.setText("");
        Log.d("TAG", "mTextViewCustomerName : 1");
        mTextViewCustomerName.setText("");
        mTextViewCompetitorName.setText("");
        mTextViewDealerName.setText("");
        mTextViewUOM.setText("");
    }

    @SuppressLint("ClickableViewAccessibility")
    public void DrawView() {
        mEditTextList = new ArrayList<>();
        mLinearLayoutChild.removeAllViews();
        for (int count = 0; count < mColumnCount; count++) {
            mLinearLayoutChild.addView(AddEditText(String.valueOf(count)));
        }
        if (Constants.marketFeedbackDetailsObj.getEx_for().toLowerCase().contains("yes")) {
            if (mColumnCount == 3) {
                FrameLayout pv = findViewById(R.id.pv_lst_row);
                pv.setVisibility(View.GONE);
            }

            mEditTextList.get(1).setOnTouchListener((v, event) -> {
                if (pp == 0) {
                    pp++;
                    ShowExForList("wsp");
                }
                return false;
            });
            mEditTextList.get(0).setOnTouchListener((v, event) -> {
                if (pp == 0) {
                    pp++;
                    ShowExForList("billing");
                }
                return false;
            });

            if (Constants.nickName.equalsIgnoreCase("SHAKTI")) {
                mEditTextList.get(2).setOnTouchListener((v, event) -> {
                    if (pp == 0) {
                        pp++;
                        ShowExForList("rsp");
                    }
                    return false;
                });
                mEditTextList.get(3).setOnTouchListener((v, event) -> {
                    if (pp == 0) {
                        pp++;
                        ShowExForList("nod");
                    }
                    return false;
                });
            }
        }
    }

    private EditText AddEditText(String tag) {
        EditText editText = new EditText(this);
        editText.setTag(tag);
        LinearLayout.LayoutParams qtparam = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
        editText.setLayoutParams(qtparam);
        editText.setInputType(InputType.TYPE_CLASS_NUMBER);
        editText.setSingleLine(false);
        editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        editText.setTextColor(Color.GRAY);
        editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
        editText.setTypeface(null, Typeface.NORMAL);
        editText.setHintTextColor(Color.LTGRAY);
        mEditTextList.add(editText);
        return editText;
    }

    @SuppressLint({"SetTextI18n"})
    public void ShowExForList(String exfor) {
        final Dialog mVerticalDialog = new Dialog(ActivityMarketFeedbackStock.this, R.style.PauseDialog);
        mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mVerticalDialog.setContentView(R.layout.select_with_search);
        mVerticalDialog.setCancelable(false);

        TextView title = mVerticalDialog.findViewById(R.id.title);
        title.setText("Please select a Option");
        ListView dialogList = mVerticalDialog.findViewById(R.id.list);
        ArrayList<String> mExForList = new ArrayList<>();
        mExForList.add("EX");
        mExForList.add("FOR");

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_masterview, mExForList);
        dialogList.setAdapter(adapter);

        EditText searchText = mVerticalDialog.findViewById(autoCompleteTextView1);
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
        searchText.setVisibility(View.GONE);
        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_VISIBLE);
            mExFor = adapter.getItem(position);
            if (exfor.toLowerCase().contains("billing")) {
                mMarketFeedback.setmBillingExFor(mExFor);
                mTextViewCol1.setText("Billing(" + mExFor + ")/Bag");
                mEditTextList.get(0).setFocusable(true);
                mEditTextList.get(0).requestFocus();
                InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                imm.showSoftInput(mEditTextList.get(0), InputMethodManager.SHOW_IMPLICIT);
            }
            if (exfor.toLowerCase().contains("wsp")) {
                mMarketFeedback.setmWspExFor(mExFor);
                mTextViewCol2.setText("WSP(" + mExFor + ")/Bag");
                mEditTextList.get(1).setFocusable(true);
                mEditTextList.get(1).requestFocus();
                InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                imm.showSoftInput(mEditTextList.get(1), InputMethodManager.SHOW_IMPLICIT);
            }
            if (exfor.toLowerCase().contains("rsp")) {
                mMarketFeedback.setmRspExFor(mExFor);
                mTextViewCol3.setText("RSP(" + mExFor + ")/Bag");
                mEditTextList.get(2).setFocusable(true);
                mEditTextList.get(2).requestFocus();
                InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                imm.showSoftInput(mEditTextList.get(2), InputMethodManager.SHOW_IMPLICIT);
            }
            if (exfor.toLowerCase().contains("nod")) {
                mMarketFeedback.setmNodExFor(mExFor);
                mTextViewCol4.setText("NOD(" + mExFor + ")/Bag");
                mEditTextList.get(3).setFocusable(true);
                mEditTextList.get(3).requestFocus();
                InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                imm.showSoftInput(mEditTextList.get(3), InputMethodManager.SHOW_IMPLICIT);
            }

            mVerticalDialog.cancel();
            pp = 0;
        });

        Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mVerticalDialog.show();
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

    public void FlowofOrder(final int step) {
        mStepProgressDialog = new ProgressDialog(mContext);
        mStepProgressDialog.setMessage("Preparing Data.\nPlease wait..");
        mStepProgressDialog.setCancelable(false);
        mStepProgressDialog.show();
        new Thread() {
            public void run() {
                switch (step) {
                    case 1:
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                            String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
                            mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForToday(today);
                        } else {
                            mRouteDetailsList = mAceDnsDatabase.getRouteList();
                        }
                        break;
                    case 2:
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                            RoutePlanDetails routeplandetails = mAceDnsDatabase.getRoutePlanDetailsObj();
                            if (routeplandetails.getRouteCustomerPlanning().equalsIgnoreCase("yes")) {
                                String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
                                mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(today, mSelectedTodayRouteDetails.getRoutecode());
                            } else {
                                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedTodayRouteDetails.getRoutecode());
                            }
                        } else {
                            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRouteDetails.getRouteCode());
                        }
                        break;
                    case 3:
                        Log.d("TAG", "_DOWNLOAD_ : "+mButtonCompetitorNameTypeString);
                        if (mButtonCompetitorNameTypeString.equalsIgnoreCase("OWN")) {
                            mCompetitorName = mAceDnsDatabase.GetCompetitorOwnProductName();
                        } else {
                            mCompetitorName = mAceDnsDatabase.GetCompetitorName();
                        }
                        break;
                    case 4:
                        mUom = mAceDnsDatabase.GetUOM(mSelectedCompetitor);
                        break;
                    case 5:
                        break;
                    case 6:
                        mDealerName = mAceDnsDatabase.getDealerName(mRdsCode);
                        break;
                    case 7:
                        mCompetitorName = mAceDnsDatabase.GetCompetitorOwnProductName();
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

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {
        final ArrayList<RouteDetails> routeListSearchingArray = new ArrayList<>(routeList);
        final Dialog routeDialog = new Dialog(ActivityMarketFeedbackStock.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        final RouteAdapter adapter = new RouteAdapter(ActivityMarketFeedbackStock.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);
        final EditText autoCompleteTextView1 = routeDialog.findViewById(R.id.autoCompleteTextView1);
        autoCompleteTextView1.setVisibility(View.VISIBLE);
        autoCompleteTextView1.addTextChangedListener(new TextWatcher() {
            public void afterTextChanged(Editable s) {
            }

            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            public void onTextChanged(CharSequence s, int start, int before, int count) {
                String searchString = autoCompleteTextView1.getText().toString();
                int textLength = searchString.length();
                routeList.clear();
                for (int i = 0; i < routeListSearchingArray.size(); i++) {
                    String routeName = routeListSearchingArray.get(i).getRouteName();
                    if (textLength <= routeName.length()) {
                        if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                            routeList.add(routeListSearchingArray.get(i));
                        }
                    }
                }
                adapter.notifyDataSetChanged();
            }
        });
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            routeDialog.cancel();
            mSelectedRouteDetails = routeList.get(arg2);
            mRouteName = mSelectedRouteDetails.getRouteName();
            mTextViewRouteName.setText("Route : " + mSelectedRouteDetails.getRouteName());
            isRouteOk = true;
            FlowofOrder(2);
        });
        Button cancel = routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        cancel.setOnClickListener(arg0 -> routeDialog.cancel());
        Button create_route = routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a customer of route " + mRouteName);
        EditText searchText = mDialogCustomer.findViewById(autoCompleteTextView1);
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
            isCustomerOk = true;
            Constants.selectedCustomer = adapterCust.getItem(arg2);
            assert Constants.selectedCustomer != null;
            Constants.selectedCustomer.setRouteName(mRouteName);
            Log.d("TAG", "mTextViewCustomerName : 2");
            mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
            if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("Sub Dealer")) {
                mRdsCode = Constants.selectedCustomer.getRdsTag().trim();
                DealerName(mRdsCode);
            } else {
                mTextViewDealerName.setText("Dealer : ");
            }
        });

        Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowTodayRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> routePlanListofToday) {
        if (!routePlanListofToday.isEmpty()) {
            final ArrayList<RoutePlanMasterDetails> routePlanListofTodaySearchArray = new ArrayList<>(routePlanListofToday);
            final Dialog routePlanListDialog = new Dialog(ActivityMarketFeedbackStock.this, R.style.PauseDialog);
            routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            routePlanListDialog.setContentView(R.layout.select_from_list);
            routePlanListDialog.setCancelable(false);
            TextView title = routePlanListDialog.findViewById(R.id.title);
            title.setText("Please select a Route");
            ListView dialogList = routePlanListDialog.findViewById(R.id.list);
            final RoutePlanTransAdapter adapter = new RoutePlanTransAdapter(ActivityMarketFeedbackStock.this, R.layout.route_list_child, routePlanListofToday);
            dialogList.setAdapter(adapter);
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
                    for (int i = 0; i < routePlanListofTodaySearchArray.size(); i++) {
                        String routeName = routePlanListofTodaySearchArray.get(i).getRouteName();
                        if (textLength <= routeName.length()) {
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                routePlanListofToday.add(routePlanListofTodaySearchArray.get(i));
                            }
                        }
                    }
                    adapter.notifyDataSetChanged();
                }
            });
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                routePlanListDialog.cancel();
                mSelectedTodayRouteDetails = routePlanListofToday.get(arg2);
                mRouteName = mSelectedTodayRouteDetails.getRouteName();
                mTextViewRouteName.setText("Route : " + mSelectedTodayRouteDetails.getRouteName());
                isRouteOk = true;
                FlowofOrder(2);
            });
            Button cancel = routePlanListDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.GONE);
            cancel.setOnClickListener(arg0 -> routePlanListDialog.cancel());
            Button create_route = routePlanListDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            routePlanListDialog.show();
        }
    }

    private Boolean checkCompetitorOkOrNot() {
        boolean isInputOk = false;
        try {
            if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("EXCLUSIVE DEALER")) {
                for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
                    MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
                    String productType = masterObj.getmProductType();
                    if (productType.equalsIgnoreCase("MANDATORY STAR")) {
                        isInputOk = true;
                    }
                }

            }
            else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SHIP TO PARTY")) {
                for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
                    MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
                    String productType = masterObj.getmProductType();
                    if (productType.equalsIgnoreCase("MANDATORY STAR")) {
                        isInputOk = true;
                    }
                }

            }
            else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("NON STAR")) {
                for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
                    MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
                    String productType = masterObj.getmProductType();
                    if (productType.equalsIgnoreCase("BENCHMARK COMPETITOR") || productType.equalsIgnoreCase("OTHER COMPETITOR")) {
                        isInputOk = true;
                    }
                }
            }
            else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SUB DEALER")) {
                for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
                    MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
                    String productType = masterObj.getmProductType();

                    if (productType.equalsIgnoreCase("MANDATORY STAR")) {
                        for (int count1 = 0; count1 < Constants.selectedFeedBackList.size(); count1++) {
                            MarketFeedback masterObj1 = Constants.selectedFeedBackList.get(count1);
                            String productType1 = masterObj1.getmProductType();
                            if (productType1.equalsIgnoreCase("BENCHMARK COMPETITOR") || productType1.equalsIgnoreCase("OTHER COMPETITOR")) {
                                isInputOk = true;
                            }
                        }
                    }
                }
            }
            else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("DEALER")) {
                for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
                    MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
                    String productType = masterObj.getmProductType();

                    if (productType.equalsIgnoreCase("MANDATORY STAR")) {
                        for (int count1 = 0; count1 < Constants.selectedFeedBackList.size(); count1++) {
                            MarketFeedback masterObj1 = Constants.selectedFeedBackList.get(count1);
                            String productType1 = masterObj1.getmProductType();
                            if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("DEALER")) {
                                if (productType1.equalsIgnoreCase("BENCHMARK COMPETITOR") || productType1.equalsIgnoreCase("OTHER COMPETITOR")) {
                                    isInputOk = true;
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception ignored) {
        }
        return isInputOk;
    }
}
