package com.forcepower.acedns.activity.non_auth.notification;

import android.app.Activity;
import android.app.ActivityManager;
import android.app.ActivityManager.RunningServiceInfo;
import android.content.Context;
import android.os.Bundle;
import android.text.Html;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.backgroundTask.TRANS_SubmitNotificationTask;
import com.forcepower.acedns.bean.AppInfo;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.ConnectionDetector;
import com.forcepower.acedns.util.GPSTracker;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.util.commonAsyncTaskMaster;

import static com.forcepower.acedns.activity.non_auth.main.MenuActivity.refresh_noti_count;
import static com.forcepower.acedns.constants.Constants.timeVal;

public class NotificationReceiverActivity extends Activity {

    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase dbObj;
    boolean showManagerActivity = false;
    Context mContext;

    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notification_receiver);
        new GPSTracker(NotificationReceiverActivity.this);
        dataHelperObj = new AceDnsTransactionDatabase(NotificationReceiverActivity.this);
        dbObj = new AceDnsDatabase(NotificationReceiverActivity.this);
        mContext = this;
        TextView msg =  findViewById(R.id.txt_msg);
        String message = getIntent().getStringExtra("Message");
        final String str_notification_id = getIntent().getStringExtra("notification_id");
        final String str_ack_id = getIntent().getStringExtra("ack_id");
        showManagerActivity = getIntent().getBooleanExtra("showManagerActivity", false);

        msg.setText(Html.fromHtml(message));

        dataHelperObj._updateNOTIFICATION(str_notification_id);
        dataHelperObj._updateLOCATION(str_ack_id);

        Button btn =  findViewById(R.id.btn);
        btn.setOnClickListener(arg0 -> {
            ConnectionDetector cd = new ConnectionDetector(NotificationReceiverActivity.this);
            if (cd.isConnectingToInternet()) {
                AceDnsDatabase dbObj = new AceDnsDatabase(NotificationReceiverActivity.this);
                AppInfo mAppInfoObj = dbObj.getAppInfo();
                if (mAppInfoObj != null) {
                    Constants.nickName = mAppInfoObj.getNickName().trim();
                    Constants.mDBVersion = mAppInfoObj.getDbVersion();
                }
                Constants.employeeDetailObject = dbObj.getEmployeeObj();

                new TRANS_SubmitNotificationTask(NotificationReceiverActivity.this).execute();

                refresh_noti_count();
                finish();
            } else {
                Toast.makeText(NotificationReceiverActivity.this, "Please connect to the internet", Toast.LENGTH_SHORT).show();
            }
        });

        if (showManagerActivity && HTTPUtils.isConnectionPossible(mContext)) {
            Utils.showProgressDialog(mContext, "Downloading Data Please Wait..");
            new Thread() {
                public void run() {
                    Constants.isOrederToNewCustomer = true;
                    assert message != null;
                    if (message.contains("@")) {
                        timeVal = message.split("@")[1].trim().replace(" ", "");
                        timeVal = timeVal + ":00";
                    } else {
                        timeVal = Utils.getCurrentDateTimeInGivenFormat("HH:mm:ss");
                    }

                    new commonAsyncTaskMaster(mContext, "manager_activity");
                }
            }.start();

        }
    }
}
