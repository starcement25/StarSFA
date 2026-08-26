package com.forcepower.acedns.new_activity.raise_issue;

import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.content.ContextCompat;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.new_activity.nt_quotation.activity.lead_quotation.LeadQuotationListActivity;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.LeadLogListAdapter;
import com.forcepower.acedns.new_activity.raise_issue.adapter.IssueHistoryAdapter;
import com.forcepower.acedns.new_activity.raise_issue.dataset.IssueHistoryData;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

public class RaiseIssueHistoryActivity extends AceDnsParentActivity implements View.OnClickListener {
    private Context mContext;
    private Button backButton;

    private LinearLayout pendingBtn,forwardedBtn,solvedBtn;
    private TextView pendingText,forwardedText,solvedText;
    private RecyclerView historyList;

    private ArrayList<IssueHistoryData> dataSets=new ArrayList<>();
    private ArrayList<IssueHistoryData> filterDataSets=new ArrayList<>();

    String status="pending";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_raise_issue_history);
        mContext=RaiseIssueHistoryActivity.this;
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
            status="forwarded";
            showData();
        }
        if(v==solvedBtn){
            status="resolved";
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

        new TRANS_IssueHistory_AsyncTask(mContext).execute();
        setDefault();
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
        if(status.equalsIgnoreCase("pending")){
            pendingBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.button_background));
            for(int i=0;i<dataSets.size();i++){
                if(dataSets.get(i).getStatus().equalsIgnoreCase("pending")){
                    filterDataSets.add(dataSets.get(i));
                }
            }
        }else if(status.equalsIgnoreCase("forwarded")){
            forwardedBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.button_background));
            for(int i=0;i<dataSets.size();i++){
                if(dataSets.get(i).getStatus().equalsIgnoreCase("forwarded")){
                    filterDataSets.add(dataSets.get(i));
                }
            }
        }else if(status.equalsIgnoreCase("resolved")){
            solvedBtn.setBackground(ContextCompat.getDrawable(mContext,R.drawable.button_background));
            for(int i=0;i<dataSets.size();i++){
                if(dataSets.get(i).getStatus().equalsIgnoreCase("resolved")){
                    filterDataSets.add(dataSets.get(i));
                }
            }
        }
        runOnUiThread(()->{
            historyList.setLayoutManager(new LinearLayoutManager(mContext));
            IssueHistoryAdapter adapter = new IssueHistoryAdapter(mContext, filterDataSets);
            historyList.setAdapter(adapter);
        });
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_IssueHistory_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String emp_code;

        public TRANS_IssueHistory_AsyncTask(Context context) {
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
                    String url = BaseUrl.baseUrl + "misreport/api_raise_issue_list.php?emp_code=" + emp_code;
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
                if(data.getString("process_status").equalsIgnoreCase("yes")){
                    JSONArray array = data.getJSONArray("data");

                    for (int i = 0; i < array.length(); i++) {
                        JSONObject object = array.getJSONObject(i);
                        IssueHistoryData dataSet = new IssueHistoryData();
                        dataSet.setEmpCode(object.getString("emp_code"));
                        dataSet.setEmpName(object.getString("emp_name"));
                        dataSet.setBranchCode(object.getString("branch_code"));
                        dataSet.setBranchName(object.getString("branch_name"));
                        dataSet.setCustomerCode(object.getString("customer_code"));
                        dataSet.setCustomerName(object.getString("customer_name"));
                        dataSet.setCategory(object.getString("category"));
                        dataSet.setState(object.getString("state"));
                        dataSet.setDistrict(object.getString("district"));
                        dataSet.setEscalatedDepartment(object.getString("esc_dept"));
                        dataSet.setIssue(object.getString("issue"));
                        dataSet.setStatus(object.getString("status"));
                        dataSet.setStatusChangeDate(object.getString("status_cnange_date"));
                        dataSet.setCreatedAt(object.getString("created_at"));
                        dataSets.add(dataSet);
                    }

                    showData();
                }
            } catch (Exception e) {
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
        }
    }
}