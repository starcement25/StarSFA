package com.forcepower.acedns.new_activity.rsar_meeting;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.LinearLayout;

import androidx.recyclerview.widget.RecyclerView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.activity.AceDnsParentActivity;
import com.forcepower.acedns.new_activity.rsar_meeting.adapter.MyMeetingListItemAdapter;
import com.forcepower.acedns.new_activity.rsar_meeting.data_set.MeetingItem;

import java.util.ArrayList;

public class MyRsarMeetingListActivity extends AceDnsParentActivity implements View.OnClickListener {
    private Button backArrow, addNewMeetingButton;
    private RecyclerView meetingList;
    private LinearLayout meetingNoDataLayout;
    ArrayList<MeetingItem> meetingItemList = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_rsar_meeting_list);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        init();
    }

    @Override
    public void onClick(View view) {
        if (view == backArrow) {
            finish();
        } else if (view == addNewMeetingButton) {
            Intent intent = new Intent(this, CreateRsarMeetingRecordActivity.class);
            startActivity(intent);
        }
    }

    private void init() {
        backArrow = findViewById(R.id.backArrow);
        backArrow.setOnClickListener(this);
        addNewMeetingButton = findViewById(R.id.addNewMeetingButton);
        addNewMeetingButton.setOnClickListener(this);
        meetingList = findViewById(R.id.meetingList);

        meetingNoDataLayout = findViewById(R.id.meetingNoDataLayout);
        meetingNoDataLayout.setVisibility(View.GONE);

        MyMeetingListItemAdapter adapter = new MyMeetingListItemAdapter(this, meetingItemList);
        meetingList.setAdapter(adapter);
    }
}