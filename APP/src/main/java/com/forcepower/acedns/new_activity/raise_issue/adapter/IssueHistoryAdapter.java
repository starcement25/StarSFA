package com.forcepower.acedns.new_activity.raise_issue.adapter;

import static android.view.View.GONE;

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
import com.forcepower.acedns.new_activity.nt_quotation.dataset.LogDataSet;
import com.forcepower.acedns.new_activity.raise_issue.dataset.IssueHistoryData;

import java.util.ArrayList;

public class IssueHistoryAdapter extends RecyclerView.Adapter<IssueHistoryAdapter.ViewHolder>{
    private final Context context;
    private final ArrayList<IssueHistoryData> list;

    public IssueHistoryAdapter(Context context, ArrayList<IssueHistoryData> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView customerNameText, branchNameText,categoryText,stateText,districtText,escalatedDepartmentText,issueText,statusText,statusChangeTimeText,issueCreatedTimeText;
        LinearLayout statusUpdateLayout;

        public ViewHolder(View itemView) {
            super(itemView);
            customerNameText = itemView.findViewById(R.id.customerNameText);
            branchNameText = itemView.findViewById(R.id.branchNameText);
            categoryText = itemView.findViewById(R.id.categoryText);
            stateText = itemView.findViewById(R.id.stateText);
            districtText = itemView.findViewById(R.id.districtText);
            escalatedDepartmentText = itemView.findViewById(R.id.escalatedDepartmentText);
            issueText = itemView.findViewById(R.id.issueText);
            statusText = itemView.findViewById(R.id.statusText);
            statusChangeTimeText = itemView.findViewById(R.id.statusChangeTimeText);
            issueCreatedTimeText = itemView.findViewById(R.id.issueCreatedTimeText);
            statusUpdateLayout=itemView.findViewById(R.id.statusUpdateLayout);
            statusUpdateLayout.setVisibility(GONE);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_issue_history_item, parent, false);
        return new ViewHolder(view);
    }

    @SuppressLint("SetTextI18n")
    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        IssueHistoryData item = list.get(position);

        holder.customerNameText.setText(item.getCustomerName());
        holder.branchNameText.setText(item.getBranchName());
        holder.categoryText.setText(item.getCategory());
        holder.stateText.setText(item.getState());
        holder.districtText.setText(item.getDistrict());
        holder.escalatedDepartmentText.setText(item.getEscalatedDepartment());
        holder.issueText.setText(item.getIssue());
        holder.statusText.setText(capitalize(item.getStatus()));
        holder.statusChangeTimeText.setText(item.getStatusChangeDate());
        holder.issueCreatedTimeText.setText(item.getCreatedAt());

        if(item.getCreatedAt().equalsIgnoreCase(item.getStatusChangeDate())){
            holder.statusUpdateLayout.setVisibility(GONE);
        }else{
            holder.statusUpdateLayout.setVisibility(View.VISIBLE);
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    public static String capitalize(String s) {
        if (s == null || s.isEmpty()) return s;
        return s.substring(0, 1).toUpperCase() + s.substring(1);
    }
}