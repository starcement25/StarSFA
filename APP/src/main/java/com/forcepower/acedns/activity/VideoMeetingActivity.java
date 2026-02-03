package com.forcepower.acedns.activity;

import android.content.Context;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

import com.forcepower.acedns.R;
import com.forcepower.acedns.util.Utils;

import java.net.MalformedURLException;
import java.net.URL;

public class VideoMeetingActivity extends AceDnsParentActivity
{
    Context mContext;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_video_meeting);
        mContext=this;
        TextView txtVersion = findViewById(R.id.txt_version);
        Button mButtonBack=findViewById(R.id.back);
        txtVersion.setText(Utils.getAppVersion(mContext) + "~" + Utils.getDBVersion(mContext));
        mButtonBack.setOnClickListener(v -> finish());
        URL serverURL;
        try {
            serverURL = new URL(getString(R.string.video_meeting_url2));
        } catch (MalformedURLException e) {
            e.printStackTrace();
            throw new RuntimeException("Invalid server URL!");
        }
//        JitsiMeetConferenceOptions defaultOptions
//                = new JitsiMeetConferenceOptions.Builder()
//                .setServerURL(serverURL)
//                .setWelcomePageEnabled(false)
//                .build();
//        JitsiMeet.setDefaultConferenceOptions(defaultOptions);
    }

    public void onButtonClick(View view)
    {
//        EditText editText = findViewById(R.id.conferenceName);
//        String text = editText.getText().toString();
//
//        if (text.length() > 0) {
//            // Build options object for joining the conference. The SDK will merge the default
//            // one we set earlier and this one when joining.
//            JitsiMeetConferenceOptions options
//                    = new JitsiMeetConferenceOptions.Builder()
//                    .setRoom(text)
//                    .build();
//            // Launch the new activity with the given options. The launch() method takes care
//            // of creating the required Intent and passing the options.
//            JitsiMeetActivity.launch(mContext, options);
//        }
    }
}
