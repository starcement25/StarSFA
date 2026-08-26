    package com.forcepower.acedns.new_activity.sitelead.helper;
    import android.os.Build;

    import androidx.annotation.RequiresApi;

    import java.time.LocalDate;
    import java.time.Month;
    import java.time.format.DateTimeFormatter;
    import java.util.ArrayList;
    import java.util.List;

    public class FYMonthList {

        /**
         * Returns month-year strings for the given FY.
         * Indian FY runs April → March.
         *
         * @param startYear  the year in which the FY starts (e.g. 2025 for FY 2025-26)
         * @return list like ["April, 2025", "May, 2025", ..., "March, 2026"]
         */
        @RequiresApi(api = Build.VERSION_CODES.O)
        public static List<String> getMonthsForFY(int startYear) {
            List<String> months = new ArrayList<>();
            DateTimeFormatter fmt = DateTimeFormatter.ofPattern("MMMM, yyyy");

            LocalDate date = LocalDate.of(startYear, Month.APRIL, 1);
            for (int i = 0; i < 12; i++) {
                months.add(date.format(fmt));
                date = date.plusMonths(1);
            }
            return months;
        }

        /**
         * Returns month-year strings for the CURRENT and PREVIOUS financial year.
         * Determines the current FY based on today's date.
         */
        @RequiresApi(api = Build.VERSION_CODES.O)
        public static List<String> getCurrentFYMonths() {
            int startYear = getCurrentFYStartYear();
            return getMonthsForFY(startYear);
        }
        @RequiresApi(api = Build.VERSION_CODES.O)
        public static List<String> getPreviousFYMonths() {
            int startYear = getCurrentFYStartYear() - 1;
            return getMonthsForFY(startYear);
        }

        /** If month is Jan–Mar, FY started the previous calendar year. */
        @RequiresApi(api = Build.VERSION_CODES.O)
        private static int getCurrentFYStartYear() {
            LocalDate today = LocalDate.now();
            return (today.getMonthValue() >= 4)
                    ? today.getYear()
                    : today.getYear() - 1;
        }
        @RequiresApi(api = Build.VERSION_CODES.O)
        public static void main(String[] args) {
            System.out.println("Current FY:");
            getCurrentFYMonths().forEach(System.out::println);

            System.out.println("\nPrevious FY:");
            getPreviousFYMonths().forEach(System.out::println);
        }
        @RequiresApi(api = Build.VERSION_CODES.O)
        public static List<String> getCurrentAndPreviousFYMonths() {
            int currentFYStart = getCurrentFYStartYear();

            List<String> combined = new ArrayList<>();
            combined.addAll(getMonthsForFY(currentFYStart - 1)); // previous FY first
            combined.addAll(getMonthsForFY(currentFYStart));      // then current FY
            return combined;
        }
    }