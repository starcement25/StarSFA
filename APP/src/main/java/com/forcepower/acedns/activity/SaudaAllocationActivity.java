package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.InputType;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.inputmethod.EditorInfo;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.LinearLayout.LayoutParams;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import com.forcepower.acedns.adapter.EmployeeAdapter;
import com.forcepower.acedns.backgroundTask.DownLoadSaudaAllocation;
import com.forcepower.acedns.backgroundTask.MASTER_LoadSaudaTransactionLog;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitSaudaAllocation;
import com.forcepower.acedns.bean.EmployeeMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import com.forcepower.acedns.R;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

public class SaudaAllocationActivity extends AceDnsParentActivity {

    /*
     * Layout Views
     */

    public static ImageView mImageViewHeaderLogo = null;
    public static LinearLayout mParentLayout = null;
    public static LinearLayout mFrameLayout = null;
    public static Button mButtonBack = null;
    public static Button mButtonNoAlloocation = null;
    public static Button mButtonSelectEmployee = null;
    public static Button mButtonSubmit = null;
    public static TextView mTextViewFrameText = null;
    final String mQuantityOK = "OK";
    final String mQuantityEXCEED = "EXCEED";
    final String mEditableQuantityLOW = "LOW";
    public Context mContext;
    public boolean isFinished = false;
    public boolean isBoss = false;
    public String mProductGroupName = "";
    public String mValidation = "";
    public String mReportingto = "";
    public String mAllocation = "";
    public String mOwnAllocation = "";
    public String mCurrentEmployeeName = "";
    public String mParentEmpCode = "";
    public String mChildEmpCode = "";
    public String mType = "";
    ProgressDialog mProgressDialogSaudaAllocation;
    Handler mHandlerSaudaAllocation;
    ArrayList<EmployeeMasterDetails> mEmployeeMasterDetailsList;
    EmployeeMasterDetails mEmployeeMasterDetails;
    private List<EditText> mEditTextList;
    private AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    private AceDnsDatabase mAceDnsDatabase;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activty_sauda_allocation_hierachywise);
        RegisterActivities.registerActivity(this);

        mContext = SaudaAllocationActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        InitializeView();

        mEditTextList = new ArrayList<EditText>();
        mParentEmpCode = Constants.employeeDetailObject.getEmpCode();

        mHandlerSaudaAllocation = new Handler() {
            public void handleMessage(Message msg) {
                mProgressDialogSaudaAllocation.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                SaudaAllocationActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        switch (jobToDo) {
                            case 1:
                                if (isBoss) {
                                    ShowEmployee();
                                } else {
                                    if (mOwnAllocation.equalsIgnoreCase("0")) {
                                        Utils.showToast(mContext, "You have no allocation " + Constants.employeeDetailObject.getEmpName());
                                    } else {
                                        ShowEmployee();
                                    }
                                }
                                break;
                            case 2:
                                if (Constants.mSaudaAllocationList.size() > 0) {
                                    //ConverttoTon();
                                    DrawView();

                                } else {
                                    mParentLayout.removeAllViews();
                                    Utils.showToast(mContext, "No product found");
                                }
                                break;
                            case 3:
                                new TRANS_SubmitSaudaAllocation(mContext, true).execute();
                                break;
                            case 4:

                                break;
                            case 5:
                                if (mAllocation.equalsIgnoreCase("0") && isBoss == false) {
                                    Utils.showToast(mContext,
                                            "Please allocate the immediate boss of "
                                                    + mEmployeeMasterDetails
                                                    .getEmpName());
                                }
                                if (mAllocation.equalsIgnoreCase("0") == false && isBoss == true) {
                                    mType = "ADD";
                                    PrepareSaudaAccessData(2, "");
                                } else {
                                    ShowConditionofAllocation();
                                }
                                break;

                        }
                    }
                });
            }
        };

        PrepareSaudaAccessData(4, "");

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

    public void InitializeView() {
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~"
                + Utils.getDBVersion(mContext));

        mImageViewHeaderLogo = (ImageView) findViewById(R.id.imagelogo);

        mButtonNoAlloocation = (Button) findViewById(R.id.no_ordr);
        mButtonNoAlloocation.setVisibility(View.GONE);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonBack.setOnClickListener(SaudaAllocationActivity.this);


        mButtonSubmit = (Button) findViewById(R.id.buttonSubmit);
        mButtonSubmit.setOnClickListener(SaudaAllocationActivity.this);

        mButtonSelectEmployee = (Button) findViewById(R.id.buttonSelectEmployee);
        mButtonSelectEmployee.setOnClickListener(SaudaAllocationActivity.this);

        //mProductListView=(ListView) findViewById(R.id.list);

        mParentLayout = (LinearLayout) findViewById(R.id.scrollLayout);
        mFrameLayout = (LinearLayout) findViewById(R.id.dynamicFramelayout);

    }

    public void onClick(View clkdView) {

        if (clkdView == mButtonBack) {
            finish();
        }
        if (clkdView == mButtonSelectEmployee) {
            mParentEmpCode = Constants.employeeDetailObject.getEmpCode();
            PrepareSaudaAccessData(1, Constants.employeeDetailObject.getEmpCode());
        }
        if (clkdView == mButtonSubmit) {
            if (mEditTextList.size() > 0) {
                for (EditText editText : mEditTextList) {
                    String tag = editText.getTag().toString();
                    String value = editText.getText().toString();
                    SetData(Integer.parseInt(tag), value, mType);
                }
            }

            if (Constants.mSaudaAllocationList != null) {
                if (CheckValdation()) {
                    mButtonSubmit.setEnabled(false);
                    new GPSTracker(mContext);
                    //RemoveSaudaAllocation();
                    PrepareSaudaAccessData(3, "");
                } else {
                    if (mValidation.equalsIgnoreCase(mQuantityEXCEED)) {
                        Utils.showToast(mContext, "Exceed Quantity " + mProductGroupName);
                    } else {
                        Utils.showToast(mContext, mEmployeeMasterDetails.getEmpName() + " own allocation on " + mProductGroupName + " is higher than the editable quantity");
                    }
                    mValidation = "";
                    mProductGroupName = "";
                }
            }
        }
    }

    public void PrepareSaudaAccessData(final int task, final String empcode) {
        mProgressDialogSaudaAllocation = new ProgressDialog(mContext);
        mProgressDialogSaudaAllocation.setCancelable(false);
        if (task == 3) {
            mProgressDialogSaudaAllocation.setMessage("Saving data.\nPlease wait..");
        } else if (task == 4) {
            mProgressDialogSaudaAllocation.setMessage("Downloading data.\nPlease wait..");
        } else {

            mProgressDialogSaudaAllocation.setMessage("Fetching data from database.\nPlease wait..");
        }
        mProgressDialogSaudaAllocation.show();
        new Thread() {
            public void run() {

                switch (task) {

                    case 1:
                        mEmployeeMasterDetailsList = mAceDnsDatabase.GetEmployeeForSaudaAllocation(empcode);
                        if (mAceDnsDatabase.CheckBoss(empcode)) {
                            isBoss = true;
                        } else {
                            isBoss = false;
                        }
                        mAllocation = mAceDnsDatabase.GetAllocationinHierarchy(empcode);
                        mOwnAllocation = mAceDnsDatabase.GetOwnAllocationinHierarchy(empcode);
                        break;
                    case 2:
                        mAceDnsDatabase.GetProductGroupList(mParentEmpCode, mEmployeeMasterDetails.getEmpCode(), isBoss, mType);
                        break;
                    case 3:
                        SaveDatatoDatabase();
                        break;
                    case 4:
                        DownLoadSaudaAllocation downLoadSaudaAllocation = new DownLoadSaudaAllocation(mContext);
                        downLoadSaudaAllocation.execute();
                        while (isFinished == false) {
                            if (downLoadSaudaAllocation.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (downLoadSaudaAllocation.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (downLoadSaudaAllocation.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                        isFinished = false;
                        downLoadSaudaAllocation = null;

                        MASTER_LoadSaudaTransactionLog downLoadSaudaAllocationLog = new MASTER_LoadSaudaTransactionLog(mContext);
                        downLoadSaudaAllocationLog.execute();
                        while (isFinished == false) {
                            if (downLoadSaudaAllocationLog.getStatus() == AsyncTask.Status.PENDING) {
                                isFinished = false;
                            }
                            if (downLoadSaudaAllocationLog.getStatus() == AsyncTask.Status.RUNNING) {
                                isFinished = false;
                            }
                            if (downLoadSaudaAllocationLog.getStatus() == AsyncTask.Status.FINISHED) {
                                isFinished = true;
                            }
                        }
                        isFinished = false;
                        downLoadSaudaAllocationLog = null;
                        mAceDnsDatabase.GetConversionFactorProductGroupWise();
                        break;

                    case 5:
                        mAllocation = mAceDnsDatabase.GetAllocationinHierarchy(mEmployeeMasterDetails.getEmpCode());
                        break;
                }

                Message msg = mHandlerSaudaAllocation.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerSaudaAllocation.sendMessage(msg);
            }
        }.start();
    }


    public void ShowEmployee() {
        if (mEmployeeMasterDetailsList.size() > 0) {
            if (mEmployeeMasterDetailsList.size() == 1) {
                mEmployeeMasterDetails = mEmployeeMasterDetailsList.get(0);
                mButtonSelectEmployee.setText(mEmployeeMasterDetails.getEmpName());
                mCurrentEmployeeName = mEmployeeMasterDetails.getEmpName();

                if (isBoss) {
                    String allocation = mAceDnsDatabase.GetOwnAllocationinHierarchy(mEmployeeMasterDetails.getEmpCode());
                    if (allocation.equalsIgnoreCase("0")) {
                        mType = "ADD";
                        PrepareSaudaAccessData(2, "");
                    } else {
                        ShowConditionofAllocation();
                    }
                } else {
                    String allocation = mAceDnsDatabase.GetOwnAllocationinHierarchy(mEmployeeMasterDetails.getEmpCode());
                    if (allocation.equalsIgnoreCase("0")) {
                        mType = "ADD";
                        PrepareSaudaAccessData(2, "");
                    } else {
                        ShowConditionofAllocation();
                    }
                }
            } else {
                ShowEmployeeDialog();
            }

        } else {
            Utils.showToast(mContext, "No employee found");
        }
    }

    public void ShowEmployeeDialog() {
        EmployeeAdapter adapter = new EmployeeAdapter(SaudaAllocationActivity.this, R.layout.customer_broker_list_child, mEmployeeMasterDetailsList);
        final Dialog dialogEmployeeList = new Dialog(SaudaAllocationActivity.this, R.style.PauseDialog);
        dialogEmployeeList.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogEmployeeList.setContentView(R.layout.select_from_list);
        dialogEmployeeList.setCancelable(false);
        TextView title = (TextView) dialogEmployeeList.findViewById(R.id.title);
        title.setText("Please select an employee");

        ListView list = (ListView) dialogEmployeeList.findViewById(R.id.list);
        list.setAdapter(adapter);
        list.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int pos, long arg3) {
                mEmployeeMasterDetails = mEmployeeMasterDetailsList.get(pos);
                mButtonSelectEmployee.setText(mEmployeeMasterDetails.getEmpName());
                mCurrentEmployeeName = mEmployeeMasterDetails.getEmpName();
                dialogEmployeeList.cancel();

                if (isBoss) {
                    String allocation = mAceDnsDatabase.GetOwnAllocationinHierarchy(mEmployeeMasterDetails.getEmpCode());
                    if (allocation.equalsIgnoreCase("0")) {
                        mType = "ADD";
                        PrepareSaudaAccessData(2, "");
                    } else {
                        ShowConditionofAllocation();
                    }
                } else {
                    String allocation = mAceDnsDatabase.GetOwnAllocationinHierarchy(mEmployeeMasterDetails.getEmpCode());
                    if (allocation.equalsIgnoreCase("0")) {
                        mType = "ADD";
                        PrepareSaudaAccessData(2, "");
                    } else {
                        ShowConditionofAllocation();
                    }
                }
            }
        });

        Button cancel = (Button) dialogEmployeeList.findViewById(R.id.btn_cncl);
        cancel.setVisibility(View.GONE);
        dialogEmployeeList.show();

    }

    public boolean SaveDatatoDatabase() {
        boolean isSuccess = false;
        try {
            String transactiotype = "FA";
            String timeStamp = "";
            timeStamp = Constants.dateString
                    + new SimpleDateFormat("HHmmss").format(Calendar
                    .getInstance().getTime());
            mAceDnsTransactionDatabase.INSERTtoSaudaAllocation(timeStamp, transactiotype, mEmployeeMasterDetails.getEmpCode());
            mAceDnsTransactionDatabase.insertToLocationTable(transactiotype, timeStamp);
            mAceDnsTransactionDatabase.UpdateAllocation();
            isSuccess = true;
        } catch (Exception ex) {
            ex.printStackTrace();
        }
        return isSuccess;
    }

    public boolean CheckValdation() {
        boolean isValidate = true;
        String validationtype = "";
        for (int count = 0; count < Constants.mSaudaAllocationList.size(); count++) {
            validationtype = Constants.mSaudaAllocationList.get(count).getValidation();
            if (validationtype.equalsIgnoreCase(mEditableQuantityLOW) || validationtype.equalsIgnoreCase(mQuantityEXCEED)) {
                mValidation = validationtype;
                mProductGroupName = Constants.mSaudaAllocationList.get(count).getProductFilterName();
                isValidate = false;
                break;
            }
        }
        return isValidate;
    }

    public void ConverttoTon() {
        double qtyinltr = 0;
        double conversionfactortwo = 0;
        double qtyinton = 0;
        for (int count = 0; count < Constants.mSaudaAllocationList.size(); count++) {
            qtyinltr = Double.parseDouble(Constants.mSaudaAllocationList.get(count).getQuantityinLtr());
            conversionfactortwo = Double.parseDouble(Constants.mSaudaAllocationList.get(count).getConversionFactorTwo());
            if (conversionfactortwo > 0) {
                qtyinton = qtyinltr / conversionfactortwo;
                Constants.mSaudaAllocationList.get(count).setQuantityinTon(String.valueOf(qtyinton));
            } else {
                Constants.mSaudaAllocationList.get(count).setQuantityinTon("0");
            }
        }
    }

    public void ShowConditionofAllocation() {
        final Dialog dialgoCondition = new Dialog(SaudaAllocationActivity.this, R.style.PauseDialog);
        dialgoCondition.setCancelable(false);
        dialgoCondition.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialgoCondition.setContentView(R.layout.condition_layout);
        TextView txtMsg = (TextView) dialgoCondition
                .findViewById(R.id.title);
        txtMsg.setText("Select an Option.");

        final RadioGroup radioSelectionGroup = (RadioGroup) dialgoCondition
                .findViewById(R.id.radioSelect);
        ;

        radioSelectionGroup
                .setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                    public void onCheckedChanged(RadioGroup group, int checkedId) {
                        RadioButton radioSelection = (RadioButton) dialgoCondition
                                .findViewById(checkedId);
                        if (radioSelection.getText().equals("Edit")) {
                            mType = "EDIT";
                            //Utils.showToast(mContext, "This feature is not available");
                            PrepareSaudaAccessData(2, "");
                        } else {
                            mParentEmpCode = mEmployeeMasterDetails.getEmpCode();
                            mType = "ADD";
                            PrepareSaudaAccessData(1, mEmployeeMasterDetails.getEmpCode());

                        }
                        dialgoCondition.cancel();
                    }
                });
        dialgoCondition.show();
    }

    public void RemoveSaudaAllocation() {
        for (int count = 0; count < Constants.mSaudaAllocationList.size(); count++) {
            String quantity = Constants.mSaudaAllocationList.get(count).getQty().trim();
            if (quantity.equalsIgnoreCase("0")) {
                Constants.mSaudaAllocationList.remove(count);
            }
        }
    }

    public void DrawView() {
        mParentLayout.removeAllViews();
        mFrameLayout.removeAllViews();

        LinearLayout.LayoutParams childlayoutparam = new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT, 100f);

        LinearLayout.LayoutParams qtparam = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 25f);
        LinearLayout.LayoutParams allocationaram = new LinearLayout.LayoutParams(0, LayoutParams.WRAP_CONTENT, 50f);

        if (mType.equalsIgnoreCase("ADD")) {
            mTextViewFrameText = new TextView(this);
            mTextViewFrameText.setGravity(Gravity.CENTER);
            mTextViewFrameText.setTextSize(13);
            mTextViewFrameText.setTextColor(Color.parseColor("#003399"));
            mTextViewFrameText.setText("Max Allocation\n(Ton)");
            mFrameLayout.addView(mTextViewFrameText);
        } else {
            LinearLayout employeenamelayout = new LinearLayout(this);
            employeenamelayout.setPadding(1, 1, 1, 1);
            employeenamelayout.setOrientation(LinearLayout.VERTICAL);
            employeenamelayout.setBackgroundColor(Color.parseColor("#FFECEE"));
            employeenamelayout.setLayoutParams(childlayoutparam);

            mTextViewFrameText = new TextView(this);
            mTextViewFrameText.setGravity(Gravity.CENTER);
            mTextViewFrameText.setTextSize(13);
            mTextViewFrameText.setTextColor(Color.parseColor("#003399"));
            mTextViewFrameText.setText(mCurrentEmployeeName);
            employeenamelayout.addView(mTextViewFrameText);

            mFrameLayout.addView(employeenamelayout);

            ///End of Header Name

            LinearLayout quantitylayout = new LinearLayout(this);
            quantitylayout.setPadding(1, 1, 1, 1);
            quantitylayout.setOrientation(LinearLayout.HORIZONTAL);
            quantitylayout.setLayoutParams(childlayoutparam);

            //Start of 1st part
            LinearLayout quantityallotedlayout = new LinearLayout(this);
            quantityallotedlayout.setOrientation(LinearLayout.VERTICAL);
            quantityallotedlayout.setBackgroundColor(Color.parseColor("#A2C7FF"));
            quantityallotedlayout.setLayoutParams(allocationaram);

            TextView textviewalloted = new TextView(this);
            textviewalloted.setText("Balance can be alloted");
            textviewalloted.setGravity(Gravity.CENTER);
            textviewalloted.setTextColor(Color.parseColor("#003399"));

            quantityallotedlayout.addView(textviewalloted);
            quantitylayout.addView(quantityallotedlayout);
            //End of 1st Part


            //Start of 2nd part
            LinearLayout quantitybookedbyteamlayout = new LinearLayout(this);
            quantitybookedbyteamlayout.setOrientation(LinearLayout.VERTICAL);
            quantitybookedbyteamlayout.setBackgroundColor(Color.parseColor("#94B6EA"));
            quantitybookedbyteamlayout.setLayoutParams(allocationaram);


            TextView textviewchildalloted = new TextView(this);
            textviewchildalloted.setText("Alloted to team");
            textviewchildalloted.setGravity(Gravity.CENTER);
            textviewchildalloted.setTextColor(Color.parseColor("#003399"));

            quantitybookedbyteamlayout.addView(textviewchildalloted);
            quantitylayout.addView(quantitybookedbyteamlayout);
            //End of 2nd Part

            mFrameLayout.addView(quantitylayout);

        }


        String productgroupname = "";
        String qtyinton = "";
        String allocationintonbyme = "";

        for (int count = 0; count < Constants.mSaudaAllocationList.size(); count++) {

            productgroupname = Constants.mSaudaAllocationList.get(count).getProductFilterName();
            qtyinton = Constants.mSaudaAllocationList.get(count).getQuantityinTon();

            if (mType.equalsIgnoreCase("EDIT")) {
                allocationintonbyme = Constants.mSaudaAllocationList.get(count).getAllotedQtyTonByMe();
            }


            LinearLayout childlayout = new LinearLayout(this);
            childlayout.setPadding(4, 2, 0, 2);
            childlayout.setOrientation(LinearLayout.HORIZONTAL);
            childlayout.setLayoutParams(childlayoutparam);
            //childlayout.setWeightSum(3);
            childlayout.setBackgroundColor(Color.parseColor("#DCE8F6"));


            TextView textviewdisplayname = new TextView(this);
            textviewdisplayname.setText(productgroupname);
            textviewdisplayname.setTextColor(Color.parseColor("#003399"));
            textviewdisplayname.setLayoutParams(qtparam);

            childlayout.addView(textviewdisplayname);

            EditText editText = new EditText(this);
            editText.setLayoutParams(qtparam);
            editText.setTag(String.valueOf(count));
            editText.setInputType(InputType.TYPE_CLASS_NUMBER
                    | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            editText.setImeOptions(EditorInfo.IME_ACTION_DONE);
            editText.setTypeface(null, Typeface.NORMAL);
            mEditTextList.add(editText);

            childlayout.addView(editText);

            //TextView textviewqty = new TextView(this);
            if (mType.equalsIgnoreCase("ADD")) {
                //textviewqty.setText(qtyinton+"  ");
            } else {
                //textviewqty.setText(qtyinton+"   +   "+allocationintonbyme);
            }

            if (mType.equalsIgnoreCase("ADD")) {
                TextView textviewqty = new TextView(this);
                textviewqty.setTextColor(Color.parseColor("#336600"));
                textviewqty.setText(qtyinton + "  ");
                textviewqty.setGravity(Gravity.CENTER);
                textviewqty.setLayoutParams(allocationaram);
                childlayout.addView(textviewqty);
            } else {

                LinearLayout quantitylayout = new LinearLayout(this);
                quantitylayout.setPadding(1, 1, 1, 1);
                quantitylayout.setOrientation(LinearLayout.HORIZONTAL);
                quantitylayout.setGravity(Gravity.CENTER_VERTICAL);
                quantitylayout.setLayoutParams(allocationaram);

                //Start of 1st part
                LinearLayout quantityallotedlayout = new LinearLayout(this);
                quantityallotedlayout.setOrientation(LinearLayout.VERTICAL);
                quantityallotedlayout.setLayoutParams(qtparam);

                TextView textviewalloted = new TextView(this);
                textviewalloted.setText(qtyinton);
                textviewalloted.setGravity(Gravity.CENTER);
                textviewalloted.setTextColor(Color.parseColor("#003399"));

                quantityallotedlayout.addView(textviewalloted);
                quantitylayout.addView(quantityallotedlayout);
                //End of 1st Part


                //Start of 2nd part
                LinearLayout quantitybookedbyteamlayout = new LinearLayout(this);
                quantitybookedbyteamlayout.setOrientation(LinearLayout.VERTICAL);
                quantitybookedbyteamlayout.setLayoutParams(qtparam);


                TextView textviewchildalloted = new TextView(this);
                textviewchildalloted.setText(allocationintonbyme);
                textviewchildalloted.setGravity(Gravity.CENTER);
                textviewchildalloted.setTextColor(Color.parseColor("#003399"));

                quantitybookedbyteamlayout.addView(textviewchildalloted);
                quantitylayout.addView(quantitybookedbyteamlayout);
                //End of 2nd Part

                childlayout.addView(quantitylayout);

            }


            mParentLayout.addView(childlayout);
        }
    }

    public void SetData(int position, String value, String type) {
        if (position < Constants.mSaudaAllocationList.size()) {
            double allotedqtybyme = 0;
            double maxallocationinton = Double.parseDouble(Constants.mSaudaAllocationList.get(position).getQuantityinTon());
            double conversionfactortwo = Double.parseDouble(Constants.mSaudaAllocationList.get(position).getConversionFactorTwo());
            double quantityinltr = 0;
            if (value.length() > 0) {
                if (isBoss == false) {
                    if (mType.equalsIgnoreCase("EDIT")) {
                        allotedqtybyme = Double.parseDouble(Constants.mSaudaAllocationList.get(position).getAllotedQtyTonByMe());
                        if ((maxallocationinton + allotedqtybyme) >= Double.parseDouble(value)) {
                            if (Double.parseDouble(value) >= allotedqtybyme) {
                                quantityinltr = conversionfactortwo * Double.parseDouble(value);
                                Constants.mSaudaAllocationList.get(position).setQty(value);
                                Constants.mSaudaAllocationList.get(position).setAllotedQuantityinLtr(String.valueOf(quantityinltr));
                                Constants.mSaudaAllocationList.get(position).setValidation(mQuantityOK);

                            } else {
                                Constants.mSaudaAllocationList.get(position).setValidation(mEditableQuantityLOW);
                            }
                        } else {
                            Constants.mSaudaAllocationList.get(position).setValidation(mQuantityEXCEED);
                        }
                    } else {
                        if (maxallocationinton >= Double.parseDouble(value)) {
                            quantityinltr = conversionfactortwo * Double.parseDouble(value);
                            Constants.mSaudaAllocationList.get(position).setQty(value);
                            Constants.mSaudaAllocationList.get(position).setAllotedQuantityinLtr(String.valueOf(quantityinltr));
                            Constants.mSaudaAllocationList.get(position).setValidation(mQuantityOK);
                        } else {
                            Constants.mSaudaAllocationList.get(position).setValidation(mQuantityEXCEED);
                        }
                    }
                } else {
                    if (mType.equalsIgnoreCase("EDIT")) {
                        allotedqtybyme = Double.parseDouble(Constants.mSaudaAllocationList.get(position).getAllotedQtyTonByMe());
                        if (Double.parseDouble(value) >= allotedqtybyme) {
                            quantityinltr = conversionfactortwo * Double.parseDouble(value);
                            Constants.mSaudaAllocationList.get(position).setQty(value);
                            Constants.mSaudaAllocationList.get(position).setAllotedQuantityinLtr(String.valueOf(quantityinltr));
                            Constants.mSaudaAllocationList.get(position).setValidation(mQuantityOK);
                        } else {
                            Constants.mSaudaAllocationList.get(position).setValidation(mEditableQuantityLOW);
                        }

                    } else {
                        quantityinltr = conversionfactortwo * Double.parseDouble(value);
                        Constants.mSaudaAllocationList.get(position).setQty(value);
                        Constants.mSaudaAllocationList.get(position).setAllotedQuantityinLtr(String.valueOf(quantityinltr));
                        Constants.mSaudaAllocationList.get(position).setValidation(mQuantityOK);
                    }
                }
            } else {
                Constants.mSaudaAllocationList.get(position).setValidation(mQuantityOK);
                Constants.mSaudaAllocationList.get(position).setQty("0");
            }
        }
    }

}
