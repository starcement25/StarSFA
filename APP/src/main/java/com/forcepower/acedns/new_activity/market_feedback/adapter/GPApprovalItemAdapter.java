package com.forcepower.acedns.new_activity.market_feedback.adapter;

import static android.view.View.GONE;
import static android.view.View.VISIBLE;

import android.content.Context;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
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
    private int filterType;


    public interface OnActionClickListener {
        void approveItem(GPApprovalItem item, int position);
        void rejectItem(GPApprovalItem item, int position);
    }

    public GPApprovalItemAdapter(Context context, ArrayList<GPApprovalItem> list,int filterType, GPApprovalItemAdapter.OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
        this.filterType=filterType;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView slNoText,customerNameText,customerCodeText,universeTypeText,stateText,districtText,blockText,gramPanchayatText,approvalButton,rejectButton,approvedText,empCodeText,empNameText;
        LinearLayout stateLayout,districtLayout,blockLayout,gramPanchayatLayout,actionLayout,empCodeLayout,empNameLayout;
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
            rejectButton=itemView.findViewById(R.id.rejectButton);
            approvedText=itemView.findViewById(R.id.approvedText);
            empCodeText=itemView.findViewById(R.id.empCodeText);
            empNameText=itemView.findViewById(R.id.empNameText);

            stateLayout=itemView.findViewById(R.id.stateLayout);
            districtLayout=itemView.findViewById(R.id.districtLayout);
            blockLayout=itemView.findViewById(R.id.blockLayout);
            gramPanchayatLayout=itemView.findViewById(R.id.gramPanchayatLayout);
            actionLayout=itemView.findViewById(R.id.actionLayout);
            empCodeLayout=itemView.findViewById(R.id.empCodeLayout);
            empNameLayout=itemView.findViewById(R.id.empNameLayout);
        }
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_gp_approval, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        // remove old watchers before setText, or they'll fire with stale position/item
        GPApprovalItem item = list.get(position);
        // Serial number must follow the current visible position. The value loaded
        // from the database becomes stale as soon as an approved row is removed.
        holder.slNoText.setText(String.valueOf(position + 1));
        holder.customerNameText.setText(item.getCustomerName());
        holder.customerCodeText.setText(item.getCustomerCode());
        holder.universeTypeText.setText(item.getUniverseType());
        holder.stateText.setText(item.getState());
        holder.districtText.setText(item.getDistrict());
        holder.blockText.setText(item.getBlock());
        holder.gramPanchayatText.setText(item.getGramPanchayat());
        holder.empCodeText.setText(item.getEmpCode());
        holder.empNameText.setText(item.getEmpName());
        holder.approvalButton.setOnClickListener(v -> {
            int currentPosition = holder.getAdapterPosition();
            if (currentPosition != RecyclerView.NO_POSITION) {
                listener.approveItem(list.get(currentPosition), currentPosition);
            }
        });
        holder.rejectButton.setOnClickListener(v -> {
            int currentPosition = holder.getAdapterPosition();
            if (currentPosition != RecyclerView.NO_POSITION) {
                listener.rejectItem(list.get(currentPosition), currentPosition);
            }
        });
        if ("1".equalsIgnoreCase(item.getApprovalStatus())) {
            holder.approvalButton.setVisibility(View.GONE);
            holder.rejectButton.setVisibility(View.GONE);
            holder.approvedText.setVisibility(View.VISIBLE);
        }else{
            holder.approvalButton.setVisibility(View.VISIBLE);
            holder.rejectButton.setVisibility(View.VISIBLE);
            holder.approvedText.setVisibility(View.GONE);
        }

        if(filterType==1){
            holder.stateLayout.setVisibility(VISIBLE);
            holder.districtLayout.setVisibility(VISIBLE);
            holder.blockLayout.setVisibility(VISIBLE);
            holder.gramPanchayatLayout.setVisibility(VISIBLE);
            holder.actionLayout.setVisibility(VISIBLE);
            holder.empCodeLayout.setVisibility(VISIBLE);
            holder.empNameLayout.setVisibility(VISIBLE);
        }else if(filterType==2){
            holder.stateLayout.setVisibility(VISIBLE);
            holder.districtLayout.setVisibility(VISIBLE);
            holder.blockLayout.setVisibility(VISIBLE);
            holder.gramPanchayatLayout.setVisibility(VISIBLE);
            holder.actionLayout.setVisibility(VISIBLE);
            holder.empCodeLayout.setVisibility(GONE);
            holder.empNameLayout.setVisibility(GONE);
        }else {
            holder.stateLayout.setVisibility(GONE);
            holder.districtLayout.setVisibility(GONE);
            holder.blockLayout.setVisibility(GONE);
            holder.gramPanchayatLayout.setVisibility(GONE);
            holder.actionLayout.setVisibility(GONE);
            holder.empCodeLayout.setVisibility(VISIBLE);
            holder.empNameLayout.setVisibility(VISIBLE);
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    public void setFilterType(int filterType) {
        this.filterType = filterType;
    }
}
