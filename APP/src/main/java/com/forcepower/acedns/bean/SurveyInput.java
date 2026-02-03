package com.forcepower.acedns.bean;

public class SurveyInput {
    String mSurveyRowId = "";
    String mSurveyActionId = "";
    String mSurveyMenuId = "";
    String mSurveyLayoutName = "";
    String mSurveyDisplayName = "";
    String mSurveyType = "";
    String mSurveyTableName = "";
    String mSurveyMadatory = "";
    String mSurveyAction = "";
    String mSurveyValidation = "";
    String mSurveyDisplayOrder = "";
    String mSurveySurveyType = "";
    String mSurveySubMenu = "";
    String mSurveyAceDns = "";
    String mSurveyClause = "";
    String value = "";
    String status = "";
    String mallID = "";
    String businessName = "";
    String transId = "";
    String surveyId = "";
    String insert_table_detail = "";
    String row_id_dependency_clause = "";
    String acedns = "";

    public void setsurveyId(String surveyId) {
        this.surveyId = surveyId;
    }

    public String getsurveyId() {
        return surveyId;
    }

    public void setransId(String transId) {
        this.transId = transId;
    }

    public String gettransId() {
        return transId;
    }

    public void setmallID(String mallID) {
        this.mallID = mallID;
    }

    public String getmallID() {
        return mallID;
    }

    public void setbusinessName(String businessName) {
        this.businessName = businessName;
    }

    public String getbusinessName() {
        return businessName;
    }

    public String getSurveyRowId() {
        return mSurveyRowId;
    }

    public void setSurveyRowId(String surveyRowId) {
        this.mSurveyRowId = surveyRowId;
    }

    public String getSurveyActionId() {
        return mSurveyActionId;
    }

    public void setSurveyActionId(String surveyActionId) {
        this.mSurveyActionId = surveyActionId;
    }

    public String getSurveyMenuId() {
        return mSurveyMenuId;
    }

    public void setSurveyMenuId(String surveyMenuId) {
        this.mSurveyMenuId = surveyMenuId;
    }

    public String getSurveyLayoutName() {
        return mSurveyLayoutName;
    }

    public void setSurveyLayoutName(String surveyLayoutName) {
        this.mSurveyLayoutName = surveyLayoutName;
    }

    public String getSurveyDisplayName() {
        return mSurveyDisplayName;
    }

    public void setSurveyDisplayName(String surveyDisplayName) {
        this.mSurveyDisplayName = surveyDisplayName;
    }

    public String getSurveyType() {
        return mSurveyType;
    }

    public void setSurveyType(String surveyType) {
        this.mSurveyType = surveyType;
    }

    public String getSurveyTableName() {
        return mSurveyTableName;
    }

    public void setSurveyTableName(String surveyTableName) {
        this.mSurveyTableName = surveyTableName;
    }

    public String getSurveyMadatory() {
        return mSurveyMadatory;
    }

    public void setSurveyMadatory(String surveyMadatory) {
        this.mSurveyMadatory = surveyMadatory;
    }

    public String getSurveyAction() {
        return mSurveyAction;
    }

    public void setSurveyAction(String surveyAction) {
        this.mSurveyAction = surveyAction;
    }

    public String getSurveyValidation() {
        return mSurveyValidation;
    }

    public void setSurveyValidation(String surveyValidation) {
        this.mSurveyValidation = surveyValidation;
    }

    public String getSurveyDisplayOrder() {
        return mSurveyDisplayOrder;
    }

    public void setSurveyDisplayOrder(String surveyDisplayOrder) {
        this.mSurveyDisplayOrder = surveyDisplayOrder;
    }

    public String getSurveySurveyType() {
        return mSurveySurveyType;
    }

    public void setSurveySurveyType(String surveySurveyType) {
        this.mSurveySurveyType = surveySurveyType;
    }

    public String getSurveySubMenu() {
        return mSurveySubMenu;
    }

    public void setSurveySubMenu(String surveySubMenu) {
        this.mSurveySubMenu = surveySubMenu;
    }

    public String getSurveyClause() {
        return mSurveyClause;
    }

    public void setSurveyClause(String surveyClause) {
        this.mSurveyClause = surveyClause;
    }

    public String getAceDns() {
        return mSurveyAceDns;
    }

    public void setAceDns(String mSurveyAceDns) {
        this.mSurveyAceDns = mSurveyAceDns;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getValue() {
        return value;
    }

    public void setValue(String value) {
        this.value = value;
    }
    public String getinsert_table_detail() {
        return insert_table_detail;
    }

    public void setinsert_table_detail(String insert_table_detail) {
        this.insert_table_detail = insert_table_detail;
    }

    public String getRow_id_dependency_clause() {
        try{
            if(row_id_dependency_clause!=null){
                return row_id_dependency_clause;
            }
            else{
                return "";
            }

        }
        catch (Exception e){
            return  "";
        }

    }

    public void setRow_id_dependency_clause(String row_id_dependency_clause) {
        this.row_id_dependency_clause = row_id_dependency_clause;
    }

}

