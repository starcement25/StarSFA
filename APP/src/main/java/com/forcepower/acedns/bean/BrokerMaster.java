package com.forcepower.acedns.bean;

public class BrokerMaster {
    String mBrokerId = "";
    String mBrokerName = "";
    String acedns = "";
    String brokerageCost = "";

    public String getBrokerId() {
        return mBrokerId;
    }

    public void setBrokerId(String brokerid) {
        this.mBrokerId = brokerid;
    }

    public String getBrokerName() {
        return mBrokerName;
    }

    public void setBrokerName(String brokername) {
        this.mBrokerName = brokername;
    }

    public String getAcedns() {
        return acedns;
    }

    public void setAcedns(String acedns) {
        this.acedns = acedns;
    }

    public String getBrokerageCost() {
        return brokerageCost;
    }

    public void setBrokerageCost(String brokerageCost) {
        this.brokerageCost = brokerageCost;
    }
}
