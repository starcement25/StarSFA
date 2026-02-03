package com.forcepower.acedns.bean;

public class YellowCardDateValidation {
    String validationMonth = "";
    String validationDate = "";
    String customer_code = "";
    String validation_from = "";
    String validation_to = "";
    String validation_last_date = "";


    public void setvalidationMonth(String validationMonth) {
        this.validationMonth = validationMonth;
    }

    public String getvalidationMonth() {
        return validationMonth;
    }

    public void setvalidationDate(String validationDate) {
        this.validationDate = validationDate;
    }

    public String getvalidationDate() {
        return validationDate;
    }

    public String getCustomer_code() {
        return customer_code;
    }

    public void setCustomer_code(String customer_code) {
        this.customer_code = customer_code;
    }

    public String getValidation_from() {
        return validation_from;
    }

    public void setValidation_from(String validation_from) {
        this.validation_from = validation_from;
    }

    public String getValidation_to() {
        return validation_to;
    }

    public void setValidation_to(String validation_to) {
        this.validation_to = validation_to;
    }

    public String getValidation_last_date() {
        return validation_last_date;
    }

    public void setValidation_last_date(String validation_last_date) {
        this.validation_last_date = validation_last_date;
    }
}
