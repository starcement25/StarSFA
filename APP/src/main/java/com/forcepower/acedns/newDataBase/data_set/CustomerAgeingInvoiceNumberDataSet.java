package com.forcepower.acedns.newDataBase.data_set;

public class CustomerAgeingInvoiceNumberDataSet {
    String customerName, customerCode, invoiceNo, invoiceDate, invoiceValue, invoiceAge;
    public CustomerAgeingInvoiceNumberDataSet(String customerName, String customerCode, String invoiceNo, String invoiceDate, String invoiceValue, String invoiceAge) {
        this.customerName = customerName;
        this.customerCode = customerCode;
        this.invoiceNo = invoiceNo;
        this.invoiceDate = invoiceDate;
        this.invoiceValue = invoiceValue;
        this.invoiceAge = invoiceAge;
    }

    public String getCustomerName() {
        return customerName;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public String getInvoiceNo() {
        return invoiceNo;
    }

    public String getInvoiceDate() {
        return invoiceDate;
    }

    public String getInvoiceValue() {
        return invoiceValue;
    }

    public String getInvoiceAge() {
        return invoiceAge;
    }
}
