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
import android.widget.ImageView;
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

public class MarketFeedbackSBGStockConfirmationActivity extends AceDnsParentActivity implements View.OnClickListener,MarketFeedbackSBGStockAuditAdapter.OnQuantityChangedListener {

    Context mContext;
    private Button backButton, buttonSubmitFeedback;
    private ListView listViewProduct;
    private TextView textViewTotalCount, textViewCustomerName, textViewCustomerType, textViewRouteName,textViewCustomerCode,textViewUniverseType;
    private NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    private AceDnsDatabase mAceDnsDatabase;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private ArrayList<CustomerCompetitorQuantityDataSet> dataSet;
    ArrayList<RoutePlanMasterDetails> mRoutePlanListofToday;
    RoutePlanMasterDetails mSelectedTodayRouteDetails;
    ArrayList<DataSet> universeTypeList;
    private String totalValue;
    String customerDNSCode="";
    ProgressDialog loader;
    Handler mHandler;
    MarketFeedbackSBGStockAuditAdapter adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_market_feedback_sbgstock_confirmation);

        mContext = MarketFeedbackSBGStockConfirmationActivity.this;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);


        try {
            Log.d("TAG", "_DOWNLOAD_ onCreate: "+PreferenceData.getCheckInOutEmpCode(mContext));
            dataSet = mNewDatabaseForSiteLead.getAllCustomerCompetitorQuantity(PreferenceData.getCheckInOutEmpCode(mContext));
            int total=0;
            for(int i=0;i<dataSet.size();i++){
                total+=Integer.parseInt(dataSet.get(i).getQuantity());
                customerDNSCode=dataSet.get(i).getCustomerDnsCode();
            }

            totalValue=String.valueOf(total);
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
            if(textViewUniverseType.getText().toString().trim().equalsIgnoreCase("Select Universe Type")){
                Toast.makeText(mContext, "Please select the universe type.", Toast.LENGTH_SHORT).show();
            }else{
                saveMarketFeedbackStockAuditToDatabase();
            }
        }
        if(v==textViewUniverseType){
            show_list_data_dialog(universeTypeList);
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        buttonSubmitFeedback = findViewById(R.id.buttonSubmitFeedback);
        listViewProduct = findViewById(R.id.listViewProduct);
        textViewTotalCount = findViewById(R.id.textViewTotalCount);
        textViewCustomerName = findViewById(R.id.textViewCustomerName);
        textViewCustomerType = findViewById(R.id.textViewCustomerType);
        textViewRouteName = findViewById(R.id.textViewRouteName);
        textViewCustomerCode=findViewById(R.id.textViewCustomerCode);

        backButton.setOnClickListener(this);
        buttonSubmitFeedback.setOnClickListener(this);

        sortDataSet(dataSet);
        adapter = new MarketFeedbackSBGStockAuditAdapter(mContext, R.layout.market_feedback_sbg_confirm_child, dataSet,this::onQuantityChanged);
        listViewProduct.setAdapter(adapter);
        listViewProduct.post(() -> setListViewHeightBasedOnChildren(listViewProduct));
        textViewCustomerName.setText(Constants.selectedCustomer.getCustomerName());
        textViewCustomerType.setText(Constants.selectedCustomer.getCustomerType());
//        textViewRouteName.setText(mRoutePlanListofToday.get(0).getRouteName());
        textViewRouteName.setText(mSelectedTodayRouteDetails.getRouteName());
        textViewCustomerCode.setText(customerDNSCode);
        textViewTotalCount.setText(totalValue);

        textViewUniverseType=findViewById(R.id.textViewUniverseType);
            textViewUniverseType.setText("Select Universe Type");
        textViewUniverseType.setOnClickListener(this);

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
    public void show_list_data_dialog(ArrayList<DataSet> dataSet) {
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
                textViewUniverseType.setText(dataSet.get(position).getValue());
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
                {"Star - RSAR","Star - RSAR"},
                {"Non Star - Non link Sub Dealer","Non Star - Non link Sub Dealer"},
                {"Non Star - Star Link Sub Dealer","Non Star - Star Link Sub Dealer"}
        };

        for (String[] entry : data) {
            universeTypeList.add(new DataSet(entry[0], entry[1], false));
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

        switch (mandatory) {
            case "Y": return 1; // Mandatory first
            case "N": return 2; // Non-mandatory second
            default:  return 3; // Anything else last
        }
    }
    private void setupHandler() {
        mHandler = new Handler(Looper.getMainLooper()) {
            @Override
            public void handleMessage(@NonNull Message msg) {}
        };
    }

    // Called by the adapter every time an EditText changes
    @Override
    public void onQuantityChanged() {
        int total = 0;
        for (CustomerCompetitorQuantityDataSet item : dataSet) {
            try {
                total += Integer.parseInt(item.getQuantity());
            } catch (NumberFormatException ignored) {}
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

            // dataSet is already up-to-date — pass it to your API task
            new TRANS_CompetitorPotentialFeedbackTask(mContext,textViewUniverseType.getText().toString().trim(),new TRANS_CompetitorPotentialFeedbackTask.OnTaskCompleteListener() {
                @Override
                public void onSuccess() {
                    // handle success — e.g. finish activity, show toast
                    runOnUiThread(() -> {
                        if (loader != null && loader.isShowing()) loader.dismiss();
                        Toast.makeText(mContext, "Submitted successfully", Toast.LENGTH_SHORT).show();
                        PreferenceData.setAddSBG(mContext,"1");
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
            Log.d("TAG", "_DOWNLOAD_ updateSbgFeedbackQuantitiesInDb: "+i);
            CustomerCompetitorQuantityDataSet sbgItem = dataSet.get(i);
            mNewDatabaseForSiteLead.insertDataFromSbgFeedback(sbgItem);
        }
    }
}