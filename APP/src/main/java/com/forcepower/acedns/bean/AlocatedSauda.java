package com.forcepower.acedns.bean;

public class AlocatedSauda {
    String mEmployeeCode = "";
    String mProductFilterCode = "";
    String mQuantity = "";
    String mBalance = "0";

    public synchronized String getEmployeCode() {
        return mEmployeeCode;
    }

    public synchronized void setEmployeCode(String employeCode) {
        this.mEmployeeCode = employeCode;
    }

    public synchronized String getProductFilterCode() {
        return mProductFilterCode;
    }

    public synchronized void setProductFilterCode(String productFilterCode) {
        this.mProductFilterCode = productFilterCode;
    }

    public synchronized String getQty() {
        return mQuantity;
    }

    public synchronized void setQty(String qty) {
        this.mQuantity = qty;
    }

    public synchronized String getBalance() {
        return mBalance;
    }

    public synchronized void setBalance(String balance) {
        this.mBalance = balance;
    }
}
