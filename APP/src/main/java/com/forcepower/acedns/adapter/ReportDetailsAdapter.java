package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ReportDetails;

import java.text.DecimalFormat;
import java.util.ArrayList;

public class ReportDetailsAdapter extends ArrayAdapter<ReportDetails> {

    private final Context context;
    private final ArrayList<ReportDetails> nameValues;
    private final int resourceId;
    DecimalFormat defaultFormat = new DecimalFormat("0.00");
    private ViewHolder viewHolder;

    public ReportDetailsAdapter(Context context, int resourceId, ArrayList<ReportDetails> nameValues) {
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
            viewHolder.col1 = (LinearLayout) convertView.findViewById(R.id.col1_layout);
            viewHolder.col2 = (LinearLayout) convertView.findViewById(R.id.col2_layout);
            viewHolder.col3 = (LinearLayout) convertView.findViewById(R.id.col3_layout);

            viewHolder.txtName = (TextView) convertView.findViewById(R.id.txt_col1);
            viewHolder.txtAmt = (TextView) convertView.findViewById(R.id.txt_col2);
            viewHolder.txtPayMode = (TextView) convertView.findViewById(R.id.txt_col3);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        if (nameValues.get(position).getTransmitted().equalsIgnoreCase("1")) {
            viewHolder.txtName.setTextColor(Color.BLUE);
            viewHolder.txtAmt.setTextColor(Color.BLUE);
            viewHolder.txtPayMode.setTextColor(Color.BLUE);
        } else {
            viewHolder.txtName.setTextColor(Color.RED);
            viewHolder.txtAmt.setTextColor(Color.RED);
            viewHolder.txtPayMode.setTextColor(Color.RED);
        }

        if (nameValues.get(position).getQty().equalsIgnoreCase("")
                && nameValues.get(position).getInvoiceNo().equalsIgnoreCase("")) {
            viewHolder.txtName.setText(nameValues.get(position).getProdCode());
            viewHolder.col1.setVisibility(View.VISIBLE);
            viewHolder.col2.setVisibility(View.GONE);
            viewHolder.col3.setVisibility(View.GONE);
        } else if (!nameValues.get(position).getQty().equalsIgnoreCase("")
                && nameValues.get(position).getInvoiceNo().equalsIgnoreCase("")) {
            viewHolder.txtName.setText(nameValues.get(position).getProdCode());
            viewHolder.txtAmt.setText(nameValues.get(position).getQty());
            String mainStr = defaultFormat.format(calculateAmt(nameValues.get(position)));
            viewHolder.txtPayMode.setText("" + mainStr);
            viewHolder.col1.setVisibility(View.VISIBLE);
            viewHolder.col2.setVisibility(View.VISIBLE);
            viewHolder.col3.setVisibility(View.VISIBLE);
        } else if (nameValues.get(position).getQty().equalsIgnoreCase("")
                && !nameValues.get(position).getInvoiceNo().equalsIgnoreCase("")) {
            viewHolder.txtName.setText(nameValues.get(position).getInvoiceNo());
            String totalStr = defaultFormat.format(Double.parseDouble(nameValues.get(position).getInvoiceAmt()));
            viewHolder.txtAmt.setText("" + totalStr);
            viewHolder.txtPayMode.setText("" + defaultFormat.format(Double.parseDouble(nameValues.get(position).getAmount())));
            viewHolder.col1.setVisibility(View.VISIBLE);
            viewHolder.col2.setVisibility(View.VISIBLE);
            viewHolder.col3.setVisibility(View.VISIBLE);
        }
        return convertView;
    }

    public double calculateAmt(ReportDetails currentObj) {
        Double total = 0.00;
        double discount = 0.00;
        double qty = Double.parseDouble(currentObj.getQty());
        double mrp = Double.parseDouble(currentObj.getAmount());
        if (currentObj.getTD() != null) {
            discount = Double.parseDouble(currentObj.getTD());
        }
        total = total + ((mrp * qty) - (mrp * qty * discount / 100));
        return total;
    }

    public class ViewHolder {
        TextView txtName, txtAmt, txtPayMode;
        LinearLayout col1, col2, col3;
    }
}