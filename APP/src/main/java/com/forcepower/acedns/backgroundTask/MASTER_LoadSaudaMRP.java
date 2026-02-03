package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.SaudaMrp;
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

public class MASTER_LoadSaudaMRP extends AsyncTask<String, Void, Long> {

    Context mContext;
    AceDnsDatabase mAceDnsDatabase;
    int noRows = -1, noColumn = -1;
    String timeStamp = "";
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    String dwnldDictTime = "2014-06-09 18:19:20"; // Just to know the format
    String mIsInCremental = "";

    public MASTER_LoadSaudaMRP(Context context) {
        this.mContext = context;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        lastUpdate = mAceDnsDatabase.getlastDownloadTime("sauda_mrp");
        dwnldDictTime = mAceDnsDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Long doInBackground(String... params) {

        InCrementalDownload();

        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.saudaMrpURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode()
                + "&incremental_download=" + mIsInCremental
                + "&last_update_time=" + lastUpdate
                + "&data_download_time=" + dwnldDictTime;
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ MASTER_LoadSaudaMRP: " +url);
        downloader(url);

        File csvFile = new File(Utils.getAppStoragePath(mContext) + "sauda_mrp.txt");
        ArrayList<SaudaMrp> saudaMrpList = new ArrayList<SaudaMrp>();
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
                    timeStamp = line;
                } else {
                    String[] RowData = line.split("\\^");
                    if (RowData.length == noColumn) {
                        SaudaMrp temp = new SaudaMrp();
                        temp.setSKUCode(RowData[0]);
                        temp.setMrpCode(RowData[1]);
                        temp.setMrpValue(RowData[2]);
                        temp.setSaleRate(RowData[3]);
                        temp.setUOM(RowData[4]);
                        temp.setBranchCode(RowData[5]);
                        temp.setbasic_rate(RowData[6]);
                        temp.setprimary_freight(RowData[7]);
                        temp.setdepot_cost(RowData[8]);
                        saudaMrpList.add(temp);
                    }
                }
            }
            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        long insertStatus = mAceDnsDatabase.InsertToSaudaMRPMaster(saudaMrpList);
        return insertStatus;
    }

    @Override
    protected void onPostExecute(Long result) {
        super.onPostExecute(result);
        if (result == noRows && noRows > 0) {
            Constants.isSaudaMrpTableUpdated = false;
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new MASTER_LoadMRPDetails(mContext).execute();
        } else if (noRows == 0 && noColumn != 0) {
            Constants.isSaudaMrpTableUpdated = false;
            decideNavigation();
        } else {
            if (noRows != 0) {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void InCrementalDownload() {
        if ((true == Constants.isFirstLoginOfApp) || (true == Constants.isSaudaMrpTableUpdated)) {
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
            outputFile = new File(Utils.getAppStoragePath(mContext) + "sauda_mrp.txt");
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

    public void decideNavigation() {
        if (!(noRows == 0 && noColumn != 0)) {
            mAceDnsDatabase.insertToLogTable(timeStamp, "sauda_mrp");
        }
    }
}
