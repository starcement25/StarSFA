package com.forcepower.acedns.bean;

public class MarketFeedbackDetails {
    String marketFeedbackId = "";
    String userId = "";
    String mfGroupEnable = "";
    String mfCol1 = "";
    String mfCol2 = "";
    String mfCol3 = "";
    String mfCol4 = "";
    String mfSubMenuDetails = "";
    String mfSubMenuImage = "";
    String mf_tagging = "";
    String mf_customer_branchwise = "";
    String mf_sub_menu_qty_unit = "";
    String ex_for = "";

    public String getMarketFeedbackId() {
        return marketFeedbackId;
    }

    public void setMarketFeedbackId(String marketFeedbackId) {
        this.marketFeedbackId = marketFeedbackId;
    }

    public String getUserId() {
        return userId;
    }

    public void setUserId(String userId) {
        this.userId = userId;
    }

    public String getMfGroupEnable() {
        return mfGroupEnable;
    }

    public void setMfGroupEnable(String mfGroupEnable) {
        this.mfGroupEnable = mfGroupEnable;
    }

    public String getMfCol1() {
        return mfCol1;
    }

    public void setMfCol1(String mfCol1) {
        this.mfCol1 = mfCol1;
    }

    public String getMfCol2() {
        return mfCol2;
    }

    public void setMfCol2(String mfCol2) {
        this.mfCol2 = mfCol2;
    }

    public String getMfCol3() {
        return mfCol3;
    }

    public void setMfCol3(String mfCol3) {
        this.mfCol3 = mfCol3;
    }

    public String getMfCol4() {
        return mfCol4;
    }

    public void setMfCol4(String mfCol4) {
        this.mfCol4 = mfCol4;
    }

    public String getMfSubMenuDetails() {
        return mfSubMenuDetails;
    }

    public void setMfSubMenuDetails(String mfSubMenuDetails) {
        this.mfSubMenuDetails = mfSubMenuDetails;
    }

    public String getMfSubMenuImage() {
        return mfSubMenuImage;
    }

    public void setMfSubMenuImage(String mfSubMenuImage) {
        this.mfSubMenuImage = mfSubMenuImage;
    }

    public String getMf_tagging() {
        if (mf_tagging == null) {
            mf_tagging = "no";
        }
        return mf_tagging;
    }

    public void setMf_tagging(String mf_tagging) {
        this.mf_tagging = mf_tagging;
    }

    public String getMf_customer_branchwise() {
        return mf_customer_branchwise;
    }

    public void setMf_customer_branchwise(String mf_customer_branchwise) {
        this.mf_customer_branchwise = mf_customer_branchwise;
    }

    public String getMf_sub_menu_qty_unit() {
        return mf_sub_menu_qty_unit;
    }

    public void setMf_sub_menu_qty_unit(String mf_sub_menu_qty_unit) {
        this.mf_sub_menu_qty_unit = mf_sub_menu_qty_unit;
    }

    public String getEx_for() {
        return ex_for;
    }

    public void setEx_for(String ex_for) {
        this.ex_for = ex_for;
    }
}
