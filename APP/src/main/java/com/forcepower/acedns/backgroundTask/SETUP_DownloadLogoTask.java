package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

import java.io.InputStream;

public class SETUP_DownloadLogoTask extends AsyncTask<String, Void, Long> {
    Context mContext;
    boolean isDownloadError = false;
    Bitmap logoBmp = null;

    public SETUP_DownloadLogoTask(Context context) {
        this.mContext = context;
        PhoneStateChangeListener.ringing = false;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        Utils.changeProgressDialogMsg(mContext, "Downloading Logo..");
    }

    @Override
    protected Long doInBackground(String... params) {

        String url = "";
        url = BaseUrl.baseUrl + AceDnsWebServiceURL.downloadLogoURL1 + "?nick_name=" + Constants.nickName + "&mode=SETUP";
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ SETUP_DownloadLogoTask: " +url);
        DownloadImage(url);
        Constants.logoBmp = null;
        if (logoBmp != null) {
            Constants.logoBmp = logoBmp;

        } else {
            isDownloadError = true;
        }

        long insertStatus = 0;
        return insertStatus;
    }

    @Override
    protected void onPostExecute(Long result) {
        super.onPostExecute(result);
        Utils.cancelProgressDialog();
        if (isDownloadError == true) {
            isDownloadError = false;
            Utils.directOutsideTheApplication(mContext, "Logo download unsuccessful due to poor connectivity.\nPlease retry.", false);
        } else {
            new DATA_LoadDatabaseDetails(mContext).execute("");
        }
    }

    private void DownloadImage(String urlstr) {
        try {
            InputStream inputStream = new HttpCalling().httpGetCallWithInputStreamResponse(urlstr);
            logoBmp = BitmapFactory.decodeStream(inputStream);
            if (inputStream != null) {
                inputStream.close();
            }
        } catch (Exception e) {
        }
    }
}
