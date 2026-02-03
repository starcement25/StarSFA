package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import androidx.fragment.app.FragmentActivity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.zj.btsdk.BluetoothService;
import com.zj.btsdk.PrintPic;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;

import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Objects;

public class BluetoothPrintSeocndProcessActivity extends FragmentActivity {
    private static final int REQUEST_ENABLE_BT = 2;
    private static final int REQUEST_CONNECT_DEVICE = 1;
    Button btnSearch;
    Button btnSendDraw,btnExit;
    Button btnSend;
    Button btnClose;
    EditText edtContext;
    DecimalFormat defaultFormat = new DecimalFormat("#.00");
    BluetoothService mService = null;
    BluetoothDevice con_dev = null;
    Context context;
    ArrayList<String> dataList;
    String printFor = "", customerName = "", totalAmount = "", invoiceNumber = "",
            date = "", bankname = "", paymentType = "", checkNo = "", chkDate = "";
    String b1, b2, b3, b4, b5, b6, b7, b8, b9;
    Double grandTotal = 0.00;
    AceDnsTransactionDatabase dbHelper;
    @SuppressLint("HandlerLeak")
    private final Handler mHandler = new Handler() {
        @Override
        public void handleMessage(Message msg) {
            switch (msg.what) {
                case BluetoothService.MESSAGE_STATE_CHANGE:
                    if (msg.arg1 == BluetoothService.STATE_CONNECTED) {
                        Toast.makeText(context, "Connect successful", Toast.LENGTH_SHORT).show();
                        btnClose.setEnabled(true);
                        btnSend.setEnabled(true);
                        btnSendDraw.setEnabled(true);
                    }
                    break;
                case BluetoothService.MESSAGE_CONNECTION_LOST:
                    Toast.makeText(context, "Device connection was lost", Toast.LENGTH_SHORT).show();
                    btnClose.setEnabled(false);
                    btnSend.setEnabled(false);
                    btnSendDraw.setEnabled(false);
                    break;
                case BluetoothService.MESSAGE_UNABLE_CONNECT:
                    Toast.makeText(context, "Unable to connect device", Toast.LENGTH_SHORT).show();
                    break;
            }
        }

    };

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_NOT_TOUCH_MODAL, WindowManager.LayoutParams.FLAG_NOT_TOUCH_MODAL);
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH, WindowManager.LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH);
        setContentView(R.layout.print_activity);

        context = this;
        dbHelper = new AceDnsTransactionDatabase(context);
        mService = new BluetoothService(this, mHandler);

        if (!mService.isAvailable()) {
            Toast.makeText(this, "Bluetooth is not available", Toast.LENGTH_LONG).show();
            finish();
        }
        Bundle bundle ;
        bundle = this.getIntent().getExtras();

        assert bundle != null;
        printFor = bundle.getString("PrintFor");
        assert printFor != null;
        if (printFor.equalsIgnoreCase("ORDER")) {
            grandTotal = 0.00;
            customerName = bundle.getString("CustomerName");
            dataList = new ArrayList<>();
            dataList.add("Bill # " + Constants.employeeDetailObject.getEmpCode() + "-" + (dbHelper.getOrderCountForPrint()));
            for (int i = 0; i < Constants.selectedProductMasterList.size(); i++) {
                String str =  BluetoothChatActivity.formatSerialNo("" + (i + 1)) +
                        BluetoothChatActivity.formatProdName(Constants.selectedProductMasterList.get(i).getDesc()) +
                        " " + BluetoothChatActivity.formatQty(Constants.selectedProductMasterList.get(i).getQty()) +
                        " " + BluetoothChatActivity.formatAmt(Constants.selectedProductMasterList.get(i).getMrpValue()) +
                        " " + BluetoothChatActivity.formatTD(Constants.selectedProductMasterList.get(i).getTradeDiscnt()) +
                        " " + BluetoothChatActivity.formatTotal(calculateTotal(Constants.selectedProductMasterList.get(i)));
                dataList.add(str);
            }
            String mTotal = defaultFormat.format(grandTotal);
            dataList.add(BluetoothChatActivity.formatGrandTotal("Total :  " + mTotal));
            edtContext =  findViewById(R.id.txt_content);
        } else if (printFor.equalsIgnoreCase("COLLECTION")) {
            customerName = bundle.getString("CustomerName");
            totalAmount = bundle.getString("TotalAmount");
            invoiceNumber = bundle.getString("InvoiceId");
            bankname = bundle.getString("BankName");
            checkNo = bundle.getString("Checkno");
            paymentType = bundle.getString("PaymentType");
            date = bundle.getString("Date");
            chkDate = bundle.getString("chkDate");

            if (paymentType.equals("Cheque")) {
                b1 = customerName + ".";
                b2 = "by " + paymentType;
                b3 = "Cheque No :" + checkNo;
                b4 = "Date:" + date + ".";
                b5 = "Bank name :" + bankname;
                b6 = "Invoice Number:" + invoiceNumber + ".";
                b7 = "Total Amount:" + totalAmount + ".";
                b8 = "Cheque Date:" + chkDate + ".";
                b9 = "---------------------------";
            } else {
                b1 = customerName + ".";
                b2 = "by " + paymentType;
                b3 = "Invoice Number:" + invoiceNumber + ".";
                b4 = "Total Amount:" + totalAmount + ".";
                b5 = "Date:" + date + ".";
                b6 = "-----------------------";
            }
        }
    }
    public String calculateTotal(ProductMasterDetails currentObj) {
        double amount = 0;
        double qty = Double.parseDouble(currentObj.getQty());
        double mrp = Double.parseDouble(currentObj.getMrpValue());
        double discount = Double.parseDouble(currentObj.getTradeDiscnt());
        amount = amount + ((mrp * qty) - (mrp * qty * discount / 100));
        grandTotal = grandTotal + amount;
        return (defaultFormat.format(amount));
    }
    @Override
    public void onStart() {
        super.onStart();
        if (!mService.isBTopen()) {
            Intent enableIntent = new Intent(BluetoothAdapter.ACTION_REQUEST_ENABLE);
            startActivityForResult(enableIntent, REQUEST_ENABLE_BT);
        }
        btnExit =  this.findViewById(R.id.btnExit);
        btnSendDraw =  this.findViewById(R.id.btn_test);
        btnExit.setOnClickListener(new ClickEvent());
        btnExit.setOnClickListener(new ClickEvent());
        btnSearch =  this.findViewById(R.id.btnSearch);
        btnSearch.setOnClickListener(new ClickEvent());
        btnSend =  this.findViewById(R.id.btnSend);
        btnSend.setOnClickListener(new ClickEvent());
        btnClose =  this.findViewById(R.id.btnClose);
        btnClose.setOnClickListener(new ClickEvent());

        btnClose.setEnabled(false);
        btnSend.setEnabled(false);
        btnSendDraw.setEnabled(false);
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (mService != null)
            mService.stop();
        mService = null;
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        switch (requestCode) {
            case REQUEST_ENABLE_BT:
                if (resultCode == Activity.RESULT_OK) {
                    Toast.makeText(this, "Bluetooth open successful", Toast.LENGTH_LONG).show();
                }
                break;
            case REQUEST_CONNECT_DEVICE:
                if (resultCode == Activity.RESULT_OK) {
                    String address = Objects.requireNonNull(data.getExtras()).getString(DeviceListActivity.EXTRA_DEVICE_ADDRESS);
                    con_dev = mService.getDevByMac(address);
                    mService.connect(con_dev);
                }
                break;
        }
    }

    @SuppressLint("SdCardPath")
    private void printImage() {
        byte[] sendData;
        PrintPic pg = new PrintPic();
        pg.initCanvas(800);
        pg.initPaint();
        pg.drawImage(0, 0, "/mnt/sdcard/print_test.png");
        sendData = pg.printDraw();
        mService.write(sendData);
    }

    class ClickEvent implements View.OnClickListener {
        public void onClick(View v) {
            if (v == btnSearch) {
                Intent serverIntent = new Intent(context, DeviceListActivity2.class);
                startActivityForResult(serverIntent, REQUEST_CONNECT_DEVICE);
            }if (v == btnExit) {
                Intent intent=new Intent(BluetoothPrintSeocndProcessActivity.this, MenuActivity.class);
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(intent);
                finish();
            } else if (v == btnSend) {
                for(int i=0;i<dataList.size();i++) {
                    mService.sendMessage(dataList.get(i), "GBK");
                }
                Intent intent=new Intent(BluetoothPrintSeocndProcessActivity.this, MenuActivity.class);
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(intent);
                finish();
            } else if (v == btnClose) {
                mService.stop();
            } else if (v == btnSendDraw) {
                printImage();
            }
        }
    }
}
