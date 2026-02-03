package com.forcepower.acedns.activity;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.drawable.BitmapDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.WindowManager;
import android.widget.AdapterView;

import android.widget.ListView;
import android.widget.PopupWindow;

import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ListAdapterTracking;
import com.forcepower.acedns.adapter.TrackRvAdapter;
import com.forcepower.acedns.bean.CommonHelper;
import com.forcepower.acedns.bean.TrackItems;
import com.forcepower.acedns.bean.TrackStatus;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.checkInternetConnection;
import static com.forcepower.acedns.constants.Constants.downloadTableList;
import static com.forcepower.acedns.util.Utils.DownloadData;
import static com.forcepower.acedns.util.Utils.mPrepareSurveyHandler;


public class TrackActivity extends AppCompatActivity
{
    Activity myActivity;
    Context mContext;
    ArrayList<TrackItems> approved_list = new ArrayList<>();

    TrackRvAdapter trackRvAdapter;
    RecyclerView rv_track;
    String default_text = "", selected_vehicle_no = "", arrival_gate_id = "";
    ArrayList<CommonHelper> participentListItemGlobal = new ArrayList<>();
    ArrayList<CommonHelper> participentListItemDefault = new ArrayList<>();
    TextView tv_vehicle_no, tv_cust_name, tv_destination;
    AceDnsDatabase mAceDnsDatabase;
    boolean isvehicle_list_downloaded = false;
    private int mCount=0;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_track);

        try
        {
            myActivity = TrackActivity.this;
            mContext = TrackActivity.this;
            //Header_View
            setContentView(R.layout.activity_track);
            RegisterActivities.registerActivity(this);
            Toolbar myToolbar = findViewById(R.id.my_toolbar);
            setSupportActionBar(myToolbar);
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setDisplayShowHomeEnabled(true);
            getSupportActionBar().setDisplayShowTitleEnabled(false);



            //
            tv_vehicle_no = (TextView) findViewById(R.id.tv_vehicle_no);
            rv_track = (RecyclerView) findViewById(R.id.rv_track);
            rv_track.setVisibility(View.INVISIBLE);
            trackRvAdapter = new TrackRvAdapter(approved_list, myActivity);
            rv_track.setAdapter(trackRvAdapter);


            TrackItems cdh = new TrackItems();

            cdh.setTrackItems("Vehicle number - has been assigned by Transporter against your Do number", "", "inActive", "transporter");
            approved_list.add(cdh);

            cdh = new TrackItems();
            cdh.setTrackItems("Vehicle has reached plant", "", "inActive", "gate_keeper1");
            approved_list.add(cdh);

            cdh = new TrackItems();
            cdh.setTrackItems("Vehicle has reported at despatch section for statutory documentation", "", "inActive", "despatch_in");
            approved_list.add(cdh);

//            cdh = new TrackItems();
//            cdh.setTrackItems("Document has been checked and release for weigh bridge inspection", "", "inActive", "gate_keeper2");
//            approved_list.add(cdh);

            cdh = new TrackItems();
            cdh.setTrackItems("Vehicle Tare weight has been measured and statutory serial number generated", "", "inActive", "weighbridge_in");
            approved_list.add(cdh);

            cdh = new TrackItems();
            cdh.setTrackItems("Vehicle loading completed", "", "inActive", "loading");
            approved_list.add(cdh);

            cdh = new TrackItems();
            cdh.setTrackItems("Truck load weight has been captured and released for final billing", "", "inActive", "weighbridge_out");
            approved_list.add(cdh);

            cdh = new TrackItems();
            cdh.setTrackItems("Invoice generated along with e-way bill and handed over to vehicle driver","", "inActive", "despatch_out");
            approved_list.add(cdh);

//            cdh = new TrackItems();
//            cdh.setTrackItems("Gate keeper2 out","", "inActive", "gate_keeper2_out");
//            approved_list.add(cdh);

            cdh = new TrackItems();
            cdh.setTrackItems("Final security checking is done and vehicle has moved out from the plant","", "inActive", "security_out");
            approved_list.add(cdh);

//            cdh = new TrackItems();
//            cdh.setTrackItems("Gate keeper1 out","", "inActive", "gate_keeper1_out");
//            approved_list.add(cdh);

//            trackRvAdapter.notifyDataSetChanged();

            if(HTTPUtils.isConnectionPossible(mContext))
            {
                loadDialog();
                downloadTableList.clear();

                downloadTableList.add("vehicle_data_download_tracking"); //DO_tracking

                DownloadData(myActivity, mCount, downloadTableList.get(mCount), "");

            }
            else
            {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
            mPrepareSurveyHandler = new Handler() {
                public void handleMessage(Message threadmsg)
                {
                    final int listcount = threadmsg.getData().getInt("JOBALLOCATE");
                    runOnUiThread(new Runnable() {
                        public void run() {
                            if(listcount== Constants.downloadTableList.size()-1)
                            {
                                mAceDnsDatabase = new AceDnsDatabase(myActivity);
                                ArrayList<CommonHelper> vehicleList = mAceDnsDatabase.getVehicleList("");
                                participentListItemGlobal.clear();
                                participentListItemDefault.clear();
                                for (int i = 0; i<vehicleList.size(); i++)
                                {
                                    CommonHelper ch = new CommonHelper();
                                    ch.setItem0(vehicleList.get(i).getItem0()); //vehicle_no
                                    ch.setItem1(vehicleList.get(i).getItem1()); //customer_code
                                    ch.setItem2(vehicleList.get(i).getItem2()); //destination
                                    ch.setItem3(vehicleList.get(i).getItem3()); //DO_no

                                    participentListItemGlobal.add(ch);
                                    participentListItemDefault.add(ch);
                                }

                                if(participentListItemGlobal.size() == 1)
                                {
                                    tv_vehicle_no.setText(participentListItemGlobal.get(0).getItem0());
                                    rv_track.setVisibility(View.VISIBLE);
                                    onitemClick(tv_vehicle_no, participentListItemGlobal.get(0).getItem3());
                                }

                                mAceDnsDatabase.closeDatabase();
                                dismissDialog();
                            }
                            else
                            {
                                mCount++;
                                DownloadData(myActivity, mCount, downloadTableList.get(mCount), "");
                                progressDialogObj.setTitle("Updating " + downloadTableList.get(mCount));
                            }

                        }
                    });
                }
            };

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void vehicle_number_list(View view)
    {
        for(int k=0; k<approved_list.size(); k++)
        {
            approved_list.get(k).set_status("inActive");
            approved_list.get(k).set_date("");
        }
        if(participentListItemGlobal.size() > 1)
        {
            openDialog(tv_vehicle_no);
        }
    }
    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }
    public void openDialog(final TextView textView)
    {
        try
        {

            textView.setText(default_text);
            final PopupWindow popup = new PopupWindow(this);
            View layout = getLayoutInflater().inflate(R.layout.list_item_dialog, null);
            popup.setContentView(layout);

            // Set content width and height
            popup.setHeight(WindowManager.LayoutParams.WRAP_CONTENT);
            popup.setWidth(textView.getWidth());

            // Closes the popup window when touch outside of it - when looses focus
            popup.setOutsideTouchable(true);
            popup.setFocusable(true);
            popup.setBackgroundDrawable(new BitmapDrawable());

            popup.showAsDropDown(textView, 0, 0);

            participentListItemGlobal.clear();
            participentListItemGlobal.addAll(participentListItemDefault);

            final ListAdapterTracking mAdapter = new ListAdapterTracking(this, participentListItemGlobal);
            ListView listView = (ListView) layout.findViewById(R.id.lvPopup);
            listView.setAdapter(mAdapter);
            listView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {

                    textView.setText(participentListItemGlobal.get(i).getItem0());
                    popup.dismiss();
                    rv_track.setVisibility(View.VISIBLE);
                    TextView tv_selected_vehicle_no = view.findViewById(R.id.tv_team_name); //vehicle_no
                    TextView tv_hidden_value_3 = view.findViewById(R.id.tv_hidden_value_3); //DO_no
                    String selected_do_no = tv_hidden_value_3.getText().toString();
                    onitemClick(tv_selected_vehicle_no, selected_do_no);
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void onitemClick(TextView tv_selected_vehicle_no, String selected_do_no)
    {
        try
        {
            mAceDnsDatabase = new AceDnsDatabase(myActivity);
            ArrayList<TrackStatus> trackingList = mAceDnsDatabase.getTrackingList(tv_selected_vehicle_no.getText().toString());
            mAceDnsDatabase.closeDatabase();

            for(int k=0; k<approved_list.size(); k++)
            {
                String _key_name  = approved_list.get(k).get_key_name();
                for(int j=0; j<trackingList.size(); j++)
                {
                    if(_key_name.matches(trackingList.get(j).get_key_name()))
                    {
                        if(trackingList.get(j).get_status().matches("DONE"))
                        {
                            if(trackingList.get(j).get_key_name().matches("transporter"))
                            {
                                approved_list.get(k).set_heading("Vehicle number - has been assigned by Transporter against your Do number " + selected_do_no);
                            }
                            approved_list.get(k).set_status("Active");
                            String data = trackingList.get(j).get_date();
                            try
                            {
                                if(!data.matches("") && data.length()>14)
                                {
                                    data = modified_date(data);
                                    data = changeDateFormat(data, "yyyy-MM-dd HH:mm:ss", "dd-MM-yyyy HH:mm");
                                    StringBuilder sb = new StringBuilder(data);
                                    sb.insert(11, ' ');
                                    data = sb.toString();
                                    sb = new StringBuilder(data);
                                    sb.insert(12, '@');
                                    data = sb.toString();
                                    approved_list.get(k).set_date("on " + data);
                                }
                                else
                                {
                                    approved_list.get(k).set_date("");
                                }
                            }
                            catch (Exception e)
                            {
                                e.printStackTrace();
                            }

                        }
                    }
                }

            }
            //
            trackRvAdapter.setFilter(approved_list);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public String modified_date(String data)
    {
        try
        {
            data = data.substring(data.length()-14);

            StringBuilder sb = new StringBuilder(data);
            sb.insert(4, '-');
            data = sb.toString();

            sb = new StringBuilder(data);
            sb.insert(7, '-');
            data = sb.toString();

            sb = new StringBuilder(data);
            sb.insert(10, ' ');
            data = sb.toString();

            sb = new StringBuilder(data);
            sb.insert(13, ':');
            data = sb.toString();

            sb = new StringBuilder(data);
            sb.insert(16, ':');
            data = sb.toString();
        }
        catch (Exception e)
        {
            e.printStackTrace();
            return "";
        }
        return data;
    }
    public static String changeDateFormat(String inputDateStr, String input, String output)
    {
        String result = "";
        try
        {
            DateFormat inputFormat = new SimpleDateFormat(input); //yyyy-MM-dd HH:mm:ss
            DateFormat outputFormat = new SimpleDateFormat(output); //dd-MM-yyyy HH:mm

            Date date = inputFormat.parse(inputDateStr);
            result = outputFormat.format(date);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return result;
    }
    ProgressDialog progressDialogObj;
    public void loadDialog()
    {
        if(progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
        progressDialogObj= new ProgressDialog(myActivity);
        progressDialogObj.setCancelable(false);
        progressDialogObj.setTitle("Updating");
        progressDialogObj.show();
    }
    public void dismissDialog()
    {
        if(progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
    }
    @Override
    public void onBackPressed()
    {
        finish();
    }
}
