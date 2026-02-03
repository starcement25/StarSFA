package com.forcepower.acedns.bean;

public class SaudaAllocationLog {
    String mAllocationId = "";
    String mDate = "";
    String mEmployeeCode = "";
    String mProductFilterCode = "";
    String mQuantityinLtr = "0";
    String mQuantityinTon = "0";

    public String getAllocationId() {
        return mAllocationId;
    }

    public void setAllocationId(String allocationId) {
        this.mAllocationId = allocationId;
    }

    public String getDate() {
        return mDate;
    }

    public void setDate(String date) {
        this.mDate = date;
    }

    public String getEmployeCode() {
        return mEmployeeCode;
    }

    public void setEmployeCode(String employeCode) {
        this.mEmployeeCode = employeCode;
    }


    public String getProductFilterCode() {
        return mProductFilterCode;
    }

    public void setProductFilterCode(String productFilterCode) {
        this.mProductFilterCode = productFilterCode;
    }

    public String getQuantityinLtr() {
        return mQuantityinLtr;
    }

    public void setQuantityinLtr(String quantityinLtr) {
        this.mQuantityinLtr = quantityinLtr;
    }

    public String getQuantityinTon() {
        return mQuantityinTon;
    }

    public void setQuantityinTon(String quantityinTon) {
        this.mQuantityinTon = quantityinTon;
    }

}
