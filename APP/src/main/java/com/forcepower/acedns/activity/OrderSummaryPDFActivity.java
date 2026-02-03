package com.forcepower.acedns.activity;

import androidx.fragment.app.FragmentActivity;

import android.app.DownloadManager;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.Utils;
import com.forcepower.acedns.constants.BaseUrl;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.text.SimpleDateFormat;
import java.util.Calendar;

public class OrderSummaryPDFActivity extends FragmentActivity {

    Button mButtonBack,mButtonDownload,buttonDownload_show,buttonDownload_share;

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase mAceDnsDatabase;
    DownloadManager manager;
    TextView txtpdf;

    private String filepath = "http://africau.edu/images/default/sample.pdf";
    private URL url = null;
    private String fileName;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_order_summary_pdfactivity);

        mButtonBack = (Button) findViewById(R.id.back);
        mButtonDownload = (Button) findViewById(R.id.buttonDownload);
        buttonDownload_show = (Button) findViewById(R.id.buttonDownload_show);
        buttonDownload_share = (Button) findViewById(R.id.buttonDownload_share);
        txtpdf = findViewById(R.id.txtpdf);
        mContext = OrderSummaryPDFActivity.this;


        filepath = BaseUrl.baseUrl + AceDnsWebServiceURL.order_summary_pdf
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();

Log.d("_DOWNLOAD_", "_DOWNLOAD_ OrderSummaryPDFActivity: " + filepath);
        try {
            url = new URL(filepath);
        } catch (MalformedURLException e) {
            e.printStackTrace();
        }

        fileName = url.getPath();
        //fileName = fileName.substring(fileName.lastIndexOf('/') + 1);
        String timeStamp = Constants.employeeDetailObject.getEmpCode() + "_" + new SimpleDateFormat("dd/MM/yyyy").format(Calendar.getInstance().getTime());
        fileName = "Transaction_" + timeStamp+".pdf";
        txtpdf.setText(fileName);

        mButtonBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();

            }
        });

        mButtonDownload.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                djob();
            }
        });

        buttonDownload_show.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                //File file=new File(Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)+"/"+fileName);
                //Uri uri= FileProvider.getUriForFile(OrderSummaryPDFActivity.this,"org.forcepower.acedns"+".provider",file);

                Intent browserIntent = new Intent(Intent.ACTION_VIEW);
                browserIntent.setDataAndType(Uri.parse(filepath),"application/pdf");
                startActivity(browserIntent);

                /*Intent i=new Intent(Intent.ACTION_VIEW);
                i.setDataAndType(Uri.parse(filepath),"application/pdf");
                i.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP|Intent.FLAG_GRANT_READ_URI_PERMISSION);
                startActivity(i);*/
            }
        });

        buttonDownload_share.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                send();
            }
        });
    }


    private void Download_txt() {

        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.order_summary_pdf
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
Log.d("_DOWNLOAD_", "_DOWNLOAD_ OrderSummaryPDFActivity: " + URL);
        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());

        HttpURLConnection c = null;
        FileOutputStream fbo = null;
        File outputFile = null;
        InputStream is = null;
        java.net.URL url = null;

        try {
            outputFile = new File(Utils.getAppStoragePath(mContext)+ timeStamp + Constants.employeeDetailObject.getEmpCode() + ".pdf");
            if (outputFile.exists())
                outputFile.delete();
            fbo = new FileOutputStream(outputFile, false);
            url = new URL(URL);
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
            }

            fbo.flush();

        } catch (Exception e) {
            Toast.makeText(mContext, ""+e.toString(), Toast.LENGTH_SHORT).show();
            e.printStackTrace();
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
            Toast.makeText(mContext, "Downloaded", Toast.LENGTH_SHORT).show();
        }
    }

    private void PdfDownload(){
        String URL = BaseUrl.baseUrl + AceDnsWebServiceURL.order_summary_pdf
                + "?nick_name=" + Constants.nickName
                + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ OrderSummaryPDFActivity: " + URL);
        manager = (DownloadManager) getSystemService(Context.DOWNLOAD_SERVICE);
        Uri uri = Uri.parse(URL);
        DownloadManager.Request request = new DownloadManager.Request(uri);
        request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE);
        long reference = manager.enqueue(request);
    }

    private void djob(){
        DownloadManager.Request request = new DownloadManager.Request(Uri.parse(url + ""));
        request.setTitle(fileName);
        request.setMimeType("applcation/pdf");
        request.allowScanningByMediaScanner();
        request.setAllowedOverMetered(true);
        request.setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED);
        request.setDestinationInExternalPublicDir(Environment.DIRECTORY_DOWNLOADS, fileName);
        DownloadManager dm = (DownloadManager) getSystemService(DOWNLOAD_SERVICE);
        dm.enqueue(request);
    }

    private void send(){
        try {
            File file=new File(Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)+"/"+fileName);
            Intent intentShare = new Intent(Intent.ACTION_SEND);
            intentShare.setType("application/pdf");
            intentShare.putExtra(Intent.EXTRA_STREAM, Uri.parse(""+file));
            startActivity(Intent.createChooser(intentShare, "Share the file ..."));
        }catch (Exception e){
            Utils.showToast(mContext,"Error to Share");
        }

    }
}