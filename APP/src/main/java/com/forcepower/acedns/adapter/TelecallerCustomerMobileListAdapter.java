package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.CommonModel;

import java.util.ArrayList;


public class TelecallerCustomerMobileListAdapter extends BaseAdapter
{

	private Activity context;
	private ArrayList<CommonModel> menu_item_list = new ArrayList<>();

	public TelecallerCustomerMobileListAdapter(final Activity context, final ArrayList<CommonModel> menu_item_list_)
	{
		this.context = context;
		this.menu_item_list = menu_item_list_;
	}

	@Override
	public int getCount()
	{
		return menu_item_list.size();
	}

	@Override
	public CommonModel getItem(int position)
	{
		return menu_item_list.get(position);
	}

	@Override
	public long getItemId(int position) {

		return 0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent)
	{
		try
		{
			ViewHolder holder ;

			if (convertView == null)
			{
				final LayoutInflater mInflater = (LayoutInflater) context
						.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

				convertView = mInflater.inflate(R.layout.existing_product_telecaller_list_child, null);
				holder = new ViewHolder();

				holder.tvProduct = (TextView) convertView.findViewById(R.id.list_details);
				holder.tvLifeProduct = (TextView) convertView.findViewById(R.id.etBrand);
				holder.tvBrand = (TextView) convertView.findViewById(R.id.etLifeProduct);


				convertView.setTag(holder);
			}
			else
			{
				holder = (ViewHolder) convertView.getTag();
			}
			holder.tvProduct.setText(menu_item_list.get(position).getCom1().toString());
			holder.tvBrand.setVisibility(View.GONE);
			holder.tvLifeProduct.setText(menu_item_list.get(position).getCom2());
			/*if(!menu_item_list.get(position).getDate().matches("No Product")) {
				holder.tvProduct.setText(menu_item_list.get(position).getDate());
				holder.tvProduct.setText(menu_item_list.get(position).getBrand());
				holder.tvProduct.setText(menu_item_list.get(position).getLife());
			}else{
				holder.tvProduct.setText(menu_item_list.get(position).getDate());
			}*/
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		return convertView;
	}

	public void setFilter(final ArrayList<CommonModel> menu_item_list_)
	{
		menu_item_list = new ArrayList<>();
		menu_item_list.addAll(menu_item_list_);
		notifyDataSetChanged();
	}

	public class ViewHolder
	{
		private TextView tvProduct,tvBrand,tvLifeProduct;


	}
}
