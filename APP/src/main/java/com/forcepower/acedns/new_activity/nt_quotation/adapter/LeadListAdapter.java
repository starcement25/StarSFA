package com.forcepower.acedns.new_activity.nt_quotation.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
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

import java.util.ArrayList;

public class LeadListAdapter extends RecyclerView.Adapter<LeadListAdapter.ViewHolder>{
    private final Context context;
    private final ArrayList<LeadListMasterTableDataSet> list;
    private final OnActionClickListener listener;

    NewDatabaseForSiteLead mNewDatabaseForSiteLead;

    public interface OnActionClickListener {
        void onDetailsClicked(LeadListMasterTableDataSet item, int position);
        void onLogDetailsClicked(LeadListMasterTableDataSet item, int position);
        void onLostReasonClicked(LeadListMasterTableDataSet item,int position);
    }

    public LeadListAdapter(Context context, ArrayList<LeadListMasterTableDataSet> list, OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView leadId, contactPersonName, contactNumber, soldToParty, shipToParty, nextVisitData, expRatePerBag, noOfBagOrder, leadStatus;
        Button viewDetailsBtn,lostReasonBtn;
        LinearLayout logDetailsButton;

        public ViewHolder(View itemView) {
            super(itemView);
            leadId = itemView.findViewById(R.id.leadId);
            contactPersonName = itemView.findViewById(R.id.contactPersonName);
            contactNumber = itemView.findViewById(R.id.contactNumber);
            soldToParty = itemView.findViewById(R.id.soldToParty);
            shipToParty = itemView.findViewById(R.id.shipToParty);
            nextVisitData = itemView.findViewById(R.id.nextVisitData);
            expRatePerBag = itemView.findViewById(R.id.expRatePerBag);
            noOfBagOrder = itemView.findViewById(R.id.noOfBagOrder);
            leadStatus = itemView.findViewById(R.id.leadStatus);
            viewDetailsBtn = itemView.findViewById(R.id.viewDetailsBtn);
            logDetailsButton=itemView.findViewById(R.id.logDetailsButton);
            lostReasonBtn=itemView.findViewById(R.id.lostReasonBtn);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_lead_generation_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        LeadListMasterTableDataSet item = list.get(position);

        mNewDatabaseForSiteLead=new NewDatabaseForSiteLead(context);

        holder.leadId.setText(toTitleCase(item.getLead_generation_id()));
        holder.contactPersonName.setText(toTitleCase(item.getContact_person_name()));
        holder.contactNumber.setText(toTitleCase(item.getContact_number()));
        holder.soldToParty.setText(toTitleCase(mNewDatabaseForSiteLead.getCustomerDetails(item.getSold_to_party()).getCust_name()));
        holder.shipToParty.setText(toTitleCase(mNewDatabaseForSiteLead.getCustomerDetails(item.getShip_to_party()).getCust_name()));
        holder.nextVisitData.setText(toTitleCase(item.getNext_visit_date()));
        holder.expRatePerBag.setText(toTitleCase(item.getExp_rate_per_bag()));
        holder.noOfBagOrder.setText(toTitleCase(item.getQty_req()));
        switch (item.getLead_quotation_status()){
            case "1":
                holder.leadStatus.setText("SO Lead Create");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "2":
                holder.leadStatus.setText("HOS Hold");
                holder.leadStatus.setTextColor(Color.parseColor("#F59127"));
                break;
            case "3":
                holder.leadStatus.setText("HOS Revision");
                holder.leadStatus.setTextColor(Color.parseColor("#555555"));
                break;
            case "4":
                holder.leadStatus.setText("HOS Reject");
                holder.leadStatus.setTextColor(Color.parseColor("#F52727"));
                break;
            case "5":
                holder.leadStatus.setText("HOS Approved");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "6":
                holder.leadStatus.setText("MIS Quotation Create");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "7":
                holder.leadStatus.setText("MIS Send To COO");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "8":
                holder.leadStatus.setText("COO Lead Revision");
                holder.leadStatus.setTextColor(Color.parseColor("#555555"));
                break;
            case "9":
                holder.leadStatus.setText("COO Lead Reject");
                holder.leadStatus.setTextColor(Color.parseColor("#F52727"));
                break;
            case "10":
                holder.leadStatus.setText("COO Lead Approved");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "11":
                holder.leadStatus.setText("MIS Sent To SAP");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "12":
                holder.leadStatus.setText("Lost Order");
                holder.leadStatus.setTextColor(Color.parseColor("#F52727"));
                break;
            case "13":
                holder.leadStatus.setText("SAP Quotation Create");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "14":
                holder.leadStatus.setText("SAP PO Received");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "15":
                holder.leadStatus.setText("SAP Contract Create");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
            case "16":
                holder.leadStatus.setText("SAP SO Create");
                holder.leadStatus.setTextColor(Color.parseColor("#000000"));
                break;
        }

        holder.viewDetailsBtn.setOnClickListener(v -> {
            if (listener != null) {
                listener.onDetailsClicked(item, position);
            }
        });
        holder.logDetailsButton.setOnClickListener(v->{
            if (listener != null) {
                listener.onLogDetailsClicked(item, position);
            }
        });
        holder.lostReasonBtn.setOnClickListener(v->{
            if (listener != null) {
                listener.onLostReasonClicked(item, position);
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
