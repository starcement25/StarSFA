package com.forcepower.acedns.activity;

import androidx.appcompat.app.AlertDialog;
import androidx.fragment.app.FragmentActivity;

import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ListView;
import android.widget.Toast;

import com.forcepower.acedns.adapter.RouteDetailsApprovalAdapter;
import com.forcepower.acedns.bean.RouteDetailsAproval;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

import com.forcepower.acedns.R;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitRoutePlanChangeRequestAproval;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;

public class RoutePlanApprovalActivity extends FragmentActivity {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase mAceDnsDatabase;
    String httpResponse = "",status = "route_plan_approval";
    ContentValues values;
    int noRows = -1, noColumn = -1;
    String timeStamp = "";

    ListView lvRoutPlanList;
    ArrayList<RouteDetailsAproval> routeList = null;
    ArrayList<RoutePlanMasterDetails> routeList11 = null;

    Handler mHandler;
    ProgressDialog loader;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_route_plan_approval);

        lvRoutPlanList = findViewById(R.id.lvRoutPlanList);

        mContext = RoutePlanApprovalActivity.this;

        Button back = findViewById(R.id.back);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();



        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    RoutePlanApprovalActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            if(routeList.size()>0){
                                show(routeList);
                            }else{
                                Toast.makeText(mContext, "No Data", Toast.LENGTH_SHORT).show();
                            }
                        }
                    });
                }
            }
        };







       // getData();

        routeList = new ArrayList<>();
        RouteDetailsApprovalAdapter adapter1 = new RouteDetailsApprovalAdapter(RoutePlanApprovalActivity.this, R.layout.item_list_route_plan_approval, routeList);
        lvRoutPlanList.setAdapter(adapter1);

        lvRoutPlanList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                //Toast.makeText(mContext, ""+position, Toast.LENGTH_SHORT).show();
                AlertDialog.Builder builder = new AlertDialog.Builder(mContext);

                builder.setTitle("Confirm");
                builder.setMessage("Are you sure?");

                builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {

                    public void onClick(DialogInterface dialog, int which) {
                        // Do nothing but close the dialog
                        //Toast.makeText(mContext, "yes"+position, Toast.LENGTH_SHORT).show();
                        dialog.dismiss();
                    }
                });

                builder.setNegativeButton("NO", new DialogInterface.OnClickListener() {

                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        //Toast.makeText(mContext, "no"+position, Toast.LENGTH_SHORT).show();
                        // Do nothing
                        dialog.dismiss();
                    }
                });

                AlertDialog alert = builder.create();
                alert.show();
            }
        });


    }


    private void show(ArrayList<RouteDetailsAproval> expList1){

        //ListView dialogList = (ListView) findViewById(R.id.lvRoutPlanList);
        RouteDetailsApprovalAdapter adapter1 = new RouteDetailsApprovalAdapter(RoutePlanApprovalActivity.this, R.layout.item_list_route_plan_approval, expList1);
        lvRoutPlanList.setAdapter(adapter1);

       /* double sum = 0;
        for(int i = 0; i < expList1.size(); i++) {
            sum += Double.parseDouble(expList1.get(i).getTotal().toString());
        }
*/

    }

    private void getData(){
        new Thread() {
            public void run() {

                _DOWNLOAD_route_master_approval();

                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }

        }.start();
    }


    public void _DOWNLOAD_route_master_approval() {

        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.route_plan_change_request_data
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();

Log.d("_DOWNLOAD_", "_DOWNLOAD_ RoutePlanApprovalActivity: " + URL);
        Download_txt(URL);

        File csvFile = new File(Utils.getAppStoragePath(mContext)+ status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }
        routeList = new ArrayList<RouteDetailsAproval>();
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line = "";
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        RouteDetailsAproval temp = new RouteDetailsAproval();
                        temp.setRoute_plan_trans_id(RowData[0]);
                        temp.setEmp_code(RowData[1]);
                        temp.setRoute_code(RowData[2]);
                        temp.setVisit_date(RowData[3]);
                        temp.setCreate_date(RowData[4]);
                        temp.setRoute_name(RowData[7]);
                        temp.setEmp_name(RowData[9]);
                        temp.setRemarks(RowData[10]);

                        routeList.add(temp);
                        temp = null;
                    }
                }
            }

            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }


    }


    private void Download_txt(String URL) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        java.net.URL url = null;

        try {
            outputFile = new File(Utils.getAppStoragePath(mContext)+ status + ".txt");
            if (outputFile.exists())
                outputFile.delete();
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.setConnectTimeout(0);
            c.connect();
            is = c.getInputStream();
            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = is.read(buffer)) != -1) {
                fbo.write(buffer, 0, len1);
            }

            fbo.flush();

        } catch (Exception e) {
            e.printStackTrace();
        } finally {

            if (c != null)
                c.disconnect();
            if (fbo != null)
                try {
                    fbo.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            if (is != null)
                try {
                    is.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }

        }
    }

    public void sendApproval(int i){
        routeList11 = new ArrayList<>();
        //Toast.makeText(context, "yes"+position, Toast.LENGTH_SHORT).show();
        RoutePlanMasterDetails temp = new RoutePlanMasterDetails();
        temp.setTranId(routeList.get(i).getRoute_plan_trans_id());
        temp.setEmpCode(routeList.get(i).getEmp_code());
        temp.setRoutecode(routeList.get(i).getRoute_code());
        temp.setVisitDate(routeList.get(i).getVisit_date());
        temp.setCreateDate(routeList.get(i).getCreate_date());
        temp.setRouteName(routeList.get(i).getRoute_name());
        temp.setRemarks(routeList.get(i).getRemarks());

        routeList11.add(temp);

        new TRANS_SubmitRoutePlanChangeRequestAproval(RoutePlanApprovalActivity.this, true, "SUBMIT",routeList11).execute();
    }


}