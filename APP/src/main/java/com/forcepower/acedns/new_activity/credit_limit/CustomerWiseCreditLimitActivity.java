package com.forcepower.acedns.new_activity.credit_limit;

import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.credit_limit.adapter.CreditLimitAdapter;
import com.forcepower.acedns.new_activity.credit_limit.dataset.CreditLimitDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet;

import java.util.ArrayList;
import java.util.Random;

public class CustomerWiseCreditLimitActivity extends AceDnsParentActivity {
    Context mContext;
    private Button backButton;
    private RecyclerView customerWiseCreditLimitDataList;
    private EditText searchEditText;
    AceDnsDatabase mAceDnsDatabase;
    ArrayList<DataSet> customerList = new ArrayList<>();
    ArrayList<CreditLimitDataSet> creditLimitDataSetList = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_customer_wise_credit_limit);
        mContext = CustomerWiseCreditLimitActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        init();
    }

    @Override
    public void onClick(View v) {
        if (backButton == v) {
            finish();
        }
    }

    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);
        customerWiseCreditLimitDataList = findViewById(R.id.customerWiseCreditLimitDataList);
        customerWiseCreditLimitDataList.setLayoutManager(new LinearLayoutManager(this));
        searchEditText = findViewById(R.id.searchEditText);
        creditLimitDataSetList.clear();
        customerList.clear();
        customerList = mAceDnsDatabase.getDistinctCustomerList();
        Log.d("TAG", "_DOWNLOAD_ init: " + customerList.size());
        Random random = new Random();
        for (int i = 0; i < customerList.size(); i++) {
            int creditLimit = (random.nextInt(9) + 1) * 100000;
            int percentage = (random.nextInt(80) + 20);
            int utilisedCL = creditLimit * percentage / 100;
            int availableCL = creditLimit - utilisedCL;
            CreditLimitDataSet obj = new CreditLimitDataSet(
                    customerList.get(i).getId(),
                    customerList.get(i).getValue(),
                    String.valueOf(creditLimit),
                    String.valueOf(utilisedCL),
                    String.valueOf(availableCL)
            );
            creditLimitDataSetList.add(obj);
        }

        CreditLimitAdapter adapter = new CreditLimitAdapter(mContext, creditLimitDataSetList);
        customerWiseCreditLimitDataList.setAdapter(adapter);
    }
}