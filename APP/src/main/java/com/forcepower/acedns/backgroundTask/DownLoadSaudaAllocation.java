package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.SaudaAllocationDetails;
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

public class DownLoadSaudaAllocation extends AsyncTask<String, Void, Long> {

    Context mContext;
    AceDnsDatabase mAceDnsDatabase;
    int noRows = -1, noColumn = -1;
    String mTimeStamp = "";

    public DownLoadSaudaAllocation(Context context) {
        this.mContext = context;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Long doInBackground(String... params) {
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.saudaURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DownLoadSaudaAllocation: " +url);
        downloader(url);

        File csvFile = new File(Utils.getAppStoragePath(mContext) + "sauda_allocation.txt");
        ArrayList<SaudaAllocationDetails> saudaList = new ArrayList<SaudaAllocationDetails>();
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
                    if (noRows != 0 && noColumn != 0) {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn) {
                            SaudaAllocationDetails temp = new SaudaAllocationDetails();
                            temp.setEmpCode(RowData[0]);
                            temp.setProdFilterCode(RowData[1]);
                            temp.setQty(RowData[2]);
                            temp.setBal(RowData[2]);
                            temp.setAllotedQty(RowData[3]);
                            saudaList.add(temp);
                            temp = null;
                        }
                    }
                }
            }
            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        long insertStatus = 0;
        if (noRows != 0 && noColumn != 0) {
            insertStatus = mAceDnsDatabase.insertToSaudaAllocation(saudaList);
        }
        return insertStatus;
    }

    @Override
    protected void onPostExecute(Long result) {
        super.onPostExecute(result);
        if (result == noRows && noRows > 0) {
            decideNavigation();
        } else if (PhoneStateChangeListener.ringing) {
            new DownLoadSaudaAllocation(mContext).execute();
        } else if (noRows == 0 && noColumn != 0) {
            decideNavigation();
        } else if (noRows == 0 && noColumn == 0) {
            decideNavigation();
        } else {
        }
    }

    private void downloader(String urlstr) {

        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        URL url = null;

        try {
            outputFile = new File(Utils.getAppStoragePath(mContext)
                    + "sauda_allocation.txt");
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

    public void decideNavigation() {
        if (!(noRows == 0 && noColumn != 0)) {
            mAceDnsDatabase.insertToLogTable(mTimeStamp, "sauda_allocation");
        }
        mAceDnsDatabase.closeDatabase();
    }
}
