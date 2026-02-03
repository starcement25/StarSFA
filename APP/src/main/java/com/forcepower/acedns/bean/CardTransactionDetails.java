package com.forcepower.acedns.bean;

public class CardTransactionDetails {
    String transactionId = "";
    String cardNumber = "";
    String outletCode = "";
    String purchaseValue = "";
    String verticalName = "";
    String vehicleType = "";
    String vehicleNo = "";
    String pointsEarned = "";
    String pointsRedeemed = "";
    String flag = "";
    String rdsCode = "";

    public String getRdsCode() {
        return rdsCode;
    }

    public void setRdsCode(String rdsCode) {
        this.rdsCode = rdsCode;
    }

    public String getFlag() {
        return flag;
    }

    public void setFlag(String flag) {
        this.flag = flag;
    }

    public String getPointsEarned() {
        return pointsEarned;
    }

    public void setPointsEarned(String pointsEarned) {
        this.pointsEarned = pointsEarned;
    }

    public String getPointsRedeemed() {
        return pointsRedeemed;
    }

    public void setPointsRedeemed(String pointsRedeemed) {
        this.pointsRedeemed = pointsRedeemed;
    }

    public String getVehicleType() {
        return vehicleType;
    }

    public void setVehicleType(String vehicleType) {
        this.vehicleType = vehicleType;
    }

    public String getVehicleNo() {
        return vehicleNo;
    }

    public void setVehicleNo(String vehicleNo) {
        this.vehicleNo = vehicleNo;
    }

    public final String getVerticalName() {
        return verticalName;
    }

    public final void setVerticalName(String verticalName) {
        this.verticalName = verticalName;
    }

    public final String getTransactionId() {
        return transactionId;
    }

    public final void setTransactionId(String transactionId) {
        this.transactionId = transactionId;
    }

    public final String getCardNumber() {
        return cardNumber;
    }

    public final void setCardNumber(String cardNumber) {
        this.cardNumber = cardNumber;
    }

    public final String getOutletCode() {
        return outletCode;
    }

    public final void setOutletCode(String outletCode) {
        this.outletCode = outletCode;
    }

    public final String getPurchaseValue() {
        return purchaseValue;
    }

    public final void setPurchaseValue(String purchaseValue) {
        this.purchaseValue = purchaseValue;
    }
}
