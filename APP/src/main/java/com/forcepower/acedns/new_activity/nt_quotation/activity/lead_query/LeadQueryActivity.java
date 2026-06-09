package com.forcepower.acedns.new_activity.nt_quotation.activity.lead_query;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.GridView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.adapter.MenuAdapter;
import com.forcepower.acedns.bean.MenuObj;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import org.json.JSONArray;

import java.util.ArrayList;

public class LeadQueryActivity extends AceDnsParentActivity implements View.OnClickListener {
    Context mContext;
    private Button backButton;
    private GridView gridMenu;

    ArrayList<MenuObj> mMenuList = new ArrayList<>();
    MenuAdapter mMenuAdapter;
    ProgressDialog progressDialog;

    // ==================== Override Function ==================== //
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_query);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        mContext = LeadQueryActivity.this;
        init();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
    }
    // ======================================== //


    // ==================== init Function ==================== //
    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        gridMenu = findViewById(R.id.gridMenu);
        new TRANS_EmployeeDetails_AsyncTask(mContext).execute();
    }
    // ======================================== //


    // ==================== Loader Dialog ==================== //
    // Progress Dialog open
    private void progressDialogOpen() {
        progressDialog = new ProgressDialog(mContext);
        progressDialog.setMessage("Wait for a while ...");
        progressDialog.setCancelable(false);
        progressDialog.show();
    }

    // Progress Dialog close
    private void progressDialogClose() {
        ((LeadQueryActivity) mContext).runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }
    // ======================================== //


    // ==================== API Calling ==================== //
    @SuppressLint("StaticFieldLeak")
    public class TRANS_EmployeeDetails_AsyncTask extends AsyncTask<String, Void, String> {
        Context mContext;
        String emp_code;

        public TRANS_EmployeeDetails_AsyncTask(Context context) {
            this.mContext = context;
            this.emp_code = Constants.employeeDetailObject.getEmpCode();
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            progressDialogOpen();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.sbDevUrl + "api/employee/?emp_code=" + emp_code;
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
                JSONArray arr = new JSONArray(result);
                if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt_to")) {
                    menuShowHOS();
                } else if (arr.getJSONObject(0).getString("level").equalsIgnoreCase("nt")) {
                    menuShowSO();
                } else {
                    ((LeadQueryActivity) mContext).runOnUiThread(LeadQueryActivity.this::finish);
                }
            } catch (Exception e) {
                Toast.makeText(mContext, "Please Synchronize Data.", Toast.LENGTH_LONG).show();
            }
            progressDialogClose();
        }
    }
    // ======================================== //


    // ==================== Menu Show ==================== //
    private void menuShowSO() {
        MenuObj menuObj = new MenuObj();
        menuObj.setResourceId(R.drawable.query_icon);
        menuObj.setFeatureName("Query");
        mMenuList.add(menuObj);

        MenuObj menuObj1 = new MenuObj();
        menuObj1.setResourceId(R.drawable.lead_icon);
        menuObj1.setFeatureName("Lead");
        mMenuList.add(menuObj1);

        menuSetup();
    }

    private void menuShowHOS() {
        MenuObj menuObj1 = new MenuObj();
        menuObj1.setResourceId(R.drawable.lead_icon);
        menuObj1.setFeatureName("Lead");
        mMenuList.add(menuObj1);

        menuSetup();
    }

    private void menuSetup() {
        mMenuAdapter = new MenuAdapter(LeadQueryActivity.this, R.layout.grid_child, mMenuList);
        gridMenu.setAdapter(mMenuAdapter);

        gridMenu.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            try {
                if (mMenuList.get(arg2).getFeatureName().equalsIgnoreCase("lead")) {
                    Intent intent;
                    intent = new Intent(mContext, LeadGenerationActivity.class);
                    intent.putExtra("SUBMENU", "Lead Generation");
                    startActivity(intent);
                } else {
                    Intent intent;
                    intent = new Intent(mContext, QueryGenerationActivity.class);
                    intent.putExtra("SUBMENU", "Lead Generation");
                    startActivity(intent);
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ menuSetup: " + e.getMessage());
            }
        });
    }
    // ======================================== //
}