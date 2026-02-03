package com.forcepower.acedns.util;

import android.app.Activity;

import java.util.Vector;

public final class RegisterActivities {

    public static Vector<Activity> activityStore = new Vector<Activity>();

    public RegisterActivities() {

    }

    public static void registerActivity(Activity context) {
        activityStore.addElement(context);
    }

    public static Vector<Activity> getAllAcivities() {
        return activityStore;
    }

    @SuppressWarnings("deprecation")
    public static void removeAllActivities() {
        for (int ii = 0; ii < activityStore.size(); ii++) {
            (activityStore.elementAt(ii)).finish();
        }
        System.runFinalizersOnExit(true);
        activityStore.removeAllElements();
    }

}
