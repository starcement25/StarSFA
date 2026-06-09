package com.forcepower.acedns.new_activity.customer_outstanding.dataset;

public class CategoryDataSet {
    String colorCode, title, amount, invoiceCount;

    public CategoryDataSet(String colorCode, String title, String amount, String invoiceCount) {
        this.colorCode = colorCode;
        this.title = title;
        this.amount = amount;
        this.invoiceCount = invoiceCount;
    }

    public String getColorCode() {
        return colorCode;
    }

    public String getTitle() {
        return title;
    }

    public String getAmount() {
        return amount;
    }

    public String getInvoiceCount() {
        return invoiceCount;
    }
}
