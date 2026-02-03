package com.forcepower.acedns.bean;

/**
 * Created by amit on 09/01/2017.
 * invoice info data model
 */

public class YellowCard {
    String yellow_card_no = "";
    String customer_code = "";
    String challan_no = "";
    String challan_date = "";
    String qty = "";
    String qty_UOM = "";
    String flag = "";



    public void setyellow_card_no(String yellow_card_no) {
        this.yellow_card_no = yellow_card_no;
    }

    public String getyellow_card_no() {
        return yellow_card_no;
    }

    public void setcustomer_code(String customer_code) {
        this.customer_code = customer_code;
    }

    public String getcustomer_code() {
        return customer_code;
    }

    public void setchallan_no(String challan_no) {
        this.challan_no = challan_no;
    }

    public String getchallan_no() {
        return challan_no;
    }

    public void setchallan_date(String challan_date) {
        this.challan_date = challan_date;
    }

    public String getchallan_date() {
        return challan_date;
    }

    public void setqty(String qty) {
        this.qty = qty;
    }

    public String getqty() {
        return qty;
    }

    public void setqty_UOM(String qty_UOM) {
        this.qty_UOM = qty_UOM;
    }

    public String getqty_UOM() {
        return qty_UOM;
    }

    public void setflag(String flag) {
        this.flag = flag;
    }

    public String getflag() {
        return flag;
    }

}
