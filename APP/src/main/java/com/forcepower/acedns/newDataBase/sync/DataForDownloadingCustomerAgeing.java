package com.forcepower.acedns.newDataBase.sync;

import android.content.Context;
import android.os.Handler;
import android.os.Looper;
import android.util.Log;

import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.CustomerAgeingDataSet;
import com.forcepower.acedns.newDataBase.data_set.CustomerAgeingInvoiceNumberDataSet;
import com.forcepower.acedns.newDataBase.data_set.CustomerMasterTableDataSet;
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
import java.util.Random;

public class DataForDownloadingCustomerAgeing {
    Context mContext;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;
    String TYPE_OF_USER = "";

    public DataForDownloadingCustomerAgeing(Context context) {
        mContext = context;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(context);
    }

    public interface DownloadCallback {
        void onComplete(boolean success);
    }

    public void addAllFormDataForCustomerAgeing(DownloadCallback callback) {
        try {
            mNewDatabaseForSiteLead.createNewTableForCustomerAgeing();
        } catch (Exception e) {
            Log.d("TAG", "DataForDownloadingLead addAllFormDataForLead: " + e.getMessage());
        }

//        _DOWNLOAD_CustomerAgeingList(successSoldToPartyList -> {
//            if (!successSoldToPartyList) {
//                callback.onComplete(false);
//            } else {
//                _DOWNLOAD_CustomerAgeingInvoiceNumberList(callback);
//            }
//        });
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
        Log.d("TAG", "_DOWNLOAD_ CustomerAgeingList: "+URL);
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
                        Log.d("TAG", "_DOWNLOAD_ CustomerAgeingList: "+line);
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
                Log.d("TAG", "_DOWNLOAD_ CustomerAgeingList: "+ignored.getMessage());
                isSuccess = false;
            }
            boolean finalResult = isSuccess;
            new Handler(Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }
}

//mNewDatabaseForSiteLead.deleteCustomerAgeingInvoiceNumber();
//            Random random = new Random();
//            for(int i=0;i<10;i++){
//                boolean isNE = random.nextBoolean();
//
//                final int invoiceCount1 = random.nextInt(10)+1;
//                final int invoiceAge1=3;
//                for (int j = 0; j < invoiceCount1; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+random.nextInt(100000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+random.nextInt(invoiceAge1)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//                final int invoiceCount2 = random.nextInt(10)+1;
//                final int invoiceAge2=isNE ?10:7;
//                for (int j = 0; j < invoiceCount2; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+(random.nextInt(100000)+100000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+(random.nextInt(invoiceAge2-invoiceAge1)+invoiceAge1)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//                final int invoiceCount3 = random.nextInt(10)+1;
//                final int invoiceAge3=isNE ?17:12;
//                for (int j = 0; j < invoiceCount3; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+(random.nextInt(100000)+200000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+(random.nextInt(invoiceAge3-invoiceAge2)+invoiceAge2)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//                final int invoiceCount4 = random.nextInt(10)+1;
//                final int invoiceAge4=25;
//                for (int j = 0; j < invoiceCount4; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+(random.nextInt(100000)+300000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+(random.nextInt(invoiceAge4-invoiceAge3)+invoiceAge3)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//                final int invoiceCount5 = random.nextInt(10)+1;
//                final int invoiceAge5=30;
//                for (int j = 0; j < invoiceCount5; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+(random.nextInt(100000)+400000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+(random.nextInt(invoiceAge5-invoiceAge4)+invoiceAge4)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//                final int invoiceCount6 = random.nextInt(10)+1;
//                final int invoiceAge6=45;
//                for (int j = 0; j < invoiceCount6; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+(random.nextInt(100000)+500000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+(random.nextInt(invoiceAge6-invoiceAge5)+invoiceAge5)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//                final int invoiceCount7 = random.nextInt(10)+1;
//                final int invoiceAge7=60;
//                for (int j = 0; j < invoiceCount7; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+(random.nextInt(100000)+600000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+(random.nextInt(invoiceAge7-invoiceAge6)+invoiceAge6)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//                final int invoiceCount8 = random.nextInt(10)+1;
//                final int invoiceAge8=90;
//                for (int j = 0; j < invoiceCount8; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+(random.nextInt(100000)+700000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+(random.nextInt(invoiceAge8-invoiceAge7)+invoiceAge7)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//                final int invoiceCount9 = random.nextInt(10)+1;
//                final int invoiceAge9=150;
//                for (int j = 0; j < invoiceCount9; j++) {
//                    CustomerAgeingInvoiceNumberDataSet temp = new CustomerAgeingInvoiceNumberDataSet(
//                            "Customer Name "+(i + 1),
//                            (i + 1)+"CNMD",
//                            "INV"+(i + 1)+"_"+(j + 1)+"_"+(random.nextInt(100000)+800000),
//                            (random.nextInt(30)+1)+"-"+(random.nextInt(6)+1)+"-2026",
//                            ""+(random.nextInt(4001)+1000),
//                            ""+(random.nextInt(invoiceAge9-invoiceAge8)+invoiceAge8)
//                    );
//                    mNewDatabaseForSiteLead.insertCustomerAgeingInvoiceNumber(temp);
//                }
//
//
//                CustomerAgeingDataSet tempCustomerAgeingDataSet=new CustomerAgeingDataSet(
//                        "Customer Name "+(i + 1),
//                        (i + 1)+"CNMD",
//                        isNE ? "Day3" : "Day3",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount1,
//                        isNE ? "Day10" : "Day7",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount2,
//                        isNE ? "Day17" : "Day12",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount3,
//                        isNE ? "Day25" : "Day25",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount4,
//                        isNE ? "Day30" : "Day30",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount5,
//                        isNE ? "Day45" : "Day45",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount6,
//                        isNE ? "Day60" : "Day60",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount7,
//                        isNE ? "Day90" : "Day90",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount8,
//                        isNE ? "Abv90" : "Abv90",
//                        ""+(random.nextInt(40001)+10000),
//                        ""+invoiceCount9,
//                        "1800200",
//                        "55"
//                );
//                mNewDatabaseForSiteLead.insertCustomerAgeing(tempCustomerAgeingDataSet);
//            }