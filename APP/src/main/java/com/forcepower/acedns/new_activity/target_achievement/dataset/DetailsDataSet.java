package com.forcepower.acedns.new_activity.target_achievement.dataset;

public class DetailsDataSet {
    String customerName, target, achievement;
    boolean isTitle;

    public DetailsDataSet(String customerName, String target, String achievement, boolean isTitle) {
        this.customerName = customerName;
        this.target = target;
        this.achievement = achievement;
        this.isTitle = isTitle;
    }

    public String getCustomerName() {
        return customerName;
    }

    public String getTarget() {
        return target;
    }

    public String getAchievement() {
        return achievement;
    }

    public boolean getIsTitle() {
        return isTitle;
    }
}
