package com.forcepower.acedns.bean;

public class JointWorkObservation {
    String joint_work_observation_id = "";
    String customer_code = "";
    String route_code = "";
    String emp_code = "";
    String observation_on_employee = "";
    String observation_on_customer = "";
    String flag = "";

    public void setjoint_work_observation(String joint_work_observation) {
        this.joint_work_observation_id = joint_work_observation;
    }

    public String getjoint_work_observation() {
        return joint_work_observation_id;
    }

    public void setcustomer_code(String customer_code) {
        this.customer_code = customer_code;
    }

    public String getcustomer_code() {
        return customer_code;
    }

    public void setroute_code(String route_code) {
        this.route_code = route_code;
    }

    public String getroute_code() {
        return route_code;
    }

    public void setemp_code(String emp_code) {
        this.emp_code = emp_code;
    }

    public String getemp_code() {
        return emp_code;
    }

    public void setobservation_on_employee(String observation_on_employee) {
        this.observation_on_employee = observation_on_employee;
    }

    public String getobservation_on_employee() {
        return observation_on_employee;
    }

    public void setobservation_on_customer(String observation_on_customer) {
        this.observation_on_customer = observation_on_customer;
    }

    public String getobservation_on_customer() {
        return observation_on_customer;
    }

    public void setflag(String flag) {
        this.flag = flag;
    }

    public String getflag() {
        return flag;
    }
}
