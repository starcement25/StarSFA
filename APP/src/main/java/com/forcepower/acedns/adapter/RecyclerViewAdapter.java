package com.forcepower.acedns.adapter;

import android.content.Context;
import android.util.SparseBooleanArray;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.CheckBox;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;

import java.util.ArrayList;

public class RecyclerViewAdapter extends RecyclerView.Adapter<RecyclerViewAdapter.RecyclerViewHolder> {

    static class RecyclerViewHolder extends RecyclerView.ViewHolder {

        private TextView label;
        private CheckBox checkBox;

        RecyclerViewHolder(View view) {
            super(view);
            label = (TextView) view.findViewById(R.id.label);
            checkBox = (CheckBox) view.findViewById(R.id.checkbox);
        }

    }

    private ArrayList<String> arrayList;
    private Context context;
    private SparseBooleanArray mSelectedItemsIds;
    private String box="";


    public RecyclerViewAdapter(Context context, ArrayList<String> arrayList) {
        this.arrayList = arrayList;
        this.context = context;
        mSelectedItemsIds = new SparseBooleanArray();
    }

    public RecyclerViewAdapter(Context context, ArrayList<String> arrayList, String box) {
        this.arrayList = arrayList;
        this.context = context;
        mSelectedItemsIds = new SparseBooleanArray();
        this.box = box;
    }

    @Override
    public RecyclerViewHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        View v = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.list_item_single_with_checkbox, viewGroup, false);
        return new RecyclerViewHolder(v);
    }

    @Override
    public void onBindViewHolder(final RecyclerViewHolder holder, final int i) {
        holder.label.setText(arrayList.get(i));
        holder.checkBox.setChecked(mSelectedItemsIds.get(i));

        holder.checkBox.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                checkCheckBox(i, !mSelectedItemsIds.get(i));
            }
        });

        holder.label.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                checkCheckBox(i, !mSelectedItemsIds.get(i));
            }
        });
    }

    @Override
    public int getItemCount() {
        return (null != arrayList ? arrayList.size() : 0);
    }

    /**
     * Remove all checkbox Selection
     **/
    public void removeSelection() {
        mSelectedItemsIds = new SparseBooleanArray();
        notifyDataSetChanged();
    }

    /**
     * Check the Checkbox if not checked
     **/
    public void checkCheckBox(int position, boolean value) {
        if(box.matches("radio")){
            for(int i=0;i< arrayList.size();i++){
                mSelectedItemsIds.delete(i);
            }
            mSelectedItemsIds.put(position, true);
        }else {
            if (value)
                mSelectedItemsIds.put(position, true);
            else
                mSelectedItemsIds.delete(position);
        }
        notifyDataSetChanged();
    }

    /**
     * Return the selected Checkbox IDs
     **/
    public SparseBooleanArray getSelectedIds() {
        return mSelectedItemsIds;
    }


}
