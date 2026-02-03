package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BranchAdapter;
import com.forcepower.acedns.adapter.BrokerAdapter;
import com.forcepower.acedns.adapter.IncotermsAdapter;
import com.forcepower.acedns.adapter.NewCustomerAdapter;
import com.forcepower.acedns.adapter.SaudaRouteAdapter;
import com.forcepower.acedns.bean.BranchMasterDetails;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.OutstandingDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.bean.RouteDetails;
import com.forcepower.acedns.bean.SaudaFormDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

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
import java.util.Arrays;
import java.util.Objects;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.text.HtmlCompat;

import static com.forcepower.acedns.constants.Constants.BrokerageCost;
import static com.forcepower.acedns.constants.Constants.dateString;
import static com.forcepower.acedns.constants.Constants.defaultFormat3;
import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;
import static com.forcepower.acedns.constants.Constants.directOrBroker;
import static com.forcepower.acedns.constants.Constants.isSecondaryFreightIncluded;
import static com.forcepower.acedns.constants.Constants.mChosenUomType;
import static com.forcepower.acedns.constants.Constants.mDepotOrPlant;
import static com.forcepower.acedns.constants.Constants.mSaudaDepoCode;
import static com.forcepower.acedns.constants.Constants.selectedProductMasterList;
import static com.forcepower.acedns.constants.Constants.selectedSSOfCustomer;
import static com.forcepower.acedns.constants.Constants.selectedState;
import static com.forcepower.acedns.util.Utils.toTitleCase;

public class ActivityBargainFilter extends AppCompatActivity {
    @SuppressLint("StaticFieldLeak")
    public static EditText ed_po = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewInvoiceNo = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewInvoice = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewBargainNumber = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView bargainLimitTv = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewBrokerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewTitleDirectorBroker = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewRouteName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewCustomerName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewLoadabilityTons = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewDespatchOriginValue = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView textViewChosenVertical = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewDepotName = null;
    @SuppressLint("StaticFieldLeak")
    public static TextView mTextViewRateType = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout poNoLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout routeCustomerLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout saudaBookedThroughLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout despatchOriginSelectLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout despatchOriginValueLayout = null;
    @SuppressLint("StaticFieldLeak")
    public static LinearLayout layoutverticalSpinner = null;
    @SuppressLint("StaticFieldLeak")
    public static RadioGroup mRadioGroupSBT = null;
    @SuppressLint("StaticFieldLeak")
    public static RadioGroup mRadioGroupRBO = null;
    @SuppressLint("StaticFieldLeak")
    public static RadioGroup radioGrpDespatchOrigin = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonBack = null;
    @SuppressLint("StaticFieldLeak")
    public static ImageView mImageViewHeaderLogo = null;
    public static String mLoadabilityTon = "";
    public TextView textViewLoadability = null;
    public TextView textViewTransportMode = null;
    public String mSaudaBookedType = "";
    public String mSaudaDespatchOrigin = "";
    public String mCustomerName = "";
    public String mSaudaType = "";
    public String mSaudaDepoName = "";
    public String verticalOfUser = "";
    public boolean isBrokerDataTaken = false;
    public boolean isCustomerChosen = false;
    public boolean isDepoSelected = false;
    public boolean isFreightRateSelected = true;
    public AceDnsDatabase mAceDnsDatabase;
    public ProgressDialog mProgressDialogPrepareSaudaData;
    public Handler mHandlerPrepareSaudaData;
    public boolean shouldCheckSaudaDespatchOrigin = false;
    public boolean isSaudaDespatchOriginSelected = true;
    int spinnerFlag = 0;
    RouteDetails mRouteDetails;
    ArrayList<CustomerDetails> mCustomerDetailsList;
    Context mContext;
    public static String selectedFreightRate = "", selectedRouteCode = "";

    ArrayList<BranchMasterDetails> saudaRDSList = new ArrayList<>();
    Spinner spinner;
    double maxAlloc = 0.0;
    public static String verticalValueOfEmployee = "";
    Spinner customerSpinner;

    @SuppressLint({"HandlerLeak", "SetTextI18n"})
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_bargain_filter);
        RegisterActivities.registerActivity(this);
        InitializeView();
        ClearData();
        Toolbar myToolbar = findViewById(R.id.my_toolbar);
        setSupportActionBar(myToolbar);
        Objects.requireNonNull(getSupportActionBar()).setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setDisplayShowHomeEnabled(true);
        getSupportActionBar().setDisplayShowTitleEnabled(false);
        mContext = ActivityBargainFilter.this;
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        Constants.mBrokerMasterList = new ArrayList<>();
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        GetSaudaSetupMasterData();
        verticalValueOfEmployee = mAceDnsDatabase.getVerticalValueOfLoggedInEmployee();
        LinearLayout ExForSelectionLayout = findViewById(R.id.ExForSelectionLayout);
        ExForSelectionLayout.setVisibility(View.GONE);

        mRadioGroupSBT.setOnCheckedChangeListener((group, checkedId) -> {
            ClearData();
            RadioButton radioSelection = findViewById(checkedId);
            if (radioSelection.getText().equals("Broker")) {
                mSaudaBookedType = "Broker";
                Constants.mBrokerMasterList = mAceDnsDatabase.GetBrokerMaster();
                ShowBrokerMaster();
            } else {
                mSaudaBookedType = "Direct";
                mTextViewBrokerName.setText(mSaudaBookedType);
                BrokerageCost = "0.00";
                ShowRoute();
            }
        });

        mRadioGroupRBO.setOnCheckedChangeListener((group, checkedId) -> {
            if (isBrokerDataTaken) {
                RadioButton radioSelection = findViewById(checkedId);
                if (radioSelection.getText().toString().equalsIgnoreCase("EX")) {
                    mSaudaType = "EX";
                    ShowSaudaDepoNameDialog();
                } else {
                    mSaudaType = "FOR";
                    ShowSaudaDepoNameDialog();
                }
            }
        });

        radioGrpDespatchOrigin.setOnCheckedChangeListener((group, checkedId) -> {
            despatchOriginValueLayout.setVisibility(View.VISIBLE);
            RadioButton radioSelection = findViewById(checkedId);
            mSaudaDespatchOrigin = radioSelection.getText().toString();
            textViewDespatchOriginValue.setText("Selected Despatch Origin: " + mSaudaDespatchOrigin);
            isSaudaDespatchOriginSelected = true;
        });

        mHandlerPrepareSaudaData = new Handler() {
            public void handleMessage(@NonNull Message msg) {
                mProgressDialogPrepareSaudaData.dismiss();
                final int jobToDo = msg.getData().getInt("JOB");
                ActivityBargainFilter.this.runOnUiThread(() -> {
                    if (jobToDo == 1) {
                        initializeSaudaBookTypeData();
                        if (verticalValueOfEmployee.contains(",")) {
                            layoutverticalSpinner.setVisibility(View.VISIBLE);
                            String[] verticalArray = verticalValueOfEmployee.split(",");
                            spinner = findViewById(R.id.spinner);
                            ArrayAdapter<String> dataAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, verticalArray);
                            // Drop down layout style - list view with radio button
                            dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                            // attaching data RoutePlanAdapter to spinner
                            spinner.setAdapter(dataAdapter);
                            showDespatchOriginOption(verticalArray[0]);
                            spinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                                @Override
                                public void onItemSelected(AdapterView<?> adapterView, View view, int position, long l) {
                                    if (spinnerFlag > 0) {
                                        String selectedCatagory = adapterView.getItemAtPosition(position).toString();
                                        showDespatchOriginOption(selectedCatagory);
                                        ShowRoute();

                                    } else {
                                        spinnerFlag++;
                                    }
                                }

                                @Override
                                public void onNothingSelected(AdapterView<?> adapterView) {
                                }
                            });
                        } else {
                            layoutverticalSpinner.setVisibility(View.GONE);
                            showDespatchOriginOption(verticalValueOfEmployee);
                        }
                    }
                });
            }
        };


        mButtonSubmit.setOnClickListener(v -> {
            if (isCustomerChosen) {
                if ((mSaudaType.matches("EX") || mSaudaType.matches("FOR")) && isBrokerDataTaken && isDepoSelected && isSaudaDespatchOriginSelected) {
                    if (mSaudaType.matches("FOR") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(verticalValueOfEmployee)) {
                        if (isFreightRateSelected) {
                            getVauesAndGotoNextPage();
                        } else {
                            if (!Constants.employeeDetailObject.getEmpCode().equalsIgnoreCase("E0042")) {
                                Utils.showToast(mContext, "Error: Please contact admin.");
                            } else {
                                Toast.makeText(mContext, "Did not get proper freight rate, please contact admin.", Toast.LENGTH_LONG).show();
                            }
                        }
                    } else {
                        getVauesAndGotoNextPage();
                    }
                } else if (!isSaudaDespatchOriginSelected) {
                    Toast.makeText(mContext, "Please Select Despatch Origin ", Toast.LENGTH_LONG).show();
                } else if (isBrokerDataTaken && !isDepoSelected) {
                    Toast.makeText(mContext, R.string.bargain_branch_route_freight_data_missing_error, Toast.LENGTH_LONG).show();
                } else {
                    Utils.showToast(mContext, "Improper Bargain data. Please contact admin.");
                }
            } else {
                Utils.showToast(mContext, "Please Choose a customer to proceed.");
            }
        });

        mButtonBack.setOnClickListener(v -> finish());

        try {
            PrepareCustomerData(1);
        } catch (Exception ignored) {
        }
    }

    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }

    public void showPendingBargain(View v) {
        if (isCustomerChosen) {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                new TRANS_Pending_Bqargain_Asynctask(getApplicationContext()).execute("");
            } else {
                Utils.showToast(mContext, "Could not fetch latest data. Please check your internet connection.");
            }
        } else {
            Utils.showToast(mContext, "Please Choose a customer to proceed.");
        }
    }

    @SuppressLint("SetTextI18n")
    private void showPendingQtyDialog(ArrayList<ProductMasterDetails> nameValues) {
        final Dialog incotermsSelectionDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        incotermsSelectionDialog.setContentView(R.layout.pending_bargain_list);
        incotermsSelectionDialog.setCancelable(false);
        Button btn_cncl = incotermsSelectionDialog.findViewById(R.id.btn_cncl);
        btn_cncl.setOnClickListener(view -> incotermsSelectionDialog.dismiss());
        TextView title = incotermsSelectionDialog.findViewById(R.id.title);
        TextView totalQtyTV = incotermsSelectionDialog.findViewById(R.id.totalQtyTV);
        TextView pendingBargainTotalRateTv = incotermsSelectionDialog.findViewById(R.id.pendingBargainTotalRateTv);
        title.setText("Pending Bargain List");
        ListView dialogList = incotermsSelectionDialog.findViewById(R.id.list);
        PendingBargaindapter PendingBargaindapterObject = new PendingBargaindapter(mContext, R.layout.pending_bargain_list_item, nameValues);
        dialogList.setAdapter(PendingBargaindapterObject);
        int totalQtyCase = 0;
        double totalQtyMt = 0.0, pendingBargainTotalAmount = 0.0;
        for (int i = 0; i < nameValues.size(); i++) {
            String qty = nameValues.get(i).getQty();
            int qtyCase = 0;
            double qtyMt;
            if (qty.contains("/")) {
                String[] qtyCaseMt = qty.split("/");
                qtyCase = Integer.parseInt(qtyCaseMt[0]);
                qtyMt = Double.parseDouble(qtyCaseMt[1]);
            } else {
                qtyMt = Double.parseDouble(qty);
            }

            double pendingBargainAmount = qtyCase * Double.parseDouble(nameValues.get(i).getMrpValue());
            if (!qty.contains("/")) {
                pendingBargainAmount = qtyMt * Double.parseDouble(nameValues.get(i).getMrpValue());
            }
            totalQtyCase = totalQtyCase + qtyCase;
            totalQtyMt = totalQtyMt + qtyMt;
            pendingBargainTotalAmount = pendingBargainTotalAmount + pendingBargainAmount;
        }

        pendingBargainTotalRateTv.setText("Approx Val: " + defaultFormatWithComma.format(pendingBargainTotalAmount));
        if (totalQtyCase > 0) {
            totalQtyTV.setText(totalQtyCase + "/" + Constants.defaultFormat.format(totalQtyMt) + "(Case/MT)");
        } else {
            totalQtyTV.setText(Constants.defaultFormat.format(totalQtyMt) + "(MT)");
        }
        incotermsSelectionDialog.show();
    }

    @SuppressLint("StaticFieldLeak")
    public class TRANS_Pending_Bqargain_Asynctask extends AsyncTask<String, Void, String> {
        ProgressDialog mStepProgressDialog;
        int noRows = -1, noColumn = -1;
        String timeStamp = "";

        public TRANS_Pending_Bqargain_Asynctask(Context mContext) {
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(mContext);
            mStepProgressDialog.setMessage("Loading Data.\nPlease wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            if (HTTPUtils.isConnectionPossible(mContext)) {
                try {
                    String url = BaseUrl.baseUrl + AceDnsWebServiceURL.downloadPendingBargainUrl
                            + "?nick_name=" + Constants.nickName
                            + "&customer_code=" + Constants.selectedCustomer.getCustomerCode();
                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ ActivityBargainFilter: " + url);
                    downloader(url);

                    File csvFile = new File(Utils.getAppStoragePath(mContext) + "pending_bargain_list.txt");

                    FileReader file = null;
                    try {
                        file = new FileReader(csvFile);
                    } catch (FileNotFoundException ignored) {
                    }
                    selectedProductMasterList = new ArrayList<>();
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
                                    ProductMasterDetails temp = new ProductMasterDetails();
                                    temp.setmfgDate(RowData[0]);
                                    temp.setDesc(RowData[1]);
                                    temp.setQty(RowData[2]);
                                    temp.setMrpValue(RowData[3]);
                                    selectedProductMasterList.add(temp);
                                }
                            }
                        }
                        buffer.close();
                    } catch (IOException ignored) {
                    }
                } catch (Exception e) {
                    POST_result = "Network Failure";
                }
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);
            mStepProgressDialog.dismiss();
            if (!selectedProductMasterList.isEmpty()) {
                showPendingQtyDialog(selectedProductMasterList);
            } else {
                Utils.showToast(mContext, "No data found for chose customer.");
            }
        }
    }

    private void downloader(String urlstr) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile;
        InputStream is = null;
        URL url;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + "pending_bargain_list.txt");
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
            int len1;
            while ((len1 = is.read(buffer)) != -1) {
                fbo.write(buffer, 0, len1);
                Log.e("length", len1 + "----");
            }
            fbo.flush();

        } catch (Exception ignored) {
        } finally {
            if (c != null)
                c.disconnect();
            if (fbo != null)
                try {
                    fbo.close();
                } catch (IOException ignored) {
                }
            if (is != null)
                try {
                    is.close();
                } catch (IOException ignored) {
                }
        }
    }

    @SuppressLint("SetTextI18n")
    public void OutstandingAmount() {
        ArrayList<OutstandingDetails> outstandingList = mAceDnsDatabase.getOutstandingListForCustomer(Constants.selectedCustomer.getCustomerCode());
        double invoiceamount = 0;
        double dueamount = 0;
        if (outstandingList != null && !outstandingList.isEmpty()) {
            for (int ii = 0; ii < outstandingList.size(); ii++) {
                if (!outstandingList.get(ii).getInvoice_amount().isEmpty()) {
                    invoiceamount = invoiceamount + Double.parseDouble(outstandingList.get(ii).getInvoice_amount());
                }
                if (!outstandingList.get(ii).getDue_amount().isEmpty()) {
                    dueamount = dueamount + Double.parseDouble(outstandingList.get(ii).getDue_amount());
                }
            }
        }
        if (outstandingList != null) {
            textViewInvoiceNo.setText(outstandingList.size() + "");
        } else {
            textViewInvoiceNo.setText(String.valueOf(0));
        }
        textViewInvoice.setText(Constants.defaultFormat.format(invoiceamount));
        textViewInvoice.setOnClickListener(view -> {
            if (Utils.isNumeric(textViewInvoice.getText().toString()) && Double.parseDouble(textViewInvoice.getText().toString()) > 0) {
                showOutStandingDetails(outstandingList);
            }
        });
        textViewInvoiceNo.setOnClickListener(view -> {
            if (Utils.isNumeric(textViewInvoiceNo.getText().toString()) && Double.parseDouble(textViewInvoiceNo.getText().toString()) > 0) {
                showOutStandingDetails(outstandingList);
            }
        });
    }

    @SuppressLint("SetTextI18n")
    private void showOutStandingDetails(ArrayList<OutstandingDetails> outstandingList) {
        final Dialog incotermsSelectionDialog = new Dialog(mContext, R.style.MyMaterialTheme);
        incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        incotermsSelectionDialog.setContentView(R.layout.outstanding_details_list_material);
        incotermsSelectionDialog.setCancelable(false);
        LinearLayout totalLayout = incotermsSelectionDialog.findViewById(R.id.totalLayout);
        totalLayout.setVisibility(View.GONE);
        Button btn_cncl = incotermsSelectionDialog.findViewById(R.id.btn_cncl);
        btn_cncl.setOnClickListener(view -> incotermsSelectionDialog.dismiss());
        TextView invamountTv = incotermsSelectionDialog.findViewById(R.id.invamountTv);
        invamountTv.setText("Inv Amnt");
        TextView dueamountTv = incotermsSelectionDialog.findViewById(R.id.dueamountTv);
        dueamountTv.setText("Due Amnt");
        TextView title = incotermsSelectionDialog.findViewById(R.id.title);
        title.setText(HtmlCompat.fromHtml("Outstanding Details: <font color='#D7B56D'>" + Constants.selectedCustomer.getCustomerName() + "</font>", HtmlCompat.FROM_HTML_MODE_LEGACY));
        ListView dialogList = incotermsSelectionDialog.findViewById(R.id.list);
        OutstandingDetailsadpter PendingBargaindapterObject = new OutstandingDetailsadpter(mContext, R.layout.outstanding_details_list_item_material, outstandingList);
        dialogList.setAdapter(PendingBargaindapterObject);
        incotermsSelectionDialog.show();
    }

    public void PrepareCustomerData(final int task) {
        mProgressDialogPrepareSaudaData = new ProgressDialog(mContext);
        mProgressDialogPrepareSaudaData.setCancelable(false);
        mProgressDialogPrepareSaudaData.setMessage("Downloading Data.\nPlease wait..");
        mProgressDialogPrepareSaudaData.show();
        new Thread() {
            public void run() {
                if (task == 1) {
                    new commonAsyncTaskMaster(mContext, "customer_master");
                    if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("secondary_freight")) {
                        new commonAsyncTaskMaster(mContext, "branch_route_freight");
                        new commonAsyncTaskMaster(mContext, "load_distribution");
                    }

                    if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("honeycomb_cost")) {
                        new commonAsyncTaskMaster(mContext, "honeycomb_cost");
                    }
                    if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("margin_cost")) {
                        new commonAsyncTaskMaster(mContext, "margin_cost");
                    }
                    if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("depot_cost")) {
                        new commonAsyncTaskMaster(mContext, "depot_cost");
                    }
                    if (Constants.saudaFormDetailsObj.getincoterms_price_components().contains("primary_freight")) {
                        new commonAsyncTaskMaster(mContext, "primary_freight");
                    }
                    new commonAsyncTaskMaster(mContext, "bargain_mrp");
                    new commonAsyncTaskMaster(mContext, "product_master");
                }

                Message msg = mHandlerPrepareSaudaData.obtainMessage();
                Bundle bundle = new Bundle();
                bundle.putInt("JOB", task);
                msg.setData(bundle);
                mHandlerPrepareSaudaData.sendMessage(msg);
            }
        }.start();
    }

    @SuppressLint("SetTextI18n")
    private void showDespatchOriginOption(String verticalOfUser) {
        this.verticalOfUser = verticalOfUser;
        Constants.selectedVerticalOfUser = verticalOfUser;
        if (Constants.saudaFormDetailsObj.getsauda_rate_dependent_on_despatch_point()
                .equalsIgnoreCase("yes") && Constants.saudaFormDetailsObj.getsauda_rate_dependent_on_despatch_point_verticlewise()
                .equalsIgnoreCase("yes") && verticalOfUser.contains(Constants.saudaFormDetailsObj.getsauda_rate_dependent_on_despatch_point_verticle_val())) {
            textViewChosenVertical.setVisibility(View.VISIBLE);
            textViewChosenVertical.setText("Vertical: " + verticalOfUser);
            despatchOriginSelectLayout.setVisibility(View.VISIBLE);
            radioGrpDespatchOrigin.setEnabled(true);
            shouldCheckSaudaDespatchOrigin = true;
            isSaudaDespatchOriginSelected = false;
        } else {
            textViewChosenVertical.setVisibility(View.GONE);
            despatchOriginSelectLayout.setVisibility(View.GONE);
            shouldCheckSaudaDespatchOrigin = false;
            isSaudaDespatchOriginSelected = true;
        }
    }

    private void initializeSaudaBookTypeData() {
        saudaBookedThroughLayout.setVisibility(View.GONE);
        mSaudaBookedType = "Direct";
        mTextViewBrokerName.setText(mSaudaBookedType);
        mTextViewBrokerName.setVisibility(View.GONE);
        ShowRoute();
    }

    private void getVauesAndGotoNextPage() {
        if (maxAlloc > 0) {
            Constants.marginPoNo = ed_po.getText().toString();
            mCustomerName = Constants.selectedCustomer.getCustomerName();
            Intent intent = new Intent(mContext, BargainActivity.class);
            intent.putExtra("CUSTOMER", mCustomerName);
            intent.putExtra("SAUDABOOKEDTYPE", mSaudaBookedType);
            intent.putExtra("SAUDATYPE", mSaudaType);
            intent.putExtra("DEPODETAILS", mSaudaDepoName);
            intent.putExtra("FREIGHTRATE", selectedFreightRate);
            intent.putExtra("shouldCheckSaudaDespatchOrigin", shouldCheckSaudaDespatchOrigin);
            intent.putExtra("mSaudaDespatchOrigin", mSaudaDespatchOrigin);
            intent.putExtra("mSaudaDespatchOrigin", mSaudaDespatchOrigin);
            intent.putExtra("selectedVerticalOfUser", verticalValueOfEmployee);
            intent.putExtra("selectedRouteName", Constants.selectedCustomer.getRouteName());
            intent.putExtra("setmaxAlloc", maxAlloc + "");
            startActivity(intent);
        } else {
            Toast.makeText(mContext, "Insufficient allocation, Please update your Bargain limit.", Toast.LENGTH_SHORT).show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void InitializeView() {
        ed_po = findViewById(R.id.ed_po);
        textViewInvoice = findViewById(R.id.textViewInvoice);
        textViewInvoiceNo = findViewById(R.id.textViewInvoiceNo);
        textViewBargainNumber = findViewById(R.id.textViewBargainNumber);
        bargainLimitTv = findViewById(R.id.bargainLimitTv);
        mTextViewBrokerName = findViewById(R.id.textViewBrokerOrDirectValue);
        textViewTitleDirectorBroker = findViewById(R.id.textViewTitleDirectorBroker);
        mTextViewRouteName = findViewById(R.id.textViewRouteValue);
        mTextViewCustomerName = findViewById(R.id.textViewCustomerValue);
        textViewLoadabilityTons = findViewById(R.id.textViewLoadabilityTons);
        textViewDespatchOriginValue = findViewById(R.id.textViewDespatchOriginValue);
        textViewChosenVertical = findViewById(R.id.textViewChosenVertical);
        textViewLoadability = findViewById(R.id.textViewLoadability);
        textViewTransportMode = findViewById(R.id.textViewTransportMode);
        mTextViewDepotName = findViewById(R.id.textViewDepotValue);
        mTextViewRateType = findViewById(R.id.textViewRateTypeValue);
        saudaBookedThroughLayout = findViewById(R.id.saudaBookedThroughLayout);
        poNoLayout = findViewById(R.id.poNoLayout);
        routeCustomerLayout = findViewById(R.id.routeCustomerLayout);
        despatchOriginSelectLayout = findViewById(R.id.despatchOriginLayout);
        despatchOriginValueLayout = findViewById(R.id.despatchOriginValueLayout);
        layoutverticalSpinner = findViewById(R.id.layoutverticalSpinner);
        mRadioGroupSBT =  findViewById(R.id.radioSelectSBT);
        mRadioGroupRBO =  findViewById(R.id.radioSelectRBO);
        radioGrpDespatchOrigin =  findViewById(R.id.radioGrpDespatchOrigin);
        mRadioGroupSBT.setEnabled(false);
        mRadioGroupRBO.setEnabled(false);
        mButtonSubmit =  findViewById(R.id.btn_Submi);
        mButtonBack =  findViewById(R.id.back);
        mImageViewHeaderLogo =  findViewById(R.id.imagelogo);
        TextView txtVersion = findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        textViewTitleDirectorBroker.setText("Bargain Booked Through");
        bargainLimitTv.setText("Bargain Limit (MT)    ");
    }

    public void ClearData() {
        mTextViewBrokerName.setText("");
        mTextViewRouteName.setText("");
        mTextViewCustomerName.setText("");
        mTextViewDepotName.setText("");
        mTextViewRateType.setText("");
        isBrokerDataTaken = false;
        isDepoSelected = false;
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

    public void ShowRoute() {
        mCustomerDetailsList = mAceDnsDatabase.getCustomerListByRouteForBargain();
        customerSpinner = findViewById(R.id.customerSpinner);
        final ArrayList<String> spinnerArray = new ArrayList<>();
        for (int i = 0; i < mCustomerDetailsList.size(); i++) {
            spinnerArray.add(mCustomerDetailsList.get(i).getCustomerName());
        }
        final ArrayAdapter<String> spinnerArrayAdapter = new ArrayAdapter<>(mContext, android.R.layout.simple_spinner_item, spinnerArray);
        spinnerArrayAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        customerSpinner.setAdapter(spinnerArrayAdapter);
        if (!mCustomerDetailsList.isEmpty())
            customerSpinner.setSelection(0);

        customerSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @SuppressLint("SetTextI18n")
            @Override
            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                if (i == 0) {
                    isCustomerChosen = false;
                } else {
                    selectedSSOfCustomer = null;
                    Constants.selectedCustomer = mCustomerDetailsList.get(i);
                    if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("D") || Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SS")) {
                        poNoLayout.setVisibility(View.GONE);
                    } else {
                        poNoLayout.setVisibility(View.VISIBLE);
                    }
                    if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("D") && Constants.selectedCustomer.getRdsTag().trim().length() > 2) {
                        selectedSSOfCustomer = mAceDnsDatabase.getCustomerDetailsByCode(Constants.selectedCustomer.getRdsTag().trim());
                    }
                    selectedRouteCode = Constants.selectedCustomer.getRouteCode();
                    textViewBargainNumber.setText("Destination                : " + Constants.selectedCustomer.getRouteName());
                    textViewTransportMode.setText("Transport Mode                    : " + toTitleCase(Constants.selectedCustomer.getTransportMode()));
                    textViewLoadability.setText("Load Size                               : " + toTitleCase(Constants.selectedCustomer.getLoadabilityTon()) + " MT");

                    OutstandingAmount();
                    isBrokerDataTaken = true;
                    setMaxLiquidationDiscount();
                    exForSelectionProcess();
                    calculateMaxSaudaLimitPendingQty();
                    directOrBroker = "direct";
                    if (mAceDnsDatabase.isBrokerMapped(Constants.selectedCustomer.getCustomerCode())) {
                        String loginType = mAceDnsDatabase.getLoggedInEmpType();
                        if (loginType.equalsIgnoreCase("employee")) {
                            showDirectOrBrokerChooseDialog();
                        } else if (loginType.equalsIgnoreCase("customer")) {
                            if (mAceDnsDatabase.isBrokerMappedAndYesForCustomerLogin(Constants.selectedCustomer.getCustomerCode())) {
                                showDirectOrBrokerChooseDialog();
                            } else {
                                directOrBroker = "broker";
                            }
                        } else {
                            directOrBroker = "broker";
                        }
                    }
                    isCustomerChosen = true;
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> adapterView) { }
        });

    }

    @SuppressLint("SetTextI18n")
    private void showDirectOrBrokerChooseDialog() {
        ArrayList<String> nameValues = new ArrayList<>();
        nameValues.add("Direct");
        nameValues.add("Broker");
        final Dialog incotermsSelectionDialog = new Dialog(mContext,
                R.style.MyMaterialTheme);
        incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        incotermsSelectionDialog.setContentView(R.layout.select_from_list_material);
        incotermsSelectionDialog.setCancelable(false);

        Button btn_cncl = incotermsSelectionDialog.findViewById(R.id.btn_cncl);
        btn_cncl.setVisibility(View.GONE);

        TextView title = incotermsSelectionDialog.findViewById(R.id.title);
        title.setText("Please select Bargain Type");
        ListView dialogList = incotermsSelectionDialog.findViewById(R.id.list);
        IncotermsAdapter bargaindapter = new IncotermsAdapter(mContext, R.layout.list_item_single_radio, nameValues);
        dialogList.setAdapter(bargaindapter);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            incotermsSelectionDialog.cancel();
            directOrBroker = nameValues.get(arg2).toLowerCase();
            incotermsSelectionDialog.cancel();
        });
        incotermsSelectionDialog.show();
    }

    @SuppressLint("SetTextI18n")
    public void ShowBrokerMaster() {
        if (!Constants.mBrokerMasterList.isEmpty()) {
            final Dialog mSaudaBrokerDialog = new Dialog(mContext, R.style.PauseDialog);
            mSaudaBrokerDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mSaudaBrokerDialog.setContentView(R.layout.select_with_search);
            mSaudaBrokerDialog.setCancelable(false);
            TextView title = mSaudaBrokerDialog.findViewById(R.id.title);
            title.setText("Please select a Broker");
            ListView dialogList =  mSaudaBrokerDialog.findViewById(R.id.list);
            final BrokerAdapter brokeradapter = new BrokerAdapter(mContext, R.layout.customer_broker_list_child, Constants.mBrokerMasterList);
            dialogList.setAdapter(brokeradapter);
            EditText searchText =  mSaudaBrokerDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    brokeradapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mSaudaBrokerDialog.cancel();
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                Constants.selectedBroker = brokeradapter.getItem(arg2);
                mTextViewBrokerName.setText("Broker: " + (Constants.selectedBroker != null ? Constants.selectedBroker.getBrokerName() : null));
                ShowRoute();
            });

            Button cancel =  mSaudaBrokerDialog.findViewById(R.id.btn_ok);
            cancel.setVisibility(View.INVISIBLE);
            mSaudaBrokerDialog.show();
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowRouteListDialog(final ArrayList<RouteDetails> routeList) {
        final Dialog mDialogRoute = new Dialog(mContext, R.style.PauseDialog);
        mDialogRoute.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogRoute.setContentView(R.layout.select_with_search);
        mDialogRoute.setCancelable(false);
        TextView title = mDialogRoute.findViewById(R.id.title);
        title.setText("Please select a Route");
        ListView dialogList = mDialogRoute.findViewById(R.id.list);
        final SaudaRouteAdapter adapter = new SaudaRouteAdapter(mContext,
                R.layout.route_list_child, routeList);
        dialogList.setAdapter(adapter);

        EditText searchText = mDialogRoute.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapter.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {
            }
        });


        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogRoute.cancel();
            mRouteDetails = adapter.getItem(arg2);
            assert mRouteDetails != null;
            mTextViewRouteName.setText("Route: " + mRouteDetails.getRouteName());
            selectedRouteCode = mRouteDetails.getRouteCode();
            if (mCustomerDetailsList.size() > 1) {
                ShowCustomerListDialog();
            } else {
                if (mCustomerDetailsList.size() == 1) {
                    mTextViewCustomerName.setText("Customer : " + mCustomerDetailsList.get(0).getCustomerName());
                    Constants.selectedCustomer = mCustomerDetailsList.get(0);
                    if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("D") || Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SS")) {
                        ed_po.setVisibility(View.GONE);
                    } else {
                        ed_po.setVisibility(View.VISIBLE);
                    }
                    routeCustomerLayout.setVisibility(View.VISIBLE);
                    isBrokerDataTaken = true;
                    setMaxLiquidationDiscount();

                    exForSelectionProcess();

                    if (!Constants.saudaFormDetailsObj.getsauda_allocation_app_vertical().contains(verticalValueOfEmployee)) {
                        calculateMaxSaudaLimitPendingQty();
                    } else {
                        maxAlloc = 0.0;
                        for (int i = 0; i < Constants.selectedAlocatedSaudaList.size(); i++) {
                            maxAlloc = maxAlloc + Double.parseDouble(Constants.selectedAlocatedSaudaList.get(i).getBalance());
                        }
                    }
                } else {
                    Toast.makeText(mContext, "No existing customer found.", Toast.LENGTH_LONG).show();
                }
            }
        });

        Button cancel =  mDialogRoute.findViewById(R.id.btn_ok);
        cancel.setVisibility(View.INVISIBLE);
        mDialogRoute.show();
    }

    @SuppressLint("SetTextI18n")
    private void exForSelectionProcess() {
        String incoTermsOfCurentCustomer = Constants.selectedCustomer.getIncoTerms().trim();
        mLoadabilityTon = Constants.selectedCustomer.getLoadabilityTon().trim();
        textViewLoadabilityTons.setText("  Transport Mode : " + Constants.selectedCustomer.getTransportMode().trim());
        textViewLoadabilityTons.setVisibility(View.VISIBLE);
        if (incoTermsOfCurentCustomer.contains(";")) {
            ArrayList<String> incotermsArrayMultiple = new ArrayList<>(Arrays.asList(incoTermsOfCurentCustomer.split(";")));
            ShowIncotermsSelectionDialog(incotermsArrayMultiple);
        } else {
            incotermsSplitProcess(incoTermsOfCurentCustomer);
        }
    }

    private void incotermsSplitProcess(String incoTermsOfCurentCustomer) {
        Constants.incoTermsOfCurentCustomer = incoTermsOfCurentCustomer;
        Constants.isPrimaryFreightIncluded = false;
        isSecondaryFreightIncluded = false;
        Constants.isDepotCostIncluded = false;
        Constants.isMarginCostIncluded = false;
        String incotermsPriceComponenet = Constants.saudaFormDetailsObj.getincoterms_price_components();
        if (incotermsPriceComponenet.contains("#")) {
            String[] splittedPriceComp = incotermsPriceComponenet.split("#");
            for (String s : splittedPriceComp) {
                String currentComp = s.toLowerCase();
                if (currentComp.contains(incoTermsOfCurentCustomer.toLowerCase())) {
                    if (currentComp.contains("primary_freight")) {
                        Constants.isPrimaryFreightIncluded = true;
                    }
                    if (currentComp.contains("secondary_freight")) {
                        isSecondaryFreightIncluded = true;
                    }
                    if (currentComp.contains("depot_cost")) {
                        Constants.isDepotCostIncluded = true;
                    }
                    if (currentComp.contains("margin_cost")) {
                        Constants.isMarginCostIncluded = true;
                    }
                }
            }
        }
        if (incoTermsOfCurentCustomer.toLowerCase().contains("for depot") || incoTermsOfCurentCustomer.toLowerCase().contains("for plant")
                || incoTermsOfCurentCustomer.toLowerCase().contains("ex depot") || incoTermsOfCurentCustomer.toLowerCase().contains("ex plant")) {
            if (incoTermsOfCurentCustomer.contains(" ")) {
                String[] inctermsArray = incoTermsOfCurentCustomer.split(" ");
                if (inctermsArray.length == 2) {
                    mSaudaType = inctermsArray[0].toUpperCase();
                    mDepotOrPlant = inctermsArray[1];
                    ShowSaudaDepoNameDialog();
                } else {
                    Utils.showToast(mContext, "Improper incoterms data found for selected customer. Please contact admin.");
                }
            }
        } else {
            Utils.showToast(mContext, "Improper incoterms data found for selected customer. Please contact admin.");
        }
    }

    //if current customer's liquidation discount is valid or higher than 0 then allow user to input
    private void setMaxLiquidationDiscount() {
        Constants.maxLiquidationDiscountForCurrentCustomer = Constants.selectedCustomer.getTradeDiscount();
    }

    @SuppressLint("SetTextI18n")
    public void ShowIncotermsSelectionDialog(final ArrayList<String> incotermsArrayMultiple) {

        if (incotermsArrayMultiple.size() > 1) {
            final Dialog incotermsSelectionDialog = new Dialog(mContext, R.style.MyMaterialTheme);
            incotermsSelectionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            incotermsSelectionDialog.setContentView(R.layout.select_from_list_material);
            incotermsSelectionDialog.setCancelable(false);

            Button btn_cncl =  incotermsSelectionDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setOnClickListener(view -> incotermsSelectionDialog.dismiss());
            TextView title = incotermsSelectionDialog.findViewById(R.id.title);
            title.setText("Please select incoterms");
            ListView dialogList = incotermsSelectionDialog.findViewById(R.id.list);
            IncotermsAdapter incotermsadapter = new IncotermsAdapter(mContext, R.layout.list_item_single_radio, incotermsArrayMultiple);
            dialogList.setAdapter(incotermsadapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                incotermsSelectionDialog.cancel();
                selectedState = incotermsArrayMultiple.get(arg2);
                incotermsSplitProcess(incotermsArrayMultiple.get(arg2));
            });
            incotermsSelectionDialog.show();
        } else {
            if (incotermsArrayMultiple.size() == 1) {
                incotermsSplitProcess(incotermsArrayMultiple.get(0));
            } else {
                Toast.makeText(mContext, "Improper incoterms data.", Toast.LENGTH_SHORT).show();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowCustomerListDialog() {
        final NewCustomerAdapter adapterCust = new NewCustomerAdapter(mContext, R.layout.customer_list_child, mCustomerDetailsList);

        final Dialog mDialogCustomer = new Dialog(mContext, R.style.PauseDialog);
        mDialogCustomer.requestWindowFeature(Window.FEATURE_NO_TITLE);
        mDialogCustomer.setContentView(R.layout.choose_customer_search);
        mDialogCustomer.setCancelable(false);
        TextView title = mDialogCustomer.findViewById(R.id.title);
        title.setText("Please select a Customer");
        EditText searchText = mDialogCustomer.findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                adapterCust.getFilter().filter(s.toString());
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {}

            @Override
            public void afterTextChanged(Editable s) {}
        });

        ListView dialogList = mDialogCustomer.findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
            Constants.isSelectCustomer = true;
            getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            mDialogCustomer.cancel();
            Constants.selectedCustomer = adapterCust.getItem(arg2);
            if (Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("D") || Constants.selectedCustomer.getCustomerType().equalsIgnoreCase("SS")) {
                ed_po.setVisibility(View.GONE);
            } else {
                ed_po.setVisibility(View.VISIBLE);
            }
            mTextViewCustomerName.setText("Customer : " + Constants.selectedCustomer.getCustomerName());
            routeCustomerLayout.setVisibility(View.VISIBLE);
            isBrokerDataTaken = true;
            setMaxLiquidationDiscount();
            exForSelectionProcess();
            maxAllocatiopnCalculationProcess();
        });

        Button addCustomer = mDialogCustomer.findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        mDialogCustomer.show();
    }

    private void maxAllocatiopnCalculationProcess() {
        if (!Constants.saudaFormDetailsObj.getsauda_allocation_app_vertical().contains(verticalValueOfEmployee)) {
            calculateMaxSaudaLimitPendingQty();
        } else {
            maxAlloc = 0.0;
            for (int i = 0; i < Constants.selectedAlocatedSaudaList.size(); i++) {
                maxAlloc = maxAlloc + Double.parseDouble(Constants.selectedAlocatedSaudaList.get(i).getBalance());
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void ShowSaudaDepoNameDialog() {

        String plantDepotFilter = "";
        if (mDepotOrPlant.equalsIgnoreCase("plant")) {
            plantDepotFilter = " LOWER(is_plant)='yes' AND ";
        } else if (mDepotOrPlant.equalsIgnoreCase("depot")) {
            plantDepotFilter = " LOWER(is_plant)='no' AND";
        }
        mTextViewRateType.setText("Rate                                         : " + mSaudaType + " - " + mDepotOrPlant);
        mTextViewDepotName.setText(mDepotOrPlant + " Name                         : ");
        if (mSaudaType.matches("FOR")) {
            if (Constants.userDetailsObj.getVerticalFields().equalsIgnoreCase("yes") && Constants.saudaFormDetailsObj.getSecondaryFreightVertical().contains(Constants.selectedVerticalOfUser)) {
                saudaRDSList = mAceDnsDatabase.getSaudaRDSListWithRouteCode(Constants.selectedCustomer.getCustomerCode(), selectedRouteCode, plantDepotFilter, Constants.selectedCustomer.getTransportMode(), Constants.selectedCustomer.getLoadabilityTon());
            } else {
                saudaRDSList = mAceDnsDatabase.getSaudaRDSListWithRouteCode(Constants.selectedCustomer.getCustomerCode(), selectedRouteCode, plantDepotFilter, Constants.selectedCustomer.getTransportMode(), Constants.selectedCustomer.getLoadabilityTon());
            }
        } else {
            saudaRDSList = mAceDnsDatabase.getSaudaRDSList(Constants.selectedCustomer.getCustomerCode(), plantDepotFilter);
        }

        if (saudaRDSList.size() > 1) {
            final Dialog mDialogDepotName = new Dialog(mContext);
            mDialogDepotName.requestWindowFeature(Window.FEATURE_NO_TITLE);
            mDialogDepotName.setContentView(R.layout.select_from_list_material);
            mDialogDepotName.setCancelable(false);

            TextView title = mDialogDepotName.findViewById(R.id.title);
            title.setText("Please select a " + mDepotOrPlant);
            ListView dialogList = mDialogDepotName.findViewById(R.id.list);

            BranchAdapter branchadapter = new BranchAdapter(mContext, R.layout.customer_list_child_do_single_item, saudaRDSList);
            dialogList.setAdapter(branchadapter);
            dialogList.setOnItemClickListener((arg0, arg1, arg2, arg3) -> {
                mDialogDepotName.cancel();
                Constants.selectedBranch = saudaRDSList.get(arg2);
                mSaudaDepoName = saudaRDSList.get(arg2).getBranchName();
                mSaudaDepoCode = saudaRDSList.get(arg2).getBranchCode();

                mTextViewDepotName.setText(mDepotOrPlant + " Name               : " + mSaudaDepoName);

                mTextViewRateType.setText("Rate                            : " + mSaudaType + " - " + mDepotOrPlant);
                isDepoSelected = true;
            });

            Button cancel =  mDialogDepotName.findViewById(R.id.btn_cncl);
            cancel.setVisibility(View.INVISIBLE);
            mDialogDepotName.show();
        } else {
            if (saudaRDSList.size() == 1) {
                Constants.selectedBranch = saudaRDSList.get(0);
                mSaudaDepoName = saudaRDSList.get(0).getBranchName();
                mSaudaDepoCode = saudaRDSList.get(0).getBranchCode();
                mTextViewRateType.setText("Rate                                         : " + mSaudaType + " - " + mDepotOrPlant);
                mTextViewDepotName.setText(mDepotOrPlant + " Name                         : " + mSaudaDepoName);
                isDepoSelected = true;
            } else {
                Toast.makeText(mContext, R.string.bargain_branch_route_freight_data_missing_error, Toast.LENGTH_LONG).show();
            }
        }
    }


    private void calculateMaxSaudaLimitPendingQty() {
        double saudaLimit = 0.0, pendingQty = 0.0;
        String SelectedCustomerCode = Constants.selectedCustomer.getCustomerCode();
        String SaudaLimitForSelectedCustomerCode = Constants.selectedCustomer.getSaudaLimit();
        String PendingQuantityForSelectedCustomerCode = mAceDnsDatabase.getPendingQuantityOfCustomer(SelectedCustomerCode);

        if (Utils.isNumeric(SaudaLimitForSelectedCustomerCode)) {
            saudaLimit = Double.parseDouble(SaudaLimitForSelectedCustomerCode);
        }
        if (Utils.isNumeric(PendingQuantityForSelectedCustomerCode)) {
            pendingQty = Double.parseDouble(PendingQuantityForSelectedCustomerCode);
        }
        maxAlloc = saudaLimit - pendingQty;

        set3Data(saudaLimit, pendingQty, maxAlloc);
    }

    public void GetSaudaSetupMasterData() {
        try {
            Constants.saudaFormDetailsObj = new SaudaFormDetails();
            Constants.selectedAlocatedSaudaList = new ArrayList<>();
            Constants.saudaFormDetailsObj = mAceDnsDatabase.GETSaudaFormDetails();
            Constants.selectedAlocatedSaudaList = mAceDnsDatabase.GETSaudaAllocation();
        } catch (Exception ignored) {}
    }

    @SuppressLint("SetTextI18n")
    public void set3Data(double saudaLimit, double pendingQty, double maxAlloc) {
        TextView textViewCalculatedSaudaLimit = findViewById(R.id.textViewCalculatedSaudaLimit);
        TextView textViewCalculatedPendingQty = findViewById(R.id.textViewCalculatedPendingQty);
        TextView textViewCalculateedAllocation = findViewById(R.id.textViewCalculateedAllocation);

        if (saudaLimit != 0.0) {
            textViewCalculatedSaudaLimit.setText(defaultFormat3.format(saudaLimit));
        } else
            textViewCalculatedSaudaLimit.setText("0.000");

        ImageView pendingbargainQtyIV = findViewById(R.id.pendingbargainQtyIV);
        if (pendingQty != 0.0) {
            textViewCalculatedPendingQty.setText(defaultFormat3.format(pendingQty));
            pendingbargainQtyIV.setVisibility(View.VISIBLE);
        } else {
            textViewCalculatedPendingQty.setText("0.000");
            pendingbargainQtyIV.setVisibility(View.GONE);
        }

        if (maxAlloc != 0.0) {
            textViewCalculateedAllocation.setText(defaultFormat3.format(maxAlloc));
        } else
            textViewCalculateedAllocation.setText("0.000");
    }

    public static class PendingBargaindapter extends ArrayAdapter<ProductMasterDetails> {
        private final Context context;
        private final int resourceId;
        Activity activity;
        AceDnsDatabase mAceDnsDatabase;
        int sizeOfList ;

        public ArrayList<ProductMasterDetails> nameValuesProductListLocalDo;
        String currentUom;

        public PendingBargaindapter(Context context, int resourceId, ArrayList<ProductMasterDetails> nameValues) {
            super(context, resourceId, nameValues);
            this.context = context;
            activity = (Activity) context;
            nameValuesProductListLocalDo = nameValues;
            this.resourceId = resourceId;
            mAceDnsDatabase = new AceDnsDatabase(context);
            sizeOfList = nameValues.size();
            currentUom = mChosenUomType;
            if (currentUom.equalsIgnoreCase("loose")) {
                currentUom = "MT";
            }
        }

        @NonNull
        @SuppressLint("ViewHolder")
        @Override
        public View getView(final int position, View convertView, @NonNull ViewGroup parent) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            ViewHolder viewHolder = new ViewHolder();
            viewHolder.txtViewProductDesc = convertView.findViewById(R.id.list_details);
            viewHolder.rateTV = convertView.findViewById(R.id.rateTV);
            viewHolder.dateTV = convertView.findViewById(R.id.dateTV);
            viewHolder.qtyTV = convertView.findViewById(R.id.qtyTV);

            viewHolder.invisibleTVProdCode = convertView.findViewById(R.id.invisibleTVProdCode);
            convertView.setTag(viewHolder);

            String desc = nameValuesProductListLocalDo.get(position).getDesc();
            String date = nameValuesProductListLocalDo.get(position).getmfgDate();
            date = Utils.changeDateFormat("dd-MM-yyyy", "dd-MM-yy", date);
            viewHolder.dateTV.setText(date);
            viewHolder.txtViewProductDesc.setText(desc);
            String qty = nameValuesProductListLocalDo.get(position).getQty();
            viewHolder.qtyTV.setText(qty);
            viewHolder.rateTV.setText(nameValuesProductListLocalDo.get(position).getMrpValue());
            return convertView;
        }

        public static class ViewHolder {
            TextView txtViewProductDesc, invisibleTVProdCode, dateTV, qtyTV, rateTV;
        }

    }

    public static class OutstandingDetailsadpter extends ArrayAdapter<OutstandingDetails> {

        private final Context context;
        private final int resourceId;
        Activity activity;
        AceDnsDatabase mAceDnsDatabase;
        int sizeOfList ;

        public ArrayList<OutstandingDetails> nameValuesProductListLocalDo;

        public OutstandingDetailsadpter(Context context, int resourceId, ArrayList<OutstandingDetails> nameValues) {
            super(context, resourceId, nameValues);
            this.context = context;
            activity = (Activity) context;
            nameValuesProductListLocalDo = nameValues;
            this.resourceId = resourceId;
            mAceDnsDatabase = new AceDnsDatabase(context);
            sizeOfList = nameValues.size();
        }

        @NonNull
        @SuppressLint({"SetTextI18n", "ViewHolder"})
        @Override
        public View getView(final int position, View convertView, @NonNull ViewGroup parent) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            ViewHolder viewHolder = new ViewHolder();
            viewHolder.txtViewProductDesc = convertView.findViewById(R.id.list_details);
            viewHolder.daysTV = convertView.findViewById(R.id.daysTV);
            viewHolder.rateTV = convertView.findViewById(R.id.rateTV);
            viewHolder.dateTV = convertView.findViewById(R.id.dateTV);
            viewHolder.qtyTV = convertView.findViewById(R.id.qtyTV);

            viewHolder.invisibleTVProdCode = convertView.findViewById(R.id.invisibleTVProdCode);
            convertView.setTag(viewHolder);
            String date = nameValuesProductListLocalDo.get(position).getDate();
            date = Utils.changeDateFormat("yyyy-MM-dd", "dd-MM-yy", date);
            viewHolder.dateTV.setText(date);
            int slNo = position + 1;
            long days = Utils.DaybetweenDates(date, dateString, "dd-MM-yy", "yyyyMMdd");
            viewHolder.txtViewProductDesc.setText(slNo + "");
            viewHolder.daysTV.setText(days + "");
            viewHolder.qtyTV.setText(defaultFormatWithComma.format(Double.parseDouble(nameValuesProductListLocalDo.get(position).getInvoice_amount())));
            viewHolder.rateTV.setText(defaultFormatWithComma.format(Double.parseDouble(nameValuesProductListLocalDo.get(position).getDue_amount())));
            return convertView;
        }

        public static class ViewHolder {
            TextView txtViewProductDesc, invisibleTVProdCode, dateTV, qtyTV, rateTV, daysTV;
        }

    }
}
