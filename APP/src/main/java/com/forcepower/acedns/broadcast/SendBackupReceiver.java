package com.forcepower.acedns.broadcast;

import static com.forcepower.acedns.constants.Constants.dateString;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.backgroundTask.DATA_EmailToDeveloperTask;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.Utils;

import java.text.SimpleDateFormat;
import java.util.Calendar;

public class SendBackupReceiver extends BroadcastReceiver {
    private static final String TAG = "SendBackupReceiver";
    private static final String RESULT_RECEIVER_PACKAGE = "com.example.filedeleteforsfa";
    @Override
    public void onReceive(Context context, Intent intent) {
        if (intent != null) {
            Log.d(TAG, "Action 1111111: " + intent.getAction());
            if(intent.getAction().equalsIgnoreCase("com.forcepower.acedns.ACTION_SEND_BACKUP_FILES")){
                sendBackup(context);
            }
        }
    }

    public void sendBackup(Context context){
        ConnectionDetector cd = new ConnectionDetector(context);
        AceDnsDatabase mAceDnsDatabase = new AceDnsDatabase(context);
        if (cd.isConnectingToInternet()) {
            Constants.isDataRefreshed = true;
            String libraryStatus = Utils.checkLibraryConditions(context);
            if (libraryStatus.equalsIgnoreCase("ALL OKK")) {
                new DATA_EmailToDeveloperTask(context, false, Constants.employeeDetailObject.getEmpCode(), "", Constants.employeeDetailObject.getEmpName(), true).execute();
                String timeStamp = dateString + new SimpleDateFormat("_HHmmss").format(Calendar.getInstance().getTime());
                mAceDnsDatabase.insertToLogTable(timeStamp, "data_refresh");
                sendResultBroadcast(context,"Successfully send your data backup to Admin","1");
            } else {
                sendResultBroadcast(context,"Sorry local db can not send for data backup","0");
            }
        } else {
            sendResultBroadcast(context,"Sorry network error.\nCheck your internet.","0");
        }
    }

    private void sendResultBroadcast(Context context, String message, String status) {
        try {
            Intent resultIntent = new Intent("com.example.filedeleteforsfa.ACTION_DATA_BACKUP_RESULT");
            resultIntent.setPackage(RESULT_RECEIVER_PACKAGE);
            resultIntent.putExtra("type", "DATA_BACKUP");
            resultIntent.putExtra("message", message);
            resultIntent.putExtra("Status", status);
            context.sendBroadcast(resultIntent);
        } catch (Exception ignored) { }
    }
}