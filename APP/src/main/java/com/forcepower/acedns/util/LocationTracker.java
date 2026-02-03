package com.forcepower.acedns.util;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Context;
import android.content.IntentSender;
import android.location.LocationManager;
import android.os.Build;
import android.provider.Settings;
import androidx.annotation.NonNull;

import com.google.android.gms.common.api.ApiException;
import com.google.android.gms.common.api.ResolvableApiException;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationCallback;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationResult;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.location.LocationSettingsRequest;
import com.google.android.gms.location.LocationSettingsResponse;
import com.google.android.gms.location.LocationSettingsStatusCodes;
import com.google.android.gms.location.SettingsClient;
import com.google.android.gms.tasks.OnFailureListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.gms.tasks.Task;

import static com.forcepower.acedns.constants.Constants.currentLat;
import static com.forcepower.acedns.constants.Constants.currentLong;
import static com.forcepower.acedns.constants.Constants.isGettingCurrentLocation;
import static com.forcepower.acedns.constants.Constants.locationAccuracy;

public class LocationTracker extends LocationCallback {
    LocationCallback mLocationCallback;
    private LocationRequest mLocationRequest;
    private FusedLocationProviderClient FusedLocationProviderClientObject=null;
    public static String transactionType="";
    private long UPDATE_INTERVAL = 500;  /* 1 millisec */
    private long FASTEST_INTERVAL = 500; /* 1 millisec */
    Context context;

    public LocationTracker(Context context,String transactionType) {
        this.context = context;
        this.transactionType = transactionType;
        // Create the location request to start receiving updates
        mLocationRequest = new LocationRequest();
        mLocationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
        mLocationRequest.setInterval(UPDATE_INTERVAL);
        mLocationRequest.setFastestInterval(FASTEST_INTERVAL);
        FusedLocationProviderClientObject=LocationServices.getFusedLocationProviderClient(context);
        mLocationCallback=getLocationCallback();
    }
    public LocationTracker(Context context) {
        this.context = context;
        this.transactionType = transactionType;
        Activity act = (Activity) context;
        // Create the location request to start receiving updates
        mLocationRequest = new LocationRequest();
        mLocationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
        mLocationRequest.setInterval(UPDATE_INTERVAL);
        mLocationRequest.setFastestInterval(FASTEST_INTERVAL);
        FusedLocationProviderClientObject=LocationServices.getFusedLocationProviderClient(context);
        mLocationCallback=getLocationCallback();
    }


    @NonNull
    private LocationCallback getLocationCallback()
    {
        return new LocationCallback() {
            @Override
            public void onLocationResult(@NonNull LocationResult locationResult)
            {
                android.location.Location lastLocation=locationResult.getLastLocation();
                assert lastLocation != null;
                double latitude=lastLocation.getLatitude();
                double longitude=lastLocation.getLongitude();
                double accuracy=lastLocation.getAccuracy();
                if (latitude != 0.0 && longitude != 0.0)
                {
                    isGettingCurrentLocation =true;
                    currentLat = String.valueOf(latitude);
                    currentLong = String.valueOf(longitude);
                    locationAccuracy = String.valueOf(accuracy);

                }

            }
        };
    }

    @SuppressLint("NewApi")
    public boolean isLocationEnabled()
    {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.P)
        {
            // This is new method provided in API 28
            LocationManager lm = (LocationManager) context.getSystemService(Context.LOCATION_SERVICE);
            isGettingCurrentLocation =lm.isLocationEnabled();

        }
        else
        {
            // This is Deprecated in API 28
            int mode = Settings.Secure.getInt(context.getContentResolver(), Settings.Secure.LOCATION_MODE,
                    Settings.Secure.LOCATION_MODE_OFF);
            isGettingCurrentLocation =  (mode != Settings.Secure.LOCATION_MODE_OFF);

        }
        return isGettingCurrentLocation;
    }

    public void checkLocationUpdateSharing()
    {
        isGettingCurrentLocation =false;
        LocationSettingsRequest settingsRequest = new LocationSettingsRequest.Builder()
                .addLocationRequest(mLocationRequest).build();
        SettingsClient client = LocationServices.getSettingsClient(context);
        Task<LocationSettingsResponse> task = client
                .checkLocationSettings(settingsRequest);
        task.addOnSuccessListener((Activity) context, new OnSuccessListener<LocationSettingsResponse>() {
            @Override
            public void onSuccess(LocationSettingsResponse locationSettingsResponse) {
                startLocationUpdates();
            }
        });
        task.addOnFailureListener( new OnFailureListener()
        {
            @Override
            public void onFailure(@NonNull Exception e)
            {
                int statusCode = ((ApiException) e).getStatusCode();
                if (statusCode
                        == LocationSettingsStatusCodes
                        .RESOLUTION_REQUIRED) {
                    // Location settings are not satisfied, but this can
                    // be fixed by showing the user a dialog
                    try {
                        // Show the dialog by calling
                        // startResolutionForResult(), and check the
                        // result in onActivityResult()
                        ResolvableApiException resolvable =
                                (ResolvableApiException) e;
                        resolvable.startResolutionForResult
                                ((Activity) context,
                                        9999);
                    } catch (IntentSender.SendIntentException sendEx) {
                        // Ignore the error
                    }
                }
            }
        });
    }
    @SuppressLint("MissingPermission")
    public void startLocationUpdates()
    {
        if(!isGettingCurrentLocation)
        {
            // Create LocationSettingsRequest object using location request
            LocationSettingsRequest.Builder builder = new LocationSettingsRequest.Builder();
            builder.addLocationRequest(mLocationRequest);
            LocationSettingsRequest locationSettingsRequest = builder.build();

            SettingsClient settingsClient = LocationServices.getSettingsClient(context);
            settingsClient.checkLocationSettings(locationSettingsRequest);

            // new Google API SDK v11 uses getFusedLocationProviderClient(this)
            FusedLocationProviderClientObject.requestLocationUpdates
                    (mLocationRequest, mLocationCallback,
                            null);
        }

    }
    @SuppressLint("MissingPermission")
    public void resumeLocationUpdates()
    {
            // Create LocationSettingsRequest object using location request
            LocationSettingsRequest.Builder builder = new LocationSettingsRequest.Builder();
            builder.addLocationRequest(mLocationRequest);
            LocationSettingsRequest locationSettingsRequest = builder.build();

            SettingsClient settingsClient = LocationServices.getSettingsClient(context);
            settingsClient.checkLocationSettings(locationSettingsRequest);

            // new Google API SDK v11 uses getFusedLocationProviderClient(this)
            FusedLocationProviderClientObject.requestLocationUpdates
                    (mLocationRequest, mLocationCallback,
                            null);

    }
    public void stopLocationUpdates()
    {
        if (FusedLocationProviderClientObject != null && mLocationCallback!=null && isGettingCurrentLocation)
        {
            isGettingCurrentLocation=false;
            currentLat = "";
            currentLong = "";
            locationAccuracy = "";
            FusedLocationProviderClientObject.removeLocationUpdates(mLocationCallback);
        }
    }

}
