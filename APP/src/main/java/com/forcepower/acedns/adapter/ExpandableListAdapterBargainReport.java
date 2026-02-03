
package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.CheckedTextView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.OrderReportDetails;
import com.forcepower.acedns.constants.Constants;

import java.util.HashMap;
import java.util.List;

import androidx.core.text.HtmlCompat;
public class ExpandableListAdapterBargainReport extends BaseExpandableListAdapter
{
    private Context _context;
    private List<String> _listDataHeader; // header titles
    // child data in format of header title, child title
    private HashMap<String, List<OrderReportDetails>> listDataChild;

    public ExpandableListAdapterBargainReport(Context context, List<String> listDataHeader, HashMap<String, List<OrderReportDetails>> listChildData) {
        this._context = context;
        this._listDataHeader = listDataHeader;
        this.listDataChild = listChildData;
    }

    @Override
    public Object getChild(int groupPosition, int childPosititon) {
        return this.listDataChild.get(this._listDataHeader.get(groupPosition)).get(childPosititon);
    }

    @Override
    public long getChildId(int groupPosition, int childPosition) {
        return childPosition;
    }

    @Override
    public View getChildView(int groupPosition, final int childPosition,
                             boolean isLastChild, View convertView, ViewGroup parent) {

        OrderReportDetails ChildTextList = (OrderReportDetails)getChild(groupPosition, childPosition);
        final ChildViewHolder childViewHolder;
        if (convertView == null)
        {
            childViewHolder = new ChildViewHolder();
            LayoutInflater infalInflater = (LayoutInflater) this._context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.bargain_report_list_item_child, null);
            childViewHolder.textViewSku = convertView.findViewById(R.id.textViewSku);
            childViewHolder.textViewQty = convertView.findViewById(R.id.textViewQty);
            childViewHolder.textViewAmount = convertView.findViewById(R.id.textViewAmount);
            convertView.setTag(childViewHolder);
        }
        else
        {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }
        childViewHolder.textViewSku.setText(ChildTextList.getdesc());
        if(ChildTextList.getisApproved())
        {
            childViewHolder.textViewSku.setText(HtmlCompat.fromHtml(ChildTextList.getdesc()+", <font color='#D7B56D'>Approved</font>",HtmlCompat.FROM_HTML_MODE_LEGACY));
        }
        childViewHolder.textViewQty.setText(ChildTextList.getQuantity());
        childViewHolder.textViewAmount.setText(Constants.defaultFormatWithComma.format(Double.parseDouble(ChildTextList.getAmount())));
        return convertView;
    }

    @Override
    public int getChildrenCount(int groupPosition) {
        return this.listDataChild.get(this._listDataHeader.get(groupPosition)).size();
    }

    @Override
    public Object getGroup(int groupPosition) {
        return this._listDataHeader.get(groupPosition);
    }

    @Override
    public int getGroupCount() {
        return this._listDataHeader.size();
    }

    @Override
    public long getGroupId(int groupPosition) {
        return groupPosition;
    }

    @Override
    public View getGroupView(int groupPosition, boolean isExpanded, View convertView, ViewGroup parent) {
        String headerTitle = (String) getGroup(groupPosition);

        if (convertView == null) {
            LayoutInflater infalInflater = (LayoutInflater) this._context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.activity_group_header_item, null);
        }
        ((CheckedTextView) convertView).setText(headerTitle);
        ((CheckedTextView) convertView).setChecked(isExpanded);
        return convertView;
    }

    @Override
    public boolean hasStableIds() {
        return false;
    }

    @Override
    public boolean isChildSelectable(int groupPosition, int childPosition) {
        return false;
    }
    public class ChildViewHolder
    {
        TextView  textViewSku, textViewQty, textViewAmount;
    }
}
