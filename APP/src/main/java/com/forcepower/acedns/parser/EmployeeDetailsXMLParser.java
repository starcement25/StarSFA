package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.EmployeeDetails;
import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;

import java.io.ByteArrayInputStream;
import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

public class EmployeeDetailsXMLParser extends DefaultHandler {
    EmployeeDetails empObj = null;
    StringBuilder sb;

    boolean e_code;
    boolean e_name;
    boolean pwd;
    boolean devID;
    boolean saleAccess;
    boolean verificationtoken;
    boolean phoneNumber;
    boolean logintype;

    public EmployeeDetailsXMLParser(String datafromserver) {
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

    public EmployeeDetails getParsedData() {
        return this.empObj;
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
            empObj = new EmployeeDetails();
        }
        if (localName.equalsIgnoreCase("emp_code")) {
            e_code = true;
        }
        if (localName.equalsIgnoreCase("emp_name")) {
            e_name = true;
        }
        if (localName.equalsIgnoreCase("newpassword")) {
            pwd = true;
        }
        if (localName.equalsIgnoreCase("deviceid")) {
            devID = true;
        }
        if (localName.equalsIgnoreCase("sale_access")) {
            saleAccess = true;
        }
        if (localName.equalsIgnoreCase("verification_token")) {
            verificationtoken = true;
        }
        if (localName.equalsIgnoreCase("phonenumber")) {
            phoneNumber = true;
        }
    }

    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);
        if (e_code) {
            if (empObj != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (e_name) {
            if (empObj != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (pwd) {
            if (empObj != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (devID) {
            if (empObj != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (saleAccess) {
            if (empObj != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (verificationtoken) {
            if (empObj != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (phoneNumber) {
            if (empObj != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
    }

    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        super.endElement(uri, localName, qName);
        if (e_code) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            empObj.setEmpCode(trueData);
            e_code = false;
        }
        if (e_name) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            empObj.setEmpName(trueData);
            e_name = false;
        }
        if (pwd) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            empObj.setNewPassword(trueData);
            pwd = false;
        }
        if (devID) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            empObj.setDeviceID(trueData);
            devID = false;
        }
        if (saleAccess) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            empObj.setSaleAccess(trueData);
            saleAccess = false;
        }
        if (verificationtoken) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            empObj.setverificationtoken(trueData);
            verificationtoken = false;
        }
        if (phoneNumber) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            empObj.setPhoneNumber(trueData);
            phoneNumber = false;
        }
    }

    public String getTrueData(String data) {
        return data;
    }
}