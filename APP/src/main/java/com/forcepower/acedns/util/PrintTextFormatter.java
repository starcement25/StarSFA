package com.forcepower.acedns.util;

import android.content.Context;
import android.text.SpannableStringBuilder;

/**
 * Created by amit on 04/01/2017.
 * //a4- width- 80 chars- height-
 */

public class PrintTextFormatter {
    Context context;

    public PrintTextFormatter(Context context) {
        this.context = context;
    }

    //line 1
    public String printLineOneSalesBill() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");
        sb.append(addSpace(34));
        sb.append("SALES BILL");
        sb.append(addSpaceAndEndLine(34));
        sb.append("|");
        return String.valueOf(sb);
    }

    //line 2
    public String printLineTwo(String customerName) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
        sb.append("Customer Name: ");//15
        sb.append(customerName);//must be 42 char
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //line 3
    public String printLineThree(String customerAddress, String companyAddress) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("Address");//address
//        sb.append(addSpace(39));
        sb.append(customerAddress);//must be 39 char
        sb.append("|");
        sb.append(addSpace(1));
//        sb.append(addSpaceAndEndLine(39));
        sb.append(companyAddress); //must be 39 char
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //line 4
    public String printLineFour(String customerPin, String companyPin) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("Pin Code:");
        sb.append(customerPin);//6 char
        sb.append(addSpace(24));
        sb.append("|");
        sb.append(addSpace(1));
        sb.append("Pin Code:");
        sb.append(companyPin);//6
        sb.append(addSpaceAndEndLine(24));
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //line 5
    public String printInvoiceDateLineSalesBill(String invoice, String date) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
        sb.append("Invoice:");//8
        sb.append(invoice);//must be 48 char
        sb.append(addSpace(1));//1
        sb.append("\r\n");

        sb.append("Date:");//5
        sb.append(date);//10 yyyy-MM-dd
        sb.append(addSpace(42));
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printDateFreight(String date) {
        SpannableStringBuilder sb = new SpannableStringBuilder();

        sb.append("Date:");//5
        sb.append(date);//10 yyyy-MM-dd
        sb.append(addSpace(42));
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //line 6
    public String printLineSix() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("Sl.");//3
        sb.append("|");//1
        sb.append("    Product    ");//15
        sb.append("|");//1
        sb.append("VAT(%)");//6
        sb.append("|");//1
        sb.append("Quantity");//8
        sb.append("|");//1
        sb.append("  Rate  ");//8
        sb.append("|");//1
        sb.append("  Amount  ");//10
        sb.append("|");//1
        sb.append("\r\n");//0
        return String.valueOf(sb);
    }

    //line 6
    public String printLineSevenAmountInWords(String amount) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("   ");//3
        sb.append("|");//1
        sb.append("Rs:");//3
        sb.append(amount);//49
        sb.append("\r\n");//0
//        sb.append("|");//1
//        sb.append(addSpaceAndEndLine(2));//2
        return String.valueOf(sb);
    }

    public String printLineSevenAmountInWordsFreight(String amount) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
        sb.append("Rs:");//3
        sb.append(amount);//54
        sb.append("\r\n");//0
//        sb.append("|");//1
//        sb.append(addSpaceAndEndLine(2));//2
        return String.valueOf(sb);
    }

    public String printLineEandOE() {
        SpannableStringBuilder sb = new SpannableStringBuilder();

        sb.append(addSpace(36));//36
        sb.append("E & OE");//6
        sb.append(addSpaceAndEndLine(15));//15

        sb.append(addSpace(33));//33
        sb.append("For RKBK Ltd.");//13
        sb.append(addSpaceAndEndLine(11));//11

//        sb.append(addEmptyLine());
        sb.append(addEmptyLine());
        sb.append(addEmptyLine());
        sb.append(addEmptyLine());
        sb.append(addEmptyLine());

        sb.append(addSpace(32));//32
        sb.append("Auth. Signatory");//15
        sb.append(addSpaceAndEndLine(10));//10

        return String.valueOf(sb);
    }

    public String printLineVateRateLoop(String vatDesc, String VatAmountCurrent) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("   ");//3
        sb.append("|");//1
        sb.append(vatDesc);//39
        sb.append(" ");//1
        sb.append(VatAmountCurrent);// within 12 chars
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printLineEightAdditionalVat(String vatAmount, String vatRate) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("   ");//3
        sb.append("|");//1
        sb.append(addSpace(13));
//        sb.append("Total without VAT    ");//21
        sb.append("     Additional VAT @");//21
        sb.append(vatRate);//6
//        sb.append(addSpace(1));//1

        sb.append(vatAmount);// within 12 chars
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //line 9
    public String printGrandTotal(String grandTotal) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("   ");//3
        sb.append("|");//1
        sb.append(addSpace(28));//28
        sb.append("Gross Total ");//12

//        sb.append("          ");//10
        sb.append(grandTotal);// within 12 chars
        sb.append("\r\n");
//        sb.append("|");//1
//        sb.append(addSpaceAndEndLine(2));//2
        return String.valueOf(sb);
    }

    //line 7
    public String printTotalPriceWithoutVat(String totalAmount) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("   ");//3
        sb.append("|");//1
        sb.append(addSpace(22));
        sb.append("Total without VAT ");//18

        sb.append(totalAmount);// within 12 chars
        sb.append("\r\n");
//        sb.append("|");//1
//        sb.append(addSpaceAndEndLine(2));//2
        return String.valueOf(sb);
    }

    //line 9
    public String printVatBreakUpString() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append(addSpace(33));//33
        sb.append("VAT BREAKUP");//11
        sb.append(addSpace(34));//34
        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }


    public String calculateStringWithSpaceLeftAligned(String data, int ExpectedLength) {
        int dataLength = data.length();

        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append(data);
        if (dataLength < ExpectedLength) {
            sb.append(addSpace(ExpectedLength - dataLength));
        } else if (ExpectedLength < dataLength) {
            int removableData = dataLength - ExpectedLength;
            sb = new SpannableStringBuilder();
            data = data.substring(0, dataLength - removableData);
            sb.append(data);
        }

        return String.valueOf(sb);
    }

    public String calculateStringWithSpaceRightAligned(String data, int ExpectedLength) {
        int dataLength = data.length();

        SpannableStringBuilder sb = new SpannableStringBuilder();
        if (dataLength < ExpectedLength) {
            sb.append(addSpace(ExpectedLength - dataLength));
        }
        sb.append(data);
        return String.valueOf(sb);
    }

    public String addSpace(int count) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        for (int l = 1; l <= count; l++) {
            sb.append(" ");
        }
        return String.valueOf(sb);
    }

    public String addOneColumn(String slNo, String productDescription, String vatRate, String quantity, String rate, String amount) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append(slNo);//3
        sb.append("|");//1
        sb.append(productDescription);//15
        sb.append("|");//1
        sb.append(vatRate);//6
        sb.append("|");//1
        sb.append(quantity);//8
        sb.append("|");//1
        sb.append(rate);//8
        sb.append("|");//1
        sb.append(amount);//10
        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String addSpaceAndEndLine(int count) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        for (int l = 1; l <= count; l++) {
            sb.append(" ");
        }
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String addUnderScoreEndLine(int count) {
        count = 57;//for a5
        SpannableStringBuilder sb = new SpannableStringBuilder();
        for (int l = 1; l <= count; l++) {
            sb.append("_");
        }
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String addEmptyLine() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append(addSpace(57));
//        sb.append(addSpace(80));
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String addEmptyLineWithMargin() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");
        sb.append(addSpace(78));
        sb.append("|");
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String addEmptyLineWithMargin2() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append(addSpace(64));//64
        sb.append("|");//1
        sb.append(addSpace(13));//13
        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printRKBKHeaderLineSalesBill() {
        SpannableStringBuilder sb = new SpannableStringBuilder();

//        sb.append("|");//1
        sb.append(addSpace(20));//20
        sb.append("TAX/RETAIL INVOICE");//17
        sb.append(addSpace(20));
//        sb.append("|");//1
        sb.append("\r\n");

//        sb.append("|");//1
        sb.append(addSpace(22));
        sb.append("RKBK LIMITED");//12
        sb.append(addSpace(23));
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printRKBKHeaderLineSalesBill2() {
        SpannableStringBuilder sb = new SpannableStringBuilder();

//        sb.append("|");//1
        sb.append(addSpace(20));//20
        sb.append("  Money Receipt  ");//17
        sb.append(addSpace(20));
//        sb.append("|");//1
        sb.append("\r\n");

//        sb.append("|");//1
        sb.append(addSpace(22));
        sb.append("RKBK LIMITED");//12
        sb.append(addSpace(23));
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printRKBKHeaderLineSalesBill3() {
        SpannableStringBuilder sb = new SpannableStringBuilder();

//        sb.append("|");//1
        sb.append(addSpace(20));//20
        sb.append("  Freight Bill   ");//17
        sb.append(addSpace(20));
//        sb.append("|");//1
        sb.append("\r\n");

//        sb.append("|");//1
        sb.append(addSpace(22));
        sb.append("RKBK LIMITED");//12
        sb.append(addSpace(23));
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printRKBKHeaderLineFreightBill() {
        SpannableStringBuilder sb = new SpannableStringBuilder();

//        sb.append("|");//1
        sb.append(addSpace(34));//33
        sb.append("FREIGHT BILL");//12
        sb.append(addSpace(34));
//        sb.append("|");//1
        sb.append("\r\n");

//        sb.append("|");//1
        sb.append(addSpace(34));
        sb.append("RKBK LIMITED");//12
        sb.append(addSpace(34));
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printRKBKHeaderLineMoneyReceipt() {
        SpannableStringBuilder sb = new SpannableStringBuilder();

        sb.append("|");//1
        sb.append(addSpace(33));
        sb.append("MONEY RECEIPT");//13
        sb.append(addSpace(32));
        sb.append("|");//1
        sb.append("\r\n");

        sb.append("|");//1
        sb.append(addSpace(33));
        sb.append("RKBK LIMITED");//12
        sb.append(addSpace(33));
        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printRKBKAddress() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
//        sb.append(addSpace(9));
//        sb.append("Regd. Office: 28, Maulavi Muzibur Rahman Sarani, Kolkata-17");
        sb.append("Regd. Ofc: 28, Maulavi Muzibur Rahman Sarani, Kolkata-17");
        sb.append(addSpace(1));
//        sb.append("|");//1
        sb.append("\r\n");

//        sb.append("|");//1
        sb.append(addSpace(16));
        sb.append("CIN: U51909WB1976PLC30289");//25
        sb.append(addSpace(16));
//        sb.append("|");//1
        sb.append("\r\n");

//        sb.append("|");//1
        sb.append(addSpace(12));
        sb.append("Head Office: Station Road, Gonda");//32
        sb.append(addSpace(13));
//        sb.append("|");//1
        sb.append("\r\n");

//        sb.append("|");//1
        sb.append(addSpace(21));
        sb.append("Branch Office:");//14
        sb.append(addSpace(22));
//        sb.append("|");//1
        sb.append("\r\n");

//        sb.append("|");//1
        sb.append(addSpace(8));
        sb.append("TIN No: 09155300117 C.S.T. No: GA-5007290");
        sb.append(addSpace(8));
//        sb.append("|");//1
        sb.append("\r\n");

        return String.valueOf(sb);
    }

    public String printInvoiceMoneyReceipt(String invoice) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append(addSpace(18));//18
        sb.append("Invoice: ");//9
        sb.append(invoice);//51
        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //received with thanks from
    public String printRKBKMoneyReceiptLine3(String customerName) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("RECEIVED with thanks from:");//26
        sb.append(customerName);//31
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //the sum of rupees
    public String printRKBKMoneyReceiptLine4(String totalAmount) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
        sb.append("The sum of rupees: ");//19
        sb.append(totalAmount);//38
//        sb.append("|");//1
//        sb.append(currency);//3
//        sb.append(totalAmount2);//10
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //by, number, date
    public String printRKBKMoneyReceiptLine5() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("         By         ");//20
        sb.append("|");//1
        sb.append("          Number           ");//27
        sb.append("|");//1
        sb.append("     Date      ");//15
        sb.append("|");//1
        sb.append("   Drawn on  ");//13
        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //Cash/Cheque/D.D./No.
    public String printRKBKMoneyReceiptLine6() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("Cash/Cheque/D.D./No.");//20
        sb.append("|");//1
        sb.append(addSpace(27));//27
        sb.append("|");//1
        sb.append(addSpace(15));//15
        sb.append("|");//1
        sb.append(addSpace(13));//13
        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //Cash/Cheque/D.D./No.
    public String printRKBKMoneyReceiptLine6(String paymentTypeString) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append(paymentTypeString);//57
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //on account of
    public String printRKBKMoneyReceiptLine7(String invocieNumber) {
        SpannableStringBuilder sb = new SpannableStringBuilder();

        sb.append("on account of ");//14
        sb.append(invocieNumber);//43

        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //deposited in
    public String printRKBKMoneyReceiptLine8() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
        sb.append("Deposited in");//12
        sb.append(addSpace(45));//45
//        sb.append("|");//1
//        sb.append(addSpace(13));//13
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //subject to realisation of
    public String printRKBKMoneyReceiptLine9() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
        sb.append("Subject to realisation of  Cheque/Demand Draft");//46
        sb.append(addSpace(11));
//        sb.append("Customer's");//10
//        sb.append("|");//1
//        sb.append(addSpace(2));//2
//        sb.append("Authorised");//10
//        sb.append(addSpace(1));//1
//        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    //cheque demand draft
    public String printRKBKMoneyReceiptLine10() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append("   Cheque/Demand Draft   ");//25
        sb.append(addSpace(29));//29
        sb.append(" Signature");//10
        sb.append("|");//1
        sb.append(addSpace(2));//2
        sb.append("Signatory");//9
        sb.append(addSpace(2));//2
        sb.append("|");//1
        sb.append("\r\n");
        return String.valueOf(sb);
    }

    public String printFreightBillHeaderLine() {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append(addSpace(26));//26
        sb.append("Particular");//10
        sb.append(addSpace(21));//21
//        sb.append("|");//1
//        sb.append("      Amount      ");//18
//        sb.append("|");//1
        sb.append("\r\n");//0
        return String.valueOf(sb);
    }

    public String printFreightBillHeaderLine1(String amount) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
        sb.append(amount);//57
        sb.append("\r\n");//0
        sb.append("charges as freight against invoice No.");//38
        sb.append(addSpace(19));//19
        sb.append("\r\n");//0
        return String.valueOf(sb);
    }

    public String printFreightBillHeaderLine2(String invoiceAndDate, String amount) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
        sb.append("|");//1
        sb.append(invoiceAndDate);//56
        sb.append("\r\n");//0
        return String.valueOf(sb);
    }

    public String printFreightBillVat1(String vatRateString, String vatAmount) {
        SpannableStringBuilder sb = new SpannableStringBuilder();
//        sb.append("|");//1
        sb.append(vatRateString);//40
        sb.append(vatAmount);//17
//        sb.append("|");//1
        sb.append("\r\n");//0
        return String.valueOf(sb);
    }

}
