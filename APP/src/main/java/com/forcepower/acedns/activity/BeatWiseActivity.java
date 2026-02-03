package com.forcepower.acedns.activity;

import androidx.fragment.app.FragmentActivity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
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

import com.forcepower.acedns.adapter.CustomerBeatAdapter;
import com.forcepower.acedns.adapter.RouteAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;

public class BeatWiseActivity extends FragmentActivity implements View.OnClickListener {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewShowDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHideDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static RelativeLayout mRelativeLayoutDuration = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout mCustomDateLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutToday = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutMTD = null;
    @SuppressLint("StaticFieldLeak")
    public static FrameLayout mFrameLayoutCustom = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonEndDate = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmitDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewStartDate = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewEndDate = null;
    @SuppressLint("StaticFieldLeak")
    static AceDnsTransactionDatabase mAceDnsTransactionDatabase;

    Dialog routeDialog;
    AceDnsDatabase mAceDnsDatabase;
    Context mContext;
    Button buttonRoute;
    ArrayList<RouteDetails> routeListForSearching;
    ArrayList<CustomerDetails> customerList;
    CustomerBeatAdapter adapterCust;
    Animation bottomUp, bottomDown, fadeIn, fadeOut;
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    private boolean isStartDate = false;
    Date startDate, endDate;
    Date currentDate = new Date();
    SimpleDateFormat mDFormatFrontEnd, mDFormatBackEnd;
    private String routeCode = "";

    @SuppressLint({"SetTextI18n", "SimpleDateFormat"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_beat_wise_report);

        buttonRoute = findViewById(R.id.buttonRoute);
        mImageViewShowDuration = findViewById(R.id.image_clk);
        mImageViewHideDuration = findViewById(R.id.image_go);

        mFrameLayoutToday = findViewById(R.id.btn_today);
        mFrameLayoutMTD = findViewById(R.id.btn_mtd);
        mFrameLayoutCustom = findViewById(R.id.btn_custom);

        mContext = BeatWiseActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);


        bottomUp = AnimationUtils.loadAnimation(this, R.anim.bottom_up);
        bottomDown = AnimationUtils.loadAnimation(this, R.anim.bottom_down);
        fadeIn = AnimationUtils.loadAnimation(this, R.anim.fadein);
        fadeOut = AnimationUtils.loadAnimation(this, R.anim.fadeut);

        mRelativeLayoutDuration = findViewById(R.id.duration_layout);
        mCustomDateLayout = findViewById(R.id.custom_date_layout);

        mTextViewStartDate = findViewById(R.id.txt_start_date);
        mTextViewEndDate = findViewById(R.id.txt_end_date);

        mTextViewStartDate.setText("");
        mTextViewEndDate.setText("");


        mButtonStartDate = findViewById(R.id.btn_start_date);
        mButtonEndDate = findViewById(R.id.btn_end_date);
        mButtonSubmitDate = findViewById(R.id.btn_date_done);

        mDFormatFrontEnd = new SimpleDateFormat("yyyy-MM-dd");
        mDFormatBackEnd = new SimpleDateFormat("yyyy-MM-dd");

        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        mImageViewShowDuration.setOnClickListener(this);
        mImageViewHideDuration.setOnClickListener(this);
        mFrameLayoutCustom.setOnClickListener(this);
        mFrameLayoutMTD.setOnClickListener(this);
        mFrameLayoutToday.setOnClickListener(this);
        mButtonStartDate.setOnClickListener(this);
        mButtonEndDate.setOnClickListener(this);
        mButtonSubmitDate.setOnClickListener(this);

        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(BeatWiseActivity.this);
        //Beat Wise Customer

        buttonRoute.setOnClickListener(v -> showRouteListDialog());

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (isStartDate) {
                    startDate = date;
                    mTextViewStartDate.setText(mDFormatFrontEnd.format(date));
                } else {
                    endDate = date;
                    mTextViewEndDate.setText(mDFormatFrontEnd.format(date));
                }
                dialogCaldroidFragment.dismiss();
            }
        };
    }

    public void finishCurrentActivity(View v) {
        finish();
    }

    @SuppressLint("SetTextI18n")
    public void showRouteListDialog() {
        final ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteListForBeatWise();
        routeListForSearching = new ArrayList<>(routeList);
        if (routeList.size() != 1) {
            if (routeList.size() > 1) {
                routeDialog = new Dialog(BeatWiseActivity.this, R.style.PauseDialog);
                routeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                routeDialog.setContentView(R.layout.select_from_list);
                routeDialog.setCancelable(false);
                TextView title = routeDialog.findViewById(R.id.title);
                title.setText("Please select a Route");
                ListView dialogList = routeDialog.findViewById(R.id.list);
                final RouteAdapter adapter1 = new RouteAdapter(BeatWiseActivity.this, R.layout.route_list_child, routeList);
                dialogList.setAdapter(adapter1);

                EditText searchText = routeDialog.findViewById(R.id.autoCompleteTextView1);

                searchText.setVisibility(View.VISIBLE);
                searchText.addTextChangedListener(new TextWatcher() {
                    @Override
                    public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                        String searchString = searchText.getText().toString();
                        int textLength = searchString.length();
                        routeList.clear();
                        for (int i = 0; i < routeListForSearching.size(); i++) {
                            String routeName = routeListForSearching.get(i).getRouteName();
                            if (textLength <= routeName.length()) {
                                if (routeName.toLowerCase().contains(searchString.toLowerCase())) {
                                    routeList.add(routeListForSearching.get(i));
                                }
                            }
                        }
                        adapter1.notifyDataSetChanged();
                    }

                    @Override
                    public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                    }

                    @Override
                    public void afterTextChanged(Editable s) {
                        adapter1.notifyDataSetChanged();
                    }
                });

                dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                    routeDialog.cancel();
                    RouteDetails detailsObj = routeList.get(arg2);
                    String routeName = detailsObj.getRouteName();
                    buttonRoute.setText(routeName);
                    routeCode = detailsObj.getRouteCode();
                    showCustomer(detailsObj.getRouteCode());

                });

                Button cancel = routeDialog.findViewById(R.id.btn_cncl);
                cancel.setVisibility(View.INVISIBLE);
                cancel.setOnClickListener(arg0 -> routeDialog.cancel());

                Button create_route = routeDialog.findViewById(R.id.create_route);
                create_route.setVisibility(View.GONE);
                create_route.setOnClickListener(arg0 -> routeDialog.cancel());
                routeDialog.show();
            } else {
                Utils.showToast(BeatWiseActivity.this, "There is no predefined route");
            }
        }

    }

    public void showCustomer(String c) {
        customerList = mAceDnsDatabase.getBeatCustomerList(c);
        Collections.sort(customerList, new Comparator<CustomerDetails>() {
            @Override
            public int compare(CustomerDetails lhs, CustomerDetails rhs) {
                return lhs.getCustomerName().compareTo(rhs.getCustomerName());
            }
        });

        adapterCust = new CustomerBeatAdapter(BeatWiseActivity.this, R.layout.customer_list_child_beat, customerList);
        ListView dialogList = findViewById(R.id.listView);
        dialogList.setAdapter(adapterCust);
    }

    public void showCustomerWithDate(String mtd, String c, String ds, String de) {
        customerList = mAceDnsDatabase.getBeatCustomerListWithDate(mtd, c, ds, de);
        Collections.sort(customerList, new Comparator<CustomerDetails>() {
            @Override
            public int compare(CustomerDetails lhs, CustomerDetails rhs) {
                return lhs.getCustomerName().compareTo(rhs.getCustomerName());
            }
        });

        adapterCust = new CustomerBeatAdapter(BeatWiseActivity.this, R.layout.customer_list_child_beat, customerList);
        ListView dialogList = findViewById(R.id.listView);
        dialogList.setAdapter(adapterCust);
    }

    @Override
    public void onClick(View v) {
        int SELECTION;
        if (v == mImageViewHideDuration) {
            mImageViewShowDuration.startAnimation(fadeIn);
            mImageViewShowDuration.setVisibility(View.VISIBLE);
            mRelativeLayoutDuration.startAnimation(bottomDown);
            mRelativeLayoutDuration.setVisibility(View.GONE);
        } else if (v == mImageViewShowDuration) {
            mImageViewShowDuration.startAnimation(fadeOut);
            mImageViewShowDuration.setVisibility(View.GONE);
            mRelativeLayoutDuration.startAnimation(bottomUp);
            mRelativeLayoutDuration.setVisibility(View.VISIBLE);
        } else if (v == mFrameLayoutToday) {
            SELECTION = 1;
            ChangeBackground(SELECTION);
            showCustomer(routeCode);
        } else if (v == mFrameLayoutMTD) {
            SELECTION = 2;
            ChangeBackground(SELECTION);
            showCustomerWithDate("mtd", routeCode, "", "");
        } else if (v == mFrameLayoutCustom) {
            SELECTION = 3;
            ChangeBackground(SELECTION);
        } else if (v == mButtonStartDate) {
            isStartDate = true;
            ChooseDateDialog();
        } else if (v == mButtonEndDate) {
            isStartDate = false;
            ChooseDateDialog();
        } else if (v == mButtonSubmitDate) {
            try {
                if (endDate.after(currentDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else if (startDate.after(endDate)) {
                    Utils.showToast(mContext, "Start Date should be less than or equal to End Date");
                } else {
                    mCustomDateLayout.startAnimation(bottomUp);
                    mCustomDateLayout.setVisibility(View.GONE);
                    mImageViewShowDuration.startAnimation(fadeOut);
                    mImageViewShowDuration.setVisibility(View.GONE);
                    mRelativeLayoutDuration.startAnimation(bottomUp);
                    mRelativeLayoutDuration.setVisibility(View.VISIBLE);
                    showCustomerWithDate("r", routeCode, mTextViewStartDate.getText().toString(), mTextViewEndDate.getText().toString());
                    mTextViewStartDate.setText("");
                    mTextViewEndDate.setText("");
                }
            } catch (Exception e) {
                Utils.showToast(mContext, "Please choose the Dates again.");
            }
        }
    }

    public void ChangeBackground(int select) {
        mFrameLayoutToday.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mFrameLayoutMTD.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mFrameLayoutCustom.setBackgroundColor(Color.parseColor("#E5E4E2"));
        mCustomDateLayout.startAnimation(bottomUp);
        mCustomDateLayout.setVisibility(View.GONE);

        switch (select) {
            case 1:
                mFrameLayoutToday.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
            case 2:
                mFrameLayoutMTD.setBackgroundColor(Color.parseColor("#B6B6B4"));
                break;
            case 3:
                mFrameLayoutCustom.setBackgroundColor(Color.parseColor("#B6B6B4"));
                mCustomDateLayout.startAnimation(bottomUp);
                mCustomDateLayout.setVisibility(View.VISIBLE);
                break;
        }
    }

    public void ChooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        Bundle bundle = new Bundle();
        bundle.putString(CaldroidFragment.DIALOG_TITLE, "Select a date");
        dialogCaldroidFragment.setArguments(bundle);
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }
}