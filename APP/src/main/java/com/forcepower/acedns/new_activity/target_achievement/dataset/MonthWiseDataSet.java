package com.forcepower.acedns.new_activity.target_achievement.dataset;

import android.annotation.SuppressLint;

public class MonthWiseDataSet {
    String monthName, target, achievement, percentage, different;
    boolean isTradeUp;
    int type;

    @SuppressLint("DefaultLocale")
    public MonthWiseDataSet(String monthName, String target, String achievement, int type) {
        this.monthName = monthName;
        this.target = target;
        this.achievement = achievement;
        this.type = type;
        try {
            float t = Float.parseFloat(target);
            float a = Float.parseFloat(achievement);
            if(t==0&&a==0){
                this.percentage = "---";
                this.different = "0";
                this.isTradeUp = false;
            }else if (t == 0 && a > 0) {
                this.percentage = "---";
                this.different = String.format("%.2f",a - t);
                this.isTradeUp = true;
            } else if (t > 0 && a == 0) {
                this.percentage = "0";
                this.different = String.format("%.2f",a - t);
                this.isTradeUp = false;
            } else {
                this.percentage = String.format("%.2f",a / t * 100);
                this.different = String.format("%.2f",a - t);
                this.isTradeUp = a / t * 100 > 100;
            }

        } catch (Exception e) {
            this.percentage = "---";
            this.different = "---";
            this.isTradeUp = false;
        }
    }

    public String getMonthName() {
        return monthName;
    }

    public String getTarget() {
        return target;
    }

    public String getAchievement() {
        return achievement;
    }

    public String getPercentage() {
        return percentage;
    }

    public String getDifferent() {
        return different;
    }

    public boolean getIsTradeUp() {
        return isTradeUp;
    }

    public int getType() {
        return type;
    }
}
