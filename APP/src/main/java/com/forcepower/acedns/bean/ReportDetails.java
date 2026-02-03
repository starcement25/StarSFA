package com.forcepower.acedns.bean;

public class ReportDetails {
    String prodCode = "";
    String qty = "";
    String amount = "";
    String invoiceNo = "";
    String invoiceAmt = "";
    String TD = "";
    String transmitted = "";

    public String getTransmitted() {
        return transmitted;
    }

    public void setTransmitted(String transmitted) {
        this.transmitted = transmitted;
    }

    public String getTD() {
        return TD;
    }

    public void setTD(String tD) {
        TD = tD;
    }

    public String getProdCode() {
        return prodCode;
    }

    public void setProdCode(String prodCode) {
        this.prodCode = prodCode;
    }

    public String getQty() {
        return qty;
    }

    public void setQty(String qty) {
        this.qty = qty;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }

    public String getInvoiceNo() {
        return invoiceNo;
    }

    public void setInvoiceNo(String invoiceNo) {
        this.invoiceNo = invoiceNo;
    }

    public String getInvoiceAmt() {
        return invoiceAmt;
    }

    public void setInvoiceAmt(String invoiceAmt) {
        this.invoiceAmt = invoiceAmt;
    }
}
