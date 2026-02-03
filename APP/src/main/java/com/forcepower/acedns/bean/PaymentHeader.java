package com.forcepower.acedns.bean;

public class PaymentHeader {
    String receiptId = "";
    String customerCode = "";
    double amount;
    int cashCheque;
    String chequeNo;
    String date = "";
    String bank = "";
    String rDate = "";
    int transferred;
    int flag;
    String instruction = "";
    String saleType = "";
    String customerLat = "";
    String customerLong = "";
    String customerImage = "";

    public String getSaleType() {
        return saleType;
    }

    public void setSaleType(String saleType) {
        this.saleType = saleType;
    }

    public String getReceiptId() {
        return receiptId;
    }

    public void setReceiptId(String receiptId) {
        this.receiptId = receiptId;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public double getAmount() {
        return amount;
    }

    public void setAmount(double amount) {
        this.amount = amount;
    }

    public int getCashCheque() {
        return cashCheque;
    }

    public void setCashCheque(int cashCheque) {
        this.cashCheque = cashCheque;
    }

    public String getChequeNo() {
        return chequeNo;
    }

    public void setChequeNo(String chequeNo) {
        this.chequeNo = chequeNo;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getBank() {
        return bank;
    }

    public void setBank(String bank) {
        this.bank = bank;
    }

    public void setrDate(String rDate) {
        this.rDate = rDate;
    }

    public int getTransferred() {
        return transferred;
    }

    public void setTransferred(int transferred) {
        this.transferred = transferred;
    }

    public int getFlag() {
        return flag;
    }

    public void setFlag(int flag) {
        this.flag = flag;
    }

    public String getInstruction() {
        return instruction;
    }

    public void setInstruction(String instruction) {
        this.instruction = instruction;
    }

    public String getcustomerLat() {
        return customerLat;
    }

    public void setcustomerLat(String customerLat) {
        this.customerLat = customerLat;
    }

    public String getcustomerLong() {
        return customerLong;
    }

    public void setcustomerLong(String customerLong) {
        this.customerLong = customerLong;
    }

    public String getcustomerImage() {
        return customerImage;
    }

    public void setcustomerImage(String customerImage) {
        this.customerImage = customerImage;
    }
}
