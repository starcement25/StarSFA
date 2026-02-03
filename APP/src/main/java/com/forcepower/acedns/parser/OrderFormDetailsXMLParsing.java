package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.OrderFormDetails;
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

public class OrderFormDetailsXMLParsing extends DefaultHandler {

    ArrayList<OrderFormDetails> list = new ArrayList<>();
    StringBuilder sb;
    boolean orderFormId;
    boolean userId;
    boolean crdtLimit;
    boolean clStock;
    boolean mrpDropdwn;
    boolean mrp;
    boolean trdDiscnt;
    boolean addCust;
    boolean tagCust;
    boolean tdType;
    boolean saleRate;
    boolean saleRateDrpDwn;
    boolean printer;
    boolean printer_mandatory;
    boolean payment_type;
    boolean timeStamp;
    boolean tagDistributor;
    boolean sale;
    boolean instruction;
    boolean vat;
    boolean vatDetails;
    boolean branchRDSTransfer;
    boolean amount;
    boolean vatType;
    boolean tdCalc;
    boolean tdTransType;
    boolean vatCalcOn;
    boolean saudaDepot;
    boolean saudaCarry;
    boolean saudaRate;
    boolean saudaValue;
    boolean mBoolTDValidation;
    boolean mBoolTDCalcBasedOn;
    boolean mBoolPremium;
    boolean mBoolPreviousOrder;
    boolean mBoolNewCustomerOTP;
    boolean mBoolCustomerInformationCheck;
    boolean mBoolAddCustomerRouteCreation;
    boolean mBoolOrderType;
    boolean mBoolFreightComponent;
    boolean mBoolTaxType;
    boolean mBoolDestination;
    boolean mBoolInputScreenNormal;
    boolean mBoolInputScreenSpecial;
    boolean mBoolAddCustDetails;
    boolean mBoolAddCustTradeNonTrade;
    boolean mBoolPrintMedium;
    boolean mBoolPrintMenu;
    boolean mBoolHintsRemarks;
    boolean mBoolHintsRemarksVal;
    boolean mBoolAddCustomImage;
    boolean mBoolDistributorRouteEmployeeRelation;
    boolean mMultipleUom;
    boolean mInputScreenPlanwise;
    boolean mTdInputDropDown;
    boolean mInputScreenPlanwiseFilter1Wise;
    boolean mInputScreenPriceValidation;
    boolean mSaudaSaleRateDrpdwn;
    boolean cust_type_wise_UOM;
    boolean cust_type_wise_UOM_val;
    boolean visit_sequence;
    boolean add_customer_activation;
    boolean multiple_distributor;
    boolean GEO_fencing_variance;
    boolean route_wise_distributor;
    boolean mrp_sale_rate_text;
    boolean order_status_remarks_flg;
    boolean tagged_distributor_business_prospect_flg;
    boolean no_order_remarks_flg;
    boolean product_wise_remarks_flg;
    private OrderFormDetails details;


    public OrderFormDetailsXMLParsing(String datafromserver) {
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
            xr.parse(new InputSource(new ByteArrayInputStream(datafromserver
                    .getBytes("ISO-8859-1"))));
        } catch (IOException e) {
            e.printStackTrace();
        } catch (SAXException e) {
            e.printStackTrace();
        }
    }

    public OrderFormDetails getParsedData() {
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
            details = new OrderFormDetails();
        }
        if (localName.equalsIgnoreCase("order_form_id")) {
            orderFormId = true;
        }
        if (localName.equalsIgnoreCase("user_id")) {
            userId = true;
        }
        if (localName.equalsIgnoreCase("credit_limit")) {
            crdtLimit = true;
        }
        if (localName.equalsIgnoreCase("cl_stk")) {
            clStock = true;
        }
        if (localName.equalsIgnoreCase("mrp_input_dropdown")) {
            mrpDropdwn = true;
        }
        if (localName.equalsIgnoreCase("mrp")) {
            mrp = true;
        }
        if (localName.equalsIgnoreCase("TD")) {
            trdDiscnt = true;
        }
        if (localName.equalsIgnoreCase("add_customer")) {
            addCust = true;
        }
        if (localName.equalsIgnoreCase("tagged_customer_for_business_prospect")) {
            tagCust = true;
        }
        if (localName.equalsIgnoreCase("TD_type")) {
            tdType = true;
        }
        if (localName.equalsIgnoreCase("sale_rate")) {
            saleRate = true;
        }
        if (localName.equalsIgnoreCase("sale_rate_input_dropdown")) {
            saleRateDrpDwn = true;
        }
        if (localName.equalsIgnoreCase("attached_printer")) {
            printer = true;
        }
        if (localName.equalsIgnoreCase("printer_mandatory")) {
            printer_mandatory = true;
        }
        if (localName.equalsIgnoreCase("payment_type")) {
            payment_type = true;
        }
        if (localName.equalsIgnoreCase("last_update_time")) {
            timeStamp = true;
        }
        if (localName.equalsIgnoreCase("tag_distributor")) {
            tagDistributor = true;
        }
        if (localName.equalsIgnoreCase("sale")) {
            sale = true;
        }
        if (localName.equalsIgnoreCase("instruction")) {
            instruction = true;
        }
        if (localName.equalsIgnoreCase("VAT")) {
            vat = true;
        }
        if (localName.equalsIgnoreCase("VAT_details")) {
            vatDetails = true;
        }
        if (localName.equalsIgnoreCase("branch_rds_transfer")) {
            branchRDSTransfer = true;
        }

        if (localName.equalsIgnoreCase("amount")) {
            amount = true;
        }
        if (localName.equalsIgnoreCase("VAT_type")) {
            vatType = true;
        }
        if (localName.equalsIgnoreCase("TD_calc")) {
            tdCalc = true;
        }
        if (localName.equalsIgnoreCase("TD_trans_type")) {
            tdTransType = true;
        }
        if (localName.equalsIgnoreCase("VAT_calc_on")) {
            vatCalcOn = true;
        }

        if (localName.equalsIgnoreCase("sauda_depot_wise")) {
            saudaDepot = true;
        }
        if (localName.equalsIgnoreCase("sauda_allocation_carry_forward")) {
            saudaCarry = true;
        }
        if (localName.equalsIgnoreCase("sauda_rate_variable")) {
            saudaRate = true;
        }
        if (localName.equalsIgnoreCase("sauda_rate_variable_value")) {
            saudaValue = true;
        }

        if (localName.equalsIgnoreCase("TD_validation")) {
            mBoolTDValidation = true;
        }
        if (localName.equalsIgnoreCase("TD_calc_basedon")) {
            mBoolTDCalcBasedOn = true;
        }
        if (localName.equalsIgnoreCase("premium")) {
            mBoolPremium = true;
        }
        if (localName.equalsIgnoreCase("previous_order")) {
            mBoolPreviousOrder = true;
        }

        if (localName.equalsIgnoreCase("add_customer_OTP")) {
            mBoolNewCustomerOTP = true;
        }

        if (localName.equalsIgnoreCase("customer_information_check")) {
            mBoolCustomerInformationCheck = true;
        }

        if (localName.equalsIgnoreCase("add_customer_route_creation")) {
            mBoolAddCustomerRouteCreation = true;
        }

        if (localName.equalsIgnoreCase("order_type")) {
            mBoolOrderType = true;
        }

        if (localName.equalsIgnoreCase("freight_component")) {
            mBoolFreightComponent = true;
        }

        if (localName.equalsIgnoreCase("tax_type")) {
            mBoolTaxType = true;
        }
        if (localName.equalsIgnoreCase("destination")) {
            mBoolDestination = true;
        }
        if (localName.equalsIgnoreCase("input_screen_normal")) {
            mBoolInputScreenNormal = true;
        }
        if (localName.equalsIgnoreCase("input_screen_special")) {
            mBoolInputScreenSpecial = true;
        }
        if (localName.equalsIgnoreCase("add_customer_details")) {
            mBoolAddCustDetails = true;
        }
        if (localName.equalsIgnoreCase("add_customer_trade_nontrade")) {
            mBoolAddCustTradeNonTrade = true;
        }
        if (localName.equalsIgnoreCase("printer_type")) {
            mBoolPrintMedium = true;
        }
        if (localName.equalsIgnoreCase("printer_menu")) {
            mBoolPrintMenu = true;
        }
        if (localName.equalsIgnoreCase("hint_remarks")) {
            mBoolHintsRemarks = true;
        }
        if (localName.equalsIgnoreCase("hint_remarks_val")) {
            mBoolHintsRemarksVal = true;
        }
        if (localName.equalsIgnoreCase("add_customer_image_creation")) {
            mBoolAddCustomImage = true;
        }
        if (localName.equalsIgnoreCase("distributor_route_emp_relation")) {
            mBoolDistributorRouteEmployeeRelation = true;
        }
        if (localName.equalsIgnoreCase("multiple_UOM")) {
            mMultipleUom = true;
        }
        if (localName.equalsIgnoreCase("input_screen_planwise")) {
            mInputScreenPlanwise = true;
        }
        if (localName.equalsIgnoreCase("TD_type_input_dropdown")) {
            mTdInputDropDown = true;
        }
        if (localName.equalsIgnoreCase("input_screen_planwise_filter1wise")) {
            mInputScreenPlanwiseFilter1Wise = true;
        }
        if (localName.equalsIgnoreCase("input_screen_price_validation")) {
            mInputScreenPriceValidation = true;
        }
        if (localName.equalsIgnoreCase("sauda_sale_rate_input_dropdown")) {
            mSaudaSaleRateDrpdwn = true;
        }
        if (localName.equalsIgnoreCase("cust_type_wise_UOM")) {
            cust_type_wise_UOM = true;
        }
        if (localName.equalsIgnoreCase("cust_type_wise_UOM_val")) {
            cust_type_wise_UOM_val = true;
        }

        if (localName.equalsIgnoreCase("visit_sequence")) {
            visit_sequence = true;
        }
        if (localName.equalsIgnoreCase("add_customer_activation")) {
            add_customer_activation = true;
        }
        if (localName.equalsIgnoreCase("multiple_distributor")) {
            multiple_distributor = true;
        }
        if (localName.equalsIgnoreCase("GEO_fencing_variance")) {
            GEO_fencing_variance = true;
        }
        if (localName.equalsIgnoreCase("route_wise_distributor")) {
            route_wise_distributor = true;
        }
        if (localName.equalsIgnoreCase("mrp_sale_rate_text")) {
            mrp_sale_rate_text = true;
        }
        if (localName.equalsIgnoreCase("order_status_remarks")) {
            order_status_remarks_flg = true;
        }
        if (localName.equalsIgnoreCase("tagged_distributor_business_prospect")) {
            tagged_distributor_business_prospect_flg = true;
        }
        if (localName.equalsIgnoreCase("no_order_remarks")) {
            no_order_remarks_flg = true;
        }
        if (localName.equalsIgnoreCase("product_wise_remarks")) {
            product_wise_remarks_flg = true;
        }

    }

    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (orderFormId) {
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

        if (crdtLimit) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (clStock) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mrpDropdwn) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mrp) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (trdDiscnt) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (addCust) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (tagCust) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (tdType) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (saleRate) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (saleRateDrpDwn) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (printer) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (printer_mandatory) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (payment_type) {
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

        if (tagDistributor) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (sale) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (instruction) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (vat) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (vatDetails) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (branchRDSTransfer) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (amount) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (vatType) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (tdCalc) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (tdTransType) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (vatCalcOn) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (saudaDepot) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (saudaCarry) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (saudaRate) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (saudaValue) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolTDValidation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolTDCalcBasedOn) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolPremium) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolPreviousOrder) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolNewCustomerOTP) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolCustomerInformationCheck) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolAddCustomerRouteCreation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolOrderType) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolFreightComponent) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolTaxType) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolDestination) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolInputScreenNormal) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolInputScreenSpecial) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolAddCustDetails) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolAddCustTradeNonTrade) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolPrintMedium) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolPrintMenu) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolHintsRemarks) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolHintsRemarksVal) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolAddCustomImage) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolDistributorRouteEmployeeRelation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mMultipleUom) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mInputScreenPlanwise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mTdInputDropDown) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mInputScreenPlanwiseFilter1Wise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mInputScreenPriceValidation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mSaudaSaleRateDrpdwn) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (cust_type_wise_UOM) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (cust_type_wise_UOM_val) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (visit_sequence) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (add_customer_activation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (multiple_distributor) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (GEO_fencing_variance) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (route_wise_distributor) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mrp_sale_rate_text) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (order_status_remarks_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (tagged_distributor_business_prospect_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (no_order_remarks_flg) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (product_wise_remarks_flg) {
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

        if (orderFormId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOrderFormId(trueData);
            orderFormId = false;
        }

        if (userId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUserId(trueData);
            userId = false;
        }

        if (crdtLimit) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCreditLimit(trueData);
            crdtLimit = false;
        }

        if (clStock) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setClosingStk(trueData);
            clStock = false;
        }

        if (mrpDropdwn) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMrpDrpdwn(trueData);
            mrpDropdwn = false;
        }

        if (mrp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMrp(trueData);
            mrp = false;
        }

        if (trdDiscnt) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTradeDiscount(trueData);
            trdDiscnt = false;
        }

        if (addCust) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAddCustomer(trueData);
            addCust = false;
        }

        if (tagCust) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCustomerBsnsProspct(trueData);
            tagCust = false;
        }
        if (tdType) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTdType(trueData);
            tdType = false;
        }

        if (saleRate) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaleRate(trueData);
            saleRate = false;
        }

        if (saleRateDrpDwn) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaleRateDrpdwn(trueData);
            saleRateDrpDwn = false;
        }

        if (printer) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAttachedPrinter(trueData);
            printer = false;
        }

        if (printer_mandatory) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPrinter_mandetory(trueData);
            printer_mandatory = false;
        }

        if (payment_type) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPayment_type(trueData);
            payment_type = false;
        }

        if (timeStamp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLastUpdateTime(trueData);
            timeStamp = false;
        }

        if (tagDistributor) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTagDistributor(trueData);
            tagDistributor = false;
        }

        if (sale) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSale(trueData);
            sale = false;
        }

        if (instruction) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setInstruction(trueData);
            instruction = false;
        }

        if (vat) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setVat(trueData);
            vat = false;
        }

        if (vatDetails) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setVatDetails(trueData);
            vatDetails = false;
        }

        if (branchRDSTransfer) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBranchRDSTransfer(trueData);
            branchRDSTransfer = false;
        }

        if (amount) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAmount(trueData);
            amount = false;
        }
        if (vatType) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setVatType(trueData);
            vatType = false;
        }
        if (tdCalc) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTdCalc(trueData);
            tdCalc = false;
        }
        if (tdTransType) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTdTransType(trueData);
            tdTransType = false;
        }

        if (vatCalcOn) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setVatCalcOn(trueData);
            vatCalcOn = false;
        }

        if (saudaDepot) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaDepo(trueData);
            saudaDepot = false;
        }
        if (saudaCarry) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaCarry(trueData);
            saudaCarry = false;
        }
        if (saudaRate) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaRate(trueData);
            saudaRate = false;
        }
        if (saudaValue) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaValue(trueData);
            saudaValue = false;
        }

        if (mBoolTDValidation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTDValidation(trueData);
            mBoolTDValidation = false;
        }
        if (mBoolTDCalcBasedOn) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTDCalcBasedOn(trueData);
            mBoolTDCalcBasedOn = false;
        }

        if (mBoolPremium) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPremium(trueData);
            mBoolPremium = false;
        }

        if (mBoolPreviousOrder) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPreviousOrder(trueData);
            mBoolPreviousOrder = false;
        }

        if (mBoolNewCustomerOTP) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setNewCustomerOtp(trueData);
            mBoolNewCustomerOTP = false;
        }

        if (mBoolCustomerInformationCheck) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCustomerInfoCheck(trueData);
            mBoolCustomerInformationCheck = false;
        }

        if (mBoolAddCustomerRouteCreation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAddCustomerRouteCreation(trueData);
            mBoolAddCustomerRouteCreation = false;
        }

        if (mBoolOrderType) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOrderType(trueData);
            mBoolOrderType = false;
        }

        if (mBoolFreightComponent) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setFreightComponent(trueData);
            mBoolFreightComponent = false;
        }

        if (mBoolTaxType) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTaxType(trueData);
            mBoolTaxType = false;
        }

        if (mBoolDestination) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDestination(trueData);
            mBoolDestination = false;
        }

        if (mBoolInputScreenNormal) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setInputScreenNormal(trueData);
            mBoolInputScreenNormal = false;
        }

        if (mBoolInputScreenSpecial) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setInputScreenSpecial(trueData);
            mBoolInputScreenSpecial = false;
        }

        if (mBoolAddCustDetails) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAddCustomerDetails(trueData);
            mBoolAddCustDetails = false;
        }

        if (mBoolAddCustTradeNonTrade) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAddCustomerTradeNonTrade(trueData);
            mBoolAddCustTradeNonTrade = false;
        }

        if (mBoolPrintMedium) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPrintMedium(trueData);
            mBoolPrintMedium = false;
        }
        if (mBoolPrintMenu) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPrinterMenu(trueData);
            mBoolPrintMenu = false;
        }
        if (mBoolHintsRemarks) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setHintsRemarks(trueData);
            mBoolHintsRemarks = false;
        }
        if (mBoolHintsRemarksVal) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setHintsRemarksVal(trueData);
            mBoolHintsRemarksVal = false;
        }
        if (mBoolAddCustomImage) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setAddCustomImage(trueData);
            mBoolAddCustomImage = false;
        }
        if (mBoolDistributorRouteEmployeeRelation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDistributorRouteEmployeeRelation(trueData);
            mBoolDistributorRouteEmployeeRelation = false;
        }
        if (mMultipleUom) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMultipleUom(trueData);
            mMultipleUom = false;
        }
        if (mInputScreenPlanwise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setInputScreenPlanwise(trueData);
            mInputScreenPlanwise = false;
        }
        if (mTdInputDropDown) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTdInputDropDown(trueData);
            mTdInputDropDown = false;
        }
        if (mInputScreenPlanwiseFilter1Wise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setInputScreenPlanwiseFilter1Wise(trueData);
            mInputScreenPlanwiseFilter1Wise = false;
        }
        if (mInputScreenPriceValidation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setInputScreenPriceValidation(trueData);
            mInputScreenPriceValidation = false;
        }
        if (mSaudaSaleRateDrpdwn) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaSaleRateDrpdwn(trueData);
            mSaudaSaleRateDrpdwn = false;
        }
        if (cust_type_wise_UOM) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcust_type_wise_UOM(trueData);
            cust_type_wise_UOM = false;
        }
        if (cust_type_wise_UOM_val) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setcust_type_wise_UOM_val(trueData);
            cust_type_wise_UOM_val = false;
        }
        if (visit_sequence) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setvisit_sequence(trueData);
            visit_sequence = false;
        }
        if (add_customer_activation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setadd_customer_activation(trueData);
            add_customer_activation = false;
        }
        if (multiple_distributor) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setmultiple_distributor(trueData);
            multiple_distributor = false;
        }
        if (GEO_fencing_variance) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setGEO_fencing_variance(trueData);
            GEO_fencing_variance = false;
        }
        if (route_wise_distributor) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setroute_wise_distributor(trueData);
            route_wise_distributor = false;
        }
        if (mrp_sale_rate_text) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setmrp_sale_rate_text(trueData);
            mrp_sale_rate_text = false;
        }
        if (order_status_remarks_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOrder_status_remarks(trueData);
            order_status_remarks_flg = false;
        }
        if (tagged_distributor_business_prospect_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTagged_distributor_business_prospect(trueData);
            tagged_distributor_business_prospect_flg = false;
        }
        if (no_order_remarks_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setNo_order_remarks(trueData);
            no_order_remarks_flg = false;
        }
        if (product_wise_remarks_flg) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProduct_wise_remarks(trueData);
            product_wise_remarks_flg = false;
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