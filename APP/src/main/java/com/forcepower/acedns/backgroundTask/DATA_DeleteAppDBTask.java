package com.forcepower.acedns.backgroundTask;

import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.Utils;

import java.io.File;

public class DATA_DeleteAppDBTask extends AsyncTask<Void, Void, String> {
    private String option = "";
    private Context mContext;


    public DATA_DeleteAppDBTask(Context context) {
        this.mContext = context;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Checking data integrity.\n Please wait");

    }

    @Override
    protected String doInBackground(Void... params) {
        try {
            String nickName = Constants.userDetailsObj.getNickName();
            String empCode = Constants.employeeDetailObject.getEmpCode();
            ContentValues values = new ContentValues();
            values.put("nick_name", nickName);
            values.put("emp_code", empCode);
            values.put("deviceid", Constants.deviceId);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DeleteAppDBTask: " + BaseUrl.baseUrl + AceDnsWebServiceURL.dbDeleteURL);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DeleteAppDBTask values: " + values);
            option = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.dbDeleteURL, values);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return option;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DeleteAppDBTask result: " + option);
        Utils.cancelProgressDialog();
        if (option.matches("1")) {
            File outputFile = new File(Utils.getAppStoragePath(mContext));
            if (outputFile.exists())
                deleteTempFolderRecursive(outputFile);

        }

    }

    private void deleteTempFolderRecursive(File fileOrDirectory) {
        if (fileOrDirectory.isDirectory())
            for (File child : fileOrDirectory.listFiles())
                deleteTempFolderRecursive(child);

        fileOrDirectory.delete();
    }
}
