package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.InputType;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MarketFeedback;
import com.forcepower.acedns.constants.Constants;

import java.util.ArrayList;

public class FeedBackAdapter extends ArrayAdapter<MarketFeedback> {

    private final Context mContext;
    private final ArrayList<MarketFeedback> mMarketFeedbackList;
    private final int resourceId;
    private ViewHolder viewHolder;

    public FeedBackAdapter(Context context, int resourceId, ArrayList<MarketFeedback> nameValues) {
        super(context, resourceId, nameValues);
        this.mContext = context;
        this.mMarketFeedbackList = nameValues;
        this.resourceId = resourceId;
    }

    public MarketFeedback getItem(int position) {
        return mMarketFeedbackList.get(position);
    }

    public void SetData(int position, String type, String value) {
        if (position < mMarketFeedbackList.size()) {
            if (type.equalsIgnoreCase("PTD")) {
                mMarketFeedbackList.get(position).setPtd(value);
            }
            if (type.equalsIgnoreCase("PTR")) {
                mMarketFeedbackList.get(position).setPtr(value);
            }
            if (type.equalsIgnoreCase("PTC")) {
                mMarketFeedbackList.get(position).setPtc(value);
            }
            if (type.equalsIgnoreCase("PV")) {
                mMarketFeedbackList.get(position).setPv(value);
            }
        }
    }


    @Override
    public View getView(int position, View convertView, ViewGroup parent) {

        if (convertView == null) {
            LayoutInflater inflater = (LayoutInflater) mContext
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();

            viewHolder.col2 = (LinearLayout) convertView.findViewById(R.id.col2_layout);
            viewHolder.col3 = (LinearLayout) convertView.findViewById(R.id.col3_layout);
            viewHolder.col4 = (LinearLayout) convertView.findViewById(R.id.col4_layout);
            viewHolder.col5 = (LinearLayout) convertView.findViewById(R.id.col5_layout);

            viewHolder.txtProductGroupName = (TextView) convertView.findViewById(R.id.textViewProductGroupName);
            viewHolder.editTextPtd = (EditText) convertView.findViewById(R.id.editTextPTD);
            viewHolder.editTextPtr = (EditText) convertView.findViewById(R.id.editTextPTR);
            viewHolder.editTextPtc = (EditText) convertView.findViewById(R.id.editTextPTC);
            viewHolder.editTextPv = (EditText) convertView.findViewById(R.id.editTextPV);

            viewHolder.editTextPtd.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            viewHolder.editTextPtd.setImeOptions(EditorInfo.IME_ACTION_DONE);

            viewHolder.editTextPtr.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            viewHolder.editTextPtr.setImeOptions(EditorInfo.IME_ACTION_DONE);


            viewHolder.editTextPtc.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            viewHolder.editTextPtc.setImeOptions(EditorInfo.IME_ACTION_DONE);

            viewHolder.editTextPv.setInputType(InputType.TYPE_CLASS_NUMBER | InputType.TYPE_NUMBER_FLAG_DECIMAL);
            viewHolder.editTextPv.setImeOptions(EditorInfo.IME_ACTION_DONE);

            if (Constants.marketFeedbackDetailsObj.getMfCol1().trim().length() > 0) {
                //mTextViewCol1.setText(Constants.marketFeedbackDetailsObj.getMfCol1());
            } else {
                viewHolder.col2.setVisibility(View.GONE);
            }

            if (Constants.marketFeedbackDetailsObj.getMfCol2().trim().length() > 0) {
                //mTextViewCol2.setText(Constants.marketFeedbackDetailsObj.getMfCol2());
            } else {
                viewHolder.col3.setVisibility(View.GONE);
            }

            if (Constants.marketFeedbackDetailsObj.getMfCol3().trim().length() > 0) {
                //mTextViewCol3.setText(Constants.marketFeedbackDetailsObj.getMfCol3());
            } else {
                viewHolder.col4.setVisibility(View.GONE);
            }

            if (Constants.marketFeedbackDetailsObj.getMfCol4().trim().length() > 0) {
                //mTextViewCol4.setText(Constants.marketFeedbackDetailsObj.getMfCol4());
            } else {
                viewHolder.col5.setVisibility(View.GONE);
            }


            viewHolder.editTextPtd.setId(position);
            viewHolder.editTextPtr.setId(position);
            viewHolder.editTextPtc.setId(position);
            viewHolder.editTextPv.setId(position);

            viewHolder.editTextPtd.setText("");
            viewHolder.editTextPtr.setText("");
            viewHolder.editTextPtc.setText("");
            viewHolder.editTextPv.setText("");


            viewHolder.editTextPtd.setOnKeyListener(new EditText.OnKeyListener() {
                @Override
                public boolean onKey(View v, int keyCode, KeyEvent event) {
                    final int position = v.getId();
                    final EditText Caption = (EditText) v;
                    String value = Caption.getText().toString();
                    SetData(position, "PTD", value);
                    Log.i("PTD", "Position " + position + " Value " + value);
                    return false;
                }
            });

            viewHolder.editTextPtr.setOnKeyListener(new EditText.OnKeyListener() {
                @Override
                public boolean onKey(View v, int keyCode, KeyEvent event) {
                    final int position = v.getId();
                    final EditText Caption = (EditText) v;
                    String value = Caption.getText().toString();
                    SetData(position, "PTR", value);
                    Log.i("PTR", "Position " + position + " Value " + value);
                    return false;
                }
            });

            viewHolder.editTextPtc.setOnKeyListener(new EditText.OnKeyListener() {
                @Override
                public boolean onKey(View v, int keyCode, KeyEvent event) {
                    final int position = v.getId();
                    final EditText Caption = (EditText) v;
                    String value = Caption.getText().toString();
                    SetData(position, "PTC", value);
                    Log.i("PTC", "Position " + position + " Value " + value);
                    return false;
                }
            });

            viewHolder.editTextPv.setOnKeyListener(new EditText.OnKeyListener() {
                @Override
                public boolean onKey(View v, int keyCode, KeyEvent event) {
                    final int position = v.getId();
                    final EditText Caption = (EditText) v;
                    String value = Caption.getText().toString();
                    SetData(position, "PV", value);
                    Log.i("PV", "Position " + position + " Value " + value);
                    return false;
                }
            });

            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.txtProductGroupName.setText(mMarketFeedbackList.get(position).getCopmpetitorName().toString());

        return convertView;
    }

    public class ViewHolder {
        TextView txtProductGroupName;
        EditText editTextPtd, editTextPtr, editTextPtc, editTextPv;
        LinearLayout col2, col3, col4, col5;
    }
}
