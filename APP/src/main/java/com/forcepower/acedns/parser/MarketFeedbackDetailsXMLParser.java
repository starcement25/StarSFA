package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.MarketFeedbackDetails;
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

public class MarketFeedbackDetailsXMLParser extends DefaultHandler {

    ArrayList<MarketFeedbackDetails> list = new ArrayList<MarketFeedbackDetails>();
    StringBuilder sb;
    Boolean isMarketFeedbackId = false;
    Boolean isUserId = false;
    Boolean isMfGroupEnable = false;
    Boolean isMfCol1 = false;
    Boolean isMfCol2 = false;
    Boolean isMfCol3 = false;
    Boolean isMfCol4 = false;
    Boolean isMfSubMenuDetails = false;
    Boolean isMfSubMenuImage = false;
    Boolean isMfTagging = false;
    Boolean isMf_customer_branchwise = false;
    Boolean isMf_sub_menu_qty_unit = false;
    Boolean isEx_for = false;
    private MarketFeedbackDetails details;


    public MarketFeedbackDetailsXMLParser(String datafromserver) {
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

    public MarketFeedbackDetails getParsedData() {
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

        sb = new StringBuilder();

        super.startElement(uri, localName, qName, attributes);

        if (localName.equalsIgnoreCase("data")) {
            details = new MarketFeedbackDetails();
        }

        if (localName.equalsIgnoreCase("market_feedback_id")) {
            isMarketFeedbackId = true;
        }
        if (localName.equalsIgnoreCase("user_id")) {
            isUserId = true;
        }
        if (localName.equalsIgnoreCase("mf_group_enable")) {
            isMfGroupEnable = true;
        }
        if (localName.equalsIgnoreCase("mf_col1")) {
            isMfCol1 = true;
        }

        if (localName.equalsIgnoreCase("mf_col2")) {
            isMfCol2 = true;
        }

        if (localName.equalsIgnoreCase("mf_col3")) {
            isMfCol3 = true;
        }

        if (localName.equalsIgnoreCase("mf_col4")) {
            isMfCol4 = true;
        }

        if (localName.equalsIgnoreCase("mf_sub_menu_details")) {
            isMfSubMenuDetails = true;
        }

        if (localName.equalsIgnoreCase("mf_sub_menu_image")) {
            isMfSubMenuImage = true;
        }
        if (localName.equalsIgnoreCase("mf_tagging")) {
            isMfTagging = true;
        }
        if (localName.equalsIgnoreCase("mf_customer_branchwise")) {
            isMf_customer_branchwise = true;
        }
        if (localName.equalsIgnoreCase("mf_sub_menu_qty_unit")) {
            isMf_sub_menu_qty_unit = true;
        }
        if (localName.equalsIgnoreCase("ex_for")) {
            isEx_for = true;
        }



    }

    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (isMarketFeedbackId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isUserId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isMfGroupEnable) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isMfCol1) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isMfCol2) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isMfCol3) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isMfCol4) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isMfSubMenuDetails) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isMfSubMenuImage) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isMfTagging) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isMf_customer_branchwise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isMf_sub_menu_qty_unit) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (isEx_for) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

    }

    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        super.endElement(uri, localName, qName);

        if (isMarketFeedbackId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMarketFeedbackId(trueData);
            isMarketFeedbackId = false;
        }

        if (isUserId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUserId(trueData);
            isUserId = false;
        }

        if (isMfGroupEnable) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMfGroupEnable(trueData);
            isMfGroupEnable = false;
        }

        if (isMfCol1) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMfCol1(trueData);
            isMfCol1 = false;
        }

        if (isMfCol2) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMfCol2(trueData);
            isMfCol2 = false;
        }

        if (isMfCol3) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMfCol3(trueData);
            isMfCol3 = false;
        }

        if (isMfCol4) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMfCol4(trueData);
            isMfCol4 = false;
        }

        if (isMfSubMenuDetails) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMfSubMenuDetails(trueData);
            isMfSubMenuDetails = false;
        }

        if (isMfSubMenuImage) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMfSubMenuImage(trueData);
            isMfSubMenuImage = false;
        }
        if (isMfTagging) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMf_tagging(trueData);
            isMfTagging = false;
        }
        if (isMf_customer_branchwise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMf_customer_branchwise(trueData);
            isMf_customer_branchwise = false;
        }
        if (isMf_sub_menu_qty_unit) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMf_sub_menu_qty_unit(trueData);
            isMf_sub_menu_qty_unit = false;
        }
        if (isEx_for) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setEx_for(trueData);
            isEx_for = false;
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
