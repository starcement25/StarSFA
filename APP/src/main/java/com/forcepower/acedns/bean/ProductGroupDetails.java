package com.forcepower.acedns.bean;

public class ProductGroupDetails {
    String groupCode = "";
    String groupName = "";
    String mVerticalValue = "";
    String mStockOutType = "";

    public String getGroupCode() {
        return groupCode;
    }

    public void setGroupCode(String groupCode) {
        this.groupCode = groupCode;
    }

    public String getGroupName() {
        return groupName;
    }

    public void setGroupName(String groupName) {
        this.groupName = groupName;
    }

    public String getVerticalValue() {
        return mVerticalValue;
    }

    public void setVerticalValue(String verticalValue) {
        this.mVerticalValue = verticalValue;
    }

    public String getStockOutType() {
        return mStockOutType;
    }

    public void setStockOutType(String mStockOutType) {
        this.mStockOutType = mStockOutType;
    }
}
