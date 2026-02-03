package com.forcepower.acedns.bean;

public class SaudaAllocationDetails {
    String empCode = "";
    String prodFilterCode = "";
    String qty = "";
    String bal = "0";
    String mAllotedQty = "";
    String td = "";

    public String getEmpCode() {
        return empCode;
    }

    public void setEmpCode(String empCode) {
        this.empCode = empCode;
    }

    public String getProdFilterCode() {
        return prodFilterCode;
    }

    public void setProdFilterCode(String prodFilterCode) {
        this.prodFilterCode = prodFilterCode;
    }

    public String getQty() {
        return qty;
    }

    public void setQty(String qty) {
        this.qty = qty;
    }

    public String getBal() {
        return bal;
    }

    public void setBal(String bal) {
        this.bal = bal;
    }

    public String getAllotedQty() {
        return mAllotedQty;
    }

    public void setAllotedQty(String bal) {
        this.mAllotedQty = bal;
    }

    //for TD allocation only
    public String getAllotedTD() {
        return td;
    }

    public void setAllotedTD(String td) {
        this.td = td;
    }
}
