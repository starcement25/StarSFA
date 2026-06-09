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
import android.util.TypedValue;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
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
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.util.ArrayList;
import java.util.List;
import java.util.Objects;

import static com.forcepower.acedns.R.id.autoCompleteTextView1;

import androidx.annotation.NonNull;

public class ActivityMarketFeedbackWSPRSP extends AceDnsParentActivity {
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
    public static Button mButtonAddtoCart = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonCheckOut = null;

    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextQuantity = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextDiscount = null;

    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mLinearLayoutChild = null;

    //FeedBackAdapter mFeedBackAdapter;
    private static int mColumnCount = 0;
    ProgressDialog mStepProgressDialog;
    Handler mStepHandler;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    ArrayList<RouteDetails> mRouteDetailsList;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    MarketFeedback mMarketFeedback;
    ArrayList<String> mCompetitorNameList;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    private Handler mHandler;
    ProgressDialog loader;

    RouteDetails mSelectedRouteDetails;
    RoutePlanMasterDetails mSelectedTodayRouteDetails;
    MarketFeedbackStockAudit mMarketFeedbackStockAudit;
    private List<EditText> mEditTextList;// = new ArrayList<EditText>();
    private boolean isRouteOk = true;
    private boolean isCustomerOk = true;
    private String mRouteName = "";
    private String mDealerName = "";
    private String mRdsCode = "";
    private String mCompetitorName = "";
    private String mSelectedCompetitor = "";
    private String mUom = "";
    private String menuType = "mf_stock";
    private String mf_tagging = "no";
    private String mf_customer_branchwise = "no";
    public String brance_code = "";

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_market_feedback_stock);
        RegisterActivities.registerActivity(this);

        mContext = ActivityMarketFeedbackWSPRSP.this;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        Constants.selectedFeedBackList = new ArrayList<>();
        if (getIntent().hasExtra("menuType")) {
            menuType = Objects.requireNonNull(getIntent().getExtras()).getString("menuType");
        }

        Constants.selectedCustomer = null;
        mSelectedRouteDetails = null;
        Constants.mMarketFeedbackStockAuditList = new ArrayList<>();

        mf_tagging = mAceDnsDatabase.getMfTagging();
        mf_customer_branchwise = mAceDnsDatabase.getMfCustomerBranchwise();
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

        mButtonAddtoCart.setOnClickListener(v -> {
            String inpqty;
            if (!mSelectedCompetitor.isEmpty() && mEditTextList != null && !mEditTextList.isEmpty()) {
                inpqty = mEditTextQuantity.getText().toString();
                mMarketFeedbackStockAudit = new MarketFeedbackStockAudit();
                mMarketFeedbackStockAudit.setCompetitorName(mSelectedCompetitor);
                mMarketFeedbackStockAudit.setQuantity(inpqty);
                mMarketFeedbackStockAudit.setDiscount(mEditTextDiscount.getText().toString());
                for (int count = 0; count < mEditTextList.size(); count++) {
                    String value = mEditTextList.get(count).getText().toString().trim();
                    if (count == 0) {
                        mMarketFeedback.setPtd(value);
                    }
                    if (count == 1) {
                        mMarketFeedback.setPtr(value);
                    }
                    if (count == 2) {
                        mMarketFeedback.setPtc(value);
                    }
                    if (count == 3) {
                        mMarketFeedback.setPv(value);
                    }
                }

                mMarketFeedbackStockAudit.setprice(mMarketFeedback.getPtd());
                Constants.mMarketFeedbackStockAuditList.add(mMarketFeedbackStockAudit);
                Constants.selectedFeedBackList.add(mMarketFeedback);
                ReFreshData();
            } else {
                Utils.showToast(mContext, "Please select competitor");
            }
        });

        mButtonCheckOut.setOnClickListener(v -> {
            if (Constants.mMarketFeedbackStockAuditList != null && !Constants.mMarketFeedbackStockAuditList.isEmpty()) {
                Intent intent = new Intent(ActivityMarketFeedbackWSPRSP.this, ActivityMarketFeedbackStockConfirmation.class);
                intent.putExtra("menuType", menuType);
                startActivity(intent);
            } else {
                Utils.showToast(mContext, "Please add to cart");
            }
        });

        mStepHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mStepProgressDialog.dismiss();
                final int step = msg.getData().getInt("STEP");
                ActivityMarketFeedbackWSPRSP.this.runOnUiThread(() -> {
                    switch (step) {
                        case 1:
                            if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
                                if (Utils.NotCheckedOut(mContext)) {
                                    isRouteOk = true;
                                    mSelectedTodayRouteDetails = mRoutePlanListofToday.get(Utils.getPositionOfCurrentCheckedInRoute(true, mContext, mRoutePlanListofToday, null));
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
                                if (Utils.NotCheckedOut(mContext)) {
                                    isRouteOk = true;
                                    mSelectedRouteDetails = mRouteDetailsList.get(Utils.getPositionOfCurrentCheckedInRoute(false, mContext, null, mRouteDetailsList));
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
                            if (Utils.NotCheckedOut(mContext)) {
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
                            mTextViewDealerName.setText("Dealer : " + mDealerName);
                            break;
                    }
                });
            }
        };
        FlowofOrder(1);

        ConnectionDetector cd;
        cd = new ConnectionDetector(mContext);
        if (cd.isConnectingToInternet()) {
            loader = new ProgressDialog(mContext);
            loader.setMessage("Fetching Data.Please wait..");
            if (cd.isConnectingToInternet()) {
                loader.show();
            }
            Constants.masterApiCallingFlag = false;
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

        if (cd.isConnectingToInternet()) {
            if (Constants.menuDetailsObj.getMarketFeedback().toLowerCase().matches("yes")) {
                if (Constants.marketFeedbackDetailsObj.getMf_tagging().equalsIgnoreCase("yes")) {
                    getData();
                }
            }
        }

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                String aResponse = msg.getData().getString("message");
                assert aResponse != null;
                if (aResponse.equalsIgnoreCase("JobDoneC")) {
                    loader.cancel();
                }
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    getDataTag();
                }
                if (aResponse.equalsIgnoreCase("JobDoneM")) {
                    getDataCustomer();
                }
            }
        };
    }

    @SuppressLint("SetTextI18n")
    public void ChooseOrderType() {
        if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary") || Constants.isVanSales) {
            Constants.orderAuditType = "Secondary";
            FlowofOrder(1);
        } else {
            final Dialog dialgoCondition = new Dialog(mContext, R.style.PauseDialog);
            dialgoCondition.setCancelable(false);
            dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialgoCondition.setContentView(R.layout.condition_trade_nontrade);
            Objects.requireNonNull(dialgoCondition.getWindow()).setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

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
                    Constants.orderAuditType = "Primary";
                } else {
                    Constants.orderAuditType = "Secondary";
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

    @SuppressLint("SetTextI18n")
    public void ShowCompetitorNameList() {
        final Dialog mVerticalDialog = new Dialog(ActivityMarketFeedbackWSPRSP.this, R.style.PauseDialog);
        mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mVerticalDialog.setContentView(R.layout.select_with_search);
        mVerticalDialog.setCancelable(false);

        TextView title = mVerticalDialog.findViewById(R.id.title);
        title.setText("Please select a competitor");
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
            mSelectedCompetitor = adapter.getItem(position);
            mTextViewCompetitorName.setText(mSelectedCompetitor);
            FlowofOrder(4);
            mVerticalDialog.cancel();
        });

        Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.GONE);
        mVerticalDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void DealerName(String rdscode) {
        if (!rdscode.trim().isEmpty()) {
            FlowofOrder(6);
        } else {
            mTextViewDealerName.setText("Dealer : ");
        }
    }

    @SuppressLint("SetTextI18n")
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

        mEditTextQuantity = findViewById(R.id.editTextQuantity);
        mEditTextDiscount = findViewById(R.id.editTextDiscount);

        mImageViewHeaderLogo = findViewById(R.id.imagelogo);

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(ActivityMarketFeedbackWSPRSP.this) + "~" + Utils.getDBVersion(ActivityMarketFeedbackWSPRSP.this));

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

        String SetupValues = Constants.marketFeedbackDetailsObj.getMfCol3();
        if (!SetupValues.trim().isEmpty()) {
            if (SetupValues.contains(",")) {
                String[] arrayOfSetupValues = SetupValues.split(",");
                for (String currentItem : arrayOfSetupValues) {
                    if (currentItem.contains(menuType) && currentItem.contains("#")) {
                        mTextViewCol3.setText(currentItem.split("#")[1]);
                        if (Constants.marketFeedbackDetailsObj.getMf_sub_menu_qty_unit().contains("MT") || Constants.marketFeedbackDetailsObj.getMf_sub_menu_qty_unit().contains("mt")) {
                            mTextViewQty.setText("Quantity(MT)");
                        }
                        break;
                    }
                }
            } else {
                if (SetupValues.contains(menuType) && SetupValues.contains("#")) {
                    mTextViewCol3.setText(SetupValues.split("#")[1]);
                    if (Constants.marketFeedbackDetailsObj.getMf_sub_menu_qty_unit().contains("MT") || Constants.marketFeedbackDetailsObj.getMf_sub_menu_qty_unit().contains("mt")) {
                        mTextViewQty.setText("Quantity(MT)");
                    }
                }
            }
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
    }

    public void ClearText() {
        mTextViewRouteName.setText("");
        mTextViewCustomerName.setText("");
        mTextViewCompetitorName.setText("");
        mTextViewDealerName.setText("");
        mTextViewUOM.setText("");
        brance_code = "";
    }

    public void DrawView() {
        mEditTextList = new ArrayList<>();
        mLinearLayoutChild.removeAllViews();
        for (int count = 0; count < mColumnCount; count++) {
            mLinearLayoutChild.addView(AddEditText(String.valueOf(count)));
        }
    }

    private EditText AddEditText(String tag) {
        EditText editText = new EditText(this);
        editText.setTag(tag);
        LayoutParams qtparam = new LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
        editText.setLayoutParams(qtparam);
        editText.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        editText.setSingleLine(false);
        editText.setImeOptions(EditorInfo.IME_ACTION_NEXT);
        editText.setTextColor(Color.GRAY);
        editText.setTextSize(TypedValue.COMPLEX_UNIT_SP, 15);
        editText.setTypeface(null, Typeface.NORMAL);
        editText.setHintTextColor(Color.LTGRAY);
        mEditTextList.add(editText);
        return editText;
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
                        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes") && mf_tagging.matches("no")) {
                            RoutePlanDetails routeplandetails = mAceDnsDatabase.getRoutePlanDetailsObj();
                            if (routeplandetails.getRouteCustomerPlanning().equalsIgnoreCase("yes")) {
                                String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
                                mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(today, mSelectedTodayRouteDetails.getRoutecode());
                            } else {
                                mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedTodayRouteDetails.getRoutecode());
                            }
                        } else if (mf_tagging.matches("no")) {
                            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRoute(mSelectedRouteDetails.getRouteCode());
                        } else {
                            mCustomerDetailsList = mAceDnsDatabase.getCustomerListByMarketFeedbackTagging();
                        }
                        break;
                    case 3:
                        brance_code = Constants.selectedCustomer.getCustomerCode();
                        mCompetitorName = mAceDnsDatabase.GetCompetitorNameForWspRsp(menuType.toLowerCase(), brance_code, mf_customer_branchwise);
                        break;
                    case 4:
                        mUom = mAceDnsDatabase.GetUOM(mSelectedCompetitor);
                        break;
                    case 5:
                        break;
                    case 6:
                        mDealerName = mAceDnsDatabase.getDealerName(mRdsCode);
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
        final Dialog routeDialog = new Dialog(ActivityMarketFeedbackWSPRSP.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = routeDialog.findViewById(R.id.list);
        final RouteAdapter adapter = new RouteAdapter(ActivityMarketFeedbackWSPRSP.this, R.layout.route_list_child, routeList);
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
            brance_code = Constants.selectedCustomer.getBranchCode();
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
            final Dialog routePlanListDialog = new Dialog(ActivityMarketFeedbackWSPRSP.this, R.style.PauseDialog);
            routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            routePlanListDialog.setContentView(R.layout.select_from_list);
            routePlanListDialog.setCancelable(false);
            TextView title = routePlanListDialog.findViewById(R.id.title);
            title.setText("Please select a Route");
            ListView dialogList = routePlanListDialog.findViewById(R.id.list);
            final RoutePlanTransAdapter adapter = new RoutePlanTransAdapter(ActivityMarketFeedbackWSPRSP.this, R.layout.route_list_child, routePlanListofToday);
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

    private void getData() {
        new Thread() {
            public void run() {
                new commonAsyncTaskMaster(mContext, "route_plan_mf");
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

    private void getDataCustomer() {
        new Thread() {
            public void run() {
                new commonAsyncTaskMaster(mContext, "customer_master_mf");
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDoneC");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }
}
