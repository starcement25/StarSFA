package com.forcepower.acedns.bean;


public class SaudaBookingProductGroupDetails {
    String mProductGroupName = "";
    String mProductGroupCode = "";
    String mQuantity = "";
    String mValue = "";

    public String getProductGroupName() {
        return mProductGroupName;
    }

    public void setProductGroupName(String productGroupName) {
        this.mProductGroupName = productGroupName;
    }

    public String getProductGroupCode() {
        return mProductGroupCode;
    }

    public void setProductGroupCode(String productGroupCode) {
        this.mProductGroupCode = productGroupCode;
    }

    public String getQuantity() {
        return mQuantity;
    }

    public void setQuantity(String quantity) {
        this.mQuantity = quantity;
    }

    public String getValue() {
        return mValue;
    }

    public void setValue(String mValue) {
        this.mValue = mValue;
    }
}
