package com.forcepower.acedns.adapter;

import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.SurveyRoot;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Locale;

/**
 * Created by Force Power Intellij Amiyo  on 06-09-2017.
 * Please follow standard Java coding conventions.
 * http://source.android.com/source/code-style.html
 */
public class ListSearchCheckAdapter extends BaseAdapter {
    private final int resourceId;
    Context mContext;
    LayoutInflater mLayoutInflater;
    String mFinalRowID;
    ArrayList<TextView> mTextViewList;
    private ListSearchCheckAdapter.ViewHolder viewHolder;
    private List<SurveyRoot> al_OLD_data = null;
    private ArrayList<SurveyRoot> al_Search_data;

    public ListSearchCheckAdapter(Context context, int resourceid, List<SurveyRoot> al_OLD_dataOBJ, String mFinalRowID, ArrayList<TextView> mTextViewList) {
        mContext = context;
        resourceId = resourceid;
        this.mFinalRowID = mFinalRowID;
        this.mTextViewList = mTextViewList;
        this.al_OLD_data = al_OLD_dataOBJ;
        mLayoutInflater = (LayoutInflater) mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE);

        this.al_Search_data = new ArrayList<SurveyRoot>();
        this.al_Search_data.addAll(al_OLD_data);
    }

    @Override
    public SurveyRoot getItem(int position) {
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

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ListSearchCheckAdapter.ViewHolder();
            viewHolder.txtView = (TextView) convertView.findViewById(R.id.list_details);
            viewHolder.editText = (EditText) convertView.findViewById(R.id.et_qty);
            viewHolder.mCheckBox = (CheckBox) convertView.findViewById(R.id.checkBox1);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ListSearchCheckAdapter.ViewHolder) convertView.getTag();
        }

        String name = al_OLD_data.get(position).getName();
        Boolean checked = currentValueAlreadyChecked(name);
        viewHolder.txtView.setText(name);
        viewHolder.mCheckBox.setChecked(true);

        if(Constants.nickName.equalsIgnoreCase("SHAKTI")){
           // viewHolder.editText.setVisibility(View.VISIBLE);
        }


        return convertView;
    }

    private Boolean currentValueAlreadyChecked(String name) {
        Boolean checked = false;
        try {
            if (mTextViewList != null) {
                int currentRowId = -1;

                for (int count = 0; count < mTextViewList.size(); count++) {
                    String rowId = String.valueOf(mTextViewList.get(count).getTag());
                    if (rowId.matches(mFinalRowID)) {
                        currentRowId = count;
                    }
                }
                if (currentRowId > -1) {
                    String[] valueOfData = mTextViewList.get(currentRowId).getText().toString().split(";");

                    if (valueOfData.length > 0) {//&& strings.contains(name)
                        List<String> strings = Arrays.asList(valueOfData);
                        if (strings.contains(name)) {
                            checked = true;
                        }

                    }
                }
            }
        } catch (Exception e) {

        }
        return checked;
    }

    // Filter Class
    public void filter(String charText) {
        Log.d("TAG", "_DOWNLOAD_ : "+charText);
        al_OLD_data.clear();
        if (charText.length() == 0) {
            al_OLD_data.addAll(al_Search_data);
        } else {
            for (SurveyRoot wp : al_Search_data) {
                if (wp.getName().toLowerCase(Locale.getDefault())
                        .contains(charText.toLowerCase(Locale.getDefault()))) {
                    al_OLD_data.add(wp);
                }
            }
        }
        notifyDataSetChanged();
    }

    public class ViewHolder {
        TextView txtView;
        EditText editText;
        CheckBox mCheckBox;
    }


}
