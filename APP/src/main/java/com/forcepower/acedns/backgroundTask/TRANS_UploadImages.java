package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.Utils;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;

public class TRANS_UploadImages extends AsyncTask<String, Void, Void> {
    ArrayList<String> mFileNamesList;
    String mLastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    String mResponse = "";
    String mSource = "";
    int BUFFER = 2048;
    boolean isFinish;
    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    BufferedInputStream mBufferedInputStream = null;
    ProgressDialog mProgressDialog;

    public TRANS_UploadImages(Context mContext, String source, String prevResponse, boolean isFinish) {
        this.mContext = mContext;
        mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mLastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
        this.mSource = source;
        this.isFinish = isFinish;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Uploading Data.\nPlease wait..");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
    }

    @Override
    protected Void doInBackground(String... params) {
        mFileNamesList = mAceDnsTransactionDatabase.getUnuploadedSupportingAttachTable(mSource);
        if (mFileNamesList.size() > 0 && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            CreateZipFile();
            HttpURLConnection connection = null;
            DataOutputStream outputStream = null;

            File dbFile = new File(Utils.getAppStoragePath(mContext) + mSource + ".zip");
            String urlServer = BaseUrl.baseUrl
                    + AceDnsWebServiceURL.submitTourAttachmentURL
                    + "?nick_name=" + Constants.nickName + "&emp_code="
                    + Constants.employeeDetailObject.getEmpCode()
                    + "&last_update_time=" + mLastUpdate;


            Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_UploadImages: " +urlServer);


            String lineEnd = "\r\n";
            String twoHyphens = "--";
            String boundary = "*****";

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
                connection.setRequestProperty("Content-Type",
                        "multipart/form-data;boundary=" + boundary);
                outputStream = new DataOutputStream(
                        connection.getOutputStream());
                outputStream.writeBytes(twoHyphens + boundary + lineEnd);
                outputStream
                        .writeBytes("Content-Disposition: form-data; name=\"file\";filename=\""
                                + mSource + ".zip" + "\"" + lineEnd);
                outputStream.writeBytes(lineEnd);

                bytesAvailable = fileInputStream.available();
                bufferSize = Math.min(bytesAvailable, maxBufferSize);
                buffer = new byte[bufferSize];
                while ((bytesRead = fileInputStream.read(buffer)) != -1) {
                    outputStream.write(buffer, 0, bytesRead);
                }

                outputStream.writeBytes(lineEnd);
                outputStream.writeBytes(twoHyphens + boundary + twoHyphens
                        + lineEnd);

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
                mResponse = sb.toString().trim();
                Log.i("Response ", String.valueOf(connection.getResponseCode()));
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        }
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        //mProgressDialog.cancel();
        if (isFinish) {
            Toast.makeText(mContext, "Data submitted successfully", Toast.LENGTH_LONG).show();
            Constants.isSurveyImageTake = false;
            if (mResponse.equalsIgnoreCase("2") || mResponse.equalsIgnoreCase("1")) {
                mAceDnsTransactionDatabase.UpdateSurveyImage();
                DeleteFiles();
                DeleteImage();
                if (mResponse.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            } else {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
    }

    public void CreateZipFile() {
        try {
            FileOutputStream dest = new FileOutputStream(Utils.getAppStoragePath(mContext) + mSource + ".zip");
            ZipOutputStream out = new ZipOutputStream(new BufferedOutputStream(dest));
            byte data[] = new byte[BUFFER];
            for (int ii = 0; ii < mFileNamesList.size(); ii++) {
                FileInputStream fi = new FileInputStream(Utils.getAppStoragePath(mContext) + mFileNamesList.get(ii));
                mBufferedInputStream = new BufferedInputStream(fi, BUFFER);
                ZipEntry entry = new ZipEntry((Utils.getAppStoragePath(mContext) + mFileNamesList.get(ii)).substring((Utils.getAppStoragePath(mContext) + mFileNamesList.get(ii)).lastIndexOf("/") + 1));
                out.putNextEntry(entry);
                int count;
                while ((count = mBufferedInputStream.read(data, 0, BUFFER)) != -1) {
                    out.write(data, 0, count);
                }
            }
            mBufferedInputStream.close();
            out.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void DeleteFiles() {
        File zipFile = new File(Utils.getAppStoragePath(mContext) + mSource + ".zip");
        if (zipFile.exists()) {
            zipFile.delete();
        }
    }

    public void DeleteImage() {
        if (mFileNamesList.size() > 0) {
            for (int count = 0; count < mFileNamesList.size(); count++) {
                File imagefile = new File(Utils.getAppStoragePath(mContext)
                        + mFileNamesList.get(count));
                if (imagefile.exists()) {
                    imagefile.delete();
                }
            }
        }
    }

}
