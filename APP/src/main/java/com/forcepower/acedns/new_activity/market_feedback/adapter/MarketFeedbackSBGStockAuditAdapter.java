package com.forcepower.acedns.new_activity.market_feedback.adapter;

import android.annotation.SuppressLint;
import android.content.Context;
import android.graphics.Color;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.TextView;

import androidx.annotation.NonNull;

import com.forcepower.acedns.R;
import com.forcepower.acedns.newDataBase.data_set.CustomerCompetitorQuantityDataSet;
import com.forcepower.acedns.newDataBase.data_set.SBGFeedbackDataSet;

import java.util.ArrayList;

public class MarketFeedbackSBGStockAuditAdapter extends ArrayAdapter<CustomerCompetitorQuantityDataSet> {
    public interface OnQuantityChangedListener {
        void onQuantityChanged();
    }

    private OnQuantityChangedListener listener;
    private final Context context;
    private final ArrayList<CustomerCompetitorQuantityDataSet> mSBGFeedbackDataSet;
    private final int resourceId;
    private MarketFeedbackSBGStockAuditAdapter.ViewHolder viewHolder;

    public MarketFeedbackSBGStockAuditAdapter(Context context, int resourceId,
                                              ArrayList<CustomerCompetitorQuantityDataSet> nameValues,
                                              OnQuantityChangedListener listener) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.mSBGFeedbackDataSet = nameValues;
        this.resourceId = resourceId;
        this.listener = listener;
    }

    @NonNull
    @SuppressLint("SetTextI18n")
    @Override
    public View getView(int position, View convertView, @NonNull ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.textViewCompetitorName =  convertView.findViewById(R.id.textViewCompetitorName);
            viewHolder.editTextViewCounterPotential =  convertView.findViewById(R.id.editTextViewCounterPotential);

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        // Remove old watcher before rebinding to avoid duplicate callbacks
        if (viewHolder.watcher != null) {
            viewHolder.editTextViewCounterPotential.removeTextChangedListener(viewHolder.watcher);
        }
        if(mSBGFeedbackDataSet.get(position).getMandatory().equalsIgnoreCase("Y")){
            viewHolder.textViewCompetitorName.setTextColor(Color.RED);
        } else {
            viewHolder.textViewCompetitorName.setTextColor(Color.BLACK);
        }
        Log.d("TAG", "_DOWNLOAD_ getView: "+mSBGFeedbackDataSet.get(position).getMandatory()+"^"+mSBGFeedbackDataSet.get(position).getCompetitorName());
        viewHolder.textViewCompetitorName.setText(mSBGFeedbackDataSet.get(position).getCompetitorName());
        viewHolder.editTextViewCounterPotential.setText(mSBGFeedbackDataSet.get(position).getQuantity());

        // Create a watcher tied to this position
        viewHolder.watcher = new TextWatcher() {
            @Override public void beforeTextChanged(CharSequence s, int start, int count, int after) {}
            @Override public void onTextChanged(CharSequence s, int start, int before, int count) {}

            @Override
            public void afterTextChanged(Editable s) {
                // Write the new value back into the data model
                String val = s.toString().trim();
                mSBGFeedbackDataSet.get(position).setQuantity(val.isEmpty() ? "0" : val);
                // Notify the Activity to refresh the total
                if (listener != null) listener.onQuantityChanged();
            }
        };

        viewHolder.editTextViewCounterPotential.addTextChangedListener(viewHolder.watcher);

        return convertView;
    }


    public static class ViewHolder {
        TextView textViewCompetitorName;
        EditText editTextViewCounterPotential;
        TextWatcher watcher;
    }
}