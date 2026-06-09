package com.forcepower.acedns.new_activity.nt_quotation.custom;

import java.util.Arrays;
import java.util.List;

public class LeadQuotationStatusTitleColor {
    static List<String> titleObject = Arrays.asList(
            "", // 0
            "SO LEAD CREATE", // 1
            "HOS HOLD", // 2
            "HOS REVISION", // 3
            "HOS REJECT", // 4
            "HOS APPROVED", // 5
            "MIS QUOTATION CREATE", // 6
            "MIS SEND TO COO", // 7
            "COO LEAD REVISION", // 8
            "COO LEAD REJECT", // 9
            "COO LEAD APPROVED", // 10
            "MIS SENT TO SAP", // 11
            "LOST ORDER", // 12
            "SAP QUOTATION CREATE", // 13
            "MIS SEND QUOTATION TO CUSTOMER", // 14
            "SO PO RECEIVED", // 15
            "HOS PO APPROVE", // 16
            "HOS PO SEND FOR REVISION", // 17
            "MIS PO APPROVE AND SEND TO SAP", // 18
            "MIS PO SEND FOR REVISION", // 19
            "SAP CONTRACT CREATE", // 20
            "SAP SO CREATE" // 21
    );
    static List<String> colorObject = Arrays.asList(
            "#000000", //"  ",
            "#000000", //"SO LEAD CREATE",
            "#F59127", //"HOS HOLD",
            "#555555", //"HOS REVISION",
            "#F52727", //"HOS REJECT",
            "#000000", //"HOS APPROVED",
            "#000000", //"MIS QUOTATION CREATE",
            "#000000", //"MIS SEND TO COO",
            "#555555", //"COO LEAD REVISION",
            "#F52727", //"COO LEAD REJECT",
            "#000000", //"COO LEAD APPROVED",
            "#000000", //"MIS SENT TO SAP",
            "#F52727", //"LOST ORDER",
            "#000000", //"SAP QUOTATION CREATE",
            "#000000", //"MIS SEND QUOTATION TO CUSTOMER",
            "#555555", //"SO PO RECEIVED",
            "#000000", //"HOS PO APPROVE",
            "#555555", //"HOS PO SEND FOR REVISION",
            "#000000", //"MIS PO APPROVE AND SEND TO SAP",
            "#555555", //"MIS PO SEND FOR REVISION",
            "#000000", //"SAP CONTRACT CREATE",
            "#000000" //"SAP SO CREATE"
    );

    public static String getTitle(int position){
        return titleObject.get(position);
    }
    public static String getColor(int position){
        return colorObject.get(position);
    }
}