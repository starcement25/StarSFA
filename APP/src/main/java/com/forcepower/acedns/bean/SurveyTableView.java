package com.forcepower.acedns.bean;

public class SurveyTableView {
    String mRowId = "";
    String mType = "";
    String mValue = "";
    String mDependentOn = "";
    String mDependentValue = "";
    String mAction = "";

    public String getRowId() {
        return mRowId;
    }

    public void setRowId(String rowId) {
        this.mRowId = rowId;
    }

    public String getType() {
        return mType;
    }

    public void setType(String type) {
        this.mType = type;
    }

    public String getValue() {
        return mValue;
    }

    public void setValue(String value) {
        this.mValue = value;
    }

    public String getDependentOn() {
        return mDependentOn;
    }

    public void setDependentOn(String dependentOn) {
        this.mDependentOn = dependentOn;
    }

    public String getDependentValue() {
        return mDependentValue;
    }

    public void setDependentValue(String dependentValue) {
        this.mDependentValue = dependentValue;
    }

    public String getAction() {
        return mAction;
    }

    public void setAction(String action) {
        this.mAction = action;
    }
}
