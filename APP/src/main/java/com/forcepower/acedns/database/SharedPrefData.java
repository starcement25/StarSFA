package com.forcepower.acedns.database;

import android.content.Context;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;

public class SharedPrefData {
    static final String KEY_SET_firebase_token = "KEY_SET_firebase_token";
    public static SharedPreferences getSharedPreferences(final Context ctx) {
        return PreferenceManager.getDefaultSharedPreferences(ctx);
    }

    public static void set_firebase_token(final Context ctx, final String firebase_token) {
        final SharedPreferences.Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_firebase_token, firebase_token);
        editor.apply();
    }

    public static String get_firebase_token(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_firebase_token, "dummy");
    }
}
