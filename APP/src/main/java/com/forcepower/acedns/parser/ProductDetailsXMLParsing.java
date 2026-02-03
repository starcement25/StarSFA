package com.forcepower.acedns.parser;

import com.forcepower.acedns.bean.ProductDetails;
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

public class ProductDetailsXMLParsing extends DefaultHandler {

    ArrayList<ProductDetails> list = new ArrayList<>();
    StringBuilder sb;
    boolean productId;
    boolean userId;
    boolean noFilter;
    boolean col1;
    boolean col2;
    boolean col3;
    boolean col4;
    //boolean conversion;
    boolean uomWiseMRP;

    boolean saudaFilter;
    boolean productBusinessProspect;
    boolean branchWiseProduct;
    boolean secondaryunit;
    boolean destinationpricelist;
    boolean destinationOrderTypePriceList;
    boolean stateWiseMrp;
    boolean multipleRate;
    boolean ProductQtyWiseTD;
    boolean FocusProduct;
    boolean prod_size;
    boolean prod_size_seperator;
    boolean branch_wise_mrp;
    private ProductDetails details;
    //boolean timeStamp;

    public ProductDetailsXMLParsing(String datafromserver) {
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

    public ProductDetails getParsedData() {
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
            details = new ProductDetails();
        }
        if (localName.equalsIgnoreCase("product_id")) {
            productId = true;
        }
        if (localName.equalsIgnoreCase("user_id")) {
            userId = true;
        }
        if (localName.equalsIgnoreCase("no_of_filter")) {
            noFilter = true;
        }
        if (localName.equalsIgnoreCase("col1")) {
            col1 = true;
        }
        if (localName.equalsIgnoreCase("col2")) {
            col2 = true;
        }
        if (localName.equalsIgnoreCase("col3")) {
            col3 = true;
        }
        if (localName.equalsIgnoreCase("col4")) {
            col4 = true;
        }
        if (localName.equalsIgnoreCase("uom_wise_mrp")) {
            uomWiseMRP = true;
        }
        if (localName.equalsIgnoreCase("sauda_allocation_basedon_filter")) {
            saudaFilter = true;
        }
        if (localName.equalsIgnoreCase("product_in_business_prospect")) {
            productBusinessProspect = true;
        }
        if (localName.equalsIgnoreCase("branch_wise_product")) {
            branchWiseProduct = true;
        }
        if (localName.equalsIgnoreCase("secondary_unit")) {
            secondaryunit = true;
        }
        if (localName.equalsIgnoreCase("destination_price_list")) {
            destinationpricelist = true;
        }
        if (localName.equalsIgnoreCase("destination_ordertype_price_list")) {
            destinationOrderTypePriceList = true;
        }
        if (localName.equalsIgnoreCase("state_wise_mrp")) {
            stateWiseMrp = true;
        }
        if (localName.equalsIgnoreCase("multiple_rate")) {
            multipleRate = true;
        }
        if (localName.equalsIgnoreCase("product_qty_wise_TD")) {
            ProductQtyWiseTD = true;
        }
        if (localName.equalsIgnoreCase("focus_product")) {
            FocusProduct = true;
        }
        if (localName.equalsIgnoreCase("prod_size")) {
            prod_size = true;
        }
        if (localName.equalsIgnoreCase("prod_size_seperator")) {
            prod_size_seperator = true;
        }
        if (localName.equalsIgnoreCase("branch_wise_mrp")) {
            branch_wise_mrp = true;
        }

    }


    public void characters(char[] ch, int start, int length)
            throws SAXException {
        super.characters(ch, start, length);

        if (productId) {
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

        if (noFilter) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }


        if (col1) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (col2) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (col3) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (col4) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

//        if(conversion){
//          	 if(details!=null)
//          		for (int i=start; i<start+length; i++) {
//         		  sb.append(ch[i]);
//               }               		
//           }

        if (uomWiseMRP) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (saudaFilter) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (productBusinessProspect) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (branchWiseProduct) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (secondaryunit) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }

        if (destinationpricelist) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (destinationOrderTypePriceList) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (stateWiseMrp) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (multipleRate) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (ProductQtyWiseTD) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (FocusProduct) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (prod_size) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (prod_size_seperator) {
            if (details != null)
                for (int i = start; i < start + length; i++) {
                    sb.append(ch[i]);
                }
        }
        if (branch_wise_mrp) {
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

        if (productId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProductId(trueData);
            productId = false;
        }

        if (userId) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUserId(trueData);
            userId = false;
        }

        if (noFilter) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setNoFilter(trueData);
            noFilter = false;
        }

        if (col1) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCol1(trueData);
            col1 = false;
        }

        if (col2) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCol2(trueData);
            col2 = false;
        }

        if (col3) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCol3(trueData);
            col3 = false;
        }

        if (col4) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setCol4(trueData);
            col4 = false;
        }

//        if(conversion){
//        	String finalString = sb.toString();
//        	String trueData = getTrueData(finalString);
//        	details.setConversion(trueData);
//        	conversion = false;
//        }

        if (uomWiseMRP) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setUomWiseMRP(trueData);
            uomWiseMRP = false;
        }


        if (saudaFilter) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSaudaFilter(trueData);
            saudaFilter = false;
        }

        if (productBusinessProspect) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProductBusinessProspect(trueData);
            productBusinessProspect = false;
        }
        if (branchWiseProduct) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBranchWiseProduct(trueData);
            branchWiseProduct = false;
        }
        if (secondaryunit) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setSecondaryUnit(trueData);
            secondaryunit = false;
        }

        if (destinationpricelist) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDestinationPriceList(trueData);
            destinationpricelist = false;
        }

        if (destinationOrderTypePriceList) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setDestinationOrderTypePriceList(trueData);
            destinationOrderTypePriceList = false;
        }

        if (stateWiseMrp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setStateWiseMrp(trueData);
            stateWiseMrp = false;
        }

        if (multipleRate) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setMultipleRate(trueData);
            multipleRate = false;
        }
        if (ProductQtyWiseTD) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setProductQtyWiseTD(trueData);
            ProductQtyWiseTD = false;
        }
        if (FocusProduct) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setFocusProduct(trueData);
            FocusProduct = false;
        }
        if (prod_size) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setprod_size(trueData);
            prod_size = false;
        }
        if (prod_size_seperator) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setprod_size_seperator(trueData);
            prod_size_seperator = false;
        }
        if (branch_wise_mrp) {
            String finalString = sb.toString();
            String trueData = getTrueData(finalString);
            details.setBranchWiseMRP(trueData);
            branch_wise_mrp = false;
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