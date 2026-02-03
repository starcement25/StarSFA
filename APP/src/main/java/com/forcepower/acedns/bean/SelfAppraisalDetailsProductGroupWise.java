package com.forcepower.acedns.bean;

public class SelfAppraisalDetailsProductGroupWise {
    String productGroupCode = "";
    String productGroupName = "";
    String empCode = "";
    String month = "";
    String target = "";
    String achievement = "";


    public void setproductGroupCode(String cutomerCode) {
        this.productGroupCode = cutomerCode;
    }

    public String getproductGroupCode() {
        return productGroupCode;
    }


    public void setproductGroupName(String customerName) {
        this.productGroupName = customerName;
    }

    public String getproductGroupName() {
        return productGroupName;
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
}
