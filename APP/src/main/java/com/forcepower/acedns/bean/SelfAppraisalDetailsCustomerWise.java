package com.forcepower.acedns.bean;

public class SelfAppraisalDetailsCustomerWise {
    String cutomerCode = "";
    String customerName = "";
    String empName = "";
    String empCode = "";
    String month = "";
    String target = "";
    String achievement = "";
    String vertical = "";
    String product_group_code  = "";
    String prod_code   = "";

    String privousTarget = "";
    String prevousAchievement = "";


    public void setcutomerCode(String cutomerCode) {
        this.cutomerCode = cutomerCode;
    }
    public String getcutomerCode() {
        return cutomerCode;
    }

    public void setempName(String empName) {
        this.empName = empName;
    }
    public String getempName() {
        return empName;
    }


    public void setcustomerName(String customerName) {
        this.customerName = customerName;
    }

    public String getcustomerName() {
        return customerName;
    }

    public void setempCode(String empCode) {
        this.empCode = empCode;
    }

    public String getempCode() {
        return empCode;
    }


    public void setmonth(String month) {
        this.month = month;
    }

    public String getmonth() {
        return month;
    }


    public void settarget(String target) {
        this.target = target;
    }

    public String gettarget() {
        return target;
    }


    public void setachievement(String achievement) {
        this.achievement = achievement;
    }

    public String getachievement() {
        return achievement;
    }

    public void setvertical(String vertical) {
        this.vertical = vertical;
    }
    public String getvertical() {
        return vertical;
    }

    public void setproduct_group_code(String product_group_code) {
        this.product_group_code = product_group_code;
    }
    public String getproduct_group_code() {
        return product_group_code;
    }


    public void setprod_code (String prod_code ) {
        this.prod_code  = prod_code ;
    }
    public String getprod_code () {
        return prod_code ;
    }

    public String getPrivousTarget() {
        return privousTarget;
    }

    public void setPrivousTarget(String privousTarget) {
        this.privousTarget = privousTarget;
    }

    public String getPrevousAchievement() {
        return prevousAchievement;
    }

    public void setPrevousAchievement(String prevousAchievement) {
        this.prevousAchievement = prevousAchievement;
    }
}
