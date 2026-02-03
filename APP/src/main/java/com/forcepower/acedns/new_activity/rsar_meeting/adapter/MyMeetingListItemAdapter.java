package com.forcepower.acedns.new_activity.rsar_meeting.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.forcepower.acedns.R;
import com.forcepower.acedns.new_activity.rsar_meeting.data_set.MeetingItem;
import java.util.ArrayList;

public class MyMeetingListItemAdapter extends RecyclerView.Adapter<MyMeetingListItemAdapter.ViewHolder> {
    private final Context context;
    private final ArrayList<MeetingItem> list;

    public MyMeetingListItemAdapter(Context context, ArrayList<MeetingItem> list) {
        this.context = context;
        this.list = list;
    }

    public static class ViewHolder extends RecyclerView.ViewHolder {
        TextView typeOfMeeting,numberOfPerson,meetingDate,rsarCounterName;
        public ViewHolder(View itemView) {
            super(itemView);
            typeOfMeeting=itemView.findViewById(R.id.typeOfMeeting);
            numberOfPerson=itemView.findViewById(R.id.numberOfPerson);
            meetingDate=itemView.findViewById(R.id.meetingDate);
            rsarCounterName=itemView.findViewById(R.id.rsarCounterName);
        }
    }

    @NonNull
    @Override
    public MyMeetingListItemAdapter.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_my_rsar_meeting, parent, false);
        return new MyMeetingListItemAdapter.ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull MyMeetingListItemAdapter.ViewHolder holder, int position) {
        MeetingItem item = list.get(position);
        holder.typeOfMeeting.setText(item.getMeetingType());
        holder.numberOfPerson.setText(item.getNoOfParticipants());
        holder.meetingDate.setText(item.getDateTime());
        holder.rsarCounterName.setText(item.getRsarCounterName());
    }

    @Override
    public int getItemCount() {
        return list.size();
    }
}



