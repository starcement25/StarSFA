package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.CardTransactionDetails;
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

public class SPECIAL_LoadLoyaltyTransactionData extends
        AsyncTask<String, Void, Long> {

    Context mContext;
    AceDnsDatabase dbHelper;
    int noRows = -1, noColumn = -1;
    String timeStamp = "";
    String lastUpdate = "1970-01-01 18:19:20"; // Just to know the format
    boolean isIndependantDownload = false;
    ProgressDialog pd;

    public SPECIAL_LoadLoyaltyTransactionData(Context context,
                                              boolean isIndependantDownload) {
        this.mContext = context;
        dbHelper = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        lastUpdate = dbHelper.getlastDownloadTime("card_transaction");
        this.isIndependantDownload = isIndependantDownload;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Long doInBackground(String... params) {
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.loyaltyCardTransactionURL
                + "?nick_name="
                + Constants.nickName
                + "&emp_code="
                + Constants.employeeDetailObject.getEmpCode()
                + "&incremental_download="
                + (Constants.isFirstLoginOfApp
                || Constants.isLoyaltyPurchaseUpdated ? "no" : "yes")
                + "&last_update_time=" + lastUpdate;

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ SPECIAL_LoadLoyaltyTransactionData: " +url);

        downloader(url);

        File csvFile = new File(Utils.getAppStoragePath(mContext)
                + "loyalty_card_transaction_details.txt");
        ArrayList<CardTransactionDetails> transList = new ArrayList<CardTransactionDetails>();
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
                        CardTransactionDetails temp = new CardTransactionDetails();
                        temp.setTransactionId(RowData[0]);
                        temp.setCardNumber(RowData[1]);
                        temp.setRdsCode(RowData[2]);
                        temp.setPurchaseValue(RowData[3]);
                        temp.setVerticalName(RowData[4]);
                        temp.setVehicleNo(RowData[5]);
                        temp.setVehicleType(RowData[6]);
                        temp.setPointsEarned(RowData[7]);
                        temp.setPointsRedeemed(RowData[8]);
                        temp.setFlag(RowData[9]);
                        transList.add(temp);
                        temp = null;
                    }
                }
            }
            buffer.close();
        } catch (IOException ex) {
            ex.printStackTrace();
        }

        if (isIndependantDownload) {
            ((Activity) mContext).runOnUiThread(new Runnable() {
                public void run() {
                    pd.setMessage("Saving " + noRows
                            + " records for \n Card Transaction Details..");
                }
            });
        } else {
            Log.i("Card Transaction", "Saving " + noRows
                    + " records for \n Card Transaction Details..");

        }
        long insertStatus = dbHelper.insertToCrdTransaction(transList);

        return insertStatus;
    }

    @Override
    protected void onPostExecute(Long result) {
        super.onPostExecute(result);
        if (result == noRows || noRows == 0) {
            dbHelper.insertToLogTable(timeStamp, "card_transaction");
        } else if (PhoneStateChangeListener.ringing) {
            new MASTER_LoadLoyaltyPurchaseData(mContext).execute();
        } else {
            Constants.isDownLoadComplete = false;
            Utils.directOutsideTheApplication(
                    mContext,
                    "Connection lost while downloading Card Transaction Details.. \n Please ReLogin.",
                    true);
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
                    + "loyalty_card_transaction_details.txt");
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
