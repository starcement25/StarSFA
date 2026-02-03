package com.forcepower.acedns.bean;

public class TrackItems
{
    String _heading = "", _date = "", _status = "", _key_name;

    // setting
    public void setTrackItems(String _heading, String _date, String _status, String _key_name)
    {
        this._heading = _heading;
        this._status = _status;
        this._date = _date;
        this._key_name = _key_name;
    }
    public String get_heading()
    {
        return this._heading;
    }
    public String get_status()
    {
        return this._status;
    }

    public String get_date()
    {
        return this._date;
    }
    public String get_key_name()
    {
        return this._key_name;
    }
    public void set_status(String val)
    {
        this._status = val;
    }
    public void set_heading(String val)
    {
        this._heading = val;
    }
    public void set_date(String val)
    {
        this._date = val;
    }
}
