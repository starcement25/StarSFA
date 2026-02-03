package com.forcepower.acedns.bean;

public class BillingInformationStockSummaryData {
    String productCode = "";
    String productName = "";
    String customerCode = "";
    String customerName = "";
    String imei = "";
    String stock_out_date = "";
    String sold_out_id = "";
    String stock = "";
    String invoiceDate = "";
    String qty = "";
    String stkOutCustomerCode = "";
    String activationDate = "";

    public String getcustomerCode() {
        return customerCode;
    }

    public void setcustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getProductCode() {
        return productCode;
    }

    public void setProductCode(String productCode) {
        this.productCode = productCode;
    }

    public String getimei() {
        return imei;
    }

    public void setimei(String iemi) {
        this.imei = iemi;
    }

    public String getcustomerName() {
        return customerName;
    }

    public void setcustomerName(String customerName) {
        this.customerName = customerName;
    }

    public String getproductName() {
        return productName;
    }

    public void setproductName(String productName) {
        this.productName = productName;
    }

    public String getsold_out_date() {
        return stock_out_date;
    }

    public void setsold_out_date(String stock_out_date) {
        this.stock_out_date = stock_out_date;
    }

    public String getsold_out_id() {
        return sold_out_id;
    }

    public void setsold_out_id(String sold_out_id) {
        this.sold_out_id = sold_out_id;
    }

    public String getstock() {
        return stock;
    }

    public void setstock(String stock) {
        this.stock = stock;
    }

    public String getinvoiceDate() {
        return invoiceDate;
    }

    public void setinvoiceDate(String invoiceDate) {
        this.invoiceDate = invoiceDate;
    }

    public String getqty() {
        return qty;
    }

    public void setqty(String qty) {
        this.qty = qty;
    }

    public String getstkOutCustomerCode() {
        return stkOutCustomerCode;
    }

    public void setstkOutCustomerCode(String stkOutCustomerCode) {
        this.stkOutCustomerCode = stkOutCustomerCode;
    }

    public String getactivationDate() {
        return activationDate;
    }

    public void setactivationDate(String activationDate) {
        this.activationDate = activationDate;
    }
}
