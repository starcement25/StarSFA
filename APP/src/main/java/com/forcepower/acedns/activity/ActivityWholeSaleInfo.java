package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitWholeSaleTask;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;

public class ActivityWholeSaleInfo extends AceDnsParentActivity {
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    private static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    private static TextView mTextViewCustomerName = null;
    @SuppressLint("StaticFieldLeak")
    private static ImageView mImageViewHeaderLogo = null;
    @SuppressLint("StaticFieldLeak")
    private static RadioGroup mRadioGroupDNC = null;
    @SuppressLint("StaticFieldLeak")
    private static RadioGroup mRadioGroupLB = null;
    @SuppressLint("StaticFieldLeak")
    private static FrameLayout mFrameLayoutSelectCustomer = null;
    @SuppressLint("StaticFieldLeak")
    private static EditText mEditTextMonth = null;
    @SuppressLint("StaticFieldLeak")
    private static EditText mEditTextYear = null;
    @SuppressLint("StaticFieldLeak")
    private static EditText mEditTextClosingStockValue = null;

    GPSTracker gpstracker;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    Context mContext;
    Calendar calnow = Calendar.getInstance();
    private boolean isCustomerOk = true;
    private String mDebitCollection = "";
    private String mDebitReceivedMonth = "";
    private String mDebitReceivedYear = "";
    private String mClosingStockValue = "";
    private String mLogBook = "";
    private AceDnsDatabase mAceDnsDatabase;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private ProgressDialog mProgressDialog;
    private Handler mHandler;
    private int currentMonth = 0, currentYear = 0;

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_wholesaleinfo);
        RegisterActivities.registerActivity(this);
        currentMonth = (calnow.get(Calendar.MONTH) + 1);
        currentYear = (calnow.get(Calendar.YEAR));
        InitializeView();
        ClearData();

        mContext = ActivityWholeSaleInfo.this;

        mAceDnsDatabase = new AceDnsDatabase(ActivityWholeSaleInfo.this);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(ActivityWholeSaleInfo.this);

        mRadioGroupDNC.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection = findViewById(checkedId);
            if (radioSelection.getText().toString().equalsIgnoreCase("Yes")) {
                mDebitCollection = "Yes";
            } else {
                mDebitCollection = "No";
            }
        });

        mRadioGroupLB.setOnCheckedChangeListener((group, checkedId) -> {
            RadioButton radioSelection = findViewById(checkedId);
            if (radioSelection.getText().toString().equalsIgnoreCase("Yes")) {
                mLogBook = "Yes";
            } else {
                mLogBook = "No";
            }
        });

        mButtonSubmit.setOnClickListener(v -> {
            mDebitReceivedMonth = mEditTextMonth.getText().toString();
            mDebitReceivedYear = mEditTextYear.getText().toString();
            mClosingStockValue = mEditTextClosingStockValue.getText().toString();

            if (mDebitReceivedYear.trim().length() == 4 && !mDebitReceivedMonth.trim().isEmpty()) {
                boolean isValidYearmonth = MonthYearValidation(Integer.parseInt(mDebitReceivedMonth), Integer.parseInt(mDebitReceivedYear));
                if (isValidYearmonth) {
                    if (isCustomerOk && !mDebitCollection.isEmpty() && !mLogBook.isEmpty()) {
                        gpstracker = new GPSTracker(mContext);
                        boolean LocationEnabled = gpstracker.canGetLocation();
                        if (LocationEnabled) {
                            mButtonSubmit.setEnabled(false);
                            FlowofOrder(3);
                        }
                    } else {
                        if (!isCustomerOk) {
                            Utils.showToast(mContext, "Please select the customer");
                        } else if (mDebitCollection.isEmpty()) {
                            Utils.showToast(mContext, "Please select debit note collected");
                        } else {
                            Utils.showToast(mContext, "Please select the log book");
                        }
                    }
                } else {
                    Utils.showToast(mContext, "Please provide valid input in month");
                }
            } else {
                Utils.showToast(mContext, "Please provide valid input in last debit not received");
            }
        });

        mButtonBack.setOnClickListener(v -> finish());

        mHandler = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialog.dismiss();
                final int step = msg.getData().getInt("STEP");
                ActivityWholeSaleInfo.this.runOnUiThread(() -> {
                    switch (step) {
                        case 2:
                            if (mCustomerDetailsList.size() > 1) {
                                ShowCustomerListDialog();
                            } else if (mCustomerDetailsList.size() == 1) {
                                isCustomerOk = true;
                                Constants.selectedCustomer = mCustomerDetailsList.get(0);
                                mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
                            } else {
                                Toast.makeText(mContext, "There is no distributor for taking whole sale information", Toast.LENGTH_LONG).show();
                                isCustomerOk = false;
                                finish();
                            }
                            break;
                        case 3:
                            new TRANS_SubmitWholeSaleTask(mContext, "SUBMIT").execute();
                            break;
                    }
                });
            }
        };

        ChangeBackgroundColor();
        FlowofOrder(2);
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        mFrameLayoutSelectCustomer = findViewById(R.id.frameLayoutSelectCustomer);
        mTextViewCustomerName = findViewById(R.id.textViewCustomerValue);
        mRadioGroupDNC = findViewById(R.id.radioSelectDebitNote);
        mRadioGroupLB = findViewById(R.id.radioSelectLogBook);
        mEditTextMonth = findViewById(R.id.editTextMonth);
        mEditTextYear = findViewById(R.id.editTextYear);
        mEditTextClosingStockValue = findViewById(R.id.editTextClosingStockValue);
        mButtonSubmit = findViewById(R.id.btn_Submi);
        mButtonBack = findViewById(R.id.back);
        mImageViewHeaderLogo = findViewById(R.id.imagelogo);
        TextView txtVersion = findViewById(R.id.txt_version);

        mFrameLayoutSelectCustomer.setOnClickListener(this);
        txtVersion.setText(Utils.getAppVersion(ActivityWholeSaleInfo.this) + "~" + Utils.getDBVersion(ActivityWholeSaleInfo.this));
    }

    @Override
    public void onClick(View v) {
        if (v == mFrameLayoutSelectCustomer) {
            ChangeBackgroundColor();
            FlowofOrder(2);
        }
    }

    @SuppressLint("SetTextI18n")
    public void ClearData() {
        mTextViewCustomerName.setText("Customer Name");
    }

    private void ChangeBackgroundColor() {
        mFrameLayoutSelectCustomer.setBackgroundColor(Color.parseColor("#E0FFFF"));
        mFrameLayoutSelectCustomer.setBackgroundColor(Color.parseColor("#7EB5D6"));
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

    private boolean MonthYearValidation(int inputMonth, int inputYear) {
        boolean isValidYearMonth = false;
        if (inputMonth > 0 && inputYear > 0) {
            if (currentMonth == 1) {
                if ((inputMonth == 1 && inputYear == currentYear) || (inputMonth == 12 && inputYear == currentYear - 1)) {
                    isValidYearMonth = true;
                }
            } else {
                if ((inputMonth == currentMonth || inputMonth == currentMonth - 1) && (inputYear == currentYear)) {
                    isValidYearMonth = true;
                }
            }
        }
        return isValidYearMonth;
    }

    public void FlowofOrder(final int step) {
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Please wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
        new Thread() {
            @SuppressLint("SimpleDateFormat")
            public void run() {
                switch (step) {
                    case 2:
                        String today = Constants.dateString.substring(6, 8) + "-" + Constants.dateString.substring(4, 6) + "-" + Constants.dateString.substring(0, 4);
                        mCustomerDetailsList = mAceDnsDatabase.GetCustomerListforRoutePlanWholeSale(today, Constants.dateString);
                        break;
                    case 3:
                        String timeStamp;
                        timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        if (Constants.selectedFsSurveyPublish != null) {
                            mAceDnsTransactionDatabase.UpadateFsSurveyPublishStatus(Constants.selectedFsSurveyPublish.getFsSurveyId());
                        }
                        String transid = "W" + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                        mAceDnsTransactionDatabase.InsertWholeSaleDetails(transid, Constants.selectedCustomer.getCustomerCode(), mDebitCollection, mDebitReceivedMonth + "/" + mDebitReceivedYear, mClosingStockValue, mLogBook);
                        mAceDnsTransactionDatabase.insertToLocationTable("W", timeStamp);
                        gpstracker.stopUsingGPS();
                        break;
                }
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putInt("STEP", step);
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);
        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a distributor");
        EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
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
        ListView dialogList = mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogCustomer.cancel();
            isCustomerOk = true;
            Constants.selectedCustomer = adapterCust.getItem(arg2);
            assert Constants.selectedCustomer != null;
            mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());

        });
        Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();
    }
}
