
package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MenuClStkMrpDetails;

import java.util.HashMap;
import java.util.List;

import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;

public class ExpandableListAdapterAslMrpReport extends BaseExpandableListAdapter
{
    String mSaudaType;
    private Context _context;
    private List<String> _listDataHeader; // header titles
    // child data in format of header title, child title
    private HashMap<String, List<MenuClStkMrpDetails>> listDataChild;

    public ExpandableListAdapterAslMrpReport(Context context, List<String> listDataHeader, HashMap<String, List<MenuClStkMrpDetails>> listChildData,String mSaudaType) {
        this._context = context;
        this._listDataHeader = listDataHeader;
        this.listDataChild = listChildData;
        this.mSaudaType = mSaudaType;
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

        MenuClStkMrpDetails ChildTextList = (MenuClStkMrpDetails)getChild(groupPosition, childPosition);
        final ChildViewHolder childViewHolder;
        if (convertView == null)
        {
            childViewHolder = new ChildViewHolder();
            LayoutInflater infalInflater = (LayoutInflater) this._context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.menu_mrp_asl, null);
            childViewHolder.textViewSku = convertView.findViewById(R.id.txt_prod);
            childViewHolder.textViewAmount = convertView.findViewById(R.id.txt_val);
            childViewHolder.txt_capacity = convertView.findViewById(R.id.txt_capacity);
            if(mSaudaType.equalsIgnoreCase("ex"))
            {
                childViewHolder.txt_capacity.setVisibility(View.GONE);
            }
            convertView.setTag(childViewHolder);
        }
        else
        {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }
        if(mSaudaType.equalsIgnoreCase("for"))
        {
            childViewHolder.txt_capacity.setText(ChildTextList.getcapacity());
        }
        childViewHolder.textViewSku.setText(ChildTextList.getProduct());
        childViewHolder.textViewAmount.setText(defaultFormatWithComma.format(Double.parseDouble(ChildTextList.getValue())));
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
        String[] headerTitlesplitted = headerTitle.split("#");
        if (convertView == null) {
            LayoutInflater infalInflater = (LayoutInflater) this._context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.activity_group_header_item_2, null);
        }

        TextView txt_grpName =  convertView.findViewById(R.id.txt_grpName);
        TextView txt_packSize =  convertView.findViewById(R.id.txt_packSize);
        txt_packSize.setTypeface(null, Typeface.BOLD);
        txt_grpName.setTypeface(null, Typeface.BOLD);
        txt_grpName.setText("Group: "+headerTitlesplitted[0]);
        txt_packSize.setText("Pack Size: "+headerTitlesplitted[1]);
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
        TextView  textViewSku, textViewAmount,txt_capacity;
    }
}
