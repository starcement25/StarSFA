package com.forcepower.acedns.bean;

public class LoyaltyPurchaseDetails {
    String loyaltyCardNo = "";
    String verticalName = "";
    String purchaseValue = "0.00";
    String rwrdPoint = "";
    String rdmdPoint = "";

    public String getRwrdPoint() {
        return rwrdPoint;
    }

    public void setRwrdPoint(String rwrdPoint) {
        this.rwrdPoint = rwrdPoint;
    }

    public String getRdmdPoint() {
        return rdmdPoint;
    }

    public void setRdmdPoint(String rdmdPoint) {
        this.rdmdPoint = rdmdPoint;
    }

    public String getLoyaltyCardNo() {
        return loyaltyCardNo;
    }

    public void setLoyaltyCardNo(String loyaltyCardNo) {
        this.loyaltyCardNo = loyaltyCardNo;
    }

    public String getVerticalName() {
        return verticalName;
    }

    public void setVerticalName(String verticalName) {
        this.verticalName = verticalName;
    }

    public String getPurchaseValue() {
        return purchaseValue;
    }

    public void setPurchaseValue(String purchaseValue) {
        this.purchaseValue = purchaseValue;
    }
}
