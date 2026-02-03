package com.forcepower.acedns.bean;

public class BranchMasterDetails {
    String companyCode = "";
    String branchCode = "";
    String branchName = "";
    String hq = "";
    String mPlantName = "";
    String mValue = "";
    String isPlant = "";
    String branch_state = "";
    String geo_fencing = "";

    public String getHq() {
        return hq;
    }

    public void setHq(String hq) {
        this.hq = hq;
    }

    public String getCompanyCode() {
        return companyCode;
    }

    public void setCompanyCode(String companyCode) {
        this.companyCode = companyCode;
    }

    public String getBranchCode() {
        return branchCode;
    }

    public void setBranchCode(String branchCode) {
        this.branchCode = branchCode;
    }

    public String getBranchName() {
        return branchName;
    }

    public void setBranchName(String branchName) {
        this.branchName = branchName;
    }

    public String getPlantName() {
        return mPlantName;
    }

    public void setPlantName(String plantName) {
        this.mPlantName = plantName;
    }

    public String getValue() {
        return mValue;
    }

    public void setValue(String value) {
        this.mValue = value;
    }

    public String getisPlant() {
        return isPlant;
    }

    public void setisPlant(String isPlant) {
        this.isPlant = isPlant;
    }

    public String getbranch_state() {
        return branch_state;
    }

    public void setbranch_state(String branch_state) {
        this.branch_state = branch_state;
    }

    public String getgeo_fencing() {
        return geo_fencing;
    }

    public void setgeo_fencing(String geo_fencing) {
        this.geo_fencing = geo_fencing;
    }
}
