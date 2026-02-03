package com.forcepower.acedns.bean;


public class Location {
    String empCode = "";
    String transId = "";
    String date = "";
    String latitude = "";
    String longitude = "";
    int flag = 0;
    String TA_DA_mode = "";
    String purpose_of_visit = "";

    public String getEmpCode() {
        return empCode;
    }

    public void setEmpCode(String empCode) {
        this.empCode = empCode;
    }

    public String getTransId() {
        return transId;
    }

    public void setTransId(String transId) {
        this.transId = transId;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getLatitude() {
        return latitude;
    }

    public void setLatitude(String latitude) {
        this.latitude = latitude;
    }

    public String getLongitude() {
        return longitude;
    }

    public void setLongitude(String longitude) {
        this.longitude = longitude;
    }

    public int getFlag() {
        return flag;
    }

    public void setFlag(int flag) {
        this.flag = flag;
    }

    public String getTA_DA_mode() {
        return TA_DA_mode;
    }

    public void setTA_DA_mode(String TA_DA_mode) {
        this.TA_DA_mode = TA_DA_mode;
    }

    public String getPurpose_of_visit() {
        return purpose_of_visit;
    }

    public void setPurpose_of_visit(String purpose_of_visit) {
        this.purpose_of_visit = purpose_of_visit;
    }
}
