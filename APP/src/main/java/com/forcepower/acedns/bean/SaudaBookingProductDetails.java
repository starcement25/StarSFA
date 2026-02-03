package com.forcepower.acedns.bean;


public class SaudaBookingProductDetails {
    String mProductName = "";
    String mProductCode = "";
    String mQuantity = "";
    String mValidFrom = "";
    String mValue = "";

    public String getProductName() {
        return mProductName;
    }

    public void setProductName(String productName) {
        this.mProductName = productName;
    }

    public String getProductCode() {
        return mProductCode;
    }

    public void setProductCode(String productCode) {
        this.mProductCode = productCode;
    }

    public String getQuantity() {
        return mQuantity;
    }

    public void setQuantity(String quantity) {
        this.mQuantity = quantity;
    }

    public String getValidFrom() {
        return mValidFrom;
    }

    public void setValidFrom(String validFrom) {
        this.mValidFrom = validFrom;
    }

    public String getValue() {
        return mValue;
    }

    public void setValue(String mValue) {
        this.mValue = mValue;
    }

}
