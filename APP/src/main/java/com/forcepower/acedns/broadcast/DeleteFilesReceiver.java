package com.forcepower.acedns.broadcast; // ✅ Must match manifest's .broadcast.DeleteFilesReceiver

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.util.Log;

import java.io.File;

public class DeleteFilesReceiver extends BroadcastReceiver {
    private static final String RESULT_RECEIVER_PACKAGE = "com.example.filedeleteforsfa";
    private static final String TAG = "DeleteFilesReceiver";

    @Override
    public void onReceive(Context context, Intent intent) {
        if (intent != null) {
            Log.d(TAG, "Action 1111111: " + intent.getAction());
            if(intent.getAction().equalsIgnoreCase("com.forcepower.acedns.ACTION_DELETE_FILES")){
                deleteAllSfaFiles(context);
            }
        }
    }
    private void deleteAllSfaFiles(Context context) {
        int successCount = 0;
        int failCount = 0;
        String dataPath = context.getApplicationInfo().dataDir;
        File[] targets = new File[]{
                new File(dataPath, "databases"),
                new File(dataPath, "shared_prefs"),
                context.getFilesDir(),
                context.getCacheDir(),
                new File(dataPath, "code_cache"),
                context.getExternalCacheDir(),
                context.getExternalFilesDir(null)
        };

        for (File target : targets) {
            if (target != null && target.exists()) {
                int[] result = deleteRecursive(target);
                successCount += result[0];
                failCount += result[1];
            }
        }
        Intent resultIntent = new Intent("com.example.filedeleteforsfa.ACTION_DELETE_RESULT");
        resultIntent.setPackage(RESULT_RECEIVER_PACKAGE);
        resultIntent.putExtra("type", "DELETE");
        resultIntent.putExtra("success_count", successCount);
        resultIntent.putExtra("fail_count", failCount);
        context.sendBroadcast(resultIntent);
    }

    // Recursive delete
    private int[] deleteRecursive(File fileOrDirectory) {
        int success = 0;
        int fail = 0;
        try {
            if (fileOrDirectory.isDirectory()) {
                File[] children = fileOrDirectory.listFiles();
                if (children != null) {
                    for (File child : children) {
                        int[] childResult = deleteRecursive(child);
                        success += childResult[0];
                        fail += childResult[1];
                    }
                }
            }
            boolean deleted = fileOrDirectory.delete();
            if (deleted) {
                success++;
            } else {
                fail++;
            }

        } catch (Exception e) {
            fail++;
        }

        return new int[]{success, fail};
    }
}