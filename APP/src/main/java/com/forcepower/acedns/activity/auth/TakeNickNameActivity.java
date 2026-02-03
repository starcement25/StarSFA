package com.forcepower.acedns.activity.auth;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.Window;
import android.view.WindowManager.LayoutParams;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.AUTH_CheckNickNameTask;

import com.forcepower.acedns.R;

import com.forcepower.acedns.backgroundTask.DATA_DeleteAppDBTask;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.HTTPUtils;
import com.forcepower.acedns.util.RegisterActivities;
import com.forcepower.acedns.util.Utils;

public class TakeNickNameActivity extends Activity {
    @SuppressLint("StaticFieldLeak")
    public static Button mButtonSubmit = null;
    @SuppressLint("StaticFieldLeak")
    public static EditText mEditTextNickName = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        super.onCreate(savedInstanceState);
        getWindow().setFlags(LayoutParams.FLAG_NOT_TOUCH_MODAL, LayoutParams.FLAG_NOT_TOUCH_MODAL);
        getWindow().setFlags(LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH, LayoutParams.FLAG_WATCH_OUTSIDE_TOUCH);
        setContentView(R.layout.activity_nickname);
        RegisterActivities.registerActivity(this);

        mButtonSubmit = findViewById(R.id.btn);
        mEditTextNickName = findViewById(R.id.ed_name);
        mButtonSubmit.setOnClickListener(v -> {
            String nickname = "";
            getWindow().setSoftInputMode(LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
            nickname = mEditTextNickName.getText().toString().toUpperCase().trim();
            if (!nickname.isEmpty()) {
                if (HTTPUtils.isConnectionPossible(TakeNickNameActivity.this)) {
                    Constants.dbDeleteCheckStatus = "0";
                    try {
                        Constants.dbDeleteCheckStatus = new DATA_DeleteAppDBTask(TakeNickNameActivity.this).execute().get();
                        if (Constants.dbDeleteCheckStatus.equalsIgnoreCase("1")) {
                            Constants.isFirstLoginOfApp = true;
                        }
                    } catch (Exception ignored) {
                    }
                    new AUTH_CheckNickNameTask(TakeNickNameActivity.this).execute(nickname);
                } else {
                    Utils.directOutsideTheApplication(TakeNickNameActivity.this, "Check your Internet Connection", false);
                }
            } else {
                Toast.makeText(TakeNickNameActivity.this, "Please provide the nick name", Toast.LENGTH_SHORT).show();
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
