package com.forcepower.acedns.newDataBase.data_set;

public class CustomerCompetitorQuantityDataSet {
    String competitorQuantityId,  customerCode,  customerName,  mandatory,  competitorName,  type,  quantity, customerDnsCode;

    public String getCustomerName() {
        return customerName;
    }

    public void setCustomerName(String customerName) {
        this.customerName = customerName;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getCompetitorName() {
        return competitorName;
    }

    public void setCompetitorName(String competitorName) {
        this.competitorName = competitorName;
    }

    public String getQuantity() {
        return quantity;
    }

    public void setQuantity(String quantity) {
        this.quantity = quantity;
    }

    public String getCompetitorQuantityId() {
        return competitorQuantityId;
    }

    public void setCompetitorQuantityId(String competitorQuantityId) {
        this.competitorQuantityId = competitorQuantityId;
    }

    public String getMandatory() {
        return mandatory;
    }

    public void setMandatory(String mandatory) {
        this.mandatory = mandatory;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getCustomerDnsCode() {
        return customerDnsCode;
    }

    public void setCustomerDnsCode(String customerDnsCode) {
        this.customerDnsCode = customerDnsCode;
    }
}
