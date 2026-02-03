package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;
import java.util.List;
import java.util.Locale;

import com.forcepower.acedns.activity.non_auth.main_menu.market_overview.SurveyActivity;

public class KeyValueNormalAdapter extends BaseAdapter /*ArrayAdapter<KeyValue> implements Filterable*/
{

    private final int resourceId;
    Context mContext;
    LayoutInflater mLayoutInflater;
    private KeyValueNormalAdapter.ViewHolder viewHolder;
    private List<KeyValue> al_OLD_data = null;
    private ArrayList<KeyValue> al_Search_data;
    AceDnsDatabase mAceDnsDatabase;
    int ShowColumnCount=3;

    public KeyValueNormalAdapter(Context context, int resourceId, ArrayList<KeyValue> al_OLD_dataOBJ) {
        //super(context,resourceId,keyValueList);
        this.mContext = context;
        this.al_OLD_data = al_OLD_dataOBJ;
        //this.mFinalKeyValueList= keyValueList;
        this.resourceId = resourceId;

        this.al_Search_data = new ArrayList<KeyValue>();
        this.al_Search_data.addAll(al_OLD_data);
        mAceDnsDatabase = new AceDnsDatabase(context);
    }
    public KeyValueNormalAdapter(Context context, int resourceId, ArrayList<KeyValue> al_OLD_dataOBJ,int ShowColumnCount) {
        //super(context,resourceId,keyValueList);
        this.mContext = context;
        this.al_OLD_data = al_OLD_dataOBJ;
        //this.mFinalKeyValueList= keyValueList;
        this.resourceId = resourceId;
        this.ShowColumnCount = ShowColumnCount;

        this.al_Search_data = new ArrayList<KeyValue>();
        this.al_Search_data.addAll(al_OLD_data);
        mAceDnsDatabase = new AceDnsDatabase(context);
    }


    @Override
    public KeyValue getItem(int position) {
        return al_OLD_data.get(position);
    }

    @Override
    public int getCount() {
        return al_OLD_data.size();
    }


    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null)
        {
            LayoutInflater inflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new KeyValueNormalAdapter.ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.label);
            viewHolder.imageViewProd =  convertView.findViewById(R.id.imageViewProd);
            if(SurveyActivity.mParentType.equalsIgnoreCase("imageview"))
            {
                viewHolder.imageViewProd.setVisibility(View.VISIBLE);
            }
            convertView.setTag(viewHolder);
        }
        else
        {
            viewHolder = (KeyValueNormalAdapter.ViewHolder) convertView.getTag();
        }

        String value = al_OLD_data.get(position).getValue();
        if(ShowColumnCount==5 || ShowColumnCount==4)
        {
            String showColOne = al_OLD_data.get(position).getmShowColumn1().trim();

            if(showColOne.length()>1 && !showColOne.matches(" "))
            {
                value=value+", "+ showColOne;
            }
            if(ShowColumnCount==5)
            {
                String showColTwo = al_OLD_data.get(position).getmShowColumn2().trim();
                if(showColTwo.length()>1 && !showColTwo.matches(" "))
                {
                    value=value+", "+ showColTwo;
                }
            }

        }
        viewHolder.txtView.setText(value);
        if(SurveyActivity.mParentType.equalsIgnoreCase("imageview"))
        {
            String[] fileNameAray = mAceDnsDatabase.getImageNameFromSampleTable(value).split("/");
            String picName=fileNameAray[fileNameAray.length-1];
            Utils.setImageOnImageView(Utils.getAppStoragePath(mContext)+ picName,viewHolder.imageViewProd) ;
        }
        return convertView;
    }

    // Filter Class
    public void filter(String charText) {

        al_OLD_data.clear();
        if (charText.length() == 0) {
            al_OLD_data.addAll(al_Search_data);
        } else {
            for (KeyValue wp : al_Search_data) {
                String searchItem = wp.getValue().toLowerCase(Locale.getDefault());
                if(ShowColumnCount==4)
                {
                    searchItem=searchItem+", "+wp.getmShowColumn1().toLowerCase(Locale.getDefault());
                }
                else if(ShowColumnCount==5)
                {
                    searchItem=searchItem+", "+wp.getmShowColumn1().toLowerCase(Locale.getDefault())+", "+wp.getmShowColumn1().toLowerCase(Locale.getDefault());
                }
                if (searchItem
                        .contains(charText.toLowerCase(Locale.getDefault()))) {
                    al_OLD_data.add(wp);
                }
            }
        }
        notifyDataSetChanged();
    }

    public class ViewHolder {
        TextView txtView;
        ImageView imageViewProd;
    }


}

