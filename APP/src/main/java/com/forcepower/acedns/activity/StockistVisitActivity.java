package com.forcepower.acedns.activity;

import static android.view.View.VISIBLE;
import static com.forcepower.acedns.constants.Constants.tempStokistRetailList;
import static com.forcepower.acedns.constants.Constants.tempStokistVisitlList;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import com.forcepower.acedns.backgroundTask.TRANS_Stokist_Visit_TransactionTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerBeatAdapter;
import com.forcepower.acedns.adapter.NewStokistVisitAdapter;
import com.forcepower.acedns.adapter.NewStokistVisitConfirmAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.StokistDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class StockistVisitActivity extends AceDnsParentActivity {

    public static Button mButtonBack = null, mButtonNoOrder = null, btnSubmit = null;
    Dialog stokistDialog;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    Button buttonType;
    Button buttonRoute;
    Button buttonStockist;
    Button buttonRetailer;
    ListView retailList;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    ArrayList<CustomerDetails> routeListForSearching;
    ConnectionDetector cd;

    ArrayList<CustomerDetails> tempCustomerList1;
    CustomerBeatAdapter adapterCust;

    ArrayList<StokistDetails> tempCustomerList;

    AceDnsTransactionDatabase dataHelperObj;

    String customerCode = "";
    String custType = "";
    String custRouteCode = "";
    String retailCode = "";
    String stockistCode = "";


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_stockist_visit);

        buttonStockist = findViewById(R.id.buttonStockist);
        buttonType = findViewById(R.id.buttonType);
        buttonRoute = findViewById(R.id.buttonRoute);
        buttonRetailer = findViewById(R.id.buttonRetailer);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setTag(105);
        mButtonBack.setOnClickListener(StockistVisitActivity.this);

        mButtonNoOrder = (Button) findViewById(R.id.no_ordr);
        mButtonNoOrder.setVisibility(View.GONE);

        btnSubmit = findViewById(R.id.buttonSubmit);

        mContext = StockistVisitActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        retailList = (ListView) findViewById(R.id.prodQtyRateListView);
        cd = new ConnectionDetector(mContext);

        tempStokistVisitlList = new ArrayList<>();
        buttonType.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                    showTypeListDialog();
                //String date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
                //Utils.showToast(mContext,date);
            }
        });
        buttonRoute.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showRouteListDialog();
                stockistCode = "";
                retailCode = "";
                //String date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
                //Utils.showToast(mContext,date);
            }
        });
        buttonStockist.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(!custRouteCode.matches("")){
                    retailCode = "";
                    showStokistListDialog();
                }
                else{
                    Utils.showToast(mContext,"Please select route first");
                }

                //String date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
                //Utils.showToast(mContext,date);
            }
        });
        buttonRetailer.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if(!custRouteCode.matches("")){
                    if(!(buttonStockist.getText().toString().contains("Select Stockist"))){
                        showRetailerListDialog(customerCode);
                    }else{
                        Utils.showToast(mContext,"Please select Stockist first");
                    }

                }
                else{
                    Utils.showToast(mContext,"Please select route first");
                }

                //String date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
                //Utils.showToast(mContext,date);
            }
        });

        btnSubmit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                //saveToDatabase();
                showRetailerSaleFolderListDialog();
            }
        });

        try {
            if(cd.isConnectingToInternet())
            PrepareCustomerData(1);
            else{
                Utils.showToast(mContext,"You need an active internet connection to use this feature...");
                finish();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();

            }
        };
    }
    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        new commonAsyncTaskMaster(mContext, "customer_master");

                        break;
                }

                Message msg = mHandlerPrepareSaudaData.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPrepareSaudaData.sendMessage(msg);
            }
        }.start();
    }
    public void onClick(View clkdView) {

        if (clkdView instanceof Button) {
            int tag = (Integer) clkdView.getTag();
            switch (tag) {


            }

        }

        if (clkdView == mButtonBack) {
            finish();
        }
    }

    public void showStokistListDialog() {
        final ArrayList<CustomerDetails> itemListBeforeSearch = mAceDnsDatabase.getStokistList(custRouteCode);
        final ArrayList<CustomerDetails> itemList = new ArrayList<>(itemListBeforeSearch);
//        routeListForSearching = new ArrayList<>(itemList);
        if (itemList.size() == 0) {
            Utils.showToast(mContext,"No Stockist Found");
            //bussinessArea(detailsObj.getRouteCode(), detailsObj.getRouteName());
        } else if (itemList.size() > 0) {
            stokistDialog = new Dialog(StockistVisitActivity.this, R.style.PauseDialog);
            stokistDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stokistDialog.setContentView(R.layout.select_from_list);
            stokistDialog.setCancelable(false);
            ImageView image_cancel = (ImageView) stokistDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    stokistDialog.cancel();
                }
            });
            TextView title = (TextView) stokistDialog.findViewById(R.id.title);
            title.setText("Please select stockist");
            ListView dialogList = (ListView) stokistDialog.findViewById(R.id.list);
            final CustomerBeatAdapter adapter1 = new CustomerBeatAdapter(StockistVisitActivity.this, R.layout.customer_list_child_stokist, itemList);
            dialogList.setAdapter(adapter1);

            EditText searchText = (EditText) stokistDialog
                    .findViewById(R.id.autoCompleteTextView1);

            searchText.setVisibility(VISIBLE);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    //adapter1.getFilter().filter(s.toString());
                    String searchString = searchText.getText().toString();
                    int textLength = searchString.length();

                    //clear the initial data set
                    itemList.clear();
                    for (int i = 0; i < itemListBeforeSearch.size(); i++) {
                        String routeName = itemListBeforeSearch.get(i).getCustomerName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                itemList.add(itemListBeforeSearch.get(i));
                            }
                        }
                    }
                    adapter1.notifyDataSetChanged();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    adapter1.notifyDataSetChanged();
                }
            });

            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    stokistDialog.cancel();
                    CustomerDetails detailsObj = itemList.get(arg2);
                    String routeName = detailsObj.getCustomerName();
                    buttonStockist.setText(routeName);
                    customerCode = detailsObj.getCustomerCode();
                    stockistCode = detailsObj.getCustomerCode();
                    retailCode = "";
                    buttonRetailer.setText("Select Dealer");
                    //bussinessArea(detailsObj.getRouteCode(), routeName);
                    //Utils.showToast(mContext,detailsObj.getCustomerCode());

                    //Retail(customerCode);
                   // routeCode = detailsObj.getRouteCode();
                    //showCustomer(detailsObj.getRouteCode());

                }
            });


            Button cancel = (Button) stokistDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    stokistDialog.cancel();
                }
            });

            Button create_route = (Button) stokistDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            create_route.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    //stokistDialog();
                    stokistDialog.cancel();
                }
            });

            stokistDialog.show();
        } else {
            Utils.showToast(StockistVisitActivity.this,
                    "There is no predefined route");
        }

    }
    public void showTypeListDialog() {
        final ArrayList<CustomerDetails> itemList = mAceDnsDatabase.geCustTypeList();
//        routeListForSearching = new ArrayList<>(itemList);
        if (itemList.size() == 0) {
            Utils.showToast(mContext,"No Type Found");
            //bussinessArea(detailsObj.getRouteCode(), detailsObj.getRouteName());
        } else if (itemList.size() > 0) {
            stokistDialog = new Dialog(StockistVisitActivity.this, R.style.PauseDialog);
            stokistDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stokistDialog.setContentView(R.layout.select_from_list);
            stokistDialog.setCancelable(false);
            ImageView image_cancel = (ImageView) stokistDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    stokistDialog.cancel();
                }
            });
            TextView title = (TextView) stokistDialog.findViewById(R.id.title);
            title.setText("Please select customer type");
            ListView dialogList = (ListView) stokistDialog.findViewById(R.id.list);
            final CustomerBeatAdapter adapter1 = new CustomerBeatAdapter(StockistVisitActivity.this, R.layout.customer_list_child_stokist, itemList);
            dialogList.setAdapter(adapter1);

            EditText searchText = (EditText) stokistDialog.findViewById(R.id.autoCompleteTextView1);

            searchText.setVisibility(View.GONE);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    //adapter1.getFilter().filter(s.toString());
                    String searchString = searchText.getText().toString();
                    int textLength = searchString.length();

                    //clear the initial data set
                    itemList.clear();
                    for (int i = 0; i < routeListForSearching.size(); i++) {
                        String routeName = routeListForSearching.get(i).getRouteName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                itemList.add(routeListForSearching.get(i));
                            }
                        }
                    }
                    adapter1.notifyDataSetChanged();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    adapter1.notifyDataSetChanged();
                }
            });

            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    stokistDialog.cancel();
                    CustomerDetails detailsObj = itemList.get(arg2);
                    custType = detailsObj.getCustomerName();//this is actually cust type
                    buttonType.setText(custType);
                    buttonRoute.setText("Select Route");
                    buttonStockist.setText("Select Stockist");
                    custRouteCode="";
                    customerCode="";
                    //bussinessArea(detailsObj.getRouteCode(), routeName);
                    //Utils.showToast(mContext,detailsObj.getCustomerCode());

//                    Retail(customerCode);
                    // routeCode = detailsObj.getRouteCode();
                    //showCustomer(detailsObj.getRouteCode());

                }
            });


            Button cancel = (Button) stokistDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    stokistDialog.cancel();
                }
            });

            Button create_route = (Button) stokistDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            create_route.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    //stokistDialog();
                    stokistDialog.cancel();
                }
            });

            stokistDialog.show();
        } else {
            Utils.showToast(StockistVisitActivity.this,
                    "There is no predefined route");
        }

    }
    public void showRouteListDialog() {
        final ArrayList<CustomerDetails> itemListBeforeSearch = mAceDnsDatabase.geRouteCodeRouteNameStockistList();
        final ArrayList<CustomerDetails> itemList = new ArrayList<>(itemListBeforeSearch);
//        routeListForSearching = new ArrayList<>(itemList);
        if (itemList.size() == 0) {
            Utils.showToast(mContext,"No Route Found");
            //bussinessArea(detailsObj.getRouteCode(), detailsObj.getRouteName());
        } else if (itemList.size() > 0) {
            stokistDialog = new Dialog(StockistVisitActivity.this, R.style.PauseDialog);
            stokistDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stokistDialog.setContentView(R.layout.select_from_list);
            stokistDialog.setCancelable(false);
            ImageView image_cancel = (ImageView) stokistDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    stokistDialog.cancel();
                }
            });
            TextView title = (TextView) stokistDialog.findViewById(R.id.title);
            title.setText("Please select route");
            ListView dialogList = (ListView) stokistDialog.findViewById(R.id.list);
            final CustomerBeatAdapter adapter1 = new CustomerBeatAdapter(StockistVisitActivity.this, R.layout.customer_list_child_stokist, itemList);
            dialogList.setAdapter(adapter1);

            EditText searchText = (EditText) stokistDialog.findViewById(R.id.autoCompleteTextView1);

            searchText.setVisibility(VISIBLE);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    //adapter1.getFilter().filter(s.toString());
                    String searchString = searchText.getText().toString();
                    int textLength = searchString.length();

                    //clear the initial data set
                    itemList.clear();
                    for (int i = 0; i < itemListBeforeSearch.size(); i++) {
                        String routeName = itemListBeforeSearch.get(i).getCustomerName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                itemList.add(itemListBeforeSearch.get(i));
                            }
                        }
                    }
                    adapter1.notifyDataSetChanged();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    adapter1.notifyDataSetChanged();
                }
            });

            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    stokistDialog.cancel();
                    CustomerDetails detailsObj = itemList.get(arg2);
                    String routeName = detailsObj.getCustomerName();
                    custRouteCode = detailsObj.getCustomerCode();
                    buttonRoute.setText(routeName);
                    buttonStockist.setText("Select Stockist");
                    customerCode="";
                    buttonRetailer.setText("Select Dealer");
                    retailCode = "";
                    //bussinessArea(detailsObj.getRouteCode(), routeName);
                    //Utils.showToast(mContext,detailsObj.getCustomerCode());

//                    Retail(customerCode);
                    // routeCode = detailsObj.getRouteCode();
                    //showCustomer(detailsObj.getRouteCode());

                }
            });


            Button cancel = (Button) stokistDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    stokistDialog.cancel();
                }
            });

            Button create_route = (Button) stokistDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            create_route.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    //stokistDialog();
                    stokistDialog.cancel();
                }
            });

            stokistDialog.show();
        } else {
            Utils.showToast(StockistVisitActivity.this,
                    "There is no predefined route");
        }

    }
    NewStokistVisitAdapter adapterStokist;
    private void Retail(String stockist_code){
        //final ArrayList<CustomerDetails> tempCustomerList = mAceDnsDatabase.getStokistRetailList(""+stockist_code,custType,custRouteCode);
        final ArrayList<CustomerDetails> tempCustomerList = mAceDnsDatabase.getStokistProductList();
        tempStokistRetailList = new ArrayList<>(tempCustomerList);
        ListView dialogList = (ListView) findViewById(R.id.prodQtyRateListView);
        adapterStokist = new NewStokistVisitAdapter(StockistVisitActivity.this, R.layout.list_item__stokist_sale_folder_input_material, tempStokistRetailList);
        dialogList.setAdapter(adapterStokist);

        dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                CustomerDetails detailsObj = tempCustomerList1.get(position);
                String routeName = detailsObj.getCustomerName();
               // Utils.showToast(mContext,detailsObj.getCustomerCode());
            }
        });

    }

    private void saveToDatabase(){

        String timeStamp = "";
            timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

        /*timeStamp="20161126145721";*/

        boolean isTransactionIDExist = false;
        do {
            isTransactionIDExist = dataHelperObj.IsTransactioIdExist(timeStamp);
            if (true == isTransactionIDExist) {
                timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
            }
        }
        while (true == isTransactionIDExist);

        if(tempStokistVisitlList.size()>0) {

            boolean r = false;
            for(int i=0; i<tempStokistVisitlList.size();i++) {
                StokistDetails sd = tempStokistVisitlList.get(i);

                r = dataHelperObj.AddStokistVisit(timeStamp,""+sd.getStokistCode(),sd.getCustomerCode(),sd.getSale(),sd.getFolder(),sd.getProd_code());
                //Utils.showToast(mContext, sd.getStokistCode() + "-" + sd.getSale().toString());
            }

            if(r==true){
                dataHelperObj.insertToLocationTable1("SV", timeStamp);
            }

            if(HTTPUtils.isConnectionPossible(mContext))
            {
                //new TRANS_Stokist_Visit_TransactionTask().s

                ArrayList<Location> unUploadedTransactionDR = dataHelperObj.getUnuploadedTransaction("stockist_visit", "");
                TRANS_Stokist_Visit_TransactionTask sb2 = new TRANS_Stokist_Visit_TransactionTask(StockistVisitActivity.this, unUploadedTransactionDR);
                sb2.execute();

                finish();

            }else {

                finish();
            }
        }else{
            Utils.showToast(mContext, "Please add minimum one");
        }

    }

    public void showRetailerListDialog(String stockist_code) {
        final ArrayList<CustomerDetails> itemListBeforeSearch = mAceDnsDatabase.getStokistList(custRouteCode);
        //final ArrayList<CustomerDetails> itemList = new ArrayList<>(itemListBeforeSearch);
//        routeListForSearching = new ArrayList<>(itemList);
        final ArrayList<CustomerDetails> tempCustomerList = mAceDnsDatabase.getStokistRetailList(""+stockist_code,custType,custRouteCode);
        final ArrayList<CustomerDetails> itemList = new ArrayList<>(tempCustomerList);
        if (itemList.size() == 0) {
            Utils.showToast(mContext,"No Dealer Found");
            //bussinessArea(detailsObj.getRouteCode(), detailsObj.getRouteName());
        } else if (itemList.size() > 0) {
            stokistDialog = new Dialog(StockistVisitActivity.this, R.style.PauseDialog);
            stokistDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stokistDialog.setContentView(R.layout.select_from_list);
            stokistDialog.setCancelable(false);
            ImageView image_cancel = (ImageView) stokistDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    stokistDialog.cancel();
                }
            });
            TextView title = (TextView) stokistDialog.findViewById(R.id.title);
            title.setText("Please select Dealer");
            ListView dialogList = (ListView) stokistDialog.findViewById(R.id.list);
            final CustomerBeatAdapter adapter1 = new CustomerBeatAdapter(StockistVisitActivity.this, R.layout.customer_list_child_stokist, itemList);
            dialogList.setAdapter(adapter1);

            EditText searchText = (EditText) stokistDialog
                    .findViewById(R.id.autoCompleteTextView1);

            searchText.setVisibility(VISIBLE);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2,
                                          int arg3) {
                    //adapter1.getFilter().filter(s.toString());
                    String searchString = searchText.getText().toString();
                    int textLength = searchString.length();

                    //clear the initial data set
                    itemList.clear();
                    for (int i = 0; i < itemListBeforeSearch.size(); i++) {
                        String routeName = itemListBeforeSearch.get(i).getCustomerName(); // it should be 'provider'..because we are use common code from Taxonomy
                        if (textLength <= routeName.length()) {
                            //compare the String in EditText with Names in the ArrayList
                            //if(searchString.equalsIgnoreCase(routeName.substring(0,textLength)))
                            if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                itemList.add(itemListBeforeSearch.get(i));
                            }
                        }
                    }
                    adapter1.notifyDataSetChanged();
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1,
                                              int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    adapter1.notifyDataSetChanged();
                }
            });

            dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    stokistDialog.cancel();
                    CustomerDetails detailsObj = itemList.get(arg2);
                    String routeName = detailsObj.getCustomerName();
                    buttonRetailer.setText(routeName);
                    //customerCode = detailsObj.getCustomerCode();
                    retailCode = detailsObj.getCustomerCode();
                    //bussinessArea(detailsObj.getRouteCode(), routeName);
                    //Utils.showToast(mContext,detailsObj.getCustomerCode());

                    Retail(customerCode);
                    // routeCode = detailsObj.getRouteCode();
                    //showCustomer(detailsObj.getRouteCode());

                }
            });


            Button cancel = (Button) stokistDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    stokistDialog.cancel();
                }
            });

            Button create_route = (Button) stokistDialog.findViewById(R.id.create_route);
            create_route.setVisibility(View.GONE);
            create_route.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    //stokistDialog();
                    stokistDialog.cancel();
                }
            });

            stokistDialog.show();
        } else {
            Utils.showToast(StockistVisitActivity.this,
                    "There is no predefined route");
        }

    }

    public void setList(String sal,String folder,String code,int pos,String prod_name){
        //tempStokistVisitlList
        if(retailCode.isEmpty()){
            Utils.showToast(StockistVisitActivity.this,
                    "Select Dealer");
        }else if(stockistCode.isEmpty()){
            Utils.showToast(StockistVisitActivity.this,
                    "Select Stockist");
        }else {
            StokistDetails sd = new StokistDetails();
            sd.setCustomerCode("" + retailCode);
            sd.setStokistCode("" + stockistCode);
            sd.setSale("" + sal);
            sd.setFolder("" + folder);
            sd.setProd_code("" + code);
            sd.setRetail_name(""+buttonRetailer.getText().toString());
            sd.setProd_name(""+prod_name);
            tempStokistVisitlList.add(sd);

            tempStokistRetailList.remove(pos);
            adapterStokist.notifyDataSetChanged();
        }
    }


    public void showRetailerSaleFolderListDialog() {
       final ArrayList<StokistDetails> itemList = new ArrayList<>(tempStokistVisitlList);
        if (itemList.size() == 0) {
            Utils.showToast(mContext,"No Record Found");
        } else if (itemList.size() > 0) {
            stokistDialog = new Dialog(StockistVisitActivity.this, R.style.PauseDialog);
            stokistDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            stokistDialog.setContentView(R.layout.cofirm_retail_folder_list);
            stokistDialog.setCancelable(false);
            ImageView image_cancel = (ImageView) stokistDialog.findViewById(R.id.image_cancel);
            image_cancel.setVisibility(VISIBLE);
            image_cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    stokistDialog.cancel();
                }
            });
            TextView title = (TextView) stokistDialog.findViewById(R.id.title);
            title.setText("Please Confirm Dealer");
            ListView dialogList = (ListView) stokistDialog.findViewById(R.id.list);
            final NewStokistVisitConfirmAdapter adapter1 = new NewStokistVisitConfirmAdapter(StockistVisitActivity.this, R.layout.customer_confirm_list_child_stokist, itemList);
            dialogList.setAdapter(adapter1);


            Button cancel = (Button) stokistDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(VISIBLE);
            cancel.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    saveToDatabase();
                    stokistDialog.cancel();
                }
            });


            stokistDialog.show();
        } else {
            Utils.showToast(StockistVisitActivity.this,
                    "There is no record");
        }

    }


}