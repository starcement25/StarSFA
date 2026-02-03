package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
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
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.FeedBackAdapter;
import com.forcepower.acedns.adapter.SaudaRouteAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitFeedBack;
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.getPositionOfCurrentCheckedInRoute;

import androidx.annotation.NonNull;

public class ActivityMarketFeedBack extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonNoReplaceMent = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSelectRoute = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSelectOil = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;

    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCol1 = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCol2 = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCol3 = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCol4 = null;

    @SuppressLint("StaticFieldLeak")
    public static ListView mProductListView = null;
    public Context mContext;
    FeedBackAdapter mFeedBackAdapter;
    RouteDetails mRouteDetails;
    String[] values;
    ProgressDialog mPrepareFeedBackProgressDialog;
    Handler mPrepareFeedBackHandler;
    private String mOilName = "";
    private String mCompanyName = "";
    private String mRouteCode = "";
    private boolean isRouteSelected = false;
    private boolean isOilSelected = false;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;

    @SuppressLint("HandlerLeak")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_market_feedback);
        RegisterActivities.registerActivity(this);

        Constants.isFromConfirmationActivity = false;

        mContext = ActivityMarketFeedBack.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);

        Constants.selectedProductMasterList = new ArrayList<>();

        InitializeView();

        if (!Constants.marketFeedbackDetailsObj.getMfGroupEnable().equalsIgnoreCase("yes")) {
            isOilSelected = true;
        }

        mPrepareFeedBackHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mPrepareFeedBackProgressDialog.cancel();
                final int jobToDo = msg.getData().getInt("JOBALLOCATE");
                ActivityMarketFeedBack.this.runOnUiThread(() -> {
                    switch (jobToDo) {
                        case 1:
                            if (values.length > 0) {
                                if (Constants.marketFeedbackDetailsObj.getMfGroupEnable().equalsIgnoreCase("yes")) {
                                    ShowCompetitorList();
                                } else {
                                    PrepareFeedBackData(2);
                                }
                            } else {
                                Utils.showToast(mContext, "No product found");
                            }
                            break;
                        case 2:
                            ParseCompanyList(mCompanyName);
                            mFeedBackAdapter = new FeedBackAdapter(mContext, R.layout.feedback_list, Constants.selectedFeedBackList);
                            mProductListView.setAdapter(mFeedBackAdapter);
                            mProductListView.setDivider(null);
                            mButtonSubmit.setEnabled(true);
                            break;
                        case 3:
                            new TRANS_SubmitFeedBack(mContext, true, "SUBMIT").execute();
                            break;
                    }
                });
            }
        };
    }

    @Override
    public void onResume() {
        super.onResume();
        if (Constants.logoBmp != null) {
            mImageViewHeaderLogo.setVisibility(View.VISIBLE);
            mImageViewHeaderLogo.setImageBitmap(Constants.logoBmp);
        } else {
            mImageViewHeaderLogo.setVisibility(View.GONE);
        }
    }

    /**
     * Called when the activity is first created. Initializes the UI for users
     * interaction.
     */
    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        mButtonSubmit = findViewById(R.id.buttonSubmit);
        mButtonSubmit.setOnClickListener(ActivityMarketFeedBack.this);
        mButtonSubmit.setEnabled(false);

        mButtonNoReplaceMent = findViewById(R.id.no_ordr);
        mButtonNoReplaceMent.setOnClickListener(ActivityMarketFeedBack.this);
        mButtonNoReplaceMent.setVisibility(View.INVISIBLE);

        mButtonBack = findViewById(R.id.back);
        mButtonBack.setOnClickListener(ActivityMarketFeedBack.this);

        mButtonSelectRoute = findViewById(R.id.buttonSelectRoute);
        mButtonSelectRoute.setOnClickListener(ActivityMarketFeedBack.this);

        mButtonSelectOil = findViewById(R.id.buttonSelectOil);
        mButtonSelectOil.setOnClickListener(ActivityMarketFeedBack.this);

        mProductListView = findViewById(R.id.list);

        if (Constants.marketFeedbackDetailsObj.getMfGroupEnable().equalsIgnoreCase("yes")) {
            mButtonSelectOil.setVisibility(View.VISIBLE);
        } else {
            mButtonSelectOil.setVisibility(View.GONE);
        }

        mTextViewCol1 = findViewById(R.id.txt_col1);
        mTextViewCol2 = findViewById(R.id.txt_col2);
        mTextViewCol3 = findViewById(R.id.txt_col3);
        mTextViewCol4 = findViewById(R.id.txt_col4);

        if (!Constants.marketFeedbackDetailsObj.getMfCol1().trim().isEmpty()) {
            mTextViewCol1.setText(Constants.marketFeedbackDetailsObj.getMfCol1());
        } else {
            mTextViewCol1.setVisibility(View.GONE);
        }

        if (!Constants.marketFeedbackDetailsObj.getMfCol2().trim().isEmpty()) {
            mTextViewCol2.setText(Constants.marketFeedbackDetailsObj.getMfCol2());
        } else {
            mTextViewCol2.setVisibility(View.GONE);
        }

        if (!Constants.marketFeedbackDetailsObj.getMfCol3().trim().isEmpty()) {
            mTextViewCol3.setText(Constants.marketFeedbackDetailsObj.getMfCol3());
        } else {
            mTextViewCol3.setVisibility(View.GONE);
        }

        if (!Constants.marketFeedbackDetailsObj.getMfCol4().trim().isEmpty()) {
            mTextViewCol4.setText(Constants.marketFeedbackDetailsObj.getMfCol4());
        } else {
            mTextViewCol4.setVisibility(View.GONE);
        }
    }

    public void onClick(View clkdView) {
        if (clkdView == mButtonSelectOil) {
            PrepareFeedBackData(1);
        }
        if (clkdView == mButtonSubmit) {
            new GPSTracker(mContext);
            if (isOilSelected && isRouteSelected) {
                Boolean isDataOk = checkInPutOkOrNot();
                if (isDataOk) {
                    PrepareFeedBackData(3);
                }

            } else if (!isOilSelected && isRouteSelected) {
                Toast.makeText(mContext, "Please select product", Toast.LENGTH_LONG).show();
            } else if (isOilSelected) {
                Toast.makeText(mContext, "Please select route", Toast.LENGTH_LONG).show();
            } else {
                Toast.makeText(mContext, "Please select route and product", Toast.LENGTH_LONG).show();
            }
        }
        if (clkdView == mButtonBack) {
            finish();
        }
        if (clkdView == mButtonSelectRoute) {
            ShowRoute();
        }
    }

    /**
     * This method is called to fetch route from local DB.
     */
    public void ShowRoute() {
        ArrayList<RouteDetails> routeList = mAceDnsDatabase.getRouteList();
        if (NotCheckedOut(mContext)) {
            mRouteDetails = routeList.get(getPositionOfCurrentCheckedInRoute(false, mContext, null, routeList));
            mRouteCode = mRouteDetails.getRouteCode();
            isRouteSelected = true;
            mButtonSelectRoute.setText(mRouteDetails.getRouteName());
            mButtonSelectRoute.setEnabled(false);
            if (!Constants.marketFeedbackDetailsObj.getMfGroupEnable().equalsIgnoreCase("yes")) {
                PrepareFeedBackData(2);
            }
        } else if (routeList.size() == 1) {
            mRouteDetails = routeList.get(0);
            mRouteCode = mRouteDetails.getRouteCode();
            isRouteSelected = true;
            mButtonSelectRoute.setText(mRouteDetails.getRouteName());
            mButtonSelectRoute.setEnabled(false);
            if (!Constants.marketFeedbackDetailsObj.getMfGroupEnable().equalsIgnoreCase("yes")) {
                PrepareFeedBackData(2);
            }
        } else if (routeList.size() > 1) {
            ShowRouteListDialog(routeList);
        } else {
            Toast.makeText(mContext, "No route found", Toast.LENGTH_LONG).show();
        }
    }

    /**
     * This method is called to show Route dialog.
     * An user can select route from the dialog list.
     */
    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {

        final Dialog mDialogRoute = new Dialog(ActivityMarketFeedBack.this, R.style.PauseDialog);
        mDialogRoute.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogRoute.setContentView(R.layout.select_with_search);
        mDialogRoute.setCancelable(false);
        TextView title = mDialogRoute.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = mDialogRoute.findViewById(R.id.list);
        final SaudaRouteAdapter adapter = new SaudaRouteAdapter(ActivityMarketFeedBack.this, R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);

        EditText searchText = mDialogRoute.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            mDialogRoute.cancel();
            mRouteDetails = adapter.getItem(arg2);
            assert mRouteDetails != null;
            mRouteCode = mRouteDetails.getRouteCode();
            isRouteSelected = true;
            mButtonSelectRoute.setText(mRouteDetails.getRouteName());
            mButtonSelectRoute.setEnabled(false);
            if (!Constants.marketFeedbackDetailsObj.getMfGroupEnable().equalsIgnoreCase("yes")) {
                PrepareFeedBackData(2);
            }
        });

        Button cancel = mDialogRoute.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mDialogRoute.show();
    }

    @SuppressLint("SimpleDateFormat")
    public boolean SaveDatatoDatabase() {
        boolean isSucess;
        String transactiotype = "MF";
        try {
            String timeStamp = "";
            timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

            mAceDnsTransactionDatabase.INSERTtoMarketFeedback(timeStamp, transactiotype, mRouteCode);
            mAceDnsTransactionDatabase.insertToLocationTable(transactiotype, timeStamp);
            isSucess = true;
        } catch (Exception ex) {
            isSucess = false;
        }
        return isSucess;
    }

    private Boolean checkInPutOkOrNot() {
        boolean isInputOk = true;
        try {
            for (int count = 0; count < Constants.selectedFeedBackList.size(); count++) {
                if (isInputOk) {
                    MarketFeedback masterObj = Constants.selectedFeedBackList.get(count);
                    String compName = masterObj.getCopmpetitorName();
                    String ptd = masterObj.getPtd();
                    String ptr = masterObj.getPtr();
                    String ptc = masterObj.getPtc();
                    if (!ptr.isEmpty()) {
                        if (ptd.isEmpty() || (Double.parseDouble(ptd) > Double.parseDouble(ptr))) {
                            Utils.showToast(mContext, "PTR value must be grater than PTD for " + compName);
                            isInputOk = false;
                        }
                    }
                    if (!ptc.isEmpty()) {
                        if (ptr.isEmpty() || (Double.parseDouble(ptr) > Double.parseDouble(ptc))) {
                            Utils.showToast(mContext, "PTC can not be grater than PTR value for " + compName);
                            isInputOk = false;
                        }
                    }
                }
            }
        } catch (Exception ignored) {
        }
        return isInputOk;
    }

    @SuppressLint("SetTextI18n")
    public void ShowCompetitorList() {
        final Dialog mCompetitorDialog = new Dialog(ActivityMarketFeedBack.this, R.style.PauseDialog);
        mCompetitorDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mCompetitorDialog.setContentView(R.layout.select_with_search);
        mCompetitorDialog.setCancelable(false);

        TextView title = mCompetitorDialog.findViewById(R.id.title);
        title.setText("Please select a product");
        ListView dialogList = mCompetitorDialog.findViewById(R.id.list);

        final ArrayAdapter<String> adapter = new ArrayAdapter<>(this, R.layout.activity_listview, values);
        dialogList.setAdapter(adapter);

        EditText searchText = mCompetitorDialog.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });

        dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
            mOilName = values[position];
            mButtonSelectOil.setText(mOilName);
            isOilSelected = true;
            mCompetitorDialog.cancel();
            PrepareFeedBackData(2);
        });

        Button cancel = mCompetitorDialog.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mCompetitorDialog.show();
    }

    public void PrepareFeedBackData(final int task) {
        mPrepareFeedBackProgressDialog = new ProgressDialog(mContext);
        if (task == 3) {
            mPrepareFeedBackProgressDialog.setMessage("Saving Data.Please wait..");
        } else {
            mPrepareFeedBackProgressDialog.setMessage("Fetching Data.Please wait..");
        }
        mPrepareFeedBackProgressDialog.show();
        new Thread() {
            public void run() {
                switch (task) {
                    case 1:
                        int max = 0;
                        max = mAceDnsDatabase.GetOilName("", "");
                        values = new String[max];
                        if (max > 0) {
                            System.arraycopy(Constants.mProductGrpouList, 0, values, 0, Constants.mProductGrpouList.length);
                        }
                        break;
                    case 2:
                        if (Constants.marketFeedbackDetailsObj.getMfGroupEnable().equalsIgnoreCase("yes")) {
                            mCompanyName = mAceDnsDatabase.GetCompetitorName(mOilName);
                        } else {
                            mCompanyName = mAceDnsDatabase.GetCompetitorName();
                        }
                        break;
                    case 3:
                        SaveDatatoDatabase();
                        break;
                }
                Message msg = mPrepareFeedBackHandler.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOBALLOCATE", task);
                msg.setData(bundle);
                mPrepareFeedBackHandler.sendMessage(msg);
            }
        }.start();
    }

    public void ParseCompanyList(String name) {
        Constants.selectedFeedBackList = new ArrayList<>();
        if (name.contains(",")) {
            String[] RowData = name.split(",");
            for (String rowDatum : RowData) {
                MarketFeedback temp = new MarketFeedback();
                temp.setProductGroup(mOilName);
                temp.setRouteCode(mRouteCode);
                temp.setCopmpetitorName(rowDatum);
                temp.setPtd("");
                temp.setPtr("");
                temp.setPtc("");
                temp.setPv("");
                Constants.selectedFeedBackList.add(temp);
            }
        } else {
            MarketFeedback temp = new MarketFeedback();
            temp.setProductGroup(mOilName);
            temp.setRouteCode(mRouteCode);
            temp.setCopmpetitorName(name);
            temp.setPtd("");
            temp.setPtr("");
            temp.setPtc("");
            temp.setPv("");
            Constants.selectedFeedBackList.add(temp);
        }
    }
}
