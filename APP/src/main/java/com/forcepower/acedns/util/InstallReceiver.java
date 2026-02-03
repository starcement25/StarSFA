package com.forcepower.acedns.util;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

import com.forcepower.acedns.backgroundTask.DATA_EmailToDeveloperTask;

public class InstallReceiver extends BroadcastReceiver {

    @Override
    public void onReceive(Context context, Intent intent) {
        new DATA_EmailToDeveloperTask(context).execute();
    }
}