package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitCustomerFeedback;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.CustomerAdapter;
import com.forcepower.acedns.adapter.ProductBrandAdapter;
import com.forcepower.acedns.adapter.ProductGrpAdapter;
import com.forcepower.acedns.adapter.ProductMasterAdapter;
import com.forcepower.acedns.adapter.ProductSubGrpAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.adapter.RoutePlanTransAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.ProductBrandDetails;
import com.forcepower.acedns.bean.ProductGroupDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.ProductSubGrpDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.RoutePlanMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class CustomerFeedbackActivity extends AceDnsParentActivity {

    ImageView headerLogo;
    Button btnContinue, btnBack, btnCustomer;
    LinearLayout filterLayout;
    Dialog customerListDialog, grpDialog, subGrpDialog, brandDialog, masterDialog, routePlanListDialog, routeDialog, customerAddDialog;
    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    ProductGroupDetails selectedGrp;
    ProductSubGrpDetails selectedSubGrp;
    ProductBrandDetails selectedBrand;
    ArrayList<String> filterList;
    ArrayList<ProductGroupDetails> productGroupList;
    ArrayList<ProductSubGrpDetails> productSubGroupList;
    ArrayList<ProductBrandDetails> productBrandList;
    ArrayList<ProductMasterDetails> productMasterList;
    ArrayList<Button> filterButtonList;
    ProductMasterDetails currentProductMasterObj;
    int filterNo;
    ProductMasterAdapter prodAdapter;
    ArrayList<ProductMasterDetails> tempProductList;
    int lastProdPos = 0;
    ProgressDialog ploader;
    Handler orderDataHandler, saveHandler;
    RouteDetails selectedRoute;
    RoutePlanMasterDetails selectedRoutePlan;
    String routeCode = "", routeName = "";
    ArrayList<CustomerDetails> customerList, tempCustomerList;
    CustomerAdapter adapterCust;
    String lastStr = "";
    int chosenCustPos;
    ProgressDialog loader;


    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_customer_feedback);
        RegisterActivities.registerActivity(this);

        filterNo = Integer.parseInt(Constants.productDetailsObj.getNoFilter());
        mContext = CustomerFeedbackActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);

        filterButtonList = new ArrayList<Button>();

        initView();
        drawFilterLayout();
        String currentDate = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);


        if (Constants.menuDetailsObj.getRoutePlan().equalsIgnoreCase("yes")) {
            ArrayList<RoutePlanMasterDetails> todayPlanList = transDataHelperObj.getPlanForToday(currentDate);
            if (todayPlanList.size() == 1) {
                selectedRoutePlan = todayPlanList.get(0);
                routeCode = selectedRoutePlan.getRoutecode();
                routeName = selectedRoutePlan.getRouteName();
                customerList = setupDataHelperObj.getCustomerListByRoute(selectedRoutePlan.getRoutecode());
                if (customerList.size() > 0) {
                    tempCustomerList = new ArrayList<CustomerDetails>();
                    reInitialiseCustomerList();
                    adapterCust = new CustomerAdapter(CustomerFeedbackActivity.this, R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found. Please add new Customer.", Toast.LENGTH_LONG).show();
                    showAddCustomerDialog();
                }
            } else {
                showRoutePlanListDialog(todayPlanList);  //RoutePlanMaster will contain data thats why we are here from Menu page.
            }
        } else {
            // No concept of Route Plan. Show all customer.
            ArrayList<RouteDetails> routeList = setupDataHelperObj.getRouteList();
            if (routeList.size() == 1) {
                selectedRoute = routeList.get(0);
                routeCode = selectedRoute.getRouteCode();
                routeName = selectedRoute.getRouteName();
                customerList = setupDataHelperObj.getCustomerListByRoute(selectedRoute.getRouteCode());
                if (customerList.size() > 0) {
                    tempCustomerList = new ArrayList<CustomerDetails>();
                    reInitialiseCustomerList();
                    adapterCust = new CustomerAdapter(CustomerFeedbackActivity.this, R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found. Please add new Customer.", Toast.LENGTH_LONG).show();
                    showAddCustomerDialog();
                }
            } else if (routeList.size() > 1) {
                showRouteListDialog(routeList);
            }
        }


        orderDataHandler = new Handler() {
            public void handleMessage(Message msg) {
                ploader.cancel();
                final int jobToDo = msg.getData().getInt("WHAT TO SHOW");
                CustomerFeedbackActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                showGrpListDialog();
                                break;
                            case 2:
                                showSubGrpListDialog();
                                break;
                            case 3:
                                showBrandListDialog();
                                break;
                            case 4:
                                showMasterListDialog();
                                break;
                        }
                    }
                });
            }
        };

        saveHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("ProspectJobDone")) {
                    loader.cancel();
                    CustomerFeedbackActivity.this.runOnUiThread(new Runnable() {
                        public void run() {
                            new TRANS_SubmitCustomerFeedback(CustomerFeedbackActivity.this, true).execute();
                        }
                    });
                }
            }
        };
    }

    /*
     *        :::::::::::::::::::::::::::::::::::   VIEW RELATED OPERATIONS    ::::::::::::::::::::::::::::::::::::::::::::::
     */

    public void initView() {
        headerLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            headerLogo.setVisibility(View.VISIBLE);
            headerLogo.setImageBitmap(Constants.logoBmp);
        } else {
            headerLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        filterLayout = (LinearLayout) findViewById(R.id.filter_layout);


        btnContinue = (Button) findViewById(R.id.btn_continue);
        btnContinue.setEnabled(false);
        btnContinue.setOnClickListener(CustomerFeedbackActivity.this);
        btnContinue.setTag(101);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setTag(105);
        btnBack.setOnClickListener(CustomerFeedbackActivity.this);

        btnCustomer = (Button) findViewById(R.id.select_customer);
        btnCustomer.setTag(107);
        btnCustomer.setOnClickListener(CustomerFeedbackActivity.this);
    }


    public void drawFilterLayout() {
        filterList = setupDataHelperObj.getFilterList();   //filterList = [4(filter_no),dadu,baba,NA,chhele]
        for (int ii = 1; ii < filterList.size(); ii++) {
            if (!filterList.get(ii).equalsIgnoreCase("NA")) {
                LinearLayout.LayoutParams buttonLayoutParams = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT, 1);
                LinearLayout buttonLayout = new LinearLayout(mContext);
                buttonLayout.setLayoutParams(buttonLayoutParams);
                Button filterButton = new Button(mContext);
                LinearLayout.LayoutParams buttonParams = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
                buttonParams.gravity = Gravity.CENTER_VERTICAL;
                filterButton.setLayoutParams(buttonParams);
                filterButton.setTag(ii);
                filterButton.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
                filterButton.setText(filterList.get(ii));
                filterButton.setOnClickListener(this);
                filterButton.setEnabled(false);
                buttonLayout.addView(filterButton);
                filterLayout.addView(buttonLayout);
                filterButtonList.add(filterButton);
            } else {
                filterButtonList.add(null);
            }
        }
    }


    /*
     *      :::::::::::::::::::::::::::::::    LISTNER METHODS  ::::::::::::::::::::::::::::::::::::::::::
     */

    public void onClick(View clkdView) {
        if (clkdView instanceof Button) {
            int tag = (Integer) clkdView.getTag();
            switch (tag) {
                case 1:
                    switch (filterNo) {
                        case 2:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 3:
                            lastProdPos = 0;
                            filterButtonList.get(1).setEnabled(true);
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 4:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            filterButtonList.get(2).setEnabled(true);
                            filterButtonList.get(1).setEnabled(true);
                            break;
                    }
                    prepareOrderData(1, "");
                    break;
                case 2:
                    switch (filterNo) {
                        case 3:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            break;
                        case 4:
                            lastProdPos = 0;
                            filterButtonList.get(3).setEnabled(true);
                            filterButtonList.get(2).setEnabled(true);
                            break;
                    }
                    if (selectedGrp != null) {
                        prepareOrderData(2, selectedGrp.getGroupCode());
                    } else {
                        Toast.makeText(CustomerFeedbackActivity.this, "Please select the parent category", 2000);
                    }
                    break;
                case 3:
                    lastProdPos = 0;
                    filterButtonList.get(3).setEnabled(true);
                    if (selectedSubGrp != null) {
                        prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                    } else {
                        Toast.makeText(CustomerFeedbackActivity.this, "Please select the parent category", 2000);
                    }
                    break;
                case 4:
                    switch (filterNo) {
                        case 1:
                            prepareOrderData(4, "");
                            break;
                        case 2:
                            if (selectedGrp != null) {
                                prepareOrderData(4, selectedGrp.getGroupCode());
                            } else {
                                Toast.makeText(CustomerFeedbackActivity.this, "Please select the parent category", Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 3:
                            if (selectedSubGrp != null) {
                                prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            } else {
                                Toast.makeText(CustomerFeedbackActivity.this, "Please select the parent category", Toast.LENGTH_LONG).show();
                            }
                            break;
                        case 4:
                            if (selectedBrand != null) {
                                prepareOrderData(4, selectedBrand.getBrandCode());
                            } else {
                                Toast.makeText(CustomerFeedbackActivity.this, "Please select the parent category", Toast.LENGTH_LONG).show();
                            }
                            break;
                    }
            }
        }
        if (clkdView == btnContinue) {
            showInstructionDialog();
        }

        if (clkdView == btnBack) {
            finish();
        }
        if (clkdView == btnCustomer) {
            //showChooseCustomerDialog();
        }
    }
    /*
     *           ::::::::::::::::::::::::::::::           CREATING DIFFERENT DIALOGs             :::::::::::::::::::::::::::::::::
     */


    public void showGrpListDialog() {
        if (productGroupList.size() > 0) {
            grpDialog = new Dialog(CustomerFeedbackActivity.this, R.style.PauseDialog);
            grpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            grpDialog.setContentView(R.layout.select_from_list);
            grpDialog.setTitle("Please select an option");
            grpDialog.setCancelable(false);
            TextView title = (TextView) grpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) grpDialog.findViewById(R.id.list);
            ProductGrpAdapter adapter1 = new ProductGrpAdapter(CustomerFeedbackActivity.this, R.layout.product_list_child, productGroupList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    grpDialog.cancel();
                    selectedGrp = productGroupList.get(arg2);
                    filterButtonList.get(0).setText(selectedGrp.getGroupName());

                    switch (filterNo) {
                        case 2:
                            prepareOrderData(4, selectedGrp.getGroupCode());
                            break;
                        case 3:
                            prepareOrderData(2, selectedGrp.getGroupCode());
                            break;
                        case 4:
                            prepareOrderData(2, selectedGrp.getGroupCode());
                            break;
                    }

                }
            });
            Button cancel = (Button) grpDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    grpDialog.cancel();
                }
            });
            grpDialog.show();
        } else {
            Toast.makeText(CustomerFeedbackActivity.this, "There are no items left.Please submit order.", Toast.LENGTH_LONG).show();
        }
    }

    public void showSubGrpListDialog() {
        if (productSubGroupList.size() > 0) {
            subGrpDialog = new Dialog(CustomerFeedbackActivity.this, R.style.PauseDialog);
            subGrpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            subGrpDialog.setContentView(R.layout.select_from_list);
            subGrpDialog.setCancelable(false);
            TextView title = (TextView) subGrpDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) subGrpDialog.findViewById(R.id.list);
            ProductSubGrpAdapter adapter1 = new ProductSubGrpAdapter(CustomerFeedbackActivity.this, R.layout.product_list_child, productSubGroupList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    subGrpDialog.cancel();
                    selectedSubGrp = productSubGroupList.get(arg2);
                    filterButtonList.get(1).setText(selectedSubGrp.getSubGrpName());
                    switch (filterNo) {
                        case 3:
                            prepareOrderData(4, selectedSubGrp.getSubGrpCode());
                            break;
                        case 4:
                            prepareOrderData(3, selectedSubGrp.getSubGrpCode());
                            break;
                    }
                }
            });
            Button cancel = (Button) subGrpDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    subGrpDialog.cancel();
                }
            });
            subGrpDialog.show();
        } else {
            Toast.makeText(CustomerFeedbackActivity.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            Constants.selectedGroupList.add(selectedGrp);
            filterButtonList.get(1).setText("");
        }
    }

    public void showBrandListDialog() {
        if (productBrandList.size() > 0) {
            brandDialog = new Dialog(CustomerFeedbackActivity.this, R.style.PauseDialog);
            brandDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            brandDialog.setContentView(R.layout.select_from_list);
            brandDialog.setCancelable(false);
            TextView title = (TextView) brandDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            ListView dialogList = (ListView) brandDialog.findViewById(R.id.list);
            ProductBrandAdapter adapter1 = new ProductBrandAdapter(CustomerFeedbackActivity.this, R.layout.product_list_child, productBrandList);
            dialogList.setAdapter(adapter1);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    brandDialog.cancel();
                    selectedBrand = productBrandList.get(arg2);
                    filterButtonList.get(2).setText(selectedBrand.getBrandName());
                    prepareOrderData(4, selectedBrand.getBrandCode());
                }
            });
            Button cancel = (Button) brandDialog.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            cancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View arg0) {
                    brandDialog.cancel();
                }
            });
            brandDialog.show();
        } else {
            Toast.makeText(CustomerFeedbackActivity.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            Constants.selectedSubGroupList.add(selectedSubGrp);
            filterButtonList.get(2).setText("");
        }
    }

    public void showMasterListDialog() {
        if (productMasterList.size() > 0) {
            prodAdapter = new ProductMasterAdapter(CustomerFeedbackActivity.this, R.layout.product_list_child, tempProductList);
            masterDialog = new Dialog(CustomerFeedbackActivity.this, R.style.PauseDialog);
            masterDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            masterDialog.setContentView(R.layout.select_with_search);
            masterDialog.setCancelable(false);
            TextView title = (TextView) masterDialog.findViewById(R.id.title);
            title.setText("Please select an option");
            EditText searchText = (EditText) masterDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                    String str = s.toString();
                    if (lastStr.length() > str.length()) {
                        reInitialiseProductList();
                    }
                    lastStr = str;
                    filterProductArray(str.length(), str);
                    prodAdapter.notifyDataSetChanged();

                    System.out.println("String::::::::" + str);
                }
            });
            ListView dialogList = (ListView) masterDialog.findViewById(R.id.list);
            dialogList.setAdapter(prodAdapter);
            if (lastProdPos != 0) {
                dialogList.setSelection(lastProdPos - 1);
            } else {
                dialogList.setSelection(0);
            }
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    masterDialog.cancel();
                    lastProdPos = arg2;
                    currentProductMasterObj = tempProductList.get(arg2);
                    filterButtonList.get(3).setText(currentProductMasterObj.getDesc());
                }
            });
            Button btnCancel = (Button) masterDialog.findViewById(R.id.btn_ok);
            btnCancel.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    masterDialog.cancel();
                }
            });

            masterDialog.show();
        } else {
            Toast.makeText(CustomerFeedbackActivity.this, "There are no items left in this category. Please choose a different category", Toast.LENGTH_LONG).show();
            filterButtonList.get(3).setText("");
        }
    }



    /*
     *         :::::::::::::::::::::::::::::::  REMOVING REPEATED ITEMS FROM SELECTION LIST         :::::::::::::::::::::::::::::::
     */

    public String getFormatString(String unformatString) {
        String formatString = "";
        StringBuffer res = new StringBuffer();

        String[] strArr = unformatString.split(" ");
        for (String str : strArr) {
            char[] stringArray = str.trim().toCharArray();
            for (int ii = 0; ii < stringArray.length; ii++) {
                if (ii == 0) {
                    stringArray[ii] = Character.toUpperCase(stringArray[ii]);
                } else {
                    stringArray[ii] = Character.toLowerCase(stringArray[ii]);
                }
            }
            str = new String(stringArray);
            res.append(str).append(" ");
        }
        formatString = res.toString().trim();
        return formatString;
    }


    public void filterProductArray(int strCnt, String charVal) {
        int size = tempProductList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempProductList.get(ii).getDesc().length() >= strCnt) {
                if (tempProductList.get(ii).getDesc().toUpperCase().contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempProductList.remove(tempProductList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempProductList.remove(tempProductList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }


    public void prepareOrderData(final int doWhat, final String param) {
        ploader = new ProgressDialog(mContext);
        ploader.setMessage("Fetching Data.Please wait..");
        ploader.show();
        new Thread() {
            public void run() {
                switch (doWhat) {
                    case 1:
                        productGroupList = setupDataHelperObj.getProductGroupList(false);
                        break;
                    case 2:
                        productSubGroupList = setupDataHelperObj.getProductSubGroupList(param, false);
                        break;
                    case 3:
                        productBrandList = setupDataHelperObj.getProductBrandList(param, false);
                        break;
                    case 4:
                        productMasterList = setupDataHelperObj.getProductMasterListIgnoringStock(param, filterNo);
                        tempProductList = new ArrayList<ProductMasterDetails>();
                        reInitialiseProductList();
                        break;
                }
                Message msgObj = orderDataHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("WHAT TO SHOW", doWhat);
                msgObj.setData(b);
                orderDataHandler.sendMessage(msgObj);
            }
        }.start();
    }


    public void showRouteListDialog(final ArrayList<RouteDetails> routeList) {
        routeDialog = new Dialog(CustomerFeedbackActivity.this, R.style.PauseDialog);
        routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routeDialog.setContentView(R.layout.select_from_list);
        routeDialog.setCancelable(false);
        TextView title = (TextView) routeDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routeDialog.findViewById(R.id.list);
        RouteAdapter adapter1 = new RouteAdapter(CustomerFeedbackActivity.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                routeDialog.cancel();
                selectedRoute = routeList.get(arg2);
                routeCode = selectedRoute.getRouteCode();
                routeName = selectedRoute.getRouteName();
                customerList = setupDataHelperObj.getCustomerListByRoute(selectedRoute.getRouteCode());
                if (customerList.size() > 0) {
                    tempCustomerList = new ArrayList<CustomerDetails>();
                    reInitialiseCustomerList();
                    adapterCust = new CustomerAdapter(CustomerFeedbackActivity.this, R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found. Please add new Customer.", Toast.LENGTH_LONG).show();
                    showAddCustomerDialog();
                }
            }
        });
        Button cancel = (Button) routeDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routeDialog.cancel();
            }
        });
        Button create_route = (Button) routeDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routeDialog.show();
    }

    public void showRoutePlanListDialog(final ArrayList<RoutePlanMasterDetails> todayList) {
        routePlanListDialog = new Dialog(CustomerFeedbackActivity.this, R.style.PauseDialog);
        routePlanListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        routePlanListDialog.setContentView(R.layout.select_from_list);
        routePlanListDialog.setCancelable(false);
        TextView title = (TextView) routePlanListDialog.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = (ListView) routePlanListDialog.findViewById(R.id.list);
        RoutePlanTransAdapter adapter1 = new RoutePlanTransAdapter(CustomerFeedbackActivity.this, R.layout.route_list_child, todayList);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                routePlanListDialog.cancel();
                selectedRoutePlan = todayList.get(arg2);
                routeCode = selectedRoutePlan.getRoutecode();
                routeName = selectedRoutePlan.getRouteName();
                customerList = setupDataHelperObj.getCustomerListByRoute(selectedRoutePlan.getRoutecode());
                if (customerList.size() > 0) {
                    tempCustomerList = new ArrayList<CustomerDetails>();
                    reInitialiseCustomerList();
                    adapterCust = new CustomerAdapter(CustomerFeedbackActivity.this, R.layout.customer_list_child, tempCustomerList);
                    showChooseCustomerDialog();
                } else {
                    Toast.makeText(mContext, "No existing customer found. Please add new Customer.", Toast.LENGTH_LONG).show();
                    showAddCustomerDialog();
                }
            }
        });
        Button cancel = (Button) routePlanListDialog.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.INVISIBLE);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                routePlanListDialog.cancel();
            }
        });
        Button create_route = (Button) routePlanListDialog.findViewById(R.id.create_route);
        create_route.setVisibility(View.GONE);
        routePlanListDialog.show();
    }


    public void showChooseCustomerDialog() {
        customerListDialog = new Dialog(CustomerFeedbackActivity.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);
        TextView title = (TextView) customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = (EditText) customerListDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

                String str = s.toString();
                if (lastStr.length() > str.length()) {
                    reInitialiseCustomerList();
                }
                lastStr = str;
                filterCustomerArray(str.length(), str);
                adapterCust.notifyDataSetChanged();

                System.out.println("String::::::::" + str);
            }
        });

        ListView dialogList = (ListView) customerListDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                Constants.isSelectCustomer = true;
                CustomerDetails currentObj = tempCustomerList.get(arg2);
                for (int ii = 0; ii < customerList.size(); ii++) {
                    if (customerList.get(ii).getCustomerCode().equalsIgnoreCase(currentObj.getCustomerCode())) {
                        chosenCustPos = ii;
                    }
                }
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                customerListDialog.cancel();
                //custName.setText(customerList.get(chosenCustPos).getCustomerName());
                btnCustomer.setText(customerList.get(chosenCustPos).getCustomerName());
                btnCustomer.setEnabled(false);
                Constants.selectedCustomer = customerList.get(chosenCustPos);
                for (int i = 0; i < filterButtonList.size(); i++) {
                    if (filterButtonList.get(i) != null) {
                        filterButtonList.get(i).setEnabled(true);
                    }
                }
                btnContinue.setEnabled(true);
            }
        });

        Button addCustomer = (Button) customerListDialog.findViewById(R.id.btn_add);
        addCustomer.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                Constants.isSelectCustomer = false;
                customerListDialog.cancel();
                showAddCustomerDialog();
            }
        });

        customerListDialog.show();
    }


    public void showAddCustomerDialog() {
        Constants.isSelectCustomer = false;
        customerAddDialog = new Dialog(CustomerFeedbackActivity.this, R.style.PauseDialog);
        customerAddDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerAddDialog.setContentView(R.layout.add_customer_dialog);
        customerAddDialog.setCancelable(false);
        TextView title = (TextView) customerAddDialog.findViewById(R.id.title);
        title.setText("Please provide the details of the new Customer");
        final EditText edName = (EditText) customerAddDialog.findViewById(R.id.ed_name);
        final EditText edArea = (EditText) customerAddDialog.findViewById(R.id.ed_area);
        if (routeName.length() > 0) {
            edArea.setText(routeName);
            edArea.setEnabled(false);
        }
        final EditText edNumber = (EditText) customerAddDialog.findViewById(R.id.ed_number);
        final EditText edPin = (EditText) customerAddDialog.findViewById(R.id.ed_pin);

        LinearLayout tagLayout = (LinearLayout) customerAddDialog.findViewById(R.id.tag_layout);
        tagLayout.setVisibility(View.GONE);

        Button btnSubmit = (Button) customerAddDialog.findViewById(R.id.btn_submit);
        Button btnCancel = (Button) customerAddDialog.findViewById(R.id.btn_cancel);
        btnSubmit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String name = "", number = "", pin = "", custType = "";
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                name = edName.getText().toString();
                name = getFormatString(name);
                number = edNumber.getText().toString();
                pin = edPin.getText().toString();
                custType = "Retailor";
                if (name.length() > 0 && number.length() > 0 && pin.length() > 0) {
                    CustomerDetails newCustObj = new CustomerDetails();
                    newCustObj.setEmpCode(Constants.employeeDetailObject.getEmpCode());
                    newCustObj.setCustomerCode("N" + Constants.employeeDetailObject.getEmpCode() + timeStamp);
                    newCustObj.setCustomerName(name);
                    newCustObj.setAddress("");
                    newCustObj.setNumber(number);
                    newCustObj.setPin(pin);
                    newCustObj.setNewRouteouteCode(routeCode);
                    newCustObj.setNewRouteName(routeName);
                    newCustObj.setRemarks("");
                    newCustObj.setFlag("0");
                    newCustObj.setCustomerType(custType.equalsIgnoreCase("Distributor") ? "D" : "R");
                    Constants.selectedCustomer = newCustObj;
                    customerAddDialog.cancel();
                    btnCustomer.setText(name);
                    btnCustomer.setEnabled(false);
                    for (int i = 0; i < filterButtonList.size(); i++) {
                        if (filterButtonList.get(i) != null) {
                            filterButtonList.get(i).setEnabled(true);
                        }
                    }
                    btnContinue.setEnabled(true);
                } else {
                    Toast.makeText(mContext, "Provide Customer name,number and pin", Toast.LENGTH_LONG).show();
                }
            }
        });
        btnCancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                customerAddDialog.cancel();
            }
        });

        customerAddDialog.show();
    }


    public void reInitialiseCustomerList() {
        tempCustomerList.removeAll(tempCustomerList);
        int size = tempCustomerList.size();
        int size1 = customerList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < customerList.size(); kk++) {
            tempCustomerList.add(customerList.get(kk));
        }

    }

    public void filterCustomerArray(int strCnt, String charVal) {
        int size = tempCustomerList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempCustomerList.get(ii).getCustomerName().length() >= strCnt) {
                if (tempCustomerList.get(ii).getCustomerName().toUpperCase().contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempCustomerList.remove(tempCustomerList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempCustomerList.remove(tempCustomerList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
        }
    }

    public void reInitialiseProductList() {
        tempProductList.removeAll(tempProductList);
        int size = tempProductList.size();
        int size1 = productMasterList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < productMasterList.size(); kk++) {
            tempProductList.add(productMasterList.get(kk));
        }

    }

    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Type your Feedback");
        final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
        final Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                submit.setEnabled(false);
                String remarks = edInst.getText().toString();
                saveFeedbackData(remarks);
                instructionDialog.cancel();
            }
        });
        instructionDialog.show();
    }

    public void saveFeedbackData(final String feedback) {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                if (!Constants.isSelectCustomer) {
                    transDataHelperObj.insertToCustomerMaster(Constants.selectedCustomer,false);
                }
                transDataHelperObj.insertToNotesInfo(timeStamp, Constants.selectedCustomer.getCustomerCode(), currentProductMasterObj.getProdCode(), feedback);
                transDataHelperObj.insertToLocationTable("CF", timeStamp);

                Message msgObj = saveHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "ProspectJobDone");
                msgObj.setData(b);
                saveHandler.sendMessage(msgObj);
            }
        }.start();
    }


}
