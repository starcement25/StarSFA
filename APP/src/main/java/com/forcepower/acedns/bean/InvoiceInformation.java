package com.forcepower.acedns.bean;

public class InvoiceInformation {
    String II_InvoiceNo = "";
    String II_InvoiceDate = "";
    String II_OrderNo = "";
    String II_CustomerCode = "";
    String II_Flag = "";
    String ChronologicalNumber = "";
    String II_FreightCharge = "";

    public String getInvoiceNo() {
        return II_InvoiceNo;
    }

    public void setInvoiceNo(String II_InvoiceNo) {
        this.II_InvoiceNo = II_InvoiceNo;
    }

    public String getInvoiceDate() {
        return II_InvoiceDate;
    }

    public void setInvoiceDate(String II_InvoiceDate) {
        this.II_InvoiceDate = II_InvoiceDate;
    }

    public String getOrderNo() {
        return II_OrderNo;
    }

    public void setOrderNo(String II_OrderNo) {
        this.II_OrderNo = II_OrderNo;
    }

    public String getCustomerCode() {
        return II_CustomerCode;
    }

    public void setCustomerCode(String II_CustomerCode) {
        this.II_CustomerCode = II_CustomerCode;
    }

    public String getFlag() {
        return II_Flag;
    }

    public void setFlag(String II_Flag) {
        this.II_Flag = II_Flag;
    }

    public String getChronologicalNumber() {
        return ChronologicalNumber;
    }

    public void setChronologicalNumber(String ChronologicalNumber) {
        this.ChronologicalNumber = ChronologicalNumber;
    }

    public String getFreightCharge() {
        return II_FreightCharge;
    }

    public void setFreightCharge(String II_FreightInvoiceNo) {
        this.II_FreightCharge = II_FreightInvoiceNo;
    }
}
