package com.forcepower.acedns.api.clients;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.Network;
import android.net.NetworkCapabilities;
import android.os.Build;

import androidx.annotation.RequiresApi;

public class NetworkUtil {
    @RequiresApi(api = Build.VERSION_CODES.M)
    public static boolean isNetworkAvailable(Context context) {

        ConnectivityManager connectivityManager =
                (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);

        if (connectivityManager != null) {

            Network network = null;
            if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.M) {
                network = connectivityManager.getActiveNetwork();
            }
            if (network == null) return false;

            NetworkCapabilities capabilities =
                    connectivityManager.getNetworkCapabilities(network);

            return capabilities != null &&
                    capabilities.hasCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET) &&
                    capabilities.hasCapability(NetworkCapabilities.NET_CAPABILITY_VALIDATED);
        }

        return false;
    }
}
