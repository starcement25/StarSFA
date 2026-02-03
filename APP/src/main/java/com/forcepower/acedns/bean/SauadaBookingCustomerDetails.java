package com.forcepower.acedns.bean;

public class SauadaBookingCustomerDetails {
    String mCustomerName = "";
    String mCustomerCode = "";
    String mQuantityBooked = "";
    String mValue = "";

    public String getCustomerName() {
        return mCustomerName;
    }

    public void setCustomerName(String customerName) {
        this.mCustomerName = customerName;
    }

    public String getCustomerCode() {
        return mCustomerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.mCustomerCode = customerCode;
    }

    public String getQuantityBooked() {
        return mQuantityBooked;
    }

    public void setQuantityBooked(String quantityBooked) {
        this.mQuantityBooked = quantityBooked;
    }

    public String getValue() {
        return mValue;
    }

    public void setValue(String mValue) {
        this.mValue = mValue;
    }


}
