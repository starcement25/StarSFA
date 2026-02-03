package com.forcepower.acedns.util;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.telephony.TelephonyManager;
import android.widget.Toast;

import com.forcepower.acedns.backgroundTask.TRANS_SubmitCRMTask;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.database.AceDnsTransactionDatabase;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Calendar;

import static com.forcepower.acedns.constants.Constants.currentRecordedFileName;
import static com.forcepower.acedns.constants.Constants.isCallRecordingStarted;
import static com.forcepower.acedns.constants.Constants.isCallingFromApp;
import static com.forcepower.acedns.constants.Constants.recordEndTime;
import static com.forcepower.acedns.constants.Constants.recordStartTime;
import static com.forcepower.acedns.constants.Constants.recordedCallDuration;

public class PhoneStateChangeBroadcastReceiver extends BroadcastReceiver {
    public static AudioRecorder AudioRecorderObject;

    @Override
    public void onReceive(Context context, Intent intent) {

        String stateStr = intent.getExtras().getString(TelephonyManager.EXTRA_STATE);
        if (stateStr.equals(TelephonyManager.EXTRA_STATE_IDLE)) {
            isCallingFromApp = false;
            if (isCallRecordingStarted) {
                try {
                    if (AudioRecorderObject != null) {
                        AudioRecorderObject.stop();
                        isCallRecordingStarted = false;
                        Toast.makeText(context, "Recording Stopped", Toast.LENGTH_SHORT).show();
                        new GPSTracker(context);
                        recordEndTime = Calendar.getInstance().getTime();
                        recordedCallDuration = Utils.getDifferenceBetweenTwoDateTime(Constants.recordStartTime, Constants.recordEndTime);
                        AceDnsTransactionDatabase mAceDnsTransactionDatabase = new AceDnsTransactionDatabase(context);
                        mAceDnsTransactionDatabase.insertToSupportingAttachTable(currentRecordedFileName, "CALL_RECORD");
                        String timeStamp = Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime());
                        String prefixForCallRecord = "CRM";
                        String id = prefixForCallRecord + Constants.employeeDetailObject.getEmpCode() + timeStamp;
                        mAceDnsTransactionDatabase.insertToLocationTable(prefixForCallRecord, timeStamp);
                        mAceDnsTransactionDatabase.insertToCrmTransaction(id);

                        new TRANS_SubmitCRMTask(context, false).execute();

                    }

                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        } else if (stateStr.equals(TelephonyManager.EXTRA_STATE_OFFHOOK)) {
            if (isCallingFromApp) {
                currentRecordedFileName = Constants.employeeDetailObject.getEmpCode() + Constants.dateString + new SimpleDateFormat("HHmmss").format(Calendar.getInstance().getTime()) + ".mp3";
                AudioRecorderObject = new AudioRecorder(Utils.getAppStoragePath(context) + currentRecordedFileName);
                try {
                    AudioRecorderObject.start();
                    recordStartTime = Calendar.getInstance().getTime();
                    Toast.makeText(context, "Recording Started", Toast.LENGTH_SHORT).show();
                    isCallRecordingStarted = true;

                } catch (IOException e) {
                    e.printStackTrace();
                }
            }

        } else if (stateStr.equals(TelephonyManager.EXTRA_STATE_RINGING)) {
            isCallingFromApp = false;
        }

    }
}