package com.forcepower.acedns.parser;

import android.util.Log;

import com.forcepower.acedns.bean.DatabaseStructure;
import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

public class DatabaseDetailsXMLParsing extends DefaultHandler {

    ArrayList<DatabaseStructure> list = new ArrayList<DatabaseStructure>();
    StringBuilder sb;
    boolean tableName;
    boolean query;
    boolean transaction;
    boolean dbVersion;
    boolean isBaseUrlChanged;
    boolean isCurrentUrl;
    private DatabaseStructure details;

    public DatabaseDetailsXMLParsing(String datafromserver) {
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
            xr.parse(new InputSource(new ByteArrayInputStream(datafromserver.getBytes(StandardCharsets.ISO_8859_1))));
        } catch (IOException e) {
            e.printStackTrace();
        } catch (SAXException e) {
            e.printStackTrace();
        }
    }

    public ArrayList<DatabaseStructure> getParsedData() {
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


    public void startElement(String uri, String localName, String qName, Attributes attributes) throws SAXException {

        sb = new StringBuilder();

        super.startElement(uri, localName, qName, attributes);

        if (localName.equalsIgnoreCase("data")) {
            details = new DatabaseStructure();
        }
        if (localName.equalsIgnoreCase("table_name")) {
            tableName = true;
        }
        if (localName.equalsIgnoreCase("table_structure")) {
            query = true;
        }
        if (localName.equalsIgnoreCase("transaction")) {
            transaction = true;
        }
        if (localName.equalsIgnoreCase("db_version")) {
            dbVersion = true;
        }
        if (localName.equalsIgnoreCase("base_url_changed")) {
            isBaseUrlChanged = true;
        }
        if (localName.equalsIgnoreCase("current_baseurl_app")) {
            isCurrentUrl = true;
        }
    }


    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (tableName) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (query) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (transaction) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (dbVersion) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isBaseUrlChanged) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (isCurrentUrl) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
    }

    public void endElement(String uri, String localName, String qName)
            throws SAXException {
        super.endElement(uri, localName, qName);

        if (tableName) {
            String finalString = sb.toString();
            Log.d("table--", ""+finalString);
            String trueData = getTrueData(finalString);
            details.setTableName(trueData);
            tableName = false;
        }
        if (query) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setQuery(trueData);
            query = false;
        }
        if (transaction) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setTransaction(trueData);
            transaction = false;
        }
        if (dbVersion) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDbVersion(trueData);
            dbVersion = false;
        }
        if (isBaseUrlChanged) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBaseUrlChanged(trueData);
            isBaseUrlChanged = false;
        }
        if (isCurrentUrl) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCurrentUrl(trueData);
            isCurrentUrl = false;
        }


        if (localName.equalsIgnoreCase("data")) {
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