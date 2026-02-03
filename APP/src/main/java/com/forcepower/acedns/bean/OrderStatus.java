package com.forcepower.acedns.bean;

public class OrderStatus {
    String mOrderNo = "";
    String mCustomerCode = "";
    String mProductCode = "";
    String mProductName = "";
    String mOrderQuantity = "";
    String mAlreadyDeliveredQuantity = "";
    String mCurrentDeliveredQuantity = "";
    String mStatus = "";
    String mRemarks = "";
    String mFlag = "";
    String rate = "";
    String amount = "";
    String weightage = "";

    public String getOrderNo() {
        return mOrderNo;
    }

    public void setOrderNo(String orderNo) {
        this.mOrderNo = orderNo;
    }

    public String getCustomerCode() {
        return mCustomerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.mCustomerCode = customerCode;
    }

    public String getProductCode() {
        return mProductCode;
    }

    public void setProductCode(String productCode) {
        this.mProductCode = productCode;
    }

    public String getProductName() {
        return mProductName;
    }

    public void setProductName(String productName) {
        this.mProductName = productName;
    }

    public String getOrderQuantity() {
        return mOrderQuantity;
    }

    public void setOrderQuantity(String orderQuantity) {
        this.mOrderQuantity = orderQuantity;
    }

    public String getAlreadyDeliveredQuantity() {
        return mAlreadyDeliveredQuantity;
    }

    public void setAlreadyDeliveredQuantity(String deliveredQuantity) {
        this.mAlreadyDeliveredQuantity = deliveredQuantity;
    }

    public String getCurrentDeliveredQuantity() {
        return mCurrentDeliveredQuantity;
    }

    public void setCurrentDeliveredQuantity(String deliveredQuantity) {
        this.mCurrentDeliveredQuantity = deliveredQuantity;
    }

    public String getStatus() {
        return mStatus;
    }

    public void setStatus(String status) {
        this.mStatus = status;
    }

    public String getRemarks() {
        return mRemarks;
    }

    public void setRemarks(String remarks) {
        this.mRemarks = remarks;
    }

    public String getFlag() {
        return mFlag;
    }

    public void setFlag(String flag) {
        this.mFlag = flag;
    }

    public String getrate() {
        return rate;
    }

    public void setrate(String rate) {
        this.rate = rate;
    }

    public String getamount() {
        return amount;
    }

    public void setamount(String amount) {
        this.amount = amount;
    }

    public String getweightage() {
        return weightage;
    }

    public void setweightage(String weightage) {
        this.weightage = weightage;
    }
}
