package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MenuOutstandingChild;
import com.forcepower.acedns.bean.MenuOutstandingParent;
import com.forcepower.acedns.bean.OutstandingDetails;

import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

import static com.forcepower.acedns.constants.Constants.currency;

public class MenuOutstandingExpandableAdapter extends BaseExpandableListAdapter {

    ArrayList<OutstandingDetails> child;
    DecimalFormat formatter = new DecimalFormat("0.00");
    @SuppressWarnings("unused")
    private Activity activity;
    private LayoutInflater inflater;
    private ArrayList<MenuOutstandingParent> parentItems;
    private ArrayList<MenuOutstandingChild> childtems;

    public MenuOutstandingExpandableAdapter(ArrayList<MenuOutstandingParent> parents, ArrayList<MenuOutstandingChild> childern) {
        this.parentItems = parents;
        this.childtems = childern;
    }

    public void setInflater(LayoutInflater inflater, Activity activity) {
        this.inflater = inflater;
        this.activity = activity;
    }

    @Override
    public View getChildView(int groupPosition, final int childPosition, boolean isLastChild, View convertView, ViewGroup parent) {
        child = childtems.get(groupPosition).getOutstandingList();
        TextView txtInvNo, txtDate, txtInvAmt, txtDueAmt, txtDueDays = null;
        LinearLayout parentLayout = null;
        if (convertView == null) {
            convertView = inflater.inflate(R.layout.outstanding_list_child, null);
        }
        parentLayout = (LinearLayout) convertView.findViewById(R.id.parent_layout);
        if (childPosition % 2 == 0) {
            parentLayout.setBackgroundColor(Color.parseColor("#FAEBD7"));
        } else {
            parentLayout.setBackgroundColor(Color.parseColor("#FFE5B4"));
        }
        //parentLayout.setBackgroundColor(Color.parseColor("#CC66FF"));
        txtInvNo = (TextView) convertView.findViewById(R.id.txt_inv);
        txtDate = (TextView) convertView.findViewById(R.id.txt_date);
        txtInvAmt = (TextView) convertView.findViewById(R.id.txt_amt);
        txtDueAmt = (TextView) convertView.findViewById(R.id.txt_due);
        txtDueDays = (TextView) convertView.findViewById(R.id.txt_due_days);

        OutstandingDetails childObj = child.get(childPosition);
        txtInvNo.setText(childObj.getInvoice_id());
        try {
            txtDate.setText(new SimpleDateFormat("dd-MM-yyyy").format(new SimpleDateFormat("yyyy-MM-dd").parse(childObj.getDate())));
        } catch (Exception e) {
            txtDate.setText("Date not available");
        }
        txtInvAmt.setText(currency + formatter.format(Float.parseFloat(childObj.getInvoice_amount())));
        txtDueAmt.setText(currency + formatter.format(Float.parseFloat(childObj.getDue_amount())));
        try {
            txtDueDays.setText("" + ((new Date().getTime() - new SimpleDateFormat("yyyy-MM-dd").parse(childObj.getDate()).getTime()) / (1000 * 60 * 60 * 24)));
        } catch (Exception e) {
            txtDueDays.setText("Unavailable");
        }
        return convertView;
    }


    @Override
    public View getGroupView(int groupPosition, boolean isExpanded, View convertView, ViewGroup parent) {
        TextView txtCust, txtInvoice, txtInvoiceNo = null;
        LinearLayout parentLayout = null;
        ImageView image = null;
        if (convertView == null) {
            convertView = inflater.inflate(R.layout.menu_outstanding_group, null);
        }
        parentLayout = (LinearLayout) convertView.findViewById(R.id.parent);
        if (groupPosition % 2 == 0) {
            parentLayout.setBackgroundColor(Color.parseColor("#DCE8F6"));
        } else {
            parentLayout.setBackgroundColor(Color.parseColor("#b1cef0"));
        }
        txtCust = (TextView) convertView.findViewById(R.id.textView1);
        txtInvoice = (TextView) convertView.findViewById(R.id.textView2);
        txtInvoiceNo = (TextView) convertView.findViewById(R.id.textView21);
        txtCust.setText(parentItems.get(groupPosition).getCustomerName());
        txtInvoice.setText(currency + formatter.format(Float.parseFloat(parentItems.get(groupPosition).getTotalInvoice())));
        txtInvoiceNo.setText(parentItems.get(groupPosition).getNumberOfInvoice());
        image = (ImageView) convertView.findViewById(R.id.img);
        if (isExpanded) {
            image.setBackgroundResource(R.drawable.up_arrow);
        } else {
            image.setBackgroundResource(R.drawable.down_arrow);
        }
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
        return (childtems.get(arg0).getOutstandingList().size());

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
