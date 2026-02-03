package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.telephony.TelephonyManager;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class DATA_SubmitDBBackupTask extends AsyncTask<Void, Void, Integer> {
    private Context mContext;

    public DATA_SubmitDBBackupTask(Context context) {
        this.mContext = context;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Integer doInBackground(Void... params) {

        HttpURLConnection connection = null;
        DataOutputStream outputStream = null;
        int uploadsuccess = 0;
        TelephonyManager tm = (TelephonyManager) mContext.getApplicationContext().getSystemService(Context.TELEPHONY_SERVICE);

        File dbFile = new File(Utils.getAppStoragePath(mContext) + "AceDns.db");
        String filename = Constants.employeeDetailObject.getEmpCode() + "_" + dbFile.getName();
        filename = filename.replace('/', '_');
        String urlServer = BaseUrl.baseUrl + AceDnsWebServiceURL.dbBackUpSubmitURL + "?deviceId=" + "" + "&nick_name=" + Constants.userDetailsObj.getNickName();
        String lineEnd = "\r\n";
        String twoHyphens = "--";
        String boundary = "*****";
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_SubmitDBBackupTask: " +urlServer);

        int bytesRead, bytesAvailable, bufferSize;
        byte[] buffer;
        int maxBufferSize = 1 * 1024 * 1024;

        try {
            FileInputStream fileInputStream = new FileInputStream(dbFile);

            URL url = new URL(urlServer);
            connection = (HttpURLConnection) url.openConnection();
            connection.setDoInput(true);
            connection.setDoOutput(true);
            connection.setUseCaches(false);
            connection.setRequestMethod("POST");
            connection.setRequestProperty("Connection", "Keep-Alive");
            connection.setRequestProperty("Content-Type", "multipart/form-data;boundary=" + boundary);
            outputStream = new DataOutputStream(connection.getOutputStream());
            outputStream.writeBytes(twoHyphens + boundary + lineEnd);
            outputStream.writeBytes("Content-Disposition: form-data; name=\"file\";filename=\"" + filename + "\"" + lineEnd);
            outputStream.writeBytes(lineEnd);

            bytesAvailable = fileInputStream.available();
            bufferSize = Math.min(bytesAvailable, maxBufferSize);
            buffer = new byte[bufferSize];
            while ((bytesRead = fileInputStream.read(buffer)) != -1) {
                outputStream.write(buffer, 0, bytesRead);
            }

            outputStream.writeBytes(lineEnd);
            outputStream.writeBytes(twoHyphens + boundary + twoHyphens + lineEnd);

            fileInputStream.close();
            outputStream.flush();
            outputStream.close();

            byte buff[] = new byte[200];
            int len = 0;
            StringBuilder sb = new StringBuilder();
            InputStream ins = (InputStream) connection.getContent();

            while ((len = ins.read(buff)) != -1) {
                sb.append(new String(buff, 0, len));
            }
            Log.e("Backup return", sb.toString());
            uploadsuccess = Integer.parseInt(sb.toString().trim());
            return uploadsuccess;
        } catch (Exception ex) {
            ex.printStackTrace();
            return 0;
        }
    }

    @Override
    protected void onPostExecute(Integer result) {
        super.onPostExecute(result);
    }

}
