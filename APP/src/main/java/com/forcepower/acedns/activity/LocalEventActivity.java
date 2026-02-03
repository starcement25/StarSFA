package com.forcepower.acedns.activity;

import android.os.Bundle;
import android.view.View;
import android.widget.Button;

import com.forcepower.acedns.R;

public class LocalEventActivity extends AceDnsParentActivity {

    Button btnBack,btnNotOk,buttonOk,button_site_name,buttonSubmit;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_local_event);

        btnBack = findViewById(R.id.back);

        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
    }
}