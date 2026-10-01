package com.forcepower.acedns.new_activity.rssd_reallocation;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.rssd_reallocation.adapter.AllocationAdapter;
import com.forcepower.acedns.new_activity.rssd_reallocation.adapter.PendingAllocationAdapter;
import com.forcepower.acedns.new_activity.rssd_reallocation.data.DataSet;
import com.forcepower.acedns.new_activity.rssd_reallocation.data.PendingDataSet;
import com.forcepower.acedns.new_activity.rssd_reallocation.data.PendingOrderDataSet;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.Map;

public class RssdReAllocationActivity extends AceDnsParentActivity implements View.OnClickListener {
    private Context mContext;
    private Button backButton;

    private LinearLayout pendingBtn,forwardedBtn,solvedBtn;
    private TextView pendingText,forwardedText,solvedText;
    private RecyclerView historyList;

    private ArrayList<DataSet> dataSets=new ArrayList<>();
    private ArrayList<DataSet> filterDataSets=new ArrayList<>();
    private final ArrayList<PendingOrderDataSet> pendingOrderDataSets = new ArrayList<>();
    String status="pending";
    AllocationAdapter adapter;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_rssd_re_allocation);
        mContext= RssdReAllocationActivity.this;
        init();
    }

    @Override
    public void onClick(View v) {
        if(v==backButton){
            finish();
        }
        if(v==pendingBtn){
            status="pending";
            showData();
        }
        if(v==forwardedBtn){
            status="approve";
            showData();
        }
        if(v==solvedBtn){
            status="reject";
            showData();
        }
    }

    private void init(){
        backButton=findViewById(R.id.backButton);
        backButton.setOnClickListener(this);
        historyList=findViewById(R.id.historyList);
        historyList.setLayoutManager(new LinearLayoutManager(this));
        pendingBtn=findViewById(R.id.pendingBtn);
        forwardedBtn=findViewById(R.id.forwardedBtn);
        solvedBtn=findViewById(R.id.solvedBtn);
        pendingText=findViewById(R.id.pendingText);
        forwardedText=findViewById(R.id.forwardedText);
        solvedText=findViewById(R.id.solvedText);
        pendingBtn.setOnClickListener(this);
        forwardedBtn.setOnClickListener(this);
        solvedBtn.setOnClickListener(this);

        new TRANS_Allocation_AsyncTask(mContext).execute();
    }

    private void setDefault(){
        pendingBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.disable_button_backgroud));
        forwardedBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.disable_button_backgroud));
        solvedBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.disable_button_backgroud));
        pendingText.setTextColor(ContextCompat.getColor(mContext,R.color.black));
        forwardedText.setTextColor(ContextCompat.getColor(mContext,R.color.black));
        solvedText.setTextColor(ContextCompat.getColor(mContext,R.color.black));
    }

    private void showData(){
        setDefault();
        filterDataSets.clear();
        if("pending".equalsIgnoreCase(status)){
            pendingBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.button_background));
            historyList.setAdapter(new PendingAllocationAdapter(mContext, pendingOrderDataSets,
                    new PendingAllocationAdapter.OnActionClickListener() {
                        @Override
                        public void clickApproved(PendingDataSet item) {
                            requestForOrderStatusChange(item.getId(), "approved");
                        }

                        @Override
                        public void clickRejected(PendingDataSet item) {
                            requestForOrderStatusChange(item.getId(), "reject");
                        }
                    }));
            return;
        }else if("approve".equalsIgnoreCase(status) || "approved".equalsIgnoreCase(status)){
            forwardedBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.button_background));
            for(int i=0;i<dataSets.size();i++){
                if("approved".equalsIgnoreCase(dataSets.get(i).getStatus()) || "approve".equalsIgnoreCase(dataSets.get(i).getStatus())){
                    filterDataSets.add(dataSets.get(i));
                }
            }
        }else if("reject".equalsIgnoreCase(status) || "rejected".equalsIgnoreCase(status)){
            solvedBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.button_background));
            for(int i=0;i<dataSets.size();i++){
                if("reject".equalsIgnoreCase(dataSets.get(i).getStatus()) || "rejected".equalsIgnoreCase(dataSets.get(i).getStatus())){
                    filterDataSets.add(dataSets.get(i));
                }
            }
        }
        runOnUiThread(()->{
            historyList.setLayoutManager(new LinearLayoutManager(mContext));
            adapter = new AllocationAdapter(mContext, filterDataSets,new AllocationAdapter.OnActionClickListener() {
                @Override
                public void clickApproved(DataSet item, int position) {
                    requestForOrderStatusChange(item,"approved");
                    filterDataSets.remove(position);
                    adapter.notifyDataSetChanged();
                }

                @Override
                public void clickRejected(DataSet item, int position) {
                    requestForOrderStatusChange(item,"reject");
                    filterDataSets.remove(position);
                    adapter.notifyDataSetChanged();
                }
            });
            historyList.setAdapter(adapter);
        });
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_Allocation_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String emp_code;

        public TRANS_Allocation_AsyncTask(Context context) {
            this.mContext = context;
            this.emp_code = Constants.employeeDetailObject.getEmpCode();
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = "https://sfa.starcement.co.in/api_get_reallocation_rqst_list.php?emp_code=" + emp_code;
                    POST_result = HttpCalling.httpGetCallWithTextResponse(url).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                JSONObject data = new JSONObject(result);
                if (data.optString("process_status", "").equalsIgnoreCase("yes")) {
                    JSONArray array = data.optJSONArray("data");
                    if (array != null) {
                        AceDnsDatabase database = null;
                        try {
                            database = new AceDnsDatabase(mContext);
                        } catch (Exception ignored) {
                        }
                        dataSets.clear();
                        pendingOrderDataSets.clear();
                        Map<String, PendingOrderDataSet> pendingOrders = new LinkedHashMap<>();
                        for (int i = 0; i < array.length(); i++) {
                            JSONObject object = array.getJSONObject(i);
                            if(object.optString("approval_status", "").equalsIgnoreCase("pending")){
                                String orderId = object.optString("order_id", object.optString("APPORDERNO", ""));
                                PendingOrderDataSet order = pendingOrders.get(orderId);
                                if (order == null) {
                                    order = new PendingOrderDataSet();
                                    order.setId(object.optString("history_id", object.optString("allocation_id", "")));
                                    order.setOrderNo(object.optString("APPORDERNO", orderId));
                                    order.setOrderDate(object.optString("date_and_time", object.optString("allocation_date", "")));
                                    order.setInvoiceNo(object.optString("inv_no", ""));
                                    order.setInvoiceDate(object.optString("inv_date", ""));
                                    order.setProductName(object.optString("prod_desc", ""));
                                    order.setQty(withMt(object.optString("old_qty", object.optString("order_qty", ""))));
                                    order.setStatus(object.optString("approval_status", ""));
                                    order.setRequestByCode(object.optString("customer_id",
                                            object.optString("created_by", object.optString("created_by_code", ""))));
                                    order.setRequestByName(object.optString("created_by_name",
                                            object.optString("request_by_name", object.optString("customer_name", ""))));
                                    pendingOrders.put(orderId, order);
                                }

                                PendingDataSet allocation = new PendingDataSet();
                                allocation.setId(object.optString("history_id", object.optString("allocation_id", "")));
                                String oldRssdCode = object.optString("old_sub_dealer_id", "");
                                String newRssdCode = object.optString("new_sub_dealer_id", "");
                                String oldRssdName = object.optString("old_sub_dealer_name",
                                        object.optString("old_rssd_name", object.optString("old_rsar_name", "")));
                                String newRssdName = object.optString("new_sub_dealer_name",
                                        object.optString("new_rssd_name", object.optString("new_rsar_name", "")));
                                oldRssdName = resolveRssdName(database, oldRssdCode, oldRssdName);
                                newRssdName = resolveRssdName(database, newRssdCode, newRssdName);
                                allocation.setOldRssdCode(oldRssdCode);
                                allocation.setNewRssdCode(newRssdCode);
                                allocation.setOldRssdName(oldRssdName);
                                allocation.setNewRssdName(newRssdName);
                                allocation.setQty(withMt(object.optString("new_qty", object.optString("old_qty", ""))));
                                allocation.setTotalAllocationQty(withMt(object.optString("total_allocation_qty",
                                        object.optString("total_allocated_qty", ""))));
                                order.getPendingDataSets().add(allocation);
                            }else{
                                DataSet dataSet = new DataSet();
                                dataSet.setId(object.optString("history_id", object.optString("allocation_id", "")));
                                dataSet.setOldRssdCode(object.optString("old_sub_dealer_id", ""));
                                dataSet.setNewRssdCode(object.optString("new_sub_dealer_id", ""));
                                dataSet.setOldRssdName(object.optString("old_sub_dealer_name", ""));
                                dataSet.setNewRssdName(object.optString("new_sub_dealer_name", ""));
                                dataSet.setQty(object.optString("new_qty", object.optString("old_qty", ""))+" MT");
                                dataSet.setProductName(object.optString("prod_desc", ""));
                                dataSet.setProductCode(object.optString("dns_prod_code", ""));
                                dataSet.setInvoiceDate(object.optString("inv_date", ""));
                                dataSet.setInvoiceNo(object.optString("inv_no", ""));
                                dataSet.setRequestedAt(object.optString("logged_at", object.optString("date_and_time", "")));
                                dataSet.setStatus(object.optString("approval_status", ""));
                                dataSet.setOrderNo(object.optString("APPORDERNO", object.optString("order_id", "")));
                                String requestDealerCode = object.optString("customer_id",
                                        object.optString("created_by", object.optString("created_by_code", "")));
                                String requestDealerName = object.optString("created_by_name",
                                        object.optString("request_by_name", object.optString("customer_name", "")));
                                dataSet.setRequestByCode(requestDealerCode);
                                dataSet.setRequestByName(resolveRssdName(database, requestDealerCode, requestDealerName));
                                dataSet.setOrderDate(object.optString("date_and_time", object.optString("allocation_date", "")));
                                dataSets.add(dataSet);
                            }
                        }
                        pendingOrderDataSets.addAll(pendingOrders.values());
                    }
                    showData();
                }
            } catch (Exception e) {
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }

    private static String withMt(String qty) {
        return qty == null || qty.trim().isEmpty() ? "" : qty.trim() + " MT";
    }

    private static String resolveRssdName(AceDnsDatabase database, String code, String apiName) {
        if (apiName != null && !apiName.trim().isEmpty()) return apiName;
        if (database == null || code == null || code.trim().isEmpty()) return "";
        try {
            return database.getCustomerName(code);
        } catch (Exception ignored) {
            return "";
        }
    }

    public void requestForOrderStatusChange(DataSet item, String status) {
        if (item == null) return;
        requestForOrderStatusChange(item.getId(), status);
    }

    private void requestForOrderStatusChange(String historyId, String status) {
        if (historyId == null || historyId.trim().isEmpty()) return;
        String action = (status != null && (status.equalsIgnoreCase("approved") || status.equalsIgnoreCase("approve"))) ? "approve" : "reject";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            new TRANS_UpdateAllocationStatus_AsyncTask(mContext, historyId, action).execute();
        } else {
            Toast.makeText(mContext, "Could not connect to internet. Please check your connection.", Toast.LENGTH_SHORT).show();
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_UpdateAllocationStatus_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String historyId;
        String action;
        String emp_code;
        ProgressDialog progressDialog;

        public TRANS_UpdateAllocationStatus_AsyncTask(Context context, String historyId, String action) {
            this.mContext = context;
            this.historyId = historyId;
            this.action = action;
            this.emp_code=Constants.employeeDetailObject.getEmpCode();
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            try {
                progressDialog = new ProgressDialog(mContext);
                progressDialog.setMessage("Updating status. Please wait...");
                progressDialog.setCancelable(false);
                progressDialog.show();
            } catch (Exception ignored) {
            }
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = "https://sfa.starcement.co.in/api_approve_reallocation_reqst.php";
                    ContentValues values = new ContentValues();
                    values.put("emp_code", emp_code);
                    values.put("history_id", historyId);
                    values.put("action", action);
                    POST_result = HttpCalling.httpPostCallWithXmlResponse(url, values).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                if (progressDialog != null && progressDialog.isShowing()) {
                    progressDialog.dismiss();
                }
            } catch (Exception ignored) {
            }

            try {
                JSONObject data = new JSONObject(result);
                String processStatus = data.optString("process_status", "");
                String message = data.optString("process_message", "");
                Log.d("TAG", "onPostExecute: "+message);
                if (processStatus.equalsIgnoreCase("yes") || processStatus.equalsIgnoreCase("true")) {
                    Toast.makeText(mContext, !message.isEmpty() ? message : "Status updated successfully.", Toast.LENGTH_LONG).show();
                } else {
                    Toast.makeText(mContext, !message.isEmpty() ? message : "Failed to update status.", Toast.LENGTH_LONG).show();
                }
            } catch (Exception e) {
                Log.d("TAG", "TRANS_UpdateAllocationStatus onPostExecute: " + e.getMessage());
                Log.d("TAG", "TRANS_UpdateAllocationStatus onPostExecute: " + result);
                Toast.makeText(mContext, "Failed to update status.", Toast.LENGTH_LONG).show();
            }

            new TRANS_Allocation_AsyncTask(mContext).execute();
        }
    }
}
