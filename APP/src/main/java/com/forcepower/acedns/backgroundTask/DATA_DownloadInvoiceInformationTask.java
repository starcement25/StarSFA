package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.InvoiceInformation;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
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

/**
 * Created by amit on 12/01/2017.
 */

public class DATA_DownloadInvoiceInformationTask extends AsyncTask<Void, Void, Void> {
    String fileNameInvoiceInformation = "invoice_information.txt";
    int noRows = -1, noColumn = -1;
    String timeStamp = "";
    Context mContext;

    public DATA_DownloadInvoiceInformationTask(Context context) {
        this.mContext = context;

    }

    @Override
    protected Void doInBackground(Void... voids) {
        String url = BaseUrl.baseUrl + AceDnsWebServiceURL.InvoiceInformationDownloadURL
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();

        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DownloadInvoiceInformationTask: " + url);

        downloader(url);
        File csvFile = new File(Utils.getAppStoragePath(mContext) + fileNameInvoiceInformation);
        ArrayList<InvoiceInformation> invoiceList = new ArrayList<>();
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
                        InvoiceInformation temp = new InvoiceInformation();
                        temp.setInvoiceNo(RowData[0]);
                        temp.setInvoiceDate(RowData[1]);
                        temp.setOrderNo(RowData[2]);
                        temp.setCustomerCode(RowData[3]);
//                        temp.setFlag(RowData[4]);
                        temp.setChronologicalNumber(RowData[4]);
                        temp.setFreightCharge(RowData[5]);
                        invoiceList.add(temp);

                    }
                }
            }
            buffer.close();
            AceDnsDatabase dbHelper;
            dbHelper = new AceDnsDatabase(mContext);
            long insertStatus = dbHelper.insertToInvoiceInformation(invoiceList);
        } catch (IOException ex) {
            ex.printStackTrace();
        }
        return null;
    }

    private void downloader(String urlstr) {
        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        URL url = null;

        try {
            outputFile = new File(Utils.getAppStoragePath(mContext) + fileNameInvoiceInformation);
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
}
