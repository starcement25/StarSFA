package com.forcepower.acedns.bean;

public class DatabaseStructure {
    String tableName = "";
    String query = "";
    String transaction = "";
    String dbVersion = "";
    String mBaseUrlChaged = "";
    String mCurrntUrl = "";

    public String getDbVersion() {
        return dbVersion;
    }

    public void setDbVersion(String dbVersion) {
        this.dbVersion = dbVersion;
    }

    public String getTransaction() {
        return transaction;
    }

    public void setTransaction(String transaction) {
        this.transaction = transaction;
    }

    public String getTableName() {
        return tableName;
    }

    public void setTableName(String tableName) {
        this.tableName = tableName;
    }

    public String getQuery() {
        return query;
    }

    public void setQuery(String query) {
        this.query = query;
    }

    public String getBaseUrlChanged() {
        return mBaseUrlChaged;
    }

    public void setBaseUrlChanged(String baseUrlChaged) {
        this.mBaseUrlChaged = baseUrlChaged;
    }

    public String getCurrentUrl() {
        return mCurrntUrl;
    }

    public void setCurrentUrl(String currentUrl) {
        this.mCurrntUrl = currentUrl;
    }
}
