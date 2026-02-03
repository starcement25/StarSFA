package com.forcepower.acedns.bean;

public class CallDurationDetails {
    String customerCode = "";
    String transId = "";
    String callDuration = "";

    public final String getCustomerCode() {
        return customerCode;
    }

    public final void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public final String getTransId() {
        return transId;
    }

    public final void setTransId(String transId) {
        this.transId = transId;
    }

    public final String getCallDuration() {
        return callDuration;
    }

    public final void setCallDuration(String callDuration) {
        this.callDuration = callDuration;
    }
}
