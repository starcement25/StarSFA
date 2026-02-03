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


public class DemoModelListAdapter extends BaseAdapter
{

	private Activity context;
	private ArrayList<CommonModel> menu_item_list = new ArrayList<>();

	public DemoModelListAdapter(final Activity context, final ArrayList<CommonModel> menu_item_list_)
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

				convertView = mInflater.inflate(R.layout.demo_form_model_list, null);
				holder = new ViewHolder();

				holder.tv_s_timing = (TextView) convertView.findViewById(R.id.list_details);

				holder.iv_check_uncheck = (ImageView) convertView.findViewById(R.id.iv_check_uncheck);


				convertView.setTag(holder);
			}
			else
			{
				holder = (ViewHolder) convertView.getTag();
			}

			holder.tv_s_timing.setText(menu_item_list.get(position).getCom1());

			holder.iv_check_uncheck.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {

							if (menu_item_list.get(position).getCom2().equalsIgnoreCase("0")) {
								holder.iv_check_uncheck.setImageResource(R.drawable.check);
								menu_item_list.get(position).setCom2("1");

							} else {
								holder.iv_check_uncheck.setImageResource(R.drawable.uncheck);
								menu_item_list.get(position).setCom2("0");

							}

				}
			});


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
		private TextView tv_s_timing,tvBrand,tvLifeProduct;
		private ImageView iv_check_uncheck;

	}
}
