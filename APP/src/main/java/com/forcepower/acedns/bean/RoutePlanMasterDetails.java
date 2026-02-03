package com.forcepower.acedns.bean;


public class RoutePlanMasterDetails {
    String tranId = "";
    String routecode = "";
    String routeName = "";
    String empCode = "";
    String visitDate = "";
    String createDate = "";
    String flag = "0";
    String previous_route_code = "";
    String previous_route_name = "";
    String remarks = "";
    String distributorCode = "";
    String status = "";
    String WorkingWIth = "";
    String customerCount = "";

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getDistributorCode() {
        return distributorCode;
    }

    public void setDistributorCode(String distributorCode) {
        this.distributorCode = distributorCode;
    }

    public String getRemarks() {
        return remarks;
    }

    public void setRemarks(String remarks) {
        this.remarks = remarks;
    }

    public String getPrevious_route_name() {
        return previous_route_name;
    }

    public void setPrevious_route_name(String previous_route_name) {
        this.previous_route_name = previous_route_name;
    }

    public String getPrevious_route_code() {
        return previous_route_code;
    }

    public void setPrevious_route_code(String previous_route_code) {
        this.previous_route_code = previous_route_code;
    }

    public String getRouteName() {
        return routeName;
    }

    public void setRouteName(String routeName) {
        this.routeName = routeName;
    }

    public String getFlag() {
        return flag;
    }

    public void setFlag(String flag) {
        this.flag = flag;
    }

    public String getVisitDate() {
        return visitDate;
    }

    public void setVisitDate(String visitDate) {
        this.visitDate = visitDate;
    }

    public String getCreateDate() {
        return createDate;
    }

    public void setCreateDate(String createDate) {
        this.createDate = createDate;
    }

    public String getTranId() {
        return tranId;
    }

    public void setTranId(String tranId) {
        this.tranId = tranId;
    }

    public String getRoutecode() {
        return routecode;
    }

    public void setRoutecode(String routecode) {
        this.routecode = routecode;
    }



    public String getEmpCode() {
        return empCode;
    }

    public void setEmpCode(String empCode) {
        this.empCode = empCode;
    }

    public String getWorkingWIth() {
        return WorkingWIth;
    }

    public void setWorkingWIth(String WorkingWIth) {
        this.WorkingWIth = WorkingWIth;
    }

    public String getcustomerCount() {
        return customerCount;
    }

    public void setcustomerCount(String customerCount) {
        this.customerCount = customerCount;
    }


}
