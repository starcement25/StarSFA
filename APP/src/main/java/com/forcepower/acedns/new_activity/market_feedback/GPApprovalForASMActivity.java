package com.forcepower.acedns.new_activity.market_feedback;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerMarketFeedbackDetailsDataSet;
import com.forcepower.acedns.new_activity.market_feedback.adapter.GPApprovalItemAdapter;
import com.forcepower.acedns.new_activity.market_feedback.data.GPApprovalItem;

import java.util.ArrayList;
import java.util.List;
import com.forcepower.acedns.new_activity.market_feedback.api_connect.GramPanchayatApproveApi;

import org.json.JSONObject;

public class GPApprovalForASMActivity extends AceDnsParentActivity implements View.OnClickListener {
    private Context mContext;
    private Button backButton;
    private LinearLayout pendingBtn, approvedBtn, notSubmittedBtn;
    private LinearLayout stateLayout,districtLayout,blockLayout,gramPanchayatLayout,actionLayout,empCodeLayout,empNameLayout;
    private RecyclerView gpDataList;
    private static final int PAGE_SIZE = 100;
    private int currentOffset = 0;
    private boolean isLoading = false;
    private boolean hasMoreData = true;
    ArrayList<GPApprovalItem> list = new ArrayList<>();
    GPApprovalItemAdapter adapter;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    AceDnsDatabase aceDnsDatabase;
    int value=1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_gp_approval_for_asm_activity);

        init();
    }
    @Override
    public void onClick(View v) {
        if (backButton == v) {
            finish();
        }
        if (pendingBtn == v) {
            defaultData();
            pendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            value=1;
            adapter.setFilterType(value);
            resetAndLoad();
            filterWiseReset();
        }
        if (approvedBtn == v) {
            defaultData();
            approvedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            value=2;
            adapter.setFilterType(value);
            resetAndLoad();
            filterWiseReset();
        }
        if (notSubmittedBtn == v) {
            defaultData();
            notSubmittedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            value=3;
            adapter.setFilterType(value);
            resetAndLoad();
            filterWiseReset();
        }
    }
    private void defaultData(){
        pendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        approvedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        notSubmittedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
    }
    private void resetAndLoad() {
        int oldSize = list.size();
        list.clear();
        adapter.notifyItemRangeRemoved(0, oldSize);
        currentOffset = 0;
        hasMoreData = true;
        loadNextPage();
    }
    private void filterWiseReset(){
        if(value==1){
            stateLayout.setVisibility(VISIBLE);
            districtLayout.setVisibility(VISIBLE);
            blockLayout.setVisibility(VISIBLE);
            gramPanchayatLayout.setVisibility(VISIBLE);
            actionLayout.setVisibility(VISIBLE);
            empCodeLayout.setVisibility(VISIBLE);
            empNameLayout.setVisibility(VISIBLE);
        }else if(value==2){
            stateLayout.setVisibility(VISIBLE);
            districtLayout.setVisibility(VISIBLE);
            blockLayout.setVisibility(VISIBLE);
            gramPanchayatLayout.setVisibility(VISIBLE);
            actionLayout.setVisibility(VISIBLE);
            empCodeLayout.setVisibility(GONE);
            empNameLayout.setVisibility(GONE);
        }else {
            stateLayout.setVisibility(GONE);
            districtLayout.setVisibility(GONE);
            blockLayout.setVisibility(GONE);
            gramPanchayatLayout.setVisibility(GONE);
            actionLayout.setVisibility(GONE);
            empCodeLayout.setVisibility(VISIBLE);
            empNameLayout.setVisibility(VISIBLE);
        }
    }
    private void init() {
        mContext = GPApprovalForASMActivity.this;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
        aceDnsDatabase = new AceDnsDatabase(mContext);

        backButton = findViewById(R.id.backButton);
        gpDataList = findViewById(R.id.gpDataList);
        backButton.setOnClickListener(this);
        LinearLayoutManager layoutManager = new LinearLayoutManager(mContext);
        gpDataList.setLayoutManager(layoutManager);
        adapter = new GPApprovalItemAdapter(mContext, list, value, new GPApprovalItemAdapter.OnActionClickListener() {
            @SuppressLint("NotifyDataSetChanged")
            @Override
            public void approveItem(GPApprovalItem item, int position) {
                String customerCode = aceDnsDatabase.getCustomerCode(item.getCustomerCode());
                Log.d("TAG", "_DDDDD_ approveItem: "+customerCode);
                String empCode = Constants.employeeDetailObject.getEmpCode();
                GramPanchayatApproveApi.updateStatus(customerCode, "1", new GramPanchayatApproveApi.ApproveCallback() {
                    @Override
                    public void onSuccess(String message, JSONObject data) {
                        mNewDatabaseForSiteLead.updateDataFromCustomerMarketFeedbackDetailsStatus(empCode,item.getCustomerCode(), "1");
                        item.setApprovalStatus("1");
                        // The API response is asynchronous, so the clicked position
                        // may have changed if another row was removed first.
                        int currentPosition = list.indexOf(item);
                        if (currentPosition != -1) {
                            list.remove(currentPosition);
                            adapter.notifyItemRemoved(currentPosition);
                            adapter.notifyItemRangeChanged(
                                    currentPosition,
                                    list.size() - currentPosition
                            );

                            // Pending rows were removed from the database too. Keep
                            // pagination aligned so later pages are not skipped.
                            if (value == 1) {
                                currentOffset = list.size();
                            }
                        }
                        Toast.makeText(mContext, message, Toast.LENGTH_SHORT).show();
                    }
                    @Override
                    public void onFailure(String errorMessage) {
                        Toast.makeText(mContext, errorMessage, Toast.LENGTH_SHORT).show();
                    }
                });

            }

            @Override
            public void rejectItem(GPApprovalItem item, int position) {
                String customerCode = aceDnsDatabase.getCustomerCode(item.getCustomerCode());
                String empCode = Constants.employeeDetailObject.getEmpCode();
                GramPanchayatApproveApi.updateStatus(customerCode, "0", new GramPanchayatApproveApi.ApproveCallback() {
                    @Override
                    public void onSuccess(String message, JSONObject data) {
                        mNewDatabaseForSiteLead.rejectCustomerMarketFeedbackDetails(empCode, item.getCustomerCode());
                        int currentPosition = list.indexOf(item);
                        if (currentPosition != -1) {
                            list.remove(currentPosition);
                            adapter.notifyItemRemoved(currentPosition);
                            adapter.notifyItemRangeChanged(currentPosition, list.size() - currentPosition);
                            if (value == 1) {
                                currentOffset = list.size();
                            }
                        }
                        Toast.makeText(mContext, message, Toast.LENGTH_SHORT).show();
                    }

                    @Override
                    public void onFailure(String errorMessage) {
                        Toast.makeText(mContext, errorMessage, Toast.LENGTH_SHORT).show();
                    }
                });
            }
        });
        gpDataList.setAdapter(adapter);

        pendingBtn= findViewById(R.id.pendingBtn);
        approvedBtn= findViewById(R.id.approvedBtn);
        notSubmittedBtn= findViewById(R.id.notSubmittedBtn);
        pendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        approvedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        notSubmittedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        pendingBtn.setOnClickListener(this);
        approvedBtn.setOnClickListener(this);
        notSubmittedBtn.setOnClickListener(this);

        stateLayout= findViewById(R.id.stateLayout);
        districtLayout= findViewById(R.id.districtLayout);
        blockLayout= findViewById(R.id.blockLayout);
        gramPanchayatLayout= findViewById(R.id.gramPanchayatLayout);
        actionLayout= findViewById(R.id.actionLayout);
        empCodeLayout= findViewById(R.id.empCodeLayout);
        empNameLayout= findViewById(R.id.empNameLayout);

        try {
            gpDataList.addOnScrollListener(new RecyclerView.OnScrollListener() {
                @Override
                public void onScrolled(RecyclerView recyclerView, int dx, int dy) {
                    super.onScrolled(recyclerView, dx, dy);
                    if (dy <= 0 || isLoading || !hasMoreData) return;
                    int visibleItemCount = layoutManager.getChildCount();
                    int totalItemCount = layoutManager.getItemCount();
                    int firstVisibleItemPosition = layoutManager.findFirstVisibleItemPosition();
                    if ((visibleItemCount + firstVisibleItemPosition) >= totalItemCount - 10 && firstVisibleItemPosition >= 0) {
                        loadNextPage();
                    }
                }
            });
            loadNextPage();
        } catch (Exception e) {
            Log.d("TAG", "_DDDDD_ init: " + e.getMessage());
        }
    }
    private void loadNextPage() {
        isLoading = true;
        try {
            List<GPApprovalItem> newItems = mNewDatabaseForSiteLead.getCustomerGPDetails(currentOffset, PAGE_SIZE,value);
            Log.d("TAG", "_DDDDD_ loadNextPage: offset=" + currentOffset + " fetched=" + newItems.size());

            for (GPApprovalItem item : newItems) {
                String customerName = aceDnsDatabase.getCustomerName(item.getCustomerCode());
                String empCode = aceDnsDatabase.getRelatedEmpCode(item.getCustomerCode());
                String empName = aceDnsDatabase.getRelatedEmpName(item.getCustomerCode());
                item.setCustomerName(customerName);
                item.setEmpCode(empCode);
                item.setEmpName(empName);
            }

            if (newItems.isEmpty() || newItems.size() < PAGE_SIZE) {
                hasMoreData = false;
            }

            int insertStart = list.size();
            list.addAll(newItems);
            adapter.notifyItemRangeInserted(insertStart, newItems.size());

            currentOffset += newItems.size();
        } catch (Exception e) {
            Log.d("TAG", "_DDDDD_ loadNextPage: " + e.getMessage());
        } finally {
            isLoading = false;
        }
    }
}
