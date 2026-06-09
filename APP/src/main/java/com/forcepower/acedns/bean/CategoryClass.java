package com.forcepower.acedns.bean;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

public class CategoryClass implements Serializable
{
    private String category_id;
    private String category_name;
    private String Qty;
    private String prod_desc;
    private String erporderdt;
    private String erporderno;

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getFreight() {
        return freight;
    }

    public void setFreight(String freight) {
        this.freight = freight;
    }

    public String getPlant_name() {
        return plant_name;
    }

    public void setPlant_name(String plant_name) {
        this.plant_name = plant_name;
    }

    private String address;
    private String freight;
    private String plant_name;

    private List<ItemDetailsClass> itemList = new ArrayList<ItemDetailsClass>();

    public CategoryClass(String category_id, String category_name, String Qty, String prod_desc,String erporderdt,String erporderno,String address,String freight,String plant_name)
    {
        this.category_id = category_id;
        this.category_name = category_name;
        this.Qty = Qty;
        this.prod_desc = prod_desc;
        this.erporderdt = erporderdt;
        this.erporderno = erporderno;
        this.address = address;
        this.freight = freight;
        this.plant_name = plant_name;
    }

    public String getCategoryId() {
        return category_id;
    }

    public void setCategoryId(String category_id) {
        this.category_id = category_id;
    }

    public String setCategoryName() {
        return category_name;
    }

    public void setCategoryName(String category_name) {
        this.category_name = category_name;
    }

    public List<ItemDetailsClass> getItemList() {
        return itemList;
    }

    public void setItemList(List<ItemDetailsClass> itemList) {
        this.itemList = itemList;
    }

    public String getQty() {
        return Qty;
    }

    public void setQty(String Qty) {
        this.Qty = Qty;
    }


    public String getprod_desc() {
        return prod_desc;
    }

    public void setprod_desc(String prod_desc) {
        this.prod_desc = prod_desc;
    }

    public String geterporderdt() {
        return erporderdt;
    }

    public String geterporderno() {
        return erporderno;
    }


}