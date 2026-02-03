package com.forcepower.acedns.bean;

public class TrackStatus {
    String _menu_name = "", _date = "", _status = "", _key_name;

    // setting
    public void setTrackStatus(String _date, String _status, String _key_name)
    {
        this._menu_name = _menu_name;
        this._status = _status;
        this._date = _date;
        this._key_name = _key_name;
    }
    public String get_menu_name()
    {
        return this._menu_name;
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

}
