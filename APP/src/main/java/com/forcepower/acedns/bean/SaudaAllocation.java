package com.forcepower.acedns.bean;

public class SaudaAllocation {
    String mAllocationId = "";
    String mDate = "";
    String mEmployeeCode = "";
    String mProductFilterCode = "";
    String mProductFilterName = "";
    String mQuantityinLtr = "0";
    String mQuantityinTon = "0";
    String mQuantity = "0";
    String mAllotedQuantityinLtr = "0";
    String mAllotedQtyTonByMe = "0";
    String mConversionFactorTwo = "0";
    String mValidation = "";


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


    public String getProductFilterName() {
        return mProductFilterName;
    }

    public void setProductFilterName(String productFilterName) {
        this.mProductFilterName = productFilterName;
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


    public String getQty() {
        return mQuantity;
    }

    public void setQty(String qty) {
        this.mQuantity = qty;
    }

    public String getAllotedQuantityinLtr() {
        return mAllotedQuantityinLtr;
    }

    public void setAllotedQuantityinLtr(String allotedQuantityinton) {
        this.mAllotedQuantityinLtr = allotedQuantityinton;
    }

    public String getConversionFactorTwo() {
        return mConversionFactorTwo;
    }

    public void setConversionFactorTwo(String conversionFactorTwo) {
        this.mConversionFactorTwo = conversionFactorTwo;
    }

    public String getValidation() {
        return mValidation;
    }

    public void setValidation(String validation) {
        this.mValidation = validation;
    }

    public String getAllotedQtyTonByMe() {
        return mAllotedQtyTonByMe;
    }

    public void setAllotedQtyTonByMe(String allotedQtyTonByMe) {
        this.mAllotedQtyTonByMe = allotedQtyTonByMe;
    }

}
