package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.TransDeleteDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.parser.TransactionDeleteXMLParser;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.PhoneStateChangeListener;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class DATA_DeleteTransactionTask2 extends AsyncTask<Void, Void, Void> {
    Context mContext;
    String httpResponse = "";

    AceDnsTransactionDatabase dbHelper;
    String empCode = "";
    Dialog deletionDialog;
    boolean isMIS = false;

    public DATA_DeleteTransactionTask2(Context context, Dialog deletionDialog, boolean isMIS, String empCode) {
        this.mContext = context;
        PhoneStateChangeListener.ringing = false;
        dbHelper = new AceDnsTransactionDatabase(mContext);
        this.deletionDialog = deletionDialog;
        this.empCode = empCode;
        this.isMIS = isMIS;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
    }

    @Override
    protected Void doInBackground(Void... params) {
        try {
            loadTransactionDeleteDetails();
            long dbResult = 0;

            if (httpResponse.length() > 0 && !httpResponse.equalsIgnoreCase("Network Failure")) {
                TransactionDeleteXMLParser parser = new TransactionDeleteXMLParser(httpResponse);
                ArrayList<TransDeleteDetails> transList = parser.getParsedData();
                if (transList != null && transList.size() > 0) {
                    dbResult = dbHelper.deleteTransaction(transList, isMIS);
                    if (dbResult == 1) {
                        confirmDelete();
                    }
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        return null;
    }

    @Override
    protected void onPostExecute(Void result) {
        super.onPostExecute(result);
        Constants.transactionDelete = false;
        if (Constants.transDeleteAndRefresh && !isMIS) {
            Constants.transDeleteAndRefresh = false;
            Utils.updateEmployeeMasterDate(mContext);
            new DATA_LoadDatabaseDetails2(mContext).execute(Constants.employeeDetailObject.getEmpCode());
        }
        if (isMIS) {
            Intent intent = new Intent(mContext, MenuActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            mContext.startActivity(intent);
            ((Activity) mContext).finish();
        }
    }

    public void loadTransactionDeleteDetails() {
        ContentValues values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", empCode);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DeleteTransactionTask: " + BaseUrl.baseUrl + AceDnsWebServiceURL.transactionToDeleteURL);
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DeleteTransactionTask values: " + values);
        httpResponse = HttpCalling.httpPostCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.transactionToDeleteURL, values);
    }

    public void confirmDelete() {
        try {
            String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.transactionDeletionConfirmationURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode();
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DeleteTransactionTask: " + uri);
            Log.d("_DOWNLOAD_", "_DOWNLOAD_ DATA_DeleteTransactionTask values: " + httpResponse);
            HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, httpResponse);
        } catch (Exception e) {

        }
    }
}