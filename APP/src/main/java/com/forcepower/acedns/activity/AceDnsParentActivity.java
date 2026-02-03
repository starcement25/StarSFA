package com.forcepower.acedns.activity;

import android.content.Context;
import android.location.Location;
import android.location.LocationListener;
import android.os.Bundle;
import androidx.fragment.app.FragmentActivity;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.View.OnTouchListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.AdapterView.OnItemLongClickListener;
import android.widget.RadioGroup;
import android.widget.RadioGroup.OnCheckedChangeListener;

//import com.splunk.mint.Mint;

import com.forcepower.acedns.constants.Constants;

public class AceDnsParentActivity extends FragmentActivity implements OnClickListener, LocationListener, OnItemClickListener, OnCheckedChangeListener, OnItemLongClickListener, OnTouchListener {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        if (Constants.employeeDetailObject != null) {} else {}
        super.onCreate(savedInstanceState);

    }

    @Override
    public void onLocationChanged(Location location) {

    }

    @Override
    public void onProviderDisabled(String provider) {

    }

    @Override
    public void onProviderEnabled(String provider) {

    }

    @Override
    public void onStatusChanged(String provider, int status, Bundle extras) {

    }

    @Override
    public void onClick(View arg0) {

    }


    @Override
    public boolean onKeyLongPress(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyLongPress(keyCode, event);
    }

    @Override
    public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {

    }

    @Override
    public void onCheckedChanged(RadioGroup arg0, int arg1) {

    }

    @Override
    public boolean onKeyUp(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
            ((InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE)).toggleSoftInput
                    (InputMethodManager.SHOW_FORCED, InputMethodManager.HIDE_IMPLICIT_ONLY);
        }
        return super.onKeyUp(keyCode, event);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            System.out.println("didnt press the exit button");
            return true;
        }
        if (keyCode == KeyEvent.KEYCODE_MENU) {
            event.startTracking();
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }

    @Override
    public boolean onItemLongClick(AdapterView<?> arg0, View arg1, int arg2,
                                   long arg3) {
        return false;
    }

    @Override
    public boolean onTouch(View v, MotionEvent event) {
        return false;
    }

    @Override
    public void onBackPressed() {
        finish();
    }
}
