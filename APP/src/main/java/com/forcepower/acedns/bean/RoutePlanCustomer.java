package com.forcepower.acedns.bean;

public class RoutePlanCustomer {
    String tranSactionId = "";
    String routeCode = "";
    String visitDate = "";
    String customerCode = "";
    String status = "";

    public String getStatus() {
        return status;
    }

    public void setStatus(String satatus) {
        this.status = satatus;
    }

    public String getTranSactionId() {
        return tranSactionId;
    }

    public void setTranSactionId(String tranSactionId) {
        this.tranSactionId = tranSactionId;
    }

    public String getRouteCode() {
        return routeCode;
    }

    public void setRouteCode(String routeCode) {
        this.routeCode = routeCode;
    }

    public String getVisitDate() {
        return visitDate;
    }

    public void setVisitDate(String visitDate) {
        this.visitDate = visitDate;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

}
