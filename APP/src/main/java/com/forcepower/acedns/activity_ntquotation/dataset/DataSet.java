package com.forcepower.acedns.activity_ntquotation.dataset;

public class DataSet {
    String id, value;
    Boolean isSelect;

    public DataSet() {
    }

    public DataSet(String id, String value, Boolean isSelect) {
        this.id = id;
        this.value = value;
        this.isSelect = isSelect;
    }

    public String getId() {
        return id;
    }

    public void setId(String title) {
        this.id = title;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }

    public Boolean getSelect() {
        return isSelect;
    }

    public void setSelect(Boolean select) {
        isSelect = select;
    }
}
