package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.TransDeleteDetails;
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

public class TransactionDeleteXMLParser extends DefaultHandler {
    ArrayList<TransDeleteDetails> list = new ArrayList<TransDeleteDetails>();
    StringBuilder sb;
    boolean transactionID;
    boolean branchCode;
    boolean rdsCode;
    boolean startDate;
    boolean endDate;
    boolean MODE;
    private TransDeleteDetails details;

    public TransactionDeleteXMLParser(String datafromserver) {
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

    public ArrayList<TransDeleteDetails> getParsedData() {
        return this.list;
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

        if (localName.equalsIgnoreCase("deletion_data")) {
            details = new TransDeleteDetails();
        }
        if (localName.equalsIgnoreCase("order_no")) {
            transactionID = true;
        }
        if (localName.equalsIgnoreCase("branch_code")) {
            branchCode = true;
        }
        if (localName.equalsIgnoreCase("rds_code")) {
            rdsCode = true;
        }
        if (localName.equalsIgnoreCase("start_date")) {
            startDate = true;
        }
        if (localName.equalsIgnoreCase("end_date")) {
            endDate = true;
        }
        if (localName.equalsIgnoreCase("deletion_mode")) {
            MODE = true;
        }
    }


    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (transactionID) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }


        if (branchCode) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (rdsCode) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }


        if (startDate) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (endDate) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (MODE) {
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

        if (transactionID) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTransID(trueData);
            transactionID = false;
        }

        if (branchCode) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBrancsCode(trueData);
            branchCode = false;
        }

        if (rdsCode) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRdsCode(trueData);
            rdsCode = false;
        }

        if (startDate) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setStartDate(trueData);
            startDate = false;
        }

        if (endDate) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setEndDate(trueData);
            endDate = false;
        }

        if (MODE) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMODE(trueData);
            MODE = false;
        }

        if (localName.equalsIgnoreCase("deletion_data")) {
            list.add(details);
        }
    }


    public String getTrueData(String data) {
//    	String[] dataArray = data.split("[");
//    	String[] dataArray1 = dataArray[2].split("]");
//    	return dataArray1[0];
        return data;
    }

}