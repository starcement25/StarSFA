package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.os.AsyncTask;
import android.widget.Toast;

import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;

import static com.forcepower.acedns.util.Utils.openCatalogue;

public class FILE_CataloguePdfDownload extends
        AsyncTask<String, Void, Boolean> {

    Context mContext;
    AceDnsDatabase dbHelper;
    Boolean shouldTakeToCatalogueActivity;

    public FILE_CataloguePdfDownload(Context context, Boolean shouldTakeToCatalogueActivity) {
        this.shouldTakeToCatalogueActivity = shouldTakeToCatalogueActivity;
        this.mContext = context;
        dbHelper = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;

    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if (shouldTakeToCatalogueActivity)
            Utils.showProgressDialog(mContext, "Downloading catalogue.Please wait.");
    }

    @Override
    protected Boolean doInBackground(String... params) {

        Boolean isDownloadSuccess = false;
        try {
//			String catalogueDownloadUrl=dbHelper.getCatalogueDownloadingUrl();
            String[] CatalogueArray = Constants.catalogueorSchemeVal.split("\\^");
            File outputFile = new File(Utils.getAppStoragePath(mContext) + CatalogueArray[2] + "-" + CatalogueArray[1]);
            isDownloadSuccess = DownloadFile(Constants.menuDetailsObj.getCatalogueUrl() + Constants.nickName.toUpperCase() + "/" + URLEncoder.encode(CatalogueArray[1], "utf-8"), outputFile);
        } catch (Exception e) {
            isDownloadSuccess = false;
        }

        return isDownloadSuccess;
    }

    @Override
    protected void onPostExecute(Boolean result) {
        super.onPostExecute(result);

        if (shouldTakeToCatalogueActivity)
        {
            Utils.cancelProgressDialog();
            if (result)
            {
                openCatalogue(mContext);
            }
            else
            {
                Toast.makeText(mContext, "Catalogue Downloading Failed!", Toast.LENGTH_SHORT).show();
            }
        }


    }

    public Boolean DownloadFile(String fileURL, File directory) {
        Boolean isDownloadSuccess;
        try {

            FileOutputStream f = new FileOutputStream(directory);
            URL u = new URL(fileURL);
//			URL u = new URL(URLEncoder.encode(fileURL, "utf-8"));
            HttpURLConnection c = (HttpURLConnection) u.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.connect();

            InputStream in = c.getInputStream();

            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = in.read(buffer)) > 0) {
                f.write(buffer, 0, len1);
            }
            f.close();
            isDownloadSuccess = true;
        } catch (Exception e) {

            isDownloadSuccess = false;
        }
        return isDownloadSuccess;
    }

}
