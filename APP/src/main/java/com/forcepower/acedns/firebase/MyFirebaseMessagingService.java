package com.forcepower.acedns.firebase;

import android.app.ActivityManager;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.os.Build;
import androidx.localbroadcastmanager.content.LocalBroadcastManager;
import android.util.Log;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.AppInfo;
import com.forcepower.acedns.bean.NotificationDetails;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.database.DatabaseHelperSqlite;
import com.forcepower.acedns.util.GPSTracker;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.List;
import java.util.Map;

import static com.forcepower.acedns.activity.non_auth.main.MenuActivity.refresh_noti_count;

/**
 * Created by Amit on 28/04/2017.
 */

public class MyFirebaseMessagingService extends FirebaseMessagingService {

    private static final String TAG = MyFirebaseMessagingService.class.getSimpleName();
    AceDnsTransactionDatabase aceDnsTransactionDatabaseOBJ;
    private NotificationUtils notificationUtils;

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {

        if (remoteMessage == null)
            return;

        if (remoteMessage.getData().size() > 0) {
            Map<String, String> dataValues = remoteMessage.getData();
            try {

                handleDataMessage(dataValues);
            } catch (Exception e) {
                Log.e(TAG, "Exception: " + e.getMessage());
            }
        }
    }


    private void handleDataMessage(Map notificationValMap) {

        try {
            String title = "ACEdns";
            String message = String.valueOf(notificationValMap.get("body"));
            String sender_id = String.valueOf(notificationValMap.get("sender_id"));
            String notification_type = String.valueOf(notificationValMap.get("notification_type"));
            String notification_id = String.valueOf(notificationValMap.get("notification_id"));
            Intent showIntent = new Intent(ApplicationContext(), MenuActivity.class);
            notificationUtils = new NotificationUtils(ApplicationContext());
            showIntent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            notificationUtils.showNotificationMessage(title, message, "", showIntent);
            AceDnsDatabase dbObj = new AceDnsDatabase(ApplicationContext());
            Constants.employeeDetailObject = dbObj.getEmployeeObj();
            String timeWhenMsgRead = "";
            Constants.dateString = Constants.employeeDetailObject.getDate().replace("-", "");
            timeWhenMsgRead = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

            aceDnsTransactionDatabaseOBJ = new AceDnsTransactionDatabase(ApplicationContext());
            aceDnsTransactionDatabaseOBJ.insertToLocationTable("PA", timeWhenMsgRead);
            String str_ACK = "PA" + Constants.employeeDetailObject.getEmpCode() + timeWhenMsgRead;
            NotificationDetails modelSEND = new NotificationDetails(notification_id, notification_type, sender_id,message, 0, str_ACK);
            aceDnsTransactionDatabaseOBJ._insertNOTIFICATION(modelSEND);
            Constants.notification_id = notification_id;
            Constants.notification_message = message;
            Constants.notification_sender_id = sender_id;
            Constants.notification_ack_id = str_ACK;
            Constants.notification_type = notification_type;

            showNotificationMessage(ApplicationContext(), title, message, "", showIntent, sender_id, notification_type, notification_id);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private Context ApplicationContext() {
        return getApplicationContext();
    }

    /**
     * Showing notification with text only
     */
    private void showNotificationMessage(final Context context, String title, final String message, String timeStamp, Intent intent, final String str_sender, final String str_type, final String str_notification_id) {

        send_NOTIFICATION(context, title, message, timeStamp, intent, str_sender, str_type, str_notification_id, 0);
//        boolean _app_background = isAppIsInBackground(context);
    }

    public void send_NOTIFICATION(final Context context, String title, final String message, String timeStamp, Intent intent, final String str_sender, final String str_type, final String str_notification_id, int read_Status) {
        new GPSTracker(context);

        AceDnsDatabase dbObj = new AceDnsDatabase(context);
        AppInfo mAppInfoObj = dbObj.getAppInfo();
        if (mAppInfoObj != null) {
            Constants.nickName = mAppInfoObj.getNickName().trim();
            Constants.mDBVersion = mAppInfoObj.getDbVersion();
        }
        refresh_noti_count();
        new GPSTracker(context).stopUsingGPS();
    }

    private boolean isAppIsInBackground(Context context) {
        boolean isInBackground = true;
        ActivityManager am = (ActivityManager) context.getSystemService(Context.ACTIVITY_SERVICE);
        if (Build.VERSION.SDK_INT > Build.VERSION_CODES.KITKAT_WATCH) {
            List<ActivityManager.RunningAppProcessInfo> runningProcesses = am.getRunningAppProcesses();
            for (ActivityManager.RunningAppProcessInfo processInfo : runningProcesses) {
                if (processInfo.importance == ActivityManager.RunningAppProcessInfo.IMPORTANCE_FOREGROUND) {
                    for (String activeProcess : processInfo.pkgList) {
                        if (activeProcess.equals(context.getPackageName())) {
                            isInBackground = false;
                        }
                    }
                }
            }
        } else {
            List<ActivityManager.RunningTaskInfo> taskInfo = am.getRunningTasks(1);
            ComponentName componentInfo = taskInfo.get(0).topActivity;
            if (componentInfo.getPackageName().equals(context.getPackageName())) {
                isInBackground = false;
            }
        }

        return isInBackground;
    }

    @Override
    public void onNewToken(String refreshedToken) {
        Log.d("Refreshed_token", "" + refreshedToken);

        // If you want to send messages to this application instance or
        // manage this apps subscriptions on the server side, send the
        // Instance ID token to your app server.
//        String refreshedToken = FirebaseInstanceId.getInstance().getToken();
        DatabaseHelperSqlite helper = new DatabaseHelperSqlite(this);
        String RegistrationId = helper.getRegistrationId();
        if (RegistrationId.matches("")) {
            helper.addRegistrationIdAndStatus(refreshedToken);
        } else {
            helper.updateRegistrationIdAndStatus(refreshedToken, "yes");
        }
        Constants.Registration_id_firebase = refreshedToken;
        // Saving reg id to shared preferences
//        storeRegIdInPref(refreshedToken);

        // sending reg id to your server
//        sendRegistrationToServer(refreshedToken);

        // Notify UI that registration has completed, so the progress indicator can be hidden.
        Intent registrationComplete = new Intent(Constants.REGISTRATION_COMPLETE);
        registrationComplete.putExtra("token", refreshedToken);
        LocalBroadcastManager.getInstance(this).sendBroadcast(registrationComplete);
    }
}
