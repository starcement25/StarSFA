package com.forcepower.acedns.bean;

public class MarketFeedback {
    String mFeedbackID = "";
    String mProductGroup = "";
    String mRouteCode = "";
    String mCopmpetitorName = "";
    String mPtd = "";
    String mPtr = "";
    String mPtc = "";
    String mPv = "";
    String mCustomerCode = "";
    String mBillingExFor = "";
    String mWspExFor = "";
    String mRspExFor = "";
    String mNodExFor = "";
    String mProductType = "";

    public String getFeedbackID() {
        return mFeedbackID;
    }

    public void setFeedbackID(String feedbackID) {
        this.mFeedbackID = feedbackID;
    }

    public String getProductGroup() {
        return mProductGroup;
    }

    public void setProductGroup(String productGroup) {
        this.mProductGroup = productGroup;
    }

    public String getCopmpetitorName() {
        return mCopmpetitorName;
    }

    public void setCopmpetitorName(String copmpetitorName) {
        this.mCopmpetitorName = copmpetitorName;
    }

    public String getRouteCode() {
        return mRouteCode;
    }

    public void setRouteCode(String routeCode) {
        this.mRouteCode = routeCode;
    }

    public String getPtd() {
        return mPtd;
    }

    public void setPtd(String ptd) {
        this.mPtd = ptd;
    }

    public String getPtr() {
        return mPtr;
    }

    public void setPtr(String ptr) {
        this.mPtr = ptr;
    }

    public String getPtc() {
        return mPtc;
    }

    public void setPtc(String ptc) {
        this.mPtc = ptc;
    }

    public String getPv() {
        return mPv;
    }

    public void setPv(String pv) {
        this.mPv = pv;
    }

    public String getCustomerCode() {
        return mCustomerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.mCustomerCode = customerCode;
    }

    public String getmBillingExFor() {
        return mBillingExFor;
    }

    public void setmBillingExFor(String mBillingExFor) {
        this.mBillingExFor = mBillingExFor;
    }

    public String getmWspExFor() {
        return mWspExFor;
    }

    public void setmWspExFor(String mWsp) {
        this.mWspExFor = mWsp;
    }

    public String getmProductType() {
        return mProductType;
    }

    public void setmProductType(String mProductType) {
        this.mProductType = mProductType;
    }

    public String getmRspExFor() {
        if (mRspExFor != null) {
            return mRspExFor;
        } else {
            mRspExFor = "";
        }
        return mRspExFor;
    }

    public void setmRspExFor(String mRspExFor) {
        this.mRspExFor = mRspExFor;
    }

    public String getmNodExFor() {
        if (mNodExFor == null) {
            mNodExFor = "";
        }
        return mNodExFor;
    }

    public void setmNodExFor(String mNodExFor) {
        this.mNodExFor = mNodExFor;
    }
}
