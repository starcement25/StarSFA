package com.forcepower.acedns.util;

import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.bluetooth.BluetoothSocket;
import android.content.Context;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;

import com.forcepower.acedns.activity.BluetoothChatActivity;

import java.io.IOException;
import java.lang.reflect.Method;
import java.util.ArrayList;


public class BluetoothChatService {
    // Constants that indicate the current connection state
    public static final int STATE_NONE = 0;       // we're doing nothings
    public static final int STATE_CONNECTING = 2; // now initiating an outgoing connection
    public static final int STATE_CONNECTED = 3;  // now connected to a remote device
    //private Context mContext;
    private static final String TAG = "BluetoothConnectivityService";
    // Member fields
    private final BluetoothAdapter mAdapter;
    BluetoothSocket printerSocket = null;
    BluetoothDevice bluetoothDevice = null;
    Handler mHandler;
    String a1, a2, a3, a4, a5, a6, a7, a8, a9;
    int adapter;
    ArrayList<String> dataList;
    private ConnectThread mConnectThread;
    private ConnectedThread mConnectedThread;
    private int mState;

    public BluetoothChatService(Context context, Handler handler, String b1, String b2, String b3, String b4, String b5, String b6) {
        //mContext = context;
        mAdapter = BluetoothAdapter.getDefaultAdapter();
        mState = STATE_NONE;
        mHandler = handler;
        a1 = b1;
        a2 = b2;
        a3 = b3;
        a4 = b4;
        a5 = b5;
        a6 = b6;
        adapter = 5;
    }

    public BluetoothChatService(Context context, Handler handler, ArrayList<String> dataList) {
        //mContext = context;
        mAdapter = BluetoothAdapter.getDefaultAdapter();
        mState = STATE_NONE;
        mHandler = handler;
        this.dataList = dataList;
        adapter = 2;
    }

    public BluetoothChatService(Context context,
                                Handler mHandler, String b1, String b2, String b3, String b4,
                                String b5, String b6, String b7, String b8, String b9) {
        // TODO Auto-generated constructor stub
        //mContext = context;
        mAdapter = BluetoothAdapter.getDefaultAdapter();
        mState = STATE_NONE;
        this.mHandler = mHandler;
        a1 = b1;
        a2 = b2;
        a3 = b3;
        a4 = b4;
        a5 = b5;
        a6 = b6;
        a7 = b7;
        a8 = b8;
        a9 = b9;
        adapter = 9;
    }

    public synchronized int getState() {
        return mState;
    }

    private synchronized void setState(int state) {
        mState = state;
    }

    public synchronized void connect(BluetoothDevice device) {
        // Cancel any thread attempting to make a connection
        if (mState == STATE_CONNECTING) {
            if (mConnectThread != null) {
                mConnectThread.cancel();
                mConnectThread = null;
            }
        }

        // Cancel any thread currently running a connection
        if (mConnectedThread != null) {
            mConnectedThread.cancel();
            mConnectedThread = null;
        }

        // Start the thread to connect with the given device
        mConnectThread = new ConnectThread(device);
        mConnectThread.start();
        setState(STATE_CONNECTING);
    }

    public synchronized void connected(BluetoothSocket socket, BluetoothDevice device) {
        printerSocket = socket;
        bluetoothDevice = device;
        // Cancel the thread that completed the connection
        if (mConnectThread != null) {
            mConnectThread.cancel();
            mConnectThread = null;
        }

        // Cancel any thread currently running a connection
        if (mConnectedThread != null) {
            mConnectedThread.cancel();
            mConnectedThread = null;
        }
        // Start the thread to manage the connection and perform transmissions
        if (printerSocket == null) {
            System.out.println("Socket is null in the enrollprinter");
        } else {
            System.out.println("Socket is active");
        }
        mConnectedThread = new ConnectedThread(this.printerSocket, this.bluetoothDevice, this);
        //mConnectedThread.start();
        int i;

        if (adapter == 9) {
            i = mConnectedThread.readImagedata(a1, a2, a3, a4, a5, a6, a7, a8, a9);
        } else if (adapter == 5) {
            i = mConnectedThread.readImagedata(a1, a2, a3, a4, a5, a6);
        } else {
            i = mConnectedThread.readImagedata(dataList);
        }

        if (i == 1) {
            unSuccessfullPrint();
        }
        setState(STATE_CONNECTED);
    }

    public void unSuccessfullPrint() {
        Message msg = mHandler.obtainMessage(BluetoothChatActivity.MESSAGE_TOAST);
        Bundle bundle = new Bundle();
        bundle.putString(BluetoothChatActivity.TOAST, "Unsuccessfull Printing, Please try again");
        msg.setData(bundle);
        mHandler.sendMessage(msg);
    }

    // public void readImage(){mConnectedThread.readImagedata();}

    public synchronized void stop() {
        if (mConnectThread != null) {
            mConnectThread.cancel();
            mConnectThread = null;
        }
        if (mConnectedThread != null) {
            mConnectedThread.cancel();
            mConnectedThread = null;
        }
        setState(STATE_NONE);
    }


    private void connectionFailed() {
        // Send a failure message back to the Activity
        Message msg = mHandler.obtainMessage(BluetoothChatActivity.MESSAGE_TOAST);
        Bundle bundle = new Bundle();
        bundle.putString(BluetoothChatActivity.TOAST, "Unable to connect device");
        msg.setData(bundle);
        mHandler.sendMessage(msg);
    }

    /**
     * Indicate that the connection was lost and notify the UI Activity.
     */
    @SuppressWarnings("unused")
    private void connectionLost() {
        // Send a failure message back to the Activity
        Message msg = mHandler.obtainMessage(BluetoothChatActivity.MESSAGE_TOAST);
        Bundle bundle = new Bundle();
        bundle.putString(BluetoothChatActivity.TOAST, "Device connection was lost");
        msg.setData(bundle);
        mHandler.sendMessage(msg);

    }

    private class ConnectThread extends Thread {
        private final BluetoothSocket mmSocket;
        private final BluetoothDevice mmDevice;

        public ConnectThread(BluetoothDevice device) {
            mmDevice = device;
            BluetoothSocket tmp = null;
            try {
                Method m = null;
                try {
                    m = device.getClass().getMethod("createRfcommSocket", new Class[]{int.class});
                } catch (SecurityException e) {
                    e.printStackTrace();
                } catch (NoSuchMethodException e) {
                    e.printStackTrace();
                }
                tmp = (BluetoothSocket) m.invoke(device, 1);

            } catch (Exception e) {
                e.printStackTrace();
            }
            mmSocket = tmp;
        }

        public void run() {
            Log.i(TAG, "BEGIN mConnectThread");
            setName("ConnectThread");
            mAdapter.cancelDiscovery();
            try {
                mmSocket.connect();
            } catch (IOException e) {
                connectionFailed();
                e.printStackTrace();
                Log.e(this.toString(), "IOException " + e.getMessage());
                try {
                    mmSocket.close();
                } catch (IOException e2) {
                    Log.e(TAG, "unable to close() socket during connection failure", e2);
                }
                return;
            }
            synchronized (BluetoothChatService.this) {
                mConnectThread = null;
            }
            connected(mmSocket, mmDevice);
        }

        public void cancel() {
            try {
                mmSocket.close();
                Log.e(TAG, "Socket is closed");
            } catch (IOException e) {
                Log.e(TAG, "close() of connect socket failed", e);
            }
        }
    }
}
