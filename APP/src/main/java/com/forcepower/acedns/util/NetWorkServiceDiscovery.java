package com.forcepower.acedns.util;

import android.annotation.TargetApi;
import android.content.Context;
import android.net.nsd.NsdManager;
import android.net.nsd.NsdServiceInfo;
import android.os.Build;

import com.forcepower.acedns.constants.Constants;

import java.net.InetAddress;

/**
 * Created by amit on 06/01/2017.
 * used for getting printer IP from network
 */


public class NetWorkServiceDiscovery {
    //        public static final String SERVICE_TYPE = "_http._tcp.";
    public static final String SERVICE_TYPE = "_ipp._tcp.";//for printer
    NsdManager.DiscoveryListener mDiscoveryListener;
    NsdManager mNsdManager;
    NsdManager.ResolveListener mResolveListener;

    public NetWorkServiceDiscovery(Context context) {
        mNsdManager = (NsdManager) context.getSystemService(Context.NSD_SERVICE);
        initializeResolveListener();
        initializeDiscoveryListener();
    }

    @TargetApi(Build.VERSION_CODES.JELLY_BEAN)
    public void discoverAndResolveService() {
        mNsdManager.discoverServices(
                SERVICE_TYPE, NsdManager.PROTOCOL_DNS_SD, mDiscoveryListener);
    }

    @TargetApi(Build.VERSION_CODES.JELLY_BEAN)
    public void StopServiceDiscovery() {
        if (mNsdManager != null) {
            mNsdManager.stopServiceDiscovery(mDiscoveryListener);
        }

    }


    @TargetApi(Build.VERSION_CODES.JELLY_BEAN)
    public void initializeDiscoveryListener() {

        // Instantiate a new DiscoveryListener
        mDiscoveryListener = new NsdManager.DiscoveryListener() {

            //  Called as soon as service discovery begins.
            @Override
            public void onDiscoveryStarted(String regType) {

            }

            @Override
            public void onServiceFound(NsdServiceInfo service) {
                if (!service.getServiceType().equals(SERVICE_TYPE)) {
//                    Log.d(TAG, "Unknown Service Type: " + service.getServiceType());
                } else if (service.getServiceName().equals(Constants.currentPrinterIp)) {
//                    Log.d(TAG, "Same machine: " + mServiceName);
                } else {
                    mNsdManager.resolveService(service, mResolveListener);
                }
//                mNsdManager.resolveService(service, mResolveListener);
            }

            @Override
            public void onServiceLost(NsdServiceInfo service) {

            }

            @Override
            public void onDiscoveryStopped(String serviceType) {

            }


            @Override
            public void onStartDiscoveryFailed(String serviceType, int errorCode) {

            }

            @Override
            public void onStopDiscoveryFailed(String serviceType, int errorCode) {

            }
        };
    }


    @TargetApi(Build.VERSION_CODES.JELLY_BEAN)
    public void initializeResolveListener() {
        mResolveListener = new NsdManager.ResolveListener() {

            @Override
            public void onResolveFailed(NsdServiceInfo serviceInfo, int errorCode) {
            }

            @Override
            public void onServiceResolved(NsdServiceInfo serviceInfo) {
//                String serviceType=serviceInfo.getServiceType();
//                String serviceName=serviceInfo.getServiceName();
                InetAddress serviceIp = serviceInfo.getHost();
//                int serviceport=serviceInfo.getPort();
                if (!Constants.currentPrinterIp.equals(serviceIp)) {
                    Constants.currentPrinterIp = String.valueOf(serviceIp);
                }

//                Log.d("", "serviceType: "+serviceType );
//                Log.d("", "serviceName: "+serviceName );
//                Log.d("", "serviceIp: "+serviceIp );
//                Log.d("", "serviceport: "+serviceport );
//
            }
        };
    }
}
