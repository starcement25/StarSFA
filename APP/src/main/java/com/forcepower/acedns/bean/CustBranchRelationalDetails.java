package com.forcepower.acedns.bean;

public class CustBranchRelationalDetails {
    String custCode = "";
    String branchCode = "";
    String mAcedns = "";

    public synchronized String getCustCode() {
        return custCode;
    }

    public synchronized void setCustCode(String custCode) {
        this.custCode = custCode;
    }

    public synchronized String getBranchCode() {
        return branchCode;
    }

    public synchronized void setBranchCode(String branchCode) {
        this.branchCode = branchCode;
    }

    public synchronized String getAcedns() {
        return mAcedns;
    }

    public synchronized void setAcedns(String acedns) {
        this.mAcedns = acedns;
    }
}
