package com.forcepower.acedns.new_activity.customer_outstanding.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.DataSet;

import java.util.ArrayList;

public class CategoryFilterAdaptor extends RecyclerView.Adapter<CategoryFilterAdaptor.ViewHolder> {
    private final Context context;
    private final ArrayList<DataSet> list;
    private final OnActionClickListener listener;

    public interface OnActionClickListener {
        void onClicked(DataSet item, int position);
    }

    public CategoryFilterAdaptor(Context context, ArrayList<DataSet> list, OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        LinearLayout layoutButton;
        TextView titleText;

        public ViewHolder(View itemView) {
            super(itemView);
            layoutButton = itemView.findViewById(R.id.layoutButton);
            titleText = itemView.findViewById(R.id.titleText);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.category_filter_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        DataSet item = list.get(position);
        holder.titleText.setText(item.getTitle());
        if (item.getIsSelect()) {
            holder.layoutButton.setBackground(ContextCompat.getDrawable(context, R.drawable.selected_item));
            holder.titleText.setTextColor(Color.parseColor("#FFFFFF"));
        } else {
            holder.layoutButton.setBackground(ContextCompat.getDrawable(context, R.drawable.unselected_item));
            holder.titleText.setTextColor(Color.parseColor("#000000"));
            holder.layoutButton.setOnClickListener(v -> {
                if (listener != null) {
                    listener.onClicked(item, position);
                }
            });
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}