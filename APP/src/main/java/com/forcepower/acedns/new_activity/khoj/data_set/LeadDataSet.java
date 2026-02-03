package com.forcepower.acedns.new_activity.khoj.data_set;

import org.json.JSONObject;

public class LeadDataSet {
    String title,leadId,leadStatus,shipToPartyName,soldToPartyName;

    public String getShipToPartyName() {
        return shipToPartyName;
    }

    public void setShipToPartyName(String shipToPartyName) {
        this.shipToPartyName = shipToPartyName;
    }

    public String getSoldToPartyName() {
        return soldToPartyName;
    }

    public void setSoldToPartyName(String soldToPartyName) {
        this.soldToPartyName = soldToPartyName;
    }

    JSONObject fullData;

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getLeadId() {
        return leadId;
    }

    public void setLeadId(String leadId) {
        this.leadId = leadId;
    }

    public String getLeadStatus() {
        return leadStatus;
    }

    public void setLeadStatus(String leadStatus) {
        this.leadStatus = leadStatus;
    }

    public JSONObject getFullData() {
        return fullData;
    }

    public void setFullData(JSONObject fullData) {
        this.fullData = fullData;
    }
}
