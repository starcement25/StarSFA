package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.SurveyFormDetails;
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

public class SurveyFormDetailsXMLParser extends DefaultHandler {

    ArrayList<SurveyFormDetails> list = new ArrayList<SurveyFormDetails>();
    StringBuilder sb;
    Boolean mBoolSurveyFormId = false;
    Boolean mBoolSurveyUserId = false;
    Boolean mBoolSurveyMenu = false;
    Boolean mBoolSurveyType = false;
    Boolean mBoolSurveyTypeDetails = false;
    Boolean mBoolMallSurveyRelation = false;
    Boolean mBoolSurveySubTypeDetails = false;
    Boolean mBoolSurveyOTP = false;
    Boolean mBoolSurveyLayer = false;
    Boolean mBoolSurveySubMenu = false;
    Boolean mBoolSurveySubMenuDetails = false;
    Boolean mBoolSurveyOutlet = false;
    Boolean mBoolSurveyRoute = false;
    Boolean mBoolSurveyOtherText = false;
    Boolean mBoolSurveyReportRowId = false;
    Boolean customer_email_update = false;
    Boolean special_input_screen = false;
    Boolean survey_menu_name  = false;
    Boolean menu_disp_sub_menu  = false;
    Boolean check_in_time_captured_menu = false;
    Boolean follow_up_menu = false;
    private SurveyFormDetails details;

    public SurveyFormDetailsXMLParser(String datafromserver) {
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

    public SurveyFormDetails getParsedData() {
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
            details = new SurveyFormDetails();
        }

        if (localName.equalsIgnoreCase("survey_form_id")) {
            mBoolSurveyFormId = true;
        }
        if (localName.equalsIgnoreCase("user_id")) {
            mBoolSurveyUserId = true;
        }
        if (localName.equalsIgnoreCase("survey_menu")) {
            mBoolSurveyMenu = true;
        }
        if (localName.equalsIgnoreCase("survey_type")) {
            mBoolSurveyType = true;
        }

        if (localName.equalsIgnoreCase("survey_type_details")) {
            mBoolSurveyTypeDetails = true;
        }

        if (localName.equalsIgnoreCase("mall_survey_relation")) {
            mBoolMallSurveyRelation = true;
        }

        if (localName.equalsIgnoreCase("survey_sub_type_details")) {
            mBoolSurveySubTypeDetails = true;
        }

        if (localName.equalsIgnoreCase("OTP")) {
            mBoolSurveyOTP = true;
        }

        if (localName.equalsIgnoreCase("survey_layer")) {
            mBoolSurveyLayer = true;
        }

        if (localName.equalsIgnoreCase("survey_submenu")) {
            mBoolSurveySubMenu = true;
        }

        if (localName.equalsIgnoreCase("survey_submenu_details")) {
            mBoolSurveySubMenuDetails = true;
        }

        if (localName.equalsIgnoreCase("outlet_menu")) {
            mBoolSurveyOutlet = true;
        }

        if (localName.equalsIgnoreCase("survey_route_plan")) {
            mBoolSurveyRoute = true;
        }

        if (localName.equalsIgnoreCase("other_text")) {
            mBoolSurveyOtherText = true;
        }

        if (localName.equalsIgnoreCase("survey_report_row_id")) {
            mBoolSurveyReportRowId = true;
        }
        if (localName.equalsIgnoreCase("customer_email_update")) {
            customer_email_update = true;
        }
        if (localName.equalsIgnoreCase("special_input_screen")) {
            special_input_screen = true;
        }
        if (localName.equalsIgnoreCase("survey_menu_name")) {
            survey_menu_name = true;
        }
        if (localName.equalsIgnoreCase("menu_disp_sub_menu")) {
            menu_disp_sub_menu = true;
        }
        if (localName.equalsIgnoreCase("check_in_time_captured_menu")) {
            check_in_time_captured_menu = true;
        }
        if (localName.equalsIgnoreCase("follow_up_menu")) {
            follow_up_menu = true;
        }

    }

    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (mBoolSurveyFormId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyUserId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyMenu) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyType) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyTypeDetails) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolMallSurveyRelation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveySubTypeDetails) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyOTP) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyLayer) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveySubMenu) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveySubMenuDetails) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyOutlet) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyRoute) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyOtherText) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (mBoolSurveyReportRowId) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (customer_email_update) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (special_input_screen) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (survey_menu_name) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (menu_disp_sub_menu) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (check_in_time_captured_menu) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (follow_up_menu) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

    }

    public void endElement(String uri, String localName, String qName) throws SAXException {
        super.endElement(uri, localName, qName);

        if (mBoolSurveyFormId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyFormId(trueData);
            mBoolSurveyFormId = false;
        }

        if (mBoolSurveyUserId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyUserId(trueData);
            mBoolSurveyUserId = false;
        }

        if (mBoolSurveyMenu) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyMenu(trueData);
            mBoolSurveyMenu = false;
        }

        if (mBoolSurveyType) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyType(trueData);
            mBoolSurveyType = false;
        }

        if (mBoolSurveyTypeDetails) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyTypeDetails(trueData);
            mBoolSurveyTypeDetails = false;
        }

        if (mBoolMallSurveyRelation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyMallSurveyRelation(trueData);
            mBoolMallSurveyRelation = false;
        }

        if (mBoolSurveySubTypeDetails) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveySubTypeDetails(trueData);
            mBoolSurveySubTypeDetails = false;
        }

        if (mBoolSurveyOTP) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyOTP(trueData);
            mBoolSurveyOTP = false;
        }

        if (mBoolSurveyLayer) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyLayer(trueData);
            mBoolSurveyLayer = false;
        }

        if (mBoolSurveySubMenu) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveySubMenu(trueData);
            mBoolSurveySubMenu = false;
        }

        if (mBoolSurveySubMenuDetails) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveySubMenuDetails(trueData);
            mBoolSurveySubMenuDetails = false;
        }

        if (mBoolSurveyOutlet) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyOutletMenu(trueData);
            mBoolSurveyOutlet = false;
        }

        if (mBoolSurveyRoute) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyRoutePlan(trueData);
            mBoolSurveyRoute = false;
        }

        if (mBoolSurveyOtherText) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyOtherText(trueData);
            mBoolSurveyOtherText = false;
        }

        if (mBoolSurveyReportRowId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSurveyReportRowId(trueData);
            mBoolSurveyReportRowId = false;
        }
        if (customer_email_update) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCustomerEmailUpdate(trueData);
            customer_email_update = false;
        }
        if (special_input_screen) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setspecial_input_screen(trueData);
            special_input_screen = false;
        }
        if (survey_menu_name) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setsurvey_menu_name(trueData);
            survey_menu_name = false;
        }
        if (menu_disp_sub_menu) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setmenu_disp_sub_menu(trueData);
            menu_disp_sub_menu = false;
        }
        if (check_in_time_captured_menu) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCheck_in_time_captured_menu(trueData);
            check_in_time_captured_menu = false;
        }
        if (follow_up_menu) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setFollow_up_menu(trueData);
            follow_up_menu = false;
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
