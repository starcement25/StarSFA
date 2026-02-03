package com.forcepower.acedns.new_activity.declaration.adapter;

import android.content.Context;
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
import com.forcepower.acedns.new_activity.declaration.data_set.DeclarationItem;

import java.util.ArrayList;

public class DeclarationAdapter extends RecyclerView.Adapter<DeclarationAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<DeclarationItem> list;
    private final OnActionClickListener listener;

    public interface OnActionClickListener {
        void onApproveClicked(DeclarationItem item, int position);
        void onRejectClicked(DeclarationItem item, int position);
    }

    public DeclarationAdapter(Context context, ArrayList<DeclarationItem> list,OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        LinearLayout dealerNameLayout;
        TextView dealerName;

        LinearLayout branchNameLayout;
        TextView branchName;

        LinearLayout asmApproveStatusLayout;
        TextView asmApproveStatus;

        LinearLayout asmApproveTimeLayout;
        TextView asmApproveTime;

        LinearLayout rsmApproveStatusLayout;
        TextView rsmApproveStatus;

        LinearLayout rsmApproveTimeLayout;
        TextView rsmApproveTime;

        LinearLayout declarationStatusLayout;
        TextView declarationStatus;

        LinearLayout declarationRejectReasonLayout;
        TextView declarationRejectReason;

        TextView declarationMonth;
        TextView requestDate;

        LinearLayout buttonLayout;
        Button approvedBtn, rejectBtn;

        public ViewHolder(View itemView) {
            super(itemView);
            dealerNameLayout = itemView.findViewById(R.id.dealerNameLayout);
            dealerName = itemView.findViewById(R.id.dealerName);
            branchNameLayout = itemView.findViewById(R.id.branchNameLayout);
            branchName = itemView.findViewById(R.id.branchName);
            asmApproveStatusLayout = itemView.findViewById(R.id.asmApproveStatusLayout);
            asmApproveStatus = itemView.findViewById(R.id.asmApproveStatus);
            asmApproveTimeLayout = itemView.findViewById(R.id.asmApproveTimeLayout);
            asmApproveTime = itemView.findViewById(R.id.asmApproveTime);
            rsmApproveStatusLayout = itemView.findViewById(R.id.rsmApproveStatusLayout);
            rsmApproveStatus = itemView.findViewById(R.id.rsmApproveStatus);
            rsmApproveTimeLayout = itemView.findViewById(R.id.rsmApproveTimeLayout);
            rsmApproveTime = itemView.findViewById(R.id.rsmApproveTime);
            declarationStatusLayout = itemView.findViewById(R.id.declarationStatusLayout);
            declarationStatus = itemView.findViewById(R.id.declarationStatus);
            declarationRejectReasonLayout = itemView.findViewById(R.id.declarationRejectReasonLayout);
            declarationRejectReason = itemView.findViewById(R.id.declarationRejectReason);
            requestDate=itemView.findViewById(R.id.requestDate);

            buttonLayout = itemView.findViewById(R.id.buttonLayout);
            approvedBtn = itemView.findViewById(R.id.approvedBtn);
            rejectBtn = itemView.findViewById(R.id.rejectBtn);

            declarationMonth=itemView.findViewById(R.id.declarationMonth);
        }
    }

    @NonNull
    @Override
    public DeclarationAdapter.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_declaration, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull DeclarationAdapter.ViewHolder holder, int position) {
        DeclarationItem item = list.get(position);

        String month = switch (item.getMonth()) {
            case "01" -> "January, " + item.getCurrentYear();
            case "02" -> "February, " + item.getCurrentYear();
            case "03" -> "March, " + item.getCurrentYear();
            case "04" -> "April, " + item.getCurrentYear();
            case "05" -> "May, " + item.getCurrentYear();
            case "06" -> "June, " + item.getCurrentYear();
            case "07" -> "July, " + item.getCurrentYear();
            case "08" -> "August, " + item.getCurrentYear();
            case "09" -> "September, " + item.getCurrentYear();
            case "10" -> "October, " + item.getCurrentYear();
            case "11" -> "November, " + item.getCurrentYear();
            case "12" -> "December, " + item.getCurrentYear();
            default -> "";
        };

        holder.dealerName.setText(item.getDealer_name());
        holder.branchName.setText(item.getBranch());
        holder.asmApproveStatus.setText(item.getAsm_approve_status());
        holder.asmApproveTime.setText(item.getAsm_approve_date());
        holder.rsmApproveStatus.setText(item.getRsm_approve_status());
        holder.rsmApproveTime.setText(item.getRsm_approve_date());
        holder.declarationStatus.setText(item.getStatus());
        holder.declarationMonth.setText(month);
        holder.requestDate.setText(item.getCreated_at());

        if(item.getStatus().equalsIgnoreCase("Rejected")){
            holder.declarationRejectReasonLayout.setVisibility(View.VISIBLE);
            holder.declarationRejectReason.setText(item.getRejectReason());
        }else{
            holder.declarationRejectReasonLayout.setVisibility(View.GONE);
        }

        if (item.getMy_user_type().equalsIgnoreCase("L3")) {
            holder.rsmApproveStatusLayout.setVisibility(View.GONE);
            holder.rsmApproveTimeLayout.setVisibility(View.GONE);
            if (item.getAsm_approve_status().equalsIgnoreCase("Pending")) {
                holder.asmApproveStatusLayout.setVisibility(View.VISIBLE);
                holder.asmApproveTimeLayout.setVisibility(View.GONE);
                holder.buttonLayout.setVisibility(View.VISIBLE);
            } else {
                holder.asmApproveStatusLayout.setVisibility(View.VISIBLE);
                holder.asmApproveTimeLayout.setVisibility(View.VISIBLE);
                holder.buttonLayout.setVisibility(View.GONE);
            }
        }
        else {
            holder.asmApproveStatusLayout.setVisibility(View.VISIBLE);
            holder.asmApproveTimeLayout.setVisibility(View.VISIBLE);
            if (item.getRsm_approve_status().equalsIgnoreCase("Pending")) {
                holder.rsmApproveStatusLayout.setVisibility(View.VISIBLE);
                holder.rsmApproveTimeLayout.setVisibility(View.GONE);
                holder.buttonLayout.setVisibility(View.VISIBLE);
            } else {
                holder.rsmApproveStatusLayout.setVisibility(View.VISIBLE);
                holder.rsmApproveTimeLayout.setVisibility(View.VISIBLE);
                holder.buttonLayout.setVisibility(View.GONE);
            }
        }

        Log.d("TAG", "_DOWNLOAD_ onBindViewHolder item id: " + item.getDealer_name());
        holder.approvedBtn.setOnClickListener(v -> {
            if (listener != null) {
                Log.d("TAG", "_DOWNLOAD_ button clicked item id: " + item.getDealer_name());
                listener.onApproveClicked(item, position);
            }
        });

        holder.rejectBtn.setOnClickListener(v -> {
            if (listener != null) {
                listener.onRejectClicked(item, position);
            }
        });
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}
