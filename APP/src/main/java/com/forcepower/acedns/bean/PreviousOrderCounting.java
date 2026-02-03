package com.forcepower.acedns.bean;

public class PreviousOrderCounting {
    String mCustomerCode = "";
    String mProductCode = "";
    String mVisitDetails = "";

    public String getCustomerCode() {
        return mCustomerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.mCustomerCode = customerCode;
    }

    public String getProductCode() {
        return mProductCode;
    }

    public void setProductCode(String brokername) {
        this.mProductCode = brokername;
    }

    public String getVisitDetails() {
        return mVisitDetails;
    }

    public void setVisitDetails(String visitDetails) {
        this.mVisitDetails = visitDetails;
    }
}
