package com.forcepower.acedns.util;

import android.Manifest;
import android.app.Activity;
import android.content.Context;
import android.content.pm.PackageManager;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import com.forcepower.acedns.constants.Constants;

public class RuntimePermissionChecking {
    Context context;

    public RuntimePermissionChecking(Context context) {
        this.context = context;
    }

    public boolean isPermissionGiven(String permissionType) {
        if (permissionType.matches(Constants.LocationPermissionString)) {
            permissionType = Manifest.permission.ACCESS_FINE_LOCATION;
        } else if (permissionType.matches(Constants.StorageWritePermissionString)) {
            permissionType = Manifest.permission.WRITE_EXTERNAL_STORAGE;
        } else if (permissionType.matches(Constants.StorageReadPermissionString)) {
            permissionType = Manifest.permission.READ_EXTERNAL_STORAGE;
        } else if (permissionType.matches((Constants.CameraPermissionString))) {
            permissionType = Manifest.permission.CAMERA;
        } else if (permissionType.matches(Constants.PhonePermissionString)) {
            permissionType = Manifest.permission.READ_PHONE_STATE;
        } else if (permissionType.matches(Constants.ContactPermissionString)) {
            permissionType = Manifest.permission.READ_CONTACTS;
        }
        int result = ContextCompat.checkSelfPermission(context, permissionType);

        if (result == PackageManager.PERMISSION_GRANTED)
            return true;

        return false;
    }
}
