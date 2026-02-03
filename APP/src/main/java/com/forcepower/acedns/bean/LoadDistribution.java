package com.forcepower.acedns.bean;

public class LoadDistribution {
    String prod_code = "";
    String qty_truck_load = "";
    String download_time = "";
    String transport_mode = "";
    String truck_load = "";

    public String getprod_code() {
        return prod_code;
    }

    public void setprod_code(String prod_code) {
        this.prod_code = prod_code;
    }

    public String getqty_truck_load() {
        return qty_truck_load;
    }

    public void setqty_truck_load(String qty_truck_load) {
        this.qty_truck_load = qty_truck_load;
    }

    public String getdownload_time() {
        return download_time;
    }

    public void setdownload_time(String download_time) {
        this.download_time = download_time;
    }

    public String gettransport_mode() {
        return transport_mode;
    }

    public void settransport_mode(String transport_mode) {
        this.transport_mode = transport_mode;
    }

    public String gettruck_load() {
        return truck_load;
    }

    public void settruck_load(String truck_load) {
        this.truck_load = truck_load;
    }
}
