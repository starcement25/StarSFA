package com.forcepower.acedns.bean;

import android.content.Context;

public class PurposeVisitDetails {
    String purpose = "";

    public PurposeVisitDetails(String purpose) {
        this.purpose = purpose;
    }

    public String getPurpose() {
        return purpose;
    }

    public void setPurpose(String purpose) {
        this.purpose = purpose;
    }
}
