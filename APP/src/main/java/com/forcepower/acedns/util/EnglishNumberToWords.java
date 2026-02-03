package com.forcepower.acedns.util;

import java.text.DecimalFormat;

/**
 * Created by amit on 10/01/2017.
 */

public class EnglishNumberToWords {
    private static final String[] tensNames =
            {
                    "",
                    " Ten",
                    " Twenty",
                    " Thirty",
                    " Forty",
                    " Fifty",
                    " Sixty",
                    " Seventy",
                    " Eighty",
                    " Ninety"
            };

    private static final String[] numNames =
            {
                    "",
                    " One",
                    " Two",
                    " Three",
                    " Four",
                    " Five",
                    " Six",
                    " Seven",
                    " Eight",
                    " Nine",
                    " Ten",
                    " Eleven",
                    " Twelve",
                    " Thirteen",
                    " Fourteen",
                    " Fifteen",
                    " Sixteen",
                    " Seventeen",
                    " Eighteen",
                    " Nineteen"
            };

    public EnglishNumberToWords() {
    }

    private static String convertLessThanOneThousand(int number) {
        String soFar;

        if (number % 100 < 20) {
            soFar = numNames[number % 100];
            number /= 100;
        } else {
            soFar = numNames[number % 10];
            number /= 10;

            soFar = tensNames[number % 10] + soFar;
            number /= 10;
        }
        if (number == 0) return soFar;
        return numNames[number] + " Hundred" + soFar;
    }

    public static String convert(long number) {
        // 0 to 999 99 99 999
        if (number == 0) {
            return "Zero";
        }

        String snumber = Long.toString(number);

        // pad with "0"
        String mask = "0000000000";
        DecimalFormat df = new DecimalFormat(mask);
        snumber = df.format(number);

        // XXXnnnnnnn
        int Crores = Integer.parseInt(snumber.substring(0, 3));
        // nnnXXnnnnn
        int Lacks = Integer.parseInt(snumber.substring(3, 5));
        // nnnnnXXnnn
        int Thousands = Integer.parseInt(snumber.substring(5, 7));
        // nnnnnnnnnXXX
        int hundred = Integer.parseInt(snumber.substring(7, 10));

        String tradCrores;
        switch (Crores) {
            case 0:
                tradCrores = "";
                break;
            case 1:
                tradCrores = convertLessThanOneThousand(Crores)
                        + " Crore ";
                break;
            default:
                tradCrores = convertLessThanOneThousand(Crores)
                        + " Crores ";
        }
        String result = tradCrores;

        String tradLacks;
        switch (Lacks) {
            case 0:
                tradLacks = "";
                break;
            case 1:
                tradLacks = convertLessThanOneThousand(Lacks)
                        + " Lack ";
                break;
            default:
                tradLacks = convertLessThanOneThousand(Lacks)
                        + " Lacks ";
        }
        result = result + tradLacks;

        String tradThousands;
        switch (Thousands) {
            case 0:
                tradThousands = "";
                break;
            case 1:
                tradThousands = "One Thousand ";
                break;
            default:
                tradThousands = convertLessThanOneThousand(Thousands)
                        + " Thousand ";
        }
        result = result + tradThousands;

        String tradHundred;
        tradHundred = convertLessThanOneThousand(hundred);
        result = result + tradHundred;

        // remove extra spaces!
        return result.replaceAll("^\\s+", "").replaceAll("\\b\\s{2,}\\b", " ");
    }

    /**
     * testing
     * @param args
     */
//    public static void main(String[] args) {
//        System.out.println("*** " + EnglishNumberToWords.convert(0));
//        System.out.println("*** " + EnglishNumberToWords.convert(1));
//        System.out.println("*** " + EnglishNumberToWords.convert(16));
//        System.out.println("*** " + EnglishNumberToWords.convert(100));
//        System.out.println("*** " + EnglishNumberToWords.convert(118));
//        System.out.println("*** " + EnglishNumberToWords.convert(200));
//        System.out.println("*** " + EnglishNumberToWords.convert(219));
//        System.out.println("*** " + EnglishNumberToWords.convert(800));
//        System.out.println("*** " + EnglishNumberToWords.convert(801));
//        System.out.println("*** " + EnglishNumberToWords.convert(1316));
//        System.out.println("*** " + EnglishNumberToWords.convert(1000000));
//        System.out.println("*** " + EnglishNumberToWords.convert(2000000));
//        System.out.println("*** " + EnglishNumberToWords.convert(3000200));
//        System.out.println("*** " + EnglishNumberToWords.convert(700000));
//        System.out.println("*** " + EnglishNumberToWords.convert(9000000));
//        System.out.println("*** " + EnglishNumberToWords.convert(9001000));
//        System.out.println("*** " + EnglishNumberToWords.convert(123456789));
//        System.out.println("*** " + EnglishNumberToWords.convert(2147483647));
//        System.out.println("*** " + EnglishNumberToWords.convert(3000000010L));

    /*
     *** zero
     *** one
     *** sixteen
     *** one hundred
     *** one hundred eighteen
     *** two hundred
     *** two hundred nineteen
     *** eight hundred
     *** eight hundred one
     *** one thousand three hundred sixteen
     *** one million
     *** two millions
     *** three millions two hundred
     *** seven hundred thousand
     *** nine millions
     *** nine millions one thousand
     *** one hundred twenty three millions four hundred
     **      fifty six thousand seven hundred eighty nine
     *** two billion one hundred forty seven millions
     **      four hundred eighty three thousand six hundred forty seven
     *** three billion ten
     **/
//    }
}
