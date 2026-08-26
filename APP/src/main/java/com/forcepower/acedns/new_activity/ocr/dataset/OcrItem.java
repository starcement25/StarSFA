package com.forcepower.acedns.new_activity.ocr.dataset;

public class OcrItem {
    String slNo, name, phoneNo, address, ilpRegistered;
    Boolean isHeader;

    public String getSlNo() {
        return slNo;
    }

    public void setSlNo(String slNo) {
        this.slNo = slNo;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getPhoneNo() {
        return phoneNo;
    }

    public void setPhoneNo(String phoneNo) {
        this.phoneNo = phoneNo;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getIlpRegistered() {
        return ilpRegistered;
    }

    public void setIlpRegistered(String ilpRegistered) {
        this.ilpRegistered = ilpRegistered;
    }

    public Boolean getHeader() {
        return isHeader;
    }

    public void setHeader(Boolean header) {
        isHeader = header;
    }
}
