package com.forcepower.acedns.new_activity.sitelead.dataset;

public class DataSet {
    String title,value;

    public DataSet(){}

    public DataSet(String title, String value) {
        this.title = title;
        this.value = value;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }
}
