package com.forcepower.acedns.bean;

public class OdometerReport {
    String date;
    String startkm;
    String endkm;
    String intrakm;
    String attendenceId;

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getStartkm() {
        return startkm;
    }

    public void setStartkm(String startkm) {
        this.startkm = startkm;
    }

    public String getEndkm() {
        return endkm;
    }

    public void setEndkm(String endkm) {
        this.endkm = endkm;
    }

    public String getIntrakm() {
        return intrakm;
    }

    public void setIntrakm(String intrakm) {
        this.intrakm = intrakm;
    }

    public void setAttendenceId(String attendenceId) {
        this.attendenceId = attendenceId;
    }
}
