package com.forcepower.acedns.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.MenuClStkMrpDetails;
import com.forcepower.acedns.util.Utils;

import java.text.DecimalFormat;
import java.util.ArrayList;

public class SaudaMRPAdapter extends ArrayAdapter<MenuClStkMrpDetails> implements Filterable {

    private final Context context;
    private final ArrayList<MenuClStkMrpDetails> nameValues;
    private final int resourceId;
    boolean isMrp = false;
    Boolean isIncotermsVertical = false;
    private ArrayList<MenuClStkMrpDetails> finalValues;
    private ItemFilter mFilter = new ItemFilter();
    private ViewHolder viewHolder;

    public SaudaMRPAdapter(Context context, int resourceId, ArrayList<MenuClStkMrpDetails> nameValues, boolean isMrp) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.finalValues = nameValues;
        this.resourceId = resourceId;
        this.isMrp = isMrp;
    }

    public SaudaMRPAdapter(Context context, int resourceId, ArrayList<MenuClStkMrpDetails> nameValues, boolean isMrp, boolean isIncotermsVertical) {
        super(context, resourceId, nameValues);
        this.context = context;
        this.nameValues = nameValues;
        this.finalValues = nameValues;
        this.resourceId = resourceId;
        this.isIncotermsVertical = isIncotermsVertical;
        this.isMrp = isMrp;
    }

    public int getCount() {
        return finalValues.size();
    }

    public MenuClStkMrpDetails getItem(int position) {
        return finalValues.get(position);
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
//			viewHolder.txt_val_ex_depot = (TextView) convertView.findViewById(R.id.txt_val_ex_depot);
//			if(isIncotermsVertical)
//			{
//				viewHolder.txt_val_ex_depot.setVisibility(View.VISIBLE);
//			}
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.txtProduct.setText(finalValues.get(position).getProduct());
        if (isIncotermsVertical) {
            String basicRate = finalValues.get(position).getValue();
            String primaryFreight = finalValues.get(position).getPrimaryFreight();
            String depotCost = finalValues.get(position).getDepotCost();
            String honeyCombCost = finalValues.get(position).getHoneycombCost();
            String marginCost = finalValues.get(position).getMarginCost();
            double exPlantDepotRate = 0.00;
            if (Utils.isNumeric(basicRate) && Double.parseDouble(basicRate) > 0) {
                exPlantDepotRate = Double.parseDouble(basicRate);
                if (Utils.isNumeric(honeyCombCost)) {
                    exPlantDepotRate = exPlantDepotRate + Double.parseDouble(honeyCombCost);
                }
                if (Utils.isNumeric(depotCost)) {
                    exPlantDepotRate = exPlantDepotRate + Double.parseDouble(depotCost);
                }
                if (Utils.isNumeric(primaryFreight)) {
                    exPlantDepotRate = exPlantDepotRate + Double.parseDouble(primaryFreight);
                }
                if (Utils.isNumeric(marginCost)) {
                    exPlantDepotRate = exPlantDepotRate + Double.parseDouble(marginCost);
                }
                viewHolder.txtValue.setText(new DecimalFormat("0.00").format(exPlantDepotRate));
            } else {
                viewHolder.txtValue.setText("0.00");
            }


//			viewHolder.txt_val_ex_depot.setText(new DecimalFormat("0.00").format(exDepotRate));
        } else if (!isMrp) {
            viewHolder.txtValue.setText(finalValues.get(position).getValue());
        } else {
            String mrp = finalValues.get(position).getValue();
            if (!Utils.isNumeric(mrp)) {
                mrp = "0";
            }
            viewHolder.txtValue.setText(new DecimalFormat("0.00").format(Double.parseDouble(mrp)));
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

            final ArrayList<MenuClStkMrpDetails> brokermasterlist = nameValues;

            int count = brokermasterlist.size();
            final ArrayList<MenuClStkMrpDetails> newMRPlist = new ArrayList<MenuClStkMrpDetails>(count);

            String filterableString;

            for (int i = 0; i < count; i++) {
                filterableString = brokermasterlist.get(i).getProduct();
                if (filterableString.toLowerCase().contains(filterString)) {
                    newMRPlist.add(brokermasterlist.get(i));
                }
            }

            results.values = newMRPlist;
            results.count = newMRPlist.size();

            return results;
        }

        @SuppressWarnings("unchecked")
        @Override
        protected void publishResults(CharSequence constraint, FilterResults results) {
            finalValues = (ArrayList<MenuClStkMrpDetails>) results.values;
            notifyDataSetChanged();
        }

    }
}
