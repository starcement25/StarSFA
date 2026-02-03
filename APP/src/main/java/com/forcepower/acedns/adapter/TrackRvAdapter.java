package com.forcepower.acedns.adapter;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.bean.TrackItems;

import java.util.ArrayList;


public class TrackRvAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder>
{
    private final int VIEW_TYPE_ITEM = 0;
    private final int VIEW_TYPE_LOADING = 1;

    Activity myActivity;
    ArrayList<TrackItems> values = new ArrayList<>();

    public TrackRvAdapter(ArrayList<TrackItems> pending_list_, Activity myActivity)
    {
        values = pending_list_;
        this.myActivity = myActivity;
    }

    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType)
    {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.list_item_track, parent, false);
        return new ItemViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull RecyclerView.ViewHolder viewHolder, int position)
    {

        try
        {
            ((ItemViewHolder)viewHolder).tv_status.setText(values.get(position).get_heading() +" " + values.get(position).get_date() + " hrs."); //caption
            ((ItemViewHolder)viewHolder).tv_date.setText(""); //date

            if(position == 0)
            {
                ((ItemViewHolder)viewHolder).tv_TOP.setVisibility(View.INVISIBLE);
                ((ItemViewHolder)viewHolder).tv_BOTTOM.setVisibility(View.VISIBLE);
            }
            else if(position == values.size()-1)
            {
                ((ItemViewHolder)viewHolder).tv_BOTTOM.setVisibility(View.INVISIBLE);
                ((ItemViewHolder)viewHolder).tv_TOP.setVisibility(View.VISIBLE);
            }
            else
            {
                ((ItemViewHolder)viewHolder).tv_TOP.setVisibility(View.VISIBLE);
                ((ItemViewHolder)viewHolder).tv_BOTTOM.setVisibility(View.VISIBLE);
            }
            if(values.get(position).get_status().equalsIgnoreCase("Active"))
            {
                ((ItemViewHolder)viewHolder).tv_circle.setBackgroundResource(R.drawable.circle_solid);
            }
            else
            {
                ((ItemViewHolder)viewHolder).tv_circle.setBackgroundResource(R.drawable.circle_);
            }

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

    }

    @Override
    public int getItemCount() {
        return values.size();
    }

    /**
     * The following method decides the type of ViewHolder to display in the RecyclerView
     *
     * @param position
     * @return
     */
    @Override
    public int getItemViewType(int position)
    {
        return values.get(position) != null ? VIEW_TYPE_ITEM : VIEW_TYPE_LOADING;
    }

    public void setFilter(ArrayList<TrackItems> pending_list_)
    {
        values = new ArrayList<>();
        values.addAll(pending_list_);
        notifyDataSetChanged();
    }


    private class ItemViewHolder extends RecyclerView.ViewHolder
    {
        TextView tv_status, tv_date, tv_circle, tv_TOP, tv_BOTTOM;
        public ItemViewHolder(@NonNull View itemView)
        {
            super(itemView);
            tv_status = itemView.findViewById(R.id.tv_status);
            tv_date = itemView.findViewById(R.id.tv_date);
            tv_circle = itemView.findViewById(R.id.tv_circle);
            tv_TOP = itemView.findViewById(R.id.tv_TOP);
            tv_BOTTOM = itemView.findViewById(R.id.tv_BOTTOM);
        }
    }
}
