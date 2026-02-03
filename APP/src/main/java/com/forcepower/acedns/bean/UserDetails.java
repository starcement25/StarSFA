package com.forcepower.acedns.bean;

public class UserDetails {

    String userId = "";
    String name = "";
    String address = "";
    String phoneNo = "";
    String email = "";
    String licenseKey = "";
    String noUser = "";
    String nickName = "";
    String noBranches = "";
    String emailHierarchy = "";
    byte[] imgArray;
    String verticalFields = "";
    String verticalFieldsValue = "";
    String previousStock = "";
    String lastUpdateTime = "";
    String multipleProspect = "";
    String multipleProspectValue = "";
    String stkAuditScan = "";
    String mStockAuditRate = "";
    String location_drag_drop = "";
    String tour_plan_daywise = "";
    String check_in_out_typeval = "";
    String fcm = "";
    String minimumStock = "";
    String stockAuditUnit = "";//minimum stock unit
    String stk_audit_irrespective_routeplan = "";
    String stk_audit_cust_type = "";
    String notes_info_hint_remarks = "";
    String notes_info_upload_photo = "";
    String country = "";
    String timeZone = "";
    String stk_audit_msl = "";
    String currency = "";
    String app_phoneno_login = "";
    String tour_plan_daywise_distributor = "";
    String GPS_all_transaction = "";
    String menu_access_attendance = "";
    String primary_cust_type = "";
    String secondary_cust_type  = "";
    String customer_product_relation  = "";
    String stk_audit_mfd_date  = "";
    String departmentwise_geo_fencing_variance  = "";
    String stk_audit_UOM  = "";
    String CI_cust_display  = "";
    String CI_logic_updated  = "";
    String CI_customer_info_tab  = "";
    String business_prospect_phone_mandatory  = "";
    String business_prospect_customer_category ="";

    public String getStkAuditScan() {
        return stkAuditScan;
    }

    public void setStkAuditScan(String stkAuditScan) {
        this.stkAuditScan = stkAuditScan;
    }

    public String getMultipleProspect() {
        return multipleProspect;
    }

    public void setMultipleProspect(String multipleProspect) {
        this.multipleProspect = multipleProspect;
    }

    public String getMultipleProspectValue() {
        return multipleProspectValue;
    }

    public void setMultipleProspectValue(String multipleProspectValue) {
        this.multipleProspectValue = multipleProspectValue;
    }

    public String getLastUpdateTime() {
        return lastUpdateTime;
    }

    public void setLastUpdateTime(String lastUpdateTime) {
        this.lastUpdateTime = lastUpdateTime;
    }

    public String getPreviousStock() {
        return previousStock;
    }

    public void setPreviousStock(String previousStock) {
        this.previousStock = previousStock;
    }

    public final String getVerticalFields() {
        return verticalFields;
    }

    public final void setVerticalFields(String verticalFields) {
        this.verticalFields = verticalFields;
    }

    public final String getVerticalFieldsValue() {
        return verticalFieldsValue;
    }

    public final void setVerticalFieldsValue(String verticalFieldsValue) {
        this.verticalFieldsValue = verticalFieldsValue;
    }

    public byte[] getImgArray() {
        return imgArray;
    }

    public void setImgArray(byte[] imgArray) {
        this.imgArray = imgArray;
    }

    public String getUserId() {
        return userId;
    }

    public void setUserId(String userId) {
        this.userId = userId;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getPhoneNo() {
        return phoneNo;
    }

    public void setPhoneNo(String phoneNo) {
        this.phoneNo = phoneNo;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getLicenseKey() {
        return licenseKey;
    }

    public void setLicenseKey(String licenseKey) {
        this.licenseKey = licenseKey;
    }

    public String getNoUser() {
        return noUser;
    }

    public void setNoUser(String noUser) {
        this.noUser = noUser;
    }

    public String getNickName() {
        return nickName;
    }

    public void setNickName(String nickName) {
        this.nickName = nickName;
    }

    public String getNoBranches() {
        return noBranches;
    }

    public void setNoBranches(String noBranches) {
        this.noBranches = noBranches;
    }

    public String getEmailHierarchy() {
        return emailHierarchy;
    }

    public void setEmailHierarchy(String emailHierarchy) {
        this.emailHierarchy = emailHierarchy;
    }

    public String getStockAuditRate() {
        return mStockAuditRate;
    }

    public void setStockAuditRate(String stockAuditRate) {
        this.mStockAuditRate = stockAuditRate;
    }

    public String getLocation_drag_drop() {
        return location_drag_drop;
    }

    public void setLocation_drag_drop(String location_drag_drop) {
        this.location_drag_drop = location_drag_drop;
    }

    public String getTourPlanDayWise()
    {
        try
        {
            return tour_plan_daywise;
        }
        catch (Exception e)
        {
            return "";
        }

    }

    public void setTourPlanDayWise(String tour_plan_daywise) {
        this.tour_plan_daywise = tour_plan_daywise;
    }

    public String getCheckInOutTypeVal() {
        return check_in_out_typeval;
    }

    public void setCheckInOutTypeVal(String check_in_out_typeval) {
        this.check_in_out_typeval = check_in_out_typeval;
    }

    public String getFcm()
    {
        if(fcm==null)
        {
            fcm="no";
        }
        return fcm;
    }

    public void setFcm(String fcm) {
        this.fcm = fcm;
    }

    public String getMinimumStock() {
        return minimumStock;
    }

    public void setMinimumStock(String minimumStock) {
        this.minimumStock = minimumStock;
    }

    public String getStockAuditUnit() {
        return stockAuditUnit;
    }

    public void setStockAuditUnit(String stockAuditUnit) {
        this.stockAuditUnit = stockAuditUnit;
    }

    public String getstk_audit_irrespective_routeplan() {
        return stk_audit_irrespective_routeplan;
    }

    public void setstk_audit_irrespective_routeplan(String stk_audit_irrespective_routeplan) {
        this.stk_audit_irrespective_routeplan = stk_audit_irrespective_routeplan;
    }

    public String getstk_audit_cust_type() {
        return stk_audit_cust_type;
    }

    public void setstk_audit_cust_type(String stk_audit_cust_type) {
        this.stk_audit_cust_type = stk_audit_cust_type;
    }

    public String getnotes_info_hint_remarks() {
        return notes_info_hint_remarks;
    }

    public void setnotes_info_hint_remarks(String notes_info_hint_remarks) {
        this.notes_info_hint_remarks = notes_info_hint_remarks;
    }

    public String getnotes_info_upload_photo() {
        return notes_info_upload_photo;
    }

    public void setnotes_info_upload_photo(String notes_info_upload_photo) {
        this.notes_info_upload_photo = notes_info_upload_photo;
    }

    public String getcountry() {
        return country;
    }

    public void setcountry(String country) {
        this.country = country;
    }

    public String gettimeZone() {
        return timeZone;
    }

    public void settimeZone(String timeZone) {
        this.timeZone = timeZone;
    }

    public String getstk_audit_msl() {
        return stk_audit_msl;
    }

    public void setstk_audit_msl(String stk_audit_msl) {
        this.stk_audit_msl = stk_audit_msl;
    }

    public String getcurrency() {
        return currency;
    }

    public void setcurrency(String currency) {
        this.currency = currency;
    }

    public String getapp_phoneno_login() {
        return app_phoneno_login;
    }

    public void setapp_phoneno_login(String app_phoneno_login) {
        this.app_phoneno_login = app_phoneno_login;
    }

    public String gettour_plan_daywise_distributor() {
        return tour_plan_daywise_distributor;
    }

    public void settour_plan_daywise_distributor(String tour_plan_daywise_distributor) {
        this.tour_plan_daywise_distributor = tour_plan_daywise_distributor;
    }

    public String getGPS_all_transaction() {
        return GPS_all_transaction;
    }

    public void setGPS_all_transaction(String GPS_all_transaction) {
        this.GPS_all_transaction = GPS_all_transaction;
    }
    public String getmenu_access_attendance() {
        return menu_access_attendance.trim();
    }

    public void setmenu_access_attendance(String menu_access_attendance) {
        this.menu_access_attendance = menu_access_attendance;
    }

    public String getprimary_cust_type() {
        return primary_cust_type.trim();
    }

    public void setprimary_cust_type(String primary_cust_type) {
        this.primary_cust_type = primary_cust_type;
    }

    public String getsecondary_cust_type() {
        return secondary_cust_type.trim();
    }

    public void setsecondary_cust_type (String secondary_cust_type ) {
        this.secondary_cust_type  = secondary_cust_type ;
    }

    public String getcustomer_product_relation() {
        return customer_product_relation.trim();
    }

    public void setcustomer_product_relation (String customer_product_relation ) {
        this.customer_product_relation  = customer_product_relation ;
    }

    public String getstk_audit_mfd_date() {
        return stk_audit_mfd_date.trim();
    }

    public void setstk_audit_mfd_date (String stk_audit_mfd_date ) {
        this.stk_audit_mfd_date  = stk_audit_mfd_date ;
    }
    public String getdepartmentwise_geo_fencing_variance() {
        return departmentwise_geo_fencing_variance.trim();
    }

    public void setdepartmentwise_geo_fencing_variance (String departmentwise_geo_fencing_variance ) {
        this.departmentwise_geo_fencing_variance  = departmentwise_geo_fencing_variance ;
    }

    public String getstk_audit_UOM() {
        return stk_audit_UOM.trim();
    }

    public void setstk_audit_UOM (String stk_audit_UOM ) {
        this.stk_audit_UOM  = stk_audit_UOM ;
    }

    public String getCI_cust_display() {
        return CI_cust_display.trim();
    }

    public void setCI_cust_display (String CI_cust_display ) {
        this.CI_cust_display  = CI_cust_display ;
    }


    public String getCI_logic_updated() {
        return CI_logic_updated.trim();
    }

    public void setCI_logic_updated (String CI_logic_updated ) {
        this.CI_logic_updated  = CI_logic_updated ;
    }

    public String getCI_customer_info_tab() {
        return CI_customer_info_tab.trim();
    }

    public void setCI_customer_info_tab (String CI_customer_info_tab ) {
        this.CI_customer_info_tab  = CI_customer_info_tab ;
    }
    public String getbusiness_prospect_phone_mandatory() {
        return business_prospect_phone_mandatory.trim();
    }

    public void setbusiness_prospect_phone_mandatory (String business_prospect_phone_mandatory ) {
        this.business_prospect_phone_mandatory  = business_prospect_phone_mandatory ;
    }

    public String getBusiness_prospect_customer_category() {
        return business_prospect_customer_category;
    }

    public void setBusiness_prospect_customer_category(String business_prospect_customer_category) {
        this.business_prospect_customer_category = business_prospect_customer_category;
    }
}
