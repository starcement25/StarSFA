package com.forcepower.acedns.backgroundTask;

import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Handler;
import android.os.Looper;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.ActivityOrderFilter;
import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;
import com.forcepower.acedns.util.UploadUnuploadedData;
import com.forcepower.acedns.util.Utils;

import static com.forcepower.acedns.constants.Constants.isCheckInToNewCustomer;

public class TRANS_SubmitNewCustomerDetailsTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String xmlData = "";
    boolean finish = false;
    boolean isOrder = false;
    String lastUpdate = "2014-06-09 18:19:20"; //Just to know the format
    String str_status = "";

    public TRANS_SubmitNewCustomerDetailsTask(Context context, boolean finish, boolean isOrder, String get_Status) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        this.isOrder = isOrder;
        this.str_status = get_Status;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    public TRANS_SubmitNewCustomerDetailsTask(Context context, boolean finish, String get_Status) {
        this.mContext = context;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        this.finish = finish;
        this.str_status = get_Status;
        lastUpdate = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }


    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        if(Constants.orderFormDetailsObj.getAddCustomerDetails().equalsIgnoreCase("customize")){
            xmlData = UploadUnuploadedData.PrepareXMLForNewCustomerCustomize(mContext);
        }
        else{
            xmlData = UploadUnuploadedData.PrepareXMLForNewCustomer(mContext);
        }

        if (str_status.equals("SYNC")) {
            Handler h = new Handler(Looper.getMainLooper());
            h.post(new Runnable() {
                public void run() {
                    Utils.showProgressDialog(mContext, "Uploading data.Please wait.");
                }
            });

        }


    }

    @Override
    protected String doInBackground(String... params) {

        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext)) {
            try {

                String uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNewCustomerURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                if(Constants.orderFormDetailsObj.getAddCustomerDetails().equalsIgnoreCase("customize")){
                    uri = BaseUrl.baseUrl + AceDnsWebServiceURL.submitNewCustomerCustomizeURL + "?nick_name=" + Constants.nickName + "&emp_code=" + Constants.employeeDetailObject.getEmpCode() + "&last_update_time=" + lastUpdate;
                }

                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewCustomerDetailsTask: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewCustomerDetailsTask value: " +xmlData);

                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitNewCustomerDetailsTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);

        if (str_status.equals("SYNC")) {
            Utils.cancelProgressDialog();

            if (this.isOrder) {
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                    mAceDnsTransactionDatabase.UpdateCustomerLocationData();
                    mAceDnsTransactionDatabase.updateUnuploadedCustomer();
                    mAceDnsTransactionDatabase.closeDatabase();
                    new TRANS_SubmitOrderTask(mContext, true).execute();
                } else {
                    mAceDnsTransactionDatabase.closeDatabase();
                    Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_LONG).show();
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            } else if (Constants.isOrederToNewCustomer) {
                Constants.isOrederToNewCustomer = false;
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                    mAceDnsTransactionDatabase.UpdateCustomerLocationData();
                    mAceDnsTransactionDatabase.updateUnuploadedCustomer();
                }
                mAceDnsTransactionDatabase.closeDatabase();
                Intent intent;
                if (isCheckInToNewCustomer) {
                    intent = new Intent(mContext, MenuActivity.class);
                    isCheckInToNewCustomer = false;
                } else {
                    intent = new Intent(mContext, ActivityOrderFilter.class);
                }
                intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                mContext.startActivity(intent);

            } else {
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                    mAceDnsTransactionDatabase.UpdateCustomerLocationData();
                    mAceDnsTransactionDatabase.updateUnuploadedCustomer();
                    mAceDnsTransactionDatabase.closeDatabase();
                    if (finish) {
                        if (result.equalsIgnoreCase("2")) {
                            Constants.dataResfresh = true;
                            Intent intent = new Intent(mContext, MenuActivity.class);
                            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                            mContext.startActivity(intent);
                        } else {
                            Intent intent = new Intent(mContext, MenuActivity.class);
                            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                            mContext.startActivity(intent);
                        }
                    }
                } else {
                    mAceDnsTransactionDatabase.closeDatabase();
                    if (finish) {
                        Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                        Intent intent = new Intent(mContext, MenuActivity.class);
                        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                        mContext.startActivity(intent);
                    }
                }
            }

        } else {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.UpdateCustomerLocationData();
                mAceDnsTransactionDatabase.updateUnuploadedCustomer();
            }
            mAceDnsTransactionDatabase.closeDatabase();
        }


    }

}
