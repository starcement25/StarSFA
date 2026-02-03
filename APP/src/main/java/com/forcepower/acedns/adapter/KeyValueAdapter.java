package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class KeyValueAdapter extends ArrayAdapter<KeyValue> {

    private final Context context;
    private final ArrayList<KeyValue> values;
    private final int resourceId;
    private ViewHolder viewHolder;
    AceDnsDatabase mAceDnsDatabase;
    Boolean showDateOfNoOrder=false;
    public KeyValueAdapter(Context context, int resourceId, ArrayList<KeyValue> values) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
    }
    public KeyValueAdapter(Context context, int resourceId, ArrayList<KeyValue> values,Boolean showDateOfNoOrder) {
        super(context, resourceId, values);
        this.context = context;
        this.values = values;
        this.resourceId = resourceId;
        this.showDateOfNoOrder = showDateOfNoOrder;
        mAceDnsDatabase = new AceDnsDatabase(context);
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);

            viewHolder = new ViewHolder();
            viewHolder.textViewKey = (TextView) convertView.findViewById(R.id.textViewKey);
            viewHolder.textViewNoOrderDate = (TextView) convertView.findViewById(R.id.textViewNoOrderDate);
            viewHolder.textViewValue = (TextView) convertView.findViewById(R.id.textViewValue);
            viewHolder.timageviewSurvey = (ImageView) convertView.findViewById(R.id.timageviewSurvey);
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (ViewHolder) convertView.getTag();
        }
        viewHolder.textViewKey.setText(values.get(position).getKey());
        if(showDateOfNoOrder){
            viewHolder.textViewNoOrderDate.setVisibility(View.VISIBLE);
            viewHolder.textViewNoOrderDate.setText(values.get(position).getmShowColumn1());
        }

        String value = values.get(position).getValue();
        if(values.get(position).getType().equalsIgnoreCase("imageview"))
        {
            viewHolder.textViewValue.setVisibility(View.GONE);
            viewHolder.timageviewSurvey.setVisibility(View.VISIBLE);
            String[] fileNameAray = mAceDnsDatabase.getImageNameFromSampleTable(value).split("/");
            String picName=fileNameAray[fileNameAray.length-1];
            Utils.setImageOnImageView(Utils.getAppStoragePath(context)+ picName,viewHolder.timageviewSurvey) ;
        }
        else
        {
            viewHolder.textViewValue.setVisibility(View.VISIBLE);
            viewHolder.timageviewSurvey.setVisibility(View.GONE);
            String insertTableDetails = values.get(position).getinsertTableDetail();
            if(value.contains(";") && insertTableDetails !=null && (insertTableDetails.equalsIgnoreCase("independent") || insertTableDetails.contains("insert")))
            {
                String[] splittedOutlet= value.split(";");
                value=splittedOutlet[0];
            }
            viewHolder.textViewValue.setText(value);
        }


        return convertView;
    }

    public class ViewHolder {
        TextView textViewKey;
        TextView textViewValue;
        TextView textViewNoOrderDate;
        ImageView timageviewSurvey;
    }
}
