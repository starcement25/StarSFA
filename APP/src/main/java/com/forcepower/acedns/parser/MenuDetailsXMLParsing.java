package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.MenuDetails;
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

public class MenuDetailsXMLParsing extends DefaultHandler {

    ArrayList<MenuDetails> list = new ArrayList<MenuDetails>();
    StringBuilder sb;
    boolean menuId;
    boolean userId;
    boolean attndnc;
    boolean routePlan;
    boolean order;
    boolean collection;
    boolean stkAudit;
    boolean businessPros;
    boolean tourExp;
    boolean captureImg;
    boolean notesInfo;
    boolean activityReport;
    boolean loyalty;
    boolean timeStamp;
    boolean misReport;
    boolean deleteTransaction;
    boolean loadingFreight;
    boolean saudaalloc;
    boolean isSurvey;
    boolean isPromotion;
    boolean isReplacement;
    boolean isMarketFeedback;
    boolean isSaudaBookedFromApp;
    boolean isPendingContract;
    boolean isSaudaMis;
    boolean isOrderStatus;
    boolean isCheckout;
    boolean isSaudaOutstanding;
    boolean isSalePerformance;
    boolean isCheckInOut;
    boolean isOutstanding;
    boolean isOutstandingAgeing;
    boolean isTargetAchevement;
    boolean isWholeSaleInfo;
    boolean isSelfAppraisalDetails;
    boolean isYellowCard;
    boolean isCatalogue;
    boolean isCatalogueUrl;
    boolean isTelephonicTransaction;
    boolean isTDAllocation;
    boolean catalogue_dependency;
    boolean TD_allocation_vertical;
    boolean run_time_TD_approval_vertical;
    boolean quotation;
    boolean CRM_app;
    boolean ISP;
    boolean monthly_report_mail;
    boolean retailer_app;
    boolean scheme;
    boolean reverse_auction;
    boolean retailer_care;
    boolean branchwise_scheme_PDF;
    boolean collection_forecast;
    boolean golden_rules;
    boolean manager_activity;
    boolean independent_check_in_out;
    boolean check_in_out_menu_access;
    boolean van_sales;
    boolean bargain;
    boolean bargain_TD;
    boolean DO;
    boolean branchwise_geo_fencing ;
    boolean DO_status ;
    boolean geo_fencing_menu ;
    boolean joint_work ;
    boolean generate_pricing ;
    boolean app_order_approval ;
    boolean cust_class  ;
    boolean hierarchical_report  ;
    boolean grn  ;
    boolean order_edit  ;
    boolean generate_pricing_MCX   ;
    boolean stock_audit_edit   ;
    boolean CI_logic   ;
    boolean scheme_pdf   ;
    boolean gift_delivery   ;
    boolean attendance_journey_info   ;
    boolean checkout_journey_info   ;
    boolean attendance_journey_info_options    ;
    boolean dealer_visit     ;
    boolean TM_approval     ;
    boolean TM_approved_meeting      ;
    boolean tour_exp_fuel_bill      ;
    boolean app_order_approval_additional_flg      ;
    boolean weightage_calc_flg;
    boolean sis_report_flg;
    boolean checkout_time_flg;
    boolean odometer_flg;
    boolean doctor_visit_flg;
    boolean customer_product_stock_flg;
    boolean beat_wise_activity_flg;
    boolean stockist_visit_flg;
    boolean prop_from_details_flg;
    boolean order_summary_PDF_flg;
    boolean TA_DA_km_tracking_mode_flg;
    boolean dsr_pdf_flg;
    boolean yellow_card_cust_type_flg;
    boolean yellow_card_product_flg;
    boolean hoarding_emp_vendor_flg;
    boolean mf_mandatory_details_flg;
    boolean purpose_of_visit_flg;
    boolean site_visit_approval_flg;
    boolean multi_travel_mode_flg;
    boolean feedback_backup_flg;
    boolean dashboard_flg;
    boolean manchtech_flg;
    boolean leaderboard_flg;
    boolean bd_leaderboard_flg;
    boolean lead_generation_approval_flg;
    boolean mle_flg;


    private MenuDetails details;

    public MenuDetailsXMLParsing(String datafromserver) {
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

    public MenuDetails getParsedData() {
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
            details = new MenuDetails();
        }

        if (localName.equalsIgnoreCase("menu_id")) {
            menuId = true;
        }
        if (localName.equalsIgnoreCase("user_id")) {
            userId = true;
        }
        if (localName.equalsIgnoreCase("attendance")) {
            attndnc = true;
        }
        if (localName.equalsIgnoreCase("route_plan")) {
            routePlan = true;
        }
        if (localName.equalsIgnoreCase("order")) {
            order = true;
        }
        if (localName.equalsIgnoreCase("collection")) {
            collection = true;
        }
        if (localName.equalsIgnoreCase("stk_audit")) {
            stkAudit = true;
        }
        if (localName.equalsIgnoreCase("business_prospect")) {
            businessPros = true;
        }
        if (localName.equalsIgnoreCase("tour_exp")) {
            tourExp = true;
        }
        if (localName.equalsIgnoreCase("capture_image")) {
            captureImg = true;
        }
        if (localName.equalsIgnoreCase("notes_and_info")) {
            notesInfo = true;
        }
        if (localName.equalsIgnoreCase("activity_report")) {
            activityReport = true;
        }
        if (localName.equalsIgnoreCase("loyalty")) {
            loyalty = true;
        }
        if (localName.equalsIgnoreCase("last_update_time")) {
            timeStamp = true;
        }
        if (localName.equalsIgnoreCase("mis_report")) {
            misReport = true;
        }
        if (localName.equalsIgnoreCase("delete_transaction")) {
            deleteTransaction = true;
        }
        if (localName.equalsIgnoreCase("loading_freight")) {
            loadingFreight = true;
        }
        if (localName.equalsIgnoreCase("sauda_allocation")) {
            saudaalloc = true;
        }
        if (localName.equalsIgnoreCase("survey")) {
            isSurvey = true;
        }
        if (localName.equalsIgnoreCase("product_promotion")) {
            isPromotion = true;
        }
        if (localName.equalsIgnoreCase("replacement")) {
            isReplacement = true;
        }
        if (localName.equalsIgnoreCase("market_feedback")) {
            isMarketFeedback = true;
        }
        if (localName.equalsIgnoreCase("sauda_allocation_app")) {
            isSaudaBookedFromApp = true;
        }
        if (localName.equalsIgnoreCase("pending_contract")) {
            isPendingContract = true;
        }
        if (localName.equalsIgnoreCase("sauda_mis")) {
            isSaudaMis = true;
        }
        if (localName.equalsIgnoreCase("order_status")) {
            isOrderStatus = true;
        }
        if (localName.equalsIgnoreCase("checkout")) {
            isCheckout = true;
        }
        if (localName.equalsIgnoreCase("sauda_outstanding")) {
            isSaudaOutstanding = true;
        }
        if (localName.equalsIgnoreCase("sale_performance")) {
            isSalePerformance = true;
        }
        if (localName.equalsIgnoreCase("check_in_out")) {
            isCheckInOut = true;
        }
        if (localName.equalsIgnoreCase("outstanding")) {
            isOutstanding = true;
        }
        if (localName.equalsIgnoreCase("outstanding_ageing")) {
            isOutstandingAgeing = true;
        }
        if (localName.equalsIgnoreCase("target_achievement")) {
            isTargetAchevement = true;
        }
        if (localName.equalsIgnoreCase("wholesaler_info")) {
            isWholeSaleInfo = true;
        }
        if (localName.equalsIgnoreCase("self_appraisal")) {
            isSelfAppraisalDetails = true;
        }
        if (localName.equalsIgnoreCase("yellow_card")) {
            isYellowCard = true;
        }
        if (localName.equalsIgnoreCase("catalogue")) {
            isCatalogue = true;
        }
        if (localName.equalsIgnoreCase("catalogue_url")) {
            isCatalogueUrl = true;
        }
        if (localName.equalsIgnoreCase("tele_tran")) {
            isTelephonicTransaction = true;
        }
        if (localName.equalsIgnoreCase("TD_allocation_app")) {
            isTDAllocation = true;
        }
        if (localName.equalsIgnoreCase("catalogue_dependency")) {
            catalogue_dependency = true;
        }
        if (localName.equalsIgnoreCase("TD_allocation_vertical")) {
            TD_allocation_vertical = true;
        }
        if (localName.equalsIgnoreCase("run_time_TD_approval_vertical")) {
            run_time_TD_approval_vertical = true;
        }
        if (localName.equalsIgnoreCase("quotation")) {
            quotation = true;
        }
        if (localName.equalsIgnoreCase("CRM_app")) {
            CRM_app = true;
        }
        if (localName.equalsIgnoreCase("ISP")) {
            ISP = true;
        }
        if (localName.equalsIgnoreCase("monthly_report_mail")) {
            monthly_report_mail = true;
        }
        if (localName.equalsIgnoreCase("retailer_app")) {
            retailer_app = true;
        }
        if (localName.equalsIgnoreCase("schemes")) {
            scheme = true;
        }
        if (localName.equalsIgnoreCase("RA_sauda")) {
            reverse_auction = true;
        }
        if (localName.equalsIgnoreCase("retailer_care")) {
            retailer_care = true;
        }
        if (localName.equalsIgnoreCase("branchwise_scheme_PDF")) {
            branchwise_scheme_PDF = true;
        }
        if (localName.equalsIgnoreCase("collection_forecast")) {
            collection_forecast = true;
        }
        if (localName.equalsIgnoreCase("golden_rules")) {
            golden_rules = true;
        }
        if (localName.equalsIgnoreCase("manager_activity")) {
            manager_activity = true;
        }
        if (localName.equalsIgnoreCase("independent_check_in_out")) {
            independent_check_in_out = true;
        }
        if (localName.equalsIgnoreCase("check_in_out_menu_access")) {
            check_in_out_menu_access = true;
        }
        if (localName.equalsIgnoreCase("van_sales")) {
            van_sales = true;
        }
        if (localName.equalsIgnoreCase("bargain")) {
            bargain = true;
        }
        if (localName.equalsIgnoreCase("bargain_TD")) {
            bargain_TD = true;
        }
        if (localName.equalsIgnoreCase("DO")) {
            DO = true;
        }
        if (localName.equalsIgnoreCase("branchwise_geo_fencing")) {
            branchwise_geo_fencing = true;
        }
        if (localName.equalsIgnoreCase("DO_status")) {
            DO_status = true;
        }
        if (localName.equalsIgnoreCase("geo_fencing_menu")) {
            geo_fencing_menu = true;
        }
        if (localName.equalsIgnoreCase("joint_work")) {
            joint_work = true;
        }
        if (localName.equalsIgnoreCase("generate_pricing")) {
            generate_pricing = true;
        }
        if (localName.equalsIgnoreCase("app_order_approval")) {
            app_order_approval = true;
        }
        if (localName.equalsIgnoreCase("cust_class")) {
            cust_class = true;
        }
        if (localName.equalsIgnoreCase("hierarchical_report")) {
            hierarchical_report = true;
        }
        if (localName.equalsIgnoreCase("grn")) {
            grn = true;
        }
        if (localName.equalsIgnoreCase("order_edit")) {
            order_edit = true;
        }
        if (localName.equalsIgnoreCase("generate_pricing_MCX")) {
            generate_pricing_MCX = true;
        }

        if (localName.equalsIgnoreCase("stock_audit_edit")) {
            stock_audit_edit = true;
        }
        if (localName.equalsIgnoreCase("CI_logic")) {
            CI_logic = true;
        }
        if (localName.equalsIgnoreCase("scheme_pdf")) {
            scheme_pdf = true;
        }
        if (localName.equalsIgnoreCase("gift_delivery")) {
            gift_delivery = true;
        }
        if (localName.equalsIgnoreCase("attendance_journey_info")) {
            attendance_journey_info = true;
        }
        if (localName.equalsIgnoreCase("checkout_journey_info")) {
            checkout_journey_info = true;
        }
        if (localName.equalsIgnoreCase("attendance_journey_info_options")) {
            attendance_journey_info_options = true;
        }
        if (localName.equalsIgnoreCase("dealer_visit")) {
            dealer_visit = true;
        }
        if (localName.equalsIgnoreCase("TM_approval")) {
            TM_approval = true;
        }
        if (localName.equalsIgnoreCase("TM_approved_meeting")) {
            TM_approved_meeting = true;
        }

        if (localName.equalsIgnoreCase("tour_exp_fuel_bill")) {
            tour_exp_fuel_bill = true;
        }
        if (localName.equalsIgnoreCase("app_order_approval_additional")) {
            app_order_approval_additional_flg = true;
        }

        if (localName.equalsIgnoreCase("weightage_calc")) {
            weightage_calc_flg = true;
        }

        if (localName.equalsIgnoreCase("sis_report")) {
            sis_report_flg = true;
        }
        if (localName.equalsIgnoreCase("checkout_time")) {
            checkout_time_flg = true;
        }
        if (localName.equalsIgnoreCase("odometer")) {
            odometer_flg = true;
        }
        if (localName.equalsIgnoreCase("doctor_visit")) {
            doctor_visit_flg = true;
        }
        if (localName.equalsIgnoreCase("customer_product_stock")) {
            customer_product_stock_flg = true;
        }
        if (localName.equalsIgnoreCase("beat_wise_activity")) {
            beat_wise_activity_flg = true;
        }
        if (localName.equalsIgnoreCase("stockist_visit")) {
            stockist_visit_flg = true;
        }
        if (localName.equalsIgnoreCase("prop_form_details")) {
            prop_from_details_flg = true;
        }
        if (localName.equalsIgnoreCase("order_summary_PDF")) {
            order_summary_PDF_flg = true;
        }
        if (localName.equalsIgnoreCase("TA_DA_km_tracking_mode")) {
            TA_DA_km_tracking_mode_flg = true;
        }
        if (localName.equalsIgnoreCase("dsr_pdf")) {
            dsr_pdf_flg = true;
        }
        if (localName.equalsIgnoreCase("yellow_card_cust_type")) {
            yellow_card_cust_type_flg = true;
        }
        if (localName.equalsIgnoreCase("yellow_card_product")) {
            yellow_card_product_flg = true;
        }
        if (localName.equalsIgnoreCase("hoarding_emp_vendor")) {
            hoarding_emp_vendor_flg = true;
        }
        if (localName.equalsIgnoreCase("mf_mandatory_details")) {
            mf_mandatory_details_flg = true;
        }
        if (localName.equalsIgnoreCase("purpose_of_visit")) {
            purpose_of_visit_flg = true;
        }
        if (localName.equalsIgnoreCase("site_visit_approval")) {
            site_visit_approval_flg = true;
        }
        if (localName.equalsIgnoreCase("multi_travel_mode")) {
            multi_travel_mode_flg = true;
        }
        if (localName.equalsIgnoreCase("feedback_backup")) {
            feedback_backup_flg = true;
        }
        if (localName.equalsIgnoreCase("dashboard")) {
            dashboard_flg = true;
        }
        if (localName.equalsIgnoreCase("manchtech")) {
            manchtech_flg = true;
        }
        if (localName.equalsIgnoreCase("leaderboard")) {
            leaderboard_flg = true;
        }
        if (localName.equalsIgnoreCase("bd_leaderboard")) {
            bd_leaderboard_flg = true;
        }
        if (localName.equalsIgnoreCase("lead_generation_approval")) {
            lead_generation_approval_flg = true;
        }
        if (localName.equalsIgnoreCase("mle")) {
            mle_flg = true;
        }


    }

    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (menuId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (userId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (attndnc) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (routePlan) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (order) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (collection) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (stkAudit) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (businessPros) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (tourExp) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (captureImg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (notesInfo) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (activityReport) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (loyalty) {
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
        if (misReport) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (deleteTransaction) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (loadingFreight) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (saudaalloc) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isSurvey) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isPromotion) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isReplacement) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isMarketFeedback) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isSaudaBookedFromApp) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isPendingContract) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isSaudaMis) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isOrderStatus) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isCheckout) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isSaudaOutstanding) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isSalePerformance) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isCheckInOut) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isOutstanding) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isOutstandingAgeing) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isTargetAchevement) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isWholeSaleInfo) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isSelfAppraisalDetails) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isYellowCard) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isCatalogue) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isCatalogueUrl) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isTelephonicTransaction) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isTDAllocation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (catalogue_dependency) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (TD_allocation_vertical) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (run_time_TD_approval_vertical) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (quotation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (CRM_app) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (ISP) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (monthly_report_mail) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (retailer_app) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (scheme) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (reverse_auction) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (retailer_care) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (branchwise_scheme_PDF) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (collection_forecast) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (golden_rules) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (manager_activity) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (independent_check_in_out) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (check_in_out_menu_access) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (van_sales) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (bargain) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (bargain_TD) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (DO) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (branchwise_geo_fencing) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (DO_status) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (geo_fencing_menu) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (joint_work) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (generate_pricing) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (app_order_approval) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (cust_class) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (hierarchical_report) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (grn) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (order_edit) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (generate_pricing_MCX) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stock_audit_edit) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (CI_logic) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (scheme_pdf) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (gift_delivery) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (attendance_journey_info) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (checkout_journey_info) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (attendance_journey_info_options) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (dealer_visit) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (TM_approval) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (TM_approved_meeting) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (tour_exp_fuel_bill) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (app_order_approval_additional_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (weightage_calc_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (sis_report_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (checkout_time_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (odometer_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (doctor_visit_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (customer_product_stock_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (beat_wise_activity_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stockist_visit_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (prop_from_details_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (order_summary_PDF_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (TA_DA_km_tracking_mode_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (dsr_pdf_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (yellow_card_cust_type_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (yellow_card_product_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (hoarding_emp_vendor_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mf_mandatory_details_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (purpose_of_visit_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (site_visit_approval_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (multi_travel_mode_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (feedback_backup_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (dashboard_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (manchtech_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (leaderboard_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (bd_leaderboard_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (lead_generation_approval_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mle_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }


    }

    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        // Log.i("tag end",localName);
        super.endElement(uri, localName, qName);

        if (menuId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMenuId(trueData);
            menuId = false;
        }

        if (userId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUserid(trueData);
            userId = false;
        }

        if (attndnc) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAttendance(trueData);
            attndnc = false;
        }

        if (routePlan) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRoutePlan(trueData);
            routePlan = false;
        }

        if (order) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOrder(trueData);
            order = false;
        }

        if (collection) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCollection(trueData);
            collection = false;
        }

        if (stkAudit) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setStkAudit(trueData);
            stkAudit = false;
        }

        if (businessPros) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBusinessProspect(trueData);
            businessPros = false;
        }

        if (tourExp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTourExp(trueData);
            tourExp = false;
        }

        if (captureImg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCaptureImage(trueData);
            captureImg = false;
        }

        if (notesInfo) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setNotesInfo(trueData);
            notesInfo = false;
        }
        if (activityReport) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setActivityReport(trueData);
            activityReport = false;
        }
        if (loyalty) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLoyalty(trueData);
            loyalty = false;
        }
        if (timeStamp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLastUpdateTime(trueData);
            timeStamp = false;
        }
        if (misReport) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMisReport(trueData);
            misReport = false;
        }
        if (deleteTransaction) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDeleteTransaction(trueData);
            deleteTransaction = false;
        }
        if (loadingFreight) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLoadingFreight(trueData);
            loadingFreight = false;
        }
        if (saudaalloc) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaAllocation(trueData);
            saudaalloc = false;
        }

        if (isSurvey) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurvey(trueData);
            isSurvey = false;
        }

        if (isPromotion) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSampling(trueData);
            isPromotion = false;
        }

        if (isReplacement) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setReplacement(trueData);
            isReplacement = false;
        }
        if (isMarketFeedback) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMarketFeedback(trueData);
            isMarketFeedback = false;
        }
        if (isSaudaBookedFromApp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaAllocationfromApp(trueData);
            isSaudaBookedFromApp = false;
        }
        if (isPendingContract) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPendingContract(trueData);
            isPendingContract = false;
        }
        if (isSaudaMis) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaMis(trueData);
            isSaudaMis = false;
        }

        if (isOrderStatus) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOrderStatus(trueData);
            isOrderStatus = false;
        }
        if (isCheckout) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCheckOut(trueData);
            isCheckout = false;
        }
        if (isSaudaOutstanding) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaOutstanding(trueData);
            isSaudaOutstanding = false;
        }
        if (isSalePerformance) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSalePerFormance(trueData);
            isSalePerformance = false;
        }

        if (isCheckInOut) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCheckInOut(trueData);
            isCheckInOut = false;
        }

        if (isOutstanding) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOutstanding(trueData);
            isOutstanding = false;
        }

        if (isOutstandingAgeing) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOutstandingAgeing(trueData);
            isOutstandingAgeing = false;
        }

        if (isTargetAchevement) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTargetAcheivement(trueData);
            isTargetAchevement = false;
        }

        if (isWholeSaleInfo) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setWholeSaleInfo(trueData);
            isWholeSaleInfo = false;
        }
        if (isSelfAppraisalDetails) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSelfAppraisalDetails(trueData);
            isSelfAppraisalDetails = false;
        }
        if (isYellowCard) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setYellowCard(trueData);
            isYellowCard = false;
        }
        if (isCatalogue) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCatalogue(trueData);
            isCatalogue = false;
        }
        if (isCatalogueUrl) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCatalogueUrl(trueData);
            isCatalogueUrl = false;
        }
        if (isTelephonicTransaction) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTelephonicTransaction(trueData);
            isTelephonicTransaction = false;
        }
        if (isTDAllocation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTDAllocation(trueData);
            isTDAllocation = false;
        }
        if (catalogue_dependency) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcatalogue_dependency(trueData);
            catalogue_dependency = false;
        }
        if (TD_allocation_vertical) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTD_allocation_vertical(trueData);
            TD_allocation_vertical = false;
        }
        if (run_time_TD_approval_vertical) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setrun_time_TD_approval_vertical(trueData);
            run_time_TD_approval_vertical = false;
        }
        if (quotation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setquotation(trueData);
            quotation = false;
        }
        if (CRM_app) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCRM_app(trueData);
            CRM_app = false;
        }
        if (ISP) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setISP(trueData);
            ISP = false;
        }
        if (monthly_report_mail) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setmonthly_report_mail(trueData);
            monthly_report_mail = false;
        }
        if (retailer_app) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setretailer_app(trueData);
            retailer_app = false;
        }
        if (scheme) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setscheme(trueData);
            scheme = false;
        }
        if (reverse_auction) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setreverseAuction(trueData);
            reverse_auction = false;
        }
        if (retailer_care) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setretailer_care(trueData);
            retailer_care = false;
        }
        if (branchwise_scheme_PDF) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setbranchwise_scheme_PDF(trueData);
            branchwise_scheme_PDF = false;
        }
        if (collection_forecast) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcollection_forecast(trueData);
            collection_forecast = false;
        }
        if (golden_rules) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setgolden_rules(trueData);
            golden_rules = false;
        }
        if (manager_activity) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setmanager_activity(trueData);
            manager_activity = false;
        }
        if (independent_check_in_out) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setindependent_check_in_out(trueData);
            independent_check_in_out = false;
        }
        if (check_in_out_menu_access) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcheck_in_out_menu_access(trueData);
            check_in_out_menu_access = false;
        }
        if (van_sales) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setvan_sales(trueData);
            van_sales = false;
        }
        if (bargain) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setbargain(trueData);
            bargain = false;
        }
        if (bargain_TD) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setbargain_TD(trueData);
            bargain_TD = false;
        }
        if (DO) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDO(trueData);
            DO = false;
        }
        if (branchwise_geo_fencing) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setbranchwise_geo_fencing(trueData);
            branchwise_geo_fencing = false;
        }
        if (DO_status) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDO_status(trueData);
            DO_status = false;
        }
        if (geo_fencing_menu) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setgeo_fencing_menu(trueData);
            geo_fencing_menu = false;
        }
        if (joint_work) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setjoint_work(trueData);
            joint_work = false;
        }
        if (generate_pricing) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setgenerate_pricing(trueData);
            generate_pricing = false;
        }
        if (app_order_approval) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setapp_order_approval(trueData);
            app_order_approval = false;
        }
        if (cust_class) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcust_class(trueData);
            cust_class = false;
        }
        if (hierarchical_report) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.sethierarchical_report(trueData);
            hierarchical_report = false;
        }
        if (grn) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setgrn(trueData);
            grn = false;
        }
        if (order_edit) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setorder_edit(trueData);
            order_edit = false;
        }
        if (generate_pricing_MCX) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setgenerate_pricing_MCX(trueData);
            generate_pricing_MCX = false;
        }
        if (stock_audit_edit) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setstock_audit_edit(trueData);
            stock_audit_edit = false;
        }
        if (CI_logic) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCI_logic(trueData);
            CI_logic = false;
        }
        if (scheme_pdf) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setscheme_pdf(trueData);
            scheme_pdf = false;
        }
        if (gift_delivery) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setgift_delivery(trueData);
            gift_delivery = false;
        }
        if (attendance_journey_info) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setattendance_journey_info(trueData);
            attendance_journey_info = false;
        }
        if (checkout_journey_info) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcheckout_journey_info(trueData);
            checkout_journey_info = false;
        }
        if (attendance_journey_info_options) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setattendance_journey_info_options(trueData);
            attendance_journey_info_options = false;
        }
        if (dealer_visit) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setdealer_visit(trueData);
            dealer_visit = false;
        }
        if (TM_approval) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTM_approval(trueData);
            TM_approval = false;
        }
        if (TM_approved_meeting) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTM_approved_meeting(trueData);
            TM_approved_meeting = false;
        }
        if (tour_exp_fuel_bill) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setfuel_bill_attachment(trueData);
            tour_exp_fuel_bill = false;
        }
        if (app_order_approval_additional_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setApp_order_approval_additional(trueData);
            app_order_approval_additional_flg = false;
        }
        if (weightage_calc_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setWeightage_calc(trueData);
            weightage_calc_flg = false;
        }

        if (sis_report_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSis_report(trueData);
            sis_report_flg = false;
        }

        if (checkout_time_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCheckout_time(trueData);
            checkout_time_flg = false;
        }

        if (odometer_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOdometer(trueData);
            odometer_flg = false;
        }

        if (doctor_visit_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDoctor_visit(trueData);
            doctor_visit_flg = false;
        }

        if (customer_product_stock_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCustomer_product_stock(trueData);
            customer_product_stock_flg = false;
        }
        if (beat_wise_activity_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBeat_wise_activity(trueData);
            beat_wise_activity_flg = false;
        }
        if (stockist_visit_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setStockist_visit(trueData);
            stockist_visit_flg = false;
        }
        if (prop_from_details_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProp_form_details(trueData);
            prop_from_details_flg = false;
        }
        if (order_summary_PDF_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOrder_summary_PDF(trueData);
            order_summary_PDF_flg = false;
        }
        if (TA_DA_km_tracking_mode_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTA_DA_km_tracking_mode(trueData);
            TA_DA_km_tracking_mode_flg = false;
        }
        if (dsr_pdf_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDsr_pdf(trueData);
            dsr_pdf_flg = false;
        }
        if (yellow_card_cust_type_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setYellow_card_cust_type(trueData);
            yellow_card_cust_type_flg = false;
        }
        if (yellow_card_product_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setYellow_card_product(trueData);
            yellow_card_product_flg = false;
        }
        if (hoarding_emp_vendor_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setHoarding_emp_vendor(trueData);
            hoarding_emp_vendor_flg = false;
        }

        if (mf_mandatory_details_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMf_mandatory_details(trueData);
            mf_mandatory_details_flg = false;
        }
        if (purpose_of_visit_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPurpose_of_visit(trueData);
            purpose_of_visit_flg = false;
        }
        if (site_visit_approval_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSite_visit_approval(trueData);
            site_visit_approval_flg = false;
        }
        if (multi_travel_mode_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMulti_travel_mode(trueData);
            multi_travel_mode_flg = false;
        }
        if (feedback_backup_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setFeedback_backup(trueData);
            feedback_backup_flg = false;
        }
        if (dashboard_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDashboard(trueData);
            dashboard_flg = false;
        }
        if (manchtech_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setManchtech(trueData);
            manchtech_flg = false;
        }
        if (leaderboard_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLeaderboard(trueData);
            leaderboard_flg = false;
        }

        if (bd_leaderboard_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBd_leaderboard(trueData);
            bd_leaderboard_flg = false;
        }
        if (lead_generation_approval_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLead_generation_approval(trueData);
            lead_generation_approval_flg = false;
        }
        if (mle_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMle(trueData);
            mle_flg = false;
        }

        if (localName.equalsIgnoreCase("data")) {
            list.add(details);
        }
    }

    public String getTrueData(String data) {
        // String[] dataArray = data.split("[");
        // String[] dataArray1 = dataArray[2].split("]");
        // return dataArray1[0];
        return data;
    }

}