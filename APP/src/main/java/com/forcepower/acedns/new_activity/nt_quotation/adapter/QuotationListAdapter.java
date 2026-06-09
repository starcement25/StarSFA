package com.forcepower.acedns.new_activity.nt_quotation.adapter;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.newDataBase.NewDatabaseForSiteLead;
import com.forcepower.acedns.newDataBase.data_set.LeadListMasterTableDataSet;
import com.forcepower.acedns.new_activity.nt_quotation.custom.LeadQuotationStatusTitleColor;

import java.util.ArrayList;

public class QuotationListAdapter extends RecyclerView.Adapter<QuotationListAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<LeadListMasterTableDataSet> list;
    private final OnActionClickListener listener;
    private final String user_type;

    NewDatabaseForSiteLead mNewDatabaseForSiteLead;

    public interface OnActionClickListener {
        void onPoDetailsFillupClicked(LeadListMasterTableDataSet item, int position);

        void onLostReasonClicked(LeadListMasterTableDataSet item, int position);

        void onCheckPoDetailsClicked(LeadListMasterTableDataSet item, int position);

        void onForwardToMisClicked(LeadListMasterTableDataSet item, int position);

        void onSendBackToSoClicked(LeadListMasterTableDataSet item, int position);
    }

    public QuotationListAdapter(Context context, ArrayList<LeadListMasterTableDataSet> list, String user_type, OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
        this.user_type = user_type;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        LinearLayout buttonLayout;
        TextView leadId, contactPersonName, contactNumber, soldToParty, shipToParty, nextVisitData, expRatePerBag, noOfBagOrder, leadStatus;
        Button poDetailsFillupButton, lostReasonBtn, checkPoDetailsButton, forwardToMisButton, sendBackToSoButton;

        public ViewHolder(View itemView) {
            super(itemView);
            buttonLayout = itemView.findViewById(R.id.buttonLayout);
            leadId = itemView.findViewById(R.id.leadId);
            contactPersonName = itemView.findViewById(R.id.contactPersonName);
            contactNumber = itemView.findViewById(R.id.contactNumber);
            soldToParty = itemView.findViewById(R.id.soldToParty);
            shipToParty = itemView.findViewById(R.id.shipToParty);
            nextVisitData = itemView.findViewById(R.id.nextVisitData);
            expRatePerBag = itemView.findViewById(R.id.expRatePerBag);
            noOfBagOrder = itemView.findViewById(R.id.noOfBagOrder);
            leadStatus = itemView.findViewById(R.id.leadStatus);
            poDetailsFillupButton = itemView.findViewById(R.id.poDetailsFillupButton);
            poDetailsFillupButton.setVisibility(GONE);
            lostReasonBtn = itemView.findViewById(R.id.lostReasonBtn);
            lostReasonBtn.setVisibility(GONE);
            checkPoDetailsButton = itemView.findViewById(R.id.checkPoDetailsButton);
            checkPoDetailsButton.setVisibility(GONE);
            forwardToMisButton = itemView.findViewById(R.id.forwardToMisButton);
            forwardToMisButton.setVisibility(GONE);
            sendBackToSoButton = itemView.findViewById(R.id.sendBackToSoButton);
            sendBackToSoButton.setVisibility(GONE);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_quotation_generation_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        LeadListMasterTableDataSet item = list.get(position);

        mNewDatabaseForSiteLead = new NewDatabaseForSiteLead(context);

        holder.leadId.setText(toTitleCase(item.getLead_generation_id()));
        holder.contactPersonName.setText(toTitleCase(item.getContact_person_name()));
        holder.contactNumber.setText(toTitleCase(item.getContact_number()));
        holder.soldToParty.setText(toTitleCase(mNewDatabaseForSiteLead.getCustomerDetails(item.getSold_to_party()).getCust_name()));
        holder.shipToParty.setText(toTitleCase(mNewDatabaseForSiteLead.getCustomerDetails(item.getShip_to_party()).getCust_name()));
        holder.nextVisitData.setText(toTitleCase(item.getNext_visit_date()));
        holder.expRatePerBag.setText(toTitleCase(item.getExp_rate_per_bag()));
        holder.noOfBagOrder.setText(toTitleCase(item.getQty_req()));
        holder.leadStatus.setText(LeadQuotationStatusTitleColor.getTitle(Integer.parseInt(item.getLead_quotation_status())));
        holder.leadStatus.setTextColor(Color.parseColor(LeadQuotationStatusTitleColor.getColor(Integer.parseInt(item.getLead_quotation_status()))));

        if (item.getLead_quotation_status().equalsIgnoreCase("14")) {
            if (user_type.equalsIgnoreCase("nt")) {
                holder.buttonLayout.setVisibility(VISIBLE);
                holder.poDetailsFillupButton.setVisibility(VISIBLE);
                holder.lostReasonBtn.setVisibility(VISIBLE);
                holder.checkPoDetailsButton.setVisibility(GONE);
                holder.forwardToMisButton.setVisibility(GONE);
                holder.sendBackToSoButton.setVisibility(GONE);
            } else {
                holder.buttonLayout.setVisibility(GONE);
            }
        }
        else if (item.getLead_quotation_status().equalsIgnoreCase("17")) {
            if (user_type.equalsIgnoreCase("nt")) {
                holder.buttonLayout.setVisibility(VISIBLE);
                holder.poDetailsFillupButton.setVisibility(VISIBLE);
                holder.lostReasonBtn.setVisibility(VISIBLE);
                holder.checkPoDetailsButton.setVisibility(GONE);
                holder.forwardToMisButton.setVisibility(GONE);
                holder.sendBackToSoButton.setVisibility(GONE);
            } else {
                holder.buttonLayout.setVisibility(GONE);
            }
        }
        else if (item.getLead_quotation_status().equalsIgnoreCase("12")) {
            if (user_type.equalsIgnoreCase("nt")) {
                if (item.getLost_reason()==null || item.getLost_reason().equalsIgnoreCase("")) {
                    holder.buttonLayout.setVisibility(VISIBLE);
                    holder.poDetailsFillupButton.setVisibility(GONE);
                    holder.lostReasonBtn.setVisibility(VISIBLE);
                    holder.checkPoDetailsButton.setVisibility(GONE);
                    holder.forwardToMisButton.setVisibility(GONE);
                    holder.sendBackToSoButton.setVisibility(GONE);
                } else {
                    holder.buttonLayout.setVisibility(GONE);
                }
            } else {
                holder.buttonLayout.setVisibility(GONE);
            }
        }
        else if (item.getLead_quotation_status().equalsIgnoreCase("15")) {
            if (user_type.equalsIgnoreCase("nt")) {
                holder.buttonLayout.setVisibility(GONE);
            } else {
                holder.buttonLayout.setVisibility(VISIBLE);
                holder.poDetailsFillupButton.setVisibility(GONE);
                holder.lostReasonBtn.setVisibility(GONE);
                holder.checkPoDetailsButton.setVisibility(VISIBLE);
                holder.forwardToMisButton.setVisibility(VISIBLE);
                holder.sendBackToSoButton.setVisibility(GONE);
            }
        }
        else if (item.getLead_quotation_status().equalsIgnoreCase("19")) {
            if (user_type.equalsIgnoreCase("nt")) {
                holder.buttonLayout.setVisibility(GONE);
            } else {
                holder.buttonLayout.setVisibility(VISIBLE);
                holder.poDetailsFillupButton.setVisibility(GONE);
                holder.lostReasonBtn.setVisibility(GONE);
                holder.checkPoDetailsButton.setVisibility(GONE);
                holder.forwardToMisButton.setVisibility(GONE);
                holder.sendBackToSoButton.setVisibility(VISIBLE);
            }
        }

        holder.poDetailsFillupButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onPoDetailsFillupClicked(item, position);
            }
        });
        holder.lostReasonBtn.setOnClickListener(v -> {
            if (listener != null) {
                listener.onLostReasonClicked(item, position);
            }
        });
        holder.checkPoDetailsButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onCheckPoDetailsClicked(item, position);
            }
        });
        holder.forwardToMisButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onForwardToMisClicked(item, position);
            }
        });
        holder.sendBackToSoButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onSendBackToSoClicked(item, position);
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
