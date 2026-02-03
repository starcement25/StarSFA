package com.forcepower.acedns.adapter;

import android.content.Context;
import android.graphics.Color;
import androidx.core.text.HtmlCompat;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.NotificationDetails;

import java.util.ArrayList;

public class NotificationAdapter extends ArrayAdapter<NotificationDetails> {
    public final ArrayList<NotificationDetails> nameValues;
    private final Context context;
    private final int resourceId;
    private ViewHolder viewHolder;

    public NotificationAdapter(Context context, int resourceId, ArrayList<NotificationDetails> nameValuesOBJ) {

        super(context, resourceId, nameValuesOBJ);
        this.context = context;
        this.nameValues = nameValuesOBJ;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.ll_rootOBJ = (LinearLayout) convertView.findViewById(R.id.ll_root);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        if (nameValues.get(position).getAckId().length() > 0) {
            viewHolder.txtView.setTextColor(Color.parseColor("#003399"));
        } else {
            viewHolder.txtView.setTextColor(Color.RED);
        }
        String menuItem = nameValues.get(position).getMessage();
        if (menuItem != null && menuItem.length() > 0 && !menuItem.equalsIgnoreCase(" ")) {
            viewHolder.txtView.setText(HtmlCompat.fromHtml(menuItem,HtmlCompat.FROM_HTML_MODE_LEGACY));
        } else {
            viewHolder.txtView.setText("Blank Message..");
        }
        System.out.println("TEST_CASE" + nameValues.get(position).getFlag());
        if (nameValues.get(position).getFlag() == 0) {
            viewHolder.ll_rootOBJ.setBackgroundColor(Color.parseColor("#e6ffe6"));
        } else {
            viewHolder.ll_rootOBJ.setBackgroundColor(Color.parseColor("#DCE8F6"));
        }

        return convertView;
    }

    public class ViewHolder {
        TextView txtView;
        LinearLayout ll_rootOBJ;
    }
}