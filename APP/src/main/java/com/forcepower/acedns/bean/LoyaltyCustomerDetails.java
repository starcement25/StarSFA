package com.forcepower.acedns.bean;

public class LoyaltyCustomerDetails {
    String cardHolderCode = "";
    String cardHolderName = "";
    String cardNumber = "";
    String cardType = "";
    String purchaseValue = "0.00";
    String rewardPoint = "";
    String lastUpdate = "";
    String redeemed = "";
    String phone = "";
    String address = "";
    String vehicleNo = "";
    String cardExpDate = "";

    public String getCardExpDate() {
        return cardExpDate;
    }

    public void setCardExpDate(String cardExpDate) {
        this.cardExpDate = cardExpDate;
    }

    public String getVehicleNo() {
        return vehicleNo;
    }

    public void setVehicleNo(String vehicleNo) {
        this.vehicleNo = vehicleNo;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getPhone() {
        return phone;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public String getCardType() {
        return cardType;
    }

    public void setCardType(String cardType) {
        this.cardType = cardType;
    }

    public String getCardHolderCode() {
        return cardHolderCode;
    }

    public void setCardHolderCode(String cardHolderCode) {
        this.cardHolderCode = cardHolderCode;
    }

    public String getCardHolderName() {
        return cardHolderName;
    }

    public void setCardHolderName(String cardHolderName) {
        this.cardHolderName = cardHolderName;
    }

    public String getCardNumber() {
        return cardNumber;
    }

    public void setCardNumber(String cardNumber) {
        this.cardNumber = cardNumber;
    }

    public String getPurchaseValue() {
        return purchaseValue;
    }

    public void setPurchaseValue(String purchaseValue) {
        this.purchaseValue = purchaseValue;
    }

    public String getRewardPoint() {
        return rewardPoint;
    }

    public void setRewardPoint(String rewardPoint) {
        this.rewardPoint = rewardPoint;
    }

    public String getLastUpdate() {
        return lastUpdate;
    }

    public void setLastUpdate(String lastUpdate) {
        this.lastUpdate = lastUpdate;
    }

    public String getRedeemed() {
        return redeemed;
    }

    public void setRedeemed(String redeemed) {
        this.redeemed = redeemed;
    }
}
