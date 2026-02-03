package com.forcepower.acedns.new_activity.khoj.data_set;

import org.json.JSONObject;

public class SiteDataSet {
    String siteId,routeId,customerNumber,siteName;
    JSONObject dataSet;

    public String getSiteId() {
        return siteId;
    }

    public void setSiteId(String siteId) {
        this.siteId = siteId;
    }

    public String getRouteId() {
        return routeId;
    }

    public void setRouteId(String routeId) {
        this.routeId = routeId;
    }

    public String getCustomerNumber() {
        return customerNumber;
    }

    public void setCustomerNumber(String customerNumber) {
        this.customerNumber = customerNumber;
    }

    public String getSiteName() {
        return siteName;
    }

    public void setSiteName(String siteName) {
        this.siteName = siteName;
    }

    public JSONObject getDataSet() {
        return dataSet;
    }

    public void setDataSet(JSONObject dataSet) {
        this.dataSet = dataSet;
    }
}
