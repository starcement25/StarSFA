package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.CommonModel;

import java.util.ArrayList;


public class GroupLeaderCustomerMobileListAdapter extends BaseAdapter
{

	private Activity context;
	private ArrayList<CommonModel> menu_item_list = new ArrayList<>();

	public GroupLeaderCustomerMobileListAdapter(final Activity context, final ArrayList<CommonModel> menu_item_list_)
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

				convertView = mInflater.inflate(R.layout.group_leader_customer_list_child, null);
				holder = new ViewHolder();

				holder.tvProduct = (TextView) convertView.findViewById(R.id.list_details);
				holder.tvLifeProduct = (TextView) convertView.findViewById(R.id.etBrand);
				holder.tvBrand = (TextView) convertView.findViewById(R.id.etLifeProduct);
				holder.img = (ImageView) convertView.findViewById(R.id.iv_check_uncheck_no_product);



				convertView.setTag(holder);
			}
			else
			{
				holder = (ViewHolder) convertView.getTag();
			}
			holder.tvProduct.setText(menu_item_list.get(position).getCom1().toString());
			holder.tvBrand.setVisibility(View.VISIBLE);
			holder.tvBrand.setText(menu_item_list.get(position).getCom3());
			holder.tvLifeProduct.setText(menu_item_list.get(position).getCom2());

			if (menu_item_list.get(position).getCom4().equalsIgnoreCase("1")) {
				holder.img.setImageResource(R.drawable.check);
				//menu_item_list.get(position).setCom4("1");
			} else {
				holder.img.setImageResource(R.drawable.uncheck);
				//menu_item_list.get(position).setCom4("0");
			}

			/*holder.img.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {

						if (menu_item_list.get(position).getCom4().equalsIgnoreCase("0")) {
							holder.img.setImageResource(R.drawable.check);
							menu_item_list.get(position).setCom4("1");
						} else {
							holder.img.setImageResource(R.drawable.uncheck);
							menu_item_list.get(position).setCom4("0");
						}
				}
			});*/



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
		ImageView img;


	}

}
