package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.BranchWisePdfMaster;

import java.util.ArrayList;

public class SchemePdfBranchSelectionAdapter extends ArrayAdapter<BranchWisePdfMaster> {

    private final Context context;
    private final int resourceId;
    private ArrayList<BranchWisePdfMaster> mFinalVerticalList;
    private ViewHolder viewHolder;
    private Boolean isShowingSubList;


    public SchemePdfBranchSelectionAdapter(Context context, int resourceId, ArrayList<BranchWisePdfMaster> customerDetailsList,Boolean isShowingSubList) {
        super(context, resourceId, customerDetailsList);
        this.context = context;
        this.mFinalVerticalList = customerDetailsList;
        this.resourceId = resourceId;
        this.isShowingSubList = isShowingSubList;
    }

    public int getCount() {
        return mFinalVerticalList.size();
    }


    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.HiddenValue1 = (TextView) convertView.findViewById(R.id.HiddenValue1);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if(isShowingSubList)
        {
            int count=position+1;
            viewHolder.txtView.setText("Scheme " +count);
        }
        else
        {
            viewHolder.txtView.setText(mFinalVerticalList.get(position).getbranch_name());
        }


        viewHolder.HiddenValue1.setText(mFinalVerticalList.get(position).getPDF_file_name());

        return convertView;
    }


    public class ViewHolder {
        TextView txtView,HiddenValue1;
    }

}