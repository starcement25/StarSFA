package com.forcepower.acedns.new_activity.target_achievement;

import android.animation.Animator;
import android.animation.ValueAnimator;
import android.annotation.SuppressLint;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.util.Pair;
import android.view.Gravity;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.AccelerateDecelerateInterpolator;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.androidplot.Plot;
import com.androidplot.ui.SeriesRenderer;
import com.androidplot.util.PixelUtils;
import com.androidplot.xy.BarFormatter;
import com.androidplot.xy.BarRenderer;
import com.androidplot.xy.BoundaryMode;
import com.androidplot.xy.ScalingXYSeries;
import com.androidplot.xy.StepMode;
import com.androidplot.xy.XYGraphWidget;
import com.androidplot.xy.XYPlot;
import com.androidplot.xy.XYSeries;
import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.bean.SelfAppraisalDetailsCustomerWise;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.new_activity.target_achievement.adapter.MonthWiseDataSetAdapter;
import com.forcepower.acedns.new_activity.target_achievement.adapter.ShowDataSetAdapter;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.DataSet;
import com.forcepower.acedns.new_activity.target_achievement.dataset.MonthWiseDataSet;

import java.text.FieldPosition;
import java.text.Format;
import java.text.ParsePosition;
import java.util.ArrayList;

import com.androidplot.xy.SimpleXYSeries;

import java.util.List;

public class EmployeeTargetAchievementActivity extends AceDnsParentActivity implements OnClickListener {
    Context mContext;

    private Button backButton;
    private LinearLayout selectYearLayoutButton, selectCustomerLayoutButton;
    private TextView selectYearText, selectCustomerText;
    private LinearLayout firstIndicator, secondIndicator;
    private TextView firstIndicatorText, secondIndicatorText;
    private RecyclerView monthList;

    ArrayList<DataSet> dropdownList = new ArrayList<>();
    ArrayList<DataSet> customerList = new ArrayList<>();
    ArrayList<SelfAppraisalDetailsCustomerWise> monthWiseDataSet = new ArrayList<>();
    ArrayList<MonthWiseDataSet> showMonthWiseDataSet = new ArrayList<>();

    AceDnsDatabase mAceDnsDatabase;
    MonthWiseDataSetAdapter adapter;

    int selectMenu = 1;
    String selectCustomerCode = "all";

    XYPlot plot;

    private Pair<Integer, XYSeries> selection;
    private MyBarFormatter selectionFormatter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_employee_target_achievement);

        mContext = EmployeeTargetAchievementActivity.this;
        mAceDnsDatabase = new AceDnsDatabase(mContext);

        init();
    }

    @Override
    public void onClick(View v) {
        if (v == backButton) {
            finish();
        }
        if (v == selectYearLayoutButton) {
            show_list_data_dialog(dropdownList, "year", "Select Your Option");
        }
        if (v == selectCustomerLayoutButton) {
            show_list_data_dialog(customerList, "customer", "Select Customer");
        }
    }

    @SuppressLint("SetTextI18n")
    private void init() {
        backButton = findViewById(R.id.backButton);
        backButton.setOnClickListener(this);

        selectYearLayoutButton = findViewById(R.id.selectYearLayoutButton);
        selectYearLayoutButton.setOnClickListener(this);
        selectYearText = findViewById(R.id.selectYearText);

        selectCustomerLayoutButton = findViewById(R.id.selectCustomerLayoutButton);
        selectCustomerLayoutButton.setOnClickListener(this);
        selectCustomerText = findViewById(R.id.selectCustomerText);

        firstIndicator = findViewById(R.id.firstIndicator);
        firstIndicatorText = findViewById(R.id.firstIndicatorText);
        firstIndicator.setBackground(ContextCompat.getDrawable(mContext, R.drawable.graph_bar_color_current_fy_target));
        firstIndicatorText.setText("Target (MT)");
        secondIndicator = findViewById(R.id.secondIndicator);
        secondIndicatorText = findViewById(R.id.secondIndicatorText);
        secondIndicator.setBackground(ContextCompat.getDrawable(mContext, R.drawable.graph_bar_color_current_fy_achievements));
        secondIndicatorText.setText("Achievement (MT)");

        plot = findViewById(R.id.selfAppraisalBarChart);

        monthList = findViewById(R.id.monthList);
        monthList.setLayoutManager(new LinearLayoutManager(this));

        setupBarChart();
        getDataList();
    }

    @SuppressLint("SetTextI18n")
    public void show_list_data_dialog(ArrayList<DataSet> dataSet, String category, String titleValue) {
        try {
            final Dialog mDestinationDialog = new Dialog(this, R.style.MyMaterialTheme);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_from_list1);
            mDestinationDialog.setCancelable(true);

            TextView title = mDestinationDialog.findViewById(R.id.title);
            title.setText(titleValue);
            ImageView imageView1 = mDestinationDialog.findViewById(R.id.imageView1);
            imageView1.setOnClickListener(view -> mDestinationDialog.dismiss());
            ListView dialogList = mDestinationDialog.findViewById(R.id.list);
            Button btn_cncl = mDestinationDialog.findViewById(R.id.btn_cncl);
            btn_cncl.setVisibility(View.GONE);

            final ShowDataSetAdapter pAdapter = new ShowDataSetAdapter(this, R.layout.list_item_single_radio, dataSet);
            dialogList.setAdapter(pAdapter);

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                mDestinationDialog.dismiss();
                switch (category) {
                    case "year":
                        selectMenu = Integer.parseInt(dataSet.get(position).getId());
                        selectYearText.setText(dataSet.get(position).getValue());
                        for (int i = 0; i < dropdownList.size(); i++) {
                            dropdownList.get(i).setSelect(dropdownList.get(i).getId().equalsIgnoreCase(dataSet.get(position).getId()));
                        }
                        filterDataList();
                        break;
                    case "customer":
                        selectCustomerCode = dataSet.get(position).getId();
                        selectCustomerText.setText(dataSet.get(position).getValue());
                        for (int i = 0; i < customerList.size(); i++) {
                            customerList.get(i).setSelect(customerList.get(i).getId().equalsIgnoreCase(dataSet.get(position).getId()));
                        }
                        filterDataList();
                        break;
                }
            });

            mDestinationDialog.show();
        } catch (Exception ignored) {
        }
    }

    private void getDataList() {
        // year dropdown
        String[][] data = {
                {"1", "Current FY"},
                {"2", "Previous FY"},
                {"3", "Current FY vs Previous FY"}
        };

        for (String[] entry : data) {
            dropdownList.add(new DataSet(entry[0], entry[1], false));
        }

        // customer dropdown
        customerList.clear();
        customerList = mAceDnsDatabase.getDistinctCustomerList();

        // Month wise Data
        try {
            monthWiseDataSet = mAceDnsDatabase.getGraphDataTotalGraphData(selectCustomerCode);
            showMonthWiseDataSet.clear();
            for (int i = 0; i < monthWiseDataSet.size(); i++) {
                MonthWiseDataSet obj = new MonthWiseDataSet(monthWiseDataSet.get(i).getmonth(), monthWiseDataSet.get(i).gettarget(), monthWiseDataSet.get(i).getachievement(), 1);
                showMonthWiseDataSet.add(obj);
            }
            setDataInList();
        } catch (Exception e) {
            Log.d("TAG", "getDataList: " + e.getMessage());
        }

    }

    private void setDataInList() {
        ArrayList<MonthWiseDataSet> temp = new ArrayList<>();
        for (int i = 0; i < showMonthWiseDataSet.size(); i++) {
            float target = Float.parseFloat(showMonthWiseDataSet.get(i).getTarget());
            float achi = Float.parseFloat(showMonthWiseDataSet.get(i).getAchievement());
            if (target != 0 || achi != 0)
                temp.add(showMonthWiseDataSet.get(i));
        }
        adapter = new MonthWiseDataSetAdapter(this, temp);
        monthList.setAdapter(adapter);
        updateBarChart();
    }

    @SuppressLint("SetTextI18n")
    private void filterDataList() {
        monthWiseDataSet = mAceDnsDatabase.getGraphDataTotalGraphData(selectCustomerCode);
        if (selectMenu == 1) {
            firstIndicator.setBackground(ContextCompat.getDrawable(mContext, R.drawable.graph_bar_color_current_fy_target));
            firstIndicatorText.setText("Target (MT)");
            secondIndicator.setBackground(ContextCompat.getDrawable(mContext, R.drawable.graph_bar_color_current_fy_achievements));
            secondIndicatorText.setText("Achievement (MT)");
            try {
                showMonthWiseDataSet.clear();
                for (int i = 0; i < monthWiseDataSet.size(); i++) {
                    MonthWiseDataSet obj = new MonthWiseDataSet(monthWiseDataSet.get(i).getmonth(), monthWiseDataSet.get(i).gettarget(), monthWiseDataSet.get(i).getachievement(), 1);
                    showMonthWiseDataSet.add(obj);
                }
                setDataInList();
            } catch (Exception e) {
                Log.d("TAG", "getDataList: " + e.getMessage());
            }
        } else if (selectMenu == 2) {
            firstIndicator.setBackground(ContextCompat.getDrawable(mContext, R.drawable.graph_bar_color_previous_fy_target));
            firstIndicatorText.setText("Target (MT)");
            secondIndicator.setBackground(ContextCompat.getDrawable(mContext, R.drawable.graph_bar_color_previous_fy_achievements));
            secondIndicatorText.setText("Achievement (MT)");
            try {
                showMonthWiseDataSet.clear();
                for (int i = 0; i < monthWiseDataSet.size(); i++) {
                    MonthWiseDataSet obj = new MonthWiseDataSet(monthWiseDataSet.get(i).getmonth(), monthWiseDataSet.get(i).getPrivousTarget(), monthWiseDataSet.get(i).getPrevousAchievement(), 1);
                    showMonthWiseDataSet.add(obj);
                }
                setDataInList();
            } catch (Exception e) {
                Log.d("TAG", "getDataList: " + e.getMessage());
            }
        } else {
            firstIndicator.setBackground(ContextCompat.getDrawable(mContext, R.drawable.graph_bar_color_compare_previous_fy_achievements));
            firstIndicatorText.setText("Last Year\nAchievements (MT)");
            secondIndicator.setBackground(ContextCompat.getDrawable(mContext, R.drawable.graph_bar_color_compare_current_fy_achievements));
            secondIndicatorText.setText("Current Year\nAchievements (MT)");
            try {
                showMonthWiseDataSet.clear();
                for (int i = 0; i < monthWiseDataSet.size(); i++) {
                    MonthWiseDataSet obj = new MonthWiseDataSet(monthWiseDataSet.get(i).getmonth(), monthWiseDataSet.get(i).getPrevousAchievement(), monthWiseDataSet.get(i).getachievement(), 2);
                    showMonthWiseDataSet.add(obj);
                }
                setDataInList();
            } catch (Exception e) {
                Log.d("TAG", "getDataList: " + e.getMessage());
            }
        }
    }

    private void setupBarChart() {
        plot.clear();

        // White background
        plot.getBackgroundPaint().setColor(Color.WHITE);
        plot.getGraph().getBackgroundPaint().setColor(Color.WHITE);
        plot.getGraph().getGridBackgroundPaint().setColor(Color.WHITE);

        // Remove border
        plot.setBorderStyle(Plot.BorderStyle.NONE, null, null);

        // Hide legend & title
        plot.getLegend().setVisible(false);
        plot.setTitle("");

        // Grid lines - light gray
        plot.getGraph().getDomainGridLinePaint().setColor(Color.LTGRAY);
        plot.getGraph().getRangeGridLinePaint().setColor(Color.LTGRAY);

        // Axis paint
        plot.getGraph().getDomainOriginLinePaint().setColor(Color.BLACK);
        plot.getGraph().getRangeOriginLinePaint().setColor(Color.BLACK);

        // Margins for labels
        plot.getGraph().setMargins(50, 30, 40, 10);

        // Range starts from 0
        plot.setRangeLowerBoundary(0, BoundaryMode.FIXED);
        plot.setRangeStep(StepMode.SUBDIVIDE, 6);
    }

    @SuppressLint({"ResourceAsColor", "ClickableViewAccessibility"})
    private void updateBarChart() {
        plot.clear();

        if (showMonthWiseDataSet == null || showMonthWiseDataSet.isEmpty()) return;

        int size = showMonthWiseDataSet.size();

        // ✅ X values like reference code (0,1,2,3...)
        final ArrayList<Number> xValues = new ArrayList<>();
        final ArrayList<String> xLabels = new ArrayList<>();
        final List<Number> targetList = new ArrayList<>();
        final List<Number> achievementList = new ArrayList<>();

        xValues.add(0);
        xLabels.add("");
        targetList.add(0);
        achievementList.add(0);

        for (int i = 0; i < size; i++) {
            MonthWiseDataSet item = showMonthWiseDataSet.get(i);
            xValues.add(i + 1);
            xLabels.add(getMonthShortName(item.getMonthName()));

            double t = 0, a = 0;
            try {
                t = Double.parseDouble(item.getTarget() != null ? item.getTarget() : "0");
            } catch (Exception ignored) {
            }
            try {
                a = Double.parseDouble(item.getAchievement() != null ? item.getAchievement() : "0");
            } catch (Exception ignored) {
            }
            targetList.add(t);
            achievementList.add(a);
        }

        xValues.add(size + 1);
        xLabels.add("");
        targetList.add(0);
        achievementList.add(0);

        // Colors
        int targetColor, achieveColor;
        if (selectMenu == 1) {
            targetColor = ContextCompat.getColor(mContext, R.color.graph_target_current);
            achieveColor = ContextCompat.getColor(mContext, R.color.graph_achieve_current);
        } else if (selectMenu == 2) {
            targetColor = ContextCompat.getColor(mContext, R.color.graph_target_previous);
            achieveColor = ContextCompat.getColor(mContext, R.color.graph_achieve_previous);
        } else {
            targetColor = ContextCompat.getColor(mContext, R.color.graph_compare_previous);
            achieveColor = ContextCompat.getColor(mContext, R.color.graph_compare_current);
        }

        // ✅ Formatters with margin like reference
        MyBarFormatter fmtTarget = new MyBarFormatter(targetColor, Color.TRANSPARENT);
        fmtTarget.setMarginLeft(PixelUtils.dpToPix(4));
        fmtTarget.setMarginRight(PixelUtils.dpToPix(4));

        MyBarFormatter fmtAchieve = new MyBarFormatter(achieveColor, Color.TRANSPARENT);
        fmtAchieve.setMarginLeft(PixelUtils.dpToPix(4));
        fmtAchieve.setMarginRight(PixelUtils.dpToPix(4));

        // ✅ Series with xValues like reference
        XYSeries series1 = new SimpleXYSeries(xValues, targetList, "Target");
        XYSeries series2 = new SimpleXYSeries(xValues, achievementList, "Achievement");

        // ✅ Domain & Range boundaries
        plot.setDomainBoundaries(0, series1.size(), BoundaryMode.AUTO);
        double boundary = 0;
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            boundary = Math.max(
                    targetList.stream().mapToDouble(Number::doubleValue).max().orElse(0),
                    achievementList.stream().mapToDouble(Number::doubleValue).max().orElse(0)
            );
        }
        plot.setRangeUpperBoundary(boundary * 1.3, BoundaryMode.FIXED);
        plot.setRangeLowerBoundary(0, BoundaryMode.FIXED);
        plot.setDomainStep(StepMode.INCREMENT_BY_VAL, 1);
        plot.setRangeStep(StepMode.INCREMENT_BY_VAL, boundary / 3);

        // ✅ ScalingXYSeries for animation like reference
        final ScalingXYSeries scalingSeries1 = new ScalingXYSeries(series1, 0, ScalingXYSeries.Mode.Y_ONLY);
        final ScalingXYSeries scalingSeries2 = new ScalingXYSeries(series2, 0, ScalingXYSeries.Mode.Y_ONLY);

        plot.addSeries(scalingSeries1, fmtTarget);
        plot.addSeries(scalingSeries2, fmtAchieve);

        // ✅ Bar renderer settings
        MyBarRenderer renderer = plot.getRenderer(MyBarRenderer.class);
        renderer.setBarOrientation(BarRenderer.BarOrientation.SIDE_BY_SIDE);
        renderer.setBarGroupWidth(BarRenderer.BarGroupWidthMode.FIXED_WIDTH, PixelUtils.dpToPix(40));

        // ✅ X axis labels
        plot.getGraph().getLineLabelStyle(XYGraphWidget.Edge.BOTTOM).setFormat(new Format() {
            @Override
            public StringBuffer format(Object obj, StringBuffer toAppendTo, FieldPosition pos) {
                int index = Math.round(((Number) obj).floatValue());
                if (index >= 0 && index < xLabels.size()) {
                    toAppendTo.append(xLabels.get(index));
                }
                return toAppendTo;
            }

            @Override
            public Object parseObject(String source, ParsePosition pos) {
                return null;
            }
        });

        // ✅ Y axis labels
        plot.getGraph().getLineLabelStyle(XYGraphWidget.Edge.LEFT).setFormat(new Format() {
            @SuppressLint("DefaultLocale")
            @Override
            public StringBuffer format(Object obj, StringBuffer toAppendTo, FieldPosition pos) {
                double val = ((Number) obj).doubleValue();
                if (val >= 1000) toAppendTo.append(String.format("%.0fK", val / 1000));
                else toAppendTo.append((int) val);
                return toAppendTo;
            }

            @Override
            public Object parseObject(String source, ParsePosition pos) {
                return null;
            }
        });

        // ✅ Animate bars growing up like reference
        ValueAnimator animator = ValueAnimator.ofFloat(0, 1);
        animator.setInterpolator(new AccelerateDecelerateInterpolator());
        animator.setDuration(1500);
        animator.addUpdateListener(valueAnimator -> {
            double scale = valueAnimator.getAnimatedFraction();
            scalingSeries1.setScale(scale);
            scalingSeries2.setScale(scale);
            plot.redraw();
        });
        animator.addListener(new Animator.AnimatorListener() {
            @Override
            public void onAnimationStart(@NonNull Animator a) {
            }

            @Override
            public void onAnimationEnd(@NonNull Animator a) {
                plot.redraw();
            }

            @Override
            public void onAnimationCancel(@NonNull Animator a) {
            }

            @Override
            public void onAnimationRepeat(@NonNull Animator a) {
            }
        });
        animator.start();

        plot.setOnTouchListener((v, event) -> {
            if (event.getAction() == MotionEvent.ACTION_UP) {
                // Convert touch X to domain (bar index)
                float touchX = event.getX();
                float plotLeft = plot.getGraph().getMarginLeft() + plot.getPaddingLeft();
                float plotRight = plot.getWidth() - plot.getGraph().getMarginRight() - plot.getPaddingRight();
                float plotWidth = plotRight - plotLeft;

                // Map touch X to data index
                float relativeX = touchX - plotLeft;
                float fraction = relativeX / plotWidth;
                int totalBars = showMonthWiseDataSet.size();
                int index = Math.round(fraction * (totalBars + 1)) - 1;

                if (index >= 0 && index < showMonthWiseDataSet.size()) {
                    MonthWiseDataSet item = showMonthWiseDataSet.get(index);
                    openDetailsPage(item);
                }
            }
            return true;
        });
    }

    private void openDetailsPage(MonthWiseDataSet item) {
        if (Integer.parseInt(item.getTarget()) != 0 && Integer.parseInt(item.getAchievement()) != 0) {
            Intent intent = new Intent(mContext, EmployeeTargetAchievementDetailsActivity.class);
            intent.putExtra("month_name", item.getMonthName());
            intent.putExtra("select_menu", selectMenu);
            startActivity(intent);
        }
    }

    private String getMonthShortName(String monthNumber) {
        String[] months = {"", "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"};
        try {
            int m = Integer.parseInt(monthNumber);
            if (m >= 1 && m <= 12) return months[m];
        } catch (Exception ignored) {
        }
        return monthNumber;
    }

    class MyBarFormatter extends BarFormatter {
        public MyBarFormatter(int fillColor, int borderColor) {
            super(fillColor, borderColor);
        }

        @Override
        public Class<? extends SeriesRenderer> getRendererClass() {
            return MyBarRenderer.class;
        }

        @Override
        public SeriesRenderer doGetRendererInstance(XYPlot plot) {
            return new MyBarRenderer(plot);
        }
    }

    class MyBarRenderer extends BarRenderer<MyBarFormatter> {
        public MyBarRenderer(XYPlot plot) {
            super(plot);
        }

        @Override
        public BarOrientation getBarOrientation() {
            return BarRenderer.BarOrientation.SIDE_BY_SIDE;
        }

        @Override
        public MyBarFormatter getFormatter(int index, XYSeries series) {
            if (selection != null &&
                    selection.second == series &&
                    selection.first == index) {
                return selectionFormatter;
            } else {
                return getFormatter(series);
            }
        }
    }
}