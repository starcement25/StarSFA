package com.forcepower.acedns.new_activity.target_achievement;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.target_achievement.adapter.CustomerWiseDetailsAdapter;
import com.forcepower.acedns.new_activity.target_achievement.dataset.DetailsDataSet;

import java.util.ArrayList;

public class EmployeeTargetAchievementDetailsActivity extends AceDnsParentActivity implements View.OnClickListener {
    Context mContext;

    private Button backButton;
    private TextView value1, value2, value3;
    private RecyclerView dataList;

    AceDnsDatabase mAceDnsDatabase;
    String monthName = "";
    int selectMenu = 0;
    int selectMonth = 0;
    ArrayList<DetailsDataSet> dataSet = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_employee_target_achievement_details);
        mContext = EmployeeTargetAchievementDetailsActivity.this;
        monthName = getIntent().getStringExtra("month_name");
        selectMenu = getIntent().getIntExtra("select_menu", 0);
        selectMonth = Integer.parseInt(monthName);
        String[] monthNameList = {"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"};
        monthName = monthNameList[selectMonth - 1];
        Log.d("TAG", "_DOOOO_ onCreate: " + monthName + "  " + selectMenu + "  " + selectMonth);
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        init();
    }

    @SuppressLint("SetTextI18n")
    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        TextView titleName = findViewById(R.id.titleName);

        TextView title1 = findViewById(R.id.title1);
        TextView title2 = findViewById(R.id.title2);

        value1 = findViewById(R.id.value1);
        value2 = findViewById(R.id.value2);
        value3 = findViewById(R.id.value3);

        dataList = findViewById(R.id.dataList);
        dataList.setLayoutManager(new LinearLayoutManager(mContext)); // ✅ ADD THIS
        dataList.setNestedScrollingEnabled(false);

        if (selectMenu == 3) {
            title1.setText("Prev Ach");
            title2.setText("Curr Ach");
        } else {
            title1.setText("Target");
            title2.setText("Ach");
        }
        titleName.setText(monthName + " - Performance Details");

        getDataset();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
    }

    @SuppressLint({"DefaultLocale", "SetTextI18n"})
    public void getDataset() {
        DetailsDataSet obj;
        if (selectMenu == 3) {
            obj = new DetailsDataSet("Customer Name", "Prev Ach (MT)", "Curr Ach (MT)", true);
        } else {
            obj = new DetailsDataSet("Customer Name", "Target", "Achievement", true);
        }
        dataSet.add(obj);
        ArrayList<DetailsDataSet> temp = mAceDnsDatabase.getCustomerPerformanceDetails(selectMenu, selectMonth);
        dataSet.addAll(temp);

        float target = 0;
        float achievement = 0;
        for (int i = 0; i < temp.size(); i++) {
            target += Float.parseFloat(temp.get(i).getTarget());
            achievement += Float.parseFloat(temp.get(i).getAchievement());
        }
        float percentage = (achievement / target) * 100;
        value1.setText(String.format("%.2f", target) + " MT");
        value2.setText(String.format("%.2f", achievement) + " MT");
        value3.setText(String.format("%.2f", percentage) + " %");

        Log.d("TAG", "_DOOOO_ onCreate: " + monthName + "  " + selectMenu + "  " + selectMonth + "  ||  " + dataSet.size());

        CustomerWiseDetailsAdapter adapter = new CustomerWiseDetailsAdapter(this, dataSet, selectMenu);
        dataList.setAdapter(adapter);
    }
}