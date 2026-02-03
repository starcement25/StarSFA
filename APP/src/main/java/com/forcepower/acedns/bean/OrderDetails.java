package com.forcepower.acedns.bean;

public class OrderDetails {
    String orderNo = "";
    String skuCode = "";
    double qty;
    String mrpCode = "";
    String TD = "";
    String mPremium = "";
    String saleRate = "";
    String VAT = "0";
    String amount = "0";
    String freight = "";
    String uom = "";
    String customerCode = "";
    String VisitQty = "";
    String VisitDate = "";
    String SchemeType = "";
    String type = "";
    String weightage = "";
    String remarks = "";
    String input_size = "";
    String purpose_of_visit = "";

    public synchronized String getFreight() {
        return freight;
    }

    public synchronized void setFreight(String freight) {
        this.freight = freight;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }

    public String getVAT() {
        return VAT;
    }

    public void setVAT(String vAT) {
        VAT = vAT;
    }

    public String getSaleRate() {
        return saleRate;
    }

    public void setSaleRate(String saleRate) {
        this.saleRate = saleRate;
    }

    public String getTD() {
        return TD;
    }

    public void setTD(String tD) {
        TD = tD;
    }

    public String getPremium() {
        return mPremium;
    }

    public void setPremium(String premium) {
        mPremium = premium;
    }

    public String getOrderNo() {
        return orderNo;
    }

    public void setOrderNo(String orderNo) {
        this.orderNo = orderNo;
    }

    public String getSkuCode() {
        return skuCode;
    }

    public void setSkuCode(String skuCode) {
        this.skuCode = skuCode;
    }

    public double getQty() {
        return qty;
    }

    public void setQty(double qty) {
        this.qty = qty;
    }

    public String getMrpCode() {
        return mrpCode;
    }

    public void setMrpCode(String mrpCode) {
        this.mrpCode = mrpCode;
    }

    public String getUom() {
        return uom;
    }

    public void setUom(String uom) {
        this.uom = uom;
    }

    public String getCustomerCode() {
        return customerCode;
    }

    public void setCustomerCode(String customerCode) {
        this.customerCode = customerCode;
    }

    public String getVisitQty() {
        return VisitQty;
    }

    public void setVisitQty(String VisitQty) {
        this.VisitQty = VisitQty;
    }

    public String getVisitDate() {
        return VisitDate;
    }

    public void setVisitDate(String VisitDate) {
        this.VisitDate = VisitDate;
    }

    public String getSchemeType() {
        return SchemeType;
    }

    public void setSchemeType(String SchemeType) {
        this.SchemeType = SchemeType;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getWeightage() {
        return weightage;
    }

    public void setWeightage(String weightage) {
        this.weightage = weightage;
    }

    public String getRemarks() {
        return remarks;
    }

    public void setRemarks(String remarks) {
        this.remarks = remarks;
    }

    public String getInput_size() {
        return input_size;
    }

    public void setInput_size(String input_size) {
        this.input_size = input_size;
    }

    public String getPurpose_of_visit() {
        return purpose_of_visit;
    }

    public void setPurpose_of_visit(String purpose_of_visit) {
        this.purpose_of_visit = purpose_of_visit;
    }
}
