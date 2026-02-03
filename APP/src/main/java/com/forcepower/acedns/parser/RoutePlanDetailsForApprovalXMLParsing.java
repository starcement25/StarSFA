package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.RoutePlanDetails;
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

public class RoutePlanDetailsForApprovalXMLParsing extends DefaultHandler {

    ArrayList<RoutePlanDetails> list = new ArrayList<RoutePlanDetails>();
    StringBuilder sb;
    boolean routePlanId;
    boolean userId;
    boolean routePlanAccessPeriod;
    boolean routePlanDeviation;
    boolean routePlanApproval;
    boolean routePlanFlow;
    boolean routecustomerplanning;
    boolean routedistributorplaning;
    boolean routedistributorplaningmultiple;
    boolean routeplanremarks;
    boolean route_plan_change_request_approval;
    boolean timeStamp;
    private RoutePlanDetails details;

    public RoutePlanDetailsForApprovalXMLParsing(String datafromserver) {
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

    public RoutePlanDetails getParsedData() {
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
            details = new RoutePlanDetails();
        }
        if (localName.equalsIgnoreCase("route_plan_id")) {
            routePlanId = true;
        }
        if (localName.equalsIgnoreCase("user_id")) {
            userId = true;
        }
        if (localName.equalsIgnoreCase("route_plan_access_period")) {
            routePlanAccessPeriod = true;
        }
        if (localName.equalsIgnoreCase("route_plan_deviation")) {
            routePlanDeviation = true;
        }
        if (localName.equalsIgnoreCase("route_plan_approval")) {
            routePlanApproval = true;
        }
        if (localName.equalsIgnoreCase("route_plan_flow")) {
            routePlanFlow = true;
        }
        if (localName.equalsIgnoreCase("route_customer_planning")) {
            routecustomerplanning = true;
        }
        if (localName.equalsIgnoreCase("distributor_route_planning")) {
            routedistributorplaning = true;
        }
        if (localName.equalsIgnoreCase("distributor_route_planning_multiple")) {
            routedistributorplaningmultiple = true;
        }
        if (localName.equalsIgnoreCase("route_plan_remarks")) {
            routeplanremarks = true;
        }
        if (localName.equalsIgnoreCase("route_plan_change_request_approval")) {
            route_plan_change_request_approval = true;
        }

        if (localName.equalsIgnoreCase("last_update_time")) {
            timeStamp = true;
        }
    }


    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (routePlanId) {
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

        if (routePlanAccessPeriod) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }


        if (routePlanDeviation) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (routePlanApproval) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (routePlanFlow) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (routecustomerplanning) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (routedistributorplaning) {
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
        if (routedistributorplaningmultiple) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (routeplanremarks) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (route_plan_change_request_approval) {
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

        if (routePlanId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRoutePlanId(trueData);
            routePlanId = false;
        }

        if (userId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUserId(trueData);
            userId = false;
        }

        if (routePlanAccessPeriod) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRoutePlanAccessPeriod(trueData);
            routePlanAccessPeriod = false;
        }

        if (routePlanDeviation) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRoutePlanDeviation(trueData);
            routePlanDeviation = false;
        }

        if (routePlanApproval) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRoutePlanApproval(trueData);
            routePlanApproval = false;
        }

        if (routePlanFlow) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRoutePlanFlow(trueData);
            routePlanFlow = false;
        }

        if (routecustomerplanning) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRouteCustomerPlanning(trueData);
            routecustomerplanning = false;
        }

        if (routedistributorplaning) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDistributorRoutePlanning(trueData);
            routedistributorplaning = false;
        }

        if (timeStamp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setLastUpdateTime(trueData);
            timeStamp = false;
        }
        if (routedistributorplaningmultiple) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDistributorRoutePlanningMultiple(trueData);
            routedistributorplaningmultiple = false;
        }
        if (routeplanremarks) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRouteplanremarks(trueData);
            routeplanremarks = false;
        }
        if (route_plan_change_request_approval) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setRoute_plan_change_request_approval(trueData);
            route_plan_change_request_approval = false;
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
