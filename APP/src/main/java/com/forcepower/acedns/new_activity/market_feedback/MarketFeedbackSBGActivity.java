package com.forcepower.acedns.new_activity.market_feedback;

import static android.view.View.VISIBLE;
import static com.forcepower.acedns.R.id.autoCompleteTextView1;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInRoute;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.ActivityMarketFeedbackStock;
import com.forcepower.acedns.activity.ActivityMarketFeedbackStockConfirmation;
import com.forcepower.acedns.adapter.CompetitorColorAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.bean.MarketFeedbackStockAudit;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.SBGFeedbackDataSet;
import com.forcepower.acedns.util.PreferenceData;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.Locale;
import java.util.Objects;

public class MarketFeedbackSBGActivity extends AceDnsParentActivity implements View.OnClickListener {

    Context mContext;

    private Button backButton, buttonAddToCart, buttonCheckOut;
    private LinearLayout layoutViewCustomerName, layoutViewDealerName, layoutViewDepoDetails, layoutViewQuantity, layoutViewScheme;
    private TextView textViewCustomerName, textViewDealerName, textViewDepoDetails, textViewQuantity, textViewScheme;
    private EditText edTextViewCustomerName, edTextViewDealerName, edTextViewDepoDetails, editTextQuantity, editTextScheme;

    private LinearLayout layoutCompetitorName;
    private Button buttonCompetitorName;
    private TextView textViewCompetitorName;
    private TextView counterPotentialHighlighter;

    AceDnsDatabase mAceDnsDatabase;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    ArrayList<String> mCompetitorNameList;
    String mSelectedProductType;
    String mSelectedCompetitor;
    int checker=0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_market_feedback_sbgactivity);

//        mContext = MarketFeedbackSBGActivity.this;
//        mAceDnsDatabase=new AceDnsDatabase(mContext);
//        mAceDnsTransactionDatabase=new AceDnsTransactionDatabase(mContext);
//        mNewDatabaseForSiteLead=new NewDatabaseForSiteLead(mContext);
//
//        try {
//            mNewDatabaseForSiteLead.createNewTable();
//
//            String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
//            mRoutePlanListofToday=mAceDnsTransactionDatabase.getPlanForToday(today);
//            Constants.selectedCustomer = new CustomerDetails();
//            Constants.selectedCustomer.setCustomerCode(PreferenceData.getCheckInOutEmpCode(mContext));
//            Constants.selectedCustomer.setCustomerName(PreferenceData.getCheckInOutEmpName(mContext));
//            Constants.selectedCustomer.setCustomerType(PreferenceData.getCheckInOutEmpType(mContext));
//        }catch (Exception e){
//            Log.d("TAG", "errorrrrrr 1 : "+e.getMessage());
//        }
//
//        initPrimary();
//        initForm();
//        setPredefineData();
    }
    @Override
    public void onClick(View v) {
//        if (v == backButton) {
//            finish();
//        }
//        if(v==buttonCompetitorName){
//            mAceDnsDatabase.GetCompetitorOwnProductName();
//            mAceDnsDatabase.GetCompetitorName();
//            ShowCompetitorNameList();
//        }
//        if (v == buttonAddToCart) {
//            // add in cart
//            if(textViewCompetitorName.getText().toString().trim().isEmpty()){
//                Utils.showToast(mContext, "Please select competitor name.");
//            }else if(editTextQuantity.getText().toString().trim().isEmpty()){
//                Utils.showToast(mContext, "Please enter counter potential.");
//            }else{
//                try {
//                    float i=Float.parseFloat(editTextQuantity.getText().toString().trim());
//
//                    SBGFeedbackDataSet data = new SBGFeedbackDataSet();
//                    data.setCustomerName(Constants.selectedCustomer.getCustomerName());
//                    data.setCustomerCode(Constants.selectedCustomer.getDnsCustCode());
//                    data.setDealerName(mAceDnsDatabase.getDealerName(Constants.selectedCustomer.getRdsTag().trim()));
//                    data.setDealerCode(mAceDnsDatabase.getDealerCode(Constants.selectedCustomer.getRdsTag().trim()));
//                    data.setCompetitorName(textViewCompetitorName.getText().toString().trim());
//                    data.setCompetitorCode(mSelectedProductType);
//                    data.setQuantity(editTextQuantity.getText().toString().trim());
//                    data.setDateTime(getDateTime());
//
//                    mNewDatabaseForSiteLead.insertDataFromSbgFeedback(data);
//                    checker++;
//                    mSelectedProductType="";
//                    mSelectedCompetitor="";
//                    editTextQuantity.setText("");
//                    textViewCompetitorName.setText("");
//                }catch (Exception e){
//                    Utils.showToast(mContext, "Please enter valid counter potential.");
//                }
//            }
//        }
//        if (v == buttonCheckOut) {
//            // goto Checkout page
//            Boolean isDataOk = checkCompetitorOkOrNot();
//            if (isDataOk) {
//                Intent intent = new Intent(MarketFeedbackSBGActivity.this, MarketFeedbackSBGStockConfirmationActivity.class);
//                startActivity(intent);
//            } else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("Exclusive Dealer")) {
//                Utils.showToast(mContext, "Add minimum One MANDATORY STAR");
//            } else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("NON STAR")) {
//                Utils.showToast(mContext, "Add minimum One BENCHMARK COMPETITOR/OTHER COMPETITOR");
//            } else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SUB DEALER")) {
//                Utils.showToast(mContext, "Add minimum MANDATORY STAR & BENCHMARK COMPETITOR/OTHER COMPETITOR");
//            } else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("DEALER")) {
//                Utils.showToast(mContext, "Add minimum One MANDATORY STAR & BENCHMARK COMPETITOR/OTHER COMPETITOR");
//            }
//        }
    }

//    public String getDateTime(){
//        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
//        return sdf.format(new Date());
//    }

//    @SuppressLint({"SetTextI18n"})
//    public void ShowCompetitorNameList() {
//        final Dialog mVerticalDialog = new Dialog(MarketFeedbackSBGActivity.this, R.style.PauseDialog);
//        mVerticalDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
//        mVerticalDialog.setContentView(R.layout.select_with_search);
//        mVerticalDialog.setCancelable(false);
//
//        TextView title = mVerticalDialog.findViewById(R.id.title);
//        title.setText("Please select a competitor");
//        ListView dialogList = mVerticalDialog.findViewById(R.id.list);
//
//        Collections.sort(Constants.competitorPoductType, new Comparator<>() {
//            @Override
//            public int compare(MarketFeedbackStockAudit o1, MarketFeedbackStockAudit o2) {
//                String p1 = o1.getProductType() != null ? o1.getProductType() : "";
//                String p2 = o2.getProductType() != null ? o2.getProductType() : "";
//
//                // Custom priority order
//                int priority1 = getPriority(p1);
//                int priority2 = getPriority(p2);
//
//                // First compare by priority
//                if (priority1 != priority2) {
//                    return Integer.compare(priority1, priority2);
//                }
//
//                // If same priority, sort alphabetically by competitorName
//                String c1 = o1.getCompetitorName() != null ? o1.getCompetitorName() : "";
//                String c2 = o2.getCompetitorName() != null ? o2.getCompetitorName() : "";
//                return c1.compareToIgnoreCase(c2);
//            }
//
//            private int getPriority(String productType) {
//                if (productType.equalsIgnoreCase("MANDATORY STAR")) return 1;
//                if (productType.equalsIgnoreCase("BENCHMARK COMPETITOR")) return 2;
//                return 3; // Others
//            }
//        });
//        for (MarketFeedbackStockAudit item : Constants.competitorPoductType) {
//            Log.d("AFTER_SORT", "_DOWNLOAD_ Name: " + item.getCompetitorName() + " | Type: " + item.getProductType());
//        }
//
//        final CompetitorColorAdapter adapter = new CompetitorColorAdapter(this, R.layout.activity_masterview, Constants.competitorPoductType);
//        dialogList.setAdapter(adapter);
//
//        EditText searchText = mVerticalDialog.findViewById(autoCompleteTextView1);
//        searchText.addTextChangedListener(new TextWatcher() {
//            @Override
//            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
//                adapter.getFilter().filter(s.toString());
//            }
//
//            @Override
//            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
//            }
//
//            @Override
//            public void afterTextChanged(Editable s) {
//            }
//        });
//
//        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
//            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
//            mSelectedCompetitor = Objects.requireNonNull(adapter.getItem(position)).getCompetitorName().toUpperCase();
//            textViewCompetitorName.setText(mSelectedCompetitor);
//            textViewCompetitorName.setVisibility(VISIBLE);
//            mSelectedProductType = Objects.requireNonNull(adapter.getItem(position)).getProductType().toUpperCase();
//
//            try{
//                String qty=mNewDatabaseForSiteLead.getQuantityAgainstCustomerAndCompetitor(Constants.selectedCustomer.getCustomerCode(),mSelectedCompetitor);
//                editTextQuantity.setText(qty);
//            }catch (Exception ignored){}
//            mVerticalDialog.cancel();
//        });
//
//        Button cancel = mVerticalDialog.findViewById(R.id.btn_ok);
//        cancel.setVisibility(View.GONE);
//        mVerticalDialog.show();
//    }
//    private void initPrimary() {
//        backButton = findViewById(R.id.backButton);
//        buttonAddToCart = findViewById(R.id.buttonAddToCart);
//        buttonCheckOut = findViewById(R.id.buttonCheckOut);
//        backButton.setOnClickListener(this);
//        buttonAddToCart.setOnClickListener(this);
//        buttonCheckOut.setOnClickListener(this);
//    }
//    private void initForm() {
//        layoutViewCustomerName = findViewById(R.id.layoutViewCustomerName);
//        layoutViewDealerName = findViewById(R.id.layoutViewDealerName);
//        layoutViewDepoDetails = findViewById(R.id.layoutViewDepoDetails);
//        layoutViewQuantity = findViewById(R.id.layoutViewQuantity);
//        textViewCustomerName = findViewById(R.id.textViewCustomerName);
//        textViewDealerName = findViewById(R.id.textViewDealerName);
//        textViewDepoDetails = findViewById(R.id.textViewDepoDetails);
//        textViewQuantity = findViewById(R.id.textViewQuantity);
//        edTextViewCustomerName = findViewById(R.id.edTextViewCustomerName);
//        edTextViewDealerName = findViewById(R.id.edTextViewDealerName);
//        edTextViewDepoDetails = findViewById(R.id.edTextViewDepoDetails);
//        editTextQuantity = findViewById(R.id.editTextQuantity);
//
//        counterPotentialHighlighter = findViewById(R.id.counterPotentialHighlighter);
//
//        layoutCompetitorName = findViewById(R.id.layoutCompetitorName);
//        buttonCompetitorName = findViewById(R.id.buttonCompetitorName);
//        textViewCompetitorName = findViewById(R.id.textViewCompetitorName);
//
//        buttonCompetitorName.setOnClickListener(this);
//
//        buttonCompetitorName.setText(Html.fromHtml("Competitor Name <font color='#FF0000'>*</font>"));
//        textViewQuantity.setText(Html.fromHtml("Counter Potential (MT) <font color='#FF0000'>*</font>"));
//        counterPotentialHighlighter.setText(Html.fromHtml("<font color='#FF0000'>*</font> The value of counter potential can be changeable."));
//    }
//    private void setPredefineData(){
//        try {
//            edTextViewCustomerName.setText(Constants.selectedCustomer.getCustomerName());
//        }catch (Exception e){
//            Log.d("TAG", "errorrrrrr 2 : "+e.getMessage());
//        }
//
//        try {
//            edTextViewDealerName.setText(mAceDnsDatabase.getDealerName(Constants.selectedCustomer.getRdsTag().trim()));
//        }catch (Exception e){
//            Log.d("TAG", "errorrrrrr 3 : "+e.getMessage());
//        }
//
//        try {
//            edTextViewDepoDetails.setText(mRoutePlanListofToday.get(0).getRouteName());
//        }catch (Exception e){
//            Log.d("TAG", "errorrrrrr 4 : "+e.getMessage());
//        }
//    }
//
//    private Boolean checkCompetitorOkOrNot() {
//        boolean isInputOk = false;
//        try {
//            if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("EXCLUSIVE DEALER")) {
//                ArrayList<SBGFeedbackDataSet> dataSet=mNewDatabaseForSiteLead.getAllSbgFeedback();
//                for (int count = 0; count < dataSet.size(); count++) {
//                    SBGFeedbackDataSet masterObj = dataSet.get(count);
//                    String productType = masterObj.getCompetitorCode();
//                    if (productType.equalsIgnoreCase("MANDATORY STAR")) {
//                        isInputOk = true;
//                    }
//                }
//
//            }
//            else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SHIP TO PARTY")) {
//                ArrayList<SBGFeedbackDataSet> dataSet=mNewDatabaseForSiteLead.getAllSbgFeedback();
//                for (int count = 0; count < dataSet.size(); count++) {
//                    SBGFeedbackDataSet masterObj = dataSet.get(count);
//                    String productType = masterObj.getCompetitorCode();
//                    if (productType.equalsIgnoreCase("MANDATORY STAR")) {
//                        isInputOk = true;
//                    }
//                }
//
//            }
//            else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("NON STAR")) {
//                ArrayList<SBGFeedbackDataSet> dataSet=mNewDatabaseForSiteLead.getAllSbgFeedback();
//                for (int count = 0; count < dataSet.size(); count++) {
//                    SBGFeedbackDataSet masterObj = dataSet.get(count);
//                    String productType = masterObj.getCompetitorCode();
//                    if (productType.equalsIgnoreCase("BENCHMARK COMPETITOR") || productType.equalsIgnoreCase("OTHER COMPETITOR")) {
//                        isInputOk = true;
//                    }
//                }
//            }
//            else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SUB DEALER")) {
//                ArrayList<SBGFeedbackDataSet> dataSet=mNewDatabaseForSiteLead.getAllSbgFeedback();
//                for (int count = 0; count < dataSet.size(); count++) {
//                    SBGFeedbackDataSet masterObj = dataSet.get(count);
//                    String productType = masterObj.getCompetitorCode();
//
//                    if (productType.equalsIgnoreCase("MANDATORY STAR")) {
//                        for (int count1 = 0; count1 < dataSet.size(); count1++) {
//                            SBGFeedbackDataSet masterObj1 = dataSet.get(count1);
//                            String productType1 = masterObj1.getCompetitorCode();
//                            if (productType1.equalsIgnoreCase("BENCHMARK COMPETITOR") || productType1.equalsIgnoreCase("OTHER COMPETITOR")) {
//                                isInputOk = true;
//                            }
//                        }
//                    }
//                }
//            }
//            else if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("DEALER")) {
//                ArrayList<SBGFeedbackDataSet> dataSet=mNewDatabaseForSiteLead.getAllSbgFeedback();
//                for (int count = 0; count < dataSet.size(); count++) {
//                    SBGFeedbackDataSet masterObj = dataSet.get(count);
//                    String productType = masterObj.getCompetitorCode();
//
//                    if (productType.equalsIgnoreCase("MANDATORY STAR")) {
//                        for (int count1 = 0; count1 < dataSet.size(); count1++) {
//                            SBGFeedbackDataSet masterObj1 = dataSet.get(count1);
//                            String productType1 = masterObj1.getCompetitorCode();
//                            if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("DEALER")) {
//                                if (productType1.equalsIgnoreCase("BENCHMARK COMPETITOR") || productType1.equalsIgnoreCase("OTHER COMPETITOR")) {
//                                    isInputOk = true;
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        } catch (Exception ignored) {
//        }
//        return isInputOk;
//    }
}