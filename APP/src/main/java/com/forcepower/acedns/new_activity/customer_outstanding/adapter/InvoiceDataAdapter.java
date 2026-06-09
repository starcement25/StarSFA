package com.forcepower.acedns.new_activity.customer_outstanding.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.graphics.drawable.GradientDrawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.DataSet;
import com.forcepower.acedns.new_activity.customer_outstanding.dataset.InvoiceDataSet;

import java.util.ArrayList;

public class InvoiceDataAdapter extends RecyclerView.Adapter<InvoiceDataAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<InvoiceDataSet> list;
    private final ArrayList<DataSet> list1;

    public InvoiceDataAdapter(Context context, ArrayList<InvoiceDataSet> list, ArrayList<DataSet> list1) {
        this.context = context;
        this.list = list;
        this.list1 = list1;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        LinearLayout iconLayout, statusLayout;
        ImageView iconImage;
        TextView invoiceNumber, invoiceDate, invoiceStatus, invoiceAmount;

        public ViewHolder(View itemView) {
            super(itemView);
            iconLayout = itemView.findViewById(R.id.iconLayout);
            statusLayout = itemView.findViewById(R.id.statusLayout);
            iconImage = itemView.findViewById(R.id.iconImage);
            invoiceNumber = itemView.findViewById(R.id.invoiceNumber);
            invoiceDate = itemView.findViewById(R.id.invoiceDate);
            invoiceStatus = itemView.findViewById(R.id.invoiceStatus);
            invoiceAmount = itemView.findViewById(R.id.invoiceAmount);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.invoice_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        InvoiceDataSet item = list.get(position);

        holder.invoiceAmount.setText("₹ " + item.getInvoiceAmount());
        holder.invoiceDate.setText(item.getInvoiceDate());
        holder.invoiceNumber.setText(item.getInvoiceNo());
        holder.invoiceStatus.setText("Age : " + item.getInvoicePendingDays() + " Days");
        String color;
        if (item.getInvoicePendingDays() <= list1.get(1).getEndDate()) {
            color = "2ECC71";
        } else if (item.getInvoicePendingDays() <= list1.get(2).getEndDate()) {
            color = "73D467";
        } else if (item.getInvoicePendingDays() <= list1.get(3).getEndDate()) {
            color = "B8DF46";
        } else if (item.getInvoicePendingDays() <= list1.get(4).getEndDate()) {
            color = "F4C430";
        } else if (item.getInvoicePendingDays() <= list1.get(5).getEndDate()) {
            color = "F4A020";
        } else if (item.getInvoicePendingDays() <= list1.get(6).getEndDate()) {
            color = "F47C20";
        } else if (item.getInvoicePendingDays() <= list1.get(7).getEndDate()) {
            color = "F05050";
        } else if (item.getInvoicePendingDays() <= list1.get(9).getEndDate()) {
            color = "D63030";
        } else {
            color = "8B0000";
        }

        holder.iconLayout.setBackground(getBackgroundDrawable("#FF" + color));
        holder.statusLayout.setBackground(getBackgroundDrawable("#FF" + color));
        holder.iconImage.setColorFilter(Color.parseColor("#FFFFFFFF"));
        holder.invoiceStatus.setTextColor(Color.parseColor("#FFFFFFFF"));
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    private GradientDrawable getBackgroundDrawable(String color) {
        GradientDrawable drawable = new GradientDrawable();
        drawable.setCornerRadius(5 * context.getResources().getDisplayMetrics().density); // 5dp
        drawable.setColor(Color.parseColor(color));
        return drawable;
    }
}