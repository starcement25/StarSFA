package com.forcepower.acedns.bean;

public class SurveyFormDetails {
    String mSurveyFormId = "";
    String mSurveyUserId = "";
    String mSurveyMenu = "";
    String mSurveyType = "";
    String mSurveyTypeDetails = "";
    String mMallsurveyRelation = "";
    String mSurveySubTypeDetails = "";
    String mSurveyOTP = "";
    String mSurveyLayer = "";
    String mSurveySubMenu = "";
    String mSurveySubMenuDetails = "";
    String mSurveyOutletMenu = "";
    String mSurveyRoutePlan = "";
    String mSurveyOtherText = "";
    String mSurveyReportRowId = "";
    String customer_email_update = "";
    String special_input_screen = "";
    String survey_menu_name  = "";
    String menu_disp_sub_menu  = "";
    String check_in_time_captured_menu = "";
    String follow_up_menu = "";

    public String getSurveyReportRowId() {
        return mSurveyReportRowId;
    }

    public void setSurveyReportRowId(String mSurveyReportRowId) {
        this.mSurveyReportRowId = mSurveyReportRowId;
    }

    public String getSurveyFormId() {
        return mSurveyFormId;
    }

    public void setSurveyFormId(String surveyformid) {
        this.mSurveyFormId = surveyformid;
    }

    public String getSurveyUserId() {
        return mSurveyUserId;
    }

    public void setSurveyUserId(String userid) {
        this.mSurveyUserId = userid;
    }

    public String getSurveyMenu() {
        return mSurveyMenu;
    }

    public void setSurveyMenu(String surveymenu) {
        this.mSurveyMenu = surveymenu;
    }

    public String getSurveyType() {
        return mSurveyType;
    }

    public void setSurveyType(String surveyType) {
        this.mSurveyType = surveyType;
    }

    public String getSurveyTypeDetails() {
        return mSurveyTypeDetails;
    }

    public void setSurveyTypeDetails(String surveyTypeDetails) {
        this.mSurveyTypeDetails = surveyTypeDetails;
    }

    public String getSurveyMallSurveyRelation() {
        return mMallsurveyRelation;
    }

    public void setSurveyMallSurveyRelation(String mallsurveyRelation) {
        this.mMallsurveyRelation = mallsurveyRelation;
    }

    public String getSurveySubTypeDetails() {
        return mSurveySubTypeDetails;
    }

    public void setSurveySubTypeDetails(String surveySubTypeDetails) {
        this.mSurveySubTypeDetails = surveySubTypeDetails;
    }

    public String getSurveyOTP() {
        return mSurveyOTP;
    }

    public void setSurveyOTP(String surveyOTP) {
        this.mSurveyOTP = surveyOTP;
    }

    public String getSurveyLayer() {
        if(mSurveyLayer==null){
            return "";
        }
        else{
            return mSurveyLayer;
        }

    }

    public void setSurveyLayer(String layer) {
        this.mSurveyLayer = layer;
    }

    public String getSurveySubMenu() {
        if(mSurveySubMenu==null){
            return "no";
        }
        else{
            return mSurveySubMenu;
        }

    }

    public void setSurveySubMenu(String surveySubMenu) {
        this.mSurveySubMenu = surveySubMenu;
    }

    public String getSurveySubMenuDetails() {
        return mSurveySubMenuDetails;
    }

    public void setSurveySubMenuDetails(String surveySubMenuDetails) {
        this.mSurveySubMenuDetails = surveySubMenuDetails;
    }

    public String getSurveyOutletMenu() {
        return mSurveyOutletMenu;
    }

    public void setSurveyOutletMenu(String surveyOutletMenu) {
        this.mSurveyOutletMenu = surveyOutletMenu;
    }

    public String getSurveyRoutePlan() {
        return mSurveyRoutePlan;
    }

    public void setSurveyRoutePlan(String surveyRoutePlan) {
        this.mSurveyRoutePlan = surveyRoutePlan;
    }

    public String getSurveyOtherText() {
        return mSurveyOtherText;
    }

    public void setSurveyOtherText(String surveyOtherText) {
        this.mSurveyOtherText = surveyOtherText;
    }

    public String getCustomerEmailUpdate() {
        return customer_email_update;
    }

    public void setCustomerEmailUpdate(String customer_email_update) {
        this.customer_email_update = customer_email_update;
    }
    public String getspecial_input_screen() {
        //return "yes";
        return special_input_screen;
    }

    public void setspecial_input_screen(String special_input_screen) {
        this.special_input_screen = special_input_screen;
    }
    public String getsurvey_menu_name() {
        return survey_menu_name;
    }

    public void setsurvey_menu_name(String survey_menu_name) {
        this.survey_menu_name = survey_menu_name;
    }
    public String getmenu_disp_sub_menu() {
        return menu_disp_sub_menu;
    }

    public void setmenu_disp_sub_menu(String menu_disp_sub_menu) {
        this.menu_disp_sub_menu = menu_disp_sub_menu;
    }

    public String getCheck_in_time_captured_menu() {
        return check_in_time_captured_menu;
    }

    public void setCheck_in_time_captured_menu(String check_in_time_captured_menu) {
        this.check_in_time_captured_menu = check_in_time_captured_menu;
    }

    public String getFollow_up_menu() {
        return follow_up_menu;
    }

    public void setFollow_up_menu(String follow_up_menu) {
        this.follow_up_menu = follow_up_menu;
    }
}