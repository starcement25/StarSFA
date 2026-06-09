package com.forcepower.acedns.new_activity.target_achievement.adapter;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.target_achievement.dataset.DetailsDataSet;

import java.util.ArrayList;

public class CustomerWiseDetailsAdapter extends RecyclerView.Adapter<CustomerWiseDetailsAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<DetailsDataSet> list;
    private final int type;

    public CustomerWiseDetailsAdapter(Context context, ArrayList<DetailsDataSet> list, int type) {
        this.context = context;
        this.list = list;
        this.type = type;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        LinearLayout titleLayout;
        TextView title1, title2, title3;
        LinearLayout valueLayout;
        TextView value1, value2, value3;

        public ViewHolder(View itemView) {
            super(itemView);
            titleLayout = itemView.findViewById(R.id.titleLayout);
            title1 = itemView.findViewById(R.id.title1);
            title2 = itemView.findViewById(R.id.title2);
            title3 = itemView.findViewById(R.id.title3);
            valueLayout = itemView.findViewById(R.id.valueLayout);
            value1 = itemView.findViewById(R.id.value1);
            value2 = itemView.findViewById(R.id.value2);
            value3 = itemView.findViewById(R.id.value3);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.customer_performance_details_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint({"SetTextI18n", "DefaultLocale"})
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        DetailsDataSet item = list.get(position);

        if (item.getIsTitle()) {
            holder.titleLayout.setVisibility(VISIBLE);
            holder.valueLayout.setVisibility(GONE);
            if (type != 3) {
                holder.title1.setText("Customer Name");
                holder.title2.setText("Prev Ach (MT)");
                holder.title3.setText("Curr Ach (MT)");
            } else {
                holder.title1.setText("Customer Name");
                holder.title2.setText("Target (MT)");
                holder.title3.setText("Ach (MT)");
            }
        } else {
            holder.titleLayout.setVisibility(GONE);
            holder.valueLayout.setVisibility(VISIBLE);
            if (Float.parseFloat(item.getTarget()) > Float.parseFloat(item.getAchievement())) {
                holder.value3.setTextColor(context.getResources().getColor(R.color.red));
            } else {
                holder.value3.setTextColor(context.getResources().getColor(R.color.authorize));
            }
            holder.value1.setText(item.getCustomerName());
            holder.value2.setText(String.format("%.2f", Float.parseFloat(item.getTarget())) + " MT");
            holder.value3.setText(String.format("%.2f", Float.parseFloat(item.getAchievement())) + " MT");
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}