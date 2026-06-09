package com.forcepower.acedns.activity;

import androidx.fragment.app.FragmentActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import com.forcepower.acedns.R;

import com.forcepower.acedns.adapter.StarLedgerAdapter;
import com.forcepower.acedns.api.clients.RestClient;
import com.forcepower.acedns.bean.starSaathiLedger;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import org.json.JSONException;
import org.json.JSONObject;

import java.lang.reflect.Type;
import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class StarOutStandingActivity  extends FragmentActivity {

    Button btnBack;
    Context mContext;
    TextView textViewOutStanding;
    RecyclerView rv;
    RecyclerView.LayoutManager recyclerViewlayoutManager;
    List<starSaathiLedger> ldList = new ArrayList<>();
    Gson gson;
    AceDnsDatabase mAceDnsDatabaseHelper;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_star_out_standing);
        btnBack = (Button) findViewById(R.id.back);
        textViewOutStanding = findViewById(R.id.outstandingbalance);


        mContext = StarOutStandingActivity.this;
        gson = new Gson();
        Intent intent=getIntent();
        String selectedCodes = intent.getStringExtra("cid");
        String selectedDNSCodes = intent.getStringExtra("cdnsid");
        mAceDnsDatabaseHelper = new AceDnsDatabase(mContext);
        String cDNScode = mAceDnsDatabaseHelper.getCustDnsCode(selectedCodes);

       // selectedCodes = "C/0007439";
        getStarSaathiLedgerById(mContext,selectedDNSCodes);
        //Utils.showToast(mContext,"dns --" + cDNScode);
        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
    }


    public void getStarSaathiLedgerById(Context mContext,String the_id) {
        Utils.showProgressDialog(mContext, "Downloding data..");
        //Toast.makeText(mContext,"json",Toast.LENGTH_SHORT).show();
        Log.d("TAG", "getStarSaathiLedgerById: "+the_id);
        Call<String> call = RestClient.getRestServiceString(mContext).starsaathi_ledger_by_id(the_id);
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
               Log.d("LD :=>", response.body() + "");
                if (response != null) {

                    String jsonResult = response.body();

                    try {

                        setValue(jsonResult);

                    }catch (Exception e){

                    }

                }
                Utils.cancelProgressDialog();
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Log.d("Error==>", t.getMessage());
                Utils.cancelProgressDialog();
            }
        });

    }

    public boolean setValue(String json) {

        boolean createSuccessful = false;
        //Toast.makeText(mContext, ""+json, Toast.LENGTH_SHORT).show();
        Log.d("ld",json);

        try {
            JSONObject obj = new JSONObject(json);
           // Toast.makeText(mContext,"PS -- "+obj.getString("process_status"),Toast.LENGTH_SHORT).show();

            if(obj.getString("process_status").toLowerCase().matches("yes")){
                try {
                    JSONObject objLBD = new JSONObject(obj.getString("ledger_balance_data"));
                    textViewOutStanding.setText("₹ " + objLBD.getString("balance"));
                    //JSONArray dataArray  = obj.getJSONArray("ledger_data");
                }catch (Exception e){

                }
                String ledger_data = obj.getString("ledger_data");
                setUpCartRecyclerview(ledger_data);


            }

        }catch (JSONException e){
            //Toast.makeText(mContext, ""+e, Toast.LENGTH_SHORT).show();
        }

        return createSuccessful;

    }

    private void setUpCartRecyclerview(String s) {
        ldList = new ArrayList<>();
        ldList = getldList(s);
        rv = findViewById(R.id.rvdata);
        rv.setHasFixedSize(true);
        recyclerViewlayoutManager = new LinearLayoutManager(getApplicationContext());
        rv.setLayoutManager(recyclerViewlayoutManager);
        //StarLedgerAdapter adapter = new StarLedgerAdapter(mContext, R.layout.route_list_child, ldList);
        //listViewLD.setAdapter(adapter);
        StarLedgerAdapter adapter= new StarLedgerAdapter(ldList, mContext);
        rv.setAdapter(adapter);
    }

    public List<starSaathiLedger> getldList(String s) {
        if (s != null) {
            String jsonCart = s;
            Type type = new TypeToken<List<starSaathiLedger>>() {
            }.getType();
            ldList = gson.fromJson(jsonCart, type);
            return ldList;
        }else{
            Toast.makeText(mContext,"No Data Found",Toast.LENGTH_SHORT).show();
        }
        return ldList;
    }

}