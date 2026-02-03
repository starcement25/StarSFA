package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Build;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.activity.ActivityDownloadStatus;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;

public class DATA_LoadDataDictionaryData extends AsyncTask<String, Void, Void> {
    Context mContext;
    AceDnsDatabase dbHelper;
    String timeStamp = "";
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format

    public DATA_LoadDataDictionaryData(Context context) {
        this.mContext = context;
        dbHelper = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        lastUpdate = dbHelper.getlastDownloadTime("download_dictionary");
        Constants.dictDownldStartTime = new Date();
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.changeProgressDialogMsg(mContext, "Downloading Data Dictionary..");
    }

    @Override
    protected Void doInBackground(String... params) {
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.downloadDictionaryURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&incremental_download=" + (Constants.isFirstLoginOfApp ? "no" : "yes")
                + "&last_update_time=" + lastUpdate
                + "&device_id=" + (Constants.deviceId.length() > 0 ? Constants.deviceId : Constants.employeeDetailObject.getDeviceID());

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_LoadDataDictionaryData: " +url);

        downloader(url);

        File csvFile = new File(Utils.getAppStoragePath(mContext) + "data_download_dict.txt");
        Constants.downloadTableList = new ArrayList<String>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }
        @SuppressWarnings("resource")
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line = "";
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("€") > 0) {
                    timeStamp = line.replace("€", "");
                    Constants.dateString = timeStamp.substring(0, 10).replace("-", "");
                    try {
                        Constants.dictDownldSrverTime = Calendar.getInstance();
                        Constants.dictDownldSrverTime.setTime(new SimpleDateFormat("yyyy-MM-ddHH:mm:ss").parse(timeStamp));
                    } catch (Exception e) {
                        Constants.dictDownldSrverTime = Calendar.getInstance();
                    }
                } else {
                    Constants.downloadTableList.add(line);
                }
            }
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Utils.cancelProgressDialog();
        if (Constants.isFirstLoginOfApp) {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                new UpdateDeviceIdAsynctask(mContext).execute();
            } else {
                Utils.insertToEmployeeMaster(mContext);
                dataDownloadCommonProcess();
            }
        } else {
            Utils.updateEmployeeMasterDate(mContext);
            dataDownloadCommonProcess();
        }
    }

    private void dataDownloadCommonProcess() {
        if (Constants.downloadTableList.size() > 0) {
            Intent intent = new Intent(mContext, ActivityDownloadStatus.class);
            mContext.startActivity(intent);
        } else {
            new DATA_ConfirmDownloadTask(mContext).execute();
        }
    }

    private void downloader(String urlstr) {

        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        URL url = null;

        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + "data_download_dict.txt");
            if (outputFile.exists())
                Log.e("File delete", outputFile.delete() + "");
            fbo = new FileOutputStream(outputFile, false);

            // connect with server where remote file is stored to download it
            url = new URL(urlstr);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.setConnectTimeout(0);

            c.connect();

            is = c.getInputStream();

            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = is.read(buffer)) != -1) {
                fbo.write(buffer, 0, len1);
                Log.e("length", len1 + "----");
            }

            fbo.flush();

        } catch (Exception e) {

        } finally {

            if (c != null)
                c.disconnect();
            if (fbo != null)
                try {
                    fbo.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            if (is != null)
                try {
                    is.close();
                } catch (IOException e) {
                    e.printStackTrace();
                }
            outputFile = null;
        }
    }
}
