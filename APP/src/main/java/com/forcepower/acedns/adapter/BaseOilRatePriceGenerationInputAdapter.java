package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.content.Context;
import android.text.Editable;
import android.text.Html;
import android.text.TextWatcher;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.commonDatabaseHelper;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsDatabase;
import com.forcepower.acedns.util.Utils;

import java.util.ArrayList;

public class BaseOilRatePriceGenerationInputAdapter extends ArrayAdapter<commonDatabaseHelper>
{
    private final Context context;
    private final int resourceId;
    Activity activity;
    AceDnsDatabase mAceDnsDatabase;
    int sizeOfList = 0;
    private ViewHolder viewHolder;
    public static ArrayList<commonDatabaseHelper> priceListOilGrp;
    private Boolean isRateEditable = false;
    public BaseOilRatePriceGenerationInputAdapter(Context context, int resourceId, ArrayList<commonDatabaseHelper> nameValues) {
        super(context, resourceId, nameValues);
        this.context = context;
        activity = (Activity) context;
        priceListOilGrp = nameValues;
        this.resourceId = resourceId;
        mAceDnsDatabase = new AceDnsDatabase(context);
        sizeOfList = nameValues.size();
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {

        LayoutInflater inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        convertView = inflater.inflate(resourceId, parent, false);
        viewHolder = new ViewHolder();
        viewHolder.txtViewProductDesc = (TextView) convertView.findViewById(R.id.list_details);
        viewHolder.list_details_ll = (LinearLayout) convertView.findViewById(R.id.list_details_ll);
        viewHolder.etRateInput = (EditText) convertView.findViewById(R.id.etRateInput);
        if (Constants.orderFormDetailsObj.getInputScreenSpecial().equalsIgnoreCase("yes")) {
            LinearLayout.LayoutParams lay = (LinearLayout.LayoutParams) viewHolder.list_details_ll.getLayoutParams();
            lay.weight = 0.3f;
            viewHolder.list_details_ll.setLayoutParams(lay);
        }

        viewHolder.invisibleTVProdCode = (TextView) convertView.findViewById(R.id.invisibleTVProdCode);
        convertView.setTag(viewHolder);

        String desc = priceListOilGrp.get(position).getItem0();

        viewHolder.txtViewProductDesc.setText(Html.fromHtml(desc));
//        viewHolder.txtViewProductDesc.setText(HtmlCompat.fromHtml(desc+"<font color='#D7B56D'>/</font>"+nameValuesProductListLocalDo.get(position).getuom4(),HtmlCompat.FROM_HTML_MODE_LEGACY));
        CharSequence invisibleProdCodeText = viewHolder.invisibleTVProdCode.getText();


        if (invisibleProdCodeText == null || invisibleProdCodeText.equals("")) {
            viewHolder.etRateInput.addTextChangedListener(new GenericTextWatcherQty(position));

//            viewHolder.etRateInput.setOnEditorActionListener(new GenericEditorActionListener(position, viewHolder.etRateInput));
//            viewHolder.etRateInput.setOnTouchListener(new GenericEditorOnTouchListener("qty", viewHolder.etRateInput));

        }
        String prodCode = priceListOilGrp.get(position).getItem0();

        viewHolder.invisibleTVProdCode.setText(prodCode);
        String currentqty = priceListOilGrp.get(position).getItem4();
        if (Utils.isNumeric(currentqty) && Double.parseDouble(currentqty)>0)
            viewHolder.etRateInput.setText(currentqty);

        return convertView;
    }


    public class ViewHolder {
        TextView txtViewProductDesc, invisibleTVProdCode;
        EditText etRateInput;
        LinearLayout list_details_ll;
    }

    private class GenericTextWatcherQty implements TextWatcher {

        private int pos;

        private GenericTextWatcherQty(int pos) {
            this.pos = pos;
        }

        public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
        }

        public void afterTextChanged(Editable editable) {
            String text = editable.toString();
            priceListOilGrp.get(pos).setItem4(text);
        }
    }


}