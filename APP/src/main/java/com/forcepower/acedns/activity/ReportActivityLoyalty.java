package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.InputFilter;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.LoyaltyCustomerAdapter;
import com.forcepower.acedns.adapter.LoyaltyTransAdapter;
import com.forcepower.acedns.bean.CardTransactionDetails;
import com.forcepower.acedns.bean.LoyaltyCustomerDetails;
import com.forcepower.acedns.bean.LoyaltyPurchaseDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class ReportActivityLoyalty extends FragmentActivity implements OnClickListener {

    ImageView imgLogo, showDurationLayout, hideDurationLayout;
    Button btnBack, btnStartDate, btnEndDate, btnDateDone;
    RelativeLayout durationLayout;
    LinearLayout customDateLayout;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    TextView startDatetxt, endDateTxt;
    Context mContext;
    AceDnsTransactionDatabase transDataHelperObj;
    String reportDuration = "TODAY";
    String startDate = "", endDate = "";
    Date startDate1, endDate1;
    CaldroidListener listener;
    boolean isStartDate = false;
    SimpleDateFormat dateFormat, dateFormat1;
    Double totalAmt = 0.00;
    Date currentDate = new Date();
    FrameLayout btnToday, btnMTD, btnCustom;
    String searchCriteria = "card";
    ArrayList<LoyaltyCustomerDetails> customerList, tempCustomerList;
    LoyaltyCustomerAdapter adapterCust;
    LoyaltyCustomerDetails selectedCustomer;
    String lastStr = "";
    LoyaltyPurchaseDetails loyaltyPurchaseStatus;
    Dialog customerListDialog;
    ImageView imgPurchase, imgAccu, imgRdmd;
    TextView nameTxt, purchaseTxt, accuTxt, reedemedTxt, expTxt;
    Button numberBtn;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    LinearLayout purchaseLayout, accuLayout, rdmdLayout;
    ArrayList<CardTransactionDetails> transList;
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_report_loyalty);
        RegisterActivities.registerActivity(this);

        mContext = ReportActivityLoyalty.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        dateFormat1 = new SimpleDateFormat("yyyyMMdd");

        reportDuration = getIntent().getStringExtra("reportDuration");
        startDate = getIntent().getStringExtra("startDate");
        endDate = getIntent().getStringExtra("endDate");

        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);


        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate1 = date;
                    startDatetxt.setText(dateFormat.format(date));
                    startDate = dateFormat1.format(date);
                } else {
                    endDate1 = date;
                    endDateTxt.setText(dateFormat.format(date));
                    endDate = dateFormat1.format(date);
                }
                dialogCaldroidFragment.dismiss();
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {

            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };

        initView();

        generateCustomerData();

    }

    public void initView() {
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        showDurationLayout = (ImageView) findViewById(R.id.image_clk);
        hideDurationLayout = (ImageView) findViewById(R.id.image_go);
        showDurationLayout.setOnClickListener(this);
        hideDurationLayout.setOnClickListener(this);

        durationLayout = (RelativeLayout) findViewById(R.id.duration_layout);
        customDateLayout = (LinearLayout) findViewById(R.id.custom_date_layout);

        purchaseLayout = (LinearLayout) findViewById(R.id.purchase_layout);
        accuLayout = (LinearLayout) findViewById(R.id.accu_point_layout);
        rdmdLayout = (LinearLayout) findViewById(R.id.rdmd_layout);

        imgPurchase = (ImageView) findViewById(R.id.img_purchase);
        imgAccu = (ImageView) findViewById(R.id.img_accu);
        imgRdmd = (ImageView) findViewById(R.id.img_rdmd);

        purchaseLayout.setOnClickListener(this);
        accuLayout.setOnClickListener(this);
        rdmdLayout.setOnClickListener(this);

        numberBtn = (Button) findViewById(R.id.card_no);
        nameTxt = (TextView) findViewById(R.id.name);
        purchaseTxt = (TextView) findViewById(R.id.purchase);
        accuTxt = (TextView) findViewById(R.id.accu_point);
        reedemedTxt = (TextView) findViewById(R.id.rdmd_point);
        expTxt = (TextView) findViewById(R.id.exp_date);


        startDatetxt = (TextView) findViewById(R.id.txt_start_date);
        endDateTxt = (TextView) findViewById(R.id.txt_end_date);
        startDatetxt.setText("");
        endDateTxt.setText("");

        btnBack = (Button) findViewById(R.id.back);
        btnStartDate = (Button) findViewById(R.id.btn_start_date);
        btnEndDate = (Button) findViewById(R.id.btn_end_date);
        btnDateDone = (Button) findViewById(R.id.btn_date_done);

        btnBack.setOnClickListener(this);
        btnStartDate.setOnClickListener(this);
        btnEndDate.setOnClickListener(this);
        btnDateDone.setOnClickListener(this);

        btnToday = (FrameLayout) findViewById(R.id.btn_today);
        btnMTD = (FrameLayout) findViewById(R.id.btn_mtd);
        btnCustom = (FrameLayout) findViewById(R.id.btn_custom);

        btnToday.setOnClickListener(this);
        btnMTD.setOnClickListener(this);
        btnCustom.setOnClickListener(this);
        numberBtn.setOnClickListener(this);

        if (reportDuration.equalsIgnoreCase("TODAY")) {
            changeReportOptionBG(4);
        } else if (reportDuration.equalsIgnoreCase("MTD")) {
            changeReportOptionBG(5);
        } else {
            changeReportOptionBG(6);
        }

    }

    @Override
    public void onClick(View v) {
        if (v == btnBack) {
            transDataHelperObj.closeDatabase();
            finish();
        } else if (v == btnStartDate) {
            isStartDate = true;
            chooseDateDialog();
        } else if (v == btnEndDate) {
            isStartDate = false;
            chooseDateDialog();
        } else if (v == btnDateDone) {
            try {
                if (endDate1.after(currentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else if (startDate1.after(endDate1)) {
                    Utils.showToast(mContext, "Start Date should be less than or equal to End Date");
                } else {
                    showDurationLayout.startAnimation(fadeIn);
                    showDurationLayout.setVisibility(View.VISIBLE);
                    durationLayout.startAnimation(bottomDown);
                    durationLayout.setVisibility(View.GONE);
                    generateLoyaltyData();
                    endDateTxt.setText("");
                    startDatetxt.setText("");
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Please choose the Dates again.");
            }
        } else if (v == btnToday) {
            showDurationLayout.startAnimation(fadeIn);
            showDurationLayout.setVisibility(View.VISIBLE);
            durationLayout.startAnimation(bottomDown);
            durationLayout.setVisibility(View.GONE);
            startDate = "";
            endDate = "";
            reportDuration = "TODAY";
            changeReportOptionBG(4);
            loyaltyPurchaseStatus = null;
            generateLoyaltyData();
        } else if (v == btnMTD) {
            showDurationLayout.startAnimation(fadeIn);
            showDurationLayout.setVisibility(View.VISIBLE);
            durationLayout.startAnimation(bottomDown);
            durationLayout.setVisibility(View.GONE);
            startDate = "";
            endDate = "";
            reportDuration = "MTD";
            loyaltyPurchaseStatus = null;
            generateLoyaltyData();
            changeReportOptionBG(5);
        } else if (v == btnCustom) {
            reportDuration = "CUSTOM";
            loyaltyPurchaseStatus = null;
            changeReportOptionBG(6);
        } else if (v == hideDurationLayout) {
            showDurationLayout.startAnimation(fadeIn);
            showDurationLayout.setVisibility(View.VISIBLE);
            durationLayout.startAnimation(bottomDown);
            durationLayout.setVisibility(View.GONE);
        } else if (v == showDurationLayout) {
            showDurationLayout.startAnimation(fadeOut);
            showDurationLayout.setVisibility(View.GONE);
            durationLayout.startAnimation(bottomUp);
            durationLayout.setVisibility(View.VISIBLE);
        } else if (v == purchaseLayout) {
            showLoyaltyDtailsDialog("purchase");
        } else if (v == accuLayout) {
            showLoyaltyDtailsDialog("reward");
        } else if (v == rdmdLayout) {
            showLoyaltyDtailsDialog("redeemed");
        } else if (v == numberBtn) {
            generateCustomerData();
        }
    }

    public void generateCustomerData() {
        customerList = transDataHelperObj.getLoyaltyCustomerListForReport(reportDuration, startDate, endDate);
        if (customerList.size() > 0) {
            showChooseCustomerDialog();
        } else {
            Utils.showToast(mContext, "No Loyalty Transaction found..");
            finish();
        }
    }

    public void generateLoyaltyData() {
        loyaltyPurchaseStatus = transDataHelperObj.getLoyaltyPurchaseDetailsForReport
                (selectedCustomer.getCardNumber(), reportDuration, startDate, endDate);
        if (loyaltyPurchaseStatus.getPurchaseValue() != null && loyaltyPurchaseStatus.getRwrdPoint() != null) {
            setDetails();
        } else {
            Utils.showToast(mContext, "No Record Found..");
            finish();
        }
    }


    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        Bundle bundle = new Bundle();
        bundle.putString(CaldroidFragment.DIALOG_TITLE, "Select a date");
        dialogCaldroidFragment.setArguments(bundle);
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    public void changeReportOptionBG(int selectedLayoutId) {
        switch (selectedLayoutId) {
            case 4:
                btnToday.setBackgroundColor(Color.parseColor("#B6B6B4"));
                btnMTD.setBackgroundColor(Color.parseColor("#E5E4E2"));
                btnCustom.setBackgroundColor(Color.parseColor("#E5E4E2"));
                customDateLayout.startAnimation(bottomDown);
                customDateLayout.setVisibility(View.GONE);
                break;
            case 5:
                btnToday.setBackgroundColor(Color.parseColor("#E5E4E2"));
                btnMTD.setBackgroundColor(Color.parseColor("#B6B6B4"));
                btnCustom.setBackgroundColor(Color.parseColor("#E5E4E2"));
                customDateLayout.startAnimation(bottomDown);
                customDateLayout.setVisibility(View.GONE);
                break;
            case 6:
                btnToday.setBackgroundColor(Color.parseColor("#E5E4E2"));
                btnMTD.setBackgroundColor(Color.parseColor("#E5E4E2"));
                btnCustom.setBackgroundColor(Color.parseColor("#B6B6B4"));
                customDateLayout.startAnimation(bottomUp);
                customDateLayout.setVisibility(View.VISIBLE);
                break;
        }
    }

    @Override
    public void onAttachedToWindow() {
        super.onAttachedToWindow();
        //this.getWindow().setType(WindowManager.LayoutParams.TYPE_KEYGUARD);
    }


    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_MENU || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    public void showChooseCustomerDialog() {
        tempCustomerList = new ArrayList<LoyaltyCustomerDetails>();
        searchCriteria = "card";
        reInitialiseCustomerList();
        adapterCust = new LoyaltyCustomerAdapter(ReportActivityLoyalty.this, R.layout.customer_list_child, tempCustomerList);
        customerListDialog = new Dialog(ReportActivityLoyalty.this, R.style.PauseDialog);
        customerListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        customerListDialog.setContentView(R.layout.choose_customer_search);
        customerListDialog.setCancelable(false);
        TextView title = (TextView) customerListDialog.findViewById(R.id.title);
        title.setText("Please select a Customer");
        final EditText searchText = (EditText) customerListDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.setSingleLine(true);
        int maxLength = 8;
        InputFilter[] fArray = new InputFilter[1];
        fArray[0] = new InputFilter.LengthFilter(maxLength);
        searchText.setFilters(fArray);
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
            }
        });

        ListView dialogList = (ListView) customerListDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                selectedCustomer = tempCustomerList.get(arg2);
                generateLoyaltyData();
                customerListDialog.cancel();
            }
        });
        Button addCustomer = (Button) customerListDialog.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        final ImageView searchOpt = (ImageView) customerListDialog.findViewById(R.id.imageView2);
        searchOpt.setTag(R.drawable.loyalty);
        searchOpt.setVisibility(View.VISIBLE);
        searchOpt.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View view) {
                ImageView imageView = (ImageView) view;
                Integer integer = (Integer) imageView.getTag();
                integer = (integer == null ? 0 : integer);
                switch (integer) {
                    case R.drawable.loyalty:
                        imageView.setBackgroundResource(R.drawable.cust_bg);
                        imageView.setTag(R.drawable.cust_bg);
                        searchCriteria = "name";
                        break;
                    case R.drawable.cust_bg:
                        imageView.setBackgroundResource(R.drawable.phone);
                        imageView.setTag(R.drawable.phone);
                        searchCriteria = "phone";
                        break;
                    case R.drawable.phone:
                        imageView.setBackgroundResource(R.drawable.loyalty);
                        imageView.setTag(R.drawable.loyalty);
                        searchCriteria = "card";
                        break;
                }
            }
        });
        customerListDialog.show();
    }

    public void filterCustomerArray(int strCnt, String charVal) {
        int size = tempCustomerList.size();
        boolean status = false;
        for (int ii = 0; ii < size; ii++) {
            if (searchCriteria.equalsIgnoreCase("name")) {
                if (tempCustomerList.get(ii).getCardHolderName().length() >= strCnt) {
                    if (tempCustomerList.get(ii).getCardHolderName().length() == strCnt) {
                        status = true;
                    }
                    if (tempCustomerList.get(ii).getCardHolderName().substring(0, strCnt).equalsIgnoreCase(charVal)) {
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
            } else if (searchCriteria.equalsIgnoreCase("card")) {
                if (tempCustomerList.get(ii).getCardNumber().length() >= strCnt) {
                    if (tempCustomerList.get(ii).getCardNumber().length() == strCnt) {
                        status = true;
                    }
                    if (tempCustomerList.get(ii).getCardNumber().substring(0, strCnt).equalsIgnoreCase(charVal)) {
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
            } else if (searchCriteria.equalsIgnoreCase("phone")) {
                if (tempCustomerList.get(ii).getPhone().length() >= strCnt) {
                    if (tempCustomerList.get(ii).getPhone().length() == strCnt) {
                        status = true;
                    }
                    if (tempCustomerList.get(ii).getPhone().substring(0, strCnt).equalsIgnoreCase(charVal)) {
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

        if (tempCustomerList.size() == 1 && status) {
            selectedCustomer = tempCustomerList.get(0);
            generateLoyaltyData();
            customerListDialog.cancel();
        }
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

    public void setDetails() {
        if (selectedCustomer != null) {
            numberBtn.setText(selectedCustomer.getCardNumber());
            nameTxt.setText(selectedCustomer.getCardHolderName());
            if (selectedCustomer.getCardExpDate().length() > 0) {
                try {
                    expTxt.setText(new SimpleDateFormat("dd-MM-yyyy").format(new SimpleDateFormat("yyyy-MM-dd").parse(selectedCustomer.getCardExpDate())));
                } catch (Exception e) {
                }
            } else {
                expTxt.setText(selectedCustomer.getCardExpDate());
            }
        }
        if (loyaltyPurchaseStatus != null) {
            if (loyaltyPurchaseStatus.getPurchaseValue().length() > 0 && Double.parseDouble(loyaltyPurchaseStatus.getPurchaseValue()) > 0) {
                purchaseTxt.setText("Rs : " + defaultFormat.format(Double.parseDouble(loyaltyPurchaseStatus.getPurchaseValue())));
                imgPurchase.setVisibility(View.VISIBLE);
            } else {
                purchaseTxt.setText("Rs : 0.00");
                imgPurchase.setVisibility(View.INVISIBLE);
                purchaseLayout.setOnClickListener(null);
            }

            if (loyaltyPurchaseStatus.getRwrdPoint().length() > 0 && Double.parseDouble(loyaltyPurchaseStatus.getRwrdPoint()) > 0) {
                accuTxt.setText(loyaltyPurchaseStatus.getRwrdPoint());
                imgAccu.setVisibility(View.VISIBLE);
            } else {
                accuTxt.setText("0");
                imgAccu.setVisibility(View.INVISIBLE);
                accuLayout.setOnClickListener(null);
            }

            if (loyaltyPurchaseStatus.getRdmdPoint().length() > 0 && Double.parseDouble(loyaltyPurchaseStatus.getRdmdPoint()) > 0) {
                reedemedTxt.setText(loyaltyPurchaseStatus.getRdmdPoint());
                imgRdmd.setVisibility(View.VISIBLE);
            } else {
                reedemedTxt.setText("0");
                imgRdmd.setVisibility(View.INVISIBLE);
                rdmdLayout.setOnClickListener(null);
            }
        }
    }


    public void showLoyaltyDtailsDialog(final String mode) {
        transList = transDataHelperObj.getLoyaltyTransList(selectedCustomer.getCardNumber(), reportDuration, startDate, endDate, mode);
        final Dialog loyaltyDetailsDialog = new Dialog(ReportActivityLoyalty.this, R.style.PauseDialog);
        loyaltyDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        loyaltyDetailsDialog.setContentView(R.layout.loyalty_details_dialog);
        loyaltyDetailsDialog.setCancelable(false);
        TextView col1 = (TextView) loyaltyDetailsDialog.findViewById(R.id.txt_col1);
        TextView col2 = (TextView) loyaltyDetailsDialog.findViewById(R.id.txt_col3);
        TextView cust = (TextView) loyaltyDetailsDialog.findViewById(R.id.txt_name);
        cust.setText(selectedCustomer.getCardHolderName());
        if (mode.equalsIgnoreCase("purchase")) {
            col1.setText("Date");
            col2.setText("Purchase Value");
        } else if (mode.equalsIgnoreCase("reward")) {
            col1.setText("Date");
            col2.setText("Points Awarded");
        } else if (mode.equalsIgnoreCase("redeemed")) {
            col1.setText("Date");
            col2.setText("Points Redeemed");
        }
        ListView dialogList = (ListView) loyaltyDetailsDialog.findViewById(R.id.list_report);
        LoyaltyTransAdapter adapter1 = new LoyaltyTransAdapter(ReportActivityLoyalty.this, R.layout.loyalty_details_child, transList, mode);
        dialogList.setAdapter(adapter1);
        ImageView cancel = (ImageView) loyaltyDetailsDialog.findViewById(R.id.back);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                loyaltyDetailsDialog.cancel();
            }
        });
        loyaltyDetailsDialog.show();
    }

}