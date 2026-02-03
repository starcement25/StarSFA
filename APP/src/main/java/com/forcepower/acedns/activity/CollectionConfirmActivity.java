package com.forcepower.acedns.activity;

import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.os.Bundle;
import android.provider.MediaStore;
import androidx.fragment.app.FragmentActivity;
import android.text.Editable;
import android.text.InputFilter;
import android.text.InputType;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.backgroundTask.TRANS_PendingRoutePlanBeforeOtherTxn;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitOrderTask;
import com.forcepower.acedns.backgroundTask.TRANS_TourAttachmentExportTask;
import com.roomorama.caldroid.CaldroidFragment;
import com.roomorama.caldroid.CaldroidListener;

import com.forcepower.acedns.R;
import com.forcepower.acedns.adapter.BankAdapter;
import com.forcepower.acedns.backgroundTask.TRANS_CashDepositeReceiveTask;
import com.forcepower.acedns.bean.BankDetails;
import com.forcepower.acedns.bean.OutstandingDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.LocationTracker;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.text.NumberFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

import static com.forcepower.acedns.constants.Constants.isGettingCurrentLocation;
import static com.forcepower.acedns.util.Utils.NotCheckedOut;
import static com.forcepower.acedns.util.Utils.setCheckInOutLatLongAccuracyToLocationLatLong;

//import android.view.View.OnClickListener;

public class CollectionConfirmActivity extends FragmentActivity implements OnClickListener {

    AceDnsTransactionDatabase transDataHelperObj;
    AceDnsDatabase setupDataHelperObj;
    Context mContext;
    SimpleDateFormat formatter, tempFormatter;
    NumberFormat currencyFormatter = NumberFormat.getCurrencyInstance(new Locale("en", "IN"));
    CaldroidFragment dialogCaldroidFragment;
    CaldroidListener listener;
    Dialog bankDialog;
    ArrayList<BankDetails> bankList, tempBankList;
    ArrayList<String> bankNameList;
    LocationTracker LocationTrackerObject;
    EditText edAmt, edDate, edCheq, edRemarks;
    Button btnBank, btnSubmit, btnBack, btnSaleType, btnRemarks;
    LinearLayout cheqLayout;
    ImageView imgLogo;
    double amt = 0.00;
    Date currentDate, minChequeDate, chequeDate;
    BankDetails selectdBank;
    BankAdapter adapter1;
    String lastStr = "", remarks = "", saleType = "";
    TextView chequeHeader, dateHeader;
    int localDataSavingFailedAttempt = 0;
    static final int REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG = 3;
    String supportingAttachmentNameForCustomerImage="";
    Bitmap customerPicBitmapForLocation = null;
    private boolean isCameralaunched = false;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_collection_confirm);
        RegisterActivities.registerActivity(this);

        mContext = CollectionConfirmActivity.this;
        transDataHelperObj = new AceDnsTransactionDatabase(mContext);
        setupDataHelperObj = new AceDnsDatabase(mContext);
        Constants.isCheckedIn=NotCheckedOut(mContext);
        if(!Constants.isCheckedIn)
        {
            LocationTrackerObject=new LocationTracker(mContext,"stock audit confirm");
        }
        else
        {
            setCheckInOutLatLongAccuracyToLocationLatLong(mContext);
        }
        String timeStamp = new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        formatter = new SimpleDateFormat("yyyy-MM-dd");
        tempFormatter = new SimpleDateFormat("dd-MM-yyyy");
        try {
            currentDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
            chequeDate = new SimpleDateFormat("yyyyMMddHHmmss").parse(Constants.dateString + timeStamp);
        } catch (Exception e) {
            currentDate = new Date();
            chequeDate = new Date();
        }

        initView();

        Calendar cal = Calendar.getInstance();
        cal.add(Calendar.DATE, -90);
        minChequeDate = cal.getTime();

        listener = new CaldroidListener() {
            @Override
            public void onSelectDate(Date date, View view) {
                if (date.before(minChequeDate)) {
                    Utils.showToast(mContext, "Invalid Date.");
                } else {
                    dialogCaldroidFragment.dismiss();
                    chequeDate = date;
                    edDate.setText(tempFormatter.format(date));
                }
            }

            @Override
            public void onChangeMonth(int month, int year) {

            }

            @Override
            public void onLongClickDate(Date date, View view) {
                if (date.before(minChequeDate)) {
                    Utils.showToast(mContext, "Future dates cannot be selected");
                } else {
                    dialogCaldroidFragment.dismiss();
                    chequeDate = date;
                    edDate.setText(tempFormatter.format(date));
                }
            }

            @Override
            public void onCaldroidViewCreated() {

            }

        };


        showSaleTypeDialog();
    }
    @Override
    public void onResume()
    {
        super.onResume();
        if(!Constants.isCheckedIn)
        {
            LocationTrackerObject.checkLocationUpdateSharing();
        }
    }
    @Override
    public void onPause()
    {
        super.onPause();
        if(!Constants.isCheckedIn && !isCameralaunched)
        {
            LocationTrackerObject.stopLocationUpdates();
        }
    }

    @Override
    public void onStop()
    {
        super.onStop();
        if(!Constants.isCheckedIn && !isCameralaunched)
        {
            LocationTrackerObject.stopLocationUpdates();
        }
    }
    private void launchCameraToTakeCustomerPictureThenSubmit()
    {
        Intent takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
        if (takePictureIntent.resolveActivity(getPackageManager()) != null)
        {
            isCameralaunched=true;
            startActivityForResult(takePictureIntent, REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG);
        }
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        if(requestCode==9999 )
        {
            if (resultCode == RESULT_CANCELED)
            {
                if(Constants.userDetailsObj.getGPS_all_transaction().equalsIgnoreCase("yes")  ||  Constants.menuDetailsObj.getgeo_fencing_menu().contains("order"))
                {
                    LocationTrackerObject.checkLocationUpdateSharing();
                }

            }
            else if (resultCode == RESULT_OK)
            {
                LocationTrackerObject.startLocationUpdates();
            }
        }
        if (requestCode == REQUEST_IMAGE_CAPTURE_FOR_LAT_LONG)
        {
            if (resultCode == RESULT_OK)
            {
                if (data != null)
                {
                    try
                    {
                        Bundle extras = data.getExtras();
                        Bitmap imageBitmap = (Bitmap) extras.get("data");
                        customerPicBitmapForLocation = Utils.getResizedBitmap(imageBitmap, 100, 100);
                        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        supportingAttachmentNameForCustomerImage = Constants.employeeDetailObject.getEmpCode() + timeStamp + ".jpeg";
                        String imagePath = Utils.getAppStoragePath(mContext) + supportingAttachmentNameForCustomerImage;
                        Utils.storeImageInLocalStorageShowOnImageView(customerPicBitmapForLocation,imagePath);
                        setupDataHelperObj.updateCustomerLatLongiImage(Constants.selectedCustomer.getCustomerCode(),supportingAttachmentNameForCustomerImage);
                        Constants.selectedCustomer.setbase_latt(Constants.currentLat);
                        Constants.selectedCustomer.setbase_longi(Constants.currentLong);
                        transDataHelperObj.insertToSupportingAttachTable(supportingAttachmentNameForCustomerImage, "collection");
                        collectionSubmissionClick();
                    }
                    catch (Exception e)
                    {
                        Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                    }
                }
                else
                {
                    Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
                }


            }
            else
            {
                Toast.makeText(mContext, "Something went wrong while getting the image, please try again.", Toast.LENGTH_SHORT).show();
            }

        }
    }
    public void initView() {
        imgLogo = (ImageView) findViewById(R.id.imagelogo);
        if (Constants.logoBmp != null) {
            imgLogo.setVisibility(View.VISIBLE);
            imgLogo.setImageBitmap(Constants.logoBmp);
        } else {
            imgLogo.setVisibility(View.GONE);
        }

        chequeHeader = (TextView) findViewById(R.id.txt_cheque_header);
        dateHeader = (TextView) findViewById(R.id.txt_date_header);

        TextView txtVersion = (TextView) findViewById(R.id.txt_version);
        //txtVersion.setText("Ver~"+Utils.getAppVersion(mContext));
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));

        edAmt = (EditText) findViewById(R.id.ed_amt);
        for (int ii = 0; ii < Constants.selectedOutstandingList.size(); ii++) {
            amt = amt + Double.parseDouble(Constants.selectedOutstandingList.get(ii).getReceiptAmt());
        }
        edAmt.setText(currencyFormatter.format(amt));
        edAmt.setBackgroundColor(Color.WHITE);
        edAmt.setEnabled(false);
        cheqLayout = (LinearLayout) findViewById(R.id.cheque_layout);

        edDate = (EditText) findViewById(R.id.ed_date);
        edDate.setFocusableInTouchMode(false);
        edDate.setText(tempFormatter.format(currentDate));
        edDate.setOnClickListener(CollectionConfirmActivity.this);
        edCheq = (EditText) findViewById(R.id.ed_cheq);
        edRemarks = (EditText) findViewById(R.id.ed_remarks);
        btnBank = (Button) findViewById(R.id.btn_bank);
        btnBank.setOnClickListener(CollectionConfirmActivity.this);
        btnSubmit = (Button) findViewById(R.id.btn_submit);
        btnSubmit.setOnClickListener(CollectionConfirmActivity.this);
        btnBack = (Button) findViewById(R.id.back);
        btnBack.setOnClickListener(this);
        btnSaleType = (Button) findViewById(R.id.btn_sale_type);
        btnSaleType.setOnClickListener(this);
        btnRemarks = (Button) findViewById(R.id.btn_remarks);
        btnRemarks.setOnClickListener(this);
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

    public void chooseBankDialog() {
        bankDialog = new Dialog(CollectionConfirmActivity.this);
        bankDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        bankDialog.setContentView(R.layout.select_with_search);
        bankList = setupDataHelperObj.getBankList();
        bankNameList = new ArrayList<String>();
        for (int ii = 0; ii < bankList.size(); ii++) {
            bankNameList.add(bankList.get(ii).getBankName());
        }
        tempBankList = new ArrayList<BankDetails>();
        reInitialiseBankList();
        adapter1 = new BankAdapter(CollectionConfirmActivity.this, R.layout.bank_list_child, tempBankList);
        TextView title = (TextView) bankDialog.findViewById(R.id.title);
        title.setText("Please select an option");
        EditText searchText = (EditText) bankDialog.findViewById(R.id.autoCompleteTextView1);
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
                    reInitialiseBankList();
                }
                lastStr = str;
                filterBankArray(str.length(), str);
                adapter1.notifyDataSetChanged();

                System.out.println("String::::::::" + str);
            }
        });
        ListView dialogList = (ListView) bankDialog.findViewById(R.id.list);
        dialogList.setAdapter(adapter1);
        dialogList.setOnItemClickListener(new OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                    long arg3) {
                bankDialog.cancel();
                selectdBank = tempBankList.get(arg2);
                btnBank.setText(tempBankList.get(arg2).getBankName());
            }
        });
        Button cancel = (Button) bankDialog.findViewById(R.id.btn_ok);
        cancel.setOnClickListener(new OnClickListener() {
            @Override
            public void onClick(View arg0) {
                bankDialog.cancel();
            }
        });
        bankDialog.show();
    }

    @Override
    public void onClick(View v) {
        if (v == edDate) {
            chooseDateDialog();
        } else if (v == btnBank) {
            chooseBankDialog();
        } else if (v == btnBack) {
            finish();
//				if(onAccountMode){
//					startActivity(new Intent(CollectionConfirmActivity.this,MenuActivity.class));
//				}
        } else if (v == btnSaleType) {
            showSaleTypeDialog();
        }
//			else if(v == btnRemarks){
//				showInstructionDialog();
//			}
        else if (v == btnSubmit)
        {
            if (Constants.menuDetailsObj.getgeo_fencing_menu().contains("collection"))
            {
                if(Utils.isNumeric(Constants.selectedCustomer.getbase_latt()) && Double.parseDouble(Constants.selectedCustomer.getbase_latt())>0 && Utils.isNumeric(Constants.selectedCustomer.getbase_longi())&& Double.parseDouble(Constants.selectedCustomer.getbase_longi())>0)
                {
                    collectionSubmissionClick();
                }
                else
                {
                    if(!isGettingCurrentLocation)
                    {
                        collectionSubmissionClick();
                    }
                    else
                    {
                        launchCameraToTakeCustomerPictureThenSubmit();
                    }
                }

            }
            else
            {
                collectionSubmissionClick();
            }

        }
    }

    public void collectionSubmissionClick() {
        btnSubmit.setEnabled(false);
//				String thousand = "0",fiveHundred = "0",hundred = "0",fifty = "0",twenty = "0",ten = "0",coins = "0";
        Boolean isTimeAutomatic = Utils.isTimeAutomatic(mContext);
        if (isTimeAutomatic)
        {
            String chequeDateStr = "", bankName = "", chequeNo = "", transType = "";
            remarks = edRemarks.getText().toString();

            GPSTracker gpstracker = new GPSTracker(mContext);
            if (cheqLayout.getVisibility() == View.VISIBLE && !saleType.equalsIgnoreCase("CARD")) {
                if (chequeDate != null) {
                    chequeDateStr = formatter.format(chequeDate);
                }
                if (selectdBank != null) {
                    bankName = selectdBank.getBankName();
                }
                chequeNo = edCheq.getText().toString();
                boolean chequeNoVer = false;
                if (saleType.equalsIgnoreCase("CHEQUE")) {
                    if (chequeNo.length() == 6) {
                        chequeNoVer = true;
                    }
                } else {
                    if (chequeNo.length() >= 8) {
                        chequeNoVer = true;
                    }
                }
                if (chequeDateStr.length() > 0 && chequeNoVer) {
                    btnSubmit.setEnabled(false);
                    if (saleType.equalsIgnoreCase("CHEQUE")) {
                        transType = "1";
                    } else if (saleType.equalsIgnoreCase("NEFT")) {
                        transType = "2";
                    } else if (saleType.equalsIgnoreCase("RTGS")) {
                        transType = "3";
                    } else if (saleType.equalsIgnoreCase("CARD")) {
                        transType = "5";
                    } else {
                        transType = "4";
                    }


                    MakeDataSubmitToLocalAndServerProcess(true, transType, chequeNo, bankName, chequeDateStr);
                }
                else
                {
                    if (saleType.equalsIgnoreCase("CHEQUE"))
                    {
                        Utils.showToast(mContext, "Please provide all the details. \n Cheque No should be 6 digits");
                    }
                    else
                    {
                        Utils.showToast(mContext, "Please provide all the details. \n UTR No should be minimum 8 digits");
                    }
                }
            }
            else
            {
                btnSubmit.setEnabled(false);

                MakeDataSubmitToLocalAndServerProcess(false, "0", "", "", "");
            }
            if (gpstracker != null)
                gpstracker.stopUsingGPS();
        }
        else
        {
            Utils.showSettingsAlertToChangeTimeZone(mContext);
        }
        btnSubmit.setEnabled(true);
    }

    private void MakeDataSubmitToLocalAndServerProcess(Boolean isCheckLayoutVisible, String transType, String chequeNo, String bankName, String chequeDateStr)
    {

        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
        Boolean isSuccessInsertToLocationTable, isSuccessInsertToPaymentHeaderTableFromCollection = true, isSuccessInsertToPaymentDetailsTableFromCollection;
        transDataHelperObj.beginTransaction();
        if (isCheckLayoutVisible)
        {
            isSuccessInsertToPaymentHeaderTableFromCollection = transDataHelperObj.insertToPaymentHeaderTableFromCollection(remarks, timeStamp, transType, chequeNo, bankName, chequeDateStr);
        }
        else
        {
            isSuccessInsertToPaymentHeaderTableFromCollection = transDataHelperObj.insertToPaymentHeaderTableFromCollection(remarks, timeStamp, transType, chequeNo, bankName, chequeDateStr);
        }

        isSuccessInsertToPaymentDetailsTableFromCollection = transDataHelperObj.insertToPaymentDetailsTableFromCollection(timeStamp);
        isSuccessInsertToLocationTable = transDataHelperObj.insertToLocationTable1("P", timeStamp);

        if (isSuccessInsertToLocationTable && isSuccessInsertToPaymentHeaderTableFromCollection && isSuccessInsertToPaymentDetailsTableFromCollection) {
            transDataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(true, true);
            transDataHelperObj = new AceDnsTransactionDatabase(mContext);
            if (Constants.orderFormDetailsObj.getSale().equalsIgnoreCase("yes")) {
                double amount = 0;
                for (int ii = 0; ii < Constants.selectedOutstandingList.size(); ii++) {
                    OutstandingDetails currentObj = Constants.selectedOutstandingList
                            .get(ii);
                    amount = amount + (Double.parseDouble(currentObj.getReceiptAmt()));
                }
                String transactionAlias = "CC";
                String transId = transactionAlias + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                transDataHelperObj.insertToLocationTable(transactionAlias, timeStamp);
                transDataHelperObj.insertToCashDepositReceiveDetails(transId, transactionAlias, Utils.changeDateFormat("yyyyMMdd", "yyyy-MM-dd", Constants.dateString), amount + "", remarks);
                new TRANS_CashDepositeReceiveTask(mContext, false).execute();
            }
            boolean isExist = transDataHelperObj.IsUnuploadedRoutePlanExist();
            transDataHelperObj.closeDatabase();
            if (true == isExist) {
                new TRANS_PendingRoutePlanBeforeOtherTxn(CollectionConfirmActivity.this, "COLLECTION").execute();
            } else {
                Constants.OrderTransactionTaskCalledFrom = "collection";
                if(!supportingAttachmentNameForCustomerImage.matches(""))
                {
                    new TRANS_TourAttachmentExportTask(mContext, "collection", "", false,false).execute();
                }
                new TRANS_SubmitOrderTask(CollectionConfirmActivity.this, true).execute();
            }
        } else {
            btnSubmit.setEnabled(true);
            transDataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false, true);
            transDataHelperObj = new AceDnsTransactionDatabase(mContext);
            if (localDataSavingFailedAttempt == 0) {
                Toast.makeText(mContext, "Oops! Something went wrong while saving data. please try again.", Toast.LENGTH_LONG).show();
                localDataSavingFailedAttempt++;

            } else if (localDataSavingFailedAttempt == 1) {
                Toast.makeText(mContext, "Issue likely a bit serious. Try once again.", Toast.LENGTH_LONG).show();
                localDataSavingFailedAttempt++;
//								dataHelperObj.setTransactionSuccessEndTransactionAndCloseDatabase(false,true);
            } else {
                Toast.makeText(mContext, "Sorry! memory related fatal exception found. Need to reenter data", Toast.LENGTH_LONG).show();
                Intent intent = new Intent(mContext,
                        MenuActivity.class);
                startActivity(intent);
            }
        }
    }


    public void reInitialiseBankList() {
        tempBankList.removeAll(tempBankList);
        int size = tempBankList.size();
        int size1 = bankList.size();
        System.out.println("SIZE" + size + "_____" + size1);
        for (int kk = 0; kk < bankList.size(); kk++) {
            tempBankList.add(bankList.get(kk));
        }

    }

    public void filterBankArray(int strCnt, String charVal) {
        int size = tempBankList.size();
        for (int ii = 0; ii < size; ii++) {
            if (tempBankList.get(ii).getBankName().length() >= strCnt) {
                if (tempBankList.get(ii).getBankName().toUpperCase().contains(charVal.toUpperCase())) {
                    // Keep this item in ArrayList
                } else {
                    tempBankList.remove(tempBankList.get(ii));
                    size = size - 1;
                    ii = ii - 1;
                }
            } else {
                tempBankList.remove(tempBankList.get(ii));
                size = size - 1;
                ii = ii - 1;
            }
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
		
		
/*		public void showInstructionDialog(){
			final Dialog instructionDialog = new Dialog(mContext);
			instructionDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
			instructionDialog.setContentView(R.layout.user_instruction_dialog);
			TextView title = (TextView)instructionDialog.findViewById(R.id.title);
			title.setText("Remarks if any ?");
			final EditText edInst = (EditText) instructionDialog.findViewById(R.id.ed_input);
			edInst.setText(remarks);
			Button submit = (Button) instructionDialog.findViewById(R.id.btn);
			submit.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					instructionDialog.cancel();	
					String instruction = "";
					instruction = edInst.getText().toString();
					remarks = instruction;
				}
			});
			instructionDialog.show();
		}*/


    public void showSaleTypeDialog() {
        final Dialog saleTypeDialog = new Dialog(CollectionConfirmActivity.this);
        saleTypeDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        saleTypeDialog.setContentView(R.layout.payment_type_dialog);
        saleTypeDialog.setCancelable(false);
        TextView title = (TextView) saleTypeDialog.findViewById(R.id.title);
        title.setText("Select a mode of Payment");

        final RadioButton radioCash = (RadioButton) saleTypeDialog.findViewById(R.id.rd_cash);
        final RadioButton radioCheque = (RadioButton) saleTypeDialog.findViewById(R.id.rd_cheque);
        final RadioButton radioNEFT = (RadioButton) saleTypeDialog.findViewById(R.id.rd_neft);
        final RadioButton radioRTGS = (RadioButton) saleTypeDialog.findViewById(R.id.rd_rtgs);
        final RadioButton rd_card = (RadioButton) saleTypeDialog.findViewById(R.id.rd_card);


        radioCash.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    radioCheque.setChecked(false);
                    radioNEFT.setChecked(false);
                    radioRTGS.setChecked(false);
                    rd_card.setChecked(false);
                    saleType = "CASH";
                    saleTypeDialog.cancel();
                    cheqLayout.setVisibility(View.GONE);
                }
            }
        });
        radioCheque.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    radioCash.setChecked(false);
                    radioNEFT.setChecked(false);
                    radioRTGS.setChecked(false);
                    rd_card.setChecked(false);
                    saleType = "CHEQUE";
                    saleTypeDialog.cancel();
                    chequeHeader.setText("Cheque No       ");
                    dateHeader.setText("Cheque Date    ");
                    cheqLayout.setVisibility(View.VISIBLE);
                    makeUiChangesChequeForNtfsRtgsCard(6);
                }
            }
        });
        radioNEFT.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    radioCash.setChecked(false);
                    radioCheque.setChecked(false);
                    radioRTGS.setChecked(false);
                    rd_card.setChecked(false);
                    saleType = "NEFT";
                    saleTypeDialog.cancel();
                    cheqLayout.setVisibility(View.VISIBLE);
                    chequeHeader.setText("UTR No          ");
                    dateHeader.setText("Trans Date     ");
                    makeUiChangesChequeForNtfsRtgsCard(22);

                }
            }
        });
        radioRTGS.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    radioCash.setChecked(false);
                    radioCheque.setChecked(false);
                    radioNEFT.setChecked(false);
                    rd_card.setChecked(false);
                    saleType = "RTGS";
                    saleTypeDialog.cancel();
                    cheqLayout.setVisibility(View.VISIBLE);
                    chequeHeader.setText("UTR No          ");
                    dateHeader.setText("Trans Date     ");
                    makeUiChangesChequeForNtfsRtgsCard(22);
                }
            }
        });
        rd_card.setOnCheckedChangeListener(new OnCheckedChangeListener() {
            @Override
            public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
                if (isChecked) {
                    radioCash.setChecked(false);
                    radioCheque.setChecked(false);
                    radioNEFT.setChecked(false);
                    radioRTGS.setChecked(false);
                    saleType = "CARD";
                    saleTypeDialog.cancel();
                    cheqLayout.setVisibility(View.VISIBLE);
                    chequeHeader.setText("UTR No          ");
                    dateHeader.setText("Trans Date     ");
                    makeUiChangesChequeForNtfsRtgsCard(22);
                }
            }
        });
        saleTypeDialog.show();
    }

    private void makeUiChangesChequeForNtfsRtgsCard(int maxLength) {
        if (saleType.equalsIgnoreCase("card")) {
            LinearLayout checkNo = (LinearLayout) findViewById(R.id.checkNo);
            LinearLayout checkDate = (LinearLayout) findViewById(R.id.checkDate);
            LinearLayout bankName = (LinearLayout) findViewById(R.id.bankName);
            checkNo.setVisibility(View.GONE);
            checkDate.setVisibility(View.GONE);
            bankName.setVisibility(View.GONE);
        } else {
            InputFilter[] fArray = new InputFilter[1];
            fArray[0] = new InputFilter.LengthFilter(maxLength);
            edCheq.setFilters(fArray);
            if (maxLength == 16) {
                edCheq.setInputType(InputType.TYPE_CLASS_TEXT);
            }
        }

    }

}
