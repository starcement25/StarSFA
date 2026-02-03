package com.forcepower.acedns.adapter;

import android.content.Context;
import android.text.Html;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;

import com.forcepower.acedns.bean.StockCustomerProduct;
import com.forcepower.acedns.constants.Constants;

import java.text.DecimalFormat;
import java.util.ArrayList;

public class MenuStockCustomerAdapter extends ArrayAdapter<StockCustomerProduct> implements Filterable {

    private final Context context;
    private final int resourceId;
    boolean isMrp = false;
    private ArrayList<StockCustomerProduct> mOriginalMenuClStkMrpDetails;
    private ArrayList<StockCustomerProduct> mFinalMenuClStkMrpDetails;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public MenuStockCustomerAdapter(Context context, int resourceId, ArrayList<StockCustomerProduct> menuClStkMrpDetails, boolean isMrp) {
        super(context, resourceId, menuClStkMrpDetails);
        this.context = context;
        this.mOriginalMenuClStkMrpDetails = menuClStkMrpDetails;
        this.mFinalMenuClStkMrpDetails = menuClStkMrpDetails;
        this.resourceId = resourceId;
        this.isMrp = isMrp;
    }

    public int getCount() {
        return mFinalMenuClStkMrpDetails.size();
    }

    public StockCustomerProduct getItem(int position) {
        return mFinalMenuClStkMrpDetails.get(position);
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
            viewHolder.txtProduct = (TextView) convertView.findViewById(R.id.txt_prod);
            viewHolder.txtValue = (TextView) convertView.findViewById(R.id.txt_val);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        String productDesc = mFinalMenuClStkMrpDetails.get(position).getProductName();
        if (Constants.productDetailsObj.getprod_size().equalsIgnoreCase("yes")) {
            productDesc = productDesc + ", <font color='#F58322'> Size:</font> " + mFinalMenuClStkMrpDetails.get(position).getStock();
        }
        viewHolder.txtProduct.setText(Html.fromHtml(productDesc));
        if (!isMrp) {
            viewHolder.txtValue.setText(mFinalMenuClStkMrpDetails.get(position).getStock());
        } else {
            String mrp = mFinalMenuClStkMrpDetails.get(position).getStock();
            if (mrp.length() > 0) {
                viewHolder.txtValue.setText(context.getString(R.string.Rs) + new DecimalFormat("0.00").format(Double.parseDouble(mrp)));
            }
        }
        return convertView;
    }

    public Filter getFilter() {
        return mFilter;
    }

    public class ViewHolder {
        TextView txtProduct, txtValue;
    }

    private class ItemFilter extends Filter {
        @Override
        protected FilterResults performFiltering(CharSequence constraint) {

            String filterString = constraint.toString().toLowerCase();
            FilterResults results = new FilterResults();

            final ArrayList<StockCustomerProduct> menuClStkMrpDetailslist = mOriginalMenuClStkMrpDetails;
            int count = menuClStkMrpDetailslist.size();
            final ArrayList<StockCustomerProduct> newmenuClStkMrpDetailslist = new ArrayList<StockCustomerProduct>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = menuClStkMrpDetailslist.get(i).getProductName();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newmenuClStkMrpDetailslist.add(menuClStkMrpDetailslist.get(i));
                }
            }
            results.values = newmenuClStkMrpDetailslist;
            results.count = newmenuClStkMrpDetailslist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            mFinalMenuClStkMrpDetails = (ArrayList<StockCustomerProduct>) results.values;
            notifyDataSetChanged();
        }

    }

}