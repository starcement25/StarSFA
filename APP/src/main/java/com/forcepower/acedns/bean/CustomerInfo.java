package com.forcepower.acedns.bean;

public class CustomerInfo {

    String recommendedOrAdditional = "";
    String recommendedCustomerName = "";
    String AdditionalCustomerName = "";
    String mBaseUrl = "";


    public String getrecommendedOrAdditional() {
        return recommendedOrAdditional;
    }

    public void setrecommendedOrAdditional(String recommendedOrAdditional) {
        this.recommendedOrAdditional = recommendedOrAdditional;
    }

    public String getrecommendedCustomerName() {
        return recommendedCustomerName;
    }

    public void setrecommendedCustomerName(String recommendedCustomerName) {
        this.recommendedCustomerName = recommendedCustomerName;
    }

    public String getAdditionalCustomerName() {
        return AdditionalCustomerName;
    }

    public void setAdditionalCustomerName(String AdditionalCustomerName) {
        this.AdditionalCustomerName = AdditionalCustomerName;
    }

    public String getBaseUrl() {
        return mBaseUrl;
    }

    public void setBaseUrl(String baseUrl) {
        this.mBaseUrl = baseUrl;
    }


}
