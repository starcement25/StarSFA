package com.forcepower.acedns.new_activity.dealer_subdealer_visit;

import static android.view.View.GONE;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Html;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.util.ArrayList;

public class DealerSubDealerVisitActivity extends AceDnsParentActivity implements View.OnClickListener {
    Context mContext;

    private Button backButton,submitButton;

    private LinearLayout branchButtonLayout,categoryButtonLayout,counterButtonLayout;
    private Button branchButton,categoryButton,counterButton;
    private TextView branchButtonText,categoryButtonText,counterButtonText;

    private LinearLayout visitPurposeLayout,remarksLayout;
    private TextView visitPurposeTitleText,remarksTitleText;
    private EditText visitPurposeTitleEditText,remarksTitleEditText;

    public ProgressDialog mPrepareSurveyProgressDialog;
    public Handler mPrepareSurveyHandler;
    public AceDnsDatabase mAceDnsDatabase;
    private String[] values;
    private ArrayList<KeyValue> mKeyValueList;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dealer_sub_dealer_visit);

        mContext=DealerSubDealerVisitActivity.this;

        init();
    }

    @Override
    public void onClick(View v) {
        if(v==backButton){
            finish();
        }

        if(v==branchButton){}
        if(v==categoryButton){}
        if(v==counterButton){}

        if(v==submitButton){}
    }

    private void init(){
        backButton=findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        branchButtonLayout=findViewById(R.id.branchButtonLayout);
        branchButtonLayout.setVisibility(GONE);
        branchButton=findViewById(R.id.branchButton);
        branchButton.setOnClickListener(this);
        branchButtonText=findViewById(R.id.branchButtonText);

        categoryButtonLayout=findViewById(R.id.categoryButtonLayout);
        categoryButtonLayout.setVisibility(GONE);
        categoryButton=findViewById(R.id.categoryButton);
        categoryButton.setOnClickListener(this);
        categoryButtonText=findViewById(R.id.categoryButtonText);

        counterButtonLayout=findViewById(R.id.counterButtonLayout);
        counterButtonLayout.setVisibility(GONE);
        counterButton=findViewById(R.id.counterButton);
        counterButton.setOnClickListener(this);
        counterButtonText=findViewById(R.id.counterButtonText);

        visitPurposeLayout=findViewById(R.id.visitPurposeLayout);
        visitPurposeLayout.setVisibility(GONE);
        visitPurposeTitleText=findViewById(R.id.visitPurposeTitleText);
        visitPurposeTitleEditText=findViewById(R.id.visitPurposeTitleEditText);

        remarksLayout=findViewById(R.id.remarksLayout);
        remarksLayout.setVisibility(GONE);
        remarksTitleText=findViewById(R.id.remarksTitleText);
        remarksTitleEditText=findViewById(R.id.remarksTitleEditText);

        submitButton=findViewById(R.id.submitButton);
        submitButton.setOnClickListener(this);

        titleShow();
    }

    private void titleShow(){
        branchButton.setText(Html.fromHtml("Branch <font color='#FF0000'>*</font>"));
        categoryButton.setText(Html.fromHtml("Category <font color='#FF0000'>*</font>"));
        counterButton.setText(Html.fromHtml("Select Counter <font color='#FF0000'>*</font>"));
        visitPurposeTitleText.setText(Html.fromHtml("Purpose of the Visit <font color='#FF0000'>*</font>"));
        remarksTitleText.setText(Html.fromHtml("Remarks <font color='#FF0000'>*</font>"));
    }

    public void PrepareSurveyData(final int task) {
        mPrepareSurveyProgressDialog = new ProgressDialog(mContext);
        mPrepareSurveyProgressDialog.setMessage("Fetching Data.Please wait..");
        mPrepareSurveyProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7("branch_master", "branch_code", "branch_name", "", "");
                        break;
                    case 2:
                        int maxx = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase6("customer_master", "cust_type", "WHERE branch_code like '%B0001%'");
                        if (maxx > 0) {
                            values = new String[maxx];
                            System.arraycopy(Constants.mSurveyLayoutList, 0, values, 0, Constants.mSurveyLayoutList.length);
                        } else {
                            values = null;
                        }
                        break;
                    case 3:
                        mKeyValueList = mAceDnsDatabase.GetSurveyMasterTableCategoryDetailsCase7("customer_master", "customer_code", "customer_name", "", "WHERE branch_code='B0001'  AND cust_type='Dealer'");
                        break;
                }
                Message msg = mPrepareSurveyHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareSurveyHandler.sendMessage(msg);
            }
        }.start();
    }
}