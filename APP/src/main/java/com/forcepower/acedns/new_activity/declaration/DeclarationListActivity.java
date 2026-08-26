package com.forcepower.acedns.new_activity.declaration;

import static android.view.View.GONE;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.text.Html;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.ComponentActivity;
import androidx.annotation.RequiresApi;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.new_activity.declaration.adapter.DeclarationAdapter;
import com.forcepower.acedns.new_activity.declaration.data_set.DeclarationItem;
import com.forcepower.acedns.new_activity.nt_quotation.adapter.ShowDataSetAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet;
import com.forcepower.acedns.new_activity.sitelead.helper.FYMonthList;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class DeclarationListActivity extends ComponentActivity implements View.OnClickListener {
    Button backButton = null;
    RecyclerView recyclerView = null;
    LinearLayout declarationNoDataLayout = null;
    Context mContext;
    ArrayList<DeclarationItem> declarationList = new ArrayList<>();
    ArrayList<DeclarationItem> filterDeclarationList = new ArrayList<>();
    ArrayList<DeclarationItem> searchAndFilterDeclarationList = new ArrayList<>();

    LinearLayout reasonPopupLayout;
    TextView textTitlePopupView;
    EditText editTextRejectReason;
    Button rejectButton;
    ImageView closePopupButton;

    DeclarationItem selectData = new DeclarationItem();


    // New function
    private TextView textViewMonthName;
    private EditText editTextSearch;
    private LinearLayout declarationPendingBtn, declarationApprovedBtn, declarationRejectedBtn;

    String type = "pending";

    ArrayList<DataSet> monthList = new ArrayList<>();

    @RequiresApi(api = Build.VERSION_CODES.O)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_declaration_list);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        mContext = DeclarationListActivity.this;
        init();
        if (HTTPUtils.isConnectionPossible(mContext)) {
            new TRANS_PendingDeclarationListAsynctask(DeclarationListActivity.this).execute();
        } else {
            Utils.showToast(mContext, "Could not fetch latest data. Please check your internet connection.");
        }
    }

    @Override
    public void onClick(View view) {
        if (view == backButton) {
            finish();
        }
        if (view == reasonPopupLayout) {
        }
        if (view == rejectButton) {
            if (editTextRejectReason.getText().toString().trim().equalsIgnoreCase("")) {
                Toast.makeText(mContext, "Please type the reason of rejection.", Toast.LENGTH_LONG).show();
            } else {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    new TRANS_DeclarationRejectAsynctask(DeclarationListActivity.this, selectData).execute();
                } else {
                    Utils.showToast(mContext, "Could not fetch latest data. Please check your internet connection.");
                }
            }
        }
        if (view == closePopupButton) {
            reasonPopupLayout.setVisibility(View.GONE);
        }

        if (view == textViewMonthName) {
            show_list_data_dialog(monthList);
        }
        if (view == declarationPendingBtn) {
            type = "pending";
            declarationPendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            declarationApprovedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            declarationRejectedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            showInList();
        }
        if (view == declarationApprovedBtn) {
            type = "approved";
            declarationPendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            declarationApprovedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            declarationRejectedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            showInList();
        }
        if (view == declarationRejectedBtn) {
            type = "rejected";
            declarationPendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            declarationApprovedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
            declarationRejectedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
            showInList();
        }
    }

    @RequiresApi(api = Build.VERSION_CODES.O)
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

        textViewMonthName = findViewById(R.id.textViewMonthName);

        editTextSearch = findViewById(R.id.editTextSearch);

        declarationPendingBtn = findViewById(R.id.declarationPendingBtn);
        declarationApprovedBtn = findViewById(R.id.declarationApprovedBtn);
        declarationRejectedBtn = findViewById(R.id.declarationRejectedBtn);

        TextView declarationPendingText = findViewById(R.id.declarationPendingText);
        TextView declarationApprovedText = findViewById(R.id.declarationApprovedText);
        TextView declarationRejectedText = findViewById(R.id.declarationRejectedText);

        textViewMonthName.setOnClickListener(this);
        declarationPendingBtn.setOnClickListener(this);
        declarationApprovedBtn.setOnClickListener(this);
        declarationRejectedBtn.setOnClickListener(this);

        declarationPendingText.setTextColor(Color.argb(255, 0, 0, 0));
        declarationApprovedText.setTextColor(Color.argb(255, 0, 0, 0));
        declarationRejectedText.setTextColor(Color.argb(255, 0, 0, 0));

        declarationPendingBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.button_background));
        declarationApprovedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));
        declarationRejectedBtn.setBackground(ContextCompat.getDrawable(mContext, R.drawable.disable_button_backgroud));

        List<String> arr = FYMonthList.getCurrentAndPreviousFYMonths();
        DataSet a = new DataSet();
        a.setId("All");
        a.setValue("All");
        monthList.add(a);
        for (int i = 0; i < arr.size(); i++) {
            DataSet dataSet = new DataSet();
            dataSet.setId(arr.get(i));
            dataSet.setValue(arr.get(i));
            monthList.add(dataSet);
        }

        editTextSearch.addTextChangedListener(new android.text.TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
            }

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                showInList();
            }

            @Override
            public void afterTextChanged(android.text.Editable s) {
            }
        });
    }

    public void showInList() {
        try {
            filterDeclarationList.clear();
            searchAndFilterDeclarationList.clear();

            // Step 1: Filter by status (pending / approved / rejected)
            for (int i = 0; i < declarationList.size(); i++) {
                if (declarationList.get(i).getStatus().equalsIgnoreCase(type)) {
                    filterDeclarationList.add(declarationList.get(i));
                }
            }

            // Step 2: Filter by month (if not "All")
            if (!textViewMonthName.getText().toString().trim().equalsIgnoreCase("all")) {
                String month = switch (textViewMonthName.getText().toString().trim().split(",")[0].trim().toLowerCase()) {
                    case "january" -> "01";
                    case "february" -> "02";
                    case "march" -> "03";
                    case "april" -> "04";
                    case "may" -> "05";
                    case "june" -> "06";
                    case "july" -> "07";
                    case "august" -> "08";
                    case "september" -> "09";
                    case "october" -> "10";
                    case "november" -> "11";
                    case "december" -> "12";
                    default -> "";
                };
                String year = textViewMonthName.getText().toString().trim().split(",")[1].trim();
                for (int i = 0; i < filterDeclarationList.size(); i++) {
                    if (filterDeclarationList.get(i).getMonth().equalsIgnoreCase(month)
                            && filterDeclarationList.get(i).getCurrentYear().equalsIgnoreCase(year)) {
                        searchAndFilterDeclarationList.add(filterDeclarationList.get(i));
                    }
                }
            } else {
                searchAndFilterDeclarationList.addAll(filterDeclarationList);
            }

            // Step 3: Filter by search text
            String query = editTextSearch.getText().toString().trim().toLowerCase();
            ArrayList<DeclarationItem> finalList = new ArrayList<>();
            if (query.isEmpty())
                finalList.addAll(searchAndFilterDeclarationList);
            else {
                for (DeclarationItem item : searchAndFilterDeclarationList) {
                    if ((item.getDealer_name() != null && item.getDealer_name().toLowerCase().contains(query)) ||
                            (item.getBranch() != null && item.getBranch().toLowerCase().contains(query)) ||
                            (item.getCustomer_code() != null && item.getCustomer_code().toLowerCase().contains(query))) {
                        finalList.add(item);
                    }
                }
            }

            // Step 4: Render
            recyclerView.setLayoutManager(new LinearLayoutManager(this));
            DeclarationAdapter adapter = new DeclarationAdapter(this, finalList, new DeclarationAdapter.OnActionClickListener() {
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
                        String text = "<font color='#000000'><b>" + item.getDealer_name() + " (" + item.getBranch() + ")</b></font> " +
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
                textViewMonthName.setText(dataSet.get(position).getValue());
                showInList();
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
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
                    Log.d("TAG", "_DDD_ doInBackground: " + url);
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

                            if (obj.has("level") && !obj.isNull("level"))
                                item.setMy_user_type(obj.getString("level"));
                            else
                                item.setMy_user_type("L3");

                            if (dataobj.has("id") && !dataobj.isNull("id"))
                                item.setId(dataobj.getString("id"));
                            else
                                item.setId("");

                            if (dataobj.has("reject_reason") && !dataobj.isNull("reject_reason"))
                                item.setRejectReason(dataobj.getString("reject_reason"));
                            else
                                item.setRejectReason("");

                            if (dataobj.has("customer_code") && !dataobj.isNull("customer_code"))
                                item.setCustomer_code(dataobj.getString("customer_code"));
                            else
                                item.setCustomer_code("");

                            if (dataobj.has("customer_id") && !dataobj.isNull("customer_id"))
                                item.setCustomer_id(dataobj.getString("customer_id"));
                            else
                                item.setCustomer_id("");

                            if (dataobj.has("month") && !dataobj.isNull("month"))
                                item.setMonth(dataobj.getString("month"));
                            else
                                item.setMonth("");

                            if (dataobj.has("current_year") && !dataobj.isNull("current_year"))
                                item.setCurrentYear(dataobj.getString("current_year"));
                            else
                                item.setCurrentYear("");

                            if (dataobj.has("dealer_name") && !dataobj.isNull("dealer_name"))
                                item.setDealer_name(dataobj.getString("dealer_name"));
                            else
                                item.setDealer_name("");

                            if (dataobj.has("branch") && !dataobj.isNull("branch"))
                                item.setBranch(dataobj.getString("branch"));
                            else
                                item.setBranch("");

                            if (dataobj.has("lifting_qty") && !dataobj.isNull("lifting_qty"))
                                item.setLifting_qty(dataobj.getString("lifting_qty"));
                            else
                                item.setLifting_qty("");

                            if (dataobj.has("asm_approved_by") && !dataobj.isNull("asm_approved_by"))
                                item.setAsm_approved_by(dataobj.getString("asm_approved_by"));
                            else
                                item.setAsm_approved_by("");

                            if (dataobj.has("asm_approve_status") && !dataobj.isNull("asm_approve_status"))
                                item.setAsm_approve_status(dataobj.getString("asm_approve_status"));
                            else
                                item.setAsm_approve_status("");

                            if (dataobj.has("asm_approve_date") && !dataobj.isNull("asm_approve_date"))
                                item.setAsm_approve_date(dataobj.getString("asm_approve_date"));
                            else
                                item.setAsm_approve_date("");

                            if (dataobj.has("rsm_approved_by") && !dataobj.isNull("rsm_approved_by"))
                                item.setRsm_approved_by(dataobj.getString("rsm_approved_by"));
                            else
                                item.setRsm_approved_by("");

                            if (dataobj.has("rsm_approve_status") && !dataobj.isNull("rsm_approve_status"))
                                item.setRsm_approve_status(dataobj.getString("rsm_approve_status"));
                            else
                                item.setRsm_approve_status("");

                            if (dataobj.has("rsm_approve_date") && !dataobj.isNull("rsm_approve_date"))
                                item.setRsm_approve_date(dataobj.getString("rsm_approve_date"));
                            else
                                item.setRsm_approve_date("");

                            if (dataobj.has("cutoff_date") && !dataobj.isNull("cutoff_date"))
                                item.setCutoff_date(dataobj.getString("cutoff_date"));
                            else
                                item.setCutoff_date("");

                            if (dataobj.has("ending_date") && !dataobj.isNull("ending_date"))
                                item.setEnding_date(dataobj.getString("ending_date"));
                            else
                                item.setEnding_date("");

                            if (dataobj.has("status") && !dataobj.isNull("status"))
                                item.setStatus(dataobj.getString("status"));
                            else
                                item.setStatus("");

                            if (dataobj.has("created_at") && !dataobj.isNull("created_at"))
                                item.setCreated_at(dataobj.getString("created_at"));
                            else
                                item.setCreated_at("");

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