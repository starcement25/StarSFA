package com.forcepower.acedns.bean;

public class NoOrderDetails {
    String order_no;
    String sku_code;
    String qty;
    String mrp_code;
    String TD;
    String sale_rate;
    String amount;
    String UOM;
    int flag;

    public String getOrder_no() {
        return order_no;
    }

    public void setOrder_no(String order_no) {
        this.order_no = order_no;
    }

    public String getSku_code() {
        return sku_code;
    }

    public void setSku_code(String sku_code) {
        this.sku_code = sku_code;
    }

    public String getQty() {
        return qty;
    }

    public void setQty(String qty) {
        this.qty = qty;
    }

    public int getFlag() {
        return flag;
    }

    public void setFlag(int flag) {
        this.flag = flag;
    }

    public String getMrp_code() {
        return mrp_code;
    }

    public void setMrp_code(String mrp_code) {
        this.mrp_code = mrp_code;
    }

    public String getTD() {
        return TD;
    }

    public void setTD(String TD) {
        this.TD = TD;
    }

    public String getSale_rate() {
        return sale_rate;
    }

    public void setSale_rate(String sale_rate) {
        this.sale_rate = sale_rate;
    }

    public String getAmount() {
        return amount;
    }

    public void setAmount(String amount) {
        this.amount = amount;
    }

    public String getUOM() {
        return UOM;
    }

    public void setUOM(String UOM) {
        this.UOM = UOM;
    }
}
