package com.forcepower.acedns.newDataBase.sync;

import android.content.Context;
import android.os.Handler;
import android.os.Looper;

import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerAgeingDataSet;
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

public class DataForDownloadingCustomerAgeingNew {
    Context mContext;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;

    public DataForDownloadingCustomerAgeingNew(Context context) {
        mContext = context;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(context);
    }

    public interface DownloadCallback {
        void onComplete(boolean success);
    }

    public void addAllFormDataForCustomerAgeing(DownloadCallback callback) {
        try {
            mNewDatabaseForSiteLead.createNewTableForCustomerAgeing();
        } catch (Exception ignored) {
        }
        _DOWNLOAD_CustomerAgeingList(callback);
    }

    private void Download_txt(String URL, String data) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile;
        InputStream is = null;
        java.net.URL url;
        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + data + ".txt");
            if (outputFile.exists()) {
                outputFile.delete();
            }
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
            c = (HttpURLConnection) url.openConnection();
            c.setRequestMethod("GET");
            c.setConnectTimeout(0);
            c.connect();
            int responseCode = c.getResponseCode();
            if (responseCode == HttpURLConnection.HTTP_OK) {
                is = c.getInputStream();
                byte[] buffer = new byte[1024];
                int len1;
                while ((len1 = is.read(buffer)) != -1) {
                    fbo.write(buffer, 0, len1);
                }
                fbo.flush();
            }
        } catch (Exception ignored) {
        } finally {
            if (c != null) {
                c.disconnect();
            }
            if (fbo != null) {
                try {
                    fbo.close();
                } catch (IOException ignored) {
                }
            }
            if (is != null) {
                try {
                    is.close();
                } catch (IOException ignored) {
                }
            }
        }
    }

    public void _DOWNLOAD_CustomerAgeingList(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/sfa_customer_ageing_api.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
            boolean isSuccess;
            Download_txt(URL, "sfa_customer_ageing_api");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "sfa_customer_ageing_api" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException ignored) {
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line;
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1]);
                    } else {
                        String[] RowData = line.split("\\^");
                        CustomerAgeingDataSet tempCustomerAgeingDataSet = new CustomerAgeingDataSet(
                                RowData[1],
                                RowData[0],
                                RowData[4], RowData[5], "",
                                RowData[6], RowData[7], "",
                                RowData[8], RowData[9], "",
                                RowData[10], RowData[11], "",
                                RowData[12], RowData[13], "",
                                RowData[14], RowData[15], "",
                                RowData[16], RowData[17], "",
                                RowData[18], RowData[19], "",
                                RowData[20], RowData[21], "",
                                "1800200",
                                "55"
                        );
                        mNewDatabaseForSiteLead.insertCustomerAgeing(tempCustomerAgeingDataSet);
                    }
                }
                buffer.close();
                isSuccess = true;
            } catch (IOException ignored) {
                isSuccess = false;
            }
            boolean finalResult = isSuccess;
            new Handler(Looper.getMainLooper()).post(() -> callback.onComplete(finalResult));
        }).start();
    }
}