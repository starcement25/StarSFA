package com.forcepower.acedns.bean;


public class MerchandisingDetails {
    String merchandisingId = "";
    String empCode = "";
    String prodCode = "";
    String clientId = "";
    String transType = "";
    String remarks = "";
    String attachment = "";
    String issueStatus = "";
    String prodGroup = "";
    String prodSubGrp = "";
    String prodBrand = "";
    String prodDesc = "";

    public String getProdDesc() {
        return prodDesc;
    }

    public void setProdDesc(String prodDesc) {
        this.prodDesc = prodDesc;
    }

    public String getMerchandisingId() {
        return merchandisingId;
    }

    public void setMerchandisingId(String merchandisingId) {
        this.merchandisingId = merchandisingId;
    }

    public String getEmpCode() {
        return empCode;
    }

    public void setEmpCode(String empCode) {
        this.empCode = empCode;
    }

    public String getProdCode() {
        return prodCode;
    }

    public void setProdCode(String prodCode) {
        this.prodCode = prodCode;
    }

    public void setClientId(String clientId) {
        this.clientId = clientId;
    }

    public String getTransType() {
        return transType;
    }

    public void setTransType(String transType) {
        this.transType = transType;
    }

    public String getRemarks() {
        return remarks;
    }

    public void setRemarks(String remarks) {
        this.remarks = remarks;
    }

    public void setAttachment(String attachment) {
        this.attachment = attachment;
    }

    public String getIssueStatus() {
        return issueStatus;
    }

    public void setIssueStatus(String issueStatus) {
        this.issueStatus = issueStatus;
    }

    public String getProdGroup() {
        return prodGroup;
    }

    public void setProdGroup(String prodGroup) {
        this.prodGroup = prodGroup;
    }

    public void setProdSubGrp(String prodSubGrp) {
        this.prodSubGrp = prodSubGrp;
    }

    public void setProdBrand(String prodBrand) {
        this.prodBrand = prodBrand;
    }
}
