package com.forcepower.acedns.bean;

public class CustomerProductRelationDetails {
    String productCode = "";
    String customerCode = "";
    String acedns = "";
    String mcxRateParam = "";
    String premium = "";
    String TD = "";

    public String getcustomerCode() {
        return customerCode;
    }

    public void setcustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getProductCode() {
        return productCode;
    }

    public void setProductCode(String productCode) {
        this.productCode = productCode;
    }

    public String getacedns() {
        return acedns;
    }

    public void setacedns(String acedns) {
        this.acedns = acedns;
    }

    public String getmcxRateParam() {
        return mcxRateParam;
    }

    public void setmcxRateParam(String mcxRateParam) {
        this.mcxRateParam = mcxRateParam;
    }

    public String getpremium() {
        return premium;
    }

    public void setpremium(String premium) {
        this.premium = premium;
    }

    public String getTD() {
        return TD;
    }

    public void setTD(String TD) {
        this.TD = TD;
    }
}
