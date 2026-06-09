package com.forcepower.acedns.new_activity.customer_outstanding.dataset;

public class InvoiceDataSet {
    String invoiceNo, invoiceDate, invoiceAmount;
    int invoicePendingDays;

    public InvoiceDataSet(String invoiceNo, String invoiceDate, String invoiceAmount, int invoicePendingDays) {
        this.invoiceNo = invoiceNo;
        this.invoiceDate = invoiceDate;
        this.invoiceAmount = invoiceAmount;
        this.invoicePendingDays = invoicePendingDays;
    }

    public InvoiceDataSet() {
    }

    public String getInvoiceNo() {
        return invoiceNo;
    }

    public String getInvoiceDate() {
        return invoiceDate;
    }

    public String getInvoiceAmount() {
        return invoiceAmount;
    }

    public int getInvoicePendingDays() {
        return invoicePendingDays;
    }

    public void setInvoiceNo(String invoiceNo) {
        this.invoiceNo = invoiceNo;
    }

    public void setInvoiceDate(String invoiceDate) {
        this.invoiceDate = invoiceDate;
    }

    public void setInvoiceAmount(String invoiceAmount) {
        this.invoiceAmount = invoiceAmount;
    }

    public void setInvoicePendingDays(int invoicePendingDays) {
        this.invoicePendingDays = invoicePendingDays;
    }
}
