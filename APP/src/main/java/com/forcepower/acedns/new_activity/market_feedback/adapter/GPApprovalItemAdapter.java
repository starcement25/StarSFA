package com.forcepower.acedns.new_activity.market_feedback.adapter;

import android.content.Context;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.market_feedback.data.GPApprovalItem;

import java.util.ArrayList;

public class GPApprovalItemAdapter extends RecyclerView.Adapter<GPApprovalItemAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<GPApprovalItem> list;
    private final GPApprovalItemAdapter.OnActionClickListener listener;

    public interface OnActionClickListener {
        void approveItem(GPApprovalItem item, int position);
    }

    public GPApprovalItemAdapter(Context context, ArrayList<GPApprovalItem> list, GPApprovalItemAdapter.OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView slNoText,customerNameText,customerCodeText,universeTypeText,stateText,districtText,blockText,gramPanchayatText,approvalButton,approvedText;
        public ViewHolder(View itemView) {
            super(itemView);
            slNoText=itemView.findViewById(R.id.slNoText);
            customerNameText=itemView.findViewById(R.id.customerNameText);
            customerCodeText=itemView.findViewById(R.id.customerCodeText);
            universeTypeText=itemView.findViewById(R.id.universeTypeText);
            stateText=itemView.findViewById(R.id.stateText);
            districtText=itemView.findViewById(R.id.districtText);
            blockText=itemView.findViewById(R.id.blockText);
            gramPanchayatText=itemView.findViewById(R.id.gramPanchayatText);
            approvalButton=itemView.findViewById(R.id.approvalButton);
            approvedText=itemView.findViewById(R.id.approvedText);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_ocr, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        // remove old watchers before setText, or they'll fire with stale position/item
        GPApprovalItem item = list.get(position);
        holder.slNoText.setText(item.getSlNo());
        holder.customerNameText.setText(item.getCustomerName());
        holder.customerCodeText.setText(item.getCustomerCode());
        holder.universeTypeText.setText(item.getUniverseType());
        holder.stateText.setText(item.getState());
        holder.districtText.setText(item.getDistrict());
        holder.blockText.setText(item.getBlock());
        holder.gramPanchayatText.setText(item.getGramPanchayat());
        holder.approvalButton.setOnClickListener(v -> listener.approveItem(item, position));
        if ("1".equalsIgnoreCase(item.getApprovalStatus())) {
            holder.approvalButton.setVisibility(View.GONE);
            holder.approvedText.setVisibility(View.VISIBLE);
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}