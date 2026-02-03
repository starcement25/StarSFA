package com.forcepower.acedns.bean;

public class SisEmpData {
    String emp_code;
    String emp_name;
    String year;
    String month;
    String base_volume;
    String volume_achv;
    String achv_percent;
    String monthly_unique_visit_target;
    String monthly_unique_visit_achv;
    String dealer_appointment_tgt;
    String dealer_appointment_achv;
    String active_dealer_cur_month;
    String active_dealer_prev_month;
    String sis_earned;
    String penalty_deducted;
    String net_sis_earned;

    public SisEmpData() {
    }

    public SisEmpData(String emp_code, String emp_name, String year, String month, String base_volume, String volume_achv, String achv_percent, String monthly_unique_visit_target, String monthly_unique_visit_achv, String dealer_appointment_tgt, String dealer_appointment_achv, String active_dealer_cur_month, String active_dealer_prev_month, String sis_earned, String penalty_deducted, String net_sis_earned) {
        this.emp_code = emp_code;
        this.emp_name = emp_name;
        this.year = year;
        this.month = month;
        this.base_volume = base_volume;
        this.volume_achv = volume_achv;
        this.achv_percent = achv_percent;
        this.monthly_unique_visit_target = monthly_unique_visit_target;
        this.monthly_unique_visit_achv = monthly_unique_visit_achv;
        this.dealer_appointment_tgt = dealer_appointment_tgt;
        this.dealer_appointment_achv = dealer_appointment_achv;
        this.active_dealer_cur_month = active_dealer_cur_month;
        this.active_dealer_prev_month = active_dealer_prev_month;
        this.sis_earned = sis_earned;
        this.penalty_deducted = penalty_deducted;
        this.net_sis_earned = net_sis_earned;
    }

    public String getEmp_code() {
        return emp_code;
    }

    public void setEmp_code(String emp_code) {
        this.emp_code = emp_code;
    }

    public String getEmp_name() {
        return emp_name;
    }

    public void setEmp_name(String emp_name) {
        this.emp_name = emp_name;
    }

    public String getYear() {
        return year;
    }

    public void setYear(String year) {
        this.year = year;
    }

    public String getMonth() {
        return month;
    }

    public void setMonth(String month) {
        this.month = month;
    }

    public String getBase_volume() {
        return base_volume;
    }

    public void setBase_volume(String base_volume) {
        this.base_volume = base_volume;
    }

    public String getVolume_achv() {
        return volume_achv;
    }

    public void setVolume_achv(String volume_achv) {
        this.volume_achv = volume_achv;
    }

    public String getAchv_percent() {
        return achv_percent;
    }

    public void setAchv_percent(String achv_percent) {
        this.achv_percent = achv_percent;
    }

    public String getMonthly_unique_visit_target() {
        return monthly_unique_visit_target;
    }

    public void setMonthly_unique_visit_target(String monthly_unique_visit_target) {
        this.monthly_unique_visit_target = monthly_unique_visit_target;
    }

    public String getMonthly_unique_visit_achv() {
        return monthly_unique_visit_achv;
    }

    public void setMonthly_unique_visit_achv(String monthly_unique_visit_achv) {
        this.monthly_unique_visit_achv = monthly_unique_visit_achv;
    }

    public String getDealer_appointment_tgt() {
        return dealer_appointment_tgt;
    }

    public void setDealer_appointment_tgt(String dealer_appointment_tgt) {
        this.dealer_appointment_tgt = dealer_appointment_tgt;
    }

    public String getDealer_appointment_achv() {
        return dealer_appointment_achv;
    }

    public void setDealer_appointment_achv(String dealer_appointment_achv) {
        this.dealer_appointment_achv = dealer_appointment_achv;
    }

    public String getActive_dealer_cur_month() {
        return active_dealer_cur_month;
    }

    public void setActive_dealer_cur_month(String active_dealer_cur_month) {
        this.active_dealer_cur_month = active_dealer_cur_month;
    }

    public String getActive_dealer_prev_month() {
        return active_dealer_prev_month;
    }

    public void setActive_dealer_prev_month(String active_dealer_prev_month) {
        this.active_dealer_prev_month = active_dealer_prev_month;
    }

    public String getSis_earned() {
        return sis_earned;
    }

    public void setSis_earned(String sis_earned) {
        this.sis_earned = sis_earned;
    }

    public String getPenalty_deducted() {
        return penalty_deducted;
    }

    public void setPenalty_deducted(String penalty_deducted) {
        this.penalty_deducted = penalty_deducted;
    }

    public String getNet_sis_earned() {
        return net_sis_earned;
    }

    public void setNet_sis_earned(String net_sis_earned) {
        this.net_sis_earned = net_sis_earned;
    }
}
