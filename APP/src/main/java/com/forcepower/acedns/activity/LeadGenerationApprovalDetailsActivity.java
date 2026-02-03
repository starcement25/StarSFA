package com.forcepower.acedns.activity;

import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.LeadGenerationListDetailsAdapter;
import com.forcepower.acedns.adapter.SiteLeadApprovalAdapter;
import com.forcepower.acedns.bean.CommonHelper;
import com.forcepower.acedns.bean.SiteLeadApproval;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

import org.json.JSONObject;

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

public class LeadGenerationApprovalDetailsActivity extends AceDnsParentActivity {


    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;
    public Context mContext;
    Button mButtonBack,btn_closed,btn_accept,btn_revision,btn_new,buttonDateFrom,buttonDateTo,buttonDateSubmit;

    Handler mHandler;
    ProgressDialog loader;

    String httpResponse = "",status = "site_lead_approval",list_status="pending";

    ListView lvRoutPlanList;
    ArrayList<CommonHelper> routeList = null;

    int noRows = -1, noColumn = -1;
    String timeStamp = "",action_on_lead = "new",sms="process_message";

    int type = 0;
    String lead_id="",su_id="",action_on_lead_update;
    EditText txt_price;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_lead_generation_approval_details);

        mContext = LeadGenerationApprovalDetailsActivity.this;
        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setOnClickListener(LeadGenerationApprovalDetailsActivity.this);

        btn_accept = (Button) findViewById(R.id.btn_accept);
        btn_accept.setOnClickListener(LeadGenerationApprovalDetailsActivity.this);
        btn_closed = (Button) findViewById(R.id.btn_closed);
        btn_closed.setOnClickListener(LeadGenerationApprovalDetailsActivity.this);
        btn_revision = (Button) findViewById(R.id.btn_revision);
        btn_revision.setOnClickListener(LeadGenerationApprovalDetailsActivity.this);

        txt_price = findViewById(R.id.txt_price);


        lead_id=getIntent().getStringExtra("id");
        su_id = getIntent().getStringExtra("s_id");;//"SU"+lead_id.substring(1, lead_id.length());
        //Utils.showToast(mContext,su_id);
        lvRoutPlanList = findViewById(R.id.lvRoutPlanList);
        routeList = new ArrayList<CommonHelper>();

        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();



        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("JobDone")) {
                    loader.cancel();
                    LeadGenerationApprovalDetailsActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            if(routeList.size()>0){
                                show(routeList);
                                list_status="pending";
                                //btn_pending.setBackground(getDrawable(R.drawable.norm_btn_bg));
                            }else{
                                Toast.makeText(mContext, "No Data", Toast.LENGTH_SHORT).show();
                            }
                        }
                    });
                }
            }
        };

 /*       mHandler = new Handler() {
            public void handleMessage(Message threadmsg) {
                loader.cancel();
                final int dojob = threadmsg.getData().getInt("JobDone22");
                LeadGenerationApprovalDetailsActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (dojob) {
                            case 1:
                                try{
                                    if(routeList.size()>0){

                                        show(routeList);
                                        list_status="pending";
                                        //btn_pending.setBackground(getDrawable(R.drawable.norm_btn_bg));
                                    }else{
                                        Toast.makeText(mContext, "No Data", Toast.LENGTH_SHORT).show();
                                    }

                                }catch (Exception e){

                                }

                                break;
                            case 2:
                                try{
                                    Utils.showToast(mContext,sms);
                                }catch (Exception e){

                                }

                                break;
                        }
                    }
                });
            }
        };
*/
        getData(1);

    }

    public void onClick(View clkdView) {

        if (clkdView == mButtonBack) {
            finish();
        }
        if (clkdView == btn_accept) {

            if(!txt_price.getText().toString().isEmpty() && !txt_price.getText().toString().equalsIgnoreCase("0")) {
                loader = new ProgressDialog(mContext);
                loader.setMessage("Updating Data.Please wait..");
                loader.show();
                action_on_lead_update="Accept";
                getData(2);
            }else{
                Toast.makeText(mContext, "Enter Approve Price", Toast.LENGTH_SHORT).show();
            }
        }
        if (clkdView == btn_revision) {
            loader = new ProgressDialog(mContext);
            loader.setMessage("Updating Data.Please wait..");
            loader.show();
            action_on_lead_update="Revision";
            getData(2);
        }
        if (clkdView == btn_closed) {
            loader = new ProgressDialog(mContext);
            loader.setMessage("Updating Data.Please wait..");
            loader.show();
            action_on_lead_update="Closed";
            getData(2);
        }
    }

    private void getData(final int task){
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        //new commonAsyncTaskMaster(mContext, "customer_master");
                        _DOWNLOAD_SITE_LEAD_APPROVAL_MASTER();
                        break;

                    case 2:
                        //new commonAsyncTaskMaster(mContext, "customer_master");
                        _UpdateLEadGenerationDetails();
                        break;
                }



                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "JobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }

        }.start();
    }

    public void _DOWNLOAD_SITE_LEAD_APPROVAL_MASTER() {

        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.lead_generation_download_detailsUrl
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&suid=" + su_id;

Log.d("_DOWNLOAD_", "_DOWNLOAD_ LeadGenerationApprovalDetailsActivity: " + URL);
        Download_txt(URL);

        File csvFile = new File(Utils.getAppStoragePath(mContext)+ status + ".txt");
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }
        routeList = new ArrayList<CommonHelper>();
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
                        CommonHelper temp = new CommonHelper();
                        temp.setItem0(RowData[0]);
                        temp.setItem1(RowData[1]);


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

    private void show(ArrayList<CommonHelper> expList1){
        LeadGenerationListDetailsAdapter adapter1 = new LeadGenerationListDetailsAdapter(LeadGenerationApprovalDetailsActivity.this, R.layout.item_list_lg_details_approval, expList1);
        lvRoutPlanList.setAdapter(adapter1);

    }

    public void _UpdateLEadGenerationDetails() {
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.lead_generation_action_updateUrl
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&action_on_lead_update=" + action_on_lead_update
                + "&txt_price="+txt_price.getText().toString()
                + "&suid=" + lead_id;
        httpResponse = HttpCalling.httpPostCallWithXmlBodyJsonResponseDecrypted(URL, "");
Log.d("_DOWNLOAD_", "_DOWNLOAD_ LeadGenerationApprovalDetailsActivity: " + URL);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ LeadGenerationApprovalDetailsActivity result: " + httpResponse);
        if (httpResponse.length() > 0 && !httpResponse.equalsIgnoreCase("Network Failure")) {

            if (httpResponse != null) {
                JSONObject obj = null;
                try {
                    obj = new JSONObject(httpResponse);
                    ;
                    if (obj.getString("process_status").equals("YES")) {
                        sms=obj.getString("process_message");
                        finish();
                    }
                } catch (Exception e) {
                    //throw new RuntimeException(e);
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                //new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }
}