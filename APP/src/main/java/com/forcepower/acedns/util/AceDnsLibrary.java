package com.forcepower.acedns.util;

import android.content.ComponentName;
import android.content.ContentResolver;
import android.content.Context;
import android.content.Intent;
import android.location.LocationListener;
import android.location.LocationManager;
import android.net.ConnectivityManager;
import android.os.Build;
import android.provider.Settings;
import android.text.TextUtils;

import java.lang.reflect.Field;
import java.lang.reflect.Method;

public class AceDnsLibrary {

    public static int turnOnLocationProvider(Context mContext, LocationListener mLocationListner) {
        int status;
        LocationManager locationManager = (LocationManager) mContext
                .getSystemService(Context.LOCATION_SERVICE);
        if (locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER)
                || isWifiLocationEnabled(mContext)) {
            status = 1;
            locationManager
                    .requestLocationUpdates(LocationManager.GPS_PROVIDER,
                            1000L, 1.0f, mLocationListner);
            locationManager.requestLocationUpdates(
                    LocationManager.NETWORK_PROVIDER, 1000L, 1.0f,
                    mLocationListner);
        } else {
            status = 0;
            Intent intent = new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS);
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            mContext.startActivity(intent);
        }
        return status;
    }

    public static boolean turnOnMobileDataEnable(Context mContext,
                                                 boolean enabled) throws Exception {
        boolean mobileDataEnabled = false;
        try {
            final ConnectivityManager conman = (ConnectivityManager) mContext
                    .getSystemService(Context.CONNECTIVITY_SERVICE);
            final Class conmanClass = Class
                    .forName(conman.getClass().getName());
            final Field iConnectivityManagerField = conmanClass
                    .getDeclaredField("mService");
            iConnectivityManagerField.setAccessible(true);
            final Object iConnectivityManager = iConnectivityManagerField
                    .get(conman);
            final Class iConnectivityManagerClass = Class
                    .forName(iConnectivityManager.getClass().getName());
            final Method setMobileDataEnabledMethod = iConnectivityManagerClass
                    .getDeclaredMethod("setMobileDataEnabled", Boolean.TYPE);
            setMobileDataEnabledMethod.setAccessible(true);

            setMobileDataEnabledMethod.invoke(iConnectivityManager, enabled);
        } catch (Exception e) {
            // Attempt 1 failed.
        }

        try {
            ConnectivityManager cm = (ConnectivityManager) mContext
                    .getSystemService(Context.CONNECTIVITY_SERVICE);
            Class cmClass = Class.forName(cm.getClass().getName());
            Method method = cmClass.getDeclaredMethod("getMobileDataEnabled");
            method.setAccessible(true);
            mobileDataEnabled = (Boolean) method.invoke(cm);
            System.out.println(mobileDataEnabled);
        } catch (Exception e) {
            // Attempt 2 failed
        }
        if (mobileDataEnabled == false) {
            try {
                Intent intent = new Intent(
                        Settings.ACTION_DATA_ROAMING_SETTINGS);
                ComponentName cName = new ComponentName("com.android.phone",
                        "com.android.phone.Settings");
                intent.setComponent(cName);
                intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                mContext.startActivity(intent);
            } catch (Exception e) {
                // Attempt 3 failed.
            }
        }
        return mobileDataEnabled;
    }

    public static void turnOnAutomaticDateTime(Context mContext) {
        Settings.System.putInt(mContext.getContentResolver(),
                Settings.System.AUTO_TIME, 1);
    }

    public static int turnOnInstallUnknownResourceAppliation(Context mContext) {
        int status = 0;
        try {
            boolean isNonPlayAppAllowed = Settings.Secure.getInt(
                    mContext.getContentResolver(),
                    Settings.Secure.INSTALL_NON_MARKET_APPS) == 1;
            System.out.println(isNonPlayAppAllowed);
            if (!isNonPlayAppAllowed) {
                if (Integer.parseInt(Build.VERSION.SDK) > 11) {
                    Intent intent = new Intent(
                            Settings.ACTION_SECURITY_SETTINGS);
                    intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                    mContext.startActivity(intent);
                } else {
                    Intent intent = new Intent(
                            Settings.ACTION_APPLICATION_SETTINGS);
                    intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                    mContext.startActivity(intent);
                }
            } else {
                status = 1;
            }
        } catch (Exception e) {
            System.out.println("Error::::::::::" + e.getMessage());
        }
        return status;
    }

    private static boolean isWifiLocationEnabled(Context mContext) {
        ContentResolver cr = mContext.getContentResolver();

        String enabledProviders = Settings.Secure.getString(cr,
                Settings.Secure.LOCATION_PROVIDERS_ALLOWED);
        if (!TextUtils.isEmpty(enabledProviders)) {
            String[] providersList = TextUtils.split(enabledProviders, ",");
            for (String provider : providersList) {
                if (LocationManager.NETWORK_PROVIDER.equals(provider)) {
                    return true;
                }
            }
        }
        return false;
    }

}
