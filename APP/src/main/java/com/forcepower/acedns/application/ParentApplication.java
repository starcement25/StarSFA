package com.forcepower.acedns.application;

import android.app.Application;
import android.content.Context;
import androidx.multidex.MultiDex;

/**
 * Created by amit paul on 05/12/2016.
 * it is created to add support of multidex
 */
public class ParentApplication extends Application {
    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(base);
        MultiDex.install(this);
    }
}
