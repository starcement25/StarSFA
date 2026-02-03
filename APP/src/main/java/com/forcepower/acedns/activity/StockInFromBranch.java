package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.text.InputFilter;
import android.text.InputType;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.adapter.GITAdapter;
import com.forcepower.acedns.adapter.ProductMasterStockinAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.bean.GITDetails;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DecimalDigitsInputFilter;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class StockInFromBranch extends FragmentActivity implements OnClickListener, OnItemClickListener {

    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    ArrayList<GITDetails> gitList;
    Dialog gitDialog;
    ArrayList<ProductMasterDetails> productMasterList;
    ProductMasterStockinAdapter stockInAdapter;
    ProgressDialog loader;
    Handler mHandler;
    String gitNo = "";
    String despatcherCode = "";

    ImageView headerLogo;
    ListView productView;
    Button btnOrder, btnCustomer, btnBack;
    String remarks = "";
    CaldroidListener listener;
    Button multipleRemarksButton;
    SimpleDateFormat dateFormat;
    boolean isShortageMenu = false;
    String remarksDate = "";
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_stockin_branch);
        RegisterActivities.registerActivity(this);

        mContext = StockInFromBranch.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);

        if (getIntent().getBooleanExtra("isShortageMenu", false)) {
            isShortageMenu = true;
        }
        initView();

        dateFormat = new SimpleDateFormat("dd-MM-yyyy");
        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                try {
                    String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
                    Date despatchDate = Utils.stringToDate(gitNo.substring(gitNo.length() - 14, gitNo.length() - 6), "yyyyMMdd");
                    if (!date.after(currentDate)) {
//							if(!despatchDate.before(date))
                        if (despatchDate.after(date)) {
                            Utils.showToast(mContext, "Selected date cannot be older than despatch date.");
                        } else {
                            dialogCaldroidFragment.dismiss();
                            multipleRemarksButton.setText(dateFormat.format(date));
                            remarksDate = new SimpleDateFormat("yyyyMMdd").format(date);
                        }

                    } else {
                        Utils.showToast(mContext, "Future Dates cannot be selected.");
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {
                try {
                    String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                    Date currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
                    Date despatchDate = Utils.stringToDate(gitNo.substring(gitNo.length() - 14, gitNo.length() - 6), "yyyyMMdd");
                    if (!date.after(currentDate)) {
                        if (despatchDate.before(date)) {
                            Utils.showToast(mContext, "Selected date cannot be older than despatch date.");
                        } else {
                            dialogCaldroidFragment.dismiss();
                            multipleRemarksButton.setText(dateFormat.format(date));
                            remarksDate = new SimpleDateFormat("yyyyMMdd").format(date);
                        }
                    } else {
                        Utils.showToast(mContext, "Future Dates cannot be selected.");
                    }
                } catch (Exception e) {

                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };

        showGITDialog();

        mHandler = new Handler() {
            public void handleMessage(Message msg) {
                String aResponse = msg.getData().getString("message");
                if (aResponse.equalsIgnoreCase("SubmitJobDone")) {
                    loader.cancel();
                    StockInFromBranch.this.runOnUiThread(new Runnable() {
                        public void run() {
                            new TRANS_SubmitOrderTask(StockInFromBranch.this, true).execute();
                        }
                    });
                }
            }
        };
    }

    public void initView() {
        headerLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            headerLogo.setVisibility(View.VISIBLE);
            headerLogo.setImageBitmap(Constants.logoBmp);
        } else {
            headerLogo.setVisibility(View.GONE);
        }
        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        // txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        productView = (ListView) findViewById(R.id.list_prod);
        productView.setOnItemClickListener(StockInFromBranch.this);

        btnOrder = (Button) findViewById(R.id.btn_order_form);
        btnOrder.setOnClickListener(StockInFromBranch.this);

        btnBack = (Button) findViewById(R.id.back);
        btnBack.setOnClickListener(StockInFromBranch.this);

        btnCustomer = (Button) findViewById(R.id.select_customer);
        if (!Constants.orderFormDetailsObj.getBranchRDSTransfer().equalsIgnoreCase("yes")) {
            btnCustomer.setText(Constants.selectedBranch.getBranchName());
        }
        btnCustomer.setOnClickListener(StockInFromBranch.this);
    }

    public void onClick(View clkdView) {
        if (clkdView == btnOrder) {
            checkValuesEntered();
        }
        if (clkdView == btnBack) {
            finish();
        }
    }

    public void showGITDialog() {
        if (Constants.orderFormDetailsObj.getBranchRDSTransfer().equalsIgnoreCase("yes")) {
            gitList = setupDataHelperObj.getPendingGITDetailsList();
        } else {
            despatcherCode = Constants.selectedBranch.getBranchCode();
            gitList = setupDataHelperObj.getGITDetailsList(despatcherCode);
        }
        if (gitList.size() > 0) {
            GITAdapter adapterVertical = new GITAdapter(StockInFromBranch.this, R.layout.simple_list_child, gitList);
            gitDialog = new Dialog(StockInFromBranch.this, R.style.PauseDialog);
            gitDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            gitDialog.setContentView(R.layout.choose_customer_search);
            gitDialog.setCancelable(false);
            TextView title = (TextView) gitDialog.findViewById(R.id.title);
            title.setText("Please select a GRN No");
            EditText searchText = (EditText) gitDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.setVisibility(View.GONE);
            ListView dialogList = (ListView) gitDialog.findViewById(R.id.list);
            dialogList.setAdapter(adapterVertical);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    gitDialog.cancel();
                    gitNo = gitList.get(arg2).getGrnNo();
                    if (Constants.orderFormDetailsObj.getBranchRDSTransfer().equalsIgnoreCase("yes")) {
                        btnCustomer.setText(gitList.get(arg2).getDespatcherName());
                        despatcherCode = gitList.get(arg2).getDespatcherCode();
                        Constants.selectedRDS = setupDataHelperObj.getRDSDetails(despatcherCode);
                    }
                    Constants.selectedProductMasterList = setupDataHelperObj.getDispatchProdList(gitNo);
                    productMasterList = setupDataHelperObj.getDispatchProdList(gitNo);
                    stockInAdapter = new ProductMasterStockinAdapter(StockInFromBranch.this, R.layout.prod_stockin_child, productMasterList, true);
                    productView.setAdapter(stockInAdapter);
                }
            });
            Button addCustomer = (Button) gitDialog.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            gitDialog.show();
        } else {
            Utils.showToast(mContext, "No pending GIT found.");
            finish();
        }

    }


    public void checkValuesEntered() {
        boolean status = true;
        for (int ii = 0; ii < Constants.selectedProductMasterList.size(); ii++) {
            double stockInQty = Double.parseDouble(Constants.selectedProductMasterList.get(ii).getQty());
//	        	double availQty = Double.parseDouble(Constants.selectedProductMasterList.get(ii).getQtyRemaining());
//	        	if(stockInQty <= availQty)
//				{
//	        		status = true;
//	        		if(stockInQty == availQty)
//					{
//	        			Constants.selectedProductMasterList.get(ii).setStatusForStockIn("1");
//	        		}
//					else
//					{
//	        			Constants.selectedProductMasterList.get(ii).setStatusForStockIn("0");
//	        		}
//	        	}
//				else
//				{
//				status = false;
//	        	}
        }
        if (status) {
            if (Constants.orderFormDetailsObj.getInstruction().equalsIgnoreCase("yes")) {
                showInstructionDialog();
            } else {
                showMultiRemarksDialog();
            }
        } else {
            Utils.showToast(mContext, "Please provide valid StockIn Qty.");
        }
    }


    public void saveStockData(final String remarks) {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Saving Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                String transType = "";
                if (isShortageMenu) {
                    transType = "SA";
                } else {
                    transType = "BT";
                }
                String timeStamp = "";
                if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes") && remarksDate.length() != 0) {
                    timeStamp = Constants.dateString + remarksDate + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                } else {
                    timeStamp = Constants.dateString + Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                }
                new GPSTracker(mContext);
                transDataHelperObj.insertToOrderHeaderTableForStockin("O", remarks, timeStamp, "", "", "", "", transType, "");
                transDataHelperObj.insertToOrderDetailsTable(timeStamp);
                transDataHelperObj.insertToLocationTable("O", timeStamp);
                transDataHelperObj.insertToGITMasterFromTransaction(gitNo, despatcherCode, timeStamp, transType);
                transDataHelperObj.updateStatusInGIT(gitNo);
                transDataHelperObj.insertToTransactionLogTable(timeStamp, transType, remarks);
                if (!isShortageMenu) {
                    transDataHelperObj.increaseClStk();
                }
                Message msgObj = mHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", "SubmitJobDone");
                msgObj.setData(b);
                mHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void showInstructionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        instructionDialog.setCancelable(false);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
        edInst.setText(remarks);
        final Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                btnOrder.setEnabled(false);
                submit.setEnabled(false);
                remarks = edInst.getText().toString();
                saveStockData(remarks);
                instructionDialog.cancel();
            }
        });
        instructionDialog.show();
    }

    public void showMultiRemarksDialog() {
        final Dialog multiRemarksDialog = new Dialog(mContext);
        multiRemarksDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        multiRemarksDialog.setContentView(R.layout.multi_instruction_dialog);
        multiRemarksDialog.setCancelable(false);
        TextView title = (TextView) multiRemarksDialog.findViewById(R.id.title);
        title.setText("Remarks if any ?");
        final EditText edInst1 = (EditText) multiRemarksDialog.findViewById(R.id.ed_input_r1);
        multipleRemarksButton = (Button) multiRemarksDialog.findViewById(R.id.ed_input_r2);
        final EditText edInst3 = (EditText) multiRemarksDialog.findViewById(R.id.ed_input_r3);
        edInst3.setFilters(new InputFilter[]{new DecimalDigitsInputFilter(16, 2)});
        String[] remarksVal = remarks.split(";");
        if (remarksVal.length > 2) {
            edInst1.setText(remarksVal[0]);
            multipleRemarksButton.setText(remarksVal[1]);
            edInst3.setText(remarksVal[2]);
        }
        Button submit = (Button) multiRemarksDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String instruction = "", billTotal;
                billTotal = edInst3.getText().toString();
                if (billTotal.length() > 0 && Double.parseDouble(billTotal) > 0) {
                    instruction = edInst1.getText().toString() + ";" + multipleRemarksButton.getText().toString() + ";" + billTotal;
                    remarks = instruction;
                    multiRemarksDialog.cancel();
                    saveStockData(remarks);
                } else {
                    Utils.showToast(mContext, "Enter Non Zero Amount");
                }
            }
        });
        multipleRemarksButton.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                chooseDateDialog();
            }
        });
        multiRemarksDialog.show();
    }

    public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
        //Utils.showToast(mContext, "Hello");
        showEditQuantityDialog(arg2);
    }


    public void showEditQuantityDialog(final int position) {
        final Dialog edQtyDialog = new Dialog(StockInFromBranch.this);
        edQtyDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        edQtyDialog.setContentView(R.layout.edit_qty_stockin);
        TextView title = (TextView) edQtyDialog.findViewById(R.id.title);
        TextView header = (TextView) edQtyDialog.findViewById(R.id.txt_header);
        title.setText("Provide valid inputs");
        if (isShortageMenu) {
            header.setText("Sortage Quantity               : ");
        } else {
            header.setText("Stock In Quantity               : ");
        }
        final EditText edDespatchQty = (EditText) edQtyDialog.findViewById(R.id.ed_despatch_qty);
        final EditText edStockinQty = (EditText) edQtyDialog.findViewById(R.id.ed_stockin_qty);

        edDespatchQty.setText(Constants.selectedProductMasterList.get(position).getDespatchQtyForStockIn());
        edStockinQty.setText(Constants.selectedProductMasterList.get(position).getQty());

        edDespatchQty.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
        edStockinQty.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);

        Button submit = (Button) edQtyDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String stockinQty = "";
                stockinQty = edStockinQty.getText().toString();
                String despatchQtyForStockIn = Constants.selectedProductMasterList.get(position).getDespatchQtyForStockIn();
                if (Utils.isNumeric(despatchQtyForStockIn) && Utils.isNumeric(stockinQty)) {
                    if (Double.parseDouble(stockinQty) <= Double.parseDouble(despatchQtyForStockIn)) {
                        Double qtyRemain = Double.parseDouble(despatchQtyForStockIn) - Double.parseDouble(stockinQty);
                        Constants.selectedProductMasterList.get(position).setQtyRemaining(String.valueOf(qtyRemain));
                        productMasterList.get(position).setQtyRemaining(String.valueOf(qtyRemain));
                        productMasterList.get(position).setQty(stockinQty);
                        Constants.selectedProductMasterList.get(position).setQty(stockinQty);
                        if (Double.parseDouble(stockinQty) == Double.parseDouble(Constants.selectedProductMasterList.get(position).getQty())) {
                            productMasterList.get(position).setStatusForStockIn("1");
                            Constants.selectedProductMasterList.get(position).setStatusForStockIn("1");
                        } else {
                            productMasterList.get(position).setStatusForStockIn("0");
                            Constants.selectedProductMasterList.get(position).setStatusForStockIn("0");
                        }
                        edQtyDialog.cancel();
                        final InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
                        edStockinQty.postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                edStockinQty.requestFocus();
                                imm.hideSoftInputFromWindow(edStockinQty.getWindowToken(), 0);
                            }
                        }, 100);
                        stockInAdapter.notifyDataSetChanged();
                    } else {
                        Toast.makeText(StockInFromBranch.this, "Please provide a valid input", Toast.LENGTH_LONG).show();
                    }
                } else {
                    Toast.makeText(StockInFromBranch.this, "Please provide a valid input", Toast.LENGTH_LONG).show();
                }
            }
        });
        edQtyDialog.show();
    }

    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }
}
