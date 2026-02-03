package com.forcepower.acedns.bean;

public class CashDeposit {
    String cash_deposit_trans_id = "";
    String bank_name = "";
    String deposit_value = "";

    public void setcash_deposit_trans_id(String cash_deposit_trans_id) {
        this.cash_deposit_trans_id = cash_deposit_trans_id;
    }

    public String getcash_deposit_trans_id() {
        return cash_deposit_trans_id;
    }

    public void setbank_name(String bank_name) {
        this.bank_name = bank_name;
    }

    public String getbank_name() {
        return bank_name;
    }

    public void setdeposit_value(String deposit_value) {
        this.deposit_value = deposit_value;
    }

    public String getdeposit_value() {
        return deposit_value;
    }
}
