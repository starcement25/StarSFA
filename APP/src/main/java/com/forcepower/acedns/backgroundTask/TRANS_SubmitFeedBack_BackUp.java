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
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.HttpCalling;

import java.util.ArrayList;

public class TRANS_SubmitFeedBack_BackUp extends AsyncTask<String, Void, String> {

    Context mContext;
    String httpResponse = "";
    AceDnsTransactionDatabase dataHelperObj;
    AceDnsDatabase dataObj;
    String xmlData = "";
    boolean finish = false;
    String lastUpdate = "2014-06-09 18:19:20"; // Just to know the format
    ProgressDialog pd;
    String str_status;

    public TRANS_SubmitFeedBack_BackUp(Context context, boolean finish, String status) {
        this.mContext = context;
        this.dataHelperObj = new AceDnsTransactionDatabase(mContext);
        this.dataObj = new AceDnsDatabase(mContext);
        this.finish = finish;
        lastUpdate = dataHelperObj.getlastDownloadTime("download_dictionary");
        this.str_status = status;
    }

    @Override
    protected void onPreExecute() {
        super.onPreExecute();

        if (str_status.equals("SUBMIT")) {
            pd = new ProgressDialog(mContext);
            pd.setMessage("Submitting transaction.Please wait");
            pd.setCancelable(false);
            pd.show();
        }
        xmlData = XmlData();
    }

    @Override
    protected String doInBackground(String... params) {
        String POST_result = "";
        if (HTTPUtils.isConnectionPossible(mContext) && !Constants.employeeDetailObject.getEmpCode().startsWith("C")) {
            try {
                String uri = BaseUrl.baseUrl +
                        AceDnsWebServiceURL.submitFeedBackURL
                        + "?nick_name=" + Constants.nickName
                        + "&emp_code="
                        + Constants.employeeDetailObject.getEmpCode()
                        + "&last_update_time=" + lastUpdate;
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitFeedBack_BackUp: " +uri);
                Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitFeedBack_BackUp value: " +xmlData);
                POST_result = HttpCalling.httpPostCallWithXmlBodyXmlResponseDecrypted(uri, xmlData);
            } catch (Exception e) {
                POST_result = "Network Failure";
            } finally {
            }
        }
        Log.d("_DOWNLOAD_", "_DOWNLOAD_ TRANS_SubmitFeedBack_BackUp result: " +POST_result);
        return POST_result;
    }

    @Override
    protected void onPostExecute(String result) {
        super.onPostExecute(result);
        if (str_status.equals("SUBMIT")) {
            pd.cancel();
            if (result.length() > 0) {
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                    dataHelperObj.UpdateFeedbackLocationData();
                    dataHelperObj.closeDatabase();
                    if (result.equalsIgnoreCase("2")) {
                        Constants.dataResfresh = true;
                    }
                    if (Constants.marketFeedbackDetailsObj.getMfSubMenuImage().equalsIgnoreCase("yes")) {
                        if (true == Constants.isSurveyImageTake) {
                            new TRANS_UploadImages(mContext, "MFS", result, finish).execute();
                        } else {
                            Intent intent = new Intent(mContext, MenuActivity.class);
                            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                            mContext.startActivity(intent);
                        }
                    } else {
                        Intent intent = new Intent(mContext, MenuActivity.class);
                        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                        mContext.startActivity(intent);
                    }
                } else {
                    dataHelperObj.closeDatabase();
                    Toast.makeText(mContext, Constants.deleveryFailedMsg, Toast.LENGTH_SHORT).show();
                    Intent intent = new Intent(mContext, MenuActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    mContext.startActivity(intent);
                }
            }
        } else {
            if (result.length() > 0) {
                if (result.equalsIgnoreCase("1") || result.equalsIgnoreCase("2")) {
                    dataHelperObj.UpdateFeedbackLocationData();
                    dataHelperObj.closeDatabase();
                }
            }
        }

    }

    private String XmlData() {
        String xmlData = "";
        AceDnsTransactionDatabase dataHelperObj = new AceDnsTransactionDatabase(mContext);
        xmlData = "<?xml version='1.0' encoding='UTF-8'?><root>";

        String ss = Constants.menuDetailsObj.getFeedback_backup();
        String[] splittedParam= ss.split(",");

        ArrayList<Location> unUploadedTransaction = dataHelperObj.getUnuploadedTransactionBackUp(""+splittedParam[0], ""+splittedParam[1]);
        for (int ii = 0; ii < unUploadedTransaction.size(); ii++) {
            Location currentLocation = unUploadedTransaction.get(ii);
            String location = "<location>" +
                    "<emp_code><![CDATA[" + currentLocation.getEmpCode() + "]]></emp_code>" +
                    "<trans_id><![CDATA[" + currentLocation.getTransId() + "]]></trans_id>" +
                    "<latt><![CDATA[" + currentLocation.getLatitude() + "]]></latt>" +
                    "<longi><![CDATA[" + currentLocation.getLongitude() + "]]></longi>" +
                    "<date><![CDATA[" + currentLocation.getDate() + "]]></date>" +
                    "<purpose_of_visit><![CDATA[" + currentLocation.getPurpose_of_visit() + "]]></purpose_of_visit>" +
                    "</location>";
            if (currentLocation.getTransId().startsWith("MF")) {
                xmlData += "<MARKET_FEEDBACK>";
                xmlData += location;
                ArrayList<MarketFeedback> marketFeedbackList = dataHelperObj.getUnuploadedFeedBackData(currentLocation.getTransId());
                for (int jj = 0; jj < marketFeedbackList.size(); jj++) {
                    MarketFeedback obj = new MarketFeedback();
                    obj = marketFeedbackList.get(jj);
                    dataObj.GetMarketFeedbackDetails();
                    if(Constants.marketFeedbackDetailsObj.getEx_for().toLowerCase().contains("yes")) {
                        String bex = "0", bfor = "0", wex = "0", wfor = "0",rex = "0", rfor = "0", nex = "0", nfor = "0";
                        if (obj.getmBillingExFor().toLowerCase().matches("for")) {
                            bex = "0";
                            bfor = obj.getPtd().toString();
                        } else {
                            bex = obj.getPtd().toString();
                            bfor = "0";
                        }
                        if (obj.getmWspExFor().toLowerCase().matches("for")) {
                            wex = "0";
                            wfor = obj.getPtr().toString();
                        } else {
                            wex = obj.getPtr().toString();
                            wfor = "0";
                        }

                        if (obj.getmRspExFor().toLowerCase().matches("for")) {
                            rex = "0";
                            rfor = obj.getPtc().toString();
                        } else {
                            rex = obj.getPtc().toString();
                            rfor = "0";
                        }
                        if (obj.getmNodExFor().toLowerCase().matches("for")) {
                            nex = "0";
                            nfor = obj.getPv().toString();
                        } else {
                            nex = obj.getPv().toString();
                            nfor = "0";
                        }

                        xmlData += "<MARKET_FEEDBACK_DATA>";
                        xmlData += "<MARKET_FEEDBACK_ID><![CDATA[" + obj.getFeedbackID() + "]]></MARKET_FEEDBACK_ID>"
                                + "<route_code><![CDATA[" + obj.getRouteCode() + "]]></route_code>"
                                + "<customer_code><![CDATA[" + obj.getCustomerCode() + "]]></customer_code>"
                                + "<PRODUCT_GROUP><![CDATA[" + obj.getProductGroup() + "]]></PRODUCT_GROUP>"
                                + "<COMPETITOR_NAME><![CDATA[" + obj.getCopmpetitorName().toUpperCase() + "]]></COMPETITOR_NAME>"
                                + "<PTD><![CDATA[" + bex + "]]></PTD>"
                                + "<PTR><![CDATA[" + wex + "]]></PTR>"
                                + "<PTC><![CDATA[" + rex + "]]></PTC>"
                                + "<PV><![CDATA[" + nex + "]]></PV>"
                                + "<BILLING_EX_FOR><![CDATA[" + bfor + "]]></BILLING_EX_FOR>"
                                + "<WSP_EX_FOR><![CDATA[" + wfor + "]]></WSP_EX_FOR>"
                                + "<RSP_EX_FOR><![CDATA[" + rfor + "]]></RSP_EX_FOR>"
                                + "<NOD_EX_FOR><![CDATA[" + nfor + "]]></NOD_EX_FOR>";
                        xmlData += "</MARKET_FEEDBACK_DATA>";
                    }else{
                        xmlData += "<MARKET_FEEDBACK_DATA>";
                        xmlData += "<MARKET_FEEDBACK_ID><![CDATA[" + obj.getFeedbackID() + "]]></MARKET_FEEDBACK_ID>"
                                + "<route_code><![CDATA[" + obj.getRouteCode() + "]]></route_code>"
                                + "<customer_code><![CDATA[" + obj.getCustomerCode() + "]]></customer_code>"
                                + "<PRODUCT_GROUP><![CDATA[" + obj.getProductGroup() + "]]></PRODUCT_GROUP>"
                                + "<COMPETITOR_NAME><![CDATA[" + obj.getCopmpetitorName().toUpperCase() + "]]></COMPETITOR_NAME>"
                                + "<PTD><![CDATA[" + obj.getPtd() + "]]></PTD>"
                                + "<PTR><![CDATA[" + obj.getPtr() + "]]></PTR>"
                                + "<PTC><![CDATA[" + obj.getPtc() + "]]></PTC>"
                                + "<PV><![CDATA[" + obj.getPv() + "]]></PV>"
                                + "<BILLING_EX_FOR><![CDATA[" + obj.getmBillingExFor() + "]]></BILLING_EX_FOR>"
                                + "<WSP_EX_FOR><![CDATA[" + obj.getmWspExFor() + "]]></WSP_EX_FOR>"
                                + "<RSP_EX_FOR><![CDATA[" + obj.getmRspExFor() + "]]></RSP_EX_FOR>"
                                + "<NOD_EX_FOR><![CDATA[" + obj.getmNodExFor() + "]]></NOD_EX_FOR>";
                        xmlData += "</MARKET_FEEDBACK_DATA>";
                    }
                }
                xmlData += "</MARKET_FEEDBACK>";
            } else if (currentLocation.getTransId().startsWith("A")) {
                xmlData += "<attendance>";
                xmlData += location;
                ArrayList<Attendance> unUploadedAttendance = dataHelperObj.getUnuploadedAttendance(currentLocation.getTransId());
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
        dataHelperObj.closeDatabase();
        return xmlData;
    }

}
