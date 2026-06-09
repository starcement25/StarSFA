package com.forcepower.acedns.new_activity.target_achievement.adapter;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.target_achievement.dataset.MonthWiseDataSet;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class MonthWiseDataSetAdapter extends RecyclerView.Adapter<MonthWiseDataSetAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<MonthWiseDataSet> list;
    private static final List<String> monthNameList = new ArrayList<>(Arrays.asList("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"));

    public MonthWiseDataSetAdapter(Context context, ArrayList<MonthWiseDataSet> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        LinearLayout graphBackground, percentageBackground, differentBackground, showDetailsLayout;
        ImageView graphImage, percentageImage;
        TextView monthName, targetText, achievementText, percentageText, differentText, differentType;
        View progressFill, progressEmpty;

        public ViewHolder(View itemView) {
            super(itemView);
            graphBackground = itemView.findViewById(R.id.graphBackground);
            graphImage = itemView.findViewById(R.id.graphImage);
            monthName = itemView.findViewById(R.id.monthName);
            targetText = itemView.findViewById(R.id.targetText);
            achievementText = itemView.findViewById(R.id.achievementText);
            percentageBackground = itemView.findViewById(R.id.percentageBackground);
            percentageImage = itemView.findViewById(R.id.percentageImage);
            percentageText = itemView.findViewById(R.id.percentageText);
            differentBackground = itemView.findViewById(R.id.differentBackground);
            differentText = itemView.findViewById(R.id.differentText);
            differentType = itemView.findViewById(R.id.differentType);
            progressFill = itemView.findViewById(R.id.progressFill);
            progressEmpty = itemView.findViewById(R.id.progressEmpty);
            showDetailsLayout = itemView.findViewById(R.id.showDetailsLayout);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.month_wise_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint({"SetTextI18n", "DefaultLocale"})
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        MonthWiseDataSet item = list.get(position);

        if (item.getType() == 2 && Float.parseFloat(item.getAchievement()) == 0) {
            holder.percentageBackground.setVisibility(GONE);
            holder.showDetailsLayout.setVisibility(GONE);
        } else {
            holder.percentageBackground.setVisibility(VISIBLE);
            holder.showDetailsLayout.setVisibility(VISIBLE);
        }

        holder.monthName.setText(monthNameList.get(Integer.parseInt(item.getMonthName()) - 1));
        if (item.getType() == 2) {
            holder.targetText.setText("Prev. FY Ach. - " + String.format("%.2f",Double.parseDouble(item.getTarget())) + " MT");
            holder.achievementText.setText("Current FY Ach. - " + String.format("%.2f",Double.parseDouble(item.getAchievement())) + " MT");
        } else {
            holder.targetText.setText("Target - " + String.format("%.2f",Double.parseDouble(item.getTarget())) + " MT");
            holder.achievementText.setText("Ach - " + String.format("%.2f",Double.parseDouble(item.getAchievement())) + " MT");
        }
        holder.percentageText.setText(item.getPercentage() + " %");
        holder.differentText.setText(String.format("%.2f",Double.parseDouble(item.getDifferent())) + " MT ");
        holder.differentType.setText(item.getIsTradeUp() ? "Surplus" : "Shortfall");

        int color= Color.parseColor("#FFF44336");
        if (item.getPercentage().equalsIgnoreCase("---")) {
            try{
                double target = Double.parseDouble(item.getTarget());
                double achievement = Double.parseDouble(item.getAchievement());

                if (target == 0 && achievement == 0) {
                    // red — 0%
                    holder.graphBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                    holder.percentageBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                    holder.differentBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                    color = Color.parseColor("#FFF44336");
                    float percentage = 0f;
                    LinearLayout.LayoutParams fillParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, percentage);
                    LinearLayout.LayoutParams emptyParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, 100 - percentage);
                    holder.progressFill.setLayoutParams(fillParams);
                    holder.progressEmpty.setLayoutParams(emptyParams);
                    holder.progressFill.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background_bar));
                    holder.percentageImage.setImageResource(R.drawable.trend);

                } else if (target == 0 && achievement > 0) {
                    // green — 100%
                    holder.graphBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.green_background));
                    holder.percentageBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.green_background));
                    holder.differentBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.green_background));
                    color = Color.parseColor("#FF4CAF50");
                    float percentage = 100f;
                    LinearLayout.LayoutParams fillParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, percentage);
                    LinearLayout.LayoutParams emptyParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, 100 - percentage);
                    holder.progressFill.setLayoutParams(fillParams);
                    holder.progressEmpty.setLayoutParams(emptyParams);
                    holder.progressFill.setBackground(ContextCompat.getDrawable(context, R.drawable.green_background_bar));
                    holder.percentageImage.setImageResource(R.drawable.trup);

                } else {
                    // red fallback
                    holder.graphBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                    holder.percentageBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                    holder.differentBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                    color = Color.parseColor("#FFF44336");
                    float percentage = 0f;
                    LinearLayout.LayoutParams fillParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, percentage);
                    LinearLayout.LayoutParams emptyParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, 100 - percentage);
                    holder.progressFill.setLayoutParams(fillParams);
                    holder.progressEmpty.setLayoutParams(emptyParams);
                    holder.progressFill.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background_bar));
                    holder.percentageImage.setImageResource(R.drawable.trend);
                }
            } catch (Exception e) {
                Log.d("TAG", "_DOWNLOAD_ onBindViewHolder: "+e.getMessage()+"   :::   "+item.getAchievement());
            }
        } else {
            try {
                float percentage = Float.parseFloat(item.getPercentage());
                holder.graphBackground.setBackground(percentage >= 100 ? ContextCompat.getDrawable(context, R.drawable.green_background) : percentage > 50 ? ContextCompat.getDrawable(context, R.drawable.orange_background) : ContextCompat.getDrawable(context, R.drawable.red_background));
                holder.percentageBackground.setBackground(percentage >= 100 ? ContextCompat.getDrawable(context, R.drawable.green_background) : percentage > 50 ? ContextCompat.getDrawable(context, R.drawable.orange_background) : ContextCompat.getDrawable(context, R.drawable.red_background));
                holder.differentBackground.setBackground(percentage >= 100 ? ContextCompat.getDrawable(context, R.drawable.green_background) : percentage > 50 ? ContextCompat.getDrawable(context, R.drawable.orange_background) : ContextCompat.getDrawable(context, R.drawable.red_background));
                holder.progressFill.setBackground(percentage >= 100 ? ContextCompat.getDrawable(context, R.drawable.green_background_bar) : percentage > 50 ? ContextCompat.getDrawable(context, R.drawable.orange_background_bar) : ContextCompat.getDrawable(context, R.drawable.red_background_bar));
                holder.percentageImage.setImageResource(percentage >= 100 ? R.drawable.trup : R.drawable.trend);
                color = percentage >= 100 ? Color.parseColor("#FF4CAF50") : percentage > 50 ? Color.parseColor("#FFFF9800") : Color.parseColor("#FFF44336");
                LinearLayout.LayoutParams fillParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, percentage);
                LinearLayout.LayoutParams emptyParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, 100 - percentage);
                holder.progressFill.setLayoutParams(fillParams);
                holder.progressEmpty.setLayoutParams(emptyParams);
            } catch (Exception e) {
                holder.graphBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                holder.percentageBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                holder.differentBackground.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background));
                color = Color.parseColor("#FFF44336");
                float percentage = 0f;
                LinearLayout.LayoutParams fillParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, percentage);
                LinearLayout.LayoutParams emptyParams = new LinearLayout.LayoutParams(0, LinearLayout.LayoutParams.MATCH_PARENT, 100 - percentage);
                holder.progressFill.setLayoutParams(fillParams);
                holder.progressEmpty.setLayoutParams(emptyParams);
                holder.progressFill.setBackground(ContextCompat.getDrawable(context, R.drawable.red_background_bar));
                holder.percentageImage.setImageResource(R.drawable.trend);
            }
        }
        holder.graphImage.setColorFilter(color);
        holder.percentageImage.setColorFilter(color);
        holder.percentageText.setTextColor(color);
        holder.differentText.setTextColor(color);
        holder.differentType.setTextColor(color);
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}