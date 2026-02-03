package com.google.zxing.integration.android;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.ActivityNotFoundException;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.pm.ResolveInfo;
import android.net.Uri;
import android.os.Bundle;
import android.util.Log;

import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class IntentIntegrator {

    public static final int REQUEST_CODE = 0x0000c0de; // Only use bottom 16 bits
    public static final String DEFAULT_TITLE = "Install Barcode Scanner?";
    public static final String DEFAULT_MESSAGE =
            "This application requires Barcode Scanner. Would you like to install it?";
    public static final String DEFAULT_YES = "Yes";
    public static final String DEFAULT_NO = "No";
    public static final Collection<String> ALL_CODE_TYPES = null;
    private static final String TAG = IntentIntegrator.class.getSimpleName();
    private static final String BS_PACKAGE = "com.google.zxing.client.android";
    private static final String BSPLUS_PACKAGE = "com.srowen.bs.android";
    public static final List<String> TARGET_ALL_KNOWN = list(
            BSPLUS_PACKAGE,             // Barcode Scanner+
            BSPLUS_PACKAGE + ".simple", // Barcode Scanner+ Simple
            BS_PACKAGE                  // Barcode Scanner
            // What else supports this intent?
    );

    private final Activity activity;
    private final Map<String, Object> moreExtras;
    private String title;
    private String message;
    private String buttonYes;
    private String buttonNo;
    private List<String> targetApplications;

    public IntentIntegrator(Activity activity) {
        this.activity = activity;
        title = DEFAULT_TITLE;
        message = DEFAULT_MESSAGE;
        buttonYes = DEFAULT_YES;
        buttonNo = DEFAULT_NO;
        targetApplications = TARGET_ALL_KNOWN;
        moreExtras = new HashMap<String, Object>(3);
    }

    private static boolean contains(Iterable<ResolveInfo> availableApps, String targetApp) {
        for (ResolveInfo availableApp : availableApps) {
            String packageName = availableApp.activityInfo.packageName;
            if (targetApp.equals(packageName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * <p>Call this from your {@link Activity}'s
     * {@link Activity#onActivityResult(int, int, Intent)} method.</p>
     *
     * @return null if the event handled here was not related to this class, or
     * else an {@link IntentResult} containing the result of the scan. If the user cancelled scanning,
     * the fields will be null.
     */
    public static IntentResult parseActivityResult(int requestCode, int resultCode, Intent intent) {
        if (requestCode == REQUEST_CODE) {
            if (resultCode == Activity.RESULT_OK) {
                String contents = intent.getStringExtra("SCAN_RESULT");
                String formatName = intent.getStringExtra("SCAN_RESULT_FORMAT");
                byte[] rawBytes = intent.getByteArrayExtra("SCAN_RESULT_BYTES");
                int intentOrientation = intent.getIntExtra("SCAN_RESULT_ORIENTATION", Integer.MIN_VALUE);
                Integer orientation = intentOrientation == Integer.MIN_VALUE ? null : intentOrientation;
                String errorCorrectionLevel = intent.getStringExtra("SCAN_RESULT_ERROR_CORRECTION_LEVEL");
                return new IntentResult(contents, formatName, rawBytes, orientation, errorCorrectionLevel);
            }
            return new IntentResult();
        }
        return null;
    }

    private static List<String> list(String... values) {
        return Collections.unmodifiableList(Arrays.asList(values));
    }

    /**
     * Initiates a scan for all known barcode types.
     */
    public final AlertDialog initiateScan() {
        return initiateScan(ALL_CODE_TYPES);
    }

    public final AlertDialog initiateScan(Collection<String> desiredBarcodeFormats) {
        Intent intentScan = new Intent(BS_PACKAGE + ".SCAN");
        intentScan.addCategory(Intent.CATEGORY_DEFAULT);

        // check which types of codes to scan for
        if (desiredBarcodeFormats != null) {
            // set the desired barcode types
            StringBuilder joinedByComma = new StringBuilder();
            for (String format : desiredBarcodeFormats) {
                if (joinedByComma.length() > 0) {
                    joinedByComma.append(',');
                }
                joinedByComma.append(format);
            }
            intentScan.putExtra("SCAN_FORMATS", joinedByComma.toString());
        }

        String targetAppPackage = findTargetAppPackage(intentScan);
        if (targetAppPackage == null) {
            return showDownloadDialog();
        }
        intentScan.setPackage(targetAppPackage);
        intentScan.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        intentScan.addFlags(Intent.FLAG_ACTIVITY_CLEAR_WHEN_TASK_RESET);
        attachMoreExtras(intentScan);
        startActivityForResult(intentScan, REQUEST_CODE);
        return null;
    }

    /**
     * Start an activity. This method is defined to allow different methods of activity starting for
     * newer versions of Android and for compatibility library.
     *
     * @param intent Intent to start.
     * @param code   Request code for the activity
     * @see android.app.Activity#startActivityForResult(Intent, int)
     * @see android.app.Fragment#startActivityForResult(Intent, int)
     */
    protected void startActivityForResult(Intent intent, int code) {
        activity.startActivityForResult(intent, code);
    }

    private String findTargetAppPackage(Intent intent) {
        PackageManager pm = activity.getPackageManager();
        List<ResolveInfo> availableApps = pm.queryIntentActivities(intent, PackageManager.MATCH_DEFAULT_ONLY);
        if (availableApps != null) {
            for (String targetApp : targetApplications) {
                if (contains(availableApps, targetApp)) {
                    return targetApp;
                }
            }
        }
        return null;
    }

    private AlertDialog showDownloadDialog() {
        AlertDialog.Builder downloadDialog = new AlertDialog.Builder(activity);
        downloadDialog.setTitle(title);
        downloadDialog.setMessage(message);
        downloadDialog.setPositiveButton(buttonYes, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
                String packageName;
                if (targetApplications.contains(BS_PACKAGE)) {
                    // Prefer to suggest download of BS if it's anywhere in the list
                    packageName = BS_PACKAGE;
                } else {
                    // Otherwise, first option:
                    packageName = targetApplications.get(0);
                }
                Uri uri = Uri.parse("market://details?id=" + packageName);
                Intent intent = new Intent(Intent.ACTION_VIEW, uri);
                try {
                    activity.startActivity(intent);
                } catch (ActivityNotFoundException anfe) {
                    // Hmm, market is not installed
                    Log.w(TAG, "Google Play is not installed; cannot install " + packageName);
                }
            }
        });
        downloadDialog.setNegativeButton(buttonNo, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialogInterface, int i) {
            }
        });
        return downloadDialog.show();
    }

    private void attachMoreExtras(Intent intent) {
        for (Map.Entry<String, Object> entry : moreExtras.entrySet()) {
            String key = entry.getKey();
            Object value = entry.getValue();
            // Kind of hacky
            if (value instanceof Integer) {
                intent.putExtra(key, (Integer) value);
            } else if (value instanceof Long) {
                intent.putExtra(key, (Long) value);
            } else if (value instanceof Boolean) {
                intent.putExtra(key, (Boolean) value);
            } else if (value instanceof Double) {
                intent.putExtra(key, (Double) value);
            } else if (value instanceof Float) {
                intent.putExtra(key, (Float) value);
            } else if (value instanceof Bundle) {
                intent.putExtra(key, (Bundle) value);
            } else {
                intent.putExtra(key, value.toString());
            }
        }
    }
}

