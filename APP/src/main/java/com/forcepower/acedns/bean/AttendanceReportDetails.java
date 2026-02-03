package com.forcepower.acedns.bean;

public class AttendanceReportDetails {
    String emp_code = "";
    String emp_name = "";
    String trans_id = "";
    String date = "";
    String time = "";
    String emp_designation = "";

    public String getemp_code() {
        return emp_code;
    }

    public void setemp_code(String emp_code) {
        this.emp_code = emp_code;
    }

    public String gettrans_id() {
        return trans_id;
    }

    public void settrans_id(String trans_id) {
        this.trans_id = trans_id;
    }

    public String getdate() {
        return date;
    }

    public void setdate(String date) {
        this.date = date;
    }

    public String getTime() {
        return time;
    }

    public void setTime(String time) {
        this.time = time;
    }

    public String getemp_name() {
        return emp_name;
    }

    public void setemp_name(String emp_name) {
        this.emp_name = emp_name;
    }

    public String getemp_designation() {
        return emp_designation;
    }

    public void setemp_designation(String emp_designation) {
        this.emp_designation = emp_designation;
    }
}
