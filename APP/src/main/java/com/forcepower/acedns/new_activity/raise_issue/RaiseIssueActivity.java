package com.forcepower.acedns.new_activity.raise_issue;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.ocr.adapter.ShowDataForBranchAdapter;
import com.forcepower.acedns.new_activity.ocr.adapter.ShowDataForDataAdapter;
import com.forcepower.acedns.new_activity.ocr.adapter.ShowDataForDealerAdapter;
import com.forcepower.acedns.new_activity.sitelead.dataset.DataSet;

import org.json.JSONObject;

import java.util.ArrayList;

import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class RaiseIssueActivity extends AceDnsParentActivity implements View.OnClickListener {

    private Context mContext;
    private Button backButton, saveButton;
    private Button branchButton, categoryButton, dealerSubDealerButton, escalatedDepartmentButton;
    private TextView branchText, categoryText, dealerSubDealerText, escalatedDepartmentText;
    private TextView stateText, districtText,issueDescriptionText;
    private EditText stateEditText, districtEditText, issueDescriptionEditText;

    public AceDnsDatabase mAceDnsDatabase;
    private ArrayList<KeyValue> mKeyValueList;
    private ArrayList<CustomerDetails> mCustomerDetailsList;
    private ArrayList<DataSet> mCustomerTypeList;
    private ArrayList<DataSet> mEscalatedDepartmentList;

    String branchCode="",branchName="";
    String custType="";
    String customerCode="",customerName="";
    String escalatedDepartment="";
    ProgressDialog progressDialog;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_raise_issue);

        mContext=RaiseIssueActivity.this;
        init();
        mAceDnsDatabase = new AceDnsDatabase(mContext);
    }

    @Override
    public void onClick(View v) {
        if(backButton==v){
            finish();
            return;
        }
        if(saveButton==v){
            if(branchCode.isEmpty()){
                Toast.makeText(mContext, "Please select Branch", Toast.LENGTH_SHORT).show();
            }else if(custType.isEmpty()){
                Toast.makeText(mContext, "Please select Category", Toast.LENGTH_SHORT).show();
            }else if(customerCode.isEmpty()){
                Toast.makeText(mContext, "Please select Customer", Toast.LENGTH_SHORT).show();
            } else if (stateEditText.getText().toString().trim().isEmpty()) {
                Toast.makeText(mContext, "Please enter State", Toast.LENGTH_SHORT).show();
            } else if (districtEditText.getText().toString().trim().isEmpty()) {
                Toast.makeText(mContext, "Please enter District", Toast.LENGTH_SHORT).show();
            } else if(escalatedDepartment.isEmpty()){
                Toast.makeText(mContext, "Please select Escalated Department", Toast.LENGTH_SHORT).show();
            } else if (issueDescriptionEditText.getText().toString().trim().isEmpty()) {
                Toast.makeText(mContext, "Please enter Issue Description", Toast.LENGTH_SHORT).show();
            }else {
                requestForRaiseIssue();
            }
        }
        if(branchButton==v){
            mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7("branch_master", "branch_code", "branch_name", "", "");
            showListDataForBranchDialog(mKeyValueList);
            return;
        }
        if(categoryButton==v){
            mCustomerTypeList= mAceDnsDatabase.getCustomerTypeForRaiseIssue("SC023");
            showListDataForTypeDialog(mCustomerTypeList,"category");
            return;
        }
        if(dealerSubDealerButton==v){
            mCustomerDetailsList = mAceDnsDatabase.getCustomerListForRaiseIssue(branchCode,custType);
            showListDataForDealerDialog(mCustomerDetailsList);
            return;
        }
        if(escalatedDepartmentButton==v){
            mEscalatedDepartmentList= mAceDnsDatabase.getCustomerTypeForRaiseIssue("SC024");
            showListDataForTypeDialog(mEscalatedDepartmentList,"escalated_department");
            return;
        }
    }

    private void init(){
        backButton=findViewById(R.id.backButton);
        backButton.setOnClickListener(this);
        saveButton=findViewById(R.id.saveButton);
        saveButton.setOnClickListener(this);
        branchButton=findViewById(R.id.branchButton);
        branchButton.setOnClickListener(this);
        branchText=findViewById(R.id.branchText);
        branchText.setVisibility(GONE);
        categoryButton=findViewById(R.id.categoryButton);
        categoryButton.setOnClickListener(this);
        categoryText=findViewById(R.id.categoryText);
        categoryText.setVisibility(GONE);
        dealerSubDealerButton=findViewById(R.id.dealerSubDealerButton);
        dealerSubDealerButton.setOnClickListener(this);
        dealerSubDealerText=findViewById(R.id.dealerSubDealerText);
        dealerSubDealerText.setVisibility(GONE);
        escalatedDepartmentButton=findViewById(R.id.escalatedDepartmentButton);
        escalatedDepartmentButton.setOnClickListener(this);
        escalatedDepartmentText=findViewById(R.id.escalatedDepartmentText);
        escalatedDepartmentText.setVisibility(GONE);
        stateText=findViewById(R.id.stateText);
        districtText=findViewById(R.id.districtText);
        issueDescriptionText=findViewById(R.id.issueDescriptionText);
        stateEditText=findViewById(R.id.stateEditText);
        districtEditText=findViewById(R.id.districtEditText);
        issueDescriptionEditText=findViewById(R.id.issueDescriptionEditText);

        setHintAndTitle();
    }

    private void setHintAndTitle(){
        branchButton.setText(Html.fromHtml("Branch<font color='#000000'>*</font>"));
        categoryButton.setText(Html.fromHtml("Category Type<font color='#000000'>*</font>"));
        dealerSubDealerButton.setText(Html.fromHtml("Customer Name<font color='#000000'>*</font>"));
        escalatedDepartmentButton.setText(Html.fromHtml("Escalated Department<font color='#000000'>*</font>"));
        stateText.setText(Html.fromHtml("State<font color='#000000'>*</font>"));
        districtText.setText(Html.fromHtml("District<font color='#000000'>*</font>"));
        issueDescriptionText.setText(Html.fromHtml("Issue Description<font color='#000000'>*</font>"));
    }

    @SuppressLint("SetTextI18n")
    public void showListDataForBranchDialog(ArrayList<KeyValue> dataSet) {
        try {
            final ShowDataForBranchAdapter pAdapter = new ShowDataForBranchAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText("Select Branch");
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    pAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            searchLayout.setVisibility(VISIBLE);

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                branchCode = dataSet.get(position).getKey();
                branchName = dataSet.get(position).getValue();
                branchText.setText(dataSet.get(position).getValue());
                branchText.setVisibility(VISIBLE);
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SetTextI18n")
    public void showListDataForDealerDialog(ArrayList<CustomerDetails> dataSet) {
        try {
            final ShowDataForDealerAdapter pAdapter = new ShowDataForDealerAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText("Select Customer");
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    pAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            searchLayout.setVisibility(VISIBLE);

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                customerCode = dataSet.get(position).getCustomerCode();
                customerName= dataSet.get(position).getCustomerName();
                dealerSubDealerText.setText(dataSet.get(position).getCustomerName());
                dealerSubDealerText.setVisibility(VISIBLE);
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }

    @SuppressLint("SetTextI18n")
    public void showListDataForTypeDialog(ArrayList<DataSet> dataSet,String type) {
        try {
            final ShowDataForDataAdapter pAdapter = new ShowDataForDataAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            String message="";
            if(type.equalsIgnoreCase("category")){
                message="Select Category";
            }else if(type.equalsIgnoreCase("escalated_department")){
                message="Select Escalated Department";
            }

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText(message);
            ImageView imageView1 = mDialogCustomer.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDialogCustomer.dismiss());

            LinearLayout searchLayout = mDialogCustomer.findViewById(R.id.searchLayout);
            EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    pAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });
            searchLayout.setVisibility(VISIBLE);

            ListView dialogList = mDialogCustomer.findViewById(R.id.list);
            dialogList.setAdapter(pAdapter);
            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDialogCustomer.dismiss();
                if(type.equalsIgnoreCase("category")){
                    custType = dataSet.get(position).getValue();
                    categoryText.setText(dataSet.get(position).getValue());
                    categoryText.setVisibility(VISIBLE);
                }else{
                    escalatedDepartment = dataSet.get(position).getValue();
                    escalatedDepartmentText.setText(dataSet.get(position).getValue());
                    escalatedDepartmentText.setVisibility(VISIBLE);
                }
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }
    private void progressDialogOpen(String title) {
        if (progressDialog == null) {
            progressDialog = new ProgressDialog(mContext);
            progressDialog.setCancelable(false);
        }
        progressDialog.setMessage(title);
        if (!progressDialog.isShowing()) {
            progressDialog.show();
        }
    }

    private void progressDialogClose() {
        ((RaiseIssueActivity) mContext).runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }
    private void requestForRaiseIssue(){
        progressDialogOpen("Raise your issue...");
        new Thread(() -> {
            try {
                String empCode = Constants.employeeDetailObject.getEmpCode();
                String state = stateEditText.getText().toString().trim();
                String district = districtEditText.getText().toString().trim();
                String issue = issueDescriptionEditText.getText().toString().trim();
                OkHttpClient client = new OkHttpClient();
                RequestBody body = new MultipartBody.Builder()
                        .setType(MultipartBody.FORM)
                        .addFormDataPart("emp_code", empCode)
                        .addFormDataPart("branch_code", branchCode)
                        .addFormDataPart("customer_code", customerCode)
                        .addFormDataPart("category", custType)
                        .addFormDataPart("state", state)
                        .addFormDataPart("district", district)
                        .addFormDataPart("esc_dept", escalatedDepartment)
                        .addFormDataPart("issue", issue)
                        .addFormDataPart("status", "pending")
                        .build();

                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/api_raise_issue_submit.php")
                        .post(body)
                        .build();

                Response response = client.newCall(request).execute();
                if (response.body() != null) {
                    String responseString = response.body().string();
                    try {
                        JSONObject jsonObject = new JSONObject(responseString);
                        if (jsonObject.optString("process_status").equalsIgnoreCase("no"))
                            ((RaiseIssueActivity) mContext).showError(jsonObject.optString("error"));
                        else
                            ((RaiseIssueActivity) mContext).showSuccess();
                    } catch (Exception e) {
                        ((RaiseIssueActivity) mContext).showError(e.getMessage());
                    }
                }
            } catch (Exception e) {
                Log.e("_DOWNLOAD_", "requestForNewSiteLeadAndConversionTracking Exception: " + e.getMessage(), e);
            }
        }).start();
    }

    private void showError(String message) {
        progressDialogClose();
        runOnUiThread(() -> Toast.makeText(mContext, message, Toast.LENGTH_LONG).show());
    }

    private void showSuccess() {
        progressDialogClose();
        runOnUiThread(() -> Toast.makeText(mContext, "Issue raised successfully", Toast.LENGTH_LONG).show());
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }
}