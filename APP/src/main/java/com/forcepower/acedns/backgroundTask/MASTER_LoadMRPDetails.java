package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.MRPDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.PhoneStateChangeListener;
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
import java.util.ArrayList;

public class MASTER_LoadMRPDetails extends AsyncTask<String, Void, Long> {

    Context mContext;
    String mTimeStamp = "";
    String mLastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    String mDwnldDictTime = "2014-06-09 18:19:20"; // Just to know the format
    AceDnsDatabase mAceDnsDatabase;
    int noRows = -1, noColumn = -1;
    String mIsInCremental = "";


    public MASTER_LoadMRPDetails(Context context) {
        this.mContext = context;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        mLastUpdate = mAceDnsDatabase.getlastDownloadTime("mrp_master");
        mDwnldDictTime = mAceDnsDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Long doInBackground(String... params) {

        InCrementalDownload();

        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.mrpURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&incremental_download=" + mIsInCremental
                + "&last_update_time=" + mLastUpdate
                + "&data_download_time=" + mDwnldDictTime;
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ MASTER_LoadMRPDetails: " +url);
        downloader(url);

        File csvFile = new File(Utils.getAppStoragePath(mContext) + "mrp_master.txt");
        ArrayList<MRPDetails> mrpList = new ArrayList<MRPDetails>();
        FileReader file = null;
        try {
            file = new FileReader(csvFile);
        } catch (FileNotFoundException e1) {
            e1.printStackTrace();
        }
        BufferedReader buffer = new BufferedReader(file);
        try {
            String line = "";
            while ((line = buffer.readLine()) != null) {
                if (line.indexOf("¥") > 0) {
                    String[] dataArray = line.split("¥");
                    noRows = Integer.parseInt(dataArray[0]);
                    noColumn = Integer.parseInt(dataArray[1]);
                } else if (line.indexOf("€") > 0) {
                    mTimeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        MRPDetails temp = new MRPDetails();
                        temp.setProdCode(RowData[0]);
                        temp.setMrpCode(RowData[1]);
                        temp.setMrpValue(RowData[2]);
                        temp.setSaleRate(RowData[3]);
                        temp.setUom(RowData[4]);
                        temp.setBranchCode(RowData[5]);
                        temp.setDestinationCode(RowData[6]);
                        temp.setOrderType(RowData[7]);
                        temp.setAcedns(RowData[8]);
                        temp.setWSRate(RowData[9]);
                        temp.setDistributorRate(RowData[10]);
                        temp.setSSRate(RowData[11]);
                        temp.setDepotRate(RowData[12]);
                        mrpList.add(temp);
                        temp = null;
                    }
                }
            }
            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        Log.i("MRP", "Saving " + noRows + " records for \n MRP Details..");
        long insertStatus = mAceDnsDatabase.insertToMRPMaster(mrpList);
        return insertStatus;
    }

    @Override
    protected void onPostExecute(Long result) {
        super.onPostExecute(result);
        if (result == noRows && noRows > 0) {
            Constants.isMrpTableUpdated = false;
            UpdateDownLoadLog();
        } else if (PhoneStateChangeListener.ringing) {
            new MASTER_LoadMRPDetails(mContext).execute();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isMrpTableUpdated = false;
            UpdateDownLoadLog();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void InCrementalDownload() {
        if ((true == Constants.isFirstLoginOfApp) || (true == Constants.isMrpTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    private void downloader(String urlstr) {

        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        URL url = null;

        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + "mrp_master.txt");
            if (outputFile.exists())
                Log.e("File delete", outputFile.delete() + "");
            fbo = new FileOutputStream(outputFile, false);
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

    public void UpdateDownLoadLog() {
        if (noRows != 0 && noColumn != 0) {
            mAceDnsDatabase.insertToLogTable(mTimeStamp, "mrp_master");
        }
    }
}
