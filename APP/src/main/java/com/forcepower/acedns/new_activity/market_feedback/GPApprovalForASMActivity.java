package com.forcepower.acedns.new_activity.market_feedback;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerMarketFeedbackDetailsDataSet;
import com.forcepower.acedns.new_activity.market_feedback.adapter.GPApprovalItemAdapter;
import com.forcepower.acedns.new_activity.market_feedback.data.GPApprovalItem;
import com.forcepower.acedns.new_activity.ocr.adapter.OcrDataItemAdapter;
import com.forcepower.acedns.new_activity.ocr.dataset.OcrItem;

import java.util.ArrayList;

public class GPApprovalForASMActivity extends AceDnsParentActivity implements View.OnClickListener {

    private Context mContext;
    private Button backButton;
    private RecyclerView gpDataList;

    ArrayList<GPApprovalItem> list = new ArrayList<>();
    GPApprovalItemAdapter adapter;

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
    }

    private void init() {
        mContext = GPApprovalForASMActivity.this;
        backButton = findViewById(R.id.backButton);
        gpDataList = findViewById(R.id.gpDataList);
        backButton.setOnClickListener(this);
        try {
            NewDatabaseForSiteLead mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(mContext);
            list = mNewDatabaseForSiteLead.getCustomerGPDetails();




            Log.d("TAG", "_DDDDD_ init: " + list.size());
        } catch (Exception e) {
            Log.d("TAG", "_DDDDD_ init: "+e.getMessage());
        }

//        gpDataList.setLayoutManager(new LinearLayoutManager(mContext));
//        adapter = new GPApprovalItemAdapter(mContext, list, new GPApprovalItemAdapter.OnActionClickListener() {
//            @SuppressLint("NotifyDataSetChanged")
//            @Override
//            public void approveItem(GPApprovalItem item, int position) {
//
//            }
//        });
//        gpDataList.setAdapter(adapter);
    }
}