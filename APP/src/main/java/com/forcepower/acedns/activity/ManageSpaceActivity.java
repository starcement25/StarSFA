package com.forcepower.acedns.activity;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;

import com.forcepower.acedns.R;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

public class ManageSpaceActivity extends Activity {

    Button btn;
    EditText edName;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_nickname);
        RegisterActivities.registerActivity(this);

        btn = (Button) findViewById(R.id.btn);
        edName = (EditText) findViewById(R.id.ed_name);
        btn.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String name = "";
                name = edName.getText().toString();
                if (name.equalsIgnoreCase("OTP")) {
                    Utils.directOutsideTheApplication(ManageSpaceActivity.this, "Data Cleared", false);
                } else {
                    Utils.directOutsideTheApplication(ManageSpaceActivity.this, "Data Cleared", false);
                }
            }
        });

    }
}