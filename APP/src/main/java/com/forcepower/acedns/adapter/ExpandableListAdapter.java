/*
 * Copyright (C) 2013 Surviving with Android (http://www.survivingwithandroid.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package com.forcepower.acedns.adapter;

import static android.view.View.GONE;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.text.Html;
import android.text.SpannableString;
import android.text.style.UnderlineSpan;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.CategoryClass;
import com.forcepower.acedns.bean.ItemDetailsClass;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.List;

import static com.forcepower.acedns.util.Utils.suffixes;


public class ExpandableListAdapter extends BaseExpandableListAdapter {

    private List<CategoryClass> catList;
    private Context ctx;

    public ExpandableListAdapter(Context activity, List<CategoryClass> catList) {

        this.catList = catList;
        this.ctx = activity;
    }

    @Override
    public Object getChild(int groupPosition, int childPosition) {
        return catList.get(groupPosition).getItemList().get(childPosition);
    }

    @Override
    public long getChildId(int groupPosition, int childPosition) {
        return catList.get(groupPosition).getItemList().get(childPosition).hashCode();
    }

    @Override
    public View getChildView(final int groupPosition, final int childPosition,
                             boolean isLastChild, View convertView, ViewGroup parent) {

        final ChildViewHolder childViewHolder;

        if (convertView == null) {
            childViewHolder = new ChildViewHolder();
            LayoutInflater infalInflater = (LayoutInflater) this.ctx
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = infalInflater.inflate(R.layout.list_item_child, parent, false);

            childViewHolder.tvChallanNo = (TextView) convertView.findViewById(R.id.tvChallanNo);
            childViewHolder.tvDate = (TextView) convertView.findViewById(R.id.tvChildDate);
            childViewHolder.tvChildQty = (TextView) convertView.findViewById(R.id.tvChildQty);
            childViewHolder.tvTrackNo = (TextView) convertView.findViewById(R.id.tvTrackNo);
            childViewHolder.tvDriverCont = (TextView) convertView.findViewById(R.id.tvDriverCont);

            convertView.setTag(childViewHolder);
        } else {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }

        try {
            final ItemDetailsClass det = catList.get(groupPosition).getItemList().get(childPosition);

            childViewHolder.tvChallanNo.setText(det.getSubcategoryId());
            String challanDatValue = det.getRateValue();
            //challanDatValue = "01/22/2022 04:44:30 PM";
            String date = Utils.changeDateFormat("MM/dd/yyyy hh:mm:ss aaa", "dd", challanDatValue);
            int day = 0;
            String dayStr = "";
            try {
                day = Integer.parseInt(date);
                dayStr = day + suffixes[day];
            } catch (Exception e) {

            }

            //childViewHolder.tvDate.setText(dayStr+" "+Utils.changeDateFormat("MM/dd/yyyy hh:mm:ss aaa","MMMM yyyy hh:mm:ss aaa",challanDatValue));
            childViewHolder.tvDate.setText(challanDatValue);
            childViewHolder.tvChildQty.setText(det.getQuantity());

            childViewHolder.tvTrackNo.setText(det.getTrackNumber());

            SpannableString content = new SpannableString(det.getDriverContact() + "");
            content.setSpan(new UnderlineSpan(), 0, content.length(), 0);
            childViewHolder.tvDriverCont.setText(content);
            childViewHolder.tvDriverCont.setOnClickListener(v -> {
                if (!det.getDriverContact().matches("")) {
                    Intent intent = new Intent(Intent.ACTION_DIAL);
                    intent.setData(Uri.parse("tel:" + det.getDriverContact()));
                    ctx.startActivity(intent);
                }
            });

        } catch (Exception e) {
            e.printStackTrace();
        }

        return convertView;

    }

    @Override
    public int getChildrenCount(int groupPosition) {
        int size = catList.get(groupPosition).getItemList().size();
        return size;
    }

    @Override
    public Object getGroup(int groupPosition) {
        return catList.get(groupPosition);
    }

    @Override
    public int getGroupCount() {
        return catList.size();
    }

    @Override
    public long getGroupId(int groupPosition) {
        return catList.get(groupPosition).hashCode();
    }

    @SuppressLint("NewApi")
    @Override
    public View getGroupView(int groupPosition, boolean isExpanded, View convertView, ViewGroup parent) {
        View v = convertView;

        if (v == null) {
            LayoutInflater inflater = (LayoutInflater) ctx.getSystemService
                    (Context.LAYOUT_INFLATER_SERVICE);
            v = inflater.inflate(R.layout.list_group_header, parent, false);
        }

        TextView groupName = (TextView) v.findViewById(R.id.lblListHeader);
        TextView tvStatus = (TextView) v.findViewById(R.id.tvStatus);
        TextView tvHeaderQty = (TextView) v.findViewById(R.id.tvHeaderQty);
        TextView tvProdName = (TextView) v.findViewById(R.id.tvProdName);
        TextView tvErpOrderNo = (TextView) v.findViewById(R.id.tvErpOrderNo);
        TextView tvDate = (TextView) v.findViewById(R.id.tvDate);
        ImageView iv_Collapse_Expand = (ImageView) v.findViewById(R.id.iv_Collapse_Expand);

        TextView tvAddress = v.findViewById(R.id.tvAddress);
        TextView tvFreight = v.findViewById(R.id.tvFreight);
        TextView tvPlantName = v.findViewById(R.id.tvPlantName);


        CategoryClass cat = catList.get(groupPosition);

        groupName.setText(cat.getCategoryId());
        tvStatus.setText(cat.setCategoryName());
        if (cat.setCategoryName().equalsIgnoreCase("order received")) {
            tvStatus.setTextColor(ctx.getResources().getColor(R.color.grey));
        } else if (cat.setCategoryName().equalsIgnoreCase("dispatched")) {
            tvStatus.setTextColor(Color.parseColor("#3a8a00")); //green
        } else if (cat.setCategoryName().equalsIgnoreCase("do approved")) {
            tvStatus.setTextColor(Color.parseColor("#edbe00")); //yellow
        }
        String qty = cat.getQty() + "";
        if (qty.matches("") || qty.equalsIgnoreCase("null")) {
            qty = "0";
        }

        tvHeaderQty.setText("X " + qty);
        tvErpOrderNo.setText(cat.geterporderno());
        tvProdName.setText(cat.getprod_desc());
        String headerDate = cat.geterporderdt();
        if (cat.getAddress().isEmpty()) {
            tvAddress.setVisibility(GONE);
        } else {
            tvAddress.setText(Html.fromHtml("<b>Destination</b> : " + cat.getAddress(), Html.FROM_HTML_MODE_LEGACY));
        }
        if (cat.getFreight().isEmpty()) {
            tvFreight.setVisibility(GONE);
        } else {
            tvFreight.setText(Html.fromHtml("<b>Freight</b> : " + cat.getFreight(), Html.FROM_HTML_MODE_LEGACY));
        }
        if (cat.getPlant_name().isEmpty()) {
            tvPlantName.setVisibility(GONE);
        } else {
            if (cat.getFreight().equalsIgnoreCase("exw"))
                tvPlantName.setText(Html.fromHtml("<b>Dump Name</b> : " + cat.getPlant_name(), Html.FROM_HTML_MODE_LEGACY));
            else
                tvPlantName.setText(Html.fromHtml("<b>Plant Name</b> : " + cat.getPlant_name(), Html.FROM_HTML_MODE_LEGACY));
        }

//        String date=Utils.changeDateFormat("MM/dd/yyyy hh:mm:ss aaa","dd",headerDate);
//        int day = 0;
//        String dayStr ="";
//        try
//        {
//            day = Integer.parseInt(date);
//            dayStr = day + suffixes[day];
//        }
//        catch (Exception e)
//        {
//
//        }
//        tvDate.setText(cat.geterporderdt());
//        tvDate.setText(dayStr+" "+Utils.changeDateFormat("MM/dd/yyyy hh:mm:ss aaa","MMMM yyyy hh:mm:ss aaa",headerDate));
        tvDate.setText(headerDate);


        if (isExpanded) {
            iv_Collapse_Expand.setImageResource(R.drawable.uu);
        } else {
            iv_Collapse_Expand.setImageResource(R.drawable.d_arrow);
        }

        if (catList.get(groupPosition).getItemList().size() > 0) {
            iv_Collapse_Expand.setVisibility(View.VISIBLE);
        } else {
            iv_Collapse_Expand.setVisibility(View.INVISIBLE);
        }

        return v;

    }


    static class ChildViewHolder {
        TextView tvChallanNo, tvDate, tvChildQty, tvTrackNo, tvDriverCont;
    }

    @Override
    public boolean hasStableIds() {
        return true;
    }

    @Override
    public boolean isChildSelectable(int groupPosition, int childPosition) {
        return true;
    }

    public void setFilter(List<CategoryClass> countryModels) {
        catList = new ArrayList<CategoryClass>();
        catList.addAll(countryModels);
        notifyDataSetChanged();
    }
}