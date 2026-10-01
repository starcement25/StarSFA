package com.forcepower.acedns.new_activity.rssd_reallocation.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.rssd_reallocation.data.PendingDataSet;
import com.forcepower.acedns.new_activity.rssd_reallocation.data.PendingOrderDataSet;

import java.util.ArrayList;

public class PendingAllocationAdapter extends RecyclerView.Adapter<PendingAllocationAdapter.ViewHolder> {
    public interface OnActionClickListener {
        void clickApproved(PendingDataSet item);
        void clickRejected(PendingDataSet item);
    }

    private final Context context;
    private final ArrayList<PendingOrderDataSet> orders;
    private final OnActionClickListener listener;
    private int expandedPosition = RecyclerView.NO_POSITION;

    public PendingAllocationAdapter(Context context, ArrayList<PendingOrderDataSet> orders,
                                    OnActionClickListener listener) {
        this.context = context;
        this.orders = orders;
        this.listener = listener;
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        final TextView orderHeader, orderNo, orderDate, invoiceNo, invoiceDate, productName,
                quantity, requestByName, requestByCode;
        final LinearLayout orderDetailsLayout, allocationContainer;

        ViewHolder(View view) {
            super(view);
            orderHeader = view.findViewById(R.id.orderHeaderText);
            orderDetailsLayout = view.findViewById(R.id.orderDetailsLayout);
            orderNo = view.findViewById(R.id.orderNoText);
            orderDate = view.findViewById(R.id.orderDateText);
            invoiceNo = view.findViewById(R.id.invoiceNoText);
            invoiceDate = view.findViewById(R.id.invoiceDateText);
            productName = view.findViewById(R.id.productNameText);
            quantity = view.findViewById(R.id.quantityText);
            requestByName = view.findViewById(R.id.requestByNameText);
            requestByCode = view.findViewById(R.id.requestByCodeText);
            allocationContainer = view.findViewById(R.id.allocationContainer);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        return new ViewHolder(LayoutInflater.from(context)
                .inflate(R.layout.item_pending_rssd_reallocation_order, parent, false));
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        PendingOrderDataSet order = orders.get(position);
        holder.orderNo.setText(order.getOrderNo());
        holder.orderDate.setText(order.getOrderDate());
        holder.invoiceNo.setText(order.getInvoiceNo());
        holder.invoiceDate.setText(order.getInvoiceDate());
        holder.productName.setText(order.getProductName());
        holder.quantity.setText(order.getQty());
        holder.requestByName.setText(order.getRequestByName());
        holder.requestByCode.setText(order.getRequestByCode());

        boolean expanded = position == expandedPosition;
        holder.allocationContainer.setVisibility(expanded ? View.VISIBLE : View.GONE);
        holder.orderHeader.setText(expanded ? "Order Details  ▲" : "Order Details  ▼");
        holder.orderDetailsLayout.setOnClickListener(v -> {
            int adapterPosition = holder.getAdapterPosition();
            if (adapterPosition == RecyclerView.NO_POSITION) return;
            int previousPosition = expandedPosition;
            expandedPosition = adapterPosition == expandedPosition
                    ? RecyclerView.NO_POSITION : adapterPosition;
            if (previousPosition != RecyclerView.NO_POSITION) notifyItemChanged(previousPosition);
            if (expandedPosition != RecyclerView.NO_POSITION) notifyItemChanged(expandedPosition);
        });
        holder.allocationContainer.removeAllViews();

        for (PendingDataSet allocation : order.getPendingDataSets()) {
            View row = LayoutInflater.from(context).inflate(
                    R.layout.item_pending_rssd_reallocation_detail, holder.allocationContainer, false);
            ((TextView) row.findViewById(R.id.assignedRSARCodeText)).setText(allocation.getOldRssdCode());
            ((TextView) row.findViewById(R.id.assignedRSARNameText)).setText(allocation.getOldRssdName());
            ((TextView) row.findViewById(R.id.changeToRSARCodeText)).setText(allocation.getNewRssdCode());
            ((TextView) row.findViewById(R.id.changeToRSARNameText)).setText(allocation.getNewRssdName());
            ((TextView) row.findViewById(R.id.allocationQtyText)).setText(allocation.getQty());
            row.findViewById(R.id.rejectBtn).setOnClickListener(v -> listener.clickRejected(allocation));
            row.findViewById(R.id.approveBtn).setOnClickListener(v -> listener.clickApproved(allocation));
            holder.allocationContainer.addView(row);
        }
    }

    @Override
    public int getItemCount() {
        return orders.size();
    }
}
