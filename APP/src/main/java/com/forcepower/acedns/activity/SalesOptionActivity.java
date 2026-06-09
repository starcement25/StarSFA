package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.annotation.TargetApi;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.nsd.NsdManager;
import android.net.nsd.NsdServiceInfo;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.SpannableStringBuilder;
import android.text.TextWatcher;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnLongClickListener;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Filterable;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.MASTER_LoadGITMasterData;
import com.forcepower.acedns.backgroundTask.TRANS_DeleteTransactionTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.GITAdapter;
import com.forcepower.acedns.adapter.InvoiceInformationAdapter;
import com.forcepower.acedns.adapter.ProductMasterStockinAdapter;
import com.forcepower.acedns.adapter.SimpleStringAdapter;
import com.forcepower.acedns.bean.CustomerDetails;
import com.forcepower.acedns.bean.GITDetails;
import com.forcepower.acedns.bean.InvoiceInformation;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.DateTimeFormatter;
import com.forcepower.acedns.util.EnglishNumberToWords;
import com.forcepower.acedns.util.PrintTextFormatter;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.PrintWriter;
import java.net.InetAddress;
import java.net.Socket;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class SalesOptionActivity extends FragmentActivity implements OnClickListener, OnLongClickListener {

    //        public static final String SERVICE_TYPE = "_http._tcp.";
    public static final String SERVICE_TYPE = "_ipp._tcp.";
    LinearLayout stockinLayout, stockoutLayout, carryinLayout,
            returnLayout, orderLayout, deleteLayout, bodyLayout, reprintLayout;
    ArrayList<GITDetails> gitList;
    Dialog gitDialog, gitDetailsDialog;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    Button pending, cancel;
    TextView txtVersion;
    ImageView imgLogo;
    ProgressDialog loader;
    AceDnsTransactionDatabase transDataHelperObj;
    ArrayList<String> misOptionList;
    Handler misHandler;
    String selectedBranchCode = "", selectedRDSCode = "", selectedBranchCode1 = "", currentDateandTime = "", selectedInvoiceNumber, currentInvoiceCustomerName = "", currentInvoiceCustomerPin = "", currentInvoiceCustomerAddress = "", chequeNo = "", bankName = "", saleType = "", freightCharge = "0";
    Button btnDetails, btnStartDate, btnEndDate;
    CaldroidListener listener;
    boolean startSelect, endSelect;
    InvoiceInformationAdapter adapterCust;
    PrintTextFormatter PrintTextFormatterObject;
    DecimalFormat df = new DecimalFormat("#.00");
    double grandTotal = 0;
    double totalPriceWithoutVat = 0.00;
    SpannableStringBuilder finalPrintStringSalesBill, finalPrintStringSalesBillVatString, finalPrintStringMoneyReceipt, finalPrintStringFreightBill;
    ArrayList<ProductMasterDetails> selectedProductMasterList;
    NsdManager.DiscoveryListener mDiscoveryListener;
    NsdManager mNsdManager;
    NsdManager.ResolveListener mResolveListener;
    ArrayList<Double> vatRateList;
    ArrayList<Double> TotalAmountForEachVatRate;
    ArrayList<InvoiceInformation> InvoiceInformationList;
    ArrayList<InvoiceInformation> InvoiceInformationListSearchResult;
    Boolean shouldPrintFreightBill = false;
    double freightServiceTaxPercentage = 14.00, freightSwachhBharatPercent = .50;
    private CaldroidFragment dialogCaldroidFragment;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sales_landing);
        RegisterActivities.registerActivity(this);
        finalPrintStringSalesBill = new SpannableStringBuilder();
        finalPrintStringMoneyReceipt = new SpannableStringBuilder();
        finalPrintStringSalesBillVatString = new SpannableStringBuilder();
        vatRateList = new ArrayList<>();
        TotalAmountForEachVatRate = new ArrayList<>();
        InvoiceInformationList = new ArrayList<>();
        InvoiceInformationListSearchResult = new ArrayList<>(InvoiceInformationList);
        mContext = SalesOptionActivity.this;
        setupDataHelperObj = new AceDnsDatabase(mContext);
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        PrintTextFormatterObject = new PrintTextFormatter(mContext);
        initView();

        misHandler = new Handler() {
            public void handleMessage(Message msg) {
                final String aResponse = msg.getData().getString("message");
                loader.cancel();
                SalesOptionActivity.this.runOnUiThread(new Runnable() {
                    public void run() {
                        showOptionForMISReport(aResponse);
                    }
                });
            }
        };

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                try {
                    if (date.after(new SimpleDateFormat("dd-MM-yyyy").parse(new SimpleDateFormat("dd-MM-yyyy").format(new Date())))) {
                        Utils.showToast(mContext, "Future Dates cannot be selected");
                    } else {
                        dialogCaldroidFragment.dismiss();
                        if (startSelect) {
                            btnStartDate.setText(new SimpleDateFormat("yyyy-MM-dd").format(date));
                        } else {
                            btnEndDate.setText(new SimpleDateFormat("yyyy-MM-dd").format(date));
                        }
                    }
                } catch (Exception e) {
                    System.out.println(e);
                }
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {
                try {
                    if (date.after(new SimpleDateFormat("dd-MM-yyyy").parse(new SimpleDateFormat("dd-MM-yyyy").format(new Date())))) {
                        Utils.showToast(mContext, "Future Dates cannot be selected");
                    } else {
                        dialogCaldroidFragment.dismiss();
                        if (startSelect) {
                            btnStartDate.setText(new SimpleDateFormat("yyyy-MM-dd").format(date));
                        } else {
                            btnEndDate.setText(new SimpleDateFormat("yyyy-MM-dd").format(date));
                        }
                    }
                } catch (Exception e) {
                    System.out.println(e);
                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };


        if (getIntent().getBooleanExtra("DELETE TRANSACTION", false)) {
            bodyLayout.setVisibility(View.INVISIBLE);
            pending.setVisibility(View.INVISIBLE);
            showDeleteOptions();
        }
    }

    public void initView() {
        txtVersion = (TextView) findViewById(R.id.txt_version);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        imgLogo = (ImageView) findViewById(R.id.imagelogo);

        pending = (Button) findViewById(R.id.btn_pending);
        cancel = (Button) findViewById(R.id.back);
        stockinLayout = (LinearLayout) findViewById(R.id.stockin_layout);
        stockoutLayout = (LinearLayout) findViewById(R.id.stockout_layout);
        carryinLayout = (LinearLayout) findViewById(R.id.carryin_layout);
        returnLayout = (LinearLayout) findViewById(R.id.return_layout);
        orderLayout = (LinearLayout) findViewById(R.id.order_layout);
        deleteLayout = (LinearLayout) findViewById(R.id.delete_layout);
        bodyLayout = (LinearLayout) findViewById(R.id.body_layout);

        pending.setOnClickListener(this);
        cancel.setOnClickListener(this);
        stockinLayout.setOnClickListener(this);
        stockoutLayout.setOnClickListener(this);
        carryinLayout.setOnClickListener(this);
        returnLayout.setOnClickListener(this);
        orderLayout.setOnClickListener(this);
        pending.setOnLongClickListener(this);
        deleteLayout.setOnClickListener(this);

        if (Constants.orderFormDetailsObj.getAttachedPrinter().equalsIgnoreCase("yes")
                && Constants.orderFormDetailsObj.getPrinter_mandetory().equalsIgnoreCase("yes") && Constants.orderFormDetailsObj.getPrintMedium().equalsIgnoreCase("wlan")) {
            reprintLayout = (LinearLayout) findViewById(R.id.reprint_layout);
            reprintLayout.setVisibility(View.VISIBLE);
            reprintLayout.setOnClickListener(this);

        }

        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }
        txtVersion.setText("Ver~" + Utils.getAppVersion(SalesOptionActivity.this));
        if (Constants.employeeDetailObject.getSaleAccess().equalsIgnoreCase("secondary")) {
            stockinLayout.setVisibility(View.GONE);
            stockoutLayout.setVisibility(View.GONE);
        }
    }

    public void showGITDialog() {
        gitList = setupDataHelperObj.getPendingGITDetailsList();
        if (gitList.size() > 0) {
            GITAdapter adapterVertical = new GITAdapter(SalesOptionActivity.this, R.layout.simple_list_child, gitList);
            gitDialog = new Dialog(SalesOptionActivity.this, R.style.PauseDialog);
            gitDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            gitDialog.setContentView(R.layout.choose_customer_search);
            gitDialog.setCancelable(false);
            TextView title = (TextView) gitDialog.findViewById(R.id.title);
            title.setText("Please select a GNR Id");
            EditText searchText = (EditText) gitDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.setVisibility(View.GONE);
            ListView dialogList = (ListView) gitDialog.findViewById(R.id.list);
            dialogList.setAdapter(adapterVertical);
            dialogList.setOnItemClickListener(new OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
                    gitDialog.cancel();
                    String gitNo = gitList.get(arg2).getGrnNo();
                    showGITDetailsDialog(gitNo);
                }
            });
            Button addCustomer = (Button) gitDialog.findViewById(R.id.btn_add);
            addCustomer.setVisibility(View.GONE);
            gitDialog.show();
        } else {
            Utils.showToast(mContext, "No pending Goods In Transit");
        }
    }

    public void showGITDetailsDialog(String grnNo) {
        ArrayList<ProductMasterDetails> productMasterList = setupDataHelperObj.getDispatchProdList(grnNo);
        if (productMasterList.size() > 0) {
            ProductMasterStockinAdapter stockInAdapter = new ProductMasterStockinAdapter(SalesOptionActivity.this, R.layout.prod_stockin_child, productMasterList, false);
            gitDetailsDialog = new Dialog(SalesOptionActivity.this, R.style.PauseDialog);
            gitDetailsDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            gitDetailsDialog.setContentView(R.layout.choose_customer_search);
            gitDetailsDialog.setCancelable(false);
            TextView title = (TextView) gitDetailsDialog.findViewById(R.id.title);
            title.setText("GoodsInTransit Details");
            EditText searchText = (EditText) gitDetailsDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.setVisibility(View.GONE);
            ListView dialogList = (ListView) gitDetailsDialog.findViewById(R.id.list);
            dialogList.setAdapter(stockInAdapter);
            Button addCustomer = (Button) gitDetailsDialog.findViewById(R.id.btn_add);
            addCustomer.setText("     Ok     ");
            addCustomer.setOnClickListener(new OnClickListener() {
                @Override
                public void onClick(View v) {
                    gitDetailsDialog.cancel();
                }
            });
            gitDetailsDialog.show();
        } else {
            Utils.showToast(mContext, "No Product left for this GRN.");
        }
    }


    public void onClick(View clkdView) {
        if (clkdView == pending) {
            showGITDialog();
        } else if (clkdView == cancel) {
            finish();
        } else if (clkdView == stockinLayout) {
            startActivity(new Intent(SalesOptionActivity.this, StockInActivity.class));
        } else if (clkdView == stockoutLayout) {
            AceDnsDatabase setupDataHelperObj = new AceDnsDatabase(SalesOptionActivity.this);
            if (setupDataHelperObj.checkIfStockAvailableForDistribution()) {
                startActivity(new Intent(SalesOptionActivity.this, StockOutActivity.class));
            } else {
                Utils.showToast(SalesOptionActivity.this, "You have no stock for Stock Out Sales.");
            }

        } else if (clkdView == carryinLayout) {
            AceDnsDatabase setupDataHelperObj = new AceDnsDatabase(SalesOptionActivity.this);
            if (setupDataHelperObj.checkIfStockAvailableForDistribution()) {
                Constants.transactionStartTime = Calendar.getInstance().getTime();
                Intent intent = new Intent(SalesOptionActivity.this, OrderFormActivity.class);
                intent.putExtra("CARRY_IN", true);
                intent.putExtra("salesOption", true);
                startActivity(intent);
            } else {
                Utils.showToast(SalesOptionActivity.this, "You have no stock for CarryIn Sales.");
            }
        } else if (clkdView == reprintLayout) {
            boolean shouldCheckFlag = false;
            InvoiceInformationList = transDataHelperObj.getInvoiceList(shouldCheckFlag);
            InvoiceInformationListSearchResult = new ArrayList<>(InvoiceInformationList);
            if (InvoiceInformationList.size() > 0) {
                adapterCust = new InvoiceInformationAdapter(mContext,
                        R.layout.customer_list_child, InvoiceInformationListSearchResult);
                showChooseInvoiceListDialog();
            } else {
                Toast.makeText(mContext, "No invoice found", Toast.LENGTH_SHORT).show();
            }
        } else if (clkdView == returnLayout) {
            AceDnsDatabase setupDataHelperObj = new AceDnsDatabase(SalesOptionActivity.this);
            if (setupDataHelperObj.checkIfStockAvailableForDistribution()) {
                Intent intent = new Intent(SalesOptionActivity.this, StockReturnActivity.class);
                startActivity(intent);
            } else {
                Utils.showToast(SalesOptionActivity.this, "You have no stock for Stock Return.");
            }
        } else if (clkdView == orderLayout) {
            Constants.transactionStartTime = Calendar.getInstance().getTime();
            Intent intent = new Intent(SalesOptionActivity.this, OrderFormActivity.class);
            intent.putExtra("CARRY_IN", false);
//			intent.putExtra("salesOption", true);
            startActivity(intent);
        }
//		else if(clkdView == deleteLayout){
//			showDeleteOptions();
//		}
    }

    @Override
    public boolean onLongClick(View arg0) {
        String libraryStatus = Utils.checkLibraryConditions(SalesOptionActivity.this);
        if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
            File gitTxt = new File(Utils.getAppStoragePath(mContext) + "git_master.txt");
            if (gitTxt.exists()) {
                gitTxt.delete();
            }
            new MASTER_LoadGITMasterData(SalesOptionActivity.this, true).execute();
        }
        return false;
    }

    public void showDeleteOptions() {
        final Dialog transDeleteOptionDialog = new Dialog(SalesOptionActivity.this, R.style.PauseDialog);
        transDeleteOptionDialog.setCancelable(false);
        transDeleteOptionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        transDeleteOptionDialog.setContentView(R.layout.stockout_dialog);
        TextView txtMsg = (TextView) transDeleteOptionDialog.findViewById(R.id.title);
        txtMsg.setText("Select an Option.");
        Button transactionWise = (Button) transDeleteOptionDialog.findViewById(R.id.btn_branch);
        transactionWise.setText("Transaction\nwise Deletion");
        transactionWise.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                transDeleteOptionDialog.cancel();
                showDeleteTransactionDialog();
            }
        });
        Button dateWise = (Button) transDeleteOptionDialog.findViewById(R.id.btn_cust);
        dateWise.setText("Date wise\nDeletion");
        dateWise.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                transDeleteOptionDialog.cancel();
                showDeleteDateWiseDialog();
            }
        });
        transDeleteOptionDialog.show();
    }

    public void showDeleteTransactionDialog() {
        final Dialog instructionDialog = new Dialog(mContext);
        instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        instructionDialog.setContentView(R.layout.user_instruction_dialog);
        TextView title = (TextView) instructionDialog.findViewById(R.id.title);
        title.setText("Provide the Transaction ID");
        final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
        Button submit = (Button) instructionDialog.findViewById(R.id.btn);
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                String instruction = "";
                instruction = edInst.getText().toString();
                if (instruction.length() >= 20) {
                    instructionDialog.cancel();
                    showConfirmationDialog(instruction);
                } else {
                    Utils.showToast(mContext, "Please provide a valid Transaction ID");
                }

            }
        });
        instructionDialog.show();
    }


    public void showDeleteDateWiseDialog() {
        final Dialog dateWiseDeleteDialog = new Dialog(mContext);
        dateWiseDeleteDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dateWiseDeleteDialog.setContentView(R.layout.date_wise_delete_dialog);
        TextView title = (TextView) dateWiseDeleteDialog.findViewById(R.id.title);
        title.setText("Provide following Details.");

        btnDetails = (Button) dateWiseDeleteDialog.findViewById(R.id.btn_branch_rds);
        btnStartDate = (Button) dateWiseDeleteDialog.findViewById(R.id.btn_start);
        btnEndDate = (Button) dateWiseDeleteDialog.findViewById(R.id.btn_end);
        Button submit = (Button) dateWiseDeleteDialog.findViewById(R.id.btn);
        btnDetails.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                showMISOptions("");
            }
        });
        btnStartDate.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                startSelect = true;
                endSelect = false;
                chooseDateDialog();
            }
        });
        btnEndDate.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                endSelect = true;
                startSelect = false;
                chooseDateDialog();
            }
        });
        submit.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View v) {
                if (btnStartDate.getText().toString().length() > 0 &&
                        btnEndDate.getText().toString().length() > 0 && selectedBranchCode.length() > 0 &&
                        selectedRDSCode.length() > 0) {
                    dateWiseDeleteDialog.cancel();
                    showConfirmationDialog("");
                } else {
                    Utils.showToast(mContext, "Provide all details");
                }
            }
        });
        dateWiseDeleteDialog.show();
    }

    public void chooseDateDialog() {
        dialogCaldroidFragment = new CaldroidFragment();
        dialogCaldroidFragment.setCaldroidListener(listener);
        final String dialogTag = "CALDROID_DIALOG_FRAGMENT";
        dialogCaldroidFragment.show(getSupportFragmentManager(), dialogTag);
    }

    public void showMISOptions(final String parameter) {
        loader = new ProgressDialog(mContext);
        loader.setMessage("Fetching Data.Please wait..");
        loader.show();
        new Thread() {
            public void run() {
                if (parameter.length() == 0) {
                    misOptionList = transDataHelperObj.getMISListForBranch();
                } else {
                    misOptionList = transDataHelperObj.getMISListForRDS(parameter);
                }
                Message msgObj = misHandler.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", parameter);
                msgObj.setData(b);
                misHandler.sendMessage(msgObj);
            }
        }.start();
    }

    public void showOptionForMISReport(final String parameter) {
        final SimpleStringAdapter adapterMISOption;
        adapterMISOption = new SimpleStringAdapter(SalesOptionActivity.this, R.layout.simple_list_child, misOptionList, "hideRest");
        final Dialog misOptionDialog = new Dialog(SalesOptionActivity.this, R.style.PauseDialog);
        misOptionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        misOptionDialog.setContentView(R.layout.select_multiple_from_list);
        misOptionDialog.setCancelable(false);
        TextView title = (TextView) misOptionDialog.findViewById(R.id.title);
        title.setText("Please select an Option");
        final ListView dialogList = (ListView) misOptionDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapterMISOption);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                String selectedCodes = "";
                String selectedNames = "";
                String val = adapterMISOption.getItem(arg2);
                String[] valueArray = val.split("\\*");
                String selectedName = valueArray[0];
                String selectedCode = valueArray[1];
                selectedCodes = selectedCodes + selectedCode;
                selectedNames = selectedNames + selectedName;
                if (parameter.length() == 0) {
                    selectedBranchCode1 = selectedBranchCode1 + "'" + selectedCode + "',";
                }
                if (parameter.length() == 0) {
                    btnDetails.setText(btnDetails.getText() + selectedNames);
                    selectedBranchCode = selectedCodes;
                    showMISOptions(selectedBranchCode1.substring(0, selectedBranchCode1.length() - 1));
                } else {
                    selectedRDSCode = selectedCodes;
                    btnDetails.setText(btnDetails.getText() + " : " + selectedNames);
                    btnDetails.setEnabled(false);
                }
                misOptionDialog.cancel();
            }
        });
//		RelativeLayout chkAllLayout = (RelativeLayout)misOptionDialog.findViewById(R.id.select_all_layout);
//		chkAllLayout.setVisibility(chkAllLayout.GONE);
//		Button submit = (Button)misOptionDialog.findViewById(R.id.button1);
//		submit.setOnClickListener(new OnClickListener() {
//			@Override
//			public void onClick(View arg0) {
//				String selectedCodes = "";
//				String selectedNames = "";
//					   final SparseBooleanArray checkedItems = dialogList.getCheckedItemPositions();
//					   int checkedItemsCount = checkedItems.size();
//					   if(checkedItemsCount > 0){
//						   for (int i = 0; i < checkedItemsCount; ++i) {
//							   int position = checkedItems.keyAt(i);
//							   if(checkedItems.valueAt(i)){
//								   String val = adapterMISOption.getItem(position);
//								   String[] valueArray = val.split("\\*");
//								   String selectedName = valueArray[0];
//								   String selectedCode = valueArray[1];
//								   selectedCodes = selectedCodes +selectedCode+",";
//								   selectedNames = selectedNames + selectedName+",";
//								   if(parameter.length() == 0){
//										selectedBranchCode1 = selectedBranchCode1 + "'" + selectedCode + "',";
//									}
//							   }
//						   }
//						   selectedCodes = selectedCodes.substring(0,selectedCodes.length()-1);
//						   selectedNames = selectedNames.substring(0,selectedNames.length()-1);
//						   if(parameter.length() == 0){
//							   btnDetails.setText(btnDetails.getText() + selectedNames);
//							   selectedBranchCode = selectedCodes;
//							   showMISOptions(selectedBranchCode1.substring(0,selectedBranchCode1.length()-1));
//						   }else{
//							    selectedRDSCode = selectedCodes;
//							    btnDetails.setText(btnDetails.getText()  + " : " + selectedNames);
//							    btnDetails.setEnabled(false);
//						   }
//						   misOptionDialog.cancel();
//				   }
//			   
//			}
//		});
        misOptionDialog.show();
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


    public void showConfirmationDialog(final String instruction) {

        AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(SalesOptionActivity.this);
        alertDialogBuilder
                .setMessage("Are you sure you want to DELETE this transaction ?")
                .setCancelable(false)
                .setPositiveButton("   Yes   ", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        if (instruction.length() == 0) {
                            new TRANS_DeleteTransactionTask(SalesOptionActivity.this, "datewise", "", btnStartDate.getText().toString(), btnEndDate.getText().toString(), selectedRDSCode, selectedBranchCode).execute();
                        } else {
                            new TRANS_DeleteTransactionTask(SalesOptionActivity.this, "transactionwise", instruction, "", "", "", "").execute();
                        }
                    }
                })
                .setNegativeButton("   No   ", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {
                        finish();
                    }
                });
        AlertDialog alertDialog = alertDialogBuilder.create();
        alertDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        alertDialog.show();
    }

    public void showChooseInvoiceListDialog() {
        final Dialog InvoiceListDialog = new Dialog(mContext,
                R.style.PauseDialog);
        InvoiceListDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        InvoiceListDialog.setContentView(R.layout.choose_customer_search);
        InvoiceListDialog.setCancelable(true);
        TextView title = (TextView) InvoiceListDialog.findViewById(R.id.title);
        title.setText("Please select an invoice to print");

        final EditText searchText = (EditText) InvoiceListDialog
                .findViewById(R.id.autoCompleteTextView1);
        searchText.addTextChangedListener(new TextWatcher() {
            @Override
            public void onTextChanged(CharSequence arg0, int arg1, int arg2,
                                      int arg3) {
                String searchString = searchText.getText().toString();
                Log.d("searchString",searchString+"");
                int textLength = searchString.length();
                //searchString.con

                //clear the initial data set
                InvoiceInformationListSearchResult.clear();
                for (int i = 0; i < InvoiceInformationList.size(); i++) {
                    String invoiceNo = InvoiceInformationList.get(i).getInvoiceNo();

                    Log.d("invoiceN",invoiceNo.substring(0,textLength)+"");
                    if (textLength <= invoiceNo.length()) {
                        if (invoiceNo.toLowerCase().contains(searchString.toLowerCase())) {
                            InvoiceInformationListSearchResult.add(InvoiceInformationList.get(i));
                        }
                    }
                }

                adapterCust.notifyDataSetChanged();
            }

            @Override
            public void beforeTextChanged(CharSequence arg0, int arg1,
                                          int arg2, int arg3) {
            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });

        ListView dialogList = (ListView) InvoiceListDialog
                .findViewById(R.id.list);
        dialogList.setAdapter(adapterCust);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @SuppressLint("NewApi")
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                TextView invNoTV = (TextView) arg1.findViewById(R.id.list_details);
                TextView orderNoTV = (TextView) arg1.findViewById(R.id.HiddenValue1);
                TextView invoiceDateTV = (TextView) arg1.findViewById(R.id.HiddenValue2);
                TextView customerCodeTV = (TextView) arg1.findViewById(R.id.HiddenValue3);
                TextView freightChargeTV = (TextView) arg1.findViewById(R.id.HiddenValue4);
                selectedInvoiceNumber = invNoTV.getText().toString();
                String selectedOrderNumber = orderNoTV.getText().toString();
                String invoiceDate = invoiceDateTV.getText().toString();
                String customerCode = customerCodeTV.getText().toString();
                freightCharge = freightChargeTV.getText().toString();
                if (!freightCharge.matches("")) {
                    shouldPrintFreightBill = true;
                } else {
                    shouldPrintFreightBill = false;
                }

                CustomerDetails detailsObj = transDataHelperObj.GetCustomerDetailsByCode(customerCode);
                if (detailsObj != null) {
                    currentInvoiceCustomerName = detailsObj.getCustomerName();
                    currentInvoiceCustomerPin = detailsObj.getPin();
                    currentInvoiceCustomerAddress = detailsObj.getAddress();
                }


                mNsdManager = (NsdManager) mContext.getSystemService(Context.NSD_SERVICE);
                initializeResolveListener();
                initializeDiscoveryListener();
                mNsdManager.discoverServices(
                        SERVICE_TYPE, NsdManager.PROTOCOL_DNS_SD, mDiscoveryListener);
                currentDateandTime = invoiceDate;
                selectedProductMasterList = transDataHelperObj.getOrderListByOrderNumber(selectedOrderNumber);
                if (selectedProductMasterList != null) {
                    ArrayList<String> instructionSaleTypeList = transDataHelperObj.getSaleTypeAndInstructionFromOrderHeader(selectedOrderNumber);
                    if (instructionSaleTypeList != null && instructionSaleTypeList.size() > 0) {
                        saleType = instructionSaleTypeList.get(0);
                        if (saleType.matches("CHEQUE")) {
                            String[] d_instruction_splitted = instructionSaleTypeList.get(1).split(";");
                            if (d_instruction_splitted.length > 1) {
                                bankName = d_instruction_splitted[d_instruction_splitted.length - 1];
                                chequeNo = d_instruction_splitted[d_instruction_splitted.length - 2];
                            }


                        }

                    }

                    calculatePrintStringSalesBill();
                    calculatePrintStringMoneyReceipt();
                    if (shouldPrintFreightBill) {
                        calculatePrintStringFreightBill();
                    }

                    new Async_PrintWlan("sales bill").execute();
                } else {

                }

                getWindow()
                        .setSoftInputMode(
                                WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                InvoiceListDialog.cancel();


            }
        });

        Button addCustomer = (Button) InvoiceListDialog
                .findViewById(R.id.btn_add);
        addCustomer.setVisibility(View.GONE);
        InvoiceListDialog.show();
    }

    private void calculatePrintStringSalesBill() {
        finalPrintStringSalesBill = new SpannableStringBuilder();
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printRKBKHeaderLineSalesBill());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printRKBKAddress());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());

        finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineTwo(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceCustomerName, 42)));

//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        //TODO take this date time function to a separate class
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        currentDateandTime = sdf.format(new Date());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printInvoiceDateLineSalesBill(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(selectedInvoiceNumber, 48), currentDateandTime));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineSix());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

        int sizeOfTotalItemsInCart = selectedProductMasterList.size();

        Double vatRate = 4.00;
        Double AdditionalVatRate = 1.00;
        Double AdditionalVat = 0.00;
        grandTotal = 0;

        for (int l = 0; l < sizeOfTotalItemsInCart; l++) {
            int Sl = l + 1;
            String currentProductDescription = selectedProductMasterList.get(l).getDesc();
            vatRate = Double.parseDouble(selectedProductMasterList.get(l).getVatRate());


            AdditionalVatRate = Double.parseDouble(selectedProductMasterList.get(l).getAdditionalVatRate());
            Double currentQuantity = Double.parseDouble(selectedProductMasterList.get(l).getQty());
            Double currentRate = Double.parseDouble(selectedProductMasterList.get(l).getMrpValue().length() > 0 ? selectedProductMasterList.get(l).getMrpValue() : selectedProductMasterList.get(l).getAmount());
            Double currentAmount = currentQuantity * currentRate;

            if (vatRateList.size() == 0) {
                vatRateList.add(vatRate);
                TotalAmountForEachVatRate.add(currentAmount);
            } else {
                int position = compareVatRateListWithCurrentItem(vatRate);
                if (position == -1) {
                    vatRateList.add(vatRate);
                    TotalAmountForEachVatRate.add(currentAmount);
                } else {
                    Double presentAmount = TotalAmountForEachVatRate.get(position);
                    TotalAmountForEachVatRate.set(position, presentAmount + currentAmount);
                }
            }

            finalPrintStringSalesBill.append(PrintTextFormatterObject.addOneColumn(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(Sl + ".", 3), PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentProductDescription, 15), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(vatRate), 6), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(currentQuantity), 8), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(currentRate), 8), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(currentAmount), 10)));

            grandTotal = grandTotal + currentAmount;
        }
        for (int l = 0; l < 1 - sizeOfTotalItemsInCart; l++) {
            finalPrintStringSalesBill.append(PrintTextFormatterObject.addOneColumn(PrintTextFormatterObject.addSpace(3), PrintTextFormatterObject.addSpace(15), PrintTextFormatterObject.addSpace(6), PrintTextFormatterObject.addSpace(8), PrintTextFormatterObject.addSpace(8), PrintTextFormatterObject.addSpace(10)));
        }

        AdditionalVat = grandTotal * (AdditionalVatRate / 100);
        totalPriceWithoutVat = grandTotal;
        for (int i = 0; i < vatRateList.size(); i++) {
            Double currentVatRate = vatRateList.get(i);
            Double amountForCurrentVatRate = TotalAmountForEachVatRate.get(i);
            String formattedVatRate = df.format(currentVatRate);
            if (formattedVatRate.matches(".00")) {
                formattedVatRate = "0.00";
            }
            String vatDesc = "VAT @" + formattedVatRate + "% on amount " + df.format(amountForCurrentVatRate);
            Double VatAmountCurrent = amountForCurrentVatRate * (currentVatRate / 100);
            String formattedVatAmount = df.format(VatAmountCurrent);
            if (formattedVatAmount.matches(".00")) {
                formattedVatAmount = "0.00";
            }

            finalPrintStringSalesBillVatString.append(PrintTextFormatterObject.printLineVateRateLoop(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(vatDesc, 39), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(formattedVatAmount, 12)));
            totalPriceWithoutVat = totalPriceWithoutVat - VatAmountCurrent;
        }
        String formattedAdditionalVat = "";
        if (AdditionalVat > 0) {
            formattedAdditionalVat = df.format(AdditionalVat);
        } else {
            formattedAdditionalVat = "0.00";
        }
        finalPrintStringSalesBillVatString.append(PrintTextFormatterObject.printLineEightAdditionalVat(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(formattedAdditionalVat, 12), PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(df.format(AdditionalVatRate) + "%", 6)));
        totalPriceWithoutVat = totalPriceWithoutVat - AdditionalVat;


        finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

//		finalPrintStringSalesBill.append(PrintTextFormatterObject.printVatBreakUpString());
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printGrandTotal(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(grandTotal), 12)));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printTotalPriceWithoutVat(PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(totalPriceWithoutVat), 12)));
        finalPrintStringSalesBill.append(finalPrintStringSalesBillVatString);

//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

        vatRateList = new ArrayList<>();
        TotalAmountForEachVatRate = new ArrayList<>();
        finalPrintStringSalesBillVatString = new SpannableStringBuilder();


//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));


        String grandTotalInWords = new EnglishNumberToWords().convert((long) grandTotal);
        double grandTotalPaisa = Double.parseDouble(df.format(grandTotal).split("\\.")[1]);
        String grandTotalInWordsPaisa = new EnglishNumberToWords().convert((long) grandTotalPaisa);
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineSevenAmountInWords(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(grandTotalInWords+" Rupees and "+grandTotalInWordsPaisa+" Paisa Only",49)));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineSevenAmountInWords(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(grandTotalInWords + "", 49)));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringSalesBill.append(PrintTextFormatterObject.printLineEandOE());
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringSalesBill.append(PrintTextFormatterObject.addEmptyLine());


    }

    private void calculatePrintStringMoneyReceipt() {
        finalPrintStringMoneyReceipt = new SpannableStringBuilder();

        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKHeaderLineSalesBill2());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLine());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKAddress());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLine());

        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine3(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceCustomerName, 31)));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printDateFreight(new DateTimeFormatter().changeDateFormat("yyyy-MM-dd", currentDateandTime, "MM/dd/yy")));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine4(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(df.format(grandTotal), 38)));


//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printLineTwo(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceCustomerName,42)));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printInvoiceMoneyReceipt(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(selectedInvoiceNumber,51)));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printInvoiceDateLineSalesBill(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceNumber,48),new SimpleDateFormat("dd/MM/yy").format(new Date())+"  "));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine3(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceCustomerName,31),new DateTimeFormatter().changeDateFormat("yyyy-MM-dd",currentDateandTime,"MM/dd/yy")));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());

        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine5());
        String paymentTypeString = calculatePaymentDetailsLine();
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine6(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(paymentTypeString, 57)));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine7(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(selectedInvoiceNumber, 43)));
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine8());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addEmptyLineWithMargin2());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine9());
//		finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printRKBKMoneyReceiptLine10());
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringMoneyReceipt.append(PrintTextFormatterObject.printLineEandOE());

    }

    private void calculatePrintStringFreightBill() {
        finalPrintStringFreightBill = new SpannableStringBuilder();
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printRKBKHeaderLineSalesBill3());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printRKBKAddress());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLineWithMargin());
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.printRKBKAddress());
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.printInvoiceMoneyReceipt(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(selectedInvoiceNumber,51)));
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLineWithMargin());

        //TODO take this date time function to a separate class
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        currentDateandTime = sdf.format(new Date());


        String invoiceAndDate = selectedInvoiceNumber + " on Date " + currentDateandTime;

        finalPrintStringFreightBill.append(PrintTextFormatterObject.printLineTwo(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(currentInvoiceCustomerName, 42)));//
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillHeaderLine());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

        double freightCost = Double.parseDouble(freightCharge);
        String formattedGrandTotal = df.format(freightCost);
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillHeaderLine1());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillHeaderLine1(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned("Being the amount " + formattedGrandTotal, 57)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillHeaderLine2(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(invoiceAndDate, 56), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(formattedGrandTotal, 18)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));


        double serviceVatAmount = freightCost * (freightServiceTaxPercentage / 100);
        double serviceSwacchVatAmount = freightCost * (freightSwachhBharatPercent / 100);
        double AmountExcludingVat = freightCost - (serviceVatAmount + serviceSwacchVatAmount);

        String vatLine = "Add Service Tax @" + freightServiceTaxPercentage + "%";
        String vatLine2 = "Add Swachh Bharat Cess @" + freightSwachhBharatPercent + "%";

        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillVat1(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(vatLine, 40), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(serviceVatAmount), 17)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillVat1(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(vatLine2, 40), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(serviceSwacchVatAmount), 17)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printFreightBillVat1(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned("Total", 40), PrintTextFormatterObject.calculateStringWithSpaceRightAligned(df.format(AmountExcludingVat), 17)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

        String grandTotalInWords = new EnglishNumberToWords().convert((long) freightCost);
        double grandTotalPaisa = Double.parseDouble(formattedGrandTotal.split("\\.")[1]);
        String grandTotalInWordsPaisa = new EnglishNumberToWords().convert((long) grandTotalPaisa);
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printLineSevenAmountInWordsFreight(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(grandTotalInWords + "", 54)));
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.printLineSevenAmountInWordsFreight(PrintTextFormatterObject.calculateStringWithSpaceLeftAligned(grandTotalInWords+" Rupees and "+grandTotalInWordsPaisa+" Paisa Only",69)));
        finalPrintStringFreightBill.append(PrintTextFormatterObject.addUnderScoreEndLine(80));

//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());
//		finalPrintStringFreightBill.append(PrintTextFormatterObject.addEmptyLine());
        finalPrintStringFreightBill.append(PrintTextFormatterObject.printLineEandOE());
    }

    public void initializeDiscoveryListener() {

        // Instantiate a new DiscoveryListener
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.JELLY_BEAN) {
            mDiscoveryListener = new NsdManager.DiscoveryListener() {

                //  Called as soon as service discovery begins.
                @Override
                public void onDiscoveryStarted(String regType) {

                }

                @Override
                public void onServiceFound(NsdServiceInfo service) {
                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN) {
                        if (service.getServiceType().equals(SERVICE_TYPE)) {
                            mNsdManager.resolveService(service, mResolveListener);
                        }

                    }
                }

                @Override
                public void onServiceLost(NsdServiceInfo service) {

                }

                @Override
                public void onDiscoveryStopped(String serviceType) {

                }


                @Override
                public void onStartDiscoveryFailed(String serviceType, int errorCode) {

                }

                @Override
                public void onStopDiscoveryFailed(String serviceType, int errorCode) {

                }
            };
        }
    }

    public void initializeResolveListener() {
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.JELLY_BEAN) {
            mResolveListener = new NsdManager.ResolveListener() {

                @Override
                public void onResolveFailed(NsdServiceInfo serviceInfo, int errorCode) {
                }

                @TargetApi(Build.VERSION_CODES.JELLY_BEAN)
                @Override
                public void onServiceResolved(NsdServiceInfo serviceInfo) {
                    InetAddress serviceIp = serviceInfo.getHost();
                    String ip = serviceIp.getHostAddress();
                    if (!Constants.currentPrinterIp.matches(ip)) {
                        Constants.currentPrinterIp = ip;
                    }
                }
            };
        }
    }

    int compareVatRateListWithCurrentItem(Double vatRate) {
        int position = -1;
        for (int i = 0; i < vatRateList.size(); i++) {
            Double aDouble = vatRateList.get(i);
            if (String.valueOf(aDouble).matches(vatRate + "")) {
                position = i;
            }

        }

        return position;
    }

    private String calculatePaymentDetailsLine() {
        String paymentTypeString = "";
        if (saleType.matches("CREDIT")) {
            paymentTypeString = "By " + saleType;
        } else if (saleType.matches("COD")) {
            paymentTypeString = "By CASH ON DELIVERY";
        } else if (saleType.matches("CASH")) {
            paymentTypeString = "By CASH";
        } else//CHEQUE
        {
            paymentTypeString = "By " + saleType + " No. " + chequeNo + " of " + bankName.toUpperCase();
        }

        return paymentTypeString;
    }

    public class CustomerAdapter extends ArrayAdapter<InvoiceInformation> implements Filterable {

        private final Context context;
        private final ArrayList<InvoiceInformation> nameValues;
        private final int resourceId;
        private ViewHolder viewHolder;

        public CustomerAdapter(Context context, int resourceId, ArrayList<InvoiceInformation> nameValues) {

            super(context, resourceId, nameValues);
            this.context = context;
            this.nameValues = nameValues;
            this.resourceId = resourceId;
        }

        @Override
        public View getView(int position, View convertView, ViewGroup parent) {

            if (convertView == null) {
                LayoutInflater inflater = (LayoutInflater) context
                        .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
                convertView = inflater.inflate(resourceId, parent, false);
                viewHolder = new ViewHolder();
                viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
                viewHolder.HiddenValue1 = (TextView) convertView.findViewById(R.id.HiddenValue1);
                viewHolder.HiddenValue2 = (TextView) convertView.findViewById(R.id.HiddenValue2);
                viewHolder.HiddenValue3 = (TextView) convertView.findViewById(R.id.HiddenValue3);
                convertView.setTag(viewHolder);
            } else {
                viewHolder = (ViewHolder) convertView.getTag();
            }
            String menuItem = nameValues.get(position).getInvoiceNo();
            String orderNumber = nameValues.get(position).getOrderNo();
            String InvoiceDate = nameValues.get(position).getInvoiceDate();
            String customerCode = nameValues.get(position).getCustomerCode();
            viewHolder.txtView.setText(menuItem);
            viewHolder.HiddenValue1.setText(orderNumber);
            viewHolder.HiddenValue2.setText(InvoiceDate);
            viewHolder.HiddenValue3.setText(customerCode);
            return convertView;
        }


        public class ViewHolder {
            TextView txtView, HiddenValue1, HiddenValue2, HiddenValue3;
        }
    }

    class Async_PrintWlan extends AsyncTask<String, Void, Void> {
        String printType = "";
        Boolean printingDone = false;

        public Async_PrintWlan(String printType) {
            this.printType = printType;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();

        }

        @Override
        protected Void doInBackground(String... params) {
            try {
//				Socket sock = new Socket("192.168.11.229", 9100);
                Socket sock = new Socket(Constants.currentPrinterIp, 9100);
                PrintWriter oStream = new PrintWriter(sock.getOutputStream());
                boolean connected = sock.isConnected();
                if (!connected) {
                    Toast.makeText(mContext, "Could not connect to printer, make sure your device and printer are connected to same network", Toast.LENGTH_SHORT).show();
                }
                if (printType.matches("sales bill")) {
                    oStream.println(finalPrintStringSalesBill);
                    finalPrintStringSalesBill = new SpannableStringBuilder();
                } else if (printType.matches("money receipt")) {
                    oStream.println(finalPrintStringMoneyReceipt);
                    finalPrintStringMoneyReceipt = new SpannableStringBuilder();
                } else//freight bill
                {
                    oStream.println(finalPrintStringFreightBill);
                    finalPrintStringFreightBill = new SpannableStringBuilder();
                }

                oStream.close();
                sock.close();
                printingDone = true;
            } catch (Exception e) {
                e.printStackTrace();
            }
            return null;
        }

        @Override
        protected void onPostExecute(Void result) {
            super.onPostExecute(result);
            if (printingDone) {
                if (printType.matches("sales bill")) {
                    new Async_PrintWlan("money receipt").execute();
                } else if (printType.matches("money receipt")) {
                    if (shouldPrintFreightBill) {
                        new Async_PrintWlan("freight bill").execute();
                    }

                }

            } else {
                Toast.makeText(mContext, "Something went wrong. \n Please make sure your printer and your device \n are connected to the same network.", Toast.LENGTH_LONG).show();
            }


        }
    }


}
