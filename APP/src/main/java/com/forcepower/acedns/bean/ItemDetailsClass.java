package com.forcepower.acedns.bean;

import java.io.Serializable;

public class ItemDetailsClass implements Serializable
{
    private String scat_id, rate, quantity, DriverContact="", name, TrackNumber="", Position;

    //---------scat_id, rate, quantity, total_order_price_with_gst
    public ItemDetailsClass(String challanNO, String date, String quantity, String TrackNumber, String DriverContact)
    {
        this.scat_id = challanNO;
        this.rate = date;
        this.quantity = quantity;
        this.TrackNumber = TrackNumber;
        this.DriverContact = DriverContact;
    }


    public String getSubcategoryId() {
        return scat_id;
    }
    public void setSubcategoryId(String id) {
        this.scat_id = id;
    }
    public String getRateValue() {
        return rate;
    }
    public void setRateValue(String rate) {
        this.rate = rate;
    }
    public String getQuantity() {
        return quantity;
    }
    public void setQuantity(String quantity) {
        this.quantity = quantity;
    }
    public String getDriverContact() {
        return DriverContact;
    }
    public void setDriverContact(String DriverContact) {
        this.DriverContact = DriverContact;
    }

    public String getName() {
        return name;
    }
    public void setName(String name) {
        this.name = name;
    }

    public String getTrackNumber() {
        return TrackNumber;
    }
    public void setTrackNumber(String TrackNumber) {
        this.TrackNumber = TrackNumber;
    }
    public String getPosition() {
        return Position;
    }
    public void setPosition(String Position) {
        this.Position = Position;
    }
}