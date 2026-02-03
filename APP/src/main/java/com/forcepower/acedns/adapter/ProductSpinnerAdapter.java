package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.activity.DOFormActivity;
import com.forcepower.acedns.activity.OrderFormActivityAlternateDesign;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.ProductMasterDetails;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;


/***** Adapter class extends with ArrayAdapter ******/
public class ProductSpinnerAdapter extends ArrayAdapter<ProductMasterDetails> {

    LayoutInflater inflater;
    int textViewResourceId;
    private Activity activity;
    private ArrayList<ProductMasterDetails> data;

    /*************  CustomAdapter Constructor *****************/
    public ProductSpinnerAdapter(
            OrderFormActivityAlternateDesign activitySpinner,
            int textViewResourceId,
            ArrayList<ProductMasterDetails> objects
    ) {
        super(activitySpinner, textViewResourceId, objects);

        /********** Take passed values **********/
        activity = activitySpinner;
        this.textViewResourceId = textViewResourceId;
        data = objects;
        /***********  Layout inflator to call external xml layout () **********************/
        inflater = (LayoutInflater) activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

    public ProductSpinnerAdapter(
            DOFormActivity activitySpinner,
            int textViewResourceId,
            ArrayList<ProductMasterDetails> objects
    ) {
        super(activitySpinner, textViewResourceId, objects);

        /********** Take passed values **********/
        activity = activitySpinner;
        this.textViewResourceId = textViewResourceId;
        data = objects;
        /***********  Layout inflator to call external xml layout () **********************/
        inflater = (LayoutInflater) activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

    @Override
    public View getDropDownView(int position, View convertView, ViewGroup parent) {
        return getCustomView(position, convertView, parent);
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        return getCustomView(position, convertView, parent);
    }

    // This funtion called for each row ( Called data.size() times )
    public View getCustomView(int position, View convertView, ViewGroup parent) {

        /********** Inflate spinner_rows.xml file for each row ( Defined below ) ************/
        View row = inflater.inflate(textViewResourceId, parent, false);


        TextView label = (TextView) row.findViewById(R.id.text1);
        if (data.get(position).getFocus().equalsIgnoreCase("y")) {
            label.setTextColor(Color.parseColor("#F58322"));
        }
        String descOfCurrentSku = data.get(position).getDesc();
        if (Constants.userDetailsObj.getNickName().equalsIgnoreCase("dixcy") && !Constants.productDetailsObj.getprod_size().equalsIgnoreCase("yes")) {
            descOfCurrentSku = descOfCurrentSku.split(":")[0].trim();
        } else if (descOfCurrentSku.contains("-") && !Constants.productDetailsObj.getprod_size().equalsIgnoreCase("yes")) {
            descOfCurrentSku = descOfCurrentSku.split("-")[0].trim();
        }
        label.setText(descOfCurrentSku);


        return row;
    }
}