package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.UserDetails;
import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.util.ArrayList;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

public class UserDetailsXMLParsing extends DefaultHandler {
    ArrayList<UserDetails> list = new ArrayList<>();
    StringBuilder sb;
    boolean userId;
    boolean name;
    boolean address;
    boolean phone;
    boolean email;
    boolean license;
    boolean noUser;
    boolean nickName;
    boolean noBranches;
    boolean emailHier;
    boolean verticalFields;
    boolean verticalFieldsValue;
    boolean previousStock;
    boolean timeStamp;
    boolean multipleProspect;
    boolean multipleProspectValue;
    boolean stkAuditScan;
    boolean stkAuditRate;
    boolean location_drag_drop;
    boolean tour_plan_day_wise;
    boolean check_in_out_typeval;
    boolean fcm;
    boolean minimumStock;
    boolean stockAuditUnit;//minimum stock unit
    boolean stk_audit_irrespective_routeplan;
    boolean stk_audit_cust_type;
    boolean notes_info_hint_remarks;
    boolean notes_info_upload_photo;
    boolean country;
    boolean time_zone;
    boolean stk_audit_msl;
    boolean app_phoneno_login;
    boolean tour_plan_daywise_distributor;
    boolean GPS_all_transaction;
    boolean menu_access_attendance;
    boolean primary_cust_type;
    boolean secondary_cust_type;
    boolean customer_product_relation;
    boolean stk_audit_mfd_date ;
    boolean departmentwise_geo_fencing_variance ;
    boolean stk_audit_UOM ;
    boolean CI_cust_display ;
    boolean CI_logic_updated ;
    boolean CI_customer_info_tab ;
    boolean business_prospect_phone_mandatory ;
    boolean business_prospect_customer_category ;
    private UserDetails details;


    public UserDetailsXMLParsing(String datafromserver) {
        SAXParserFactory spf = SAXParserFactory.newInstance();
        SAXParser sp = null;
        XMLReader xr = null;
        try {
            sp = spf.newSAXParser();
        } catch (ParserConfigurationException e) {
            e.printStackTrace();
        } catch (SAXException e) {
            e.printStackTrace();
        }
        try {
            xr = sp.getXMLReader();

        } catch (SAXException e) {
            e.printStackTrace();
        }
        try {
            xr.setContentHandler(this);
            xr.parse(new InputSource(new ByteArrayInputStream(datafromserver.getBytes("ISO-8859-1"))));
        } catch (IOException e) {
            e.printStackTrace();
        } catch (SAXException e) {
            e.printStackTrace();
        }
    }

    public UserDetails getParsedData() {
        return this.details;
    }

    @Override
    public void startDocument() throws SAXException {
        super.startDocument();
    }


    @Override
    public void endDocument() throws SAXException {
        super.endDocument();
    }


    public void startElement(String uri, String localName, String qName,
                             Attributes attributes) throws SAXException {
        // Log.i("tag start",localName);

        sb = new StringBuilder();

        super.startElement(uri, localName, qName, attributes);

        if (localName.equalsIgnoreCase("data")) {
            details = new UserDetails();
        }
        if (localName.equalsIgnoreCase("user_id")) {
            userId = true;
        }
        if (localName.equalsIgnoreCase("name")) {
            name = true;
        }
        if (localName.equalsIgnoreCase("address")) {
            address = true;
        }
        if (localName.equalsIgnoreCase("phone_no")) {
            phone = true;
        }
        if (localName.equalsIgnoreCase("email")) {
            email = true;
        }
        if (localName.equalsIgnoreCase("license_key")) {
            license = true;
        }
        if (localName.equalsIgnoreCase("no_users")) {
            noUser = true;
        }
        if (localName.equalsIgnoreCase("nick_name")) {
            nickName = true;
        }
        if (localName.equalsIgnoreCase("no_of_branches")) {
            noBranches = true;
        }
        if (localName.equalsIgnoreCase("email_hierarchywise")) {
            emailHier = true;
        }

        if (localName.equalsIgnoreCase("vertical_fields")) {
            verticalFields = true;
        }
        if (localName.equalsIgnoreCase("vertical_fields_value")) {
            verticalFieldsValue = true;
        }
        if (localName.equalsIgnoreCase("previous_stock")) {
            previousStock = true;
        }
        if (localName.equalsIgnoreCase("last_update_time")) {
            timeStamp = true;
        }
        if (localName.equalsIgnoreCase("multiple_prospect")) {
            multipleProspect = true;
        }
        if (localName.equalsIgnoreCase("multiple_prospect_value")) {
            multipleProspectValue = true;
        }
        if (localName.equalsIgnoreCase("stock_audit_scan")) {
            stkAuditScan = true;
        }
        if (localName.equalsIgnoreCase("stock_audit_rate")) {
            stkAuditRate = true;
        }

        if (localName.equalsIgnoreCase("location_drag_drop")) {
            location_drag_drop = true;
        }
        if (localName.equalsIgnoreCase("tour_plan_daywise")) {
            tour_plan_day_wise = true;
        }
        if (localName.equalsIgnoreCase("check_in_out_typeval")) {
            check_in_out_typeval = true;
        }
        if (localName.equalsIgnoreCase("fcm")) {
            fcm = true;
        }
        if (localName.equalsIgnoreCase("minimum_stock")) {
            minimumStock = true;
        }
        if (localName.equalsIgnoreCase("stk_audit_unit")) {
            stockAuditUnit = true;
        }
        if (localName.equalsIgnoreCase("stk_audit_irrespective_routeplan")) {
            stk_audit_irrespective_routeplan = true;
        }
        if (localName.equalsIgnoreCase("stk_audit_cust_type")) {
            stk_audit_cust_type = true;
        }
        if (localName.equalsIgnoreCase("notes_info_hint_remarks")) {
            notes_info_hint_remarks = true;
        }
        if (localName.equalsIgnoreCase("notes_info_upload_photo")) {
            notes_info_upload_photo = true;
        }
        if (localName.equalsIgnoreCase("country")) {
            country = true;
        }
        if (localName.equalsIgnoreCase("time_zone")) {
            time_zone = true;
        }
        if (localName.equalsIgnoreCase("stk_audit_msl")) {
            stk_audit_msl = true;
        }
        if (localName.equalsIgnoreCase("app_phoneno_login")) {
            app_phoneno_login = true;
        }
        if (localName.equalsIgnoreCase("tour_plan_daywise_distributor")) {
            tour_plan_daywise_distributor = true;
        }
        if (localName.equalsIgnoreCase("GPS_all_transaction")) {
            GPS_all_transaction = true;
        }
        if (localName.equalsIgnoreCase("menu_access_attendance")) {
            menu_access_attendance = true;
        }
        if (localName.equalsIgnoreCase("primary_cust_type")) {
            primary_cust_type = true;
        }
        if (localName.equalsIgnoreCase("secondary_cust_type")) {
            secondary_cust_type = true;
        }
        if (localName.equalsIgnoreCase("customer_product_relation")) {
            customer_product_relation = true;
        }
        if (localName.equalsIgnoreCase("stk_audit_mfd_date")) {
            stk_audit_mfd_date = true;
        }
        if (localName.equalsIgnoreCase("departmentwise_geo_fencing_variance")) {
            departmentwise_geo_fencing_variance = true;
        }
        if (localName.equalsIgnoreCase("stk_audit_UOM")) {
            stk_audit_UOM = true;
        }
        if (localName.equalsIgnoreCase("CI_cust_display")) {
            CI_cust_display = true;
        }
        if (localName.equalsIgnoreCase("CI_logic_updated")) {
            CI_logic_updated = true;
        }
        if (localName.equalsIgnoreCase("CI_customer_info_tab")) {
            CI_customer_info_tab = true;
        }
        if (localName.equalsIgnoreCase("business_prospect_phone_mandatory")) {
            business_prospect_phone_mandatory = true;
        }
        if (localName.equalsIgnoreCase("business_prospect_customer_category")) {
            business_prospect_customer_category = true;
        }

    }

    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (userId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }


        if (name) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (address) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }


        if (phone) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (email) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (license) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (noUser) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }


        if (nickName) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (noBranches) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (emailHier) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (verticalFields) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (verticalFieldsValue) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (previousStock) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (multipleProspect) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (multipleProspectValue) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stkAuditScan) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stkAuditRate) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (timeStamp) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (location_drag_drop) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (tour_plan_day_wise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (check_in_out_typeval) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (fcm) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (minimumStock) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stockAuditUnit) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stk_audit_irrespective_routeplan) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stk_audit_cust_type) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (notes_info_hint_remarks) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (notes_info_upload_photo) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (country) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (time_zone) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stk_audit_msl) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (app_phoneno_login) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (tour_plan_daywise_distributor) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (GPS_all_transaction) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (menu_access_attendance) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (primary_cust_type) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (secondary_cust_type) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (customer_product_relation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stk_audit_mfd_date) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (departmentwise_geo_fencing_variance) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stk_audit_UOM) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (CI_cust_display) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (CI_logic_updated) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (CI_customer_info_tab) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (business_prospect_phone_mandatory) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (business_prospect_customer_category) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

    }


    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        //  Log.i("tag end",localName);
        super.endElement(uri, localName, qName);

        if (userId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUserId(trueData);
            userId = false;
        }

        if (name) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setName(trueData);
            name = false;
        }

        if (address) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAddress(trueData);
            address = false;
        }

        if (phone) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPhoneNo(trueData);
            phone = false;
        }

        if (email) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setEmail(trueData);
            email = false;
        }

        if (license) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLicenseKey(trueData);
            license = false;
        }

        if (noUser) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setNoUser(trueData);
            noUser = false;
        }

        if (nickName) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setNickName(trueData);
            nickName = false;
        }

        if (noBranches) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setNoBranches(trueData);
            noBranches = false;
        }

        if (emailHier) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setEmailHierarchy(trueData);
            emailHier = false;
        }

        if (verticalFields) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setVerticalFields(trueData);
            verticalFields = false;
        }


        if (verticalFieldsValue) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setVerticalFieldsValue(trueData);
            verticalFieldsValue = false;
        }

        if (previousStock) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPreviousStock(trueData);
            previousStock = false;
        }

        if (timeStamp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLastUpdateTime(trueData);
            timeStamp = false;
        }

        if (multipleProspect) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMultipleProspect(trueData);
            multipleProspect = false;
        }

        if (multipleProspectValue) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMultipleProspectValue(trueData);
            multipleProspectValue = false;
        }

        if (stkAuditScan) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setStkAuditScan(trueData);
            stkAuditScan = false;
        }

        if (stkAuditRate) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setStockAuditRate(trueData);
            stkAuditRate = false;
        }

        if (location_drag_drop) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            System.out.println("DATA_DRAG" + trueData);
            details.setLocation_drag_drop(trueData);
            location_drag_drop = false;
        }
        if (tour_plan_day_wise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTourPlanDayWise(trueData);
            tour_plan_day_wise = false;
        }
        if (check_in_out_typeval) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCheckInOutTypeVal(trueData);
            check_in_out_typeval = false;
        }
        if (fcm) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setFcm(trueData);
            fcm = false;
        }
        if (minimumStock) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMinimumStock(trueData);
            minimumStock = false;
        }
        if (stockAuditUnit) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setStockAuditUnit(trueData);
            stockAuditUnit = false;
        }
        if (stk_audit_irrespective_routeplan) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setstk_audit_irrespective_routeplan(trueData);
            stk_audit_irrespective_routeplan = false;
        }
        if (stk_audit_cust_type) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setstk_audit_cust_type(trueData);
            stk_audit_cust_type = false;
        }
        if (notes_info_hint_remarks) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setnotes_info_hint_remarks(trueData);
            notes_info_hint_remarks = false;
        }
        if (notes_info_upload_photo) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setnotes_info_upload_photo(trueData);
            notes_info_upload_photo = false;
        }
        if (country) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcountry(trueData);
            country = false;
        }
        if (time_zone) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.settimeZone(trueData);
            time_zone = false;
        }
        if (stk_audit_msl) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setstk_audit_msl(trueData);
            stk_audit_msl = false;
        }
        if (app_phoneno_login) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setapp_phoneno_login(trueData);
            app_phoneno_login = false;
        }
        if (tour_plan_daywise_distributor) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.settour_plan_daywise_distributor(trueData);
            tour_plan_daywise_distributor = false;
        }
        if (GPS_all_transaction) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setGPS_all_transaction(trueData);
            GPS_all_transaction = false;
        }
        if (menu_access_attendance) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setmenu_access_attendance(trueData);
            menu_access_attendance = false;
        }
        if (primary_cust_type) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setprimary_cust_type(trueData);
            primary_cust_type = false;
        }
        if (secondary_cust_type) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setsecondary_cust_type(trueData);
            secondary_cust_type = false;
        }
        if (customer_product_relation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcustomer_product_relation(trueData);
            customer_product_relation = false;
        }
        if (stk_audit_mfd_date) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setstk_audit_mfd_date(trueData);
            stk_audit_mfd_date = false;
        }
        if (departmentwise_geo_fencing_variance) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setdepartmentwise_geo_fencing_variance(trueData);
            departmentwise_geo_fencing_variance = false;
        }
        if (stk_audit_UOM) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setstk_audit_UOM(trueData);
            stk_audit_UOM = false;
        }
        if (CI_cust_display) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCI_cust_display(trueData);
            CI_cust_display = false;
        }
        if (CI_logic_updated) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCI_logic_updated(trueData);
            CI_logic_updated = false;
        }
        if (CI_customer_info_tab) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCI_customer_info_tab(trueData);
            CI_customer_info_tab = false;
        }
        if (business_prospect_phone_mandatory) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setbusiness_prospect_phone_mandatory(trueData);
            business_prospect_phone_mandatory = false;
        }
        if (business_prospect_customer_category) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBusiness_prospect_customer_category(trueData);
            business_prospect_customer_category = false;
        }

        if (localName.equalsIgnoreCase("data")) {
            list.add(details);
        }
    }


    public String getTrueData(String data) {
        return data;
    }

}