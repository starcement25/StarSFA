package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.content.Context;
import android.content.Intent;
import android.net.nsd.NsdManager;
import android.net.nsd.NsdServiceInfo;
import android.os.Bundle;
import android.os.CancellationSignal;
import android.os.Handler;
import android.os.Message;
import android.os.ParcelFileDescriptor;
import android.print.PageRange;
import android.print.PrintAttributes;
import android.print.PrintDocumentAdapter;
import android.print.PrintDocumentInfo;
import android.print.PrintManager;
import android.view.MotionEvent;
import android.view.Window;
import android.view.WindowManager.LayoutParams;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.BluetoothChatService;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.InetAddress;
import java.text.DecimalFormat;
import java.util.ArrayList;
import java.util.Objects;

public class BluetoothChatActivity extends AceDnsParentActivity {

    public static final int MESSAGE_WRITE = 3;
    public static final int MESSAGE_TOAST = 5;
    public static final String TOAST = "toast";
    public static final String SERVICE_TYPE = "_ipp._tcp.";
    // Intent request codes
    private static final int REQUEST_CONNECT_DEVICE_SECURE = 1;
    private static final int REQUEST_CONNECT_DEVICE_INSECURE = 2;
    private static final int REQUEST_ENABLE_BT = 3;
    @SuppressLint("HandlerLeak")
    private final Handler mHandler = new Handler() {
        @Override
        public void handleMessage(Message msg) {
            switch (msg.what) {
                case MESSAGE_WRITE:
                    break;
                case MESSAGE_TOAST:
                    Toast.makeText(getApplicationContext(), msg.getData().getString(TOAST), Toast.LENGTH_SHORT).show();
                    break;
            }
        }
    };
    AceDnsTransactionDatabase dbHelper;
    String b1, b2, b3, b4, b5, b6, b7, b8, b9;
    String printFor = "", customerName = "", totalAmount = "", invoiceNumber = "",
            date = "", bankname = "", paymentType = "", checkNo = "", chkDate = "";
    DecimalFormat defaultFormat = new DecimalFormat("#.00");
    ArrayList<String> dataList;
    Double grandTotal = 0.00;
    String blueToothOrWlan = "", finalPrintStringSalesBill = "";
    Context mContext;
    NsdManager.DiscoveryListener mDiscoveryListener;
    NsdManager mNsdManager;
    NsdManager.ResolveListener mResolveListener;
    PrintDocumentAdapter pda = new PrintDocumentAdapter() {
        @SuppressLint("NewApi")
        @Override
        public void onLayout(PrintAttributes printAttributes, PrintAttributes printAttributes1, CancellationSignal cancellationSignal, LayoutResultCallback callback, Bundle bundle) {
            if (cancellationSignal.isCanceled()) {
                callback.onLayoutCancelled();
                return;
            }
            PrintDocumentInfo pdi = new PrintDocumentInfo.Builder("rkbk sales bill").setContentType(PrintDocumentInfo.CONTENT_TYPE_DOCUMENT).build();
            callback.onLayoutFinished(pdi, true);
        }

        @SuppressLint("NewApi")
        @Override
        public void onWrite(PageRange[] pageRanges, ParcelFileDescriptor destination, CancellationSignal cancellationSignal, WriteResultCallback callback) {
            InputStream input = null;
            OutputStream output = null;
            try {
                File myFile = new File(Utils.getAppStoragePath(mContext) + "/invoice/" + finalPrintStringSalesBill.replace("/", "_") + ".pdf");
                input = new FileInputStream(myFile);
                output = new FileOutputStream(destination.getFileDescriptor());
                byte[] buf = new byte[1024];
                int bytesRead;
                while ((bytesRead = input.read(buf)) > 0) {
                    output.write(buf, 0, bytesRead);
                }
                callback.onWriteFinished(new PageRange[]{PageRange.ALL_PAGES});
            } catch (Exception ignored) {
            } finally {
                try {
                    input.close();
                    output.close();
                } catch (IOException ignored) {
                }
            }
        }
    };
    private BluetoothAdapter mBluetoothAdapter = null;
    private BluetoothChatService mChatService = null;
    private Button printBtn = null;

    @SuppressLint("SetTextI18n")
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        getWindow().setFlags(LayoutParams.FLAG_NOT_TOUCH_MODAL, LayoutParams.FLAG_NOT_TOUCH_MODAL);
        getWindow().setFlags(LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH, LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH);
        setContentView(R.layout.main_blutoothprint);
        mContext = this;
        dbHelper = new AceDnsTransactionDatabase(BluetoothChatActivity.this);

        blueToothOrWlan = Constants.orderFormDetailsObj.getPrintMedium();

        Button back = findViewById(R.id.btnExit);
        back.setOnClickListener(v -> {
            startActivity(new Intent(BluetoothChatActivity.this, MenuActivity.class));
            finish();
        });

        Bundle bundle;
        bundle = this.getIntent().getExtras();

        assert bundle != null;
        printFor = bundle.getString("PrintFor");
        if (blueToothOrWlan.equalsIgnoreCase("bluetooth")) {
            assert printFor != null;
            if (printFor.equalsIgnoreCase("ORDER")) {
                grandTotal = 0.00;
                customerName = bundle.getString("CustomerName");
                dataList = new ArrayList<>();
                dataList.add("Bill # " + Constants.employeeDetailObject.getEmpCode() + "-" + (dbHelper.getOrderCountForPrint()));
                for (int i = 0; i < Constants.selectedProductMasterList.size(); i++) {
                    String str = formatSerialNo("" + (i + 1)) +
                            formatProdName(Constants.selectedProductMasterList.get(i).getDesc()) +
                            " " + formatQty(Constants.selectedProductMasterList.get(i).getQty()) +
                            " " + formatAmt(Constants.selectedProductMasterList.get(i).getMrpValue()) +
                            " " + formatTD(Constants.selectedProductMasterList.get(i).getTradeDiscnt()) +
                            " " + formatTotal(calculateTotal(Constants.selectedProductMasterList.get(i)));
                    dataList.add(str);
                }
                String mTotal = defaultFormat.format(grandTotal);
                dataList.add(formatGrandTotal("Total :  " + mTotal));
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

            mBluetoothAdapter = BluetoothAdapter.getDefaultAdapter();
            if (mBluetoothAdapter == null) {
                Toast.makeText(this, "Bluetooth is not available", Toast.LENGTH_LONG).show();
                finish();
                return;
            }
        } else {
            finalPrintStringSalesBill = bundle.getString("wlanPrintDataSalesBill");
            mNsdManager = (NsdManager) mContext.getSystemService(NSD_SERVICE);
            initializeResolveListener();
            initializeDiscoveryListener();
            mNsdManager.discoverServices(SERVICE_TYPE, NsdManager.PROTOCOL_DNS_SD, mDiscoveryListener);
        }

        printBtn = findViewById(R.id.btnPrint);
        printBtn.setOnClickListener(v -> {
            printBtn.setText("Print again");
            if (blueToothOrWlan.equalsIgnoreCase("bluetooth")) {
                if (mChatService == null) setupChat();
                Intent serverIntent;
                serverIntent = new Intent(getBaseContext(), DeviceListActivity.class);
                startActivityForResult(serverIntent, REQUEST_CONNECT_DEVICE_INSECURE);
            } else {
                doPrintJob();
            }
        });
    }

    @Override
    public void onStart() {
        super.onStart();
        if (blueToothOrWlan.equalsIgnoreCase("bluetooth")) {
            if (!mBluetoothAdapter.isEnabled()) {
                Intent enableIntent = new Intent(BluetoothAdapter.ACTION_REQUEST_ENABLE);
                startActivityForResult(enableIntent, REQUEST_ENABLE_BT);
            } else {

                if (mChatService == null)
                    setupChat();
            }
        }

    }

    private void setupChat() {
        // Initialize the array RoutePlanAdapter for the conversation thread
        ArrayAdapter<String> mConversationArrayAdapter = new ArrayAdapter<>(this, R.layout.message);
        ListView mConversationView = findViewById(R.id.in);
        mConversationView.setAdapter(mConversationArrayAdapter);
        // Initialize the BluetoothChatService to perform bluetooth connections
        if (paymentType.equals("Cheque")) {
            mChatService = new BluetoothChatService(this, mHandler, b1, b2, b3, b4, b5, b6, b7, b8, b9);
        } else if (printFor.equalsIgnoreCase("ORDER")) {
            mChatService = new BluetoothChatService(this, mHandler, dataList);
        } else {
            mChatService = new BluetoothChatService(this, mHandler, b1, b2, b3, b4, b5, b6);
        }
    }

    @Override
    public void onDestroy() {
        super.onDestroy();

        if (mChatService != null)
            mChatService.stop();
    }

    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        switch (requestCode) {
            case REQUEST_CONNECT_DEVICE_SECURE, REQUEST_CONNECT_DEVICE_INSECURE:
                // When DeviceListActivity returns with a device to connect
                if (resultCode == RESULT_OK) {
                    connectDevice(data);
                }
                break;

            case REQUEST_ENABLE_BT:
                // When the request to enable Bluetooth returns
                if (resultCode == RESULT_OK) {
                    // Bluetooth is now enabled, so set up a chat session
                    setupChat();
                } else {
                    Toast.makeText(this, "Bluetooth not enabled", Toast.LENGTH_SHORT).show();
                    finish();
                }
        }
    }

    private void connectDevice(Intent data) {
        String address = Objects.requireNonNull(data.getExtras()).getString(DeviceListActivity.EXTRA_DEVICE_ADDRESS);
        Toast.makeText(this, "Device connecting " + address, Toast.LENGTH_SHORT).show();
        BluetoothDevice device = mBluetoothAdapter.getRemoteDevice(address);
        mChatService.connect(device);
        setupChat();
    }

    public static String formatSerialNo(String slNo) {
        //SerialNo will be 2 charac
        String frmtdSrl = "";
        StringBuilder sb = new StringBuilder();
        if (slNo.length() < 3) {
            for (int ii = 1; ii < (3 - slNo.length()); ii++) {
                sb.append(" ");
            }
            sb.append(slNo);
        } else {
            sb.append(slNo.substring(0, 3));
            sb.append("..");
        }
        frmtdSrl = sb.toString();
        return frmtdSrl;
    }

    public static String formatProdName(String name) {
        //Name can will be 40 charac with ^^^ at end
        String frmtdSrl = "";
        StringBuilder sb = new StringBuilder();
        sb.append(" ");
        if (name.length() < 40) {
            sb.append(name);
            for (int ii = 1; ii < (39 - name.length()); ii++) {
                sb.append(" ");
            }
            sb.append("^");
        } else {
            sb.append(name.substring(0, 37));
            sb.append("..");
            sb.append("^");
        }
        frmtdSrl = sb.toString();
        return frmtdSrl;
    }

    public static String formatQty(String qty) {
        StringBuilder sb = new StringBuilder();
        //Spaces to be added before Qty
        for (int ii = 0; ii < 9; ii++) {
            sb.append(" ");
        }
        //Qty can will be 5 charac
        String frmtdSrl = "";
        sb.append(" ");
        if (qty.length() < 5) {
            for (int ii = 1; ii < (5 - qty.length()); ii++) {
                sb.append(" ");
            }
            sb.append(qty);
        } else {
            sb.append(qty.substring(0, 3));
            sb.append((".."));
        }
        frmtdSrl = sb.toString();
        return frmtdSrl;
        //return qty;
    }

    public static String formatAmt(String amt) {
        //Qty can will be 8 charac
        String frmtdSrl = "";
        StringBuilder sb = new StringBuilder();
        sb.append(" ");
        if (amt.length() < 8) {
            for (int ii = 1; ii < (8 - amt.length()); ii++) {
                sb.append(" ");
            }
            sb.append(amt);
        } else {
            sb.append(amt.substring(0, 6));
            sb.append((".."));
        }
        frmtdSrl = sb.toString();
        return frmtdSrl;
    }

    public static String formatTD(String amt) {
        //Qty can will be 5 charac
        String frmtdSrl = "";
        StringBuilder sb = new StringBuilder();
        sb.append(" ");
        if (amt.length() < 5) {
            for (int ii = 1; ii < (5 - amt.length()); ii++) {
                sb.append(" ");
            }
            sb.append(amt);
        } else {
            sb.append(amt.substring(0, 3));
            sb.append((".."));
        }
        frmtdSrl = sb.toString();
        return frmtdSrl;
    }

    public static String formatTotal(String total) {
        //Qty can will be 10 charac
        String frmtdSrl = "";
        StringBuilder sb = new StringBuilder();
        sb.append("  ");
        if (total.length() < 10) {
            for (int ii = 1; ii < (9 - total.length()); ii++) {
                sb.append(" ");
            }
            sb.append(total);
        } else {
            sb.append(total.substring(0, 8));
            sb.append((".."));
        }
        frmtdSrl = sb.toString();
        return frmtdSrl;
    }

    public static String formatGrandTotal(String total) {
        //Qty can will be 42 characwith ^ at end
        String frmtdSrl = "";
        StringBuilder sb = new StringBuilder();
        sb.append("  ");
        if (total.length() < 40) {
            for (int ii = 1; ii < (40 - total.length()); ii++) {
                sb.append(" ");
            }
            sb.append(total);
            sb.append("^ ");
        } else {
            sb.append(total.substring(0, 38));
            sb.append((".."));
            sb.append("^ ");
        }
        frmtdSrl = sb.toString();
        return frmtdSrl;
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
    public boolean onTouchEvent(MotionEvent event) {
        if (MotionEvent.ACTION_OUTSIDE == event.getAction()) {
            return true;
        }
        return super.onTouchEvent(event);
    }

    public void initializeDiscoveryListener() {
        mDiscoveryListener = new NsdManager.DiscoveryListener() {
            @Override
            public void onDiscoveryStarted(String regType) {
            }

            @Override
            public void onServiceFound(NsdServiceInfo service) {
                if (service.getServiceType().equals(SERVICE_TYPE)) {
                    mNsdManager.resolveService(service, mResolveListener);
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

    public void initializeResolveListener() {
        mResolveListener = new NsdManager.ResolveListener() {
            @Override
            public void onResolveFailed(NsdServiceInfo serviceInfo, int errorCode) {
            }

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

    @SuppressLint("NewApi")
    public void doPrintJob() {
        PrintManager printManager = (PrintManager) getSystemService(PRINT_SERVICE);
        String jobName = this.getString(R.string.app_name) + " Document";
        printManager.print(jobName, pda, null);
    }
}
