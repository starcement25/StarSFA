package com.forcepower.acedns.activity;

import android.content.Context;
import android.os.Build;

import com.forcepower.acedns.backgroundTask.DATA_EmailCrashLogToDeveloperTask;
import com.forcepower.acedns.constants.Constants;
import com.forcepower.acedns.util.Utils;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.lang.Thread.UncaughtExceptionHandler;
import java.text.DateFormat;
import java.util.Date;

public class CustomExceptionHandler implements UncaughtExceptionHandler {

    private UncaughtExceptionHandler defaultUEH;
    private final Context context;
    public CustomExceptionHandler(Context context) {
        this.defaultUEH = Thread.getDefaultUncaughtExceptionHandler();
        this.context = context;
    }

    public void uncaughtException(Thread t, Throwable e) {
            new Thread() {
                public void run() {
                    final Writer result = new StringWriter();
                    final PrintWriter printWriter = new PrintWriter(result);
                    e.printStackTrace(printWriter);

                    String stacktrace = result.toString();
                    printWriter.close();

                    DateFormat[] formats = new DateFormat[]
                            {
                                    DateFormat.getDateTimeInstance(),
                            };
                    String time = "";
                    for (DateFormat df : formats) {
                        time += df.format(new Date(System.currentTimeMillis()));
                    }

                    stacktrace += " \nCrash Happened: At :" + time;
                    stacktrace += "\n";
                    stacktrace += "Manufacturer : " + Build.MANUFACTURER;
                    stacktrace += "\n";
                    stacktrace += "Device Model : " + Build.MODEL;
                    stacktrace += "\n";
                    stacktrace += "Operating System of device : " + Build.VERSION.RELEASE;
                    stacktrace += "\n";
                    stacktrace += "..................................................................................";
                    stacktrace += "\n\n";
                    try {
                        writeFile(stacktrace);
                    } catch (IOException ex) {
                        ex.printStackTrace();
                    }
                    String empcode="";
                    if(Constants.employeeDetailObject!=null)
                    {
                        empcode=Constants.employeeDetailObject.getEmpCode();
                    }
                    try{
                        new DATA_EmailCrashLogToDeveloperTask(context, false, empcode, "", Constants.employeeDetailObject.getEmpName(), true).execute();
                    }catch (Exception e){

                    }

                }
            }.start();
        defaultUEH.uncaughtException(t, e);
    }

    private void writeFile(String data) throws IOException {
        File myFile = new File(Utils.getAppStoragePath(context)+"ACEdnsCrashLog.txt");
        if (!myFile.exists())
            myFile.createNewFile();

        FileOutputStream fOut = new FileOutputStream(myFile, true);
        OutputStreamWriter myOutWriter = new OutputStreamWriter(fOut);
        myOutWriter.append(data);
        myOutWriter.close();
        fOut.close();
    }
}
