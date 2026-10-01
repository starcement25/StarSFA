package com.forcepower.acedns.newDataBase.sync;

import android.content.Context;
import android.util.Log;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.ShipToPartyLocationDataSet;
import com.forcepower.acedns.util.Utils;

import org.json.JSONObject;

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

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class DataForDownloadingShipToPartyLocation {
    Context mContext;
    NewDatabaseForSiteLead mNewDatabaseForSiteLead;

    public DataForDownloadingShipToPartyLocation(Context context) {
        mContext = context;
        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(context);
        mNewDatabaseForSiteLead.createNewTableForShipToPartyLocation();
    }

    public void addAllData(DownloadCallback callback) {
        mNewDatabaseForSiteLead.deleteAllDataShipToPartyLocation();
         uploadPendingShipToPartyLocations(() -> {
             mNewDatabaseForSiteLead.deleteAllDataShipToPartyLocation();
             _DOWNLOAD_ShipToPartyLocation(callback);
         });
    }
    private void uploadPendingShipToPartyLocations(Runnable onDone) {
        new Thread(() -> {
            ArrayList<ShipToPartyLocationDataSet> pending = mNewDatabaseForSiteLead.getShipToPartyLocation_NotUploadedList();

            for (ShipToPartyLocationDataSet data : pending) {
                try {
                    OkHttpClient client = new OkHttpClient();
                    JSONObject payload = new JSONObject();
                    payload.put("emp_code", Constants.employeeDetailObject.getEmpCode());
                    payload.put("branch_code", data.getBranch_code());
                    payload.put("dns_ship_to_party_code", data.getShip_to_party_code());
                    payload.put("dealer_code", data.getDealer_code());
                    payload.put("latitude", data.getLatitude());
                    payload.put("longitude", data.getLongitude());
                    MediaType mediaType = MediaType.parse("application/json");
                    assert mediaType != null;
                    RequestBody body = RequestBody.create(mediaType, payload.toString());
                    Request request = new Request.Builder()
                            .url(BaseUrl.baseUrl + "misreport/api_update_ship_to_party_location.php")
                            .post(body)
                            .build();

                    Response response = client.newCall(request).execute();
                    if (response.body() != null) {
                        JSONObject jsonObject = new JSONObject(response.body().string());
                        if (!jsonObject.optString("process_status").equalsIgnoreCase("no")) {
                            mNewDatabaseForSiteLead.updateShipToPartyLocation(data.getShip_to_party_code(),data.getLatitude(),data.getLongitude(),2);
                        }
                    }
                } catch (Exception e) {
                    Log.e("_DOWNLOAD_", "uploadPendingShipToPartyLocations: " + e.getMessage(), e);
                }
            }

            onDone.run();
        }).start();
    }
    public interface DownloadCallback {
        void onComplete(boolean success);
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
    
    public void _DOWNLOAD_ShipToPartyLocation(DownloadCallback callback) {
        final int[] noColumn = {-1};
        String URL = BaseUrl.baseUrl + "misreport/api_get_ship_to_party_list.php?emp_code=" + Constants.employeeDetailObject.getEmpCode();
        new Thread(() -> {
             Download_txt(URL, "ShipToPartyLocation");
            File csvFile = new File(Utils.getAppStoragePath(mContext) + "ShipToPartyLocation" + ".txt");
            FileReader file = null;
            try {
                file = new FileReader(csvFile);
            } catch (FileNotFoundException e) {
                Log.d("TAG", "_DOWNLOAD_ ShipToPartyLocation: " + e.getMessage());
            }
            BufferedReader buffer = new BufferedReader(file);
            try {
                String line;
                while ((line = buffer.readLine()) != null) {
                    if (line.indexOf("¥") > 0) {
                        String[] dataArray = line.split("¥");
                        noColumn[0] = Integer.parseInt(dataArray[1].trim());
                    }else if (line.indexOf("#") > 0) {
                    } else {
                        String[] RowData = line.split("\\^");
                        if (RowData.length == noColumn[0]) {
                            ShipToPartyLocationDataSet temp = new ShipToPartyLocationDataSet();
                            temp.setShip_to_party_code(RowData[0]);
                            temp.setShip_to_party_name(RowData[1]);
                            temp.setCustomer_type(RowData[2]);
                            temp.setBranch_code(RowData[3]);
                            temp.setBranch_name(RowData[4]);
                            temp.setDealer_code(RowData[5]);
                            temp.setDealer_name(RowData[6]);
                            temp.setLatitude(RowData[7]);
                            temp.setLongitude(RowData[8]);
                            temp.setDestination_name(RowData[9]);
                            double lat = 0, lng = 0;
                            try {
                                lat = Double.parseDouble(RowData[7].trim());
                            } catch (Exception ignored) {}
                            try {
                                lng = Double.parseDouble(RowData[8].trim());
                            } catch (Exception ignored) {}
                            temp.setFlag((lat == 0 || lng == 0) ? 0 : 2);
                            mNewDatabaseForSiteLead.insertShipToPartyLocation(temp);
                        }
                    }
                }
                buffer.close();
            } catch (IOException e) {
                Log.d("TAG", "_DOWNLOAD_ MarketFeedbackAddress 1: " + e.getMessage());
            }
            boolean finalResult = true;
            new android.os.Handler(android.os.Looper.getMainLooper()).post(() ->
                    callback.onComplete(finalResult)
            );
        }).start();
    }
}
