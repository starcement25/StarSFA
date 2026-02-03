package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

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

public class TRANS_TravelFoodingLodgingAttachmentExportTask extends AsyncTask<String, Void, Void> {
    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    String source = "";
    int BUFFER = 2048;
    BufferedInputStream origin = null;
    ArrayList<String> attachmentFileNames;
    String prevResponse = "";
    boolean finish;
    String mResponse = "";

    public TRANS_TravelFoodingLodgingAttachmentExportTask(Context mContext, String source, String prevResponse, boolean isFinish) {
        this.mContext = mContext;
        dataHelperObj = new AceDnsTransactionDatabase(mContext);
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.source = source;
        this.prevResponse = prevResponse;
        this.finish = isFinish;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (finish)
            Utils.showProgressDialog(mContext, "Uploading Attachments.Please wait.");
    }

    @Override
    protected Void doInBackground(String... params) {
        attachmentFileNames = dataHelperObj.getUnuploadedSupportingAttachTable(source);
        if (attachmentFileNames.size() > 0 && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            for (int ii = 0; ii < attachmentFileNames.size(); ii++) {
                String fileName = attachmentFileNames.get(ii);
                File file = new File(Utils.getAppStoragePath(mContext), fileName);
                if (file.exists()) {
                    createZipFile(fileName);
                    HttpURLConnection connection = null;
                    DataOutputStream outputStream = null;

                    File dbFile = new File(Utils.getAppStoragePath(mContext) + source + ".zip");
                    String urlServer = BaseUrl.baseUrl + AceDnsWebServiceURL.submitTourAttachmentURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;

                    Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_TravelFoodingLodgingAttachmentExportTask: " +urlServer);

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
                        connection.setRequestProperty("Content-Type", "multipart/form-data;boundary=" + boundary);
                        outputStream = new DataOutputStream(connection.getOutputStream());
                        outputStream.writeBytes(twoHyphens + boundary + lineEnd);
                        outputStream.writeBytes("Content-Disposition: form-data; name=\"file\";filename=\"" + source + ".zip" + "\"" + lineEnd);
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
                        mResponse = sb.toString().trim();
                        if (mResponse.equalsIgnoreCase("2") || mResponse.equalsIgnoreCase("1")) {
                            dataHelperObj.UpdateSurveyImage(fileName);
                            DeleteFiles();
                            DeleteImage(fileName);
                        }

                    } catch (Exception ex) {
                        ex.printStackTrace();
                    }
                }
            }

        }
        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);

        if (finish)
        {
            try
            {
                Utils.cancelProgressDialog();
            }
            catch(Exception e)
            {
                e.printStackTrace();
            }

            if (prevResponse.equalsIgnoreCase("2")) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.putExtra("REFRESH DATA", "yes");
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            } else {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
    }

    public void createZipFile(String fileName) {
        try {
            DeleteFiles();
            FileOutputStream dest = new FileOutputStream(Utils.getAppStoragePath(mContext) + source + ".zip");
            ZipOutputStream out = new ZipOutputStream(new BufferedOutputStream(dest));
            byte data[] = new byte[BUFFER];
            FileInputStream fi = new FileInputStream(Utils.getAppStoragePath(mContext) + fileName);
            origin = new BufferedInputStream(fi, BUFFER);
            ZipEntry entry = new ZipEntry((Utils.getAppStoragePath(mContext) + fileName).substring((Utils.getAppStoragePath(mContext) + fileName).lastIndexOf("/") + 1));
            out.putNextEntry(entry);
            int count;
            while ((count = origin.read(data, 0, BUFFER)) != -1) {
                out.write(data, 0, count);
            }
            origin.close();
            out.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void DeleteFiles() {
        File zipFile = new File(Utils.getAppStoragePath(mContext) + source + ".zip");
        if (zipFile.exists()) {
            zipFile.delete();
        }
    }

    public void DeleteImage(String fileName) {
        File imagefile = new File(Utils.getAppStoragePath(mContext)
                + fileName);
        if (imagefile.exists()) {
            imagefile.delete();
        }

    }


}
