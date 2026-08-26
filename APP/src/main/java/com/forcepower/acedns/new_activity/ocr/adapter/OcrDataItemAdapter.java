package com.forcepower.acedns.new_activity.ocr.adapter;

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
import com.forcepower.acedns.new_activity.declaration.adapter.DeclarationAdapter;
import com.forcepower.acedns.new_activity.declaration.data_set.DeclarationItem;
import com.forcepower.acedns.new_activity.ocr.dataset.OcrItem;

import java.util.ArrayList;

public class OcrDataItemAdapter extends RecyclerView.Adapter<OcrDataItemAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<OcrItem> list;
    private final OnActionClickListener listener;

    public interface OnActionClickListener {
        void deleteItem(OcrItem item, int position);
    }

    public OcrDataItemAdapter(Context context, ArrayList<OcrItem> list, OnActionClickListener listener) {
        this.context = context;
        this.list = list;
        this.listener = listener;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView slNoText;
        EditText nameText, phoneNoText,addressText;
        Button deleteIcon;
        RadioGroup ilpRegisteredRadioGroup;
        RadioButton yesRadioButton,noRadioButton;
        TextWatcher nameWatcher, phoneWatcher,addressWatcher;
        RadioGroup.OnCheckedChangeListener ilpListener;

        public ViewHolder(View itemView) {
            super(itemView);
            nameText = itemView.findViewById(R.id.nameText);
            slNoText=itemView.findViewById(R.id.slNoText);
            phoneNoText = itemView.findViewById(R.id.phoneNoText);
            addressText=itemView.findViewById(R.id.addressText);
            deleteIcon = itemView.findViewById(R.id.deleteIcon);
            ilpRegisteredRadioGroup=itemView.findViewById(R.id.ilpRegisteredRadioGroup);
            yesRadioButton=itemView.findViewById(R.id.yesRadioButton);
            noRadioButton=itemView.findViewById(R.id.noRadioButton);
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
        if (holder.nameWatcher != null) holder.nameText.removeTextChangedListener(holder.nameWatcher);
        if (holder.phoneWatcher != null) holder.phoneNoText.removeTextChangedListener(holder.phoneWatcher);
        if (holder.addressWatcher != null) holder.addressText.removeTextChangedListener(holder.addressWatcher);

        OcrItem item = list.get(position);
        holder.slNoText.setText(item.getSlNo());
        holder.nameText.setText(item.getName());
        holder.phoneNoText.setText(item.getPhoneNo());
        holder.addressText.setText(item.getAddress());
        if ("Yes".equalsIgnoreCase(item.getIlpRegistered())) {
            holder.yesRadioButton.setChecked(true);
        } else if ("No".equalsIgnoreCase(item.getIlpRegistered())) {
            holder.noRadioButton.setChecked(true);
        } else {
            holder.ilpRegisteredRadioGroup.clearCheck();
        }

        holder.nameWatcher = new TextWatcher() {
            @Override public void beforeTextChanged(CharSequence s, int a, int b, int c) {}
            @Override public void onTextChanged(CharSequence s, int a, int b, int c) {}
            @Override public void afterTextChanged(Editable s) {
                int pos = holder.getAdapterPosition();
                if (pos != RecyclerView.NO_POSITION) {
                    list.get(pos).setName(s.toString());
                }
            }
        };
        holder.phoneWatcher = new TextWatcher() {
            @Override public void beforeTextChanged(CharSequence s, int a, int b, int c) {}
            @Override public void onTextChanged(CharSequence s, int a, int b, int c) {}
            @Override public void afterTextChanged(Editable s) {
                int pos = holder.getAdapterPosition();
                if (pos != RecyclerView.NO_POSITION) {
                    list.get(pos).setPhoneNo(s.toString());
                }
            }
        };
        holder.addressWatcher = new TextWatcher() {
            @Override public void beforeTextChanged(CharSequence s, int a, int b, int c) {}
            @Override public void onTextChanged(CharSequence s, int a, int b, int c) {}
            @Override public void afterTextChanged(Editable s) {
                int pos = holder.getAdapterPosition();
                if (pos != RecyclerView.NO_POSITION) {
                    list.get(pos).setAddress(s.toString());
                }
            }
        };
        holder.ilpListener = (group, checkedId) -> {
            int pos = holder.getAdapterPosition();
            if (pos != RecyclerView.NO_POSITION) {
                if (checkedId == holder.yesRadioButton.getId()) {
                    list.get(pos).setIlpRegistered("Yes");
                } else if (checkedId == holder.noRadioButton.getId()) {
                    list.get(pos).setIlpRegistered("No");
                }
            }
        };

        holder.nameText.addTextChangedListener(holder.nameWatcher);
        holder.phoneNoText.addTextChangedListener(holder.phoneWatcher);
        holder.addressText.addTextChangedListener(holder.addressWatcher);
        holder.ilpRegisteredRadioGroup.setOnCheckedChangeListener(holder.ilpListener);

        holder.deleteIcon.setOnClickListener(v -> {
            int pos = holder.getAdapterPosition();
            if (pos != RecyclerView.NO_POSITION) {
                listener.deleteItem(list.get(pos), pos);
            }
        });
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}