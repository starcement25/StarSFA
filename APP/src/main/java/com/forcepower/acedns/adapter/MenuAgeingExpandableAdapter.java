package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.AgeingDetails;
import com.forcepower.acedns.bean.MenuAegingListChild;
import com.forcepower.acedns.bean.MenuOutstandingParent;

import java.text.DecimalFormat;
import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.currency;

public class MenuAgeingExpandableAdapter extends BaseExpandableListAdapter {

    ArrayList<AgeingDetails> child;
    DecimalFormat formatter = new DecimalFormat("0.00");
    String period1, period2, period3;
    private LayoutInflater inflater;
    private ArrayList<MenuOutstandingParent> parentItems;
    private ArrayList<MenuAegingListChild> childtems;

    public MenuAgeingExpandableAdapter(ArrayList<MenuOutstandingParent> parents, ArrayList<MenuAegingListChild> childern,
                                       String period1, String period2, String period3) {
        this.parentItems = parents;
        this.childtems = childern;

        this.period1 = period1;
        this.period2 = period2;
        this.period3 = period3;

    }

    public void setInflater(LayoutInflater inflater, Activity activity) {
        this.inflater = inflater;
    }

    @Override
    public View getChildView(int groupPosition, final int childPosition, boolean isLastChild, View convertView, ViewGroup parent) {
        child = childtems.get(groupPosition).getAgeingChildList();
        TextView txtParty, txtInv, txtPeriod1, txtPeriod2, txtPeriod3 = null;
        if (convertView == null) {
            convertView = inflater.inflate(R.layout.ageing_list_child, null);
        }
        txtParty = (TextView) convertView.findViewById(R.id.party);
        txtInv = (TextView) convertView.findViewById(R.id.inv);
        txtPeriod1 = (TextView) convertView.findViewById(R.id.period1);
        txtPeriod2 = (TextView) convertView.findViewById(R.id.period2);
        txtPeriod3 = (TextView) convertView.findViewById(R.id.period3);

        txtParty.setText(child.get(childPosition).getParty());
        txtInv.setText(child.get(childPosition).getInvNo());

        AgeingDetails detailObj = child.get(childPosition);
        if (detailObj.getPeriod1().length() > 0) {
            txtPeriod1.setText(currency + new DecimalFormat("0.00").format(Double.parseDouble(detailObj.getPeriod1())));
        } else {
            txtPeriod1.setText("-");
        }
        if (detailObj.getPeriod2().length() > 0) {
            txtPeriod2.setText(currency + new DecimalFormat("0.00").format(Double.parseDouble(detailObj.getPeriod2())));
        } else {
            txtPeriod2.setText("-");
        }
        if (detailObj.getPeriod3().length() > 0) {
            txtPeriod3.setText(currency + new DecimalFormat("0.00").format(Double.parseDouble(detailObj.getPeriod3())));
        } else {
            txtPeriod3.setText("-");
        }
        return convertView;
    }


    @Override
    public View getGroupView(int groupPosition, boolean isExpanded, View convertView, ViewGroup parent) {
        TextView txtCust, txtInvoice, txtInvoiceNo = null;
        LinearLayout parentLayout = null;
        if (convertView == null) {
            convertView = inflater.inflate(R.layout.menu_ageing_group, null);
        }
        parentLayout = (LinearLayout) convertView.findViewById(R.id.parent);
        parentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        txtCust = (TextView) convertView.findViewById(R.id.textView1);
        txtInvoice = (TextView) convertView.findViewById(R.id.textView2);
        txtInvoiceNo = (TextView) convertView.findViewById(R.id.textView21);
        txtCust.setText(parentItems.get(groupPosition).getCustomerName());
        txtInvoice.setText(currency + formatter.format(Float.parseFloat(parentItems.get(groupPosition).getTotalInvoice())));
        txtInvoiceNo.setText(parentItems.get(groupPosition).getNumberOfInvoice());

        TextView title1 = (TextView) convertView.findViewById(R.id.txt_period1);
        title1.setText("0 - " + period1);
        TextView title2 = (TextView) convertView.findViewById(R.id.txt_period2);
        title2.setText(Integer.parseInt(period1) + 1 + " - " + period2);
        TextView title3 = (TextView) convertView.findViewById(R.id.txt_period3);
        title3.setText(Integer.parseInt(period2) + 1 + "-" + period3);


        return convertView;
    }

    @Override
    public Object getChild(int arg0, int arg1) {
        return null;
    }

    @Override
    public long getChildId(int arg0, int arg1) {
        return 0;
    }

    @Override
    public int getChildrenCount(int arg0) {
        return (childtems.get(arg0).getAgeingChildList().size());

    }

    @Override
    public Object getGroup(int arg0) {
        return null;
    }

    @Override
    public int getGroupCount() {
        return parentItems.size();

    }

    @Override
    public long getGroupId(int arg0) {
        return 0;
    }

    @Override
    public boolean hasStableIds() {
        return false;
    }

    @Override
    public boolean isChildSelectable(int arg0, int arg1) {
        return true;
    }


}
