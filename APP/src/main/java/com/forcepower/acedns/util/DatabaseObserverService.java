package com.forcepower.acedns.util;

import android.app.Service;
import android.content.Intent;
import android.os.IBinder;
import android.util.Log;

public class DatabaseObserverService extends Service {

    DatabaseDeleteObserver obsrvr;

    @Override
    public IBinder onBind(Intent arg0) {
        return null;
    }

    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        Log.e("SERVICE STATUS", "STARTED");
        obsrvr = new DatabaseDeleteObserver(this);
        obsrvr.startWatching();
        return START_STICKY;
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        Log.e("SERVICE STATUS", "STARTED");
        obsrvr.stopWatching();
    }


}