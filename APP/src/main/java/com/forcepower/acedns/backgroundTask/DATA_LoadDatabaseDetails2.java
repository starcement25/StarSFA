package com.forcepower.acedns.backgroundTask;

import static com.forcepower.acedns.constants.Constants.isLARAVELAPI;

import android.app.Activity;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.util.Log;
import android.view.View;
import android.view.Window;
import android.widget.Button;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.DatabaseStructure;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.parser.DatabaseDetailsXMLParsing;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

import java.io.ByteArrayOutputStream;
import java.util.ArrayList;

public class DATA_LoadDatabaseDetails2 extends AsyncTask<String, Void, Long> {
    Context mContext;
    String mHttpResponse = "";
    String mBaseUrlChanged = "";
    String mEmployeeCode = "";

    ArrayList<DatabaseStructure> mDatabaseStructureList;

    AceDnsDatabase mAceDnsDatabase;
    boolean isDataDownload = true;

    public DATA_LoadDatabaseDetails2(Context context) {
        this.mContext = context;
        PhoneStateChangeListener.ringing = false;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Long doInBackground(String... params) {
        mEmployeeCode = params[0];
        if (mEmployeeCode.trim().length() > 0) {

        } else {
            if (Constants.employeeDetailObject != null) {
                mEmployeeCode = Constants.employeeDetailObject.getEmpCode();
            }
        }
        loadDatabaseDetails();
        long dbResult = 0;
        if (mHttpResponse.length() > 0 && !mHttpResponse.equalsIgnoreCase("Network Failure")) {
            DatabaseDetailsXMLParsing parser = new DatabaseDetailsXMLParsing(mHttpResponse);
            mDatabaseStructureList = parser.getParsedData();
            if (mDatabaseStructureList != null && mDatabaseStructureList.size() > 0) {
                Constants.isEmpMasterLoginUpdated = false;
                dbResult = mAceDnsDatabase.createAppTables(mDatabaseStructureList);
                Constants.mDBVersion = mDatabaseStructureList.get(mDatabaseStructureList.size() - 1).getDbVersion();
                mBaseUrlChanged = mDatabaseStructureList.get(mDatabaseStructureList.size() - 1).getBaseUrlChanged();
            } else {
                dbResult = 1;   //DB has already been created. No response for Table Structure.
            }
            if (Constants.dbDeleteCheckStatus.equalsIgnoreCase("1")) {
                showAppCloseDialog();
            }
            mAceDnsDatabase.closeDatabase();
            try {
                mAceDnsDatabase = new AceDnsDatabase(mContext);
            } catch (Exception e) {
                e.printStackTrace();

            }
            try {
                if (mDatabaseStructureList != null && mDatabaseStructureList.size() > 0) {
                    InsertToAppInfo(mDatabaseStructureList.get(mDatabaseStructureList.size() - 1).getDbVersion());
                }
            } catch (Exception e) {
                e.printStackTrace();

            }

            mAceDnsDatabase.closeDatabase();
        }
        return dbResult;
    }

    @Override
    protected void onPostExecute(Long result) {
        super.onPostExecute(result);
        Log.d("TAG", "CheckNickName: onPostExecute  DATA_LoadDatabaseDetails  "+mHttpResponse);
        if (Constants.isEmpMasterLoginUpdated && isLARAVELAPI) {
            try {
                Utils.insertToEmployeeMaster(mContext);
                Constants.isEmpMasterLoginUpdated = false;
            } catch (Exception e) {

            }

        }
        if (result > 0) {
            if (Constants.dataResfresh == true) {
                Log.d("TAG", "CheckNickName: ?_?_?"+"5.1.4");
                Constants.dataResfresh = false;
                new AUTH_UpdateCheck2(mContext, isDataDownload).execute(mEmployeeCode);
            } else {
                if (Constants.isDbCheckinStatus == true) {
                    Constants.isDbCheckinStatus = false;
                } else {
                    ((Activity) mContext).finish();
                }
            }
        } else if (mHttpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                Log.d("TAG", "CheckNickName: "+"Network Failure");
                new DATA_LoadDatabaseDetails2(mContext).execute();
            }
        }
    }

    public void loadDatabaseDetails() {
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", mEmployeeCode);
        if (Constants.isFirstLoginOfApp) {
            values.put("device_id", "");
            values.put("mode", "INSTALL");
        } else if (Constants.dbDeleteCheckStatus.equalsIgnoreCase("1")) {
            values.put("device_id", "");
            values.put("mode", "INSTALL");
        } else {
            values.put("device_id", Constants.deviceId);
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_LoadDatabaseDetails: " + BaseUrl.baseUrl + AceDnsWebServiceURL.databaseDetailsURL);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_LoadDatabaseDetails: " + AceDnsWebServiceURL.parentURL + AceDnsWebServiceURL.databaseDetailsURL);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_LoadDatabaseDetails values: " + values);
        mHttpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.databaseDetailsURL, values);
    }

    public void InsertToAppInfo(String dbVersion) {
        byte[] byteArray = null;
        if (Constants.logoBmp != null) {
            ByteArrayOutputStream stream = new ByteArrayOutputStream();
            Constants.logoBmp.compress(Bitmap.CompressFormat.PNG, 100, stream);
            byteArray = stream.toByteArray();
        } else {
            ByteArrayOutputStream stream = new ByteArrayOutputStream();
            Constants.logoBmp.compress(Bitmap.CompressFormat.PNG, 100, stream);
            byteArray = stream.toByteArray();
        }
        String appVersion = Utils.getAppVersion(mContext);
        mAceDnsDatabase.insertOrUpdateAppInfo(Constants.nickName, appVersion, dbVersion, byteArray, mBaseUrlChanged);
    }

    public void showAppCloseDialog() {
        final Dialog appCloseDialog = new Dialog(mContext);
        appCloseDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        appCloseDialog.setContentView(R.layout.printing_dialog);
        appCloseDialog.setCancelable(false);
        TextView title = (TextView) appCloseDialog.findViewById(R.id.title);
        title.setText("Database deleted to maintain data integrity.\n Please Login again.");
        Button yes = (Button) appCloseDialog.findViewById(R.id.btn_yes);
        Button no = (Button) appCloseDialog.findViewById(R.id.btn_no);
        no.setVisibility(View.GONE);
        yes.setText("OK");
        yes.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                appCloseDialog.cancel();
                RegisterActivities.removeAllActivities();
            }
        });

        appCloseDialog.show();
    }
}
