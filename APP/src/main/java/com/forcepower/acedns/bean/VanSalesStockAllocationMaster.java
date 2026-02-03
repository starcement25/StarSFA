package com.forcepower.acedns.bean;

public class VanSalesStockAllocationMaster {
    String emp_code  = "";
    String prod_code  = "";
    String allocated_qty   = "";
    String balance_qty    = "";
    String acedns     = "";


    public String getemp_code() {
        return emp_code;
    }

    public void setemp_code(String emp_code) {
        this.emp_code = emp_code;
    }

    public String getprod_code() {
        return prod_code;
    }

    public void setprod_code(String prod_code) {
        this.prod_code = prod_code;
    }

    public String getallocated_qty() {
        return allocated_qty;
    }

    public void setallocated_qty(String allocated_qty) {
        this.allocated_qty = allocated_qty;
    }

    public String getbalance_qty() {
        return balance_qty;
    }

    public void setbalance_qty(String balance_qty) {
        this.balance_qty = balance_qty;
    }

    public String getacedns() {
        return acedns;
    }

    public void setacedns(String acedns) {
        this.acedns = acedns;
    }
}
