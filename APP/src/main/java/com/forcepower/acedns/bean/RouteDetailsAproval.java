package com.forcepower.acedns.bean;

public class RouteDetailsAproval {
    String route_plan_trans_id;
    String emp_code;
    String route_code;
    String route_name;
    String visit_date;
    String remarks;
    String create_date;
    String emp_name;


    public String getRoute_plan_trans_id() {
        return route_plan_trans_id;
    }

    public void setRoute_plan_trans_id(String route_plan_trans_id) {
        this.route_plan_trans_id = route_plan_trans_id;
    }

    public String getEmp_code() {
        return emp_code;
    }

    public void setEmp_code(String emp_code) {
        this.emp_code = emp_code;
    }

    public String getRoute_code() {
        return route_code;
    }

    public void setRoute_code(String route_code) {
        this.route_code = route_code;
    }

    public String getRoute_name() {
        return route_name;
    }

    public void setRoute_name(String route_name) {
        this.route_name = route_name;
    }

    public String getVisit_date() {
        return visit_date;
    }

    public void setVisit_date(String visit_date) {
        this.visit_date = visit_date;
    }

    public String getRemarks() {
        if (remarks==null){
            return "";
        }
        return remarks;
    }

    public void setRemarks(String remarks) {
        this.remarks = remarks;
    }

    public String getCreate_date() {
        return create_date;
    }

    public void setCreate_date(String create_date) {
        this.create_date = create_date;
    }

    public String getEmp_name() {
        return emp_name;
    }

    public void setEmp_name(String emp_name) {
        this.emp_name = emp_name;
    }
}
