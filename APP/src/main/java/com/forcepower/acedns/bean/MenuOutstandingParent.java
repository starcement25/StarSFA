package com.forcepower.acedns.bean;

public class MenuOutstandingParent {
    String customerCode = "";
    String customerName = "";
    String totalInvoice = "";
    String numberOfInvoice = "";
    String forecastAmount = "";
    String forecastId = "";
    String forecastDate = "";

    public String getNumberOfInvoice() {
        return numberOfInvoice;
    }

    public void setNumberOfInvoice(String numberOfInvoice) {
        this.numberOfInvoice = numberOfInvoice;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getCustomerName() {
        return customerName;
    }

    public void setCustomerName(String customerName) {
        this.customerName = customerName;
    }

    public String getTotalInvoice() {
        return totalInvoice;
    }

    public void setTotalInvoice(String totalInvoice) {
        this.totalInvoice = totalInvoice;
    }

    public String getForecastAmount() {
        return forecastAmount;
    }

    public void setForecastAmount(String forecastAmount) {
        this.forecastAmount = forecastAmount;
    }

    public String getForecastId() {
        return forecastId;
    }

    public void setForecastId(String forecastId) {
        this.forecastId = forecastId;
    }

    public String getForecastDate() {
        return forecastDate;
    }

    public void setForecastDate(String forecastDate) {
        this.forecastDate = forecastDate;
    }
}
