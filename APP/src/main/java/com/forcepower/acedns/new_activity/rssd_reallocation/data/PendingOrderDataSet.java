package com.forcepower.acedns.new_activity.rssd_reallocation.data;

import java.util.ArrayList;

public class PendingOrderDataSet {
    private String id,orderNo,orderDate,invoiceNo,invoiceDate,productName,qty,status,requestByCode,requestByName;
    private ArrayList<PendingDataSet> pendingDataSets = new ArrayList<>();

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getOrderNo() {
        return orderNo;
    }

    public void setOrderNo(String orderNo) {
        this.orderNo = orderNo;
    }

    public String getOrderDate() {
        return orderDate;
    }

    public void setOrderDate(String orderDate) {
        this.orderDate = orderDate;
    }

    public String getInvoiceNo() {
        return invoiceNo;
    }

    public void setInvoiceNo(String invoiceNo) {
        this.invoiceNo = invoiceNo;
    }

    public String getInvoiceDate() {
        return invoiceDate;
    }

    public void setInvoiceDate(String invoiceDate) {
        this.invoiceDate = invoiceDate;
    }

    public String getProductName() {
        return productName;
    }

    public void setProductName(String productName) {
        this.productName = productName;
    }

    public String getQty() {
        return qty;
    }

    public void setQty(String qty) {
        this.qty = qty;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getRequestByCode() {
        return requestByCode;
    }

    public void setRequestByCode(String requestByCode) {
        this.requestByCode = requestByCode;
    }

    public String getRequestByName() {
        return requestByName;
    }

    public void setRequestByName(String requestByName) {
        this.requestByName = requestByName;
    }

    public ArrayList<PendingDataSet> getPendingDataSets() {
        return pendingDataSets;
    }

    public void setPendingDataSets(ArrayList<PendingDataSet> pendingDataSets) {
        this.pendingDataSets = pendingDataSets;
    }
}
