package com.forcepower.acedns.util;

import java.text.SimpleDateFormat;
import java.util.Date;

/**
 * Created by amit on 10/01/2017.
 */

public class DateTimeFormatter {

    public DateTimeFormatter() {

    }

    public static Boolean isDateOneGraterThanDate2(String inputDateFormat, String value1, String value2) {
        Boolean isDateOneGraterThanDate2 = false;
        try {
            SimpleDateFormat formatter = new SimpleDateFormat(inputDateFormat);
            Date date1 = formatter.parse(value1);
            Date date2 = formatter.parse(value2);

            if (date1.compareTo(date2) < 0) {
                isDateOneGraterThanDate2 = false;
            } else {
                isDateOneGraterThanDate2 = true;
            }
        } catch (Exception e) {
            isDateOneGraterThanDate2 = false;
        }

        return isDateOneGraterThanDate2;
    }

    public String changeDateFormat(String inputDateFormat, String value, String outputDateFormat) {
        String formattedDate = null;
        try {
            SimpleDateFormat format = new SimpleDateFormat(inputDateFormat);
            Date newDate = format.parse(value);

            format = new SimpleDateFormat(outputDateFormat);
            formattedDate = format.format(newDate);
        } catch (Exception e) {

        }

        return formattedDate;
    }
}
