package com.forcepower.acedns.activity_ntquotation.dataset;

public class CustomerFilterModel {
    public String searchText;
    public String fromDate;
    public String toDate;
    public String status;
    public String user_type;

    public CustomerFilterModel(String searchText, String fromDate, String toDate, String status,String user_type) {
        this.searchText = searchText;
        this.fromDate = fromDate;
        this.toDate = toDate;
        this.status = status;
        this.user_type = user_type;
    }
}
