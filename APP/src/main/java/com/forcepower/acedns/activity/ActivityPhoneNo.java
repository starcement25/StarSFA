package com.forcepower.acedns.activity;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.os.Bundle;
import android.text.InputType;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.Window;
import android.view.WindowManager.LayoutParams;
import android.widget.Button;
import android.widget.EditText;

import com.forcepower.acedns.backgroundTask.AUTH_PhoneNoValidation;
import com.forcepower.acedns.R;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

public class ActivityPhoneNo extends Activity {
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextPhoneNo = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        getWindow().setFlags(LayoutParams.FLAG_NOT_TOUCH_MODAL, LayoutParams.FLAG_NOT_TOUCH_MODAL);
        getWindow().setFlags(LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH, LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH);
        setContentView(R.layout.activity_phoneno);
        RegisterActivities.registerActivity(this);

        mButtonSubmit = findViewById(R.id.btn);
        mEditTextPhoneNo = findViewById(R.id.ed_name);
        mEditTextPhoneNo.setInputType(InputType.TYPE_CLASS_NUMBER);

        mButtonSubmit.setOnClickListener(v -> {
            if (!mEditTextPhoneNo.getText().toString().isEmpty()) {
                if (mEditTextPhoneNo.getText().toString().length() == 10) {
                    if (mEditTextPhoneNo.getText().toString().startsWith("9") || mEditTextPhoneNo.getText().toString().startsWith("8") || mEditTextPhoneNo.getText().toString().startsWith("7")) {
                        Constants.EMAMIMSGRECEIPENT = mEditTextPhoneNo.getText().toString();
                        new AUTH_PhoneNoValidation(ActivityPhoneNo.this).execute();
                    } else {
                        Utils.showToast(ActivityPhoneNo.this, "Please provide valid Phone Number");
                    }
                } else {
                    Utils.showToast(ActivityPhoneNo.this, "Please provide a 10 digit Phone Number");
                }
            } else {
                Utils.showToast(ActivityPhoneNo.this, "Please provide valid input");
            }
        });

    }

    @Override
    public boolean onTouchEvent(MotionEvent event) {
        if (MotionEvent.ACTION_OUTSIDE == event.getAction()) {
            return true;
        }
        return super.onTouchEvent(event);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK || keyCode == KeyEvent.KEYCODE_MENU || keyCode == KeyEvent.KEYCODE_HOME || keyCode == KeyEvent.KEYCODE_POWER) {
            return true;
        }
        return super.onKeyDown(keyCode, event);
    }
}
