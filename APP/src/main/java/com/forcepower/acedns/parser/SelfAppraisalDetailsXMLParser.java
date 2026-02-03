package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.SelfAppraisalDetails;
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

public class SelfAppraisalDetailsXMLParser extends DefaultHandler {

    ArrayList<SelfAppraisalDetails> list = new ArrayList<>();
    StringBuilder sb;
    Boolean mBoolSelfAppraisalId = false;
    Boolean mBoolUserId = false;
    Boolean mBoolmultipleTargetAchievement = false;
    Boolean multipleTargetAchievementVal = false;
    Boolean mBoolvolumeWise = false;
    Boolean mBoolvalueWise = false;
    Boolean mBoolproductGroupWise = false;
    Boolean mBoolproductSubGroupWise = false;
    Boolean mBoolproductBrandWise = false;
    Boolean mBoolproductWise = false;
    Boolean mBoolemployeeWise = false;
    Boolean mBoolcustomerWise = false;
    Boolean mBoolbranchWise = false;
    Boolean mBoolHQWise = false;
    Boolean mBoolrouteWise = false;
    Boolean mBoolonTotal = false;
    Boolean mBoolonIndividual = false;
    Boolean mBoolmonthWise = false;
    Boolean mBoolweekWise = false;
    Boolean mBooldayWise = false;
    Boolean mBoolUomVal = false;
    Boolean mPreviousYear = false;
    Boolean vertical_wise = false;
    private SelfAppraisalDetails details;

    public SelfAppraisalDetailsXMLParser(String datafromserver) {
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

    public SelfAppraisalDetails getParsedData() {
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
            details = new SelfAppraisalDetails();
        }

        if (localName.equalsIgnoreCase("self_appraisal_id")) {
            mBoolSelfAppraisalId = true;
        }
        if (localName.equalsIgnoreCase("user_id")) {
            mBoolUserId = true;
        }
        if (localName.equalsIgnoreCase("multiple_target_achievement")) {
            mBoolmultipleTargetAchievement = true;
        }
        if (localName.equalsIgnoreCase("multiple_target_achievement_val")) {
            multipleTargetAchievementVal = true;
        }
        if (localName.equalsIgnoreCase("volume_wise")) {
            mBoolvolumeWise = true;
        }
        if (localName.equalsIgnoreCase("value_wise")) {
            mBoolvalueWise = true;
        }
        if (localName.equalsIgnoreCase("product_group_wise")) {
            mBoolproductGroupWise = true;
        }
        if (localName.equalsIgnoreCase("product_sub_group_wise")) {
            mBoolproductSubGroupWise = true;
        }
        if (localName.equalsIgnoreCase("product_brand_wise")) {
            mBoolproductBrandWise = true;
        }
        if (localName.equalsIgnoreCase("product_wise")) {
            mBoolproductWise = true;
        }
        if (localName.equalsIgnoreCase("employee_wise")) {
            mBoolemployeeWise = true;
        }
        if (localName.equalsIgnoreCase("customer_wise")) {
            mBoolcustomerWise = true;
        }
        if (localName.equalsIgnoreCase("branch_wise")) {
            mBoolbranchWise = true;
        }
        if (localName.equalsIgnoreCase("HQ_wise")) {
            mBoolHQWise = true;
        }
        if (localName.equalsIgnoreCase("route_wise")) {
            mBoolrouteWise = true;
        }
        if (localName.equalsIgnoreCase("on_total")) {
            mBoolonTotal = true;
        }
        if (localName.equalsIgnoreCase("on_individual")) {
            mBoolonIndividual = true;
        }
        if (localName.equalsIgnoreCase("month_wise")) {
            mBoolmonthWise = true;
        }
        if (localName.equalsIgnoreCase("week_wise")) {
            mBoolweekWise = true;
        }
        if (localName.equalsIgnoreCase("day_wise")) {
            mBooldayWise = true;
        }
        if (localName.equalsIgnoreCase("UOM_val")) {
            mBoolUomVal = true;
        }
        if (localName.equalsIgnoreCase("previous_year")) {
            mPreviousYear = true;
        }

        if (localName.equalsIgnoreCase("vertical_wise")) {
            vertical_wise = true;
        }


    }

    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (mBoolSelfAppraisalId) {
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

        if (mBoolmultipleTargetAchievement) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (multipleTargetAchievementVal) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolvolumeWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolvalueWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolproductGroupWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolproductSubGroupWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolproductBrandWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolproductWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolemployeeWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolcustomerWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolbranchWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolHQWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolrouteWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolonTotal) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolonIndividual) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolmonthWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolweekWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBooldayWise) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mBoolUomVal) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (mPreviousYear) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (vertical_wise) {
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

        if (mBoolSelfAppraisalId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSelfAppraisalId(trueData);
            mBoolSelfAppraisalId = false;
        }

        if (mBoolUserId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUserId(trueData);
            mBoolUserId = false;
        }

        if (mBoolmultipleTargetAchievement) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMultipleTargetAchievement(trueData);
            mBoolmultipleTargetAchievement = false;
        }

        if (multipleTargetAchievementVal) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMultipleTargetAchievementVal(trueData);
            multipleTargetAchievementVal = false;
        }

        if (mBoolvolumeWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setVolumeWise(trueData);
            mBoolvolumeWise = false;
        }

        if (mBoolvalueWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setValueWise(trueData);
            mBoolvalueWise = false;
        }

        if (mBoolproductGroupWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProductGroupWise(trueData);
            mBoolproductGroupWise = false;
        }

        if (mBoolproductSubGroupWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProductSubGroupWise(trueData);
            mBoolproductSubGroupWise = false;
        }
        if (mBoolproductBrandWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProductBrandWise(trueData);
            mBoolproductBrandWise = false;
        }
        if (mBoolproductWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProductWise(trueData);
            mBoolproductWise = false;
        }
        if (mBoolemployeeWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setEmployeeWise(trueData);
            mBoolemployeeWise = false;
        }
        if (mBoolcustomerWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCustomerWise(trueData);
            mBoolcustomerWise = false;
        }
        if (mBoolbranchWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBranchWise(trueData);
            mBoolbranchWise = false;
        }
        if (mBoolHQWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setHQWise(trueData);
            mBoolHQWise = false;
        }
        if (mBoolrouteWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRouteWise(trueData);
            mBoolrouteWise = false;
        }
        if (mBoolonTotal) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOnTotal(trueData);
            mBoolonTotal = false;
        }
        if (mBoolonIndividual) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setOnIndividual(trueData);
            mBoolonIndividual = false;
        }
        if (mBoolmonthWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMonthWise(trueData);
            mBoolmonthWise = false;
        }
        if (mBoolweekWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setWeekWise(trueData);
            mBoolweekWise = false;
        }
        if (mBooldayWise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDayWise(trueData);
            mBooldayWise = false;
        }
        if (mBoolUomVal) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUomVal(trueData);
            mBoolUomVal = false;
        }
        if (mPreviousYear) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setPrevious_year(trueData);
            mPreviousYear = false;
        }
        if (vertical_wise) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setvertical_wise(trueData);
            vertical_wise = false;
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
