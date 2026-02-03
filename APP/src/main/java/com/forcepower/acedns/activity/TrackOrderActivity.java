package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ExpandableListView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.ExpandableListAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.bean.CategoryClass;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.ItemDetailsClass;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.StarCementDB;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;
import org.json.JSONArray;
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
import java.util.Collections;
import java.util.List;

public class TrackOrderActivity extends AceDnsParentActivity
{
    public static TextView mTextViewCustomerName = null;
    TextView tvAppOrder, tvOfflineOrder, tvApporderLine, tvOfflineorderLine;
    ExpandableListView expListView;
    AceDnsDatabase mAceDnsDatabase ;
    StarCementDB starCementDB;
    ProgressDialog mStepProgressDialog;
    Handler mStepHandler;
    Context mContext;
    String mIsInCremental = "";
    ArrayList<CustomerDetails> mCustomerDetailsList;
    Button btnBack;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_track_order);

        mContext = TrackOrderActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        starCementDB = new StarCementDB(getApplicationContext());
        mTextViewCustomerName =  findViewById(R.id.textViewCustomerValue);
        /*Toolbar myToolbar = findViewById(R.id.my_toolbar);
        myToolbar.setTitleTextColor(Color.WHITE);
        myToolbar.setSubtitleTextColor(Color.WHITE);
        setSupportActionBar(myToolbar);
        getSupportActionBar().setTitle("TRACK ORDERS");
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);*/
        tvAppOrder =  findViewById(R.id.tvAppOrder);
        tvOfflineOrder =  findViewById(R.id.tvOfflineOrder);
        tvApporderLine =  findViewById(R.id.tvApporderLine);
        tvOfflineorderLine =  findViewById(R.id.tvOfflineorderLine);
        expListView =  findViewById(R.id.exlvTrackOrders);

        btnBack =  findViewById(R.id.back);

        tvAppOrder.setOnTouchListener(new View.OnTouchListener() {
            @Override
            public boolean onTouch(View v, MotionEvent event) {
                tvAppOrder.setTextColor(Color.RED);
                tvApporderLine.setBackgroundColor(Color.RED);

                tvOfflineOrder.setTextColor(Color.GRAY);
                tvOfflineorderLine.setBackgroundColor(Color.GRAY);
                FlowofCheckINAndOut(1);
                return false;
            }
        });
        tvOfflineOrder.setOnTouchListener((v, event) -> {
            tvOfflineOrder.setTextColor(Color.RED);
            tvOfflineorderLine.setBackgroundColor(Color.RED);

            tvAppOrder.setTextColor(Color.GRAY);
            tvApporderLine.setBackgroundColor(Color.GRAY);
            FlowofCheckINAndOut(2);
            return false;
        });
        if(HTTPUtils.isConnectionPossible(mContext))
        {
            new TRANS_Bqargain_Transaction_Asynctask(getApplicationContext()).execute("");
        }
        else
        {
            customerSelectionCommonProcess();
            Utils.closeApp(mContext,"Could not fetch latest data. Please check your internet connection.");
        }


        // Listview Group click listener
        expListView.setOnGroupClickListener(new ExpandableListView.OnGroupClickListener() {
            @Override
            public boolean onGroupClick(ExpandableListView parent, View v, int groupPosition,
                                        long id) {
                int childCountWay2 = parent.getExpandableListAdapter().getChildrenCount(groupPosition);
                if (childCountWay2<1)
                {
                    // do whatever you want
                    return true;
                }
                else
                {
                    return false;
                }
            }
        });

        btnBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        mStepHandler = new Handler() {
            public void handleMessage(Message msg) {
                mStepProgressDialog.dismiss();
                final int step = msg.getData().getInt("STEP");
                TrackOrderActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        expListView.setAdapter(mAdapter);
                    }
                });

            }
        };
    }
    public void customerSelectionCommonProcess()
    {
//        if (NotCheckedOut(mContext))
//        {
//            ṁrouteCustSelectionProcess();
//        }
        mCustomerDetailsList = mAceDnsDatabase.getCustomerListForTrackOrder();
        if (mCustomerDetailsList.size() > 1)
        {
            ShowCustomerListDialog();
        }
        else if (mCustomerDetailsList.size() == 1)
        {
            Constants.selectedCustomer = mCustomerDetailsList.get(0);
            mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
            orderStatusLoadingProcess();
        }
        else
        {
            Toast.makeText(mContext, "No customer to track order.", Toast.LENGTH_SHORT).show();
            finish();
        }
    }

    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title =  mDialogCustomer.findViewById(R.id.title);
        title.setText("Customers to check order status");

        EditText searchText = (EditText) mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setEmptyView(findViewById(R.id.empty_text_view));
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {

                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                Log.d("_DOWNLOAD_", "onItemClick: "+ adapterCust.getItem(arg2));
                Log.d("_DOWNLOAD_", "onItemClick: "+ Constants.selectedCustomer.getDnsCustCode());
                mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
                orderStatusLoadingProcess();

            }
        });

        Button addnewcustomer = (Button) mDialogCustomer.findViewById(R.id.btn_add);
        addnewcustomer.setText("ADD");
        addnewcustomer.setVisibility(View.GONE);

        Button back = (Button) mDialogCustomer.findViewById(R.id.back);
        back.setVisibility(View.VISIBLE);
        back.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                mDialogCustomer.cancel();
                finish();
            }
        });

        mDialogCustomer.show();
    }
    private void orderStatusLoadingProcess() {
        // setting list adapter
        if(HTTPUtils.isConnectionPossible(mContext))
        {
            new TRANS_GetOrderDetails_Asynctask(getApplicationContext()).execute("");
        }
        else
        {
            FlowofCheckINAndOut(1);
            Utils.closeApp(mContext,"You need to have an active internet connection to use this feature.");
        }
    }

   /* @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }*/
    private static CategoryClass createCategory(String name, String group_name, String Qty, String prod_desc,String erporderdt,String erporderno)
    {
        return new CategoryClass(name, group_name, Qty, prod_desc,erporderdt,erporderno);
    }

    public class TRANS_GetOrderDetails_Asynctask extends AsyncTask<String, Void, String>
    {
        Context mContext;
        public TRANS_GetOrderDetails_Asynctask(Context mContext) {
            this.mContext = mContext;
        }

        @Override
        protected void onPreExecute()
        {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(TrackOrderActivity.this.mContext);
            mStepProgressDialog.setMessage("Preparing Data.\nPlease wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();

        }
        @Override
        protected String doInBackground(String... params)
        {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
//                    String url = OrderStatusUrl + "C/0002199";
                    String url = AceDnsWebServiceURL.OrderStatusUrl + Constants.selectedCustomer.getDnsCustCode();
//                    String url = OrderStatusUrl + "A007";
                    //url = OrderStatusUrl + "WBD038";
                    Log.d("_DOWNLOAD_", "doInBackground: "+url);
                    POST_result = HttpCalling.httpGetCallWithXmlResponse(url, null);

                    mAceDnsDatabase.TruncateTableByTableName("T_APPERPDO");
                    mAceDnsDatabase.TruncateTableByTableName("T_DOCHALLAN");

                    JSONObject jo = new JSONObject(POST_result);
                    if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
                    {
                        String order_data = jo.getString("order_data");
                        JSONArray ja = new JSONArray(order_data);
                        if(ja.length()>0)
                        {
                            for(int i=0; i<ja.length(); i++) {
                                JSONObject e = ja.getJSONObject(i);

                                //insert to T_APPERPDO table
                                mAceDnsDatabase.insertOrderData(
                                        e.getString("apporderno"),
                                        e.getString("erporderno"),
                                        e.getString("customer_code"),
                                        e.getString("dns_customer_code"),
                                        e.getString("order_full_date_time"),
//                                    e.getString("order_challan_data"),
                                        e.getString("order_for"),
                                        e.getString("status"),
                                        e.getString("prod_code"),
                                        e.getString("dns_prod_code"),
                                        e.getString("prod_display_name"),
                                        e.getString("qty")
                                );

                                String order_challan_data = e.getString("order_challan_data");
                                JSONArray jaChallan = new JSONArray(order_challan_data);
                                if(jaChallan.length()>0)
                                {
                                for (int j = 0; j < jaChallan.length(); j++) {
                                    JSONObject eChallan = jaChallan.getJSONObject(j);

                                    mAceDnsDatabase.insertChallan(
                                            eChallan.getString("apporderno"),
                                            eChallan.getString("invdt"),
                                            eChallan.getString("invno"),
                                            eChallan.getString("invqty"),
                                            "",
                                            "",
                                            eChallan.getString("erporderno"),
                                            "",
                                            "",
                                            eChallan.getString("truckno"),
                                            eChallan.getString("prod_display_name")
                                    );
                                }
                            }
                            }
                        }
                        else
                        {
                            POST_result = "No Data";
                        }


                    }
                    else
                    {
                        POST_result = "No Data";
                    }

                }
                catch (Exception e)
                {
                    e.printStackTrace();
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }
        @Override
        protected void onPostExecute(String result)
        {
            super.onPostExecute(result);
            mStepProgressDialog.dismiss();

            if(result.equalsIgnoreCase("Network Failure"))
            {
                Toast.makeText(mContext, "Internet connectivity error, Please try again later.", Toast.LENGTH_SHORT).show();
            }
            else if(result.equalsIgnoreCase("No Data"))
            {
                Toast.makeText(mContext, "No Valid Data Found For Chosen Customer, Please try again", Toast.LENGTH_SHORT).show();
                finish();
            }
            else
            {
                FlowofCheckINAndOut(1);
            }
        }
    }

    public class TRANS_Bqargain_Transaction_Asynctask extends AsyncTask<String, Void, String>
    {

        Context mContext;

        int noRows = -1, noColumn = -1;
        String timeStamp = "";
        String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
        public TRANS_Bqargain_Transaction_Asynctask(Context mContext) {
            this.mContext = mContext;
            lastUpdate = mAceDnsDatabase.getlastDownloadTime("DO_transaction");
        }

        @Override
        protected void onPreExecute()
        {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(TrackOrderActivity.this.mContext);
            mStepProgressDialog.setMessage("Loading Data.\nPlease wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();

        }
        @Override
        protected String doInBackground(String... params)
        {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    InCrementalDownload();
                    String url = BaseUrl.baseUrl + AceDnsWebServiceURL.DownloadDoMasterStatusUrl
                            + "?nick_name=" + Constants.nickName
                            + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                            + "&incremental_download=" + mIsInCremental
                            + "&last_update_time=" + lastUpdate;
Log.d("_DOWNLOAD_", "_DOWNLOAD_ TrackOrderActivity: " + url);
                    downloader(url);

                    File csvFile = new File(Utils.getAppStoragePath(mContext) + "DO_transaction_status.txt");

                    FileReader file = null;
                    try {
                        file = new FileReader(csvFile);
                    } catch (FileNotFoundException e1) {
                        e1.printStackTrace();
                    }
                    ArrayList gitList = new ArrayList<commonDatabaseHelper>();
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
                                    commonDatabaseHelper temp = new commonDatabaseHelper();
                                    temp.setItem0(RowData[0]);
                                    temp.setItem1(RowData[8]);
                                    gitList.add(temp);

                                }
                            }
                        }
                        buffer.close();
                    } catch (IOException ex) {
                        ex.printStackTrace();
                    }

                    mAceDnsDatabase.UpdateDOTransactionStatus(gitList);
                }
                catch (Exception e)
                {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }
        @Override
        protected void onPostExecute(String result)
        {
            super.onPostExecute(result);
            mStepProgressDialog.dismiss();
            customerSelectionCommonProcess();

        }
    }
    public void InCrementalDownload() {
        if ((true == Constants.isFirstLoginOfApp)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }
    private void downloader(String urlstr) {

        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        URL url = null;

        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + "DO_transaction_status.txt");
            if (outputFile.exists())
                Log.e("File delete", outputFile.delete() + "");
            fbo = new FileOutputStream(outputFile, false);

            // connect with server where remote file is stored to download it
            url = new URL(urlstr);
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
                Log.e("length", len1 + "----");
            }

            fbo.flush();

        } catch (Exception e) {

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
            outputFile = null;

        }

    }
    public void setDataAPPORDER()
    {
        try
        {
            // setting list adapter
            List<CategoryClass> catList = new ArrayList<CategoryClass>();
            List<commonDatabaseHelper> category = mAceDnsDatabase.getAllAppOrderDetails();
            for (commonDatabaseHelper cat : category)
            {
                CategoryClass cat1 = createCategory(cat.getItem0(), cat.getItem1(), cat.getItem2(), cat.getItem3(),cat.getItem4(),cat.getItem5());
                List<commonDatabaseHelper> sub_category = mAceDnsDatabase.getSubCategoryAppOrder(cat.getItem0());
                List<ItemDetailsClass> result = new ArrayList<ItemDetailsClass>();

                for (commonDatabaseHelper scat : sub_category)
                {
                    //-------------------------------------------challanno, date, quantity, TrackNumber, DriverContact
                    ItemDetailsClass item = new ItemDetailsClass(scat.getItem0(), scat.getItem1(), scat.getItem2(), scat.getItem3(), scat.getItem4());
                    result.add(item);
                }
                cat1.setItemList(result);
                catList.add(cat1);
            }

//            Collections.reverse(catList);
            mAdapter = new ExpandableListAdapter(mContext, catList);
//            expListView.setAdapter(mAdapter);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    ExpandableListAdapter mAdapter;
    public void setDataOFFLINEORDER()
    {
        try
        {
            // setting list adapter
            List<CategoryClass> catList = new ArrayList<CategoryClass>();
            List<commonDatabaseHelper> category = mAceDnsDatabase.getAllOffLineOrderDetails();
            for (commonDatabaseHelper cat : category)
            {
                CategoryClass cat1 = createCategory(cat.getItem0(), cat.getItem1(), cat.getItem2(), cat.getItem3(),cat.getItem4(),cat.getItem5());
                List<commonDatabaseHelper> sub_category = mAceDnsDatabase.getSubCategoryOfflineOrder(cat.getItem0());
                List<ItemDetailsClass> result = new ArrayList<ItemDetailsClass>();

                for (commonDatabaseHelper scat : sub_category)
                {
                    //-------------------------------------------challanno, date, quantity, TrackNumber, DriverContact
                    ItemDetailsClass item = new ItemDetailsClass(scat.getItem0(), scat.getItem1(), scat.getItem2(), scat.getItem3(), scat.getItem4());
                    result.add(item);
                }
                cat1.setItemList(result);
                catList.add(cat1);
            }

            Collections.reverse(catList);
            mAdapter = new ExpandableListAdapter(this, catList);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void FlowofCheckINAndOut(final int step) {

        mStepProgressDialog = new ProgressDialog(mContext);
        mStepProgressDialog.setMessage("Preparing Data.\nPlease wait..");
        mStepProgressDialog.setCancelable(false);
        mStepProgressDialog.show();
        new Thread() {
            public void run() {
                switch (step) {
                    case 1:
                        setDataAPPORDER();
                        break;
                    case 2:
                        setDataOFFLINEORDER();
                        break;
                }
//                mStepProgressDialog.dismiss();
                Message msgObj = mStepHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("STEP", step);
                msgObj.setData(b);
                mStepHandler.sendMessage(msgObj);
            }
        }.start();
    }
}
