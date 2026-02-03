package com.forcepower.acedns.new_activity.rsar_meeting.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.rsar_meeting.data_set.ParticipatedInformation;

import java.util.ArrayList;

public class ParticipateEnterAdapter extends RecyclerView.Adapter<ParticipateEnterAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<ParticipatedInformation> list;
    private final OnActionClickListener listener;

    public interface OnActionClickListener {
        void onRemoveItemClicked(ParticipatedInformation item, int position);

        void onRSARCounterName(ParticipatedInformation item, int position);
    }

    public ParticipateEnterAdapter(Context context, ArrayList<ParticipatedInformation> list, ParticipateEnterAdapter.OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        Button rsarCounterNameButton;
        TextView textRsarCounterName;
        TextView textName;
        EditText editTextName;
        TextView textPhoneNumber;
        EditText editTextPhoneNumber;
        LinearLayout removeButtonLayout;
        Button removeButton;

        public ViewHolder(View itemView) {
            super(itemView);
            rsarCounterNameButton = itemView.findViewById(R.id.rsarCounterNameButton);
            textRsarCounterName = itemView.findViewById(R.id.textRsarCounterName);
            textName = itemView.findViewById(R.id.textName);
            editTextName = itemView.findViewById(R.id.editTextName);
            textPhoneNumber = itemView.findViewById(R.id.textPhoneNumber);
            editTextPhoneNumber = itemView.findViewById(R.id.editTextPhoneNumber);
            removeButtonLayout = itemView.findViewById(R.id.removeButtonLayout);
            removeButton = itemView.findViewById(R.id.removeButton);
        }
    }

    @NonNull
    @Override
    public ParticipateEnterAdapter.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_participate_enter, parent, false);
        return new ParticipateEnterAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ParticipateEnterAdapter.ViewHolder holder, int position) {
        ParticipatedInformation item = list.get(position);
        holder.textRsarCounterName.setText(item.getRsarCounterName());
        if (list.size() == 1) {
            holder.removeButtonLayout.setVisibility(View.GONE);
        } else {
            holder.removeButtonLayout.setVisibility(View.VISIBLE);
        }
        holder.removeButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onRemoveItemClicked(item, position);
            }
        });
        holder.rsarCounterNameButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onRSARCounterName(item, position);
            }
        });
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}