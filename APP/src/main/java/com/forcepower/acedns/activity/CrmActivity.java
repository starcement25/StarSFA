package com.forcepower.acedns.activity;


import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import androidx.annotation.NonNull;

import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.Spinner;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerTypeAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.allocatedRouteCodeTodayCrm;
import static com.forcepower.acedns.constants.Constants.allocatedRouteNameTodayCrm;
import static com.forcepower.acedns.constants.Constants.currentCustomerCode;
import static com.forcepower.acedns.constants.Constants.currentCustomerNumber;
import static com.forcepower.acedns.constants.Constants.isCallingFromApp;

public class CrmActivity extends AceDnsParentActivity {
//    private static final int /*MAKE_CALL_PERMISSION_REQUEST_CODE = 1,*/ /*RECORD_AUDIO_PERMISSION_CODE = 2*/;
    Context mContext;
    AceDnsDatabase mAceDnsDatabase;
    Spinner spinner;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    int currentapiVersion = android.os.Build.VERSION.SDK_INT;
    int resultForPhoneCall;
    int resultForRecordAudio;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_crm);
        mContext = this;
//        resultForPhoneCall = ContextCompat.checkSelfPermission(mContext, Manifest.permission.CALL_PHONE);
//        resultForRecordAudio = ContextCompat.checkSelfPermission(mContext, Manifest.permission.RECORD_AUDIO);
        Utils.getAppVersionDbVersion((TextView) findViewById(R.id.txt_version), mContext);
        Utils.getAppLogo((ImageView) findViewById(R.id.imagelogo));
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        spinner = (Spinner) findViewById(R.id.spinner);
        ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, allocatedRouteNameTodayCrm);

        // Drop down layout style - list view with radio button
        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

        // attaching data RoutePlanAdapter to spinner
        spinner.setAdapter(dataAdapter);
        spinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int position, long l) {
//                String selectedCatagory=adapterView.getItemAtPosition(position).toString();
                mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlan(allocatedRouteCodeTodayCrm.get(position));
                final ListView list = (ListView) findViewById(R.id.list);
                final CustomerTypeAdapter adapterCust = new CustomerTypeAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList, true);
                list.setAdapter(adapterCust);
                list.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                    @Override
                    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                        currentCustomerCode = mCustomerDetailsList.get(position).getCustomerCode();
                        currentCustomerNumber = mCustomerDetailsList.get(position).getNumber();
//                        if (currentapiVersion > android.os.Build.VERSION_CODES.LOLLIPOP) {
//
//                            //If permission is granted returning true
//                            if (resultForPhoneCall == PackageManager.PERMISSION_GRANTED && resultForRecordAudio == PackageManager.PERMISSION_GRANTED) {
//                                makeCall();
//                            } else {
//                                if (resultForPhoneCall != PackageManager.PERMISSION_GRANTED) {
//                                    ActivityCompat.requestPermissions((Activity) mContext, new String[]{Manifest.permission.CALL_PHONE}, MAKE_CALL_PERMISSION_REQUEST_CODE);
//                                } else {
//                                    ActivityCompat.requestPermissions((Activity) mContext, new String[]{Manifest.permission.RECORD_AUDIO}, RECORD_AUDIO_PERMISSION_CODE);
//                                }
//
//                            }
//
//                        } else {
//                            makeCall();
//                        }
                        makeCall();

                    }
                });
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) {

            }
        });
//        isCallingFromApp=true;
//        String dial = "tel: 9836389152"  ;
//        startActivity(new Intent(Intent.ACTION_CALL, Uri.parse(dial)));
//        finish();
    }

    private void makeCall() {
        isCallingFromApp = true;
        String dial = "tel:" + currentCustomerNumber;
        Intent intent = new Intent(Intent.ACTION_DIAL);
        intent.setData(Uri.parse("tel:"+currentCustomerNumber));
        startActivity(intent);
    }

    public void finishCurrentActivity(View v) {
        finish();
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        switch (requestCode) {
//            case MAKE_CALL_PERMISSION_REQUEST_CODE:
//                if (grantResults.length > 0 && (grantResults[0] == PackageManager.PERMISSION_GRANTED)) {
//
//                    if (resultForRecordAudio != PackageManager.PERMISSION_GRANTED) {
//                        ActivityCompat.requestPermissions((Activity) mContext, new String[]{Manifest.permission.RECORD_AUDIO}, RECORD_AUDIO_PERMISSION_CODE);
//                    } else {
//                        makeCall();
//                    }
//                } else if (grantResults.length > 0 && (grantResults[0] != PackageManager.PERMISSION_GRANTED)) {
//                    Utils.showToast(mContext, "You can not make call without accepting the permission.");
//                }
//                return;
//            case RECORD_AUDIO_PERMISSION_CODE:
//                if (grantResults.length > 0 && (grantResults[0] == PackageManager.PERMISSION_GRANTED)) {
//                    makeCall();
//                } else if (grantResults.length > 0 && (grantResults[0] != PackageManager.PERMISSION_GRANTED)) {
//                    Utils.showToast(mContext, "You can not make call without accepting the permission.");
//                }
//                return;

        }
    }

}
