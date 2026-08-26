package com.forcepower.acedns.new_activity.raise_issue;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.GridView;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.ActivitySurveyLanding;
import com.forcepower.acedns.adapter.MenuAdapter;
import com.forcepower.acedns.bean.MenuObj;

import java.util.ArrayList;

public class RaiseIssueMenuActivity extends AceDnsParentActivity implements View.OnClickListener {
    private GridView mGridViewMenu;
    private Button backButton;
    ArrayList<MenuObj> mMenuList;
    MenuAdapter mMenuAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_raise_issue_menu);
        try {
            mGridViewMenu = findViewById(R.id.menuList);
            backButton = findViewById(R.id.backButton);
            backButton.setOnClickListener(this);
            mMenuList = new ArrayList<>();

            MenuObj menuObj = new MenuObj();
            menuObj.setFeatureName("new");
            menuObj.setResourceId(R.drawable.new_issue);
            mMenuList.add(menuObj);

            MenuObj menuObj1 = new MenuObj();
            menuObj1.setFeatureName("history");
            menuObj1.setResourceId(R.drawable.issue_history);
            mMenuList.add(menuObj1);


            mMenuAdapter = new MenuAdapter(RaiseIssueMenuActivity.this, R.layout.grid_child, mMenuList, false);
            mGridViewMenu.setAdapter(mMenuAdapter);

            mGridViewMenu.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                String feature = mMenuList.get(arg2).getFeatureName();
                if (feature.equalsIgnoreCase("new")) {
                    Intent intent = new Intent(RaiseIssueMenuActivity.this, RaiseIssueActivity.class);
                    intent.putExtra("SURVEYSUBMENUDETAILS", getIntent().getStringExtra("SURVEYSUBMENUDETAILS"));
                    startActivity(intent);
                } else {
                    Intent intent = new Intent(RaiseIssueMenuActivity.this, RaiseIssueHistoryActivity.class);
                    intent.putExtra("SURVEYSUBMENUDETAILS", getIntent().getStringExtra("SURVEYSUBMENUDETAILS"));
                    startActivity(intent);
                }
            });
        } catch (Exception e) {
            Log.d("TAG", "_DOWNLOAD_ onCreate: " + e);
        }
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
    }
}