package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filterable;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.TentProductList;

import java.util.ArrayList;

public class TentFormExistingProductAdapter extends ArrayAdapter<TentProductList> implements Filterable {

    private final Context context;
    private ArrayList<TentProductList> nameValues;
    private final int resourceId;
    //private ViewHolder viewHolder;
    ViewHolder viewHolder = null;

    public TentFormExistingProductAdapter(Context context, int resourceId, ArrayList<TentProductList> nameValues) {

        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {



        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.imgCheckUncheck = (ImageView) convertView.findViewById(R.id.iv_check_uncheck);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        String menuItem = nameValues.get(position).getDate();

        viewHolder.txtView.setText(menuItem);

        viewHolder.imgCheckUncheck.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                viewHolder.imgCheckUncheck.setImageResource(R.drawable.uncheck);
                /*if(nameValues.get(position).getTotal().matches("1")){
                    viewHolder.imgCheckUncheck.setImageResource(R.drawable.uncheck);
                    nameValues.get(position).setTotal("0");
                }*//*else if(nameValues.get(position).getTotal().matches("0")){
                    viewHolder.imgCheckUncheck.setImageResource(R.drawable.check);
                    nameValues.get(position).setTotal("1");
                }*//*
                else{
                    viewHolder.imgCheckUncheck.setImageResource(R.drawable.check);
                    nameValues.get(position).setTotal("1");
                }*/
            }
        });




        return convertView;
    }

    public void setFilter(final ArrayList<TentProductList> menu_item_list_)
    {
        nameValues = new ArrayList<>();
        nameValues.addAll(menu_item_list_);
        notifyDataSetChanged();
    }

    public class ViewHolder {
        TextView txtView;
        ImageView imgCheckUncheck;
    }
}