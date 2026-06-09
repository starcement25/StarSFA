package com.forcepower.acedns.broadcast;

import android.annotation.SuppressLint;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.util.Log;

import androidx.annotation.NonNull;

import com.forcepower.acedns.backgroundTask.DATA_LoadDatabaseDetails;
import com.forcepower.acedns.backgroundTask.DATA_LoadDatabaseDetails2;
import com.forcepower.acedns.backgroundTask.TRANS_CheckOutTask;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitFeedBack_BackUp;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.ConnectionDetector;

public class SyncFilesReceiver extends BroadcastReceiver {
    private static final String TAG = "SyncFilesReceiver";
    private static final String RESULT_RECEIVER_PACKAGE = "com.example.filedeleteforsfa";

    @Override
    public void onReceive(Context context, Intent intent) {
        if (intent == null) return;
        String action = intent.getAction();
        Log.d(TAG, "Received Action: " + action);
        if ("com.forcepower.acedns.ACTION_SYNC_FILES".equals(action)) {
            final PendingResult pendingResult = goAsync();
            new Thread(() -> {
                try {
                    ConnectionDetector cd = new ConnectionDetector(context);
                    if (cd.isConnectingToInternet()) {
                        PendingingDataUploadFeedback(context, "syncpending");
                    } else {
                        sendResultBroadcast(context, "Sorry network error.\nCheck your internet.", "0");
                    }
                } catch (Exception e) {
                    sendResultBroadcast(context, "Sync failed.", "0");
                } finally {
                    pendingResult.finish();
                }
            }).start();
        }
    }

    @SuppressLint("HandlerLeak")
    public void PendingingDataUploadFeedback(Context context, final String pendingtype) {
        Handler mHandlerPending =
                new Handler(Looper.getMainLooper()) {
                    @Override
                    public void handleMessage(@NonNull Message msg) {
                        String response = msg.getData().getString("message");

                        if (response == null) return;
                        try {
                            if (response.equalsIgnoreCase("checkoutpending")) {
                                new TRANS_CheckOutTask(context, false).execute();
                            }
                            if (response.equalsIgnoreCase("syncpending")) {
                                Constants.dataResfresh = true;
                                // START MAIN SYNC
                                new DATA_LoadDatabaseDetails2(context).execute(Constants.employeeDetailObject.getEmpCode());
                                // WAIT FOR SYNC COMPLETE
                                new Handler(Looper.getMainLooper()).postDelayed(() -> {
                                    sendResultBroadcast(context, "Sync completed successfully.", "1");
                                }, 30000);
                            }
                        } catch (Exception e) {
                            sendResultBroadcast(context, "Sync failed.", "0");
                        }
                    }
                };
        new Thread(() -> {
            try {
                if (Constants.menuDetailsObj != null && Constants.menuDetailsObj.getFeedback_backup() != null && Constants.menuDetailsObj.getFeedback_backup().length() > 8) {
                    TRANS_SubmitFeedBack_BackUp sbs = new TRANS_SubmitFeedBack_BackUp(context, true, "SYNC");
                    sbs.execute();
                }
                Message msgObj = mHandlerPending.obtainMessage();
                Bundle b = new Bundle();
                b.putString("message", pendingtype);
                msgObj.setData(b);
                mHandlerPending.sendMessage(msgObj);
            } catch (Exception e) {
                sendResultBroadcast(context, "Sync failed.", "0");
            }
        }).start();
    }

    // SEND RESULT BACK TO OTHER APP
    private void sendResultBroadcast(Context context, String message, String status) {
        try {
            Intent resultIntent = new Intent("com.example.filedeleteforsfa.ACTION_SYNC_RESULT");
            resultIntent.setPackage(RESULT_RECEIVER_PACKAGE);
            resultIntent.putExtra("type", "SYNC");
            resultIntent.putExtra("message", message);
            resultIntent.putExtra("Status", status);
            context.sendBroadcast(resultIntent);
            Log.d(TAG, "Result Sent -> " + message + " | Status: " + status);
        } catch (Exception e) {
            Log.e(TAG, "Broadcast Send Error: " + e.getMessage());
        }
    }
}