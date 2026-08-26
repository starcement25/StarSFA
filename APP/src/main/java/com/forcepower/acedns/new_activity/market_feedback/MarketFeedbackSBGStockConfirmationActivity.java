package com.forcepower.acedns.new_activity.market_feedback;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInRoute;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.ActivityMarketFeedbackStockConfirmation;
import com.forcepower.acedns.adapter.MarketFeedbackStockAuditAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerCompetitorQuantityDataSet;
import com.forcepower.acedns.newDataBase.data_set.CustomerMarketFeedbackDetailsDataSet;
import com.forcepower.acedns.newDataBase.data_set.SBGFeedbackDataSet;
import com.forcepower.acedns.new_activity.market_feedback.adapter.MarketFeedbackSBGStockAuditAdapter;
import com.forcepower.acedns.new_activity.market_feedback.api_connect.TRANS_CompetitorPotentialFeedbackTask;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.ShowDataSetAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.PreferenceData;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;

public class MarketFeedbackSBGStockConfirmationActivity extends AceDnsParentActivity implements View.OnClickListener, MarketFeedbackSBGStockAuditAdapter.OnQuantityChangedListener {

    Context mContext;
    private Button backButton, buttonSubmitFeedback;
    private ListView listViewProduct;
    private TextView textViewTotalCount, textViewCustomerName, textViewCustomerType, textViewRouteName, textViewCustomerCode, textViewUniverseType, textViewStateName, textViewDistrictName, textViewBlockName, textViewGramPanchayat;
    private LinearLayout addressLayout;
    private NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private ArrayList<CustomerCompetitorQuantityDataSet> dataSet;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    RoutePlanMasterDetails mSelectedTodayRouteDetails;
    ArrayList<DataSet> universeTypeList;
    private String totalValue;
    String customerDNSCode = "";
    ProgressDialog loader;
    Handler mHandler;
    MarketFeedbackSBGStockAuditAdapter adapter;
    String customertype="";
    String gram="";

// New Feature
    private LinearLayout otherGramPanchayatLayout,otherGramPanchayatLayoutForm;
    private EditText otherGramPanchayatEditText;
    private Button otherGramPanchayatSaveButton;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_market_feedback_sbgstock_confirmation);
        mContext = MarketFeedbackSBGStockConfirmationActivity.this;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        try {
            Log.d("TAG", "_DOWNLOAD_ onCreate: " + PreferenceData.getCheckInOutEmpCode(mContext));
            dataSet = mNewDatabaseForSiteLead.getAllCustomerCompetitorQuantity(PreferenceData.getCheckInOutEmpCode(mContext));
            int total = 0;
            for (int i = 0; i < dataSet.size(); i++) {
                total += Integer.parseInt(dataSet.get(i).getQuantity());
                customerDNSCode = dataSet.get(i).getCustomerDnsCode();
            }
            totalValue = String.valueOf(total);
            mNewDatabaseForSiteLead.createNewTable();
            String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
            mRoutePlanListofToday = mAceDnsTransactionDatabase.getPlanForToday(today);
            mSelectedTodayRouteDetails = mRoutePlanListofToday.get(getPositionOfCurrentCheckedInRoute(true, mContext, mRoutePlanListofToday, null));
            Constants.selectedCustomer = new CustomerDetails();
            Constants.selectedCustomer.setCustomerCode(PreferenceData.getCheckInOutEmpCode(mContext));
            Constants.selectedCustomer.setCustomerName(PreferenceData.getCheckInOutEmpName(mContext));
            Constants.selectedCustomer.setCustomerType(PreferenceData.getCheckInOutEmpType(mContext));
        } catch (Exception e) {
            Log.d("TAG", "errorrrrrr 1 : " + e.getMessage());
        }
        init();

        setupHandler();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton)
            finish();
        if (v == buttonSubmitFeedback) {
            if(customertype.equalsIgnoreCase("ne")){
                if (textViewUniverseType.getText().toString().trim().equalsIgnoreCase("Select Universe Type")||textViewUniverseType.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please select the universe type.", Toast.LENGTH_SHORT).show();
                } else if (textViewStateName.getText().toString().trim().equalsIgnoreCase("Select State Name")||textViewStateName.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please select the state name.", Toast.LENGTH_SHORT).show();
                } else if (textViewDistrictName.getText().toString().trim().equalsIgnoreCase("Select District Name")||textViewDistrictName.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please select the district name.", Toast.LENGTH_SHORT).show();
                } else if (textViewBlockName.getText().toString().trim().equalsIgnoreCase("Select Block Name")||textViewBlockName.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please select the block name.", Toast.LENGTH_SHORT).show();
                } else if (textViewGramPanchayat.getText().toString().trim().equalsIgnoreCase("Select Gram Panchayat")||textViewGramPanchayat.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please select the gram panchayat.", Toast.LENGTH_SHORT).show();
                } else {
                    saveMarketFeedbackStockAuditToDatabase();
                }
            }else{
                if (textViewUniverseType.getText().toString().trim().equalsIgnoreCase("Select Universe Type")||textViewUniverseType.getText().toString().trim().isEmpty()) {
                    Toast.makeText(mContext, "Please select the universe type.", Toast.LENGTH_SHORT).show();
                } else {
                    saveMarketFeedbackStockAuditToDatabase();
                }
            }
        }
        if (v == textViewUniverseType) {
            show_list_data_dialog(universeTypeList, "universeType");
        }
        if (v == textViewStateName) {
            if (textViewStateName.getText().toString().equalsIgnoreCase("Select State Name")||textViewStateName.getText().toString().isEmpty()){
                ArrayList<DataSet> arr= mNewDatabaseForSiteLead.getAllStateName();
                show_list_data_dialog(arr, "stateName");
            }
            else
                Toast.makeText(mContext, "Please contact to admin for changing the state.", Toast.LENGTH_SHORT).show();
        }
        if (v == textViewDistrictName) {
            if(textViewStateName.getText().toString().equalsIgnoreCase("Select State Name")||textViewStateName.getText().toString().isEmpty())
                Toast.makeText(mContext, "Please Select State Name.", Toast.LENGTH_SHORT).show();
            else{
                if (textViewDistrictName.getText().toString().equalsIgnoreCase("Select District Name")||textViewDistrictName.getText().toString().isEmpty()) {
                    ArrayList<DataSet> arr= mNewDatabaseForSiteLead.getAllDistrictNameAgainstState(textViewStateName.getText().toString());
                    show_list_data_dialog(arr, "districtName");
                }else{
                    Toast.makeText(mContext, "Please contact to admin for changing the district.", Toast.LENGTH_SHORT).show();
                }
            }
        }
        if (v == textViewBlockName) {
            if(textViewDistrictName.getText().toString().equalsIgnoreCase("Select District Name")||textViewDistrictName.getText().toString().isEmpty())
                Toast.makeText(mContext, "Please Select District Name.", Toast.LENGTH_SHORT).show();
            else{
                if (textViewBlockName.getText().toString().equalsIgnoreCase("Select Block Name")||textViewBlockName.getText().toString().isEmpty()) {
                    ArrayList<DataSet> arr= mNewDatabaseForSiteLead.getAllBlockNameAgainstDistrict(textViewStateName.getText().toString(),textViewDistrictName.getText().toString());
                    show_list_data_dialog(arr, "blockName");
                }else{
                    Toast.makeText(mContext, "Please contact to admin for changing the block.", Toast.LENGTH_SHORT).show();
                }
            }
        }
        if (v == textViewGramPanchayat) {
            if(textViewBlockName.getText().toString().equalsIgnoreCase("Select Block Name")||textViewBlockName.getText().toString().isEmpty())
                Toast.makeText(mContext, "Please Select Block Name.", Toast.LENGTH_SHORT).show();
            else{
                if (textViewGramPanchayat.getText().toString().equalsIgnoreCase("Select Gram Panchayat")||textViewGramPanchayat.getText().toString().isEmpty()) {
                    ArrayList<DataSet> arr= mNewDatabaseForSiteLead.getAllGramPanchayatAgainstBlock(textViewStateName.getText().toString(),textViewDistrictName.getText().toString(),textViewBlockName.getText().toString());
                    show_list_data_dialog(arr, "gramPanchayat");
                }else{
                    Toast.makeText(mContext, "Please contact to admin for changing the gram panchayat.", Toast.LENGTH_SHORT).show();
                }

            }
        }

        if(v==otherGramPanchayatSaveButton){
            if(otherGramPanchayatEditText.getText().toString().trim().isEmpty()){
                Toast.makeText(mContext, "Please enter the gram panchayat.", Toast.LENGTH_SHORT).show();
                return;
            }
            textViewGramPanchayat.setText(otherGramPanchayatEditText.getText().toString()+" (O)");
            otherGramPanchayatLayout.setVisibility(GONE);
        }
        if(v==otherGramPanchayatLayout){
            otherGramPanchayatLayout.setVisibility(GONE);
        }
    }

    @SuppressLint("SetTextI18n")
    private void init() {
        backButton = findViewById(R.id.backButton);
        buttonSubmitFeedback = findViewById(R.id.buttonSubmitFeedback);
        listViewProduct = findViewById(R.id.listViewProduct);
        textViewTotalCount = findViewById(R.id.textViewTotalCount);
        textViewCustomerName = findViewById(R.id.textViewCustomerName);
        textViewCustomerType = findViewById(R.id.textViewCustomerType);
        textViewRouteName = findViewById(R.id.textViewRouteName);
        textViewCustomerCode = findViewById(R.id.textViewCustomerCode);
        addressLayout=findViewById(R.id.addressLayout);
        addressLayout.setVisibility(GONE);

        backButton.setOnClickListener(this);
        buttonSubmitFeedback.setOnClickListener(this);

        sortDataSet(dataSet);
        adapter = new MarketFeedbackSBGStockAuditAdapter(mContext, R.layout.market_feedback_sbg_confirm_child, dataSet, this);
        listViewProduct.setAdapter(adapter);
        listViewProduct.post(() -> setListViewHeightBasedOnChildren(listViewProduct));
        textViewCustomerName.setText(Constants.selectedCustomer.getCustomerName());
        textViewCustomerType.setText(Constants.selectedCustomer.getCustomerType());
//        textViewRouteName.setText(mRoutePlanListofToday.get(0).getRouteName());
        textViewRouteName.setText(mSelectedTodayRouteDetails.getRouteName());
        textViewCustomerCode.setText(customerDNSCode);
        textViewTotalCount.setText(totalValue);

        textViewUniverseType = findViewById(R.id.textViewUniverseType);
        textViewUniverseType.setText("Select Universe Type");
        textViewUniverseType.setOnClickListener(this);
        textViewStateName = findViewById(R.id.textViewStateName);
        textViewStateName.setText("Select State Name");
        textViewStateName.setOnClickListener(this);
        textViewDistrictName = findViewById(R.id.textViewDistrictName);
        textViewDistrictName.setText("Select District Name");
        textViewDistrictName.setOnClickListener(this);
        textViewBlockName = findViewById(R.id.textViewBlockName);
        textViewBlockName.setText("Select Block Name");
        textViewBlockName.setOnClickListener(this);
        textViewGramPanchayat = findViewById(R.id.textViewGramPanchayat);
        textViewGramPanchayat.setText("Select Gram Panchayat");
        textViewGramPanchayat.setOnClickListener(this);

        otherGramPanchayatLayout=findViewById(R.id.otherGramPanchayatLayout);
        otherGramPanchayatLayout.setVisibility(GONE);
        otherGramPanchayatLayout.setOnClickListener(this);
        otherGramPanchayatLayoutForm=findViewById(R.id.otherGramPanchayatLayoutForm);
        otherGramPanchayatLayoutForm.setOnClickListener(this);
        otherGramPanchayatEditText=findViewById(R.id.otherGramPanchayatEditText);
        otherGramPanchayatSaveButton=findViewById(R.id.otherGramPanchayatSaveButton);
        otherGramPanchayatSaveButton.setOnClickListener(this);

        showCustomerDetails();

        dataSetUniverseTypeList();
    }

    // Add this method
    private void setListViewHeightBasedOnChildren(ListView listView) {
        if (listView.getAdapter() == null) return;

        int totalHeight = 0;
        for (int i = 0; i < listView.getAdapter().getCount(); i++) {
            View listItem = listView.getAdapter().getView(i, null, listView);
            listItem.measure(
                    View.MeasureSpec.makeMeasureSpec(listView.getWidth(), View.MeasureSpec.AT_MOST),
                    View.MeasureSpec.makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED)
            );
            totalHeight += listItem.getMeasuredHeight();
        }

        ViewGroup.LayoutParams params = listView.getLayoutParams();
        params.height = totalHeight + (listView.getDividerHeight() * (listView.getAdapter().getCount() - 1));
        listView.setLayoutParams(params);
        listView.requestLayout();
    }

    @SuppressLint("SetTextI18n")
    public void show_list_data_dialog(ArrayList<DataSet> dataSet, String type) {
        try {
            final Dialog mDestinationDialog = new Dialog(this, R.style.MyMaterialTheme);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_from_list1);
            mDestinationDialog.setCancelable(true);

            TextView title = mDestinationDialog.findViewById(R.id.title);
            title.setText("Please select");
            ImageView imageView1 = mDestinationDialog.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDestinationDialog.dismiss());
            ListView dialogList = mDestinationDialog.findViewById(R.id.list);
            Button btn_cncl = mDestinationDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setVisibility(GONE);

            final ShowDataSetAdapter pAdapter = new ShowDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                if (type.equalsIgnoreCase("universeType")) {
                    textViewUniverseType.setText(dataSet.get(position).getValue());
                }
                if (type.equalsIgnoreCase("stateName")) {
                    textViewStateName.setText(dataSet.get(position).getValue());
                }
                if (type.equalsIgnoreCase("districtName")) {
                    textViewDistrictName.setText(dataSet.get(position).getValue());
                }
                if (type.equalsIgnoreCase("blockName")) {
                    textViewBlockName.setText(dataSet.get(position).getValue());
                }
                if (type.equalsIgnoreCase("gramPanchayat")) {
                    if(dataSet.get(position).getValue().equalsIgnoreCase("other")){
                        gram="other";
                        otherGramPanchayatLayout.setVisibility(VISIBLE);
                    }else{
                        textViewGramPanchayat.setText(dataSet.get(position).getValue());
                    }
                }
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
    }

    private void dataSetUniverseTypeList() {
        universeTypeList = new ArrayList<>();

        String[][] data = {
                {"Star - Dealer", "Star - Dealer"},
                {"Non Star - Dealer", "Non Star - Dealer"},
                {"Star -Sub Dealer", "Star -Sub Dealer"},
                {"Star - RSAR", "Star - RSAR"},
                {"Non Star - Non link Sub Dealer", "Non Star - Non link Sub Dealer"},
                {"Non Star - Star Link Sub Dealer", "Non Star - Star Link Sub Dealer"}
        };

        for (String[] entry : data) {
            universeTypeList.add(new DataSet(entry[0], entry[1], false));
        }
    }

    private void showCustomerDetails() {
        try{
            CustomerMarketFeedbackDetailsDataSet customerDetails = mNewDatabaseForSiteLead.getCustomerMarketFeedbackDetails(customerDNSCode);
            textViewUniverseType.setText(customerDetails.getCustomerUniverseType());
            textViewStateName.setText(customerDetails.getCustomerState());
            textViewDistrictName.setText(customerDetails.getCustomerDistrict());
            textViewBlockName.setText(customerDetails.getCustomerBlockName());
            textViewGramPanchayat.setText(customerDetails.getCustomerGramPanchayat());
            customertype=customerDetails.getCustomerType();
            Log.d("TAG", "_DOWNLOAD_ showCustomerDetails: "+customertype);
            if(customertype.equalsIgnoreCase("ne"))
                addressLayout.setVisibility(VISIBLE);
            else
                addressLayout.setVisibility(GONE);
        }catch (Exception e){
            Log.d("TAG", "showCustomerDetails: "+e.getMessage());
        }
    }

    private void sortDataSet(ArrayList<CustomerCompetitorQuantityDataSet> list) {
        Collections.sort(list, (a, b) -> {
            int priorityA = getSortPriority(a);
            int priorityB = getSortPriority(b);

            if (priorityA != priorityB) {
                return Integer.compare(priorityA, priorityB);
            }

            // Within same group sort alphabetically by competitor name
            String nameA = a.getCompetitorName() == null ? "" : a.getCompetitorName();
            String nameB = b.getCompetitorName() == null ? "" : b.getCompetitorName();
            return nameA.compareToIgnoreCase(nameB);
        });
    }

    private int getSortPriority(CustomerCompetitorQuantityDataSet item) {
        String mandatory = item.getMandatory() == null ? "" : item.getMandatory().trim().toUpperCase();
        String competitorName = item.getCompetitorName() == null ? "" : item.getCompetitorName().trim();

        // "Others" always last regardless of mandatory flag
        if (competitorName.equalsIgnoreCase("Others")) return 3;

        return switch (mandatory) {
            case "Y" -> 1; // Mandatory first
            case "N" -> 2; // Non-mandatory second
            default -> 3; // Anything else last
        };
    }

    private void setupHandler() {
        mHandler = new Handler(Looper.getMainLooper()) {
            @Override
            public void handleMessage(@NonNull Message msg) {
            }
        };
    }

    // Called by the adapter every time an EditText changes
    @Override
    public void onQuantityChanged() {
        int total = 0;
        for (CustomerCompetitorQuantityDataSet item : dataSet) {
            try {
                total += Integer.parseInt(item.getQuantity());
            } catch (NumberFormatException ignored) {
            }
        }
        totalValue = String.valueOf(total);
        textViewTotalCount.setText(totalValue);  // live update
    }

    public void saveMarketFeedbackStockAuditToDatabase() {
        new GPSTracker(mContext);
        runOnUiThread(() -> {
            loader = new ProgressDialog(mContext);
            loader.setMessage("Saving Data. Please wait...");
            loader.setCancelable(false); // prevent accidental dismissal
            loader.show();
        });

        new Thread(() -> {
            // 1. Persist the edited quantities back into the DB
            updateSbgFeedbackQuantitiesInDb();

            String universeType=textViewUniverseType.getText().toString().trim();
            String state=textViewStateName.getText().toString().trim();
            String district=textViewDistrictName.getText().toString().trim();
            String block=textViewBlockName.getText().toString().trim();
            String gramPanchayat=textViewGramPanchayat.getText().toString().trim();

            if(state.equalsIgnoreCase("Select State Name")){
                state="";
            }
            if(district.equalsIgnoreCase("Select District Name")){
                district="";
            }
            if(block.equalsIgnoreCase("Select Block Name")){
                block="";
            }
            if(gramPanchayat.equalsIgnoreCase("Select Gram Panchayat")){
                gramPanchayat="";
            }

            // dataSet is already up-to-date — pass it to your API task
            new TRANS_CompetitorPotentialFeedbackTask(mContext, universeType, state, district, block, gramPanchayat, new TRANS_CompetitorPotentialFeedbackTask.OnTaskCompleteListener() {
                @Override
                public void onSuccess() {
                    // handle success — e.g. finish activity, show toast
                    runOnUiThread(() -> {
                        if (loader != null && loader.isShowing()) loader.dismiss();
                        Toast.makeText(mContext, "Submitted successfully", Toast.LENGTH_SHORT).show();
                        PreferenceData.setAddSBG(mContext, "1");
                        finish();
                    });
                }

                @Override
                public void onFailure(String error) {
                    runOnUiThread(() -> {
                        if (loader != null && loader.isShowing()) loader.dismiss();
                        Toast.makeText(mContext, "Failed: " + error, Toast.LENGTH_SHORT).show();
                    });
                }
            }).execute();

            Message msgObj = mHandler.obtainMessage();
            Bundle b = new Bundle();
            b.putString("message", "SubmitJobDone");
            msgObj.setData(b);
            mHandler.sendMessage(msgObj);
        }).start();
    }

    private void updateSbgFeedbackQuantitiesInDb() {
        for (int i = 0; i < dataSet.size(); i++) {
            Log.d("TAG", "_DOWNLOAD_ updateSbgFeedbackQuantitiesInDb: " + i);
            CustomerCompetitorQuantityDataSet sbgItem = dataSet.get(i);
            mNewDatabaseForSiteLead.insertDataFromSbgFeedback(sbgItem);
        }
    }
}