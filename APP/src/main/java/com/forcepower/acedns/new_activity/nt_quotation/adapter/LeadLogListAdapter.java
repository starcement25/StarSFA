package com.forcepower.acedns.new_activity.nt_quotation.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.nt_quotation.dataset.LogDataSet;

import java.util.ArrayList;

public class LeadLogListAdapter extends RecyclerView.Adapter<LeadLogListAdapter.ViewHolder>{
    private final Context context;
    private final ArrayList<LogDataSet> list;

    public LeadLogListAdapter(Context context, ArrayList<LogDataSet> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView titleText, valueText;

        public ViewHolder(View itemView) {
            super(itemView);
            titleText = itemView.findViewById(R.id.titleText);
            valueText = itemView.findViewById(R.id.valueText);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_lead_log_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        LogDataSet item = list.get(position);

        holder.titleText.setText(item.getTitle());
        holder.valueText.setText(item.getValue());

    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    private String toTitleCase(String input) {
        if (input == null || input.isEmpty()) return input;

        String[] words = input.toLowerCase().trim().split("\\s+");
        StringBuilder titleCase = new StringBuilder();

        for (String word : words) {
            if (!word.isEmpty()) {
                titleCase.append(Character.toUpperCase(word.charAt(0)))
                        .append(word.substring(1))
                        .append(" ");
            }
        }
        return titleCase.toString().trim();
    }
}