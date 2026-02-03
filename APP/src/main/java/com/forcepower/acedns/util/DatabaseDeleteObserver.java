package com.forcepower.acedns.util;

import android.content.Context;
import android.os.FileObserver;

import com.forcepower.acedns.backgroundTask.DATA_EmailToDeveloperTask;

public class DatabaseDeleteObserver extends FileObserver {

    static String path = "";
    Context context;

    public DatabaseDeleteObserver(Context context) {
        super(path, FileObserver.ALL_EVENTS);
        this.context = context;
        path = "/mnt/sdcard/AceDnsDB";
    }

    @Override
    public void onEvent(int event, String path) {

        //a new file or subdirectory was created under the monitored directory
        if ((FileObserver.CREATE & event) != 0) {
            sendMail("CREATE");
        }
        //a file or directory was opened
        if ((FileObserver.OPEN & event) != 0) {
            sendMail("OPEN");
        }
        //data was read from a file
        if ((FileObserver.ACCESS & event) != 0) {
            sendMail("ACCESS");
        }
        //data was written to a file
        if ((FileObserver.MODIFY & event) != 0) {
            sendMail("MODIFY");
        }
        //someone has a file or directory open read-only, and closed it
        if ((FileObserver.CLOSE_NOWRITE & event) != 0) {
            sendMail("CLOSE_NOWRITE");
        }
        //someone has a file or directory open for writing, and closed it 
        if ((FileObserver.CLOSE_WRITE & event) != 0) {
            sendMail("CLOSE_WRITE");
        }
        //a file was deleted from the monitored directory
        if ((FileObserver.DELETE & event) != 0) {
            sendMail("DELETE");
        }
        //the monitored file or directory was deleted, monitoring effectively stops
        if ((FileObserver.DELETE_SELF & event) != 0) {
            sendMail("DELETE_SELF");
        }
        //a file or subdirectory was moved from the monitored directory
        if ((FileObserver.MOVED_FROM & event) != 0) {
            sendMail("MOVED_FROM");
        }
        //a file or subdirectory was moved to the monitored directory
        if ((FileObserver.MOVED_TO & event) != 0) {
            sendMail("MOVED_TO");
        }
        //the monitored file or directory was moved; monitoring continues
        if ((FileObserver.MOVE_SELF & event) != 0) {
            sendMail("MOVE_SELF");
        }
        //Metadata (permissions, owner, timestamp) was changed explicitly
        if ((FileObserver.ATTRIB & event) != 0) {
            sendMail("ATTRIB");
        }

    }

    public void sendMail(String message) {
        new DATA_EmailToDeveloperTask(context, "", message, "", false).execute();
    }
}