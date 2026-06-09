package com.forcepower.acedns.new_activity.customer_outstanding.dataset;

public class DataSet {
    String title;
    int startDate, endDate;
    boolean isSelect = false;

    public DataSet(int startDate, int endDate, String title, boolean isSelect) {
        this.startDate = startDate;
        this.endDate = endDate;
        this.title = title;
        this.isSelect = isSelect;
    }

    public int getStartDate() {
        return startDate;
    }

    public int getEndDate() {
        return endDate;
    }

    public String getTitle() {
        return title;
    }

    public boolean getIsSelect() {
        return isSelect;
    }

    public void setIsSelect(boolean isSelect) {
        this.isSelect = isSelect;
    }
}
