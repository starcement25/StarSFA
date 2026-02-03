package com.forcepower.acedns.bean;

public class StockAuditDetails {
    String transactionId = "";
    String customerCode = "";
    String productCode = "";
    String quantity = "";
    String productMrp = "";
    String productDetails = "";
    String remarks = "";
    String flag = "";
    String ReturnOrderNumber = "";
    String ReturnReason = "";
    String mfgDate = "";
    String uom = "";
    String weightage = "";


    public String getProductDetails() {
        return productDetails;
    }

    public void setProductDetails(String productDetails) {
        this.productDetails = productDetails;
    }

    public String getProductMrp() {
        return productMrp;
    }

    public void setProductMrp(String productMrp) {
        this.productMrp = productMrp;
    }

    public String getRemarks() {
        return remarks;
    }

    public void setRemarks(String remarks) {
        this.remarks = remarks;
    }

    public String getTransactionId() {
        return transactionId;
    }

    public void setTransactionId(String transactionId) {
        this.transactionId = transactionId;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getProductCode() {
        return productCode;
    }

    public void setProductCode(String productCode) {
        this.productCode = productCode;
    }

    public String getQuantity() {
        return quantity;
    }

    public void setQuantity(String quantity) {
        this.quantity = quantity;
    }

    public String getFlag() {
        return flag;
    }

    public void setFlag(String flag) {
        this.flag = flag;
    }

    public String getReturnOrderNumber() {
        return ReturnOrderNumber;
    }

    public void setReturnOrderNumber(String ReturnOrderNumber) {
        this.ReturnOrderNumber = ReturnOrderNumber;
    }

    public String getReturnReason() {
        return ReturnReason;
    }

    public void setReturnReason(String ReturnReason) {
        this.ReturnReason = ReturnReason;
    }

    public String getmfgDate() {
        return mfgDate;
    }

    public void setmfgDate(String mfgDate) {
        this.mfgDate = mfgDate;
    }

    public String getuom() {
        return uom;
    }

    public void setuom(String uom) {
        this.uom = uom;
    }

    public String getWeightage() {
        return weightage;
    }

    public void setWeightage(String weightage) {
        this.weightage = weightage;
    }
}
