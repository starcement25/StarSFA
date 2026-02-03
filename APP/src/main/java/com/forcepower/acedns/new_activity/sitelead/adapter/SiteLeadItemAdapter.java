package com.forcepower.acedns.new_activity.sitelead.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.sitelead.dataset.SiteLeadDataSet;

import java.util.ArrayList;

public class SiteLeadItemAdapter extends RecyclerView.Adapter<SiteLeadItemAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<SiteLeadDataSet> list;
    private final OnActionClickListener listener;

    public interface OnActionClickListener {
        void onDetailsClicked(SiteLeadDataSet item, int position);
    }

    public SiteLeadItemAdapter(Context context, ArrayList<SiteLeadDataSet> list, OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView customerName, contactNo, address, dealerName, dealerCode, visitType, productName, noOfBag, requestDate, status;
        Button viewDetailsBtn;

        public ViewHolder(View itemView) {
            super(itemView);
            customerName = itemView.findViewById(R.id.customerName);
            contactNo = itemView.findViewById(R.id.contactNo);
            address = itemView.findViewById(R.id.address);
            dealerName = itemView.findViewById(R.id.dealerName);
            dealerCode = itemView.findViewById(R.id.dealerCode);
            visitType = itemView.findViewById(R.id.visitType);
            productName = itemView.findViewById(R.id.productName);
            noOfBag = itemView.findViewById(R.id.noOfBag);
            requestDate = itemView.findViewById(R.id.requestDate);
            status = itemView.findViewById(R.id.status);
            viewDetailsBtn = itemView.findViewById(R.id.viewDetailsBtn);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_site_lead, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        SiteLeadDataSet item = list.get(position);

        holder.customerName.setText(toTitleCase(item.getCustomerName()));
        holder.contactNo.setText(toTitleCase(item.getCustomerPhoneNo()));
        holder.address.setText(toTitleCase(item.getAddress()));
        holder.dealerName.setText(toTitleCase(item.getCounterName()));
        holder.dealerCode.setText(toTitleCase(item.getCounterCode()));
        holder.visitType.setText(toTitleCase(item.getVisitType()));
        holder.productName.setText(toTitleCase(item.getSelectProduct()));
        holder.noOfBag.setText(toTitleCase(item.getNoOfBagsOrdered()));
        holder.requestDate.setText(toTitleCase(item.getRequestedDate()));
        holder.status.setText(toTitleCase(item.getApprovalStatus()));

//        if(!item.getApprovalStatus().equalsIgnoreCase("pending")){
//            holder.viewDetailsBtn.setVisibility(View.GONE);
//        }else{
//            holder.viewDetailsBtn.setVisibility(View.VISIBLE);
//        }
        holder.viewDetailsBtn.setOnClickListener(v -> {
            if (listener != null) {
                listener.onDetailsClicked(item, position);
            }
        });
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
