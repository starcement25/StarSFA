package com.forcepower.acedns.bean;

public class CustomerProductAllocation {
    String AllocationId = "";
    String customerCode = "";
    String prodCode = "";
    String qty;
    String fromDate = "";
    String toDate = "";
    String Acedns = "";

    public String getAllocationId() {
        return AllocationId;
    }

    public void setAllocationId(String AllocationId) {
        this.AllocationId = AllocationId;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getprodCode() {
        return prodCode;
    }

    public void setprodCode(String prodCode) {
        this.prodCode = prodCode;
    }

    public String getQty() {
        return qty;
    }

    public void setQty(String qty) {
        this.qty = qty;
    }

    public String getFromDate() {
        return fromDate;
    }

    public void setFromDate(String fromDate) {
        this.fromDate = fromDate;
    }

    public String getToDate() {
        return toDate;
    }

    public void setToDate(String toDate) {
        this.toDate = toDate;
    }

    public String getAcedns() {
        return Acedns;
    }

    public void setAcedns(String Acedns) {
        this.Acedns = Acedns;
    }
}
