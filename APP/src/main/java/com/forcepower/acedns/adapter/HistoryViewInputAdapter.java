package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.KeyValue;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

import static com.forcepower.acedns.constants.Constants.defaultFormatWithComma;

public class HistoryViewInputAdapter extends ArrayAdapter<KeyValue> implements Filterable {

    private final Context context;
    public static  ArrayList<KeyValue> mOrderReportDetailsList;
    private final int resourceId;
    private ViewHolder viewHolder;


    public HistoryViewInputAdapter(Context context, int resourceId, ArrayList<KeyValue> orderReportDetailsList ) {
        super(context, resourceId, orderReportDetailsList);
        this.context = context;
        this.mOrderReportDetailsList = orderReportDetailsList;
        this.resourceId = resourceId;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {


            LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(resourceId, parent, false);
            viewHolder = new ViewHolder();
            viewHolder.mTextViewSku =  convertView.findViewById(R.id.textViewSku);
            viewHolder.invisibleTVProdCode = convertView.findViewById(R.id.invisibleTVProdCode);
            viewHolder.textViewRate =  convertView.findViewById(R.id.textViewRate);
            viewHolder.textViewAmount =  convertView.findViewById(R.id.textViewAmount);
            viewHolder.etQty =  convertView.findViewById(R.id.etQty);
            convertView.setTag(viewHolder);

        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();
        if (invisibleProdCodeText == null || invisibleProdCodeText.equals(""))
        {
            viewHolder.etQty.addTextChangedListener(new GenericTextWatcherQty(position,viewHolder.textViewAmount));
        }
        String prodCode = mOrderReportDetailsList.get(position).getmShowColumn1();

        viewHolder.textViewRate.setText(mOrderReportDetailsList.get(position).getmShowColumn4());
        viewHolder.invisibleTVProdCode.setText(prodCode);
        viewHolder.mTextViewSku.setText(mOrderReportDetailsList.get(position).getmShowColumn2()+" "+mOrderReportDetailsList.get(position).getmShowColumn3());
        String currentqty = mOrderReportDetailsList.get(position).getEnteredValue();
        if (!currentqty.matches("NA"))
        {
            viewHolder.etQty.setText(currentqty);
        }
        Double qty=0.00,rate=0.00,amount=0.00;
        if(Utils.isNumeric(currentqty))
        {
            qty=Double.parseDouble(currentqty);
        }
        if(Utils.isNumeric(mOrderReportDetailsList.get(position).getmShowColumn4()))
        {
            rate=Double.parseDouble(mOrderReportDetailsList.get(position).getmShowColumn4());
        }
        amount=qty*rate;
        viewHolder.textViewAmount.setText( defaultFormatWithComma.format(amount));
        return convertView;
    }

    private class GenericTextWatcherQty implements TextWatcher {

        private int pos;
        TextView tv;
        private GenericTextWatcherQty(int pos,TextView tv) {
            this.pos = pos;
            this.tv = tv;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            Double currentValDouble=0.00,currentInput=0.00,curretRateDouble=0.00;
//            String currentVal=mOrderReportDetailsList.get(pos).getEnteredValue();
            String currentRate=mOrderReportDetailsList.get(pos).getmShowColumn4();

//            String currentVal=mOrderReportDetailsList.get(pos).get();
            if(Utils.isNumeric(text))
            {
                currentInput= Double.parseDouble(text);
            }
            if(Utils.isNumeric(currentRate))
            {
                curretRateDouble= Double.parseDouble(currentRate);
            }
            mOrderReportDetailsList.get(pos).setEnteredValue(text);
//            if(currentInput!=currentValDouble)
//            {
//
//                notifyDataSetChanged();
//            }
            Double amount=currentInput*curretRateDouble;
            tv.setText( defaultFormatWithComma.format(amount));

        }
    }
    public class ViewHolder {
        TextView mTextViewSku,invisibleTVProdCode,textViewRate,textViewAmount;
        EditText etQty;
    }


}