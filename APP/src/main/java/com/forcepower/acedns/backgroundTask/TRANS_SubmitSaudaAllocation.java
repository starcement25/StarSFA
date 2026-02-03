package com.forcepower.acedns.backgroundTask;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.util.Log;
import android.widget.Toast;

import com.forcepower.acedns.activity.non_auth.main.MenuActivity;
import com.forcepower.acedns.bean.Attendance;
import com.forcepower.acedns.bean.Location;
import com.forcepower.acedns.bean.SaudaAllocation;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitSaudaAllocation extends AsyncTask<String, Void, String> {

    Context mContext;
    AceDnsTransactionDatabase mAceDnsTransactionDatabase;
    String mXmlData = "";
    String mLastDownloadTime = "2014-06-09 18:19:20"; // Just to know the format
    boolean finish = false;
    ProgressDialog mProgressDialog;

    public TRANS_SubmitSaudaAllocation(Context context, boolean finish) {
        this.mContext = context;
        this.finish = finish;
        this.mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(mContext);
        mLastDownloadTime = mAceDnsTransactionDatabase.getlastDownloadTime("download_dictionary");
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        mProgressDialog = new ProgressDialog(mContext);
        mProgressDialog.setMessage("Uploading Data.Please wait");
        mProgressDialog.setCancelable(false);
        mProgressDialog.show();
    }

    @Override
    protected String doInBackground(String... params) {
        mXmlData = XmlData();
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitSaudaAllocation
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + mLastDownloadTime;

                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSaudaAllocation: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSaudaAllocation value: " +mXmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, mXmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {

            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitSaudaAllocation result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        mProgressDialog.cancel();
        if (result.length() > 0) {
            if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                mAceDnsTransactionDatabase.UpdateSaudaAllocationLocationData();
                mAceDnsTransactionDatabase.closeDatabase();
                if (result.equalsIgnoreCase("2")) {
                    Constants.dataResfresh = true;
                }
                Toast.makeText(mContext, "Sauda allocation submitted successfully", 15000)
                        .show();
            } else {
                mAceDnsTransactionDatabase.closeDatabase();
                Toast.makeText(mContext, Constants.deleveryFailedMsg, 15000)
                        .show();
            }
            if (finish) {
                Intent intent = new Intent(mContext, MenuActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                mContext.startActivity(intent);
            }
        }
    }

    private String XmlData() {
        String xmlData = "";
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";
        ArrayList<Location> unUploadedTransaction = mAceDnsTransactionDatabase.getUnuploadedTransaction("", "");
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "</location>";
            if (currentLocation.getTransId().startsWith("FA")) {
                xmlData += "<sauda_allocation>";
                xmlData += location;
                ArrayList<SaudaAllocation> saudaAllocationList = mAceDnsTransactionDatabase.GetUnuploadedSaudaAllocationData(currentLocation.getTransId());
                for (int jj = 0; jj < saudaAllocationList.size(); jj++) {
                    SaudaAllocation obj = new SaudaAllocation();
                    obj = saudaAllocationList.get(jj);
                    xmlData += "<sauda_allocation_details>";
                    xmlData += "<allocation_id><![CDATA[" + obj.getAllocationId() + "]]></allocation_id>"
                            + "<allocation_date><![CDATA[" + obj.getDate() + "]]></allocation_date>"
                            + "<emp_code><![CDATA[" + obj.getEmployeCode() + "]]></emp_code>"
                            + "<product_filter_code><![CDATA[" + obj.getProductFilterCode() + "]]></product_filter_code>"
                            + "<qty><![CDATA[" + obj.getQty() + "]]></qty>";
                    xmlData += "</sauda_allocation_details>";
                }
                xmlData += "</sauda_allocation>";
            } else if (currentLocation.getTransId().startsWith("A")) {
                xmlData += "<attendance>";
                xmlData += location;
                ArrayList<Attendance> unUploadedAttendance = mAceDnsTransactionDatabase.getUnuploadedAttendance(currentLocation.getTransId());
                for (int jj = 0; jj < unUploadedAttendance.size(); jj++) {
                    Attendance detailsObj = unUploadedAttendance.get(jj);
                    xmlData += "<attendancedata>"
                            + "<emp_code><![CDATA[" + detailsObj.getEmpCode() + "]]></emp_code>"
                            + "<date><![CDATA[" + detailsObj.getDate() + "]]></date>" +
                            "</attendancedata>";
                }
                xmlData += "</attendance>";
            }
        }
        xmlData += "</root>";
        return xmlData;
    }
	
	/*private String SetAllotedQtyInTon(String allotedqtyinltr,String productfiltercode){
		double qtyinltr=0;
		double conversionfactortwo=0;
		double qtyinton=0;
		for(int count=0; count<Constants.mSaudaProductConversionList.size();count++){
			if(Constants.mSaudaProductConversionList.get(count).getProductGroupCode().equalsIgnoreCase(productfiltercode)){
				conversionfactortwo=Double.parseDouble(Constants.mSaudaProductConversionList.get(count).getConverSionFactor());
				qtyinltr=Double.parseDouble(allotedqtyinltr);
				if(conversionfactortwo>0){
					qtyinton=qtyinltr/conversionfactortwo;
					return String.valueOf(qtyinton);
				}
			}
		}
		return String.valueOf(qtyinton);
	}*/

}
