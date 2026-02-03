package com.forcepower.acedns.new_activity.declaration;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.text.Html;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.ComponentActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.new_activity.declaration.adapter.DeclarationAdapter;
import com.forcepower.acedns.new_activity.declaration.data_set.DeclarationItem;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

public class DeclarationListActivity extends ComponentActivity implements View.OnClickListener {
    Button backButton = null;
    RecyclerView recyclerView = null;
    LinearLayout declarationNoDataLayout = null;
    Context mContext;
    ArrayList<DeclarationItem> declarationList = new ArrayList<>();

    LinearLayout reasonPopupLayout;
    TextView textTitlePopupView;
    EditText editTextRejectReason;
    Button rejectButton;
    ImageView closePopupButton;

    DeclarationItem selectData = new DeclarationItem();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_declaration_list);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        init();
        mContext = DeclarationListActivity.this;
        if (HTTPUtils.isConnectionPossible(mContext)) {
            new TRANS_PendingDeclarationListAsynctask(DeclarationListActivity.this).execute();
        } else {
            Utils.showToast(mContext, "Could not fetch latest data. Please check your internet connection.");
        }
    }

    @Override
    public void onClick(View view) {
        if (view == backButton) {
            Intent intent = new Intent(mContext, MenuActivity.class);
            startActivity(intent);
        } else if (view == reasonPopupLayout) {

        } else if (view == rejectButton) {
            if (editTextRejectReason.getText().toString().trim().equalsIgnoreCase("")) {
                Toast.makeText(mContext, "Please type the reason of rejection.", Toast.LENGTH_LONG).show();
            } else {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    new TRANS_DeclarationRejectAsynctask(DeclarationListActivity.this, selectData).execute();
                } else {
                    Utils.showToast(mContext, "Could not fetch latest data. Please check your internet connection.");
                }
            }
        } else if (view == closePopupButton) {
            reasonPopupLayout.setVisibility(View.GONE);
        }
    }

    public void init() {
        backButton = findViewById(R.id.back);
        recyclerView = findViewById(R.id.declarationList);
        declarationNoDataLayout = findViewById(R.id.declarationNoDataLayout);
        backButton.setOnClickListener(this);

        // Popup View
        reasonPopupLayout = findViewById(R.id.reasonPopupLayout);
        textTitlePopupView = findViewById(R.id.textTitlePopupView);
        editTextRejectReason = findViewById(R.id.editTextRejectReason);
        rejectButton = findViewById(R.id.rejectButton);
        closePopupButton = findViewById(R.id.closePopupButton);
        reasonPopupLayout.setVisibility(View.GONE);

        reasonPopupLayout.setOnClickListener(this);
        rejectButton.setOnClickListener(this);
        closePopupButton.setOnClickListener(this);
    }

    public void showInList() {
        try {
            recyclerView.setLayoutManager(new LinearLayoutManager(this));
            DeclarationAdapter adapter = new DeclarationAdapter(this, declarationList, new DeclarationAdapter.OnActionClickListener() {
                @Override
                public void onApproveClicked(DeclarationItem item, int position) {
                    approveDeclaration(item);
                }

                @Override
                public void onRejectClicked(DeclarationItem item, int position) {
                    selectData = item;
                    runOnUiThread(() -> {
                        String month = switch (item.getMonth()) {
                            case "01" -> "January, " + item.getCurrentYear();
                            case "02" -> "February, " + item.getCurrentYear();
                            case "03" -> "March, " + item.getCurrentYear();
                            case "04" -> "April, " + item.getCurrentYear();
                            case "05" -> "May, " + item.getCurrentYear();
                            case "06" -> "June, " + item.getCurrentYear();
                            case "07" -> "July, " + item.getCurrentYear();
                            case "08" -> "August, " + item.getCurrentYear();
                            case "09" -> "September, " + item.getCurrentYear();
                            case "10" -> "October, " + item.getCurrentYear();
                            case "11" -> "November, " + item.getCurrentYear();
                            case "12" -> "December, " + item.getCurrentYear();
                            default -> "";
                        };
                        String text = "<font color='#000000'><b>" + item.getDealer_name() + " (" + item.getBranch() + ")" + "</b></font> " +
                                "<font color='#808080'>wants to get declaration approval for </font>" +
                                "<font color='#000000'><b>" + month + "</b></font><font color='#808080'>.</font>";
                        textTitlePopupView.setText(Html.fromHtml(text));
                        reasonPopupLayout.setVisibility(View.VISIBLE);
                    });
                }
            });
            recyclerView.setAdapter(adapter);
        } catch (Exception ignored) {
        }
    }

    private void approveDeclaration(DeclarationItem item) {
        if (HTTPUtils.isConnectionPossible(mContext)) {
            new TRANS_DeclarationApprovedAsynctask(DeclarationListActivity.this, item).execute();
        } else {
            Utils.showToast(mContext, "Could not fetch latest data. Please check your internet connection.");
        }
    }

    public void cleanAndReloadList() {
        runOnUiThread(() -> reasonPopupLayout.setVisibility(View.GONE));
        declarationList.clear();
        new TRANS_PendingDeclarationListAsynctask(DeclarationListActivity.this).execute();
    }

    public void noDataFound() {
        runOnUiThread(() -> declarationNoDataLayout.setVisibility(View.VISIBLE));
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_PendingDeclarationListAsynctask extends AsyncTask<String, Void, String> {
        ProgressDialog mStepProgressDialog;
        Context mContext;

        public TRANS_PendingDeclarationListAsynctask(Context context) {
            this.mContext = context;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Loading Data.\nPlease wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.devUrl + AceDnsWebServiceURL.declarationListUrl + "?emp_code=" + Constants.employeeDetailObject.getEmpCode();
                    POST_result = HttpCalling.httpGetCallWithTextResponse(url);
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            mStepProgressDialog.dismiss();
            try {
                JSONObject obj = new JSONObject(result);
                try {
                    if (obj.getString("process_status").equalsIgnoreCase("YES")) {
                        for (int i = 0; i < obj.getJSONArray("result").length(); i++) {
                            DeclarationItem item = new DeclarationItem();
                            JSONObject dataobj = obj.getJSONArray("result").getJSONObject(i);

                            if (obj.has("level") && !obj.isNull("level")) {
                                item.setMy_user_type(obj.getString("level"));
                            } else {
                                item.setMy_user_type("L3");
                            }

                            if (dataobj.has("id") && !dataobj.isNull("id")) {
                                item.setId(dataobj.getString("id"));
                            } else {
                                item.setId("");
                            }

                            if (dataobj.has("reject_reason") && !dataobj.isNull("reject_reason")) {
                                item.setRejectReason(dataobj.getString("reject_reason"));
                            } else {
                                item.setRejectReason("");
                            }

                            if (dataobj.has("customer_code") && !dataobj.isNull("customer_code")) {
                                item.setCustomer_code(dataobj.getString("customer_code"));
                            } else {
                                item.setCustomer_code("");
                            }

                            if (dataobj.has("customer_id") && !dataobj.isNull("customer_id")) {
                                item.setCustomer_id(dataobj.getString("customer_id"));
                            } else {
                                item.setCustomer_id("");
                            }

                            if (dataobj.has("month") && !dataobj.isNull("month")) {
                                item.setMonth(dataobj.getString("month"));
                            } else {
                                item.setMonth("");
                            }

                            if (dataobj.has("current_year") && !dataobj.isNull("current_year")) {
                                item.setCurrentYear(dataobj.getString("current_year"));
                            } else {
                                item.setCurrentYear("");
                            }

                            if (dataobj.has("dealer_name") && !dataobj.isNull("dealer_name")) {
                                item.setDealer_name(dataobj.getString("dealer_name"));
                            } else {
                                item.setDealer_name("");
                            }

                            if (dataobj.has("branch") && !dataobj.isNull("branch")) {
                                item.setBranch(dataobj.getString("branch"));
                            } else {
                                item.setBranch("");
                            }

                            if (dataobj.has("lifting_qty") && !dataobj.isNull("lifting_qty")) {
                                item.setLifting_qty(dataobj.getString("lifting_qty"));
                            } else {
                                item.setLifting_qty("");
                            }

                            if (dataobj.has("asm_approved_by") && !dataobj.isNull("asm_approved_by")) {
                                item.setAsm_approved_by(dataobj.getString("asm_approved_by"));
                            } else {
                                item.setAsm_approved_by("");
                            }

                            if (dataobj.has("asm_approve_status") && !dataobj.isNull("asm_approve_status")) {
                                item.setAsm_approve_status(dataobj.getString("asm_approve_status"));
                            } else {
                                item.setAsm_approve_status("");
                            }

                            if (dataobj.has("asm_approve_date") && !dataobj.isNull("asm_approve_date")) {
                                item.setAsm_approve_date(dataobj.getString("asm_approve_date"));
                            } else {
                                item.setAsm_approve_date("");
                            }

                            if (dataobj.has("rsm_approved_by") && !dataobj.isNull("rsm_approved_by")) {
                                item.setRsm_approved_by(dataobj.getString("rsm_approved_by"));
                            } else {
                                item.setRsm_approved_by("");
                            }

                            if (dataobj.has("rsm_approve_status") && !dataobj.isNull("rsm_approve_status")) {
                                item.setRsm_approve_status(dataobj.getString("rsm_approve_status"));
                            } else {
                                item.setRsm_approve_status("");
                            }

                            if (dataobj.has("rsm_approve_date") && !dataobj.isNull("rsm_approve_date")) {
                                item.setRsm_approve_date(dataobj.getString("rsm_approve_date"));
                            } else {
                                item.setRsm_approve_date("");
                            }

                            if (dataobj.has("cutoff_date") && !dataobj.isNull("cutoff_date")) {
                                item.setCutoff_date(dataobj.getString("cutoff_date"));
                            } else {
                                item.setCutoff_date("");
                            }

                            if (dataobj.has("ending_date") && !dataobj.isNull("ending_date")) {
                                item.setEnding_date(dataobj.getString("ending_date"));
                            } else {
                                item.setEnding_date("");
                            }

                            if (dataobj.has("status") && !dataobj.isNull("status")) {
                                item.setStatus(dataobj.getString("status"));
                            } else {
                                item.setStatus("");
                            }

                            if (dataobj.has("created_at") && !dataobj.isNull("created_at")) {
                                item.setCreated_at(dataobj.getString("created_at"));
                            } else {
                                item.setCreated_at("");
                            }

                            declarationList.add(item);
                        }
                        showInList();
                    } else {
                        noDataFound();
                    }
                } catch (JSONException ignored) {
                }
            } catch (Exception ignored) {
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_DeclarationApprovedAsynctask extends AsyncTask<String, Void, String> {
        ProgressDialog mStepProgressDialog;
        DeclarationItem item;
        Context mContext;

        public TRANS_DeclarationApprovedAsynctask(Context context, DeclarationItem item) {
            this.mContext = context;
            this.item = item;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Updating Data.\nPlease wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.devUrl + AceDnsWebServiceURL.declarationApproveRejectUrl;
                    JSONObject jo = new JSONObject();
                    jo.put("table_id", item.getId());
                    jo.put("dealer_id", item.getCustomer_id());
                    jo.put("emp_code", Constants.employeeDetailObject.getEmpCode());
                    jo.put("status", "Approve");

                    POST_result = HttpCalling.httpPostCallWithDecrypted(url, jo.toString()).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            mStepProgressDialog.dismiss();
            try {
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("NO")) {
                    Utils.showToast(mContext, "Could not update the declaration status.\nTry again some time later.");
                } else {
                    Utils.showToast(mContext, "Declaration approved successfully.");
                    cleanAndReloadList();
                }
            } catch (Exception ignored) {
            }
        }
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_DeclarationRejectAsynctask extends AsyncTask<String, Void, String> {
        ProgressDialog mStepProgressDialog;
        DeclarationItem item;
        Context mContext;

        public TRANS_DeclarationRejectAsynctask(Context context, DeclarationItem item) {
            this.mContext = context;
            this.item = item;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Updating Data.\nPlease wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = AceDnsWebServiceURL.devUrl + AceDnsWebServiceURL.declarationApproveRejectUrl;
                    JSONObject jo = new JSONObject();
                    jo.put("table_id", item.getId());
                    jo.put("dealer_id", item.getCustomer_id());
                    jo.put("emp_code", Constants.employeeDetailObject.getEmpCode());
                    jo.put("status", "Reject");
                    jo.put("reason", editTextRejectReason.getText().toString().trim());

                    POST_result = HttpCalling.httpPostCallWithDecrypted(url, jo.toString()).trim();
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            mStepProgressDialog.dismiss();
            try {
                JSONObject obj = new JSONObject(result);
                if (obj.getString("process_status").equalsIgnoreCase("NO")) {
                    Utils.showToast(mContext, "Could not update the declaration status.\nTry again some time later.");
                } else {
                    Utils.showToast(mContext, "Declaration reject successfully.");
                    cleanAndReloadList();
                }
            } catch (Exception ignored) {
            }
        }
    }
}