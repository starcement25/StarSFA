package com.forcepower.acedns.new_activity.customer_outstanding.dataset;

public class GraphDataSet {
    String colorCode, title, amount, percentage; // NEW

    public GraphDataSet(String colorCode, String title, String amount, String percentage) {
        this.colorCode = colorCode;
        this.title = title;
        this.amount = amount;
        this.percentage = percentage;
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

    public String getPercentage() {
        return percentage;
    }
}