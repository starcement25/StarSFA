package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.SaudaFormDetails;
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

public class SaudaFormDetailsXMLParser extends DefaultHandler {

    ArrayList<SaudaFormDetails> list = new ArrayList<SaudaFormDetails>();
    StringBuilder sb;
    Boolean mBoolSaudaFormId = false;
    Boolean mBoolUserId = false;
    Boolean mBoolSaudaAllocationCarryForward = false;
    Boolean mBoolSaudaDepotWise = false;
    Boolean mBoolSaudaRateVariable = false;
    Boolean mBoolSaudaRateVariableValue = false;
    Boolean mBoolSaudaBookedThrough = false;
    Boolean mBoolSaudaValidFrom = false;
    Boolean sauda_rate_dependent_on_despatch_point = false;
    Boolean sauda_rate_dependent_on_despatch_point_val = false;
    Boolean sauda_rate_dependent_on_despatch_point_verticlewise = false;
    Boolean sauda_rate_dependent_on_despatch_point_verticle_val = false;
    Boolean secondary_freight_vertical = false;
    Boolean special_discount_vertical = false;
    Boolean customer_email_check_vertical = false;
    Boolean incoterms_vertical = false;
    Boolean sauda_allocation_app_vertical = false;
    Boolean incoterms_price_components = false;
    private SaudaFormDetails details;

    public SaudaFormDetailsXMLParser(String datafromserver) {
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

    public SaudaFormDetails getParsedData() {
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
            details = new SaudaFormDetails();
        }

        if (localName.equalsIgnoreCase("sauda_form_id")) {
            mBoolSaudaFormId = true;
        }
        if (localName.equalsIgnoreCase("user_id")) {
            mBoolUserId = true;
        }
        if (localName.equalsIgnoreCase("sauda_allocation_carry_forward")) {
            mBoolSaudaAllocationCarryForward = true;
        }
        if (localName.equalsIgnoreCase("sauda_depot_wise")) {
            mBoolSaudaDepotWise = true;
        }
        if (localName.equalsIgnoreCase("sauda_rate_variable")) {
            mBoolSaudaRateVariable = true;
        }
        if (localName.equalsIgnoreCase("sauda_rate_variable_value")) {
            mBoolSaudaRateVariableValue = true;
        }
        if (localName.equalsIgnoreCase("sauda_booked_through")) {
            mBoolSaudaBookedThrough = true;
        }
        if (localName.equalsIgnoreCase("sauda_valid_from")) {
            mBoolSaudaValidFrom = true;
        }
        if (localName.equalsIgnoreCase("sauda_rate_dependent_on_despatch_point")) {
            sauda_rate_dependent_on_despatch_point = true;
        }
        if (localName.equalsIgnoreCase("sauda_rate_dependent_on_despatch_point_val")) {
            sauda_rate_dependent_on_despatch_point_val = true;
        }
        if (localName.equalsIgnoreCase("sauda_rate_dependent_on_despatch_point_verticlewise")) {
            sauda_rate_dependent_on_despatch_point_verticlewise = true;
        }
        if (localName.equalsIgnoreCase("sauda_rate_dependent_on_despatch_point_verticle_val")) {
            sauda_rate_dependent_on_despatch_point_verticle_val = true;
        }
        if (localName.equalsIgnoreCase("secondary_freight_vertical")) {
            secondary_freight_vertical = true;
        }
        if (localName.equalsIgnoreCase("special_discount_vertical")) {
            special_discount_vertical = true;
        }
        if (localName.equalsIgnoreCase("customer_email_check_vertical")) {
            customer_email_check_vertical = true;
        }
        if (localName.equalsIgnoreCase("incoterms_vertical")) {
            incoterms_vertical = true;
        }
        if (localName.equalsIgnoreCase("sauda_allocation_app_vertical")) {
            sauda_allocation_app_vertical = true;
        }
        if (localName.equalsIgnoreCase("incoterms_price_components")) {
            incoterms_price_components = true;
        }

    }

    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (mBoolSaudaFormId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolUserId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSaudaAllocationCarryForward) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSaudaDepotWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSaudaRateVariable) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSaudaRateVariableValue) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSaudaBookedThrough) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSaudaValidFrom) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (sauda_rate_dependent_on_despatch_point) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (sauda_rate_dependent_on_despatch_point_val) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (sauda_rate_dependent_on_despatch_point_verticlewise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (sauda_rate_dependent_on_despatch_point_verticle_val) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (secondary_freight_vertical) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (special_discount_vertical) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (customer_email_check_vertical) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (incoterms_vertical) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (sauda_allocation_app_vertical) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (incoterms_price_components) {
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

        if (mBoolSaudaFormId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaFormId(trueData);
            mBoolSaudaFormId = false;
        }

        if (mBoolUserId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUserId(trueData);
            mBoolUserId = false;
        }

        if (mBoolSaudaAllocationCarryForward) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaAllocationCarryForward(trueData);
            mBoolSaudaAllocationCarryForward = false;
        }

        if (mBoolSaudaDepotWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaDepotWise(trueData);
            mBoolSaudaDepotWise = false;
        }

        if (mBoolSaudaRateVariable) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaRateVariable(trueData);
            mBoolSaudaRateVariable = false;
        }

        if (mBoolSaudaRateVariableValue) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaRateVariableValue(trueData);
            mBoolSaudaRateVariableValue = false;
        }

        if (mBoolSaudaBookedThrough) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaBookedThrough(trueData);
            mBoolSaudaBookedThrough = false;
        }

        if (mBoolSaudaValidFrom) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaValidFrom(trueData);
            mBoolSaudaValidFrom = false;
        }
        if (sauda_rate_dependent_on_despatch_point) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setsauda_rate_dependent_on_despatch_point(trueData);
            sauda_rate_dependent_on_despatch_point = false;
        }
        if (sauda_rate_dependent_on_despatch_point_val) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setsauda_rate_dependent_on_despatch_point_val(trueData);
            sauda_rate_dependent_on_despatch_point_val = false;
        }
        if (sauda_rate_dependent_on_despatch_point_verticlewise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setsauda_rate_dependent_on_despatch_point_verticlewise(trueData);
            sauda_rate_dependent_on_despatch_point_verticlewise = false;
        }
        if (sauda_rate_dependent_on_despatch_point_verticle_val) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setsauda_rate_dependent_on_despatch_point_verticle_val(trueData);
            sauda_rate_dependent_on_despatch_point_verticle_val = false;
        }
        if (secondary_freight_vertical) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSecondaryFreightVertical(trueData);
            secondary_freight_vertical = false;
        }
        if (special_discount_vertical) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSpecialDiscountVertical(trueData);
            special_discount_vertical = false;
        }
        if (customer_email_check_vertical) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCustomerEmailCheckVertical(trueData);
            customer_email_check_vertical = false;
        }
        if (incoterms_vertical) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setincoterms_vertical(trueData);
            incoterms_vertical = false;
        }
        if (sauda_allocation_app_vertical) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setsauda_allocation_app_vertical(trueData);
            sauda_allocation_app_vertical = false;
        }
        if (incoterms_price_components) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setincoterms_price_components(trueData);
            incoterms_price_components = false;
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
