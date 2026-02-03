package com.forcepower.acedns.bean;

public class CustomerProductWiseOrderPlanDetails {
    String cutomerCode = "";
    String prodCode = "";
    String month = "";
    String purchase = "";
    String plan = "";

    public void setcutomerCode(String cutomerCode) {
        this.cutomerCode = cutomerCode;
    }

    public String getcustomerCode() {
        return cutomerCode;
    }

    public void setProdCode(String prodCode) {
        this.prodCode = prodCode;
    }

    public String geteProdCode() {
        return prodCode;
    }

    public String getMonth() {
        return month;
    }

    public void setMonth(String month) {
        this.month = month;
    }

    public String getPurchase() {
        return purchase;
    }

    public void setPurchase(String purchase) {
        this.purchase = purchase;
    }

    public String getPlan() {
        return plan;
    }

    public void setPlan(String plan) {
        this.plan = plan;
    }
}
