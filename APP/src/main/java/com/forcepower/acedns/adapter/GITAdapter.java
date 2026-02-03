package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.GITDetails;

import java.util.ArrayList;

public class GITAdapter extends ArrayAdapter<GITDetails> {

    private final Context context;
    private final ArrayList<GITDetails> nameValues;
    private final int resourceId;
    private ViewHolder viewHolder;

    public GITAdapter(Context context, int resourceId, ArrayList<GITDetails> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        GITDetails currentObj = nameValues.get(position);
        String gitNo = currentObj.getGrnNo();
        String yearVal = gitNo.substring(gitNo.length() - 14, gitNo.length() - 10);
        String monthVal = gitNo.substring(gitNo.length() - 10, gitNo.length() - 8);
        String dayVal = gitNo.substring(gitNo.length() - 8, gitNo.length() - 6);
        String HourVal = gitNo.substring(gitNo.length() - 6, gitNo.length() - 4);
        String MinuteVal = gitNo.substring(gitNo.length() - 4, gitNo.length() - 2);
        String SecondVal = gitNo.substring(gitNo.length() - 2);
        String text = "Despatched by : " + currentObj.getDespatcherName() + "\nDespatched on " + dayVal + "-" + monthVal + "-" + yearVal + " " + HourVal + ":" + MinuteVal + ":" + SecondVal;
        viewHolder.txtView.setText(text);
        return convertView;
    }


    public class ViewHolder {
        TextView txtView;
    }
}
