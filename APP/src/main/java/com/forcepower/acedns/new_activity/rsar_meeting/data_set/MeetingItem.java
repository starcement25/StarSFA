package com.forcepower.acedns.new_activity.rsar_meeting.data_set;

import org.json.JSONArray;

public class MeetingItem {
    JSONArray meetingPersons;
    String dateTime, meetingType,noOfParticipants,rsarCounterName;

    public String getRsarCounterName() {
        return rsarCounterName;
    }
    public void setRsarCounterName(String rsarCounterName) {
        this.rsarCounterName = rsarCounterName;
    }

    public JSONArray getMeetingPersons() {
        return meetingPersons;
    }
    public void setMeetingPersons(JSONArray meetingPersons) {
        this.meetingPersons = meetingPersons;
    }

    public String getDateTime() {
        return dateTime;
    }
    public void setDateTime(String dateTime) {
        this.dateTime = dateTime;
    }

    public String getMeetingType() {
        return meetingType;
    }
    public void setMeetingType(String meetingType) {
        this.meetingType = meetingType;
    }

    public String getNoOfParticipants() {
        return noOfParticipants;
    }
    public void setNoOfParticipants(String noOfParticipants) {
        this.noOfParticipants = noOfParticipants;
    }
}
