package com.forcepower.acedns.new_activity.customer_outstanding.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.graphics.drawable.GradientDrawable;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.GraphDataSet;

import java.util.ArrayList;

public class AgeWiseOutstandingAdapter extends RecyclerView.Adapter<AgeWiseOutstandingAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<GraphDataSet> list;

    public AgeWiseOutstandingAdapter(Context context, ArrayList<GraphDataSet> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        LinearLayout colorCodeLayout;
        TextView titleText, percentageText, valueText;
        View barFill;
        FrameLayout barTrack;

        public ViewHolder(View itemView) {
            super(itemView);
            colorCodeLayout = itemView.findViewById(R.id.colorCodeLayout);
            titleText = itemView.findViewById(R.id.titleText);
            percentageText = itemView.findViewById(R.id.percentageText);
            valueText = itemView.findViewById(R.id.valueText);
            barFill = itemView.findViewById(R.id.barFill);
            barTrack = itemView.findViewById(R.id.barTrack); // add id to FrameLayout
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context)
                .inflate(R.layout.age_wise_outstanding_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        GraphDataSet item = list.get(position);

        // Dot color
        holder.colorCodeLayout.setBackgroundColor(Color.parseColor(item.getColorCode()));

        // Make dot circular
        GradientDrawable dot = new GradientDrawable();
        dot.setShape(GradientDrawable.OVAL);
        dot.setColor(Color.parseColor(item.getColorCode()));
        holder.colorCodeLayout.setBackground(dot);

        // Texts
        holder.titleText.setText(item.getTitle());
        holder.percentageText.setText(item.getPercentage());
        holder.valueText.setText(item.getAmount());

        // Bar fill — set width proportional to percentage after layout is ready
        holder.barTrack.post(() -> {
            int trackWidth = holder.barTrack.getWidth();
            int fillWidth;
            try {
                fillWidth = (int) (trackWidth * Float.parseFloat(item.getPercentage().replace("%", "")) / 100.0f);
            } catch (Exception e) {
                fillWidth = 0;
                Log.d("TAG", "_DOWNLOAD_ onBindViewHolder: " + e.getMessage());
            }


            ViewGroup.LayoutParams params = holder.barFill.getLayoutParams();
            params.width = fillWidth;
            holder.barFill.setLayoutParams(params);

            // Bar color
            GradientDrawable barDrawable = new GradientDrawable();
            barDrawable.setColor(Color.parseColor(item.getColorCode()));
            barDrawable.setCornerRadius(8f);
            holder.barFill.setBackground(barDrawable);
        });
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}