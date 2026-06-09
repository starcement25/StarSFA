package com.forcepower.acedns.util;

import android.content.ContentValues;
import android.content.Context;
import android.graphics.Bitmap;
import android.util.Log;

import com.forcepower.acedns.parser.MarketFeedbackDetailsXMLParser;
import com.forcepower.acedns.parser.MenuDetailsXMLParsing;
import com.forcepower.acedns.parser.OrderFormDetailsXMLParsing;
import com.forcepower.acedns.parser.ProductDetailsXMLParsing;
import com.forcepower.acedns.parser.RoutePlanDetailsXMLParsing;
import com.forcepower.acedns.parser.SaudaFormDetailsXMLParser;
import com.forcepower.acedns.parser.SelfAppraisalDetailsXMLParser;
import com.forcepower.acedns.parser.SurveyFormDetailsXMLParser;
import com.forcepower.acedns.parser.UserDetailsXMLParsing;
import com.forcepower.acedns.bean.RoutePlanDetails;
import com.forcepower.acedns.constants.AceDnsWebServiceURL;
import com.forcepower.acedns.constants.BaseUrl;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;

import java.io.ByteArrayOutputStream;

/**
 * Created by Intellij Amiyo  on 20-07-2017.
 * Please follow standard Java coding conventions.
 * <a href="http://source.android.com/source/code-style.html">...</a>
 */
public class commonAsyncTaskSETUP {
    Context mContext;
    String httpResponse = "";
    ContentValues values;
    AceDnsDatabase mAceDnsDatabase;
    String lastUpdate;
    String dwnldDictTime; // Just to know the format
    String mIsInCremental = "", status;

    public commonAsyncTaskSETUP(Context context, String getNAME) {
        this.mContext = context;
        this.status = getNAME;
        mAceDnsDatabase = new AceDnsDatabase(mContext);
        PhoneStateChangeListener.ringing = false;
        lastUpdate = mAceDnsDatabase.getlastDownloadTime("menu_details");
        dwnldDictTime = mAceDnsDatabase.getlastDownloadTime("download_dictionary");

        switch (status) {
            case "menu_details" -> _DOWNLOAD_menu_details();
            case "user_details" -> _DOWNLOAD_user_details();
            case "order_details" -> _DOWNLOAD_order_details();
            case "product_details" -> _DOWNLOAD_product_details();
            case "route_plan_details" -> _DOWNLOAD_route_plan_details();
            case "sauda_form_details" -> _DOWNLOAD_sauda_form_details();
            case "self_appraisal_details" -> _DOWNLOAD_self_appraisal_details();
            case "survey_form_details" -> _DOWNLOAD_survey_form_details();
            case "market_feedback_details" -> _DOWNLOAD_market_feedback_details();
        }
    }

    public void _DOWNLOAD_market_feedback_details() {
        InCrementalDownload_market_feedback_details();
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.marketFeedbackDetailsURL, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            MarketFeedbackDetailsXMLParser parser = new MarketFeedbackDetailsXMLParser(httpResponse);
            Constants.marketFeedbackDetailsObj = parser.getParsedData();
            if (Constants.marketFeedbackDetailsObj != null) {
                long insertStatus = mAceDnsDatabase.InsertToMarketFeedbackDetails(Constants.marketFeedbackDetailsObj);
                if (insertStatus == 1) {
                    Constants.isMarketFeedbackDetailsUpdated = false;
                    decideNavigation_market_feedback_details();
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_survey_form_details() {
        Log.d("TAG", "_DOWNLOAD_survey_form_details: Calling ");
        InCrementalDownload_survey_form_details();
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.surveyFormDetailsURL, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            SurveyFormDetailsXMLParser parser = new SurveyFormDetailsXMLParser(httpResponse);
            Constants.surveyFormDetailsObj = parser.getParsedData();
            if (Constants.surveyFormDetailsObj != null) {
                long insertStatus = mAceDnsDatabase.InsertToSurveyFormDetails(Constants.surveyFormDetailsObj);
                if (insertStatus == 1) {
                    Constants.isSurveyFormDetailsUpdated = false;
                    decideNavigation_survey_form_details();
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_self_appraisal_details() {
        InCrementalDownload_self_appraisal_details();
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.selfAppraisalDetails, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            SelfAppraisalDetailsXMLParser parser = new SelfAppraisalDetailsXMLParser(httpResponse);
            Constants.selfAppraisalDetails = parser.getParsedData();
            if (Constants.selfAppraisalDetails != null) {
                long insertStatus = mAceDnsDatabase.insertToSelfAppraisalDetails(Constants.selfAppraisalDetails);
                if (insertStatus == 1) {
                    Constants.isSelfAppraisalDetailsTableUpdated = false;
                    decideNavigation_self_appraisal_details();
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_sauda_form_details() {
        InCrementalDownload_sauda_form_details();
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.saudaFormDetailsURL, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            SaudaFormDetailsXMLParser parser = new SaudaFormDetailsXMLParser(httpResponse);
            Constants.saudaFormDetailsObj = parser.getParsedData();
            if (Constants.saudaFormDetailsObj != null) {
                long insertStatus = mAceDnsDatabase.insertToSaudaFormDetails(Constants.saudaFormDetailsObj);
                if (insertStatus == 1) {
                    Constants.isSaudaFormDetailsUpdated = false;
                    decideNavigation_sauda_form_details();
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_route_plan_details() {
        InCrementalDownload_route_plan_details();
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.routePlanDetailsURL, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            RoutePlanDetailsXMLParsing parser = new RoutePlanDetailsXMLParsing(httpResponse);
            RoutePlanDetails routeObj = parser.getParsedData();
            if (routeObj != null) {
                long insertStatus = mAceDnsDatabase.insertToRoutePlanDetails(routeObj);
                if (insertStatus == 1) {
                    Constants.isRoutePlanDetailsUpdated = false;
                    decideNavigation_route_plan_details(routeObj.getLastUpdateTime());
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_product_details() {
        InCrementalDownload_product_details();
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.productDetailsURL, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            ProductDetailsXMLParsing parser = new ProductDetailsXMLParsing(httpResponse);
            Constants.productDetailsObj = parser.getParsedData();
            if (Constants.productDetailsObj != null) {
                long insertStatus = mAceDnsDatabase.insertToProductDetails(Constants.productDetailsObj);
                if (insertStatus == 1) {
                    Constants.isProductDetailsUpdated = false;
                    decideNavigation_product_details();
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_order_details() {
        InCrementalDownload_order_details();
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.orderFormDetailsURL, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            OrderFormDetailsXMLParsing parser = new OrderFormDetailsXMLParsing(httpResponse);
            Constants.orderFormDetailsObj = parser.getParsedData();
            if (Constants.orderFormDetailsObj != null) {
                long insertStatus = mAceDnsDatabase.insertToOrderFormDetails(Constants.orderFormDetailsObj);
                if (insertStatus == 1) {
                    Constants.isOrderFormDetailsUpdated = false;
                    decideNavigation_order_details();
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void _DOWNLOAD_user_details() {
        InCrementalDownload_user_details();
        Log.d("TAG", "_DOWNLOAD_user_details: " + Constants.nickName);
        Log.d("TAG", "_DOWNLOAD_user_details: " + Constants.employeeDetailObject.getEmpCode());
        Log.d("TAG", "_DOWNLOAD_user_details: " + "SETUP");
        Log.d("TAG", "_DOWNLOAD_user_details: " + mIsInCremental);
        Log.d("TAG", "_DOWNLOAD_user_details: " + lastUpdate);
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.userDetailsURL, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            UserDetailsXMLParsing parser = new UserDetailsXMLParsing(httpResponse);
            Constants.userDetailsObj = parser.getParsedData();
            if (Constants.userDetailsObj != null) {
                if (Constants.logoBmp != null) {
                    ByteArrayOutputStream stream = new ByteArrayOutputStream();
                    Constants.logoBmp.compress(Bitmap.CompressFormat.PNG, 100, stream);
                    byte[] byteArray = stream.toByteArray();
                    Constants.userDetailsObj.setImgArray(byteArray);
                    long insertStatus = mAceDnsDatabase.insertToUserDetails(Constants.userDetailsObj);
                    if (insertStatus == 1) {
                        decideNavigation_user_details();
                        Constants.isUserDetailsUpdated = false;
                    } else {
                        Constants.isDownLoadComplete = false;
                    }
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
                Utils.directOutsideTheApplication(mContext, "Network Failure while downloading UserDetails", true);
            }
        }
    }

    public void _DOWNLOAD_menu_details() {
        InCrementalDownload();
        commonNameValuePair();
        httpResponse = HttpCalling.httpGetCallWithXmlResponse(BaseUrl.baseUrl + AceDnsWebServiceURL.menuDetailsURL, values);
        if (!httpResponse.isEmpty() && !httpResponse.equalsIgnoreCase("Network Failure")) {
            MenuDetailsXMLParsing parser = new MenuDetailsXMLParsing(httpResponse);
            Constants.menuDetailsObj = parser.getParsedData();
            if (Constants.menuDetailsObj != null) {
                long insertStatus = mAceDnsDatabase.InsertToMenuDetails(Constants.menuDetailsObj);
                if (insertStatus == 1) {
                    Constants.isMenuDetailsUpdated = false;
                    decideNavigation_menu_details();
                } else {
                    Constants.isDownLoadComplete = false;
                }
            } else {
                Constants.isDownLoadComplete = false;
            }
        } else if (httpResponse.equalsIgnoreCase("Network Failure")) {
            if (PhoneStateChangeListener.ringing) {
                new commonAsyncTaskSETUP(mContext, status);
            } else {
                Constants.isDownLoadComplete = false;
            }
        }
    }

    public void commonNameValuePair() {
        values = new ContentValues();
        values.put("nick_name", Constants.nickName);
        values.put("emp_code", Constants.employeeDetailObject.getEmpCode());
        values.put("mode", "SETUP");
        values.put("incremental_download", mIsInCremental);
        values.put("last_update_time", lastUpdate);
        Log.d("TAG", "commonNameValuePair: " + values);
    }

    public void InCrementalDownload_market_feedback_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isMarketFeedbackDetailsUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrementalDownload_survey_form_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSurveyFormDetailsUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrementalDownload_self_appraisal_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSelfAppraisalDetailsTableUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrementalDownload_sauda_form_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isSaudaFormDetailsUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrementalDownload_route_plan_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isRoutePlanDetailsUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrementalDownload() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isMenuDetailsUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrementalDownload_user_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isUserDetailsUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrementalDownload_order_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isOrderFormDetailsUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }

    public void InCrementalDownload_product_details() {
        if ((Constants.isFirstLoginOfApp) || (Constants.isProductDetailsUpdated)) {
            mIsInCremental = "no";
        } else {
            mIsInCremental = "yes";
        }
    }


    public void decideNavigation_menu_details() {

        mAceDnsDatabase.insertToLogTable(Constants.menuDetailsObj.getLastUpdateTime(), "menu_details");
        mAceDnsDatabase.closeDatabase();
    }

    public void decideNavigation_user_details() {
        mAceDnsDatabase.insertToLogTable(Constants.userDetailsObj.getLastUpdateTime(), "user_details");
        mAceDnsDatabase.closeDatabase();
    }

    public void decideNavigation_order_details() {
        mAceDnsDatabase.insertToLogTable(Constants.orderFormDetailsObj.getLastUpdateTime(), "order_form_details");
        mAceDnsDatabase.closeDatabase();
    }

    public void decideNavigation_product_details() {
        mAceDnsDatabase.insertToLogTable(lastUpdate, "product_details");
        mAceDnsDatabase.closeDatabase();
    }

    public void decideNavigation_route_plan_details(String timeOBJ) {
        mAceDnsDatabase.insertToLogTable(timeOBJ, "route_plan_details");
        mAceDnsDatabase.closeDatabase();
    }

    public void decideNavigation_sauda_form_details() {
        mAceDnsDatabase.insertToLogTable(lastUpdate, "sauda_form_details");
        mAceDnsDatabase.closeDatabase();
    }

    public void decideNavigation_self_appraisal_details() {
        mAceDnsDatabase.insertToLogTable(lastUpdate, "self_appraisal_details");
        mAceDnsDatabase.closeDatabase();
    }

    public void decideNavigation_survey_form_details() {
        mAceDnsDatabase.insertToLogTable(lastUpdate, "survey_form_details");
        mAceDnsDatabase.closeDatabase();
    }

    public void decideNavigation_market_feedback_details() {
        try {
            mAceDnsDatabase.insertToLogTable(Constants.menuDetailsObj.getLastUpdateTime(), "market_feedback_details");
            mAceDnsDatabase.closeDatabase();
        } catch (Exception ignored) {
        }
    }
}
