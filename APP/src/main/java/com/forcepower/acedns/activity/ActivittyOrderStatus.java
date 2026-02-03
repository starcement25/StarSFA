package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.OrderListAdapter;
import com.forcepower.acedns.adapter.OrderNowithDateAdapter;
import com.forcepower.acedns.adapter.RemarksAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderStatus;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.OrderStatus;
import com.forcepower.acedns.bean.OrdernoWithDate;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DateTimeFormatter;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;

import static com.forcepower.acedns.R.id.ed_input;
import static com.forcepower.acedns.R.id.right;

public class ActivittyOrderStatus extends AceDnsParentActivity  implements OnClickListener {

    public static LinearLayout mParentLayout = null;
    public static Button mButtonSelectOrder = null;
    public Context mContext;
    public String mOrderNo = "";
    int positionOfCurrentItemToSubmit = 0;
    String currentItemPendingORCLosed = "PENDING", currentItemReasonToCLose = "Stock Not Available", currentDeliveredQtyInput;
    EditText etReasonToCloseOthers;
    Boolean isOrderItemNotLeft = false, DeliveredQtyNotSameAsRemainingQty = false;
    String chosenDateOfOrder;
    ProgressDialog mProgressDialogOrderStatus;
    Handler mHandlerOrderStatus;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    ArrayList<OrdernoWithDate> mOrdernoWithDateList;
    OrdernoWithDate mOrdernoWithDate;
    ArrayList<OrderStatus> mOrderStatusList;
    TextView textViewDate, textViewCustomername, textViewOrderNumber;
    private List<EditText> mEditTextList;
//    private List<CheckBox> mCheckBoxList;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;
    String orderStatusRemarks = "";
    ArrayList<String> hintRemarksValList;
    boolean isSuccess = false;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_status);
        RegisterActivities.registerActivity(this);

        mContext = ActivittyOrderStatus.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        //Utils.showToast(mContext,"status click");
        new commonAsyncTaskMaster(mContext, "order_status");
        InitializeView();
        /*Toolbar myToolbar = findViewById(R.id.my_toolbar);
        setSupportActionBar(myToolbar);
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayShowTitleEnabled(false);*/
        mEditTextList = new ArrayList<EditText>();

        mHandlerOrderStatus = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogOrderStatus.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                ActivittyOrderStatus.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                ShowCustomer();
                                break;
                            case 2:
                                ShowChooseOrder();
                                break;
                            case 3:
                                DrawView();
                                break;
                            case 4:
                                new TRANS_SubmitOrderStatus(mContext, isOrderItemNotLeft).execute();
                                break;
                            case 5:
                                break;

                        }
                    }
                });
            }
        };
    }
   /* @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }*/
    @Override
    public void onBackPressed(){
        super.onBackPressed();
        finish();
        //return true;
    }
    @Override
    public void onResume() {
        super.onResume();

    }

    public void backButtonClicked(View v){
        finish();
    }

    public void InitializeView() {
        textViewDate = (TextView) findViewById(R.id.textViewDate);
        textViewCustomername = (TextView) findViewById(R.id.textViewCustomername);
        textViewOrderNumber = (TextView) findViewById(R.id.textViewOrderNumber);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));

        mButtonSelectOrder = (Button) findViewById(R.id.buttonSelectCustomer);
        if(!Constants.nickName.equalsIgnoreCase("abdos") && !Constants.nickName.equalsIgnoreCase("abdost")){
            mButtonSelectOrder.setText("Select order date");
            mButtonSelectOrder.setOnClickListener(ActivittyOrderStatus.this);
        }
        else{
            mButtonSelectOrder.setVisibility(View.GONE);
            chosenDateOfOrder = Constants.dateString;
            textViewDate.setText("  Date: " + Utils.changeDateFormat("yyyyMMdd","dd/MM/yyyy",chosenDateOfOrder));
            chosenDateOfOrder = new DateTimeFormatter().changeDateFormat("dd/MM/yyyy", chosenDateOfOrder, "yyyyMMdd");
            PrepareOrderStatusData(1);
        }


        mParentLayout = (LinearLayout) findViewById(R.id.scrollLayout);

    }

    public void onClick(View clkdView) {

        if (clkdView == mButtonSelectOrder) {
//            openDatePicker();
            ArrayList<commonDatabaseHelper> empActivityList=mAceDnsDatabase.getOrderStatusDateList();
            if(empActivityList.size()>0)
                showOrderList(empActivityList);
        }

        String tag = (String) clkdView.getTag();
        if (true == isCheckBoxChecked(tag)) {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            EditText editText = mEditTextList.get(positionOfCurrentItemToSubmit);
            currentDeliveredQtyInput = editText.getText().toString();
            if (Utils.isNumeric(currentDeliveredQtyInput)) {
                mOrderStatusList.get(positionOfCurrentItemToSubmit).setCurrentDeliveredQuantity(currentDeliveredQtyInput);

                String AlreadyDeliveredQuantity = mOrderStatusList.get(positionOfCurrentItemToSubmit).getAlreadyDeliveredQuantity();
                String OrderedQuantity = mOrderStatusList.get(positionOfCurrentItemToSubmit).getOrderQuantity();
                Double remainingQty = Double.parseDouble(OrderedQuantity) - Double.parseDouble(AlreadyDeliveredQuantity);
                Double remainingQtyClose = Double.parseDouble(currentDeliveredQtyInput) + Double.parseDouble(AlreadyDeliveredQuantity);
                Double OrderedQuantityClose = Double.parseDouble(OrderedQuantity);
                if(remainingQty==0 || remainingQtyClose.equals(OrderedQuantityClose)){
                    mOrderStatusList.get(positionOfCurrentItemToSubmit).setStatus("closed");
                }else{
                    mOrderStatusList.get(positionOfCurrentItemToSubmit).setStatus("pending");
                }

                if (Double.parseDouble(currentDeliveredQtyInput) > remainingQty)
                {
                    Toast.makeText(mContext, "Quantity to be delivered can not be grater than "+remainingQty, Toast.LENGTH_SHORT).show();
//                    mCheckBoxList.get(positionOfCurrentItemToSubmit).setChecked(false);
                } else {
                    orderStatusRemarks = mAceDnsDatabase.getOrderStatusRemarks();
                    if (orderStatusRemarks.isEmpty() || orderStatusRemarks ==null) {
                        //ShowStatusDialog();
                        if(HTTPUtils.isConnectionPossible(mContext))
                        {
                            UpdateOrder();
                        }
                        else
                        {
                            Utils.showToast(mContext,"You need an active internet connection to Submit.");
                        }

                    }else{
                        if(HTTPUtils.isConnectionPossible(mContext))
                        {
                            ShowRemarksDialog();
                        }
                        else
                        {
                            Utils.showToast(mContext,"You need an active internet connection to Submit.");
                        }
                        //ShowStatusDialog();

                    }
                }

            } else {
                Toast.makeText(mContext, "Please provide proper quantity to be delivered.", Toast.LENGTH_SHORT).show();
//                mCheckBoxList.get(positionOfCurrentItemToSubmit).setChecked(false);
            }
        }
    }
    public void showOrderList(ArrayList<commonDatabaseHelper> empActivityList)
    {
//        final Dialog mDetailsDialog = new Dialog(mContext, R.style.CustomMaterialDialogTheme);
        final Dialog mDetailsDialog = new Dialog(mContext);

        /*if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("yes")){
            mDetailsDialog.setContentView(R.layout.order_status_details_date_select);
        }else {
            mDetailsDialog.setContentView(R.layout.order_status_details);
        }*/
        mDetailsDialog.setContentView(R.layout.order_status_details_date_select);
        TextView text1 =  mDetailsDialog.findViewById(R.id.text1);
        FrameLayout secondDataLayout =  mDetailsDialog.findViewById(R.id.secondDataLayout);
        text1.setText("Dates in list");
        text1.setVisibility(View.GONE);
        secondDataLayout.setVisibility(View.GONE);
        ImageView back =  mDetailsDialog.findViewById(R.id.image_cancel);
        back.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        mDetailsDialog.setCancelable(true);
        TextView textViewTitleName =  mDetailsDialog.findViewById(R.id.title);
        Button btn_generate =  mDetailsDialog.findViewById(R.id.btn_generate);
        btn_generate.setText("CANCEL");
        btn_generate.setOnClickListener(view ->
        {
            mDetailsDialog.dismiss();
        });
        textViewTitleName.setText("Choose Order Date...");

        ListView dialogList = (ListView) mDetailsDialog.findViewById(R.id.list);
        OrderListAdapter ReportAdapterObject = new OrderListAdapter(mContext, R.layout.list_item_hierarchical_report_light_material, empActivityList);
        dialogList.setAdapter(ReportAdapterObject);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            mDetailsDialog.cancel();
            commonDatabaseHelper mOrdernoWithDate = ReportAdapterObject.getItem(arg2);
            chosenDateOfOrder = mOrdernoWithDate.getItem1();
            textViewDate.setText("  Date: " + chosenDateOfOrder);
            chosenDateOfOrder = new DateTimeFormatter().changeDateFormat("dd/MM/yyyy", chosenDateOfOrder, "yyyyMMdd");
            mButtonSelectOrder.setVisibility(View.GONE);
            PrepareOrderStatusData(1);
        });
        mDetailsDialog.show();
        //Window window = mDetailsDialog.getWindow();
        //window.setLayout(700, 400);

    }
    public boolean isCheckBoxChecked(String productcode) {
        boolean isPresent = false;
        String prodeCode = "";
        if (mOrderStatusList != null) {
            for (int count = 0; count < mOrderStatusList.size(); count++) {
                prodeCode = mOrderStatusList.get(count).getProductCode();
                if (prodeCode.equalsIgnoreCase(productcode)) {
                    isPresent = true;
                    positionOfCurrentItemToSubmit = count;
                    break;
                }
            }
        }

        return isPresent;
    }

    public void PrepareOrderStatusData(final int task) {
        mProgressDialogOrderStatus = new ProgressDialog(mContext);
        mProgressDialogOrderStatus.setCancelable(false);
        if (task == 4) {
            mProgressDialogOrderStatus.setMessage("Saving data.\nPlease wait..");
        } else if (task == 5) {
            mProgressDialogOrderStatus.setMessage("Downloading data.\nPlease wait..");
        } else {
            mProgressDialogOrderStatus.setMessage("Fetching data from database.\nPlease wait..");
        }
        mProgressDialogOrderStatus.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        mCustomerDetailsList = mAceDnsDatabase.GetOrderStatusCustomer(chosenDateOfOrder);
                        break;
                    case 2:
                        mOrdernoWithDateList = mAceDnsDatabase.GetCustomerWiseOrderNO(Constants.selectedCustomer.getCustomerCode(), chosenDateOfOrder);
                        break;
                    case 3:
                        mOrderNo = mOrdernoWithDate.getOrderNo();
                        mOrderStatusList = mAceDnsDatabase.GetCustomerWiseOrderStatus(mOrderNo);
                        if (mOrderStatusList.size() > 0) {
                            mAceDnsDatabase.UpdateOrderStatus(mOrderNo);
                        }
                        break;
                    case 4:
                        SaveDatatoDatabase();
                        break;

                    case 5:
                        break;
                }

                Message msg = mHandlerOrderStatus.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerOrderStatus.sendMessage(msg);
            }
        }.start();
    }

    public void ShowCustomer() {
        if (mCustomerDetailsList.size() > 1) {
            ShowCustomerListDialog();
        } else if (mCustomerDetailsList.size() == 1) {
            Constants.selectedCustomer = mCustomerDetailsList.get(0);
            textViewCustomername.setText("Customer: " + Constants.selectedCustomer.getCustomerName());
            PrepareOrderStatusData(2);
        } else {
            Toast.makeText(mContext, "No customer found.", Toast.LENGTH_SHORT).show();
            if (mOrderStatusList != null)
                mOrderStatusList.clear();
            textViewDate.setText("  Date: ");
            textViewCustomername.setText("  Customer: ");
            textViewOrderNumber.setText("  Order: ");
            DrawView();

        }

    }

    public void ShowChooseOrder() {
        if (mOrdernoWithDateList.size() > 1) {
            ShowOrderSelectionDialog();
        } else {
            if (mOrdernoWithDateList.size() == 1) {
                mOrdernoWithDate = mOrdernoWithDateList.get(0);
                textViewOrderNumber.setText("  Order 1");
                PrepareOrderStatusData(3);
            } else {
                Toast.makeText(mContext, "No order found", Toast.LENGTH_SHORT).show();

            }
        }
    }

    public void ShowOrderSelectionDialog() {
        final OrderNowithDateAdapter adapterDate = new OrderNowithDateAdapter(mContext,
                R.layout.customer_list_child_do_single_item, mOrdernoWithDateList);

        final Dialog mDialogOrderWithDate = new Dialog(mContext);
        mDialogOrderWithDate.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOrderWithDate.setContentView(R.layout.select_from_list_material);
        mDialogOrderWithDate.setCancelable(false);
        TextView title = (TextView) mDialogOrderWithDate.findViewById(R.id.title);
        title.setText("Please select an order");


        ListView dialogList = (ListView) mDialogOrderWithDate.findViewById(R.id.list);
        dialogList.setAdapter(adapterDate);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                mDialogOrderWithDate.cancel();
                mOrdernoWithDate = adapterDate.getItem(arg2);
                int ororderNumber = arg2 + 1;
                textViewOrderNumber.setText("  Order: " + ororderNumber);
                PrepareOrderStatusData(3);
            }
        });

        Button addCustomer = (Button) mDialogOrderWithDate
                .findViewById(R.id.btn_cncl);
        addCustomer.setVisibility(View.GONE);
        mDialogOrderWithDate.show();
    }

    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child_do_single_item, mCustomerDetailsList);

        final Dialog mDialogCustomer = new Dialog(mContext, R.style.MyMaterialTheme);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search_material);
        mDialogCustomer.setCancelable(false);
        TextView title = (TextView) mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = (EditText) mDialogCustomer
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2,
                                      int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });

        ListView dialogList = (ListView) mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                mDialogCustomer.cancel();
                Constants.selectedCustomer = adapterCust.getItem(arg2);
                textViewCustomername.setText("  Customer: " + Constants.selectedCustomer.getCustomerName());
                PrepareOrderStatusData(2);
            }
        });

        Button addCustomer = (Button) mDialogCustomer
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();
    }

    public boolean SaveDatatoDatabase() {
        isSuccess = false;
        try {
            mAceDnsTransactionDatabase.UpdateOrderStatusItem(mOrderStatusList.get(positionOfCurrentItemToSubmit));
            mOrderStatusList.remove(positionOfCurrentItemToSubmit);
//            mCheckBoxList.remove(positionOfCurrentItemToSubmit);
            if (mOrderStatusList.size() > 0) {
                isOrderItemNotLeft = false;
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        DrawView();

                    }
                });

            } else {
                isOrderItemNotLeft = true;
            }

            isSuccess = true;
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        return isSuccess;
    }

    @SuppressLint("RestrictedApi")
    @SuppressWarnings("deprecation")
    public void DrawView() {
        mParentLayout.removeAllViews();

        String productcode = "";
        String productname = "";
        String orderedquantity = "";
        String alreadydeliveredQuantity = "";
        OrderStatus orderStatus;
        mEditTextList = new ArrayList<EditText>();
//        mCheckBoxList = new ArrayList<>();

        LinearLayout.LayoutParams param = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 1);
        param.gravity=Gravity.CENTER_VERTICAL|right;
        LinearLayout.LayoutParams param2 = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 3);
        LinearLayout.LayoutParams param3 = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 2);
        if (mOrderStatusList != null) {
            for (int count = 0; count < mOrderStatusList.size(); count++)
            {
                String backgroundColor="#ffffff";
                String textColor="#000000";
                if (count==0 || count % 2 == 0)
                {
                    backgroundColor="#EFFFFF";
                    textColor="#003399";
                }
                else
                {
                    backgroundColor="#FFE4B5";
                    textColor="#003399";
                }

                orderStatus = mOrderStatusList.get(count);
                productname = orderStatus.getProductName();
                orderedquantity = orderStatus.getOrderQuantity();
                productcode = orderStatus.getProductCode();

                LinearLayout childlayout = new LinearLayout(this);
                childlayout.setPadding(1, 2, 1, 2);
                childlayout.setOrientation(LinearLayout.HORIZONTAL);
                childlayout.setBackgroundColor(Color.parseColor(backgroundColor));


                TextView textviewproductname = new TextView(this);
                textviewproductname.setText(productname);
                textviewproductname.setTextColor(Color.parseColor(textColor));
                textviewproductname.setLayoutParams(param2);
                childlayout.addView(textviewproductname);

                TextView textvieworderqty = new TextView(this);
                textvieworderqty.setText(orderedquantity);
                textvieworderqty.setTextColor(Color.parseColor(textColor));
                textvieworderqty.setLayoutParams(param);
                textvieworderqty.setGravity(Gravity.CENTER_VERTICAL|right);
                childlayout.addView(textvieworderqty);

                alreadydeliveredQuantity = mOrderStatusList.get(count).getAlreadyDeliveredQuantity();
                if (!Utils.isNumeric(alreadydeliveredQuantity)) {
                    alreadydeliveredQuantity = "0.00";
                }
//                TextView textviewremainingqty = new TextView(this);
//                textviewremainingqty.setText(Constants.defaultFormat.format(Double.parseDouble(alreadydeliveredQuantity)));
//                textviewremainingqty.setTextColor(Color.parseColor(textColor));
//                textviewremainingqty.setLayoutParams(param);
//                textviewremainingqty.setGravity(Gravity.CENTER);
//                childlayout.addView(textviewremainingqty);

                EditText editText = new EditText(this);
                editText.setPadding(3,0,0,0);
                LayoutParams paramsForEditText = new LayoutParams(0, LayoutParams.WRAP_CONTENT, 3);
                paramsForEditText.setMargins(1,3,1,3);
                paramsForEditText.gravity=Gravity.CENTER;
                editText.setLayoutParams(paramsForEditText);
                editText.setTag(productcode);
                editText.setInputType(InputType.TYPE_CLASS_NUMBER
                        | InputType.TYPE_NUMBER_FLAG_DECIMAL);
                textvieworderqty.setGravity(Gravity.CENTER);
                editText.setImeOptions(EditorInfo.IME_ACTION_DONE);
                editText.setTypeface(null, Typeface.NORMAL);
                editText.setTextColor(Color.parseColor("#000000"));
                editText.setHint("Input");
//                if (count==0 || count % 2 == 0)
//                {

//                    editText.setBackgroundResource(R.drawable.edit_text_background_black_material);
//                }
//                else
//                {
                    editText.setBackgroundResource(R.drawable.edit_text_background_white_material);
//                }

                mEditTextList.add(editText);

                LinearLayout childlayoutInput = new LinearLayout(this);
                childlayoutInput.setLayoutParams(param3);
                childlayoutInput.setBackgroundColor(Color.parseColor(backgroundColor));

//                AppCompatCheckBox  checkBoxSubmitOrderDelivery = new AppCompatCheckBox(this);
//                checkBoxSubmitOrderDelivery.setLayoutParams(new LinearLayout.LayoutParams(5, LayoutParams.WRAP_CONTENT,1));
//                checkBoxSubmitOrderDelivery.setTag(productcode);
//                if (count==0 || count % 2 == 0)
//                {
//                    checkBoxSubmitOrderDelivery.setSupportButtonTintList(new ColorStateList(states, thumbColors));
//                }
//                else
//                {
//                    checkBoxSubmitOrderDelivery.setSupportButtonTintList(new ColorStateList(states, thumbColors2));
//                }
                ImageView checkBoxSubmitOrderDelivery = new ImageView(this);
                checkBoxSubmitOrderDelivery.setLayoutParams(new LinearLayout.LayoutParams(5, LayoutParams.WRAP_CONTENT,1));
                checkBoxSubmitOrderDelivery.setTag(productcode);
                if (count==0 || count % 2 == 0)
                {
                    checkBoxSubmitOrderDelivery.setBackgroundResource(R.drawable.ic_black_add_24);
                }
                else
                {
                    checkBoxSubmitOrderDelivery.setBackgroundResource(R.drawable.ic_black_add_24);
                }
//                checkBoxSubmitOrderDelivery.setACC
                checkBoxSubmitOrderDelivery.setOnClickListener(this);
//                mCheckBoxList.add(checkBoxSubmitOrderDelivery);
                childlayoutInput.addView(editText);
                childlayoutInput.addView(checkBoxSubmitOrderDelivery);
                childlayout.addView(childlayoutInput);

                LinearLayout childlayout2 = new LinearLayout(this);
                childlayout2.setPadding(1, 2, 1, 2);
                childlayout2.setOrientation(LinearLayout.VERTICAL);
                childlayout2.setBackgroundColor(Color.parseColor(backgroundColor));
                View lineView = new View(this);
                LayoutParams paramsLineView = new LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT, 1, 0);
                paramsForEditText.setMargins(1,3,1,3);
                paramsLineView.gravity=Gravity.CENTER;
                lineView.setLayoutParams(paramsLineView);
                lineView.setBackgroundColor(getResources().getColor(R.color.black));
                childlayout2.addView(lineView);

                Button button = new Button(mContext);
                button.setTag(productcode);
                button.setHorizontallyScrolling(true);
                button.setLayoutParams(param);
                button.setGravity(Gravity.CENTER);
                button.setText("Status");
                button.setBackgroundDrawable(getResources().getDrawable(R.drawable.button_background));
                button.setSingleLine(false);
                button.setOnClickListener(this);

                mParentLayout.addView(childlayout);
            }
        }

    }
    int[][] states = new int[][] {
            new int[] {-android.R.attr.state_checked},
            new int[] {android.R.attr.state_checked},
    };

    int[] thumbColors = new int[] {
            Color.BLACK,
            Color.BLACK,
    };
    int[] thumbColors2 = new int[] {
            Color.WHITE,
            Color.WHITE,
    };

    public void ShowStatusDialog() {
        final Dialog mDialogOrderWithDate = new Dialog(mContext);
        mDialogOrderWithDate.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogOrderWithDate.setContentView(R.layout.dialog_order_status_pending_closed);
        mDialogOrderWithDate.setCancelable(false);
        LinearLayout llDialogPendingClosed = (LinearLayout) mDialogOrderWithDate.findViewById(R.id.llDialogPendingClosed);
        RadioGroup radioSelectPrice = (RadioGroup) mDialogOrderWithDate.findViewById(R.id.radioSelectPrice);
        etReasonToCloseOthers = (EditText) mDialogOrderWithDate.findViewById(ed_input);
        final RadioGroup RGReasonToClose = (RadioGroup) mDialogOrderWithDate.findViewById(R.id.RGReasonToClose);
        RGReasonToClose.setVisibility(View.VISIBLE);
        RadioButton radioStockNotAvlabl = (RadioButton) mDialogOrderWithDate.findViewById(R.id.radioStockNotAvlabl);
        radioStockNotAvlabl.setChecked(true);
        RGReasonToClose.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
            public void onCheckedChanged(RadioGroup group, int checkedId) {
                RadioButton radioSelection = (RadioButton) mDialogOrderWithDate.findViewById(checkedId);

                currentItemReasonToCLose = radioSelection.getText().toString();
                if (currentItemReasonToCLose.matches("Other")) {
                    etReasonToCloseOthers.setVisibility(View.VISIBLE);
                } else {
                    etReasonToCloseOthers.setVisibility(View.GONE);
                }
            }
        });
        currentItemPendingORCLosed="CLOSED";
//        radioSelectPrice.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
//            public void onCheckedChanged(RadioGroup group, int checkedId) {
//                RadioButton radioSelection = (RadioButton) mDialogOrderWithDate.findViewById(checkedId);
//                currentItemPendingORCLosed = radioSelection.getText().toString();
//                String AlreadyDeliveredQuantity = mOrderStatusList.get(positionOfCurrentItemToSubmit).getAlreadyDeliveredQuantity();
//                String OrderedQuantity = mOrderStatusList.get(positionOfCurrentItemToSubmit).getOrderQuantity();
//                Double remainingQty = Double.parseDouble(OrderedQuantity) - Double.parseDouble(AlreadyDeliveredQuantity);
////                if (Double.parseDouble(currentDeliveredQtyInput) != remainingQty) {
////                    DeliveredQtyNotSameAsRemainingQty = true;
////                } else {
////                    DeliveredQtyNotSameAsRemainingQty = false;
////                }
//                if (currentItemPendingORCLosed.matches("PENDING")) {
//                    etReasonToCloseOthers.setVisibility(View.GONE);
//                    RGReasonToClose.setVisibility(View.GONE);
//                } else if (currentItemPendingORCLosed.matches("CLOSED") && DeliveredQtyNotSameAsRemainingQty) {
//
//
//
//                }
//
//            }
//        });

//		llDialogPendingClosed.setLayoutParams(new LinearLayout.LayoutParams(100, LayoutParams.WRAP_CONTENT));
        Button btn_submit = (Button) mDialogOrderWithDate.findViewById(R.id.btn_submit);
        btn_submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                if (currentItemPendingORCLosed.matches("PENDING")) {
                    PrepareOrderStatusData(4);
                    mDialogOrderWithDate.dismiss();
                } else//if closed
                {
                    mOrderStatusList.get(positionOfCurrentItemToSubmit).setStatus("closed");
                    if (DeliveredQtyNotSameAsRemainingQty) {
                        if (currentItemReasonToCLose.matches("Other")) {
                            String specifiedReasonToCloseIncompleteOrder = etReasonToCloseOthers.getText().toString();
                            if (specifiedReasonToCloseIncompleteOrder != null && !specifiedReasonToCloseIncompleteOrder.matches("null") && specifiedReasonToCloseIncompleteOrder != "") {
                                mOrderStatusList.get(positionOfCurrentItemToSubmit).setRemarks(currentItemReasonToCLose + "#" + specifiedReasonToCloseIncompleteOrder);
                                PrepareOrderStatusData(4);
                                mDialogOrderWithDate.dismiss();
                            } else {
                                Toast.makeText(mContext, "Please specify a reason to close the order", Toast.LENGTH_SHORT).show();
                            }
                        } else {
                            mOrderStatusList.get(positionOfCurrentItemToSubmit).setRemarks(currentItemReasonToCLose);
                            PrepareOrderStatusData(4);
                            mDialogOrderWithDate.dismiss();
                        }
                    } else//expected quantity is being delivered, so just make the order closed without asking for reason
                    {
                        PrepareOrderStatusData(4);
                        mDialogOrderWithDate.dismiss();
                    }


                }
            }
        });

        mDialogOrderWithDate.show();

    }

    private void openDatePicker() {
        int mYear, mMonth, mDay;
        // Get Current Date
        final Calendar c = Calendar.getInstance();
        mYear = c.get(Calendar.YEAR);
        mMonth = c.get(Calendar.MONTH);
        mDay = c.get(Calendar.DAY_OF_MONTH);
        //launch datepicker modal
        Utils.CustomDatePickerDialogWithPermanentTitle datePickerDialog = new Utils.CustomDatePickerDialogWithPermanentTitle(this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int year, int monthOfYear, int dayOfMonth) {
                        chosenDateOfOrder = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
                        chosenDateOfOrder = new DateTimeFormatter().changeDateFormat("d/M/yyyy", chosenDateOfOrder, "yyyyMMdd");
                        textViewDate.setText("  Date: " + new DateTimeFormatter().changeDateFormat("yyyyMMdd", chosenDateOfOrder, "dd/MM/yyyy"));
                        PrepareOrderStatusData(1);
                    }
                }, mYear, mMonth, mDay);
        datePickerDialog.setPermanentTitle("Select order date...");
        datePickerDialog.show();

        datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());

    }
    Dialog remarkOrderDialog;
    ListView rList;
    public void ShowRemarksDialog(){
        hintRemarksValList = new ArrayList<>();
        String[] arrayOfData = orderStatusRemarks.split("#");
        //Utils.showToast(mContext,re[1]);
        for (int i = 0; i < arrayOfData.length; i++) {
            hintRemarksValList.add(arrayOfData[i]);
        }

        remarkOrderDialog = new Dialog(ActivittyOrderStatus.this, R.style.PauseDialog);
        remarkOrderDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        remarkOrderDialog.setContentView(R.layout.dialog_order_status_pending_closed_dynamic);
        remarkOrderDialog.setCancelable(false);

        rList = (ListView) remarkOrderDialog.findViewById(R.id.list);

        RemarksAdapter adapter = new RemarksAdapter(ActivittyOrderStatus.this,
                R.layout.activity_listview_remarks, hintRemarksValList);
        rList.setAdapter(adapter);

        rList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

                mOrderStatusList.get(positionOfCurrentItemToSubmit).setRemarks(hintRemarksValList.get(position));
                for(int i=0; i<parent.getChildCount(); i++)
                {
                    if(i == position)
                    {
                        parent.getChildAt(i).setBackgroundColor(Color.LTGRAY);
                    }
                    else
                    {
                        parent.getChildAt(i).setBackgroundColor(Color.TRANSPARENT);
                    }

                }
            }
        });



        Button btn_submit = (Button) remarkOrderDialog.findViewById(R.id.btn_submit);
        btn_submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                //Utils.showToast(mContext,"submit"+mOrderStatusList.get(positionOfCurrentItemToSubmit).getRemarks());
                UpdateOrder();
            }
        });

        remarkOrderDialog.show();
    }
    public static String getCalculatedDate(String dateFormat, int days) {
        Calendar cal = Calendar.getInstance();
        SimpleDateFormat s = new SimpleDateFormat(dateFormat);
        cal.add(Calendar.DAY_OF_YEAR, days);
        return s.format(new Date(cal.getTimeInMillis()));
    }

    public void SaveRemarksOrder(String item,int i){
        mOrderStatusList.get(positionOfCurrentItemToSubmit).setRemarks(item);

    }

    private void UpdateOrder(){
        //Utils.showToast(mContext,mOrderStatusList.get(positionOfCurrentItemToSubmit).getOrderQuantity() + " Al"+mOrderStatusList.get(positionOfCurrentItemToSubmit).getAlreadyDeliveredQuantity()+";"+mOrderStatusList.get(positionOfCurrentItemToSubmit).getStatus());
        PrepareOrderStatusData(4);
        if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("ys")){
            if(isSuccess){

                mAceDnsTransactionDatabase.UpdateOrderStockCustomer(mOrderStatusList.get(positionOfCurrentItemToSubmit));
            }
        }


    }

    private void UpdateOrderOLd(){
        AlertDialog.Builder builder = new AlertDialog.Builder(this);

        builder.setTitle("Confirm Reson");
        builder.setMessage(""+mOrderStatusList.get(positionOfCurrentItemToSubmit).getRemarks());

        builder.setPositiveButton("YES", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
                // Do nothing but close the dialog
                //Utils.showToast(mContext,"click " + positionOfCurrentItemToSubmit);
                PrepareOrderStatusData(4);
                //mAceDnsTransactionDatabase.UpdateOrderStockCustomer(mOrderStatusList.get(positionOfCurrentItemToSubmit));
                if(Constants.menuDetailsObj.getCustomer_product_stock().toLowerCase().matches("ys")){
                    if(isSuccess){

                        mAceDnsTransactionDatabase.UpdateOrderStockCustomer(mOrderStatusList.get(positionOfCurrentItemToSubmit));
                    }
                }
                dialog.dismiss();
                remarkOrderDialog.cancel();
            }
        });

        builder.setNegativeButton("NO", new DialogInterface.OnClickListener() {

            @Override
            public void onClick(DialogInterface dialog, int which) {

                // Do nothing
                dialog.dismiss();
            }
        });

        AlertDialog alert = builder.create();
        alert.show();
    }
}