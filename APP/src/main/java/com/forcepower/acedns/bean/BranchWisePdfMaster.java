package com.forcepower.acedns.bean;

public class BranchWisePdfMaster {
    String branch_code = "";
    String branch_name = "";
    String PDF_file_name = "";
    String gr_file_name = "";
    String acedns = "";
    String startDate = "";
    String endDate = "";
    String schemeName = "";

    public String getbranch_code() {
        return branch_code;
    }

    public void setbranch_code(String branch_code) {
        this.branch_code = branch_code;
    }

    public String getbranch_name() {
        return branch_name;
    }

    public void setbranch_name(String branch_name) {
        this.branch_name = branch_name;
    }

    public String getPDF_file_name() {
        return PDF_file_name;
    }

    public void setPDF_file_name(String PDF_file_name) {
        this.PDF_file_name = PDF_file_name;
    }

    public String getAcedns() {
        return acedns;
    }

    public void setAcedns(String acedns) {
        this.acedns = acedns;
    }

    public String getStartDate() {
        return startDate;
    }

    public void setStartDate(String startDate) {
        this.startDate = startDate;
    }

    public String getEndDate() {
        return endDate;
    }

    public void setEndDate(String endDate) {
        this.endDate = endDate;
    }

    public String getgr_file_name() {
        return gr_file_name;
    }

    public void setgr_file_name(String gr_file_name) {
        this.gr_file_name = gr_file_name;
    }

    public String getSchemeName() {
        return schemeName;
    }

    public void setSchemeName(String schemeName) {
        this.schemeName = schemeName;
    }
}
