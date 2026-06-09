package com.forcepower.acedns.new_activity.credit_limit.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.credit_limit.dataset.CreditLimitDataSet;

import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Locale;

public class CreditLimitAdapter extends RecyclerView.Adapter<CreditLimitAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<CreditLimitDataSet> list;

    public CreditLimitAdapter(Context context, ArrayList<CreditLimitDataSet> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView customerCode, customerName, creditLimit, utilisedCreditLimit, availableCreditLimit;

        public ViewHolder(View itemView) {
            super(itemView);
            customerCode = itemView.findViewById(R.id.customerCode);
            customerName = itemView.findViewById(R.id.customerName);
            creditLimit = itemView.findViewById(R.id.creditLimit);
            utilisedCreditLimit = itemView.findViewById(R.id.utilisedCreditLimit);
            availableCreditLimit = itemView.findViewById(R.id.availableCreditLimit);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context)
                .inflate(R.layout.customer_wise_credit_limit_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        CreditLimitDataSet item = list.get(position);

        holder.customerCode.setText(item.getCustomerCode());
        holder.customerName.setText(item.getCustomerName());
        holder.creditLimit.setText("₹ " + NumberFormat.getNumberInstance(new Locale("en", "IN")).format(Integer.parseInt(item.getCreditLimit())));
        holder.utilisedCreditLimit.setText("₹ " + NumberFormat.getNumberInstance(new Locale("en", "IN")).format(Integer.parseInt(item.getUtilisedCL())));
        holder.availableCreditLimit.setText("₹ " + NumberFormat.getNumberInstance(new Locale("en", "IN")).format(Integer.parseInt(item.getAvailableCL())));
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}