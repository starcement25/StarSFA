package com.forcepower.acedns.backgroundTask;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ContentValues;
import android.content.Context;
import android.os.AsyncTask;
import android.util.Log;

import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitCustomerClassUpdationTask extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase dataHelperObj;
    ProgressDialog pd;
    Boolean shouldFinish = false;
    ArrayList<commonDatabaseHelper> unUploadedTransaction;
    public TRANS_SubmitCustomerClassUpdationTask(Context context, Boolean shouldFinish) {
        this.mContext = context;
        this.shouldFinish = shouldFinish;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
         unUploadedTransaction = dataHelperObj.getUnuploadedCustomerClassUpdateList();
    }

    @Override
    protected void onPreExecute()
    {
        super.onPreExecute();
        if (shouldFinish) {
            pd = new ProgressDialog(mContext);
            pd.setMessage("Submitting transaction.Please wait");
            pd.setCancelable(false);
            pd.show();
        }
    }

    @Override
    protected String doInBackground(String... params)
    {
        String POST_result = "";
        if(unUploadedTransaction.size()>0)
        {
            String customerData="";
            for(int i=0;i<unUploadedTransaction.size();i++)
            {
                if(customerData.matches(""))
                {
                    customerData=unUploadedTransaction.get(i).getItem0()+"#"+unUploadedTransaction.get(i).getItem1();
                }
                else
                {
                    customerData=customerData+";"+unUploadedTransaction.get(i).getItem0()+"#"+unUploadedTransaction.get(i).getItem1();
                }
            }


                if (HTTPUtils.isConnectionPossible(mContext) )
                {
                    try
                    {
                        String uri = BaseUrl.baseUrl +
                                AceDnsWebServiceURL.submitClassCastUpdation
                                + "?nick_name=" + Constants.nickName
                                + "&emp_code="
                                + Constants.employeeDetailObject.getEmpCode();
                        ContentValues values = new ContentValues();
                        values.put("cust_class", customerData);
                        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCustomerClassUpdationTask: " +uri);
                        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCustomerClassUpdationTask value: " +values);
                        POST_result = HttpCalling.httpPostCallWithXmlResponse(uri, values);
                    } catch (Exception e) {
                        POST_result = "Network Failure";
                    } finally {
                    }
                }

        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitCustomerClassUpdationTask result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (unUploadedTransaction.size()>0)
        {
            if (result.length() == 1)
            {
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2"))
                {
                    dataHelperObj.updateUploadedCustomerFlag(unUploadedTransaction);
                }
            }

        }
        dataHelperObj.closeDatabase();
        if (shouldFinish) {
            pd.cancel();
            Activity activity = (Activity) mContext;
            activity.finish();
        }

    }
}
