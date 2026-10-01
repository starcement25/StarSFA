package com.forcepower.acedns.new_activity.ship_to_party_location;

import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.text.Editable;
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
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet;
import com.forcepower.acedns.new_activity.ship_to_party_location.adapter.ShowDataAdapter;
import com.forcepower.acedns.util.LocationTracker;
import org.json.JSONObject;
import java.util.ArrayList;

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class ShipToPartyLocationCollectActivity extends AceDnsParentActivity implements View.OnClickListener {
    Context mContext;
    private Button backButton,editLocationButton,saveButton;
    private Button branchButton,typeButton,dealerButton,shipToPartyButton;
    private TextView branchText,typeText,dealerText,shipToPartyText;
    private EditText latitudeEditText,longitudeEditText;

    NewDatabaseForSiteLead newDatabaseForSiteLead;
    private String branchCode="",dealerCode="",shipToPartyCode="",latitude="",longitude="",type1="";
    ProgressDialog progressDialog;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_ship_to_party_location_collect);
        mContext=ShipToPartyLocationCollectActivity.this;
        init();
        newDatabaseForSiteLead=new NewDatabaseForSiteLead(mContext);
    }
    @Override
    public void onClick(View v) {
        if(v==backButton){
            finish();
        }
        if(v==editLocationButton){
            getLocation();
        }
        if(v==saveButton){
            if(branchCode.isEmpty()){
                Toast.makeText(mContext, "Please select branch", Toast.LENGTH_SHORT).show();
            }else if(dealerCode.isEmpty()){
                Toast.makeText(mContext, "Please select dealer", Toast.LENGTH_SHORT).show();
            }else if(shipToPartyCode.isEmpty()){
                Toast.makeText(mContext, "Please select ship to party", Toast.LENGTH_SHORT).show();
            }else if(latitude.isEmpty()){
                Toast.makeText(mContext, "Latitude can not update.\nClick on edit location.", Toast.LENGTH_SHORT).show();
            }else if(longitude.isEmpty()){
                Toast.makeText(mContext, "Longitude can not update.\nClick on edit location.", Toast.LENGTH_SHORT).show();
            }else {
                requestForLocationSubmit();
            }
        }
        if(v==branchButton){
            ArrayList<DataSet> branchList = newDatabaseForSiteLead.getShipToPartyLocation_BranchList();
            showListDataDialog(branchList,"Branch");
        }
        if(v==typeButton){
            ArrayList<DataSet> typeList = new ArrayList<>();
            DataSet o1=new DataSet();
            o1.setId("Ship to Party-dealer");
            o1.setValue("Dealer");
            typeList.add(o1);
            DataSet o2=new DataSet();
            o2.setId("ShiptoParty-Subdealer");
            o2.setValue("RSAR");
            typeList.add(o2);
            showListDataDialog(typeList,"Type");
        }
        if(v==dealerButton){
            ArrayList<DataSet> dealerList = newDatabaseForSiteLead.getShipToPartyLocation_DealerList(branchCode,type1);
            showListDataDialog(dealerList,"Dealer");
        }
        if(v==shipToPartyButton){
            ArrayList<DataSet> shipToPartyList = newDatabaseForSiteLead.getShipToPartyLocation_ShipToPartyList(branchCode, dealerCode);
            showListDataDialog(shipToPartyList,"Ship to Party");
        }
    }
    private void init(){
        backButton=findViewById(R.id.backButton);
        editLocationButton=findViewById(R.id.editLocationButton);
        saveButton=findViewById(R.id.saveButton);
        branchButton=findViewById(R.id.branchButton);
        typeButton=findViewById(R.id.typeButton);
        dealerButton=findViewById(R.id.dealerButton);
        shipToPartyButton=findViewById(R.id.shipToPartyButton);
        branchText=findViewById(R.id.branchText);
        typeText=findViewById(R.id.typeText);
        dealerText=findViewById(R.id.dealerText);
        shipToPartyText=findViewById(R.id.shipToPartyText);
        latitudeEditText=findViewById(R.id.latitudeEditText);
        longitudeEditText=findViewById(R.id.longitudeEditText);
        latitudeEditText.setEnabled(false);
        longitudeEditText.setEnabled(false);

        backButton.setOnClickListener(this);
        editLocationButton.setOnClickListener(this);
        saveButton.setOnClickListener(this);
        branchButton.setOnClickListener(this);
        typeButton.setOnClickListener(this);
        dealerButton.setOnClickListener(this);
        shipToPartyButton.setOnClickListener(this);

        getLocation();
    }
    private void getLocation(){
        LocationTracker locationTracker = new LocationTracker(this);
        locationTracker.checkLocationUpdateSharing();
        new Handler().postDelayed(() -> {
            latitude = "0";
            longitude = "0";
            latitude = Constants.currentLat;
            longitude = Constants.currentLong;
            showLocation();
        }, 2000);
    }
    private void showLocation(){
        runOnUiThread(()->{
            latitudeEditText.setText(latitude);
            longitudeEditText.setText(longitude);
        });
    }
    @SuppressLint("SetTextI18n")
    public void showListDataDialog(ArrayList<DataSet> dataSet,String type) {
        try {
            final ShowDataAdapter pAdapter = new ShowDataAdapter(this, R.layout.list_item_single_radio, dataSet);
            final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
            mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogCustomer.setContentView(R.layout.custome_popup_v2);
            mDialogCustomer.setCancelable(false);

            TextView title = mDialogCustomer.findViewById(R.id.title);
            title.setText("Select "+type);
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
                DataSet selectedItem = pAdapter.getItem(position);
                if (selectedItem == null) {
                    return;
                }
                mDialogCustomer.dismiss();
                if(type.equalsIgnoreCase("Branch")){
                    branchCode = selectedItem.getId();
                    branchText.setText(selectedItem.getValue());
                }else if(type.equalsIgnoreCase("Dealer")){
                    dealerCode = selectedItem.getId();
                    dealerText.setText(selectedItem.getValue());
                }else if(type.equalsIgnoreCase("Ship to Party")){
                    shipToPartyCode = selectedItem.getId();
                    shipToPartyText.setText(selectedItem.getValue());
                }else if(type.equalsIgnoreCase("Type")){
                    type1= selectedItem.getId();
                    typeText.setText(selectedItem.getValue());
                    dealerButton.setText(selectedItem.getValue());
                }
            });
            mDialogCustomer.show();
        } catch (Exception ignored) {
        }
    }
    private void progressDialogOpen() {
        if (progressDialog == null) {
            progressDialog = new ProgressDialog(mContext);
            progressDialog.setCancelable(false);
        }
        progressDialog.setMessage("Loading ...");
        if (!progressDialog.isShowing()) {
            progressDialog.show();
        }
    }
    private void progressDialogClose() {
        ((ShipToPartyLocationCollectActivity) mContext).runOnUiThread(() -> {
            if (progressDialog != null && progressDialog.isShowing()) {
                progressDialog.dismiss();
            }
        });
    }
    private void requestForLocationSubmit(){
        progressDialogOpen();
        new Thread(() -> {
            try {
                String empCode = Constants.employeeDetailObject.getEmpCode();
                JSONObject payload = new JSONObject();
                payload.put("emp_code", empCode);
                payload.put("branch_code", branchCode);
                payload.put("dns_ship_to_party_code", shipToPartyCode);
                payload.put("dealer_code", dealerCode);
                payload.put("latitude", latitude);
                payload.put("longitude", longitude);

                OkHttpClient client = new OkHttpClient();
                MediaType mediaType = MediaType.parse("application/json");
                assert mediaType != null;
                RequestBody body = RequestBody.create(mediaType, payload.toString());
                Log.d("TAG", "requestForLocationSubmit: "+empCode+" | "+branchCode+" | "+shipToPartyCode+" | "+dealerCode+" | "+latitude+" | "+longitude+" | "+BaseUrl.baseUrl + "misreport/api_update_ship_to_party_location.php");
                Request request = new Request.Builder()
                        .url(BaseUrl.baseUrl + "misreport/api_update_ship_to_party_location.php")
                        .post(body)
                        .build();

                Response response = client.newCall(request).execute();
                if (response.body() != null) {
                    String responseString = response.body().string();
                    try {
                        JSONObject jsonObject = new JSONObject(responseString);
                        if (jsonObject.optString("process_status").equalsIgnoreCase("no"))
                            ((ShipToPartyLocationCollectActivity) mContext).showError();
                        else
                            ((ShipToPartyLocationCollectActivity) mContext).showSuccess();
                    } catch (Exception e) {
                        ((ShipToPartyLocationCollectActivity) mContext).showError();
                    }
                }
            } catch (Exception e) {
                Log.e("_DOWNLOAD_", "requestForLocationSubmit Exception: " + e.getMessage(), e);
            }
        }).start();
    }
    private void showError() {
        progressDialogClose();
        newDatabaseForSiteLead.updateShipToPartyLocation(shipToPartyCode,latitude,longitude,1);
        runOnUiThread(() -> Toast.makeText(mContext, "Location can't save in server.\nPlease sync to upload location data.", Toast.LENGTH_LONG).show());
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }
    private void showSuccess() {
        progressDialogClose();
        newDatabaseForSiteLead.updateShipToPartyLocation(shipToPartyCode,latitude,longitude,2);
        runOnUiThread(() -> Toast.makeText(mContext, "Location save successfully", Toast.LENGTH_LONG).show());
        new Handler(Looper.getMainLooper()).postDelayed(this::finish, 2000);
    }
}
