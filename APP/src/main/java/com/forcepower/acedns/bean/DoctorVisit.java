package com.forcepower.acedns.bean;

public class DoctorVisit {
    String transId = "", customer_code = "", remarks = "", other_remarks = "", met_with = "";

    public String getTransId() {
        return transId;
    }

    public void setTransId(String transId) {
        this.transId = transId;
    }

    public String getCustomer_code() {
        return customer_code;
    }

    public void setCustomer_code(String customer_code) {
        this.customer_code = customer_code;
    }

    public String getRemarks() {
        return remarks;
    }

    public void setRemarks(String remarks) {
        this.remarks = remarks;
    }

    public String getOther_remarks() {
        return other_remarks;
    }

    public void setOther_remarks(String other_remarks) {
        this.other_remarks = other_remarks;
    }

    public String getMet_with() {
        return met_with;
    }

    public void setMet_with(String met_with) {
        this.met_with = met_with;
    }
}
