package com.forcepower.acedns.bean;

public class KeyValue {
    String mKey = "";
    String mValue = "0";
    String mEnteredValue = "0";
    String mImageName = "";
    String mType = "";
    String mShowColumn1 = "";
    String mShowColumn2 = "";
    String mShowColumn3 = "";
    String mShowColumn4 = "";
    String mShowColumn5 = "";
    String mShowColumn6 = "";
    String rowId = "";
    String insertTableDetail = "";
    String displayName = "";
    String validation = "";

    public KeyValue() {
    }

    public KeyValue(String mValue) {
        this.mValue = mValue;
    }

    public KeyValue(String mValue, String mKey) {
        this.mValue = mValue;
        this.mKey = mKey;
    }

    public KeyValue(String mValue, String mKey, String mType) {
        this.mValue = mValue;
        this.mKey = mKey;
        this.mType = mType;
    }

    public KeyValue(String mValue, String mShowColumn1, String mShowColumn2, String mKey, String mType) {
        this.mValue = mValue;
        this.mKey = mKey;
        this.mType = mType;
        this.mShowColumn1 = mShowColumn1;
        this.mShowColumn2 = mShowColumn2;
    }

    public String getKey() {
        return mKey;
    }

    public void setKey(String key) {
        this.mKey = key;
    }

    public String getValue() {
        return mValue;
    }

    public void setValue(String value) {
        this.mValue = value;
    }

    public String getEnteredValue() {
        return mEnteredValue;
    }

    public void setEnteredValue(String enteredValue) {
        this.mEnteredValue = enteredValue;
    }

    public String getImageName() {
        return mImageName;
    }

    public void setImageName(String imageName) {
        this.mImageName = imageName;
    }

    public String getType() {
        return mType;
    }

    public void setType(String type) {
        this.mType = type;
    }

    public String getmShowColumn1() {
        return mShowColumn1;
    }

    public void setmShowColumn1(String mShowColumn1) {
        this.mShowColumn1 = mShowColumn1;
    }

    public String getmShowColumn2() {
        return mShowColumn2;
    }

    public void setmShowColumn2(String mShowColumn2) {
        this.mShowColumn2 = mShowColumn2;
    }

    public String getmShowColumn3() {
        return mShowColumn3;
    }

    public void setmShowColumn3(String mShowColumn3) {
        this.mShowColumn3 = mShowColumn3;
    }

    public String getmShowColumn4() {
        return mShowColumn4;
    }

    public void setmShowColumn4(String mShowColumn4) {
        this.mShowColumn4 = mShowColumn4;
    }

    public String getmShowColumn5() {
        return mShowColumn5;
    }

    public void setmShowColumn5(String mShowColumn5) {
        this.mShowColumn5 = mShowColumn5;
    }

    public String getmmShowColumn6() {
        return mShowColumn6;
    }

    public void setmShowColumn6(String mShowColumn6) {
        this.mShowColumn6 = mShowColumn6;
    }

    public void setrowId(String rowId) {
        this.rowId = rowId;
    }

    public String getinsertTableDetail() {
        return insertTableDetail;
    }

    public void setinsertTableDetail(String insertTableDetail) {
        this.insertTableDetail = insertTableDetail;
    }

    public String getdisplayName() {
        return displayName;
    }

    public void setdisplayName(String displayName) {
        this.displayName = displayName;
    }

    public String getValidation() {
        return validation;
    }

    public void setValidation(String validation) {
        this.validation = validation;
    }
}
