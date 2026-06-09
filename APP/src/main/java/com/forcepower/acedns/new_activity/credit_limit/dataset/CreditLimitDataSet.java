package com.forcepower.acedns.new_activity.credit_limit.dataset;

public class CreditLimitDataSet {
    String customerCode;
    String customerName;
    String creditLimit;
    String utilisedCL;
    String availableCL;

    public CreditLimitDataSet(String customerCode, String customerName, String creditLimit, String utilisedCL, String availableCL) {
        this.customerCode = customerCode;
        this.customerName = customerName;
        this.creditLimit = creditLimit;
        this.utilisedCL = utilisedCL;
        this.availableCL = availableCL;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public String getCustomerName() {
        return customerName;
    }

    public String getCreditLimit() {
        return creditLimit;
    }

    public String getUtilisedCL() {
        return utilisedCL;
    }

    public String getAvailableCL() {
        return availableCL;
    }
}
