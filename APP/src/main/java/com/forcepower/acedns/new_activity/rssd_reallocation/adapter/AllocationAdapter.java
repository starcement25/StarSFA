package com.forcepower.acedns.new_activity.rssd_reallocation.adapter;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.rssd_reallocation.data.DataSet;

import java.util.ArrayList;

public class AllocationAdapter extends RecyclerView.Adapter<AllocationAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<DataSet> list;
    private final OnActionClickListener listener;

    public interface OnActionClickListener {
        void clickApproved(DataSet item, int position);
        void clickRejected(DataSet item, int position);
    }

    public AllocationAdapter(Context context, ArrayList<DataSet> list, OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener=listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView orderNoText, orderDateText, invoiceNoText, invoiceDateText, productNameText, quantityText, assignedRSARCodeText, assignedRSARNameText, changeToRSARCodeText,
                changeToRSARNameText, requestDealerCodeText, requestDealerNameText;
        LinearLayout buttonLayout;
        Button rejectBtn, approveBtn;

        public ViewHolder(View itemView) {
            super(itemView);
            orderNoText = itemView.findViewById(R.id.orderNoText);
            orderDateText = itemView.findViewById(R.id.orderDateText);
            invoiceNoText = itemView.findViewById(R.id.invoiceNoText);
            invoiceDateText = itemView.findViewById(R.id.invoiceDateText);
            productNameText = itemView.findViewById(R.id.productNameText);
            quantityText = itemView.findViewById(R.id.quantityText);
            assignedRSARCodeText = itemView.findViewById(R.id.assignedRSARCodeText);
            assignedRSARNameText = itemView.findViewById(R.id.assignedRSARNameText);
            changeToRSARCodeText = itemView.findViewById(R.id.changeToRSARCodeText);
            changeToRSARNameText = itemView.findViewById(R.id.changeToRSARNameText);
            requestDealerCodeText = itemView.findViewById(R.id.requestDealerCodeText);
            requestDealerNameText = itemView.findViewById(R.id.requestDealerNameText);
            buttonLayout = itemView.findViewById(R.id.buttonLayout);
            rejectBtn = itemView.findViewById(R.id.rejectBtn);
            approveBtn = itemView.findViewById(R.id.approveBtn);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_rssd_reallocation_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint({"SetTextI18n","RecyclerView"})
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        DataSet item = list.get(position);
        if (holder.buttonLayout != null) {
            holder.buttonLayout.setVisibility(GONE);
        }

        holder.orderNoText.setText(item.getOrderNo());
        holder.orderDateText.setText(item.getOrderDate());
        holder.invoiceNoText.setText(item.getInvoiceNo());
        holder.invoiceDateText.setText(item.getInvoiceDate());
        holder.productNameText.setText(item.getProductName());
        holder.quantityText.setText(item.getQty());
        holder.assignedRSARCodeText.setText(item.getOldRssdCode());
        holder.assignedRSARNameText.setText(item.getOldRssdName());
        holder.changeToRSARCodeText.setText(item.getNewRssdCode());
        holder.changeToRSARNameText.setText(item.getNewRssdName());
        holder.requestDealerCodeText.setText(item.getRequestByCode());
        holder.requestDealerNameText.setText(item.getRequestByName());
        if ("pending".equalsIgnoreCase(item.getStatus()) && holder.buttonLayout != null)
            holder.buttonLayout.setVisibility(VISIBLE);
        holder.rejectBtn.setOnClickListener(v -> listener.clickRejected(list.get(position), position));
        holder.approveBtn.setOnClickListener(v -> listener.clickApproved(list.get(position), position));
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}
