package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.SimpleStringAdapter;

import java.util.ArrayList;

public class HoardingActivity extends AceDnsParentActivity {

    Button btnBack,btnNotOk,buttonOk,button_site_name,buttonSubmit;
    Uri imageUri;
    String imageName;
    int attachmentType, maxImageLimit=5,numberOfImageAdded = 0,numberOfImageAddedDa = 0;
    private int TAKE_PHOTO_CODE = 0;
    Context mContext;
    ArrayList<String> AttachmentNames = new ArrayList<>();
    ArrayList<String> accessMonthList = new ArrayList<>();

    ImageView attachmentImageView2, attachmentImageView3,attachmentImageView4,attachmentImageView5;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_hoarding);

        btnBack = findViewById(R.id.back);
        buttonSubmit = findViewById(R.id.buttonSubmit);
        button_site_name = findViewById(R.id.button_site_name);
        mContext = HoardingActivity.this;

        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        buttonSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
        button_site_name.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                ShowEventDialog("State Name");
            }
        });
    }

    public void ShowEventDialog(String parentText) {

        accessMonthList = new ArrayList<String>();

        accessMonthList.add("WB");
        accessMonthList.add("UP");
        accessMonthList.add("MP");
        accessMonthList.add("HR");

        final Dialog routePlanAccessDialog = new Dialog(HoardingActivity.this,
                R.style.PauseDialog);
        routePlanAccessDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanAccessDialog.setContentView(R.layout.select_from_list);
        routePlanAccessDialog.setCancelable(false);
        TextView title = (TextView) routePlanAccessDialog
                .findViewById(R.id.title);
        title.setText(parentText);
        ListView dialogList = (ListView) routePlanAccessDialog
                .findViewById(R.id.list);
        SimpleStringAdapter adapter1 = new SimpleStringAdapter(
                HoardingActivity.this, R.layout.routeplan_access_dialog_child,
                accessMonthList);
        dialogList.setAdapter(adapter1);
        Button cancel = (Button) routePlanAccessDialog
                .findViewById(R.id.btn_cncl);
        cancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanAccessDialog.cancel();
            }
        });

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1,
                                    int arg2, long arg3) {

                button_site_name.setText("" + accessMonthList.get(arg2).toString());
                routePlanAccessDialog.cancel();
            }
        });


        routePlanAccessDialog.show();
    }


}