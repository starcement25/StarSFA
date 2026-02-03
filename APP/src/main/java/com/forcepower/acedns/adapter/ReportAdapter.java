package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.ReportData;
import com.forcepower.acedns.constants.Constants;

import java.text.DecimalFormat;
import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.defaultFormat;

public class ReportAdapter extends ArrayAdapter<ReportData> {

    private final Context context;
    private final ArrayList<ReportData> nameValues;
    private final int resourceId;
    DecimalFormat defaultFormat1 = new DecimalFormat("0.000");
    boolean formatReq = false;
    String sales = "";
    private ViewHolder viewHolder;

    public ReportAdapter(Context context, int resourceId,
                         ArrayList<ReportData> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    public ReportAdapter(Context context, int resourceId,
                         ArrayList<ReportData> nameValues, boolean formatReq) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
        this.formatReq = formatReq;
    }

    public ReportAdapter(Context context, int resourceId,
                         ArrayList<ReportData> nameValues, String sales) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
        this.sales = sales;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.col1 = (LinearLayout) convertView
                    .findViewById(R.id.col1_layout);
            viewHolder.col2 = (LinearLayout) convertView
                    .findViewById(R.id.col2_layout);
            viewHolder.col3 = (LinearLayout) convertView
                    .findViewById(R.id.col3_layout);

            viewHolder.txtName = (TextView) convertView
                    .findViewById(R.id.txt_col1);
            viewHolder.txtAmt = (TextView) convertView
                    .findViewById(R.id.txt_col2);
            viewHolder.txtPayMode = (TextView) convertView
                    .findViewById(R.id.txt_col3);
            if (sales.length() > 0) {
                viewHolder.txtAmt.setGravity(Gravity.LEFT);
            } else if (formatReq) {
                viewHolder.txtAmt.setGravity(Gravity.RIGHT);
            } else {
                viewHolder.txtAmt.setGravity(Gravity.CENTER_HORIZONTAL);
            }
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if (nameValues.get(position).getAmount().equalsIgnoreCase("")) {
            viewHolder.txtName.setText(nameValues.get(position)
                    .getCustomerName());
            viewHolder.col1.setVisibility(View.VISIBLE);
            viewHolder.col2.setVisibility(View.GONE);
            viewHolder.col3.setVisibility(View.GONE);
        } else if (nameValues.get(position).getPayMode().equalsIgnoreCase("")) {
            String compltStr = "";
            if (formatReq) {
                compltStr = defaultFormat1.format(Double.parseDouble(nameValues
                        .get(position).getAmount()));
            } else {
                compltStr = defaultFormat.format(Double.parseDouble(nameValues
                        .get(position).getAmount()));
            }
            if (nameValues.get(position).getRdsName().length() == 0) {
                viewHolder.txtName.setText(nameValues.get(position)
                        .getCustomerName());
                viewHolder.col2.setVisibility(View.INVISIBLE);
                viewHolder.txtPayMode.setText("" + compltStr);

                if (nameValues.get(position).getFlag().equalsIgnoreCase("1")) {
                    viewHolder.txtName.setTextColor(Color.BLUE);
                    viewHolder.txtAmt.setTextColor(Color.BLUE);
                    viewHolder.txtPayMode.setTextColor(Color.BLUE);
                } else {
                    viewHolder.txtName.setTextColor(Color.RED);
                    viewHolder.txtAmt.setTextColor(Color.RED);
                    viewHolder.txtPayMode.setTextColor(Color.RED);
                }

            } else {
                viewHolder.txtName.setText(nameValues.get(position)
                        .getCustomerName()
                        + "\n"
                        + nameValues.get(position).getRdsName());
                viewHolder.col2.setVisibility(View.VISIBLE);
                viewHolder.txtAmt.setText("" + compltStr);
                viewHolder.txtPayMode.setText(""
                        + defaultFormat.format(Double.parseDouble(nameValues
                        .get(position).getTransAmt())));

                if (nameValues.get(position).getFlag().equalsIgnoreCase("1")) {
                    viewHolder.txtName.setTextColor(Color.BLUE);
                    viewHolder.txtAmt.setTextColor(Color.BLUE);
                    viewHolder.txtPayMode.setTextColor(Color.BLUE);
                } else {
                    viewHolder.txtName.setTextColor(Color.RED);
                    viewHolder.txtAmt.setTextColor(Color.RED);
                    viewHolder.txtPayMode.setTextColor(Color.RED);
                }

            }
            viewHolder.col1.setVisibility(View.VISIBLE);
            viewHolder.col3.setVisibility(View.VISIBLE);
        } else {
            viewHolder.col1.setVisibility(View.VISIBLE);
            viewHolder.col2.setVisibility(View.VISIBLE);
            viewHolder.col3.setVisibility(View.VISIBLE);
            if (!formatReq && sales.length() == 0) {
                viewHolder.txtName.setText(nameValues.get(position)
                        .getCustomerName());
                String totalStr = defaultFormat.format(Double
                        .parseDouble(nameValues.get(position).getAmount()));
                viewHolder.txtAmt.setText("" + totalStr);
                int paymentModeCode = Integer.parseInt(nameValues.get(
                        position).getPayMode());
//				viewHolder.txtPayMode.setText(paymentModeCode == 0 ? "CASH" : "CHEQUE");
                if (paymentModeCode == 0) {
                    viewHolder.txtPayMode.setText("CASH");
                } else if (paymentModeCode == 5) {
                    viewHolder.txtPayMode.setText("CARD");
                } else {
                    viewHolder.txtPayMode.setText("CHEQUE");
                }

                if (nameValues.get(position).getFlag().equalsIgnoreCase("1")) {
                    viewHolder.txtName.setTextColor(Color.BLUE);
                    viewHolder.txtAmt.setTextColor(Color.BLUE);
                    viewHolder.txtPayMode.setTextColor(Color.BLUE);
                } else {
                    viewHolder.txtName.setTextColor(Color.RED);
                    viewHolder.txtAmt.setTextColor(Color.RED);
                    viewHolder.txtPayMode.setTextColor(Color.RED);
                }

            } else if (!formatReq && sales.length() > 0) {
                ReportData obj = nameValues.get(position);
                viewHolder.txtName.setText(obj.getPayMode());
                viewHolder.txtAmt.setText(obj.getCustomerName());
                String compltStr = "";
                compltStr = defaultFormat.format(Double.parseDouble(obj
                        .getAmount()));
                viewHolder.txtPayMode.setText(compltStr);

                if (nameValues.get(position).getFlag().equalsIgnoreCase("1")) {
                    viewHolder.txtName.setTextColor(Color.BLUE);
                    viewHolder.txtAmt.setTextColor(Color.BLUE);
                    viewHolder.txtPayMode.setTextColor(Color.BLUE);
                } else {
                    viewHolder.txtName.setTextColor(Color.RED);
                    viewHolder.txtAmt.setTextColor(Color.RED);
                    viewHolder.txtPayMode.setTextColor(Color.RED);
                }

            }
        }

        if(Constants.menuDetailsObj.getBusinessProspect().equalsIgnoreCase("checkin")){
            viewHolder.col3.setVisibility(View.VISIBLE);
            viewHolder.col2.setVisibility(View.VISIBLE);
            String id = nameValues.get(position).getTransId().toString();
            String out = id.substring(7,11) + "-"+id.substring(11,13)+ "-"+id.substring(13,15)+ " "+id.substring(15,17)+ ":"+id.substring(17,19);
            viewHolder.txtPayMode.setText(out);
            viewHolder.txtAmt.setText(nameValues.get(position).getCheckIn().toString());
        }

        return convertView;
    }

    public class ViewHolder {
        TextView txtName, txtAmt, txtPayMode;
        LinearLayout col1, col2, col3;
    }
}