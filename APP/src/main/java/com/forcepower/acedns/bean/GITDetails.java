package com.forcepower.acedns.bean;


public class GITDetails {
    String grnNo = "", prodCode = "", despatcherCode = "", balancedRecceivedQty = "", despatchQty = "", status = "0",
            orderNumber = "", transType = "", saleRate = "", despatcherName = "";


    public String getDespatcherName() {
        return despatcherName;
    }

    public void setDespatcherName(String despatcherName) {
        this.despatcherName = despatcherName;
    }

    public String getSaleRate() {
        return saleRate;
    }

    public void setSaleRate(String saleRate) {
        this.saleRate = saleRate;
    }

    public String getTransType() {
        return transType;
    }

    public void setTransType(String transType) {
        this.transType = transType;
    }

    public String getDespatchQty() {
        return despatchQty;
    }

    public void setDespatchQty(String despatchQty) {
        this.despatchQty = despatchQty;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getOrderNumber() {
        return orderNumber;
    }

    public void setOrderNumber(String orderNumber) {
        this.orderNumber = orderNumber;
    }

    public String getGrnNo() {
        return grnNo;
    }

    public void setGrnNo(String grnNo) {
        this.grnNo = grnNo;
    }

    public String getProdCode() {
        return prodCode;
    }

    public void setProdCode(String prodCode) {
        this.prodCode = prodCode;
    }

    public String getDespatcherCode() {
        return despatcherCode;
    }

    public void setDespatcherCode(String despatcherCode) {
        this.despatcherCode = despatcherCode;
    }

    public String getBalancedRecceivedQty() {
        return balancedRecceivedQty;
    }

    public void setBalancedRecceivedQty(String balancedRecceivedQty) {
        this.balancedRecceivedQty = balancedRecceivedQty;
    }
}
